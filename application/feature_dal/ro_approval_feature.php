<?php


namespace application\feature_dal;

use application\repo\BaseDAL;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

include_once APPPATH . 'repo/base_dal.php';

class RoApprovalFeature extends BaseDAL
{
    public function __construct()
    {
        parent::__construct();
        log_message('DEBUG', 'In ro_approval_feature@constructor | constructor');
    }

    /**
     * @param $roId
     * @return mixed|null
     */
    public function getRoDetailForRoId($roId)
    {
        $condition = array(array('id', $roId));
        log_message('DEBUG', 'In ro_approval_feature@getRoDetailForRoId | Fetching RO records for RO_ID- ' . print_r($condition, true));
        $result = $this->RoAmExternalRo->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In ro_approval_feature@getRoDetailForRoId | RO details - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@getRoDetailForRoId | RO not found');
        return NULL;
    }

    /**
     * @param $condition
     * @return mixed|null
     */
    public function isCancelRequestSentByAm($condition)
    {
        log_message('DEBUG', 'In ro_approval_feature@isCancelRequestSentByAm | is cancel request send by AM for - ' . print_r($condition, true));
        $result = $this->RoCancelExternalRo->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In ro_approval_feature@isCancelRequestSentByAm | cancel request send by AM result - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@isCancelRequestSentByAm | No records found');
        return NULL;
    }

    /**
     * @param $internalRoId
     * @return mixed|null
     */
    public function isApprovedRo($internalRoId)
    {
        $condition = array(array('internal_ro_number', $internalRoId));
        log_message('DEBUG', 'In ro_approval_feature@isApprovedRo | Fetching approved RO details for - ' . print_r($condition, true));
        $result = $this->RoApprovedNetworks->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In ro_approval_feature@isApprovedRo | Approved RO result - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@isApprovedRo | No approved RO found');
        return NULL;
    }

    /**
     * @param $userId
     * @return mixed|null
     */
    public function getUserName($userId)
    {
        $condition = array(array('user_id', $userId));
        log_message('DEBUG', 'In ro_approval_feature@getUserName | Fetching user name for user id - ' . print_r($condition, true));
        $result = $this->RoUser->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In ro_approval_feature@getUserName | user name is  - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@getUserName | No records found');
        return NULL;
    }

    /**
     * @param $brand
     * @return string
     */
    public function getBrandNames($brand)
    {
        $brand = array_map('intval', explode(',', $brand));
        log_message('INFO', 'In ro_approval_feature@getBrandNames | Fetching brand name record for - ' . print_r($brand, true));
        $brandName = '';
        if ($brand != '') {
            $result = $this->SvNewBrand->getColumnsWhereIn('id', $brand, array('*'));

            if ($result->count() > 0) {
                $result = $result->toArray();
                foreach ($result as $brands) {
                    if ($brandName == '') {
                        $brandName .= $brands['brand'];
                    } else {
                        $brandName .= ', ' . $brands['brand'];
                    }
                }
                log_message('INFO', 'In ro_approval_feature@getBrandNames | Brand names fetched successfully - ' . print_r($brandName, true));
                return $brandName;
            }
        }
        log_message('INFO', 'In ro_approval_feature@getBrandNames | No record found');
        return $brandName;
    }

    /**
     * @param $internalRoId
     * @return mixed|null
     */
    public function getRoAmount($internalRoId)
    {
        $condition = array(array('internal_ro_number', $internalRoId));
        log_message('DEBUG', 'In ro_approval_feature@getRoAmount | Fetching RO amount for INTERNAL_RO_ID- ' . print_r($condition, true));
        $result = $this->RoAmount->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In ro_approval_feature@getRoAmount | RO AMOUNT details - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@getRoAmount | RO not found');
        return NULL;
    }

