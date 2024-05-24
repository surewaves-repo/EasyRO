<?php
if (!defined('BASEPATH'))
    exit("NO Direct Script Access Allowed");

/*
 * Class for Network Service Managers
 * advance_ro.php
 *
 * Author : Madhup Mani
 */

class Advanced_ro_manager extends CI_Controller
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
        $this->load->model("common_model");

        //Loading Libraries
        $this->load->library('session');
        $this->load->library('curl');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('email');

        //Loading Helpers
        $this->load->helper("app_validation_helper");
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->helper('url');

        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    public function create_advanced_ext_ro($cust_ro = '', $ro_date = '', $agency = '', $agency_contact = 'new', $client = '', $client_contact = 'new')
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];
        $model['all_agency'] = $this->am_model->get_all_agency();
        $model['all_client'] = $this->am_model->get_all_adv();
        $model['all_markets'] = $this->am_model->get_all_markets();
        $model['all_clusters'] = $this->am_model->get_all_markets(1);
        $cust_ro = urldecode($cust_ro);
        $cust_ro = str_replace("~", "/", $cust_ro);
        $model['cust_ro'] = $cust_ro;
        $model['ro_date'] = $ro_date;
        $model['agency'] = urldecode($agency);
        $model['agency_contact'] = $agency_contact;
        $model['client'] = urldecode($client);
        $model['client_contact'] = $client_contact;
        $model['user_name'] = $logged_in[0]['user_name'];
        $model['user_id'] = $logged_in[0]['user_id'];
        //Getting login user regions
        $model['regionArray'] = $this->am_model->getLoginUserRegions($logged_in[0]['user_id']);

        $this->load->view('advanced_ro/create_advanced_ext_ro', $model);
    }

    public function is_logged_in($javascript_redirect = 0)
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

    public function post_create_advanced_ext_ro()
    {
        $this->is_logged_in();

        $logged_in = $this->session->userdata("logged_in_user");
        $cust_ro = trim($this->input->post('txt_ext_ro'));
        $agency = $this->input->post('sel_agency');
        $client = $this->input->post('sel_client');
        $brand = $this->input->post('hid_brand');
        $industry = '';
        $user_id = $this->input->post('hid_user_id');
        $vertical = '';
        $region = $this->input->post('regionSelectBox');
        $is_test_user = $this->input->post('user_type');
        $ro_date = $this->input->post('txt_ro_date');
        $content_name_spot = $this->input->post('txt_content_name_spot');
        $content_duration_spot = $this->input->post('txt_content_duration_spot');
        $content_name_banner = $this->input->post('txt_content_name_banner');
        $content_duration_banner = $this->input->post('txt_content_duration_banner');
        $camp_start_date = $this->input->post('txt_camp_start_date');
        $camp_end_date = $this->input->post('txt_camp_end_date');
        $booking = '';
        $category = '';
        $gross = $this->input->post('txt_gross');
        $agency_com = $this->input->post('txt_agency_com');
        $net_agency_com = $this->input->post('txt_net_agency_com');
        $spcl_inst = $this->input->post('txt_spcl_inst');
        $file_pdf = $_FILES['file_pdf']['name'];
        $make_good_type = $this->input->post('rd_make_good');
        $agency_contact_id = $this->input->post('sel_agency_contact');
        $client_contact_id = $this->input->post('sel_client_contact');


        $market = '';
        foreach ($_POST['markets'] as $market_name => $market_ro_amount) {
            if ($market_ro_amount['spot'] == 0 && $market_ro_amount['banner'] == 0) {
                continue;
            }
            if ($market == '') {
                $market = $market_name;
                $market = str_replace("_", " ", $market);
            } else {
                $market .= ',' . $market_name;
                $market = str_replace("_", " ", $market);
            }
        }

        //$running_no = $this->am_model->get_financial_running_number($ro_date);
        $running_no = $this->am_model->get_financial_running_number_v1($ro_date, $is_test_user);

        $running_no = $running_no + 1;
        $db_running_no = sprintf('%04d', $running_no);
        $financial_year = $this->am_model->get_financial_year_for_ro_date($ro_date);
        $db_financial_year = $this->am_model->get_financial_year_for_ro_date_v1($ro_date);
        $running_no = $financial_year . '-' . $db_running_no;
        // end

        $internal_ro_no = $this->am_model->generate_internal_ro_number_advance_ro($client, $agency, $cust_ro, $camp_start_date, $running_no);
        if ($client_contact_id == 'new') {
            $client_contact_id = 0;
        }

        $chk_cust_ro_exist_qry = $this->am_model->check_for_ro_existence_for_new($cust_ro);
        if ($chk_cust_ro_exist_qry > 0) {
            echo '<span style="color:#F00;">The external ro already exists.</span>';
        } else {
            if ($file_pdf != '') {
                if (!is_dir('easy_ro_temp_pdf')) {
                    mkdir('easy_ro_temp_pdf');
                    //shell_exec("chmod 0777 /surewaves_easy_ro/easy_ro_temp_pdf/");
                }
                $config['upload_path'] = 'easy_ro_temp_pdf/';
                $config['allowed_types'] = '*';
                $this->load->library('upload', $config);
                $this->upload->do_upload('file_pdf');
                $data = $this->upload->data();
                // added by lokanath to convert the uploaded file to zip file
                if ($_FILES['file_pdf']['type'] != 'application/zip') {
                    $zip = new ZipArchive();
                    if ($zip->open('easy_ro_temp_pdf/' . $file_pdf . ".zip", ZIPARCHIVE::CREATE) != TRUE) {
                        die ("Could not read ro attachment");
                    }
                    $pattern = '/[ ]+/';
                    $replced_file_name = preg_replace($pattern, "_", $file_pdf);
                    $zip->addFile('easy_ro_temp_pdf/' . $replced_file_name, $file_pdf);
                    // close and save archive
                    $zip->close();
                    $file_pdf = $file_pdf . ".zip";
                } else {
                    $file_pdf = $file_pdf;
                }
                // end
                $file_location = 'easy_ro_temp_pdf/' . $file_pdf;
                // upload to S3
                require_once("S3.php");
                $s3 = new S3(AMAZON_KEY, AMAZON_VALUE);
                S3::putObject(S3::inputFile("$file_location"), "sw_easy_ro_am_pdf", "$file_pdf", S3::ACL_PUBLIC_READ);
                // end uploading
                $file_pdf_s3_url = "https://s3.amazonaws.com/sw_easy_ro_am_pdf/" . $file_pdf;
            }

            $mailList = implode(",", $this->input->post('Order_history_recipient_ids'));

            $create_ro_data = array(
                'cust_ro' => $cust_ro,
                'internal_ro' => $internal_ro_no,
                'agency' => $agency,
                'client' => $client,
                'brand' => $brand,
                'industry' => $industry,
                'user_id' => $user_id,
                'vertical' => $vertical,
                'region_id' => $region,
                'market' => $market,
                'ro_date' => $ro_date,
                'camp_start_date' => $camp_start_date,
                'camp_end_date' => $camp_end_date,
                'booking' => $booking,
                'category' => $category,
                'gross' => $gross,
                'agency_com' => $agency_com,
                'net_agency_com' => $net_agency_com,
                'spcl_inst' => $spcl_inst,
                'file_path' => $file_pdf_s3_url,
                'make_good_type' => $make_good_type,
                'agency_contact_id' => $agency_contact_id,
                'client_contact_id' => $client_contact_id,
                'previous_ro_amount' => $gross,
                'financial_year_running_no' => $db_running_no,
                'financial_year' => $db_financial_year,
                'test_user_creation' => $is_test_user,
                'order_history_mail_list' => $mailList
            );

            /*insert ro details in table*/
            $this->am_model->insert_into_am_ext_ro($create_ro_data);
            $am_external_ro_id = $this->am_model->get_last_insert_id();

            //update ro_status
            $this->am_model->update_ro_status($am_external_ro_id, 'scheduling_in_progress');
            //update/insert into ro_cancel_external_ro
            $approval_progression_data = array(
                'ext_ro_id' => $am_external_ro_id,
                'cancel_type' => 'submit_ro_approval',
                'user_id' => $user_id,
                'date_of_submission' => date('Y-m-d'),
                'date_of_cancel' => date('Y-m-d'),
                'reason' => 'None',
                'invoice_instruction' => 'None',
                'ro_amount' => $gross,
                'cancel_ro_by_admin' => 1,
                'bh_reason' => '',
                'net_contribuition_percent' => 0.00,
                'approval_level' => 3
            );
            $this->am_model->insert_into_cancel_market($approval_progression_data);
            $approved_data = array(
                'ext_ro_id' => $am_external_ro_id,
                'cancel_type' => 'ro_approval',
                'user_id' => $user_id,
                'date_of_submission' => date('Y-m-d'),
                'date_of_cancel' => date('Y-m-d'),
                'reason' => 'None',
                'invoice_instruction' => 'None',
                'ro_amount' => $gross,
                'cancel_ro_by_admin' => 1,
                'bh_reason' => '',
                'net_contribuition_percent' => 0.00,
                'approval_level' => 3
            );
            $this->am_model->insert_into_cancel_market($approved_data);

            // activate advertiser if non active
            $data = array(
                'active' => 1
            );
            $where = array(
                'advertiser' => $client
            );
            $this->am_model->update_new_advertiser($data, $where);

            //insert data into ro_content
            $content_data_spot = array(
                'ro_id' => $am_external_ro_id,
                'content_name' => $content_name_spot,
                'content_duration' => $content_duration_spot,
                'region_id' => 1
            );
            $this->am_model->insert_content_data_for_ro($content_data_spot);

            $content_data_banner = array(
                'ro_id' => $am_external_ro_id,
                'content_name' => $content_name_banner,
                'content_duration' => $content_duration_banner,
                'region_id' => 3
            );
            $this->am_model->insert_content_data_for_ro($content_data_banner);


            //change by mani : updating ro amount in ro_amount
            $ro_amount_data = array(
                'customer_ro_number' => $cust_ro,
                'internal_ro_number' => $internal_ro_no,
                'ro_amount' => $gross,
                'agency_commission_amount' => $agency_com,
                'timestamp' => date("Y-m-d H:i:s"),
                'approval_timestamp' => date("Y-m-d H:i:s"),
                'approval_user_id' => $user_id
            );
            $this->am_model->add_ro_amount($ro_amount_data);

            // add individual market amount
            $this->am_model->add_ro_amount_for_each_market($am_external_ro_id);

            $campaign_json = array();
            $campaign_json['customerRo'] = $cust_ro;
            $campaign_json['internalRo'] = $internal_ro_no;
            $campaign_json['spotContentName'] = $content_name_spot;
            $campaign_json['spotContentDuration'] = $content_duration_spot;
            $campaign_json['bannerContentname'] = $content_name_banner;
            $campaign_json['bannerContentDuration'] = $content_duration_banner;
            $campaign_json['brandName'] = $brand;
            $campaign_json['clientName'] = $client;
            $campaign_json['agencyName'] = $agency;
            $campaign_json['startDate'] = $camp_start_date;
            $campaign_json['endDate'] = $camp_end_date;
            $campaign_json['marketInfo'] = $this->getEachMarketData($_POST['markets'], $_POST['FCT']);

            //Insert Into Job Queue For Creation Of Campaign
            $job_queue_user_data = array(
                'job_code' => 'createCampaignAdvanceRo',
                'done' => '0',
                'params' => $internal_ro_no,
                'customer_id' => $logged_in[0]['user_id'],
                'longParams' => json_encode($campaign_json)
            );
            $this->am_model->insert_into_job_queue($job_queue_user_data);

            //create external ro
            //$model['res_create_ext_ro'] = $this->am_model->create_external_ro();

            $external_ro = $this->input->post('txt_ext_ro');
            $file_pdf = $_FILES['file_pdf']['name'];
            $file_type = $_FILES['file_pdf']['type'];
            if ($_FILES['file_pdf']['type'] != 'application/zip') {
                $file_pdf = $file_pdf . ".zip";
            } else {
                $file_pdf = $file_pdf;
            }
            $file_location = 'easy_ro_temp_pdf/' . $file_pdf;
            if ($file_pdf == '') {
                $file_location = '';
            }

            //get all data of am_ext_ro for internal ros
            $ro_details = $this->am_model->ro_detail_for_external_ro($external_ro);

            //insert into ro_progression_mail_status
            $user_data = array(
                'ro_id' => $ro_details[0]['id'],
                'submit_status' => 'submitted'
            );
            $this->mg_model->insert_into_progression_mail_status(array('ro_id' => $ro_details[0]['id']), $user_data);

            // send mail to coo/bh/scheduling_user
            $users = $this->am_model->get_mailto_list();
            $email_data = array();
            foreach ($users as $val) {
                if (empty($val['user_email']) || !isset($val['user_email'])) continue;
                array_push($email_data, $val['user_email']);
            }
            $to_email = implode(",", $email_data) . "," . $logged_in[0]['user_email'];
            $make_good_type = '';
            if ($this->input->post('rd_make_good') == 0) {
                $make_good_type = 'Auto Make Good';
            } else if ($this->input->post('rd_make_good') == 1) {
                $make_good_type = 'Client approved make good';
            } else {
                $make_good_type = 'No Make Good';
            }
            mail_send_v1($to_email,
                "create_ext_ro",
                array('EXTERNAL_RO' => $external_ro),
                array(
                    'AM_NAME' => $logged_in[0]['user_name'],
                    'EXTERNAL_RO' => $external_ro,
                    'INTERNAL_RO' => $ro_details[0]['internal_ro'],
                    'AGENCY' => $this->input->post('sel_agency'),
                    'CLIENT' => $this->input->post('sel_client'),
                    'BRAND' => $this->am_model->get_brand_names($ro_details[0]['brand']),
                    'MAKEGOOD_TYPE' => $make_good_type,
                    'MARKET' => $this->input->post('hid_market'),
                    'INSTRUCTION' => $this->input->post('txt_spcl_inst'),
                    'START_DATE' => $this->input->post('txt_camp_start_date'),
                    'END_DATE' => $this->input->post('txt_camp_end_date')
                ),
                $file_location,
                '',
                '',
                $file_type
            );
        }

        // close colorbox and refresh parent for showing created ro
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';

    }

    public function getEachMarketData($market, $fct)
    {
        $data = array();
        array_push($market, $fct);

        foreach ($market as $marketId => $market_ro_amount) {
            $marketName = str_replace("_", " ", $marketId);
            if ($market_ro_amount['spot'] == 0 && $market_ro_amount['banner'] == 0) {
                continue;

            }
            $marketData = $this->common_model->getMarketData(array('sw_market_name' => $marketName));
            $tmp = array();
            $tmp['marketName'] = $marketName;
            $tmp['marketId'] = $marketData[0]['id'];
            $tmp['spotFCT'] = $market[0][$marketId]['spot_FCT'];
            $tmp['bannerFCT'] = $market[0][$marketId]['banner_FCT'];

            array_push($data, $tmp);

        }
        return $data;
    }

    //function used to get already linked advanced ros by ajax :Nitish
    public function get_linked_advanced_ros()
    {
        $cust_ro_id = $this->input->post('cust_ro_id');
        $linked_adv_ros = $this->am_model->get_linked_advanced_ros($cust_ro_id);
        /*$ros_linked = array();
        foreach($linked_adv_ros as $lro){
            array_push($ros_linked,$lro['advance_ro_id']);
        }*/
        if (isset($linked_adv_ros) && $linked_adv_ros != null) {
            echo json_encode($linked_adv_ros);
        } else {
            echo 'No Result';
        }

    }

    //function used to get data for link advanced ro page : Nitish
    public function link_advanced_ro()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $menu = $this->menu_model->get_header_menu();
        $cust_ros = $this->am_model->get_all_cust_ros();
        $advanced_ros = $this->am_model->get_all_advanced_ros();
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];
        $model['cust_ros'] = $cust_ros;
        $model['advanced_ros'] = $advanced_ros;
        $this->load->view('advanced_ro/link_advanced_ro', $model);
    }

    //function used to recieve post data from link advanced ro page : Nitish
    public function post_link_advanced_ext_ro()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $cust_ro_id = $this->input->post('sel_ext_ro');
        $adv_ro_id = $this->input->post('sel_adv_ro');

        //delete records for that ro if any
        $this->am_model->remove_link_to_advanced_ros(array('ro_id' => $cust_ro_id));

        foreach ($adv_ro_id as $adv) {
            $link_data = array(
                'ro_id' => $cust_ro_id,
                'advance_ro_id' => $adv
            );
            $link_ro = $this->am_model->link_ro_to_advanced_ro($link_data);
        }

        // close colorbox and refresh parent for showing created ro
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }


}

/* End of advanced_ro_manager.php */
