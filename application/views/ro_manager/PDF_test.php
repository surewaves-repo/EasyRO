<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 10/14/15
 * Time: 12:57 PM
 */

$html =
    "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html>
    <head>

        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <title>Surewaves Easy RO</title>

        <link rel=\"stylesheet\" type=\"text/css\" href=\"http://test.tv.surewaves.com/surewaves_easy_ro/bootstrap/css/bootstrap.min.css\" />

        <style type=\"text/css\">
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

    </head>" ;
$revision_txt = '' ;
$nw_ro_txt = 'Network RO Number' ;
$revision_no = '' ;
if($network_amount_details['revision_no'] > 0) {
    $revision_txt = '  -  Revision' ;
    $nw_ro_txt = 'Revised NRO Number' ;
    $revision_no = '-R'.$network_amount_details['revision_no'] ;
}
$start_date = date('d-M-Y', strtotime($dates['start_date'])) ;
$end_date = date('d-M-Y', strtotime( $dates['end_date'])) ;
$next_date = date('d-M-Y', strtotime($end_date."+1 days"))  ;
if($complete_cancellation) {
    $next_date = date('d-M-Y', strtotime($cancel_start_date))  ;
}
$html .="<body>
            <div class=\"container\">

                    <div class=\"col-xs-12\" style=\"margin-top:10px\">

                        <div class=\"col-xs-3\">
                            <label>
                                <h2 style=\"margin-right:10px;float:right\">
                                <img src=\"http://test.tv.surewaves.com/surewaves_easy_ro/images/logo_pdf.png\" style=\"width: 180px;\"/></h2>
                            </label>
                        </div>

                        <div class=\"col-xs-9\">
                            <div class=\"col-xs-12\" style=\"padding-top:40px;padding-left: 108px;\">
                                <label><h4>SureWaves National Spot TV Network</h4></label>
                            </div>
                        </div>


                    </div>

                    <div class=\"col-xs-12 text-center text-success\">
                        <label><h4>Network Release Order".$revision_txt."</h4></label>
                    </div>";

$html .="<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

$html .="<div class=\"col-xs-12\">";
$html .="<div class=\"col-xs-3 \"><label>".$nw_ro_txt."</label></div>";
$html .="<div class=\"col-xs-9\">".$nw_ro_number.''.$revision_no."</div>
    </div>";

$html .="<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Network Name</label></div>";
$html .="   <div class=\"col-xs-9\">".$ro_details[0]['customer_name']."</div>
             </div>";

$html .="<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Billing Name</label></div>";
$html .="   <div class=\"col-xs-9\">".$network_amount_details['billing_name']."</div>
             </div>";

$html .="<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Market/Cluster</label></div>";
$html .="   <div class=\"col-xs-9\">".$market_cluster_name."</div>
             </div>";

$html .="<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Channels</label></div>";
$html .="   <div class=\"col-xs-9\">".implode(',',$channels)."</div>
             </div>";

$html .="<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Advertiser Name</label></div>";
$html .="   <div class=\"col-xs-9\">".$ro_details[0]['client_name']."</div>
             </div>";

$html .="<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Campaign Period</label></div>";
$html .="   <div class=\"col-xs-9\">".$start_date." to ".$end_date."</div>
             </div>";

$html .="<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Release Date</label></div>";
$html .="   <div class=\"col-xs-9\">".date("d-M-Y H:i:s")."</div>
             </div>";

$total_amount = $network_amount_details['network_amount'];
$surewaves_share = 100 - $network_amount_details['customer_share'];
$network_amount = $total_amount*(100-$surewaves_share)/100;
if($surewaves_share != 0) {

    $html .="<div class=\"col-xs-12\">
                <div class=\"col-xs-3 \"><label>Investment</label></div>";
    $html .="   <div class=\"col-xs-9\">".round($total_amount)."(Plus Service Tax)</div>
             </div>";

}
//$html .="<tr>";
//$html .="<div class=\"col-xs-3 \"><label>Surewaves Commission(%)</div></label>";
//$html .="<td>".$surewaves_share."(Plus Service Tax)</td>";
$html .="<div class=\"col-xs-12\">
            <div class=\"col-xs-3 \"><label>Net Amount Payable</label></div>";
