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

class Strategy_manager extends CI_Controller
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
        $this->load->model("common_model");
        $this->load->model("strategy_model");

        //Loading Libraries
        $this->load->library('session');
        $this->load->library('curl');
        $this->load->library('form_validation');
        $this->load->library('pagination');

        //Loading Helpers
        $this->load->helper("app_validation_helper");
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->helper('url');

        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    public function user_details()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $profile_details = $this->user_model->get_profile_details($profile_id);

        $menu = $this->menu_model->get_header_menu();
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_details'] = $profile_details[0];
        $model['menu'] = $menu;
        $this->load->view('strategy_manager/user_details', $model);
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

    public function home()
    {
        $this->is_logged_in();
        $menu = $this->menu_model->get_header_menu();
        $logged_in = $this->session->userdata("logged_in_user");
        $all_users = $this->strategy_model->getAllUserData();

        $userFinalData = $this->get_achieved_targets($all_users);
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $model['all_users'] = $userFinalData;
        $this->load->view('strategy_manager/home', $model);
    }

    public function get_achieved_targets($all_users)
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $user_data = array();
        foreach ($all_users as $user) {
            $data = array();
            $data['user_id'] = $user['user_id'];
            $data['user_name'] = $user['user_name'];
            $data['reporting_manager_id'] = $user['reporting_manager_id'];
            $data['designation'] = $user['designation'];
            $data['region_id'] = $user['region_id'];
            $data['region_name'] = $user['region_name'];
            $data['profile_image'] = $user['profile_image'];
            $data['reporting_to'] = $user['reporting_to'];

            $monthly_target = $this->am_model->getMonthlyTarget($user['user_id']);
            $data['monthly_target'] = $monthly_target * 100000;

            $childUserIds = array();
            array_push($childUserIds, $user['user_id']);
            $childId = $this->common_model->getChildUserIds($user['user_id'], $childUserIds);
            $commaSeparatedBelowUserId = $this->common_model->getCommaSepartedValues($childId);

            $approvedRos = $this->am_model->getApprovedRo($commaSeparatedBelowUserId, $start_date, $end_date);
            $data['monthly_achieved'] = $this->am_model->getAchievedROAmount($approvedRos);
            array_push($user_data, $data);
        }
        return $user_data;
    }

    public function get_ros_submitted($user_id)
    {
        $this->is_logged_in();

        $childUserIds = array();
        array_push($childUserIds, $user_id);
        $childId = $this->common_model->getChildUserIds($user_id, $childUserIds);
        $commaSeparatedBelowUserId = $this->common_model->getCommaSepartedValues($childId);

        $all_ros_for_user = $this->strategy_model->get_ros_submitted_by_user($commaSeparatedBelowUserId);

        $model = array();
        $model['ro_list'] = $all_ros_for_user;
        $this->load->view('strategy_manager/ro_details_for_user', $model);
    }

    public function download_ro_data()
    {
        $this->is_logged_in();
        $menu = $this->menu_model->get_header_menu();
        $logged_in = $this->session->userdata("logged_in_user");
        $all_users = $this->strategy_model->getAllUserData();
        //Get All Regions
        $regions = $this->mg_model->getAllRegions();
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $model['all_users'] = $all_users;
        $model['regions'] = $regions;
        $this->load->view('strategy_manager/download', $model);
    }

    public function get_users_for_region()
    {
        $this->is_logged_in();
        $region_id = $this->input->post('region_id');
        $all_users_for_region = $this->strategy_model->get_users_for_region($region_id);
        echo json_encode($all_users_for_region);
    }

    public function download_ro_data_for_user($start_month, $end_month, $region_id, $user_id)
    {
        $this->is_logged_in();
        /*$start_month = $this->input->post('start_month');
        $end_month = $this->input->post('end_month');
        $region_id = $this->input->post('region_id');
        $user_id = $this->input->post('user_id');*/

        $region_id = urldecode($region_id);
        $user_id = urldecode($user_id);
        $start_month = urldecode($start_month);
        $end_month = urldecode($end_month);

        $start_date = date('Y-m-01', strtotime($start_month));
        $end_date = date('Y-m-t', strtotime($end_month));

        $UserData = $this->strategy_model->getUserRoData($user_id, $region_id, $start_date, $end_date);

        $yearWiseMonths = $this->common_model->getMonthNameAcrossFinancialYear($start_month, $end_month);


        //$this->load->helper('csv');
        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: max-age=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        //header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=user_summary.xls');

        $csv_header = "User,Region,Reporting Manager,Target,Achieved,Customer Ro Number,Internal Ro Number,Client,Markets,Ro Date, Ro Start Date, Ro End Date,Ro Amount";
        $csv_header = str_replace(",", "\t", $csv_header);
        $csv_data = "";
        echo $csv_header;

        $UserData_count = count($UserData);

        for ($i = 0; $i < $UserData_count; $i++) {
            $monthly_target = 0;
            foreach ($yearWiseMonths as $year => $months) {

                $monthValue = explode(",", $months);
                $monthList = implode("','", $monthValue);

                $monthly_target += $this->am_model->getMonthlyTargetSumForDownload($UserData[$i]['user_id'], $monthList, $year);
            }

            $childUserIds = array();
            array_push($childUserIds, $UserData[$i]['user_id']);
            $childId = $this->common_model->getChildUserIds($UserData[$i]['user_id'], $childUserIds);
            $commaSeparatedBelowUserId = $this->common_model->getCommaSepartedValues($childId);

            $approvedRos = $this->am_model->getApprovedRo($commaSeparatedBelowUserId, $start_date, $end_date);
            $monthly_achieved = $this->am_model->getAchievedROAmount($approvedRos);

            $csv_data .= "\n";

            $csv_data .= $UserData[$i]['user_name'];
            $csv_data .= "\t" . ucfirst($UserData[$i]['region_name']);
            $csv_data .= "\t" . $UserData[$i]['reporting_manager'];
            $csv_data .= "\t" . $monthly_target * 100000;
            $csv_data .= "\t" . $monthly_achieved;
            $csv_data .= "\t" . $UserData[$i]['cust_ro'];
            $csv_data .= "\t" . $UserData[$i]['internal_ro'];
            $csv_data .= "\t" . $UserData[$i]['client'];
            $csv_data .= "\t" . $UserData[$i]['market'];
            $csv_data .= "\t" . date('Y-m-d', strtotime($UserData[$i]['ro_date']));
            $csv_data .= "\t" . date('Y-m-d', strtotime($UserData[$i]['camp_start_date']));
            $csv_data .= "\t" . date('Y-m-d', strtotime($UserData[$i]['camp_end_date']));
            $csv_data .= "\t" . $UserData[$i]['gross'];
            echo $csv_data;
            $csv_data = "";
        }

    }


}
