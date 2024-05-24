<?php

class API_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->ci = get_instance();
        $this->ci->load->helper('hash_api');
        $this->ci->load->helper("generic");
    }

    public function fetchMgChannels()
    {
        $this->db->distinct();
        $select = array('tv_channel_id', 'channel_name');
        $this->db->select($select);
        $res = $this->db->get('sv_tv_channel');
        $finalArr = $res->result("array");
        if (count($finalArr) > 0) {
            return $finalArr;
        } else {
            return array();
        }
    }

    public function updateBrChannelInfo($updateData)
    {
        $userData = array('tv_channel_id' => $updateData['mg_channel_id']);
        $this->db->where($userData);
        $res = $this->db->get('sv_tv_channel');
        $finalArr = $res->result("array");
        if (count($finalArr) > 0) {
            $userBrData = array('br_channel_id' => $updateData['br_channel_id']);
            $this->db->where($userBrData);
            $res = $this->db->get('sv_tv_channel');
            $finalBrArr = $res->result("array");
            if (count($finalBrArr) > 0) {
                $brdata = array('br_channel_id' => NULL, 'br_channel_name' => NULL, 'br_customer_id' => NULL);
                $this->db->where($userBrData);
                $res = $this->db->update('sv_tv_channel', $brdata);
            }
            $data = array('br_channel_id' => $updateData['br_channel_id'], 'br_channel_name' => $updateData['br_channel_name'], 'br_customer_id' => $updateData['br_customer_id']);
            $this->db->where('tv_channel_id', $updateData['mg_channel_id']);
            $res = $this->db->update('sv_tv_channel', $data);
            if ($res) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function manageCustomerIdUserId()
    {
        $returnArr = array();

        $timeStamp = date("d-m-Y H:i:s");
        $api['appkey'] = APPKEY;
        $api['authkey'] = apiAuth(array("timestamp" => $timeStamp, "appkey" => APPKEY), "getEasyRoCustomer");
        $api['timestamp'] = $timeStamp;

        $url = INGESTXPRESS_API . "/getEasyRoCustomer";
        $_ch = curl_init();
        curl_setopt($_ch, CURLOPT_URL, $url);
        curl_setopt($_ch, CURLOPT_POST, 1);
        curl_setopt($_ch, CURLOPT_POSTFIELDS, http_build_query(array("request" => json_encode($api))));
        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($_ch);
        curl_close($_ch);
        $br_contentArr = json_decode($result, true);
        if ($br_contentArr['status'] == 'success') {
            $this->db->select('*');
            $this->db->from("sv_br_customer");
            $this->db->order_by("br_customer_name");
            $query = $this->db->get();
            $records = $query->result_array();

            $br_customer_id = $br_contentArr['data']['customer_id'];
            $br_customer_name = $br_contentArr['data']['customer_name'];
            $br_user_id = $br_contentArr['data']['user_id'];
            $br_user_name = $br_contentArr['data']['full_name'];

            if (count($records) > 0) {
                if ($records[0]['br_customer_id'] != $br_contentArr['data']['customer_id']) {
                    $this->deleteBRCustomerINfo($records[0]['br_customer_id']);
                    $this->insertBRCustomerInfo($br_customer_id, $br_customer_name, $br_user_id, $br_user_name);
                }
            } else {
                $this->insertBRCustomerInfo($br_customer_id, $br_customer_name, $br_user_id, $br_user_name);
            }
            $returnArr['br_customer_id'] = $br_customer_id;
            $returnArr['br_user_id'] = $br_user_id;
        } else {// send the mail
            email_send('biswabijayee@surewaves.com', '', 'br_customer', '', array('ERROR' => $result));
        }
        return $returnArr;
    }

    public function deleteBRCustomerINfo($br_customer_id)
    {
        $this->db->where('br_customer_id', $br_customer_id);
        $this->db->delete('sv_br_customer');
    }

    public function insertBRCustomerInfo($br_customer_id = '', $br_customer_name = '', $br_user_id = '', $br_user_name = '')
    {

        $data['br_customer_id'] = $br_customer_id;
        $data['br_customer_name'] = $br_customer_name;
        $data['br_user_id'] = $br_user_id;
        $data['br_user_name'] = $br_user_name;
        $ret = $this->db->insert('sv_br_customer', $data);

    }

    public function getLangaugeIds($br_customer_id, $br_user_id)
    {
        $timeStamp = date("d-m-Y H:i:s");
        $api['appkey'] = APPKEY;
        $api['authkey'] = apiAuth(array("timestamp" => $timeStamp, "appkey" => APPKEY), "LanguageList");
        $api['timestamp'] = $timeStamp;
        $url = INGESTXPRESS_API . "/LanguageList";
        $_ch = curl_init();
        curl_setopt($_ch, CURLOPT_URL, $url);
        curl_setopt($_ch, CURLOPT_POST, 1);
        curl_setopt($_ch, CURLOPT_POSTFIELDS, http_build_query(array("request" => json_encode($api))));
        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($_ch);
        curl_close($_ch);
        $br_langaugeArr = json_decode($result, true);
        if ($br_langaugeArr['status'] == 'success') {
            return $br_langaugeArr['data'];
        } else {
            return array();
        }
    }

    /**
     * Function to update the AD Log data
     * @param  $input_values
     */
    public function updatePOPFromAdLog($input_values)
    {
        $ro_details = $this->_getMGBRData($input_values);
        $campaign_data = $this->_getCampaignDetails($ro_details['mg_ro_id'], $ro_details['mg_channel_id'], $input_values['start_time']);
        $played_duration = $this->_getPlayedDuration($input_values['start_time'], $input_values['end_time']);
        $insert_data = array("screen_id" => $campaign_data['screen_id'], "content_id" => $ro_details['mg_content_id'], "playduration" => $played_duration, "start_time" => $input_values['start_time'], "end_time" => $input_values['end_time'], "schedule_date" => $input_values['detected_date'], "content_played" => "yes", "play_completion" => "yes", "customer_id" => $campaign_data['enterprise_id'], "advertiser_id" => $campaign_data['advertiser_id'], "campaign_id" => $campaign_data['campaign_id'], "create_datetime" => date('Y-m-d H:i:s'), "screen_region_id" => $campaign_data['selected_region_id']);
        $this->db->insert("sv_advertiser_report", $insert_data);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            //log_message('error', 'Could not insert sms into database');
            //log_message('error', $this->db->_error_message());
            return FALSE; // sms was not inserted correctly
        }
    }

    /**
     * Function to get the BR mapping data
     * @param $ro_data
     */
    private function _getMGBRData($ro_data)
    {
        $this->db->select("mg_content_id, mg_channel_id, mg_ro_id");
        $this->db->where("br_ro_id", $ro_data['ro_id']);
        $this->db->where("br_channel_id", $ro_data['channel_id']);
        $this->db->where("br_content_id", $ro_data['content_id']);
        $query = $this->db->get("sv_mg_br_ro_map");
        if ($query->num_rows() > 0) {
            $res = $query->result_array();
            return $res[0];
        } else
            return false;
    }

    /**
     * Get the campaign details ...
     * @param $ro_id
     * @param $channel_id
     */
    private function _getCampaignDetails($ro_id, $channel_id, $start_time)
    {
        $this->db->select("DISTINCT sdc.campaign_id, sdc.advertiser_id, stc.enterprise_id, ss.screen_id, sdc.selected_region_id", false);
        $this->db->from("sv_advertiser_campaign sdc");
        $this->db->join("ro_am_external_ro rer", "sdc.customer_ro_number = rer.cust_ro");
        $this->db->join("sv_tv_channel stc", "stc.tv_channel_id = sdc.channel_id");
        $this->db->join("sv_advertiser_campaign_screens_dates scs", "scs.campaign_id = sdc.campaign_id");
        $this->db->join("sv_screen ss", "scs.screen_id = ss.screen_id");
        $this->db->where("rer.id", $ro_id);
        $this->db->where("stc.tv_channel_id", $channel_id);
        $this->db->where("time('" . $start_time . "') between ss.start_time and ss.end_time", NULL, false);
        $query = $this->db->get();
        $result = $query->result_array();
        if ($query->num_rows() > 0)
            return $result[0];
        else
            return false;
    }

    /**
     * Get the content played duration ...
     * @param $start_time
     * @param $end_time
     */
    private function _getPlayedDuration($start_time, $end_time)
    {
        $timeFirst = strtotime($start_time);
        $timeSecond = strtotime($end_time);
        return $timeSecond - $timeFirst;
    }
}

?>
