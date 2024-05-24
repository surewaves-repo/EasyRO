<?php

class Reports
{

    function Reports($db)
    {
        $this->db = $db;
        $this->campaigns = array();
        $this->contents = array();
        $this->screens = array();
    }

    function get_monthly_report_for_advertiser($advertiser_id, $start_date, $end_date, $str_campaign_ids = '', $str_screen_ids = '')
    {
        global $c_node;
        $result = $this->get_reports_for_advertiser($advertiser_id, $start_date, $end_date, " schedule_date DESC , start_time DESC", $str_campaign_ids, $str_screen_ids);
        foreach ($result as &$report) {
            if (!isset($this->campaigns[$report['campaign_id']])) {
                $query = "select campaign_name,selected_region_id,agency_name,campaign_type from sv_advertiser_campaign where campaign_id = '" . $report['campaign_id'] . "'";
                $temp_result = Utility::getQueryResult($this->db, $query);
                if (isset($temp_result[0]) && isset($temp_result[0]['campaign_name'])) {
                    $report['campaign_name'] = $temp_result[0]['campaign_name'];
                    $report['agency_name'] = $temp_result[0]['agency_name'];
                    $report['campaign_type'] = $temp_result[0]['campaign_type'];
                }
                if (isset($temp_result[0]) && isset($temp_result[0]['selected_region_id']) && $temp_result[0]['selected_region_id'] != 0) {
                    $query = "select screen_region_name from sv_screen_region where screen_region_id = " . $temp_result[0]['selected_region_id'];
                    $id = Utility::getSingleRowQueryResult($this->db, $query);
                    $report['region_name'] = $id['screen_region_name'];
                }
                $this->campaigns[$report['campaign_id']] = $temp_result[0];
            } else {
                $report['campaign_name'] = $this->campaigns[$report['campaign_id']]['campaign_name'];
                $report['agency_name'] = $this->campaigns[$report['campaign_id']]['agency_name'];
                $report['campaign_type'] = $this->campaigns[$report['campaign_id']]['campaign_type'];
                $query = "select screen_region_name from sv_screen_region where screen_region_id = " . $this->campaigns[$report['campaign_id']]['selected_region_id'];
                $id = Utility::getSingleRowQueryResult($this->db, $query);
                $report['region_name'] = $id['screen_region_name'];
            }

            if (!isset($this->screens[$report['screen_id']])) {
                $query = "select * from sv_screen where screen_id = '" . $report['screen_id'] . "'";
                $temp_result = Utility::getQueryResult($this->db, $query);
                $pieces = explode("_", $temp_result[0]['screen_name']);
                $count = count($pieces);
                $name = '';
                for ($i = 0; $i < ($count - 2); $i++)
                    $name .= $pieces[$i];
                $temp_result[0]['screen_name'] = $name;
                $temp_result[0]['channel'] = $pieces[$count - 2];
                $temp_result[0]['station'] = $pieces[$count - 1];

                if (isset($temp_result[0]) && isset($temp_result[0]['screen_name'])) {
                    $report['screen_name'] = $temp_result[0]['screen_name'];
                    $report['channel'] = $temp_result[0]['channel'];
                    $report['station'] = $temp_result[0]['station'];
                    $report['channel_id'] = $temp_result[0]['channel_id'];
                    $report['station_id'] = $temp_result[0]['station_id'];
                    $this->screens[$report['screen_id']] = $temp_result[0];
                }
            } else {
                $temp_result = $this->screens[$report['screen_id']];
                $report['screen_name'] = $temp_result['screen_name'];
                $report['channel'] = $temp_result['channel'];
                $report['station'] = $temp_result['station'];
                $report['station_id'] = $temp_result['station_id'];
                $report['channel_id'] = $temp_result['channel_id'];
            }

            if (!isset($this->contents[$report['content_id']])) {
                $query = "select content_name,thumb_url from sv_advertiser_content where content_id = '" . $report['content_id'] . "'";
                $temp_result = Utility::getQueryResult($this->db, $query);
                if (isset($temp_result[0]) && isset($temp_result[0]['content_name'])) {
                    $report['content_name'] = $temp_result[0]['content_name'];
                    $report['thumb_url'] = $temp_result[0]['thumb_url'];
                    $this->contents[$report['content_id']] = $temp_result[0];
                }
            } else {
                $temp_result = $this->contents[$report['content_id']];
                $report['content_name'] = $temp_result['content_name'];
                $report['thumb_url'] = $temp_result['thumb_url'];
            }

            if (isset($report['time_taken']) && $report['time_taken'] != "0000-00-00 00:00:00") {
                //$report['ss_url'] = $temp_result[0]['thumb_url'];
                $filename = "ad_" . $report['campaign_id'] . '_' . $report['group_id'] . '_' . $report['content_id'] . ".png";
                $report['ss_url'] = "http://" . $c_node['host_name'] . "/surewaves/content/ScreenShots/" . $filename;
            }

            $report['date'] = date("d-m-Y", strtotime($report['schedule_date']));
            $report['start_time'] = date("H:i:s", strtotime($report['start_time']));
            $report['end_time'] = date("H:i:s", strtotime($report['end_time']));

            if ($report['content_played'] === 'yes')
                $report['played'] = 'Yes';
            else
                $report['played'] = 'No';

            if ($report['play_completion'] === 'yes')
                $report['completed'] = 'Yes';
            else
                $report['completed'] = 'No';
        }
        unset($report);
        return $result;
    }

    function get_reports_for_advertiser($advertiser_id, $start_date, $end_date, $orderby = '', $str_campaign_ids = '', $str_screen_ids = '')
    {
        Zend_Log::log('In ' . __FILE__ . '::' . __CLASS__ . '::' . __FUNCTION__, Zend_Log::LEVEL_DEBUG);
        global $c_node;

        $query = "select * from sv_advertiser where advertiser_id = $advertiser_id";
        $advertiser = Utility::getQueryResult($this->db, $query);

        $str_advertisers = " advertiser_id = '$advertiser_id' ";
        if ($advertiser[0]['enterprise_id'] != 0 && $advertiser[0]['enterprise_administrator'] == '1') {
            $query = "select distinct advertiser_id from sv_advertiser_network where customer_id = " . $advertiser[0]['enterprise_id'];
            $ads = Utility::getQueryResult($this->db, $query);
            $advertisers = array();
            foreach ($ads as $ad) {
                array_push($advertisers, $ad['advertiser_id']);
            }
            $str_advertisers = "'" . implode("','", $advertisers) . "'";
            $str_advertisers = " advertiser_id IN ($str_advertisers) ";
        }
        if ($advertiser[0]['is_agency'] == '1') {
            $str_advertisers = '1';
        }

        $start_datetime = $start_date . " 00:00:00";
        $end_datetime = $end_date . " 23:59:59";
        $query = "select count(*) as total_records from sv_advertiser_report where $str_advertisers "
            /*					." and content_played = 'yes'"
                                ." and play_completion = 'yes'"*/
            . " and start_time >='" . $start_datetime . "'"
            . " and end_time <='" . $end_datetime . "'"
            . " and within_valid_hours = '1'";
        if (!empty($str_campaign_ids)) {
            $query = $query . " and campaign_id IN ( $str_campaign_ids ) ";
        } else if ($advertiser[0]['is_agency'] == '1' && !empty($advertiser[0]['agency_name'])) {
            $query = $query . " and campaign_id IN ( Select campaign_id from sv_advertiser_campaign where agency_name LIKE '%" . $advertiser[0]['agency_name'] . "%' ) ";
        }
        if (!empty($str_screen_ids))
            $query = $query . " and screen_id IN ( $str_screen_ids ) ";
        if ($orderby != '')
            $query .= " order by " . $orderby;
        $result = Utility::getQueryResult($this->db, $query);
        Zend_Log::log('In ' . __FILE__ . '::' . __CLASS__ . '::' . __FUNCTION__ . ':: Query - ' . $query, Zend_Log::LEVEL_DEBUG);
        $total_records = $result[0]['total_records'];
        $this->total_pages = (int)($total_records / $c_node['advertiser']['display_pop_reports_count']);
        if ($total_records % $c_node['advertiser']['display_pop_reports_count'] != 0)
            $this->total_pages++;
        if ($this->total_pages == 0)
            $this->total_pages = 1;
        $query = str_replace("count(*) as total_records", "*", $query);
        if (isset($this->page)) {
            $starts = ($this->page - 1) * $c_node['advertiser']['display_pop_reports_count'];
            $query .= " limit " . $starts . "," . $c_node['advertiser']['display_pop_reports_count'];
        }
        $result = Utility::getQueryResult($this->db, $query);

        /* $query = "select screen_id,group_id from sv_screen";
        $screen_result = Utility::getQueryResult($this->db, $query);
        $map_screen_group = array();
        foreach($screen_result as $screen)
        {
            $map_screen_group[$screen['screen_id']] = $screen['group_id'];
        }
        foreach($result as &$row)
        {
            $row['group_id'] = $map_screen_group[$row['screen_id']];
        }*/

        return $result;
    }

