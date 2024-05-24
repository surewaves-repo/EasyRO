<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?> 
	<script type="text/javascript" src="/surewaves_easy_ro/js/datetimepicker.js"></script>
	
			
			<div class="block">		
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Network Wise Schedule Summary</h2>					
				</div>	
				
				<div class="block_content">
				
				
					<table  cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<th>Internal RO</th>
								<th>Network Name</th>
								<th>Network ID</th>
								<th>Total Impressions</th>
								<th> Ad Seconds </th>
								<th> Amount </th>
							</tr>
							<?php foreach($network_details as $network=>$data) { ?>
							<tr>
								<td><?php echo $data['internal_ro_number'] ?></td>
								<td><?php echo $data['customer_name'] ?></td>
								<td><?php echo $data['customer_id'] ?></td>
								<td><?php echo $data['total_impressions'] ?></td>
								<td><?php echo $data['total_ad_seconds'] ?></td>
								<td><?php echo $data['network_amount'] ?></td>
							</tr>
				<?php } ?>
				</table>
				
					<table  cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<th>Channel Name</th>
								<th>Client Name</th>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Total Impressions</th>
								<th>Ad Duration</th>
								<th> Ad Seconds </th>
							</tr>
					<?php foreach($network_channels_summary as $channels_summary=>$data) { ?>
							<tr>
								<td><?php echo $data['channel_name'] ?></td>
								<td><?php echo $data['client_name'] ?></td>
								<td><?php echo $data['start_date'] ?></td>
								<td><?php echo $data['end_date'] ?></td>
								<td><?php echo $data['timp'] ?></td>
								<tD><?php echo $data['duration_to_play'] ?></td>
								<td><?php echo $data['timp']*$data['duration_to_play'] ?></td>
							</tr>
				<?php } ?>
				</table>
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_approve_network" method="post">
						<p>
							<input type="submit" class="submit" value="Approve" />					
						</p>
					</form>
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .block ends -->
			
		
