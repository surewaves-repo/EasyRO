<?php


namespace application\services\feature_services;

use application\feature_dal\ProcessEditedNetworkFeature;
use application\services\common_services\UpdateApprovalStatusAndCampaignStatusService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;

include_once APPPATH . 'feature_dal/process_edited_network_feature.php';
include_once APPPATH . 'services/common_services/update_approval_status_and_campaign_status_service.php';

class ProcessEditedNetworkService
{
    private $CI;
    private $processEditedNetworkFeatureObj;
    private $updateApprovalStatusAndCampaignStatusObj;

    public function __construct()
    {
        log_message('DEBUG', 'In process_edited_network@constructor | Constructor');
        $this->CI = &get_instance();
        $this->CI->load->library('session');
        $this->processEditedNetworkFeatureObj = new ProcessEditedNetworkFeature();
        $this->updateApprovalStatusAndCampaignStatusObj = new updateApprovalStatusAndCampaignStatusService();
    }

    /**
     * Ravishankar Singh: 2019-11-23
     * This function processes the channels which are edited/added/cancelled after approval is done.
     */
    public function processEditedNetwork()
    {
        log_message('DEBUG', 'In process_edited_network_service@processEditedNetwork | Inside processEditedNetwork');

        $editedNetworkDetails = $this->CI->input->post('networks');
        log_message('DEBUG', 'In process_edited_network_service@processEditedNetwork | Edited Network Details - ' . print_r($editedNetworkDetails, true));

        if(!isset($editedNetworkDetails) || empty($editedNetworkDetails)){
            log_message('DEBUG', 'In process_edited_network_service@processEditedNetwork | Edited network details is empty. Nothing to update!!');
            return array('Status' => 'fail', 'Message' => 'NO changes were done !!');
        }

        $loggedInUser = $this->CI->session->userdata['logged_in_user'];
        $userId = $loggedInUser[0]['user_id'];
        log_message('DEBUG', 'In process_edited_network_service@processEditedNetwork | Logged in USER ID - ' . print_r($userId, true));

        $internalRoId = $this->CI->input->post('internalRoId');
        $clientName = $this->CI->input->post('client_name');
        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Internal RO Id, Client Name - ' . print_r(array($internalRoId, $clientName), true));

        $data = $this->extractNetworkAndChannelIds($editedNetworkDetails);
        $editedNetworkIds = $data['editedNetworkIds'];
        $editedChannelIds = $data['editedChannelIds'];
        $cancelledChannelIds = $data['cancelledChannelIds'];
        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Edited channel/network IDs - ' . print_r($data, true));


        $approvedRoDetails = $this->approvedRoDetails($internalRoId, $editedNetworkIds, $cancelledChannelIds);
        $approvedChannelIds = $approvedRoDetails['approvedChannelIds'];
        $approvedNetworkIds = $approvedRoDetails['approvedNetworkIds'];
        $approvedNetworkDetails = $approvedRoDetails['approvedNetworkDetails'];
        $cancelledApprovedChannelIds = $approvedRoDetails['cancelledApprovedChannelIds'];
        $cancelledApprovedChannelDetails = $approvedRoDetails['cancelledApprovedChannelsDetails'];
        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Channels Already Existing in ro_approved_networks - ' . print_r($approvedRoDetails, true));

        $cancelledNonApprovedChannelIds = array_diff($cancelledChannelIds, $cancelledApprovedChannelIds);
        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | NEW Channels in ALL NETWORKS which are cancelled  - ' . print_r($cancelledNonApprovedChannelIds, true));

        $nonApprovedNetworkDetails = $this->fetchNonApprovedNetworkDetails(array_diff($editedNetworkIds, $approvedNetworkIds));
        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | NEW (non-approved)network details - ' . print_r($nonApprovedNetworkDetails, true));

//        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Updating PDF_PROCESSING = 1 to LOCK PDF GENERATION while updating channel details');
//        $this->processEditedNetworkFeatureObj->lockPdfGenerationProcessForEditedNetworks(array('pdf_processing' => 1), $internalRoId, $editedNetworkIds);

        try {
            DB::beginTransaction();
            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | cancelling NON-APPROVED(new channels which are cancelled) channels from SAC & SACSD');
            $this->processEditedNetworkFeatureObj->cancelChannelsBeforeApproval($cancelledNonApprovedChannelIds, $internalRoId);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return array('Status' => 'fail', 'Message' => 'Database exception occurred!');
        }

        foreach ($editedNetworkDetails as $network) {
            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | (foreach) NETWORK from $_POST - ' . print_r($network, true));
            $customerId = $network['network_id'];
            $customerShare = $network['network_share'];

            if (array_key_exists('nw_' . $customerId, $approvedNetworkDetails)) {
                log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Processing EXISTING NETWORK - ' . $customerId);
                $cancelledApprovedChannelIdsForCurrentNetwork = array();
                $oldRevisionNumber = $approvedNetworkDetails['nw_' . $customerId]['revision_no'];
                $newRevisionNumber = $oldRevisionNumber + 1;
                log_message('INFO', 'In process_edited_network_service@processEditedNetwork | OLD REVISION NUMBER for CUSTOMER_ID: ' . $customerId . ' - ' . print_r($oldRevisionNumber, true));
                log_message('INFO', 'In process_edited_network_service@processEditedNetwork | NEW REVISION NUMBER for CUSTOMER_ID: ' . $customerId . ' - ' . print_r($newRevisionNumber, true));

                $pdfGenerationStatus = $approvedNetworkDetails['nw_' . $customerId]['pdf_generation_status'];
                log_message('INFO', 'In process_edited_network_service@processEditedNetwork | PDF GENERATION STATUS for CUSTOMER_ID: ' . $customerId . ' - ' . print_r($pdfGenerationStatus, true));

                $networkFinalInfo = $this->initiateInvoiceCancelProcess($customerId, $internalRoId);
                log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Invoice cancel initiation details for CUSTOMER_ID: ' . $customerId . ' - ' . print_r($networkFinalInfo, true));

                // this variable checks if channels in current network are only for cancellation. If a channel is edited/new channel is added in current
                // network then this variable becomes false.
                $areChannelsCancelledOnly = true;
                try {
                    DB::beginTransaction();
                    foreach ($network['channels_data_array'] as $channel) {

                        $channelId = $channel['channel_id'];

                        if (in_array($channelId, $cancelledApprovedChannelIds)) {
                            array_push($cancelledApprovedChannelIdsForCurrentNetwork, $channelId);
                            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Channel is cancelled - ' . print_r($cancelledApprovedChannelIdsForCurrentNetwork, true));
                            continue;
                        }

                        if (in_array($channelId, $approvedChannelIds)) {
                            $areChannelsCancelledOnly = false;
                            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | EXISTING CHANNEL in EXISTING NETWORK is edited - ' . print_r($channelId, true));

                            $whereCondition = array(array('tv_channel_id', $channelId), array('internal_ro_number', $internalRoId), array('customer_id', $customerId));
                            $updateData = array(
                                'total_spot_ad_seconds' => (float)$channel['total_spot_ad_seconds'],
                                'total_banner_ad_seconds' => (float)$channel['total_banner_ad_seconds'],
                                'channel_spot_avg_rate' => (float)$channel['channel_spot_avg_rate'],
                                'channel_banner_avg_rate' => (float)$channel['channel_banner_avg_rate'],
                                'channel_spot_amount' => (float)$channel['channel_spot_amount'],
                                'channel_banner_amount' => (float)$channel['channel_banner_amount']
                            );
                            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Updating EXISTING CHANNEL(' . $channelId . ') in EXISTING NETWORK');
                            $this->processEditedNetworkFeatureObj->updateEditedDetailsInRoApprovedNetworks($updateData, $whereCondition);
                        } else {
                            $areChannelsCancelledOnly = false;
                            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | NEW CHANNEL in EXISTING NETWORK is edited - ' . print_r($channelId, true));

                            $insertData = array(
                                'internal_ro_number' => $internalRoId,
                                'client_name' => $approvedNetworkDetails['nw_' . $customerId]['client_name'],
                                'customer_id' => $approvedNetworkDetails['nw_' . $customerId]['customer_id'],
                                'customer_name' => $approvedNetworkDetails['nw_' . $customerId]['customer_name'],
                                'customer_share' => $customerShare,
                                'customer_location' => $approvedNetworkDetails['nw_' . $customerId]['customer_location'],
                                'tv_channel_id' => $channelId,
                                'channel_name' => $channel['channel_name'],
                                'total_spot_ad_seconds' => (float)$channel['total_spot_ad_seconds'],
                                'total_banner_ad_seconds' => (float)$channel['total_banner_ad_seconds'],
                                'channel_spot_avg_rate' => (float)$channel['channel_spot_avg_rate'],
                                'channel_banner_avg_rate' => (float)$channel['channel_banner_avg_rate'],
                                'channel_spot_amount' => (float)$channel['channel_spot_amount'],
                                'channel_banner_amount' => (float)$channel['channel_banner_amount'],
                                'channel_approval_status' => 1,
                                'pdf_generation_status' => 0,
                                'billing_name' => $approvedNetworkDetails['nw_' . $customerId]['billing_name'],
                                'revision_no' => $approvedNetworkDetails['nw_' . $customerId]['revision_no'],
                            );
                            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Inserting NEW CHANNEL(' . $channelId . ') in EXISTING NETWORK');
                            $this->processEditedNetworkFeatureObj->insertEditedDetailsInRoApprovedNetworks($insertData);
                        }
                    } // end of foreach CHANNEL loop

                    $this->cancelChannelsAfterApprovalNetworkWise($cancelledApprovedChannelIdsForCurrentNetwork, $cancelledApprovedChannelDetails, $internalRoId, $customerId, $userId, $areChannelsCancelledOnly, $newRevisionNumber, $networkFinalInfo);

                    if (!$areChannelsCancelledOnly) {
                        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Updating records NETWORK WISE');
                        $whereCondition = array(array('internal_ro_number', $internalRoId), array('customer_id', $customerId));
                        $updatePdfRevision = array(
                            'customer_share' => $customerShare,
                            'revision_no' => $newRevisionNumber,
                            'pdf_generation_status' => 0,
                            'pdf_processing' => 0
                        );
                        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Updating REVISION DETAILS in EXISTING NETWORK - ' . $customerId);
                        $this->processEditedNetworkFeatureObj->updateEditedDetailsInRoApprovedNetworks($updatePdfRevision, $whereCondition);
                        $this->updatingInvoiceCancelProcess($networkFinalInfo);
                    }
                    DB::commit();
                } catch (QueryException $e) {
                    log_message('ERROR', 'In process_edited_network_service@processEditedNetwork | Rolling Back : Database query exception occurred - ' . print_r($e->getMessage(), true));
                    DB::rollback();
                    return array('Status' => 'fail', 'Message' => 'Database exception occurred!');
                }
            } else {
                log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Processing added NEW NETWORK - ' . $customerId);
                //revision number is set to  0 , as it is new network so its pdf has not been generated
                $newRevisionNumber = 0;
                try {
                    DB::beginTransaction();
                    foreach ($network['channels_data_array'] as $channel) {
                        $channelId = $channel['channel_id'];

                        if (in_array($channelId, $cancelledChannelIds)) {
                            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Channel is cancelled - ' . print_r($channelId, true));
                            continue;
                        }
                        $insertData = array(
                            'internal_ro_number' => $internalRoId,
                            'client_name' => $clientName,
                            'customer_id' => $customerId,
                            'customer_name' => $nonApprovedNetworkDetails['nw_' . $customerId]['customer_name'],
                            'customer_share' => $customerShare,
                            'customer_location' => $nonApprovedNetworkDetails['nw_' . $customerId]['customer_location'],
                            'tv_channel_id' => $channelId,
                            'channel_name' => $channel['channel_name'],
                            'total_spot_ad_seconds' => (float)$channel['total_spot_ad_seconds'],
                            'total_banner_ad_seconds' => (float)$channel['total_banner_ad_seconds'],
                            'channel_spot_avg_rate' => (float)$channel['channel_spot_avg_rate'],
                            'channel_banner_avg_rate' => (float)$channel['channel_banner_avg_rate'],
                            'channel_spot_amount' => (float)$channel['channel_spot_amount'],
                            'channel_banner_amount' => (float)$channel['channel_banner_amount'],
                            'channel_approval_status' => 1,
                            'pdf_generation_status' => 0,  // transaction will handle locking
                            'billing_name' => $nonApprovedNetworkDetails['nw_' . $customerId]['billing_name'],
                            'revision_no' => $newRevisionNumber,
                            'pdf_processing' => 0 // transaction will handle locking
                        );
                        log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Inserting NEW CHANNEL(' . $channelId . ') in NEW NETWORK');
                        $this->processEditedNetworkFeatureObj->insertEditedDetailsInRoApprovedNetworks($insertData);
                    }
                    DB::commit();
                } catch (QueryException $e) {
                    log_message('ERROR', 'In process_edited_network_service@processEditedNetwork | Rolling Back : Database query exception occurred - ' . print_r($e->getMessage(), true));
                    DB::rollback();
                    return array('Status' => 'fail', 'Message' => 'Database exception occurred!');
                }
            }
        }// foreach NETWORK
        try {
            DB::beginTransaction();
            log_message('INFO', 'network edit looking for internal_ro_number -' . $internalRoId);
            $this->updateIntoExternalRoReportDetail($internalRoId);
            $this->updateApprovalStatusAndCampaignStatusObj->updateApprovalStatusAndCampaignStatus($internalRoId);
            DB::commit();
            return array('Status' => 'success', 'Message' => 'Network Edited Successfully!');
        } catch (QueryException $e) {
            log_message('ERROR', 'In process_edited_network_service@processEditedNetwork | Rolling Back : Database query exception occurred - ' . print_r($e->getMessage(), true));
            DB::rollback();
            return array('Status' => 'fail', 'Message' => 'Database exception occurred!');
        }
    }

