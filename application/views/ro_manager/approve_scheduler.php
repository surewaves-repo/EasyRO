
<div  style="height:700px">

		<div class="wrapper">		<!-- wrapper begins -->


						<div class="block" style="margin-bottom: 0px;">




				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>

					<h2>Customer Release Order</h2>
                    <input type="hidden" id='hid_id' name="hid_id" value="<?php echo $id ?>">
                    <input type="hidden" id="hid_internal_ro" name="hid_internal_ro" value="<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>">

				</div>

              <div class="block_content">
						<table cellpadding="0" cellspacing="0" width="100%">						
							<tr>
								<th>Customer RO Number</th>
                                <th>Submitted By</th>
                                <th>Approved By</th>
								<th>Agency Name</th>
								<th>Advertiser name</th>
								<th>Brand Name</th>
								<th>RO Start Date</th>
								<th>RO End Date</th>
								<th>RO Status</th>
								<th> Gross CRO Amount</th>
							</tr>			
							
								<tr>
									<td> 
                                    <?php echo $submit_ext_ro_data[0]['cust_ro'] ?><br/> <?php echo '(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ; ?>
                                    </td>
                                    <td><?php echo $submit_ext_ro_data[0]['submitted_by'] ?> &nbsp;</td>
                                    <td><?php echo $submit_ext_ro_data[0]['approved_by'] ?> &nbsp;</td>
									<td><?php echo $submit_ext_ro_data[0]['agency'] ?> &nbsp;</td>
									<td><?php echo $submit_ext_ro_data[0]['client'] ?></td>
									<td><?php echo $submit_ext_ro_data[0]['brand_name'] ?></td>
									<td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_start_date'])); ?> </td>
									<td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_end_date'])); ?> </td>
                                    <td><?php echo $ro_status_entry ; ?> </td>
									<td id="ro_amount"><?php echo round($ro_amount_detail[0]['ro_amount'],2) ?> </td>
						
						</tr><tr>	

									<td style="border-bottom: none"><span id="scheduler_campaign_schedule_span" class="link">Campaign Schedule</span></td>
                                <td style="border-bottom: none"><span id="scheduler_channels_schedule_span" class="link">Channels Schedule</span></td>
									<td style="border-bottom: none">&nbsp;</td> <?php if($ro_approval[0]['ro_approval_request_status']==1 ||  $ro_amount_detail[0]['ro_approval_status'] == 1) { ?>
									<td style="border-bottom: none">&nbsp;</td>
									<?php } else { ?>
                                                                            <?php
                                                                            
                                                                            if( strcmp(trim($ro_status_entry),'Scheduling In Progress') == 0 && $campaign_created == 1 && $ro_ready_for_schedule_approval == 1) { ?>
                                                                                <td style="border-bottom: none"><span class="link" id="scheduler_approval_request_span">Approval Request</span></td>
                                                                        <?php } ?>
                                                                        
                                                                       
                                <?php  }?> <td style="border-bottom: none"><span id="scheduler_add_file_location_span" class="link">Add File Location</span></td>
									

					
								</tr>
													 
						</table><br>
						

					</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>	<!-- .block ends -->	
					
					
				
			
			<?php if(count($scheduled_data) == 0) { ?>
				<div class="block">

                                <div class="block_head">
                                        <div class="bheadl"></div>
                                        <div class="bheadr"></div>
				</div>
			<div class="block_content">
				<h2 style="text-align:center;font-weight:300;">No Active Campaign !</h2>	
			</div>	
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>
			<?php } else { ?>
			<div class="block">
			
				<div  style="margin-top: 10px" class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Network RO Summary</h2>
				</div>		<!-- .block_head ends -->
				 <?php 
					$final_amount = 0 ;
                                	foreach($scheduled_data as $key => $dat) {
                                        	$total_amount = 0;
											$total_rows=0;
	                                        foreach($dat['channels_data_array'] as $chnl => $ch) {
        	                                        $channel_id = $ch['channel_id'];
                	                                $customer_id = $dat['network_id'];
													$customer_share = $ch['customer_share'];
                        	                        //foreach($ch['ad_types'] as $types => $ad) {
													if($ch['total_spot_ad_seconds'] != 0){
														$total_rows++;
														$total_amount += $ch['channel_spot_amount'] ;
													}
													if($ch['total_banner_ad_seconds'] != 0){
														$total_rows++;
														$total_amount += $ch['channel_banner_amount'] ;
													}
													
        	                                        //}
                	                        }
											$rows[$customer_id] = $total_rows;
                        	       			$final_amount += $total_amount - $total_amount * (100-$customer_share)/100;
                                	}
					if($ro_amount_detail[0]['ro_approval_status']==1) {
                                                $final_amount = $total_network_payout['network_payout'];
                                        }
				?>
				
				<div class="block_content">
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/saving_data_for_confirmation" method="post" id="myForm">
                    <input type="hidden" id='hid_edit' name="hid_edit" value="<?php echo $edit ?>">
                    <input type="hidden" name="nw_load_status" id="nw_load_status" value="0">
                    <input type="hidden" name="load_all_nw" id="load_all_nw" value="0">

                    <div id="recs">  
					<?php
						$count = 0 ;
						if(count($scheduled_data) > 0) {
						//foreach($scheduled_data as $scheduled_key => $data) {
							foreach($scheduled_data as $key => $dat) { 
							$total_networks = count($scheduled_data);
                                                        $row_id = 0;
					?>
					<table id ="<?php echo 'network_'.$key ?>" cellpadding="0" cellspacing="0" width="100%">
						<tr cellpadding="0" cellspacing="0" width="100%" >
							<td colspan="4" class="network_name_style" style="font-weight:bold; size:20px; width:400px;">Network Name: <?php echo $dat['network_name'] ?> &nbsp; (<?php echo $dat['market_name'] ?>)</td>
						</tr>
						<?php 	$total_amount = 0;
							$approved_total_amount = 0;
							foreach($dat['channels_data_array'] as $chnl => $ch) { 
							$total_channel =  count($dat['channels_data_array']);
							$channel_id = $ch['channel_id'];
							$customer_id = $dat['network_id'];
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
                                                        <td colspan="4" style="font-weight:bold">Channel Name: <?php echo $ch['channel_name'] ?>
                                                        </td>
                                                </tr>
						 <tr cellpadding="0" cellspacing="0" width="100%">
                                                        <th colspan="3">Ad Type</th>
                                                        <th colspan="3">Total Ad Seconds</th>
                                                </tr>
						<?php if($ch['total_spot_ad_seconds'] != 0){
								$amount = 0;
								$rate = $ch['channel_spot_avg_rate'];
                                $amount += $ch['total_spot_ad_seconds'] * $rate/10;
								$total_amount += $amount;
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Spot Ad</td>
							<td colspan="3" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0'; ?>" ><?php echo $ch['total_spot_ad_seconds'] ?></td>
							<?php    }
							if($ch['total_banner_ad_seconds'] != 0){
								$amount = 0;
								$rate = $ch['channel_banner_avg_rate'];
                                $amount += $ch['total_banner_ad_seconds'] * $rate/10;
								$total_amount += $amount;
						?>
						<tr cellpadding="0" cellspacing="0" width="100%">
							<td colspan="3">Banner Ad</td>
							<td colspan="3" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0'; ?>" ><?php echo $ch['total_banner_ad_seconds'] ?></td>
							<?php    } ?>
						<?php }
							
							$count++ ;
							$lastCount = $count ;
							}
						}
						?>
					</table>
					</div>


				<div class="paggination right">
					<?php echo $page_links ?>
				</div>		<!-- .paggination ends -->		
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->	
			<?php }   ?>
