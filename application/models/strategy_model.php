<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 9/16/15
 * Time: 4:53 PM
 */

class Strategy_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getAllUserData()
    {
        $query = "select ru.user_id,ru.user_name,ru.reporting_manager_id,"
            . "rup.profile_name as designation,rur.region_id,rmgr.region_name, ru.profile_image, "
            . " (select user_name FROM ro_user where user_id = ru.reporting_manager_id) as reporting_to "
            . " from ro_user ru"
            . " inner join ro_user_profile rup on ru.profile_id = rup.profile_id "
            . " inner join ro_user_region rur on ru.user_id = rur.user_id"
            . " inner join ro_master_geo_regions rmgr on rmgr.id = rur.region_id"
            . " where ru.profile_id in (6,11,12) and ru.active = 1 and rur.region_id != 9 ";

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

    public function get_ros_submitted_by_user($userId)
    {

        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        $query = "select * from ro_am_external_ro as am
                    inner join ro_external_ro_report_details as nr on am.internal_ro = nr.internal_ro_number where user_id in ($userId)";
        $query = $query . " and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
                                                or
                ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' ))";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
        return array();

    }

    public function get_users_for_region($region_id)
    {
        $query = "SELECT * from ro_user as ru
                    INNER JOIN ro_user_region rur on ru.user_id = rur.user_id
                    WHERE ru.profile_id in (6,11,12) AND ru.active = 1";

        if ($region_id != 'all') {
            $query .= " AND rur.region_id = $region_id ";
        }

        $logged_in = $this->session->userdata("logged_in_user");
        $userType = $logged_in[0]['is_test_user'];

        $query .= " and ru.is_test_user = '$userType' ";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            //add association=1 or 0
            return $result->result("array");
        }
        return array();
    }

    public function getUserRoData($user_id, $region_id, $start_date, $end_date)
    {
        $query = "SELECT ru.user_name,rmgr.region_name,(select user_name FROM ro_user where user_id = ru.reporting_manager_id) as reporting_manager,am.* FROM ro_am_external_ro as am
                    INNER JOIN ro_user as ru ON am.user_id = ru.user_id
                    INNER JOIN ro_master_geo_regions rmgr on rmgr.id = am.region_id
                    WHERE am.camp_start_date >= '$start_date' AND am.camp_start_date <= '$end_date' ";

        if ($user_id != 'all') {
            $query .= " AND am.user_id = $user_id ";
        }

        if ($region_id != 'all') {
            $query .= " AND am.region_id = $region_id ";
        }

        $logged_in = $this->session->userdata("logged_in_user");
        $userType = $logged_in[0]['is_test_user'];

        $query .= " and ru.is_test_user = '$userType' and ru.profile_id in (6,11,12) and ru.active = 1 and am.region_id != 9 ";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            //add association=1 or 0
            return $result->result("array");
        }
        return array();
    }

} 