    /**
     * @param $editedNetworkDetails
     * @return array
     * Ravishankar Singh: 2019-11-23
     * Extract the networkIDs/ channelIds from the JSON received from UI
     */
    public function extractNetworkAndChannelIds($editedNetworkDetails)
    {
        log_message('DEBUG', 'In process_edited_network_service@extractNetworkAndChannelIds | Inside extractNetworkAndChannelIds');
        $editedNetworkIds = array();
        $editedChannelIds = array();
        $cancelledChannelIds = array();
        foreach ($editedNetworkDetails as $network) {
            array_push($editedNetworkIds, $network['network_id']);
            foreach ($network['channels_data_array'] as $channel) {
                array_push($editedChannelIds, $channel['channel_id']);
                if ($channel['cancel_channel'] == '1') {
                    array_push($cancelledChannelIds, $channel['channel_id']);
                }
            }
        }
        log_message('DEBUG', 'In process_edited_network_service@extractNetworkAndChannelIds | Leaving extractNetworkAndChannelIds');
        return array('editedNetworkIds' => $editedNetworkIds, 'editedChannelIds' => $editedChannelIds, 'cancelledChannelIds' => $cancelledChannelIds);
    }

    /**
     * @param $internalRoId
     * @param $editedNetworkIds
     * @param $cancelledChannelIds
     * @return array
     * Ravishankar Singh: 2019-11-23
     * Fetches the details of edited channels which are approved and categorises them.
     */
    public function approvedRoDetails($internalRoId, $editedNetworkIds, $cancelledChannelIds)
    {
        log_message('DEBUG', 'In process_edited_network_service@roApprovedNetworkDetails | Inside roApprovedNetworkDetails');
        $channelWiseDetails = $this->processEditedNetworkFeatureObj->getRoApprovedNetworkDetails($internalRoId, $editedNetworkIds);

        $approvedChannelIds = array();
        $approvedNetworkIds = array();
        $approvedNetworkDetails = array();
        $cancelledApprovedChannelIds = array();
        $cancelledApprovedChannelDetails = array();
        foreach ($channelWiseDetails as $channelDetails) {
            array_push($approvedChannelIds, $channelDetails['tv_channel_id']);
            if (in_array($channelDetails['tv_channel_id'], $cancelledChannelIds)) {
                $cancelledApprovedChannelDetails['ch_' . $channelDetails['tv_channel_id']] = $channelDetails;
                array_push($cancelledApprovedChannelIds, $channelDetails['tv_channel_id']);
            }
            if (!array_key_exists('nw_' . $channelDetails['customer_id'], $approvedNetworkDetails)) {
                $approvedNetworkDetails['nw_' . $channelDetails['customer_id']]['customer_id'] = $channelDetails['customer_id'];
                $approvedNetworkDetails['nw_' . $channelDetails['customer_id']]['customer_name'] = $channelDetails['customer_name'];
                $approvedNetworkDetails['nw_' . $channelDetails['customer_id']]['customer_location'] = $channelDetails['customer_location'];
                $approvedNetworkDetails['nw_' . $channelDetails['customer_id']]['client_name'] = $channelDetails['client_name'];
                $approvedNetworkDetails['nw_' . $channelDetails['customer_id']]['billing_name'] = $channelDetails['billing_name'];
                $approvedNetworkDetails['nw_' . $channelDetails['customer_id']]['pdf_generation_status'] = $channelDetails['pdf_generation_status'];
                $approvedNetworkDetails['nw_' . $channelDetails['customer_id']]['revision_no'] = $channelDetails['revision_no'];
                array_push($approvedNetworkIds, $channelDetails['customer_id']);
            }
        }
        log_message('DEBUG', 'In process_edited_network_service@roApprovedNetworkDetails | Leaving roApprovedNetworkDetails');
        return array('approvedChannelIds' => $approvedChannelIds, 'approvedNetworkIds' => $approvedNetworkIds, 'approvedNetworkDetails' => $approvedNetworkDetails, 'cancelledApprovedChannelsDetails' => $cancelledApprovedChannelDetails, 'cancelledApprovedChannelIds' => $cancelledApprovedChannelIds);
    }