<input type="hidden" id="service_tax_value" value="<?php echo SERVICE_TAX ?>"/>
<script language="javascript">

    $('#scheduler_add_file_location_span').click(function(){
        console.log("scheduler_add_File_location");
        $.ajax(BASE_URL+'/ro_manager/add_file_location/',{
            type:'POST',
            data:{
                "order_id":$('#hid_internal_ro').val(),
                "edit":"1",
                "id":$('#hid_id').val()
            },
            dataType:'json',
            beforeSend:function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            success:function(responsedata)
            {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                if(responsedata.Status == 'success') {
                    $('#date_modal_title').text('Add File location');
                    $('#dateModal').modal('show');
                    $("#dateModal").data('bs.modal')._config.backdrop = 'static';
                    $('#date_Modal_body').empty();
                    $('#date_Modal_body').html(responsedata.Data.html);
                } else {
                    alert('Something Went Wrong!!');
                }
            },
            error:function(data)
            {
                alert("error in loading");
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
            }


        })
    })

    $(document).on("click",'#scheduler_cancel_campaigns',function(){

        var order_id = $('#hid_internal_ro').val();
        var edit = 0;
        var id = $('#hid_id').val();
        var cancel_campaigns = new Array();
        $("input:checked").each(function() {
            cancel_campaigns.push($(this).val());
        });

        $.ajax(BASE_URL+"/ro_manager/cancel_campaign/",{
            type: "POST",
            beforeSend:function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            data: { campaigns_to_cancel:cancel_campaigns},
            success: function(data){
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                $.ajax(BASE_URL+'/ro_manager/campaigns_schedule',{
                    type:'POST',
                    data:{
                        "order_id":$('#hid_internal_ro').val(),
                        "edit":"1",
                        "id":$('#hid_id').val()
                    },
                    dataType:'json',
                    beforeSend:function () {
                        $('#loader_background').css("display", "block");
                        $('#loader_spin').css("display", "block");
                    },
                    success:function(responsedata)
                    {
                        window.location.href=BASE_URL +'/account_manager/home';
                        $('#scheduler_cancel_campaigns').hide();
                        $('#loader_background').css("display", "none");
                        $('#loader_spin').css("display", "none");
                        var data=responsedata.Data.jsonData;
                        var ro_detail='';
                        if(data.campaigns.length>0) {
                            $.map(data.campaigns, function (campaign, index) {
                                ro_detail = ro_detail + '<tr>';
                                var csv = campaign.csv_input.split(',');
                                var channels = csv[12].split('#');
                                var channels_name = channels.join();
                                var color;
                                if(campaign['mismatch_impression'] == 0) {
                                    color = 'rgba(200, 247, 197, 0.5)' ;
                                }else if(campaign['mismatch_impression'] == 1) {
                                    color = 'rgba(231, 76, 60, 0.5)' ;
                                }else {
                                    color = 'rgba(245, 230, 83, 0.5)' ;
                                }
                                if (data.logged_in_user.profile_id == 3) {
                                    ro_detail = ro_detail + '<td>';
                                    if (campaign['derived_campaign_status'] == 'pending_approval') {
                                        ro_detail = ro_detail + "<input type='checkbox' class='campaigns_to_cancel' value='" + campaign['campaign_id'] + "'>";
                                    }
                                    ro_detail = ro_detail + '</td>';
                                }
                                ro_detail = ro_detail + "<td  style='background-color:"+color+" '><div style='width:90px;word-wrap:break-word;'>" + campaign['campaign_name'] + "</div></td>" +
                                    "                <td ><div style='width:90px;word-wrap:break-word;'>" + campaign['brand_new'] + "</div></td>" +
                                    "                <td > <div style='width:67px;word-wrap:break-word;'>" + campaign['agency_name'] + "</div></td>" +
                                    "                <td > <div style='width:77px;word-wrap:break-word;'>" + campaign['start_date'].split(' ')[0] + "</div></td>" +
                                    "                <td > <div style='width:77px;word-wrap:break-word;'>" + campaign['end_date'].split(' ')[0] + "</div></td>" +
                                    "                <td >  <div style='width:74px;word-wrap:break-word;'>" + campaign['caption_name'] + "</div></td>" +
                                    "                <td > <div style='width:50px;word-wrap:break-word;'>" + (campaign['ro_duration']) + "</div></td>" +
                                    "                <td > <div style='width:70px;word-wrap:break-word;'>" + channels_name + "</div></td>" +
                                    "                <td > <div style='width:70px;word-wrap:break-word;'>" + campaign['scheduled_impression'] + "/" + campaign['booked_impression'] + " </div></td>" +
                                    "                <td ><div style='width:73px;word-wrap:break-word;'>" + campaign['derived_campaign_status'] + "</div></td>" +
                                    "                <td > <div style='width:93px;word-wrap:break-word;'>" + campaign['campaign_id'] + "</div></td>" +
                                    "                <td > <div style='width:71px;word-wrap:break-word;'>" + campaign['sw_market_name'] + "</div> </td>" +
                                    "            </tr>";

                            });
                            $('#campaign_tbody').empty();
                            $('#campaign_tbody').html(ro_detail);
                        }
                    },
                    error:function()
                    {
                        $('#loader_background').css("display", "none");
                        $('#loader_spin').css("display", "none");
                        alert("error in cancelling channel");
                    }
                })
            }
        });

    });
    $('#scheduler_channels_schedule_span').click(function(){
        $.ajax(BASE_URL+'/ro_manager/channels_schedule',{
            type:'POST',
            data:{
                "order_id":$('#hid_internal_ro').val(),
                "edit":"0",
                "id":$('#hid_id').val()

            },
            beforeSend:function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            success:function(responsedata)
            {
                console.log("res "+responsedata);
                var data=responsedata.Data['html'];
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                $('#option_modal_title').text('Channels Schedule');
                $('#optionModal').modal('show');
                $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                $('#Option_Modal_body').html(data);
            },
            error:function(data)
            {
                alert("error in loading");
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
            }

        })
    })


    $(document).on('click','#scheduler_search_campaign_btn',function(){
        if($('#search_by').val()=='')
        {
            alert('select search by');
            return false;
        }
        var search_by='';
        var search_camp='';
        search_by=$('#search_by').val();
        search_camp=$('#search_camp').val();
        $.ajax(BASE_URL+'/ro_manager/campaigns_schedule',{
            type:'POST',
            data:{
                "order_id":$('#hid_internal_ro').val(),
                "edit":"0",
                "id":$('#hid_id').val(),
                "search_by":search_by,
                "search_value":search_camp
            },
            dataType:'json',
            beforeSend:function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            success:function(responsedata)
            {
                var data=responsedata.Data.jsonData;
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                console.log(data.campaigns);
                $('#campaign_tbody').empty();
                var ro_detail='';
                if(data.campaigns.length>0) {
                    $.map(data.campaigns, function (campaign, index) {
                        ro_detail = ro_detail + '<tr>';
                        var csv = campaign.csv_input.split(',');
                        var channels = csv[12].split('#');
                        var channels_name = channels.join();
                        var color;
                        if(campaign['mismatch_impression'] == 0) {
                            color = 'rgba(200, 247, 197, 0.5)' ;
                        }else if(campaign['mismatch_impression'] == 1) {
                            color = 'rgba(231, 76, 60, 0.5)' ;
                        }else {
                            color = 'rgba(245, 230, 83, 0.5)' ;
                        }
                        if (data.logged_in_user.profile_id == 3) {
                            ro_detail = ro_detail + '<td>';
                            if (campaign['derived_campaign_status'] == 'pending_approval') {
                                ro_detail = ro_detail + "<input type='checkbox' class='campaigns_to_cancel' value='" + campaign['campaign_id'] + "'>";
                            }
                            ro_detail = ro_detail + '</td>';
                        }
                        ro_detail = ro_detail + "<td  style='background-color:"+color+" '><div style='width:90px;word-wrap:break-word;'>" + campaign['campaign_name'] + "</div></td>" +
                            "                <td ><div style='width:90px;word-wrap:break-word;'>" + campaign['brand_new'] + "</div></td>" +
                            "                <td > <div style='width:67px;word-wrap:break-word;'>" + campaign['agency_name'] + "</div></td>" +
                            "                <td > <div style='width:77px;word-wrap:break-word;'>" + campaign['start_date'].split(' ')[0] + "</div></td>" +
                            "                <td > <div style='width:77px;word-wrap:break-word;'>" + campaign['end_date'].split(' ')[0] + "</div></td>" +
                            "                <td >  <div style='width:74px;word-wrap:break-word;'>" + campaign['caption_name'] + "</div></td>" +
                            "                <td > <div style='width:50px;word-wrap:break-word;'>" + (campaign['ro_duration']) + "</div></td>" +
                            "                <td > <div style='width:70px;word-wrap:break-word;'>" + channels_name + "</div></td>" +
                            "                <td > <div style='width:70px;word-wrap:break-word;'>" + campaign['scheduled_impression'] + "/" + campaign['booked_impression'] + " </div></td>" +
                            "                <td ><div style='width:73px;word-wrap:break-word;'>" + campaign['derived_campaign_status'] + "</div></td>" +
                            "                <td > <div style='width:93px;word-wrap:break-word;'>" + campaign['campaign_id'] + "</div></td>" +
                            "                <td > <div style='width:71px;word-wrap:break-word;'>" + campaign['sw_market_name'] + "</div> </td>" +
                            "            </tr>";

                    });

                    $('#campaign_tbody').html(ro_detail);
                }
            },
            error:function()
            {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                alert('Error in searching!!');
            }

        })
    })

    $(document).on('click','#scheduler_load_campaign_btn',function(){
        $.ajax(BASE_URL+'/ro_manager/campaigns_schedule',{
            type:'POST',
            data:{
                "order_id":$('#hid_internal_ro').val(),
                "edit":"0",
                "id":$('#hid_id').val(),

            },
            dataType:'json',
            beforeSend:function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            success:function(responsedata)
            {
                var data=responsedata.Data.jsonData;
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                $('#campaign_tbody').empty();
                var ro_detail='';
                if(data.campaigns.length>0) {
                    $.map(data.campaigns, function (campaign, index) {
                        ro_detail = ro_detail + '<tr>';
                        var csv = campaign.csv_input.split(',');
                        var channels = csv[12].split('#');
                        var channels_name = channels.join();
                        var color;
                        if(campaign['mismatch_impression'] == 0) {
                            color = 'rgba(200, 247, 197, 0.5)' ;
                        }else if(campaign['mismatch_impression'] == 1) {
                            color = 'rgba(231, 76, 60, 0.5)' ;
                        }else {
                            color = 'rgba(245, 230, 83, 0.5)' ;
                        }
                        if (data.logged_in_user.profile_id == 3) {
                            ro_detail = ro_detail + '<td>';
                            if (campaign['derived_campaign_status'] == 'pending_approval') {
                                ro_detail = ro_detail + "<input type='checkbox' class='campaigns_to_cancel' value='" + campaign['campaign_id'] + "'>";
                            }
                            ro_detail = ro_detail + '</td>';
                        }
                        ro_detail = ro_detail + "<td  style='background-color:"+color+" '><div style='width:90px;word-wrap:break-word;'>" + campaign['campaign_name'] + "</div></td>" +
                            "                <td ><div style='width:90px;word-wrap:break-word;'>" + campaign['brand_new'] + "</div></td>" +
                            "                <td > <div style='width:67px;word-wrap:break-word;'>" + campaign['agency_name'] + "</div></td>" +
                            "                <td > <div style='width:77px;word-wrap:break-word;'>" + campaign['start_date'].split(' ')[0] + "</div></td>" +
                            "                <td > <div style='width:77px;word-wrap:break-word;'>" + campaign['end_date'].split(' ')[0] + "</div></td>" +
                            "                <td >  <div style='width:74px;word-wrap:break-word;'>" + campaign['caption_name'] + "</div></td>" +
                            "                <td > <div style='width:50px;word-wrap:break-word;'>" + (campaign['ro_duration']) + "</div></td>" +
                            "                <td > <div style='width:70px;word-wrap:break-word;'>" + channels_name + "</div></td>" +
                            "                <td > <div style='width:70px;word-wrap:break-word;'>" + campaign['scheduled_impression'] + "/" + campaign['booked_impression'] + " </div></td>" +
                            "                <td ><div style='width:73px;word-wrap:break-word;'>" + campaign['derived_campaign_status'] + "</div></td>" +
                            "                <td > <div style='width:93px;word-wrap:break-word;'>" + campaign['campaign_id'] + "</div></td>" +
                            "                <td > <div style='width:71px;word-wrap:break-word;'>" + campaign['sw_market_name'] + "</div> </td>" +
                            "            </tr>";

                    });

                    $('#campaign_tbody').html(ro_detail);
                }
            },
            error:function()
            {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                alert('error in loading');
            }

        })
    });


    function show_hide_cancel_campaign()
    {
        if($("input:checked").length > 0){
            $("#scheduler_cancel_campaigns").show();
        }else{
            $("#scheduler_cancel_campaigns").hide();
        }
    }

    $('#scheduler_approval_request_span').click(function(){
        $.ajax(BASE_URL+'/ro_manager/approval_request/',{
            type:'POST',
            data:{
                "order_id":$('#hid_internal_ro').val(),
                "edit":"1",
                "id":$('#hid_id').val()
            },
            dataType:'json',
            beforeSend:function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            success:function(responsedata)
            {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                if(responsedata.Status == 'success') {
                    alert(responsedata.Message);
                    console.log("inside success");
                    window.location.href = BASE_URL + "/account_manager/home";
                    $('#scheduler_approval_request_span').css('display', 'none');
                } else {
                    alert('Something Went Wrong !!');
                }
            },
            error:function()
            {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
            }
        })
    })

    $('#scheduler_campaign_schedule_span').click(function(){
        $.ajax(BASE_URL+'/ro_manager/campaigns_schedule',{
            type:'POST',
            data:{
                "order_id":$('#hid_internal_ro').val(),
                "edit":"1",
                "id":$('#hid_id').val(),
                "search_by":'',
                "search_value":''
            },
            dataType:'json',
            beforeSend:function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            success:function(responsedata)
            {
                data=responsedata.Data.jsonData;
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                $('#option_modal_title').text('Campaign Wise Schedule Summary');
                $('#optionModal').modal('show');
                $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                console.log(data);
                var ro_detail="<form id='campaign_form'>" +
                    "<div class='container mycustomclass' style='background-color:#F0EEE9;height:46px;'>" +
                    "<div class='row'>" +
                    "<div class='col-6'><h4>Ro Detail</h4></div>" +
                    "<div class='col-6'>Search By:<select style='margin-right: 5px' name='search_by' id='search_by'>" +
                    "<option value=''>-</option>" +
                    "<option value='campaign_name'>Campaign Name</option>" +
                    "<option value='brand_new'>Brand Name</option>" +
                    "<option value='agency_name'>Agency Name</option>" +
                    "<option value='caption_name'>Caption Name</option>" +
                    "<option value='derived_campaign_status'>Campaign Status</option></select>" +
                    "<input type='text' style='margin-right: 5px' id='search_camp' placeholder='Enter text to Search.....'>" +
                    "<input type='button' class='btn btn-info' style='font-size: 11px' id='scheduler_search_campaign_btn' value='Search'> " +
                    "<input type='button' class='btn btn-info' style='font-size: 11px' id='scheduler_load_campaign_btn' value='Load'>" +
                    "</div></div>" +
                    "</div>" +
                    "</form>";
                ro_detail=ro_detail+"<div class='table_div' style='border: 1px solid lightgrey'>" +
                    "<table class='table'>" +
                    "<tr>" +
                    "<th>Customer RO Number</th>" +
                    "<th>Agency Name</th>" +
                    "<th>Advertise name</th>" +
                    "<th>Brand Name</th>" +
                    "<th>RO Start Date</th>" +
                    "<th>RO End Date</th>" +
                    " </tr>" +
                    "<tr>" +
                    "<td>"+data.am_ext_ro+"<br/>(Internal RO Number:"+data.internal_ro+")</td>" +
                    "<td>"+data.agency+"</td>" +
                    "<td>"+data.client+"</td>" +
                    "<td>"+data.brand+"</td>" +
                    "<td>"+data.camp_start_date+"</td>" +
                    "<td>"+data.camp_end_date+"</td>" +
                    "</tr>" +
                    "</table>" +
                    "</div>" +
                    "<br>";
                ro_detail=ro_detail+"<div id='overflow_div' style='border-top-left-radius:5px;border-top-right-radius:5px;'>" +
                    "    <input type='button' class='submitlong' id='scheduler_cancel_campaigns' style='display: none' value='Cancel Campaign'>";
                if(data.campaigns.length>0)
                {
                    
                    ro_detail=ro_detail+'<table class="table table-sm"  cellpadding="0" cellspacing="0"  style="font-size:11px;"><tr style="background-color: white">';
                    if(data.logged_in_user.profile_id==3)
                    {
                        ro_detail=ro_detail+'<th style="background-color:#F0EEE9;border-top-left-radius:5px;">&nbsp;</th>'
                    }
                    ro_detail=ro_detail+"<th style='background-color:#F0EEE9'>Campaign Name</th>" +
                        "                <th style='background-color:#F0EEE9'>Brand Name</th>" +
                        "                <th style='background-color:#F0EEE9'>Agency Name</th>" +
                        "                <th style='background-color:#F0EEE9'>Campaign Start Date</th>" +
                        "                <th style='background-color:#F0EEE9'>Campaign End Date</th>" +
                        "                <th style='background-color:#F0EEE9'>Caption Name</th>" +
                        "                <th style='background-color:#F0EEE9'>Caption Duration(Sec)</th>" +
                        "                <th style='background-color:#F0EEE9'>Channel Name</th>" +
                        "                <th style='background-color:#F0EEE9'>Scheduled / Booked</th>" +
                        "                <th style='background-color:#F0EEE9'>Campaign Status</th>" +
                        "                <th style='background-color:#F0EEE9'>Campaign ID</th>" +
                        "                <th style='background-color:#F0EEE9;border-top-right-radius:5px;'>Market</th>" +
                        "            </tr>" +
                        "            <tbody id='campaign_tbody'>";
                    $.map(data.campaigns,function(campaign,index){
                        var csv=campaign.csv_input.split(',');
                        var channels=csv[12].split('#');
                        var channels_name=channels.join();
                        var color;
                        if(campaign['mismatch_impression'] == 0) {
                            color = 'rgba(200, 247, 197, 0.5)' ;
                        }else if(campaign['mismatch_impression'] == 1) {
                            color = 'rgba(231, 76, 60, 0.5)' ;
                        }else {
                            color = 'rgba(245, 230, 83, 0.5)' ;
                        }
                        if(data.logged_in_user.profile_id==3)
                        {
                            ro_detail=ro_detail+'<td>';
                            if(campaign['derived_campaign_status'] == 'pending_approval')
                            {
                                ro_detail=ro_detail+"<input type='checkbox' onclick='show_hide_cancel_campaign()' class='campaigns_to_cancel' value='"+campaign['campaign_id']+"'>";
                            }
                            ro_detail=ro_detail+'</td>';
                        }
                        ro_detail=ro_detail+"<td style='background-color:"+color+" ' ><div style='width:90px;word-wrap:break-word;'>"+campaign['campaign_name']+"</div></td>" +
                            "                <td ><div style='width:90px;word-wrap:break-word;'>"+campaign['brand_new']+"</div></td>" +
                            "                <td > <div style='width:67px;word-wrap:break-word;'>"+campaign['agency_name']+"</div></td>" +
                            "                <td > <div style='width:77px;word-wrap:break-word;'>"+campaign['start_date'].split(' ')[0]+"</div></td>" +
                            "                <td > <div style='width:77px;word-wrap:break-word;'>"+campaign['end_date'].split(' ')[0] +"</div></td>" +
                            "                <td >  <div style='width:74px;word-wrap:break-word;'>"+campaign['caption_name']+"</div></td>" +
                            "                <td > <div style='width:50px;word-wrap:break-word;'>"+(campaign['ro_duration'])+"</div></td>" +
                            "                <td > <div style='width:70px;word-wrap:break-word;'>"+channels_name+"</div></td>" +
                            "                <td > <div style='width:70px;word-wrap:break-word;'>"+campaign['scheduled_impression']+"/"+ campaign['booked_impression']+" </div></td>" +
                            "                <td ><div style='width:73px;word-wrap:break-word;'>"+campaign['derived_campaign_status']+"</div></td>" +
                            "                <td > <div style='width:93px;word-wrap:break-word;'>"+campaign['campaign_id']+"</div></td>" +
                            "                <td > <div style='width:71px;word-wrap:break-word;'>"+campaign['sw_market_name']+"</div> </td>" +
                            "            </tr>";

                    });
                    ro_detail=ro_detail+'</tbody>';
                    $('#Option_Modal_body').html(ro_detail);
                    $('#overflow_div').addClass('overflow_div');
                }
                else if(data.search_value_set==1)
                {
                    ro_detail=ro_detail+"<p style='text-align:center;font-size:18px;'>No Result Found !</p>";
                    $('#Option_Modal_body').html(ro_detail);
                    $('#overflow_div').removeClass('overflow_div');
                }
                else
                {
                    ro_detail=ro_detail+'<p style="text-align:center;font-size:18px;">No Active Campaign !</p>';
                    $('#Option_Modal_body').html(ro_detail);
                    $('#overflow_div').removeClass('overflow_div');
                }

                
            },
            error:function(data)
            {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                alert("could not load");
            }

        })
    })



