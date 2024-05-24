<?php

namespace application\services\feature_services;

use application\feature_dal\RoApprovalFeature;

include_once APPPATH . 'feature_dal/ro_approval_feature.php';

class RoApprovalService
{
    private $CI;
    private $roApprovalFeatureObj;

    public function __construct()
    {
        log_message('DEBUG', 'In ro_approval_service@constructor | Constructor');
        $this->CI = &get_instance();
        $this->CI->load->library('session');
        $this->roApprovalFeatureObj = new RoApprovalFeature();
    }

    public function preRoApproval($roId)
    {
        log_message('DEBUG', 'In ro_approval_service@preRoApproval | ----------------Inside preApprovalRo function-------------------'.print_r($_POST,true));

        $submittedExtRoDetails = $this->submittedExtRoDetails($roId);
        log_message('INFO', 'In ro_approval_service@preRoApproval | STEP_01 - Submitted RO details - ' . print_r($submittedExtRoDetails, true));


        $internalRoId = $submittedExtRoDetails[0]['internal_ro'];
        $loggedInUser = $this->CI->session->userdata['logged_in_user'];

        log_message('INFO', 'In ro_approval_service@preRoApproval | STEP_02 - Ro Amount details -');
        $roAmountDetails = $this->roApprovalFeatureObj->getRoAmount($internalRoId);

        $this->CI->session->set_userdata('order_id', rtrim(base64_encode($internalRoId), '='));

        $networkDetail = $this->getAllRoNetworkDetails($internalRoId);
        log_message('INFO', 'In ro_approval_service@preRoApproval | STEP_03 - RO Network Details - ' . print_r($networkDetail, true));


        $userId = $loggedInUser[0]['user_id'];
        log_message('INFO', 'In ro_approval_service@preRoApproval | Logged In User Id - ' . print_r($userId, true));

        $nwDataArray = array();
        $priority['priority'] = array();

        log_message('DEBUG', 'In ro_approval_service@preRoApproval | STEP_04 - Foreach Loop for network');
        $totalNwPayout = 0;
        foreach ($networkDetail['networkDetails'] as $nwValue) {
            $nwPayout = 0;
            $nwTmp = array();
            $nwTmp['network_id'] = $nwValue['network_id'];
            $nwTmp['network_name'] = $nwValue['network_name'];
            $nwTmp['market_name'] = $nwValue['market_name'];
            $nwTmp['market_id'] = $nwValue['market_id'];

            $priority['marketPriority'][$nwTmp['market_id']]['market_id'] = $nwValue['market_id'];
            $priority['marketPriority'][$nwTmp['market_id']]['market_name'] = $nwValue['market_name'];

            $customerShare = $nwValue['revenue_sharing'];
            $nwTmp['channels_data_array'] = array();

            log_message('DEBUG', 'In ro_approval_service@preRoApproval | For each channel loop to calculate channel details of a given channel');
            foreach ($nwValue['channels_data_array'] as $key => $chDetails) {
                //storing data for market priorities
                array_push($priority['priority'], $chDetails['priority']);
                $priority['marketPriority'][$nwTmp['market_id']]['channel_priority'][$chDetails['priority']][$chDetails['channel_id']] = $chDetails['channel_name'];

                $tmp = array();
                $tmp['channel_id'] = $chDetails['channel_id'];
                $tmp['channel_name'] = $chDetails['channel_name'];
                $tmp['to_play_spot'] = $chDetails['to_play_spot'];
                $tmp['to_play_banner'] = $chDetails['to_play_banner'];
                $tmp['spot_region_id'] = '1';
                $tmp['total_spot_ad_seconds'] = round($chDetails['total_spot_ad_seconds'], 2);
                $tmp['banner_region_id'] = '3';
                $tmp['total_banner_ad_seconds'] = round($chDetails['total_banner_ad_seconds'], 2);

                $channelHistoricalRate = $this->roApprovalFeatureObj->getChannelHistoricalRateForClient($chDetails['client_name'], $chDetails['channel_name']);
                log_message('INFO', 'In ro_approval_service@roApproved | Fetching channel historical rate for CLIENT -> ' . $chDetails['client_name'] . ' | CHANNEL -> ' . $chDetails['channel_name'] . print_r($channelHistoricalRate, true));

                if ($channelHistoricalRate[0]['channel_spot_avg_rate'] != 0) {
                    $channelSpotAvgRate = round((float)$channelHistoricalRate[0]['channel_spot_avg_rate'], 2);
                } else {
                    $channelSpotAvgRate = round($chDetails['spot_avg'], 2);
                }
                if ($channelHistoricalRate[0]['channel_banner_avg_rate'] != 0) {
                    $channelBannerAvgRate = round((float)$channelHistoricalRate[0]['channel_banner_avg_rate'], 2);
                } else {
                    $channelBannerAvgRate = round($chDetails['banner_avg'], 2);
                }

                //spot and banner rates based on whether client has previously booked advertisement on channel
                $tmp['channel_spot_avg_rate'] = $channelSpotAvgRate;
                $tmp['channel_banner_avg_rate'] = $channelBannerAvgRate;

                //Spot and Banner rates stored in sv_tv_channel
                $tmp['channel_spot_reference_rate'] = round($chDetails['spot_avg'], 2);
                $tmp['channel_banner_reference_rate'] = round($chDetails['banner_avg'], 2);

                $channelSpotAmount = round(($channelSpotAvgRate * $chDetails['total_spot_ad_seconds']) / 10, 2);
                $channelBannerAmount = round(($channelBannerAvgRate * $chDetails['total_banner_ad_seconds']) / 10, 2);
                $tmp['channel_spot_amount'] = $channelSpotAmount;
                $tmp['channel_banner_amount'] = $channelBannerAmount;

                $tmp['customer_share'] = $customerShare;
                $tmp['approved'] = 0;
                $tmp['additional_campaign'] = 0;
                $tmp['cancel_channel'] = 0;

                $nwPayout += ($channelSpotAmount + $channelBannerAmount) * ($customerShare / 100);
                log_message('INFO', 'In ro_approval_service@preRoApproval | Payout for Network - ' . print_r(array('Network' => $nwValue['network_id'], 'Payout' => $nwPayout), true));

                $totalNwPayout += ($channelSpotAmount + $channelBannerAmount) * ($customerShare / 100);
                log_message('INFO', 'In ro_approval_service@preRoApproval | Total Payout till now - ' . print_r(array('TotalPayout' => $nwPayout), true));

                array_push($nwTmp['channels_data_array'], $tmp);
            }   // inner for loop

            $nwTmp['nw_payout'] = $nwPayout;
            $nwTmp['revenue_sharing'] = $customerShare;
            $nwTmp['approved'] = 0;
            log_message('INFO', 'In ro_approval_service@preRoApproval | nw_tmp - ' . print_r($nwTmp, true));
            array_push($nwDataArray, $nwTmp);
        }   //outer for loop

        log_message('DEBUG', 'In ro_approval_service@preRoApproval | nw_data_array - ' . print_r($nwDataArray, true));
        $nwDataArray = $this->sortByMarket($nwDataArray);
        log_message('DEBUG', 'In ro_approval_service@preRoApproval | STEP_05 - nw_data_array sorted by market - ' . print_r($nwDataArray, true));

        $priority['priority'] = array_unique($priority['priority']);
        log_message('INFO', 'In ro_approval_service@preRoApproval | STEP_06 - Channel Wise Priority Details - ' . print_r($priority, true));

        $marketIds = array_keys($priority['marketPriority']);
        $marketIdPriorityData = $this->makeMarketPriorityCombination($marketIds, $priority['priority']);
        log_message('INFO', 'In ro_approval_service@preRoApproval | STEP_07 - marketId_priority Combination data - ' . print_r($marketIdPriorityData, true));

        $roStatus = $this->convertRoStatusIntoUserReadableFormat($roId);
        log_message('DEBUG', 'In ro_approval_service@preRoApproval | STEP_08 - User Readable RO status - ' . print_r($roStatus, true));

        log_message('DEBUG', 'In ro_approval_service@preRoApproval | STEP_09 - RO APPROVAL - ');
        $roApproval = $this->roApprovalFeatureObj->getApprovalRequestData($internalRoId);

        //for showing cancel button
        log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_10 - Is RO cancel request sent - ');
        $cancelRequestSent = $this->roApprovalFeatureObj->isCancelRequestSentByAm(array(array('ext_ro_id', $roId), array('cancel_type', 'cancel_ro')));

        //to verify if an ro is completed
        $today = date('Y-m-d');
        if (isset($submittedExtRoDetails[0]) && strtotime($today) > strtotime($submittedExtRoDetails[0]['camp_end_date'])) {
            $isRoCompleted = 1;
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_11 - RO end date passed on - ' . print_r($submittedExtRoDetails[0]['camp_end_date'], true));
        } else if (isset($submittedExtRoDetails[0]) && strtotime($today) > strtotime($cancelRequestSent[0]['date_of_cancel']) && $cancelRequestSent[0]['date_of_cancel'] != NULL) {
            if ($cancelRequestSent[0]['cancel_ro_by_admin'] == 1) {
                $isRoCompleted = 1;
                log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_11 - RO cancelled on - ' . print_r($cancelRequestSent[0]['date_of_cancel'], true));
            } else {
                $isRoCompleted = 0;
            }
        } else {
            $isRoCompleted = 0;
        }

        //to find whether a market cancellation for an RO is requested
        $cancelMarketRequestSent = $this->roApprovalFeatureObj->isCancelRequestSentByAm(array(array('ext_ro_id', $roId), array('cancel_type', 'cancel_market'), array('cancel_ro_by_admin', 0)));
        if (!$cancelMarketRequestSent) {
            $isCancelMarketRequested = 0;
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_12 - CANCEL Market Request NOT SENT by AM');
        } else {
            $isCancelMarketRequested = 1;
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_12 - CANCEL Market Request SENT by AM');
        }

        if (strtotime($today) > strtotime($submittedExtRoDetails[0]['camp_end_date'])) {
            $editSaveNotToShow = 1;   // 1 => do not show edit save
            $readOnly = 'readonly = "readonly" ';
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_13 - DO NOT show SAVE EDIT button');
        } else {
            $editSaveNotToShow = 0; // 0 => show edit save
            $readOnly = '';
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_13 - May Show SAVE EDIT button');
        }

//        $menu = $this->getMenu($loggedInUser[0]['profile_id']);

        $model = array();
//        $model['menu'] = $menu;
        $model['edit'] = 0;
        $model['id'] = $roId;
        $model['logged_in_user'] = $loggedInUser[0];
        $model['ro_status_entry'] = $roStatus['userReadableRoStatus'];
        $model['is_ro_completed'] = $isRoCompleted;
        $model['is_cancel_market_requested'] = $isCancelMarketRequested;
        $model['submit_ext_ro_data'] = $submittedExtRoDetails;
        $model['ro_amount_detail'] = $roAmountDetails;
        $model['scheduled_data'] = $nwDataArray;
        $model['total_network_payout'] = round(0.0, 2); // total_network_payout is networkPayout stored in ro_approved_network(i.e. value stored after approval) - not required here
        $model['ro_approval'] = $roApproval;
        $model['total_nw_payout'] = round($totalNwPayout, 2);
        $model['priority'] = $priority['priority'];
        $model['market_priority_details'] = $priority['marketPriority'];
        $model['marketId_priority_data'] = $marketIdPriorityData;
        $model['edit_save_not_to_show'] = $editSaveNotToShow;
        $model['readOnly'] = $readOnly;
        $model['verify_ro_approved'] = 0;
        $model['is_ro_approved'] = 0;

        //for showing cancel button
        if (!$cancelRequestSent) {
            $model['cancel_request_status'] = 3;
        } else {
            if ($cancelRequestSent[0]['cancel_ro_by_admin'] == 0) {
                $model['cancel_request_status'] = 0;
            } else if ($cancelRequestSent[0]['cancel_ro_by_admin'] == 1) {
                $model['cancel_request_status'] = 1;
            } else if ($cancelRequestSent[0]['cancel_ro_by_admin'] == 2) {
                $model['cancel_request_status'] = 2;
            }
        }

        //cancel market
        $cancelRequestMarket = $this->roApprovalFeatureObj->isCancelRequestSentByam(array(array('ext_ro_id', $roId), array('cancel_type', 'cancel_market')));
        if (!$cancelRequestMarket) {
            $model['cancel_request_status_mkt'] = 3;
        } else {
            foreach ($cancelRequestMarket as $crm) {
                if ($crm['cancel_ro_by_admin'] == 2) {
                    $model['cancel_request_status_mkt'] = 2;
                }
            }
        }

        //ro_approval request : checking for RO approval status
        $submitRoApproval = $this->roApprovalFeatureObj->isCancelRequestSentByAm(array(array('ext_ro_id', $roId), array('cancel_type', 'submit_ro_approval')));
        if ($submitRoApproval != NULL) {
            if ($submitRoApproval[0]['cancel_ro_by_admin'] == 1) {
                $model['ro_ready_for_schedule_approval'] = 1;
            } else {
                $model['ro_ready_for_schedule_approval'] = 0;
            }

            if ($submitRoApproval[0]['cancel_ro_by_admin'] == 2) {
                $model['rejection_of_submit_ro'] = 1;
            } else {
                $model['rejection_of_submit_ro'] = 0;
            }
        }

        log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_14 - Checking whether to show APPROVAL REQUEST');
        $verifyCampaignCreated = $this->roApprovalFeatureObj->verifyCampaignCreatedForInternalRo($internalRoId);
        if ($verifyCampaignCreated) {
            $model['campaign_created'] = 1;
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_14a - MAY SHOW APPROVAL REQUEST');
        } else {
            $model['campaign_created'] = 0;
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_14a - DO NOT SHOW APPROVAL REQUEST');
        }

        log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_16 - Checking whether any type of cancellation request is SENT');
        $cancellationRequestSent = $this->roApprovalFeatureObj->isCancellationRequestSent($roId);
        if (!$cancellationRequestSent) {
            $model['cancellationRequestSent'] = 0;
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_15a - Cancellation request is NOT SENT');
        } else {
            $model['cancellationRequestSent'] = 1;
            log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_15a - Cancellation request is SENT in any of (cancel_ro,cancel_market,cancel_brand,cancel_content)');
        }

        log_message('INFO', 'In pre_approval_service@preAppprovalRo | STEP_16 FINAL STEP - loading view with following MODEL ARRAY - ' . print_r($model, true));

        if ($submittedExtRoDetails[0]['test_user_creation'] != 2 && $loggedInUser[0]['is_test_user'] == 2) {
            log_message('INFO', 'In ro_approval_service@preRoApproval | 1.Loading View - /ro_manager/approve_scheduler');
            return array('view' => '/ro_manager/approve_scheduler', 'model' => $model);
        } else {
            if ($loggedInUser[0]['profile_id'] == 1 || $loggedInUser[0]['profile_id'] == 2 || $loggedInUser[0]['profile_id'] == 10) {
                log_message('INFO', 'In ro_approval_service@preRoApproval | 3.Loading View - /ro_manager/approve');
                return array('view' => '/ro_manager/approve', 'model' => $model);
            } else if ($loggedInUser[0]['profile_id'] == 3) {
                log_message('INFO', 'In ro_approval_service@preRoApproval | 4.Loading View - /ro_manager/approve_scheduler');
                return array('view' => '/ro_manager/approve_scheduler', 'model' => $model);
            } else if ($loggedInUser[0]['profile_id'] == 6 || $loggedInUser[0]['profile_id'] == 11 || $loggedInUser[0]['profile_id'] == 12) {
                log_message('INFO', 'In ro_approval_service@preRoApproval | 5.Loading View - /ro_manager/approve_account_manager');
                return array('view' => '/ro_manager/approve_account_manager', 'model' => $model);
            } else if ($loggedInUser[0]['profile_id'] == 7) {
                log_message('INFO', 'In ro_approval_service@preRoApproval | 6.Loading View - /ro_manager/approve_finance');
                return array('view' => '/ro_manager/approve_finance', 'model' => $model);
            }
        }
    }