    /**
     * @param $nonApprovedNetworkIds
     * @return array
     * Ravishankar Singh: 2019-11-23
     * Fetch the details of those networks which were not earlier in RO upon approval. These networks are added upon addition of new channels which don't belong to existing network
     */
    public function fetchNonApprovedNetworkDetails($nonApprovedNetworkIds)
    {
        log_message('DEBUG', 'In process_edited_network_service@fetchNonApprovedNetworkDetails | Inside fetchNonApprovedNetworkDetails');
        $nonApprovedNetworkDetails = array();
        $results = $this->processEditedNetworkFeatureObj->getNonApprovedNetworkDetails($nonApprovedNetworkIds);
        foreach ($results as $result) {
            $nonApprovedNetworkDetails['nw_' . $result['customer_id']] = $result;
        }
        log_message('DEBUG', 'In process_edited_network_service@fetchNonApprovedNetworkDetails | Inside fetchNonApprovedNetworkDetails');
        return $nonApprovedNetworkDetails;
    }

    /**
     * @param $customerId
     * @param $internalRoId
     * @return array
     * Ravishankar Singh: 2019-11-23
     * Fetches the RO Report details and market details to cancel the previous generated invoice
     */
    public function initiateInvoiceCancelProcess($customerId, $internalRoId)
    {
        log_message('DEBUG', 'In process_edited_network_service@initiateInvoiceCancelProcess | Inside initiateInvoiceCancelProcess');
        $marketStr = '';
        $networkFinalInfo = array();
        $networkInfo = $this->processEditedNetworkFeatureObj->getAllNetworkInfo($customerId, $internalRoId);

        if ($networkInfo) {
            $networkFinalInfo = $networkInfo[0];
            $roNetworkMarkets = $this->processEditedNetworkFeatureObj->getScheduledMarketForChannel(explode(',', $networkFinalInfo['channel_names']), $internalRoId);

            foreach ($roNetworkMarkets as $eachNetworkMarkets) {
                if ($marketStr == '') {
                    $marketStr = $eachNetworkMarkets['market_name'];
                } else {
                    if ($marketStr != $eachNetworkMarkets['market_name']) {
                        $marketStr .= "," . $eachNetworkMarkets['market_name'];
                    }
                }
            }
            $networkFinalInfo['market'] = $marketStr;
        }
        log_message('DEBUG', 'In process_edited_network_service@initiateInvoiceCancelProcess | Leaving initiateInvoiceCancelProcess');
        return $networkFinalInfo;
    }

