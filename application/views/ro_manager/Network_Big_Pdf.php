<?php
	require_once("pdfcrowd/pdfcrowd.php");
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
						$html .="<div class=\"block_content1\"  style=\"background:none;margin:0px;\">";
						$html .="<table>";
						$html .="<tr>";
						$html .="<td>".$nw_ro_txt."</td>";
						$html .="<td>".$nw_ro_number.''.$revision_no."</td>";
						$html .="</tr>";
						$html .="<tr>";
                                	        $html .="<td>Network Name</td>";
                                        	$html .="<td>".$ro_details[0]['customer_name']."</td>";
	                                        $html .="</tr>";
						$html .="<tr>";
                                	        $html .="<td>Billing Name</td>";
                                        	$html .="<td>".$network_amount_details['billing_name']."</td>";
	                                        $html .="</tr>";					
						$html .="<tr>";
                	                        $html .="<td>Market/Cluster</td>";
                        	                $html .="<td>".$market_cluster_name."</td>";
                                	        $html .="</tr>";
						$html .="<tr>";
	                                        $html .="<td>Channels</td>";
        	                                $html .="<td>".implode(',',$channels)."</td>";
                	                        $html .="</tr>";
						$html .="<tr>";
                                	        $html .="<td>Advertiser Name</td>";
                                        	$html .="<td>".$ro_details[0]['client_name']."</td>";
	                                        $html .="</tr>";
						$html .="<tr>";
                	                        $html .="<td>Campaign Period</td>";
                        	                $html .="<td>".$start_date." to ".$end_date."</td>";
                                	        $html .="</tr>";
                                        	$html .="<tr>";
	                                        $html .="<td>Release Date</td>";
        	                                $html .="<td>".date("d-M-Y H:i:s")."</td>";
                	                        $html .="</tr>";                                                
						$total_amount = $network_amount_details['network_amount'];
                                                $surewaves_share = 100 - $network_amount_details['customer_share'];
                                                $network_amount = $total_amount*(100-$surewaves_share)/100;
						if($surewaves_share != 0) {
						$html .="<tr>";
                                                $html .="<td>Investment</td>";
                                                $html .="<td>".round($total_amount)."(Plus Service Tax)</td>";
                                                $html .="</tr>";
						}
                                                //$html .="<tr>";
                                                //$html .="<td>Surewaves Commission(%)</td>";
                                                //$html .="<td>".$surewaves_share."(Plus Service Tax)</td>";
                                                //$html .="</tr>";
                                                $html .="<tr>";
                                                $html .="<td>Net Amount Payable</td>";
                                                $html .="<td>INR ".round($network_amount)."(Plus Service Tax)</td>";
                                                $html .="</tr>";
						$html .="</table>";
						$html .="</div><br/>";
						
						if($show_link == 1) {
							$html .="<div class=\"block_content1\" style=\"background:none;border:0px\">";						
							$html .="<table>" ;
							$html .="<tr><th>Content Download Link</th></tr>";
							
							$html .="<tr>";
							$html .="<th>Channel Name</th>";
							$html .="<th>Download Link</th>";
							$html .="</tr>";
							
							foreach($content_download_link as $value) {
								$html .="<tr>";			
									$html .="<td>". $value['channel_name'] ."</td>";
									$html .="<td>". $value['file_location'] ."</td>";
								$html .= "</tr>" ;
							}
							$html .="</table>" ;
							$html .="</div><br/>";
						}
					/*	$spots = 0;
						$total_seconds = 0;
				                $html .="<div class=\"block_content\" style=\"background:none;border:0px\">
			  				 	<h3>Schedule Summary</h3>
								<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"  width=\"80%\">
                        			   			<tr>
										<th>Channel Name</th>
										<th>Ad Type</th>
										<th>Language</th>
                                				    		<th>Brand</th>
				                                    		<th>Caption</th>	
                                				    		<th>Duration(Sec)</th>
				                                  		<th>Total Spots</th>
                                				    		<th>Total Seconds</th>
				                                	</tr>";
						$k = 0;
						$spots = 0;
						$seconds = 0;
		                                foreach($reports as $key => $r) { 
							$total_spots = 0;
							$total_seconds = 0;
							if($k<count($reports)) { 
								$html .="<tr>";
								$html .="<td>".$reports[$k]['channel_name']."</td>";
								$html .="<td>".$reports[$k]['ad_type']."</td>";
								$html .="<td>".$reports[$k]['language']."</td>";
								$html .="<td>".$reports[$k]['brand_new']."</td>";
								$html .="<td>".$reports[$k]['caption_name']."</td>";
								$html .="<td>".round($reports[$k]['ro_duration'],2)."</td>";
								for($i = 0;$i<5;$i++) {
                                                 	               foreach($days as $d) {
                                                       		               $total_spots = $reports[$k][$d]+ $total_spots;
                                                               	               $total_seconds = $total_spots*$reports[$k]['ro_duration'];
                                                                       }
                                                                       $k++;
                                                                }
								$spots = $spots +$total_spot;									                                                                        $seconds = $seconds+$total_seconds;
								$html .="<td style=\"font-weight:bold\">".$total_spots."</td>";
								$html .="<td style=\"font-weight:bold\">".$total_seconds."</td>";
								$html .="</tr>";
				 			}
						}
			                    	$html .="</table><br/>*/
						$html .="<h5>See detailed schedule in subsequent pages</h5>";
						/*$html .="</div>";*/
						$html .="<div class=\"block_content1\" style=\"background:none;border:0px\">";/*
							 <h3>Order Summary</h3>
							 <table>";
						$html .="<tr>";
						$html .="<td>Total Seconds</td>";
						$html .="<td>".$seconds."</td>";
						$html .="</tr>";
						$html .="<tr>";
						$html .="<td>Investment</td>";
						$total_amount = $network_amount_details['network_amount'];
						$surewaves_share = 100 - $network_amount_details['customer_share'];
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
                	                        $html .="</tr></table><br/>";*/
						$html .="<h5>Terms & Conditions for RO's:</h5>";
						$html .="<h5>1. Spots are to be scheduled as mentioned and within the day-part specified in the RO <br> 2. If any spots are missed for any reason, Make Good will be done as per schedule made by SureWaves<br> 3. In case of shortfalls within the campaign schedule, customer will not be paying for such shortfall of spots, if any, and the same shall be adjusted in final payment, as applicable.<br>4. PAN No and Service Tax No (where applicable) must be provided to SureWaves before payments are released.<br>5. Invoices to be raised in the name of \"SureWaves MediaTech Pvt. Ltd.\" at the address mentioned below.<br><br><br></h5>";
						$html .="<h4>SUREWAVES MEDIATECH PRIVATE LIMITED,</h4>";
						$html .="<h5>3rd Floor, Ashok Chambers, 6th Cross,</h5>";
						$html .="<h5>Koramangala, Srinivagulu, Near Ejipura Junction, 25 Intermediate Ring Road,</h5>";
						$html .="<h5>Bangalore - 560047,</h5>";
						$html .="<h5>Tel:+91 80 49698922, Fax: +91 80 49698910<br><br></h5>";
						$html .="<h5>THIS IS A COMPUTER GENERATED RELEASE ORDER AND DOES NOT REQUIRE SIGNATURE AND STAMP.</h5>";
						$html .="</div><br/><br/>";