    /**
     * @param $internalRoId
     * @return mixed|null
     */
    public function getAllRoNetworkDetails($internalRoId)
    {
        log_message('DEBUG', 'In ro_approval_feature@getAllRoNetworkDetails | Fetching All Network Related RO details for - ' . print_r(array('internal ro' => $internalRoId), true));
        $result = DB::select("
                        SELECT sc.customer_id,sc.customer_name,sc.revenue_sharing,ssm.sw_market_name,temp.market_id,
		                       temp.channel_id, stc.channel_name,stc.priority,
                               temp.total_ad_seconds,temp.client_name,temp.screen_region_id,temp.scheduled_date,
                               stc.spot_avg, stc.banner_avg
                        FROM (
		                       SELECT  sac.ro_duration*SUM(acsd.impressions) AS total_ad_seconds,
			                           sac.client_name,
			                           sac.channel_id, 
                                       acsd.screen_region_id,
			                           sac.market_id,
                                       acsd.date as scheduled_date
		                       FROM sv_advertiser_campaign AS sac
		                       INNER JOIN sv_advertiser_campaign_screens_dates AS acsd ON sac.campaign_id = acsd.campaign_id
		                       WHERE sac.internal_ro_number = '$internalRoId'
		                       AND acsd.status != 'cancelled'
		                       AND sac.is_make_good = 0
		                       GROUP BY sac.channel_id, acsd.screen_region_id, acsd.date, sac.ro_duration
	                        ) AS temp
                        INNER JOIN sv_tv_channel AS stc ON stc.tv_channel_id = temp.channel_id
                        INNER JOIN sv_customer AS sc ON sc.customer_id = stc.enterprise_id
                        INNER JOIN sv_sw_market AS ssm ON ssm.id = temp.market_id
                        LIMIT 100000"
        );
        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In ro_approval_feature@getAllRoNetworkDetails | All RO network details are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@getAllRoNetworkDetails | NO RO network details found');
        return array();
    }

    /**
     * @param $roId
     * @return mixed|null
     */
    public function dataForRoIdInRoStatus($roId)
    {
        $condition = array(array('am_external_ro_id', $roId));
        log_message('DEBUG', 'In ro_approval_feature@dataForRoIdInRoStatus | Fetching RO status for  - ' . print_r($condition, true));
        $result = $this->RoStatus->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In ro_approval_feature@dataForRoIdInRoStatus | RO status details are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@dataForRoIdInRoStatus | NO RO status details found');
        return NULL;
    }

    /**
     * @param $internalRoId
     * @return mixed|null
     */
    public function getApprovalRequestData($internalRoId)
    {
        $condition = array(array('internal_ro_number', $internalRoId));
        log_message('DEBUG', 'In ro_approval_feature@getApprovalRequestData | Fetching RO Approval Request status for  - ' . print_r($condition, true));
        $result = $this->RoOrders->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In ro_approval_feature@getApprovalRequestData | RO status details are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@getApprovalRequestData | NO RO status details found');
        return NULL;
    }

