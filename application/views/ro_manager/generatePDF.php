    <?php

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
                                                        <h4>Network Release Order".$revision_txt."</h4>
                                                    </div>";

    $html .="</br><h5>See detailed schedule in subsequent pages</h5>";

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

                                               if( $intervalDate[$i] != "totalSpots"){

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
                                               }else{

                                                   $totalImpressions = $intervalDate[$i] ;
                                                   $totalSeconds = $totalImpressions * $captionValue['caption_duration'] ;

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
    echo $html ;
    ?>
