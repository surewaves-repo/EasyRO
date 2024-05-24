<?php if (!defined('BASEPATH')) exit("NO Direct Script Access Allowed");

class Report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("common_model");
        $this->load->model("am_model");
        $this->load->model("mg_model");
        $this->load->model("menu_model");
        $this->load->model("ro_model");
        $this->load->model("user_model");
        //$this->load->model("mso_payment_model") ;
        $this->load->helper("common");
        $this->load->helper('url');
        $this->load->library('curl');
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler(TRUE);
        }

        $this->load->helper('url');
    }

    //function to redirect based on the user logged in status
    public function index()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        if (isset($logged_in[0])) {
            redirect("account_manager/home");
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    //Pass order id
    public function show_mis_report_description($order_id, $edit, $am_ro_id)
    {
        //is_logged_in ();
        $logged_in = $this->session->userdata("logged_in_user");
        $internal_ro_number = base64_decode($order_id);

        //Get All Channel Scheduled
        //$channels_scheduled_val = $this->common_model->get_monthwise_breakup_scheduled_fct($internal_ro_number) ;      
        //echo print_r($channels_scheduled,true);exit;
        //$monthly_values = $this->mg_model->monthwise_breakup_for_given_ro($internal_ro_number,$channels_scheduled_val) ;

        //Get All Market For Given Ro
        //$ro_detail = $this->am_model->get_ro_details_for_internal_ro($internal_ro_number);
        //$ro_id = $ro_detail[0]['id'] ;

        $market_price = $this->mg_model->get_market_ro_price(array('ro_id' => $am_ro_id));
        $market_channels_scheduled = $this->common_model->get_channels_scheduled_for_market($market_price, $internal_ro_number);
        $channels_scheduled = $market_channels_scheduled['channel_scheduled'];
        $channel_scheduled_data = $this->common_model->getScheduledChannelData($channels_scheduled, $internal_ro_number);
        $monthlyValues = $this->mg_model->monthwise_breakup_for_given_ro($internal_ro_number, $channels_scheduled);

        $model = array();
        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];
        $model['channels_scheduled'] = $channel_scheduled_data;
        $model['market_channels_scheduled'] = $market_channels_scheduled['markets_channel'];
        $model['monthly_values'] = $monthlyValues;

        $model['edit'] = $edit;
        $model['id'] = $am_ro_id;

        $am_ext_ro = $this->am_model->get_ro_details($am_ro_id, 0);
        $model['am_ext_ro'] = $am_ext_ro[0]['cust_ro'];
        $model['internal_ro'] = $am_ext_ro[0]['internal_ro'];
        $model['agency'] = $am_ext_ro[0]['agency'];
        $model['client'] = $am_ext_ro[0]['client'];
        $model['brand'] = $am_ext_ro[0]['brand_name'];
        $model['camp_start_date'] = $am_ext_ro[0]['camp_start_date'];
        $model['camp_end_date'] = $am_ext_ro[0]['camp_end_date'];

        $this->load->view('/ro_manager/monthwise_fct_breakup', $model);

    }

    function showEmailPdfStatus()
    {
        $model = array();
        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;
        $this->load->view('/ro_manager/ro_email_pdf_status', $model);
    }

    function getRosForEmailAndPdfStatus()
    {

        $startDate = date('Y-m-d', strtotime($this->input->post("startData")));
        $endDate = date('Y-m-d', strtotime($this->input->post("endDate")));

        $resultObject = $this->mg_model->getRosForEmailAndPdfStatusFromDB($startDate, $endDate);

        if ($resultObject->num_rows() > 0) {

            foreach ($resultObject->result() as $value) {

                $rosArray['ros'][] = array("ro" => $value->internal_ro);

            }
            echo json_encode($rosArray);

        } else {

            echo "No_Ros";

        }


    }

    function getRoDetails()
    {

        $internalRoNumber = $this->input->post("internalRoNumber");

        $roDetailsObject = $this->mg_model->getRoDetailsFromDb($internalRoNumber);

        $resultArray = $this->setRoResultArray($roDetailsObject);

        if (isset($resultArray)) {

            foreach ($resultArray as $customerIdName => $channels) {

                //Getting the pdf generation status
                $customerIdAndName = explode("##", $customerIdName);

                // Function will return Pdf Link mail Status if PDF generated..
                $pdfMailResult = $this->getPdfGenerationAndEmailStatus($internalRoNumber, $customerIdAndName[0], $customerIdAndName[1]);

                $channelNameArray = array();

                foreach ($channels as $chanelId => $details) {

                    $channelNameStatus = $this->getChannelStatus($details[0], $details[1]);
                    array_push($channelNameArray, $channelNameStatus);

                }

                $channelNames = implode(",", $channelNameArray);
                $returnArray[$customerIdAndName[0]][] = array(

                    "Network" => $customerIdAndName[1],
                    "Channels" => $channelNames,
                    "mailSent" => $pdfMailResult["mailStatus"],
                    "MailCount" => $pdfMailResult["mailCount"],
                    "PdfLink" => $pdfMailResult["pdfLink"],
                    "buttonStatus" => $pdfMailResult["buttonStatus"],
                    "buttonValue" => $pdfMailResult["buttonValue"]
                );
            }
            echo json_encode($returnArray);

        } else {

            echo "Not_Set";
        }

    }

    private function setRoResultArray($resultObject)
    {

        foreach ($resultObject->result() as $row) {

            $customerArray[$row->customer_id . '##' . $row->customer_name][$row->tv_channel_id][] = $row->channel_name;
            $customerArray[$row->customer_id . '##' . $row->customer_name][$row->tv_channel_id][] = $row->approved_status;
        }
        if (isset($customerArray)) {

            return $customerArray;

        } else {

            return null;

        }

    }

    private function getPdfGenerationAndEmailStatus($internalRoNumber, $customerId, $customerName)
    {

        $resultObject = $this->mg_model->getPdfGenerationStatusFromDB($internalRoNumber, $customerId);

        // PDF status o for not generated, 1 for generated and 2 for not Customer Not Approved..
        $pdfGenerationStatus = 2;
        $pdfProcessingStatus = 0;

        if ($resultObject->num_rows() > 0) {

            $row = $resultObject->row();

            $pdfGenerationStatus = $row->pdf_generation_status;
            $pdfProcessingStatus = $row->pdf_processing;

        } else {

            $pdfGenerationStatus = 2;
        }

        //This will return email status pdf link if true
        $result = $this->processPdfGenerationStatus($pdfGenerationStatus, $pdfProcessingStatus, $internalRoNumber, $customerName);

        return $result;

    }

    private function processPdfGenerationStatus($pdfGenerationStatus, $pdfProcessingStatus, $internalRoNumber, $customerName)
    {

        $emailStatus = '-';
        $mailCount = '-';
        $pdfLink = '-';
        $regenerateButtonState = "disabled";

        switch ($pdfGenerationStatus) {

            case 0:

                $emailStatus = 'Not Sent';
                $mailCount = '0';
                $pdfLink = 'Not Available';
                $regenerateButtonState = $this->checkPdfProcessingStatus($pdfProcessingStatus);
                $regenerateButtonValue = $this->getPdfGenerationButtonValue($pdfProcessingStatus);

                break;

            case 1:

                $resultArray = $this->getEmailStatusAndMailCountAndPdfLink($internalRoNumber, $customerName);

                $emailStatus = $resultArray[0];
                $mailCount = $resultArray[1];
                $pdfLink = $resultArray[2];
                $regenerateButtonState = "enabled";
                $regenerateButtonValue = "Re-Generate";
                break;

            case 2: // Not approve condition

                $emailStatus = '-';
                $mailCount = '-';
                $pdfLink = '-';
                $regenerateButtonState = "disabled";
                $regenerateButtonValue = "Re-Generate";
                break;

        }

        $returnArray = array("mailStatus" => $emailStatus, "mailCount" => $mailCount, "pdfLink" => $pdfLink, "buttonStatus" => $regenerateButtonState, "buttonValue" => $regenerateButtonValue);

        return $returnArray;

    }

    private function checkPdfProcessingStatus($pdfProcessingStatus)
    {

        $returnStatus = "disabled";

        if ($pdfProcessingStatus == 0) {

            $returnStatus = "enabled";

        } else if ($pdfProcessingStatus == 1) {

            $returnStatus = "disabled";

        }
        return $returnStatus;
    }

    private function getPdfGenerationButtonValue($pdfProcessingStatus)
    {

        $returnStatus = "Re-Generate";

        if ($pdfProcessingStatus == 1) {

            $returnStatus = "Processing..";

        } else if ($pdfProcessingStatus == 0) {

            $returnStatus = "Re-Generate";

        }
        return $returnStatus;
    }

    private function getEmailStatusAndMailCountAndPdfLink($internalRoNumber, $customerName)
    {

        $subject = $internalRoNumber . "/___/" . $customerName;

        $countSubjectObject = $this->mg_model->getSentMailCount($subject);

        $row = $countSubjectObject->row();

        $totalMailSent = $row->totalMailSent;

        if ($totalMailSent > 0) {

            $resultObject = $this->mg_model->getSentMailPDFLink($subject);
            $pathRow = $resultObject->row();

            $returnData = array("Mail Sent", $totalMailSent, $pathRow->filePath);

        } else {

            $returnData = array("Not Sent", 0, "Not Available");

        }

        return $returnData;
    }

    private function getChannelStatus($channelName, $channelStatus)
    {

        $status = "";

        switch ($channelStatus) {

            case "Not_Approved":

                $status = "(NA)";
                break;

            case "Approved":

                $status = "(A)";
                break;

            default:

                $status = "(" . $channelStatus . ")";
        }

        return $channelName . $status;
    }

    function regeneratePdf()
    {

        $internalRoCustomerId = $this->input->post("internalRoAndCustomerId");


        echo $internalRoCustomerId;
    }

    function getPdfPrivateUrl()
    {

        $fileName = $this->input->post("fileName");
        $this->load->library('s3');
        $this->s3->setAuth(AMAZON_S3_KEY, AWS_SECRET_KEY);

        $result = $this->s3->getObject(NETWORK_RO_BUCKET, $fileName, false);

        header("Content-Type: {$result->headers['type']}");
        echo $result->body;

    }

    public function updateRegionIdForRos()
    {

    }

    public function mis_ro_report()
    {
        //$this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;
        $this->load->view('ro_manager/mis_ro_report', $model);
    }

    public function getMisReport()
    {
        $records = $this->ro_model->getMisReport();

        $this->output->set_header('Content-Type: application/json');

        $this->output->set_output($records);
    }

    /*public function download_mis_report($month_name, $financial_year)
    {
        if (!isset($month_name) || empty($month_name)) {
            $month_name = date("F");
        }
        if (!isset($financial_year) || empty($financial_year)) {
            $financial_year = date("Y");
        }

        $misReportData = $this->ro_model->downloadMisReportData($financial_year, $month_name);
        $fileName = 'mis_ro_report_' . $month_name . "_" . $financial_year . ".csv";

        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=' . $fileName);
        ob_clean();
        flush();

        $heading = "'Customer Ro Number','Internal Ro Number','Network Ro Number','Network Name','Network Id','Advertiser Client Name','Agency Name','Market','Start Date','End Date','Activity Months','Network Share','Net Channel Payout','Channel Id','Spot Amount','Banner Amount','Spot FCT','Banner FCT','Release Date','Billing Name' ";

        echo $heading;
        echo "\n";
        $csv_content = '';

        foreach ($misReportData as $data) {
            $csv_content = $data['customer_ro_number'] . "," . $data['internal_ro_number'] . "," . $data['network_ro_number'] . "," . $data['customer_name'];
            $csv_content = $csv_content . "," . $data['customer_id'] . "," . $data['client'] . "," . $data['agency_name'] . "," . $data['market'] . "," . $data['start_date'] . "," . $data['end_date'];
            $csv_content = $csv_content . "," . $data['month_name'] . "," . $data['customer_share'] . "," . $data['channel_payout'] . "," . $data['channel_id'] . "," . $data['spot_amount'] . "," . $data['banner_amount'] . "," . $data['scheduled_spot_impression'];
            $csv_content = $csv_content . "," . $data['scheduled_banner_impression'] . "," . $data['release_date'] . "," . $data['billing_name'];

            echo $csv_content;
            echo "\n";
        }
    }*/
    public function download_mis_report($month_name, $financial_year)
{
    if (!isset($month_name) || empty($month_name)) {
        $month_name = date("F");
    }
    if (!isset($financial_year) || empty($financial_year)) {
        $financial_year = date("Y");
    }
    $misReportData = $this->ro_model->downloadMisReportData($financial_year, $month_name);
    $csv = tmpfile();
    $heading = array('Customer Ro Number','Internal Ro Number','Network Ro Number','Network Name','Network Id','Advertiser Client Name','Agency Name','Market','Start Date','End Date','Activity Months','Network Share','Net Channel Payout','Channel Id','Spot Amount','Banner Amount','Spot FCT','Banner FCT','Release Date','Billing Name');
    fputcsv($csv, array_values($heading));
    
    $fileName = 'mis_ro_report_' . $month_name . "_" . $financial_year . ".csv";

   /* header('Expires:0');
    header('Cache-control: private');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-disposition: attachment; filename=' . $fileName);
    ob_clean();
    flush();

    $heading = "'Customer Ro Number','Internal Ro Number','Network Ro Number','Network Name','Network Id','Advertiser Client Name','Agency Name','Market','Start Date','End Date','Activity Months','Network Share','Net Channel Payout','Channel Id','Spot Amount','Banner Amount','Spot FCT','Banner FCT','Release Date','Billing Name' ";

    echo $heading;
    echo "\n";
    $csv_content = '';*/

    foreach ($misReportData as $data) {
        $csv_content = array (  $data['customer_ro_number'], 
                                $data['internal_ro_number'], 
                                $data['network_ro_number'], 
                                $data['customer_name'],
                                $data['customer_id'], 
                                $data['client'], 
                                $data['agency_name'], 
                                $data['market'], 
                                $data['start_date'], 
                                $data['end_date'],
                                $data['month_name'], 
                                $data['customer_share'], 
                                $data['channel_payout'], 
                                $data['channel_id'], 
                                $data['spot_amount'], 
                                $data['banner_amount'], 
                                $data['scheduled_spot_impression'],
                                $data['scheduled_banner_impression'], 
                                $data['release_date'], 
                                $data['billing_name']
                            );
        
        fputcsv($csv, array_values($csv_content));
    }
    rewind($csv);
    $fstat = fstat($csv);
    $now = gmdate("D, d M Y H:i:s");
   
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Type: text/x-csv');

    // disposition / encoding on response body
    if (isset($fileName) && strlen($fileName) > 0)
        header("Content-Disposition: attachment;filename={$fileName}");
    if (isset($fstat['size']))
        header("Content-Length: ".$fstat['size']);
    header("Content-Transfer-Encoding: binary");
    header("Connection: close");
    fpassthru($csv);
    fclose($csv);

}

    public function mis_non_fct_ro_report()
    {
        //$this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['profile_id'] = $logged_in[0]['profile_id'];

        $menu = $this->menu_model->get_header_menu();
        $model['menu'] = $menu;
        $this->load->view('ro_manager/mis_non_fct_ro_report', $model);
    }

    public function getMisNonFctReport()
    {
        $records = $this->ro_model->getMisNonFctReport();

        $this->output->set_header('Content-Type: application/json');

        $this->output->set_output($records);
    }

    public function download_mis_non_fct_report($month_name, $financial_year)
    {
        if (!isset($month_name) || empty($month_name)) {
            $month_name = date("F");
        }
        if (!isset($financial_year) || empty($financial_year)) {
            $financial_year = date("Y");
        }

        $misReportData = $this->ro_model->downloadMisNonFctReportData($financial_year, $month_name);
        $fileName = 'mis_non_fct_ro_report_' . $month_name . "_" . $financial_year . ".csv";

        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=' . $fileName);
        ob_clean();
        flush();

        $heading = "customer_ro_number,internal_ro_number,Agency,Agency Contact,Advertiser Client Name,Month Name,Amount,Agency Commision";

        echo $heading;
        echo "\n";
        $csv_content = '';

        foreach ($misReportData as $data) {
            $csv_content = $data['customer_ro_number'] . "," . $data['internal_ro_number'] . "," . $data['agency'] . "," . $data['agency_contact'] . "," . $data['client'] . "," . $data['month'] . "," . $data['price'] . "," . $data['agency_commision'];
            echo $csv_content;
            echo "\n";
        }
    }

    public function assignedRoRegionAsUser($tableName)
    {
        //get All distinct User of Ro
        $users = $this->am_model->getAllDistinctUsersOfRo($tableName);
        foreach ($users as $val) {
            $userId = $val['user_id'];
            $userData = $this->user_model->getRegionIdForUser($userId);
            $regionId = $userData[0]['region_id'];
            if (!isset($regionId) || empty($regionId)) continue;
            $this->am_model->updateRegionForUser($regionId, $userId, $tableName);
        }
        $this->load->helper('csv');
        array_to_csv($csv_array, 'mis_ro_report.csv');
    }

    public function download_payment_detail($start_date, $end_date, $network_id)
    {

        if ($network_id != 0) {
            $paymentDetail = $this->mso_payment_model->downloadExcel($start_date, $end_date, $network_id);
            foreach ($paymentDetail as &$payment) {
                if ($payment['ro_amount'] == 0) {
                    $payment['Amount'] = $payment['ro_amount_payable'];
                } else {
                    $payment['Amount'] = $payment['ro_amount'];
                }
            }
            unset($payment);
        } else {
            $paymentDetail = $this->mso_payment_model->downloadExcel($start_date, $end_date);
            foreach ($paymentDetail as &$payment) {
                if ($payment['ro_amount'] == 0) {
                    $payment['Amount'] = $payment['ro_amount_payable'];
                } else {
                    $payment['Amount'] = $payment['ro_amount'];
                }
            }
            unset($payment);
        }
        $fileName = 'Payment_Report_' . date('Y_m_d_H_i_s') . ".csv";

        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=' . $fileName);

        $heading = "Invoice Number,Invoice Date,Billing Name,Invoice Amount,Amount Paid,New Payment,Basic Amount,TDS,Service Tax,Payment Date,Mode of Payment,Cheque/Transaction Number,Bank Name,Cheque/Transaction Date,Remarks,Remaining Amount";

        echo $heading;
        echo "\n";
        $csv_content = '';

        foreach ($paymentDetail as $data) {
            $csv_content = $data['invoice_date'] . "," . $data['invoice_number'] . "," . $data['billing_name'] . "," . $data['Amount'] . "," . $data['amount_paid'] . "," . $data['new_payment'] . "," . $data['basic_amount'];
            $csv_content = $csv_content . "," . $data['tds'] . "," . $data['service_tax'] . "," . $data['payment_date'] . "," . $data['mode_of_payment'] . "," . $data['transaction_number'] . "," . $data['bank_name'] . "," . $data['transaction_date'];
            $csv_content = $csv_content . "," . $data['remarks'] . "," . $data['remaining_amount'];
            echo $csv_content;
            echo "\n";
        }
    }
}
/* End of file report.php */
/* Location: ./application/controllers/report.php */
