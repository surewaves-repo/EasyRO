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
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/process_network_ro_payment_csv" method="post" enctype="multipart/form-data">
					
					<table cellpadding="0" cellspacing="0">
					<?php if(isset($value)){ ?>
						<tr>
							<td colspan="3"><span style="color:#F00;"><?php echo $value; ?> </span> </td>
							
						</tr>
					<? } ?>
					<tr>
						<td colspan="3">Select File<span style="color:#F00;"> *</span> : <input type="file" id="file_upload" name="file_upload" class="file_class" /></td>
                            
                                
                        </td>
					</tr>

					<tr>	
						<td> 
							<input type="submit" class="submit" value="Submit" onclick="return check_form();" />	
						</td>
						<td>&nbsp; </td>	
					<tr>	
					</table>	
					</form>	
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->

		</div>  <!--wrapper ends-->
</div> 	<!--hld ends -->

<script language="javascript">
function check_form(){
	if($('#file_upload').val() != ""){
		if($('#file_upload').val().substring($('#file_upload').val().lastIndexOf('.')+1).toLowerCase() != 'csv'){
			alert("Please upload csv file.");
			return false;
		}
	}
}
</script>