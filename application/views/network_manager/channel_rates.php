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
				<h2>Channel Rates</h2>
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
								echo form_input('filterKeyText', ($filterOptions['filterKey'] == '0' ? '' : $filterOptions['filterKey']), 'id = "filterKeyText" placeholder="Enter Keyword" required="required" class="textsmall"'); 
							?>
						</div>

						<input type="submit" name="filter" value=" Filter " class="submit" />
						<input type="button" name="reset" value=" Reset " class="submit" id="resetBtn" data-href="<?php echo base_url('network_svc_manager/channel_rates') ?>" />
                        <?php ?>
                        <a href="<?php echo base_url('network_svc_manager/get_channel_rates_csv'); ?>" target="_blank" class="sw-export-btn" title="Export as CSV">
                            <img src="<?php echo ROOT_FOLDER ?>/images/CSV.png" style="vertical-align: middle;">
                        </a>
					</form>
				</div>
				<form method="post" action="<?php base_url('network_svc_manager/channel_status'); ?>">
				<table style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 5%">#</th>
							<th style="width: 10%">Network</th>
                            <th style="width: 10%">Channel Id</th>
                            <th style="width: 10%">Channel Name</th>
                            <th style="width: 10%">Channel display Name</th>
							<th style="width: 13%">Spot Rate</th>
							<th style="width: 15%">Banner Rate</th>
                            <th style="width: 13%">Network Share </th>
                            <th style="width: 10%">Deployment Status</th>
							<th style="width: 25%">Markets</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = $links['from'];
                        $customerId = 0 ;
						foreach($data as $d):

                            $displayName = "" ;

                            if( !empty($d['display_name']) && !empty($d['locale']) ){

                            $displayName = $d['display_name']."-".$d['locale'] ;

                            }elseif( !empty($d['display_name']) ){

                                $displayName = $d['display_name']    ;

                            }elseif( !empty($d['display_name']) ){

                                $displayName = $d['locale']    ;

                            }
                        ?>
						<tr>
							<td><?php echo $i++; ?></td>
							<td><?php echo $d['network_name']; ?></td>
                            <td><?php echo $d['channel_id']; ?></td>
                            <td><div style="word-wrap: break-word;word-break: break-all;"><?php echo $d['channel_name']; ?></div></td>
                            <td><div style="word-wrap: break-word; word-break: break-all;"><?php echo  $displayName ; ?></div></td>
           				<td>
								<input type="number" step="any" class="textsmall" required="required" name="spot_avg<?php echo $d['channel_id']; ?>" id="spot_avg<?php echo $d['channel_id']; ?>" value="<?php echo number_format((float)$d['spot_avg'], 2, '.', ''); ?>" >
							</td>
							<td>
							<!--<label id="banner_avg_label<?php echo $d['channel_id']; ?>"> <?php echo number_format((float)$d['banner_avg'], 2, '.', ''); ?> </label>-->
								<input type="number" step="any" style="width:100px" class="textsmall" required="required" name="banner_avg<?php echo $d['channel_id']; ?>" id="banner_avg<?php echo $d['channel_id']; ?>"  value="<?php echo number_format((float)$d['banner_avg'], 2, '.', ''); ?>">
							</td>
                            <td>
                            <?php if( $customerId != $d['customer_id'] ) { ?>

                                <input type="number" class="textsmall" required="required" name="network_share_[<?php echo $d['customer_id']; ?>]" value="<?php echo number_format((float)$d['revenue_sharing'], 2, '.', ''); ?>">
                                <input type="hidden" name="network_share_old_[<?php echo $d['customer_id']; ?>]" value="<?php echo number_format((float)$d['revenue_sharing'], 2, '.', ''); ?>">

                            <?php }else{ ?>

                                -

                            <?php }

                            $customerId = $d['customer_id'] ;

                            ?>
                            </td>
                            <td><?php echo $d['deployment_status']; ?></td>
							<td><div style="width: 25%"><?php echo $d['market_name']; ?></div></td>
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
					<input type="submit" name="updateRates" value=" Update " class="submit" />
				</div>
				</form>
			</div>

			<div class="bendl"></div>
			<div class="bendr"></div>
		</div>

	</div>
</div>
<script type="text/javascript">
	function change_user_password(id){

			$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/change_user_password/' + id,iframe:true, width: '530px', height:'280px'}); 
	}
	$("#resetBtn").click(function(){
		window.location = $(this).attr('data-href');
	});

	$(document).ready(function(){
				<?php
						$selected_user_id = $this->session->userdata('selected_user_id');
						if ( isset($selected_user_id) && !empty($selected_user_id) ) { ?>
							change_user_password('<?php echo $selected_user_id ?>');
				<? 
							$this->session->unset_userdata("selected_user_id");
						} 
				?>

        $("input").change(function(){

            var spotValue = parseInt( $(this).val() );
            var spotFctId = $(this).attr("id") ;

            var channelId = spotFctId.split("spot_avg") ;
            var bannerFCTId = "banner_avg" + channelId[1] ;
	    //var bannerFctLabelId= "banner_avg_label" + channelId[1] ;

            var bannerFctValue = (spotValue/4).toFixed(2);

            $( "#"+bannerFCTId).val( bannerFctValue ) ;
		//$( "#"+bannerFctLabelId).html( bannerFctValue ) ;
        }) ;

    }) ;
</script>
