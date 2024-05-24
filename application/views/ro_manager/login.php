
<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

	<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->

		<div class="block small center login">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>SureWaves Easy RO Login</h2>
					<img src="<?php echo ROOT_FOLDER ?>/images/EasyR0-logo-Black.PNG" width="200" height="35px"style="float:right; padding-top:13px;" />

				</div>		<!-- .block_head ends -->
				
	
				<div class="block_content">
					
					<?php if ( isset($error_msg)&&!empty($error_msg) ) { ?>
					<div class="message info">
					<p>
					<?php echo $error_msg ?>
					</p>
					</div>
					<? } ?>
					
					<form action="<?php echo ROOT_FOLDER ?>/login_controller/postLogin" method="post" >
						<p>
							<label>Email:</label> <?php echo form_error('email'); ?><br />
							<input type="text" class="text" name="email" value="<?php echo set_value('email'); ?>" />
						</p>
						
						<p>
							<label>Password:</label> <?php echo form_error('passwd'); ?><br />
							<input type="password" name="passwd" class="text" value="" />
						</p>
						
						<p>
							<input type="submit"  class="submit" value="Login" />&nbsp;&nbsp;
                            <a href="javascript:forgot_password()">Forgot password?</a>
						</p>
					</form>	

                    <p><img src="<?php echo ROOT_FOLDER ?>/images/Black_SW.png" style="float:right" width="200"/></p>
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			

		</div>						<!-- wrapper ends -->
		
	</div>		<!-- #hld ends -->
	
<script language="javascript">
function forgot_password() {
    console.log("hello");
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/forgot_password/',iframe:true, width: '540px', height:'270px'});
}
</script>	
