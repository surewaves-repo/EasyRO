<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{

    private $profilesArray = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('mg_model');
        $this->load->library('session');
        $this->load->config('form_validation');
        $this->load->library('form_validation');
        $this->load->helper('generic_helper');
        $this->load->helper("app_validation_helper");
        $this->load->helper('url');
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    public function index()
    {
        $logged_in = $this->session->userdata("logged_in_admin");
        if (isset($logged_in[0]['admin_id'])) {
            redirect("/admin/home");
        } else {
            $this->load->view('admin/index');
        }
    }

    public function home($start_index = 0)
    {
        $this->is_logged_in();

        $this->load->helper("generic");
        $users = $this->user_model->get_users();

        $model = array();
        $model['page_links'] = create_page_links(base_url() . "admin/home", ITEM_PER_PAGE_USERS, count($users));
        $filtered_users = array_splice($users, $start_index, ITEM_PER_PAGE_USERS);
        $model['users'] = $filtered_users;
        $model['start_index'] = $start_index;

        $this->load->view("admin/home", $model);

    }

    function is_logged_in($javascript_redirect = 0)
    {
        $logged_in_user = $this->session->userdata("logged_in_admin");
        $logged_in_user = $logged_in_user[0];
        if (!isset($logged_in_user) || empty($logged_in_user)) {
            if ($javascript_redirect == '1') {
                echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/admin";</script>';
            } else {
                redirect("/admin");
            }
        }
    }

    public function post_login()
    {

        $email = $this->input->post('email');
        $passwd = $this->input->post('passwd');
        $error_in_validation = $this->set_form_validation($this->config->item('login_form'));

        if ($error_in_validation) {
            $this->show_form_validation_error('admin/index');
        } else {

            $ret = $this->user_model->admin_login($email, $passwd);
            if ($ret == NULL) {

                $model = array();
                $model['error_msg'] = $this->config->item('login_form_error_code_1');;
                $this->load->view('admin/index', $model);
            } else {
                redirect("/admin/home");
            }

        }

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

    public function duplicate_user_email_check($str)
    {
        return duplicate_user_email_check($str);
    }

    public function edit_duplicate_user_email_check($str)
    {
        return edit_duplicate_user_email_check($str);
    }

    public function add_user()
    {

        $model = array();
        $returnArray = array();

        $profileArray = $this->user_model->get_profiles();

        $model['profiles'] = $this->filterProfile($profileArray);
        $model['regionsJson'] = json_encode($this->mg_model->getAllRegions());
        $model['reportingManagersJson'] = json_encode($this->user_model->getAllReportingManagers());

        $this->load->view('admin/add_user', $model);
    }

    function filterProfile($profileArray)
    {

        $notShowingProfileId = json_decode(NOT_SHOWING_PROFILE_ID);
        $returnArray = array();

        foreach ($profileArray as $profile) {

            if (!in_array($profile['profile_id'], $notShowingProfileId)) {

                array_push($returnArray, array("profile_id" => $profile['profile_id'], "profile_name" => $profile['profile_name']));
            }
        }
        return $returnArray;
    }

    public function post_add_user()
    {
        $this->is_logged_in();
        $error_in_validation = $this->set_form_validation($this->config->item('add_user_form'));


        if ($error_in_validation) {
            $model = array();

            $profileArray = $this->user_model->get_profiles();

            $model['profiles'] = $this->filterProfile($profileArray);
            $model['regionsJson'] = json_encode($this->mg_model->getAllRegions());
            $model['reportingManagersJson'] = json_encode($this->user_model->getAllReportingManagers());

            $this->show_form_validation_error('admin/add_user', $model);
        } else {
            $time = date("Y-m-d H:i:s");
            /*Nitish: commented to make default password as surew@ves as requested by Nandini
             * $name = explode(" ",$this->input->post('user_name'));
            $password = $name[0];*/
            $password = 'surew@ves';
            $userdata = array(
                'user_phone' => $this->input->post('user_phone'),
                'user_name' => $this->input->post('user_name'),
                'user_email' => $this->input->post("user_email"),
                'profile_id' => $this->input->post('profile_id'),
                'user_password' => md5($password),
                'plain_text_password' => $password,
                'agency_id' => '2',
                'active' => '1',
                'creation_datetime' => $time,
                'is_test_user' => $this->input->post('user_type'),
                'reporting_manager_id' => $this->input->post('reporting_manager')
            );
            $profile_id = $userdata['profile_id'];
            $ret = $this->user_model->add_user($userdata);

            $profile_details = $this->user_model->get_profile_details($profile_id);
            $profile_name = $profile_details[0]['profile_name'];
            $login_url = 'http://' . $_SERVER['SERVER_NAME'] . ROOT_FOLDER;
            $to = $userdata['user_email'];
            $text = "login_url";
            $subject = array('USER_NAME' => $userdata['user_name']);
            $message = array(
                'USER_NAME' => $userdata['user_name'],
                'USER_EMAIL' => $userdata['user_email'],
                'PROFILE_NAME' => $profile_name,
                'PASSWORD' => $userdata['plain_text_password'],
                'LOGIN_URL' => $login_url,
            );
            $file = '';
            $url = '';
            $cc = '';

            /* Associating user regions */

            $regions = $this->input->post("regions");

            if (trim($regions) != 0) {

                $insertedRowId = $this->user_model->getInsertedRowId($userdata['user_email'], $userdata['profile_id'], $userdata['creation_datetime']);;
                $regions = explode(",", $regions);

                foreach ($regions as $regionId) {

                    $this->user_model->associateUserWithRegion($insertedRowId, $regionId);
                }

            }

            mail_send($to, $text, $subject, $message, $file, $cc, $url);
            echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/admin/home";</script>';
        }
    }

    public function delete_user($user_id)
    {
        $activate = 0;

        $this->user_model->manageUserActivationDeactivation($user_id, $activate);
        echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/admin/home";</script>';
    }

    //For Validation from UI

    public function edit_user($user_id)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $user_details = $this->user_model->edit_user($user_id);
        $profileArray = $this->user_model->get_profiles();

        $model['user_details'] = $user_details[0];
        $model['profiles'] = $this->filterProfile($profileArray);

        $model['regionsJson'] = json_encode($this->mg_model->getAllRegions());
        $model['reportingManagersJson'] = json_encode($this->user_model->getAllReportingManagers());
        $model['selectedReportingManager'] = $user_details[0]['reporting_manager_id'];

        $this->load->view('admin/edit_user', $model);
    }

    public function post_edit_user()
    {
        $this->is_logged_in();
        $error_in_validation = $this->set_form_validation($this->config->item('edit_user_form'));

        if ($error_in_validation) {
            $user_id = $this->input->post('user_id');
            $model = array();
            $user_details = $this->user_model->edit_user($user_id);
            $model['user_details'] = $user_details[0];
            $profileArray = $this->user_model->get_profiles();

            $model['user_details'] = $user_details[0];
            $model['profiles'] = $this->filterProfile($profileArray);

            $model['regionsJson'] = json_encode($this->mg_model->getAllRegions());
            $model['reportingManagersJson'] = json_encode($this->user_model->getAllReportingManagers());
            $model['selectedReportingManager'] = $user_details[0]['reporting_manager_id'];
            $this->show_form_validation_error('admin/edit_user', $model);
        } else {
            $time = date("Y-m-d H:i:s");
            $user_id = $this->input->post('user_id');
            $user_data = array(
                'user_phone' => $this->input->post('user_phone'),
                'user_name' => $this->input->post('user_name'),
                'profile_id' => $this->input->post('profile_id'),
                'agency_id' => '2',
                'active' => '1',
                'creation_datetime' => $time,
                //'is_test_user' => $this->input->post('user_type'),
                'reporting_manager_id' => $this->input->post('reporting_manager')
            );
            $ret = $this->user_model->update_user($user_data, $user_id);
            $details = $this->user_model->edit_user($user_id);
            $password = $details[0]['plain_text_password'];
            $login_url = 'http://' . $_SERVER['SERVER_NAME'] . ROOT_FOLDER;
            $to = $details[0]['user_email'];
            $text = "user_update";
            $subject = array('USER_NAME' => $user_data['user_name']);
            $message = array(
                'USER_NAME' => $user_data['user_name'],
                'PASSWORD' => $password,
                'USER_EMAIL' => $details[0]['user_email'],
                'LOGIN_URL' => $login_url,
            );
            $file = '';
            $url = '';
            $cc = '';

            /* Associating user regions */

            $regions = $this->input->post("regions");
            $this->user_model->deleteFromUserRegions($user_id);

            if (trim($regions) != 0) {

                $regions = explode(",", $regions);

                foreach ($regions as $regionId) {

                    $this->user_model->associateUserWithRegion($user_id, $regionId);
                }

            }

            mail_send($to, $text, $subject, $message, $file, $cc, $url);
            echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/admin/home";</script>';
        }
    }

    /* Helper function */

    public function logout()
    {
        $this->user_model->logout();
        redirect("/admin/index");
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

    function enableUser()
    {

        $activate = 1;
        $userId = $this->input->post("userId");

        $this->user_model->manageUserActivationDeactivation($userId, $activate);
        echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/admin/home";</script>';
    }

    public function getRegionReportingManger($userId, $profileId)
    {

        $profileHierarchyArray = $this->user_model->getProfileActualHierarchy();
        $userRootProfiles = $this->getProfileHierarchy($profileId, $profileHierarchyArray, 0);
        $profiles = implode("','", $userRootProfiles);

        $model = array();
        $model['regions'] = $this->mg_model->getAllRegions();
        $model['reportingMangers'] = $this->user_model->getReportingManagersUsingProfileId($profiles);
        $model['userId'] = $userId;
        $model['profileId'] = $profileId;

        $this->load->view('admin/assign_reporting_region', $model);
    }

    /**
     * @param $profileId
     * @param array $dataArray
     * @param $iteration
     * @return $parentProfileIds Array0./8
     */
    public function getProfileHierarchy($profileId, $dataArray = array(), $iteration)
    {

        $newProfileId = 0;
        $totalIteration = $iteration;
        $returnValue = null;
        $dataArrayLength = count($dataArray);

        if ($profileId == 0 || $dataArrayLength == $totalIteration) {

            return $this->profilesArray;
        }

        foreach ($dataArray as $value) {

            if ($value["profile_id"] == $profileId) {

                $newProfileId = $value["parent_id"];
                array_push($this->profilesArray, $value["parent_id"]);
                break;

            }
        }

        $totalIteration++;
        $returnValue = $this->getProfileHierarchy($newProfileId, $dataArray, $totalIteration);

        return $returnValue;

    }

    public function assignRegionReportingManager()
    {

        $regionId = $this->input->post('region');
        $reportingManager = $this->input->post('reporting_manager');
        $userId = $this->input->post('userId');

        $this->user_model->associateUserWithRegion($userId, $regionId);
        $this->user_model->updateReportingManagerForBelowUser($userId, $reportingManager);
        echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/admin/home";</script>';
    }


} /* end of Admin Class */
?>
