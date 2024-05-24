<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/flexigrid.pack.css" />
<script type="text/javascript" src="/surewaves_easy_ro/js/flexigrid.js"></script>
<style type="text/css" media="all">
#campaign_loader	{
    background: none repeat scroll 0 0 #FFFFFF;
    border-radius: 10px;
    border: 5px solid #A2A2A2;
    display: block;
    font-weight: bold;
    height: 100px;
    left: 38%;
    padding-top: 15px;
    position: absolute;
    text-align: center;
    top: 45%;
    width: 280px;
    z-index: 9999;
}
#overlay {
    background-color: #000;
    filter:alpha(opacity=60); /* IE */
    opacity: 0.6; /* Safari, Opera */
    -moz-opacity:0.66; /* FireFox */
    z-index: 20;
    height: 200%;
    width: 100%;
    background-repeat:no-repeat;
    background-position:center;
    position:absolute;
    top: 0px;
    left: 0px;
}
</style>

<div id="hld">
     <div id="campaign_loader" style="display:none;"><img src='<?php echo ROOT_FOLDER ?>/images/loader_full.GIF' height="50" width="50"/><br><br>Loading Campaigns ...</div>
		<div class="wrapper">		<!-- wrapper begins -->
	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
			<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>	
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				
				<?php echo $menu; ?>
				
				<p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
			
			<div class="block">
			
				<form method="post" name="search_form" id="search_form" action="<?php echo ROOT_FOLDER ?>/ro_manager/search_campaign_schedule_content">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Campaign Wise Schedule Summary</h2>
					
					<ul style="float:right;padding-left:10px;">
                        <li><label>Search By: </label>
                                <select name="search_by" id="search_by">
                                    <option value="">-</option>
                                    <option value="campaign_name">Campaign Name</option>
                                    <option value="brand_new">Brand Name</option>
                                    <option value="agency_name">Agency Name</option>
                                    <option value="caption_name">Caption Name</option>
                                    <!--<option value="">Channel Name</option>-->
                                    <option value="derived_campaign_status">Campaign Status</option>
                                </select>
                            <input type="hidden" name="order_id" id="order_id_hid" value="<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=')?>"/>
                            <input type="hidden" name="edit" id="edit_hid" value="<?php echo $edit ?>"/>
                            <input type="hidden" name="am_ro_id" id="am_ro_id_hid" value="<?php echo $id ?>"/>
                        
                        <label> </label> &nbsp; <input type="text" id="SearchTF" class="text" placeholder="Enter text to search ..." value="<?php if ( isset($search_str) && !empty($search_str) ) { echo $search_str; } ?>" name="search_str"   />
                            <input type="hidden" id="SearchTF_encode" value="" name="SearchTF_encode" />
                        </li>
						<li >
                            <input type="image" style="vertical-align:middle" value="submit" src="<?php echo ROOT_FOLDER ?>/images/search_small.png" onclick="return check_form()"/>
                            <input type="image" style="vertical-align:middle" value="button" src="<?php echo ROOT_FOLDER ?>/images/reload_small.png" onclick="javascript:load_all_campaigns('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')"/>
                        </li>

					<ul>
					<ul>
						
					</ul>
				</div>		<!-- .block_head ends -->
				</form>
				<div class="block_content">				
					
						<table cellpadding="0" cellspacing="0" width="100%">
						
							<tr>
								<th>Customer RO Number</th>
								<th>Agency Name</th>
								<th>Advertise name</th>
								<th>Brand Name</th>
								<th>RO Start Date</th>
								<th>RO End Date</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th><!--
								<th>&nbsp;</th>
								<th>&nbsp;</th>-->
							</tr>			
							
								<tr>
									<td> <?php echo $am_ext_ro?><br/> <?php echo '(Internal RO Number:'.$internal_ro.")" ; ?> </td>
									<td><?php echo $agency ?> &nbsp;</td>
									<td><?php echo $client ?></td>
									<td><?php echo $brand ?></td>
									<td><?php print date('d-M-Y', strtotime($camp_start_date)); ?> </td>
									<td><?php print date('d-M-Y', strtotime($camp_end_date)); ?> </td>
									<!--<td><a href="javascript:downloadPDF('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Download PDF</a></td>
									<td><a href="javascript:campaigns_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Campaigns Schedule</a></td>-->
									<td><a href="javascript:channels_schedule('<?php echo  rtrim(base64_encode($internal_ro),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Channels Schedule</a></td>
