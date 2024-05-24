<?php

class NS_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Method to Get all Markets and Channels
     * @param Filter By (String)    | To Filter by what
     * @param Filter Key (String)    | The Key to Filter By
     * @param Start Index (Integer) | Index from which the Query should return
     * @param Count (Integer)        | Total Number of Rows to be fetched
     *
     * @return Array                | Response
     */

    public function get_markets_channels($filter_by = FALSE, $filter_key = FALSE, $start_index = 0, $count = 20)
    {

        $query = $this->prepare_markets_channels_query($filter_by, $filter_key);
        $query .= " order by channel_id ";//limit $start_index, $count;";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    /**
     * Method to Get all Markets and Channels
     * @param Filter By (String)    | To Filter by what
     * @param Filter Key (String)    | The Key to Filter By
     * @param Start Index (Integer) | Index from which the Query should return
     * @param Count (Integer)        | Total Number of Rows to be fetched
     */

    private function prepare_markets_channels_query($filter_by = FALSE, $filter_key = FALSE)
    {
        $query = "select * from
		(
			select sc.customer_display_name as network_name, sc.customer_display_name ,stc.channel_name as channel_name,stc.deployment_status,stc.is_satellite_channel,stc.priority,stc.tv_channel_id as channel_id,group_concat(sm.sw_market_name) as market_name,
			is_notice, is_notice_ro, is_blocked, spot_avg, banner_avg,stc.display_name, locale, sc.revenue_sharing, sc.customer_id
			from sv_tv_channel stc
			inner join sv_customer sc on sc.customer_id = stc.enterprise_id
			inner join sv_customer_sw_location scl on scl.customer_id = sc.customer_id
			inner join sv_sw_market sm on sm.id = scl.sw_market_id WHERE  sm.is_cluster = 0 group by tv_channel_id
			) x ";

        if ($filter_by == '0')
            $filter_by = FALSE;

        if ($filter_by) {

            $filter_key = urldecode($filter_key);

            $query .= " where";
            switch ($filter_by) {
                case "market" :
                    $query .= " market_name LIKE '%$filter_key%'";
                    break;
                case "channel" :
                    $query .= " channel_name LIKE '%$filter_key%'";
                    break;
                case "network" :
                    $query .= " network_name LIKE '%$filter_key%'";
                    break;
                case 'notice' :
                    $filter_key = (($filter_key == 'true' || $filter_key == '1') ? '1' : '0');
                    $query .= " is_notice = $filter_key";
                    break;
                case 'noticero' :
                    $filter_key = (($filter_key == 'true' || $filter_key == '1') ? '1' : '0');
                    $query .= " is_notice_ro = $filter_key";
                    break;
                case 'blocked' :
                    $filter_key = (($filter_key == 'true' || $filter_key == '1') ? '1' : '0');
                    $query .= " is_blocked = $filter_key";
                    break;
            }
        }
        return $query;
    }

    /**
     * Method to Get all Networks and Channels
     * @param Filter By (String)    | To Filter by what
     * @param Filter Key (String)    | The Key to Filter By
     * @param Start Index (Integer) | Index from which the Query should return
     * @param Count (Integer)        | Total Number of Rows to be fetched
     *
     * @return Array                | Response
     */

    public function get_networks_channels($filter_by = FALSE, $filter_key = FALSE, $start_index = 0, $count = 20)
    {

        $query = $this->prepare_networks_channels_query($filter_by, $filter_key);
        $query .= " order by channel_id ";//limit $start_index, $count;";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    /**
     * Method to Get all Networks and Channels
     * @param Filter By (String)    | To Filter by what
     * @param Filter Key (String)    | The Key to Filter By
     * @param Start Index (Integer) | Index from which the Query should return
     * @param Count (Integer)        | Total Number of Rows to be fetched
     */

    private function prepare_networks_channels_query($filter_by = FALSE, $filter_key = FALSE)
    {
        return $this->prepare_markets_channels_query($filter_by, $filter_key);
    }

    /**
     * Method to Count all Markets and Channels
     * @param Filter By (String)    | To Filter by what
     * @param Filter Key (String)    | The Key to Filter By
     *
     * @return Integer | Number of Rows
     */

    public function get_count_markets_channels($filter_by = FALSE, $filter_key = FALSE)
    {
        $query = $this->prepare_markets_channels_query($filter_by, $filter_key);
        $result = $this->db->query($query);
        return $result->num_rows();
    }

    /**
     * Method to Count all Networks and Channels
     * @param Filter By (String)    | To Filter by what
     * @param Filter Key (String)    | The Key to Filter By
     *
     * @return Integer | Number of Rows
     */

    public function get_count_networks_channels($filter_by = FALSE, $filter_key = FALSE)
    {
        $query = $this->prepare_networks_channels_query($filter_by, $filter_key);
        $result = $this->db->query($query);
        return $result->num_rows();
    }

    /**
     * Method to Update Notices and ROs
     * @param All Channels                    | Array
     * @param Channels which are to be in Notice        | Array
     * @param Channels which are to be in Notice and RO    | Array
     * @param Channels which are to be Blocked        | Array
     */
    public function update_channel_notices($channels, $noticeTrueChannels, $noticeRoTrueChannels, $blockedTrueChannels, $satelliteTrueChannels, $priority)
    {

        $noticeFalseChannels = array();
        $noticeRoFalseChannels = array();
        $blockedFalseChannels = array();
        $satelliteFalseChannels = array();

        foreach ($channels as $c) {
            $found = 0;

            if (!in_array($c, $noticeTrueChannels))
                $noticeFalseChannels[] = $c;
            else
                $found++;

            if (!in_array($c, $noticeRoTrueChannels))
                $noticeRoFalseChannels[] = $c;
            else
                $found++;

            if (!in_array($c, $blockedTrueChannels))
                $blockedFalseChannels[] = $c;
            else
                $found++;

            if (!in_array($c, $satelliteTrueChannels))
                $satelliteFalseChannels[] = $c;

            if ($found > 1)
                return 'VALID_FAIL';
        }

        $this->db->trans_start();

        //Updating Notice Checked
        if (count($noticeTrueChannels) != 0) {
            $this->db->where_in('tv_channel_id', $noticeTrueChannels);
            $this->db->update('sv_tv_channel', array('is_notice' => TRUE));
        }

        //Updating Notice Un Checked
        if (count($noticeFalseChannels) != 0) {
            $this->db->where_in('tv_channel_id', $noticeFalseChannels);
            $this->db->update('sv_tv_channel', array('is_notice' => FALSE));
        }

        //Updating Notice RO Checked
        if (count($noticeRoTrueChannels) != 0) {
            $this->db->where_in('tv_channel_id', $noticeRoTrueChannels);
            $this->db->update('sv_tv_channel', array('is_notice_ro' => TRUE));
        }

        //Updating Notice RO Un Checked
        if (count($noticeRoFalseChannels) != 0) {
            $this->db->where_in('tv_channel_id', $noticeRoFalseChannels);
            $this->db->update('sv_tv_channel', array('is_notice_ro' => FALSE));
        }

        //Updating Block Checked
        if (count($blockedTrueChannels) != 0) {
            $this->db->where_in('tv_channel_id', $blockedTrueChannels);
            $this->db->update('sv_tv_channel', array('is_blocked' => TRUE));
        }

        //Updating Block Un Checked
        if (count($blockedFalseChannels) != 0) {
            $this->db->where_in('tv_channel_id', $blockedFalseChannels);
            $this->db->update('sv_tv_channel', array('is_blocked' => FALSE));
        }

        //Updating Satellite Checked
        if (count($satelliteTrueChannels) != 0) {
            $this->db->where_in('tv_channel_id', $satelliteTrueChannels);
            $this->db->update('sv_tv_channel', array('is_satellite_channel' => TRUE));
        }

        //Updating Satellite Un Checked
        if (count($satelliteFalseChannels) != 0) {
            $this->db->where_in('tv_channel_id', $satelliteFalseChannels);
            $this->db->update('sv_tv_channel', array('is_satellite_channel' => FALSE));
        }

        foreach ($priority as $priorityVal) {
            $whereArray = array();
            $explode_priority_arr = explode("_", $priorityVal);
            $whereArray[] = $explode_priority_arr[0];
            $this->db->where_in('tv_channel_id', $whereArray);
            $this->db->update('sv_tv_channel', array('priority' => $explode_priority_arr[1]));
            unset($whereArray);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return 'SUCCESS';
    }

    /**
     * Method to Update Spot and Banner Rates
     * @param array $channels | Array of All Channels to be Updated
     * @param array $spot_rates | Associative Array of Spot Ad Rates of all Channels
     * @param array $banner_rates | Associative Array of Banner Ad Rates of all Channels
     * @return boolean
     */
    public function update_channel_rates($channels, $spot_rates, $banner_rates, $newNetworkShareArray, $oldNetworkShareArray)
    {

        $this->db->trans_start();

        foreach ($channels as $c) {
            $this->db->where('tv_channel_id', $c);
            $update = array('spot_avg' => $spot_rates[$c], 'banner_avg' => $banner_rates[$c]);
            $this->db->update('sv_tv_channel', $update);

        }
        // updating cusotmer share

        foreach ($newNetworkShareArray as $networkId => $networkShare) {

            $newShare = trim($networkShare);
            $oldShare = trim($oldNetworkShareArray[$networkId]);

            if ((md5($newShare) != md5($oldShare))) {

                $this->db->where('customer_id', $networkId);
                $update = array('revenue_sharing' => $networkShare);
                $this->db->update('sv_customer', $update);

            }
        }


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Method to get all Channels between From and To
     * @param From - Channel ID (Integer)
     * @param To - Channel ID (Integer)
     *
     * @return Channel IDs between From and To (Array)
     *
     * @deprecated
     */

    /*
   private function get_channels_between($from, $to) {

       $return = array();

       $this -> db -> select('tv_channel_id');
       $this -> db -> where("tv_channel_id >= $from AND tv_channel_id <= $to");
       $query = $this -> db -> get('sv_tv_channel');

       foreach ($query -> result() as $row) {
           $return[] = $row -> tv_channel_id;
       }

       return $return;
   }
     * */
    function updateChannelDisplayNameLocalStatus($set, $channelId, $updateCustomerDisplaySet)
    {
        if ($set != '') {
            $updateQuery = " UPDATE sv_tv_channel SET $set WHERE tv_channel_id = '$channelId' ";
            $this->db->query($updateQuery);
        }
        if ($updateCustomerDisplaySet != '') {
            $updateQuery = " UPDATE sv_customer  SET $updateCustomerDisplaySet WHERE customer_id IN (select enterprise_id from sv_tv_channel where tv_channel_id = '$channelId')";
            $this->db->query($updateQuery);
        }
        return true;
    }

    function get_ping_status_for_ReachSheet($startdate, $enddate)
    {
        $query = "select distinct ch.tv_channel_id  as channel_id,ch.deployment_status from sv_customer_sw_location sl inner join sv_tv_channel ch ON ch.enterprise_id = sl.customer_id inner join sv_sw_market swm ON sl.sw_market_id = swm.id inner join sv_channel_monitor scm ON ch.tv_channel_id = scm.channel_id where swm.is_cluster = 0  AND ch.deployment_status = 'Deployed' AND client_connect_time between '" . $startdate . "' and  '" . $enddate . "' order by swm.sw_market_name ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }


    function get_ping_status($startdate, $enddate)
    {
        $query = "select distinct ch.tv_channel_id  as channel_id,deployment_status  from sv_customer_sw_location sl inner join sv_tv_channel ch ON ch.enterprise_id = sl.customer_id inner join sv_sw_market swm ON sl.sw_market_id = swm.id inner join sv_channel_monitor scm ON ch.tv_channel_id = scm.channel_id where swm.is_cluster = 0 AND client_connect_time between '" . $startdate . "' and  '" . $enddate . "' order by swm.sw_market_name ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function getActiveClient()
    {
        $this->db->order_by("advertiser", "asc");
        $result = $this->db->get_where('sv_new_advertiser', array('active' => 1));

        if ($result->num_rows() > 0) {
            $res = $result->result("array");

            $client = array();
            foreach ($res as $clnt) {
                $client_id = $clnt['id'];
                $client[$client_id] = $clnt['advertiser'];
            }
            return $client;
        }
    }

    public function getAllMarket()
    {
        $this->db->order_by("sw_market_name", "asc");
        $result = $this->db->get_where('sv_sw_market');

        if ($result->num_rows() > 0) {
            $res = $result->result("array");

            $markets = array();

            foreach ($res as $mkt) {
                $market_id = $mkt['id'];
                $markets[$market_id] = $mkt['sw_market_name'];
            }
            return $markets;
        }
    }

    public function getAllChannel()
    {
        $this->db->order_by("channel_name", "asc");
        $result = $this->db->get_where('sv_tv_channel');

        if ($result->num_rows() > 0) {
            $res = $result->result("array");

            $channels = array();

            foreach ($res as $chnl) {
                $channel_id = $chnl['tv_channel_id'];
                $channels[$channel_id] = $chnl['channel_name'];
            }
            return $channels;
        }
    }

    public function getChannelForMarket($market_id)
    {
        $query = "select tv_channel_id as channel_id,channel_name from sv_tv_channel stc "
            . "Inner Join sv_market_x_channel smxc on stc.tv_channel_id = smxc.channel_fk_id "
            . "where smxc.market_fk_id = $market_id";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
    }

    public function getCustomerNameOrDisplayName($channelId)
    {
        $query = "select stc.deployment_status, customer_display_name as network_name,customer_name as customer_name ,stc.display_name as channel_names,stc.locale as market from sv_customer sc
                  inner join sv_tv_channel stc on  stc.enterprise_id = sc.customer_id
                  where stc.tv_channel_id = $channelId ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

}

/* End of ns_model.php */
?>
