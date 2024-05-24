<?php if (!defined('BASEPATH')) exit("NO Direct Script Access Allowed");

use application\feature_dal\MenuFeature;
use application\services\feature_services\CreateExtRoService;
use application\services\feature_services\UpdateExtRoService;
use application\feature_dal\CreateExtRoFeature;

include_once APPPATH . 'feature_dal/menu_feature.php';
include_once APPPATH . 'services/feature_services/create_ext_ro_service.php';
include_once APPPATH . 'services/feature_services/update_ext_ro_service.php';
include_once APPPATH . 'feature_dal/create_ext_ro_feature.php';

class Account_manager extends CI_Controller
{
    private $createExtRoServiceObj;
    public $memcache;

    public function __construct()
    {
        parent::__construct();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        $this->load->model('am_model');
        $this->load->model('mg_model');
        $this->load->model('ro_model');
        $this->load->model('user_model');
        $this->load->model('non_fct_ro_model');
        $this->load->library('session');
        $this->load->library('curl');
        $this->load->config('form_validation');
        $this->load->library('form_validation');
        $this->load->helper("app_validation_helper");
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->helper('url');
        $this->load->model("menu_model");
        $this->load->helper('file_helper');

        $this->memcache = new Memcached;
        $this->memcache->addServer("127.0.0.1", 11211);

        $this->createExtRoServiceObj = new CreateExtRoService();

        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }

    }

    //function to check whether user is already logged in or not

    public function approved_ros($start_index = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $value = 0;

        // changed by Lokanath to get the ro details from ro_am_external_ro table as any RO can have multiple brands and markets
        //$list2 = $this->ro_model->get_campaigns_approved($logged_in[0]['profile_id']);
        $list2 = $this->am_model->get_campaigns_approved($logged_in[0]['profile_id'], $logged_in[0]['user_id']);
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
        $search_str = $this->session->userdata("search_str");
        if (isset($search_str) && !empty($search_str)) {
            $ro_lists = filter_using_search_str($ro_lists, array('internal_ro_number', 'brand_new', 'client_name', 'product_new', 'customer_ro_number', 'agency_name', 'campaign_name'), $search_str);
            $model['search_str'] = $search_str;
            $total_rows = count($ro_lists);
        } else {
            $total_rows = count($ro_lists);
        }
        // Pagination to show internal_ro_numbers
        $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);
        $model['value'] = $value;
        $model['ro_list'] = $filtered_ros;
        $model['page_links'] = create_page_links(base_url() . "/ro_manager/approved_ros", ITEM_PER_PAGE_USERS, $total_rows);
        $this->load->view('account_manager/approved_ros', $model);
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
        } else {
            $this->is_menu_hit();
        }


    }

    //function to show all ros created by AM

    public function is_menu_hit()
    {
        $is_hit = $this->ro_model->is_menu_hit();
        if ($is_hit) {
            $this->find_data_for_cache();
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

            $key_channel = 'add_cancel_channel_' . $user_id . "_" . $order_id;;
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

    public function delete_from_cache($key)
    {
        $this->memcache->delete($key);
    }

    public function create_ext_ro($cust_ro = '', $ro_date = '', $agency = '', $agency_contact = 'new', $client = '', $client_contact = 'new')
    {
        $isLoggedIn = $this->isUserloggedIn();
        if (!$isLoggedIn) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Session Expired!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }

        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['all_agency'] = $this->am_model->get_all_agency();
        $model['all_client'] = $this->am_model->get_all_adv();
        $model['all_markets'] = $this->am_model->get_all_markets();
        $model['all_clusters'] = $this->am_model->get_all_markets(1);
        $cust_ro = urldecode($cust_ro);
        $cust_ro = str_replace("~", "/", $cust_ro);
        $model['cust_ro'] = $cust_ro;
        $model['ro_date'] = $ro_date;
        $model['agency'] = base64_decode($agency);
        $model['agency_contact'] = $agency_contact;
        $model['client'] = urldecode($client);
        $model['client_contact'] = $client_contact;
        $model['user_name'] = $logged_in[0]['user_name'];
        $model['user_id'] = $logged_in[0]['user_id'];

        //Getting login user regions
        $model['regionArray'] = $this->am_model->getLoginUserRegions($logged_in[0]['user_id']);

        $response['Status'] = 'success';
        $response['isLoggedIn'] = $isLoggedIn;
        $response['Message'] = 'Create External RO view';
        $response['Data']['html'] = $this->load->view('account_manager/create_ext_ro', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function create_non_fct_ext_ro($cust_ro = '', $ro_date = '', $agency = '', $agency_contact = 'new', $client = '', $client_contact = 'new')
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['all_agency'] = $this->am_model->get_all_agency();
        $model['all_client'] = $this->am_model->get_all_adv();
        $cust_ro = urldecode($cust_ro);
        $cust_ro = str_replace("~", "/", $cust_ro);
        $model['cust_ro'] = $cust_ro;
        $model['agency'] = urldecode($agency);
        $model['agency_contact'] = $agency_contact;
        $model['client'] = urldecode($client);
        $model['client_contact'] = $client_contact;
        $model['user_name'] = $logged_in[0]['user_name'];
        $model['user_id'] = $logged_in[0]['user_id'];

        $model['regionArray'] = $this->am_model->getLoginUserRegions($logged_in[0]['user_id']);

        $this->load->view('non_fct_ro/create_non_fct_ext_ro', $model);
    }

    public function add_agency($cust_ro, $ro_date)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $cust_ro = urldecode($cust_ro);
        $cust_ro = str_replace("~", "/", $cust_ro);
        $model['cust_ro'] = $cust_ro;
        $model['ro_date'] = $ro_date;
        $this->load->view('account_manager/add_agency', $model);
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
        $this->load->view('account_manager/create_ext_ro', $model);
    }

    public function get_agency_info()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $agency = $this->input->post('agency');
        $result = $this->am_model->get_agency_info($agency);
        echo $result;
    }

    public function add_edit_agency_contact($agency_display = '', $agency_contact = '', $cust_ro = '', $ro_date = '', $client = '', $client_contact = '', $am_edit = 0)
    {
        $isLoggedIn = $this->isUserloggedIn();
        $response = array();
        if (!$isLoggedIn) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Session Expired!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }

        $model = $this->createExtRoServiceObj->addEditAgencyContact();

        $response['Status'] = 'success';
        $response['isLoggedIn'] = $isLoggedIn;
        $response['Message'] = 'Add/Edit Agency Contact View';
        $response['Data']['html'] = $this->load->view('account_manager/add_edit_agency_contact', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function post_add_edit_agency_contact()
    {
        $isLoggedIn = $this->isUserloggedIn();
        $response = array();
        if (!$isLoggedIn) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Session Expired!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }

        log_message('INFO', 'In account_manager@post_add_edit_agency_contact | Agency Data from UI - ' . print_r($_POST, true));

        //Validating Fields
        $errorInValidation = set_form_validation($this->config->item('addEditAgencyContact'));
        log_message('DEBUG', 'In account_manager@post_add_edit_agency_contact | Form Validated = ' . print_r(!$errorInValidation, true));

        $validationKeys = $this->config->item('addEditAgencyContact');
        $errorArray['Status'] = 'fail';
        $errorArray['isLoggedIn'] = $isLoggedIn;
        $errorArray['Message'] = 'Data Validation Failed!';
        $errorArray['Data']['formValidation'] = array();
        foreach ($validationKeys as $key) {
            log_message('DEBUG', 'In account_manager@post_add_edit_agency_contact | Validating ' . print_r($key['key'] . ' ' . form_error($key['key']), true));
            $validationErrorMessage = form_error($key['key']);
            if ($validationErrorMessage != '') {
                array_push($errorArray['Data']['formValidation'], array($key['key'] => form_error($key['key'])));
            }
        }
        log_message('INFO', 'In account_manager@post_add_edit_agency_contact | Error Array - ' . print_r(json_encode($errorArray), true));
        if (!empty($errorArray['Data']['formValidation'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($errorArray));
            return;
        }
        //Validation Finished

        if ($this->input->post('hid_agency_contact') == 'new')
            $operation = 'add';
        else
            $operation = $this->input->post('hid_agency_contact');

        $response = $this->createExtRoServiceObj->postAddEditAgencyContact($operation);
        $response['isLoggedIn'] = $isLoggedIn;
        log_message('INFO', 'In account_manager@post_add_edit_agency_contact | Final Response = ' . print_r(json_encode($response), True));
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function add_edit_client_contact($client_display_name = '', $client_contact = '', $cust_ro = '', $ro_date = '', $agency = '', $agency_contact = '', $am_edit = 0)
    {
        $isLoggedIn = $this->isUserloggedIn();
        $response = array();
        if (!$isLoggedIn) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Session Expired!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }
        $model = $this->createExtRoServiceObj->addEditClientContact();
        log_message('INFO', 'In account_manager@add_edit_client_contact | ' . print_r($model, true));

        $response['Status'] = 'success';
        $response['isLoggedIn'] = $isLoggedIn;
        $response['Message'] = 'Add/Edit Client Contact View!';
        $response['Data']['html'] = $this->load->view('account_manager/add_edit_client_contact', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function post_add_edit_client_contact()
    {
        $isLoggedIn = $this->isUserloggedIn();
        $response = array();
        if (!$isLoggedIn) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Session Expired!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }

        log_message('INFO', 'In account_manager@post_add_edit_client_contact | Agency Data from UI - ' . print_r($_POST, true));

        //Validating Fields
        $errorInValidation = set_form_validation($this->config->item('addEditClientContact'));
        log_message('DEBUG', 'In account_manager@post_add_edit_client_contact | Form Validated = ' . print_r(!$errorInValidation, true));

        $validationKeys = $this->config->item('addEditClientContact');
        $errorArray['Status'] = 'fail';
        $errorArray['isLoggedIn'] = $isLoggedIn;
        $errorArray['Message'] = 'Data Validation Failed!';
        $errorArray['Data']['formValidation'] = array();
        foreach ($validationKeys as $key) {
            log_message('DEBUG', 'In account_manager@post_add_edit_client_contact | Validating ' . print_r($key['key'] . ' ' . form_error($key['key']), true));
            $validationErrorMessage = form_error($key['key']);
            if ($validationErrorMessage != '') {
                array_push($errorArray['Data']['formValidation'], array($key['key'] => form_error($key['key'])));
            }
        }
        log_message('INFO', 'In account_manager@post_add_edit_client_contact | Error Array - ' . print_r(json_encode($errorArray), true));
        if (!empty($errorArray['Data']['formValidation'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($errorArray));
            return;
        }
        //Validation Finished

        if ($this->input->post('hid_client_contact') == 'new')
            $operation = 'add';
        else
            $operation = $this->input->post('hid_client_contact');
        $response = $this->createExtRoServiceObj->postAddEditClientContact($operation);
        $response['isLoggedIn'] = $isLoggedIn;
        log_message('INFO', 'In account_manager@post_add_edit_client_contact | Final Response = ' . print_r(json_encode($response), True));
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function get_client_info()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $client_display_name = $this->input->post('client');
        $result = $this->am_model->get_client_info($client_display_name);
        echo $result;
    }

    public function add_client($cust_ro)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['cust_ro'] = $cust_ro;
        $this->load->view('account_manager/add_client', $model);
    }

    public function post_add_client()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $cust_ro = $this->input->post('hid_cust_ro');
        $model['client_result'] = $this->am_model->insert_client_info_against_ro($cust_ro);
        $model['all_agency'] = $this->am_model->get_all_agency();
        $model['all_client'] = $this->am_model->get_all_adv();
        $model['all_markets'] = $this->am_model->get_all_markets();
        $model['cust_ro'] = $cust_ro;
        $this->load->view('account_manager/create_ext_ro', $model);
    }

    public function add_brand($cust_ro, $brand_id)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['cust_ro'] = $cust_ro;
        $model['brand_id'] = $brand_id;
        $this->load->view('account_manager/add_brand', $model);
    }

    public function post_add_brand()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $brand_id = $this->input->post('hid_brand_id');
        $cust_ro = $this->input->post('hid_cust_ro');
        $model['brand_result'] = $this->am_model->insert_brand_for_client($brand_id);
        $model['all_agency'] = $this->am_model->get_all_agency();
        $model['all_client'] = $this->am_model->get_all_adv();
        $model['all_markets'] = $this->am_model->get_all_markets();
        $model['cust_ro'] = $cust_ro;
        $this->load->view('account_manager/create_ext_ro', $model);
    }

    public function get_brand_ajax()
    {
        $adv = $this->input->post('adv');
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['adv_brand'] = $this->am_model->get_adv_brand($adv);
        echo json_encode($model['adv_brand']);
    }

    public function post_create_ext_ro()
    {
        $isLoggedIn = $this->isUserloggedIn();
        if (!$isLoggedIn) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Session Expired!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }
        log_message('DEBUG', 'In account_manager@post_create_ext_ro | Body Data' . print_r(json_decode(file_get_contents('php://input'), true), true));
        log_message('DEBUG', 'In account_manager@post_create_ext_ro | POST data from UI' . print_r($_POST, true));
        log_message('DEBUG', 'In account_manager@post_create_ext_ro | FILE data from UI' . print_r($_FILES, true));

        $errorInValidation = set_form_validation($this->config->item('create_ext_ro_form'));
        log_message('DEBUG', 'In account_manager@post_create_ext_ro | Form Validated = ' . print_r(!$errorInValidation, true));

        $validationKeys = $this->config->item('create_ext_ro_form');
        $errorArray['Status'] = 'fail';
        $errorArray['isLoggedIn'] = $isLoggedIn;
        $errorArray['Message'] = 'Data Validation Failed!';
        $errorArray['Data']['formValidation'] = array();
        foreach ($validationKeys as $key) {
            log_message('DEBUG', 'In account_manager@post_create_ext_ro | Validating ' . print_r($key['key'] . ' ' . form_error($key['key']), true));
            $validationErrorMessage = form_error($key['key']);
            if ($validationErrorMessage != '') {
                array_push($errorArray['Data']['formValidation'], array($key['key'] => form_error($key['key'])));
            }
        }
        log_message('INFO', 'In account_manager@post_create_ext_ro | Error Array - ' . print_r(json_encode($errorArray), true));
        if (!empty($errorArray['Data']['formValidation'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($errorArray));
            return;
        }

        $response = $this->createExtRoServiceObj->createExtRo();
        $response['isLoggedIn'] = $isLoggedIn;
        log_message('INFO', 'In account_manager@post_create_ext_ro | Final Response - ' . print_r(json_encode($response), true));
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

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
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        //$model['user_info']         = $this->accountManagersInfo();
        $profile_id = $logged_in[0]['profile_id'];
        /* if($profile_id == 6 || $profile_id == 11 || $profile_id == 12) {
                            $model['user_info']         = $this->accountManagerTarget();
                        } */
        $list1 = $this->am_model->get_campaigns($logged_in[0]['profile_id'], $logged_in[0]['user_id'], $logged_in[0]['is_test_user']);
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
        /*$internal_ros = array();
			foreach($ro_lists as $ros) {
				array_push($internal_ros, $ros['internal_ro_number']);
			}*/


        //$ro_lists					= $this->am_model->get_ro_for_am($logged_in[0]['user_id']);
        $total_rows = 0;
        $search_str = $this->session->userdata("search_str");//echo $search_str;
        if (isset($search_str) && !empty($search_str) && $serch_set == 1) {
            //$search_str = base64_decode($search_str);
            $ro_lists = filter_using_search_str($ro_lists, array('client', 'cust_ro', 'agency', 'internal_ro', 'submitted_by', 'approved_by'), $search_str);
            $model['search_str'] = $search_str;
            $total_rows = count($ro_lists);
            // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);//print_r($ro_lists);exit;
            $model['ro_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
            $model['page_links'] = create_page_links(base_url() . "/account_manager/home", ITEM_PER_PAGE_USERS, $total_rows, $serch_set);
            //$model['page_links'] = $model['page_links'].'/1';
            //$this->load->view('account_manager/home/'.$start_index.'/1',$model);
        } else {
            $total_rows = count($ro_lists);
            // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);//print_r($ro_lists);exit;
            $model['ro_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
            $model['page_links'] = create_page_links(base_url() . "/account_manager/home", ITEM_PER_PAGE_USERS, $total_rows);
            //$this->load->view('account_manager/home',$model);
        }
        // Pagination to show internal_ro_numbers
        /*$filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);//print_r($ro_lists);exit;
			$model['ro_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
			$model['page_links'] = create_page_links (base_url()."/account_manager/home", ITEM_PER_PAGE_USERS, $total_rows);
			*/
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;

        $model['userRegion'] = $this->session->userdata('logged_in_user_none_region');
        $this->load->view('account_manager/home', $model);
    }

    public function show_details($id, $edit)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];

        $ro_details = $this->am_model->get_ro_details($id, $edit);
        $model['edit'] = $edit;
        $model['ro_list'] = $ro_details;
        $model['is_cancelled'] = $this->am_model->is_cancelled($id);
        $this->load->view('account_manager/show_details', $model);
    }

    public function edit_ext_ro($id)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['all_agency'] = $this->am_model->get_all_agency();
        $model['all_client'] = $this->am_model->get_all_adv();
        $model['all_markets'] = $this->am_model->get_all_markets();
        $model['all_am'] = $this->am_model->get_all_am();
        $ro_details = $this->am_model->get_ro_details($id);

        $campaign_created = 0;
        $verify_campaign_created = $this->am_model->verify_campaign_created_for_internal_ro($ro_details[0]['internal_ro']);
        if (count($verify_campaign_created) > 0) {
            $campaign_created = 1;
        }

        $model['am_ro_id'] = $id;
        $model['ro_details'] = $ro_details;
        $model['campaign_created'] = $campaign_created;
        $model['client_contact'] = $this->am_model->get_all_client_contact($ro_details[0]['client']);
        $this->load->view('account_manager/edit_ext_ro', $model);
    }

    public function post_edit_ext_ro()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model['res_edit_ext_ro'] = $this->am_model->edit_external_ro();
        // close colorbox and refresh parent for showing created ro
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    public function am_edit_ext_ro()
    {
        log_message('INFO', 'In account_manager@am_edit | ' . print_r($_POST, true));

        //echo $id;exit;
        $id = $this->input->post("id");
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];//print_r($logged_in[0]);exit;
        $model['ro_details'] = $this->am_model->get_ro_details($id);
        $model['file_path'] = $this->am_model->get_ro_file_path($id);
        $model['id'] = $id;
        $model['client_contact'] = $this->am_model->get_all_client_contact($model['ro_details'][0]['client']);
        $model['agency_contact'] = $this->am_model->get_all_agency_contact($model['ro_details'][0]['agency']);
        $model['all_am'] = $this->am_model->get_all_am_bh_coo();
        $model['all_markets'] = $this->am_model->get_all_markets();
        $model['all_clusters'] = $this->am_model->get_all_markets(1);
        $model['markets_against_ro'] = $this->am_model->get_markets_against_ro($id);
        $campaign_created = 0;
        $verify_campaign_created = $this->am_model->verify_campaign_created_for_internal_ro($model['ro_details'][0]['internal_ro']);
        if (count($verify_campaign_created) > 0) {
            $campaign_created = 1;
        }
        $model['campaign_created'] = $campaign_created;
        // check ro status
        $ro_approved = 0;
        $verify_ro_status = $this->am_model->verify_ro_status($model['ro_details'][0]['id']);
        if (count($verify_ro_status) > 0) {
            $ro_approved = 1;
            $ro_assigned_user_name = $this->am_model->ro_assigned_user_name($model['ro_details'][0]['user_id']);
        }
        // end
        $model['ro_status'] = $ro_approved;
        $model['ro_assigned_user_name'] = $ro_assigned_user_name;

        /* Storing data into cash to check while updating */

        foreach ($model['markets_against_ro'] as $marketData) {

            $marketFilterArray[] = array(str_replace(" ", "_", $marketData['market']),
                $marketData['spot_price'],
                $marketData['spot_fct'],
                $marketData['banner_price'],
                $marketData['banner_fct']
            );

        }
        $currentDetails = array(
            $model['ro_details'][0]['cust_ro'],
            $model['ro_details'][0]['ro_date'],
            $model['ro_details'][0]['camp_start_date'],
            $model['ro_details'][0]['camp_end_date'],
            $marketFilterArray
        );

        $userId = $logged_in[0]['user_id'];

        /*$model["regionArray"] = $this->am_model->getLoginUserRegions( $model['ro_details'][0]['user_id'] ) ;*/

        /*$this->memcache->set("CURRENT_INSERTED_DATA_EDIT_EXTERNAL_RO_$userId", json_encode($currentDetails), 7200);*/
        /* Storing data into cash to check while updating */

        // $this->load->view('account_manager/am_edit_ext_ro', $model);

        $createExtRoFeatureObj = new createExtRoFeature();
        $model['disableUpdate'] = $createExtRoFeatureObj->checkIfRoForwaded($id);

        $response['Status'] = 'success';
        $response['Message'] = 'Edit external RO successfull!';
        $response['Data']['html'] = $this->load->view('account_manager/am_edit_ext_ro', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function post_am_edit_ext_ro()
    {
        $isLoggedIn = $this->isUserloggedIn();
        if (!$isLoggedIn) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Session Expired!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }

        $updateExtRoObject = new UpdateExtRoService();
        $response = $updateExtRoObject->UpdateExtRo();
        $response['isLoggedIn'] = $isLoggedIn;
        log_message('INFO', 'In account_manager@post_create_ext_ro | Final Response - ' . print_r(json_encode($response), true));
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

//    public function post_am_edit_ext_ro()
//    {
//        $this->is_logged_in();
//        $logged_in = $this->session->userdata("logged_in_user");
//
//        $model = array();
//        $model['res_am_edit_ext_ro'] = $this->am_model->am_edit_external_ro();
//
//        /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@Gettting data into cash to check while updating */
//
//        $userId = $logged_in[0]['user_id'];
//
//        $lastInsertedData = $this->memcache->get("CURRENT_INSERTED_DATA_EDIT_EXTERNAL_RO_$userId");
//
//        $market = $_POST['markets'];
//        $fct = $_POST['FCT'];
//
//        array_push($market, $fct);
//
//        foreach ($market as $marketId => $market_ro_amount) {
//
//            if ($market_ro_amount['spot'] == 0 && $market_ro_amount['banner'] == 0) {
//                continue;
//            }
//
//            $marketData[] = array(
//
//                $marketId,
//                $market_ro_amount['spot'],
//                $market[0][$marketId]['spot_FCT'],
//                $market_ro_amount['banner'],
//                $market[0][$marketId]['banner_FCT']
//            );
//        }
//
//        $cust_ro = trim($this->input->post('txt_ext_ro'));
//        $ro_date = $this->input->post('txt_ro_date');
//        $camp_start_date = $this->input->post('txt_camp_start_date');
//        $camp_end_date = $this->input->post('txt_camp_end_date');
//
//        $currentDataArray = array($cust_ro,
//            $ro_date,
//            $camp_start_date,
//            $camp_end_date,
//            $marketData
//        );
//
//        /*if( md5( json_encode($currentDataArray)) != md5( $lastInsertedData) ){
//
//                //Insert into email_progression table to sent mail.
//                $roId = $this->input->post('hid_id');
//                $setData = " SET edit_ro_action = 'send_mail' " ;
//
//                $this->am_model->updateRoProgressionDetail( $roId, $setData ) ;
//
//            }*/
//
//        /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@Storing data into cash to check while updating */
//        $email_data = $this->user_model->userEmailForRoCreation($logged_in[0]['user_id']);
//        $ro_ext_id = $this->input->post('hid_id');
//        $whereData = array('ro_id' => $ro_ext_id, 'mail_type' => 'submit_ro_approval');
//        $updateData = array('cc_email_id' => $email_data, 'mail_sent' => 0);
//
//        $this->ro_model->updateMailForRo($whereData, $updateData);
//        $reporting_manager_details = $this->user_model->getUserDetailOfUserReportingManager($logged_in[0]['user_id']);
//
//        if ($reporting_manager_details[0]['profile_id'] == 10) {
//            $approvalLevel = 2;
//        } else {
//            $approvalLevel = $this->user_model->getApprovalLevel($logged_in[0]['profile_id']);
//        }
//
//        $this->am_model->update_cancelled_data(array('cancel_ro_by_admin' => 0, 'approval_level' => $approvalLevel), array('ext_ro_id' => $ro_ext_id, 'cancel_type' => 'submit_ro_approval'));
//
//        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/MailSentForRo > /dev/null &");
//        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/calculateNetContribuitionAndMarketRate/$ro_ext_id > /dev/null &");
//        if ($model['res_am_edit_ext_ro'] == 'exists') {
//            //echo '<span style="color:#F00;">The customer ro is already exists.</span>';
//            $id = $this->input->post('hid_id');
//            $model['logged_in_user'] = $logged_in[0];//print_r($logged_in[0]);exit;
//            $model['ro_details'] = $this->am_model->get_ro_details($id);
//            $model['id'] = $id;
//            $model['client_contact'] = $this->am_model->get_all_client_contact($model['ro_details'][0]['client']);
//            $model['agency_contact'] = $this->am_model->get_all_agency_contact($model['ro_details'][0]['agency']);
//            $model['all_am'] = $this->am_model->get_all_am_bh_coo();
//            $campaign_created = 0;
//            $verify_campaign_created = $this->am_model->verify_campaign_created_for_internal_ro($model['ro_details'][0]['internal_ro']);
//            if (count($verify_campaign_created) > 0) {
//                $campaign_created = 1;
//            }
//            $model['campaign_created'] = $campaign_created;
//            // check ro status
//            $ro_approved = 0;
//            $verify_ro_status = $this->am_model->verify_ro_status($model['ro_details'][0]['id']);
//            if (count($verify_ro_status) > 0) {
//                $ro_approved = 1;
//                $ro_assigned_user_name = $this->am_model->ro_assigned_user_name($model['ro_details'][0]['user_id']);
//            }
//            // end
//            $model['ro_status'] = $ro_approved;
//            $model['ro_assigned_user_name'] = $ro_assigned_user_name;
//            $this->load->view('account_manager/am_edit_ext_ro', $model);
//        } else {
//
//            echo '<script>window.location.href="/surewaves_easy_ro/account_manager/home";</script>';
//        }
//    }

    public function am_edit_non_fct_ext_ro($id)
    {//echo $id;exit;
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $ro_details = $this->am_model->get_non_fct_ro_details($id);
        $region_name = $this->am_model->get_region_name($ro_details[0]['region_id']);

        $model = array();
        $model['logged_in_user'] = $logged_in[0];//print_r($logged_in[0]);exit;
        $model['ro_details'] = $ro_details;
        $model['financial_year'] = $ro_details[0]['financial_year'];
        $model['amount'] = $this->non_fct_ro_model->get_monthly_amounts_for_ro($ro_details[0]['id']);
        $whereData = array('non_fct_ro_id' => $ro_details[0]['id']);
        $model['approval_status'] = $this->non_fct_ro_model->get_non_fct_ro_approval_status($whereData);
        $model['id'] = $id;
        $model['client_contact'] = $this->am_model->get_all_client_contact($model['ro_details'][0]['client']);
        $model['agency_contact'] = $this->am_model->get_all_agency_contact($model['ro_details'][0]['agency']);
        $model['all_am'] = $this->am_model->get_all_am_bh_coo();
        $model['regionArray'] = $this->am_model->getLoginUserRegions($model['ro_details'][0]['user_id']);
        $model['region'] = $region_name[0]['region_name'];

        $this->load->view('non_fct_ro/edit_non_fct_ext_ro', $model);
    }

    public function cancel_ro()
    {
        log_message('INFO', 'In account_manager@cancel_ro | ' . print_r($_POST, true));
        $this->is_logged_in();
        $id = $this->input->post('id');
        $external_ro = $this->input->post('cust_ro');
        $edit = $this->input->post('edit');
        $internal_ro = $this->input->post('internal_ro');
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['id'] = $id;
        $model['external_ro'] = $external_ro;
        $model['edit'] = $edit;
        $model['internal_ro'] = $internal_ro;
        $get_ro_dates = $this->am_model->get_ro_dates($id);
        $model['ro_start_date'] = $get_ro_dates[0]['camp_start_date'];
        $model['ro_end_date'] = $get_ro_dates[0]['camp_end_date'];
//        $this->load->view('account_manager/cancel_ro', $model);

        $response['Status'] = 'success';
        $response['Message'] = 'Cancel RO colorbox!!';
        $response['Data']['html'] = $this->load->view('account_manager/cancel_ro', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    // function used to change RO campaign start and end date

    public function post_cancelt_ro()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $ro_id = $this->input->post('hid_id');
        $date_of_cancel = $this->input->post('txt_cancel_date');
        $invoice_amount = $this->input->post('txt_inv_amnt');

        $cancel_id = $this->am_model->cancel_ro();

        //calculating new values after cancellation

        $markets_for_ro = $this->am_model->get_market_for_ro($ro_id);
        $ro_details = $this->am_model->ro_detail_for_ro_id($ro_id);

        $this->db->trans_start();
        foreach ($markets_for_ro as $market) {
            if ($market['is_cancel'] == 0 || $market['is_cancel'] == 1) {
                $fctData = $this->getFctData($ro_details[0]['internal_ro'], $market['market'], $ro_details[0]['camp_start_date'], $market['spot_fct'], $market['banner_fct'], $date_of_cancel);

                $marketSpotFct = $market['spot_fct'];
                if ($marketSpotFct > $fctData['totalSpotFct']) {
                    $marketSpotFct = $fctData['totalSpotFct'];
                }
                $marketBannerFct = $market['banner_fct'];
                if ($marketBannerFct > $fctData['totalBannerFct']) {
                    $marketBannerFct = $fctData['totalBannerFct'];
                }

                $spotFraction = 0.0;
                if (isset($marketSpotFct) && !empty($marketSpotFct) && $marketSpotFct != 0) {
                    $spotFraction = $fctData['scheduledSpotFct'] / $marketSpotFct;
                }
                $bannerFraction = 0.0;
                if (isset($marketBannerFct) && !empty($marketBannerFct) && $marketBannerFct != 0) {
                    $bannerFraction = $fctData['scheduledBannerFct'] / $marketBannerFct;
                }

                $spot_price_after_cancel = round($market['spot_price'] * $spotFraction, 2);
                $banner_price_after_cancel = round($market['banner_price'] * $bannerFraction, 2);

                $spot_fct_after_cancel = $fctData['scheduledSpotFct'];
                $banner_fct_after_cancel = $fctData['scheduledBannerFct'];

                $insert_user_data = array(
                    'cancel_id' => $cancel_id,
                    'am_ro_id' => $ro_id,
                    'market' => $market['market'],
                    'spot_price' => $spot_price_after_cancel,
                    'spot_fct' => $spot_fct_after_cancel,
                    'banner_price' => $banner_price_after_cancel,
                    'banner_fct' => $banner_fct_after_cancel,
                    'approved_type' => 0,
                    'is_cancelled' => 1
                );

                $update_user_data = array(
                    'spot_price' => $spot_price_after_cancel,
                    'spot_fct' => $spot_fct_after_cancel,
                    'banner_price' => $banner_price_after_cancel,
                    'banner_fct' => $banner_fct_after_cancel,
                    'approved_type' => 0,
                    'is_cancelled' => 1
                );
                $where_data = array(
                    'cancel_id' => $cancel_id,
                    'am_ro_id' => $ro_id,
                    'market' => $market['market']
                );

                $request_count = $this->am_model->get_value_tmp_market($where_data);
                if (count($request_count) > 0) {
                    $this->am_model->update_tmp_market($update_user_data, $where_data);
                } else {
                    $this->am_model->insert_into_tmp_table($insert_user_data);
                }

            }
        }


        //update status
        $am_external_ro_id = $this->input->post('hid_id');
        $this->am_model->update_ro_status($am_external_ro_id, 'cancel_requested');

        $external_ro = $this->input->post('hid_ext_ro');

        // send mail to coo/bh/scheduling_user
        $users = $this->am_model->cancel_ro_mailto_list();
        $email_data = array();
        foreach ($users as $val) {
            array_push($email_data, $val['user_email']);
        }
        $to_email = implode(",", $email_data);

        $ro_details = $this->am_model->ro_detail_for_external_ro($external_ro);
        $scheduler_data = $this->ro_model->get_scheduler_details();
        $scheduler_id_email = array();
        foreach ($scheduler_data as $data) {
            array_push($scheduler_id_email, $data['user_email']);
        }
        $scheduler_id_email = implode(",", $scheduler_id_email);
        $cc = $scheduler_id_email . "," . $logged_in[0]['user_email'];
        //added by Nitish to get actual campaign date for RO (2.9.8)
        $campaign_end_date = $this->am_model->get_actual_campaign_end_date_for_ro($ro_details[0]['internal_ro']);
        $userData = array(
            'ro_id' => $ro_id,
            'mail_type' => 'cancel_ro_requested',
            'mail_status' => 0,
            'approval_level' => 1,
            'user_email_id' => $to_email,
            'cc_email_id' => $cc,
            'file_name' => '',
            'mail_sent_date' => date('Y-m-d'),
            'mail_sent' => 0
        );
        $this->ro_model->insertForMail($userData);
        $this->db->trans_complete();
        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/MailSentForRo > /dev/null &");

//        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
        $response['Status'] = 'success';
        $response['Message'] = 'Cancel Request Submitted Successfully!!';
        $response['Data'] = array();
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function getFctData($internalRo, $marketName, $startDate, $spotFct, $bannerFct, $cancelDate = null)
    {
        if (!isset($cancelDate) || empty($cancelDate)) {
            $cancelDate = date('Y-m-d', strtotime("+2 days"));
        }

        /*if(strtotime($startDate) > strtotime($cancelDate)) {
                $cancelDate = $startDate ;
            }*/

        $marketsDetail = $this->mg_model->getMarketIdForMarketName(array('sw_market_name' => $marketName));
        $marketId = $marketsDetail[0]['id'];

        $scheduledFct = $this->mg_model->get_scheduled_impression_for_time_period_Market($internalRo, $marketId, $startDate, $cancelDate, $spotFct, $bannerFct);
        log_message('info', 'in account_manager@getFctData | $scheduledFct ' . print_r($scheduledFct, true));
        //$totalFct = $this->mg_model->get_total_scheduled_seconds_For_Market($internalRo,$marketId,$spotFct,$bannerFct) ;

        $data = array();
        if (count($scheduledFct) > 0) {
            $spotKey = array_keys($scheduledFct['spotFct']);
            $bannerKey = array_keys($scheduledFct['bannerFct']);

            $spotChannelId = $spotKey[0];
            $bannerChannelId = $bannerKey[0];
            $data['scheduledSpotFct'] = $scheduledFct['spotFct'][$spotChannelId];
            $data['totalSpotFct'] = $scheduledFct['totalSpotFct'][$spotChannelId];
            $data['scheduledBannerFct'] = $scheduledFct['bannerFct'][$bannerChannelId];
            $data['totalBannerFct'] = $scheduledFct['totalBannerFct'][$bannerChannelId];
        }
        log_message('info', 'in account_manager@getFctData | fctReturn data ' . print_r($data, true));

        return $data;
    }

    public function cancel_ro_admin($id, $external_ro, $edit, $internal_ro)
    {
        $this->is_logged_in();
        $internal_ro = base64_decode($internal_ro);
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['id'] = $id;

        $cancel_ro_details = $this->am_model->is_cancel_request_sent_cancel_ro($id);
        $get_ro_dates = $this->am_model->get_ro_dates($id);
        $model['ro_start_date'] = $get_ro_dates[0]['camp_start_date'];
        $model['ro_end_date'] = $get_ro_dates[0]['camp_end_date'];
        if (count($cancel_ro_details) > 0) {
            $model['cancel_start_date'] = $cancel_ro_details[0]['date_of_cancel'];
            $model['reason'] = $cancel_ro_details[0]['reason'];
            $model['invoice_instruction'] = $cancel_ro_details[0]['invoice_instruction'];
            $model['ro_amount'] = $cancel_ro_details[0]['ro_amount'];
        } else {
            $ro_detail = $this->am_model->ro_detail_for_external_ro(base64_decode($external_ro));
            $model['cancel_start_date'] = date('Y-m-d', strtotime($ro_detail[0]['ro_date']));
        }

        $model['external_ro'] = base64_decode($external_ro);
        $this->load->view('account_manager/cancel_ro_admin', $model);
    }

    public function post_cancel_ro_admin()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $ro_id = $this->input->post('hid_id');
        $external_ro = $this->input->post('hid_ext_ro');
        $user_id = $this->input->post('hid_user_id');
        $date_of_cancel = $this->input->post('txt_cancel_date');
        $reason = $this->input->post('txt_reason');
        $invoice_inst = $this->input->post('txt_inv_inst');
        $invoice_amount = $this->input->post('txt_inv_amnt');

        $parameter = $external_ro . "##" . $date_of_cancel . "##" . $ro_id;
        $longParams = $reason . "##" . $invoice_inst;
        $this->db->trans_start();
        $user_data = array('job_code' => 'cancelRo',
            'done' => 0,
            'params' => $parameter,
            'customer_id' => $user_id,
            'longParams' => $longParams
        );
        $this->am_model->insert_into_job_queue($user_data);

        $user_data_for_entry = array('ext_ro_id' => $ro_id,
            'user_id' => $user_id,
            'date_of_submission' => date('Y-m-d'),
            'date_of_cancel' => $date_of_cancel,
            'reason' => $reason,
            'invoice_instruction' => $invoice_inst,
            'cancel_ro_by_admin' => 1,
            'ro_amount' => $invoice_amount
        );
        $this->am_model->update_cancel_for_admin($ro_id, $user_data_for_entry);
        $this->am_model->update_ro_status($ro_id, 'cancel_approved');

        $ro_details = $this->am_model->ro_detail_for_ro_id($ro_id);
        $ro_amount = $ro_details[0]['gross'];
        $agency_comm_from_ext_ro = $ro_details[0]['agency_com'];
        $internal_ro_number = $ro_details[0]['internal_ro'];

        $ro_amount_val = $this->am_model->get_ro_amount_data($internal_ro_number);
        $agency_commission_amount = $ro_amount_val[0]['agency_commission_amount'];
        $agency_rebate = $ro_amount_val[0]['agency_rebate'];
        $marketing_promotion_amount = $ro_amount_val[0]['marketing_promotion_amount'];
        $field_activation_amount = $ro_amount_val[0]['field_activation_amount'];
        $sales_commissions_amount = $ro_amount_val[0]['sales_commissions_amount'];
        $creative_services_amount = $ro_amount_val[0]['creative_services_amount'];
        $other_expenses_amount = $ro_amount_val[0]['other_expenses_amount'];


        //proportionality calculation
        if ($ro_amount >= $invoice_amount) {
            $percentage = ($ro_amount - $invoice_amount) / $ro_amount;
            $agency_comm_from_ext_ro = $agency_comm_from_ext_ro - ($agency_comm_from_ext_ro * $percentage);
            $agency_commission_amount = $agency_commission_amount - ($agency_commission_amount * $percentage);
            //$agency_rebate = $agency_rebate - ($agency_rebate*$percentage) ;
            $marketing_promotion_amount = $marketing_promotion_amount - ($marketing_promotion_amount * $percentage);
            $field_activation_amount = $field_activation_amount - ($field_activation_amount * $percentage);
            $sales_commissions_amount = $sales_commissions_amount - ($sales_commissions_amount * $percentage);
            $creative_services_amount = $creative_services_amount - ($creative_services_amount * $percentage);
            $other_expenses_amount = $other_expenses_amount - ($other_expenses_amount * $percentage);
        }


        // code added by Lokanath: calculation of net_agency_com
        $net_agency_com = $invoice_amount - $agency_comm_from_ext_ro;
        // end

        $ro_amount_data = array('gross' => $invoice_amount, 'agency_com' => $agency_commission_amount, 'net_agency_com' => $net_agency_com, 'previous_ro_amount' => $ro_amount);
        $this->am_model->update_ro_data_in_ro_ext($ro_id, $ro_amount_data);

        $ro_amnt_data = array(
            'ro_amount' => $invoice_amount,
            'agency_commission_amount' => $agency_commission_amount,
            'agency_rebate' => $agency_rebate,
            'marketing_promotion_amount' => $marketing_promotion_amount,
            'field_activation_amount' => $field_activation_amount,
            'sales_commissions_amount' => $sales_commissions_amount,
            'creative_services_amount' => $creative_services_amount,
            'other_expenses_amount' => $other_expenses_amount
        );
        $this->am_model->update_ro_amount($ro_amnt_data, $internal_ro_number);

        //changed by mani:reason-only updating ro amount in report.
        //$ro_amnt_data = array('gross_ro_amount' =>$invoice_amount ) ;
        //$this->am_model->update_external_ro_report_details($ro_amnt_data,$internal_ro_number) ;

        // get_campaigns_for_internal_ro_number
        $data = array('internal_ro_number' => $internal_ro_number, 'is_make_good' => 0, 'visible_in_easyro' => 1);
        $campaigns = $this->am_model->get_campaigns_from_adv_campaign($data);
        $campaign_id = array();
        foreach ($campaigns as $campaigns_val) {
            array_push($campaign_id, $campaigns_val['campaign_id']);
        }
        $campaign_ids = implode(",", $campaign_id);

        // update advertiser_screen_dates
        $this->am_model->update_advertiser_screens_dates($campaign_ids, $date_of_cancel);

        //verify whether ro is approved
        $where_approved_data = array('internal_ro_number' => $internal_ro_number);
        $is_approved = $this->mg_model->get_approved_nw($where_approved_data);
        if (count($is_approved) > 0) {

            $channels_scheduled = $this->am_model->get_all_channels_scheduled_v1($internal_ro_number);
            //filter channel which is being cancelled
            $cancel_channel = $this->mg_model->get_cancel_channel_for_internal_ro($internal_ro_number);
            $channels_scheduled = $this->mg_model->filter_schedule_channel($channels_scheduled, $cancel_channel);

            $update_revision_no = 0;
            $update_field = FALSE;
            $customer_revision = array();
            foreach ($channels_scheduled as $chnl) {
                $channel_id = $chnl['channel_id'];
                $approved_nw_data = array(
                    'tv_channel_id' => $channel_id,
                    'internal_ro_number' => $internal_ro_number
                );
                $ro_approved_nw_data = $this->mg_model->get_approved_data_for_customer_channel_ro($approved_nw_data);
                $update_revision_no = $ro_approved_nw_data[0]['revision_no'] + 1;

                //maintain historical data
                $this->maintain_historical_data($ro_approved_nw_data, 'cancel_ro', $ro_id, $user_id);

                $customer_id = $ro_approved_nw_data[0]['customer_id'];
                if (!array_key_exists($customer_id, $customer_revision)) {
                    $customer_revision[$customer_id]['customer_id'] = $customer_id;
                    $customer_revision[$customer_id]['revision_no'] = $update_revision_no;
                }

                if (($chnl['total_spot_ad_seconds'] == 0) && ($chnl['total_banner_ad_seconds'] == 0)) {
                    //$this->mg_model->delete_approved_data_for_customer_channel_ro($approved_nw_data) ;
                    $update_field = TRUE;
                    $update_approved_nw_data = array(
                        'total_spot_ad_seconds' => $chnl['total_spot_ad_seconds'],
                        'channel_spot_amount' => ($chnl['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                        'total_banner_ad_seconds' => $chnl['total_banner_ad_seconds'],
                        'channel_banner_amount' => ($chnl['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                    );
                    $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $approved_nw_data);
                } else {
                    $update_field = TRUE;
                    $update_approved_nw_data = array(
                        'total_spot_ad_seconds' => $chnl['total_spot_ad_seconds'],
                        'channel_spot_amount' => ($chnl['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                        'total_banner_ad_seconds' => $chnl['total_banner_ad_seconds'],
                        'channel_banner_amount' => ($chnl['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                    );
                    $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $approved_nw_data);
                }
            }

            if ($update_field) {
                //update revision number,pdf generation status in ro_approved_network
                $cid = $user_id;
                foreach ($customer_revision as $value) {
                    $endDateCrossed = $this->mg_model->checkEndDateCrossedForROCid($internal_ro_number, $cid);
                    if (!$endDateCrossed) {
                        $revision_pdf = array(
                            'revision_no' => $value['revision_no'],
                            'pdf_generation_status' => 0
                        );
                        $where_data = array(
                            'internal_ro_number' => $internal_ro_number,
                            'customer_id' => $value['customer_id']
                        );

                        $this->mg_model->update_approved_data_where_ro_customer($revision_pdf, $where_data);
                    }


                }


            }
            //update external ro report detail
            $this->update_into_external_ro_report_detail($internal_ro_number);

            //if all ro is cancelled,code is remaining for external ro report detail// else {it should come but all 0 }

        }

        $this->db->trans_complete();
        // send mail to coo/bh/scheduling_user/AM owns/Finance user
        $users = $this->am_model->cancel_ro_mailto_list();
        $email_data = array();
        foreach ($users as $val) {
            if (!isset($val['user_email']) || empty($val['user_email'])) continue;
            array_push($email_data, $val['user_email']);
        }
        $am_email_for_ro = $this->am_model->get_am_email_for_created_ro($ro_id);

        $to_email = implode(",", $email_data) . "," . $am_email_for_ro[0]['user_email'];

        $ro_details = $this->am_model->ro_detail_for_external_ro($external_ro);

        mail_send_v1($to_email,
            "cancel_ext_ro",
            array('EXTERNAL_RO' => $external_ro),
            array(
                'EXTERNAL_RO' => $external_ro,
                'INTERNAL_RO' => $ro_details[0]['internal_ro'],
                'CLIENT_NAME' => $ro_details[0]['client'],
                'AGENCY_NAME' => $ro_details[0]['agency'],
                'CAMPAIGN_END_DATE' => $ro_details[0]['camp_end_date'],
                'RO_CANCEL_DATE' => $this->input->post('txt_cancel_date'),
                'REASON' => $this->input->post('txt_reason'),
                'INVOICE_INST' => $this->input->post('txt_inv_inst')
            ),
            '',
            '',
            '',
            ''
        );


        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    public function maintain_historical_data($ro_approved_nw_data, $action, $ro_ext_id, $user_id)
    {
        foreach ($ro_approved_nw_data as $val) {
            $history_data = array(
                'am_external_ro_id' => $ro_ext_id,
                'revision_no' => $val['revision_no'],
                'customer_id' => $val['customer_id'],
                'customer_share' => $val['customer_share'],
                'channel_id' => $val['tv_channel_id'],
                'channel_spot_avg_rate' => $val['channel_spot_avg_rate'],
                'channel_spot_amount' => $val['channel_spot_amount'],
                'channel_banner_avg_rate' => $val['channel_banner_avg_rate'],
                'channel_banner_amount' => $val['channel_banner_amount'],
                'action' => $action,
                'user_id' => $user_id
            );
            $this->mg_model->maintain_historical_data($history_data);
        }
    }

    public function update_into_external_ro_report_detail($internal_ro_number)
    {
        $ro_campaign_amount = $this->mg_model->get_external_ro_report_details($internal_ro_number);

        $gross_ro_amount = $ro_campaign_amount[0]['gross_ro_amount'];
        $agency_commission_amount = $ro_campaign_amount[0]['agency_commission_amount'];
        if ($ro_campaign_amount[0]['agency_rebate_on'] == "ro_amount") {
            $agency_rebate = $ro_campaign_amount[0]['gross_ro_amount'] * ($ro_campaign_amount[0]['agency_rebate'] / 100);
        } else {
            $agency_rebate = ($ro_campaign_amount[0]['gross_ro_amount'] - $ro_campaign_amount[0]['agency_commission_amount']) * ($ro_campaign_amount[0]['agency_rebate'] / 100);
        }
        $other_expenses = $ro_campaign_amount[0]['other_expenses'];

        $total_network_payout = $this->mg_model->get_total_network_payout($internal_ro_number);
        if (count($total_network_payout) == 0)
            $total_network_payout[0]['network_payout'] = 0;

        $actual_net_amount = $gross_ro_amount - $agency_commission_amount - $agency_rebate - $other_expenses;
        $net_contribution_amount = $actual_net_amount - $total_network_payout[0]['network_payout'];

        $net_revenue = $gross_ro_amount - $agency_commission_amount;
        $net_revenue = round(($net_revenue * SERVICE_TAX), 2);
        $net_contribution_amount_per = round(($net_contribution_amount / $net_revenue) * 100, 2);

        $total_scheduled_seconds = $this->mg_model->get_total_network_seconds_internal_ro($internal_ro_number);
        if (count($total_scheduled_seconds) == 0)
            $total_scheduled_seconds[0]['total_scheduled_seconds'] = 0;

        $report_data = array(
            'gross_ro_amount' => $gross_ro_amount,
            'agency_commission_amount' => $agency_commission_amount,
            'agency_rebate' => $agency_rebate,
            'other_expenses' => $other_expenses,
            'total_seconds_scheduled' => $total_scheduled_seconds[0]['total_scheduled_seconds'],
            'total_network_payout' => $total_network_payout[0]['network_payout'],
            'net_contribution_amount' => $net_contribution_amount,
            'net_contribution_amount_per' => $net_contribution_amount_per,
            'net_revenue' => $net_revenue
        );
        $where_data = array(
            'internal_ro_number' => $internal_ro_number
        );
        $this->mg_model->update_external_ro_report_detail($report_data, $where_data);
    }

    public function post_cancel_ro_admin_v1()
    {
        $this->is_logged_in();
        //$logged_in = $this->session->userdata("logged_in_user");

        $ro_id = $this->input->post('hid_id');
        $external_ro = $this->input->post('hid_ext_ro');
        $user_id = $this->input->post('hid_user_id');
        $date_of_cancel = $this->input->post('txt_cancel_date');
        $reason = $this->input->post('txt_reason');
        $invoice_inst = $this->input->post('txt_inv_inst');
        $invoice_amount = $this->input->post('txt_inv_amnt');

        $parameter = $external_ro . "##" . $date_of_cancel . "##" . $ro_id;
        $longParams = $reason . "##" . $invoice_inst;

        $user_data = array('job_code' => 'cancelRo',
            'done' => 0,
            'params' => $parameter,
            'customer_id' => $user_id,
            'longParams' => $longParams
        );
        $this->am_model->insert_into_job_queue($user_data);

        //Updating in ro_cancel_external_ro
        $user_data_for_entry = array('ext_ro_id' => $ro_id,
            'user_id' => $user_id,
            'date_of_submission' => date('Y-m-d'),
            'date_of_cancel' => $date_of_cancel,
            'reason' => $reason,
            'invoice_instruction' => $invoice_inst,
            'cancel_ro_by_admin' => 1,
            'ro_amount' => $invoice_amount
        );
        $cancel_id = $this->am_model->update_cancel_for_admin($ro_id, $user_data_for_entry);

        //Update The status
        $this->am_model->update_ro_status($ro_id, 'cancel_approved');

        //Fetching:For updating the proportional values
        $ro_details = $this->am_model->ro_detail_for_ro_id($ro_id);
        $ro_amount = $ro_details[0]['gross'];
        $agency_comm_from_ext_ro = $ro_details[0]['agency_com'];
        $internal_ro_number = $ro_details[0]['internal_ro'];

        $ro_amount_val = $this->am_model->get_ro_amount_data($internal_ro_number);
        $agency_commission_amount = $ro_amount_val[0]['agency_commission_amount'];
        $agency_rebate = $ro_amount_val[0]['agency_rebate'];
        $marketing_promotion_amount = $ro_amount_val[0]['marketing_promotion_amount'];
        $field_activation_amount = $ro_amount_val[0]['field_activation_amount'];
        $sales_commissions_amount = $ro_amount_val[0]['sales_commissions_amount'];
        $creative_services_amount = $ro_amount_val[0]['creative_services_amount'];
        $other_expenses_amount = $ro_amount_val[0]['other_expenses_amount'];


        //proportionality calculation
        if ($ro_amount >= $invoice_amount) {
            $percentage = ($ro_amount - $invoice_amount) / $ro_amount;
            $agency_comm_from_ext_ro = $agency_comm_from_ext_ro - ($agency_comm_from_ext_ro * $percentage);
            $agency_commission_amount = $agency_commission_amount - ($agency_commission_amount * $percentage);
            //$agency_rebate = $agency_rebate - ($agency_rebate*$percentage) ;
            $marketing_promotion_amount = $marketing_promotion_amount - ($marketing_promotion_amount * $percentage);
            $field_activation_amount = $field_activation_amount - ($field_activation_amount * $percentage);
            $sales_commissions_amount = $sales_commissions_amount - ($sales_commissions_amount * $percentage);
            $creative_services_amount = $creative_services_amount - ($creative_services_amount * $percentage);
            $other_expenses_amount = $other_expenses_amount - ($other_expenses_amount * $percentage);
        } else {
            $percentage = ($invoice_amount - $ro_amount) / $ro_amount;

            $agency_comm_from_ext_ro = $agency_comm_from_ext_ro + ($agency_comm_from_ext_ro * $percentage);
            $agency_commission_amount = $agency_commission_amount + ($agency_commission_amount * $percentage);
            //$agency_rebate = $agency_rebate - ($agency_rebate*$percentage) ;
            $marketing_promotion_amount = $marketing_promotion_amount + ($marketing_promotion_amount * $percentage);
            $field_activation_amount = $field_activation_amount + ($field_activation_amount * $percentage);
            $sales_commissions_amount = $sales_commissions_amount + ($sales_commissions_amount * $percentage);
            $creative_services_amount = $creative_services_amount + ($creative_services_amount * $percentage);
            $other_expenses_amount = $other_expenses_amount + ($other_expenses_amount * $percentage);
        }


        // code added by Lokanath: calculation of net_agency_com
        $net_agency_com = $invoice_amount - $agency_comm_from_ext_ro;

        $ro_amount_data = array('gross' => $invoice_amount, 'agency_com' => $agency_commission_amount, 'net_agency_com' => $net_agency_com, 'previous_ro_amount' => $ro_amount);
        $this->am_model->update_ro_data_in_ro_ext($ro_id, $ro_amount_data);

        $ro_amnt_data = array(
            'ro_amount' => $invoice_amount,
            'agency_commission_amount' => $agency_commission_amount,
            'agency_rebate' => $agency_rebate,
            'marketing_promotion_amount' => $marketing_promotion_amount,
            'field_activation_amount' => $field_activation_amount,
            'sales_commissions_amount' => $sales_commissions_amount,
            'creative_services_amount' => $creative_services_amount,
            'other_expenses_amount' => $other_expenses_amount
        );
        $this->am_model->update_ro_amount($ro_amnt_data, $internal_ro_number);

        // get_campaigns_for_internal_ro_number
        $data = array('internal_ro_number' => $internal_ro_number);
        $campaigns = $this->am_model->get_campaigns_from_adv_campaign($data);
        $campaign_id = array();
        foreach ($campaigns as $campaigns_val) {
            array_push($campaign_id, $campaigns_val['campaign_id']);
        }
        $campaign_ids = implode(",", $campaign_id);

        // update advertiser_screen_dates
        $this->am_model->update_advertiser_screens_dates($campaign_ids, $date_of_cancel);

        //verify whether ro is approved
        $where_approved_data = array('internal_ro_number' => $internal_ro_number);
        $is_approved = $this->mg_model->get_approved_nw($where_approved_data);

        if (count($is_approved) > 0) {
            //update ro_market_price with tmp data
            $this->cancel_ro_market_wise($ro_id, $cancel_id);
            $channels_scheduled = $this->am_model->get_all_channels_scheduled_v1($internal_ro_number);

            $update_revision_no = 0;
            $update_field = FALSE;
            $customer_revision = array();


            //$count = 0 ;
            //$channel_values = $this->mg_model->get_channel_for_market($market_name) ;
            //$channel_values = $this->mg_model->get_scheduled_channel_for_market($market_name,$internal_ro_number) ;
            //foreach($channel_values as $mkt_chnl) {
            //    $mkt_chnl_id = $mkt_chnl['tv_channel_id'] ;

            foreach ($channels_scheduled as $chnl) {
                $channel_id = $chnl['channel_id'];
                //if($mkt_chnl_id != $channel_id) continue ;

                $approved_nw_data = array(
                    'tv_channel_id' => $channel_id,
                    'internal_ro_number' => $internal_ro_number
                );
                $ro_approved_nw_data = $this->mg_model->get_approved_data_for_customer_channel_ro($approved_nw_data);
                if (count($ro_approved_nw_data) <= 0) {
                    continue;
                }
                $update_revision_no = $ro_approved_nw_data[0]['revision_no'] + 1;

                //$initial_total_spot_ad = $ro_approved_nw_data[0]['total_spot_ad_seconds'] ;
                //$initial_total_banner_ad = $ro_approved_nw_data[0]['total_banner_ad_seconds'] ;
                $total_spot_second = $total_spot_second + $ro_approved_nw_data[0]['total_spot_ad_seconds'];
                $total_banner_second = $total_banner_second + $ro_approved_nw_data[0]['total_banner_ad_seconds'];

                //maintain historical data
                $this->maintain_historical_data($ro_approved_nw_data, 'cancel_ro', $ro_id, $user_id);

                $customer_id = $ro_approved_nw_data[0]['customer_id'];
                if (!array_key_exists($customer_id, $customer_revision)) {
                    $customer_revision[$customer_id]['customer_id'] = $customer_id;
                    $customer_revision[$customer_id]['revision_no'] = $update_revision_no;
                    log_message('info', 'ro_cancel_invoice complete_cancel_ro - printing customer id-' . $customer_id . ' and internal_ro_number-' . $internal_ro_number);
                    $networkFinalInfo = $this->initiateInvoiceCancelProcess($customer_id, $internal_ro_number);
                    log_message('info', 'ro_cancel_invoice complete_cancel_ro - printing networkFinalInfo -' . print_r($networkFinalInfo, TRUE));
                    $this->updatingInvoiceCancelProcess($networkFinalInfo);
                }
                //$chnl['total_spot_ad_seconds'] == 0 :means completely cancelled
                if (($chnl['total_spot_ad_seconds'] == 0) && ($chnl['total_banner_ad_seconds'] == 0)) {
                    //$this->mg_model->delete_approved_data_for_customer_channel_ro($approved_nw_data) ;
                    $update_field = TRUE;
                    $update_approved_nw_data = array(
                        'total_spot_ad_seconds' => $chnl['total_spot_ad_seconds'],
                        'channel_spot_amount' => ($chnl['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                        'total_banner_ad_seconds' => $chnl['total_banner_ad_seconds'],
                        'channel_banner_amount' => ($chnl['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                    );
                    $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $approved_nw_data);
                } else {
                    $update_field = TRUE;
                    $update_approved_nw_data = array(
                        'total_spot_ad_seconds' => $chnl['total_spot_ad_seconds'],
                        'channel_spot_amount' => ($chnl['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                        'total_banner_ad_seconds' => $chnl['total_banner_ad_seconds'],
                        'channel_banner_amount' => ($chnl['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                    );
                    $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $approved_nw_data);
                }

            }

            if ($update_field) {
                //update revision number,pdf generation status in ro_approved_network
                foreach ($customer_revision as $value) {
                    $endDateCrossed = $this->mg_model->checkEndDateCrossedForROCidAndCancelRo($internal_ro_number, $value['customer_id'], $this->input->post('txt_cancel_date'));
                    if (!$endDateCrossed) {
                        $revision_pdf = array(
                            'revision_no' => $value['revision_no'],
                            'pdf_generation_status' => 0
                        );
                        $where_data = array(
                            'internal_ro_number' => $internal_ro_number,
                            'customer_id' => $value['customer_id']
                        );
                        //$networkFinalInfo = $this->initiateInvoiceCancelProcess($customer_id,$internal_ro_number);
                        $this->mg_model->update_approved_data_where_ro_customer($revision_pdf, $where_data);
                        //$this->updatingInvoiceCancelProcess($networkFinalInfo);
                    }
                }
            }

            //update external ro report detail
            $this->update_into_external_ro_report_detail($internal_ro_number);
        }

        //Insert into Surefire
        $this->mg_model->insertIntoRoSureFire(array('ro_id' => $ro_id, 'ro_status' => 'CANCEL_RO', 'cancel_date' => $this->input->post('txt_cancel_date'), 'processing_status' => 0));

        // send mail to coo/bh/scheduling_user/AM owns/Finance user
        $users = $this->am_model->cancel_ro_mailto_list();
        $email_data = array();
        foreach ($users as $val) {
            if (!isset($val['user_email']) || empty($val['user_email'])) continue;
            array_push($email_data, $val['user_email']);
        }
        $am_email_for_ro = $this->am_model->get_am_email_for_created_ro($ro_id);

        $to_email = implode(",", $email_data) . "," . $am_email_for_ro[0]['user_email'];

        $ro_details = $this->am_model->ro_detail_for_external_ro($external_ro);

        $actual_campaign_end_date = $this->am_model->get_actual_campaign_end_date_for_ro($ro_details[0]['internal_ro']);

        if ($actual_campaign_end_date != '') {
            mail_send_v1($to_email,
                "cancel_ext_ro",
                array('EXTERNAL_RO' => $external_ro),
                array(
                    'EXTERNAL_RO' => $external_ro,
                    'INTERNAL_RO' => $ro_details[0]['internal_ro'],
                    'CLIENT_NAME' => $ro_details[0]['client'],
                    'AGENCY_NAME' => $ro_details[0]['agency'],
                    'CAMPAIGN_END_DATE' => $actual_campaign_end_date,
                    'RO_CANCEL_DATE' => $this->input->post('txt_cancel_date'),
                    'REASON' => $this->input->post('txt_reason'),
                    'INVOICE_INST' => $this->input->post('txt_inv_inst')
                ),
                '',
                '',
                '',
                ''
            );
        } else {
            mail_send_v1($to_email,
                "complete_cancel_ext_ro",
                array('EXTERNAL_RO' => $external_ro),
                array(
                    'EXTERNAL_RO' => $external_ro,
                    'INTERNAL_RO' => $ro_details[0]['internal_ro'],
                    'CLIENT_NAME' => $ro_details[0]['client'],
                    'AGENCY_NAME' => $ro_details[0]['agency'],
                    'RO_CANCEL_DATE' => $this->input->post('txt_cancel_date'),
                    'REASON' => $this->input->post('txt_reason'),
                    'INVOICE_INST' => $this->input->post('txt_inv_inst')
                ),
                '',
                '',
                '',
                ''
            );
        }
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    public function cancel_ro_market_wise($ro_id, $cancel_id)
    {
        $cancel_market_data = $this->ro_model->get_cancel_market_data(array('am_ro_id' => $ro_id, 'cancel_id' => $cancel_id));

        foreach ($cancel_market_data as $cm) {
            $market_name = $cm['market'];
            $market_spot_price = $cm['spot_price'];
            $market_banner_price = $cm['banner_price'];

            $market_spot_fct = $cm['spot_fct'];
            $market_banner_fct = $cm['banner_fct'];

            $market_price = $market_spot_price + $market_banner_price;

            $where_data = array('am_ro_id' => $ro_id, 'cancel_id' => $cancel_id);
            $user_data = array('approved_type' => 1);

            $this->am_model->update_tmp_market($user_data, $where_data);

            //update into ro_market_price
            $this->ro_model->update_market_wise_price(array('is_cancel' => 1, 'spot_price' => $market_spot_price, 'banner_price' => $market_banner_price, 'price' => $market_price, 'spot_fct' => $market_spot_fct, 'banner_fct' => $market_banner_fct), array('ro_id' => $ro_id, 'market' => $market_name));
        }
    }

    public function initiateInvoiceCancelProcess($customer_id, $internal_ro)
    {
        $marketStr = '';
        $networkFinalInfo = array();
        $networkInfo = $this->mg_model->getAllNetworkInfo($customer_id, $internal_ro);
        log_message('info', 'ro_cancel_invoice complete_cancel_ro complete ro networkInfo array-' . print_r($networkInfo, TRUE));
        if (count($networkInfo) > 0) {
            $networkFinalInfo = $networkInfo[0];
            $roNetworkMarkets = $this->mg_model->getScheduledMarketForChannel(array($networkFinalInfo['channel_names']), $internal_ro);
            log_message('info', 'ro_cancel_invoice complete_cancel_ro complete ro network related channels-' . print_r($roNetworkMarkets, TRUE));
            //echo "<pre>";print_r($networkFinalInfo);exit;
            foreach ($roNetworkMarkets as $eachNetworkMarkets) {
                if ($marketStr == '') {
                    $marketStr = $eachNetworkMarkets['market_name'];
                } else {
                    if ($marketStr != $eachNetworkMarkets['market_name']) {
                        $marketStr .= "," . $eachNetworkMarkets['market_name'];
                    }
                }
            }
            $networkFinalInfo['market'] = $marketStr;
        }
        return $networkFinalInfo;
    }

    public function updatingInvoiceCancelProcess($networkFinalInfo)
    {
        if (count($networkFinalInfo) > 0) {
            $checkForPresenceOfInvoicedata = $this->mg_model->checkForPresenceOfInvoicedata($networkFinalInfo['network_ro_number']);
            $networkFinalInfo['pdf_processing'] = 0;
            log_message('info', 'ro_cancel_invoice complete_cancel_ro networkInfo array-' . print_r($networkFinalInfo, TRUE));
            if (count($checkForPresenceOfInvoicedata) > 0) {
                $this->mg_model->updateInvoiceCancelData($networkFinalInfo);
            } else {
                $this->mg_model->insertInvoiceCancelData($networkFinalInfo);
            }
        }

    }

    public function change_ro_dates($ro_id)
    {
        $this->is_logged_in();
        $ro_details = $this->am_model->ro_detail_for_ro_id($ro_id);

        $model['ro_details'] = $ro_details;
        $this->load->view('account_manager/change_ro_dates', $model);

    }

    public function post_changed_ro_dates()
    {

        $campaign_start_date = $this->input->post('txt_camp_start_date');
        $campaign_end_date = $this->input->post('txt_camp_end_date');
        $ro_id = $this->input->post('hid_ro_id');

        $data = array(
            'camp_start_date' => $campaign_start_date,
            'camp_end_date' => $campaign_end_date
        );
        $where_data = array(
            'id' => $ro_id
        );
        $this->db->trans_start();
        $this->ro_model->update_into_am_ext_ro($data, $where_data);
        $this->db->trans_complete();

//        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
        $response['Status'] = 'success';
        $response['Message'] = 'Dates Changed!!';
        $response['Data'] = array();
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function cancel_markets_for_ro($am_ro_id, $external_ro, $edit, $internal_ro)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];

        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];

        $model['id'] = $am_ro_id;
        $model['edit'] = $edit;

        $markets_for_ro = $this->am_model->get_market_for_ro($am_ro_id);
        $cancelled_markets_for_ro = $this->am_model->get_cancelled_markets_for_ro_v1($am_ro_id);
        $cancel_requested_markets_for_ro = $this->am_model->get_cancel_requested_markets_for_ro_v1($am_ro_id);

        $cancelled_markets = array();
        foreach ($cancelled_markets_for_ro as $can_mkt) {
            array_push($cancelled_markets, $can_mkt['market']);
        }
        $cancel_requested_markets = array();
        foreach ($cancel_requested_markets_for_ro as $can_req_mkt) {
            array_push($cancel_requested_markets, $can_req_mkt['market']);
        }

        $cancelDate = date('Y-m-d', strtotime("+2 days"));

        $markets = array();
        foreach ($markets_for_ro as $market) {
            $tmp = array();

            $tmp['market'] = $market['market'];
            $fctData = $this->getFctData($am_ext_ro[0]['internal_ro'], $market['market'], $am_ext_ro[0]['camp_start_date'], $market['spot_fct'], $market['banner_fct'], $cancelDate);

            $marketSpotFct = $market['spot_fct'];
            if ($marketSpotFct > $fctData['totalSpotFct']) {
                $marketSpotFct = $fctData['totalSpotFct'];
            }
            $marketBannerFct = $market['banner_fct'];
            if ($marketBannerFct > $fctData['totalBannerFct']) {
                $marketBannerFct = $fctData['totalBannerFct'];
            }

            $spotFraction = 0.0;
            if (isset($marketSpotFct) && !empty($marketSpotFct) && $marketSpotFct != 0) {
                $spotFraction = $fctData['scheduledSpotFct'] / $marketSpotFct;
            }

            $bannerFraction = 0.0;
            if (isset($marketBannerFct) && !empty($marketBannerFct) && $marketBannerFct != 0) {
                $bannerFraction = $fctData['scheduledBannerFct'] / $marketBannerFct;
            }

            $tmp['spot_price'] = $market['spot_price'];
            $tmp['banner_price'] = $market['banner_price'];
            $tmp['spot_price_after_cancel'] = round($market['spot_price'] * $spotFraction, 2);
            $tmp['banner_price_after_cancel'] = round($market['banner_price'] * $bannerFraction, 2);


            $tmp['spot_fct'] = $market['spot_fct'];
            $tmp['banner_fct'] = $market['banner_fct'];
            $tmp['spot_fct_after_cancel'] = $fctData['scheduledSpotFct'];
            $tmp['banner_fct_after_cancel'] = $fctData['scheduledBannerFct'];

            //RC::Deepak - check if campaign exists for particular RO and market for next two days
            $tmp['has_campaigns'] = $this->mg_model->isCampaignAvailableForMarketAndRo($am_ext_ro[0]['internal_ro'], $market['market']);
            array_push($markets, $tmp);
        }
        $model['markets'] = $markets;
        $model['cancelled_markets'] = $cancelled_markets;
        $model['cancel_requested_markets'] = $cancel_requested_markets;
        $this->load->view('account_manager/cancel_markets_for_ro', $model);
    }

    public function cancel_markets_for_ro_new()
    {
        $this->is_logged_in();
        log_message('info', 'in account_manager@cancel_markets_for_ro_new | post data - ' . print_r($_POST, true));
        $am_ro_id = $this->input->post('id');
        $external_ro = $this->input->post('cust_ro');
        $edit = $this->input->post('edit');
        $internal_ro = $this->input->post('internal_ro');
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];

        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];

        $model['id'] = $am_ro_id;
        $model['edit'] = $edit;


        //$model['markets'] = $markets;
        //$model['cancelled_markets'] = $cancelled_markets;
        //$model['cancel_requested_markets'] = $cancel_requested_markets;
//        $this->load->view('account_manager/cancel_market_for_ro_new', $model);
        $response['Status'] = 'success';
        $response['Message'] = 'Cancel market for RO view';
        $response['Data']['html'] = $this->load->view('account_manager/cancel_market_for_ro_new', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function getMarketsForCancellation()
    {
        $internal_ro = $this->input->post('internal_ro');
        $cancel_date = $this->input->post('cancelDate');
        $cancelDate = date('Y-m-d', strtotime("$cancel_date"));

        $roDetails = $this->am_model->get_ro_details_for_internal_ro($internal_ro);
        $am_ro_id = $roDetails[0]['id'];
        $markets_for_ro = $this->am_model->get_market_for_ro($am_ro_id);
        $cancelled_markets_for_ro = $this->am_model->get_cancelled_markets_for_ro_v1($am_ro_id);
        $cancel_requested_markets_for_ro = $this->am_model->get_cancel_requested_markets_for_ro_v1($am_ro_id);

        $cancelled_markets = array();
        foreach ($cancelled_markets_for_ro as $can_mkt) {
            array_push($cancelled_markets, $can_mkt['market']);
        }
        $cancel_requested_markets = array();
        foreach ($cancel_requested_markets_for_ro as $can_req_mkt) {
            array_push($cancel_requested_markets, $can_req_mkt['market']);
        }

        $html = "<table cellpadding='0' cellspacing='0' width='100%'>
                                <tr>
                                    <th style='width:5%'></th>
                                    <th style='width:30%'>Markets</th>
                                    <th style='width:40%'>Spot Price</th>
                                    <th style='width:25%'>Banner Price</th>
                                </tr>";

        foreach ($markets_for_ro as $market) {
            $tmp = array();

            $tmp['market'] = $market['market'];
            $fctData = $this->getFctData($internal_ro, $market['market'], $roDetails[0]['camp_start_date'], $market['spot_fct'], $market['banner_fct'], $cancelDate);

            $marketSpotFct = $market['spot_fct'];
            if ($marketSpotFct > $fctData['totalSpotFct']) {
                $marketSpotFct = $fctData['totalSpotFct'];
            }
            $marketBannerFct = $market['banner_fct'];
            if ($marketBannerFct > $fctData['totalBannerFct']) {
                $marketBannerFct = $fctData['totalBannerFct'];
            }
            $spotFraction = 0.0;
            if (isset($marketSpotFct) && !empty($marketSpotFct) && $marketSpotFct != 0) {
                $spotFraction = $fctData['scheduledSpotFct'] / $marketSpotFct;
            }
            $bannerFraction = 0.0;
            if (isset($marketBannerFct) && !empty($marketBannerFct) && $marketBannerFct != 0) {
                $bannerFraction = $fctData['scheduledBannerFct'] / $marketBannerFct;
            }

            $spot_price = $market['spot_price'];
            $banner_price = $market['banner_price'];
            $spot_price_after_cancel = round($market['spot_price'] * $spotFraction, 2);
            $banner_price_after_cancel = round($market['banner_price'] * $bannerFraction, 2);


            $spot_fct = $market['spot_fct'];
            $banner_fct = $market['banner_fct'];
            $spot_fct_after_cancel = $fctData['scheduledSpotFct'];
            $banner_fct_after_cancel = $fctData['scheduledBannerFct'];

            //RC::Deepak - check if campaign exists for particular RO and market for next two days
            $has_campaigns = $this->mg_model->isCampaignAvailableForMarketAndRo($internal_ro, $market['market'], $cancelDate);
            if ($has_campaigns == 0) {
                $spot_price_after_cancel = 0;
                $banner_price_after_cancel = 0;
                $spot_fct_after_cancel = 0;
                $banner_fct_after_cancel = 0;
            }
            $market_name = str_replace(" ", "_", $market['market']);
            if (in_array($market['market'], $cancel_requested_markets)) {
                $htmlValue = 'disabled="disabled"';
            } else if (in_array($market['market'], $cancelled_markets)) {
                $htmlValue = 'class="markets_cancelled"';
            } else {
                $htmlValue = 'class="markets_to_cancel"';
            }


            if (!in_array($market['market'], $cancelled_markets)) {
                $html .= "<tr>";
                $html .= '<td><input type="checkbox" id="cancel_' . $market_name . '"' . $htmlValue . ' class="markets_to_cancel" onclick="javascript:price_handler(\'' . $market_name . '\');show_hide_req_button()"></td>';

                $html .= "<td>" . $market['market'] . "</td>
                                    <td><input type='text' name='markets[" . $market_name . "][spot]' readonly class='amount'  id='spot_" . $market_name . "' value='" . $spot_price . "' ></td>
                                    <td><input type='text' name='markets[" . $market_name . "][banner]' readonly class='amount'  id='banner_" . $market_name . "' value='" . $banner_price . "' ></td>
                                </tr> ";

                $html .= "  <input type='hidden' name='markets[" . $market_name . "][spot_fct]' id='spot_fct_" . $market_name . "' value='" . $spot_fct . " '>
                                <input type='hidden' name='markets[" . $market_name . "][banner_fct]' id='banner_fct_" . $market_name . "' value='" . $banner_fct . "'>

                                <input type='hidden' id='hid_spot_fct_" . $market_name . "' value='" . $spot_fct . "'>
                                <input type='hidden' id='hid_banner_fct_" . $market_name . "' value='" . $banner_fct . "'>

                                <input type='hidden' id='hid_spot_fct_cancel_" . $market_name . "' value='$spot_fct_after_cancel'>
                                <input type='hidden' id='hid_banner_fct_cancel_" . $market_name . "' value='$banner_fct_after_cancel'>

                                <input type='hidden' id='hid_spot_price_cancel_" . $market_name . "' value='$spot_price_after_cancel'>
                                <input type='hidden' id='hid_banner_price_cancel_" . $market_name . "' value='$banner_price_after_cancel'>

                                <input type='hidden' id='hid_spot_" . $market_name . "' value='$spot_price'>
                                <input type='hidden' id='hid_banner_" . $market_name . "' value='$banner_price'>

                                <input type='hidden' name='markets[$market_name][is_cancelled]' id='is_cancelled_" . $market_name . "' value='0'>
                                <input type='hidden' name='markets[$market_name][has_campaigns]' id='has_campaigns_" . $market_name . "' value='$has_campaigns'>";
            }
        }

        $html .= "<tr id='reason_controls' style='display: none'>
                                                <td>&nbsp;</td>
                                                <td><span id='reason_label'>Reason for Cancellation:</span> <span style='color:#F00;'> *</span></td>
                                                <td><input type='text' name='reason_can' id='reason_can' class='amount' value='' style='width:250px;'</td>
                                            </tr>
                            <tr id='req_controls' style='display: none'>
                                <td>&nbsp;</td>
                                <td>
                                    <input type='button' class='submitlong' id='req_market_can_market' value='Approval Request'  />
                                </td>
                            <tr>

                            </table>";

        $html .= "</table>";
        echo $html;
    }

    public function cancel_market_for_ro($am_ro_id, $external_ro, $edit, $internal_ro)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];

        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];

        $model['id'] = $am_ro_id;
        $model['edit'] = $edit;

        $markets_for_ro = $this->am_model->get_market_for_ro($am_ro_id);
        $markets = array();
        foreach ($markets_for_ro as $market) {
            $tmp = array();
            $tmp['spot_price'] = $market['spot_price'];
            $tmp['banner_price'] = $market['banner_price'];

            $tmp['spot_fct'] = $market['spot_fct'];
            $tmp['banner_fct'] = $market['banner_fct'];
            $tmp['cancelled'] = $market['is_cancel'];

            array_push($markets, $tmp);
        }
        $model['markets'] = $markets;
        $this->load->view('account_manager/cancel_market_for_ro', $model);
    }

    public function cancel_markets_by_content($am_ro_id, $external_ro, $edit, $internal_ro)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];

        $all_contents = $this->am_model->get_all_contents_for_ro(base64_decode($internal_ro));

        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];
        $model['all_contents'] = $all_contents;

        $model['id'] = $am_ro_id;
        $model['edit'] = $edit;

        $cancelled_markets_for_ro = $this->am_model->get_cancelled_markets_for_ro($am_ro_id);
        $cancel_requested_markets_for_ro = $this->am_model->get_cancel_requested_markets_for_ro($am_ro_id);

        $cancelled_markets = array();
        foreach ($cancelled_markets_for_ro as $can_mkt) {
            array_push($cancelled_markets, $can_mkt['market']);
        }
        $cancel_requested_markets = array();
        foreach ($cancel_requested_markets_for_ro as $can_req_mkt) {
            array_push($cancel_requested_markets, $can_req_mkt['market']);
        }

        $model['cancelled_markets'] = $cancelled_markets;
        $model['cancel_requested_markets'] = $cancel_requested_markets;
        $this->load->view('account_manager/cancel_markets_by_content', $model);
    }

    //[BugFix:793]BH must see the changed RO amounts in the pending requests tab when markets are cancelled

    public function cancel_markets_by_contents()
    {
        $this->is_logged_in();
        $am_ro_id = $this->input->post('id');
        $external_ro = $this->input->post('cust_ro');
        $edit = $this->input->post('edit');
        $internal_ro = $this->input->post('internal_ro');
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];

        $all_contents = $this->am_model->get_all_contents_for_ro(($internal_ro));

        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];
        $model['all_contents'] = $all_contents;

        $model['id'] = $am_ro_id;
        $model['edit'] = $edit;

        $cancelled_markets_for_ro = $this->am_model->get_cancelled_markets_for_ro($am_ro_id);
        $cancel_requested_markets_for_ro = $this->am_model->get_cancel_requested_markets_for_ro($am_ro_id);

        $cancelled_markets = array();
        foreach ($cancelled_markets_for_ro as $can_mkt) {
            array_push($cancelled_markets, $can_mkt['market']);
        }
        $cancel_requested_markets = array();
        foreach ($cancel_requested_markets_for_ro as $can_req_mkt) {
            array_push($cancel_requested_markets, $can_req_mkt['market']);
        }

        $model['cancelled_markets'] = $cancelled_markets;
        $model['cancel_requested_markets'] = $cancel_requested_markets;
        //$this->load->view('account_manager/cancel_market_by_contents_new', $model);
        $response['Status'] = 'success';
        $response['Message'] = 'Content cancellation view!!';
        $response['Data']['html'] = $this->load->view('account_manager/cancel_market_by_contents_new', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function getMarketsForContent()
    {
        $content = $this->input->post('content');
        $internal_ro = $this->input->post('internal_ro');
        $cancel_date = $this->input->post('cancelDate');
        $cancelDate = date('Y-m-d', strtotime("$cancel_date"));

        $markets = $this->am_model->get_markets_for_content($content, $internal_ro);
        $html = "<table cellpadding='0' cellspacing='0' width='100%'>
                            <tr>
                                <th style='width:5%'></th>
                                <th style='width:30%'>Markets</th>
                                <th style='width:40%'>Spot Price</th>
                                <th style='width:25%'>Banner Price</th>
                            </tr>";

        foreach ($markets as $mkt) {
            $fctData = $this->getFctDataForContent($internal_ro, $mkt['market_id'], $content, $mkt['camp_start_date'], $mkt['spot_fct'], $mkt['banner_fct'], $cancelDate);

            $marketSpotFct = $mkt['spot_fct'];
            if ($marketSpotFct > $fctData['totalSpotFct']) {
                $marketSpotFct = $fctData['totalSpotFct'];
            }
            $marketBannerFct = $mkt['banner_fct'];
            if ($marketBannerFct > $fctData['totalBannerFct']) {
                $marketBannerFct = $fctData['totalBannerFct'];
            }

            //$market['spot_fct'] - $fctData['scheduledSpotFct'] / $marketSpotFct ;
            $spotFctRemaining = $marketSpotFct - $fctData['scheduledSpotFct'];
            $bannerFctRemaining = $marketBannerFct - $fctData['scheduledBannerFct'];

            if ($spotFctRemaining < 0) {
                $spotFctRemaining = 0;
            }
            if ($bannerFctRemaining < 0) {
                $bannerFctRemaining = 0;
            }

            $spotFraction = 0.0;
            if (isset($marketSpotFct) && !empty($marketSpotFct) && $marketSpotFct != 0) {
                $spotFraction = $spotFctRemaining / $marketSpotFct;
            }
            $bannerFraction = 0.0;
            if (isset($marketBannerFct) && !empty($marketBannerFct) && $marketBannerFct != 0) {
                $bannerFraction = $bannerFctRemaining / $marketBannerFct;
            }

            $spot_price = $mkt['spot_price'];
            $banner_price = $mkt['banner_price'];
            $spot_price_after_cancel = round($mkt['spot_price'] * $spotFraction, 2);
            $banner_price_after_cancel = round($mkt['banner_price'] * $bannerFraction, 2);


            $spot_fct = $mkt['spot_fct'];
            $banner_fct = $mkt['banner_fct'];
            $spot_fct_after_cancel = $spotFctRemaining;
            $banner_fct_after_cancel = $bannerFctRemaining;

            //RC::Deepak - check if campaign exists for particular RO and market for next two days
            //$has_campaigns = $this->mg_model->isCampaignAvailableForMarketRoContentBrand($internal_ro,$mkt['market'],$cancelDate,$content) ;
            //array_push($markets,$tmp) ;

            $market_name = str_replace(" ", "_", $mkt['market']);

            $html .= "<tr>";
            $html .= '  <td><input type="checkbox" id="cancel_' . $market_name . '" class="markets_to_cancel" onclick="javascript:price_handler(\'' . $market_name . '\');show_hide_req_button()"></td>';
            $html .= "<td>" . $mkt['market'] . "</td>
                            <td><input type='text' name='markets[" . $market_name . "][spot]' readonly class='amount'  id='spot_" . $market_name . "' value='" . $spot_price . "' ></td>
                            <td><input type='text' name='markets[" . $market_name . "][banner]' readonly class='amount'  id='banner_" . $market_name . "' value='" . $banner_price . "' ></td>
                        </tr> ";

            $html .= "  <input type='hidden' name='markets[" . $market_name . "][spot_fct]' id='spot_fct_" . $market_name . "' value='" . $spot_fct . " '>
                        <input type='hidden' name='markets[" . $market_name . "][banner_fct]' id='banner_fct_" . $market_name . "' value='" . $banner_fct . "'>

                        <input type='hidden' id='hid_spot_fct_" . $market_name . "' value='" . $spot_fct . "'>
                        <input type='hidden' id='hid_banner_fct_" . $market_name . "' value='" . $banner_fct . "'>

                        <input type='hidden' id='hid_spot_fct_cancel_" . $market_name . "' value='$spot_fct_after_cancel'>
                        <input type='hidden' id='hid_banner_fct_cancel_" . $market_name . "' value='$banner_fct_after_cancel'>

                        <input type='hidden' id='hid_spot_price_cancel_" . $market_name . "' value='$spot_price_after_cancel'>
                        <input type='hidden' id='hid_banner_price_cancel_" . $market_name . "' value='$banner_price_after_cancel'>

                        <input type='hidden' id='hid_spot_" . $market_name . "' value='$spot_price'>
                        <input type='hidden' id='hid_banner_" . $market_name . "' value='$banner_price'>

                        <input type='hidden' name='markets[$market_name][is_cancelled]' id='is_cancelled_" . $market_name . "' value='0'>";
            //<input type='hidden' name='markets[$market_name][has_campaigns]' id='has_campaigns_".$market_name."' value='$has_campaigns'>
        }
        $html .= "<tr id='reason_controls' style='display: none'>
                                            <td>&nbsp;</td>
                                            <td><span id='reason_label'>Reason for Cancellation:</span> <span style='color:#F00;'> *</span></td>
                                            <td><input type='text' name='reason_can' id='reason_can' class='amount' value='' style='width:250px;'</td>
                                        </tr>
                        <tr id='req_controls' style='display: none'>
                            <td>&nbsp;</td>
                            <td>
                                <input type='button' class='submitlong' id='req_market_can_content' value='Approval Request' />
                            </td>
                        <tr>

                        </table>";

        $html .= "</table>";
        echo $html;
    }

    public function getFctDataForContent($internalRo, $marketId, $content, $startDate, $spotFct, $bannerFct, $cancelDate = null)
    {
        if (!isset($cancelDate) || empty($cancelDate)) {
            $cancelDate = date('Y-m-d', strtotime("+2 days"));
        }


        $scheduledFct = $this->mg_model->get_scheduled_impression_for_ro_by_brand_and_content($internalRo, $marketId, null, $content, $startDate, $cancelDate, $spotFct, $bannerFct);
        //$totalFct = $this->mg_model->get_total_scheduled_seconds_For_Market($internalRo,$marketId,$spotFct,$bannerFct) ;

        $data = array();

        $spotKey = array_keys($scheduledFct['spotFct']);
        $bannerKey = array_keys($scheduledFct['bannerFct']);

        $spotChannelId = $spotKey[0];
        $bannerChannelId = $bannerKey[0];

        $data['scheduledSpotFct'] = $scheduledFct['spotFct'][$spotChannelId];
        $data['totalSpotFct'] = $scheduledFct['totalSpotFct'][$spotChannelId];
        $data['scheduledBannerFct'] = $scheduledFct['bannerFct'][$bannerChannelId];
        $data['totalBannerFct'] = $scheduledFct['totalBannerFct'][$bannerChannelId];
        return $data;
    }

    public function cancel_markets_by_brand($am_ro_id, $external_ro, $edit, $internal_ro)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];

        $all_brands = $this->am_model->get_all_brands_for_ro(base64_decode($internal_ro));

        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];
        $model['all_brands'] = $all_brands;

        $model['id'] = $am_ro_id;
        $model['edit'] = $edit;

        $cancelled_markets_for_ro = $this->am_model->get_cancelled_markets_for_ro($am_ro_id);
        $cancel_requested_markets_for_ro = $this->am_model->get_cancel_requested_markets_for_ro($am_ro_id);

        $cancelled_markets = array();
        foreach ($cancelled_markets_for_ro as $can_mkt) {
            array_push($cancelled_markets, $can_mkt['market']);
        }
        $cancel_requested_markets = array();
        foreach ($cancel_requested_markets_for_ro as $can_req_mkt) {
            array_push($cancel_requested_markets, $can_req_mkt['market']);
        }

        $model['cancelled_markets'] = $cancelled_markets;
        $model['cancel_requested_markets'] = $cancel_requested_markets;
        $this->load->view('account_manager/cancel_markets_by_brand', $model);
    }

    //[BugFix:793]BH must see the changed RO amounts in the pending requests tab when markets are cancelled

    public function cancel_market_by_brands()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $am_ro_id = $this->input->post('id');
        $external_ro = $this->input->post('cust_ro');
        $edit = $this->input->post('edit');
        $internal_ro = $this->input->post('internal_ro');
        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];

        $all_brands = $this->am_model->get_all_brands_for_ro(($internal_ro));

        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];
        $model['all_brands'] = $all_brands;

        $model['id'] = $am_ro_id;
        $model['edit'] = $edit;

        $cancelled_markets_for_ro = $this->am_model->get_cancelled_markets_for_ro($am_ro_id);
        $cancel_requested_markets_for_ro = $this->am_model->get_cancel_requested_markets_for_ro($am_ro_id);

        $cancelled_markets = array();
        foreach ($cancelled_markets_for_ro as $can_mkt) {
            array_push($cancelled_markets, $can_mkt['market']);
        }
        $cancel_requested_markets = array();
        foreach ($cancel_requested_markets_for_ro as $can_req_mkt) {
            array_push($cancel_requested_markets, $can_req_mkt['market']);
        }

        $model['cancelled_markets'] = $cancelled_markets;
        $model['cancel_requested_markets'] = $cancel_requested_markets;
        // $this->load->view('account_manager/cancel_markets_by_brand_new', $model);
        $response['Status'] = 'success';
        $response['Message'] = 'Brand cancellation view!!';
        $response['Data']['html'] = $this->load->view('account_manager/cancel_markets_by_brand_new', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function getMarketsForBrand()
    {
        $brand = $this->input->post('brand');
        $internal_ro = $this->input->post('internal_ro');
        $cancel_date = $this->input->post('cancelDate');
        $cancelDate = date('Y-m-d', strtotime("$cancel_date"));

        $markets = $this->am_model->get_markets_for_brand($brand, $internal_ro);
        $html = "<table cellpadding='0' cellspacing='0' width='100%'>
                            <tr>
                                <th style='width:5%'></th>
                                <th style='width:30%'>Markets</th>
                                <th style='width:40%'>Spot Price</th>
                                <th style='width:25%'>Banner Price</th>
                            </tr>";
        foreach ($markets as $mkt) {

            $fctData = $this->getFctDataForBrand($internal_ro, $mkt['market_id'], $brand, $mkt['camp_start_date'], $mkt['spot_fct'], $mkt['banner_fct'], $cancelDate);

            $marketSpotFct = $mkt['spot_fct'];
            if ($marketSpotFct > $fctData['totalSpotFct']) {
                $marketSpotFct = $fctData['totalSpotFct'];
            }
            $marketBannerFct = $mkt['banner_fct'];
            if ($marketBannerFct > $fctData['totalBannerFct']) {
                $marketBannerFct = $fctData['totalBannerFct'];
            }

            //$market['spot_fct'] - $fctData['scheduledSpotFct'] / $marketSpotFct ;
            $spotFctRemaining = $marketSpotFct - $fctData['scheduledSpotFct'];
            $bannerFctRemaining = $marketBannerFct - $fctData['scheduledBannerFct'];

            if ($spotFctRemaining < 0) {
                $spotFctRemaining = 0;
            }
            if ($bannerFctRemaining < 0) {
                $bannerFctRemaining = 0;
            }

            $spotFraction = 0.0;
            if (isset($marketSpotFct) && !empty($marketSpotFct) && $marketSpotFct != 0) {
                $spotFraction = $spotFctRemaining / $marketSpotFct;
            }
            $bannerFraction = 0.0;
            if (isset($marketBannerFct) && !empty($marketBannerFct) && $marketBannerFct != 0) {
                $bannerFraction = $bannerFctRemaining / $marketBannerFct;
            }

            $spot_price = $mkt['spot_price'];
            $banner_price = $mkt['banner_price'];
            $spot_price_after_cancel = round($mkt['spot_price'] * $spotFraction, 2);
            $banner_price_after_cancel = round($mkt['banner_price'] * $bannerFraction, 2);


            $spot_fct = $mkt['spot_fct'];
            $banner_fct = $mkt['banner_fct'];
            $spot_fct_after_cancel = $spotFctRemaining;
            $banner_fct_after_cancel = $bannerFctRemaining;

            //RC::Deepak - check if campaign exists for particular RO and market for next two days
            //$has_campaigns = $this->mg_model->isCampaignAvailableForMarketAndRo($internal_ro,$mkt['market']) ;
            //array_push($markets,$tmp) ;

            $market_name = str_replace(" ", "_", $mkt['market']);
            $html .= "  <tr>";
            $html .= '
                         <td><input type="checkbox" id="cancel_' . $market_name . '" class="markets_to_cancel" onclick="javascript:price_handler(\'' . $market_name . '\');show_hide_req_button()"></td>';
            $html .= "<td>" . $mkt['market'] . "</td>
                            <td><input type='text' name='markets[" . $market_name . "][spot]' readonly class='amount'  id='spot_" . $market_name . "' value='" . $spot_price . "' ></td>
                            <td><input type='text' name='markets[" . $market_name . "][banner]' readonly class='amount'  id='banner_" . $market_name . "' value='" . $banner_price . "' ></td>
                        </tr> ";

            $html .= "  <input type='hidden' name='markets[" . $market_name . "][spot_fct]' id='spot_fct_" . $market_name . "' value='" . $spot_fct . " '>
                        <input type='hidden' name='markets[" . $market_name . "][banner_fct]' id='banner_fct_" . $market_name . "' value='" . $banner_fct . "'>

                        <input type='hidden' id='hid_spot_fct_" . $market_name . "' value='" . $spot_fct . "'>
                        <input type='hidden' id='hid_banner_fct_" . $market_name . "' value='" . $banner_fct . "'>

                        <input type='hidden' id='hid_spot_fct_cancel_" . $market_name . "' value='$spot_fct_after_cancel'>
                        <input type='hidden' id='hid_banner_fct_cancel_" . $market_name . "' value='$banner_fct_after_cancel'>

                        <input type='hidden' id='hid_spot_price_cancel_" . $market_name . "' value='$spot_price_after_cancel'>
                        <input type='hidden' id='hid_banner_price_cancel_" . $market_name . "' value='$banner_price_after_cancel'>

                        <input type='hidden' id='hid_spot_" . $market_name . "' value='$spot_price'>
                        <input type='hidden' id='hid_banner_" . $market_name . "' value='$banner_price'>

                        <input type='hidden' name='markets[$market_name][is_cancelled]' id='is_cancelled_" . $market_name . "' value='0'>";
            //<input type='hidden' name='markets[$market_name][has_campaigns]' id='has_campaigns_".$market_name."' value='$has_campaigns'>";
        }

        $html .= "<tr id='reason_controls' style='display: none'>
                            <td>&nbsp;</td>
                            <td><span id='reason_label'>Reason for Cancellation:</span> <span style='color:#F00;'> *</span></td>
                            <td><input type='text' name='reason_can' id='reason_can' class='amount' value='' style='width:250px;'</td>
                        </tr>
                        <tr id='req_controls' style='display: none'>
                            <td>&nbsp;</td>
                            <td>
                                <input type='button' class='submitlong' id='req_market_can_brand' value='Approval Request' />
                            </td>
                        <tr>

                        </table>";
        echo $html;
    }

    public function getFctDataForBrand($internalRo, $marketId, $brand_id, $startDate, $spotFct, $bannerFct, $cancelDate = null)
    {
        if (!isset($cancelDate) || empty($cancelDate)) {
            $cancelDate = date('Y-m-d', strtotime("+2 days"));
        }


        $scheduledFct = $this->mg_model->get_scheduled_impression_for_ro_by_brand_and_content($internalRo, $marketId, $brand_id, null, $startDate, $cancelDate, $spotFct, $bannerFct);
        //$totalFct = $this->mg_model->get_total_scheduled_seconds_For_Market($internalRo,$marketId,$spotFct,$bannerFct) ;

        $data = array();

        $spotKey = array_keys($scheduledFct['spotFct']);
        $bannerKey = array_keys($scheduledFct['bannerFct']);

        $spotChannelId = $spotKey[0];
        $bannerChannelId = $bannerKey[0];

        $data['scheduledSpotFct'] = $scheduledFct['spotFct'][$spotChannelId];
        $data['totalSpotFct'] = $scheduledFct['totalSpotFct'][$spotChannelId];
        $data['scheduledBannerFct'] = $scheduledFct['bannerFct'][$bannerChannelId];
        $data['totalBannerFct'] = $scheduledFct['totalBannerFct'][$bannerChannelId];
        return $data;
    }

    public function get_gross_after_cancellation()
    {

        $cancel_date = $this->input->post('cancel_date');
        $am_ro_id = $this->input->post('ro_id');
        log_message('info', 'in account_manager@get_gross_after_cancellation | ' . print_r($_POST, true));
        $markets_for_ro = $this->am_model->get_market_for_ro($am_ro_id);
        log_message('info', 'in account_manager@get_gross_after_cancellation | ' . print_r($markets_for_ro, true));
        $ro_details = $this->am_model->ro_detail_for_ro_id($am_ro_id);
        log_message('info', 'in account_manager@get_gross_after_cancellation | ' . print_r($ro_details, true));
        $markets_price_after_cancel = 0;
        foreach ($markets_for_ro as $market) {
            $fctData = array();
            if ($market['is_cancel'] == 0 || $market['is_cancel'] == 1) {
                $fctData = $this->getFctData($ro_details[0]['internal_ro'], $market['market'], $ro_details[0]['camp_start_date'], $market['spot_fct'], $market['banner_fct'], $cancel_date);
                if (count($fctData) > 0) {
                    $marketSpotFct = $market['spot_fct'];
                    if ($marketSpotFct > $fctData['totalSpotFct']) {
                        $marketSpotFct = $fctData['totalSpotFct'];
                    }
                    $marketBannerFct = $market['banner_fct'];
                    if ($marketBannerFct > $fctData['totalBannerFct']) {
                        $marketBannerFct = $fctData['totalBannerFct'];
                    }

                    log_message('info', 'in account_manager@get_gross_after_cancellation | market fct spot value =' . print_r($marketSpotFct, true));
                    $spotFraction = 0.0;
                    if (isset($marketSpotFct) && !empty($marketSpotFct) && $marketSpotFct != 0) {
                        $spotFraction = $fctData['scheduledSpotFct'] / $marketSpotFct;
                        log_message('info', 'in account_manager@get_gross_after_cancellation | $spotFraction =' . print_r($spotFraction, true));
                    }
                    log_message('info', 'in account_manager@get_gross_after_cancellation | market fct banner value =' . print_r($marketBannerFct, true));
                    $bannerFraction = 0.0;
                    if (isset($marketBannerFct) && !empty($marketBannerFct) && $marketBannerFct != 0) {
                        $bannerFraction = $fctData['scheduledBannerFct'] / $marketBannerFct;
                        log_message('info', 'in account_manager@get_gross_after_cancellation | $bannerFraction =' . print_r($bannerFraction, true));
                    }

                    $spot_price_after_cancel = round($market['spot_price'] * $spotFraction, 2);
                    log_message('info', 'in account_manager@get_gross_after_cancellation | $spot_price_after_cancel =' . print_r($spot_price_after_cancel, true));
                    $banner_price_after_cancel = round($market['banner_price'] * $bannerFraction, 2);
                    log_message('info', 'in account_manager@get_gross_after_cancellation | $banner_price_after_cancel =' . print_r($banner_price_after_cancel, true));
                    $markets_price_after_cancel += $spot_price_after_cancel + $banner_price_after_cancel;
                    log_message('info', 'in account_manager@get_gross_after_cancellation | $markets_price_after_cancel =' . print_r($markets_price_after_cancel, true));
                }
            }
        }

        echo $markets_price_after_cancel;

    }

    public function revised_market_price_for_ro($cancel_id)
    {
        $this->is_logged_in();
        $cancelled_markets = $this->am_model->get_cancelled_markets(array('cancel_id' => $cancel_id));
        $markets = array();
        foreach ($cancelled_markets as $market) {
            $tmp = array();
            $tmp['market'] = $market['market'];
            $tmp['spot_price'] = $market['spot_price'];
            $tmp['banner_price'] = $market['banner_price'];
            $tmp['is_cancelled'] = $market['is_cancelled'];
            array_push($markets, $tmp);
        }
        $model['markets'] = $markets;
        $this->load->view('account_manager/revised_market_price_for_ro', $model);
    }

    public function show_revised_content_brand($cancel_id)
    {
        $this->is_logged_in();

        $cancelRequestedData = $this->am_model->get_revised_ro_amount(array('id' => $cancel_id));
        if ($cancelRequestedData[0]['cancel_type'] == 'cancel_brand') {
            $model['subject'] = 'Brand Cancellation Request';
            $model['content_brand_cancellation'] = 'Brand Name :';
            $brandValues = $this->mg_model->getBrandName_v1(trim($cancelRequestedData[0]['caption_brand_name']));
            $model['content_brand'] = $brandValues[0]['brand'];
        } else if ($cancelRequestedData[0]['cancel_type'] == 'cancel_content') {
            $model['subject'] = 'Content Cancellation Request';
            $model['content_brand_cancellation'] = 'Content Name :';
            $model['content_brand'] = $cancelRequestedData[0]['caption_brand_name'];
        }

        $cancelled_markets = $this->am_model->get_cancelled_markets(array('cancel_id' => $cancel_id));

        $markets = array();
        foreach ($cancelled_markets as $market) {
            $tmp = array();
            $tmp['market'] = $market['market'];
            $tmp['spot_price'] = $market['spot_price'];
            $tmp['banner_price'] = $market['banner_price'];
            $tmp['is_cancelled'] = $market['is_cancelled'];
            array_push($markets, $tmp);
        }
        $model['markets'] = $markets;
        $this->load->view('account_manager/show_revised_content_brand', $model);
    }

    public function review_fct_ro_details($am_ro_id, $cancel_id, $cancel_type)
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];//print_r($logged_in[0]);exit;
        $model['ro_details'] = $this->am_model->get_ro_details($am_ro_id);
        $model['file_path'] = $this->am_model->get_ro_file_path($am_ro_id);

        $model['id'] = $am_ro_id;
        $model['markets_against_ro'] = $this->am_model->get_markets_against_ro($am_ro_id);
        $campaign_created = 0;
        $verify_campaign_created = $this->am_model->verify_campaign_created_for_internal_ro($model['ro_details'][0]['internal_ro']);
        if (count($verify_campaign_created) > 0) {
            $campaign_created = 1;
        }
        $model['campaign_created'] = $campaign_created;
        // check ro status

        $ro_assigned_user_name = $this->am_model->ro_assigned_user_name($model['ro_details'][0]['user_id']);
        // end
        $model['ro_assigned_user_name'] = $ro_assigned_user_name;

        $userId = $logged_in[0]['user_id'];

        if ($cancel_type == 'submit_ro_approval') {
            $where_data = array(
                'cancel_type' => 'submit_ro_approval',
                'id' => $cancel_id
            );
            $get_request_details = $this->am_model->is_cancel_request_sent_by_am($where_data);
            $model['net_contribution_percent'] = $get_request_details[0]['net_contribuition_percent'];

            $markets_for_ro = $this->am_model->get_markets_against_ro($am_ro_id);
            $markets = array();
            foreach ($markets_for_ro as $market) {
                $tmp = array();
                $tmp['market'] = $market['market'];
                $tmp['spot_discount'] = $market['less_spot_rate_percentage'];
                $tmp['banner_discount'] = $market['less_banner_rate_percentage'];
                array_push($markets, $tmp);
            }
            $model['markets'] = $markets;
        }
        $model['cancel_type'] = $cancel_type;
        $this->load->view('account_manager/review_fct_ro_details', $model);
    }

    public function review_non_fct_ro_details($am_ro_id, $cancel_id, $cancel_type)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $ro_details = $this->am_model->get_non_fct_ro_details($am_ro_id);
        $region_name = $this->am_model->get_region_name($ro_details[0]['region_id']);

        $model = array();
        $model['logged_in_user'] = $logged_in[0];//print_r($logged_in[0]);exit;
        $model['ro_details'] = $ro_details;
        $model['client_contact'] = $this->am_model->get_client_contact($ro_details[0]['client_contact']);
        $model['agency_contact'] = $this->am_model->get_agency_contact($ro_details[0]['agency_contact']);
        $model['financial_year'] = $ro_details[0]['financial_year'];
        $model['amount'] = $this->non_fct_ro_model->get_monthly_amounts_for_ro($ro_details[0]['id']);
        $model['id'] = $am_ro_id;
        $model['region'] = $region_name[0]['region_name'];

        $this->load->view('non_fct_ro/review_non_fct_ro_details', $model);
    }

    public function revised_ro_amount($cancel_id)
    {
        $this->is_logged_in();
        $revised_amount = $this->am_model->get_revised_ro_amount(array('id' => $cancel_id));
        $model['revised_amount'] = $revised_amount[0]['ro_amount'];
        $model['date_of_cancel'] = $revised_amount[0]['date_of_cancel'];
        $this->load->view('account_manager/revised_ro_amount', $model);
    }

    public function post_cancel_content_brand()
    {
        $this->is_logged_in();
        log_message('info', 'in account_manager@post_cancel_markets| post data - ' . print_r($_POST, true));
        $logged_in = $this->session->userdata("logged_in_user");

        $order_id = $this->input->post('hid_order_id');
        $am_ro_id = $this->input->post('hid_id');
        $edit = $this->input->post('hid_edit');

        $cancel_date = $this->input->post('txt_cancel_date');
        $cancel_reason = $this->input->post('reason_can');
        $markets_price = $this->input->post('markets');
        $cancel_type = $this->input->post('hid_cancel_market_type');
        $caption_brand = $this->input->post('caption_brand');

        $user_data = array(
            'ext_ro_id' => $am_ro_id,
            'cancel_type' => $cancel_type,
            'user_id' => $logged_in[0]['user_id'],
            'date_of_submission' => date('Y-m-d'),
            'date_of_cancel' => $cancel_date,
            'reason' => $cancel_reason,
            'invoice_instruction' => 'None',
            'ro_amount' => 0,
            'cancel_ro_by_admin' => 0,
            'caption_brand_name' => $caption_brand
        );
        $this->db->trans_start();
        $inserted_id = $this->am_model->insert_into_cancel_market($user_data);
        $market_edited_pricelist = array();
        $market_cancelled = array();
        $revised_market_data = '';

        foreach ($markets_price as $market_name => $market_ro_amount) {
            $market_name = str_replace("_", " ", $market_name);
            $user_data = array(
                'cancel_id' => $inserted_id,
                'am_ro_id' => $am_ro_id,
                'market' => $market_name,
                'spot_price' => $market_ro_amount['spot'],
                'spot_fct' => $market_ro_amount['spot_fct'],
                'banner_price' => $market_ro_amount['banner'],
                'banner_fct' => $market_ro_amount['banner_fct'],
                'approved_type' => 0,
                'is_cancelled' => $market_ro_amount['is_cancelled']
            );
            if ($market_ro_amount['is_cancelled'] == 1) {
                array_push($market_cancelled, $market_name);
            }//else{
            //array_push($market_edited,$market_name) ;
            $tmp = array();
            $tmp['market_name'] = $market_name;
            $tmp['spot_price'] = $market_ro_amount['spot'];
            $tmp['banner_price'] = $market_ro_amount['banner'];

            $revised_market_data = $revised_market_data . $market_name . " : " . $market_ro_amount['spot'] . " : " . $market_ro_amount['banner'] . "<br/>";
            array_push($market_edited_pricelist, $tmp);
            // }
            $this->am_model->insert_into_tmp_table($user_data);
            // }
        }
        $this->db->trans_complete();
        //RC:Deepak - market edited and price should be part of the email sent
        //fixed above RC
        //Mail Intimation
        $ro_details = $this->am_model->ro_detail_for_ro_id($am_ro_id);
        $to = implode(",", convert_into_array($this->user_model->get_bhs(), 'user_email'));
        $cc = '';

        if ($cancel_type == 'cancel_content') {

            email_send($to,
                $cc,
                "content_cancellation_request",
                array('EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro']),
                array(
                    'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                    'INTERNAL_RO_NUMBER' => $ro_details[0]['internal_ro'],
                    'CLIENT_NAME' => $ro_details[0]['client'],
                    'AGENCY_NAME' => $ro_details[0]['agency'],
                    'CONTENT_CANCELLED' => $caption_brand . "( " . implode(",", $market_cancelled) . " )",
                    'MARKET_CANCELLED' => $revised_market_data,
                    'REASON' => $cancel_reason
                )
            );
        } else if ($cancel_type == 'cancel_brand') {
            $brandName = $this->mg_model->getBrandName($caption_brand);
            email_send($to,
                $cc,
                "brand_cancellation_request",
                array('EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro']),
                array(
                    'ACCOUNT_MANAGER_NAME' => $logged_in[0]['user_name'],
                    'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                    'INTERNAL_RO_NUMBER' => $ro_details[0]['internal_ro'],
                    'CLIENT_NAME' => $ro_details[0]['client'],
                    'AGENCY_NAME' => $ro_details[0]['agency'],
                    'BRAND_CANCELLED' => $brandName . "( " . implode(",", $market_cancelled) . " )",
                    'MARKET_CANCELLED' => $revised_market_data,
                    'REASON' => $cancel_reason
                )
            );
        }
        //redirect('ro_manager/approve/' . $order_id . '/' . $edit . '/' . $am_ro_id);
        $response['Status'] = 'success';
        $response['Message'] = 'Request Successful!!';
        $response['Data'] = array();
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function post_cancel_markets()
    {

        $this->is_logged_in();
        log_message('info', 'in account_manager@post_cancel_markets| postdata - ' . print_r($_POST, true));
        $logged_in = $this->session->userdata("logged_in_user");

        $order_id = $this->input->post('hid_order_id');
        $am_ro_id = $this->input->post('hid_id');
        $edit = $this->input->post('hid_edit');

        $cancel_date = $this->input->post('txt_cancel_date');
        $cancel_reason = $this->input->post('reason_can');
        $markets_price = $this->input->post('markets');
        $cancel_type = $this->input->post('hid_cancel_market_type');


        $user_data = array(
            'ext_ro_id' => $am_ro_id,
            'cancel_type' => $cancel_type,
            'user_id' => $logged_in[0]['user_id'],
            'date_of_submission' => date('Y-m-d'),
            'date_of_cancel' => $cancel_date,
            'reason' => $cancel_reason,
            'invoice_instruction' => 'None',
            'ro_amount' => 0,
            'cancel_ro_by_admin' => 0
        );

        $this->db->trans_start();
        $inserted_id = $this->am_model->insert_into_cancel_market($user_data);
        $market_edited_pricelist = array();
        $market_cancelled = array();

        foreach ($markets_price as $market_name => $market_ro_amount) {
            $market_name = str_replace("_", " ", $market_name);
            $user_data = array(
                'cancel_id' => $inserted_id,
                'am_ro_id' => $am_ro_id,
                'market' => $market_name,
                'spot_price' => $market_ro_amount['spot'],
                'spot_fct' => $market_ro_amount['spot_fct'],
                'banner_price' => $market_ro_amount['banner'],
                'banner_fct' => $market_ro_amount['banner_fct'],
                'approved_type' => 0,
                'is_cancelled' => $market_ro_amount['is_cancelled']
            );
            if ($market_ro_amount['is_cancelled'] == 1) {
                array_push($market_cancelled, $market_name);
            }//else{
            //array_push($market_edited,$market_name) ;
            $tmp = array();
            $tmp['market_name'] = $market_name;
            $tmp['spot_price'] = $market_ro_amount['spot'];
            $tmp['banner_price'] = $market_ro_amount['banner'];

            array_push($market_edited_pricelist, $tmp);
            // }
            $this->am_model->insert_into_tmp_table($user_data);
            // }
        }
        $this->db->trans_complete();
        //RC:Deepak - market edited and price should be part of the email sent
        //fixed above RC
        //Mail Intimation
        $ro_details = $this->am_model->ro_detail_for_ro_id($am_ro_id);
        $to = implode(",", convert_into_array($this->user_model->get_bhs(), 'user_email'));
        $cc = '';
        email_send($to,
            $cc,
            "market_cancellation_request",
            array('EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro']),
            array(
                'ACCOUNT_MANAGER_NAME' => $logged_in[0]['user_name'],
                'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                'INTERNAL_RO_NUMBER' => $ro_details[0]['internal_ro'],
                'CLIENT_NAME' => $ro_details[0]['client'],
                'AGENCY_NAME' => $ro_details[0]['agency'],
                'MARKET_CANCELLED' => implode(",", $market_cancelled),
                'MARKET_EDITED_PRICELIST' => make_market_price($market_edited_pricelist),
                'REASON' => $cancel_reason
            )
        );
//        redirect('ro_manager/approve/' . $order_id . '/' . $edit . '/' . $am_ro_id);
        $response['Status'] = 'success';
        $response['Message'] = 'Request Successful!!';
        $response['Data'] = array();
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function invoice_collection($cust_ro)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['external_ro'] = base64_decode($cust_ro);
        $model['all_invoice_for_ro'] = $this->am_model->get_all_invoice_for_ro($logged_in[0]['user_id'], $model['external_ro']);
        $model['chk_add_inv'] = $this->am_model->chk_to_add_invoice($logged_in[0]['user_id'], $model['external_ro']);
        $this->load->view('account_manager/invoice_collection', $model);
    }

    public function post_invoice_collection()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['res_invoice'] = $this->am_model->invoice_collection();
        $model['chk_add_inv'] = $this->am_model->chk_to_add_invoice($logged_in[0]['user_id'], $this->input->post('hid_ext_id'));
        //redirect('account_manager/invoice_collection');
        redirect('account_manager/invoice_collection/' . rtrim(base64_encode($this->input->post('hid_ext_id')), '='));
    }

    public function am_invoice_report()
    {
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $this->load->view('account_manager/invoice_report', $model);
    }

    public function get_invoice_report($user_id = NULL)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        if ($logged_in[0]['profile_id'] != 6) {
            $user_id = NULL;
        }
        $records = $this->am_model->get_invoice_reports($user_id, $logged_in[0]['is_test_user']);
        $this->output->set_header('Content-Type: application/json');
        $this->output->set_output($records);
    }

    public function download_invoice_report_csv($start_date, $end_date, $user_id = NULL, $report_type = NULL)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        if ($logged_in[0]['profile_id'] != 6) {
            $user_id = NULL;
        }
        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 00:00:00';
        $records = $this->am_model->download_invoice_report_csv($start_date, $end_date, $user_id, $report_type, $logged_in[0]['is_test_user']);
        $invoice_details = json_decode($records, true);//echo '<pre>';print_r($invoice_details);
        $this->load->helper('csv');
        $csv_array = array(
            array('Added By', 'Invoice Number', 'Customer RO Number', 'Internal RO Number', 'Start Date', 'End Date', 'Client Name', 'Agency Name', 'Amount Collected', 'TDS', 'Cheque Number', 'Cheque Date', 'Collection Date'));

        $invoice_count = count($invoice_details['rows']);//echo $invoice_count;exit;
        $count = count($csv_array[0]);

        for ($i = 0; $i < $invoice_count; $i++) {
            $k = $i + 1;
            for ($j = 0; $j < $count; $j++) {
                $csv_array[$k][$j] = $invoice_details['rows'][$i]['cell'][$j];
            }
        }
        //echo '<pre>';print_r($csv_array);exit;
        array_to_csv($csv_array, 'invoice_report.csv');

    }

    /* Order history recipient  */

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
        $this->load->view('account_manager/activate_client', $model);
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
        $this->load->view('account_manager/create_ext_ro', $model);
    }

    public function check_for_ro_existence()
    {
        $cust_ro = $this->input->post('ext_ro');
        $existence = $this->am_model->check_for_ro_existence($cust_ro);
        return $existence; //1 or 0
    }

    function getMailIdsUsingContactIds()
    {

        $agencyContactIds = $this->input->post('agenctContactIds');
        $clientContactIds = $this->input->post('clientContactIds');

        $agencyContactMails = $this->am_model->getAgencyContactMailIdsUsingIds($agencyContactIds);
        $clientContactMails = $this->am_model->getClientContactMailIdsUsingIds($clientContactIds);


        foreach ($agencyContactMails as $key => $value) {

            $temp = explode(",", $value["agency_email"]);

            foreach ($temp as $id) {

                $dataArray['mailId'][] = array("id" => $id);

            }
        }
        foreach ($clientContactMails as $key => $value) {

            $temp = explode(",", $value["client_email"]);

            foreach ($temp as $id) {

                $dataArray['mailId'][] = array("id" => $id);

            }
        }
        if (isset($dataArray) && !empty($dataArray)) {

            echo json_encode($dataArray);

        } else {

            echo "null";
        }
    }

    public function accountManagerTarget()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        //$profile_id = $logged_in[0]['profile_id'] ;
        $user_id = $logged_in[0]['user_id'];
        //$userType = $logged_in[0]['is_test_user'] ;

        $childUserIds = array();
        array_push($childUserIds, $user_id);

        $this->load->model('common_model');
        $childId = $this->common_model->getChildUserIds($user_id, $childUserIds);
        //echo print_r($childId,true);exit;

        $commaSeparatedBelowUserId = $this->common_model->getCommaSepartedValues($childId);
        $quaterlyValues = $this->getQuaterlyValuesForUser($commaSeparatedBelowUserId, $user_id);

        //echo print_r($quaterlyValues,true);exit;
        $monthName = $this->getMonthName();
        $monthlyTarget = $this->am_model->getMonthlyTarget($user_id, $monthName);
        $monthlyAchieved = $this->getMonthlyAchievedValues($commaSeparatedBelowUserId, $monthName);
        //echo print_r($monthlyAchieved,true);exit;

        if ($monthlyTarget == 0.00) {
            $percentageAchieved = 'NA';
        } else {
            $percentageAchieved = round(($monthlyAchieved['achievedAmount'] / $monthlyTarget) * 100, 2);
        }
        if (empty($percentageAchieved) || !isset($percentageAchieved)) {
            $percentageAchieved = 1;
        }

        $messageDisplay = $this->getAllMessages($percentageAchieved, $monthlyTarget, $commaSeparatedBelowUserId);

        $data = array();
        $data['Quaterly'] = $quaterlyValues;
        $data['target'] = $monthlyTarget;
        $data['monthlyAchieved'] = $monthlyAchieved;
        $data['percentageAchieved'] = round(($monthlyAchieved['achievedAmount'] / $monthlyTarget) * 100, 2);
        $data['message'] = $messageDisplay;
        //echo print_r($data,true);exit;
        return $data;
    }

    public function getQuaterlyValuesForUser($childIds, $parentId)
    {
        $this->load->model('common_model');
        $yearValues = $this->common_model->getYears();
        $inputValues = $this->getQuarterInputValues($yearValues);
        return $this->getQuarterOutputValues($childIds, $inputValues, $parentId);

    }

    public function getQuarterInputValues($yearValues)
    {
        $data = array();

        $from_year = $yearValues['from_year'];
        $to_year = $yearValues['to_year'];

        $data['q1_Month'] = "April,May,June";
        $data['q2_Month'] = "July,August,September";
        $data['q3_Month'] = "October,November,December";
        $data['q4_Month'] = "January,February,March";

        $data['q1_startDate'] = date("$from_year-04-01");
        $data['q1_endDate'] = date("$from_year-06-30");
        $data['q1_displayStartDate'] = date("$from_year-03-25");
        $data['q1_displayEndDate'] = date("$from_year-06-24");

        $data['q2_startDate'] = date("$from_year-07-01");
        $data['q2_endDate'] = date("$from_year-09-30");
        $data['q2_displayStartDate'] = date("$from_year-06-25");
        $data['q2_displayEndDate'] = date("$from_year-09-24");

        $data['q3_startDate'] = date("$from_year-10-01");
        $data['q3_endDate'] = date("$from_year-12-31");
        $data['q3_displayStartDate'] = date("$from_year-09-25");
        $data['q3_displayEndDate'] = date("$from_year-12-24");

        $data['q4_startDate'] = date("$to_year-01-01");
        $data['q4_endDate'] = date("$to_year-03-31");
        $data['q4_displayStartDate'] = date("$from_year-12-25");
        $data['q4_displayEndDate'] = date("$to_year-03-24");

        $data['financial_year'] = $from_year . "-" . $to_year;
        return $data;
    }

    public function getQuarterOutputValues($userIds, $inputValues, $parentId)
    {
        $data = array();
        $todaysDate = date('Y-m-d');
        $financialYear = $inputValues['financial_year'];

        if (strtotime($todaysDate) >= strtotime($inputValues['q1_displayStartDate'])) {
            if ((strtotime($todaysDate) <= strtotime($inputValues['q1_displayEndDate'])) && (strtotime($todaysDate) >= strtotime($inputValues['q1_displayStartDate']))) {
                //$q1_endDate = $todaysDate ;
                $quarterlyAchieved = $this->getAchievedAmountForUserAndDate($userIds, $inputValues['q1_startDate'], $inputValues['q1_endDate']);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUserAndMonths($parentId, $financialYear, $inputValues['q1_Month']);
                $data['current'] = round(($quarterlyAchieved / $quarterlyTarget) * 100, 2);
            } else {
                $quarterlyAchieved = $this->getAchievedAmountForUserAndDate($userIds, $inputValues['q1_startDate'], $inputValues['q1_endDate']);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUserAndMonths($parentId, $financialYear, $inputValues['q1_Month']);
                $data['Q1'] = round(($quarterlyAchieved / $quarterlyTarget) * 100, 2);
            }
        }

        if (strtotime($todaysDate) >= strtotime($inputValues['q2_displayStartDate'])) {
            if ((strtotime($todaysDate) >= strtotime($inputValues['q2_displayStartDate'])) && (strtotime($todaysDate) <= strtotime($inputValues['q2_displayEndDate']))) {
                //$q2_endDate = $todaysDate ;
                $quarterlyAchieved = $this->getAchievedAmountForUserAndDate($userIds, $inputValues['q2_startDate'], $inputValues['q2_endDate']);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUserAndMonths($parentId, $financialYear, $inputValues['q2_Month']);
                $data['current'] = round(($quarterlyAchieved / $quarterlyTarget) * 100, 2);
            } else {
                $quarterlyAchieved = $this->getAchievedAmountForUserAndDate($userIds, $inputValues['q2_startDate'], $inputValues['q2_endDate']);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUserAndMonths($parentId, $financialYear, $inputValues['q2_Month']);
                $data['Q2'] = round(($quarterlyAchieved / $quarterlyTarget) * 100, 2);
            }
        }


        if (strtotime($todaysDate) >= strtotime($inputValues['q3_displayStartDate'])) {
            if ((strtotime($todaysDate) >= strtotime($inputValues['q3_displayStartDate'])) && (strtotime($todaysDate) <= strtotime($inputValues['q3_displayEndDate']))) {
                //$q3_endDate = $todaysDate ;
                $quarterlyAchieved = $this->getAchievedAmountForUserAndDate($userIds, $inputValues['q3_startDate'], $inputValues['q3_endDate']);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUserAndMonths($parentId, $financialYear, $inputValues['q3_Month']);
                $data['current'] = round(($quarterlyAchieved / $quarterlyTarget) * 100, 2);
            } else {
                $quarterlyAchieved = $this->getAchievedAmountForUserAndDate($userIds, $inputValues['q3_startDate'], $inputValues['q3_endDate']);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUserAndMonths($parentId, $financialYear, $inputValues['q3_Month']);
                $data['Q3'] = round(($quarterlyAchieved / $quarterlyTarget) * 100, 2);
            }
        }


        if (strtotime($todaysDate) >= strtotime($inputValues['q4_displayStartDate'])) {
            if ((strtotime($todaysDate) >= strtotime($inputValues['q4_displayStartDate'])) && (strtotime($todaysDate) <= strtotime($inputValues['q4_displayEndDate']))) {
                //$q4_endDate = $todaysDate ;
                $quarterlyAchieved = $this->getAchievedAmountForUserAndDate($userIds, $inputValues['q4_startDate'], $inputValues['q4_endDate']);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUserAndMonths($parentId, $financialYear, $inputValues['q4_Month']);
                $data['current'] = round(($quarterlyAchieved / $quarterlyTarget) * 100, 2);
            } else {
                $quarterlyAchieved = $this->getAchievedAmountForUserAndDate($userIds, $inputValues['q4_startDate'], $inputValues['q4_endDate']);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUserAndMonths($parentId, $financialYear, $inputValues['q4_Month']);
                $data['Q4'] = round(($quarterlyAchieved / $quarterlyTarget) * 100, 2);
            }
        }

        return $data;
    }

    public function getAchievedAmountForUserAndDate($userId, $startDate, $endDate)
    {
        $achievedAmount = $this->am_model->getAchievedAmountForUserAndDate($userId, $startDate, $endDate);
        return $achievedAmount;
    }

    public function getMonthName()
    {
        $date = date("d");

        if ($date < 25) {
            $monthName = date("F");
        } else {
            $nextMonth = date("m") + 1;
            $fromYear = date("Y");

            if ($nextMonth > 12) {
                $nextMonth = $nextMonth - 12;
                $fromYear = $fromYear + 1;
            }

            $nextMonthDate = date("$fromYear-$nextMonth-01");
            $monthName = date("F", strtotime($nextMonthDate));
        }
        return $monthName;
    }

    public function getMonthlyAchievedValues($userId, $monthName)
    {

        $toMonth = date("m");

        if (date("m") == 12 && date("d") >= 25) {
            $fromYear = date("Y") + 1;
            $toYear = date("Y") + 1;
        } else {
            $fromYear = date("Y");
            $toYear = date("Y");
        }

        $date = date("d");

        if ($date < 25) {
            $fromMonth = date("m");
            $toMonth = date("m");
        } else {
            $fromMonth = date("m") + 1;
            $toMonth = date("m") + 1;
        }

        $startDate = date("$fromYear-$fromMonth-01");
        $endDate = date("$toYear-$toMonth-t");
        $ros = $this->am_model->getApprovedRo($userId, $startDate, $endDate);

        $data = array();
        if (count($ros) > 0) {
            $data['no_of_ro'] = count($ros);
            $achievedAmount = $this->am_model->getAchievedROAmount($ros, $monthName, $startDate, $endDate);
            $data['achievedAmount'] = round($achievedAmount / 100000, 2);
        } else {
            $data['no_of_ro'] = 0;
            $data['achievedAmount'] = 0;
        }
        return $data;
    }

    public function getAllMessages($percentageAchieved, $monthlyTarget, $userId)
    {
        if ($percentageAchieved == 'NA') {
            return 'NA';
        } else {
            $incentiveGoal = round($monthlyTarget * 75 / 100, 2);


            if (date("m") == 12 && date("d") >= 25) {
                $fromYear = date("Y");
                $toYear = date("Y") + 1;
            } else {
                $fromYear = date("Y");
                $toYear = date("Y");
            }

            $date = date("d");

            if ($date < 25) {
                $fromMonth = date("m");
                $toMonth = date("m");

                $displayFromMonth = date("m") - 1;
                $displayToMonth = date("m");
            } else {
                $fromMonth = date("m") + 1;
                $toMonth = date("m") + 1;

                $displayFromMonth = date("m");
                $displayToMonth = date("m") + 1;
            }

            if ($fromMonth > 12) {
                $fromMonth = $fromMonth - 12;
            }
            if ($toMonth > 12) {
                $toMonth = $toMonth - 12;
            }
            if ($displayFromMonth > 12) {
                $displayFromMonth = $displayFromMonth - 12;
            }
            if ($displayToMonth > 12) {
                $displayToMonth = $displayToMonth - 12;
            }

            $startDate = date("$fromYear-$fromMonth-01");
            $endDate = date("$fromYear-$fromMonth-t");

            $ros = $this->am_model->getApprovedRo($userId, $startDate, $endDate);
            $number_of_rows = count($ros);
            if ($number_of_rows >= 1) {
                $gotRo = True;
            } else {
                $gotRo = False;
            }

            $todayDate = date('Y-m-d');

            $startingDate = date("$fromYear-$displayFromMonth-25");
            $TillFifthDate = date("$toYear-$displayToMonth-05");

            $remainingNextFifth = (strtotime($TillFifthDate) + 86400 - strtotime($todayDate)) / 86400;
            //echo $todayDate."--".$startingDate."--".$TillFifthDate ;

            if ((strtotime($startingDate) <= strtotime($todayDate)) && (strtotime($TillFifthDate) >= strtotime($todayDate))) {
                if ($percentageAchieved >= 75 && $percentageAchieved < 100) {
                    return 'Kudos! You have done it! You just earned INR 10,000 as incentive';
                } else if ($percentageAchieved >= 100) {
                    return 'Kudos! You have done it! You just earned INR 10,000 as incentive';
                } else {
                    if ($gotRo) {
                        return 'Current goal: 10k as incentive <br/> Target amount ' . $incentiveGoal . 'L in ' . $remainingNextFifth . " days <br/>" . " Great job! You are on track! You now have clocked " . $number_of_rows . "  ROs this month. All the best!";
                    } else {
                        return 'Current goal: 10k as incentive <br/> Target amount ' . $incentiveGoal . 'L in ' . $remainingNextFifth . " days <br/>" . " All the best!";
                    }
                }
            }

            $stratingFromSixth = date("$toYear-$displayFromMonth-06");
            $TillTwentyFourthDate = date("$toYear-$displayToMonth-24");
            $remainingNextTwentyFourth = (strtotime($TillTwentyFourthDate) + 86400 - strtotime($todayDate)) / 86400;

            if ((strtotime($stratingFromSixth) <= strtotime($todayDate)) && (strtotime($TillTwentyFourthDate) >= strtotime($todayDate))) {
                if ($percentageAchieved >= 75 && $percentageAchieved < 100) {
                    return "Great job! You now have clocked " . $number_of_rows . " ROs this month. All the best!";
                } elseif ($percentageAchieved >= 100) {
                    return 'Kudos! You have hit your target of ' . round($monthlyTarget, 2) . 'L this month! Great job!';
                } else {
                    if ($gotRo) {
                        return 'Target amount ' . round($monthlyTarget, 2) . 'L in ' . $remainingNextTwentyFourth . " days <br/>" . " Great job! You now have clocked " . $number_of_rows . " ROs this month. All the best!";
                    } else {
                        return 'Target amount ' . round($monthlyTarget, 2) . 'L in ' . $remainingNextTwentyFourth . " days  <br/>" . " All the best!";
                    }
                }
            }
        }
    }

    public function accountManagersInfo()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $userId = $logged_in[0]['user_id'];
        $todaysDate = date("Y-m-d");

        $month = date("m");
        $monthName = date("F");
        //$date = date("d") ;


        $fromMonth = $month - 1;


        $year = date("Y");
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }

        $financialYear = $from_year . '-' . $to_year;
        $fromDate = date("$from_year-$fromMonth-25");
        $toDate = date("$from_year-$month-24");
        $targetAchievedDate = date("$from_year-$month-05");


        $numberOfDays = abs((strtotime($fromDate) - strtotime($todaysDate)) / 86400);

        $showAchievedMessage = FALSE;
        if (strtotime($todaysDate) <= strtotime($targetAchievedDate)) {
            $showAchievedMessage = TRUE;
        }

        //$monthlyData : - Get Monthly number of ro and and total amount
        $monthlyData = $this->am_model->getRoDetailsForAccountManagersForRoDate($userId, $fromDate, $toDate);
        $forIncentiveData = $this->am_model->getRoDetailsForAccountManagersForRoDate($userId, $fromDate, $targetAchievedDate);
        $amountRaised = $forIncentiveData[0]['total_ro_amount'];

        //$userMonthlyTarget : - MOnthly User Target
        $userMonthlyTarget = $this->am_model->getMonthlyTargetForUser($userId, $financialYear, $monthName);
        $targetAmount = $userMonthlyTarget[0]['target'];

        $targetAchieved = FALSE;
        if (round($targetAmount * 75 / 100, 2) >= $amountRaised) {
            $targetAchieved = TRUE;
        }

        $showMessageBox = 0;
        if ($showAchievedMessage || $targetAchieved) {
            $showMessageBox = 1;
        }

        /*$quaterlyArray = $this->getQuarterMonth() ;
            foreach($quaterlyArray as $value){
                foreach($value as $quarter=>$val) {
                    $startDate = $val['startDate'] ;
                    $endDate = $val['endDate'] ;
                    $monthsName = explode(",",$val['monthsName']) ;

                    $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDate($userId,$fromDate,$toDate) ;
                    $quarterlyTarget = $this->am_model->getMonthlyTargetForUser($userId,$financialYear,$monthName) ;
                }
            } */


        $data = array();
        if ($showMessageBox) {
            $data['incentive'] = '50K';
            $data['number_of_days'] = $numberOfDays;
        }

        $q1_Month = "April','May','June";
        $q2_Month = "July','August','September";
        $q3_Month = "October','Number','December";
        $q4_Month = "January','February','March";

        $q1_startDate = date("Y-04-01");
        $q1_endDate = date("Y-06-30");

        $q2_startDate = date("Y-07-01");
        $q2_endDate = date("Y-09-30");

        $q3_startDate = date("Y-10-01");
        $q3_endDate = date("Y-12-31");

        $q4_startDate = date("2016-01-01");
        $q4_endDate = date("2016-03-31");

        if (strtotime($todaysDate) < strtotime($q1_endDate)) {
            $q1_endDate = $todaysDate;
            $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDateQuaterly($userId, $q1_startDate, $q1_endDate);
            $quarterlyTarget = $this->am_model->getQuarterlyTargetForUser($userId, $financialYear, $q1_Month);
            $data['current'] = round(($quarterlyAchieved[0]['total_ro_amount'] / $quarterlyTarget[0]['target']) * 100, 2);
        } else {
            $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDateQuaterly($userId, $q1_startDate, $q1_endDate);
            $quarterlyTarget = $this->am_model->getQuarterlyTargetForUser($userId, $financialYear, $q1_Month);
            $data['Q1'] = round(($quarterlyAchieved[0]['total_ro_amount'] / $quarterlyTarget[0]['target']) * 100, 2);
        }

        if (strtotime($todaysDate) > strtotime($q2_startDate)) {
            if (strtotime($todaysDate) < strtotime($q2_endDate)) {
                $q2_endDate = $todaysDate;
                $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDateQuaterly($userId, $q2_startDate, $q2_endDate);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUser($userId, $financialYear, $q2_Month);
                $data['current'] = round(($quarterlyAchieved[0]['total_ro_amount'] / $quarterlyTarget[0]['target']) * 100, 2);
            } else {
                $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDateQuaterly($userId, $q2_startDate, $q2_endDate);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUser($userId, $financialYear, $q2_Month);
                $data['Q2'] = round(($quarterlyAchieved[0]['total_ro_amount'] / $quarterlyTarget[0]['target']) * 100, 2);
            }
        }

        if (strtotime($todaysDate) > strtotime($q3_startDate)) {
            if (strtotime($todaysDate) < strtotime($q3_endDate)) {
                $q3_endDate = $todaysDate;
                $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDateQuaterly($userId, $q3_startDate, $q3_endDate);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUser($userId, $financialYear, $q3_Month);
                $data['current'] = round(($quarterlyAchieved[0]['total_ro_amount'] / $quarterlyTarget[0]['target']) * 100, 2);
            } else {
                $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDateQuaterly($userId, $q3_startDate, $q3_endDate);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUser($userId, $financialYear, $q3_Month);
                $data['Q3'] = round(($quarterlyAchieved[0]['total_ro_amount'] / $quarterlyTarget[0]['target']) * 100, 2);
            }
        }

        if (strtotime($todaysDate) > strtotime($q4_startDate)) {
            if (strtotime($todaysDate) < strtotime($q4_endDate)) {
                $q4_endDate = $todaysDate;
                $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDateQuaterly($userId, $q4_startDate, $q4_endDate);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUser($userId, $financialYear, $q4_Month);
                $data['current'] = round(($quarterlyAchieved[0]['total_ro_amount'] / $quarterlyTarget[0]['target']) * 100, 2);
            } else {
                $quarterlyAchieved = $this->am_model->getRoDetailsForAccountManagersForRoDateQuaterly($userId, $q4_startDate, $q4_endDate);
                $quarterlyTarget = $this->am_model->getQuarterlyTargetForUser($userId, $financialYear, $q4_Month);
                $data['Q4'] = round(($quarterlyAchieved[0]['total_ro_amount'] / $quarterlyTarget[0]['target']) * 100, 2);
            }
        }

        $data['userAchievedData'] = $monthlyData;
        $data['number_of_days'] = $numberOfDays;
        $data['userMonthlyTarget'] = $userMonthlyTarget;
        $data['targetAchieved'] = $showMessageBox;
        //echo print_r($data) ;exit;
        return $data;
    }

    public function getQuarterMonth()
    {
        $monthArray = array();
        $returnArray = array();
        for ($i = 1; $i <= 12; $i++) {

            array_push($monthArray, date("F", mktime(0, 0, 0, $i + 3, 1)));

            $startDate = date("Y-m-d", mktime(0, 0, 0, $i + 3, 1));
            $endDate = date("Y-m-t", mktime(0, 0, 0, $i + 3, 1));

            if (($i % 3) == 0) {

                $tmp = array(
                    "startDate" => $startDate,
                    "EndDate" => $endDate,
                    "NextMonths" => implode(",", $monthArray)
                );

                array_push($returnArray, $tmp);
                $monthArray = array();

            }

        }

//            echo '<pre>' ;
        print_r($returnArray);
        exit;
    }

    public function getMultipleFileUpload()
    {
        $this->load->view('account_manager/file_upload_test');
    }

    public function multipleFileUpload()
    {
        //$this->multipleFileUploadForRoTest() ;
        $this->fileUploadTest();
    }

    public function fileUploadTest()
    {
        $file_name = $_FILES['file_pdf']['name'];
        $config['upload_path'] = 'easy_ro_temp_pdf/';
        $config['allowed_types'] = '*';
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file_pdf') == False) {
            $error = array('error' => $this->upload->display_errors());
            echo $this->upload->display_errors();
            exit;
            $this->load->view('account_manager/file_upload_test', $error);
        } else {
            $this->upload->data();
        }
        //$this->uploadOntoS3(NULL,$file_name) ;
    }

    public function fileUploadForRoTest()
    {
        if (!is_dir('easy_ro_temp_pdf')) {
            mkdir('easy_ro_temp_pdf');
        }
        $files = $_FILES;
        $count = count($_FILES['file_pdf']['name']);

        for ($i = 0; $i < $count; $i++) {
            $_FILES['file_pdf']['name'] = date('Y_m_d_H_i_s') . "_" . $files['file_pdf']['name'][$i];
            $_FILES['file_pdf']['type'] = $files['file_pdf']['type'][$i];
            $_FILES['file_pdf']['tmp_name'] = $files['file_pdf']['tmp_name'][$i];
            $_FILES['file_pdf']['error'] = $files['file_pdf']['error'][$i];
            $_FILES['file_pdf']['size'] = $files['file_pdf']['size'][$i];

            //$file_pdf = $_FILES['file_pdf']['name'];
            $config['upload_path'] = 'easy_ro_temp_pdf/';
            $config['allowed_types'] = '*';
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file_pdf') == False) {
                $error = array('error' => $this->upload->display_errors());
                echo $this->upload->display_errors();
                exit;
                $this->load->view('account_manager/file_upload_test', $error);
            } else {
                $this->upload->data();
            }

        }
    }

    public function addGst()
    {
        $gstValue = $this->input->post('gst');
        $isClientOrAgency = $this->input->post('isClientOrAgency');
        $name = $this->input->post('name');
        if ($isClientOrAgency === 'agency') {
            //	echo $gstValue."-".$isClientOrAgency."-".$name.'--agency';exit;
            $affected = $this->am_model->updateGSTForAgency($gstValue, $name);
        } else {
            //	echo $gstValue."-".$isClientOrAgency."-".$name.'--adv';exit;
            $affected = $this->am_model->updateGSTForAdvertiser($gstValue, $name);
        }
        if ($affected > 0) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo json_encode(array('status' => 'failed'));
        }

    }

    public function getGst()
    {
        $isClientOrAgency = $this->input->post('isClientOrAgency');
        $name = $this->input->post('name');
        if ($isClientOrAgency === 'agency') {
            //      echo $gstValue."-".$isClientOrAgency."-".$name.'--agency';exit;
            $retVal = $this->am_model->getGSTForAgency($name);
        } else {
            //      echo $gstValue."-".$isClientOrAgency."-".$name.'--adv';exit;
            $retVal = $this->am_model->getGSTForAdvertiser($name);
        }
        if (count($retVal) > 0) {
            echo json_encode(array('status' => 'success', 'gst' => $retVal['gst']));
        } else {
            echo json_encode(array('status' => 'failed', 'gst' => ''));
        }

    }

    public function getMenu($profileId)
    {
        log_message('DEBUG', 'In getMenu | Trying to fetch from session for profile id = ' . $profileId);
        $menu = $this->session->userdata('menu');
        if ($menu == NULL) {
            log_message('DEBUG', 'In getMenu | Menu is not set in session. Fetching menu from database for profile id = ' . $profileId);
            $menu = new MenuFeature();
            $menu = $menu->getHeaderMenu($profileId);
            log_message('DEBUG', 'In getMenu | Fetching menu from database successful for profile id = ' . $profileId);
        }
        log_message('DEBUG', 'In getMenu | Menu successfully fetched for profile id = ' . $profileId);
        return $menu;
    }

    public function validateRoAttachment($str)
    {
        log_message('INFO', ' Files are :- ' . print_r($_FILES, True));

        $allowed_mime_type_arr = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/excel', 'application/vnd.ms-excel', 'application/msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/x-zip', 'application/zip', 'application/x-rar-compressed',
            'application/x-zip-compressed');
        $mime = get_mime_by_extension($_FILES['file_pdf']['name']);
        log_message('INFO', ' Mime are :- ' . print_r($mime, True));

        if (isset($_FILES['file_pdf']['name']) && $_FILES['file_pdf']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                set_form_validation_error_messages(array('rule' => 'validateRoAttachment', 'errorMessage' => 'Please select only pdf/doc/docx/xls/xlsx/zip/rar file.'));
                return false;
            }
        } else {
            set_form_validation_error_messages(array('rule' => 'validateRoAttachment', 'errorMessage' => 'Please choose a file to upload.'));
            return false;
        }
    }

    public function validateMailAttachment($str)
    {
        log_message('INFO', ' Files are :- ' . print_r($_FILES, True));

        $allowed_mime_type_arr = array('message/rfc822', 'application/vnd.ms-outlook');
        $mime = get_mime_by_extension($_FILES['client_aproval_mail']['name']);
        log_message('INFO', ' Mime are :- ' . print_r($mime, True));

        if (isset($_FILES['client_aproval_mail']['name']) && $_FILES['client_aproval_mail']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                set_form_validation_error_messages(array('rule' => 'validateMailAttachment', 'errorMessage' => 'Please select only eml/msg file.'));
                return false;
            }
        } else {
            set_form_validation_error_messages(array('rule' => 'validateMailAttachment', 'errorMessage' => 'Please choose a file to upload.'));
            return false;
        }
    }

    private function isUserloggedIn()
    {
        $logged_in_user = $this->session->userdata("logged_in_user");
        $logged_in_user = $logged_in_user[0];
        if (!isset($logged_in_user) || empty($logged_in_user)) {
            return false;
        } else {
            return true;
        }
    }
}
