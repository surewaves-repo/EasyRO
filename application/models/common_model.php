<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class COMMON_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getScheduledChannelData($channels_scheduled, $internal_ro_number)
    {
        $data = array();
        foreach ($channels_scheduled as $key => $value) {
            $tmp = array();
            $tmp['channel_id'] = $value['channel_id'];
            $tmp['channel_name'] = $value['channel_name'];
            $scheduledImpression = $this->getScheduledChannelImpressionForRo($value['channel_id'], $internal_ro_number);
            $tmp['monthwise'] = $this->getContentImressionByMonth($scheduledImpression);
            array_push($data, $tmp);
        }
        return $data;
    }

    public function getScheduledChannelImpressionForRo($channelId, $internal_ro_number)
    {
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,ac.caption_name,acsd.screen_region_id,acsd.date as scheduled_date "
            . " FROM sv_advertiser_campaign_screens_dates AS acsd "
            . " inner join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id"
            . " WHERE acsd.status !='cancelled' and ac.internal_ro_number = '$internal_ro_number' and ac.channel_id='$channelId' and visible_in_easyro = 1 GROUP BY ac.caption_name,ac.ro_duration,acsd.screen_region_id,acsd.date order by acsd.date";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getContentImressionByMonth($scheduledImpression)
    {
        $data = array();
        foreach ($scheduledImpression as $si) {
            $scheduled_date = trim(substr($si['scheduled_date'], 0, 11));
            $month_name = get_month($scheduled_date);
            $year_name = get_year($scheduled_date);
            $month_year = $month_name . "-" . $year_name;

            $caption_name = trim($si['caption_name']);

            if ($si['screen_region_id'] == 1) {
                if (!isset($data['spot_fct'][$caption_name][$month_year])) {
                    $data['spot_fct'][$caption_name][$month_year] = $si['total_ad_seconds'];
                } else {
                    $data['spot_fct'][$caption_name][$month_year] = $data['spot_fct'][$caption_name][$month_year] + $si['total_ad_seconds'];
                }
            } else if ($si['screen_region_id'] == 3) {
                if (!isset($data['banner_fct'][$caption_name][$month_year])) {
                    $data['banner_fct'][$caption_name][$month_year] = $si['total_ad_seconds'];
                } else {
                    $data['banner_fct'][$caption_name][$month_year] = $data['banner_fct'][$caption_name][$month_year] + $si['total_ad_seconds'];
                }

            }
        }
        return $data;
    }


    public function get_daywise_breakup_scheduled_fct($internal_ro_number)
    {
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,ac.caption_name,c.tv_channel_id,c.channel_name,acsd.screen_region_id,acsd.date as scheduled_date FROM sv_advertiser_campaign_screens_dates AS acsd, sv_advertiser_campaign as ac,sv_tv_channel AS c WHERE acsd.status !='cancelled' AND ac.campaign_id=acsd.campaign_id and ac.channel_id=c.tv_channel_id AND ac.internal_ro_number = '$internal_ro_number' AND visible_in_easyro = 1 GROUP BY c.channel_name,ac.ro_duration,acsd.screen_region_id,acsd.date order by c.channel_name,acsd.date";
        $result = $this->db->query($query);

        $channels_seconds = $result->result("array");
        //We have to make loop in such a way
        //channel_id => array('OCT'=>Seconds,'Nov'=>Seconds)

        $channel_array = array();
        foreach ($channels_seconds as $value) {
            $channel_id = $value['tv_channel_id'];
            if (!array_key_exists($channel_id, $channel_array)) {
                $channel_array[$channel_id]['channel_id'] = $value['tv_channel_id'];
                $channel_array[$channel_id]['channel_name'] = $value['channel_name'];

                $scheduled_date = trim(substr($value['scheduled_date'], 0, 11));
                $caption_name = trim($value['caption_name']);

                if ($value['screen_region_id'] == 1) {
                    $channel_array[$channel_id]['spot_fct'][$caption_name][$scheduled_date] = $value['total_ad_seconds'];
                } else if ($value['screen_region_id'] == 3) {
                    $channel_array[$channel_id]['banner_fct'][$caption_name][$scheduled_date] = $value['total_ad_seconds'];
                }
            } else {
                if ($value['screen_region_id'] == 1) {
                    $channel_array[$channel_id]['spot_fct'][$caption_name][$scheduled_date] += $value['total_ad_seconds'];
                } else if ($value['screen_region_id'] == 3) {
                    $channel_array[$channel_id]['banner_fct'][$caption_name][$scheduled_date] += $value['total_ad_seconds'];
                }
            }
        }
        return $channel_array;
    }

    public function get_channels_scheduled_for_market($market_price, $internal_ro_number)
    {
        $market_data = array();
        $markets = array();
        $channel_scheduled = array();
        foreach ($market_price as $mkts) {
            $tmp = array();
            $tmp['market_name'] = $mkts['market'];
            $tmp['spot_price'] = $mkts['spot_price'];
            $tmp['banner_price'] = $mkts['banner_price'];
            $tmp['channels_scheduled'] = array();

            //get channel_id and channel_name for that market
            $channel_mkts = $this->mg_model->get_scheduled_channel_for_market($mkts['market'], $internal_ro_number);
            foreach ($channel_mkts as $chnls) {
                $tmp_channel = array();
                $tmp_channel['channel_id'] = $chnls['tv_channel_id'];
                $tmp_channel['channel_name'] = $chnls['channel_name'];
                array_push($tmp['channels_scheduled'], $tmp_channel);
                array_push($channel_scheduled, $tmp_channel);
            }

            array_push($markets, $tmp);
        }
        $market_data['markets_channel'] = $markets;
        $market_data['channel_scheduled'] = $channel_scheduled;
        return $market_data;
    }

    public function get_monthwise_breakup_fct($internal_ro_number)
    {
        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,ac.caption_name,c.tv_channel_id,c.channel_name,acsd.screen_region_id,acsd.date as scheduled_date FROM sv_advertiser_campaign_screens_dates AS acsd, sv_advertiser_campaign as ac,sv_tv_channel AS c WHERE acsd.status !='cancelled' AND ac.campaign_id=acsd.campaign_id and ac.channel_id=c.tv_channel_id AND ac.internal_ro_number = '$internal_ro_number' AND visible_in_easyro = 1 GROUP BY c.channel_name,ac.ro_duration,acsd.screen_region_id,acsd.date order by c.channel_name,acsd.date";
        $result = $this->db->query($query);

        $channels_seconds = $result->result("array");
        //We have to make loop in such a way
        //channel_id => array('OCT'=>Seconds,'Nov'=>Seconds)

        $month_array = array();
        foreach ($channels_seconds as $value) {
            $channel_id = $value['tv_channel_id'];
            $scheduled_date = trim(substr($value['scheduled_date'], 0, 11));
            $scheduled_month = date('F', strtotime($scheduled_date));

            if (!array_key_exists($scheduled_month, $month_array)) {
                $month_array[$scheduled_month]['channel_id'] = $value['tv_channel_id'];
                $month_array[$scheduled_month]['channel_name'] = $value['channel_name'];


                $caption_name = trim($value['caption_name']);
                if ($value['screen_region_id'] == 1) {
                    $month_array[$scheduled_month]['spot_fct'][$caption_name] = $value['total_ad_seconds'];

                } else if ($value['screen_region_id'] == 3) {
                    $month_array[$scheduled_month]['banner_fct'][$caption_name] = $value['total_ad_seconds'];

                }
            } else {
                if ($value['screen_region_id'] == 1) {
                    $month_array[$scheduled_month]['spot_fct'][$caption_name] += $value['total_ad_seconds'];

                } else if ($value['screen_region_id'] == 3) {
                    $month_array[$scheduled_month]['banner_fct'][$caption_name] += $value['total_ad_seconds'];

                }
            }
        }
        return $month_array;
    }

    public function getMarketData($whereData)
    {
        $result = $this->db->get_where('sv_sw_market', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getChildProfileIds($profile_id, $belowProfileIds)
    {
        $childProfileId = $this->am_model->getChildProfileId(array('parent_id' => $profile_id));
        if (count($childProfileId) == 0) {
            return $belowProfileIds;
        } else {
            $profile_id = $childProfileId[0]['profile_id'];
            array_push($belowProfileIds, $profile_id);
            return $this->getChildProfileIds($profile_id, $belowProfileIds);
        }
    }

    public function getChildUserIds($user_id, $childUserIds)
    {
        $childUserId = $this->am_model->getChildUserId($user_id);
        if (count($childUserId) == 0) {
            return $childUserIds;
        } else {
            $fetchedIds = array();
            foreach ($childUserId as $val) {
                array_push($childUserIds, $val['user_id']);
                array_push($fetchedIds, $val['user_id']);
            }
            return $this->getChildUserIds(implode(",", $fetchedIds), $childUserIds);
        }
    }

    public function getCommaSepartedValues($userData)
    {
        $value = '';
        foreach ($userData as $val) {
            if (empty($value)) {
                $value = $val;
            } else {
                $value = $value . "," . $val;
            }
        }
        return $value;
    }

    public function getYears()
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

        $data = array();
        $data['from_year'] = $from_year;
        $data['to_year'] = $to_year;
        $data['financial_year'] = $from_year . '-' . $to_year;;
        return $data;
    }

    public function getMonthNameAcrossFinancialYear($startDate, $endDate)
    {
        $modified_startDate = date("Y-m-01", strtotime($startDate));
        $data = array();
        for ($i = strtotime($modified_startDate); $i <= strtotime($endDate); $i = strtotime("+1 month", $i)) {
            $month_name = date("F", $i);
            $financialYear = $this->getFinancialYear($i);

            if (!array_key_exists($financialYear, $data)) {
                $data[$financialYear] = array();
            }
            if (!isset($data[$financialYear]) || empty($data[$financialYear])) {
                $data[$financialYear] = $month_name;
            } else {
                $data[$financialYear] = $data[$financialYear] . "," . $month_name;
            }
        }
        return $data;
    }

    public function getFinancialYear($i)
    {
        $year = date("Y", $i);
        if (date("m", $i) <= 3) {
            $firstFinacialYear = $year - 1;
            $financialYear = $firstFinacialYear . '-' . $year;
            return $financialYear;
        } else {
            $nextFinacialYear = $year + 1;
            $financialYear = $year . '-' . $nextFinacialYear;
            return $financialYear;
        }
    }

}

/* End of file common_model.php */
/* Location: ./application/models/common_model.php */
