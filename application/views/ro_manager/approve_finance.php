
<div >


	
		<div class="wrapper">		<!-- wrapper begins -->	
	
			
						<div class="block">
			
				
				<!-- .block_head ends -->
				
				
								
						
					
				
			<?php   if(count($scheduled_data) == 0) { ?>
            	
					<form method="post"  action="<?php echo ROOT_FOLDER ?>/account_manager/search_content">
                        <div class="block_head">
                            <div class="bheadl"></div>
                            <div class="bheadr"></div>
                            
                            <h2>Customer Release Order</h2>
                            
                            <ul style="float:right;padding-left:10px;">
                                <li ><label> </label> &nbsp; <input type="text" id="SearchTF" class="text" placeholder="Enter text to search ..." value="<?php if ( isset($search_str) && !empty($search_str) ) { echo $search_str; } ?>" name="search_str"   /></li>
                                <li ><input type="submit" class="submit" value="search"   /></li>
                                
                                
                            </ul>
                            
                        </div>	
                        </form>
					<div class="block_content">
						<table width="100%" cellspacing="0" cellpadding="0">						
							<tbody><tr>
								<th>Customer RO Number</th>
                                <th>Submitted By</th>
                                <th>Approved By</th>
								<th>Agency Name</th>
								<th>Advertiser name</th>
								<th>Brand Name</th>
								<th>RO Start Date</th>
								<th>RO End Date</th>
								<th>RO Status</th>
								<th>&nbsp;</th>					
							</tr>			
							
								<tr>
									<td>
                                        <?php if($edit != 1 && $logged_in_user['is_test_user'] != 2 && $submit_ext_ro_data[0]['user_id'] == $logged_in_user['user_id'] && ($rejection_of_submit_ro == 1)) { ?>
                                            <a href="javascript:am_edit_ext_ro('<?php echo $id ?>')">view details<br/><?php echo $submit_ext_ro_data[0]['cust_ro'] ?><br/> <?php echo '(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ; ?></a>
                                        <?php } else{ ?>
                                            <?php echo $submit_ext_ro_data[0]['cust_ro'] ?><br/> <?php echo '(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ; ?>
                                        <?php }  ?>

                                    </td>
                                    <td><?php echo $submit_ext_ro_data[0]['submitted_by'] ?> &nbsp;</td>
                                    <td><?php echo $submit_ext_ro_data[0]['approved_by'] ?> &nbsp;</td>
									<td><?php echo $submit_ext_ro_data[0]['agency'] ?> &nbsp;</td>
									<td><?php echo $submit_ext_ro_data[0]['client'] ?></td>
									<td><?php echo $submit_ext_ro_data[0]['brand_name'] ?></td>
									<td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_start_date'])); ?> </td>
									<td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_end_date'])); ?> </td>
                                    <td><?php echo $ro_status_entry ; ?> </td>
									
						</tr>
                               <tr>
                                    <td>
                                    <a href="<?php echo ROOT_FOLDER ?>/account_manager/invoice_collection/<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') ?>">Invoice</a>
                                    </td>
									<td><span  onclick="nw_ro_payment('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Network Ro Payment</span></td>
								</tr>                     
													 
						</tbody></table><br>
						
					<div class="paggination right">
												</div>		<!-- .paggination ends -->
					</div>
                    			
						<div class="block_content">							
							<h2 style="text-align:center;">No Active Campaign !</h2>
							<div class="bheadl"></div>
							<div class="bheadr"></div>	
						</div>
				
			<?php }else{  ?>
					<form method="post"  action="<?php echo ROOT_FOLDER ?>/ro_manager/search_content">
                        <div class="block_head">
                            <div class="bheadl"></div>
                            <div class="bheadr"></div>
                            <h2>Customer Release Order</h2>
                        </div>	
                        </form>
					<div class="block_content">
						<table width="100%" cellspacing="0" cellpadding="0">						
							<tbody>
							<tr>
								<th>Customer RO Number</th>
                                <th>Submitted By</th>
                                <th>Approved By</th>
								<th>Agency Name</th>
								<th>Advertiser name</th>
								<th>Brand Name</th>
								<th>RO Start Date</th>
								<th>RO End Date</th>
								<th>RO Status</th>
								<th> Gross CRO Amount</th>
							</tr>	
							<tr>
								<td>
                                    <?php if($edit != 1 && $logged_in_user['is_test_user'] != 2 && $submit_ext_ro_data[0]['user_id'] == $logged_in_user['user_id'] && ($rejection_of_submit_ro == 1)) { ?>
                                        <a href="javascript:am_edit_ext_ro('<?php echo $id ?>')">view details<br/><?php echo $submit_ext_ro_data[0]['cust_ro'] ?><br/> <?php echo '(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ; ?></a>
                                    <?php } else{ ?>
                                        <?php echo $submit_ext_ro_data[0]['cust_ro'] ?><br/> <?php echo '(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ; ?>
                                    <?php }  ?>
								 </td>
                                <td><?php echo $submit_ext_ro_data[0]['submitted_by'] ?> &nbsp;</td>
                                <td><?php echo $submit_ext_ro_data[0]['approved_by'] ?> &nbsp;</td>
								<td><?php echo $submit_ext_ro_data[0]['agency'] ?> &nbsp;</td>
									<td><?php echo $submit_ext_ro_data[0]['client'] ?></td>
									<td><?php echo $submit_ext_ro_data[0]['brand_name'] ?></td>
									<td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_start_date'])); ?> </td>
									<td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_end_date'])); ?> </td>
                                    <td><?php echo $ro_status_entry ; ?> </td>
								
								
								<td id="ro_amount"><?php echo round($ro_amount_detail[0]['ro_amount'],2) ?> </td>
							</tr>
							
							<!-- <tr>	
								<td>
								<a href="<?php /*echo ROOT_FOLDER */?>/account_manager/invoice_collection/<?php /*echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') */?>">Invoice</a>
								</td>-->
								
								<td style="border-bottom:none;"><span style="color:blue;cursor:pointer;" onclick="nw_ro_payment('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Network Ro Payment</span></td>
							</tr>                   
													 
						</tbody></table><br>
						
					</div>
                
                <div class="block" >
			
				<div class="block_head" style="margin-top:10px">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Network RO Summary</h2>
				</div>		<!-- .block_head ends -->
				 <?php 
					$final_amount = 0 ;
                                	foreach($scheduled_data as $key => $dat) {
                                        	$total_amount = 0;
											$total_rows=0;
	                                        foreach($dat['channels_data_array'] as $chnl => $ch) {
        	                                        $channel_id = $ch['channel_id'];
                	                                $customer_id = $dat['network_id'];
													$customer_share = $ch['customer_share'];
                        	                        //foreach($ch['ad_types'] as $types => $ad) {
													if($ch['total_spot_ad_seconds'] != 0){
														$total_rows++;
														$total_amount += $ch['channel_spot_amount'] ;
													}
													if($ch['total_banner_ad_seconds'] != 0){
														$total_rows++;
														$total_amount += $ch['channel_banner_amount'] ;
													}
													
        	                                        //}
                	                        }
											$rows[$customer_id] = $total_rows;
                        	       			$final_amount += $total_amount - $total_amount * (100-$customer_share)/100;
                                	}
					if($ro_amount_detail[0]['ro_approval_status']==1) {
                                                $final_amount = $total_network_payout['network_payout'];
                                        }
				?>
				
				<div class="block_content">
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/saving_data_for_confirmation" method="post" id="myForm">
                    <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
                    <input type="hidden" name="hid_id" value="<?php echo $id ?>">
                    <input type="hidden" name="hid_internal_ro" value="<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>">
                    <input type="hidden" name="nw_load_status" id="nw_load_status" value="0">
                    <input type="hidden" name="load_all_nw" id="load_all_nw" value="0">
					
					<table cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold;">
						<tr cellpadding="0" cellspacing="0" width="100%">
							<th>Gross CRO  Amount</th>
							<th>Media Agency Commission</th>
							<th>Net Amount</th>
							<th>Agency Rebate</th>
							<th>Other Expenses</th>
							<th>Actual Net Amount</th>
							<th>Net Revenue</th>
							<th>Total Network's Payout</th>
							<th>Net Contribution Amount</th>
							<th>Net Contribution Amount(%)</th>
						</tr>
						<?php if(isset($ro_amount_detail[0]['ro_amount'])) { ?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td id="ro_amount"><?php echo round($ro_amount_detail[0]['ro_amount'],2) ?> </td>
							<td id="agency_amount"><?php echo round($ro_amount_detail[0]['agency_commission_amount'],2) ?> </td>
							<td id="amount"><?php echo round($ro_amount_detail[0]['ro_amount'],2)- round($ro_amount_detail[0]['agency_commission_amount'],2) ?></td>
							<?php if($ro_amount_detail[0]['ro_valid_field'] == 1) { ?>
							<td id="agency_rebate" style="color:red;"><?php echo "Missing" ?></td>
							<td id="other_expenses" style="color:red;"><?php echo "Missing" ?></td>
							<?php } else { ?>
							<td id="agency_rebate" style="color:green;"><?php 
							if($ro_amount_detail[0]['agency_rebate_on'] != 'net_amount'){
								$agency_rebate = round($ro_amount_detail[0]['ro_amount'],2)*round($ro_amount_detail[0]['agency_rebate'],2)/100;
							}
							else{
								$agency_rebate = round(($ro_amount_detail[0]['ro_amount'] - $ro_amount_detail[0]['agency_commission_amount']),2)*round($ro_amount_detail[0]['agency_rebate'],2)/100;
							}
							echo $agency_rebate; ?></td>
							<td id="other_expenses" style="color:green;"><?php $other_expenses = $ro_amount_detail[0]['marketing_promotion_amount']+$ro_amount_detail[0]['field_activation_amount']+$ro_amount_detail[0]['sales_commissions_amount']+$ro_amount_detail[0]['creative_services_amount']+$ro_amount_detail[0]['other_expenses_amount']; echo $other_expenses; ?></td>
							<?php } ?>
							<!--@mani:added on 8.7.2013: $net_revenue -->
							<td id="net_amount"><?php $actual_net_amount =  round($ro_amount_detail[0]['ro_amount'],2)- round($ro_amount_detail[0]['agency_commission_amount'],2)-$agency_rebate-$other_expenses;echo $actual_net_amount; ?></td>
							<td id="net_revenue"><?php $net_revenue = round($ro_amount_detail[0]['ro_amount'],2)- round($ro_amount_detail[0]['agency_commission_amount'],2) ; $net_revenue = round(($net_revenue*SERVICE_TAX ),2) ; echo $net_revenue; ?></td>
							<td id="total_network_payout"><?php /*$net_payout = round($final_amount);*/echo $total_nw_payout; ?></td>
							<td id="surewaves_share"><?php $surewaves_share = $actual_net_amount - $total_nw_payout; echo $surewaves_share; ?></td>
							<td id="surewaves_share_per"><?php echo round(($surewaves_share/$net_revenue*100),2) ?></td>
						</tr>
						<?php } else { ?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td id="ro_amount">In Process</td>
							<td id="agency_amount">In Process</td>
							<td id="amount">In Process</td>
							<td id="agency_rebate">In Process</td>
							<td id"other_expenses"><?php $other_expenses = $ro_amount_detail[0]['marketing_promotion_amount']+$ro_amount_detail[0]['field_activation_amount']+$ro_amount_detail[0]['sales_commissions_amount']+$ro_amount_detail[0]['creative_services_amount']+$ro_amount_detail[0]['other_expenses_amount']; echo $other_expenses; ?></td>
							<td id="net_amount">In Process</td>
							<td id="net_revenue">In Process</td>
							<td id="total_network_payout"><?php /*$net_payout = round($final_amount);echo $net_payout;*/echo $total_nw_payout; ?></td>
							<td>In Process</td>
                                                        <td>In Process</td>
						</tr>
						<?php } ?>
						
                                                <tr cellpadding="0" cellspacing="0" width="100%">
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
														<td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                </tr>
                                                
					</table>
                                        

                                        <!--<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_add_price_approve" method="post">-->
					<div id="recs">
					<?php 
						$count = 0 ;
						if(count($scheduled_data) > 0) {
						//foreach($scheduled_data as $scheduled_key => $data) {
							foreach($scheduled_data as $key => $dat) { 
							$total_networks = count($scheduled_data);
                                                        $row_id = 0;
					?>
					<table id ="<?php echo 'network_'.$key ?>" cellpadding="0" cellspacing="0" width="100%">
						<tr cellpadding="0" class="network_name_style" cellspacing="0" width="100%" >
							<td colspan="16"  style="font-weight:bold;">Network Name: <?php echo $dat['network_name'] ?> &nbsp; (<?php echo $dat['market_name'] ?>)</td>
                            <td>&nbsp;</td>
						</tr>
						<?php 	$total_amount = 0;
							$approved_total_amount = 0;
							foreach($dat['channels_data_array'] as $chnl => $ch) { 
							$total_channel =  count($dat['channels_data_array']);
							$channel_id = $ch['channel_id'];
							$customer_id = $dat['network_id'];
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
                                                        <td colspan="16" style="font-weight:bold">Channel Name: <?php echo $ch['channel_name'] ?> 									
                                                        </td>
                                                </tr>
						 <tr cellpadding="0" cellspacing="0" width="100%">
                                                        <th colspan="3">Ad Type</th>
                                                        <th colspan="3">Total Ad Seconds</th>
                                                        <th colspan="3">Last Rate</th>
                                                        <th colspan="3">Reference Rate</th>
                                                        <th colspan="3">Amount</th>
                                                </tr>
						<?php if($ch['total_spot_ad_seconds'] != 0) {
								$amount = 0;
								$rate = $ch['channel_spot_avg_rate'];
                                $amount += $ch['total_spot_ad_seconds'] * $rate/10;
								$total_amount += $amount;
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Spot Ad</td>
							<td colspan="3" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0'; ?>" ><?php echo $ch['total_spot_ad_seconds'] ?></td>
							<?php   
								if($ch['approved'] == 1) { ?>
							<td colspan="3"><?php echo $ch['channel_spot_avg_rate'] ?></td>
							<?php } else { ?>
							<td colspan="3"><label><?php echo form_error('channel_avg_rate'); ?></label><input type="text" class="rate"  name="post_channel_avg_rate[<?php echo $channel_id; ?>][0]" id="<?php echo 'network_'.$key.'_channel_'.$row_id.'_1'; ?>" value="<?php echo set_value('post_channel_avg_rate[<?php echo $channel_id; ?>][0]',$rate); ?>" onchange = "find_price(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" /> </td>									
							<?php }?>

                            <td colspan="3"><?php echo $ch['channel_spot_reference_rate'] ?></td>

                            <?php if($ch['approved'] == 1) {
								$approved_total_amount += $ch['channel_spot_amount'] ?>
							<td colspan="3"><?php echo round($ch['channel_spot_amount'],2) ?></td>
                                                        <?php } else { ?>
							<td colspan="3"><label><?php echo form_error('channel_amount'); ?></label><input type="text" class="amount"  name="post_channel_amount[<?php echo $channel_id; ?>][0]" id="<?php echo 'network_'.$key.'_amount_'.$row_id.'_2'; ?>" value="<?php echo set_value('post_channel_amount[<?php echo $channel_id; ?>][0]',round($amount,2)); ?>" onchange = "find_rate(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" />
							<input type="hidden" name="hid_total_seconds[<?php echo $channel_id; ?>][0]" value="<?php echo $ch['total_spot_ad_seconds'] ?>" /> 
							<input type="hidden" name="hid_client_name" value="<?php echo $submit_ext_ro_data[0]['client'] ?>" />
							<input type="hidden" name="hid_internal_ro" value="<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>" />
							<input type="hidden" name="hid_cust_id[<?php echo $customer_id; ?>]" value="<?php echo $customer_id ?>" />
							<?php $row_id++; ?>
                                                        <input type="hidden" name="total_rows" value="<?php echo $rows[$customer_id]; ?>" /></td>								<?php } ?>
						</tr>
						<?php  }
						if($ch['total_banner_ad_seconds'] != 0) {
								$amount = 0;
								$rate = $ch['channel_banner_avg_rate'];
                                $amount += $ch['total_banner_ad_seconds'] * $rate/10;
								$total_amount += $amount;
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Banner Ad</td>
							<td colspan="3" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0'; ?>" ><?php echo $ch['total_banner_ad_seconds'] ?></td>
							<?php   
								if($ch['approved'] == 1) { ?>
							<td colspan="3"><?php echo $ch['channel_banner_avg_rate'] ?></td>
							<?php } else { ?>
							<td colspan="3"><label><?php echo form_error('channel_avg_rate'); ?></label><input type="text" class="rate"  name="post_channel_avg_rate[<?php echo $channel_id; ?>][1]" id="<?php echo 'network_'.$key.'_channel_'.$row_id.'_1'; ?>" value="<?php echo set_value('post_channel_avg_rate[<?php echo $channel_id; ?>][1]',$rate); ?>" onchange = "find_price(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" /> </td>									
							<?php }?>

                            <td colspan="3"><?php echo $ch['channel_banner_reference_rate'] ?></td>

                            <?php if($ch['approved'] == 1) {
								$approved_total_amount += $ch['channel_banner_amount'] ?>
							<td colspan="3"><?php echo round($ch['channel_banner_amount'],2) ?></td>
                                                        <?php } else { ?>
							<td colspan="3"><label><?php echo form_error('channel_amount'); ?></label><input type="text" class="amount"  name="post_channel_amount[<?php echo $channel_id; ?>][1]" id="<?php echo 'network_'.$key.'_amount_'.$row_id.'_2'; ?>" value="<?php echo set_value('post_channel_amount[<?php echo $channel_id; ?>][1]',round($amount,2)); ?>" onchange = "find_rate(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" />
							<input type="hidden" name="hid_total_seconds[<?php echo $channel_id; ?>][1]" value="<?php echo $ch['total_banner_ad_seconds'] ?>" /> 
							<input type="hidden" name="hid_client_name" value="<?php echo $submit_ext_ro_data[0]['client'] ?>" />
							<input type="hidden" name="hid_internal_ro" value="<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>" />
							<input type="hidden" name="hid_cust_id[<?php echo $customer_id; ?>]" value="<?php echo $customer_id ?>" />
							<?php $row_id++; ?>
                                                        <input type="hidden" name="total_rows" value="<?php echo $rows[$customer_id]; ?>" /></td>								<?php } ?>
						</tr>
						<?php  } ?>
						<?php } ?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Network RO Amount</td>
							<td colspan="3">&nbsp;</td>
                            <td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3" id="<?php echo 'network_'.$key.'_total_amount'; ?>" ><?php echo round($total_amount,2) ?></td>					
						</tr>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Network Share(%)</td>
							<td colspan="3">&nbsp;</td>
                            <td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<?php  if($dat['approved'] == 1) { ?>
							<td colspan="3"><?php echo  $dat['revenue_sharing'] ?></td>
                                                        <?php } else { ?>
							<td colspan="3"><label><?php echo form_error('network_share'); ?></label><input type="text" class="network_share"  name="post_network_share[<?php echo $customer_id; ?>]" id="<?php echo 'network_'.$key.'_network_share'; ?>" value="<?php echo set_value('post_network_share[<?php echo $customer_id; ?>]', $network_share_details[$customer_id]);?>" onchange ="find_final_amount(<?php echo $key ?>,<?php echo $total_networks ?>);find_total_payout(<?php echo $total_networks ?>);" /> </td>
							<?php } ?>
						</tr>
						<tr cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold">
							<td colspan="3">Network Payout(Net)</td>
							<td colspan="3">&nbsp;</td>
                            <td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<?php  if($dat['approved'] == 1) { 
							$net_payout_loaded +=  round($total_amount * $dat['revenue_sharing']/100,2);
							?>
							<td colspan="3"><?php echo  round($total_amount * $dat['revenue_sharing']/100,2) ?></td>
                                                        <?php } else { 
														$net_payout_loaded +=  round($total_amount * $dat['revenue_sharing']/100,2);
														?>
							<td colspan="3" style="font-weight:bold"><label><?php echo form_error('final_amount'); ?></label><input type="text" class="final_amount"  name="post_final_amount[<?php echo $customer_id; ?>]" id="<?php echo 'network_'.$key.'_final_amount'; ?>" value="<?php echo set_value('post_final_amount[<?php echo $customer_id; ?>]',round($total_amount * $dat['revenue_sharing']/100)); ?>" onchange ="find_channel_amounts(<?php echo $key ?>,<?php echo $rows[$customer_id] ?>);find_total_payout(<?php echo $total_networks ?>);" /> </td>
							<?php } ?>
						</tr>
						<?php  } ?>
					</table>
					</div>

                        
                    <span id="net_payout_loaded" style="display:none;"><?php echo $net_payout_loaded; ?></span>
					
					
				<div class="paggination right">
					<?php echo $page_links ?>
				</div>		<!-- .paggination ends -->		
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->
                
				
				
			<?php  }} ?>
