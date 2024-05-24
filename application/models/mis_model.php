<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mis_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getMonthlyFct($givenDate)
    {

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
            $am_res = $am_query_res->result("array");
            foreach ($am_res as $am_data) {
                $client_name = $am_data['client'];
                //$ext_ro = $am_data['cust_ro'];
                $am_ext_id = $am_data['id'];
                $internal_ro = $am_data['internal_ro'];
                $proportional_calculation = $this->time_period_proportional_calculation($am_ext_id, $internal_ro, $current_month_start_date, $current_month_end_date);

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
            $data = $this->convert_data_into_structure($data);
            return $this->getMonthlyMisReport($data);
        }
    }

    public function time_period_proportional_calculation($am_ext_id, $internal_ro, $given_start_date, $given_end_date)
    {
        $market_price_data = array('ro_id' => $am_ext_id);
        $market_price = $this->mg_model->get_market_ro_price($market_price_data);
        $ro_amount_values = $this->mg_model->get_ro_amount($internal_ro);

        $nw_payout = 0;
        $ro_value_market = 0;

        foreach ($market_price as $mp) {
            $market_name = $mp['market'];
            $spot_market_price = $mp['spot_price'];
            $banner_market_price = $mp['banner_price'];

            $channel_detail = $this->mg_model->get_scheduled_channel_for_market($market_name, $internal_ro);

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
                $approved_nw_data = $this->mg_model->get_approved_data_for_customer_channel_ro(array('tv_channel_id' => $channel_id, 'internal_ro_number' => $internal_ro));

                //find scheduled fct for given time period
                $scheduled_impression = $this->get_scheduled_impression_for_time_period($channel_id, $internal_ro, $given_start_date, $given_end_date, $market_id);
                $total_scheduled_seconds = $this->get_total_scheduled_seconds($internal_ro, $channel_id, $market_id);

                if (($scheduled_impression['spot_ad_seconds'] == 0 && $scheduled_impression['banner_ad_seconds'] == 0) || (!isset($approved_nw_data[0]['total_spot_ad_seconds']) && !isset($approved_nw_data[0]['total_banner_ad_seconds']))) {
                    continue;
                } else {
                    $scheduled_spot_seconds = $scheduled_spot_seconds + $scheduled_impression['spot_ad_seconds'];
                    $scheduled_banner_seconds = $scheduled_banner_seconds + $scheduled_impression['banner_ad_seconds'];

                    $total_spot_second = $total_spot_second + $total_scheduled_seconds['spot_ad_seconds'];
                    $total_banner_second = $total_banner_second + $total_scheduled_seconds['banner_ad_seconds'];

                    //$spot_impression_fraction = $scheduled_impression['spot_ad_seconds']/$total_spot_second ;
                    //$banner_impression_fraction = $scheduled_impression['banner_ad_seconds']/$total_banner_second ;

                    //$total_spot_fraction = $total_spot_fraction + $spot_impression_fraction ;
                    //$total_banner_fraction = $total_banner_fraction + $banner_impression_fraction ;

                    $channel_payout = ($scheduled_impression['spot_ad_seconds'] * $approved_nw_data[0]['channel_spot_avg_rate'] * ($approved_nw_data[0]['customer_share'] / 100)) / 10 + ($scheduled_impression['banner_ad_seconds'] * $approved_nw_data[0]['channel_banner_avg_rate'] * ($approved_nw_data[0]['customer_share'] / 100)) / 10;
                    $total_channel_payout += $channel_payout;
                }
            }

            $average_spot_fraction = $scheduled_spot_seconds / $total_spot_second;
            $average_banner_fraction = $scheduled_banner_seconds / $total_banner_second;

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
        $other_expenses_ary = $this->mg_model->get_external_ro_report_details($internal_ro);
        $other_expenses = $other_expenses_ary[0]['other_expenses'] * $ro_fraction;

        $actual_net_amount = $ro_value_market - $agency_commission - $agency_rebate - $other_expenses;

        $result['total_sw_net_contribution'] = $actual_net_amount - $nw_payout;

        return $result;
    }

    public function get_scheduled_impression_for_time_period($channel_id, $internal_ro, $start_date, $end_date, $market_id = 0)
    {
        $campaignIds = $this->getScheduledCampaignIds($internal_ro, $channel_id, $market_id);

        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id,min(date) as start_date,max(date) as end_date "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaignIds) and acsd.screen_region_id in(1,3) and acsd.status ='scheduled' and "
            . "(acsd.date >= '$start_date' and acsd.date <='$end_date' ) ";

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

            if (isset($value['start_date']) || !isset($data['start_date'])) {
                $data['start_date'] = substr($value['start_date'], 0, 11);
            } else {
                if (strtotime($value['start_date']) > strtotime($data['start_date'])) {
                    $data['start_date'] = substr($value['start_date'], 0, 11);
                }
            }
            if (isset($value['end_date']) || !isset($data['end_date'])) {
                $data['end_date'] = substr($value['end_date'], 0, 11);
            } else {
                if (strtotime($value['end_date']) > strtotime($data['end_date'])) {
                    $data['end_date'] = substr($value['end_date'], 0, 11);
                }
            }
        }
        return $data;
    }

    public function getScheduledCampaignIds($internal_ro, $channel_id, $market_id)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                    where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                    AND internal_ro_number = '$internal_ro'  AND is_make_good =0 and channel_id = '$channel_id'";
        if (isset($market_id) && !empty($market_id)) {
            $query .= " and ac.market_id='$market_id' ";
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

    public function getMonthlyMisReport($data)
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

    public function current_financial_email_report_details_v1($givenDate)
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
            $am_query = "select am.internal_ro,am.cust_ro,am.id from ro_am_external_ro as am, ro_external_ro_report_details as nr where am.internal_ro=nr.internal_ro_number and test_user_creation=0";
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
        $monthWiseRoReport = $this->mg_model->getMonthWiseRoReportSummation($financialYear);
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
}
/* End of file Menu_model.php */
/* Location: ./application/models/Menu_model.php */