<?php
if (!defined('BASEPATH'))
    exit("NO Direct Script Access Allowed");

/*
 * Class for Network Service Managers
 * network_svc_manager.php
 *
 * Author : Roopak A N
 * Email  : roopak@surewaves.com |  anroopak@gmail.com
 */

class Network_svc_manager extends CI_Controller
{

    /*
     * Constructor Method
     */
    public function __construct()
    {
        parent::__construct();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

        //Loading Configs
        $this->load->config('form_validation');

        //Loading Models
        $this->load->model('user_model');
        $this->load->model('ro_model');
        $this->load->model('mg_model');
        $this->load->model('am_model');
        $this->load->model("menu_model");
        $this->load->model("ns_model");
        $this->load->model("network_model");

        //Loading Libraries
        $this->load->library('session');
        $this->load->library('curl');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('phpexcel');
        $this->load->library('PHPExcel/PHPExcel_IOFactory');

        //Loading Helpers
        $this->load->helper("app_validation_helper");
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->helper('url');
        $this->phpExcelObj = '';
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    public function channel_status()
    {
        $data = array();
        $filterOptions = array('market' => 'Market', 'network' => 'Network', 'channel' => 'Channel', 'notice' => 'Notice', 'noticero' => 'Notice + RO', 'blocked' => 'Blocked');
        $filters = array();
        foreach ($filterOptions as $key => $value)
            $filters[] = $key;

        //Checking Log-In details
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];

        //Fetching the Filters
        $start_index = $this->uri->segment(5);
        $start_index = ($start_index == '' ? 0 : $start_index);
        $filter_by = $this->input->post('filterBy');
        if (in_array($filter_by, array('market', 'channel', 'network')))
            $filter_key = $this->input->post('filterKeyText');
        else {
            $filter_key = $this->input->post('filterKeyBox');
            if ($filter_key != 'true')
                $filter_key = 'false';
        }

        if (!in_array($filter_by, $filters)) {
            $filter_by = FALSE;
            $filter_key = FALSE;
        }
        if ($filter_key) {
            redirect(base_url("network_svc_manager/channel_status/$filter_by/$filter_key/0"), 'refresh');
            exit;
        }
        $filter_by = $this->uri->segment(3);
        $filter_by = ($filter_by == '' ? 0 : $filter_by);
        $filter_key = $this->uri->segment(4);
        $filter_key = ($filter_key == '' ? 0 : $filter_key);

        //Update the Data
        $result = null;
        if ($this->input->post('updateNotice') != FALSE) {
            $result = $this->update_channel_notices();
            if ($result == 'VALID_FAIL')
                $data['msg'] = 'Error : Some Channels has multiple statuses checked.';
            else if ($result == 'SUCCESS')
                $data['msg'] = 'Updated Succesfully.';
            else
                $data['msg'] = 'Update Failed.';
        }

        //Fetching the Data
        $count = 20;
        $data['data'] = $this->ns_model->get_markets_channels($filter_by, $filter_key, $start_index, $count);

        //Doing Pagination
        $config['base_url'] = base_url("network_svc_manager/channel_status/$filter_by/$filter_key/");
        $config['total_rows'] = $this->ns_model->get_count_markets_channels($filter_by, $filter_key);
        $config['per_page'] = $count;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);
        $data['links']['links'] = $this->pagination->create_links();
        $data['links']['count'] = $config['total_rows'];
        $data['links']['from'] = $start_index + 1;
        $data['links']['to'] = $start_index + $count;
        $data['links']['to'] = (($config['total_rows'] < $data['links']['to']) ? $config['total_rows'] : $data['links']['to']);

        // get list of channels pinging status of past 1 week
        $startdate = date("Y-m-d", strtotime("-1 week"));
        $enddate = date("Y-m-d");
        $channle_list = $this->ns_model->get_ping_status($startdate, $enddate);
        foreach ($channle_list as $channel_data) {
            if ($channel_data['deployment_status'] == 'Deployed')
                $final_channle_list[] = $channel_data['channel_id'];
        }
        unset($channle_list);
        //Preparing the Data to be sent
        $menu = $this->menu_model->get_header_menu();
        $data['logged_in_user'] = $logged_in[0];
        $data['profile_details'] = $profile_details[0];
        $data['menu'] = $menu;
        $data['filterOptions']['options'] = $filterOptions;
        $data['filterOptions']['filterBy'] = $filter_by;
        $data['filterOptions']['filterKey'] = $filter_key;

