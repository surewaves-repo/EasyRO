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

class postApprovalRoFeature extends BaseDAL
{
    public function __construct()
    {
        parent::__construct();
        log_message('DEBUG', 'In PostApprovalRoFeature@constructor | Object Created');
    }

    public function updateChannelStatusAsCancel($whereData)
    {
        log_message('DEBUG', 'In PostApprovalRoFeature@updateChannelStatusAsCancel | Entered');
        $result = $this->SvAdvertiserCampaign->getcolumnsWhere($whereData, array('*'));
        if($result->count() > 0){
            log_message('DEBUG', 'In PostApprovalRoFeature@updateChannelStatusAsCancel | Updating Status Cancelled in SAC');
            $updateData = array('campaign_status' => 'cancelled');
            $this->SvAdvertiserCampaign->updateData($whereData,$updateData);
            $result = $result->toArray();
            $campaignIds = array();

            foreach ($result as $val) {
                array_push($campaignIds, $val['campaign_id']);
            }

            log_message('DEBUG', 'In PostApprovalRoFeature@updateChannelStatusAsCancel | Updating Status Cancelled in SACSD');
            $updateData = array('status' => 'cancelled');
            $whereInCondition = array('whereInColumn' => 'campaign_id', 'whereInData' => $campaignIds);
            $this->SvAdvertiserCampaignScreensDates->updateWhereIn($whereInCondition, $updateData);
        }
    }

    public function removeChannelFileLocation($whereData)
    {
        log_message('DEBUG', 'In PostApprovalRoFeature@removeChannelFileLocation | Deleting from RoChannelFileLocation');
        $this->RoChannelFileLocation->deleteWhere($whereData);
    }