    /**
     * @param $cancelledApprovedChannelIds
     * @param $cancelledApprovedChannelDetails
     * @param $internalRoId
     * @param $customerId
     * @param $userId
     * @param $areChannelsCancelledOnly
     * @param $newRevisionNumber
     * @param $networkFinalInfo
     * Ravishankar Singh:2019-11-23
     * cancels the channels which were earlier approved but are now cancelled.
     */
    public function cancelChannelsAfterApprovalNetworkWise($cancelledApprovedChannelIds, $cancelledApprovedChannelDetails, $internalRoId, $customerId, $userId, $areChannelsCancelledOnly, $newRevisionNumber, $networkFinalInfo)
    {
        log_message('DEBUG', 'In process_edited_network_service@cancelChannelsAfterApproval | Inside cancelChannelsAfterApproval');
        foreach ($cancelledApprovedChannelIds as $cancelledApprovedChannelId) {

            // get_campaigns_for internal_ro_number and update sv_advertiser_campaign_screens_dates where date >= +2 days
            $campaignIds = $this->processEditedNetworkFeatureObj->updateSvAdvertiserCampaignScreensDates($internalRoId, $cancelledApprovedChannelId);
            log_message('INFO', 'In process_edited_network_service@cancelChannelsAfterApproval | CampaignIds of updated channels - ' . print_r($campaignIds, true));

            //Insert into ro_cancel_channel
            $insertData = array(
                'channel_id' => $cancelledApprovedChannelId,
                'internal_ro_number' => $internalRoId,
                'marker_for_cancellation' => 0,
                'user_id' => $userId,
                'cancel_requested_time' => DATE_OF_CHANNEL_CANCEL
            );
            log_message('INFO', 'In process_edited_network_service@cancelChannelsAfterApproval | Inserting approved cancelled channels in ro_cancel _channel');
            $this->processEditedNetworkFeatureObj->insertIntoCancelChannel($insertData);

            //get remaining number of impression for channel
            $impressionResults = $this->processEditedNetworkFeatureObj->getChannelImpressions($campaignIds);

            $channelImpressions = array();
            $channelImpressions['total_spot_ad_seconds'] = 0;
            $channelImpressions['total_banner_ad_seconds'] = 0;
            foreach ($impressionResults as $impression) {
                if ($impression['screen_region_id'] == 1) {
                    $channelImpressions['total_spot_ad_seconds'] += $impression['total_ad_seconds'];
                } else if ($impression['screen_region_id'] == 3) {
                    $channelImpressions['total_banner_ad_seconds'] += $impression['total_ad_seconds'];
                }
            }
            log_message('INFO', 'In process_edited_network_service@cancelChannelsAfterApproval | Total remaining seconds after channel cancellation - ' . print_r($channelImpressions, true));

            $whereCondition = array(array('tv_channel_id', $cancelledApprovedChannelId), array('internal_ro_number', $internalRoId), array('customer_id', $customerId));
            $updateData = array(
                'total_spot_ad_seconds' => $channelImpressions['total_spot_ad_seconds'],
                'channel_spot_amount' => ($channelImpressions['total_spot_ad_seconds'] * $cancelledApprovedChannelDetails['ch_' . $cancelledApprovedChannelId]['channel_spot_avg_rate']) / 10,
                'total_banner_ad_seconds' => $channelImpressions['total_banner_ad_seconds'],
                'channel_banner_amount' => ($channelImpressions['total_banner_ad_seconds'] * $cancelledApprovedChannelDetails['ch_' . $cancelledApprovedChannelId]['channel_banner_avg_rate']) / 10
            );
            log_message('INFO', 'In process_edited_network_service@processEditedNetwork | Updating EXISTING CHANNEL(' . $cancelledApprovedChannelId . ') in EXISTING NETWORK');
            $this->processEditedNetworkFeatureObj->updateEditedDetailsInRoApprovedNetworks($updateData, $whereCondition);
        }

        //Whether to Update The field for Ro and customer
        $endDateCrossed = $this->processEditedNetworkFeatureObj->checkEndDateCrossedForROCustomerId($internalRoId, $customerId);
        if (!$endDateCrossed && $areChannelsCancelledOnly) {
            $whereCondition = array(array('internal_ro_number', $internalRoId), array('customer_id', $customerId));
            $updatePdfRevision = array(
                'revision_no' => $newRevisionNumber,
                'pdf_generation_status' => 0,
                'pdf_processing' => 0
            );
            log_message('INFO', 'In process_edited_network_service@cancelChannelsAfterApproval | End date has not crossed. Updating revision status');
            $this->processEditedNetworkFeatureObj->updateEditedDetailsInRoApprovedNetworks($updatePdfRevision, $whereCondition);
            log_message('INFO', 'In process_edited_network_service@cancelChannelsAfterApproval | Updating invoice status');
            $this->updatingInvoiceCancelProcess($networkFinalInfo);
        }
        log_message('INFO', 'In process_edited_network_service@cancelChannelsAfterApproval | Updating report details');
        $this->updateIntoExternalRoReportDetail($internalRoId);
        log_message('DEBUG', 'In process_edited_network_service@cancelChannelsAfterApproval | Leaving cancelChannelsAfterApproval');
    }