        $data['channels'] = array();
        foreach ($data['data'] as $key => $d) {
            $data['channels'][] = $d['channel_id'];
            if (in_array($d['channel_id'], $final_channle_list)) {
                $data['data'][$key]['channel_connectivity'] = 1;
            } else {
                $data['data'][$key]['channel_connectivity'] = 0;
            }
        }
        $data['channels'] = implode(",", $data['channels']);
        //echo "<pre>";print_r($data);exit;
        //Sending the View
        $this->load->view('network_manager/channel_status', $data);
    }


    /*
     * Method to display Channel Status
     */

    function is_logged_in($javascript_redirect = 0)
    {

        $logged_in_user = $this->session->userdata("logged_in_user");
        $logged_in_user = $logged_in_user[0];
        if (!isset($logged_in_user) || empty($logged_in_user)) {

            if ($javascript_redirect == '1') {
                echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/login";</script>';
            } else {
                redirect("/");
            }
        }
    }

    //function to get channel status as csv

    /**
     * Method to Update notices
     */
    private function update_channel_notices()
    {
        /**
         * @var Array | Holds all Channels which are to be Notice
         */
        $notices = $this->input->post('isNotice');

        /**
         * @var Array | Holds all Channels which are to be Notice and RO
         */
        $noticeROs = $this->input->post('isNoticeRO');

        /**
         * @var Array | Holds all Channels which are to be Blocked
         */
        $blockedChannels = $this->input->post('isBlocked');

        /**
         * @var Array | Holds all Channels which are to be Satellite
         */
        $satelliteChannels = $this->input->post('isSatellite');

        /**
         * @var Array | Holds all Channels
         */
        $channels = explode(",", $this->input->post('channels'));

        $priority = $this->input->post('priority');

        $result = $this->ns_model->update_channel_notices($channels, $notices, $noticeROs, $blockedChannels, $satelliteChannels, $priority);

        return $result;
    }

    public function get_channel_status_csv()
    {

        $filter_by = FALSE;
        $filter_key = FALSE;
        $start_index = 0;
        $count = $this->ns_model->get_count_markets_channels($filter_by, $filter_key);;

        //create csv file from campaign array
        $channel_status = $this->ns_model->get_markets_channels($filter_by, $filter_key, $start_index, $count);
        // get list of channels pinging status of past 1 week
        $startdate = date("Y-m-d", strtotime("-1 week"));
        $enddate = date("Y-m-d");
        $channle_list = $this->ns_model->get_ping_status($startdate, $enddate);
        foreach ($channle_list as $channel_data) {
            if ($channel_data['deployment_status'] == 'Deployed')
                $final_channle_list[] = $channel_data['channel_id'];
        }
        unset($channle_list);
        $this->load->helper('csv');
        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=channel_status.csv');

        $csv_header = "Network,Channel Id,Channel Name,Channel Display Name,Priority,Sent Notice,Sent Notice and RO,Blocked,Satellite,Ping Status,Deployment Status,Markets";
        $csv_data = "";
        echo $csv_header;

        foreach ($channel_status as $ch) {

            $csv_data .= "\n";

            if ($ch['is_notice'] == 1) {
                $ch['is_notice'] = 'Yes';
            } else {
                $ch['is_notice'] = 'No';
            }

            if ($ch['is_notice_ro'] == 1) {
                $ch['is_notice_ro'] = 'Yes';
            } else {
                $ch['is_notice_ro'] = 'No';
            }

            if ($ch['is_blocked'] == 1) {
                $ch['is_blocked'] = 'Yes';
            } else {
                $ch['is_blocked'] = 'No';
            }

            $ch['market_name'] = str_replace(",", "#", $ch['market_name']);

            $displayName = '';

            if (!empty($ch['display_name']) && !empty($ch['locale'])) {

                $displayName = $ch['display_name'] . "-" . $ch['locale'];

            } elseif (!empty($ch['display_name'])) {

                $displayName = $ch['display_name'];

            } elseif (!empty($ch['display_name'])) {

                $displayName = $ch['locale'];

            }

            $csv_data .= $ch['network_name'];
            $csv_data .= "," . $ch['channel_id'];
            $csv_data .= "," . $ch['channel_name'];
            $csv_data .= "," . $displayName;
            $csv_data .= "," . $ch['priority'];
            $csv_data .= "," . $ch['is_notice'];
            $csv_data .= "," . $ch['is_notice_ro'];
            $csv_data .= "," . $ch['is_blocked'];
            $csv_data .= "," . $ch['is_satellite_channel'];
            if (in_array($ch['channel_id'], $final_channle_list)) {
                $csv_data .= "," . "1";
            } else {
                $csv_data .= "," . "0";
            }
            $csv_data .= "," . $ch['deployment_status'];
            $csv_data .= "," . $ch['market_name'];
            echo $csv_data;
            $csv_data = "";
        }

    }

    /**
     * Method to Update the Channel Rates
     * @return type
     */
    public function channel_rates()
    {
        $data = array();
        $filterOptions = array('market' => 'Market', 'network' => 'Network', 'channel' => 'Channel');
        $filters = array();
        foreach ($filterOptions as $key => $value)
            $filters[] = $key;

        //Checking Log-In details
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];

        //Fetching the Filters
        $start_index = $this->uri->segment(5);
        $start_index = ($start_index == '' ? 0 : $start_index);
        $filter_by = $this->input->post('filterBy');
        $filter_key = $this->input->post('filterKeyText');

        if (!in_array($filter_by, $filters)) {
            $filter_by = FALSE;
            $filter_key = FALSE;
        }

        if ($filter_key != '') {
            redirect(base_url("network_svc_manager/channel_rates/$filter_by/$filter_key/0"), 'refresh');
            exit;
        }
        $filter_by = $this->uri->segment(3);
        $filter_by = ($filter_by == '' ? 0 : $filter_by);
        $filter_key = $this->uri->segment(4);
        $filter_key = ($filter_key == '' ? 0 : $filter_key);

        //Update the Data
        $result = null;
        if ($this->input->post('updateRates') != FALSE) {
            $result = $this->update_channel_rates();
            if ($result)
                $data['msg'] = 'Updated Succesfully.';
            else
                $data['msg'] = 'Update Failed.';
        }

        //Fetching the Data
        $count = 20;
        $data['data'] = $this->ns_model->get_networks_channels($filter_by, $filter_key, $start_index, $count);

        //Doing Pagination
        $config['base_url'] = base_url("network_svc_manager/channel_rates/$filter_by/$filter_key/");
        $config['total_rows'] = $this->ns_model->get_count_networks_channels($filter_by, $filter_key);
        $config['per_page'] = $count;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);
        $data['links']['links'] = $this->pagination->create_links();
        $data['links']['count'] = $config['total_rows'];
        $data['links']['from'] = $start_index + 1;
        $data['links']['to'] = $start_index + $count;
        $data['links']['to'] = (($config['total_rows'] < $data['links']['to']) ? $config['total_rows'] : $data['links']['to']);

        //Preparing the Data to be sent
        $menu = $this->menu_model->get_header_menu();
        $data['logged_in_user'] = $logged_in[0];
        $data['profile_details'] = $profile_details[0];
        $data['menu'] = $menu;
        $data['filterOptions']['options'] = $filterOptions;
        $data['filterOptions']['filterBy'] = $filter_by;
        $data['filterOptions']['filterKey'] = $filter_key;

        $data['channels'] = array();
        foreach ($data['data'] as $d) {
            $data['channels'][] = $d['channel_id'];
        }
        $data['channels'] = implode(",", $data['channels']);

        //Sending the View
        $this->load->view('network_manager/channel_rates', $data);
    }


    //function to get channel rates as csv

    /**
     * Method to Update Channel Rates
     */
    private function update_channel_rates()
    {

        /**
         * @var Array | Holds all Channels
         */
        $channels = explode(",", $this->input->post('channels'));

        /**
         * @var Array | Holds all Spot Rates which are to be Notice
         */
        $spot_rates = array();

        /**
         * @var Array | Holds all Banner Rates
         */
        $banner_rates = array();

        foreach ($channels as $c) {
            $spot_rates[$c] = $this->input->post('spot_avg' . $c);
            $banner_rates[$c] = $this->input->post('banner_avg' . $c);
        }

        $newNetworkShareArray = $this->input->post('network_share_');
        $oldNetworkShareArray = $this->input->post('network_share_old_');

        $result = $this->ns_model->update_channel_rates($channels, $spot_rates, $banner_rates, $newNetworkShareArray, $oldNetworkShareArray);

        return $result;
    }

    public function get_channel_rates_csv()
    {

        $filter_by = FALSE;
        $filter_key = FALSE;
        $start_index = 0;
        $count = $this->ns_model->get_count_markets_channels($filter_by, $filter_key);;

        //create csv file from campaign array
        $channel_rates = $this->ns_model->get_networks_channels($filter_by, $filter_key, $start_index, $count);

        $this->load->helper('csv');
        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=channel_rates.csv');

        $csv_header = "Network,Channel Id, Channel Name, Channel Display Name ,Spot Rate,Banner Rate, Network Share ,Deployment Status,Markets";
        $csv_data = "";
        echo $csv_header;

        foreach ($channel_rates as $ch) {

            $csv_data .= "\n";
            $displayName = '';
            $ch['market_name'] = str_replace(",", "#", $ch['market_name']);

            if (!empty($ch['display_name']) && !empty($ch['locale'])) {

                $displayName = $ch['display_name'] . "-" . $ch['locale'];

            } elseif (!empty($ch['display_name'])) {

                $displayName = $ch['display_name'];

            } elseif (!empty($ch['display_name'])) {

                $displayName = $ch['locale'];

            }

            $csv_data .= $ch['network_name'];
            $csv_data .= "," . $ch['channel_id'];
            $csv_data .= "," . $ch['channel_name'];
            $csv_data .= "," . $displayName;
            $csv_data .= "," . $ch['spot_avg'];
            $csv_data .= "," . $ch['banner_avg'];
            $csv_data .= "," . $ch['revenue_sharing'];
            $csv_data .= "," . $ch['deployment_status'];
            $csv_data .= "," . $ch['market_name'];
            echo $csv_data;
            $csv_data = "";
        }

    }

    public function find_data_for_cache()
    {
        $user_id = $this->get_user_id();

        $confirmation_data = $this->ro_model->get_data_for_confirmation_customer($user_id);
        foreach ($confirmation_data as $val) {
            $order_id_ser = $val['order_id'];
            $confirmation_id = $val['confirmation_id'];
            $order_id = unserialize($order_id_ser);

            $key_channel = 'add_cancel_channel_' . $user_id . "_" . $order_id;
            $key_posted = "loadSave_" . $user_id . "_" . $order_id;

            $this->delete_from_cache($key_channel);
            $this->delete_from_cache($key_posted);

            $this->ro_model->delete_from_confirmation($order_id_ser);
            $this->ro_model->delete_from_confirmation_customer($confirmation_id, $user_id);
        }
    }

    public function get_user_id()
    {
        $logged_in_user = $this->session->userdata("logged_in_user");
        return $logged_in_user[0]['user_id'];

    }

    /* Code for manage channel names */

    public function manageChannelName()
    {
        $data = array();
        $filterOptions = array('market' => 'Market', 'network' => 'Network', 'channel' => 'Channel');
        $filters = array();
        foreach ($filterOptions as $key => $value)
            $filters[] = $key;

        //Checking Log-In details
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];

        //Fetching the Filters
        $start_index = $this->uri->segment(5);
        $start_index = ($start_index == '' ? 0 : $start_index);
        $filter_by = $this->input->post('filterBy');
        $filter_key = $this->input->post('filterKeyText');

        if (!in_array($filter_by, $filters)) {
            $filter_by = FALSE;
            $filter_key = FALSE;
        }

        if ($filter_key != '') {
            redirect(base_url("network_svc_manager/manageChannelName/$filter_by/$filter_key/0"), 'refresh');
            exit;
        }
        $filter_by = $this->uri->segment(3);
        $filter_by = ($filter_by == '' ? 0 : $filter_by);
        $filter_key = $this->uri->segment(4);
        $filter_key = ($filter_key == '' ? 0 : $filter_key);

        //Update the Data
        /*  $result = null;
          if ($this -> input -> post('updateRates') != FALSE) {
              $result = $this -> update_channel_rates();
              if($result)
                  $data['msg'] = 'Updated Succesfully.';
              else
                  $data['msg'] = 'Update Failed.';
          }
      */
        //Fetching the Data
        $count = 20;
        //Calling same query for getting data
        $data['data'] = $this->ns_model->get_networks_channels($filter_by, $filter_key, $start_index, $count);

        //Doing Pagination
        $config['base_url'] = base_url("network_svc_manager/manageChannelName/$filter_by/$filter_key/");
        $config['total_rows'] = $this->ns_model->get_count_networks_channels($filter_by, $filter_key);
        $config['per_page'] = $count;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);
        $data['links']['links'] = $this->pagination->create_links();
        $data['links']['count'] = $config['total_rows'];
        $data['links']['from'] = $start_index + 1;
        $data['links']['to'] = $start_index + $count;
        $data['links']['to'] = (($config['total_rows'] < $data['links']['to']) ? $config['total_rows'] : $data['links']['to']);

        //Preparing the Data to be sent
        $menu = $this->menu_model->get_header_menu();
        $data['logged_in_user'] = $logged_in[0];
        $data['profile_details'] = $profile_details[0];
        $data['menu'] = $menu;
        $data['filterOptions']['options'] = $filterOptions;
        $data['filterOptions']['filterBy'] = $filter_by;
        $data['filterOptions']['filterKey'] = $filter_key;

        $data['channels'] = array();
        foreach ($data['data'] as $d) {
            $data['channels'][] = $d['channel_id'];
        }
        $data['channels'] = implode(",", $data['channels']);

        //Sending the View
        $this->load->view('network_manager/manage_channel_names', $data);
    }

    public function manage_enterprise()
    {
        //Checking Log-In details
        $this->is_logged_in();
        /*$this->find_data_for_cache() ;*/
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $profile_details = $this->user_model->get_profile_details($profile_id);
        $menu = $this->menu_model->get_header_menu();

        $all_enterprise = $this->network_model->getAllEnterprise();

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_details'] = $profile_details[0];
        $model['menu'] = $menu;
        $model['all_enterprise'] = $all_enterprise;
        $model['all_genre'] = $this->network_model->getAllGenre();
        $model['all_language'] = $this->network_model->getAllLanguage();
        $model['all_dominantContent'] = $this->network_model->getdominantContent();


        $this->load->view('network_manager/network_servicing', $model);
    }

    public function save_enterprise_data()
    {
        if ($this->input->post('upload_attachment') == 1) {
            $this->uploadFileAttachment();
        }
        $ret = $forEnterpriceContact = $forEnterpriceContact = 0;
        $data = array();
        $forEnterpriceContact = $this->input->post('set_enterprise_contact');
        $forEnterpriceFinance = $this->input->post('set_enterprise_finance');
        $forEnterpriceChannel = $this->input->post('set_enterprise_channel');
        if ($forEnterpriceContact) {
            $customer_id = $this->input->post('contact_enterprise_id');
            $data['contact_person'] = $this->input->post('enterprise_contact_person');
            $data['email_id'] = $this->input->post('email_id');
            $data['mobile_number'] = $this->input->post('mobile_no');
            $data['land_line_number'] = $this->input->post('land_no');
            $data['fax_number'] = $this->input->post('fax_no');
            $data['address'] = $this->input->post('address_details');
            $if_enterprise_exist = $this->input->post('check_enterprise_exist');
            if ($if_enterprise_exist == "exist") {
                $ret = $this->network_model->updateEntepriseData('sv_customer_contact_details', $data, 'customer_id', $customer_id);
            } else {
                $data['customer_id'] = $customer_id;
                $ret = $this->network_model->setEntepriseData('sv_customer_contact_details', $data);
            }
            $this->network_model->updateNetworkPrimaryEmails('sv_customer', array('customer_id' => $customer_id), array('customer_email' => $data['email_id']));
            $status = 1;
        } else if ($forEnterpriceFinance) {
            $customer_id = $this->input->post('finance_enterprise_id');
            $data['contact_person'] = $this->input->post('contact_person');
            $data['email_id'] = $this->input->post('finance_details_email');
            $data['contact_number'] = $this->input->post('contact_number');
            $data['billing_name'] = $this->input->post('billing_name');
            $data['pan_number'] = $this->input->post('pan_no');
            $data['account_number'] = $this->input->post('acc_no');
            $data['bank_name'] = $this->input->post('bank_name');
            $data['branch_name'] = $this->input->post('branch_name');
            $data['ifsc_code'] = $this->input->post('ifsc_code');
            $data['service_tax_number'] = $this->input->post('service_tax_no');
            $if_enterprise_exist = $this->input->post('check_enterprise_exist');

            if ($if_enterprise_exist == "exist") {
                $ret = $this->network_model->updateEntepriseData('sv_customer_finance_details', $data, 'customer_id', $customer_id);
            } else {
                $data['customer_id'] = $customer_id;
                $ret = $this->network_model->setEntepriseData('sv_customer_finance_details', $data);
            }
            $data_array['billing_name'] = $data['billing_name'];
            $this->network_model->updateEntepriseData('sv_customer', $data_array, 'customer_id', $customer_id);
            $status = 2;
        } else if ($forEnterpriceChannel) {
            $channel_id = $this->input->post('channel_id');
            $contact_personArr = $this->input->post('channel_contact_person');
            $contact_person_emailArr = $this->input->post('channel_email_id');
            $channel_contact_numberArr = $this->input->post('channel_contact_number');
            for ($contact_count = 1; $contact_count <= count($contact_personArr); $contact_count++) {
                $data['contact_person' . $contact_count] = $contact_personArr[($contact_count - 1)];
                $data['contact_number' . $contact_count] = $channel_contact_numberArr[($contact_count - 1)];
            }
            for ($email_count = 1; $email_count <= count($contact_person_emailArr); $email_count++) {
                $data['email_id' . $email_count] = $contact_person_emailArr[($email_count - 1)];
            }
            $data['genre'] = implode(",", $this->input->post('genere'));
            $data['dominant_content'] = implode(",", $this->input->post('dominant_content'));
            $data['address'] = $this->input->post('channel_address_details');
            $data['language'] = implode(",", $this->input->post('language'));

            $if_channel_exist = $this->input->post('check_enterprise_channel_exist');
            if ($if_channel_exist == "exist") {
                $ret = $this->network_model->updateEntepriseData('sv_channel_contact_details', $data, 'tv_channel_id', $channel_id);
            } else {
                $data['tv_channel_id'] = $channel_id;
                $ret = $this->network_model->setEntepriseData('sv_channel_contact_details', $data);
            }
            $status = 3;
        }
        echo $ret . "~" . $status;

    }

    public function uploadFileAttachment()
    {
        //echo print_r($this->input->post()) ;
        //echo "----". print_r($_FILES,true) ;exit;
        $customer_id = $this->input->post('customer_id');
        $billing_number = $this->input->post('billing_number');

        $same_as_name = $this->input->post('eid');
        $signed_on = $this->input->post('signed_on');
        $validity_number = $this->input->post('validity');
        $validity_period = $this->input->post('validity_values');
        $document_status = $this->input->post('document_status');
        $document_attached = $this->input->post('document_status_value');
        $service_tax_document = '';
        if (isset($_FILES['service_tax_docs']['name']) && $_FILES['service_tax_docs']['name'] != '') {
            $formElementName = "service_tax_docs";
            $fileName = $_FILES['service_tax_docs']['name'];
            $service_tax_document = $this->fileUpload($formElementName, $customer_id, $fileName);
        }
        $pan_card = '';
        if (isset($_FILES['pan_card']['name']) && $_FILES['pan_card']['name'] != '') {
            $formElementName = "pan_card";
            $fileName = $_FILES['pan_card']['name'];
            $pan_card = $this->fileUpload($formElementName, $customer_id, $fileName);
        }
        $cancelled_cheque = '';
        if (isset($_FILES['file_cancelled_cheque']['name']) && $_FILES['file_cancelled_cheque']['name'] != '') {
            $formElementName = "file_cancelled_cheque";
            $fileName = $_FILES['file_cancelled_cheque']['name'];
            $cancelled_cheque = $this->fileUpload($formElementName, $customer_id, $fileName);
        }
        $erf = '';
        if (isset($_FILES['file_erf']['name']) && $_FILES['file_erf']['name'] != '') {
            $formElementName = "file_erf";
            $fileName = $_FILES['file_erf']['name'];
            $erf = $this->fileUpload($formElementName, $customer_id, $fileName);
        }
        $household_info = '';
        if (isset($_FILES['file_household_info']['name']) && $_FILES['file_household_info']['name'] != '') {
            $formElementName = "file_household_info";
            $fileName = $_FILES['file_household_info']['name'];
            $household_info = $this->fileUpload($formElementName, $customer_id, $fileName);
        }

        $attachment_hard_copies = $this->input->post('attachment_hard_copies');
        $mediastation_status = $this->input->post('media_station');

        $userData = array('customer_id' => $customer_id, 'billing_number' => $billing_number, 'same_as_name' => $same_as_name,
            'signed_on' => $signed_on, 'validity_number' => $validity_number, 'validity_period' => $validity_period,
            'document_status' => $document_status, 'document_attached' => $document_attached,
            'attachment_hard_copies' => $attachment_hard_copies, 'mediastation_status' => $mediastation_status);

        if ($service_tax_document != '') {
            $userData['service_tax_document'] = $service_tax_document;
        }
        if ($pan_card != '') {
            $userData['pan_card'] = $pan_card;
        }
        if ($cancelled_cheque != '') {
            $userData['cancelled_cheque'] = $cancelled_cheque;
        }
        if ($erf != '') {
            $userData['erf'] = $erf;
        }
        if ($household_info != '') {
            $userData['household_info'] = $household_info;
        }

        $getAttachment = $this->network_model->getUploadAttachment(array('customer_id' => $customer_id));
        if (count($getAttachment) > 0) {
            /* $updateData = array('same_as_name'=>$same_as_name,'signed_on'=>$signed_on,
                          'validity_number'=>$validity_number,'validity_period' => $validity_period,'document_status' => $document_status,'document_attached' => $document_attached,
                        'service_tax_document' => $service_tax_document,'pan_card' => $pan_card, 'cancelled_cheque' => $cancelled_cheque,
                        'erf' => $erf,'household_info' => $household_info,'attachment_hard_copies'=>$attachment_hard_copies,'mediastation_status' => $mediastation_status ) ; */
            $this->network_model->updateUploadAttachment($userData, array('customer_id' => $customer_id,));
        } else {
            $this->network_model->insertUploadAttachment($userData);
        }
        redirect(base_url("network_svc_manager/manage_enterprise"));
    }

    public function fileUpload($formElementName, $cid, $fileName)
    {
        $s3_file_location = $this->initializeUploadConfiguration($formElementName, $cid, $fileName);
        /*$this->load->library('upload',$config);
        if($this->upload->do_upload($formElementName) != False){
            $this->upload->data();
            return $this->uploadOntoS3($config['file_name']) ;
        }          */
        //return $this->uploadOntoS3($file_location) ;
        return $s3_file_location;
    }

    private function initializeUploadConfiguration($formElementName, $cid, $fileName)
    {
        /*$config = array() ;
        $config['upload_path'] = 'easy_ro_temp_pdf/';
        $config['allowed_types'] = '*';
    $fileExt 			= substr($fileName, strrpos($fileName, '.') + 1);
    $fileNameWithoutExt 	= substr($fileName, 0,strrpos($fileName, '.'));
        $config['file_name'] = $cid."_".$fileNameWithoutExt."_".date('Y_m_d_H_i_s').".".$fileExt;
        return $config ;*/
        $fileExt = substr($fileName, strrpos($fileName, '.') + 1);
        $fileNameWithoutExt = substr($fileName, 0, strrpos($fileName, '.'));
        $new_file_name = $cid . "_" . $fileNameWithoutExt . "_" . strtotime(date('Y-m-d H:i:s.u')) . "." . $fileExt;
        $file_location = $_SERVER['DOCUMENT_ROOT'] . "surewaves_easy_ro/" . 'easy_ro_temp_pdf/' . $new_file_name;
        // echo $file_location."<br>";
        $status = move_uploaded_file($_FILES[$formElementName]["tmp_name"], $file_location);
        /*	if($status) {
                echo "moved"."<br />";
            }else{
                echo "failed to move"."<br/>";
            }*/
        return $this->uploadOntoS3($file_location, $new_file_name);

    }

    public function uploadOntoS3($file_location, $new_file_name)
    {
        //$file_location = $_SERVER['DOCUMENT_ROOT']."surewaves_easy_ro/".'easy_ro_temp_pdf/'.$file_name;
        // upload to S3
        require_once("S3.php");
        $s3 = new S3(AMAZON_KEY, AMAZON_VALUE);
        //echo  $file_location."---".$new_file_name."<br />";
        $status = S3::putObject(S3::inputFile("$file_location"), "sw_easy_ro_am_pdf", "$new_file_name", S3::ACL_PUBLIC_READ);
        // $s3->putObjectFile($file_location, "sw_easy_ro_am_pdf", $file_name, S3::ACL_PUBLIC_READ);
        /*	if($status){
                echo "uploaded"."<br />";
            }else{
                echo "failed"."<br />";
            }*/

        // end uploading
        $file_pdf_s3_url = "http://s3.amazonaws.com/sw_easy_ro_am_pdf/" . $new_file_name;
        return $file_pdf_s3_url;
    }

    function updateChannelNames()
    {

        $newChannelsIdValueArray = $this->input->post('newChannelDisplayName_');
        $oldChannelsIdValueArray = $this->input->post('oldChannelDisplayName_');

        $newCustomerValueArray = $this->input->post('newCustomerDisplayName_');
        $oldCustomerValueArray = $this->input->post('oldCustomerDisplayName_');

        $newLocalIdsValueArray = $this->input->post('newLocale_');
        $oldLocalIdsValueArray = $this->input->post('oldLocale_');

        foreach ($newChannelsIdValueArray as $channelId => $displayName) {

            $updateSet = "";
            $updateCustomerDisplaySet = '';

            $newDisplayName = trim($displayName);
            $oldDisplayName = trim($oldChannelsIdValueArray[$channelId]);

            $newCustomerDisplayName = trim($newCustomerValueArray[$channelId]);
            $oldCustomerDisplayName = trim($oldCustomerValueArray[$channelId]);

            $newLocal = trim($newLocalIdsValueArray[$channelId]);
            $oldLocal = trim($oldLocalIdsValueArray[$channelId]);

            if ((md5($newDisplayName) != md5($oldDisplayName))) {

                $updateSet .= " display_name = '$displayName' ";

            }

            if ((md5($newCustomerDisplayName) != md5($oldCustomerDisplayName))) {

                $updateCustomerDisplaySet .= " customer_display_name = '$newCustomerDisplayName'";

            }

            if ((md5($newLocal) != md5($oldLocal))) {

                if (!empty($updateSet)) {

                    $updateSet .= " ,";

                }
                $updateSet .= " locale = '$newLocal'";

            }

            $this->updateChannelDisplayLocalStatus($updateSet, $channelId, $updateCustomerDisplaySet);
        }

        redirect(base_url("network_svc_manager/manageChannelName/"));
    }

    private function updateChannelDisplayLocalStatus($updateSet, $channelId, $updateCustomerDisplaySet = '')
    {

        if (!empty($updateSet) || !empty($updateCustomerDisplaySet)) {

            return $this->ns_model->updateChannelDisplayNameLocalStatus($updateSet, $channelId, $updateCustomerDisplaySet);

        }

    }

    public function getGenereAndLanguageByChannelWise()
    {
        set_time_limit(0);
        $filterParam = array();
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $profile_details = $this->user_model->get_profile_details($profile_id);
        $menu = $this->menu_model->get_header_menu();
        $isIndirectLoad = $this->input->post('isIndirectLoad');
        $fromRecordNo = $this->input->post('fromRecordNo');
        $upToNoOfRecords = 30;
        if ($fromRecordNo == '') {
            $fromRecordNo = 0;
        } else {
            if ($fromRecordNo == 0) {
                $fromRecordNo = $upToNoOfRecords + 1;
            } else {
                $fromRecordNo = $fromRecordNo + $upToNoOfRecords;
            }
        }
        $data = array();
        $data['logged_in_user'] = $logged_in[0];
        $data['profile_details'] = $profile_details[0];
        $data['menu'] = $menu;
        $data['all_genre'] = $this->network_model->getGenre();
        $data['all_dominantContent'] = $this->network_model->getdominantContent();
        $data['all_language'] = $this->network_model->getlanguage();
        $data['displayGenereLanguageArr'] = $this->network_model->fetchGenereAndLanguageByChannelWise($fromRecordNo, $upToNoOfRecords);
        $startdate = date("Y-m-d", strtotime("-1 week"));
        $enddate = date("Y-m-d");
        $channel_list = $this->ns_model->get_ping_status($startdate, $enddate);
        foreach ($channel_list as $channel_data) {
            $channel_list_with_ping[] = $channel_data['channel_id'];
        }
        $data['channel_list_with_ping'] = $channel_list_with_ping;
        if (count($data['displayGenereLanguageArr']) < $upToNoOfRecords) {
            $data['fetchRecords'] = 0;
        } else {
            $data['fetchRecords'] = 1;
        }
        $data['fromRecordNo'] = $fromRecordNo;
        if ($isIndirectLoad)
            echo json_encode($data);
        else
            $this->load->view('network_manager/manage_genere_language', $data);


    }

    /* Code for manage channel names*/

    public function generate_allenterprise_details()
    {
        ini_set('max_execution_time', 0);
        $colorArray = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => '538ED5'),
        );
        $colorArray1 = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'FAC090'),
        );

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                    'color' => array('rgb' => '538ED5')
                )
            )
        );
        $styleArray1 = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );
        $fontStyleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 10,
                'name' => 'Verdana'
            )
        );
        $headingStyleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $title = 'Enterprise Details';
        $objPHPExcel->getActiveSheet()->setTitle($title);
        /*$objPHPExcel->getDefaultStyle()->applyFromArray(
            array(
                'fill' => array(
                    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            )
        );*/

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(40);

        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'NETWORK DETAILS');
        $objPHPExcel->getActiveSheet()->mergeCells('B2:D2');
        $objPHPExcel->getActiveSheet()->getStyle('B2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:D2')->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B2:D2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B2:D2')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('E2', 'ENTERPRISE CONTACT DETAILS ');
        $objPHPExcel->getActiveSheet()->mergeCells('E2:J2');
        $objPHPExcel->getActiveSheet()->getStyle('E2:J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E2:J2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E2:J2')->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E2:J2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E2:J2')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('K2', 'ENTERPRISE FINANCE DETAILS ');
        $objPHPExcel->getActiveSheet()->mergeCells('K2:T2');
        $objPHPExcel->getActiveSheet()->getStyle('K2:T2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('K2:T2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('K2:T2')->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('K2:T2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('K2:T2')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('U2', 'CHANNEL CONTACT DETAILS ');
        $objPHPExcel->getActiveSheet()->mergeCells('U2:AK2');
        $objPHPExcel->getActiveSheet()->getStyle('U2:AK2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('U2:AK2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('U2:AK2')->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('U2:AK2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('U2:AK2')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->getStyle('B2:AK2')->getFill()->applyFromArray($colorArray);

        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'NETWORK ID');
        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'NETWORK NAME');
        $objPHPExcel->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('D3', 'NETWORK DISPLAY NAME');
        $objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('E3', 'CONTACT PERSON');
        $objPHPExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('F3', 'EMAIL ID');
        $objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('G3', 'MOBILE NO');
        $objPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('H3', 'LAND LINE NO');
        $objPHPExcel->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('H3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('I3', 'FAX NO');
        $objPHPExcel->getActiveSheet()->getStyle('I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('I3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('J3', 'ADDRESS');
        $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('J3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('K3', 'CONTACT PERSON');
        $objPHPExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('K3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('K3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('L3', 'EMAIL ID');
        $objPHPExcel->getActiveSheet()->getStyle('L3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('L3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('L3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('M3', 'CONTACT NO');
        $objPHPExcel->getActiveSheet()->getStyle('M3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('M3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('M3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('N3', 'BILLING NAME');
        $objPHPExcel->getActiveSheet()->getStyle('N3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('N3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('N3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('O3', 'PAN NO');
        $objPHPExcel->getActiveSheet()->getStyle('O3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('O3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('O3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('P3', 'SERVICE TAX NO');
        $objPHPExcel->getActiveSheet()->getStyle('P3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('P3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('P3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('Q3', 'ACCOUNT NO');
        $objPHPExcel->getActiveSheet()->getStyle('Q3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('Q3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('Q3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('R3', 'BANK NAME');
        $objPHPExcel->getActiveSheet()->getStyle('R3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('R3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('R3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('S3', 'BRANCH NAME');
        $objPHPExcel->getActiveSheet()->getStyle('S3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('S3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('S3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('T3', 'IFSC CODE');
        $objPHPExcel->getActiveSheet()->getStyle('T3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('T3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('T3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('U3', 'CHANNEL ID');
        $objPHPExcel->getActiveSheet()->getStyle('U3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('U3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('U3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('V3', 'CHANNEL NAME');
        $objPHPExcel->getActiveSheet()->getStyle('V3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('V3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('V3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('W3', 'CHANNEL STATUS');
        $objPHPExcel->getActiveSheet()->getStyle('W3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('W3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('W3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('X3', 'CONTACT PERSON 1');
        $objPHPExcel->getActiveSheet()->getStyle('X3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('X3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('X3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('Y3', 'EMAIL ID 1');
        $objPHPExcel->getActiveSheet()->getStyle('Y3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('Y3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('Y3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('Z3', 'CONTACT NO 1');
        $objPHPExcel->getActiveSheet()->getStyle('Z3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('Z3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('Z3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AA3', 'CONTACT PERSON 2');
        $objPHPExcel->getActiveSheet()->getStyle('AA3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AA3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AA3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AB3', 'EMAIL ID 2');
        $objPHPExcel->getActiveSheet()->getStyle('AB3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AB3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AB3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AC3', 'CONTACT NO 2');
        $objPHPExcel->getActiveSheet()->getStyle('AC3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AC3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AC3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AD3', 'CONTACT PERSON 3');
        $objPHPExcel->getActiveSheet()->getStyle('AD3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AD3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AD3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AE3', 'EMAIL ID 3');
        $objPHPExcel->getActiveSheet()->getStyle('AE3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AE3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AE3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AF3', 'CONTACT NO 3');
        $objPHPExcel->getActiveSheet()->getStyle('AF3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AF3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AF3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AG3', 'GENRE');
        $objPHPExcel->getActiveSheet()->getStyle('AG3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AG3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AG3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AH3', 'DOMINANT CONTENT');
        $objPHPExcel->getActiveSheet()->getStyle('AG3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AG3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AG3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AI3', 'LANGUAGE');
        $objPHPExcel->getActiveSheet()->getStyle('AH3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AH3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AH3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AJ3', 'ADDRESS');
        $objPHPExcel->getActiveSheet()->getStyle('AI3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AI3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AI3')->applyFromArray($headingStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('AI3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('AK3', 'DEPLOYMENT STATUS');
        $objPHPExcel->getActiveSheet()->getStyle('AK3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('AK3')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('AK3')->applyFromArray($headingStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('AK3')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->getStyle('B3:AK3')->getFill()->applyFromArray($colorArray1);
        $this->phpExcelObj = $objPHPExcel;

        $startOrgRow = $startRow = $endRow = 4;
        $channel_list_with_ping = array();
        $startdate = date("Y-m-d", strtotime("-1 week"));
        $enddate = date("Y-m-d");
        $ping_channel_list = $this->ns_model->get_ping_status($startdate, $enddate);
        foreach ($ping_channel_list as $channel_data) {
            $channel_list_with_ping[] = $channel_data['channel_id'];
        }
        $all_enterpriseID = explode(",", base64_decode($this->input->post('allEnterpriseDetails')));
        $excludeRDNEnterprises = $this->network_model->getRDNEnterprises();
        $allRDNEnterprise = array();
        foreach ($excludeRDNEnterprises as $eachRdnEnterprise) {
            array_push($allRDNEnterprise, $eachRdnEnterprise['customer_id_mso']);
        }
        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/octet-stream');
        //header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=enterprise-details.xls');
        header('Content-Transfer-Encoding: binary');
        //ob_clean();
        //flush();


        foreach ($all_enterpriseID as $key => $eachEnterpriseID) {
            if (in_array($eachEnterpriseID, $allRDNEnterprise)) continue;
            $enterpriseAllDetails = $this->get_enterprise_details($eachEnterpriseID, true, $channel_list_with_ping);
            $startRow = $endRow;

            $total_rows = count($enterpriseAllDetails['enterprise_channel_details']);
            if ($total_rows > 1) {
                $endRow = $startRow + ($total_rows - 1);
            }
            //data for enterprise contact details
            $startColumn = 'E';
            $endColumn = 'J';
            $this->writeEachTabIntoExcel($enterpriseAllDetails['enterprise_contact_details'], $startRow, $endRow, $startColumn, $endColumn);

            //data for enterprise finance details
            $startColumn = 'K';
            $endColumn = 'T';
            $this->writeEachTabIntoExcel($enterpriseAllDetails['enterprise_finance_details'], $startRow, $endRow, $startColumn, $endColumn);

            //data for channel contact details
            $startColumn = 'U';
            $endColumn = 'AK';
            $this->writeEachTabIntoExcel($enterpriseAllDetails['enterprise_channel_details'], $startRow, $endRow, $startColumn, $endColumn, 0);
            $endRow = $endRow + 1;
        }
        $objPHPExcel->getActiveSheet()->getStyle('B' . $startOrgRow . ':' . 'AK' . $endRow)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->freezePane('A4');
        /*$startColumnCell 	= 'B';
        $endColumnCell		= 'AI';
        $startRowCount		= 3;*/


        /*		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="enterprise-details.xls"');
                header('Cache-Control: max-age=0');
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Type: application/force-download');
                header('Content-Type: application/octet-stream');
                header('Content-Type: application/download');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=enterprise-details.xls');
                header('Content-Transfer-Encoding: binary');*/
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_clean();
        flush();
        $objWriter->save('php://output');
    }

    public function get_enterprise_details($enterprise_id = '', $statusForExcelDownload = false, $channel_list_with_ping = array())
    {
        if ($enterprise_id == '')
            $customer_id = $this->input->post('enterprise_id');
        else
            $customer_id = $enterprise_id;

        $data['enterprise_contact_details'] = $this->network_model->getEnterpriseContactDetails($customer_id);
        $data['enterprise_finance_details'] = $this->network_model->getEnterpriseFinanceDetails($customer_id);
        $data['enterprise_upload_attachment'] = $this->network_model->getEnterpriseUploadAttachment($customer_id);
        $channel_list = $this->network_model->getAllChannelsForEnterprise($customer_id);
        $startdate = date("Y-m-d", strtotime("-1 week"));
        $enddate = date("Y-m-d");
        if (count($channel_list_with_ping) <= 0) {
            $ping_channel_list = $this->ns_model->get_ping_status($startdate, $enddate);
            foreach ($ping_channel_list as $channel_data) {
                $channel_list_with_ping[] = $channel_data['channel_id'];
            }
        }
        if ($channel_list == NULL) {
            $data['enterprise_channel_details'] = NULL;
        } else {
            $count = 0;
            foreach ($channel_list as $channel_details) {
                $data['enterprise_channel_details'][$count] = array();
                $data['enterprise_channel_details'][$count] = $this->network_model->getEnterpriseChannelDetails($channel_details['tv_channel_id']);
                $data['enterprise_channel_details'][$count]['channel_id'] = $channel_details['tv_channel_id'];
                $data['enterprise_channel_details'][$count]['channel_name'] = $channel_details['channel_name'];
                $data['enterprise_channel_details'][$count]['deployment_status'] = $channel_details['deployment_status'];
                if (in_array($channel_details['tv_channel_id'], $channel_list_with_ping)) {
                    $data['enterprise_channel_details'][$count]['channel_status'] = 'ONLINE';
                } else {
                    $data['enterprise_channel_details'][$count]['channel_status'] = 'OFFLINE';
                }
                $count++;
            }
        }
        if ($statusForExcelDownload)
            return $data;
        else
            echo json_encode($data);

    }

    function writeEachTabIntoExcel($tmpEnterpriseObj, $startRow, $endRow, $startOrgColumn, $endOrgColumn, $merge = 1)
    {
        $newPhpExcelArr = array();
        for ($arrayCount = 0; $arrayCount < count($tmpEnterpriseObj); $arrayCount++) {
            if ($merge) {
                //$arrayCount = 0;
                //loop for converting object to array
                $newPhpExcelArr = (array)($tmpEnterpriseObj[$arrayCount]);
                $networkDetails = array_slice($newPhpExcelArr, 0, 2, true);
                $newPhpExcelArr = array_slice($newPhpExcelArr, 2);
                //echo "<pre>";print_r($networkDetails);print_r($newPhpExcelArr);exit;
                if ($this->phpExcelObj->getActiveSheet()->getCell('B' . $startRow)->getValue() == '') {
                    //setting network id and name
                    $network = $this->network_model->getNetworkDetails($networkDetails['customer_id']);
                    $this->phpExcelObj->getActiveSheet()->setCellValue('B' . $startRow, $network[0]['customer_id']);
                    $this->phpExcelObj->getActiveSheet()->getStyle('B' . $startRow . ':' . 'B' . $endRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->phpExcelObj->getActiveSheet()->getStyle('B' . $startRow . ':' . 'B' . $endRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcelObj->getActiveSheet()->mergeCells('B' . $startRow . ':' . 'B' . $endRow);
                    $this->phpExcelObj->getActiveSheet()->setCellValue('C' . $startRow, $network[0]['customer_name']);
                    $this->phpExcelObj->getActiveSheet()->getStyle('C' . $startRow . ':' . 'C' . $endRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->phpExcelObj->getActiveSheet()->getStyle('C' . $startRow . ':' . 'C' . $endRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcelObj->getActiveSheet()->mergeCells('C' . $startRow . ':' . 'C' . $endRow);
                    $this->phpExcelObj->getActiveSheet()->setCellValue('D' . $startRow, $network[0]['customer_display_name']);
                    $this->phpExcelObj->getActiveSheet()->getStyle('D' . $startRow . ':' . 'D' . $endRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->phpExcelObj->getActiveSheet()->getStyle('D' . $startRow . ':' . 'D' . $endRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcelObj->getActiveSheet()->mergeCells('D' . $startRow . ':' . 'D' . $endRow);
                }

                $startColumn = $startOrgColumn;
                foreach ($newPhpExcelArr as $key => $val) {
                    $this->phpExcelObj->getActiveSheet()->mergeCells($startColumn . $startRow . ':' . $startColumn . $endRow);
                    $this->phpExcelObj->getActiveSheet()->setCellValue($startColumn . $startRow, $val);
                    $this->phpExcelObj->getActiveSheet()->getStyle($startColumn . $startRow . ':' . $startColumn . $endRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->phpExcelObj->getActiveSheet()->getStyle($startColumn . $startRow . ':' . $startColumn . $endRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    ++$startColumn;
                }
            } else {
                $newPhpExcelArr = array();
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['channel_id'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['channel_name'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['channel_status'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['contact_person1'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['email_id1'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['contact_number1'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['contact_person2'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['email_id2'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['contact_number2'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['contact_person3'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['email_id3'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['contact_number3'];
                $genreDetails = array();
                $genreStr = '';
                if (trim($tmpEnterpriseObj[$arrayCount]['genre']) != '') {
                    $genreDetails = $this->network_model->getGenre(str_replace(",", "','", $tmpEnterpriseObj[$arrayCount]['genre']));
                    foreach ($genreDetails as $eachGenre) {
                        $genreStr .= ($genreStr == '') ? $eachGenre['genre'] : "," . $eachGenre['genre'];
                    }
                    $newPhpExcelArr[] = $genreStr;
                } else {
                    $newPhpExcelArr[] = '';
                }
                $dominant_contentStr = '';
                if (trim($tmpEnterpriseObj[$arrayCount]['dominant_content']) != '') {
                    $dominant_contentDetails = $this->network_model->getdominantContent(str_replace(",", "','", $tmpEnterpriseObj[$arrayCount]['dominant_content']));
                    foreach ($dominant_contentDetails as $eachDominant_content) {
                        $dominant_contentStr .= ($dominant_contentStr == '') ? $eachDominant_content['dominant_content'] : "," . $eachDominant_content['dominant_content'];
                    }
                    $newPhpExcelArr[] = $dominant_contentStr;
                } else {
                    $newPhpExcelArr[] = '';
                }

                $languageDetails = array();
                $languageStr = '';
                if (trim($tmpEnterpriseObj[$arrayCount]['language']) != '') {
                    $languageDetails = $this->network_model->getlanguage(str_replace(",", "','", $tmpEnterpriseObj[$arrayCount]['language']));
                    foreach ($languageDetails as $eachLanguage) {
                        $languageStr .= ($languageStr == '') ? $eachLanguage['language'] : "," . $eachLanguage['language'];
                    }
                    $newPhpExcelArr[] = $languageStr;
                } else {
                    $newPhpExcelArr[] = '';
                }
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['address'];
                $newPhpExcelArr[] = $tmpEnterpriseObj[$arrayCount]['deployment_status'];
                $startColumn = $startOrgColumn;
                foreach ($newPhpExcelArr as $key => $val) {
                    $this->phpExcelObj->getActiveSheet()->setCellValue($startColumn . $startRow, $val);
                    $this->phpExcelObj->getActiveSheet()->getStyle($startColumn . $startRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    ++$startColumn;
                }
                $startRow = $startRow + 1;
            }

        }
    }

    public function update_language_genre()
    {
        $changedChannelIDForGenre = $this->input->post('changedChIDforGenre');
        $changedChannelIDForLanguage = $this->input->post('changedChIDforlanguage');
        $changedChIDfordominanatContent = $this->input->post('changedChIDfordominanatContent');
        if ($changedChannelIDForGenre != '' || $changedChannelIDForLanguage != '' || $changedChIDfordominanatContent != '') {
            $select_genreArr = $this->input->post('select_genre');
            $select_langaugeArr = $this->input->post('select_langauge');
            $select_dominant_contentArr = $this->input->post('select_dominant_content');
            $changedChannelIDForGenreArr = explode(",", $changedChannelIDForGenre);
            $changedChannelIDForLanguageArr = explode(",", $changedChannelIDForLanguage);
            $changedChIDfordominantContentArr = explode(",", $changedChIDfordominanatContent);
            foreach ($changedChannelIDForGenreArr as $val) {
                if (array_key_exists($val, $select_genreArr)) {
                    $data['genre'] = implode(",", $select_genreArr[$val]);
                    if (in_array($val, $changedChannelIDForLanguageArr)) {
                        $data['language'] = implode(",", $select_langaugeArr[$val]);
                        unset($select_langaugeArr[$val]);
                    }
                    if (in_array($val, $changedChIDfordominantContentArr)) {
                        $data['dominant_content'] = implode(",", $select_dominant_contentArr[$val]);
                        unset($select_dominant_contentArr[$val]);
                    }
                    $res = $this->network_model->updateLanguageGenre($val, $data);
                }
            }
            foreach ($changedChannelIDForLanguageArr as $val) {
                if (array_key_exists($val, $select_langaugeArr)) {
                    $data['language'] = implode(",", $select_langaugeArr[$val]);
                    $res = $this->network_model->updateLanguageGenre($val, $data);
                }
            }
            foreach ($changedChIDfordominantContentArr as $val) {
                if (array_key_exists($val, $select_dominant_contentArr)) {
                    $data['dominant_content'] = implode(",", $select_dominant_contentArr[$val]);
                    $res = $this->network_model->updateLanguageGenre($val, $data);
                }
            }
        }
        if ($res) {
            echo '<script language="javascript">alert("Data Updated Successfully");</script>';
            echo '<script language="javascript">top.location.href="' . base_url("network_svc_manager/getGenereAndLanguageByChannelWise/") . '";</script>';
        } else {
            echo '<script language="javascript">alert("Data Updated Unsuccessfully");</script>';
            echo '<script language="javascript">top.location.href="' . base_url("network_svc_manager/getGenereAndLanguageByChannelWise/") . '";</script>';
        }
        //redirect(base_url("network_svc_manager/manageChannelName/")) ;
    }

    public function displayGenereAndLanguageByChannelWise()
    {
        $filterParam = array();
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $profile_details = $this->user_model->get_profile_details($profile_id);
        $menu = $this->menu_model->get_header_menu();
        $data = array();
        $data['logged_in_user'] = $logged_in[0];
        $data['profile_details'] = $profile_details[0];
        $data['menu'] = $menu;
        $data['filterParam'] = $filterParam;
        $data['all_genre'] = $this->network_model->getGenre();
        $data['all_dominantContent'] = $this->network_model->getdominantContent();
        $data['all_language'] = $this->network_model->getlanguage();
        $data['displayGenereLanguageArr'] = $this->network_model->showGenereAndLanguageByChannelWise();
        $startdate = date("Y-m-d", strtotime("-1 week"));
        $enddate = date("Y-m-d");
        $channel_list = $this->ns_model->get_ping_status($startdate, $enddate);
        foreach ($channel_list as $channel_data) {
            $channel_list_with_ping[] = $channel_data['channel_id'];
        }
        $data['channel_list_with_ping'] = $channel_list_with_ping;
        $set_download = $this->input->post('set_download');
        if ($set_download) {
            $this->downloadExcelForDisplayedGenreAndLanguage($data);
        } else {
            $this->load->view('network_manager/display_genre_language', $data);
        }

    }

    function downloadExcelForDisplayedGenreAndLanguage($data)
    {
        //echo "<pre>";print_r($data);exit;
        $fontStyleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 10,
                'name' => 'Verdana'
            )
        );
        $headingStyleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                    'color' => array('rgb' => '538ED5')
                )
            )
        );
        $colorArray = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => '538ED5'),
        );
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $title = 'Genre Language Details';
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);

        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'STATE');
        $objPHPExcel->getActiveSheet()->setCellValue('C2', 'NETWORK NAME');
        $objPHPExcel->getActiveSheet()->setCellValue('D2', 'CHANNEL NAME');
        $objPHPExcel->getActiveSheet()->setCellValue('E2', 'GENRE');
        $objPHPExcel->getActiveSheet()->setCellValue('F2', 'DOMINANT CONTENT');
        $objPHPExcel->getActiveSheet()->setCellValue('G2', 'LANGUAGE');
        $objPHPExcel->getActiveSheet()->setCellValue('H2', 'CHANNEL STATUS');
        $objPHPExcel->getActiveSheet()->setCellValue('I2', 'DEPLOYMENT STATUS');


        $objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:I2')->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B2:I2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B2:I2')->applyFromArray($headingStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getFill()->applyFromArray($colorArray);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
        $startRowCell = 3;
        foreach ($data['displayGenereLanguageArr'] as $key => $val) {
            switch ($val['id']) {
                case 12:
                    $val['state'] .= ' and Telengana';
                    break;
                case 14:
                    $val['state'] .= ' and Suburbs';
                    break;
                case 15:
                    $val['state'] .= ' and NCR';
                    break;
            }
            $startColCell = 'B';
            $startCalcRowCell = $startRowCell + $key;
            $objPHPExcel->getActiveSheet()->setCellValue($startColCell . $startCalcRowCell, $val['state']);
            $objPHPExcel->getActiveSheet()->setCellValue(++$startColCell . $startCalcRowCell, $val['network_name']);
            $objPHPExcel->getActiveSheet()->setCellValue(++$startColCell . $startCalcRowCell, $val['channel_names']);
            $saved_genreIds = explode(",", $val['genre']);
            $genreStr = '';
            foreach ($data['all_genre'] as $key => $arr_genre_val) {
                if (in_array($arr_genre_val['id'], $saved_genreIds)) {
                    if ($genreStr == '') {
                        $genreStr = $arr_genre_val['genre'];
                    } else {
                        $genreStr .= ',' . $arr_genre_val['genre'];
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue(++$startColCell . $startCalcRowCell, $genreStr);

            $dominanatContentStr = '';
            $saved_dominantContentIds = explode(",", $val['dominant_content']);
            foreach ($data['all_dominantContent'] as $key => $arr_dominantContent_val) {
                if (in_array($arr_dominantContent_val['id'], $saved_dominantContentIds)) {
                    if ($dominanatContentStr == '') {
                        $dominanatContentStr = $arr_dominantContent_val['dominant_content'];
                    } else {
                        $dominanatContentStr .= ',' . $arr_dominantContent_val['dominant_content'];
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue(++$startColCell . $startCalcRowCell, $dominanatContentStr);

            $languageStr = '';
            $saved_languageIds = explode(",", $val['language']);
            foreach ($data['all_language'] as $key => $arr_langauge_val) {
                if (in_array($arr_langauge_val['id'], $saved_languageIds)) {
                    if ($languageStr == '') {
                        $languageStr = $arr_langauge_val['language'];
                    } else {
                        $languageStr .= ',' . $arr_langauge_val['language'];
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue(++$startColCell . $startCalcRowCell, $languageStr);


            $channel_status = '';
            if (in_array($val['channel_id'], $data['channel_list_with_ping'])) {
                $channel_status = 'ONLINE';
            } else {
                $channel_status = 'OFFLINE';
            }
            $objPHPExcel->getActiveSheet()->setCellValue(++$startColCell . $startCalcRowCell, $channel_status);
            $objPHPExcel->getActiveSheet()->setCellValue(++$startColCell . $startCalcRowCell, $val['deployment_status']);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="genre_language_details.xls"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=genre_language_details.xls');
        header('Content-Transfer-Encoding: binary');
        ob_clean();
        flush();
        $objWriter->save('php://output');

    }

    public function getClientMarketChannels()
    {
        $logged_in_user = $this->session->userdata("logged_in_user");

        $menu = $this->menu_model->get_header_menu();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_details'] = $profile_details[0];
        $model['menu'] = $menu;

        $client = $this->ns_model->getActiveClient();
        $model['client'] = $client;

        //Get All Market
        $market = $this->ns_model->getAllMarket();
        $model['market'] = $market;

        //Get All Channels
        $channels = $this->ns_model->getAllChannel();
        $model['channel'] = $channels;

        //$model['submit'] = array('type' => 'submit','value'=> 'Submit','class'=> 'blue_btn');

        $this->load->view('network_manager/client_market_channel', $model);
    }

    public function establishClientChannelRelations()
    {
        $client_id = $this->input->post('client');
        $market_id = $this->input->post('market');
        $channel_id = $this->input->post('channel');

        $this->ns_model->updateClientMarketChannel($client_id, $market_id, $channel_id);
        //$this->getClientMarketChannels() ;
        redirect("network_svc_manager/getClientMarketChannels");
    }

    public function getChannelForMarket()
    {
        $market_id = $this->input->post('market_id');
        $channels = $this->ns_model->getChannelForMarket($market_id);
        echo json_encode($channels);
    }
}

/* End of network_svc_manager.php */
