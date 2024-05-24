<?php
/**
 * Created by PhpStorm.
 * Author: Yash Bansal & Biswa Bijayee Mishra
 * Date: November, 2019 
 */

namespace application\services\feature_services;

use application\services\common_services\HtmlToPdfService;
use application\services\common_services\S3UploadService;
use application\services\common_services\EmailService;
use application\feature_dal\GenerateRoPdfFeature;

include_once APPPATH . 'services/common_services/html_to_pdf_service.php';
include_once APPPATH . 'services/common_services/s3_upload_service.php';
include_once APPPATH . 'services/common_services/email_service.php';
include_once APPPATH . 'feature_dal/generate_ro_pdf_feature.php';

class GenerateRoPdfService
{
    private $CI;
    private $htmlToPdfServiceObj;
    private $s3FileUploadObj;
    //private $emailObj;
    private $featureObj;

    /**
     * Author: Yash
     * Date: November, 2019
     *
     * GenerateRoPdfService constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->htmlToPdfServiceObj  = new HtmlToPdfService();
        $this->s3FileUploadObj      = new S3UploadService(NETWORK_RO_BUCKET);
       // $this->emailObj             = new EmailService();
        $this->featureObj           = new GenerateRoPdfFeature();
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
	}catch(\Exception $e){
		throw $e;
		
	}
    }

    /**
     * Author: Yash , Modified by Biswa Bijayee Mishra
     * Date: November, 2019
     *
     * @param $data
     * Description: Called From generate_ro_pdf Cron and Prepare pdf using HtmlToPdfService
     */
    public function GeneratePdf($data)
    {

        log_message('info','In GenerateRoPdfService@GeneratePdf | Entered');
        try{
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
                   // $subjectCancelInvoice = $data['cancel_invoice_details_html']['network_ro_number'] . "-R" . $data['cancel_invoice_details_html']['revision'] . '-cancel';
                } else {
                    $cancelInvoiceFileName = $data['cancel_invoice_details_html']['network_ro_number'] . "_" . date('Y-m-d') . "_cancel.pdf";
                    //$subjectCancelInvoice = $data['cancel_invoice_details_html']['network_ro_number'] . '-cancel';
                }

                $cancelInvoiceFileName = str_replace('/', '_', $cancelInvoiceFileName);
                $cancelInvoiceFileLocation = $_SERVER['DOCUMENT_ROOT'] . "/surewaves_easy_ro/pdfReports/" . $cancelInvoiceFileName;

                $this->htmlToPdfServiceObj->convertHtmlToPdf($data['cancel_invoice_details_html']['html'], $cancelInvoiceFileLocation);
                $allFilesLocation .= ',' . $cancelInvoiceFileLocation;
                $allFilesNames .= ',' . $cancelInvoiceFileName;

            }
            log_message('info','In GenerateRoPdfService@GeneratePdf | AllFilesLocation '.print_r($allFilesLocation,True));
            log_message('info','In GenerateRoPdfService@GeneratePdf | AllFilesName '.print_r($allFilesNames,True));