    public function getExternalRoReportDetails($internalRoNo)
    {
        log_message('DEBUG', 'In PostApprovalRoFeature@getExternalRoReportDetails | Entered');
        $result = DB::table('sv_advertiser_campaign as ac')
            ->crossJoin('ro_amount as r','ac.internal_ro_number','=','r.internal_ro_number')
            ->select(DB::raw('ac.customer_ro_number, ac.internal_ro_number, ac.client_name, ac.agency_name, MIN(ac.start_date) as start_date,
	                        MAX(ac.end_date) as end_date,
	                        r.ro_amount as gross_ro_amount, r.agency_commission_amount,
	                        r.agency_rebate as agency_rebate, r.agency_rebate_on,
                            (r.marketing_promotion_amount + r.field_activation_amount + r.sales_commissions_amount + r.creative_services_amount +
                            r.other_expenses_amount) as other_expenses'))
            ->where(array
                        (array('r.internal_ro_number', $internalRoNo),
                        array('ac.internal_ro_number',$internalRoNo))
            )
            ->get();
        $result = json_decode(json_encode($result), true);
        if (count($result) > 0) {
            return $result[0];
        }
        return array();
    }

    public function getCustomerDetails($customerID)
    {
        $result = $this->SvCustomer->getColumnsWhereIn('customer_id',$customerID, array('*'));
        if ($result->count() > 0) {
            return $result->toArray();
        }
        return array();
    }

    public function getChannelDetails($channelIds)
    {
        $result = $this->SvTvChannel->getColumnsWhereIn('tv_channel_id',$channelIds, array('*'));
        if ($result->count() > 0) {
            log_message('INFO', 'In PostApprovalRoFeature@getUserEmails | Data Found - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In PostApprovalRoFeature@getUserEmails | Data does not exist');
        return array();
    }

    public function insertIntoRoApprovedNetworks($data)
    {
        log_message('debug', 'In PostApprovalRoFeature@addIntoRoApprovedNetworks | Entered');
        $this->RoApprovedNetworks->insertData($data);
    }

    public function getUserEmails($profileIds, $isTestUser)
    {
        $condition = array('is_test_user' => $isTestUser);
        $whereInCondition = array('whereInColumn' => 'profile_id', 'whereInData' => $profileIds);
        $result = $this->RoUser->getColumnsWhereWhereIn($condition, $whereInCondition, array('*'));
        if ($result->count() > 0) {
            log_message('INFO', 'In PostApprovalRoFeature@getUserEmails | Data Found - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In PostApprovalRoFeature@getUserEmails | Data does not exist');
        return array();
    }

    public function getRoId($externalRO)
    {
        $condition = array('cust_ro' => $externalRO);
        $result = $this->RoAmExternalRo->getcolumnsWhere($condition, array('*'));
        $result = $result->toArray();
        return $result[0];
    }

    public function updateCustomerSharesInSvCustomer($networkShare, $networkId)
    {
        $cond = array('customer_id' => $networkId);
        $data = array('revenue_sharing' => $networkShare);
        $this->SvCustomer->updateData($cond, $data);
    }

    public function checkInRoExternalRoReportDetails($internalRoNo, $customerRoNo)
    {
        $condition = array('customer_ro_number' => $customerRoNo, 'internal_ro_number' => $internalRoNo);
        $result = $this->RoExternalRoReportDetails->getcolumnsWhere($condition, array('*'));
        if($result->count() > 0){
            log_message('INFO', 'In PostApprovalRoFeature@checkInRoExternalRoReportDetails | Data Found - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In PostApprovalRoFeature@checkInRoExternalRoReportDetails | Data does not exist');
        return array();
    }

    public function UpdateInRoExternalRoReportDetails($internalRoNo, $customerRoNo, $data)
    {
        $condition = array('customer_ro_number' => $customerRoNo, 'internal_ro_number' => $internalRoNo);
        $result = $this->RoExternalRoReportDetails->updateData($condition, $data);
        if($result->count() > 0){
            log_message('INFO', 'In PostApprovalRoFeature@checkInRoExternalRoReportDetails | Data Found - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In PostApprovalRoFeature@checkInRoExternalRoReportDetails | Data does not exist');
        return array();
    }

    public function insertIntoRoExternalRoReportDetails($data)
    {
        $this->RoExternalRoReportDetails->insertData($data);
    }

    public function updateRoStatusInRoAmount($internalRoNo, $userId, $approvalDate)
    {
        // update ro_amount set ro_approval_status=1,approval_timestamp = '$date',approval_user_id = $user_id
        // where internal_ro_number = '$order_id'
        $cond = array('internal_ro_number' => $internalRoNo);
        $data = array('ro_approval_status' => 1, 'approval_timestamp' => $approvalDate, 'approval_user_id' => $userId);
        $this->RoAmount->updateData($cond,$data);
    }

    public function updateApprovalStatus($where, $data)
    {
        $this->RoCancelExternalRo->updateData($where, $data);
    }

    public function updateInRoProgressionMailStatus($where, $data)
    {
        $this->RoProgressionMailStatus->updateData($where, $data);
    }

    public function insertRemarks($data)
    {
        $this->RoApprovalRemarks->insertData($data);
    }

    public function getPlaylistId($internalRoNo)
    {
        log_message('DEBUG', 'In PostApprovalRoFeature@getPlaylistId | Entered');
        $result = DB::select("select c.content_id, content_name, content_order, cc.content_duration,
            cc.playlist_content_id, c.content_url, c.duration, ac.approved_status, ac.campaign_status, ac.playlist_id from 
            sv_advertiser_content as c inner join
            sv_advertiser_playlist_contents as cc  on cc.content_id = c.content_id inner join 
            sv_advertiser_campaign as ac on cc.playlist_id = ac.playlist_id
            where ac.approved_status = 'Not_Approved' and ac.campaign_status = 'pending_approval' 
            and ac.internal_ro_number = '$internalRoNo'
            order by content_order");

        $result = json_decode(json_encode($result), true);
        log_message('info','In PostApprovalRoFeature@getPlaylistId | Result=> '.print_r($result,True));
        if (count($result) > 0) {
            return $result;
        }
        return NULL;
    }

    public function updateInSvAdvertiserCampaign($cond, $data)
    {
        $this->SvAdvertiserCampaign->updateData($cond,$data);
    }
}