    public function roApproved($roId)
    {
        log_message('DEBUG', 'In ro_approval_service@roApproved | ----------------Inside roApproved function-------------------');

        $submittedExtRoDetails = $this->submittedExtRoDetails($roId);
        log_message('INFO', 'In ro_approval_service@roApproved | STEP_01 - Submitted RO details - ' . print_r($submittedExtRoDetails, true));

        $internalRoId = $submittedExtRoDetails[0]['internal_ro'];
        $loggedInUser = $this->CI->session->userdata['logged_in_user'];

        //For verifying edit is enabled for given ro in sv_job_queue table
        $editing = $this->roApprovalFeatureObj->isEditingInProgress($internalRoId);
        if ($editing && ($loggedInUser[0]['profile_id'] == 1 || $loggedInUser[0]['profile_id'] == 2 || $loggedInUser[0]['profile_id'] == 10)) {
            log_message('INFO', 'In ro_approval_service@roApproved | PDF generation is under process. Loading View - /ro_manager/approve_edit');
            return array('view' => '/ro_manager/approve_edit', 'model' => array());
        }

        log_message('INFO', 'In ro_approval_service@roApproved | STEP_02 - Ro Amount details -');
        $roAmountDetails = $this->roApprovalFeatureObj->getRoAmount($internalRoId);

        $this->CI->session->set_userdata('order_id', rtrim(base64_encode($internalRoId), '='));

        $networkDetail = $this->getAllRoNetworkDetails($internalRoId);
        log_message('INFO', 'In ro_approval_service@roApproved | STEP_03 - RO Network Details - ' . print_r($networkDetail, true));

        $userId = $loggedInUser[0]['user_id'];
        log_message('INFO', 'In ro_approval_service@roApproved | Logged In User Id - ' . print_r($userId, true));

        $nwDataArray = array();
        $nwDataArray['approved'] = array();
        $nwDataArray['non_approved'] = array();
        $priority['priority'] = array();

        $nwShareValueApproved = $this->getNwShareValuesInProperFormat($internalRoId, array_keys($networkDetail['networkDetails']));
        log_message('DEBUG', 'In ro_approval_service@roApproved | STEP_04 - Revenue share for ro customer - ' . print_r($nwShareValueApproved, true));
        $isApproved = $this->getDetailsFromApprovedNetworks($internalRoId, array_keys($networkDetail['networkDetails']), $networkDetail['allChannels']);
        log_message('INFO', 'In ro_approval_service@roApproved | STEP_05 - Channel details in approved network - ' . print_r($isApproved, true));

        log_message('DEBUG', 'In ro_approval_service@roApproved | STEP_06 - For each network loop to find channels corresponding to a network');
        $totalNwPayout = 0;
        foreach ($networkDetail['networkDetails'] as $nwValue) {
            $approved = 0;
            $nwApprovalStatus = 1;
            $nwPayout = 0;
            $nwTmp = array();
            $nwTmp['network_id'] = $nwValue['network_id'];
            $nwTmp['network_name'] = $nwValue['network_name'];
            $nwTmp['market_name'] = $nwValue['market_name'];
            $nwTmp['market_id'] = $nwValue['market_id'];

            $priority['marketPriority'][$nwTmp['market_id']]['market_id'] = $nwValue['market_id'];
            $priority['marketPriority'][$nwTmp['market_id']]['market_name'] = $nwValue['market_name'];

            if (array_key_exists($nwValue['network_id'], $nwShareValueApproved)) {
                $customerShare = $nwShareValueApproved[$nwValue['network_id']];
            } else {
                $customerShare = $nwValue['revenue_sharing'];
            }

            $nwTmp['channels_data_array'] = array();
            $nwTmp['channels_data_array']['approved'] = array();
            $nwTmp['channels_data_array']['non_approved'] = array();

            log_message('DEBUG', 'In ro_approval_service@roApproved | For each channel loop to calculate channel details of a given channel');
            foreach ($nwValue['channels_data_array'] as $key => $chDetails) {
                //storing data for market priorities
                array_push($priority['priority'], $chDetails['priority']);
                $priority['marketPriority'][$nwTmp['market_id']]['channel_priority'][$chDetails['priority']][$chDetails['channel_id']] = $chDetails['channel_name'];

                $tmp = array();
                $channelApproved = 0;
                $tmp['channel_id'] = $channelId = $chDetails['channel_id'];
                $tmp['channel_name'] = $chDetails['channel_name'];
                $tmp['to_play_spot'] = $chDetails['to_play_spot'];
                $tmp['to_play_banner'] = $chDetails['to_play_banner'];
                if (array_key_exists($channelId, $isApproved)) {
                    $channelApproved = 1;
                    $additionalCampaign = 0;

                    $tmp['spot_region_id'] = '1';
                    $tmp['total_spot_ad_seconds'] = round($chDetails['total_spot_ad_seconds'], 2);
                    $tmp['banner_region_id'] = '3';
                    $tmp['total_banner_ad_seconds'] = round($chDetails['total_banner_ad_seconds'], 2);

                    $spotSecondsNotMatched = (round($isApproved[$channelId]['total_spot_ad_seconds'], 2) != round($chDetails['total_spot_ad_seconds'], 2));
                    $bannerSecondsNotMatched = (round($isApproved[$channelId]['total_banner_ad_seconds'], 2) != round($chDetails['total_banner_ad_seconds'], 2));
                    if ($spotSecondsNotMatched or $bannerSecondsNotMatched) {
                        $channelApproved = 0;
                        $additionalCampaign = 1;
                        $nwApprovalStatus = 0;
                    }

                    //Spot and Banner rates stored in ro_approved_network
                    $channelSpotAvgRate = round($isApproved[$channelId]['channel_spot_avg_rate'], 2);
                    $channelBannerAvgRate = round($isApproved[$channelId]['channel_banner_avg_rate'], 2);
                    $tmp['channel_spot_avg_rate'] = $channelSpotAvgRate;
                    $tmp['channel_banner_avg_rate'] = $channelBannerAvgRate;

                    //Spot and Banner rates stored in sv_tv_channel
                    $tmp['channel_spot_reference_rate'] = round($chDetails['spot_avg'], 2);
                    $tmp['channel_banner_reference_rate'] = round($chDetails['banner_avg'], 2);

                    //Spot and Banner amount
                    $channelSpotAmount = round(($channelSpotAvgRate * $chDetails['total_spot_ad_seconds']) / 10, 2);
                    $channelBannerAmount = round(($channelBannerAvgRate * $chDetails['total_banner_ad_seconds']) / 10, 2);
                    $tmp['channel_spot_amount'] = $channelSpotAmount;
                    $tmp['channel_banner_amount'] = $channelBannerAmount;

                    $tmp['customer_share'] = $isApproved[$channelId]['customer_share'];
                    $tmp['approved'] = $channelApproved;
                    $tmp['additional_campaign'] = $additionalCampaign;
                    $tmp['cancel_channel'] = 0;
                    $approved = 1;

                    $nwPayout += ($channelSpotAmount + $channelBannerAmount) * ($isApproved[$channelId]['customer_share'] / 100);
                    $totalNwPayout += ($channelSpotAmount + $channelBannerAmount) * ($isApproved[$channelId]['customer_share'] / 100);

                } else {
                    $tmp['spot_region_id'] = '1';
                    $tmp['total_spot_ad_seconds'] = round($chDetails['total_spot_ad_seconds'], 2);
                    $tmp['banner_region_id'] = '3';
                    $tmp['total_banner_ad_seconds'] = round($chDetails['total_banner_ad_seconds'], 2);

                    $channelHistoricalRate = $this->roApprovalFeatureObj->getChannelHistoricalRateForClient($chDetails['client_name'], $chDetails['channel_name']);
                    log_message('INFO', 'In ro_approval_service@roApproved | Fetching channel historical rate for CLIENT -> ' . $chDetails['client_name'] . ' | CHANNEL -> ' . $chDetails['channel_name'] . print_r($channelHistoricalRate, true));

                    if ($channelHistoricalRate[0]['channel_spot_avg_rate'] != 0) {
                        $channelSpotAvgRate = round((float)$channelHistoricalRate[0]['channel_spot_avg_rate'], 2);
                    } else {
                        $channelSpotAvgRate = round($chDetails['spot_avg'], 2);
                    }
                    if ($channelHistoricalRate[0]['channel_banner_avg_rate'] != 0) {
                        $channelBannerAvgRate = round((float)$channelHistoricalRate[0]['channel_banner_avg_rate'], 2);
                    } else {
                        $channelBannerAvgRate = round($chDetails['banner_avg'], 2);
                    }

                    //spot and banner rates based on whether client has previously booked advertisement on channel
                    $tmp['channel_spot_avg_rate'] = $channelSpotAvgRate;
                    $tmp['channel_banner_avg_rate'] = $channelBannerAvgRate;

                    //Spot and Banner rates stored in sv_tv_channel
                    $tmp['channel_spot_reference_rate'] = round($chDetails['spot_avg'], 2);
                    $tmp['channel_banner_reference_rate'] = round($chDetails['banner_avg'], 2);

                    $channelSpotAmount = round(($channelSpotAvgRate * $chDetails['total_spot_ad_seconds']) / 10, 2);
                    $channelBannerAmount = round(($channelBannerAvgRate * $chDetails['total_banner_ad_seconds']) / 10, 2);
                    $tmp['channel_spot_amount'] = $channelSpotAmount;
                    $tmp['channel_banner_amount'] = $channelBannerAmount;

                    $tmp['customer_share'] = $customerShare;
                    $tmp['approved'] = 0;
                    $tmp['additional_campaign'] = 0;
                    $tmp['cancel_channel'] = 0;

                    $nwPayout += ($channelSpotAmount + $channelBannerAmount) * ($customerShare / 100);
                    log_message('INFO', 'In ro_approval_service@roApproved | Payout for Network - ' . print_r(array('Network' => $nwValue['network_id'], 'Payout' => $nwPayout), true));

                    $totalNwPayout += ($channelSpotAmount + $channelBannerAmount) * ($customerShare / 100);
                    log_message('INFO', 'In ro_approval_service@roApproved | Total Payout till now - ' . print_r(array('TotalPayout' => $nwPayout), true));

                    $nwApprovalStatus = 0;
                }   // inner else
                if ($channelApproved == 1) {
                    array_push($nwTmp['channels_data_array']['approved'], $tmp);
                } else {
                    array_push($nwTmp['channels_data_array']['non_approved'], $tmp);
                }

            }   // inner for loop

            $nwTmp['nw_payout'] = $nwPayout;
            $nwTmp['revenue_sharing'] = $customerShare;
            $nwTmp['approved'] = $nwApprovalStatus;
            log_message('INFO', 'In ro_approval_service@roApproved | nw_tmp - ' . print_r($nwTmp, true));
            if ($approved == 1) {
                array_push($nwDataArray['approved'], $nwTmp);
            } else {
                array_push($nwDataArray['non_approved'], $nwTmp);
            }
        } //outer for loop
        log_message('DEBUG', 'In ro_approval_service@roApproved | nw_data_array - ' . print_r($nwDataArray, true));

        //Make $nwDataArray such that :- Network which is not approved should show first in sorted order similarly for channel
        $nwDataArray = $this->makeNwChannelSortedOrder($nwDataArray);
        log_message('DEBUG', 'In ro_approval_service@roApproved | $nwDataArray modified as per approval status (non approved should show first)- ' . print_r($nwDataArray, true));
        $nwDataArray = $this->sortByMarket($nwDataArray);
        log_message('DEBUG', 'In ro_approval_service@roApproved | STEP_07 - nw_data_array sorted by market - ' . print_r($nwDataArray, true));

        $priority['priority'] = array_unique($priority['priority']);
        log_message('INFO', 'In ro_approval_service@roApproved | STEP_08 - Channel Wise Priority Details - ' . print_r($priority, true));

        $marketIds = array_keys($priority['marketPriority']);
        $marketIdPriorityData = $this->makeMarketPriorityCombination($marketIds, $priority['priority']);
        log_message('INFO', 'In ro_approval_service@roApproved | STEP_09 - marketId_priority Combination data - ' . print_r($marketIdPriorityData, true));

        $roStatus = $this->convertRoStatusIntoUserReadableFormat($roId);
        log_message('DEBUG', 'In ro_approval_service@roApproved | STEP_10 - User Readable RO status - ' . print_r($roStatus, true));

        log_message('DEBUG', 'In ro_approval_service@roApproved | STEP_11 - TOTAL NETWORK PAYOUT - ');
        $totalNetworkPayout = $this->roApprovalFeatureObj->getTotalNetworkPayout($internalRoId);

        log_message('DEBUG', 'In ro_approval_service@roApproved | STEP_12 - RO APPROVAL - ');
        $roApproval = $this->roApprovalFeatureObj->getApprovalRequestData($internalRoId);

        //for showing cancel button
        log_message('INFO', 'In pre_approval_service@roApproved | STEP_14 - Is RO cancel request sent - ');
        $cancelRequestSent = $this->roApprovalFeatureObj->isCancelRequestSentByAm(array(array('ext_ro_id', $roId), array('cancel_type', 'cancel_ro')));

        //to verify if an ro is completed
        $today = date('Y-m-d');
        if (isset($submittedExtRoDetails[0]) && strtotime($today) > strtotime($submittedExtRoDetails[0]['camp_end_date'])) {
            $isRoCompleted = 1;
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_15 - RO end date passed on - ' . print_r($submittedExtRoDetails[0]['camp_end_date'], true));
        } else if (isset($submittedExtRoDetails[0]) && strtotime($today) > strtotime($cancelRequestSent[0]['date_of_cancel']) && $cancelRequestSent[0]['date_of_cancel'] != NULL) {
            if ($cancelRequestSent[0]['cancel_ro_by_admin'] == 1) {
                $isRoCompleted = 1;
                log_message('INFO', 'In pre_approval_service@roApproved | STEP_15 - RO cancelled on - ' . print_r($cancelRequestSent[0]['date_of_cancel'], true));
            } else {
                $isRoCompleted = 0;
            }
        } else {
            $isRoCompleted = 0;
        }

        //to find whether a market cancellation for an RO is requested
        $cancelMarketRequestSent = $this->roApprovalFeatureObj->isCancelRequestSentByAm(array(array('ext_ro_id', $roId), array('cancel_type', 'cancel_market'), array('cancel_ro_by_admin', 0)));
        if (!$cancelMarketRequestSent) {
            $isCancelMarketRequested = 0;
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_16 - CANCEL Market Request NOT SENT by AM');
        } else {
            $isCancelMarketRequested = 1;
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_16 - CANCEL Market Request SENT by AM');
        }

        if (strtotime($today) > strtotime($submittedExtRoDetails[0]['camp_end_date'])) {
            $editSaveNotToShow = 1;   // 1 => do not show edit save
            $readOnly = 'readonly = "readonly" ';
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_17 - DO NOT show SAVE EDIT button');
        } else {
            $editSaveNotToShow = 0; // 0 => show edit save
            $readOnly = '';
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_17 - May Show SAVE EDIT button');
        }

        $model = array();
        $model['edit'] = 1;
        $model['id'] = $roId;
        $model['logged_in_user'] = $loggedInUser[0];
        $model['ro_status_entry'] = $roStatus['userReadableRoStatus'];
        $model['is_ro_completed'] = $isRoCompleted;
        $model['is_cancel_market_requested'] = $isCancelMarketRequested;
        $model['submit_ext_ro_data'] = $submittedExtRoDetails;
        $model['ro_amount_detail'] = $roAmountDetails;
        $model['scheduled_data'] = $nwDataArray;
        $model['total_network_payout'] = round($totalNetworkPayout[0]['network_payout'], 2);
        $model['ro_approval'] = $roApproval;
        $model['total_nw_payout'] = round($totalNwPayout, 2);
        $model['priority'] = $priority['priority'];
        $model['market_priority_details'] = $priority['marketPriority'];
        $model['marketId_priority_data'] = $marketIdPriorityData;
        $model['edit_save_not_to_show'] = $editSaveNotToShow;
        $model['readOnly'] = $readOnly;

        if ($roStatus['roStatus'] == 'approved') {
            $model['verify_ro_approved'] = 1;
        } else {
            $model['verify_ro_approved'] = 0;
        }

        $isRoApproved = $this->roApprovalFeatureObj->isApprovedRo($internalRoId);
        if (!$isRoApproved) {
            $model['is_ro_approved'] = 0;
        } else {
            $model['is_ro_approved'] = 1;
        }

        //for showing cancel button
        if (!$cancelRequestSent) {
            $model['cancel_request_status'] = 3;
        } else {
            if ($cancelRequestSent[0]['cancel_ro_by_admin'] == 0) {
                $model['cancel_request_status'] = 0;
            } else if ($cancelRequestSent[0]['cancel_ro_by_admin'] == 1) {
                $model['cancel_request_status'] = 1;
            } else if ($cancelRequestSent[0]['cancel_ro_by_admin'] == 2) {
                $model['cancel_request_status'] = 2;
            }
        }

        //cancel market
        $cancelRequestMarket = $this->roApprovalFeatureObj->isCancelRequestSentByam(array(array('ext_ro_id', $roId), array('cancel_type', 'cancel_market')));
        if (!$cancelRequestMarket) {
            $model['cancel_request_status_mkt'] = 3;
        } else {
            foreach ($cancelRequestMarket as $crm) {
                if ($crm['cancel_ro_by_admin'] == 2) {
                    $model['cancel_request_status_mkt'] = 2;
                }
            }
        }

        //ro_approval request : checking for RO approval status
        $submitRoApproval = $this->roApprovalFeatureObj->isCancelRequestSentByAm(array(array('ext_ro_id', $roId), array('cancel_type', 'submit_ro_approval')));
        if ($submitRoApproval != NULL) {
            if ($submitRoApproval[0]['cancel_ro_by_admin'] == 1) {
                $model['ro_ready_for_schedule_approval'] = 1;
            } else {
                $model['ro_ready_for_schedule_approval'] = 0;
            }

            if ($submitRoApproval[0]['cancel_ro_by_admin'] == 2) {
                $model['rejection_of_submit_ro'] = 1;
            } else {
                $model['rejection_of_submit_ro'] = 0;
            }
        }

        log_message('INFO', 'In pre_approval_service@roApproved | STEP_18 - Checking whether to show APPROVAL REQUEST');
        $verifyCampaignCreated = $this->roApprovalFeatureObj->verifyCampaignCreatedForInternalRo($internalRoId);
        if ($verifyCampaignCreated) {
            $model['campaign_created'] = 1;
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_18a - MAY SHOW APPROVAL REQUEST');
        } else {
            $model['campaign_created'] = 0;
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_18a - DO NOT SHOW APPROVAL REQUEST');
        }

        log_message('INFO', 'In pre_approval_service@roApproved | STEP_19 - Checking whether any type of cancellation request is SENT');
        $cancellationRequestSent = $this->roApprovalFeatureObj->isCancellationRequestSent($roId);
        if (!$cancellationRequestSent) {
            $model['cancellationRequestSent'] = 0;
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_19a - Cancellation request is NOT SENT');
        } else {
            $model['cancellationRequestSent'] = 1;
            log_message('INFO', 'In pre_approval_service@roApproved | STEP_19a - Cancellation request is SENT in any of (cancel_ro,cancel_market,cancel_brand,cancel_content)');
        }

        log_message('INFO', 'In pre_approval_service@roApproved | STEP_20 FINAL STEP - loading view with following MODEL ARRAY - ' . print_r($model, true));

        if ($submittedExtRoDetails[0]['test_user_creation'] != 2 && $loggedInUser[0]['is_test_user'] == 2) {
            log_message('INFO', 'In ro_approval_service@roApproved | 1.Loading View - /ro_manager/approve_scheduler');
            return array('view' => '/ro_manager/approve_scheduler', 'model' => $model);
        } else {
            if ($loggedInUser[0]['profile_id'] == 1 || $loggedInUser[0]['profile_id'] == 2 || $loggedInUser[0]['profile_id'] == 10) {
                log_message('INFO', 'In ro_approval_service@roApproved | 3.Loading View - /ro_manager/approve');
                return array('view' => '/ro_manager/approve', 'model' => $model);
            } else if ($loggedInUser[0]['profile_id'] == 3) {
                log_message('INFO', 'In ro_approval_service@roApproved | 4.Loading View - /ro_manager/approve_scheduler');
                return array('view' => '/ro_manager/approve_scheduler', 'model' => $model);
            } else if ($loggedInUser[0]['profile_id'] == 6 || $loggedInUser[0]['profile_id'] == 11 || $loggedInUser[0]['profile_id'] == 12) {
                log_message('INFO', 'In ro_approval_service@roApproved | 5.Loading View - /ro_manager/approve_account_manager');
                return array('view' => '/ro_manager/approve_account_manager', 'model' => $model);
            } else if ($loggedInUser[0]['profile_id'] == 7) {
                log_message('INFO', 'In ro_approval_service@roApproved | 6.Loading View - /ro_manager/approve_finance');
                return array('view' => '/ro_manager/approve_finance', 'model' => $model);
            }
        }
    }

