<?php if (!defined('BASEPATH')) exit("NO Direct Script Access Allowed");

class User_manager extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        $this->load->model('user_model');
        $this->load->model('mg_model');
        $this->load->model("menu_model");
        $this->load->library('session');
        $this->load->helper('generic_helper');
        $this->load->library('curl');
        $this->load->helper('url');
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    public function user_management($user_action = NULL)
    {
        /*$user_action =    1:Create    2:Delete
                            3:Edited    4:Promoted
                            5.Targets set
        */

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        //Get All Regions
        $regions = $this->mg_model->getAllRegions();

        $all_regions = array();

        foreach ($regions as $reg) {

            $temp = array();

            $rd_for_region = $this->user_model->check_RD_for_region($reg['id']);
            $temp['id'] = $reg['id'];
            $temp['region_name'] = $reg['region_name'];


            if (count($rd_for_region) == 0) {
                $temp['rd_exists'] = 0;
            } else {
                $temp['rd_exists'] = 1;
            }

            array_push($all_regions, $temp);
        }

        //Get ALL Profile Details
        $businessHead = $this->user_model->getUserData('Business Head');
        $nationalHead = $this->user_model->getUserData('National Head');
        $nationalHeadGroupByMarket = $this->user_model->getProfileGroupByRegion($nationalHead);
        $loginUser = $this->user_model->filterUserByLoggedIn($nationalHeadGroupByMarket, $logged_in[0]['user_id']);
        $businessHeadGroupByMarket = $this->user_model->getProfileGroupByRegion($businessHead);

        $regionalDirectors = $this->user_model->getUserData('Regional Director');
        $groupManagers = $this->user_model->getUserData('Group Manager');
        $accountManagers = $this->user_model->getUserData('Account Manager');

        $menu = $this->menu_model->get_header_menu();

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $model['regions'] = $all_regions;
        $model['business_head'] = $businessHeadGroupByMarket;
        $model['national_head'] = $loginUser;
        $model['regional_directors'] = $regionalDirectors;
        $model['group_managers'] = $groupManagers;
        $model['account_managers'] = $accountManagers;
        $model['user_action'] = $user_action;

        $this->load->view('ro_manager/user_management', $model);
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

    public function post_create_user_data()
    {
        $this->is_logged_in();
        $logged_in_user = $this->session->userdata("logged_in_user");

        $username = $this->input->post('name');
        $email = $this->input->post('email');
        $profile_id = $this->input->post('profile_id');

        $user_data = array(
            'user_name' => $this->input->post('name'),
            'user_email' => $this->input->post('email'),
            'user_phone' => $this->input->post('contact'),
            'user_password' => md5('surew@ves'),
            'profile_id' => $this->input->post('profile_id'),
            'reporting_manager_id' => $this->input->post('reporting_manager_id'),
            'active' => 1,
            'reporting_manager_id' => $this->input->post('reporting_manager_id'),
            'plain_text_password' => 'surew@ves',
            'creation_datetime' => date('Y-m-d H:i:s'),
            'reset_password' => 1,
            'is_test_user' => $logged_in_user[0]['is_test_user']
        );
        $user_id = $this->user_model->add_user($user_data);
        $region_id = $this->input->post('region');
        $this->user_model->associateUserWithRegion($user_id, $region_id);

        $nh = $this->input->post('nh_user_id');

        if ($profile_id == 11) {
            //Users reporting NH will now report to RD
            $users_reporting_nh = $this->user_model->getUsersReportingNHForRegion($nh, $region_id);
            foreach ($users_reporting_nh as $user) {
                $this->user_model->associateUsersToRD($user['user_id'], $user_id);
            }
        }

        /*intimating user for creation*/
        $profile_id = $this->input->post('profile_id');
        $profile_details = $this->user_model->get_profile_details($profile_id);
        $profile_name = $profile_details[0]['profile_name'];
        $login_url = 'http://' . $_SERVER['SERVER_NAME'] . ROOT_FOLDER;
        $to = $email;
        $text = "login_url";
        $subject = array('USER_NAME' => $username);
        $message = array(
            'USER_NAME' => $username,
            'USER_EMAIL' => $email,
            'PROFILE_NAME' => $profile_name,
            'PASSWORD' => 'surew@ves',
            'LOGIN_URL' => $login_url,
        );
        $file = '';
        $url = '';
        $cc = 'nitish@surewaves.com';

        mail_send($to, $text, $subject, $message, $file, $cc, $url);

        redirect('user_manager/user_management/1');

        //Mail logic is remaining

    }

    public function post_delete_user()
    {
        $userId = $this->input->post('hid_delete_user_id');

        $this->updateReportingManagerForBelowUser($userId);

        //Deactivate  the user
        $activate = 0;
        $this->user_model->manageUserActivationDeactivation($userId, $activate);
        redirect('user_manager/user_management/2');
    }

    public function updateReportingManagerForBelowUser($userId)
    {
        //Get All user which is below to them
        $belowUsers = $this->user_model->getUserDetails(array('reporting_manager_id' => $userId));
        $belowUserIds = array();

        if (count($belowUsers) > 0) {
            foreach ($belowUsers as $user) {
                array_push($belowUserIds, $user['user_id']);
            }
            $belowUserIds = implode(",", $belowUserIds);

            //Find reporting Manager of it
            $userData = $this->user_model->getUserDetails(array('user_id' => $userId));
            $reportingManagerId = $userData[0]['reporting_manager_id'];

            //Assign all user below to them 
            $this->user_model->updateReportingManagerForBelowUser($belowUserIds, $reportingManagerId);
        }
    }

    public function post_edit_user_data()
    {
        //print_r($_POST);exit;
        $userId = $this->input->post('hid_user_id');
        //$this->updateReportingManagerForBelowUser($userId) ;
        $userData = array(
            'user_name' => $this->input->post('name'),
            'user_phone' => $this->input->post('contact'),
            'reporting_manager_id' => $this->input->post('reporting_manager_id')
        );
        $this->user_model->update_user($userData, $userId);
        $region_id = $this->input->post('region');
        $this->user_model->updateUserWithRegion(array('region_id' => $region_id), $userId);
        redirect('user_manager/user_management/3');
    }


    public function get_user_details()
    {
        //print_r($_POST);exit;
        $user_id = $this->input->post('user_id');
        $userData = $this->user_model->getUserDataForUserId($user_id);
        $parentData = $this->user_model->getParent($userData[0]['profile_id']);
        $data = $this->mergeUserAndParent($userData, $parentData);

        echo json_encode($data);
    }

    public function mergeUserAndParent($userData, $parentData)
    {
        $data = array();

        $tmp = array();
        $tmp['user_id'] = $userData[0]['user_id'];
        $tmp['user_name'] = $userData[0]['user_name'];
        $tmp['user_email'] = $userData[0]['user_email'];
        $tmp['user_phone'] = $userData[0]['user_phone'];
        $tmp['reporting_manager_id'] = $userData[0]['reporting_manager_id'];
        $tmp['reporting_manager_profile_id'] = $userData[0]['reporting_manager_profile_id'];
        $tmp['designation'] = $userData[0]['designation'];
        $tmp['profile_id'] = $userData[0]['profile_id'];
        $tmp['region_id'] = $userData[0]['region_id'];
        $tmp['region_name'] = $userData[0]['region_name'];
        $tmp['parent_profile_id'] = $parentData[0]['profile_id'];
        $tmp['parent_profile_name'] = $parentData[0]['profile_name'];

        array_push($data, $tmp);
        return $data;

    }

    public function getParentData($userId)
    {
        $data = array();

        //Check Reporting Manager 0
        //$this->user_model->isLeafNode($userId) ;

        //
        $userData = $this->user_model->getUserDetailOfUserReportingManager($userId);
        if (count($userData) > 0) {
            $data['parent_id'] = $userData[0]['user_id'];
            $data['parent_name'] = $userData[0]['user_name'];

            if ($userData[0]['reporting_manager_id'] != 0) {
                $parentData = $this->user_model->getUserDetailOfUserReportingManager($userData[0]['user_id']);

                if (count($parentData) > 0) {
                    $data['grand_parent_id'] = $parentData[0]['user_id'];
                    $data['grand_parent_name'] = $parentData[0]['user_name'];
                } else {
                    $data['grand_parent_id'] = 'Not_Exist';
                    $data['grand_parent_name'] = 'Not_Exist';
                }
            }
        } else {
            $data['parent_id'] = 'Not_Exist';
            $data['parent_name'] = 'Not_Exist';
            $data['grand_parent_id'] = 'Not_Exist';
            $data['grand_parent_name'] = 'Not_Exist';
        }

        echo json_encode($data);
    }

    function duplicate_user_email_check()
    {

        $str = $this->input->post('emailId');

        $ret = $this->user_model->get_ro_user_from_email($str);

        if (isset($ret) && !empty($ret[0]['user_email'])) {
            echo "FALSE";
        } else {
            echo "TRUE";
        }
    }

    function uploadProfileImage()
    {

        $error = "";
        $msg = "";
        $filename = "Uploading Error";
        $file_element_name = 'avatar_file';

        $userId = $this->input->post("hid_profile_image_user_id");

        $avatarData = json_decode($this->input->post("avatar_data"));

        $cropXAxis = $avatarData->x;
        $cropYAxis = $avatarData->y;
        $cropHeight = $avatarData->height;
        $cropWidth = $avatarData->width;

        $FileName = $_FILES[$file_element_name]['name'];

        $fileExt = array_pop(explode(".", $FileName));
        $new_filename = md5(time()) . "." . $fileExt;

        $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . 'surewaves_easy_ro/uploads/';

        $config['allowed_types'] = '*';
        $config['max_size'] = 1024 * 50;
        $config['max_width'] = '10000';
        $config['max_height'] = '10000';
        $config['file_name'] = $new_filename;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($file_element_name)) {
            $status = 'error';
            $filename = $this->upload->display_errors();
        } else {
            $data = $this->upload->data();
            $filename = $data['file_name'];
            $status = 200;
            $msg = "File successfully uploaded";

            $file = $_SERVER['DOCUMENT_ROOT'] . 'surewaves_easy_ro/uploads/' . $filename;
            $type = exif_imagetype($file);

            $this->crop($file, $file, $type, $cropWidth, $cropHeight, $cropXAxis, $cropYAxis);

            $fileNameUserId = $filename . "_" . $userId;

            $this->uploadImageOnS3($file, $fileNameUserId);

            $s3FilePath = "https://s3-ap-southeast-1.amazonaws.com/" . SALES_PROFILE_SNAP_BUCKET . "/" . $fileNameUserId;

            $this->user_model->updateUserImage($s3FilePath, $userId);

            $filename = $s3FilePath;
        }
        @unlink($_FILES[$file_element_name]);

        //echo json_encode(array('status' => $status, 'filename' =>$filename));
        echo json_encode($response = array(
            'state' => $status,
            'message' => $msg,
            'result' => $filename
        ));
    }

    private function crop($src, $dst, $type, $width, $height, $xAxis, $yAxis)
    {

        if (!empty($src) && !empty($dst)) {
            switch ($type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($src);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($src);
                    break;
            }
            $size = getimagesize($src);
            $size_w = $size[0]; // natural width
            $size_h = $size[1]; // natural height

            $src_img_w = $size_w;
            $src_img_h = $size_h;

            $tmp_img_w = $width;
            $tmp_img_h = $height;
            $dst_img_w = 220;
            $dst_img_h = 220;

            $src_x = $xAxis;
            $src_y = $yAxis;

            if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
                $src_x = $src_w = $dst_x = $dst_w = 0;
            } else if ($src_x <= 0) {
                $dst_x = -$src_x;
                $src_x = 0;
                $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
            } else if ($src_x <= $src_img_w) {
                $dst_x = 0;
                $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
            }

            if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
                $src_y = $src_h = $dst_y = $dst_h = 0;
            } else if ($src_y <= 0) {
                $dst_y = -$src_y;
                $src_y = 0;
                $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
            } else if ($src_y <= $src_img_h) {
                $dst_y = 0;
                $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
            }

            // Scale to destination position and size
            $ratio = $tmp_img_w / $dst_img_w;
            $dst_x /= $ratio;
            $dst_y /= $ratio;
            $dst_w /= $ratio;
            $dst_h /= $ratio;

            $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

            // Add transparent background to destination image
            imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
            imagesavealpha($dst_img, true);

            $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

            if ($result) {
                if (!imagepng($dst_img, $dst)) {
                    $msg = "Failed to save the cropped image file";
                }
            } else {
                $msg = "Failed to crop the image file";
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }

    private function uploadImageOnS3($fileLocation, $fileNameUserId)
    {

        require_once("S3.php");

        $s3 = new S3("10W51DTTBDR9TBACB3G2", "p5wWY/7HQcEzcXK5A45PqSm0ghy0Yfpqtkk3LrMm");
        S3::putObject(S3::inputFile("$fileLocation"), SALES_PROFILE_SNAP_BUCKET, "$fileNameUserId", S3::ACL_PUBLIC_READ);

    }

    public function post_user_targets()
    {
        $userId = $this->input->post('user_id');
        $fin_year = $this->input->post('fin_year');
        $monthlyTargets = $this->input->post('target');
        foreach ($monthlyTargets as $month => $amount) {

            $whereData = array(
                'user_id' => $userId,
                'financial_year' => $fin_year,
                'month' => $month
            );

            //check if targets are already set for User
            $user_targets = $this->user_model->get_targets('ro_user_targets', $whereData);

            $monthlyTargetUserData = array(
                'user_id' => $userId,
                'financial_year' => $fin_year,
                'month' => $month,
                'target' => $amount
            );

            if (count($user_targets) > 0) {
                //Update user targets
                $this->user_model->update_targets('ro_user_targets', $monthlyTargetUserData, $whereData);
            } else {
                //Set targets for User
                $this->user_model->insert_targets('ro_user_targets', $monthlyTargetUserData);
            }

        }
        redirect('user_manager/user_management/4');
    }

    public function get_user_targets()
    {
        $userId = $this->input->post('user_id');
        $fin_year = $this->input->post('fin_year');

        $whereData = array(
            'user_id' => $userId,
            'financial_year' => $fin_year
        );

        $monthly_targets = $this->user_model->get_targets('ro_user_targets', $whereData);

        $user_targets = array();
        foreach ($monthly_targets as $target) {
            $user_targets[$target['month']] = $target['target'];
        }

        echo json_encode($user_targets);

    }

    public function post_region_targets()
    {
        $region_id = $this->input->post('region_id');
        $fin_year = $this->input->post('fin_year');
        $monthlyTargets = $this->input->post('target');
        foreach ($monthlyTargets as $month => $amount) {

            $whereData = array(
                'region_id' => $region_id,
                'financial_year' => $fin_year,
                'month' => $month
            );

            //check if targets are already set for User
            $user_targets = $this->user_model->get_targets('ro_region_targets', $whereData);

            $monthlyTargetUserData = array(
                'region_id' => $region_id,
                'financial_year' => $fin_year,
                'month' => $month,
                'target' => $amount
            );

            if (count($user_targets) > 0) {
                //Update user targets
                $this->user_model->update_targets('ro_region_targets', $monthlyTargetUserData, $whereData);
            } else {
                //Set targets for User
                $this->user_model->insert_targets('ro_region_targets', $monthlyTargetUserData);
            }

        }
        redirect('user_manager/user_management/6');
    }

    public function get_region_targets()
    {
        $region_id = $this->input->post('region_id');
        $fin_year = $this->input->post('fin_year');

        $whereData = array(
            'region_id' => $region_id,
            'financial_year' => $fin_year
        );

        $monthly_targets = $this->user_model->get_targets('ro_region_targets', $whereData);

        $region_targets = array();
        foreach ($monthly_targets as $target) {
            $region_targets[$target['month']] = $target['target'];
        }

        echo json_encode($region_targets);

    }

    public function post_profile_discount()
    {
        $profile_id = $this->input->post('profile_id');
        $discount = $this->input->post('discount');

        $wheredata = array(
            'profile_id' => $profile_id
        );

        //check if targets are already set for User
        $profile_discounts = $this->user_model->get_discount_for_profile($wheredata);

        $discount_details = array(
            'profile_id' => $profile_id,
            'discount' => $discount
        );

        if (count($profile_discounts) > 0) {
            //Update user targets
            $this->user_model->update_discounts_for_profile($discount_details, $wheredata);
        } else {
            //Set targets for User
            $this->user_model->insert_discounts_for_profile($discount_details);
        }

        redirect('user_manager/user_management/5');
    }

    public function get_profile_discounts()
    {
        $profile_id = $this->input->post('profile_id');

        $whereData = array(
            'profile_id' => $profile_id
        );

        $parent_discount = $this->user_model->get_parent_discounts($profile_id);
        $child_discount = $this->user_model->get_child_discounts($profile_id);

        $profile_discount = $this->user_model->get_discount_for_profile($whereData);


        $discount['discount'] = $profile_discount[0]['discount'];
        $discount['parent_discount'] = $parent_discount[0]['discount'];
        $discount['child_discount'] = $child_discount[0]['discount'];

        echo json_encode($discount);
    }


}

/* End of file ro_manager.php */
/* Location: ./application/controllers/ro_manager.php */