function addPrice(order_id,network_str) {
					
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_price/'+order_id+'/'+network_str,iframe:true, width: '530px', height:'820px'}); 
}
function editPrice(order_id,network_str) {
					
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_price/'+order_id+'/'+network_str,iframe:true, width: '530px', height:'820px'}); 
}
function approve_network(order_id,network_id) {
					
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/approve_network/'+order_id+'/'+network_id,iframe:true, width: '930px', height:'820px'}); 
}
function NetworkRO(order_id,network_id){
	window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/NetworkRO/" +order_id +'/'+network_id ;
}

function mail_networkropdf(order_id,network_id){
	window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/mail_networkropdf/" +order_id +'/'+network_id ;
}
function campaigns_schedule(order_id,edit,id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/campaigns_schedule/" + order_id + "/" + edit + "/" + id;
}
function channels_schedule(order_id,edit,id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/channels_schedule/" + order_id+ "/" + edit + "/" + id;
}
function nw_ro_payment(order_id,edit,id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/nw_ro_payment/'+order_id+ "/" + edit + "/" + id,iframe:true, width: '900px', height:'900px'});
	//window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/nw_ro_payment/" + order_id;
}
function ro_schedule(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/ro_schedule/" + order_id;
}
function add_price_approve(order_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/add_price_approve/" + order_id ;
}
function add_ro_amount(external_ro) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_ro_amount/'+external_ro,iframe:true, width: '540px', height:'250px'}); 
}
function find_price(row_id,total_rows,network_id) {

//	alert(total_rows);
	var total_amount = 0;
	for(var i = 0;i<total_rows;i++)
	{
//		alert(i);
	//	if(i == row_id) {
		var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
		var total_ad_seconds = document.getElementById(total_ad_sec_id).innerHTML;
		var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
		var temp_channel_avg_rate = document.getElementById(channel_avg_rate_id).value;
		/* for bug: 426 code change starts here */
		if(temp_channel_avg_rate === "" || isNaN(temp_channel_avg_rate) || temp_channel_avg_rate < 0)
		{
			alert(" Please enter a positive numerical value for channel Last Rate");
		/* for bug: 426 code change done here*/
		} else 
		{
		var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
		var temp_amount = temp_channel_avg_rate * total_ad_seconds/10;
                document.getElementById(amount_id).value = temp_amount.toFixed(2);
                total_amount += parseFloat(temp_amount.toFixed(2),10);
		}
	}
	var total_amount_id = 'network_'+network_id+'_total_amount';
	document.getElementById(total_amount_id).innerHTML = total_amount.toFixed(2);
	var network_share_id = 'network_'+network_id+'_network_share';
	var network_share = document.getElementById(network_share_id).value;
	var final_amount_id = 'network_'+network_id+'_final_amount';
	var final_amount = total_amount.toFixed(2) * network_share/100;
	document.getElementById(final_amount_id).value = final_amount.toFixed(2);
}
function find_rate(row_id,total_rows,network_id) {
	
	var total_amount = 0;
	for(var i = 0;i<total_rows;i++) 
	{
			if(i == row_id) {
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
			var amount = document.getElementById(amount_id).value;
			if(amount === "" || isNaN(amount) || amount < 0)
     			{       
	/*
  				alert("please enter Channel Amount");
  //				return false;
  			} else if(isNaN(amount))
  			{ 
    				alert("please enter numerical value as a  Channel Amount");
//				return false;
  			}else if(amount < 0) {
				alert("Please enter positive channel Amount");
//				return false;
			}
	//		alert(amount);
*/			
				alert("Please enter a positive numerical value for channel Amount");
			}else {
			var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
                //      alert(total_ad_sec_id);
                        var total_ad_seconds = document.getElementById(total_ad_sec_id).innerHTML;
			var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
//                      alert(channel_avg_rate_id);
                        var channel_avg_rate = amount/total_ad_seconds * 10;
			document.getElementById(channel_avg_rate_id).value = channel_avg_rate.toFixed(2);
			total_amount += parseFloat(amount,10);
			} }  else { 
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
                        var amount = document.getElementById(amount_id).value;
			total_amount += parseFloat(amount,10);
			}
	}
	 var total_amount_id = 'network_'+network_id+'_total_amount';
        document.getElementById(total_amount_id).innerHTML = total_amount.toFixed(2);
        var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = document.getElementById(network_share_id).value;
        var final_amount_id = 'network_'+network_id+'_final_amount';
        var final_amount = total_amount.toFixed(2) * network_share/100;
        document.getElementById(final_amount_id).value = final_amount.toFixed(2);
}
function find_final_amount(network_id,total_networks) {
	
	var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = document.getElementById(network_share_id).value;
//	alert(network_share);
	if(network_share==="" || isNaN(network_share) || network_share >100 || network_share < 0)
	{       
/*
		alert("pplease enter Network Share");
//		return false;
	} else if(isNaN(network_share))
	{ 
    		alert("please enter a numerical number as a share");
//		return false;
	} else if(network_share >100) {
    		alert("Please enter Network share less than or equal to 100");
//		return false;
	} else if(network_share < 0) {
    		alert("Please enter Network share greater than or equal to 0");
//		return false;
	}
*/
		alert("Please enter Network share greater than or equal to 0 and less than or equal to 100");
	} else {
	var total_amount_id = 'network_'+network_id+'_total_amount';
	var total_amount =  document.getElementById(total_amount_id).innerHTML;
//	alert(total_amount);
	var final_amount_id = 'network_'+network_id+'_final_amount';
        var final_amount = total_amount * network_share/100;
        document.getElementById(final_amount_id).value = final_amount.toFixed(2);
	}
}
/*-------------------- for bug 423 code changes starts here------------------*/
function find_channel_amounts(network_id,total_rows){

	var final_amount_id = 'network_'+network_id+'_final_amount';	
 	var final_amount = parseFloat(document.getElementById(final_amount_id).value);
	var total_amount_id = 'network_'+network_id+'_total_amount';
        var total_amount_old = parseFloat(document.getElementById(total_amount_id).innerHTML);
        var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = parseFloat(document.getElementById(network_share_id).value);
	/*------------------- For Bug 427 to make sure values not struck at character values -----------------*/
	if(final_amount==="" || isNaN(final_amount) || final_amount < 0)
       	{       
		alert("Please enter a positive numerical value for Network Payout(Net)");
	/* for bug: 427 code changes done here */
	} else {
		var total_amount_new = final_amount * 100/network_share;
		document.getElementById(total_amount_id).innerHTML = total_amount_new.toFixed(2);
		for(var i = 0;i<total_rows;i++)
		{
			var total_amount_id = 'network_'+network_id+'_total_amount';
		        var total_amount =  parseFloat(document.getElementById(total_amount_id).innerHTML);
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
                        var amount_old = parseFloat(document.getElementById(amount_id).value);
			var amount_new = total_amount * amount_old/total_amount_old;
			//alert('total amount:'+total_amount+'channel amount old:'+amount_old+'tot amount old:'+total_amount_old+'amount new:'+amount_new);
			document.getElementById(amount_id).value = amount_new.toFixed(2);
			var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
                //      alert(total_ad_sec_id);
                        var total_ad_seconds = parseFloat(document.getElementById(total_ad_sec_id).innerHTML);
			var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
//                      alert(channel_avg_rate_id);
			var amount = amount_new.toFixed(2);
                        var channel_avg_rate = amount/total_ad_seconds * 10;
                        document.getElementById(channel_avg_rate_id).value = channel_avg_rate.toFixed(2);
		}
	}
}
/* for bug:423 code changes done ehre */
function find_total_payout(total_networks) {
	
	var total_payout = 0;
	for(var i = 0;i<total_networks; i++) 
	{
		var final_amount_id = 'network_'+i+'_final_amount';
	        var final_amount = document.getElementById(final_amount_id).value;
		total_payout += parseFloat(final_amount,10);
	}
	net_payout = total_payout.toFixed(2);
	document.getElementById('total_network_payout').innerHTML =  net_payout;
	document.getElementById('total_network_payout1').innerHTML =  net_payout;
	var ro_amount = document.getElementById('net_amount').innerHTML;
	var ro_amount1 =  document.getElementById('net_amount1').innerHTML;
	 var surewaves_share1 = ro_amount1 - net_payout;
	var surewaves_share = ro_amount - net_payout;
	//changed by mani : 3rd august 2013
	var agency_commission_amount = document.getElementById('agency_amount').innerHTML;
	var agency_commission_amount1 =  document.getElementById('agency_amount1').innerHTML;
	
	var net_revenue = ro_amount - agency_commission_amount ;net_revenue = net_revenue * $("#service_tax_value").val();
	var net_revenue1 = ro_amount1 - agency_commission_amount1 ;	net_revenue1 = net_revenue1* $("#service_tax_value").val();
	var net_revenue_from_innerhtml = document.getElementById('net_revenue').innerHTML;
	//var surewaves_share_per = surewaves_share/ro_amount*100;
	//var surewaves_share_per1 = surewaves_share1/ro_amount1*100;
	var surewaves_share_per = surewaves_share/net_revenue_from_innerhtml*100;
	var surewaves_share_per1 = surewaves_share1/net_revenue_from_innerhtml*100;
	
	document.getElementById('surewaves_share').innerHTML = surewaves_share.toFixed(2);
	document.getElementById('surewaves_share_per').innerHTML = surewaves_share_per.toFixed(2);
	document.getElementById('surewaves_share1').innerHTML = surewaves_share1.toFixed(2);
        document.getElementById('surewaves_share_per1').innerHTML = surewaves_share_per1.toFixed(2);
}
function show_channels(order_id) { 

	$.colorbox({href:"<?php echo ROOT_FOLDER ?>/ro_manager/show_channels/" + order_id,iframe:true,width:'515px',height:'374px'});
}
function approval_request(order_id,edit,id) {

	 window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approval_request/" + order_id + "/" + edit + "/" + id;
}