    public function submittedExtRoDetails($roId)
    {
        log_message('DEBUG', 'In ro_approval_service@submittedExtRoDetails | Inside submittedExtRoDetails');
        $result = $this->roApprovalFeatureObj->getRoDetailForRoId($roId);
        if ($result) {
            $roList = array();
            $i = 0;
            foreach ($result as $val) {
                array_push($roList, $val);
                $scheduleApproval = $this->roApprovalFeatureObj->isCancelRequestSentByAm(array(array('ext_ro_id', $val['id']), array('cancel_type', 'ro_approval')));
                $submittedBy = $this->roApprovalFeatureObj->getUserName($val['user_id']);
                log_message('INFO', 'In ro_approval_service@submittedExtRoDetails | Ro submitted by - ' . print_r($submittedBy[0]['user_name'], true));
                $approved_by = $this->roApprovalFeatureObj->getUserName($scheduleApproval[0]['user_id']);
                log_message('INFO', 'In ro_approval_service@submittedExtRoDetails | Ro approved by - ' . print_r($approved_by[0]['user_name'], true));
                $brandName = $this->roApprovalFeatureObj->getBrandNames($val['brand']);
                log_message('INFO', 'In ro_approval_service@submittedExtRoDetails | Ro brand names - ' . print_r($brandName, true));

                $roList[$i]['submitted_by'] = $submittedBy[0]['user_name'];;
                $roList[$i]['approved_by'] = $approved_by[0]['user_name'];
                $roList[$i]['brand_name'] = $brandName;
                $i++;
            }
            log_message('DEBUG', 'In ro_approval_service@submittedExtRoDetails | Leaving submittedExtRoDetails');
            return $roList;
        }
        log_message('DEBUG', 'In ro_approval_service@submittedExtRoDetails | Leaving submittedExtRoDetails');
        return NULL;
    }

