<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
	
			
			<div class="block small center login" style="margin:0px">		
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Change Password</h2>					
				</div>	
				
				<div class="block_content">
					
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_change_user_password" method="post">						
						<p>
							<label>Enter New Password: <?php echo form_error('user_password'); ?></label> <br />
							<input type="password" class="text" name="user_password" value="<?php echo set_value('user_password'); ?>" />
							
						</p>	
						
						<input type="hidden" name="id_for_change" value="<?php echo $id_for_change ?>" />
						<p>
							<input type="submit" class="submit" value="submit" /> 
							
						</p>
					</form>
					
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			
	<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>	
		