//						$no_of_months =  round(abs(strtotime(date('Y-m-d', strtotime($dates['end_date'])))-strtotime( 	date('Y-m-d', strtotime($dates['start_date']))))/86400/30)+1;
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
						$html .="<div class=\"block_content\" style=\"border:0px;background:none;\" >";
						for($m = 0;$m<$no_of_months;$m++) {
							if($m == 0)
							{
								 $html .="<br/>"; 
							}
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
									<table  cellpadding=\"0\" cellspacing=\"0\"  width=\"80%\">
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
						$html .="<br/>";
					}
				$html .="</div></div>";
				$html .="</div>
				</div>";
			$html .="</body></html>";                       
        $client = new Pdfcrowd("anant", "fa44ace8d8e174c203e28ba3c62dd331");
                
	$network_ro = $nw_ro_number;
	$network = str_replace('/','_',$network_ro);//echo $network;
	$network_name = $network_details['customer_name'];
	//$email  =  $network_details['customer_email'];
        $email  =  $nw_ro_to_email_list;
	$emails_list = explode(',',$email);
	$channels = implode(',',$channels);
	
	// For Development
	//$cc = 'venkat@surewaves.com';
	//for Testing
	//$cc = 'nandini@surewaves.com,mani@surewaves.com,deepak@surewaves.com,era@surewaves.com,nitish@surewaves.com';
        $cc = $nw_ro_email ;
	//for Production
	//$cc = 'kiran@surewaves.com,manajit@surewaves.com,nandini@surewaves.com,mandar@surewaves.com,anant@surewaves.com,tripur@surewaves.com,jagadish@surewaves.com,kumara@surewaves.com,arvind@surewaves.com,sony@surewaves.com';
	$user = $logged_in_user['user_name'];
	
	$file_location = $_SERVER['DOCUMENT_ROOT']."surewaves_easy_ro/pdfReports/".$network."_".date('Y-m-d').".pdf";
	
        
        $out_file = fopen($file_location, "wb");
        $client->convertHtml($html, $out_file);
        fclose($out_file);
	
	//save file here,store all data in db
	//$s3 = new S3("10W51DTTBDR9TBACB3G2", "p5wWY/7HQcEzcXK5A45PqSm0ghy0Yfpqtkk3LrMm");
	
	$file_name = $network."_".date('Y-m-d').".pdf";
	//S3::putObject(S3::inputFile("$file_location"), "sw_easy_ro", "$file_name", S3::ACL_PRIVATE);

	//$auth_url = "https://s3.amazonaws.com/sw_easy_ro/".$file_name;
	
	// check for amendment or cancellation
	if($network_amount_details['revision_no'] == 0){
		$text = "networkro";
        $subject = array('NETWORK_RO' => $network_ro.''.$revision_no);
        $network_ro_subject = $network_ro.''.$revision_no ;
		$message = array(
						'NETWORK_NAME' => $network_name,
						'INTERNAL_RO' => $internal_ro_number,
						'CLIENT_NAME' => $ro_details[0]['client_name'],
						'NETWORK_RO' => $network_ro,
						'START_DATE' => date('d-M-Y', strtotime($dates['start_date'])),
						'END_DATE' => date('d-M-Y', strtotime( $dates['end_date'])),
						'CHANNELS' => $channels,
						);
	$old_nw_ro_number = $network_ro_subject;
	}
	elseif($network_amount_details['revision_no'] > 0){
		$old_revision_no = $network_amount_details['revision_no'] - 1;
		//if($old_revision_no == 0)
		if($old_revision_no > 0) {
			$old_nw_ro_number = $network_ro.'-R'.$old_revision_no;
		}
		else{
			$old_nw_ro_number = $network_ro;
		}
		// if ro cancel
		if(count($cancel_ro) > 0){
			$text = "stop";
			$subject = array('OLD_NETWORK_RO' => $old_nw_ro_number);
			$network_ro_subject = $network_ro.''.$revision_no ;
			//$network_ro_subject = $old_nw_ro_number ;
		}
		// else in case of amendment
		else{
			$text = "amendment";
			$subject = array('OLD_NETWORK_RO' => $old_nw_ro_number);
			$network_ro_subject = $network_ro.''.$revision_no ;
			//$network_ro_subject = $old_nw_ro_number ;
		}
		if($complete_cancellation) {
			$text = "complete_stop";
			$subject = array('OLD_NETWORK_RO' => $old_nw_ro_number);
			$network_ro_subject = $network_ro.''.$revision_no ;
			$message = array(
						'NETWORK_NAME' => $network_name,
						'INTERNAL_RO' => $internal_ro_number,
						'CLIENT_NAME' => $ro_details[0]['client_name'],
						'NETWORK_RO' => $network_ro_subject,
						'NEXT_DATE' => $next_date,
						'CHANNELS' => $channels,
						'OLD_NETWORK_RO'=> $old_nw_ro_number
						); 
		}
		else{
			$message = array(
						'NETWORK_NAME' => $network_name,
						'INTERNAL_RO' => $internal_ro_number,
						'CLIENT_NAME' => $ro_details[0]['client_name'],
						'NETWORK_RO' => $network_ro_subject,
						'START_DATE' =>$start_date ,
						'END_DATE' => $end_date ,
						'NEXT_DATE' => $next_date,
						'CHANNELS' => $channels,
						'OLD_NETWORK_RO'=> $old_nw_ro_number
						); 
		}
	}
		//store mail_data into DB
		store_mail_data($emails_list,$text,$network_ro_subject,$message,$file_location,$cc,$file_name);	
	/*foreach($emails_list as $key => $to) {	
                        mail_send($to,$text,$subject,$message,$file_location,$cc,$auth_url);
	}*/
?>
