<?php if (!defined('BASEPATH')) exit("NO Direct Script Access Allowed");
ob_start();

use application\feature_dal\CreateExtRoFeature;
use application\feature_dal\MenuFeature;
use application\feature_dal\UserLoginFeature;
use application\services\common_services\EmailService;
use application\services\feature_services\RoApprovalService;
use application\services\feature_services\ProcessEditedNetworkService;
use application\services\feature_services\postApprovalRoService;

include_once APPPATH . 'feature_dal/menu_feature.php';
include_once APPPATH . 'feature_dal/user_login_feature.php';
include_once APPPATH . 'feature_dal/create_ext_ro_feature.php';
include_once APPPATH . 'services/common_services/email_service.php';
include_once APPPATH . 'services/feature_services/ro_approval_service.php';
include_once APPPATH . 'services/feature_services/process_edited_network_service.php';
include_once APPPATH . 'services/feature_services/post_approval_ro_service.php';

class RO_manager extends CI_Controller
{
    private $createExtRoFeatureObj;
    private $roApprovalServiceObj;
    private $processEditedNetworkServiceObj;
    private $postApprovalRoServiceObj;
    public $memcache;

    public function __construct()
    {
        parent::__construct();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        $this->load->model('user_model');
        $this->load->model('ro_model');
        $this->load->model('mg_model');
        $this->load->model('am_model');
        $this->load->library('session');
        $this->load->library('curl');
        $this->load->config('form_validation');
        $this->load->library('form_validation');
        $this->load->helper("app_validation_helper");
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->helper('url');
        $this->load->model("menu_model");

        $this->createExtRoFeatureObj = new CreateExtRoFeature();
        $this->roApprovalServiceObj = new RoApprovalService();
        $this->processEditedNetworkServiceObj = new ProcessEditedNetworkService();
        $this->postApprovalRoServiceObj = new postApprovalRoService();
        $this->memcache = new Memcached;
        $this->memcache->addServer("127.0.0.1", 11211);

        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    //function to check whether user is already logged in or not

    public function index()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];

        if (isset($logged_in[0])) {
            switch ($profile_id) {
                case "6":
                    redirect("account_manager/home");
                    break;
                case "7":
                    redirect("ro_manager/approved_ros");
                    break;
                case "8":
                    redirect("network_svc_manager/channel_status");
                    break;
                case "9":
                    redirect("network_svc_manager/channel_rates");
                    break;
                default:
                    redirect("account_manager/home");
            }
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    public function home($start_index = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        //Bug#422 to make sure there is no differences in RO statuses
        //ReviewComment(kiran):: Please format the code
        //ReviewComment(kiran):: Please move the loop one level up
        $list1 = $this->ro_model->get_campaigns($logged_in[0]['profile_id']);
        $list2 = $this->ro_model->get_campaigns_approved($logged_in[0]['profile_id']);
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
        $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);//print_r($ro_lists);exit;
        $model['ro_list'] = $filtered_ros;
        $model['page_links'] = create_page_links(base_url() . "/ro_manager/home", ITEM_PER_PAGE_USERS, $total_rows);
        $this->load->view('ro_manager/home', $model);
    }

    //function to redirect based on the user logged in status

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

    //function to show all the internal_ro_numbers in the home page

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

            $key_channel = 'add_cancel_channel_' . $user_id . "_" . $order_id;
            $key_posted = "loadSave_" . $user_id . "_" . $order_id;

            $this->delete_from_cache($key_channel);
            $this->delete_from_cache($key_posted);

