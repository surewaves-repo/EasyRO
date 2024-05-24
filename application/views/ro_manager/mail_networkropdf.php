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
	
		</head>
		<body style=\"background:none;margin:20px;padding-bottom:0px;\">
			<div id=\"hld\" style=\"background:none;margin:0px;\">	
				<div class=\"wrapper\" style=\"background:none;padding-top:0px;\">	
					<div class=\"block\" style=\"background:none;\">				
						<div class=\"block_head\" style=\"background:none;margin:0px;width:100%; height: 60px;font-size:16px;font-weight:bold;itext-align:center;\">
						<img class=\"myimage\" src=".$_SERVER['DOCUMENT_ROOT']."/surewaves_easy_ro/images/SureWaves-Logo-withWhiteBG.png  height=\"60px\" width=\"100px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SureWaves National Spot TV Network
						</div>
						 <div class=\"block_head\" style=\"background:none;width:100%; height:auto;font-size:16px;font-weight:bold;margin-left:300px;\">
                                                	<h4>Network Release Order</h4>
                                                </div>";
					$html .="<div class=\"block_content1\"  style=\"background:none;margin:0px;\">
				<table>";
					$html .="<tr>";
					$html .="<td>Network RO Number</td>";
					$html .="<td>".$internal_ro_number."/".$ro_details[0]['customer_name']."</td>";
					$html .="</tr>";
					$html .="<tr>";
                                        $html .="<td>Network Name</td>";
                                        $html .="<td>".$ro_details[0]['customer_name']."</td>";
                                        $html .="</tr>";
					$html .="<tr>";
                                        $html .="<td>Market</td>";
                                        $html .="<td>".$ro_details[0]['state']."</td>";
                                        $html .="</tr>";
					$html .="<tr>";
                                        $html .="<td>Channels</td>";
                                        $html .="<td>".$channels."</td>";
                                        $html .="</tr>";
					$html .="<tr>";
                                        $html .="<td>Advertiser Name</td>";
                                        $html .="<td>".$ro_details[0]['client_name']."</td>";
                                        $html .="</tr>";
					$html .="<tr>";
                                        $html .="<td>Campaign Period</td>";
                                        $html .="<td>".date('d-M-Y', strtotime( $dates[0]['start_date'])) ." to ". date('d-M-Y', strtotime( $dates[0]['end_date']))."</td>";
                                        $html .="</tr>";
                                        $html .="<tr>";
                                        $html .="<td>Release Date</td>";
                                        $html .="<td>".date("d-M-Y H:i:s")."</td>";
                                        $html .="</tr></table>";
					$html .="</div><br/>";
					$spots = 0;
					$total_seconds = 0;
                    $html .="<div class=\"block_content\" style=\"background:none;border:0px\">
				<h3>Schedule Summary</h3>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"  width=\"80%\">
                           		     <tr>
						<th>Channel Name</th>
						<th>Language</th>
                                    		<th>Brand</th>
                                    		<th>Caption</th>
                                    		<th>Duration(Sec)</th>
                                    		<th>Total Spots</th>
                                    		<th>Total Seconds</th>
                                	     </tr>";
				$k = 0;
				$spots = 0;$seconds = 0;
                                foreach($reports as $key=>$r) { 
				$total_spots = 0;
				$total_seconds = 0;
				if($k<count($reports)) { 
				$html .="<tr>";
					 $html .="<td>".$reports[$k]['channel_name']."</td>";
					 $html .="<td>".$reports[$k]['Language']."</td>";
					$html .="<td>".$reports[$k]['brand_new']."</td>";
					$html .="<td>".$reports[$k]['caption_name']."</td>";
					$html .="<td>".round($reports[$k]['duration_to_play'],2)."</td>";
					for($i = 0;$i<5;$i++) {
                                                                                        foreach($days as $d) {
                                                                                                $total_spots = $reports[$k][$d]+ $total_spots;
                                                                                                $total_seconds = $total_spots*$reports[$k]['duration_to_play'];
                                                                                        }
                                                                                $k++;
                                                                                }
						$spots = $spots +$total_spots;
                                                                                                $seconds = $seconds+$total_seconds;
					$html .="<td style=\"font-weight:bold\">".$total_spots."</td>";
					$html .="<td style=\"font-weight:bold\">".$total_seconds."</td>";
				$html .="</tr>";
				 }}
                    $html .="</table><br/>
				<h5>See detailed schedule in subsequent pages</h5>";
			$html .="</div>
				<div class=\"block_content1\" style=\"background:none;border:0px\">
				<h3>Order Summary</h3>
				<table>";
					$html .="<tr>";
					$html .="<td>Total Seconds</td>";
					$html .="<td>".$seconds."</td>";
					$html .="</tr>";
					$html .="<tr>";
                                        $html .="<td>Investment</td>";
				$total_amount = $network_amount_details[0]['network_amount'];
				$surewaves_share = 100 - $network_amount_details[0]['customer_share'];
				$network_amount = $total_amount*(100-$surewaves_share)/100;
                                        $html .="<td>".$total_amount."(Plus Service Tax)</td>";
                                        $html .="</tr>";		
					$html .="<tr>";
                                        $html .="<td>Surewaves Commission(%)</td>";
                                        $html .="<td>".$surewaves_share."(Plus Service Tax)</td>";
                                        $html .="</tr>";
					$html .="<tr>";
                                        $html .="<td>Net Amount Payable</td>";
                                        $html .="<td>".$network_amount."(Plus Service Tax)</td>";
                                        $html .="</tr></table><br/>";
					$html .="<h5>IF SPOTS ARE MISSED THEN A MAKEGOOD WILL BE SENT AFTER APPROVAL FROM CLIENT OR WILL RESULT IN BILLING LOSS</h5>";
					$html .="<h5>Terms & Conditions for RO's</h5>";
					$html .="<h5>1. Spots are to be scheduled within the day-part specified in the RO <br> 2. If any spots are missed for any reason, Make Good must be done as per schedule advised by SureWaves<br> 3. Clients will not be paying for shortfall of spots, if any, within the campaign period and the same shall be deducted from final payment<br>4. Invoices to be raised in the name of \"SureWaves Media Tech Pvt Ltd.\" and should sent at the following address.<br>5. Mention this release order number on your invoice and attach a copy of this release order with your invoice.<br>6. Check the rate mentioned in the release order and notify us immediately, if incorrect.<br>7. Pleae ensure that the right audion/video level is maintained.<br>8. Any deviations from the above auditions/cancellations to be pre intimated and confirmed in writing by both the parties.<br>9. Agreed rate is gross, including agency commission, and as per the aggreement entered between the parties.<br>10. Service tax to be shown separately in the invoice.<br>11. This Release Order is subjected to the removal of scrollers, tickets, towers, bugs, etc or any other messaging that diverts the attention of the viewer whilst<br> out TV commercial is being telecast.<br>12. Please ensure that our commercial is telecast on your chanel on fullscreen with no interference with the exception of channel logo. If this is not possible,<br> please do not telecast our commercial.<br>13. Bill(s) for this Release Order should be sent to us within the same month or latest by 15th of next month to the address given below else we will not accept any Release Order.<br>14. Unless otherized otherwise by us in writing, you shall not disclose to any third party any information and/or rates mentioned in this Release Order or <br>  information you received pertaining to this Campaign/activity.<br>15.In case you are unable to compyl with the above instructions or if the release can not be amde, please notify su immediately.</h5><h5>THIS IS A COMPUTER GENERATED RELEASE ORDER AND DOES NOT REQUIRE SIGNATURE AND STAMP.</h5>";
					$html .="</div><br/><br/>";
					$no_of_months =  round(abs(strtotime(date('Y-m-d', strtotime($dates[0]['end_date'])))-strtotime( 	date('Y-m-d', strtotime($dates[0]['start_date']))))/86400/30)+1;
					$camp_start_date = strtotime( date('Y-m-d', strtotime($dates[0]['start_date'])));
					$month = date('m',$camp_start_date);
					$year = date('Y',$camp_start_date);
					$month_start_date = date('Y-m-01',$camp_start_date);
					$month_end_date = date('Y-m-t',$camp_start_date);
					$no_of_days =  round(abs(strtotime(date('Y-m-d', strtotime($month_end_date)))-strtotime(date('Y-m-d', strtotime($month_start_date))))/86400)+1;
				$month_start_date = strtotime($month_start_date);
				$day = "";
				$tsec = 0;
				$html .="<p style=\"page-break-after: always;\"></p>";
				 $html .="<div class=\"block_content\" style=\"border:0px;background:none;\" >";
					 for($m = 0;$m<$no_of_months;$m++) {
						if($m == 0) { $html .="<br/>"; }
						$html .="<h3>Detailed Schedule for ".date('F,Y',$month_start_date)."</h3>";
					 $month_end_date =  date('Y-m-t',$month_start_date);
				$no_of_days =  round(abs(strtotime(date('Y-m-d', strtotime($month_end_date)))-strtotime(date('Y-m-d', $month_start_date)))/86400)+1;
					$k = 0;
		foreach($reports as $key=> $r) { 
			$sec = 0;
			if($k<count($reports)) 
			{ 
                $html .="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"80%\">
							<tr>
					<th>Channel Name</th>
                                        <th>Language</th>
                                    <th>Brand</th>
                                    <th>Caption</th>
                                    <th>Duration(Sec)</th>
					<th>Month</th>
                            </tr>
                            <tr>";
					$html .="<td>". $reports[$k]['channel_name'] ."</td>";
					 $html .="<td>". $reports[$k]['Language'] ."</td>";
								$html .="<td>".$reports[$k]['brand_new']."</td>";
								$html .="<td>". $reports[$k]['caption_name'] ."</td>";
								$html .="<td>". round($reports[$k]['duration_to_play'],2) ."</td>";
					$html .="<td>".date('F',$month_start_date)."</td>";
                    $html .="</tr>
                        </table>
			<table  cellpadding=\"0\" cellspacing=\"0\"  width=\"80%\">
                            <tr>
                                <th>Time Band</th>";
                                for($i = 0; $i<$no_of_days; $i++) { 
									$html .="<th>".date('d', strtotime("+$i day", $month_start_date))."</th>";
                                } 
                                $html .="<th>Total Spots</th>
                                <th>Total Seconds</th>
                            </tr>";
							for($j = 0;$j<5;$j++) { $sec = 0;$tsec = 0;
                        $html .="    <tr>";
								$html .="<td>". $reports[$k]['timeband'] ."</td>";
								for($i = 0; $i<$no_of_days; $i++) {
									$day = date('Y-m-d', strtotime("+$i day", $month_start_date));
									$sec = $sec + $reports[$k][$day];
									$tsec =$tsec+$reports[$k][$day]*$reports[$k]['duration_to_play'];		
									$total_sec = $total_sec +$reports[$k][$day]*$reports[$k]['duration_to_play'];
									if(isset($reports[$k][$day])) {
								$html .="<td>". $reports[$k][$day] ."</td>";
									} else {
									$html .="<td>-</td>"; 
									}	
								}
								$html .="<td style=\"font-weight:bold\">". $sec ."</td>";
								$html .="<td style=\"font-weight:bold\">". $tsec ."</td>
                            </tr>";
                            $k++; 
							}
				$html .="</table><br/>";
			} 
		}
		$month_start_date = strtotime($day)+86400;
		$month++;
			$html .="<br/>";
		}
					$html .="</div></div>";
					$html .="</div>
				</div>
		</body>
		</html>";
	$dompdf->load_html($html);    
	$dompdf->render();
//	$dompdf->stream("my_pdf.pdf", array("Attachment" => 0));  
	$network_ro = $internal_ro_number."/".$network_details[0]['customer_name'];
	$network = str_replace('/','_',$network_ro);//echo $network;
	$network_name = $network_details[0]['customer_name'];
	$user = $logged_in_user['user_name'];
	$pdf = $dompdf->output();
	$file_location = $_SERVER['DOCUMENT_ROOT']."surewaves_easy_ro/pdfReports/".$network."_".date('Y-m-d H:i:s').".pdf";
//echo $file_location;
	file_put_contents($file_location,$pdf); 
		$to ="venkat@surewaves.com";
		 $text = "networkro";
                        $subject = array('NETWORK_RO' => $network_ro);
                        $message = array(
                                                               'NETWORK_NAME' => $network_name,
                                                               'INTERNAL_RO' => $internal_ro_number,
                                                               'NETWORK_RO' => $network_ro,
                                                               'START_DATE' => date('d-M-Y', strtotime($dates[0]['start_date'])),
                                                               'END_DATE' => date('d-M-Y', strtotime( $dates[0]['end_date'])),
                                                         );
                        mail_send($to,$text,$subject,$message,$file_location,'' ,'');

?>