$html .="   <div class=\"col-xs-9\">INR ".round($network_amount)."(Plus Service Tax)</div>
         </div>";


$html .="<br/>";

//if($show_link == 1) {
    $html .="<div class=\"col-xs-12 text-center\">";

    $html .="<div class=\"col-xs-12\"><label>Content Download Link</label>";
    $html .="<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";
    $html .="</div>" ;

    $html .="<div class=\"col-xs-3 \"><label>Channel Name</label></div>";
    $html .="<div class=\"col-xs-9\">Download Link</div>";

    foreach($content_download_link as $value) {
        $html .="<div class=\"col-xs-3 \"><label>". $value['channel_name'] ."</label></div>";
        $html .="<div class=\"col-xs-9\">". $value['file_location'] ."</div>";
    }
    $html .="</div>" ;
    $html .="<br/>";
//}

$html .="<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

$html .="<div class=\"col-xs-12\" style=\"margin-left: 25px;\">
            See detailed schedule in subsequent pages
         </div>";

$html .="<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" class=\"col-xs-12 \" />";

$html .="<table class=\"table table-condensed table-details\" style=\"text-align: left \">
         <tr><th>Terms & Conditions for RO's:</th></tr>";
$html .="<tr><td>1. Spots are to be scheduled as mentioned and within the day-part specified in the RO</td></tr>";
$html .="<tr><td>2. If any spots are missed for any reason, Make Good will be done as per schedule made by SureWaves</td></tr>";
$html .="<tr><td>3. In case of shortfalls within the campaign schedule, customer will not be paying for such shortfall of spots, if any, and the same shall be adjusted in final payment, as applicable.</td></tr>";
$html .="<tr><td>4. PAN No and Service Tax No (where applicable) must be provided to SureWaves before payments are released.</td></tr>";
$html .="<tr><td>5. Invoices to be raised in the name of \"SureWaves MediaTech Pvt. Ltd.\" at the address mentioned below.</td></tr>";
$html .="</table>";

/*$html .="<div class=\"col-xs-12\" >";
$html .="<label class=\"text-center\">Terms & Conditions for RO's:</label>";
$html .="<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" />";
$html .="<h5>1. Spots are to be scheduled as mentioned and within the day-part specified in the RO <br>
            2. If any spots are missed for any reason, Make Good will be done as per schedule made by SureWaves<br>
            3. In case of shortfalls within the campaign schedule, customer will not be paying for such shortfall of spots, if any, and the same shall be adjusted in final payment, as applicable.<br>
            4. PAN No and Service Tax No (where applicable) must be provided to SureWaves before payments are released.<br>
            5. Invoices to be raised in the name of \"SureWaves MediaTech Pvt. Ltd.\" at the address mentioned below.<br>
            <br><br>
         </h5>";
$html .= "</div>";*/

$html .="<hr style=\"width: 100%; color: black; height: 2px; background-color:#EEEEEE;\" />";

$html .="<table class=\"table table-condensed table-details\" style=\"text-align: left \">";
$html .="<tr><th>SUREWAVES MEDIATECH PRIVATE LIMITED,</th></tr>";
$html .="<tr><td>3rd Floor, Ashok Chambers, 6th Cross,</td></tr>";
$html .="<tr><td>Koramangala, Srinivagulu, Near Ejipura Junction, 25 Intermediate Ring Road,</td></tr>";
$html .="<tr><td>Bangalore - 560047,</td></tr>";
$html .="<tr><td>Tel:+91 80 49698922, Fax: +91 80 49698910</td></tr>";
$html .="</table>";

$html .="<h5 style=\"margin-left: 25px;\">THIS IS A COMPUTER GENERATED RELEASE ORDER AND DOES NOT REQUIRE SIGNATURE AND STAMP.</h5>";

/*$html .="<div class=\"col-xs-12\" >";
$html .="<h4>SUREWAVES MEDIATECH PRIVATE LIMITED,</h4>";
$html .="<h5>3rd Floor, Ashok Chambers, 6th Cross,</h5>";
$html .="<h5>Koramangala, Srinivagulu, Near Ejipura Junction, 25 Intermediate Ring Road,</h5>";
$html .="<h5>Bangalore - 560047,</h5>";
$html .="<h5>Tel:+91 80 49698922, Fax: +91 80 49698910<br><br></h5>";
$html .="<h5>THIS IS A COMPUTER GENERATED RELEASE ORDER AND DOES NOT REQUIRE SIGNATURE AND STAMP.</h5>";
$html .="</div><br/><br/>";

$html .= "</div>";*/ //ending container first page



