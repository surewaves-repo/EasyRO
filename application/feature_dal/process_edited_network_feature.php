<?php


namespace application\feature_dal;

use application\repo\BaseDAL;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

include_once APPPATH . 'repo/base_dal.php';


class ProcessEditedNetworkFeature extends BaseDAL
{
    public function __construct()
    {
        parent::__construct();
        log_message('DEBUG', 'In ro_approval_feature@constructor | constructor');
    }

    /**
     * @param $internalRoId
     * @param $networkIds
     * @return mixed|null
     * Ravishankar Singh: 2019-11-23
     */
    public function getRoApprovedNetworkDetails($internalRoId, $networkIds)
    {
        $whereCondition = array(array('internal_ro_number', $internalRoId));
        $whereInCondition = array('whereInColumn' => 'customer_id', 'whereInData' => $networkIds);
        log_message('INFO', 'In process_edited_network_feature@getRoApprovedNetworkDetails | Fetching records for - ' . print_r(array($whereCondition, $whereInCondition), true));
        $result = $this->RoApprovedNetworks->getColumnsWhereWhereIn($whereCondition, $whereInCondition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In process_edited_network_feature@getRoApprovedNetworkDetails | roApprovedNetwork details are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In rprocess_edited_network_feature@getRoApprovedNetworkDetails | NO roApprovedNetwork details FOUND');
        return NULL;
    }

//    /**
//     * @param $updateData
//     * @param $internalRoId
//     * @param $networkIds
//     * Ravishankar Singh: 2019-11-23
//     */
//    public function lockPdfGenerationProcessForEditedNetworks($updateData, $internalRoId, $networkIds)
//    {
//        $whereCondition = array(array('internal_ro_number', $internalRoId));
//        $whereInCondition = array('whereInColumn' => 'customer_id', 'whereInData' => $networkIds);
//        log_message('INFO', 'In process_edited_network_feature@lockPdfGenerationProcessForEditedNetworks | Updating with - ' . print_r(array($updateData, $whereCondition, $whereInCondition), true));
//        $this->RoApprovedNetworks->updateWhereWhereIn($whereCondition, $whereInCondition, $updateData);
//        log_message('INFO', 'In process_edited_network_feature@lockPdfGenerationProcessForEditedNetworks | Record Updated successfully');
//    }

    /**
     * @param $cancelledNonApprovedChannelIds
     * @param $internalRoId
     * Ravishankar Singh: 2019-11-23
     * fetches campaignIds corresponding to cancelled channels then updates campaign_status/status to cancelled in sv_advertiser_campaign and sv_advertiser_campaign_screen_dates
     */
    public function cancelChannelsBeforeApproval($cancelledNonApprovedChannelIds, $internalRoId)
    {
        $whereCondition = array(array('internal_ro_number', $internalRoId));
        $whereInCondition = array('whereInColumn' => 'channel_id', 'whereInData' => $cancelledNonApprovedChannelIds);
        log_message('INFO', 'In process_edited_network_feature@cancelChannelBeforeApproval | Fetching where - ' . print_r(array($whereCondition, $whereInCondition), true));
        $result = $this->SvAdvertiserCampaign->getColumnsWhereWhereIn($whereCondition, $whereInCondition, array('campaign_id'));
        if ($result->count() > 0) {
            $result = $result->toArray();

            log_message('INFO', 'In process_edited_network_feature@cancelChannelBeforeApproval | Updating campaign_status = cancelled in SAC');
            $updateData = array('campaign_status' => 'cancelled');
            $this->SvAdvertiserCampaign->updateWhereWhereIn($whereCondition, $whereInCondition, $updateData);

            $campaignIds = array();
            foreach ($result as $val) {
                array_push($campaignIds, $val['campaign_id']);
            }

            $updateData = array('status' => 'cancelled');
            $whereInCondition = array('whereInColumn' => 'campaign_id', 'whereInData' => $campaignIds);
            log_message('INFO', 'In process_edited_network_feature@cancelChannelBeforeApproval | Updating status = cancelled in SACSD');
            $this->SvAdvertiserCampaignScreensDates->updateWhereIn($whereInCondition, $updateData);
            log_message('INFO', 'In process_edited_network_feature@cancelChannelBeforeApproval | Record Updated successfully');
        }

    }

    /**
     * @param $updateData
     * @param $whereCondition
     * Ravishankar Singh: 2019-11-23
     */
    public function updateEditedDetailsInRoApprovedNetworks($updateData, $whereCondition)
    {
        log_message('INFO', 'In process_edited_network_feature@updateEditedDetailsInRoApprovedNetworks | Updating with - ' . print_r(array('updateWith' => $updateData, 'whereCondition' => $whereCondition), true));
        $this->RoApprovedNetworks->updateData($whereCondition, $updateData);
        log_message('INFO', 'In process_edited_network_feature@updateEditedDetailsInRoApprovedNetworks | Data updated successfully');
    }

    /**
     * @param $insertData
     * Ravishankar Singh: 2019-11-23
     */
    public function insertEditedDetailsInRoApprovedNetworks($insertData)
    {
        log_message('INFO', 'In process_edited_network_feature@insertEditedDetailsInRoApprovedNetworks | Inserting record - ' . print_r($insertData, true));
        $this->RoApprovedNetworks->insertData($insertData);
        log_message('INFO', 'In process_edited_network_feature@insertEditedDetailsInRoApprovedNetworks | Record inserted successfully');
    }

    /**
     * @param $nonApprovedNetworkIds
     * @return mixed|null
     * Ravishankar Singh: 2019-11-23
     * fetches the details of networks which are added due to addition of new channels which don't belong to existing network in RO
     */
    public function getNonApprovedNetworkDetails($nonApprovedNetworkIds)
    {
        log_message('INFO', 'In process_edited_network_feature@getNonApprovedNetworkDetails | Fetching NON approved network details for  - ' . print_r($nonApprovedNetworkIds, true));
        $columns = array('customer_id', 'customer_name', 'customer_location', 'billing_name');
        $result = $this->SvCustomer->getColumnsWhereIn('customer_id', $nonApprovedNetworkIds, $columns);
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In process_edited_network_feature@getNonApprovedNetworkDetails | non approved network details are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In process_edited_network_feature@getNonApprovedNetworkDetails | NO details FOUND');
        return NULL;
    }

    /**
     * @param $customerId
     * @param $internalRoId
     * @return Collection|mixed|null
     * Ravishankar Singh: 2019-11-23
     * fetches details of invoice report status and this data is later used to make changes in invoice
     */
    public function getAllNetworkInfo($customerId, $internalRoId)
    {
        log_message('DEBUG', 'In process_edited_network_feature@getAllNetworkInfo | Fetching Network details for - ' . print_r(array('internal ro' => $internalRoId, 'customer id' => $customerId), true));
        $result = DB::table('ro_approved_networks AS ran')
            ->select(
                'rnr.customer_ro_number', 'rnr.internal_ro_number', 'rnr.network_ro_number',
                'rnr.customer_name', 'rnr.start_date', 'rnr.end_date', 'rnr.release_date',
                'rnr.gross_network_ro_amount', 'rnr.customer_share', 'ran.customer_id',
                'ran.billing_name', 'ran.revision_no', 'rnr.client_name',
                DB::raw('GROUP_CONCAT(ran.channel_name) AS channel_names')
            )
            ->join('ro_network_ro_report_details AS rnr', function ($join) {
                $join->on('ran.internal_ro_number', '=', 'rnr.internal_ro_number')
                    ->on('ran.customer_name', '=', 'rnr.customer_name');
            })
            ->where(array(array('ran.customer_id', $customerId), array('ran.internal_ro_number', $internalRoId)))
            ->groupBy('rnr.network_ro_number')
            ->get();
        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In process_edited_network_feature@getAllNetworkInfo | Network details are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In process_edited_network_feature@getAllNetworkInfo | Details not found');
        return array();
    }

    /**
     * @param $channelNames
     * @param $internalRoId
     * @return Collection|mixed|null
     * Ravishankar Singh: 2019-11-23
     * fetches the market names of the channels i.e. channels belong to which market
     */
    public function getScheduledMarketForChannel($channelNames, $internalRoId)
    {
        log_message('INFO', 'In process_edited_network_feature@getScheduledMarketForChannel | Fetching Scheduled market for channel - ' . print_r(array($channelNames, $internalRoId), true));
        $result = DB::table('sv_advertiser_campaign AS sac')
            ->select('stc.tv_channel_id', 'stc.channel_name', 'sm.id as market_id', 'sm.sw_market_name as market_name')
            ->join('sv_tv_channel AS stc', 'sac.channel_id', '=', 'stc.tv_channel_id')
            ->join('sv_market_x_channel AS smc', 'stc.tv_channel_id', '=', 'smc.channel_fk_id')
            ->join('sv_sw_market AS sm', function ($join) {
                $join->on('sm.id', '=', 'smc.market_fk_id')
                    ->on('sm.id', '=', 'sac.market_id');
            })
            ->distinct()
            ->where(array(array('sac.internal_ro_number', $internalRoId), array('sac.campaign_status', '!=', 'cancelled')))
            ->whereIn('stc.channel_name', $channelNames)
            ->orderBy('sm.sw_market_name')
            ->get();
        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In process_edited_network_feature@getScheduledMarketForChannel | Scheduled market for channels are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In process_edited_network_feature@getScheduledMarketForChannel | Details not found');
        return array();
    }

    /**
     * @param $networkRoId
     * @return mixed|null
     * Ravishankar Singh: 2019-11-23
     * fetches the invoice details of a network
     */
    public function checkForPresenceOfInvoiceData($networkRoId)
    {
        $condition = array(array('network_ro_number', $networkRoId));
        log_message('DEBUG', 'In process_edited_network_feature@checkForPresenceOfInvoiceData | Checking presence of invoice data for - ' . print_r($condition, true));
        $result = $this->RoCancelInvoice->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In process_edited_network_feature@checkForPresenceOfInvoiceData | Invoice details are - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In process_edited_network_feature@checkForPresenceOfInvoiceData | NO invoice details found');
        return NULL;
    }

    /**
     * @param $updateData
     * Ravishankar Singh: 2019-11-23
     */
    public function updateInvoiceCancelData($updateData)
    {
        $whereCondition = array(array('network_ro_number', $updateData['network_ro_number']));
        log_message('INFO', 'In process_edited_network_feature@updateInvoiceCancelData | Updating with - ' . print_r(array('updateWith' => $updateData, 'whereCondition' => $whereCondition), true));
        $this->RoCancelInvoice->updateData($whereCondition, $updateData);
        log_message('INFO', 'In process_edited_network_feature@updateInvoiceCancelData | Data updated successfully');
    }

    /**
     * @param $insertData
     * Ravishankar Singh: 2019-11-23
     */
    public function insertInvoiceCancelData($insertData)
    {
        log_message('INFO', 'In process_edited_network_feature@insertInvoiceCancelData | Inserting record - ' . print_r($insertData, true));
        $this->RoCancelInvoice->insertData($insertData);
        log_message('INFO', 'In process_edited_network_feature@insertInvoiceCancelData | Record inserted successfully');
    }

    /**
     * @param $internalRoId
     * @return array|null
     * Ravishankar Singh: 2019-11-23
     */
    public function getExternalRoReportDetails($internalRoId)
    {
        log_message('DEBUG', 'In process_edited_network_feature@getExternalRoReportDetails | Fetching External ro report details for internalro- ' . print_r($internalRoId, true));
        $result = DB::table('sv_advertiser_campaign AS ac')
            ->select('ac.customer_ro_number', 'ac.internal_ro_number', 'ac.client_name',
                'ac.agency_name', 'r.ro_amount as gross_ro_amount', 'r.agency_commission_amount',
                'r.agency_rebate as agency_rebate', 'r.agency_rebate_on',
                DB::raw('(r.marketing_promotion_amount + r.field_activation_amount + r.sales_commissions_amount + r.creative_services_amount + r.other_expenses_amount) as other_expenses'),
                DB::raw('MIN(ac.start_date) as start_date'),
                DB::raw('MAX(ac.end_date) as end_date'))
            ->crossJoin('ro_amount AS r')
            ->where(array(array('r.internal_ro_number', $internalRoId), array('ac.internal_ro_number', $internalRoId)))
            ->get();
        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In process_edited_network_feature@getExternalRoReportDetails | External Ro Report Details are - ' . print_r($result, true));
            return $result[0];
        }
        log_message('INFO', 'In process_edited_network_feature@getExternalRoReportDetails | NO External Ro Report Details found');
        return array();
    }

