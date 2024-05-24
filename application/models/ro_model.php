<?php

class RO_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function insertRemarks($userData)
    {
        $this->db->insert('ro_approval_remarks', $userData);
    }

    public function get_ro($profile_id)
    {

        $query = "select * from ro_order";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array(); //no orders found return null array;
    }

    public function get_campaigns($profile_id)
    {

        $query = "SELECT  customer_ro_number,internal_ro_number,agency_name,brand_new,MIN(start_date) as start_date,MAX(end_date) as end_date,client_name , 0 as ro_approval_status FROM `sv_advertiser_campaign` as c WHERE  visible_in_easyro = 1 and c.internal_ro_number not in ( select internal_ro_number from ro_amount AS r
WHERE r.ro_approval_status =1 ) group by internal_ro_number order by start_date DESC";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array(); //no orders found return null array;
    }

    /* for Bug: 422 code changes */

    public function get_campaigns_approved($profile_id)
    {

        $query = "SELECT ac.customer_ro_number, ac.internal_ro_number, ac.agency_name, ac.brand_new, MIN( ac.start_date ) AS start_date, MAX( ac.end_date ) AS end_date, ac.client_name, r.ro_approval_status
FROM sv_advertiser_campaign AS ac, ro_amount AS r
WHERE visible_in_easyro =1
AND ac.internal_ro_number = r.internal_ro_number and r.ro_approval_status = 1
GROUP BY ac.internal_ro_number
ORDER BY r.approval_timestamp DESC ";
        /* for bug: 422 code chnages done */
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array(); //no orders found return null array;
    }

    public function get_ro_details($order_id)
    {

        $query = $this->db->query("select * from sv_advertiser_campaign where internal_ro_number='$order_id'");
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function get_ro_data($order_id)
    {
        $query = $this->db->query("select customer_ro_number,internal_ro_number,agency_name,brand_new,MIN(start_date) as start_date,MAX(end_date) as end_date,client_name from sv_advertiser_campaign where internal_ro_number = '$order_id' and visible_in_easyro = 1");

        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function get_ro_info($customer_ro)
    {

        $query = $this->db->query("select ro_amount from ro_orders where customer_ro_number='$customer_ro'");
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function ros_with_pending_requests()
    {

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $profile_id = $logged_in[0]['profile_id'];
        $profileData = $this->user_model->get_discount_for_profile(array('profile_id' => $profile_id));
        $profileDiscountPercent = $profileData[0]['discount'];
        $profileNetContribuition = unserialize(PROFILE_NETCONTRIBUITION_PERCENT);
        $profileNetContribuitionDiscount = $profileNetContribuition[$profile_id];

        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$profile_id];

        $userId = $logged_in[0]['user_id'];

        if ($profile_id == 11) {
            $belowUserIds = $this->getBelowGroupManagerUserId($userId);
        }

        switch ($is_test_user) {
            case 2 :
                $queryString = "select rcex.id,rcex.ext_ro_id, rcex.cancel_type, rcex.reason,rcex.cancel_ro_by_admin,rcex.approval_level,rcex.net_contribuition_percent,
                    rcex.date_of_submission,raex.cust_ro, raex.internal_ro FROM ro_cancel_external_ro rcex
                    Inner JOIN ro_am_external_ro raex ON rcex.ext_ro_id = raex.id 
                    where raex.id not in (select ro_id from ro_linked_advance_ro) and 
                    rcex.cancel_ro_by_admin IN (0,3) and rcex.approval_level='$positionLevelForUser' ";
                if ($profile_id == 11) {
                    $queryString = $queryString . " and raex.user_id in ($belowUserIds) ";
                }
                $queryString = $queryString . " order by rcex.date_of_submission desc";
                $query = $this->db->query($queryString);
                break;
            default:
                $queryString = "select rcex.id,rcex.ext_ro_id, rcex.cancel_type, rcex.reason,rcex.cancel_ro_by_admin,rcex.net_contribuition_percent,
                      rcex.date_of_submission,raex.cust_ro, raex.internal_ro FROM ro_cancel_external_ro rcex
                      Inner JOIN ro_am_external_ro raex ON rcex.ext_ro_id = raex.id 
                      where raex.test_user_creation='$is_test_user' and "
                    . "rcex.cancel_ro_by_admin IN (0,3) and rcex.approval_level='$positionLevelForUser' ";

                if ($profile_id == 11) {
                    $queryString = $queryString . " and raex.user_id in ($belowUserIds) ";
                }
                $queryString = $queryString . " order by rcex.date_of_submission desc";
                $query = $this->db->query($queryString);
                break;
        }
        $pending_request_for_ros = array();
        if ($query->num_rows() > 0) {
            $pending_request_res = $query->result("array");
            foreach ($pending_request_res as $req) {
                $temp = array();
                $temp['id'] = $req['id'];
                $temp['ext_ro_id'] = $req['ext_ro_id'];
                $temp['cancel_type'] = $req['cancel_type'];
                $temp['reason'] = $req['reason'];
                $temp['cust_ro'] = $req['cust_ro'];
                $temp['internal_ro'] = $req['internal_ro'];
                $temp['requested_date'] = $req['date_of_submission'];
                $temp['net_contribuition_percent'] = $req['net_contribuition_percent'];
                if ($profile_id != 1) {
                    $isLesserDiscountPercent = $this->get_from_ro_market_price($req['ext_ro_id'], $profileDiscountPercent);
                    if ($isLesserDiscountPercent || ($req['net_contribuition_percent'] < $profileNetContribuitionDiscount)) {
                        $temp['show_approve'] = 0;
                    } else {
                        $temp['show_approve'] = 1;
                    }
                } else {
                    $temp['show_approve'] = 1;
                }


                if ($req['cancel_ro_by_admin'] == 0) {
                    $temp['cancel_status'] = 0;
                }

                if ($req['cancel_type'] == 'cancel_market') {

                    $mkt_query = "select * from `ro_market_price_tmp` where am_ro_id = '" . $req['ext_ro_id'] . "' and cancel_id = '" . $req['id'] . "' and is_cancelled = '1' ";
                    $pending_mkt_res = $this->db->query($mkt_query);
                    $pending_mkt_result = $pending_mkt_res->result("array");

                    $markets_temp = array();
                    foreach ($pending_mkt_result as $market) {
                        array_push($markets_temp, $market['market']);
                    }
                    $markets_for_ro = implode(",", $markets_temp);

                    $temp['market'] = $markets_for_ro;
                }
                array_push($pending_request_for_ros, $temp);
            }

        }

        $non_fct = $this->non_fct_ros_with_pending_requests();

        $data = $this->makeFctAndNonFctInOneArray($pending_request_for_ros, $non_fct);
        $data = $this->sort_by_date($data);
        return $data;


    }

    public function getBelowGroupManagerUserId($userId)
    {
        $query = "select user_id from ro_user where reporting_manager_id='$userId' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            $belowGroupManagerIds = array();
            foreach ($res as $val) {
                array_push($belowGroupManagerIds, $val['user_id']);
            }

            if (count($belowGroupManagerIds) > 0) {
                $groupManagerIds = implode(",", $belowGroupManagerIds);
                $belowAccountManagerId = $this->getBelowAccountManagerId($groupManagerIds);
                if (count($belowAccountManagerId) > 0) {
                    $ids = array_merge($belowGroupManagerIds, $belowAccountManagerId);
                } else {
                    $ids = $belowGroupManagerIds;
                }

                $totalIds = implode(",", $ids) . "," . $userId;
                return $totalIds;
            }
        }
    }

    public function getBelowAccountManagerId($groupManagerIds)
    {
        $query = "select user_id from ro_user where reporting_manager_id in ($groupManagerIds) ";
        $result = $this->db->query($query);

        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            $belowAccountManagerIds = array();
            foreach ($res as $val) {
                array_push($belowAccountManagerIds, $val['user_id']);
            }

            return $belowAccountManagerIds;
        }
    }

    public function get_from_ro_market_price($ro_id, $profile_discount)
    {
        $query = "select * from ro_market_price where ro_id='$ro_id' and (less_spot_rate_percentage > $profile_discount or less_banner_rate_percentage > $profile_discount)";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            if (isset($res[0]['ro_id'])) {
                return true;
            }
        }

        return FALSE;
    }

    public function non_fct_ros_with_pending_requests()
    {

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $profile_id = $logged_in[0]['profile_id'];
        $profileData = $this->user_model->get_discount_for_profile(array('profile_id' => $profile_id));
        $profileDiscountPercent = $profileData[0]['discount'];
        $profileNetContribuition = unserialize(PROFILE_NETCONTRIBUITION_PERCENT);
        $profileNetContribuitionDiscount = $profileNetContribuition[$profile_id];

        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$profile_id];

        $query = $this->db->query("select  rnfreq.id, rnfreq.non_fct_ro_id, rnfreq.request_type, rnfreq.approved_by,rnfreq.requested_date,rnf.customer_ro_number, rnf.internal_ro_number
                                    FROM ro_non_fct_request rnfreq
                                    JOIN ro_non_fct_ro rnf ON rnfreq.non_fct_ro_id = rnf.id
                                    WHERE rnfreq.approved_by = 0
                                    AND rnfreq.approval_level = '$positionLevelForUser'
                                    order by rnfreq.requested_date desc");

        if ($query->num_rows() > 0) {
            $pending_request_res = $query->result("array");
            $pending_request_for_non_fct_ros = array();
            foreach ($pending_request_res as $req) {
                $temp = array();
                $temp['id'] = $req['id'];
                $temp['ext_ro_id'] = $req['non_fct_ro_id'];
                $temp['cancel_type'] = $req['request_type'];
                $temp['cancel_status'] = $req['approved_by'];
                $temp['reason'] = 'None';
                $temp['cust_ro'] = $req['customer_ro_number'];
                $temp['internal_ro'] = $req['internal_ro_number'];
                $temp['requested_date'] = $req['requested_date'];
                array_push($pending_request_for_non_fct_ros, $temp);
            }

            return $pending_request_for_non_fct_ros;
        }
    }

    public function makeFctAndNonFctInOneArray($fctArray, $nonFctArray)
    {
        $data = array();
        if (count($fctArray) > 0) {
            foreach ($fctArray as $val) {
                $tmp = array();
                $tmp['id'] = $val['id'];
                $tmp['ext_ro_id'] = $val['ext_ro_id'];
                $tmp['cancel_type'] = $val['cancel_type'];
                $tmp['cancel_status'] = $val['cancel_status'];
                $tmp['reason'] = $val['reason'];
                $tmp['cust_ro'] = $val['cust_ro'];
                $tmp['internal_ro'] = $val['internal_ro'];
                $tmp['market'] = $val['market'];
                $tmp['non_fct'] = '0';
                $tmp['requested_date'] = $val['requested_date'];
                $tmp['show_approve'] = $val['show_approve'];
                $tmp['approval_level'] = $val['approval_level'];

                array_push($data, $tmp);
            }
        }

        if (count($nonFctArray) > 0) {
            foreach ($nonFctArray as $val) {
                $tmp = array();
                $tmp['id'] = $val['id'];
                $tmp['ext_ro_id'] = $val['ext_ro_id'];
                $tmp['cancel_type'] = $val['cancel_type'];
                $tmp['cancel_status'] = $val['cancel_status'];
                $tmp['reason'] = $val['reason'];
                $tmp['cust_ro'] = $val['cust_ro'];
                $tmp['internal_ro'] = $val['internal_ro'];
                $tmp['market'] = $val['market'];
                $tmp['non_fct'] = '1';
                $tmp['requested_date'] = $val['requested_date'];
                $tmp['show_approve'] = '1';
                $tmp['approval_level'] = $val['approval_level'];

                array_push($data, $tmp);
            }
        }


        return $data;
    }

    public function sort_by_date($data)
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

        $orderby = "requested_date"; //change this to whatever key you want from the array

        array_multisort($sortArray[$orderby], SORT_DESC, $data);

        return $data;
    }

    public function ros_with_actioned_requests()
    {

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $profile_id = $logged_in[0]['profile_id'];
        $profileData = $this->user_model->get_discount_for_profile(array('profile_id' => $profile_id));
        $profileDiscountPercent = $profileData[0]['discount'];
        $profileNetContribuition = unserialize(PROFILE_NETCONTRIBUITION_PERCENT);
        $profileNetContribuitionDiscount = $profileNetContribuition[$profile_id];

        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$profile_id];

        if ($profile_id == 1 || $profile_id == 2) {
            $query = '';
            switch ($is_test_user) {
                case 2 :
                    $query = $this->db->query("select rcex.id,rcex.ext_ro_id, rcex.cancel_type, rcex.reason,rcex.cancel_ro_by_admin,rcex.approval_level,rcex.date_of_submission,raex.cust_ro, raex.internal_ro FROM ro_cancel_external_ro rcex
                  JOIN ro_am_external_ro raex ON rcex.ext_ro_id = raex.id where raex.id not in (select ro_id from ro_linked_advance_ro) and rcex.cancel_ro_by_admin IN (1,2,3) and rcex.approval_level >='$positionLevelForUser' order by rcex.date_of_submission desc");
                    break;
                default:
                    $query = $this->db->query("select rcex.id,rcex.ext_ro_id, rcex.cancel_type, rcex.reason,rcex.cancel_ro_by_admin,rcex.approval_level,rcex.date_of_submission,raex.cust_ro, raex.internal_ro FROM ro_cancel_external_ro rcex
                  JOIN ro_am_external_ro raex ON rcex.ext_ro_id = raex.id where raex.test_user_creation='$is_test_user' and rcex.cancel_ro_by_admin IN (1,2,3) and rcex.approval_level >='$positionLevelForUser' order by rcex.date_of_submission desc");
                    break;
            }
        } else {
            if ($profile_id == 11) {
                $userId = $logged_in[0]['user_id'];
                $belowUserIds = $this->getBelowGroupManagerUserId($userId);
            }

            $query = '';
            switch ($is_test_user) {
                case 2 :
                    $queryString = "select rcex.id,rcex.ext_ro_id, rcex.cancel_type, rcex.reason,rcex.cancel_ro_by_admin,rcex.approval_level,
                    rcex.date_of_submission,raex.cust_ro, raex.internal_ro FROM ro_cancel_external_ro rcex
                    Inner JOIN ro_am_external_ro raex ON rcex.ext_ro_id = raex.id where raex.id not in (select ro_id from ro_linked_advance_ro) and 
                  rcex.cancel_ro_by_admin IN (1,2,3) and rcex.cancel_type ='submit_ro_approval' and rcex.approval_level >='$positionLevelForUser' ";
                    if ($profile_id == 11) {
                        $queryString = $queryString . " and raex.user_id in ($belowUserIds) ";
                    }
                    $queryString = $queryString . " order by rcex.date_of_submission desc";
                    $query = $this->db->query($queryString);
                    break;
                default:
                    $queryString = "select rcex.id,rcex.ext_ro_id, rcex.cancel_type, rcex.reason,rcex.cancel_ro_by_admin,
                    rcex.approval_level,rcex.date_of_submission,raex.cust_ro, raex.internal_ro FROM ro_cancel_external_ro rcex
                  JOIN ro_am_external_ro raex ON rcex.ext_ro_id = raex.id where raex.test_user_creation='$is_test_user' and "
                        . "rcex.cancel_ro_by_admin IN (1,2,3) and rcex.cancel_type ='submit_ro_approval' and rcex.approval_level >='$positionLevelForUser' ";
                    if ($profile_id == 11) {
                        $queryString = $queryString . " and raex.user_id in ($belowUserIds) ";
                    }
                    $queryString = $queryString . " order by rcex.date_of_submission desc";
                    $query = $this->db->query($queryString);
                    break;
            }
        }


        $actioned_request_for_ros = array();
        if ($query->num_rows() > 0) {
            $actioned_request_res = $query->result("array");

            foreach ($actioned_request_res as $req) {
                $temp = array();
                $temp['id'] = $req['id'];
                $temp['ext_ro_id'] = $req['ext_ro_id'];
                $temp['cancel_type'] = $req['cancel_type'];
                $temp['reason'] = $req['reason'];
                $temp['cust_ro'] = $req['cust_ro'];
                $temp['internal_ro'] = $req['internal_ro'];
                $temp['requested_date'] = $req['date_of_submission'];
                $temp['approval_level'] = $req['approval_level'];

                if ($req['cancel_ro_by_admin'] == 1) {
                    $temp['cancel_status'] = 1;
                } elseif ($req['cancel_ro_by_admin'] == 2) {
                    $temp['cancel_status'] = 2;
                } elseif ($req['cancel_ro_by_admin'] == 3) {
                    $temp['cancel_status'] = 3;
                }

                if ($req['cancel_type'] == 'cancel_market') {

                    $mkt_query = "select * from `ro_market_price_tmp` where am_ro_id = '" . $req['ext_ro_id'] . "' and cancel_id = '" . $req['id'] . "' and is_cancelled = '1'";
                    $pending_mkt_res = $this->db->query($mkt_query);
                    $pending_mkt_result = $pending_mkt_res->result("array");

                    $markets_temp = array();
                    foreach ($pending_mkt_result as $market) {
                        array_push($markets_temp, $market['market']);
                    }
                    $markets_for_ro = implode(",", $markets_temp);

                    $temp['market'] = $markets_for_ro;
                }
                array_push($actioned_request_for_ros, $temp);
            }
        }

        $non_fct = $this->non_fct_ros_with_actioned_requests();

        $data = $this->makeFctAndNonFctInOneArray($actioned_request_for_ros, $non_fct);
        $data = $this->sort_by_date($data);
        return $data;
    }

    public function non_fct_ros_with_actioned_requests()
    {

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $profile_id = $logged_in[0]['profile_id'];
        $profileData = $this->user_model->get_discount_for_profile(array('profile_id' => $profile_id));
        $profileDiscountPercent = $profileData[0]['discount'];
        $profileNetContribuition = unserialize(PROFILE_NETCONTRIBUITION_PERCENT);
        $profileNetContribuitionDiscount = $profileNetContribuition[$profile_id];

        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$profile_id];

        $query = $this->db->query("select  rnfreq.id, rnfreq.non_fct_ro_id, rnfreq.request_type, rnfreq.approval_level, rnfreq.approved_by,rnfreq.requested_date,rnf.customer_ro_number, rnf.internal_ro_number
                                    FROM ro_non_fct_request rnfreq
                                    JOIN ro_non_fct_ro rnf ON rnfreq.non_fct_ro_id = rnf.id
                                    WHERE rnfreq.approved_by = 1
                                    AND rnfreq.approval_level = '$positionLevelForUser'
                                    order by rnfreq.requested_date desc");

        if ($query->num_rows() > 0) {
            $pending_request_res = $query->result("array");
            $pending_request_for_non_fct_ros = array();
            foreach ($pending_request_res as $req) {
                $temp = array();
                $temp['id'] = $req['id'];
                $temp['ext_ro_id'] = $req['non_fct_ro_id'];
                $temp['cancel_type'] = $req['request_type'];
                $temp['cancel_status'] = $req['approved_by'];
                $temp['reason'] = 'None';
                $temp['cust_ro'] = $req['customer_ro_number'];
                $temp['internal_ro'] = $req['internal_ro_number'];
                $temp['requested_date'] = $req['requested_date'];
                $temp['approval_level'] = $req['approval_level'];
                array_push($pending_request_for_non_fct_ros, $temp);
            }

            return $pending_request_for_non_fct_ros;
        }
    }

    public function approve_request_for_cancellation($order_id, $status, $cancel_type, $cancel_id)
    {
        $query = "UPDATE `ro_cancel_external_ro` SET `cancel_ro_by_admin` = '" . $status . "' WHERE ext_ro_id = '" . $order_id . "' and cancel_type = '" . $cancel_type . "' and id='$cancel_id' ";
        $this->db->query($query);
    }

    public function reject_request_for_cancellation($order_id, $status, $cancel_type, $cancel_id, $bh_reason)
    {
        $query = "UPDATE `ro_cancel_external_ro` SET `cancel_ro_by_admin` = '" . $status . "', `bh_reason` = '" . $bh_reason . "' WHERE ext_ro_id = '" . $order_id . "' and cancel_type = '" . $cancel_type . "' and id='$cancel_id' ";
        $this->db->query($query);
    }

    public function get_maxm_level_from_price_tmp($order_id)
    {
        $this->db->select_max('requested_number');
        $result = $this->db->get_where('ro_market_price_tmp', array('am_ro_id' => $order_id));

        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function add_new_order($data)
    {
        return $this->insert_helper('ro_order', $data);
    }

    public function insert_helper($table_name, $data_array)
    {
        $this->db->insert($table_name, $data_array);
	log_message('info','in ro_manager@approval_request |query_printing - '.$this->db->last_query());
        return $this->db->insert_id();

    }

    public function add_order_ro_details($data)
    {
        $this->insert_helper('ro_ext_ro', $data);
    }

    public function add_amount($data)
    {
        $this->insert_helper('ro_approved_networks', $data);
    }

    public function add_amount_v1($data)
    {
        $internal_ro_number = $data['internal_ro_number'];
        $client_name = $data['client_name'];
        $customer_id = $data['customer_id'];
        $customer_name = $data['customer_name'];
        $customer_share = $data['customer_share'];
        $customer_location = $data['customer_location'];
        $tv_channel_id = $data['tv_channel_id'];
        $channel_name = $data['channel_name'];
        $channel_approval_status = $data['channel_approval_status'];
        $pdf_generation_status = $data['pdf_generation_status'];
        $billing_name = $data['billing_name'];
        $revision_no = $data['revision_no'];
        $query = "INSERT INTO ro_approved_networks(id,internal_ro_number,client_name,customer_id,customer_name,customer_share,customer_location,tv_channel_id,channel_name,channel_approval_status,pdf_generation_status,billing_name,revision_no) values (NULL,'$internal_ro_number','$client_name','$customer_id','$customer_name','$customer_share','$customer_location','$tv_channel_id','$channel_name','$channel_approval_status','$pdf_generation_status','$billing_name','$revision_no')";
        $this->db->query($query);
        //$this->insert_helper('ro_approved_networks',$data);
    }

    public function add_approved_channels($data)
    {
        foreach ($data as $summary => $channels_summary)
            $this->insert_helper('sv_approved_networks', $channels_summary);
    }

    public function make_email_copy($data)
    {
        $this->insert_helper('ro_email_copy', $data);
    }

    public function set_approved_status($status, $network_data)
    {
        $where = array('internal_ro_number' => $network_data['internal_ro'], 'customer_id' => $network_data['network_id']);
        $this->db->where($where);
        $this->db->update('sv_networks_amount_info', $status);
    }

    public function add_ro_amount($data)
    {
        $this->insert_helper('ro_amount', $data);
    }

    public function save_campaigns($data)
    {

        $this->insert_helper('ro_approved_campaigns', $data);
    }

    public function get_all_campaigns()
    {

        $query = "select * from ro_approved_campaigns where approval_status=0";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_data_for_confirmation_customer($user_id)
    {
        $query = "select c.confirmation_id,c.order_id from ro_amount_confirmation as c,ro_amount_confirmation_customer as cc where c.confirmation_id=cc.confirmation_id and cc.customer_id='$user_id' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function delete_from_confirmation_customer($confirmation_id, $user_id)
    {
        $this->db->where(array('confirmation_id' => $confirmation_id, 'customer_id' => $user_id));
        $this->db->delete('ro_amount_confirmation_customer');
    }

    public function add_ro_expenses($data)
    {
        $query = "update ro_amount set agency_rebate = " . $data['agency_rebate'] . ",agency_rebate_on = '" . $data['agency_rebate_on'] . "',marketing_promotion_amount = " . $data['marketing_promotion_amount'] . ",field_activation_amount = " . $data['field_activation_amount'] . ",sales_commissions_amount =" . $data['sales_commissions_amount'] . ",creative_services_amount =" . $data['creative_services_amount'] . ",other_expenses_amount = " . $data['other_expenses_amount'] . ",ro_valid_field =" . $data['ro_valid_field'] . " where customer_ro_number = '" . $data['customer_ro_number'] . "'";
        $res = $this->db->query($query);

    }

    public function ro_approval_request_status($data)
    {
        $this->insert_helper('ro_orders', $data);
    }

    public function add_network_ro_report_details($data)
    {
        $this->insert_helper('ro_network_ro_report_details', $data);
    }

    public function add_external_ro_report_details($data)
    {
        $this->insert_helper('ro_external_ro_report_details', $data);
    }

    public function saving_data_into_db_for_confirmation($data)
    {
        $logged_in_user = $this->session->userdata("logged_in_user");
        $user_id = $logged_in_user[0]['user_id'];

        $order_id = $data['order_id'];
        $result = $this->get_data_for_confirmation($order_id);

        if (count($result) > 0) {
            $confirmation_id = $result[0]['confirmation_id'];

            $this->db->where('order_id', $order_id);
            $this->db->delete('ro_amount_confirmation');

            $this->db->where(array('confirmation_id' => $confirmation_id, 'customer_id' => $user_id));
            $this->db->delete('ro_amount_confirmation_customer');
        }
        $id = $this->insert_helper('ro_amount_confirmation', $data);
        //store in ro_amount_confirmation_customer
        $this->insert_helper('ro_amount_confirmation_customer', array('confirmation_id' => $id, 'customer_id' => $user_id));
    }

    public function get_data_for_confirmation($order_id)
    {
        $query = "select * from ro_amount_confirmation where order_id='$order_id' ";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function delete_from_confirmation($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->delete('ro_amount_confirmation');
    }

    public function get_market_name($cid)
    {
        $query = "select sw_market_name from sv_sw_market as ssm,sv_customer_sw_location as csl where ssm.id =csl.sw_market_id and customer_id = '$cid' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $market_name = $res->result("array");
            return $market_name[0]['sw_market_name'];
        } else {
            $query = "select state from sv_customer_demographics where customer_id = '$cid' ";
            $res = $this->db->query($query);

            if ($res->num_rows() > 0) {
                $market_name = $res->result("array");
                return $market_name[0]['state'];
            } else {
                return 'N.A.';
            }
        }
    }

    public function update_billing_name($cid, $user_data)
    {
        $where = array('customer_id' => $cid);
        $this->db->where($where);
        $this->db->update('sv_customer', $user_data);
    }

    public function update_customer_email($cid, $user_data)
    {
        $where = array('customer_id' => $cid);
        $this->db->where($where);
        $this->db->update('sv_customer', $user_data);

        $data['email_id'] = $user_data['customer_email'];
        $where = array('customer_id' => $cid);
        $this->db->where($where);
        $this->db->update('sv_customer_contact_details', $data);
    }

    //added by mani:18.10.2013,storing data
    public function store_mail_data($data)
    {
        $subject = $data['subject'];
        $date = $data['pdf_generation_date'];
        $query = "select * from ro_mail_data where subject='$subject' and pdf_generation_date='$date' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return 0;
        } else {
            $this->insert_helper('ro_mail_data', $data);
            return $this->db->insert_id();
        }

    }

    public function get_mail_data()
    {
        $query = "select * from ro_mail_data where mail_sent=0 Limit 1";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function getSentMailDataForInternalRo($internalRoNumber)
    {
        $query = "select * from ro_mail_data where subject LIKE '$internalRoNumber%' and mail_sent= 1";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_timeband_from_config($cid)
    {
        $query = "select TIMEBAND from ro_customer_time_entry where cid='$cid' ";
        $res = $this->db->query($query);
        $data = array();

        if ($res->num_rows() > 0) {
            $value = $res->result("array");
            $value = $value[0];

            $data['timeband'] = $value['TIMEBAND'];
        } else {
            $data['timeband'] = CID_TIMEBAND;
        }
        return $data;
    }

    public function add_file_location($file_location, $where)
    {
        $channel_id = $where['channel_id'];
        $internal_ro_number = $where['internal_ro_number'];
        $query = "select * from ro_channel_file_location where channel_id='$channel_id' and internal_ro_number='$internal_ro_number' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $data = array('file_location' => $file_location);
            $this->db->update('ro_channel_file_location', $data, $where);
        } else {
            $data = array(
                'channel_id' => $channel_id,
                'internal_ro_number' => $internal_ro_number,
                'file_location' => $file_location
            );

            $this->db->insert('ro_channel_file_location', $data);
        }
    }

    public function get_download_link($internal_ro_number, $cid)
    {
        $query = "select fl.file_location,tc.channel_name,tc.tv_channel_id as channel_id from ro_channel_file_location as fl,sv_tv_channel as tc where internal_ro_number='$internal_ro_number' and status='approved' and tc.tv_channel_id=fl.channel_id and tc.enterprise_id='$cid' ";

        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_file_location($channel_id, $internal_ro_number)
    {
        $query = "select file_location from ro_channel_file_location where internal_ro_number='$internal_ro_number' and  	channel_id='$channel_id' ";

        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_ro_details_for_markets($market_name)
    {
        $query = "select amr.cust_ro,amr.internal_ro,amr.agency,amr.client,amr.camp_start_date,amr.camp_end_date, amc.chk_complete,amc.amnt_collected from ro_am_external_ro as amr,ro_am_invoice_collection as amc where amr.cust_ro =amc.ext_ro and amr.market Like '%$market_name%' and amr.cust_ro not in (select customer_ro from ro_network_remittance) order by amr.cust_ro";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    //Nitish 9.1.2015: commented to avoid conflicts for Cluster implementation
    /*public function get_all_markets_for_channels($channel_ids) {
		$query = "select sm.sw_market_name,tcm.tv_channel_id from sv_sw_market as sm,sv_tam_channel_market as tcm,sv_sw_tam_market as swtm where tcm.tv_channel_id in ($channel_ids) and swtm.tam_market_id = tcm.tam_market_id and sm.id=swtm.sw_market_id group by tcm.tv_channel_id" ;

		$res = $this->db->query($query);
		if($res->num_rows() > 0) {
				return $res->result("array");
		}
		return array();
	}*/

    public function get_ro_details_for_network_ro($network_ro)
    {
        $query = "select amr.cust_ro,amr.internal_ro,amc.cheque_no,amc.collection_date,amc.amnt_collected,amc.comment from ro_am_external_ro as amr,ro_am_invoice_collection as amc where amr.cust_ro =amc.ext_ro and amr.cust_ro='$network_ro' order by amc.id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function insert_into_network_remittance($user_data)
    {
        $this->db->insert("ro_network_remittance", $user_data);
    }

    public function get_ro_details_for_network($network_id)
    {
        $query = "select amr.cust_ro,amr.internal_ro,amr.agency,amr.client,amr.camp_start_date,amr.camp_end_date, amc.chk_complete,amc.amnt_collected from ro_am_external_ro as amr,ro_am_invoice_collection as amc, ro_approved_networks as an where amr.cust_ro =amc.ext_ro and an.customer_id='$network_id' group by an.internal_ro_number order by amr.cust_ro ";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    function get_network_remittance()
    {
        $page = $this->input->post('page');
        $per_page = $this->input->post('rp');

        if ($page > 1) {
            $offset = ($page - 1) * $per_page;
        } else {
            $offset = 0;
        }

        $record = $this->_get_network_remittance($page, $offset, $per_page);
        $record = $this->convert_data_into_structure($record);
        $total_row = count($record);

        return $this->build_record_json($record, $total_row, $page);
    }

    public function _get_network_remittance($page = 1, $offset = NULL, $limit = NULL)
    {

        $where = "";
        if ($this->input->post('from')) {
            $start_date = date('Y-m-d', strtotime($this->input->post('from')));
            $where = $where . " and ((amr.camp_start_date <='$start_date' and amr.camp_end_date >='$start_date') or (amr.camp_start_date >='$start_date') )";
        }

        if ($this->input->post('to')) {
            $end_date = date('Y-m-d', strtotime($this->input->post('to')));
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $where = $where . " and ((amr.camp_end_date <='$end_date') or (amr.camp_start_date <='$end_date' and amr.camp_end_date >='$end_date'))";
        }
        /*
		if($this->input->post('network_id') && $this->input->post('network_id') !='all' )
		{
			$network_id = $this->input->post('network_id');
			$where = $where." and c.customer_id='$network_id' ";
		} */


        $where = $where . " order by amr.cust_ro";

        //$query = "select amr.cust_ro,amr.internal_ro,amr.agency,amr.client,amr.camp_start_date,amr.camp_end_date, amc.chk_complete,amc.amnt_collected from ro_am_external_ro as amr,ro_am_invoice_collection as amc, ro_approved_networks as an where amr.cust_ro =amc.ext_ro " ;
        $query = "select amr.cust_ro,amr.internal_ro,amr.agency,amr.client,amr.camp_start_date,amr.camp_end_date,amr.gross, amc.chk_complete,amc.amnt_collected from ro_am_external_ro as amr,ro_am_invoice_collection as amc where amr.cust_ro =amc.ext_ro";

        $query = $query . "  $where";

        if (isset($limit) && isset($offset)) {
            $query = $query . " limit " . $offset . "," . $limit;
        }
        $res = $this->db->query($query);
        return $res->result("array");
    }

    public function convert_data_into_structure($data_values)
    {
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
                $data[$customer_ro]['ro_amount'] = $val['gross'];

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
        }
        return $data;
    }

    function build_record_json($records, $total_row, $page)
    {
        $json = Array();
        $items = Array();
        foreach ($records as $record) {
            $impressions_data = $this->get_booked_vs_scheduled_impression_for_ros($record['internal_ro']);
            $payment_detail = $this->get_nw_payment_for_internal_ros_v1($record['internal_ro']);
            $payment_details = $this->get_payment_details_group_by_nw_ro($payment_detail);

            if (count($payment_details) > 0) {
                foreach ($payment_details as $nw_detail) {
                    $items[] = array(
                        'id' => $record['customer_ro'],
                        'cell' => array(
                            $record['customer_ro'],
                            $record['internal_ro'],
                            $nw_detail['network_ro'],
                            $record['client'],
                            $record['agency'],
                            date('d-M-Y', strtotime($record['camp_start_date'])),
                            date('d-M-Y', strtotime($record['camp_end_date'])),
                            $impressions_data['booked_impression'],
                            $impressions_data['scheduled_impression'],
                            'content_duration',
                            $record['amount_collected'],
                            $record['client_payment_received'],
                            $record['ro_amount'],
                            $nw_detail['amount_paid'],
                            $nw_detail['payment_paid']
                        )
                    );
                }
            } else {
                $items[] = array(
                    'id' => $record['customer_ro'],
                    'cell' => array(
                        'network_ro',
                        'network_name',
                        $record['customer_ro'],
                        $record['client'],
                        $record['agency'],
                        date('d-M-Y', strtotime($record['camp_start_date'])),
                        date('d-M-Y', strtotime($record['camp_end_date'])),
                        $impressions_data['booked_impression'],
                        $impressions_data['scheduled_impression'],
                        'content_duration',
                        $record['amount_collected'],
                        $record['client_payment_received'],
                        $record['ro_amount'],
                        'network_ro_amount',
                        'swaves_share_amount',
                        'net_amount_payable',
                        'service_tax',
                        'nw_ro_release_date',
                        'nw_ro_paid_amount',
                        'nw_ro_fully_paid'
                    )
                );
            }


        }

        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;

        return json_encode($json);
    }

    public function get_booked_vs_scheduled_impression_for_ros($internal_ro_number)
    {

        $internal_ro_number = base64_encode($internal_ro_number);
//        $url = "http://tv.mediagrid100.surewaves.com/surewaves/apis/get_day_wise_actual_scheduled_impression.php?internal_ro_number=" . $internal_ro_number;
        $url = SERVER_NAME."/surewaves/apis/get_day_wise_actual_scheduled_impression.php?internal_ro_number=" . $internal_ro_number;

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
            $total_booked_impression = 0;
            $total_scheduled_impression = 0;
            foreach ($result['mesg'] as $msg) {
                foreach ($msg['dates'] as $dates) {
                    $total_booked_impression += $dates['booked_impression'];
                    $total_scheduled_impression += $dates['scheduled_impression'];
                }
            }
            $data['booked_impression'] = $total_booked_impression;
            $data['scheduled_impression'] = $total_scheduled_impression;
        }
        //echo print_r($data,true);
        return $data;
    }

    public function get_nw_payment_for_internal_ros_v1($internal_ro_number)
    {
        if ($this->input->post('network_id') && $this->input->post('network_id') != 'all') {
            $network_id = $this->input->post('network_id');
            $where = " and c.customer_id='$network_id' ";
        }

        $query = "select * from ro_network_payment as rnp,sv_customer as c where internal_ro_number='$internal_ro_number' and c.customer_name=rnp.network_name ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_payment_details_group_by_nw_ro($data_values)
    {
        $data = array();
        foreach ($data_values as $val) {
            $Network_ro_number = trim($val['Network_ro_number']);
            if (!isset($data[$Network_ro_number])) {
                $data[$Network_ro_number] = array();

                $data[$Network_ro_number]['network_ro'] = $val['Network_ro_number'];
                $data[$Network_ro_number]['network_name'] = $val['network_name'];

                if ($val['payment_paid'] == '0') {
                    $data[$Network_ro_number]['payment_paid'] = 'No';
                } else if ($val['payment_paid'] == '1') {
                    $data[$Network_ro_number]['payment_paid'] = 'Yes';
                }
                $data[$Network_ro_number]['amount_paid'] = $val['amount_paid'];
            } else {
                if ($Network_ro_number == trim($data[$Network_ro_number]['Network_ro_number'])) {
                    if ($val['payment_paid'] == '1') {
                        $data[$Network_ro_number]['payment_paid'] = 'Yes';
                    }
                    $data[$Network_ro_number]['amount_paid'] = $data[$Network_ro_number]['amount_paid'] + $val['amount_paid'];

                }
            }
        }
        return $data;
    }

    public function get_network_remittance_report()
    {
        $page = $this->input->post('page');
        $per_page = $this->input->post('rp');

        if ($page > 1) {
            $offset = ($page - 1) * $per_page;
        } else {
            $offset = 0;
        }

        $record = $this->_get_network_remittance_report($page, $offset, $per_page);
        //$total_row = count($record) ;
        $total_row = $this->_count_get_network_remittance_report();
        return $this->build_record_json_remittance_report($record, $total_row, $page);
    }

    public function _get_network_remittance_report($page = 1, $offset = NULL, $limit = NULL)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $where = "";
        $start_date = date('Y-m-d', strtotime($this->input->post('from')));
        $end_date = date('Y-m-d', strtotime($this->input->post('to')));

        if (strtotime($end_date) < strtotime($start_date)) {
            $end_date = $start_date;
        }
        $where = $where . " and (( Activity_Start_Date >= '$start_date' and Activity_End_Date <= '$end_date' )
or
( Activity_Start_Date <= '$start_date' and Activity_End_Date between '$start_date' and '$end_date')
or
( Activity_Start_Date between '$start_date' and '$end_date' and Activity_End_Date >= '$end_date' )
or
( Activity_Start_Date <= '$start_date' and Activity_End_Date >= '$end_date' ))";

        if ($this->input->post('network_id') && $this->input->post('network_id') != 'all') {
            $network_id = $this->input->post('network_id');
            $network_name = $this->get_network_name_for_network_id($network_id);
            $network_name = $network_name[0]['customer_name'];

            $where = $where . " and customer_name='$network_name' ";
        }
        if ($this->input->post('hid_fully_paid') == 1) {
            $where = $where . " and  (Network_RO_fully_paid = 0 or Network_RO_fully_paid is null) ";
        }

        //$where =  $where." and is_test_ro= $is_test_user " ;
        if ($is_test_user == 2) {
            $where = $where . " and internal_ro_number not in (SELECT amer.internal_ro FROM ro_linked_advance_ro AS rlar
                                                          INNER JOIN ro_am_external_ro AS amer ON amer.id = rlar.ro_id)
                                                          and is_test_ro != 1";
        } else {
            $where = $where . " and is_test_ro= $is_test_user ";
        }

        $where = $where . " order by Activity_Start_Date desc ";

        $query = "select * from Network_Remitance_Report_View";
        $query = $query . " where 1 $where";

        if (isset($limit) && isset($offset)) {
            $query = $query . " limit " . $offset . "," . $limit;
        }
        $res = $this->db->query($query);
        return $res->result("array");
    }

    public function get_network_name_for_network_id($network_id)
    {
        $query = "select customer_name from sv_customer where customer_id='$network_id' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function _count_get_network_remittance_report()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $where = "";
        $start_date = date('Y-m-d', strtotime($this->input->post('from')));
        $end_date = date('Y-m-d', strtotime($this->input->post('to')));

        if (strtotime($end_date) < strtotime($start_date)) {
            $end_date = $start_date;
        }
        $where = $where . " and (( Activity_Start_Date >= '$start_date' and Activity_End_Date <= '$end_date' )
or
( Activity_Start_Date <= '$start_date' and Activity_End_Date between '$start_date' and '$end_date')
or
( Activity_Start_Date between '$start_date' and '$end_date' and Activity_End_Date >= '$end_date' )
or
( Activity_Start_Date <= '$start_date' and Activity_End_Date >= '$end_date' ))";

        if ($this->input->post('network_id') && $this->input->post('network_id') != 'all') {
            $network_id = $this->input->post('network_id');
            $network_name = $this->get_network_name_for_network_id($network_id);
            $network_name = $network_name[0]['customer_name'];

            $where = $where . " and customer_name='$network_name' ";
        }
        if ($this->input->post('hid_fully_paid') == 1) {
            $where = $where . " and  (Network_RO_fully_paid = 0 or Network_RO_fully_paid is null) ";
        }
        //$where =  $where." and is_test_ro= $is_test_user " ;
        if ($is_test_user == 2) {
            $where = $where . " and internal_ro_number not in (SELECT amer.internal_ro FROM ro_linked_advance_ro AS rlar
                                                      INNER JOIN ro_am_external_ro AS amer ON amer.id = rlar.ro_id)
                                                      and is_test_ro != 1";
        } else {
            $where = $where . " and is_test_ro= $is_test_user ";
        }

        $where = $where . " order by Activity_Start_Date desc ";

        $query = "select * from Network_Remitance_Report_View";
        $query = $query . " where 1 $where";

        $res = $this->db->query($query);
        $result = $res->result("array");
        return (count($result));
    }

    /* this function is dependent on _get_network_remittance()
		if the query in above method changes then the same change should be refelect below
	*/

    public function build_record_json_remittance_report($records, $total_row, $page)
    {
        $json = Array();
        $items = Array();
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        foreach ($records as $record) {
            if (empty($record['Network_RO_fully_paid']) || !isset($record['Network_RO_fully_paid']) || ($record['Network_RO_fully_paid'] == 0) || (trim($record['Network_RO_fully_paid']) == '')) {
                $record['Network_RO_fully_paid'] = 'False';
            } else {
                $record['Network_RO_fully_paid'] = 'True';
            }
            if (empty($record['Full_Payment_Received']) || !isset($record['Full_Payment_Received']) || ($record['Full_Payment_Received'] == 0) || (trim($record['Full_Payment_Received']) == '')) {
                $record['Full_Payment_Received'] = 'False';
            } else {
                $record['Full_Payment_Received'] = 'True';
            }
            if (empty($record['Network_RO_Paid_Amount']) || !isset($record['Network_RO_Paid_Amount']) || ($record['Network_RO_Paid_Amount'] == 0) || (trim($record['Network_RO_Paid_Amount']) == '')) {
                $record['Network_RO_Paid_Amount'] = '0';
            }
            if (empty($record['Cancelled']) || !isset($record['Cancelled']) || ($record['Cancelled'] == 0) || (trim($record['Cancelled']) == '')) {
                $record['Cancelled'] = 'NO';
            }

            // code added for activity_seconds_qry for spot and banner add
            $start_date = date('Y-m-d', strtotime($this->input->post('from')));
            $end_date = date('Y-m-d', strtotime($this->input->post('to')));
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }

            if (strtotime($start_date) < strtotime($record['Activity_Start_Date'])) {
                $report_start_date = $record['Activity_Start_Date'];
            } else {
                $report_start_date = $start_date;
            }

            if (strtotime($end_date) > strtotime($record['Activity_End_Date'])) {
                $report_end_date = $record['Activity_End_Date'];
            } else {
                $report_end_date = $end_date;
            }

            //$activity_seconds_qry = "select sum(sva.playduration) as activity_seconds ,sva.screen_region_id from sv_advertiser_report sva join ro_approved_campaigns ra ON sva.campaign_id = ra.campaign_id join ro_network_ro_report_details rrr ON rrr.internal_ro_number = ra.internal_ro_number WHERE sva.screen_region_id IN (1,3) and rrr.network_ro_number = '".$record['network_ro_number']."' and sva.schedule_date between '$start_date' and '$end_date' group by sva.screen_region_id";

            //for extra fields in Network remittance report 18-Aug-2014
            $temp = $this->get_activity_rate($record['network_ro_number'], $start_date, $end_date, $record['customer_name'], 1);
            $scheduled_spot_amount = $temp[0];
            $scheduled_spot_seconds = $temp[1];
            $scheduled_banner_amount = $temp[2];
            $scheduled_banner_seconds = $temp[3];


            $temp = $this->get_activity_rate($record['network_ro_number'], $start_date, $end_date, $record['customer_name'], 0);
            $activity_spot_amount = $temp[0];
            $activity_spot_seconds = $temp[1];
            $activity_banner_amount = $temp[2];
            $activity_banner_seconds = $temp[3];


            //if activity spot/banner sec/amount is greater than scheduled spot/banner sec/amount
            //if user is advanced ro user then make sch vs activity equal
            if ($activity_spot_seconds > $scheduled_spot_seconds || $record['is_test_ro'] == 2) {
                $activity_spot_seconds = $scheduled_spot_seconds;
                $activity_spot_amount = $scheduled_spot_amount;
            }
            if ($activity_banner_seconds > $scheduled_banner_seconds || $record['is_test_ro'] == 2) {
                $activity_banner_seconds = $scheduled_banner_seconds;
                $activity_banner_amount = $scheduled_banner_amount;
            }


            $temp = $this->get_activity_rate($record['network_ro_number'], NULL, NULL, $record['customer_name'], 1);
            $total_scheduled_spot_amount = $temp[0];
            $total_scheduled_spot_seconds = $temp[1];
            $total_scheduled_banner_amount = $temp[2];
            $total_scheduled_banner_seconds = $temp[3];

            //end

            // find revision number and attach to nw_ro)number
            $where_data = array('internal_ro_number' => $record['internal_ro_number'], 'customer_name' => $record['customer_name']);
            $result = $this->db->get_where('ro_approved_networks', $where_data);
            if ($result->num_rows() > 0) {
                $res = $result->result("array");
                if ($res[0]['revision_no'] == 0) {
                    $nw_ro_number = $record['network_ro_number'];
                } else {
                    $nw_ro_number = $record['network_ro_number'] . '-R' . $res[0]['revision_no'];
                }
            }
            // end
            $items[] = array(
                'id' => $record['customer_ro_number'],
                'cell' => array(
                    $record['customer_name'],
                    $record['Network_RO_fully_paid'],
                    $record['Full_Payment_Received'],
                    $record['client_name'],
                    $nw_ro_number,
                    date('y-m', strtotime($record['release_date'])),
                    $record['Network_RO_Paid_Amount'],
                    $record['SureWaves_Share_Amount'],
                    $record['Network_Billing_Name'],
                    $record['customer_ro_number'],
                    $record['agency_name'],
                    $record['Activity_Start_Date'],
                    $record['Activity_End_Date'],
                    $report_start_date,
                    $report_end_date,
                    /*$record['Activity_Run_Spot_Seconds'],
                                        $record['Activity_Run_Banner_Seconds'],*/
                    $activity_spot_seconds,
                    $activity_spot_amount,
                    $activity_banner_seconds,
                    $activity_banner_amount,
                    $scheduled_spot_seconds,
                    $scheduled_spot_amount,
                    $scheduled_banner_seconds,
                    $scheduled_banner_amount,
                    $total_scheduled_spot_seconds,
                    $total_scheduled_spot_amount,
                    $total_scheduled_banner_seconds,
                    $total_scheduled_banner_amount,
                    $record['Payment_Collected_Amount'],
                    $record['External_RO_Amount'],
                    $record['Network_RO_Amount'],
                    $record['Service_Tax'],
                    $record['Cancelled']
                )
            );
        }
        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;

        return json_encode($json);
    }

    public function get_activity_rate($network_ro_number, $start_date, $end_date, $customer_name, $isScheduled)
    {
        //added for calculating activity rate for spot and banner add 18-Aug-2014
        $channel_spot_activity_rate = 0;
        $Activity_spot_Run_Seconds = 0;
        $channel_banner_activity_rate = 0;
        $Activity_banner_Run_Seconds = 0;
        if ($isScheduled == 0) {
            $activity_seconds_qry = "select sum(T.seconds) as seconds,T.channel_id,T.internal_ro_number,T.screen_region_id from
											(
											select sum(sva.playduration) as seconds,svc.channel_id,svc.internal_ro_number,
											sva.screen_region_id as screen_region_id 
											from sv_advertiser_report sva join sv_advertiser_campaign svc 
											ON sva.campaign_id = svc.campaign_id join ro_network_ro_report_details rrr
											ON rrr.internal_ro_number = svc.internal_ro_number 
											join sv_tv_channel stc ON stc.tv_channel_id = svc.channel_id
											join sv_customer sc ON sc.customer_id = stc.enterprise_id
											WHERE rrr.network_ro_number = '" . $network_ro_number . "' 
											and sva.schedule_date between '$start_date' and '$end_date'
											and sc.customer_name = '" . $customer_name . "'
											and sva.requested_url is null and svc.approved_status = 'Approved' 
											group by sva.campaign_id,sva.screen_region_id,svc.channel_id
											) T
											group by T.screen_region_id,T.channel_id";
        } else {
            $date_query = "and acs.date between '$start_date' and '$end_date'";
            if ($start_date == NULL && $end_date == NULL) {
                $date_query = "";
            }

            $activity_seconds_qry = "select sum(T.seconds) as seconds,T.channel_id,T.internal_ro_number,T.screen_region_id from
                (
                    select sum(impressions)*sva.ro_duration as seconds,sva.channel_id,sva.internal_ro_number,
												acs.screen_region_id as screen_region_id
												from  sv_advertiser_campaign sva inner join  sv_advertiser_campaign_screens_dates acs
												ON acs.campaign_id = sva.campaign_id
												join sv_tv_channel stc ON stc.tv_channel_id = sva.channel_id
												join ro_network_ro_report_details rrr ON rrr.internal_ro_number = sva.internal_ro_number
												join sv_customer sc ON sc.customer_id = stc.enterprise_id
												WHERE acs.status != 'cancelled' and sva.approved_status = 'Approved' AND
                rrr.network_ro_number =  '" . $network_ro_number . "'
                and sc.customer_name = '" . $customer_name . "' $date_query	group by sva.campaign_id,acs.screen_region_id,sva.channel_id) T
												group by T.screen_region_id,T.channel_id";
        }

        $activity_seconds_qry_res = $this->db->query($activity_seconds_qry);
        $activity_seconds_qry_result = $activity_seconds_qry_res->result("array");
        $Activity_Run_Spot_Seconds = 0;
        $Activity_Run_Banner_Seconds = 0;
        $channel_activity_rate = 0;
        $Activity_Run_Seconds = 0;
        foreach ($activity_seconds_qry_result as $activity_seconds_value) {

            $query_avg_rate = "select * from ro_approved_networks where internal_ro_number = '" . $activity_seconds_value['internal_ro_number'] . "' and tv_channel_id = " . $activity_seconds_value['channel_id'] . " ";
            $query_avg_rate_res = $this->db->query($query_avg_rate);
            $avg_rate_result = $query_avg_rate_res->result("array");

            $region_id = $activity_seconds_value['screen_region_id'];

            //calculating rate per second
            if ($region_id == 1) {
                $channel_spot_activity_rate += $activity_seconds_value['seconds'] * $avg_rate_result[0]['channel_spot_avg_rate'] / 10;
            }
            if ($region_id == 3) {
                $channel_banner_activity_rate += $activity_seconds_value['seconds'] * $avg_rate_result[0]['channel_banner_avg_rate'] / 10;
            }

            //calculating seconds
            if ($region_id == 1) {
                $Activity_spot_Run_Seconds += $activity_seconds_value['seconds'];
            }
            if ($region_id == 3) {
                $Activity_banner_Run_Seconds += $activity_seconds_value['seconds'];
            }
        }


        return array($channel_spot_activity_rate, $Activity_spot_Run_Seconds,
            $channel_banner_activity_rate, $Activity_banner_Run_Seconds);

    }

    public function get_all_external_ro()
    {
        $query = "select distinct customer_ro_number from ro_network_ro_report_details order by customer_ro_number";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function download_network_remittance_report_csv($network_id, $start_date, $end_date, $fully_paid = null, $is_test_user)
    {
        if ($is_test_user == NULL && !isset($is_test_user)) {
            $logged_in = $this->session->userdata("logged_in_user");
            $is_test_user = $logged_in[0]['is_test_user'];
        }

        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        if (strtotime($end_date) < strtotime($start_date)) {
            $end_date = $start_date;
        }

        $where = "";
        $where = $where . " and (( Activity_Start_Date >= '$start_date' and Activity_End_Date <= '$end_date' )
                            or
                            ( Activity_Start_Date <= '$start_date' and Activity_End_Date between '$start_date' and '$end_date')
                            or
                            ( Activity_Start_Date between '$start_date' and '$end_date' and Activity_End_Date >= '$end_date' )
                            or
                            ( Activity_Start_Date <= '$start_date' and Activity_End_Date >= '$end_date' ))";

        if (isset($network_id) && !empty($network_id) && ($network_id != 'all')) {
            $network_name = $this->get_network_name_for_network_id($network_id);
            $network_name = $network_name[0]['customer_name'];

            $where = $where . " and customer_name='$network_name' ";
        }

        if ($fully_paid == 1) {
            $where = $where . " and  (Network_RO_fully_paid = 0 or Network_RO_fully_paid is null) ";
        }

        //$where =  $where." and is_test_ro= $is_test_user " ;
        if ($is_test_user == 2) {
            $where = $where . " and internal_ro_number not in (SELECT amer.internal_ro FROM ro_linked_advance_ro AS rlar
                                                          INNER JOIN ro_am_external_ro AS amer ON amer.id = rlar.ro_id)
                                                          and is_test_ro != 1";
        } else {
            $where = $where . " and is_test_ro= $is_test_user ";
        }

        $where = $where . " order by Activity_Start_Date desc ";

        $query = "select * from Network_Remitance_Report_View";
        $query = $query . " where 1 $where";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function get_all_related_ros_for_customer_inernal_ro($network_name, $internal_ro)
    {
        $query = "select distinct customer_ro_number,network_ro_number from ro_network_ro_report_details where internal_ro_number='$internal_ro' and customer_name='$network_name' ";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function insert_into_network_ro_payment($user_data)
    {
        $this->insert_helper('ro_network_payment', $user_data);
    }

    public function download_network_payment_report_csv()
    {
        $query = "select * from ro_network_payment";
        $res = $this->db->query($query);
        $record = $res->result("array");
        return $this->build_network_payment_report_json($record);
    }

    function build_network_payment_report_json($records)
    {
        $json = Array();
        $items = Array();
        foreach ($records as $record) {
            $items[] = array(
                'id' => $record->id,
                'cell' => array(
                    $record['remittance_advice_number'],
                    $record['Network_ro_number'],
                    $record['external_ro_number'],
                    $record['network_name'],
                    $record['amount_paid'],
                    date('d-M-Y', strtotime($record['date'])),
                    $record['cheque_number'],
                    date('d-M-Y', strtotime($record['cheque_date'])),
                    $record['payment_paid'],
                    $record['instructions']
                )
            );
        }

        //$json['page'] = $page;
        //$json['total'] = $total_row;
        $json['rows'] = $items;
        return json_encode($json);
    }

    public function get_all_customer_detail_for_internal_ros($internal_ro_number)
    {
        $query = "select distinct(customer_id),customer_name from ro_approved_networks where internal_ro_number='$internal_ro_number' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_approved_customer_for_internal_ros($internal_ro_number, $cnames)
    {
        $cnames = "'" . implode("','", $cnames) . "'";
        $query = "select distinct(customer_id),customer_name from ro_approved_networks where internal_ro_number='$internal_ro_number' and  customer_name not in ($cnames) ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_nw_payment_for_internal_ros($internal_ro_number)
    {
        $query = "select * from ro_network_payment where internal_ro_number='$internal_ro_number' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function is_payment_done_for_internal_ros($internal_ro_number)
    {
        $query = "select * from ro_network_payment where internal_ro_number='$internal_ro_number' and payment_paid='1' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_scheduler_details()
    {
        $query = "select * from ro_user where profile_id in (3) ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function get_networks_for_internal_ro($internal_ro)
    {
        $query = "select distinct(customer_id),customer_name from ro_approved_networks where internal_ro_number='$internal_ro' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_start_end_date($internal_ro_number)
    {
        $query = "select min(start_date) as start_date,max(end_date) as end_date from sv_advertiser_campaign where internal_ro_number='$internal_ro_number' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function insert_ro_nw_sequence($data)
    {
        $this->db->insert('ro_network_sequence', $data);
    }

    public function get_ro_nw_sequence($where_data)
    {
        $this->db->select_max('sequence_no');
        $result = $this->db->get_where('ro_network_sequence', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_activity_run_seconds($start_date, $end_date, $customer_ro_number, $customer_name)
    {
        // code added for activity_seconds_qry for spot and banner add
        if (strtotime($end_date) < strtotime($start_date)) {
            $end_date = $start_date;
        }
        //$activity_seconds_qry = "select sum(sva.playduration) as activity_seconds ,sva.screen_region_id from sv_advertiser_report sva join ro_approved_campaigns ra ON sva.campaign_id = ra.campaign_id join ro_network_ro_report_details rrr ON rrr.internal_ro_number = ra.internal_ro_number WHERE sva.screen_region_id IN (1,3) and rrr.network_ro_number = '$nw_ro_number' and sva.schedule_date between '$start_date' and '$end_date' group by sva.screen_region_id";
        $activity_seconds_qry = "SELECT sum(sr.playduration) as activity_seconds, sr.screen_region_id
from sv_advertiser_report sr inner join (select distinct sdd.campaign_id from  sv_advertiser_campaign sdd inner join sv_advertiser_campaign_screens_dates sd on sdd.campaign_id = sd.campaign_id inner join sv_screen ss on ss.screen_id = sd.screen_id inner join sv_advertiser_report sr on sr.campaign_id = sdd.campaign_id where sdd.internal_ro_number = (select internal_ro from ro_am_external_ro where cust_ro = '$customer_ro_number') and ss.customer_id = (select customer_id from sv_customer where customer_name = '$customer_name')) res on sr.campaign_id = res.campaign_id where sr.screen_region_id IN (1,3) and sr.requested_url is null and
sr.schedule_date between '$start_date' and '$end_date'
group by sr.screen_region_id";
        $activity_seconds_qry_res = $this->db->query($activity_seconds_qry);
        $activity_seconds_qry_result = $activity_seconds_qry_res->result("array");
        $Activity_Run_Spot_Seconds = 0;
        $Activity_Run_Banner_Seconds = 0;
        foreach ($activity_seconds_qry_result as $activity_seconds_value) {
            if ($activity_seconds_value['screen_region_id'] == 1) {
                $Activity_Run_Spot_Seconds = $activity_seconds_value['activity_seconds'];
            }
            if ($activity_seconds_value['screen_region_id'] == 3) {
                $Activity_Run_Banner_Seconds = $activity_seconds_value['activity_seconds'];
            }
        }
        $activity_seconds = array();
        $activity_seconds['Activity_Run_Spot_Seconds'] = $Activity_Run_Spot_Seconds;
        $activity_seconds['Activity_Run_Banner_Seconds'] = $Activity_Run_Banner_Seconds;
        return $activity_seconds;
        // end
    }

    public function get_ros($date)
    {
        $this->db->order_by("cust_ro", "asc");
        $result = $this->db->get_where('ro_am_external_ro', array('camp_start_date >= ' => $date));
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function ro_details($id)
    {
        $result = $this->db->get_where('ro_am_external_ro', array('id >= ' => $id));
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_market_price($ro_id, $market)
    {
        $result = $this->db->get_where('ro_market_price', array('ro_id' => $ro_id, 'market' => $market));
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_market_price()
    {
        $ro_id = $_POST['sel_ro'];
        $gross = $_POST['txt_gross'];
        $agency_commission = $_POST['txt_agency_com'];
        $remarks = $_POST['txt_remarks'] . "_" . $_POST['additional_remark'];


        //update into ro_amount
        $this->updateAmountForRO_V1($ro_id, $gross, $agency_commission);

        foreach ($_POST['markets'] as $market => $market_ro_amount) {
            $market = trim(str_replace("_", " ", $market));
            $market_total_price = $market_ro_amount['spot'] + $market_ro_amount['banner'];
            $new_gross = $new_gross + $market_total_price;
            $updatedMarketData = array('price' => $market_total_price, 'spot_price' => $market_ro_amount['spot'], 'banner_price' => $market_ro_amount['banner']);
            $whereMarketData = array('ro_id' => $ro_id, 'market' => $market);

            $this->update_market_wise_price($updatedMarketData, $whereMarketData);
        }

        //add remarks for update
        $this->remarks_for_update_ro_amount(array('ro_id' => $ro_id, 'remark' => $remarks));
    }

    public function updateAmountForRO_V1($ro_id, $new_gross, $new_agency_commision)
    {
        $roData = $this->get_am_ro_id($ro_id);
        $internal_ro_number = $roData[0]['internal_ro'];
        $ro_amount = $roData[0]['gross'];

        $net_gross = $new_gross - $new_agency_commision;

        $ro_amount_data = array('gross' => $new_gross, 'agency_com' => $new_agency_commision, 'net_agency_com' => $net_gross, 'previous_ro_amount' => $ro_amount);

        //update into am_external_ro
        $this->am_model->update_ro_data_in_ro_ext($ro_id, $ro_amount_data);

        $ro_amnt_data = array(
            'ro_amount' => $new_gross,
            'agency_commission_amount' => $new_agency_commision
        );
        // update into ro_order
        $this->am_model->update_ro_amount($ro_amnt_data, $internal_ro_number);

        // update into external_ro_report_detail
        $this->update_into_external_ro_report_detail($internal_ro_number);
    }

    public function get_am_ro_id($id)
    {
        $query = "select cust_ro,internal_ro,gross from ro_am_external_ro where id = '$id' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
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

    public function update_market_wise_price($data, $where_data)
    {
        $this->db->where($where_data);
        $this->db->update('ro_market_price', $data);
    }

    public function remarks_for_update_ro_amount($data)
    {
        $this->db->insert('ro_price_remarks', $data);
    }

    public function updateAmountForRO($ro_id, $new_gross, $new_agency_commision)
    {
        $roData = $this->get_am_ro_id($ro_id);
        $internal_ro_number = $roData[0]['internal_ro'];

        $invoice_amount = $new_gross;

        $agency_comm_from_ext_ro = $new_agency_commision;
        $agency_commission_amount = $new_agency_commision;

        $ro_amount_val = $this->am_model->get_ro_amount_data($internal_ro_number);
        $ro_amount = $ro_amount_val[0]['ro_amount'];
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

    }

    public function update_into_am_ext_ro($data, $where_data)
    {
        $this->db->where($where_data);
        $this->db->update('ro_am_external_ro', $data);
    }

    public function update_into_ro_amount($data, $where_data)
    {
        $this->db->where($where_data);
        $this->db->update('ro_amount', $data);
    }

    public function get_channel_name($channel_id)
    {
        $this->db->select('channel_name');
        $query = $this->db->get_where('sv_tv_channel', array('tv_channel_id' => $channel_id));
        if ($query->num_rows() > 0) {
            $result = $query->result("array");
            return $result[0]['channel_name'];
        }
    }

    public function get_customer_market_detail($channel_id)
    {
        $query = "SELECT distinct c.customer_id,c.customer_name,sm.sw_market_name FROM sv_customer as c,sv_sw_market as sm,sv_customer_sw_location as csl,sv_tv_channel as tc WHERE  c.customer_id=csl.customer_id and c.customer_id=tc.enterprise_id and tc.enterprise_id=csl.customer_id and csl.sw_market_id=sm.id and tc.tv_channel_id='$channel_id' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $value = $result->result("array");
            return $value[0];
        }
    }

    public function cancel_channel_before_approval($where_data)
    {
        $this->db->select('campaign_id');
        $query = $this->db->get_where('sv_advertiser_campaign', $where_data);

        if ($query->num_rows() > 0) {
            //update campaign status with cancelled
            $update_data = array('campaign_status' => 'cancelled');
            $this->db->update('sv_advertiser_campaign', $update_data, $where_data);

            $result = $query->result("array");
            $campaign_ids = array();

            foreach ($result as $val) {
                array_push($campaign_ids, $val['campaign_id']);
            }

            $campaign_ids = implode(",", $campaign_ids);
            $query_campaign = "update sv_advertiser_campaign_screens_dates set status='cancelled' where campaign_id IN ($campaign_ids) ";
            $this->db->query($query_campaign);
        }
    }

    public function is_menu_hit()
    {
        if(!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER'])) {
            return FALSE;
        }
        $uri = $_SERVER['HTTP_REFERER'];
        $parts = explode('/', $uri);

        if(!isset($parts[5]) || empty($parts[5])) {
            return FALSE;
        }
        log_message('DEBUG','In ro_model@is_menu_hit | HTTP_REFERER = '.print_r(array($_SERVER['HTTP_REFERER'],'task' => $parts[5]),true));
        $menu = $parts[5];
        $menu = str_replace("_", " ", $menu);
        $query = "select * from ro_task where `Name` = '$menu' and Is_Menu='1' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function change_channel_file_location_status($where_data)
    {
        $status = array('status' => 'cancelled');
        $this->db->where($where_data);
        $this->db->update('ro_channel_file_location', $status);
    }

    public function remove_channel_file_location($where_data)
    {
        $this->db->where($where_data);
        $this->db->delete('ro_channel_file_location');
    }

    public function cancel_campaign_b4_approval($campaign_ids)
    {
        $query = "update sv_advertiser_campaign set campaign_status='cancelled' where campaign_id in ($campaign_ids)";
        $this->db->query($query);

        $query = "update sv_advertiser_campaign_screens_dates set status='cancelled' where campaign_id in ($campaign_ids) ";
        $this->db->query($query);
    }

    public function get_cancel_data($where_data)
    {
        $result = $this->db->get_where('ro_cancel_external_ro', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_cancel_market_data($where_data)
    {
        $result = $this->db->get_where('ro_market_price_tmp', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_total_market_price($ro_id)
    {
        $this->db->select_sum('price');
        $result = $this->db->get_where('ro_market_price', array('ro_id' => $ro_id));

        if ($result->num_rows() > 0) {
            $ro_amount = $result->result("array");
            return $ro_amount[0]['price'];
        }
        return array();
    }

    public function get_customer_for_channel($channel_ids)
    {
        $channel_ids = implode(",", $channel_ids);

        $query = "select distinct(enterprise_id) from sv_tv_channel where tv_channel_id in ($channel_ids) ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");

        }
        return array();

    }

    public function get_cancel_market_messages($id)
    {
        $query = "select * from ro_cancel_external_ro where ext_ro_id=$id order by id desc";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");

        }
        return array();
    }

    public function update_revision_number_for_nw()
    {

    }


    public function download_channel_performance_report_csv($start_d, $end_d, $is_test_user)
    {
        $count_records = $this->_count_get_campaign_performance_report($start_d, $end_d, $is_test_user);
        $total_row = array();
        $total_row = $count_records[0]['roCount'] + 1;

        $report_result = $this->_get_campaign_performance_report(0, $total_row, $start_d, $end_d, $is_test_user);
        return $report_result;
    }

    public function _count_get_campaign_performance_report($start_date, $end_date, $is_test_user)
    {
        if ($is_test_user == NULL && !isset($is_test_user)) {
            $logged_in = $this->session->userdata("logged_in_user");
            $is_test_user = $logged_in[0]['is_test_user'];
        }

        if (strtotime($end_date) < strtotime($start_date)) {
            $end_date = $start_date;
        }

        $storeProcedure = "CALL Campaign_Performance_Report(?,?,?,?,?,?)";
        $CallData = array(
            'startDate' => $start_date,
            'endDate' => $end_date,
            'startIndex' => 0,
            'endIndex' => 0,
            'isCount' => 1,
            'isTestUser' => $is_test_user
        );
        $res = $this->db->query($storeProcedure, $CallData);
        $performance_res = $res->result("array");
        $res->next_result();
        $res->free_result();


        //$res = $this->db->query($query);
        return $performance_res;
    }

    public function _get_campaign_performance_report($start_index, $end_index, $start_date, $end_date, $is_test_user)
    {
        if ($is_test_user == NULL && !isset($is_test_user)) {
            $logged_in = $this->session->userdata("logged_in_user");
            $is_test_user = $logged_in[0]['is_test_user'];
        }

        if (strtotime($end_date) < strtotime($start_date)) {
            $end_date = $start_date;
        }

        $storeProcedure = "CALL Campaign_Performance_Report(?,?,?,?,?,?)";
        $CallData = array(
            'startDate' => $start_date,
            'endDate' => $end_date,
            'startIndex' => $start_index,
            'endIndex' => $end_index,
            'isCount' => 0,
            'isTestUser' => $is_test_user
        );
        $res = $this->db->query($storeProcedure, $CallData);
        $performance_res = $res->result("array");
        $res->next_result();
        $res->free_result();

        //$res = $this->db->query($query);
        return $performance_res;
    }

    public function get_campaign_performance_report($start_d, $end_d)
    {
        $page = $this->input->post('page');
        $per_page = $this->input->post('rp');

        if ($page > 1) {
            $offset = ($page - 1) * $per_page;
        } else {
            $offset = 0;
        }

        $start_index = $offset;
        //$end_index = $offset + $per_page;
        $end_index = $per_page;

        $report_result = $this->_get_campaign_performance_report($start_index, $end_index, $start_d, $end_d, NULL);
        $record = array();
        $record = $report_result;

        $count_records = $this->_count_get_campaign_performance_report($start_d, $end_d, NULL);
        $total_row = array();
        $total_row = $count_records[0]['roCount'];

        $ch_p_report = $this->build_record_json_performance_report($record, $total_row, $page, $start_d, $end_d);

        return $ch_p_report;
    }

    public function build_record_json_performance_report($records, $total_row, $page, $start_date, $end_date)
    {
        $json = Array();
        $items = Array();
        foreach ($records as $record) {
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            if ($record['scheduledSpotSeconds'] == NULL) {
                $record['scheduledSpotSeconds'] = 0;
            }
            if ($record['playedSpotSeconds'] == NULL) {
                $record['playedSpotSeconds'] = 0;
            }
            if ($record['scheduledBannerSeconds'] == NULL) {
                $record['scheduledBannerSeconds'] = 0;
            }
            if ($record['playedBannerSeconds'] == NULL) {
                $record['playedBannerSeconds'] = 0;
            }
            $totalPayable = ($record['playedSpotSeconds'] * $record['spotRate'] / 10) +
                ($record['playedBannerSeconds'] * $record['bannerRate'] / 10);
            $items[] = array(
                'id' => $record['internalRoNumber'],
                'cell' => array(
                    $record['mso'],
                    $record['channelName'],
                    $record['networkRoNumber'],
                    $record['billingName'],
                    $record['internalRoNumber'],
                    date('Y-m-d', strtotime($record['startDT'])),
                    date('Y-m-d', strtotime($record['endDT'])),
                    $record['scheduledSpotSeconds'],
                    $record['playedSpotSeconds'],
                    $record['scheduledBannerSeconds'],
                    $record['playedBannerSeconds'],
                    $record['spotRate'],
                    $record['bannerRate'],
                    $totalPayable

                )
            );
        }
        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;

        return json_encode($json);
    }

    public function insertIntoApprovalStatus($data)
    {
        $this->db->insert('ro_cancel_external_ro', $data);
    }

    public function insert_for_pending_status($whereApprovalData, $approvalData)
    {
        $data_avail_in_db = $this->is_request_sent($whereApprovalData);

        if (count($data_avail_in_db) > 0) {
            $this->db->where($whereApprovalData);
            $this->db->update('ro_cancel_external_ro', $approvalData);
        } else {
            $this->db->insert('ro_cancel_external_ro', $approvalData);
        }
        log_message('info', 'ro_creation printing query STEP_13-' . $this->db->last_query());
    }

    public function is_request_sent($whereApprovalData)
    {
        $result = $this->db->get_where('ro_cancel_external_ro', $whereApprovalData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function update_pending_status($whereData, $data)
    {
        $this->db->where($whereData);
        $this->db->update('ro_market_price', $data);
    }

    public function update_approval_status($whereData, $data)
    {
        $this->db->where($whereData);
        $this->db->update('ro_cancel_external_ro', $data);
        log_message('info', 'ro_creation printing query STEP_15-' . $this->db->last_query());
    }

    public function get_cancellation_status($whereData)
    {
        //$data_format = array('ext_ro_id'=>$am_ro_id,'cancel_type'=>'cancel_market','cancel_ro_by_admin'=>0) ;
        $query = $this->db->get_where('ro_cancel_external_ro', $whereData);
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function get_campaign_end_date_for_ro($internal_ro)
    {
        $query = "select max(sacd.date) as date from sv_advertiser_campaign sac "
            . "inner join sv_advertiser_campaign_screens_dates sacd "
            . "on sac.campaign_id= sacd.campaign_id "
            . "where sac.internal_ro_number='$internal_ro' "
            . "and sacd.status='scheduled' and visible_in_easyro='1' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $val = $result->result("array");
            return date('Y-m-d', strtotime($val[0]['date']));
        }

        return array();
    }

    public function get_all_clusters()
    {
        $query = "select * from sv_sw_clusters order by cluster_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function sendMailForNetworkRo($emails_list, $text, $message, $file_location, $cc, $file_name, $id)
    {
        $internal_ro_number = $message['INTERNAL_RO'];
        $network_name = $message['NETWORK_NAME'];

        $revision_number = $this->mg_model->get_revision_number($internal_ro_number, $network_name);
        $revision_number = $revision_number - 1;

        $network_ro = $this->mg_model->get_nw_ro_number($internal_ro_number, $network_name);
        log_message('info','In ro_model| NetworkRoNumber => '.print_r($network_ro, True));
        if ($revision_number > 0) {
            $old_nw_ro_number = $network_ro . '-R' . $revision_number;
        } else {
            $old_nw_ro_number = $network_ro;
        }

        $subject = array('NETWORK_RO' => $old_nw_ro_number);
        $this->update_mail_sent_data(array('mail_sent' => 2, 'network_ro_number' => $network_ro), $id[0]);

        $fileLocationArr = explode(",", $file_location);
        $file_nameArr = explode(",", $file_name);

        require_once("S3.php");
        foreach ($fileLocationArr as $fileKey => $eachFileLocation) {
            $fileNameTemp = $file_nameArr[$fileKey];
            $fileNameTemp = str_replace('/', '_', $fileNameTemp);
            $s3 = new S3(AMAZON_KEY, AMAZON_VALUE);
            S3::putObject(S3::inputFile("$eachFileLocation"), NETWORK_RO_BUCKET, "$fileNameTemp", S3::ACL_PRIVATE);
            $auth_url[$fileKey] = "https://s3.amazonaws.com/" . NETWORK_RO_BUCKET . "/" . $fileNameTemp;
            if (strpos($fileNameTemp, '_cancel') !== false) {
                $user_data = array(
                    'actual_file_location' => $auth_url[$fileKey],
                    'network_ro_number' => $network_ro
                );
                log_message('info', 'network pdf mail in sendMailForNetworkRo function id is - ' . $id[$fileKey] . '--' . print_r($user_data, TRUE));
                $this->ro_model->update_mail_sent_data($user_data, $id[$fileKey]);
            }
        }
        log_message('info', 'network pdf mail in sendMailForNetworkRo function auth_url is - ' . print_r($auth_url, TRUE));


        $to_list = '';
        $to_list = implode(',', $emails_list);

        $noticedRoNetwork = $this->mg_model->isNetworkHaveNoticedChannelRo($network_name);
        if (!empty($noticedRoNetwork)) {
            $this->config->load('email_text');
            $channelIds = explode(",", $noticedRoNetwork);
            if (count($channelIds) == 1) {
                $noticeRoBody = $this->config->item('single_channel_notice_ro_body');
            } else {
                $noticeRoBody = $this->config->item('all_channels_notice_ro_body');
            }
            $noticeRoBody = str_replace('%CHANNELS%', $noticedRoNetwork, $noticeRoBody);
            $message['INSERT_NOTICE_RO'] = $noticeRoBody;
            mail_send($to_list, $text, $subject, $message, $file_location, $cc, $auth_url[0]);
        } else {
            $message['INSERT_NOTICE_RO'] = '';
            mail_send($to_list, $text, $subject, $message, $file_location, $cc, $auth_url[0]);
        }

        $user_data = array(
            'mail_sent' => 1,
            'actual_file_location' => $auth_url[0]
        );
        log_message('info', 'network pdf mail in sendMailForNetworkRo function sending data to update_mail_sent_data function is with id is ' . $id[0] . '-' . print_r($user_data, TRUE));
        $this->ro_model->update_mail_sent_data($user_data, $id[0]);

        foreach ($fileLocationArr as $fileKey => $eachFileLocation) {
            if (file_exists($eachFileLocation)) {
                unlink($eachFileLocation);
            }
        }

    }

    public function update_mail_sent_data($user_data, $id)
    {
        $this->db->update('ro_mail_data', $user_data, "id =" . $id);
    }

    function getMisNonFctReport()
    {
        $page = $this->input->post('page');
        $per_page = $this->input->post('rp');

        if ($page > 1) {
            $offset = ($page - 1) * $per_page;
        } else {
            $offset = 0;
        }

        $record = $this->getMisNonFctReportData($page, $offset, $per_page);
        $total_row = $this->getMisNonFctReportData()->num_rows();

        return $this->build_record_json_mis_non_fct($record, $total_row, $page);

    }

    function getMisNonFctReportData($page = 1, $offset = NULL, $limit = NULL)
    {
        if ($this->input->post('month_name')) {
            $postedValues = $this->input->post('month_name');
            $year_month = explode(" ", $this->input->post('month_name'));
            $month_name = $year_month[0];
            $financial_year = $year_month[1];

            $month = date("m", strtotime($postedValues));
            if ($month <= 3) {
                $financial_year = $financial_year - 1;
            }
        } else {
            $month = date("m", strtotime(date('Y-m-d')));
            $year = date("Y", strtotime(date('Y-m-d')));
            if ($month <= 3) {
                $from_year = $year - 1;
            } else {
                $from_year = $year;
            }
            $financial_year = $from_year;
            $month_name = date("F", strtotime(date('Y-m-d')));
        }

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $where = " and financial_year= '$financial_year' and month='$month_name' ";

        $query = "SELECT rnfr.customer_ro_number, rnfr.internal_ro_number, rnfr.agency, rnfr.agency_contact, rnfr.client, rnfmp.month, 
                    rnfmp.price, rnfr.agency_commision
                    FROM ro_non_fct_ro rnfr
                    INNER JOIN ro_non_fct_request rnf ON rnfr.id = rnf.non_fct_ro_id
                    INNER JOIN ro_non_fct_monthly_price rnfmp ON rnfr.id = rnfmp.non_fct_ro_id
                    Inner Join ro_master_geo_regions rmgr ON rnfr.region_id = rmgr.id
                    WHERE rnfmp.price !=0
                    AND rnf.approved_by =1 And rmgr.is_international = 0 ";
        $query = $query . "  $where ORDER BY rnfr.customer_ro_number";
        if (isset($limit) && isset($offset)) {
            $query = $query . " limit " . $offset . "," . $limit;
        }
        return $this->db->query($query);
        //return $this->db->get('ro_network_ro_report_details', $limit, $offset);
    }

    function build_record_json_mis_non_fct($record, $total_row, $page)
    {
        $json = Array();
        $items = Array();
        foreach ($record->result() as $record) {
            $items[] = array(
                'id' => $record->customer_ro_number,
                'cell' => array(
                    $record->customer_ro_number,
                    $record->internal_ro_number,
                    $record->agency,
                    $record->agency_contact,
                    $record->client,
                    $record->month,
                    $record->price,
                    $record->agency_commision
                )
            );
        }

        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;

        return json_encode($json);
    }

    function downloadMisNonFctReportData($financial_year, $month_name)
    {
        if (isset($financial_year)) {
            $year_month = $financial_year . " " . $month_name;
            $monthValue = date("m", strtotime($year_month));

            if ($monthValue <= 3) {
                $financial_year = $financial_year - 1;
            }
        } else {
            $month = date("m", strtotime(date('Y-m-d')));
            $year = date("Y", strtotime(date('Y-m-d')));
            if ($month <= 3) {
                $from_year = $year - 1;
            } else {
                $from_year = $year;
            }
            $financial_year = $from_year;
        }


        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $where = " and financial_year= '$financial_year' and month='$month_name' ";

        $query = "SELECT rnfr.customer_ro_number, rnfr.internal_ro_number, rnfr.agency, rnfr.agency_contact, rnfr.client, rnfmp.month, 
                    rnfmp.price, rnfr.agency_commision
                    FROM ro_non_fct_ro rnfr
                    INNER JOIN ro_non_fct_request rnf ON rnfr.id = rnf.non_fct_ro_id
                    INNER JOIN ro_non_fct_monthly_price rnfmp ON rnfr.id = rnfmp.non_fct_ro_id
                    Inner Join ro_master_geo_regions rmgr ON rnfr.region_id = rmgr.id
                    WHERE rnfmp.price !=0
                    AND rnf.approved_by =1 AND  rmgr.is_international = 0";

        $query = $query . "  $where ORDER BY rnfr.customer_ro_number";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();

    }

    function getMisReport()
    {
        $page = $this->input->post('page');
        $per_page = $this->input->post('rp');

        if ($page > 1) {
            $offset = ($page - 1) * $per_page;
        } else {
            $offset = 0;
        }

        $record = $this->getMisReportData($page, $offset, $per_page);
        $total_row = $this->getMisReportData()->num_rows();

        return $this->build_record_json_mis($record, $total_row, $page);

    }

    function getMisReportData($page = 1, $offset = NULL, $limit = NULL)
    {
        if ($this->input->post('month_name')) {
            $postedValues = $this->input->post('month_name');
            $year_month = explode(" ", $this->input->post('month_name'));
            $month_name = $year_month[0];
            $financial_year = $year_month[1];

            $month = date("m", strtotime($postedValues));
            if ($month <= 3) {
                $financial_year = $financial_year - 1;
            }
        } else {
            $month = date("m", strtotime(date('Y-m-d')));
            $year = date("Y", strtotime(date('Y-m-d')));
            if ($month <= 3) {
                $from_year = $year - 1;
            } else {
                $from_year = $year;
            }
            $financial_year = $from_year;
            $month_name = date("F", strtotime(date('Y-m-d')));
        }

        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $where = " and financial_year= '$financial_year' and month_name='$month_name' and is_fct= 1 ";
        if ($is_test_user != 2) {
            $query = "SELECT nro.customer_ro_number, mr.internal_ro_number, nro.network_ro_number, mr.customer_name, mr.customer_id, mr.client, 
                            nro.agency_name, nro.market,mr.start_date,mr.end_date, mr.`month_name` , nro.customer_share, mr.channel_payout,mr.`channel_id`,mr.spot_amount,mr.banner_amount, 
                            mr.`scheduled_spot_impression`,mr.`scheduled_banner_impression`,date_format(nro.release_date,'%e-%M-%Y') as release_date,mr.billing_name
                            FROM ro_mis_report mr
                            INNER JOIN ro_network_ro_report_details nro ON nro.internal_ro_number = mr.internal_ro_number
                            WHERE mr.customer_name = nro.customer_name
                            AND mr.client = nro.client_name and user_type='$is_test_user'";
        } else {
            $query = "SELECT nro.customer_ro_number, mr.internal_ro_number, nro.network_ro_number, mr.customer_name, mr.customer_id, mr.client, 
                            nro.agency_name, nro.market,mr.start_date,mr.end_date, mr.`month_name` , nro.customer_share, mr.channel_payout,mr.`channel_id`,mr.spot_amount,mr.banner_amount, 
                            mr.`scheduled_spot_impression`,mr.`scheduled_banner_impression`,nro.release_date,mr.billing_name
                            FROM ro_mis_report mr
                            INNER JOIN ro_network_ro_report_details nro ON nro.internal_ro_number = mr.internal_ro_number
                            WHERE mr.customer_name = nro.customer_name
                            AND mr.client = nro.client_name and mr.ro_id not in (select ro_id from ro_linked_advance_ro)";

            /*$query = "select distinct(re.internal_ro_number),re.customer_ro_number,re.network_ro_number,re.customer_name, "
                        . "re.client_name,re.agency_name,re.market,re.start_date,re.end_date,re.activity_months,re.gross_network_ro_amount,"
                        . "re.customer_share,re.net_amount_payable,re.release_date,an.billing_name from ro_network_ro_report_details as re,"
                        . "ro_approved_networks as an,ro_am_external_ro as aer where re.customer_name=an.customer_name and "
                        . "re.internal_ro_number=an.internal_ro_number and re.internal_ro_number=aer.internal_ro and "
                        . "an.internal_ro_number=aer.internal_ro and aer.id not in (select ro_id from ro_linked_advance_ro) "; */
        }

        $query = $query . "  $where ORDER BY mr.customer_name";
        if (isset($limit) && isset($offset)) {
            $query = $query . " limit " . $offset . "," . $limit;
        }
        return $this->db->query($query);
        //return $this->db->get('ro_network_ro_report_details', $limit, $offset);
    }

    function build_record_json_mis($record, $total_row, $page)
    {
        $json = Array();
        $items = Array();
        foreach ($record->result() as $record) {
            // find revision number and attach to nw_ro)number
            $where_data = array('internal_ro_number' => $record->internal_ro_number, 'customer_name' => $record->customer_name);
            $result = $this->db->get_where('ro_approved_networks', $where_data);
            if ($result->num_rows() > 0) {
                $res = $result->result("array");
                if ($res[0]['revision_no'] == 0) {
                    $nw_ro_number = $record->network_ro_number;
                } else {
                    $nw_ro_number = $record->network_ro_number . '-R' . $res[0]['revision_no'];
                }
            }
            // end
            $items[] = array(
                'id' => $record->customer_ro_number,
                'cell' => array(
                    $record->customer_ro_number,
                    $record->internal_ro_number,
                    $nw_ro_number,
                    $record->customer_name,
                    $record->customer_id,
                    $record->client,
                    $record->agency_name,
                    $record->market,
                    date('d-M-Y', strtotime($record->start_date)),
                    date('d-M-Y', strtotime($record->end_date)),
                    $record->month_name,
                    $record->customer_share,
                    $record->channel_payout,
                    $record->channel_id,
                    $record->spot_amount,
                    $record->banner_amount,
                    $record->scheduled_spot_impression,
                    $record->scheduled_banner_impression,
                    $record->release_date,
                    $record->billing_name
                )
            );
        }

        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;

        return json_encode($json);
    }

    function downloadInvoiceCollectionReportData()
    {
        $query = "SELECT raer.cust_ro,raer.internal_ro,raic.invoice_no,raic.chk_complete,raic.collection_date,
                        raic.mode_of_payment,raic.cheque_no,raic.cheque_issued_by,raic.cheque_date,raic.amnt_collected,raic.tds,raic.comment
                        from ro_am_external_ro raer
                        Inner Join ro_am_invoice_collection raic on raer.id = raic.ro_id";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    function downloadMisReportData($financial_year, $month_name)
    {
        if (isset($financial_year)) {
            $year_month = $financial_year . " " . $month_name;
            $monthValue = date("m", strtotime($year_month));

            if ($monthValue <= 3) {
                $financial_year = $financial_year - 1;
            }
        } else {
            $month = date("m", strtotime(date('Y-m-d')));
            $year = date("Y", strtotime(date('Y-m-d')));
            if ($month <= 3) {
                $from_year = $year - 1;
            } else {
                $from_year = $year;
            }
            $financial_year = $from_year;
        }


        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $where = " and financial_year= '$financial_year' and month_name='$month_name' and is_fct= 1 ";

        if ($is_test_user != 2) {
            $query = "SELECT nro.customer_ro_number, mr.internal_ro_number, nro.network_ro_number, mr.customer_name, mr.customer_id, mr.client, 
                            nro.agency_name, nro.market,mr.start_date,mr.end_date, mr.`month_name` , nro.customer_share, mr.channel_payout,mr.`channel_id`,mr.spot_amount,mr.banner_amount, 
                            mr.`scheduled_spot_impression`,mr.`scheduled_banner_impression`,date_format(nro.release_date,'%e-%M-%Y') as release_date,mr.billing_name
                            FROM ro_mis_report mr
                            INNER JOIN ro_network_ro_report_details nro ON nro.internal_ro_number = mr.internal_ro_number
                            WHERE mr.customer_name = nro.customer_name
                            AND mr.client = nro.client_name and user_type='$is_test_user'";
        } else {
            $query = "SELECT nro.customer_ro_number, mr.internal_ro_number, nro.network_ro_number, mr.customer_name, mr.customer_id, mr.client, 
                            nro.agency_name, nro.market,mr.start_date,mr.end_date, mr.`month_name` , nro.customer_share, mr.channel_payout,mr.`channel_id`,mr.spot_amount,mr.banner_amount, 
                            mr.`scheduled_spot_impression`,mr.`scheduled_banner_impression`,nro.release_date,mr.billing_name
                            FROM ro_mis_report mr
                            INNER JOIN ro_network_ro_report_details nro ON nro.internal_ro_number = mr.internal_ro_number
                            WHERE mr.customer_name = nro.customer_name
                            AND mr.client = nro.client_name and mr.ro_id not in (select ro_id from ro_linked_advance_ro)";

            /*$query = "select distinct(re.internal_ro_number),re.customer_ro_number,re.network_ro_number,re.customer_name, "
                        . "re.client_name,re.agency_name,re.market,re.start_date,re.end_date,re.activity_months,re.gross_network_ro_amount,"
                        . "re.customer_share,re.net_amount_payable,re.release_date,an.billing_name from ro_network_ro_report_details as re,"
                        . "ro_approved_networks as an,ro_am_external_ro as aer where re.customer_name=an.customer_name and "
                        . "re.internal_ro_number=an.internal_ro_number and re.internal_ro_number=aer.internal_ro and "
                        . "an.internal_ro_number=aer.internal_ro and aer.id not in (select ro_id from ro_linked_advance_ro) "; */
        }

        $query = $query . "  $where ORDER BY mr.customer_name";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
        //return $this->db->get('ro_network_ro_report_details', $limit, $offset);
    }

    public function insertForMail($userData)
    {
        $this->db->insert('ro_mail', $userData);
        log_message('info', 'ro_creation query STEP_23 --' . $this->db->last_query());
    }

    public function getMailForRo($whereData)
    {
        $result = $this->db->get_where('ro_mail', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateMailForRo($userData, $whereData)
    {
        $this->db->where($whereData);
        $this->db->update('ro_mail', $userData);
    }

    public function getRoAmountForRoAndMarket($internalRo, $market)
    {
        $query = "select price from ro_market_price rmp"
            . " inner join ro_am_external_ro raer on rmp.ro_id = raer.id "
            . " where raer.internal_ro = '$internalRo' and rmp.market= '$market' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $amount = $result->result("array");
            return $amount[0]['price'];
        }
        return 0;
    }

    public function getDataForInvoiceGeneration($month, $year)
    {
        $query = "select rnfr.customer_ro_number,rnfr.internal_ro_number,rnfr.agency,rnfr.client,rnfr.financial_year,
                    rnfr.gross_ro_amount,rnfr.agency_commision,rnfmp.month,rnfmp.price from ro_non_fct_ro  as rnfr
                                    INNER JOIN ro_non_fct_request as rnfrq ON rnfr.id = rnfrq.non_fct_ro_id
                                    INNER JOIN ro_non_fct_monthly_price as rnfmp ON rnfr.id = rnfmp.non_fct_ro_id
                                    WHERE rnfr.financial_year='" . $year . "' AND rnfrq.approved_by = 1 AND
                                    rnfmp.month LIKE '%" . strtolower($month) . "%' AND rnfmp.price!=0";
        $result = $this->db->query($query);

        return $result->result("array");

    }

    public function approvalPending($internal_ro_number, $market_name)
    {
        $query = "select * from sv_advertiser_campaign sac "
            . " Inner Join sv_sw_market ssm on sac.market_id = ssm.id "
            . " where ssm.sw_market_name= '$market_name' and internal_ro_number='$internal_ro_number' and  visible_in_easyro =1 and approved_status='Not_Approved' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }
}

?>
