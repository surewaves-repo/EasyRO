<?php
/**
 * View to Display the Channel Status
 */
include_once dirname(__FILE__) . "/../inc/header.inc.php";
?>
<style type="text/css">
	.block form select.sw-select {
		width: 245px;
		height: 33px;
		margin-right: 20px;
		padding: 7px;
		display: inline-block;
		margin-right: 20px;
		border: 1px solid #bbb;
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
		/*background: url('../images/sdd.jpg') center right no-repeat;
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;*/
	}

	.block form .sw-select:hover {
		/*background: url('../images/sdd_.jpg') center right no-repeat;*/
	}
</style>

<div id="hld">

	<div class="wrapper">

		<div id="header">
			<div class="hdrl"></div>
			<div class="hdrr"></div>

			<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
			<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

			<?php echo $menu; ?>

			<p class="user">
				Hello, <?php echo $logged_in_user['user_name'] ?>
				| <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a>
			</p>
		</div>

		<div class="block">

			<div class="block_head">
				<div class="bheadl"></div>
				<div class="bheadr"></div>
				<h2>Channel Status</h2>
			</div>

			<div class="block_content">
				
				<?php if(isset($msg)): ?>
					<div class="message info">
						<p>
							<?php echo $msg ?>
						</p>
					</div>
				<?php endif; ?>

				<div>
					<form method="post" action="<?php base_url('network_svc_manager/channel_status'); ?>">

						<label>Filter : </label>

						<?php echo form_dropdown('filterBy', $filterOptions['options'], ($filterOptions['filterBy'] == '0' ? $filterOptions['options'][0] : $filterOptions['filterBy']), 'id = "selFilter" class="sw-select"'); ?>

						<div id="filterKeyContainer" style="display: inline;">
							<?php
							$check = (in_array((string)$filterOptions['filterBy'], array('notice', 'noticero', 'blocked')) ? TRUE : FALSE);
							$checked = ($check && $filterOptions['filterKey'] == 'true' ? TRUE : FALSE);

							$disabled = ($check ? ' disabled = "disabled" style="display: none"' : '');
							echo form_input('filterKeyText', ($filterOptions['filterKey'] == '0' ? '' : urldecode($filterOptions['filterKey'])), 'id = "filterKeyText" required="required" class="textsmall" placeholder="Enter Keyword"'.$disabled);
							?>
							<span>
								<?php
								$disabled = (!$check ? ' disabled = "disabled" style="display: none"' : ''); 
								echo form_checkbox('filterKeyBox', 'true', $checked, 'id = "filterKeyBox" class="checkbox"'.$disabled);
								
								$disabled = (!$check ? ' style="display: none"' : '');  
								?>
								<label for="filterKeyBox" id="filterKeyBoxLabel" <?php echo $disabled; ?>>Check</label>
							</span>
						</div>

						<input type="submit" name="filter" value=" Filter " class="submit" />
						<input type="button" name="reset" value=" Reset " class="submit" id="resetBtn" data-href="<?php echo base_url('network_svc_manager/channel_status') ?>" />
                        <a href="<?php echo base_url('network_svc_manager/get_channel_status_csv'); ?>" target="_blank" class="sw-export-btn" title="Export as CSV">
                            <img src="<?php echo ROOT_FOLDER ?>/images/CSV.png" style="vertical-align: middle;">
                        </a>
                        <?php ?>
					</form>

				</div>
				<form method="post" action="<?php base_url('network_svc_manager/channel_status'); ?>">
					<table style="width: 100%;">
						<thead>
							<tr>

								<th >Network</th>
                                <th >Channel Id</th>
                                <th >Channel Name</th>
                                <th >Channel Display Name</th>
                                <th >Priority</th>
                                <th >Sent Notice</th>
								<th >Sent Notice and RO</th>
								<th >Blocked</th>
                                <th >Is Satellite</th>
								<th >Ping Status</th>
                                <th >Deployment Status</th>
								<th >Markets</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = $links['from']; 
							foreach($data as $d):
                                $arrPriority =  array(  $d['channel_id'].'_p1' => 'p1',
                                                        $d['channel_id'].'_p2' => 'p2',
                                                        $d['channel_id'].'_p3' => 'p3'
                                                );
                                $displayName = "" ;

                                if( !empty($d['display_name']) && !empty($d['locale']) ){

                                    $displayName = $d['display_name']."-".$d['locale'] ;

                                }elseif( !empty($d['display_name']) ){

                                    $displayName = $d['display_name']    ;

                                }elseif( !empty($d['locale']) ){

                                    $displayName = $d['locale']    ;

                                }
								?>
							<tr>

								<td><?php echo $d['network_name']; ?></td>
                                <td><?php echo $d['channel_id']; ?></td>
                                <td><?php echo $d['channel_name']; ?></td>
                                <td><?php echo  $displayName ; ?></td>
                                <td>
                                    <select name="priority[]" >
                                        <?php

                                        foreach($arrPriority as $key => $val){
                                            $selected="";
                                            if($val == $d['priority']) {
                                                $selected='selected="selected"';
                                            }
                                            ?>
                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?> > <?php echo $val; ?> </option>
                                        <?php } ?>
                                    </select>
                                </td>

								<td align="center">
									<input type="checkbox" name="isNotice[]" id="chkIsNotice<?php echo $d['channel_id'] ?>" value="<?php echo $d['channel_id'] ?>" <?php echo ($d['is_notice'] == 1 ? 'checked="checked"' : '' ) ?> />
									<!--<label for="chkIsNotice<?php echo $d['channel_id'] ?>">Notice</label>-->
								</td>
								<td align="center">
									<input type="checkbox" name="isNoticeRO[]" id="chkIsNoticeRO<?php echo $d['channel_id'] ?>" value="<?php echo $d['channel_id'] ?>"  <?php echo ($d['is_notice_ro'] == 1 ? 'checked="checked"' : '' ) ?> />
                                    <!--<label for="chkIsNoticeRO<?php echo $d['channel_id'] ?>">Notice and RO</label>-->
								</td>
								<td align="center">
									<input type="checkbox" name="isBlocked[]" id="chkIsBlocked<?php echo $d['channel_id'] ?>" value="<?php echo $d['channel_id'] ?>"  <?php echo ($d['is_blocked'] == 1 ? 'checked="checked"' : '' ) ?> />
									<!--<label for="chkIsBlocked<?php echo $d['channel_id'] ?>">Blocked</label>-->
								</td>
                                <td align="center">
                                    <input type="checkbox" name="isSatellite[]" id="chkIsSatellite<?php echo $d['channel_id'] ?>" value="<?php echo $d['channel_id'] ?>"  <?php echo ($d['is_satellite_channel'] == 1 ? 'checked="checked"' : '' ) ?> />
                                    <!--<label for="chkIsSatellite<?php echo $d['channel_id'] ?>">Satellite</label>-->
                                </td>
								<td align="center" style="padding-left:25px">
								   <?php  echo $d['channel_connectivity'];  ?>
								</td>
                                <td><?php echo $d['deployment_status']; ?></td>
                                <td><?php echo $d['market_name']; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<!--<div style="display: inline-block; float: left;">
					<?php echo $links['links']; ?> <br />
					<?php echo 'Showing ' . $links['from'] . ' - ' . $links['to'] . ' out of ' . $links['count'] . ' result(s)'; ?>
				</div>-->
				<div style="display: inline-block; float: right;">
					<input type="hidden" name="channels" value="<?php echo $channels; ?>" />
					<input type="submit" name="updateNotice" value=" Update " class="submit" />
				</div>
			</form>
		</div>

		<div class="bendl"></div>
		<div class="bendr"></div>
	</div>