            /* Here filepaths and file names are stored as comma separated and stored separately */
            log_message('info','In GenerateRoPdfService@GeneratePdf | Exiting');
            return array('gotError'=>false,
                        'data'=>array('filepaths'=>$allFilesLocation)
                    );
        }catch(\Exception $e) {
           
            log_message('error', 'In GenerateRoPdfService@GeneratePdf | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        return array('gotError'=>true,'data'=>array('msg'=>'At line number '.__LINE__.' the exception is'.$e->getMessage()));
            //throw $e;
        }
        
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
        try{

        
        log_message('info', 'In GenerateRoPdfService@getHtml | Entered with arguments => ' . print_r(func_get_args(), True));
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
        log_message('INFO', 'In UpdateExtRoService@getHtml |  Exiting ');
        return $html;
    }catch(\Exception $e){
        log_message('error', 'In GenerateRoPdfService@getHtml | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
        throw $e;
    }
    }
    public function convertCancelDetailsIntoHtml($data)
    {
        log_message('info', 'In GenerateRoPdfService@convertCancelDetailsIntoHtml | Entered with arguments => ' . print_r(func_get_args(), True));

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
        $nw_ro_txt = 'Network RO Number';
        $revision_no = '';
        if ($data['revision_no'] > 0) {
            $revision_txt = '  -  Revision';
            $nw_ro_txt = 'Revised NRO Number';
            $revision_no = '-R' . $data['revision_no'];
        }
        $nw_ro_number = $data['network_ro_number'];
        $start_date = date('d-M-Y', strtotime($data['start_date']));
        $end_date = date('d-M-Y', strtotime($data['end_date']));


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
							<label><h4>Network Release Order - <b style=\"color:red\">Cancel</b> </h4></label>
						</div>";

        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<div class=\"col-xs-12\">";
        $html .= "<div class=\"col-xs-3 \"><label>" . $nw_ro_txt . "</label></div>";
        $html .= "<div class=\"col-xs-9\">" . $nw_ro_number . '' . $revision_no . "- <b>Cancel</b></div>
			</div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Network Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['customer_name'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Billing Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['billing_name'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Market/Cluster</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['market'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Channels</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['channel_names'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Advertiser Name</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $data['client_name'] . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Campaign Period</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . $start_date . " to " . $end_date . "</div>
					 </div>";

        $html .= "<div class=\"col-xs-12\">
						<div class=\"col-xs-3 \"><label>Release Date</label></div>";
        $html .= "   <div class=\"col-xs-9\">" . date("d-M-Y H:i:s") . "</div>
					 </div>";

        $total_amount = $data['gross_network_ro_amount'];
        $surewaves_share = 100 - $data['customer_share'];
        $network_amount = $total_amount * (100 - $surewaves_share) / 100;
        if ($surewaves_share != 0) {

            $html .= "<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Investment</label></div>";
            $html .= "   <div class=\"col-xs-9\">-" . round($total_amount) . "(Plus GST)</div>
             </div>";

        }
        $html .= "<div class=\"col-xs-12\">
            <div class=\"col-xs-3 \"><label>Net Amount Payable</label></div>";
        $html .= "   <div class=\"col-xs-9\">INR -" . round($network_amount) . "(Plus GST)</div>
         </div>";

        $html .= "<br/>";
        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

        $html .= "<table class=\"table table-condensed table-details\" style=\"text-align: left \">
				 <tr><th>Terms & Conditions for RO's:</th></tr>";
        $html .= "<tr><td>1. Spots are to be scheduled as mentioned and within the day-part specified in the RO</td></tr>";
        $html .= "<tr><td>2. If any spots are missed for any reason, Make Good will be done as per schedule made by SureWaves</td></tr>";
        $html .= "<tr><td>3. In case of shortfalls within the campaign schedule, customer will not be paying for such shortfall of spots, if any, and the same shall be adjusted in final payment, as applicable.</td></tr>";
        $html .= "<tr><td>4. PAN No and Service Tax No (where applicable) must be provided to SureWaves before payments are released.</td></tr>";
        $html .= "<tr><td>5. Invoices to be raised in the name of \"SureWaves MediaTech Pvt. Ltd.\" at the address mentioned below.</td></tr>";
        $html .= "<tr><td>6. Please update our GST No. 29AABCI5171N1Z4 compulsorily against the Invoice which is raised to us.</td></tr>";
        $html .= "<tr><td style=\"color:red\">7. If Invoice has already been raised against the release order (which contains  â€œCANCELâ€), please cancel that Invoice or Issue a Credit Note to SureWaves, in case if you canâ€™t cancel the Invoice.</td></tr>";
        $html .= "</table>";


        $html .= "<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" />";

        $html .= "<table class=\"table table-condensed table-details\" style=\"text-align: left \">";
        $html .= "<tr><th>SUREWAVES MEDIATECH PRIVATE LIMITED,</th></tr>";
        $html .= "<tr><td>3rd Floor, Ashok Chambers, 6th Cross,</td></tr>";
        $html .= "<tr><td>Koramangala, Srinivagulu, Near Ejipura Junction, 25 Intermediate Ring Road,</td></tr>";
        $html .= "<tr><td>Bangalore - 560047,</td></tr>";
        $html .= "<tr><td>Tel:+91 80 49698922, Fax: +91 80 49698910</td></tr>";
        $html .= "</table>";

        $html .= "<h5 style=\"margin-left: 25px;\">THIS IS A COMPUTER GENERATED RELEASE ORDER AND DOES NOT REQUIRE SIGNATURE AND STAMP.</h5>";
        $html .= '</div></body><html>';
        log_message('INFO', 'In UpdateExtRoService@convertCancelDetailsIntoHtml |  Exiting ');
        return array('status' => true, 'html' => $html, 'network_ro_number' => $nw_ro_number, 'revision' => $data['revision_no']);

    }
    public function uploadPdfToS3($pdfFilesDetails){
        log_message('info','In GenerateRoPdfService@uploadPdfToS3 | Entered');
        log_message('info','In GenerateRoPdfService@uploadPdfToS3 | printing the params to function' . print_r($pdfFilesDetails, True));
        $pdfS3Urls = array();
        try{
            $filePathArr = explode(",", $pdfFilesDetails['filepaths']);
            foreach($filePathArr as $eachFilePath){
                $filePathbreak = explode("/", $eachFilePath);
                $eachFileName = $filePathbreak[count($filePathbreak) - 1];

                $status = $this->s3FileUploadObj->uploadFile($eachFilePath, $eachFileName);
                log_message('info','In GenerateRoPdfService@uploadPdfToS3 | After uploading to s3 , the status is' . print_r($status, True));
                if ($status == True) {
                    log_message('INFO', 'In GenerateRoPdfService@uploadPdfToS3 | Pdf File Uploaded successfully');
                     array_push($pdfS3Urls , $this->s3FileUploadObj->generateURL($eachFileName));
                } else {
                    log_message('INFO', 'In GenerateRoPdfService@uploadPdfToS3 | File Upload failed');
                    return false;
                }
            }
            $ret = array('gotError'=>false,'data'=>array('pdfS3Urls'=> implode(",", $pdfS3Urls)));
            log_message('INFO', 'In GenerateRoPdfService@uploadPdfToS3 | Before Exiting , printing the return values'. print_r($ret, True));
            return $ret;
        }catch (\Exception $e){
            log_message('error', 'In GenerateRoPdfService@uploadPdfToS3 | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        return array('gotError'=>true,'data'=>array('msg'=>'At line number '.__LINE__.' the exception is'.$e->getMessage()));
            //throw $e;

        }
    }
    public function getMailDataForNetwork($data,$fromEmail){
        $revision_no = '';
        $subjectCancelInvoice = '';
        $networkRoNumber = $data['nw_ro_number'];
        $network_ro_subject = '';
        try{
            log_message('info', 'In GenerateRoPdfService@getMailDataForNetwork | Entered with arguments => ' . print_r(func_get_args(), True));
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
                $network_ro_subject = $networkRoNumber . '' . $revision_no;
                if (count($data['cancel_ro']) > 0) {
                    $text = "stop";
                  //  $network_ro_subject = $networkRoNumber . '' . $revision_no;
                } // else in case of amendment
                else {
                    $text = "amendment";
                    //$network_ro_subject = $networkRoNumber . '' . $revision_no;
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
            if ($data['cancel_invoice_details_html']['status']) {
                if ($data['cancel_invoice_details_html']['revision'] > 0) {
                    
                    $subjectCancelInvoice = $data['cancel_invoice_details_html']['network_ro_number'] . "-R" . $data['cancel_invoice_details_html']['revision'] . '-cancel';
                } else {
                   
                    $subjectCancelInvoice = $data['cancel_invoice_details_html']['network_ro_number'] . '-cancel';
                }
            }
            //$emailList = explode(',', $data['nw_ro_to_email_list']);
            $emailList = $data['nw_ro_to_email_list'];
    
            //Added sw support email in CC List
            $surewavesEmails = $data['nw_ro_email'] . ',' . $fromEmail;
            log_message('info','In GenerateRoPdfService@getMailDataForNetwork | CC Email List => '.print_r($data['nw_ro_email'],True));
            if($subjectCancelInvoice != ''){
                $subjectArr = array($network_ro_subject, $subjectCancelInvoice);
            }else{
                $subjectArr = array($network_ro_subject);
            }
            
            log_message('info','In GenerateRoPdfService@getMailDataForNetwork | SubjectArr => '.print_r($subjectArr,True));
            log_message('info','In GenerateRoPdfService@getMailDataForNetwork | Text => '.print_r($text,True));
            log_message('info','In GenerateRoPdfService@getMailDataForNetwork | Message => '.print_r($message,True));
            log_message('info','In GenerateRoPdfService@getMailDataForNetwork | emailList => '.print_r($emailList,True));
            log_message('info','In GenerateRoPdfService@getMailDataForNetwork | surewavesEmails => '.print_r($surewavesEmails,True));
            log_message('info','In GenerateRoPdfService@getMailDataForNetwork | Exiting');
            /* "subject" key in return array is useless , only used in db store . Nowhere it is used in the rest of the flow  */
            return array('gotError'=>false,'data'=>array('emailList' => $emailList , 'subject' => implode(",",$subjectArr) , 'text' => $text , 'mailMessage' => $message, "surewavesEmails" => $surewavesEmails));
        }catch (\Exception $e){
            log_message('error', 'In GenerateRoPdfService@getMailDataForNetwork | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        return array('gotError'=>true,'data'=>array('msg'=>'At line number '.__LINE__.' the exception is'.$e->getMessage()));
            //throw $e;

        }
    }
    /**
     * Author: Biswa
     * Date: 22 November, 2024
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
    public function generatePdfData($roDetail, $marketCampaignPeriod, $internalRoNo, $nwId, $scheduleData, $channelIds, $DayParts){
       // log_message('info', 'In GenerateRoPdfService@generatePdfData | Entered with ScheduleData=> ' . print_r($scheduleData, True));
        try{

            log_message('info', 'In GenerateRoPdfService@generatePdfData | Entered with arguments => ' . print_r(func_get_args(), True));
            //$this->featureObj = new GenerateRoPdfFeature();
            $customerName       = $roDetail['customer_name'];
            $networkRoData      = $this->get_nw_seq_no($internalRoNo, $customerName);
            log_message('info', 'In GenerateRoPdfService@generatePdfData | networkRoData => ' . print_r($networkRoData, True));

            $networkRoNo        = $networkRoData['networkRoNo'];
            $netWorkRoExistInDB = $networkRoData['enrtyInDB'];

            $marketCluster = $marketCampaignPeriod['Market_Cluster'];
            

            $AllChannelIdsInNetwork = explode(',',$roDetail['channel_id']);
            log_message('info', 'In GenerateRoPdfService@generatePdfData | AllChannelIdInNetwork => ' . print_r($AllChannelIdsInNetwork, True));
            $contentDownloadLink = $this->featureObj->getContentLinks($internalRoNo, $nwId, $AllChannelIdsInNetwork);
            $showLink = 0;
            if (count($contentDownloadLink) > 0) {
                $showLink = 1;
            }
            log_message('info', 'In GenerateRoPdfService@generatePdfData | ContentDownloadLink => ' . print_r($contentDownloadLink, True));

            $channelsDetail = $this->featureObj->getChannelNames($channelIds);
            $channelNameByID = array();
            foreach ($channelsDetail as $channelData) {
                $channelNameByID[$channelData['tv_channel_id']] = $channelData['channel_name'];
            }
            log_message('info', 'In GenerateRoPdfService@generatePdfData | ChannelNameById => ' . print_r($channelNameByID, True));

            log_message('info', 'In GenerateRoPdfService@generatePdfData | ShowLink = ' . print_r($showLink, True));
            $startDate = $marketCampaignPeriod['start_date'];
            $endDate = $marketCampaignPeriod['end_date'];

            $completeCancellation = false;
            $cancelStartDate = '';
            if (!isset($marketCampaignPeriod['start_date']) || !isset($marketCampaignPeriod['end_date'])) {
                $res = $this->featureObj->getNetworkCancelDate($internalRoNo, $nwId);
                log_message('info', 'In GenerateRoPdfService@generatePdfData | Data in complete Cancellation=> ' . print_r($res, True));
                $completeCancellation = true;
                $cancelStartDate = $res[0]['start_date'];
                $startDate = $res[0]['start_date'];
                $endDate = $res[0]['end_date'];
                $marketCluster = $res[0]['Market_Cluster'];
            }

            $nwDates = array("start_date" => $startDate, "end_date" => $endDate);
            log_message('info', 'In GenerateRoPdfService@generatePdfData | NwDates => ' . print_r($nwDates, True));
            $month1 = date('m', strtotime($startDate));
            $month2 = date('m', strtotime($endDate));
            $year_1 = date('Y', strtotime($startDate));
            $year_2 = date('Y', strtotime($endDate));

            if ($year_2 > $year_1) {
                $year_gap = $year_2 - $year_1;
                $month2 = $year_gap * 12 + $month2;
            }

            $noOfMonths = $month2 - $month1 + 1; // Send to UI
            log_message('info', 'In GenerateRoPdfService@generatePdfData NoOFMonths => | ' . print_r($noOfMonths, True));
            $start_date = date('Y-m-d', strtotime($startDate));
            $end_date = date('Y-m-d', strtotime($endDate));
            $day = 86400; // Day in seconds
            $format = 'Y-m-d'; // Output format (see PHP date function)
            $sTime = strtotime($start_date); // Start as time  // return seconds from beginning till this date
            $eTime = strtotime($end_date); // End as time
            $numDays = round(($eTime - $sTime) / $day) + 1;
            log_message('info', 'In GenerateRoPdfService@generatePdfData | NumDays => ' . print_r($numDays, True));
            log_message('info', 'num_days=> ' . print_r($numDays, True));
            $days = array();            // Send to UI
            for ($d = 0; $d < $numDays; $d++) {
                $days[] = date($format, ($sTime + ($d * $day)));
            }
            log_message('info', 'In GenerateRoPdfService@generatePdfData | Days => ' . print_r($days, True));

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
                log_message('info', 'In GenerateRoPdfService@generatePdfData | Report Array for TimeBand ' . $timeBand . ' and Cummulative TimeBands is => ' . print_r($report, True));
            }
            log_message('info', 'In GenerateRoPdfService@generatePdfData | Report Array for all time bands => ' . print_r($report, True));

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
            log_message('Info', 'In GenerateRoPdfService@generatePdfData | Report Array after fixing=>' . print_r($report, True));

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
            log_message('info', 'In GenerateRoPdfService@generatePdfData | Final Reports => ' . print_r($reports, True));

            //Integration For Br
            //$this->mg_model->insert_into_br_ro_schedule($internalRoNo); // sv_br_ro_schedule

            $networkRoReportDetails['market'] = $marketCluster;
            $networkRoReportDetails['start_date'] = $nwDates['start_date'];
            $networkRoReportDetails['end_date'] = $nwDates['end_date'];
            $networkRoReportDetails['activity_months'] = $activityMonths;
            $networkRoReportDetails['gross_network_ro_amount'] = $roDetail['Net_Amount'];
            $networkRoReportDetails['customer_share'] = $roDetail['customer_share'];
            $networkRoReportDetails['net_amount_payable'] = round(($roDetail['Net_Amount'] * ($roDetail['customer_share']) / 100));
            $networkRoReportDetails['release_date'] = date("Y-m-d H:i:s");
            //$check = $this->featureObj->checkNetworkRoExist($networkRoNo);
            if ($netWorkRoExistInDB) {
                $networkRoReportDetails['network_ro_number'] = $networkRoNo;
                $networkRoReportDetails['customer_name'] = $customerName;
                $networkRoReportDetails['customer_ro_number'] = $marketCampaignPeriod['customer_ro'];
                $networkRoReportDetails['client_name'] = $marketCampaignPeriod['client_name'];
                $networkRoReportDetails['agency_name'] = $marketCampaignPeriod['agency_name'];
                $networkRoReportDetails['billing_name'] = $roDetail['billing_name'];
                $networkRoReportDetails['internal_ro_number'] = $internalRoNo;
                $this->featureObj->insertNetworkRoDetails($networkRoReportDetails);
                log_message('info', 'In GenerateRoPdfService@generatePdfData | NetworkRoReportDetails Insert Data is => ' . print_r($networkRoReportDetails, True));
            } else {
               
                $this->featureObj->updateNetworkRoDetails($networkRoReportDetails, $networkRoNo);
                log_message('info', 'In GenerateRoPdfService@generatePdfData | NetworkRoReportDetails Update Data is => ' . print_r($networkRoReportDetails, True));
            }
            

            $networkAmountDetails = array(
                'customer_share' => $roDetail['customer_share'],
                'billing_name' => $roDetail['billing_name'],
                'network_amount' => $roDetail['Net_Amount'],
                'revision_no' => $roDetail['revision_no']
            );
            $networkDetails = $this->featureObj->getCustomerDetail($nwId);
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
            $pdfArray['cancel_ro'] = $this->featureObj->check_for_ro_cancellation($internalRoNo);
            $pdfArray['nw_ro_to_email_list'] = $networkDetails[0]['customer_email'];
            $pdfArray['nw_ro_email'] = NW_RO_EMAIL;
            $pdfArray['Rate'] = $roDetail['Rate'];

            if ($pdfArray['cancel_invoice_details_html']['status']) {
                log_message('info', 'In GenerateRoPdfService@generatePdfData | Updating pdf_processing as 1 in Ro_Cancel_Invoice');
                $this->featureObj->updateStatusForRoCancelInvoiceData($networkRoNo);
            }
            log_message('info', 'In GenerateRoPdfService@generatePdfData | Before Exiting , return values are '.print_r($pdfArray,True));
            return array('gotError'=>false,'data'=>$pdfArray);
            //return $pdfArray;
        }catch(\Exception $e){
            log_message('error', 'In GenerateRoPdfService@generatePdfData | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        return array('gotError'=>true,'data'=>array('msg'=>'At line number '.__LINE__.' the exception is'.$e->getMessage()));

        }
    }
    
    public function sendToViewWithCancelNWInvoiceDetails($nw_ro_number)
    {
        try{
            log_message('info', 'In GenerateRoPdfService@sendToViewWithCancelNWInvoiceDetails | Entered with arguments => ' . print_r(func_get_args(), True));
            //$whereCancelInvoiceArray = array(array('network_ro_number' => $nw_ro_number, 'pdf_processing' => 0));
	     $whereCancelInvoiceArray = array('network_ro_number' => $nw_ro_number, 'pdf_processing' => 0);
            $cancelInvoiceNetworkDetails = $this->featureObj->getAllRoCancelInvoiceData($whereCancelInvoiceArray);
            log_message('info', 'ro_cancel_invoice printing whereCancelInvoiceArray array - ' . print_r($whereCancelInvoiceArray, TRUE));
            log_message('info', 'ro_cancel_invoice printing cancelInvoiceNetworkDetails array - ' . print_r($cancelInvoiceNetworkDetails, TRUE));
            $htmlDataWithStatus = array('status' => false, 'html' => null, 'network_ro_number' => null, 'revision' => null);
            if (count($cancelInvoiceNetworkDetails) > 0) {
                $htmlDataWithStatus = $this->convertCancelDetailsIntoHtml($cancelInvoiceNetworkDetails[0]);
            }
            log_message('info', 'In GenerateRoPdfService@sendToViewWithCancelNWInvoiceDetails | Before exiting , return values are'. print_r($htmlDataWithStatus , TRUE));
            
            return $htmlDataWithStatus;
        }catch(\Exception $e){
            log_message('error', 'In GenerateRoPdfService@sendToViewWithCancelNWInvoiceDetails | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
       
    }
    
    public function get_nw_seq_no($internal_ro_number, $customer_name)
    {
        try{
            log_message('info', 'In GenerateRoPdfService@get_nw_seq_no | Entered with arguments => ' . print_r(func_get_args(), True));
            $networkRoNumber = '';
            $count = 1;
            $enrtyInDB = false;
            $networkRoDetails = $this->featureObj->checkNetworkRoExistAgainstInternalRo($internal_ro_number);
            
            if (count($networkRoDetails) > 0) {
                foreach($networkRoDetails as $eachNetwork){
                    $count++;
                    if(strtolower($eachNetwork['customer_name']) === strtolower($customer_name)){
                        $networkRoNumber = $eachNetwork['network_ro_number'];
                        $enrtyInDB = true;
                        break;
                    }
                }
            }
            if($networkRoNumber === ''){
                $count = sprintf('%03d', $count);
                $networkRoDetails = $internal_ro_number . "/" . $count . "/" . $customer_name;
            }
            $ret = array('networkRoNo'=>$networkRoDetails, 'enrtyInDB'=> $enrtyInDB);
            log_message('info', 'In GenerateRoPdfService@get_nw_seq_no | Before exiting , return values are'. print_r($ret , TRUE));
            return $ret;
        }catch(\Exception $e){
            log_message('error', 'In GenerateRoPdfService@get_nw_seq_no | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        throw $e;
        }
        
        
    }
    public function storeMailDataBeforeSending($mailData , $s3UrlsOfPdfFiles,$actualFilesPathSavedInDisk,$networkRo){
        log_message('info', 'In GenerateRoPdfService@storeMailDataBeforeSending | Entered with arguments => ' . print_r(func_get_args(), True));
        try{
            $actualFiles = explode(",",$actualFilesPathSavedInDisk);
            $allFileStr = '';
            foreach($actualFiles as $eachActualFilePath){
                $filePathbreak = explode("/", $eachActualFilePath);
                if($allFileStr == ''){
                    $allFileStr = $filePathbreak[count($filePathbreak) - 1];
                }else{
                    $allFileStr .= ",".$filePathbreak[count($filePathbreak) - 1];
                }
            }
            log_message('info', 'In GenerateRoPdfService@storeMailDataBeforeSending | File paths are => ' . print_r($allFileStr, True));
            $user_data = array(
                'emails_list' => serialize($mailData['emails_list']),
                'text' => $mailData['text'],
                'subject' => serialize($mailData['subject']),
                'message' => serialize($mailData['mailMessage']),
                'file_location' => serialize($actualFilesPathSavedInDisk),
                'cc' => serialize($mailData['surewavesEmails']),
                'file_name' => serialize($allFileStr),
                'actual_file_location' => serialize($s3UrlsOfPdfFiles),
                'mail_sent' => 1,
                'pdf_generation_date' => date('Y-m-d'),
                'network_ro_number' => $networkRo
            );

            $this->featureObj->insertIntoRoMailData($user_data);
            log_message('info', 'In GenerateRoPdfService@storeMailDataBeforeSending | Exiting');
            return array('gotError'=>false,'data'=>array());

        }catch(\Exception $e){
            log_message('error', 'In GenerateRoPdfService@storeMailDataBeforeSending | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        return array('gotError'=>true,'data'=>array('msg'=>'At line number '.__LINE__.' the exception is'.$e->getMessage()));
        }
    }
    public function sendPdfOverMail($mailData,$actualPdfFilePaths){
        log_message('info', 'In GenerateRoPdfService@sendPdfOverMail | Entered with arguments => ' . print_r(func_get_args(), True));
        try{
            
            $emailTextKey       = $mailData['text'];
            $messageKeyValues   = $mailData['mailMessage'];
            $toEmails           = $mailData['emailList'];
            $ccEmails           = $mailData['surewavesEmails'];
            $subjectKeyValues   = array('NETWORK_RO' => $mailData['mailMessage']['OLD_NETWORK_RO']);
            log_message('info', 'In GenerateRoPdfService@sendPdfOverMail | subjectKeyValues is => ' . print_r($subjectKeyValues, True));
	    $toEmails = 'biswabijayee@surewaves.com';
	    $ccEmails = 'nilanjan@surewaves.com';
            $emailObj           = new EmailService($toEmails,$ccEmails);
            $emailObj->sendMail($emailTextKey, $subjectKeyValues, $messageKeyValues, $actualPdfFilePaths);
            log_message('info', 'In GenerateRoPdfService@sendPdfOverMail | Exiting');
            return array('gotError'=>false,'data'=>array());
        }catch(\Exception $e){
            log_message('error', 'In GenerateRoPdfService@sendPdfOverMail | At line number '.__LINE__.' Exception error is '.print_r($e->getMessage(),True));
	        return array('gotError'=>true,'data'=>array('msg'=>'At line number '.__LINE__.' the exception is'.$e->getMessage()));
        }

    }

}
