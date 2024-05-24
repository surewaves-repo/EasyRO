<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?> 

<?php //echo $chnl_str;exit;
$chnl_array = explode('~',$network_str);
//echo $order_id;echo $chnl_array[2];
?>
	<script type="text/javascript" src="/surewaves_easy_ro/js/datetimepicker.js"></script>
	
			
			<div class="block small center login" style="margin:0px">		
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Network Wise Schedule Summary</h2>					
				</div>	
				
				<div class="block_content">
					<table border=\"1\">

							<tr><th>Internal RO</th><td><?php echo $order_id ?></td></tr>
							<tr><th>Network Name</th><td><?php echo $chnl_array[0] ?></td></tr>
							<tr><th>Network ID</th><td><?php echo $chnl_array[1] ?></td></tr>
							<tr><th>Total Impressions</th><td><?php echo $chnl_array[2] ?></td></tr>
							<tr><th> Ad Seconds </th><td><?php echo $chnl_array[3]?></td></tr>
						
					</table>
					
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_add_price" method="post">
						<p>
							<label>Amount:* <?php echo form_error('amount'); ?></label><br />
							<input type="hidden" name="hid_internal_ro" value="<?php echo $order_id; ?>">
							<input type="hidden" name="hid_network_name" value="<?php echo $chnl_array[0] ?>">
							<input type="hidden" name="hid_timp" value="<?php echo $chnl_array[2] ?>">
							<input type="hidden" name="hid_ad_seconds" value="<?php echo $chnl_array[3] ?>">
							<input type="text" class="text" name="amount" value="<?php echo set_value('amount'); ?>" />
							<label>Channel Share(100%): <?php echo form_error('share'); ?></label><br />
							<input type="text" class="text" name="share" value="100" />
							<input type="hidden" name="hid_network_id" value="<?php echo $chnl_array[1] ?>">
						</p>
						<p>
							<input type="submit" class="submit" name="ap" value="Add Price" /> 
							<input type="submit" class="submit" name="apa" value="Add Price & Approve" /> 
							
						</p>
					</form>
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			
		
