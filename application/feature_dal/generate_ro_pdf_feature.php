<?php
/**
 * Created by PhpStorm.
 * Author: Yash
 * Date: November, 2019
 */

namespace application\feature_dal;

use application\repo\BaseDAL;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;

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
        
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@getNetworkDetails | Entered with arguments => ' . print_r(func_get_args(), True));
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
            log_message('DEBUG', 'In GenerateRoPdfFeature@getNetworkDetails | Before exiting , the db query values are'. print_r($result , TRUE));
            if (count($result) > 0) {
                
                return $result;
            }
            return array();
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@getNetworkDetails  | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
        
    }

    public function getMarketClusterAndCampaignPeriod($internalRoNo, $nwIds)
    {
        
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@getMarketClusterAndCampaignPeriod | Entered with arguments ' . print_r(func_get_args(), True));
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
            log_message('DEBUG', 'In GenerateRoPdfFeature@getMarketClusterAndCampaignPeriod | Before exiting , the db query values are'. print_r($result , TRUE));
            if (count($result) > 0) {
                return $result;
            }
            return array();
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@getMarketClusterAndCampaignPeriod | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
        
    }

    public function getScheduleDataTimeBandWise($internalRoNo, $nwIds, $startTime, $endTime)
    {
        
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@getScheduleDataTimeBandWise | Entered with arguments ' . print_r(func_get_args(), True));
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
        log_message('DEBUG', 'In GenerateRoPdfFeature@getScheduleDataTimeBandWise | Before exiting , the db query values are'. print_r($result , TRUE));
        if (count($result) > 0) {
            return $result;
        }
        return array();
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@getScheduleDataTimeBandWise | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
        
    }

    public function getContentLinks($internalRoNo, $customerID, $channelIds)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getContentLinks | Entered with arguments ' . print_r(func_get_args(), True));
        try{
            $condition = array('internal_ro_number' => $internalRoNo, 'tc.enterprise_id' => $customerID, 'status' => 'approved');
            $result = DB::table('ro_channel_file_location as fl')
                ->join('sv_tv_channel as tc', 'fl.channel_id', '=', 'tc.tv_channel_id')
                ->select(DB::raw('fl.file_location, tc.channel_name, tc.tv_channel_id'))
                ->where($condition)
                ->whereIn('fl.channel_id', $channelIds)
                ->get();
            $result = json_decode(json_encode($result), true);
            log_message('DEBUG', 'In GenerateRoPdfFeature@getContentLinks | Before exiting , the db query values are'. print_r($result , TRUE));
            if (count($result) > 0) {
                return $result;
            }
            return array();
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@getContentLinks | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
       
    }

    public function updatePdfStatusInRoApprovedNetworks($condition, $data)
    {
        
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@updatePdfStatusInRoApprovedNetworks | Entered with arguments ' . print_r(func_get_args(), True));
            $this->RoApprovedNetworks->updateData($condition, $data);
            log_message('DEBUG', 'In GenerateRoPdfFeature@updatePdfStatusInRoApprovedNetworks | Exiting');
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@updatePdfStatusInRoApprovedNetworks | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
        
    }

    public function getCustomerDetail($customerId)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getCustomerDetail | Entered with arguments ' . print_r(func_get_args(), True));
        $result = $this->SvCustomer->getColumnsWhere(array('customer_id' => $customerId), array('*'));
        log_message('DEBUG', 'In GenerateRoPdfFeature@getCustomerDetail | Before exiting , the db query values are'. print_r($result , TRUE));
        if ($result->count() > 0) {
            return $result->toArray();
        }
        return array();
    }

    public function getNetworkCancelDate($internalRoNo, $nwId)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getNetworkCancelDate |  Entered with arguments ' . print_r(func_get_args(), True));
        $result = DB::select("select MIN(acs.date) as start_date,MAX(acs.date) as end_date,
                group_concat(distinct ssm.sw_market_name) as Market_Cluster 
                from sv_advertiser_campaign as ac inner join
                sv_advertiser_campaign_screens_dates as acs on ac.campaign_id = acs.campaign_id inner join 
                sv_sw_market ssm on ac.market_id = ssm.id
                where ac.internal_ro_number = '$internalRoNo' and
                acs.enterprise_id = $nwId and acs.status='cancelled'");

        $result = json_decode(json_encode($result), true);
        log_message('DEBUG', 'In GenerateRoPdfFeature@getNetworkCancelDate | Before exiting , the db query values are'. print_r($result , TRUE));
        if (count($result) > 0) {
            return $result;
        }
        return array();
    }

    public function getChannelNames($channelIds)
    {
        log_message('DEBUG', 'In GenerateRoPdfFeature@getChannelNames |  Entered with arguments ' . print_r(func_get_args(), True));
	    if($channelIds == null or empty($channelIds))
		    $channelIds = array();
        $result = $this->SvTvChannel->getColumnsWhereIn('tv_channel_id', $channelIds, array('*'));
        log_message('DEBUG', 'In GenerateRoPdfFeature@getChannelNames | Before exiting , the db query values are'. print_r($result , TRUE));
        if ($result->count() > 0) {
            return $result->toArray();
        }
        return array();
    }
    
    public function checkNetworkRoExistAgainstInternalRo($internalRo)
    {
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@checkNetworkRoExistAgainstInternalRo | Entered with arguments ' . print_r(func_get_args(), True));
            $condition = array(array('internal_ro_number', $internalRo));
            
	    //DB::enableQueryLog();
	    //$result = $this->RoNetworkRoReportDetails->getColumnsWhere($condition, array('*'));
	    
	    $result = DB::select("select * from ro_network_ro_report_details where internal_ro_number = '".$internalRo."'");
	    $result = json_decode(json_encode($result), true);
	    
	    //$quries = DB::getQueryLog();
	    //var_dump($quries);
            log_message('DEBUG', 'In GenerateRoPdfFeature@checkNetworkRoExistAgainstInternalRo | Before exiting , the db query values are'. print_r($result , TRUE));
           /* if ($result->count() > 0) {
                return $result->toArray();
            }*/
	    if (count($result) > 0) {
            	return $result;
            }
            return array();
            
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@checkNetworkRoExistAgainstInternalRo | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
        
    }
    public function checkNetworkRoExist($networkRo)
    {
        
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@checkNetworkRoExist | Entered with arguments ' . print_r(func_get_args(), True));
            $condition = array(array('network_ro_number', $networkRo));
            $result = $this->RoNetworkRoReportDetails->getColumnsWhere($condition, array('*'));
            log_message('DEBUG', 'In GenerateRoPdfFeature@checkNetworkRoExist | Before exiting , the db query values are'. print_r($result , TRUE));
            if ($result->count() > 0) {
                return $result->toArray();
            }
            return array();
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@checkNetworkRoExist | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
    }
    public function insertNetworkRoDetails($insertData)
    {
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@insertNetworkRoDetails | Entered with arguments ' . print_r(func_get_args(), True));
            $this->RoNetworkRoReportDetails->insertData($insertData);
            log_message('DEBUG', 'In GenerateRoPdfFeature@insertNetworkRoDetails | Exiting');
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@insertNetworkRoDetails | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        } 
        
    }
    public function updateNetworkRoDetails($updateData , $networkRoNo){
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@updateNetworkRoDetails | Entered with arguments ' . print_r(func_get_args(), True));
            $where_data = array(array('network_ro_number', $networkRoNo));
        
            $this->RoNetworkRoReportDetails->updateData($where_data,$updateData);
            log_message('DEBUG', 'In GenerateRoPdfFeature@updateNetworkRoDetails | Exiting');
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@updateNetworkRoDetails | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        } 
    }
    public function getAllRoCancelInvoiceData($whereData){
        log_message('DEBUG', 'In GenerateRoPdfFeature@getAllRoCancelInvoiceData |  Entered with arguments ' . print_r(func_get_args(), True));
        //DB::enableQueryLog();
	$result = $this->RoCancelInvoice->getColumnsWhere($whereData, array('*'));
	//$query = DB::getQueryLog();
	//var_dump($query);
        log_message('DEBUG', 'In GenerateRoPdfFeature@getAllRoCancelInvoiceData | Before exiting , the db query values are'. print_r($result , TRUE));
        if ($result->count() > 0) {
            return $result->toArray();
        }
        return array();
    }
    public function check_for_ro_cancellation($internalRoNo){
        log_message('DEBUG', 'In GenerateRoPdfFeature@check_for_ro_cancellation |  Entered with arguments ' . print_r(func_get_args(), True));
       
        $result = DB::select("select * from ro_cancel_external_ro where ext_ro_id in(select id from ro_am_external_ro 
                                where internal_ro='$internalRoNo') and cancel_type = 'cancel_ro' 
                                and cancel_ro_by_admin='1'");
        $result = json_decode(json_encode($result), true);
        log_message('DEBUG', 'In GenerateRoPdfFeature@check_for_ro_cancellation | Before exiting , the db query values are'. print_r($result , TRUE));
        if (count($result) > 0) {
            return $result;
        }
        return array();
    }
    
    public function updateStatusForRoCancelInvoiceData($networkRoNo){
        try {
            log_message('DEBUG', 'In GenerateRoPdfFeature@updateStatusForRoCancelInvoiceData |  Entered with arguments ' . print_r(func_get_args(), True));
            $updatedata = array('pdf_processing' => 1);
            
            $where_data = array(array('network_ro_number', $networkRoNo));
            
            $this->RoCancelInvoice->updateData($where_data,$updatedata);
            log_message('DEBUG', 'In GenerateRoPdfFeature@updateStatusForRoCancelInvoiceData | Exiting');
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@updateStatusForRoCancelInvoiceData | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }

    }
    public function insertIntoRoMailData($mailData){
        try{
            log_message('DEBUG', 'In GenerateRoPdfFeature@insertIntoRoMailData |  Entered with arguments ' . print_r(func_get_args(), True));
            $this->RoMailData->insertData($mailData);
            log_message('DEBUG', 'In GenerateRoPdfFeature@insertIntoRoMailData | Exiting');
        }catch(QueryException $e){
            log_message('error', 'In GenerateRoPdfFeature@insertIntoRoMailData | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
        

    }

}
