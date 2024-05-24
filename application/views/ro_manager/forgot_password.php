<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
	
	
			
			<div class="block small center login" style="margin:0px">		
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Recover Password</h2>					
				</div>	
				
				<div class="block_content">
					
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_forgot_password" method="post">
						
						<p>
							<label>Email:<?php echo form_error('user_email'); ?></label> <br />
							<input type="text" class="text" name="user_email" value="<?php echo set_value('user_email'); ?>" />
						</p>		
						<p>
							<input type="submit" class="submitlong" value="Recover Password" /> 
							
						</p>
					</form>
					
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		
			
		
		

