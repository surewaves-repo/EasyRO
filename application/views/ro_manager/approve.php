<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/flexigrid.pack.css" />
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/jquery-ui.css" />
<script type="text/javascript" src="/surewaves_easy_ro/js/flexigrid.js"></script>
    <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
<style type="text/css" media="all">
	#cancel_loader	{
        background: none repeat scroll 0 0 #FFFFFF;
        border-radius: 10px;
        border: 5px solid #A2A2A2;
        display: block;
        font-weight: bold;
        height: 100px;
        left: 38%;
        padding-top: 15px;
        position: absolute;
        text-align: center;
        top: 45%;
        width: 280px;
        z-index: 9999;
    }
    #overlay {
        background-color: #000;
        filter:alpha(opacity=60); /* IE */
        opacity: 0.6; /* Safari, Opera */
        -moz-opacity:0.66; /* FireFox */
        z-index: 20;
        height: 200%;
        width: 100%;
        background-repeat:no-repeat;
        background-position:center;
        position:absolute;
        top: 0px;
        left: 0px;
    }

    .go-to-top:hover	{
        cursor:pointer;
    }

    .go-to-top	{
        width:40px;
        height:40px;
        opacity:0.3;
        position:fixed;
        bottom:30px;
        right:20px;
        display:none;
        text-indent:-9999px;
        background: url('<?php echo ROOT_FOLDER ?>/images/top-button.png') no-repeat;
    }
</style>

