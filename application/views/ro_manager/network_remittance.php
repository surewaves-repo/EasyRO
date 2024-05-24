<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->
	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
				
				<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				
				<ul id="nav">
					<?php if($logged_in_user['profile_id'] != 6) { ?>
					<?php if($logged_in_user['profile_id'] != 7) { ?>
					<li><a href="<?php echo ROOT_FOLDER ?>/account_manager/home">Home</a></li>
					<?php } ?>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/approved_ros">Approved RO's</a></li>
					
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user_details">User Details</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_ro_report">Reports</a>
						<ul id="nav">
							<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/external_ro_report">External RO Report</a></li>
							<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_ro_report">Network RO Report</a></li>
                            <li><a href="<?php echo ROOT_FOLDER ?>/account_manager/am_invoice_report">Collection Report</a></li>
							<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_remittance_report">N/w Remittance</a></li>
							<?php if($logged_in_user['profile_id'] == 1 or $logged_in_user['profile_id'] == 2 or $logged_in_user['profile_id'] == 7) { ?>
							<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/update_network_payment">Update Network Payment</a></li>
							<?php } ?>
						</ul>
					</li>
					<?php } else{ ?>
                    <li class="active" ><a href="<?php echo ROOT_FOLDER ?>/account_manager/home">Home</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/approved_ros">Approved RO's</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user_details">User Details</a></li>					
					<?php } ?>
				</ul>
				
				<p class="user">Hello, <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
			
			<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Network Remittance Generation</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">	
					<form action="" method="post">				
					<p><label>Select Network:</label>
						<select class="styled" name="network_id" id="network_id">
							<option value="-">-</option>
							<?php foreach($customer_detail as $c_value) { ?>
							<option value="<?php echo $c_value['customer_id'] ?> " <?php if(isset($cid) && $c_value['customer_id'] == $cid) echo 'selected' ;?> ><?php echo $c_value['customer_name'] ?></option>
							<?php } ?>
						</select>
					</p>			
					</form>
				
				
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/generate_network_remittance/<?php echo $cid;?>" method="post">
					
						<table cellpadding="0" cellspacing="0" width="100%">
						
							<tr>
								<td width="10"><input type="checkbox" class="check_all" /></td>
								<th>Network RO Number</th>
								<th>Internal RO Number</th>
								<th>Client Name</th>
								<th>Agency Name</th>
								<th>Activity Start Date</th>
								<th>Activity End Date</th>
								<th>Activity Scheduled spots</th>
								<th>Activity Run Spots</th>
								<th>Client Payment Received( Yes/NO/Part)</th>
								<th>RO Amount</th>
							</tr>
							
							<?php if(isset($ro_details)) { foreach($ro_details as $detail) { ?>
							<tr>
								<?php if($detail['client_payment_received'] == 'Yes') { ?>
								<td><input type="checkbox" name="chk_ro[]" value="<?php echo $detail['customer_ro'] ;?>" /></td>
								<?php }else { ?>
								<td>&nbsp;</td>
								<?php } ?>
								<td><a href="#"><?php echo $detail['customer_ro'] ;?></a></td>
								<td><?php echo $detail['internal_ro'] ;?></td>
								<td><?php echo $detail['client'] ;?></td>
								<td><?php echo $detail['agency'] ;?></td>
								<td><?php echo $detail['camp_start_date'] ;?></td>
								<td><?php echo $detail['camp_end_date'] ;?></td>
								<td>scheduled_seconds_1</td>
								<td>run_seconds_1</td>
								<td><?php echo $detail['client_payment_received'] ;?></td>
								<td><?php echo $detail['amount_collected'] ;?></td>
							</tr>
							<?php } } ?>
							</table>
							
							<!--<div class="tableactions">
								<select name="get_action" id="get_action">
									<option>Issue Remittance Advice</option>
									<!-- <option>Delete</option>
									<option>Edit</option> -->
								<!--</select>
								
								<input type="submit" class="submit tiny" value="Apply to selected" />
							</div>		 .tableactions ends -->
							
							<!-- <div class="paggination right">
								<a href="#">&laquo;</a>
								<a href="#" class="active">1</a>
								<a href="#">2</a>
								<a href="#">3</a>
								<a href="#">4</a>
								<a href="#">5</a>
								<a href="#">6</a>
								<a href="#">&raquo;</a>
							</div>		 .paggination ends -->
							<?php if(isset($cid)){ ?>
							<input type="submit" class="submit" value="Issue" />
							<?php } ?>
							</form>
						
				</div><!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->

		</div>  <!--wrapper ends-->
</div> 	<!--hld ends -->

<script language="javascript">
	$("#network_id").change(function(){
		//alert($("#network_id").val());
		var network_id = $("#network_id").val() ;
		window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/get_network_remittance/" + network_id ;
    });
	/*function submit_form(){
		counter = 1
		$('input[type=checkbox]').each(function () {
			if(counter != 1){
				//(this.checked ? alert(this.value) : alert(0));
			}
			counter++;
		});
	}*/
</script>