$no_of_months = $no_of_months;
$camp_start_date = strtotime( date('Y-m-d', strtotime($dates['start_date'])));
$month = date('m',$camp_start_date);
$year = date('Y',$camp_start_date);
$month_start_date = date('Y-m-01',$camp_start_date);
$month_end_date = date('Y-m-t',$camp_start_date);
$no_of_days =  round(abs(strtotime(date('Y-m-d', strtotime($month_end_date)))-strtotime(date('Y-m-d', strtotime($month_start_date))))/86400)+1;
$month_start_date = strtotime($month_start_date);
$day = "";
$tsec = 0;
$html .="<p style=\"page-break-after: always;\"></p>";

$html .= "<div class=\"container\">";   //start container page 2
$html .="<div class=\"col-xs-12\">";
for($m = 0;$m<$no_of_months;$m++) {
    if($m == 0)
    {
        $html .="<br/>";
    }
    $html .="<div class=\"col-xs-12 text-center text-muted\">
                <h3>Detailed Schedule for ".date('F,Y',$month_start_date)."</h3>";
    $month_end_date =  date('Y-m-t',$month_start_date);
    $no_of_days =  round(abs(strtotime(date('Y-m-d', strtotime($month_end_date)))-strtotime(date('Y-m-d', $month_start_date)))/86400)+1;
    $k = 0;
    foreach($reports as $key=> $r) {
        $sec = 0;
        if($k<count($reports))
        {
            $html .="<table class=\"table table-condensed \" style=\"text-align: left \">
                    <tr>
                        <th>Channel Name</th>
                        <th>Ad Type</th>
                        <th>Language</th>
                        <th>Brand</th>
                        <th>Caption</th>
                        <th>Duration(Sec)</th>
                        <th>Month</th>
                    </tr>
                    <tr>";
                    $html .="<td>". $reports[$k]['channel_name'] ."</td>";
                    $html .="<td>". $reports[$k]['ad_type']."</td>";
                    $html .="<td>". $reports[$k]['language'] ."</td>";
                    $html .="<td>". $reports[$k]['brand_new']."</td>";
                    $html .="<td>". $reports[$k]['caption_name'] ."</td>";
                    $html .="<td>". round($reports[$k]['ro_duration'],2) ."</td>";
                    $html .="<td>".date('F',$month_start_date)."</td>";
                    $html .="</tr>
                    </table>

                    <table  class=\"table table-condensed table-striped\" style=\"text-align: left \" >
                        <tr>
						<th>Time Band/Date</th>";
            for($i = 0; $i<$no_of_days; $i++) {
                $html .="<th>".date('d', strtotime("+$i day", $month_start_date))."</th>";
            }
            $html .="<th>Total Spots</th>
                     <th>Total Seconds</th>";
            $html .=" </tr>";
            for($j = 0;$j<5;$j++) { $sec = 0;$tsec = 0;
                $html .="<tr>";
                $html .="<td>". $reports[$k]['timeband'] ."</td>";
                for($i = 0; $i<$no_of_days; $i++) {
                    $day = date('Y-m-d', strtotime("+$i day", $month_start_date));
                    $sec = $sec + $reports[$k][$day];
                    $tsec =$tsec+$reports[$k][$day]*$reports[$k]['ro_duration'];
                    $total_sec = $total_sec +$reports[$k][$day]*$reports[$k]['ro_duration'];
                    if(isset($reports[$k][$day])) {
                        $html .="<td>". $reports[$k][$day] ."</td>";
                    } else {
                        $html .="<td>-</td>";
                    }
                }
                $html .="<td style=\"font-weight:bold\">". $sec ."</td>";
                $html .="<td style=\"font-weight:bold\">". $tsec ."</td>";

                $html .="</tr>";
                $k++;
            }
            $html .="</table><br/>";
        }
    }
    $month_start_date = strtotime($day)+86400;
    $month++;
    $html .="</div><br/>";
}

$html .="</div>";

$html .= "</div></body></html>";
echo $html;



?>