    /**
     * @param $networkFinalInfo
     * Ravishankar Singh: 2019-11-23
     * the changes required in invoice report are committed to database
     */
    public function updatingInvoiceCancelProcess($networkFinalInfo)
    {
        log_message('DEBUG', 'In process_edited_network_service@updatingInvoiceCancelProcess | Inside updatingInvoiceCancelProcess');
        if ($networkFinalInfo) {
            $checkForPresenceOfInvoiceData = $this->processEditedNetworkFeatureObj->checkForPresenceOfInvoiceData($networkFinalInfo['network_ro_number']);
            $networkFinalInfo['pdf_processing'] = 0;
            log_message('info', 'In process_edited_network_service@updatingInvoiceCancelProcess | ro_cancel_invoice networkInfo array-' . print_r($networkFinalInfo, true));
            if (count($checkForPresenceOfInvoiceData) > 0) {
                $this->processEditedNetworkFeatureObj->updateInvoiceCancelData($networkFinalInfo);
            } else {
                $this->processEditedNetworkFeatureObj->insertInvoiceCancelData($networkFinalInfo);
            }
        }
        log_message('DEBUG', 'In process_edited_network_service@updatingInvoiceCancelProcess | Leaving updatingInvoiceCancelProcess');
    }

    /**
     * @param $internalRoId
     * Ravishankar Singh: 2019-11-23
     * updates the finance related data in database
     */
    public function updateIntoExternalRoReportDetail($internalRoId)
    {
        log_message('DEBUG', 'In process_edited_network_service@updateIntoExternalRoReportDetail | Inside updateIntoExternalRoReportDetail');
        $roCampaignAmount = $this->processEditedNetworkFeatureObj->getExternalRoReportDetails($internalRoId);

        $grossRoAmount = $roCampaignAmount['gross_ro_amount'];
        $agencyCommissionAmount = $roCampaignAmount['agency_commission_amount'];
        if ($roCampaignAmount['agency_rebate_on'] == "ro_amount") {
            $agencyRebate = $roCampaignAmount['gross_ro_amount'] * ($roCampaignAmount['agency_rebate'] / 100);
        } else {
            $agencyRebate = ($roCampaignAmount['gross_ro_amount'] - $roCampaignAmount['agency_commission_amount']) * ($roCampaignAmount['agency_rebate'] / 100);
        }
        $otherExpenses = $roCampaignAmount['other_expenses'];

        $roDetails = $this->processEditedNetworkFeatureObj->getTotalNetworkPayoutAndTotalNetworkSeconds($internalRoId);

        $totalNetworkPayout = $roDetails['network_payout'];
        $totalScheduledSeconds = $roDetails['total_scheduled_seconds'];

        $actualNetAmount = $grossRoAmount - $agencyCommissionAmount - $agencyRebate - $otherExpenses;
        $netContributionAmount = $actualNetAmount - $totalNetworkPayout;

        $netRevenue = $grossRoAmount - $agencyCommissionAmount;
        $netRevenue = round(($netRevenue * SERVICE_TAX), 2);
        $netContributionAmountPercent = round(($netContributionAmount / $netRevenue) * 100, 2);

        $reportData = array(
            'gross_ro_amount' => $grossRoAmount,
            'agency_commission_amount' => $agencyCommissionAmount,
            'other_expenses' => $otherExpenses,
            'agency_rebate' => $agencyRebate,
            'total_seconds_scheduled' => $totalScheduledSeconds,
            'total_network_payout' => $totalNetworkPayout,
            'net_contribution_amount' => $netContributionAmount,
            'net_contribution_amount_per' => $netContributionAmountPercent,
            'net_revenue' => $netRevenue
        );
        log_message('INFO', 'In process_edited_network_service@updateIntoExternalRoReportDetail | Updating RO_EXTERNAL_RO_REPORT_DETAILS');
        $this->processEditedNetworkFeatureObj->updateExternalRoReportDetails($reportData, $internalRoId);
        log_message('DEBUG', 'In process_edited_network_service@updateIntoExternalRoReportDetail | Leaving updateIntoExternalRoReportDetail');
    }
}