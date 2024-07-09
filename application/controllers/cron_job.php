<?php

use application\feature_dal\GenerateRoPdfFeature;
use application\services\common_services\EmailService;
use application\services\feature_services\GenerateRoPdfService;
use Illuminate\Database\Capsule\Manager as DB;
//use Illuminate\Database\QueryException;

include_once APPPATH . 'feature_dal/generate_ro_pdf_feature.php';
include_once APPPATH . 'services/feature_services/generate_ro_pdf_service.php';
include_once APPPATH . 'services/common_services//email_service.php';

class Cron_job extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ro_model');
        $this->load->model('mg_model');
        $this->load->model('user_model');
        $this->load->model('am_model');
        $this->load->model('non_fct_ro_model');
        $this->load->helper('url');
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->model("channel_aggregate_model");
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
        $this->CI = &get_instance();
        
    }

    public function try_pdf()
    {
        $pdfServiceObj = new GenerateRoPdfService();
        log_message("info",'In cron_job | try_pdf Entered!!');
	try{
        	$pdfServiceObj->try_pdf();
	}catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		var_dump($e->getMessage());
	}
        log_message("info",'In cron_job | try_pdf Exit!!');
    }

    public function index()
    {
    }

    public function approve_campaigns()
    {

        /*		require_once '/opt/lampp/htdocs/surewaves/cron/inc/header.inc.php';

                if(!lockManager::acquire_lock("approve_campaigns"))
        {
                echo "exiting as already approve_campaigns cron running " . " at time : " . date("Y-m-d H:i:s",time()) . "\n";
                exit;
        }
                $ret = lockManager::acquire_lock("approve_campaigns");

  */
        $this->load->library('Cron_Lock_Manager');
        $isLock = $this->lock('approve_campaigns');
        if ($isLock) {
            $campaigns = $this->ro_model->get_all_campaigns();

            foreach ($campaigns as $cmp) {
                if ($cmp['approval_status'] == 0) {
                    $campaign_id = $cmp['campaign_id'];

                    $campaign_status = file_get_contents(SERVER_NAME . "/surewaves/apis/approve_campaign.php?campaign_id=$campaign_id");
                    sleep(10);
                    $result = json_decode($campaign_status, true);

                    if ($result['status'] = 'success') {
                        $value = 1;
                    } else {
                        $value = 0;
                    }
                    $this->mg_model->update_campaign_status($campaign_id, $value);
                }
            }

            $this->cron_lock_manager->unlock('approve_campaigns');
        }


        /*foreach($result as $key => $res) {
			if($res['status'] = 'success')
			{
				$value = 1;
			}
			else {
				$value = 0;
			}
			$this->mg_model->update_campaign_status($key,$value);
		 } */
        /*		$ret = lockManager::release_lock("approve_campaigns");

                echo "Approved all campaigns";

                exit;
    */
    }

    public function lock($functionName)
    {
        $this->load->library('Cron_Lock_Manager');
        if ($this->cron_lock_manager->lock($functionName) !== FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Author: Yash & Biswa Bijayee Mishra
     * Date: 20 November, 2019 
     *
     * Description: Send PDFs to network, detailing about full Schedule of playing content
     */
    public function generate_ro_pdfs()
    {
        $this->load->library('Cron_Lock_Manager');
        try {
            
            $isLock = $this->lock('generate_ro_pdfs');
            log_message('info', 'In cron_job@generate_ro_pdfs | Entered');

            if ($isLock) {
                log_message('info', 'In cron_job@generate_ro_pdfs | Lock Acquired');
                $featureObj = new GenerateRoPdfFeature();
                $pdfServiceObj = new GenerateRoPdfService();
                $condition = array('pdf_generation_status' => 0, 'pdf_processing' => 0);

                $networks = $featureObj->getNetworkDetails($condition);
                log_message('info', 'In cron_job@generate_ro_pdfs | Network Details are ' . print_r($networks, True));

                if(count($networks) == 0 || empty($networks)){
                    log_message('info', 'In cron_job@generate_ro_pdfs | No Network For Pdf');
                    $this->cron_lock_manager->unlock('generate_ro_pdfs');
                    log_message('info', 'In cron_job@generate_ro_pdfs | Cron UnLocked Exiting');
                    return;
                }

                $internalRoNo = array_column($networks, 'internal_ro_number');
                $internalRoNo = array_unique($internalRoNo);
                log_message('info', 'In cron_job@generate_ro_pdfs | Unique IntRoNo ' . print_r($internalRoNo, True));

                $NetworkIDsForInternalRO = array();
                foreach ($internalRoNo as $IRN) {
                    $NetworkIDsForInternalRO[$IRN] = array();
                }

                $RoDetailNetworkWise = array();
                foreach ($networks as $n) {
                    array_push($NetworkIDsForInternalRO[$n['internal_ro_number']], $n['customer_id']);

                    $RoDetailNetworkWise[$n['internal_ro_number']][$n['customer_id']] = array('customer_name' => $n['customer_name'],
                        'billing_name' => $n['billing_name'],
                        'ChannelName' => $n['ChannelName'],
                        'client_name' => $n['client_name'],
                        'Net_Amount' => $n['Net_Amount'],
                        'revision_no' => $n['revision_no'],
                        'customer_share' => $n['customer_share'],
                        'channel_id' => $n['channel_id'],
                        'Rate' => $n['Rate']
                    );
                }
                log_message('info', 'In cron_job@generate_ro_pdfs | NetworkIdsPerInternalRONo ' . print_r($NetworkIDsForInternalRO, True));
                log_message('info', 'In cron_job@generate_ro_pdfs | RoDetailNetworkWise' . print_r($RoDetailNetworkWise, True));

                foreach ($NetworkIDsForInternalRO as $internalRoNo => $networkIds) {
                    log_message('info', 'In cron_job@generate_Ro_pdf | Inside for loop NetworkIds ' . print_r($networkIds, True));
                    $detail[$internalRoNo] = $featureObj->getMarketClusterAndCampaignPeriod($internalRoNo, implode(',', $networkIds));
                    log_message('info', 'In cron_job@generate_ro_pdfs | MarketForAllNetworkPerInternalRoNo' . print_r($detail[$internalRoNo], True));
                    $marketCampaignPeriodDetailNetworkWise = array();
                    foreach ($detail[$internalRoNo] as $iterator) {
                        $marketCampaignPeriodDetailNetworkWise[$iterator['enterprise_id']] = array('Market_Cluster' => $iterator['Market_Cluster'],
                            'start_date' => $iterator['start_date'],
                            'end_date' => $iterator['end_date'],
                            'customer_ro' => $iterator['customer_ro_number'],
                            'client_name' => $iterator['client_name'],
                            'agency_name' => $iterator['agency_name']
                        );
                    }
                    log_message('info', 'In cron_job@generate_ro_pdfs | MarketCampaignPeriod ' . print_r($marketCampaignPeriodDetailNetworkWise, True));

                    /*$differentTimeBandNwId = array();
                    $GTPLNwIDs = array(452, 494, 495, 520, 530, 531);
                    foreach ($GTPLNwIDs as $Id) {
                        if (in_array($Id, $networkIds)) {
                            array_push($differentTimeBandNwId, $Id);
                        }
                    }
                    $normalTimeBandNwId = array_diff($networkIds, $differentTimeBandNwId);
                    log_message('info', 'In cron_job@generate_ro_pdfs | NormalTimeBandNwId'.print_r($normalTimeBandNwId,True));
                    log_message('info', 'In cron_job@generate_ro_pdfs | DiffTimeBandNwId'.print_r($differentTimeBandNwId,True));

                    $timeBandValue = CID_TIMEBAND;
                    $timeBands = explode(",", $timeBandValue);
                    for ($i = 0; $i < count($timeBands); $i++) {
                        $time_val = explode("#", $timeBands[$i]);

                        $tmp = array();
                        $tmp['start_time'] = $time_val[0];
                        $tmp['end_time'] = $time_val[1];

                        $timeBand[$i] = $tmp;
                    }
                    log_message('info', 'In cron_job@generate_ro_pdfs | TimeBands => '.print_r($timeBand,True));
                    $i = 0;
                    foreach ($timeBand as $times) { // Run 5 times
                        $start = $times['start_time'] . ":00";
                        if ($times['end_time'] == '23:59') {
                            $end = $times['end_time'] . ":59";
                        } else {
                            $end = $times['end_time'] . ":00";
                        }
                        $detailedScheduleDataPerInternalRoNo[$internalRoNo][$i] = $featureObj->getScheduleDataTimeBandWise($internalRoNo, implode(',',$normalTimeBandNwId), $start, $end);
                        $i++;
                    }
                    log_message('info', 'In cron_job@generate_ro_pdfs | DetailScheduleDataPerInternalRo'.print_r($detailedScheduleDataPerInternalRoNo[$internalRoNo],True));
                    if (!empty($differentTimeBandNwId)) {
                        $timeBandValue = CID_TIMEBAND_GTPL;
                        $timeBands = explode(",", $timeBandValue);
                        for ($i = 0; $i < count($timeBands); $i++) {
                            $time_val = explode("#", $timeBands[$i]);

                            $tmp = array();
                            $tmp['start_time'] = $time_val[0];
                            $tmp['end_time'] = $time_val[1];

                            $timeBand[$i] = $tmp;
                        }
                        $i = 0;
                        foreach ($timeBand as $times) { // Run 5 times
                            $start = $times['start_time'] . ":00";
                            if ($times['end_time'] == '23:59') {
                                $end = $times['end_time'] . ":59";
                            } else {
                                $end = $times['end_time'] . ":00";
                            }
                            $detailedScheduleDataForDT[$internalRoNo][$i] = $featureObj->getScheduleDataTimeBandWise($internalRoNo, implode(',',$differentTimeBandNwId), $start, $end);
                            $detailedScheduleDataPerInternalRoNo[$internalRoNo][$i] = array_merge(
                                $detailedScheduleDataPerInternalRoNo[$internalRoNo][$i],
                                $detailedScheduleDataForDT[$internalRoNo][$i]
                            );
                            $i++;
                        }
                        log_message('info', 'In cron_job@generate_ro_pdfs | DetailScheduleDataForDT'.print_r($detailedScheduleDataForDT,True));
                    }
                    log_message('info', 'In cron_job@generate_ro_pdfs | DetailScheduleDataPerInternalRo After Merging => '.print_r($detailedScheduleDataPerInternalRoNo,True));*/
                    $timeBandValue = CID_TIMEBAND;
                    $timeBands = explode(",", $timeBandValue);
                    for ($i = 0; $i < count($timeBands); $i++) {
                        $time_val = explode("#", $timeBands[$i]);

                        $tmp = array();
                        $tmp['start_time'] = $time_val[0];
                        $tmp['end_time'] = $time_val[1];

                        $timeBand[$i] = $tmp;
                    }
                    log_message('info', 'In cron_job@generate_ro_pdfs | TimeBands => ' . print_r($timeBand, True));
                    $i = 0;
                    foreach ($timeBand as $times) { // Run 5 times
                        $start = $times['start_time'] . ":00";
                        if ($times['end_time'] == '23:59') {
                            $end = $times['end_time'] . ":59";
                        } else {
                            $end = $times['end_time'] . ":00";
                        }
                        $detailedScheduleDataPerInternalRoNo[$internalRoNo][$i] = $featureObj->getScheduleDataTimeBandWise($internalRoNo, implode(',', $networkIds), $start, $end);
                        $i++;
                    }
                    log_message('info', 'In cron_job@generate_ro_pdfs | DetailScheduleDataPerInternalRo' . print_r($detailedScheduleDataPerInternalRoNo[$internalRoNo], True));

                    $scheduleDetailNetworkWise = array();
                    $channelIDs = array();
                    $DayParts = array();
                    foreach ($detailedScheduleDataPerInternalRoNo[$internalRoNo] as $key => $timeBandWise) { // Run 5 time
                        $DayPart = array();
                        array_push($DayPart, $timeBand[$key]['start_time']);
                        array_push($DayPart, $timeBand[$key]['end_time']);
                        $DayPart = implode('-', $DayPart);            // converts in a string e.g. "06:00-10:00"
                        array_push($DayParts, $DayPart);
                        foreach ($timeBandWise as $t) { // Run no of entries in one time bands
                            if (!isset($scheduleDetailNetworkWise[$internalRoNo][$t['enterprise_id']][$DayPart])) {
                                $scheduleDetailNetworkWise[$internalRoNo][$t['enterprise_id']][$DayPart] = array();
                            }
                            array_push($scheduleDetailNetworkWise[$internalRoNo][$t['enterprise_id']][$DayPart],
                                array('DATE' => $t['DATE'],
                                    'TotalImp' => $t['TotalImp'],
                                    'channel_id' => $t['channel_id'], // for channel Name
                                    'caption_name' => $t['caption_name'],
                                    'screen_region_id' => $t['screen_region_id'],
                                    'brand_new' => $t['brand_new'],
                                    'language' => $t['language'],
                                    'ro_duration' => $t['ro_duration']
                                )
                            );
                            if (!isset($channelIDs[$t['enterprise_id']]))
                                $channelIDs[$t['enterprise_id']] = array();
                            array_push($channelIDs[$t['enterprise_id']], $t['channel_id']);
                        }
                    }
                    log_message('info', 'In cron_job@generate_ro_pdfs | ScheduleDataNetworkWise => ' . print_r($scheduleDetailNetworkWise, True));
                    foreach ($channelIDs as $nwId => $Channels) {
                        $channelIDs[$nwId] = array_unique($Channels);
                    }
                    log_message('info', 'In cron_job@generate_ro_pdfs | Channel Ids For All Networks=> ' . print_r($channelIDs, True));
                   
                    foreach ($networkIds as $nwId) {
                        $networkRoNo = '';
                        DB::beginTransaction();
                        $condition = array('internal_ro_number' => $internalRoNo, 'customer_id' => $nwId);
                        $statusData = array('pdf_generation_status' => 0, 'pdf_processing' => 1);
                        $featureObj->updatePdfStatusInRoApprovedNetworks($condition, $statusData);
                        log_message('info', 'In cron_job@generate_ro_pdfs | Updated ProcessingStatus as 1 for NwId ' . print_r($nwId, True));

                        $pdfData = $pdfServiceObj->generatePdfData($RoDetailNetworkWise[$internalRoNo][$nwId],
                            $marketCampaignPeriodDetailNetworkWise[$nwId],
                            $internalRoNo, $nwId,
                            $scheduleDetailNetworkWise[$internalRoNo][$nwId],
                            $channelIDs[$nwId], $DayParts);
			//echo "pdfData---";echo "<pre>";print_r($pdfData);
                        if($pdfData['gotError']){
                            DB::rollBack();
                            continue;
                        }
                        log_message('info', 'In cron_job@generate_ro_pdfs | PdfData for NwId ' . $nwId . ' => ' . print_r($pdfData, True));
                        $networkRoNo = $pdfData['data']['nw_ro_number'];
			echo "networkRoNo----".$networkRoNo;echo "<br>";
                        $pdfFilesDetails    = $pdfServiceObj->GeneratePdf($pdfData['data']);
			echo "pdfFilesDetails---"; echo "<pre>";print_r($pdfFilesDetails);
                        if($pdfFilesDetails['gotError']){
                            log_message('info', 'In cron_job@generate_ro_pdfs | Rolling back as function as GeneratePdf have error');
                            DB::rollBack();
                            continue;
                        }
                        $pdfS3UrlsData      = $pdfServiceObj->uploadPdfToS3($pdfFilesDetails['data']);
			echo "pdfS3UrlsData---";echo "<pre>";print_r($pdfS3UrlsData);	
                        if($pdfS3UrlsData['gotError']){
                            log_message('info', 'In cron_job@generate_ro_pdfs | Rolling back as function uploadPdfToS3 have error');
                            DB::rollBack();
                            continue;
                        }
                        // $pdfS3UrlsData['pdfS3Urls'],
			/*$CI = &get_instance();
        		$CI->load->library('email');
        		$CI->email->clear(TRUE);
                        $fromSureWavesEmail = $CI->config->item('from_email');*/
			$fromSureWavesEmail = '';
			
			echo "fromSureWavesEmail---".$fromSureWavesEmail;echo "<br>";
                        $preparedMailDataForNetworks = $pdfServiceObj->getMailDataForNetwork($pdfData['data'],$fromSureWavesEmail);
			echo "preparedMailDataForNetworks---";echo "<pre>";print_r($preparedMailDataForNetworks);
                        if($preparedMailDataForNetworks['gotError']){
                            log_message('info', 'In cron_job@generate_ro_pdfs | Rolling back as function getMailDataForNetwork have error');
                            DB::rollBack();
                            continue;
                        }

                        $storedResponse = $pdfServiceObj->storeMailDataBeforeSending($preparedMailDataForNetworks['data'],
                                                                    $pdfS3UrlsData['data']['pdfS3Urls'],
                                                                    $pdfFilesDetails['data']['filepaths'],$networkRoNo );
                        
                        if($storedResponse['gotError']){
                            log_message('info', 'In cron_job@generate_ro_pdfs | Rolling back as function storeMailDataBeforeSending have error');
                            DB::rollBack();
                            continue;
                        }

                        $mailResponse = $pdfServiceObj->sendPdfOverMail($preparedMailDataForNetworks['data'],$pdfFilesDetails['data']['filepaths']);
                        if($mailResponse['gotError']){
                            log_message('info', 'In cron_job@generate_ro_pdfs | Rolling back as function sendPdfOverMail have error');
                            DB::rollBack();
                            continue;
                        }

                        $statusData = array('pdf_generation_status' => 1, 'pdf_processing' => 0);
                        $featureObj->updatePdfStatusInRoApprovedNetworks($condition, $statusData);
                        DB::commit();
                        log_message('info', 'In cron_job@generate_ro_pdfs | Updated GenerationStatus as 1 for NwId ' . print_r($nwId, True));
                    }
                    
                }

                $this->cron_lock_manager->unlock('generate_ro_pdfs');
                log_message('info', 'In cron_job@generate_ro_pdfs | Cron UnLocked Exiting');
            }
            else{
                log_message('info', 'In cron_job@generate_ro_pdfs | Lock is Not Acquired So Exiting');
            }
            
        }catch (Exception $e) {
            log_message('ERROR', 'In cron_job@generate_ro_pdfs | exception occurred - ' . print_r($e->getMessage(), true));
            //DB::rollBack();
            log_message('DEBUG', 'In cron_job@generate_ro_pdfs | Database Rolled Back successfully!');
	        $this->cron_lock_manager->unlock('generate_ro_pdfs');
	        log_message('info', 'In cron_job@generate_ro_pdfs | Cron UnLocked after db rollback Exiting');
        }
    }

    /**
     * Author: Yash
     * Date: 20 November, 2019
     *
     * @param $roDetail
     * @param $marketCampaignPeriod
     * @param $internalRoNo
     * @param $nwId
     * @param $scheduleData
     * @param $channelIds
     * @param $DayParts
     * @return array
     */
    public function generatePdfData($roDetail, $marketCampaignPeriod, $internalRoNo, $nwId, $scheduleData, $channelIds, $DayParts)
    {
        log_message('info', 'In cron_job@generatePdfData | Entered with ScheduleData=> ' . print_r($scheduleData, True));
        $featureObj = new GenerateRoPdfFeature();
        $customerName = $roDetail['customer_name'];
        $networkRoNo = $this->get_nw_seq_no($internalRoNo, $customerName);
        $marketCluster = $marketCampaignPeriod['Market_Cluster'];
        log_message('info', 'In cron_job@generatePdfData | NetworkRoNo => ' . print_r($networkRoNo, True));

        $AllChannelIdsInNetwork = explode(',',$roDetail['channel_id']);
        log_message('info', 'In cron_job@generatePdfData | AllChannelIdInNetwork => ' . print_r($AllChannelIdsInNetwork, True));
        $contentDownloadLink = $featureObj->getContentLinks($internalRoNo, $nwId, $AllChannelIdsInNetwork);
        $showLink = 0;
        if (count($contentDownloadLink) > 0) {
            $showLink = 1;
        }
        log_message('info', 'In cron_job@generatePdfData | ContentDownloadLink => ' . print_r($contentDownloadLink, True));

        $channelsDetail = $featureObj->getChannelNames($channelIds);
        $channelNameByID = array();
        foreach ($channelsDetail as $channelData) {
            $channelNameByID[$channelData['tv_channel_id']] = $channelData['channel_name'];
        }
        log_message('info', 'In cron_job@generatePdfData | ChannelNameById => ' . print_r($channelNameByID, True));

        log_message('info', 'In cron_job@generatePdfData | ShowLink = ' . print_r($showLink, True));
        $startDate = $marketCampaignPeriod['start_date'];
        $endDate = $marketCampaignPeriod['end_date'];

        $completeCancellation = false;
        $cancelStartDate = '';
        if (!isset($marketCampaignPeriod['start_date']) || !isset($marketCampaignPeriod['end_date'])) {
            $res = $featureObj->getNetworkCancelDate($internalRoNo, $nwId);
            log_message('info', 'In cron_job @ generatePdfData | Data in complete Cancellation=> ' . print_r($res, True));
            $completeCancellation = true;
            $cancelStartDate = $res[0]['start_date'];
            $startDate = $res[0]['start_date'];
            $endDate = $res[0]['end_date'];
            $marketCluster = $res[0]['Market_Cluster'];
        }

        $nwDates = array("start_date" => $startDate, "end_date" => $endDate);
        log_message('info', 'In cron_job@generatePdfData | NwDates => ' . print_r($nwDates, True));
        $month1 = date('m', strtotime($startDate));
        $month2 = date('m', strtotime($endDate));
        $year_1 = date('Y', strtotime($startDate));
        $year_2 = date('Y', strtotime($endDate));

        if ($year_2 > $year_1) {
            $year_gap = $year_2 - $year_1;
            $month2 = $year_gap * 12 + $month2;
        }

        $noOfMonths = $month2 - $month1 + 1; // Send to UI
        log_message('info', 'In cron_job@generatePdfData NoOFMonths => | ' . print_r($noOfMonths, True));
        $start_date = date('Y-m-d', strtotime($startDate));
        $end_date = date('Y-m-d', strtotime($endDate));
        $day = 86400; // Day in seconds
        $format = 'Y-m-d'; // Output format (see PHP date function)
        $sTime = strtotime($start_date); // Start as time  // return seconds from beginning till this date
        $eTime = strtotime($end_date); // End as time
        $numDays = round(($eTime - $sTime) / $day) + 1;
        log_message('info', 'In cron_job@generatePdfData | NumDays => ' . print_r($numDays, True));
        log_message('info', 'num_days=> ' . print_r($numDays, True));
        $days = array();            // Send to UI
        for ($d = 0; $d < $numDays; $d++) {
            $days[] = date($format, ($sTime + ($d * $day)));
        }
        log_message('info', 'In cron_job@generatePdfData | Days => ' . print_r($days, True));

        $tmp = date('mY', $eTime);
        $months[] = date('F', $sTime);

        while ($sTime < $eTime) {
            $sTime = strtotime(date('Y-m-d', $sTime) . ' +1 month');
            if (date('mY', $sTime) != $tmp && ($sTime < $eTime)) {
                $months[] = date('F', $sTime);
            }
        }
        $months[] = date('F', $eTime);
        log_message('info', ' Months Array => ' . print_r($months, True));
        $activityMonths = implode(',', array_unique($months));

        $report = array();
        $regionArray = array(1 => 'Spot Ad', 3 => 'Banner Ad');
        foreach ($scheduleData as $timeBand => $timeWise) { // runs 5 times
            foreach ($timeWise as $data) {
                if (!array_key_exists($regionArray[$data['screen_region_id']], $report)) {
                    $report[$regionArray[$data['screen_region_id']]] = array();
                }
                if (!array_key_exists($channelNameByID[$data['channel_id']], $report[$regionArray[$data['screen_region_id']]])) {
                    $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]] = array();
                }
                if (!array_key_exists($data['caption_name'], $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]])) {
                    $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]][$data['caption_name']] = array();
                }
                if (!array_key_exists($timeBand, $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]][$data['caption_name']])) {
                    $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]][$data['caption_name']][$timeBand] = array();
                }
                $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]][$data['caption_name']][$timeBand]['brand_new'] = $data['brand_new'];
                $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]][$data['caption_name']][$timeBand]['language'] = $data['language'];
                $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]][$data['caption_name']][$timeBand]['ro_duration'] = $data['ro_duration'];
                $date = date('Y-m-d', strtotime($data['DATE']));
                if (in_array($date, $days))
                    $report[$regionArray[$data['screen_region_id']]][$channelNameByID[$data['channel_id']]][$data['caption_name']][$timeBand][$date] = $data['TotalImp'];
            }
            log_message('info', 'In cron_job@generatePdfData | Report Array for TimeBand ' . $timeBand . ' and Cummulative TimeBands is => ' . print_r($report, True));
        }
        log_message('info', 'In cron_job@generatePdfData | Report Array for all time bands => ' . print_r($report, True));

        // For those timeBands whose doesn't have any schedule
        foreach ($report as $ad => $adtype) {
            foreach ($adtype as $chnl => $channel) {
                foreach ($channel as $cap => $caption) {
                    foreach ($caption as $tb => $timeband) {
                        $brand = $timeband['brand_new'];
                        $language = $timeband['language'];
                        $roDuration = $timeband['ro_duration'];
                        foreach ($days as $day) {
                            if (!isset($timeband[$day]))
                                $report[$ad][$chnl][$cap][$tb][$day] = 0;
                        }
                    }
                    foreach ($DayParts as $dayTime) {
                        if (!array_key_exists($dayTime, $caption)) {
                            $report[$ad][$chnl][$cap][$dayTime]['brand_new'] = $brand;
                            $report[$ad][$chnl][$cap][$dayTime]['language'] = $language;
                            $report[$ad][$chnl][$cap][$dayTime]['ro_duration'] = $roDuration;
                            foreach ($days as $day)
                                $report[$ad][$chnl][$cap][$dayTime][$day] = 0;
                        }
                    }
                    ksort($report[$ad][$chnl][$cap]);
                }
            }
        }
        log_message('Info', 'In Cron_job @ generatePdfData | Report Array after fixing=>' . print_r($report, True));

        $reports = array();
        foreach ($report as $ad => $adtype) {
            foreach ($adtype as $chnl => $channel) {
                foreach ($channel as $cap => $caption) {
                    foreach ($caption as $tb => $timeband) {
                        $reports[] = array_merge(array("ad_type" => $ad, "channel_name" => $chnl, "caption_name" => $cap, "timeband" => $tb), $timeband);
                        //$timeband => array('brand' => sd, 'language' => das, 'ro_Duration' => dsa, 'Date1' => 1, 'Date2' => 2 ....., 'DateN   ' => n)
                    }
                }
            }
        }
        log_message('info', 'In cron_job@generatePdfData | Final Reports => ' . print_r($reports, True));

        //Integration For Br
        $this->mg_model->insert_into_br_ro_schedule($internalRoNo); // sv_br_ro_schedule

        $networkRoReportDetails['market'] = $marketCluster;
        $networkRoReportDetails['start_date'] = $nwDates['start_date'];
        $networkRoReportDetails['end_date'] = $nwDates['end_date'];
        $networkRoReportDetails['activity_months'] = $activityMonths;
        $networkRoReportDetails['gross_network_ro_amount'] = $roDetail['Net_Amount'];
        $networkRoReportDetails['customer_share'] = $roDetail['customer_share'];
        $networkRoReportDetails['net_amount_payable'] = round(($roDetail['Net_Amount'] * ($roDetail['customer_share']) / 100));
        $networkRoReportDetails['release_date'] = date("Y-m-d H:i:s");
        $check = $this->mg_model->get_network_ro_report($networkRoNo);
        if (empty($check)) {
            $networkRoReportDetails['network_ro_number'] = $networkRoNo;
            $networkRoReportDetails['customer_name'] = $customerName;
            $networkRoReportDetails['customer_ro_number'] = $marketCampaignPeriod['customer_ro'];
            $networkRoReportDetails['client_name'] = $marketCampaignPeriod['client_name'];
            $networkRoReportDetails['agency_name'] = $marketCampaignPeriod['agency_name'];
            $networkRoReportDetails['billing_name'] = $roDetail['billing_name'];
            $networkRoReportDetails['internal_ro_number'] = $internalRoNo;
            $this->ro_model->add_network_ro_report_details($networkRoReportDetails);
        } else {
            $where_data = array(
                'network_ro_number' => $networkRoNo
            );
            $this->mg_model->update_network_ro_report_detail($networkRoReportDetails, $where_data);
        }
        log_message('info', 'In cron_job@generatePdfData | NetworkRoReportDetails Insert Data is => ' . print_r($networkRoReportDetails, True));

        $networkAmountDetails = array(
            'customer_share' => $roDetail['customer_share'],
            'billing_name' => $roDetail['billing_name'],
            'network_amount' => $roDetail['Net_Amount'],
            'revision_no' => $roDetail['revision_no']
        );
        $networkDetails = $featureObj->getCustomerDetail($nwId);
        $pdfArray = array();
        $pdfArray['cancel_invoice_details_html'] = $this->sendToViewWithCancelNWInvoiceDetails($networkRoNo);
        $pdfArray['internal_ro_number'] = $internalRoNo;
        $pdfArray['reports'] = $reports;
        $pdfArray['cc_emails_list'] = BH_EmailID;
        $pdfArray['ro_details'] = array(array('customer_name' => $customerName, 'client_name' => $roDetail['client_name'])); //=> Network Name AND Client Name
        $pdfArray['channels'] = $roDetail['ChannelName'];
        $pdfArray['market_cluster_name'] = $marketCluster;
        $pdfArray['dates'] = $nwDates;
        $pdfArray['days'] = $days;
        $pdfArray['no_of_months'] = $noOfMonths;
        $pdfArray['network_details'] = array('customer_name' => $customerName);
        $pdfArray['network_amount_details'] = $networkAmountDetails;
        $pdfArray['content_download_link'] = $contentDownloadLink;
        $pdfArray['show_link'] = $showLink;
        $pdfArray['nw_ro_number'] = $networkRoNo;
        $pdfArray['complete_cancellation'] = $completeCancellation;
        $pdfArray['cancel_start_date'] = $cancelStartDate;
        $pdfArray['cancel_ro'] = $this->mg_model->check_for_ro_cancellation($internalRoNo);
        $pdfArray['nw_ro_to_email_list'] = get_nw_ro_to_email_list($internalRoNo, $networkDetails[0]);
        $pdfArray['nw_ro_email'] = get_nw_ro_email($internalRoNo);
        $pdfArray['Rate'] = $roDetail['Rate'];

        if ($pdfArray['cancel_invoice_details_html']['status']) {
            log_message('info', 'In cron_job@generatePdfData | Updating pdf_processing as 1 in Ro_Cancel_Invoice');
            $this->mg_model->updateStatusForRoCancelInvoiceData($networkRoNo);
        }
        return $pdfArray;
    }

    public function get_nw_seq_no($internal_ro_number, $customer_name)
    {
        $qry = "select * from ro_network_ro_report_details where internal_ro_number='$internal_ro_number' and customer_name='$customer_name'";
        $res = $this->db->query($qry);
        $result = $res->result('array');
        if (count($result) > 0) {
            return $result[0]['network_ro_number'];
        } else {
            /*$qry = "select count(distinct (internal_ro_number,customer_name)+1 as nw_seq_no from ro_network_ro_report_details where internal_ro_number='$internal_ro_number'*/

            $qry = "SELECT  distinct(customer_name) FROM ro_network_ro_report_details WHERE internal_ro_number = '$internal_ro_number'";
            $res = $this->db->query($qry);
            if ($res->num_rows() > 0) {
                $result = $res->result('array');
                $count_val = count($result);
                $count_val = $count_val + 1;
                $count_val = sprintf('%03d', $count_val);
                return $internal_ro_number . "/" . $count_val . "/" . $customer_name;
            } else {
                $count_val = 1;
                $count_val = sprintf('%03d', $count_val);
                return $internal_ro_number . "/" . $count_val . "/" . $customer_name;
            }

        }
    }

    public function make_unique_channel($channels)
    {
        $data = array();
        foreach ($channels as $chnl) {
            $chnl_value = explode(",", $chnl);
            foreach ($chnl_value as $val) {
                if (!in_array($val, $data)) {
                    array_push($data, $val);
                } else {
                    continue;
                }
            }
        }
        return $data;
    }

    public function sendToViewWithCancelNWInvoiceDetails($nw_ro_number)
    {
        $whereCancelInvoiceArray = array('network_ro_number' => $nw_ro_number, 'pdf_processing' => 0);
        $cancelInvoiceNetworkDetails = $this->mg_model->getAllRoCancelInvoiceData($whereCancelInvoiceArray);
        log_message('info', 'ro_cancel_invoice printing whereCancelInvoiceArray array - ' . print_r($whereCancelInvoiceArray, TRUE));
        log_message('info', 'ro_cancel_invoice printing cancelInvoiceNetworkDetails array - ' . print_r($cancelInvoiceNetworkDetails, TRUE));
        $htmlDataWithStatus = array('status' => false, 'html' => null, 'network_ro_number' => null, 'revision' => null);
        if (count($cancelInvoiceNetworkDetails) > 0) {
            $htmlDataWithStatus = $this->mg_model->convertCancelDetailsIntoHtml($cancelInvoiceNetworkDetails[0]);
        }
        log_message('info', 'ro_cancel_invoice printing htmlDataWithStatus array - ' . print_r($htmlDataWithStatus, TRUE));
        return $htmlDataWithStatus;
    }

    public function get_monthly_details($dates, $reports)
    {

        $start_date = date('Y-m-d', strtotime($dates['start_date']));
        $end_date = date('Y-m-d', strtotime($dates['end_date']));

        $month1 = date('m', strtotime($dates['start_date']));
        $month2 = date('m', strtotime($dates['end_date']));

        $no_of_months = $month2 - $month1 + 1;

        $camp_start_date = strtotime(date('Y-m-d', strtotime($dates['start_date'])));
        $month = date('m', $camp_start_date);
        $year = date('Y', $camp_start_date);
        $month_start_date = date('Y-m-01', $camp_start_date);
        $month_end_date = date('Y-m-t', $camp_start_date);
        $no_of_days = round(abs(strtotime(date('Y-m-d', strtotime($month_end_date))) - strtotime(date('Y-m-d', strtotime($month_start_date)))) / 86400) + 1;
        $month_start_date = strtotime($month_start_date);
        $day = "";
        $tsec = 0;

        $monthly_details = array();
        for ($m = 0; $m < $no_of_months; $m++) {
            $month_end_date = date('Y-m-t', $month_start_date);
            $no_of_days = round(abs(strtotime(date('Y-m-d', strtotime($month_end_date))) - strtotime(date('Y-m-d', $month_start_date))) / 86400) + 1;
            $k = 0;
            $month = date('F', $month_start_date);
            foreach ($reports as $key => $r) {
                $sec = 0;
                if ($k < count($reports)) {
                    $monthly_details[$k]['channel_name'] = $reports[$k]['channel_name'];
                    $monthly_details[$k]['ad_type'] = $reports[$k]['ad_type'];
                    $msec = 0;
                    for ($j = 0; $j < 5; $j++) {
                        $sec = 0;
                        $tsec = 0;
                        for ($i = 0; $i < $no_of_days; $i++) {
                            $day = date('Y-m-d', strtotime("+$i day", $month_start_date));
                            $sec = $sec + $reports[$k][$day];
                            $tsec = $tsec + $reports[$k][$day] * $reports[$k]['ro_duration'];
                            $total_sec = $total_sec + $reports[$k][$day] * $reports[$k]['ro_duration'];
                        }
                        $msec = $msec + $tsec;
                        $k++;
                    }
                    $monthly_details[$k - 5][$m]['month'] = $month;
                    $monthly_details[$k - 5][$m]['total_seconds'] = $msec;
                }
            }
            $month_start_date = strtotime($day) + 86400;
            $month++;
        }
        $details = array_values($monthly_details);
        echo "<pre>";
        print_r($details);
    }

    public function send_mail()
    {
        $mail_data = $this->ro_model->get_mail_data();


        foreach ($mail_data as $data) {
            $id = $data['id'];
            $emails_list = unserialize($data['emails_list']);
            $text = $data['text'];
            //$subject = array('NETWORK_RO' => $data['subject']) ;
            $message = unserialize($data['message']);
            // sending old nw ro number
            $internal_ro_number = $message['INTERNAL_RO'];
            $network_name = $message['NETWORK_NAME'];
            $rev_no = $this->mg_model->get_revision_number($internal_ro_number, $network_name);
            $rev_no = $rev_no - 1;
            $nw_ro = $this->mg_model->get_nw_ro_number($internal_ro_number, $network_name);
            if ($rev_no > 0) {
                $old_nw_ro_number = $nw_ro . '-R' . $rev_no;
            } else {
                $old_nw_ro_number = $nw_ro;
            }
            // end
            $subject = array('NETWORK_RO' => $old_nw_ro_number);
            $file_location = unserialize($data['file_location']);
            $cc = unserialize($data['cc']);
            $file_name = $data['file_name'];

            $user_data = array(
                'mail_sent' => 2
            );
            $this->ro_model->update_mail_sent_data($user_data, $id);

            //$this->load->library('S3');
            require_once("S3.php");
            $s3 = new S3(AMAZON_KEY, AMAZON_VALUE);
            S3::putObject(S3::inputFile("$file_location"), NETWORK_RO_BUCKET, "$file_name", S3::ACL_PRIVATE);
            $auth_url = "https://s3.amazonaws.com/" . NETWORK_RO_BUCKET . "/" . $file_name;
//	echo $id."<br/>".print_r($emails_list,true)."<br/>".$text."<br/>".print_r($subject,true)."<br/>".print_r($message,true)."<br/>".$file_location."<br/>".$file_name."<br/>".$cc;exit;	
            $to_list = null;
            $to_list = implode(',', $emails_list);

            //Network Containing Notice+Ro
            $noticedRoNetwork = $this->mg_model->isNetworkHaveNoticedChannelRo($network_name);
            if (!empty($noticedRoNetwork)) {
                $this->config->load('email_text');
                $channelIds = explode(",", $noticedRoNetwork);
                if (count($channelIds) == 1) {
                    $noticeRoBody = $this->config->item('single_channel_notice_ro_body');
                } else {
                    $noticeRoBody = $this->config->item('all_channels_notice_ro_body');
                }
                $noticeRoBody = str_replace('%CHANNELS%', $noticedRoNetwork, $noticeRoBody);
                $message['INSERT_NOTICE_RO'] = $noticeRoBody;
                mail_send($to_list, $text, $subject, $message, $file_location, $cc, $auth_url);
            } else {
                $message['INSERT_NOTICE_RO'] = '';
                mail_send($to_list, $text, $subject, $message, $file_location, $cc, $auth_url);
            }

            $user_data = array(
                'mail_sent' => 1,
                'actual_file_location' => $auth_url
            );
            $this->ro_model->update_mail_sent_data($user_data, $id);
            unlink($file_location);

        }
    }

    public function mail_notice_intimation()
    {
        $this->load->library('Cron_Lock_Manager');
        $isLock = $this->lock('mail_notice_intimation');
        if (!$isLock) {
            exit;
        }
        //Get Distinct internal ro number from network_ro_report which is not in ro_mail_notice_intimation
        $internalRoNumbers = $this->mg_model->remaining_notice_ro();

        //check for that ro any entry in ro_mail_data
        foreach ($internalRoNumbers as $irn) {
            $internalRoNumber = $irn['internal_ro_number'];
            $mailData = $this->ro_model->getSentMailDataForInternalRo($internalRoNumber);

            if (count($mailData) > 0) {
                $noticedChannel = $this->mg_model->getNetworkNoticedChannelForRo($internalRoNumber);
                $channel_names = "";
                if ($noticedChannel != 'Not_Noticed') {
                    $cc = get_nw_ro_email($internalRoNumber);
                    $email_text_key = "all_notice_channel";
                    foreach ($noticedChannel as $customer_id => $value) {
                        if ($value['all_channel_noticed'] == 'Yes') {
                            $is_already_inserted = $this->mg_model->getNoticeMail(array('internal_ro_number' => $internalRoNumber, 'customer_name' => $value['customer_name'], 'channelNames' => $value['channel_names'], 'mail_sent' => 1));
                            if (count($is_already_inserted) > 0) {
                                continue;
                            } else {
                                $userData = array(
                                    'internal_ro_number' => $internalRoNumber,
                                    'customer_name' => $value['customer_name'],
                                    'mail_sent' => '0',
                                    'channelNames' => $value['channel_names']
                                );
                                $this->mg_model->insertNoticeMail($userData, array('internal_ro_number' => $internalRoNumber, 'customer_name' => $value['customer_name']));
                                $to_list = $value['network_email'];
                                $message_key_values = array('CHANNELS' => $value['channel_names']);
                                email_send($to_list, $cc, $email_text_key, '', $message_key_values);

                                $this->mg_model->updateNoticeMail(array('mail_sent' => '1'), array('internal_ro_number' => $internalRoNumber));
                            }


                        }
                    }
                }


            }
        }
        $this->cron_lock_manager->unlock('mail_notice_intimation');

    }

    public function daily_email_report()
    {
        // send mail to coo/bh
        $users = $this->mg_model->daily_email_report_list();
        $data = $this->mg_model->daily_email_report_details();
        foreach ($users as $key => $user) {
            mail_send_v1($user['user_email'],
                "daily_report",
                array(
                    array(
                        'USER_NAME' => $user['user_name'],
                        'DATA' => $data
                    ),
                    '',
                    '',
                    '',
                    '')
            );
        }
    }

    public function current_month_report($userType = 0, $givenDate = NULL)
    {
        //$givenDate format will be Y-m-d format e.g. 2014-12-08

        if (empty($givenDate) || !isset($givenDate)) {
            $givenDate = date('Y-m-d');
        }

        if (!isset($userType)) {
            $userType = 0;
        }
        $current_month = date("M-y", strtotime($givenDate));
        $month = date("F", strtotime($givenDate));
        $current_month_year = date("M-Y", strtotime($givenDate));

        /*
            $this->load->model('mis_model');            
           
            $monthlyFct =  $this->mg_model->daily_email_report_details_v1($givenDate);
            $monthlyNonFct = $this->non_fct_ro_model->monthlyNonFctReport($givenDate) ;
            $financialYearFct = $this->mg_model->current_financial_email_report_details_v1($givenDate);
            $financialYearNonFct = $this->non_fct_ro_model->financialYearNonFctReport($givenDate) ;
            $financialTotalValue = $this->mg_model->financialYearTotalReport($givenDate) ; */

        //$monthlyFct =  $this->mis_model->getMonthlyFct($givenDate); echo $monthlyFct ;exit;
        /*$monthlyNonFct = $this->non_fct_ro_model->monthlyNonFctReport($givenDate) ;
            $financialYearFct = $this->mg_model->current_financial_email_report_details_v1($givenDate);
            $financialYearNonFct = $this->non_fct_ro_model->financialYearNonFctReport($givenDate) ;
            $financialTotalValue = $this->mg_model->financialYearTotalReport($givenDate) ;
            
            $to_list = $to_list.",raj@surewaves.com,mandar@surewaves.com,manajit@surewaves.com,mani@surewaves.com,tripur@surewaves.com,deepak@surewaves.com,swapnil@surewaves.com,sony@surewaves.com,nishant@surewaves.com" ;
            $emailKey = "mis_month_report_prev" ; */


        $is_fct = 1;
        $monthlyFct = $this->mg_model->getMonthlyMisReportDataClientWise($region_name = 'ALL', $givenDate, $is_fct, $userType);
        $financialYearFct = $this->mg_model->getFinancialYearMisReport_ForPrevios($region_name = 'ALL', $givenDate, $is_fct, $userType);

        $is_fct = 0;
        $monthlyNonFct = $this->mg_model->getMonthlyMisReportDataClientWise($region_name = 'ALL', $givenDate, $is_fct, $userType);
        $financialYearNonFct = $this->mg_model->getFinancialYearMisReport_ForPrevios($region_name = 'ALL', $givenDate, $is_fct, $userType);

        $financialTotalValue = $this->mg_model->getTotalFinancialYearMisReportForRegion($region_name = 'ALL', $givenDate, $userType);


        // send mail to coo/bh
        $users = $this->mg_model->daily_email_report_list($userType);
        $email_ids = array();
        foreach ($users as $val) {
            array_push($email_ids, $val['user_email']);
        }

        $to_list = implode(',', $email_ids);
        if ($userType == 0) {
            $to_list = $to_list . ",raj@surewaves.com,manajit@surewaves.com,tripur@surewaves.com,sony@surewaves.com,nishant@surewaves.com,sridhar@surewaves.com";
            $emailKey = "mis_month_report_prev";

        } else if ($userType == 2) {
            $to_list = $to_list;
            $emailKey = "mis_month_report_adv_prev";
        }

        //$to_list = "mani@surewaves.com,deepak@surewaves.com" ;
        mail_send_v1($to_list,
            $emailKey,
            array('MONTH' => $current_month),
            array(
                'MONTH' => $month,
                'CURRENT_MONTH_YEAR' => $current_month_year,
                'MONTHLY_DATA' => $monthlyFct,
                'MONTHLY_NON_FCT_DATA' => $monthlyNonFct,
                'TODAYS_DATE' => date("d-M-Y"),
                'FINANCIAL_YEAR_DATA' => $financialYearFct,
                'FINANCIAL_YEAR_NON_FCT_DATA' => $financialYearNonFct,
                'FINANCIAL_YEAR_TOTAL_DATA' => $financialTotalValue
            ),
            '',
            '',
            '',
            ''
        );
    }

    public function generateMisReport($userType = 0, $givenDate = NULL)
    {
        log_message('INFO', 'In cron_job@generateMisReport | Inside');
        if (!isset($userType)) {
            $userType = 0;
        }

        //$givenDate format will be Y-m-d format e.g. 2014-12-08
        if (empty($givenDate) || !isset($givenDate)) {
            $givenDate = date('Y-m-d');
        }
        log_message('INFO', 'In cron_job@generateMisReport | User Type and Given Date- ' . print_r(array($userType, $givenDate), true));

        $this->db->trans_start();
        //$this->mg_model->generateFCTMisReport($givenDate, $userType);
        if ($userType == 0) {
            //$this->mg_model->generateNonFCTMisReport($givenDate, $userType);
        }
        $this->db->trans_complete();

        //Email Sent To RD Once In a week On Monday As Per Requirement
        $is_international = 0;
        //if(date("w",strtotime($givenDate)) == 1) {
        $regions = $this->mg_model->getAllRoRegions(array('is_international' => $is_international));
        log_message('INFO','In cron_job@generateMisReport | Non international region names - '.print_r($regions,true));
        //Mail Send To RD
        foreach ($regions as $region) {
            log_message('INFO','In cron_job@generateMisReport | For region - '.print_r($region,true));
            $region_id = $region['id'];
            //9 is for no region
            if ($region_id == 9) continue;
            $region_name = $region['region_name'];

            // send mail to RD
            $profileIds = '11';
            $users = $this->user_model->getUserDetailForGivenRegionAndProfile($region_id, $profileIds, $userType);
            log_message('INFO','In cron_job@generateMisReport | User detail for region('.$region_name.') and profile id = 11 - '.print_r($users,true));

            $email_ids = array();
            foreach ($users as $val) {
                array_push($email_ids, $val['user_email']);
            }
            $to_list = implode(',', $email_ids);
            log_message('INFO','In cron_job@generateMisReport | To email list - '.print_r($to_list,true));
            $staticEmails = $this->getStaticMails('mis_weekly');
            log_message('INFO','In cron_job@generateMisReport | Static emails for weekly mis - '.print_r($staticEmails,true));
            $emailIds = $to_list . "," . $staticEmails;
            $regionMisWeekly = $this->getStaticMails('mis_weekly-' . $region_name);
            log_message('INFO','In cron_job@generateMisReport | Static emails for weekly mis region wise - '.print_r($regionMisWeekly,true));
            if ($regionMisWeekly != '') {
                $emailIds = $emailIds . "," . $regionMisWeekly;
            }
            log_message('INFO','In cron_job@generateMisReport | All email ids  - '.print_r($emailIds,true));

            $summaryForMonth = $this->mg_model->getSummaryForRegion($region_name, $givenDate, $userType, $is_international);
            log_message('INFO','In cron_job@generateMisReport | summary for month - '.print_r($summaryForMonth,true));
            $monthlyFct = $this->mg_model->getMonthlyMisReportData($region_name, $givenDate, $is_fct = 1, $userType, $is_international);
            log_message('INFO','In cron_job@generateMisReport | $monthlyFct - '.print_r($monthlyFct,true));
            $monthlyNonFct = $this->mg_model->getMonthlyMisReportData($region_name, $givenDate, $is_fct = 0, $userType, $is_international);
            log_message('INFO','In cron_job@generateMisReport | $monthlyNonFct - '.print_r($monthlyNonFct,true));
            $financialYearFct = $this->mg_model->getFinancialYearMisReport($region_name, $givenDate, $is_fct = 1, $userType, $is_international);
            log_message('INFO','In cron_job@generateMisReport | $financialYearFct - '.print_r($financialYearFct,true));
            $financialYearNonFct = $this->mg_model->getFinancialYearMisReport($region_name, $givenDate, $is_fct = 0, $userType, $is_international);
            log_message('INFO','In cron_job@generateMisReport | $financialYearNonFct - '.print_r($financialYearNonFct,true));
            $financialTotalValue = $this->mg_model->getTotalFinancialYearMisReportForRegion($region_name, $givenDate, $userType, $is_international);
            log_message('INFO','In cron_job@generateMisReport | $financialTotalValue - '.print_r($financialTotalValue,true));

            log_message('INFO','In cron_job@generateMisReport | SENDING MAIL RD');
            $this->mailSendForMis($givenDate, $emailIds, $summaryForMonth, $monthlyFct, $monthlyNonFct, $financialYearFct, $financialYearNonFct, $financialTotalValue);
        }
        //}

        //Mail Send to BH/COO
        log_message('INFO','In cron_job@generateMisReport | Sending mails for COO/BH ');
        $users = $this->mg_model->daily_email_report_list($userType);
        log_message('INFO','In cron_job@generateMisReport | COO/BH Email user list - '.print_r($users,true));
        $email_ids = array();
        foreach ($users as $val) {
            array_push($email_ids, $val['user_email']);
        }

        $to_list = implode(',', $email_ids);
        log_message('INFO','In cron_job@generateMisReport | To email list - '.print_r($to_list,true));
        $staticEmails = $this->getStaticMails('mis_daily');
        log_message('INFO','In cron_job@generateMisReport | Static emails for weekly mis - '.print_r($staticEmails,true));
        if ($userType == 0) {
            $emailIds = $to_list . "," . $staticEmails;
        } else {
            $emailIds = $to_list;
        }
        log_message('INFO','In cron_job@generateMisReport | All email ids  - '.print_r($emailIds,true));
        $summaryForMonth = $this->mg_model->getSummaryForRegion($region_name = 'ALL', $givenDate, $userType, $is_international);
        log_message('INFO','In cron_job@generateMisReport | $summaryForMonth - '.print_r($summaryForMonth,true));
        $monthlyFct = $this->mg_model->getMonthlyMisReportData($region_name = 'ALL', $givenDate, $is_fct = 1, $userType, $is_international);
        log_message('INFO','In cron_job@generateMisReport | $monthlyFct - '.print_r($monthlyFct,true));
        $monthlyNonFct = $this->mg_model->getMonthlyMisReportData($region_name = 'ALL', $givenDate, $is_fct = 0, $userType, $is_international);
        log_message('INFO','In cron_job@generateMisReport | $monthlyNonFct - '.print_r($monthlyNonFct,true));
        $financialYearFct = $this->mg_model->getFinancialYearMisReport($region_name = 'ALL', $givenDate, $is_fct = 1, $userType, $is_international);
        log_message('INFO','In cron_job@generateMisReport | $financialYearFct - '.print_r($financialYearFct,true));
        $financialYearNonFct = $this->mg_model->getFinancialYearMisReport($region_name = 'ALL', $givenDate, $is_fct = 0, $userType, $is_international);
        log_message('INFO','In cron_job@generateMisReport | $financialYearNonFct - '.print_r($financialYearNonFct,true));
        $financialTotalValue = $this->mg_model->getTotalFinancialYearMisReportForRegion($region_name = 'ALL', $givenDate, $userType, $is_international);
        log_message('INFO','In cron_job@generateMisReport | $financialTotalValue - '.print_r($financialTotalValue,true));
        log_message('INFO','In cron_job@generateMisReport | SENDING MAIL COO/BH');
        $this->mailSendForMis($givenDate, $emailIds, $summaryForMonth, $monthlyFct, $monthlyNonFct, $financialYearFct, $financialYearNonFct, $financialTotalValue);
        //$this->current_month_report($userType,$givenDate) ;

        //Send Mail For International Region
        log_message('INFO','In cron_job@generateMisReport | Mail sending for international users');
        if ($userType == 0) {
            $todaysDate = date("d", strtotime($givenDate));
            log_message('INFO','In cron_job@generateMisReport | User type = 0, todays date - '.print_r($todaysDate,true));
            //if(($todaysDate % 15) == 0){
            if (($todaysDate == 1) || ($todaysDate == 15) || ($todaysDate == 30)) {
                log_message('INFO','In cron_job@generateMisReport | Date is either 1 or 15 or 30');
                $staticEmails = $this->getStaticMails('mis_international');
                log_message('INFO','In cron_job@generateMisReport | Static user emails for international mails - '.print_r($staticEmails, true));
                $emailIds = $staticEmails;
                $is_international = 1;
                $summaryForMonth = $this->mg_model->getSummaryForRegion($region_name = 'ALL', $givenDate, $userType, $is_international);
                log_message('INFO','In cron_job@generateMisReport | $summaryForMonth - '.print_r($summaryForMonth,true));
                $monthlyFct = $this->mg_model->getMonthlyMisReportData($region_name = 'ALL', $givenDate, $is_fct = 1, $userType, $is_international);
                log_message('INFO','In cron_job@generateMisReport | $monthlyFct - '.print_r($monthlyFct,true));
                $monthlyNonFct = $this->mg_model->getMonthlyMisReportData($region_name = 'ALL', $givenDate, $is_fct = 0, $userType, $is_international);
                log_message('INFO','In cron_job@generateMisReport | $monthlyNonFct - '.print_r($monthlyNonFct,true));
                $financialYearFct = $this->mg_model->getFinancialYearMisReport($region_name = 'ALL', $givenDate, $is_fct = 1, $userType, $is_international = 0);
                log_message('INFO','In cron_job@generateMisReport | $financialYearFct - '.print_r($financialYearFct,true));
                $financialYearInternationalFct = $this->mg_model->getFinancialYearMisReport($region_name = 'ALL', $givenDate, $is_fct = 1, $userType, $is_international = 1);
                log_message('INFO','In cron_job@generateMisReport | $financialYearInternationalFct - '.print_r($financialYearInternationalFct,true));
                $financialYearNonFct = $this->mg_model->getFinancialYearMisReport($region_name = 'ALL', $givenDate, $is_fct = 0, $userType, $is_international = 0);
                log_message('INFO','In cron_job@generateMisReport | $financialYearNonFct - '.print_r($financialYearNonFct,true));
                $financialYearInternationalNonFct = $this->mg_model->getFinancialYearMisReport($region_name = 'ALL', $givenDate, $is_fct = 0, $userType, $is_international = 1);
                log_message('INFO','In cron_job@generateMisReport | $financialYearInternationalNonFct - '.print_r($financialYearInternationalNonFct,true));
                $financialTotalValue = $this->mg_model->getTotalFinancialYearMisReportForRegion($region_name = 'ALL', $givenDate, $userType, $is_international);
                log_message('INFO','In cron_job@generateMisReport | $financialTotalValue - '.print_r($financialTotalValue,true));

                log_message('INFO','In cron_job@generateMisReport | SENDING MAIL INTERNATIONAL');
                $this->mailSendForMisInternationally($givenDate, $emailIds, $summaryForMonth, $monthlyFct, $monthlyNonFct, $financialYearFct, $financialYearNonFct, $financialTotalValue, $financialYearInternationalFct, $financialYearInternationalNonFct);
            }
        }


    }

    public function getStaticMails($searchKey)
    {
        $typeArr = array($searchKey);
        $result = $this->mg_model->getStaticEmails($typeArr);
        if (count($result) > 0) {
            return $result[0]['static_emails'];
        } else {
            return '';
        }
    }

    public function mailSendForMis($givenDate, $emailIds, $summaryForMonth, $monthlyFct, $monthlyNonFct, $finacialYearFct, $financialYearNonFct, $financialTotalValue)
    {
        $current_month = date("M-y", strtotime($givenDate));
        $month = date("F", strtotime($givenDate));
        $current_month_year = date("M-Y", strtotime($givenDate));
        $mailPlaceHolderValues = array(
            'CURRENT_MONTH' => $current_month,
            'MONTH' => $month,
            'CURRENT_MONTH_YEAR' => $current_month_year,
            'SUMMARY_MONTH' => $summaryForMonth,
            'MONTHLY_DATA' => $monthlyFct,
            'MONTHLY_NON_FCT_DATA' => $monthlyNonFct,
            'TODAYS_DATE' => date("d-M-Y"),
            'FINANCIAL_YEAR_DATA' => $finacialYearFct,
            'FINANCIAL_YEAR_NON_FCT_DATA' => $financialYearNonFct,
            'FINANCIAL_YEAR_TOTAL_DATA' => $financialTotalValue
        );
        log_message('INFO', 'In cron_job@mailSendForMis | calling the email service with following parameters - '.print_r($mailPlaceHolderValues, true));
        $emailServiceObj = new EmailService($emailIds);
        $status = $emailServiceObj->sendMailOverApi('mis_month_report.html',$mailPlaceHolderValues);
        log_message('INFO', 'In cron_job@mailSendForMis | printing the response of email service '.print_r($status, true));

    }

    public function mailSendForMisInternationally($givenDate, $emailIds, $summaryForMonth, $monthlyFct, $monthlyNonFct, $finacialYearFct, $financialYearNonFct, $financialTotalValue, $financialYearInternationalFct, $financialYearInternationalNonFct)
    {
        $current_month = date("M-y", strtotime($givenDate));
        $month = date("F", strtotime($givenDate));
        $current_month_year = date("M-Y", strtotime($givenDate));

        $mailPlaceHolderValues = array(
                'CURRENT_MONTH' => $current_month,
                'MONTH' => $month,
                'CURRENT_MONTH_YEAR' => $current_month_year,
                'SUMMARY_MONTH' => $summaryForMonth,
                'MONTHLY_DATA' => $monthlyFct,
                'MONTHLY_NON_FCT_DATA' => $monthlyNonFct,
                'TODAYS_DATE' => date("d-M-Y"),
                'FINANCIAL_YEAR_DATA' => $finacialYearFct,
                'FINANCIAL_YEAR_NON_FCT_DATA' => $financialYearNonFct,
                'FINANCIAL_YEAR_TOTAL_DATA' => $financialTotalValue,
                'FINANCIAL_YEAR_DATA_INTERNATIONAL' => $financialYearInternationalFct,
                'FINANCIAL_YEAR_NON_FCT_DATA_INTERNATIONAL' => $financialYearInternationalNonFct
            );
        log_message('INFO', 'In cron_job@mailSendForMisInternationally | calling the email service with following parameters - '.print_r($mailPlaceHolderValues, true));
        $emailServiceObj = new EmailService($emailIds);
        $status = $emailServiceObj->sendMailOverApi('mis_month_report_international.html',$mailPlaceHolderValues);
        log_message('INFO', 'In cron_job@mailSendForMisInternationally | printing the response of email service '.print_r($status, true));
    }

    public function current_month_report_advance($givenDate = NULL)
    {
        // send mail to coo/bh
        $users = $this->mg_model->daily_email_report_list_advance_ro();
        $monthly = $this->mg_model->daily_email_report_details_advance_ro($givenDate);
        $current_year = $this->mg_model->current_financial_email_report_details_advance_ro($givenDate);

        $current_month = date("M-y");

        $email_ids = array();
        foreach ($users as $val) {
            array_push($email_ids, $val['user_email']);
        }
        $to_list = '';
        $to_list = implode(',', $email_ids);
        //$to_list = $to_list.",mani@surewaves.com,deepak@surewaves.com" ;
        $to_list = "biswabijayee@surewaves.com,saurabh@surewaves.com";
        mail_send_v1($to_list,
            "mis_month_report_adv",
            array('MONTH' => $current_month),
            array(
                'MONTH' => date("F"),
                'CURRENT_MONTH_YEAR' => date("M-Y"),
                'MONTHLY_DATA' => $monthly,
                'TODAYS_DATE' => date("d-M-Y"),
                'FINANCIAL_YEAR_DATA' => $current_year
            ),
            '',
            '',
            '',
            ''
        );
    }

    public function current_financial_report()
    {
        // send mail to coo/bh
        $users = $this->mg_model->daily_email_report_list();
        $data = $this->mg_model->current_financial_email_report_details();

        $current_month = date("Y");
        foreach ($users as $key => $user) {
            mail_send_v1($user['user_email'],
                "mis_month_report",
                array('MONTH' => $current_month),
                array(
                    'DATA' => $data
                ),
                '',
                '',
                '',
                ''
            );
        }
    }

    public function make_uniform_structure_for_edit_nw($channel_avg_rate, $channel_amount, $channel_total_seconds = null)
    {
        //echo "in method <pre>"; print_r($channel_avg_rate) ; print_r($channel_amount); print_r($channel_total_seconds) ;
        $data = array();
        foreach ($channel_avg_rate as $channel_id => $value) {
            if (!array_key_exists($channel_id, $data)) {
                $data[$channel_id] = array();
            }
            foreach ($value as $region_id => $avg_rate) {
                $region_id_val = $this->get_region_id($region_id);
                if (!array_key_exists($region_id_val, $data[$channel_id])) {
                    $data[$channel_id][$region_id_val] = array();
                }
                $data[$channel_id][$region_id_val]['channel_avg_rate'] = $avg_rate;
            }
        }

        foreach ($channel_amount as $channel_id => $value) {
            if (!array_key_exists($channel_id, $data)) {
                $data[$channel_id] = array();
            }
            foreach ($value as $region_id => $amount) {
                $region_id_val = $this->get_region_id($region_id);
                if (!array_key_exists($region_id_val, $data[$channel_id])) {
                    $data[$channel_id][$region_id_val] = array();
                }
                $data[$channel_id][$region_id_val]['channel_amount'] = $amount;
            }
        }

        foreach ($channel_total_seconds as $channel_id => $value) {
            if (!array_key_exists($channel_id, $data)) {
                $data[$channel_id] = array();
            }
            foreach ($value as $region_id => $seconds) {
                $region_id_val = $this->get_region_id($region_id);
                if (!array_key_exists($region_id_val, $data[$channel_id])) {
                    $data[$channel_id][$region_id_val] = array();
                }
                $data[$channel_id][$region_id_val]['channel_seconds'] = $seconds;
            }
        }
        //echo 'return <pre>';print_r($data);exit;
        return $data;
    }

    public function get_region_id($region_id)
    {
        $region_id_val = 1;
        if ($region_id == 0) {
            $region_id_val = 1;
        } else if ($region_id == 1) {
            $region_id_val = 3;
        }
        return $region_id_val;
    }

    public function initiateInvoiceCancelProcess($customer_id, $internal_ro)
    {
        $marketStr = '';
        $networkFinalInfo = array();
        $networkInfo = $this->mg_model->getAllNetworkInfo($customer_id, $internal_ro);
        log_message('info', 'ro_cancel_invoice networkInfo array-' . print_r($networkInfo, TRUE));
        if (count($networkInfo) > 0) {
            $networkFinalInfo = $networkInfo[0];
            $roNetworkMarkets = $this->mg_model->getScheduledMarketForChannel(array($networkFinalInfo['channel_names']), $internal_ro);
            log_message('info', 'ro_cancel_invoice network related channels-' . print_r($roNetworkMarkets, TRUE));
            //echo "<pre>";print_r($networkFinalInfo);exit;
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
        return $networkFinalInfo;
    }

    public function is_channel_available($channel_id, $post_cancel_channel)
    {
        if (in_array($channel_id, $post_cancel_channel)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function maintain_historical_data($ro_approved_nw_data, $action, $ro_ext_id, $user_id)
    {
        foreach ($ro_approved_nw_data as $val) {
            $history_data = array(
                'am_external_ro_id' => $ro_ext_id,
                'revision_no' => $val['revision_no'],
                'customer_id' => $val['customer_id'],
                'customer_share' => $val['customer_share'],
                'channel_id' => $val['tv_channel_id'],
                'channel_spot_avg_rate' => $val['channel_spot_avg_rate'],
                'channel_spot_amount' => $val['channel_spot_amount'],
                'channel_banner_avg_rate' => $val['channel_banner_avg_rate'],
                'channel_banner_amount' => $val['channel_banner_amount'],
                'action' => $action,
                'user_id' => $user_id
            );
            $this->mg_model->maintain_historical_data($history_data);
        }
    }

    public function cancel_nw_channel($cancel_nw_channel_ids, $order_id, $am_ro_id, $cid, $user_id, $revision_no_change, $revision_no_val, $pdf_generation_status, $networkFinalInfo)
    {
        $internal_ro_number = base64_decode($order_id);
        $update_field = FALSE;

        foreach ($cancel_nw_channel_ids as $channel_id) {
            $ro_detail = $this->am_model->ro_detail_for_internal_ro($internal_ro_number);

            //Get Data from ro_approved_network
            $approved_nw_data = array(
                'tv_channel_id' => $channel_id,
                'internal_ro_number' => $internal_ro_number,
                'customer_id' => $cid
            );
            $ro_approved_nw_data = $this->mg_model->get_approved_data_for_customer_channel_ro($approved_nw_data);

//            $date_of_cancel = date('Y-m-d');
            $approved = FALSE;
            if (count($ro_approved_nw_data) > 0) {
                $approved = TRUE;
                $date_of_cancel = date("Y-m-d", strtotime("+2 day"));
            } else {
//                $date_of_cancel = date('Y-m-d');
                $this->cancel_nw_channel_before_approval($channel_id, $order_id);
                continue;
            }

            //$revision_no = $ro_approved_nw_data[0]['revision_no'] ;
            //$update_revision_no = $revision_no + 1 ;
            // get_campaigns_for_internal_ro_number
            $campaigns = $this->mg_model->get_campaigns_for_internal_ro_and_channel($internal_ro_number, $channel_id);
            $campaign_id = array();
            foreach ($campaigns as $campaigns_val) {
                array_push($campaign_id, $campaigns_val['campaign_id']);
            }
            $campaign_ids = implode(",", $campaign_id);
            $this->am_model->update_advertiser_screens_dates($campaign_ids, $date_of_cancel);

            //Insert into ro_cancel_channel
            $user_data = array(
                'channel_id' => $channel_id,
                'internal_ro_number' => $internal_ro_number,
                'marker_for_cancellation' => 0,
                'user_id' => $user_id,
                'cancel_requested_time' => date('Y-m-d H:i:s', strtotime("+2 days"))
            );
            $this->mg_model->insert_into_cancel_channel($user_data); //ro_cancel_channel

            if ($approved) {
                //insert data into history
                $this->maintain_historical_data($ro_approved_nw_data, 'cancel_channel', $ro_detail[0]['id'], $user_id);

                //get number of impression for channel(remaining)
                $channel_impression = $this->mg_model->get_channel_impression_v1($campaign_ids, $channel_id);
                //echo '<pre>';print_r($channel_impression);exit;

                if (($channel_impression['total_spot_ad_seconds'] == 0) && ($channel_impression['total_banner_ad_seconds'] == 0)) {
                    //delete from approved n/w
                    $update_field = TRUE;
                    //$this->mg_model->delete_approved_data_for_customer_channel_ro($approved_nw_data) ;
                    $update_approved_nw_data = array(
                        'total_spot_ad_seconds' => $channel_impression['total_spot_ad_seconds'],
                        'channel_spot_amount' => ($channel_impression['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                        'total_banner_ad_seconds' => $channel_impression['total_banner_ad_seconds'],
                        'channel_banner_amount' => ($channel_impression['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                    );
                    $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $approved_nw_data);
                } else {
                    $update_field = TRUE;
                    $update_approved_nw_data = array(
                        'total_spot_ad_seconds' => $channel_impression['total_spot_ad_seconds'],
                        'channel_spot_amount' => ($channel_impression['total_spot_ad_seconds'] * $ro_approved_nw_data[0]['channel_spot_avg_rate']) / 10,
                        'total_banner_ad_seconds' => $channel_impression['total_banner_ad_seconds'],
                        'channel_banner_amount' => ($channel_impression['total_banner_ad_seconds'] * $ro_approved_nw_data[0]['channel_banner_avg_rate']) / 10
                    );
                    $this->mg_model->update_approved_data_where_ro_customer($update_approved_nw_data, $approved_nw_data);
                }
            }
        }

        if ($update_field && $revision_no_change) {
            //update revision number,pdf generation status in ro_approved_network
            if ($pdf_generation_status == 0) {
                $update_revision_no = $revision_no_val;
            } else {
                $update_revision_no = $revision_no_val + 1;
            }
            //$update_revision_no =  $revision_no_val + 1 ;
            //Whether to Update The field for Ro and customer
            $endDateCrossed = $this->mg_model->checkEndDateCrossedForROCid($internal_ro_number, $cid);
            if (!$endDateCrossed) {
                $revision_pdf = array(
                    'revision_no' => $update_revision_no,
                    'pdf_generation_status' => 0,
                    'pdf_processing' => 0
                );
                $where_data = array(
                    'internal_ro_number' => $internal_ro_number,
                    'customer_id' => $cid
                );
                $this->mg_model->update_approved_data_for_cancel_channel($revision_pdf, $where_data);
                $this->updatingInvoiceCancelProcess($networkFinalInfo);
            }

        }
        //update external ro report detail
        $this->update_into_external_ro_report_detail($internal_ro_number);

    }

    public function cancel_nw_channel_before_approval($channel_id, $order_id)
    {
        $internal_ro_number = base64_decode($order_id);
        $this->ro_model->cancel_channel_before_approval(array('channel_id' => $channel_id, 'internal_ro_number' => $internal_ro_number));
    }

    public function updatingInvoiceCancelProcess($networkFinalInfo)
    {
        if (count($networkFinalInfo) > 0) {
            $checkForPresenceOfInvoicedata = $this->mg_model->checkForPresenceOfInvoicedata($networkFinalInfo['network_ro_number']);
            $networkFinalInfo['pdf_processing'] = 0;
            log_message('info', 'ro_cancel_invoice networkInfo array-' . print_r($networkFinalInfo, TRUE));
            if (count($checkForPresenceOfInvoicedata) > 0) {
                $this->mg_model->updateInvoiceCancelData($networkFinalInfo);
            } else {
                $this->mg_model->insertInvoiceCancelData($networkFinalInfo);
            }
        }

    }

    /* Helper function */

    public function update_into_external_ro_report_detail($internal_ro_number)
    {
        $ro_campaign_amount = $this->mg_model->get_external_ro_report_details($internal_ro_number);

        $gross_ro_amount = $ro_campaign_amount[0]['gross_ro_amount'];
        $agency_commission_amount = $ro_campaign_amount[0]['agency_commission_amount'];
        if ($ro_campaign_amount[0]['agency_rebate_on'] == "ro_amount") {
            $agency_rebate = $ro_campaign_amount[0]['gross_ro_amount'] * ($ro_campaign_amount[0]['agency_rebate'] / 100);
        } else {
            $agency_rebate = ($ro_campaign_amount[0]['gross_ro_amount'] - $ro_campaign_amount[0]['agency_commission_amount']) * ($ro_campaign_amount[0]['agency_rebate'] / 100);
        }
        $other_expenses = $ro_campaign_amount[0]['other_expenses'];

        $total_network_payout = $this->mg_model->get_total_network_payout($internal_ro_number);
        $actual_net_amount = $gross_ro_amount - $agency_commission_amount - $agency_rebate - $other_expenses;
        $net_contribution_amount = $actual_net_amount - $total_network_payout[0]['network_payout'];

        $net_revenue = $gross_ro_amount - $agency_commission_amount;
        $net_revenue = round(($net_revenue * SERVICE_TAX), 2);
        $net_contribution_amount_per = round(($net_contribution_amount / $net_revenue) * 100, 2);

        $total_scheduled_seconds = $this->mg_model->get_total_network_seconds_internal_ro($internal_ro_number);

        $report_data = array(
            'gross_ro_amount' => $gross_ro_amount,
            'agency_commission_amount' => $agency_commission_amount,
            'other_expenses' => $other_expenses,
            'agency_rebate' => $agency_rebate,
            'total_seconds_scheduled' => $total_scheduled_seconds[0]['total_scheduled_seconds'],
            'total_network_payout' => $total_network_payout[0]['network_payout'],
            'net_contribution_amount' => $net_contribution_amount,
            'net_contribution_amount_per' => $net_contribution_amount_per,
            'net_revenue' => $net_revenue
        );
        $where_data = array(
            'internal_ro_number' => $internal_ro_number
        );
        $this->mg_model->update_external_ro_report_detail($report_data, $where_data);
    }

    /***************----- Date 2014- Nov 26 PDF Generation ------*****************/

    public function generateRoPDF()
    {

        $setProcessing = array('pdf_processing' => 1);
        $setGeneration = array('pdf_processing' => 0, 'pdf_generation_status' => 1);

        $roDataArray = $this->mg_model->getRoCustomersChannels();

        foreach ($roDataArray as $ro => $customerIds) {

            $customerIdKeys = array_keys($customerIds);

            $customerIdString = implode(",", $customerIdKeys);

            $this->mg_model->updatePDFProcessingStatus($ro, $customerIdString, $setProcessing);

            foreach ($customerIds as $customerId => $channelIdArray) {

                $this->buildRoPDFReport($channelIdArray, $customerId, $ro);

            }

            $this->mg_model->updatePDFProcessingStatus($ro, $customerIdString, $setGeneration);
        }


    }

    public function buildRoPDFReport($channelIdArray, $customerId, $internalRoNumber)
    {

        //Network Wise PDF Generation
        $reportArray = array();
        $dateFinderArray = array();

        foreach ($channelIdArray as $channelId) {

            $dataArray = $this->mg_model->getChannelIdReportData($channelId, $internalRoNumber);

            foreach ($dataArray->result() as $row) {

                $monthDate = date('F,Y', strtotime($row->scheduled_date));
                $scheduleDate = date('Y-m-d', strtotime($row->scheduled_date));

                $reportArray[$monthDate][$row->caption_name][$row->channel_name][$scheduleDate][] = array(

                    "schedule_date" => $row->scheduled_date,
                    "channel_name" => $row->channel_name,
                    "screen_region" => $row->screen_region,
                    "impressions" => $row->final_impressions,
                    "start_time" => $row->start_time,
                    "end_time" => $row->end_time,
                    "duration" => $row->duration,
                    "language" => $row->language,
                    "brand" => $row->brand,
                );

                if ($row->final_impressions > 0) {

                    $dateFinderArray["scheduled"][] = $row->scheduled_date;

                } else if ($row->final_impressions == 0 || $row->final_impressions == "-") {

                    $dateFinderArray["cancelled"][] = $row->scheduled_date;

                }

            }

        }

        $headerResultObject = $this->getPDFHeaderData($customerId, $internalRoNumber);
        $amountObject = $this->mg_model->getAmountForInternalRoAndCustomerId($customerId, $internalRoNumber);

        $minMaxTemplateTypeDataArray = $this->getMaxMinDateAndFindWhichTemplateToGenerate($dateFinderArray, $headerResultObject->row()->revision_no);
        $networkRoNumber = $this->get_nw_seq_no($internalRoNumber, $headerResultObject->row()->customer_name);


        $mergedArray = $this->getMergedArray($headerResultObject->result_array(), $amountObject->result_array(),
            $minMaxTemplateTypeDataArray, $networkRoNumber, $internalRoNumber, $customerId);


        $data["filteredArray"] = $this->filterRoPDFReportArray($reportArray);
        $data["headerArray"] = $mergedArray;

        /*Inserting into ro_network_ro_report_details */
        $this->updateRoNetworkTable($mergedArray);

        $this->load->view('ro_manager/NetworkRoPdf', $data);

    }

    function getPDFHeaderData($customerId, $internalRoNumber)
    {

        $resultObject = $this->mg_model->getPDFHeaderDataFromDB($internalRoNumber, $customerId);

        return $resultObject;
    }

    function getMaxMinDateAndFindWhichTemplateToGenerate($dateFinderArray, $revisionNumber)
    {

        if (array_key_exists("scheduled", $dateFinderArray)) {

            $templateType = $this->getTemplateType($revisionNumber);
            $returnArray = array(

                "templateType" => $templateType,
                "minDate" => min($dateFinderArray["scheduled"]),
                "maxDate" => max($dateFinderArray["scheduled"])
            );


        } else {

            $returnArray = array(

                "templateType" => "Cancel",
                "minDate" => min($dateFinderArray["scheduled"]),
                "maxDate" => max($dateFinderArray["scheduled"])
            );

        }

        return $returnArray;
    }

    public function getTemplateType($revisionNumber)
    {

        $type = "";

        if ($revisionNumber == 0) {

            $type = "";

        } else if ($revisionNumber > 0) {

            $type = "Revision";

        }
        return $type;
    }

    public function getMergedArray($headerArray, $amountArray, $MinMaxDateTemplateTypeArray, $networkRoNumber, $internalRoNumber, $customerId)
    {

        $PDFDataArray = array();
        $revisedNumber = "";
        $reviseKey = "";
        $subject = "";

        if ($headerArray[0]["revision_no"] > 0) {

            $revisionNumber = $headerArray[0]["revision_no"] - 1;
            $revisedNumber = "-R" . $revisionNumber;
            $reviseKey = "Revised NRO Number";

        } else {

            $reviseKey = "Network Ro Number";
        }

        if (!empty($MinMaxDateTemplateTypeArray["templateType"])) {

            $subject = " - " . $MinMaxDateTemplateTypeArray["templateType"];

        }

        $networkName = $headerArray[0]["customer_name"];
        $billingName = $headerArray[0]["billing_name"];
        $market = $headerArray[0]["sw_market_name"];
        $channels = $headerArray[0]["channels"];
        $agency = $headerArray[0]["client"];

        $campaignStartDate = $MinMaxDateTemplateTypeArray["minDate"];
        $campaignEndDate = $MinMaxDateTemplateTypeArray["maxDate"];
        $customerShare = $amountArray[0]["customer_share"];

        $tempArray = array(
            "revisionHeading" => $reviseKey,
            "revisionValue" => $networkRoNumber . $revisedNumber,
            "Network_Name" => $networkName,
            "Billing_Name" => $billingName,

            "Market" => $market,
            "Channels" => $channels,
            "Advertiser_Name" => $agency,

            "Campaign_start_Period" => $campaignStartDate,
            "Campaign_end_Period" => $campaignEndDate,
            "customerShare" => $customerShare,
            "revisionNumber" => $headerArray[0]["revision_no"],
            "Release_Date" => date('Y-m-d H:i:s'),
            "Investment" => round($amountArray[0]["investement"], 2) . "(Plus GST)",
            "Net_Amount_Payable" => "INR " . round($amountArray[0]["network_payout"], 2) . "(Plus GST)"

        );

        $clientAndCustomerRoObject = $this->mg_model->getCustomerRoClient($internalRoNumber);
        $content_download_link = $this->ro_model->get_download_link($internalRoNumber, $customerId);
        $cancelRo = $this->mg_model->check_for_ro_cancellation($internalRoNumber);


        $show_link = 0;

        if (count($content_download_link) > 0) {

            $show_link = 1;

        } else {

            $show_link = 0;

        }

        $PDFDataArray["subject"] = $subject;
        $PDFDataArray["heading"] = $tempArray;
        $PDFDataArray["customerRoAndClient"] = array("customerRo" => $clientAndCustomerRoObject->row()->cust_ro,
            "clientName" => $clientAndCustomerRoObject->row()->client,
            "networkRoNumber" => $networkRoNumber,
            "internalRoNumber" => $internalRoNumber,
            "monthActivity" => $this->findMonthActivity($campaignStartDate, $campaignEndDate)
        );

        $PDFDataArray["showLink"] = $show_link;
        $PDFDataArray["contentDownloadLink"] = $content_download_link;
        $PDFDataArray['ccEmailList'] = get_nw_ro_email($internalRoNumber);
        $PDFDataArray['networkDetails'] = $this->mg_model->get_network_details($customerId);
        $PDFDataArray['networkRoWithoutRevision'] = $networkRoNumber;
        $PDFDataArray['templateType'] = $MinMaxDateTemplateTypeArray["templateType"];
        $PDFDataArray['cancelRo'] = $cancelRo;

        return $PDFDataArray;

    }

    function findMonthActivity($startDate, $endDate)
    {

        $start = new DateTime($startDate);
        $start->modify('first day of this month');

        $end = new DateTime($endDate);
        $end->modify('first day of next month');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        $month = '';
        foreach ($period as $dt) {

            if (!isset($month) || empty($month)) {

                $month = $dt->format("M");

            } else {

                $month .= "," . $dt->format("M");

            }

        }
        return $month;
    }



    /**---------------------------- Functions for mail progression body */

    /*----- Mail Ro progression ** Start *************/
    function filterRoPDFReportArray($reportArray)
    {

        $filteredArray = array();

        foreach ($reportArray as $monthKey => $monthValue) {

            foreach ($monthValue as $captionKey => $captionValue) {

                foreach ($captionValue as $channelKey => $channelValue) {

                    foreach ($channelValue as $dateKey => $dateValue) {

                        $dateKey = date("d", strtotime($dateKey));

                        foreach ($dateValue as $timeKey => $timeValue) {

                            $currentProgramTime = strtotime($timeValue["start_time"]);

                            $six = strtotime('06:00:00');
                            $ten = strtotime('10:00:00');
                            $thirteen = strtotime('13:00:00');
                            $eighteen = strtotime('18:00:00');
                            $twentyOne = strtotime('20:00:00');
                            $twentyThree = strtotime('24:00:00');

                            if (($currentProgramTime >= $six) && ($currentProgramTime <= $ten)) {

                                $timeIndex = $this->getFormattedTime($six, $ten);

                                $filteredArray[$monthKey][$captionKey]['caption_duration'] = $timeValue["duration"];
                                $filteredArray[$monthKey][$captionKey]['language'] = $timeValue["language"];
                                $filteredArray[$monthKey][$captionKey]['brand'] = $timeValue["brand"];
                                $filteredArray[$monthKey][$captionKey]['screen_region'] = $timeValue["screen_region"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex][$dateKey] += $timeValue["impressions"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex]['totalSpots'] += $timeValue["impressions"];

                            } else {

                                $timeIndex = $this->getFormattedTime($six, $ten);
                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex] = '';
                            }
                            if (($currentProgramTime >= $ten + 1) && ($currentProgramTime <= $thirteen)) {

                                $timeIndex = $this->getFormattedTime($ten + 1, $thirteen);

                                $filteredArray[$monthKey][$captionKey]['caption_duration'] = $timeValue["duration"];
                                $filteredArray[$monthKey][$captionKey]['language'] = $timeValue["language"];
                                $filteredArray[$monthKey][$captionKey]['brand'] = $timeValue["brand"];
                                $filteredArray[$monthKey][$captionKey]['screen_region'] = $timeValue["screen_region"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex][$dateKey] += $timeValue["impressions"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex]['totalSpots'] += $timeValue["impressions"];
                            } else {

                                $timeIndex = $this->getFormattedTime($ten + 1, $thirteen);
                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex] = '';

                            }
                            if (($currentProgramTime >= $thirteen + 1) && ($currentProgramTime <= $eighteen)) {

                                $timeIndex = $this->getFormattedTime($thirteen + 1, $eighteen);

                                $filteredArray[$monthKey][$captionKey]['caption_duration'] = $timeValue["duration"];
                                $filteredArray[$monthKey][$captionKey]['language'] = $timeValue["language"];
                                $filteredArray[$monthKey][$captionKey]['brand'] = $timeValue["brand"];
                                $filteredArray[$monthKey][$captionKey]['screen_region'] = $timeValue["screen_region"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex][$dateKey] += $timeValue["impressions"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex]['totalSpots'] += $timeValue["impressions"];

                            } else {

                                $timeIndex = $this->getFormattedTime($thirteen + 1, $eighteen);
                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex] = '';

                            }
                            if (($currentProgramTime >= $eighteen + 1) && ($currentProgramTime <= $twentyOne)) {

                                $timeIndex = $this->getFormattedTime($eighteen + 1, $twentyOne);

                                $filteredArray[$monthKey][$captionKey]['caption_duration'] = $timeValue["duration"];
                                $filteredArray[$monthKey][$captionKey]['language'] = $timeValue["language"];
                                $filteredArray[$monthKey][$captionKey]['brand'] = $timeValue["brand"];
                                $filteredArray[$monthKey][$captionKey]['screen_region'] = $timeValue["screen_region"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex][$dateKey] += $timeValue["impressions"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex]['totalSpots'] += $timeValue["impressions"];

                            } else {

                                $timeIndex = $this->getFormattedTime($eighteen + 1, $twentyOne);
                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex] = '';

                            }
                            if (($currentProgramTime >= $twentyOne + 1) && ($currentProgramTime <= $twentyThree)) {

                                $timeIndex = $this->getFormattedTime($twentyOne + 1, $twentyThree);

                                $filteredArray[$monthKey][$captionKey]['caption_duration'] = $timeValue["duration"];
                                $filteredArray[$monthKey][$captionKey]['language'] = $timeValue["language"];
                                $filteredArray[$monthKey][$captionKey]['brand'] = $timeValue["brand"];
                                $filteredArray[$monthKey][$captionKey]['screen_region'] = $timeValue["screen_region"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex][$dateKey] += $timeValue["impressions"];

                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex]['totalSpots'] += $timeValue["impressions"];

                            } else {

                                $timeIndex = $this->getFormattedTime($twentyOne + 1, $twentyThree);
                                $filteredArray[$monthKey][$captionKey][$channelKey]
                                [$timeIndex] = '';

                            }

                        }

                    }
                }

            }

        }

        return $filteredArray;
    }

    function getFormattedTime($startTime, $endTime)
    {

        $startTime = date("H:s", $startTime);
        $endTime = date("H:s", $endTime);

        return $startTime . '-' . $endTime;

    }

    function updateRoNetworkTable($mergedArray)
    {

        /* Check row already exist */

        $response = $this->mg_model->checkRoNetworkRowExist($mergedArray);

        if ($response) {

            $this->mg_model->updateRoNetworkRoReportDetails($mergedArray);

        } else {

            $this->mg_model->insertRoNetworkRoReportDetails($mergedArray);

        }

    }


    public function progression_emails()
    {
        $this->load->library('Cron_Lock_Manager');
        $isLock = $this->lock('progression_emails');
        if (!$isLock) {
            exit;
        }
        $typeArray = array(SUBMIT, APPROVED, CAMPAIGN_START,
            CAMPAIGN_PRE_CLOSURE, CAMPAIGN_END,
            RO_CANCEL, MARKET_CANCEL, EDIT_RO);

        foreach ($typeArray as $type) {

            $emailRoDetails = $this->mg_model->getProgressionEmailRoDetails($type);

            if (count($emailRoDetails) != 0) {

                foreach ($emailRoDetails as $roData) {
                    if ($roData['ro_id'] != NULL) {
                        $this->routeMailTypeToSend($type, $roData);
                    }
                }

            }

        }
        $this->cron_lock_manager->unlock('progression_emails');

    }

    private function routeMailTypeToSend($type, $dataArray)
    {

        $this->config->load('email_text');

        $currentDate = date('Y-m-d');
        $logMessageArray = array();

        $roId = $dataArray['ro_id'];
        $userId = $dataArray['user_id'];
        $ccMailListArray = $this->mg_model->getCCMailIdList($roId);

        $toEmailList = $dataArray['order_history_mail_list'];
        $ccEmailList = $ccMailListArray[0]["ccMailId"];
        $campaignStartDate = $dataArray['camp_start_date'];
        $campaignEndDate = $dataArray['camp_end_date'];
        $userName = $dataArray['user_name'];
        $userPhone = $dataArray['user_phone'];
        $userProfileImage = $dataArray['profile_image'];
        $clientName = $dataArray['client'];
        $brandNames = $this->mg_model->getBrandName($dataArray['brandIds']);
        $brandName = $brandNames[0]['brand'];

        if (empty($userProfileImage)) {

            $userProfileImage = base_url('images/ro_progress/final_revised/phone.png');

        }

        $subjectRoNumber = str_replace(" ", "_", $dataArray['cust_ro']);

        array_push($logMessageArray, $roId, $campaignStartDate, $campaignEndDate, $userName, $userPhone, $subjectRoNumber);

        $RoNumber = "<span style='font-weight:bold;font-size: 89.5%;'>" . $subjectRoNumber . "</span>";
        $AcMangerName = "<span style='font-weight:bold;font-size: 89%;'>" . $userName . "</span>";
        $AcMangerNumber = "<span style='font-weight:bold;font-size: 89%;'>" . $userPhone . "</span>";
        $RoStartDate = "<span style='font-weight:bold;font-size: 89%;'>" . date('l, M d', strtotime($campaignStartDate)) . "</span>";
        $RoEndDate = "<span style='font-weight:bold;font-size: 89%;'>" . date('l, M d, Y', strtotime($campaignEndDate)) . "</span>";
        $campaignExpressLink = '<a style="text-decoration: none !important;" href=\'http://campaignxpress.surewaves.com\'><span style="color: turquoise;">SureWaves CampaignXpress</span></a>';

        if ($type == SUBMIT) {

            $marketDataArray = $this->mg_model->getBookedRoMarketData($roId);

            array_push($logMessageArray, array("markets" => $marketDataArray));

            $marketDataTable = $this->buildMarketDataTableForSubmitMail($marketDataArray);

            $emailTextKey = "ro_progress_order_booked";
            $subject = array("BOOKED" => "Order booked - $subjectRoNumber ");

            $greetingsHeading = "Thank you for your order!";
            $greetingsHeadingNextLine = "This email confirms that your RO number " . $RoNumber . " is now booked.";
            $bodyHeader = "Please find below, the summary of your order:";
            $nextStepText = "We'll send you an email once your order is scheduled.";
            $queryText = "Please get in touch, for any support.";
            $contact = $AcMangerName . ' - ( ' . $AcMangerNumber . " )";

            $message = array(
                "HEAD_TEAXTURE" => base_url('images/ro_progress/final_revised/head-texture.png'),
                "LOGO" => base_url('images/ro_progress/final_revised/surewaves-logo.png'),
                "BLUE" => base_url('images/ro_progress/final_revised/blue-1.jpg'),
                "MAIL" => base_url('images/ro_progress/final_revised/mail.png'),
                "PHONE" => $userProfileImage,

                "GREETINGS_HEADING" => $greetingsHeading,
                "GREETINGS_HEADING_NEXT_LINE" => $greetingsHeadingNextLine,
                "BODY_HEADER" => $bodyHeader,
                "DATA_TABLE" => $marketDataTable,
                "NEXT_STEP_TEXT" => $nextStepText,
                "QUERY_TEXT" => $queryText,
                "CONTACT" => $contact,
                "CLIENT_NAME" => $clientName,
                "BRAND_NAME" => $brandName
            );

            $this->sendRoProgressMail($toEmailList, $ccEmailList, $emailTextKey, $message, $subject);
            $this->mg_model->insertRoProgressionMailLog($roId, "Order booked", "Order booked - $subjectRoNumber ", $toEmailList, "", json_encode($logMessageArray));

//            $setData = " SET submit_status = 'mail_sent' ";
            $setData = array('submit_status' => 'mail_sent');
            $this->mg_model->updateProgressionEmaiLStatus($setData, $roId);

        } else if ($type == APPROVED) {

            $marketDataArray = $this->mg_model->getScheduledRoMarketData($roId);
            array_push($logMessageArray, array("markets" => $marketDataArray));

            $marketDataTable = $this->buildMarketBulletsForProgressionMail($marketDataArray);

            $emailTextKey = "ro_progress_order_scheduled";
            $subject = array("SCHEDULED" => "Campaign is now scheduled - $subjectRoNumber");

            $greetingsHeading = "Your campaign is scheduled!";
            $bodyHeader = "We would like to inform you that your order " . $RoNumber . " has been successfully scheduled to play from " . $RoStartDate . " to " . $RoEndDate . " in the following markets:";
            $nextStepText = " We will inform you when your campaign starts to play.";
            $queryText = "Please get in touch, for any support.";
            $contact = $AcMangerName . ' - ( ' . $AcMangerNumber . " )";

            $message = array("HEAD_TEAXTURE" => base_url('images/ro_progress/final_revised/head-texture.png'),
                "LOGO" => base_url('images/ro_progress/final_revised/surewaves-logo.png'),
                "BLUE" => base_url('images/ro_progress/final_revised/blue-1.jpg'),
                "MAIL" => base_url('images/ro_progress/final_revised/mail.png'),
                "PHONE" => $userProfileImage,

                "GREETINGS_HEADING" => $greetingsHeading,
                "GREETINGS_HEADING_NEXT_LINE" => $bodyHeader,
                "DATA_TABLE" => $marketDataTable,
                "NEXT_STEP_TEXT" => $nextStepText,
                "QUERY_TEXT" => $queryText,
                "CLIENT_NAME" => $clientName,
                "BRAND_NAME" => $brandName,
                "CONTACT" => $contact
            );

            $this->sendRoProgressMail($toEmailList, $ccEmailList, $emailTextKey, $message, $subject);
            $this->mg_model->insertRoProgressionMailLog($roId, "Order Scheduled", "Order Scheduled - $subjectRoNumber ", $toEmailList, $ccEmailList, json_encode($logMessageArray));

//            $setData = " SET approved_status = 'mail_sent' ";
            $setData = array('approved_status' => 'mail_sent');
            $this->mg_model->updateProgressionEmaiLStatus($setData, $roId);

        } else if ($type == CAMPAIGN_START && strtotime($currentDate) >= strtotime($campaignStartDate)) {

            $marketDataArray = $this->mg_model->getScheduledRoMarketData($roId);
            array_push($logMessageArray, array("markets" => $marketDataArray));

            $marketDataTable = $this->buildMarketBulletsForProgressionMail($marketDataArray);

            $emailTextKey = "ro_progress_start";
            $subject = array("START" => "Campaign is now playing - $subjectRoNumber ");

            $greetings = "Your campaign is now playing!";
            $greetingsHeading = "We would like to inform you that your order " . $RoNumber . " is now successfully playing as per the schedule in the following markets:";
            $nextStepText = "We will inform you when your campaign is about to end. ";
            $queryText = "Please get in touch, for any support.";
            $contact = $AcMangerName . ' - ( ' . $AcMangerNumber . " )";
            $innerBodyText = "You can track the progress at any time by logging in to " . $campaignExpressLink . ". <br/> In case, you do not have an access, please get in touch with " . $AcMangerName . " on " . $AcMangerNumber . ".";

            $message = array(

                "HEAD_TEAXTURE" => base_url('images/ro_progress/final_revised/head-texture.png'),
                "LOGO" => base_url('images/ro_progress/final_revised/surewaves-logo.png'),
                "BLUE" => base_url('images/ro_progress/final_revised/blue-1.jpg'),
                "MAIL" => base_url('images/ro_progress/final_revised/mail.png'),
                "PHONE" => $userProfileImage,

                "GREETINGS_HEADING" => $greetings,
                "GREETINGS_HEADING_NEXT_LINE" => $greetingsHeading,
                "DATA_TABLE" => $marketDataTable,
                "INNER_BODY_TEXT" => $innerBodyText,
                "NEXT_STEP_TEXT" => $nextStepText,
                "QUERY_TEXT" => $queryText,
                "CLIENT_NAME" => $clientName,
                "BRAND_NAME" => $brandName,
                "CONTACT" => $contact
            );

            $this->sendRoProgressMail($toEmailList, $ccEmailList, $emailTextKey, $message, $subject);
            $this->mg_model->insertRoProgressionMailLog($roId, "Order now playing", "Order now playing - $subjectRoNumber ", $toEmailList, "", json_encode($logMessageArray));

//            $setData = " SET campaign_start_status = 'mail_sent' ";
            $setData = array('campaign_start_status' => 'mail_sent');
            $this->mg_model->updateProgressionEmaiLStatus($setData, $roId);

        } else if ($type == CAMPAIGN_END && strtotime($currentDate) > strtotime($campaignEndDate)) {

            $marketDataArray = $this->mg_model->getScheduledRoMarketData($roId);
            array_push($logMessageArray, array("markets" => $marketDataArray));

            $marketDataTable = $this->buildMarketBulletsForProgressionMail($marketDataArray);

            $emailTextKey = "ro_progress_complete";
            $subject = array("COMPLETE" => "Campaign is now complete - $subjectRoNumber");

            $greetings = "Your campaign is now complete. Thank you!";
            $greetingsSubHeading = "We hope to see you again soon.";
            $greetingsHeading = "We are pleased to inform you that your order " . $RoNumber . " has successfully completed in the following markets:";
            $nextStepText = "Please get in touch with " . $AcMangerName . " for renewing the campaign.";
            $queryText = "Please get in touch, for start a new campaign.";
            $contact = $AcMangerName . ' - ( ' . $AcMangerNumber . " )";
            $innerBodyText = "It has been a pleasure serving you. Thanks for choosing SureWaves. We hope to see you again soon!";

            $message = array(

                "HEAD_TEAXTURE" => base_url('images/ro_progress/final_revised/head-texture.png'),
                "LOGO" => base_url('images/ro_progress/final_revised/surewaves-logo.png'),
                "BLUE" => base_url('images/ro_progress/final_revised/blue-1.jpg'),
                "MAIL" => base_url('images/ro_progress/final_revised/mail.png'),
                "PHONE" => $userProfileImage,

                "GREETINGS_HEADING" => $greetings,
                "GREETINGS_SUB_HEADING" => $greetingsSubHeading,
                "GREETINGS_HEADING_NEXT_LINE" => $greetingsHeading,

                "DATA_TABLE" => $marketDataTable,
                "INNER_BODY_TEXT" => $innerBodyText,
                "NEXT_STEP_TEXT" => $nextStepText,
                "QUERY_TEXT" => $queryText,
                "CLIENT_NAME" => $clientName,
                "BRAND_NAME" => $brandName,
                "CONTACT" => $contact
            );

            $this->sendRoProgressMail($toEmailList, $ccEmailList, $emailTextKey, $message, $subject);
            $this->mg_model->insertRoProgressionMailLog($roId, "Order Complete", "Order Complete - $subjectRoNumber ", $toEmailList, "", json_encode($logMessageArray));

//            $setData = " SET campaign_end_status = 'mail_sent' ";
            $setData = array('campaign_end_status' => 'mail_sent');
            $this->mg_model->updateProgressionEmaiLStatus($setData, $roId);

        } else if ($type == CAMPAIGN_PRE_CLOSURE &&
            ($this->getDatesDifference($campaignStartDate, $campaignEndDate) >= 15 &&
                $this->getDatesDifference($campaignEndDate, $currentDate) <= 3)) {

            array_push($logMessageArray, array("markets" => array()));

            $emailTextKey = "ro_progress_pre_closure";
            $subject = array("PRE_CLOSURE" => "Campaign is about to complete - $subjectRoNumber");

            $greetings = "Your campaign is about to complete!";
            $greetingsHeading = "We would like to inform you that your campaign against order " . $RoNumber . " will complete on " . $RoEndDate . ". We hope that the campaign has successfully helped your brand reach its audience. Please get in touch with " . $AcMangerName . " on " . $AcMangerNumber . " for renew the campaign.";
            $nextStepText = "We will inform you when your campaign is complete.";
            $queryText = "Please get in touch, for any support.";
            $contact = $AcMangerName . ' - ( ' . $AcMangerNumber . " )";
            $innerBodyText = "You can track the progress at any time by logging in to " . $campaignExpressLink . ".<br/> In case, you do not have an access, please get in touch with " . $AcMangerName . " on " . $AcMangerNumber . ".";

            $message = array(
                "HEAD_TEAXTURE" => base_url('images/ro_progress/final_revised/head-texture.png'),
                "LOGO" => base_url('images/ro_progress/final_revised/surewaves-logo.png'),
                "BLUE" => base_url('images/ro_progress/final_revised/blue-1.jpg'),
                "MAIL" => base_url('images/ro_progress/final_revised/mail.png'),
                "PHONE" => $userProfileImage,

                "GREETINGS_HEADING" => $greetings,
                "GREETINGS_HEADING_NEXT_LINE" => $greetingsHeading,
                "INNER_BODY_TEXT" => $innerBodyText,
                "NEXT_STEP_TEXT" => $nextStepText,
                "QUERY_TEXT" => $queryText,
                "CLIENT_NAME" => $clientName,
                "BRAND_NAME" => $brandName,
                "CONTACT" => $contact
            );

            $this->sendRoProgressMail($toEmailList, $ccEmailList, $emailTextKey, $message, $subject);
            $this->mg_model->insertRoProgressionMailLog($roId, "Order about to complete", "Order about to complete - $subjectRoNumber ", $toEmailList, "", json_encode($logMessageArray));

//            $setData = " SET campaign_preclousure_status = 'mail_sent' ";
            $setData = array('campaign_preclousure_status' => 'mail_sent');
            $this->mg_model->updateProgressionEmaiLStatus($setData, $roId);

        } else if ($type == RO_CANCEL) {

            // If cancellation request get accepted.

            if ($dataArray['cancel_ro_by_admin'] == 1) {

                array_push($logMessageArray, array("markets" => array()));


                $this->mg_model->insertRoProgressionMailLog($roId, "Order canceled", "Order canceled - $subjectRoNumber ", $toEmailList, "", json_encode($logMessageArray));

//                $setData = " SET ro_cancel_status = 'mail_sent' ";
                $setData = array('ro_cancel_status' => 'mail_sent');
                $this->mg_model->updateProgressionEmaiLStatus($setData, $roId);

            } else {

                // Else cancellation request get rejected.
//                $setData = " SET ro_cancel_status = 'dont_send_mail' ";
                $setData = array('ro_cancel_status' => 'dont_send_mail');
                $this->mg_model->updateProgressionEmaiLStatus($setData, $roId);
            }
        } else if ($type == MARKET_CANCEL) {

            $oldCancelCount = $dataArray['oldCount'];
            $currentCancelCount = $dataArray['currentCount'];

            array_push($logMessageArray, array("markets" => array()));

            if ($currentCancelCount > $oldCancelCount) {

                $marketDataArray = $this->mg_model->getScheduledRoMarketData($roId);
                $this->mg_model->insertRoProgressionMailLog($roId, "Market cancelled", "Market cancelled - $subjectRoNumber ", $toEmailList, "", json_encode($logMessageArray));

//                $setData = " SET market_cancel_count = $currentCancelCount ";
                $setData = array('market_cancel_count' => $currentCancelCount);
                $this->mg_model->updateProgressionEmaiLStatus($setData, $roId);
            }

        } else if ($type == EDIT_RO) {

        }

        return 1;
    }

    //function to send reports by mail

    function buildMarketDataTableForSubmitMail($marketDataArray)
    {

        $marketTable = '<table width="100%" class="order-table" style="width: 100%;margin: 25px auto 17px;border: 1px solid #D5D5D5;color: #304856;">
                        <thead style=" background: #E0E0E0;">
                            <tr align="center" style="height: 32px;border-bottom: 1px solid #d5d5d5;color: #505050;font-size: 14px;" width="100%">
                                <th>Markets/Clusters</th>
                                <th>Spot FCT </th>
                                <th>Aston FCT</th>
                            </tr>
                        </thead>';

        foreach ($marketDataArray as $key => $value) {

            $marketTable .= ' <tbody style="font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px;background: rgba(255, 255, 255, 0.69);">
                                            <tr align="center" style="border-top: 1px dotted #ddd;border-bottom: 1px dotted #CBC9C9;height: 35px;color: #000;">
                                                <td width="600">' . $value['market'] . '</td>
                                                <td width="300" >' . $value['spot_fct'] . '</td>
                                                <td width="300" >' . $value['banner_fct'] . '</td>
                                            </tr>
                                        </tbody> ';

        }
        $marketTable .= ' </table>';

        return $marketTable;
    }


    //function to get campaign performance report csv on mail

    private function sendRoProgressMail($sendToList, $ccEmailList, $emailTextKey, $message, $subject)
    {
        $staticEmails = $this->getStaticMails('ro_progress_email');
        email_send_bcc($sendToList, $ccEmailList, $staticEmails, $emailTextKey, $subject, $message);

        echo '<pre>';
        echo "Sending Mail------->" . $sendToList . "  " . $emailTextKey;

        return 1;

    }

    //function to get campaign performance report csv on mail

    function buildMarketBulletsForProgressionMail($marketDataArray)
    {

        $leftPadding = (count($marketDataArray) < 2) ? "41%" : "20%";

        $marketTable = '<table width="100%" style="margin:0px auto 10px;border:1px solid #d5d5d5;color:#304856">
                        <tbody style="font-size:13px;background:rgba(255,255,255,0.69)">
                        <tr align="" style="border-top:1px dotted #ddd;border-bottom:1px dotted #cbc9c9;height:35px;color:#000">
                        <td>
                        <table  width="100%" align="center" style="padding:0px 10px 10px ' . $leftPadding . ' ;font-family: Verdana, Geneva, sans-serif;border-width:0px;" border="0">
                        <tr>';

        $pointer = base_url("images/ro_progress/final_revised/Pointer_Grey.png");
        $count = 0;

        foreach ($marketDataArray as $key => $value) {

            /*$marketTable .= '<table align="left" width="50%" style="padding-left: 15%;margin-top:5px;margin-bottom:5px;font-family: Verdana, Geneva, sans-serif;border-width:0px;min-width: 50%;" border="0">
                             <tr><td><img src='.$pointer.'><span style="margin-left:4px;">'.$value['market'].'</span></td></tr></table>	' ;
            */
            $count = $key + 1;

            $marketTable .= '<td style="padding-top: 10px;"> <img src=' . $pointer . '><span style="margin-left:4px;">' . $value['market'] . '</span> </td>';

            if (($count % 2) == 0) {

                $marketTable .= "</tr><tr>";
            }
        }

        //$marketTable .= '</td> </tr></table></td></tr></tbody></table>' ;
        $marketTable .= '</tr></table></td></tr></tbody></table>';

        return $marketTable;
    }

    /***************----- ENd Date 2014- Nov 26 PDF Generation ------*****************/


    public function getDatesDifference($startDate, $endDate)
    {


        $start = strtotime($startDate);
        $end = strtotime($endDate);

        $days_between = ceil(abs($end - $start) / 86400);

        return $days_between;
    }


    /* Send mail from accounts */

    public function process_requested_reports()
    {
        /*$this->load->library('Cron_Lock_Manager') ;
        $isLock = $this->lock('process_requested_reports');
        if(! $isLock) {
            exit ;
        }*/
        $data = array(
            'job_code' => 'processRequestedReports',
            'done' => 2
        );
        $job_queue_data = $this->mg_model->get_from_job_queue($data);

        if (count($job_queue_data) > 0) {
            //if yes then exit
            return;
        } else {
            //else create a new csv file
            $data = array(
                'job_code' => 'processRequestedReports',
                'done' => 0
            );
            $job_queue_data = $this->mg_model->get_from_job_queue($data);

            foreach ($job_queue_data as $job) {
                $mail_data = explode("#", $job['params']);

                if ($mail_data[0] == 'networkRemittanceReport') {
                    //process request for networkRemittanceReport
                    $this->process_network_remittance_csv($job);

                } else if ($mail_data[0] == 'campaignPerformanceReport') {
                    //process request for campaignPerformanceReport
                    $this->process_campaign_performance_csv($job);
                } else if ($mail_data[0] == 'networkRoReportMonthWise') {
                    //process request for campaignPerformanceReport
                    $this->sendNetworkRoReportByMail($job);
                }

            }

        }
        $this->cron_lock_manager->unlock('process_requested_reports');
    }
    /*
    public function sendNetworkRoReportByMail() {
        $whereData = array('job_code'=>'processRequestedReports','done' => 0) ;
        $jobQueueData = $this->mg_model->get_from_job_queue($whereData) ;
        foreach($jobQueueData as $queueVal) {
            $jobQueueId = $queueVal['job_id'] ;
            $params = explode("#",$queueVal['params']) ;
            
            $customer_ro_number = $params[1] ;
            $start_date = $params[2];
            $end_date = $params[3] ;
            $user_type = $params[4] ;
	    $mailIds = $params[5];
            
            //update done=2 in sv_job_queue for job_id
            $where_data = array('job_id' => $jobQueueId);
            $user_data = array('done' => 2);
            $this->mg_model->update_job_queue($where_data,$user_data);
            $networkRos = $this->mg_model->getNetworkReportMisData($customer_ro_number,$start_date,$end_date,$user_type) ;
            $csvHeader = "customer_ro_number,internal_ro_number,network_ro_number,network_name,advertiser_client_name,"
                        . "agency_name,market,start_date,end_date,activity_months,gross_network_ro_amount,network_share,"
                        . "net_amount_payable,release_date,billing_name" ;
            
            
        }    
    } */
    /* Send mail from accounts */

    public function process_network_remittance_csv($data)
    {

        $mail_data = explode("#", $data['params']);

        //getting data from job queue
        $timestamp = $mail_data[1];
        $start_date = $mail_data[2];
        $end_date = $mail_data[3];
        $mail_id = $mail_data[4];
        $network_id = $mail_data[5];
        $fully_paid = $mail_data[6];
        $is_test_user = $mail_data[7];
        $job_id = $data['job_id'];
        $month = date('F', strtotime($start_date));


        //update done=2 in sv_job_queue for job_id
        $where_data = array('job_id' => $job_id);
        $user_data = array('done' => 2);
        $this->mg_model->update_job_queue($where_data, $user_data);


        //create csv file from campaign array
        $network_remittance = $this->ro_model->download_network_remittance_report_csv($network_id, $start_date, $end_date, $fully_paid, $is_test_user);

        $csv_header = "Network Name,Network Ro Fully Paid,Full Payment Received,Client Name,Network RO Number,Network Ro Release Date,Network Ro Paid Amount,Surewaves Share Amount,Network Billing Name,External RO Number,Agency Name,Activity Start Date,Activity End Date,Report Start Date,Report End Date,Activity spot seconds,Activity spot amount,Activity banner seconds,Activity banner amount,Scheduled spot seconds,Scheduled spot amount,Scheduled banner seconds,Scheduled banner amount,Total scheduled spot seconds,Total scheduled spot amount,Total scheduled banner seconds,Total scheduled banner amount,Payment Collected Amount,External RO Amount,Network RO Amount,Service Tax,Cancelled";
        $csv_data = "";
        $remittance_count = count($network_remittance);

        for ($i = 0; $i < $remittance_count; $i++) {
            if (empty($network_remittance[$i]['Network_RO_fully_paid']) || !isset($network_remittance[$i]['Network_RO_fully_paid']) || ($network_remittance[$i]['Network_RO_fully_paid'] == 0) || (trim($network_remittance[$i]['Network_RO_fully_paid']) == '')) {
                $Network_RO_fully_paid = 'False';
            } else {
                $Network_RO_fully_paid = 'True';
            }
            if (empty($network_remittance[$i]['Full_Payment_Received']) || !isset($network_remittance[$i]['Full_Payment_Received']) || ($network_remittance[$i]['Full_Payment_Received'] == 0) || (trim($network_remittance[$i]['Full_Payment_Received']) == '')) {
                $Full_Payment_Received = 'False';
            } else {
                $Full_Payment_Received = 'True';
            }
            if (empty($network_remittance[$i]['Network_RO_Paid_Amount']) || !isset($network_remittance[$i]['Network_RO_Paid_Amount']) || ($network_remittance[$i]['Network_RO_Paid_Amount'] == 0) || (trim($network_remittance[$i]['Network_RO_Paid_Amount']) == '')) {
                $network_remittance[$i]['Network_RO_Paid_Amount'] = '0';
            }
            // code commented by lokanath as csv report should be same as ui

            if (empty($network_remittance[$i]['Cancelled']) || !isset($network_remittance[$i]['Cancelled']) || ($network_remittance[$i]['Cancelled'] == 0) || (trim($network_remittance[$i]['Cancelled']) == '')) {
                $network_remittance[$i]['Cancelled'] = 'NO';
            }

            //for extra fields in Network remittance report 18-Aug-2014
            $temp = $this->ro_model->get_activity_rate($network_remittance[$i]['network_ro_number'], $start_date, $end_date, $network_remittance[$i]['customer_name'], 1);
            $scheduled_spot_amount = $temp[0];
            $scheduled_spot_seconds = $temp[1];
            $scheduled_banner_amount = $temp[2];
            $scheduled_banner_seconds = $temp[3];


            $temp = $this->ro_model->get_activity_rate($network_remittance[$i]['network_ro_number'], $start_date, $end_date, $network_remittance[$i]['customer_name'], 0);
            $activity_spot_amount = $temp[0];
            $activity_spot_seconds = $temp[1];
            $activity_banner_amount = $temp[2];
            $activity_banner_seconds = $temp[3];

            //if activity spot/banner sec/amount is greater than scheduled spot/banner sec/amount
            //if user is advanced ro user then make sch vs activity equal
            if ($activity_spot_seconds > $scheduled_spot_seconds || $network_remittance[$i]['is_test_ro'] == 2) {
                $activity_spot_seconds = $scheduled_spot_seconds;
                $activity_spot_amount = $scheduled_spot_amount;
            }
            if ($activity_banner_seconds > $scheduled_banner_seconds || $network_remittance[$i]['is_test_ro'] == 2) {
                $activity_banner_seconds = $scheduled_banner_seconds;
                $activity_banner_amount = $scheduled_banner_amount;
            }

            if (strtotime($start_date) < strtotime($network_remittance[$i]['Activity_Start_Date'])) {
                $report_start_date = $network_remittance[$i]['Activity_Start_Date'];
            } else {
                $report_start_date = $start_date;
            }

            if (strtotime($end_date) > strtotime($network_remittance[$i]['Activity_End_Date'])) {
                $report_end_date = $network_remittance[$i]['Activity_End_Date'];
            } else {
                $report_end_date = $end_date;
            }

            $temp = $this->ro_model->get_activity_rate($network_remittance[$i]['network_ro_number'], NULL, NULL, $network_remittance[$i]['customer_name'], 1);
            $total_scheduled_spot_amount = $temp[0];
            $total_scheduled_spot_seconds = $temp[1];
            $total_scheduled_banner_amount = $temp[2];
            $total_scheduled_banner_seconds = $temp[3];
            //end

            $csv_data .= "\n";

            $csv_data .= $network_remittance[$i]['customer_name'];
            $csv_data .= "," . $Network_RO_fully_paid;
            $csv_data .= "," . $Full_Payment_Received;
            $csv_data .= "," . $network_remittance[$i]['client_name'];
            $csv_data .= "," . $network_remittance[$i]['network_ro_number'];
            $csv_data .= "," . date('y-m', strtotime($network_remittance[$i]['release_date']));
            $csv_data .= "," . $network_remittance[$i]['Network_RO_Paid_Amount'];
            $csv_data .= "," . $network_remittance[$i]['SureWaves_Share_Amount'];
            $csv_data .= "," . $network_remittance[$i]['Network_Billing_Name'];
            $csv_data .= "," . $network_remittance[$i]['customer_ro_number'];
            $csv_data .= "," . $network_remittance[$i]['agency_name'];
            $csv_data .= "," . $network_remittance[$i]['Activity_Start_Date'];
            $csv_data .= "," . $network_remittance[$i]['Activity_End_Date'];
            $csv_data .= "," . $report_start_date;
            $csv_data .= "," . $report_end_date;
            $csv_data .= "," . $activity_spot_seconds;
            $csv_data .= "," . $activity_spot_amount;
            $csv_data .= "," . $activity_banner_seconds;
            $csv_data .= "," . $activity_banner_amount;
            $csv_data .= "," . $scheduled_spot_seconds;
            $csv_data .= "," . $scheduled_spot_amount;
            $csv_data .= "," . $scheduled_banner_seconds;
            $csv_data .= "," . $scheduled_banner_amount;
            $csv_data .= "," . $total_scheduled_spot_seconds;
            $csv_data .= "," . $total_scheduled_spot_amount;
            $csv_data .= "," . $total_scheduled_banner_seconds;
            $csv_data .= "," . $total_scheduled_banner_amount;
            $csv_data .= "," . $network_remittance[$i]['Payment_Collected_Amount'];
            $csv_data .= "," . $network_remittance[$i]['External_RO_Amount'];
            $csv_data .= "," . $network_remittance[$i]['Network_RO_Amount'];
            $csv_data .= "," . $network_remittance[$i]['Service_Tax'];
            $csv_data .= "," . $network_remittance[$i]['Cancelled'];
        }

        //download csv to server
        $network_remittance_report = "/opt/lampp/htdocs/surewaves_easy_ro/mailReports/network_remittance_report" . $timestamp . ".csv";

        $fp = fopen($network_remittance_report, "w");
        fwrite($fp, $csv_header);
        fwrite($fp, $csv_data);
        fclose($fp);

        //sending the report by mail
        $to = $mail_id;
        $cc = '';
        $file_location = "/opt/lampp/htdocs/surewaves_easy_ro/mailReports/network_remittance_report" . $timestamp . ".csv";
        $file_type = 'csv';

        mail_send_v1($to,
            "network_remittance_report",
            array(
                'START_DATE' => $start_date,
                'END_DATE' => $end_date
            ),
            array(
                'START_DATE' => $start_date,
                'END_DATE' => $end_date
            ),
            $file_location,
            '',
            '',
            $file_type
        );

        //update done=1 in sv_job_queue for job_id
        $where_data = array('job_id' => $job_id);
        $user_data = array('done' => 1);
        $this->mg_model->update_job_queue($where_data, $user_data);

    }

    public function process_campaign_performance_csv($data)
    {

        $mail_data = explode("#", $data['params']);

        //getting data from job queue
        $timestamp = $mail_data[1];
        $start_d = $mail_data[2];
        $end_d = $mail_data[3];
        $mail_id = $mail_data[4];
        $is_test_user = $mail_data[5];
        $job_id = $data['job_id'];
        $month = date('F o', strtotime($start_d));

        //update done=2 in sv_job_queue for job_id
        $where_data = array('job_id' => $job_id);
        $user_data = array('done' => 2);
        $this->mg_model->update_job_queue($where_data, $user_data);


        //create csv file from campaign array
        $channel_performance = $this->ro_model->download_channel_performance_report_csv($start_d, $end_d, $is_test_user);

        $csv_header = "MSO,Channel Name,Network Ro Number,Billing Name,Internal Ro Number,Start Date,End Date,Scheduled Spot Seconds,Played Spot Seconds,Scheduled Banner Seconds,played Banner Seconds,Spot Rate,Banner Rate,Total Payable";
        $csv_data = "";

        $channel_performance_count = count($channel_performance);

        for ($i = 0; $i < $channel_performance_count; $i++) {

            $csv_data .= "\n";

            $total_payable = ($channel_performance[$i]['playedSpotSeconds'] * $channel_performance[$i]['spotRate'] / 10) +
                ($channel_performance[$i]['playedBannerSeconds'] * $channel_performance[$i]['bannerRate'] / 10);

            if ($channel_performance[$i]['scheduledSpotSeconds'] == NULL) {
                $channel_performance[$i]['scheduledSpotSeconds'] = 0;
            }
            if ($channel_performance[$i]['playedSpotSeconds'] == NULL) {
                $channel_performance[$i]['playedSpotSeconds'] = 0;
            }
            if ($channel_performance[$i]['scheduledBannerSeconds'] == NULL) {
                $channel_performance[$i]['scheduledBannerSeconds'] = 0;
            }
            if ($channel_performance[$i]['playedBannerSeconds'] == NULL) {
                $channel_performance[$i]['playedBannerSeconds'] = 0;
            }

            $csv_data .= $channel_performance[$i]['mso'];
            $csv_data .= "," . $channel_performance[$i]['channelName'];
            $csv_data .= "," . $channel_performance[$i]['networkRoNumber'];
            $csv_data .= "," . $channel_performance[$i]['billingName'];
            $csv_data .= "," . $channel_performance[$i]['internalRoNumber'];
            $csv_data .= "," . date('Y-m-d', strtotime($channel_performance[$i]['startDT']));
            $csv_data .= "," . date('Y-m-d', strtotime($channel_performance[$i]['endDT']));
            $csv_data .= "," . $channel_performance[$i]['scheduledSpotSeconds'];
            $csv_data .= "," . $channel_performance[$i]['playedSpotSeconds'];
            $csv_data .= "," . $channel_performance[$i]['scheduledBannerSeconds'];
            $csv_data .= "," . $channel_performance[$i]['playedBannerSeconds'];
            $csv_data .= "," . $channel_performance[$i]['spotRate'];
            $csv_data .= "," . $channel_performance[$i]['bannerRate'];
            $csv_data .= "," . $total_payable;
        }


        //download csv to server
        $performance_report = "/opt/lampp/htdocs/surewaves_easy_ro/mailReports/campaign_performance_report_" . $timestamp . ".csv";

        $fp = fopen($performance_report, "w");
        fwrite($fp, $csv_header);
        fwrite($fp, $csv_data);
        fclose($fp);

        //sending the report by mail
        $to = $mail_id;
        $cc = '';
        $file_location = "/opt/lampp/htdocs/surewaves_easy_ro/mailReports/campaign_performance_report_" . $timestamp . ".csv";
        $file_type = 'csv';

        mail_send_v1($to,
            "campaign_performance_report",
            array(
                'MONTH' => $month
            ),
            array(
                'MONTH' => $month
            ),
            $file_location,
            '',
            '',
            $file_type
        );

        //update done=1 in sv_job_queue for job_id
        $where_data = array('job_id' => $job_id);
        $user_data = array('done' => 1);
        $this->mg_model->update_job_queue($where_data, $user_data);

    }

    /* Send mail from accounts */

    public function sendNetworkRoReportByMail()
    {

        $whereData = array('job_code' => 'processRequestedReports', 'done' => 0);
        $jobQueueData = $this->mg_model->get_from_job_queue($whereData);
        foreach ($jobQueueData as $queueVal) {
            $jobQueueId = $queueVal['job_id'];
            $params = explode("#", $queueVal['params']);

            $customer_ro_number = $params[1];
            $start_date = $params[2];
            $end_date = $params[3];
            $user_id = $params[4];
            $mailIds = $params[5];
            $mail_start_date = $start_date;
            $mail_end_date = $end_date;

            //update done=2 in sv_job_queue for job_id
            $where_data = array('job_id' => $jobQueueId);
            $user_data = array('done' => 2);
            $this->mg_model->update_job_queue($where_data, $user_data);

            $networkRos = $this->mg_model->getNetworkReportData($customer_ro_number, $start_date, $end_date, $user_id);
            $csvHeader = "customer_ro_number,internal_ro_number,network_ro_number,network_name,advertiser_client_name,"
                . "agency_name,market,start_date,end_date,activity_months,gross_network_ro_amount,network_share,"
                . "net_amount_payable,release_date,billing_name";
            $csvData = "";
            foreach ($networkRos as $value) {
                //commented 9 Nov 2015 to make calculations faster through mis report
                //$monthlyFraction = $this->mg_model->getNetworkRoReportBreakUpByMonth($internal_ro_number,$start_date,$end_date) ;

                $network_mis_details = $this->mg_model->getMisDataForNetwork($value['internal_ro_number'], $value['customer_name']);

                foreach ($network_mis_details as $network) {

                    $nw_gross = ($network['net_amount_payable'] * 100) / $value['customer_share'];

                    $csvData .= "\n";
                    $csvData .= $value['customer_ro_number'];
                    $csvData .= "," . $value['internal_ro_number'];
                    $csvData .= "," . $value['network_ro_number'];
                    $csvData .= "," . $value['customer_name'];
                    $csvData .= "," . $value['client_name'];
                    $csvData .= "," . $value['agency_name'];
                    $csvData .= "," . $value['market'];
                    $csvData .= "," . $value['start_date'];
                    $csvData .= "," . $value['end_date'];
                    $csvData .= "," . $network['month_name'];
                    $csvData .= "," . round($nw_gross);
                    $csvData .= "," . $value['customer_share'];
                    $csvData .= "," . round($network['net_amount_payable']);
                    $csvData .= "," . $value['release_date'];
                    $csvData .= "," . $value['billing_name'];
                }

            }
            $network_report = "/opt/lampp/htdocs/surewaves_easy_ro/mailReports/network_report_" . date('Y-m-d_H:i:s') . ".csv";

            $fp = fopen($network_report, "w");
            fwrite($fp, $csvHeader);
            fwrite($fp, $csvData);
            fclose($fp);

            //sending the report by mail
            //$to = 'mani@surewaves.com,shivaraj@surewaves.com' ;
            $to = $mailIds;
            $cc = '';
            $file_location = $network_report;
            $file_type = 'csv';

            mail_send_v1($to,
                "network_report_month_wise",
                array(
                    'START_DATE' => $mail_start_date,
                    'END_DATE' => $mail_end_date
                ),
                array(
                    'START_DATE' => $mail_start_date,
                    'END_DATE' => $mail_end_date
                ),
                $file_location,
                '',
                '',
                $file_type
            );
            $this->mg_model->update_job_queue(array('job_id' => $jobQueueId), array('done' => 1));
        }

    }

    public function updateMailDataNetworkRoNumber()
    {

        $mail_data = $this->ro_model->get_mail_data();

        foreach ($mail_data as $data) {
            $id = $data['id'];
            $message = unserialize($data['message']);


            $internal_ro_number = $message['INTERNAL_RO'];
            $network_name = $message['NETWORK_NAME'];

            $network_ro = $this->mg_model->get_nw_ro_number($internal_ro_number, $network_name);
            $this->ro_model->update_mail_sent_data(array('network_ro_number' => $network_ro), $id);

        }
    }

    public function getCustomerListForSendMail($year)
    {

        $emailTextKey = "mail_from_accounts";
        $subject = array("SUBJECT" => "Change in Service Tax rate.");
        $message = array("HEADER" => base_url('images/ro_progress/header.png'),
            "FOTTER" => base_url('images/ro_progress/footer.png'));

        $sendToListTemp = $this->mg_model->getCustomerMailIdsToSend($year);
        //echo '<pre>' ;
        // print_r( $sendToListTemp ) ;

//      $sendToList =  array('shahrukh@surewaves.com,deepak@surewaves.com') ;
        $sendToList = $sendToListTemp;

        foreach ($sendToList as $mailIds) {

            echo 'Sending mail to' . $mailIds;
            //email_send($mailIds, 'shivaraj@surewaves.com,sudheer@surewaves.com', $emailTextKey, $subject, $message);
		email_send($mailIds, 'shivaraj@surewaves.com', $emailTextKey, $subject, $message);

        }

    }

    public function getAggrgateReport($parameters1, $parameters2, $parameters3)
    {
        //echo $parameters1."<br>".$parameters2.$parameters3;
        echo "<pre>";
        print_r($args);
        //$this->channel_aggregate_model->call_channelwiseAggregate();
    }

    Public function calculateNetContribuitionAndMarketRate($ro_id = NULL)
    {
        $this->mg_model->getApproximateNetContribution($ro_id);
    }

    public function MailSentForRo($mail_type_val)
    {
        if (isset($mail_type_val) && !empty($mail_type_val)) {
            $mailData = $this->ro_model->getMailForRo(array('mail_sent' => 0, 'mail_type' => $mail_type_val));
        } else {
            $mailData = $this->ro_model->getMailForRo(array('mail_sent' => 0));
        }

        foreach ($mailData as $val) {
            $this->ro_model->updateMailForRo(array('id' => $val['id']), array('mail_sent' => 2));
            $mail_type = $val['mail_type'];
            //$mailTemplate = $this->getMailTemplateId($mail_type);

            if ($mail_type == 'submit_ro_approval') {
                //Mail for FCT RO

                $roDetail = $this->am_model->ro_detail_for_ro_id($val['ro_id']);


//                This flow is changed. now ro files are uploaded as a single file in s3 and file location is saved in ro_am_external_ro <file_path> table
//                instead of ro_am_external_ro_files <file_location> table

//                $roFiles = $this->am_model->getFilesForAmRo(array('ro_id' => $val['ro_id']));
                $allfiles = "";

                $fileDocumentPath = $_SERVER['DOCUMENT_ROOT'];
                if (!isset($fileDocumentPath) || empty($fileDocumentPath)) {
                    $fileDocumentPath = "/opt/lampp/htdocs/";
                }
                $actual_path_location = $fileDocumentPath . "surewaves_easy_ro/" . 'easy_ro_temp_pdf/';

                foreach ($roDetail as $file) {
//                    $path_parts = pathinfo($file['file_location']); //file location is available in ro_am_external_ro <file_path>
                    $path_parts = pathinfo($file['file_path']);
                    if (empty($allfiles) || !isset($allfiles)) {
                        $allfiles = $actual_path_location . "" . $path_parts['basename'];
                    } else {
                        $allfiles = $allfiles . "," . $actual_path_location . "" . $path_parts['basename'];
                    }
                }
                $client_approval_mail_attachment = pathinfo($roDetail[0]['client_approval_mail']);
                $allfiles = $allfiles . "," . $actual_path_location . "" . $client_approval_mail_attachment['basename'];

                $submittedUserDetail = $this->user_model->getUserDetailValues($roDetail[0]['user_id']);
                $brand_name = $this->am_model->get_brand_names($roDetail[0]['brand']);
                $where_data = array(
                    'ext_ro_id' => $val['ro_id'],
                    'cancel_type' => 'submit_ro_approval',
                    'cancel_ro_by_admin' => 2
                );
                $get_request_details = $this->am_model->is_cancel_request_sent_by_am($where_data);
                $reason = $get_request_details[0]['bh_reason'];
                $make_good_type = '';
                if ($roDetail[0]['make_good_type'] == 0) {
                    $make_good_type = 'Auto Make Good';
                } else if ($roDetail[0]['make_good_type'] == 1) {
                    $make_good_type = 'Client approved make good';
                } else {
                    $make_good_type = 'No Make Good';
                }

                switch ($val['mail_status']) {

                    //When the RO is submitted
                    case 0:
                        mail_send_v1($val['user_email_id'],
                            'create_ext_ro',
                            array('EXTERNAL_RO' => $roDetail[0]['cust_ro']),
                            array(
                                'AM_NAME' => $submittedUserDetail[0]['user_name'],
                                'EXTERNAL_RO' => $roDetail[0]['cust_ro'],
                                'INTERNAL_RO' => $roDetail[0]['internal_ro'],
                                'AGENCY' => $roDetail[0]['agency'],
                                'CLIENT' => $roDetail[0]['client'],
                                'BRAND' => $brand_name,
                                'MAKEGOOD_TYPE' => $make_good_type,
                                'MARKET' => $roDetail[0]['market'],
                                'INSTRUCTION' => $roDetail[0]['spcl_inst'],
                                'START_DATE' => $roDetail[0]['camp_start_date'],
                                'END_DATE' => $roDetail[0]['camp_end_date']
                            ),
                            $allfiles,
                            $val['cc_email_id'],
                            '',
                            ''
                        );
                        $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('id' => $val['id']));
                        break;
                    //When RO is approved & Ready for Scheduling
                    case 1:
                        mail_send_v1($val['user_email_id'],
                            'approve_ext_ro',
                            array('EXTERNAL_RO' => $roDetail[0]['cust_ro']),
                            array(
                                'AM_NAME' => $submittedUserDetail[0]['user_name'],
                                'EXTERNAL_RO' => $roDetail[0]['cust_ro'],
                                'INTERNAL_RO' => $roDetail[0]['internal_ro'],
                                'AGENCY' => $roDetail[0]['agency'],
                                'CLIENT' => $roDetail[0]['client'],
                                'BRAND' => $brand_name,
                                'MAKEGOOD_TYPE' => $make_good_type,
                                'MARKET' => $roDetail[0]['market'],
                                'INSTRUCTION' => $roDetail[0]['spcl_inst'],
                                'START_DATE' => $roDetail[0]['camp_start_date'],
                                'END_DATE' => $roDetail[0]['camp_end_date']
                            ),
                            $allfiles,
                            $val['cc_email_id'],
                            '',
                            ''
                        );
                        $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('id' => $val['id']));
                        break;
                    //When RO is Rejected
                    case 2:
                        mail_send_v1($val['user_email_id'],
                            'reject_ext_ro',
                            array('EXTERNAL_RO' => $roDetail[0]['cust_ro']),
                            array(
                                'AM_NAME' => $submittedUserDetail[0]['user_name'],
                                'EXTERNAL_RO' => $roDetail[0]['cust_ro'],
                                'INTERNAL_RO' => $roDetail[0]['internal_ro'],
                                'AGENCY' => $roDetail[0]['agency'],
                                'CLIENT' => $roDetail[0]['client'],
                                'BRAND' => $brand_name,
                                'MAKEGOOD_TYPE' => $make_good_type,
                                'MARKET' => $roDetail[0]['market'],
                                'INSTRUCTION' => $roDetail[0]['spcl_inst'],
                                'START_DATE' => $roDetail[0]['camp_start_date'],
                                'END_DATE' => $roDetail[0]['camp_end_date'],
                                'REASON' => $reason
                            ),
                            $allfiles,
                            $val['cc_email_id'],
                            '',
                            ''
                        );
                        $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('id' => $val['id']));
                        break;
                    //When RO is forwarded to upper level
                    case 3:
                        if ($val['approval_level'] == 2) {
                            $forwarded_to = "National Head";
                        } else if ($val['approval_level'] == 3) {
                            $forwarded_to = "Business Head";
                        }
                        mail_send_v1($val['user_email_id'],
                            'forward_ext_ro',
                            array('EXTERNAL_RO' => $roDetail[0]['cust_ro']),
                            array(
                                'AM_NAME' => $submittedUserDetail[0]['user_name'],
                                'EXTERNAL_RO' => $roDetail[0]['cust_ro'],
                                'INTERNAL_RO' => $roDetail[0]['internal_ro'],
                                'AGENCY' => $roDetail[0]['agency'],
                                'CLIENT' => $roDetail[0]['client'],
                                'BRAND' => $brand_name,
                                'MAKEGOOD_TYPE' => $make_good_type,
                                'MARKET' => $roDetail[0]['market'],
                                'INSTRUCTION' => $roDetail[0]['spcl_inst'],
                                'START_DATE' => $roDetail[0]['camp_start_date'],
                                'END_DATE' => $roDetail[0]['camp_end_date'],
                                'FORWARDED_TO' => $forwarded_to
                            ),
                            $allfiles,
                            $val['cc_email_id'],
                            '',
                            ''
                        );
                        $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('id' => $val['id']));
                        break;

                }
            } else if ($mail_type == 'non_fct_ro_approval') {
                //Mail for Non FCT RO
                $this->ro_model->updateMailForRo(array('id' => $val['id']), array('mail_sent' => 2));
                $roDetail = $this->am_model->get_non_fct_ro_details($val['ro_id'], $edit = null);
                $submittedUserDetail = $this->user_model->getUserDetailValues($roDetail[0]['user_id']);

                switch ($val['mail_status']) {
                    //When the RO is submitted
                    case 0:
                        mail_send_v1($val['user_email_id'],
                            'create_non_fct_ro',
                            array('EXTERNAL_RO' => $roDetail[0]['customer_ro_number']),
                            array(
                                'AM_NAME' => $submittedUserDetail[0]['user_name'],
                                'EXTERNAL_RO' => $roDetail[0]['customer_ro_number'],
                                'INTERNAL_RO' => $roDetail[0]['internal_ro_number'],
                                'AGENCY' => $roDetail[0]['agency'],
                                'CLIENT' => $roDetail[0]['client'],
                                'INSTRUCTION' => $roDetail[0]['description']
                            ),
                            '',
                            $val['cc_email_id'],
                            '',
                            ''
                        );

                        $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('id' => $val['id']));
                        break;

                    //When RO is approved
                    case 1:
                        mail_send_v1($val['user_email_id'],
                            'approve_non_fct_ro',
                            array('EXTERNAL_RO' => $roDetail[0]['customer_ro_number']),
                            array(
                                'AM_NAME' => $submittedUserDetail[0]['user_name'],
                                'EXTERNAL_RO' => $roDetail[0]['customer_ro_number'],
                                'INTERNAL_RO' => $roDetail[0]['internal_ro_number'],
                                'AGENCY' => $roDetail[0]['agency'],
                                'CLIENT' => $roDetail[0]['client'],
                                'INSTRUCTION' => $roDetail[0]['description']
                            ),
                            '',
                            $val['cc_email_id'],
                            '',
                            ''
                        );
                        $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('id' => $val['id']));
                        break;

                    //When RO is Rejected
                    case 2:
                        mail_send_v1($val['user_email_id'],
                            'reject_non_fct_ro',
                            array('EXTERNAL_RO' => $roDetail[0]['customer_ro_number']),
                            array(
                                'AM_NAME' => $submittedUserDetail[0]['user_name'],
                                'EXTERNAL_RO' => $roDetail[0]['customer_ro_number'],
                                'INTERNAL_RO' => $roDetail[0]['internal_ro_number'],
                                'AGENCY' => $roDetail[0]['agency'],
                                'CLIENT' => $roDetail[0]['client'],
                                'INSTRUCTION' => $roDetail[0]['description']
                            ),
                            '',
                            $val['cc_email_id'],
                            '',
                            ''
                        );
                        $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('id' => $val['id']));
                        break;
                }
            } else if ($mail_type == 'channel_performance') {
                $givenDate = date('Y-m-d');
                $to_mail_id = $val['user_email_id'];
                $cc_mail_id = $val['cc_email_id'];
                $duration = 'Daily';
                if (date("w", strtotime($givenDate)) == 1) {
                    $from_number_of_days = "-1";
                    //$to_number_of_days = "-31" ;
                    //$mail_key = "channel_performance_monthly" ;
                    //$staticEmails  	= $this->getStaticMails('channel_performance_weekly');
                    //$cc_mail_id = $cc_mail_id.','.$staticEmails;
                    $to_number_of_days = "-7";
                    $mail_key = "channel_performance_weekly";
                    $duration = 'Weekly';
                    //$this->generateChannelSummaryReport($from_number_of_days,$to_number_of_days,$to_mail_id,$cc_mail_id,$mail_key) ;                    
                } else {
                    $from_number_of_days = "-1";
                    $to_number_of_days = "-1";
                    $mail_key = "channel_performance_weekly";
                    //$this->generateChannelSummaryReport($from_number_of_days,$to_number_of_days,$to_mail_id,$cc_mail_id,$mail_key) ;
                }
                $this->generateChannelSummaryReport($from_number_of_days, $to_number_of_days, $to_mail_id, $cc_mail_id, $mail_key, $duration);
                $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('mail_type' => 'channel_performance'));
            } else if ($mail_type == 'cancel_ro_requested') {
                $ro_id = $val['ro_id'];
                $to_email = $val['user_email_id'];
                $cc = $val['cc_email_id'];
                $ro_details = $this->am_model->ro_detail_for_ro_id($ro_id);
                $external_ro = $ro_details[0]['cust_ro'];
                $client_name = $ro_details[0]['client'];
                $agency_name = $ro_details[0]['agency'];
                $campaign_end_date = $this->am_model->get_actual_campaign_end_date_for_ro($ro_details[0]['internal_ro']);
                $cancelled_data = $this->am_model->get_cancelled_data(array('cancel_type' => 'cancel_ro', 'ext_ro_id' => $ro_id));
                $user_id = $cancelled_data[0]['user_id'];
                $userName = $this->am_model->get_user_name($user_id);

                mail_send_v1($to_email,
                    "am_cancel_ext_ro",
                    array('EXTERNAL_RO' => $external_ro),
                    array(
                        'ACCOUNT_MGR_NAME' => $userName[0]['user_name'],
                        'EXTERNAL_RO' => $external_ro,
                        'CLIENT_NAME' => $client_name,
                        'AGENCY_NAME' => $agency_name,
                        'CAMPAIGN_END_DATE' => $campaign_end_date,
                        'RO_CANCEL_DATE' => $cancelled_data[0]['date_of_cancel'],
                        'CANCEL_REASON' => $cancelled_data[0]['reason'],
                        'BILLING_INSTRUCTION' => $cancelled_data[0]['invoice_instruction'],
                    ),
                    '',
                    $cc,
                    '',
                    '');
                $this->ro_model->updateMailForRo(array('mail_sent' => 1, 'mail_sent_date' => date('Y-m-d')), array('ro_id' => $ro_id, 'mail_type' => 'cancel_ro_requested'));
            }
        }
    }

    public function generateChannelSummaryReport($from_number_of_days, $to_number_of_days, $to_mail_id, $cc_mail_id, $mail_key, $duration)
    {
        $onlineChannelSummary = $this->mg_model->getOnlineChannelSummary($from_number_of_days, $to_number_of_days);
        $channelSummary = $this->mg_model->getChannelSummary($from_number_of_days, $to_number_of_days);

        $OnlinePerformanceReportGreaterThanNinetyFive = $this->mg_model->getChannelPerformanceByCriteria(0, 95, 200, $from_number_of_days, $to_number_of_days);
        $OnlinePerformanceReportGreaterThanEightyFive = $this->mg_model->getChannelPerformanceByCriteria(0, 85, 95, $from_number_of_days, $to_number_of_days);
        $OnlinePerformanceReportGreaterThanSeventyFive = $this->mg_model->getChannelPerformanceByCriteria(0, 75, 85, $from_number_of_days, $to_number_of_days);
        $OnlinePerformanceReportGreaterThanSixty = $this->mg_model->getChannelPerformanceByCriteria(0, 60, 75, $from_number_of_days, $to_number_of_days);
        $OnlinePerformanceReportGreaterThanFourty = $this->mg_model->getChannelPerformanceByCriteria(0, 40, 60, $from_number_of_days, $to_number_of_days);
        $OnlinePerformanceReportLessThanFourty = $this->mg_model->getChannelPerformanceByCriteria(0, 0, 40, $from_number_of_days, $to_number_of_days);
        //exit;
        //$OnlinePerformanceReport = $this->mg_model->getChannelPerformance(0,$from_number_of_days,$to_number_of_days) ;
        $OfflinePerformanceReport = $this->mg_model->getChannelPerformance(1, $from_number_of_days, $to_number_of_days);
        $deployNotPinging = $this->mg_model->deployedAndNotPinging($from_number_of_days, $to_number_of_days);

        //echo $channelSummary."--".$onlineChannelSummary;exit;
        mail_send_v1($to_mail_id,
            $mail_key,
            array('TODAYS_DATE' => date('Y-m-d'), 'DURATION' => $duration),
            array(
                'CHANNELS_SUMMARY' => $channelSummary,
                'ONLINE_CHANNELS_SUMMARY' => $onlineChannelSummary,

                'ONLINE_CHANNEL_DATA_GREATER_THAN_95' => $OnlinePerformanceReportGreaterThanNinetyFive,
                'ONLINE_CHANNEL_DATA_GREATER_THAN_85' => $OnlinePerformanceReportGreaterThanEightyFive,
                'ONLINE_CHANNEL_DATA_GREATER_THAN_75' => $OnlinePerformanceReportGreaterThanSeventyFive,
                'ONLINE_CHANNEL_DATA_GREATER_THAN_60' => $OnlinePerformanceReportGreaterThanSixty,
                'ONLINE_CHANNEL_DATA_GREATER_THAN_40' => $OnlinePerformanceReportGreaterThanFourty,
                'ONLINE_CHANNEL_DATA_LESS_THAN_40' => $OnlinePerformanceReportLessThanFourty,

                //'ONLINE_CHANNEL_DATA' => $OnlinePerformanceReport,
                'OFFLINE_CHANNEL_DATA' => $OfflinePerformanceReport,
                'DEPLOYED_NOT_PINGING' => $deployNotPinging
            ),
            '',
            $cc_mail_id,
            '',
            ''
        );
    }

    public function getMailTemplateId($mail_type)
    {
        $mail_template = '';
        switch ($mail_type) {
            case 'submit_ro_approval':
                $mail_template = 'create_ext_ro';
                break;
            default:
                $mail_template = 'create_ext_ro';
        }
        return $mail_template;
    }

    public function generateChannelPerformance($givenDate = NULL, $userType = 0)
    {
        //$givenDate format will be Y-m-d format e.g. 2014-12-08
        if (!isset($userType)) {
            $userType = 0;
        }

        if (empty($givenDate) || !isset($givenDate)) {
            $givenDate = date('Y-m-d');
        }

        $this->mg_model->generateChannelPerformance($givenDate, $userType);
    }

    public function getChannelPerformance($userType = 0, $givenDate = NULL)
    {
        //$givenDate format will be Y-m-d format e.g. 2014-12-08
        //set_time_limit(0);
        if (!isset($userType)) {
            $userType = 0;
        }

        if (empty($givenDate) || !isset($givenDate)) {
            $givenDate = date('Y-m-d');
        }

        $this->mg_model->generateChannelPerformance($givenDate, $userType);

        //Mail Generation Logic
        $userData = $this->user_model->getUserValues(array('profile_id' => 4, 'is_test_user' => $userType));
        $emailIds = array();
        foreach ($userData as $val) {
            array_push($emailIds, $val['user_email']);
        }
        $staticEmails = $this->getStaticMails('channel_performance_daily');
        if (count($emailIds) > 0) {
            $user_email_ids = implode(",", $emailIds);
            $user_email_ids .= ',' . $staticEmails;
        } else {
            $user_email_ids = $staticEmails;
        }

        if (date("w") == 1) {
            $userManagementDetail = $this->user_model->get_user_management_detail($userType);
            $cc_email_id = array();
            foreach ($userManagementDetail as $val) {
                array_push($cc_email_id, $val['user_email']);
            }
            $cc_id_val = implode(",", $cc_email_id);
            $staticEmails = $this->getStaticMails('channel_performance_weekly');
            $cc_id_val = $cc_id_val . ',' . $staticEmails;
        } else {
            $cc_id_val = '';
        }

        $whereData = array('mail_sent' => 0, 'mail_type' => 'channel_performance');
        $mailData = $this->ro_model->getMailForRo($whereData);

        if (count($mailData) > 0) {
            $updateData = array('user_email_id' => $user_email_ids, 'cc_email_id' => $cc_id_val, 'mail_sent_date' => date('Y-m-d'), 'mail_sent' => 0);
            $this->ro_model->updateMailForRo($updateData, array('mail_type' => 'channel_performance'));
        } else {
            $insertData = array(
                'ro_id' => 0,
                'mail_type' => 'channel_performance',
                'mail_status' => 1,
                'approval_level' => 3,
                'user_email_id' => $user_email_ids,
                'cc_email_id' => $cc_id_val,
                'file_name' => 'None',
                'mail_sent_date' => date('Y-m-d'),
                'mail_sent' => 0
            );
            $this->ro_model->insertForMail($insertData);
        }

        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/MailSentForRo/channel_performance > /dev/null &");

        //      exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/generateMisReport > /dev/null &") ;


    }

    /** Functions for mail progression body */
    public function test_pdf()
    {
        $this->load->view('ro_manager/generate_pdf');
    }

    public function test_cron($val = 'Hello')
    {
        echo "Hello {$val}!" . PHP_EOL;
    }

    /**
     * @param $date
     * @param $isFct
     * @return string
     */
    private function buildRegionsTable($date)
    {

        $type = "";
        $requestArray = array(" date = " . "'$date'");

        $current_month_start_date = date("Y-m-01", strtotime($date));
        $current_month_end_date = date("Y-m-t", strtotime($date));

        $resultArray = $this->mg_model->selectFromMisRegionReport($requestArray, $type, $current_month_start_date, $current_month_end_date);

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Region</th></b>"
            . "<th><b>No of Ro's</th></b>"
            . "<th><b>Target</th></b>"
            . "<th><b>Total Revenue</th></b>"
            . "<th><b>% Achieved</th></b>"
            . "<th><b>Total Network Payout</th></b>"
            . "<th><b>Total Net Contribution</th></b>"
            . "<th><b>Total Net Contribution %</th></b>"
            . "</tr>";

        if (count($resultArray) > 0) {

            foreach ($resultArray as $val) {

                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . $val['region_name'] . "</td>";
                $structure = $structure . "<td>" . $val['ro_count'] . "</td>";
                $structure = $structure . "<td>" . $val['target'] . "</td>";
                $structure = $structure . "<td>" . $val['revenue'] . "</td>";
                $structure = $structure . "<td>" . $val['achieved'] . "</td>";
                $structure = $structure . "<td>" . $val['network_payout'] . "</td>";
                $structure = $structure . "<td>" . $val['net_contribution'] . "</td>";
                $structure = $structure . "<td>" . $val['net_contribution_percentage'] . "</td>";
                $structure = $structure . "</tr>";

            }

        } else {

            $structure = $structure . "<tr>";
            $structure = $structure . "<td>-</td>";
            $structure = $structure . "<td>0</td>";
            $structure = $structure . "<td>0</td>";
            $structure = $structure . "<td>0</td>";
            $structure = $structure . "<td>0</td>";
            $structure = $structure . "<td>0</td>";
            $structure = $structure . "<td>0</td>";
            $structure = $structure . "<td>0</td>";
            $structure = $structure . "</tr>";
        }

        $structure .= "</table>";

        return $structure;
    }
    public function testMail(){
	ini_set('display_errors', 1);
	 echo 'hi';
                $CI = &get_instance();
               $CI->load->library('email');
               $CI->email->clear(TRUE);
		echo "\n-1-";
		echo "\n-2-";
               $CI->email->from('biswabijayee@surewaves.com');
               $CI->email->to('biswa.bijayee.kiit@gmail.com');
		echo "\n-3-";
               $CI->email->subject('testing for config set');
		echo "\n-4-";
              $CI->email->message('Hey there its a test mail');
               if (!$CI->email->send()) {
                       echo "not sent";
                       echo $CI->email->print_debugger();
              }else{
                      echo 'sent';
               }
    }
}

?>
