<?php
/**
 * Created by PhpStorm.
 * Author: Yash | Ravishankar
 * Date: September,2019
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

class CreateExtRoService
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
     * Author: Yash | Ravishankar
     * Date: September,2019
     *
     * @return array|int
     */
    public function createExtRo()
    {

        log_message('DEBUG', 'In CreateExtRoService@CreateExtRo | Entered function after pressing \'CREATE\' button in UI');
        log_message('INFO', 'In CreateExtRoService@CreateExtRo | Data from UI - ' . print_r($_POST, true));

        $custRo = $this->CI->input->post('txt_ext_ro');

        $result = $this->createExtRoFeatureObj->getRoDetails($custRo);
        if (isset($result) || !empty($result)) {
            log_message('INFO', 'In CreateExtRoService@CreateExtRo | RO already exists.');
            return array('Status' => 'fail', 'Message' => 'Ro Already Exists!', 'Data' => array());
        }

        $agencyDisplayName = $this->CI->input->post('sel_agency');
        $agency = $this->createExtRoFeatureObj->getAgencyName($agencyDisplayName);
        $clientDisplayName = $this->CI->input->post('sel_client');
        $client = $this->createExtRoFeatureObj->getClientName($clientDisplayName);
        $brand = $this->CI->input->post('hid_brand');
        $industry = '';
        $userId = $this->CI->input->post('hid_user_id');
        $isTestUser = $this->CI->input->post('user_type');
        $vertical = '';
        $region = $this->CI->input->post('regionSelectBox');

        // Taking markets excluding 0 amount
        $markets = json_decode($_POST['markets'], true);
        log_message('debug', 'In CreateExtRoService@createExtRo | Market Data - ' . print_r($markets, true));

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
        $booking = '';
        $category = '';
        $gross = $this->CI->input->post('txt_gross');
        $agencyCom = $this->CI->input->post('txt_agency_com');
        $netAgencyCom = $this->CI->input->post('txt_net_agency_com');
        $spclInst = $this->CI->input->post('txt_spcl_inst');

        $clientApprovalEmail = $this->uploadClientApproveEmail($userId);
        if ($clientApprovalEmail == false) {
            log_message('INFO', 'In CreateExtRoService@CreateExtRo | Client Attachment not uploaded');
            return array('Status' => 'fail', 'Message' => 'Client Attachment Upload Failed!', 'Data' => array());
        }

        $RoFilePath = $this->fileUploadForRo($userId);
        if ($RoFilePath == false) {
            log_message('INFO', 'In CreateExtRoService@CreateExtRo | RO Attachment not uploaded. Rolling back database');
            return array('Status' => 'fail', 'Message' => 'RO Attachment Upload Failed!', 'Data' => array());
        }

        $runningNo = $this->getFinancialRunningNumber($roDate, $isTestUser);
        $runningNo = $runningNo + 1;
        $dbRunningNo = sprintf('%04d', $runningNo);
        $financialYear = $this->getFinancialYearForRoDate($roDate);
        $month = date("m", strtotime($roDate));
        if ($month <= 3) {
            $financialYear1 = date("y", strtotime($roDate)) - 1;
        } else {
            $financialYear1 = date("y", strtotime($roDate));
        }
        $runningNo = $financialYear1 . '-' . $dbRunningNo;
        $internalRoNo = $this->generateInternalRoNumber($client, $agency, $custRo, $campStartDate, $runningNo);
        $makeGoodType = $this->CI->input->post('rd_make_good');
        $agencyContactId = $this->CI->input->post('sel_agency_contact');
        $clientContactId = $this->CI->input->post('sel_client_contact');
        if ($clientContactId == 'new') {
            $clientContactId = 0;
        }

        $mailList = $this->CI->input->post('Order_history_recipient_ids');
        $loggedIn = $this->CI->session->userdata("logged_in_user");
        $mailList = $loggedIn[0]['user_email'] . "," . $mailList;

        log_message('DEBUG', 'In CreateExtRoService@CreateExtRo | Preparing data to insert in ro_am_external_ro');
        $roData = array(
            'cust_ro' => $custRo,
            'internal_ro' => $internalRoNo,
            'agency' => $agency,
            'client' => $client,
            'brand' => $brand,
            'industry' => $industry,
            'user_id' => $userId,
            'vertical' => $vertical,
            'region_id' => $region,
            'market' => $market,
            'ro_date' => $roDate,
            'camp_start_date' => $campStartDate,
            'camp_end_date' => $campEndDate,
            'booking' => $booking,
            'category' => $category,
            'gross' => $gross,
            'agency_com' => $agencyCom,
            'net_agency_com' => $netAgencyCom,
            'spcl_inst' => $spclInst,
            'file_path' => $RoFilePath,
            'make_good_type' => $makeGoodType,
            'agency_contact_id' => $agencyContactId,
            'client_contact_id' => $clientContactId,
            'previous_ro_amount' => $gross,
            'financial_year_running_no' => $dbRunningNo,
            'financial_year' => $financialYear,
            'test_user_creation' => $isTestUser,
            'order_history_mail_list' => $mailList,
            'client_approval_mail' => $clientApprovalEmail
        );
        try {
            DB::beginTransaction();
            log_message('DEBUG', 'In CreateExtRoService@createExtRo | Database transaction has begun, inserting data in ro_am_external_ro');
            $lastROInsertedId = $this->createExtRoFeatureObj->insertRo($roData);
            log_message('INFO', 'In CreateExtRoService@createExtRo | Ro Record added successfully, last insertId - ' . print_r($lastROInsertedId, true));


            log_message('INFO', 'In CreateExtRoService@createExtRo | Updating ro_status for ro_id - ' . print_r($lastROInsertedId, true));
            $this->updateRoStatus($lastROInsertedId, 'RO_Created');

            // activate advertiser if non active
            log_message('INFO', 'In CreateExtRoService@createExtRo | Updating sv_new_advertiser for client - ' . print_r($client, true));
            $this->createExtRoFeatureObj->updateNewAdv($client);

            //updating ro amount in ro_amount
            $roAmountData = array(
                'customer_ro_number' => $custRo,
                'internal_ro_number' => $internalRoNo,
                'ro_amount' => $gross,
                'agency_commission_amount' => $agencyCom,
                'timestamp' => date("Y-m-d H:i:s"),
                'approval_timestamp' => date("Y-m-d H:i:s"),
                'approval_user_id' => $userId
            );

            $this->createExtRoFeatureObj->addRoAmount($roAmountData);
            $marketData = $this->addRoAmountForEachMarket($lastROInsertedId);
            log_message('INFO','In CreateExtRoService@createExtRo | Market Data is - '.print_r($marketData,true));

            //Adding in Approval Flow
            $userType = $loggedIn[0]['is_test_user'];

            $reportingManagerDetails = $this->createExtRoFeatureObj->getUserDetailOfUserReportingManager($userId, $userType);
            $mailReportingManagerDetails = $reportingManagerDetails;
            if ($reportingManagerDetails[0]['profile_id'] == 12) {
                $reportingManagerDetails = $this->createExtRoFeatureObj->getUserDetailOfUserReportingManager($reportingManagerDetails[0]['user_id'], $userType);
            }
            if ($reportingManagerDetails[0]['profile_id'] == 10) {
                $approvalLevel = 2;
            } else {
                $approvalLevel = $this->getApprovalLevel($loggedIn[0]['profile_id']);
            }

            $approvalData = array(
                'ext_ro_id' => $lastROInsertedId,
                'cancel_type' => 'submit_ro_approval',
                'user_id' => $userId,
                'date_of_submission' => date('Y-m-d H:i:s'),
                'date_of_cancel' => date('Y-m-d'),
                'reason' => 'None',
                'invoice_instruction' => 'Submitted By AM/GM',
                'ro_amount' => 0,
                'cancel_ro_by_admin' => 0,
                'bh_reason' => '',
                'approval_level' => $approvalLevel
            );
            log_message('INFO', 'In CreateExtRoService@createExtRo | Prepared approval data - ' . print_r($approvalData, true));
            $whereApprovalData = array(array('ext_ro_id', $lastROInsertedId), array('cancel_type', 'submit_ro_approval'));

            $this->createExtRoFeatureObj->insertForPendingStatus($whereApprovalData, $approvalData);

            // =====================REMOVED CRON EXECUTION PART 1===================== //
            $netContributionPercent = $this->getApproximateNetContribution($approvalData, $marketData, $roData);
            $this->createExtRoFeatureObj->updateNetContributionPercent($netContributionPercent, $lastROInsertedId);

            $pdfAttachmentParts = pathinfo($RoFilePath);
            $clientPdfBaseName = $pdfAttachmentParts['basename'];
            $fileActualPath = $_SERVER['DOCUMENT_ROOT'] . "surewaves_easy_ro/" . 'easy_ro_temp_pdf/' . $clientPdfBaseName;

            $userData = array(
                'ro_id' => $lastROInsertedId,
                'submit_status' => 'created'
            );

            $this->createExtRoFeatureObj->insertIntoProgressionMailStatus($userData);

            //record RO creation history details
            $this->roCreationHistory($loggedIn[0]['user_id'], $lastROInsertedId, 1);

            //Mail Data For Ro
            if ($mailReportingManagerDetails[0]['profile_id'] == 10) {
                $approvalLevel = 2;
            } else {
                $approvalLevel = $this->getApprovalLevel($loggedIn[0]['profile_id']);
            }

            log_message('DEBUG', 'In CreateExtRoService@createExtRo | Proceeding towards emailing');
            $emailData = $this->userEmailForRoCreation($userId, $userType);
//            $staticEmails = $this->featureObj->getStaticMails('post_ro_creation');

            log_message('DEBUG', 'In CreateExtRoService@createExtRo | Preparing Email data');
//            $ccEmailId = $emailData . "," . $mailReportingManagerDetails[0]['user_email'] . "," . $staticEmails;
            $ccEmailId = $emailData . "," . $mailReportingManagerDetails[0]['user_email'];
            $data = array(
                'ro_id' => $lastROInsertedId,
                'mail_type' => 'submit_ro_approval',
                'mail_status' => 0,
                'approval_level' => $approvalLevel,
                'user_email_id' => $loggedIn[0]['user_email'],
                'cc_email_id' => $ccEmailId,
                'file_name' => $fileActualPath,
                'mail_sent_date' => date('Y-m-d'),
                'mail_sent' => 1
            );

            $lastMailInsertedId = $this->createExtRoFeatureObj->insertMailData($data);
            log_message('INFO', 'In CreateExtRoService@createExtRo | Email record inserted successfully with last insert id - ' . print_r($lastMailInsertedId, true));
            // ===================== REMOVED CRON EXECUTION PART 2 <MAIL> ===================== //

            $fileDocumentPath = $_SERVER['DOCUMENT_ROOT'];
            if (!isset($fileDocumentPath) || empty($fileDocumentPath)) {
                $fileDocumentPath = "/opt/lampp/htdocs/";
            }
            $actualPathLocation = $fileDocumentPath . "/surewaves_easy_ro/" . 'easy_ro_temp_pdf/';

            log_message('DEBUG', 'In CreateExtRoService@createExtRo | Preparing RO and CLIENT MAIL attachment file location');

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
            log_message('INFO', 'In CreateExtRoService@createExtRo | File location for RO and CLIENT APPROVED attachment prepared - ' . print_r($allFiles, true));

            $brandNames = $this->createExtRoFeatureObj->getBrandNames($brand);
            $mailType = unserialize(MAIL_TYPE);
            $makeGoodType1 = unserialize(MAKE_GOOD_TYPE);
            log_message('DEBUG', 'In CreateExtRoService@createExtRo | Calculating email type and makeGood type - ' . print_r(array('emailType' => $mailType['CREATE_EXT_RO'], 'makeGoodType' => $makeGoodType1[$makeGoodType]), true));

            $emailObject = new EmailService($loggedIn[0]['user_email'], $ccEmailId);
            $mailSent = $emailObject->sendMail(
                $mailType['CREATE_EXT_RO'],
                array('EXTERNAL_RO' => $custRo),
                array('AM_NAME' => $loggedIn[0]['user_name'],
                    'EXTERNAL_RO' => $custRo,
                    'INTERNAL_RO' => $internalRoNo,
                    'AGENCY' => $agency,
                    'CLIENT' => $client,
                    'BRAND' => $brandNames,
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
                $this->createExtRoFeatureObj->updateMailData($lastMailInsertedId, array('mail_sent' => 0));
            }
            DB::commit();
            log_message('DEBUG', 'In CreateExtRoService@createExtRo | RO created Successfully !');
            return array('Status' => 'success', 'Message' => 'RO CREATED successfully!', 'Data' => array());
        } catch (QueryException $e) {
            log_message('ERROR', 'In CreateExtRoService@createExtRo | Rolling Back : Database query exception occurred - ' . print_r($e->getMessage(), true));
            DB::rollBack();
            log_message('DEBUG', 'In CreateExtRoService@createExtRo | Database Rolled Back successfully!  Now Deleting email attachments.');

            $this->s3Obj->deleteFile(pathinfo($clientApprovalEmail)['basename']);
            $this->s3Obj->deleteFile(pathinfo($RoFilePath)['basename']);

            return array('Status' => 'fail', 'Message' => 'Database exception occurred!', 'Data' => array());
        }
    }

    //===============================================================================================================================//
    //=========================================== HELPER FUNCTIONS FOR THIS SERVICE =================================================//
    //===============================================================================================================================//

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $userId
     * @return bool
     */
    public function uploadClientApproveEmail($userId)
    {
        log_message('DEBUG', 'In CreateExtRoService@uploadClientApproveEmail | Uploading Mail Attachment');
        return ($this->uploadFile($userId, 'mail_attachment_', 'client_aproval_mail'));
    }

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $userId
     * @return bool
     */
    public function fileUploadForRo($userId)
    {
        log_message('DEBUG', 'In CreateExtRoService@fileUploadForRo | Uploading RO Attachment');
        return ($this->uploadFile($userId, 'Ro_attachment_', 'file_pdf'));
    }

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $userId
     * @param $uploadType
     * @param $fileIdentity
     * @return bool
     */
    public function uploadFile($userId, $uploadType, $fileIdentity)
    {
        log_message('DEBUG', 'In CreateExtRoService@uploadFile | ');
        if (!is_dir('easy_ro_temp_pdf')) {
            mkdir('easy_ro_temp_pdf');
        }
        $fileName = str_replace(" ", "_", $_FILES[$fileIdentity]['name']);
        $fileNewName = $uploadType . $userId . "_" . date('Y_m_d_H_i_s') . "_" . $fileName;
        $_FILES[$fileIdentity]['name'] = $fileNewName;

        $config['upload_path'] = 'easy_ro_temp_pdf/';
        $config['allowed_types'] = '*';
        $this->CI->load->library('upload', $config);

        log_message('INFO', 'In CreateExtRoService@uploadFile | Uploading - ' . $fileNewName . ' to server');
        if ($this->CI->upload->do_upload($fileIdentity) == False) {
            $error = array('error' => $this->CI->upload->display_errors());
            log_message('INFO', 'In CreateExtRoService@uploadFile | Uploading to server FAILED, Loading create_ext_ro view with error message ' . $error);
            $this->CI->load->view('account_manager/create_ext_ro', $error);
        } else {
            $this->CI->upload->data();
            log_message('INFO', 'In CreateExtRoService@uploadFile | Uploading to server successful, Now Uploading - ' . $fileNewName . ' to S3');
            return $this->uploadOntoS3($fileNewName);
        }
    }

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $fileName
     * @return bool
     */
    public function uploadOntoS3($fileName)
    {
        log_message('DEBUG', 'In CreateExtRoService@uploadOntoS3 | ');

        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/surewaves_easy_ro/" . 'easy_ro_temp_pdf/' . $fileName;
        $status = $this->s3Obj->uploadFile($filePath, $fileName);

        if ($status == True) {
            log_message('INFO', 'In CreateExtRoService@uploadOntoS3 | File Uploaded successfully');
            return $this->s3Obj->generateURL($fileName);
        } else {
            log_message('INFO', 'In CreateExtRoService@uploadOntoS3 | File Upload failed');
            return false;
        }
    }

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $roDate
     * @param $isTestUser
     * @return int|mixed
     */
    public function getFinancialRunningNumber($roDate, $isTestUser)
    {
        log_message('DEBUG', 'In CreateExtRoService@getFinancialRunningNumber | ');
        $month = date("m", strtotime($roDate));
        if ($month <= 3) {
            $financialYear = date("Y", strtotime($roDate)) - 1;
        } else {
            $financialYear = date("Y", strtotime($roDate));
        }
        $year = $this->createExtRoFeatureObj->getFinancialRunningNumber($financialYear, $isTestUser);
        return $year;
    }

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $ro_date
     * @return false|int|string
     */
    public function getFinancialYearForRoDate($ro_date)
    {
        $month = date("m", strtotime($ro_date));
        if ($month <= 3) {
            $financial_year = date("Y", strtotime($ro_date)) - 1;
        } else {
            $financial_year = date("Y", strtotime($ro_date));
        }
        log_message('INFO', 'In CreateExtRoService@getFinancialYearForRoDate | Financial Year for RO date is - ' . $financial_year);
        return $financial_year;
    }

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $clientName
     * @param $agencyName
     * @param $croNumber
     * @param $campStartDate
     * @param $runningNo
     * @return int|string|null
     */
    public function generateInternalRoNumber($clientName, $agencyName, $croNumber, $campStartDate, $runningNo)
    {
        log_message('DEBUG', 'In CreateExtRoService@generateInternalRoNumber | Generating Internal RO number for - ' . print_r(array($clientName, $agencyName, $croNumber, $campStartDate, $runningNo), true));
        $roStartDate = date('M-Y', strtotime($campStartDate));

        // Internal RO number nomenclature SW/<ClientName>/<AgencyName>/<month>/<year yyyy>-<ro-number>
        $roPrefixDb = $clientName . '/' . $agencyName . '/' . $roStartDate . '-';
        $roPrefix = 'SW' . '/' . $runningNo . '/' . $roPrefixDb;
        $orderNumber = 1;

        $internalRoNumber = $this->createExtRoFeatureObj->getInternalRoNumber($croNumber, $roPrefix);
        if ($internalRoNumber != 0) {
            return $internalRoNumber;
        }

        log_message('INFO', 'In CreateExtRoService@generateInternalRoNumber | Internal Ro Number = 0');
        $internalRoNumber = $this->createExtRoFeatureObj->getDistinctInternalRoNumber($roPrefixDb);
        if (isset($internalRoNumber) && !empty($internalRoNumber)) {
            $len = strlen($roPrefix);
            $snum = substr($internalRoNumber, $len); // return string from $len index
            $orderNumber = (int)$snum + 1;
        }

        $orderNumber = sprintf('%03d', $orderNumber);
        $internalRoNumber = $roPrefix . $orderNumber;
        log_message('INFO', 'In CreateExtRoService@generateInternalRoNumber | Internal Ro Number - ' . print_r($internalRoNumber, true));
        return $internalRoNumber;
    }

    /**
     * Author: Yash | Ravishankar
     * Date: September,2019
     *
     * @param $amExternalRoId
     * @param $status
     */
    public function updateRoStatus($amExternalRoId, $status)
    {
        log_message('DEBUG', 'In CreateExtRoService@updateRoStatus | ');

        $this->createExtRoFeatureObj->updateDataForRoIdInRoStatus($amExternalRoId, $status);
    }

    /**
     * Author: Ravishankar
     * Date: September,2019
     * Date Modified: October,2019
     *
     * @param $roId
     * @return array
     */
    public function addRoAmountForEachMarket($roId)
    {
        log_message('DEBUG', 'In CreateExtRoService@addRoAmountForEachMarket | Adding roAmount for each market');

        $markets = json_decode($_POST['markets'], true);
        $retData = array();
        foreach ($markets as $m) {
            if ($m['spotAmount'] != 0 || $m['bannerAmount'] != 0) {
                $data = array(
                    'ro_id' => $roId,
                    'market' => str_replace("_", " ", $m['marketName']),
                    'price' => ($m['spotAmount'] + $m['bannerAmount']),
                    'spot_price' => $m['spotAmount'],
                    'spot_fct' => $m['spotFCT'],
                    'spot_rate' => $m['spotRate'],
                    'banner_price' => $m['bannerAmount'],
                    'banner_fct' => $m['bannerFCT'],
                    'banner_rate' => $m['bannerRate'],
                    'fixed_spot_price' => $m['spotAmount'],
                    'fixed_spot_fct' => $m['spotFCT'],
                    'fixed_banner_price' => $m['bannerAmount'],
                    'fixed_banner_fct' => $m['bannerFCT']
                );
                array_push($retData, $data);
            }
        }
        $this->createExtRoFeatureObj->addMarkets($retData);
        return $retData;
    }

    /**
     * Author: Yash
     * Date: September,2019
     * Date Modified: October,2019
     *
     * @param $profileId
     * @return int
     */
    public function getApprovalLevel($profileId)
    {
        $profileApprovalPosition = unserialize(PROFILE_APPROVAL_POSITION);
        $positionLevelForUser = $profileApprovalPosition[$profileId];
        $approvalLevel = $positionLevelForUser + 1;
        log_message("INFO", 'In CreateExtRoService@getApprovalLevel | Approval level - ' . print_r($approvalLevel, true) . ' for profileId - ' . print_r($profileId, true));
        return $approvalLevel;
    }

    /**
     * @param $userId
     * @param $roId
     * @param $isFct
     */
    public function roCreationHistory($userId, $roId, $isFct)
    {
        log_message('DEBUG', 'In CreateExtRoService@roCreationHistory | ');

        $resultArray = $this->createExtRoFeatureObj->getUserHierarchy($userId);
        log_message('INFO', 'In CreateExtRoService@roCreationHistory | User hierarchy fetched successfully from ro_user ' . print_r($resultArray, true));

        $userMap = array();
        if (count($resultArray) > 0) {
            $userMap[$resultArray[0]['lev1profile_id']] = $resultArray[0]['lev1userid'];
            $userMap[$resultArray[0]['lev2profile_id']] = $resultArray[0]['lev2userid'];
            $userMap[$resultArray[0]['lev3profile_id']] = $resultArray[0]['lev3userid'];
            $userMap[$resultArray[0]['lev4profile_id']] = $resultArray[0]['lev4userid'];
            $userMap[$resultArray[0]['lev5profile_id']] = $resultArray[0]['lev5userid'];
        }

        log_message('DEBUG', 'In CreateExtRoService@roCreationHistory | Preparing data for RO creation history');
        //insert into table to record RO creation history
        $data = array('external_ro_id' => $roId, 'is_fct' => $isFct);
        if (isset($userMap[6]) && !empty($userMap[6])) {
            $data['account_manager'] = $userMap[6];
        }
        if (isset($userMap[12]) && !empty($userMap[12])) {
            $data['group_manager'] = $userMap[12];
        }
        if (isset($userMap[11]) && !empty($userMap[11])) {
            $data['regional_director'] = $userMap[11];
        }
        if (isset($userMap[10]) && !empty($userMap[10])) {
            $data['national_head'] = $userMap[10];
        }
        if (isset($userMap[1]) && !empty($userMap[1])) {
            $data['business_head'] = $userMap[1];
        }
        $this->createExtRoFeatureObj->storeHistoryForRoCreation($data);
    }

    //Use for it creation and forward

    /**
     * @param $userId
     * @param $userType
     * @return string
     */
    public function userEmailForRoCreation($userId, $userType)
    {
        log_message('DEBUG', 'In CreateExtRoService@userEmailForRoCreation | ');

        $userDetail = $this->createExtRoFeatureObj->getUserDetailForUserId($userId, $userType);
        $userProfileID = $userDetail[0]['profile_id'];
        $userRegionId = $userDetail[0]['region_id'];

        log_message('DEBUG', 'In CreateExtRoService@userEmailForRoCreation | Calculating User Region, profile approval level, next approval level etc. ');
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
        log_message('INFO', 'In CreateExtRoService@userEmailForRoCreation | Email data - ' . print_r($emailVal, true));
        return $emailVal;
    }

    /**
     * @param $profileApprovalPosition
     * @param $nextPositionLevelValue
     * @return array
     */
    public function getUserProfileIdsForNextPosition($profileApprovalPosition, $nextPositionLevelValue)
    {
        log_message('INFO', 'In CreateExtRoService@getUserProfileIdsForNextPosition | Calculating user profile ids for next position for - ' . print_r(array('profileApprovalPosition' => $profileApprovalPosition, 'nextPositionLevelValue' => $nextPositionLevelValue), true));
        $profileIds = array();
        foreach ($profileApprovalPosition as $key => $val) {
            if ($val == $nextPositionLevelValue) {
                array_push($profileIds, $key);
            }
        }
        log_message('INFO', 'In CreateExtRoService@getUserProfileIdsForNextPosition | User profile for next position - ' . print_r($profileIds, true));
        return $profileIds;
    }
    //=============================================================//

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $approvalData
     * @param $marketData
     * @param $roDetail
     * @return float
     */
    public function getApproximateNetContribution($approvalData, $marketData, $roDetail)
    {
        $value = $approvalData;
        $roId = $value['ext_ro_id'];

        log_message('INFO', 'In CreateExtRoService @ getApproximateNetContribution | Entering with roId is - ' . print_r($roId, true));


        $totalMarketWiseNetContribuition = 0;
        foreach ($marketData as $mkt) {
            $this->insertMarketRate($mkt, $roId); // Inserting Less Spot percentage and less banner percentage
            $marketWiseNetContribuition = $this->getMarketWiseNetContribuition($mkt); // Adding banner and spot
            $totalMarketWiseNetContribuition += $marketWiseNetContribuition;
        }

        $ro_amount = $roDetail['gross'];
        $agency_commission = $roDetail['agency_com'];

        $actual_net_amount = $ro_amount - $agency_commission;

        $netContribuition = $actual_net_amount - $totalMarketWiseNetContribuition;
        $netContribuitionPercent = round(($netContribuition / $actual_net_amount) * 100, 2);
        log_message('INFO', 'In CreateExtRoService@getApproximateNetContribution | netContribuition is - ' . print_r($netContribuition, true));

        return $netContribuitionPercent;
    }

    /**
     * Author: Yash
     * Date: September,2019
     *
     * @param $market
     * @param $ro_id
     */
    public function insertMarketRate($market, $ro_id)
    {
        log_message('INFO', 'In CreateExtRoService @ insertMarketRate | Entering ');


        $marketName = $market['market'];
        $spotFct = $market['spot_fct'];
        $bannerFct = $market['banner_fct'];
        $spotPrice = $market['spot_price'];
        $bannerPrice = $market['banner_price'];

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
        log_message('INFO', 'In CreateExtRoService @ insertMarketRate | $spotPercentage is ' . print_r($spotPercentage, True));

        if ($bannerPrice == 0 || $bannerFct == 0) {
            $bannerPercentage = 0;
        } else {
            $bannerPercentage = 100 - round(($banner_rate / $marketBannerRate) * 100, 2);
        }
        log_message('INFO', 'In CreateExtRoService @ insertMarketRate | $bannerPercentage is ' . print_r($bannerPercentage, True));

        //Update Market Price
        //less_spot_rate_percentage.
        $whereDataArray = array(array('ro_id', $ro_id), array('market', $marketName));
        $data = array('less_spot_rate_percentage' => $spotPercentage, 'less_banner_rate_percentage' => $bannerPercentage);
        $this->createExtRoFeatureObj->updateMarketPrice($whereDataArray, $data);
    }

    /**
     * @param $market
     * @return float|int
     */
    public function getMarketWiseNetContribuition($market)
    {
        log_message('INFO', 'In CreateExtRoService @ getMarketWiseNetContribuition | Entering with Markets - ' . print_r($market, true));

        $marketName = $market['market'];
        $spotFct = $market['spot_fct'];
        $bannerFct = $market['banner_fct'];

        $channelData = $this->createExtRoFeatureObj->getActiveChannelDetailForMarket($marketName);
        $totalChannel = count($channelData);
        log_message('INFO', 'In CreateExtRoService @ getMarketWiseNetContribuition | $totalChannel is ' . print_r($totalChannel, True));

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
            log_message('INFO', 'In CreateExtRoService @ getMarketWiseNetContribuition | $totalChannelContribuition is ' . print_r($totalChannelContribuition, True));
            return $totalChannelContribuition;
        } else {
            log_message('INFO', 'In CreateExtRoService @ getMarketWiseNetContribuition | Returning with 0 ');
            return 0;
        }
    }

    /**
     * Author: Yash
     * Date: October,2019
     *
     * @return array
     */
    public function addEditAgencyContact()
    {
        $model = array();
        $model['cust_ro'] = trim($this->CI->input->post('txt_ext_ro'));
        $model['agency_display'] = $this->CI->input->post('sel_agency');
        $model['ro_date'] = $this->CI->input->post('txt_ro_date');
        $model['agency_contact'] = $this->CI->input->post('sel_agency_contact');
        $model['client_display'] = $this->CI->input->post('sel_client');
        $model['client_contact'] = $this->CI->input->post('sel_client_contact');

        $model['all_states'] = $this->createExtRoFeatureObj->getAllStates();
        if ($model['agency_contact'] != 'new') {
            $fetchAgency = $this->createExtRoFeatureObj->getAgencyContactInfo($model['agency_contact']);
            $model['agency_contact_name'] = $fetchAgency['agency_contact_name'];
            $model['agency_contact_no'] = $fetchAgency['agency_contact_no'];
            $model['agency_address'] = $fetchAgency['agency_address'];
            $model['agency_email'] = $fetchAgency['agency_email'];
        } else {
            $fetchAgency = $this->createExtRoFeatureObj->getAgencyDetails($model['agency_display']);
            $model['agency_address'] = $fetchAgency['agency_address'];
        }
        return $model;
    }

    /**
     * Author: Yash
     * Date: October,2019
     *
     * @param $operation
     * @return array
     */
    public function postAddEditAgencyContact($operation)
    {
        $agency_dis_name = $this->CI->input->post('txt_agency_name');
        log_message('info', 'In CreateExtRoService @ postAddEditAgencyContact | AgencyDisplayName -> '.print_r($agency_dis_name,True));
        $agency_contact_name = $this->CI->input->post('txt_agency_contact_name');
        $agency_contact_no = $this->CI->input->post('txt_agency_contact_no');
        $agency_email = $this->CI->input->post('txt_agency_email');
        $agency_state = $this->CI->input->post('agency_state');

        $agencyDetails = $this->createExtRoFeatureObj->getAgencyDetails($agency_dis_name);
        log_message('info', 'In CreateExtRoService @ postAddEditAgencyContact | AgencyDetails -> '.print_r($agencyDetails,True));
        $agency_name = $agencyDetails['sv_new_agency']['agency_name'];
        $billing_info = $agencyDetails['billing_info'];
        $billing_address = $agencyDetails['billing_address'];
        $billing_cycle = $agencyDetails['billing_cycle'];
        $agency_address = $agencyDetails['agency_address'];

        $data = array(
            'billing_info' => $billing_info,
            'billing_address' => $billing_address,
            'billing_cylce' => $billing_cycle,
            'agency_contact_name' => $agency_contact_name,
            'agency_contact_no' => $agency_contact_no,
            'agency_address' => $agency_address,
            'agency_email' => $agency_email,
            'agency_state' => $agency_state
        );

        if ($operation == 'add') {
            $result = $this->createExtRoFeatureObj->checkAgencyInRoAgency($agency_name, $agency_contact_name, $agency_email);
            log_message('info', 'In CreateExtRoService @ postAddEditAgencyContact | Result -> '.print_r($result,True));
            if (count($result) != 0 || !empty($result)) {
                return array('Status' => 'fail', 'Message' => 'Contact Already Exist!', 'Data' => array());
            }
            $data['agency_display_name'] = $agency_dis_name;
            $data['agency_name'] = $agency_name;
            $this->createExtRoFeatureObj->insertRoAgencyContact($data);
        } else {
            $this->createExtRoFeatureObj->updateRoAgencyContact($operation, $data);
        }
        return array('Status' => 'success', 'Message' => 'Agency details updated !', 'Data' => array());
    }

    /**
     * Author: Yash
     * Date: October,2019
     *
     * @return array
     */
    public function addEditClientContact()
    {
        $model = array();

        $model['cust_ro'] = trim($this->CI->input->post('txt_ext_ro'));
        $model['ro_date'] = $this->CI->input->post('txt_ro_date');
        $model['client_display_name'] = $this->CI->input->post('sel_client');
        $model['client_contact'] = $this->CI->input->post('sel_client_contact');
        $model['agency'] = $this->CI->input->post('sel_agency');
        $model['agency_contact'] = $this->CI->input->post('sel_agency_contact');

        log_message('INFO', 'In CreateExtRoService@addEditClientContact | ' . print_r($model, true));

        $model['all_states'] = $this->createExtRoFeatureObj->getAllStates();
        if ($model['client_contact'] != 'new') {
            // fetch data
            $fetchClient = $this->createExtRoFeatureObj->getClientContactInfo($model['client_contact']);
            $model['billing_info'] = $fetchClient['billing_info'];
            $model['billing_address'] = $fetchClient['billing_address'];
            $model['billing_cylce'] = $fetchClient['billing_cycle'];
            $model['client_contact_name'] = $fetchClient['client_contact_name'];
            $model['client_designation'] = $fetchClient['client_designation'];
            $model['client_contact_no'] = $fetchClient['client_contact_number'];
            $model['client_location'] = $fetchClient['client_location'];
            $model['client_address'] = $fetchClient['client_address'];
            $model['client_email'] = $fetchClient['client_email'];
            $model['direct_client'] = $fetchClient['direct_client'];
            $model['client_state'] = $fetchClient['client_state'];
        } else {
            $fetchClient = $this->createExtRoFeatureObj->getAdvertiserDetails($model['client_display_name']);
            $model['billing_info'] = $fetchClient['billing_info'];
            $model['billing_address'] = $fetchClient['billing_address'];
            $model['billing_cylce'] = $fetchClient['billing_cycle'];
            $model['client_address'] = $fetchClient['client_address'];
        }
        return $model;
    }

    /**
     * Author: Yash
     * Date: October,2019
     *
     * @param $operation
     * @return array
     */
    public function postAddEditClientContact($operation)
    {
        $clientDisplayName = $this->CI->input->post('txt_client_display_name');
        $clientContactName = $this->CI->input->post('txt_client_contact_name');
        $clientDesignation = $this->CI->input->post('txt_client_designation');
        $clientContactNo = $this->CI->input->post('txt_client_contact_no');
        $clientLocation = $this->CI->input->post('txt_client_location');
        $clientEmail = $this->CI->input->post('txt_client_email');
        $clientState = $this->CI->input->post('client_state');

        $clientDetails = $this->createExtRoFeatureObj->getAdvertiserDetails($clientDisplayName);
        $clientName = $clientDetails['sv_new_advertiser']['advertiser'];
        $billingAddress = $clientDetails['billing_address'];
        $billingInfo = $clientDetails['billing_info'];
        $billingCycle = $clientDetails['billing_cycle'];
        $clientAddress = $clientDetails['client_address'];
        $directClient = $clientDetails['direct_client'];

        $data = array(
            'billing_info' => $billingInfo,
            'billing_address' => $billingAddress,
            'bill_cylce' => $billingCycle,
            'client_contact_name' => $clientContactName,
            'client_designation' => $clientDesignation,
            'client_contact_number' => $clientContactNo,
            'client_location' => $clientLocation,
            'client_address' => $clientAddress,
            'client_email' => $clientEmail,
            'direct_client' => $directClient,
            'client_state' => $clientState
        );

        if ($operation == 'add') {
            $data['client_name'] = $clientName;
            $data['client_display_name'] = $clientDisplayName;
            $this->createExtRoFeatureObj->insertRoClientContact($data);

        } else {
            $result = $this->createExtRoFeatureObj->checkClientInRoClient($clientName);
            if (count($result) == 0 || empty($result)) {
                return array('Status' => 'fail', 'Message' => 'Client Not Found!', 'Data' => array());
            }
            $this->createExtRoFeatureObj->updateRoClientContact($operation, $data);
        }
        return array('Status' => 'success', 'Message' => 'Client Details updated !', 'Data' => array());
    }
}