<input type="hidden" id="service_tax_value" value="<?php echo SERVICE_TAX ?>"/>
<script language="javascript">

function addPrice(order_id,network_str) {
					
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_price/'+order_id+'/'+network_str,iframe:true, width: '530px', height:'820px'}); 
}
function editPrice(order_id,network_str) {
					
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_price/'+order_id+'/'+network_str,iframe:true, width: '530px', height:'820px'}); 
}
function approve_network(order_id,network_id) {
					
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/approve_network/'+order_id+'/'+network_id,iframe:true, width: '930px', height:'820px'}); 
}
function NetworkRO(order_id,network_id){
	window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/NetworkRO/" +order_id +'/'+network_id ;
}

function mail_networkropdf(order_id,network_id){
	window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/mail_networkropdf/" +order_id +'/'+network_id ;
}
function campaigns_schedule(order_id,edit,id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/campaigns_schedule/" + order_id + "/" + edit + "/" + id;
}
function channels_schedule(order_id,edit,id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/channels_schedule/" + order_id+ "/" + edit + "/" + id;
}
function nw_ro_payment(order_id,edit,id) {
	$.ajax(BASE_URL + '/ro_manager/nw_ro_payment', {
            type: 'POST',
            data: {
                "order_id": order_id,
                "edit": edit,
                "id": id

            },
            dataType:'json',
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            success: function (responsedata) {
                var data = responsedata.Data.html;
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                $('#option_modal_title').text('Channels Schedule');
                $('#optionModal').modal('show');
                $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                $('#Option_Modal_body').html(data);
            },
            error: function (data) {
                alert("error in loading");
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
            }

        })
	//$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/nw_ro_payment/'+order_id+ "/" + edit + "/" + id,iframe:true, width: '900px', height:'900px'});
	//window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/nw_ro_payment/" + order_id;
}
function ro_schedule(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/ro_schedule/" + order_id;
}
function add_price_approve(order_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/add_price_approve/" + order_id ;
}
function add_ro_amount(external_ro) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_ro_amount/'+external_ro,iframe:true, width: '540px', height:'250px'}); 
}
function find_price(row_id,total_rows,network_id) {

//	alert(total_rows);
	var total_amount = 0;
	for(var i = 0;i<total_rows;i++)
	{
//		alert(i);
	//	if(i == row_id) {
		var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
		var total_ad_seconds = document.getElementById(total_ad_sec_id).innerHTML;
		var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
		var temp_channel_avg_rate = document.getElementById(channel_avg_rate_id).value;
		/* for bug: 426 code change starts here */
		if(temp_channel_avg_rate === "" || isNaN(temp_channel_avg_rate) || temp_channel_avg_rate < 0)
		{
			alert(" Please enter a positive numerical value for channel Last Rate");
		/* for bug: 426 code change done here*/
		} else 
		{
		var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
		var temp_amount = temp_channel_avg_rate * total_ad_seconds/10;
                document.getElementById(amount_id).value = temp_amount.toFixed(2);
                total_amount += parseFloat(temp_amount.toFixed(2),10);
		}
	}
	var total_amount_id = 'network_'+network_id+'_total_amount';
	document.getElementById(total_amount_id).innerHTML = total_amount.toFixed(2);
	var network_share_id = 'network_'+network_id+'_network_share';
	var network_share = document.getElementById(network_share_id).value;
	var final_amount_id = 'network_'+network_id+'_final_amount';
	var final_amount = total_amount.toFixed(2) * network_share/100;
	document.getElementById(final_amount_id).value = final_amount.toFixed(2);
}
function find_rate(row_id,total_rows,network_id) {
	
	var total_amount = 0;
	for(var i = 0;i<total_rows;i++) 
	{
			if(i == row_id) {
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
			var amount = document.getElementById(amount_id).value;
			if(amount === "" || isNaN(amount) || amount < 0)
     			{       
	/*
  				alert("please enter Channel Amount");
  //				return false;
  			} else if(isNaN(amount))
  			{ 
    				alert("please enter numerical value as a  Channel Amount");
//				return false;
  			}else if(amount < 0) {
				alert("Please enter positive channel Amount");
//				return false;
			}
	//		alert(amount);
*/			
				alert("Please enter a positive numerical value for channel Amount");
			}else {
			var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
                //      alert(total_ad_sec_id);
                        var total_ad_seconds = document.getElementById(total_ad_sec_id).innerHTML;
			var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
//                      alert(channel_avg_rate_id);
                        var channel_avg_rate = amount/total_ad_seconds * 10;
			document.getElementById(channel_avg_rate_id).value = channel_avg_rate.toFixed(2);
			total_amount += parseFloat(amount,10);
			} }  else { 
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
                        var amount = document.getElementById(amount_id).value;
			total_amount += parseFloat(amount,10);
			}
	}
	 var total_amount_id = 'network_'+network_id+'_total_amount';
        document.getElementById(total_amount_id).innerHTML = total_amount.toFixed(2);
        var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = document.getElementById(network_share_id).value;
        var final_amount_id = 'network_'+network_id+'_final_amount';
        var final_amount = total_amount.toFixed(2) * network_share/100;
        document.getElementById(final_amount_id).value = final_amount.toFixed(2);
}
function find_final_amount(network_id,total_networks) {
	
	var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = document.getElementById(network_share_id).value;
//	alert(network_share);
	if(network_share==="" || isNaN(network_share) || network_share >100 || network_share < 0)
	{       
/*
		alert("pplease enter Network Share");
//		return false;
	} else if(isNaN(network_share))
	{ 
    		alert("please enter a numerical number as a share");
//		return false;
	} else if(network_share >100) {
    		alert("Please enter Network share less than or equal to 100");
//		return false;
	} else if(network_share < 0) {
    		alert("Please enter Network share greater than or equal to 0");
//		return false;
	}
*/
		alert("Please enter Network share greater than or equal to 0 and less than or equal to 100");
	} else {
	var total_amount_id = 'network_'+network_id+'_total_amount';
	var total_amount =  document.getElementById(total_amount_id).innerHTML;
//	alert(total_amount);
	var final_amount_id = 'network_'+network_id+'_final_amount';
        var final_amount = total_amount * network_share/100;
        document.getElementById(final_amount_id).value = final_amount.toFixed(2);
	}
}
/*-------------------- for bug 423 code changes starts here------------------*/
function find_channel_amounts(network_id,total_rows){

	var final_amount_id = 'network_'+network_id+'_final_amount';	
 	var final_amount = parseFloat(document.getElementById(final_amount_id).value);
	var total_amount_id = 'network_'+network_id+'_total_amount';
        var total_amount_old = parseFloat(document.getElementById(total_amount_id).innerHTML);
        var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = parseFloat(document.getElementById(network_share_id).value);
	/*------------------- For Bug 427 to make sure values not struck at character values -----------------*/
	if(final_amount==="" || isNaN(final_amount) || final_amount < 0)
       	{       
		alert("Please enter a positive numerical value for Network Payout(Net)");
	/* for bug: 427 code changes done here */
	} else {
		var total_amount_new = final_amount * 100/network_share;
		document.getElementById(total_amount_id).innerHTML = total_amount_new.toFixed(2);
		for(var i = 0;i<total_rows;i++)
		{
			var total_amount_id = 'network_'+network_id+'_total_amount';
		        var total_amount =  parseFloat(document.getElementById(total_amount_id).innerHTML);
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
                        var amount_old = parseFloat(document.getElementById(amount_id).value);
			var amount_new = total_amount * amount_old/total_amount_old;
			//alert('total amount:'+total_amount+'channel amount old:'+amount_old+'tot amount old:'+total_amount_old+'amount new:'+amount_new);
			document.getElementById(amount_id).value = amount_new.toFixed(2);
			var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
                //      alert(total_ad_sec_id);
                        var total_ad_seconds = parseFloat(document.getElementById(total_ad_sec_id).innerHTML);
			var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
//                      alert(channel_avg_rate_id);
			var amount = amount_new.toFixed(2);
                        var channel_avg_rate = amount/total_ad_seconds * 10;
                        document.getElementById(channel_avg_rate_id).value = channel_avg_rate.toFixed(2);
		}
	}
}
/* for bug:423 code changes done ehre */
function find_total_payout(total_networks) {
	
	var total_payout = 0;
	for(var i = 0;i<total_networks; i++) 
	{
		var final_amount_id = 'network_'+i+'_final_amount';
	        var final_amount = document.getElementById(final_amount_id).value;
		total_payout += parseFloat(final_amount,10);
	}
	net_payout = total_payout.toFixed(2);
	document.getElementById('total_network_payout').innerHTML =  net_payout;
	document.getElementById('total_network_payout1').innerHTML =  net_payout;
	var ro_amount = document.getElementById('net_amount').innerHTML;
	var ro_amount1 =  document.getElementById('net_amount1').innerHTML;
	 var surewaves_share1 = ro_amount1 - net_payout;
	var surewaves_share = ro_amount - net_payout;
	//changed by mani : 3rd august 2013
	var agency_commission_amount = document.getElementById('agency_amount').innerHTML;
	var agency_commission_amount1 =  document.getElementById('agency_amount1').innerHTML;
	
	var net_revenue = ro_amount - agency_commission_amount ;net_revenue = net_revenue * $("#service_tax_value").val();
	var net_revenue1 = ro_amount1 - agency_commission_amount1 ;	net_revenue1 = net_revenue1 * $("#service_tax_value").val();
	var net_revenue_from_innerhtml = document.getElementById('net_revenue').innerHTML;
	//var surewaves_share_per = surewaves_share/ro_amount*100;
	//var surewaves_share_per1 = surewaves_share1/ro_amount1*100;
	var surewaves_share_per = surewaves_share/net_revenue_from_innerhtml*100;
	var surewaves_share_per1 = surewaves_share1/net_revenue_from_innerhtml*100;
	
	document.getElementById('surewaves_share').innerHTML = surewaves_share.toFixed(2);
	document.getElementById('surewaves_share_per').innerHTML = surewaves_share_per.toFixed(2);
	document.getElementById('surewaves_share1').innerHTML = surewaves_share1.toFixed(2);
        document.getElementById('surewaves_share_per1').innerHTML = surewaves_share_per1.toFixed(2);
}
function show_channels(order_id) { 

	$.colorbox({href:"<?php echo ROOT_FOLDER ?>/ro_manager/show_channels/" + order_id,iframe:true,width:'515px',height:'374px'});
}
function approval_request(order_id,edit,id) {

	 window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approval_request/" + order_id + "/" + edit + "/" + id;
}

