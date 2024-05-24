<?php
/**
 * Created by PhpStorm.
 * Author: Yash | Ravishankar
 * Date: August, 2019
 */

use application\feature_dal\MenuFeature;
use application\feature_dal\UserLoginFeature;

include_once APPPATH . 'feature_dal/menu_feature.php';
include_once APPPATH . 'feature_dal/user_login_feature.php';

class Login_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

        $this->load->library('session');
        $this->load->library('curl');
        $this->load->library('form_validation');

        $this->load->config('form_validation');

        $this->load->helper("app_validation_helper");
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->helper('url');
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    /**
     * Author : Yash | Ravishankar
     * Date: 2019-08-14
     *
     * This function redirects to specific page if user is already logged-in
     * else redirects to login page
     */
    public function index()
    {
        log_message('DEBUG', 'In index | Checking if user is there in session');
        $logged_in = $this->session->userdata("logged_in_user");
        if (isset($logged_in[0])) {
            log_message('DEBUG', 'In index | User is there in session, redirecting to ' . $logged_in[0]['url']);
            redirect($logged_in[0]['url']);
        } else {
            log_message('DEBUG', 'In index | User is not in session, redirecting to login page');
            $this->load->view('ro_manager/login');
        }
    }

    /**
     * Author : Ravishankar Singh | Yash
     * Date: 2019-08-15
     * This function takes data from login page then validates the data.
     * If credentials are correct then a session is created and user is logged in and redirected to specific page.
     * If credentials are incorrect then it redirects to login page with error message
     */
    public function postLogin()
    {
        log_message('DEBUG', 'In post_login | Validating login data');
        $error_in_validation = set_form_validation($this->config->item('login_form'));

        if ($error_in_validation) {
            log_message('DEBUG', 'In post_login | Error in Validating login data');
            show_form_validation_error('ro_manager/login');
        } else {
            log_message('DEBUG', 'In post_login | Successfully validated login data');
            $email = $this->input->post('email');
            $password = $this->input->post('passwd');

            // DB query
            $userObject = new UserLoginFeature();
            //Getting user record
            $user = $userObject->userLogin($email, $password);

            if ($user == NULL) {
                log_message('DEBUG', 'In post_login | User not found in database after validation redirecting to login page');
                $model = array();
                $model['error_msg'] = $this->config->item('login_form_error_code_1');
                $this->load->view('ro_manager/login', $model);
            } else {
                log_message('DEBUG', 'In post_login | User found in database after validation');
                //adding logged_in_user in session
                $this->addToSession('logged_in_user', $user);
                //getting user_region_id
                $region = $userObject->setUserNoneRegion($user[0]['user_id']);

                $this->addToSession('logged_in_user_none_region', $region[0]['region_id']);

                $this->loadMenuToSession($user[0]['profile_id']);

                redirect($user[0]['url']);
            }
        }
    }

    /**
     * Author :  Ravishankar Singh | Yash
     * Date: 2019-08-19
     *
     * @param $profileId
     * This function fetches the menu details of a given user.
     * Then adds menu in session (this prevents repeating call to load menu again and again from controllers
     */
    public function loadMenuToSession($profileId)
    {
        $menuObject = new MenuFeature();
        $menu = $menuObject->getHeaderMenu($profileId);
        $this->addToSession('menu', $menu);
    }

    /**
     * Author : Ravishankar | Yash
     * Date: 2019-08-21
     *
     * @param $key
     * @param $value
     * This function sets user data as key value pair in current session.
     */
    private function addToSession($key, $value)
    {
        log_message('DEBUG', 'In addToSession | Adding ' . $key . ' to session');
        $this->session->set_userdata($key, $value);
        log_message('DEBUG', 'In addToSession | ' . $key . ' successfully added to session');
    }
}