function add_ro_amount(external_ro) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_ro_amount/'+external_ro,iframe:true, width: '520px', height:'410px'}); 
}
function add_other_expenses(external_ro,order_id,edit,id) {
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_other_expenses/'+external_ro+'/'+ order_id + "/" + edit + "/" + id,iframe:true, width: '520px', height:'700px'});
}
function add_file_location(internal_ro,edit,id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_file_location/'+internal_ro+ "/" + edit + "/" + id,iframe:true, width: '530px', height:'400px'}); 
}
$(function() {

                $(".submitlong").click(function() {
                    var isValid = true;
			var ro_amount = document.getElementById('ro_amount').innerHTML;
			var value = document.getElementById('agency_rebate').innerHTML;
			if(ro_amount === 'In Process') {
				alert("Please Enter Gross CRO Amount details to approve RO");
				isValid = false;
			}
			 
			if(value === 'Missing') {
				alert("Please Enter Other Expenses details to approve RO");
				isValid = false;
			}
                    $("#myForm input[type=text]").each(function() {

                        if(isNaN(this.value) || (this.value < 0)|| (this.value ==='')) {
                            $(this).css('background', 'red');
                            isValid = false;
                        } else {
                            $(this).css('background', 'green');
                        }
                    });
                    if(isValid === false) {
                        return false;
                    } else {
			//Do everything you need to do with the form
                    }
                    
                });

            });
	function confirm_add_price_approve(id,edit,am_ro_id) {
		$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/confirm_add_price_approve/'+id + "/" + edit + "/" + am_ro_id,iframe:true, width: '520px', height:'540px'});
	}
	function cancel_ro(id,cust_ro,edit,internal_ro) {
		$.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/cancel_ro/'+id+'/'+cust_ro+ "/" + edit + "/" + internal_ro,iframe:true, width: '800px', height:'350px'});
	}
	function cancel_ro_admin(id,cust_ro,edit,internal_ro) {
		$.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/cancel_ro_admin/'+id+'/'+cust_ro+ "/" + edit + "/" + internal_ro,iframe:true, width: '800px', height:'350px'});
	}
	$(document).ready(function() {
		<?php 
			$selected_order_id = $this->session->userdata('selected_order_id');
			if ( isset($selected_order_id) && !empty($selected_order_id) ) { 
		?>
			confirm_add_price_approve('<?php echo $selected_order_id ?>','<?php echo $edit ?>','<?php echo $id ?>');
		<?php 
			$this->session->unset_userdata("selected_order_id");
			} 
		?>
	});	