</div>
</div>

<script type="text/javascript">
	$("#selFilter").change(function(){
		$("#filterKeyContainer").find("input").attr('disabled', 'disabled').css('display', 'none');
		if($("#selFilter").val() == 'market' || $("#selFilter").val() == 'channel' || $("#selFilter").val() == 'network'){
			$("#filterKeyText").removeAttr('disabled').css('display', 'inline').val('');
			$("#filterKeyBoxLabel").css('display', 'none');
			
		}
		else{
			$("#filterKeyBox").removeAttr('disabled').css('display', 'inline');
			$("#filterKeyBoxLabel").css('display', 'inline');
		}
	});

	$('input[type="checkbox"]').change(function(){
		var chnl = $(this).val();
		var status = $(this).attr('checked');
		$("#chkIsNotice"+chnl).removeAttr('checked');
		$("#chkIsNoticeRO"+chnl).removeAttr('checked');
		$("#chkIsBlocked"+chnl).removeAttr('checked');
		$(this).attr('checked', status);
	});

	$("#resetBtn").click(function(){
		window.location = $(this).attr('data-href');
	});
        
        function change_user_password(id){
            $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/change_user_password/' + id,iframe:true, width: '530px', height:'280px'}); 
        }
        
        $(document).ready(function() {
                <?php 
                      $selected_user_id = $this->session->userdata('selected_user_id');
                      if ( isset($selected_user_id) && !empty($selected_user_id) ) { ?>
                        change_user_password('<?php echo $selected_user_id ?>');
                      <?php
                        $this->session->unset_userdata("selected_user_id");
                } ?>
        });
       
</script>
