<?php if (!defined('BASEPATH')) exit("NO Direct Script Access Allowed");

class Non_fct_ro extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        $this->load->model("menu_model");
        $this->load->model("am_model");
        $this->load->model('ro_model');
        $this->load->model('user_model');
        $this->load->model('non_fct_ro_model');
        $this->load->helper('url');
        $this->load->helper("generic");
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    //function to check whether user is already logged in or not

    public function search_content()
    {
        $this->session->unset_userdata("search_str");
        $search_str = $this->input->post("search_str");
        $this->session->set_userdata("search_str", $search_str);
        $uri = $_SERVER['HTTP_REFERER'];
        //$this->home();
        $this->home(0, 1);
    }

    public function home($start_index = 0, $serch_set = 0)
    {
        //$this->find_data_for_cache() ;
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];

        $list1 = $this->non_fct_ro_model->get_non_fct_ros($logged_in[0]['profile_id'], $logged_in[0]['user_id'], $logged_in[0]['is_test_user']);
        //$list2 = $this->am_model->get_campaigns_approved($logged_in[0]['profile_id'],$logged_in[0]['user_id']);
        if (empty($list2)) {
            $ro_lists = $list1;
        } else {
            foreach ($list1 as $key => $l) {
                $ro_lists[] = $l;
            }
            $count = count($ro_lists);
            foreach ($list2 as $key => $l) {
                $ro_lists[$count] = $l;
                $count++;
            }
        }

        $total_rows = 0;
        $search_str = $this->session->userdata("search_str");//echo $search_str;
        if (isset($search_str) && !empty($search_str) && $serch_set == 1) {
            //$search_str = base64_decode($search_str);
            $ro_lists = filter_using_search_str($ro_lists, array('client', 'customer_ro_number', 'agency', 'internal_ro_number', 'account_manager_name', 'approved_by'), $search_str);
            $model['search_str'] = $search_str;
            $total_rows = count($ro_lists);
            // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);//print_r($ro_lists);exit;
            $model['ro_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
            $model['page_links'] = create_page_links(base_url() . "/non_fct_ro/home", ITEM_PER_PAGE_USERS, $total_rows, $serch_set);
            //$model['page_links'] = $model['page_links'].'/1';
            //$this->load->view('account_manager/home/'.$start_index.'/1',$model);
        } else {
            $total_rows = count($ro_lists);
            // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);//print_r($ro_lists);exit;
            $model['ro_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
            $model['page_links'] = create_page_links(base_url() . "/non_fct_ro/home", ITEM_PER_PAGE_USERS, $total_rows);
            //$this->load->view('account_manager/home',$model);
        }

        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;

        $model['userRegion'] = $this->session->userdata('logged_in_user_none_region');
        $this->load->view('non_fct_ro/home', $model);
    }

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


    //function to show all NON FCT ros

    public function search_content_approved()
    {
        $this->session->unset_userdata("search_str");
        $search_str = $this->input->post("search_str");
        $this->session->set_userdata("search_str", $search_str);
        $this->session->set_userdata("approved_ros_search_str", $search_str);
        $uri = $_SERVER['HTTP_REFERER'];
        $parts = explode('/', $uri);
        $this->approved_ros(0, 1);

        $this->session->unset_userdata("search_str");
    }

    public function approved_ros($start_index = 0, $serch_set = 0)
    {//echo '<pre>';print_r($this->session->userdata);exit;

        //$this->find_data_for_cache() ;
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $value = 0;

        $list2 = $this->non_fct_ro_model->get_non_fct_approved_ros($logged_in[0]['profile_id'], $logged_in[0]['user_id'], $logged_in[0]['is_test_user']);
        // end

        if (empty($list2)) {
            $value = 0;
        } else {
            $ro_lists = $list2;
            $value = 1;
        }
        $internal_ros = array();
        foreach ($ro_lists as $ros) {
            array_push($internal_ros, $ros['internal_ro_number']);
        }
        $total_rows = 0;
        $search_str = $this->session->userdata("approved_ros_search_str");//echo $search_str;echo '<pre>';print_r($this->session->userdata);exit;
        if (isset($search_str) && !empty($search_str) && $serch_set == 1) {
            $ro_lists = filter_using_search_str($ro_lists, array('client', 'customer_ro_number', 'agency', 'internal_ro_number', 'account_manager_name', 'approved_by'), $search_str);
            $model['search_str'] = $search_str;
            $total_rows = count($ro_lists);
            // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);
            $model['value'] = $value;
            $model['ro_list'] = $filtered_ros;
            $model['page_links'] = create_page_links(base_url() . "/non_fct_ro/approved_ros", ITEM_PER_PAGE_USERS, $total_rows, $serch_set);
        } else {
            $total_rows = count($ro_lists);
            // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);
            $model['value'] = $value;
            $model['ro_list'] = $filtered_ros;
            $model['page_links'] = create_page_links(base_url() . "/non_fct_ro/approved_ros", ITEM_PER_PAGE_USERS, $total_rows);
        }

        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;
        $this->load->view('non_fct_ro/approved_ros', $model);
    }


    public function postCreateNonFctRo()
    {
        //echo print_r($_POST,true);exit;
        //Fill this array with data
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $financialYear = explode("-", $this->input->post('fin_year'));

        $clientName = $this->input->post('sel_client');
        $agencyName = $this->input->post('sel_agency');
        $customerRo = $this->input->post('txt_ext_ro');
        $running_no = $this->non_fct_ro_model->getFinancialRunningNumber($financialYear[0]);
        $running_no = $running_no + 1;

        //RC:Deepak - Use this method to generate internal number
        //Fixed
        $internalRoNumber = $this->generateInternalRo($clientName, $agencyName, $customerRo, $running_no);
        $userData = array(
            'customer_ro_number' => $customerRo,
            'internal_ro_number' => $internalRoNumber,
            'agency' => $agencyName,
            'agency_contact' => $this->input->post('sel_agency_contact'),
            'client' => $clientName,
            'client_contact' => $this->input->post('sel_client_contact'),
            'account_manager_name' => $this->input->post('txt_am_name'),
            'user_id' => $this->input->post('hid_user_id'),
            'financial_year' => $financialYear[0],
            'financial_year_running_no' => $running_no,
            'gross_ro_amount' => $this->input->post('txt_gross'),
            'agency_commision' => $this->input->post('txt_agency_com'),
            'description' => $this->input->post('txt_spcl_inst'),
            'region_id' => $this->input->post('regionSelectBox')
        );
        $roId = $this->non_fct_ro_model->createNonFctRo($userData);

        $monthlyAmount = $this->input->post('amount');
        foreach ($monthlyAmount as $month => $amount) {
            /*if(empty($amount) || !isset($amount)) {
                continue ;
            }else{*/
            $monthlyPriceUserData = array(
                'non_fct_ro_id' => $roId,
                'month' => $month,
                'price' => $amount
            );
            $this->non_fct_ro_model->createNonFctMonthlyPrice($monthlyPriceUserData);
            /*}*/
        }
        $regionData = $this->non_fct_ro_model->getRegionData(array('id' => $this->input->post('regionSelectBox')));

        if ($regionData[0]['is_international'] == 1) {
            $approvalLevel = 3;

            //Email For Business Head
            $userEmails = $this->user_model->get_bhs();
            $emailIds = array();
            foreach ($userEmails as $email) {
                array_push($emailIds, $email['user_email']);
            }
            $cc_email_list = implode(",", $emailIds);
        } else {
            $approvalLevel = 2;

            $email_data = $this->user_model->userEmailForRoCreation($logged_in[0]['user_id']);
            $emails = $this->user_model->getEmailIdsForProfile(array(1, 10));
            $reportingManagerDetail = $this->user_model->getUserDetailOfUserReportingManager($logged_in[0]['user_id']);
            $cc_email_list = $email_data . "," . $emails . "," . $reportingManagerDetail[0]['user_email'];
        }
        $approvalUserData = array(
            'non_fct_ro_id' => $roId,
            'request_type' => 'ro_approval',
            'approved_by' => 0,
            'approval_level' => $approvalLevel,
            'requested_date' => date('Y-m-d H:i:s')
        );
        $this->non_fct_ro_model->approvalRequestNonFctRo($approvalUserData);


        $data = array(
            'ro_id' => $roId,
            'mail_type' => 'non_fct_ro_approval',
            'mail_status' => 0,
            'approval_level' => $approvalLevel,
            'user_email_id' => $logged_in[0]['user_email'],
            'cc_email_id' => $cc_email_list,
            'file_name' => '',
            'mail_sent_date' => date('Y-m-d'),
            'mail_sent' => 0
        );
        $this->ro_model->insertForMail($data);

        //record RO creation history details
        $this->user_model->ro_creation_history($logged_in[0]['user_id'], $roId, 0);

        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/MailSentForRo > /dev/null &");

//        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    public function generateInternalRo($client_name, $agency_name, $customerRo, $running_no)
    {
        $ro_prefix = 'SW' . '/' . $running_no . '/' . $client_name . '/' . $agency_name . '/';
        $ro_prefix_db = $client_name . '/' . $agency_name . '/';

        $internalRoNumber = $this->non_fct_ro_model->getNonFctRoForCustomerRoAndPrfeix($customerRo, $ro_prefix);
        if (!empty($internalRoNumber) && isset($internalRoNumber)) {
            return $internalRoNumber;
        } else {
            return $this->non_fct_ro_model->getNonFctRoForGeneratingInternalRo($ro_prefix_db, $ro_prefix);
        }
    }

    public function post_edit_non_fct_ext_ro()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $cust_ro = trim($this->input->post('txt_ext_ro'));
        $ro_id = $this->input->post('hid_id');
        $user_id = $this->input->post('hid_user_id');
        $account_manager_id = $this->input->post('sel_am');
        $agency = $this->input->post('txt_agency');
        $agency_contact = $this->input->post('sel_agency_contact_name');
        $client = $this->input->post('txt_client');
        $client_contact = $this->input->post('sel_client_contact_name');
        $amount = $this->input->post('amount');
        $gross = $this->input->post('txt_gross');
        $agency_com = $this->input->post('txt_agency_com');
        $description = $this->input->post('txt_spcl_inst');

        $whereData = array('id' => $ro_id);
        $userData = array(
            'agency_contact' => $agency_contact,
            'client_contact' => $client_contact,
            'gross_ro_amount' => $gross,
            'agency_commision' => $agency_com,
            'description' => $description
        );
        //update non fct ro details
        $this->non_fct_ro_model->updateNonFctRo($whereData, $userData);

        //update monthwise amounts
        foreach ($amount as $key => $monthly) {
            $whereData = array('non_fct_ro_id' => $ro_id, 'month' => $key);
            $userData = array(
                'price' => $amount[$key]
            );
            $this->non_fct_ro_model->updateNonFctAmounts($whereData, $userData);
        }

        // close colorbox and refresh parent for showing created non fct ro
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    public function approve_non_fct($orderId)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $internalRoNumber = base64_decode($orderId);

        $model = array();
        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;

        $nonfct_result = $this->non_fct_ro_model->getNonFctRoForInternalRo($internalRoNumber);

        $region_id = $nonfct_result[0]['region_id'];
        $regionData = $this->non_fct_ro_model->getRegionData(array('id' => $region_id));

        if ($regionData[0]['is_international'] == 1) {
            $NationalHeadDetails = $this->user_model->get_user_detail_for_profile(1, 2);
            $model['is_international'] = 1;
        } else {
            $NationalHeadDetails = $this->user_model->get_user_detail_for_profile(10);
            $model['is_international'] = 0;
        }
        //getting National Head details
        $model['national_head'] = $NationalHeadDetails[0];

        $model['ro_details'] = $this->non_fct_ro_model->getNonFctFinalArray($nonfct_result);
        $ro_id = $model['ro_details']['id'];

        $whereData = array('id' => $ro_id);
        $non_fct_expences = $this->non_fct_ro_model->getNonFctRoForRoId($whereData);
        $model['total_expences'] = $non_fct_expences[0]['marketing_promotion_amount'] + $non_fct_expences[0]['field_activation_amount'] +
            $non_fct_expences[0]['sales_commissions_amount'] + $non_fct_expences[0]['creative_services_amount'] +
            $non_fct_expences[0]['other_expenses_amount'];

        $model['logged_in_user'] = $logged_in[0];
        $model['net_contribution'] = $model['ro_details']['gross_ro_amount'] - $model['ro_details']['agency_commision'];
        $model['net_contribution_percentage'] = ($model['net_contribution'] / $model['ro_details']['gross_ro_amount']) * 100;

        $this->load->view('/non_fct_ro/approve_non_fct', $model);
    }

    public function non_fct_approve($order_id)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $internalRoNumber = base64_decode($order_id);
        $nonFctData = $this->non_fct_ro_model->getNonFctRoForRoId(array('internal_ro_number' => $internalRoNumber));
        $nonFctRoId = $nonFctData[0]['id'];

        $this->non_fct_ro_model->updateApprovalRequestNonFctRo(array('approved_by' => 1, 'requested_date' => date('Y-m-d')), array('non_fct_ro_id' => $nonFctRoId));

        $userData = array(
            'mail_status' => 1,
            'mail_sent' => 0
        );
        //update ro mail details
        $this->ro_model->updateMailForRo($userData, array('ro_id' => $nonFctRoId, 'mail_type' => 'non_fct_ro_approval'));
        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/MailSentForRo > /dev/null &");
        redirect('/non_fct_ro/approve_non_fct/' . $order_id);
    }

    public function add_other_expenses_non_fct($external_ro, $internal_ro, $am_ro_id)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $ro_details = $this->non_fct_ro_model->get_non_fct_ro_details($am_ro_id);

        $order_id = $ro_details[0]['internal_ro_number'];
        $model['internal_ro_number'] = $order_id;
        $model['ro_details'] = $ro_details;
        $model['id'] = $am_ro_id;
        // end
        $this->load->view('/non_fct_ro/add_other_expenses_for_non_fct', $model);
    }

    public function check_for_non_fct_ro_existence()
    {
        $cust_ro = $this->input->post('ext_ro');
        $existence = $this->non_fct_ro_model->check_for_non_fct_ro_existence($cust_ro);
        return $existence; //1 or 0
    }

    public function updateNonFctOtherExpense()
    {
        $ro_id = $this->input->post('hid_id');
        $whereData = array('id' => $ro_id);
        $nonFctRoData = $this->non_fct_ro_model->getNonFctRoForRoId($whereData);
        $grossRoAmount = $nonFctRoData[0]['gross_ro_amount'];

        $agencyRebate = $this->input->post('agency_rebate');
        $marketing_promotion_amount = $this->input->post('marketing_promotion_amount');
        $field_activation_amount = $this->input->post('field_activation_amount');
        $sales_commissions_amount = $this->input->post('sales_commissions_amount');
        $creative_services_amount = $this->input->post('creative_services_amount');
        $other_expenses_amount = $this->input->post('other_expenses_amount');

        $agencyCommision = ($grossRoAmount * $agencyRebate) / 100;
        $userData = array(
            'agency_commision' => $agencyCommision,
            'agency_rebate' => $agencyRebate,
            'marketing_promotion_amount' => $marketing_promotion_amount,
            'field_activation_amount' => $field_activation_amount,
            'sales_commissions_amount' => $sales_commissions_amount,
            'creative_services_amount' => $creative_services_amount,
            'other_expenses_amount' => $other_expenses_amount
        );
        $this->non_fct_ro_model->updateNonFctRo($whereData, $userData);

        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';

    }

    /*Submit NON FCT RO page redirections*/
    //BugFix#938:Deepak - Written different Method for not to navigate other pages
    public function add_agency($cust_ro = 0, $ro_date = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $cust_ro = urldecode($cust_ro);
        $cust_ro = str_replace("~", "/", $cust_ro);
        $model['cust_ro'] = $cust_ro;
        $model['ro_date'] = $ro_date;
        $this->load->view('non_fct_ro/add_agency', $model);
    }

    public function post_add_agency()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $cust_ro = $this->input->post('hid_cust_ro');
        $model['result'] = $this->am_model->insert_agency_info_against_ro($cust_ro);
        $model['all_agency'] = $this->am_model->get_all_agency();
        $model['all_client'] = $this->am_model->get_all_adv();
        $model['all_markets'] = $this->am_model->get_all_markets();
        $model['cust_ro'] = $cust_ro;
        $model['ro_date'] = $this->input->post('hid_ro_date');
        $model['user_name'] = $logged_in[0]['user_name'];
        $model['user_id'] = $logged_in[0]['user_id'];
