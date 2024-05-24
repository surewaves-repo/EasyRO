<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reach_report/report_css.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reach_report/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reach_report/prism.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reach_report/chosen.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style.css">
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/googleapis_jquery_1_6_4.min.js"></script>
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/chosen.jquery.js"></script>
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/prism.js"></script>
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/spot_tv_reach.js"></script>

</head>
<body>
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
				<h2 style="border-bottom:1px solid #ccc;margin:0px;">SPOT TV REACH</h2>
			</div>
			<div class="block_content">
			 <form name="spot_tv_reach_data" method="post" id="spot_tv_reach_data" action="<?php echo ROOT_FOLDER ?>/spot_tv_reach/save_reach_data">
				<div class="sub_content1">
					<div class="shadow_div">HOUSEHOLD DATA</div>
					<div class="sub_content1_child">
						<div>
							<label>TOTAL HOUSEHOLDS</label>
							<span> 
								<input type="text" id="total_households" name="total_households" class="" value="<?php echo $tam_overview[0]['total_households']; ?>"/>
							</span>
						</div>
						<div>
							<label> TOTAL TV HOUSEHOLDS</label>
							<span> <input type="text" id="total_tv_households" name="total_tv_households" class="" value="<?php echo $tam_overview[0]['total_tv_households']; ?>"/> </span>
						</div>
						<div>
							<label> TOTAL CABLE & SATELLITE HOUSEHOLDS </label>
							<span> <input type="text" id="total_cable_sat_households" name="total_cable_sat_households" class="" value="<?php echo $tam_overview[0]['total_cns_households']; ?>"/> </span>
						</div>
						
					</div>
				</div>
				<div class="sub_content2">
				
					<div class="shadow_div">MARKET-WISE REACH DATA</div>
					<div class="sub_content2_child">
						<!--<div class="sub_content2_child_sub1">
							<label class="label_heading">MARKETS</label>
							<span class="list_markets">
								<select id="market_list" class="" name="" multiple="multiple">
									<option value="all" selected="selected">all</option>
									<option value="1">ALL MARKETS1</option>
									<option value="2">ALL MARKETS2</option>
									<option value="3">ALL MARKETS3</option>
								</select>
							</span>
						</div>-->
						<div class="sub_content2_child_sub2">
							<div class="sub_content2_child_sub2_heading">
								<div>State</div>  								
								<div style="width:18%">Excluded Channels</div>								
								<div>Tv Households</div>
								<div>C&S</div>
								<div>Cable TV</div>
								<div>SW PARTNER</div>								
								<div class="lbl_endcell">% SHARE</div>
							</div>
							<br style="clear:both;" />
							<?php 
							$marketIdStr  = '';
								foreach($market_data as $val){
									($marketIdStr == '')?$marketIdStr = $val['id'] : $marketIdStr.= ",".$val['id'];
									$channel_idArr 		= explode(",",$val['channel_id']);
									$channel_nameArr 	= explode(",",$val['channel_names']);
							?>
							<div class="sub_content2_child_sub2_value">							
								<div><p><?php echo $val['state']; ?></p><p style="padding:0"><input type="radio" class="sheetFilter"  data-marketId="<?php echo $val['id']; ?>" name="sheet[<?php echo $val['id']; ?>]" value="both" checked="checked">Both <input type="radio" class="sheetFilter" name="sheet[<?php echo $val['id']; ?>]" data-marketId="<?php echo $val['id']; ?>" value="online">Online <input type="radio" name="sheet[<?php echo $val['id']; ?>]" class="sheetFilter" data-marketId="<?php echo $val['id']; ?>" value="offline">Offline</p></div>
								
								<div style="width:18%">
									<select name="channel_list_<?php echo $val['id']; ?>[]" id="channel_list_<?php echo $val['id']; ?>" class="channel_list" multiple="multiple" data-placeholder="Select a channel" data-market="<?php echo $val['id']; ?>">
										<option selected="selected" value="all">No Channels Excluded</option>
										<?php for($arrCount = 0;$arrCount<count($channel_idArr);$arrCount++) { ?>
											<option value="<?php echo $channel_idArr[$arrCount]; ?>"><?php echo $channel_nameArr[$arrCount]; ?></option>
										<?php } ?>										
									</select>
								</div>
																
								<div><input type="text" name="tvh_<?php echo $val['id']; ?>" id="tvh_<?php echo $val['id']; ?>" data-market="<?php echo $val['id']; ?>" class="content_value tvh"  value="<?php echo trim($val['tvHH']); ?>"/></div>
								<div><input type="text" name="cns_<?php echo $val['id']; ?>" id="cns_<?php echo $val['id']; ?>" data-market="<?php echo $val['id']; ?>" class="content_value cns" value="<?php echo trim($val['cns']); ?>"/></div>
								<div><input type="text" name="cabletv_<?php echo $val['id']; ?>" data-market="<?php echo $val['id']; ?>" id="cabletv_<?php echo $val['id']; ?>" class="content_value cabletv" value="<?php echo trim($val['cabletv']); ?>"  onkeyup="javascript:changeReachPercentage('<?php echo $val['id']; ?>');" /></div>
								<div><input type="text" name="sw_partner_<?php echo $val['id']; ?>" data-market="<?php echo $val['id']; ?>" id="sw_partner_<?php echo $val['id']; ?>" class="content_value sw_partner" value="<?php echo trim($val['sw_partner']); ?>" onkeyup="javascript:changeReachPercentage('<?php echo $val['id']; ?>');" /></div>
								<div class=""><input type="text" name="share_<?php echo $val['id']; ?>" readonly data-market="<?php echo $val['id']; ?>" id="share_<?php echo $val['id']; ?>" class="content_value share" value="<?php echo (ceil(($val['sw_partner']/$val['cabletv']) * 100)); ?>" /></div>
								
							</div>	
								<?php } ?>
							
						</div>						
					</div>
					<div class="submit_button">
                        <input type="button" value="Save" class="submit" id="submit_data" />
                       <input type="button" id="report_data" class="generate_report_btn" />
                    </div>
				</div>
				<input type="hidden" value = "<?php echo $marketIdStr; ?>" name="market_id_list" />

			  </form>

			</div>
			
		</div>
	</div>
</div>
<form name="generate_reach_data" method="post" id="generate_reach_data" action="<?php echo ROOT_FOLDER ?>/spot_tv_reach/generate_reach_report">
    <input type="hidden" name="excluded_Channel_ids" id="excluded_Channel_ids"  value="" />
	<input type="hidden" name="filtered_sheets" id="filtered_sheets" value="" />
</form>
</body>
</html>
		
