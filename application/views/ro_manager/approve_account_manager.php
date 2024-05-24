<html>
<head>
    <script language="javascript">

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

            console.log("inside cust_ro "+cust_ro);
            $.ajax(BASE_URL+'/account_manager/cancel_ro',{
               type:'POST',
               data:{
                   "id":id,
                   "cust_ro":cust_ro,
                   "edit":edit,
                   "internal_ro":internal_ro
               },
                dataType:'json',
                beforeSend:function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success:function(responsedata){
                   console.log('Inside success');
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#date_modal_title').text("Request for Cancellation");
                    $('#date_Modal_body').html('');
                    $('#dateModal').modal('show');
                    $("#dateModal").data('bs.modal')._config.backdrop = 'static';
                     $('#date_Modal_body').html(responsedata.Data.html);
                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                }

            });

            //$.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/cancel_ro/'+id+'/'+cust_ro+ "/" + edit + "/" + internal_ro,iframe:true, width: '800px', height:'350px'});
        }
        function cancel_ro_admin(id,cust_ro,edit,internal_ro) {
            $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/cancel_ro_admin/'+id+'/'+cust_ro+ "/" + edit + "/" + internal_ro,iframe:true, width: '800px', height:'350px'});
        }
        function cancel_markets(id,cust_ro,edit,internal_ro){
            $.ajax(BASE_URL+'/account_manager/cancel_markets_for_ro_new',{
                type:'POST',
                data:{
                    "id":id,
                    "cust_ro":cust_ro,
                    "edit":edit,
                    "internal_ro":internal_ro
                },
                dataType:'json',
                beforeSend:function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success:function(data){
                    console.log(data);
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#option_modal_title').text("Cancel Market");
                    $('#Option_Modal_body').html('');
                    $('#optionModal').modal('show');
                    $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                    $('#Option_Modal_body').html(data.Data.html);
                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                }

            });
            //window.location.href = "<?php echo ROOT_FOLDER ?>/account_manager/cancel_markets_for_ro_new/" +id+"/"+cust_ro+ "/" + edit + "/" + internal_ro;
        }
        function cancel_markets_by_content(id,cust_ro,edit,internal_ro){
            $.ajax(BASE_URL+'/account_manager/cancel_markets_by_contents',{
                type:'POST',
                data:{
                    "id":id,
                    "cust_ro":cust_ro,
                    "edit":edit,
                    "internal_ro":internal_ro
                },
                dataType:'json',
                beforeSend:function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success:function(data){
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#option_modal_title').text("Cancel Market by Content");
                    $('#Option_Modal_body').html('');
                    $('#optionModal').modal('show');
                    $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                    $('#Option_Modal_body').html(data.Data.html);
                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                }

            });
            //window.location.href = "<?php echo ROOT_FOLDER ?>/account_manager/cancel_markets_by_contents/" +id+"/"+cust_ro+ "/" + edit + "/" + internal_ro;
        }
        function cancel_markets_by_brand(id,cust_ro,edit,internal_ro){
            $.ajax(BASE_URL+'/account_manager/cancel_market_by_brands',{
                type:'POST',
                data:{
                    "id":id,
                    "cust_ro":cust_ro,
                    "edit":edit,
                    "internal_ro":internal_ro
                },
                dataType:'json',
                beforeSend:function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success:function(data){
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#option_modal_title').text("Cancel Market by Brands");
                    $('#Option_Modal_body').html('');
                    $('#optionModal').modal('show');
                    $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                    $('#Option_Modal_body').html(data.Data.html);
                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                }

            });
            //window.location.href = "<?php echo ROOT_FOLDER ?>/account_manager/cancel_market_by_brands/" +id+"/"+cust_ro+ "/" + edit + "/" + internal_ro;
        }
        function show_cancel_reasons(id){
            $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/cancel_market_reasons/'+id,iframe:true, width: '800', height:'350px'});
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
            
            console.log("id "+id);
            $.ajax(BASE_URL+"/account_manager/am_edit_ext_ro/",{
                type:'POST',
                data:{
                    "id":id,
                },
                dataType:'json',
                beforeSend:function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success:function(data){
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#option_modal_title').text("Edit Ro");
                    $('#Option_Modal_body').html('');
                    $('#optionModal').modal('show');
                    $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                    $('#Option_Modal_body').html(data.Data.html);
                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert('could not load');
                }


            })

           // $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/am_edit_ext_ro/'+id,iframe:true, width: '940px', height:'650px'});
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


</head>
<body>
<div  style="height:700px">

    <div class="wrapper">		<!-- wrapper begins -->


        <div class="block">




            <?php if(count($scheduled_data) == 0) { ?>


                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2>Customer Release Order</h2>



                </div>
                <div class="block_content">

                    <table  width="100%" cellspacing="0" cellpadding="0">
                        <tbody>

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

                        </tr>

                        <tr>
                            <td>
                                <?php if( (($submit_ext_ro_data[0]['user_id'] == $logged_in_user['user_id']) ) && $ro_status_entry=='RO Created') {?>
                                    <span style='color:blue;cursor:pointer;' id='view_detail_span' onclick='am_edit_ext_ro("<?php echo $id?>")'>view details</span><br/><?php echo $submit_ext_ro_data[0]['cust_ro']; echo '<br/>(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ;?>
                                <?php }else {
                                    echo $submit_ext_ro_data[0]['cust_ro']; echo '<br/>(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ;
                                
                               } ?>
                            
                            </td>
                            <td><?php echo $submit_ext_ro_data[0]['submitted_by'] ?> &nbsp;</td>
                            <td><?php echo $submit_ext_ro_data[0]['approved_by'] ?> &nbsp;</td>

                            <td><?php echo $submit_ext_ro_data[0]['agency'] ?> &nbsp;</td>
                            <td><?php echo $submit_ext_ro_data[0]['client'] ?></td>
                            <td><?php echo $submit_ext_ro_data[0]['brand_name'] ?></td>
                            <td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_start_date'])); ?> </td>
                            <td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_end_date'])); ?> </td>
                            <td id="status_td"><?php echo $ro_status_entry ; ?> </td>

                        </tr>
                        <tr>

                            <?php
                            if($is_ro_completed == 0){
                                if($submit_ext_ro_data[0]['user_id'] == $logged_in_user['user_id'] && $ro_ready_for_schedule_approval == 1) {
                                    if($cancellationRequestSent == 0) {
                                        if($is_cancel_market_requested == 0){
                                            if($cancel_request_status == 0) { ?>
                                                <td style="border-bottom: none">
                                                    <span style="color:#F00;">Cancel RO Request Sent </span>
                                                </td>
                                            <?php } else if ($cancel_request_status == 1) { ?>
                                                <td style="border-bottom: none">
                                                    <span style="color:#F00;">RO Cancelled </span>
                                                </td>
                                            <?php } else if ($cancel_request_status == 2) { ?>
                                                <td style="border-bottom: none">
                                                    <a href="javascript:cancel_ro('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>')" style="color:#F00;">
                                                        Resend Cancel RO Request
                                                    </a>
                                                </td>
                                            <?php }else{ ?>
                                                <td style="border-bottom: none">
                                                    <a href="javascript:cancel_ro('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro']?>')" style="color:#F00;">
                                                        <span id="cancel_ro_request_span">Cancel RO Request</span>
                                                    </a>
                                                </td>
                                            <?php }
                                        }?>


                                        <?php if($is_ro_approved == 1) { ?>

                                            <td style="border-bottom: none">
                                            <?php if($cancel_request_status != 1) {?>
                                                <!--[BugFix:801]User must be able to cancel market multiple times -->
                                                <a href="javascript:cancel_markets('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>')" style="color:#F00;">
                                                    <span id="cancel_markets_span">Cancel Markets</span>
                                                </a>

                                                </td>

                                                <td style="border-bottom: none">
                                                    <a href="javascript:cancel_markets_by_content('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>')" style="color:#F00;">
                                                        <span id="cancel_markets_by_content_span">Cancel Markets by Content</span>
                                                    </a>
                                                </td>

                                                <td style="border-bottom: none">
                                                <a href="javascript:cancel_markets_by_brand('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>')" style="color:#F00;">
                                                    <span id="cancel_markets_by_brand_span">Cancel Markets by Brand</span>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if ($cancel_request_status_mkt == 2) { ?>
                                            <a href="javascript:show_cancel_reasons('<?php echo $id ?>')"></a>
                                        <?php }

                                    }//Cancellation Request Sent
                                }
                            } else { ?>
                                </td>
                                <td style="border-bottom: none">
                                    <span style="color:#F00;"> Ro Completed </span>
                                </td>
                            <?php } ?>

                            <!-- <td>
                                        <a href="javascript:cancel_ro('<?php echo $id ?>','<?php echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>')" style="color:#F00;">
                                        Cancel RO Request
                                        </a>
                                    </td> -->

                            <?php /*if($submit_ext_ro_data[0]['user_id'] == $logged_in_user['user_id']){*/?><!--
                                        <td>
                                        <a href="<?php /*echo ROOT_FOLDER */?>/account_manager/invoice_collection/<?php /*echo  rtrim(base64_encode($submit_ext_ro_data[0]['cust_ro']),'=') */?>">Invoice</a>
                                        </td>
                                    --><?php /*} */?>


                        </tr>

                        </tbody></table><br>
                    <div id="cancellation_div" style="display:none;text-align: center">
                        <span style='color:red;'>Cancellation Request is Pending !! </span>
                    </div>

                    <div class="paggination right">
                    </div>		<!-- .paggination ends -->
                </div>

                <div class="block_content">
                    <h2 style="text-align:center;">No Active Campaign !</h2>
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>
                </div>

            <?php } else {

                ?>

                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2>Customer Release Order</h2>



                </div>
                <div class="block_content">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tbody><tr>
                            <th>Customer RO Number</th>
                            <th>Submitted By</th>
                            <th>Approved By</th>
                            <th>Agency Name</th>
                            <th>Advertiser name</th>
                            <th>Brand Name</th>
                            <th>RO Start Date</th>
                            <th>RO End Date</th>
                            <th>RO Status</th>
                            <th>&nbsp;</th>
                        </tr>

                        <tr>
                            <td>
                                <?php if($submit_ext_ro_data[0]['user_id'] == $logged_in_user['user_id'] ) {?>
                                   <?php echo $submit_ext_ro_data[0]['cust_ro']; echo '<br/>(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ;?>
                                <?php } else{ ?>
                                    <?php echo $submit_ext_ro_data[0]['cust_ro']; echo '<br/>(Internal RO Number:'.$submit_ext_ro_data[0]['internal_ro'].")" ; ?>
                                <?php }  ?>
                            </td>
                            <td><?php echo $submit_ext_ro_data[0]['submitted_by'] ?> &nbsp;</td>
                            <td><?php echo $submit_ext_ro_data[0]['approved_by'] ?> &nbsp;</td>
                            <td><?php echo $submit_ext_ro_data[0]['agency'] ?> &nbsp;</td>
                            <td><?php echo $submit_ext_ro_data[0]['client'] ?></td>
                            <td><?php echo $submit_ext_ro_data[0]['brand_name'] ?></td>
                            <td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_start_date'])); ?> </td>
                            <td><?php print date('d-M-Y', strtotime($submit_ext_ro_data[0]['camp_end_date'])); ?> </td>
                            <td id="status_td"><?php echo $ro_status_entry ; ?> </td>

                        </tr>
                        <tr>
                            <?php
                            if($is_ro_completed == 0){
                                if($submit_ext_ro_data[0]['user_id'] == $logged_in_user['user_id'] && $ro_ready_for_schedule_approval == 1) {
                                    if($cancellationRequestSent == 0) {
                                        if($is_cancel_market_requested == 0){
                                            if($cancel_request_status == 0) { ?>
                                                <td style="border-bottom: none">
                                                    <span style="color:#F00;">Cancel RO Request Sent </span>
                                                </td>
                                            <?php } else if ($cancel_request_status == 1) { ?>
                                                <td style="border-bottom: none">
                                                    <span style="color:#F00;">RO Cancelled </span>
                                                </td>
                                            <?php } else if ($cancel_request_status == 2) { ?>
                                                <td style="border-bottom: none">
                                                    <a href="javascript:cancel_ro('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>')" style="color:#F00;">
                                                        Resend Cancel RO Request
                                                    </a>
                                                </td>
                                            <?php }else{ ?>
                                                <td style="border-bottom: none">
                                                    <a href="javascript:cancel_ro('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>')" style="color:#F00;">
                                                        <span id="cancel_ro_request_span">Cancel RO Request</span>
                                                    </a>
                                                </td>
                                            <?php } }?>



                                        <?php if($is_ro_approved == 1) { ?>

                                            <td style="border-bottom: none">
                                            <?php if($cancel_request_status != 1) { ?>
                                                <a href="javascript:cancel_markets('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro']?>')" style="color:#F00;">
                                                    <span id="cancel_markets_span">Cancel Markets</span>
                                                </a>
                                                </td>

                                                <td style="border-bottom: none">
                                                    <a href="javascript:cancel_markets_by_content('<?php echo $id ?>','<?php echo  $submit_ext_ro_data[0]['cust_ro'] ?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro'] ?>')" style="color:#F00;">
                                                        <span id="cancel_markets_by_content_span">Cancel Markets by Content</span>
                                                    </a>
                                                </td>

                                                <td style="border-bottom: none">
                                                <a href="javascript:cancel_markets_by_brand('<?php echo $id ?>','<?php echo $submit_ext_ro_data[0]['cust_ro']?>','<?php echo $edit ?>','<?php echo $submit_ext_ro_data[0]['internal_ro']?>')" style="color:#F00;">
                                                    <span id="cancel_markets_by_brand_span">Cancel Markets by Brand</span>
                                                </a>

                                            <?php } ?>
                                        <?php } ?>

                                        <?php if ($cancel_request_status_mkt == 2) { ?>
                                            <a href="javascript:show_cancel_reasons('<?php echo $id ?>')"></a>
                                        <?php }
                                    }
                                }
                            } else { ?>
                                </td>


                                <td style="border-bottom: none">
                                    <span style="color:#F00;"> Ro Completed </span>
                                </td>
                            <?php } ?>


                        </tr>

                        </tbody></table><br>
                        <div id="cancellation_div" style="display:none;text-align: center">
                            <span style='color:red;'>Cancellation Request is Pending !! </span>
                        </div>
                    <?php if($cancellationRequestSent == 1){ ?>
                        <div id="cancellation_request_div" style="text-align: center">
                            <span style='color:red;'>Cancellation Request is Pending !! </span>
                        </div>
                    <?php } ?>

                    <div class="paggination right">
                    </div>		<!-- .paggination ends -->
                </div>




                <div class="block">
                    <div style="margin-top:10px" class="block_head">
                        <div class="bheadl"></div>
                        <div class="bheadr"></div>
                        <h2>Network RO Summary</h2>

                    </div>

                    <div class="block_content">
                        <div id="recs">
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
                            $count = 0 ;
                            foreach($scheduled_data as $key => $dat) {
                            $total_networks = count($scheduled_data);
                            $row_id = 0;
                            ?>
                            <table id ="<?php echo 'network_'.$key ?>" cellpadding="0" cellspacing="0" width="100%">
                                <tr cellpadding="0" cellspacing="0" width="100%" >
                                    <td colspan="4" class="network_name_style" style="font-weight:bold; size:20px; width:400px;">Network Name: <?php echo $dat['network_name'] ?> &nbsp; (<?php echo $dat['market_name'] ?>)</td>
                                </tr>

                                <?php
                                $total_amount = 0;
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

                                    <?php //foreach($ch['ad_types'] as $types => $ad) {
                                    if($ch['total_spot_ad_seconds'] != 0){
                                        $amount = 0;
                                        $rate = $ch['channel_spot_avg_rate'];
                                        $amount += $ch['total_spot_ad_seconds'] * $rate/10;
                                        $total_amount += $amount;
                                        ?>
                                        <tr cellpadding="0" cellspacing="0" width="100%">
                                            <td colspan="3">Spot Ad</td>
                                            <td colspan="3" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0'; ?>" ><?php echo $ch['total_spot_ad_seconds'] ?></td>

                                        </tr>
                                    <?php }
                                    if($ch['total_banner_ad_seconds'] != 0){
                                        $amount = 0;
                                        $rate = $ch['channel_banner_avg_rate'];
                                        $amount += $ch['total_banner_ad_seconds'] * $rate/10;
                                        $total_amount += $amount;
                                        ?>
                                        <tr cellpadding="0" cellspacing="0" width="100%">
                                            <td colspan="3">Banner Ad</td>
                                            <td colspan="3" id="<?php echo 'network_'.$key.'_total_sec_'.$row_id.'_0'; ?>" ><?php echo $ch['total_banner_ad_seconds'] ?></td>

                                        </tr>
                                    <?php }
                                    ?>
                                <?php }  ?>
                                <?php
                                $count++ ;
                                $lastCount = $count ;
                                }
                                ?>
                            </table>
                        </div>



                    </div>
                    <div class="bendl"></div>
                    <div class="bendr"></div>
                </div>
            <?php }  ?>
            <input type="hidden" id="service_tax_value" value="<?php echo SERVICE_TAX ?>"/>
            <input type="hidden" name="nw_load_status" id="nw_load_status" value="0">
            <input type="hidden" name="load_all_nw" id="load_all_nw" value="0">


</body>
</html>
