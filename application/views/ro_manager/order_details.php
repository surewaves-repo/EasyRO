<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/flexigrid.pack.css" />
<script type="text/javascript" src="/surewaves_easy_ro/js/flexigrid.js"></script>
<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->
	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
				
				<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/DigiXpress-logo.png" style="padding-top:10px;"/></h1>
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				
				<ul id="nav">
					<li class="active" ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/home">Home</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/ro_report">RO Report</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/audit">Audit Trail</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user">User</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/preferences">Preferences	</a></li>
				</ul>
				
				<p class="user">Hello, <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
			

			
			
			
			
			<div class="block">
			
				<form method="post" action="<?php echo ROOT_FOLDER ?>/ro_manager/search_content">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>RO Order Details</h2>
					
					<ul style="float:right;padding-left:10px;">
						<li ><label> </label> &nbsp; <input type="text" id="SearchTF" class="text" placeholder="Enter text to search ..." value="<?php if ( isset($search_str) && !empty($search_str) ) { echo $search_str; } ?>" name="search_str"   /></li>
						<li ><input type="submit" class="submit" value="search"   /></li>
						
						
					<ul>
					<ul>
						
					</ul>
				</div>		<!-- .block_head ends -->
				</form>
				
				<div class="block_content">				
					
						<table cellpadding="0" cellspacing="0" width="100%">						
							<tr>
								<th>Customer RO number</th>
								<th>Agency Name</th>
								<th>Client Name</th>
								<th>Campaign Start Date</th>
								<th>Campaign End Date</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>			
							
								<tr>
									<td> <?php echo $content['customer_ro_number']?><br/> <?php echo '(Internal RO Number:'.$content['internal_ro_number'].")" ; ?> </td>
									<td><?php echo $content['agency_name'] ?></td>
									<td><?php echo $content['client_name'] ?></td>
									<td><?php print date('d-M-Y', strtotime($content['start_date'])); ?> </td>
									<td><?php print date('d-M-Y', strtotime($content['end_date'])); ?> </td>
									<td><a href="javascript:add_price_approve('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Individual Approval</a></td>
									<td><a href="javascript:approve('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Approval</a></td>
									<td><a href="javascript:campaigns_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Campaigns Schedule</a></td>
									<td><a href="javascript:channels_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Channels Schedule</a></td>
									<td><a href="javascript:ro_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">RO Schedule</a></td>
								</tr>
													
						</table><br>		
					<div class="paggination right">
							<?php echo $page_links ?>
					</div>		<!-- .paggination ends -->
						
					
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>	<!-- .block ends -->
			<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Network Wise RO Summary</h2>
				</div>
				<div class="block_content">										
						<?php 
						$network_name="";
						foreach($network_details as $evaluate) {
    $total = 0;
    foreach($evaluate as $value) {
        $total = $total + $value;
    }
} $ro_amount = $ro_details['ro_amount']?>
						<table cellpadding="0" cellspacing="0" width="50%">
							<tr><td>Total External RO Value:</td><td><?php $ro_amount; ?></td></tr>
							<tr><td>Total Network Payout:</td><td><?php if($total!=0) { echo $total; } else { echo "0"; }  ?></td></tr>
							<tr><td>SureWaves Share:</td><td><?php $ro_amount-$total ?></td></tr>
							<tr><td>SureWaves Share Per(%):</td><td><?php ($total/$ro_amount)*100 ?></td></tr>
						</table>
						<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_add_price_approve" method="post">
						<table cellpadding="0" cellspacing="0" width="100%">
							<?php
								$count = 0;
								$cust_id_prev=0;
								$cust_id_cur=0;
								$total_impressions_prev=0;
								$total_seconds_prev =0;
								$total_impressions_cur=0;
								$total_seconds_cur =0;
								$network_str_cur="";
								$network_email_cur = "";
								$network_email_prev ="";
								$network_name_cur ="";
								$network_name_prev ="";
								$network_str_prev=""; 
								foreach($channel_summary as $c) {
									$channel_str_cur="";
									$int_ro = $c['internal_ro_number'];
										if($network_name!=$c['customer_name']) { 
										
											$channel_str_cur .= $c['customer_name'].'~'.str_replace('/','surero',$c['internal_ro_number']).'~'.$c['client_name'].'~'.str_replace(' ','surero',$c['channel_name']).'~'.date('d-M-Y', strtotime( $c['start_date'])).'~'.date('d-M-Y', strtotime( $c['end_date'])).'~'.$c['timp'].'~'.$c['duration_to_play'].'~'.str_replace('@','surero',$c['customer_location']).'~'.$c['customer_id'];
											$network_name = $c['customer_name'];
											if($count == 0) { ?>
							<tr cellpadding="0" cellspacing="0" width="100%">
								<th colspan="3">Network Name</th>
								<th colspan="3">Network Contact</th>
								<th colspan="3">Total Ad Seconds</th>
								<th colspan="3">Approval Status</th>
								<th colspan="3">Amount*</th>
								<th colspan="3">Channel Share(%)</th>
							</tr>
											<?php  }if($count != 0){
											$total_impressions_prev = $total_impressions_cur;
											$total_seconds_prev = $total_seconds_cur;
											$network_str_prev = $network_str_cur;
											$cust_id_prev= $cust_id_cur;
											$network_email_prev = $network_email_cur;
											$network_name_prev = $network_name_cur;
											$network_name_cur ="";
											$network_str_cur = "";
											$network_email_cur ="";
											$total_impressions_cur = 0;
											$total_seconds_cur = 0;
											$cust_id_cur = 0;
											
							?>
							<tr cellpadding="0" cellspacing="0" width="100%">
       		                                                <td colspan="3"><?php echo $network_name_prev;  ?></td>
								<td colspan="3"><?php echo $network_email_prev; ?></td>
								<td colspan="3"><?php echo $total_seconds_prev ?></td>
								<?php if($network_approval[$int_ro][$cust_id_prev] == 0) {  ?>                                                                                       <td colspan="3">Not Approved</td>								 
						        <?php } else  { ?>
                                    <td colspan="3">Approved</td>
								<?php } ?>
								<?php if($profile_id == 1) {
								$int_ro = $c['internal_ro_number'];
								if($network_details[$int_ro][$cust_id_prev] != '') { ?>
								<td colspan="3"><?echo $network_details[$int_ro][$cust_id_prev] ?></td>
								<?php } else { ?>
								<td colspan="3"><label><?php echo form_error('amount'); ?></label><input type="text" class="textsmall" name="post_cost[<?php echo $cust_id_prev; ?>]" id="<?php echo $cust_id_prev; ?>"value="<?php echo set_value('amount'); ?>" /></td>
								<td colspan="3"><label><?php echo form_error('share'); ?></label><input type="text" class="textsmall" name="post_share[<?php echo $cust_id_prev; ?>]" id="<?php echo $cust_id_prev; ?>" value="100" /><input type="hidden" name="hid_cust_id[<?php echo $cust_id_prev; ?>]" value="<?php echo $cust_id_prev ?>">
								<input type="hidden" name="hid_cust_name[<?php echo $cust_id_prev; ?>]" value="<?php echo $network_email_prev ?>"><input type="hidden" name="hid_total_impressions[<?php echo $cust_id_prev; ?>]" value="<?php echo $total_impressions_prev ?>"><input type="hidden" name="hid_total_ad_seconds[<?php echo $cust_id_prev; ?>]" value="<?php echo $total_seconds_prev ?>"></td>
								<?php } } else { ?>
								<td colspan ="3">&nbsp;</td> 
								<?php }?>
							</tr>
							
							<?php }
							$network_str_cur .= $c['customer_name'].'~'.$c['customer_id'];
							$total_impressions_cur = $total_impressions_cur+$c['timp'];
							$total_seconds_cur = $total_seconds_cur+$c['duration_to_play']* $c['timp'];
							$cust_id_cur = $c['customer_id'];
							$cust_id = $c['customer_id'];
							$network_name_cur = $c['customer_name'];
							$network_email_cur = $c['customer_location'];		
							$count++;
							} else {
								
								$channel_str_prev = $c['customer_name'].'~'.str_replace('/','surero',$c['internal_ro_number']).'~'.$c['client_name'].'~'.str_replace(' ','surero',$c['channel_name']).'~'.date('d-M-Y', strtotime( $c['start_date'])).'~'.date('d-M-Y', strtotime( $c['end_date'])).'~'.$c['timp'].'~'.$c['duration_to_play'].'~'.str_replace('@','surero',$c['customer_location']).'~'.$c['customer_id'];
								$total_impressions_cur = $total_impressions_cur+$c['timp'];
								$total_seconds_cur = $total_seconds_cur+$c['duration_to_play']* $c['timp'];
								$cust_id_cur = $c['customer_id'];
								$network_name = $c['customer_name'];
								$count++;
								} 
						if(count($channel_summary) == $count){
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3"><?php echo $network_name_cur;  ?></td>
							<td colspan="3"><?php echo $network_email_cur; ?></td>
							 <td colspan="3"><?php echo $total_seconds_cur ?></td>
							<?php if($network_approval[$int_ro][$cust_id_cur] == 0) {  ?>
							<td colspan="3">Not Approved</td>
 							<?php } else  { ?>
							<td colspan="3">Approved</td> 
							<?php } ?>
							 <?php if($profile_id == 1) {
                                                                $int_ro = $c['internal_ro_number'];
                                                                if($network_details[$int_ro][$cust_id_cur] != '') { ?>
                                                                <td colspan="3"><?echo $network_details[$int_ro][$cust_id_cur] ?></td>
                                                                <?php } else { ?>
                                                                <td colspan="3"><label><?php echo form_error('amount'); ?></label><input type="text" class="textsmall" name="post_cost[<?php echo $cust_id_cur; ?>]" id="<?php echo $cust_id_cur; ?>"value="<?php echo set_value('amount'); ?>" /></td>
                                                                <td colspan="3"><label><?php echo form_error('share'); ?></label><input type="text" class="textsmall" name="post_share[<?php echo $cust_id_cur; ?>]" id="<?php echo $cust_id_cur; ?>" value="100" /><input type="hidden" name="hid_cust_id[<?php echo $cust_id_cur; ?>]" value="<?php echo $cust_id_cur ?>">
                                                                <input type="hidden" name="hid_cust_name[<?php echo $cust_id_cur; ?>]" value="<?php echo $network_email_cur ?>"><input type="hidden" name="hid_total_impressions[<?php echo $cust_id_cur; ?>]" value="<?php echo $total_impressions_cur ?>"><input type="hidden" name="hid_total_ad_seconds[<?php echo $cust_id_cur; ?>]" value="<?php echo $total_seconds_cur ?>"></td>
                                                                 
						</tr>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3">&nbsp;</td>
							<td colspan="3"><input type="submit" class="submitlong" name="apa" value="Add Price & Approve" /></td>
						</tr><?php } } else { }?>             
					<?php	}  } ?>
				</table></form>				
							
						<div class="paggination right">
							<?php echo $page_links ?>
						</div>		<!-- .paggination ends -->
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>
			

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
function add_price_approve(order_id){
	window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/add_price_approve/" + order_id ;
}
function approve(order_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approve/" + order_id ;
}

function downloadPDF(order_id) {
	
	window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/downloadPDF/" + order_id;
}
function campaigns_schedule(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/campaigns_schedule/" + order_id;
}
function channels_schedule(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/channels_schedule/" + order_id;
}
function ro_schedule(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/ro_schedule/" + order_id;
}
</script>