function add_ro_amount(external_ro) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_ro_amount/'+external_ro,iframe:true, width: '520px', height:'410px'}); 
}
function add_other_expenses(external_ro,order_id,edit,id) {
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_other_expenses/'+external_ro+'/'+ order_id + "/" + edit + "/" + id,iframe:true, width: '520px', height:'700px'});
}
function add_file_location(internal_ro,edit,id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_file_location/'+internal_ro+ "/" + edit + "/" + id,iframe:true, width: '530px', height:'400px'}); 
}
$(function() {

                $(".submitlong").click(function() {
                    var isValid = true;
			var ro_amount = document.getElementById('ro_amount').innerHTML;
			var value = document.getElementById('agency_rebate').innerHTML;
			if(ro_amount === 'In Process') {
				alert("Please Enter Gross CRO Amount details to approve RO");
				isValid = false;
			}
			 
			if(value === 'Missing') {
				alert("Please Enter Other Expenses details to approve RO");
				isValid = false;
			}
                    $("#myForm input[type=text]").each(function() {

                        if(isNaN(this.value) || (this.value < 0)|| (this.value ==='')) {
                            $(this).css('background', 'red');
                            isValid = false;
                        } else {
                            $(this).css('background', 'green');
                        }
                    });
                    if(isValid === false) {
                        return false;
                    } else {
			//Do everything you need to do with the form
                    }
                    
                });

            });
	function confirm_add_price_approve(id,edit,am_ro_id) {
		$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/confirm_add_price_approve/'+id + "/" + edit + "/" + am_ro_id,iframe:true, width: '520px', height:'540px'});
	}
	function cancel_ro(id,cust_ro,edit,internal_ro) {
		$.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/cancel_ro/'+id+'/'+cust_ro+ "/" + edit + "/" + internal_ro,iframe:true, width: '800px', height:'350px'});
	}
	function cancel_ro_admin(id,cust_ro,edit,internal_ro) {
		$.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/cancel_ro_admin/'+id+'/'+cust_ro+ "/" + edit + "/" + internal_ro,iframe:true, width: '800px', height:'350px'});
	}
	$(document).ready(function() {
		<?php 
			$selected_order_id = $this->session->userdata('selected_order_id');
			if ( isset($selected_order_id) && !empty($selected_order_id) ) { 
		?>
			confirm_add_price_approve('<?php echo $selected_order_id ?>','<?php echo $edit ?>','<?php echo $id ?>');
		<?php 
			$this->session->unset_userdata("selected_order_id");
			} 
		?>
	});	
