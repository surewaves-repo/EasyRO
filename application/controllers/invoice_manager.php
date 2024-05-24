<?php if (!defined('BASEPATH')) exit("NO Direct Script Access Allowed");

class Invoice_manager extends CI_Controller
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
        $this->load->helper('url');
        $this->load->helper("generic");
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    //function to check whether user is already logged in or not

    public function cancel_download_invoice($start_index = 0, $serch_set = 0)
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();

        $month = $this->input->post('month');
        if (isset($month) && $month != null) {
            $month = $this->input->post('month');
        }

        $start_d = date('Y-m-d', strtotime($month));
        $end_d = date('Y-m-t', strtotime($month));

        $monthName = date("F", strtotime($month));
        $status = 3;
        $invoice_list = $this->invoice_model->getInvoiceData($monthName, $status);


        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        //$model['ro_list'] = $invoice_list;

        /*$total_rows = count($invoice_list);
        // Pagination to show internal_ro_numbers
        $filtered_ros = array_splice($invoice_list, $start_index, 20);//print_r($invoice_lists);exit;
        $model['invoice_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
        $model['page_links'] = create_page_links (base_url()."invoice_manager/cancel_download_invoice/".$month, 20, $total_rows);
        $model['month'] = $month;*/

        $model['invoice_list'] = $invoice_list;
        $model['month'] = $month;

        $this->load->view('invoice_manager/cancel_download_invoice', $model);
    }

    /*public function generate_invoice(){
        $this->is_logged_in ();
        $logged_in = $this->session->userdata("logged_in_user");

        $menu = $this->menu_model->get_header_menu() ;

        $model=array();
        $model['logged_in_user']=$logged_in[0];//print_r($logged_in[0]);exit;
        $model['menu'] = $menu ;

        $this->load->view('invoice_manager/generate_invoice',$model);
    }*/

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

    public function download_invoice($start_index = 0, $serch_set = 0)
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();

        $month = $this->input->post('month');
        if (isset($month) && $month != null) {
            $month = $this->input->post('month');
        }

        $start_d = date('Y-m-d', strtotime($month));
        $end_d = date('Y-m-t', strtotime($month));

        $monthName = date("F", strtotime($month));
        $status = 1;
        $invoice_list = $this->invoice_model->getInvoiceData($monthName, $status);


        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        //$model['ro_list'] = $invoice_list;

        /*$total_rows = count($invoice_list);
        // Pagination to show internal_ro_numbers
        $filtered_ros = array_splice($invoice_list, $start_index, 20);//print_r($invoice_lists);exit;
        $model['invoice_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
        $model['page_links'] = create_page_links (base_url()."invoice_manager/download_invoice/".$month, 20, $total_rows);
        $model['month'] = $month;*/

        $model['invoice_list'] = $invoice_list;//echo '<pre>';print_r($model['ro_list']);exit;
        $model['month'] = $month;

        $this->load->view('invoice_manager/download_invoice', $model);
    }

    public function download_invoice_old($sel_month, $start_index = 0, $serch_set = 0)
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();

        $month = $this->input->post('month');
        if (isset($month) && $month != null) {
            $month = $this->input->post('month');
        } else {
            $month = urldecode($sel_month);
        }

        $start_d = date('Y-m-d', strtotime($month));
        $end_d = date('Y-m-t', strtotime($month));

        $ro_list = $this->invoice_model->getInvoiceDetail($start_d, $end_d);
        $invoiceRovalues = $this->invoice_model->getInvoiceRo($start_d, $end_d);

        $invoice_list = array();

        if (count($invoiceRovalues) > 0) {
            foreach ($invoiceRovalues as $val) {
                $roId = $val['ro_id'];
                $date_key = strtotime($val['start_date']) . "-" . strtotime($val['end_date']);
                $invoiceArrayCount = count($ro_list[$roId][$date_key]);
                for ($count = 0; $count < $invoiceArrayCount; $count++) {
                    array_push($invoice_list, $ro_list[$roId][$date_key][$count]);
                }
            }
        }


        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        //$model['ro_list'] = $invoice_list;

        $total_rows = count($invoice_list);
        // Pagination to show internal_ro_numbers
        $filtered_ros = array_splice($invoice_list, $start_index, 20);//print_r($invoice_lists);exit;
        $model['invoice_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
        $model['page_links'] = create_page_links(base_url() . "invoice_manager/download_invoice/" . $month, 20, $total_rows);
        $model['month'] = $month;

        $this->load->view('invoice_manager/download_invoice', $model);
    }


    public function downloadAllInvoice()
    {
        $invoiceNumber = $this->input->post('invoice_no');
        $fileLocations = array();
        foreach ($invoiceNumber as $in) {
            $invoiceData = $this->invoice_model->getInvoiceGenerationFile(array('invoice_number' => $in));
            $fileLocation = $invoiceData[0]['file_location'];
            array_push($fileLocations, $fileLocation);
        }
        $this->zippingAndDownload($fileLocations);
    }

    public function zippingAndDownload($fileLocations)
    {
        $pathName = $_SERVER['DOCUMENT_ROOT'] . '/surewaves_easy_ro/invoice_pdf/';
        $archive_file_name = "Invoice_pdf_" . date("Y-m-d_H_i_s") . ".zip";


        $this->load->library('zip');

        foreach ($fileLocations as $fileLocation) {
            $pathBreakUpOfFile = explode("/", $fileLocation);
            $pathBreakUpCount = count($pathBreakUpOfFile);
            $fileName = $pathBreakUpOfFile[$pathBreakUpCount - 1];
            $roId = $pathBreakUpOfFile[$pathBreakUpCount - 2];
            $filePath = $roId . "/" . $fileName;

            $this->zip->read_file($pathName . $filePath);
        }
        $this->zip->download($archive_file_name);


        /*$zip = new ZipArchive();
        foreach($fileLocations as $fileLocation) {
            $pathBreakUpOfFile = explode("/",$fileLocation) ;
            $pathBreakUpCount = count($pathBreakUpOfFile) ;
            $fileName =   $pathBreakUpOfFile[$pathBreakUpCount-1];
            $roId = $pathBreakUpOfFile[$pathBreakUpCount-2];
            $filePath = $roId."/".$fileName ;
            $zipPath = $pathName.$archive_file_name ;
            
            
            //create the file and throw the error if unsuccessful
            $zipCreation = $zip->open($zipPath, ZipArchive::CREATE);
            exec("chmod -R 707 $zipPath") ;

            if ($zipCreation !==TRUE) {
                exit("cannot open <$archive_file_name>\n");
            }else {
                $zip->addFile($pathName.$filePath,$fileName);
                $zip->close();
            }
        }
        
        ob_start();
        header("Pragma: hack");
        header('Expires:0');
        header('Cache-control: public');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-disposition: attachment; filename='.$archive_file_name);
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: binary\n");

        ob_end_clean();
        flush(); */
        //readfile($pathName.$archive_file_name);
    }

    public function download_invoice_pdf($ro_id, $invoice_id, $split, $sel_month)
    {

        $month = urldecode($sel_month);
        $start_date = date('Y-m-d', strtotime($month));
        $end_date = date('Y-m-t', strtotime($month));

        $pathName = $_SERVER['DOCUMENT_ROOT'] . '/surewaves_easy_ro/invoice_pdf/' . $ro_id . '/';
        mkdir($pathName);
        exec("chmod -R 707 $pathName");

        if ($split == 0) {
            $model['invoice_details'] = $this->invoice_model->get_unsplitted_invoice_details($ro_id, $start_date, $end_date);
            $this->load->view('ro_manager/tax_invoice', $model);
        } else if ($split == 1) {
            $model['invoice_details'] = $this->invoice_model->get_splitted_invoice_details($invoice_id, $start_date, $end_date);
            $this->load->view('ro_manager/tax_invoice', $model);
        } else if ($split == 2) {
            $invoice_ids = str_replace("~", ",", $invoice_id);
            $invoice_details = $this->invoice_model->get_splitted_invoice_details($invoice_ids, $start_date, $end_date);
            $model['invoice_details'] = $invoice_details;
            $this->load->view('ro_manager/tax_invoice', $model);
        }

        $archiveName = $_SERVER['DOCUMENT_ROOT'] . '/surewaves_easy_ro/invoice_pdf/' . $ro_id . '/' . "Invoice_pdf_" . $ro_id . ".zip";

        $file_name = "Invoice_pdf_" . $ro_id . ".zip";
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $file_name);
        header('Content-Length: ' . filesize($archiveName));
        readfile($archiveName);
        sleep(5);
        exec("rm -rf $pathName");
    }

    public function download_invoice_zipped($ro_id, $invoice_ids, $zipped, $sel_month)
    {
        $month = urldecode($sel_month);
        $start_date = date('Y-m-d', strtotime($month));
        $end_date = date('Y-m-t', strtotime($month));
        $invoiceIdsFromUI = explode("_", $invoice_ids);

        $pathName = $_SERVER['DOCUMENT_ROOT'] . '/surewaves_easy_ro/invoice_pdf/' . $ro_id . '/';
        mkdir($pathName);
        exec("chmod -R 707 $pathName");

        //$invoice_details = $this->invoice_model->get_unsplitted_invoice_details($ro_id,$start_date,$end_date);
        $invoice_details = $this->invoice_model->getInvoiceDetail($start_date, $end_date, $ro_id);
        $pdfGeneration = false;
        foreach ($invoice_details[$ro_id] as $key => $invoiceValue) {
            $roId_start_end_date = explode("-", $key);
            $roId_startDate = $roId_start_end_date[0];
            $roId_endDate = $roId_start_end_date[1];
            $getAllValues = $this->getRoData($roId_startDate, $roId_endDate, $start_date, $end_date);
            if ($getAllValues) {
                $pdfGeneration = true;
                foreach ($invoiceValue as $value) {
                    $toGeneratePdf = $this->toGeneratePdfForGivenInvoice($value['id'], $invoiceIdsFromUI);

                    if ($toGeneratePdf) {
                        $invoice_ids = $value['id'];
                        $invoice_details = $this->invoice_model->get_splitted_invoice_details($invoice_ids, $start_date, $end_date);
                        $model['invoice_details'] = $invoice_details;
                        $this->load->view('ro_manager/tax_invoice', $model);
                    }

                }
            } else {
                continue;
            }
        }

        if ($pdfGeneration) {

            $archiveName = $_SERVER['DOCUMENT_ROOT'] . '/surewaves_easy_ro/invoice_pdf/' . $ro_id . '/' . "Invoice_pdf_" . $ro_id . ".zip";

            $file_name = "Invoice_pdf_" . $ro_id . ".zip";
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $file_name);
            header('Content-Length: ' . filesize($archiveName));
            readfile($archiveName);
            sleep(5);
            exec("rm -rf $pathName ");
        }


    }

    public function getRoData($roId_startDate, $roId_endDate, $start_date, $end_date)
    {
        if (((strtotime($start_date) <= $roId_startDate) && (strtotime($end_date) >= $roId_startDate)) && ((strtotime($start_date) <= $roId_endDate) && (strtotime($end_date) >= $roId_endDate))) {
            return true;
        } else {
            return false;
        }
    }

    public function toGeneratePdfForGivenInvoice($invoiceIdsFromDB, $invoiceIdsFromUI)
    {
        $invoiceIdsFromDBArray = explode(",", $invoiceIdsFromDB);
        $isfound = false;
        foreach ($invoiceIdsFromUI as $invoiceIds) {
            $invoiceIdsFromUIArray = explode("~", $invoiceIds);
            $result = array_diff($invoiceIdsFromDBArray, $invoiceIdsFromUIArray);
            if (count($result) == 0) {
                $isfound = TRUE;
                break;
            } else {
                continue;
            }
        }
        if ($isfound) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function generate_invoice($start_index = 0, $serch_set = 0)
    {
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();

        $month = $this->input->post('month');
        if (isset($month) && $month != null) {
            $month = $this->input->post('month');
        }

        $start_d = date('Y-m-d', strtotime($month));
        $end_d = date('Y-m-t', strtotime($month));

        $ro_list = $this->invoice_model->get_ros_for_invoice($start_d, $end_d);
        $todaysDate = date('Y-m-d');

        //Add Generation Status
        $finalRoList = $ro_list;
        foreach ($finalRoList as &$res) {
            $ro_id = $res['id'];
            $generation_status = $this->invoice_model->getInvoiceGeneration(array('ro_id' => $ro_id, 'month_year' => $month));
            $res['status'] = $generation_status[0]['is_generated'];
            $res['billingCycle'] = $this->invoice_model->getBillingCyclePayment($res['internal_ro']);

            $startEndDateOfRo = $this->invoice_model->getStartEndDateForRo($res['internal_ro']);
            if ((strtotime($startEndDateOfRo['end_date']) <= strtotime($end_d)) && (strtotime($startEndDateOfRo['end_date']) <= strtotime($todaysDate))) {
                $res['campaignStatus'] = 'Completed';
            } else {
                $res['campaignStatus'] = 'Running';
            }

            $invoice_collection = $this->invoice_model->getInvoiceCollectionCount($ro_id);
            $res['collection_count'] = count($invoice_collection);
        }

        $ro_list = $finalRoList;
        unset($finalRoList);
        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        //$model['ro_list'] = $ro_list;

        /*$total_rows = count($ro_list);
        // Pagination to show internal_ro_numbers
        $filtered_ros = array_splice($ro_list, $start_index, 20);//print_r($ro_lists);exit;
        $model['ro_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
        $model['page_links'] = create_page_links (base_url()."invoice_manager/generate_invoice/".$month, 20, $total_rows);
        $model['month'] = $month;
		 //echo "<pre>";print_r($model);exit;*/

        $model['ro_list'] = $ro_list;
        $model['month'] = $month;
        $this->load->view('invoice_manager/generate_invoice', $model);
    }

    public function generate_invoice_for_ro()
    {
        $roIds = $this->input->post('ro_id');
        $split_by_market = $this->input->post('hid_split_by_market');
        $split_by_brand = $this->input->post('hid_split_by_brand');
        $split_by_content = $this->input->post('hid_split_by_content');
        $monthYear = $this->input->post('month');

        /*$split_by = array(
            'split_by_market' =>$split_by_market,
            'split_by_brand'=>$split_by_brand,
            'split_by_content'=>$split_by_content
        ); */

        $splitCriteria = $split_by_market . "" . $split_by_brand . "" . $split_by_content;
        foreach ($roIds as $val) {
            $ro_id = $val;
            $insertData = array('ro_id' => $ro_id, 'month_year' => $monthYear, 'splitCriteria' => $splitCriteria, 'is_generated' => 2);
            $updateData = array('splitCriteria' => $splitCriteria, 'is_generated' => 2);
            $whereData = array('ro_id' => $ro_id, 'month_year' => $monthYear, 'splitCriteria' => $splitCriteria);

            $getData = $this->invoice_model->getInvoiceGeneration($whereData);
            if (count($getData) > 0) {
                $this->invoice_model->updateForInvoiceGeneration($updateData, $whereData);
            } else {
                $this->invoice_model->insertForInvoiceGeneration($insertData);
            }
        }


        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /invoice_manager/generateInvoiceCron > /dev/null &");

        $data = array();
        $data['status'] = 'success';
        $data['ro_id'] = implode(",", $roIds);
        echo json_encode($data);
    }

    public function generateInvoiceCron()
    {
        $whereData = array('is_generated' => 2);
        $getData = $this->invoice_model->getInvoiceGeneration($whereData);

        foreach ($getData as $val) {
            $id = $val['id'];
            $ro_id = $val['ro_id'];
            $this->invoice_model->updateForInvoiceGeneration(array('is_generated' => 4), array('id' => $id));

            $monthYear = $val['month_year'];
            $splitCriteria = str_split($val['splitCriteria']);

            $split_by = array(
                'split_by_market' => $splitCriteria[0],
                'split_by_brand' => $splitCriteria[1],
                'split_by_content' => $splitCriteria[2]
            );

            $ro_details = $this->am_model->get_ro_details($ro_id);

            $internal_ro_number = $ro_details[0]['internal_ro'];

            $this->invoice_model->getStartAndEndDateForInvoiceGeneration($internal_ro_number, $split_by, $monthYear);

            $invoiceValues = $this->invoice_model->getInvoiceValuesForRoAndMonth($monthYear, $ro_id);
            $this->saveInvoicePdf($invoiceValues, $monthYear, $id);
        }

    }

    public function saveInvoicePdf($invoiceValues, $monthYear, $invoiceQueueId)
    {
        $start_date = date('Y-m-d', strtotime($monthYear));
        $end_date = date('Y-m-t', strtotime($monthYear));
        $monthName = date("F", strtotime($monthYear));

        foreach ($invoiceValues as $value) {
            $invoice_ids = $value['id'];
            $ro_id = $value['ro_id'];
            //array_push($roIds,$ro_id) ;

            $this->invoice_model->updateRoInvoiceDataForInvoiceIds($invoice_ids, 2);

            $this->createDirectoryForRo($ro_id);
            //Generate Invoice Number
            $invoiceNumber = $this->invoice_model->generateInvoiceNumber($ro_id, $monthName);

            $invoice_details = $this->invoice_model->get_splitted_invoice_details($invoice_ids, $start_date, $end_date);
            $model['invoice_details'] = $invoice_details;
            $model['split_criteria'] = $value['split_by_market'] . "" . $value['split_by_brand'] . "" . $value['split_by_content'];
            $model['month_name'] = $monthName;
            $model['invoice_number'] = $invoiceNumber['invoiceNumber'];
            $model['invoiceFormat'] = $invoiceNumber['invoice_format'];
            $model['invoice_date'] = $end_date;

            if (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT'])) {
                $documentRoot = '/opt/lampp/htdocs';
            } else {
                $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            }

            $model['document_root'] = $documentRoot;
            $this->load->view('ro_manager/tax_invoice', $model);
        }
        $this->invoice_model->updateForInvoiceGeneration(array('is_generated' => 1), array('id' => $invoiceQueueId));
    }

    public function createDirectoryForRo($ro_id)
    {
        if (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT'])) {
            $documentRoot = '/opt/lampp/htdocs';
        } else {
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        }
        $pathName = $documentRoot . '/surewaves_easy_ro/invoice_pdf/' . $ro_id;

        if (!file_exists($pathName)) {
            mkdir($pathName);
            exec("chmod -R 707 $pathName");
        }
    }

    public function checkGenerationStatus()
    {
        $monthYear = $this->input->post('month');
        $data = array(
            'month_year' => $monthYear
        );
        $roIds = $this->invoice_model->getInvoiceGeneration($data);

        echo json_encode($roIds);
    }

    public function generateInvoiceRo($internal_ro_number, $split_by)
    {
        $this->invoice_model->getStartAndEndDateForInvoiceGeneration($internal_ro_number, $split_by);
    }

    public function record_mail_sent()
    {
        $invoice_id = $this->input->post('invoice_id');
        $data = array(
            'mail_sent' => 1
        );
        $where = array(
            'id' => $invoice_id
        );
        $this->invoice_model->update_invoice_details($data, $where);
    }

    public function record_money_received()
    {
        $invoice_id = $this->input->post('invoice_id');
        $data = array(
            'money_received' => 1
        );
        $where = array(
            'id' => $invoice_id
        );
        $this->invoice_model->update_invoice_details($data, $where);
    }

    public function invoice_collection($invoices)
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $model['invoice_list'] = $invoices;

        $this->load->view('invoice_manager/invoice_collection', $model);
    }

    public function get_clients_for_invoice()
    {
        $agency = $this->input->post('agency');
        $monthYear = $this->input->post('month');

        if (isset($monthYear)) {
            $monthYearValues = explode(" ", $monthYear);
            $month = $monthYearValues[0];
        } else {
            $month = date("m");
        }

        $clients = $this->invoice_model->getClientsForInvoice($month, $agency);

        echo json_encode($clients);
    }

    public function get_inputs_for_search()
    {
        $monthYear = $this->input->post('month');

        if (isset($monthYear)) {
            $monthYearValues = explode(" ", $monthYear);
            $month = $monthYearValues[0];
            $year = $monthYearValues[1];

            /*$startDate = date('Y-m-d',strtotime($month));
            $end_date = date('Y-m-t',strtotime($month)); */
        } else {
            $year = date("Y");
            $month = date("m");
            /*$startDate = date("$year-$month-01") ;
            $end_date = date("$year-$month-t") ;*/
        }
        $agencies = $this->invoice_model->getAgenciesForInvoice($month);
        $invoices = $this->invoice_model->getAllInvoiceNumber($month);

        $inputsToSearch = array();
        $inputsToSearch['agencies'] = $agencies;
        $inputsToSearch['invoices'] = $invoices;

        echo json_encode($inputsToSearch);

    }

    public function test_invoice_report($monthYear, $ro_id)
    {
        $result = $this->invoice_model->getInvoiceValuesForRoAndMonth($monthYear, $ro_id);
        //$result = $this->invoice_model->getInvoiceDetail($start_date,$end_date);
        echo print_r($result, true);
    }

    public function cancel_invoice_for_ro()
    {
        $ro_id = $this->input->post('ro_id');

        $billingCycle = $this->invoice_model->getBillingCyclePaymentForRoId($ro_id);
        $consolidatedBill = FALSE;

        if ($billingCycle == 'Consolidated') {
            $consolidatedBill = TRUE;
        }

        if ($consolidatedBill) {
            $ro_details = $this->am_model->get_ro_details($ro_id);
            $internal_ro_number = $ro_details[0]['internal_ro'];
            $rostartEndDate = $this->invoice_model->getStartEndDateForRo($internal_ro_number);

            $this->deleteConsolidatedInvoiceValue($ro_id, $rostartEndDate['start_date'], $rostartEndDate['end_date']);
        } else {
            $monthYear = $this->input->post('month');
            $month_year = explode(" ", $monthYear);
            $whereData = array('ro_id' => $ro_id, 'month_name' => $month_year[0]);
            $this->invoice_model->updateForInvoiceGenerationFile(array('status' => 3), $whereData);

            //Get cancelled Data
            $cancelledData = $this->invoice_model->getRoInvoiceData($whereData);

            //store History
            $this->storeInvoiceHistory($cancelledData);

            //Delete from ro_invoice_report
            $this->invoice_model->deleteInvoiceData($whereData);

            //Delete from Queue
            $this->invoice_model->deleteForInvoiceGeneration($ro_id, $monthYear);
        }

        //Delete from ro_invoice_report
        $this->invoice_model->deleteInvoiceData($whereData);

        //Delete from Queue
        $this->invoice_model->deleteForInvoiceGeneration($ro_id, $monthYear);

        $data['status'] = "success";
        echo json_encode($data);

        //Delete from Queue
        //$this->invoice_model->deleteForInvoiceGeneration($ro_id,$monthYear) ;
    }

    /*public function getAllInvoiceNumber(){
        $invoice_details = $this->invoice_model->getInvoiceDetail($start_date,$end_date);
        $invoiceNumbers = array() ;
        foreach($invoice_details[$ro_id] as $key=>$invoiceValue){
            $roId_start_end_date = explode("-",$key) ;
            $roId_startDate = $roId_start_end_date[0] ;
            $roId_endDate = $roId_start_end_date[1] ;
            $getAllValues = $this->getRoData($roId_startDate,$roId_endDate,$start_date,$end_date) ;
            if($getAllValues) {
                foreach($invoiceValue as $value) {
                    $invoiceIds = explode(",",$value['id']);
                    if(count($invoiceIds) > 1) {
                        $sortedInvoiceIds = asort($invoiceIds) ;
                        $number = $sortedInvoiceIds[0] ;
                    }else{
                        $number = $invoiceIds[0] ;
                    }
                    
                    array_push($invoiceNumbers,$number) ;
                }
            }else{
                continue ;
            }
        }
        return $invoiceNumbers ;
    }*/

    public function deleteConsolidatedInvoiceValue($ro_id, $start_date, $end_date)
    {
        $month = date("m", strtotime($start_date));
        $year = date("Y", strtotime($start_date));

        $month_start_date = date("$year-$month-01");
        for ($i = strtotime($month_start_date); $i <= strtotime($end_date); $i = strtotime("+1 month", $i)) {

            $month_name = date("F", $i);
            $whereData = array('ro_id' => $ro_id, 'month_name' => $month_name);
            $this->invoice_model->updateForInvoiceGenerationFile(array('status' => 3), $whereData);

            //Get cancelled Data
            $cancelledData = $this->invoice_model->getRoInvoiceData($whereData);

            //store History
            $this->storeInvoiceHistory($cancelledData);

            //Delete from ro_invoice_report
            $this->invoice_model->deleteInvoiceData($whereData);

            //Delete from Queue
            $monthYear = date("F Y", $i);
            $this->invoice_model->deleteForInvoiceGeneration($ro_id, $monthYear);
        }
    }

    public function storeInvoiceHistory($data)
    {
        foreach ($data as $val) {
            $userData = array(
                'ro_id' => $val['ro_id'],
                'customer_ro' => $val['customer_ro'],
                'internal_ro' => $val['internal_ro'],
                'month_name' => $val['month_name'],
                'start_date' => $val['start_date'],
                'end_date' => $val['end_date'],
                'region_id' => $val['region_id'],
                'market_id' => $val['market_id'],
                'channel_id' => $val['channel_id'],
                'brand_id' => $val['brand_id'],
                'brand_name' => $val['brand_name'],
                'content_name' => $val['content_name'],
                'invoice_date' => $val['invoice_date'],
                'no_of_impression' => $val['no_of_impression'],
                'duration' => $val['duration'],
                'rate' => $val['rate'],
                'release_order_date' => $val['release_order_date'],
                'split_by_market' => $val['split_by_market'],
                'split_by_brand' => $val['split_by_brand'],
                'split_by_content' => $val['split_by_content'],
                'file_location' => $val['file_location'],
                'is_generated' => $val['is_generated'],
                'mail_sent' => $val['mail_sent'],
                'money_received' => $val['money_received'],
                'invoice_number' => $val['invoice_number']
            );
            $this->invoice_model->storeInvoiceHistory($userData);
        }
    }

    public function post_invoice_search_set()
    {
        $monthYear = $this->input->post('monthYear');
        $agency = $this->input->post('sel_agency');
        $invoice_str = $this->input->post('inv_search_str');
        $client = $this->input->post('sel_client');
        $search_by = $this->input->post('invoice_by');

        $month_year = explode(" ", $monthYear);
        $month = $month_year[0];

        if ($search_by == 'by_invoice_no') {
            $invoices = $this->invoice_model->searchInvoicesForCollectionByInvoiceStr($invoice_str);
        } else {
            $invoices = $this->invoice_model->getInvoicesForCollectionByAgencyClient($agency, $client, $month);
        }

        $html = "<table cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                    <th>Invoice No</th>
                    <th>Internal RO No.</th>
                    <th>Invoice Amount</th>
                    <th>Collection Date</th>
                    <th>Cheque No</th>
                    <th>Cheque Date</th>
                    <th>Amount Collected</th>
                    <th>TDS</th>
                    <th>Comment</th>
                    <th>#</th>
                </tr>";
        foreach ($invoices as $inv) {

            $collection_dates = explode(",", $inv['collection_date']);
            $cheque_nos = explode(",", $inv['cheque_no']);
            $cheque_dates = explode(",", $inv['cheque_date']);
            $amnt_collected = explode(",", $inv['amnt_collected']);
            $tds = explode(",", $inv['tds']);
            $comments = explode(",", $inv['comment']);

            $collection_count = count($collection_dates);
            $internal_ro = $inv['internal_ro'];
            //$invoice_no = "INV_".str_replace('/','_',$internal_ro)."_".$inv['invoice_number'];

            $html .= "<tr>
                        <td rowspan='$collection_count'>" . $inv['alias_invoice_number'] . "</td>
                        <td rowspan='$collection_count'>" . $internal_ro . "</td>
                        <td rowspan='$collection_count'>" . round($inv['invoice_amount'], 2) . "</td>";
            for ($i = 0; $i < $collection_count; $i++) {
                $html .= "<td>" . $collection_dates[$i] . "</td>
                                    <td>" . $cheque_nos[$i] . "</td>
                                    <td>" . $cheque_dates[$i] . "</td>
                                    <td>" . round($amnt_collected[$i], 2) . "</td>
                                    <td>" . round($tds[$i], 2) . "</td>
                                    <td>" . $comments[$i] . "</td>";
                if ($i == 0) {
                    $html .= '<td rowspan=' . $collection_count . '><input type="button" value="Payment" class="_button" onclick="update_collections(' . "'" . $inv['alias_invoice_number'] . "'" . ')" ></td>';
                }
                $html .= "</tr>";
            }
        }
        $html .= "</table>";

        if (count($invoices) > 0) {
            echo $html;
        } else {
            echo "<h2 style='text-align:center;'>No Invoices Found !</h2>";
        }


    }

    public function update_invoice_payment($invoice_no)
    {
        /*$monthYear = urldecode($monthYear);
        $month_year = explode(" ",$monthYear) ;
        $month = $month_year[0];*/

        $invoice_no = urldecode($invoice_no);

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $menu = $this->menu_model->get_header_menu();
        $invoice_details = $this->invoice_model->getInvoicesForCollectionByInvoiceNo($invoice_no);
        $amount_collected = $this->invoice_model->getAmountCollectedForInvoices($invoice_no);

        $amount_remaining = $invoice_details[0]['invoice_amount'] - $amount_collected[0]['amount_collected'];

        /*if(count($invoice_details) > 0){

        }*/

        if ($amount_remaining == 0) {
            echo '<script>parent.jQuery.colorbox.close();
                    alert("Invoice amount already collected");</script>';
            return false;
        }

        $model = array();
        $model['logged_in_user'] = $logged_in[0];
        $model['menu'] = $menu;
        $model['invoice_details'] = $invoice_details;
        $model['amount_remaining'] = $amount_remaining;

        $this->load->view('invoice_manager/update_invoice_payment', $model);
    }

    public function post_invoice_collection()
    {

        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");

        $invoice_no = $this->input->post('txt_inv_no');
        $invoice_alias = $this->input->post('txt_inv_alias');
        $invoice_info = $this->invoice_model->getInvoiceFileDetails($invoice_alias);
        $mode_of_payment = $this->input->post('radio_mode');
        $issued_by = $this->input->post('txt_issued_by');
        $collection_date = $this->input->post('txt_collection_date');
        $cheque_no = $this->input->post('txt_cheque_no');
        $cheque_date = $this->input->post('txt_cheque_date');
        $amnt_collected = $this->input->post('txt_amnt_collected');
        $tds = $this->input->post('txt_tds');
        $comment = $this->input->post('txt_comment');

        $ro_id = $invoice_info[0]['ro_id'];
        $roData = $this->ro_model->get_am_ro_id($ro_id);
        $external_ro_number = $roData[0]['cust_ro'];

        $update_data = array(
            'ro_id' => $ro_id,
            'ext_ro' => $external_ro_number,
            'user_id' => $logged_in[0]['user_id'],
            'invoice_no' => $invoice_alias,
            'collection_date' => $collection_date,
            'mode_of_payment' => $mode_of_payment,
            'cheque_no' => $cheque_no,
            'cheque_issued_by' => $issued_by,
            'cheque_date' => $cheque_date,
            'amnt_collected' => $amnt_collected,
            'tds' => $tds,
            'comment' => $comment
        );
        /*$where_data = array(
            'ro_id'=> $invoice_info[0]['ro_id'],
            'invoice_no'=> $invoice_no
        );*/

        $this->invoice_model->updatePaymentsForInvoice($update_data);

        $ro_details = $this->am_model->ro_detail_for_ro_id($invoice_info[0]['ro_id']);

        $amount_collected = $this->invoice_model->getAmountCollectedForInvoices($invoice_alias);
        $amount_remaining = $invoice_info[0]['invoice_amount'] - $amount_collected[0]['amount_collected'];

        //getting email_ids of reciepents
        $email_data = $this->user_model->userEmailForRoCreation($ro_details[0]['user_id']);
        $am_details = $this->user_model->get_user_detail_for_user_id($ro_details[0]['user_id']);

        $to_user = $am_details[0]['user_email'];
        $cc_user = "nitish@surewaves.com" . "," . $email_data;
        email_send($to_user,
            $cc_user,
            "invoice_collection",
            array('INVOICE_NUMBER' => $invoice_alias),
            array(
                'EXTERNAL_RO_NUMBER' => $ro_details[0]['cust_ro'],
                'INVOICE_NUMBER' => $invoice_alias,
                'AGENCY_NAME' => $invoice_info[0]['agency_name'],
                'CLIENT_NAME' => $invoice_info[0]['client_name'],
                'INVOICE_AMOUNT' => $invoice_info[0]['invoice_amount'],
                'AMOUNT_COLLECTED' => $amnt_collected,
                'AMOUNT_REMAINING' => $amount_remaining,
                'MODE_OF_PAYMENT' => $mode_of_payment,
                'COLLECTION_DATE' => date('d-m-Y', strtotime($collection_date)),
                'AM_NAME' => $am_details[0]['user_name']
            )
        );

        echo '<script>parent.jQuery.colorbox.close();</script>';
    }

    public function post_InvoiceDataForGeneration()
    {
        $roIds = $this->input->post('ro_id');
        $split_by_market = $this->input->post('hid_split_by_market');
        $split_by_brand = $this->input->post('hid_split_by_brand');
        $split_by_content = $this->input->post('hid_split_by_content');
        $monthYear = $this->input->post('month');

        $splitCriteria = $split_by_market . "" . $split_by_brand . "" . $split_by_content;
        foreach ($roIds as $val) {
            $ro_id = $val;
            $insertData = array('ro_id' => $ro_id, 'month_year' => $monthYear, 'splitCriteria' => $splitCriteria, 'is_generated' => 2);
            $updateData = array('splitCriteria' => $splitCriteria, 'is_generated' => 2);
            $whereData = array('ro_id' => $ro_id, 'month_year' => $monthYear, 'splitCriteria' => $splitCriteria);

            $getData = $this->invoice_model->getInvoiceGeneration($whereData);
            if (count($getData) > 0) {
                $this->invoice_model->updateForInvoiceGeneration($updateData, $whereData);
            } else {
                $this->invoice_model->insertForInvoiceGeneration($insertData);
            }
        }


        exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /invoice_manager/cronForInvoiceGeneration > /dev/null &");

        $data = array();
        $data['status'] = 'success';
        $data['ro_id'] = implode(",", $roIds);
        echo json_encode($data);
    }

    public function cronForInvoiceGeneration()
    {
        $whereData = array('is_generated' => 2);
        $getData = $this->invoice_model->getInvoiceGeneration($whereData);

        foreach ($getData as $val) {
            $id = $val['id'];
            $ro_id = $val['ro_id'];
            $this->invoice_model->updateForInvoiceGeneration(array('is_generated' => 4), array('id' => $id));

            $monthYear = $val['month_year'];
            $splitCriteria = str_split($val['splitCriteria']);

            $split_by = array(
                'split_by_market' => $splitCriteria[0],
                'split_by_brand' => $splitCriteria[1],
                'split_by_content' => $splitCriteria[2]
            );

            $ro_details = $this->am_model->get_ro_details($ro_id);

            $internal_ro_number = $ro_details[0]['internal_ro'];

            $this->invoice_model->getStartAndEndDateForInvoiceGeneration($internal_ro_number, $split_by, $monthYear);

            $billingCycle = $this->invoice_model->getBillingCyclePayment($internal_ro_number);
            $consolidatedBill = FALSE;

            if ($billingCycle == 'Consolidated') {
                $consolidatedBill = TRUE;
            }

            if (!$consolidatedBill) {
                $invoiceValues = $this->invoice_model->getInvoiceValuesForRoAndMonth($monthYear, $ro_id);
                $this->saveInvoicePdf($invoiceValues, $monthYear, $id);
            } else {
                $invoiceValues = $this->invoice_model->getConsolidatedInvoiceValuesForRo($ro_id);
                $rostartEndDate = $this->invoice_model->getStartEndDateForRo($internal_ro_number);
                $this->saveConsolidatedInvoicePdf($invoiceValues, $rostartEndDate['start_date'], $rostartEndDate['end_date'], $id);
            }

        }
    }

    /*public function cancel_download_invoice(){
        $this->is_logged_in ();
            $logged_in = $this->session->userdata("logged_in_user");
            $menu = $this->menu_model->get_header_menu() ;

            $month = $this->input->post('month');
            if(isset($month) && $month != null){
                    $month = $this->input->post('month');
            }else{
                    $month = urldecode($sel_month);
            }

            $start_d = date('Y-m-d',strtotime($month));
            $end_d = date('Y-m-t',strtotime($month));

            $monthName = date("F",strtotime($month)) ;
            $status = 3 ;
            $invoice_list = $this->invoice_model->getInvoiceData($monthName,$status) ;


            $model=array();
            $model['logged_in_user']=$logged_in[0];
            $model['menu'] = $menu ;
        //$model['ro_list'] = $invoice_list;

            $total_rows = count($invoice_list);
        // Pagination to show internal_ro_numbers
            $filtered_ros = array_splice($invoice_list, $start_index, 20);//print_r($invoice_lists);exit;
            $model['invoice_list'] = $filtered_ros;//echo '<pre>';print_r($model['ro_list']);exit;
            $model['page_links'] = create_page_links (base_url()."invoice_manager/download_invoice/".$month, 20, $total_rows);
            $model['month'] = $month;

            $this->load->view('invoice_manager/cancel_download_invoice',$model);
    } */

    public function saveConsolidatedInvoicePdf($invoiceValues, $start_date, $end_date, $invoiceQueueId)
    {
        $monthName = date("F", strtotime($end_date));

        foreach ($invoiceValues as $value) {
            $invoice_ids = $value['id'];
            $ro_id = $value['ro_id'];
            //array_push($roIds,$ro_id) ;

            $this->invoice_model->updateRoInvoiceDataForInvoiceIds($invoice_ids, 2);

            $this->createDirectoryForRo($ro_id);
            //Generate Invoice Number
            $invoiceNumber = $this->invoice_model->generateInvoiceNumber($ro_id, $monthName);

            $invoice_details = $this->invoice_model->get_splitted_invoice_details($invoice_ids, $start_date, $end_date);
            $model['invoice_details'] = $invoice_details;
            $model['split_criteria'] = $value['split_by_market'] . "" . $value['split_by_brand'] . "" . $value['split_by_content'];
            $model['month_name'] = $monthName;
            $model['invoice_number'] = $invoiceNumber['invoiceNumber'];
            $model['invoiceFormat'] = $invoiceNumber['invoice_format'];
            $model['invoice_date'] = $end_date;

            if (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT'])) {
                $documentRoot = '/opt/lampp/htdocs';
            } else {
                $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            }

            $model['document_root'] = $documentRoot;
            $this->load->view('ro_manager/tax_invoice', $model);
        }
        $this->invoice_model->updateForInvoiceGeneration(array('is_generated' => 1), array('id' => $invoiceQueueId));
        //store consolidated queuing cycle into ro_invoice_generation_queue
        $this->storeConsolidatedInvoiceQueue($invoiceQueueId, $start_date, $end_date);
    }

    public function storeConsolidatedInvoiceQueue($invoiceQueueId, $start_date, $end_date)
    {
        $month = date("m", strtotime($start_date));
        $year = date("Y", strtotime($start_date));

        $month_start_date = date("$year-$month-01");
        $invoiceQueueData = $this->invoice_model->getInvoiceGeneration(array('id' => $invoiceQueueId));
        for ($i = strtotime($month_start_date); $i <= strtotime($end_date); $i = strtotime("+1 month", $i)) {
            /*
            if(date("m",$i)== date("m")) {
                $end_date = date("Y-m-t") ;
            }else {
                $end_date = date("Y-m-t",strtotime("+1 month -1 days", $i)) ;
            } */

            $monthYear = date("F Y", $i);

            $ro_id = $invoiceQueueData[0]['ro_id'];
            $monthYearFromTable = $invoiceQueueData[0]['month_year'];
            $splitCriteria = $invoiceQueueData[0]['splitCriteria'];

            if ($monthYear == $monthYearFromTable) {
                continue;
            } else {
                $this->invoice_model->insertForInvoiceGeneration(array('ro_id' => $ro_id, 'month_year' => $monthYear, 'splitCriteria' => $splitCriteria, 'is_generated' => 1));
            }
        }
    }

}

/* End of file non_fct_ro.php */
/* Location: ./application/controllers/non_fct_ro.php */
