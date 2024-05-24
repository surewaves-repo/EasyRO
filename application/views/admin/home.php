<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->
		
			<div class="block">			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Manage Users</h2>
					
							
					<ul>
						<li><a href="javascript:add_user()">Add User</a> | <a href="<?php echo ROOT_FOLDER ?>/admin/logout">Logout</a></p></li>
					</ul>	
				</div>		<!-- .block_head ends -->

                <!--<div style="margin-top: 10px; color: red; margin-left: auto; margin-right: auto; width: 40%;">
                    !! Please click on assign; for assigning region and reporting manger to user.
                </div>
				-->
				<div class="block_content">					
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr style="font-weight:bold">
							<td>User Name</td>
							<td>User Email</td>
							<td>User Phone</td>
							<td>Profile Type</td>
							<td>Status</td>
							<td>Registration Date</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						
						<?php foreach ( $users as $user) {?>
						<tr>
							<?php if( ($user['region_id'] == 0 || $user['reporting_manager_id'] == 0) && $user['active'] == '1') {?>

                                <td>
                                    <?php echo $user['user_name'] ?> <!--| <a href="javascript:assign_reporting_region('<?php echo $user['user_id'] ?>','<?php echo $user['profile_id'] ?>')"> Assign </a>
                                --></td>

                            <?php }else{ ?>

                                <td><?php echo $user['user_name']  ?></td>

                            <?php } ?>


                            <td><?php echo $user['user_email'] ?></td>
							<td><?php echo $user['user_phone'] ?></td>
							<td><?php echo $user['profile_name'] ?></td>
							<td><?php if($user['active'] == '1') { ?>

                                    Activated

                               <?php } else {?>

                                   De-Activated <!-- | <a href="javascript:activate_user('<?php echo $user["user_id"] ?>')">Activate</a>
                                                -->
                              <?php  } ?></td>
							<td><?php print date('d-M-Y', strtotime( $user['creation_datetime'] )); ?></td>	
							<td>
                                <?php if($user['active'] == '1') { ?>
                                <a href="javascript:edit_user('<?php echo $user['user_id'] ?>')">Edit User</a>
                                <?php }else{ ?>
                                    Edit User
                                <?php }?>
                            </td>
							<td>
                                <?php if($user['active'] == '1') { ?>

                                <a href="javascript:delete_user('<?php echo $user['user_id'] ?>')" onclick="return confirm('Are you sure you want to delete?')"><img src="<?php echo ROOT_FOLDER ?>/images/remove-item.png" /></a></a>

                                <?php } else{ ?>
                                    <img src="<?php echo ROOT_FOLDER ?>/images/remove-item.png" />
                               <?php }?>
                            </td>
						</tr>
						<? } ?>							
					</table>
					
					<div class="paggination right">
						<?php echo $page_links ?>
					</div>		<!-- .paggination ends -->
					
				</div>		<!-- .block_content ends -->
				
				
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->
			
		    <form id="enableForm" method="post" action="<?php echo ROOT_FOLDER ?>/admin/enableUser">

                <input type="hidden" name="userId" id="userId" value="">

		    </form>
		
		</div>						<!-- wrapper ends -->
		
	</div>		<!-- #hld ends -->

<script language="javascript">

function add_user () {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/admin/add_user',iframe:true, width: '515px', height:'580px'});
}
function delete_user(user_id) {
	window.location.href = "<?php echo ROOT_FOLDER ?>/admin/delete_user/" + user_id ;
}
function edit_user (user_id) {
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/admin/edit_user/'+user_id,iframe:true, width: '520px', height:'540px'});
}
function activate_user (user_id) {

    $("#userId").val( user_id ) ;
    $("#enableForm").submit() ;
}

function assign_reporting_region( userId, profileId ){

    $.colorbox({href:'<?php echo ROOT_FOLDER ?>/admin/getRegionReportingManger/'+userId +'/'+profileId,iframe:true, width: '520px', height:'352'});

}

</script>