    /**
     * @param $internalRoId
     * @return bool
     */
    public function isEditingInProgress($internalRoId)
    {
        $condition = array(array('internal_ro_number', $internalRoId));
        log_message('DEBUG', 'In ro_approval_feature@isEditingInProgress | Fetching PG and PP status for - ' . print_r($condition, true));
        $result = DB::table('ro_approved_networks')
            ->select(DB::raw('internal_ro_number AS internalRoId, MIN(pdf_generation_status) AS PG, MAX(pdf_processing) AS PP'))
            ->where($condition)
            ->get();
        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            log_message('DEBUG', 'In ro_approval_feature@isEditingInProgress | PG and PP status is  - ' . print_r($result, true));
            if ($result[0]['PG'] == 1 and $result[0]['PP'] == 0) {
                log_message('DEBUG', 'In ro_approval_feature@isEditingInProgress | PG and PP is finished');
                return false;
            }
        }
        log_message('DEBUG', 'In ro_approval_feature@isEditingInProgress | PG and PP is under processing');
        return true;
    }

    /**
     * @param $internalRoId
     * @return mixed|null
     */
    public function verifyCampaignCreatedForInternalRo($internalRoId)
    {
        $condition = array(array('internal_ro_number', $internalRoId), array('campaign_status', 'pending_approval'), array('approved_status', 'Not_Approved'));
        log_message('DEBUG', 'In ro_approval_feature@verifyCampaignCreatedForInternalRo | Fetching campaign details for - ' . print_r($condition, true));
        $result = $this->SvAdvertiserCampaign->getColumnsWhere($condition, array('*'))->count();
        if ($result > 0) {
            log_message('DEBUG', 'In ro_approval_feature@verifyCampaignCreatedForInternalRo | Total rows are - ' . print_r($result, true));
            return 1;
        }
        log_message('DEBUG', 'In ro_approval_feature@verifyCampaignCreatedForInternalRo | NO Campaign details FOUND');
        return 0;
//        $condition = array(array('sac.internal_ro_number', $internalRoId), array('sac.campaign_status', 'pending_approval'), array('sac.approved_status', 'Not_Approved'));
//        log_message('DEBUG', 'In ro_approval_feature@verifyCampaignCreatedForInternalRo | Fetching campaign details for - ' . print_r($condition, true));
//        $result = DB::table('sv_advertiser_campaign AS sac')
//            ->join('sv_advertiser_campaign_screens_dates AS acsd', 'acsd.campaign_id', '=', 'sac.campaign_id')
//            ->where($condition)
//            ->get()->count();
//        if ($result > 0) {
//            log_message('DEBUG', 'In ro_approval_feature@verifyCampaignCreatedForInternalRo | Total rows are - ' . print_r($result, true));
//            return 1;
//        }
//        log_message('DEBUG', 'In ro_approval_feature@verifyCampaignCreatedForInternalRo | NO Campaign details FOUND');
//        return 0;
    }

    /**
     * @param $roId
     * @return mixed|null
     */
    public function isCancellationRequestSent($roId)
    {
        $whereCondition = array(array('ext_ro_id', $roId), array('cancel_ro_by_admin', 0));
        $whereInCondition = array('whereInColumn' => 'cancel_type', 'whereInData' => array('cancel_ro', 'cancel_market', 'cancel_brand', 'cancel_content'));
        log_message('DEBUG', 'In ro_approval_feature@isCancellationRequestSent | Fetching is cancellation request sent for  - ' . print_r(array($whereCondition, $whereInCondition), true));
        $result = $this->RoCancelExternalRo->getColumnsWhereWhereIn($whereCondition, $whereInCondition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('DEBUG', 'In ro_approval_feature@isCancellationRequestSent | Cancellation details are - ' . print_r($result, true));
            return $result;
        }
        log_message('DEBUG', 'In ro_approval_feature@isCancellationRequestSent | NO cancellation details FOUND');
        return NULL;
    }

    /**
     * @param $internalRoId
     * @param $customerIds
     * @return mixed|null
     */
    public function getRevenueShareForRoCustomer($internalRoId, $customerIds)
    {
        log_message('DEBUG', 'In PreApprovalRoFeature@getRevenueShareForRoCustomer | Fetching Revenue Share for Internal Ro and Customer IDs - ' . print_r(array('internalRoId' => $internalRoId, 'customerIds' => $customerIds), true));
        $result = $this->RoApprovedNetworks->getRevenueShareForRoCustomer($internalRoId, $customerIds);
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('DEBUG', 'In ro_approval_feature@getRevenueShareForRoCustomer | Network Wise details are - ' . print_r($result, true));
            return $result;
        }
        log_message('DEBUG', 'In ro_approval_feature@verifyCampaignCreatedForInternalRo | NO RO details FOUND');
        return NULL;
    }

    /**
     * @param $internalRoId
     * @param $customerIds
     * @param $channelIds
     * @return Collection|mixed|null
     */
    public function isApproved($internalRoId, $customerIds, $channelIds)
    {
        log_message('DEBUG', 'In ro_approval_feature@isApproved | Fetching Approved Network details for - ' . print_r(array('internal ro' => $internalRoId, 'customer id' => $customerIds, 'channel id' => $channelIds), true));
        $result = DB::table('ro_approved_networks AS ran')
            ->select(
                DB::raw('(CASE WHEN ran.total_spot_ad_seconds = 0 THEN svt.spot_avg ELSE ran.channel_spot_avg_rate END) AS channel_spot_avg_rate'),
                DB::raw('(CASE WHEN ran.total_banner_ad_seconds = 0 THEN svt.banner_avg ELSE ran.channel_banner_avg_rate END) AS channel_banner_avg_rate'),
                'ran.customer_id',
                'ran.customer_share',
                'ran.tv_channel_id',
                'ran.total_spot_ad_seconds',
                'ran.total_banner_ad_seconds'
            )
            ->join('sv_tv_channel AS svt', 'svt.tv_channel_id', '=', 'ran.tv_channel_id')
            ->where(array(array('ran.internal_ro_number', $internalRoId)))
            ->whereIn('ran.customer_id', $customerIds)
            ->whereIn('ran.tv_channel_id', $channelIds)
            ->groupBy('ran.tv_channel_id')
            ->get();
        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In ro_approval_feature@isApproved | Approved Network details - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@isApproved | Approved Network Details not found');
        return NULL;
    }

    /**
     * @param $clientName
     * @param $channelName
     * @return mixed|null
     */
    public function getChannelHistoricalRateForClient($clientName, $channelName)
    {
        $condition = array(array('client_name', $clientName), array('channel_name', $channelName));
        $columns = array('id', 'channel_spot_avg_rate', 'channel_banner_avg_rate');

        $orderByCondition = array('orderColumn' => 'id', 'order' => 'DESC');
        $limitCondition = array('offset' => 0, 'limit' => 1);
        log_message('DEBUG', 'In ro_approval_feature@getChannelHistoricalRateForClient | Fetching historical spot and banner rate for  - ' . print_r($condition, true));
        $result = $this->RoApprovedNetworks->getColumnsWhereOrderByLimit($condition, $orderByCondition, $limitCondition, $columns);
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In ro_approval_feature@getChannelHistoricalRateForClient | Historical Rate details are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@getChannelHistoricalRateForClient | NO historical rate details found');
        return NULL;
    }

    /**
     * @param $internalRoId
     * @return Collection|mixed|null
     */
    public function getTotalNetworkPayout($internalRoId)
    {
        $condition = array(array('internal_ro_number', $internalRoId));
        log_message('DEBUG', 'In ro_approval_feature@getTotalNetworkPayout | Fetching total network payout for - ' . print_r($condition, true));
        $result = DB::table('ro_approved_networks')
            ->select(DB::raw('SUM( channel_spot_amount*(customer_share /100 ) + channel_banner_amount*(customer_share /100 )) AS network_payout'))
            ->where($condition)
            ->get();
        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In ro_approval_feature@getTotalNetworkPayout | Total Network Payout details - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In ro_approval_feature@getTotalNetworkPayout | Total Network Payout Details not found');
        return NULL;
    }

    /**
     * @param $roId
     * @return bool
     */
    public function isRoAlreadyApproved($roId)
    {
        log_message('INFO', 'In ro_approval_feature@isRoAlreadyApproved | Inside');
        $count = DB::table('ro_approved_networks AS ran')
            ->select('ran.internal_ro_number')
            ->join('ro_am_external_ro AS raer', 'raer.internal_ro', '=', 'ran.internal_ro_number')
            ->where(array(array('raer.id', $roId)))
            ->get()
            ->count();
        return $count > 0 ? true : false;
    }
}