/*function edit_ext_ro(id) {
	$.colorbox({href:'<?php //echo ROOT_FOLDER ?>/account_manager/edit_ext_ro/'+id,iframe:true, width: '940px', height:'650px'});
}*/
function am_edit_ext_ro(id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/am_edit_ext_ro/'+id,iframe:true, width: '940px', height:'650px'});
}
function cancel_channel(channel_id,order_id,edit,am_ro_id,chnl_name){
	/*if(confirm("Are you sure !")){
		window.location = '<?php echo ROOT_FOLDER ?>/ro_manager/cancel_channel_from_ro/'+channel_id+'/'+ order_id + "/" + edit + "/" + am_ro_id;
	}*/
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/confirm_cancel_channel/'+channel_id + "/" + order_id + "/" + edit + "/" + am_ro_id + "/" + chnl_name,iframe:true, width: '520px', height:'300px'});
}
function edit_network(internal_ro_number,customer_id,edit,id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/edit_network/'+internal_ro_number+'/'+customer_id+'/'+edit+'/'+id,iframe:true, width: '820px', height:'600px'});
}
function cancel_nw_channel(internal_ro_number,customer_id,edit,id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/cancel_nw_channel/'+internal_ro_number+'/'+customer_id+'/'+edit+'/'+id,iframe:true, width: '520px', height:'500px'});
}

