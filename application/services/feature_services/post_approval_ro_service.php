<?php
/**
 * Author: Yash
 * Date: 10/11/2019
 */

namespace application\services\feature_services;

use application\feature_dal\postApprovalRoFeature;
use application\feature_dal\CreateExtRoFeature;
use application\services\common_services\EmailService;
use application\services\common_services\UpdateApprovalStatusAndCampaignStatusService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;

include_once APPPATH . 'feature_dal/post_approval_ro_feature.php';
include_once APPPATH . 'feature_dal/create_ext_ro_feature.php';
include_once APPPATH . 'services/common_services/email_service.php';
include_once APPPATH . 'services/common_services/update_approval_status_and_campaign_status_service.php';

class postApprovalRoService
{
    private $CI;
    private $postApprovalFeatureObj;
    private $createExtRoFeatureObj;

    /**
     * Author: Yash
     * Date: 10/11/2019
     *
     * postApprovalRoService constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('session');
        $this->postApprovalFeatureObj = new postApprovalRoFeature();
        $this->createExtRoFeatureObj = new CreateExtRoFeature();
    }

    /**
     * Author: Yash
     * Date: 10 November, 2019
     *
     * @return array
     * Description: Calls upon final confirmation of Approve Button, insert data into ro_approved_networks
     */
    public function postAddPriceApprove()
    {
        log_message('info', 'In postAddPriceApprove | Entering with Data from UI '.print_r($this->CI->input->post(),True));

        $internalRoNo = $this->CI->input->post('internal_ro');
        $networkDetails = $this->CI->input->post('networks');
        log_message('info','In postAddPriceApprove | NetworkDetails are => '.print_r($networkDetails,True));

        $networkIds = array();
        $scheduleChannelIds = array();
        foreach ($networkDetails as $network){
            array_push($networkIds, (int)$network['network_id']);
            foreach ($network['channels_data_array'] as $channelDetails){
                if($channelDetails['cancel_channel'] == 0){
                    array_push($scheduleChannelIds, (int)$channelDetails['channel_id']);
                }
            }
        }
        log_message('info','In postAddPriceApprove | Network Ids are => '.print_r($networkIds,True));
        $scheduleChannelIds = array_unique($scheduleChannelIds);
        log_message('info','In postAddPriceApprove | Schedule Channel Ids are => '.print_r($scheduleChannelIds,True));

        $customerLocationAndBilling = array();
        $customerDetails =  $this->postApprovalFeatureObj->getCustomerDetails($networkIds);
        log_message('info','Customer Details are => '.print_r($customerDetails,True));
        foreach ($customerDetails as $c){
            $customerLocationAndBilling[$c['customer_id']] = array('customer_location' => $c['customer_location'], 'billing_name' => $c['billing_name']);
        }
        log_message('info','In postAddPriceApprove | CustomerLocationAndBilling Details are => '.print_r($customerLocationAndBilling,True));

        $channelDeploymentStatusByChannelId = array();
        $scheduleChannelDetails = $this->postApprovalFeatureObj->getChannelDetails($scheduleChannelIds);
        foreach ($scheduleChannelDetails as $chDetail){
            $channelDeploymentStatusByChannelId[$chDetail['tv_channel_id']] = $chDetail['deployment_status'];
        }
        log_message('info','In postAddPriceApprove | ChannelDeploymentStatus Details are => '.print_r($channelDeploymentStatusByChannelId,True));

        $roApprovedNetworksData = array();
        $cancelChannelIds = array();

        $marketNames = array();
        $networkNames = array();
        $channelNames = array();
        $totalScheduleSeconds = 0;
        $totalNetPayout = 0;
        $totalPayoutNetworkWise = array();

        foreach ($networkDetails as $n){
            array_push($marketNames, $n['market_name']);
            array_push($networkNames, $n['network_name']);

            $total = 0;
            foreach ($n['channels_data_array'] as $channelDetails){
                if($channelDetails['cancel_channel'] == 1){
                    array_push($cancelChannelIds, (int)$channelDetails['channel_id']);
                }
                else{
                    array_push($channelNames, $channelDetails['channel_name']);
                    $a = array(
                        'internal_ro_number' => $internalRoNo,
                        'client_name' => $this->CI->input->post('client_name'),
                        'customer_id' => (int)$n['network_id'],
                        'customer_name' => $n['network_name'],
                        'customer_share' => (float)$n['customer_share'],
                        'customer_location' => $customerLocationAndBilling[(int)$n['network_id']]['customer_location'],
                        'tv_channel_id' => (int)$channelDetails['channel_id'],
                        'channel_name' => $channelDetails['channel_name'],
                        'channel_spot_avg_rate' => (float)$channelDetails['channel_spot_avg_rate'],
                        'total_spot_ad_seconds' => (float)$channelDetails['total_spot_ad_seconds'],
                        'channel_spot_amount' => ((float)$channelDetails['channel_spot_avg_rate'] * (float)$channelDetails['total_spot_ad_seconds']) / 10,
                        'channel_banner_avg_rate' => (float)$channelDetails['channel_banner_avg_rate'],
                        'total_banner_ad_seconds' => (float)$channelDetails['total_banner_ad_seconds'],
                        'channel_banner_amount' => ((float)$channelDetails['channel_banner_avg_rate'] * (float)$channelDetails['total_banner_ad_seconds']) / 10,
                        'channel_approval_status' => 1,
                        'billing_name' => $customerLocationAndBilling[(int)$n['network_id']]['billing_name'],
                        'channel_deployment_status' => $channelDeploymentStatusByChannelId[(int)$channelDetails['channel_id']]
                    );
                    array_push($roApprovedNetworksData, $a);
                    $total += ($a['channel_spot_amount'] * ((float)$n['customer_share']/100) + $a['channel_banner_amount'] * ((float)$n['customer_share']/100));
                    $totalScheduleSeconds += ($a['total_spot_ad_seconds'] + $a['total_banner_ad_seconds']);
                }
            }
            $totalPayoutNetworkWise[(int)$n['network_id']] = array('network_name' => $n['network_name'], 'nw_payout' => $total, 'customer_share' => (float)$n['customer_share']);
            $totalNetPayout += $total;
        }
        log_message('info','In postAddPriceApprove | Cancel Channel Ids are => '.print_r($cancelChannelIds,True));
        log_message('info','In postAddPriceApprove | TotalScheduleSeconds are => '.print_r($totalScheduleSeconds,True));
        log_message('info','In postAddPriceApprove | TotalPayoutNetworkWise are => '.print_r($totalPayoutNetworkWise,True));
        log_message('info','In postAddPriceApprove | TotalNetPayout are => '.print_r($totalNetPayout,True));

        $marketNames = array_unique($marketNames);
        $marketNames = implode(',', $marketNames);
        log_message('info','In postAddPriceApprove | Market Names are => '.print_r($marketNames,True));

        $networkNames = array_unique($networkNames);
        $networkNames = implode(',', $networkNames);
        log_message('info','In postAddPriceApprove | Network Names are => '.print_r($networkNames,True));

        $channelNames = array_unique($channelNames);
        $channelNames = implode(',', $channelNames);
        log_message('info','In postAddPriceApprove | Channel names are => '.print_r($channelNames,True));

        $roDetails = $this->postApprovalFeatureObj->getExternalRoReportDetails($internalRoNo);
        log_message('info','In postAddPriceApprove | RoDetails form SAC and Ro Amount are => '.print_r($roDetails,True));
        $roExternalRoReportDetailsData = array();
        $roExternalRoReportDetailsData['customer_ro_number'] = $roDetails['customer_ro_number'];
        $roExternalRoReportDetailsData['internal_ro_number'] = $roDetails['internal_ro_number'];
        $roExternalRoReportDetailsData['client_name'] = $roDetails['client_name'];
        $roExternalRoReportDetailsData['agency_name'] = $roDetails['agency_name'];
        $roExternalRoReportDetailsData['start_date'] = $roDetails['start_date'];
        $roExternalRoReportDetailsData['end_date'] = $roDetails['end_date'];
        $roExternalRoReportDetailsData['gross_ro_amount'] = $roDetails['gross_ro_amount'];
        $roExternalRoReportDetailsData['agency_commission_amount'] = $roDetails['agency_commission_amount'];
        $roExternalRoReportDetailsData['other_expenses'] = $roDetails['other_expenses'];
        $roExternalRoReportDetailsData['total_seconds_scheduled'] = $totalScheduleSeconds;
        $roExternalRoReportDetailsData['total_network_payout'] = $totalNetPayout;

        if ($roDetails['agency_rebate_on'] == "ro_amount") {
            $agencyRebate = $roDetails['gross_ro_amount'] * ($roDetails['agency_rebate'] / 100);
        } else {
            $agencyRebate = ($roDetails['gross_ro_amount'] - $roDetails['agency_commission_amount']) * ($roDetails['agency_rebate'] / 100);
        }

        $actualNetAmount = $roDetails['gross_ro_amount'] - $roDetails['agency_commission_amount'] - $agencyRebate - $roDetails['other_expenses'];
        $netContributionAmount = $actualNetAmount - $totalNetPayout;

        $netRevenue = $roDetails['gross_ro_amount'] - $roDetails['agency_commission_amount'];
        $netRevenue = round(($netRevenue * SERVICE_TAX), 2);

        $netContributionAmountPer = round(($netContributionAmount / $netRevenue) * 100, 2);

        $roExternalRoReportDetailsData['agency_rebate'] = $agencyRebate;
        $roExternalRoReportDetailsData['net_revenue'] = $netRevenue;
        $roExternalRoReportDetailsData['net_contribution_amount'] = $netContributionAmount;
        $roExternalRoReportDetailsData['net_contribution_amount_per'] = $netContributionAmountPer;

        try {
            //cancel the channel update in SAC and SACSD
            if (count($cancelChannelIds) > 0) {
                DB::beginTransaction();
                foreach ($cancelChannelIds as $channel_id) {
                    $this->cancelChannelBeforeApproval($channel_id, $internalRoNo);
                }
                DB::commit();
            }

            DB::beginTransaction();

            log_message('info','In postAddPriceApprove | RoApprovedNetworks Data are => '.print_r($roApprovedNetworksData,True));
            $this->postApprovalFeatureObj->insertIntoRoApprovedNetworks($roApprovedNetworksData);
            log_message('info','In postAddPriceApprove | RoExternalRoReportDetails are => '.print_r($roExternalRoReportDetailsData,True));
            $this->postApprovalFeatureObj->insertIntoRoExternalRoReportDetails($roExternalRoReportDetailsData);

            foreach ($totalPayoutNetworkWise as $NwId => $value) {
                $this->postApprovalFeatureObj->updateCustomerSharesInSvCustomer($value['customer_share'], $NwId);
            }

            $roData = $this->postApprovalFeatureObj->getRoId($roDetails['customer_ro_number']);
            log_message('info', 'In postAddPriceApprove | RoId ' . print_r($roData, True));

            $roId = $roData['id'];
            $amUserId = $roData['user_id'];
            $loggedIn = $this->CI->session->userdata("logged_in_user");
            $approval_date = date("Y-m-d H:i:s");
            $this->postApprovalFeatureObj->updateRoStatusInRoAmount($internalRoNo, $loggedIn[0]['user_id'], $approval_date);

            $this->statusUpdation($roId);

            //Update Campaign And Approval Status in SAC
            $updateInSacObj = new UpdateApprovalStatusAndCampaignStatusService();
            $updateInSacObj->updateApprovalStatusAndCampaignStatus($internalRoNo);

            //=========================================== MAILING PROCESS START FROM HERE ===================================================//

            if ($this->CI->input->post('make_good_type') == 0) {
                $MakeGood = 'Auto Make Good';
            } else if ($this->CI->input->post('make_good_type') == 1) {
                $MakeGood = 'Client approved make good';
            } else {
                $MakeGood = 'No make good';
            }
            log_message('Debug', 'In postAddPriceApprove | MakeGood => ' . print_r($MakeGood, True));

            // Approval Mail To Scheduler and Operation
            $this->approvalMailToSchedulerAndOperation($amUserId,$roDetails['customer_ro_number'], $internalRoNo, $MakeGood, $networkNames, $channelNames, $marketNames, $roDetails['start_date'], $roDetails['end_date']);

            //Approval mail to BH,COO
            $this->approvalMailToBHAndCoo($roDetails['customer_ro_number'], $internalRoNo, $MakeGood, $networkNames, $channelNames,$totalPayoutNetworkWise, $roExternalRoReportDetailsData);

            //Mail to Raj
            if ($roExternalRoReportDetailsData['net_contribution_amount_per'] < 25) {
                log_message('debug','In postAddPriceApprove | Mail To Raj');
                $justificationForApproval = $this->CI->input->post('justification_for_approval');
                $correctiveActionPlan = $this->CI->input->post('corrective_action_plan');
                $roRemarkData = array('ro_id' => $roId, 'justification_for_approval' => $justificationForApproval, 'corrective_action_plan_for_future' => $correctiveActionPlan);
                $this->postApprovalFeatureObj->insertRemarks($roRemarkData); // Insert into Ro Approval Remarks

                $roDetail = $this->createExtRoFeatureObj->getRoDetailsForRoId($roId); // Ro Am External RO

                $submittedBy = $this->createExtRoFeatureObj->getUserNameForUserId($roDetail[0]['user_id']); // From Ro USer
                $approvedBy = $loggedIn[0]['user_name'];

                $toUser = "raj@surewaves.com";
                $ccUser = "";
                $emailObject = new EmailService($toUser, $ccUser);
                $mailSent = $emailObject->sendMail(
                    "ceo_notify_net",
                    array('EXTERNAL_RO' => $roDetail[0]['cust_ro']),
                    array(
                        'EXTERNAL_RO' => $roDetail[0]['cust_ro'],
                        'AM_NAME' => $submittedBy[0]['user_name'],
                        'APPROVED_BY' => $approvedBy,
                        'GROSS' => $roDetail[0]['gross'],
                        'MARKET' => $roDetail[0]['market'],
                        'JUSTIFICATION_FOR_APPROVAL' => $justificationForApproval,
                        'CORRECTIVE_ACTION_PLAN' => $correctiveActionPlan,
                        'NET_CONTRIBUTION' => $roExternalRoReportDetailsData['net_contribution_amount_per']
                    ),
                    ''
                );
                if (!$mailSent) {
                    log_message('INFO', 'Mail was not sent to CEO, Operation ');
                }
            }

            DB::commit();
            log_message('DEBUG', 'In postAddPriceApprove | Transactions Committed!');
            return array('Status' => 'success', 'Message' => 'Approved Successfully!');
        } catch (QueryException $e) {
            log_message('ERROR', 'In postAddPriceApprove | Rolling Back : Database query exception occurred - ' . print_r($e->getMessage(), true));
            DB::rollBack();
            log_message('DEBUG', 'In postAddPriceApprove | Database Rolled Back successfully!');
            return array('Status' => 'fail', 'Message' => 'Database exception occurred!');
        }
    }

