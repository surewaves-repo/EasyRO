<?php
	require_once("pdfcrowd/pdfcrowd.php");
	//date("d-m-Y",strtotime($invoice_date)) in Place for 15-06-2016
	$html = "<!DOCTYPE html>
			<html>
			<head>
				<meta charset=\"utf-8\" />
				<title>Tax Invoice</title>
				<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />

				<style>
					.invoice_items {
						height:180px;
					}
					.panel-heading {
					    background-color: #D5DEDC !important
					}
					.panel {
					    border: 2px solid #dddddd !important;
					}
				</style>

				<link rel=\"stylesheet\" type=\"text/css\" href=\"http://easyro.surewaves.com/surewaves_easy_ro/bootstrap/css/bootstrap.min.css\" />

			</head>";

    //$invoice_no = "INV_".str_replace("/","_",$invoice_details[0]['internal_ro'])."_".$invoice_number;
        $invoice_no = $invoiceFormat;
	$html .= "<body>

				<div class=\"container\">

				<!-- Simple Invoice - START -->
				<div class=\"container\">
					<div class=\"row\">
						<div class=\"col-xs-12\">
							<h1 style=\"margin-right:10px;float:right\"><img src=\"http://easyro.surewaves.com/surewaves_easy_ro/images/logo_pdf.png\"/></h1>
							<div class=\"\">
								<h1>Tax Invoice</h1>
							</div>

							<hr>
							<div class=\"row\">
								<div class=\"col-xs-6 pull-left\">
									<div class=\"panel panel-default height\">
										<div class=\"panel-heading\"><strong>Supplier</strong></div>
										<div class=\"panel-body\">
											SureWaves MediaTech Private Limited <br>

											3rd Floor, Ashok Chambers, 6th Cross, Koramangala, Srinivagulu, <br>

											Near Ejipura Junction, 25 Intermediate Ring Road, Bangalore – 47 <br>

											Phone:+91 80-49698900 <br>
										</div>
									</div>
								</div>
								<div class=\"col-xs-6\">
									<div class=\"panel panel-default height\">
										<div class=\"panel-heading\"><strong>Invoice Information</strong></div>
										<div class=\"panel-body\">
											
											<strong>Invoice Number:</strong>".$invoice_no."<br>
											
											<strong>Invoice Date:</strong>".date("d-m-Y",strtotime($invoice_date))."<br>
											
											<strong>Terms of Payment:</strong>Within 60 days<br>
											
											<strong>Release Order Number:</strong>".$invoice_details[0]['customer_ro']."<br>

											<strong>Release Order Date:</strong>".date("d-m-Y",strtotime($invoice_details[0]['ro_date']))."<br>
											
											<strong>Buyer's Contact:</strong>".$invoice_details[0]['agency_contact_name']."<br>
											
											<strong>Supplier's Contact:</strong>".$invoice_details[0]['supplier']."<br>
											
										</div>
									</div>
								</div>
								<div class=\"col-xs-6\">
									<div class=\"panel panel-default height\">
										<div class=\"panel-heading\"><strong>Buyer</strong></div>";

                                    if($invoice_details[0]['agency_name'] != 'Surewaves' ){
                                        $html.="<div class=\"panel-body\">
                                                    <strong>".$invoice_details[0]['agency_name']."</strong><br>
                                                    ".$invoice_details[0]['agency_address']."<br>
                                                    <strong>Phone:</strong>".$invoice_details[0]['agency_contact_no']."<br>
						    <strong>GST No:</strong>".$invoice_details[0]['agency_gst']."<br>
                                                </div>";
                                    }else{
                                        $html.="<div class=\"panel-body\">
                                                    <strong>".$invoice_details[0]['client_name']."</strong><br>
                                                    ".$invoice_details[0]['client_address']."<br>
                                                    <strong>Phone:</strong>".$invoice_details[0]['client_contact_number']."<br>
						    <strong>GST No:</strong>".$invoice_details[0]['client_gst']."<br>
                                                </div>";
                                    }

                            $html.=	"</div>
								</div>
								<div class=\"col-xs-6 pull-right\">
									<div class=\"panel panel-default height\">
										<div class=\"panel-heading\"><strong>Client</strong></div>
										<div class=\"panel-body\">
										    <strong>".$invoice_details[0]['client_name']."</strong><br>
											".$invoice_details[0]['client_address']."
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class=\"row\">
						<div class=\"col-xs-12\">
							<div class=\"panel panel-default\">
								<div class=\"panel-heading\">
									<h3 class=\"text-center\"><strong>Order summary</strong></h3>
								</div>
								<div class=\"panel-body\">
									<div class=\"col-xs-6\">
										<div class=\"table-responsive\">
											<table class=\"table table-condensed\">
												<thead>
												<tr>
													<td>
													    <strong>Description</strong><br>
													</td>
												</tr>
												</thead>
											
												<tbody>";

                            foreach($invoice_details as $invoice){

                            $html .=
                                        "<tr class=\"invoice_items\">
                                            <td>
                                                <strong>Campaign : BRAND AWARENESS</strong><br>

                                                Brand : ".$invoice['brand_name']."<br>

                                                Product : ".$invoice['product_group']."<br>

                                                Campaign : ".$invoice['content_name']."<br>

                                                Period: ".$invoice['start_date']." to ".$invoice['end_date']."<br>

                                                Market : ".$invoice['market_name']."<br>

                                                Duration Seconds: ".$invoice['duration']."<br>

                                                Total Spots: ".$invoice['no_of_impression']."<br>
                                            </td>
                                        </tr>";
                            }

                            $html .=			"</tbody>
											</table>
										</div>
									</div>
								
									<div class=\"col-xs-6\">
										<div class=\"table-responsive\">
											<table class=\"table table-condensed\">
												<thead>
													<tr>
														<td class=\"text-center col-xs-4\"><strong>Total FCT(In Secs)</strong></td>
														<td class=\"text-center col-xs-4\"><strong>Rate(Per 10 Secs)</strong></td>
														<td class=\"text-right col-xs-4\"><strong>Amount(In Rupees)</strong></td>
													</tr>
												</thead>
												<tbody>";
                                    $total_amount = 0;
                                    foreach($invoice_details as $invoice){
                                        $total_fct = $invoice['no_of_impression'] * $invoice['duration'];
                                        $amount = ($total_fct * $invoice['rate'])/10;

                                        $total_amount = $total_amount + $amount;

                                        $agency_commission = $total_amount * $invoice['agency_commission_percent'];

                                        /*if($invoice_details[0]['agency_name'] == 'Surewaves' ){
                                            $total_amount_after_commission = $total_amount;
                                        }else{
                                            $total_amount_after_commission = $total_amount - $agency_commission;
                                        }*/

                                        /*$service_tax = $total_amount_after_commission * 14.00/100;
                                        $sbc = $total_amount_after_commission * 0.50/100;
										$KKC = $total_amount_after_commission * 0.50/100;
                                        
                                        $total_payable = $total_amount_after_commission + $service_tax + $sbc + $KKC;*/
					/*$service_tax = $total_amount_after_commission * 14.00/100;
                                        $sbc = $total_amount_after_commission * 0.50/100;
                                                                                $KKC = $total_amount_after_commission * 0.50/100;

                                        $total_payable = $total_amount_after_commission + $final_gst;*/

                                        $html .=	"<tr class=\"invoice_items\">
														<td class=\"text-center\">".$total_fct."</td>
														<td class=\"text-center\">".$invoice['rate']."</td>
														<td class=\"text-right\">".round($amount,2)."</td>
													</tr>";
                                    }
					 if($invoice_details[0]['agency_name'] == 'Surewaves' ){
                                            $total_amount_after_commission = $total_amount;
					    $officialState = $invoice_details[0]['client_state'];
                                         }else{
                                            $total_amount_after_commission = $total_amount - $agency_commission;
					    $officialState = $invoice_details[0]['agency_state'];
                                         }
					   $igst = 0;
					   $cgst = 0;
					   $sgst = 0;
					  if($officialState != 'Karnataka'){
						$igst = round($total_amount_after_commission * 18.00/100);
						$finalGst = $igst;
					  }else{
					  	$cgst = round($total_amount_after_commission * 09.00/100);
						$sgst = round($total_amount_after_commission * 09.00/100);
						$finalGst = $cgst + $sgst;
					  }
					  $total_payable = $total_amount_after_commission + $finalGst; 
                                            $html .="<tr>
														<td class=\"highrow text-center\"><strong>Total Amount</strong></td>
														<td class=\"highrow\"></td>
														<td class=\"highrow text-right\">".round($total_amount,2)."</td>
													</tr>";

                                            if($invoice_details[0]['agency_name'] != 'Surewaves' ){
                                                $html .= "<tr>
                                                            <td class=\"emptyrow text-center\"><strong>Less: Agency Commission</strong></td>
                                                            <td class=\"emptyrow\"></td>
															<td class=\"emptyrow text-right\">".round($agency_commission,2)."</td>
                                                        </tr>

                                                        <tr>
                                                            <td class=\"highrow\"></td>
                                                            <td class=\"highrow\"></td>
                                                            <td class=\"highrow text-right\">".round($total_amount_after_commission,2)."</td>
                                                        </tr>";
                                            }

                                            $html .= "<tr>
														<td class=\"emptyrow text-center\">Add:<strong>IGST @ 18.00%</strong></td>
														<td class=\"emptyrow\"></td>
														<td class=\"emptyrow text-right\">".round($igst)."</td>
													</tr>
													<tr>
														<td class=\"emptyrow text-center\">Add:<strong>CGST @ 9%</strong></td>
														<td class=\"emptyrow\"></td>
														<td class=\"emptyrow text-right\">".round($cgst)."</td>
													</tr>
													
													<tr>
														<td class=\"emptyrow text-center\">Add:<strong>SGST @ 9%</strong></td>
														<td class=\"emptyrow\"></td>
														<td class=\"emptyrow text-right\">".round($sgst)."</td>
													</tr>
													<tr>
                                                                                                                <td class=\"emptyrow text-center\"><strong>Total GST </strong></td>
                                                                                                                <td class=\"emptyrow\"></td>
                                                                                                                <td class=\"emptyrow text-right\">".round($finalGst)."</td>
                                                                                                        </tr>


													<tr>
														<td class=\"emptyrow text-center\"><strong>Total Amount Payable</strong></td>
														<td class=\"emptyrow\"></td>
														<td class=\"emptyrow text-right\">".round($total_payable,2)."</td>
													</tr>
													<tr>
														<td class=\"emptyrow text-center\"><strong>Amount Payable (in words)</strong></td>
														<td class=\"emptyrow\"></td>
														<td class=\"emptyrow text-right\">".convert_number(round($total_payable,2))."</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>

							<div class=\"row\">
								<div class=\"col-xs-12\">
									<div class=\"panel panel-default height\">
										<div class=\"panel-heading\"><strong>Company Details</strong></div>
										<div class=\"panel-body\">
											
											<div class=\"col-xs-4\">
												<strong>Company's PAN No.:</strong><br>
												<strong>Company's GST No.:</strong><br>
												<strong>Wire Transfer Information </strong>(if paid thru wire transfer)<br>
												<strong>A/C Holder:</strong><br>
												<strong>Account Number </strong>(Current Account):<br>
												<strong>Bank Details:</strong><br>
												<strong>IFSC Code:</strong>

											</div>

											<div class=\"col-xs-4\">
												AABCI5171N<br>
												29AABCI5171N1Z4<br>
												<br>
												SureWaves MediaTech Private Limited,<br>
												9611400346<br>
												Kotak Mahindra Bank,  Koramangala,Bangalore<br>
												KKBK0000424
											</div>

											<div class=\"col-xs-4\">
												<strong>For</strong><br>
												SureWaves MediaTech Private Limited<br><br>

												<strong>Authorised Signatory</strong>
											</div>
											<br><br>
											<div class=\"col-xs-12\">
												<strong>Declaration:</strong>
												We declare that this invoice shows the actual price of the goods and services described above and that all particulars are true and correct.	
											</div>
											
										</div>
									</div>
								</div>
							</div>

                                                        <div class=\"col-xs-12\">
                                                                THIS IS A COMPUTER GENERATED INVOICE.HENCE SIGNATURE IS NOT REQUIRED.	
                                                        </div>        

						</div>
					</div>
				</div>";
				
	$html .= "<style>
				.height {
					min-height: 200px;
				}

				.icon {
					font-size: 47px;
					color: #5CB85C;
				}

				.iconbig {
					font-size: 77px;
					color: #5CB85C;
				}

				.table > tbody > tr > .emptyrow {
					border-top: none;
				}

				.table > thead > tr > .emptyrow {
					border-bottom: none;
				}

				.table > tbody > tr > .highrow {
					border-top: 3px solid;
				}
				</style>

		<!-- Simple Invoice - END -->

		</div>

		</body>
		</html>";

            $client = new Pdfcrowd(PDF_CROWD_USER,PDF_CROWD_PASSWORD);
            
			$token_count = $client->numTokens();

            //alert admin if the tokens are less than 100
            if($token_count <= 100 ){

                $to = 'deepak@surewaves.com' ;
                $cc = 'nitish@surewaves.com,mani@surewaves.com' ;
                $text = "pdf_token_alert";
                $subject = array();
                $message = array(
                    'TOKEN_COUNT' => $token_count
                );
                $file = '';
                $url ='';
                mail_send($to,$text,$subject,$message,$file,$cc,$url);
            }
			
			$pathName = $document_root.'/surewaves_easy_ro/invoice_pdf/'.$invoice_details[0]['ro_id'].'/'  ;
            //$file_name = "INV_".str_replace("/","_",$invoice_details[0]['internal_ro'])."_".$invoice_number.".pdf";
            $file_name = $invoiceFormat.".pdf";
            $file_location = $pathName."".$file_name ;
            $out_file = fopen($file_location, "wb");
            $client->convertHtml($html, $out_file);
            fclose($out_file);

            /*header("Content-Type: application/pdf");
            header("Cache-Control: max-age=0");
            header("Accept-Ranges: none");
            header("Content-Disposition: attachment; filename=\"INVOICE_.$invoice_details[0]['id'].pdf\"");

			echo $pdf;*/
            $invoiceAmount = round($total_payable,2);
            store_invoice_pdf($invoice_details[0]['ro_id'],$month_name,$split_criteria,$file_location,$invoice_details[0]['client_name'],$invoice_details[0]['agency_name'],$invoiceAmount,$invoice_number,$invoice_no);

	?>