</script>
<script type="text/javascript">

    $('#view_more').click(function(){
        var div = $('#recs');
        var start= div.children('table').length ;
        //var start= div.children().length/2 ;
        var nw_loaded = $("#nw_load_status").val();
        var end_of_nw = 0;
        if(!div.hasClass('ended')) {
            $.ajax({
                type : 'GET',
                url : '<?php echo ROOT_FOLDER ?>/ro_manager/approval_pagination/<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>/<?php echo $edit; ?>/<?php echo $id; ?>/'+start ,
                //data : 'lastelement='+start,
                beforeSend:function() {
                    //$('.getmore').html('Loading ......');
                },
                success:function(data){//alert(data)
                    if(data == ''){
                        end_of_nw = 1;
                        $('#view_more').text('No more Networks');
                        $('#view_all').text('No more Networks');
                    }else{
                        div.append(data);
                    }
                    /*data_with_net_payout_loaded = data_with_net_payout_loaded.split('~');
                    data = data_with_net_payout_loaded[0];
                    if(data_with_net_payout_loaded[1] != ''){
                        net_payout_loaded_ajax = parseFloat(document.getElementById("net_payout_loaded").innerHTML);
                        document.getElementById("net_payout_loaded").innerHTML = net_payout_loaded_ajax + parseFloat(data_with_net_payout_loaded[1]);
                    }
                    if(data !== 'end') {
                        div.append(data);
                    }else {
                        if(!div.hasClass('ended')){
                            alert("no result found") ;
                            div.addClass('ended') ;
                        }
                    }*/

                },
                complete:function(){
                    $("#nw_load_status").val(start);

                    //Calling again to load all networks
                    if($("#load_all_nw").val() == 1 && end_of_nw != 1){
                        $("#view_more").trigger("click");
                    }

                }
            });
        }
    });

    $("#view_all").click(function(){
        $("#load_all_nw").val(1);
        $("#view_more").trigger("click");
    });

    $(window).scroll(function(){

        //for handling scroll to top button
        if ($(this).scrollTop() > 100) {
            $('.go-to-top').fadeIn();
        } else {
            $('.go-to-top').fadeOut();
        }
    });


    $('.go-to-top').click(function() {
        $("html, body").animate({
            scrollTop: 0
        }, 1500);
        return false;
    });

</script>