    function get_creative_report_for_advertiser($advertiser_id, $start_date, $end_date, $str_campaign_ids = '')
    {
        $start_datetime = $start_date . " 00:00:00";
        $end_datetime = $end_date . " 23:59:59";
        if ($_REQUEST['frequency'] == 'summary') {
            $query = "select campaign_id,content_id,screen_id,count(*) as playbacks,sum(locked_price) as price from sv_advertiser_report where advertiser_id = '" . $advertiser_id . "'"
                . " and content_played = 'yes'"
                . " and play_completion = 'yes'"
                . " and start_time >='" . $start_datetime . "'"
                . " and end_time <='" . $end_datetime . "'"
                . " and within_valid_hours = '1'";
            if (!empty($str_campaign_ids))
                $query = $query . " and campaign_id IN ( $str_campaign_ids ) ";
            $query = $query . " group by content_id,screen_id ";
        } else {
            $query = "select campaign_id,schedule_date,content_id,screen_id,count(*) as playbacks,sum(locked_price) as price from sv_advertiser_report where advertiser_id = '" . $advertiser_id . "'"
                . " and content_played = 'yes'"
                . " and play_completion = 'yes'"
                . " and start_time >='" . $start_datetime . "'"
                . " and end_time <='" . $end_datetime . "'"
                . " and within_valid_hours = '1'";
            if (!empty($str_campaign_ids))
                $query = $query . " and campaign_id IN ( $str_campaign_ids ) ";
            $query = $query . " group by schedule_date,content_id,screen_id ";
        }
        Zend_Log::log('In ' . __FILE__ . '::' . __CLASS__ . '::' . __FUNCTION__ . ':: Query - ' . $query, Zend_Log::LEVEL_DEBUG);
        $result = Utility::getQueryResult($this->db, $query);
        foreach ($result as &$report) {
            if (!isset($this->contents[$report['content_id']])) {
                $query = "select content_name,thumb_url from sv_advertiser_content where content_id = '" . $report['content_id'] . "'";
                $temp_result = Utility::getQueryResult($this->db, $query);
                if (isset($temp_result[0]) && isset($temp_result[0]['content_name'])) {
                    $report['content_name'] = $temp_result[0]['content_name'];
                    $report['thumb_url'] = $temp_result[0]['thumb_url'];
                    $this->contents[$report['content_id']] = $temp_result[0];
                }
            } else {
                $temp_result = $this->contents[$report['content_id']];
                $report['content_name'] = $temp_result['content_name'];
                $report['thumb_url'] = $temp_result['thumb_url'];
            }

            if (!isset($this->screens[$report['screen_id']])) {
                $query = "select * from sv_screen where screen_id = '" . $report['screen_id'] . "'";
                $temp_result = Utility::getQueryResult($this->db, $query);
                if (isset($temp_result[0]) && isset($temp_result[0]['screen_name'])) {
                    $report['screen_name'] = $temp_result[0]['screen_name'];
                    $report['city'] = $temp_result[0]['citi'];
                    $report['location'] = $temp_result[0]['location'];
                    $this->screens[$report['screen_id']] = $temp_result[0];
                }
            } else {
                $temp_result = $this->screens[$report['screen_id']];
                $report['screen_name'] = $temp_result['screen_name'];
                $report['city'] = $temp_result['citi'];
                $report['location'] = $temp_result['location'];
            }
            $pieces = explode("_", $report['screen_name']);
            $count = count($pieces);
            $name = '';
            for ($i = 0; $i < ($count - 2); $i++)
                $name .= $pieces[$i];
            $report['screen_name'] = $name;
            $report['channel'] = $pieces[$count - 2];
            $report['station'] = $pieces[$count - 1];

            if (!isset($this->campaigns[$report['campaign_id']])) {
                $query = "select campaign_name from sv_advertiser_campaign where campaign_id = '" . $report['campaign_id'] . "'";
                $temp_result = Utility::getQueryResult($this->db, $query);
                if (isset($temp_result[0]) && isset($temp_result[0]['campaign_name'])) {
                    $report['campaign_name'] = $temp_result[0]['campaign_name'];
                }
                $this->campaigns[$report['campaign_id']] = $temp_result[0];
            } else {
                $report['campaign_name'] = $this->campaigns[$report['campaign_id']]['campaign_name'];
            }

            if ($_REQUEST['frequency'] == 'daily')
                $report['date'] = date("d-m-Y", strtotime($report['schedule_date']));
        }
        return $result;
    }

    function get_campaign_report_for_advertiser($advertiser_id, $start_date, $end_date)
    {
        $start_datetime = $start_date . " 00:00:00";
        $end_datetime = $end_date . " 23:59:59";
        $query = "select campaign_id,count(*) as playbacks,count(distinct screen_id) as screens,count(distinct schedule_date) as days,sum(locked_price) as price from sv_advertiser_report where advertiser_id = '" . $advertiser_id . "'"
            . " and content_played = 'yes'"
            . " and play_completion = 'yes'"
            . " and start_time >='" . $start_datetime . "'"
            . " and end_time <='" . $end_datetime . "'"
            . " and within_valid_hours = '1'"
            . " group by campaign_id ";
        $result = Utility::getQueryResult($this->db, $query);
        foreach ($result as &$report) {
            if (!isset($this->campaigns[$report['campaign_id']])) {
                $query = "select * from sv_advertiser_campaign where campaign_id = '" . $report['campaign_id'] . "'";
                $temp_result = Utility::getQueryResult($this->db, $query);
                if (isset($temp_result[0]) && isset($temp_result[0]['campaign_name'])) {
                    $report['campaign_name'] = $temp_result[0]['campaign_name'];
                    $report['start_date'] = date("d-m-Y", strtotime($temp_result[0]['start_date']));
                    $report['end_date'] = date("d-m-Y", strtotime($temp_result[0]['end_date']));
                    $this->campaigns[$report['campaign_id']] = $temp_result[0];
                }
            } else {
                $report['campaign_name'] = $this->campaigns[$report['campaign_id']]['campaign_name'];
                $report['start_date'] = date("d-m-Y", strtotime($this->campaigns[$report['campaign_id']]['start_date']));
                $report['end_date'] = date("d-m-Y", strtotime($this->campaigns[$report['campaign_id']]['end_date']));
            }
        }
        return $result;
    }


