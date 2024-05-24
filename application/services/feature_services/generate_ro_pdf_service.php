<?php
/**
 * Created by PhpStorm.
 * Author: Yash Bansal
 * Date: November, 2019
 */

namespace application\services\feature_services;

use application\services\common_services\HtmlToPdfService;

include_once APPPATH . 'services/common_services/html_to_pdf_service.php';

class GenerateRoPdfService
{
    private $CI;
    private $htmlToPdfServiceObj;

    /**
     * Author: Yash
     * Date: November, 2019
     *
     * GenerateRoPdfService constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->htmlToPdfServiceObj = new HtmlToPdfService();
    }

    public function try_pdf()
    {
        $fileName = "test_pdf" . "_" . date('Y-m-d') . ".pdf";
        //$fileLocation = $_SERVER['DOCUMENT_ROOT'] . "/surewaves_easy_ro/pdfReports/" . $fileName;
	$fileLocation = "/opt/lampp/htdocs/surewaves_easy_ro/pdfReports/" . $fileName;
        log_message("info",'In GenerateRoPdfService@try_pdf | File Location '.print_r($fileLocation,True));

        $html = "<html>
	<head></head>
	<body>
		<h1> Hi <h1>
		<br>
		<p> this is p</p>
	</body>
</html>";
	try
	{
        	$this->htmlToPdfServiceObj->convertHtmlToPdf($html, $fileLocation);
        	log_message("info",'In GenerateRoPdfService@try_pdf | Exit!!');
	}catch(Exception $e){
		throw $e;
		
	}
    }

    /**
     * Author: Yash
     * Date: November, 2019
     *
     * @param $data
     * Description: Called From generate_ro_pdf Cron and Prepare pdf using HtmlToPdfService
     */
    public function GeneratePdf($data)
    {
        log_message('info','In GenerateRoPdfService@GeneratePdf | Entered');
        $html = $this->getHtml($data);
        log_message('info','In GenerateRoPdfService@GeneratePdf | Html => '.print_r($html,True));

        $networkRoNumber = $data['nw_ro_number'];
        $networkRoNo = str_replace('/', '_', $networkRoNumber);
        log_message('info','In GenerateRoPdfService@GeneratePdf | NetworkRoNo '.print_r($networkRoNo,True));

        $fileName = $networkRoNo . "_" . date('Y-m-d') . ".pdf";
        $fileLocation = $_SERVER['DOCUMENT_ROOT'] . "/surewaves_easy_ro/pdfReports/" . $fileName;

        $this->htmlToPdfServiceObj->convertHtmlToPdf($html, $fileLocation);

        $allFilesLocation = $fileLocation;
        $allFilesNames = $fileName;

        if ($data['cancel_invoice_details_html']['status']) {
            if ($data['cancel_invoice_details_html']['revision'] > 0) {
                $cancelInvoiceFileName = $data['cancel_invoice_details_html']['network_ro_number'] . "-R" . $data['cancel_invoice_details_html']['revision'] . '_' . date('Y-m-d') . "_cancel.pdf";
                $subjectCancelInvoice = $data['cancel_invoice_details_html']['network_ro_number'] . "-R" . $data['cancel_invoice_details_html']['revision'] . '-cancel';
            } else {
                $cancelInvoiceFileName = $data['cancel_invoice_details_html']['network_ro_number'] . "_" . date('Y-m-d') . "_cancel.pdf";
                $subjectCancelInvoice = $data['cancel_invoice_details_html']['network_ro_number'] . '-cancel';
            }

            $cancelInvoiceFileName = str_replace('/', '_', $cancelInvoiceFileName);
            $cancelInvoiceFileLocation = $_SERVER['DOCUMENT_ROOT'] . "/surewaves_easy_ro/pdfReports/" . $cancelInvoiceFileName;

            $this->htmlToPdfServiceObj->convertHtmlToPdf($data['cancel_invoice_details_html']['html'], $cancelInvoiceFileLocation);
            $allFilesLocation .= ',' . $cancelInvoiceFileLocation;
            $allFilesNames .= ',' . $cancelInvoiceFileName;

        }
        log_message('info','In GenerateRoPdfService@GeneratePdf | AllFilesLocation '.print_r($allFilesLocation,True));
        log_message('info','In GenerateRoPdfService@GeneratePdf | AllFilesName '.print_r($allFilesNames,True));

        // Check for amendment or cancellation
        $revision_no = '';
        if ($data['network_amount_details']['revision_no'] > 0) {
            $revision_no = '-R' . $data['network_amount_details']['revision_no'];
        }
        $start_date = date('d-M-Y', strtotime($data['dates']['start_date']));
        $end_date = date('d-M-Y', strtotime($data['dates']['end_date']));
        $next_date = date('d-M-Y', strtotime($end_date . "+1 days"));

        if ($data['network_amount_details']['revision_no'] == 0) {
            $text = "networkro";
            $network_ro_subject = $networkRoNumber . '' . $revision_no;
            $message = array(
                'NETWORK_NAME' => $data['ro_details'][0]['customer_name'],
                'INTERNAL_RO' => $data['internal_ro_number'],
                'CLIENT_NAME' => $data['ro_details'][0]['client_name'],
                'NETWORK_RO' => $networkRoNumber,
                'START_DATE' => date('d-M-Y', strtotime($data['dates']['start_date'])),
                'END_DATE' => date('d-M-Y', strtotime($data['dates']['end_date'])),
                'CHANNELS' => $data['channels']
            );
        } elseif ($data['network_amount_details']['revision_no'] > 0) {
            $old_revision_no = $data['network_amount_details']['revision_no'] - 1;
            if ($old_revision_no > 0) {
                $old_nw_ro_number = $networkRoNumber . '-R' . $old_revision_no;
            } else {
                $old_nw_ro_number = $networkRoNumber;
            }

            // if ro cancel
            if (count($data['cancel_ro']) > 0) {
                $text = "stop";
                $network_ro_subject = $networkRoNumber . '' . $revision_no;
            } // else in case of amendment
            else {
                $text = "amendment";
                $network_ro_subject = $networkRoNumber . '' . $revision_no;
            }

            if ($data['complete_cancellation']) {
                $next_date = date('d-M-Y', strtotime($data['cancel_start_date']));
                $text = "complete_stop";
                $network_ro_subject = $networkRoNumber . '' . $revision_no;
                $message = array(
                    'NETWORK_NAME' => $data['ro_details'][0]['customer_name'],
                    'INTERNAL_RO' => $data['internal_ro_number'],
                    'CLIENT_NAME' => $data['ro_details'][0]['client_name'],
                    'NETWORK_RO' => $network_ro_subject,
                    'NEXT_DATE' => $next_date,
                    'CHANNELS' => $data['channels'],
                    'OLD_NETWORK_RO' => $old_nw_ro_number
                );
            } else {
                $message = array(
                    'NETWORK_NAME' => $data['ro_details'][0]['customer_name'],
                    'INTERNAL_RO' => $data['internal_ro_number'],
                    'CLIENT_NAME' => $data['ro_details'][0]['client_name'],
                    'NETWORK_RO' => $network_ro_subject,
                    'START_DATE' => $start_date,
                    'END_DATE' => $end_date,
                    'NEXT_DATE' => $next_date,
                    'CHANNELS' => $data['channels'],
                    'OLD_NETWORK_RO' => $old_nw_ro_number
                );
            }
        }
        $emailList = explode(',', $data['nw_ro_to_email_list']);

        //Added sw support email in CC List
        $data['nw_ro_email'] = $data['nw_ro_email'] . ',' . $this->CI->config->item('from_email');
        log_message('info','In GenerateRoPdfService@GeneratePdf | CC Email List => '.print_r($data['nw_ro_email'],True));

        $subjectArr = array($network_ro_subject, $subjectCancelInvoice);
        log_message('info','In GenerateRoPdfService@GeneratePdf | SubjectArr => '.print_r($subjectArr,True));
        log_message('info','In GenerateRoPdfService@GeneratePdf | Text => '.print_r($text,True));
        log_message('info','In GenerateRoPdfService@GeneratePdf | Message => '.print_r($message,True));
        store_mail_data($emailList, $text, $subjectArr, $message, $allFilesLocation, $data['nw_ro_email'], $allFilesNames);
    }