/*function edit_ext_ro(id) {
	$.colorbox({href:'<?php //echo ROOT_FOLDER ?>/account_manager/edit_ext_ro/'+id,iframe:true, width: '940px', height:'650px'});
}*/
function am_edit_ext_ro(id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/am_edit_ext_ro/'+id,iframe:true, width: '940px', height:'650px'});
}
function cancel_channel(channel_id,order_id,edit,am_ro_id,chnl_name){
	/*if(confirm("Are you sure !")){
		window.location = '<?php echo ROOT_FOLDER ?>/ro_manager/cancel_channel_from_ro/'+channel_id+'/'+ order_id + "/" + edit + "/" + am_ro_id;
	}*/
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/confirm_cancel_channel/'+channel_id + "/" + order_id + "/" + edit + "/" + am_ro_id + "/" + chnl_name,iframe:true, width: '520px', height:'300px'});
}
function edit_network(internal_ro_number,customer_id,edit,id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/edit_network/'+internal_ro_number+'/'+customer_id+'/'+edit+'/'+id,iframe:true, width: '820px', height:'600px'});
}
function cancel_nw_channel(internal_ro_number,customer_id,edit,id) {
	$.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/cancel_nw_channel/'+internal_ro_number+'/'+customer_id+'/'+edit+'/'+id,iframe:true, width: '520px', height:'500px'});
}