    /**
     * Author: Yash
     * Date: 10 November, 2019
     *
     * @param $chnlId
     * @param $internalRoNo
     */
    public function cancelChannelBeforeApproval($chnlId, $internalRoNo)
    {
        log_message('info', 'postApprovalRoService@cancelChannelBeforeApproval | Entering Channel Id ' . print_r($chnlId, True));
        $this->postApprovalFeatureObj->updateChannelStatusAsCancel(array('channel_id' => $chnlId, 'internal_ro_number' => $internalRoNo));
        $this->postApprovalFeatureObj->removeChannelFileLocation(array('channel_id' => $chnlId, 'internal_ro_number' => $internalRoNo));
        log_message('info', 'postApprovalRoService@cancelChannelBeforeApproval | Exiting');
    }

    /**
     * Author: Yash
     * Date: 11 November, 2019
     *
     * @param $amUserId
     * @param $customerRoNo
     * @param $internalRoNo
     * @param $MakeGood
     * @param $networkNames
     * @param $channelNames
     * @param $marketNames
     * @param $startDate
     * @param $endDate
     */
    public function approvalMailToSchedulerAndOperation($amUserId, $customerRoNo, $internalRoNo, $MakeGood, $networkNames, $channelNames, $marketNames, $startDate, $endDate)
    {
        log_message('info', 'postApprovalRoService@approvalMailToSchedulerAndOperation | Entering with Data ' . print_r(array($amUserId, $customerRoNo, $internalRoNo, $MakeGood, $networkNames, $channelNames, $marketNames, $startDate, $endDate), True));
        $loggedIn = $this->CI->session->userdata("logged_in_user");
        log_message('info', 'In postAddPriceApprove | LoggedIn ' . print_r($loggedIn, True));
        $isTestUser = $loggedIn[0]['is_test_user'];

        $users = $this->postApprovalFeatureObj->getUserEmails(array(3, 8), $isTestUser);
        log_message('info', 'postApprovalRoService@approvalMailToSchedulerAndOperation | Users for profile 3,8 ' . print_r($users, True));
        $amEmailId = $this->createExtRoFeatureObj->getUserNameForUserId($amUserId);
        log_message('info', 'In postApprovalRoService@approvalMailToSchedulerAndOperation | Users with profile Id : 3,8 => ' . print_r($users, True));

        $userEmails = array();
        foreach ($users as $usr) {
            array_push($userEmails, $usr['user_email']);
        }

        $userEmailIds = implode(",", $userEmails) . ',' . $amEmailId[0]['user_email'];
        log_message('info', 'In postApprovalRoService@approvalMailToSchedulerAndOperation | Users EmailID of profile Id : 3,8 => ' . print_r($userEmailIds, True));

        log_message('info', 'In postApprovalRoService@approvalMailToSchedulerAndOperation | StartDate => ' . print_r($startDate, True));
        log_message('info', 'In postApprovalRoService@approvalMailToSchedulerAndOperation | EndDate => ' . print_r($endDate, True));

        $text = "approval_alert";
        $subject = array('EXTERNAL_RO' => $customerRoNo);
        $file = '';
        $message = array(
            'EXTERNAL_RO' => $customerRoNo,
            'INTERNAL_RO' => $internalRoNo,
            'NETWORK_NAME' => $networkNames,
            'START_DATE' => date('Y-m-d', strtotime($startDate)),
            'END_DATE' => date('Y-m-d', strtotime($endDate)),
            'CURRENT_USER' => $loggedIn[0]['user_name'],
            'CHANNELS' => $channelNames,
            'MARKETS' => $marketNames,
            'MAKEGOOD' => $MakeGood
        );
        log_message('info','In postApprovalRoService@approvalMailToSchedulerAndOperation | Mail message for Scheduler, Operation is '.print_r($message,True));

        //Added sw support email
        //$userEmailIds = $userEmailIds . ',' . $this->CI->config->item('from_email') ;
        //log_message("INFO",'In postApprovalRoService@approvalMailToSchedulerAndOperation | All CC Email IDs => '.print_r($userEmailIds, True));

        $emailObject = new EmailService($loggedIn[0]['user_email'], $userEmailIds);
        $mailSent = $emailObject->sendMail(
            $text,
            $subject,
            $message,
            $file
        );
        if (!$mailSent) {
            log_message('INFO', 'In postApprovalRoService@approvalMailToSchedulerAndOperation | Mail was not sent to Scheduler, Operation ');
        }
    }