    function get_creative_report_for_advertiser_old($advertiser_id, $start_date, $end_date)
    {
        $frequency = $_REQUEST['frequency'];
        if ($frequency == 'daily')
            $result = $this->get_reports_for_advertiser($advertiser_id, $start_date, $end_date, "schedule_date,content_id,screen_id,start_time");
        else
            $result = $this->get_reports_for_advertiser($advertiser_id, $start_date, $end_date, "content_id,screen_id");

        $creatives = array();
        $last_content_id = 0;
        $last_screen_id = 0;
        $last_date = date("Y-m-d H:i:s", strtotime($start_date) - 24 * 60 * 60);
        if (count($result) > 0) {
            array_push($result, array());
        }
        foreach ($result as &$report) {
            if ($frequency == 'daily')
                $date_check = idate("d", strtotime($last_date)) != idate("d", strtotime($report['schedule_date']));
            else
                $date_check = 0;

            if (isset($report['content_id'])
                && ($last_content_id != $report['content_id']
                    || $last_screen_id != $report['screen_id']
                    || $date_check)) {
                if (!isset($this->campaigns[$report['campaign_id']])) {
                    $query = "select campaign_name from sv_advertiser_campaign where campaign_id = '" . $report['campaign_id'] . "'";
                    $temp_result = Utility::getQueryResult($this->db, $query);
                    if (isset($temp_result[0]) && isset($temp_result[0]['campaign_name'])) {
                        $report['campaign_name'] = $temp_result[0]['campaign_name'];
                    }
                    $this->campaigns[$report['campaign_id']] = $temp_result[0];
                } else {
                    $report['campaign_name'] = $this->campaigns[$report['campaign_id']]['campaign_name'];
                }

                if (!isset($this->screens[$report['screen_id']])) {
                    $query = "select * from sv_screen where screen_id = '" . $report['screen_id'] . "'";
                    $temp_result = Utility::getQueryResult($this->db, $query);
                    if (isset($temp_result[0]) && isset($temp_result[0]['screen_name'])) {
                        $report['screen_name'] = $temp_result[0]['screen_name'];
                        $report['city'] = $temp_result[0]['citi'];
                        $report['location'] = $temp_result[0]['location'];
                        $this->screens[$report['screen_id']] = $temp_result[0];
                    }
                } else {
                    $temp_result = $this->screens[$report['screen_id']];
                    $report['screen_name'] = $temp_result['screen_name'];
                    $report['city'] = $temp_result['citi'];
                    $report['location'] = $temp_result['location'];
                }
                /*
                                $query = "select screen_region_name from sv_screen_region where screen_region_id = '".$report['screen_region_id']."'";
                                $temp_result = Utility::getQueryResult($this->db, $query);
                                if(isset($temp_result[0]) && isset($temp_result[0]['screen_region_name']))
                                {
                                    $report['screen_region_name'] = $temp_result[0]['screen_region_name'];
                                }
                */

                if (!isset($this->contents[$report['content_id']])) {
                    $query = "select content_name,thumb_url from sv_advertiser_content where content_id = '" . $report['content_id'] . "'";
                    $temp_result = Utility::getQueryResult($this->db, $query);
                    if (isset($temp_result[0]) && isset($temp_result[0]['content_name'])) {
                        $report['content_name'] = $temp_result[0]['content_name'];
                        $report['thumb_url'] = $temp_result[0]['thumb_url'];
                        $this->contents[$report['content_id']] = $temp_result[0];
                    }
                } else {
                    $temp_result = $this->contents[$report['content_id']];
                    $report['content_name'] = $temp_result['content_name'];
                    $report['thumb_url'] = $temp_result['thumb_url'];
                }

                if ($frequency == 'daily')
                    $report['date'] = date("d-m-Y", strtotime($report['schedule_date']));
                $report['playbacks'] = 1;
                $report['price'] = number_format($report['locked_price'], 2, '.', '');

                $last_content_id = $report['content_id'];
                $last_screen_id = $report['screen_id'];
                $last_date = $report['schedule_date'];
                array_push($creatives, $report);
                $last_report = &$creatives[count($creatives) - 1];
            } else {
                if (isset($report['content_id'])) {
                    $last_report['playbacks']++;
                    $last_report['price'] += number_format($report['locked_price'], 2, '.', '');
                }
            }
        }
        return $creatives;
    }

    function get_details_report_for_advertiser($from_date, $to_date, $campaign_id = null)
    {
        //group_concat(distinct screen_id order by screen_id separator ',') as screen_costs
        $networks = array();

        if ($campaign_id == null) {
            $campaign_str = '';
            $advertiser_str = " and advertiser_id = '" . $_SESSION['advertiser_id'] . "' ";
        } else {
            $campaign_str = " campaign_id = '" . $campaign_id . "' and ";
            $advertiser_str = "";
        }

        $query = "select customer_id,screen_id,campaign_id,count(*) as playbacks,sum(locked_price) as price from sv_advertiser_report where " . $campaign_str
            . " content_played = 'yes'"
            . " and play_completion = 'yes'"
            . " and schedule_date >= '" . $from_date . " 00:00:00'"
            . " and schedule_date <= '" . $to_date . " 00:00:00'"
            . " and play_completion = 'yes'"
            . " and within_valid_hours = '1'"
            . $advertiser_str
            . " group by customer_id,screen_id,campaign_id order by customer_id, playbacks DESC ";
        $result = Utility::getQueryResult($this->db, $query);
        Zend_Log::log('In ' . __FILE__ . '::' . __CLASS__ . '::' . __FUNCTION__ . ':: Query - ' . $query, Zend_Log::LEVEL_DEBUG);
        $networks = $this->split_array($result, "customer_id", "screens");

        /* ajay pending enhancements
                $campaign_ids = array();
                $query = "select * from sv_advertiser_campaign where start_date <= '".$to_date . " 00:00:00' and end_date >='" . $from_date." 00:00:00' and advertiser_id='" . $_SESSION['advertiser_id'] . "'";
                $result = Utility::getQueryResult($this->db, $query);
                foreach($result as $row)
                {
                    array_push($campaign_ids,$row['campaign_id']);
                }
                $str_campaign_ids = implode("','",$campaign_ids);
                $str_campaign_ids = "'".$str_campaign_ids."'";

        */
        foreach ($networks as &$network) {
            /* ajay pending enhancements
                        $query = "select screen_id,campaign_id from sv_advertiser_campaign_screens where campaign_id IN ( $str_campaign_ids ) and enterprise_id='".$network['customer_id']."'";
                        $result = Utility::getQueryResult($this->db, $query);

                        $not_added_screens = array();
                        $added_screens = array();
                        foreach($network['screens'] as $screens)
                        {
                            foreach($screens as $screen)
                            {
                                $added_screens[$screen['screen_id']] = 1;
                            }
                        }

                        $screen_campaign_ids = array();
                        foreach($result as $row)
                        {
                            if(!isset($added_screens[$row['screen_id']]))
                            {
                                array_push($not_added_screens,$row['screen_id']);
                                if(!isset($screen_campaign_ids[$row['screen_id']]))
                                    $screen_campaign_ids[$row['screen_id']] = array();
                                array_push($screen_campaign_ids[$row['screen_id']],$row['campaign_id']);
                            }
                        }

                        $not_added_screens = array_unique($not_added_screens);
                        $str_screen_ids = implode("','",$not_added_screens);
                        $str_screen_ids = "'".$str_screen_ids."'";
                        $query = "select * from sv_screen where screen_id IN ( $str_screen_ids ) ";
                        $result = Utility::getQueryResult($this->db, $query);
                        foreach($result as $new_screen)
                        {
                            $screens = array();
                            $screen = array();
                            $screen = $new_screen;
                            $screen['playbacks'] = 0;
                            $screen['price'] = 0;
                            $screen['campaign_playbacks'] = array();
                            foreach($screen_campaign_ids[$new_screen['screen_id']] as $campaign_id)
                            {
                                $screen['campaign_id'] = $campaign_id;
                                $screen['campaign_playbacks'][$campaign_id] = 0;
                                array_push($screens,$screen);
                            }
                            array_push($network['screens'],$screens);
                        }
            */

            $query = "select customer_name from sv_customer where customer_id = '" . $network['customer_id'] . "'";
            $result = $this->db->Execute($query);
            $network['customer_name'] = $result->fields['customer_name'];
            $network['screen_count'] = 0;
            $network['price'] = 0;
            $network['playbacks'] = 0;
            $network['actual_screens'] = array();
            foreach ($network['screens'] as $screen_id => $screens) {
                $actual_screen = array();
                $actual_screen['playbacks'] = 0;
                $actual_screen['price'] = 0;
                $actual_screen['campaign_playbacks'] = array();
                foreach ($screens as $screen) {
                    $actual_screen['customer_id'] = $screen['customer_id'];
                    $actual_screen['screen_id'] = $screen['screen_id'];
                    $actual_screen['playbacks'] += $screen['playbacks'];
                    $actual_screen['price'] += $screen['price'];
                    if (!isset($actual_screen['campaign_playbacks'][$screen['campaign_id']])) {
                        $actual_screen['campaign_playbacks'][$screen['campaign_id']] = $screen['playbacks'];
                    } else {
                        $actual_screen['campaign_playbacks'][$screen['campaign_id']] += $screen['playbacks'];
                    }
                }
                array_push($network['actual_screens'], $actual_screen);
            }
            foreach ($network['actual_screens'] as &$screen) {
                $query = "select * from sv_screen where screen_id = '" . $screen['screen_id'] . "'";
                Zend_Log::log('In ' . __FILE__ . '::' . __CLASS__ . '::' . __FUNCTION__ . ':: Query - ' . $query, Zend_Log::LEVEL_DEBUG);
                $temp_result = $this->db->Execute($query);
                if (isset($temp_result->fields['screen_name'])) {
                    $screen['screen_name'] = $temp_result->fields['screen_name'];
                    $screen['city'] = $temp_result->fields['citi'];
                    $screen['location'] = $temp_result->fields['location'];
                }
                $pieces = explode("_", $screen['screen_name']);
                $count = count($pieces);
                $name = '';
                for ($i = 0; $i < ($count - 2); $i++)
                    $name .= $pieces[$i];
                $screen['screen_name'] = $name;
                $screen['channel'] = $pieces[$count - 2];
                $screen['station'] = $pieces[$count - 1];

                //$network['price'] += number_format($screen['price'], 2, '.', '');
                $query = "select impressions from sv_advertiser_campaign_screens_dates where " . $campaign_str . " screen_id = '" . $screen['screen_id'] . "'";
                Zend_Log::log('In ' . __FILE__ . '::' . __CLASS__ . '::' . __FUNCTION__ . ':: Query - ' . $query, Zend_Log::LEVEL_DEBUG);
                $temp_result = Utility::getQueryResult($this->db, $query);
                foreach ($temp_result as $temp) {
                    $network['impressions'] = $temp['impressions'];
                }
                $network['playbacks'] += $screen['playbacks'];
                $network['screen_count']++;
            }
            unset($screen);
        }
        unset($network);
        return $networks;
    }