</script>
<script type="text/javascript">

    $('#view_more').click(function(){
        var div = $('#recs');
        var start= div.children('table').length ;
        //var start= div.children().length/2 ;
        var nw_loaded = $("#nw_load_status").val();
        var end_of_nw = 0;
        if(!div.hasClass('ended')) {
            $.ajax({
                type : 'GET',
                url : '<?php echo ROOT_FOLDER ?>/ro_manager/approval_pagination/<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['internal_ro']),'=') ?>/<?php echo $edit; ?>/<?php echo $id; ?>/'+start ,
                //data : 'lastelement='+start,
                beforeSend:function() {
                    //$('.getmore').html('Loading ......');
                },
                success:function(data){//alert(data)
                    if(data == ''){
                        end_of_nw = 1;
                        $('#view_more').text('No more Networks');
                        $('#view_all').text('No more Networks');
                    }else{
                        div.append(data);
                    }
                    /*data_with_net_payout_loaded = data_with_net_payout_loaded.split('~');
                    data = data_with_net_payout_loaded[0];
                    if(data_with_net_payout_loaded[1] != ''){
                        net_payout_loaded_ajax = parseFloat(document.getElementById("net_payout_loaded").innerHTML);
                        document.getElementById("net_payout_loaded").innerHTML = net_payout_loaded_ajax + parseFloat(data_with_net_payout_loaded[1]);
                    }
                    if(data !== 'end') {

                    }else {
                        if(!div.hasClass('ended')){
                            alert("no result found") ;
                            div.addClass('ended') ;
                        }
                    }*/

                },
                complete:function(){
                    $("#nw_load_status").val(start);

                    //Calling again to load all networks
                    if($("#load_all_nw").val() == 1 && end_of_nw != 1){
                        $("#view_more").trigger("click");
                    }

                }
            });
        }
    });

    $("#view_all").click(function(){
        /*var div = $('#recs');
        var start= div.children('table').length ;
        var start= div.children().length/2 ;
        var nw_loaded = $("#nw_load_status").val();*/

        $("#load_all_nw").val(1);
        $("#view_more").trigger("click");
    });

    $(window).scroll(function(){

        //for handling scroll to top button
        if ($(this).scrollTop() > 100) {
            $('.go-to-top').fadeIn();
        } else {
            $('.go-to-top').fadeOut();
        }
    });


    $('.go-to-top').click(function() {
        $("html, body").animate({
            scrollTop: 0
        }, 1500);
        return false;
    });

</script>
