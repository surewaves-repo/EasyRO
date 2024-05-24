<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/flexigrid.pack.css" />
<script type="text/javascript" src="/surewaves_easy_ro/js/flexigrid.js"></script>
<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
				
				<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				
				<ul id="nav">
					<li class="active" ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/home">Home</a></li>
					<li class="active" ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user_details">User Details</a></li>
					<!--<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/ro_report">RO Report</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/audit">Audit Trail</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user">User</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/preferences">Preferences	</a></li>-->
					
				</ul>
				
				<p class="user">Hello, <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
						<div class="block">
			
				<form method="post"  action="<?php echo ROOT_FOLDER ?>/ro_manager/search_content">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Customer Release Order</h2>
					
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
								<th>Customer RO Number</th>
								<th>Agency Name</th>
								<th>Advertiser name</th>
								<th>Brand Name</th>
								<th>RO Start Date</th>
								<th>RO End Date</th>
								<th>RO Status</th>
								<?php if($profile_id == 1 or $profile_id == 2 or $profile_id == 3) { 
									if(isset($ro_details[0]['ro_amount'])) { ?>
										<th>CRO Amount</th>
									<?php } else { ?>
									<th>&nbsp;</th>
								<?php } }  ?>
									
							</tr>			
							
								<tr>
									<td> <?php echo $content['customer_ro_number']?><br/> <?php echo '(Internal RO Number:'.$content['internal_ro_number'].")" ; ?> </td>
									<td><?php echo $content['agency_name'] ?> &nbsp;</td>
									<td><?php echo $content['client_name'] ?></td>
									<td><?php echo $content['brand_new'] ?></td>
									<td><?php print date('d-M-Y', strtotime($content['start_date'])); ?> </td>
									<td><?php print date('d-M-Y', strtotime($content['end_date'])); ?> </td>
									<?php if(!isset($ro_details[0]['ro_approval_status'])){ ?>
										<td>In Process</td>
									<?php } elseif($ro_details[0]['ro_approval_status']==1) { ?>
										<td>Approved</td>
									<?php }  elseif($ro_details[0]['ro_approval_status']==0) { ?>
										<td>In Process</td>
									<?php } ?>
									<?php if($profile_id == 1 or $profile_id == 2 or $profile_id == 3) { 
										if(isset($ro_details[0]['ro_amount'])) { ?>
						<td id="ro_amount"><?php echo round($ro_details[0]['ro_amount'],2) ?> </td>
						<?php } else { ?>
						<td><a href="javascript:add_ro_amount('<?php echo  rtrim(base64_encode($content['customer_ro_number']),'=') ?>')">Add CRO Amount</a></td>
						<?php } ?>
						</tr><tr>
									<!--<td><a href="javascript:add_price_approve('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Indidual Approval</a></td>-->
									<td><a href="javascript:show_channels('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Show Channels</a></td>
									<td><a href="javascript:campaigns_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Campaigns Schedule</a></td>
									<td><a href="javascript:channels_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Channels Schedule</a></td>
						<?php } else { ?>
									<td><a href="javascript:show_channels('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Show Channels</a></td>
									<td><a href="javascript:campaigns_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Campaigns Schedule</a></td>
                                                                        <td><a href="javascript:channels_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Channels Schedule</a></td> <?php } ?>
<!--									<td><a href="javascript:ro_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">RO Schedule</a></td>-->
									<?php if($profile_id == 3) { ?>
									<td><a href="javascript:approval_request('<?php echo rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Approval Request</a></td> <?php } ?>
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
				</div>
				<div class="block_content">
					<h2 style="text-align:center;">Schedule is not prepared due to lack of Inventory</h2>
				</div>		<!-- .paggination ends -->		
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>
<script language="javascript">
function campaigns_schedule(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/campaigns_schedule/" + order_id;
}
function channels_schedule(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/channels_schedule/" + order_id;
}
function show_channels(order_id) { 

	$.colorbox({href:"<?php echo ROOT_FOLDER ?>/ro_manager/show_channels/" + order_id,iframe:true,width:'515px',height:'374px'});
}
function approval_request(order_id) {

	 window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approval_request/" + order_id ;
}

function add_ro_amount(external_ro) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_ro_amount/'+external_ro,iframe:true, width: '520px', height:'310px'}); 
}