    function split_array($input_array, $key, $inner_key1)
    {
        $output_array = array();
        foreach ($input_array as $element) {
            $screen_id = $element['screen_id'];
            if ($last_value != $element[$key]) {
                $output_array[count($output_array)] = array();
                $output_array[count($output_array) - 1][$key] = $element[$key];
                $output_array[count($output_array) - 1][$inner_key1] = array();
                $output_array[count($output_array) - 1][$inner_key1][$screen_id] = array();
                array_push($output_array[count($output_array) - 1][$inner_key1][$screen_id], $element);
                $last_value = $element[$key];
            } else {
                if (!isset($output_array[count($output_array) - 1][$inner_key1][$screen_id])) {
                    $output_array[count($output_array) - 1][$inner_key1][$screen_id] = array();
                }
                array_push($output_array[count($output_array) - 1][$inner_key1][$screen_id], $element);
            }
        }
        Zend_Log::log('In ' . __FILE__ . '::' . __CLASS__ . '::' . __FUNCTION__ . 'Output array: ' . print_r($output_array, true), Zend_Log::LEVEL_DEBUG);
        return $output_array;
    }

    function get_ro_report($ro_type,
                           $ro_number,
                           $client_name)
    {
        global $cacheMapObj;
        $summary = array();
        if ($_SESSION['advertiser_type'] == 'enterprise') {
            $query = "select campaign_id, client_name, start_date, end_date, internal_ro_number, customer_ro_number from sv_advertiser_campaign where  advertiser_id = '" . $_SESSION['advertiser_id'] . "'";
        }
        if ($_SESSION['advertiser_type'] == 'advertiser') {
            $query = "select campaign_id, client_name, start_date, end_date, internal_ro_number, customer_ro_number from sv_advertiser_campaign where 1 ";
        }
        if ($ro_type == 'internal_ro') {
            $query .= " and `internal_ro_number` = '$ro_number' ";
        } else {
            $query .= " and `customer_ro_number` = '$ro_number' ";
            $query .= " and `client_name` = '$client_name' ";
        }

        $result = Utility::getQueryResult($this->db, $query);
        if (isset($result[0]) && !empty($result[0])) {
            $summary['client_name'] = $result[0]['client_name'];
            $summary['start_date'] = $result[0]['start_date'];
            $summary['end_date'] = $result[0]['end_date'];
            $summary['internal_ro_number'] = $result[0]['internal_ro_number'];
            $summary['customer_ro_number'] = $result[0]['customer_ro_number'];
        }

        $campaign_ids = array();
        foreach ($result as $campaign) {
            array_push($campaign_ids, $campaign['campaign_id']);
        }
        $str_campaign_ids = "'" . implode("','", $campaign_ids) . "'";
        $campaign_str = " and `campaign_id` IN ($str_campaign_ids) ";

        $advertiser_str = " advertiser_id = '" . $_SESSION['advertiser_id'] . "' ";

        if ($_SESSION['advertiser_type'] == 'enterprise') {
            $query = "select customer_id,screen_id,campaign_id,content_id, schedule_date,count(*) as playbacks from sv_advertiser_report where "
                . $advertiser_str
                . $campaign_str
                . " and content_played = 'yes'"
                . " and play_completion = 'yes'"
                . " and within_valid_hours = '1'"
                . " group by schedule_date,customer_id,campaign_id,screen_id order by schedule_date asc,customer_id asc,screen_id asc";
        }
        if ($_SESSION['advertiser_type'] == 'advertiser') {
            $query = "select customer_id,screen_id,campaign_id,content_id, schedule_date,count(*) as playbacks from sv_advertiser_report where 1"
                . $campaign_str
                . " and content_played = 'yes'"
                . " and play_completion = 'yes'"
                . " and within_valid_hours = '1'"
                . " group by schedule_date,customer_id,campaign_id,screen_id order by schedule_date asc,customer_id asc,screen_id asc";
        }
        $result = Utility::getQueryResult($this->db, $query);

        $records = array();
        foreach ($result as $report) {
            $record = array();
            $record = $report;
            $customer = $cacheMapObj->getValue("Customer", $report['customer_id']);
            $screen = $cacheMapObj->getValue("Screen", $report['screen_id']);
            $program_id = $screen['program_id'];
            $station_id = $screen['station_id'];
            $program = $cacheMapObj->getValue("Program", $program_id);
            $channel_id = $program['channel_id'];
            $station = $cacheMapObj->getValue("Station", $station_id);
            $channel = $cacheMapObj->getValue("Channel", $channel_id);
            $campaign = $cacheMapObj->getValue("Campaign", $report['campaign_id']);

            $region = $cacheMapObj->getValue("ScreenRegion", $campaign['selected_region_id']);

            $content = $cacheMapObj->getValue("AdvertiserContent", $report['content_id']);
            $tag = $cacheMapObj->getValue("Tags", $report['content_id']);

            $record['publisher'] = $customer['customer_name'];
            $record['channel'] = $channel['channel_name'];
            $record['channel_logo'] = $channel['logo'];

            $record['content_name'] = $content['content_name'];

            $record['tape_id'] = $tag['tape_id'];
            $record['caption_name'] = $tag['caption_name'];

            $record['region'] = $region['screen_region_name'];

            $record['program'] = $program['pu_name'];
            $record['program_start_time'] = $program['start_time'];
            $record['program_end_time'] = $program['end_time'];

            $record['station'] = $station['station_name'];

            $record['seconds'] = $report['playbacks'] * $campaign['duration_to_play'];
            if (!isset($campaign['brand_owner_id']) || empty($campaign['brand_owner_id'])) {
                $record['brand_owner_id'] = $campaign['brand_owner_new'];
                $record['brand_id'] = $campaign['brand_new'];
                $record['product_id'] = $campaign['product_new'];
            } else {
                $record['brand_owner_id'] = $campaign['brand_owner_id'];
                $record['brand_id'] = $campaign['brand_id'];
                $record['product_id'] = $campaign['product_id'];
            }

            array_push($records, $record);
        }
        $schedule_date = array();
        $publisher = array();
        $channel = array();
        $region = array();
        $program = array();
        $program_start_time = array();
        $station = array();
        $content_name = array();
        // Obtain a list of columns
        foreach ($records as $key => $row) {
            $schedule_date[$key] = $row['schedule_date'];
            $publisher[$key] = $row['publisher'];
            $channel[$key] = $row['channel'];
            $region[$key] = $row['region'];
            $program[$key] = $row['program'];
            $program_start_time[$key] = $row['program_start_time'];
            $station[$key] = $row['station'];
            $content_name[$key] = $row['content_name'];
        }

        // Sort the data with volume descending, edition ascending
        // Add $data as the last parameter, to sort by the common key
        array_multisort($schedule_date, SORT_ASC,
            $publisher, SORT_ASC,
            $channel, SORT_ASC,
            $region, SORT_ASC,
            $program_start_time, SORT_ASC,
            $station, SORT_ASC,
            $records);

        $output = array();
        $output['impressions_records'] = $records;

        //Sort by content name to display content based summary report
        array_multisort(
            $content_name, SORT_ASC,
            $schedule_date, SORT_ASC,
            $publisher, SORT_ASC,
            $channel, SORT_ASC,
            $region, SORT_ASC,
            $program_start_time, SORT_ASC,
            $station, SORT_ASC,
            $records);

        $seconds_records = array();
        $region_val = array();
        foreach ($records as $record) {
            $brand_key = $record['brand_owner_id'] . "_" . $record['brand_id'] . "_" . $record['product_id'];
            if (!isset($seconds_records[$brand_key])) {
                $seconds_records[$brand_key] = array();
                $brandOwner = $cacheMapObj->getValue("BrandOwner", $record['brand_owner_id']);
                $seconds_records[$brand_key]['brand_owner_name'] = $brandOwner['brand_owner_name'];
                $brand = $cacheMapObj->getValue("Brand", $record['brand_id']);
                $seconds_records[$brand_key]['brand_name'] = $brand['brand_name'];
                $product = $cacheMapObj->getValue("Product", $record['product_id']);
                $seconds_records[$brand_key]['product_name'] = $product['product_name'];
                $seconds_records[$brand_key]['contents'] = array();
            }
            if (!isset($seconds_records[$brand_key]['contents'][$record['content_name']])) {
                $seconds_records[$brand_key]['contents'][$record['content_name']] = array();
                $seconds_records[$brand_key]['contents'][$record['content_name']]['channel'] = $record['channel'];
                $seconds_records[$brand_key]['contents'][$record['content_name']]['tape_id'] = $record['tape_id'];
                $seconds_records[$brand_key]['contents'][$record['content_name']]['caption_name'] = $record['caption_name'];
                $channel_logo_path = parse_url($record['channel_logo']);
                if (!empty($channel_logo_path['path'])) {
                    $channel_logo_url = $_SERVER['DOCUMENT_ROOT'] . $channel_logo_path['path'];
                } else {
                    $channel_logo_url = $_SERVER['DOCUMENT_ROOT'] . "/surewaves/advertiser/view/imgs/slogo.png";
                }
                $seconds_records[$brand_key]['contents'][$record['content_name']]['channel_logo'] = $channel_logo_url;
                $seconds_records[$brand_key]['contents'][$record['content_name']]['publisher'] = $record['publisher'];
                $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'] = array();
                $seconds_records[$brand_key]['contents'][$record['content_name']]['sub_total'] = 0;
            }
            if (!isset($seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']])) {
                $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']] = array();
                $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']]['name'] = $record['region'];
                $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']]['total'] = 0;
            }
            $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']]['total'] += $record['seconds'];
            $seconds_records[$brand_key]['contents'][$record['content_name']]['sub_total'] += $record['seconds'];

            //changed by mani for adding total secodage for respective region
            if (isset($region_val[$record['region']]) || !empty($region_val[$record['region']])) {
                $region_val[$record['region']] = $region_val[$record['region']] + $record['seconds'];
            } else {
                $region_val[$record['region']] = $record['seconds'];
            }
        }
        $output['seconds_records'] = $seconds_records;
        $output['summary'] = $summary;
        $output['region_val'] = $region_val;
        return $output;
    }

    function get_future_ro_report($start_date, $end_date, $ro_type, $ro_number, $client_name)
    {
        global $cacheMapObj;
        $start_date = str_replace("/", "-", $start_date);
        $end_date = str_replace("/", "-", $end_date);

        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        $summary = array();
        if ($_SESSION['advertiser_type'] == 'enterprise') {
            $query = "select campaign_id, client_name, start_date, end_date, internal_ro_number, customer_ro_number from sv_advertiser_campaign where  advertiser_id = '" . $_SESSION['advertiser_id'] . "'";
        }
        if ($_SESSION['advertiser_type'] == 'advertiser') {
            $query = "select campaign_id, client_name, start_date, end_date, internal_ro_number, customer_ro_number from sv_advertiser_campaign where 1 ";
        }
        if ($ro_type == 'internal_ro') {
            $query .= " and `internal_ro_number` = '$ro_number' ";
        } else {
            $query .= " and `customer_ro_number` = '$ro_number' ";
            $query .= " and `client_name` = '$client_name' ";
        }
        $query .= "and ((start_date >='" . $start_date . "' and start_date <='" . $end_date . "') or ( end_date >='" . $start_date . "' and end_date <='" . $end_date . "'))";
        $result = Utility::getQueryResult($this->db, $query);
        if (isset($result[0]) && !empty($result[0])) {
            $summary['client_name'] = $result[0]['client_name'];
            $summary['start_date'] = $result[0]['start_date'];
            $summary['end_date'] = $result[0]['end_date'];
            $summary['internal_ro_number'] = $result[0]['internal_ro_number'];
            $summary['customer_ro_number'] = $result[0]['customer_ro_number'];
        }

        $campaign_ids = array();
        foreach ($result as $campaign) {
            array_push($campaign_ids, $campaign['campaign_id']);
        }
        $str_campaign_ids = "'" . implode("','", $campaign_ids) . "'";
        $campaign_str = " and `campaign_id` IN ($str_campaign_ids) ";

        $advertiser_str = " advertiser_id = '" . $_SESSION['advertiser_id'] . "' ";

        if ($_SESSION['advertiser_type'] == 'enterprise') {
            $query = "select distinct group_id from sv_advertiser_campaign_screens_dates where 1 "
                . $campaign_str
                . " and (date >='" . $start_date . "' and date <='" . $end_date . "')"
                . " order by date asc,group_id asc";
        }
        if ($_SESSION['advertiser_type'] == 'advertiser') {
            $query = "select distinct group_id from sv_advertiser_campaign_screens_dates where 1"
                . $campaign_str
                . " and (date >='" . $start_date . "' and date <='" . $end_date . "')"
                . " order by date asc,group_id asc";
        }
        $result = Utility::getQueryResult($this->db, $query);
        $group_ids = array();
        foreach ($result as $gid) {
            array_push($group_ids, $gid['group_id']);
        }
        $group_ids = array_unique($group_ids);
        $str_group_ids = "'" . implode("','", $group_ids) . "'";

        $query = "select distinct loop_id from sv_loop_status where group_id in ($str_group_ids) and (date >='" . $start_date . "' and date <='" . $end_date . "')";
        $result = Utility::getQueryResult($this->db, $query);

        $loop_ids = array();
        foreach ($result as $lid) {
            array_push($loop_ids, $lid['loop_id']);
        }
        $loop_ids = array_unique($loop_ids);
        $str_loop_ids = "'" . implode("','", $loop_ids) . "'";

        $query = "select distinct content_id from sv_loop_contents where loop_id in ($str_loop_ids)";
        $result = Utility::getQueryResult($this->db, $query);

        $content_ids = array();
        foreach ($result as $con_id) {
            array_push($content_ids, $con_id['content_id']);
        }
        $content_ids = array_unique($content_ids);
        $str_content_ids = "'" . implode("','", $content_ids) . "'";
        $content_str = " and `content_id` IN ($str_campaign_ids) ";

        /*$query = "select * from sv_advertiser_content where content_id in ($str_content_ids)" ;
        $result = Utility::getQueryResult($this->db, $query); */

        if ($_SESSION['advertiser_type'] == 'enterprise') {
            $query = "select customer_id,screen_id,campaign_id,content_id, schedule_date,count(*) as playbacks from sv_advertiser_report where "
                . $advertiser_str
                . $content_str
                . " group by schedule_date,customer_id,campaign_id,screen_id order by schedule_date asc,customer_id asc,screen_id asc";
        }
        if ($_SESSION['advertiser_type'] == 'advertiser') {
            $query = "select customer_id,screen_id,campaign_id,content_id, schedule_date,count(*) as playbacks from sv_advertiser_report where 1"
                . $content_str
                . " group by schedule_date,customer_id,campaign_id,screen_id order by schedule_date asc,customer_id asc,screen_id asc";
        }
        $result = Utility::getQueryResult($this->db, $query);

        $records = array();
        foreach ($result as $report) {
            $record = array();
            $record = $report;

            $customer = $cacheMapObj->getValue("Customer", $report['customer_id']);
            $screen = $cacheMapObj->getValue("Screen", $report['screen_id']);
            $program_id = $screen['program_id'];
            $station_id = $screen['station_id'];
            $program = $cacheMapObj->getValue("Program", $program_id);
            $channel_id = $program['channel_id'];
            $station = $cacheMapObj->getValue("Station", $station_id);
            $channel = $cacheMapObj->getValue("Channel", $channel_id);
            $campaign = $cacheMapObj->getValue("Campaign", $report['campaign_id']);

            $region = $cacheMapObj->getValue("ScreenRegion", $campaign['selected_region_id']);

            $content = $cacheMapObj->getValue("AdvertiserContent", $report['content_id']);
            $tag = $cacheMapObj->getValue("Tags", $report['content_id']);

            $record['publisher'] = $customer['customer_name'];

            $program_id = $screen['program_id'];
            $station_id = $screen['station_id'];
            $channel_id = $program['channel_id'];
            $record['channel'] = $channel['channel_name'];
            $record['channel_logo'] = $channel['logo'];
            $record['content_name'] = $content['content_name'];

            $record['tape_id'] = $tag['tape_id'];
            $record['caption_name'] = $tag['caption_name'];

            $record['region'] = $region['screen_region_name'];

            $record['program'] = $program['pu_name'];
            $record['program_start_time'] = $program['start_time'];
            $record['program_end_time'] = $program['end_time'];

            $record['station'] = $station['station_name'];

            $record['seconds'] = $report['playbacks'] * $campaign['duration_to_play'];
            if (!isset($campaign['brand_owner_id']) || empty($campaign['brand_owner_id'])) {
                $record['brand_owner_id'] = $campaign['brand_owner_new'];
                $record['brand_id'] = $campaign['brand_new'];
                $record['product_id'] = $campaign['product_new'];
            } else {
                $record['brand_owner_id'] = $campaign['brand_owner_id'];
                $record['brand_id'] = $campaign['brand_id'];
                $record['product_id'] = $campaign['product_id'];
            }

            array_push($records, $record);
        }

        $schedule_date = array();
        $publisher = array();
        $channel = array();
        $region = array();
        $program = array();
        $program_start_time = array();
        $station = array();
        $content_name = array();
        // Obtain a list of columns
        foreach ($records as $key => $row) {
            $schedule_date[$key] = $row['schedule_date'];
            $publisher[$key] = $row['publisher'];
            $channel[$key] = $row['channel'];
            $region[$key] = $row['region'];
            $program[$key] = $row['program'];
            $program_start_time[$key] = $row['program_start_time'];
            $station[$key] = $row['station'];
            $content_name[$key] = $row['content_name'];
        }

        // Sort the data with volume descending, edition ascending
        // Add $data as the last parameter, to sort by the common key
        array_multisort($schedule_date, SORT_ASC,
            $publisher, SORT_ASC,
            $channel, SORT_ASC,
            $region, SORT_ASC,
            $program_start_time, SORT_ASC,
            $station, SORT_ASC,
            $records);

        $output = array();
        $output['impressions_records'] = $records;


        //Sort by content name to display content based summary report
        array_multisort(
            $content_name, SORT_ASC,
            $schedule_date, SORT_ASC,
            $publisher, SORT_ASC,
            $channel, SORT_ASC,
            $region, SORT_ASC,
            $program_start_time, SORT_ASC,
            $station, SORT_ASC,
            $records);

        $seconds_records = array();
        $region_val = array();

        foreach ($records as $record) {
            $brand_key = $record['brand_owner_id'] . "_" . $record['brand_id'] . "_" . $record['product_id'];
            if (!isset($seconds_records[$brand_key])) {
                $seconds_records[$brand_key] = array();
                $brandOwner = $cacheMapObj->getValue("BrandOwner", $record['brand_owner_id']);
                $seconds_records[$brand_key]['brand_owner_name'] = $brandOwner['brand_owner_name'];
                $brand = $cacheMapObj->getValue("Brand", $record['brand_id']);
                $seconds_records[$brand_key]['brand_name'] = $brand['brand_name'];
                $product = $cacheMapObj->getValue("Product", $record['product_id']);
                $seconds_records[$brand_key]['product_name'] = $product['product_name'];
                $seconds_records[$brand_key]['contents'] = array();
            }
            if (!isset($seconds_records[$brand_key]['contents'][$record['content_name']])) {
                $seconds_records[$brand_key]['contents'][$record['content_name']] = array();
                $seconds_records[$brand_key]['contents'][$record['content_name']]['channel'] = $record['channel'];
                $seconds_records[$brand_key]['contents'][$record['content_name']]['tape_id'] = $record['tape_id'];
                $seconds_records[$brand_key]['contents'][$record['content_name']]['caption_name'] = $record['caption_name'];
                $channel_logo_path = parse_url($record['channel_logo']);
                if (!empty($channel_logo_path['path'])) {
                    $channel_logo_url = $_SERVER['DOCUMENT_ROOT'] . $channel_logo_path['path'];
                } else {
                    $channel_logo_url = $_SERVER['DOCUMENT_ROOT'] . "/surewaves/advertiser/view/imgs/slogo.png";
                }
                $seconds_records[$brand_key]['contents'][$record['content_name']]['channel_logo'] = $channel_logo_url;
                $seconds_records[$brand_key]['contents'][$record['content_name']]['publisher'] = $record['publisher'];
                $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'] = array();
                $seconds_records[$brand_key]['contents'][$record['content_name']]['sub_total'] = 0;
            }
            if (!isset($seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']])) {
                $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']] = array();
                $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']]['name'] = $record['region'];
                $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']]['total'] = 0;
            }
            $seconds_records[$brand_key]['contents'][$record['content_name']]['regions'][$record['region']]['total'] += $record['seconds'];
            $seconds_records[$brand_key]['contents'][$record['content_name']]['sub_total'] += $record['seconds'];

            //changed by mani for adding total secodage for respective region
            if (isset($region_val[$record['region']]) || !empty($region_val[$record['region']])) {
                $region_val[$record['region']] = $region_val[$record['region']] + $record['seconds'];
            } else {
                $region_val[$record['region']] = $record['seconds'];
            }
        }

        $output['seconds_records'] = $seconds_records;
        $output['summary'] = $summary;
        $output['region_val'] = $region_val;
        return $output;
    }// end of functiion

    /*@explain - Use to show RO report in agency view */
    function get_ro_tracking_report($start_date, $end_date, $ro_numbers, $channel_ids, $content_filter, $program_filter, $region_id = 1)
    {
        echo "here";
        exit;
        $this->reports = array();
        global $cacheMapObj;
        require_once $_SERVER['DOCUMENT_ROOT'] . '/surewaves' . '/advertiser/db/Campaign.class.php';
        $CampaignObj = new Campaign($this->db);

        $agency_name = $_SESSION['agency_name'];
        $str_ros = implode("','", $ro_numbers);
        $str_channel_ids = implode("','", $channel_ids);


        //@explain - Get all station_ids for the given channels
        $station_ids = array();
        $query = "select station_id from sv_tv_station where channel_id IN ('" . $str_channel_ids . "')";
        $query .= " and install_date IS NOT NULL and independent_station = 1 ";
        $stations = Utility::getQueryResult($this->db, $query);
        foreach ($stations as $station)
            array_push($station_ids, $station['station_id']);
        $str_station_ids = implode("','", $station_ids);

        //@explain - Get all screens corresponding to channels and station_ids
        $screen_ids = array();
        $group_ids = array();    //Required to get loops from sv_loop_status
        $query = "select screen_id,group_id,channel_id,program_id from sv_screen where channel_id IN ('" . $str_channel_ids . "') and station_id IN ('$str_station_ids')";
        $screens = Utility::getQueryResult($this->db, $query);
        foreach ($screens as $screen) {
            array_push($screen_ids, $screen['screen_id']);
            array_push($group_ids, $screen['group_id']);
        }
        $str_screen_ids = implode("','", $screen_ids);
        $str_group_ids = implode("','", $group_ids);

        //@explain - Get mapping of channel_id => total_no_of_stations
        $this->channel_total_stations = array();
        $query = "select channel_id,count(*) as total_stations from sv_tv_station where channel_id IN ('" . $str_channel_ids . "')";
        $query .= " and install_date IS NOT NULL and independent_station = 1 group by channel_id ";
        $channel_total_stations_rows = Utility::getQueryResult($this->db, $query);
        foreach ($channel_total_stations_rows as $channel_total_stations_row) {
            $this->channel_total_stations[$channel_total_stations_row['channel_id']] = $channel_total_stations_row['total_stations'];
        }

        $todays_date = date('Y-m-d') . " 00:00:00";
        //@explain - Get all loop_ids for the given group_ids and date range
        $loop_ids = array();
        $query = "select group_id,date,loop_id from sv_loop_status where group_id IN ('$str_group_ids')";
        $query .= " and (date >='" . $start_date . "' and date <='" . $end_date . "') and screen_region_id = " . $region_id . " order by date asc";
        $loop_status_rows = Utility::getQueryResult($this->db, $query);
        foreach ($loop_status_rows as $loop_status_row) {
            array_push($loop_ids, $loop_status_row['loop_id']);
        }
        $loop_ids = array_unique($loop_ids);
        $str_loop_ids = implode("','", $loop_ids);


        $campaign_types = array("n", "r");

        foreach ($campaign_types as $campaign_type) {
            //@explain - Get all original campaign_ids from customer ro which are not make good
            $campaign_ids = array();
            $query = "select campaign_id from sv_advertiser_campaign where customer_ro_number IN('" . $str_ros . "')";
            $query .= " and is_make_good = 0 and (start_date <= '" . $end_date . "' and end_date >='" . $start_date . "')";
            $query .= " and agency_name LIKE '%$agency_name%'";
            $query .= " and nsr = '$campaign_type' and selected_region_id = $region_id";
            $campaigns = Utility::getQueryResult($this->db, $query);

            foreach ($campaigns as $campaign)
                array_push($campaign_ids, $campaign['campaign_id']);
            $str_campaign_ids = implode("','", $campaign_ids);

            //@explain -  28th August,2012 Vikas - Get only make good campaigns
            $makegood_campaign_ids = array();
            $query = "select campaign_id from sv_advertiser_campaign where customer_ro_number IN('" . $str_ros . "')";
            $query .= " and (start_date <= '" . $end_date . "' and end_date >='" . $start_date . "')";
            $query .= " and agency_name LIKE '%$agency_name%'";
            $query .= " and nsr = '$campaign_type' and selected_region_id = $region_id and is_make_good = 1";
            $results = Utility::getQueryResult($this->db, $query);
            foreach ($results as $result)
                array_push($makegood_campaign_ids, $result['campaign_id']);
            $str_makegood_combined_ids = implode("','", $makegood_campaign_ids);

            //@explain - Get all campaign_ids along with make good campaigns
            $combined_campaign_ids = array();
            $query = "select campaign_id from sv_advertiser_campaign where customer_ro_number IN('" . $str_ros . "')";
            $query .= " and (start_date <= '" . $end_date . "' and end_date >='" . $start_date . "')";
            $query .= " and agency_name LIKE '%$agency_name%'";
            $query .= " and nsr = '$campaign_type' and selected_region_id = $region_id";
            $results = Utility::getQueryResult($this->db, $query);
            foreach ($results as $result)
                array_push($combined_campaign_ids, $result['campaign_id']);
            $str_combined_ids = implode("','", $combined_campaign_ids);


            //@explain - Get requested impressions from screen/dates
            $query = "select * from sv_advertiser_campaign_screens_dates where campaign_id IN ('$str_campaign_ids') and screen_id IN ('$str_screen_ids')";
            $query .= " and (date >='" . $start_date . "' and date <='" . $end_date . "') order by date asc";
            $screens_dates = Utility::getQueryResult($this->db, $query);

            foreach ($screens_dates as $screen_date)
                $this->init_station_program($screen_date['date'], $screen_date['screen_id'], $campaign_type, $content_filter, $program_filter, 'requested_impressions', $screen_date['impressions']);

            //@explain 28th August,2012 Vikas - Get make good impressions for screen/dates
            $this->init_scheduled_impressions_for_loop_campaigns($str_loop_ids, $str_makegood_combined_ids, $loop_status_rows, $campaign_type, $content_filter, $program_filter, 'makegood');

            //@explain - Get scheduled impressions from the loop
            $this->init_scheduled_impressions_for_loop_campaigns($str_loop_ids, $str_campaign_ids, $loop_status_rows, $campaign_type, $content_filter, $program_filter, 'scheduled');

            //actual impressions and
            //actual duration
            $query = "select campaign_id,screen_id,content_id,schedule_date,count(*) as impressions, sum(playduration) as duration from sv_advertiser_report where campaign_id IN ('$str_combined_ids') and screen_id IN ('$str_screen_ids')";
            $query .= " and campaign_id != 0 and screen_id != 0 and screen_region_id = $region_id";
            $query .= " and (schedule_date >='" . $start_date . "' and schedule_date <='" . $end_date . "') group by schedule_date,screen_id,content_id order by schedule_date asc";
            $report_rows = Utility::getQueryResult($this->db, $query);

            //change
            foreach ($report_rows as $report_row) {
                $group_id = $cacheMapObj->getValue("GroupIdFromScreenId", $report_row['screen_id']);
                $program_id = $cacheMapObj->getValue("ProgramIdFromGroupId", $group_id);
                if (!$CampaignObj->is_program_valid_on_date($program_id, $report_row['schedule_date']))
                    continue;

                $this->init_station_program($report_row['schedule_date'], $report_row['screen_id'], $campaign_type, $content_filter, $program_filter, 'actual_impressions', $report_row['impressions'], 'actual_duration', $report_row['duration'], $report_row['content_id']);
            }
        }

        $date_summary = "Summary for  All Dates";
        if (count($this->reports) > 0) {
            foreach ($this->reports as $date => $stations) {
                if (!isset($this->reports[$date_summary])) {
                    $this->reports[$date_summary] = array();
                }
                foreach ($stations as $station_id => $station) {

                    if (!isset($this->reports[$date_summary][$station_id])) {
                        $this->reports[$date_summary][$station_id] = array();
                        $this->reports[$date_summary][$station_id]['station_name'] = $station['station_name'];
                        $this->reports[$date_summary][$station_id]['station_type'] = $station['station_type'];
                        $this->reports[$date_summary][$station_id]['total_stations'] = $station['total_stations'];

                        //if($this->reports[$date_summary][$station_id]==$last_updated_time['station_id'])
                        $this->reports[$date_summary][$station_id]['last_updated'] = $station['last_updated'];

                        $this->init_impressions($this->reports[$date_summary][$station_id]);

                        $this->reports[$date_summary][$station_id]['contents'] = array();
                    }


                    if ($station['station_type'] == 'n') {
                        $this->avg_impressions($this->reports[$date][$station_id], $station['total_stations']);
                    }

                    $this->reports[$date_summary][$station_id]['requested_impressions'] += $this->reports[$date][$station_id]['requested_impressions'];
                    $this->reports[$date_summary][$station_id]['scheduled_impressions'] += $this->reports[$date][$station_id]['scheduled_impressions'];
                    $this->reports[$date_summary][$station_id]['makegood_impressions'] += $this->reports[$date][$station_id]['makegood_impressions'];
                    $this->reports[$date_summary][$station_id]['actual_impressions'] += $this->reports[$date][$station_id]['actual_impressions'];


                    //change

                    $this->reports[$date_summary][$station_id]['scheduled_duration'] += $this->reports[$date][$station_id]['scheduled_duration'];
                    $this->reports[$date_summary][$station_id]['actual_duration'] += $this->reports[$date][$station_id]['actual_duration'];


                    if (!$content_filter)
                        continue;
                    $contents = $station['contents'];
                    foreach ($contents as $content_id => $content) {
                        if (empty($content['content_name']))
                            continue;

                        if (!isset($this->reports[$date_summary][$station_id]['contents'][$content_id])) {
                            $this->reports[$date_summary][$station_id]['contents'][$content_id] = array();
                            $this->reports[$date_summary][$station_id]['contents'][$content_id]['content_name'] = $content['content_name'];
                            $this->init_impressions($this->reports[$date_summary][$station_id]['contents'][$content_id]);
                        }

                        if ($station['station_type'] == 'n') {
                            $this->avg_impressions($this->reports[$date][$station_id]['contents'][$content_id], $station['total_stations']);

                            foreach ($content['programs'] as $program_id => $program) {
                                $this->avg_impressions($this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id], $station['total_stations']);
                            }
                        }

                        $this->reports[$date_summary][$station_id]['contents'][$content_id]['requested_impressions'] += $this->reports[$date][$station_id]['contents'][$content_id]['requested_impressions'];
                        $this->reports[$date_summary][$station_id]['contents'][$content_id]['scheduled_impressions'] += $this->reports[$date][$station_id]['contents'][$content_id]['scheduled_impressions'];
                        $this->reports[$date_summary][$station_id]['contents'][$content_id]['makegood_impressions'] += $this->reports[$date][$station_id]['contents'][$content_id]['makegood_impressions'];
                        $this->reports[$date_summary][$station_id]['contents'][$content_id]['actual_impressions'] += $this->reports[$date][$station_id]['contents'][$content_id]['actual_impressions'];

                        //change pushing to report
                        $this->reports[$date_summary][$station_id]['contents'][$content_id]['scheduled_duration'] += $this->reports[$date][$station_id]['contents'][$content_id]['scheduled_duration'];

                        $this->reports[$date_summary][$station_id]['contents'][$content_id]['actual_duration'] += $this->reports[$date][$station_id]['contents'][$content_id]['actual_duration'];

                    }
                }
            }
            // change last updated
        }

        $this->reports = array_reverse($this->reports);
        return $this->reports;
    }

    /*@explain -  */

    function init_station_program($date, $screen_id, $campaign_type, $content_filter, $program_filter, $impression_type, $impressions, $duration_type, $duration, $content_id)
    {
        global $cacheMapObj;

        if (!isset($this->reports[$date])) {
            $this->reports[$date] = array();
        }

        $screen = $cacheMapObj->getValue("Screen", $screen_id);
        $channel = $cacheMapObj->getValue("Channel", $screen['channel_id']);

        $station_id = $screen['station_id'];
        $station = $cacheMapObj->getValue("Station", $station_id);
        $last_updated = $station['last_updated'];
        if ($campaign_type == "r") {
            $station_name = $channel['channel_name'] . " - " . $station['station_name'];
        } else {
            $station_id = "ch_" . $channel['tv_channel_id'];
            $station_name = $channel['channel_name'] . " - " . $this->channel_total_stations[$channel['tv_channel_id']] . " Stations";
        }

        if (!isset($this->reports[$date][$station_id])) {
            $this->reports[$date][$station_id] = array();
            $this->reports[$date][$station_id]['station_name'] = $station_name;
            $this->reports[$date][$station_id]['station_type'] = $campaign_type;
            $this->reports[$date][$station_id]['last_updated'] = $last_updated;
            $this->reports[$date][$station_id]['total_stations'] = $this->channel_total_stations[$channel['tv_channel_id']];
            $this->reports[$date][$station_id]['requested_impressions'] = 0;
            $this->reports[$date][$station_id]['scheduled_impressions'] = 0;
            $this->reports[$date][$station_id]['makegood_impressions'] = 0;
            $this->reports[$date][$station_id]['actual_impressions'] = 0;
            $this->reports[$date][$station_id]['contents'] = array();
            //change
            $this->reports[$date][$station_id]['scheduled_duration'] = 0;
            $this->reports[$date][$station_id]['actual_duration'] = 0;
        }
        $this->reports[$date][$station_id][$impression_type] += $impressions;
        //change
        $this->reports[$date][$station_id][$duration_type] += $duration;

        if (!$content_filter)
            return;

        $program_id = $cacheMapObj->getValue("ProgramIdFromGroupId", $screen['group_id']);
        $program = $cacheMapObj->getValue("Program", $program_id);

        $content = $cacheMapObj->getValue("AdvertiserContent", $content_id);

        if (empty($content['content_name']))
            return;

        if (!isset($this->reports[$date][$station_id]['contents'][$content_id])) {
            $this->reports[$date][$station_id]['contents'][$content_id] = array();
            //$this->reports[$date][$station_id]['contents'][$content_id]['program_name'] = $program['pu_name']."</br>".$program['start_time']."-".$program['end_time'];
            $this->reports[$date][$station_id]['contents'][$content_id]['content_name'] = $content['content_name'];
            $this->reports[$date][$station_id]['contents'][$content_id]['requested_impressions'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['scheduled_impressions'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['makegood_impressions'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['actual_impressions'] = 0;

            //change
            $this->reports[$date][$station_id]['contents'][$content_id]['scheduled_duration'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['actual_duration'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['programs'] = array();
        }
        $this->reports[$date][$station_id]['contents'][$content_id][$impression_type] += $impressions;
        $this->reports[$date][$station_id]['contents'][$content_id][$duration_type] += $duration;

        if (!$program_filter)
            return;

        if (!isset($this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id])) {
            $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id] = array();

            $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id]['program_name'] = $program['pu_name'];
            $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id]['requested_impressions'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id]['scheduled_impressions'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id]['makegood_impressions'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id]['actual_impressions'] = 0;

            //change
            $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id]['scheduled_duration'] = 0;
            $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id]['actual_duration'] = 0;
            //$this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id][$program]=array();
        }

        $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id][$impression_type] += $impressions;
        $this->reports[$date][$station_id]['contents'][$content_id]['programs'][$program_id][$duration_type] += $duration;
    }

    private function init_scheduled_impressions_for_loop_campaigns($str_loop_ids, $str_campaign_ids, $loop_status_rows, $campaign_type, $content_filter, $program_filter, $type = 'scheduled')
    {
        $todays_date = date('Y-m-d') . " 00:00:00";
        global $cacheMapObj;
        require_once $_SERVER['DOCUMENT_ROOT'] . '/surewaves' . '/advertiser/db/Campaign.class.php';
        $CampaignObj = new Campaign($this->db);

        $loop_content_impressions = array(); //store total impressions for this content_id and loop -  $a[loop_id][content_id] = 6
        $loop_content_duration = array();        //store total duration for this content_id and loop	- $a[loop_id][content_id] = 5
        $query = "select loop_id,content_id,count(*) as impressions,sum(duration) as duration from sv_loop_contents where loop_id IN ('" . $str_loop_ids . "') ";
        $query .= " and campaign_id IN ('$str_campaign_ids') and campaign_id != 0 group by loop_id,content_id";
        $loop_contents = Utility::getQueryResult($this->db, $query);
        foreach ($loop_contents as $loop_content) {
            if (!isset($loop_content_impressions[$loop_content['loop_id']]))
                $loop_content_impressions[$loop_content['loop_id']] = array();
            $loop_content_impressions[$loop_content['loop_id']][$loop_content['content_id']] = $loop_content['impressions'];

            if (!isset($loop_content_duration[$loop_content['loop_id']]))
                $loop_content_duration[$loop_content['loop_id']] = array();
            $loop_content_duration[$loop_content['loop_id']][$loop_content['content_id']] = $loop_content['duration'];
        }
        //$loop_status_rows - Has all loop_ids for the given group_ids and date range
        //Filter extra loop_ids based on programs not valid for dates and programs yet to start
        foreach ($loop_status_rows as $loop_status_row) {
            $program_id = $cacheMapObj->getValue("ProgramIdFromGroupId", $loop_status_row['group_id']);
            if (!$CampaignObj->is_program_valid_on_date($program_id, $loop_status_row['date']))
                continue;

            if (!isset($loop_content_impressions[$loop_status_row['loop_id']])) {
                continue;
            }
            //pp change
            $ignore_impressions = false;
            if ($loop_status_row['date'] == $todays_date) {
                $program = $cacheMapObj->getValue("Program", $program_id);
                if ($program['start_time'] > date('H:i:s')) {
                    $ignore_impressions = true;
                }
            }
            if (!$ignore_impressions) {
                $screen_id = $cacheMapObj->getValue("ScreenIdFromGroupId", $loop_status_row['group_id']);
                foreach ($loop_content_impressions[$loop_status_row['loop_id']] as $content_id => $impressions) {
                    $this->init_station_program($loop_status_row['date'], $screen_id, $campaign_type, $content_filter, $program_filter, $type . '_impressions', $impressions, $type . '_duration', $loop_content_duration[$loop_status_row['loop_id']][$content_id], $content_id);
                }
            }
        }
    }

    function init_impressions(&$obj)
    {
        $obj['requested_impressions'] = 0;
        $obj['scheduled_impressions'] = 0;
        $obj['makegood_impressions'] = 0;
        $obj['actual_impressions'] = 0;
        $obj['scheduled_duration'] = 0;
        $obj['actual_duration'] = 0;
    }

    function avg_impressions(&$obj, $total_stations)
    {
        $obj['requested_impressions'] /= $total_stations;
        $obj['scheduled_impressions'] /= $total_stations;
        $obj['makegood_impressions'] /= $total_stations;
        $obj['actual_impressions'] /= $total_stations;
        $obj['scheduled_duration'] /= $total_stations;
        $obj['actual_duration'] /= $total_stations;

        $obj['requested_impressions'] = ceil($obj['requested_impressions']);
        $obj['scheduled_impressions'] = ceil($obj['scheduled_impressions']);
        $obj['makegood_impressions'] = ceil($obj['makegood_impressions']);
        $obj['actual_impressions'] = ceil($obj['actual_impressions']);
        $obj['scheduled_duration'] = ceil($obj['scheduled_duration']);
        $obj['actual_duration'] = ceil($obj['actual_duration']);
    }
    //Nitish 9.1.2015: commented to avoid conflicts for Cluster implementation
    /*function getChannelTamInfo($channel_id){
        $sql = "SELECT tm.tam_market_name, tm.tam_market_code, cm.tam_channel_reach, tm.tam_display_market_name FROM `sv_tam_channel_market` cm, sv_tam_market tm where cm.tam_market_id = tm.tam_market_id and cm.tv_channel_id = ".$channel_id;
        return Utility::getQueryResult($this->db, $sql);
    }*/
}

?>
