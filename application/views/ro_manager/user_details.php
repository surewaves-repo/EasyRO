<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->
	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
				
				<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				
				<?php echo $menu ?>
				
				<p class="user">Hello, <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
			
			<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>User Details</h2>
				</div>		<!-- .block_head ends -->
				</form>
				
				
				<div class="block_content">							
					<table cellpadding="0" cellspacing="0" width="60%">
						<tr>
							<td style="font-weight:bold">Name</td><td><?php echo $logged_in_user['user_name'] ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Email</td><td><?php echo $logged_in_user['user_email'] ?></td>
						</tr>    
						<tr>
							<td style="font-weight:bold">Phone</td><td><?php echo $logged_in_user['user_phone'] ?></td>
						</tr>
						<tr>
                                                        <td style="font-weight:bold">Profile Type</td><td><?php echo $profile_details['profile_name'] ?></td>
                                                </tr>
						<tr>
							<td style="font-weight:bold">Password</td>
							<td><a href="javascript:change_user_password('<?php echo $logged_in_user['user_id'] ?>')">Change User Password</a></td>
						</tr>
					</table>		
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->

		</div>  <!--wrapper ends-->
</div> 	<!--hld ends -->

<script language="javascript">
function change_user_password(id){
			$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/change_user_password/' + id,iframe:true, width: '530px', height:'280px'}); 
}
</script>
