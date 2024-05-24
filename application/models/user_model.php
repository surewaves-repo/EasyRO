<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function admin_login($email, $passwd)
    {
        $query = $this->db->query("select admin_id, email from ro_admin where email='$email' and pass='$passwd' ");

        if ($query->num_rows() > 0) {
            $login = $query->result('array');
            $this->session->set_userdata('logged_in_admin', $login);
            return $login;
        }

        return Null;
    }

    public function user_login($email, $passwd)
    {

        $query = $this->db->query("select * from ro_user where user_email='$email' and user_password=md5('$passwd') and active = 1 ");

        if ($query->num_rows() > 0) {
            $login = $query->result('array');
            $this->session->set_userdata('logged_in_user', $login);

            $this->setUserNoneRegion($login[0]['user_id']);

            return $login;
        }

        return Null;
    }

    public function setUserNoneRegion($userId)
    {

        $query = "SELECT *
                    FROM ro_user_region
                  WHERE user_id = $userId
                    ";
        $result = $this->db->query($query);
        $resultArray = $result->result("array");
        $this->session->set_userdata('logged_in_user_none_region', $resultArray[0]['region_id']);

        return 1;
    }

    public function logout()
    {
        $this->session->sess_destroy();
    }

    public function get_profiles()
    {
        $query = $this->db->query("select profile_id, profile_name from ro_user_profile");
        return $query->result('array');
    }

    public function add_user($userdata)
    {
        $this->db->insert("ro_user", $userdata);
        return $this->db->insert_id();
    }

    public function delete_user($user_id)
    {
        $query = "delete from ro_user where user_id = $user_id";

        $this->db->query($query);
    }

    public function manageUserActivationDeactivation($user_id, $activate)
    {
        $date = date("Y-m-d H:i:s");
        $query = "update ro_user SET active = $activate,deactivation_date='" . $date . "' where user_id =" . $user_id;

        $this->db->query($query);
    }

    public function get_bhs()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select * from ro_user where profile_id IN(1,2) and is_test_user='$is_test_user'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_user_detail_for_profile($profile_id)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select * from ro_user where profile_id in ($profile_id) and is_test_user='$is_test_user'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function getUserDetailForGivenRegionAndProfile($region_id, $profileIds, $userType)
    {
        $query = "select * from ro_user ru "
            . "Inner Join ro_user_region rur on ru.user_id = rur.user_id "
            . "where ru.profile_id in ($profileIds) and rur.region_id = $region_id and ru.is_test_user='$userType' and ru.active=1 ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function getUserDetailValues($userId)
    {
        $query = "select ru.user_id,ru.user_name,ru.user_email,ru.user_phone,ru.reporting_manager_id,ru.profile_id,"
            . "rup.profile_name as designation,rur.region_id,rmgr.region_name, ru.profile_image "
            . " from ro_user ru"
            . " inner join ro_user_profile rup on ru.profile_id = rup.profile_id "
            . " inner join ro_user_region rur on ru.user_id = rur.user_id"
            . " inner join ro_master_geo_regions rmgr on rmgr.id = rur.region_id"
            . " where ru.user_id='$userId' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            //add association=1 or 0
            return $result->result("array");
        }
        return array();
    }

    public function get_user_detail_for_user_id($user_id)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select * from ro_user where user_id=$user_id and is_test_user='$is_test_user'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function edit_user($user_id)
    {
        $query = "select * from ro_user where user_id = $user_id";

        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_users()
    {
        $logged_in = $this->session->userdata("logged_in_user");

        $query = $this->db->query("select * from ro_user as u, ro_user_profile as p where u.profile_id=p.profile_id order by u.user_name");
        /*$query = " SELECT DISTINCT ru.*,
                    ru.reporting_manager_id,
                    ru.user_id,
                    ru.profile_id,
                    rur.region_id,
                    rup.profile_name
                    FROM   ro_user ru
                           INNER JOIN ro_user_profile rup
                                   ON ru.profile_id
                           LEFT OUTER JOIN ro_user_region rur
                                        ON ru.user_id = rur.user_id
                    GROUP  BY ru.user_id,
                              ru.user_email  " ;
        */
        return $query->result('array');
    }

    public function get_users_other()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = $this->db->query("select * from ro_user where profile_id in (3,4,8) and is_test_user='$is_test_user' ");
        return $query->result('array');
    }

    public function get_users_admin()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = $this->db->query("select * from ro_user where profile_id in (1,2,7) and is_test_user='$is_test_user' ");
        return $query->result('array');
    }

    public function get_user_management_detail($userType)
    {
        $query = $this->db->query("select * from ro_user where profile_id in (1,2) and is_test_user='$userType' ");
        return $query->result('array');
    }

    public function get_ro_user_from_email($email)
    {
        //Checking email id for resuse if user is deactivate

        $query = $this->db->query("select * from ro_user where user_email='$email' and active = 1");
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return NULL;
    }

    public function get_profile_details($id)
    {
        $query = "SELECT * FROM  `ro_user_profile` WHERE profile_id =$id ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array(); //no orders found return null array;
    }

    public function update_user($user_data, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('ro_user', $user_data);
    }

    public function get_all_mainusers($profile_ids)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select * from ro_user where profile_id IN($profile_ids) and is_test_user='$is_test_user'";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_all_customer_detail()
    {
        $query = "select customer_id,customer_name,customer_email from sv_customer order by customer_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_billing_name($cid)
    {
        $query = "select billing_name from sv_customer where customer_id=$cid";
        $res = $this->db->query($query);
        $billing_names = $res->result("array");

        return $billing_names[0]['billing_name'];
    }

    public function get_customer_email($cid)
    {
        $query = "select customer_email from sv_customer where customer_id=$cid";
        $res = $this->db->query($query);
        $customer_emails = $res->result("array");

        return $customer_emails[0]['customer_email'];
    }

    public function get_all_channels_for_customer($cid)
    {
        $query = "select * from sv_tv_channel where enterprise_id = $cid";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function get_am_user_email($customer_ro)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "SELECT user_email FROM  ro_am_external_ro AS re INNER JOIN ro_user AS ru ON re.user_id = ru.user_id
		WHERE cust_ro = '" . $customer_ro . "' and is_test_user='$is_test_user' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            return $res[0]['user_email'];
        }
        return array();

    }

    public function getUserData($profileName)
    {
        $query = "select ru.user_id,ru.user_name,ru.user_email,ru.user_phone,ru.reporting_manager_id,"
            . "rup.profile_name as designation,rur.region_id,rmgr.region_name, ru.profile_image "
            . " from ro_user ru"
            . " inner join ro_user_profile rup on ru.profile_id = rup.profile_id "
            . " inner join ro_user_region rur on ru.user_id = rur.user_id"
            . " inner join ro_master_geo_regions rmgr on rmgr.id = rur.region_id"
            . " where rup.profile_name='$profileName' and ru.active = 1 ";

        $logged_in = $this->session->userdata("logged_in_user");
        $userType = $logged_in[0]['is_test_user'];

        $query .= " and  ru.is_test_user = '$userType' ";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            //add association=1 or 0
            return $result->result("array");
        }
        return array();
    }

    public function getUserValues($whereData)
    {
        $result = $this->db->get_where('ro_user', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getUserDataForUserId($userId)
    {
        $query = "select ru.user_id,ru.user_name,ru.user_email,ru.user_phone,ru.reporting_manager_id,"
            . " rup.profile_name as designation,rup.profile_id,rur.region_id,rmgr.region_name, "
            . "(select profile_id from ro_user where user_id = ru.reporting_manager_id) as reporting_manager_profile_id"
            . " from ro_user ru"
            . " inner join ro_user_profile rup on ru.profile_id = rup.profile_id "
            . " inner join ro_user_region rur on ru.user_id = rur.user_id"
            . " inner join ro_master_geo_regions rmgr on rmgr.id = rur.region_id"
            . " where ru.user_id='$userId' ";

        $logged_in = $this->session->userdata("logged_in_user");
        $userType = $logged_in[0]['is_test_user'];

        $query .= " and  ru.is_test_user = '$userType' ";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getAllReportingManagers()
    {

        $query = " SELECT profile_id,
                       user_id,
                       user_name
                FROM   ro_user
                WHERE  profile_id IN ( 1, 10 ) ";

        $logged_in = $this->session->userdata("logged_in_user");
        $userType = $logged_in[0]['is_test_user'];

        $query .= " and is_test_user = '$userType' ";

        $resultObj = $this->db->query($query);

        return $resultObj->result("array");

    }

    public function getProfileGroupByRegion($result)
    {
        $data = array();
        foreach ($result as $res) {
            $userId = $res['user_id'];
            if (!array_key_exists($userId, $data)) {
                $data[$userId]['user_id'] = $userId;
                $data[$userId]['user_name'] = $res['user_name'];
                $data[$userId]['reporting_manager_id'] = $res['reporting_manager_id'];
                $data[$userId]['designation'] = $res['designation'];
                $data[$userId]['region_name'] = $res['region_name'];
                $data[$userId]['region_id'] = $res['region_id'];

                $data[$userId]['profile_image'] = $res['profile_image'];

            } else {
                $data[$userId]['region_name'] = $data[$userId]['region_name'] . "," . $res['region_name'];
            }
        }
        return $data;
    }

    public function associateUserWithRegion($userId, $regionId)
    {

        $query = "INSERT INTO ro_user_region ( user_id, region_id ) values('$userId', '$regionId' ) ";
        return $this->db->query($query);
    }

    public function updateUserWithRegion($userData, $userId)
    {
        $this->db->where('user_id', $userId);
        $this->db->update('ro_user_region', $userData);
    }

    function getInsertedRowId($email, $profileId, $createDateTime)
    {

        $query = " select user_id from ro_user where user_email = '$email' and profile_id = '$profileId' and creation_datetime = '$createDateTime' ; ";
        $resultObj = $this->db->query($query);

        $userId = $resultObj->result("array");
        return $userId[0]['user_id'];
    }

    function deleteFromUserRegions($user_id)
    {

        $query = "delete from ro_user_region where user_id = $user_id";
        $this->db->query($query);
    }

    public function getUserDetails($whereData)
    {
        $result = $this->db->get_where('ro_user', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function ro_creation_history($user_id, $ro_id, $is_fct)
    {
        $user_map = $this->get_user_hierarchy($user_id);

        //insert into table to record RO creation history

        $account_manager_id = $user_map[6];
        $group_manager_id = $user_map[12];
        $regional_director_id = $user_map[11];
        $national_head_id = $user_map[10];
        $business_head_id = $user_map[1];


        $data = array(
            'external_ro_id' => $ro_id,
            'account_manager' => $account_manager_id,
            'group_manager' => $group_manager_id,
            'regional_director' => $regional_director_id,
            'national_head' => $national_head_id,
            'business_head' => $business_head_id,
            'is_fct' => $is_fct
        );

        $this->db->insert('ro_external_ro_user_map', $data);
    }

    public function get_user_hierarchy($user_id)
    {

        //getting user hierarchy of user who submitted RO

        $query = "SELECT t1.profile_id AS lev1profile_id, t1.user_id AS lev1userid, t2.profile_id AS lev2profile_id, t2.user_id AS lev2userid, t3.profile_id AS lev3profile_id, t3.user_id AS lev3userid, t4.profile_id AS lev4profile_id, t4.user_id AS lev4userid, t5.profile_id AS lev5profile_id, t5.user_id AS lev5userid
                        FROM ro_user AS t1
                        LEFT JOIN ro_user AS t2 ON t1.reporting_manager_id = t2.user_id
                        LEFT JOIN ro_user AS t3 ON t2.reporting_manager_id = t3.user_id
                        LEFT JOIN ro_user AS t4 ON t3.reporting_manager_id = t4.user_id
                        LEFT JOIN ro_user AS t5 ON t4.reporting_manager_id = t5.user_id
                        WHERE t1.user_id =$user_id";
        $result = $this->db->query($query);
        $userMap = array();
        if ($result->num_rows() > 0) {
            $resultArray = $result->result("array");
            $userMap[$resultArray[0]['lev1profile_id']] = $resultArray[0]['lev1userid'];
            $userMap[$resultArray[0]['lev2profile_id']] = $resultArray[0]['lev2userid'];
            $userMap[$resultArray[0]['lev3profile_id']] = $resultArray[0]['lev3userid'];
            $userMap[$resultArray[0]['lev4profile_id']] = $resultArray[0]['lev4userid'];
            $userMap[$resultArray[0]['lev5profile_id']] = $resultArray[0]['lev5userid'];
            return $userMap;
        }
        return array();
    }

    public function updateReportingManagerForBelowUser($userIds, $reportingManagerId)
    {
        $query = "update ro_user set reporting_manager_id='$reportingManagerId' where user_id in ($userIds) ";
        $this->db->query($query);
    }

    public function filterUserByLoggedIn($userData, $loggedInUserId)
    {
        return $userData[$loggedInUserId];
    }

    public function getParent($profileId)
    {
        $query = "select rup.profile_id,rup.profile_name"
            . " from ro_user_profile rup"
            . " inner join ro_profile_hierarchy rph on rup.profile_id = rph.parent_id "
            . " where rph.profile_id='$profileId' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();

    }

    public function getUserDetailOfUserReportingManager($userId)
    {
        $query = "select * from ro_user where user_id in (select reporting_manager_id from ro_user where user_id = $userId) ";

        $logged_in = $this->session->userdata("logged_in_user");
        $userType = $logged_in[0]['is_test_user'];

        $query .= " and is_test_user = '$userType' ";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
            //return $res[0]['user_email'];
        }
        return array();
    }

    public function isLeafNode($userId)
    {
        $query = "select * from ro_user where user_id=$userId ";
        $this->db->query($query);

    }

    public function getProfileActualHierarchy()
    {

        $query = "select profile_id , parent_id from ro_profile_hierarchy ";
        $resultObj = $this->db->query($query);
        return $resultObj->result("array");
    }

    public function getReportingManagersUsingProfileId($profileIds)
    {

        $query = " select distinct user_id, user_name from ro_user where profile_id IN( '" . $profileIds . "')";

        $resultObj = $this->db->query($query);
        return $resultObj->result("array");

    }

    public function getUsersReportingNHForRegion($nh, $region_id)
    {
        $query = "SELECT * FROM `ro_user` AS ru INNER JOIN ro_user_region AS rur ON rur.user_id = ru.user_id WHERE ru.reporting_manager_id = $nh AND rur.region_id = $region_id ";
        $resultObj = $this->db->query($query);
        return $resultObj->result("array");
    }

    public function associateUsersToRD($userId, $reportingManagerId)
    {
        $query = "update ro_user set reporting_manager_id='$reportingManagerId' where user_id  = $userId and user_id  != $reportingManagerId ";
        $this->db->query($query);
    }

    public function check_RD_for_region($region_id)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "SELECT ru.user_id,ru.active,rur.region_id FROM `ro_user` AS ru INNER JOIN ro_user_region AS rur ON ru.user_id = rur.user_id WHERE ru.profile_id =11 AND ru.active =1 AND ru.is_test_user = $is_test_user AND rur.region_id = $region_id GROUP BY rur.region_id";
        $resultObj = $this->db->query($query);
        return $resultObj->result("array");
    }

    function updateUserImage($imageName, $userId)
    {

        $query = " UPDATE ro_user set profile_image = '$imageName' where user_id = $userId ";
        $this->db->query($query);
    }

    public function get_targets($table_name, $whereData)
    {
        $result = $this->db->get_where($table_name, $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_targets($table_name, $monthlyTargetUserData, $whereData)
    {
        //$this->db->where('user_id', $whereData);
        $this->db->update($table_name, $monthlyTargetUserData, $whereData);
    }

    public function insert_targets($table_name, $userdata)
    {
        $this->db->insert($table_name, $userdata);
        return $this->db->insert_id();
    }

    public function get_discount_for_profile($whereData)
    {
        $result = $this->db->get_where('ro_profile_discounts', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_discounts_for_profile($discountData, $whereData)
    {
        $this->db->update('ro_profile_discounts', $discountData, $whereData);
    }

    public function insert_discounts_for_profile($userdata)
    {
        $this->db->insert("ro_profile_discounts", $userdata);
        return $this->db->insert_id();
    }

    public function get_parent_discounts($profile_id)
    {
        $query = "SELECT discount FROM ro_profile_discounts WHERE profile_id = ( SELECT parent_id FROM ro_profile_hierarchy WHERE profile_id =$profile_id ) ";
        $resultObj = $this->db->query($query);
        return $resultObj->result("array");
    }

    public function get_child_discounts($profile_id)
    {
        $query = "SELECT discount FROM ro_profile_discounts WHERE profile_id = ( SELECT profile_id FROM ro_profile_hierarchy WHERE parent_id =$profile_id ) ";
        $resultObj = $this->db->query($query);
        return $resultObj->result("array");
    }

    public function getApprovalLevel($profileId)
    {
        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$profileId];
        $approvalLevel = $positionLevelForUser + 1;
        return $approvalLevel;
    }

    public function userEmailForRoCreation($userId)
    {
        $userDetail = $this->getUserDetailForUserId($userId);
        $userProfileID = $userDetail[0]['profile_id'];
        $userRegionId = $userDetail[0]['region_id'];

        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$userProfileID];
        $nextPositionLevelValue = $positionLevelForUser + 1;

        $usersProfileIdsForNextPosition = $this->getUserProfileIdsForNextPosition($profileApprovalPosition, $nextPositionLevelValue);
        $userMailIds = $this->getUserDeatilForProfileIdsAndRegion($usersProfileIdsForNextPosition, $userRegionId);
        $emailData = array();
        foreach ($userMailIds as $val) {
            array_push($emailData, $val['user_email']);
        }
        $emailVal = implode(",", $emailData);
        return $emailVal;

    }

    public function getUserDetailForUserId($userId)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $userType = $logged_in[0]['is_test_user'];

        $query = "select ru.user_id,ru.user_name,ru.user_email,ru.user_phone,ru.reporting_manager_id,ru.profile_id,"
            . "rup.profile_name as designation,rur.region_id,rmgr.region_name, ru.profile_image "
            . " from ro_user ru"
            . " inner join ro_user_profile rup on ru.profile_id = rup.profile_id "
            . " inner join ro_user_region rur on ru.user_id = rur.user_id"
            . " inner join ro_master_geo_regions rmgr on rmgr.id = rur.region_id"
            . " where ru.user_id='$userId' and ru.active = 1 ";

        if (isset($userType)) {
            $query .= " and  ru.is_test_user = '$userType' ";
        }

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            //add association=1 or 0
            return $result->result("array");
        }
        return array();
    }

    //Use for it creation and forward

    public function getUserProfileIdsForNextPosition($profileApprovalPosition, $nextPositionLevelValue)
    {
        $profileIds = '';
        foreach ($profileApprovalPosition as $key => $val) {
            if ($val == $nextPositionLevelValue) {
                if (empty($profileIds) || !isset($profileIds)) {
                    $profileIds = $key;
                } else {
                    $profileIds .= "," . $key;
                }

            }
        }
        return $profileIds;

    }

    public function getUserDeatilForProfileIdsAndRegion($profileIds, $regionId)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $userType = $logged_in[0]['is_test_user'];

        $query = "select ru.user_id,ru.user_name,ru.user_email,ru.user_phone,ru.reporting_manager_id,ru.profile_id,"
            . "rup.profile_name as designation,rur.region_id,rmgr.region_name, ru.profile_image "
            . " from ro_user ru"
            . " inner join ro_user_profile rup on ru.profile_id = rup.profile_id "
            . " inner join ro_user_region rur on ru.user_id = rur.user_id"
            . " inner join ro_master_geo_regions rmgr on rmgr.id = rur.region_id"
            . " where ru.profile_id in ($profileIds) and ru.active = 1 and rur.region_id= $regionId";

        $query .= " and  ru.is_test_user = '$userType' ";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            //add association=1 or 0
            return $result->result("array");
        }
        return array();
    }

    public function userEmailForRoRejection($userId)
    {
        $userDetail = $this->getUserDetailForUserId($userId);
        $userProfileID = $userDetail[0]['profile_id'];
        $userRegionId = $userDetail[0]['region_id'];

        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$userProfileID];

        $usersProfileIdsForBelowPosition = $this->getUserProfileIdsForBelowPosition($profileApprovalPosition, $positionLevelForUser);
        if (empty($usersProfileIdsForBelowPosition) || !isset($usersProfileIdsForBelowPosition)) {
            $usersProfileIdsForBelowPosition = $userProfileID;
        }
        $userMailIds = $this->getUserDeatilForProfileIdsAndRegion($usersProfileIdsForBelowPosition, $userRegionId);
        //In this email id,add extra email for whoever created it.
        $emailData = array();
        foreach ($userMailIds as $val) {
            array_push($emailData, $val['user_email']);
        }
        $emailVal = implode(",", $emailData);
        return $emailVal;
    }

    public function getUserProfileIdsForBelowPosition($profileApprovalPosition, $positionLevel)
    {
        $profileIds = '';
        foreach ($profileApprovalPosition as $key => $val) {
            //Why $val != 0;not knowing Either GM Created Or AM Created
            if (($val < $positionLevel) && ($val != 0)) {
                if (empty($profileIds) || !isset($profileIds)) {
                    $profileIds = $key;
                } else {
                    $profileIds .= "," . $key;
                }

            }
        }
        return $profileIds;

    }

    public function userEmailForRoApproval($userId)
    {

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select user_email from ro_user where profile_id IN(1,3,10) and is_test_user='$is_test_user' and active=1 ";
        $res = $this->db->query($query);
        $result = $res->result("array");
        $email_list = '';
        if ($res->num_rows() > 0) {
            foreach ($result as $val) {
                if (empty($email_list)) {
                    $email_list = $val['user_email'];
                } else {
                    $email_list = $email_list . "," . $val['user_email'];
                }
            }
            return $email_list;
        }

        return array();
    }

    public function getEmailIdsForProfile($profileIds)
    {

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $ProfileList = '';

        foreach ($profileIds as $profile) {
            if ($ProfileList == '') {
                $ProfileList = "'" . $profile . "'";
            } else {
                $ProfileList = $ProfileList . ",'" . $profile . "'";
            }
        }

        $query = "select user_email from ro_user where profile_id IN ($ProfileList) and is_test_user='$is_test_user' and active=1 ";
        $res = $this->db->query($query);
        $result = $res->result("array");
        $email_list = '';
        if ($res->num_rows() > 0) {
            foreach ($result as $val) {
                if (empty($email_list)) {
                    $email_list = $val['user_email'];
                } else {
                    $email_list = $email_list . "," . $val['user_email'];
                }
            }
            return $email_list;
        }

        return array();
    }

    public function getRegionIdForUser($userId)
    {
        $query = "select region_id from ro_user_region where user_id = $userId";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            //add association=1 or 0
            return $result->result("array");
        }
    }
}

?>
