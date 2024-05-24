<?php
if (!defined('BASEPATH'))
    exit("NO Direct Script Access Allowed");

/*
 * Class for Spot Tv Reach 
 * spot_tv_reach.php
 *
 * Author : Biswa Bijayee
 * Email  : biswabijayee@surewaves.com
 */

class Spot_tv_reach extends CI_Controller
{

    /*
     * Constructor Method
     */
    public $objPHPexcel;

    public function __construct()
    {
        parent::__construct();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

        //Loading Configs
        $this->objPHPexcel = '';
        $this->sheetcount = 0;
        $this->load->config('form_validation');

        //Loading Models
        $this->load->model('user_model');
        $this->load->model('ro_model');
        $this->load->model('mg_model');
        $this->load->model('am_model');
        $this->load->model("menu_model");
        $this->load->model("ns_model");
        $this->load->model("network_model");

        //Loading Libraries
        $this->load->library('session');
        $this->load->library('curl');
        $this->load->library('form_validation');
        $this->load->library('pagination');

        //Loading Helpers
        $this->load->helper("app_validation_helper");
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->helper('url');

        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    public function get_reach_report()
    {
        //Checking Log-In details
        $this->is_logged_in();
        $logged_in = $this->session->userdata("logged_in_user");
        $profile_id = $logged_in[0]['profile_id'];
        $profile_details = $this->user_model->get_profile_details($profile_id);
        $menu = $this->menu_model->get_header_menu();

        $data = array();
        $data['logged_in_user'] = $logged_in[0];
        $data['profile_details'] = $profile_details[0];
        $data['menu'] = $menu;
        $data['tam_overview'] = $this->network_model->getTamOverViewData();
        $data['market_data'] = $this->network_model->getMarketWiseData();
        /*echo "<pre>";
        print_r($data['market_data']);
        exit;*/

        $this->load->view('network_manager/spottv_reach', $data);
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

    public function save_reach_data()
    {
        $tam_data['total_households'] = $this->input->post('total_households');
        $tam_data['total_tv_households'] = $this->input->post('total_tv_households');
        $tam_data['total_cns_households'] = $this->input->post('total_cable_sat_households');
        $this->network_model->save_spotTvReach_data('sv_tam_overview', $tam_data, 'id', 1);

        $market_id_listArr = explode(",", $this->input->post('market_id_list'));
        foreach ($market_id_listArr as $market_id_listArrVal) {

            $data['tv_house_holds'] = $this->input->post('tvh_' . $market_id_listArrVal);
            $data['cns_house_holds'] = $this->input->post('cns_' . $market_id_listArrVal);
            $data['ctv_dth_house_holds'] = $this->input->post('cabletv_' . $market_id_listArrVal);
            $data['sw_partner'] = $this->input->post('sw_partner_' . $market_id_listArrVal);
            $data['sw_reach'] = $this->input->post('share_' . $market_id_listArrVal);
            $this->network_model->save_spotTvReach_data('sv_sw_market', $data, 'id', $market_id_listArrVal);
        }
        //$this->generate_reach_report($this->input->post('excluded_Channel_ids'));
        redirect(base_url("spot_tv_reach/get_reach_report/"));

    }

    public function generate_reach_report()
    {
        $excluded_Channel_ids = $this->input->post('excluded_Channel_ids');
        $channel_idList = explode(",", $excluded_Channel_ids);
        $excel_dataArr = $this->network_model->generateReachReport(trim($excluded_Channel_ids));
        $startdate = date("Y-m-d", strtotime("-1 week"));
        $enddate = date("Y-m-d");
        $channel_list = $this->ns_model->get_ping_status_for_ReachSheet($startdate, $enddate);
        foreach ($channel_list as $channel_data) {
            $channel_list_with_ping[] = $channel_data['channel_id'];
        }

        $this->load->library('phpexcel');
        $this->load->library('PHPExcel/PHPExcel_IOFactory');
        $colorArray = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => '538ED5'),
        );
        $colorArray1 = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'FAC090'),
        );

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                    'color' => array('rgb' => '538ED5')
                )
            )
        );
        $headingBorder_styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                    'color' => array('rgb' => 'FFFFFF')
                )
            )
        );
        $fontStyleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 10,
                'name' => 'Verdana'
            )
        );

        $headingStyleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $this->sheetcount = $count = 0;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex($count);
        $title = 'Spot Tv Reach Data';
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $objPHPExcel->getDefaultStyle()->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            )
        );
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(63);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(11);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(11);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12);


        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'SUREWAVES NATIONAL SPOT TV REACH REPORT GENERATED ON ' . date("d-m-Y"));
        $objPHPExcel->getActiveSheet()->mergeCells('B1:C1');
        $objPHPExcel->getActiveSheet()->getStyle('B1:C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B1:C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1:C1')->applyFromArray($headingStyleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'DATA REGARDING CABLE AND SATELLITE HOUSEHOLDS IN INDIA - IRS');
        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($headingStyleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'OVERVIEW OF INDIAN TELEVISION MARKET (TAM)');
        $objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($headingStyleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('B5', 'TOTAL HOUSEHOLDS');
        $objPHPExcel->getActiveSheet()->setCellValue('B6', 'TOTAL TV HOUSEHOLDS');
        $objPHPExcel->getActiveSheet()->setCellValue('B7', 'TOTAL CABLE & SATELLITE HOUSEHOLDS');


        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Source : IRS 2012/2013');
        $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($headingStyleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', 'IN MILLIONS');
        $objPHPExcel->getActiveSheet()->getStyle('C4')->applyFromArray($headingStyleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('C5', $excel_dataArr[1][0]['total_households']);
        $objPHPExcel->getActiveSheet()->setCellValue('C6', $excel_dataArr[1][0]['total_tv_households']);
        $objPHPExcel->getActiveSheet()->setCellValue('C7', $excel_dataArr[1][0]['total_cns_households']);
        $objPHPExcel->getActiveSheet()->getStyle('B3:C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B3:C7')->applyFromArray($styleArray);

        $objPHPExcel->getActiveSheet()->getStyle('A10:J11')->getFill()->applyFromArray($colorArray);
        $objPHPExcel->getActiveSheet()->setCellValue('E10', 'Households IN MILLIONS');
        $objPHPExcel->getActiveSheet()->mergeCells('E10:G10');
        $objPHPExcel->getActiveSheet()->getStyle('E10:G10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E10:G10')->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(55);


        $objPHPExcel->getActiveSheet()->setCellValue('A11', 'State');
        $objPHPExcel->getActiveSheet()->setCellValue('B11', 'Network Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C11', 'Channel Name');
        $objPHPExcel->getActiveSheet()->setCellValue('D11', 'Market');
        $objPHPExcel->getActiveSheet()->setCellValue('E11', 'TV HH');
        $objPHPExcel->getActiveSheet()->setCellValue('F11', 'C&S');
        $objPHPExcel->getActiveSheet()->setCellValue('G11', 'Cable TV (C&S minus DTH)');
        $objPHPExcel->getActiveSheet()->setCellValue('H11', 'SW PARTNER *');
        $objPHPExcel->getActiveSheet()->setCellValue('I11', '% SHARE');
        $objPHPExcel->getActiveSheet()->setCellValue('J11', 'Analytics');
        $objPHPExcel->getActiveSheet()->getStyle('A11:J11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A11:J11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A11:J11')->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A11:J11')->getAlignment()->setWrapText(true);


        $objPHPExcel->getActiveSheet()->setCellValue('L11', 'Channel Name');
        $objPHPExcel->getActiveSheet()->setCellValue('M11', 'Rate/10Sec');
        $objPHPExcel->getActiveSheet()->setCellValue('N11', 'Market');
        $objPHPExcel->getActiveSheet()->getStyle('L11:N11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('L11:N11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('L11:N11')->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('L11:N11')->getFill()->applyFromArray($colorArray);
        $objPHPExcel->getActiveSheet()->getStyle('L11:N11')->applyFromArray($headingBorder_styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('L11:N11')->getAlignment()->setWrapText(true);


        $startCell = 'A';
        $endCell = 'J';
        $startRowNo = $startTempRowNo = 12;

        $startHIMDataCell = 'E';
        $sheetStrArr = explode(",", $this->input->post('filtered_sheets'));
        foreach ($sheetStrArr as $sheet_val) {
            $tempArr = explode("_", $sheet_val);
            $sheetArr[$tempArr[0]] = $tempArr[1];
        }
        foreach ($excel_dataArr[0] as $val) {
            $startHIMDataCell = 'E';
            switch ($val['id']) {
                case 12:
                    $val['state'] .= ' and Telengana';
                    break;
                case 14:
                    $val['state'] .= ' and Suburbs';
                    break;
                case 15:
                    $val['state'] .= ' and NCR';
                    break;
            }
            $objPHPExcel->getActiveSheet()->setCellValue($startCell . $startTempRowNo, strtoupper($val['state']));
            $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo)->applyFromArray($headingStyleArray);
            $objPHPExcel->getActiveSheet()->setCellValue($startHIMDataCell . $startTempRowNo, $val['tvHH']);
            $objPHPExcel->getActiveSheet()->getStyle($startHIMDataCell . $startTempRowNo)->applyFromArray($headingStyleArray);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startHIMDataCell) . $startTempRowNo, $val['cns']);
            $objPHPExcel->getActiveSheet()->getStyle($startHIMDataCell . $startTempRowNo)->applyFromArray($headingStyleArray);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startHIMDataCell) . $startTempRowNo, $val['cabletv']);
            $objPHPExcel->getActiveSheet()->getStyle($startHIMDataCell . $startTempRowNo)->applyFromArray($headingStyleArray);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startHIMDataCell) . $startTempRowNo, $val['sw_partner']);
            $objPHPExcel->getActiveSheet()->getStyle($startHIMDataCell . $startTempRowNo)->applyFromArray($headingStyleArray);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startHIMDataCell) . $startTempRowNo, (ceil(($val['sw_partner'] / $val['cabletv']) * 100)));
            $objPHPExcel->getActiveSheet()->getStyle($startHIMDataCell . $startTempRowNo)->applyFromArray($headingStyleArray);

            $channel_Idarr = explode(",", $val['channel_id']);
            $channel_arr = explode(",", $val['channel_names']);
            $market = explode(",", $val['market']);
            $network_name = explode(",", $val['network_name']);
            $customer_name = explode(",", $val['customer_name']);
            $tempRowNo = $startTempRowNo;

            $startHIMDataCell++;
            foreach ($channel_Idarr as $key => $value) {
                $status = 'offline';
                $temPstartCell = $startCell;
                //  $tempRowNo = $tempRowNo+1;
                $res = $this->ns_model->getCustomerNameOrDisplayName($value);
                if (in_array($value, $channel_list_with_ping)) {
                    $status = 'online';
                }
                if ($sheetArr[$val['id']] != $status && $sheetArr[$val['id']] != 'both') {
                    continue;
                }
                $tempRowNo = $tempRowNo + 1;
                if ($res[0]['network_name'] != '') {
                    $objPHPExcel->getActiveSheet()->setCellValue((++$temPstartCell) . $tempRowNo, $res[0]['network_name']);
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue((++$temPstartCell) . $tempRowNo, $res[0]['customer_name']);
                }

                $objPHPExcel->getActiveSheet()->setCellValue((++$temPstartCell) . $tempRowNo, $res[0]['channel_names']);
                $objPHPExcel->getActiveSheet()->setCellValue((++$temPstartCell) . $tempRowNo, $res[0]['market']);
                /*                if(in_array($value,$channel_list_with_ping)){
                                    $status = 'online';
                                }*/
                $objPHPExcel->getActiveSheet()->setCellValue($startHIMDataCell . $tempRowNo, $status);
            }
            $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . ($startHIMDataCell) . $startTempRowNo)->getFill()->applyFromArray($colorArray1);
            $startTempRowNo = $tempRowNo + 2;

        }
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startRowNo . ':' . $endCell . ($startTempRowNo - 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startRowNo . ':' . $endCell . ($startTempRowNo - 2))->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->freezePane($startCell . $startRowNo);

        //
        $startCell = $startTempCell = 'L';
        $endCell = 'N';
        $startRowNo = $startTempRowNo = 12;
        foreach ($excel_dataArr[2] as $val) {
            $startTempCell = 'L';
            $objPHPExcel->getActiveSheet()->setCellValue($startTempCell . $startTempRowNo, $val['display_name']);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startTempCell) . $startTempRowNo, $val['rate']);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startTempCell) . $startTempRowNo, $val['market']);
            $startTempRowNo = $startTempRowNo + 1;
        }
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startRowNo . ':' . ($endCell) . ($startTempRowNo - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startRowNo . ':' . ($endCell) . ($startTempRowNo - 1))->applyFromArray($styleArray);

        $startCell = $startTempCell = 'L';
        $endCell = 'P';
        $startTempRowNo = $startTempRowNo + 1;
        $startRowNo = $startTempRowNo;

        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . $endCell . ($startTempRowNo + 1))->getFill()->applyFromArray($colorArray);
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . $endCell . ($startTempRowNo + 1))->applyFromArray($headingBorder_styleArray);
        $objPHPExcel->getActiveSheet()->mergeCells($startCell . $startTempRowNo . ':' . $endCell . $startTempRowNo);
        $objPHPExcel->getActiveSheet()->setCellValue($startCell . $startTempRowNo, 'Regional Satellite');
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . $endCell . $startTempRowNo)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . $endCell . $startTempRowNo)->applyFromArray($fontStyleArray);
        $startTempRowNo = $startTempRowNo + 1;
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $startTempRowNo, 'Channel Name');
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $startTempRowNo, 'Rate/10Sec');
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $startTempRowNo, 'Market');
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $startTempRowNo, 'Channel Share');
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $startTempRowNo, 'Analytics');
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . $endCell . $startTempRowNo)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . $endCell . $startTempRowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . $endCell . $startTempRowNo)->applyFromArray($fontStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startTempRowNo . ':' . $endCell . $startTempRowNo)->getAlignment()->setWrapText(true);
        $startTempRowNo = $startTempRowNo + 1;
        foreach ($excel_dataArr[3] as $val) {
            $startTempCell = 'L';
            $objPHPExcel->getActiveSheet()->setCellValue($startTempCell . $startTempRowNo, $val['display_name']);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startTempCell) . $startTempRowNo, $val['rate']);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startTempCell) . $startTempRowNo, $val['market']);
            $objPHPExcel->getActiveSheet()->setCellValue((++$startTempCell) . $startTempRowNo, $val['channel_share']);
            $status = 'offline';
            if (in_array($val['channel_id'], $channel_list_with_ping)) {
                $status = 'online';
            }
            $objPHPExcel->getActiveSheet()->setCellValue((++$startTempCell) . $startTempRowNo, $status);
            $startTempRowNo = $startTempRowNo + 1;
        }
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startRowNo . ':' . ($endCell) . ($startTempRowNo - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($startCell . $startRowNo . ':' . ($endCell) . ($startTempRowNo - 1))->applyFromArray($styleArray);


        $this->objPHPexcel = $objPHPExcel;
        $finalParsedReachArr = array();
        $district_reach = $this->network_model->getDistrictReachData($excluded_Channel_ids);
        $finalParsedReachArr = $this->parseDBArray($district_reach, $channel_list_with_ping, $sheetArr);
        //echo "<pre>";print_r($finalParsedReachArr);exit;
        if (count($finalParsedReachArr['online']) > 0) {
            $this->createMultipleReachSheets($finalParsedReachArr['online'], 'online');
        }
        if (count($finalParsedReachArr['offline']) > 0) {
            $this->createMultipleReachSheets($finalParsedReachArr['offline'], 'offline');
        }
        $objPHPExcel = $this->objPHPexcel;
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="spot_tv_reach.xls"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=spot_tv_reach.xls');
        header('Content-Transfer-Encoding: binary');
        ob_clean();
        flush();
        $objWriter->save('php://output');
        redirect(base_url("spot_tv_reach/get_reach_report/"));

    }

    public function parseDBArray($district_reach, $channel_list_with_ping, $sheetArr)
    {

        $newArr = array();
        $state_id = '';
        $stateMarketArr = array();
        foreach ($district_reach as $district_reachVal) {
            $strictToSheet = 0;// this status is used for strictly checking what user has clicked either "online" or "offline" not both.
            if ($state_id != $district_reachVal['channel_id']) {
                $districtArr = array();
                $state_id = $district_reachVal['channel_id'];
            }
            $state_id = $district_reachVal['state_id'];
            if ($district_reachVal['channel_id'] == NUll || $district_reachVal['channel_id'] == '') {
                $newArr['online'][$district_reachVal['state_id']]['state_name'] = $district_reachVal['state_name'];
                //$newArr['online'][$district_reachVal['state_id']]['markets'][$district_reachVal['market_id']]['market_name'] = $district_reachVal['market_name'];

                $newArr['offline'][$district_reachVal['state_id']]['state_name'] = $district_reachVal['state_name'];
                //$newArr['offline'][$district_reachVal['state_id']]['markets'][$district_reachVal['market_id']]['market_name'] = $district_reachVal['market_name'];

                $districtArr['district_name'] = $district_reachVal['district_name'];
                $districtArr['customer']['000']['customer_name'] = 'NOT PRESENT';
                $districtArr['customer']['000']['channel']['000']['channel_name'] = 'NOT PRESENT';

                $newArr['online'][$district_reachVal['state_id']]['districts'][$district_reachVal['district_id']] = $districtArr;
                $newArr['offline'][$district_reachVal['state_id']]['districts'][$district_reachVal['district_id']] = $districtArr;
            } else {
                if (in_array($district_reachVal['channel_id'], $channel_list_with_ping)) {
                    $onlineOrOffline = 'online';
                } else {
                    $onlineOrOffline = 'offline';
                }
                $marketList = $this->network_model->getMarketListForStates($state_id);
                foreach ($marketList as $market) {
                    $marketId = $market['id'];
                    if (!array_key_exists($marketId, $sheetArr)) {
                        continue;
                    }
                    if ($sheetArr[$marketId] != $onlineOrOffline && $sheetArr[$marketId] != 'both') {
                        $strictToSheet = 1;
                        break;
                    }
                }


                if ($strictToSheet) {
                    continue;
                }
                $newArr[$onlineOrOffline][$district_reachVal['state_id']]['state_name'] = $district_reachVal['state_name'];
                //$newArr[$onlineOrOffline][$district_reachVal['state_id']]['markets'][$district_reachVal['market_id']]['market_name'] = $district_reachVal['market_name'];
                $districtArr = $newArr[$onlineOrOffline][$district_reachVal['state_id']]['districts'][$district_reachVal['district_id']];

                $districtArr['district_name'] = $district_reachVal['district_name'];
                $districtArr['customer'][$district_reachVal['customer_id']]['customer_name'] = $district_reachVal['network_name'];
                $districtArr['customer'][$district_reachVal['customer_id']]['channel'][$district_reachVal['channel_id']]['channel_name'] = $district_reachVal['channel_name'];

                $newArr[$onlineOrOffline][$district_reachVal['state_id']]['districts'][$district_reachVal['district_id']] = $districtArr;
            }

        }
        return $newArr;

    }

    public function createMultipleReachSheets($array_ReachData, $status)
    {
        $colorArray = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => '538ED5'),
        );
        $colorArray1 = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'FAC090'),
        );

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                    'color' => array('rgb' => '538ED5')
                )
            )
        );
        $headingBorder_styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                    'color' => array('rgb' => 'FFFFFF')
                )
            )
        );
        $fontStyleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 10,
                'name' => 'Verdana'
            )
        );

        $headingStyleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri'
            )
        );

        $this->sheetcount++;
        if ($this->sheetcount != 0) {
            $this->objPHPexcel->createSheet();
        }
        $this->objPHPexcel->setActiveSheetIndex($this->sheetcount);
        $title = "District reach $status channels";
        $this->objPHPexcel->getActiveSheet()->setTitle($title);
        $this->objPHPexcel->getDefaultStyle()->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            )
        );
        $this->objPHPexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        //$this->objPHPexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->objPHPexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->objPHPexcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $this->objPHPexcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);


        $this->objPHPexcel->getActiveSheet()->getStyle('A2:D2')->getFill()->applyFromArray($colorArray);
        $this->objPHPexcel->getActiveSheet()->setCellValue('A2', 'State');
        // $this->objPHPexcel->getActiveSheet()->setCellValue('B2','Markets');
        $this->objPHPexcel->getActiveSheet()->setCellValue('B2', 'District');
        $this->objPHPexcel->getActiveSheet()->setCellValue('C2', 'Network');
        $this->objPHPexcel->getActiveSheet()->setCellValue('D2', 'Channel');


        $colArr['State'] = 'A';
        //$colArr['Markets'] 		= 'B';
        $colArr['District'] = 'B';
        $colArr['Network'] = 'C';
        $colArr['Channel'] = 'D';


        $this->objPHPexcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->objPHPexcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->objPHPexcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($fontStyleArray);
        $this->objPHPexcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($headingBorder_styleArray);
        $this->objPHPexcel->getActiveSheet()->getRowDimension(2)->setRowHeight(55);
        $this->objPHPexcel->getActiveSheet()->freezePane('A3');

        $startCell = $startTempCell = 'A';
        $endCell = 'D';
        $startRowNo = $startRow = $startTempRowNo = 3;
        foreach ($array_ReachData as $stateWise) {
            $state_name = $stateWise['state_name'];
            $state_name = str_replace("&", "AND", $state_name);
            /* foreach($stateWise['markets'] as $marketWise){
                $market_name = $marketWise['market_name']; */

            foreach ($stateWise['districts'] as $districtWise) {
                $district_name = $districtWise['district_name'];

                $network_name_strArr = array();
                $channel_strArr = array();
                foreach ($districtWise['customer'] as $NetworkWise) {
                    if (!in_array($NetworkWise['customer_name'], $network_name_strArr)) {
                        $network_name_strArr[] = $NetworkWise['customer_name'];
                    }
                    //echo "<pre>";print_r($NetworkWise);
                    /* if($network_name_str == ''){
                        $network_name_str 	= $NetworkWise['customer_name'];
                    }else if($NetworkWise['customer_name'] !== $network_name){
                        $network_name_str .= ",".$NetworkWise['customer_name'];
                    }  */

                    //$network_name		= $NetworkWise['customer_name'];
                    foreach ($NetworkWise['channel'] as $channelWiseInfo) {
                        /* if($channel_str == ''){
                           $channel_str 	= $channelWiseInfo['channel_name'];
                       }else if($channelWiseInfo['channel_name'] !== $channel_name){
                           $channel_str .= ",".$channelWiseInfo['channel_name'];
                       }  */
                        if (!in_array($channelWiseInfo['channel_name'], $channel_strArr)) {
                            $channel_strArr[] = $channelWiseInfo['channel_name'];
                        }
                        /*$channel_name		= $channelWiseInfo['channel_name'];
                        $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['State'].$startTempRowNo,$state_name);
                        $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['Markets'].$startTempRowNo,$market_name);
                        $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['District'].$startTempRowNo,$district_name);
                        $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['Network'].$startTempRowNo,$network_name);
                        $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['Channel'].$startTempRowNo,$channel_name);
                        $startTempRowNo++;*/
                    }
                }
                //$state_name = str_replace("&","AND",$state_name);
                $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['State'] . $startTempRowNo, $state_name);
                //$this->objPHPexcel->getActiveSheet()->setCellValue($colArr['Markets'].$startTempRowNo,$market_name);
                $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['District'] . $startTempRowNo, $district_name);
                $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['Network'] . $startTempRowNo, implode(",", $network_name_strArr));
                $this->objPHPexcel->getActiveSheet()->setCellValue($colArr['Channel'] . $startTempRowNo, implode(",", $channel_strArr));
                $startTempRowNo++;
                //}
            }

            $this->objPHPexcel->getActiveSheet()->getStyle($startCell . '1:' . $endCell . $startTempRowNo)->getAlignment()->setWrapText(true);
            $this->objPHPexcel->getActiveSheet()->getStyle($startCell . '1:' . $endCell . $startTempRowNo)->getAlignment()->setWrapText(true);
            $this->objPHPexcel->getActiveSheet()->getStyle($startCell . '1:' . $endCell . $startTempRowNo)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->objPHPexcel->getActiveSheet()->getStyle($startCell . '1:' . $endCell . $startTempRowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->objPHPexcel->getActiveSheet()->getStyle($startCell . $startRow . ':' . $endCell . $startTempRowNo)->applyFromArray($styleArray);

        }
        //exit;

    }
}
