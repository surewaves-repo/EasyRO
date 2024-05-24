<?php
if (!defined('BASEPATH'))
    exit("NO Direct Script Access Allowed");

class Mso_payment extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        $this->load->model("menu_model");
        $this->load->model("am_model");
        $this->load->model('ro_model');
        $this->load->model('invoice_model');
        $this->load->model('user_model');
        $this->load->model('non_fct_ro_model');
        $this->load->model('mso_payment_model');
        $this->load->helper('url');
        $this->load->helper("generic");
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    //function to check whether user is already logged in or not

    /**
     * Loading MSO Payments page
     */

    public function viewMsoPayment()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $this->load->view('ro_manager/mso_payment_view', $model);
    }

    function is_logged_in($javascript_redirect = 0)
    {

        $logged_in_user = $this->session->userdata("logged_in_user");
        $logged_in_user = $logged_in_user[0];
        if (!isset($logged_in_user) || empty($logged_in_user)) {

            if ($javascript_redirect == '1') {
                echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/login";</script>';
            } else {
                redirect("/");
            }
        }

    }

    /**
     * AJAX call for fetching Invoice data uploaded by MSO
     */

    public function getAllInvoiceData()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $network_id = $this->input->post('network_id');

        if ($network_id != 0) {
            $allInvoiceData = $this->mso_payment_model->getAllInvoiceData($start_date, $end_date, $network_id);
        } else {
            $allInvoiceData = $this->mso_payment_model->getAllInvoiceData($start_date, $end_date);
        }
        echo json_encode($allInvoiceData);
    }

    /**
     * AJAX call for downloading invoices uploaded by MSO
     */

    public function downloadInvoicePdf()
    {

        $InvoiceNo = $this->input->post('InvoiceNo');
        $InvoiceNoArray = explode(",", $InvoiceNo);
        $getActualPdf = array();
        if (is_array($InvoiceNoArray)) {
            foreach ($InvoiceNoArray as $InvoiceNumber) {
                $result = $this->mso_payment_model->downloadInvoicePdf($InvoiceNumber);
                if (!empty($result) && is_array($result)) {
                    $getPdf = $result[0]['file'];
                    array_push($getActualPdf, $getPdf);

                }


            }
            $this->saveS3FilesLocally($getActualPdf);
        }
    }

    /**
     * call for downloading invoices from S3 bucket uploaded by MSO
     */

    function saveS3FilesLocally($file_path)
    {

        if (is_array($file_path)) {

            $archive_file_name = "MSO_Invoice_" . date('Y_m_d_H_i_s') . ".zip";

            foreach ($file_path as $actualLocation => $actualPdf) {

                $this->load->library('s3');
                $this->s3->setAuth(AMAZON_S3_KEY, AWS_SECRET_KEY);

                $expArray = explode("/", $actualPdf);

                $bucketName = $expArray[3];
                $fileName = $expArray[4];

                $result = $this->s3->getObject($bucketName, $fileName, false);
                $path_parts = pathinfo($actualPdf);
                $folder = 'mso_invoice';
                if (!file_exists($folder)) {
                    shell_exec('mkdir /surewaves_easy_ro/' . $folder);
                }
                $path_name = $_SERVER['DOCUMENT_ROOT'] . "/surewaves_easy_ro/mso_invoice/";
                $fullPath = "/surewaves_easy_ro/mso_invoice/$archive_file_name";
                $zip = new ZipArchive();
                if ($zip->open($path_name . $archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
                    exit("cannot open <$archive_file_name>\n");
                } else {
                    $fp = fopen($path_name . $fileName, 'w');
                    $fw = fwrite($fp, $result->body);
                    $zip->addFile($path_name . $fileName, $fileName);
                    $zip->close();
                }
            }
            echo json_encode(array(
                'zip' => $fullPath
            ));
        } else {
            echo 0;
        }
    }

    /**
     * AJAX call for loading billing name for the invoices uploaded by MSO
     */

    public function getBillingName()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $billing_name = $this->mso_payment_model->getBillingName($start_date, $end_date);
        echo json_encode($billing_name);
    }

    /* *
    * Loading Update MSO Payments page 
    */

    public function updateMsoPayment()
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $this->load->view('ro_manager/update_mso_payment_view', $model);
    }

    /* *
    * AJAX call for loading updated invoice details based on billing name 
    */

    public function getUpdatedInvoiceData()
    {

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $network_id = $this->input->post('network_id');

        if ($network_id != 0) {
            $allUpdatedInvoiceData = $this->mso_payment_model->getUpdatedInvoiceData($start_date, $end_date, $network_id);
        } else {
            $allUpdatedInvoiceData = $this->mso_payment_model->getUpdatedInvoiceData($start_date, $end_date);
        }
        echo json_encode($allUpdatedInvoiceData);
    }

    /* *
    * Loading Colorbox pop up window for update payments 
    */

    public function update_mso_payment_record($invoiceNumber)
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model = array();
        $model['payment'] = $this->mso_payment_model->getPayment($invoiceNumber);
        $amountPaid = $model['payment'][0]['amount_paid'];
        foreach ($model['payment'] as $totalAmt) {
            $amountPaid = $amountPaid + $totalAmt['new_payment'];
        }
        $model['amount'] = $amountPaid;
        $this->load->view('ro_manager/update_mso_payment_record', $model);
    }

    /* *
    * Loading Colorbox pop up window for viewing payments details 
    */

    function getDetails($invoiceNumber)
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $model['details'] = $this->mso_payment_model->getDetails($invoiceNumber);
        $this->load->view('ro_manager/updated_mso_payment_details', $model);
    }

    /* *
    * Loading Colorbox pop up window for uploading CSV 
    */

    public function excelUpload()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $this->load->view('ro_manager/excelUpload', $model);
    }

    /* posting colorbox values for updating payments */

    public function update_mso_payment_record_details()
    {

        $this->is_logged_in();

        $logged_in = $this->session->userdata("logged_in_user");
        $invoiceNo = $this->input->post('invoice_no');
        $paymentDate = date('Y-m-d');
        $totalInvoiceAmt = $this->input->post('total_amt');
        $amtPaid = $this->input->post('amt_paid');
        $newPayment = $this->input->post('new_payment');
        $basicAmt = $this->input->post('basic_amt');
        $tds = $this->input->post('tds');
        $srvcTax = $this->input->post('srvc_tax');
        $mode = $this->input->post('select_mode');
        $transactionNo = $this->input->post('cheque_no');
        $bankName = $this->input->post('bank_name');
        $transactionDate = date('Y-m-d', strtotime($this->input->post('transaction_date')));
        $notes = $this->input->post('notes');
        $remainingAmt = $totalInvoiceAmt - ($amtPaid + $newPayment);


        $data = array(
            'user_id' => $logged_in[0]['user_id'],
            'invoice_number' => $invoiceNo,
            'payment_date' => $paymentDate,
            'amount_paid' => $amtPaid,
            'new_payment' => $newPayment,
            'basic_amount' => $basicAmt,
            'tds' => $tds,
            'service_tax' => $srvcTax,
            'mode_of_payment' => $mode,
            'transaction_number' => $transactionNo,
            'bank_name' => $bankName,
            'transaction_date' => $transactionDate,
            'remarks' => $notes,
            'remaining_amount' => $remainingAmt
        );
        $this->mso_payment_model->update_payment_record_details($data);
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    /* posting colorbox values for updating payments through CSV upload */

    public function uploadMsoPaymentCsv()
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $fileName = $this->fileUpload();
        $file_path = 'easy_ro_temp_pdf/' . $fileName;
        $this->load->library('csvreader');

        $content = $this->csvreader->parse_file($file_path);
        foreach ($content as $val) {
            $data = array(
                'user_id' => $logged_in[0]['user_id'],
                'invoice_number' => $val['Invoice_Number'],
                'payment_date' => $val['Payment_Date'],
                'amount_paid' => $val['Amount_Paid(excl_New_Payment)'],
                'new_payment' => $val['New_Payment'],
                'basic_amount' => $val['Basic_Amount'],
                'tds' => $val['TDS'],
                'service_tax' => $val['Service_Tax'],
                'mode_of_payment' => $val['Mode_of_Payment'],
                'transaction_number' => $val['Cheque/Transaction_Number'],
                'bank_name' => $val['Bank_Name'],
                'transaction_date' => $val['Cheque/Transaction_Date'],
                'remarks' => $val['Remarks'],
                'remaining_amount' => $val['Invoice_Amount'] - ($val['Amount_Paid(excl_New_Payment)'] + $val['New_Payment'])
            );
            $this->mso_payment_model->insertExcelUpload($data);
        }
        echo '<script>parent.jQuery.colorbox.close();parent.location.reload();</script>';
    }

    public function fileUpload()
    {
        $fileNewName = date('Y_m_d_H_i_s') . "_" . $_FILES['csv_file']['name'];
        $_FILES['csv_file']['name'] = $fileNewName;

        $config['upload_path'] = 'easy_ro_temp_pdf/';
        $config['allowed_types'] = '*';
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('csv_file') == False) {
            $error = array(
                'error' => $this->upload->display_errors()
            );
            $this->load->view('mso_payment/updateMsoPayment', $error);
        } else {
            $this->upload->data();
            return $fileNewName;
        }
    }

    /* *
    * Loading RO Performance page 
    */

    public function roperformance()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $this->load->view('ro_manager/roperformance_view', $model);
    }

    /* *
    * AJAX call for laoding customer name
    */

    public function getNetwork()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $getNetwork = $this->mso_payment_model->getNetwork($invoiceNumber);
        echo json_encode($getNetwork);
    }

    /* *
    * AJAX call for loading all ROs for chosen network along with performance
    */

    public function getRoData()
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $startDate = $this->input->post('start_date');
        $endDate = $this->input->post('end_date');
        $networkID = $this->input->post('networkID');
        $roData = $this->mso_payment_model->getRoData($startDate, $endDate, $networkID);

        if (!empty($roData)) {

            foreach ($roData as $val) {
                $performance = $this->mso_payment_model->performanceData($val['internal_ro'], $networkID);
                foreach ($performance as $value) {

                    if ($value['spotPlayed'] > $value['spotScheduled']) {
                        $value['spotPlayed'] = $value['spotScheduled'];
                    }

                    if ($value['banPlayed'] > $value['banScheduled']) {
                        $value['banPlayed'] = $value['banScheduled'];
                    }

                    if ($value['goodSpotPlayed'] > $value['goodSpotScheduled']) {
                        $value['goodSpotPlayed'] = $value['goodSpotScheduled'];
                    }

                    if ($value['goodBanPlayed'] > $value['goodBanScheduled']) {
                        $value['goodBanPlayed'] = $value['goodBanScheduled'];
                    }

                    $scheduled = $value['spotScheduled'] + $value['banScheduled'] + $value['goodSpotScheduled'] + $value['goodBanScheduled'];
                    $played = $value['spotPlayed'] + $value['banPlayed'] + $value['goodSpotPlayed'] + $value['goodBanPlayed'];
                    $percentage = ($played / $scheduled) * 100;
                    if ($percentage > 100) {
                        $percentage = 100;
                    }
                }
                $amountPayable = ($percentage * $val['net_amount_payable']) / 100;
                $data['values'][] = array(

                    "roId" => $val['id'],
                    "roNumber" => $val['internal_ro'],
                    "per" => $percentage,
                    "roAmount" => $val['net_amount_payable'],
                    "roAmountPayable" => $amountPayable,
                    "mailStatus" => $val['mail_status']
                );
            }
            echo json_encode($data);


        } else {
            echo "null";
        }
    }

    /* *
    * AJAX call to Send mail if performance is below threshold
    */

    public function sendMail()
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $roId = $this->input->post('ro_id');
        $networkId = $this->input->post('network_id');
        $mailStatus = $this->mso_payment_model->sendMail($roId, $networkId);

    }


}