//        $this->load->view('non_fct_ro/create_non_fct_ext_ro', $model);
    }

    public function add_edit_agency_contact($agency = '', $agency_contact = '', $cust_ro = '', $ro_date = '', $client = '', $client_contact = '', $am_edit = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $cust_ro = urldecode($cust_ro);
        $cust_ro = str_replace("~", "/", $cust_ro);
        $model['cust_ro'] = trim($cust_ro);
        $model['agency'] = $agency;
        $model['ro_date'] = $ro_date;
        $model['agency_contact'] = $agency_contact = $_POST['sel_agency_contact'];
        $model['client'] = urldecode($client);
        $model['client_contact'] = $client_contact;
        $model['am_edit'] = $am_edit;
        if ($agency_contact != 'new') {
            // fetch data
            $fetch_agency = $this->am_model->get_agency_contact_info($agency_contact);
            $model['billing_info'] = $fetch_agency[0]['billing_info'];
            $model['billing_address'] = $fetch_agency[0]['billing_address'];
            $model['pay_cylce'] = $fetch_agency[0]['pay_cylce'];
            $model['agency_contact_name'] = $fetch_agency[0]['agency_contact_name'];
            $model['agency_designation'] = $fetch_agency[0]['agency_designation'];
            $model['agency_contact_no'] = $fetch_agency[0]['agency_contact_no'];
            $model['agency_location'] = $fetch_agency[0]['agency_location'];
            $model['agency_address'] = $fetch_agency[0]['agency_address'];
            $model['agency_email'] = $fetch_agency[0]['agency_email'];
        }
        $this->load->view('non_fct_ro/add_edit_agency_contact', $model);
    }

    public function post_add_edit_agency_contact()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $cust_ro = $this->input->post('hid_cust_ro');
        $model['agency'] = urldecode($this->input->post('txt_agency_name'));
        $model['cust_ro'] = trim($this->input->post('hid_cust_ro'));
        $model['ro_date'] = trim($this->input->post('hid_ro_date'));
        $am_edit = $this->input->post('hid_am_edit');
        if ($this->input->post('agency_contact_val') == 'new')
            $operation = 'add';
        else
            $operation = $this->input->post('agency_contact_val');
        $model['result'] = $this->am_model->post_add_agency_contact($operation);
        /*if ($am_edit == 0) {
            $model['all_agency'] = $this->am_model->get_all_agency();
            $model['all_client'] = $this->am_model->get_all_adv();
            $model['all_markets'] = $this->am_model->get_all_markets();
            $model['user_name'] = $logged_in[0]['user_name'];
            $model['user_id'] = $logged_in[0]['user_id'];
            $this->load->view('non_fct_ro/create_non_fct_ext_ro', $model);
        } else {
            $model['ro_details'] = $this->am_model->get_non_fct_ro_details($am_edit);
            $model['client_contact'] = $this->am_model->get_all_client_contact($model['ro_details'][0]['client']);
            $model['agency_contact'] = $this->am_model->get_all_agency_contact($model['ro_details'][0]['agency']);
            //$model['am_edit'] = $am_edit;
            $model['id'] = $am_edit;
            $model['all_am'] = $this->am_model->get_all_am_bh_coo();
            $model['user_name'] = $logged_in[0]['user_name'];
            $model['user_id'] = $logged_in[0]['user_id'];

            $model['financial_year'] = $model['ro_details'][0]['financial_year'];
            $model['amount'] = $this->non_fct_ro_model->get_monthly_amounts_for_ro($model['ro_details'][0]['id']);
            $whereData = array('non_fct_ro_id' => $model['ro_details'][0]['id']);
            $model['approval_status'] = $this->non_fct_ro_model->get_non_fct_ro_approval_status($whereData);
            $model['client_contact'] = $this->am_model->get_all_client_contact($model['ro_details'][0]['client']);
            $model['agency_contact'] = $this->am_model->get_all_agency_contact($model['ro_details'][0]['agency']);
            $model['all_am'] = $this->am_model->get_all_am_bh_coo();

            $this->load->view('non_fct_ro/edit_non_fct_ext_ro', $model);
        }*/
    }

    public function activate_client($cust_ro, $ro_date, $agency, $agency_contact)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $cust_ro = urldecode($cust_ro);
        $cust_ro = str_replace("~", "/", $cust_ro);
        $model['client'] = $this->am_model->get_all_advertiser();
        $model['cust_ro'] = $cust_ro;
        $model['ro_date'] = $ro_date;
        $model['agency'] = $agency;
        $model['agency_contact'] = $agency_contact;
        $this->load->view('non_fct_ro/activate_client', $model);
    }

    public function post_activate_client()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $model['client_result'] = $this->am_model->post_activate_client($this->input->post('sel_client'));
        $model['all_agency'] = $this->am_model->get_all_agency();
        $model['all_client'] = $this->am_model->get_all_adv();
        $model['all_markets'] = $this->am_model->get_all_markets();

        $model['cust_ro'] = $this->input->post('hid_cust_ro');
        $model['ro_date'] = $this->input->post('hid_ro_date');
        $model['agency'] = urldecode($this->input->post('hid_agency'));
        $model['user_name'] = $logged_in[0]['user_name'];
        $model['user_id'] = $logged_in[0]['user_id'];
        $model['agency_contact'] = urldecode($this->input->post('hid_agency_contact'));
        $this->load->view('non_fct_ro/create_non_fct_ext_ro', $model);
    }


    public function add_edit_client_contact($client = '', $client_contact = '', $cust_ro = '', $ro_date = '', $agency = '', $agency_contact = '', $am_edit = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $cust_ro = urldecode($cust_ro);
        $cust_ro = str_replace("~", "/", $cust_ro);
        $model['cust_ro'] = trim($cust_ro);
        $model['ro_date'] = $ro_date;
        $model['client'] = urldecode($client);
        $model['client_contact'] = $client_contact = $_POST['sel_client_contact'];
        $model['agency'] = $agency;
        $model['agency_contact'] = $agency_contact;
        $model['am_edit'] = $am_edit;
        if ($client_contact != 'new') {
            // fetch data
            $fetch_client = $this->am_model->get_client_contact_info($client_contact);
            $model['billing_info'] = $fetch_client[0]['billing_info'];
            $model['billing_address'] = $fetch_client[0]['billing_address'];
            $model['pay_cylce'] = $fetch_client[0]['pay_cylce'];
            $model['client_contact_name'] = $fetch_client[0]['client_contact_name'];
            $model['client_designation'] = $fetch_client[0]['client_designation'];
            $model['client_contact_no'] = $fetch_client[0]['client_contact_number'];
            $model['client_location'] = $fetch_client[0]['client_location'];
            $model['client_address'] = $fetch_client[0]['client_address'];
            $model['client_email'] = $fetch_client[0]['client_email'];
            $model['direct_client'] = $fetch_client[0]['direct_client'];
        }
        $this->load->view('non_fct_ro/add_edit_client_contact', $model);
    }

    public function post_add_edit_client_contact()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $cust_ro = $this->input->post('hid_cust_ro');
        $model['client'] = $this->input->post('txt_client_name');
        $model['cust_ro'] = trim($this->input->post('hid_cust_ro'));
        $model['ro_date'] = $this->input->post('hid_ro_date');
        $model['agency'] = urldecode($this->input->post('hid_agency'));
        $model['agency_contact'] = urldecode($this->input->post('hid_agency_contact'));
        $model['client'] = $this->input->post('txt_client_name');
        $am_edit = $this->input->post('hid_am_edit');

        if ($this->input->post('client_contact_val') == 'new')
            $operation = 'add';
        else
            $operation = $this->input->post('client_contact_val');
        $model['result'] = $this->am_model->post_add_client_contact($operation);

        /*if ($am_edit == 0) {
            $model['all_agency'] = $this->am_model->get_all_agency();
            $model['all_client'] = $this->am_model->get_all_adv();
            $model['all_markets'] = $this->am_model->get_all_markets();
            $model['user_name'] = $logged_in[0]['user_name'];
            $model['user_id'] = $logged_in[0]['user_id'];
            $this->load->view('non_fct_ro/create_non_fct_ext_ro', $model);
        } else {
            $model['ro_details'] = $this->am_model->get_non_fct_ro_details($am_edit);
            $model['client_contact'] = $this->am_model->get_all_client_contact($model['ro_details'][0]['client']);
            $model['agency_contact'] = $this->am_model->get_all_agency_contact($model['ro_details'][0]['agency']);
            //$model['am_edit'] = $am_edit;
            $model['id'] = $am_edit;
            $model['all_am'] = $this->am_model->get_all_am_bh_coo();

            $model['user_name'] = $logged_in[0]['user_name'];
            $model['user_id'] = $logged_in[0]['user_id'];

            $model['financial_year'] = $model['ro_details'][0]['financial_year'];
            $model['amount'] = $this->non_fct_ro_model->get_monthly_amounts_for_ro($model['ro_details'][0]['id']);
            $whereData = array('non_fct_ro_id' => $model['ro_details'][0]['id']);
            $model['approval_status'] = $this->non_fct_ro_model->get_non_fct_ro_approval_status($whereData);
            $model['client_contact'] = $this->am_model->get_all_client_contact($model['ro_details'][0]['client']);
            $model['agency_contact'] = $this->am_model->get_all_agency_contact($model['ro_details'][0]['agency']);
            $model['all_am'] = $this->am_model->get_all_am_bh_coo();

            $this->load->view('non_fct_ro/edit_non_fct_ext_ro', $model);
        }*/
    }
}

/* End of file non_fct_ro.php */
/* Location: ./application/controllers/non_fct_ro.php */
