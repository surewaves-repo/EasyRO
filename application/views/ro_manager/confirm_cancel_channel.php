<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?> 
	
	
			
			<div class="block small center login" style="margin:0px">		
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Confirm Channel Cancellation</h2>					
				</div>	
				
				<div class="block_content">
				<form action="<?php echo ROOT_FOLDER ?>/ro_manager/cancel_channel_from_ro" method="post">
					<table cellpadding="0" cellspacing="0" width="100%">						
						<tr>
							<td>You have selected the following channel for cancellation: <strong><?php echo urldecode($channel_name )?></strong></td>
                        </tr>
                        <tr>
                            <td>
All Campaigns scheduled on this Channel for this RO will be cancelled
							</td>	
						</tr>
						<tr>	
							<td> <input type="submit" class="submit" value="confirm" /> <input type="button" class="submit" value="Cancel" onclick="javascript:parent.jQuery.colorbox.close();" />	</td>						
						<tr>
					
					</table>
						<input type="hidden" name="hid_channel_id" value="<?php echo $channel_id ?>" />
                        <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
                        <input type="hidden" name="hid_id" value="<?php echo $id ?>">
                        <input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro ?>">
						<!--<input type="hidden" name="hid_" value="<?php echo $post_channel_avg_rate ?>" />
						<input type="hidden" name="post_channel_amount" value="<?php echo $post_channel_amount ?>" />
						 <input type="hidden" name="hid_total_seconds" value="<?php echo $hid_total_seconds ?>" />
						<input type="hidden" name="hid_client_name" value="<?php echo $hid_client_name ?>" />
						<input type="hidden" name="hid_internal_ro" value="<?php echo $hid_internal_ro ?>" />
						<input type="hidden" name="hid_cust_id" value="<?php echo $hid_cust_id ?>" />
						<input type="hidden" name="post_network_share" value="<?php echo $post_network_share ?>" />
						<input type="hidden" name="total_rows" value="<?php echo $total_rows ?>" />
						<input type="hidden" name="post_final_amount" value="<?php echo $post_final_amount ?>" /> -->
						
					</form>
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			
		
	<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>	