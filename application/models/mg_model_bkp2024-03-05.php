<?php

class MG_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->db->query("SET SESSION group_concat_max_len = 1000000");
    }

    public function insert_helper($table_name, $data_array)
    {
        $this->db->insert($table_name, $data_array);
        return $this->db->insert_id();

    }

    public function get_schedule_for_campaign($camp_id)
    {

        $result = $this->db->query("select sd.date, sum(impressions) as timp,sc.channel_id, c.channel_name  
				from  sv_advertiser_campaign_screens_dates as sd, sv_screen as sc, sv_tv_channel as c,sv_advertiser_campaign as ac
				where sd.campaign_id ='$camp_id'  and sd.screen_id=sc.screen_id and c.tv_channel_id=sc.channel_id AND 
				sd.status!='cancelled' group by sd.date, c.channel_name;");

        if ($result->num_rows() > 0) {
            $schedule = array();
            foreach ($result->result("array") as $s) {
                //generic function that will add schedule to the $param1
                update_schedule_helper($schedule, $s['channel_name'], $s['date'], $s['timp']);

            }
            return $schedule;
        }
        return array();

    }

    public function get_channel_schedule_summary($camp_ids, $id)
    {
        if (!isset($camp_ids) || empty($camp_ids)) {
            $camp_ids = 0;
        }
        $query_str = "SELECT MIN( sd.date ) as start_date , MAX( sd.date ) as end_date , ac.ro_duration, SUM( impressions ) AS timp,sc.channel_id, ac.internal_ro_number,e.customer_location,ac.client_name
							,channel_name,ac.Caption_name,ac.Language,mkt.sw_market_name,e.customer_name, e.customer_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,
								sv_customer as e,sv_sw_market AS mkt WHERE c.enterprise_id=e.customer_id and
						sd.campaign_id IN ($camp_ids) AND sd.status!='cancelled' and sd.screen_region_id =$id  AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id AND ac.market_id = mkt.id GROUP BY c.channel_name,ac.ro_duration";
        $res = $this->db->query($query_str);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_schedule_summary_br($camp_ids, $id)
    {
        if (!isset($camp_ids) || empty($camp_ids)) {
            $camp_ids = 0;
        }
        $query_str = "SELECT MIN( sd.date ) as start_date , MAX( sd.date ) as end_date , ac.ro_duration, SUM( impressions ) AS timp,sc.channel_id, ac.internal_ro_number,e.customer_location,ac.client_name
							,channel_name,ac.Caption_name,ac.Language,mkt.sw_market_name,e.customer_name, e.customer_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,
								sv_customer as e,sv_sw_market AS mkt WHERE c.enterprise_id=e.customer_id and
						sd.campaign_id IN ($camp_ids) AND sd.status!='cancelled' and sd.screen_region_id =$id  AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id AND ac.market_id = mkt.id and c.br_channel_id is not null GROUP BY c.channel_name,ac.ro_duration";
        $res = $this->db->query($query_str);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_schedule_summary_v1($camp_ids, $id)
    {
        if (!isset($camp_ids) || empty($camp_ids)) {
            $camp_ids = 0;
        }
        $query_str = "SELECT MIN( sd.date ) as start_date , MAX( sd.date ) as end_date , ac.ro_duration, 0 AS timp,sc.channel_id, ac.internal_ro_number,e.customer_location,ac.client_name
							,channel_name,ac.Caption_name,ac.Language,e.customer_name, e.customer_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac, 
								sv_customer as e WHERE c.enterprise_id=e.customer_id and
						sd.campaign_id IN ($camp_ids) AND sd.status ='cancelled' and ac.campaign_status != 'cancelled' and sd.screen_region_id =$id  AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id GROUP BY c.channel_name,ac.ro_duration";
        $res = $this->db->query($query_str);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_schedule_summary_v1_br($camp_ids, $id)
    {
        if (!isset($camp_ids) || empty($camp_ids)) {
            $camp_ids = 0;
        }
        $query_str = "SELECT MIN( sd.date ) as start_date , MAX( sd.date ) as end_date , ac.ro_duration, 0 AS timp,sc.channel_id, ac.internal_ro_number,e.customer_location,ac.client_name
							,channel_name,ac.Caption_name,ac.Language,e.customer_name, e.customer_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and
						sd.campaign_id IN ($camp_ids) AND sd.status ='cancelled' and ac.campaign_status != 'cancelled' and sd.screen_region_id =$id  AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id and c.br_channel_id is not null GROUP BY c.channel_name,ac.ro_duration";
        $res = $this->db->query($query_str);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_schedule_summary1($camp_ids, $id)
    {
        if (!isset($camp_ids) || empty($camp_ids)) {
            $camp_ids = 0;
        }
        $query_str = "SELECT MIN( sd.date ) as start_date , MAX( sd.date ) as end_date , ac.ro_duration, SUM( impressions ) AS timp,sc.channel_id, ac.internal_ro_number,e.customer_location,ac.client_name
                                                        ,channel_name,ac.Caption_name,ac.Language,e.customer_name, e.customer_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,
                                                                sv_customer as e WHERE c.enterprise_id=e.customer_id and
                                                sd.campaign_id IN ($camp_ids) AND sd.status!='cancelled' and sd.screen_region_id =$id  AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id GROUP BY ac.Caption_name,c.channel_name";
        $res = $this->db->query($query_str);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_network_wise_channel_schedule_summary($camp_ids, $internal_ro, $network_id)
    {

        $query_str = "SELECT MIN( sd.date ) as start_date , MAX( sd.date ) as end_date , ac.ro_duration, SUM( impressions ) AS timp, sc.channel_id, ac.internal_ro_number,e.customer_location,ac.client_name
							,channel_name,e.customer_name,ac.Caption_name,ac.Language, e.customer_id,ac.product_new,ac.brand_new FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac, 
								sv_customer as e WHERE c.enterprise_id=e.customer_id and
						sd.campaign_id IN ($camp_ids) AND sd.status!='cancelled' AND sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id and ac.internal_ro_number='$internal_ro' and e.customer_id=$network_id GROUP BY c.channel_name,ac.ro_duration";
        $res = $this->db->query($query_str);
        $this->session->set_userdata('channel_summary', $res);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_dates($camp_ids)
    {

        $query = "select MIN( sd.date ) as start_date , MAX( sd.date ) as end_date from sv_advertiser_campaign_screens_dates as sd where sd.campaign_id IN ($camp_ids)";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function get_network_schedule_details($internal_ro, $network_id)
    {

        $query = "select * from sv_networks_amount_info where internal_ro_number='$internal_ro' and customer_id=$network_id";
        $this->session->set_userdata('internal_ro', $internal_ro);
        $this->session->set_userdata('network_id', $network_id);
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function get_order_campaigns_v1($internal_ro_number, $id, $cancel_channel)
    {//1186, 531, 1211 cid=8591 / IOR=382

        //$internal_ro_number="SW/VINI/DIRECT/JAS 2012-01/Prag";


        $query = "select ssm.sw_market_name,ac.campaign_id,ac.market_id,ac.campaign_name,ac.start_date,  ac.end_date, ac.ro_duration,ac.create_datetime,ac.do_make_good,
				ac.brand_id,ac.caption_name, ac.campaign_status,ac.product_id, ac.client_name, ac.brand_new,	ac.product_new,ac.customer_ro_number, ac.internal_ro_number, 
				ac.csv_input, ac.agency_name, 	ac.daypart,ac.actual_impressions,ac.advertiser_id from sv_advertiser_campaign as ac
				inner join sv_sw_market as ssm on ssm.id = ac.market_id
				where ac.internal_ro_number = '$internal_ro_number'and `is_make_good` = 0 and selected_region_id = $id and visible_in_easyro = 1";
        if (count($cancel_channel) > 0) {
            $channel_ids = implode(",", $cancel_channel);
            if (empty($channel_ids) || !isset($channel_ids)) {
                $channel_ids = 0;
            }
            $query .= " and channel_id not in ($channel_ids)";
        }

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }

        return array();


    }

    public function get_channel_schedule($channel_id, $ro_number)
    {

        $res = $this->get_order_campaigns($ro_number);

        $camp_ids = array();
        foreach ($res as $camp) {
            array_push($camp_ids, $camp['campaign_id']);

        }
        $camp_ids = implode(',', $camp_ids);
        $query_str = "SELECT sd.date , ac.ro_duration,brand_new,product_new,ac.client_name, SUM( impressions ) AS day_imp, sc.channel_id, c.channel_name,
						e.customer_name, e.customer_id 
						FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac, 
						sv_customer as e WHERE c.enterprise_id=e.customer_id and
						 sd.campaign_id IN ($camp_ids) AND sd.status!='cancelled' AND sd.screen_id = sc.screen_id 
						AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id GROUP BY sd.date, ac.ro_duration order by sd.date";

        $res = $this->db->query($query_str);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }

        return array();

    }

    public function get_order_campaigns($internal_ro_number, $id)
    {//1186, 531, 1211 cid=8591 / IOR=382

        //$internal_ro_number="SW/VINI/DIRECT/JAS 2012-01/Prag";

        $query = "select ssm.sw_market_name,ac.campaign_id,ac.market_id,ac.campaign_name,ac.start_date,  ac.end_date, ac.ro_duration,ac.create_datetime,ac.do_make_good,
				ac.brand_id,ac.caption_name, ac.campaign_status,ac.product_id, ac.client_name, ac.brand_new,	ac.product_new,ac.customer_ro_number, ac.internal_ro_number, 
				ac.csv_input, ac.agency_name, 	ac.daypart,ac.actual_impressions,ac.advertiser_id from sv_advertiser_campaign as ac
				inner join sv_sw_market as ssm on ssm.id = ac.market_id
				where ac.internal_ro_number = '$internal_ro_number'and `is_make_good` = 0 and selected_region_id = $id and visible_in_easyro = 1  ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }

        return array();


    }

    public function get_approved_campaigns_summary($order_id, $network_id)
    {

        $query = "select * from sv_networks_amount_info where internal_ro_number = '$order_id' and customer_id = '$network_id'";

        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }

        return array();

    }

    public function get_network_ro_report_summary($order_id, $network_id)
    {


        $res = $this->db->query("select internal_ro_number, customer_name, channel_name,MIN( start_date ) as camp_start_date , MAX( end_date ) as camp_end_date, start_date,end_date,ro_duration, timp, customer_location,channel_id, client_name,brand_new,product_new from sv_approved_networks where internal_ro_number='$order_id' and customer_id='$network_id'");
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }

        return array();

    }

    public function get_network_ro_report_channels_summary($order_id, $network_id)
    {


        $res = $this->db->query("select internal_ro_number,Caption_name,Language, customer_name, channel_name,start_date,end_date,ro_duration, timp, customer_location,channel_id, client_name,brand_new,product_new from sv_approved_networks where internal_ro_number='$order_id' and customer_id='$network_id'");
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }

        return array();

    }

    public function get_network_summary($order_id)
    {

        $query = "select * from sv_networks_amount_info where internal_ro_number='$order_id'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_impressions_info($camp_ids, $network_id)
    {

        $query = "SELECT ac.`campaign_id` , ac.`screen_id` , a.brand_new, a.product_new, a.Language, a.ro_duration, ac.`date` , ac.`impressions` , s.start_time, s.end_time, tc.channel_name
		FROM sv_advertiser_campaign_screens_dates AS ac, sv_screen AS s, sv_tv_pu AS tp, sv_tv_channel AS tc, sv_advertiser_campaign AS a
		WHERE ac.`campaign_id` 
		IN ($camp_ids) 
		AND ac.status !=  'cancelled'
		AND s.screen_id = ac.screen_id
		AND tp.pu_id = s.program_id
		AND tc.tv_channel_id = tp.channel_id
		AND a.campaign_id = ac.campaign_id
		AND tc.enterprise_id = $network_id
		ORDER BY  `a`.`ro_duration` ASC ";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_network_channels($channel_ids, $network_id)
    {
        $query = "select channel_name from sv_tv_channel where tv_channel_id IN ($channel_ids) and enterprise_id = $network_id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_report($camp_ids, $network_id, $channel_name, $start_time, $end_time, $caption_name, $program_id_channel = null)
    {
        $query = " select acs.date as date,SUM(acs.impressions) as impressions,p.pu_id
    from sv_advertiser_campaign_screens_dates as acs,sv_advertiser_campaign as ac,sv_tv_channel as tc,sv_screen as s,sv_tv_pu as p
    where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id = $network_id and acs.status!='cancelled' and s.screen_id = acs.screen_id and p.channel_id = tc.tv_channel_id and tc.channel_name ='$channel_name' and p.pu_id = s.program_id and ((p.start_time >= '$start_time' and p.end_time <= '$end_time') or (p.start_time < '$start_time' and (p.end_time >= '$start_time' and p.end_time <= '$end_time'))) and ac.caption_name='$caption_name'";
        //global $program_id_channel;
        //echo print_r($program_id_channel,true)."<br/>";
        if (isset($program_id_channel) && count($program_id_channel) > 0 && !empty($program_id_channel)) {
            $pu_ids = implode(",", $program_id_channel);
            $pu_ids = "(" . $pu_ids . ")";
            $query = $query . "and p.pu_id not in $pu_ids";
        }
        $query = $query . " group by acs.date";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_report_v1($camp_ids, $network_id, $channel_name, $start_time, $end_time, $caption_name, $program_id_channel = null)
    {
        $query = " select acs.date as date,0 as impressions,p.pu_id
    from sv_advertiser_campaign_screens_dates as acs,sv_advertiser_campaign as ac,sv_tv_channel as tc,sv_screen as s,sv_tv_pu as p
    where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id = $network_id and acs.status ='cancelled' and ac.campaign_status != 'cancelled' and s.screen_id = acs.screen_id and p.channel_id = tc.tv_channel_id and tc.channel_name ='$channel_name' and p.pu_id = s.program_id and ((p.start_time >= '$start_time' and p.end_time <= '$end_time') or (p.start_time < '$start_time' and (p.end_time >= '$start_time' and p.end_time <= '$end_time'))) and ac.caption_name='$caption_name'";
        //global $program_id_channel;
        //echo print_r($program_id_channel,true)."<br/>";
        if (isset($program_id_channel) && count($program_id_channel) > 0 && !empty($program_id_channel)) {
            $pu_ids = implode(",", $program_id_channel);
            $pu_ids = "(" . $pu_ids . ")";
            $query = $query . "and p.pu_id not in $pu_ids";
        }
        $query = $query . " group by acs.date";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_captions_under_network($camp_ids, $network_id)
    {

        $query = "select distinct ac.caption_name 
from sv_advertiser_campaign_screens_dates as acs,sv_advertiser_campaign as ac
where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id = $network_id and acs.status!='cancelled'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_captions_under_network_v1($camp_ids, $network_id)
    {
        $query = "select distinct ac.caption_name 
    from sv_advertiser_campaign_screens_dates as acs,sv_advertiser_campaign as ac
    where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id = $network_id and acs.status ='cancelled' and ac.campaign_status != 'cancelled'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_network_dates($camp_ids, $network_id)
    {

        $query = "select MIN(ac.start_date) as start_date,MAX(ac.end_date) as end_date
from sv_advertiser_campaign as ac, sv_advertiser_campaign_screens_dates as acs
where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id=$network_id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_network_dates_v1($camp_ids, $network_id)
    {

        $query = "select MIN(acs.date) as start_date,MAX(acs.date) as end_date
from sv_advertiser_campaign as ac, sv_advertiser_campaign_screens_dates as acs
where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id=$network_id and acs.status='scheduled'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_network_cancel_dates_v1($camp_ids, $network_id)
    {

        $query = "select MIN(acs.date) as start_date,MAX(acs.date) as end_date
from sv_advertiser_campaign as ac, sv_advertiser_campaign_screens_dates as acs
where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id=$network_id and acs.status='cancelled'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_caption_details($camp_ids, $network_id, $caption_name)
    {

        $query = "select distinct ac.brand_new,ac.Language,ac.caption_name,ac.ro_duration
    from sv_advertiser_campaign_screens_dates as acs,sv_advertiser_campaign as ac
    where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id = $network_id and acs.status!='cancelled' and ac.caption_name = '$caption_name' ";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_caption_details_v1($camp_ids, $network_id, $caption_name)
    {

        $query = "select distinct ac.brand_new,ac.Language,ac.caption_name,ac.ro_duration
    from sv_advertiser_campaign_screens_dates as acs,sv_advertiser_campaign as ac
    where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id = $network_id and acs.status ='cancelled' and ac.campaign_status != 'cancelled' and ac.caption_name = '$caption_name' ";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_ro_report_details($camp_ids, $network_id)
    {

        $query = " select distinct ac.client_name,c.customer_name,swm.sw_market_name as state
from sv_advertiser_campaign_screens_dates as acs,sv_advertiser_campaign as ac,sv_customer as c,sv_sw_market as swm,sv_customer_sw_location as swl
where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id = $network_id and acs.status!='cancelled' and c.customer_id = acs.enterprise_id and c.customer_id = swl.customer_id and swl.sw_market_id=swm.id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_ro_report_details_v1($camp_ids, $network_id)
    {
        $query = " select distinct ac.client_name,c.customer_name,swm.sw_market_name as state
    from sv_advertiser_campaign_screens_dates as acs,sv_advertiser_campaign as ac,sv_customer as c,sv_sw_market as swm,sv_customer_sw_location as swl
    where ac.campaign_id IN ($camp_ids) and ac.campaign_id = acs.campaign_id and acs.enterprise_id = $network_id and acs.status ='cancelled' and ac.campaign_status != 'cancelled' and c.customer_id = acs.enterprise_id and c.customer_id = swl.customer_id and swl.sw_market_id=swm.id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_rates($channel_ids)
    {

        $query = "select spot_avg as channel_spot_avg_rate,banner_avg as channel_banner_avg_rate,tv_channel_id  from sv_tv_channel where tv_channel_id IN ($channel_ids)";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_network_shares($nw_ids)
    {

        $query = "select customer_id,revenue_sharing as customer_share from sv_customer where customer_id IN ($nw_ids)";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_details($id)
    {

        $query = "select c.customer_id,c.customer_name,c.customer_location,c.billing_name,tc.tv_channel_id,tc.channel_name,tc.deployment_status from sv_tv_channel as tc, sv_customer as c 
where tc. tv_channel_id = $id  and tc.enterprise_id = c.customer_id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_network_details($id)
    {

        $query = "select customer_id,customer_name,customer_location,customer_email from sv_customer where customer_id = $id";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_avg_rate($client_name, $channel_name)
    {

        $query = "select r. channel_spot_avg_rate as channel_spot_avg_rate,r.channel_banner_avg_rate as channel_banner_avg_rate  from(SELECT id, channel_spot_avg_rate,channel_banner_avg_rate FROM `ro_approved_networks` WHERE `client_name` = '$client_name' and `channel_name` = '$channel_name' ORDER BY `ro_approved_networks`.`id`  DESC  limit 1) as r";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_avg_rates($channel_name)
    {

        $query = "select AVG(r. channel_spot_avg_rate) as channel_spot_avg_rate,AVG(r.channel_banner_avg_rate) as channel_banner_avg_rate  from(SELECT id, channel_spot_avg_rate,channel_banner_avg_rate FROM `ro_approved_networks` WHERE `channel_name` = '$channel_name' ORDER BY `ro_approved_networks`.`id`  DESC  limit 0,5) as r";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function update_customer_shares($network_share, $network_id)
    {

        $query = "update sv_customer SET revenue_sharing  = $network_share where customer_id =$network_id";

        $res = $this->db->query($query);
    }

    public function get_approved_channel_details($internal_ro_number, $channel_id)
    {

        $query = "select * from ro_approved_networks where internal_ro_number = '$internal_ro_number' and tv_channel_id =$channel_id";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function set_customer_shares($internal_ro_number, $client_name, $network_share, $network_id)
    {

        $query = "update ro_approved_networks SET customer_share = $network_share where internal_ro_number = '$internal_ro_number' and client_name = '$client_name' and customer_id =$network_id";
        $res = $this->db->query($query);

    }

    public function get_network_amount_details($internal_ro_number, $network_id)
    {

        $query = "select customer_share, SUM(channel_spot_amount)+ SUM(channel_banner_amount) as network_amount,billing_name,revision_no from ro_approved_networks where  internal_ro_number = '$internal_ro_number' and customer_id = $network_id";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_ro_details($customer_ro_number)
    {

        $query = "SELECT DISTINCT internal_ro_number
     FROM  `sv_advertiser_campaign`
     WHERE  `customer_ro_number` =  '$customer_ro_number' and visible_in_easyro = 1";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function update_campaign_status($camp_id, $value)
    {

        $query = "update ro_approved_campaigns SET approval_status = $value where campaign_id = $camp_id";

        $res = $this->db->query($query);

    }

    /*public function get_channel_schedule_summary_v1($customer_ro_number) {

	$query = "SELECT DISTINCT internal_ro_number
FROM  `sv_advertiser_campaign`
WHERE  `customer_ro_number` =  '$customer_ro_number' and visible_in_easyro = 1";
	 $res = $this->db->query($query);

         if($res->num_rows() > 0) {
                return $res->result("array");
        }
        return array();
        }*/

    public function get_networks()
    {

        $query = "select distinct internal_ro_number,customer_id from ro_approved_networks where pdf_generation_status=0 and pdf_processing = 0 order by id desc limit 10";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_networks_for_ro($internal_ro_number)
    {
        $query = "select customer_id from ro_approved_networks where internal_ro_number = '$internal_ro_number' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_networks_for_ro_br($internal_ro_number)
    {
        $query = "SELECT DISTINCT ran.customer_id,ran.internal_ro_number FROM ro_approved_networks AS ran
					INNER JOIN sv_tv_channel AS stc ON stc.enterprise_id = ran.customer_id
					WHERE internal_ro_number = '$internal_ro_number'
					AND stc.tv_channel_id = ran.tv_channel_id
					AND stc.br_channel_id IS NOT NULL";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function update_pdf_generation_status($order_id, $network_id)
    {

        $query = "update ro_approved_networks set pdf_generation_status = 1,pdf_processing = 0 where internal_ro_number = '$order_id' and customer_id = $network_id";

        $res = $this->db->query($query);

    }

    public function get_screen_region_ids($order_id)
    {

        $query = "select distinct selected_region_id from sv_advertiser_campaign where internal_ro_number='$order_id'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function update_ro_status($order_id, $user_id, $date)
    {
        $query = "update ro_amount set ro_approval_status=1,approval_timestamp = '$date',approval_user_id = $user_id where internal_ro_number = '$order_id'";
        $res = $this->db->query($query);

    }

    public function get_approval_request_data($order_id)
    {
        $query = "SELECT * FROM `ro_orders` WHERE `internal_ro_number`='$order_id'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_network_campaigns($order_id)
    {
        $query = "SELECT DISTINCT ac.campaign_id, sd.screen_region_id, sd.enterprise_id
FROM sv_advertiser_campaign AS ac, sv_advertiser_campaign_screens_dates AS sd
WHERE internal_ro_number =  '$order_id'
AND ac.campaign_id = sd.campaign_id
AND sd.status !=  'cancelled'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_network_campaigns_v1($order_id)
    {
        $query = "SELECT DISTINCT ac.campaign_id, sd.screen_region_id, sd.enterprise_id
FROM sv_advertiser_campaign AS ac, sv_advertiser_campaign_screens_dates AS sd
WHERE internal_ro_number =  '$order_id'
AND ac.campaign_id = sd.campaign_id
AND sd.status =  'cancelled'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_captions_per_channel($camp_ids)
    {

        $query = "SELECT distinct ac.campaign_id,ac.caption_name,sc.channel_id,tc.channel_name from sv_advertiser_campaign as ac,sv_advertiser_campaign_screens_dates as sd,sv_screen as sc,sv_tv_channel as tc where ac.campaign_id IN ($camp_ids) and sd.campaign_id = ac.campaign_id and sd.status!='cancelled' and sd.screen_id = sc.screen_id and sc.channel_id = tc.tv_channel_id";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_captions_per_channel_v1($camp_ids)
    {

        $query = "SELECT distinct ac.campaign_id,ac.caption_name,sc.channel_id,tc.channel_name from sv_advertiser_campaign as ac,sv_advertiser_campaign_screens_dates as sd,sv_screen as sc,sv_tv_channel as tc where ac.campaign_id IN ($camp_ids) and sd.campaign_id = ac.campaign_id and sd.status='cancelled' and ac.campaign_status != 'cancelled' and sd.screen_id = sc.screen_id and sc.channel_id = tc.tv_channel_id";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_total_network_seconds_internal_ro($internal_ro)
    {
        $query = "SELECT SUM( `total_spot_ad_seconds`  + `total_banner_ad_seconds`  ) AS total_scheduled_seconds FROM  `ro_approved_networks` WHERE  `internal_ro_number` = '$internal_ro'";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_total_network_payout_for_nw($internal_ro, $customer_id)
    {
        $query = "SELECT SUM(`channel_spot_amount`*(`customer_share` /100 ) + `channel_banner_amount` * ( `customer_share`/100 ) ) AS network_payout,sum(total_spot_ad_seconds) as total_seconds FROM  `ro_approved_networks` WHERE  `internal_ro_number` = '$internal_ro' and customer_id='$customer_id' ";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_network_ro_report($network_ro)
    {
        $query = "select * from ro_network_ro_report_details where network_ro_number = '$network_ro'";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function update_network_ro_report_detail($data, $where)
    {
        $this->db->update('ro_network_ro_report_details', $data, $where);
    }

    function get_customer_ros($where = Array())
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        //$where['date_format(start_date, \'%Y-%m-%d\') >='] = "date_format('{$this->input->post('from')}', '%Y-%m-%d')";
        //$where['date_format(end_date, \'%Y-%m-%d\') <='] =  "date_format('{$this->input->post('to')}', '%Y-%m-%d')";

        // added by lokanath for ro dropdown with date range
        $start_date = $this->input->post('from');
        $end_date = $this->input->post('to');

        $where = "(( rnro.start_date >= '$start_date' and rnro.end_date <= '$end_date' )
or
( rnro.start_date <= '$start_date' and rnro.end_date between '$start_date' and '$end_date')
or
( rnro.start_date between '$start_date' and '$end_date' and rnro.end_date >= '$end_date' )
or
( rnro.start_date <= '$start_date' and rnro.end_date >= '$end_date' ))";
        // end
        if ($is_test_user != 2) {
            $query = "select distinct customer_ro_number from ro_network_ro_report_details as rnro inner join ro_am_external_ro as raer on rnro.internal_ro_number=raer.internal_ro and raer.test_user_creation='$is_test_user' ";
        } else {
            $query = "select distinct customer_ro_number from ro_network_ro_report_details as rnro inner join ro_am_external_ro as raer on rnro.internal_ro_number=raer.internal_ro and raer.id not in (select ro_id from ro_linked_advance_ro) ";
        }

        $query = $query . " and " . $where . " order by customer_ro_number asc";
        $res = $this->db->query($query);

        //$this->db->distinct();
        //$this->db->select('customer_ro_number');
        //$this->db->from('ro_network_ro_report_details');
        // $this->db->join('ro_am_external_ro','ro_network_ro_report_details.internal_ro_number=ro_am_external_ro.internal_ro','inner');
        //$this->db->where($where, NULL, FALSE);
        //$this->db->order_by('customer_ro_number','asc');
        $result = $this->build_ro_json($res->result());
        return $result;
    }

    function build_ro_json($datas)
    {
        $json = array();
        foreach ($datas as $data) {
            $json['row'][$data->customer_ro_number] = $data->customer_ro_number;
        }

        $json['result'] = 'success';

        return json_encode($json);
    }

    function get_records()
    {
        $page = $this->input->post('page');
        $per_page = $this->input->post('rp');
        log_message('info','In mg_model@get_records | POST Data=> '.print_r($_POST,True));

        if ($page > 1) {
            $offset = ($page - 1) * $per_page;
        } else {
            $offset = 0;
        }
        log_message('info','In mg_model@get_records | Offset=> '.print_r($offset,True));

        $record = $this->_get_records($page, $offset, $per_page);
        log_message('info','In mg_model@get_records | Record=> '.print_r($record->result("array"),True));
        $total_row = $this->_get_records()->num_rows();
        log_message('info','In mg_model@get_records | Total Row => '.print_r($total_row,True));

        return $this->build_record_json($record, $total_row, $page);
    }

    function _get_records($page = 1, $offset = NULL, $limit = NULL)
    {

        //$where = array();
        $where = "";
        if ($this->input->post('from')) {
            //$where['date_format(start_date, \'%Y-%m-%d\') >='] = "date_format('{$this->input->post('from')}', '%Y-%m-%d')";
            $start_date = date('Y-m-d', strtotime($this->input->post('from')));
            $where = $where . " ((start_date <='$start_date' and end_date >='$start_date') or (start_date >='$start_date') )";
        }

        if ($this->input->post('to')) {
            //$where['date_format(end_date, \'%Y-%m-%d\') <='] =  "date_format('{$this->input->post('to')}', '%Y-%m-%d')";
            $end_date = date('Y-m-d', strtotime($this->input->post('to')));
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $where = $where . " and ((end_date <='$end_date') or (start_date <='$end_date' and end_date >='$end_date'))";
        }

        if ($this->input->post('ro_number') && $this->input->post('ro_number') != 'all') {
            //$where['customer_ro_number'] =  "'{$this->input->post('ro_number')}'";
            $customer_ro_number = $this->input->post('ro_number');
            $where = $where . " and customer_ro_number='$customer_ro_number' ";
        }

        /*if(count($where))
		{
			$this->db->where($where, NULL, FALSE);
		}*/

        //$this->db->order_by($this->input->post('sortname'), $order = $this->input->post('sortorder'));
        //$where = $where . " order by start_date";
        $where = $where . " order by market,customer_name";
        /*
		 **changed by mani on 22.8.2013
		 ** for adding billing name
		*/
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        if ($is_test_user != 2) {
            /*$query = "select distinct(re.internal_ro_number),re.customer_ro_number,re.network_ro_number,re.customer_name, re.client_name,
                   re.agency_name,re.market,re.start_date,re.end_date,re.activity_months,re.gross_network_ro_amount,re.customer_share,
                   re.net_amount_payable,re.release_date,an.billing_name from 
                   ro_network_ro_report_details as re,ro_approved_networks as an,ro_am_external_ro as aer where 
                   re.customer_name=an.customer_name and re.internal_ro_number=an.internal_ro_number and 
                   re.internal_ro_number=aer.internal_ro and an.internal_ro_number=aer.internal_ro and test_user_creation='$is_test_user' ";*/
            $query = "select distinct(re.internal_ro_number),re.customer_ro_number,re.network_ro_number,re.customer_name, re.client_name,
                   re.agency_name,re.market,re.start_date,re.end_date,re.activity_months,re.gross_network_ro_amount,re.customer_share,
                   re.net_amount_payable,re.release_date,re.billing_name from 
                   ro_network_ro_report_details as re where ";
        } else {
            $query = "select distinct(re.internal_ro_number),re.customer_ro_number,re.network_ro_number,re.customer_name, re.client_name,
                    re.agency_name,re.market,re.start_date,re.end_date,re.activity_months,re.gross_network_ro_amount,re.customer_share,
                    re.net_amount_payable,re.release_date,an.billing_name from
                     ro_network_ro_report_details as re,ro_approved_networks as an,ro_am_external_ro as aer where
                      re.customer_name=an.customer_name and re.internal_ro_number=an.internal_ro_number and
                       re.internal_ro_number=aer.internal_ro and an.internal_ro_number=aer.internal_ro and
                        aer.id not in (select ro_id from ro_linked_advance_ro) and ";
        }

        $query = $query . "  $where";
        if (isset($limit) && isset($offset)) {
            $query = $query . " limit " . $offset . "," . $limit;
        }
        log_message('info', 'In mg_model@_get_records | Query => '.print_r($query,True));
        return $this->db->query($query);
        //return $this->db->get('ro_network_ro_report_details', $limit, $offset);
    }

    function build_record_json($record, $total_row, $page)
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
                    $nw_ro_number,
                    $record->customer_name,
                    $record->client_name,
                    $record->agency_name,
                    $record->market,
                    date('d-M-Y', strtotime($record->start_date)),
                    date('d-M-Y', strtotime($record->end_date)),
                    $record->activity_months,
                    $record->gross_network_ro_amount,
                    $record->customer_share,
                    $record->net_amount_payable,
                    $record->release_date,
                    $record->billing_name
                )
            );
        }

        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;

        log_message('info','In mg_model@build_record_json | Json=> '.print_r($json,true));
        return json_encode($json);
    }

    function get_external_ros($where = Array())
    {
        $where['date_format(start_date, \'%Y-%m-%d\') >='] = "date_format('{$this->input->post('from')}', '%Y-%m-%d')";
        $where['date_format(end_date, \'%Y-%m-%d\') <='] = "date_format('{$this->input->post('to')}', '%Y-%m-%d')";

        $this->db->distinct();
        $this->db->select('customer_ro_number');
        $this->db->where($where, NULL, FALSE);

        $result = $this->build_externalro_json($this->db->get('ro_external_ro_report_details')->result());
        return $result;
    }

    function build_external_ro_json($datas)
    {
        $json = array();
        foreach ($datas as $data) {
            $json['row'][$data->customer_ro_number] = $data->customer_ro_number;
        }

        $json['result'] = 'success';

        return json_encode($json);
    }

    function get_external_ro_records()
    {
        $page = $this->input->post('page');
        $per_page = $this->input->post('rp');

        if ($page > 1) {
            $offset = ($page - 1) * $per_page;
        } else {
            $offset = 0;
        }

        $record = $this->_get_ro_records($page, $offset, $per_page);
        $total_row = $this->_get_ro_records()->num_rows();

        return $this->build_ro_record_json($record, $total_row, $page);
    }

    function _get_ro_records($page = 1, $offset = NULL, $limit = NULL)
    {
        //$where = array();

        if ($this->input->post('sel_view_type') == 1) {
            $logged_in = $this->session->userdata("logged_in_user");
            $is_test_user = $logged_in[0]['is_test_user'];

            $where = "1 ";
            if ($this->input->post('from')) {
                //$where['date_format(start_date, \'%Y-%m-%d\') >='] = "date_format('{$this->input->post('from')}', '%Y-%m-%d')";
                $start_date = date('Y-m-d', strtotime($this->input->post('from')));
                $where = $where . " and ((ro_external_ro_report_details.start_date <='$start_date' and ro_external_ro_report_details.end_date >='$start_date') or (ro_external_ro_report_details.start_date >='$start_date')) ";
            }

            if ($this->input->post('to')) {
                //$where['date_format(end_date, \'%Y-%m-%d\') <='] =  "date_format('{$this->input->post('to')}', '%Y-%m-%d')";
                $end_date = date('Y-m-d', strtotime($this->input->post('to')));
                if (strtotime($end_date) < strtotime($start_date)) {
                    $end_date = $start_date;
                }
                $where = $where . " and ((ro_external_ro_report_details.end_date <='$end_date') or (ro_external_ro_report_details.start_date <='$end_date' and ro_external_ro_report_details.end_date >='$end_date'))";
            }

            if ($this->input->post('ro_number') && $this->input->post('ro_number') != 'all') {
                //$where['customer_ro_number'] =  "'{$this->input->post('ro_number')}'";
                $customer_ro_number = $this->input->post('ro_number');
                $where = $where . " and ro_external_ro_report_details.customer_ro_number='$customer_ro_number' ";
            }
            if ($is_test_user != 2) {
                $where = $where . " and ro_am_external_ro.test_user_creation='$is_test_user' ";
            } else {
                $where = $where . " and ro_am_external_ro.id not in (select ro_id from ro_linked_advance_ro) ";
            }

            $where = $where . " order by ro_external_ro_report_details.start_date";

            /*if(!empty($where))
                    {
                            $this->db->where($where, NULL, FALSE);
                    } */

            //$this->db->order_by($this->input->post('sortname'), $order = $this->input->post('sortorder'));

            //return $this->db->get('ro_external_ro_report_details', $limit, $offset);
            $query = "select ro_external_ro_report_details.*,ro_am_external_ro.id as ro_id from ro_external_ro_report_details INNER JOIN ro_am_external_ro on ro_am_external_ro.internal_ro = ro_external_ro_report_details.internal_ro_number";
            $query = $query . " where " . $where;
            if (isset($limit) && isset($offset)) {
                $query = $query . " limit " . $offset . "," . $limit;
            }
            $res = $this->db->query($query);
            return $res;
        } else {

            $logged_in = $this->session->userdata("logged_in_user");
            $is_test_user = $logged_in[0]['is_test_user'];

            $start_date = date('Y-m-d', strtotime($this->input->post('from')));
            $end_date = date('Y-m-d', strtotime($this->input->post('to')));
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $where = $where . " where (ro_date >='$start_date' and ro_date <= '$end_date')";
            $where = $where . "  and ro_am_external_ro.test_user_creation='$is_test_user'";
            $where = $where . " order by ro_date";
//                    $this->db->select('*');
//                    $this->db->from('ro_external_ro_report_details');
//                    $this->db->join('ro_am_external_ro', 'ro_am_external_ro.internal_ro = ro_external_ro_report_details.internal_ro_number');

            $query = "select * from ro_external_ro_report_details INNER JOIN ro_am_external_ro on ro_am_external_ro.internal_ro = ro_external_ro_report_details.internal_ro_number ";
            $query = $query . "  $where";
            if (isset($limit) && isset($offset)) {
                $query = $query . " limit " . $offset . "," . $limit;
            }

            $res = $this->db->query($query);
            return $res;
        }
    }

    function build_ro_record_json($record, $total_row, $page)
    {
        $json = Array();
        $items = Array();
        foreach ($record->result() as $record) {
            $ro_id = $record->ro_id;
            $data = $this->get_invoice_data_for_external_ro($ro_id);
            $items[] = array(
                'id' => $record->customer_ro_number,
                'cell' => array(
                    $record->customer_ro_number,
                    $record->internal_ro_number,
                    $record->client_name,
                    $record->agency_name,
                    date('d-M-Y', strtotime($record->start_date)),
                    date('d-M-Y', strtotime($record->end_date)),
                    $record->gross_ro_amount,
                    $record->agency_commission_amount,
                    $record->agency_rebate,
                    $record->other_expenses,
                    $record->total_seconds_scheduled,
                    $record->total_network_payout,
                    $record->net_revenue,
                    $record->net_contribution_amount,
                    $record->net_contribution_amount_per,
                    $data['total_amount_collected'],
                    $data['tds'],
                    $data['total_payment_received']
                )
            );
        }

        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;

        return json_encode($json);
    }

    public function get_invoice_data_for_external_ro($ro_id)
    {
        $query = "select * from  ro_am_invoice_collection where ro_id = '$ro_id' order by id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");

            $data = array();
            foreach ($result as $res) {
                if (count($data) > 0) {
                    $data['total_amount_collected'] = $data['total_amount_collected'] + $res['amnt_collected'];
                    $data['tds'] = $data['tds'] + $res['tds'];
                    if ($res['chk_complete'] == 1) {
                        $data['total_payment_received'] = 'Yes';
                    }

                } else {
                    $data['total_amount_collected'] = $res['amnt_collected'];
                    $data['tds'] = $res['tds'];

                    if ($res['chk_complete'] == 1) {
                        $data['total_payment_received'] = 'Yes';
                    } else {
                        $data['total_payment_received'] = 'No';
                    }

                }
            }
            return $data;
        }
    }

    public function get_csv_customer_ros($start_date, $end_date)
    {
        //$query = "select distinct customer_ro_number from ro_network_ro_report_details where start_date >='$start_date' and end_date <='$end_date' order by customer_ro_number";
        if (strtotime($end_date) < strtotime($start_date)) {
            $end_date = $start_date;
        }
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select distinct nro.customer_ro_number from ro_network_ro_report_details as nro,ro_am_external_ro as aer where 1 ";
        $where = " and ((nro.start_date <='$start_date' and nro.end_date >='$start_date') or (nro.start_date >='$start_date') )";
        $where = $where . " and ((nro.end_date <='$end_date') or ((nro.start_date <='$end_date' and nro.end_date >='$end_date')) )";
        $where = $where . " and nro.customer_ro_number=aer.cust_ro and aer.test_user_creation='$is_test_user' ";
        $where = $where . " order by nro.start_date";
        $query = $query . "" . $where;

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_network_ro_report_details($start_date = null, $end_date = null, $ro_name = null)
    {
        //$query = "select * from ro_network_ro_report_details where customer_ro_number IN ($ro_name)";

        /*
		 **changed by mani on 22.8.2013
		 ** for adding billing name
		*/
        /*$query = "select distinct(re.internal_ro_number),re.customer_ro_number,re.network_ro_number,re.customer_name, re.client_name,
                re.agency_name,re.market,re.start_date,re.end_date,re.activity_months,re.gross_network_ro_amount,re.customer_share,
                re.net_amount_payable,re.release_date,an.billing_name from
                 ro_network_ro_report_details as re,ro_approved_networks as an where
                  re.internal_ro_number=an.internal_ro_number and re.customer_name=an.customer_name and
                   customer_ro_number IN ($ro_name)";*/

        $query = "select re.internal_ro_number,re.customer_ro_number,re.network_ro_number,re.customer_name, re.client_name,
                re.agency_name,re.market,re.start_date,re.end_date,re.activity_months,re.gross_network_ro_amount,re.customer_share,
                re.net_amount_payable,re.release_date,re.billing_name from
                 ro_network_ro_report_details as re where ";
        if ($ro_name != null) {
            $query = $query . " customer_ro_number = " . $ro_name . " and ";
        }
        /*
		 **changed by mani on 29.8.2013
		 ** for verifying date range
		*/
        if (isset($start_date) && isset($end_date)) {
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $where = "((start_date <='$start_date' and end_date >='$start_date') or (start_date >='$start_date')) ";
            $where = $where . " and ((end_date <='$end_date') or (start_date <='$end_date' and end_date >='$end_date')) ";
            $where = $where . " order by start_date";
            $query = $query . "" . $where;
        }

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_external_ro_csv_report_details($start_date, $end_date, $report_type)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        if ($report_type == 1) {
            //$query = "select * from ro_external_ro_report_details where start_date >='$start_date' and end_date<='$end_date' order by customer_ro_number";
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $query = "select ero.*,aer.id as ro_id from ro_external_ro_report_details as ero,ro_am_external_ro as aer where 1 ";
            $where = $where . " and ((ero.start_date <='$start_date' and ero.end_date >='$start_date') or (ero.start_date >='$start_date') )";
            $where = $where . " and ((ero.end_date <='$end_date') or (ero.start_date <='$end_date' and ero.end_date >='$end_date')) ";
            if ($is_test_user != 2) {
                $where = $where . " and ero.internal_ro_number = aer.internal_ro and aer.test_user_creation='$is_test_user' ";
            } else {
                $where = $where . " and ero.internal_ro_number = aer.internal_ro and aer.id not in (select ro_id from ro_linked_advance_ro) ";
            }

            $where = $where . " order by start_date";
            $query = $query . "" . $where;

            $res = $this->db->query($query);

            if ($res->num_rows() > 0) {
                return $res->result("array");
            }
        } else {
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $query = "select * from ro_external_ro_report_details as nr inner join ro_am_external_ro as am  on am.internal_ro=nr.internal_ro_number  ";
            $query = $query . " where ro_date >='$start_date' and ro_date <= '$end_date' ";
            $query = $query . " and am.test_user_creation='$is_test_user' ";
            $query = $query . " order by client";

            $res = $this->db->query($query);

            if ($res->num_rows() > 0) {
                return $res->result("array");
            }
        }

    }

    public function get_report_for_invoice($start_date, $end_date, $report_type = null)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        if ($report_type == 1) {
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $query = "select ero.* from ro_external_ro_report_details as ero,ro_am_external_ro as aer where 1 ";
            $where = $where . " and ((aer.camp_start_date <='$start_date' and aer.camp_end_date >='$start_date') or (aer.camp_start_date >='$start_date') )";
            $where = $where . " and ((aer.camp_end_date <='$end_date') or (aer.camp_start_date <='$end_date' and aer.camp_end_date >='$end_date')) ";
            switch ($is_test_user) {
                case 2:
                    $where = $where . " and ero.internal_ro_number = aer.internal_ro and aer.id not in (select ro_id from ro_linked_advance_ro) ";
                    break;
                default :
                    $where = $where . " and ero.internal_ro_number = aer.internal_ro and aer.test_user_creation='$is_test_user' ";
                    break;
            }

            $where = $where . " order by start_date";
            $query = $query . "" . $where;

            $res = $this->db->query($query);

            if ($res->num_rows() > 0) {
                return $res->result("array");
            }
        } else {
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $query = "select * from ro_external_ro_report_details as nr inner join ro_am_external_ro as am  on am.internal_ro=nr.internal_ro_number  ";
            $query = $query . " where ro_date >='$start_date' and ro_date <= '$end_date' ";
            $query = $query . " and am.test_user_creation='$is_test_user' ";
            $query = $query . " order by client";

            $res = $this->db->query($query);

            if ($res->num_rows() > 0) {
                return $res->result("array");
            }
        }

    }

    public function get_buyers_details($customer_ro)
    {
        $query = "select client_contact_id,agency_contact_id,internal_agency from ro_am_external_ro
    	            INNER JOIN sv_new_agency ON agency = agency_name where cust_ro='$customer_ro'";
        $res = $this->db->query($query);
        $result = $res->result('array');

        if (strtolower($result[0]['internal_agency']) == 1) {
            if (isset($result[0]['client_contact_id'])) {
                $client_query = "select * from `ro_client_contact` where `id` = " . $result[0]['client_contact_id'] . "";
                $c_res = $this->db->query($client_query);
                $client_res = $c_res->result('array');
                $buyer_details['buyer_contact'] = $client_res[0]['client_contact_name'];
                $buyer_details['buyer_address'] = $client_res[0]['client_address'];
            }

        } else {
            if (isset($result[0]['agency_contact_id'])) {
                $agency_query = "select * from `ro_agency_contact` where `id` = " . $result[0]['agency_contact_id'] . "";
                $a_res = $this->db->query($agency_query);
                $agency_res = $a_res->result('array');
                $buyer_details['buyer_contact'] = $agency_res[0]['agency_contact_name'];
                $buyer_details['buyer_address'] = $agency_res[0]['agency_address'];
            }

        }
        //$result = $res->result("array");
        return $buyer_details;
    }

    public function get_supplier_details($customer_ro)
    {
        $query = "select ru.user_name,amer.user_id from ro_am_external_ro as amer, ro_user as ru where amer.cust_ro='$customer_ro' and ru.user_id = amer.user_id ";
        $res = $this->db->query($query);
        $supplier_res = $res->result('array');
        $supplier_contact['user_name'] = $supplier_res[0]['user_name'];
        return $supplier_contact;
    }

    // added by loakanath for invoice generator

    public function get_campaign_details_v1($customer_ro, $start_date, $end_date)
    {
        $campaignDetails = $this->getScheduledCampaignIdsForCustomerRos($customer_ro, $start_date, $end_date);

        $campaignIds = $campaignDetails['campaign_id'];
        $startDate = $campaignDetails['start_date'];
        $endDate = $campaignDetails['end_date'];

        $invoiceDetails = $this->generateInvoiceValues($campaignIds, $startDate, $endDate);
        $invoiceValues = $this->getInvoiceValues($invoiceDetails, $customer_ro, $startDate, $endDate);
        return $invoiceValues;
    }

    //added by Nitish to get Buyer related info

    public function getScheduledCampaignIdsForCustomerRos($customer_ro, $start_date, $end_date)
    {
        $query = "select group_concat(campaign_id) as campaign_id,min(start_date) as start_date, max(end_date) as end_date
                        from sv_advertiser_campaign
                    where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                    AND customer_ro_number = '$customer_ro' AND is_make_good = 0 ";
        $results = $this->db->query($query);

        $data = array();
        if ($results->num_rows() != 0) {
            $campaignDetails = $results->result("array");
            if ($campaignDetails[0]['campaign_id'] != NULL) {
                $data['campaign_id'] = $campaignDetails[0]['campaign_id'];
                $data['start_date'] = $campaignDetails[0]['start_date'];
                $data['end_date'] = $campaignDetails[0]['end_date'];
                return $data;
            } else {
                $data['campaign_id'] = 0;
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                return $data;
            }
        }
        $data['campaign_id'] = 0;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        return $data;
    }

    //added by Nitish to get Supplier contact for invoice generator

    public function generateInvoiceValues($campaignIds, $startDate, $endDate)
    {
        $query = "Select max(X.total_ad_seconds) as total_fct,X.screen_region_id,X.ro_duration,X.impressions,X.brand_id,X.brand_name,X.market_id,
                    X.channel_id,X.caption_name
                        from
                    (SELECT ro_duration*SUM(acsd.impressions) as total_ad_seconds,acsd.screen_region_id,ac.ro_duration,SUM(acsd.impressions) as impressions,
                        ac.brand_id,ac.brand_new as brand_name,ac.market_id,ac.channel_id,ac.caption_name
                        FROM sv_advertiser_campaign_screens_dates acsd 
                        INNER JOIN sv_advertiser_campaign ac on ac.campaign_id=acsd.campaign_id 
                        WHERE  ac.campaign_id IN ($campaignIds)  and acsd.screen_region_id in(1,3) and (acsd.date >= '$startDate' and acsd.date <='$endDate') 
                        and  acsd.status ='scheduled' 
                        GROUP BY acsd.screen_region_id,ac.ro_duration,ac.brand_id,ac.market_id,ac.channel_id ) X
                        GROUP BY X.screen_region_id,X.ro_duration,X.brand_id,X.market_id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getInvoiceValues($invoiceDetails, $customer_ro, $startDate, $endDate)
    {
        $data = array();
        foreach ($invoiceDetails as $value) {
            $data['channel_id'] = $value['channel_id'];
            $data['sw_market'] = $this->getMarketName($value['market_id']);
            $data['brand'] = $value['brand_name'];
            $data['product'] = $this->getProductGroup($value['brand_id']);
            $data['spots'] = $value['impressions'];
            $data['start_date'] = $startDate;
            $data['end_date'] = $endDate;
            $data['channel_fct'] = $value['total_fct'];
            $data['sum_impression'] = $value['impressions'];
            if ($value['screen_region_id'] == 1) {
                $value['region_name'] = 'Spot';
                $market_price = $this->get_rate_for_ro_market_region($customer_ro, $data['sw_market'], 'spot_price');
                $total_channel_fct = $this->get_total_channel_fct_for_ro_channel_region($customer_ro, $value['channel_id'], 'total_spot_ad_seconds');
            } else if ($value['screen_region_id'] == 3) {
                $data['region_name'] = 'Banner';
                $market_price = $this->get_rate_for_ro_market_region($customer_ro, $val['sw_market'], 'banner_price');
                $total_channel_fct = $this->get_total_channel_fct_for_ro_channel_region($customer_ro, $value['channel_id'], 'total_banner_ad_seconds');
            }
            $data['rate'] = ($market_price / $total_channel_fct) * 10;
        }
        return $data;
    }

    public function getMarketName($marketId)
    {
        $query = "select * from sv_sw_market where id='$marketId' ";
        $result = $this->db->query($query);
        $sw_market_array = $result->result('array');
        return $sw_market_array[0]['sw_market_name'];
    }

    public function getProductGroup($brandId)
    {
        $query = "select product_group from sv_product_group spg
			Inner Join sv_new_advertiser sna on sna.product_group_id = spg.id
			Inner Join sv_new_brand snb on sna.id = snb.new_advertiser_id
			where snb.id = $brandId ";
        $result = $this->db->query($query);
        $product = $result->result('array');
        return $product[0]['product_group'];
    }

    public function get_rate_for_ro_market_region($customer_ro, $market_name, $region_price)
    {
        $query = "select $region_price as price from ro_market_price as mp,ro_am_external_ro as am where mp.market='$market_name' and mp.ro_id=am.id and am.cust_ro='$customer_ro' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            return $res[0]['price'];
        } else {
            return 0;
        }


    }

    public function get_total_channel_fct_for_ro_channel_region($customer_ro, $channel_id, $region_fct)
    {
        $query = "select $region_fct as total_fct from ro_approved_networks as an,ro_am_external_ro as am where an.internal_ro_number=am.internal_ro and am.cust_ro='$customer_ro' and an.tv_channel_id='$channel_id'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            if ($res[0]['total_fct'] == 0) {
                return 1;
            } else {
                return $res[0]['total_fct'];
            }
        } else {
            return 1;
        }


    }

    public function get_campaign_details($customer_ro, $start_date, $end_date)
    {
        $query = "select * from sv_advertiser_campaign where customer_ro_number='" . $customer_ro . "' and is_make_good=0";
        $res = $this->db->query($query);

        // min of start date and max of end date
        $min_max_date_query = "select min(start_date) as start_date, max(end_date) as end_date from sv_advertiser_campaign where customer_ro_number='" . $customer_ro . "' and is_make_good=0";
        $res_min_max_date_query = $this->db->query($min_max_date_query);
        $res_min_max_date_res = $res_min_max_date_query->result('array');
        // end

        //echo '<pre>';print_r($res->result('array'));exit;
        $invoice_array = array();
        $loop_array = $res->result('array');
        $i = 0;
        foreach ($loop_array as $camp_dtls) {
            // fetching market
            // getting screen id
            $screen_qry = "select screen_id,sum(impressions) as sum_impression,screen_region_id as region_id from sv_advertiser_campaign_screens_dates where campaign_id = " . $camp_dtls['campaign_id'] . " and status != 'cancelled' and date >= '" . $start_date . "' and date <= '" . $end_date . "' group by screen_region_id order by screen_id desc";
            $screen_qry_rs = $this->db->query($screen_qry);
            $screen_array = $screen_qry_rs->result('array');
            $scr_id = $screen_array[0]['screen_id'];
            if ($scr_id == '') {
                continue;
            }
            $invoice_array[$i]['sum_impression'] = $screen_array[0]['sum_impression'];

            // getting channel id
            /*$chnl_qry = "select channel_id from sv_screen where screen_id = ".$scr_id;
			$chnl_qry_rs = $this->db->query($chnl_qry);
			$chnl_array = $chnl_qry_rs->result('array');
			$channel_id = $chnl_array[0]['channel_id'];

			// getting tam market id
			if($channel_id == 409 || $channel_id == 271 || $channel_id == 428){continue;} // these are the internal channels for testing
			$tam_market_qry = "select tam_market_id from sv_tam_channel_market where tv_channel_id = ".$channel_id." limit 1";
			$tam_market_qry_rs = $this->db->query($tam_market_qry);
			$tam_market_array = $tam_market_qry_rs->result('array');
			$tam_market_id = $tam_market_array[0]['tam_market_id'];

			// getting SW market
			$sw_market_qry = "select swm.sw_market_name from  sv_sw_market as swm,sv_sw_tam_market as swtm where swm.id = swtm.sw_market_id and swtm.tam_market_id = ".$tam_market_id;
			$sw_market_qry_rs = $this->db->query($sw_market_qry);
			$sw_market_array = $sw_market_qry_rs->result('array');
			$sw_market_name = $sw_market_array[0]['sw_market_name']; */
            // end

            $channel_id = $camp_dtls['channel_id'];
            $market_id = $camp_dtls['market_id'];

            $market_query = "select * from sv_sw_market where id='$market_id' ";
            $sw_market_qry_rs = $this->db->query($market_query);
            $sw_market_array = $sw_market_qry_rs->result('array');
            $sw_market_name = $sw_market_array[0]['sw_market_name'];

            $invoice_array[$i]['sw_market'] = $sw_market_name;

            if ($camp_dtls['brand_id'] == '') {
                $invoice_array[$i]['brand'] = $camp_dtls['brand_new'];
                $invoice_array[$i]['product'] = $camp_dtls['product_new'];
            } else {
                // fetchin brand
                $brand_qry = "select brand,new_advertiser_id from sv_new_brand where id = " . $camp_dtls['brand_id'];
                $brand_qry_rs = $this->db->query($brand_qry);
                $brand_array = $brand_qry_rs->result('array');
                $brand_name = $brand_array[0]['brand'];
                $new_adv_id = $brand_array[0]['new_advertiser_id'];
                // end

                $invoice_array[$i]['brand'] = $brand_name;

                // fetchi product
                $prod_qry = "select product_group from sv_product_group where id = (select product_group_id from sv_new_advertiser where id = " . $new_adv_id . ")";
                $prod_qry_rs = $this->db->query($prod_qry);
                $prod_array = $prod_qry_rs->result('array');
                $prod_name = $prod_array[0]['product_group'];
                // end

                $invoice_array[$i]['product'] = $prod_name;
            }

            $invoice_array[$i]['sw_market'] = $sw_market_name;
            $invoice_array[$i]['duration'] = $camp_dtls['ro_duration'];
            $invoice_array[$i]['spots'] = $camp_dtls['actual_impressions'];
            $invoice_array[$i]['start_date'] = $res_min_max_date_res[0]['start_date'];
            $invoice_array[$i]['end_date'] = $res_min_max_date_res[0]['end_date'];
            $invoice_array[$i]['channel_id'] = $channel_id;
            $invoice_array[$i]['region_id'] = $screen_array[0]['region_id'];
            $invoice_array[$i]['channel_fct'] = $screen_array[0]['sum_impression'] * $camp_dtls['ro_duration'];
            //$invoice_array[$i]['FCT'] = $invoice_array[$i]['duration'] * $invoice_array[$i]['spots'];

            $i = $i + 1;
        }

        //echo '<pre>';print_r($invoice_array);exit;
        // merging invoice_array according to common market, duration and brand wise
//		$new_arr = array();
//		$index = -1;
//		foreach($invoice_array as $arr_val){
//			$found = 0;
//			foreach($new_arr as $new_arr_val){
//				if($new_arr_val['sw_market'] == $arr_val['sw_market'] && $new_arr_val['duration'] == $arr_val['duration'] && $new_arr_val['brand'] == $arr_val['brand']){
//					if($new_arr_val['sum_impression'] >= $arr_val['sum_impression']){
//						$new_arr[$index]['sum_impression'] = $new_arr_val['sum_impression'];
//					}
//					else{
//						$new_arr[$index]['sum_impression'] = $arr_val['sum_impression'];
//					}
//					//$new_arr[$index]['sum_impression'] = $new_arr_val['sum_impression'] + $arr_val['sum_impression'];
//					$found = 1;
//					break;
//				}
//			}
//			if($found == 0){
//				array_push($new_arr,$arr_val);
//				$index = $index + 1;
//			}
//		}
//		// end merging
//		//echo '<pre>';print_r($new_arr);exit;

        $new_array = array();
        foreach ($invoice_array as $keys => $inv) {

            if (count($new_array) == 0) {
                if (!in_array($inv['channel_id'], $new_array) && !in_array($inv['region_id'], $new_array) && !in_array($inv['sw_market'], $new_array) && !in_array($inv['brand'], $new_array)) {

                    $tmp = array();
                    $tmp['sum_impression'] = $inv['sum_impression'];
                    $tmp['channel_id'] = $inv['channel_id'];
                    $tmp['sw_market'] = $inv['sw_market'];
                    $tmp['brand'] = $inv['brand'];
                    $tmp['product'] = $inv['product'];
                    $tmp['duration'] = $inv['duration'];
                    $tmp['spots'] = $inv['spots'];
                    $tmp['start_date'] = $inv['start_date'];
                    $tmp['end_date'] = $inv['end_date'];
                    $tmp['channel_fct'] = $inv['channel_fct'];

                    $tmp['region_id'] = $inv['region_id'];

                    $tmp['durations'] = array();
                    $tmp['durations'][$inv['duration']] = $inv['sum_impression'];

                    array_push($new_array, $tmp);
                }
            } else {
                $counter = 1;
                foreach ($new_array as $key => $new_array_val) {
                    if (($inv['channel_id'] == $new_array_val['channel_id']) && ($inv['region_id'] == $new_array_val['region_id']) && ($inv['sw_market'] == $new_array_val['sw_market']) && ($inv['brand'] == $new_array_val['brand'])) {
                        $counter = 0;
                        $new_array[$key]['channel_fct'] = $new_array_val['channel_fct'] + $inv['channel_fct'];

                        if (!isset($new_array[$key]['durations'])) {
                            $new_array[$key]['durations'] = array();

                            $new_array[$key]['durations'][$inv['duration']] = $inv['sum_impression'];
                        } else {
                            if (!array_key_exists($inv['duration'], $new_array[$key]['durations'])) {
                                $new_array[$key]['durations'][$inv['duration']] = $inv['sum_impression'];
                            } else {
                                $new_array[$key]['durations'][$inv['duration']] = $new_array[$key]['durations'][$inv['duration']] + $inv['sum_impression'];
                            }
                        }

                    }
                }
                if ($counter == 1) {
                    $tmp = array();
                    $tmp['sum_impression'] = $inv['sum_impression'];
                    $tmp['channel_id'] = $inv['channel_id'];
                    $tmp['sw_market'] = $inv['sw_market'];
                    $tmp['brand'] = $inv['brand'];
                    $tmp['product'] = $inv['product'];
                    $tmp['duration'] = $inv['duration'];
                    $tmp['spots'] = $inv['spots'];
                    $tmp['start_date'] = $inv['start_date'];
                    $tmp['end_date'] = $inv['end_date'];
                    $tmp['channel_fct'] = $inv['channel_fct'];
                    $tmp['region_id'] = $inv['region_id'];
                    $tmp['durations'] = array();
                    $tmp['durations'][$inv['duration']] = $inv['sum_impression'];

                    array_push($new_array, $tmp);
                }
            }
        }
        //echo '<pre>';print_r($new_array);

        $data = array();
        foreach ($new_array as $key => $val) {
            $market_name = trim($val['sw_market']);
            $brand_name = trim($val['brand']);
//                        echo $market_name."--".$brand_name." " ;
            if (count($data) == 0) {
                array_push($data, $val);
            } else {
                $counter = 1;
                foreach ($data as $d_key => $d_val) {
                    if ((trim($val['sw_market']) == $d_val['sw_market']) && (trim($val['brand']) == $d_val['brand']) && ($val['region_id'] == $d_val['region_id'])) {
                        $counter = 0;
                        if ($val['channel_fct'] > $d_val['channel_fct']) {
                            $data[$d_key] = $val;
                            break;
                        }
                    }
                }
                if ($counter == 1) {
                    array_push($data, $val);
                }

            }

        }
        //echo '<pre>';print_r($data);

        $new_data = array();
        foreach ($data as $val) {
            foreach ($val['durations'] as $duration => $sum_impression) {
                $tmp = array();
                $tmp['channel_id'] = $val['channel_id'];
                $tmp['sw_market'] = $val['sw_market'];
                $tmp['brand'] = $val['brand'];
                $tmp['product'] = $val['product'];
                $tmp['spots'] = $val['spots'];
                $tmp['start_date'] = $val['start_date'];
                $tmp['end_date'] = $val['end_date'];
                //$tmp['channel_fct'] = $val['channel_fct'] ;
                $channel_fct = $duration * $sum_impression;
                $tmp['channel_fct'] = $duration * $sum_impression;
                $tmp['duration'] = $duration;
                $tmp['sum_impression'] = $sum_impression;
                if ($val['region_id'] == 1) {
                    $tmp['region_name'] = 'Spot';
                    $market_price = $this->get_rate_for_ro_market_region($customer_ro, $val['sw_market'], 'spot_price');
                    $total_channel_fct = $this->get_total_channel_fct_for_ro_channel_region($customer_ro, $val['channel_id'], 'total_spot_ad_seconds');
                } else if ($val['region_id'] == 3) {
                    $tmp['region_name'] = 'Banner';
                    $market_price = $this->get_rate_for_ro_market_region($customer_ro, $val['sw_market'], 'banner_price');
                    $total_channel_fct = $this->get_total_channel_fct_for_ro_channel_region($customer_ro, $val['channel_id'], 'total_banner_ad_seconds');
                }
                $tmp['rate'] = ($market_price / $total_channel_fct) * 10;
                array_push($new_data, $tmp);
            }
        }

        //echo '<pre>';print_r($new_data);
        return $new_data;
    }

    public function cancel_channel_from_ro($chnl_id, $order_id)
    {
        $int_ro_no = base64_decode($order_id);
        $get_camp_id_qry = "select campaign_id from sv_advertiser_campaign where internal_ro_number = '$int_ro_no'";
        $res_get_camp_id_qry = $this->db->query($get_camp_id_qry);
        $result = $res_get_camp_id_qry->result("array");//print_r($result);
        $camp_id_arr = array();
        foreach ($result as $val) {
            array_push($camp_id_arr, $val['campaign_id']);
        }
        $campaign_ids = implode(",", $camp_id_arr);//echo $campaign_ids;exit;

        $get_all_campaigns_for_channel = "select distinct(campaign_id) from sv_advertiser_campaign_screens_dates where status != 'cancelled' and campaign_id in ($campaign_ids) and screen_id in (select distinct screen_id from sv_screen where channel_id=$chnl_id)";
        $result = $this->db->query($get_all_campaigns_for_channel);

        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    // end

    public function delete_channel_file_location($channel_id, $internale_ro_number)
    {
        $data = array('channel_id' => $channel_id, 'internal_ro_number' => $internale_ro_number);
        $this->db->delete('ro_channel_file_location', $data);
    }

    public function test_user()
    {
        //$query = "select * from ro_user where profile_id IN(1,2,7)";
        $query = "select * from ro_user where profile_id IN(1,2,7) where is_test_user=1"; //send MIS to BH, COO, Finance
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function daily_email_report_list($userType)
    {
        //$query = "select * from ro_user where profile_id IN(1,2,7)";
        $query = "select * from ro_user where profile_id IN(1,2,7) and is_test_user=$userType and active=1"; //send MIS to BH, COO, Finance
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_users_for_region($regionId)
    {
        //$query = "select * from ro_user where profile_id IN(1,2,7)";
        $query = "select * from ro_user as ru inner join ro_user_region as rur on ru.id=rur.user_id
                      where rur.region_id = $regionId and ru.profile_id IN(11,12) and ru.is_test_user=0"; //send MIS to BH, COO, Finance
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function daily_email_report_list_advance_ro()
    {
        //$query = "select * from ro_user where profile_id IN(1,2,7)";
        $query = "select * from ro_user where profile_id IN(1,2,7) and is_test_user=2"; //send MIS to BH, COO, Finance
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function daily_email_report_details()
    {
        // summary for current month for each client
        $month = date("m");
        $current_month_start_date = date("Y-m-01");
        $current_month_end_date = date("Y-m-t");

        $am_query = "select am.client,am.internal_ro,am.cust_ro from ro_am_external_ro as am,ro_external_ro_report_details as nr where am.internal_ro=nr.internal_ro_number";
        $am_query = $am_query . " and (ro_date >='$current_month_start_date' and ro_date <= '$current_month_end_date')";
        $am_query = $am_query . " order by client";

        $am_query_res = $this->db->query($am_query);
        $data = array();

        if ($am_query_res->num_rows() != 0) {
            $structure = "";
            $am_res = $am_query_res->result("array");
            foreach ($am_res as $am_data) {
                $client_name = $am_data['client'];
                $ext_ro = $am_data['cust_ro'];
                $order_id = $am_data['internal_ro'];

                // get ro data for every ext_ro
                /*$get_ro_qy = "select * from ro_amount where customer_ro_number='$ext_ro'";
				$get_ro_qy_res = $this->db->query($get_ro_qy);
				$ro_data = $get_ro_qy_res->result("array");
				$revenue = ($ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount']) * SERVICE_TAX;

				$total_network_payout_ary = $this->get_total_network_payout($order_id);
				$other_expenses_ary = $this->get_external_ro_report_details($order_id);

				$total_network_payout = $total_network_payout_ary[0]['network_payout'];

                                $agency_rebate = 0 ;
                                if($ro_data[0]['agency_rebate_on'] == "ro_amount"){
                                        $agency_rebate = $ro_data[0]['ro_amount'] * ($ro_data[0]['agency_rebate']/100);
                                }
                                else{
                                        $agency_rebate = ($ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount']) * ($ro_data[0]['agency_rebate']/100);
                                }

				$actual_net_amount = $ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount'] - $agency_rebate - $other_expenses_ary[0]['other_expenses'];

				$total_sw_net_contribution = $actual_net_amount - $total_network_payout; */
                // end
                $get_ro_qy = "select * from ro_external_ro_report_details where customer_ro_number='$ext_ro'";
                $get_ro_qy_res = $this->db->query($get_ro_qy);
                $ro_data = $get_ro_qy_res->result("array");

                $revenue = $ro_data[0]['net_revenue'];
                $total_network_payout = $ro_data[0]['total_network_payout'];
                $total_sw_net_contribution = $ro_data[0]['net_contribution_amount'];

                $tmp = array();
                $tmp['client'] = $client_name;
                $tmp['internal_ro'] = $order_id;
                $tmp['revenue'] = $revenue;
                $tmp['network_payout'] = $total_network_payout;
                $tmp['sw_net_contribute'] = $total_sw_net_contribution;

                array_push($data, $tmp);

                /*$data .=  "<br>"."Client Name : ".$client_name."<br>";
				$data .=  "External RO : ".$ext_ro."<br>";
				$data .=  "Revenue : ".$revenue."<br>";
				$data .=  "Total Network Payout : ".$total_network_payout."<br>";
				$data .=  "Surewaves Net Contribution : ".$total_sw_net_contribution."<br>"; */
            }//echo $data;
            $data = $this->convert_data_into_structure($data);

            $structure = "<table><tr>"
                . "<td><b>Client Name</b></td>"
                . "<td><b>No of Ro's</b></td>"
                . "<td><b>Total Revenue</b></td>"
                . "<td><b>Total Network Payout</b></td>"
                . "<td><b>Total SureWaves Net Contribution</b></td>"
                . "</tr>";
            foreach ($data as $val) {
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $val['client'] . "</td>";
                $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
                $structure = $structure . "<td>" . $val['revenue'] . "</td>";
                $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
                $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
                $structure = $structure . "</tr>";
            }
            $structure = $structure . "</table>";
            return $structure;
        }
    }

    public function convert_data_into_structure($data)
    {
        $result = array();

        foreach ($data as $val) {
            $client = trim($val['client']);
            $region = trim($val['region']);
            $ro_id = trim($val['ro_id']);

            if (!array_key_exists($client, $result)) {
                $result[$client] = array();
            }

            if (!array_key_exists($region, $result[$client])) {
                $result[$client][$region] = array();
                $tmp = array();

                $tmp['client'] = trim($val['client']);
                $tmp['internal_ro'] = trim($val['internal_ro']);
                $tmp['ro_id'] = trim($val['ro_id']);
                $tmp['region'] = trim($val['region']);
                $tmp['is_international'] = $val['is_international'];
                $tmp['revenue'] = trim($val['revenue']);
                $tmp['network_payout'] = trim($val['network_payout']);
                $tmp['sw_net_contribute'] = trim($val['sw_net_contribute']);
                $tmp['total_internal_ro'] = 1;
                $tmp['net_contribuition_percent'] = 0.0;
                if (isset($val['revenue']) && !empty($val['revenue']) && $val['revenue'] != 0) {
                    $tmp['net_contribuition_percent'] = round(($val['sw_net_contribute'] / $val['revenue']) * 100);
                }

                array_push($result[$client][$region], $tmp);

            } else {
                $roexistInArray = $this->checkRoExistInArray($ro_id, $result[$client][$region]);
                if ($roexistInArray['found']) {
                    $index = $roexistInArray['index'];
                    $result[$client][$region][$index]['total_internal_ro'] = $result[$client][$region][$index]['total_internal_ro'] + 1;
                    $result[$client][$region][$index]['revenue'] = $result[$client][$region][$index]['revenue'] + $val['revenue'];
                    $result[$client][$region][$index]['network_payout'] = $result[$client][$region][$index]['network_payout'] + $val['network_payout'];
                    $result[$client][$region][$index]['sw_net_contribute'] = $result[$client][$region][$index]['sw_net_contribute'] + $val['sw_net_contribute'];
                    $result[$client][$region][$index]['net_contribuition_percent'] = 0.0;
                    if (isset($result[$client][$region][$index]['revenue']) && $result[$client][$region][$index]['revenue'] != 0) {
                        $result[$client][$region][$index]['net_contribuition_percent'] = round(($result[$client][$region][$index]['sw_net_contribute'] / $result[$client][$region][$index]['revenue']) * 100);
                    }
                } else {
                    $tmp = array();

                    $tmp['client'] = trim($val['client']);
                    $tmp['internal_ro'] = trim($val['internal_ro']);
                    $tmp['ro_id'] = trim($val['ro_id']);
                    $tmp['is_international'] = $val['is_international'];
                    $tmp['region'] = trim($val['region']);
                    $tmp['revenue'] = trim($val['revenue']);
                    $tmp['network_payout'] = trim($val['network_payout']);
                    $tmp['sw_net_contribute'] = trim($val['sw_net_contribute']);
                    $tmp['total_internal_ro'] = 1;
                    $tmp['net_contribuition_percent'] = 0.0;
                    if (isset($val['revenue']) && $val['revenue'] != 0) {
                        $tmp['net_contribuition_percent'] = round(($val['sw_net_contribute'] / $val['revenue']) * 100);
                    }

                    array_push($result[$client][$region], $tmp);
                }
            }
        }
        return $result;
    }

    public function checkRoExistInArray($ro_id, $arrayData)
    {
        $data = array();
        $found = FALSE;
        foreach ($arrayData as $key => $val) {
            if ($ro_id == trim($val['ro_id'])) {
                $index = $key;
                $found = TRUE;
            }
            if ($found) {
                break;
            }
        }
        if ($found) {
            $data['index'] = $index;
        }
        $data['found'] = $found;
        return $data;
    }

    public function daily_email_report_details_v1($givenDate)
    {
        // summary for current month for each client
        //$month = date("m");

        $current_month_start_date = date("Y-m-01", strtotime($givenDate));
        $current_month_end_date = date("Y-m-t", strtotime($givenDate));


        $am_query = "select am.id,am.client,am.internal_ro,am.cust_ro from ro_am_external_ro as am,ro_external_ro_report_details as nr where am.internal_ro=nr.internal_ro_number and test_user_creation=0";
        $am_query = $am_query . " and (( camp_start_date >= '$current_month_start_date' and camp_end_date <= '$current_month_end_date' )
                                                or
                ( camp_start_date <= '$current_month_start_date' and camp_end_date between '$current_month_start_date' and '$current_month_end_date')
                                                or
                ( camp_start_date between '$current_month_start_date' and '$current_month_end_date' and camp_end_date >= '$current_month_end_date' )
                                                or
                ( camp_start_date <= '$current_month_start_date' and camp_end_date >= '$current_month_end_date' ))";
        $am_query = $am_query . " order by client";

        $am_query_res = $this->db->query($am_query);
        $data = array();

        if ($am_query_res->num_rows() != 0) {
            $structure = "";
            $am_res = $am_query_res->result("array");
            foreach ($am_res as $am_data) {
                $client_name = $am_data['client'];
                //$ext_ro = $am_data['cust_ro'];
                $am_ext_id = $am_data['id'];
                $internal_ro = $am_data['internal_ro'];
                $proportional_calculation = $this->time_period_proportional_calculation_advance($am_ext_id, $internal_ro, $current_month_start_date, $current_month_end_date);

                $tmp = array();
                $tmp['client'] = $client_name;
                $tmp['internal_ro'] = $internal_ro;
                $tmp['revenue'] = floor($proportional_calculation['revenue']);
                $tmp['network_payout'] = floor($proportional_calculation['total_nw_payout']);
                $tmp['sw_net_contribute'] = floor($proportional_calculation['total_sw_net_contribution']);
                $tmp['net_contribuition_percent'] = round(($proportional_calculation['total_sw_net_contribution'] / $proportional_calculation['revenue']) * 100);

                array_push($data, $tmp);

                /*$data .=  "<br>"."Client Name : ".$client_name."<br>";
				$data .=  "External RO : ".$ext_ro."<br>";
				$data .=  "Revenue : ".$revenue."<br>";
				$data .=  "Total Network Payout : ".$total_network_payout."<br>";
				$data .=  "Surewaves Net Contribution : ".$total_sw_net_contribution."<br>"; */
            }//echo $data;
            //for converting into client wise
            $data = $this->convert_data_into_structure_bkp($data);
            return $this->getMonthlyMisReport_bkp($data);
        }
    }

    public function time_period_proportional_calculation_advance($am_ext_id, $internal_ro, $given_start_date, $given_end_date)
    {
        $market_price_data = array('ro_id' => $am_ext_id);
        $market_price = $this->get_market_ro_price($market_price_data);
        $ro_amount_values = $this->get_ro_amount($internal_ro);
        $nw_payout = 0;
        $ro_value_market = 0;

        foreach ($market_price as $mp) {
            $market_name = $mp['market'];
            $spot_market_price = $mp['spot_price'];
            $banner_market_price = $mp['banner_price'];

            //* remaining get channel_id and channel_name for that market
            //$channel_detail = $this->get_channel_for_market($market_name) ;
            $channel_detail = $this->get_scheduled_channel_for_market($market_name, $internal_ro);

            //$total_spot_fraction = 0 ;
            //$total_banner_fraction = 0 ;
            $scheduled_spot_seconds = 0;
            $scheduled_banner_seconds = 0;
            $total_spot_second = 0;
            $total_banner_second = 0;
            $channel_payout = 0;
            $total_channel_payout = 0;
            //$count = 0 ;

            foreach ($channel_detail as $chnl) {
                $channel_id = $chnl['tv_channel_id'];
                $market_id = $chnl['market_id'];

                //get data from ro_approved_network
                $approved_nw_data = $this->get_approved_data_for_customer_channel_ro(array('tv_channel_id' => $channel_id, 'internal_ro_number' => $internal_ro));
                //$total_spot_second = $approved_nw_data[0]['total_spot_ad_seconds'] ;
                //$total_banner_second = $approved_nw_data[0]['total_banner_ad_seconds'] ;

                //find scheduled fct for given time period
                $scheduled_impression = $this->get_scheduled_impression_for_time_period($channel_id, $internal_ro, $given_start_date, $given_end_date, $market_id);
                $total_scheduled_seconds = $this->get_total_scheduled_seconds($internal_ro, $channel_id, $market_id);

                if (($scheduled_impression['spot_ad_seconds'] == 0 && $scheduled_impression['banner_ad_seconds'] == 0) || (!isset($approved_nw_data[0]['total_spot_ad_seconds']) && !isset($approved_nw_data[0]['total_banner_ad_seconds']))) {
                    //For Testing Purpose

                    continue;
                } else {
                    $scheduled_spot_seconds = $scheduled_spot_seconds + $scheduled_impression['spot_ad_seconds'];
                    $scheduled_banner_seconds = $scheduled_banner_seconds + $scheduled_impression['banner_ad_seconds'];

                    //$total_spot_second = $total_spot_second + $approved_nw_data[0]['total_spot_ad_seconds'] ;
                    //$total_banner_second = $total_banner_second + $approved_nw_data[0]['total_banner_ad_seconds'] ;

                    $total_spot_second = $total_spot_second + $total_scheduled_seconds['spot_ad_seconds'];
                    $total_banner_second = $total_banner_second + $total_scheduled_seconds['banner_ad_seconds'];

                    //$spot_impression_fraction = $scheduled_impression['spot_ad_seconds']/$total_spot_second ;
                    //$banner_impression_fraction = $scheduled_impression['banner_ad_seconds']/$total_banner_second ;

                    //$total_spot_fraction = $total_spot_fraction + $spot_impression_fraction ;
                    //$total_banner_fraction = $total_banner_fraction + $banner_impression_fraction ;

                    $channel_payout = ($scheduled_impression['spot_ad_seconds'] * $approved_nw_data[0]['channel_spot_avg_rate'] * ($approved_nw_data[0]['customer_share'] / 100)) / 10 + ($scheduled_impression['banner_ad_seconds'] * $approved_nw_data[0]['channel_banner_avg_rate'] * ($approved_nw_data[0]['customer_share'] / 100)) / 10;
                    $total_channel_payout += $channel_payout;

                    //$count += 1 ;
                }


            }
            //$average_percentage = ($avg_spot_percentage+$avg_banner_percenatge)/2 ;
            //$average_spot_fraction = $total_spot_fraction/$count ;
            //$average_banner_fraction = $total_banner_fraction/$count ;
            $average_spot_fraction = $scheduled_spot_seconds / $total_spot_second;
            $average_banner_fraction = $scheduled_banner_seconds / $total_banner_second;
            if ($average_spot_fraction > 1) $average_spot_fraction = 1;
            if ($average_banner_fraction > 1) $average_banner_fraction = 1;
            $market_price = $spot_market_price * $average_spot_fraction + $banner_market_price * $average_banner_fraction;

            $nw_payout += $total_channel_payout;
            $ro_value_market += $market_price;
        }
        $ro_fraction = $ro_value_market / $ro_amount_values[0]['ro_amount'];
        $agency_commission = $ro_amount_values[0]['agency_commission_amount'] * $ro_fraction;
        $result = array();

        $result['revenue'] = ($ro_value_market - $agency_commission) * SERVICE_TAX;
        $result['total_nw_payout'] = $nw_payout;

        $agency_rebate = 0;
        if ($ro_amount_values[0]['agency_rebate_on'] == "ro_amount") {
            $agency_rebate = $ro_value_market * ($ro_amount_values[0]['agency_rebate'] / 100);
        } else {
            $agency_rebate = ($ro_value_market - $agency_commission) * ($ro_amount_values[0]['agency_rebate'] / 100);
        }
        //$agency_rebate = $agency_rebate*$ro_fraction ;
        $other_expenses_ary = $this->get_external_ro_report_details($internal_ro);
        $other_expenses = $other_expenses_ary[0]['other_expenses'] * $ro_fraction;

        $actual_net_amount = $ro_value_market - $agency_commission - $agency_rebate - $other_expenses;

        $result['total_sw_net_contribution'] = $actual_net_amount - $nw_payout;

        return $result;

    }

    public function get_market_ro_price($where_data)
    {
        $result = $this->db->get_where('ro_market_price', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_ro_amount($order_id)
    {

//        $query = "select * from ro_amount where internal_ro_number = '$order_id'";
//        $res = $this->db->query($query);
        $whereData = array('internal_ro_number' => $order_id);
        $res = $this->db->get_where('ro_amount', $whereData);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_scheduled_channel_for_market($market_name, $internal_ro)
    {
        /*$query = "select distinct stc.tv_channel_id,stc.channel_name,sm.id as market_id from sv_advertiser_campaign sac
                       inner join sv_tv_channel stc on sac.channel_id = stc.tv_channel_id
                       inner join sv_market_x_channel smc ON stc.tv_channel_id = smc.channel_fk_id
                       inner join sv_sw_market sm ON sm.id = smc.market_fk_id and sm.id = sac.market_id
                       where sm.sw_market_name='$market_name' and sac.internal_ro_number='$internal_ro' and sac.campaign_status !='cancelled' order by tv_channel_id " ; */
        $query = "select distinct stc.tv_channel_id,stc.channel_name,sm.id as market_id from sv_advertiser_campaign sac
                       inner join sv_tv_channel stc on sac.channel_id = stc.tv_channel_id                       
                       inner join sv_sw_market sm ON sm.id = sac.market_id
                       where sm.sw_market_name='$market_name' and sac.internal_ro_number='$internal_ro' and sac.campaign_status Not In ('pending_approval','cancelled') order by tv_channel_id ";
        $result = $this->db->query($query);
        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_approved_data_for_customer_channel_ro($data)
    {
        $result = $this->db->get_where('ro_approved_networks', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        } else {
            return array();
        }
    }

    public function get_scheduled_impression_for_time_period($channel_id, $internal_ro, $start_date, $end_date, $market_id = 0)
    {
        $campaignIds = $this->getScheduledCampaignIds($internal_ro, $channel_id, $market_id);
        /*
            $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id,min(date) as start_date,max(date) as end_date "
                    . "FROM sv_advertiser_campaign_screens_dates AS acsd "
                    . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
                    . "WHERE  ac.channel_id='$channel_id' and (acsd.date >= '$start_date' and acsd.date <='$end_date' ) "
                    . "and acsd.screen_region_id in(1,3) and acsd.status ='scheduled' and ac.campaign_status Not In ('pending_approval','cancelled') and "
                    . "ac.internal_ro_number='$internal_ro' and ac.is_make_good = 0";
            if(isset($market_id) && !empty($market_id)) {
                $query .= " and ac.market_id='$market_id' " ;
            } */

        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id,min(date) as start_date,max(date) as end_date "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaignIds) and ac.campaign_status Not In ('pending_approval') and ac.approved_status = 'Approved' and  acsd.screen_region_id in(1,3) and acsd.status ='scheduled' and "
            . "(acsd.date >= '$start_date' and acsd.date <='$end_date' ) and ac.is_make_good = 0";

        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id";
        $results = $this->db->query($query);
        $fcts = $results->result("array");

        $data = array();
        $data['spot_ad_seconds'] = 0;
        $data['banner_ad_seconds'] = 0;

        foreach ($fcts as $value) {
            if ($value['screen_region_id'] == 1) {
                $data['spot_ad_seconds'] += $value['total_ad_seconds'];
            } else if ($value['screen_region_id'] == 3) {
                $data['banner_ad_seconds'] += $value['total_ad_seconds'];
            }

            if (!isset($data['start_date'])) {
                $data['start_date'] = substr($value['start_date'], 0, 11);
            } else if (strtotime($value['start_date']) < strtotime($data['start_date'])) {
                $data['start_date'] = substr($value['start_date'], 0, 11);
            }

            if (!isset($data['end_date'])) {
                $data['end_date'] = substr($value['end_date'], 0, 11);
            } else if (strtotime($value['end_date']) > strtotime($data['end_date'])) {
                $data['end_date'] = substr($value['end_date'], 0, 11);
            }

            /*if (isset($value['start_date']) || !isset($data['start_date'])) {
                $data['start_date'] = substr($value['start_date'], 0, 11);
            } else {
                if (strtotime($value['start_date']) > strtotime($data['start_date'])) {  // the operator user here should be less than '<' operator
                    $data['start_date'] = substr($value['start_date'], 0, 11);
                }
            }
            if (isset($value['end_date']) || !isset($data['end_date'])) {
                $data['end_date'] = substr($value['end_date'], 0, 11);
            } else {
                if (strtotime($value['end_date']) > strtotime($data['end_date'])) {
                    $data['end_date'] = substr($value['end_date'], 0, 11);
                }
            }*/
        }
        return $data;
    }

    public function getScheduledCampaignIds($internal_ro, $channel_id, $market_id)
    {
//        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
//                        where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
//                        AND internal_ro_number = '$internal_ro'  AND visible_in_easyro = 1 and channel_id = '$channel_id'";

        $query = "   SELECT group_concat(campaign_id) as campaign_id
                    FROM sv_advertiser_campaign
                    WHERE campaign_status IN ( 'pending_content', 'saved', 'scheduled', 'completed' )
                    AND approved_status = 'Approved'
                    AND internal_ro_number = '$internal_ro'
		    AND is_make_good = 0
                    AND visible_in_easyro = 1
                    AND channel_id = '$channel_id'";

        if (isset($market_id) && !empty($market_id)) {
            $query .= " and market_id='$market_id' ";
        }
        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function get_total_scheduled_seconds($internal_ro, $channel_id, $market_id = 0)
    {
        $campaignIds = $this->getScheduledCampaignIds($internal_ro, $channel_id, $market_id);

        /*$query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
                    . "FROM sv_advertiser_campaign_screens_dates AS acsd "
                    . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
                    . "WHERE  ac.channel_id='$channel_id' and acsd.screen_region_id in(1,3) and "
                    . "acsd.status ='scheduled' and ac.campaign_status Not In ('pending_approval','cancelled')  and ac.internal_ro_number='$internal_ro' and ac.is_make_good = 0";
            if(isset($market_id) && !empty($market_id)) {
                $query .= " and ac.market_id='$market_id' " ;
            } */

        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaignIds) and acsd.status ='scheduled' and acsd.screen_region_id in(1,3) and ac.campaign_status Not In ('pending_approval') and ac.approved_status = 'Approved' and ac.is_make_good = 0 ";

        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id";
        $results = $this->db->query($query);
        $fcts = $results->result("array");

        $data = array();
        $data['spot_ad_seconds'] = 0;
        $data['banner_ad_seconds'] = 0;

        foreach ($fcts as $value) {
            if ($value['screen_region_id'] == 1) {
                $data['spot_ad_seconds'] += $value['total_ad_seconds'];
            } else if ($value['screen_region_id'] == 3) {
                $data['banner_ad_seconds'] += $value['total_ad_seconds'];
            }

        }
        return $data;
    }

    public function get_external_ro_report_details($internal_ro)
    {
        //$query = "select ac.customer_ro_number,ac.internal_ro_number,ac.client_name,ac.agency_name,MIN(ac.start_date) as start_date,MAX(ac.end_date) as end_date,r.ro_amount as gross_ro_amount,r.agency_commission_amount,r.ro_amount*(r.agency_rebate /100) as agency_rebate,(r.marketing_promotion_amount+r.field_activation_amount+r.sales_commissions_amount+r.creative_services_amount+r.other_expenses_amount) as other_expenses from  sv_advertiser_campaign as ac,ro_amount as r where r.internal_ro_number = '$internal_ro'and ac.internal_ro_number = '$internal_ro'";

        // query changed by Lokanath as agency rebate calculation is now both on ro_amount and net_amount

        $query = "select ac.customer_ro_number,ac.internal_ro_number,ac.client_name,ac.agency_name,MIN(ac.start_date) as start_date,MAX(ac.end_date) as end_date,r.ro_amount as gross_ro_amount,r.agency_commission_amount,r.agency_rebate as agency_rebate, r.agency_rebate_on, (r.marketing_promotion_amount+r.field_activation_amount+r.sales_commissions_amount+r.creative_services_amount+r.other_expenses_amount) as other_expenses from  sv_advertiser_campaign as ac,ro_amount as r where r.internal_ro_number = '$internal_ro'and ac.internal_ro_number = '$internal_ro'";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function convert_data_into_structure_bkp($data)
    {
        $result = array();

        foreach ($data as $val) {
            $client = trim($val['client']);
            if (!isset($result[$client])) {
                $result[$client] = array();

                $result[$client]['client'] = trim($val['client']);
                $result[$client]['internal_ro'] = trim($val['internal_ro']);
                $result[$client]['revenue'] = trim($val['revenue']);
                $result[$client]['network_payout'] = trim($val['network_payout']);
                $result[$client]['sw_net_contribute'] = trim($val['sw_net_contribute']);
                $result[$client]['total_internal_ro'] = 1;
                $result[$client]['net_contribuition_percent'] = round(($val['sw_net_contribute'] / $val['revenue']) * 100);

            } else {
                if ($client == trim($result[$client]['client'])) {
                    $result[$client]['total_internal_ro'] = $result[$client]['total_internal_ro'] + 1;
                    $result[$client]['revenue'] = $result[$client]['revenue'] + $val['revenue'];
                    $result[$client]['network_payout'] = $result[$client]['network_payout'] + $val['network_payout'];
                    $result[$client]['sw_net_contribute'] = $result[$client]['sw_net_contribute'] + $val['sw_net_contribute'];
                    $result[$client]['net_contribuition_percent'] = round(($result[$client]['sw_net_contribute'] / $result[$client]['revenue']) * 100);
                }
            }
        }
        return $result;
    }

    public function getMonthlyMisReport_bkp($data)
    {
        $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
            . "<th><b>Client Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Network Payout(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th>"
            . "</tr>";
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;

        foreach ($data as $val) {
            $structure = $structure . "<tr>";
            $structure = $structure . "<td>" . $val['client'] . "</td>";
            $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
            $structure = $structure . "<td>" . $val['revenue'] . "</td>";
            $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
            $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
            $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
            $structure = $structure . "</tr>";

            $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
            $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
            $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
            $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
        }
        $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
        $structure = $structure . "<td>" . $total_mnth_revenue . "</td>";
        $structure = $structure . "<td>" . $total_mnth_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_mnth_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_mnth_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;
    }

    public function getFctMonthlyMisReport($data)
    {
        $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
            . "<th><b>Region</b></th>"
            . "<th><b>Client Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Network Payout(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th>"
            . "</tr>";
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;

        foreach ($data as $client => $regionArray) {
            foreach ($regionArray as $region => $value) {
                foreach ($value as $key => $val) {
                    //$region = $key ;
                    $structure = $structure . "<tr>";
                    $structure = $structure . "<td>" . $val['region'] . "</td>";
                    $structure = $structure . "<td>" . $val['client'] . "</td>";
                    $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
                    $structure = $structure . "<td>" . $val['revenue'] . "</td>";
                    $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
                    $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
                    $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
                    $structure = $structure . "</tr>";

                    $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
                    $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
                    $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
                    $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
                }
            }
        }
        $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
        $structure = $structure . "<td>" . $total_mnth_revenue . "</td>";
        $structure = $structure . "<td>" . $total_mnth_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_mnth_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_mnth_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;
    }

    //return channel_id and channel_name for that market

    public function getMonthlyMisReport($data)
    {
        $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
            . "<th><b>Region</b></th>"
            . "<th><b>Client Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Network Payout(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th>"
            . "</tr>";
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;

        foreach ($data as $val) {
            $structure = $structure . "<tr>";
            $structure = $structure . "<td>" . $val['region'] . "</td>";
            $structure = $structure . "<td>" . $val['client'] . "</td>";
            $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
            $structure = $structure . "<td>" . $val['revenue'] . "</td>";
            $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
            $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
            $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
            $structure = $structure . "</tr>";

            $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
            $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
            $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
            $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
        }
        $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
        $structure = $structure . "<td>" . $total_mnth_revenue . "</td>";
        $structure = $structure . "<td>" . $total_mnth_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_mnth_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_mnth_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;
    }

    public function getMonthlyMisReport_region($data, $regionName, $regionId, $givenDate)
    {
        /*$structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
            ."<th><b>Client Name</b></th>"
            ."<th><b>No. Ro's</b></th>"
            ."<th><b>Revenue(INR)</b></th>"
            ."<th><b>Network Payout(INR)</b></th>"
            ."<th><b>Net Contribution(INR)</b></th>"
            ."<th><b>Net Contribution %</b></th>"
            ."</tr>";*/
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;

        foreach ($data as $val) {
            $structure = "<tr>";
            $structure = $structure . "<td>" . $regionName . "</td>";
            $structure = $structure . "<td>" . $val['client'] . "</td>";
            $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
            $structure = $structure . "<td>" . $val['revenue'] . "</td>";
            $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
            $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
            $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
            $structure = $structure . "</tr>";

            $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
            $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
            $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
            $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
        }
        $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);


        //commented to not show total as per regions
        /*$structure = $structure."<tr>";
        $structure = $structure."<td>#</td>";
        $structure = $structure."<td>#</td>";
        $structure = $structure."<td>".$total_mnth_ro."</td>";
        $structure = $structure."<td>".$total_mnth_revenue."</td>";
        $structure = $structure."<td>".$total_mnth_network_payout_val."</td>";
        $structure = $structure."<td>".$total_mnth_sw_net_contribution."</td>";
        $structure = $structure."<td>".$net_mnth_contribuition_percent." %</td>";
        $structure = $structure."</tr>" ;*/

        /*$structure = $structure."</table>" ;*/

        $this->checkAndInsertUpdateRoMisRegionsReport($regionName, $regionId, $givenDate, $total_mnth_ro,
            $total_mnth_revenue, $total_mnth_network_payout_val, $total_mnth_sw_net_contribution, $net_mnth_contribuition_percent, 1);

        return $structure;
    }

    public function checkAndInsertUpdateRoMisRegionsReport($regionName, $regionId, $date, $roCount, $revenue, $networkPayout, $netContribution, $netContributionPercentage, $fct)
    {

        $whereArray = array(" region_id =" . "'$regionId'",
            " date = " . "'$date'",
            " is_fct = " . "'$fct'"
        );
        $type = " AND ";

        $resultArray = $this->selectFromMisRegionReportWithOutRegion($whereArray, $type);

        if (count($resultArray) > 0) {

            $this->updateIntoMisRegionReport($regionId, $date, $roCount, $revenue, $networkPayout, $netContribution, $netContributionPercentage, $fct);

        } else {

            $this->insertIntoMisRegionReport($regionName, $regionId, $date, $roCount, $revenue, $networkPayout, $netContribution, $netContributionPercentage, $fct);

        }

        return 1;
    }

    function selectFromMisRegionReportWithOutRegion($whereData, $type)
    {

        $whereData = implode($type, $whereData);

        $query = " SELECT region_id, region_name,
                              sum(ro_count) AS ro_count,
                              sum(revenue) AS revenue,
                              sum(network_payout) AS network_payout,
                              sum(net_contribution) AS net_contribution
                       FROM ro_mis_regions_report
                       WHERE " . $whereData . "
                       GROUP BY region_name ";

        $queryObj = $this->db->query($query);

        return $queryObj->result("array");

    }

    public function updateIntoMisRegionReport($regionId, $date, $roCount, $revenue, $networkPayout, $netContribution, $netContributionPercentage, $fct)
    {

        $updateQuery = "UPDATE ro_mis_regions_report
                            SET ro_count = '$roCount',
                                revenue = '$revenue',
                                network_payout = '$networkPayout',
                                net_contribution = '$netContribution',
                                net_contribution_percentage = '$netContributionPercentage'
                            WHERE region_id = '$regionId'
                              AND date = '$date'
                              AND is_fct = '$fct' ";

        $this->db->query($updateQuery);

    }

    public function insertIntoMisRegionReport($regionName, $regionId, $date, $roCount, $revenue, $networkPayout, $netContribution, $netContributionPercentage, $fct)
    {

        $query = " INSERT INTO ro_mis_regions_report (region_name, region_id, date, ro_count, revenue, network_payout, net_contribution, net_contribution_percentage, is_fct)
                        VALUES('$regionName',
                               '$regionId',
                               '$date',
                               '$roCount',
                               '$revenue',
                               '$networkPayout',
                               '$netContribution',
                               '$netContributionPercentage',
                               '$fct' ) ";

        $this->db->query($query);

    }

    public function getMonthlyNonFctMisReport_bkp($data)
    {
        $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
            . "<th><b>Client Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Expenses(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th>"
            . "</tr>";
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;

        foreach ($data as $val) {
            $structure = $structure . "<tr>";
            $structure = $structure . "<td>" . $val['client'] . "</td>";
            $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
            $structure = $structure . "<td>" . $val['revenue'] . "</td>";
            $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
            $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
            $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
            $structure = $structure . "</tr>";

            $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
            $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
            $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
            $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
        }
        $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
        $structure = $structure . "<td>" . $total_mnth_revenue . "</td>";
        $structure = $structure . "<td>" . $total_mnth_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_mnth_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_mnth_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;
    }

    public function getMonthlyNonFctMisReport($data)
    {
        $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
            . "<th><b>Region</b></th>"
            . "<th><b>Client Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Expenses(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th>"
            . "</tr>";
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;

        foreach ($data as $client => $regionArray) {
            foreach ($regionArray as $region => $value) {
                foreach ($value as $key => $val) {
                    //$region = $key ;
                    $structure = $structure . "<tr>";
                    $structure = $structure . "<td>" . $val['region'] . "</td>";
                    $structure = $structure . "<td>" . $val['client'] . "</td>";
                    $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
                    $structure = $structure . "<td>" . $val['revenue'] . "</td>";
                    $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
                    $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
                    $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
                    $structure = $structure . "</tr>";

                    $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
                    $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
                    $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
                    $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
                }
            }
        }
        $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
        $structure = $structure . "<td>" . $total_mnth_revenue . "</td>";
        $structure = $structure . "<td>" . $total_mnth_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_mnth_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_mnth_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;
    }

    //For Market

    public function getMonthlyNonFctMisReport_region($data, $givenDate, $regionName, $regionId)
    {
        /*$structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
                                      ."<th><b>Client Name</b></th>"
                                        ."<th><b>No. Ro's</b></th>"
                                        ."<th><b>Revenue(INR)</b></th>"
                                        ."<th><b>Expenses(INR)</b></th>"
                                        ."<th><b>Net Contribution(INR)</b></th>"
                                        ."<th><b>Net Contribution %</b></th>"
                                     ."</tr>";*/
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;

        foreach ($data as $val) {
            $structure = "<tr>";
            $structure = $structure . "<td>" . $regionName . "</td>";
            $structure = $structure . "<td>" . $val['client'] . "</td>";
            $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
            $structure = $structure . "<td>" . $val['revenue'] . "</td>";
            $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
            $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
            $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
            $structure = $structure . "</tr>";

            $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
            $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
            $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
            $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
        }
        $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);

        //commented to not show total as per regions
        /*$structure = $structure."<tr>";
            $structure = $structure."<td>#</td>";
            $structure = $structure."<td>".$total_mnth_ro."</td>";
            $structure = $structure."<td>".$total_mnth_revenue."</td>";
            $structure = $structure."<td>".$total_mnth_network_payout_val."</td>";
            $structure = $structure."<td>".$total_mnth_sw_net_contribution."</td>";
            $structure = $structure."<td>".$net_mnth_contribuition_percent." %</td>";
            $structure = $structure."</tr>" ;*/

        /*$structure = $structure."</table>" ;*/

        $this->checkAndInsertUpdateRoMisRegionsReport($regionName, $regionId, $givenDate, $total_mnth_ro,
            $total_mnth_revenue, $total_mnth_network_payout_val, $total_mnth_sw_net_contribution, $net_mnth_contribuition_percent, 0);

        return $structure;
    }

    public function daily_email_report_details_test()
    {
        // summary for current month for each client
        $month = date("m");
        $current_month_start_date = date("Y-m-01");
        $current_month_end_date = date("Y-m-t");

        $am_query = "select am.id,am.client,am.internal_ro,am.cust_ro,reg.region_name from ro_am_external_ro as am,ro_external_ro_report_details as nr,ro_master_geo_regions as reg where am.internal_ro=nr.internal_ro_number and reg.id = am.region_id and test_user_creation=1";
        $am_query = $am_query . " and (( camp_start_date >= '$current_month_start_date' and camp_end_date <= '$current_month_end_date' )
                                                or
                ( camp_start_date <= '$current_month_start_date' and camp_end_date between '$current_month_start_date' and '$current_month_end_date')
                                                or
                ( camp_start_date between '$current_month_start_date' and '$current_month_end_date' and camp_end_date >= '$current_month_end_date' )
                                                or
                ( camp_start_date <= '$current_month_start_date' and camp_end_date >= '$current_month_end_date' ))";
        $am_query = $am_query . " order by client";

        $am_query_res = $this->db->query($am_query);
        $data = array();

        if ($am_query_res->num_rows() != 0) {
            $structure = "";
            $am_res = $am_query_res->result("array");
            foreach ($am_res as $am_data) {
                $client_name = $am_data['client'];
                //$ext_ro = $am_data['cust_ro'];
                $am_ext_id = $am_data['id'];
                $order_id = $am_data['internal_ro'];
                $region = $am_data['region_name'];
                $proportional_calculation = $this->time_period_proportional_calculation($am_ext_id, $order_id, $current_month_start_date, $current_month_end_date);

                $tmp = array();
                $tmp['client'] = $client_name;
                $tmp['internal_ro'] = $order_id;
                $tmp['region'] = $region;
                $tmp['revenue'] = floor($proportional_calculation['revenue']);
                $tmp['network_payout'] = floor($proportional_calculation['total_nw_payout']);
                $tmp['sw_net_contribute'] = floor($proportional_calculation['total_sw_net_contribution']);
                $tmp['net_contribuition_percent'] = round(($proportional_calculation['total_sw_net_contribution'] / $proportional_calculation['revenue']) * 100);

                array_push($data, $tmp);

                /*$data .=  "<br>"."Client Name : ".$client_name."<br>";
				$data .=  "External RO : ".$ext_ro."<br>";
				$data .=  "Revenue : ".$revenue."<br>";
				$data .=  "Total Network Payout : ".$total_network_payout."<br>";
				$data .=  "Surewaves Net Contribution : ".$total_sw_net_contribution."<br>"; */
            }//echo $data;
            //for converting into client wise
            $data = $this->convert_data_into_structure($data);

            $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
                . "<th><b>Client Name</b></th>"
                . "<th><b>No. Ro's</b></th>"
                . "<th><b>Revenue(INR)</b></th>"
                . "<th><b>Network Payout(INR)</b></th>"
                . "<th><b>Net Contribution(INR)</b></th>"
                . "<th><b>Net Contribution %</b></th>"
                . "</tr>";
            $total_mnth_ro = 0;
            $total_mnth_revenue = 0;
            $total_mnth_network_payout_val = 0;
            $total_mnth_sw_net_contribution = 0;

            foreach ($data as $val) {
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $val['client'] . "</td>";
                $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
                $structure = $structure . "<td>" . $val['revenue'] . "</td>";
                $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
                $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
                $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
                $structure = $structure . "</tr>";

                $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
                $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
                $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
                $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
            }
            $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);

            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
            $structure = $structure . "<td>" . $total_mnth_revenue . "</td>";
            $structure = $structure . "<td>" . $total_mnth_network_payout_val . "</td>";
            $structure = $structure . "<td>" . $total_mnth_sw_net_contribution . "</td>";
            $structure = $structure . "<td>" . $net_mnth_contribuition_percent . " %</td>";
            $structure = $structure . "</tr>";

            $structure = $structure . "</table>";
            return $structure;
        }
    }

    public function time_period_proportional_calculation($am_ext_id, $internal_ro, $given_start_date, $given_end_date, $userType, $financial_year)
    {
        $market_price_data = array('ro_id' => $am_ext_id);
        $market_price = $this->get_market_ro_price($market_price_data);
        log_message('INFO', 'In mg_model@time_period_proportional_calculation | $market_price' . print_r($market_price, true));
        $ro_amount_values = $this->get_ro_amount($internal_ro);
        log_message('INFO', 'In mg_model@time_period_proportional_calculation | $ro_amount_values' . print_r($ro_amount_values, true));
        $nw_payout = 0;
        $ro_value_market = 0;

        foreach ($market_price as $mp) {
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | for each market - ' . print_r($mp, true));
            $market_name = $mp['market'];
            $spot_market_price = $mp['spot_price'];
            $banner_market_price = $mp['banner_price'];

            //* remaining get channel_id and channel_name for that market
            //$channel_detail = $this->get_channel_for_market($market_name) ;
            $channel_detail = $this->get_scheduled_channel_for_market($market_name, $internal_ro);
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | $channel_detail - ' . print_r($channel_detail, true));

            $scheduled_spot_seconds = 0;
            $scheduled_banner_seconds = 0;
            $total_spot_second = 0;
            $total_banner_second = 0;
            $channel_payout = 0;
            $total_channel_payout = 0;
            $maxmChannelsFct = array();

            foreach ($channel_detail as $chnl) {
                log_message('INFO', 'In mg_model@time_period_proportional_calculation | for each $channel_detail - ' . print_r($chnl, true));
                $channel_id = $chnl['tv_channel_id'];
                $market_id = $chnl['market_id'];

                //get data from ro_approved_network
                $approved_nw_data = $this->get_approved_data_for_customer_channel_ro(array('tv_channel_id' => $channel_id, 'internal_ro_number' => $internal_ro));
                log_message('INFO', 'In mg_model@time_period_proportional_calculation | $approved_nw_data - ' . print_r($approved_nw_data, true));
                //find scheduled fct for given time period
                $scheduled_impression = $this->get_scheduled_impression_for_time_period($channel_id, $internal_ro, $given_start_date, $given_end_date, $market_id);
                log_message('INFO', 'In mg_model@time_period_proportional_calculation | $scheduled_impression - ' . print_r($scheduled_impression, true));
                $total_scheduled_seconds = $this->get_total_scheduled_seconds($internal_ro, $channel_id, $market_id);
                log_message('INFO', 'In mg_model@time_period_proportional_calculation | $total_scheduled_seconds - ' . print_r($total_scheduled_seconds, true));

                $isExistScheduleImpression = !empty($scheduled_impression['spot_ad_seconds']) || !empty($scheduled_impression['banner_ad_seconds']);
                $isExistTotalScheduleImpression = !empty($total_scheduled_seconds['spot_ad_seconds']) || !empty($total_scheduled_seconds['banner_ad_seconds']);
                $isApprovalNwDataAvailable = isset($approved_nw_data[0]['total_spot_ad_seconds']) || isset($approved_nw_data[0]['total_banner_ad_seconds']);

                if (($isExistScheduleImpression || $isExistTotalScheduleImpression) && ($isApprovalNwDataAvailable)) {
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | Inside IF');

                    $scheduled_spot_seconds = $scheduled_spot_seconds + $scheduled_impression['spot_ad_seconds'];
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $scheduled_spot_seconds - ' . print_r($scheduled_spot_seconds, true));
                    $scheduled_banner_seconds = $scheduled_banner_seconds + $scheduled_impression['banner_ad_seconds'];
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $scheduled_banner_seconds - ' . print_r($scheduled_banner_seconds, true));

                    $total_spot_second = $total_spot_second + $total_scheduled_seconds['spot_ad_seconds'];
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $total_spot_second - ' . print_r($total_spot_second, true));
                    $total_banner_second = $total_banner_second + $total_scheduled_seconds['banner_ad_seconds'];
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $total_banner_second - ' . print_r($total_banner_second, true));

                    if (!isset($maxmChannelsFct['maxmTotalSpotFct'])) {
                        $maxmChannelsFct['maxmScheduledSpotFct'] = $scheduled_impression['spot_ad_seconds'];
                        $maxmChannelsFct['maxmTotalSpotFct'] = $total_scheduled_seconds['spot_ad_seconds'];
                    } else {
                        if ($total_scheduled_seconds['spot_ad_seconds'] > $maxmChannelsFct['maxmTotalSpotFct']) {
                            $maxmChannelsFct['maxmScheduledSpotFct'] = $scheduled_impression['spot_ad_seconds'];
                            $maxmChannelsFct['maxmTotalSpotFct'] = $total_scheduled_seconds['spot_ad_seconds'];
                        }
                    }
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $maxmChannelsFct - ' . print_r($maxmChannelsFct, true));
                    if (!isset($maxmChannelsFct['maxmTotalBannerFct'])) {
                        $maxmChannelsFct['maxmScheduledBannerFct'] = $scheduled_impression['banner_ad_seconds'];
                        $maxmChannelsFct['maxmTotalBannerFct'] = $total_scheduled_seconds['banner_ad_seconds'];
                    } else {
                        if ($total_scheduled_seconds['banner_ad_seconds'] > $maxmChannelsFct['maxmTotalBannerFct']) {
                            $maxmChannelsFct['maxmScheduledBannerFct'] = $scheduled_impression['banner_ad_seconds'];
                            $maxmChannelsFct['maxmTotalBannerFct'] = $total_scheduled_seconds['banner_ad_seconds'];
                        }
                    }
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $maxmChannelsFct - ' . print_r($maxmChannelsFct, true));

                    $spot_amount = ($scheduled_impression['spot_ad_seconds'] * $approved_nw_data[0]['channel_spot_avg_rate'] * ($approved_nw_data[0]['customer_share'] / 100)) / 10;
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $spot_amount - ' . print_r($spot_amount, true));

                    $banner_amount = ($scheduled_impression['banner_ad_seconds'] * $approved_nw_data[0]['channel_banner_avg_rate'] * ($approved_nw_data[0]['customer_share'] / 100)) / 10;
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $banner_amount - ' . print_r($banner_amount, true));

                    $channel_payout = $spot_amount + $banner_amount;
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $channel_payout - ' . print_r($channel_payout, true));

                    $total_channel_payout += $channel_payout;
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $total_channel_payout - ' . print_r($total_channel_payout, true));

                    if (!isset($scheduled_impression['start_date'])) {
                        $scheduled_impression['start_date'] = $given_start_date;
                    }
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $scheduled_impression - ' . print_r($scheduled_impression, true));
                    if (!isset($scheduled_impression['end_date'])) {
                        $scheduled_impression['end_date'] = $given_end_date;
                    }
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $scheduled_impression - ' . print_r($scheduled_impression, true));
                    // if( strtotime($given_end_date) > strtotime("now") ){
                    $whereData = array(
                        'ro_id' => $am_ext_id,
                        'internal_ro_number' => $internal_ro,
                        'customer_id' => $approved_nw_data[0]['customer_id'],
                        'client' => $approved_nw_data[0]['client_name'],
                        'channel_id' => $channel_id,
                        'month_name' => date("F", strtotime($given_start_date)),
                        'financial_year' => $financial_year,
                        'is_fct' => 1,
                        'user_type' => $userType,
                        'market_id' => $market_id
                    );
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | Find details from ro_mis_report - ' . print_r($whereData, true));
                    $getMisData = $this->getMisDataForTesting($whereData);
                    log_message('INFO', 'In mg_model@time_period_proportional_calculation | $getMisData - ' . print_r($getMisData, true));
                    if (count($getMisData) > 0) {
                        $updateDataArray = array(
                            'channel_payout' => $channel_payout,
                            'spot_amount' => $spot_amount,
                            'banner_amount' => $banner_amount,
                            'scheduled_spot_impression' => $scheduled_impression['spot_ad_seconds'],
                            'scheduled_banner_impression' => $scheduled_impression['banner_ad_seconds'],
                            'start_date' => $scheduled_impression['start_date'],
                            'end_date' => $scheduled_impression['end_date']
                        );
                        log_message('INFO', 'In mg_model@time_period_proportional_calculation | updateMisDataForTesting - ' . print_r($updateDataArray, true));
                        $this->updateMisDataForTesting($updateDataArray, $whereData);
                    } else {
                        $insertDataArray = array(
                            'ro_id' => $am_ext_id,
                            'internal_ro_number' => $internal_ro,
                            'customer_id' => $approved_nw_data[0]['customer_id'],
                            'customer_name' => $approved_nw_data[0]['customer_name'],
                            'client' => $approved_nw_data[0]['client_name'],
                            'channel_id' => $channel_id,
                            'channel_payout' => $channel_payout,
                            'spot_amount' => $spot_amount,
                            'banner_amount' => $banner_amount,
                            'scheduled_spot_impression' => $scheduled_impression['spot_ad_seconds'],
                            'scheduled_banner_impression' => $scheduled_impression['banner_ad_seconds'],
                            'month_name' => date("F", strtotime($given_start_date)),
                            'start_date' => $scheduled_impression['start_date'],
                            'end_date' => $scheduled_impression['end_date'],
                            'billing_name' => $approved_nw_data[0]['billing_name'],
                            'financial_year' => $financial_year,
                            'is_fct' => 1,
                            'user_type' => $userType,
                            'market_id' => $market_id
                        );
                        log_message('INFO', 'In mg_model@time_period_proportional_calculation | insertIntoTableForMisTesting - ' . print_r($insertDataArray, true));
                        $this->insertIntoTableForMisTesting($insertDataArray);

                    }
                    // }
                } else {
                    continue;
                }

            }

            //$average_spot_fraction = $scheduled_spot_seconds/$total_spot_second ;
            //$average_banner_fraction = $scheduled_banner_seconds/$total_banner_second ;
            $average_spot_fraction = 0.0;
            if (isset($maxmChannelsFct['maxmTotalSpotFct']) && !empty($maxmChannelsFct['maxmTotalSpotFct']) && $maxmChannelsFct['maxmTotalSpotFct'] != 0) {
                $average_spot_fraction = $maxmChannelsFct['maxmScheduledSpotFct'] / $maxmChannelsFct['maxmTotalSpotFct'];
            }
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | $average_spot_fraction - ' . print_r($average_spot_fraction, true));

            $average_banner_fraction = 0.0;
            if (isset($maxmChannelsFct['maxmTotalBannerFct']) && !empty($maxmChannelsFct['maxmTotalBannerFct']) && $maxmChannelsFct['maxmTotalBannerFct'] != 0) {
                $average_banner_fraction = $maxmChannelsFct['maxmScheduledBannerFct'] / $maxmChannelsFct['maxmTotalBannerFct'];
            }
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | $average_banner_fraction - ' . print_r($average_banner_fraction, true));

            if ($average_spot_fraction > 1) $average_spot_fraction = 1;
            if ($average_banner_fraction > 1) $average_banner_fraction = 1;
            $market_price_val = $spot_market_price * $average_spot_fraction + $banner_market_price * $average_banner_fraction;
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | $market_price_val - ' . print_r($market_price_val, true));

            $nw_payout += $total_channel_payout;
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | $nw_payout - ' . print_r($nw_payout, true));

            $ro_value_market += $market_price_val;
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | $ro_value_market - ' . print_r($ro_value_market, true));
        }
        $ro_fraction = 0.0;
        if (isset($ro_amount_values[0]['ro_amount']) && !empty($ro_amount_values[0]['ro_amount']) && $ro_amount_values[0]['ro_amount'] != 0) {
            $ro_fraction = $ro_value_market / $ro_amount_values[0]['ro_amount'];
        }
        log_message('INFO', 'In mg_model@time_period_proportional_calculation | $ro_fraction - ' . print_r($ro_fraction, true));
        $agency_commission = $ro_amount_values[0]['agency_commission_amount'] * $ro_fraction;
        log_message('INFO', 'In mg_model@time_period_proportional_calculation | $agency_commission - ' . print_r($agency_commission, true));
        $result = array();

        $result['revenue'] = ($ro_value_market - $agency_commission) * SERVICE_TAX;
        $result['total_nw_payout'] = $nw_payout;

        $agency_rebate = 0;
        if ($ro_amount_values[0]['agency_rebate_on'] == "ro_amount") {
            $agency_rebate = $ro_value_market * ($ro_amount_values[0]['agency_rebate'] / 100);
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | $agency_rebate - ' . print_r($agency_rebate, true));
        } else {
            $agency_rebate = ($ro_value_market - $agency_commission) * ($ro_amount_values[0]['agency_rebate'] / 100);
            log_message('INFO', 'In mg_model@time_period_proportional_calculation | $agency_rebate - ' . print_r($agency_rebate, true));
        }
        //$agency_rebate = $agency_rebate*$ro_fraction ;
        $other_expenses_ary = $this->get_external_ro_report_details($internal_ro);
        log_message('INFO', 'In mg_model@time_period_proportional_calculation | $other_expenses_ary - ' . print_r($other_expenses_ary, true));
        $other_expenses = $other_expenses_ary[0]['other_expenses'] * $ro_fraction;
        log_message('INFO', 'In mg_model@time_period_proportional_calculation | $other_expenses - ' . print_r($other_expenses, true));

        $actual_net_amount = $ro_value_market - $agency_commission - $agency_rebate - $other_expenses;
        log_message('INFO', 'In mg_model@time_period_proportional_calculation | $actual_net_amount - ' . print_r($actual_net_amount, true));

        $result['total_sw_net_contribution'] = $actual_net_amount - $nw_payout;

        log_message('INFO', 'In mg_model@time_period_proportional_calculation | $result - ' . print_r($result, true));
        return $result;

    }

    public function getMisDataForTesting($data)
    {
        $result = $this->db->get_where('ro_mis_report', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateMisDataForTesting($data, $where)
    {
        $this->db->update('ro_mis_report', $data, $where);
    }

    public function insertIntoTableForMisTesting($userData)
    {
        $this->db->insert('ro_mis_report', $userData);
    }

    public function daily_email_report_details_advance_ro($givenDate)
    {
        // summary for current month for each client

        $current_month_start_date = date("Y-m-01", strtotime($givenDate));
        $current_month_end_date = date("Y-m-t", strtotime($givenDate));

        $am_query = "select am.id,am.client,am.internal_ro,am.cust_ro from ro_am_external_ro as am,ro_external_ro_report_details as nr where am.internal_ro=nr.internal_ro_number and test_user_creation !=1 and am.id not in (select ro_id from ro_linked_advance_ro)";
        $am_query = $am_query . " and (( camp_start_date >= '$current_month_start_date' and camp_end_date <= '$current_month_end_date' )
                                                or
                ( camp_start_date <= '$current_month_start_date' and camp_end_date between '$current_month_start_date' and '$current_month_end_date')
                                                or
                ( camp_start_date between '$current_month_start_date' and '$current_month_end_date' and camp_end_date >= '$current_month_end_date' )
                                                or
                ( camp_start_date <= '$current_month_start_date' and camp_end_date >= '$current_month_end_date' ))";
        $am_query = $am_query . " order by client";

        $am_query_res = $this->db->query($am_query);
        $data = array();

        if ($am_query_res->num_rows() != 0) {
            $structure = "";
            $am_res = $am_query_res->result("array");
            foreach ($am_res as $am_data) {
                $client_name = $am_data['client'];
                //$ext_ro = $am_data['cust_ro'];
                $am_ext_id = $am_data['id'];
                $order_id = $am_data['internal_ro'];
                $proportional_calculation = $this->time_period_proportional_calculation_advance($am_ext_id, $order_id, $current_month_start_date, $current_month_end_date);

                $tmp = array();
                $tmp['client'] = $client_name;
                $tmp['internal_ro'] = $order_id;
                $tmp['revenue'] = floor($proportional_calculation['revenue']);
                $tmp['network_payout'] = floor($proportional_calculation['total_nw_payout']);
                $tmp['sw_net_contribute'] = floor($proportional_calculation['total_sw_net_contribution']);
                $tmp['net_contribuition_percent'] = round(($proportional_calculation['total_sw_net_contribution'] / $proportional_calculation['revenue']) * 100);

                array_push($data, $tmp);

                /*$data .=  "<br>"."Client Name : ".$client_name."<br>";
				$data .=  "External RO : ".$ext_ro."<br>";
				$data .=  "Revenue : ".$revenue."<br>";
				$data .=  "Total Network Payout : ".$total_network_payout."<br>";
				$data .=  "Surewaves Net Contribution : ".$total_sw_net_contribution."<br>"; */
            }//echo $data;
            //for converting into client wise
            $data = $this->convert_data_into_structure_bkp($data);

            $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
                . "<th><b>Client Name</b></th>"
                . "<th><b>No. Ro's</b></th>"
                . "<th><b>Revenue(INR)</b></th>"
                . "<th><b>Network Payout(INR)</b></th>"
                . "<th><b>Net Contribution(INR)</b></th>"
                . "<th><b>Net Contribution %</b></th>"
                . "</tr>";
            $total_mnth_ro = 0;
            $total_mnth_revenue = 0;
            $total_mnth_network_payout_val = 0;
            $total_mnth_sw_net_contribution = 0;

            foreach ($data as $val) {
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $val['client'] . "</td>";
                $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
                $structure = $structure . "<td>" . $val['revenue'] . "</td>";
                $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
                $structure = $structure . "<td>" . $val['sw_net_contribute'] . "</td>";
                $structure = $structure . "<td>" . $val['net_contribuition_percent'] . " %</td>";
                $structure = $structure . "</tr>";

                $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
                $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
                $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
                $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $val['sw_net_contribute'];
            }
            $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100);

            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
            $structure = $structure . "<td>" . $total_mnth_revenue . "</td>";
            $structure = $structure . "<td>" . $total_mnth_network_payout_val . "</td>";
            $structure = $structure . "<td>" . $total_mnth_sw_net_contribution . "</td>";
            $structure = $structure . "<td>" . $net_mnth_contribuition_percent . " %</td>";
            $structure = $structure . "</tr>";

            $structure = $structure . "</table>";
            return $structure;
        }
    }

    public function current_financial_email_report_details_advance_ro($givenDate)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));

        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("$to_year-03-31");

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Month Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Network Payout(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th><tr>";

        $total_yr_ro = 0;
        $total_yr_revenue = 0;
        $total_yr_network_payout_val = 0;
        $total_yr_sw_net_contribution = 0;
        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {
            $start_date = date("Y-m-d", $i);
            if (date("m", $i) == date("m")) {
                $end_date = date("Y-m-t");
            } else {
                $end_date = date("Y-m-t", strtotime("+1 month -1 days", $i));
            }
            // echo $start_date."---".$end_date."<br/>";
            $am_query = "select am.internal_ro,am.cust_ro,am.id from ro_am_external_ro as am, ro_external_ro_report_details as nr where am.internal_ro=nr.internal_ro_number and test_user_creation !=1 and am.id not in (select ro_id from ro_linked_advance_ro)";
            $am_query = $am_query . " and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
                                                or
                ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' ))";

            $am_query_res = $this->db->query($am_query);

            if ($am_query_res->num_rows() != 0) {
                $no_of_ro = $am_query_res->num_rows();

                $total_revenue = 0;
                $total_network_payout_val = 0;
                $total_sw_net_contribution = 0;
                //$net_contribution = 0 ;
                $am_res = $am_query_res->result("array");
                foreach ($am_res as $am_data) {
                    $am_ext_id = $am_data['id'];
                    //$ext_ro = $am_data['cust_ro'];
                    $order_id = $am_data['internal_ro'];

                    $proportional_calculation = $this->time_period_proportional_calculation_advance($am_ext_id, $order_id, $start_date, $end_date);

                    $total_revenue = $total_revenue + $proportional_calculation['revenue'];
                    $total_network_payout_val = $total_network_payout_val + $proportional_calculation['total_nw_payout'];
                    $total_sw_net_contribution = $total_sw_net_contribution + $proportional_calculation['total_sw_net_contribution'];
                }
                $net_contribuition_percent = round(($total_sw_net_contribution / $total_revenue) * 100);
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . date("M-Y", $i) . "</td>";
                $structure = $structure . "<td>" . $no_of_ro . "</td>";
                $structure = $structure . "<td>" . floor($total_revenue) . "</td>";
                $structure = $structure . "<td>" . floor($total_network_payout_val) . "</td>";
                $structure = $structure . "<td>" . floor($total_sw_net_contribution) . "</td>";
                $structure = $structure . "<td>" . $net_contribuition_percent . " %</td>";
                $structure = $structure . "</tr>";

                $total_yr_ro = $total_yr_ro + $no_of_ro;
                $total_yr_revenue = $total_yr_revenue + floor($total_revenue);
                $total_yr_network_payout_val = $total_yr_network_payout_val + floor($total_network_payout_val);
                $total_yr_sw_net_contribution = $total_yr_sw_net_contribution + floor($total_sw_net_contribution);
            }
        }
        $net_yr_contribuition_percent = round(($total_yr_sw_net_contribution / $total_yr_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_yr_ro . "</td>";
        $structure = $structure . "<td>" . $total_yr_revenue . "</td>";
        $structure = $structure . "<td>" . $total_yr_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_yr_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_yr_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";


        $structure = $structure . "</table>";
        return $structure;

    }

    public function get_channel_for_market($market_name)
    {
        /*$query = "select distinct stc.tv_channel_id,stc.channel_name  from sv_tv_channel stc
                        join sv_tam_channel_market stcm ON stcm.tv_channel_id = stc.tv_channel_id
                        join sv_tam_market stm ON stcm.tam_market_id = stm.tam_market_id
                        join sv_sw_tam_market ssm on ssm.tam_market_id = stcm.tam_market_id
                        join sv_sw_market swm on swm.id = ssm.sw_market_id where sw_market_name='$market_name' order by tv_channel_id " ; */


        $query = "select distinct stc.tv_channel_id,stc.channel_name,sm.id as market_id from sv_tv_channel stc 
                       inner join sv_market_x_channel smc ON stc.tv_channel_id = smc.channel_fk_id
                       inner join sv_sw_market sm ON sm.id = smc.market_fk_id 
                       where sm.sw_market_name='$market_name' order by tv_channel_id ";

        $result = $this->db->query($query);
        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
        return array();

    }

    public function getScheduledMarketForChannel($channelNames, $internal_ro)
    {
        $channel_string = '';
        foreach ($channelNames as $chnl) {
            $chnl_value = explode(",", $chnl);
            if (empty($channel_string) || !isset($channel_string)) {
                $channel_string = "'" . implode("','", $chnl_value) . "'";
            } else {
                $new_chnl_string = "'" . implode("','", $chnl_value) . "'";
                $channel_string = $channel_string . "," . $new_chnl_string;
            }
        }
        /*foreach($channelNames as $chn){
		if(empty($channel_string)|| !isset($channel_string)){
			$channel_string = ".'$chn'.";
		}else{
			$channel_string = $channel_string . ","."'$chn'";
		}
	    }	*/
        //$channelNames = "('" . implode("','",$channelNames) . "')";
        $query = "select distinct stc.tv_channel_id,stc.channel_name,sm.id as market_id,sm.sw_market_name as market_name
                        from sv_advertiser_campaign sac
                       inner join sv_tv_channel stc on sac.channel_id = stc.tv_channel_id
                       inner join sv_market_x_channel smc ON stc.tv_channel_id = smc.channel_fk_id
                       inner join sv_sw_market sm ON sm.id = smc.market_fk_id and sm.id = sac.market_id
                       where stc.channel_name in ($channel_string) and sac.internal_ro_number='$internal_ro' and "
            . " sac.campaign_status !='cancelled' order by sm.sw_market_name ";

        $result = $this->db->query($query);
        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_total_second_for_channel_ro($channel_id, $internal_ro)
    {
        $data = array('tv_channel_id' => $channel_id, 'internal_ro_number' => $internal_ro);
        $this->db->select('total_spot_ad_seconds,total_banner_ad_seconds');
        $result = $this->db->get_where('ro_approved_networks', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getMarketIdForMarketName($userData)
    {
        $this->db->select('id');
        $result = $this->db->get_where('sv_sw_market', $userData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
    }

    public function get_scheduled_impression_for_time_period_Market($internal_ro, $market_id, $start_date, $end_date, $spotFct, $bannerFct)
    {
        $campaignIds = $this->getScheduledCampaignIdForMarket($internal_ro, $market_id, null, null);
        $scheduledFct = $this->getScheduledFCTForGivenPeriod($campaignIds, null, null);
        log_message('info', 'in mg_model@get_scheduled_impression_for_time_period_Market | scheduled fct - ' . print_r($scheduledFct, true));
        $data = array();
        if (count($scheduledFct) > 0) {

            $nearestChannelFct = $this->getNearestChannelFct($scheduledFct, $spotFct, $bannerFct);
            $spotChannelIds = array_keys($nearestChannelFct['spotFct']);
            $bannerChannelIds = array_keys($nearestChannelFct['bannerFct']);

            if (count($spotChannelIds) > 0) {    //add brandid in all three
                $spotCampaignIdForChannel = $this->getScheduledCampaignIdForChannel($internal_ro, null, null, $spotChannelIds[0]);
                $scheduledSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, null, null, $start_date, $end_date);
                $totalSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, null, null);

                $data['spotFct'][$spotChannelIds[0]] = $scheduledSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                $data['totalSpotFct'][$spotChannelIds[0]] = $totalSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
            }

            if (count($bannerChannelIds) > 0) {
                $bannerCampaignIdForChannel = $this->getScheduledCampaignIdForChannel($internal_ro, null, null, $bannerChannelIds[0]);
                $scheduledBannerFct = $this->getScheduledFCTForGivenPeriod($bannerCampaignIdForChannel, null, null, $start_date, $end_date);
                $totalBannerFct = $this->getScheduledFCTForGivenPeriod($bannerCampaignIdForChannel, null, null);

                $data['bannerFct'][$bannerChannelIds[0]] = $scheduledBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                $data['totalBannerFct'][$bannerChannelIds[0]] = $totalBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
            }//echo print_r($data,true);exit;

        }
        log_message('info', 'in mg_model@get_scheduled_impression_for_time_period_Market | get_scheduled_impression_for_time_period_Market - ' . print_r($data, true));
        return $data;

    }

    public function getScheduledCampaignIdForMarket($internal_ro, $market_id, $brand_id, $content)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                        where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                        AND internal_ro_number = '$internal_ro'  AND is_make_good =0 and market_id='$market_id' and is_value_added = 0 ";
        if (isset($brand_id) && $brand_id != null) {
            $query .= " and brand_id = $brand_id ";
        }
        if (isset($content) && $content != null) {
            $query .= " and caption_name = '$content'";
        }

        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    //Used for cancel brand and cancel content

    public function getScheduledFCTForGivenPeriod($campaignIds, $brand_id, $content, $start_date = NULL, $end_date = NULL)
    {
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id,ac.channel_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaignIds) and acsd.screen_region_id in(1,3) and acsd.status ='scheduled' ";

        if (isset($brand_id) && $brand_id != null) {
            $query .= " and ac.brand_id = $brand_id ";
        }
        if (isset($content) && $content != null) {
            $query .= " and ac.caption_name = '$content'";
        }

        if (isset($start_date) && isset($end_date)) {
            $query .= " and (acsd.date >= '$start_date' and acsd.date < '$end_date' )";
        }
        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id,ac.channel_id";
        $results = $this->db->query($query);
        if ($results->num_rows() != 0) {
            $fcts = $results->result("array");
            $data = array();
            foreach ($fcts as $value) {
                $channelId = $value['channel_id'];
                if (!array_key_exists($channelId, $data)) {
                    $data[$channelId] = array();
                    $data[$channelId]['spot_ad_seconds'] = 0;
                    $data[$channelId]['banner_ad_seconds'] = 0;
                }
                if ($value['screen_region_id'] == 1) {
                    $data[$channelId]['spot_ad_seconds'] += $value['total_ad_seconds'];
                } else if ($value['screen_region_id'] == 3) {
                    $data[$channelId]['banner_ad_seconds'] += $value['total_ad_seconds'];
                }
            }
            return $data;
        }
        return array();
    }

    public function getNearestChannelFct($data, $spotFct, $bannerFct)
    {
        $lessOrEqualFct = $this->getLessOrEqualFct($data, $spotFct, $bannerFct);
        log_message('info', 'in mg_model@getNearestChannelFct | less or equal fct - ' . print_r($lessOrEqualFct, true));

        /**
         * For Logic optimization change the code to find nearest to FCT
         * Rather than Finding First nearest less or equal
         * If Zero Then Finding First Nearest greater
         */

        arsort($lessOrEqualFct['lessOrEqualSpotFct']);
        arsort($lessOrEqualFct['lessOrEqualBannerFct']);
        log_message('info', 'in mg_model@getNearestChannelFct | after sorting less or equal fct - ' . print_r($lessOrEqualFct, true));

        //$sortedLessOrEqualSpotFct = $this->getSortedLessOrEqualFct($lessOrEqualFct['lessOrEqualSpotFct']) ;
        //$sortedLessOrEqualBannerFct = $this->getSortedLessOrEqualFct($lessOrEqualFct['lessOrEqualBannerFct']) ;

        $sortedLessOrEqualSpotFctChannelKeys = array_keys($lessOrEqualFct['lessOrEqualSpotFct']);
        log_message('info', 'in mg_model@getNearestChannelFct | $sortedLessOrEqualSpotFctChannelKeys - ' . print_r($sortedLessOrEqualSpotFctChannelKeys, true));
        $sortedLessOrEqualBannerFctChannelKeys = array_keys($lessOrEqualFct['lessOrEqualBannerFct']);
        log_message('info', 'in mg_model@getNearestChannelFct | $sortedLessOrEqualBannerFctChannelKeys - ' . print_r($sortedLessOrEqualBannerFctChannelKeys, true));

        $spotFctchannelId = $sortedLessOrEqualSpotFctChannelKeys[0];
        log_message('info', 'in mg_model@getNearestChannelFct | $spotFctchannelId - ' . print_r($spotFctchannelId, true));
        $bannerFctchannelId = $sortedLessOrEqualBannerFctChannelKeys[0];
        log_message('info', 'in mg_model@getNearestChannelFct | $bannerFctchannelId - ' . print_r($bannerFctchannelId, true));

        $userData = array();
        $nearestSpotFct = array();
        $nearestBannerFct = array();

        //If selected spot Fct equal to zero
        if ($lessOrEqualFct['lessOrEqualSpotFct'][$spotFctchannelId] == 0) {
            $greaterOrEqualFct = $this->getGreaterOrEqualFct($data, $spotFct, 'spot');
            log_message('info', 'in mg_model@getNearestChannelFct | $greaterOrEqualFct - ' . print_r($greaterOrEqualFct, true));
            asort($greaterOrEqualFct['greaterOrEqualSpotFct']);
            log_message('info', 'in mg_model@getNearestChannelFct | $greaterOrEqualFct - ' . print_r($greaterOrEqualFct, true));

            $sortedGreaterOrEqualSpotFctChannelKeys = array_keys($greaterOrEqualFct['greaterOrEqualSpotFct']);
            log_message('info', 'in mg_model@getNearestChannelFct | $sortedGreaterOrEqualSpotFctChannelKeys - ' . print_r($sortedGreaterOrEqualSpotFctChannelKeys, true));
            $spotFctchannelId = $sortedGreaterOrEqualSpotFctChannelKeys[0];
            log_message('info', 'in mg_model@getNearestChannelFct | $spotFctchannelId - ' . print_r($spotFctchannelId, true));

            $nearestSpotFct[$spotFctchannelId] = $greaterOrEqualFct['greaterOrEqualSpotFct'][$spotFctchannelId];
        } else {
            $nearestSpotFct[$spotFctchannelId] = $lessOrEqualFct['lessOrEqualSpotFct'][$spotFctchannelId];
        }

        //If selected spot Fct equal to zero
        if ($lessOrEqualFct['lessOrEqualBannerFct'][$bannerFctchannelId] == 0) {
            $greaterOrEqualFct = $this->getGreaterOrEqualFct($data, $bannerFct, 'banner');
            asort($greaterOrEqualFct['greaterOrEqualSpotFct']);

            $sortedGreaterOrEqualBannerFctChannelKeys = array_keys($greaterOrEqualFct['greaterOrEqualSpotFct']);
            $bannerFctchannelId = $sortedGreaterOrEqualBannerFctChannelKeys[0];

            $nearestSpotFct[$bannerFctchannelId] = $greaterOrEqualFct['greaterOrEqualSpotFct'][$bannerFctchannelId];
        } else {
            $nearestBannerFct[$bannerFctchannelId] = $lessOrEqualFct['lessOrEqualBannerFct'][$bannerFctchannelId];
        }

        $userData['spotFct'] = $nearestSpotFct;
        $userData['bannerFct'] = $nearestBannerFct;
        log_message('info', 'in mg_model@getNearestChannelFct | $userData - ' . print_r($userData, true));
        return $userData;
    }

    public function getLessOrEqualFct($data, $spotFct, $bannerFct)
    {
        $userData = array();
        $lessOrEqualSpotFct = array();
        $lessOrEqualBannerFct = array();

        foreach ($data as $channel_id => $value) {
            if ($value['spot_ad_seconds'] <= $spotFct) {
                $lessOrEqualSpotFct[$channel_id] = $value['spot_ad_seconds'];
            }
            if ($value['banner_ad_seconds'] <= $bannerFct) {
                $lessOrEqualBannerFct[$channel_id] = $value['banner_ad_seconds'];
            }

        }
        $userData['lessOrEqualSpotFct'] = $lessOrEqualSpotFct;
        $userData['lessOrEqualBannerFct'] = $lessOrEqualBannerFct;
        return $userData;
    }

    public function getGreaterOrEqualFct($data, $fct, $region)
    {
        $userData = array();
        if ($region == 'spot') {
            $greaterOrEqualSpotFct = array();
        }
        if ($region == 'banner') {
            $greaterOrEqualBannerFct = array();
        }

        foreach ($data as $channel_id => $value) {
            if (($value['spot_ad_seconds'] >= $fct) && ($region == 'spot')) {
                $greaterOrEqualSpotFct[$channel_id] = $value['spot_ad_seconds'];
            }
            if (($value['banner_ad_seconds'] >= $fct) && ($region == 'banner')) {
                $greaterOrEqualBannerFct[$channel_id] = $value['banner_ad_seconds'];
            }

        }
        if ($region == 'spot') {
            $userData['greaterOrEqualSpotFct'] = $greaterOrEqualSpotFct;
        }
        if ($region == 'banner') {
            $userData['greaterOrEqualSpotFct'] = $greaterOrEqualBannerFct;
        }
        return $userData;
    }

    // summary for current financial year for each month

    public function getScheduledCampaignIdForChannel($internal_ro, $brand_id, $content, $channelId)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                        where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                        AND internal_ro_number = '$internal_ro'  AND is_make_good =0 and channel_id='$channelId' and is_value_added = 0 ";

        if (isset($brand_id) && $brand_id != null) {
            $query .= " and brand_id = $brand_id ";
        }
        if (isset($content) && $content != null) {
            $query .= " and caption_name = '$content'";
        }

        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function getSortedLessOrEqualFct($data)
    {
        $userdata = arsort($data);
        return $userdata;
    }

    public function get_scheduled_impression_for_ro_by_brand_and_content($internalRo, $marketId, $brand_id, $content, $start_date, $end_date, $spotFct, $bannerFct)
    {

        if (isset($brand_id) && $brand_id != null) {
            //For Brand wise market cancellation

            $campaignIds = $this->getScheduledCampaignIdForMarket($internalRo, $marketId, $brand_id, null);

            //Get Campaigns For that market of different region
            $regionofThatBrand = $this->getRegionIdOfBrandContent($internalRo, $marketId, $brand_id, null);
            if ($regionofThatBrand == 1) {
                $region_id = 3;
            } else {
                $region_id = 1;
            }
            $campaignIdsOfOtherRegion = $this->getScheduledCampaignIdForMarketRegion($internalRo, $marketId, $region_id);
            if ($campaignIdsOfOtherRegion != 0) {
                $campaignIds = $campaignIds . "," . $campaignIdsOfOtherRegion;
            }
            $scheduledFct = $this->getScheduledFCTForGivenPeriod($campaignIds, null, null);

            if (count($scheduledFct) > 0) {
                $data = array();
                $nearestChannelFct = $this->getNearestChannelFct($scheduledFct, $spotFct, $bannerFct);
                $spotChannelIds = array_keys($nearestChannelFct['spotFct']);
                $bannerChannelIds = array_keys($nearestChannelFct['bannerFct']);

                if (count($spotChannelIds) > 0) {    //add brandid in all three
                    $spotCampaignIdForChannel = $this->getScheduledCampaignIdForChannel($internalRo, $brand_id, null, $spotChannelIds[0]);
                    $scheduledSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, $brand_id, null, $start_date, $end_date);
                    $scheduledTotalSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, $brand_id, null);
                    $totalSpotFct = $this->getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $spotChannelIds[0], 'scheduled');

                    $data['spotFct'][$spotChannelIds[0]] = $scheduledTotalSpotFct[$spotChannelIds[0]]['spot_ad_seconds'] - $scheduledSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                    $data['totalSpotFct'][$spotChannelIds[0]] = $totalSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                }

                if (count($bannerChannelIds) > 0) {
                    $bannerCampaignIdForChannel = $this->getScheduledCampaignIdForChannel($internalRo, $brand_id, null, $bannerChannelIds[0]);
                    $scheduledBannerFct = $this->getScheduledFCTForGivenPeriod($bannerCampaignIdForChannel, $brand_id, null, $start_date, $end_date);
                    $scheduledTotalSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, $brand_id, null);
                    $totalBannerFct = $this->getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $bannerChannelIds[0], 'scheduled');

                    $data['bannerFct'][$bannerChannelIds[0]] = $scheduledTotalSpotFct[$bannerChannelIds[0]]['banner_ad_seconds'] - $scheduledBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                    $data['totalBannerFct'][$bannerChannelIds[0]] = $totalBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                }//echo print_r($data,true);exit;
                return $data;
            } else {
                $campaignIds = $this->getPendingCampaignIdForMarket($internalRo, $marketId, $brand_id, null);
                $scheduledFct = $this->getScheduledFCTForGivenPeriod($campaignIds, null, null);

                $data = array();
                $nearestChannelFct = $this->getNearestChannelFct($scheduledFct, $spotFct, $bannerFct);
                $spotChannelIds = array_keys($nearestChannelFct['spotFct']);
                $bannerChannelIds = array_keys($nearestChannelFct['bannerFct']);

                if (count($spotChannelIds) > 0) {
                    $spotCampaignIdForChannel = $this->getPendingCampaignIdForChannel($internalRo, $brand_id, null, $spotChannelIds[0]);
                    $scheduledSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, $brand_id, null);
                    $totalSpotFct = $this->getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $spotChannelIds[0], 'pending');

                    $data['spotFct'][$spotChannelIds[0]] = $scheduledSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                    $data['totalSpotFct'][$spotChannelIds[0]] = $totalSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                }

                if (count($bannerChannelIds) > 0) {
                    $bannerCampaignIdForChannel = $this->getPendingCampaignIdForChannel($internalRo, $brand_id, null, $bannerChannelIds[0]);
                    $scheduledBannerFct = $this->getScheduledFCTForGivenPeriod($bannerCampaignIdForChannel, $brand_id, null);
                    $totalBannerFct = $this->getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $bannerChannelIds[0], 'pending');

                    $data['bannerFct'][$bannerChannelIds[0]] = $scheduledBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                    $data['totalBannerFct'][$bannerChannelIds[0]] = $totalBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                }
                return $data;
            }
        }

        if (isset($content) && $content != null) {
            //For Content wise market cancellation

            $campaignIds = $this->getScheduledCampaignIdForMarket($internalRo, $marketId, null, $content);

            //Get Campaigns For that market of different region
            $regionofThatBrand = $this->getRegionIdOfBrandContent($internalRo, $marketId, null, $content);
            if ($regionofThatBrand == 1) {
                $region_id = 3;
            } else {
                $region_id = 1;
            }
            $campaignIdsOfOtherRegion = $this->getScheduledCampaignIdForMarketRegion($internalRo, $marketId, $region_id);
            if ($campaignIdsOfOtherRegion != 0) {
                $campaignIds = $campaignIds . "," . $campaignIdsOfOtherRegion;
            }
            $scheduledFct = $this->getScheduledFCTForGivenPeriod($campaignIds, null, null);

            if (count($scheduledFct) > 0) {
                $data = array();
                $nearestChannelFct = $this->getNearestChannelFct($scheduledFct, $spotFct, $bannerFct);
                $spotChannelIds = array_keys($nearestChannelFct['spotFct']);
                $bannerChannelIds = array_keys($nearestChannelFct['bannerFct']);

                if (count($spotChannelIds) > 0) {
                    $spotCampaignIdForChannel = $this->getScheduledCampaignIdForChannel($internalRo, null, $content, $spotChannelIds[0]);
                    $scheduledSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, null, $content, $start_date, $end_date);
                    $scheduledTotalSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, null, $content);
                    $totalSpotFct = $this->getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $spotChannelIds[0], 'scheduled');

                    $data['spotFct'][$spotChannelIds[0]] = $scheduledTotalSpotFct[$spotChannelIds[0]]['spot_ad_seconds'] - $scheduledSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                    $data['totalSpotFct'][$spotChannelIds[0]] = $totalSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                }

                if (count($bannerChannelIds) > 0) {
                    $bannerCampaignIdForChannel = $this->getScheduledCampaignIdForChannel($internalRo, null, $content, $bannerChannelIds[0]);
                    $scheduledBannerFct = $this->getScheduledFCTForGivenPeriod($bannerCampaignIdForChannel, null, $content, $start_date, $end_date);
                    $scheduledTotalSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, null, $content);
                    $totalBannerFct = $this->getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $bannerChannelIds[0], 'scheduled');

                    $data['bannerFct'][$bannerChannelIds[0]] = $scheduledTotalSpotFct[$bannerChannelIds[0]]['banner_ad_seconds'] - $scheduledBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                    $data['totalBannerFct'][$bannerChannelIds[0]] = $totalBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                }//echo print_r($data,true);exit;
                return $data;
            } else {
                $campaignIds = $this->getPendingCampaignIdForMarket($internalRo, $marketId, null, $content);
                $scheduledFct = $this->getScheduledFCTForGivenPeriod($campaignIds, null, null);

                $data = array();
                $nearestChannelFct = $this->getNearestChannelFct($scheduledFct, $spotFct, $bannerFct);
                $spotChannelIds = array_keys($nearestChannelFct['spotFct']);
                $bannerChannelIds = array_keys($nearestChannelFct['bannerFct']);

                if (count($spotChannelIds) > 0) {
                    $spotCampaignIdForChannel = $this->getPendingCampaignIdForChannel($internalRo, null, $content, $spotChannelIds[0]);
                    $scheduledSpotFct = $this->getScheduledFCTForGivenPeriod($spotCampaignIdForChannel, null, $content);
                    $totalSpotFct = $this->getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $spotChannelIds[0], 'pending');

                    $data['spotFct'][$spotChannelIds[0]] = $scheduledSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                    $data['totalSpotFct'][$spotChannelIds[0]] = $totalSpotFct[$spotChannelIds[0]]['spot_ad_seconds'];
                }

                if (count($bannerChannelIds) > 0) {
                    $bannerCampaignIdForChannel = $this->getPendingCampaignIdForChannel($internalRo, null, $content, $bannerChannelIds[0]);
                    $scheduledBannerFct = $this->getScheduledFCTForGivenPeriod($bannerCampaignIdForChannel, null, $content);
                    $totalBannerFct = $this->getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $bannerChannelIds[0], 'pending');

                    $data['bannerFct'][$bannerChannelIds[0]] = $scheduledBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                    $data['totalBannerFct'][$bannerChannelIds[0]] = $totalBannerFct[$bannerChannelIds[0]]['banner_ad_seconds'];
                }
                return $data;
            }
        }


    }

    public function getRegionIdOfBrandContent($internal_ro, $market_id, $brand_id, $content)
    {
        $query = "select selected_region_id from sv_advertiser_campaign
                        where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                        AND internal_ro_number = '$internal_ro'  AND is_make_good =0 and market_id='$market_id' and is_value_added = 0 ";
        if (isset($brand_id) && $brand_id != null) {
            $query .= " and brand_id = $brand_id ";
        }
        if (isset($content) && $content != null) {
            $query .= " and caption_name = '$content'";
        }
        $query .= " limit 1 ";
        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $regionId = $results->result("array");
            if ($regionId[0]['selected_region_id'] != NULL) {
                return $regionId[0]['selected_region_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function getScheduledCampaignIdForMarketRegion($internal_ro, $market_id, $region_id)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                        where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                        AND internal_ro_number = '$internal_ro'  AND is_make_good =0 and market_id='$market_id' ";

        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function getTotalScheduledFCTForGivenPeriod($internalRo, $marketId, $channelId, $campaign_status)
    {
        $campaignIds = $this->getScheduledPendingCampaignIds($internalRo, $channelId, $marketId, $campaign_status);
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id,ac.channel_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaignIds) and acsd.screen_region_id in(1,3) and acsd.status ='scheduled' ";
        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id,ac.channel_id";
        $results = $this->db->query($query);
        if ($results->num_rows() != 0) {
            $fcts = $results->result("array");
            $data = array();
            foreach ($fcts as $value) {
                $channelId = $value['channel_id'];
                if (!array_key_exists($channelId, $data)) {
                    $data[$channelId] = array();
                    $data[$channelId]['spot_ad_seconds'] = 0;
                    $data[$channelId]['banner_ad_seconds'] = 0;
                }
                if ($value['screen_region_id'] == 1) {
                    $data[$channelId]['spot_ad_seconds'] += $value['total_ad_seconds'];
                } else if ($value['screen_region_id'] == 3) {
                    $data[$channelId]['banner_ad_seconds'] += $value['total_ad_seconds'];
                }
            }
            return $data;
        }
        return array();
    }

    public function getScheduledPendingCampaignIds($internal_ro, $channel_id, $market_id, $campaign_status)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                        where internal_ro_number = '$internal_ro'  AND is_make_good =0 and channel_id = '$channel_id' and is_value_added= 0";
        if (isset($market_id) && !empty($market_id)) {
            $query .= " and market_id='$market_id' ";
        }
        if ($campaign_status == 'pending') {
            $query .= " and campaign_status IN ( 'pending_approval')";
        } else {
            $query .= " and campaign_status Not IN ( 'pending_approval','cancelled')";
        }
        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function getPendingCampaignIdForMarket($internal_ro, $market_id, $brand_id, $content)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                        where campaign_status IN ( 'pending_approval')
                        AND internal_ro_number = '$internal_ro'  AND is_make_good =0 and market_id='$market_id' and is_value_added = 0 ";
        if (isset($brand_id) && $brand_id != null) {
            $query .= " and brand_id = $brand_id ";
        }
        if (isset($content) && $content != null) {
            $query .= " and caption_name = '$content'";
        }

        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function getPendingCampaignIdForChannel($internal_ro, $brand_id, $content, $channelId)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                        where campaign_status IN ( 'pending_approval')
                        AND internal_ro_number = '$internal_ro'  AND is_make_good =0 and channel_id='$channelId' and is_value_added = 0 ";

        if (isset($brand_id) && $brand_id != null) {
            $query .= " and brand_id = $brand_id ";
        }
        if (isset($content) && $content != null) {
            $query .= " and caption_name = '$content'";
        }

        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function get_total_scheduled_seconds_For_Market($internal_ro, $market_id)
    {
        $campaignIds = $this->getScheduledCampaignIdForMarket($internal_ro, $market_id, null, null);

        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaignIds) and acsd.status ='scheduled' and acsd.screen_region_id in(1,3)  ";

        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id";
        $results = $this->db->query($query);
        $fcts = $results->result("array");

        $data = array();
        $data['spot_ad_seconds'] = 0;
        $data['banner_ad_seconds'] = 0;

        foreach ($fcts as $value) {
            if ($value['screen_region_id'] == 1) {
                $data['spot_ad_seconds'] += $value['total_ad_seconds'];
            } else if ($value['screen_region_id'] == 3) {
                $data['banner_ad_seconds'] += $value['total_ad_seconds'];
            }

        }
        return $data;
    }

    public function monthwise_email_report_details()
    {
        /*$year = date("Y");
		if($month <= 3){
			$to_year = $year;
			$from_year = $year - 1;
		}
		else{
			$to_year = $year + 1;
			$from_year = $year;
		}

		$data_month_wise = '';
		for($i=4;$i<16;$i++){
			if($i>12){
				$m = $i - 12;
				$m = "0".$m;
			}
			else{
				if($m == 10 || $m == 11 || $m == 12){
					$m = $i;
				}
				else{
					$m = "0".$i;
				}
			}

			$am_query_month_wise = "select * from ro_am_external_ro where month(camp_start_date) = '$m' or month(camp_end_date) = '$m'";
			$am_query_month_wise_res = $this->db->query($am_query_month_wise);

			//$data_arr_month_wise = array();
			if($am_query_month_wise_res->num_rows()!=0){

				$am_month_wise_res = $am_query_month_wise_res->result("array");
				//$count = 0;
				foreach($am_month_wise_res as $am_month_wise_data){
					//$client_name = $am_data['client'];
					$ext_ro = $am_month_wise_data['cust_ro'];
					$order_id = $am_month_wise_data['internal_ro'];
					// get ro data for every ext_ro
					$get_ro_qy = "select * from ro_amount where customer_ro_number='$ext_ro'";
					$get_ro_qy_res = $this->db->query($get_ro_qy);
					$ro_data = $get_ro_qy_res->result("array");
					$revenue = ($ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount']) * SERVICE_TAX;

					$total_network_payout_ary = $this->get_total_network_payout($order_id);
					$other_expenses_ary = $this->get_external_ro_report_details($order_id);

					$total_network_payout = $total_network_payout_ary[0]['network_payout'];

					$actual_net_amount = $ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount'] - $ro_data[0]['agency_rebate'] - $other_expenses_ary[0]['other_expenses'];

					$total_sw_net_contribution = $actual_net_amount - $total_network_payout;
					// end
					//if(!isset($data_arr_month_wise[$m]))
					/*$data_arr_month_wise[$m][$count] = array();
					array_push($data_arr_month_wise[$m][$count],$ext_ro);
					array_push($data_arr_month_wise[$m][$count],$revenue);
					array_push($data_arr_month_wise[$m][$count],$total_network_payout);
					array_push($data_arr_month_wise[$m][$count],$total_sw_net_contribution);
					$count++;*/
        //echo '<pre>';print_r($data_arr_month_wise);
        /*$data_month_wise .= "<br>"."Month Name : ".date("F", mktime(null, null, null, $m))."<br>";
					$data_month_wise .=  "External RO : ".$ext_ro."<br>";
					$data_month_wise .=  "Revenue : ".$revenue."<br>";
					$data_month_wise .=  "Total Network Payout : ".$total_network_payout."<br>";
					$data_month_wise .=  "Surewaves Net Contribution : ".$total_sw_net_contribution."<br>";

					$invoice_qry = "select sum(amnt_collected) as total_amnt_collected from ro_am_invoice_collection where month(collection_date) = '$m' and `ext_ro`='$ext_ro'";
					$invoice_qry_res = $this->db->query($invoice_qry);
					$invoice_rs = $invoice_qry_res->result("array");
					$total_amnt_collected = $invoice_rs[0]['total_amnt_collected'];
					$data_month_wise .=  "Amount Collected : ".$total_amnt_collected."<br>";
				}
			}
		}//echo '<pre>';print_r($data_arr_month_wise);exit;
		// end
		//echo $data.$data_month_wise;exit;
		return $data.$data_month_wise; */
        //how to calculate year
        $current_month_start_date = date("Y-04-01");
        $current_month_end_date = date("Y-m-t");

        //here iterate date
        $am_query = "select * from ro_am_external_ro where ";
        $am_query = $am_query . " ((camp_start_date <='$current_month_start_date' and camp_end_date >='$current_month_start_date') or (camp_start_date >='$current_month_start_date') and ((camp_end_date <='$current_month_end_date') or (camp_start_date <='$current_month_end_date' and camp_end_date >='$current_month_end_date')))";
        $am_query = $am_query . " order by client";

        $am_query_res = $this->db->query($am_query);
        $data = array();

        if ($am_query_res->num_rows() != 0) {
            $structure = "";
            $am_res = $am_query_res->result("array");
            foreach ($am_res as $am_data) {
                $client_name = $am_data['client'];
                $ext_ro = $am_data['cust_ro'];
                $order_id = $am_data['internal_ro'];

                // get ro data for every ext_ro
                $get_ro_qy = "select * from ro_amount where customer_ro_number='$ext_ro'";
                $get_ro_qy_res = $this->db->query($get_ro_qy);
                $ro_data = $get_ro_qy_res->result("array");
                $revenue = ($ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount']) * SERVICE_TAX;

                $total_network_payout_ary = $this->get_total_network_payout($order_id);
                $other_expenses_ary = $this->get_external_ro_report_details($order_id);

                $total_network_payout = $total_network_payout_ary[0]['network_payout'];

                $actual_net_amount = $ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount'] - $ro_data[0]['agency_rebate'] - $other_expenses_ary[0]['other_expenses'];

                $total_sw_net_contribution = $actual_net_amount - $total_network_payout;
                // end


                $tmp = array();
                $tmp['client'] = $client_name;
                $tmp['internal_ro'] = $order_id;
                $tmp['revenue'] = $revenue;
                $tmp['network_payout'] = $total_network_payout;

                array_push($data, $tmp);

                /*$data .=  "<br>"."Client Name : ".$client_name."<br>";
				$data .=  "External RO : ".$ext_ro."<br>";
				$data .=  "Revenue : ".$revenue."<br>";
				$data .=  "Total Network Payout : ".$total_network_payout."<br>";
				$data .=  "Surewaves Net Contribution : ".$total_sw_net_contribution."<br>"; */
            }//echo $data;
            $data = $this->convert_data_into_structure($data);

            $structure = "<table><tr>"
                . "<td>Client Name<td>"
                . "<td>No of Ro's<td>"
                . "<td>Total Revenue<td>"
                . "<td>Total Network Payout<td>"
                . "</tr>";
            foreach ($data as $val) {
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $val['client'] . "</td>";
                $structure = $structure . "<td>" . $val['total_internal_ro'] . "</td>";
                $structure = $structure . "<td>" . $val['revenue'] . "</td>";
                $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
                $structure = $structure . "</tr>";
            }
            $structure = $structure . "</table>";
            return $structure;
        }

    }

    public function get_total_network_payout($internal_ro)
    {
        $query = "SELECT SUM(`channel_spot_amount`*(`customer_share` /100 )  +  `channel_banner_amount`*(`customer_share` /100 ) ) AS network_payout FROM  `ro_approved_networks` WHERE  `internal_ro_number` = '$internal_ro'";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function current_financial_email_report_details()
    {
        $month = date("m");
        $year = date("Y");
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("Y-m-d");

        $structure = "<table><tr>"
            . "<td><b>Month Name</b></td>"
            . "<td><b>No of Ro's</b></td>"
            . "<td><b>Total Revenue</b></td>"
            . "<td><b>Total Network Payout</b></td>"
            . "<td><b>Total SureWaves Net Contribution</b></td>";

        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {
            $start_date = date("Y-m-d", $i);
            if (date("m", $i) == date("m")) {
                $end_date = date("Y-m-d");
            } else {
                $end_date = date("Y-m-d", strtotime("+1 month -1 days", $i));
            }
            // echo $start_date."---".$end_date."<br/>";
            $am_query = "select am.internal_ro,am.cust_ro from ro_am_external_ro as am, ro_external_ro_report_details as nr where am.internal_ro=nr.internal_ro_number ";
            $am_query = $am_query . " and (ro_date >='$start_date' and ro_date <= '$end_date')";

            $am_query_res = $this->db->query($am_query);

            if ($am_query_res->num_rows() != 0) {
                $no_of_ro = $am_query_res->num_rows();

                $total_revenue = 0;
                $total_network_payout_val = 0;
                $total_sw_net_contribution = 0;
                //$net_contribution = 0 ;
                $am_res = $am_query_res->result("array");
                foreach ($am_res as $am_data) {
                    $ext_ro = $am_data['cust_ro'];
                    $order_id = $am_data['internal_ro'];

                    // get ro data for every ext_ro
                    /*$get_ro_qy = "select * from ro_amount where customer_ro_number='$ext_ro'";
						$get_ro_qy_res = $this->db->query($get_ro_qy);
						$ro_data = $get_ro_qy_res->result("array");

						$revenue = ($ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount']) * SERVICE_TAX;
						$total_revenue = $total_revenue + $revenue ;

						$total_network_payout_ary = $this->get_total_network_payout($order_id);
						$other_expenses_ary = $this->get_external_ro_report_details($order_id);

						$total_network_payout = $total_network_payout_ary[0]['network_payout'];
						$total_network_payout_val = $total_network_payout_val + $total_network_payout ;

                                                 $agency_rebate = 0 ;
                                                if($ro_data[0]['agency_rebate_on'] == "ro_amount"){
                                                        $agency_rebate = $ro_data[0]['ro_amount'] * ($ro_data[0]['agency_rebate']/100);
                                                }
                                                else{
                                                        $agency_rebate = ($ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount']) * ($ro_data[0]['agency_rebate']/100);
                                                }

						$actual_net_amount = $ro_data[0]['ro_amount'] - $ro_data[0]['agency_commission_amount'] - $agency_rebate - $other_expenses_ary[0]['other_expenses'];
						$net_contribution = $actual_net_amount - $total_network_payout ;
						$total_sw_net_contribution = $total_sw_net_contribution + $net_contribution ; */

                    $get_ro_qy = "select * from ro_external_ro_report_details where customer_ro_number='$ext_ro'";
                    $get_ro_qy_res = $this->db->query($get_ro_qy);
                    $ro_data = $get_ro_qy_res->result("array");

                    $total_revenue = $total_revenue + $ro_data[0]['net_revenue'];
                    $total_network_payout_val = $total_network_payout_val + $ro_data[0]['total_network_payout'];
                    $total_sw_net_contribution = $total_sw_net_contribution + $ro_data[0]['net_contribution_amount'];
                }
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . date("M-Y", $i) . "</td>";
                $structure = $structure . "<td>" . $no_of_ro . "</td>";
                $structure = $structure . "<td>" . $total_revenue . "</td>";
                $structure = $structure . "<td>" . $total_network_payout_val . "</td>";
                $structure = $structure . "<td>" . $total_sw_net_contribution . "</td>";
                $structure = $structure . "</tr>";
            }
        }
        $structure = $structure . "</table>";
        return $structure;

    }

    public function current_financial_email_report_details_v1($givenDate = Null, $userType)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("$to_year-03-31");

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Month Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Network Payout(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th><tr>";

        $total_yr_ro = 0;
        $total_yr_revenue = 0;
        $total_yr_network_payout_val = 0;
        $total_yr_sw_net_contribution = 0;
        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {
            $start_date = date("Y-m-d", $i);
            if (date("m", $i) == date("m")) {
                $end_date = date("Y-m-t");
            } else {
                $end_date = date("Y-m-t", strtotime("+1 month -1 days", $i));
            }
            // echo $start_date."---".$end_date."<br/>";
            $am_query = "select am.internal_ro,am.cust_ro,am.id from ro_am_external_ro as am, ro_external_ro_report_details as nr where am.internal_ro=nr.internal_ro_number and test_user_creation='$userType'";
            $am_query = $am_query . " and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
                                                or
                ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' ))";

            $am_query_res = $this->db->query($am_query);

            if ($am_query_res->num_rows() != 0) {
                $no_of_ro = $am_query_res->num_rows();

                $total_revenue = 0;
                $total_network_payout_val = 0;
                $total_sw_net_contribution = 0;
                //$net_contribution = 0 ;
                $am_res = $am_query_res->result("array");
                foreach ($am_res as $am_data) {
                    $am_ext_id = $am_data['id'];
                    //$ext_ro = $am_data['cust_ro'];
                    $internal_ro = $am_data['internal_ro'];

                    $proportional_calculation = $this->time_period_proportional_calculation($am_ext_id, $internal_ro, $start_date, $end_date);

                    $total_revenue = $total_revenue + $proportional_calculation['revenue'];
                    $total_network_payout_val = $total_network_payout_val + $proportional_calculation['total_nw_payout'];
                    $total_sw_net_contribution = $total_sw_net_contribution + $proportional_calculation['total_sw_net_contribution'];


                }
                $net_contribuition_percent = round(($total_sw_net_contribution / $total_revenue) * 100);
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . date("M-Y", $i) . "</td>";
                $structure = $structure . "<td>" . $no_of_ro . "</td>";
                $structure = $structure . "<td>" . floor($total_revenue) . "</td>";
                $structure = $structure . "<td>" . floor($total_network_payout_val) . "</td>";
                $structure = $structure . "<td>" . floor($total_sw_net_contribution) . "</td>";
                $structure = $structure . "<td>" . $net_contribuition_percent . " %</td>";
                $structure = $structure . "</tr>";

                $userData = array(
                    'financial_year' => $from_year,
                    'month_name' => date("F", $i),
                    'no_of_ro' => $no_of_ro,
                    'revenue' => floor($total_revenue),
                    'network_payout' => floor($total_network_payout_val),
                    'net_contribuition' => floor($total_sw_net_contribution),
                    'is_fct' => 1
                );
                $whereData = array(
                    'financial_year' => $from_year,
                    'month_name' => date("F", $i),
                    'is_fct' => 1
                );

                $this->insertIntoMonthWiseRoReport($userData, $whereData);

                $total_yr_ro = $total_yr_ro + $no_of_ro;
                $total_yr_revenue = $total_yr_revenue + floor($total_revenue);
                $total_yr_network_payout_val = $total_yr_network_payout_val + floor($total_network_payout_val);
                $total_yr_sw_net_contribution = $total_yr_sw_net_contribution + floor($total_sw_net_contribution);
            }
        }
        $net_yr_contribuition_percent = round(($total_yr_sw_net_contribution / $total_yr_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_yr_ro . "</td>";
        $structure = $structure . "<td>" . $total_yr_revenue . "</td>";
        $structure = $structure . "<td>" . $total_yr_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_yr_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_yr_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";


        $structure = $structure . "</table>";
        return $structure;

    }

    public function insertIntoMonthWiseRoReport($userData, $whereData)
    {
        //Get:Whether existing
        $monthWiseRoData = $this->getMonthWiseRoReport($whereData);
        if (count($monthWiseRoData) > 0) {
            $updateData = array(
                'no_of_ro' => $userData['no_of_ro'],
                'target' => $userData['target'],
                'revenue' => $userData['revenue'],
                'target_achieved' => $userData['target_achieved'],
                'network_payout' => $userData['network_payout'],
                'net_contribuition' => $userData['net_contribuition']
            );
            $this->updateMonthWiseRoReport($updateData, $whereData);

        } else {
            $this->db->insert('ro_monthwise_mis_report', $userData);
        }

    }

    public function getMonthWiseRoReport($whereData)
    {
        $result = $this->db->get_where('ro_monthwise_mis_report', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateMonthWiseRoReport($userData, $whereData)
    {
        $this->db->update('ro_monthwise_mis_report', $userData, $whereData);
    }

    public function current_financial_email_report_details_test()
    {
        $month = date("m");
        $year = date("Y");
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("$to_year-03-31");

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Month Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Network Payout(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th><tr>";

        $total_yr_ro = 0;
        $total_yr_revenue = 0;
        $total_yr_network_payout_val = 0;
        $total_yr_sw_net_contribution = 0;
        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {
            $start_date = date("Y-m-d", $i);
            if (date("m", $i) == date("m")) {
                $end_date = date("Y-m-t");
            } else {
                $end_date = date("Y-m-t", strtotime("+1 month -1 days", $i));
            }
            // echo $start_date."---".$end_date."<br/>";
            $am_query = "select am.internal_ro,am.cust_ro,am.id from ro_am_external_ro as am, ro_external_ro_report_details as nr where am.internal_ro=nr.internal_ro_number and test_user_creation=1";
            $am_query = $am_query . " and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
                                                or
                ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' ))";

            $am_query_res = $this->db->query($am_query);

            if ($am_query_res->num_rows() != 0) {
                $no_of_ro = $am_query_res->num_rows();

                $total_revenue = 0;
                $total_network_payout_val = 0;
                $total_sw_net_contribution = 0;
                //$net_contribution = 0 ;
                $am_res = $am_query_res->result("array");
                foreach ($am_res as $am_data) {
                    $am_ext_id = $am_data['id'];
                    //$ext_ro = $am_data['cust_ro'];
                    $order_id = $am_data['internal_ro'];

                    $proportional_calculation = $this->time_period_proportional_calculation($am_ext_id, $order_id, $start_date, $end_date);

                    $total_revenue = $total_revenue + $proportional_calculation['revenue'];
                    $total_network_payout_val = $total_network_payout_val + $proportional_calculation['total_nw_payout'];
                    $total_sw_net_contribution = $total_sw_net_contribution + $proportional_calculation['total_sw_net_contribution'];
                }
                $net_contribuition_percent = round(($total_sw_net_contribution / $total_revenue) * 100);
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . date("M-Y", $i) . "</td>";
                $structure = $structure . "<td>" . $no_of_ro . "</td>";
                $structure = $structure . "<td>" . floor($total_revenue) . "</td>";
                $structure = $structure . "<td>" . floor($total_network_payout_val) . "</td>";
                $structure = $structure . "<td>" . floor($total_sw_net_contribution) . "</td>";
                $structure = $structure . "<td>" . $net_contribuition_percent . " %</td>";
                $structure = $structure . "</tr>";

                $total_yr_ro = $total_yr_ro + $no_of_ro;
                $total_yr_revenue = $total_yr_revenue + floor($total_revenue);
                $total_yr_network_payout_val = $total_yr_network_payout_val + floor($total_network_payout_val);
                $total_yr_sw_net_contribution = $total_yr_sw_net_contribution + floor($total_sw_net_contribution);
            }
        }
        $net_yr_contribuition_percent = round(($total_yr_sw_net_contribution / $total_yr_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_yr_ro . "</td>";
        $structure = $structure . "<td>" . $total_yr_revenue . "</td>";
        $structure = $structure . "<td>" . $total_yr_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_yr_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_yr_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";


        $structure = $structure . "</table>";
        return $structure;

    }

    public function get_ro_date_from_am_ro($customer_ro)
    {
        $query = "select * from ro_am_external_ro where cust_ro='$customer_ro'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_all_channel_info($internal_ro_number, $customer_id)
    {
        $query = "select * from ro_approved_networks where internal_ro_number='$internal_ro_number' and customer_id='$customer_id'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_cancel_channel_for_internal_ro($internal_ro)
    {
        $data = array('internal_ro_number' => $internal_ro);
        $result = $this->db->get_where('ro_cancel_channel', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
    }

    public function get_channels_schedule_for_internal_ro_customer($internal_ro, $cid)
    {
        $query = "select distinct(ch.tv_channel_id) as channel_id,ch.channel_name from sv_tv_channel as ch,sv_screen as s,sv_advertiser_campaign as ac,sv_advertiser_campaign_screens_dates as acsd where ch.tv_channel_id=s.channel_id and s.screen_id=acsd.screen_id and ac.internal_ro_number='$internal_ro' and ac.campaign_id=acsd.campaign_id and acsd.enterprise_id=$cid and acsd.status='scheduled' order by ch.channel_name";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function insert_into_cancel_channel($data)
    {
        $this->db->insert('ro_cancel_channel', $data);
    }

    public function update_approved_data_where_ro_customer($data, $where)
    {
        $this->db->update('ro_approved_networks', $data, $where);
    }

    public function update_approved_data_for_cancel_channel($data, $where)
    {
        $this->db->update('ro_approved_networks', $data, $where);
        /*
            $revision_no = $data['revision_no'] ;
            $pdf_geneartion_status = $data['pdf_generation_status'] ;

            $internal_ro_number = $where['internal_ro_number'] ;
            $customer_id = $where['customer_id'] ;

            $query = "update ro_approved_networks set pdf_generation_status='$pdf_geneartion_status',revision_no='$revision_no' where internal_ro_number='$internal_ro_number' and customer_id='$customer_id' " ;
            $this->db->query($query) ; */

    }

    public function delete_approved_data_for_customer_channel_ro($data)
    {
        $this->db->delete('ro_approved_networks', $data);
    }

    public function maintain_historical_data($data)
    {
        $this->db->insert('ro_history', $data);
    }

    public function filter_schedule_channel($schedule_channel, $cancel_channel)
    {
        $data = array();
        foreach ($schedule_channel as $sc) {
            $schedule_channel_id = $sc['channel_id'];
            $found = false;
            foreach ($cancel_channel as $cc) {
                $cancel_channel_id = $cc['channel_id'];
                if ($schedule_channel_id == $cancel_channel_id) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_push($data, $sc);
            }
        }
        return $data;
    }

    public function filter_schedule_channel_v1($schedule_channel, $cancel_channel)
    {
        $data = array();
        foreach ($schedule_channel as $sc) {
            $schedule_channel_id = $sc['tv_channel_id'];
            $found = false;
            foreach ($cancel_channel as $cc) {
                $cancel_channel_id = $cc['channel_id'];
                if ($schedule_channel_id == $cancel_channel_id) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_push($data, $sc);
            }
        }
        return $data;
    }

    public function filter_schedule_channel_v2($schedule_channel, $cancel_channel)
    {
        $data = array();
        foreach ($schedule_channel as $sc) {
            $schedule_channel_id = $sc['tv_channel_id'];
            $found = false;
            foreach ($cancel_channel as $cc) {
                $cancel_channel_id = $cc['channel_id'];
                if ($schedule_channel_id == $cancel_channel_id) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                array_push($data, $sc);
            }
        }
        return $data;
    }

    public function get_all_channels_of_internal_ro($campaign_ids, $customer_id)
    {
        /*$query = "select ac.campaign_id from sv_advertiser_campaign as ac where ac.internal_ro_number = '$internal_ro_number' and `is_make_good` = 0 and selected_region_id in (1,3) and visible_in_easyro = 1" ;
            $res = $this->db->query($query);
			$result = $res->result("array");
			$campaign_id = array();
			foreach($ as $val){
				array_push($campaign_id,$val['campaign_id']);
			}
            $campaign_ids = implode(",",$campaign_id);*/

        $channel_seconds_qry = "SELECT (ac.ro_duration*SUM( impressions )) as total_ad_seconds, sc.channel_id, c.channel_name,c.priority, e.revenue_sharing,ac.client_name,sd.screen_region_id,sd.date as scheduled_date FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and c.enterprise_id='$customer_id' and sd.campaign_id IN ($campaign_ids) and sd.screen_region_id in(1,3) AND sd.status !='cancelled' AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id and ac.is_make_good  = 0 GROUP BY c.channel_name,ac.ro_duration,sd.screen_region_id,sd.date";
        $channel_seconds = $this->db->query($channel_seconds_qry);
        $channel_seconds = $channel_seconds->result("array");
        $to_play_date = strtotime("+1 day");
        $channel_array = array();

        foreach ($channel_seconds as $value) {
            $channel_id = $value['channel_id'];
            if (!array_key_exists($channel_id, $channel_array)) {
                $channel_array[$channel_id] = array(
                    'channel_id' => '',
                    'channel_name' => '',
                    'revenue_sharing' => '',
                    'client_name' => '',
                    'priority' => '',
                    'total_spot_ad_seconds' => 0,
                    'to_play_spot' => 0,
                    'total_banner_ad_seconds' => 0,
                    'to_play_banner' => 0
                );
                $channel_array[$channel_id]['channel_id'] = $value['channel_id'];
                $channel_array[$channel_id]['channel_name'] = $value['channel_name'];
                $channel_array[$channel_id]['revenue_sharing'] = (float)$value['revenue_sharing'];
                $channel_array[$channel_id]['client_name'] = $value['client_name'];
                $channel_array[$channel_id]['priority'] = $value['priority'];
                if ($value['screen_region_id'] == 1) {
                    $channel_array[$channel_id]['total_spot_ad_seconds'] = (float)$value['total_ad_seconds'];
                    if (strtotime($value['scheduled_date']) <= $to_play_date) {
                        $channel_array[$channel_id]['to_play_spot'] = (float)$value['total_ad_seconds'];
                    }
                } else if ($value['screen_region_id'] == 3) {
                    $channel_array[$channel_id]['total_banner_ad_seconds'] = (int)$value['total_ad_seconds'];
                    if (strtotime($value['scheduled_date']) <= $to_play_date) {
                        $channel_array[$channel_id]['to_play_banner'] = (float)$value['total_ad_seconds'];
                    }
                }
            } else {
                if ($value['screen_region_id'] == 1) {
                    $channel_array[$channel_id]['total_spot_ad_seconds'] += (float)$value['total_ad_seconds'];
                    if (strtotime($value['scheduled_date']) <= $to_play_date) {
                        $channel_array[$channel_id]['to_play_spot'] += (float)$value['total_ad_seconds'];
                    }
                } else if ($value['screen_region_id'] == 3) {
                    $channel_array[$channel_id]['total_banner_ad_seconds'] += (float)$value['total_ad_seconds'];
                    if (strtotime($value['scheduled_date']) <= $to_play_date) {
                        $channel_array[$channel_id]['to_play_banner'] += (float)$value['total_ad_seconds'];
                    }
                }
            }
        }
//        echo '<pre>';print_r($channel_array);exit();
        return $channel_array;
    }

    public function get_tv_chnl_spot_bnr_avg_rates($channel_name)
    {
        $query = "select spot_avg,banner_avg from sv_tv_channel where channel_name='$channel_name'";
        $res = $this->db->query($query);
        return $res->result("array");
    }

    public function get_channel_detail($data)
    {
        $res = $this->db->get_where('sv_tv_channel', $data);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function update_external_ro_report_detail($data, $where)
    {
        $this->db->update('ro_external_ro_report_details', $data, $where);
    }

    public function get_customer_detail($where_data)
    {
        $res = $this->db->get_where('sv_customer', $where_data);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_campaigns_for_internal_ro_and_channel($internal_ro_number = '', $tv_channel_id = '')
    {
        $query = "select distinct campaign_id from sv_advertiser_campaign
                        where campaign_status NOT IN ( 'cancelled' )
                        AND internal_ro_number = '$internal_ro_number'  AND is_make_good =0 and channel_id = '$tv_channel_id'";

        //$query = "select distinct ac.campaign_id from sv_advertiser_campaign as ac, sv_screen as sc,sv_advertiser_campaign_screens_dates as sd where ac.internal_ro_number='$internal_ro_number' and sc.channel_id = '$tv_channel_id' and sc.screen_id=sd.screen_id and ac.campaign_id = sd.campaign_id";
        $res = $this->db->query($query);
        return $res->result("array");
    }

    public function checkEndDateCrossedForROCid($internal_ro_number, $cid)
    {
        $query = "select max(sacsd.date) as end_date  from sv_advertiser_campaign as sac "
            . "inner join sv_advertiser_campaign_screens_dates as sacsd on sac.campaign_id=sacsd.campaign_id "
            . "where sac.internal_ro_number='$internal_ro_number' and sacsd.enterprise_id='$cid' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $end_date = substr($result[0]['end_date'], 0, 11);
            $next_2_days = date("Y-m-d", strtotime("+2 days"));
            if (strtotime($next_2_days) > strtotime($end_date)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    public function checkEndDateCrossedForROCidAndCancelRo($internal_ro_number, $cid, $cancel_date)
    {
        $query = "select max(sacsd.date) as end_date  from sv_advertiser_campaign as sac "
            . "inner join sv_advertiser_campaign_screens_dates as sacsd on sac.campaign_id=sacsd.campaign_id "
            . "where sac.internal_ro_number='$internal_ro_number' and sacsd.enterprise_id='$cid' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $end_date = $result[0]['end_date'];
            if (strtotime($cancel_date) > strtotime($end_date)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    public function get_external_report($where)
    {
        $res = $this->db->get_where('ro_external_ro_report_details', $where);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function update_external_ro_report($data, $where)
    {
        $this->db->update('ro_external_ro_report_details', $data, $where);
    }

    public function get_approved_nw($where)
    {
        $res = $this->db->get_where('ro_approved_networks', $where);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_channel_impression_v1($campaign_ids = '', $tv_channel_id = '')
    {
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaign_ids) and acsd.screen_region_id in(1,3) and acsd.status IN ( 'scheduled') ";

        $query .= " GROUP BY  ac.campaign_id,acsd.screen_region_id";
        $results = $this->db->query($query);
        $spot_res = $results->result("array");

        $channel_array = array();
        $channel_array['total_spot_ad_seconds'] = 0;
        $channel_array['total_banner_ad_seconds'] = 0;

        //$channel_qry_spot = "SELECT ac.ro_duration,sc.channel_id, c.channel_name, e.revenue_sharing,sd.status,sd.impressions,sd.screen_region_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and sd.campaign_id IN ($campaign_ids) and sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id";
        // $channel_qry_spot_res = $this->db->query($channel_qry_spot);
        // $spot_res = $channel_qry_spot_res->result("array");


        foreach ($spot_res as $spot_res_val) {
            //$value = $this->get_total_ad_seconds_for_status($spot_res_val['status'],$spot_res_val['impressions'],$spot_res_val['ro_duration']) ;
            if ($spot_res_val['screen_region_id'] == 1) {
                $channel_array['total_spot_ad_seconds'] += $spot_res_val['total_ad_seconds'];
            } else if ($spot_res_val['screen_region_id'] == 3) {
                $channel_array['total_banner_ad_seconds'] += $spot_res_val['total_ad_seconds'];
            }

        }
        return $channel_array;
    }

    public function get_channel_impression($campaign_ids = '', $tv_channel_id = '')
    {
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaign_ids) and acsd.screen_region_id in(1,3) and ac.campaign_status NOT IN ( 'pending_approval', 'cancelled' ) ";

        $query .= " GROUP BY acsd.screen_region_id";
        $results = $this->db->query($query);
        $spot_res = $results->result("array");

        $channel_array = array();
        $channel_array['total_spot_ad_seconds'] = 0;
        $channel_array['total_banner_ad_seconds'] = 0;

        //$channel_qry_spot = "SELECT ac.ro_duration,sc.channel_id, c.channel_name, e.revenue_sharing,sd.status,sd.impressions,sd.screen_region_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and sd.campaign_id IN ($campaign_ids) and sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id";
        // $channel_qry_spot_res = $this->db->query($channel_qry_spot);
        // $spot_res = $channel_qry_spot_res->result("array");


        foreach ($spot_res as $spot_res_val) {
            //$value = $this->get_total_ad_seconds_for_status($spot_res_val['status'],$spot_res_val['impressions'],$spot_res_val['ro_duration']) ;
            if ($spot_res_val['screen_region_id'] == 1) {
                $channel_array['total_spot_ad_seconds'] += $spot_res_val['total_ad_seconds'];
            } else if ($spot_res_val['screen_region_id'] == 3) {
                $channel_array['total_banner_ad_seconds'] += $spot_res_val['total_ad_seconds'];
            }

        }
        return $channel_array;
    }

    public function get_total_ad_seconds_for_status($status, $impression, $duration)
    {
        $value = 0;
        if ($status == 'cancelled') {
            return $value;
        } elseif ($status == 'saved' || $status == 'scheduled') {
            $value = $duration * $impression;
            return $value;
        } else {
            return $value;
        }
    }

    public function get_nw_ro_number_from_nw_ro_report($data)
    {
        $result = $this->db->get_where('ro_network_ro_report_details', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
    }

    public function get_financial_year_for_ro_date($ro_date)
    {
        $month = date("m", strtotime($ro_date));

        $financial_year = '';
        if ($month <= 3) {
            $financial_year = date("y") - 1;
        } else {
            $financial_year = date("y");
        }

        return $financial_year;
    }

    public function get_revenue_share_for_ro_customer($internal_ro_number, $customer_id)
    {
        $data = array('internal_ro_number' => $internal_ro_number, 'customer_id' => $customer_id);
        $result = $this->db->get_where('ro_approved_networks', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function check_for_ro_cancellation($internal_ro_number)
    {
        $query = "select * from ro_cancel_external_ro where ext_ro_id in(select id from ro_am_external_ro where internal_ro='$internal_ro_number') and cancel_type = 'cancel_ro' and cancel_ro_by_admin='1' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_revision_number($internal_ro_number, $network_name)
    {
        $data = array('internal_ro_number' => $internal_ro_number, 'customer_name' => $network_name);
        $result = $this->db->get_where('ro_approved_networks', $data);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            return $res[0]['revision_no'];
        }
        return array();
    }

    public function get_nw_ro_number($internal_ro_number, $network_name)
    {
        $data = array('internal_ro_number' => $internal_ro_number, 'customer_name' => $network_name);
        $result = $this->db->get_where('ro_network_ro_report_details', $data);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            return $res[0]['network_ro_number'];
        }
        return NULL;
    }

    public function get_network_campaigns_new($order_id, $nw_id)
    {
        $query = "SELECT DISTINCT ac.campaign_id, sd.screen_region_id, sd.enterprise_id FROM sv_advertiser_campaign AS ac, sv_advertiser_campaign_screens_dates AS sd
                              WHERE internal_ro_number =  '$order_id' and sd.enterprise_id='$nw_id' and ac.visible_in_easyro='1' AND ac.campaign_id = sd.campaign_id AND sd.status !=  'cancelled'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_network_campaigns_v1_new($order_id, $nw_id)
    {
        $query = "SELECT DISTINCT ac.campaign_id, sd.screen_region_id, sd.enterprise_id FROM sv_advertiser_campaign AS ac, sv_advertiser_campaign_screens_dates AS sd
                              WHERE internal_ro_number =  '$order_id' and sd.enterprise_id='$nw_id' and ac.visible_in_easyro='1' AND ac.campaign_id = sd.campaign_id AND sd.status =  'cancelled' and ac.campaign_status != 'cancelled' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_historical_rate($client_name, $channel_name, $internal_ro_number)
    {
        $query = "select r. channel_spot_avg_rate as channel_spot_avg_rate,r.channel_banner_avg_rate as channel_banner_avg_rate  from(SELECT id, channel_spot_avg_rate,channel_banner_avg_rate FROM `ro_approved_networks` WHERE `client_name` = '$client_name' and `channel_name` = '$channel_name' and internal_ro_number='$internal_ro_number' ORDER BY `ro_approved_networks`.`id`  DESC  limit 1) as r";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_historical_rate_for_client($client_name, $channel_name)
    {
        $query = "SELECT id, channel_spot_avg_rate,channel_banner_avg_rate FROM ro_approved_networks WHERE client_name = '$client_name' and channel_name = '$channel_name' ORDER BY id  DESC  limit 1 ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_channel_default_rates($channel_name)
    {
        $query = "select spot_avg,banner_avg from sv_tv_channel where channel_name='$channel_name'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_nw_detail_for_ro($internal_ro_number)
    {
//        $query = "select ac.campaign_id from sv_advertiser_campaign as ac where ac.internal_ro_number = '$internal_ro_number' and `is_make_good` = 0 and selected_region_id in (1,3) and visible_in_easyro = 1";
//        $res = $this->db->query($query);
//        $result = $res->result("array");
//        $campaign_id = array();
//        foreach ($result as $val) {
//            array_push($campaign_id, $val['campaign_id']);
//        }
//        $campaign_ids = implode(",", $campaign_id);

        $whereData = array('internal_ro_number' => $internal_ro_number, 'is_make_good' => 0, 'visible_in_easyro' => 1);
        $result = $this->db->select('GROUP_CONCAT(campaign_id) AS campaign_ids')
            ->where($whereData)
            ->where_in('selected_region_id', array(1, 3))
            ->get('sv_advertiser_campaign')
            ->result("array");
        $campaign_ids = $result[0]['campaign_ids'];

        if ($campaign_ids != NULL) {
            /*$customer_qry = "SELECT distinct c.customer_id,c.customer_name,c.revenue_sharing,sm.sw_market_name FROM sv_advertiser_campaign_screens_dates AS acsd,sv_customer as c,sv_sw_market as sm,sv_customer_sw_location as csl WHERE acsd.enterprise_id=c.customer_id and acsd.campaign_id IN ($campaign_ids) and acsd.screen_region_id in(1,3) AND acsd.status ='scheduled' and c.customer_id=csl.customer_id and csl.sw_market_id=sm.id order by sm.sw_market_name,c.customer_name ";*/
            //Nitish: changed query for cluster implementation & to reduce execution time
            $customer_qry = "SELECT DISTINCT x.customer_id, x.customer_name, x.revenue_sharing, group_concat( x.sw_market_name ) as sw_market_name,group_concat( x.id ) as market_id
                                            FROM (
                                            select distinct c.customer_id, c.customer_name, c.revenue_sharing, sm.sw_market_name,sm.id
                                            FROM sv_advertiser_campaign sac
                                            INNER JOIN sv_advertiser_campaign_screens_dates acsd ON acsd.campaign_id = sac.campaign_id
                                            INNER JOIN sv_sw_market sm ON sm.id = sac.market_id
                                            INNER JOIN sv_tv_channel stc ON stc.tv_channel_id = sac.channel_id
                                            INNER JOIN sv_customer as c ON stc.enterprise_id = c.customer_id
                                            where sac.campaign_id IN ($campaign_ids)
                                            AND acsd.status ='scheduled'
                                            order by sm.sw_market_name,c.customer_name
                                            )x
                                            GROUP BY x.customer_name";
        } else {
            /*$customer_qry = "SELECT distinct c.customer_id,c.customer_name,c.revenue_sharing,sm.sw_market_name FROM sv_advertiser_campaign_screens_dates AS acsd,sv_customer as c,sv_sw_market as sm,sv_customer_sw_location as csl WHERE acsd.enterprise_id=c.customer_id and acsd.campaign_id IN ('') and acsd.screen_region_id in(1,3) AND acsd.status ='scheduled' and c.customer_id=csl.customer_id and csl.sw_market_id=sm.id order by sm.sw_market_name,c.customer_name ";*/
            //Nitish: changed query for cluster implementation & to reduce execution time
            $customer_qry = "SELECT DISTINCT x.customer_id, x.customer_name, x.revenue_sharing, group_concat( x.sw_market_name ) as sw_market_name
                                            FROM (
                                            select distinct c.customer_id, c.customer_name, c.revenue_sharing, sm.sw_market_name
                                            FROM sv_advertiser_campaign sac
                                            INNER JOIN sv_advertiser_campaign_screens_dates acsd ON acsd.campaign_id = sac.campaign_id
                                            INNER JOIN sv_sw_market sm ON sm.id = sac.market_id
                                            INNER JOIN sv_tv_channel stc ON stc.tv_channel_id = sac.channel_id
                                            INNER JOIN sv_customer as c ON stc.enterprise_id = c.customer_id
                                            where sac.campaign_id IN ('')
                                            AND acsd.status ='scheduled'
                                            order by sm.sw_market_name,c.customer_name
                                            )x
                                            GROUP BY x.customer_name";
        }
        $customer_res = $this->db->query($customer_qry);
        if ($customer_res->num_rows() > 0) {
            return array('campaignIds' => $campaign_ids, 'network_detail' => $customer_res->result("array"));
        }
        return array('campaignIds' => $campaign_ids, 'network_detail' => array());

        /*if ($customer_res->num_rows() > 0) {
            return $customer_res->result("array");
        }
        return array();*/

    }

    public function is_approved_ro($data)
    {
        $result = $this->db->get_where('ro_approved_networks', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        } else {
            return array();
        }
    }

    public function is_cancellation_request_sent($ro_id)
    {
        $query = "select * from ro_cancel_external_ro where ext_ro_id = $ro_id and cancel_type in ('cancel_ro','cancel_market','cancel_brand','cancel_content') and cancel_ro_by_admin = 0";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        } else {
            return array();
        }
    }

    public function is_approved_v1($internal_ro_number, $cid, $channel_id)
    {
        $query = "select (CASE WHEN ran.total_spot_ad_seconds = 0 THEN svt.spot_avg ELSE ran.channel_spot_avg_rate END) AS channel_spot_avg_rate,
                            (CASE WHEN ran.total_banner_ad_seconds = 0 THEN svt.banner_avg ELSE ran.channel_banner_avg_rate END) AS channel_banner_avg_rate,
                            ran.id,ran.internal_ro_number,ran.client_name,
                            ran.customer_id,ran.customer_name,ran.customer_share,ran.customer_location,
                            ran.tv_channel_id,ran.channel_name,ran.total_spot_ad_seconds,ran.channel_spot_amount,
                            ran.total_banner_ad_seconds,ran.channel_banner_amount,ran.channel_approval_status,
                            ran.pdf_generation_status,ran.billing_name,ran.revision_no,ran.pdf_processing
                            FROM ro_approved_networks ran
                            inner join sv_tv_channel svt on svt.tv_channel_id = ran.tv_channel_id
                            where ran.internal_ro_number='$internal_ro_number'
                            and ran.customer_id='$cid' and ran.tv_channel_id= '$channel_id' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        } else {
            return FALSE;
        }
    }

    public function get_from_job_queue($data)
    {
        $result = $this->db->get_where('sv_job_queue', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function insertIntoJobQueue($user_data)
    {
        $this->db->insert('sv_job_queue', $user_data);
    }

    public function update_job_queue($where_data, $user_data)
    {
        $this->db->update('sv_job_queue', $user_data, $where_data);
    }

    public function is_edit_in_progress($key_for_edit, $job_id, $done)
    {
        $query = "select * from sv_job_queue where job_code='$job_id' and done='$done' and params like '%$key_for_edit%'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function monthwise_breakup_for_given_ro($internal_ro_number, $channels_scheduled)
    {
        $query = "select id,camp_start_date,camp_end_date from ro_am_external_ro where internal_ro='$internal_ro_number'";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $ro_result = $result->result("array");

            $ro_start_date = $ro_result[0]['camp_start_date'];
            $ro_end_date = $ro_result[0]['camp_end_date'];
            $am_ext_id = $ro_result[0]['id'];

            $monthly_values = array();
            $total_revenue = 0;
            $total_network_payout_val = 0;
            $total_sw_net_contribution = 0;
            $new_values = strtotime($ro_start_date);

            //echo "ro_start_date-".$ro_start_date."<br/>" ;
            //echo "ro_end_date-".$ro_end_date."<br/>" ;
            for ($i = strtotime($ro_start_date); $i <= strtotime($ro_end_date); $i = strtotime($new_values)) {
                $start_date = date("Y-m-d", $i);
                $end_date = date("Y-m-t", $i);

                $next_month = date("m", strtotime("+1 month", $i));
                $year = date("Y", strtotime("+1 month", $i));
                $new_values = $year . "-" . $next_month . "-01";
                //echo "new_value-".$new_values ;

                $key = date("M-y", $i);
                //$monthly_values[$month] = $month ;

                $proportional_calculation = $this->time_period_proportional_calculation_channels($am_ext_id, $internal_ro_number, $start_date, $end_date, $channels_scheduled);


                $monthly_values[$key]['revenue'] = $total_revenue + round($proportional_calculation['revenue']);
                $monthly_values[$key]['network_payout'] = $total_network_payout_val + round($proportional_calculation['total_nw_payout']);
                $monthly_values[$key]['net_contribution'] = $total_sw_net_contribution + round($proportional_calculation['total_sw_net_contribution']);
                $monthly_values[$key]['net_contribution_percent'] = round(($proportional_calculation['total_sw_net_contribution'] / $proportional_calculation['revenue']) * 100);
            }
            return $monthly_values;
        }
    }

    public function time_period_proportional_calculation_channels($am_ext_id, $internal_ro, $given_start_date, $given_end_date, $channels_scheduled)
    {
        $channel_scheduled_ids = array_keys($channels_scheduled);
        $market_price_data = array('ro_id' => $am_ext_id);

        $market_price = $this->get_market_ro_price($market_price_data);
        $ro_amount_values = $this->get_ro_amount($internal_ro);

        $nw_payout = 0;
        $ro_value_market = 0;

        foreach ($market_price as $mp) {
            $market_name = $mp['market'];
            $spot_market_price = $mp['spot_price'];
            $banner_market_price = $mp['banner_price'];
            //echo "market_name-".$market_name."<br/>";
            //echo "spot_price-".$spot_market_price."<br/>";
            //echo "banner_price-".$banner_market_price."<br/>";
            //* remaining get channel_id and channel_name for that market
            $channel_detail = $this->get_scheduled_channel_for_market($market_name, $internal_ro);

            //$total_spot_fraction = 0 ;
            //$total_banner_fraction = 0 ;
            $scheduled_spot_seconds = 0;
            $scheduled_banner_seconds = 0;
            $total_spot_second = 0;
            $total_banner_second = 0;
            $channel_payout = 0;
            //$count = 0 ;

            foreach ($channel_detail as $chnl) {
                $channel_id = $chnl['tv_channel_id'];
                $market_id = $chnl['market_id'];

                //echo "channel_id-".$channel_id."<br/>";
                //get data from ro_approved_network
                $approved_nw_data = $this->get_approved_data_for_customer_channel_ro(array('tv_channel_id' => $channel_id, 'internal_ro_number' => $internal_ro));

                //find scheduled fct for given time period
                $scheduled_impression = $this->get_scheduled_impression_for_time_period($channel_id, $internal_ro, $given_start_date, $given_end_date, $market_id);
                //echo "schedule_impression-".print_r($scheduled_impression,true)."<br/>";
                $total_scheduled_seconds = $this->get_total_scheduled_seconds($internal_ro, $channel_id);
                //echo "schedule_impression-".print_r($total_scheduled_seconds,true)."<br/>";
                if ($scheduled_impression['spot_ad_seconds'] == 0 && $scheduled_impression['banner_ad_seconds'] == 0) {
                    continue;
                } else {
                    $scheduled_spot_seconds = $scheduled_spot_seconds + $scheduled_impression['spot_ad_seconds'];
                    //echo "schedule_spot_sec-".$scheduled_spot_seconds."<br/>";
                    $scheduled_banner_seconds = $scheduled_banner_seconds + $scheduled_impression['banner_ad_seconds'];
                    //echo "schedule_banner_sec-".$scheduled_banner_seconds."<br/>";
                    $total_spot_second = $total_spot_second + $total_scheduled_seconds['spot_ad_seconds'];
                    //echo "total_spot_second-".$total_spot_second."<br/>";
                    $total_banner_second = $total_banner_second + $total_scheduled_seconds['banner_ad_seconds'];
                    //echo "total_banner_second-".$total_banner_second."<br/>";
                    $channel_payout += ($scheduled_impression['spot_ad_seconds'] * $approved_nw_data[0]['channel_spot_avg_rate'] * ($approved_nw_data[0]['customer_share'] / 100)) / 10 + ($scheduled_impression['banner_ad_seconds'] * $approved_nw_data[0]['channel_banner_avg_rate'] * ($approved_nw_data[0]['customer_share'] / 100)) / 10;
                    // echo "channel_payout--".$channel_payout."<br/>";
                    //$count += 1 ;
                }
            }

            $average_spot_fraction = $scheduled_spot_seconds / $total_spot_second;
            // echo "average_spot_fraction".$average_spot_fraction."<br/>";
            $average_banner_fraction = $scheduled_banner_seconds / $total_banner_second;
            // echo "average_banner_fraction".$average_banner_fraction."<br/>";
            $market_price = $spot_market_price * $average_spot_fraction + $banner_market_price * $average_banner_fraction;
            //echo "market_price--".$market_price."<br/>";
            $nw_payout += $channel_payout;
            //echo "networ_payout--".$nw_payout."<br/>";
            $ro_value_market += $market_price;
            //echo "ro_market_value--".$ro_value_market."<br/><br/>";
        }
        $ro_fraction = $ro_value_market / $ro_amount_values[0]['ro_amount'];
        //echo "ro_fraction-".$ro_fraction."<br/>";
        $agency_commission = $ro_amount_values[0]['agency_commission_amount'] * $ro_fraction;
        //echo "agency_commision-".$agency_commission."<br/>" ;
        $result = array();
        //doubt :how to divide agency commision amount
        //echo "ro_value_market-".$ro_value_market."<br/>" ;
        $result['revenue'] = ($ro_value_market - $agency_commission) * SERVICE_TAX;
        //echo "final_revenue-".$result['revenue']."<br/>" ;
        $result['total_nw_payout'] = $nw_payout;

        $agency_rebate = 0;
        if ($ro_amount_values[0]['agency_rebate_on'] == "ro_amount") {
            $agency_rebate = $ro_value_market * ($ro_amount_values[0]['agency_rebate'] / 100);
        } else {
            $agency_rebate = ($ro_value_market - $agency_commission) * ($ro_amount_values[0]['agency_rebate'] / 100);
        }
        //$agency_rebate = $agency_rebate*$ro_fraction ;
        $other_expenses_ary = $this->get_external_ro_report_details($internal_ro);
        $other_expenses = $other_expenses_ary[0]['other_expenses'] * $ro_fraction;

        $actual_net_amount = $ro_value_market - $agency_commission - $agency_rebate - $other_expenses;

        $result['total_sw_net_contribution'] = $actual_net_amount - $nw_payout;

        return $result;

    }

    /***************----- Date 2014- Nov 26 PDF Generation ------*****************/

    function getRoCustomersChannels()
    {

        $query = " select distinct
                        internal_ro_number,
                        customer_id,
                        stc.tv_channel_id,
                        stc.channel_name
                    from
                        ro_approved_networks rap
                        inner join
                        sv_tv_channel stc ON rap.customer_id = stc.enterprise_id
                    where
                        rap.pdf_generation_status = 0
                        and rap.pdf_processing = 0";

        $result = $this->db->query($query);

        $dataArray = array();

        if ($result->num_rows() > 0) {

            foreach ($result->result() as $key => $value) {

                $dataArray[$value->internal_ro_number][$value->customer_id][] = $value->tv_channel_id;

            }

        }

        return $dataArray;

        /* Return Array Structure

        Array
        (           Internal Ro
            [SW/14-0191/Coca Cola India Ltd/Surewaves/Nov-2014-001] => Array
                (    customer id and channel id
                    [95] => Array
                        (
                            [0] => 172
                            [1] => 171
                        )

                )

            [SW/14-0192/Titan Industries Ltd/Surewaves/Nov-2014-001] => Array
                (
                    [156] => Array
                        (
                            [0] => 214
                        )

                    [95] => Array
                        (
                            [0] => 172
                        )

                )

        )*/


    }

    function getChannelIdReportData($channelId, $internalRoNumber)
    {

        $query = " select
                        svsd.screen_id,
                        svac.caption_name,
                        svsd.impressions as actual_impession,
                        svac.ro_duration as duration,
                        svac.language,
	                    svac.brand_new as brand,
                        (CASE
                            WHEN svsd.screen_region_id = 1 THEN 'Spot Ad'
                            WHEN svsd.screen_region_id = 2 THEN 'BrandingLogo'
                            WHEN svsd.screen_region_id = 3 THEN 'Banner'
                            WHEN svsd.screen_region_id = 4 THEN 'Logo'
                            WHEN svsd.screen_region_id = 7 THEN 'DayBrandingLogo'
                            WHEN svsd.screen_region_id = 8 THEN 'SplashAd'
                            WHEN svsd.screen_region_id = 9 THEN 'ScrollingBanner'
                            WHEN svsd.screen_region_id = 10 THEN 'FlashBanner'
                            WHEN svsd.screen_region_id = 11 THEN 'TopBanner'
                            ELSE svsd.screen_region_id
                        END) as screen_region,
                        (CASE
                        WHEN svsd.status = 'scheduled' and svac.campaign_status != 'cancelled' THEN svsd.impressions
                        WHEN svsd.status = 'cancelled' and svac.campaign_status != 'cancelled' THEN 0
                        WHEN svsd.status = 'cancelled' and svac.campaign_status  = 'cancelled' THEN '-'
                        END) as final_impressions,
                        svsd.status as screen_dates_status,
                        svac.campaign_status,
                        ss.start_time,
                        ss.end_time,
                        svsd.date as scheduled_date,
                        stc.channel_name,
                        stc.tv_channel_id

                    from
                        sv_advertiser_campaign svac
                            inner join
                        sv_advertiser_campaign_screens_dates svsd ON svac.campaign_id = svsd.campaign_id
                            inner join
                        sv_screen ss ON svsd.screen_id = ss.screen_id
                            inner join
                        sv_tv_channel stc ON svac.channel_id = stc.tv_channel_id
                    where
                        svac.internal_ro_number = '$internalRoNumber'
                        and svac.channel_id = '$channelId'
                        order by svsd.date, ss.start_time  ";

        $result = $this->db->query($query);

        return $result;
    }

    public function getPDFHeaderDataFromDB($internal_ro_number, $customer_id)
    {

        $query = " select distinct
                    rap.customer_name,
                    GROUP_CONCAT( DISTINCT stc.channel_name ) as channels,
                    raer.agency,
                    raer.client,
                    swm.sw_market_name,
                    rap.billing_name,
                    rap.revision_no
                from
                    ro_approved_networks rap
                        inner join
                    sv_tv_channel stc ON rap.tv_channel_id = stc.tv_channel_id
                        inner join
                    ro_am_external_ro raer ON rap.internal_ro_number = raer.internal_ro
                        inner join
                    sv_customer_sw_location scsl ON rap.customer_id = scsl.customer_id
                        inner join
                    sv_sw_market swm ON scsl.sw_market_id = swm.id
                where
                    rap.internal_ro_number = '$internal_ro_number'
                        and rap.customer_id = '$customer_id'

                 ";

        $result = $this->db->query($query);

        return $result;

    }

    public function getAmountForInternalRoAndCustomerId($customerId, $internalRoNumber)
    {

        $query = " select
                        customer_share,
                        SUM(channel_spot_amount) + SUM(channel_banner_amount) as investement,
                        (( SUM(channel_spot_amount) + SUM(channel_banner_amount) ) * customer_share)/100 as  network_payout
                    from
                        ro_approved_networks
                    where
                         internal_ro_number = '$internalRoNumber'
                            and customer_id = '$customerId' ";

        $result = $this->db->query($query);

        return $result;

    }

    public function getCustomerRoClient($internalRoNumber)
    {

        $query = " SELECT
                        cust_ro, client
                    FROM
                        ro_am_external_ro
                    where
                        internal_ro = '$internalRoNumber' ";

        $result = $this->db->query($query);

        return $result;

    }

    public function checkRoNetworkRowExist($dataArray)
    {

        $customerRoNumber = $dataArray["customerRoAndClient"]["customerRo"];
        $internalRoNumber = $dataArray["customerRoAndClient"]["internalRoNumber"];
        $networkRoNumber = $dataArray["customerRoAndClient"]["networkRoNumber"];

        $query = " SELECT
                    count(customer_ro_number) as countRow
                FROM
                    ro_network_ro_report_details
                where
                    customer_ro_number = '$customerRoNumber'
                        AND internal_ro_number = '$internalRoNumber'
                        AND network_ro_number = '$networkRoNumber' ";

        $result = $this->db->query($query);

        if (($result->row()->countRow) > 0) {

            return TRUE;

        } else {

            return FALSE;

        }

    }

    public function insertRoNetworkRoReportDetails($dataArray)
    {

        $this->db->set("customer_ro_number", $dataArray["customerRoAndClient"]["customerRo"]);
        $this->db->set("internal_ro_number", $dataArray["customerRoAndClient"]["internalRoNumber"]);
        $this->db->set("network_ro_number", $dataArray["customerRoAndClient"]["networkRoNumber"]);
        $this->db->set("customer_name", $dataArray["heading"]["Network_Name"]);

        $this->db->set("client_name", $dataArray["customerRoAndClient"]["clientName"]);
        $this->db->set("agency_name", $dataArray["heading"]["Advertiser_Name"]);
        $this->db->set("market", $dataArray["heading"]["Market"]);
        $this->db->set("start_date", $dataArray["heading"]["Campaign_start_Period"]);

        $this->db->set("end_date", $dataArray["heading"]["Campaign_end_Period"]);
        $this->db->set("activity_months", $dataArray["customerRoAndClient"]["monthActivity"]);
        $this->db->set("gross_network_ro_amount", $dataArray["heading"]["Investment"]);
        $this->db->set("customer_share", $dataArray["heading"]["customerShare"]);

        $this->db->set("net_amount_payable", $dataArray["heading"]["Net_Amount_Payable"]);
        $this->db->set("release_date", $dataArray["heading"]["Release_Date"]);


        $this->db->insert("ro_network_ro_report_details");

    }

    public function updateRoNetworkRoReportDetails($dataArray)
    {

        $data = array(
            "gross_network_ro_amount" => $dataArray["heading"]["Investment"],
            "customer_share" => $dataArray["heading"]["customerShare"],
            "net_amount_payable" => $dataArray["heading"]["Net_Amount_Payable"]
        );

        $whereData = array(
            "customer_ro_number" => $dataArray["customerRoAndClient"]["customerRo"],
            "internal_ro_number" => $dataArray["customerRoAndClient"]["internalRoNumber"],
            "network_ro_number" => $dataArray["customerRoAndClient"]["networkRoNumber"]
        );

        $this->db->where($whereData);
        $this->db->update("ro_network_ro_report_details", $data);
    }

    public function updatePDFProcessingStatus($internalRoNumber, $customerIds, $set)
    {

        $this->db->where("internal_ro_number", $internalRoNumber);
        $this->db->where_in("customer_id", $customerIds);
        $this->db->update("ro_approved_networks", $set);

    }

    /***************----- END Date 2014- Nov 26 PDF Generation ------*****************/
    public function isCampaignAvailableForMarketAndRo($inernalRo, $market, $cancelDate)
    {
        $isRoApproved = $this->is_approved(array('internal_ro_number' => $inernalRo));
        if (count($isRoApproved) > 0) {
            $result = $this->getScheduledChannelForRoAndMarket($inernalRo, $market, $cancelDate);
            if ($result[0]['channel_id'] == NUll) {
                return 0;
            } else {
                return 1;
            }

        } else {
            return 0;
        }

    }

    public function is_approved($data)
    {
        $result = $this->db->get_where('ro_approved_networks', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        } else {
            return FALSE;
        }
    }

    public function getScheduledChannelForRoAndMarket($inernalRo, $market, $date)
    {
        if (!isset($date) || empty($date)) {
            $date = date('Y-m-d', strtotime("+2 days"));
        }


        /*$query = " select GROUP_CONCAT(DISTINCT stc.tv_channel_id) as channel_id,sac.approved_status
                  from
                    sv_advertiser_campaign sac
                        inner join
                    sv_advertiser_campaign_screens_dates sacd ON sac.campaign_id = sacd.campaign_id
                        inner join
                    sv_tv_channel stc ON sac.channel_id = stc.tv_channel_id
                        inner join
                    sv_customer sc ON stc.enterprise_id = sc.customer_id
                        inner join
                    sv_customer_sw_location scsl ON sc.customer_id = scsl.customer_id
                        inner join
                    sv_sw_market swm ON scsl.sw_market_id = swm.id
                where
                    sc.customer_id = sacd.enterprise_id
                    and sac.internal_ro_number = '$inernalRo'
                    and swm.sw_market_name = '$market'
                    and sacd.status = 'scheduled'
                    and sacd.date < '$date'
                    and sac.approved_status = 'Approved'

                 " ; */

        $query = "select GROUP_CONCAT(DISTINCT sac.channel_id) as channel_id
				from
                    sv_advertiser_campaign sac
                        inner join
                    sv_advertiser_campaign_screens_dates sacd ON sac.campaign_id = sacd.campaign_id
                        inner join
                    sv_sw_market swm ON sac.market_id = swm.id
				where
                    sac.internal_ro_number = '$inernalRo'
                    and swm.sw_market_name = '$market'
                    and sacd.status != 'cancelled' 
                    and sacd.date < '$date' 
                    and sac.approved_status = 'Approved' and sac.is_value_added = 0";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function clusterToMarket()
    {
        $clusterMaket = $this->getClusterMarket();
        $data = array();
        foreach ($clusterMaket as $cmt) {

        }
    }

    public function getClusterMarket()
    {
        $query = "SELECT swc.id,swc.cluster_name,swm.id,swm.sw_market_name 
                  FROM
                sv_sw_clusters swc
                    INNER JOIN
                sv_cluster_market swcm ON swc.id = swcm.cluster_id
                    INNER JOIN
                sv_sw_market swm ON swcm.market_id = swm.id";
        $result = $this->db->query($query);
        return $result;

    }

    function getRosForEmailAndPdfStatusFromDB($startDate, $endDate)
    {

        $query = " select
                     distinct rae.internal_ro
                    from
                        ro_am_external_ro rae
                        inner join
                        ro_approved_networks rap ON rae.internal_ro = rap.internal_ro_number
                    where
                        (rae.ro_date between '" . $startDate . "' and '" . $endDate . "') ";

        $resultObject = $this->db->query($query);

        return $resultObject;
    }

    //insert into progression mail status

    function getRoDetailsFromDb($internalRoNumber)
    {

        $query = " SELECT DISTINCT
                    stc.channel_name,
                    stc.tv_channel_id,
                    sc.customer_id,
                    sc.customer_name,
                    sdc.approved_status
                FROM
                    sv_advertiser_campaign sdc
                        INNER JOIN
                    sv_tv_channel stc ON sdc.channel_id = stc.tv_channel_id
                        INNER JOIN
                    sv_customer sc ON stc.enterprise_id = sc.customer_id
                WHERE
                    sdc.visible_in_easyro = 1
                        AND sdc.campaign_status != 'cancelled'
                        AND sdc.internal_ro_number = '$internalRoNumber' ";

        $resultObject = $this->db->query($query);

        return $resultObject;
    }

    function getPdfGenerationStatusFromDB($internalRoNumber, $customerId)
    {

        $query = " SELECT
                            pdf_generation_status, pdf_processing
                        FROM
                            ro_approved_networks
                        WHERE
                            internal_ro_number = '$internalRoNumber'
                                AND customer_id = '$customerId' ";

        $resultObject = $this->db->query($query);

        return $resultObject;
    }

    function getSentMailCount($subject)
    {

        $query = " SELECT
						COUNT(*) AS totalMailSent
					FROM
						ro_mail_data
					WHERE
						subject LIKE '%$subject%'; ";

        $resultObject = $this->db->query($query);

        return $resultObject;
    }

    function getSentMailPDFLink($subject)
    {

        $query = " SELECT
						file_name as filePath
					FROM
						ro_mail_data
					WHERE
						subject LIKE '%$subject%'
					ORDER BY id DESC
					LIMIT 1 ";

        $resultObject = $this->db->query($query);

        return $resultObject;
    }

    function getLatestInsertedSubjectRowId($subject, $date)
    {

        $query = " SELECT
						id
					FROM
						ro_mail_data
					WHERE
						subject LIKE '%$subject%'
						AND pdf_generation_date = '$date' ";

        $resultObject = $this->db->query($query);

        return $resultObject;
    }

    public function getChannelGroupByNoticed($network_name)
    {
        $channelCustomerDetail = $this->getChannelFromNeworkMarketForNetworkName($network_name);
        if (count($channelCustomerDetail) > 0) {
            return $this->getNoticedChannel($channelCustomerDetail);
        } else {
            return 'Not_Noticed';
        }

    }

    function getChannelFromNeworkMarketForNetworkName($network_name)
    {
        /*
        $query = "select  ssm.id.ssm.sw_market_name "
                . " from sv_customer sc"
                . " inner join sv_customer_sw_location scsl on sc.customer_id=scsl.customer_id"
                . " inner join sv_sw_market ssm on ssm.id = scsl.sw_market_id "
                . " where sc.customer_name='$network_name' " ;
        $result = $this->db->query($query);
        if($result->num_rows() > 0) {
                return TRUE ;
        } */
        $query = "select distinct sw_market_id from sv_customer sc"
            . " inner join sv_customer_sw_location scsl on sc.customer_id = scsl.customer_id "
            . " where sc.customer_name='$network_name' order by sw_market_id ";
        $result_val = $this->db->query($query);
        $mktresult = $result_val->result("array");
        $marketIds = array();
        foreach ($mktresult as $val) {
            array_push($marketIds, $val['sw_market_id']);
        }
        $marketIds = implode(",", $marketIds);

        $query = "select  distinct stc.tv_channel_id as channel_id,stc.channel_name,stc.is_notice,stc.is_notice_ro,sc.customer_id,sc.customer_name,sc.customer_email "
            . " from sv_tv_channel stc"
            . " inner join sv_customer sc on sc.customer_id = stc.enterprise_id "
            . " inner join sv_customer_sw_location scsl on sc.customer_id=scsl.customer_id"
            . " where scsl.sw_market_id in ($marketIds) order by sc.customer_id";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
    }

    public function getNoticedChannel($channelCustomerDetail)
    {
        $data = array();
        foreach ($channelCustomerDetail as $ccd) {
            $customerId = $ccd['customer_id'];
            if (!array_key_exists($customerId, $data)) {
                $data[$customerId]['customer_name'] = $ccd['customer_name'];
                $data[$customerId]['network_email'] = $ccd['customer_email'];
                $data[$customerId]['is_notice_ro'] = $ccd['is_notice_ro'];
                if ($ccd['is_notice_ro'] == 1) {
                    $data[$customerId]['all_channel_noticed'] = 'No';
                } else {
                    if ($ccd['is_notice'] == 1) {
                        $data[$customerId]['all_channel_noticed'] = 'Yes';
                        $data[$customerId]['channel_names'] = $ccd['channel_name'];
                    }

                }

            } else {
                if ($data[$customerId]['is_notice_ro'] == 1) {
                    $data[$customerId]['all_channel_noticed'] = 'No';
                } else {
                    if ($ccd['is_notice_ro'] == 1) {
                        $data[$customerId]['all_channel_noticed'] = 'No';
                        $data[$customerId]['is_notice_ro'] = $ccd['is_notice_ro'];
                    } else {
                        if ($ccd['is_notice'] == 1) {
                            $data[$customerId]['all_channel_noticed'] = 'Yes';
                            $data[$customerId]['is_notice_ro'] = $ccd['is_notice_ro'];
                            $data[$customerId]['channel_names'] = $data[$customerId]['channel_names'] . " , " . $ccd['channel_name'];
                        }
                    }

                }
            }
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return 'Not_Noticed';
        }


    }

    public function insert_into_progression_mail_status($user_data)
    {

        $this->db->insert('ro_progression_mail_status', $user_data);
        log_message('info', 'ro_creation printing query STEP_22 --' . $this->db->last_query());
    }

    public function update_progression_mail_status($whereData, $data)
    {

        $this->db->where($whereData);
        $this->db->update('ro_progression_mail_status', $data);

        return array();
    }

    public function get_available_progression_mail_status($whereData)
    {
        return $this->is_avaialble_progression_mail_status($whereData);
    }

    public function isNetworkHaveNoticedChannelRo($networkName)
    {
        $noticedChannelR0 = $this->getNoticedROChannelForNetwork($networkName);

        if (count($noticedChannelR0) > 0) {
            $channel_ids = '';
            foreach ($noticedChannelR0 as $ncr) {
                if ($ncr['is_notice_ro'] == 1 && $ncr['is_notice'] != 1) {
                    if (empty($channel_ids) || !isset($channel_ids)) {
                        $channel_ids = $ncr['channel_name'];
                    } else {
                        $channel_ids .= "," . $ncr['channel_name'];
                    }

                }
            }
            if (!empty($channel_ids) && isset($channel_ids)) {
                return $channel_ids;
            } else {
                return '0';
            }
        } else {
            return '0';
        }
    }

    public function getNoticedROChannelForNetwork($networkName)
    {
        $query = "select  distinct stc.tv_channel_id as channel_id,stc.channel_name,stc.is_notice,stc.is_notice_ro,sc.customer_id,sc.customer_name,sc.customer_email "
            . " from sv_tv_channel stc"
            . " inner join sv_customer sc on sc.customer_id = stc.enterprise_id"
            . " where sc.customer_name='$networkName' ";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    /**
     * Method to Fetch the Email Details of ROs from the Progression Email Details
     * @param array $whereData
     * @return array
     */
    public function getBrandName($brandIds)
    {
        $query = "select group_concat(distinct brand) as brand"
            . " from sv_new_brand "
            . " where id in ($brandIds)";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        } else {
            return array();
        }

    }

    public function getBrandName_v1($brandIds)
    {
        $query = "select brand from sv_new_brand where id=$brandIds";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        } else {
            return array();
        }

    }

    /**
     * Get All the Email Details, including E-Mail ids, and, the Email Body
     * @param string $type - Defines what type of Email Contents have to be fetched
     * @return type
     */
    public function getProgressionEmailRoDetails($type)
    {

        $type_conditions = array(

            SUBMIT => json_decode(SUBMIT_CON),
            APPROVED => json_decode(APPROVED_CON),
            CAMPAIGN_START => json_decode(CAMPAIGN_START_CON),
            CAMPAIGN_PRE_CLOSURE => json_decode(CAMPAIGN_PRE_CLOSURE_CON),
            CAMPAIGN_END => json_decode(CAMPAIGN_END_CON),
            RO_CANCEL => json_decode(RO_CANCEL_CON),
            EDIT_RO => json_decode(EDIT_RO_CON),
            MARKET_CANCEL => json_decode(MARKET_CANCEL_CON)
        );

        $dataArray = $this->getRosToProgressionWithBasicDetails($type_conditions[$type], $type);

        return $dataArray;
    }

    private function getRosToProgressionWithBasicDetails($whereData, $type)
    {

        if ($type == RO_CANCEL) {

            $query = " SELECT DISTINCT
                        eo.id AS ro_id,
                        eo.cust_ro,
                        eo.client,
                        eo.brand,
                        eo.order_history_mail_list,
                        ru.user_id,
                        ru.user_phone,
                        ru.user_name,
                        rcer.date_of_cancel,
                        rcer.cancel_ro_by_admin,
                        ru.profile_image
                    FROM
                        ro_progression_mail_status ps
                            INNER JOIN
                        ro_am_external_ro eo ON ps.ro_id = eo.id
                            INNER JOIN
                        ro_user ru ON eo.user_id = ru.user_id
                            INNER JOIN
                        ro_cancel_external_ro rcer ON eo.id = rcer.ext_ro_id
                    WHERE
                        rcer.cancel_type = 'cancel_ro'
                            AND rcer.cancel_ro_by_admin = 1
                            AND eo.order_history_mail_list != '' AND ";

        } else if ($type == MARKET_CANCEL) {

            $query = " SELECT
                            rpms.ro_id,
                            rpms.market_cancel_count AS oldCount,
                            SUM(rmp.is_cancel) AS currentCount,
                            eo.cust_ro,
                            eo.client,
                            eo.brand,
                            eo.order_history_mail_list,
                            eo.camp_start_date,
                            eo.camp_end_date,
                            ru.user_id,
                            ru.user_phone,
                            ru.user_name,
                            ru.profile_image
                        FROM
                                ro_progression_mail_status rpms
                                INNER JOIN
                            ro_am_external_ro eo ON rpms.ro_id = eo.id
                                INNER JOIN
                            ro_market_price rmp ON rpms.ro_id = rmp.ro_id
                                INNER JOIN
                            ro_user ru ON eo.user_id = ru.user_id
                        WHERE
                            rpms.ro_cancel_status NOT IN ('send_mail' , 'mail_sent') AND ";

        } else {

            $query = " SELECT DISTINCT
                        eo.id AS ro_id,
                        eo.cust_ro,
                        eo.client,
                        eo.brand,
                        eo.order_history_mail_list,
                        eo.camp_start_date,
                        eo.camp_end_date,
                        ru.user_id,
                        ru.user_phone,
                        ru.user_name,
                        ru.profile_image
                    FROM
                        ro_progression_mail_status ps
                            INNER JOIN
                        ro_am_external_ro eo ON ps.ro_id = eo.id
                            INNER JOIN
                        ro_user ru ON eo.user_id = ru.user_id
                            INNER JOIN
                        ro_status rs ON eo.id = rs.am_external_ro_id
                    WHERE
                        rs.ro_status NOT IN ('cancel_requested' , 'cancel_approved', 'cancelled')
                            AND eo.order_history_mail_list != '' AND ";

        }

        $and = FALSE;
        $where = '';
        foreach ($whereData as $key => $value) {
            $where .= ($and ? ' AND ' : ' ');
            $where .= "$key = '$value'";
            $and = TRUE;
        }

        $query .= $where;

        $finalQuery = '';
        if ($type == RO_CANCEL) {
            $finalQuery = "SELECT X.ro_id,X.cust_ro,group_concat(distinct SNS.brand) as brand,X.client,X.brand as brandIds,X.order_history_mail_list,
                        X.user_phone,X.user_name,X.user_id,X.date_of_cancel,X.cancel_ro_by_admin,X.profile_image
                        from sv_new_brand SNS,
                        ($query) X
                        Where SNS.id in (X.brand) group by X.ro_id ";
        } else if ($type == MARKET_CANCEL) {
            $finalQuery = "SELECT X.ro_id,X.oldCount,X.currentCount,X.cust_ro,group_concat(distinct SNS.brand) as brand,X.client,X.brand as brandIds,
                            X.order_history_mail_list,X.camp_start_date,X.camp_end_date,X.user_phone,X.user_name,X.user_id,X.profile_image 
                        from sv_new_brand SNS,
                        ($query) X
                        Where SNS.id in (X.brand) group by X.ro_id ";
        } else {
            $finalQuery = "SELECT X.ro_id,X.cust_ro,group_concat(distinct SNS.brand) as brand,X.client,X.order_history_mail_list,X.brand as brandIds,
                        X.camp_start_date,X.camp_end_date,X.user_phone,X.user_name,X.user_id,X.profile_image 
                        from sv_new_brand SNS,
                        ($query) X
                        Where SNS.id in (X.brand) group by X.ro_id ";
        }


        $result = $this->db->query($finalQuery);

        if ($result->num_rows() > 0) {

            return $result->result("array");

        } else {

            return array();
        }
    }

    function getScheduledRoMarketData($roId)
    {

        $query = " SELECT DISTINCT rmp.market, rmp.spot_fct, rmp.banner_fct, rmp.is_cancel, ssm.id
                    FROM ro_market_price rmp
                    inner join ro_am_external_ro reo ON rmp.ro_id = reo.id
                    inner join sv_advertiser_campaign sac ON reo.internal_ro = sac.internal_ro_number
                    inner join sv_sw_market ssm ON sac.market_id = ssm.id
                    WHERE rmp.ro_id = '$roId' and rmp.market = ssm.sw_market_name  ";

        $result = $this->db->query($query);

        return $result->result("array");
    }

    function getBookedRoMarketData($roId)
    {

        $query = " SELECT rmp.market, rmp.spot_fct, rmp.banner_fct, rmp.is_cancel
                    FROM ro_market_price rmp
                    inner join ro_am_external_ro reo ON rmp.ro_id = reo.id
                    WHERE rmp.ro_id = '$roId' ";

        $result = $this->db->query($query);

        return $result->result("array");
    }

    /**
     * Method to Update the Status of Progression Email
     * @param string $where_in_ro_ids | RO IDs which are to be updated
     * @param string $type | Which type of Email
     * @return type
     */

    public function insertRoProgressionMailLog($roNumber, $type, $subject, $toList, $ccList, $message)
    {

        $date = date('Y-m-d H:i:s');

        $query = " INSERT INTO ro_progression_mail_logs
                           ( ro_number, type , subject, to_list, cc_list, message, mail_send_date )
                    VALUES ( '$roNumber', '$type', '$subject', '$toList', '$ccList', '$message', '$date' ) ";

        $this->db->query($query);

        return 1;
    }

    public function updateProgressionEmaiLStatus($setData, $roId)
    {

//        $updateQuery = " UPDATE ro_progression_mail_status $setData where ro_id = '$roId' ";
//        echo '--->' . $updateQuery;
//        $this->db->query($updateQuery);

        $this->db->where('ro_id', $roId);
        $this->db->update('ro_progression_mail_status', $setData);

        return 1;
    }

    public function remaining_notice_ro()
    {
        $given_date = date("Y-m-d");
        $query = "select distinct internal_ro_number from ro_network_ro_report_details "
            . "     where internal_ro_number NOT IN "
            . " (select internal_ro_number from ro_mail_notice_intimation ) "
            . " and start_date >= '$given_date' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getNetworkNoticedChannelForRo($internalRoNumber)
    {
        $scheduledMarket = $this->getMarketIdForInternalRo($internalRoNumber);
        if (count($scheduledMarket) > 0) {
            $marketIds = array();
            foreach ($scheduledMarket as $sm) {
                array_push($marketIds, $sm['market_id']);
            }
            $marketId = implode(",", $marketIds);
            $channelCustomerDetail = $this->getCustomerChannelForMarket($marketId);
            if (count($channelCustomerDetail) > 0) {
                return $this->getNoticedChannel($channelCustomerDetail);
            } else {
                return 'Not_Noticed';
            }
        } else {
            return 'Not_Noticed';
        }
    }

    public function getMarketIdForInternalRo($internalRo)
    {
        $query = "select distinct market_id from sv_advertiser_campaign where campaign_status='scheduled' and internal_ro_number='$internalRo' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getCustomerChannelForMarket($marketIds)
    {
        $query = "select  distinct stc.tv_channel_id as channel_id,stc.channel_name,stc.is_notice,stc.is_notice_ro,sc.customer_id,sc.customer_name,sc.customer_email "
            . " from sv_tv_channel stc"
            . " inner join sv_customer sc on sc.customer_id = stc.enterprise_id "
            . " inner join sv_customer_sw_location scsl on sc.customer_id=scsl.customer_id"
            . " where scsl.sw_market_id in ($marketIds) order by sc.customer_id";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
    }

    public function insertNoticeMail($userData, $whereData)
    {
        $data_avail_in_db = $this->getNoticeMail($whereData);

        if (count($data_avail_in_db) > 0) {
            $this->updateNoticeMail($userData, $whereData);
        } else {
            $this->db->insert('ro_mail_notice_intimation', $userData);
        }
    }

    public function getNoticeMail($whereData)
    {
        $query = $this->db->get_where('ro_mail_notice_intimation', $whereData);
        //$sql = $this->db->last_query();echo $sql;
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function updateNoticeMail($userData, $whereData)
    {
        $this->db->where($whereData);
        $this->db->update('ro_mail_notice_intimation', $userData);
        //$sql = $this->db->last_query();echo $sql;exit;
    }

    public function getCustomerMailIdsToSend($year)
    {

        $selectQuery = " SELECT DISTINCT sc.customer_email,
                                         sc.customer_id,
                                         sc.customer_name
                        FROM ro_am_external_ro rae
                        INNER JOIN ro_approved_networks ran ON rae.internal_ro = ran.internal_ro_number
                        INNER JOIN sv_customer sc ON ran.customer_id = sc.customer_id
                        WHERE financial_year = '$year' ";

        $resultObject = $this->db->query($selectQuery);

        return $resultObject->result("array");

    }

    public function financialYearTotalReport($givenDate)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $financialYear = $year - 1;
        } else {
            $financialYear = $year;
        }

        $total_yr_ro = 0;
        $total_yr_revenue = 0;
        $total_yr_network_payout_val = 0;
        $total_yr_sw_net_contribution = 0;

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Month Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Expenses(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th><tr>";
        $monthWiseRoReport = $this->getMonthWiseRoReportSummation($financialYear);
        foreach ($monthWiseRoReport as $value) {
            $net_contribuition_percent = round(($value['net_contribuition'] / $value['revenue']) * 100);
            $monthName = $value['month_name'];
            $monthValue = date("m", strtotime($monthName));

            if ($monthValue <= 3) {
                $displayYear = $financialYear + 1;
            } else {
                $displayYear = $financialYear;
            }
            $month = ucfirst(substr($value['month_name'], 0, 3));

            $structure = $structure . "<tr>";
            $structure = $structure . "<td>" . $month . '-' . $displayYear . "</td>";
            $structure = $structure . "<td>" . $value['no_of_ro'] . "</td>";
            $structure = $structure . "<td>" . $value['revenue'] . "</td>";
            $structure = $structure . "<td>" . $value['network_payout'] . "</td>";
            $structure = $structure . "<td>" . $value['net_contribuition'] . "</td>";
            $structure = $structure . "<td>" . $net_contribuition_percent . " %</td>";
            $structure = $structure . "</tr>";

            $total_yr_ro = $total_yr_ro + $value['no_of_ro'];
            $total_yr_revenue = $total_yr_revenue + $value['revenue'];
            $total_yr_network_payout_val = $total_yr_network_payout_val + $value['network_payout'];
            $total_yr_sw_net_contribution = $total_yr_sw_net_contribution + $value['net_contribuition'];
        }

        $net_yr_contribuition_percent = round(($total_yr_sw_net_contribution / $total_yr_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_yr_ro . "</td>";
        $structure = $structure . "<td>" . $total_yr_revenue . "</td>";
        $structure = $structure . "<td>" . $total_yr_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_yr_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_yr_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";


        $structure = $structure . "</table>";
        return $structure;
    }

    public function getMonthWiseRoReportSummation($financialYear)
    {
        $query = "select month_name,sum(target) as target,sum(target_achieved) as target_achieved,sum(no_of_ro) as no_of_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,sum(net_contribuition) as net_contribuition"
            . " from ro_monthwise_mis_report "
            . " where financial_year='$financialYear' group by month_name order by id";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();

    }

    public function getNetworkRoReportBreakUpByMonth($internalRo, $startDate, $endDate)
    {
        $timePeriodRoSeconds = $this->getScheduledImpressionForGivenTimePeriodAndRo($internalRo, $startDate, $endDate);
        $totalPeriodRoSeconds = $this->getTotalScheduledSecondsForRo($internalRo);

        if ($totalPeriodRoSeconds['total_ad_seconds'] == 0) {
            $totalPeriodRoSeconds['total_ad_seconds'] = 1;
        }
        $fraction = round($timePeriodRoSeconds['total_ad_seconds'] / $totalPeriodRoSeconds['total_ad_seconds'], 2);
        return $fraction;

    }

    public function getScheduledImpressionForGivenTimePeriodAndRo($internalRo, $startDate, $endDate)
    {
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
            . " FROM sv_advertiser_campaign_screens_dates AS acsd, sv_screen AS sc, sv_advertiser_campaign as ac "
            . " WHERE  acsd.screen_id = sc.screen_id and (acsd.date >= '$startDate' and acsd.date <='$endDate' ) "
            . " and acsd.screen_region_id in(1,3) and acsd.status ='scheduled' and  ac.campaign_id=acsd.campaign_id "
            . " and ac.internal_ro_number='$internalRo' and ac.is_make_good = 0";

        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id";
        $results = $this->db->query($query);
        $fcts = $results->result("array");

        $data = array();
        $data['spot_ad_seconds'] = 0;
        $data['banner_ad_seconds'] = 0;
        $data['total_ad_seconds'] = 0;

        foreach ($fcts as $value) {
            $data['total_ad_seconds'] += $value['total_ad_seconds'];
            if ($value['screen_region_id'] == 1) {
                $data['spot_ad_seconds'] += $value['total_ad_seconds'];
            } else if ($value['screen_region_id'] == 3) {
                $data['banner_ad_seconds'] += $value['total_ad_seconds'];
            }

        }
        return $data;
    }

    public function getTotalScheduledSecondsForRo($internalRo)
    {
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd, sv_screen AS sc, sv_advertiser_campaign as ac "
            . "WHERE acsd.screen_id = sc.screen_id and acsd.screen_region_id in(1,3) and "
            . "acsd.status ='scheduled' and  ac.campaign_id=acsd.campaign_id and "
            . "ac.internal_ro_number='$internalRo' and ac.is_make_good = 0";

        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id";
        $results = $this->db->query($query);
        $fcts = $results->result("array");

        $data = array();
        $data['spot_ad_seconds'] = 0;
        $data['banner_ad_seconds'] = 0;
        $data['total_ad_seconds'] = 0;

        foreach ($fcts as $value) {
            $data['total_ad_seconds'] += $value['total_ad_seconds'];
            if ($value['screen_region_id'] == 1) {
                $data['spot_ad_seconds'] += $value['total_ad_seconds'];
            } else if ($value['screen_region_id'] == 3) {
                $data['banner_ad_seconds'] += $value['total_ad_seconds'];
            }

        }
        return $data;
    }

    public function getNetworkReportData($customer_ro_number, $start_date, $end_date, $user_type)
    {
        $query = "select distinct(re.internal_ro_number),re.customer_ro_number,re.network_ro_number,re.customer_name,"
            . "re.client_name,re.agency_name,re.market,re.start_date,re.end_date,re.activity_months,"
            . "re.gross_network_ro_amount,re.customer_share,re.net_amount_payable,re.release_date,an.billing_name"
            . " from ro_network_ro_report_details re "
            . " inner join ro_approved_networks an on re.internal_ro_number=an.internal_ro_number"
            . " inner join ro_am_external_ro aer on an.internal_ro_number=aer.internal_ro"
            . " where re.internal_ro_number=aer.internal_ro and re.customer_name=an.customer_name and "
            . " test_user_creation='$user_type' ";

        $where = "";
        if (isset($start_date) && !empty($start_date)) {
            //$where['date_format(start_date, \'%Y-%m-%d\') >='] = "date_format('{$this->input->post('from')}', '%Y-%m-%d')";
            $start_date = date('Y-m-d', strtotime($start_date));
            $where = $where . " and ((start_date <='$start_date' and end_date >='$start_date') or (start_date >='$start_date') )";
        }

        if (isset($end_date) && !empty($end_date)) {
            //$where['date_format(end_date, \'%Y-%m-%d\') <='] =  "date_format('{$this->input->post('to')}', '%Y-%m-%d')";
            $end_date = date('Y-m-d', strtotime($end_date));
            if (strtotime($end_date) < strtotime($start_date)) {
                $end_date = $start_date;
            }
            $where = $where . " and ((end_date <='$end_date') or (start_date <='$end_date' and end_date >='$end_date'))";
        }

        if (isset($customer_ro_number) && $customer_ro_number != 'all') {
            //$where['customer_ro_number'] =  "'{$this->input->post('ro_number')}'";
            //$customer_ro_number = $ro_number;
            $where = $where . " and customer_ro_number='$customer_ro_number' ";
        }


        $where = $where . " order by market,customer_name";

        $query = $query . "  $where";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getMisDataForNetwork($internal_ro_number, $customer_name)
    {
        $query = "select sum(channel_payout) as net_amount_payable,month_name from ro_mis_report
                        where internal_ro_number= '" . $internal_ro_number . "' and customer_name = '" . $customer_name . "'
                        group by month_name order by start_date";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getAllRegions()
    {
        $query = $this->db->get('ro_master_geo_regions');
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    /**
     * @param $dataArray " attribute = value "
     * @param $type " AND " OR " OR " ETC
     * @return mixed     Result Array
     */
    public function selectFromMisRegionReport($dataArray, $type, $current_month_start_date, $current_month_end_date)
    {

        $whereData = implode($type, $dataArray);

        $query = " SELECT rmgr.region_name,
                    (CASE
                     WHEN R.ro_count is null
                     THEN 0 else R.ro_count end ) as ro_count,

                     (CASE
                     WHEN R.revenue is null
                     THEN 0 else R.revenue end ) as revenue,

                    (CASE
                     WHEN R.network_payout is null
                     THEN 0 else R.network_payout end ) as network_payout,

                    (CASE
                     WHEN R.net_contribution is null
                     THEN 0 else R.net_contribution end ) as net_contribution,

                    (CASE
                     WHEN R.revenue is null
                     THEN 0 else R.revenue end ) as revenue,

                    (CASE
                     WHEN (R.net_contribution is null and R.revenue is null)
                     THEN 0 else ROUND(((R.net_contribution / R.revenue) * 100),2) end ) as net_contribution_percentage
                    FROM
                      (SELECT region_id, region_name,
                              sum(ro_count) AS ro_count,
                              sum(revenue) AS revenue,
                              sum(network_payout) AS network_payout,
                              sum(net_contribution) AS net_contribution
                       FROM ro_mis_regions_report
                       WHERE " . $whereData . "
                       GROUP BY region_name) R
                       right outer join
                       ro_master_geo_regions rmgr ON R.region_id = rmgr.id ";

        $queryObj = $this->db->query($query);
        $resultArray = $queryObj->result("array");

        $region_summary = array();
        foreach ($resultArray as $val) {

            if ($val['region_name'] != 'None') {
                $tmp = array();
                $targets = $this->get_targets_for_region($val['region_name'], $current_month_start_date, $current_month_end_date);

                $tmp['region_name'] = $val['region_name'];
                $tmp['ro_count'] = $val['ro_count'];
                $tmp['revenue'] = $val['revenue'];
                $tmp['network_payout'] = $val['network_payout'];
                $tmp['net_contribution'] = $val['net_contribution'];
                $tmp['net_contribution_percentage'] = $val['net_contribution_percentage'];

                $tmp['target'] = $targets[0]['target'];
                $tmp['achieved'] = round(($val['revenue'] / $targets[0]['target']) * 100);
                array_push($region_summary, $tmp);
            }

        }

        return $region_summary;

    }

    function get_targets_for_region($region, $current_month_start_date, $current_month_end_date)
    {
        $query = "SELECT * from ro_monthly_targets WHERE region = '$region' and (date BETWEEN '$current_month_start_date' AND '$current_month_end_date') ";
        $queryObj = $this->db->query($query);

        return $queryObj->result("array");
    }

    function get_targets_for_month($region_id, $start_date, $end_date)
    {
        $query = "SELECT * from ro_monthly_targets WHERE region_id = $region_id and (date BETWEEN '$start_date' AND '$end_date') ";
        $queryObj = $this->db->query($query);

        return $queryObj->result("array");
    }

    public function getScheduledRegionForGivenMonth($givenDate)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));

        if ($month <= 3) {
            $from_year = $year - 1;
        } else {
            $from_year = $year;
        }
    }

    public function generateFCTMisReport($givenDate, $userType)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("$to_year-03-31");

        log_message('INFO', 'In mg_model@generateFCTMisReport | Current financial year - (' . $from_year . ' - ' . $to_year . ')');
        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {

            $start_date = date("Y-m-d", $i);

            if (date("m", $i) == date("m")) {
                $end_date = date("Y-m-t");
            } else {
                $end_date = date("Y-m-t", strtotime("+1 month -1 days", $i));
            }
            log_message('INFO', 'In mg_model@generateFCTMisReport | Foreach month start and end date - (' . $start_date . ' - ' . $end_date . ')');
            //This code is for not changing the previous month data once it has crossed the month

            $todaysDate = date("Y-m-d");

            $yd = date('Y', strtotime($todaysDate)) - date('Y', strtotime($start_date));   //year difference between two years
            $md = date('m', strtotime($todaysDate)) - date('m', strtotime($start_date)); //month difference between two months
            //actual month difference between two "DATES"
            if ($yd == 0) {
                $amd = $md;
            } else {
                $amd = $yd * 12 + $md;
            }

            $todaysDay = date('d', strtotime($todaysDate));
	    // This condition checks whether its a first date of current month . If its first and in the main loop you have reach just the previous month , then it will calculate entire previous month till end date of current month otherwise keep the back up data.  
            if (($todaysDay == 1) && ($amd == 1)) {
                $start_date = date('Y-m-01', strtotime('-1 month', $i));
                $end_date = date("Y-m-t", strtotime("+1 month -1 days", $i));
            }elseif (strtotime($todaysDate) > strtotime($end_date)) {
                //Update last date of Mis report to current date
                log_message('INFO', 'In mg_model@generateFCTMisReport | Maintain backup of all previous months');
                $this->keepingBackupOfLastMonth($end_date, $givenDate, $from_year);
                continue;
            }

            // echo $start_date."---".$end_date."<br/>";
            // if($userType != 2) {
            $am_query = "select am.id,am.client,am.internal_ro,am.cust_ro,reg.region_name,reg.is_international "
                . " from ro_am_external_ro am "
                . " Inner join ro_external_ro_report_details nr on am.internal_ro=nr.internal_ro_number"
                . " Inner join ro_master_geo_regions reg on reg.id = am.region_id "
                . " where test_user_creation= $userType";

            $am_query = $am_query . " and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
                                                    or
                    ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
                                                    or
                    ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
                                                    or
                    ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' ))";
            $am_query = $am_query . " order by reg.region_name,am.client";

            $am_query_res = $this->db->query($am_query);
            $data = array();
            if ($am_query_res->num_rows() != 0) {
                //$selected_ro_id = array(4218);
                $am_res = $am_query_res->result("array");
                log_message('INFO', 'In mg_model@generateFCTMisReport | List of internal ros to be processed - ' . print_r($am_res, true));
                foreach ($am_res as $am_data) {
                    //if(!in_array($am_data['id'],$selected_ro_id)) continue;
                    log_message('INFO', 'In mg_model@generateFCTMisReport | for each internal ro - ' . print_r($am_data, true));
                    $client_name = $am_data['client'];
                    $am_ext_id = $am_data['id'];
                    $internal_ro = $am_data['internal_ro'];
                    $region = $am_data['region_name'];
                    $proportional_calculation = $this->time_period_proportional_calculation($am_ext_id, $internal_ro, $start_date, $end_date, $userType, $from_year);
                    log_message('INFO', 'In mg_model@generateFCTMisReport | $proportional_calculation - ' . print_r($proportional_calculation, true));

                    $tmp = array();
                    $tmp['user_type'] = $userType;
                    $tmp['client'] = $client_name;
                    $tmp['internal_ro'] = $internal_ro;
                    $tmp['ro_id'] = $am_ext_id;
                    $tmp['region'] = $region;
                    $tmp['is_international'] = $am_data['is_international'];
                    $tmp['revenue'] = round($proportional_calculation['revenue'], 2);
                    $tmp['network_payout'] = round($proportional_calculation['total_nw_payout'], 2);
                    $tmp['sw_net_contribute'] = round($proportional_calculation['total_sw_net_contribution'], 2);
                    $net_contribuition_percent = 0.0;
                    if (isset($proportional_calculation['revenue']) && !empty($proportional_calculation['revenue']) && $proportional_calculation['revenue'] != 0) {
                        $net_contribuition_percent = round(($proportional_calculation['total_sw_net_contribution'] / $proportional_calculation['revenue']) * 100, 2);
                    }
                    $tmp['net_contribuition_percent'] = $net_contribuition_percent;

                    log_message('INFO', 'In mg_model@generateFCTMisReport | $tmp - ' . print_r($tmp, true));
                    array_push($data, $tmp);

                    //if(date("m",$i) == date("m")) {
                    //For Testing Purpose
                   /* $whereData = array(
                        'ro_id' => $am_ext_id,
                        'internal_ro_number' => $internal_ro,
                        'finanacial_year' => $from_year,
                        'month_name' => date("F", strtotime($start_date)),
                        'is_fct' => 1,
                        'user_type' => $userType);*/
                    /*$getMisRevenueData = $this->getMisRevenueDataForTesting($whereData);
                    log_message('INFO', 'In mg_model@generateFCTMisReport | getMisRevenueDataForTesting revenue data- ' . print_r($getMisRevenueData, true));*/
			
                /*    if (count($getMisRevenueData) > 0) {
                        $updateDataArray = array(
                            'revenue' => round($proportional_calculation['revenue'], 2),
                            'network_payout' => round($proportional_calculation['total_nw_payout'], 2),
                            'net_contribuition' => round($proportional_calculation['total_sw_net_contribution'], 2),
                            'net_contribuition_percent' => $net_contribuition_percent
                        );
                        log_message('INFO', 'In mg_model@generateFCTMisReport | updateMisRevenueDataForTesting - ' . print_r($updateDataArray, true));
                        $this->updateMisRevenueDataForTesting($updateDataArray, $whereData);
                    } else {
                        $insertDataArray = array(
                            'ro_id' => $am_ext_id,
                            'internal_ro_number' => $internal_ro,
                            'client_name' => $client_name,
                            'finanacial_year' => $from_year,
                            'month_name' => date("F", strtotime($start_date)),
                            'revenue' => round($proportional_calculation['revenue'], 2),
                            'network_payout' => round($proportional_calculation['total_nw_payout'], 2),
                            'net_contribuition' => round($proportional_calculation['total_sw_net_contribution'], 2),
                            'net_contribuition_percent' => $net_contribuition_percent,
                            'is_fct' => 1,
                            'user_type' => $userType
                        );
                        log_message('INFO', 'In mg_model@generateFCTMisReport | insertMisRevenueDataForMisTesting - ' . print_r($insertDataArray, true));
                        $this->insertMisRevenueDataForMisTesting($insertDataArray);
                    }*/
                    //End Of If}

                }
                //echo $data;
                //for converting into client wise
                $data = $this->convert_data_into_structure($data);
                log_message('INFO', 'In mg_model@generateFCTMisReport | convert_data_into_structure - ' . print_r($data, true));
                $is_fct = 1;
                $month_name = date("F", $i);
                $this->insertIntoMisReport($data, $month_name, $from_year, $is_fct, $userType, $givenDate);
            }//End Of IF Query
            $month_name = date("F", $i);
            $is_fct = 1;
            $this->generateFCTMisForNonExistingRegion($month_name, $from_year, $is_fct, $userType, $givenDate);
        }//End Of For Loop

    }

    private function keepingBackupOfLastMonth($end_date, $givenDate, $financialYear)
    {
        $monthName = date("F", strtotime($end_date));
        $whereData = array('created_date' => $end_date, 'is_fct' => 1, 'month_name' => $monthName, 'financial_year' => $financialYear);

        $result = $this->db->get_where('ro_mis_report_datewise', $whereData);//echo $this->db->last_query();exit;
        if ($result->num_rows() > 0) {
            $resultValues = $result->result("array");

            foreach ($resultValues as $val) {
                $userDataForInsertion = array(
                    'financial_year' => $val['financial_year'],
                    'month_name' => $val['month_name'],
                    'created_date' => $givenDate,
                    'region' => $val['region'],
                    'ro_id' => $val['ro_id'],
                    'internal_ro_number' => $val['internal_ro_number'],
                    'client' => $val['client'],
                    'total_number_ro' => $val['total_number_ro'],
                    'revenue' => $val['revenue'],
                    'network_payout' => $val['network_payout'],
                    'net_contribuition' => $val['net_contribuition'],
                    'net_contribuition_percent' => $val['net_contribuition_percent'],
                    'is_fct' => $val['is_fct'],
                    'user_type' => $val['user_type'],
                    'is_international' => $val['is_international']
                );

                $this->insertIntoMisMonthlyReport($userDataForInsertion);
            }
        }
    }

    public function insertIntoMisMonthlyReport($userData)
    {
        $this->db->insert('ro_mis_report_datewise', $userData);
    }

    public function getMisRevenueDataForTesting($data)
    {
        $result = $this->db->get_where('ro_mis_report_revenue', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateMisRevenueDataForTesting($data, $where)
    {
        $this->db->update('ro_mis_report_revenue', $data, $where);
    }

    public function insertMisRevenueDataForMisTesting($userData)
    {
        $this->db->insert('ro_mis_report_revenue', $userData);
    }

    public function insertIntoMisReport($data, $month, $financialYear, $isFct, $userType, $givenDate)
    {
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribuition = 0;

        foreach ($data as $client => $regionArray) {
            foreach ($regionArray as $region => $value) {
                foreach ($value as $key => $val) {
                    $userDataForInsertion = array(
                        'financial_year' => $financialYear,
                        'month_name' => $month,
                        'created_date' => $givenDate,
                        'region' => $val['region'],
                        'ro_id' => $val['ro_id'],
                        'internal_ro_number' => $val['internal_ro'],
                        'client' => $val['client'],
                        'total_number_ro' => $val['total_internal_ro'],
                        'revenue' => $val['revenue'],
                        'network_payout' => $val['network_payout'],
                        'net_contribuition' => $val['sw_net_contribute'],
                        'net_contribuition_percent' => $val['net_contribuition_percent'],
                        'is_fct' => $isFct,
                        'user_type' => $userType,
                        'is_international' => $val['is_international']
                    );
                    //Check For (financial_year,month,date,region,client) inserted then update
                    //else
                    $whereData = array(
                        'financial_year' => $financialYear,
                        'month_name' => $month,
                        'created_date' => $givenDate,
                        'region' => $val['region'],
                        'ro_id' => $val['ro_id'],
                        'client' => $val['client'],
                        'is_fct' => $isFct,
                        'user_type' => $userType,
                        'is_international' => $val['is_international']
                    );

                    $userDataToUpdate = array(
                        'total_number_ro' => $val['total_internal_ro'],
                        'revenue' => $val['revenue'],
                        'network_payout' => $val['network_payout'],
                        'net_contribuition' => $val['sw_net_contribute'],
                        'net_contribuition_percent' => $val['net_contribuition_percent']
                    );
                    $misMonthlyData = $this->getMisMonthlyData($whereData);
                    log_message('info', 'in mg_model@insertIntoMisReport | getMisMonthlyData - ' . print_r($misMonthlyData, true));
                    if (count($misMonthlyData) > 0) {
                        log_message('info', 'in mg_model@insertIntoMisReport | updateMisMonthlyData - ' . print_r($userDataToUpdate, true));
                        $this->updateMisMonthlyData($userDataToUpdate, $whereData);
                    } else {
                        log_message('info', 'in mg_model@insertIntoMisReport | insertIntoMisMonthlyReport - ' . print_r($userDataForInsertion, true));
                        $this->insertIntoMisMonthlyReport($userDataForInsertion);
                    }


                    $total_mnth_ro = $total_mnth_ro + $val['total_internal_ro'];
                    $total_mnth_revenue = $total_mnth_revenue + $val['revenue'];
                    $total_mnth_network_payout_val = $total_mnth_network_payout_val + $val['network_payout'];
                    $total_mnth_sw_net_contribuition = $total_mnth_sw_net_contribuition + $val['sw_net_contribute'];
                }
            }
        }

        //Insert Whole Value for financial year
        $net_mnth_contribuition_percent = 0.0;
        if (isset($total_mnth_revenue) && $total_mnth_revenue != 0) {
            $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribuition / $total_mnth_revenue) * 100, 2);
        }
        $financialYearData = array(
            'financial_year' => $financialYear,
            'month_name' => $month,
            'created_date' => $givenDate,
            'total_number_ro' => $total_mnth_ro,
            'revenue' => $total_mnth_revenue,
            'network_payout' => $total_mnth_network_payout_val,
            'net_contribuition' => $total_mnth_sw_net_contribuition,
            'net_contribuition_percent' => $net_mnth_contribuition_percent,
            'is_fct' => $isFct,
            'user_type' => $userType
        );
        $whereFinancialData = array(
            'financial_year' => $financialYear,
            'month_name' => $month,
            'is_fct' => $isFct,
            'user_type' => $userType
        );

        $userFinancialDataToUpdate = array(
            'created_date' => $givenDate,
            'total_number_ro' => $total_mnth_ro,
            'revenue' => $total_mnth_revenue,
            'network_payout' => $total_mnth_network_payout_val,
            'net_contribuition' => $total_mnth_sw_net_contribuition,
            'net_contribuition_percent' => $net_mnth_contribuition_percent
        );
        $misFinancialData = $this->getMisFinancialData($whereFinancialData);
        log_message('info', 'in mg_model@insertIntoMisReport | getMisFinancialData - ' . print_r($misFinancialData, true));
        if (count($misFinancialData) > 0) {
            log_message('info', 'in mg_model@insertIntoMisReport | updateMisFinancialData - ' . print_r($userFinancialDataToUpdate, true));
            $this->updateMisFinancialData($userFinancialDataToUpdate, $whereFinancialData);
        } else {
            log_message('info', 'in mg_model@insertIntoMisFinancialReport | insertIntoMisMonthlyReport - ' . print_r($financialYearData, true));
            $this->insertIntoMisFinancialReport($financialYearData);
        }

    }

    public function getMisMonthlyData($data)
    {
        $result = $this->db->get_where('ro_mis_report_datewise', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateMisMonthlyData($data, $where)
    {
        $this->db->update('ro_mis_report_datewise', $data, $where);
    }

    public function getMisFinancialData($data)
    {
        $result = $this->db->get_where('ro_mis_report_yearly', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateMisFinancialData($data, $where)
    {
        $this->db->update('ro_mis_report_yearly', $data, $where);
    }

    public function insertIntoMisFinancialReport($userData)
    {
        $this->db->insert('ro_mis_report_yearly', $userData);
    }

    public function generateFCTMisForNonExistingRegion($month_name, $from_year, $is_fct, $userType, $givenDate)
    {
        $regions = $this->getAllRoRegions();
        foreach ($regions as $region) {
            $region_name = $region['region_name'];
            if ($region_name == 'none') {
                continue;
            } else {
                $userDataForInsertion = array(
                    'financial_year' => $from_year,
                    'month_name' => $month_name,
                    'created_date' => $givenDate,
                    'region' => $region_name,
                    'is_international' => $region['is_international'],
                    'ro_id' => 0,
                    'internal_ro_number' => '0',
                    'client' => '0',
                    'total_number_ro' => 0,
                    'revenue' => '0',
                    'network_payout' => '0',
                    'net_contribuition' => '0',
                    'net_contribuition_percent' => '0',
                    'is_fct' => $is_fct,
                    'user_type' => $userType
                );
                //Check For (financial_year,month,date,region,client) inserted then update
                //else
                $whereData = array(
                    'financial_year' => $from_year,
                    'month_name' => $month_name,
                    'created_date' => $givenDate,
                    'region' => $region_name,
                    'is_fct' => $is_fct,
                    'user_type' => $userType,
                    'is_international' => $region['is_international']
                );


                $misMonthlyData = $this->getMisMonthlyData($whereData);
                log_message('info', 'in mg_model@insertIntoMisFinancialReport | getMisMonthlyData - ' . print_r($misMonthlyData, true));
                if (count($misMonthlyData) == 0) {
                    log_message('info', 'in mg_model@insertIntoMisFinancialReport | insertIntoMisMonthlyReport - ' . print_r($userDataForInsertion, true));
                    $this->insertIntoMisMonthlyReport($userDataForInsertion);
                }
            }

        }

    }//End Of Method

    public function getAllRoRegions($userData = NULL)
    {
        if ($userData == NULL) {
            $result = $this->db->get('ro_master_geo_regions');
        } else {
            $result = $this->db->get_where('ro_master_geo_regions', $userData);
        }
//	echo $this->db->last_query();
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getMonthlyMisReportDataClientWise($regionName, $givenDate, $is_fct, $userType)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $from_year = $year - 1;
        } else {
            $from_year = $year;
        }
        $month_name = date("F", strtotime($givenDate));

        if ($userType != 2) {
            $query = "select region,client,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,"
                . "sum(net_contribuition) as net_contribuition "
                . " from ro_mis_report_datewise rmr ";
            $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id != '0' and rmr.client !='0' ";
        } else {
            $query = "select region,client,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,"
                . "sum(net_contribuition) as net_contribuition "
                . " from ro_mis_report_datewise rmr ";
            $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                . " rmr.is_fct='$is_fct' and rmr.ro_id !='0' and rmr.client !='0' and rmr.user_type !=1 and rmr.ro_id not in (select ro_id from ro_linked_advance_ro)";
        }

        if ($regionName != 'ALL') {
            $query = $query . " and region = '$regionName' ";
        }
        $query = $query . " group by client order by client";


        $result = $this->db->query($query);

        $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
            . "<th><b>Client Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue</b></th>";
        if (!isset($is_fct) || empty($is_fct)) {
            $structure .= "<th><b>Payout</b></th>";
        } else {
            $structure .= "<th><b>N/W Payout</b></th>";
        }
        $structure .= "<th><b>Net Cont</b></th>"
            . "<th><b>Net Cont %</b></th>";


        $structure .= "</tr>";

        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;
        $net_mnth_contribuition_percent = 0;


        if ($result->num_rows() != 0) {
            $resultData = $result->result("array");
            foreach ($resultData as $value) {
                $revenue = round($value['revenue'], 2);
                $networkPayout = round($value['network_payout'], 2);
                $netContribuition = round($value['net_contribuition'], 2);
                $netContribuitionPercent = round(($netContribuition / $revenue) * 100, 2);

                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $value['client'] . "</td>";
                $structure = $structure . "<td>" . $value['total_number_ro'] . "</td>";
                $structure = $structure . "<td>" . floor($revenue) . "</td>";
                $structure = $structure . "<td>" . floor($networkPayout) . "</td>";
                $structure = $structure . "<td>" . floor($netContribuition) . "</td>";
                $structure = $structure . "<td>" . $netContribuitionPercent . " %</td>";

                $structure = $structure . "</tr>";

                $total_mnth_ro = $total_mnth_ro + $value['total_number_ro'];
                $total_mnth_revenue = $total_mnth_revenue + $revenue;
                $total_mnth_network_payout_val = $total_mnth_network_payout_val + $networkPayout;
                $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $netContribuition;
            }
            $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100, 2);

            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
            $structure = $structure . "<td>" . floor($total_mnth_revenue) . "</td>";
            $structure = $structure . "<td>" . floor($total_mnth_network_payout_val) . "</td>";
            $structure = $structure . "<td>" . floor($total_mnth_sw_net_contribution) . "</td>";
            $structure = $structure . "<td>" . round($net_mnth_contribuition_percent, 2) . "</td>";

            $structure = $structure . "</tr>";

            $structure = $structure . "</table>";
            return $structure;
        } else {
            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td>" . $total_mnth_ro . "</td>";
            $structure = $structure . "<td>" . floor($total_mnth_revenue) . "</td>";
            $structure = $structure . "<td>" . floor($total_mnth_network_payout_val) . "</td>";
            $structure = $structure . "<td>" . floor($total_mnth_sw_net_contribution) . "</td>";
            $structure = $structure . "<td>" . $net_mnth_contribuition_percent . "</td>";
            $structure = $structure . "</tr>";

            $structure = $structure . "</table>";
            return $structure;

        }

    }

    public function getMonthlyMisReportData($regionName, $givenDate, $is_fct, $userType, $is_international)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $from_year = $year - 1;
        } else {
            $from_year = $year;
        }
        $month_name = date("F", strtotime($givenDate));

        //$start_date = date("$from_year-$month-01")  ;
        //$end_date = date("$from_year-$month-t",strtotime($givenDate))  ;

        if ($is_international) {
            if ($is_fct == 0) {
                $query = "select region,client,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,"
                    . "sum(net_contribuition) as net_contribuition "
                    . " from ro_mis_report_datewise rmr ";
                $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                    . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id !='0' and rmr.client !='0' ";
                if ($regionName != 'ALL') {
                    $query = $query . " and region = '$regionName' ";
                }
                $query = $query . " group by client order by region,client";
            } else {
                $query = "SELECT region,client,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,"
                    . "sum(net_contribuition) as net_contribuition,sum(scheduleSeconds) as scheduleSeconds,sum(playedSeconds) as  playedSeconds,sum(makeGoodPlayedSeconds) as makeGoodPlayedSeconds,"
                    . "sum(bannerScheduleSeconds) as bannerScheduleSeconds,sum(bannerPlayedSeconds) as  bannerPlayedSeconds,sum(makeGoodBannerPlayedSeconds) as makeGoodBannerPlayedSeconds "
                    . "FROM "
                    . "(select region,client,total_number_ro,revenue,network_payout,net_contribuition,"
                    . "sum(spotScheduleSec) as scheduleSeconds,sum(spotPlayedSec)  as playedSeconds,sum(makeGood_spotPlayedSec) as makeGoodPlayedSeconds,"
                    . "sum(bannerScheduleSec) as bannerScheduleSeconds,sum(bannerPlayedSec)  as bannerPlayedSeconds,sum(makeGood_bannerPlayedSec) as makeGoodBannerPlayedSeconds "
                    . "from ro_mis_report_datewise rmr "
                    . "left join sv_mis_online_channel_fct_aggregate smcf on rmr.ro_id = smcf.ro_id";
                $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                    . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id !='0' and rmr.client !='0' ";
                if ($regionName != 'ALL') {
                    $query = $query . " and region = '$regionName' ";
                }
                $query = $query . " group by rmr.ro_id) X GROUP BY X.client order by X.region,X.client";
            }
        } else {
            if ($is_fct == 0) {
                $query = "select region,client,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,"
                    . "sum(net_contribuition) as net_contribuition "
                    . " from ro_mis_report_datewise rmr ";
                $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                    . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id !='0' and rmr.client !='0' and is_international='$is_international' ";
                if ($regionName != 'ALL') {
                    $query = $query . " and region = '$regionName' ";
                }
                $query = $query . " group by region,client order by region,client";
            } else {
                $query = "SELECT region,client,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,"
                    . "sum(net_contribuition) as net_contribuition,sum(scheduleSeconds) as scheduleSeconds,sum(playedSeconds) as  playedSeconds,sum(makeGoodPlayedSeconds) as makeGoodPlayedSeconds,"
                    . "sum(bannerScheduleSeconds) as bannerScheduleSeconds,sum(bannerPlayedSeconds) as  bannerPlayedSeconds,sum(makeGoodBannerPlayedSeconds) as makeGoodBannerPlayedSeconds "
                    . "FROM "
                    . "(select region,client,total_number_ro,revenue,network_payout,net_contribuition,"
                    . "sum(spotScheduleSec) as scheduleSeconds,sum(spotPlayedSec)  as playedSeconds,sum(makeGood_spotPlayedSec) as makeGoodPlayedSeconds,"
                    . "sum(bannerScheduleSec) as bannerScheduleSeconds,sum(bannerPlayedSec)  as bannerPlayedSeconds,sum(makeGood_bannerPlayedSec) as makeGoodBannerPlayedSeconds "
                    . "from ro_mis_report_datewise rmr "
                    . "left join sv_mis_online_channel_fct_aggregate smcf on rmr.ro_id = smcf.ro_id";
                $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                    . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id !='0' and rmr.client !='0' and is_international='$is_international' ";
                if ($regionName != 'ALL') {
                    $query = $query . " and region = '$regionName' ";
                }
                $query = $query . " group by rmr.ro_id) X GROUP BY X.region,X.client order by X.region,X.client";
            }
        }


        $result = $this->db->query($query);

        $structure = "<table border='1' style='text-align:center;width:100%' ><tr>"
            . "<th><b>Region</b></th>"
            . "<th><b>Client Name</b></th>"
            . "<th><b># Ro's</b></th>"
            . "<th><b>Revenue</b></th>";
        if (!isset($is_fct) || empty($is_fct)) {
            $structure .= "<th><b>Payout</b></th>";
        } else {
            $structure .= "<th><b>N/W Payout</b></th>";
        }
        $structure .= "<th><b>Net Cont</b></th>"
            . "<th><b>Net Cont %</b></th>";

        if ($is_fct == 1) {
            $structure .= "<th><b>Perf %</b></th>";
        }

        $structure .= "</tr>";
        $total_mnth_ro = 0;
        $total_mnth_revenue = 0;
        $total_mnth_network_payout_val = 0;
        $total_mnth_sw_net_contribution = 0;
        $net_mnth_contribuition_percent = 0;
        $total_scheduled_performance = 0;
        $total_played_performance = 0;
        $total_banner_played_performance = 0;
        $total_makeGood_played_performance = 0;
        $total_performance_percentage = 0;

        if ($result->num_rows() != 0) {
            $resultData = $result->result("array");
            foreach ($resultData as $value) {
                $revenue = round($value['revenue'], 2);
                $networkPayout = round($value['network_payout'], 2);
                $netContribuition = round($value['net_contribuition'], 2);
                $netContribuitionPercent = 0.0;
                if (isset($revenue) && !empty($revenue) && $revenue != 0) {
                    $netContribuitionPercent = round(($netContribuition / $revenue) * 100, 2);
                }
                $netContribuitionPercent = sprintf("%.2F", $netContribuitionPercent);

                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . ucfirst($value['region']) . "</td>";
                $structure = $structure . "<td style='text-align:left'>" . $value['client'] . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . $value['total_number_ro'] . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($revenue)) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($networkPayout)) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($netContribuition)) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . $netContribuitionPercent . " </td>";

                if ($is_fct == 1) {
                    $spotScheduledSeconds = $value['scheduleSeconds'];
                    $spotPlayedSeconds = $value['playedSeconds'];
                    $spotMakeGoodPlayedSeconds = $value['makeGoodPlayedSeconds'];

                    if ($spotPlayedSeconds > $spotScheduledSeconds) {
                        $spotPlayedSeconds = $spotScheduledSeconds;
                    }

                    $bannerScheduledSeconds = $value['bannerScheduleSeconds'];
                    $bannerPlayedSeconds = $value['bannerPlayedSeconds'];
                    $bannerMakeGoodPlayedSeconds = $value['makeGoodBannerPlayedSeconds'];

                    $spotPlayed = $spotPlayedSeconds + $spotMakeGoodPlayedSeconds;
                    $bannerPlayed = $bannerPlayedSeconds + $bannerMakeGoodPlayedSeconds;

                    if ($bannerPlayed > $bannerScheduledSeconds) {
                        $bannerPlayed = $bannerScheduledSeconds;
                    }
                    $scheduledSeconds = $spotScheduledSeconds + $bannerScheduledSeconds;
                    $performance_percentage = 0.0;
                    if (isset($scheduledSeconds) && !empty($scheduledSeconds) && $scheduledSeconds != 0) {
                        $performance_percentage = round((($spotPlayed + $bannerPlayed) / ($scheduledSeconds)) * 100, 2);
                    }

                    $total_scheduled_performance = $total_scheduled_performance + $spotScheduledSeconds + $bannerScheduledSeconds;
                    $total_played_performance = $total_played_performance + $spotPlayedSeconds;
                    $total_makeGood_played_performance = $total_makeGood_played_performance + $spotMakeGoodPlayedSeconds;
                    $total_banner_played_performance = $total_banner_played_performance + $bannerPlayed;

                    $performance_percentage = sprintf("%.2F", $performance_percentage);
                    $structure = $structure . "<td style='text-align:right'>" . $performance_percentage . "</td>";
                }
                $structure = $structure . "</tr>";

                $total_mnth_ro = $total_mnth_ro + $value['total_number_ro'];
                $total_mnth_revenue = $total_mnth_revenue + $revenue;
                $total_mnth_network_payout_val = $total_mnth_network_payout_val + $networkPayout;
                $total_mnth_sw_net_contribution = $total_mnth_sw_net_contribution + $netContribuition;
            }
            $net_mnth_contribuition_percent = 0.0;
            if (isset($total_mnth_revenue) && !empty($total_mnth_revenue) && $total_mnth_revenue != 0) {
                $net_mnth_contribuition_percent = round(($total_mnth_sw_net_contribution / $total_mnth_revenue) * 100, 2);
            }
            $net_mnth_contribuition_percent = sprintf("%.2F", $net_mnth_contribuition_percent);

            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td>&nbsp;</td>";
            $structure = $structure . "<td style='text-align:right'>" . $total_mnth_ro . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_mnth_revenue)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_mnth_network_payout_val)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_mnth_sw_net_contribution)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . $net_mnth_contribuition_percent . "</td>";
            if ($is_fct == 1) {
                $total_performance_percentage = 0.0;
                if (isset($total_scheduled_performance) && !empty($total_scheduled_performance) && $total_scheduled_performance != 0) {
                    $total_performance_percentage = round((($total_played_performance + $total_makeGood_played_performance + $total_banner_played_performance) / $total_scheduled_performance) * 100, 2);
                }
                $total_performance_percentage = sprintf("%.2F", $total_performance_percentage);
                $structure = $structure . "<td style='text-align:right'>" . $total_performance_percentage . "</td>";
            }
            $structure = $structure . "</tr>";

            $structure = $structure . "</table>";
            return $structure;
        } else {
            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td>&nbsp;</td>";
            $structure = $structure . "<td style='text-align:right'>" . $total_mnth_ro . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_mnth_revenue)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_mnth_network_payout_val)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_mnth_sw_net_contribution)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . $net_mnth_contribuition_percent . "</td>";

            if ($is_fct == 1) {
                $structure = $structure . "<td style='text-align:right'>" . $total_performance_percentage . "</td>";
            }
            $structure = $structure . "</tr>";

            $structure = $structure . "</table>";
            return $structure;
        }
    }

    public function getSummaryForRegion($region_name, $givenDate, $userType, $is_international)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $month_name = date("F", strtotime($givenDate));
        $financial_year = $from_year . "-" . $to_year;

        $structure = "<table border='1' style='text-align:center;width:100%'><tr>"
            . "<th><b>Region</b></th>"
            . "<th><b># Ro's</b></th>"
            . "<th><b>Target</b></th>"
            . "<th><b>Revenue</b></th>"
            . "<th><b>% Achieved</b></th>"
            . "<th><b>Payout</b></th>"
            . "<th><b>Net Cont</b></th>"
            . "<th><b>Net Cont %</b></th>"
            . "<tr>";

        if ($is_international) {
            $query = "select region,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,sum(net_contribuition) as net_contribuition from ro_mis_report_datewise "
                . "where financial_year='$from_year' and month_name='$month_name' "
                . " and created_date='$givenDate' and user_type='$userType' ";
        } else {
            $query = "select region,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,sum(net_contribuition) as net_contribuition from ro_mis_report_datewise "
                . "where financial_year='$from_year' and month_name='$month_name' "
                . " and created_date='$givenDate' and user_type='$userType' and is_international='$is_international' ";
        }
        if ($region_name != 'ALL') {
            $query = $query . " and region = '$region_name' ";
        }
        $query = $query . " group by region";

        $result = $this->db->query($query);

        $total_number_ro = 0;
        $total_target = 0;
        $total_revenue = 0;
        $total_network_payout_val = 0;
        $total_sw_net_contribution = 0;

        if ($result->num_rows() != 0) {
            $resultData = $result->result("array");

            foreach ($resultData as $value) {
                $total_number_ro = $total_number_ro + $value['total_number_ro'];
                $total_revenue = $total_revenue + $value['revenue'];
                $total_network_payout_val = $total_network_payout_val + $value['network_payout'];
                $total_sw_net_contribution = $total_sw_net_contribution + $value['net_contribuition'];

                $target = $this->getTargetForFinancialYearMonth($financial_year, $month_name, $value['region']);
                if (!isset($target) || empty($target)) {
                    $target = 0;
                    $targetAchivedPercentage = 'NA';
                } else {
                    $targetAchivedPercentage = round(($value['revenue'] / $target) * 100, 2);
                }
                $total_target = $total_target + $target;
                $targetAchivedPercentage = sprintf("%.2F", $targetAchivedPercentage);

                if ($total_revenue == 0) {
                    $net_contribuition_percent = 0;
                } else {
                    $net_contribuition_percent = round(($value['net_contribuition'] / $value['revenue']) * 100, 2);
                }
                $net_contribuition_percent = sprintf("%.2F", $net_contribuition_percent);

                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . ucfirst($value['region']) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . $value['total_number_ro'] . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($target)) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($value['revenue'])) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . $targetAchivedPercentage . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($value['network_payout'])) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($value['net_contribuition'])) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . $net_contribuition_percent . "</td>";
                $structure = $structure . "</tr>";
            }

        }
        $total_net_contribuition_percent = 0.0;
        if ($total_revenue) {
            $total_net_contribuition_percent = round(($total_sw_net_contribution / $total_revenue) * 100, 2);
        }
        $total_net_contribuition_percent = sprintf("%.2F", $total_net_contribuition_percent);

        if (!isset($total_target) || empty($total_target)) {
            $total_target = 0;
            $totalTargetAchivedPercentage = 'NA';
        } else {
            $totalTargetAchivedPercentage = round(($total_revenue / $total_target) * 100, 2);
        }
        $totalTargetAchivedPercentage = sprintf("%.2F", $totalTargetAchivedPercentage);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td style='text-align:right'>" . $total_number_ro . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_target)) . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_revenue)) . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . $totalTargetAchivedPercentage . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_network_payout_val)) . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_sw_net_contribution)) . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . $total_net_contribuition_percent . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;

    }

    public function getTargetForFinancialYearMonth($financial_year, $month_name, $regionName)
    {
        $query = "select sum(target) as target from ro_region_targets rt "
            . " inner join ro_master_geo_regions mgr on rt.region_id = mgr.id "
            . " where month='$month_name' and financial_year='$financial_year' ";
        if ($regionName != 'ALL') {
            $query = $query . " and mgr.region_name = '$regionName' ";
        }
        $result = $this->db->query($query);
        $target = 0;

        if ($result->num_rows() != 0) {
            $resultValue = $result->result("array");
            $target = $resultValue[0]['target'];
        }
        return $target;
    }

    public function getFinancialYearMisReport_ForPrevios($regionName, $givenDate, $is_fct, $userType)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("$to_year-03-31");

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Month Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue</b></th>"
            . "<th><b>Payout</b></th>"
            . "<th><b>Net Cont</b></th>"
            . "<th><b>Net Cont %</b></th></tr>";


        $total_yr_ro = 0;
        $total_yr_revenue = 0;
        $total_yr_network_payout_val = 0;
        $total_yr_sw_net_contribution = 0;

        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {
            $month_name = date("F", $i);

            if ($userType != 2) {
                $query = "select * from ro_mis_report_datewise rmr";
                $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                    . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id != '0' and rmr.client !='0' ";
            } else {
                $query = "select * from ro_mis_report_datewise rmr";
                $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                    . "rmr.is_fct='$is_fct' and rmr.user_type !='1' and rmr.ro_id !='0' and rmr.client !='0' and rmr.ro_id not in (select ro_id from ro_linked_advance_ro)";
            }

            if ($regionName != 'ALL') {
                $query = $query . " and region = '$regionName' ";
            }
            $query = $query . " order by region,client";


            $result = $this->db->query($query);

            if ($result->num_rows() != 0) {
                //$no_of_ro = $result->num_rows() ;
                $no_of_ro = 0;
                $total_revenue = 0;
                $total_network_payout_val = 0;
                $total_sw_net_contribution = 0;
                //$net_contribution = 0 ;
                $resultData = $result->result("array");

                foreach ($resultData as $value) {
                    $no_of_ro = $no_of_ro + $value['total_number_ro'];
                    $total_revenue = $total_revenue + $value['revenue'];
                    $total_network_payout_val = $total_network_payout_val + $value['network_payout'];
                    $total_sw_net_contribution = $total_sw_net_contribution + $value['net_contribuition'];
                }

                if ($total_revenue == 0) {
                    $net_contribuition_percent = 0;
                } else {
                    $net_contribuition_percent = round(($total_sw_net_contribution / $total_revenue) * 100, 2);
                }


                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $month_name . "</td>";
                $structure = $structure . "<td>" . $no_of_ro . "</td>";
                $structure = $structure . "<td>" . floor($total_revenue) . "</td>";
                $structure = $structure . "<td>" . floor($total_network_payout_val) . "</td>";
                $structure = $structure . "<td>" . floor($total_sw_net_contribution) . "</td>";
                $structure = $structure . "<td>" . $net_contribuition_percent . "</td>";
                $structure = $structure . "</tr>";


                $total_yr_ro = $total_yr_ro + $no_of_ro;

                $total_yr_revenue = $total_yr_revenue + $total_revenue;
                $total_yr_network_payout_val = $total_yr_network_payout_val + $total_network_payout_val;
                $total_yr_sw_net_contribution = $total_yr_sw_net_contribution + $total_sw_net_contribution;
            }
        }
        $net_yr_contribuition_percent = round(($total_yr_sw_net_contribution / $total_yr_revenue) * 100, 2);


        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_yr_ro . "</td>";
        $structure = $structure . "<td>" . floor($total_yr_revenue) . "</td>";
        $structure = $structure . "<td>" . floor($total_yr_network_payout_val) . "</td>";
        $structure = $structure . "<td>" . floor($total_yr_sw_net_contribution) . "</td>";
        $structure = $structure . "<td>" . $net_yr_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";


        $structure = $structure . "</table>";
        return $structure;
    }

    public function getFinancialYearMisReport($regionName, $givenDate, $is_fct, $userType, $is_international)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("$to_year-03-31");
        $financial_year = $from_year . "-" . $to_year;
        log_message('info', 'in mg_model@getFinancialYearMisReport | financial year - ' . print_r($financial_year, true));
        if ($is_fct == 1) {
            $structure = "<table border='1' style='text-align:center;width:100%'><tr>"
                . "<th><b>Month Name</b></th>"
                . "<th><b># Ro's</b></th>"
                . "<th><b>Target</b></th>"
                . "<th><b>Revenue</b></th>"
                . "<th><b>% Achieved</b></th>"
                . "<th><b>N/W Payout</b></th>"
                . "<th><b>Net Cont</b></th>"
                . "<th><b>Net Cont %</b></th>";
            $structure .= "<th><b>Perf %</b></th></tr>";
            log_message('info', 'in mg_model@getFinancialYearMisReport | IS FCT = 1 STRUCTURE - ' . print_r($structure, true));
        } else {
            $structure = "<table border='1' style='text-align:center;width:100%'><tr>"
                . "<th><b>Month Name</b></th>"
                . "<th><b># Ro's</b></th>"
                . "<th><b>Revenue</b></th>"
                . "<th><b>Payout</b></th>"
                . "<th><b>Net Cont</b></th>"
                . "<th><b>Net Cont %</b></th></tr>";
            log_message('info', 'in mg_model@getFinancialYearMisReport | IS FCT = 0 STRUCTURE - ' . print_r($structure, true));
        }


        $total_yr_ro = 0;
        $total_target = 0;
        $total_yr_revenue = 0;
        $total_yr_network_payout_val = 0;
        $total_yr_sw_net_contribution = 0;

        $total_scheduled_performance = 0;
        $total_played_performance = 0;
        $total_makeGood_played_performance = 0;
        $total_makeGood_banner_played_performance = 0;
        $total_performance_percentage = 0;

        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {
            $month_name = date("F", $i);
            $month_val = date("m", $i);
            log_message('info', 'in mg_model@getFinancialYearMisReport | Foreach Month - ' . print_r($month_name, true));
            if ($is_international) {
                log_message('info', 'in mg_model@getFinancialYearMisReport | $is_international - ' . print_r($is_international, true));
                if ($is_fct == 0) {
                    log_message('info', 'in mg_model@getFinancialYearMisReport | $is_fct - ' . print_r($is_fct, true));
                    $query = "select * from ro_mis_report_datewise rmr";
                    $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                        . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id !='0' and rmr.client !='0' and is_international='$is_international' ";
                    if ($regionName != 'ALL') {
                        $query = $query . " and region = '$regionName' ";
                    }
                    $query = $query . " order by region,client";
                    log_message('info', 'in mg_model@getFinancialYearMisReport | query  - ' . print_r($query, true));
                } else {
                    log_message('info', 'in mg_model@getFinancialYearMisReport | $is_fct - ' . print_r($is_fct, true));
                    $query = "SELECT region,client,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,"
                        . "sum(net_contribuition) as net_contribuition,sum(scheduleSeconds) as scheduleSeconds,sum(playedSeconds) as  playedSeconds,sum(makeGoodPlayedSeconds) as makeGoodPlayedSeconds,"
                        . "sum(bannerScheduleSeconds) as bannerScheduleSeconds,sum(bannerPlayedSeconds) as  bannerPlayedSeconds,sum(makeGoodBannerPlayedSeconds) as makeGoodBannerPlayedSeconds "
                        . "FROM "
                        . "(select region,client,total_number_ro,revenue,network_payout,net_contribuition,"
                        . "sum(spotScheduleSec) as scheduleSeconds,sum(spotPlayedSec)  as playedSeconds,sum(makeGood_spotPlayedSec) as makeGoodPlayedSeconds,"
                        . "sum(bannerScheduleSec) as bannerScheduleSeconds,sum(bannerPlayedSec)  as bannerPlayedSeconds,sum(makeGood_bannerPlayedSec) as makeGoodBannerPlayedSeconds "
                        . "from ro_mis_report_datewise rmr "
                        . "left join sv_mis_online_channel_fct_aggregate smcf on rmr.ro_id = smcf.ro_id";
                    $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                        . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id !='0' and rmr.client !='0' and is_international='$is_international' ";
                    //AND smcf.createdDate BETWEEN '$start_date' AND '$end_date'
                    if ($regionName != 'ALL') {
                        $query = $query . " and region = '$regionName' ";
                    }
                    $query = $query . " group by rmr.ro_id) X GROUP BY X.client ";
                    log_message('info', 'in mg_model@getFinancialYearMisReport | query - ' . print_r($query, true));
                }
            } else {
                log_message('info', 'in mg_model@getFinancialYearMisReport | $is_international - ' . print_r($is_international, true));
                if ($is_fct == 0) {
                    log_message('info', 'in mg_model@getFinancialYearMisReport | $is_fct - ' . print_r($is_fct, true));
                    $query = "select * from ro_mis_report_datewise rmr";
                    $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                        . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id !='0' and rmr.client !='0' and is_international='$is_international' ";
                    if ($regionName != 'ALL') {
                        $query = $query . " and region = '$regionName' ";
                    }
                    $query = $query . " order by region,client";
                    log_message('info', 'in mg_model@getFinancialYearMisReport | query  - ' . print_r($query, true));
                } else {
                    log_message('info', 'in mg_model@getFinancialYearMisReport | $is_fct - ' . print_r($is_fct, true));
                    $query = "SELECT region,client,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,"
                        . "sum(net_contribuition) as net_contribuition,sum(scheduleSeconds) as scheduleSeconds,sum(playedSeconds) as  playedSeconds,sum(makeGoodPlayedSeconds) as makeGoodPlayedSeconds,"
                        . "sum(bannerScheduleSeconds) as bannerScheduleSeconds,sum(bannerPlayedSeconds) as  bannerPlayedSeconds,sum(makeGoodBannerPlayedSeconds) as makeGoodBannerPlayedSeconds "
                        . "FROM "
                        . "(select region,client,total_number_ro,revenue,network_payout,net_contribuition,"
                        . "sum(spotScheduleSec) as scheduleSeconds,sum(spotPlayedSec)  as playedSeconds,sum(makeGood_spotPlayedSec) as makeGoodPlayedSeconds,"
                        . "sum(bannerScheduleSec) as bannerScheduleSeconds,sum(bannerPlayedSec)  as bannerPlayedSeconds,sum(makeGood_bannerPlayedSec) as makeGoodBannerPlayedSeconds "
                        . "from ro_mis_report_datewise rmr "
                        . "left join sv_mis_online_channel_fct_aggregate smcf on rmr.ro_id = smcf.ro_id";
                    $query = $query . " where rmr.financial_year='$from_year' and rmr.month_name='$month_name' and rmr.created_date='$givenDate' and "
                        . "rmr.is_fct='$is_fct' and rmr.user_type='$userType' and rmr.ro_id !='0' and rmr.client !='0' and is_international='$is_international'";
                    //AND smcf.createdDate BETWEEN '$start_date' AND '$end_date'
                    if ($regionName != 'ALL') {
                        $query = $query . " and region = '$regionName' ";
                    }
                    $query = $query . " group by rmr.ro_id) X GROUP BY X.client ";
                    log_message('info', 'in mg_model@getFinancialYearMisReport | query - ' . print_r($query, true));
                }
            }

            $result = $this->db->query($query);
            log_message('info', 'in mg_model@getFinancialYearMisReport | query result - ' . print_r($result->result("array"), true));

            if ($result->num_rows() != 0) {
                log_message('info', 'in mg_model@getFinancialYearMisReport | query result FOUND!');
                //$no_of_ro = $result->num_rows() ;
                $no_of_ro = 0;
                $total_revenue = 0;
                $total_network_payout_val = 0;
                $total_sw_net_contribution = 0;
                $monthly_scheduled_performance = 0;
                $monthly_played_performance = 0;
                $monthly_makeGood_played_performance = 0;
                $monthly_banner_played_performance = 0;

                //$net_contribution = 0 ;
                $resultData = $result->result("array");

                foreach ($resultData as $value) {
                    log_message('info', 'in mg_model@getFinancialYearMisReport | foreach query result - ' . print_r($value, true));
                    $no_of_ro = $no_of_ro + $value['total_number_ro'];
                    $total_revenue = $total_revenue + $value['revenue'];
                    $total_network_payout_val = $total_network_payout_val + $value['network_payout'];
                    $total_sw_net_contribution = $total_sw_net_contribution + $value['net_contribuition'];

                    if ($is_fct == 1) {
                        log_message('info', 'in mg_model@getFinancialYearMisReport | is ftc =1 ');
                        $spotScheduledSeconds = $value['scheduleSeconds'];
                        $spotPlayedSeconds = $value['playedSeconds'];
                        $spotMakeGoodPlayedSeconds = $value['makeGoodPlayedSeconds'];

                        if ($spotPlayedSeconds > $spotScheduledSeconds) {
                            $spotPlayedSeconds = $spotScheduledSeconds;
                        }

                        $bannerScheduledSeconds = $value['bannerScheduleSeconds'];
                        $bannerPlayedSeconds = $value['bannerPlayedSeconds'];
                        $bannerMakeGoodPlayedSeconds = $value['makeGoodBannerPlayedSeconds'];

                        $bannerPlayed = $bannerPlayedSeconds + $bannerMakeGoodPlayedSeconds;

                        if ($bannerPlayed > $bannerScheduledSeconds) {
                            $bannerPlayed = $bannerScheduledSeconds;
                        }

                        $monthly_scheduled_performance = $monthly_scheduled_performance + $spotScheduledSeconds + $bannerScheduledSeconds;
                        $monthly_played_performance = $monthly_played_performance + $spotPlayedSeconds;
                        $monthly_makeGood_played_performance = $monthly_makeGood_played_performance + $spotMakeGoodPlayedSeconds;
                        $monthly_banner_played_performance = $monthly_banner_played_performance + $bannerPlayed;
                    }

                }

                //Get Region Target For $month_name For Finacial year
                $target = $this->getTargetForFinancialYearMonth($financial_year, $month_name, $regionName);
                log_message('info', 'in mg_model@getFinancialYearMisReport | getTargetForFinancialYearMonth - ' . print_r($target, true));
                if (!isset($target) || empty($target)) {
                    $target = 0;
                    $targetAchivedPercentage = 'NA';
                } else {
                    $targetAchivedPercentage = round(($total_revenue / $target) * 100, 2);
                }
                $targetAchivedPercentage = sprintf("%.2F", $targetAchivedPercentage);
                log_message('info', 'in mg_model@getFinancialYearMisReport | $targetAchivedPercentage - ' . print_r($targetAchivedPercentage, true));
                $total_target = $total_target + $target;
                log_message('info', 'in mg_model@getFinancialYearMisReport | $total_target - ' . print_r($total_target, true));


                if ($total_revenue == 0) {
                    $net_contribuition_percent = 0;
                } else {
                    $net_contribuition_percent = round(($total_sw_net_contribution / $total_revenue) * 100, 2);
                }
                $net_contribuition_percent = sprintf("%.2F", $net_contribuition_percent);
                log_message('info', 'in mg_model@getFinancialYearMisReport | $net_contribuition_percent - ' . print_r($net_contribuition_percent, true));

                if ($is_fct == 1) {
                    log_message('info', 'in mg_model@getFinancialYearMisReport | is fct =1 ');
                    $structure = $structure . "<tr>";
                    $structure = $structure . "<td style='text-align:left'>" . $month_name . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . $no_of_ro . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . number_format(floor($target)) . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_revenue)) . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . $targetAchivedPercentage . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_network_payout_val)) . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_sw_net_contribution)) . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . $net_contribuition_percent . "</td>";
                    $performance_percentage = 0.0;
                    if (isset($monthly_scheduled_performance) && !empty($monthly_scheduled_performance) && $monthly_scheduled_performance != 0) {
                        $performance_percentage = round((($monthly_played_performance + $monthly_makeGood_played_performance + $monthly_banner_played_performance) / $monthly_scheduled_performance) * 100, 2);
                    }
                    $total_scheduled_performance = $total_scheduled_performance + $monthly_scheduled_performance;
                    $total_played_performance = $total_played_performance + $monthly_played_performance;
                    $total_makeGood_played_performance = $total_makeGood_played_performance + $monthly_makeGood_played_performance;
                    $total_makeGood_banner_played_performance = $total_makeGood_banner_played_performance + $monthly_banner_played_performance;

                    $performance_percentage = sprintf("%.2F", $performance_percentage);
                    $structure = $structure . "<td style='text-align:right'>" . $performance_percentage . "</td>";

                    $structure = $structure . "</tr>";
                } else {
                    log_message('info', 'in mg_model@getFinancialYearMisReport | is fct =0');
                    $structure = $structure . "<tr>";
                    $structure = $structure . "<td style='text-align:left'>" . $month_name . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . $no_of_ro . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_revenue)) . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_network_payout_val)) . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_sw_net_contribution)) . "</td>";
                    $structure = $structure . "<td style='text-align:right'>" . $net_contribuition_percent . "</td>";
                    $structure = $structure . "</tr>";
                }


                $total_yr_ro = $total_yr_ro + $no_of_ro;
                log_message('info', 'in mg_model@getFinancialYearMisReport | $total_yr_ro - ' . print_r($total_yr_ro, true));

                $total_yr_revenue = $total_yr_revenue + $total_revenue;
                log_message('info', 'in mg_model@getFinancialYearMisReport | $total_yr_revenue - ' . print_r($total_yr_revenue, true));
                $total_yr_network_payout_val = $total_yr_network_payout_val + $total_network_payout_val;
                log_message('info', 'in mg_model@getFinancialYearMisReport | $total_yr_network_payout_val - ' . print_r($total_yr_network_payout_val, true));
                $total_yr_sw_net_contribution = $total_yr_sw_net_contribution + $total_sw_net_contribution;
                log_message('info', 'in mg_model@getFinancialYearMisReport | $total_yr_sw_net_contribution - ' . print_r($total_yr_sw_net_contribution, true));
            }
        }
        $net_yr_contribuition_percent = 0.0;
        if ($total_yr_revenue) {
            $net_yr_contribuition_percent = round(($total_yr_sw_net_contribution / $total_yr_revenue) * 100, 2);
        }
        log_message('info', 'in mg_model@getFinancialYearMisReport | $net_yr_contribuition_percent - ' . print_r($net_yr_contribuition_percent, true));
        $net_yr_contribuition_percent = sprintf("%.2F", $net_yr_contribuition_percent);
        log_message('info', 'in mg_model@getFinancialYearMisReport | $net_yr_contribuition_percent - ' . print_r($net_yr_contribuition_percent, true));

        if (!isset($total_target) || empty($total_target)) {
            $total_target = 0;
            $net_yr_targetAchieved_percent = 'NA';
        } else {
            $net_yr_targetAchieved_percent = round(($total_yr_revenue / $total_target) * 100, 2);
        }
        $net_yr_targetAchieved_percent = sprintf("%.2F", $net_yr_targetAchieved_percent);
        log_message('info', 'in mg_model@getFinancialYearMisReport | $net_yr_targetAchieved_percent - ' . print_r($net_yr_targetAchieved_percent, true));

        if ($is_fct == 1) {
            log_message('info', 'in mg_model@getFinancialYearMisReport | is fct =1');
            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td style='text-align:right'>" . $total_yr_ro . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_target)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_revenue)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . round($net_yr_targetAchieved_percent) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_network_payout_val)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_sw_net_contribution)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . $net_yr_contribuition_percent . "</td>";

            if (isset($total_scheduled_performance) && !empty($total_scheduled_performance) && $total_scheduled_performance != 0) {
                $total_performance_percentage = round((($total_played_performance + $total_makeGood_played_performance + $total_makeGood_banner_played_performance) / $total_scheduled_performance) * 100, 2);
            }
            $total_performance_percentage = sprintf("%.2F", $total_performance_percentage);
            $structure = $structure . "<td style='text-align:right'>" . $total_performance_percentage . "</td>";

            $structure = $structure . "</tr>";
        } else {
            log_message('info', 'in mg_model@getFinancialYearMisReport | is fct =0');
            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td style='text-align:right'>" . $total_yr_ro . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_revenue)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_network_payout_val)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_sw_net_contribution)) . "</td>";
            $structure = $structure . "<td style='text-align:right'>" . $net_yr_contribuition_percent . "</td>";
            $structure = $structure . "</tr>";
        }


        $structure = $structure . "</table>";
        return $structure;
    }

    public function getTotalFinancialYearMisReportForRegion($region_name, $givenDate, $userType, $is_international)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $from_year = $year - 1;
            $to_year = $year;
        } else {
            $from_year = $year;
            $to_year = $year + 1;
        }
        $financial_year = $from_year . "-" . $to_year;
        $structure = "<table border='1' style='text-align:center;width:100%'><tr>"
            . "<th><b>Month Name</b></th>"
            . "<th><b># Ro's</b></th>"
            . "<th><b>Target</b></th>"
            . "<th><b>Revenue</b></th>"
            . "<th><b>% Achieved</b></th>";
        if (!isset($is_fct) || empty($is_fct)) {
            $structure .= "<th><b>Payout</b></th>";
        } else {
            $structure .= "<th><b>N/W Payout</b></th>";
        }
        $structure .= "<th><b>Net Cont</b></th>"
            . "<th><b>Net Cont %</b></th><tr>";


        $total_yr_ro = 0;
        $total_target = 0;
        $total_yr_revenue = 0;
        $total_yr_network_payout_val = 0;
        $total_yr_sw_net_contribution = 0;

        if ($is_international) {
            if ($userType != 2) {
                $query = "select month_name,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,sum(net_contribuition) as net_contribuition "
                    . " from ro_mis_report_datewise "
                    . " where financial_year= '$from_year' and created_date='$givenDate' and user_type='$userType' and ro_id !='0' and client !='0' and ro_id !='0' and client !='0' ";
            } else {
                $query = "select month_name,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,sum(net_contribuition) as net_contribuition "
                    . " from ro_mis_report_datewise "
                    . " where financial_year= '$from_year' and created_date='$givenDate' and ro_id !='0' and client !='0' and user_type != 1 and ro_id !='0' and client !='0' and ro_id not in (select ro_id from ro_linked_advance_ro) ";
            }
        } else {
            if ($userType != 2) {
                $query = "select month_name,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,sum(net_contribuition) as net_contribuition "
                    . " from ro_mis_report_datewise "
                    . " where financial_year= '$from_year' and created_date='$givenDate' and user_type='$userType' and ro_id !='0' and client !='0' and ro_id !='0' and client !='0' and is_international='$is_international' ";
            } else {
                $query = "select month_name,sum(total_number_ro) as total_number_ro,sum(revenue) as revenue,sum(network_payout) as network_payout,sum(net_contribuition) as net_contribuition "
                    . " from ro_mis_report_datewise "
                    . " where financial_year= '$from_year' and created_date='$givenDate' and ro_id !='0' and client !='0' and user_type != 1 and ro_id !='0' and client !='0' and is_international='$is_international' and ro_id not in (select ro_id from ro_linked_advance_ro) ";
            }
        }


        if ($region_name != 'ALL') {
            $query = $query . " and region= '$region_name' ";
        }
        $query = $query . " group by month_name order by created_date ";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultData = $result->result("array");

            foreach ($resultData as $value) {
                $total_yr_ro = $total_yr_ro + $value['total_number_ro'];
                $total_yr_revenue = $total_yr_revenue + $value['revenue'];
                $total_yr_network_payout_val = $total_yr_network_payout_val + $value['network_payout'];
                $total_yr_sw_net_contribution = $total_yr_sw_net_contribution + $value['net_contribuition'];

                //Get Region Target For $month_name For Finacial year
                $target = $this->getTargetForFinancialYearMonth($financial_year, $value['month_name'], $region_name);
                if (!isset($target) || empty($target)) {
                    $target = 0;
                    $targetAchivedPercentage = 'NA';
                } else {
                    $targetAchivedPercentage = round(($value['revenue'] / $target) * 100, 2);
                }
                $targetAchivedPercentage = sprintf("%.2F", $targetAchivedPercentage);
                $total_target = $total_target + $target;


                if ($value['revenue'] == 0) {
                    $net_contribuition_percent = 0;
                } else {
                    $net_contribuition_percent = round(($value['net_contribuition'] / $value['revenue']) * 100, 2);
                }
                $net_contribuition_percent = sprintf("%.2F", $net_contribuition_percent);

                $structure = $structure . "<tr>";
                $structure = $structure . "<td style='text-align:left'>" . $value['month_name'] . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . $value['total_number_ro'] . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($target)) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($value['revenue'])) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . $targetAchivedPercentage . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($value['network_payout'])) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . number_format(floor($value['net_contribuition'])) . "</td>";
                $structure = $structure . "<td style='text-align:right'>" . $net_contribuition_percent . "</td>";
                $structure = $structure . "</tr>";
            }

        }
        if (!isset($total_target) || empty($total_target)) {
            $total_target = 0;
            $net_yr_targetAchieved_percent = 'NA';
        } else {
            $net_yr_targetAchieved_percent = round(($total_yr_revenue / $total_target) * 100, 2);
        }
        $net_yr_targetAchieved_percent = sprintf("%.2F", $net_yr_targetAchieved_percent);

        $net_yr_contribuition_percent = round(($total_yr_sw_net_contribution / $total_yr_revenue) * 100, 2);

        $net_yr_contribuition_percent = sprintf("%.2F", $net_yr_contribuition_percent);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td style='text-align:right'>" . $total_yr_ro . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_target)) . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_revenue)) . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . $net_yr_targetAchieved_percent . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_network_payout_val)) . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . number_format(floor($total_yr_sw_net_contribution)) . "</td>";
        $structure = $structure . "<td style='text-align:right'>" . $net_yr_contribuition_percent . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;


    }

    public function generateNonFCTMisReport($givenDate, $userType)
    {
        $is_fct = 0;
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }

        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("$to_year-03-31");

        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {
            $monthName = date("F", $i);

            $start_date = date("Y-m-d", $i);
            if (date("m", $i) == date("m")) {
                $end_date = date("Y-m-t");
            } else {
                $end_date = date("Y-m-t", strtotime("+1 month -1 days", $i));
            }

            $query = "select rnfr.id,rnfr.internal_ro_number,rnfr.client,rnfr.agency_commision,rnfmp.price,reg.region_name,reg.is_international from ro_non_fct_ro rnfr "
                . " inner join ro_non_fct_request rnf on rnfr.id=rnf.non_fct_ro_id"
                . " inner join ro_non_fct_monthly_price rnfmp on rnfr.id=rnfmp.non_fct_ro_id"
                . " inner join ro_master_geo_regions as reg on rnfr.region_id = reg.id"
                . " where rnfr.financial_year='$from_year' and rnf.approved_by= 1 and rnfmp.month='$monthName'
            and rnfmp.price !=0 order by rnfr.client ";
            $query_res = $this->db->query($query);
            $data = array();

            if ($query_res->num_rows() != 0) {
                $result = $query_res->result("array");
                foreach ($result as $val) {
                    $client_name = $val['client'];
                    $non_fct_id = $val['id'];
                    $internal_ro = $val['internal_ro_number'];
                    $region = $val['region_name'];
                    $expense = $val['agency_commision'];
                    $monthlyRoAmount = $val['price'];

                    $monthlyCalculation = $this->non_fct_ro_model->nonFctMonthlyCalculation($non_fct_id, $monthName, $expense, $monthlyRoAmount, $internal_ro, $from_year);

                    $tmp = array();
                    $tmp['user_type'] = $userType;
                    $tmp['client'] = $client_name;
                    $tmp['internal_ro'] = $internal_ro;
                    $tmp['ro_id'] = $non_fct_id;
                    $tmp['region'] = $region;
                    $tmp['is_international'] = $val['is_international'];
                    $tmp['revenue'] = round($monthlyCalculation['revenue'], 2);
                    $tmp['network_payout'] = round($monthlyCalculation['network_payout'], 2);
                    $tmp['sw_net_contribute'] = round($monthlyCalculation['net_contribution'], 2);
                    $net_contribuition_percent = round(($monthlyCalculation['net_contribution'] / $monthlyCalculation['revenue']) * 100, 2);
                    $tmp['net_contribuition_percent'] = $net_contribuition_percent;

                    array_push($data, $tmp);

                    //For Testing Purpose
                    $whereData = array(
                        'ro_id' => $non_fct_id,
                        'internal_ro_number' => $internal_ro,
                        'finanacial_year' => $from_year,
                        'month_name' => date("F", strtotime($start_date)),
                        'is_fct' => $is_fct,
                        'user_type' => $userType
                    );
                    $getMisRevenueData = $this->getMisRevenueDataForTesting($whereData);
                    if (count($getMisRevenueData) > 0) {
                        $updateDataArray = array(
                            'revenue' => round($monthlyCalculation['revenue'], 2),
//                            'network_payout' => round($monthlyCalculation['total_nw_payout'], 2),
//                            'net_contribuition' => round($monthlyCalculation['total_sw_net_contribution'], 2),
                            'network_payout' => round($monthlyCalculation['network_payout'], 2),
                            'net_contribuition' => round($monthlyCalculation['net_contribution'], 2),
                            'net_contribuition_percent' => $net_contribuition_percent
                        );
                        $this->updateMisRevenueDataForTesting($updateDataArray, $whereData);
                    } else {
                        $insertDataArray = array(
                            'ro_id' => $non_fct_id,
                            'internal_ro_number' => $internal_ro,
                            'client_name' => $client_name,
                            'finanacial_year' => $from_year,
                            'month_name' => date("F", strtotime($start_date)),
                            'revenue' => round($monthlyCalculation['revenue'], 2),
//                            'network_payout' => round($monthlyCalculation['total_nw_payout'], 2),
//                            'net_contribuition' => round($monthlyCalculation['total_sw_net_contribution'], 2),
                            'network_payout' => round($monthlyCalculation['network_payout'], 2),
                            'net_contribuition' => round($monthlyCalculation['net_contribution'], 2),
                            'net_contribuition_percent' => $net_contribuition_percent,
                            'is_fct' => $is_fct,
                            'user_type' => $userType
                        );
                        $this->insertMisRevenueDataForMisTesting($insertDataArray);

                    }


                }
                //echo $data;
                $month_name = date("F", $i);
                $data = $this->convert_data_into_structure($data);
                $this->insertIntoMisReport($data, $month_name, $from_year, $is_fct, $userType, $givenDate);
                //return $this->mg_model->getMonthlyNonFctMisReport($data);

            }

        }
    }

    public function approvePendingCampaigns($internalRo, $channelId)
    {
        /*$query = "Insert into ro_approved_campaigns ('internal_ro_number','campaign_id','approval_status','timestamp') "
                . "select internal_ro_number,campaign_id from  sv_advertiser_campaign where internal_ro_number='$internalRo' and "
                . "channel_id='$channelId' and campaign_status='pending_approval' and approved_status='Not_Approved' and is_make_good =0 " ;
        $this->db->query($query) ; */
        $campaigns = $this->getPendingCampaign($internalRo, $channelId);
        if (count($campaigns) > 0) {
            foreach ($campaigns as $campaigns_val) {
                $whereData = array('internal_ro_number' => $internalRo, 'campaign_id' => $campaigns_val['campaign_id']);
                $notApprovedCampaigns = $this->getNotApprovedCampaigns($whereData);
                if (count($notApprovedCampaigns) > 0) {
                    $userUpdateData = array('approval_status' => 0, 'timestamp' => date('Y-m-d H:i:s'));
                    $this->updateNotApprovedCampaigns($userUpdateData, $whereData);
                } else {
                    $userData = array('internal_ro_number' => $internalRo, 'campaign_id' => $campaigns_val['campaign_id'], 'approval_status' => 0, 'timestamp' => date('Y-m-d H:i:s'));
                    $this->insertPendingApprovalCampaign($userData);
                }
            }
        }
    }

    public function getPendingCampaign($internalRo, $channelId)
    {
        $query = "select campaign_id from  sv_advertiser_campaign where internal_ro_number='$internalRo' and "
            . "channel_id='$channelId' and campaign_status='pending_approval' and approved_status='Not_Approved' and is_make_good =0 ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getNotApprovedCampaigns($data)
    {
        $result = $this->db->get_where('ro_approved_campaigns', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateNotApprovedCampaigns($data, $where)
    {
        $this->db->update('ro_approved_campaigns', $data, $where);
    }

    public function insertPendingApprovalCampaign($userData)
    {
        $this->db->insert('ro_approved_campaigns', $userData);
    }

    public function updateMarketRate($data, $where)
    {
        $this->db->update('sv_sw_market', $data, $where);
    }

    public function getApproximateNetContribution($ro_id = NULL)
    {
        if (isset($ro_id) && !empty($ro_id)) {
            $whereData = array('ext_ro_id' => $ro_id, 'cancel_type' => 'submit_ro_approval', 'cancel_ro_by_admin' => 0);
        } else {
            $whereData = array('cancel_type' => 'submit_ro_approval', 'cancel_ro_by_admin' => 0);
        }

        $approvalStageRo = $this->ro_model->is_request_sent($whereData);

        //Get All Market
        foreach ($approvalStageRo as $value) {
            $id = $value['id'];
            $roId = $value['ext_ro_id'];
            $marketData = $this->get_market_ro_price(array('ro_id' => $roId, 'is_cancel' => 0));

            $totalMarketWiseNetContribuition = 0;
            foreach ($marketData as $mkt) {
                $this->insertMarketRate($mkt, $roId);
                $marketWiseNetContribuition = $this->getMarketWiseNetContribuition($mkt);
                $totalMarketWiseNetContribuition += $marketWiseNetContribuition;
            }

            $roDetail = $this->am_model->ro_detail_for_ro_id($roId);
            $ro_amount = $roDetail[0]['gross'];
            $agency_commission = $roDetail[0]['agency_com'];

            $ro_amount_values = $this->get_ro_amount($roDetail[0]['internal_ro']);
            $agency_rebate = 0;

            if ($ro_amount_values[0]['agency_rebate_on'] == "ro_amount") {
                $agency_rebate = $ro_amount * ($ro_amount_values[0]['agency_rebate'] / 100);
            } else {
                $agency_rebate = ($ro_amount - $agency_commission) * ($ro_amount_values[0]['agency_rebate'] / 100);
            }
            $other_expenses_ary = $this->get_external_ro_report_details($roDetail[0]['internal_ro']);
            $other_expenses = $other_expenses_ary[0]['other_expenses'];

            $actual_net_amount = $ro_amount - $agency_commission - $agency_rebate - $other_expenses;

            $netContribuition = $actual_net_amount - $totalMarketWiseNetContribuition;
            $netContribuitionPercent = round(($netContribuition / $actual_net_amount) * 100, 2);

            $data = array('net_contribuition_percent' => $netContribuitionPercent);
            $whereApprovalData = array('id' => $id, 'cancel_type' => 'submit_ro_approval', 'cancel_ro_by_admin' => 0);
            log_message('info', 'ro_creation entering update_approval_status function with param1 STEP_13' . print_r($whereApprovalData, TRUE));
            log_message('info', 'ro_creation entering update_approval_status function with param2 STEP_14' . print_r($data, TRUE));
            $this->ro_model->update_approval_status($whereApprovalData, $data);

            //update Net Contribuition Percent According To Requirement
        }

    }

    public function insertMarketRate($market, $ro_id)
    {
        $marketName = $market['market'];
        $spotFct = $market['spot_fct'];
        $bannerFct = $market['banner_fct'];
        $spotPrice = $market['spot_price'];
        $bannerPrice = $market['banner_price'];

        if (!empty($spotFct)) {
            $spot_rate = ($spotPrice / $spotFct) * 10;
        }
        if (!empty($bannerFct)) {
            $banner_rate = ($bannerPrice / $bannerFct) * 10;
        }

        $marketData = $this->getMarketDataForMarketName(array('sw_market_name' => $marketName));

        $marketSpotRate = $marketData[0]['spot_rate'];
        $marketBannerRate = $marketData[0]['banner_rate'];

        if (!isset($marketSpotRate) || empty($marketSpotRate)) {
            $marketSpotRate = 1;
        }
        if (!isset($marketBannerRate) || empty($marketBannerRate)) {
            $marketBannerRate = 1;
        }

        if ($spotPrice == 0 || $spotFct == 0) {
            $spotPercentage = 0;
        } else {
            $spotPercentage = 100 - round(($spot_rate / $marketSpotRate) * 100, 2);
        }

        if ($bannerPrice == 0 || $bannerFct == 0) {
            $bannerPercentage = 0;
        } else {
            $bannerPercentage = 100 - round(($banner_rate / $marketBannerRate) * 100, 2);
        }


        //Update Market Price
        //less_spot_rate_percentage.
        $whereDataArray = array('ro_id' => $ro_id, 'market' => $marketName);
        $data = array('less_spot_rate_percentage' => $spotPercentage, 'less_banner_rate_percentage' => $bannerPercentage);
        $this->update_market_price($whereDataArray, $data);

    }

    public function getMarketDataForMarketName($where_data)
    {
        $result = $this->db->order_by('sw_market_name', 'ASC')->get_where('sv_sw_market', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_market_price($where_data, $data)
    {
        $this->db->where($where_data);
        $this->db->update('ro_market_price', $data);
    }

    public function getMarketWiseNetContribuition($market)
    {
        $marketName = $market['market'];
        $spotFct = $market['spot_fct'];
        $bannerFct = $market['banner_fct'];

        $channelData = $this->getActiveChannelDetailForMarket($marketName);
        $totalChannel = count($channelData);

        if ($totalChannel > 0) {
            /*
            $residueSpotFct = 0 ;
            $spotFctDivision = 0 ;
            $residueBannerFct = 0;
            $bannerFctDivision = 0 ;

            if(isset($spotFct) && !empty($spotFct)) {
                $residueSpotFct = $spotFct % $totalChannel ;
                $spotFctDivision = $spotFct/$totalChannel ;
            }

            if(isset($bannerFct) && !empty($bannerFct)) {
                $residueBannerFct = $bannerFct % $totalChannel ;
                $bannerFctDivision = $bannerFct/$totalChannel ;
            }

            $channelContribuition = 0 ;
            $residueSpotContribuition = 0 ;
            $residueBannerContribuition = 0 ; */

            foreach ($channelData as $value) {
                $spotRate = $value['spot_avg'];
                $bannerRate = $value['banner_avg'];
                $revenueShare = $value['revenue_sharing'];

                $spotContribuition = $spotFct * $spotRate * $revenueShare * (1 / 10) * (1 / 100);
                $bannerContribuition = $bannerFct * $bannerRate * $revenueShare * (1 / 10) * (1 / 100);
                $channelContribuition += $spotContribuition + $bannerContribuition;
            }
            /*
            if(isset($residueSpotFct) && !empty($residueSpotFct)) {
                $residueSpotContribuition = $residueSpotFct*$spotRate*$revenueShare ;
            }
            if(isset($residueBannerFct) && !empty($residueBannerFct)) {
                $residueBannerContribuition = $residueSpotFct*$spotRate*$revenueShare ;
            }
            $totalChannelContribuition = $channelContribuition + $residueSpotContribuition + $residueBannerContribuition ; */

            $totalChannelContribuition = $channelContribuition;
            return $totalChannelContribuition;
        } else {
            return 0;
        }
    }

    public function getActiveChannelDetailForMarket($market_name)
    {
        $query = "select distinct stc.tv_channel_id,stc.channel_name,stc.spot_avg,stc.banner_avg,sm.id as market_id,sc.revenue_sharing from sv_tv_channel stc
                   inner join sv_customer sc On sc.customer_id = stc.enterprise_id 
                   inner join sv_market_x_channel smc ON stc.tv_channel_id = smc.channel_fk_id
                   inner join sv_sw_market sm ON sm.id = smc.market_fk_id
                   where sm.sw_market_name='$market_name' and is_notice !=1 and is_blocked !=1 order by tv_channel_id ";
        $result = $this->db->query($query);
        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
        return array();
    }

    public function generateChannelPerformance($givenDate, $userType)
    {
        /*
            $current_month_start_date = date("Y-m-01",strtotime($givenDate)) ;
            $current_month_end_date = date("Y-m-t",strtotime($givenDate)) ;
        } */
        echo "start-date-time --" . date("Y-m-d H:i:s");
        $current_month_start_date = date("Y-m-d", strtotime("-2 days"));
        $current_month_end_date = date("Y-m-d", strtotime("-1 day"));

        //echo $current_month_start_date."---".$current_month_end_date ;exit;
        $am_query = "select am.id,am.client,am.internal_ro,am.cust_ro,am.camp_start_date,am.camp_end_date,reg.region_name from ro_am_external_ro am 
        inner join ro_external_ro_report_details as nr on am.internal_ro=nr.internal_ro_number
        inner join ro_master_geo_regions reg on reg.id = am.region_id 
        where test_user_creation='$userType'";
        $am_query = $am_query . " and (( camp_start_date >= '$current_month_start_date' and camp_end_date <= '$current_month_end_date' )
                                                                                        or
                        ( camp_start_date <= '$current_month_start_date' and camp_end_date between '$current_month_start_date' and '$current_month_end_date')
                                                                                        or
                        ( camp_start_date between '$current_month_start_date' and '$current_month_end_date' and camp_end_date >= '$current_month_end_date' )
                                                                                        or
                        ( camp_start_date <= '$current_month_start_date' and camp_end_date >= '$current_month_end_date' ))";
        $am_query = $am_query . " order by camp_end_date";
        $am_query_res = $this->db->query($am_query);


        echo $am_query . "\n";
        if ($am_query_res->num_rows() != 0) {
            $am_res = $am_query_res->result("array");
            echo "<pre>";
            print_r($am_res);
            foreach ($am_res as $key => $am_data) {
                //$ro_id_array = array(2563,2614,2619,2622,2643,2650,2659,2664,2681,2684,2716) ;
                $ro_id = $am_data['id']; //if( ! in_array($ro_id,$ro_id_array) ) continue ;
                //if($ro_id != 2765) continue ;
                $internal_ro = $am_data['internal_ro']; //echo $ro_id ;exit;
                //$region = $am_data['region_name'];

                $campaignDetail = $this->getAllCampaignDetail($internal_ro, $current_month_start_date, $current_month_end_date, 0);
                $makeGoodCampaignDetail = $this->getAllCampaignDetail($internal_ro, $current_month_start_date, $current_month_end_date, 1);
                echo "<pre>";
                print_r($campaignDetail);
                print_r($makeGoodCampaignDetail);//exit;
                $this->insertIntoPerformanceReport($ro_id, $internal_ro, $campaignDetail, 0);
                $this->insertIntoPerformanceReport($ro_id, $internal_ro, $makeGoodCampaignDetail, 1);
                //echo "end-time-date--".date("Y-m-d H:i:s");
                //exit;
            }

        }
        echo "end-time-date--" . date("Y-m-d H:i:s");
    }

    public function getAllCampaignDetail($internal_ro, $startDate, $endDate, $makeGood)
    {
        $data = array();
        $campaign_query = "select campaign_id,channel_id from sv_advertiser_campaign where internal_ro_number = '$internal_ro' and is_make_good = $makeGood and campaign_status !='cancelled' order by campaign_id";
        $campaign_res = $this->db->query($campaign_query);

        if ($campaign_res->num_rows() != 0) {
            $campaign_result = $campaign_res->result("array");

           /* $schedule_campaigns = array();
            $channleId_str = '';
            foreach ($campaign_result as $value) {
                //$channel_ids_array = array(821,822,823,1060,1061) ;
                //if(!in_array($value['channel_id'],$channel_ids_array)) continue ;
                array_push($schedule_campaigns, $value['campaign_id']);
                if (empty($channleId_str)) {
                    $channleId_str = $value['channel_id'];
                } else {
                    $channleId_str = $channleId_str . "," . $value['channel_id'];
                }
            }

            $scheduled_query = "select count(total_impressions) as tot_scheduled,svc.customer_name,svc.customer_id,
            stc.channel_name,stc.tv_channel_id as channel_id,stc.deployment_status,sls.date,sls.screen_region_id 
            from sv_loop_contents slc 
            INNER JOIN sv_loop_status sls ON slc.loop_id = sls.loop_id 
            INNER JOIN sv_screen sc ON sc.group_id = sls.group_id  
            INNER JOIN sv_tv_channel stc ON sc.channel_id = stc.tv_channel_id 
            INNER JOIN sv_customer svc ON svc.customer_id = stc.enterprise_id 
            where slc.campaign_id in ('" . implode("','", $schedule_campaigns) . "') and
            sls.date between '" . $startDate . "' AND '" . $endDate . "'
            and sls.screen_region_id in (1,3) and slc.is_cuetone = 0
            AND stc.tv_channel_id  IN (" . $channleId_str . ") 
            group by sls.date,stc.tv_channel_id,sls.screen_region_id order by sls.date";

            $schedule_res = $this->db->query($scheduled_query);
            $data['scheduled'] = $schedule_res->result("array");

            $played_query = "select count(*) as tot_played,stc.channel_name,svc.customer_name,svc.customer_id,stc.deployment_status,
                stc.tv_channel_id as channel_id,sar.schedule_date as date,sar.screen_region_id
            from sv_screen as svs
            INNER JOIN   sv_advertiser_report as sar ON svs.screen_id = sar.screen_id
            INNER JOIN   sv_tv_channel as stc ON svs.channel_id = stc.tv_channel_id
            INNER JOIN   sv_customer as svc ON svc.customer_id = stc.enterprise_id
            WHERE sar.screen_region_id in (1,3) AND sar.requested_url is NULL
            AND sar.campaign_id IN ('" . implode("','", $schedule_campaigns) . "')
            AND sar.schedule_date BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND stc.tv_channel_id  IN (" . $channleId_str . ") 
            group by sar.schedule_date,stc.tv_channel_id,sar.screen_region_id order by sar.schedule_date";
	    
	    $play_res = $this->db->query($played_query);
            $data['played'] = $play_res->result("array");

            return $data;

	    */

	    $schedule_campaigns = array();
            $channelId_arr = array();
            foreach ($campaign_result as $value) {
                if( !empty($value['campaign_id']) && $value['campaign_id'] != '' ){
                    array_push($schedule_campaigns, $value['campaign_id']);
                }
                if( !empty($value['channel_id']) &&  $value['channel_id'] != '' ){
                    array_push($channelId_arr, $value['channel_id']);
                }
                $schedule_campaigns = array_unique($schedule_campaigns);
                $channelId_arr = array_unique($channelId_arr);

            }

            $scheduled_query = "select count(total_impressions) as tot_scheduled,svc.customer_name,svc.customer_id,
            stc.channel_name,stc.tv_channel_id as channel_id,stc.deployment_status,sls.date,sls.screen_region_id 
            from sv_loop_contents slc 
            INNER JOIN sv_loop_status sls ON slc.loop_id = sls.loop_id 
            INNER JOIN sv_screen sc ON sc.group_id = sls.group_id  
            INNER JOIN sv_tv_channel stc ON sc.channel_id = stc.tv_channel_id 
            INNER JOIN sv_customer svc ON svc.customer_id = stc.enterprise_id 
            where slc.campaign_id in ('" . implode("','", $schedule_campaigns) . "') and
            sls.date between '" . $startDate . "' AND '" . $endDate . "'
            and sls.screen_region_id in (1,3) and slc.is_cuetone = 0
            AND stc.tv_channel_id  IN ('" . implode("','", $channelId_arr) . "')
            group by sls.date,stc.tv_channel_id,sls.screen_region_id order by sls.date";

            $schedule_res = $this->db->query($scheduled_query);
            $data['scheduled'] = $schedule_res->result("array");

            $played_query = "select count(*) as tot_played,stc.channel_name,svc.customer_name,svc.customer_id,stc.deployment_status,
                stc.tv_channel_id as channel_id,sar.schedule_date as date,sar.screen_region_id
            from sv_screen as svs
            INNER JOIN   sv_advertiser_report as sar ON svs.screen_id = sar.screen_id
            INNER JOIN   sv_tv_channel as stc ON svs.channel_id = stc.tv_channel_id
            INNER JOIN   sv_customer as svc ON svc.customer_id = stc.enterprise_id
            WHERE sar.screen_region_id in (1,3) AND sar.requested_url is NULL
            AND sar.campaign_id IN ('" . implode("','", $schedule_campaigns) . "')
            AND sar.schedule_date BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND stc.tv_channel_id  IN ('" . implode("','", $channelId_arr) . "')
            group by sar.schedule_date,stc.tv_channel_id,sar.screen_region_id order by sar.schedule_date";
            
	    $play_res = $this->db->query($played_query);
            $data['played'] = $play_res->result("array");

            return $data;
        }


    }

    public function insertIntoPerformanceReport($ro_id, $internal_ro, $campaignDetail, $makeGood)
    {
        foreach ($campaignDetail['scheduled'] as $cmpdtl) {
            if ($makeGood == 0) {
                $spotScheduleKey = 'spotScheduleSec';
                $bannerScheduleKey = 'bannerScheduleSec';
            } else {
                $spotScheduleKey = 'makeGood_spotScheduleSec';
                $bannerScheduleKey = 'makeGood_bannerScheduleSec';
            }

            if ($cmpdtl['deployment_status'] == 'Deployed') {
                $isOffline = 0;
            } else {
                $isOffline = 1;
            }

            if ($cmpdtl['screen_region_id'] == 1) {
                $spotScheduleSec = $cmpdtl['tot_scheduled'];
                $bannerScheduleSec = 0;

                $userDataForInsertion = array(
                    'ro_id' => $ro_id,
                    'internal_ro_number' => $internal_ro,
                    'channel_id' => $cmpdtl['channel_id'],
                    'channel_name' => $cmpdtl['channel_name'],
                    'customer_name' => $cmpdtl['customer_name'],
                    'customer_id' => $cmpdtl['customer_id'],
                    'createdDate' => $cmpdtl['date'],
                    $spotScheduleKey => $spotScheduleSec,
                    $bannerScheduleKey => $bannerScheduleSec,
                    'is_offline' => $isOffline
                );
                $whereData = array(
                    'ro_id' => $ro_id,
                    'channel_id' => $cmpdtl['channel_id'],
                    'customer_id' => $cmpdtl['customer_id'],
                    'createdDate' => $cmpdtl['date'],
                );
                $userDataToUpdate = array($spotScheduleKey => $spotScheduleSec);

                $this->processForPerformanceData($userDataForInsertion, $userDataToUpdate, $whereData);
                echo "schduled_spot -<pre>";
                print_r($userDataForInsertion);
                print_r($whereData);
                print_r($userDataToUpdate);
                if ($isOffline == 0) {
                    //echo "<pre> Scheduled___". print_r($userDataForInsertion).print_r($userDataToUpdate). print_r($whereData) ;
                    $this->processForPerformanceOnlineData($userDataForInsertion, $userDataToUpdate, $whereData);
                }

            } else {
                $spotScheduleSec = 0;
                $bannerScheduleSec = $cmpdtl['tot_scheduled'];

                $userDataForInsertion = array(
                    'ro_id' => $ro_id,
                    'internal_ro_number' => $internal_ro,
                    'channel_id' => $cmpdtl['channel_id'],
                    'channel_name' => $cmpdtl['channel_name'],
                    'customer_name' => $cmpdtl['customer_name'],
                    'customer_id' => $cmpdtl['customer_id'],
                    'createdDate' => $cmpdtl['date'],
                    $spotScheduleKey => $spotScheduleSec,
                    $bannerScheduleKey => $bannerScheduleSec,
                    'is_offline' => $isOffline
                );
                $whereData = array(
                    'ro_id' => $ro_id,
                    'channel_id' => $cmpdtl['channel_id'],
                    'customer_id' => $cmpdtl['customer_id'],
                    'createdDate' => $cmpdtl['date'],
                );
                $userDataToUpdate = array($bannerScheduleKey => $bannerScheduleSec);

                $this->processForPerformanceData($userDataForInsertion, $userDataToUpdate, $whereData);
                echo "scehduled_banner -<pre>";
                print_r($userDataForInsertion);
                print_r($whereData);
                print_r($userDataToUpdate);

                if ($isOffline == 0) {
                    //echo "<pre> Scheduled___". print_r($userDataForInsertion).print_r($userDataToUpdate). print_r($whereData) ;
                    $this->processForPerformanceOnlineData($userDataForInsertion, $userDataToUpdate, $whereData);
                }
            }


        }

        foreach ($campaignDetail['played'] as $cmpdtl) {

            if ($cmpdtl['deployment_status'] == 'Deployed') {
                $isOffline = 0;
            } else {
                $isOffline = 1;
            }

            if ($makeGood == 0) {
                $spotPlayedKey = 'spotPlayedSec';
                $bannerPlayedKey = 'bannerPlayedSec';
            } else {
                $spotPlayedKey = 'makeGood_spotPlayedSec';
                $bannerPlayedKey = 'makeGood_bannerPlayedSec';
            }

            if ($cmpdtl['screen_region_id'] == 1) {
                $spotPlayedSec = $cmpdtl['tot_played'];
                $bannerPlayedSec = 0;

                $userDataForInsertion = array(
                    'ro_id' => $ro_id,
                    'internal_ro_number' => $internal_ro,
                    'channel_id' => $cmpdtl['channel_id'],
                    'channel_name' => $cmpdtl['channel_name'],
                    'customer_name' => $cmpdtl['customer_name'],
                    'customer_id' => $cmpdtl['customer_id'],
                    'createdDate' => $cmpdtl['date'],
                    $spotPlayedKey => $spotPlayedSec,
                    $bannerPlayedKey => $bannerPlayedSec,
                    'is_offline' => $isOffline
                );
                $whereData = array(
                    'ro_id' => $ro_id,
                    'channel_id' => $cmpdtl['channel_id'],
                    'customer_id' => $cmpdtl['customer_id'],
                    'createdDate' => $cmpdtl['date'],
                );
                $userDataToUpdate = array($spotPlayedKey => $spotPlayedSec);
                echo "played_spot -<pre>";
                print_r($userDataForInsertion);
                print_r($whereData);
                print_r($userDataToUpdate);

                $this->processForPerformanceData($userDataForInsertion, $userDataToUpdate, $whereData);

                if ($isOffline == 0) {
                    //echo "<pre> Played___". print_r($userDataForInsertion).print_r($userDataToUpdate). print_r($whereData) ;
                    $this->processForPerformanceOnlineData($userDataForInsertion, $userDataToUpdate, $whereData);
                }
            } else {
                $spotPlayedSec = 0;
                $bannerPlayedSec = $cmpdtl['tot_played'];

                $userDataForInsertion = array(
                    'ro_id' => $ro_id,
                    'internal_ro_number' => $internal_ro,
                    'channel_id' => $cmpdtl['channel_id'],
                    'channel_name' => $cmpdtl['channel_name'],
                    'customer_name' => $cmpdtl['customer_name'],
                    'customer_id' => $cmpdtl['customer_id'],
                    'createdDate' => $cmpdtl['date'],
                    $spotPlayedKey => $spotPlayedSec,
                    $bannerPlayedKey => $bannerPlayedSec,
                    'is_offline' => $isOffline
                );
                $whereData = array(
                    'ro_id' => $ro_id,
                    'channel_id' => $cmpdtl['channel_id'],
                    'customer_id' => $cmpdtl['customer_id'],
                    'createdDate' => $cmpdtl['date'],
                );
                $userDataToUpdate = array($bannerPlayedKey => $bannerPlayedSec);
                $this->processForPerformanceData($userDataForInsertion, $userDataToUpdate, $whereData);
                echo "played_banner -<pre>";
                print_r($userDataForInsertion);
                print_r($whereData);
                print_r($userDataToUpdate);

                if ($isOffline == 0) {
                    //echo "<pre> Played___". print_r($userDataForInsertion).print_r($userDataToUpdate). print_r($whereData) ;
                    $this->processForPerformanceOnlineData($userDataForInsertion, $userDataToUpdate, $whereData);
                }
            }
        }
        //exit;
    }

    public function processForPerformanceData($userDataForInsertion, $userDataToUpdate, $whereData)
    {
        $performanceData = $this->getPerformanceData($whereData);
        if (count($performanceData) > 0) {
            $this->updatePerformanceData($userDataToUpdate, $whereData);
        } else {
            $this->insertPerformanceData($userDataForInsertion);
        }
    }

    public function getPerformanceData($whereData)
    {
        $result = $this->db->get_where('sv_mis_channel_fct_aggregate', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updatePerformanceData($data, $where)
    {
        $this->db->update('sv_mis_channel_fct_aggregate', $data, $where);
        echo "\n -- updatePerformanceData --" . $this->db->last_query() . "\n";
    }

    public function insertPerformanceData($userData)
    {
        $this->db->insert('sv_mis_channel_fct_aggregate', $userData);
        echo "\n -- insertPerformanceData --" . $this->db->last_query() . "\n";
    }

    public function processForPerformanceOnlineData($userDataForInsertion, $userDataToUpdate, $whereData)
    {
        $performanceData = $this->getPerformanceOnlineData($whereData);
        if (count($performanceData) > 0) {
            $this->updatePerformanceOnlineData($userDataToUpdate, $whereData);
        } else {
            $this->insertPerformanceOnlineData($userDataForInsertion);
        }
    }

    public function getPerformanceOnlineData($whereData)
    {
        $result = $this->db->get_where('sv_mis_online_channel_fct_aggregate', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    //For Onlline Channels

    public function updatePerformanceOnlineData($data, $where)
    {
        $this->db->update('sv_mis_online_channel_fct_aggregate', $data, $where);
        echo "\n -- updatePerformanceOnlineData --" . $this->db->last_query() . "\n";
    }

    public function insertPerformanceOnlineData($userData)
    {
        $this->db->insert('sv_mis_online_channel_fct_aggregate', $userData);
        echo "\n -- insertPerformanceOnlineData --" . $this->db->last_query() . "\n";
    }

    public function getChannelPerformanceByCriteria($isOffline, $greaterThan, $lesserThan, $from_number_of_days, $to_number_of_days)
    {
        $todays_date = date('Y-m-d', strtotime($from_number_of_days . " day"));
        $back_date = date('Y-m-d', strtotime($to_number_of_days . " days"));

        if (strtotime($back_date) < strtotime('2016-03-31')) {
            $back_date = '2016-03-31';
        }
        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Channel</b></th>"
            . "<th><b>Scheduled Impression</b></th>"
            . "<th><b>Played Impression</b></th>"
            . "<th><b>% Performance</b></th>"
            . "<th><b>MakeGood Scheduled Impression</b></th>"
            . "<th><b>MakeGood Played Impression</b></th>"
            . "<th><b>% MakeGood Performance</b></th>"
            . "<th><b>Overall Performance %</b></th></tr>";
        /*
        $query = "SELECT channel_name, spotScheduleSec, bannerScheduleSec, spotPlayedSec, bannerPlayedSec, makeGood_spotScheduleSec, makeGood_bannerScheduleSec, percentage_schedule FROM "
                . "(select channel_name,sum(spotScheduleSec) as spotScheduleSec,sum(bannerScheduleSec) as bannerScheduleSec,sum(spotPlayedSec) as spotPlayedSec,sum(bannerPlayedSec) as 	bannerPlayedSec"
                . ", sum(makeGood_spotScheduleSec) as makeGood_spotScheduleSec,sum(makeGood_bannerScheduleSec) as makeGood_bannerScheduleSec,sum(makeGood_spotPlayedSec) as makeGood_spotPlayedSec,sum(makeGood_bannerPlayedSec) as makeGood_bannerPlayedSec"
                . ", ((sum(spotScheduleSec) + sum(makeGood_spotScheduleSec)) / sum(spotPlayedSec))*100 as percentage_schedule"
                . " from sv_mis_channel_fct_aggregate "
                . " where createdDate < '$todays_date' And createdDate > '$back_date' And is_offline= $isOffline group by channel_id order by channel_name) X"
                . " WHERE 1 " ;
        if(isset($greaterThan) && !empty($greaterThan)) {
            $query .= " AND X.percentage_schedule > $greaterThan " ;
        }
        if(isset($lesserThan)) {
            if($lesserThan == 40) {
                $query .= " AND X.percentage_schedule < $lesserThan or X.percentage_schedule is null" ;
            }else{
                $query .= " AND X.percentage_schedule < $lesserThan " ;
            }
        }
         */
        $query = "select channel_name,sum(spotScheduleSec) as spotScheduleSec,sum(bannerScheduleSec) as bannerScheduleSec,sum(spotPlayedSec) as spotPlayedSec,sum(bannerPlayedSec) as 	bannerPlayedSec"
            . ", sum(makeGood_spotScheduleSec) as makeGood_spotScheduleSec,sum(makeGood_bannerScheduleSec) as makeGood_bannerScheduleSec,sum(makeGood_spotPlayedSec) as makeGood_spotPlayedSec,sum(makeGood_bannerPlayedSec) as makeGood_bannerPlayedSec"
            . " from sv_mis_channel_fct_aggregate "
            . " where createdDate <= '$todays_date' And createdDate >= '$back_date' And is_offline= $isOffline and spotScheduleSec > 0 group by channel_id order by channel_name ";
        $result = $this->db->query($query);

        $total_scheduled_impression = 0;
        $total_played_impression = 0;
        $total_makeGood_scheduled_impression = 0;
        $total_makeGood_played_impression = 0;

        if ($result->num_rows() > 0) {
            $values = $result->result("array");
            foreach ($values as $val) {
                $channel_name = $val['channel_name'];

                $scheduled_impression = $val['spotScheduleSec'];
                $played_impression = $val['spotPlayedSec'];

                if ($played_impression > $scheduled_impression) {
                    $played_impression = $scheduled_impression;
                }

                $makeGood_scheduled_impression = $val['makeGood_spotScheduleSec'];
                $makeGood_played_impression = $val['makeGood_spotPlayedSec'];

                if ($makeGood_played_impression > $makeGood_scheduled_impression) {
                    $makeGood_played_impression = $makeGood_scheduled_impression;
                }

                $overAllPerformancePercentage = round((($played_impression + $makeGood_played_impression) / $scheduled_impression) * 100, 2);

                if (($overAllPerformancePercentage >= $greaterThan) && ($overAllPerformancePercentage < $lesserThan)) {

                    if ($scheduled_impression != 0) {
                        $performance_percentage = round(($played_impression / $scheduled_impression) * 100, 2);
                    } else {
                        $performance_percentage = 'N.A';
                    }
                    $performance_percentage = sprintf("%.2F", $performance_percentage);

                    if ($makeGood_scheduled_impression != 0) {
                        $makeGood_performance_percentage = round(($makeGood_played_impression / $makeGood_scheduled_impression) * 100, 2);
                    } else {
                        $makeGood_performance_percentage = 'N.A';
                    }
                    $makeGood_performance_percentage = sprintf("%.2F", $makeGood_performance_percentage);


                    $total_scheduled_impression = $total_scheduled_impression + $scheduled_impression;
                    $total_played_impression = $total_played_impression + $played_impression;
                    $total_makeGood_scheduled_impression = $total_makeGood_scheduled_impression + $makeGood_scheduled_impression;
                    $total_makeGood_played_impression = $total_makeGood_played_impression + $makeGood_played_impression;

                    $structure = $structure . "<tr>";
                    $structure = $structure . "<td>" . $channel_name . "</td>";
                    $structure = $structure . "<td>" . number_format($scheduled_impression) . "</td>";
                    $structure = $structure . "<td>" . number_format($played_impression) . "</td>";
                    $structure = $structure . "<td>" . $performance_percentage . "</td>";
                    $structure = $structure . "<td>" . number_format($makeGood_scheduled_impression) . "</td>";
                    $structure = $structure . "<td>" . number_format($makeGood_played_impression) . "</td>";
                    $structure = $structure . "<td>" . $makeGood_performance_percentage . "</td>";
                    $structure = $structure . "<td>" . $overAllPerformancePercentage . "</td>";
                    $structure = $structure . "</tr>";
                }

            }
        }
        if ($total_scheduled_impression != 0) {
            $total_performance_percenatge = round(($total_played_impression / $total_scheduled_impression) * 100, 2);
        }
        $total_performance_percenatge = sprintf("%.2F", $total_performance_percenatge);

        if ($total_makeGood_scheduled_impression != 0) {
            $total_makeGood_performance_percenatge = round(($total_makeGood_played_impression / $total_makeGood_scheduled_impression) * 100, 2);
        }
        $total_makeGood_performance_percenatge = sprintf("%.2F", $total_makeGood_performance_percenatge);
        $total_overall_performance_percentage = round((($total_played_impression + $total_makeGood_played_impression) / $total_scheduled_impression) * 100, 2);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . number_format($total_scheduled_impression) . "</td>";
        $structure = $structure . "<td>" . number_format($total_played_impression) . "</td>";
        $structure = $structure . "<td>" . $total_performance_percenatge . "</td>";
        $structure = $structure . "<td>" . number_format($total_makeGood_scheduled_impression) . "</td>";
        $structure = $structure . "<td>" . number_format($total_makeGood_played_impression) . "</td>";
        $structure = $structure . "<td>" . $total_makeGood_performance_percenatge . "</td>";
        $structure = $structure . "<td>" . $total_overall_performance_percentage . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;

    }

    public function deployedAndNotPinging($from_number_of_days, $to_number_of_days)
    {
        $todays_date = date('Y-m-d', strtotime($from_number_of_days . " day"));
        $back_date = date('Y-m-d', strtotime($to_number_of_days . " days"));
        if (strtotime($back_date) < strtotime('2016-03-31')) {
            $back_date = '2016-03-31';
        }

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Channel</b></th>"
            . "<th><b>Scheduled Impression</b></th>"
            . "<th><b>Played Impression</b></th>"
            . "<th><b>% Performance</b></th>"
            . "<th><b>MakeGood Scheduled Impression</b></th>"
            . "<th><b>MakeGood Played Impression</b></th>"
            . "<th><b>% MakeGod Performance</b></th>"
            . "<th><b>Overall Performance %</b></th></tr>";

        $query = "select channel_name from sv_tv_channel where deployment_status = 'Deployed' 
                  and tv_channel_id not in
                    (select distinct ch.tv_channel_id
                                    from sv_tv_channel ch 
                                    inner join sv_channel_monitor scm 
                                    ON ch.tv_channel_id = scm.channel_id 
                                    where client_connect_time between '" . $back_date . " 00:00:00' and  '" . $todays_date . " 23:59:59') order by channel_name";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $values = $result->result("array");
            foreach ($values as $val) {
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $val['channel_name'] . "</td>";
                $structure = $structure . "<td> - </td>";
                $structure = $structure . "<td> - </td>";
                $structure = $structure . "<td> - </td>";
                $structure = $structure . "<td> - </td>";
                $structure = $structure . "<td> - </td>";
                $structure = $structure . "<td> - </td>";
                $structure = $structure . "<td> - </td>";
                $structure = $structure . "</tr>";
            }
        }
        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td> - </td>";
        $structure = $structure . "<td> - </td>";
        $structure = $structure . "<td> - </td>";
        $structure = $structure . "<td> - </td>";
        $structure = $structure . "<td> - </td>";
        $structure = $structure . "<td> - </td>";
        $structure = $structure . "<td> - </td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;
    }

    public function getChannelPerformance($isOffline, $from_number_of_days, $to_number_of_days)
    {
        $todays_date = date('Y-m-d', strtotime($from_number_of_days . " day"));
        $back_date = date('Y-m-d', strtotime($to_number_of_days . " days"));

        if (strtotime($back_date) < strtotime('2016-03-31')) {
            $back_date = '2016-03-31';
        }
        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Channel</b></th>"
            . "<th><b>Scheduled Impression</b></th>"
            . "<th><b>Played Impression</b></th>"
            . "<th><b>% Performance</b></th>"
            . "<th><b>MakeGood Scheduled Impression</b></th>"
            . "<th><b>MakeGood Played Impression</b></th>"
            . "<th><b>% MakeGod Performance</b></th>"
            . "<th><b>Overall Performance %</b></th></tr>";

        $query = "select channel_name,sum(spotScheduleSec) as spotScheduleSec,sum(bannerScheduleSec) as bannerScheduleSec,sum(spotPlayedSec) as spotPlayedSec,sum(bannerPlayedSec) as 	bannerPlayedSec"
            . ", sum(makeGood_spotScheduleSec) as makeGood_spotScheduleSec,sum(makeGood_bannerScheduleSec) as makeGood_bannerScheduleSec,sum(makeGood_spotPlayedSec) as makeGood_spotPlayedSec,sum(makeGood_bannerPlayedSec) as makeGood_bannerPlayedSec"
            . " from sv_mis_channel_fct_aggregate "
            . " where createdDate <= '$todays_date' And createdDate >= '$back_date' And is_offline= $isOffline and spotScheduleSec > 0 group by channel_id order by channel_name";
        $result = $this->db->query($query);

        $total_scheduled_impression = 0;
        $total_played_impression = 0;
        $total_makeGood_scheduled_impression = 0;
        $total_makeGood_played_impression = 0;

        if ($result->num_rows() > 0) {
            $values = $result->result("array");
            foreach ($values as $val) {
                $channel_name = $val['channel_name'];

                // $scheduled_impression = $val['spotScheduleSec'] + $val['bannerScheduleSec'] ;
                // $played_impression = $val['spotPlayedSec'] + $val['bannerPlayedSec'] ;
                $scheduled_impression = $val['spotScheduleSec'];
                $played_impression = $val['spotPlayedSec'];

                if ($scheduled_impression != 0) {
                    $performance_percentage = round(($played_impression / $scheduled_impression) * 100, 2);
                } else {
                    $performance_percentage = 'N.A';
                }
                $performance_percentage = sprintf("%.2F", $performance_percentage);

                $total_scheduled_impression = $total_scheduled_impression + $scheduled_impression;
                $total_played_impression = $total_played_impression + $played_impression;

                //$makeGood_scheduled_impression = $val['makeGood_spotScheduleSec'] + $val['makeGood_bannerScheduleSec'] ;
                //$makeGood_played_impression = $val['makeGood_spotPlayedSec'] + $val['makeGood_bannerPlayedSec'] ;

                $makeGood_scheduled_impression = $val['makeGood_spotScheduleSec'];
                $makeGood_played_impression = $val['makeGood_spotPlayedSec'];

                if ($makeGood_scheduled_impression != 0) {
                    $makeGood_performance_percentage = round(($makeGood_played_impression / $makeGood_scheduled_impression) * 100, 2);
                } else {
                    $makeGood_performance_percentage = 'N.A';
                }
                $makeGood_performance_percentage = sprintf("%.2F", $makeGood_performance_percentage);

                $total_makeGood_scheduled_impression = $total_makeGood_scheduled_impression + $makeGood_scheduled_impression;
                $total_makeGood_played_impression = $total_makeGood_played_impression + $makeGood_played_impression;

                $overAllPerformancePercentage = round((($played_impression + $makeGood_played_impression) / $scheduled_impression) * 100, 2);

                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $channel_name . "</td>";
                $structure = $structure . "<td>" . number_format($scheduled_impression) . "</td>";
                $structure = $structure . "<td>" . number_format($played_impression) . "</td>";
                $structure = $structure . "<td>" . $performance_percentage . "</td>";
                $structure = $structure . "<td>" . number_format($makeGood_scheduled_impression) . "</td>";
                $structure = $structure . "<td>" . number_format($makeGood_played_impression) . "</td>";
                $structure = $structure . "<td>" . $makeGood_performance_percentage . "</td>";
                $structure = $structure . "<td>" . $overAllPerformancePercentage . "</td>";
                $structure = $structure . "</tr>";
            }
        }
        if ($total_scheduled_impression != 0) {
            $total_performance_percenatge = round(($total_played_impression / $total_scheduled_impression) * 100, 2);
        }
        $total_performance_percenatge = sprintf("%.2F", $total_performance_percenatge);

        if ($total_makeGood_scheduled_impression != 0) {
            $total_makeGood_performance_percenatge = round(($total_makeGood_played_impression / $total_makeGood_scheduled_impression) * 100, 2);
        }
        $total_makeGood_performance_percenatge = sprintf("%.2F", $total_makeGood_performance_percenatge);
        $total_overall_performance_percentage = round((($total_played_impression + $total_makeGood_played_impression) / $total_scheduled_impression) * 100, 2);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . number_format($total_scheduled_impression) . "</td>";
        $structure = $structure . "<td>" . number_format($total_played_impression) . "</td>";
        $structure = $structure . "<td>" . $total_performance_percenatge . "</td>";
        $structure = $structure . "<td>" . number_format($total_makeGood_scheduled_impression) . "</td>";
        $structure = $structure . "<td>" . number_format($total_makeGood_played_impression) . "</td>";
        $structure = $structure . "<td>" . $total_makeGood_performance_percenatge . "</td>";
        $structure = $structure . "<td>" . $total_overall_performance_percentage . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        return $structure;
    }

    public function getChannelSummary($from_number_of_days, $to_number_of_days)
    {
        $todays_date = date('Y-m-d', strtotime($from_number_of_days . " day"));
        $back_date = date('Y-m-d', strtotime($to_number_of_days . " days"));

        if (strtotime($back_date) < strtotime('2016-03-31')) {
            $back_date = '2016-03-31';
        }

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th style='width:50%'><b>&nbsp;</b></th>"
            . "<th style='width:50%'><b>No. Of Channels</b></th>"
            . "</tr>";

        $query_total_deployed = "SELECT * FROM `sv_tv_channel` WHERE `deployment_status` = 'Deployed' ";
        $result_total_deployed = $this->db->query($query_total_deployed);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>Total Number of Deployed</td>";
        $structure = $structure . "<td>" . $result_total_deployed->num_rows() . "</td>";
        $structure = $structure . "</tr>";

        $query_deployed_pinging = "select distinct ch.tv_channel_id
                                    from sv_tv_channel ch 
                                    inner join sv_channel_monitor scm 
                                    ON ch.tv_channel_id = scm.channel_id 
                                    where client_connect_time between '" . $back_date . " 00:00:00' and '" . $todays_date . " 23:59:59' ";
        $result_deployed_pinging = $this->db->query($query_deployed_pinging);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>Number of Deployed and Pinging</td>";
        $structure = $structure . "<td>" . $result_deployed_pinging->num_rows() . "</td>";
        $structure = $structure . "</tr>";

        $query_deployed_Ro = "select distinct channel_id from sv_mis_online_channel_fct_aggregate where createdDate between '$back_date' And '$todays_date'";
        $result_deployed_Ro = $this->db->query($query_deployed_Ro);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>No. of Deployed And Ro</td>";
        $structure = $structure . "<td>" . $result_deployed_Ro->num_rows() . "</td>";
        $structure = $structure . "</tr>";

        $query_deployed_ro_not_pinging = "select distinct ch.tv_channel_id
                                    from sv_tv_channel ch 
                                    inner join sv_channel_monitor scm ON ch.tv_channel_id = scm.channel_id 
                                    inner join sv_mis_online_channel_fct_aggregate smocfa on scm.channel_id = smocfa.channel_id                                    
                                    where client_connect_time between '" . $back_date . " 00:00:00' and  '" . $todays_date . " 23:59:59' and createdDate between '$back_date' And '$todays_date' ";
        $result_deployed_ro_not_pinging = $this->db->query($query_deployed_ro_not_pinging);
        $deployedRoAndNotPinging = $result_deployed_Ro->num_rows() - $result_deployed_ro_not_pinging->num_rows();

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>No. of Deployed And Ro And Not Pinging</td>";
        $structure = $structure . "<td>" . $deployedRoAndNotPinging . "</td>";
        $structure = $structure . "</tr>";


        $query_non_deployed_Ro = "select distinct channel_id from sv_mis_channel_fct_aggregate where createdDate between '$back_date' And '$todays_date' and is_offline = 1 ";
        $result_non_deployed_Ro = $this->db->query($query_non_deployed_Ro);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>No. of Non Deployed And Ro</td>";
        $structure = $structure . "<td>" . $result_non_deployed_Ro->num_rows() . "</td>";
        $structure = $structure . "</tr>";

        /*$query = " SELECT X.is_offline, count( * ) as channel_count  from "
                . "(select * from sv_mis_channel_fct_aggregate where createdDate < '$todays_date' And createdDate > '$back_date' group by channel_id,is_offline)X"
                . " GROUP BY X.is_offline" ;
        $result = $this->db->query($query);
        $chanel_non_deployed_and_ro = 0 ;

        if($result->num_rows() > 0){
            $values =  $result->result("array");
            foreach($values as $val) {
                if($val['is_offline'] == 0) {

                }else{
                    $chanel_non_deployed_and_ro = $val['channel_count'] ;
                }

            }
        }

        $structure = $structure."<tr>";
        $structure = $structure."<td>No. of Non Deployed And Ro</td>";
        $structure = $structure."<td>".$chanel_non_deployed_and_ro."</td>";
        $structure = $structure."</tr>";
        */

        $structure = $structure . "</table>";
        return $structure;
    }

    public function getOnlineChannelSummary($from_number_of_days, $to_number_of_days)
    {
        $todays_date = date('Y-m-d', strtotime($from_number_of_days . " day"));
        $back_date = date('Y-m-d', strtotime($to_number_of_days . " days"));

        if (strtotime($back_date) < strtotime('2016-03-31')) {
            $back_date = '2016-03-31';
        }

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th style='width:50%'><b>Channel Performance % </b></th>"
            . "<th style='width:50%'><b>No. of Channels</b></th>"
            . "</tr>";
        $query = "select channel_name,sum(spotScheduleSec) as spotScheduleSec,sum(bannerScheduleSec) as bannerScheduleSec,sum(spotPlayedSec) as spotPlayedSec,sum(bannerPlayedSec) as 	bannerPlayedSec"
            . ", sum(makeGood_spotScheduleSec) as makeGood_spotScheduleSec,sum(makeGood_bannerScheduleSec) as makeGood_bannerScheduleSec,sum(makeGood_spotPlayedSec) as makeGood_spotPlayedSec,sum(makeGood_bannerPlayedSec) as makeGood_bannerPlayedSec"
            . " from sv_mis_channel_fct_aggregate "
            . " where createdDate <= '$todays_date' And createdDate >= '$back_date' And is_offline= 0 and spotScheduleSec > 0 group by channel_id order by channel_name";
        $result = $this->db->query($query);
        $criteriaValue = $this->getChannelByGrouping($result);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>Above 95%</td>";
        $structure = $structure . "<td>" . $criteriaValue['Above_95'] . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>Between 85% - 95%</td>";
        $structure = $structure . "<td>" . $criteriaValue['85-95'] . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>Between 75% - 85%</td>";
        $structure = $structure . "<td>" . $criteriaValue['75-85'] . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>Between 60% - 75%</td>";
        $structure = $structure . "<td>" . $criteriaValue['60-75'] . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>Between 40% - 60%</td>";
        $structure = $structure . "<td>" . $criteriaValue['40-60'] . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>Below 40%</td>";
        $structure = $structure . "<td>" . $criteriaValue['Below_40'] . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "<tr>";
        $structure = $structure . "<td> # </td>";
        $structure = $structure . "<td>" . $criteriaValue['totalChannelNumber'] . "</td>";
        $structure = $structure . "</tr>";

        $structure = $structure . "</table>";
        $structure = $structure . "<br/><br/>";

        $structure_1 = "<span><b>Overall Network Performance for Online Channels :" . $criteriaValue['overallPerfomance'] . "</b></span>";
        $structure_1 = $structure_1 . "</table>";
        return $structure . $structure_1;
    }

    private function getChannelByGrouping($result)
    {
        if ($result->num_rows() > 0) {
            $values = $result->result("array");
            $data = array();
            $totalChannel = 0;
            $totalPlayedImpression = 0;
            $totalScheduledImpression = 0;

            foreach ($values as $val) {
                $scheduled_impression = $val['spotScheduleSec'];
                $played_impression = $val['spotPlayedSec'];

                if ($played_impression > $scheduled_impression) {
                    $played_impression = $scheduled_impression;
                }

                $makeGood_scheduled_impression = $val['makeGood_spotScheduleSec'];
                $makeGood_played_impression = $val['makeGood_spotPlayedSec'];

                if ($makeGood_played_impression > $makeGood_scheduled_impression) {
                    $makeGood_played_impression = $makeGood_scheduled_impression;
                }

                $totalPlayedImpression += ($makeGood_played_impression + $played_impression);
                $totalScheduledImpression += $scheduled_impression;

                $performance_percentage = round((($played_impression + $makeGood_played_impression) / $scheduled_impression) * 100, 2);

                if ($performance_percentage >= 95) {
                    $data['Above_95'] += 1;
                    $totalChannel += 1;
                } else if ($performance_percentage >= 85 && $performance_percentage < 95) {
                    $data['85-95'] += 1;
                    $totalChannel += 1;
                } else if ($performance_percentage >= 75 && $performance_percentage < 85) {
                    $data['75-85'] += 1;
                    $totalChannel += 1;
                } else if ($performance_percentage >= 60 && $performance_percentage < 75) {
                    $data['60-75'] += 1;
                    $totalChannel += 1;
                } else if ($performance_percentage >= 40 && $performance_percentage < 60) {
                    $data['40-60'] += 1;
                    $totalChannel += 1;
                } else if ($performance_percentage >= 0 && $performance_percentage < 40) {
                    $data['Below_40'] += 1;
                    $totalChannel += 1;
                } else {
                    $data['Zero'] += 1;
                    //$totalChannel += 1 ;
                }
            }
            $overallPercentage = round(($totalPlayedImpression / $totalScheduledImpression) * 100, 2);
            $data['totalChannelNumber'] = $totalChannel;
            $data['overallPerfomance'] = $overallPercentage;
            return $data;
        }

    }

    function getCCMailIdList($roId)
    {

        $query = "
                    SELECT
                    GROUP_CONCAT(ru.user_email) as ccMailId
                    FROM
                    ro_user ru
                    LEFT OUTER JOIN
                    ro_external_ro_user_map rum ON ru.user_id = rum.account_manager
                    OR ru.user_id = rum.regional_director
                    where rum.external_ro_id = " . $roId;

        $obj = $this->db->query($query);
        return $obj->result_array();
    }

    public function getInternalRoNumber()
    {
        $query = "SELECT sbrs.ro_id,raer.internal_ro FROM sv_br_ro_schedule AS sbrs
					INNER JOIN ro_am_external_ro raer ON sbrs.ro_id = raer.id 
					WHERE sbrs.status = 'PENDING'";
        $obj = $this->db->query($query);
        return $obj->result_array();
    }

    public function insert_into_br_ro_schedule($internal_ro_number)
    {
        $roDetails = $this->get_ext_ro_details(array('internal_ro' => $internal_ro_number));
        $roId = $roDetails[0]['id'];
        $brRoSchedule = $this->getBrRoSchedule(array('ro_id' => $roId));
        if (count($brRoSchedule) > 0) {
            return;
        } else {
            $userData = array('ro_id' => $roId, 'status' => 'pending', 'ro_status' => 'created');
            $this->insertIntoBrRoSchedule($userData);
        }
    }

    public function get_ext_ro_details($data)
    {

        $result = $this->db->get_where('ro_am_external_ro', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();

    }

    public function getBrRoSchedule($data)
    {
        $result = $this->db->get_where('sv_br_ro_schedule', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function insertIntoBrRoSchedule($userData)
    {
        $this->db->insert('sv_br_ro_schedule', $userData);
    }

    public function updateBrRoSchedule($data, $where)
    {
        $this->db->update('sv_br_ro_schedule', $data, $where);
    }

    public function insert_into_external_ro_report_detail($data)
    {
        $this->db->insert('ro_external_ro_report_details', $data);
    }

    public function insertIntoRoSureFire($data)
    {
        $this->db->insert('ro_surefire_update', $data);
    }

    public function getDataFromRoSureFire($data)
    {
        $result = $this->db->get_where('ro_surefire_update', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
    }

    public function updateRoSureFireData($data, $where)
    {
        $this->db->update('ro_surefire_update', $data, $where);
    }

    public function getCaptionId($whereData)
    {
        $this->db->select('content_id');
        $query = $this->db->get_where('sv_advertiser_content', $whereData);
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function getCaptionIdForInternalRo($caption_name, $internal_ro_number)
    {
        $query = "select distinct adc.content_id as content_id
				   from sv_advertiser_content adc
				   inner join sv_advertiser_playlist_contents adpc on adc.content_id = adpc.content_id
				   inner join sv_advertiser_campaign adcc on adcc.playlist_id = adpc.playlist_id
				   where adcc.internal_ro_number = '$internal_ro_number' and adcc.caption_name = '$caption_name'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function getCaptionNameOfBrandId($whereData)
    {
        $this->db->distinct();
        $this->db->select('caption_name');
        $query = $this->db->get_where('sv_advertiser_campaign', $whereData);
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function getStaticEmails($typeArr)
    {
        $this->db->select('GROUP_CONCAT(email_id) as static_emails');
        $this->db->from('roStaticEmails');
        $this->db->where_in('type', $typeArr);
        $this->db->group_by('type');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function insertInvoiceCancelData($networkFinalInfo)
    {
        $this->db->insert('ro_cancel_invoice', $networkFinalInfo);
        log_message('info', 'ro_cancel_invoice printing insert query - ' . $this->db->last_query());
    }

    public function updateInvoiceCancelData($networkFinalInfo)
    {

        $this->db->where('network_ro_number', $networkFinalInfo['network_ro_number']);
        $this->db->update('ro_cancel_invoice', $networkFinalInfo);
        log_message('info', 'ro_cancel_invoice printing update query - ' . $this->db->last_query());

    }

    public function checkForPresenceOfInvoicedata($network_ro)
    {
        $this->db->select('*');
        $this->db->from('ro_cancel_invoice');
        $this->db->where('network_ro_number', $network_ro);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function getAllNetworkInfo($customer_id, $internal_ro)
    {
        $query = "SELECT rnr.customer_ro_number, 
						rnr.internal_ro_number,
						rnr.network_ro_number,rnr.customer_name,rnr.start_date,rnr.end_date,rnr.release_date,
						rnr.gross_network_ro_amount,rnr.customer_share,ran.customer_id,
						group_concat(ran.channel_name)as channel_names,
						ran.billing_name,ran.revision_no,rnr.client_name
						FROM ro_approved_networks ran
						INNER JOIN ro_network_ro_report_details rnr ON ran.internal_ro_number = rnr.internal_ro_number
						WHERE ran.customer_id = $customer_id AND ran.customer_name = rnr.customer_name AND ran.internal_ro_number = '" . $internal_ro . "' 
						GROUP BY rnr.network_ro_number";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function getAllRoCancelInvoiceData($whereCancelInvoiceArray)
    {
        $this->db->select('*');
        $this->db->from('ro_cancel_invoice');
        $this->db->where($whereCancelInvoiceArray);
        $query = $this->db->get();
        log_message('info', 'ro_cancel_invoice printing getAllRoCancelInvoiceData function query - ' . $this->db->last_query());
        if ($query->num_rows() > 0) {
            return $query->result("array");
        }
        return array();
    }

    public function updateStatusForRoCancelInvoiceData($network_ro)
    {
        $updatedata = array('pdf_processing' => 1);
        $this->db->where('network_ro_number', $network_ro);
        $this->db->update('ro_cancel_invoice', $updatedata);
        log_message('info', 'ro_cancel_invoice printing updateStatusForRoCancelInvoiceData function update query - ' . $this->db->last_query());
    }

    public function convertCancelDetailsIntoHtml($data)
    {


        $html =
            "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html>
		<head>

			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
			<title>Surewaves Easy RO</title>

			<link rel=\"stylesheet\" type=\"text/css\" href=\"http://easyro.surewaves.com/surewaves_easy_ro/bootstrap/css/bootstrap.min.css\" />";

        $html .=
            "<style type=\"text/css\">
				body {
					font-size:16px;
				}
				.h4, h4 {
					font-size: 20px;
				}
				.table-details {
					margin-left: 25px;
				}
				.table-details > tbody > tr > td {
					border-top:none;
					padding:3px;
				}
				.table-details > tbody > tr > th {
					border-top:none;
					padding:3px;
				}
			</style>

		</head>";
        $revision_txt = '';
        $nw_ro_txt = 'Network RO Number';
        $revision_no = '';
        if ($data['revision_no'] > 0) {
            $revision_txt = '  -  Revision';
            $nw_ro_txt = 'Revised NRO Number';
            $revision_no = '-R' . $data['revision_no'];
        }
        $nw_ro_number = $data['network_ro_number'];
        $start_date = date('d-M-Y', strtotime($data['start_date']));
        $end_date = date('d-M-Y', strtotime($data['end_date']));


        $html .= "<body>
				<div class=\"container\">

						<div class=\"col-xs-12\" style=\"margin-top:10px\">

							<div class=\"col-xs-3\">
								<label>
									<h2 style=\"margin-right:10px;float:right\"><img src=\"http://easyro.surewaves.com/surewaves_easy_ro/images/logo_pdf.png\" style=\"width: 180px;\"/></h2>
								</label>
							</div>

							<div class=\"col-xs-9\">
								<div class=\"col-xs-12\" style=\"padding-top:40px;padding-left: 108px;\">
									<label><h4>SureWaves National Spot TV Network</h4></label>
								</div>
							</div>


						</div>

						<div class=\"col-xs-12 text-center text-success\">
							<label><h4>Network Release Order - <b style=\"color:red\">Cancel</b> </h4></label>
						</div>";

        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<div class=\"col-xs-12\">";
        $html .= "<div class=\"col-xs-3 \"><label>" . $nw_ro_txt . "</label></div>";
        $html .= "<div class=\"col-xs-9\">" . $nw_ro_number . '' . $revision_no . "- <b>Cancel</b></div>
			</div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Network Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['customer_name'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Billing Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['billing_name'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Market/Cluster</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['market'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Channels</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['channel_names'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Advertiser Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['client_name'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Campaign Period</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $start_date . " to " . $end_date . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Release Date</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . date("d-M-Y H:i:s") . "</div>
					 </div>";

        $total_amount = $data['gross_network_ro_amount'];
        $surewaves_share = 100 - $data['customer_share'];
        $network_amount = $total_amount * (100 - $surewaves_share) / 100;
        if ($surewaves_share != 0) {

            $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Investment</label></div>";
            $html .= "   <div class=\"col-xs-9\">-" . round($total_amount) . "(Plus GST)</div>
             </div>";

        }
        $html .= "<div class=\"col-xs-12\">
            <div class=\"col-xs-3 \"><label>Net Amount Payable</label></div>";
        $html .= "   <div class=\"col-xs-9\">INR -" . round($network_amount) . "(Plus GST)</div>
         </div>";

        $html .= "<br/>";
        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<table class=\"table table-condensed table-details\" style=\"text-align: left \">
				 <tr><th>Terms & Conditions for RO's:</th></tr>";
        $html .= "<tr><td>1. Spots are to be scheduled as mentioned and within the day-part specified in the RO</td></tr>";
        $html .= "<tr><td>2. If any spots are missed for any reason, Make Good will be done as per schedule made by SureWaves</td></tr>";
        $html .= "<tr><td>3. In case of shortfalls within the campaign schedule, customer will not be paying for such shortfall of spots, if any, and the same shall be adjusted in final payment, as applicable.</td></tr>";
        $html .= "<tr><td>4. PAN No and Service Tax No (where applicable) must be provided to SureWaves before payments are released.</td></tr>";
        $html .= "<tr><td>5. Invoices to be raised in the name of \"SureWaves MediaTech Pvt. Ltd.\" at the address mentioned below.</td></tr>";
        $html .= "<tr><td>6. Please update our GST No. 29AABCI5171N1Z4 compulsorily against the Invoice which is raised to us.</td></tr>";
        $html .= "<tr><td style=\"color:red\">7. If Invoice has already been raised against the release order (which contains  “CANCEL”), please cancel that Invoice or Issue a Credit Note to SureWaves, in case if you can’t cancel the Invoice.</td></tr>";
        $html .= "</table>";


        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" />";

        $html .= "<table class=\"table table-condensed table-details\" style=\"text-align: left \">";
        $html .= "<tr><th>SUREWAVES MEDIATECH PRIVATE LIMITED,</th></tr>";
        $html .= "<tr><td>3rd Floor, Ashok Chambers, 6th Cross,</td></tr>";
        $html .= "<tr><td>Koramangala, Srinivagulu, Near Ejipura Junction, 25 Intermediate Ring Road,</td></tr>";
        $html .= "<tr><td>Bangalore - 560047,</td></tr>";
        $html .= "<tr><td>Tel:+91 80 49698922, Fax: +91 80 49698910</td></tr>";
        $html .= "</table>";

        $html .= "<h5 style=\"margin-left: 25px;\">THIS IS A COMPUTER GENERATED RELEASE ORDER AND DOES NOT REQUIRE SIGNATURE AND STAMP.</h5>";
        $html .= '</div></body><html>';
        return array('status' => true, 'html' => $html, 'network_ro_number' => $nw_ro_number, 'revision' => $data['revision_no']);

    }

//    public function getCampaignsForInternalRo($internal_ro_number)
//    {
//        $query = "select group_concat(ac.campaign_id) as campaignIds from sv_advertiser_campaign as ac where ac.internal_ro_number = '$internal_ro_number' and `is_make_good` = 0 and selected_region_id in (1,3) and visible_in_easyro = 1";
//        $res = $this->db->query($query);
//        $result = $res->result("array");
//        return $result[0]['campaignIds'];
//    }

    /**
     * Method to Check if the Given RO is deleted
     * @param integer $ro_id - ID of the Ro
     * @return boolean - TRUE, if RO is cancelled
     */
    private function is_ro_cancelled($ro_id)
    {
        $result = $this->db->get_where('ro_status', array('am_external_ro_id' => $ro_id, 'ro_status' => 'cancelled'));
        if ($result->num_rows() == 0)
            return false;
        return true;
    }

    public function get_channel_performance_details($startDate, $endDate)
    {
        log_message('debug','In mg_model@get_channel_performance_details | Entered');
        $query = "select * from sv_mis_channel_fct_aggregate ";
        if(isset($startDate) && isset($endDate)){
            $where = " where createdDate >= '$startDate' and createdDate <= '$endDate'";
            $query = $query . "" . $where;
        }
        $res = $this->db->query($query);
        $result = $res->result('array');
        return $result;
    }
}
?>