<div id="hld">

    <div id="cancel_loader" style="display:none;"><img src='<?php echo ROOT_FOLDER ?>/images/loader_full.GIF' height="50" width="50"/><br><br>Cancelling Channels ...</div>
		<div class="wrapper">		<!-- wrapper begins -->	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
				
				<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				
				<?php echo $menu; ?>
				
				<p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
						<div class="block">
			
				
				
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
					<!-- .block_head ends -->
                        <div class="block_content">
						<table cellpadding="0" cellspacing="0" width="100%">						
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
						</tr><tr>
									
									<td><a href="javascript:show_channels('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>')">Show Channels</a></td>
									<td><a href="javascript:campaigns_schedule('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Campaigns Schedule</a></td>
									<td><a href="javascript:channels_schedule('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Channels Schedule</a></td>
									<td><a href="javascript:nw_ro_payment('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Network Ro Payment</a></td>
                                                                        <!--<td><a href="javascript:mis_report('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Mis Report</a></td>-->

                                    <?php if($verify_ro_approved == 0) {?>
                                        <td><a href="javascript:change_ro_dates('<?php echo $id ?>')">Change RO Dates</a></td>
                                    <?php } ?>

                                    <?php if($is_ro_completed == 1){?>
                                        <td>
                                            <span style="color:#F00;"> Ro Completed </span>
                                        </td>
                                    <?php } else {
                                        if($is_cancel_market_requested == 0){
                                            //code commented for new requirement(Do not show cancel ro on approval page) in release 2.9.6 by Nitish
                                            /*if($cancel_request_status == 0) { */?><!--
                                            <td>
                                                <a href="javascript:cancel_ro_admin('<?php /*echo $id */?>','<?php /*echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') */?>','<?php /*echo $edit */?>','<?php /*echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') */?>')" style="color:#F00;">
                                                    Cancel RO Request
                                                </a>
                                            </td>
                                            --><?php /*}else*/

                                            if($cancel_request_status == 1) {?>
                                            <td>
                                                <span style="color:#F00;"> Cancelled </span>
                                            </td>
                                            <?php }
                                            /*else if($cancel_request_status == 2) {*/?><!--
                                            <td>
                                                <a href="javascript:cancel_ro_admin('<?php /*echo $id */?>','<?php /*echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') */?>','<?php /*echo $edit */?>','<?php /*echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') */?>')" style="color:#F00;">
                                                    Cancel RO
                                                </a>
                                            </td>
                                            --><?php /*}*/
                                        }
                                    }?>

                                </tr>
													 
						</table><br>

                        <?php if($is_cancel_market_requested == 1){ ?>
                            <div style="text-align: center">
                                <span style='color:red;'>Can't Cancel RO! Market Cancellation Request is Pending !! </span>
                            </div>
                        <?php } ?>

					<div class="paggination right">
							<?php echo $page_links ?>
					</div>		<!-- .paggination ends -->
					</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>	<!-- .block ends -->	
					
					
				
			
			<?php 
			if(count($scheduled_data) == 0){	
			?>
				<div class="block">

                                <div class="block_head">
                                        <div class="bheadl"></div>
                                        <div class="bheadr"></div>
				</div>
			<div class="block_content">
				<h2 style="text-align:center;">No Active Campaign !</h2>	
			</div>	
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>
			<?php } else{ ?>
			<div class="block">
			
				<div class="block_head">
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
                                                                                                                if($ch['cancel_channel'] == 'no') {
                                                                                                                    $total_amount += $ch['channel_spot_amount'] ;
                                                                                                                }   
														
													}
													if($ch['total_banner_ad_seconds'] != 0){
														$total_rows++;
                                                                                                                if($ch['cancel_channel'] == 'no') {
                                                                                                                    $total_amount += $ch['channel_banner_amount'] ;
                                                                                                                }   
														
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

                <?php if($verify_ro_approved != 1){ ?>
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/saving_data_for_confirmation" method="post" id="myForm">
                    <?php } ?>
                    <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
                    <input type="hidden" name="hid_id" value="<?php echo $id ?>">
                    <input type="hidden" name="hid_internal_ro" value="<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>">
                    <input type="hidden" id="hid_order_id" value="<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>">
                    <input type="hidden" id="hid_is_market_cancellation_requested" name="hid_is_market_cancellation_requested" value="<?php echo $is_cancel_market_requested ?>">
                    <input type="hidden" name="nw_load_status" id="nw_load_status" value="0">
                    <input type="hidden" name="load_all_nw" id="load_all_nw" value="0">

				<div id="fixedSummary">
					<table cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold">
						<tr cellpadding="0" cellspacing="0" width="100%">
							<th>Gross CRO Amount</th>
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
							<td id="other_expenses" style="color:red;"><?php echo "Missing" ?><?php if($logged_in_user['profile_id'] == 1) { ?><a href="javascript:add_other_expenses('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') ?>','<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">View/Modify</a><?php } ?></td>
							<?php $agency_rebate = 0; $other_expenses = 0;} else { ?>
							<td id="agency_rebate" style="color:green;"><?php 
							if($ro_amount_detail[0]['agency_rebate_on'] != 'net_amount'){
								$agency_rebate = round($ro_amount_detail[0]['ro_amount'],2)*round($ro_amount_detail[0]['agency_rebate'],2)/100;
							}
							else{
								$agency_rebate = round(($ro_amount_detail[0]['ro_amount'] - $ro_amount_detail[0]['agency_commission_amount']),2)*round($ro_amount_detail[0]['agency_rebate'],2)/100;
							}
							echo $agency_rebate; ?></td>
							<td id="other_expenses" style="color:green;"><?php $other_expenses = $ro_amount_detail[0]['marketing_promotion_amount']+$ro_amount_detail[0]['field_activation_amount']+$ro_amount_detail[0]['sales_commissions_amount']+$ro_amount_detail[0]['creative_services_amount']+$ro_amount_detail[0]['other_expenses_amount']; echo $other_expenses; ?><?php if($logged_in_user['profile_id'] == 1) { ?><a href="javascript:add_other_expenses('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') ?>','<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">View/Modify</a><?php } ?></td>
							<?php } ?>
							<!--@mani:added on 8.7.2013: $net_revenue -->
							<td id="net_amount"><?php $actual_net_amount =  round($ro_amount_detail[0]['ro_amount'],2)- round($ro_amount_detail[0]['agency_commission_amount'],2)-$agency_rebate-$other_expenses;echo $actual_net_amount; ?></td>
							<td id="net_revenue"><?php $net_revenue = round($ro_amount_detail[0]['ro_amount'],2)- round($ro_amount_detail[0]['agency_commission_amount'],2) ; $net_revenue = round(($net_revenue*SERVICE_TAX),2) ; echo $net_revenue; ?></td>
							<td id="total_network_payout"><?php /*$net_payout = round($final_amount);*/echo $total_nw_payout; ?></td><input type="hidden" id="hid_total_network_payout" name="hid_total_network_payout" value="<?php echo $total_nw_payout; ?>" />
							<td id="surewaves_share"><?php $surewaves_share = $actual_net_amount - $total_nw_payout; echo $surewaves_share; ?></td><input type="hidden" id="hid_surewaves_share" name="hid_surewaves_share" value="<?php $surewaves_share = $actual_net_amount - $total_nw_payout; echo $surewaves_share; ?>" />
							<td id="surewaves_share_per"><?php echo round(($surewaves_share/$net_revenue*100),2) ?></td><input type="hidden" id="hid_surewaves_share_per" name="hid_surewaves_share_per" value="<?php echo round(($surewaves_share/$net_revenue*100),2) ?>" />
						</tr>
						<?php } else { ?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td id="ro_amount">In Process</td>
							<td id="agency_amount">In Process</td>
							<td id="amount">In Process</td>
							<td id="agency_rebate">In Process</td>
							<td id"other_expenses"><?php $other_expenses = $ro_amount_detail[0]['marketing_promotion_amount']+$ro_amount_detail[0]['field_activation_amount']+$ro_amount_detail[0]['sales_commissions_amount']+$ro_amount_detail[0]['creative_services_amount']+$ro_amount_detail[0]['other_expenses_amount']; echo $other_expenses; ?>
							<?php if($logged_in_user['profile_id'] == 1) { ?><a href="javascript:add_other_expenses('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') ?>','<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">View/Modify</a><?php } ?></td>
							<td id="net_amount">In Process</td>
							<td id="net_revenue">In Process</td>
							<td id="total_network_payout"><?php /*$net_payout = round($final_amount);*/echo $total_nw_payout; ?></td>
							<td>In Process</td>
                                                        <td>In Process</td>
						</tr>
						<?php } ?>
						
                                               
                            <?php if($logged_in_user['profile_id'] == 1) { ?>               
								<tr cellpadding="0" cellspacing="0" width="100%">
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<?php
										if($reset_cache_button == 'yes'){ ?>
												<td><input type="button" id="revert" class="submitlong" name="apa" value="Revert Changes" onclick="javascript:revert_changes_for_ro('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')"/></td>
										<? }else{ ?>
												<td>&nbsp;</td>
										<?}				 
										if($ro_approval[0]['ro_approval_request_status']==1 ||  $ro_amount_detail[0]['ro_approval_status'] == 1) {
											// added by lokanath to show approval button only when requested
											if($ro_status_entry == 'Approval Requested' && $is_cancel_market_requested == 0){ ?>
												<td><input type="submit" id="submit_approve1" class="submitlong" name="apa" value="Approve" onclick="javascript:show_processing()" /><span id="submit_processing1" style="display:none;color:#008000">Processing..</span></td> <?php
											} else if($ro_status_entry == 'Approval Requested' && $is_cancel_market_requested == 1){?>
                                                <td><span style='color:red;font-weight: normal;'>Can't Approve RO! Market Cancellation Request is Pending !! </span></td>
                                            <?php }
                                        } else {?>
                                            <td><span style='color:red;font-weight: normal;'>Can't Approve RO! Waiting for Approval Request </span></td>
                                        <?php }?>

                                        <td>
                                        <?php if($verify_ro_approved == 1 && $final_save == 1 ){ ?>
                                        <!--<a href="<?php echo ROOT_FOLDER ?>/ro_manager/final_save_to_cache/<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>/<?php echo $edit; ?>/<?php echo $id; ?>">
                                        <input type="button" id="final_save" class="submitlong" name="final_save" value="Final Save" />
                                        </a>-->
                                        <?php } ?>
                                        <a href="<?php echo ROOT_FOLDER ?>/ro_manager/final_save_to_cache/<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>/<?php echo $edit; ?>/<?php echo $id; ?>"><input type="button" id="final_save1" class="submitlong" name="final_save1" value="Final Save" style="display:none;" onclick="return validateAllNetworks()"/></a>
                                        </td>
                                        
										
								</tr>
							<?php } ?>
                                                
                                                
                       <?php } ?>
					</table>
				</div>

                    <?php if($ro_status_entry == 'Approval Requested' && $is_cancel_market_requested == 0 && $logged_in_user['profile_id'] == 1) {?>

                    <!--div to allow cancellation as per channel priority-->
                    <div class="block_content">
                    <div id="accordion">
                        <h3>Market Priority</h3>
                        <div>
                            <table cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold">
                                <tr cellpadding="0" cellspacing="0" width="100%" style="background-color: #f5f5f5;">
                                    <th style="width:25%">Market</th>
                                    <?php foreach($priority as $val){ ?>
                                        <th style="width:15%"><?php echo strtoupper($val); ?></th>
                                    <?php } ?>
                                </tr>

                                <?php foreach($market_priority_details as $mkt){ ?>
                                    <tr>
                                        <td><?php echo $mkt['market_name']?></td>
                                        <?php foreach($priority as $val){
                                            if(array_key_exists($val,$mkt['channel_priority'])){ ?>
                                                <td><input type="checkbox" name="<?php echo $mkt['market_id']."_".$val ?>" id="<?php echo $mkt['market_id']."_".$val ?>" class="cancel_priorities_all cancel_priorities_<?php echo $mkt['market_id'] ?>" value="<?php echo $val?>" <?php if($marketId_priority_data[$mkt['market_id']."_".$val] != 0 || $marketId_priority_data[$mkt['market_id']."_".$val] == null){ echo "checked='checked'"; }?> onclick="cancel_channels_by_priority('<?php echo $mkt['market_id']?>','<?php echo $val ?>','<?php echo $id ?>','<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>')"></td>
                                        <?php }else{ ?>
                                                <td>&nbsp</td>
                                        <?php }
                                        }?>

                                    </tr>
                                <?php } ?>

                            </table>

                        </div>
                    </div>

                    <?php } ?>


                                        <!--<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_add_price_approve" method="post">-->
					<div id="recs">
					<?php 
						$count = 0 ;
						if(count($scheduled_data) > 0) {
						//foreach($scheduled_data as $scheduled_key => $data) {
							$net_payout_loaded = 0;
							foreach($scheduled_data as $key => $dat) { 
							$total_networks = count($scheduled_data);
                                                        $row_id = 0;
					?>
                    <?php if($verify_ro_approved == 1){ ?>
                    <form id="form_<?php echo $dat['network_id'] ?>" name="form_<?php echo $dat['network_id'] ?>" method="post" action="<?php echo ROOT_FOLDER ?>/ro_manager/save_to_cache" target="iframe_<?php echo $dat['network_id'] ?>">
                    <?php } ?>
					<table id ="<?php echo 'network_'.$key ?>" cellpadding="0" cellspacing="0" width="100%">
						<tr cellpadding="0" cellspacing="0" width="100%" >
							<td colspan="4" style="font-weight:bold;background:#DDDDDD;size:20px; width:400px;">Network Name: <?php echo $dat['network_name'] ?> &nbsp; (<?php echo $dat['market_name'] ?>)</td>
                            <td>
                            <?php 
									if($is_ro_completed == 0 && ($dat['approved'] == 1  || $verify_ro_approved == 1)){ ?>
									<input type="button" value="Edit" class="submit" onclick="modify_network(<?php echo $dat['network_id'];?>)">&nbsp;<input type="submit" value="Save" class="submit" onclick="return save_to_cache(<?php echo $dat['network_id'];?>);" title="Changes will be discarded if not submitted">
                                                                        <?php }else{echo '&nbsp;';} ?>
                            
                            </td>
						</tr>
						<?php 	$total_amount = 0;
							$approved_total_amount = 0;
							foreach($dat['channels_data_array'] as $chnl => $ch) { 
							$total_channel =  count($dat['channels_data_array']);
							$channel_id = $ch['channel_id'];
							$customer_id = $dat['network_id'];
						?>
                                                <input type="hidden" id="<?php echo $channel_id.'_approved'; ?>" value="<?php echo $ch['approved'] ?>" />
                                                <tr cellpadding="0" cellspacing="0" width="100%" id="row_<?php echo $key.'_'.$row_id; ?>"  >
                                                        <td class="channel_td"  id="row_<?php echo $key.'_'.$row_id.'_td'; ?>" style="font-weight:bold;<?php if($ch['cancel_channel'] == 'yes') {echo "background-color:#ff9999"; } else if($ch['additional_campaign'] == 1){ echo "background-color:#F3F781";}else{echo 'colspan="3"';} ?> ">
                                                        <input type="checkbox" name="channels[<?php echo $customer_id; ?>][]" value="<?php echo $channel_id ?>" onchange="change_channel_data(<?php echo $key; ?>,<?php echo $row_id; ?>,<?php echo $channel_id; ?>)" <? if($ch['cancel_channel'] == 'no'){ echo "checked='checked'";}?> id="channel_<?php echo $channel_id?>" class="show_checkbox_<?php echo $customer_id;?>" <?php if($dat['approved'] == 1){echo 'disabled';}?> />
                                                        <input type="hidden" name="cancel_channel[]" id="hid_cancel_channel_<?php echo $channel_id?>"  />
                                                        <input type="hidden" id="row_<?php echo $key.'_'.$row_id.'_additional_campaign';?>" value="<?php echo $ch['additional_campaign']; ?>" />
                                                        &nbsp;
                                                        Channel Name: <?php echo $ch['channel_name'] ?> 
                                                        </td>
							<?php if($ch['additional_campaign'] == 1){ ?>
									<input type="hidden" name="changed_network_<?php echo $dat['network_id'];?>[]"  class="changed_network changed_network_<?php echo $dat['network_id'];?>" value="0">
							<?php }	?>					
							<? if($ch['cancel_channel'] == 'no' && $ch['additional_campaign'] == 0){ ?>
							<td style="font-weight:bold" align="right">
                                                        </td> <?php } ?>
                                                </tr>
						 <tr cellpadding="0" cellspacing="0" width="100%">
                                                        <th colspan="3">Ad Type</th>
                                                        <th colspan="3">Total Ad Seconds</th>
                                                        <th colspan="3">Last Rate</th>
                                                        <th colspan="3">Reference Rate</th>
                                                        <th colspan="3">Amount</th>
                                                </tr>
						<?php //foreach($ch['ad_types'] as $types => $ad) 
							if($ch['total_spot_ad_seconds'] != 0)
							{
								//$total_ad_types = count($ch['ad_types']);
								$amount = 0;
								$rate = $ch['channel_spot_avg_rate'];
                                                                $amount += $ch['total_spot_ad_seconds'] * $rate/10;
                                                                if($ch['cancel_channel'] == 'no') {
                                                                    $total_amount += $amount;
                                                                }    
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Spot Ad</td>
							<td colspan="3" class="<?php echo "seconds_".$channel_id."_spot" ?>" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0'; ?>" ><?php echo $ch['total_spot_ad_seconds'] ?></td>
                                                        <input type="hidden" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0_hidden_play_spot'; ?>" value="<?php echo $ch['to_play_spot'] ?>" />
                                                        <input type="hidden" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0_hidden_schedule_spot'; ?>" value="<?php echo $ch['total_spot_ad_seconds'] ?>" />
                                                        <?php  
								if($ch['approved'] == 1) { ?>
							<td colspan="3" class="hide_<?php echo $customer_id;?>"><?php echo $ch['channel_spot_avg_rate'] ?></td>
                            <td colspan="3" class="show_<?php echo $customer_id;?>" style="display:none;"><label><?php echo form_error('channel_avg_rate'); ?></label><input type="text" class="rate" <?php echo $readOnly ?> name="post_channel_avg_rate[<?php echo $channel_id; ?>][0]" id="<?php echo 'network_'.$key.'_channel_'.$row_id.'_1'; ?>" value="<?php echo set_value('post_channel_avg_rate[<?php echo $channel_id; ?>][0]',$rate); ?>" onchange = "find_price(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" <?php if($ch['cancel_channel'] == 'yes') {echo "readonly='readonly'"; } ?> /> </td>
							<?php } else { ?>
							<td colspan="3" class="show_<?php echo $customer_id;?>"  > <label><?php echo form_error('channel_avg_rate'); ?></label><input type="text" class="rate" <?php echo $readOnly ?> name="post_channel_avg_rate[<?php echo $channel_id; ?>][0]" id="<?php echo 'network_'.$key.'_channel_'.$row_id.'_1'; ?>" value="<?php echo set_value('post_channel_avg_rate[<?php echo $channel_id; ?>][0]',$rate); ?>" onchange = "find_price(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" <?php if($ch['cancel_channel'] == 'yes') {echo "readonly='readonly'"; } ?> /> </td>
							<?php }?>

                            <td colspan="3"><?php echo $ch['channel_spot_reference_rate'] ?></td>
                            <input type="hidden" name="post_channel_ref_rate[<?php echo $channel_id; ?>][0]" value="<?php echo $ch['channel_spot_reference_rate'] ?>" />

                            <?php if($ch['approved'] == 1) {
							$approved_total_amount += $ch['channel_spot_amount'] ?>
							<td colspan="3" class="hide_<?php echo $customer_id;?>"><?php echo round($ch['channel_spot_amount'],2) ?></td>
                            <td colspan="3" class="show_<?php echo $customer_id;?>" style="display:none;"><label><?php echo form_error('channel_amount'); ?></label><input type="text" class="amount" <?php echo $readOnly ?> name="post_channel_amount[<?php echo $channel_id; ?>][0]" id="<?php echo 'network_'.$key.'_amount_'.$row_id.'_2'; ?>" value="<?php echo set_value('post_channel_amount[<?php echo $channel_id; ?>][0]',round($amount,2)); ?>" onchange = "find_rate(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" <?php if($ch['cancel_channel'] == 'yes') {echo "readonly='readonly'"; } ?> />
							<input type="hidden" name="hid_total_seconds[<?php echo $channel_id; ?>][0]" value="<?php echo $ch['total_spot_ad_seconds'] ?>" /> 
							<input type="hidden" name="hid_client_name" value="<?php echo $submit_ext_ro_data[0]['client'] ?>" />
							<input type="hidden" name="hid_internal_ro" value="<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>" />
							<input type="hidden" name="hid_cust_id[<?php echo $customer_id; ?>]" value="<?php echo $customer_id ?>" />
							
                                                        <input type="hidden" name="total_rows" value="<?php echo $rows[$customer_id]; ?>" /></td>
                            
                                                        <?php } else { ?>
							<td colspan="3" class="show_<?php echo $customer_id;?>" ><label><?php echo form_error('channel_amount'); ?></label><input type="text" class="amount" <?php echo $readOnly ?> name="post_channel_amount[<?php echo $channel_id; ?>][0]" id="<?php echo 'network_'.$key.'_amount_'.$row_id.'_2'; ?>" value="<?php echo set_value('post_channel_amount[<?php echo $channel_id; ?>][0]',round($amount,2)); ?>" onchange = "find_rate(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" <?php if($ch['cancel_channel'] == 'yes') {echo "readonly='readonly'"; } ?> />
							<input type="hidden" name="hid_total_seconds[<?php echo $channel_id; ?>][0]" value="<?php echo $ch['total_spot_ad_seconds'] ?>" /> 
							<input type="hidden" name="hid_client_name" value="<?php echo $submit_ext_ro_data[0]['client'] ?>" />
							<input type="hidden" name="hid_internal_ro" value="<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>" />
							<input type="hidden" name="hid_cust_id[<?php echo $customer_id; ?>]" value="<?php echo $customer_id ?>" />
							
                                                        <input type="hidden" name="total_rows" value="<?php echo $rows[$customer_id]; ?>" /></td>								<?php } ?>
						</tr>
						<?php $row_id++; ?>
						<?php  }
						if($ch['total_banner_ad_seconds'] != 0)
							{
								//$total_ad_types = count($ch['ad_types']);
								$amount = 0;
								$rate = $ch['channel_banner_avg_rate'];
                                                                $amount += $ch['total_banner_ad_seconds'] * $rate/10;
                                                                if($ch['cancel_channel'] == 'no') {
                                                                    $total_amount += $amount;
                                                                }    
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Banner Ad</td>
							<td colspan="3" class="<?php echo "seconds_".$channel_id."_banner" ?>" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0'; ?>" ><?php echo $ch['total_banner_ad_seconds'] ?></td>
                                                        <input type="hidden" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0_hidden_play_banner'; ?>" value="<?php echo $ch['to_play_banner'] ?>" />
							<input type="hidden" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0_hidden_schedule_banner'; ?>" value="<?php echo $ch['total_banner_ad_seconds'] ?>" />
							<?php  
								if($ch['approved'] == 1) { ?>
							<td colspan="3" class="hide_<?php echo $customer_id;?>"><?php echo $ch['channel_banner_avg_rate'] ?></td>
                            <td colspan="3" class="show_<?php echo $customer_id;?>" style="display:none;"><label><?php echo form_error('channel_avg_rate'); ?></label><input type="text" class="rate"  <?php echo $readOnly ?> name="post_channel_avg_rate[<?php echo $channel_id; ?>][1]" id="<?php echo 'network_'.$key.'_channel_'.$row_id.'_1'; ?>" value="<?php echo set_value('post_channel_avg_rate[<?php echo $channel_id; ?>][1]',$rate); ?>" onchange = "find_price(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" /> </td>
							<?php } else { ?>
							<td colspan="3"><label><?php echo form_error('channel_avg_rate'); ?></label><input type="text" class="rate" <?php echo $readOnly ?> name="post_channel_avg_rate[<?php echo $channel_id; ?>][1]" id="<?php echo 'network_'.$key.'_channel_'.$row_id.'_1'; ?>" value="<?php echo set_value('post_channel_avg_rate[<?php echo $channel_id; ?>][1]',$rate); ?>" onchange = "find_price(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" /> </td>
							<?php }?>

                            <td colspan="3"><?php echo $ch['channel_banner_reference_rate'] ?></td>
                            <input type="hidden" name="post_channel_ref_rate[<?php echo $channel_id; ?>][1]" value="<?php echo $ch['channel_banner_reference_rate'] ?>" />

                            <?php if($ch['approved'] == 1) {
								$approved_total_amount += $ch['channel_banner_amount'] ?>
							<td colspan="3" class="hide_<?php echo $customer_id;?>"><?php echo round($ch['channel_banner_amount'],2) ?></td>
                            <td colspan="3" class="show_<?php echo $customer_id;?>" style="display:none;"><label><?php echo form_error('channel_amount'); ?></label><input type="text" class="amount" <?php echo $readOnly ?> name="post_channel_amount[<?php echo $channel_id; ?>][1]" id="<?php echo 'network_'.$key.'_amount_'.$row_id.'_2'; ?>" value="<?php echo set_value('post_channel_amount[<?php echo $channel_id; ?>][1]',round($amount,2)); ?>" onchange = "find_rate(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" />
							<input type="hidden" name="hid_total_seconds[<?php echo $channel_id; ?>][1]" value="<?php echo $ch['total_banner_ad_seconds'] ?>" /> 
							<input type="hidden" name="hid_client_name" value="<?php echo $submit_ext_ro_data[0]['client'] ?>" />
							<input type="hidden" name="hid_internal_ro" value="<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>" />
							<input type="hidden" name="hid_cust_id[<?php echo $customer_id; ?>]" value="<?php echo $customer_id ?>" />
							
                                                        <input type="hidden" name="total_rows" value="<?php echo $rows[$customer_id]; ?>" /></td>
                            
                                                        <?php } else { ?>
							<td colspan="3"><label><?php echo form_error('channel_amount'); ?></label><input type="text" class="amount" <?php echo $readOnly ?> name="post_channel_amount[<?php echo $channel_id; ?>][1]" id="<?php echo 'network_'.$key.'_amount_'.$row_id.'_2'; ?>" value="<?php echo set_value('post_channel_amount[<?php echo $channel_id; ?>][1]',round($amount,2)); ?>" onchange = "find_rate(<?php echo $row_id ?>,<?php echo $rows[$customer_id] ?>,<?php echo $key ?>);find_total_payout(<?php echo $total_networks ?>);" />
							<input type="hidden" name="hid_total_seconds[<?php echo $channel_id; ?>][1]" value="<?php echo $ch['total_banner_ad_seconds'] ?>" />
							<input type="hidden" name="hid_client_name" value="<?php echo $submit_ext_ro_data[0]['client'] ?>" />
							<input type="hidden" name="hid_internal_ro" value="<?php echo rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>" />
							<input type="hidden" name="hid_cust_id[<?php echo $customer_id; ?>]" value="<?php echo $customer_id ?>" />
							
                                                        <input type="hidden" name="total_rows" value="<?php echo $rows[$customer_id]; ?>" /></td>								<?php } ?>
						</tr>
                        <?php $row_id++; ?>
						<?php  } ?>
						<?php } ?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Network RO Amount</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
                            <td colspan="3">&nbsp;</td>
							<?php  //if($approved_channel_details[$channel_id]['channel_approval_status'] == 1) { ?>
							<!--<td colspan="3"><?php //echo $approved_total_amount ?></td>-->
							<?php //} else { ?>
                                                        <td colspan="3" id="<?php echo 'network_'.$key.'_total_amount'; ?>" ><?php echo round($total_amount,2) ; ?></td>
							<?php //} ?>
						</tr>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Network Share(%)</td>
							<td colspan="3">&nbsp;</td>
                            <td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<?php  if($dat['approved'] == 1) { ?>
							<td colspan="3" class="hide_<?php echo $customer_id;?>"><?php echo $dat['revenue_sharing'] ?></td>
                            <td colspan="3" class="show_<?php echo $customer_id;?>" style="display:none;"><label><?php echo form_error('network_share'); ?></label><input type="text" class="network_share"  <?php echo $readOnly ?> name="post_network_share[<?php echo $customer_id; ?>]" id="<?php echo 'network_'.$key.'_network_share'; ?>" value="<?php echo set_value('post_network_share[<?php echo $customer_id; ?>]', $dat['revenue_sharing']);?>" onchange ="find_final_amount(<?php echo $key ?>,<?php echo $total_networks ?>);find_total_payout(<?php echo $total_networks ?>);" <?php if($ch['cancel_channel'] == 'yes') {echo "readonly='readonly'"; } ?> /> </td>
                                                        <?php } else { ?>
							<td colspan="3"><label><?php echo form_error('network_share'); ?></label><input type="text" class="network_share" <?php echo $readOnly ?>  name="post_network_share[<?php echo $customer_id; ?>]" id="<?php echo 'network_'.$key.'_network_share'; ?>" value="<?php echo set_value('post_network_share[<?php echo $customer_id; ?>]', $dat['revenue_sharing']);?>" onchange ="find_final_amount(<?php echo $key ?>,<?php echo $total_networks ?>);find_total_payout(<?php echo $total_networks ?>);" <?php if($ch['cancel_channel'] == 'yes') {echo "readonly='readonly'"; } ?> /> </td>
							<?php } ?>
						</tr>
						<tr cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold">
							<td colspan="3">Network Payout(Net)</td>
							<td colspan="3">&nbsp;</td>
                            <td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<?php  if($dat['approved'] == 1) {
								$net_payout_loaded +=  round($total_amount * $dat['revenue_sharing']/100,2); ?>
							<td colspan="3" class="hide_<?php echo $customer_id;?>"><?php echo  round($total_amount * $dat['revenue_sharing']/100,2) ?>
                            </td>
                            <td colspan="3" style="font-weight:bold;display:none;" class="show_<?php echo $customer_id;?>"><label><?php echo form_error('final_amount'); ?></label><input type="text" class="final_amount" <?php echo $readOnly ?> name="post_final_amount[<?php echo $customer_id; ?>]" id="<?php echo 'network_'.$key.'_final_amount'; ?>" value="<?php echo set_value('post_final_amount[<?php echo $customer_id; ?>]',round($dat['nw_payout'],2)); ?>" onchange ="find_channel_amounts(<?php echo $key ?>,<?php echo $rows[$customer_id] ?>);find_total_payout(<?php echo $total_networks ?>);" <?php if($ch['cancel_channel'] == 'yes') {echo "readonly='readonly'"; } ?> /> </td>
                                                        <?php } else {
														$net_payout_loaded +=  round($total_amount * $dat['revenue_sharing']/100,2);														
														?>
							<td colspan="3" style="font-weight:bold"><label><?php echo form_error('final_amount'); ?></label><input type="text" class="final_amount" <?php echo $readOnly ?> name="post_final_amount[<?php echo $customer_id; ?>]" id="<?php echo 'network_'.$key.'_final_amount'; ?>" value="<?php echo set_value('post_final_amount[<?php echo $customer_id; ?>]',round($dat['nw_payout'],2)); ?>" onchange ="find_channel_amounts(<?php echo $key ?>,<?php echo $rows[$customer_id] ?>);find_total_payout(<?php echo $total_networks ?>);" <?php if($ch['cancel_channel'] == 'yes') {echo "readonly='readonly'"; } ?> /> </td>
							<?php } ?>
						</tr>
						<?php 
							
						
							
								$count++ ;
								$lastCount = $count ;
							//}
						//}
						
						?>
					</table>

                    <?php if($verify_ro_approved == 1){ ?>
                    </form>
                    <?php } ?>
                    <iframe id="iframe_<?php echo $dat['network_id']; ?>" name="iframe_<?php echo $dat['network_id']; ?>" style="position: absolute; left: -1000px; top: -1000px;" src="about&#058;blank"></iframe>
<?php } ?>
                    </div>
					
					<div style="text-align:center;margin: 5px 5px;width:47%;float:left;position: relative;background-color: #6FAFD8;color: #FFF;padding: 10px;font-size: 14px;border-radius:4px !important;cursor: pointer;" id="view_more">
					    View More Networks  </br>
                        <img src='<?php echo ROOT_FOLDER ?>/images/wddn.png'/>
					</div>

                    <div style="text-align:center;margin: 5px 5px;width:47%;float:left;position: relative;background-color: #6FAFD8;color: #FFF;padding: 10px;font-size: 14px;border-radius:4px !important;cursor: pointer;" id="view_all">
                        View All Networks  </br>
                        <img src='<?php echo ROOT_FOLDER ?>/images/wddn.png'/>
                    </div>
                    
					<span id="net_payout_loaded" style="display:none;"><?php echo $net_payout_loaded; ?></span>
                    <?php } if(count($scheduled_data) > 0) {?>
					<table cellpadding="0" cellspacing="0" width="100%" style="font-weight:bold;display:none">
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
							<td id="ro_amount1"><?php echo round($ro_amount_detail[0]['ro_amount'],2) ?> </td>
							<td id="agency_amount1"><?php echo round($ro_amount_detail[0]['agency_commission_amount'],2) ?> </td>
							<td><?php echo round($ro_amount_detail[0]['ro_amount'],2)- round($ro_amount_detail[0]['agency_commission_amount'],2) ?></td>
							<?php if($ro_amount_detail[0]['ro_valid_field'] == 1) { ?>
							<td id="agency_rebate1" style="color:red;"><?php echo "Missing"; ?></td>
                                                        <td id="other_expenses1" style="color:red;"><?php echo "Missing";  ?>
														<?php if($logged_in_user['profile_id'] == 1) { ?>
														<a href="javascript:add_other_expenses('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') ?>','<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">View/Modify</a> <?php } ?> </td>
                                                        <?php } else { ?>
							<td id="agency_rebate1" style="color:green;"><?php 
							if($ro_amount_detail[0]['agency_rebate_on'] != 'net_amount'){
								$agency_rebate = round($ro_amount_detail[0]['ro_amount'],2)*round($ro_amount_detail[0]['agency_rebate'],2)/100;
							}
							else{
								$agency_rebate = round(($ro_amount_detail[0]['ro_amount'] - $ro_amount_detail[0]['agency_commission_amount']),2)*round($ro_amount_detail[0]['agency_rebate'],2)/100;
							}
							echo $agency_rebate; ?></td>
							<td id="other_expenses1" style="color:green;"><?php $other_expenses = $ro_amount_detail[0]['marketing_promotion_amount']+$ro_amount_detail[0]['field_activation_amount']+$ro_amount_detail[0]['sales_commissions_amount']+$ro_amount_detail[0]['creative_services_amount']+$ro_amount_detail[0]['other_expenses_amount']; echo $other_expenses;?><?php if($logged_in_user['profile_id'] == 1) { ?><a href="javascript:add_other_expenses('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') ?>','<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">View/Modify</a><?php } ?></td>
								<?php } ?>
							<td id="net_amount1"><?php $actual_net_amount =  round($ro_amount_detail[0]['ro_amount'],2)- round($ro_amount_detail[0]['agency_commission_amount'],2)-$agency_rebate-$other_expenses;; echo $actual_net_amount; ?></td>
							<td id="net_revenue1"><?php $net_revenue = round($ro_amount_detail[0]['ro_amount'],2)- round($ro_amount_detail[0]['agency_commission_amount'],2) ; $net_revenue = round(($net_revenue*SERVICE_TAX),2) ; echo $net_revenue; ?></td>
							<td id="total_network_payout1"><?php /*$net_payout = round($final_amount);*/echo $total_nw_payout; ?></td><input type="hidden" id="hid_total_network_payout1" name="hid_total_network_payout1" value="<?php echo $total_nw_payout ?>" />
							<td id="surewaves_share1"><?php $surewaves_share = $actual_net_amount - $total_nw_payout; echo $surewaves_share; ?></td><input type="hidden" id="hid_surewaves_share1" name="hid_surewaves_share1" value="<?php $surewaves_share = $actual_net_amount - $total_nw_payout; echo $surewaves_share; ?>" />
							<td id="surewaves_share_per1"><?php echo round(($surewaves_share/$net_revenue*100),2) ?></td><input type="hidden" id="hid_surewaves_share_per1" name="hid_surewaves_share_per1" value="<?php echo round(($surewaves_share/$net_revenue*100),2) ?>" />
						</tr>
						<?php } else { ?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td>In Process</td>
							<td>In Process</td>
							<td>In Process</td>
							<td>In Process</td>
							<td><?php $other_expenses = $ro_amount_detail[0]['marketing_promotion_amount']+$ro_amount_detail[0]['field_activation_amount']+$ro_amount_detail[0]['sales_commissions_amount']+$ro_amount_detail[0]['creative_services_amount']+$ro_amount_detail[0]['other_expenses_amount']; echo $other_expenses; ?>  <?php if($logged_in_user['profile_id'] == 1) { ?> <a href="javascript:add_other_expenses('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') ?>','<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">View/Modify</a><?php } ?></td>
							<td>In Process</td>
							<td id="net_revenue1">In Process</td>
							<td id="total_network_payout1"><?php /*$net_payout = round($final_amount);*/echo $total_nw_payout; ?></td>
							<td>In Process</td>
                                                        <td>In Process</td>
						</tr>
						<?php } ?>
                                        <?php ?>
					
						
						
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
							<?php 
							if($reset_cache_button == 'yes'){ ?>
									<td><input type="button" id="revert" class="submitlong" name="apa" value="Revert Changes" onclick="javascript:revert_changes_for_ro('<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')"/></td>
							<? }else{ ?>
									<td>&nbsp;</td>
							<?}
							if($ro_approval[0]['ro_approval_request_status']==1 ||  $ro_amount_detail[0]['ro_approval_status'] == 1) { 
								// added by lokanath to show approval button only when requested
								if($ro_status_entry == 'Approval Requested' && $is_cancel_market_requested == 0){ ?>
									  <td><input type="submit" id="submit_approve2" class="submitlong" name="apa" value="Approve" onclick="javascript:show_processing()" /><span id="submit_processing2" style="display:none;color:#008000">Processing..</span></td> <?php
								} else if($is_cancel_market_requested == 1){?>
                                      <td><span style='color:red;font-weight: normal;'>Can't Approve RO! Market Cancellation Request is Pending !! </span></td>
                                <?php }
							} else {?>
                                <td><span style='color:red;font-weight: normal;'>Can't Approve RO ! Waiting for Approval Request </span></td>
                            <?php }?>
						</tr>

					</table>

				<div class="paggination right">
					<?php echo $page_links ?>
				</div>		<!-- .paggination ends -->		
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->	
			<?php 
					?>
					
                    <?php }  ?>