    /**
     * Author: Yash
     * Date: 11 November, 2019
     *
     * @param $customerRoNo
     * @param $internalRoNo
     * @param $MakeGood
     * @param $networkNames
     * @param $channelNames
     * @param $totalPayoutNetworkWise
     * @param $roExternalRoReportDetailsData
     */
    public function approvalMailToBHAndCoo($customerRoNo, $internalRoNo, $MakeGood, $networkNames, $channelNames, $totalPayoutNetworkWise, $roExternalRoReportDetailsData)
    {
        log_message('info', 'postApprovalRoService@approvalMailToBHAndCoo | Entering with Data ' . print_r(array($customerRoNo, $internalRoNo, $MakeGood, $networkNames, $channelNames, $totalPayoutNetworkWise, $roExternalRoReportDetailsData), True));
        $loggedIn = $this->CI->session->userdata("logged_in_user");
        log_message('info', 'In postAddPriceApprove | LoggedIn ' . print_r($loggedIn, True));
        $isTestUser = $loggedIn[0]['is_test_user'];

        $usersAdmin = $this->postApprovalFeatureObj->getUserEmails(array(1, 2, 7), $isTestUser);
        log_message('info', 'In postApprovalRoService@approvalMailToBHAndCoo | Users with profile Id : 1,2,7 => ' . print_r($usersAdmin, True));

        $userAdminEmails = array();
        foreach ($usersAdmin as $usr) {
            array_push($userAdminEmails, $usr['user_email']);
        }
        $userAdminEmails = implode(",", $userAdminEmails);

        $text_admin = "approval_alert_bh";
        $htmlTable = $this->networkAndTotalPayoutHtmlTable($totalPayoutNetworkWise);
        $subject = array('EXTERNAL_RO' => $customerRoNo);
        $file = '';
        $message = array(
            'EXTERNAL_RO' => $customerRoNo,
            'INTERNAL_RO' => $internalRoNo,
            'NETWORK_NAME' => $networkNames,
            'START_DATE' => date('Y-m-d', strtotime($roExternalRoReportDetailsData['start_date'])),
            'END_DATE' => date('Y-m-d', strtotime($roExternalRoReportDetailsData['end_date'])),
            'CURRENT_USER' => $loggedIn[0]['user_name'],
            'CHANNELS' => $channelNames,
            'MAKEGOOD' => $MakeGood,
            'RO_AMOUNT' => $roExternalRoReportDetailsData['gross_ro_amount'],
            'AGENCY_COMMISSION' => $roExternalRoReportDetailsData['agency_commission_amount'],
            'TOTAL_OTHER_EXPENSE' => $roExternalRoReportDetailsData['agency_rebate'] + $roExternalRoReportDetailsData['other_expenses'],
            'TOTAL_NW_PAYOUT' => $roExternalRoReportDetailsData['total_network_payout'],
            'SUREWAVES_REVENUE' => $roExternalRoReportDetailsData['net_revenue'],
            'TABLE_WITH_DATA' => $htmlTable
        );

        log_message('info','In postApprovalRoService@approvalMailToBHAndCoo | Mail message for BH,COO is '.print_r($message,True));

        //Added sw support email
        //$userAdminEmails = $userAdminEmails . ',' . $this->CI->config->item('from_email') ;
        //log_message("INFO",'In postApprovalRoService@approvalMailToSchedulerAndOperation | All CC Email IDs => '.print_r($userAdminEmails, True));

        $emailObject = new EmailService($loggedIn[0]['user_email'], $userAdminEmails);
        $mailSent = $emailObject->sendMail(
            $text_admin,
            $subject,
            $message,
            $file
        );
        if (!$mailSent) {
            log_message('INFO', 'In postApprovalRoService@approvalMailToBHAndCoo | Mail was not sent to Scheduler, Operation ');
        }
    }

