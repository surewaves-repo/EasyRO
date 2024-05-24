<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Update extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('ro_model');
        $this->load->model('am_model');
        $this->load->model('mg_model');
        $this->load->model("menu_model");
        $this->load->model("invoice_model");
        $this->load->config('form_validation');
        $this->load->library('form_validation');
        $this->load->helper('generic');

        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    public function index($start_index = 0)
    {
        $model = array();
        //Checking Log-In details
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];

        $customer_detail = $this->user_model->get_all_customer_detail();
        //$filtered_customer_detail = $customer_detail;
        //$filtered_customer_detail = array_splice($filtered_customer_detail, $start_index, ITEM_PER_PAGE_USERS);

        $menu = $this->menu_model->get_header_menu();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $model['customer_detail'] = $customer_detail;
        //$model['filtered_customer_detail'] = $filtered_customer_detail;

        //$model['page_links'] = create_page_links (base_url()."update/index", ITEM_PER_PAGE_USERS, count($customer_detail));
        //$model['start_index'] = $start_index;
        $this->load->view('update_entry', $model);

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

    public function home($value = 0)
    {
        $model = array();
        $customer_detail = $this->user_model->get_all_customer_detail();

        $model['customer_detail'] = $customer_detail;
        $model['incorrect_email'] = $value;

        $this->load->view('update_entry', $model);

    }

    public function update_billing_name()
    {
        /*$error_in_validation=set_form_validation($this->config->item('billing_name'));

        if($error_in_validation){
            show_form_validation_error('update/index');
        }else { */
        $customer_id = $this->input->post('cid');
        $user_data = array(
            'billing_name' => $this->input->post('billing_name')
        );
        $this->ro_model->update_billing_name($customer_id, $user_data);
        header("Location: index");
        //}


    }

    public function update_customer_email()
    {
        /*$error_in_validation=$this->set_form_validation($this->config->item('update_email_form'));

        if($error_in_validation){
            $this->show_form_validation_error('update/home');
        }else { */
        $customer_id = $this->input->post('cid');
        $user_data = array(
            'customer_email' => $this->input->post('customer_email')
        );
        $this->ro_model->update_customer_email($customer_id, $user_data);
        header("Location: index");
        //}


    }

    public function get_billing_name()
    {
        $cid = $this->input->post('cid');
        $model = array();
        $model['billing_name'] = $this->user_model->get_billing_name($cid);
        echo $model['billing_name'];
    }

    public function get_customer_email()
    {
        $cid = $this->input->post('cid');
        $model = array();
        $model['customer_email'] = $this->user_model->get_customer_email($cid);
        echo $model['customer_email'];
    }

    public function set_form_validation($rules)
    {
        $this->load->library('form_validation');
        foreach ($rules as $rule) {
            $this->form_validation->set_rules($rule['key'], $rule['value'], $rule['rule']);
        }

        if ($this->form_validation->run() == FALSE) {
            return true;
        } else {
            return false;
        }
    }

    //http://localhost/surewaves_easy_ro/update/index

    public function show_form_validation_error($url, $model = null)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<span style="color:#990000; font-weight:normal; font-size:10px;">', '</span>');
        if (!isset($model) || empty($model)) {
            //$this->load->view( $url );
            header("Location: update/home/incorrect");
        } else {
            //$this->load->view( $url, $model );
            header("Location: update/home");
        }
    }

    public function update_market_price()
    {
        //$this->is_logged_in_tripur();
        $this->is_logged_as_coo();

        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;

        $date = "2015-04-01";
        $ro_list = $this->ro_model->get_ros($date);//print_r($ro_list);exit;
        $model['ro_list'] = $ro_list;
        $this->load->view('update_market_price', $model);
    }

    public function is_logged_as_coo()
    {
        $logged_in_user = $this->session->userdata("logged_in_user");

        if ($logged_in_user[0]['profile_id'] != 1) {
            echo '<script language="javascript">alert("Invalid Login");</script>';
            $this->load - view("/ro_manager/index");
        }
    }

    public function get_ro_data()
    {
        $model = array();

        $ro_data = $this->ro_model->ro_details($_POST['id']);
        $markets = explode(',', $ro_data[0]['market']);
        $campaign_start_date = $ro_data[0]['camp_start_date'];
        if (date("m") != date("m", strtotime($campaign_start_date))) {
            $not_to_allow_update_data = 1;
        } else {
            $not_to_allow_update_data = 0;
        }
        $html = $ro_data[0]['gross'] . '~' . $ro_data[0]['agency_com'] . '~' . '<table>';
        foreach ($markets as $market) {

            $market_price = $this->ro_model->get_market_price($_POST['id'], $market);
            if (isset($market_price[0]['spot_price'])) {
                $spot_price = $market_price[0]['spot_price'];
            } else {
                $spot_price = "0.00";
            }
            if (isset($market_price[0]['banner_price'])) {
                $banner_price = $market_price[0]['banner_price'];
            } else {
                $banner_price = "0.00";
            }
            $market = str_replace(" ", "_", $market);
            $html .= '  <tr><td>' . $market . ':</td>
                            <td>Spot</td><td><input class="text amount" style="width:100px;" type="text" name="markets[' . $market . '][spot]" value="' . $spot_price . '" onblur="javascript:recalculate_gross_amount()" />
                            <td>Banner</td><td><input class="text amount" style="width:100px;" type="text" name="markets[' . $market . '][banner]" value="' . $banner_price . '" onblur="javascript:recalculate_gross_amount()" />
                        </td></tr>';
        }
        $html .= '</table>';
        $html .= '~' . $not_to_allow_update_data;
        echo $html;
    }

    //function to check whether user is already logged in or not

    public function post_update_market_price()
    {
        $this->is_logged_as_coo();
        $this->ro_model->update_market_price();

        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;

        $date = "2014-04-01";
        $ro_list = $this->ro_model->get_ros($date);//print_r($ro_list);exit;
        $model['ro_list'] = $ro_list;
        $this->load->view('update_market_price', $model);
    }

    public function get_invoice_status_for_ro()
    {
        $ro_id = $this->input->post('ro_id');
        $data = array('ro_id' => $ro_id);
        $invoice_status = $this->invoice_model->getInvoiceGeneration($data);
        echo json_encode($invoice_status[0]['is_generated']);
    }

    public function is_logged_in_tripur()
    {
        $logged_in_user = $this->session->userdata("logged_in_user");

        if ($logged_in_user[0]['user_email'] != 'coo@surewaves.com') {
            echo '<script language="javascript">alert("Invalid Login");</script>';
            $this->load - view("/ro_manager/index");
        }
    }
}

?>