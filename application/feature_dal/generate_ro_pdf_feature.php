<?php
/**
 * Created by PhpStorm.
 * Author: Yash
 * Date: November, 2019
 */

namespace application\feature_dal;

use application\repo\BaseDAL;
use Illuminate\Database\Capsule\Manager as DB;

include_once APPPATH . 'repo/base_dal.php';

class GenerateRoPdfFeature extends BaseDAL
{
    public function __construct()
    {
        parent::__construct();
        log_message('DEBUG', 'In GenerateRoPdfFeature@constructor | Object Created');
    }

    public function getNetworkDetails($condition)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getNetworkDetails | Entered');
        $result = DB::table('ro_approved_networks')
            ->select(DB::raw('internal_ro_number,customer_id, 
	                            customer_name, billing_name, group_concat(distinct channel_name) as ChannelName,
	                            group_concat(distinct tv_channel_id) as channel_id,
	                            group_concat(concat_ws(\'#\',channel_name,channel_spot_avg_rate,channel_banner_avg_rate) separator \'?\') as Rate,
	                             client_name, revision_no,
	                             customer_share,
	                             sum(channel_spot_amount) + sum(channel_banner_amount) as Net_Amount'))
            ->where($condition)
            ->groupBy('internal_ro_number', 'customer_id')
            ->orderBy('id', 'desc')
            ->get();
        $result = json_decode(json_encode($result), true);
        if (count($result) > 0) {
            return $result;
        }
        return array();
    }

    public function getMarketClusterAndCampaignPeriod($internalRoNo, $nwIds)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getMarketClusterAndCampaignPeriod | Entered with NetworkIds ' . print_r($nwIds, True));
        $result = DB::select("select sac.campaign_id, internal_ro_number, sac.customer_ro_number, sac.client_name, sac.agency_name,
    sd.enterprise_id, market_id,
    min(sd.date) as start_date, max(sd.date) as end_date,
	group_concat(distinct ssm.sw_market_name) as Market_Cluster from 
	sv_advertiser_campaign sac inner join
	sv_advertiser_campaign_screens_dates sd  on sac.campaign_id = sd.campaign_id inner join
    sv_sw_market ssm on sac.market_id = ssm.id where
    sac.internal_ro_number = '$internalRoNo'
    and sd.enterprise_id in ($nwIds) and
	sd.status = 'scheduled' and (sac.campaign_status = 'pending_content' or sac.campaign_status = 'scheduled' or sac.campaign_status = 'saved')
	 and sac.approved_status = 'approved' and sac.is_make_good = 0
    group by sac.internal_ro_number, sd.enterprise_id
    order by campaign_id desc");

        $result = json_decode(json_encode($result), true);
        log_message('DEBUG', 'In GenerateRoPdfFeature@getMarketClusterAndCampaignPeriod | Result=> ' . print_r($result, True));
        if (count($result) > 0) {
            return $result;
        }
        return array();
    }

    public function getScheduleDataTimeBandWise($internalRoNo, $nwIds, $startTime, $endTime)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getScheduleDataTimeBandWise | Entered');
        $result = DB::select("SELECT 
sacsd.DATE, sacsd.group_id, sum(sacsd.impressions) as TotalImp,
sacsd.enterprise_id,sac.channel_id, sac.caption_name, sacsd.screen_region_id,
sac.brand_new, sac.language, sac.ro_duration
FROM sv_advertiser_campaign sac inner JOIN
sv_advertiser_campaign_screens_dates sacsd  ON sac.campaign_id = sacsd.campaign_id INNER JOIN
sv_screen s ON s.group_id = sacsd.group_id INNER JOIN
sv_tv_pu stp ON stp.pu_id = s.program_id
WHERE sac.internal_ro_number = '$internalRoNo' AND 
sacsd.enterprise_id in ($nwIds) and 
((stp.start_time >= '$startTime' and stp.end_time <= '$endTime')
    OR (stp.start_time < '$startTime' and	
   (stp.end_time >= '$startTime' and stp.end_time <= '$endTime' )))
   and (sacsd.status = 'scheduled')
    and (sac.campaign_status = 'pending_content' or sac.campaign_status = 'scheduled' or sac.campaign_status = 'saved')
     and sac.approved_status = 'Approved' and sac.is_make_good = 0
    GROUP BY sacsd.enterprise_id,sac.channel_id,sacsd.date, sac.caption_name
ORDER by sacsd.DATE ASC , sacsd.campaign_id ASC ,sacsd.enterprise_id asc;");

        $result = json_decode(json_encode($result), true);
        if (count($result) > 0) {
            return $result;
        }
        return array();
    }

    public function getContentLinks($internalRoNo, $customerID, $channelIds)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getContentLinks | Entered');
        $condition = array('internal_ro_number' => $internalRoNo, 'tc.enterprise_id' => $customerID, 'status' => 'approved');
        $result = DB::table('ro_channel_file_location as fl')
            ->join('sv_tv_channel as tc', 'fl.channel_id', '=', 'tc.tv_channel_id')
            ->select(DB::raw('fl.file_location, tc.channel_name, tc.tv_channel_id'))
            ->where($condition)
            ->whereIn('fl.channel_id', $channelIds)
            ->get();
        $result = json_decode(json_encode($result), true);
        if (count($result) > 0) {
            return $result;
        }
        return array();
    }

    public function updatePdfStatusInRoApprovedNetworks($condition, $data)
    {
        $this->RoApprovedNetworks->updateData($condition, $data);
    }

    public function getCustomerDetail($customerId)
    {
        $result = $this->SvCustomer->getColumnsWhere(array('customer_id' => $customerId), array('*'));
        if ($result->count() > 0) {
            return $result->toArray();
        }
        return array();
    }

    public function getNetworkCancelDate($internalRoNo, $nwId)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getNetworkCancelDate | Entered');
        $result = DB::select("select MIN(acs.date) as start_date,MAX(acs.date) as end_date,
                group_concat(distinct ssm.sw_market_name) as Market_Cluster 
                from sv_advertiser_campaign as ac inner join
                sv_advertiser_campaign_screens_dates as acs on ac.campaign_id = acs.campaign_id inner join 
                sv_sw_market ssm on ac.market_id = ssm.id
                where ac.internal_ro_number = '$internalRoNo' and
                acs.enterprise_id = $nwId and acs.status='cancelled'");

        $result = json_decode(json_encode($result), true);
        if (count($result) > 0) {
            return $result;
        }
        return array();
    }

    public function getChannelNames($channelIds)
    {
	if($channelIds == null or empty($channelIds))
		$channelIds = array();
        $result = $this->SvTvChannel->getColumnsWhereIn('tv_channel_id', $channelIds, array('*'));
        if ($result->count() > 0) {
            return $result->toArray();
        }
        return array();
    }
}