<input type="hidden" id="service_tax_value" value="<?php echo SERVICE_TAX ?>"/>
<a href="#" style="color:#099;" class="go-to-top">Back to Top</a>
<div id="overlay" style="display: none;"></div>
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
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/nw_ro_payment/'+order_id+ "/" + edit + "/" + id,iframe:true, width: '900px', height:'900px'});
	//window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/nw_ro_payment/" + order_id;
}
function mis_report(order_id,edit,id) {
        window.location.href = "<?php echo ROOT_FOLDER ?>/report/show_mis_report_description/" + order_id + "/" + edit + "/" + id;
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
function revert_changes_for_ro(order_id,edit,id){
	if(confirm("Revert All Changes")){
		window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/revert_all_changes/" +order_id+ "/" + edit + "/" + id;
	}
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
		if($('#'+channel_avg_rate_id).attr('readonly')== true){
			//alert(1)
			continue;
		}
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
			if($('#'+amount_id).attr('readonly')== true){
        	                continue;
	                }

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
			 if($('#'+channel_avg_rate_id).attr('readonly')== true){
                                continue;
                        }
//                      alert(channel_avg_rate_id);
                        var channel_avg_rate = amount/total_ad_seconds * 10;
			document.getElementById(channel_avg_rate_id).value = channel_avg_rate.toFixed(2);
			total_amount += parseFloat(amount,10);
			} }  else { 
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
			if($('#'+amount_id).attr('readonly')== true){
                                continue;
                        }
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
	
	/*var total_payout = 0;
	for(var i = 0;i<total_networks; i++) 
	{
		var final_amount_id = 'network_'+i+'_final_amount';
	        var final_amount = document.getElementById(final_amount_id).value;
		total_payout += parseFloat(final_amount,10);
	}*/
	
	var total_networks = $('#recs').children().length/2;
	var total_payout = 0;
	for(var i = 0;i<total_networks; i++) 
	{
		var final_amount_id = 'network_'+i+'_final_amount';
	        var final_amount = document.getElementById(final_amount_id).value;
		total_payout += parseFloat(final_amount,10);
	}
	net_payout_calculated = total_payout.toFixed(2);
	net_payout_loaded = document.getElementById('net_payout_loaded').innerHTML;
	var diff_net_payout = net_payout_calculated - net_payout_loaded;
	net_payout = <?php echo $total_nw_payout; ?> + diff_net_payout;
	net_payout = net_payout.toFixed(2);
	
	document.getElementById('total_network_payout').innerHTML =  net_payout;
	document.getElementById('total_network_payout1').innerHTML =  net_payout;
	
	document.getElementById('hid_total_network_payout').value =  net_payout;
	document.getElementById('hid_total_network_payout1').value =  net_payout;
	
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
	
	document.getElementById('hid_surewaves_share').value = surewaves_share.toFixed(2);
	document.getElementById('hid_surewaves_share_per').value = surewaves_share_per.toFixed(2);
	document.getElementById('hid_surewaves_share1').value = surewaves_share1.toFixed(2);
    document.getElementById('hid_surewaves_share_per1').value = surewaves_share_per1.toFixed(2);
}
function show_channels(order_id) { 

	$.colorbox({href:"<?php echo ROOT_FOLDER ?>/ro_manager/show_channels/" + order_id,iframe:true,width:'515px',height:'374px'});
}
function change_ro_dates(ro_id){
    $.colorbox({href:"<?php echo ROOT_FOLDER ?>/account_manager/change_ro_dates/" + ro_id,iframe:true,width:'515px',height:'374px'});
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
		$(this).scrollTop(0);
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
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>-->
<script type="text/javascript">
    $(function() {
        $( "#accordion" ).accordion({
            collapsible: true,
            active: false
        });
    });


    $('#view_more').click(function(){
        var div = $('#recs');
        //var start= div.children('table').length ;
        var start= div.children().length/2 ;
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
                success:function(data_with_net_payout_loaded){//alert(data)
                    if(data_with_net_payout_loaded == '~'){
                        end_of_nw = 1;
                        $('#view_more').text('No more Networks');
                        $('#view_all').text('No more Networks');
                    }
                    data_with_net_payout_loaded = data_with_net_payout_loaded.split('~');
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
                    }

                },
                complete:function(){
                    $("#nw_load_status").val(start);

                    var div = $('#recs');
                    //Calling again to load all networks
                    if($("#load_all_nw").val() == 1 && end_of_nw != 1){
                        $("#view_more").trigger("click");
                    }

                }
            });
        }
    });

    $("#view_all").click(function(){
        var div = $('#recs');
        //var start= div.children('table').length ;
        var start= div.children().length/2 ;
        var nw_loaded = $("#nw_load_status").val();

        $("#load_all_nw").val(1);
        $("#view_more").trigger("click");
    });


	$(window).scroll(function(){			
			$el = $('#fixedSummary'); 
			if ($(this).scrollTop() > 200 && $el.css('position') != 'fixed'){ 
				$('#fixedSummary').css({'position': 'fixed', 'top': '0px','background-color':'#FFFFFF','margin-left': '-20px', 'width': '1212px'}); 
			}
			if ($(this).scrollTop() < 200 && $el.css('position') == 'fixed')
			{
				$('#fixedSummary').css({'position': 'static', 'top': '0px','background-color':'#FFFFFF','margin-left':'0px','width': '1170px'}); 
			}

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
<script type="text/javascript">
function show_processing(){
    $("#submit_approve1").hide();
    $("#submit_approve2").hide();
    $("#submit_processing1").show();
    $("#submit_processing2").show();
}
function change_channel_data(key,row_id,channel_id){
    console.log("onchange");
	var channel_spot_amount_id = $('input[name="post_channel_amount['+channel_id+'][0]"]').attr("value");
	var channel_banner_amount_id = $('input[name="post_channel_amount['+channel_id+'][1]"]').attr("value");
	var channel_spot_amount = 0;
	var channel_banner_amount = 0;
        
        var channel_spot_schedule_second = parseFloat($('#network_'+key+'_total_sec_'+row_id+'_0_hidden_schedule_spot').val());
        var channel_spot_toplay_second = parseFloat($('#network_'+key+'_total_sec_'+row_id+'_0_hidden_play_spot').val());
        
		var next_row_id = row_id ;
        if(!isNaN(channel_spot_schedule_second)) {
            next_row_id = row_id+1 ;
        }
	var channel_banner_schedule_second = parseFloat($('#network_'+key+'_total_sec_'+next_row_id+'_0_hidden_schedule_banner').val());
        var channel_banner_toplay_second = parseFloat($('#network_'+key+'_total_sec_'+next_row_id+'_0_hidden_play_banner').val());
        
        var channel_approved = parseInt($('#'+channel_id+'_approved').val()) ;        
        
        if(isNaN(channel_spot_schedule_second)) {
            channel_spot_schedule_second = 0 ;
        }
        if(isNaN(channel_spot_toplay_second)) {
            channel_spot_toplay_second = 0 ;
        }
        if(isNaN(channel_banner_schedule_second)) {
            channel_banner_schedule_second = 0 ;
        }
        if(isNaN(channel_banner_toplay_second)) {
            channel_banner_toplay_second = 0 ;
        }
        
        var channel_spot_rate =  $('input[name="post_channel_avg_rate['+channel_id+'][0]"]').attr("value");
        var channel_banner_rate = $('input[name="post_channel_avg_rate['+channel_id+'][1]"]').attr("value");
        
        if(isNaN(channel_spot_rate)) {
            channel_spot_rate = 0 ;
        }
        if(isNaN(channel_banner_rate)) {
            channel_banner_rate = 0 ;
        }
        
	var nw_ro_amount = parseFloat(document.getElementById('network_'+key+'_total_amount').innerHTML);
	var nw_share = parseFloat(document.getElementById('network_'+key+'_network_share').value);
	var nw_payout = parseFloat(document.getElementById('network_'+key+'_final_amount').value);
	if(channel_spot_amount_id){
		//channel_spot_amount = parseFloat(document.getElementById('network_'+key+'_amount_0_2').value);
		channel_spot_amount = parseFloat(channel_spot_amount_id);
	}
	if(channel_banner_amount_id){
		//channel_banner_amount = parseFloat(document.getElementById('network_'+key+'_amount_1_2').value);
		channel_banner_amount = parseFloat(channel_banner_amount_id);
	}
        effective_channel_spot_amount = 0 ;
        effective_channel_banner_amount = 0 
	// change row colour and make calculation
	if($('#channel_'+channel_id).is(":checked")){

		if($('#row_'+key+'_'+row_id+'_additional_campaign').val() == 1){
			$('#row_'+key+'_'+row_id).css('background-color','#F3F781');
			$('#row_'+key+'_'+row_id+'_td').css('background-color','#F3F781');
		}else{
			$('#row_'+key+'_'+row_id).css('background-color','#FBFBFB');
			$('#row_'+key+'_'+row_id+'_td').css('background-color','#FBFBFB');
		}
		
                
                //change values of seconds(Problem:same id they are changing)
                if(channel_approved !== 0){
                    $(".seconds_"+channel_id+"_spot").html("");
                    $(".seconds_"+channel_id+"_banner").html("");
                    $(".seconds_"+channel_id+"_spot").html(channel_spot_schedule_second) ;
                    $(".seconds_"+channel_id+"_banner").html(channel_banner_schedule_second) ;

                    //Effective value
                    channel_spot_amount = channel_spot_schedule_second*channel_spot_rate/10 ;
                    channel_spot_amount = parseFloat(channel_spot_amount.toFixed(2));
                    $('input[name="post_channel_amount['+channel_id+'][0]"]').val(channel_spot_amount) ;

                    channel_banner_amount = channel_banner_schedule_second*channel_banner_rate/10 ;
                    channel_banner_amount = parseFloat(channel_banner_amount.toFixed(2));
                    $('input[name="post_channel_amount['+channel_id+'][1]"]').val(channel_banner_amount) ;

                    //Effective spot and banner calculation
                    effective_channel_spot_sec = channel_spot_schedule_second - channel_spot_toplay_second ;
                    effective_channel_spot_amount = effective_channel_spot_sec*channel_spot_rate/10 ;
                    effective_channel_spot_amount = parseFloat(effective_channel_spot_amount.toFixed(2));

                    effective_channel_banner_sec = channel_banner_schedule_second - channel_banner_toplay_second ;
                    effective_channel_banner_amount = effective_channel_banner_sec*channel_banner_rate/10 ;
                    effective_channel_banner_amount = parseFloat(effective_channel_banner_amount.toFixed(2));
                }else{
                    effective_channel_spot_amount = channel_spot_amount ;
                    effective_channel_banner_amount = channel_banner_amount ;
                }
		 

		new_nw_ro_amount = nw_ro_amount + (effective_channel_spot_amount + effective_channel_banner_amount);
		document.getElementById('network_'+key+'_total_amount').innerHTML = "";
		document.getElementById('network_'+key+'_total_amount').innerHTML = new_nw_ro_amount;
		addition_amount = new_nw_ro_amount*(nw_share/100) - nw_payout;
		addition_amount = parseFloat(addition_amount.toFixed(2));
		var final_am_cal = new_nw_ro_amount*(nw_share/100);
		document.getElementById('network_'+key+'_final_amount').value = final_am_cal.toFixed(2);
		new_total_nw_payout = parseFloat(document.getElementById('total_network_payout').innerHTML) + addition_amount;
		document.getElementById('total_network_payout').innerHTML = new_total_nw_payout.toFixed(2);
		document.getElementById('total_network_payout1').innerHTML = new_total_nw_payout.toFixed(2);
		
		document.getElementById('hid_total_network_payout').value = new_total_nw_payout.toFixed(2);
		document.getElementById('hid_total_network_payout1').value = new_total_nw_payout.toFixed(2);
		
		surewaves_share = (parseFloat(document.getElementById('net_amount').innerHTML) - new_total_nw_payout).toFixed(2);
		document.getElementById('surewaves_share').innerHTML = surewaves_share;
		document.getElementById('surewaves_share1').innerHTML = surewaves_share;
		document.getElementById('surewaves_share_per').innerHTML = (surewaves_share/parseFloat(document.getElementById('net_revenue').innerHTML) * 100).toFixed(2);
		document.getElementById('surewaves_share_per1').innerHTML = (surewaves_share/parseFloat(document.getElementById('net_revenue').innerHTML) * 100).toFixed(2);
		
		document.getElementById('hid_surewaves_share').value = surewaves_share;
		document.getElementById('hid_surewaves_share1').value = surewaves_share;
		document.getElementById('hid_surewaves_share_per').value = (surewaves_share/parseFloat(document.getElementById('net_revenue').innerHTML) * 100).toFixed(2);
		document.getElementById('hid_surewaves_share_per1').value = (surewaves_share/parseFloat(document.getElementById('net_revenue').innerHTML) * 100).toFixed(2);
		
		// make text boxes readonly
		if(channel_spot_amount_id){
			$('input[name="post_channel_avg_rate['+channel_id+'][0]"]').attr("readonly",false);
			$('input[name="post_channel_amount['+channel_id+'][0]"]').attr("readonly",false);
		}
		if(channel_banner_amount_id){
			$('input[name="post_channel_avg_rate['+channel_id+'][1]"]').attr("readonly",false);
			$('input[name="post_channel_amount['+channel_id+'][1]"]').attr("readonly",false);
		}
		$('#network_'+key+'_network_share').attr("readonly",false);
		$('#network_'+key+'_final_amount').attr("readonly",false);
		// end
		
		$('#hid_cancel_channel_'+channel_id).val("");
		$('#channel_'+channel_id).removeClass('cancel_channel_event');
		var hid_order_id = $('#hid_order_id').val();
		$.ajax({
		  type: "POST",
		  data: 'channel_id='+channel_id+'&order_id='+hid_order_id,
		  url: '<?php echo ROOT_FOLDER ?>/ro_manager/remove_from_cancel_channel'
		  
		});
		
	}
	else{
		$('#row_'+key+'_'+row_id).css('background-color','#ff9999');
		$('#row_'+key+'_'+row_id+'_td').css('background-color','#ff9999');
                
                if(channel_approved !== 0){
                    //change values of seconds(Problem:same id they are changing)
                    $(".seconds_"+channel_id+"_spot").html(channel_spot_toplay_second) ;
                    $(".seconds_"+channel_id+"_banner").html(channel_banner_toplay_second) ;

                    //Effective value
                    channel_spot_amount = channel_spot_toplay_second*channel_spot_rate/10 ;
                    channel_spot_amount = parseFloat(channel_spot_amount.toFixed(2));
                    $('input[name="post_channel_amount['+channel_id+'][0]"]').val(channel_spot_amount) ;

                    channel_banner_amount = channel_banner_toplay_second*channel_banner_rate/10 ;
                    channel_banner_amount = parseFloat(channel_banner_amount.toFixed(2));
                    $('input[name="post_channel_amount['+channel_id+'][1]"]').val(channel_banner_amount)  ;

                    //Effective spot and banner calculation
                    effective_channel_spot_sec = channel_spot_schedule_second - channel_spot_toplay_second ;
                    effective_channel_spot_amount = effective_channel_spot_sec*channel_spot_rate/10 ;
                    effective_channel_spot_amount = parseFloat(effective_channel_spot_amount.toFixed(2));

                    effective_channel_banner_sec = channel_banner_schedule_second - channel_banner_toplay_second ;
                    effective_channel_banner_amount = effective_channel_banner_sec*channel_banner_rate/10 ;
                    effective_channel_banner_amount = parseFloat(effective_channel_banner_amount.toFixed(2));
                }else{
                    effective_channel_spot_amount = channel_spot_amount ;
                    effective_channel_banner_amount = channel_banner_amount ;
                }    
                
                
		new_nw_ro_amount = nw_ro_amount - (effective_channel_spot_amount + effective_channel_banner_amount);
		document.getElementById('network_'+key+'_total_amount').innerHTML = "";
		document.getElementById('network_'+key+'_total_amount').innerHTML = new_nw_ro_amount;
		removal_amount = nw_payout - new_nw_ro_amount*(nw_share/100);
		removal_amount = removal_amount.toFixed(2);
		var final_am_cal = new_nw_ro_amount*(nw_share/100);
		document.getElementById('network_'+key+'_final_amount').value = final_am_cal.toFixed(2);
		new_total_nw_payout = parseFloat(document.getElementById('total_network_payout').innerHTML) - removal_amount;
		document.getElementById('total_network_payout').innerHTML = new_total_nw_payout.toFixed(2);
		document.getElementById('total_network_payout1').innerHTML = new_total_nw_payout.toFixed(2);
		

		document.getElementById('hid_total_network_payout').value = new_total_nw_payout.toFixed(2);
		document.getElementById('hid_total_network_payout1').value = new_total_nw_payout.toFixed(2);
		
		surewaves_share = (parseFloat(document.getElementById('net_amount').innerHTML) - new_total_nw_payout).toFixed(2);
		document.getElementById('surewaves_share').innerHTML = surewaves_share;
		document.getElementById('surewaves_share1').innerHTML = surewaves_share;
		document.getElementById('surewaves_share_per').innerHTML = (surewaves_share/parseFloat(document.getElementById('net_revenue').innerHTML) * 100).toFixed(2);
		document.getElementById('surewaves_share_per1').innerHTML = (surewaves_share/parseFloat(document.getElementById('net_revenue').innerHTML) * 100).toFixed(2);
		
		document.getElementById('hid_surewaves_share').value = surewaves_share;
		document.getElementById('hid_surewaves_share1').value = surewaves_share;
		document.getElementById('hid_surewaves_share_per').value = (surewaves_share/parseFloat(document.getElementById('net_revenue').innerHTML) * 100).toFixed(2);
		document.getElementById('hid_surewaves_share_per1').value = (surewaves_share/parseFloat(document.getElementById('net_revenue').innerHTML) * 100).toFixed(2);
		
		// make text boxes readonly
		if(channel_spot_amount_id){
			$('input[name="post_channel_avg_rate['+channel_id+'][0]"]').attr("readonly",true);
			$('input[name="post_channel_amount['+channel_id+'][0]"]').attr("readonly",true);
		}
		if(channel_banner_amount_id){
			$('input[name="post_channel_avg_rate['+channel_id+'][1]"]').attr("readonly",true);
			$('input[name="post_channel_amount['+channel_id+'][1]"]').attr("readonly",true);
		}
		$('#network_'+key+'_network_share').attr("readonly",true);
		$('#network_'+key+'_final_amount').attr("readonly",true);
		// end
		$('#hid_cancel_channel_'+channel_id).val(channel_id);

		var hid_order_id = $('#hid_order_id').val();
		$.ajax({
		  type: "POST",
		  data: 'channel_id='+channel_id+'&order_id='+hid_order_id,
		  url: '<?php echo ROOT_FOLDER ?>/ro_manager/add_to_cancel_channel'
		  
		});
		$('#channel_'+channel_id).addClass('cancel_channel_event');
	}
	// end
	
}
function modify_network(nw_id){
	$('.hide_'+nw_id).hide();
	$('.show_'+nw_id).show();
	$('.show_checkbox_'+nw_id).attr('disabled',false);
	
}
function save_to_cache(nw_id){
	//$('.show_'+nw_id).hide();
	//$('.hide_'+nw_id).show();
	//$('.show_checkbox_'+nw_id).attr('disabled',true);.myClass[type=checkbox]
	$('.show_'+nw_id+' input[type=text]').css('background-color','#31B404');
	$('#final_save1').show();
	$('#final_save').hide();
	if ($('.changed_network').length > 0) {
		$('.changed_network_'+nw_id).val(1);
	}
	
	$('.show_checkbox_'+nw_id).each(function(){
		if($(this).hasClass( "cancel_channel_event" )){
			$(this).removeClass( "cancel_channel_event" );
		}
	});
	
	
	
	//form_name = 'form_'+nw_id;
	//document.form_name.submit();
}
function cancel_channels_by_priority(market_id,priority,am_ro_id,order_id){

    if($('.cancel_priorities_'+market_id).not(':checked').length == $('.cancel_priorities_'+market_id).length){
        $('#'+market_id+"_"+priority).attr("checked",true);
        alert("Please use Cancel Market from AM Login");
        return false;
    }

    if($('.cancel_priorities_all').not(':checked').length == $('.cancel_priorities_all').length){
        $('#'+market_id+"_"+priority).attr("checked",true);
        alert("Please use Cancel RO from AM Login");
        return false;
    }

    // $('#overlay').show();
    // $('#cancel_loader').show();

    $.ajax({
        type: "POST",
        data: 'market_id='+market_id+'&priority='+priority+'&am_ro_id='+am_ro_id,
        url: '<?php echo ROOT_FOLDER ?>/ro_manager/get_channels_for_market',
        success:function(data){

            var channels = JSON.parse(data);

            //for (var key in channels) {
            //    var channel_id = channels[key];
            //    if($('#'+market_id+'_'+priority).is(":checked")){
            //        $.ajax({
            //            type: "POST",
            //            data: 'channel_id='+channel_id+'&order_id='+order_id,
            //            url: '<?php //echo ROOT_FOLDER ?>///ro_manager/remove_from_cancel_channel'
            //
            //        });
            //    }else{
            //        $.ajax({
            //            type: "POST",
            //            data: 'channel_id='+channel_id+'&order_id='+order_id,
            //            url: '<?php //echo ROOT_FOLDER ?>///ro_manager/add_to_cancel_channel'
            //
            //        });
            //    }
            //}
            if($('#'+market_id+'_'+priority).is(":checked")) {
                for (var key in channels) {
                    var channel_id = channels[key];
                    $('#channel_'+channel_id).attr('checked',true);
                    $('#channel_'+channel_id).trigger('change');
                }
            } else {
                for (var key in channels) {
                    var channel_id = channels[key];
                    $('#channel_'+channel_id).attr('checked',false);
                    $('#channel_'+channel_id).trigger('change');
                }
            }
        }
    });

    //sending priority cancel info
    if($('#'+market_id+'_'+priority).is(":checked")){
        $.ajax({
            type: "POST",
            data: 'market_id='+market_id+'&priority='+priority+'&order_id='+order_id,
            url: '<?php echo ROOT_FOLDER ?>/ro_manager/remove_from_cancel_priority'
        });
    }else{
        $.ajax({
            type: "POST",
            data: 'market_id='+market_id+'&priority='+priority+'&order_id='+order_id,
            url: '<?php echo ROOT_FOLDER ?>/ro_manager/add_to_cancel_priority'
        });
    }


     // $('body').load(window.location.pathname);
}
function validateAllNetworks(){
		var num_changed_network = $('.changed_network').length;
		var setChangedNetwork = 0;
		$('.changed_network').each(function(){
			if($(this).val() == 1){
				setChangedNetwork++;
			}

		});
		if(setChangedNetwork != num_changed_network && num_changed_network != 0){
			alert("Looks like some extra spots has been scheduled in some channels but those channel has not been saved.Kindly first click save button available besides network name of respective channels.");
			return false;
		}
		if($('.cancel_channel_event').length >= 1){
			alert("Looks like some channel have been marked as cancel but save button not clicked.Kindly first click save button available besides network name of respective channels.");
			return false;
		}
		alert("Your changes for respective network and its channel has been captured successfully.Now you will be directed to network ro summary page.Kindly wait for this tranistion which might take a while.");
}
</script>
