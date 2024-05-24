    <?php

    require_once("dompdf/dompdf_config.inc.php");
    $dompdf = new DOMPDF();


    $html =
        "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
            <html xmlns=\"http://www.w3.org/1999/xhtml\">
            <head>

                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
                <title>Surewaves Easy RO</title>

                <style type=\"text/css\" media=\"all\">
                    @import url(\"css/style.css\");
                    @import url(\"css/jquery.wysiwyg.css\");
                    @import url(\"css/facebox.css\");
                    @import url(\"css/visualize.css\");
                    @import url(\"css/date_input.css\");
                    @import url(\"css/colorbox.css\");
                </style>

                <style>
                    #response{
                        display: none;
                        border: 1px solid #ccc;
                        background: #FFFFA0;
                        padding: 10px;
                        width: 300px;
                    }
                    #hld{
                    font-size:8px;
                    }
                    .block_content { font-size:10px;font-family:Times-Roman;
                    }
                    .block .block_content1 {
                        overflow: hidden;
                        font-size:12px;font-family:Times-Roman;font-weight:bold;
                        background: #fff;
                        padding: 10px 20px 0;
                    }
                    .block table tr td, .block table tr th { padding:3px;line-height:15px; }
                    td { height:auto; }
                    tr { height:auto; }
                    .myimage {float:left; width:15%; height:60px; margin-left:25px;margin-top:10px;margin-right:20px;border:0px;}
                </style>

                <script type=\"text/javascript\" src=\"js/jquery.js\"></script>
                <script type=\"text/javascript\" src=\"js/jquery.img.preload.js\"></script>
                <script type=\"text/javascript\" src=\"js/jquery.filestyle.mini.js\"></script>
                <script type=\"text/javascript\" src=\"js/jquery.wysiwyg.js\"></script>
                <script type=\"text/javascript\" src=\"js/jquery.date_input.pack.js\"></script>
                <script type=\"text/javascript\" src=\"js/facebox.js\"></script>
                <script type=\"text/javascript\" src=\"js/jquery.visualize.js\"></script>
                <script type=\"text/javascript\" src=\"js/jquery.select_skin.js\"></script>
                <script type=\"text/javascript\" src=\"js/ajaxupload.js\"></script>
                <script type=\"text/javascript\" src=\"js/jquery.pngfix.js\"></script>
                <script type=\"text/javascript\" src=\"js/custom.js\"></script>
                <script type=\"text/javascript\" src=\"js/jquery.colorbox-min.js\"></script>

            </head>" ;


    $html .="<body style=\"background:none;margin:20px;padding-bottom:0px;\">
                <div id=\"hld\" style=\"background:none;margin:0px;\">
                    <div class=\"wrapper\" style=\"background:none;padding-top:0px;\">
                        <div class=\"block\" style=\"background:none;\">
                            <div class=\"block_head\" style=\"background:none;margin:0px;width:100%; height: 60px;font-size:16px;font-weight:bold;itext-align:center;\">
                            <img class=\"myimage\" src=".$_SERVER['DOCUMENT_ROOT']."/surewaves_easy_ro/images/logo_pdf.png  height=\"58px\" width=\"200px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SureWaves National Spot TV Network
                            </div>
                             <div class=\"block_head\" style=\"background:none;width:100%; height:auto;font-size:16px;font-weight:bold;margin-left:300px;\">
                                                        <h4>Network Release Order".$headerArray["subject"]."</h4>
                                                    </div>";

        $html .="<div class=\"block_content1\"  style=\"background:none;margin:0px;\">";
        $html .="<table>";
        $html .="<tr>";
        $html .="<td>".$headerArray["heading"]["revisionHeading"]."</td>";
        $html .="<td>".$headerArray["heading"]["revisionValue"]."</td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td>Network Name</td>";
        $html .="<td>".$headerArray["heading"]["Network_Name"]."</td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td>Billing Name</td>";
        $html .="<td>".$headerArray["heading"]["Billing_Name"]."</td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td>Market</td>";
        $html .="<td>".$headerArray["heading"]["Market"]."</td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td>Channels</td>";
        $html .="<td>".$headerArray["heading"]["Channels"]."</td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td>Advertiser Name</td>";
        $html .="<td>".$headerArray["heading"]["Advertiser_Name"]."</td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td>Campaign Period</td>";
        $html .="<td>".date("d-M-Y", strtotime($headerArray["heading"]["Campaign_start_Period"]) )." to ".date("d-M-Y", strtotime($headerArray["heading"]["Campaign_end_Period"]) )."</td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td>Release Date</td>";
        $html .="<td>".date("d-M-Y H:i:s", strtotime($headerArray["heading"]["Release_Date"]) )."</td>";
        $html .="</tr>";


        $html .="<tr>";
        $html .="<td>Investment</td>";
        $html .="<td>".$headerArray["heading"]["Investment"]."</td>";
        $html .="</tr>";


        $html .="<tr>";
        $html .="<td>Net Amount Payable</td>";
        $html .="<td>".$headerArray["heading"]["Net_Amount_Payable"]."</td>";
        $html .="</tr>";
        $html .="</table>";
        $html .="</div><br/>";

        if($headerArray["showLink"] == 1) {

            $html .="<div class=\"block_content1\" style=\"background:none;border:0px\">";
            $html .="<table>" ;
            $html .="<tr><th>Content Download Link</th></tr>";

            $html .="<tr>";
            $html .="<th>Channel Name</th>";
            $html .="<th>Download Link</th>";
            $html .="</tr>";

            foreach( $headerArray["contentDownloadLink"] as $value ) {

                $html .="<tr>";
                $html .="<td>". $value['channel_name'] ."</td>";
                $html .="<td>". $value['file_location'] ."</td>";
                $html .= "</tr>" ;

            }
            $html .="</table>" ;
            $html .="</div><br/>";

        }


   // $html .="</br><h5>See detailed schedule in subsequent pages</h5>";

    $html .="<div class=\"block_content1\" style=\"background:none;border:0px\">";

    $html .="<h5>Terms & Conditions for RO's:</h5>";
    $html .="<h5>1. Spots are to be scheduled as mentioned and within the day-part specified in the RO <br> 2. If any spots are missed for any reason, Make Good will be done as per schedule made by SureWaves<br> 3. In case of shortfalls within the campaign schedule, customer will not be paying for such shortfall of spots, if any, and the same shall be adjusted in final payment, as applicable.<br>4. PAN No and Service Tax No (where applicable) must be provided to SureWaves before payments are released.<br>5. Invoices to be raised in the name of \"SureWaves MediaTech Pvt. Ltd.\" at the address mentioned below.<br><br><br></h5>";
    $html .="<h4>SUREWAVES MEDIATECH PRIVATE LIMITED,</h4>";
    $html .="<h5>3rd Floor, Ashok Chambers, 6th Cross,</h5>";
    $html .="<h5>Koramangala, Srinivagulu, Near Ejipura Junction, 25 Intermediate Ring Road,</h5>";
    $html .="<h5>Bangalore - 560047,</h5>";
    $html .="<h5>Tel:+91 80 49698922, Fax: +91 80 49698910<br><br></h5>";
    $html .="<h5>THIS IS A COMPUTER GENERATED RELEASE ORDER AND DOES NOT REQUIRE SIGNATURE AND STAMP.</h5>";
    $html .="</div><br/><br/>";

    $html .="<p style=\"page-break-after: always;\"></p>";
    $html .="<div class=\"block_content\" style=\"border:0px;background:none;\" >";

    foreach( $filteredArray as $monthKey => $monthValue ){

        $monthStartDate = date( 'y-m-01', strtotime($monthKey) ) ;
        $monthEndDate   = date( 'y-m-t', strtotime($monthKey) ) ;
        $currentMonth   = date( 'F', strtotime($monthKey) ) ;
        $monthTotalDays   = date( 't', strtotime($monthKey) ) ;

        $html .="<br/>";

        $html .="<h3>Detailed Schedule for ".$monthKey."</h3>";

            foreach( $monthValue as $captionKey => $captionValue ){

                    foreach( $captionValue as $channelKey => $channelValue ){

                        if( $channelKey != "caption_duration" && $channelKey != "language" && $channelKey != "brand" && $channelKey != "screen_region" ){

                            /* Table upper header */
                            $html .="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"80%\">
                                          <tr>
                                              <th>Channel Name</th>
                                              <th>Ad Type</th>
                                              <th>Language</th>
                                              <th>Brand</th>
                                              <th>Caption</th>
                                              <th>Duration(Sec)</th>
                                              <th>Month</th>
                                          </tr>" ;

                                  $html .=  "<tr><td>" .$channelKey. "</td>" ;
                                  $html .=  "<td>" .$captionValue['screen_region']. "</td>" ;
                                  $html .=  "<td>" .$captionValue['language']. "</td>" ;
                                  $html .=  "<td>" .$captionValue['brand']. "</td>" ;
                                  $html .=  "<td>" .$captionKey. "</td>" ;
                                  $html .=  "<td>" .$captionValue['caption_duration']. "</td>" ;
                                  $html .=  "<td>" .$currentMonth. "</td></tr></table>" ;

                            /* End Table upper header */

                            /* Inner Table header */

                            $html .= "<table  cellpadding=\"0\" cellspacing=\"0\"  width=\"80%\">
                                    <tr>
                                    <th>Time Band/Date</th>";

                                    $thisTime = strtotime($monthStartDate);
                                    $endTime = strtotime($monthEndDate);
                                    while($thisTime <= $endTime)
                                    {
                                        $thisDate = date('d', $thisTime);
                                        $html .="<th>". $thisDate ."</th>";
                                        $thisTime = strtotime('+1 day', $thisTime); // increment for loop
                                    }

                                    $html .="<th>Total Spots</th>
                                            <th>Total Seconds</th>";

                                   /* Creating Time intervals */

                                    foreach( $channelValue as $interval => $intervalDate ){

                                        $html .= "<tr><td>".$interval."</td>" ;

                                        if( !is_array($intervalDate) ){

                                            for( $i = $monthTotalDays +2; $i >= 1; $i-- ){

                                                if( $i == 1 || $i ==2){

                                                    $html .= "<td>0</td>" ;


                                                }else{

                                                    $html .= "<td>-</td>" ;

                                                }

                                            }

                                        }else{

                                           for( $i= 1; $i <= $monthTotalDays +2 ; $i++ ){

                                                   $totalImpressions = $intervalDate["totalSpots"]  ;
                                                   $totalSeconds = $totalImpressions * $captionValue['caption_duration'] ;
	
 						 $i = sprintf("%02d", $i) ;

                                                   if( isset($intervalDate[$i]) ){

                                                       $html .= "<td>".$intervalDate[$i]."</td>" ;

                                                   }else if( $i == $monthTotalDays + 1 ) {

                                                       $html .= "<td>".$totalImpressions."</td>" ;

                                                   }else if( $i == $monthTotalDays + 2 ){

                                                       $html .= "<td>".$totalSeconds."</td>" ;

                                                   }else
                                                   {

                                                       $html .= "<td>-</td>" ;

                                                   }

                                           }

                                        }

                                        $html .= "</tr>" ;

                                    }

                                 $html .="</table><br/>";

                                $html .="<br/>" ;

                            /* Table data Body */

                        }//End If

                }
        }

    }

    $html .="</div></div>";
    $html .="</div>
                    </div>";
    $html .="</body></html>";

    $dompdf->load_html($html);
    $dompdf->render();

    $networkRo = $headerArray["networkRoWithoutRevision"] ;
    $editedNetworkRo = str_replace('/','_',$networkRo);

    $networkName = $headerArray['heading']['Network_Name'];
    $networkEmail = $headerArray['networkDetails'][0]['customer_email'];

    $emails_list = explode(',',$networkEmail);
    $channels =  $headerArray['heading']['Channels'];

    $cc = $headerArray['ccEmailList'] ;
    $pdf = $dompdf->output();

    $file_location = $_SERVER['DOCUMENT_ROOT']."surewaves_easy_ro/pdfReports/".$networkName."_".date('Y-m-d').".pdf";
    file_put_contents($file_location,$pdf);
    $file_name = $networkName."_".date('Y-m-d').".pdf";

    $nextDate = date('d-M-Y', strtotime($endDate."+1 days")) ;
    $revisionNumber   = $headerArray["heading"]["revisionNumber"] ;
    $internalRoNumber = $headerArray["customerRoAndClient"]["internalRoNumber"] ;
    $clientName       = $headerArray["customerRoAndClient"]["clientName"];
    $startDate        = $headerArray["heading"]["Campaign_start_Period"];
    $endDate          = $headerArray["heading"]["Campaign_end_Period"];

    if( $headerArray["heading"]["revisionNumber"] == 0){

        $text = "networkro";
        $subject = array('NETWORK_RO' => $networkRo);
        $network_ro_subject = $networkRo ;

        $message = array(

            'NETWORK_NAME' => $networkName,
            'INTERNAL_RO' => $internalRoNumber,
            'CLIENT_NAME' => $clientName,
            'NETWORK_RO' => $networkRo,
            'START_DATE' => date('d-M-Y', strtotime($startDate)),
            'END_DATE' => date('d-M-Y', strtotime( $endDate)),
            'CHANNELS' => $channels,

        );

        $old_nw_ro_number = $network_ro_subject;

    }elseif($revisionNumber > 0){

        $old_revision_no = $revisionNumber - 1;

        //if($old_revision_no > 0) {
            $old_nw_ro_number = $networkRo.'-R'.$old_revision_no;
        /*}
        else{
            $old_nw_ro_number = $networkRo;
        }*/

        if(count($headerArray["cancelRo"]) > 0){
            $text = "stop";
            $subject = array('OLD_NETWORK_RO' => $old_nw_ro_number);
            $network_ro_subject = $old_nw_ro_number ;
        }
        else{
            $text = "amendment";
            $subject = array('OLD_NETWORK_RO' => $old_nw_ro_number);
            $network_ro_subject = $old_nw_ro_number ;
        }

        if( $headerArray["templateType"] == 'Cancel' ) {

            $text = "complete_stop";
            $subject = array('OLD_NETWORK_RO' => $old_nw_ro_number);
            $network_ro_subject = $old_nw_ro_number ;
            $message = array(
                'NETWORK_NAME' => $networkName,
                'INTERNAL_RO' => $internalRoNumber,
                'CLIENT_NAME' => $clientName,
                'NETWORK_RO' => $network_ro_subject,
                'NEXT_DATE' =>  $nextDate,
                'CHANNELS' => $channels,
                'OLD_NETWORK_RO'=> $old_nw_ro_number
            );
        }
        else{
            $message = array(
                'NETWORK_NAME' => $networkName,
                'INTERNAL_RO' => $internalRoNumber,
                'CLIENT_NAME' => $clientName,
                'NETWORK_RO' => $network_ro_subject,
                'START_DATE' =>$startDate ,
                'END_DATE' => $endDate ,
                'NEXT_DATE' => $nextDate,
                'CHANNELS' => $channels,
                'OLD_NETWORK_RO'=> $old_nw_ro_number
            );
        }
    }

    store_mail_data($emails_list,$text,$network_ro_subject,$message,$file_location,$cc,$file_name);

    //echo $html ;
    ?>