    public function getAllRoNetworkDetails($internalRoId)
    {
        log_message('DEBUG', 'In ro_approval_service@getAllRoNetworkDetails | Inside getAllRoNetworkDetails');
        $result = $this->roApprovalFeatureObj->getAllRoNetworkDetails($internalRoId);
        if (empty($result)) return array('networkDetails' => array(), 'allChannels' => array());

        //Creating $networkDetailsArray to store Network and its channels.
        $networkDetailsArray = array();
        $allChannelsArray = array();
        $toPlayDate = strtotime("+1 day");
        foreach ($result as $res) {
            if (!array_key_exists($res['customer_id'], $networkDetailsArray)) {
                $networkDetailsArray[$res['customer_id']] = array(
                    'network_id' => (int)$res['customer_id'],
                    'network_name' => $res['customer_name'],
                    'market_name' => $res['sw_market_name'],
                    'market_id' => (int)$res['market_id'],
                    'revenue_sharing' => (float)$res['revenue_sharing'],
                    'channels_data_array' => array()
                );
                if (!array_key_exists($res['channel_id'], $networkDetailsArray[$res['customer_id']]['channels_data_array'])) {
                    array_push($allChannelsArray, (int)$res['channel_id']);
                    $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']] = array(
                        'channel_id' => (int)$res['channel_id'],
                        'channel_name' => $res['channel_name'],
                        'revenue_sharing' => (float)$res['revenue_sharing'],
                        'client_name' => $res['client_name'],
                        'priority' => $res['priority'],
                        'spot_avg' => (float)$res['spot_avg'],
                        'banner_avg' => (float)$res['banner_avg'],
                        'total_spot_ad_seconds' => 0.0,
                        'to_play_spot' => 0.0,
                        'total_banner_ad_seconds' => 0.0,
                        'to_play_banner' => 0.0
                    );
                    if ($res['screen_region_id'] == 1) {
                        $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['total_spot_ad_seconds'] = (float)$res['total_ad_seconds'];
                        if (strtotime($res['scheduled_date']) <= $toPlayDate) {
                            $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['to_play_spot'] = (float)$res['total_ad_seconds'];
                        }
                    } else if ($res['screen_region_id'] == 3) {
                        $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['total_banner_ad_seconds'] = (float)$res['total_ad_seconds'];
                        if (strtotime($res['scheduled_date']) <= $toPlayDate) {
                            $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['to_play_banner'] = (float)$res['total_ad_seconds'];
                        }
                    }
                } else {
                    if ($res['screen_region_id'] == 1) {
                        $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['total_spot_ad_seconds'] += (float)$res['total_ad_seconds'];
                        if (strtotime($res['scheduled_date']) <= $toPlayDate) {
                            $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['to_play_spot'] += (float)$res['total_ad_seconds'];
                        }
                    } else if ($res['screen_region_id'] == 3) {
                        $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['total_banner_ad_seconds'] += (float)$res['total_ad_seconds'];
                        if (strtotime($res['scheduled_date']) <= $toPlayDate) {
                            $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['to_play_banner'] += (float)$res['total_ad_seconds'];
                        }
                    }
                }
            } else {
                if (!array_key_exists($res['channel_id'], $networkDetailsArray[$res['customer_id']]['channels_data_array'])) {
                    array_push($allChannelsArray, (int)$res['channel_id']);
                    $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']] = array(
                        'channel_id' => (int)$res['channel_id'],
                        'channel_name' => $res['channel_name'],
                        'revenue_sharing' => (float)$res['revenue_sharing'],
                        'client_name' => $res['client_name'],
                        'priority' => $res['priority'],
                        'spot_avg' => (float)$res['spot_avg'],
                        'banner_avg' => (float)$res['banner_avg'],
                        'total_spot_ad_seconds' => 0.0,
                        'to_play_spot' => 0.0,
                        'total_banner_ad_seconds' => 0.0,
                        'to_play_banner' => 0.0
                    );
                    if ($res['screen_region_id'] == 1) {
                        $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['total_spot_ad_seconds'] = (float)$res['total_ad_seconds'];
                        if (strtotime($res['scheduled_date']) <= $toPlayDate) {
                            $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['to_play_spot'] = (float)$res['total_ad_seconds'];
                        }
                    } else if ($res['screen_region_id'] == 3) {
                        $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['total_banner_ad_seconds'] = (float)$res['total_ad_seconds'];
                        if (strtotime($res['scheduled_date']) <= $toPlayDate) {
                            $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['to_play_banner'] = (float)$res['total_ad_seconds'];
                        }
                    }
                } else {
                    if ($res['screen_region_id'] == 1) {
                        $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['total_spot_ad_seconds'] += (float)$res['total_ad_seconds'];
                        if (strtotime($res['scheduled_date']) <= $toPlayDate) {
                            $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['to_play_spot'] += (float)$res['total_ad_seconds'];
                        }
                    } else if ($res['screen_region_id'] == 3) {
                        $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['total_banner_ad_seconds'] += (float)$res['total_ad_seconds'];
                        if (strtotime($res['scheduled_date']) <= $toPlayDate) {
                            $networkDetailsArray[$res['customer_id']]['channels_data_array'][$res['channel_id']]['to_play_banner'] += (float)$res['total_ad_seconds'];
                        }
                    }
                }
            }
        }
        log_message('DEBUG', 'In ro_approval_service@getAllRoNetworkDetails | Leaving getAllRoNetworkDetails');
        return array('networkDetails' => $networkDetailsArray, 'allChannels' => $allChannelsArray);
    }

    public function sortByMarket($data)
    {
        log_message('DEBUG', 'In ro_approval_service@makePriority | Inside makePriority');
        $sortArray = array();
        foreach ($data as $val) {
            foreach ($val as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }
        $orderBy = "market_name"; //change this to whatever key you want from the array
        array_multisort($sortArray[$orderBy], SORT_ASC, $data);

        log_message('DEBUG', 'In ro_approval_service@makePriority | Leaving makePriority');
        return $data;
    }

    public function makeMarketPriorityCombination($marketIds, $priority)
    {
        log_message('DEBUG', 'In ro_approval_service@makePriority | Inside makePriority');
        $data = array();
        foreach ($marketIds as $id) {
            foreach ($priority as $pry) {
                $key = $id . "_" . $pry;
                $data[$key] = '1';
            }
        }
        log_message('DEBUG', 'In ro_approval_service@makePriority | Leaving makePriority');
        return $data;
    }

    public function convertRoStatusIntoUserReadableFormat($roId)
    {
        log_message('DEBUG', 'In ro_approval_service@makePriority | Inside makePriority');
        $roStatus = $this->roApprovalFeatureObj->dataForRoIdInRoStatus($roId);
        switch ($roStatus[0]['ro_status']) {
            case 'RO_Created':
                $userReadableValue = 'RO Created';
                break;
            case 'RO_Rejected':
                $userReadableValue = 'RO Rejected';
                break;
            case 'scheduling_in_progress':
                $userReadableValue = 'Scheduling In Progress';
                break;
            case 'approval_requested':
                $userReadableValue = 'Approval Requested';
                break;
            case 'approved':
                $userReadableValue = 'Approved';
                break;
            case 'cancel_requested':
                $userReadableValue = 'Cancel Requested';
                break;
            case 'cancel_approved':
                $userReadableValue = 'Cancel Approved';
                break;
            case 'cancelled':
                $userReadableValue = 'Cancelled';
                break;
            default:
                $userReadableValue = 'Scheduling In Progress';
                break;
        }
        log_message('DEBUG', 'In ro_approval_service@makePriority | Leaving makePriority');
        return array('roStatus' => $roStatus[0]['ro_status'], 'userReadableRoStatus' => $userReadableValue);
    }

    public function getNwShareValuesInProperFormat($internalRoId, $customerIds)
    {
        log_message('DEBUG', 'In ro_approval_service@getNwShareValuesInProperFormat | Inside getNwShareValuesInProperFormat');
        $result = $this->roApprovalFeatureObj->getRevenueShareForRoCustomer($internalRoId, $customerIds);
        if (!$result) {
            log_message('DEBUG', 'In ro_approval_service@getNwShareValuesInProperFormat | Leaving getNwShareValuesInProperFormat');
            return array();
        }
        $nwShareValues = array();
        foreach ($result as $res) {
            $nwShareValues[$res['customer_id']] = $res['customer_share'];
        }
        log_message('DEBUG', 'In ro_approval_service@getNwShareValuesInProperFormat | Leaving getNwShareValuesInProperFormat');
        return $nwShareValues;
    }

    public function getDetailsFromApprovedNetworks($internalRoId, $customerIds, $channelIds)
    {
        $result = $this->roApprovalFeatureObj->isApproved($internalRoId, $customerIds, $channelIds);
        if (!$result) {
            log_message('DEBUG', 'In ro_approval_service@getDetailsFromApprovedNetworks | Leaving getDetailsFromApprovedNetworks');
            return array();
        }
        $isApproved = array();
        foreach ($result as $res) {
            $isApproved[$res['tv_channel_id']]['channel_id'] = (int)$res['tv_channel_id'];
            $isApproved[$res['tv_channel_id']]['customer_id'] = (int)$res['customer_id'];
            $isApproved[$res['tv_channel_id']]['customer_share'] = (float)$res['customer_share'];
            $isApproved[$res['tv_channel_id']]['total_spot_ad_seconds'] = (float)$res['total_spot_ad_seconds'];
            $isApproved[$res['tv_channel_id']]['total_banner_ad_seconds'] = (float)$res['total_banner_ad_seconds'];
            $isApproved[$res['tv_channel_id']]['channel_spot_avg_rate'] = (float)$res['channel_spot_avg_rate'];
            $isApproved[$res['tv_channel_id']]['channel_banner_avg_rate'] = (float)$res['channel_banner_avg_rate'];
        }
        log_message('DEBUG', 'In ro_approval_service@getDetailsFromApprovedNetworks | Leaving getDetailsFromApprovedNetworks');
        return $isApproved;
    }

    public function makeNwChannelSortedOrder($nwDataArray)
    {
        $data = array();
        foreach ($nwDataArray['non_approved'] as $nw_array) {
            if (count($nw_array) > 0) {
                $new_nw_data = array();
                foreach ($nw_array as $nw_key => $nw_value) {
                    if ($nw_key != 'channels_data_array') {
                        $new_nw_data[$nw_key] = $nw_value;
                    } else {
                        $new_nw_data['channels_data_array'] = array();
                        foreach ($nw_value['non_approved'] as $chnl_array) {
                            if (count($chnl_array) > 0) {
                                $tmp = array();
                                foreach ($chnl_array as $chnl_key => $chnl_value) {
                                    $tmp[$chnl_key] = $chnl_value;
                                }
                                array_push($new_nw_data['channels_data_array'], $tmp);
                            }
                        }

                        foreach ($nw_value['approved'] as $chnl_array) {
                            if (count($chnl_array) > 0) {
                                $tmp = array();
                                foreach ($chnl_array as $chnl_key => $chnl_value) {
                                    $tmp[$chnl_key] = $chnl_value;
                                }
                                array_push($new_nw_data['channels_data_array'], $tmp);
                            }
                        }
                    }
                }
                array_push($data, $new_nw_data);
            }

        }

        foreach ($nwDataArray['approved'] as $value) {
            if (count($value) > 0) {
                $new_nw_data = array();
                foreach ($value as $nw_key => $nw_value) {
                    if ($nw_key != 'channels_data_array') {
                        $new_nw_data[$nw_key] = $nw_value;
                    } else {
                        $new_nw_data['channels_data_array'] = array();
                        foreach ($nw_value['non_approved'] as $chnl_array) {
                            if (count($chnl_array) > 0) {
                                $tmp = array();
                                foreach ($chnl_array as $chnl_key => $chnl_value) {
                                    $tmp[$chnl_key] = $chnl_value;
                                }
                                array_push($new_nw_data['channels_data_array'], $tmp);
                            }
                        }

                        foreach ($nw_value['approved'] as $chnl_array) {
                            if (count($chnl_array) > 0) {
                                $tmp = array();
                                foreach ($chnl_array as $chnl_key => $chnl_value) {
                                    $tmp[$chnl_key] = $chnl_value;
                                }
                                array_push($new_nw_data['channels_data_array'], $tmp);
                            }
                        }
                    }
                }
                array_push($data, $new_nw_data);
            }
        }
        return $data;
    }

    public function isRoAlreadyApproved($roId)
    {
        log_message('INFO', 'In ro_approval_service@isRoAlreadyApproved | Inside');
        return $this->roApprovalFeatureObj->isRoAlreadyApproved($roId);
    }
}