            $this->ro_model->delete_from_confirmation($order_id_ser);
            $this->ro_model->delete_from_confirmation_customer($confirmation_id, $user_id);
        }
    }

    //Checks whether user is valid user or not based on that it will redirect

    public function get_user_id()
    {
        $logged_in_user = $this->session->userdata("logged_in_user");
        return $logged_in_user[0]['user_id'];

    }

    public function delete_from_cache($key)
    {
        $this->memcache->delete($key);
    }

    public function user_details()
    {
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $profile_details = (new UserLoginFeature())->getProfileDetails($profile_id);

//        $profile_details = $this->user_model->get_profile_details($profile_id);
        //$menu = array('Home'=>'/account_manager/home','Approved RO'=>'/ro_manager/approved_ros','User Details'=>'/ro_manager/user_details','Reports'=>array('Reports'=>'/ro_manager/network_ro_report','child'=>array('External RO Report'=>'/ro_manager/external_ro_report','Network RO Report'=>'/ro_manager/network_ro_report','Collection Report'=>'/account_manager/am_invoice_report','N/w Remittance'=>'/ro_manager/network_remittance_report'))) ;
        //$menu = array('Home'=>'/account_manager/home','Approved RO'=>'/ro_manager/approved_ros','User Details'=>'/ro_manager/user_details','Reports'=>array('External RO Report'=>'/ro_manager/external_ro_report','Network RO Report'=>'/ro_manager/network_ro_report','Collection Report'=>'/account_manager/am_invoice_report','N/w Remittance'=>'/ro_manager/network_remittance_report')) ;
        //$menu = array('Home'=>'/account_manager/home','Approved RO'=>'/ro_manager/approved_ros','User Details'=>array('User Details'=>'/ro_manager/network_ro_report','child'=>array('External RO Report'=>'/ro_manager/external_ro_report','Network RO Report'=>'/ro_manager/network_ro_report','Collection Report'=>'/account_manager/am_invoice_report','N/w Remittance'=>'/ro_manager/network_remittance_report')),'Reports'=>'/ro_manager/user_details') ;

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_details'] = $profile_details[0];
        $model['menu'] = $menu;
        $this->load->view('ro_manager/user_details', $model);
    }

    public function post_login()
    {
        $error_in_validation = set_form_validation($this->config->item('login_form'));

        if ($error_in_validation) {
            show_form_validation_error('ro_manager/login');
        } else {
            $email = $this->input->post('email');
            $passwd = $this->input->post('passwd');
            $ret = $this->user_model->user_login($email, $passwd);

            if ($ret == NULL) {

                $model = array();
                $model['error_msg'] = $this->config->item('login_form_error_code_1');;
                $this->load->view('ro_manager/login', $model);
            } else {
                if ($ret[0]['reset_password'] == true) {
                    $this->session->set_userdata('selected_user_id', $ret[0]['user_id']);
                }
                //echo '<pre>';print_r($ret);exit;
                if ($ret[0]['profile_id'] == 6) {
                    redirect("account_manager/home");
                } else if ($ret[0]['profile_id'] == 7) {
                    redirect("ro_manager/approved_ros");
                } else if ($ret[0]['profile_id'] == 8) {
                    redirect("network_svc_manager/channel_status");
                } else if ($ret[0]['profile_id'] == 9) {
                    redirect("network_svc_manager/channel_rates");
                } else if ($ret[0]['profile_id'] == 13) {
                    redirect("strategy_manager/home");
                } else {
                    redirect("account_manager/home");
                }
                //redirect("ro_manager/home");

            }

        }

    }

    public function pending_requests($start_index = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];

        $ros_with_pending_requests = $this->ro_model->ros_with_pending_requests();
        log_message('info','in ro_manager@pending_requests | ros_with_pending_requests '.print_r($ros_with_pending_requests,true));
        $total_rows = count($ros_with_pending_requests);

        $ros_with_pending_requests = $this->addCancelMarketInPendingRequest($ros_with_pending_requests);
        log_message('info','in ro_manager@pending_requests | ros_with_pending_requests after adding cancel market id '.print_r($ros_with_pending_requests,true));
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;

        $filtered_pending_requests = $ros_with_pending_requests;
        $filtered_pending_requests = array_splice($filtered_pending_requests, $start_index, ITEM_PER_PAGE_USERS);
        log_message('info','in ro_manager@pending_requests | $filtered_pending_requests '.print_r($filtered_pending_requests,true));
        $model['pending_requests'] = $filtered_pending_requests;
        $model['start_index'] = $start_index;

        //$model['pending_requests'] = $ros_with_pending_requests;
        $model['page_links'] = create_page_links(base_url() . "/ro_manager/pending_requests", ITEM_PER_PAGE_USERS, $total_rows);
        log_message('info','in ro_manager@pending_requests | $model '.print_r($model,true));
        $this->load->view('ro_manager/pending_cancellation_requests', $model);
    }

    public function addCancelMarketInPendingRequest($ros_with_pending_requests)
    {
        $data = array();
        foreach ($ros_with_pending_requests as $value) {

            //to find whether a market cancellation for an RO is requested : Nitish
            $cancel_market_request_sent = $this->am_model->is_cancel_request_sent_by_am(array('ext_ro_id' => $value['ext_ro_id'], 'cancel_type' => 'cancel_market', 'cancel_ro_by_admin' => 0));

            if (count($cancel_market_request_sent) > 0) {
                $is_cancel_market_requested = 1;
            } else {
                $is_cancel_market_requested = 0;
            }

            $tmp = array();
            $tmp['id'] = $value['id'];
            $tmp['ext_ro_id'] = $value['ext_ro_id'];
            $tmp['cancel_type'] = $value['cancel_type'];
            $tmp['reason'] = $value['reason'];
            $tmp['cust_ro'] = $value['cust_ro'];
            $tmp['internal_ro'] = $value['internal_ro'];
            $tmp['cancel_status'] = $value['cancel_status'];
            $tmp['non_fct'] = $value['non_fct'];
            if (isset($value['market']) && $value['market'] != NULL) {
                $tmp['market'] = $value['market'];

            }
            $tmp['requested_date'] = $value['requested_date'];
            $tmp['show_approve'] = $value['show_approve'];
            $tmp['is_cancel_market_requested'] = $is_cancel_market_requested;
            array_push($data, $tmp);
        }
        return $data;
    }

    public function actioned_requests($start_index = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$profile_id];

        $ros_with_actioned_requests = $this->ro_model->ros_with_actioned_requests();
        $total_rows = count($ros_with_actioned_requests);
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['positionLevelForUser'] = $positionLevelForUser;
        $model['menu'] = $menu;

        $filtered_actioned_requests = $ros_with_actioned_requests;
        $filtered_actioned_requests = array_splice($filtered_actioned_requests, $start_index, ITEM_PER_PAGE_USERS);
        $model['actioned_requests'] = $filtered_actioned_requests;
        $model['start_index'] = $start_index;

        //$model['pending_requests'] = $ros_with_pending_requests;
        $model['page_links'] = create_page_links(base_url() . "/ro_manager/actioned_requests", ITEM_PER_PAGE_USERS, $total_rows);
        $this->load->view('ro_manager/actioned_cancellation_requests', $model);
    }

    public function approve_request_for_cancellation($am_ro_id, $status, $cancel_type, $cancel_id)
    {
        $this->is_logged_in();
        log_message('info','in ro_manager@approve_request_for_cancellation | entered');
        $this->db->trans_start();
        $this->ro_model->approve_request_for_cancellation($am_ro_id, $status, $cancel_type, $cancel_id);

        //change status of ro by previos status
        if ($cancel_type == 'cancel_ro' && $status == 2) {
            $this->am_model->change_ro_status($am_ro_id);
        }

        $where_data = array('am_ro_id' => $am_ro_id, 'cancel_id' => $cancel_id);
        $user_data = array('approved_type' => $status);

        //$status=1 for approval;$status=2 for rejection

        $cancel_types_to_action = array('cancel_market', 'cancel_brand', 'cancel_content');

        //Market Cancelletion Approved
        if (in_array($cancel_type, $cancel_types_to_action) && $status == 1) {
            if ($cancel_type == 'cancel_market') {
                $this->am_model->update_tmp_market($user_data, $where_data);
                $this->approved_cancel_market($am_ro_id, $cancel_id);
            } else if ($cancel_type == 'cancel_brand' || $cancel_type == 'cancel_content') {
                $this->am_model->update_tmp_market($user_data, $where_data);
                $this->approved_cancel_content_brand($am_ro_id, $cancel_id);
            }

        }

        //Market Cancelletion Rejected
        if (in_array($cancel_type, $cancel_types_to_action) && $status == 2) {
            $this->am_model->update_tmp_market($user_data, $where_data);
        }
        $this->db->trans_complete();
        redirect("/ro_manager/pending_requests");
    }

    public function approved_cancel_market($ro_id, $cancel_id)
    {
        $this->is_logged_in();
        $user_id = $this->get_user_id();

        $ro_detail = $this->am_model->ro_detail_for_ro_id($ro_id);

        $internal_ro_number = $ro_detail[0]['internal_ro'];
        //fetch data from ro_amount for given internal ro
        $ro_amount_details = $this->mg_model->get_ro_amount($internal_ro_number);
        //RC::Deepak - Get All the market and pricing for current cancellations
        //fixed above RC comment
        $cancel_market_data = $this->ro_model->get_cancel_market_data(array('am_ro_id' => $ro_id, 'cancel_id' => $cancel_id));
        $cancelled_market_name = array();
        $channel_ids = array();

        foreach ($cancel_market_data as $cm) {
            $market_name = $cm['market'];
            $market_spot_price = $cm['spot_price'];
            $market_banner_price = $cm['banner_price'];

            $market_spot_fct = $cm['spot_fct'];
            $market_banner_fct = $cm['banner_fct'];

            $market_price = $market_spot_price + $market_banner_price;

            if ($cm['is_cancelled'] == 1) {
                array_push($cancelled_market_name, $market_name);

                $channel_detail = $this->mg_model->get_channel_for_market($market_name);

                foreach ($channel_detail as $chnl) {
                    $channel_id = $chnl['tv_channel_id'];
                    $market_id = $chnl['market_id'];

                    //verify campaign scheduled for this channel and ro
                    $campaign_data = array('internal_ro_number' => $internal_ro_number, 'channel_id' => $channel_id, 'market_id' => $market_id);
                    $campaigns = $this->am_model->get_campaigns_from_adv_campaign($campaign_data);

                    //Verify campaign scheduled for this channel and ro
                    if (count($campaigns) > 0) {
                        $campaign_id = array();
                        foreach ($campaigns as $campaigns_val) {
                            array_push($campaign_id, $campaigns_val['campaign_id']);
                        }
                        $campaign_ids = implode(",", $campaign_id);


                        //verify whether ro is approved
                        $where_approved_data = array('internal_ro_number' => $internal_ro_number, 'tv_channel_id' => $channel_id);
                        $is_approved = $this->mg_model->get_approved_nw($where_approved_data);

                        if (count($is_approved) > 0) {
                            $cancel_data = $this->ro_model->get_cancel_data(array('ext_ro_id' => $ro_id, 'id' => $cancel_id));

                            //$date_of_cancel = date('Y-m-d',strtotime("+2 days"));
                            $date_of_cancel = $cancel_data[0]['date_of_cancel'];
                            $this->am_model->update_advertiser_screens_dates($campaign_ids, $date_of_cancel);

                            $ro_approved_nw_data = $this->mg_model->get_approved_data_for_customer_channel_ro($where_approved_data);
                            //$update_revision_no = $ro_approved_nw_data[0]['revision_no'] + 1 ;

                            //get scheduled data for this channel
                            $channel_scheduled_value = $this->am_model->get_channel_scheduled_value($internal_ro_number, $channel_id);

                            if (($channel_scheduled_value['total_spot_ad_seconds'] == 0) && ($channel_scheduled_value['total_banner_ad_seconds'] == 0)) {
                                //$this->mg_model->delete_approved_data_for_customer_channel_ro($approved_nw_data) ;
                                $update_field = TRUE;
                                $update_approved_nw_data = array(
                                    'total_spot_ad_seconds' => $channel_scheduled_value['total_spot_ad_seconds'],
                                    'channel_spot_amount' => ($channel_scheduled_value['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                                    'total_banner_ad_seconds' => $channel_scheduled_value['total_banner_ad_seconds'],
                                    'channel_banner_amount' => ($channel_scheduled_value['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                                );
                                $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $where_approved_data);
                            } else {
                                $update_field = TRUE;
                                $update_approved_nw_data = array(
                                    'total_spot_ad_seconds' => $channel_scheduled_value['total_spot_ad_seconds'],
                                    'channel_spot_amount' => ($channel_scheduled_value['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                                    'total_banner_ad_seconds' => $channel_scheduled_value['total_banner_ad_seconds'],
                                    'channel_banner_amount' => ($channel_scheduled_value['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                                );
                                $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $where_approved_data);
                            }
                            array_push($channel_ids, $channel_id);
                            //insert into ro_cancel_channel
                            $cancel_channel_data = array(
                                'channel_id' => $channel_id,
                                'internal_ro_number' => $internal_ro_number,
                                'marker_for_cancellation' => 0,
                                'user_id' => $user_id,
                                'market_id' => $market_id,
                                'cancel_requested_time' => date('Y-m-d H:i:s', strtotime("+2 days"))
                            );
                            $this->mg_model->insert_into_cancel_channel($cancel_channel_data);

                        } else {
                            $date_of_cancel = date('Y-m-d');
                            if (isset($campaign_ids) && !empty($campaign_ids)) {
                                $this->am_model->update_campaign_status($campaign_ids);
                                $this->am_model->update_advertiser_screens_dates($campaign_ids, $date_of_cancel);
                            }
                        }//End if Else is_approved
                    } else {
                        continue;
                    }


                }//End Of Channel Loop
                //update Cancel in ro_market_price
                $this->ro_model->update_market_wise_price(array('is_cancel' => 1), array('ro_id' => $ro_id, 'market' => $market_name));
            }
            //update into ro_market_price
            $this->ro_model->update_market_wise_price(array('spot_price' => $market_spot_price, 'banner_price' => $market_banner_price, 'price' => $market_price, 'spot_fct' => $market_spot_fct, 'banner_fct' => $market_banner_fct), array('ro_id' => $ro_id, 'market' => $market_name));

        }//End Of Market

        //update into ro_am_ext_ro  (check_ro_amount ?)
        $new_ro_amount = $this->ro_model->get_total_market_price($ro_id);
        $gross = $ro_detail[0]['gross'];
        $agency_commission = $ro_detail[0]['agency_com'];
        $new_agency_commission = 0;
        $marketing_promotion_amount = $ro_amount_details[0]['marketing_promotion_amount'];
        $field_activation_amount = $ro_amount_details[0]['field_activation_amount'];
        $sales_commissions_amount = $ro_amount_details[0]['sales_commissions_amount'];
        $creative_services_amount = $ro_amount_details[0]['creative_services_amount'];
        $other_expenses_amount = $ro_amount_details[0]['other_expenses_amount'];


        if ($gross >= $new_ro_amount) {
            $ro_amount_change = $gross - $new_ro_amount;
            $fraction_changed = $ro_amount_change / $gross;
            $new_agency_commission = $agency_commission - $agency_commission * $fraction_changed;
            $marketing_promotion_amount = $marketing_promotion_amount - ($marketing_promotion_amount * $fraction_changed);
            $field_activation_amount = $field_activation_amount - ($field_activation_amount * $fraction_changed);
            $sales_commissions_amount = $sales_commissions_amount - ($sales_commissions_amount * $fraction_changed);
            $creative_services_amount = $creative_services_amount - ($creative_services_amount * $fraction_changed);
            $other_expenses_amount = $other_expenses_amount - ($other_expenses_amount * $fraction_changed);
        }

        if ($gross < $new_ro_amount) {
            $ro_amount_change = $new_ro_amount - $gross;
            $fraction_changed = $ro_amount_change / $gross;
            $new_agency_commission = $agency_commission + $agency_commission * $fraction_changed;
            $marketing_promotion_amount = $marketing_promotion_amount + ($marketing_promotion_amount * $fraction_changed);
            $field_activation_amount = $field_activation_amount + ($field_activation_amount * $fraction_changed);
            $sales_commissions_amount = $sales_commissions_amount + ($sales_commissions_amount * $fraction_changed);
            $creative_services_amount = $creative_services_amount + ($creative_services_amount * $fraction_changed);
            $other_expenses_amount = $other_expenses_amount + ($other_expenses_amount * $fraction_changed);
        }
        //update into am_ext_ro
        $new_net_agency = $new_ro_amount - $new_agency_commission;
        $this->ro_model->update_into_am_ext_ro(array('gross' => $new_ro_amount, 'agency_com' => $new_agency_commission, 'net_agency_com' => $new_net_agency, 'previous_ro_amount' => $gross), array('id' => $ro_id));

        //update into ro_amount
        $this->ro_model->update_into_ro_amount(array('ro_amount' => $new_ro_amount, 'agency_commission_amount' => $new_agency_commission, 'marketing_promotion_amount' => $marketing_promotion_amount,
            'field_activation_amount' => $field_activation_amount, 'sales_commissions_amount' => $sales_commissions_amount,
            'creative_services_amount' => $creative_services_amount, 'other_expenses_amount' => $other_expenses_amount),
            array('internal_ro_number' => $internal_ro_number));

        //call update_external_report
        $this->update_into_external_ro_report_detail($internal_ro_number);

        //update revision number for given channel
        if (count($channel_ids) > 0) {
            $cids = $this->ro_model->get_customer_for_channel($channel_ids);
            foreach ($cids as $cid) {
                $customer_id = $cid['enterprise_id'];
                $endDateCrossed = $this->mg_model->checkEndDateCrossedForROCid($internal_ro_number, $customer_id);
                if (!$endDateCrossed) {
                    $where_approved_data = array('internal_ro_number' => $internal_ro_number, 'customer_id' => $customer_id);
                    $approved_nw_value = $this->mg_model->get_approved_nw($where_approved_data);

                    if ($approved_nw_value[0]['pdf_processing'] == 0) {
                        $networkFinalInfo = $this->initiateInvoiceCancelProcess($customer_id, $internal_ro_number);
                        $revision_number = $approved_nw_value[0]['revision_no'] + 1;
                        $this->mg_model->update_approved_data_where_ro_customer(array('revision_no' => $revision_number, 'pdf_generation_status' => 0), $where_approved_data);
                        $this->updatingInvoiceCancelProcess($networkFinalInfo);
                    }
                }


            }

        }//End of if for updating revision number

        //Mail Intimation
        $ro_details = $this->am_model->ro_detail_for_ro_id($ro_id);
        $cancel_data = $this->am_model->get_data_from_cancel_ro(array('id' => $cancel_id));
        $edited_market_data = $this->ro_model->get_cancel_market_data(array('am_ro_id' => $ro_id, 'cancel_id' => $cancel_id));
        //$edited_market_name = implode(",",  convert_into_array($edited_market_data, 'market')) ;

        //Insert into Surefire
        foreach ($cancelled_market_name as $market_name) {
            $marketChannel = $this->mg_model->get_channel_for_market($market_name);
            $this->mg_model->insertIntoRoSureFire(array('ro_id' => $ro_id, 'market_id' => $marketChannel[0]['market_id'], 'ro_status' => 'CANCEL_MARKET', 'cancel_date' => $cancel_data[0]['date_of_cancel'], 'processing_status' => 0));
        }


        $to_user = implode(",", convert_into_array($this->user_model->get_bhs(), 'user_email'));
        $account_mgr_email = implode(",", convert_into_array($this->user_model->get_user_detail_for_user_id($cancel_data[0]['user_id']), 'user_email'));
        $scheduler_email = implode(",", convert_into_array($this->user_model->get_user_detail_for_profile(3), 'user_email'));

        $cc_user = $account_mgr_email . "," . $scheduler_email;
        email_send($to_user,
            $cc_user,
            "market_cancellation_approved",
            array('EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro']),
            array(
                'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                'INTERNAL_RO_NUMBER' => $ro_details[0]['internal_ro'],
                'CLIENT_NAME' => $ro_details[0]['client'],
                'AGENCY_NAME' => $ro_details[0]['agency'],
                'MARKET_EDITED_PRICELIST' => make_market_price_tmp($edited_market_data),
                'MARKET_CANCELLED' => implode(",", $cancelled_market_name),
                'RO_AMOUNT' => $ro_details[0]['gross'],
                'REASON' => $cancel_data[0]['reason'],
                'CANCELLED_DATE' => date('d-m-Y', strtotime("+2 days"))
            )
        );
        log_message('debug','in ro_manager| cancel markets leaving');
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
        $actual_net_amount = $gross_ro_amount - $agency_commission_amount - $agency_rebate - $other_expenses;
        $net_contribution_amount = $actual_net_amount - $total_network_payout[0]['network_payout'];

        $net_revenue = $gross_ro_amount - $agency_commission_amount;
        $net_revenue = round(($net_revenue * SERVICE_TAX), 2);
        $net_contribution_amount_per = round(($net_contribution_amount / $net_revenue) * 100, 2);

        $total_scheduled_seconds = $this->mg_model->get_total_network_seconds_internal_ro($internal_ro_number);

        $report_data = array(
            'gross_ro_amount' => $gross_ro_amount,
            'agency_commission_amount' => $agency_commission_amount,
            'other_expenses' => $other_expenses,
            'agency_rebate' => $agency_rebate,
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

    public function initiateInvoiceCancelProcess($customer_id, $internal_ro)
    {
        $marketStr = '';
        $networkFinalInfo = array();
        $networkInfo = $this->mg_model->getAllNetworkInfo($customer_id, $internal_ro);
        log_message('info', 'ro_cancel_invoice complete ro networkInfo array-' . print_r($networkInfo, TRUE));
        if (count($networkInfo) > 0) {
            $networkFinalInfo = $networkInfo[0];
            $roNetworkMarkets = $this->mg_model->getScheduledMarketForChannel(array($networkFinalInfo['channel_names']), $internal_ro);
            log_message('info', 'ro_cancel_invoice complete ro network related channels-' . print_r($roNetworkMarkets, TRUE));
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
            log_message('info', 'ro_cancel_invoice complete ro networkInfo array-' . print_r($networkFinalInfo, TRUE));
            if (count($checkForPresenceOfInvoicedata) > 0) {
                $this->mg_model->updateInvoiceCancelData($networkFinalInfo);
            } else {
                $this->mg_model->insertInvoiceCancelData($networkFinalInfo);
            }
        }

    }

    public function approved_cancel_content_brand($ro_id, $cancel_id)
    {
        $this->is_logged_in();
        //$this->session->unset_userdata('pending_approved_campaign') ;

        $user_id = $this->get_user_id();

        $ro_detail = $this->am_model->ro_detail_for_ro_id($ro_id);

        $internal_ro_number = $ro_detail[0]['internal_ro'];
        //fetch data from ro_amount for given internal ro
        $ro_amount_details = $this->mg_model->get_ro_amount($internal_ro_number);
        //RC::Deepak - Get All the market and pricing for current cancellations
        //fixed above RC comment
        $cancel_data = $this->ro_model->get_cancel_data(array('ext_ro_id' => $ro_id, 'id' => $cancel_id));
        $cancel_type = $cancel_data[0]['cancel_type'];
        $caption_brand_name = $cancel_data[0]['caption_brand_name'];
        $cancel_date = $cancel_data[0]['date_of_cancel'];

        $cancel_market_data = $this->ro_model->get_cancel_market_data(array('am_ro_id' => $ro_id, 'cancel_id' => $cancel_id));
        $cancelled_market_name = array();
        $revised_market_data = '';
        $channel_ids = array();
        $approvalPending = FALSE;

        foreach ($cancel_market_data as $cm) {
            $market_name = $cm['market'];
            # this line commented by Biswa . The below line checks whether some campaigns may not got approval and throws an error , saying campaign is not approved  if $approvalpending is true.
	    # To counter the logic , the cancellation of any kind be it cancel by market or cancel ro or cancel by brand or cancel by content , unless and untill campaign is approved user cannot excercise cancellation  option at all. 
            //$approvalPending = $this->ro_model->approvalPending($internal_ro_number, $market_name);
            if ($approvalPending) {
                break;
            }

            $market_spot_price = $cm['spot_price'];
            $market_banner_price = $cm['banner_price'];

            $market_spot_fct = $cm['spot_fct'];
            $market_banner_fct = $cm['banner_fct'];

            $market_price = $market_spot_price + $market_banner_price;

            $revised_market_data = $revised_market_data . $market_name . " : " . $market_spot_price . " : " . $market_banner_price . "<br/>";
            if ($cm['is_cancelled'] == 1) {
                array_push($cancelled_market_name, $market_name);

                $channel_detail = $this->mg_model->get_channel_for_market($market_name);

                foreach ($channel_detail as $chnl) {
                    $channel_id = $chnl['tv_channel_id'];
                    $market_id = $chnl['market_id'];

                    //verify campaign scheduled for this channel and ro
                    if ($cancel_type == 'cancel_content') {
                        $campaign_data = array('internal_ro_number' => $internal_ro_number, 'channel_id' => $channel_id, 'market_id' => $market_id, 'caption_name' => $caption_brand_name);
                    }
                    if ($cancel_type == 'cancel_brand') {
                        $campaign_data = array('internal_ro_number' => $internal_ro_number, 'channel_id' => $channel_id, 'market_id' => $market_id, 'brand_id' => $caption_brand_name);
                    }
                    $campaigns = $this->am_model->get_campaigns_from_adv_campaign($campaign_data);

                    //Verify campaign scheduled for this channel and ro
                    if (count($campaigns) > 0) {
                        $campaign_id = array();
                        foreach ($campaigns as $campaigns_val) {
                            array_push($campaign_id, $campaigns_val['campaign_id']);
                        }
                        $campaign_ids = implode(",", $campaign_id);


                        //verify whether ro is approved
                        $where_approved_data = array('internal_ro_number' => $internal_ro_number, 'tv_channel_id' => $channel_id);
                        $is_approved = $this->mg_model->get_approved_nw($where_approved_data);

                        if (count($is_approved) > 0) {
                            $date_of_cancel = $cancel_date;
                            $this->am_model->update_advertiser_screens_dates($campaign_ids, $date_of_cancel);

                            $ro_approved_nw_data = $this->mg_model->get_approved_data_for_customer_channel_ro($where_approved_data);
                            //$update_revision_no = $ro_approved_nw_data[0]['revision_no'] + 1 ;

                            //get scheduled data for this channel
                            $channel_scheduled_value = $this->am_model->get_channel_scheduled_value($internal_ro_number, $channel_id);

                            if (($channel_scheduled_value['total_spot_ad_seconds'] == 0) && ($channel_scheduled_value['total_banner_ad_seconds'] == 0)) {
                                //$this->mg_model->delete_approved_data_for_customer_channel_ro($approved_nw_data) ;
                                $update_field = TRUE;
                                $update_approved_nw_data = array(
                                    'total_spot_ad_seconds' => $channel_scheduled_value['total_spot_ad_seconds'],
                                    'channel_spot_amount' => ($channel_scheduled_value['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                                    'total_banner_ad_seconds' => $channel_scheduled_value['total_banner_ad_seconds'],
                                    'channel_banner_amount' => ($channel_scheduled_value['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                                );
                                $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $where_approved_data);
                            } else {
                                $update_field = TRUE;
                                $update_approved_nw_data = array(
                                    'total_spot_ad_seconds' => $channel_scheduled_value['total_spot_ad_seconds'],
                                    'channel_spot_amount' => ($channel_scheduled_value['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                                    'total_banner_ad_seconds' => $channel_scheduled_value['total_banner_ad_seconds'],
                                    'channel_banner_amount' => ($channel_scheduled_value['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                                );
                                $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $where_approved_data);
                            }
                            array_push($channel_ids, $channel_id);
                            //insert into ro_cancel_channel
                            $cancel_channel_data = array(
                                'channel_id' => $channel_id,
                                'internal_ro_number' => $internal_ro_number,
                                'marker_for_cancellation' => 0,
                                'user_id' => $user_id,
                                'market_id' => $market_id,
                                'cancel_requested_time' => date('Y-m-d H:i:s', strtotime("+2 days"))
                            );
                            $this->mg_model->insert_into_cancel_channel($cancel_channel_data);

                        } else {
                            $date_of_cancel = date('Y-m-d');
                            if (isset($campaign_ids) && !empty($campaign_ids)) {
                                $this->am_model->update_campaign_status($campaign_ids);
                                $this->am_model->update_all_advertiser_screens_dates($campaign_ids);
                            }
                        }//End if Else is_approved
                    } else {
                        continue;
                    }


                }//End Of Channel Loop
                //update Cancel in ro_market_price
                //$this->ro_model->update_market_wise_price(array('ro_id'=>$ro_id,'market'=>$market_name)) ;
            }
            //update into ro_market_price
            $this->ro_model->update_market_wise_price(array('spot_price' => $market_spot_price, 'banner_price' => $market_banner_price, 'price' => $market_price, 'spot_fct' => $market_spot_fct, 'banner_fct' => $market_banner_fct), array('ro_id' => $ro_id, 'market' => $market_name));

        }//End Of Market

        if (!$approvalPending) {
            //update into ro_am_ext_ro  (check_ro_amount ?)
            $new_ro_amount = $this->ro_model->get_total_market_price($ro_id);
            $gross = $ro_detail[0]['gross'];
            $agency_commission = $ro_detail[0]['agency_com'];
            $new_agency_commission = 0;
            $marketing_promotion_amount = $ro_amount_details[0]['marketing_promotion_amount'];
            $field_activation_amount = $ro_amount_details[0]['field_activation_amount'];
            $sales_commissions_amount = $ro_amount_details[0]['sales_commissions_amount'];
            $creative_services_amount = $ro_amount_details[0]['creative_services_amount'];
            $other_expenses_amount = $ro_amount_details[0]['other_expenses_amount'];


            if ($gross >= $new_ro_amount) {
                $ro_amount_change = $gross - $new_ro_amount;
                $fraction_changed = $ro_amount_change / $gross;
                $new_agency_commission = $agency_commission - $agency_commission * $fraction_changed;
                $marketing_promotion_amount = $marketing_promotion_amount - ($marketing_promotion_amount * $fraction_changed);
                $field_activation_amount = $field_activation_amount - ($field_activation_amount * $fraction_changed);
                $sales_commissions_amount = $sales_commissions_amount - ($sales_commissions_amount * $fraction_changed);
                $creative_services_amount = $creative_services_amount - ($creative_services_amount * $fraction_changed);
                $other_expenses_amount = $other_expenses_amount - ($other_expenses_amount * $fraction_changed);
            }

            if ($gross < $new_ro_amount) {
                $ro_amount_change = $new_ro_amount - $gross;
                $fraction_changed = $ro_amount_change / $gross;
                $new_agency_commission = $agency_commission + $agency_commission * $fraction_changed;
                $marketing_promotion_amount = $marketing_promotion_amount + ($marketing_promotion_amount * $fraction_changed);
                $field_activation_amount = $field_activation_amount + ($field_activation_amount * $fraction_changed);
                $sales_commissions_amount = $sales_commissions_amount + ($sales_commissions_amount * $fraction_changed);
                $creative_services_amount = $creative_services_amount + ($creative_services_amount * $fraction_changed);
                $other_expenses_amount = $other_expenses_amount + ($other_expenses_amount * $fraction_changed);
            }
            //update into am_ext_ro
            $new_net_agency = $new_ro_amount - $new_agency_commission;
            $this->ro_model->update_into_am_ext_ro(array('gross' => $new_ro_amount, 'agency_com' => $new_agency_commission, 'net_agency_com' => $new_net_agency, 'previous_ro_amount' => $gross), array('id' => $ro_id));

            //update into ro_amount
            $this->ro_model->update_into_ro_amount(array('ro_amount' => $new_ro_amount, 'agency_commission_amount' => $new_agency_commission, 'marketing_promotion_amount' => $marketing_promotion_amount,
                'field_activation_amount' => $field_activation_amount, 'sales_commissions_amount' => $sales_commissions_amount,
                'creative_services_amount' => $creative_services_amount, 'other_expenses_amount' => $other_expenses_amount),
                array('internal_ro_number' => $internal_ro_number));

            //call update_external_report
            $this->update_into_external_ro_report_detail($internal_ro_number);

            //update revision number for given channel
            if (count($channel_ids) > 0) {
                $cids = $this->ro_model->get_customer_for_channel($channel_ids);
                foreach ($cids as $cid) {
                    $customer_id = $cid['enterprise_id'];
                    $endDateCrossed = $this->mg_model->checkEndDateCrossedForROCid($internal_ro_number, $customer_id);
                    if (!$endDateCrossed) {
                        $where_approved_data = array('internal_ro_number' => $internal_ro_number, 'customer_id' => $customer_id);
                        $approved_nw_value = $this->mg_model->get_approved_nw($where_approved_data);

                        if ($approved_nw_value[0]['pdf_processing'] == 0) {
                            $networkFinalInfo = $this->initiateInvoiceCancelProcess($customer_id, $internal_ro_number);
                            $revision_number = $approved_nw_value[0]['revision_no'] + 1;
                            $this->mg_model->update_approved_data_where_ro_customer(array('revision_no' => $revision_number, 'pdf_generation_status' => 0), $where_approved_data);
                            $this->updatingInvoiceCancelProcess($networkFinalInfo);
                        }
                    }


                }

            }//End of if for updating revision number

            //Mail Intimation
            $ro_details = $this->am_model->ro_detail_for_ro_id($ro_id);
            $cancel_data = $this->am_model->get_data_from_cancel_ro(array('id' => $cancel_id));
            $edited_market_data = $this->ro_model->get_cancel_market_data(array('am_ro_id' => $ro_id, 'cancel_id' => $cancel_id));
            //$edited_market_name = implode(",",  convert_into_array($edited_market_data, 'market')) ;

            $to_user = implode(",", convert_into_array($this->user_model->get_bhs(), 'user_email'));
            $account_mgr_email = implode(",", convert_into_array($this->user_model->get_user_detail_for_user_id($cancel_data[0]['user_id']), 'user_email'));
            $scheduler_email = implode(",", convert_into_array($this->user_model->get_user_detail_for_profile(3), 'user_email'));

            $cc_user = $account_mgr_email . "," . $scheduler_email;

            if ($cancel_type == 'cancel_content') {
                //$contentIds = $this->mg_model->getCaptionId(trim($cancel_data[0]['caption_brand_name']),$internal_ro_number) ;

                $contentIds = $this->mg_model->getCaptionIdForInternalRo(trim($captionName[0]['caption_name']), $internal_ro_number);


                foreach ($cancelled_market_name as $market_name) {
                    $marketChannel = $this->mg_model->get_channel_for_market($market_name);
                    $this->mg_model->insertIntoRoSureFire(array('ro_id' => $ro_id, 'market_id' => $marketChannel[0]['market_id'], 'content_id' => $contentIds[0]['content_id'], 'ro_status' => 'CANCEL_CONTENT', 'cancel_date' => $cancel_data[0]['date_of_cancel'], 'processing_status' => 0));
                }
                email_send($to_user,
                    $cc_user,
                    "content_cancellation_approved",
                    array('EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro']),
                    array(
                        'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                        'INTERNAL_RO_NUMBER' => $ro_details[0]['internal_ro'],
                        'CLIENT_NAME' => $ro_details[0]['client'],
                        'AGENCY_NAME' => $ro_details[0]['agency'],
                        'CONTENT_CANCELLED' => $cancel_data[0]['caption_brand_name'] . "( " . implode(",", $cancelled_market_name) . " )",
                        'MARKET_CANCELLED' => $revised_market_data,
                        'REVISED_GROSS_RO_AMOUNT' => $new_ro_amount,
                        'REASON' => $cancel_data[0]['reason'],
                        'CANCELLED_DATE' => date('d-m-Y', strtotime($cancel_data[0]['date_of_cancel']))
                    )
                );
            } else if ($cancel_type == 'cancel_brand') {
                $brandName = $this->mg_model->getBrandName($cancel_data[0]['caption_brand_name']);

                $captionName = $this->mg_model->getCaptionNameOfBrandId(array('brand_id' => $cancel_data[0]['caption_brand_name'], 'internal_ro_number' => $ro_details[0]['internal_ro']));
                //$contentIds = $this->mg_model->getCaptionId(array('content_name' =>trim($captionName[0]['caption_name']))) ;
                $contentIds = $this->mg_model->getCaptionIdForInternalRo(trim($captionName[0]['caption_name']), $internal_ro_number);

                foreach ($cancelled_market_name as $market_name) {
                    $marketChannel = $this->mg_model->get_channel_for_market($market_name);
                    $this->mg_model->insertIntoRoSureFire(array('ro_id' => $ro_id, 'market_id' => $marketChannel[0]['market_id'], 'content_id' => $contentIds[0]['content_id'], 'ro_status' => 'CANCEL_CONTENT', 'cancel_date' => $cancel_data[0]['date_of_cancel'], 'processing_status' => 0));
                }

                email_send($to_user,
                    $cc_user,
                    "brand_cancellation_approved",
                    array('EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro']),
                    array(
                        'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                        'INTERNAL_RO_NUMBER' => $ro_details[0]['internal_ro'],
                        'CLIENT_NAME' => $ro_details[0]['client'],
                        'AGENCY_NAME' => $ro_details[0]['agency'],
                        'BRAND_CANCELLED' => $brandName . "( " . implode(",", $cancelled_market_name) . " )",
                        'MARKET_CANCELLED' => $revised_market_data,
                        'REVISED_GROSS_RO_AMOUNT' => $new_ro_amount,
                        'REASON' => $cancel_data[0]['reason'],
                        'CANCELLED_DATE' => date('d-m-Y', strtotime($cancel_data[0]['date_of_cancel']))
                    )
                );
            }
        } else {
            $this->session->set_userdata('pending_approved_campaign', 'not_approved');
            redirect("/ro_manager/pending_requests");
        }
    }

    //function to show campaigns wise scheulde of RO
    //RC::Deepak-use default values for search_by and search_value arguments
    //fixed above RC comment

    public function reject_request_reason($am_ro_id, $status, $cancel_type, $cancel_id)
    {
        $this->is_logged_in();
        $model['am_ro_id'] = $am_ro_id;
        $model['status'] = $status;
        $model['cancel_type'] = $cancel_type;
        $model['cancel_id'] = $cancel_id;
        $this->load->view('ro_manager/reject_request_for_cancellation', $model);
    }

    //function to show Channel wise schedule of RO

    public function reject_request_for_cancellation()
    {
        $this->is_logged_in();
        $this->db->trans_start();
        $bh_reason = $this->input->post('reason_rej');
        $am_ro_id = $this->input->post('hid_am_ro_id');
        $status = $this->input->post('hid_status');
        $cancel_type = $this->input->post('hid_cancel_type');
        $cancel_id = $this->input->post('hid_cancel_id');

        $this->ro_model->reject_request_for_cancellation($am_ro_id, $status, $cancel_type, $cancel_id, $bh_reason);

        //change status of ro by previos status
        if ($cancel_type == 'cancel_ro' && $status == 2) {
            $this->am_model->change_ro_status($am_ro_id);
        }

        $where_data = array('am_ro_id' => $am_ro_id, 'cancel_id' => $cancel_id);
        $user_data = array('approved_type' => $status);

        if ($cancel_type == 'cancel_market' && $status == 2) {
            $this->am_model->update_tmp_market($user_data, $where_data);
        }
        $this->db->trans_complete();
        //Mail Intimation
        $ro_details = $this->am_model->ro_detail_for_ro_id($am_ro_id);
        $cancel_data = $this->am_model->get_data_from_cancel_ro(array('id' => $cancel_id));
        $cancel_market_data = $this->ro_model->get_cancel_market_data(array('am_ro_id' => $am_ro_id, 'cancel_id' => $cancel_id, 'is_cancelled' => 1));
        $cancelled_market_name = implode(",", convert_into_array($cancel_market_data, 'market'));
        $edited_market_data = $this->ro_model->get_cancel_market_data(array('am_ro_id' => $am_ro_id, 'cancel_id' => $cancel_id));

        $to_user = implode(",", convert_into_array($this->user_model->get_bhs(), 'user_email'));
        $account_mgr_email = implode(",", convert_into_array($this->user_model->get_user_detail_for_user_id($cancel_data[0]['user_id']), 'user_email'));
        $scheduler_email = implode(",", convert_into_array($this->user_model->get_user_detail_for_profile(3), 'user_email'));

        $cc_user = $account_mgr_email . "," . $scheduler_email;
        //Market cancelletion Rejection
        if ($cancel_type == 'cancel_market') {
            email_send($to_user,
                $cc_user,
                "market_cancellation_reject",
                array('EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro']),
                array(
                    'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                    'INTERNAL_RO_NUMBER' => $ro_details[0]['internal_ro'],
                    'CLIENT_NAME' => $ro_details[0]['client'],
                    'AGENCY_NAME' => $ro_details[0]['agency'],
                    'MARKET_CANCELLED' => $cancelled_market_name,
                    'MARKET_EDITED_PRICELIST' => make_market_price_tmp($edited_market_data),
                    'REASON' => $cancel_data[0]['bh_reason']
                )
            );
        }

        sleep(5);
        //Ro cancelletion Rejection
        if ($cancel_type == 'cancel_ro') {
            //commented by nitish to get campaign end date correct in Reject mail (2.9.8)
            //$campaign_end_date = $this->ro_model->get_campaign_end_date_for_ro($ro_details[0]['internal_ro']) ;
            $campaign_end_date = $this->am_model->get_actual_campaign_end_date_for_ro($ro_details[0]['internal_ro']);

            email_send($to_user,
                $cc_user,
                "ro_cancellation_reject",
                array('EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro']),
                array(
                    'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                    'INTERNAL_RO_NUMBER' => $ro_details[0]['internal_ro'],
                    'CLIENT_NAME' => $ro_details[0]['client'],
                    'AGENCY_NAME' => $ro_details[0]['agency'],
                    'CAMPAIGN_END_DATE' => $campaign_end_date,
                    'CANCEL_DATE' => $cancel_data[0]['date_of_cancel'],
                    'REASON' => $cancel_data[0]['bh_reason'],
                    'BILLING_INSTUCTION' => $cancel_data[0]['invoice_instruction']
                )
            );
        }

        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    public function submit_ro_approve($am_ro_id, $status, $cancel_type, $cancel_id)
    {
        $this->db->trans_start();
        //Used for RO approval request and raising request for schedule approval
        log_message('DEBUG', 'In ro_manager@submit_ro_approve | Transaction Started');
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $where_data = array(
            'ext_ro_id' => $am_ro_id,
            'cancel_type' => $cancel_type,
            'id' => $cancel_id
        );

        $update_data = array(
            'cancel_ro_by_admin' => $status
        );

        //update status
        $this->am_model->update_cancelled_data($update_data, $where_data);

        $where = array(
            'ro_id' => $am_ro_id,
            'mail_type' => 'submit_ro_approval'
        );
        $get_mail_details = $this->ro_model->getMailForRo($where);
        $request_emails = $get_mail_details[0]['cc_email_id'];

        $emails = $this->user_model->userEmailForRoApproval($logged_in[0]['user_id']);

        $roDetails = $this->createExtRoFeatureObj->getRoDetailsForRoId($am_ro_id);
        log_message('INFO', 'In ro_manager@submit_ro_approve | RO data - ' . print_r($roDetails, true));
        if ($logged_in[0]['profile_id'] != 11) {
            $RID = $roDetails[0]['region_id'];
            $RdMail = $this->createExtRoFeatureObj->getRdMailRegionWise($RID, $logged_in[0]['is_test_user']);
            $emails = $emails . "," . $RdMail['user_email'];
        }

        $cc_email_list = $request_emails . "," . $emails;
        $get_request_details = $this->am_model->is_cancel_request_sent_by_am($where_data);

        $submittedBy_ro_user_id = $get_request_details[0]['user_id'];

        $user_data = array(
            'ext_ro_id' => $am_ro_id,
            'cancel_type' => 'ro_approval',
            'user_id' => $logged_in[0]['user_id'],
            'date_of_submission' => date('Y-m-d'),
            'date_of_cancel' => date('Y-m-d', strtotime("+2 day")),
            'reason' => 'None',
            'invoice_instruction' => 'Approved by ' . $logged_in[0]['user_name'],
            'ro_amount' => 0,
            'approval_level' => 4,
            'cancel_ro_by_admin' => 0
        );

        //insert row(request) for schedule approval
        $this->am_model->insert_into_cancel_market($user_data);

        $to_email = $this->user_model->getUserDetailOfUserReportingManager($submittedBy_ro_user_id);
        $rep_man = $to_email[0]['user_email'];
        $cc_emails = $cc_email_list . "," . $rep_man;
        $userData = array(
            'mail_status' => 1,
            'cc_email_id' => $cc_emails,
            'mail_sent_date' => date('Y-m-d'),
            'mail_sent' => 1
        );
        //update ro mail details
        $this->ro_model->updateMailForRo($userData, array('ro_id' => $am_ro_id, 'mail_type' => 'submit_ro_approval'));

        //======================== REMOVED CRON EXECUTION PART 4 <MAIL> =================================//

        $emailDetails = $this->createExtRoFeatureObj->getMailData($am_ro_id);
        log_message('INFO', 'In ro_manager@submit_ro_approve | Email data for sending RO approved mail - ' . print_r(array($emailDetails[0]['user_email_id'], $cc_emails), true));

        $brandNames = $this->createExtRoFeatureObj->getBrandNames($roDetails[0]['brand']);
        $userDetails = $this->createExtRoFeatureObj->getUserNameForUserId($roDetails[0]['user_id']);
        $mailType = unserialize(MAIL_TYPE);
        $makeGoodType1 = unserialize(MAKE_GOOD_TYPE);
        $makeGoodType = $roDetails[0]['make_good_type'];

        //Files for email
        $fileDocumentPath = $_SERVER['DOCUMENT_ROOT'];
        if (!isset($fileDocumentPath) || empty($fileDocumentPath)) {
            $fileDocumentPath = "/opt/lampp/htdocs/";
        }
        $actualPathLocation = $fileDocumentPath . "/surewaves_easy_ro/" . 'easy_ro_temp_pdf/';

        log_message('DEBUG', 'In ro_manager@submit_ro_approve | Preparing RO and CLIENT MAIL attachment file location');
        $allFiles = '';
        $ro_parts = pathinfo($roDetails[0]['file_path']);
        $client_parts = pathinfo($roDetails[0]['client_approval_mail']);

        if($ro_parts['basename'] != '' && !empty($ro_parts['basename'])){
            $allFiles = $actualPathLocation . $ro_parts['basename'];
            if($client_parts['basename'] != '' && !empty($client_parts['basename'])) {
                $allFiles = $allFiles . ',' . $actualPathLocation . $client_parts['basename'];
            }
        }else if($client_parts['basename'] != '' && !empty($client_parts['basename'])){
            $allFiles = $actualPathLocation . $client_parts['basename'];
        }
        log_message('INFO', 'In ro_manager@submit_ro_approve | File location for RO and CLIENT APPROVED attachment prepared - ' . print_r($allFiles, true));

        $emailServiceObject = new EmailService($emailDetails[0]['user_email_id'], $cc_emails);
        $mailSent = $emailServiceObject->sendMail(
            $mailType['APPROVE_EXT_RO'],
            array('EXTERNAL_RO' => $roDetails[0]['cust_ro']),
            array('AM_NAME' => $userDetails[0]['user_name'],
                'EXTERNAL_RO' => $roDetails[0]['cust_ro'],
                'INTERNAL_RO' => $roDetails[0]['internal_ro'],
                'AGENCY' => $roDetails[0]['agency'],
                'CLIENT' => $roDetails[0]['client'],
                'BRAND' => $brandNames,
                'MAKEGOOD_TYPE' => $makeGoodType1[$makeGoodType],
                'MARKET' => $roDetails[0]['market'],
                'INSTRUCTION' => $roDetails[0]['spcl_inst'],
                'START_DATE' => $roDetails[0]['camp_start_date'],
                'END_DATE' => $roDetails[0]['camp_end_date'],
            ),
            $allFiles
        );
        if (!$mailSent) {
            log_message('INFO', 'Mail was not sent for RO - ' . $roDetails[0]['cust_ro']);
            $this->ro_model->updateMailForRo(array('mail_sent' => 0), array('ro_id' => $am_ro_id));
        } else {
            //Deleting files from local server as RO has been approved
            if ($allFiles != '') {
                $filesToDelete = explode(",", $allFiles);
                foreach ($filesToDelete as $deleteFile) {
                    if (unlink($deleteFile)) {
                        log_message('INFO', 'In ro_manager@submit_ro_approve | File Deleted - ' . print_r($deleteFile, true));
                    } else {
                        log_message('INFO', 'In ro_manager@submit_ro_approve | File Not Deleted - ' . print_r($deleteFile, true));
                    }
                }
            }
        }
        //update RO status
        $this->am_model->update_ro_status($am_ro_id, 'scheduling_in_progress');

        //========================REMOVED AUTOMATED CRON FOR SENDING MAIL TO CLIENT AND AGENCY==============================================//

        log_message('INFO', 'In ro_manager@submit_ro_approve | Sending External Mail to Agency and Client');
        $logMessageArray = array();
        $ccMailListArray = $this->mg_model->getCCMailIdList($roDetails[0]['id']);

        $toEmailList = $roDetails[0]['order_history_mail_list'];
        $ccEmailList = $ccMailListArray[0]["ccMailId"];
        $campaignStartDate = $roDetails[0]['camp_start_date'];
        $campaignEndDate = $roDetails[0]['camp_end_date'];
        $userName = $userDetails[0]['user_name'];
        $userPhone = $userDetails[0]['user_phone'];
        $userProfileImage = $userDetails[0]['profile_image'];
        $clientName = $roDetails[0]['client'];

        if (empty($userProfileImage)) {
            $userProfileImage = base_url('images/ro_progress/final_revised/phone.png');
        }
        $subjectRoNumber = str_replace(" ", "_", $roDetails[0]['cust_ro']);
        array_push($logMessageArray, $roDetails[0]['id'], $campaignStartDate, $campaignEndDate, $userName, $userPhone, $subjectRoNumber);

        $RoNumber = "<span style='font-weight:bold;font-size: 89.5%;'>" . $subjectRoNumber . "</span>";
        $AcMangerName = "<span style='font-weight:bold;font-size: 89%;'>" . $userName . "</span>";
        $AcMangerNumber = "<span style='font-weight:bold;font-size: 89%;'>" . $userPhone . "</span>";

        //------------------------ Preparing Email Data------------------------------------------------------------//
        $marketDataArray = $this->mg_model->getBookedRoMarketData($roDetails[0]['id']);
        array_push($logMessageArray, array("markets" => $marketDataArray));
        $marketDataTable = $this->buildMarketDataTableForSubmitMail($marketDataArray);

        $greetingsHeading = "Thank you for your order!";
        $greetingsHeadingNextLine = "This email confirms that your RO number " . $RoNumber . " is now booked.";
        $bodyHeader = "Please find below, the summary of your order:";
        $nextStepText = "We'll send you an email once your order is scheduled.";
        $queryText = "Please get in touch, for any support.";
        $contact = $AcMangerName . ' - ( ' . $AcMangerNumber . " )";

        $mailType = unserialize(MAIL_TYPE);
        $subject = array("BOOKED" => "Order booked - $subjectRoNumber ");
        $message = array(
            "HEAD_TEAXTURE" => base_url('images/ro_progress/final_revised/head-texture.png'),
            "LOGO" => base_url('images/ro_progress/final_revised/surewaves-logo.png'),
            "BLUE" => base_url('images/ro_progress/final_revised/blue-1.jpg'),
            "MAIL" => base_url('images/ro_progress/final_revised/mail.png'),
            "PHONE" => $userProfileImage,

            "GREETINGS_HEADING" => $greetingsHeading,
            "GREETINGS_HEADING_NEXT_LINE" => $greetingsHeadingNextLine,
            "BODY_HEADER" => $bodyHeader,
            "DATA_TABLE" => $marketDataTable,
            "NEXT_STEP_TEXT" => $nextStepText,
            "QUERY_TEXT" => $queryText,
            "CONTACT" => $contact,
            "CLIENT_NAME" => $clientName,
            "BRAND_NAME" => $brandNames
        );

        $emailServiceObject = new EmailService($toEmailList, $ccEmailList);
        $mailSent = $emailServiceObject->sendMail($mailType['RO_PROGRESS_ORDER_BOOKED'], $subject, $message, '');

        $this->mg_model->insertRoProgressionMailLog($roDetails[0]['id'], "Order booked", "Order booked - $subjectRoNumber ", $toEmailList, $ccEmailList, json_encode($logMessageArray));

        if (!$mailSent) {
            $setData = array('submit_status' => 'submitted');
        } else {
            $setData = array('submit_status' => 'mail_sent');
        }
        $this->mg_model->updateProgressionEmaiLStatus($setData, $roDetails[0]['id']);

        //==================================================================================================================================//
        $this->db->trans_complete();
        log_message('DEBUG', 'In ro_manager@submit_ro_approve | Transaction Completed. Redirecting to Pending Request');
        redirect("/ro_manager/pending_requests");
    }

    public function submit_ro_forward($am_ro_id, $status, $cancel_type, $cancel_id)
    {
        $this->db->trans_start();
        log_message('DEBUG', 'In ro_manager@submit_ro_forward | Transaction Started');
        log_message('DEBUG', 'In ro_manager@sumbit_ro_forward | Email forwarding for - ' . print_r(array('RO_Id' => $am_ro_id, 'Cancel Type' => $cancel_type, '' => $cancel_id), true));
        //Used for forwarding a RO approval request to upper level for approval

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $where_data = array(
            'ext_ro_id' => $am_ro_id,
            'cancel_type' => $cancel_type,
            'id' => $cancel_id
        );
        $get_request_details = $this->am_model->is_cancel_request_sent_by_am($where_data);
        log_message('DEBUG','In ro_manager@sumbit_ro_forward | Get Request Details -> '.print_r($get_request_details,True));
        $approval_level = $get_request_details[0]['approval_level'];
        $new_approval_level = $approval_level + 1;

        $update_data = array(
            'cancel_ro_by_admin' => 3,
            'approval_level' => $new_approval_level
        );
        log_message('DEBUG','In ro_manager@sumbit_ro_forward | Approval Level -> '.print_r($new_approval_level,True));

        //update status
        $this->am_model->update_cancelled_data($update_data, $where_data);
        log_message('DEBUG','In ro_manager@sumbit_ro_forward | Updated in ro_cancel_external_ro');

        $emails = $this->user_model->userEmailForRoCreation($logged_in[0]['user_id']);

        $whereData = array(
            'ro_id' => $am_ro_id
        );

        $userData = array(
            'mail_status' => 3,
            'approval_level' => $new_approval_level,
            'cc_email_id' => $emails,
            'mail_sent_date' => date('Y-m-d'),
            'mail_sent' => 1
        );
        //update ro mail details
        $this->ro_model->updateMailForRo($userData, $whereData);
        log_message('DEBUG','In ro_manager@sumbit_ro_forward | ro_mail Updated data is -> '.print_r($userData,True));

        //======================== REMOVED CRON EXECUTION PART 3 <MAIL> =================================//

        $emailDetails = $this->createExtRoFeatureObj->getMailData($am_ro_id);
        log_message('INFO', 'In ro_manager@submit_ro_forward | Email data for sending RO forwarded mails - ' . print_r(array($emailDetails, $emails), true));

        $roDetails = $this->createExtRoFeatureObj->getRoDetailsForRoId($am_ro_id);
        log_message('INFO', 'In ro_manager@submit_ro_forward | RO data - ' . print_r($roDetails, true));

        $forwardedTo = '';
        if ($new_approval_level == 2) {
            $forwardedTo = "National Head";
        } else if ($new_approval_level == 3) {
            $forwardedTo = "Business Head";
        }
        log_message('DEBUG','In ro_manager@submit_ro_forward | ForwardedTo value is '.print_r($forwardedTo,true));

        $brandNames = $this->createExtRoFeatureObj->getBrandNames($roDetails[0]['brand']);
        $amName = $this->createExtRoFeatureObj->getUserNameForUserId($roDetails[0]['user_id']);
        $mailType = unserialize(MAIL_TYPE);
        $makeGoodType1 = unserialize(MAKE_GOOD_TYPE);
        $makeGoodType = $roDetails[0]['make_good_type'];

        //Files for email
        $fileDocumentPath = $_SERVER['DOCUMENT_ROOT'];
        if (!isset($fileDocumentPath) || empty($fileDocumentPath)) {
            $fileDocumentPath = "/opt/lampp/htdocs/";
        }
        $actualPathLocation = $fileDocumentPath . "/surewaves_easy_ro/" . 'easy_ro_temp_pdf/';

        log_message('DEBUG', 'In ro_manager@submit_ro_forward | Preparing RO and CLIENT MAIL attachment file location');
        $allFiles = '';
        $ro_parts = pathinfo($roDetails[0]['file_path']);
        $client_parts = pathinfo($roDetails[0]['client_approval_mail']);

        if($ro_parts['basename'] != '' && !empty($ro_parts['basename'])){
            $allFiles = $actualPathLocation . $ro_parts['basename'];
            if($client_parts['basename'] != '' && !empty($client_parts['basename'])) {
                $allFiles = $allFiles . ',' . $actualPathLocation . $client_parts['basename'];
            }
        }else if($client_parts['basename'] != '' && !empty($client_parts['basename'])){
            $allFiles = $actualPathLocation . $client_parts['basename'];
        }
        log_message('INFO', 'In ro_manager@submit_ro_forward | File location for RO and CLIENT APPROVED attachment prepared - ' . print_r($allFiles, true));

        $emailServiceObject = new EmailService($emailDetails[0]['user_email_id'], $emails);
        $mailSent = $emailServiceObject->sendMail(
            $mailType['FORWARD_EXT_RO'],
            array('EXTERNAL_RO' => $roDetails[0]['cust_ro']),
            array('AM_NAME' => $amName[0]['user_name'],
                'EXTERNAL_RO' => $roDetails[0]['cust_ro'],
                'INTERNAL_RO' => $roDetails[0]['internal_ro'],
                'AGENCY' => $roDetails[0]['agency'],
                'CLIENT' => $roDetails[0]['client'],
                'BRAND' => $brandNames,
                'MAKEGOOD_TYPE' => $makeGoodType1[$makeGoodType],
                'MARKET' => $roDetails[0]['market'],
                'INSTRUCTION' => $roDetails[0]['spcl_inst'],
                'START_DATE' => $roDetails[0]['camp_start_date'],
                'END_DATE' => $roDetails[0]['camp_end_date'],
                'FORWARDED_TO' => $forwardedTo
            ),
            $allFiles
        );
        if (!$mailSent) {
            log_message('INFO', 'Mail was not sent for RO - ' . $roDetails[0]['cust_ro']);
            $this->ro_model->updateMailForRo(array('mail_sent' => 0), $whereData);
        }
        $this->db->trans_complete();
        log_message('DEBUG', 'In ro_manager@submit_ro_forward | Transaction Completed. Redirecting to pending requests ');
        redirect("/ro_manager/pending_requests");
    }

    //user logout function

    public function submit_ro_reject()
    {
        $this->db->trans_start();
        log_message('DEBUG', 'In ro_manager@submit_ro_reject | Transaction started.');
        //Used for Rejecting a RO approval request

        $am_ro_id = $this->input->post('hid_am_ro_id');
        $status = $this->input->post('hid_status');
        $cancel_type = $this->input->post('hid_cancel_type');
        $cancel_id = $this->input->post('hid_cancel_id');
        $reason = $this->input->post('reason_rej');

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $where_data = array(
            'ext_ro_id' => $am_ro_id,
            'cancel_type' => $cancel_type,
            'id' => $cancel_id
        );

        $update_data = array(
            'cancel_ro_by_admin' => $status,
            'bh_reason' => $reason
        );

        //update status
        $this->am_model->update_cancelled_data($update_data, $where_data);

        $where = array(
            'ro_id' => $am_ro_id,
            'mail_type' => 'submit_ro_approval'
        );
        $get_mail_details = $this->ro_model->getMailForRo($where);
        $request_emails = $get_mail_details[0]['cc_email_id'];

        $emails = $this->user_model->userEmailForRoRejection($logged_in[0]['user_id']);

        $get_request_details = $this->am_model->is_cancel_request_sent_by_am($where_data);
        $submittedBy_ro_user_id = $get_request_details[0]['user_id'];
        $to_email = $this->user_model->getUserDetailOfUserReportingManager($submittedBy_ro_user_id);
        $rep_man = $to_email[0]['user_email'];

        $whereData = array(
            'ro_id' => $am_ro_id
        );

        $cc_emails = $emails . "," . $request_emails . "," . $rep_man;
        $userData = array(
            'mail_status' => 2,
            'cc_email_id' => $cc_emails,
            'mail_sent_date' => date('Y-m-d'),
            'mail_sent' => 0
        );
        //update ro mail details
        $this->ro_model->updateMailForRo($userData, $whereData);

        //======================== REMOVED CRON EXECUTION PART 5 <MAIL> =================================//

        $emailDetails = $this->createExtRoFeatureObj->getMailData($am_ro_id);
        log_message('INFO', 'In ro_manager@submit_ro_reject | Email data for sending RO rejected mail - ' . print_r(array($emailDetails[0]['user_email_id'], $cc_emails), true));

        $roDetails = $this->createExtRoFeatureObj->getRoDetailsForRoId($am_ro_id);
        log_message('INFO', 'In ro_manager@submit_ro_reject | RO data - ' . print_r($roDetails, true));

        $brandNames = $this->createExtRoFeatureObj->getBrandNames($roDetails[0]['brand']);
        $amName = $this->createExtRoFeatureObj->getUserNameForUserId($roDetails[0]['user_id']);
        $mailType = unserialize(MAIL_TYPE);
        $makeGoodType1 = unserialize(MAKE_GOOD_TYPE);
        $makeGoodType = $roDetails[0]['make_good_type'];

        //Files for email
        $fileDocumentPath = $_SERVER['DOCUMENT_ROOT'];
        if (!isset($fileDocumentPath) || empty($fileDocumentPath)) {
            $fileDocumentPath = "/opt/lampp/htdocs/";
        }
        $actualPathLocation = $fileDocumentPath . "/surewaves_easy_ro/" . 'easy_ro_temp_pdf/';

        log_message('DEBUG', 'In ro_manager@submit_ro_reject | Preparing RO and CLIENT MAIL attachment file location');
        $allFiles = '';
        $ro_parts = pathinfo($roDetails[0]['file_path']);
        $client_parts = pathinfo($roDetails[0]['client_approval_mail']);

        if($ro_parts['basename'] != '' && !empty($ro_parts['basename'])){
            $allFiles = $actualPathLocation . $ro_parts['basename'];
            if($client_parts['basename'] != '' && !empty($client_parts['basename'])) {
                $allFiles = $allFiles . ',' . $actualPathLocation . $client_parts['basename'];
            }
        }else if($client_parts['basename'] != '' && !empty($client_parts['basename'])){
            $allFiles = $actualPathLocation . $client_parts['basename'];
        }
        log_message('INFO', 'In ro_manager@submit_ro_reject | File location for RO and CLIENT APPROVED attachment prepared - ' . print_r($allFiles, true));

        $emailServiceObject = new EmailService($emailDetails[0]['user_email_id'], $cc_emails);
        $mailSent = $emailServiceObject->sendMail(
            $mailType['REJECT_EXT_RO'],
            array('EXTERNAL_RO' => $roDetails[0]['cust_ro']),
            array('AM_NAME' => $amName[0]['user_name'],
                'EXTERNAL_RO' => $roDetails[0]['cust_ro'],
                'INTERNAL_RO' => $roDetails[0]['internal_ro'],
                'AGENCY' => $roDetails[0]['agency'],
                'CLIENT' => $roDetails[0]['client'],
                'BRAND' => $brandNames,
                'MAKEGOOD_TYPE' => $makeGoodType1[$makeGoodType],
                'MARKET' => $roDetails[0]['market'],
                'INSTRUCTION' => $roDetails[0]['spcl_inst'],
                'START_DATE' => $roDetails[0]['camp_start_date'],
                'END_DATE' => $roDetails[0]['camp_end_date'],
                'REASON' => $reason
            ),
            $allFiles
        );
        if (!$mailSent) {
            log_message('INFO', 'Mail was not sent for RO - ' . $roDetails[0]['cust_ro']);
            $this->ro_model->updateMailForRo(array('mail_sent' => 0), $whereData);
        } else {
            //Deleting files from server as RO has been rejected
            if ($allFiles != '') {
                $filesToDelete = explode(",", $allFiles);
                foreach ($filesToDelete as $deleteFile) {
                    if (unlink($deleteFile)) {
                        log_message('INFO', 'In ro_manager@submit_ro_reject | File Deleted - ' . print_r($deleteFile, true));
                    } else {
                        log_message('INFO', 'In ro_manager@submit_ro_reject | File Not Deleted - ' . print_r($deleteFile, true));
                    }
                }
            }
        }

//        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/MailSentForRo > /dev/null &");

        //update RO status
        $this->am_model->update_ro_status($am_ro_id, 'RO_Rejected');

        $this->db->trans_complete();
        log_message('DEBUG', 'In ro_manager@submit_ro_reject | Transaction Completed.');

        //redirect("/ro_manager/pending_requests");
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    //function to show channels of an RO

    public function show_pending_approved_campaign()
    {
        $this->is_logged_in();
        $this->load->view('/ro_manager/message_pending_approval_campaign');
    }

    public function campaigns_schedule()
    {
        log_message('DEBUG', 'In ro_manager@campaigns_scheduled | post data' . print_r($_POST, true));
        $order_id = $this->input->post('order_id');
        $edit = 0;
        $am_ro_id = $this->input->post('id');
        $search_by = $this->input->post('search_by');
        $search_value = $this->input->post('search_value');
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        // To find details about RO
        $order = $this->ro_model->get_ro_details($order_id);
        $ro_order = $this->ro_model->get_ro_data($order_id);
        //TO find campaigns belong to above RO
        $selected_region_ids = $this->mg_model->get_screen_region_ids($order_id);
        $region_ids = array();
        foreach ($selected_region_ids as $region) {
            array_push($region_ids, $region['selected_region_id']);
        }
        foreach ($region_ids as $key => $id) {
            $camp[$key] = $this->mg_model->get_order_campaigns($order_id, $id);
        }
        $campaigns = array();
        foreach ($camp as $key => $cam) {
            foreach ($cam as $key1 => $c) {
                $campaigns[] = $c;
            }
        }
        $impressions_data = $this->get_booked_vs_scheduled_impression($order_id);
        //echo print_r($impressions_data,true);

        foreach ($campaigns as &$data) {
            $campaign_id_from_campaign = $data['campaign_id'];
            foreach ($impressions_data as $imp) {
                $campaign_id_from_impression = $imp['campaign_id'];
                if ($campaign_id_from_campaign == $campaign_id_from_impression) {
                    $data['derived_campaign_status'] = $imp['derived_campaign_status'];
                    if (empty($imp['booked_impression']) || !isset($imp['booked_impression'])) {
                        $imp['booked_impression'] = 0;
                    }
                    if (empty($imp['scheduled_impression']) || !isset($imp['scheduled_impression'])) {
                        $imp['scheduled_impression'] = 0;
                    }
                    if ($imp['booked_impression'] == $imp['scheduled_impression']) {
                        $data['mismatch_impression'] = 0;
                        $data['booked_impression'] = $imp['booked_impression'];
                        $data['scheduled_impression'] = $imp['scheduled_impression'];
                        break;
                    } else if ($imp['booked_impression'] < $imp['scheduled_impression']) {
                        $data['mismatch_impression'] = 1;
                        $data['booked_impression'] = $imp['booked_impression'];
                        $data['scheduled_impression'] = $imp['scheduled_impression'];
                        break;
                    } else {
                        $data['mismatch_impression'] = -1;
                        $data['booked_impression'] = $imp['booked_impression'];
                        $data['scheduled_impression'] = $imp['scheduled_impression'];
                        break;
                    }
                }
            }
        }
        $search_value_set = 0;
        if (isset($search_by) && !empty($search_by)) {
            $campaigns = $this->search_campaign($campaigns, $search_by, $search_value);
            $search_value_set = 1;
        }
        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];
        $model['content'] = $ro_order[0];
        $model['campaigns'] = $campaigns;

        // to view ro data from ro_am_extertal_ro table
        $model['edit'] = $edit;
        $model['id'] = $am_ro_id;
        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];
        $model['search_value_set'] = $search_value_set;

//        $this->load->view('/ro_manager/campaigns_schedule', $model);

        $response['Status'] = 'success';
        $response['isLoggedIn'] = 1;
        $response['Message'] = 'Campaigns Scheduled';
        $response['Data']['jsonData'] = $model;
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function get_booked_vs_scheduled_impression($internal_ro_number)
    {

        $internal_ro_number = base64_encode($internal_ro_number);
        $url = SERVER_NAME . "/surewaves/apis/get_day_wise_actual_scheduled_impression.php?internal_ro_number=" . $internal_ro_number;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POST, 1);

        //curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        $data = array();
        if ($result['status'] == 'success') {
            foreach ($result['mesg'] as $msg) {
                $tmp = array();
                $tmp['campaign_id'] = $msg['campaign_id'];
                $tmp['campaign_name'] = $msg['campaign_name'];
                $tmp['derived_campaign_status'] = $msg['status'];

                $total_booked_impression = 0;
                $total_scheduled_impression = 0;
                foreach ($msg['dates'] as $dates) {
                    $total_booked_impression += $dates['booked_impression'];
                    $total_scheduled_impression += $dates['scheduled_impression'];
                }

                $tmp['booked_impression'] = $total_booked_impression;
                $tmp['scheduled_impression'] = $total_scheduled_impression;

                array_push($data, $tmp);
            }
        }
        //echo print_r($data,true);
        return $data;
    }

    public function search_campaign($campaigns, $search_by, $search_value)
    {
        //echo $search_by."<br/>";
        //echo print_r($campaigns,true);
        $search_value = str_replace("(", "\(", $search_value);
        $search_value = str_replace(")", "\)", $search_value);
        $search_value = str_replace("[", "\[", $search_value);
        $search_value = str_replace("]", "\]", $search_value);
        $search_value = str_replace(".", "\.", $search_value);
        $search_value = str_replace("*", "\*", $search_value);
        $search_value = str_replace("+", "\+", $search_value);
        $search_value = str_replace("?", "\?", $search_value);
        $data = array();
        foreach ($campaigns as $cmpgn) {
            if (preg_match("/" . $search_value . "/i", $cmpgn[$search_by])) {
                $tmp = array();

                $tmp['campaign_id'] = $cmpgn['campaign_id'];
                $tmp['campaign_name'] = $cmpgn['campaign_name'];
                $tmp['start_date'] = $cmpgn['start_date'];
                $tmp['end_date'] = $cmpgn['end_date'];
                $tmp['ro_duration'] = $cmpgn['ro_duration'];
                $tmp['create_datetime'] = $cmpgn['create_datetime'];
                $tmp['do_make_good'] = $cmpgn['do_make_good'];
                $tmp['brand_id'] = $cmpgn['brand_id'];
                $tmp['caption_name'] = $cmpgn['caption_name'];
                $tmp['campaign_status'] = $cmpgn['campaign_status'];
                $tmp['product_id'] = $cmpgn['product_id'];
                $tmp['client_name'] = $cmpgn['client_name'];
                $tmp['brand_new'] = $cmpgn['brand_new'];
                $tmp['product_new'] = $cmpgn['product_new'];
                $tmp['customer_ro_number'] = $cmpgn['customer_ro_number'];
                $tmp['internal_ro_number'] = $cmpgn['internal_ro_number'];
                $tmp['csv_input'] = $cmpgn['csv_input'];
                $tmp['agency_name'] = $cmpgn['agency_name'];
                $tmp['daypart'] = $cmpgn['daypart'];
                $tmp['actual_impressions'] = $cmpgn['actual_impressions'];
                $tmp['advertiser_id'] = $cmpgn['advertiser_id'];
                $tmp['derived_campaign_status'] = $cmpgn['derived_campaign_status'];
                $tmp['mismatch_impression'] = $cmpgn['mismatch_impression'];
                $tmp['booked_impression'] = $cmpgn['booked_impression'];
                $tmp['scheduled_impression'] = $cmpgn['scheduled_impression'];

                array_push($data, $tmp);
            }
        }
        return $data;
    }

    public function channels_schedule()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $order_id = $this->input->post('order_id');
        $edit = $this->input->post('edit');
        $am_ro_id = $this->input->post('id');
        // To find details about RO
        $order = $this->ro_model->get_ro_details($order_id);
        $ro_order = $this->ro_model->get_ro_data($order_id);
        //TO find campaigns belong to above RO
        $selected_region_ids = $this->mg_model->get_screen_region_ids($order_id);
        $region_ids = array();
        foreach ($selected_region_ids as $region) {
            array_push($region_ids, $region['selected_region_id']);
        }
        //Bug#424 to make sure caption is not missing in channels schedule
        //ReviewComment(kiran):: Please format the code
        //ReviewComment(kiran):: Please move the loop one level up
        foreach ($region_ids as $key => $id) {
            $campaigns[$key] = $this->mg_model->get_order_campaigns($order_id, $id);

            $camp_ids[$key] = array();
            foreach ($campaigns[$key] as $key1 => $camp) {
                array_push($camp_ids[$key], $camp['campaign_id']);
            }
            $channels_summary[$key] = $this->mg_model->get_channel_schedule_summary1(implode(',', $camp_ids[$key]), $id);
        }
        //BUG#462 channels should be sorted by Market/MSO Wise
        $summary = array();
        foreach ($channels_summary as $key => $channels) {
            foreach ($channels as $key1 => $ch) {
                $summary[] = $ch;
            }
        }
        //filter channel which is being cancelled
        $cancel_channel = $this->mg_model->get_cancel_channel_for_internal_ro($order_id);
        //$summary = $this->mg_model->filter_schedule_channel($summary,$cancel_channel) ;

        $model = array();
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;

        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];
        $model['content'] = $ro_order[0];
        $model['campaigns'] = $campaigns;
        $model['channel_summary'] = $summary;

        // to view ro data from ro_am_extertal_ro table
        $model['edit'] = $edit;
        $model['id'] = $am_ro_id;
        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];
        // end

//        $this->load->view('/ro_manager/channels_schedule', $model,true);
        $response['Status'] = 'success';
        $response['isLoggedIn'] = 1;
        $response['Message'] = 'Channels Scheduled';
        $response['Data']['html'] = $this->load->view('/ro_manager/channels_schedule', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function logout()
    {
        $this->find_data_for_cache();
        $this->user_model->logout();
        redirect("/ro_manager/home");
    }

    public function show_channels($order_id)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $order = $this->ro_model->get_ro_details(base64_decode($order_id));
        $selected_region_ids = $this->mg_model->get_screen_region_ids(base64_decode($order_id));
        $region_ids = array();
        foreach ($selected_region_ids as $region) {
            array_push($region_ids, $region['selected_region_id']);
        }
        foreach ($region_ids as $key => $id) {
            $campaigns[$key] = $this->mg_model->get_order_campaigns(base64_decode($order_id), $id);

            $camp_ids[$key] = array();
            foreach ($campaigns[$key] as $key1 => $camp) {
                array_push($camp_ids[$key], $camp['campaign_id']);
            }
            $channels_summary[$key] = $this->mg_model->get_channel_schedule_summary(implode(',', $camp_ids[$key]), $id);
        }
        $summary = array();
        foreach ($channels_summary as $key => $channels) {
            foreach ($channels as $key1 => $ch) {
                $summary[] = $ch;
            }
        }
        $channels = array();
        //filter channel which is being cancelled
        $cancel_channel = $this->mg_model->get_cancel_channel_for_internal_ro(base64_decode($order_id));
        //$summary = $this->mg_model->filter_schedule_channel($summary,$cancel_channel) ;

        foreach ($summary as $chanl) {
            array_push($channels, $chanl['channel_name']);
        }
        $ro_channels = array_unique($channels);
        $value = 0;
        if (empty($ro_channels)) {
            $value = 0;
        } else {
            $value = 1;
        }
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['order_id'] = $order_id;
        $model['channels'] = $ro_channels;
        $model['value'] = $value;
        $this->load->view('/ro_manager/add_ro', $model);
    }

    public function post_add_price_approve()
    {
        log_message('DEBUG', 'In ro_manager @ post_add_price_approve | Entering');
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

        $response = $this->postApprovalRoServiceObj->postAddPriceApprove();
        $response['isLoggedIn'] = $isLoggedIn;
        $response['Data']['html'] = $this->load->view('/ro_manager/approve_edit', array(), true);
        log_message('INFO', 'In ro_manager @ post_add_price_approve | Final Response - ' . print_r(json_encode($response), true));
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function cancel_channel_before_approval($channel_id, $order_id)
    {
        //RC:Deepak - order_id should be base64 decoded
        //above RC is fixed
        $internal_ro_number = base64_decode($order_id);
        $this->ro_model->cancel_channel_before_approval(array('channel_id' => $channel_id, 'internal_ro_number' => $internal_ro_number));
        $this->ro_model->remove_channel_file_location(array('channel_id' => $channel_id, 'internal_ro_number' => $internal_ro_number));
    }

    public function isCancelChannelExistDuringApproval($channelId, $cancelChannelIds)
    {
        if (in_array($channelId, $cancelChannelIds)) {
            return true;
        } else {
            return false;
        }
    }

    public function update_approval_status($am_ro_id)
    {
        $this->ro_model->update_approval_status(array('ext_ro_id' => $am_ro_id, 'cancel_type' => 'ro_approval'), array('cancel_ro_by_admin' => 1));
    }

    public function get_network_amount_seconds($network_id, $internal_ro)
    {
        $internal_ro = serialize($internal_ro);
        $post_details_for_confirmation = $this->ro_model->get_data_for_confirmation($internal_ro);
        $post_details_for_confirmation = $post_details_for_confirmation[0];

        $network_share_detail = unserialize($post_details_for_confirmation['post_final_amount']);
        $network_seconds_detail = unserialize($post_details_for_confirmation['hid_total_seconds']);

        $data = array();
        $total_amount = 0;
        foreach ($network_share_detail as $cid => $net_amnt) {
            $total_amount = $total_amount + $net_amnt;
        }
        $total_seconds = 0;
        foreach ($network_seconds_detail as $cid => $net_seconds) {
            $total_seconds = $total_seconds + $net_seconds;
        }
        $data['total_amount'] = $total_amount;
        $data['total_seconds'] = $total_seconds;
        return $data;
    }

    public function search_content()
    {
        $this->session->unset_userdata("search_str");
        $search_str = $this->input->post("search_str");
        $this->session->set_userdata("search_str", $search_str);
        $this->session->set_userdata("approved_ros_search_str", $search_str);
        $uri = $_SERVER['HTTP_REFERER'];
        $parts = explode('/', $uri);
        //if($parts[5] == 'approved_ros')
        //{
        //$this->approved_ros();
        $this->approved_ros(0, 1);
        //}else {
        //$this->home();
        //$this->home(0,1);
        //}
        $this->session->unset_userdata("search_str");
    }

    public function approved_ros($start_index = 0, $serch_set = 0)
    {//echo '<pre>';print_r($this->session->userdata);exit;
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $value = 0;

        // changed by Lokanath to get the ro details from ro_am_external_ro table as any RO can have multiple brands and markets
        //$list2 = $this->ro_model->get_campaigns_approved($logged_in[0]['profile_id']);
        $list2 = $this->am_model->get_campaigns_approved($logged_in[0]['profile_id'], $logged_in[0]['user_id'], $logged_in[0]['is_test_user']);
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
            $ro_lists = filter_using_search_str($ro_lists, array('client', 'cust_ro', 'agency', 'internal_ro', 'submitted_by', 'approved_by'), $search_str);
            $model['search_str'] = $search_str;
            $total_rows = count($ro_lists);
            // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);
            $model['value'] = $value;
            $model['ro_list'] = $filtered_ros;
            $model['page_links'] = create_page_links(base_url() . "/ro_manager/approved_ros", ITEM_PER_PAGE_USERS, $total_rows, $serch_set);
        } else {
            $total_rows = count($ro_lists);
            // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);
            $model['value'] = $value;
            $model['ro_list'] = $filtered_ros;
            $model['page_links'] = create_page_links(base_url() . "/ro_manager/approved_ros", ITEM_PER_PAGE_USERS, $total_rows);
        }
        // Pagination to show internal_ro_numbers
        /*$filtered_ros = array_splice($ro_lists, $start_index, ITEM_PER_PAGE_USERS);
			$model['value'] = $value;
			$model['ro_list'] = $filtered_ros;
			$model['page_links'] = create_page_links (base_url()."/ro_manager/approved_ros", ITEM_PER_PAGE_USERS, $total_rows);	*/
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $this->load->view('ro_manager/approved_ros', $model);
    }

    public function change_user_password($id)
    {
        $this->is_logged_in(1);
        $model = array();
        $model['id_for_change'] = $id;
        $this->load->view('ro_manager/change_user_password', $model);
    }

    public function post_change_user_password()
    {
        $this->is_logged_in(1);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_password', 'Password', 'trim|required|callback_password_verification|xss_clean');
        $reset_id = 0;
        if ($this->form_validation->run() == FALSE) {
            $model = array();
            $model['id_for_change'] = $this->input->post('id_for_change');
            $this->show_form_validation_error('ro_manager/change_user_password', $model);
        } else {
            $user_data = array(
                'user_password' => md5($this->input->post('user_password')),
                'plain_text_password' => $this->input->post('user_password'),
                'reset_password' => $reset_id,
            );

            $this->user_model->update_user($user_data, $this->input->post('id_for_change'));


            //Venkat: call function to email user credentials
            $logged_in = $this->session->userdata("logged_in_user");
            $to = $logged_in[0]['user_email'];
            $profile_id = $logged_in[0]['profile_id'];
            $login_url = 'http://' . $_SERVER['SERVER_NAME'] . ROOT_FOLDER;
            $text = "user_update";
            $subject = array('USER_NAME' => $logged_in[0]['user_name']);
            $message = array(
                'USER_NAME' => $logged_in[0]['user_name'],
                'USER_EMAIL' => $logged_in[0]['user_email'],
                'PASSWORD' => $this->input->post('user_password'),
                'LOGIN_URL' => $login_url,
            );
            $file = '';
            $url = '';
            mail_send($to, $text, $subject, $message, $file, '', $url);
            /*echo '<script language="javascript">top.location.href="'.ROOT_FOLDER.'/ro_manager/home";</script>';*/

            switch ($profile_id) {
                case "8":
                    echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/network_svc_manager/channel_status";</script>';
                    break;
                case "9":
                    echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/network_svc_manager/channel_rates";</script>';
                    break;
                default:
                    echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/account_manager/home";</script>';
            }


        }
    }

    public function show_form_validation_error($url, $model = null)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<span style="color:#990000; font-weight:normal; font-size:10px;">', '</span>');
        if (!isset($model) || empty($model)) {
            $this->load->view($url);
        } else {
            $this->load->view($url, $model);
        }
    }

    public function password_verification($str)
    {
        $passwordPattern = "/^[a-zA-Z0-9_@*#]{6,15}$/";
        $value = preg_match($passwordPattern, $str);
        if ($value == 0) {
            //does not match
            $this->form_validation->set_message('password_verification', 'password is not valid');
            return FALSE;
        } else {
            return TRUE;
        }
        exit;
    }

    public function pre_ro_approval()
    {
        log_message('DEBUG', 'In ro_manager@preroapproval | Inside preroapproval.');
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

        $roId = $this->input->post('id');
        log_message('INFO', 'In ro_manager@preroapproval | RoId - ' . print_r($roId, true));

        $errorInValidation = is_numeric($roId);
        log_message('INFO', 'In ro_manager@preroapproval | Invalid RO id? - ' . print_r(!$errorInValidation, true));
        if (!$errorInValidation) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Data Validation Failed! Invalid RO ID!!';
            $response['Data']['formValidation'] = array(array('roId' => 'Invalid RO ID!!'));
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }

        $isRoAlreadyApproved = $this->roApprovalServiceObj->isRoAlreadyApproved($roId);
        log_message('INFO', 'In ro_manager@preroapproval | Is RO already approved ? - ' . print_r($isRoAlreadyApproved, true));
        if($isRoAlreadyApproved){
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'This RO is already approved. Goto APPROVED ROs Page!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }

        $viewLoad = $this->roApprovalServiceObj->preRoApproval($roId);
        log_message('INFO', 'In ro_manager@preroapproval | View to load - ' . print_r($viewLoad['view'], true));

        $response['Status'] = 'success';
        $response['isLoggedIn'] = $isLoggedIn;
        if ($viewLoad['view'] == '/ro_manager/approve') {
            log_message('DEBUG', 'In ro_manager@preroapproval | Sending JSON');
            $response['Message'] = 'Pre RO Approval for BH';
            $response['Data']['jsonData'] = $viewLoad['model'];
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        } else {
            log_message('DEBUG', 'In ro_manager@preroapproval | Loading view');
            $response['Message'] = 'Pre RO Approval for other profiles';
            $response['Data']['html'] = $this->load->view($viewLoad['view'], $viewLoad['model'], true);
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }
    }

    public function ro_approved()
    {
        log_message('DEBUG', 'In ro_manager@ro_approved | Inside ro_approved.');
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

        $roId = $this->input->post('id');
        log_message('INFO', 'In ro_manager@ro_approved | RoId - ' . print_r($roId, true));

        $errorInValidation = is_numeric($roId);
        log_message('INFO', 'In ro_manager@ro_approved | Invalid RO id? - ' . print_r(!$errorInValidation, true));
        if (!$errorInValidation) {
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'Data Validation Failed!';
            $response['Data']['formValidation'] = array(array('roId' => 'Invalid RO ID!!'));
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }
        $isRoAlreadyApproved = $this->roApprovalServiceObj->isRoAlreadyApproved($roId);
        log_message('INFO', 'In ro_manager@ro_approved | Is RO already approved ? - ' . print_r($isRoAlreadyApproved, true));
        if(!$isRoAlreadyApproved){
            $response['Status'] = 'fail';
            $response['isLoggedIn'] = $isLoggedIn;
            $response['Message'] = 'This RO is NOT approved. Goto HOME Page!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }

        $viewLoad = $this->roApprovalServiceObj->roApproved($roId);
        log_message('INFO', 'In ro_manager@roApproved | View to load - ' . print_r($viewLoad['view'], true));

        $response['Status'] = 'success';
        $response['isLoggedIn'] = $isLoggedIn;
        if ($viewLoad['view'] == '/ro_manager/approve') {
            log_message('DEBUG', 'In ro_manager@ro_approved | Sending JSON');
            $response['Message'] = 'Pre RO Approval for BH';
            $response['Data']['jsonData'] = $viewLoad['model'];
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        } else {
            log_message('DEBUG', 'In ro_manager@ro_approved | Loading view');
            $response['Message'] = 'Pre RO Approval for other profiles';
            $response['Data']['html'] = $this->load->view($viewLoad['view'], $viewLoad['model'], true);
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        }
    }

    public function process_edited_network()
    {
        log_message('DEBUG', 'In ro_manager@process_edited_network | Inside process_edited_network.');
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

        $response = $this->processEditedNetworkServiceObj->processEditedNetwork();
        log_message('INFO', 'In ro_manager@process_edited_network | View to load - /ro_manager/approve_edit');
        $response['isLoggedIn'] = $isLoggedIn;
        $response['Data']['html'] = $this->load->view('/ro_manager/approve_edit', array(), true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function makeCacheDataUniform($network_detail, $user_id, $order_id)
    {
        $key_for_cancel_channel = 'add_cancel_channel_' . $user_id . "_" . $order_id;
        $cancel_channels = $this->memcache->get($key_for_cancel_channel);
        $data = array();
        $total_nw_payout = 0;

        foreach ($network_detail as $nw_value) {
            $nw_payout = 0;
            $customer_id = $nw_value['network_id'];
            $nw_tmp = array();
            $nw_tmp['network_id'] = $customer_id;
            $nw_tmp['network_name'] = $nw_value['network_name'];
            $nw_tmp['market_name'] = $nw_value['market_name'];
            //$nw_tmp['market_id']  = $nw_value['market_id'];
            $customer_share = $nw_value['revenue_sharing'];

            $channel_details = $nw_value['channels_data_array'];
            foreach ($channel_details as $chnl_details) {
                $tmp = array();
                $channel_id = $chnl_details['channel_id'];
                $tmp['channel_id'] = $channel_id;
                $tmp['channel_name'] = $chnl_details['channel_name'];
                /*   $tmp['to_play_spot'] = $chnl_details['to_play_spot'] ;
                $tmp['to_play_banner'] = $chnl_details['to_play_banner'] ; */


                if (isset($chnl_details['total_spot_ad_seconds']) && !empty($chnl_details['total_spot_ad_seconds'])) {
                    $tmp['spot_region_id'] = '1';
                    $tmp['total_spot_ad_seconds'] = round($chnl_details['total_spot_ad_seconds'], 2);
                    //$channel_spot_avg_rate = round($chnl_details['channel_spot_avg_rate'],2) ;
                }
                if (isset($chnl_details['total_banner_ad_seconds']) && !empty($chnl_details['total_banner_ad_seconds'])) {
                    $tmp['banner_region_id'] = '3';
                    $tmp['total_banner_ad_seconds'] = round($chnl_details['total_banner_ad_seconds'], 2);
                    //$channel_banner_avg_rate = round($chnl_details['channel_banner_avg_rate'],2) ;
                }
                $tmp['channel_spot_avg_rate'] = round($chnl_details['channel_spot_avg_rate'], 2);
                $tmp['channel_spot_reference_rate'] = round($chnl_details['channel_spot_reference_rate'], 2);
                $tmp['channel_spot_amount'] = round($chnl_details['channel_spot_amount'], 2);

                $tmp['channel_banner_avg_rate'] = round($chnl_details['channel_banner_avg_rate'], 2);
                $tmp['channel_banner_reference_rate'] = round($chnl_details['channel_banner_reference_rate'], 2);
                $tmp['channel_banner_amount'] = round($chnl_details['channel_banner_amount'], 2);

                $tmp['customer_share'] = $chnl_details['customer_share'];
                $tmp['approved'] = $chnl_details['approved'];

                if (in_array($channel_id, $cancel_channels)) {
                    $tmp['cancel_channel'] = 'yes';
                    $nw_payout += 0;
                    $total_nw_payout += 0;
                } else {
                    $tmp['cancel_channel'] = 'no';
                    $nw_payout += ($chnl_details['channel_spot_amount'] + $chnl_details['channel_banner_amount']) * ($customer_share / 100);
                    $total_nw_payout += ($chnl_details['channel_spot_amount'] + $chnl_details['channel_banner_amount']) * ($customer_share / 100);
                }
                $nw_tmp['channels_data_array'][$channel_id] = $tmp;

            }

            $nw_tmp['nw_payout'] = $nw_payout;
            $nw_tmp['revenue_sharing'] = $nw_value['revenue_sharing'];
            $nw_tmp['approved'] = $nw_value['approved'];
            $data['nw_data'][$customer_id] = $nw_tmp;
        }
        //$data['nw_data'] = $data['nw_data'][$customer_id] ;
        $data['total_nw_payout'] = $total_nw_payout;
        return $data;
    }

    public function make_market_priority_combination($marketIds, $priority)
    {
        $data = array();
        foreach ($marketIds as $id) {
            foreach ($priority as $pry) {
                $key = $id . "_" . $pry;
                $data[$key] = '1';
            }
        }
        return $data;
    }

    public function make_nw_channel_sorted_order($nw_data_array)
    {
        $data = array();
        foreach ($nw_data_array['non_approved'] as $nw_array) {
            if (count($nw_array) > 0) {
                $new_nw_data = array();
                foreach ($nw_array as $nw_key => $nw_value) {
                    if ($nw_key != 'channels_data_array') {
                        $new_nw_data[$nw_key] = $nw_value;
                    } else {
                        $new_nw_data['channels_data_array'] = array();
                        foreach ($nw_value['non_approved'] as $chnl_array) {
                            if (count($chnl_array) > 0) {
                                $tmp = array();
                                foreach ($chnl_array as $chnl_key => $chnl_value) {
                                    $tmp[$chnl_key] = $chnl_value;
                                }
                                array_push($new_nw_data['channels_data_array'], $tmp);
                            }

                        }

                        foreach ($nw_value['approved'] as $chnl_array) {
                            if (count($chnl_array) > 0) {
                                $tmp = array();
                                foreach ($chnl_array as $chnl_key => $chnl_value) {
                                    $tmp[$chnl_key] = $chnl_value;
                                }
                                array_push($new_nw_data['channels_data_array'], $tmp);
                            }

                        }
                    }
                }
                array_push($data, $new_nw_data);
            }

        }

        foreach ($nw_data_array['approved'] as $value) {
            if (count($value) > 0) {
                $new_nw_data = array();
                foreach ($value as $nw_key => $nw_value) {
                    if ($nw_key != 'channels_data_array') {
                        $new_nw_data[$nw_key] = $nw_value;
                    } else {
                        $new_nw_data['channels_data_array'] = array();
                        foreach ($nw_value['non_approved'] as $chnl_array) {
                            if (count($chnl_array) > 0) {
                                $tmp = array();
                                foreach ($chnl_array as $chnl_key => $chnl_value) {
                                    $tmp[$chnl_key] = $chnl_value;
                                }
                                array_push($new_nw_data['channels_data_array'], $tmp);
                            }

                        }

                        foreach ($nw_value['approved'] as $chnl_array) {
                            if (count($chnl_array) > 0) {
                                $tmp = array();
                                foreach ($chnl_array as $chnl_key => $chnl_value) {
                                    $tmp[$chnl_key] = $chnl_value;
                                }
                                array_push($new_nw_data['channels_data_array'], $tmp);
                            }

                        }
                    }
                }
                array_push($data, $new_nw_data);
            }

        }
        return $data;
    }

    public function sort_by_market($data)
    {

        $sortArray = array();
        foreach ($data as $val) {
            foreach ($val as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }

        $orderby = "market_name"; //change this to whatever key you want from the array

        array_multisort($sortArray[$orderby], SORT_ASC, $data);

        return $data;
    }

    public function not_to_show_edit_save($camp_end_date)
    {
        /* $todays_month = date("m") ;
            $campaign_start_month = date("m",strtotime($camp_start_date)) ;
            if($todays_month > $campaign_start_month) {
                return FALSE ;
            }else {
                return FALSE ;
            }*/
        $today = date("Y-m-d");

        if (strtotime($today) > strtotime($camp_end_date)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function approval_pagination($order_id, $edit, $am_ro_id, $lastelement)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $internal_ro_number = base64_decode($order_id);
        $submit_ext_ro_details = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);

        //$cache_data = fetch_cache_data($am_ro_id) ;print_r($cache_data);exit;

        $cache_data = $this->memcache->get($am_ro_id);
        //$lastelement = $_REQUEST['lastelement'] ;

        $pagination_data = array_slice($cache_data, $lastelement, APPROVAL_RO_PAGINATION);
        if ($submit_ext_ro_details[0]['test_user_creation'] != 2 && $logged_in[0]['is_test_user'] == 2) {
            $html = $this->create_html_scheduler($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement);
            echo $html;
        } else {
            if ($profile_id == 1 || $profile_id == 2 || $profile_id == 10) {
                $html = $this->create_html($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement);
                echo $html;
            } else if ($profile_id == 3) {
                $html = $this->create_html_scheduler($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement);
                echo $html;
            } else if ($profile_id == 6 || $profile_id == 11 || $profile_id == 12) {
                $html = $this->create_html_am($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement);
                echo $html;
            } else if ($profile_id == 7) {
                $html = $this->create_html_finance($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement);
                echo $html;
            }
        }

    }

    public function create_html_scheduler($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement)
    {
        $submit_ext_ro_details = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);
        $ro_amount_details = $this->mg_model->get_ro_amount($internal_ro_number);
        $ro_status_detail = $this->am_model->data_for_ro_id_in_ro_status($am_ro_id);
        $ro_status_entry = $this->am_model->convert_status_into_format($ro_status_detail[0]['ro_status']);
        $total_network_payout = $this->mg_model->get_total_network_payout(base64_decode($order_id));
        $ro_approval = $this->mg_model->get_approval_request_data(base64_decode($order_id));

        $cache_data = $this->memcache->get($am_ro_id);
        //$total_networks = count($key_data);
        foreach ($cache_data as $key => $dat) {
            $total_amount = 0;
            $total_rows = 0;
            foreach ($dat['channels_data_array'] as $chnl => $ch) {
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['network_id'];
                $customer_share = $ch['customer_share'];
                //foreach($ch['ad_types'] as $types => $ad) {
                if ($ch['total_spot_ad_seconds'] != 0) {
                    $total_rows++;
                    $total_amount += $ch['channel_spot_amount'];
                }
                if ($ch['total_banner_ad_seconds'] != 0) {
                    $total_rows++;
                    $total_amount += $ch['channel_banner_amount'];
                }

                //}
            }
            $rows[$customer_id] = $total_rows;
            $final_amount += $total_amount - $total_amount * (100 - $customer_share) / 100;
        }
        $html = '';
        $nw_count = $lastelement;
        foreach ($pagination_data as $key => $dat) {
            $total_networks = count($pagination_data);
            $row_id = 0;
            $html .= '<table id ="' . network_ . $key . '" cellpadding="0" cellspacing="0" width="100%">
						<tr cellpadding="0" cellspacing="0" width="100%" >
							<td colspan="4" style="font-weight:bold;background:#DDDDDD;size:20px; width:400px;">Network Name: ' . $dat['network_name'] . ' &nbsp; (' . $dat['market_name'] . ')</td>
						</tr>';
            $total_amount = 0;
            $approved_total_amount = 0;
            foreach ($dat['channels_data_array'] as $chnl => $ch) {
                $total_channel = count($dat['channels_data_array']);
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['network_id'];

                $html .= '<tr cellpadding="0" cellspacing="0" width="100%">
                                                        <td colspan="3" style="font-weight:bold">Channel Name: ' . $ch['channel_name'] . ' 									 
                                                        </td>
                                                </tr>
						 <tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
                                                        <th colspan="3">Ad Type</th>
                                                        <th colspan="3">Total Ad Seconds</th>
                                                </tr>';
                if ($ch['total_spot_ad_seconds'] != 0) {
                    $amount = 0;
                    $rate = $ch['channel_spot_avg_rate'];
                    $amount += $ch['total_spot_ad_seconds'] * $rate / 10;
                    $total_amount += $amount;

                    $html .= '<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Spot Ad</td>
							<td colspan="3" id="' . network_ . $key . _total_sec_ . $row_id . '_0' . '" >' . $ch['total_spot_ad_seconds'] . '</td>';
                }
                if ($ch['total_banner_ad_seconds'] != 0) {
                    $amount = 0;
                    $rate = $ch['channel_banner_avg_rate'];
                    $amount += $ch['total_banner_ad_seconds'] * $rate / 10;
                    $total_amount += $amount;

                    $html .= '<tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
							<td colspan="3">Banner Ad</td>
							<td colspan="3" id="' . network_ . $key . _total_sec_ . $row_id . '_0' . '" >' . $ch['total_banner_ad_seconds'] . '</td>';
                }
            }
            $html .= '</table>';

            $count++;
            $lastCount = $count;

            $nw_count++;
        }
        return $html;
    }

    public function create_html($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement)
    {
        $submit_ext_ro_data = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);
        //$camp_start_date = $submit_ext_ro_data[0]['camp_start_date'] ;
        $camp_end_date = $submit_ext_ro_data[0]['camp_end_date'];
        $edit_save_not_to_show = $this->not_to_show_edit_save($camp_end_date);
        $readOnly = 0;
        if ($edit_save_not_to_show) {
            //$readOnly = "readonly = 'readonly' " ;
            $readOnly = 1;
        }

        $ro_amount_details = $this->mg_model->get_ro_amount($internal_ro_number);
        $ro_status_detail = $this->am_model->data_for_ro_id_in_ro_status($am_ro_id);
        $ro_status_entry = $this->am_model->convert_status_into_format($ro_status_detail[0]['ro_status']);

        //How are using order_id
        $total_network_payout = $this->mg_model->get_total_network_payout(base64_decode($order_id));
        $ro_approval = $this->mg_model->get_approval_request_data(base64_decode($order_id));

        $verify_ro_approved = 0;
        $verify = $this->am_model->verify_ro_status($am_ro_id);
        if (count($verify) > 0) {
            $verify_ro_approved = 1;
        }

        $cache_data = $this->memcache->get($am_ro_id);
        //$total_networks = count($key_data);
        foreach ($cache_data as $key => $dat) {
            $total_amount = 0;
            $total_rows = 0;
            foreach ($dat['channels_data_array'] as $chnl => $ch) {
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['network_id'];
                $customer_share = $ch['customer_share'];
                //foreach($ch['ad_types'] as $types => $ad) {
                if ($ch['total_spot_ad_seconds'] != 0) {
                    $total_rows++;
                    if ($ch['cancel_channel'] == 'no') {
                        $total_amount += $ch['channel_spot_amount'];
                    }
                }
                if ($ch['total_banner_ad_seconds'] != 0) {
                    $total_rows++;
                    if ($ch['cancel_channel'] == 'no') {
                        $total_amount += $ch['channel_banner_amount'];
                    }
                }

                //}
            }
            $rows[$customer_id] = $total_rows;
            $final_amount += $total_amount - $total_amount * (100 - $customer_share) / 100;
        }
        $html = '';
        $nw_count = $lastelement;
        foreach ($pagination_data as $key => $dat) {
            $total_networks = count($pagination_data);
            $row_id = 0;
            if ($verify_ro_approved == 1) {
                $html .= '<form id="form_' . $dat['network_id'] . '" name="form_' . $dat['network_id'] . '" method="post" action="' . ROOT_FOLDER . '/ro_manager/save_to_cache" target="iframe_' . $dat['network_id'] . '">';
            }

            $html .= '<table id ="' . network_ . $nw_count . '" cellpadding="0" cellspacing="0" width="100%">
						<tr cellpadding="0" cellspacing="0" width="100%" >
							<td colspan="4" style="font-weight:bold;background:#DDDDDD;size:20px; width:400px;">Network Name: ' . $dat['network_name'] . ' &nbsp; (' . $dat['market_name'] . ')</td>
                            <td>';
            if ($readOnly == 0 && ($dat['approved'] == 1 || $verify_ro_approved == 1)) {
                $html .= '<input type="button" value="Edit" class="submit" onclick="modify_network(' . $dat['network_id'] . ')">&nbsp;<input type="submit" value="Save" class="submit" onclick="return save_to_cache(' . $dat['network_id'] . ');" title="Changes will be discarded if not submitted">';
            } else {
                $html .= '&nbsp;';
            }

            $html .= '</td>
						</tr>';
            $total_amount = 0;
            $approved_total_amount = 0;
            foreach ($dat['channels_data_array'] as $chnl => $ch) {
                $total_channel = count($dat['channels_data_array']);
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['network_id'];
                $html .= '<input type="hidden" id="' . $channel_id . '_approved" value="' . $ch['approved'] . '" />';
                $html .= '<tr cellpadding="0" cellspacing="0" width="100%" ';

                if ($ch['cancel_channel'] == 'yes') {
                    $html .= 'style="background-color:#ff9999"';
                } else if ($ch['additional_campaign'] == 1) {
                    $html .= 'style="background-color:#F3F781"';
                } else {
                    $html .= 'style="background-color:#fbfbfb"';
                }
                $html .= 'id="row_' . $nw_count . '_' . $row_id . '" >';


                $html .= '<td colspan="3" style="font-weight:bold"><input type="checkbox" name="channels[' . $customer_id . '][]" value="' . $channel_id . '" onclick="change_channel_data(' . $nw_count . ',' . $row_id . ',' . $channel_id . ')" ';
                if ($ch['cancel_channel'] == 'no') {
                    $html .= 'checked="checked"';
                } else {
                    //$html .= '<script language="javascript">';
                    //$html .= '$("#hid_cancel_channel_"+'.$channel_id.').val('.$channel_id.')';
                    //$html .= '</script>';
                }
                $html .= 'id="channel_' . $channel_id . '" class="show_checkbox_' . $customer_id . '"';
                if ($dat['approved'] == 1) {
                    $html .= 'disabled';
                }
                $html .= ' />';
                if ($ch['additional_campaign'] == 1) {
                    $html .= '<input type="hidden" name="changed_network_' . $dat['network_id'] . '[]" id="" class="changed_network changed_network_' . $dat['network_id'] . '" value="0">';
                }
                $html .= '<input type="hidden" name="cancel_channel[]" id="hid_cancel_channel_' . $channel_id . '"  />
														<input type="hidden" id="row_' . $nw_count . '_' . $row_id . '_additional_campaign" value="' . $ch['additional_campaign'] . '" />
														&nbsp;
														Channel Name: ' . $ch['channel_name'] . '
                                                        </td><td style="font-weight:bold" align="right">
                                                        </td>
                                                </tr>
						 <tr cellpadding="0" cellspacing="0" width="100%">
                                                        <th colspan="3">Ad Type</th>
                                                        <th colspan="3">Total Ad Seconds</th>
                                                        <th colspan="3">Last Rate</th>
                                                        <th colspan="3">Reference Rate</th>
                                                        <th colspan="3">Amount</th>
                                                </tr>';

                if ($ch['total_spot_ad_seconds'] != 0) {
                    //$total_ad_types = count($ch['ad_types']);
                    $amount = 0;
                    $rate = $ch['channel_spot_avg_rate'];
                    $amount += $ch['total_spot_ad_seconds'] * $rate / 10;
                    if ($ch['cancel_channel'] == 'no') {
                        $total_amount += $amount;
                    }

                    $html .= '<tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
							<td colspan="3">Spot Ad</td>
							<td colspan="3" class="' . seconds_ . $channel_id . _spot . '" id="' . network_ . $nw_count . _total_sec_ . $row_id . '_0' . '" >' . $ch['total_spot_ad_seconds'] . '</td>';
                    $html .= '<input type="hidden" id="' . network_ . $nw_count . _total_sec_ . $row_id . _0_hidden_play_spot . '" value="' . $ch['to_play_spot'] . '" />
								<input type="hidden" id="' . network_ . $nw_count . _total_sec_ . $row_id . _0_hidden_schedule_spot . '" value="' . $ch['total_spot_ad_seconds'] . '" />';


                    if ($ch['approved'] == 1) {
                        $html .= '<td colspan="3" class="hide_' . $customer_id . '">' . $ch['channel_spot_avg_rate'] . '</td>';
                        $html .= '<td colspan="3" class="show_' . $customer_id . '" style="display:none;"><input type="text" class="rate" ' . $readOnly . '  name="post_channel_avg_rate[' . $channel_id . '][0]" id="' . network_ . $nw_count . _channel_ . $row_id . '_1' . '" value="' . $rate . '"';
                        if ($ch['cancel_channel'] == 'yes') {
                            $html .= " readonly='readonly'";
                        }
                        $html .= ' onchange = "find_price(' . $row_id . ',' . $rows[$customer_id] . ',' . $nw_count . ');find_total_payout(' . $total_networks . ');" /> </td>';
                    } else {
                        $html .= '<td colspan="3"><input type="text" class="rate" ' . $readOnly . ' name="post_channel_avg_rate[' . $channel_id . '][0]" id="' . network_ . $nw_count . _channel_ . $row_id . '_1' . '" value="' . $rate . '"';
                        if ($ch['cancel_channel'] == 'yes') {
                            $html .= " readonly='readonly'";
                        }
                        $html .= ' onchange = "find_price(' . $row_id . ',' . $rows[$customer_id] . ',' . $nw_count . ');find_total_payout(' . $total_networks . ');" /> </td>';
                    }

                    $html .= '<td colspan="3">' . round($ch['channel_spot_reference_rate'], 2) . '</td>
                            <input type="hidden" name="post_channel_ref_rate[' . $channel_id . '][0]" value="' . round($ch['channel_spot_reference_rate'], 2) . '" />';

                    if ($ch['approved'] == 1) {
                        $approved_total_amount += $ch['channel_spot_amount'];
                        $html .= '<td colspan="3" class="hide_' . $customer_id . '">' . round($ch['channel_spot_amount'], 2) . '</td>';
                        $html .= '<td colspan="3" class="show_' . $customer_id . '" style="display:none;"><input type="text" class="amount" ' . $readOnly . ' name="post_channel_amount[' . $channel_id . '][0]" id="' . network_ . $nw_count . _amount_ . $row_id . '_2' . '" value="' . round($amount, 2) . '"';
                        if ($ch['cancel_channel'] == 'yes') {
                            $html .= " readonly='readonly'";
                        }
                        $html .= ' onchange = "find_rate(' . $row_id . ',' . $rows[$customer_id] . ',' . $nw_count . ');find_total_payout(' . $total_networks . ');" />
							<input type="hidden" name="hid_total_seconds[' . $channel_id . '][0]" value="' . $ch['total_spot_ad_seconds'] . '" /> 
							<input type="hidden" name="hid_client_name" value="' . $submit_ext_ro_data[0]['client'] . '" />
							<input type="hidden" name="hid_internal_ro" value="' . rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']), '=') . '" />
							<input type="hidden" name="hid_cust_id[' . $customer_id . ']" value="' . $customer_id . '" />';
                        $row_id++;
                        $html .= '<input type="hidden" name="total_rows" value="' . $rows[$customer_id] . '" /></td>';
                    } else {
                        $html .= '<td colspan="3"><input type="text" class="amount" ' . $readOnly . ' name="post_channel_amount[' . $channel_id . '][0]" id="' . network_ . $nw_count . _amount_ . $row_id . '_2' . '" value="' . round($amount, 2) . '"';
                        if ($ch['cancel_channel'] == 'yes') {
                            $html .= " readonly='readonly'";
                        }
                        $html .= ' onchange = "find_rate(' . $row_id . ',' . $rows[$customer_id] . ',' . $nw_count . ');find_total_payout(' . $total_networks . ');" />
							<input type="hidden" name="hid_total_seconds[' . $channel_id . '][0]" value="' . $ch['total_spot_ad_seconds'] . '" /> 
							<input type="hidden" name="hid_client_name" value="' . $submit_ext_ro_data[0]['client'] . '" />
							<input type="hidden" name="hid_internal_ro" value="' . rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']), '=') . '" />
							<input type="hidden" name="hid_cust_id[' . $customer_id . ']" value="' . $customer_id . '" />';
                        $row_id++;
                        $html .= '<input type="hidden" name="total_rows" value="' . $rows[$customer_id] . '" /></td>';
                    }
                    $html .= '</tr>';
                }
                if ($ch['total_banner_ad_seconds'] != 0) {
                    //$total_ad_types = count($ch['ad_types']);
                    $amount = 0;
                    $rate = $ch['channel_banner_avg_rate'];
                    $amount += $ch['total_banner_ad_seconds'] * $rate / 10;
                    if ($ch['cancel_channel'] == 'no') {
                        $total_amount += $amount;
                    }

                    $html .= '<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Banner Ad</td>
							<td colspan="3" class="' . seconds_ . $channel_id . _banner . '" id="' . network_ . $nw_count . _total_sec_ . $row_id . '_0' . '" >' . $ch['total_banner_ad_seconds'] . '</td>';
                    $html .= '<input type="hidden" id="' . network_ . $nw_count . _total_sec_ . $row_id . '_0_hidden_play_banner' . '" value="' . $ch['to_play_banner'] . '" />
                                 <input type="hidden" id="' . network_ . $nw_count . _total_sec_ . $row_id . '_0_hidden_schedule_banner' . '"  value="' . $ch['total_banner_ad_seconds'] . '" />';

                    if ($ch['approved'] == 1) {
                        $html .= '<td colspan="3" class="hide_' . $customer_id . '">' . $ch['channel_banner_avg_rate'] . '</td>';
                        $html .= '<td colspan="3" class="show_' . $customer_id . '" style="display:none;"><input type="text" class="rate" ' . $readOnly . ' name="post_channel_avg_rate[' . $channel_id . '][1]" id="' . network_ . $nw_count . _channel_ . $row_id . '_1' . '" value="' . $rate . '" onchange = "find_price(' . $row_id . ',' . $rows[$customer_id] . ',' . $nw_count . ');find_total_payout(' . $total_networks . ');" /> </td>';
                    } else {
                        $html .= '<td colspan="3"><input type="text" class="rate" ' . $readOnly . ' name="post_channel_avg_rate[' . $channel_id . '][1]" id="' . network_ . $nw_count . _channel_ . $row_id . '_1' . '" value="' . $rate . '" onchange = "find_price(' . $row_id . ',' . $rows[$customer_id] . ',' . $nw_count . ');find_total_payout(' . $total_networks . ');" /> </td>';
                    }

                    $html .= '<td colspan="3">' . round($ch['channel_banner_reference_rate'], 2) . '</td>
                             <input type="hidden" name="post_channel_ref_rate[' . $channel_id . '][1]" value="' . round($ch['channel_banner_reference_rate'], 2) . '" />';

                    if ($ch['approved'] == 1) {
                        $approved_total_amount += $ch['channel_banner_amount'];
                        $html .= '<td colspan="3" class="hide_' . $customer_id . '">' . round($ch['channel_banner_amount'], 2) . '</td>';
                        $html .= '<td colspan="3" class="show_' . $customer_id . '" style="display:none;"><input type="text" class="amount" ' . $readOnly . ' name="post_channel_amount[' . $channel_id . '][1]" id="' . network_ . $nw_count . _amount_ . $row_id . '_2' . '" value="' . round($amount, 2) . '" onchange = "find_rate(' . $row_id . ',' . $rows[$customer_id] . ',' . $nw_count . ');find_total_payout(' . $total_networks . ');" />
							<input type="hidden" name="hid_total_seconds[' . $channel_id . '][1]" value="' . $ch['total_banner_ad_seconds'] . '" /> 
							<input type="hidden" name="hid_client_name" value="' . $submit_ext_ro_data[0]['client'] . '" />
							<input type="hidden" name="hid_internal_ro" value="' . rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']), '=') . '" />
							<input type="hidden" name="hid_cust_id[' . $customer_id . ']" value="' . $customer_id . '" />';
                        $row_id++;
                        $html .= '<input type="hidden" name="total_rows" value="' . $rows[$customer_id] . '" /></td>';
                    } else {
                        $html .= '<td colspan="3"><input type="text" class="amount" ' . $readOnly . ' name="post_channel_amount[' . $channel_id . '][1]" id="' . network_ . $nw_count . _amount_ . $row_id . '_2' . '" value="' . round($amount, 2) . '" onchange = "find_rate(' . $row_id . ',' . $rows[$customer_id] . ',' . $nw_count . ');find_total_payout(' . $total_networks . ');" />
							<input type="hidden" name="hid_total_seconds[' . $channel_id . '][1]" value="' . $ch['total_banner_ad_seconds'] . '" /> 
							<input type="hidden" name="hid_client_name" value="' . $submit_ext_ro_data[0]['client'] . '" />
							<input type="hidden" name="hid_internal_ro" value="' . rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']), '=') . '" />
							<input type="hidden" name="hid_cust_id[' . $customer_id . ']" value="' . $customer_id . '" />';
                        $row_id++;
                        $html .= '<input type="hidden" name="total_rows" value="' . $rows[$customer_id] . '" /></td>';
                    }
                    $html .= '</tr>';
                }
            }
            $html .= '<tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
							<td colspan="3">Network RO Amount</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3" id="' . network_ . $nw_count . _total_amount . '" >';
            $html .= round($total_amount, 2);
            $html .= '</td>					
						</tr>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Network Share(%)</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>';
            if ($dat['approved'] == 1) {
                $html .= '<td colspan="3" class="hide_' . $customer_id . '">' . $dat['revenue_sharing'] . '</td>';
                $html .= '<td colspan="3" class="show_' . $customer_id . '" style="display:none;"><input type="text" class="network_share" ' . $readOnly . ' name="post_network_share[' . $customer_id . ']" id="' . network_ . $nw_count . _network_share . '" value="' . $dat['revenue_sharing'] . '"';
                if ($ch['cancel_channel'] == 'yes') {
                    $html .= " readonly='readonly'";
                }
                $html .= ' onchange ="find_final_amount(' . $nw_count . ',' . $total_networks . ');find_total_payout(' . $total_networks . ');" /> </td>';
            } else {
                $html .= '<td colspan="3"><input type="text" class="network_share" ' . $readOnly . '  name="post_network_share[' . $customer_id . ']" id="' . network_ . $nw_count . _network_share . '" value="' . $dat['revenue_sharing'] . '"';
                if ($ch['cancel_channel'] == 'yes') {
                    $html .= " readonly='readonly'";
                }
                $html .= ' onchange ="find_final_amount(' . $nw_count . ',' . $total_networks . ');find_total_payout(' . $total_networks . ');" /> </td>';
            }
            $html .= '</tr>
						<tr cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold;background-color: #fbfbfb;">
							<td colspan="3">Network Payout(Net)</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>';
            if ($dat['approved'] == 1) {
                $net_payout_loaded += round($total_amount * $dat['revenue_sharing'] / 100, 2);
                $html .= '<td colspan="3" class="hide_' . $customer_id . '">' . round($total_amount * $dat['revenue_sharing'] / 100, 2) . '</td>';
                $html .= '<td colspan="3" style="font-weight:bold;display:none;" class="show_' . $customer_id . '"><input type="text" class="final_amount" ' . $readOnly . ' name="post_final_amount[' . $customer_id . ']" id="' . network_ . $nw_count . _final_amount . '" value="' . round($dat['nw_payout'], 2) . '"';
                if ($ch['cancel_channel'] == 'yes') {
                    $html .= " readonly='readonly'";
                }
                $html .= ' onchange ="find_channel_amounts(' . $nw_count . ',' . $rows[$customer_id] . ');find_total_payout(' . $total_networks . ');" /> </td>';
            } else {
                $net_payout_loaded += round($total_amount * $dat['revenue_sharing'] / 100, 2);
                $html .= '<td colspan="3" style="font-weight:bold"><input type="text" class="final_amount" ' . $readOnly . ' name="post_final_amount[' . $customer_id . ']" id="' . network_ . $nw_count . _final_amount . '" value="' . round($dat['nw_payout'], 2) . '"';
                if ($ch['cancel_channel'] == 'yes') {
                    $html .= " readonly='readonly'";
                }
                $html .= ' onchange ="find_channel_amounts(' . $nw_count . ',' . $rows[$customer_id] . ');find_total_payout(' . $total_networks . ');" /> </td>';
            }
            $html .= '</tr></table>';
            if ($verify_ro_approved == 1) {
                $html .= '</form>';
            }
            $html .= '<iframe id="iframe_' . $dat['network_id'] . '" name="iframe_' . $dat['network_id'] . '" style="position: absolute; left: -1000px; top: -1000px;" src="about&#058;blank"></iframe>';

            $count++;
            $lastCount = $count;

            $nw_count++;
        }
        return $html . '~' . $net_payout_loaded;
    }

    public function create_html_am($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement)
    {
        $submit_ext_ro_details = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);
        $ro_amount_details = $this->mg_model->get_ro_amount($internal_ro_number);
        $ro_status_detail = $this->am_model->data_for_ro_id_in_ro_status($am_ro_id);
        $ro_status_entry = $this->am_model->convert_status_into_format($ro_status_detail[0]['ro_status']);
        $total_network_payout = $this->mg_model->get_total_network_payout(base64_decode($order_id));
        $ro_approval = $this->mg_model->get_approval_request_data(base64_decode($order_id));

        $cache_data = $this->memcache->get($am_ro_id);
        //$total_networks = count($key_data);
        foreach ($cache_data as $key => $dat) {
            $total_amount = 0;
            $total_rows = 0;
            foreach ($dat['channels_data_array'] as $chnl => $ch) {
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['network_id'];
                $customer_share = $ch['customer_share'];
                //foreach($ch['ad_types'] as $types => $ad) {
                if ($ch['total_spot_ad_seconds'] != 0) {
                    $total_rows++;
                    $total_amount += $ch['channel_spot_amount'];
                }
                if ($ch['total_banner_ad_seconds'] != 0) {
                    $total_rows++;
                    $total_amount += $ch['channel_banner_amount'];
                }

                //}
            }
            $rows[$customer_id] = $total_rows;
            $final_amount += $total_amount - $total_amount * (100 - $customer_share) / 100;
        }
        $html = '';
        $nw_count = $lastelement;
        foreach ($pagination_data as $key => $dat) {
            $total_networks = count($pagination_data);
            $row_id = 0;
            $html .= '<table id ="' . network_ . $key . '" cellpadding="0" cellspacing="0" width="100%">
										<tr cellpadding="0" cellspacing="0" width="100%" >
											<td colspan="4" style="font-weight:bold; size:20px; width:400px;">Network Name: ' . $dat['network_name'] . ' &nbsp; (' . $dat['market_name'] . ')</td>
										</tr>';


            $total_amount = 0;
            $approved_total_amount = 0;
            foreach ($dat['channels_data_array'] as $chnl => $ch) {
                $total_channel = count($dat['channels_data_array']);
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['network_id'];

                $html .= '<tr cellpadding="0" cellspacing="0" width="100%">
												<td colspan="3" style="font-weight:bold">Channel Name: ' . $ch['channel_name'] . '	   
												</td>
											</tr>
											<tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
												<th colspan="3">Ad Type</th>
												<th colspan="3">Total Ad Seconds</th>
											</tr>';


                if ($ch['total_spot_ad_seconds'] != 0) {
                    $amount = 0;
                    $rate = $ch['channel_spot_avg_rate'];
                    $amount += $ch['total_spot_ad_seconds'] * $rate / 10;
                    $total_amount += $amount;

                    $html .= '<tr cellpadding="0" cellspacing="0" width="100%">
												<td colspan="3">Spot Ad</td>
												<td colspan="3" id="' . network_ . $key . _total_sec_ . $row_id . '_0' . '" >' . $ch['total_spot_ad_seconds'] . '</td></tr>';
                }
                if ($ch['total_banner_ad_seconds'] != 0) {
                    $amount = 0;
                    $rate = $ch['channel_banner_avg_rate'];
                    $amount += $ch['total_banner_ad_seconds'] * $rate / 10;
                    $total_amount += $amount;
                    $html .= '<tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
													<td colspan="3">Banner Ad</td>
													<td colspan="3" id="' . network_ . $key . _total_sec_ . $row_id . '_0' . '" >' . $ch['total_banner_ad_seconds'] . '</td></tr>';
                }

            }

            $count++;
            $lastCount = $count;
            $html .= '</table>';

            $count++;
            $lastCount = $count;

            $nw_count++;
        }
        return $html;
    }

    public function create_html_finance($internal_ro_number, $pagination_data, $edit, $am_ro_id, $lastelement)
    {
        $submit_ext_ro_details = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);
        $ro_amount_details = $this->mg_model->get_ro_amount($internal_ro_number);
        $ro_status_detail = $this->am_model->data_for_ro_id_in_ro_status($am_ro_id);
        $ro_status_entry = $this->am_model->convert_status_into_format($ro_status_detail[0]['ro_status']);
        $total_network_payout = $this->mg_model->get_total_network_payout(base64_decode($order_id));
        $ro_approval = $this->mg_model->get_approval_request_data(base64_decode($order_id));

        $cache_data = $this->memcache->get($am_ro_id);
        //$total_networks = count($key_data);
        foreach ($cache_data as $key => $dat) {
            $total_amount = 0;
            $total_rows = 0;
            foreach ($dat['channels_data_array'] as $chnl => $ch) {
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['network_id'];
                $customer_share = $ch['customer_share'];
                //foreach($ch['ad_types'] as $types => $ad) {
                if ($ch['total_spot_ad_seconds'] != 0) {
                    $total_rows++;
                    $total_amount += $ch['channel_spot_amount'];
                }
                if ($ch['total_banner_ad_seconds'] != 0) {
                    $total_rows++;
                    $total_amount += $ch['channel_banner_amount'];
                }

                //}
            }
            $rows[$customer_id] = $total_rows;
            $final_amount += $total_amount - $total_amount * (100 - $customer_share) / 100;
        }
        $html = '';
        $nw_count = $lastelement;
        foreach ($pagination_data as $key => $dat) {
            $total_networks = count($pagination_data);
            $row_id = 0;
            $html .= '<table id ="' . network_ . $key . '" cellpadding="0" cellspacing="0" width="100%">
						<tr cellpadding="0" cellspacing="0" width="100%" >
							<td colspan="4" style="font-weight:bold;background:#DDDDDD;size:20px; width:400px;">Network Name: ' . $dat['network_name'] . ' &nbsp; (' . $dat['market_name'] . ')</td>
                            <td>&nbsp;</td>
						</tr>';
            $total_amount = 0;
            $approved_total_amount = 0;
            foreach ($dat['channels_data_array'] as $chnl => $ch) {
                $total_channel = count($dat['channels_data_array']);
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['network_id'];

                $html .= '<tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
                                                        <td colspan="3" style="font-weight:bold">Channel Name: ' . $ch['channel_name'] . '									
                                                        </td>
                                                </tr>
						 <tr cellpadding="0" cellspacing="0" width="100%">
                                                        <th colspan="3">Ad Type</th>
                                                        <th colspan="3">Total Ad Seconds</th>
                                                        <th colspan="3">Last Rate</th>
                                                        <th colspan="3">Reference Rate</th>
                                                        <th colspan="3">Amount</th>
                                                </tr>';
                if ($ch['total_spot_ad_seconds'] != 0) {
                    $amount = 0;
                    $rate = $ch['channel_spot_avg_rate'];
                    $amount += $ch['total_spot_ad_seconds'] * $rate / 10;
                    $total_amount += $amount;

                    $html .= '<tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
							<td colspan="3">Spot Ad</td>
							<td colspan="3" id="' . network_ . $key . _total_sec_ . $row_id . '_0' . '" >' . $ch['total_spot_ad_seconds'] . '</td>';


                    $html .= '<td colspan="3">' . $ch['channel_spot_avg_rate'] . '</td>';
                    $html .= '<td colspan="3">' . round($ch['channel_spot_reference_rate'], 2) . '</td>';
                    $approved_total_amount += $ch['channel_spot_amount'];
                    $html .= '<td colspan="3">' . round($ch['channel_spot_amount'], 2) . '</td>
                                                        
						</tr>';
                }
                if ($ch['total_banner_ad_seconds'] != 0) {
                    $amount = 0;
                    $rate = $ch['channel_banner_avg_rate'];
                    $amount += $ch['total_banner_ad_seconds'] * $rate / 10;
                    $total_amount += $amount;

                    $html .= '<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Banner Ad</td>
							<td colspan="3" id="' . network_ . $key . _total_sec_ . $row_id . '_0' . '" >' . $ch['total_banner_ad_seconds'] . '</td>';


                    $html .= '<td colspan="3">' . $ch['channel_banner_avg_rate'] . '</td>';
                    $html .= '<td colspan="3">' . round($ch['channel_banner_reference_rate'], 2) . '</td>';
                    $approved_total_amount += $ch['channel_banner_amount'];
                    $html .= '<td colspan="3">' . round($ch['channel_banner_amount'], 2) . '</td>
                                                        
						</tr>';
                }
            }
            $html .= '<tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #fbfbfb;">
							<td colspan="3">Network RO Amount</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3" id="' . network_ . $key . _total_amount . '" >';
            $html .= round($total_amount, 2);

            $html .= '</td>					
						</tr>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Network Share(%)</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							
							<td colspan="3">' . $dat['revenue_sharing'] . '</td>
                                                       
						</tr>
						<tr cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold;background-color: #fbfbfb;">
							<td colspan="3">Network Payout(Net)</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">' . round($total_amount * $dat['revenue_sharing'] / 100, 2) . '</td>
                                                        
						</tr></table>';


            $count++;
            $lastCount = $count;
            $nw_count++;
        }
        return $html;
    }

    public function approve_old($order_id, $edit = 0, $am_ro_id = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $this->session->set_userdata('order_id', $order_id);
        // To find details about RO
        $order = $this->ro_model->get_ro_details(base64_decode($order_id));
        // @mani:visible in easy ro=1
        $ro_order = $this->ro_model->get_ro_data(base64_decode($order_id));

        //TO find campaigns belong to above RO
        $order_ids = array();
        array_push($order_ids, base64_decode($order_id));
        //@mani:getting screen region id for given ro
        $selected_region_ids = $this->mg_model->get_screen_region_ids(base64_decode($order_id));
        $region_ids = array();
        foreach ($selected_region_ids as $region) {
            array_push($region_ids, $region['selected_region_id']);
        }
        $ro_amount_details = $this->mg_model->get_ro_amount(base64_decode($order_id));
        $total_network_payout = $this->mg_model->get_total_network_payout(base64_decode($order_id));
        $ro_approval = $this->mg_model->get_approval_request_data(base64_decode($order_id));
        foreach ($region_ids as $key => $id) {
            $campaigns[$key] = $this->mg_model->get_order_campaigns(implode(',', $order_ids), $id);

            $camp_ids[$key] = array();
            foreach ($campaigns[$key] as $camp) {
                array_push($camp_ids[$key], $camp['campaign_id']);
            }

            //To find one which channels these campaigns are running
            $channels_summary[$key] = $this->mg_model->get_channel_schedule_summary(implode(',', $camp_ids[$key]), $id);
            $channels[$key] = array();
            foreach ($channels_summary[$key] as $chanl) {
                array_push($channels[$key], $chanl['channel_name']);
            }
            $channel_ids[$key] = array();
            foreach ($channels_summary[$key] as $chanl) {
                array_push($channel_ids[$key], $chanl['channel_id']);
            }
            $result[$key] = array();
            foreach ($channels_summary[$key] as $key1 => $value) {
                $result[$key][$key1] = $value['internal_ro_number'] . '~' . $value['client_name'] . '~' . $value['customer_name'] . '~' . $value['customer_id'] . '~' . $value['customer_location'] . '~' . $value['channel_name'] . '~@' . $value['channel_id'] . '~~' . $value['ro_duration'] * $value['timp'];
            }

            //Bug#463: Use service tax calculations on the approval page for calculating SureWaves Net Contribution
            foreach ($result[$key] as $result1) {

                $spot_rows = count($result[$key]);
                for ($i = 0; $i < $spot_rows;) {
                    $x = explode("~@", $result[$key][$i]);
                    $y = explode("~~", $x[1]);
                    $total = 0;
                    for ($j = 0; $j < $spot_rows; $j++) {
                        $first = explode("~@", $result[$key][$j]);
                        $second = explode("~~", $first[1]);
                        if ($y[0] == $second[0]) {
                            $total += $second[1];
                            $i++;
                        }
                    }
                    $res1[$key][$i] = $x[0] . '~@' . $y[0] . '~~' . $total;
                }
            }
            $res[$key] = array();
            foreach ($res1[$key] as $re) {
                $res[$key][] = $re;
            }
        }
        $value = 0;
        foreach ($channels_summary as $key => $ch) {
            foreach ($ch as $key1 => $c) {
                if (empty($c)) {
                    $value = 0;
                } else {
                    $value = 1;
                }
            }
        }

        //ReviewComment(Kiran): Prefetch all the values and do the calculation in controller not in pdf
        $model = array();
        if ($value == 0) {
            //@mani:no fpc or no inventory or all campaign are cancelled for particulal ros
            //$model = array();
            $model['edit'] = $edit;
            $model['id'] = $am_ro_id;
            $model['logged_in_user'] = $logged_in[0];
            $model['ro_details'] = $ro_amount_details;
            $model['profile_id'] = $logged_in[0]['profile_id'];
            $model['content'] = $ro_order[0];

            //$am_ext_ro = $this->ro_model->get_am_ro_id($id) ;
            $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
            $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];

            //Cancel Approval sent request by AM
            $cancel_request_sent = $this->am_model->is_cancel_request_sent($am_ro_id);
            if (count($cancel_request_sent) > 0) {
                $model['cancel_request_sent_by_am'] = 1;
            } else {
                $model['cancel_request_sent_by_am'] = 0;
            }

            //Cancel Sent by BH/COO
            if (isset($cancel_request_sent[0]['cancel_ro_by_admin']) && $cancel_request_sent[0]['cancel_ro_by_admin'] == 1) {
                $model['cancel_request_sent_by_admin'] = 1;
            } else {
                $model['cancel_request_sent_by_admin'] = 0;
            }

            $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
            $model['agency'] = $am_ext_ro[0]['agency'];
            $model['client'] = $am_ext_ro[0]['client'];
            $model['brand'] = $am_ext_ro[0]['brand_name'];
            $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
            $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];

            //changed by mani to get campaign status
            $ro_status_detail = $this->am_model->data_for_ro_id_in_ro_status($am_ro_id);
            $ro_status = $this->am_model->convert_status_into_format($ro_status_detail[0]['ro_status']);
            $model['ro_status_entry'] = $ro_status;

            $model['ro_approval'] = $ro_approval;
            $model['value'] = $value;
            //echo $model['am_ext_ro'];exit;

            // added by lokanath to check if campaign is created for this ro or not (used for visibility of "approval request" for scheduler)
            $campaign_created = 0;
            $verify_campaign_created = $this->am_model->verify_campaign_created_for_internal_ro($model['internal_ro']);
            if (count($verify_campaign_created) > 0) {
                $campaign_created = 1;
            }
            $model['campaign_created'] = $campaign_created;

            // end
            //$this->load->view('ro_manager/approve',$model);

        } elseif ($value == 1) {
            $region_ids_count = count($region_ids);
            // @mani:2 verifying only for spot ad and banner ad
            if ($region_ids_count == 2) {
                foreach ($res as $key => $re) {
                    //change by mani:1.10.2013,adding spot and banner region
                    if ($key == 0) {
                        $ad_type = 'S';
                    } else if ($key == 1) {
                        $ad_type = 'B';
                    }
                    foreach ($re as $key1 => $r) {
                        //$temp [] = $r ;
                        $temp [] = $r . '##' . $ad_type;
                    }
                }
                $count = count($temp);
                for ($i = 0; $i < $count; $i++) {
                    $x = explode('~@', $temp[$i]);
                    $y = explode('~~', $x[1]);
                    for ($j = $i + 1; $j < $count; $j++) {
                        $first = explode('~@', $temp[$j]);
                        $second = explode('~~', $first[1]);
                        if ($y[0] == $second[0]) {
                            $spot = explode("##", $y[1]);
                            $banner = explode("##", $second[1]);

                            //$final_result[$y[0]] = $x[0].'~'.$y[0].'~@'.$y[1].'~~'.$second[1];
                            $final_result[$y[0]] = $x[0] . '~' . $y[0] . '~@' . $spot[0] . '~~' . $banner[0];
                            $j = $count;
                        } elseif ($j == $count - 1 && !isset($final_result[$y[0]])) {
                            //$final_result[$y[0]] = $x[0].'~'.$y[0].'~@'.$y[1];
                            $region_val = explode("##", $y[1]);
                            if ($region_val[1] == 'S') {
                                $final_result[$y[0]] = $x[0] . '~' . $y[0] . '~@' . $region_val[0];
                            } else if ($region_val[1] == 'B') {
                                $final_result[$y[0]] = $x[0] . '~' . $y[0] . '~@~~' . $region_val[0];
                            }

                        }
                    }
                }
                //for last value
                $x = explode('~@', $temp[$count - 1]);
                $y = explode('~~', $x[1]);
                $region_val = explode("##", $y[1]);
                if (!array_key_exists($y[0], $final_result)) {
                    if ($region_val[1] == 'S') {
                        $final_result[$y[0]] = $x[0] . '~' . $y[0] . '~@' . $region_val[0];
                    } else if ($region_val[1] == 'B') {
                        $final_result[$y[0]] = $x[0] . '~' . $y[0] . '~@~~' . $region_val[0];
                    }
                }
                $final_summary = array();
                $i = 0;
                foreach ($final_result as $key => $res) {
                    $first = explode("~@", $res);
                    $second = explode("~", $first[0]);
                    $third = explode("~~", $first[1]);
                    $final_summary[$i]['internal_ro_number'] = $second[0];
                    $final_summary[$i]['client_name'] = $second[1];
                    $final_summary[$i]['customer_name'] = $second[2];
                    $final_summary[$i]['customer_id'] = $second[3];
                    $final_summary[$i]['customer_location'] = $second[4];
                    $final_summary[$i]['channel_name'] = $second[5];
                    $final_summary[$i]['channel_id'] = $second[6];
                    $final_summary[$i]['total_spot_ad_seconds'] = $third[0];
                    $final_summary[$i]['total_banner_ad_seconds'] = $third[1];
                    $i++;
                }
                //changed by mani,the value which is not set
                $final_data = array();
                for ($i = 0; $i < count($final_summary); $i++) {
                    $tmp = array();
                    foreach ($final_summary[$i] as $key => $val) {
                        if (isset($val) && !empty($val)) {
                            $tmp[$key] = $val;
                        }
                    }
                    array_push($final_data, $tmp);
                }
                $final_summary = $final_data;
                $data = array();
                $j = 0;
                $cust_id_prev = 0;
                foreach ($final_summary as $summary) {
                    $cust_id_cur = 0;
                    $cust_id_cur = $summary['customer_id'];
                    $i = 0;
                    $k = 0;
                    if ($cust_id_prev != $cust_id_cur) {
                        foreach ($final_summary as $sum) {
                            if ($cust_id_cur == $sum['customer_id']) {
                                if ($i == 0) {
                                    $data[$j]['internal_ro_number'] = $sum['internal_ro_number'];
                                    $data[$j]['client_name'] = $sum['client_name'];
                                    $data[$j]['customer_id'] = $sum['customer_id'];
                                    $data[$j]['customer_name'] = $sum['customer_name'];
                                    $data[$j]['customer_location'] = $sum['customer_location'];
                                    $data[$j]['channels'] [$i] ['channel_id'] = $sum['channel_id'];
                                    $data[$j]['channels'] [$i] ['channel_name'] = $sum['channel_name'];
                                    if (isset($sum['total_spot_ad_seconds'])) {
                                        $data[$j]['channels'] [$i] ['ad_types'][$k]['ad_type'] = 'Spot Ad';
                                        $data[$j]['channels'] [$i] ['ad_types'][$k]['total_ad_seconds'] = $sum['total_spot_ad_seconds'];
                                    }
                                    if (isset($sum['total_banner_ad_seconds'])) {
                                        $data[$j]['channels'] [$i] ['ad_types'][$k + 1]['ad_type'] = 'Banner Ad';
                                        $data[$j]['channels'] [$i] ['ad_types'][$k + 1]['total_ad_seconds'] = $sum['total_banner_ad_seconds'];
                                    }
                                    $i++;
                                } else {
                                    $data[$j]['channels'][$i]['channel_id'] = $sum['channel_id'];
                                    $data[$j]['channels'][$i]['channel_name'] = $sum['channel_name'];
                                    if (isset($sum['total_spot_ad_seconds'])) {
                                        $data[$j]['channels'] [$i] ['ad_types'][$k]['ad_type'] = 'Spot Ad';
                                        $data[$j]['channels'][$i]['ad_types'][$k]['total_ad_seconds'] = $sum['total_spot_ad_seconds'];
                                    }
                                    if (isset($sum['total_banner_ad_seconds'])) {
                                        $data[$j]['channels'] [$i] ['ad_types'][$k + 1]['ad_type'] = 'Banner Ad';
                                        $data[$j]['channels'][$i]['ad_types'][$k + 1]['total_ad_seconds'] = $sum['total_banner_ad_seconds'];
                                    }
                                    $i++;
                                }
                            }
                        }
                        $j++;
                    }
                    $cust_id_prev = $cust_id_cur;
                }
                $chnl_ids = array();
                foreach ($final_summary as $s) {
                    array_push($chnl_ids, $s['channel_id']);
                }
                $internal_ro_number = base64_decode($order_id);
                foreach ($chnl_ids as $channel_id) {
                    $approved_channel_details[$channel_id] = $this->mg_model->get_approved_channel_details($internal_ro_number, $channel_id);
                }
                $post_data = array();
                foreach ($approved_channel_details as $key => $channels) {
                    $j = 0;
                    $i = 0;
                    $post_data[$key]['internal_ro_number'] = $channels[0]['internal_ro_number'];
                    $post_data[$key]['client_name'] = $channels[0]['client_name'];
                    $post_data[$key]['customer_id'] = $channels[0]['customer_id'];
                    $post_data[$key]['customer_name'] = $channels[0]['customer_name'];
                    $post_data[$key]['customer_share'] = $channels[0]['customer_share'];
                    $post_data[$key]['customer_location'] = $channels[0]['customer_location'];
                    $post_data[$key]['channel_id'] = $channels[0]['tv_channel_id'];
                    $post_data[$key]['channel_name'] = $channels[0]['channel_name'];
                    $post_data[$key]['channel_approval_status'] = $channels[0]['channel_approval_status'];
                    $post_data[$key]['ad_types'][$i]['ad_type'] = 'Spot Ad';
                    $post_data[$key]['ad_types'][$i]['channel_avg_rate'] = $channels[0]['channel_spot_avg_rate'];
                    $post_data[$key]['ad_types'][$i]['total_ad_seconds'] = $channels[0]['total_spot_ad_seconds'];
                    $post_data[$key]['ad_types'][$i]['amount'] = $channels[0]['channel_spot_amount'];
                    $post_data[$key]['ad_types'][$i + 1]['ad_type'] = 'Banner Ad';
                    $post_data[$key]['ad_types'][$i + 1]['channel_avg_rate'] = $channels[0]['channel_banner_avg_rate'];
                    $post_data[$key]['ad_types'][$i + 1]['total_ad_seconds'] = $channels[0]['total_banner_ad_seconds'];
                    $post_data[$key]['ad_types'][$i + 1]['amount'] = $channels[0]['channel_banner_amount'];
                }
                $nw_ids = array();
                foreach ($final_summary as $s) {
                    array_push($nw_ids, $s['customer_id']);
                }
                $nw_ids = array_unique($nw_ids);
                $channel_rates = $this->mg_model->get_channel_rates(implode(',', $chnl_ids));
                $network_shares = $this->mg_model->get_network_shares(implode(',', $nw_ids));
                $network_share_details = array();
                foreach ($network_shares as $nw) {
                    $network_share_details[$nw['customer_id']] = $nw['customer_share'];
                }
                $channel_rates_details = array();
                foreach ($channel_rates as $ch) {
                    $channel_rates_details[$ch['tv_channel_id']]['Spot Ad'] = $ch['channel_spot_avg_rate'];
                    $channel_rates_details[$ch['tv_channel_id']]['Banner Ad'] = $ch['channel_banner_avg_rate'];
                }
                $channel_avg_rate = array();
                $channel_avg_rates = array();
                foreach ($data as $key => $dat) {
                    $client_name = $dat['client_name'];
                    foreach ($dat['channels'] as $chnl => $ch) {
                        $channel_name = $ch['channel_name'];
                        $channel_id = $ch['channel_id'];
                        $channel_avg_rate[$channel_id] = $this->mg_model->get_channel_avg_rate($client_name, $channel_name);
                        $channel_avg_rates[$channel_id] = $this->mg_model->get_channel_avg_rates($channel_name);
                    }
                }
                $chnl_avg_rate = array();
                foreach ($channel_avg_rate as $key => $ch) {
                    $chnl_avg_rate[$key]['Spot Ad'] = $ch[0]['channel_spot_avg_rate'];
                    $chnl_avg_rate[$key]['Banner Ad'] = $ch[0]['channel_banner_avg_rate'];
                }
                $chnl_avg_rates = array();
                foreach ($channel_avg_rates as $key => $ch) {
                    $chnl_avg_rates[$key]['Spot Ad'] = $ch[0]['channel_spot_avg_rate'];
                    $chnl_avg_rates[$key]['Banner Ad'] = $ch[0]['channel_banner_avg_rate'];
                }
                $historical_rates = array();
                foreach ($data as $key => $dat) {
                    foreach ($dat['channels'] as $chnl => $ch) {
                        $channel_id = $ch['channel_id'];
                        if ($chnl_avg_rate[$channel_id]['Spot Ad'] != 0) {
                            $historical_rates[$channel_id]['Spot Ad'] = $chnl_avg_rate[$channel_id]['Spot Ad'];
                        } elseif ($chnl_avg_rates[$channel_id]['Spot Ad'] != 0) {
                            $historical_rates[$channel_id]['Spot Ad'] = $chnl_avg_rates[$channel_id]['Spot Ad'];
                        } else {
                            $historical_rates[$channel_id]['Spot Ad'] = $channel_rates_details[$channel_id]['Spot Ad'];
                        }
                        if ($chnl_avg_rate[$channel_id]['Banner Ad'] != 0) {
                            $historical_rates[$channel_id]['Banner Ad'] = $chnl_avg_rate[$channel_id]['Banner Ad'];
                        } elseif ($chnl_avg_rates[$channel_id]['Banner Ad'] != 0) {
                            $historical_rates[$channel_id]['Banner Ad'] = $chnl_avg_rates[$channel_id]['Banner Ad'];
                        } else {
                            $historical_rates[$channel_id]['Banner Ad'] = $channel_rates_details[$channel_id]['Banner Ad'];
                        }
                    }
                }
            } else {
                $final_result = array();
                $final_result = $res[$key];
                $final_summary = array();
                $i = 0;
                foreach ($final_result as $f) {
                    $x = explode("~@", $f);
                    $y = explode("~", $x[0]);
                    $z = explode("~~", $x[1]);
                    $final_summary[$i]['internal_ro_number'] = $y[0];
                    $final_summary[$i]['client_name'] = $y[1];
                    $final_summary[$i]['customer_name'] = $y[2];
                    $final_summary[$i]['customer_id'] = $y[3];
                    $final_summary[$i]['customer_location'] = $y[4];
                    $final_summary[$i]['channel_id'] = $z[0];
                    $final_summary[$i]['channel_name'] = $y[5];
                    $final_summary[$i]['total_ad_seconds'] = $z[1];
                    $i++;
                }
                $region_id = $region_ids[0][0];
                if ($region_id == 1) {
                    $ad_type = 'Spot Ad';
                } elseif ($region_id == 3) {
                    $ad_type = 'Banner Ad';
                }
                $data = array();
                $j = 0;
                $cust_id_prev = 0;
                foreach ($final_summary as $r) {
                    $cust_id_cur = 0;
                    $cust_id_cur = $r['customer_id'];
                    $i = 0;
                    $k = 0;
                    if ($cust_id_prev != $cust_id_cur) {
                        foreach ($final_summary as $s) {
                            if ($cust_id_cur == $s['customer_id']) {
                                if ($i == 0) {
                                    $data[$j]['internal_ro_number'] = $s['internal_ro_number'];
                                    $data[$j]['client_name'] = $s['client_name'];
                                    $data[$j]['customer_id'] = $s['customer_id'];
                                    $data[$j]['customer_name'] = $s['customer_name'];
                                    $data[$j]['customer_location'] = $s['customer_location'];
                                    $data[$j]['channels'] [$i] ['channel_id'] = $s['channel_id'];
                                    $data[$j]['channels'] [$i] ['channel_name'] = $s['channel_name'];
                                    $data[$j]['channels'] [$i] ['ad_types'][$k]['ad_type'] = $ad_type;;
                                    $data[$j]['channels'] [$i] ['ad_types'][$k]['total_ad_seconds'] = $s['total_ad_seconds'];
                                    $i++;
                                } else {
                                    $data[$j]['channels'] [$i] ['channel_id'] = $s['channel_id'];
                                    $data[$j]['channels'] [$i] ['channel_name'] = $s['channel_name'];
                                    $data[$j]['channels'] [$i] ['ad_types'][$k]['ad_type'] = $ad_type;;
                                    $data[$j]['channels'] [$i] ['ad_types'][$k]['total_ad_seconds'] = $s['total_ad_seconds'];
                                    $i++;
                                }
                            }
                        }
                        $j++;
                    }
                    $cust_id_prev = $cust_id_cur;
                }
                $chnl_ids = array();
                foreach ($final_summary as $s) {
                    array_push($chnl_ids, $s['channel_id']);
                }
                $internal_ro_number = base64_decode($order_id);
                foreach ($chnl_ids as $channel_id) {
                    $approved_channel_details[$channel_id] = $this->mg_model->get_approved_channel_details($internal_ro_number, $channel_id);
                }
                $post_data = array();
                foreach ($approved_channel_details as $key => $channels) {
                    $j = 0;
                    $i = 0;
                    $post_data[$key]['internal_ro_number'] = $channels[0]['internal_ro_number'];
                    $post_data[$key]['client_name'] = $channels[0]['client_name'];
                    $post_data[$key]['customer_id'] = $channels[0]['customer_id'];
                    $post_data[$key]['customer_name'] = $channels[0]['customer_name'];
                    $post_data[$key]['customer_share'] = $channels[0]['customer_share'];
                    $post_data[$key]['customer_location'] = $channels[0]['customer_location'];
                    $post_data[$key]['channel_id'] = $channels[0]['tv_channel_id'];
                    $post_data[$key]['channel_name'] = $channels[0]['channel_name'];
                    $post_data[$key]['channel_approval_status'] = $channels[0]['channel_approval_status'];
                    $post_data[$key]['ad_types'][$i]['ad_type'] = $ad_type;
                    if ($region_id == 1) {
                        $post_data[$key]['ad_types'][$i]['channel_avg_rate'] = $channels[0]['channel_spot_avg_rate'];
                        $post_data[$key]['ad_types'][$i]['total_ad_seconds'] = $channels[0]['total_spot_ad_seconds'];
                        $post_data[$key]['ad_types'][$i]['amount'] = $channels[0]['channel_spot_amount'];
                    }
                    if ($region_id == 3) {
                        $post_data[$key]['ad_types'][$i]['channel_avg_rate'] = $channels[0]['channel_banner_avg_rate'];
                        $post_data[$key]['ad_types'][$i]['total_ad_seconds'] = $channels[0]['total_banner_ad_seconds'];
                        $post_data[$key]['ad_types'][$i]['amount'] = $channels[0]['channel_banner_amount'];
                    }
                }
                $nw_ids = array();
                foreach ($final_summary as $s) {
                    array_push($nw_ids, $s['customer_id']);
                }
                $nw_ids = array_unique($nw_ids);
                $channel_rates = $this->mg_model->get_channel_rates(implode(',', $chnl_ids));
                $network_shares = $this->mg_model->get_network_shares(implode(',', $nw_ids));
                $network_share_details = array();
                foreach ($network_shares as $nw) {
                    $network_share_details[$nw['customer_id']] = $nw['customer_share'];
                }
                $channel_rates_details = array();
                foreach ($channel_rates as $key => $ch) {
                    $channel_rates_details[$ch['tv_channel_id']]['Spot Ad'] = $ch['channel_spot_avg_rate'];
                    $channel_rates_details[$ch['tv_channel_id']]['Banner Ad'] = $ch['channel_banner_avg_rate'];
                }
                $channel_avg_rate = array();
                $channel_avg_rates = array();
                foreach ($data as $key => $dat) {
                    $client_name = $dat['client_name'];
                    foreach ($dat['channels'] as $chnl => $ch) {
                        $channel_name = $ch['channel_name'];
                        $channel_id = $ch['channel_id'];
                        $channel_avg_rate[$channel_id] = $this->mg_model->get_channel_avg_rate($client_name, $channel_name);
                        $channel_avg_rates[$channel_id] = $this->mg_model->get_channel_avg_rates($channel_name);
                    }
                }
                $chnl_avg_rate = array();
                foreach ($channel_avg_rate as $key => $ch) {
                    $chnl_avg_rate[$key]['Spot Ad'] = $ch[0]['channel_spot_avg_rate'];
                    $chnl_avg_rate[$key]['Banner Ad'] = $ch[0]['channel_banner_avg_rate'];
                }
                $chnl_avg_rates = array();
                foreach ($channel_avg_rates as $key => $ch) {
                    $chnl_avg_rates[$key]['Spot Ad'] = $ch[0]['channel_spot_avg_rate'];
                    $chnl_avg_rates[$key]['Banner Ad'] = $ch[0]['channel_banner_avg_rate'];
                }
                $historical_rates = array();
                foreach ($data as $key => $dat) {
                    foreach ($dat['channels'] as $chnl => $ch) {
                        $channel_id = $ch['channel_id'];
                        if ($chnl_avg_rate[$channel_id]['Spot Ad'] != 0) {
                            $historical_rates[$channel_id]['Spot Ad'] = $chnl_avg_rate[$channel_id]['Spot Ad'];
                        } elseif ($chnl_avg_rates[$channel_id]['Spot Ad'] != 0) {
                            $historical_rates[$channel_id]['Spot Ad'] = $chnl_avg_rates[$channel_id]['Spot Ad'];
                        } else {
                            $historical_rates[$channel_id]['Spot Ad'] = $channel_rates_details[$channel_id]['Spot Ad'];
                        }
                        if ($chnl_avg_rate[$channel_id]['Banner Ad'] != 0) {
                            $historical_rates[$channel_id]['Banner Ad'] = $chnl_avg_rate[$channel_id]['Banner Ad'];
                        } elseif ($chnl_avg_rates[$channel_id]['Banner Ad'] != 0) {
                            $historical_rates[$channel_id]['Banner Ad'] = $chnl_avg_rates[$channel_id]['Banner Ad'];
                        } else {
                            $historical_rates[$channel_id]['Banner Ad'] = $channel_rates_details[$channel_id]['Banner Ad'];
                        }
                    }
                }

            }
            //make array unique
            $data = $this->make_array_unique($data);
            //changed by mani,for inserting demographics
            //BUG#462 channels should be sorted by Market/MSO Wise
            $data = $this->insert_demographics_name($data);
            $data = $this->sort_by_market($data);

            //filter channel which is being cancelled
            $cancel_channel = $this->mg_model->get_cancel_channel_for_internal_ro(base64_decode($order_id));
            $data_val = array();
            foreach ($data as $val) {
                $tmp = array();
                $tmp['internal_ro_number'] = $val['internal_ro_number'];
                $tmp['client_name'] = $val['client_name'];
                $tmp['customer_id'] = $val['customer_id'];
                $tmp['customer_name'] = $val['customer_name'];
                $tmp['customer_location'] = $val['customer_location'];
                $tmp['channels'] = $this->mg_model->filter_schedule_channel($val['channels'], $cancel_channel);
                $tmp['market_name'] = $val['market_name'];
                array_push($data_val, $tmp);
            }
            $data = array();
            $data = $data_val;

            //$model=array();
            $model['edit'] = $edit;
            $model['id'] = $am_ro_id;
            $model['logged_in_user'] = $logged_in[0];
            $model['value'] = 1;
            $model['ro_details'] = $ro_amount_details;
            $model['profile_id'] = $logged_in[0]['profile_id'];
            $model['content'] = $ro_order[0];
            //$am_ext_ro = $this->ro_model->get_am_ro_id($am_ro_id) ;
            //$am_ext_ro = $this->ro_model->get_am_ro_id($id) ;
            $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
            $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];

            $cancel_request_sent = $this->am_model->is_cancel_request_sent($am_ro_id);
            if (count($cancel_request_sent) > 0) {
                $model['cancel_request_sent_by_am'] = 1;
            } else {
                $model['cancel_request_sent_by_am'] = 0;
            }
            if (isset($cancel_request_sent[0]['cancel_ro_by_admin']) && $cancel_request_sent[0]['cancel_ro_by_admin'] == 1) {
                $model['cancel_request_sent_by_admin'] = 1;
            } else {
                $model['cancel_request_sent_by_admin'] = 0;
            }
            //changed by mani to get campaign status
            $ro_status_detail = $this->am_model->data_for_ro_id_in_ro_status($am_ro_id);
            $ro_status = $this->am_model->convert_status_into_format($ro_status_detail[0]['ro_status']);
            $model['ro_status_entry'] = $ro_status;

            $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
            $model['agency'] = $am_ext_ro[0]['agency'];
            $model['client'] = $am_ext_ro[0]['client'];
            $model['brand'] = $am_ext_ro[0]['brand_name'];
            $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
            $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];
            $model['page_links'] = "";
            $model['campaigns'] = $campaigns;
            $model['channels'] = $channels;
            $model['order_id'] = $order_id;
            $model['approved_channel_details'] = $post_data;
            $model['channel_summary'] = $channels_summary;

            set_cache($am_ro_id, $data);

            $model['data'] = array_slice($data, 0, 1);
            $model['channel_ids'] = $chnl_ids;
            $model['historical_avg_rates'] = $historical_rates;
            $model['channel_rates_details'] = $channel_rates_details;
            $model['channel_avg_rate'] = $chnl_avg_rate;
            $model['channel_avg_rates'] = $chnl_avg_rates;
            $model['network_share_details'] = $network_share_details;
            $model['ro_approval'] = $ro_approval;
            $model['total_network_payout'] = $total_network_payout[0];
            //echo $model['am_ext_ro'];exit;

            // added by lokanath to check if campaign is created for this ro or not (used for visibility of "approval request" for scheduler)
            $campaign_created = 0;
            $verify_campaign_created = $this->am_model->verify_campaign_created_for_internal_ro($model['internal_ro']);
            if (count($verify_campaign_created) > 0) {
                $campaign_created = 1;
            }
            $model['campaign_created'] = $campaign_created;

            // end
            //$this->load->view('/ro_manager/approve',$model);

        }

        // checking if the RO is approved or not
        $ro_approved = 0;
        $verify_ro_approved = $this->am_model->verify_ro_status($am_ro_id);
        if (count($verify_ro_approved) > 0) {
            $ro_approved = 1;
        }
        $model['verify_ro_approved'] = $ro_approved;
        // end checking

        $this->load->view('/ro_manager/approve', $model);
    }

    public function make_array_unique($data)
    {
        $aux_data = array();
        $arrange_data = array();
        for ($n = 0; $n < count($data); $n++) {
            $aux_data[] = serialize($data[$n]);
        }
        $mat = array_unique($aux_data);
        for ($n = 0; $n < count($data); $n++) {

            $arrange_data[] = unserialize($mat[$n]);

        }
        $final_data = array();
        foreach ($arrange_data as $val) {
            if (!isset($val) || empty($val)) continue;
            else array_push($final_data, $val);
        }
        return $final_data;
    }

    public function insert_demographics_name($data)
    {
        //$final_data = array()
        foreach ($data as &$val) {
            $cid = $val['customer_id'];
            $market_name = $this->ro_model->get_market_name($cid);
            $val['market_name'] = $market_name;
        }
        unset($val);
        return $data;
    }

    public function approval_pagination_old($edit, $am_ro_id, $approved_channel_details, $network_share_details)
    {
        // get all data
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $verify_ro_approved = 0;
        $chk_verify_ro_approved = $this->am_model->verify_ro_status($am_ro_id);
        if (count($chk_verify_ro_approved) > 0) {
            $verify_ro_approved = 1;
        }
        $ro_status_detail = $this->am_model->data_for_ro_id_in_ro_status($am_ro_id);
        $ro_status_entry = $this->am_model->convert_status_into_format($ro_status_detail[0]['ro_status']);

        $lastelement = $_REQUEST['lastelement'];
        $key_data = $fetch_cache_data($am_ro_id);

        $total_networks = count($key_data);
        foreach ($data as $key => $dat) {
            $total_amount = 0;
            $total_rows = 0;
            foreach ($dat['channels'] as $chnl => $ch) {
                $channel_id = $ch['channel_id'];
                $customer_id = $dat['customer_id'];
                foreach ($ch['ad_types'] as $types => $ad) {
                    $total_rows++;
                    $rate = $historical_avg_rates[$channel_id][$ad['ad_type']];
                    $total_amount += $ad['total_ad_seconds'] * $rate / 10;
                }
            }
            $rows[$customer_id] = $total_rows;
            $final_amount += $total_amount - $total_amount * (100 - $network_share_details[$customer_id]) / 100;
        }
        // end getting all data

        if ($lastelement != '') {
            $output = array_slice($key_data, $lastelement, 1);
            if (count($output) > 0) {
                $count = $lastelement;//echo $count;
                foreach ($output as $key => $dat) {
                    $internal_ro = $dat['internal_ro_number'];
                    //echo '<li>'.$count.'</li>' ;
                    $table_html = '<table id ="<?php echo network_.$key ?>" cellpadding="0" cellspacing="0" width="100%">
						<tr cellpadding="0" cellspacing="0" width="100%" >
							<td colspan="4" style="font-weight:bold;background:#DDDDDD;size:20px; width:400px;">Network Name: ' . $dat['customer_name'] . ' &nbsp; (' . $dat['market_name'] . ')</td>
                            <td>';
                    if ($profile_id == 1 || $profile_id == 2) {
                        if ($verify_ro_approved == 1) {
                            $table_html .= '<a href="javascript:edit_network(\'' . rtrim(base64_encode($internal_ro), '=') . '\',\'' . $dat['customer_id'] . '\',\'' . $edit . '\',\'' . $am_ro_id . '\')">
									<input type="button" value="Edit" class="submit">
									</a>';
                        }
                        if ($ro_status_entry != 'Cancel Approved' && $ro_status_entry != 'Cancelled') {
                            $table_html .= '<a href="javascript:cancel_nw_channel(\'' . rtrim(base64_encode($internal_ro), '=') . '\',\'' . $dat['customer_id'] . '\',\'' . $edit . '\',\'' . $am_ro_id . '\')">
									<input type="button" value="Cancel Channel" class="cancellong">
									</a>';
                        } else {
                            $table_html .= '&nbsp;';
                        }
                    }
                    $table_html .= '</td>
						</tr>';
                    $total_amount = 0;
                    $approved_total_amount = 0;
                    foreach ($dat['channels'] as $chnl => $ch) {
                        $total_channel = count($dat['channels']);
                        $channel_id = $ch['channel_id'];
                        $customer_id = $dat['customer_id'];

                        $table_html .= '<tr cellpadding="0" cellspacing="0" width="100%">
                                                        <td colspan="3" style="font-weight:bold">Channel Name: ' . $ch['channel_name'];
                        /*if($approved_channel_details[$channel_id]['channel_approval_status'] != 1 && ($profile_id == 1 or $profile_id == 2)){
														if($ro_approval[0]['ro_approval_request_status']==1 ||  $ro_details[0]['ro_approval_status'] == 1) {
                                                        $table_html .= '</td><td style="font-weight:bold" align="right">
                                                        <!--<a href="<?php echo ROOT_FOLDER ?>/ro_manager/cancel_channel_from_ro/'.$channel_id.'/'.rtrim(base64_encode($internal_ro),'=').'/'.$edit.'/'.$id .'">--><span style="color:#F00"><!--Cancel--><!--<input type="button" class="submit_modified" value="Cancel Channel" onclick="cancel_channel(\''.$channel_id .'\',\''.rtrim(base64_encode($internal_ro),'=').'\',\''.$edit .'\',\''.$id .'\',\''. $ch['channel_name'] .'\')" />--></span><!--</a>-->';
                                                      }} */
                        $table_html .= '</td>
                                                </tr>
						 <tr cellpadding="0" cellspacing="0" width="100%">
                                                        <th colspan="3">Ad Type</th>
                                                        <th colspan="3">Total Ad Seconds</th>';
                        if ($profile_id == 1 or $profile_id == 2) {
                            $table_html .= '<th colspan="3">Historical Avg Rate/10Sec</th>
                                                        <th colspan="3">Amount</th>';
                        }
                        $table_html .= '</tr>';
                        foreach ($ch['ad_types'] as $types => $ad) {
                            $total_ad_types = count($ch['ad_types']);
                            $amount = 0;
                            $rate = $historical_avg_rates[$channel_id][$ad['ad_type']];
                            $amount += $ad['total_ad_seconds'] * $rate / 10;
                            $total_amount += $amount;
                            $table_html .= '<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">' . $ad['ad_type'] . '</td>
							<td colspan="3" id=network_"' . $key . '_total_sec_' . $row_id . '_0' . '" >' . $ad['total_ad_seconds'] . '</td>';
                            if ($profile_id == 1 or $profile_id == 2) {
                                if ($approved_channel_details[$channel_id]['channel_approval_status'] == 1) {
                                    $table_html .= '<td colspan="3">' . $approved_channel_details[$channel_id]['ad_types'][$types]['channel_avg_rate'] . '</td>';
                                } else {
                                    $table_html .= '<td colspan="3"><label>form_error(\'channel_avg_rate\')</label><input type="text" class="rate"  name="post_channel_avg_rate[' . $channel_id . '][' . $types . ']" id="network_' . $key . '_channel_' . $row_id . '_1' . '" value="set_value(\'post_channel_avg_rate[' . $channel_id . '][' . $types . ']\',' . $rate . ')" onchange = "find_price(' . $row_id . ',' . $rows[$customer_id] . ',' . $key . ');find_total_payout(' . $total_networks . ');" /> </td>';
                                }
                                if ($approved_channel_details[$channel_id]['channel_approval_status'] == 1) {
                                    $approved_total_amount += $approved_channel_details[$channel_id]['ad_types'][$types]['amount'];
                                    $table_html .= '<td colspan="3">' . round($approved_channel_details[$channel_id]['ad_types'][$types]['amount'], 2) . '</td>';
                                } else {
                                    $table_html .= '<td colspan="3"><label>form_error(\'channel_amount\')</label><input type="text" class="amount"  name="post_channel_amount[' . $channel_id . '][' . $types . ']" id="network_' . $key . '_amount_' . $row_id . '_2' . '" value="set_value(\'post_channel_amount[' . $channel_id . '][' . $types . ']\',' . round($amount, 2) . ')" onchange = "find_rate(' . $row_id . ',' . $rows[$customer_id] . ',' . $key . ');find_total_payout(' . $total_networks . ');" />
							<input type="hidden" name="hid_total_seconds[' . $channel_id . '][' . $types . ']" value="' . $ad['total_ad_seconds'] . '" /> 
							<input type="hidden" name="hid_client_name" value="' . $dat['client_name'] . '" />
							<input type="hidden" name="hid_internal_ro" value="' . rtrim(base64_encode($internal_ro), '=') . '" />
							<input type="hidden" name="hid_cust_id[' . $customer_id . ']" value="' . $customer_id . '" />';
                                    $row_id++;
                                    $table_html .= '<input type="hidden" name="total_rows" value="' . $rows[$customer_id] . '" /></td>';
                                }
                                $table_html .= '</tr>';
                            }
                        }
                    }
                    if ($profile_id == 1 or $profile_id == 2) {
                        $table_html .= '<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Network RO Amount</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>';
                        if ($approved_channel_details[$channel_id]['channel_approval_status'] == 1) {
                            $table_html .= '<td colspan="3">' . $approved_total_amount . '</td>';
                        } else {
                            $table_html .= '<td colspan="3" id="network_' . $key . '_total_amount" >' . round($total_amount, 2) . '</td>';
                        }
                        $table_html .= '</tr>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Network Share(%)</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>';
                        if ($approved_channel_details[$channel_id]['channel_approval_status'] == 1) {
                            $table_html .= '<td colspan="3">' . $approved_channel_details[$channel_id]['customer_share'] . '</td>';
                        } else {
                            $table_html .= '<td colspan="3"><label>form_error(\'network_share\')</label><input type="text" class="network_share"  name="post_network_share[' . $customer_id . ']" id="network_' . $key . '_network_share" value="set_value(\'post_network_share[' . $customer_id . ']\', ' . $network_share_details[$customer_id] . ')" onchange ="find_final_amount(' . $key . ',' . $total_networks . ');find_total_payout(' . $total_networks . ');" /> </td>';
                        }
                        $table_html .= '</tr>
						<tr cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold">
							<td colspan="3">Network Payout(Net)</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>';
                        if ($approved_channel_details[$channel_id]['channel_approval_status'] == 1) {
                            $table_html .= '<td colspan="3">' . round($approved_total_amount * $approved_channel_details[$channel_id]['customer_share'] / 100) . '</td>';
                        } else {
                            $table_html .= '<td colspan="3" style="font-weight:bold"><label>form_error(\'final_amount\')</label><input type="text" class="final_amount"  name="post_final_amount[' . $customer_id . ']" id="network_' . $key . '_final_amount" value="set_value(\'post_final_amount[' . $customer_id . ']\',' . round($total_amount * $network_share_details[$customer_id] / 100) . ')" onchange ="find_channel_amounts(' . $key . ',' . $rows[$customer_id] . ');find_total_payout(' . $total_networks . ');" /> </td>';
                        }
                        $table_html .= '</tr></table>';
                    }
                    //$count_data++ ;
                    //$lastCount_data = $count_data ;
                    $lastCount = $count;
                    $count++;
                }
                //echo '<li class="getmore" id="'.$lastCount_data.'">Get More</li>' ;


                //$lastCount = $count ;
                //$count++ ;
                echo $table_html;
                echo '<li class="getmore" id="' . $count . '">Get More</li>';
            } else {
                echo '<li class="nomore">No More Records Found</li>';
            }
        }
    }

    public function add_ro_amount($external_ro)
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $url_string = $_SERVER['HTTP_REFERER'];
        $this->session->set_userdata('url_path', $url_string);
        $parts = explode('/', $url_string);
        $model = array();
        $model['pag_number'] = $parts[6];
        $model['customer_ro_number'] = base64_decode($external_ro);
        $ro_details = $this->mg_model->get_ro_details(base64_decode($external_ro));
        $order_id = $ro_details[0]['internal_ro_number'];
        $ro_amount_details = $this->mg_model->get_ro_amount($order_id);
        $model['ro_amount_details'] = $ro_amount_details;
        $this->session->set_userdata('customer_ro_number', $external_ro);
        $model['url_string'] = $url_string;
        $model['internal_ro_number'] = $order_id;
        $this->load->view('ro_manager/add_ro_amount', $model);
    }

    // added by lokanath for invoice generator

    public function post_add_ro_amount()
    {
        $error_in_validation = set_form_validation($this->config->item('add_ro_amount_form'));

        if ($error_in_validation) {

            $model = array();
            $external_ro = $_POST['hid_customer_ro'];
            $order_id = $_POST['hid_internal_ro'];
            $model['internal_ro_number'] = $order_id;
            $model['customer_ro_number'] = $external_ro;
            $url_string = $this->session->userdata('url_path');
            $parts = explode('/', $url_string);
            $model['pag_number'] = $parts[6];
            $model['url_string'] = $url_string;
            show_form_validation_error('ro_manager/add_ro_amount', $model);
        } else {
            $logged_in = $this->session->userdata("logged_in_user");
            $data = array(
                'customer_ro_number' => $_POST['hid_customer_ro'],
                'internal_ro_number' => $_POST['hid_internal_ro'],
                'ro_amount' => $_POST['ro_amount'],
                'agency_commission_amount' => $_POST['agency_amount'],
                'timestamp' => date("Y-m-d H:i:s"),
                'approval_timestamp' => date("Y-m-d H:i:s"),
                'approval_user_id' => $logged_in[0]['user_id'],
            );
            $order_id = rtrim(base64_encode($_POST['hid_internal_ro']), '=');
            $this->ro_model->add_ro_amount($data);
            sleep(5);
            echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/ro_manager/approve/' . $order_id . '";</script>';
        }

    }
    // end
    /*
	 **@mani:changed for showing confirmation page
	 **date:9th July 2013,Phase 1.5 Implementation
	 */

    public function approval_request()
    {
        $order_id = $this->input->post('order_id');
        $edit = $this->input->post('edit');
        $am_ro_id = $this->input->post('id');
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $profile_ids = array('0' => 1, '1' => 2);
        $users = $this->user_model->get_all_mainusers(implode(',', $profile_ids));
        $order = $this->ro_model->get_ro_data($order_id);
        log_message('info','in ro_manager@approval_request | $order - '.print_r($order,true));
        $internal_ro = $order_id;
        $data = array(
            'customer_ro_number' => $order[0]['customer_ro_number'],
            'internal_ro_number' => $order[0]['internal_ro_number'],
            'start_date' => $order[0]['start_date'],
            'end_date' => $order[0]['end_date'],
            'ro_approval_request_status' => 1
        );
        log_message('info','in ro_manager@approval_request | $data - '.print_r($data,true));
        $this->db->trans_start();
        $this->ro_model->ro_approval_request_status($data);
        //@changed by mani:get new ro's detail from external ro and update ro status
        $ro_details = $this->am_model->get_ro_details_for_external_ro($order[0]['customer_ro_number']);
        $this->am_model->update_ro_status($ro_details[0]['id'], 'approval_requested');

        $external_ro = $order[0]['customer_ro_number'];
        //Entry Into ro_cancel_external_ro
        $approvalData = array(
            'approval_level' => 3
        );
        $whereApprovalData = array(
            'ext_ro_id' => $am_ro_id,
            'cancel_type' => 'ro_approval',
        );
        $this->ro_model->insert_for_pending_status($whereApprovalData, $approvalData);
        $this->db->trans_complete();

        $users = $this->user_model->get_bhs();
        $text = "ro_approval_request_alert";
        $login_url = 'http://' . $_SERVER['SERVER_NAME'] . ROOT_FOLDER;
        $subject = array('EXTERNAL_RO' => $external_ro);
        $file = '';
        $url = '';
        foreach ($users as $key => $user) {
            $to = $user['user_email'];
            $message = array(
                'USER_NAME' => $user['user_name'],
                'EXTERNAL_RO' => $external_ro,
                'INTERNAL_RO' => $internal_ro,
                'START_DATE' => date('Y-m-d', strtotime($order[0]['start_date'])),
                'END_DATE' => date('Y-m-d', strtotime($order[0]['end_date'])),
                'CURRENT_USER' => $logged_in[0]['user_name'],
                'LOGIN_URL' => $login_url,
            );
            mail_send($to, $text, $subject, $message, $file, '', $url);
        }

        //echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/ro_manager/approve/' . $order_id . '/' . $edit . '/' . $am_ro_id . '";</script>';
        $response['Status'] = 'success';
        $response['Message'] = 'Approval Request Sent !!';
        $response['Data'] = array();
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function add_other_expenses()
    {
        $this->is_logged_in();
        $external_ro = $this->input->post('external_ro');
        $internal_ro = $this->input->post('internal_ro');
        $edit = $this->input->post('edit');
        $am_ro_id = $this->input->post('am_ro_id');
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $url_string = $_SERVER['HTTP_REFERER'];
        $this->session->set_userdata('url_path', $url_string);
        $parts = explode('/', $url_string);
        $model = array();
        $model['pag_number'] = $parts[6];
        $model['customer_ro_number'] = $external_ro;
        $ro_details = $this->mg_model->get_ro_details($external_ro);
        $order_id = $ro_details[0]['internal_ro_number'];
        $ro_amount_details = $this->mg_model->get_ro_amount($order_id);
        $model['ro_amount_details'] = $ro_amount_details;
        $this->session->set_userdata('customer_ro_number', rtrim(base64_encode($external_ro), '='));
        $model['url_string'] = $url_string;
        $model['internal_ro_number'] = $order_id;
        // added by lokanath
        $model['edit'] = $edit;
        $model['id'] = $am_ro_id;
        $model['internal_ro'] = rtrim(base64_encode($internal_ro), '=');
        // end
//        $this->load->view('ro_manager/add_other_expenses', $model);

        $response['Status'] = 'success';
        $response['Message'] = 'Loading View Add other expenses!';
        $response['Data']['html'] = $this->load->view('ro_manager/add_other_expenses', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function post_add_other_expenses()
    {
        $error_in_validation = set_form_validation($this->config->item('add_other_expenses_form'));

        $is_edit = $_POST['hid_edit'];
        $am_ro_id = $_POST['hid_id'];
        $internal_ro = $_POST['hid_internal_ro'];
        log_message('info', 'in ro_manager@post_add_other_expenses | post data' . print_r($_POST, true));
        if ($error_in_validation) {
            $model = array();
            $external_ro = $_POST['hid_customer_ro'];
            $order_id = $_POST['hid_internal_ro'];
            $model['internal_ro_number'] = $order_id;
            $model['customer_ro_number'] = $external_ro;
            $ro_amount_details = $this->mg_model->get_ro_amount($order_id);
            $model['ro_amount_details'] = $ro_amount_details;
            $url_string = $this->session->userdata('url_path');
            $parts = explode('/', $url_string);
            $model['pag_number'] = $parts[6];
            $model['url_string'] = $url_string;
            $model['edit'] = $is_edit;
            $model['id'] = $am_ro_id;
            $model['internal_ro'] = $internal_ro;
//            show_form_validation_error('ro_manager/add_other_expenses', $model);

            $response['Status'] = 'fail';
            $response['Message'] = 'Form Validation Failed!';
            $response['Data'] = array();
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
            return;
        } else {
            $data = array(
                'customer_ro_number' => $this->input->post('hid_customer_ro'),
                'internal_ro_number' => $this->input->post('hid_internal_ro'),
                'agency_rebate' => $this->input->post('agency_rebate'),
                'agency_rebate_on' => $this->input->post('sel_type'),
                'marketing_promotion_amount' => $this->input->post('marketing_promotion_amount'),
                'field_activation_amount' => $this->input->post('field_activation_amount'),
                'sales_commissions_amount' => $this->input->post('sales_commissions_amount'),
                'creative_services_amount' => $this->input->post('creative_services_amount'),
                'other_expenses_amount' => $this->input->post('other_expenses_amount'),
                'ro_valid_field' => $this->input->post('hid_ro_valid'),
                'timestamp' => date("Y-m-d H:i:s")
            );
//            $order_id = rtrim(base64_encode($_POST['hid_internal_ro']), '=');
            $this->db->trans_start();
            $this->ro_model->add_ro_expenses($data);
            $is_approved_ro = $this->mg_model->get_approved_nw(array('internal_ro_number' => $internal_ro));
            if (count($is_approved_ro) > 0) {
                $this->update_into_external_ro_report_detail($internal_ro);
            }
            $this->db->trans_complete();
//        sleep(5);
//        echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/ro_manager/approve/' . $internal_ro . '/' . $is_edit . '/' . $am_ro_id . '";</script>';
        }
        $response['Status'] = 'success';
        $response['Message'] = 'Other Expenses added successfully!';
        $response['Data'] = array();
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function forgot_password()
    {
        $this->load->view('ro_manager/forgot_password');
    }

    public function post_forgot_password()
    {
        $this->form_validation->set_rules('user_email', 'User Email', 'trim|required|valid_email|callback_user_email_check');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('ro_manager/forgot_password');
        } else {
            $email = $this->input->post('user_email');
            $ret = $this->user_model->get_ro_user_from_email($email);
            $login_url = 'http://' . $_SERVER['SERVER_NAME'] . ROOT_FOLDER;
            $userdata = $ret[0];
            $to = $userdata['user_email'];
            $text = "user";
            $subject = array('USER_NAME' => $userdata['user_name']);
            $message = array(
                'USER_NAME' => $userdata['user_name'],
                'USER_EMAIL' => $userdata['user_email'],
                'PASSWORD' => $userdata['plain_text_password'],
                'LOGIN_URL' => $login_url,
            );
            $file = '';
            $url = '';
            mail_send($to, $text, $subject, $message, $file, '', $url);
            echo "<h3>We have sent your login credentials for SureWaves EasyRO to your registered email " . $to . "</h3>";
        }
    }

    public function user_email_check($str)
    {

        $ret = $this->user_model->get_ro_user_from_email($str);

        if ($ret == NULL) {
            $this->form_validation->set_message('user_email_check', 'Email is not in use');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function network_ro_report()
    {
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $this->load->view('ro_manager/network_ro_report', $model);
    }

    public function external_ro_report()
    {
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $this->load->view('ro_manager/external_ro_report', $model);
    }

    function get_ros()
    {
        $list = $this->mg_model->get_customer_ros();


        $this->output->set_header('Content-Type: application/json');

        $this->output->set_output($list);
    }

    function get_record()
    {
        $records = $this->mg_model->get_records();
        log_message('INFO','In ro_manager@get_record | Records=> '.print_r($records,true));

        $this->output->set_header('Content-Type: application/json');

        $this->output->set_output($records);
    }

    function get_external_ros()
    {
        $list = $this->mg_model->get_external_ros();


        $this->output->set_header('Content-Type: application/json');

        $this->output->set_output($list);
    }

    function get_external_report()
    {
        $records = $this->mg_model->get_external_ro_records();

        $this->output->set_header('Content-Type: application/json');

        $this->output->set_output($records);
    }

    public function download_channel_performance_csv()
    {
        log_message('INFO', 'In ro_manager @ download_channel_performance | Entered');
        $startDate = $_POST['from_date'];
        $endDate = $_POST['to_date'];
        log_message('INFO', 'Start Date => '.print_r($startDate,True). '\n' . 'End Date => '.print_r($endDate,True));

        $channelPerformance = $this->mg_model->get_channel_performance_details($startDate, $endDate);
        log_message('INFO', 'In ro_manager @ download_channel_performance | ChannelPerformance => '.print_r($channelPerformance,True));
        if(count($channelPerformance) < 1 || empty($channelPerformance)){
            echo 'You Didn\'t Have Any Ro!';
            exit;
        }

        $csvArray = array(
            array(
                'internal_ro_number', 'channel_id', 'channel_name', 'customer_name', 'customer_id', 'createdDate',
                'spotScheduleSec', 'bannerScheduleSec', 'spotPlayedSec', 'bannerPlayedSec', 'spotPerformancePercentage',
                'bannerPerformancePercentage', 'makeGood_spotScheduleSec', 'makeGood_bannerScheduleSec', 'makeGood_spotPlayedSec',
                'makeGood_bannerPlayedSec', 'makeGood_spotPerformancePercentage', 'makeGood_bannerPerformancePercentage', 'is_offline'
            )
        );
        $i = 1;
        foreach ($channelPerformance as $item) {
            $csvArray[$i][0] = $item['internal_ro_number'];
            $csvArray[$i][1] = $item['channel_id'];
            $csvArray[$i][2] = $item['channel_name'];
            $csvArray[$i][3] = $item['customer_name'];
            $csvArray[$i][4] = $item['customer_id'];
            $csvArray[$i][5] = $item['createdDate'];
            $csvArray[$i][6] = $item['spotScheduleSec'];
            $csvArray[$i][7] = $item['bannerScheduleSec'];
            $csvArray[$i][8] = $item['spotPlayedSec'];
            $csvArray[$i][9] = $item['bannerPlayedSec'];
            $csvArray[$i][10] = $item['spotPerformancePercentage'];
            $csvArray[$i][11] = $item['bannerPerformancePercentage'];
            $csvArray[$i][12] = $item['makeGood_spotScheduleSec'];
            $csvArray[$i][13] = $item['makeGood_bannerScheduleSec'];
            $csvArray[$i][14] = $item['makeGood_spotPlayedSec'];
            $csvArray[$i][15] = $item['makeGood_bannerPlayedSec'];
            $csvArray[$i][16] = $item['makeGood_spotPerformancePercentage'];
            $csvArray[$i][17] = $item['makeGood_bannerPerformancePercentage'];
            $csvArray[$i][18] = $item['is_offline'];
            $i++;
        }
        log_message('info', 'In ro_manager@download_channel_performance | CSV => '.print_r($csvArray,True));
        $this->load->helper('csv');
        array_to_csv($csvArray, 'Channel_performance_report.csv');
    }

    public function download_csv($start_date, $end_date, $ro_name)
    {
        $ro_name = urldecode($ro_name);
        $ro_name = str_replace("~", "/", $ro_name);
        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 00:00:00';
        log_message('INFO', 'In ro_manager @ download_csv | Ro Name '.print_r($ro_name,True));
        log_message('INFO', 'In ro_manager @ download_csv | Start Date '.print_r($start_date,True));
        log_message('INFO', 'In ro_manager @ download_csv | End Data '.print_r($end_date,True));

//        $customer_ro_numbers = $this->mg_model->get_csv_customer_ros($start_date, $end_date);

        /*if (empty($customer_ro_numbers)) {
            echo "you have no RO's'";
            exit;
        } elseif ($ro_name == 'all') {
            foreach ($customer_ro_numbers as $key => $ro)
                $customer_ros[] = $ro['customer_ro_number'];

            $customer_ro = "'" . implode("','", $customer_ros) . "'";
            $network_ro_details = $this->mg_model->get_network_ro_report_details($customer_ro, $start_date, $end_date);
        } else {
            $customer_ro = "'" . $ro_name . "'";
            $network_ro_details = $this->mg_model->get_network_ro_report_details($customer_ro, $start_date, $end_date);
        }*/
        if($ro_name == 'all'){
            $network_ro_details = $this->mg_model->get_network_ro_report_details($start_date, $end_date);
            if(count($network_ro_details) == 0 || empty($network_ro_details)){
                echo "You have no RO's'";
                exit;
            }
        }
        else{
            $customer_ro = "'" . $ro_name . "'";
            $network_ro_details = $this->mg_model->get_network_ro_report_details($start_date, $end_date, $customer_ro);
            if(count($network_ro_details) == 0 || empty($network_ro_details)){
                echo "You have no RO's'";
                exit;
            }
        }
        log_message('INFO', 'In ro_manager @ download_csv | NetworkRoDetails '.print_r($network_ro_details,True));

        $csv_array = array(
            array('customer_ro_number', 'internal_ro_number', 'network_ro_number', 'network_name', 'advertiser_client_name', 'agency_name',
                'market', 'start_date', 'end_date', 'activity_months', 'gross_network_ro_amount', 'network_share', 'net_amount_payable',
                'release_date', 'billing_name')
        );

        $ro_count = count($network_ro_details);
        $count = count($csv_array[0]);
        $internalRoS = array();
        $markets = array();
        for ($i = 0; $i < $ro_count; $i++) {
            $k = $i + 1;
            for ($j = 0; $j < $count; $j++) {
//                $market_amount = $this->ro_model->getRoAmountForRoAndMarket($network_ro_details[$i]['internal_ro_number'], $network_ro_details[$i]['market']);
                $csv_array[$k][0] = $network_ro_details[$i]['customer_ro_number'];
                $csv_array[$k][1] = $network_ro_details[$i]['internal_ro_number'];
                $csv_array[$k][2] = $network_ro_details[$i]['network_ro_number'];
                $csv_array[$k][3] = $network_ro_details[$i]['customer_name'];
                $csv_array[$k][4] = $network_ro_details[$i]['client_name'];
                $csv_array[$k][5] = $network_ro_details[$i]['agency_name'];
                $csv_array[$k][6] = $network_ro_details[$i]['market'];
                $csv_array[$k][7] = $network_ro_details[$i]['start_date'];
                $csv_array[$k][8] = $network_ro_details[$i]['end_date'];
                $csv_array[$k][9] = $network_ro_details[$i]['activity_months'];
                $csv_array[$k][10] = $network_ro_details[$i]['gross_network_ro_amount'];
                $csv_array[$k][11] = $network_ro_details[$i]['customer_share'];
                $csv_array[$k][12] = $network_ro_details[$i]['net_amount_payable'];
                $csv_array[$k][13] = $network_ro_details[$i]['release_date'];
                $csv_array[$k][14] = $network_ro_details[$i]['billing_name'];

                /*if (in_array($network_ro_details[$i]['internal_ro_number'], $internalRoS)) {
                    if (in_array($network_ro_details[$i]['market'], $markets)) {
                        if (!isset($csv_array[$k][15]))
                            $csv_array[$k][15] = 'N.A.';
                    } else {
                        if (!isset($csv_array[$k][15]))
                            $csv_array[$k][15] = $market_amount;
                    }
                } else {
                    if (!isset($csv_array[$k][15]))
                        $csv_array[$k][15] = $market_amount;
                }

                if (!in_array($network_ro_details[$i]['internal_ro_number'], $internalRoS)) {
                    array_push($internalRoS, $network_ro_details[$i]['internal_ro_number']);
                }
                if (!in_array($network_ro_details[$i]['market'], $markets)) {
                    array_push($markets, $network_ro_details[$i]['market']);
                }*/

            }
        }
        log_message('INFO', 'In ro_manager @ download_csv | CsvArray '.print_r($csv_array,True));
        $this->load->helper('csv');
        array_to_csv($csv_array, 'Network_ro_report.csv');
    }

    public function download_export_ro_report_csv($start_date, $end_date, $report_type = null)
    {

        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 00:00:00';

        $external_ro_details = $this->mg_model->get_external_ro_csv_report_details($start_date, $end_date, $report_type);
        $this->load->helper('csv');

        $csv_array = array(
            array('Customer RO Number', 'Internal RO Number', 'Client Name', 'Agency Name', 'Start Date', 'End Date', 'Gross RO Amount', 'Media Agency Commission', 'Agency Rebate', 'Other Expenses', 'Total Seconds Scheduled', 'Total Network Payout', 'Net Revenue', 'Net Contribution Amount', 'Net Contribution Amount(%)', 'Total Amount Collected', 'TDS', 'Complete Payment'));

        $ro_count = count($external_ro_details);
        $count = count($csv_array[0]);

        for ($i = 0; $i < $ro_count; $i++) {
            $k = $i + 1;
            for ($j = 0; $j < $count; $j++) {
                $data = $this->mg_model->get_invoice_data_for_external_ro($external_ro_details[$i]['customer_ro_number']);

                $csv_array[$k][0] = $external_ro_details[$i]['customer_ro_number'];
                $csv_array[$k][1] = $external_ro_details[$i]['internal_ro_number'];
                $csv_array[$k][2] = $external_ro_details[$i]['client_name'];
                $csv_array[$k][3] = $external_ro_details[$i]['agency_name'];
                $csv_array[$k][4] = $external_ro_details[$i]['start_date'];
                $csv_array[$k][5] = $external_ro_details[$i]['end_date'];
                $csv_array[$k][6] = $external_ro_details[$i]['gross_ro_amount'];
                $csv_array[$k][7] = $external_ro_details[$i]['agency_commission_amount'];
                $csv_array[$k][8] = $external_ro_details[$i]['agency_rebate'];
                $csv_array[$k][9] = $external_ro_details[$i]['other_expenses'];
                $csv_array[$k][10] = $external_ro_details[$i]['total_seconds_scheduled'];
                $csv_array[$k][11] = $external_ro_details[$i]['total_network_payout'];
                $csv_array[$k][12] = $external_ro_details[$i]['net_revenue'];
                $csv_array[$k][13] = $external_ro_details[$i]['net_contribution_amount'];
                $csv_array[$k][14] = $external_ro_details[$i]['net_contribution_amount_per'];
                $csv_array[$k][15] = $data['total_amount_collected'];
                $csv_array[$k][16] = $data['tds'];
                $csv_array[$k][17] = $data['total_payment_received'];
            }
        }
        array_to_csv($csv_array, 'External_ro_report.csv');

    }

    public function download_invoice_generator($start_date, $end_date, $rd_invoice, $report_type = null)
    {

        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 00:00:00';

        $external_ro_details = $this->mg_model->get_report_for_invoice($start_date, $end_date, $report_type);
        $this->load->helper('csv');

        $csv_array = array(
            array('Invoice Date', 'Internal RO Number', 'Release Order Number', 'Release Order Date', 'Buyer\'s Contact', 'Supplier\'s Contact', 'Client', 'Buyer', 'Buyer Address Line 1', 'Buyer Address Line 2', 'Period', 'Market', 'Total FCT Seconds', 'Duration', 'Total Spots', 'Rate', 'Campaign', 'Brand', 'Product', 'Agency Commission', 'Region', 'Geo Region'));
        $ro_count = count($external_ro_details);
        $count = count($csv_array[0]);
        $k = 0;
        for ($i = 0; $i < $ro_count; $i++) {
            //$k = $i+1;
            // for($j = 0;$j<$count;$j++)
            // {
            // fetching data related to customer ro
            // code added for all or completed reports between given date range
            if ($rd_invoice == "completed" && ($external_ro_details[$i]['end_date'] < $start_date || $external_ro_details[$i]['end_date'] > $end_date)) {
                continue;
            }
            //end
            $camp_dtls = $this->mg_model->get_campaign_details($external_ro_details[$i]['customer_ro_number'], $start_date, $end_date);
            // end

            // get RO date
            $ro_details = $this->mg_model->get_ro_date_from_am_ro($external_ro_details[$i]['customer_ro_number']);
            $ro_date = $ro_details[0]['ro_date'];
            // end
		
	    // get master geo regions
	  //  echo "<pre>";
	  //  print_r($ro_details);
            $geoRegionDetails = $this->mg_model->getAllRoRegions(array('id'=>$ro_details[0]['region_id']));
           // print_r($geoRegionDetails);
	    //exit;
            // end
            //get Client/Agency details
            $buyer_details = $this->mg_model->get_buyers_details($external_ro_details[$i]['customer_ro_number']);
            $buyer_contact = $buyer_details['buyer_contact'];
            $buyer_address = $buyer_details['buyer_address'];
            //end

            //get Supplier's Contact
            $supplier_details = $this->mg_model->get_supplier_details($external_ro_details[$i]['customer_ro_number']);
            $supplier_contact = $supplier_details['user_name'];
            //end

            foreach ($camp_dtls as $invoice_array) {
                // code added for all or completed reports between given date range
                /*if($rd_invoice == "completed" && ($invoice_array['end_date'] < $start_date || $invoice_array['end_date'] > $end_date)){
										continue;
									}*/
                //end
                $k++;
                $csv_array[$k][0] = date('Y-m-d');
                $csv_array[$k][1] = $external_ro_details[$i]['internal_ro_number'];
                $csv_array[$k][2] = $external_ro_details[$i]['customer_ro_number'];
                $csv_array[$k][3] = date('Y-m-d', strtotime($ro_date));
                $csv_array[$k][4] = $buyer_contact;
                $csv_array[$k][5] = $supplier_contact;
                $csv_array[$k][6] = $external_ro_details[$i]['client_name'];
                if (strtolower($external_ro_details[$i]['agency_name']) == 'surewaves') {
                    $csv_array[$k][7] = $external_ro_details[$i]['client_name'];
                } else {
                    $csv_array[$k][7] = $external_ro_details[$i]['agency_name'];
                }
                $csv_array[$k][8] = $buyer_address;
                $csv_array[$k][9] = "";
                // change of requirement in date range in report
                if ($invoice_array['start_date'] >= $start_date) {
                    $range_start_date = $invoice_array['start_date'];
                } else {
                    $range_start_date = $start_date;
                }
                if ($invoice_array['end_date'] <= $end_date) {
                    $range_end_date = $invoice_array['end_date'];
                } else {
                    $range_end_date = $end_date;
                }

                $agency_commision_percentage = ($external_ro_details[$i]['agency_commission_amount'] / $external_ro_details[$i]['gross_ro_amount']) * 100;

                // end
                $csv_array[$k][10] = date('Y-m-d', strtotime($range_start_date)) . ' to ' . date('Y-m-d', strtotime($range_end_date));
                $csv_array[$k][11] = $invoice_array['sw_market'];
                $csv_array[$k][12] = $invoice_array['channel_fct'];
                $csv_array[$k][13] = $invoice_array['duration'];
                $csv_array[$k][14] = $invoice_array['sum_impression'];
                $csv_array[$k][15] = $invoice_array['rate'];
                $csv_array[$k][16] = "";
                $csv_array[$k][17] = $invoice_array['brand'];
                $csv_array[$k][18] = $invoice_array['product'];
                $csv_array[$k][19] = round($agency_commision_percentage, 2);
                $csv_array[$k][20] = $invoice_array['region_name'];
		$csv_array[$k][21] = $geoRegionDetails[0]['region_name'];
            }
            /*$csv_array[$k][0] = $external_ro_details[$i]['customer_ro_number'];
                                $csv_array[$k][1] = $external_ro_details[$i]['internal_ro_number'];
                                $csv_array[$k][2] = $external_ro_details[$i]['client_name'];
                                $csv_array[$k][3] = $external_ro_details[$i]['agency_name'];
                                $csv_array[$k][4] = $external_ro_details[$i]['start_date'];
                                $csv_array[$k][5] = $external_ro_details[$i]['end_date'];
                                $csv_array[$k][6] = $external_ro_details[$i]['gross_ro_amount'];
                                $csv_array[$k][7] = $external_ro_details[$i]['agency_commission_amount'];
                                $csv_array[$k][8] = $external_ro_details[$i]['agency_rebate'];
                                $csv_array[$k][9] = $external_ro_details[$i]['other_expenses'];
                                $csv_array[$k][10] = $external_ro_details[$i]['total_seconds_scheduled'];
                                $csv_array[$k][11] = $external_ro_details[$i]['total_network_payout'];
								$csv_array[$k][12] = $external_ro_details[$i]['net_revenue'];
                                $csv_array[$k][13] = $external_ro_details[$i]['net_contribution_amount'];
                                $csv_array[$k][14] = $external_ro_details[$i]['net_contribution_amount_per'];*/
            //}
        }
	//print_r($csv_array);exit;
        array_to_csv($csv_array, 'invoice_generator.csv');

    }

    public function confirm_add_price_approve($order_id, $edit, $am_ro_id)
    {
        $this->is_logged_in(1);
        $internal_ro = $order_id;
        $order_id = serialize($order_id);
        $post_details_for_confirmation = $this->ro_model->get_data_for_confirmation($order_id);
        $post_details_for_confirmation = $post_details_for_confirmation[0];

        $network_share_detail = unserialize($post_details_for_confirmation['post_final_amount']);
        $data = array();
        $total_amount = 0;
        foreach ($network_share_detail as $cid => $net_amnt) {
            $network_details = $this->mg_model->get_network_details($cid);

            $tmp = array();
            $tmp['network'] = $network_details[0]['customer_name'];
            $tmp['net_amount'] = round($net_amnt, 2);
            $total_amount += $net_amnt;

            array_push($data, $tmp);
        }
        $internal_ro_number = base64_decode($internal_ro);
        $ro_amount_detail = $this->mg_model->get_ro_amount($internal_ro_number);

        if ($ro_amount_detail[0]['agency_rebate_on'] != 'net_amount') {
            $agency_rebate = round($ro_amount_detail[0]['ro_amount'], 2) * round($ro_amount_detail[0]['agency_rebate'], 2) / 100;
        } else {
            $agency_rebate = round(($ro_amount_detail[0]['ro_amount'] - $ro_amount_detail[0]['agency_commission_amount']), 2) * round($ro_amount_detail[0]['agency_rebate'], 2) / 100;
        }
        $other_expenses = $ro_amount_detail[0]['marketing_promotion_amount'] + $ro_amount_detail[0]['field_activation_amount'] + $ro_amount_detail[0]['sales_commissions_amount'] + $ro_amount_detail[0]['creative_services_amount'] + $ro_amount_detail[0]['other_expenses_amount'];
        $actual_net_amount = round($ro_amount_detail[0]['ro_amount'], 2) - round($ro_amount_detail[0]['agency_commission_amount'], 2) - $agency_rebate - $other_expenses;
        $net_revenue = round(($ro_amount_detail[0]['ro_amount'] - $ro_amount_detail[0]['agency_commission_amount']) * SERVICE_TAX, 2);
        $surewaves_share = $actual_net_amount - $total_amount;
        $net_contribuition_percent = round(($surewaves_share / $net_revenue * 100), 2);

        $model = array();
        $model['network_amount'] = $data;
        $model['total_amount'] = round($total_amount, 2);
        $model['order_id'] = unserialize($order_id);
        $model['edit'] = $edit;
        $model['id'] = $am_ro_id;
        $model['internal_ro'] = $internal_ro;
        $model['net_revenue'] = $net_revenue;
        $model['net_cont_percent'] = $net_contribuition_percent;

        $this->load->view('ro_manager/confirm_add_price_approve', $model);
    }

    /* public function get_network_remittance($network_id = 0) {
			$this->is_logged_in();
			$logged_in = $this->session->userdata("logged_in_user");

			$model = array();
			$model['logged_in_user']=$logged_in[0];
			$model['profile_id'] = $logged_in[0]['profile_id'] ;

			$this->load->model('user_model');
			$model['customer_detail'] = $this->user_model->get_all_customer_detail();

			if(!empty($network_id)) {
				$channels = $this->user_model->get_all_channels_for_customer($network_id);

				if(count($channels) > 0) {
					$channel_ids = array();
					foreach($channels as $chnl) {
						array_push($channel_ids,$chnl['tv_channel_id']) ;
					}
					$channel_ids = implode(",",$channel_ids) ;

					$markets = $this->ro_model->get_all_markets_for_channels($channel_ids);

					$data = array();
					foreach($markets as $mkt) {

						$data_values = $this->ro_model->get_ro_details_for_markets($mkt['sw_market_name']);
						foreach($data_values as $val) {
							$customer_ro = trim($val['cust_ro']) ;
							if(!isset($data[$customer_ro])) {
								$data[$customer_ro] = array();

								$data[$customer_ro]['customer_ro'] = $val['cust_ro'] ;
								$data[$customer_ro]['internal_ro'] = $val['internal_ro'] ;
								$data[$customer_ro]['agency'] = $val['agency'] ;
								$data[$customer_ro]['client'] = $val['client'] ;
								$data[$customer_ro]['camp_start_date'] = $val['camp_start_date'] ;
								$data[$customer_ro]['camp_end_date'] = $val['camp_end_date'] ;

								if($val['chk_complete'] == '0') {
									$data[$customer_ro]['client_payment_received'] = 'No' ;
								}else if($val['chk_complete'] == '1') {
									$data[$customer_ro]['client_payment_received'] = 'Yes' ;
								}
								$data[$customer_ro]['amount_collected'] = $val['amnt_collected'] ;

							}else {
								if($customer_ro == trim($data[$customer_ro]['customer_ro']) ) {
									if($val['chk_complete'] == '1') {
										$data[$customer_ro]['client_payment_received'] = 'Yes' ;
									}
									$data[$customer_ro]['amount_collected'] = $data[$customer_ro]['amount_collected'] + $val['amnt_collected'] ;
								}
							}
						}

					}
				$model['ro_details'] = $data ;

				}
				$model['cid'] = $network_id ;
			}

			$this->load->view('ro_manager/network_remittance',$model);
		} */

    public function saving_data_for_confirmation()
    {
        $this->is_logged_in();
        $edit = $_POST['hid_edit'];
        $am_ro_id = $_POST['hid_id'];
        $order_id = $this->input->post('hid_internal_ro');
        log_message('INFO', 'In ro_manager@saving_data_for_confirmation | $_POST data - ' . print_r($_POST, true));

        //Get data from cache make as posted data which is not available in posted data
        $add_data_from_cache = $this->add_data_from_cache($am_ro_id, $_POST);

        //make $_POST as loading page of schedulea data of approve page
        $post_data_as_cache = $this->make_post_data_as_load($_POST);
        //add data which is not posted,get it from cache
        $this->add_reamin_data_from_cache($am_ro_id, $post_data_as_cache, $order_id);

        $channel_avg_rate = $add_data_from_cache['channel_avg_rate'];
        $channel_amount = $add_data_from_cache['channel_amount'];
        $channel_seconds = $add_data_from_cache['channel_seconds'];
        $customer_ids = $add_data_from_cache['customer_ids'];
        $nw_shares = $add_data_from_cache['network_share'];
        $nw_amount = $add_data_from_cache['network_amount'];
        $total_rows = $add_data_from_cache['total_rows'];
        $cancel_channel = $add_data_from_cache['cancel_channel'];

        /*
		$user_data = array(
						'order_id' => serialize($order_id),
						'apa' => serialize($this->input->post('apa')),
						'post_channel_avg_rate' => serialize($this->input->post('post_channel_avg_rate')),
						'post_channel_amount' => serialize($this->input->post('post_channel_amount')),
						'hid_total_seconds' => serialize($this->input->post('hid_total_seconds')),
						'hid_client_name' => serialize($this->input->post('hid_client_name')),
						'hid_internal_ro' => serialize($this->input->post('hid_internal_ro')),
						'hid_cust_id' => serialize($this->input->post('hid_cust_id')),
						'total_rows' => serialize($this->input->post('total_rows')),
						'post_network_share' => serialize($this->input->post('post_network_share')),
						'post_final_amount' => serialize($this->input->post('post_final_amount'))
					); */
        $user_data = array(
            'order_id' => serialize($order_id),
            'apa' => serialize($this->input->post('apa')),
            'post_channel_avg_rate' => serialize($channel_avg_rate),
            'post_channel_amount' => serialize($channel_amount),
            'hid_total_seconds' => serialize($channel_seconds),
            'hid_client_name' => serialize($this->input->post('hid_client_name')),
            'hid_internal_ro' => serialize($this->input->post('hid_internal_ro')),
            'hid_cust_id' => serialize($customer_ids),
            'total_rows' => serialize($total_rows),
            'post_network_share' => serialize($nw_shares),
            'post_final_amount' => serialize($nw_amount),
            'post_cancel_channel' => serialize($cancel_channel)
        );

        $this->ro_model->saving_data_into_db_for_confirmation($user_data);
        $this->session->set_userdata('selected_order_id', $order_id);
        redirect("/ro_manager/approve/" . $order_id . "/" . $edit . "/" . $am_ro_id);
    }

    public function add_data_from_cache($am_ro_id, $post_data)
    {
        $cache_data = $this->memcache->get($am_ro_id);
        log_message('INFO', 'In ro_manager@add_data_from_cache | cached data from memcache for - ' . print_r(array('roId' => $am_ro_id, 'cached data' => $cache_data), true));

        /*
            $channel_avg_rate = array() ;
            $channel_amount = array() ;
            $channel_seconds = array();
            $customer_ids = array() ;
            $nw_shares = array() ;
            $nw_amount = array() ; */
        $order_id = $post_data['hid_internal_ro'];
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$order_id' => $order_id), true));
        $channel_avg_rate = $post_data['post_channel_avg_rate'];
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_avg_rate' => $channel_avg_rate), true));
        $channel_amount = $post_data['post_channel_amount'];
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_amount' => $channel_amount), true));
        $channel_seconds = $post_data['hid_total_seconds'];
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_seconds' => $channel_seconds), true));
        $customer_ids = $post_data['hid_cust_id'];
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$customer_ids' => $customer_ids), true));
        $total_rows = 0;
        $nw_shares = $post_data['post_network_share'];
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$nw_shares' => $nw_shares), true));
        $nw_amount = $post_data['post_final_amount'];
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$nw_amount' => $nw_amount), true));

        $channel_reference_rate = $post_data['post_channel_ref_rate'];
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_reference_rate' => $channel_reference_rate), true));

        $cancel_channel = $this->get_cancel_channel_from_cache($order_id);
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$cancel_channel' => $cancel_channel), true));
        //$cancel_channel = $post_data['cancel_channel'] ;
        $cancel_channel_ids = array();

        foreach ($cancel_channel as $value) {
            if (empty($value)) continue;

            $channel_id = $value;
            unset($channel_avg_rate[$channel_id]);
            unset($channel_amount[$channel_id]);
            unset($channel_seconds[$channel_id]);
            array_push($cancel_channel_ids, $channel_id);
        }
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_avg_rate' => $channel_avg_rate), true));
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_amount' => $channel_amount), true));
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_seconds' => $channel_seconds), true));
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$cancel_channel_ids' => $cancel_channel_ids), true));
        foreach ($cache_data as $data) {
            $total_rows += 1;
            $nw_id = $data['network_id'];
            if (array_key_exists($nw_id, $customer_ids)) {
                continue;
            } else {
                $customer_ids[$nw_id] = $nw_id;
                $nw_shares[$nw_id] = $data['revenue_sharing'];
                $nw_amount[$nw_id] = $data['nw_payout'];

                foreach ($data['channels_data_array'] as $channel_data) {
                    $channel_id = $channel_data['channel_id'];

                    if (isset($channel_data['channel_spot_amount'])) {
                        $channel_avg_rate[$channel_id][0] = $channel_data['channel_spot_avg_rate'];
                        $channel_reference_rate[$channel_id][0] = $channel_data['channel_spot_reference_rate'];
                        $channel_amount[$channel_id][0] = $channel_data['channel_spot_amount'];
                        $channel_seconds[$channel_id][0] = $channel_data['total_spot_ad_seconds'];
                    }

                    if (isset($channel_data['channel_banner_amount'])) {
                        $channel_avg_rate[$channel_id][1] = $channel_data['channel_banner_avg_rate'];
                        $channel_reference_rate[$channel_id][1] = $channel_data['channel_banner_reference_rate'];
                        $channel_amount[$channel_id][1] = $channel_data['channel_banner_amount'];
                        $channel_seconds[$channel_id][1] = $channel_data['total_banner_ad_seconds'];
                    }
                }
            }
        }
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_avg_rate' => $channel_avg_rate), true));
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_reference_rate' => $channel_reference_rate), true));
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_amount' => $channel_amount), true));
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$channel_seconds' => $channel_seconds), true));

        $return_data = array();
        $return_data['channel_avg_rate'] = $channel_avg_rate;
        $return_data['channel_reference_rate'] = $channel_reference_rate;
        $return_data['channel_amount'] = $channel_amount;
        $return_data['channel_seconds'] = $channel_seconds;
        $return_data['network_share'] = $nw_shares;
        $return_data['network_amount'] = $nw_amount;
        $return_data['customer_ids'] = $customer_ids;
        $return_data['total_rows'] = $total_rows;
        $return_data['cancel_channel'] = $cancel_channel_ids;
        log_message('INFO', 'In ro_manager@add_data_from_cache | ' . print_r(array('$return_data' => $return_data), true));

        return $return_data;
    }

    public function get_cancel_channel_from_cache($order_id)
    {
        $user_id = $this->get_user_id();
        $key = 'add_cancel_channel_' . $user_id . "_" . $order_id;
        $cancel_channel_arr = $this->memcache->get($key);
        return $cancel_channel_arr;
    }

    public function make_post_data_as_load($post_data)
    {
        $post_channel_avg_rate = $post_data['post_channel_avg_rate'];
        $post_channel_amount = $post_data['post_channel_amount'];
        $post_channel_seconds = $post_data['hid_total_seconds'];
        $post_nw_share = $post_data['post_network_share'];
        $post_final_amount = $post_data['post_final_amount'];
        $order_id = $post_data['hid_internal_ro'];
        $post_channel_ref_rate = $post_data['post_channel_ref_rate'];
        //$cancel_channel_ids = $post_data['cancel_channel'] ;

        $cancel_channel_ids = $this->get_cancel_channel_from_cache($order_id);

        $data = array();
        $data['total_nw_payout'] = $post_data['hid_total_network_payout'];

        /*$data[''] = $post_data['hid_surewaves_share'] ;
              $data[''] = $post_data['hid_surewaves_share_per'] ; */

        foreach ($post_channel_seconds as $channel_id => $value) {
            $customer_detail = $this->ro_model->get_customer_market_detail($channel_id);

            $network_id = $customer_detail['customer_id'];

            if (array_key_exists($network_id, $data['nw_data'])) {
                foreach ($value as $region_id => $value_1) {
                    if (array_key_exists($channel_id, $data['nw_data'][$network_id]['channels_data_array'])) {
                        if ($region_id == 0) {
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['spot_region_id'] = '1';
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['total_spot_ad_seconds'] = $value_1;
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_avg_rate'] = $post_channel_avg_rate[$channel_id][$region_id];
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_reference_rate'] = $post_channel_ref_rate[$channel_id][$region_id];
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_amount'] = $post_channel_amount[$channel_id][$region_id];
                        } else if ($region_id == 1) {
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['banner_region_id'] = '3';
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['total_banner_ad_seconds'] = $value_1;
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_avg_rate'] = $post_channel_avg_rate[$channel_id][$region_id];
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_reference_rate'] = $post_channel_ref_rate[$channel_id][$region_id];
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_amount'] = $post_channel_amount[$channel_id][$region_id];
                        }

                        if (!isset($data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_avg_rate']) || empty($data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_avg_rate'])) {
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_avg_rate'] = 0;
                        }
                        if (!isset($data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_avg_rate']) || empty($data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_avg_rate'])) {
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_avg_rate'] = 0;
                        }

                        if (!isset($data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_amount']) || empty($data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_amount'])) {
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_spot_amount'] = 0;
                        }
                        if (!isset($data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_amount']) || empty($data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_amount'])) {
                            $data['nw_data'][$network_id]['channels_data_array'][$channel_id]['channel_banner_amount'] = 0;
                        }
                    } else {
                        $tmp = array();
                        $tmp['channel_id'] = $channel_id;

                        $cancel_channel = 'no';
                        if (in_array($channel_id, $cancel_channel_ids)) {
                            $cancel_channel = 'yes';
                        }

                        $tmp['channel_name'] = $this->ro_model->get_channel_name($channel_id);
                        //get channel name for given channel id
                        //$tmp['channel_name'] = $chnl_details['channel_name'];

                        if ($region_id == 0) {
                            $tmp['spot_region_id'] = '1';
                            $tmp['total_spot_ad_seconds'] = $value_1;
                            $tmp['channel_spot_avg_rate'] = $post_channel_avg_rate[$channel_id][$region_id];
                            $tmp['channel_spot_reference_rate'] = $post_channel_ref_rate[$channel_id][$region_id];
                            $tmp['channel_spot_amount'] = $post_channel_amount[$channel_id][$region_id];
                        } else if ($region_id == 1) {
                            $tmp['banner_region_id'] = '3';
                            $tmp['total_banner_ad_seconds'] = $value_1;
                            $tmp['channel_banner_avg_rate'] = $post_channel_avg_rate[$channel_id][$region_id];
                            $tmp['channel_banner_reference_rate'] = $post_channel_ref_rate[$channel_id][$region_id];
                            $tmp['channel_banner_amount'] = $post_channel_amount[$channel_id][$region_id];
                        }
                        if (!isset($tmp['channel_spot_avg_rate']) || empty($tmp['channel_spot_avg_rate'])) {
                            $tmp['channel_spot_avg_rate'] = 0;
                        }
                        if (!isset($tmp['channel_banner_avg_rate']) || empty($tmp['channel_banner_avg_rate'])) {
                            $tmp['channel_banner_avg_rate'] = 0;
                        }

                        if (!isset($tmp['channel_spot_amount']) || empty($tmp['channel_spot_amount'])) {
                            $tmp['channel_spot_amount'] = 0;
                        }
                        if (!isset($tmp['channel_banner_amount']) || empty($tmp['channel_banner_amount'])) {
                            $tmp['channel_banner_amount'] = 0;
                        }

                        $tmp['customer_share'] = $post_nw_share[$network_id];
                        $tmp['approved'] = 0;
                        $tmp['cancel_channel'] = $cancel_channel;

                        //array_push($nw_tmp['channels_data_array'],$tmp);
                        $data['nw_data'][$network_id]['channels_data_array'][$channel_id] = $tmp;
                    }
                }
            } else {
                $nw_tmp = array();
                $nw_tmp['network_id'] = $network_id;
                $nw_tmp['network_name'] = $customer_detail['customer_name'];
                $nw_tmp['market_name'] = $customer_detail['sw_market_name'];

                $nw_tmp['channels_data_array'] = array();
                foreach ($value as $region_id => $value_1) {
                    if (array_key_exists($channel_id, $nw_tmp['channels_data_array'])) {
                        if ($region_id == 0) {
                            $nw_tmp['channels_data_array'][$channel_id]['spot_region_id'] = '1';
                            $nw_tmp['channels_data_array'][$channel_id]['total_spot_ad_seconds'] = $value_1;
                            $nw_tmp['channels_data_array'][$channel_id]['channel_spot_avg_rate'] = $post_channel_avg_rate[$channel_id][$region_id];
                            $nw_tmp['channels_data_array'][$channel_id]['channel_spot_reference_rate'] = $post_channel_ref_rate[$channel_id][$region_id];
                            $nw_tmp['channels_data_array'][$channel_id]['channel_spot_amount'] = $post_channel_amount[$channel_id][$region_id];
                        } else if ($region_id == 1) {
                            $nw_tmp['channels_data_array'][$channel_id]['banner_region_id'] = '3';
                            $nw_tmp['channels_data_array'][$channel_id]['total_banner_ad_seconds'] = $value_1;
                            $nw_tmp['channels_data_array'][$channel_id]['channel_banner_avg_rate'] = $post_channel_avg_rate[$channel_id][$region_id];
                            $nw_tmp['channels_data_array'][$channel_id]['channel_banner_reference_rate'] = $post_channel_ref_rate[$channel_id][$region_id];
                            $nw_tmp['channels_data_array'][$channel_id]['channel_banner_amount'] = $post_channel_amount[$channel_id][$region_id];
                        }

                        if (!isset($nw_tmp['channels_data_array'][$channel_id]['channel_spot_avg_rate']) || empty($nw_tmp['channels_data_array'][$channel_id]['channel_spot_avg_rate'])) {
                            $nw_tmp['channels_data_array'][$channel_id]['channel_spot_avg_rate'] = 0;
                        }
                        if (!isset($nw_tmp['channels_data_array'][$channel_id]['channel_banner_avg_rate']) || empty($nw_tmp['channels_data_array'][$channel_id]['channel_banner_avg_rate'])) {
                            $nw_tmp['channels_data_array'][$channel_id]['channel_banner_avg_rate'] = 0;
                        }

                        if (!isset($nw_tmp['channels_data_array'][$channel_id]['channel_spot_amount']) || empty($nw_tmp['channels_data_array'][$channel_id]['channel_spot_amount'])) {
                            $nw_tmp['channels_data_array'][$channel_id]['channel_spot_amount'] = 0;
                        }
                        if (!isset($nw_tmp['channels_data_array'][$channel_id]['channel_banner_amount']) || empty($nw_tmp['channels_data_array'][$channel_id]['channel_banner_amount'])) {
                            $nw_tmp['channels_data_array'][$channel_id]['channel_banner_amount'] = 0;
                        }
                    } else {
                        $tmp = array();
                        $tmp['channel_id'] = $channel_id;

                        $cancel_channel = 'no';
                        if (in_array($channel_id, $cancel_channel_ids)) {
                            $cancel_channel = 'yes';
                        }

                        $tmp['channel_name'] = $this->ro_model->get_channel_name($channel_id);
                        //get channel name for given channel id
                        //$tmp['channel_name'] = $chnl_details['channel_name'];

                        if ($region_id == 0) {
                            $tmp['spot_region_id'] = '1';
                            $tmp['total_spot_ad_seconds'] = $value_1;
                            $tmp['channel_spot_avg_rate'] = $post_channel_avg_rate[$channel_id][$region_id];
                            $tmp['channel_spot_reference_rate'] = $post_channel_ref_rate[$channel_id][$region_id];
                            $tmp['channel_spot_amount'] = $post_channel_amount[$channel_id][$region_id];
                        } else if ($region_id == 1) {
                            $tmp['banner_region_id'] = '3';
                            $tmp['total_banner_ad_seconds'] = $value_1;
                            $tmp['channel_banner_avg_rate'] = $post_channel_avg_rate[$channel_id][$region_id];
                            $tmp['channel_banner_reference_rate'] = $post_channel_ref_rate[$channel_id][$region_id];
                            $tmp['channel_banner_amount'] = $post_channel_amount[$channel_id][$region_id];
                        }
                        if (!isset($tmp['channel_spot_avg_rate']) || empty($tmp['channel_spot_avg_rate'])) {
                            $tmp['channel_spot_avg_rate'] = 0;
                        }
                        if (!isset($tmp['channel_banner_avg_rate']) || empty($tmp['channel_banner_avg_rate'])) {
                            $tmp['channel_banner_avg_rate'] = 0;
                        }

                        if (!isset($tmp['channel_spot_amount']) || empty($tmp['channel_spot_amount'])) {
                            $tmp['channel_spot_amount'] = 0;
                        }
                        if (!isset($tmp['channel_banner_amount']) || empty($tmp['channel_banner_amount'])) {
                            $tmp['channel_banner_amount'] = 0;
                        }

                        $tmp['customer_share'] = $post_nw_share[$network_id];
                        $tmp['approved'] = 0;
                        $tmp['cancel_channel'] = $cancel_channel;

                        //array_push($nw_tmp['channels_data_array'],$tmp);
                        $nw_tmp['channels_data_array'][$channel_id] = $tmp;
                    }

                }
                $nw_tmp['nw_payout'] = $post_final_amount[$network_id];
                $nw_tmp['revenue_sharing'] = $post_nw_share[$network_id];
                $nw_tmp['approved'] = 0;
                $data['nw_data'][$network_id] = $nw_tmp;
                //array_push($nw_data_array,$nw_tmp) ;
            }
        }
        //keep network payout and other stuff in data array index
        //$data['']

        return $data;
    }

    public function add_reamin_data_from_cache($am_ro_id, $post_data, $order_id)
    {
        $cache_data = $this->memcache->get($am_ro_id);

        //data which is posted made structure as cache
        $posted_data = $post_data;

        foreach ($cache_data as $data) {
            $nw_id = $data['network_id'];
            if (array_key_exists($nw_id, $posted_data['nw_data'])) {
                continue;
            } else {
                $posted_data['nw_data'][$nw_id] = $data;
            }
        }
        //save data only for loading after confirmation
        $user_id = $this->get_user_id();
        $key = "loadSave_" . $user_id . "_" . $order_id;

        //setting the cache for loading
        $this->memcache->set($key, $posted_data, 86400);

    }

    public function show_field($action, $keys)
    {
        $keys = explode("::", $keys);
        $action = str_replace(":::", "/", $action);
        $action = base_url() . $action;
        $model = array();
        $model['keys'] = $keys;
        $model['action'] = $action;
        $this->load->view("update_value", $model);
    }

    public function update_value()
    {
        print_r($_POST);
        $user_data = array();
        foreach ($_POST as $key => $value) {
            $user_data[$key] = $value;
        }

        /*              $user_data = array(
                                                'billing_name' => $this->input->post('billing_name')
                                        ) ;

*/
        $this->ro_model->update_billing_name($this->input->post('customer_id'), $user_data);
    }

    public function add_file_location()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $internal_ro = $this->input->post('order_id');
        $edit = $this->input->post('edit');
        $am_ro_id = $this->input->post('id');
        $order = $this->ro_model->get_ro_details(($internal_ro));
        $selected_region_ids = $this->mg_model->get_screen_region_ids(($internal_ro));
        $region_ids = array();
        foreach ($selected_region_ids as $region) {
            array_push($region_ids, $region['selected_region_id']);
        }
        foreach ($region_ids as $key => $id) {
            $campaigns[$key] = $this->mg_model->get_order_campaigns(($internal_ro), $id);

            $camp_ids[$key] = array();
            foreach ($campaigns[$key] as $key1 => $camp) {
                array_push($camp_ids[$key], $camp['campaign_id']);
            }
            $channels_summary[$key] = $this->mg_model->get_channel_schedule_summary(implode(',', $camp_ids[$key]), $id);
        }
        $summary = array();
        foreach ($channels_summary as $key => $channels) {
            foreach ($channels as $key1 => $ch) {
                $summary[] = $ch;
            }
        }
        //filter channel which is being cancelled
        $cancel_channel = $this->mg_model->get_cancel_channel_for_internal_ro(($internal_ro));
        $summary = $this->mg_model->filter_schedule_channel($summary, $cancel_channel);

        $channels = array();
        $channel_ids = array();
        foreach ($summary as $chanl) {
            if (!in_array($chanl['channel_id'], $channel_ids)) {
                $tmp = array();
                $tmp['channel_id'] = $chanl['channel_id'];
                $tmp['channel_name'] = $chanl['channel_name'];
                $tmp['market_name'] = $chanl['sw_market_name'];

                array_push($channel_ids, $chanl['channel_id']);
                array_push($channels, $tmp);
            }
        }
        $ro_channels = $channels;
        $value = 0;
        if (empty($ro_channels)) {
            $value = 0;
        } else {
            $value = 1;
        }
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['internal_ro'] = $internal_ro;

        $channels_detail = array();
        foreach ($ro_channels as $chnls) {
            $tmp = array();
            $tmp['channel_id'] = $chnls['channel_id'];
            $tmp['channel_name'] = $chnls['channel_name'];
            $tmp['market_name'] = $chnls['market_name'];

            $file_location = $this->ro_model->get_file_location($chnls['channel_id'], ($internal_ro));
            $tmp['file_location'] = $file_location[0]['file_location'];
            array_push($channels_detail, $tmp);
        }
        log_message('Info','Channel Detaisl => '.print_r($channels_detail,True));

        $marketWiseChannels = array();
        
        foreach($channels_detail as $ch){
            if(!array_key_exists($ch['market_name'], $marketWiseChannels))
                $marketWiseChannels[$ch['market_name']] = array();
            array_push($marketWiseChannels[$ch['market_name']],
            array('channel_id' => $ch['channel_id'],
            'channel_name' => $ch['channel_name'],
            'file_location' => $ch['file_location']
        ));
        }
        log_message('INFO','In add_file_location | MarketWiseChannels => '.print_r($marketWiseChannels,True));
        $model['channels'] = $marketWiseChannels;
        $model['value'] = $value;
        $model['edit'] = $edit;
        $model['id'] = $am_ro_id;
//        $this->load->view('ro_manager/add_file_location', $model);

        log_message('INFO','In add_file_location | Model => '.print_r($model,True));
        $response['Status'] = 'success';
        $response['isLoggedIn'] = 1;
        $response['Message'] = 'Add file location view!';
        $response['Data']['html'] = $this->load->view('ro_manager/add_file_location', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }


    //request for network remittance report on mail : Nitish 26 feb 2015

    public function post_add_file_location()
    {
        $this->is_logged_in();
        //echo print_r($this->input->post(),true);exit;

        $order_id = $this->input->post('hid_internal_ro');
        $internal_ro_number = ($order_id);
        $edit = $_POST['hid_edit'];
        $am_ro_id = $_POST['hid_id'];
        log_message('INFO', 'In ro_manager@post_add_file_location | ' . print_r(array($order_id, $internal_ro_number, $edit, $am_ro_id), true));
        //$ro_details = $this->ro_model->get_ro_details($internal_ro_number);
        //$customer_ro_number = $ro_details[0]['customer_ro_number'];

        log_message('INFO', 'In ro_manager@post_add_file_location | ' . print_r($this->input->post(), true));
        foreach ($this->input->post() as $key => $value) {
            $pos = strpos($key, 'location_');
            if ($pos !== false) {
                if (empty($value) || !isset($value) || $value == "") continue;
                $channel_key = explode("_", $key);
                $channel_id = $channel_key[1];
                $file_location = $value;

                $where = array(
                    'channel_id' => $channel_id,
                    'internal_ro_number' => $internal_ro_number
                );
                $this->db->trans_start();
                $this->ro_model->add_file_location($file_location, $where);
                $this->db->trans_complete();

            }
        }

//        echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/ro_manager/approve/' . $order_id . '/' . $edit . '/' . $am_ro_id . '";</script>';
        $response['Status'] = 'success';
        $response['Message'] = 'Files added successfully!';
        $response['Data'] = array();
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function confirm_cancel_channel($channel_id, $order_id, $edit, $am_ro_id, $channel_name)
    {
        $this->is_logged_in(1);
        $internal_ro = $order_id;
        $order_id = serialize($order_id);
        $model = array();
        $model['edit'] = $edit;
        $model['am_ro_id'] = $am_ro_id;
        $model['internal_ro'] = $internal_ro;
        $model['channel_name'] = $channel_name;
        $model['channel_id'] = $channel_id;
        $this->load->view('ro_manager/confirm_cancel_channel', $model);
    }

    public function cancel_channel_from_ro()
    {//echo $channel_id; exit;
        $channel_id = $this->input->post('hid_channel_id');
        $order_id = $this->input->post('hid_internal_ro');
        $edit = $this->input->post('hid_edit');
        $am_ro_id = $this->input->post('hid_id');
        $campaign_ids = $this->mg_model->cancel_channel_from_ro($channel_id, $order_id);

        foreach ($campaign_ids as $cmp_ids) {
            $campaign_id = $cmp_ids['campaign_id'];
//            $url = "http://tv.mediagrid100.surewaves.com/surewaves/apis/cancel_campaign.php?campaign_id=" . $campaign_id;
            $url = SERVER_NAME . "/surewaves/apis/cancel_campaign.php?campaign_id=" . $campaign_id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //curl_setopt($ch, CURLOPT_POST, 1);

            //curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            $response = curl_exec($ch);
            curl_close($ch);
        }
        sleep(5);
        $this->mg_model->delete_channel_file_location($channel_id, base64_decode($order_id));
        //redirect("ro_manager/approve/".$order_id.'/'.$edit.'/'.$am_ro_id);
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    public function network_remittance()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $this->load->model('user_model');
        $model['customer_detail'] = $this->user_model->get_all_customer_detail();

        $this->load->view('ro_manager/network_remittance', $model);
    }

    public function get_network_remittance($network_id = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $this->load->model('user_model');
        $model['customer_detail'] = $this->user_model->get_all_customer_detail();

        if (!empty($network_id)) {
            $data_values = $this->ro_model->get_ro_details_for_network($network_id);

            $data = array();
            foreach ($data_values as $val) {
                $customer_ro = trim($val['cust_ro']);
                if (!isset($data[$customer_ro])) {
                    $data[$customer_ro] = array();

                    $data[$customer_ro]['customer_ro'] = $val['cust_ro'];
                    $data[$customer_ro]['internal_ro'] = $val['internal_ro'];
                    $data[$customer_ro]['agency'] = $val['agency'];
                    $data[$customer_ro]['client'] = $val['client'];
                    $data[$customer_ro]['camp_start_date'] = $val['camp_start_date'];
                    $data[$customer_ro]['camp_end_date'] = $val['camp_end_date'];

                    if ($val['chk_complete'] == '0') {
                        $data[$customer_ro]['client_payment_received'] = 'No';
                    } else if ($val['chk_complete'] == '1') {
                        $data[$customer_ro]['client_payment_received'] = 'Yes';
                    }
                    $data[$customer_ro]['amount_collected'] = $val['amnt_collected'];

                } else {
                    if ($customer_ro == trim($data[$customer_ro]['customer_ro'])) {
                        if ($val['chk_complete'] == '1') {
                            $data[$customer_ro]['client_payment_received'] = 'Yes';
                        }
                        $data[$customer_ro]['amount_collected'] = $data[$customer_ro]['amount_collected'] + $val['amnt_collected'];
                    }
                }
                $model['ro_details'] = $data;
            }
            $model['cid'] = $network_id;
        }

        $this->load->view('ro_manager/network_remittance', $model);
    }

    public function network_remittance_report()
    {
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $this->load->model('user_model');
        $model['customer_detail'] = $this->user_model->get_all_customer_detail();

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $this->load->view('ro_manager/network_remittance_report', $model);
    }

    public function get_network_remittance_report()
    {//echo print_r($this->input->post()) ;exit;
        $list = $this->ro_model->get_network_remittance();
        $this->output->set_header('Content-Type: application/json');
        $this->output->set_output($list);
    }

    public function get_network_remittance_report_v1()
    {
        $list = $this->ro_model->get_network_remittance_report();
        $this->output->set_header('Content-Type: application/json');
        $this->output->set_output($list);
    }

    public function nw_ro_payment()
    {
        $order_id = $this->input->post('order_id');
        $edit = $this->input->post('edit');
        $am_ro_id = $this->input->post('id');
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $payment_done_detail = $this->ro_model->is_payment_done_for_internal_ros($order_id);
        $customer_name_ignore = array();
        foreach ($payment_done_detail as $pmnt) {
            array_push($customer_name_ignore, $pmnt['network_name']);
        }
        $model['customer_detail'] = $this->ro_model->get_approved_customer_for_internal_ros($order_id, $customer_name_ignore);
        //$model['customer_detail'] = $this->ro_model->get_all_customer_detail_for_internal_ros(base64_decode($order_id));
        $model['order_id'] = rtrim(base64_encode($order_id), '=');

        $payment_detail = $this->ro_model->get_nw_payment_for_internal_ros($order_id);


        $model['remittance_payment_detail'] = $this->payment_detail_for_each_remittance($payment_detail);
        // added by lokanath
        $model['edit'] = $edit;
        $model['id'] = $am_ro_id;
        $model['internal_ro'] = $order_id;
        $model['payment_done'] = $payment_done;
        // end
//        $this->load->view('ro_manager/nw_ro_payment', $model);
        $response['Status'] = 'success';
        $response['Message'] = 'Expenses Updated!!';
        $response['Data']['html'] = $this->load->view('ro_manager/nw_ro_payment', $model, true);
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
        return;
    }

    public function payment_detail_for_each_remittance($data_values)
    {
        $data = array();
        foreach ($data_values as $val) {
            $remittance_advice_number = trim($val['remittance_advice_number']);
            if (!isset($data[$remittance_advice_number])) {
                $data[$remittance_advice_number] = array();

                $data[$remittance_advice_number]['remittance_advice_number'] = $val['remittance_advice_number'];
                $data[$remittance_advice_number]['network_name'] = $val['network_name'];
                $data[$remittance_advice_number]['date'] = $val['date'];
                $data[$remittance_advice_number]['cheque_number'] = $val['cheque_number'];
                $data[$remittance_advice_number]['cheque_date'] = $val['cheque_date'];

                if ($val['payment_paid'] == '0') {
                    $data[$remittance_advice_number]['payment_paid'] = 'No';
                } else if ($val['payment_paid'] == '1') {
                    $data[$remittance_advice_number]['payment_paid'] = 'Yes';
                }
                $data[$remittance_advice_number]['amount_paid'] = $val['amount_paid'];
                $data[$remittance_advice_number]['instructions'] = $val['instructions'];

            } else {
                if ($remittance_advice_number == trim($data[$remittance_advice_number]['remittance_advice_number'])) {
                    if ($val['payment_paid'] == '1') {
                        $data[$remittance_advice_number]['payment_paid'] = 'Yes';
                    }
                    $data[$remittance_advice_number]['amount_paid'] = $data[$remittance_advice_number]['amount_paid'] + $val['amount_paid'];

                    $data[$remittance_advice_number]['date'] = $data[$remittance_advice_number]['date'] . ", " . $val['date'];
                    $data[$remittance_advice_number]['cheque_number'] = $data[$remittance_advice_number]['cheque_number'] . ", " . $val['cheque_number'];
                    $data[$remittance_advice_number]['cheque_date'] = $data[$remittance_advice_number]['cheque_date'] . ", " . $val['cheque_date'];
                    $data[$remittance_advice_number]['instructions'] = $data[$remittance_advice_number]['instructions'] . ", " . $val['instructions'];
                }
            }
        }
        return $data;
    }

    public function post_network_ro_payment()
    {
        $order_id = $this->input->post('order_id');
        $internal_ro = base64_decode($order_id);
        $network_id = $this->input->post('network_id');
        $network_name = $this->ro_model->get_network_name_for_network_id($network_id);
        $network_name = $network_name[0]['customer_name'];

        //$all_related_ros = $this->ro_model->get_all_related_ros_for_customer_inernal_ro($network_name,$internal_ro) ;
        $ro_detail = $this->am_model->ro_detail_for_internal_ro($internal_ro);
        if (!$this->input->post('instruction')) {
            $instructions = '';
        } else {
            $instructions = $this->input->post('instruction');
        }
        $data_array = array('internal_ro_number' => $internal_ro, 'customer_name' => $network_name);
        $nw_ro_report = $this->mg_model->get_nw_ro_number_from_nw_ro_report($data_array);
        $nw_ro_number = $nw_ro_report[0]['network_ro_number'];

        //foreach($all_related_ros as $ros) {
        $user_data = array(
            'remittance_advice_number' => $this->input->post('advice_number'),
            'internal_ro_number' => $internal_ro,
            'Network_ro_number' => $nw_ro_number,
            'external_ro_number' => $ro_detail[0]['cust_ro'],
            'network_name' => $network_name,
            'amount_paid' => $this->input->post('amount_paid'),
            'date' => date('Y-m-d', strtotime($this->input->post('date_paid'))),
            'cheque_number' => $this->input->post('cheque_number'),
            'cheque_date' => date('Y-m-d', strtotime($this->input->post('cheque_date'))),
            'payment_paid' => $this->input->post('payment_paid'),
            'instructions' => $instructions
        );
        $this->ro_model->insert_into_network_ro_payment($user_data);
        //}
        $edit = $_POST['hid_edit'];
        $am_ro_id = $_POST['hid_id'];
        echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/ro_manager/approve/' . $order_id . '/' . $edit . '/' . $am_ro_id . '";</script>';
    }

    public function download_network_remittance_csv($network_id, $start_date, $end_date, $fully_paid = null)
    {
        $st_date = $start_date;
        $en_date = $end_date;
        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 00:00:00';
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $network_remittance = $this->ro_model->download_network_remittance_report_csv($network_id, $start_date, $end_date, $fully_paid);

        $this->load->helper('csv');
        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=network_remittance_report.csv');

        $csv_header = "Network Name,Network Ro Fully Paid,Full Payment Received,Client Name,Network RO Number,Network Ro Release Date,Network Ro Paid Amount,Surewaves Share Amount,Network Billing Name,External RO Number,Agency Name,Activity Start Date,Activity End Date,Report Start Date,Report End Date,Activity spot seconds,Activity spot amount,Activity banner seconds,Activity banner amount,Scheduled spot seconds,Scheduled spot amount,Scheduled banner seconds,Scheduled banner amount,Total scheduled spot seconds,Total scheduled spot amount,Total scheduled banner seconds,Total scheduled banner amount,Payment Collected Amount,External RO Amount,Network RO Amount,Service Tax,Cancelled";
        $csv_data = "";
        echo $csv_header;
        $remittance_count = count($network_remittance);

        for ($i = 0; $i < $remittance_count; $i++) {
            if (empty($network_remittance[$i]['Network_RO_fully_paid']) || !isset($network_remittance[$i]['Network_RO_fully_paid']) || ($network_remittance[$i]['Network_RO_fully_paid'] == 0) || (trim($network_remittance[$i]['Network_RO_fully_paid']) == '')) {
                $Network_RO_fully_paid = 'False';
            } else {
                $Network_RO_fully_paid = 'True';
            }
            if (empty($network_remittance[$i]['Full_Payment_Received']) || !isset($network_remittance[$i]['Full_Payment_Received']) || ($network_remittance[$i]['Full_Payment_Received'] == 0) || (trim($network_remittance[$i]['Full_Payment_Received']) == '')) {
                $Full_Payment_Received = 'False';
            } else {
                $Full_Payment_Received = 'True';
            }
            if (empty($network_remittance[$i]['Network_RO_Paid_Amount']) || !isset($network_remittance[$i]['Network_RO_Paid_Amount']) || ($network_remittance[$i]['Network_RO_Paid_Amount'] == 0) || (trim($network_remittance[$i]['Network_RO_Paid_Amount']) == '')) {
                $network_remittance[$i]['Network_RO_Paid_Amount'] = '0';
            }
            // code commented by lokanath as csv report should be same as ui
            /*else {
                                            $network_remittance[$i]['Network_RO_Paid_Amount'] = '1' ;
                                        }*/

            if (empty($network_remittance[$i]['Cancelled']) || !isset($network_remittance[$i]['Cancelled']) || ($network_remittance[$i]['Cancelled'] == 0) || (trim($network_remittance[$i]['Cancelled']) == '')) {
                $network_remittance[$i]['Cancelled'] = 'NO';
            }

            // code added for activity run spot and banner seconds
            //$activity_secoonds = $this->ro_model->get_activity_run_seconds($start_date,$end_date,$network_remittance[$i]['network_ro_number']);
            //$activity_secoonds = $this->ro_model->get_activity_run_seconds($start_date,$end_date,$network_remittance[$i]['customer_ro_number'],$network_remittance[$i]['customer_name']);
            // end

            //for extra fields in Network remittance report 18-Aug-2014
            $temp = $this->ro_model->get_activity_rate($network_remittance[$i]['network_ro_number'], $st_date, $en_date, $network_remittance[$i]['customer_name'], 1);
            $scheduled_spot_amount = $temp[0];
            $scheduled_spot_seconds = $temp[1];
            $scheduled_banner_amount = $temp[2];
            $scheduled_banner_seconds = $temp[3];


            $temp = $this->ro_model->get_activity_rate($network_remittance[$i]['network_ro_number'], $st_date, $en_date, $network_remittance[$i]['customer_name'], 0);
            $activity_spot_amount = $temp[0];
            $activity_spot_seconds = $temp[1];
            $activity_banner_amount = $temp[2];
            $activity_banner_seconds = $temp[3];

            //if activity spot/banner sec/amount is greater than scheduled spot/banner sec/amount
            //if user is advanced ro user then make sch vs activity equal
            if ($activity_spot_seconds > $scheduled_spot_seconds || $network_remittance[$i]['is_test_ro'] == 2) {
                $activity_spot_seconds = $scheduled_spot_seconds;
                $activity_spot_amount = $scheduled_spot_amount;
            }
            if ($activity_banner_seconds > $scheduled_banner_seconds || $network_remittance[$i]['is_test_ro'] == 2) {
                $activity_banner_seconds = $scheduled_banner_seconds;
                $activity_banner_amount = $scheduled_banner_amount;
            }

            if (strtotime($st_date) < strtotime($network_remittance[$i]['Activity_Start_Date'])) {
                $report_start_date = $network_remittance[$i]['Activity_Start_Date'];
            } else {
                $report_start_date = $st_date;
            }

            if (strtotime($en_date) > strtotime($network_remittance[$i]['Activity_End_Date'])) {
                $report_end_date = $network_remittance[$i]['Activity_End_Date'];
            } else {
                $report_end_date = $en_date;
            }

            $temp = $this->ro_model->get_activity_rate($network_remittance[$i]['network_ro_number'], NULL, NULL, $network_remittance[$i]['customer_name'], 1);
            $total_scheduled_spot_amount = $temp[0];
            $total_scheduled_spot_seconds = $temp[1];
            $total_scheduled_banner_amount = $temp[2];
            $total_scheduled_banner_seconds = $temp[3];
            //end

            $csv_data .= "\n";

            $csv_data .= $network_remittance[$i]['customer_name'];
            $csv_data .= "," . $Network_RO_fully_paid;
            $csv_data .= "," . $Full_Payment_Received;
            $csv_data .= "," . $network_remittance[$i]['client_name'];
            $csv_data .= "," . $network_remittance[$i]['network_ro_number'];
            $csv_data .= "," . date('y-m', strtotime($network_remittance[$i]['release_date']));
            $csv_data .= "," . $network_remittance[$i]['Network_RO_Paid_Amount'];
            $csv_data .= "," . $network_remittance[$i]['SureWaves_Share_Amount'];
            $csv_data .= "," . $network_remittance[$i]['Network_Billing_Name'];
            $csv_data .= "," . $network_remittance[$i]['customer_ro_number'];
            $csv_data .= "," . $network_remittance[$i]['agency_name'];
            $csv_data .= "," . $network_remittance[$i]['Activity_Start_Date'];
            $csv_data .= "," . $network_remittance[$i]['Activity_End_Date'];
            $csv_data .= "," . $report_start_date;
            $csv_data .= "," . $report_end_date;
            /*$csv_data .= ",".$network_remittance[$i]['Activity_Run_Spot_Seconds'];
                                        $csv_data .= ",".$network_remittance[$i]['Activity_Run_Banner_Seconds'];*/
            //$csv_data .= ",".$activity_secoonds['Activity_Run_Spot_Seconds'];
            //$csv_array[$k][14] = $activity_secoonds['Activity_Run_Banner_Seconds'];
            $csv_data .= "," . $activity_spot_seconds;
            $csv_data .= "," . $activity_spot_amount;
            $csv_data .= "," . $activity_banner_seconds;
            $csv_data .= "," . $activity_banner_amount;
            $csv_data .= "," . $scheduled_spot_seconds;
            $csv_data .= "," . $scheduled_spot_amount;
            $csv_data .= "," . $scheduled_banner_seconds;
            $csv_data .= "," . $scheduled_banner_amount;
            $csv_data .= "," . $total_scheduled_spot_seconds;
            $csv_data .= "," . $total_scheduled_spot_amount;
            $csv_data .= "," . $total_scheduled_banner_seconds;
            $csv_data .= "," . $total_scheduled_banner_amount;
            $csv_data .= "," . $network_remittance[$i]['Payment_Collected_Amount'];
            $csv_data .= "," . $network_remittance[$i]['External_RO_Amount'];
            $csv_data .= "," . $network_remittance[$i]['Network_RO_Amount'];
            $csv_data .= "," . $network_remittance[$i]['Service_Tax'];
            $csv_data .= "," . $network_remittance[$i]['Cancelled'];
            echo $csv_data;
            $csv_data = "";
        }
        //array_to_csv($csv_array, 'network_remittance_report.csv');
        // echo $csv_header.$csv_data;
    }

    public function request_network_remittance_csv()
    {

        $network_id = $this->input->post('network_id');
        $start_date = $this->input->post('from');
        $end_date = $this->input->post('to');
        $fully_paid = $this->input->post('hid_fully_paid');
        $mail_ids = $this->input->post('mail_ids');

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $timestamp = strtotime(date("m/d/Y h:i:s a", time()));

        //Insert Into Job Queue For mail request
        $job_queue_user_data = array(
            'job_code' => 'processRequestedReports',
            'done' => '0',
            'params' => 'networkRemittanceReport' . "#" . $timestamp . "#" . $start_date . "#" . $end_date . "#" . $mail_ids . "#" . $network_id . "#" . $fully_paid . "#" . $is_test_user,
            'customer_id' => 0,
        );
        $this->am_model->insert_into_job_queue($job_queue_user_data);
        redirect("ro_manager/network_remittance_report");
    }

    public function download_nw_payment_csv()
    {

        $records = $this->ro_model->download_network_payment_report_csv();
        $network_payment = json_decode($records, true);//echo '<pre>';print_r($network_remittance_details);exit;
        $this->load->helper('csv');
        $csv_array = array(
            array('Remittance Advice Number', 'Network RO Number,External Ro Number', 'Network Name', 'Amount Paid', 'Date Paid', 'Cheque Number', 'Cheque Date', 'Payment Paid', 'Instructions'));

        $network_payment_count = count($network_payment['rows']);//echo $invoice_count;exit;
        $count = count($csv_array[0]);

        for ($i = 0; $i < $network_payment_count; $i++) {
            $k = $i + 1;
            for ($j = 0; $j < $count; $j++) {
                $csv_array[$k][$j] = $network_payment['rows'][$i]['cell'][$j];
            }
        }
        //echo '<pre>';print_r($csv_array);exit;
        array_to_csv($csv_array, 'network_payment_report.csv');
    }

    public function update_network_payment()
    {
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $profile_details = $this->user_model->get_profile_details($profile_id);
        $model = array();
        $model['logged_in_user'] = $logged_in[0];

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $model['value'] = "First row should have header names: \"advice_number,network_ro_number,customer_ro_number,network_name,amount_paid,date_paid,cheque_number,cheque_date,payment_paid,instructions,\", followed by actual values in subsequents rows.";
        $this->load->view("ro_manager/update_network_payment", $model);
    }

    public function process_network_ro_payment_csv()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        //file upload
        //$this->load->helper(array('form', 'url'));
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = '*';
        //$config['max_size']	= '5000';
        $config['encrypt_name'] = TRUE;
        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_upload')) {
            $model = array();
            $model['value'] = "Invalid file. Please upload a valid CSV file.";
            $model['logged_in_user'] = $logged_in[0];
            $model['menu'] = $menu;
            $this->load->view("ro_manager/update_network_payment", $model);
        } else {
            $data = $this->upload->data();
            $file_name = $data['file_name'];
            $this->load->library('csvreader');
            $csvData = $this->csvreader->parse_file("./uploads/$file_name");

            if (!(isset($csvData[0]['advice_number'])) || !(isset($csvData[0]['network_ro_number'])) || !(isset($csvData[0]['customer_ro_number'])) ||
                !(isset($csvData[0]['network_name'])) || !(isset($csvData[0]['amount_paid'])) || !(isset($csvData[0]['date_paid'])) || !(isset($csvData[0]['cheque_number'])) || !(isset($csvData[0]['cheque_date'])) || !(isset($csvData[0]['payment_paid'])) || !(isset($csvData[0]['instructions']))) {
                $model['value'] = "CSV header is incorrect. First row should have header names: \"advice_number,network_ro_number,customer_ro_number,network_name,amount_paid,date_paid,cheque_number,cheque_date,payment_paid,instructions,\", followed by actual values in subsequents rows.";
                $model['logged_in_user'] = $logged_in[0];
                $model['menu'] = $menu;
                $this->load->view("ro_manager/update_network_payment", $model);
                return false;
            }

            $value = '';
            $to_process = TRUE;
            foreach ($csvData as $data) {
                $ro_details = $this->am_model->ro_detail_for_external_ro($data['customer_ro_number']);
                if (count($ro_details) > 0) {
                    continue;
                } else {
                    $to_process = FALSE;
                    $value = "Network:" . $data['network_name'] . " is not approved";
                    break;
                }
            }

            if (!$to_process) {
                $model['value'] = $value;
                $model['logged_in_user'] = $logged_in[0];
                $model['menu'] = $menu;
                $this->load->view("ro_manager/update_network_payment", $model);
                return false;
            } else {
                foreach ($csvData as $data) {
                    if (strcasecmp($data['payment_paid'], 'yes') == 0) {
                        $data['payment_paid'] = 1;
                    } else {
                        $data['payment_paid'] = 0;
                    }
                    $ro_details = $this->am_model->ro_detail_for_external_ro($data['customer_ro_number']);
                    $user_data = array(
                        'remittance_advice_number' => $data['advice_number'],
                        'internal_ro_number' => $ro_details[0]['internal_ro'],
                        'Network_ro_number' => $data['network_ro_number'],
                        'external_ro_number' => $data['customer_ro_number'],
                        'network_name' => $data['network_name'],
                        'amount_paid' => $data['amount_paid'],
                        'date' => $data['date_paid'],
                        'cheque_number' => $data['cheque_number'],
                        'cheque_date' => $data['cheque_date'],
                        'payment_paid' => $data['payment_paid'],
                        'instructions' => $data['instructions']
                    );
                    $this->ro_model->insert_into_network_ro_payment($user_data);
                }

                $model['menu'] = $menu;
                $model['value'] = "Successfully uploaded";
                $model['logged_in_user'] = $logged_in[0];
                $this->load->view("ro_manager/update_network_payment", $model);
            }
        }
    }

    public function edit_network($internal_ro_number = '', $customer_id = '', $edit, $am_ro_id)
    {
        $model = array();
        $internal_ro_number = base64_decode($internal_ro_number);
        //$get_all_channel_info = $this->mg_model->get_all_channel_info($internal_ro_number,$customer_id);

        $get_all_channels_of_internal_ro = $this->mg_model->get_all_channels_of_internal_ro($internal_ro_number, $customer_id);


        $array_to_be_pushed = array();
        $get_all_channel_info = array();
        foreach ($get_all_channels_of_internal_ro as $chnl_details) {
            /*$found = 0;
				foreach($get_all_channel_info as $get_all_channel_info_val){
					$client_name = $get_all_channel_info_val['client_name'];
					$customer_share = $get_all_channel_info_val['customer_share'];
					if($chnl_details['channel_id'] == $get_all_channel_info_val['tv_channel_id']){
						$found = 1;
						break;
					}
				}
				if($found == 0){
					if(!isset($customer_share) && $customer_share == ''){*/
            $nw_share_value = $this->mg_model->get_revenue_share_for_ro_customer($internal_ro_number, $customer_id);
            if (count($nw_share_value) > 0) {
                $customer_share = $nw_share_value[0]['customer_share'];
            } else {
                $customer_share = $chnl_details['revenue_sharing'];
            }

            $client_name = $chnl_details['client_name'];
            //}
            $channel_avg_rate = $this->mg_model->get_channel_avg_rate($client_name, $chnl_details['channel_name']);
            $channel_avg_rates = $this->mg_model->get_channel_avg_rates($chnl_details['channel_name']);
            $get_tv_chnl_details = $this->mg_model->get_tv_chnl_spot_bnr_avg_rates($chnl_details['channel_name']);

            if ($channel_avg_rate[0]['channel_spot_avg_rate'] != 0) {
                $array_to_be_pushed['channel_spot_avg_rate'] = $channel_avg_rate[0]['channel_spot_avg_rate'];
            } elseif ($channel_avg_rates[0]['channel_spot_avg_rate'] != 0) {
                $array_to_be_pushed['channel_spot_avg_rate'] = $channel_avg_rates[0]['channel_spot_avg_rate'];
            } else {
                $array_to_be_pushed['channel_spot_avg_rate'] = $get_tv_chnl_details[0]['spot_avg'];
            }
            if ($channel_avg_rate[0]['channel_banner_avg_rate'] != 0) {
                $array_to_be_pushed['channel_banner_avg_rate'] = $channel_avg_rate[0]['channel_banner_avg_rate'];
            } elseif ($channel_avg_rates[0]['channel_banner_avg_rate'] != 0) {
                $array_to_be_pushed['channel_banner_avg_rate'] = $channel_avg_rates[0]['channel_banner_avg_rate'];
            } else {
                $array_to_be_pushed['channel_banner_avg_rate'] = $get_tv_chnl_details[0]['banner_avg'];
            }

            $array_to_be_pushed['total_spot_ad_seconds'] = $chnl_details['total_spot_ad_seconds'];
            $array_to_be_pushed['total_banner_ad_seconds'] = $chnl_details['total_banner_ad_seconds'];
            $array_to_be_pushed['tv_channel_id'] = $chnl_details['channel_id'];
            $array_to_be_pushed['channel_name'] = $chnl_details['channel_name'];
            $array_to_be_pushed['channel_spot_amount'] = ($array_to_be_pushed['channel_spot_avg_rate'] * $array_to_be_pushed['total_spot_ad_seconds']) / 10;
            $array_to_be_pushed['channel_banner_amount'] = ($array_to_be_pushed['channel_banner_avg_rate'] * $array_to_be_pushed['total_banner_ad_seconds']) / 10;
            $array_to_be_pushed['customer_share'] = $customer_share;

            array_push($get_all_channel_info, $array_to_be_pushed);
            //}
        }
        //echo '<pre>';print_r($get_all_channel_info);
        $cancel_channel = $this->mg_model->get_cancel_channel_for_internal_ro($internal_ro_number);
        $get_all_channel_info = $this->mg_model->filter_schedule_channel_v1($get_all_channel_info, $cancel_channel);

        $model['internal_ro_number'] = $internal_ro_number;
        $model['customer_id'] = $customer_id;
        $model['get_all_channel_info'] = $get_all_channel_info;
        $model['edit'] = $edit;
        $model['am_ro_id'] = $am_ro_id;
        $this->load->view("ro_manager/edit_network", $model);
    }

    public function cancel_nw_channel($internal_ro_number = '', $customer_id = '', $edit, $am_ro_id)
    {
        $schedule_channel = $this->mg_model->get_channels_schedule_for_internal_ro_customer(base64_decode($internal_ro_number), $customer_id);
        $cancel_channel = $this->mg_model->get_cancel_channel_for_internal_ro(base64_decode($internal_ro_number));
        $channel_detail = $this->mg_model->filter_schedule_channel($schedule_channel, $cancel_channel);

//				//filter from ro_approved_nw
//				$approved_nw_array = array('internal_ro_number' => base64_decode($internal_ro_number),'customer_id'=>$customer_id) ;
//				$approved_nw_data = $this->mg_model->get_approved_nw($approved_nw_array);
//				$channel_detail = $this->mg_model->filter_schedule_channel_v2($approved_nw_data,$channel_detail) ;

        $model = array();
        $model['internal_ro_number'] = $internal_ro_number;
        $model['customer_id'] = $customer_id;
        $model['channels_detail'] = $channel_detail;
        $model['edit'] = $edit;
        $model['id'] = $am_ro_id;
        $this->load->view("ro_manager/cancel_nw_channel", $model);
    }

    public function post_cancel_nw_channel()
    {
        $channel_ids = $this->input->post('channel_ids');
        $order_id = $this->input->post('hid_internal_ro');
        $edit = $this->input->post('hid_edit');
        $am_ro_id = $this->input->post('hid_id');
        $cid = $this->input->post('hid_cid');

        $internal_ro_number = base64_decode($order_id);
        $logged_in = $this->session->userdata("logged_in_user");
        $user_id = $logged_in[0]['user_id'];

        $ro_detail = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);
        $update_revision_no = 0;
        $update_field = FALSE;

        foreach ($channel_ids as $id) {
            //Get Data from ro_approved_network
            $approved_nw_data = array(
                'tv_channel_id' => $id,
                'internal_ro_number' => $internal_ro_number,
                'customer_id' => $cid
            );
            $ro_approved_nw_data = $this->mg_model->get_approved_data_for_customer_channel_ro($approved_nw_data);
            $revision_no = $ro_approved_nw_data[0]['revision_no'];
            $update_revision_no = $revision_no + 1;

            // get_campaigns_for_internal_ro_number
            $campaigns = $this->mg_model->get_campaigns_for_internal_ro_and_channel($internal_ro_number, $id);
            $campaign_id = array();
            foreach ($campaigns as $campaigns_val) {
                array_push($campaign_id, $campaigns_val['campaign_id']);
            }
            $campaign_ids = implode(",", $campaign_id);


            // update advertiser_screen_dates
            $date_of_cancel = date('Y-m-d');
            $approved = FALSE;
            if (count($ro_approved_nw_data) > 0) {
                $approved = TRUE;
                $date_of_cancel = date("Y-m-d", strtotime("+2 day"));
            } else {
                $date_of_cancel = date('Y-m-d');;
            }
            $this->am_model->update_advertiser_screens_dates($campaign_ids, $date_of_cancel);

            //Insert into ro_cancel_channel
            $user_data = array(
                'channel_id' => $id,
                'internal_ro_number' => $internal_ro_number,
                'marker_for_cancellation' => 0,
                'user_id' => $user_id,
                'cancel_requested_time' => date('Y-m-d H:i:s')
            );
            $this->mg_model->insert_into_cancel_channel($user_data);

            if ($approved) {
                //insert data into history
                $this->maintain_historical_data($ro_approved_nw_data, 'cancel_channel', $ro_detail[0]['id'], $user_id);
                //get number of impression for channel(remaining)
                $channel_impression = $this->mg_model->get_channel_impression($campaign_ids, $id);
                //echo '<pre>';print_r($channel_impression);exit;

                if (($channel_impression['total_spot_ad_seconds'] == 0) && ($channel_impression['total_banner_ad_seconds'] == 0)) {
                    //delete from approved n/w
                    $update_field = TRUE;
                    //$this->mg_model->delete_approved_data_for_customer_channel_ro($approved_nw_data) ;
                    $update_approved_nw_data = array(
                        'total_spot_ad_seconds' => $channel_impression['total_spot_ad_seconds'],
                        'channel_spot_amount' => ($channel_impression['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                        'total_banner_ad_seconds' => $channel_impression['total_banner_ad_seconds'],
                        'channel_banner_amount' => ($channel_impression['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                    );
                    $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $approved_nw_data);
                } else {
                    $update_field = TRUE;
                    $update_approved_nw_data = array(
                        'total_spot_ad_seconds' => $channel_impression['total_spot_ad_seconds'],
                        'channel_spot_amount' => ($channel_impression['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                        'total_banner_ad_seconds' => $channel_impression['total_banner_ad_seconds'],
                        'channel_banner_amount' => ($channel_impression['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                    );
                    $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $approved_nw_data);
                }
            }
        }

        if ($update_field) {
            //update revision number,pdf generation status in ro_approved_network
            $endDateCrossed = $this->mg_model->checkEndDateCrossedForROCid($internal_ro_number, $cid);
            if (!$endDateCrossed) {
                $revision_pdf = array(
                    'revision_no' => $update_revision_no,
                    'pdf_generation_status' => 0
                );
                $where_data = array(
                    'internal_ro_number' => $internal_ro_number,
                    'customer_id' => $cid
                );
                $this->mg_model->update_approved_data_where_ro_customer($revision_pdf, $where_data);
            }

        }


        //update external ro report detail
        $this->update_into_external_ro_report_detail($internal_ro_number);

        echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/ro_manager/approve/' . $order_id . '/' . $edit . '/' . $am_ro_id . '";</script>';
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

    public function post_edit_network()
    {
        //echo '<pre>';print_r($_POST);
        // $post_value = $this->input->$this->input->post();
        $logged_in = $this->session->userdata("logged_in_user");
        $user_id = $logged_in[0]['user_id'];

        $ext_ro_id = $this->input->post('hid_id');
        $edit = $this->input->post('hid_edit');
        $internal_ro_number = $this->input->post('hid_internal_ro');
        $order_id = rtrim(base64_encode($internal_ro_number), '=');
        $customer_id = $this->input->post('hid_customer_id');
        $network_share = $this->input->post('post_network_share');
        $network_share = $network_share[0];

        $network_final_amnt = $this->input->post('post_final_amount');
        $network_final_amnt = $network_final_amnt[0];

        $channel_avg_rate = $this->input->post('post_channel_avg_rate');
        $channel_amount = $this->input->post('post_channel_amount');
        $channel_total_seconds = $this->input->post('post_channel_total_add_sec');

        $ro_detail = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);
        //converting uniform array for generalization
        $edit_nw_val = $this->make_uniform_structure_for_edit_nw($channel_avg_rate, $channel_amount, $channel_total_seconds);

        //verify whether entry available for customer id and internal_ro_number,for extra n/w edition
        $ro_customer = array(
            'internal_ro_number' => $internal_ro_number,
            'customer_id' => $customer_id
        );
        $data_avail_for_ro_customer = $this->mg_model->get_approved_data_for_customer_channel_ro($ro_customer);

        if (count($data_avail_for_ro_customer) > 0) {
            $update_revision_no = 0;
            foreach ($edit_nw_val as $channel_id => $value) {
                //Get Data from ro_approved_network
                $approved_nw_data = array(
                    'tv_channel_id' => $channel_id,
                    'internal_ro_number' => $internal_ro_number,
                    'customer_id' => $customer_id
                );
                $ro_approved_nw_data = $this->mg_model->get_approved_data_for_customer_channel_ro($approved_nw_data);
                if (count($ro_approved_nw_data) > 0) {
                    //update the value
                    $update_revision_no = $ro_approved_nw_data[0]['revision_no'] + 1;

                    //insert data into history
                    $this->maintain_historical_data($ro_approved_nw_data, 'edit', $ext_ro_id, $user_id);

                    foreach ($value as $region_id => $rate_amnt) {
                        if ($region_id == 1) {
                            $region_array = array(
                                'channel_spot_avg_rate' => $rate_amnt['channel_avg_rate'],
                                'channel_spot_amount' => $rate_amnt['channel_amount'],
                                'total_spot_ad_seconds' => $rate_amnt['channel_seconds']
                            );

                        } else if ($region_id == 3) {
                            $region_array = array(
                                'channel_banner_avg_rate' => $rate_amnt['channel_avg_rate'],
                                'channel_banner_amount' => $rate_amnt['channel_amount'],
                                'total_banner_ad_seconds' => $rate_amnt['channel_seconds']
                            );
                        }
                        $this->mg_model->update_approved_data_where_ro_customer($region_array, $approved_nw_data);

                    }


                } else {
                    //insert,for adding extra channel after approval
                    $channel_id_array = array('tv_channel_id' => $channel_id);
                    $channel_name = $this->mg_model->get_channel_detail($channel_id_array);
                    $update_revision_no = $data_avail_for_ro_customer[0]['revision_no'] + 1;

                    $insert_data = array(
                        'internal_ro_number' => $internal_ro_number,
                        'client_name' => $data_avail_for_ro_customer[0]['client_name'],
                        'customer_id' => $data_avail_for_ro_customer[0]['customer_id'],
                        'customer_name' => $data_avail_for_ro_customer[0]['customer_name'],
                        'customer_share' => $network_share,
                        'customer_location' => $data_avail_for_ro_customer[0]['customer_location'],
                        'tv_channel_id' => $channel_id,
                        'channel_name' => $channel_name[0]['channel_name'],
                        'channel_approval_status' => 1,
                        'pdf_generation_status' => 1,
                        'billing_name' => $data_avail_for_ro_customer[0]['billing_name'],
                        'revision_no' => $data_avail_for_ro_customer[0]['revision_no']

                    );
                    $this->ro_model->add_amount($insert_data);
                    foreach ($value as $region_id => $rate_amnt) {

                        if ($region_id == 1) {
                            $region_array = array(
                                'channel_spot_avg_rate' => $rate_amnt['channel_avg_rate'],
                                'channel_spot_amount' => $rate_amnt['channel_amount'],
                                'total_spot_ad_seconds' => $rate_amnt['channel_seconds']
                            );

                        } else if ($region_id == 3) {
                            $region_array = array(
                                'channel_banner_avg_rate' => $rate_amnt['channel_avg_rate'],
                                'channel_banner_amount' => $rate_amnt['channel_amount'],
                                'total_banner_ad_seconds' => $rate_amnt['channel_seconds']
                            );
                        }

                        $this->mg_model->update_approved_data_where_ro_customer($region_array, $approved_nw_data);

                    }

                }
            }//End of Foreach $edit_nw_val

            //update revision number,pdf generation status in ro_approved_network
            $revision_pdf = array(
                'customer_share' => $network_share,
                'revision_no' => $update_revision_no,
                'pdf_generation_status' => 0
            );
            $where_data = array(
                'internal_ro_number' => $internal_ro_number,
                'customer_id' => $customer_id
            );

            $this->mg_model->update_approved_data_where_ro_customer($revision_pdf, $where_data);
        } else {
            //extra n/w addition  after approval
            $update_revision_no = 0;
            $customer_data_array = array('customer_id' => $customer_id);
            $customer_detail = $this->mg_model->get_customer_detail($customer_data_array);

            $ro_data_array = array(
                'internal_ro_number' => $internal_ro_number
            );
            $data_ro = $this->mg_model->get_approved_data_for_customer_channel_ro($ro_data_array);
            foreach ($edit_nw_val as $channel_id => $value) {
                $approved_nw_data = array(
                    'tv_channel_id' => $channel_id,
                    'internal_ro_number' => $internal_ro_number,
                    'customer_id' => $customer_id
                );
                $channel_data_array = array('tv_channel_id' => $channel_id);
                $channel_detail = $this->mg_model->get_channel_detail($channel_data_array);

                $insert_data = array(
                    'internal_ro_number' => $internal_ro_number,
                    'client_name' => $data_ro[0]['client_name'],
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_detail[0]['customer_name'],
                    'customer_share' => $network_share,
                    'customer_location' => $customer_detail[0]['customer_location'],
                    'tv_channel_id' => $channel_id,
                    'channel_name' => $channel_detail[0]['channel_name'],
                    'channel_approval_status' => 1,
                    'pdf_generation_status' => 1,
                    'billing_name' => $customer_detail[0]['billing_name'],
                    'revision_no' => 0

                );
                $this->ro_model->add_amount($insert_data);
                foreach ($value as $region_id => $rate_amnt) {
                    if ($region_id == 1) {
                        $region_array = array(
                            'channel_spot_avg_rate' => $rate_amnt['channel_avg_rate'],
                            'channel_spot_amount' => $rate_amnt['channel_amount'],
                            'total_spot_ad_seconds' => $rate_amnt['channel_seconds']
                        );

                    } else if ($region_id == 3) {
                        $region_array = array(
                            'channel_banner_avg_rate' => $rate_amnt['channel_avg_rate'],
                            'channel_banner_amount' => $rate_amnt['channel_amount'],
                            'total_banner_ad_seconds' => $rate_amnt['channel_seconds']
                        );
                    }

                    $this->mg_model->update_approved_data_where_ro_customer($region_array, $approved_nw_data);
                }
                //update revision number,pdf generation status in ro_approved_network
                $revision_pdf = array(
                    'revision_no' => $update_revision_no,
                    'pdf_generation_status' => 0
                );
                $where_data = array(
                    'internal_ro_number' => $internal_ro_number,
                    'customer_id' => $customer_id
                );

                $this->mg_model->update_approved_data_where_ro_customer($revision_pdf, $where_data);
            }
        }//else extra n/w addition  after approval

        $this->update_into_external_ro_report_detail($internal_ro_number);
        echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/ro_manager/approve/' . $order_id . '/' . $edit . '/' . $ext_ro_id . '";</script>';
    }

    public function make_uniform_structure_for_edit_nw($channel_avg_rate, $channel_amount, $channel_total_seconds = null)
    {
        $data = array();

        foreach ($channel_avg_rate as $channel_id => $value) {
            if (!array_key_exists($channel_id, $data)) {
                $data[$channel_id] = array();
            }
            foreach ($value as $region_id => $avg_rate) {
                if (!array_key_exists($region_id, $data[$channel_id])) {
                    $data[$channel_id][$region_id] = array();
                }
                $data[$channel_id][$region_id]['channel_avg_rate'] = $avg_rate;
            }
        }

        foreach ($channel_amount as $channel_id => $value) {
            if (!array_key_exists($channel_id, $data)) {
                $data[$channel_id] = array();
            }
            foreach ($value as $region_id => $amount) {
                if (!array_key_exists($region_id, $data[$channel_id])) {
                    $data[$channel_id][$region_id] = array();
                }
                $data[$channel_id][$region_id]['channel_amount'] = $amount;
            }
        }

        foreach ($channel_total_seconds as $channel_id => $value) {
            if (!array_key_exists($channel_id, $data)) {
                $data[$channel_id] = array();
            }
            foreach ($value as $region_id => $seconds) {
                if (!array_key_exists($region_id, $data[$channel_id])) {
                    $data[$channel_id][$region_id] = array();
                }
                $data[$channel_id][$region_id]['channel_seconds'] = $seconds;
            }
        }
        //echo '<pre>';print_r($edit_nw_data);exit;
        return $data;
    }

    public function insert_into_external_ro_report_detail($order_id)
    {
        //if not inserted into ro_external_report_detail uncomment
        $internal_ro_number = base64_decode($order_id);
        //select ac.customer_ro_number,ac.internal_ro_number,ac.client_name,ac.agency_name,MIN(ac.start_date) as start_date,MAX(ac.end_date) as end_date,r.ro_amount as gross_ro_amount,r.agency_commission_amount,r.agency_rebate as agency_rebate, r.agency_rebate_on, (r.marketing_promotion_amount+r.field_activation_amount+r.sales_commissions_amount+r.creative_services_amount+r.other_expenses_amount) as other_expenses from  sv_advertiser_campaign as ac,ro_amount as r where r.internal_ro_number = 'SW/13-0009/Bharatiya Janata Party/Madison/Apr-2014-001'and ac.internal_ro_number = 'SW/13-0009/Bharatiya Janata Party/Madison/Apr-2014-001'
        $ro_detail = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);

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
        $actual_net_amount = $gross_ro_amount - $agency_commission_amount - $agency_rebate - $other_expenses;
        $net_contribution_amount = $actual_net_amount - $total_network_payout[0]['network_payout'];

        $net_revenue = $gross_ro_amount - $agency_commission_amount;
        $net_revenue = round(($net_revenue * SERVICE_TAX), 2);
        $net_contribution_amount_per = round(($net_contribution_amount / $net_revenue) * 100, 2);

        $total_scheduled_seconds = $this->mg_model->get_total_network_seconds_internal_ro($internal_ro_number);

        $report_data = array(
            'customer_ro_number' => $ro_detail[0]['cust_ro'],
            'internal_ro_number' => $internal_ro_number,
            'client_name' => $ro_detail[0]['client'],
            'agency_name' => $ro_detail[0]['agency'],
            'start_date' => $ro_detail[0]['camp_start_date'],
            'end_date' => $ro_detail[0]['camp_end_date'],
            'gross_ro_amount' => $gross_ro_amount,
            'agency_commission_amount' => $agency_commission_amount,
            'agency_rebate' => $agency_rebate,
            'other_expenses' => 0,
            'total_seconds_scheduled' => $total_scheduled_seconds[0]['total_scheduled_seconds'],
            'total_network_payout' => $total_network_payout[0]['network_payout'],
            'net_contribution_amount' => $net_contribution_amount,
            'net_contribution_amount_per' => $net_contribution_amount_per,
            'net_revenue' => $net_revenue
        );

        $this->mg_model->insert_into_external_ro_report_detail($report_data);
    }

    /**
     * For changing  ro amount after approval
     */
    public function force_change_ro_amount($order_id, $new_ro_amount, $new_agency_commision)
    {

        echo "Not Allowed";
        exit;
        $this->is_logged_in_tripur();

        $internal_ro_number = base64_decode($order_id);
        $invoice_amount = $new_ro_amount;

        //get ro details from am_external_ro
        $ro_details = $this->am_model->get_ro_details_for_internal_ro($internal_ro_number);
        $returned_value = $this->validation_for_ro_change($ro_details, $new_ro_amount, $new_agency_commision);

        if ($returned_value) {
            echo "Invalid Entry";
            exit;
        }

        $ro_amount = $ro_details[0]['gross'];
        $ro_id = $ro_details[0]['id'];

        $ro_amount_val = $this->am_model->get_ro_amount_data($internal_ro_number);
        if (!isset($new_agency_commision) && empty($new_agency_commision)) {
            $agency_comm_from_ext_ro = $ro_details[0]['agency_com'];
            $agency_commission_amount = $ro_amount_val[0]['agency_commission_amount'];
        } else {
            $agency_comm_from_ext_ro = $new_agency_commision;
            $agency_commission_amount = $new_agency_commision;
        }
        $agency_rebate = $ro_amount_val[0]['agency_rebate'];
        $marketing_promotion_amount = $ro_amount_val[0]['marketing_promotion_amount'];
        $field_activation_amount = $ro_amount_val[0]['field_activation_amount'];
        $sales_commissions_amount = $ro_amount_val[0]['sales_commissions_amount'];
        $creative_services_amount = $ro_amount_val[0]['creative_services_amount'];
        $other_expenses_amount = $ro_amount_val[0]['other_expenses_amount'];

        //proportionality calculation
        if ($ro_amount >= $invoice_amount) {
            $percentage = ($ro_amount - $invoice_amount) / $ro_amount;
            if (!isset($new_agency_commision) && empty($new_agency_commision)) {
                $agency_comm_from_ext_ro = $agency_comm_from_ext_ro - ($agency_comm_from_ext_ro * $percentage);
                $agency_commission_amount = $agency_commission_amount - ($agency_commission_amount * $percentage);
            }

            //$agency_rebate = $agency_rebate - ($agency_rebate*$percentage) ;
            $marketing_promotion_amount = $marketing_promotion_amount - ($marketing_promotion_amount * $percentage);
            $field_activation_amount = $field_activation_amount - ($field_activation_amount * $percentage);
            $sales_commissions_amount = $sales_commissions_amount - ($sales_commissions_amount * $percentage);
            $creative_services_amount = $creative_services_amount - ($creative_services_amount * $percentage);
            $other_expenses_amount = $other_expenses_amount - ($other_expenses_amount * $percentage);
        } else {
            $percentage = ($invoice_amount - $ro_amount) / $ro_amount;
            if (!isset($new_agency_commision) && empty($new_agency_commision)) {
                $agency_comm_from_ext_ro = $agency_comm_from_ext_ro + ($agency_comm_from_ext_ro * $percentage);
                $agency_commission_amount = $agency_commission_amount + ($agency_commission_amount * $percentage);
            }

            //$agency_rebate = $agency_rebate - ($agency_rebate*$percentage) ;
            $marketing_promotion_amount = $marketing_promotion_amount + ($marketing_promotion_amount * $percentage);
            $field_activation_amount = $field_activation_amount + ($field_activation_amount * $percentage);
            $sales_commissions_amount = $sales_commissions_amount + ($sales_commissions_amount * $percentage);
            $creative_services_amount = $creative_services_amount + ($creative_services_amount * $percentage);
            $other_expenses_amount = $other_expenses_amount + ($other_expenses_amount * $percentage);
        }

        $net_agency_com = $invoice_amount - $agency_comm_from_ext_ro;
        $ro_amount_data = array('gross' => $invoice_amount, 'agency_com' => $agency_commission_amount, 'net_agency_com' => $net_agency_com, 'previous_ro_amount' => $ro_amount);
        //update into am_external_ro
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
        // update into ro_order
        $this->am_model->update_ro_amount($ro_amnt_data, $internal_ro_number);

        // update into external_ro_report_detail
        $this->update_into_external_ro_report_detail($internal_ro_number);
        echo "successfully updated";
    }

    public function is_logged_in_tripur()
    {
        $logged_in_user = $this->session->userdata("logged_in_user");

        if ($logged_in_user[0]['user_email'] != 'tripur@surewaves.com') {
            echo "Invalid Login";
            exit;
        }
    }

    public function validation_for_ro_change($ro_details, $ro_amount, $agency_commision)
    {
        if (count($ro_details) == 0) {
            return TRUE;
        }
        if (!is_numeric($ro_amount)) {
            return TRUE;
        }
        if (!is_numeric($agency_commision)) {
            return TRUE;
        }
        return FALSE;
    }

    public function test_caching()
    {
        /*$this->load->library('memcached_library');

		// Lets try to get the key
		$results = $this->memcached_library->get('test');

		// If the key does not exist it could mean the key was never set or expired
		if (!$results)
		{

			// Lets store the results
			$this->memcached_library->add('test', 'Lokanath');

			// Output a basic msg
			echo "Alright! Stored some results from the Query... Refresh Your Browser";
		}
		else
		{
			// Output
			echo $results ;

			// Now let us delete the key for demonstration sake!
			//$this->memcached_library->delete('test');
		} */

        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        echo "1";
        $result = $this->cache->memcached->get('test_value');
        echo "2";
        if (!$result) {
            echo "if_here";
            echo 'Saving to the cache!<br />';
            $foo = 'lokanath';

            // Save into the cache for 5 minutes
            $this->cache->memcached->save('test_value', $foo, 300);
        } else {
            echo "here";
            echo $result;
        }

    }

    public function cache_state()
    {
        $this->load->library('memcached_library');

        echo $this->memcached_library->getversion();
        echo "<br/>";

        // We can use any of the following "reset, malloc, maps, cachedump, slabs, items, sizes"
        $p = $this->memcached_library->getstats("sizes");

        var_dump($p);
    }

    public function save_each_nw($internal_ro_number, $nw_id, $post_data)
    {
        $key = "edit_" . $internal_ro_number;

        $cache_data = $this->memcache->get($key);

        $data = array();
        if (count($cache_data) > 0) {
            if (array_key_exists($nw_id, $cache_data)) {
                //replace  the whole data of nw  key
            } else {
                $cache_data[$nw_id] = $post_data;
            }
        }


        //make new

    }

    //For testing purpose to fetch data

    public function save_to_cache()
    {
        $internal_ro_number = $_POST['hid_internal_ro'];
        $key = "edit_" . $internal_ro_number;

        $nw_id_values = array_values($_POST['hid_cust_id']);
        $nw_id = $nw_id_values[0];

        $cancel_channel = array();
        foreach ($_POST['cancel_channel'] as $chnls) {
            if (empty($chnls)) {
                continue;
            }
            array_push($cancel_channel, $chnls);
        }

        $cache_data = $this->memcache->get($key);
        /*
                    if(!array_key_exists('hid_edit', $cache_data)) {
                        $cache_data['hid_edit'] = $_POST['hid_edit'] ;
                    }
                    if(!array_key_exists('hid_id', $cache_data)) {
                        $cache_data['hid_id'] = $_POST['hid_id'] ;
                    } */
        if (!array_key_exists('internal_ro_number', $cache_data)) {
            $cache_data['internal_ro_number'] = $internal_ro_number;
        }
        /*if(is_array($cache_data)) {
                            $cache_data[$nw_id]['cancel_channel'] = $cancel_channel ;
                            $cache_data[$nw_id]['post_channel_avg_rate'] = $_POST['post_channel_avg_rate'] ;
                            $cache_data[$nw_id]['post_channel_amount'] = $_POST['post_channel_amount'] ;
                            $cache_data[$nw_id]['hid_total_seconds'] = $_POST['hid_total_seconds'] ;
                            $cache_data[$nw_id]['post_network_share'] = $_POST['post_network_share'][$nw_id] ;
                            $cache_data[$nw_id]['post_final_amount'] = $_POST['post_final_amount'][$nw_id] ;
                    }else {
                        $cache_data = array() ; */

        $cache_data[$nw_id]['cancel_channel'] = $cancel_channel;
        $cache_data[$nw_id]['post_channel_avg_rate'] = $_POST['post_channel_avg_rate'];
        $cache_data[$nw_id]['post_channel_amount'] = $_POST['post_channel_amount'];
        $cache_data[$nw_id]['hid_total_seconds'] = $_POST['hid_total_seconds'];
        $cache_data[$nw_id]['post_network_share'] = $_POST['post_network_share'][$nw_id];
        $cache_data[$nw_id]['post_final_amount'] = $_POST['post_final_amount'][$nw_id];
        //}
        $this->memcache->set($key, $cache_data, 10000);
        log_message('INFO', 'In ro_manager@save_to_cache | cached data set = ' . print_r($cache_data, true));
        log_message('INFO', 'In ro_manager@save_to_cache | cached data get after set  = ' . print_r($this->memcache->get($key), true));
    }

    public function final_save_to_cache($internal_ro_number, $edit = 0, $am_ro_id)
    {
        //internal ro number should be encrypted
        $key = "edit_" . $internal_ro_number;
        $cache_data = $this->memcache->get($key);
        log_message('INFO', 'In ro_manager@final_save_to_cache | cached data get  = ' . print_r($cache_data, true));

        //make new key:final_internalRo_timestamp
        $timestamp = strtotime("now");
        $new_key = "final_" . $internal_ro_number . "_" . $timestamp;

        //store in job queue
        $logged_in = $this->session->userdata("logged_in_user");
        $user_id = $logged_in[0]['user_id'];

        $user_data = array(
            'job_code' => 'editNw',
            'done' => 0,
            'params' => $new_key,
            'customer_id' => $user_id
        );
        $this->am_model->insert_into_job_queue($user_data);

        //store into cache
        $this->memcache->set($new_key, $cache_data, 50000);
        log_message('INFO', 'In ro_manager@final_save_to_cache | new key cached data set = ' . print_r($cache_data, true));
        log_message('INFO', 'In ro_manager@final_save_to_cache | new key cached data get after set  = ' . print_r($this->memcache->get($new_key), true));

        //delete from cache
        $this->memcache->delete($key);
        log_message('INFO', 'In ro_manager@final_save_to_cache | cached data after deleting old key  = ' . print_r($this->memcache->get($key), true));
        //$this->approve($internal_ro_number,$edit,$am_ro_id) ;
        redirect("ro_manager/approve/" . $internal_ro_number . "/" . $edit . "/" . $am_ro_id);
    }

    public function remove_from_cancel_channel()
    {
        $order_id = $_POST['order_id'];
        $channel_id = $_POST['channel_id'];

        $user_id = $this->get_user_id();
        $key = 'add_cancel_channel_' . $user_id . "_" . $order_id;

        $cancel_channel_arr = $this->memcache->get($key);
        //echo "before";print_r($cancel_channel_arr);
        unset($cancel_channel_arr[$channel_id]);
        //echo "after";print_r($cancel_channel_arr);
        //store into cache
        $this->memcache->set($key, $cancel_channel_arr, 50000);


    }

    public function add_to_cancel_channel()
    {
        $order_id = $_POST['order_id'];
        $channel_id = $_POST['channel_id'];

        $user_id = $this->get_user_id();
        $key = 'add_cancel_channel_' . $user_id . "_" . $order_id;
        $cancel_channel_arr = $this->memcache->get($key);
        if (count($cancel_channel_arr) > 0) {
            $cancel_channel_arr[$channel_id] = $channel_id;
        } else {
            $cancel_channel_arr = array();
            $cancel_channel_arr[$channel_id] = $channel_id;
        }
        //store into cache
        $this->memcache->set($key, $cancel_channel_arr, 50000);
    }

    public function remove_from_cancel_priority()
    {
        $order_id = $_POST['order_id'];
        $market_id = $_POST['market_id'];
        $priority = $_POST['priority'];

        $user_id = $this->get_user_id();
        $key = 'marketId_priority_' . $user_id . "_" . $order_id;
        $marketId_priority_array = $this->memcache->get($key);
        $marketId_priority_array[$market_id . "_" . $priority] = '1';


        //store into cache
        $this->memcache->set($key, $marketId_priority_array, 50000);


    }

    public function add_to_cancel_priority()
    {
        $order_id = $_POST['order_id'];
        $market_id = $_POST['market_id'];
        $priority = $_POST['priority'];

        $user_id = $this->get_user_id();
        $key = 'marketId_priority_' . $user_id . "_" . $order_id;
        $marketId_priority_array = $this->memcache->get($key);
        $marketId_priority_array[$market_id . "_" . $priority] = '0';


        //store into cache
        $this->memcache->set($key, $marketId_priority_array, 50000);
    }

    public function get_channels_for_market()
    {
        $market_id = $this->input->post("market_id");
        $priority = $this->input->post("priority");
        $am_ro_id = $this->input->post("am_ro_id");

        $market_priority_array = $this->memcache->get('edit_market_priority_' . $am_ro_id);
        $priority_channels = array();
        foreach ($market_priority_array[$market_id]['channel_priority'][$priority] as $key => $value) {
            array_push($priority_channels, $key);
        }
        echo json_encode($priority_channels);

    }

    public function get_cache_data($internal_ro_number)
    {
        //$key = "edit_".$internal_ro_number ;
        $key = $internal_ro_number;
        $cache_data = $this->memcache->get($key);
        echo "all_cache_data:" . print_r($cache_data, true);

        //$memcache->delete($key) ;
        //$cache_data = $memcache->get($key);
        //echo "After Delete :-".print_r($cache_data,true);

    }

    public function revert_all_changes($order_id, $edit, $am_ro_id)
    {
        //$internal_ro_number = base64_decode($order_id) ;
        $user_id = $this->get_user_id();

        $key_loading = $am_ro_id;
        $key_posted = "loadSave_" . $user_id . "_" . $order_id;
        $key_channel = 'add_cancel_channel_' . $user_id . "_" . $order_id;
        $key_priority = 'marketId_priority_' . $user_id . "_" . $order_id;

        $this->delete_from_cache($key_loading);
        $this->delete_from_cache($key_channel);
        $this->delete_from_cache($key_posted);
        $this->delete_from_cache($key_priority);

        $order_id_ser = serialize($order_id);
        $confirmation_data = $this->ro_model->get_data_for_confirmation($order_id_ser);
        $confirmation_id = $confirmation_data[0]['confirmation_id'];

        $this->ro_model->delete_from_confirmation($order_id_ser);
        $this->ro_model->delete_from_confirmation_customer($confirmation_id, $user_id);

        //$this->approve($order_id, $edit, $am_ro_id) ;
        redirect("ro_manager/approve/" . $order_id . "/" . $edit . "/" . $am_ro_id);
    }

    public function cancel_campaign()
    {
        $campaign_id_val = $this->input->post('campaigns_to_cancel');

        $campaign_ids = implode(",", $campaign_id_val);
        $this->ro_model->cancel_campaign_b4_approval($campaign_ids);
    }

    public function search_campaign_schedule_content()
    {
        //$search_data = $this->input->post();
        //print_r($search_data);
        $order_id = $this->input->post('order_id');
        $edit = $this->input->post('edit');
        $am_ro_id = $this->input->post('am_ro_id');

        $search_by = $this->input->post('search_by');
        $search_value = $this->input->post('search_str');

        $search_value_encode = rtrim(base64_encode($search_value), '=');
        redirect("/ro_manager/campaigns_schedule/" . $order_id . "/" . $edit . "/" . $am_ro_id . "/" . $search_by . "/" . $search_value_encode);

    }

    public function cancel_market_reasons($am_ro_id)
    {
        $this->is_logged_in();

        $cancel_messages = $this->ro_model->get_cancel_market_messages($am_ro_id);
        $model = array();
        $model['messages'] = $cancel_messages;
        $this->load->view('ro_manager/cancel_market_reasons', $model);
    }
    //update pdf_generation_status=0 in  ro_approved_network for given internalRo and customerID
    //Find Latest Subject(internalro/___/customername) for todays date in ro_mail_data,if available remove that work


    //request for campaign performance report on mail : Nitish 23 feb 2015

    public function campaign_performance_report()
    {
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $this->load->model('user_model');
        //$model['customer_detail'] = $this->user_model->get_all_customer_detail();

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        if ($logged_in[0]['profile_id'] == 7) {
            $this->load->view('ro_manager/channel_performance_report', $model);
        } else {
            $this->load->view('ro_manager/campaign_performance_report', $model);
        }
    }

    public function get_campaign_performance_report()
    {
        $start_d = $this->input->post('month');
        //October 2014
        //2014-11-01----2014-11-31
        $start_d = date('Y-m-d', strtotime($start_d));
        $end_d = date('Y-m-t', strtotime($start_d));

        $list = $this->ro_model->get_campaign_performance_report($start_d, $end_d);
        $this->output->set_header('Content-Type: application/json');
        $this->output->set_output($list);
    }

    public function download_campaign_performance_csv($from_date)
    {

        $from_date = urldecode($from_date);

        $start_d = date('Y-m-d', strtotime($from_date));
        $end_d = date('Y-m-t', strtotime($from_date));
        $channel_performance = $this->ro_model->download_channel_performance_report_csv($start_d, $end_d, 0);

        $this->load->helper('csv');
        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=campaign_performance_report.csv');

        $csv_header = "MSO,Channel Name,Network Ro Number,Billing Name,Internal Ro Number,Start Date,End Date,Scheduled Spot Seconds,Played Spot Seconds,Scheduled Banner Seconds,played Banner Seconds,Spot Rate,Banner Rate,Total Payable";
        $csv_data = "";
        echo $csv_header;

        $channel_performance_count = count($channel_performance);

        for ($i = 0; $i < $channel_performance_count; $i++) {

            $csv_data .= "\n";
            //$total_payable = '';

            $total_payable = ($channel_performance[$i]['playedSpotSeconds'] * $channel_performance[$i]['spotRate'] / 10) +
                ($channel_performance[$i]['playedBannerSeconds'] * $channel_performance[$i]['bannerRate'] / 10);

            if ($channel_performance[$i]['scheduledSpotSeconds'] == NULL) {
                $channel_performance[$i]['scheduledSpotSeconds'] = 0;
            }
            if ($channel_performance[$i]['playedSpotSeconds'] == NULL) {
                $channel_performance[$i]['playedSpotSeconds'] = 0;
            }
            if ($channel_performance[$i]['scheduledBannerSeconds'] == NULL) {
                $channel_performance[$i]['scheduledBannerSeconds'] = 0;
            }
            if ($channel_performance[$i]['playedBannerSeconds'] == NULL) {
                $channel_performance[$i]['playedBannerSeconds'] = 0;
            }

            $csv_data .= $channel_performance[$i]['mso'];
            $csv_data .= "," . $channel_performance[$i]['channelName'];
            $csv_data .= "," . $channel_performance[$i]['networkRoNumber'];
            $csv_data .= "," . $channel_performance[$i]['billingName'];
            $csv_data .= "," . $channel_performance[$i]['internalRoNumber'];
            $csv_data .= "," . date('Y-m-d', strtotime($channel_performance[$i]['startDT']));
            $csv_data .= "," . date('Y-m-d', strtotime($channel_performance[$i]['endDT']));
            $csv_data .= "," . $channel_performance[$i]['scheduledSpotSeconds'];
            $csv_data .= "," . $channel_performance[$i]['playedSpotSeconds'];
            $csv_data .= "," . $channel_performance[$i]['scheduledBannerSeconds'];
            $csv_data .= "," . $channel_performance[$i]['playedBannerSeconds'];
            $csv_data .= "," . $channel_performance[$i]['spotRate'];
            $csv_data .= "," . $channel_performance[$i]['bannerRate'];
            $csv_data .= "," . $total_payable;
            echo $csv_data;
            $csv_data = "";
        }
        //array_to_csv($csv_array, 'campaign_performance_report.csv');
        //echo $csv_header.$csv_data;
    }

    public function request_campaign_performance_csv()
    {

        $from_date = $this->input->post('month');
        $mail_ids = $this->input->post('mail_ids');

        $from_date = urldecode($from_date);

        $mail_ids = $mail_ids;
        $start_d = date('Y-m-d', strtotime($from_date));
        $end_d = date('Y-m-t', strtotime($from_date));
        $timestamp = strtotime(date("m/d/Y h:i:s a", time()));
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        //Insert Into Job Queue For mail request
        $job_queue_user_data = array(
            'job_code' => 'processRequestedReports',
            'done' => '0',
            'params' => 'campaignPerformanceReport' . "#" . $timestamp . "#" . $start_d . "#" . $end_d . "#" . $mail_ids . "#" . $is_test_user,
            'customer_id' => 0,
        );
        $this->am_model->insert_into_job_queue($job_queue_user_data);
        redirect("ro_manager/campaign_performance_report");
    }

    public function regeneratePDF()
    {
        $internalRoCustomerId = $this->input->post("internalRoAndCustomerId");
        $rawData = explode("#", $internalRoCustomerId);
	//echo $internalRoCustomerId;exit;
	//echo "<pre>";print_r($this->input->post());exit;
        $internalRoNumber = $rawData[0];
        $customerId = $rawData[1];
        $customerName = $rawData[2];

        $subject = $internalRoNumber . "/___/" . $customerName;
        $date = date('Y-m-d');

        $resultObject = $this->mg_model->getLatestInsertedSubjectRowId($subject, $date);
        if ($resultObject->num_rows() > 0) {

            foreach ($resultObject->result() as $row) {

                $rowId = $row->id;

                $this->db->where('id', $rowId);
                $this->db->delete('ro_mail_data');
            }

        }

        $setGeneration = array('pdf_generation_status' => 0);

        $this->mg_model->updatePDFProcessingStatus($internalRoNumber, $customerId, $setGeneration);

        echo "TRUE";
    }

    public function mail_network_ro_report()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $userId = $logged_in[0]['user_id'];
        $test_user = $logged_in[0]['is_test_user'];
        $startDate = $this->input->post("from");
        $endDate = $this->input->post("to");
        $customerRo = $this->input->post("ro");
        $mailIds = trim($this->input->post("maild_ids"));

        $params = 'networkRoReportMonthWise' . "#" . $customerRo . "#" . $startDate . "#" . $endDate . "#" . $test_user . "#" . $mailIds;
        $whereData = array('job_code' => 'processRequestedReports', 'params' => $params, 'customer_id' => $userId);
        $dataExist = $this->mg_model->get_from_job_queue($whereData);

        $job_queue_data = array(
            'job_code' => 'processRequestedReports',
            'done' => 0,
            'params' => $params,
            'customer_id' => $userId
        );
        if (count($dataExist) > 0) {
            $this->mg_model->update_job_queue($whereData, array('done' => 0));
        } else {
            $this->mg_model->insertIntoJobQueue($job_queue_data);
        }
    }

    public function get_priority_total_nw_payout($marketPriorityArray)
    {
        $total_nw_payout = 0;
        foreach ($marketPriorityArray as $value) {
            foreach ($value['channel_priority'] as $priority) {
                foreach ($priority as $channel_detail) {
                    $channel_spot_amount = $channel_detail['channel_spot_amount'];
                    $channel_banner_amount = $channel_detail['channel_banner_amount'];
                    $customer_share = $channel_detail['customer_share'];
                    $total_nw_payout += ($channel_spot_amount + $channel_banner_amount) * ($customer_share / 100);
                }
            }
        }

        return $total_nw_payout;
    }

    public function set_market_rates()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $model['all_markets'] = $this->mg_model->getMarketDataForMarketName();
        $this->load->view('ro_manager/set_market_rates', $model);
    }

    public function get_market_details()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $market_id = $this->input->post('market_id');
        $market_details = $this->am_model->get_market_details($market_id);
        echo json_encode($market_details);
    }

    public function postMarketRate()
    {
        $data = array(
            'spot_rate' => $this->input->post('spot_rate'),
            'banner_rate' => $this->input->post('banner_rate')
        );
        $whereData = array('id' => $this->input->post('market_id'));
        $this->mg_model->updateMarketRate($data, $whereData);
        redirect("ro_manager/set_market_rates");
    }

    public function test_email_send()
    {
        test_send_mail();
    }

    public function test_method($inputTxt)
    {
        //$userData = $this->user_model->userEmailForRoCreation($user_id);
        //echo $userData;
        $internalRoNumber = base64_decode($inputTxt);
        $roValues = $this->am_model->get_all_channels_scheduled_v1($internalRoNumber);
        echo print_r($roValues, true);
    }

    public function invoice_generate()
    {
        $this->is_logged_in();
        $this->find_data_for_cache();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $this->load->model('user_model');
        $model['customer_detail'] = $this->user_model->get_all_customer_detail();

        $menu = $this->getMenu($logged_in[0]['profile_id']);
        $model['menu'] = $menu;
        $this->load->view('ro_manager/invoice_generator', $model);
    }

    public function request_invoice_generator()
    {
        $month_year = explode(" ", trim($this->input->post('month')));
        $data = $this->ro_model->getDataForInvoiceGeneration($month_year[0], $month_year[1]);
        $filename = 'invoice_report' . date("Y-m-d") . '.csv';

        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-disposition: attachment; filename=' . $filename);

        $header = 'Customer RO Number,Internal RO Number,Agency,Client,Financial Year,Financial Month,Price,Expenses';
        $csv_content = '';
        echo $header;
        foreach ($data as $row) {
            $expenses = round((($row['price'] / $row['gross_ro_amount']) * $row['agency_commision']), 2);
            $csv_content = PHP_EOL;
            $csv_content .= $row['customer_ro_number'];
            $csv_content .= "," . $row['internal_ro_number'];
            $csv_content .= "," . $row['agency'];
            $csv_content .= "," . $row['client'];
            $csv_content .= "," . $row['financial_year'];
            $csv_content .= "," . $row['month'];
            $csv_content .= "," . $row['price'];
            $csv_content .= "," . $expenses;
            echo $csv_content;
        }
        exit;
    }

    public function RoPdfTest()
    {
        $model = array();
        $model['internal_ro_number'] = 'SW/internalro';
        $model['reports'] = '';
        $model['cc_emails_list'] = 'nothing';
        $model['channels'] = '';
        $model['days'] = '20';
        $model['no_of_months'] = 2;
        $model['content_download_link'] = 'www.sdsdssds.com';
        $model['show_link'] = 1;
        $model['nw_ro_number'] = 'sw/internalro/nw';
        $model['complete_cancellation'] = 1;
        $this->load->view('ro_manager/PDF_test', $model);
    }

    public function getMenu($profileId)
    {
        log_message('DEBUG', 'In getMenu | Trying to fetch from session for profile id = ' . $profileId);
        $menu = $this->session->userdata('menu');
        if ($menu == NULL) {
            log_message('DEBUG', 'In getMenu | Menu is not set in session. Fetching menu from database for profile id = ' . $profileId);
            $menu = (new MenuFeature())->getHeaderMenu($profileId);
            log_message('DEBUG', 'In getMenu | Fetching menu from database successful for profile id = ' . $profileId);
        }
        log_message('DEBUG', 'In getMenu | Menu successfully fetched for profile id = ' . $profileId);
        return $menu;
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

    function buildMarketDataTableForSubmitMail($marketDataArray)
    {

        $marketTable = '<table width="100%" class="order-table" style="width: 100%;margin: 25px auto 17px;border: 1px solid #D5D5D5;color: #304856;">
                        <thead style=" background: #E0E0E0;">
                            <tr align="center" style="height: 32px;border-bottom: 1px solid #d5d5d5;color: #505050;font-size: 14px;" width="100%">
                                <th>Markets/Clusters</th>
                                <th>Spot FCT </th>
                                <th>Aston FCT</th>
                            </tr>
                        </thead>';

        foreach ($marketDataArray as $key => $value) {

            $marketTable .= ' <tbody style="font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px;background: rgba(255, 255, 255, 0.69);">
                                            <tr align="center" style="border-top: 1px dotted #ddd;border-bottom: 1px dotted #CBC9C9;height: 35px;color: #000;">
                                                <td width="600">' . $value['market'] . '</td>
                                                <td width="300" >' . $value['spot_fct'] . '</td>
                                                <td width="300" >' . $value['banner_fct'] . '</td>
                                            </tr>
                              </tbody> ';

        }
        $marketTable .= ' </table>';

        return $marketTable;
    }
}

/* End of file ro_manager.php */
/* Location: ./application/controllers/ro_manager.php */