    /**
     * @param $internalRoId
     * @return array|null
     * Ravishankar Singh: 2019-11-23
     * fetches network_payout and total_ad_seconds(spot+banner)
     */
    public function getTotalNetworkPayoutAndTotalNetworkSeconds($internalRoId)
    {
        log_message('DEBUG', 'In process_edited_network_feature@getTotalNetworkPayoutAndTotalNetworkSeconds | Fetching RO network payout and total seconds for internal ro - ' . print_r($internalRoId, true));
        $result = DB::table('ro_approved_networks')
            ->select(
                DB::raw('SUM(channel_spot_amount * (customer_share/100) + channel_banner_amount * (customer_share/100)) AS network_payout'),
                DB::raw('SUM(total_spot_ad_seconds + total_banner_ad_seconds) AS total_scheduled_seconds'))
            ->where(array(array('internal_ro_number', $internalRoId)))
            ->get();
        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In process_edited_network_feature@getTotalNetworkPayoutAndTotalNetworkSeconds | Network Payout and Total Second details are - ' . print_r($result, true));
            return $result[0];
        }
        log_message('INFO', 'In process_edited_network_feature@getTotalNetworkPayoutAndTotalNetworkSeconds | NO External Ro Report Details found');
        return array();
    }

    /**
     * @param $updateData
     * @param $internalRoId
     * Ravishankar Singh: 2019-11-23
     */
    public function updateExternalRoReportDetails($updateData, $internalRoId)
    {
        $whereCondition = array(array('internal_ro_number', $internalRoId));
        log_message('INFO', 'In process_edited_network_feature@updateExternalRoReportDetails | Updating with - ' . print_r(array('updateWith' => $updateData, 'whereCondition' => $whereCondition), true));
        $this->RoExternalRoReportDetails->updateData($whereCondition, $updateData);
        log_message('INFO', 'In process_edited_network_feature@updateExternalRoReportDetails | Data updated successfully');
    }

    /**
     * @param $internalRoId
     * @param $cancelledApprovedChannelId
     * @return array
     * Ravishankar Singh: 2019-11-23
     * Update status=cancelled in sv_advertiser_campaign_screens_dates for cancelled channels which were once approved. Campaigns are cancelled after 2 days from the date of request
     */
    public function updateSvAdvertiserCampaignScreensDates($internalRoId, $cancelledApprovedChannelId)
    {
        $whereCondition = array(array('internal_ro_number', $internalRoId), array('channel_id', $cancelledApprovedChannelId), array('is_make_good', 0), array('campaign_status', '!=', 'cancelled'));
        log_message('INFO', 'In process_edited_network_feature@updateSvAdvertiserCampaignScreensDates | Fetching where - ' . print_r(array($whereCondition), true));
        $result = $this->SvAdvertiserCampaign->getColumnsWhere($whereCondition, array('campaign_id'));
        if ($result->count() > 0) {
            $result = $result->toArray();

            $campaignIds = array();
            foreach ($result as $val) {
                array_push($campaignIds, $val['campaign_id']);
            }

            $updateData = array('status' => 'cancelled');
            $whereCondition = array(array('date', '>=', DATE_OF_CHANNEL_CANCEL));
            $whereInCondition = array('whereInColumn' => 'campaign_id', 'whereInData' => $campaignIds);
            log_message('INFO', 'In process_edited_network_feature@updateSvAdvertiserCampaignScreensDates | Updating status = cancelled in SACSD');
            $this->SvAdvertiserCampaignScreensDates->updateWhereWhereIn($whereCondition, $whereInCondition, $updateData);
            log_message('INFO', 'In process_edited_network_feature@cancelChannelBeforeApproval | Record Updated successfully');

            return $campaignIds;
        }
        log_message('INFO', 'In process_edited_network_feature@cancelChannelBeforeApproval | NO records to update');
        return array();
    }

    /**
     * @param $insertData
     * Ravishankar Singh: 2019-11-23
     */
    public function insertIntoCancelChannel($insertData)
    {
        log_message('INFO', 'In process_edited_network_feature@insertIntoCancelChannel | Inserting record - ' . print_r($insertData, true));
        $this->RoCancelChannel->insertData($insertData);
        log_message('INFO', 'In process_edited_network_feature@insertIntoCancelChannel | Record inserted successfully');
    }

    /**
     * @param $campaignIds
     * @return array|Collection|mixed
     * Ravishankar Singh: 2019-11-23
     */
    public function getChannelImpressions($campaignIds)
    {
        log_message('INFO', 'In process_edited_network_feature@getChannelImpressions | Fetching total ad seconds of campaignIDs' . print_r($campaignIds, true));
        $results = DB::table('sv_advertiser_campaign_screens_dates AS acsd')
            ->select(
                DB::raw('(ac.ro_duration * SUM(acsd.impressions )) as total_ad_seconds'),
                'acsd.screen_region_id'
            )
            ->join('sv_advertiser_campaign AS ac', 'ac.campaign_id', '=', 'acsd.campaign_id')
            ->where(array(array('acsd.status', 'scheduled')))
            ->whereIn('ac.campaign_id', $campaignIds)
            ->whereIn('acsd.screen_region_id', array(1, 3))
            ->groupBy('ac.campaign_id', 'acsd.screen_region_id')
            ->get();
        if (count($results) > 0) {
            $results = json_decode(json_encode($results), true);
            log_message('INFO', 'In process_edited_network_feature@getChannelImpressions | Network Payout and Total Second details are - ' . print_r($results, true));
            return $results;
        }
        log_message('INFO', 'In process_edited_network_feature@getChannelImpressions | NO impressions Details found');
        return array();
    }

    /**
     * @param $internalRoId
     * @param $customerId
     * @return bool
     * Ravishankar Singh: 2019-11-23
     */
    public function checkEndDateCrossedForROCustomerId($internalRoId, $customerId)
    {
        log_message('INFO', 'In process_edited_network_feature@checkEndDateCrossedForROCustomerId | Has end date passed for - ' . print_r(array('internalRoId' => $internalRoId, 'customerId' => $customerId), true));
        $result = DB::table('sv_advertiser_campaign AS sac')
            ->select(DB::raw('MAX(sacsd.date) as end_date'))
            ->join('sv_advertiser_campaign_screens_dates AS sacsd', 'sac.campaign_id', '=', 'sacsd.campaign_id')
            ->where(array(array('sac.internal_ro_number', $internalRoId), array('sacsd.enterprise_id', $customerId)))
            ->get();

        if (count($result) > 0) {
            $result = json_decode(json_encode($result), true);
            $endDate = substr($result[0]['end_date'], 0, 11);
            $dateAfterNextTwoDays = date("Y-m-d", strtotime("+2 days"));
            if (strtotime($dateAfterNextTwoDays) > strtotime($endDate)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }
}