    /**
     * @param $networkData
     * @return string
     */
    public function networkAndTotalPayoutHtmlTable($networkData)
    {
        log_message('info','In postApprovalRoService@networkAndTotalPayoutHtmlTable | Network Data => '.print_r($networkData,True));
        $tableStr = "<table>
											<tr>
												<td><b>Network Name</b></td>
												<td><b>Total Payout</b></td>
											</tr>";
        foreach ($networkData as $nw) {
            $tableStr = $tableStr . "<tr>";
            $tableStr = $tableStr . "<td>" . $nw['network_name'] . "</td>";
            $tableStr = $tableStr . "<td>" . $nw['nw_payout'] . "</td>";
            $tableStr = $tableStr . "</tr>";
        }
        $tableStr = $tableStr . "</table>";
        log_message('info','In postApprovalRoService@networkAndTotalPayoutHtmlTable | TableHtml String is '.print_r($tableStr,True));
        return $tableStr;
    }

    /**
     * Author: Yash
     * Date: 11 November, 2019
     *
     * @param $RoId
     * Description: Update Status in DB
     */
    public function statusUpdation($RoId)
    {
        log_message('debug','In postApprovalRoService @ statusUpdation | Entered');
        //update ro status
        $this->createExtRoFeatureObj->updateDataForRoIdInRoStatus($RoId, 'approved');

        //update pending status in ro_cancel_external_ro
        $this->postApprovalFeatureObj->updateApprovalStatus(array('ext_ro_id' => $RoId, 'cancel_type' => 'ro_approval'), array('cancel_ro_by_admin' => 1));

        $this->postApprovalFeatureObj->updateInRoProgressionMailStatus(array('ro_id' => $RoId), array('approved_status' => 'approved'));

        log_message('debug','In postApprovalRoService @ statusUpdation | Exited');
    }
}
