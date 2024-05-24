<?php
/**
 * Created by PhpStorm.
 * Author: Yash
 * Date: 29/11/2019
 * Time: 19:15
 */

namespace application\services\feature_services;

use application\feature_dal\CreateExtRoFeature;
use application\services\common_services\EmailService;
use application\services\common_services\S3UploadService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;

include_once APPPATH . 'feature_dal/create_ext_ro_feature.php';
include_once APPPATH . 'services/common_services/s3_upload_service.php';
include_once APPPATH . 'services/common_services/email_service.php';


class UpdateExtRoService
{
    private $CI;
    private $createExtRoFeatureObj;
    private $s3Obj;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('session');
        $this->createExtRoFeatureObj = new CreateExtRoFeature();
        $this->s3Obj = new S3UploadService(Ro_Bucket);
    }

    /**
     * Author: Yash
     * Date: 29/11/2019
     *
     * @return array
     * Description: Responsible for updating Ro when it is in RO_CREATED status
     */
    public function UpdateExtRo()
    {
        log_message('INFO', 'In UpdateExtRoService@UpdateExtRo | Data from UI - ' . print_r($_POST, true));

        $custRo = $this->CI->input->post('txt_ext_ro');

        $roDetails = $this->createExtRoFeatureObj->getRoDetails($custRo);
        if (!isset($roDetails) || empty($roDetails)) {
            log_message('INFO', 'In UpdateExtRoService@UpdateExtRo | RO Does Not Exist');
            return array('Status' => 'fail', 'Message' => 'RO Does not Exist!', 'Data' => array());
        }
        $extRoId = $roDetails[0]['id'];
        $internalRoNo = $roDetails[0]['internal_ro'];

        $agencyDisplayName = $this->CI->input->post('txt_agency');
        $agency = $this->createExtRoFeatureObj->getAgencyName($agencyDisplayName);

        $clientDisplayName = $this->CI->input->post('txt_client');
        $client = $this->createExtRoFeatureObj->getClientName($clientDisplayName);

        $userId = $this->CI->input->post('hid_user_id');

        $region = $this->CI->input->post('regionSelectBox');

        // Taking markets excluding 0 amount
        $markets = json_decode($_POST['markets'], true);
        log_message('debug', 'In UpdateExtRoService@UpdateExtRo | Market Data - ' . print_r($markets, true));

        $market = '';
        foreach ($markets as $m) {
            if ($m['spotAmount'] != 0 || $m['bannerAmount'] != 0) {
                if ($market == '') {
                    $market = str_replace("_", " ", $m['marketName']);
                } else {
                    $market .= ',' . str_replace("_", " ", $m['marketName']);
                }
            }
        }

        $roDate = $this->CI->input->post('txt_ro_date');
        $campStartDate = $this->CI->input->post('txt_camp_start_date');
        $campEndDate = $this->CI->input->post('txt_camp_end_date');

        $gross = $this->CI->input->post('txt_gross');
        $agencyCom = $this->CI->input->post('txt_agency_com');
        $netAgencyCom = $this->CI->input->post('txt_net_agency_com');
        $spclInst = $this->CI->input->post('txt_spcl_inst');

        $ClientApprovalEdit = $this->CI->input->post('client_approval_edit');
        if ($ClientApprovalEdit == 1) {
            $clientApprovalEmail = $this->uploadClientApproveEmail($userId);
            if ($clientApprovalEmail == false) {
                log_message('INFO', 'In UpdateExtRoService@UpdateExtRo | Client Attachment not uploaded');
                return array('Status' => 'fail', 'Message' => 'Client Attachment Upload Failed!', 'Data' => array());
            }
        } else {
            $clientApprovalEmail = $this->CI->input->post('client_approval_mail');
        }

        $RoAttachEdit = $this->CI->input->post('ro_attach_edit');
        if ($RoAttachEdit == 1) {
            $RoFilePath = $this->fileUploadForRo($userId);
            if ($RoFilePath == false) {
                log_message('INFO', 'In UpdateExtRoService@UpdateExtRo | RO Attachment not uploaded. Rolling back database');
                return array('Status' => 'fail', 'Message' => 'RO Attachment Upload Failed!', 'Data' => array());
            }
        } else {
            $RoFilePath = $this->CI->input->post('file_pdf');
        }

        $makeGoodType = $this->CI->input->post('rd_make_good');
        $agencyContactId = $this->CI->input->post('sel_agency_contact');
        $clientContactId = $this->CI->input->post('sel_client_contact');
        if ($clientContactId == 'new') {
            $clientContactId = 0;
        }

        $mailList = $this->CI->input->post('Order_history_recipient_ids');
        $loggedIn = $this->CI->session->userdata("logged_in_user");
        $mailList = $loggedIn[0]['user_email'] . "," . $mailList;

        $updateRoData = array(
            'user_id' => $userId,
            'region_id' => $region,
            'market' => $market,
            'ro_date' => $roDate,
            'camp_start_date' => $campStartDate,
            'camp_end_date' => $campEndDate,
            'gross' => $gross,
            'agency_com' => $agencyCom,
            'net_agency_com' => $netAgencyCom,
            'spcl_inst' => $spclInst,
            'file_path' => $RoFilePath,
            'make_good_type' => $makeGoodType,
            'agency_contact_id' => $agencyContactId,
            'client_contact_id' => $clientContactId,
            'previous_ro_amount' => $gross,
            'order_history_mail_list' => $mailList,
            'client_approval_mail' => $clientApprovalEmail
        );
        try {
            DB::beginTransaction();
            log_message('DEBUG', 'In UpdateExtRoService@UpdateExtRo | Database transaction has begun, inserting data in ro_am_external_ro');
            $this->createExtRoFeatureObj->updateRoDetails($extRoId, $updateRoData);

            //updating ro amount in ro_amount
            $roAmountData = array(
                'ro_amount' => $gross,
                'agency_commission_amount' => $agencyCom,
                'timestamp' => date("Y-m-d H:i:s"),
                'approval_timestamp' => date("Y-m-d H:i:s"),
                'approval_user_id' => $userId
            );

            $this->createExtRoFeatureObj->updateRoAmount($internalRoNo, $roAmountData);

            $netContributionPercent = $this->getApproximateNetContribution($extRoId, $updateRoData);
            $this->createExtRoFeatureObj->updateNetContributionPercent($netContributionPercent, $extRoId);
            // ======================  //

            $pdfAttachmentParts = pathinfo($RoFilePath);
            $clientPdfBaseName = $pdfAttachmentParts['basename'];
            $fileActualPath = $_SERVER['DOCUMENT_ROOT'] . "surewaves_easy_ro/" . 'easy_ro_temp_pdf/' . $clientPdfBaseName;


            $emailIds = $this->userEmailForRoCreation($userId, $loggedIn[0]['is_test_user']);

            log_message('DEBUG', 'In UpdateExtRoService@UpdateExtRo | Preparing Email data');

            $whereMailData = array(
                'ro_id' => $extRoId,
                'mail_type' => 'submit_ro_approval',
            );
            $data = array(
                'file_name' => $fileActualPath,
                'mail_sent_date' => date('Y-m-d'),
                'mail_sent' => 1
            );

            $this->createExtRoFeatureObj->updateMailSentData($whereMailData, $data);

            $fileDocumentPath = $_SERVER['DOCUMENT_ROOT'];
            if (!isset($fileDocumentPath) || empty($fileDocumentPath)) {
                $fileDocumentPath = "/opt/lampp/htdocs/";
            }
            $actualPathLocation = $fileDocumentPath . "/surewaves_easy_ro/" . 'easy_ro_temp_pdf/';

            log_message('DEBUG', 'In UpdateExtRoService@UpdateExtRo | Preparing RO and CLIENT MAIL attachment file location');

            $mailAttachmentParts = pathinfo($clientApprovalEmail);
            $clientMailBaseName = $mailAttachmentParts['basename'];

            $allFiles = '';
            if($clientPdfBaseName != '' && !empty($clientPdfBaseName)){
                $allFiles = $actualPathLocation . $clientPdfBaseName;
                if($clientMailBaseName != '' && !empty($clientMailBaseName)) {
                    $allFiles = $allFiles . ',' . $actualPathLocation . $clientMailBaseName;
                }
            }else if($clientMailBaseName != '' && !empty($clientMailBaseName)){
                $allFiles = $actualPathLocation . $clientMailBaseName;
            }
            log_message('INFO', 'In UpdateExtRoService@UpdateExtRo | File location for RO and CLIENT APPROVED attachment prepared - ' . print_r($allFiles, true));

            $mailType = unserialize(MAIL_TYPE);
            $makeGoodType1 = unserialize(MAKE_GOOD_TYPE);
            log_message('DEBUG', 'In UpdateExtRoService@UpdateExtRo | Calculating email type and makeGood type - ' . print_r(array('emailType' => $mailType['CREATE_EXT_RO'], 'makeGoodType' => $makeGoodType1[$makeGoodType]), true));

            $emailObject = new EmailService($loggedIn[0]['user_email'], $emailIds);
            $mailSent = $emailObject->sendMail(
                $mailType['EDIT_EXT_RO'],
                array('EXTERNAL_RO' => $custRo),
                array('AM_NAME' => $loggedIn[0]['user_name'],
                    'EXTERNAL_RO' => $custRo,
                    'INTERNAL_RO' => $internalRoNo,
                    'AGENCY' => $agency,
                    'CLIENT' => $client,
                    'BRAND' => $this->CI->input->post('txt_brand'),
                    'MAKEGOOD_TYPE' => $makeGoodType1[$makeGoodType],
                    'MARKET' => $market,
                    'INSTRUCTION' => $spclInst,
                    'START_DATE' => $campStartDate,
                    'END_DATE' => $campEndDate
                ),
                $allFiles
            );
            if (!$mailSent) {
                log_message('INFO', 'Mail was not sent for RO - ' . $custRo);
                $this->createExtRoFeatureObj->updateMailSentData($whereMailData, array('mail_sent' => 0));
            }
            DB::commit();
            log_message('DEBUG', 'In UpdateExtRoService@UpdateExtRo | RO Updated Successfully !');
            return array('Status' => 'success', 'Message' => 'RO Updated successfully!', 'Data' => array());
        } catch (QueryException $e) {
            log_message('ERROR', 'In UpdateExtRoService@UpdateExtRo | Rolling Back : Database query exception occurred - ' . print_r($e->getMessage(), true));
            DB::rollBack();
            log_message('DEBUG', 'In UpdateExtRoService@UpdateExtRo | Database Rolled Back successfully!  Now Deleting email attachments.');

            return array('Status' => 'fail', 'Message' => 'Database exception occurred!', 'Data' => array());
        }
    }

    /**
     * Author: Yash
     * Date: 29/11/2019
     *
     * @param $userId
     * @return bool
     */
    public function uploadClientApproveEmail($userId)
    {
        log_message('DEBUG', 'In UpdateExtRoService@uploadClientApproveEmail | Uploading Mail Attachment');
        return ($this->uploadFile($userId, 'mail_attachment_', 'client_approval_mail'));
    }

    /**
     * Author: Yash
     * Date: 29/11/2019
     *
     * @param $userId
     * @return bool
     */
    public function fileUploadForRo($userId)
    {
        log_message('DEBUG', 'In UpdateExtRoService@fileUploadForRo | Uploading RO Attachment');
        return ($this->uploadFile($userId, 'Ro_attachment_', 'file_pdf'));
    }

    /**
     * Author: Yash
     * Date: 29/11/2019
     *
     * @param $userId
     * @param $uploadType
     * @param $fileIdentity
     * @return bool
     */
    public function uploadFile($userId, $uploadType, $fileIdentity)
    {
        log_message('DEBUG', 'In UpdateExtRoService@uploadFile | ');
        if (!is_dir('easy_ro_temp_pdf')) {
            mkdir('easy_ro_temp_pdf');
        }
        $fileName = str_replace(" ", "_", $_FILES[$fileIdentity]['name']);
        $fileNewName = $uploadType . $userId . "_" . date('Y_m_d_H_i_s') . "_" . $fileName;
        $_FILES[$fileIdentity]['name'] = $fileNewName;

        $config['upload_path'] = 'easy_ro_temp_pdf/';
        $config['allowed_types'] = '*';
        $this->CI->load->library('upload', $config);

        log_message('INFO', 'In UpdateExtRoService@uploadFile | Uploading - ' . $fileNewName . ' to server');
        if ($this->CI->upload->do_upload($fileIdentity) == False) {
            $error = array('error' => $this->CI->upload->display_errors());
            log_message('INFO', 'In UpdateExtRoService@uploadFile | Uploading to server FAILED, Loading create_ext_ro view with error message ' . $error);
            $this->CI->load->view('account_manager/create_ext_ro', $error);
        } else {
            $this->CI->upload->data();
            log_message('INFO', 'In UpdateExtRoService@uploadFile | Uploading to server successful, Now Uploading - ' . $fileNewName . ' to S3');
            return $this->uploadOntoS3($fileNewName);
        }
    }

    /**
     * Author: Yash
     * Date: 29/11/2019
     *
     * @param $fileName
     * @return bool
     */
    public function uploadOntoS3($fileName)
    {
        log_message('DEBUG', 'In UpdateExtRoService@uploadOntoS3 | ');

        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/surewaves_easy_ro/" . 'easy_ro_temp_pdf/' . $fileName;
        $status = $this->s3Obj->uploadFile($filePath, $fileName);

        if ($status == True) {
            log_message('INFO', 'In UpdateExtRoService@uploadOntoS3 | File Uploaded successfully');
            return $this->s3Obj->generateURL($fileName);
        } else {
            log_message('INFO', 'In UpdateExtRoService@uploadOntoS3 | File Upload failed');
            return false;
        }
    }

    public function getApprovalLevel($profileId)
    {
        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$profileId];
        $approvalLevel = $positionLevelForUser + 1;
        log_message("INFO", 'In UpdateExtRoService@getApprovalLevel | Approval level - ' . print_r($approvalLevel, true) . ' for profileId - ' . print_r($profileId, true));
        return $approvalLevel;
    }

    /**
     * @param $approvalData
     * @param $marketData
     * @param $roDetail
     * @return float
     */
    public function getApproximateNetContribution($roId, $roDetail)
    {
        log_message('INFO', 'In UpdateExtRoService @ getApproximateNetContribution | Entering with roId is - ' . print_r($roId, true));

        $marketData = json_decode($_POST['markets'], true);
        $totalMarketWiseNetContribuition = 0;
        foreach ($marketData as $mkt) {
            if ($mkt['spotAmount'] == 0 && $mkt['bannerAmount'] == 0) {
                continue;
            }
            $this->insertMarketRate($mkt, $roId); // Inserting Less Spot percentage and less banner percentage
            $marketWiseNetContribuition = $this->getMarketWiseNetContribuition($mkt); // Adding banner and spot
            $totalMarketWiseNetContribuition += $marketWiseNetContribuition;
        }

        $ro_amount = $roDetail['gross'];
        $agency_commission = $roDetail['agency_com'];

        $actual_net_amount = $ro_amount - $agency_commission;

        $netContribuition = $actual_net_amount - $totalMarketWiseNetContribuition;
        $netContribuitionPercent = round(($netContribuition / $actual_net_amount) * 100, 2);
        log_message('INFO', 'In UpdateExtRoService@getApproximateNetContribution | netContribuition is - ' . print_r($netContribuition, true));

        return $netContribuitionPercent;
    }

    /**
     * Author: Yash
     * Date: 30/11/2019
     *
     * @param $market
     * @param $ro_id
     */
    public function insertMarketRate($market, $ro_id)
    {
        log_message('INFO', 'In UpdateExtRoService @ insertMarketRate | Entering ');

        $marketName = str_replace("_", " ", $market['marketName']);
        $spotFct = $market['spotFCT'];
        $bannerFct = $market['bannerFCT'];
        $spotPrice = $market['spotAmount'];
        $bannerPrice = $market['bannerAmount'];

        if (!empty($spotFct)) {
            $spot_rate = ($spotPrice / $spotFct) * 10;
        }
        if (!empty($bannerFct)) {
            $banner_rate = ($bannerPrice / $bannerFct) * 10;
        }

        $marketData = $this->createExtRoFeatureObj->getMarketDataForMarketName(array(array('sw_market_name', $marketName)));

        $marketSpotRate = $marketData[0]['spot_rate'];
        $marketBannerRate = $marketData[0]['banner_rate'];

        if (!isset($marketSpotRate) || empty($marketSpotRate) || $marketSpotRate == 0) {
            $marketSpotRate = 1;
        }
        if (!isset($marketBannerRate) || empty($marketBannerRate) || $marketBannerRate == 0) {
            $marketBannerRate = 1;
        }

        if ($spotPrice == 0 || $spotFct == 0) {
            $spotPercentage = 0;
        } else {
            $spotPercentage = 100 - round(($spot_rate / $marketSpotRate) * 100, 2);
        }
        log_message('INFO', 'In UpdateExtRoService @ insertMarketRate | $spotPercentage is ' . print_r($spotPercentage, True));

        if ($bannerPrice == 0 || $bannerFct == 0) {
            $bannerPercentage = 0;
        } else {
            $bannerPercentage = 100 - round(($banner_rate / $marketBannerRate) * 100, 2);
        }
        log_message('INFO', 'In UpdateExtRoService @ insertMarketRate | $bannerPercentage is ' . print_r($bannerPercentage, True));

        //Update Market Price
        //less_spot_rate_percentage.
        $whereDataArray = array(array('ro_id', $ro_id), array('market', $marketName));
        $data = array(
            'less_spot_rate_percentage' => $spotPercentage,
            'less_banner_rate_percentage' => $bannerPercentage,
            'spot_price' => $spotPrice,
            'spot_fct' => $spotFct,
            'spot_rate' => $spot_rate,
            'banner_price' => $bannerPrice,
            'banner_fct' => $bannerFct,
            'banner_rate' => $banner_rate,
            'price' => ($spotPrice + $bannerPrice)
        );
        $result = $this->createExtRoFeatureObj->checkDataInRoMarketPrice($whereDataArray);
        if(count($result) == 0){
            $data['ro_id'] = $ro_id;
            $data['market'] = $marketName;
            $data['fixed_spot_price'] = $market['spotAmount'];
            $data['fixed_spot_fct'] = $market['spotFCT'];
            $data['fixed_banner_price'] = $market['bannerAmount'];
            $data['fixed_banner_fct'] = $market['bannerFCT'];
            $this->createExtRoFeatureObj->addMarkets($data);
        }
        else {
            $this->createExtRoFeatureObj->updateMarketPrice($whereDataArray, $data);
        }
    }

    /**
     * Author: Yash
     * Date: 30/11/2019
     * Date Modified: 06/12/2019
     *
     * @param $market
     * @return float|int
     */
    public function getMarketWiseNetContribuition($market)
    {
        log_message('INFO', 'In UpdateExtRoService @ getMarketWiseNetContribuition | Entering with Markets - ' . print_r($market, true));

        $marketName = str_replace("_", " ", $market['marketName']);
        $spotFct = $market['spotFCT'];
        $bannerFct = $market['bannerFCT'];

        $channelData = $this->createExtRoFeatureObj->getActiveChannelDetailForMarket($marketName);
        $totalChannel = count($channelData);
        log_message('INFO', 'In UpdateExtRoService @ getMarketWiseNetContribuition | $totalChannel is ' . print_r($totalChannel, True));

        if ($totalChannel > 0) {
            $channelContribuition = 0;
            foreach ($channelData as $value) {
                $spotRate = $value['spot_avg'];
                $bannerRate = $value['banner_avg'];
                $revenueShare = $value['revenue_sharing'];

                $spotContribuition = $spotFct * $spotRate * $revenueShare * (1 / 10) * (1 / 100);
                $bannerContribuition = $bannerFct * $bannerRate * $revenueShare * (1 / 10) * (1 / 100);
                $channelContribuition += $spotContribuition + $bannerContribuition;
            }
            $totalChannelContribuition = $channelContribuition;
            log_message('INFO', 'In UpdateExtRoService @ getMarketWiseNetContribuition | $totalChannelContribuition is ' . print_r($totalChannelContribuition, True));
            return $totalChannelContribuition;
        } else {
            log_message('INFO', 'In UpdateExtRoService @ getMarketWiseNetContribuition | Returning with 0 ');
            return 0;
        }
    }

    /**
     * Author: Yash
     * Date: 30/11/2019
     *
     * @param $userId
     * @param $userType
     * @return string
     */
    public function userEmailForRoCreation($userId, $userType)
    {
        log_message('DEBUG', 'In UpdateExtRoService@userEmailForRoCreation | ');

        $userDetail = $this->createExtRoFeatureObj->getUserDetailForUserId($userId, $userType);
        $userProfileID = $userDetail[0]['profile_id'];
        $userRegionId = $userDetail[0]['region_id'];

        log_message('DEBUG', 'In UpdateExtRoService@userEmailForRoCreation | Calculating User Region, profile approval level, next approval level etc. ');
        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$userProfileID];
        $nextPositionLevelValue = $positionLevelForUser + 1;

        $usersProfileIdsForNextPosition = $this->getUserProfileIdsForNextPosition($profileApprovalPosition, $nextPositionLevelValue);
        $userMailIds = $this->createExtRoFeatureObj->getUserDetailForProfileIdsAndRegion($usersProfileIdsForNextPosition, $userRegionId, $userType);
        $emailData = array();
        foreach ($userMailIds as $val) {
            array_push($emailData, $val['user_email']);
        }
        $emailVal = implode(",", $emailData);
        log_message('INFO', 'In UpdateExtRoService@userEmailForRoCreation | Email data - ' . print_r($emailVal, true));
        return $emailVal;
    }

    /**
     * Author: Yash
     * Date: 30/11/2019
     *
     * @param $profileApprovalPosition
     * @param $nextPositionLevelValue
     * @return array
     */
    public function getUserProfileIdsForNextPosition($profileApprovalPosition, $nextPositionLevelValue)
    {
        log_message('INFO', 'In UpdateExtRoService@getUserProfileIdsForNextPosition | Calculating user profile ids for next position for - ' . print_r(array('profileApprovalPosition' => $profileApprovalPosition, 'nextPositionLevelValue' => $nextPositionLevelValue), true));
        $profileIds = array();
        foreach ($profileApprovalPosition as $key => $val) {
            if ($val == $nextPositionLevelValue) {
                array_push($profileIds, $key);
            }
        }
        log_message('INFO', 'In UpdateExtRoService@getUserProfileIdsForNextPosition | User profile for next position - ' . print_r($profileIds, true));
        return $profileIds;
    }
}