<!--									<td><a href="javascript:ro_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">RO Schedule</a></td>-->
								<td><a href="javascript:approve('<?php echo  rtrim(base64_encode($internal_ro),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Approval Page</a></td>	
								</tr>
													
						</table><br>		
					<div class="paggination right">
							<?php echo $page_links ?>
					</div>		<!-- .paggination ends -->
						
				</div>	<!-- .block ends -->
				<div class="block_content">
                    <input type="button" class="submitlong" id="cancel_campaigns" style="display: none" value="Cancel Campaign" onclick="javascript:cancel_selected_campaigns()">
					<?php if(count($campaigns) > 0) { ?>
					<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px;">
						<tr>
								<?php if($profile_id == 3){?><th>&nbsp;</th><?php }?>
                                <th>Campaign Name</th>
								<th>Brand Name</th>
								<th>Agency Name</th>
								<th>Campaign Start Date</th>
								<th>Campaign End Date</th>
								<th>Caption Name</th>
								<th>Caption Duration(Sec)</th>
								<th>Channel Name</th>
								<!--<th>RO Impressions</th> -->
								<th>Scheduled / Booked</th>
								<th>Campaign Status</th>				
								<th>Campaign ID</th>
                                <th>Market</th>
							</tr>
						<tr><?php foreach($campaigns as $camp){	
								$csv = explode(",",$camp['csv_input']);
								$channels = explode("#",$csv[12]);
								$channel_names = implode(",",$channels);
								
								if($camp['mismatch_impression'] == 0) {
									$color = '#00FF00' ;
								}else if($camp['mismatch_impression'] == 1) {
									$color = '#FF0000' ;
								}else {
									$color = '#FFFF00' ;
								}
							?>
							<?php if($profile_id == 3){?>
                            <td>
                                <?php if($camp['derived_campaign_status'] == 'pending_approval'){?>
                                <input type="checkbox" name="cancel_<?php echo $camp['campaign_id']?>" class="campaigns_to_cancel" value="<?php echo $camp['campaign_id']?>" onclick="javascript:show_hide_cancel_campaign()">
                                <?php }?>
                            </td>
                            <?}?>

                            <td bgcolor="<?php echo $color; ?>"><div style="width:90px;word-wrap:break-word;">  <?php echo $camp['campaign_name']?> </div></td>
							 <td> <div style="width:90px;word-wrap:break-word;"> <?php echo $camp['brand_new']?></div></td>
							 <td> <div style="width:67px;word-wrap:break-word;"> <?php echo $camp['agency_name']?></div></td>
							 <td> <div style="width:77px;word-wrap:break-word;"> <?php print date('d-M-Y', strtotime( $camp['start_date']))?></div></td>
							 <td> <div style="width:77px;word-wrap:break-word;"> <?php print date('d-M-Y', strtotime( $camp['end_date']))?></div></td>
							<td>  <div style="width:74px;word-wrap:break-word;"> <?php echo $camp['caption_name'] ?>  </div></td>
							 <td> <div style="width:50px;word-wrap:break-word;"><?php echo round($camp['ro_duration'],2)?></div></td>
							 <td> <div style="width:70px;word-wrap:break-word;"><?php echo $channel_names; ?></div></td>
							<!-- <td>// <?php //echo $camp['actual_impressions']?> </td> -->
							<td> <div style="width:70px;word-wrap:break-word;"><?php echo $camp['scheduled_impression'] .' / '. $camp['booked_impression']?> </div></td>
							 <!--<td> <?php //echo $camp['campaign_status']?> </td>-->
                            <td><div style="width:73px;word-wrap:break-word;">  <?php echo $camp['derived_campaign_status']?> </div></td>
							 <td> <div style="width:93px;word-wrap:break-word;"><?php echo $camp['campaign_id']?> </div></td>
                             <td> <div style="width:71px;word-wrap:break-word;"><?php echo $camp['sw_market_name']?></div> </td>
						</tr>
						
						<?php }  ?>
						
					</table>
					<?php } else {
                                if($search_value_set == 1){ ?>
                                    <h2 style="text-align:center;">No Result Found !</h2>
                                <?php }else{ ?>
                                    <h2 style="text-align:center;">No Active Campaign !</h2>
                                <?php }
                            }?>
                </div>
			</div>
		</div>
	</div>
<div id="overlay" style="display: none;"></div>
<script language="javascript">
function approve(order_id,edit,id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approve/" + order_id + "/" + edit + "/" + id;
}
function channels_schedule(order_id,edit,id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/channels_schedule/" + order_id + "/" + edit + "/" + id;
}
function cancel_selected_campaigns(){
    $('#overlay').show();
    $('#campaign_loader').show();

    var order_id = $('#order_id_hid').val();
    var edit = $('#edit_hid').val();
    var id = $('#am_ro_id_hid').val();

    var cancel_campaigns = new Array();
    $("input:checked").each(function() {
        cancel_campaigns.push($(this).val());
    });

    $.ajax({
        type: "POST",
        url: "<?php echo ROOT_FOLDER ?>/ro_manager/cancel_campaign/",
        data: { campaigns_to_cancel:cancel_campaigns},
        success: function(data){
            load_all_campaigns(order_id,edit,id);
        }
    });

}
    function show_hide_cancel_campaign(){
        if($("input:checked").length > 0){
            $("#cancel_campaigns").show();
        }else{
            $("#cancel_campaigns").hide();
        }
    }

    function check_form(){
        var str = $('#SearchTF').val();
        var search_by = $('#search_by').val();
        if(search_by == ''){
            alert('Please select search by');
            return false;
        }
    }

    function load_all_campaigns(order_id,edit,id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/campaigns_schedule/" + order_id + "/" + edit + "/" + id;
    }
</script>
	