    /**
     * Author: Yash
     * Date: November, 2019
     *
     * @param $data
     * @return string
     * Description: Prepare Html Code for Pdf
     */
    public function getHtml($data)
    {
        $reports = $data['reports'];
        $ro_details = $data['ro_details'];
        $channels = $data['channels'];
        $market_cluster_name = $data['market_cluster_name'];
        $dates = $data['dates'];
        $no_of_months = $data['no_of_months'];
        $network_amount_details = $data['network_amount_details'];
        $content_download_link = $data['content_download_link'];
        $show_link = $data['show_link'];
        $nw_ro_number = $data['nw_ro_number'];

        // Rate Part Commented for Future Requirement
        // Commented By:: Yash
        /*$rate = $data['Rate'];

        $channelRates = explode('?',$rate);
        $channelWiseRate = array();
        foreach ($channelRates as $channelRate){
            $chRate = explode('#',$channelRate);
            $channelWiseRate[$chRate[0]]['Spot Ad'] = $chRate[1];
            $channelWiseRate[$chRate[0]]['Banner Ad'] = $chRate[2];
        }*/

        $html =
            "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html>
    <head>

        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <title>Surewaves Easy RO</title>

        <link rel=\"stylesheet\" type=\"text/css\" href=\"http://easyro.surewaves.com/surewaves_easy_ro/bootstrap/css/bootstrap.min.css\" />";

        $html .=
            "<style type=\"text/css\">
            body {
                font-size:16px;
            }
            .h4, h4 {
                font-size: 20px;
            }
            .table-details {
                margin-left: 25px;
            }
            .table-details > tbody > tr > td {
                border-top:none;
                padding:3px;
            }
            .table-details > tbody > tr > th {
                border-top:none;
                padding:3px;
            }
        </style>

    </head>";
        $revision_txt = '';
        $term_condition_txt = '';
        $nw_ro_txt = 'Network RO Number';
        $revision_no = '';
        if ($network_amount_details['revision_no'] > 0) {
            $revision_txt = '  -  Revision';
            $nw_ro_txt = 'Revised NRO Number';
            $revision_no = '-R' . $network_amount_details['revision_no'];
            $term_condition_txt = "<tr><td>6. IGNORE THE EARLIER RO AND USE THIS RO FOR INVOICING </td></tr>";
        }
        $start_date = date('d-M-Y', strtotime($dates['start_date']));
        $end_date = date('d-M-Y', strtotime($dates['end_date']));

        $html .= "<body>
            <div class=\"container\">

                    <div class=\"col-xs-12\" style=\"margin-top:10px\">

                        <div class=\"col-xs-3\">
                            <label>
                                <h2 style=\"margin-right:10px;float:right\"><img src=\"http://easyro.surewaves.com/surewaves_easy_ro/images/logo_pdf.png\" style=\"width: 180px;\"/></h2>
                            </label>
                        </div>

                        <div class=\"col-xs-9\">
                            <div class=\"col-xs-12\" style=\"padding-top:40px;padding-left: 108px;\">
                                <label><h4>SureWaves National Spot TV Network</h4></label>
                            </div>
                        </div>


                    </div>

                    <div class=\"col-xs-12 text-center text-success\">
                        <label><h4>Network Release Order <b>" . $revision_txt . "<b> </h4></label>
                    </div>";

        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<div class=\"col-xs-12\">";
        $html .= "<div class=\"col-xs-3 \"><label>" . $nw_ro_txt . "</label></div>";
        $html .= "<div class=\"col-xs-9\">" . $nw_ro_number . '' . $revision_no . "</div>
    </div>";

        $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Network Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $ro_details[0]['customer_name'] . "</div>
             </div>";

        $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Billing Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $network_amount_details['billing_name'] . "</div>
             </div>";

        $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Market/Cluster</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $market_cluster_name . "</div>
             </div>";

        $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Channels</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $channels . "</div>
             </div>";

        $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Advertiser Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $ro_details[0]['client_name'] . "</div>
             </div>";

        $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Campaign Period</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $start_date . " to " . $end_date . "</div>
             </div>";

        $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Release Date</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . date("d-M-Y H:i:s") . "</div>
             </div>";

        $total_amount = $network_amount_details['network_amount'];
        $surewaves_share = 100 - $network_amount_details['customer_share'];
        $network_amount = $total_amount * (100 - $surewaves_share) / 100;
        if ($surewaves_share != 0) {

            $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Investment</label></div>";
            $html .= "   <div class=\"col-xs-9\">" . round($total_amount) . "(Plus GST)</div>
             </div>";

        }
        $html .= "<div class=\"col-xs-12\">
            <div class=\"col-xs-3 \"><label>Net Amount Payable</label></div>";
        $html .= "   <div class=\"col-xs-9\">INR " . round($network_amount) . "(Plus GST)</div>
         </div>";


        $html .= "<br/>";

        if ($show_link == 1) {
            $html .= "<div class=\"col-xs-12 text-center\">";

            $html .= "<div class=\"col-xs-12\"><label>Content Download Link</label>";
            $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";
            $html .= "</div>";

            $html .= "<table><tr>
                                <th colspan='2' style='padding-right: 20px;'>Channel Name</th>
                                <th style='padding-left: 20px;'>Download Link</th>
                             </tr>";

            foreach ($content_download_link as $value) {
                $html .= "<tr>";
                $html .= "<td colspan='2' align='left' style='padding-right: 20px;'>" . $value['channel_name'] . "</td>";
                $html .= "<td align='right' style='padding-left: 20px'>" . $value['file_location'] . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
            $html .= "</div>";
            $html .= "<br/>";
        }

        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<div class=\"col-xs-12\" style=\"margin-left: 25px;\">
            See detailed schedule in subsequent pages
         </div>";

        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<table class=\"table table-condensed table-details\" style=\"text-align: left \">
         <tr><th>Terms & Conditions for RO's:</th></tr>";
        $html .= "<tr><td>1. Spots are to be scheduled as mentioned and within the day-part specified in the RO</td></tr>";
        $html .= "<tr><td>2. If any spots are missed for any reason, Make Good will be done as per schedule made by SureWaves</td></tr>";
        $html .= "<tr><td>3. In case of shortfalls within the campaign schedule, customer will not be paying for such shortfall of spots, if any, and the same shall be adjusted in final payment, as applicable.</td></tr>";
        //$html .= "<tr><td>4. PAN No and Service Tax No (where applicable) must be provided to SureWaves before payments are released.</td></tr>";
	$html .= "<tr><td>4. PAN, GST certificate, Cancelled cheque copy & Filled ERF (Where applicable), must be provided to Surewaves before payments are released.</td></tr>";
        $html .= "<tr><td>5. Invoices to be raised in the name of \"SureWaves MediaTech Pvt. Ltd.\" at the address mentioned below.</td></tr>";
        $html .= "<tr><td>6. Please update our GST No. 29AABCI5171N1Z4 compulsorily against the Invoice which is raised to us.</td></tr>";
        $html .= $term_condition_txt;
        $html .= "</table>";


        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" />";

        $html .= "<table class=\"table table-condensed table-details\" style=\"text-align: left \">";
        $html .= "<tr><th>SUREWAVES MEDIATECH PRIVATE LIMITED,</th></tr>";
        #$html .= "<tr><td>3rd Floor, Ashok Chambers, 6th Cross,</td></tr>";
	$html .= "<tr><td>#HD-005, WeWork Salarpuria Magnificia, Tin Factory,</td></tr>";
        #$html .= "<tr><td>Koramangala, Srinivagulu, Near Ejipura Junction, 25 Intermediate Ring Road,</td></tr>";
	$html .= "<tr><td>78 Old Madras Road,Mahadevapura, next to KR Puram,</td></tr>";
        #$html .= "<tr><td>Bangalore - 560047,</td></tr>";
	$html .= "<tr><td>Bangalore,Karnataka - 560016,</td></tr>";
        $html .= "<tr><td>Tel:+91 80 49698922, Fax: +91 80 49698910</td></tr>";
        $html .= "</table>";

        $html .= "<h5 style=\"margin-left: 25px;\">THIS IS A COMPUTER GENERATED RELEASE ORDER AND DOES NOT REQUIRE SIGNATURE AND STAMP.</h5>";

        $camp_start_date = strtotime(date('Y-m-d', strtotime($dates['start_date'])));
        $month = date('m', $camp_start_date);
        $year = date('Y', $camp_start_date);
        $month_start_date = date('Y-m-01', $camp_start_date);
        $month_end_date = date('Y-m-t', $camp_start_date);
        $no_of_days = round(abs(strtotime(date('Y-m-d', strtotime($month_end_date))) - strtotime(date('Y-m-d', strtotime($month_start_date)))) / 86400) + 1;
        $month_start_date = strtotime($month_start_date);
        $day = "";
        $tsec = 0;
        $html .= "<p style=\"page-break-after: always;\"></p>";

        //================================================================ start container page 2 ===============================================
        $html .= "<div class=\"container\">";
        $html .= "<div class=\"col-xs-12\">";
        for ($m = 0; $m < $no_of_months; $m++) {
            if ($m == 0) {
                $html .= "<br/>";
            }
            $html .= "<div class=\"col-xs-12 text-center text-muted\">
                <h3>Detailed Schedule for " . date('F,Y', $month_start_date) . "</h3>";
            $month_end_date = date('Y-m-t', $month_start_date);
            $no_of_days = round(abs(strtotime(date('Y-m-d', strtotime($month_end_date))) - strtotime(date('Y-m-d', $month_start_date))) / 86400) + 1;
            $k = 0;
            foreach ($reports as $key => $r) {
                $sec = 0;
                if ($k < count($reports)) {
                    $html .= "<table class=\"table table-condensed \" style=\"text-align: left;font-size: 14px; \">
                    <tr>
                        <th>Channel Name</th>
                        <th>Ad Type</th>
                        <th>Language</th>
                        <th>Brand</th>
                        <th>Caption</th>
                        <th>Duration(Sec)</th>
                        <th>Month</th>
                        <!--<th>Rate/10 Sec</th>-->
                    </tr>
                    <tr>";
                    $html .= "<td>" . $reports[$k]['channel_name'] . "</td>";
                    $html .= "<td>" . $reports[$k]['ad_type'] . "</td>";
                    $html .= "<td>" . $reports[$k]['language'] . "</td>";
                    $html .= "<td>" . $reports[$k]['brand_new'] . "</td>";
                    $html .= "<td>" . $reports[$k]['caption_name'] . "</td>";
                    $html .= "<td>" . round($reports[$k]['ro_duration'], 2) . "</td>";
                    $html .= "<td>" . date('F', $month_start_date) . "</td>";
//                    $html .= "<td>" . $channelWiseRate[$reports[$k]['channel_name']][$reports[$k]['ad_type']] . "</td>";
                    $html .= "</tr>
                    </table>

                    <table  class=\"table table-condensed table-striped\" style=\"text-align: left;font-size: 14px; \" >
                        <tr>
						<th>Time Band/Date</th>";
                    for ($i = 0; $i < $no_of_days; $i++) {
                        $html .= "<th>" . date('d', strtotime("+$i day", $month_start_date)) . "</th>";
                    }
                    $html .= "<th>Total Spots</th>
                     <th>Total Seconds</th>";
                    $html .= " </tr>";

                    $sumTotal = array();
                    for ($j = 0; $j < 5; $j++) {
                        $sec = 0;
                        $tsec = 0;
                        $html .= "<tr>";
                        $html .= "<td>" . $reports[$k]['timeband'] . "</td>";
                        for ($i = 0; $i < $no_of_days; $i++) {
                            $day = date('Y-m-d', strtotime("+$i day", $month_start_date));

                            if (isset($reports[$k][$day])) {
                                $html .= "<td>" . $reports[$k][$day] . "</td>";
                                $sec = $sec + $reports[$k][$day];
                                $tsec = $tsec + $reports[$k][$day] * $reports[$k]['ro_duration'];
                                $sumTotal[$i] = $sumTotal[$i] + $reports[$k][$day];
                            } else {
                                $html .= "<td>-</td>";
                                $sumTotal[$i] = '-';
                            }
                        }
                        $html .= "<td style=\"font-weight:bold\">" . $sec . "</td>";
                        $html .= "<td style=\"font-weight:bold\">" . $tsec . "</td>";

                        $sumTotal[$no_of_days] = $sumTotal[$no_of_days] + $sec;
                        $sumTotal[$no_of_days+1] = $sumTotal[$no_of_days+1] + $tsec;

                        $html .= "</tr>";
                        $k++;
                    }

                    $html .= "<tr>";
                    $html .= "<td> Total </td>";
                    for($i=0; $i < $no_of_days; $i++)
                        $html .= "<td>" . $sumTotal[$i] . "</td>";

                    $html .= "<td>" . $sumTotal[$no_of_days] . "</td>";
                    $html .= "<td>" . $sumTotal[$no_of_days+1] . "</td>";

                    $html .= "</tr>";

                    $html .= "</table><br/>";
                }
            }
            $month_start_date = strtotime($day) + 86400;
            $month++;
            $html .= "</div><br/>";
        }

        $html .= "</div>";

        $html .= "</div></body></html>";

        return $html;
    }
}
