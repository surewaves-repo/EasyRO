<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/flexigrid.pack.css" />
<script type="text/javascript" src="/surewaves_easy_ro/js/flexigrid.js"></script>
<link rel="stylesheet" href="/surewaves_easy_ro/css/bootstrapstyle.css">
<style>
    body{
        font-size: 14px;
    }
</style>
<div id="hld">
     
    <div class="wrapper">		<!-- wrapper begins -->
        <!--------------------------------------Modal------------------------------------------------->


        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>


            <?php echo $menu; ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->
        <div class="modal fade"  id="myModal">
            <div class="modal-dialog modal-xl modal-lg" >
                <div class="modal-content"  >

                    <!-- Modal Header -->
                    <div class="modal-header" id="home_modal_header">
                        <h5 id="modal_title" class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body" id="Modal_body">

                    </div>
                </div>
            </div>
        </div> <!--End Of Modal-->

        <div class="modal fade" id="confirmModal">
            <div class="modal-dialog modal-lg" >
                <div class="modal-content"  >

                    <!-- Modal Header -->
                    <div class="modal-header" id="home_modal_header">
                        <h5  class="modal-title">Confirm Add Price</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body" id="confirm_Modal_body">

                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade"  id="optionModal">
            <div class="modal-dialog modal-xl modal-lg" >
                <div class="modal-content"  >

                    <!-- Modal Header -->
                    <div class="modal-header" id="home_modal_header">
                        <h5 id="option_modal_title" class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body" id="Option_Modal_body">

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade"  id="dateModal">
            <div class="modal-dialog modal-lg" >
                <div class="modal-content"  >

                    <!-- Modal Header -->
                    <div class="modal-header" id="home_modal_header">
                        <h5 id="date_modal_title" class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body" id="date_Modal_body">
                        <table class="table">
                            <tr>
                                <th style="display:none;"></th>
                                <th style="display:none;"></th>
                            </tr>
                            <tr>
                                <td>Ro Date</td>
                                <td><input type="text" readonly id="date_ro_date"></td>
                            </tr>
                            <tr>
                                <td>Campaign Start Date<span style="color:red">*</span></td>
                                <td><input type="text" id="date_camp_start_date">
                                    <span class="fa fa-calendar"></span></td>
                            </tr>

                            <tr>
                                <td>Campaign End Date<span style="color:red">*</span></td>
                                <td><input type="text" id="date_camp_end_date">
                                    <span class="fa fa-calendar"></span></td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="button" id="dates_submit" class="btn btn-outline-dark"  value="Submit"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <div class="loader_overlay" id="loader_background" style="display:none;"></div>
        <div class="page_loader_mini" id="loader_spin" style="display:none;" ></div>

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>Pending Requests</h2>

            </div>		<!-- .block_head ends -->
            <div class="block_content">
            <?php if(count($pending_requests) > 0) {?>
                <table cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <th style="width: 25%">Customer RO Number</th>
                        <th style="width: 10%">RO Type</th>
                        <th style="width: 12%">Request Type</th>
                        <th style="width: 20%">Markets</th>
                        <th style="width: 22%">Reason</th>
                        <th style="width: 11%">Action</th>
                    </tr>

                    <?php foreach($pending_requests as $req){?>
                    <tr>
                        <?php if($req['non_fct'] == '1'){?>
                        <td> <a href="javascript:review_non_fct_ro_details('<?php echo $req['ext_ro_id']; ?>','<?php echo $req['id'] ?>','<?php echo $req['cancel_type']; ?>')"><?php echo $req['cust_ro'];?></a> <br/> (<?php echo $req['internal_ro']; ?>) </td>
                        <td>NON FCT</td>
                        <?php } else {?>
                        <td> <a href="javascript:review_fct_ro_details('<?php echo $req['ext_ro_id']; ?>','<?php echo $req['id'] ?>','<?php echo $req['cancel_type']; ?>')"><?php echo $req['cust_ro'];?></a> <br/> (<?php echo $req['internal_ro']; ?>) </td>
                        <td>FCT</td>
                        <?php } ?>

                        <td><?php
                            if($req['cancel_type'] == 'cancel_market') {?>
                                <a href="javascript:show_revised_market_price('<?php echo $req['id'] ?>')"><?php echo ucwords(str_replace("_", " ", $req['cancel_type'])); ?> &nbsp;</a>
                            <?php }else
                            if($req['cancel_type'] == 'cancel_content') {?>
                                <!--<span>Cancel Content &nbsp;</span> -->
                                <a href="javascript:show_revised_content_brand('<?php echo $req['id'] ?>')"><?php echo ucwords(str_replace("_", " ", $req['cancel_type'])); ?> &nbsp;</a>
                            <?php }else

                            if($req['cancel_type'] == 'cancel_brand') {?>
                                <!--<span>Cancel Brand &nbsp;</span> -->
                                <a href="javascript:show_revised_content_brand('<?php echo $req['id'] ?>')"><?php echo ucwords(str_replace("_", " ", $req['cancel_type'])); ?> &nbsp;</a>
                            <?php }else
                                if($req['cancel_type'] == 'cancel_ro') {?>
                                    <a href="javascript:show_revised_ro_amount('<?php echo $req['id'] ?>')"><?php echo ucwords(str_replace("_", " ", $req['cancel_type'])); ?> &nbsp;</a>
                            <?php }else

                                if($req['cancel_type'] == 'submit_ro_approval'){?>
                                    <span>RO Approval &nbsp;</span>
                            <?php }else

                                if($req['cancel_type'] == 'ro_approval'){?>
                                    <span>Schedule Approval &nbsp;</span>
                            <?php } ?>
                        </td>
                        <td><?php if($req['cancel_type'] == 'cancel_market') {
                                echo $req['market'];
                            }else{
                                echo "All";
                            } ?> &nbsp;
                        </td>
                        <td><?php echo $req['reason']; ?></td>

                        <?php
//for RO approval requests
                        if($req['cancel_type'] == 'submit_ro_approval') {?>

                            <td>
                                <?php if($req['cancel_status'] == 0 ){
                                    if($req['show_approve'] == '1'){ ?>
                                        <a href="javascript:submit_ro_approval_request_handler('<?php echo $req['ext_ro_id'];?>','1','<?php echo $req['cancel_type']; ?>','<?php echo $req['id'] ?>')">Approve</a> |
                                    <?php } else {
                                        if($profile_id != 11){ ?>
                                            <a href="javascript:submit_ro_forward_request_handler('<?php echo $req['ext_ro_id'];?>','1','<?php echo $req['cancel_type']; ?>','<?php echo $req['id'] ?>')">Forward</a> |
                                        <?php }
                                    }?>
                                        <a href="javascript:submit_ro_reject_request_handler('<?php echo $req['ext_ro_id'];?>','2','<?php echo $req['cancel_type']; ?>','<?php echo $req['id'] ?>')" value="Reject">Reject</a>

                                <?php } ?>
                            </td>

                        <?php }

//for schedule approval requests
                        else if($req['cancel_type'] == 'ro_approval') {
                                if($req['non_fct'] == '0'){ ?>
                                    <td>
                                        <a href="javascript:ro_approval('<?php echo $req['ext_ro_id'] ?>')">Approve</a>
                                    </td>
                                <?php } else {?>
                                    <td>
                                        <a href="javascript:non_fct_ro_approval('<?php echo  rtrim(base64_encode($req['internal_ro']),'=') ?>')">Approve</a>
                                    </td>
                                <?php }
                        }
//for cancel_ro and cancel_market requests
                        else { ?>
                                <td>
                                    <?php if($req['cancel_status'] == 0 ){
                                        if($req['cancel_type'] == 'cancel_ro'){ ?>
                                            <a href="javascript:cancel_ro_admin('<?php echo $req['ext_ro_id'] ?>','<?php echo  rtrim(base64_encode($req['cust_ro']),'=') ?>','0','<?php echo rtrim(base64_encode($req['internal_ro']),'=') ?>')">Approve</a> |
                                            <input type="hidden" id="is_cancel_market_requested" value="<?php echo $req['is_cancel_market_requested'] ?>">
                                        <?php } else {?>
                                            <a href="javascript:approval_request_handler('<?php echo $req['ext_ro_id'];?>','1','<?php echo $req['cancel_type']; ?>','<?php echo $req['id'] ?>')">Approve</a> |
                                        <?php }
                                    } ?>

                                    <?php if($req['cancel_status'] ==  0 ){?>
                                        <a href="javascript:reject_request_handler('<?php echo $req['ext_ro_id'];?>','2','<?php echo $req['cancel_type']; ?>','<?php echo $req['id'] ?>')" value="Reject">Reject</a>
                                    <?php } ?>
                                </td>

                        <?php } ?>
                    </tr>
                    <?php } ?>

                </table><br>

                <div class="pagination right">
                    <?php echo $page_links ?>
                </div>		<!-- .paggination ends -->
            <?php } else {?>
                <h2 style="text-align:center;">No Pending Requests !</h2>
            <?php } ?>

            </div>	<!-- .block ends -->


        </div>
    </div>
</div>
<script language="javascript">
    var BASE_URL = '/surewaves_easy_ro';
    var SERVICE_TAX = 1.0;
    var show_details;
    var schedule_data;
    var submit_ext;
    var market_priority_detail;
    var internal_ro_no;
    var ro_id;
    var external_ro;
    var client_name;
    var make_good_type;
    function cancel_ro_admin(id,cust_ro,edit,internal_ro) {
        if($('#is_cancel_market_requested').val() == 1){
            alert("Can't Cancel RO! Market Cancellation Request is Pending !!");
            return false;
        } else {
            $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/cancel_ro_admin/'+id+'/'+cust_ro+ "/" + edit + "/" + internal_ro,iframe:true, width: '800px', height:'350px'});
        }
    }
    function show_revised_market_price(cancel_id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/revised_market_price_for_ro/'+cancel_id,iframe:true, width: '800', height:'350px'});
    }
    function show_revised_content_brand(cancel_id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/show_revised_content_brand/'+cancel_id,iframe:true, width: '800', height:'350px'});
    }
    
    function show_revised_ro_amount(cancel_id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/revised_ro_amount/'+cancel_id,iframe:true, width: '800', height:'350px'});
    }

    function review_fct_ro_details(order_id,cancel_id,cancel_type){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/review_fct_ro_details/'+order_id + "/" +cancel_id + "/" +cancel_type,iframe:true, width: '1040px', height:'650px'});
    }

    function review_non_fct_ro_details(order_id,cancel_id,cancel_type){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/review_non_fct_ro_details/'+order_id + "/" +cancel_id + "/" +cancel_type,iframe:true, width: '940px', height:'650px'});
    }


    function approval_request_handler(order_id,status,cancel_type,cancel_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approve_request_for_cancellation/" + order_id + "/" + status + "/" + cancel_type+ "/" + cancel_id;
    }

    function ro_approval(id){
        $("#modal_title").text("APPROVE RO");
        $('#Modal_body').html("");
        ro_id = id;
        $.ajax(BASE_URL + '/ro_manager/pre_ro_approval/', {
            async: true,
            data: {
                "id": id,

            },
            dataType: 'json',
            type: "POST",
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            complete: function () {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
            },
            success: function (responsedata) {

                console.log(responsedata);
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                if (!responsedata.isLoggedIn) {
                    window.location.href = BASE_URL;
                    return false;
                } else if (responsedata.Status == 'fail') {
                    if ('formValidation' in responsedata.Data) {
                        alert('Invalid RO ID!!');
                        return false;
                    }
                }

                if ("jsonData" in responsedata.Data) {
                    $('#myModal').modal('show');
                    $("#myModal").data('bs.modal')._config.backdrop = 'static';
                    var data = responsedata.Data.jsonData;
                    var cust_ro = data.submit_ext_ro_data[0].cust_ro;
                    var internal_ro = data.submit_ext_ro_data[0].internal_ro;
                    var submitted_by = data.submit_ext_ro_data[0].submitted_by;
                    var approved_by = data.submit_ext_ro_data[0].approved_by;
                    var agency_name = data.submit_ext_ro_data[0].agency;
                    var advertiser_name = data.submit_ext_ro_data[0].client;
                    var brand_name = data.submit_ext_ro_data[0].brand_name;
                    var ro_start_date = data.submit_ext_ro_data[0].camp_start_date;
                    var ro_end_date = data.submit_ext_ro_data[0].camp_end_date;
                    var ro_status = data.ro_status_entry;
                    var gross_amount = data.ro_amount_detail[0].ro_amount;
                    internal_ro_no = internal_ro;
                    external_ro = cust_ro;
                    client_name = advertiser_name;
                    make_good_type = data.submit_ext_ro_data[0].make_good_type;
                    $('#date_ro_date').val(data.submit_ext_ro_data[0].ro_date);
                    $('#date_camp_start_date').val(data.submit_ext_ro_data[0].camp_start_date);
                    $('#date_camp_end_date').val(data.submit_ext_ro_data[0].camp_end_date);


                    submit_ext = data.submit_ext_ro_data;

                    //Ro details
                    var customer_release_order = "<div class='custom_head'>" +
                        "<h2>CUSTOMER RELEASE ORDER</h2>" +
                        "</div>" +
                        "<div class='table_div'>" +
                        "<table class='table' width='100%'>" +
                        "<tr>" +
                        "<th>Customer RO Number</th>" +
                        "<th>Submitted By</th>" +
                        "<th>Approved By</th>" +
                        "<th>Agency Name</th>" +
                        "<th>Advertiser Name</th>" +
                        "<th>Brand Name</th>" +
                        "<th>RO start date</th>" +
                        "<th>Ro end Date</th>" +
                        "<th>Ro status</th>" +
                        "<th>Gross CRO Amount</th>" +
                        "</tr>" +
                        "<tr>" +
                        "<td>" + cust_ro + "<br>" + "(" + internal_ro + ")" + "</td>" +
                        "<td>" + submitted_by + "</td>" +
                        "<td>" + approved_by + "</td>" +
                        "<td>" + agency_name + "</td>" +
                        "<td>" + advertiser_name + "</td>" +
                        "<td>" + brand_name + "</td>" +
                        "<td id='approve_camp_start_date'>" + ro_start_date + "</td>" +
                        "<td id='approve_camp_end_date'>" + ro_end_date + "</td>" +
                        "<td>" + ro_status + "</td>" +
                        "<td>" + gross_amount + "</td>" +
                        "</tr>" +
                        "</table>" +
                        "</div>";
                    $('#Modal_body').append(customer_release_order);
                    var option = "<div id='option_div'>" +
                        "<span class='link' id='campaigns_schedule_span'>Campaign Schedule</span>" +
                        "<span class='link' id='channels_schedule_span'>Channels Schedule</span>" +
                        "<span class='link' id='nw_ro_payment_span'>Network Ro Payment</span>";
                    if (data.verify_ro_approved == 0) {
                        option = option + "<span class='link' id='change_ro_dates_span'>Change Ro dates</span>"
                    }
                    if (data.is_ro_completed == 1) {
                        option = option + "<span class='invalid' id='ro_completed_div'>Ro Completed</span>";
                    } else if (data.is_cancel_market_requested == 0 && data.cancel_request_status == 1) {
                        option = option + "<span class='invalid' class='cancelled_span' >Cancelled</span></div>";
                    }
                    var cancel = "";
                    if (data.is_cancel_market_requested == 1) {
                        cancel = "<div class='cancel_div'>Can't Cancel RO! Market Cancellation Request is Pending !! </div>"
                    }
                    $('#Modal_body').append(option);
                    $('#Modal_body').append(cancel);

                    var network_summary = '';

                    //Network Summary
                    if (data.scheduled_data.length == 0) {
                        network_summary = "<div class='custom_head' ><h2>No Active Campaign</h2></div>";

                    } else {
                        schedule_data = data.scheduled_data;
                        network_summary = "<form id='approve_form'><div  class='custom_head'>" +
                            "<h2>Network Ro Summary</h2>" +
                            "</div>" +
                            "<div id='network_summary_table' class='table_div'>" +
                            "<div class='row custom_margin'>" +
                            "<div class='col-1'><b>Gross CRO Amount</b></div>" +
                            "<div class='col-1'><b>Media Agency Commission</b></div>" +
                            "<div class='col-1'><b>Net Amount</b></div>" +
                            "<div class='col-1'><b>Agency Rebate</b></div>" +
                            "<div class='col-1'><b>Other Expenses</b></div>" +
                            "<div class='col-1'><b>Actual Net Amount</b></div>" +
                            "<div class='col-1'><b>Net Revenue</b></div>" +
                            "<div class='col-1'><b>Total Network's Payout</b></div>" +
                            "<div class='col-1'><b>Net Contribution Amount</b></div>" +
                            "<div class='col-1'><b>Net Contribution Almount(%)</b></div>" +
                            "</div>";
                        if (typeof data.ro_amount_detail[0].ro_amount == undefined) {
                            var other_expenses = Number(data.ro_amount_detail[0].marketing_promotion_amount) +
                                Number(data.ro_amount_detail[0].field_activation_amount) +
                                Number(data.ro_amount_detail[0].sales_commissions_amount) +
                                Number(data.ro_amount_detail[0].creative_services_amount) +
                                Number(data.ro_amount_detail[0].other_expenses_amount);

                            network_summary = network_summary + "<div class='row custom_margin '>" +
                                "<div class='col-1 custom_border'>In process</div>" +
                                "<div class='col-1 custom_border'>In process</div>" +
                                "<div class='col-1 custom_border'>In process</div>" +
                                "<div class='col-1 custom_border'>In process</div>" +
                                "<div class='col-1 custom_border'>" + other_expenses;
                            if (data.logged_in_user.profile_id == 1) {
                                network_summary = network_summary + "<div class='link' id='view_modify_span'>View/Modify</div></div>";
                            }
                            network_summary = network_summary +
                                "<div class='col-1 custom_border'>In process</div>" +
                                "<div class='col-1 custom_border'>In process</div>" +
                                "<div class='col-1 custom_border'>" + data.total_nw_payout + "</div>" +
                                "<div class='col-1 custom_border'>In process</div>" +
                                "<div class='col-1 custom_border'>In process</div>" +
                                "</div>";

                        } else {
                            //agency rebate and other expenses haven't been used since the beginning of the easy ro.
                            var agency_rebate = 0;
                            var other_expenses = 0;
                            var net_amount = Number(data.ro_amount_detail[0].ro_amount) - Number(data.ro_amount_detail[0].agency_commission_amount);
                            network_summary = network_summary + "<div class='row custom_margin '>" +
                                "<div class='col-1 custom_border' id='ro_amount'>" + data.ro_amount_detail[0].ro_amount + "</div>" +
                                "<div class='col-1 custom_border' id='agency_commission_amount'>" + data.ro_amount_detail[0].agency_commission_amount + "</div>" +
                                "<div class='col-1 custom_border' id='net_amount'>" + net_amount + "</div>";
                            if (Number(data.ro_amount_detail[0].ro_valid_field) == 1) {
                                network_summary = network_summary + "<div class='col-1 custom_border' style='color:red'><span  id='agency_rebate'>Missing</span></div>" +
                                    "<div class='col-1 custom_border'   style='color:red'><span  id='other_expenses'>Missing</span>";
                                if (data.logged_in_user.profile_id == 1) {
                                    network_summary = network_summary + "<div class='link' id='view_modify_span' >View/Modify</div></div>";
                                }

                            } else {
                                other_expenses = Number(data.ro_amount_detail[0].marketing_promotion_amount) +
                                    Number(data.ro_amount_detail[0].field_activation_amount) +
                                    Number(data.ro_amount_detail[0].sales_commissions_amount) +
                                    Number(data.ro_amount_detail[0].creative_services_amount) +
                                    Number(data.ro_amount_detail[0].other_expenses_amount);


                                if (data.ro_amount_detail[0].agency_rebate_on != 'net_amount') {
                                    agency_rebate = Number(data.ro_amount_detail[0].ro_amount) * Number(data.ro_amount_detail[0].agency_rebate) / 100;
                                } else {
                                    agency_rebate = (Number(data.ro_amount_detail[0].ro_amount) - Number(data.ro_amount_detail[0].agency_commission_amount))
                                        * Number(data.ro_amount_detail[0].agency_rebate) / 100;
                                }
                                network_summary = network_summary + "<div class='col-1 custom_border' name='agency_rebate' id='agency_rebate' style='color:green'>" + agency_rebate + "</div>" +
                                    "<div class='col-1 custom_border'  name='other_expenses' style='color:green'><span id='other_expenses'>" + other_expenses + "</span>";
                                if (data.logged_in_user.profile_id == 1) {
                                    network_summary = network_summary + "<div id='view_modify_span' class='link'>View/Modify</div></div>";
                                } else {
                                    network_summary = network_summary + "</div>";
                                }

                            }
                            var actual_net_amount = Number(data.ro_amount_detail[0].ro_amount) - Number(data.ro_amount_detail[0].agency_commission_amount) - Number(agency_rebate) - Number(other_expenses);
                            var net_revenue = Number(data.ro_amount_detail[0].ro_amount) - Number(data.ro_amount_detail[0].agency_commission_amount);
                            net_revenue = net_revenue * 1.0;
                            var total_nw_payout = data.total_nw_payout;
                            var surewaves_share = actual_net_amount - total_nw_payout;
                            var surewaves_share_per = (surewaves_share / net_revenue * 100);
                            network_summary = network_summary + "<div class='col-1 custom_border' id='actual_net_amount'>" + actual_net_amount + "</div>" +
                                "<div class='col-1 custom_border' id='net_revenue'>" + net_revenue + "</div>" +
                                "<div class='col-1 custom_border' ><span id='total_network_payout'>" + total_nw_payout.toFixed(2) + "</span></div>" +
                                "<div class='col-1 custom_border' id='surewaves_share'>" + surewaves_share.toFixed(2) + "</div>" +
                                "<div class='col-1 custom_border' id='surewaves_share_per'>" + surewaves_share_per.toFixed(2) + "</div></div>";

                        }
                        //Approve button
                        if (data.logged_in_user.profile_id == 1) {
                            var ro_approval_stat;
                            if (data.ro_approval === null) {
                                ro_approval_stat = 0;
                            } else {
                                ro_approval_stat = data.ro_approval[0].ro_approval_request_status == 1;
                            }

                            if (ro_approval_stat || data.ro_amount_detail[0].ro_approval_status == 1) {
                                if (data.ro_status_entry == 'Approval Requested' && data.is_cancel_market_requested == 0 && data.is_ro_completed == 0) {
                                    network_summary = network_summary + "<div class='row'>" +
                                        "<div class='col-11'>" +
                                        "<input type='button' id='reset_approve_btn' class='btn btn-primary' value='RESET'>" +
                                        "<input type='submit' id='approve_btn' class='btn btn-primary' value='APPROVE'>" +
                                        "</div>" +
                                        "</div>";

                                } else if (data.ro_status_entry == 'Approval Requested' && data.is_cancel_market_requested == 1) {
                                    network_summary = network_summary + "<div class='row'>" +
                                        "<div>" +
                                        "<div class='col-11 invalid'>Can't Approve Ro! Market Cancellation request is pending</div>" +
                                        "</div>" +
                                        "</div>";
                                }
                            } else {
                                network_summary = network_summary + "<div class='row '>" +
                                    "<div class='col-11''>" +
                                    "<div class='invalid'>Can't Approve Ro! Waiting for Approval Request</div>" +
                                    "</div>" +
                                    "</div>";
                            }
                        }
                        network_summary = network_summary + "</div>";


                        //Market Priority
                        if (data.ro_status_entry == 'Approval Requested' && data.is_cancel_market_requested == 0 && data.logged_in_user.profile_id == 1) {
                            network_summary = network_summary + "<div class='market_priority_div' id='market_priority_div' data-toggle='collapse' data-target='#market_priority_table_div'>" +
                                "<h2>Market Priority" +
                                "</h2>" +
                                "<i id='market_i' class='down'></i></div>" +
                                "<div id='market_priority_table_div' class='table_div collapse'>" +
                                "<table class='table'>" +
                                "<tr>" +
                                "<th>Market</th>";

                            $.map(data.priority, function (value, index) {
                                network_summary = network_summary + "<th>" + value + "</th>";

                            });
                            network_summary = network_summary + "</tr>";
                            market_priority_detail = data.market_priority_details;
                            $.map(data.market_priority_details, function (value, index) {
                                network_summary = network_summary + "<tr><td>" + value.market_name + "</td>";
                                $.map(data.priority, function (val, ind) {
                                    if (val in value.channel_priority) {
                                        network_summary = network_summary + "<td>" +
                                            "<input type='checkbox' class='prioritycheckbox' checked='checked' id='" + value.market_id + "_" + val + "' value='" + val + "'>" +
                                            "</td>"
                                    }
                                });

                            });
                            network_summary = network_summary + "</tr></table></div>";
                        }

                        //Network Details
                        network_summary = network_summary + "<div class='market_priority_div' id='network_detail_div' data-toggle='collapse' data-target='#network_detail_table_div'>" +
                            "<h2>Network Details</h2>" +
                            "<i id='network_i' class='down'></i></div>" +
                            "<div id='network_detail_table_div' class='table_div collapse'>" +
                            "<table class='table' id='channel_table'>" +
                            "<tr>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "<th style='display: none'>&nbsp;</th>" +
                            "</tr>";
                        $.map(data.scheduled_data, function (network, index) {
                            network_summary = network_summary + "<tr>" +
                                "<td colspan='9' class='network_name_style'>Network Name:" + network.network_name + "(" + network.market_name + ")" +
                                "</td>" +
                                "</tr>";
                            var nw_amount = 0;
                            $.map(network.channels_data_array, function (channel, ind) {

                                network_summary = network_summary + "<tr>" +
                                    "<td colspan='9' id='" + index + "_" + channel.channel_id + "_checkbox_td'>" +
                                    "<input type='checkbox' data='0'";
                                if (data.logged_in_user.profile_id != 1) {
                                    network_summary = network_summary + " disabled ";
                                }
                                network_summary = network_summary + "class='checkboxclass' id='" + index + "_" + channel.channel_id + "_checkbox'  checked='checked' value='" + channel.channel_id + "'>Channel Name:" + channel.channel_name +
                                    "</td>" +
                                    "</tr>" +
                                    "<tr>" +
                                    "<td colspan='3'><b>Ad Type</b></td>" +
                                    "<td colspan='2'><b>Total Ad Seconds</b></td>" +
                                    "<td colspan='1'><b>Last Rate</b></td>" +
                                    "<td colspan='2'><b>Reference Rate</b></td>" +
                                    "<td colspan='1'><b>Amount</b></td>" +
                                    "</tr>";
                                if (channel.total_spot_ad_seconds > 0) {
                                    network_summary = network_summary + "<tr>" +
                                        "<td colspan='3'>Spot Ad</td>" +
                                        "<td colspan='2' id='" + index + "_" + channel.channel_id + "_total_spot_ad_seconds'>" + channel.total_spot_ad_seconds + "</td>" +
                                        "<td ><input type='text'";
                                    if (data.logged_in_user.profile_id != 1) {
                                        network_summary = network_summary + " disabled ";
                                    }
                                    network_summary = network_summary + "class='form-control stylewidth spotclass' name='" + index + "_" + channel.channel_id + "_spot_rate' id='" + index + "_" + channel.channel_id + "_spot_rate' value='" + channel.channel_spot_avg_rate + "'></td>" +
                                        "<td colspan='2'>" + channel.channel_spot_reference_rate + "</td>" +
                                        "<td ><input type='text'";
                                    if (data.logged_in_user.profile_id != 1) {
                                        network_summary = network_summary + " disabled ";
                                    }
                                    network_summary = network_summary + "class='form-control stylewidth spotamountclass' name='" + index + "_" + channel.channel_id + "_spot_amount' id='" + index + "_" + channel.channel_id + "_spot_amount' value='" + channel.channel_spot_amount + "'></td>";
                                    nw_amount = nw_amount + channel.channel_spot_amount;
                                }
                                if (channel.total_banner_ad_seconds > 0) {
                                    network_summary = network_summary + "<tr>" +
                                        "<td colspan='3'>Banner Ad</td>" +
                                        "<td colspan='2' id='" + index + "_" + channel.channel_id + "_total_banner_ad_seconds'>" + channel.total_banner_ad_seconds + "</td>" +
                                        "<td><input type='text'";
                                    if (data.logged_in_user.profile_id != 1) {
                                        network_summary = network_summary + " disabled ";
                                    }
                                    network_summary = network_summary + "class='form-control stylewidth bannerclass' name='" + index + "_" + channel.channel_id + "_banner_rate' id='" + index + "_" + channel.channel_id + "_banner_rate' value='" + channel.channel_banner_avg_rate + "'></td>" +
                                        "<td colspan='2'>" + channel.channel_spot_reference_rate + "</td>" +
                                        "<td><input type='text'";
                                    if (data.logged_in_user.profile_id != 1) {
                                        network_summary = network_summary + " disabled ";
                                    }
                                    network_summary = network_summary + "class='form-control stylewidth banneramountclass' name='" + index + "_" + channel.channel_id + "_banner_amount' id='" + index + "_" + channel.channel_id + "_banner_amount' value='" + channel.channel_banner_amount + "'></td>";
                                    nw_amount = nw_amount + channel.channel_banner_amount;
                                }
                            });
                            var nw_payout = 0;
                            if (nw_amount > 0 && network.revenue_sharing > 0) {
                                nw_payout = nw_amount * network.revenue_sharing / 100;
                            }
                            network_summary = network_summary + "<tr>" +
                                "<td colspan='3'><b>Network Ro Amount</b></td>" +
                                "<td colspan='2'>&nbsp;</td>" +
                                "<td>&nbsp;</td>" +
                                "<td colspan='2'>&nbsp;</td>" +
                                "<td  id='" + index + "_network_amount'>" + nw_amount + "</td>" +
                                "</tr>" +
                                "<tr>" +
                                "<td colspan='3'><b>Network Share(%)</b></td>" +
                                "<td colspan='2'>&nbsp;</td>" +
                                "<td>&nbsp;</td>" +
                                "<td colspan='2'></td>" +
                                "<td  ><input type='text'";
                            if (data.logged_in_user.profile_id != 1) {
                                network_summary = network_summary + " disabled ";
                            }
                            network_summary = network_summary + "class='form-control stylewidth networkshareclass' name='" + index + "_network_share' id='" + index + "_network_share' value='" + network.revenue_sharing + "'></td>" +
                                "</tr>" +
                                "<tr>" +
                                "<td colspan='3'><b>Network Payout(Net)</b></td>" +
                                "<td colspan='2'>&nbsp;</td>" +
                                "<td>&nbsp;</td>" +
                                "<td colspan='2'>&nbsp;</td>" +
                                "<td  ><input type='text'";
                            if (data.logged_in_user.profile_id != 1) {
                                network_summary = network_summary + " disabled ";
                            }
                            network_summary = network_summary + "name='" + index + "_network_payout' id='" + index + "_network_payout' class='form-control stylewidth networkpayoutclass' value='" + nw_payout.toFixed(2) + "'</td> " +
                                "</tr>";

                        });
                        network_summary = network_summary + "</table>" +
                            "</div>";
                        network_summary = network_summary + "</form>";


                    }
                    $('#Modal_body').append(network_summary);
                    var validator = $('#approve_form').validate({
                        errorClass: 'invalid_error',
                        errorPlacement: function (error, element) {
                            $(element).after(error);
                        },
                        submitHandler: function (form, event) {
                            event.preventDefault();
                            $('#confirmModal').modal('show');
                            $("#confirmModal").data('bs.modal')._config.backdrop = 'static';
                            var confirm_layout = "<table class='table'><tr>" +
                                "<th>Network</th>" +
                                "<th>Total Payout</th></tr>";
                            $.map(schedule_data, function (network, nw_index) {
                                confirm_layout = confirm_layout + "<tr>" +
                                    "<td>" + network.network_name + "</td>" +
                                    "<td>" + $('#' + nw_index + '_network_payout').val() + "</td>" +
                                    "</tr>"
                            });

                            confirm_layout = confirm_layout + "<tr>" +
                                "<td>Total Network Payout</td>" +
                                "<td>" + $('#total_network_payout').text() + "</td>" +
                                "</tr>" +
                                "<tr>" +
                                "<td>Net Revenue</td>" +
                                "<td>" + $('#net_revenue').text() + "</td>" +
                                "</tr>" +
                                "<tr>" +
                                "<td>Net Contribution Percent</td>" +
                                "<td>" + $('#surewaves_share_per').text() + "</td>" +
                                "</tr>";
                            if (Number($('#surewaves_share_per').text()) < 25) {
                                confirm_layout = confirm_layout + "<tr>" +
                                    "<td>Justification for Approval<span style='color:red'>*</span></td>" +
                                    "<td><input type='text' id='justification_for_approval' name='justification_for_approval'></td>" +
                                    "</tr>" +
                                    "<tr>" +
                                    "<td>Corrective Action Plan for future<span style='color:red'>*</span></td>" +
                                    "<td><input type='text' id='corrective_action_plan' name='corrective_action_plan'></td>" +
                                    "</tr>";
                            }
                            confirm_layout = confirm_layout + "<tr>" +
                                "<td colspan='2'><input type='button' id='confirm_btn' class='btn btn-primary' value='CONFIRM'> </td></tr>";
                            $('#confirm_Modal_body').html('');
                            $('#confirm_Modal_body').html(confirm_layout);
                            $(document).on("click", "#confirm_btn", function () {
                                if($('#justification_for_approval').length>0)
                                {
                                    //console.log("inside this statement");
                                    //console.log($('#justification_for_approval').val().length);
                                    if ($('#justification_for_approval').val().length == 0) {
                                        //console.log("hellow");
                                        alert("justification for approval cannot be empty");
                                        return false;
                                    }
                                }
                                if($('#corrective_action_plan').length>0)
                                {
                                    //console.log("inside else statement");
                                    //console.log()
                                    if ($('#corrective_action_plan').val().length == 0) {
                                        //console.log("world");
                                        alert('corrective action plan cannot be empty');
                                        return false;
                                    }
                                }

                                var data_object = createApproveData();
                                //console.log(data_object);
                                $.ajax(BASE_URL + '/ro_manager/post_add_price_approve', {
                                    type: 'POST',
                                    dataType:'json',
                                    beforeSend: function () {
                                        $('#loader_background').css("display", "block");
                                        $('#loader_spin').css("display", "block");
                                        $('#confirm_btn').attr('disabled', true);
                                    },
                                    data: data_object,
                                    success: function (responsedata) {
                                        $('#loader_background').css("display", "none");
                                        $('#loader_spin').css("display", "none");
                                        if (!responsedata.isLoggedIn) {
                                            window.location.href = BASE_URL;
                                            return false;
                                        } else if (responsedata.Status == 'fail') {

                                            alert(responsedata.Message);
                                            return false;

                                        }
                                        else if('html' in responsedata.Data)
                                        {
                                            $('#confirmModal').modal('hide');
                                            window.location.href=BASE_URL+'/account_manager/home';
                                    
                                    //        $('#Modal_body').html('');
                                      //      $('#Modal_body').html(responsedata.Data.html)
                                        }
                                        else
                                        {
                                            alert("Some error occurred!!");
                                            window.location.href=BASE_URL+'/account_manager/home';
                                        }

                                    },
                                    error: function () {
                                        $('#loader_background').css("display", "none");
                                        $('#loader_spin').css("display", "none");
                                        alert('error in submitting');
                                        $('#confirm_btn').attr('disabled', false);
                                    }
                                })
                            });

                        }
                    });
                    $(document).on("click", "#approve_btn", function () {
                        $('#approve_form').validate();
                        if (isNaN(Number($('#agency_rebate').text()))) {
                            alert("Enter Agency Rebate");
                            return false;
                        }
                        if (isNaN(Number($('#agency_rebate').text()))) {
                            alert("Enter Other Expenses");
                            return false;
                        }

                        $.map(data.scheduled_data, function (network, nw_index) {
                            $.map(network.channels_data_array, function (channel, index) {
                                var channel_id = channel.channel_id;
                                if ($('#' + nw_index + '_' + channel_id + '_checkbox').attr('data') == 0) {
                                    if (channel.total_spot_ad_seconds > 0) {
                                        var spot_rate_id = nw_index + "_" + channel_id + "_spot_rate";
                                        var spot_amount_id = nw_index + "_" + channel_id + "_spot_amount";

                                        $('#' + spot_rate_id).rules('add', {
                                            required: true,
                                            check_spot_rate: true
                                        });
                                        $('#' + spot_amount_id).rules('add', {
                                            required: true,
                                            check_spot_amount: true
                                        });
                                    }
                                    if (channel.total_banner_ad_seconds > 0) {
                                        var banner_rate_id = nw_index + "_" + channel_id + "_banner_rate";
                                        var banner_amount_id = nw_index + "_" + channel_id + "_banner_amount";

                                        $('#' + banner_rate_id).rules('add', {
                                            required: true,
                                            check_banner_rate: true

                                        });
                                        $('#' + banner_amount_id).rules('add', {
                                            required: true,
                                            check_banner_amount: true
                                        });
                                    }
                                }
                            });
                            $('#' + nw_index + '_network_share').rules('add', {
                                required: true,
                                check_network_share: true
                            });
                            $('#' + nw_index + '_network_payout').rules('add', {
                                required: true,
                                check_network_payout: true,
                            })

                        });
                        $.validator.addMethod("check_spot_rate", function (value, element) {
                            if (isNaN(value)) {
                                return false;
                            } else if (value < 0) {
                                return false;
                            }
                            return true;
                        }, "Enter correct value of spot rate");

                        $.validator.addMethod("check_spot_amount", function (value, element) {
                            if (isNaN(value)) {
                                return false;
                            } else if (value < 0) {
                                return false;
                            }
                            return true;
                        }, "Enter correct value of spot amount");

                        $.validator.addMethod("check_network_payout", function (value, element) {
                            if (isNaN(value)) {
                                return false;
                            } else if (value < 0) {
                                return false;
                            }
                            return true;
                        }, "Enter correct value of network payout");

                        $.validator.addMethod("check_network_share_also", function (value, element) {
                            var id = $(value).attr('id');
                            var id_arr = id.split('_');
                            var nw_index = id_arr[0];
                            var nw_share_value = $('#' + nw_index + '_network_share').val();
                            if (isNaN(nw_share_value)) {
                                return false;
                            } else if (nw_share_value < 0) {
                                return false;
                            }
                            return true;
                        }, "Enter the value of network_share greater than 0");

                        $.validator.addMethod("check_banner_rate", function (value, element) {

                            if (isNaN(value)) {
                                return false;
                            } else if (value < 0) {
                                return false;
                            }
                            return true;
                        }, "Enter correct value of banner rate");

                        $.validator.addMethod("check_banner_amount", function (value, element) {
                            if (isNaN(value)) {
                                return false;
                            } else if (value < 0) {
                                return false;
                            }
                            return true;
                        }, "Enter correct value of banner amount");

                        $.validator.addMethod("check_network_share", function (value, element) {
                            if (isNaN(value)) {
                                return false;
                            } else if (value < 0 || value > 100) {
                                return false;
                            }
                            return true;
                        }, "Enter network share between 0 and 100");

                    });
                } else if ("html" in responsedata.Data) {
                    $('#myModal').modal('show');
                    $("#myModal").data('bs.modal')._config.backdrop = 'static';
                    $('#Modal_body').html(responsedata.Data.html);
                }
            },
            error: function () {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                alert("could not load");
            }
        });

    }

    function non_fct_ro_approval(order_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/non_fct_ro/approve_non_fct/" + order_id ;
    }

    function reject_request_handler(order_id,status,cancel_type,cancel_id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/reject_request_reason/'+ order_id + "/" + status + "/" + cancel_type+ "/" + cancel_id ,iframe:true, width: '800', height:'350px'});
    }

    function submit_ro_approval_request_handler(order_id,status,cancel_type,cancel_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/submit_ro_approve/" + order_id + "/" + status + "/" + cancel_type+ "/" + cancel_id;
    }
    function submit_ro_forward_request_handler(order_id,status,cancel_type,cancel_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/submit_ro_forward/" + order_id + "/" + status + "/" + cancel_type+ "/" + cancel_id;
    }
    function submit_ro_reject_request_handler(order_id,status,cancel_type,cancel_id){
        //window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/submit_ro_reject/" + order_id + "/" + status + "/" + cancel_type+ "/" + cancel_id;
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/reject_request_reason/'+ order_id + "/" + status + "/" + cancel_type+ "/" + cancel_id ,iframe:true, width: '800', height:'350px'});
    }
    
    $(document).ready(function() {
        $("#date_camp_start_date").datepicker({
            defaultDate: "+1w",
            minDate: 1,
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            onClose: function (selectedDate) {
                $("#date_camp_end_date").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#date_camp_end_date").datepicker({
            defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            onClose: function (selectedDate) {
                $("#date_camp_start_date").datepicker("option", "maxDate", selectedDate);
            }
        });
        $('.modal').on("hidden.bs.modal", function (e) {
            if ($('.modal:visible').length) {
                $('body').addClass('modal-open');
            }
        });

        $(document).on('click', '#market_priority_div', function () {
            if ($('#market_priority_div').hasClass('collapsed')) {
                $('#market_i').removeClass('up');
                $('#market_i').addClass('down');

            } else {
                $('#market_i').removeClass('down');
                $('#market_i').addClass('up');
            }

        });

        $(document).on('click', '#network_detail_div', function () {
            if ($('#network_detail_div').hasClass('collapsed')) {
                $('#network_i').removeClass('up');
                $('#network_i').addClass('down');

            } else {
                $('#network_i').removeClass('down');
                $('#network_i').addClass('up');
            }

        });


        $(document).on('click', '#load_campaign_btn', function () {
            $.ajax(BASE_URL + '/ro_manager/campaigns_schedule', {
                type: 'POST',
                data: {
                    "order_id": internal_ro_no,
                    "edit": "1",
                    "id": ro_id
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success: function (responsedata) {
                    var data = responsedata.Data.jsonData;
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    console.log(data.campaigns);
                    $('#campaign_tbody').empty();
                    var ro_detail = '';
                    if (data.campaigns.length > 0) {
                        $.map(data.campaigns, function (campaign, index) {
                            ro_detail = ro_detail + '<tr>';
                            var csv = campaign.csv_input.split(',');
                            var channels = csv[12].split('#');
                            var channels_name = channels.join();
                            var color;
                            if (campaign['mismatch_impression'] == 0) {
                                color = 'rgba(200, 247, 197, 0.5)';
                            } else if (campaign['mismatch_impression'] == 1) {
                                color = 'rgba(231, 76, 60, 0.5)';
                            } else {
                                color = 'rgba(245, 230, 83, 0.5)';
                            }
                            if (data.logged_in_user.profile_id == 3) {
                                ro_detail = ro_detail + '<td>';
                                if (campaign['derived_campaign_status'] == 'pending_approval') {
                                    ro_detail = ro_detail + "<input type='checkbox' class='campaigns_to_cancel' value='" + campaign['campaign_id'] + "'>";
                                }
                                ro_detail = ro_detail + '</td>';
                            }
                            ro_detail = ro_detail + "<td  style='background-color:" + color + " '><div style='width:90px;word-wrap:break-word;'>" + campaign['campaign_name'] + "</div></td>" +
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
                }
            })
        });


        $(document).on('click', '#change_ro_dates_span', function () {

            $('#date_modal_title').text('Change Ro Dates');
            $('#dateModal').modal('show');
            $("#dateModal").data('bs.modal')._config.backdrop = 'static';
        })


        $(document).on('click', '#search_campaign_btn', function () {
            if ($('#search_by').val() == '') {
                alert('select search by');
                return false;
            }
            var search_by = '';
            var search_camp = '';
            search_by = $('#search_by').val();
            search_camp = $('#search_camp').val();
            $.ajax(BASE_URL + '/ro_manager/campaigns_schedule', {
                type: 'POST',
                data: {
                    "order_id": internal_ro_no,
                    "edit": "1",
                    "id": ro_id,
                    "search_by": search_by,
                    "search_value": search_camp
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success: function (responsedata) {
                    var data = responsedata.Data.jsonData;
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    console.log(data.campaigns);
                    $('#campaign_tbody').empty();
                    var ro_detail = '';
                    if (data.campaigns.length > 0) {
                        $.map(data.campaigns, function (campaign, index) {
                            ro_detail = ro_detail + '<tr>';
                            var csv = campaign.csv_input.split(',');
                            var channels = csv[12].split('#');
                            var channels_name = channels.join();
                            var color;
                            if (campaign['mismatch_impression'] == 0) {
                                color = 'rgba(200, 247, 197, 0.5)';
                            } else if (campaign['mismatch_impression'] == 1) {
                                color = 'rgba(231, 76, 60, 0.5)';
                            } else {
                                color = 'rgba(245, 230, 83, 0.5)';
                            }
                            if (data.logged_in_user.profile_id == 3) {
                                ro_detail = ro_detail + '<td>';
                                if (campaign['derived_campaign_status'] == 'pending_approval') {
                                    ro_detail = ro_detail + "<input type='checkbox' class='campaigns_to_cancel' value='" + campaign['campaign_id'] + "'>";
                                }
                                ro_detail = ro_detail + '</td>';
                            }
                            ro_detail = ro_detail + "<td  style='background-color:" + color + " '><div style='width:90px;word-wrap:break-word;'>" + campaign['campaign_name'] + "</div></td>" +
                                "                <td ><div style='width:90px;word-wrap:break-word;'>" + campaign['brand_new'] + "</div></td>" +
                                "                <td > <div style='width:67px;word-wrap:break-word;'>" + campaign['agency_name'] + "</div></td>" +
                                "                <td > <div style='width:77px;word-wrap:break-word;'>" + campaign['start_date'] + "</div></td>" +
                                "                <td > <div style='width:77px;word-wrap:break-word;'>" + campaign['end_date'] + "</div></td>" +
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
                }
            })
        })

        $(document).on('click', '#dates_submit', function () {
            $.ajax(BASE_URL + '/account_manager/post_changed_ro_dates/', {
                type: 'POST',
                data: {
                    "txt_camp_start_date": $('#date_camp_start_date').val(),
                    "txt_camp_end_date": $('#date_camp_end_date').val(),
                    "hid_ro_id": ro_id
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert(data.Message);
                    $('#approve_camp_start_date').text($('#date_camp_start_date').val());
                    $('#approve_camp_end_date').text($('#date_camp_end_date').val());
                    $('#dateModal').modal('hide');
                },
                error: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert("could not save");
                }
            })
        });
        //for opening campaign schedule
        $(document).on("click", "#campaigns_schedule_span", function () {

            $.ajax(BASE_URL + '/ro_manager/campaigns_schedule', {
                type: 'POST',
                data: {
                    "order_id": internal_ro_no,
                    "edit": "1",
                    "id": ro_id,
                    "search_by": '',
                    "search_value": ''
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success: function (responsedata) {
                    var data = responsedata.Data.jsonData;
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#option_modal_title').text('Campaign Wise Schedule Summary');
                    $('#optionModal').modal('show');
                    $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                    console.log(data);
                    var ro_detail = "<form id='campaign_form'>" +
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
                        "<input type='button' class='btn btn-outline-success' style='font-size: 11px' id='search_campaign_btn' value='Search'> " +
                        "<input type='button' class='btn btn-outline-success' style='font-size: 11px' id='load_campaign_btn' value='Load'>" +
                        "</div></div>" +
                        "</div>" +
                        "</form>";
                    ro_detail = ro_detail + "<div class='table_div' style='border: 1px solid lightgrey'>" +
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
                        "<td>" + data.am_ext_ro + "<br/>(Internal RO Number:" + data.internal_ro + ")</td>" +
                        "<td>" + data.agency + "</td>" +
                        "<td>" + data.client + "</td>" +
                        "<td>" + data.brand + "</td>" +
                        "<td>" + data.camp_start_date + "</td>" +
                        "<td>" + data.camp_end_date + "</td>" +
                        "</tr>" +
                        "</table>" +
                        "</div>" +
                        "<br>";
                    ro_detail = ro_detail + "<div id='overflow_div' style='border-top-left-radius:5px;border-top-right-radius:5px;'>" +
                        "    <input type='button' class='submitlong' id='cancel_campaigns' style='display: none' value='Cancel Campaign'>";
                    if (data.campaigns.length > 0) {
                        ro_detail = ro_detail + '<table class="table table-sm"  cellpadding="0" cellspacing="0"  style="font-size:11px;"><tr style="background-color: white">';
                        if (data.logged_in_user.profile_id == 3) {
                            ro_detail = ro_detail + '<th>&nbsp;</th>'
                        }
                        ro_detail = ro_detail + "<th style='background-color:#F0EEE9;border-top-left-radius:5px;>Campaign Name</th>" +
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
                        $.map(data.campaigns, function (campaign, index) {
                            var csv = campaign.csv_input.split(',');
                            var channels = csv[12].split('#');
                            var channels_name = channels.join();
                            var color;
                            if (campaign['mismatch_impression'] == 0) {
                                color = 'rgba(200, 247, 197, 0.5)';
                            } else if (campaign['mismatch_impression'] == 1) {
                                color = 'rgba(231, 76, 60, 0.5)';
                            } else {
                                color = 'rgba(245, 230, 83, 0.5)';
                            }
                            if (data.logged_in_user.profile_id == 3) {
                                ro_detail = ro_detail + '<td>';
                                if (campaign['derived_campaign_status'] == 'pending_approval') {
                                    ro_detail = ro_detail + "<input type='checkbox' class='campaigns_to_cancel' value='" + campaign['campaign_id'] + "'>";
                                }
                                ro_detail = ro_detail + '</td>';
                            }
                            ro_detail = ro_detail + "<td  style='background-color:" + color + " '><div style='width:90px;word-wrap:break-word;'>" + campaign['campaign_name'] + "</div></td>" +
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
                        ro_detail = ro_detail + '</tbody>';
                    } else if (data.search_value_set == 1) {
                        ro_detail = ro_detail + "<h2 style='text-align:center;'>No Result Found !</h2>";
                    } else {
                        ro_detail = ro_detail + '<h2 style="text-align:center;">No Active Campaign !</h2>';
                    }

                    $('#Option_Modal_body').html(ro_detail);
                },
                error: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert('could not load');
                }

            })
        });

        //for opening channels schedule
        $(document).on("click", "#channels_schedule_span", function () {
            $.ajax(BASE_URL + '/ro_manager/channels_schedule', {
                type: 'POST',
                data: {
                    "order_id": internal_ro_no,
                    "edit": "1",
                    "id": ro_id

                },
                dataType:'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success: function (responsedata) {
                    var data = responsedata.Data.html;
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#option_modal_title').text('Channels Schedule');
                    $('#optionModal').modal('show');
                    $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                    $('#Option_Modal_body').html(data);
                },
                error: function (data) {
                    alert("error in loading");
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                }

            })
        });
        //for opening nw_ro_payment status
        $(document).on("click", "#nw_ro_payment_span", function () {

            $.ajax(BASE_URL + '/ro_manager/nw_ro_payment', {
                type: 'POST',
                data: {
                    "order_id": internal_ro_no,
                    "edit": "1",
                    "id": ro_id

                },
                dataType:'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success: function (responsedata) {
                    console.log(responsedata);
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#option_modal_title').text('Network Ro Payment');
                    $('#optionModal').modal('show');
                    $("#optionModal").data('bs.modal')._config.backdrop = 'static';
                    $('#Option_Modal_body').html(responsedata.Data.html);

                },
                error: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                }

            })
        });

        //To calculate the value of spot amount using last rate
        $(document).on("blur", ".spotclass", function (e) {
            var spot_rate = Number(e.currentTarget.value);
            var spot_rate_id = e.currentTarget.id;
            var id_arr = spot_rate_id.split('_');
            var channel_id = id_arr[1];
            var index = id_arr[0];
            var spot_amount_id = index + "_" + channel_id + "_spot_amount";
            var total_spot_ad_sec_id = index + "_" + channel_id + "_total_spot_ad_seconds";
            var total_spot_ad_seconds = Number($('#' + total_spot_ad_sec_id).text());
            if (spot_rate < 0 || isNaN(spot_rate)) {
                spot_rate = 0;
                $('#' + spot_rate_id).val(0);
            }
            var new_spot_amount = total_spot_ad_seconds * spot_rate / 10;
            $('#' + spot_amount_id).val(new_spot_amount.toFixed(2));
            calculate_network_amount(schedule_data[index].channels_data_array, index);
        });

        //to calculate the value of last rate using spot amount
        $(document).on("blur", ".spotamountclass", function (e) {
            var spot_amount = Number(e.currentTarget.value);
            var spot_amount_id = e.currentTarget.id;
            var id_arr = spot_amount_id.split('_');
            var channel_id = id_arr[1];
            var index = id_arr[0];
            var spot_rate_id = index + "_" + channel_id + "_spot_rate";
            var total_spot_ad_sec_id = index + "_" + channel_id + "_total_spot_ad_seconds";
            var total_spot_ad_seconds = Number($('#' + total_spot_ad_sec_id).text());
            if (spot_amount < 0 || isNaN(spot_amount)) {
                spot_amount = 0;
                $('#' + spot_amount_id).val(0.0);
            }
            var new_spot_rate = spot_amount * 10 / total_spot_ad_seconds;
            $('#' + spot_rate_id).val(new_spot_rate.toFixed(2));
            calculate_network_amount(schedule_data[index].channels_data_array, index);
        });

        //to calculate the value of banner amount using last rate
        $(document).on("blur", ".bannerclass", function (e) {
            var banner_rate = Number(e.currentTarget.value);
            var banner_rate_id = e.currentTarget.id;
            var id_arr = banner_rate_id.split('_');
            var channel_id = id_arr[1];
            var index = id_arr[0];
            var banner_amount_id = index + "_" + channel_id + "_banner_amount";
            var total_banner_ad_sec_id = index + "_" + channel_id + "_total_banner_ad_seconds";
            var total_banner_ad_seconds = Number($('#' + total_banner_ad_sec_id).text());
            if (banner_rate < 0 || isNaN(banner_rate)) {
                banner_rate = 0;
                $('#' + banner_rate_id).val(0);
            }
            var new_banner_amount = total_banner_ad_seconds * banner_rate / 10;
            $('#' + banner_amount_id).val(new_banner_amount.toFixed(2));
            calculate_network_amount(schedule_data[index].channels_data_array, index);

        });

        //to calculate the value of last rate using banner amount
        $(document).on("blur", ".banneramountclass", function (e) {
            var banner_amount = Number(e.currentTarget.value);
            var banner_amount_id = e.currentTarget.id;
            var id_arr = banner_amount_id.split('_');
            var channel_id = id_arr[1];
            var index = id_arr[0];
            var banner_rate_id = index + "_" + channel_id + "_banner_rate";
            var total_banner_ad_sec_id = index + "_" + channel_id + "_total_banner_ad_seconds";
            var total_banner_ad_seconds = Number($('#' + total_banner_ad_sec_id).text());
            if (banner_amount < 0 || isNaN(banner_amount)) {
                banner_amount = 0;
                $('#' + banner_amount_id).val(0.0);
            }
            var new_banner_rate = banner_amount * 10 / total_banner_ad_seconds;
            $('#' + banner_rate_id).val(new_banner_rate.toFixed(2));
            calculate_network_amount(schedule_data[index].channels_data_array, index);

        });

        //calculating network payout  using network share
        $(document).on("blur", ".networkshareclass", function (e) {
            var network_share_id = e.currentTarget.id;
            var nw_share_id_arr = network_share_id.split("_");
            var nw_index = nw_share_id_arr[0];
            calculate_network_payout(nw_index);

        });

        //calculating amount using network payout
        $(document).on("blur", ".networkpayoutclass", function (e) {
            var nw_payout_id = e.currentTarget.id;
            var nw_payout = Number(e.currentTarget.value);
            var nw_id_arr = nw_payout_id.split("_");
            var nw_index = nw_id_arr[0];
            var old_nw_amount = Number($("#" + nw_index + "_network_amount").text());
            var nw_share = Number($("#" + nw_index + "_network_share").val());
            if (nw_payout > 0 && nw_share == 0) {
                $('#' + nw_payout_id).val("0");
            } else if (nw_payout == 0 && nw_share > 0) {
                calculate_network_payout(nw_index);
            } else if (nw_payout > 0 && nw_share > 0) {
                var new_nw_amount = nw_payout * 100 / nw_share;
                $('#' + nw_index + "_network_amount").text(new_nw_amount.toFixed(2));
                $.map(schedule_data[nw_index].channels_data_array, function (channel, index) {
                    if (channel.total_spot_ad_seconds > 0) {
                        var old_spot_amount = Number($("#" + nw_index + "_" + channel.channel_id + "_spot_amount").val());
                        var percentage = old_spot_amount / old_nw_amount;
                        var new_spot_amount = percentage * new_nw_amount;
                        $("#" + nw_index + "_" + channel.channel_id + "_spot_amount").val(new_spot_amount.toFixed(2));
                        var total_spot_ad_seconds = Number($("#" + nw_index + "_" + channel.channel_id + "_total_spot_ad_seconds").text());
                        var new_spot_rate = new_spot_amount * 10 / total_spot_ad_seconds;
                        $("#" + nw_index + "_" + channel.channel_id + "_spot_rate").val(new_spot_rate.toFixed(2));

                    }
                    if (channel.total_banner_ad_seconds > 0) {
                        var old_banner_amount = Number($("#" + nw_index + "_" + channel.channel_id + "_banner_amount").val());
                        var percentage = old_banner_amount / old_nw_amount;
                        var new_banner_amount = percentage * new_nw_amount;
                        $("#" + nw_index + "_" + channel.channel_id + "_banner_amount").val(new_banner_amount.toFixed(2));
                        var total_banner_ad_seconds = Number($("#" + nw_index + "_" + channel.channel_id + "_total_banner_ad_seconds").text());
                        var new_banner_rate = new_banner_amount * 10 / total_banner_ad_seconds;
                        $("#" + nw_index + "_" + channel.channel_id + "_banner_rate").val(new_banner_rate.toFixed(2));
                    }

                });
                calculate_total_network_payout();
            }


        });
        //view/modify
        $(document).on('click', '#view_modify_span', function () {
            $.ajax(BASE_URL + '/ro_manager/add_other_expenses', {
                type: 'POST',
                data: {
                    "external_ro": external_ro,
                    "internal_ro": internal_ro_no,
                    "edit": 0,
                    "am_ro_id": ro_id
                },
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                dataType:'json',
                success: function (data) {
                    console.log("ss");
                    console.log(data);
                    $('#confirm_Modal_body').html('');
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#confirmModal').modal('show');
                    $("#confirmModal").data('bs.modal')._config.backdrop = 'static';
                    $('#confirm_Modal_body').html(data.Data.html);
                },
                error: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert("error in loading");
                }
            })
        });
        //cancel channel by priority
        $(document).on("change", ".prioritycheckbox", function (e) {
            var priority_checkbox_id = e.currentTarget.id;
            console.log(priority_checkbox_id);
            var priority_checkbox_id_arr = priority_checkbox_id.split('_');
            var market_id = priority_checkbox_id_arr[0];
            var priority_val = priority_checkbox_id_arr[1];
            if ($("input[id^='" + market_id + "']:checked").length == 0) {
                alert('Use account Manager to cancel market');
                $('#' + priority_checkbox_id).prop('checked', true);
                return false;
            }
            if ($('#' + priority_checkbox_id).is(':checked')) {
                $.map(market_priority_detail[market_id].channel_priority[priority_val], function (value, index) {
                    var channel_checkbox_id = index + "_checkbox";
                    if ($("input[id$='" + channel_checkbox_id + "']").length > 0) {
                        $.map($("input[id$='" + channel_checkbox_id + "']"), function (value) {
                            if ($(value).is(':checked')) {
                                //nothing
                            } else {
                                $(value).prop('checked', true);
                                $(value).trigger('change');
                            }
                        })
                    }
                })

            } else {
                //     console.log($('.prioritycheckbox:not(:checked)').length);
                $.map(market_priority_detail[market_id].channel_priority[priority_val], function (value, index) {
                    var channel_checkbox_id = index + "_checkbox";
                    if ($("input[id$='" + channel_checkbox_id + "']").length > 0) {
                        $.map($("input[id$='" + channel_checkbox_id + "']"), function (value) {
                            if ($(value).is(':checked')) {
                                $(value).prop('checked', false);
                                $(value).trigger('change');

                            }
                        })
                    }

                })

            }
        });

        //Reset btn
        $(document).on("click", "#reset_approve_btn", function () {
            $('#approve_form').trigger('reset');
            var total_nw_payout = 0;
            $.map(schedule_data, function (network, nw_index) {
                var nw_amount = 0;
                $.map(network.channels_data_array, function (channel, index) {
                    $('#' + nw_index + '_' + channel.channel_id + '_checkbox').attr('data', '0');
                    $('#' + nw_index + '_' + channel.channel_id + '_checkbox_td').css('background-color', 'white');
                    if (channel.total_spot_ad_seconds > 0) {
                        $('#' + nw_index + '_' + channel.channel_id + '_spot_amount').attr('disabled', false);
                        $('#' + nw_index + '_' + channel.channel_id + '_spot_rate').attr('disabled', false);
                        nw_amount = nw_amount + Number($('#' + nw_index + '_' + channel.channel_id + '_spot_amount').val());
                    }
                    if (channel.total_banner_ad_seconds > 0) {
                        $('#' + nw_index + '_' + channel.channel_id + '_banner_amount').attr('disabled', false);
                        $('#' + nw_index + '_' + channel.channel_id + '_banner_rate').attr('disabled', false);
                        nw_amount = nw_amount + Number($('#' + nw_index + '_' + channel.channel_id + '_banner_amount').val())
                    }

                })
                total_nw_payout = total_nw_payout + Number($('#' + nw_index + '_network_payout').val());
                $('#' + nw_index + '_network_amount').text(nw_amount);
                $('#' + nw_index + '_network_share').attr('disabled', false);
                $('#' + nw_index + '_network_payout').attr('disabled', false);

            });
            console.log("total_nw_payout " + total_nw_payout);
            $('#total_network_payout').text(total_nw_payout);
            var actual_net_amount = Number($('#actual_net_amount').text());
            console.log("actual_net_amount" + actual_net_amount);
            var surewaves_share = actual_net_amount - total_nw_payout;
            console.log("surewaves_share " + surewaves_share);
            var net_revenue = Number($('#net_revenue').text());
            var surewaves_share_per = surewaves_share / net_revenue * 100;
            $('#surewaves_share').text(surewaves_share);
            $('#surewaves_share_per').text(surewaves_share_per.toFixed(2));

        });

        //cancel channel
        $(document).on("change", ".checkboxclass", function (e) {
            var checkbox_id = e.currentTarget.id;
            var checkbox_id_arr = checkbox_id.split("_");
            var nw_index = checkbox_id_arr[0];
            var channel_id = checkbox_id_arr[1];
            var checkbox_td_id = nw_index + "_" + channel_id + "_" + "checkbox_td";
            if ($('#' + checkbox_id).is(':checked')) {
                $('#' + checkbox_id).attr('data', '0');
                $('#' + checkbox_td_id).css('background-color', 'white');
                var old_nw_amount = Number($('#' + nw_index + '_network_amount').text());
                var new_nw_amount = 0;
                var channel_spot_amount = 0;
                var channel_banner_amount = 0;
                if ($('#' + nw_index + "_" + channel_id + "_total_spot_ad_seconds").length > 0) {
                    channel_spot_amount = Number($('#' + nw_index + "_" + channel_id + "_spot_amount").val());
                    $("#" + nw_index + "_" + channel_id + "_spot_rate").attr('disabled', false);
                    $("#" + nw_index + "_" + channel_id + "_spot_amount").attr('disabled', false);

                }
                if ($('#' + nw_index + "_" + channel_id + "_total_banner_ad_seconds").length > 0) {
                    channel_banner_amount = Number($('#' + nw_index + "_" + channel_id + "_banner_amount").val());
                    $("#" + nw_index + "_" + channel_id + "_banner_rate").attr('disabled', false);
                    $("#" + nw_index + "_" + channel_id + "_banner_amount").attr('disabled', false);
                }
                var old_nw_payout = Number($('#' + nw_index + "_network_payout").val());
                new_nw_amount = old_nw_amount + channel_spot_amount + channel_banner_amount;
                $('#' + nw_index + "_network_amount").text(new_nw_amount.toFixed(2));
                var nw_share = Number($('#' + nw_index + "_network_share").val());
                var new_nw_payout = new_nw_amount * nw_share / 100;
                $('#' + nw_index + "_network_payout").val(new_nw_payout);
                var addition_amount = new_nw_payout - old_nw_payout;
                var old_total_nw_payout = Number($('#total_network_payout').text());
                var new_total_nw_payout = old_total_nw_payout + addition_amount;
                $('#total_network_payout').text(new_total_nw_payout.toFixed(2));
                var actual_net_amount = Number($('#actual_net_amount').text());
                var surewaves_share = actual_net_amount - new_total_nw_payout;
                var net_revenue = Number($('#net_revenue').text()) * SERVICE_TAX;
                var surewaves_share_per = surewaves_share / net_revenue * 100;
                $('#surewaves_share').text(surewaves_share);
                $('#surewaves_share_per').text(surewaves_share_per.toFixed(2));
                $("#" + nw_index + "_network_share").attr('disabled', false);
                $("#" + nw_index + "_network_payout").attr('disabled', false);

            } else {
                $('#' + checkbox_id).attr('data', '1');
                $('#' + checkbox_td_id).css('background-color', 'rgba(246, 71, 71,0.6)');
                var old_nw_amount = Number($('#' + nw_index + '_network_amount').text());

                var new_nw_amount = 0;
                var channel_spot_amount = 0;
                var channel_banner_amount = 0;
                if ($('#' + nw_index + "_" + channel_id + "_total_spot_ad_seconds").length > 0) {
                    channel_spot_amount = Number($('#' + nw_index + "_" + channel_id + "_spot_amount").val());
                    $("#" + nw_index + "_" + channel_id + "_spot_rate").attr('disabled', true);
                    $("#" + nw_index + "_" + channel_id + "_spot_amount").attr('disabled', true);
                }
                if ($('#' + nw_index + "_" + channel_id + "_total_banner_ad_seconds").length > 0) {

                    channel_banner_amount = Number($('#' + nw_index + "_" + channel_id + "_banner_amount").val());
                    $("#" + nw_index + "_" + channel_id + "_banner_rate").attr('disabled', true);
                    $("#" + nw_index + "_" + channel_id + "_banner_amount").attr('disabled', true);
                }

                var old_nw_payout = Number($('#' + nw_index + "_network_payout").val());
                new_nw_amount = Number(old_nw_amount) - Number(channel_spot_amount) - Number(channel_banner_amount);
                $('#' + nw_index + "_network_amount").text(new_nw_amount.toFixed(2));
                var nw_share = Number($('#' + nw_index + "_network_share").val());
                var new_nw_payout = new_nw_amount * nw_share / 100;
                $('#' + nw_index + "_network_payout").val(new_nw_payout);
                var removal_amount = old_nw_payout - new_nw_payout;
                var old_total_nw_payout = Number($('#total_network_payout').text());
                var new_total_nw_payout = old_total_nw_payout - removal_amount;
                $('#total_network_payout').text(new_total_nw_payout.toFixed(2));
                var actual_net_amount = Number($('#actual_net_amount').text());
                var surewaves_share = actual_net_amount - new_total_nw_payout;
                var net_revenue = Number($('#net_revenue').text()) * SERVICE_TAX;
                var surewaves_share_per = surewaves_share / net_revenue * 100;
                $('#surewaves_share').text(surewaves_share);
                $('#surewaves_share_per').text(surewaves_share_per.toFixed(2))

                $("#" + nw_index + "_network_share").attr('disabled', true);
                $("#" + nw_index + "_network_payout").attr('disabled', true);


            }

        })


    <?php
                  $pending_approved_campaign = $this->session->userdata('pending_approved_campaign');
                  if ( isset($pending_approved_campaign) && !empty($pending_approved_campaign) ) { ?>
                  show_pending_approved_campaign();
                  <? 
                  $this->session->unset_userdata("pending_approved_campaign");
                 } ?>
  });
    function show_pending_approved_campaign(){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/show_pending_approved_campaign/',iframe:true, width: '530px', height:'280px'}); 
    }
    function calculate_network_amount(channel_arr, nw_index) {
        var amount = 0;
        var spot_amount_id;
        var banner_amount_id;
        $.map(channel_arr, function (value, index) {
            var channel_id = value.channel_id;
            if (value.total_spot_ad_seconds > 0) {
                spot_amount_id = nw_index + "_" + value.channel_id + "_spot_amount";
                var spot_amount = Number($("#" + spot_amount_id).val());
                amount = amount + spot_amount;
            }
            if (value.total_banner_ad_seconds > 0) {
                banner_amount_id = nw_index + "_" + value.channel_id + "_banner_amount";
                var banner_amount = Number($("#" + banner_amount_id).val());
                amount = amount + banner_amount;
            }
        });
        $('#' + nw_index + "_network_amount").text(amount);
        calculate_network_payout(nw_index);
    }

    function calculate_network_payout(nw_index) {
        var nw_payout = 0;
        var nw_payout_id = nw_index + "_network_payout";
        var nw_amount_id = nw_index + "_network_amount";
        var nw_share_id = nw_index + "_network_share";
        var nw_share = Number($('#' + nw_share_id).val());
        var nw_amount = Number($('#' + nw_amount_id).text());
        if (nw_amount > 0 && nw_share > 0) {
            nw_payout = nw_amount * nw_share / 100;
        }
        $('#' + nw_payout_id).val(nw_payout);
        calculate_total_network_payout();

    }

    function calculate_total_network_payout() {
        var total_nw_payout = 0;
        $.map(schedule_data, function (value, index) {
            var nw_payout_id = index + "_" + "network_payout";
            var nw_payout = Number($('#' + nw_payout_id).val());
            if (nw_payout > 0) {
                total_nw_payout = total_nw_payout + nw_payout;
            }

        });
        $('#total_network_payout').text(total_nw_payout.toFixed(2));
        var actual_net_amount = Number($('#actual_net_amount').text());
        var surewaves_share = actual_net_amount - total_nw_payout;
        var agency_commission_amount = Number($('#agency_commission_amount').text());
        var net_revenue = Number($('#net_revenue').text()) * SERVICE_TAX;
        var surewaves_share_per = surewaves_share / net_revenue * 100;
        $('#surewaves_share').text(surewaves_share);
        $('#surewaves_share_per').text(surewaves_share_per.toFixed(2));

    }
    function createApproveData() {
        var data = {};
        var network_arr = [];
        $.map(schedule_data, function (network, nw_index) {
            var network_obj = {};
            network_obj['network_id'] = network.network_id;
            network_obj['network_name'] = network.network_name;
            network_obj['market_name'] = network.market_name;
            network_obj['market_id'] = network.market_id;
            var channels_data_arr = [];
            $.map(network.channels_data_array, function (channel, ind) {
                var channel_obj = {};
                var channel_id = channel.channel_id;

                // creating network wise channel details
                channel_obj['channel_id'] = channel_id;
                channel_obj['channel_name'] = channel.channel_name;
                // channel_obj['to_play_spot'] = channel.to_play_spot;
                // channel_obj['to_play_banner'] = channel.to_play_banner;
                // channel_obj['spot_region_id'] = channel.spot_region_id;
                channel_obj['total_spot_ad_seconds'] = channel.total_spot_ad_seconds;
                // channel_obj['banner_region_id'] = channel.banner_region_id;
                channel_obj['total_banner_ad_seconds'] = channel.total_banner_ad_seconds;


                channel_obj['channel_spot_avg_rate'] = 0;
                channel_obj['channel_banner_avg_rate'] = 0;

                channel_obj['channel_spot_amount'] = 0;
                channel_obj['channel_banner_amount'] = 0;

                // channel_obj['channel_spot_reference_rate'] = 0;
                // channel_obj['channel_banner_reference_rate'] = 0;

                if (channel.total_spot_ad_seconds > 0) {
                    channel_obj['channel_spot_avg_rate'] = $('#' + nw_index + '_' + channel_id + '_spot_rate').val();
                    channel_obj['channel_spot_amount'] = $('#' + nw_index + '_' + channel_id + '_spot_amount').val();
                    // channel_obj['channel_spot_reference_rate'] = channel.channel_spot_reference_rate;
                }
                if (channel.total_banner_ad_seconds > 0) {

                    channel_obj['channel_banner_avg_rate'] = $('#' + nw_index + '_' + channel_id + '_banner_rate').val();
                    channel_obj['channel_banner_amount'] = $('#' + nw_index + '_' + channel_id + '_banner_amount').val();
                    // channel_obj['channel_banner_reference_rate'] = channel.channel_banner_reference_rate;
                }

                // channel_obj['customer_share'] = channel.customer_share;
                // channel_obj['approved'] = channel.approved;
                // channel_obj['additional_campaign'] = channel.additional_campaign;
                if ($('#' + nw_index + '_' + channel_id + '_checkbox').attr('data') == 0) {
                    channel_obj['cancel_channel'] = 0;
                } else {
                    channel_obj['cancel_channel'] = 1;
                }
                channels_data_arr.push(channel_obj);
            });
            var nw_payout = $('#' + nw_index + '_network_payout').val();
            var revenue_sharing = $('#' + nw_index + '_network_share').val();
            network_obj['customer_share'] = revenue_sharing;
            network_obj['channels_data_array'] = channels_data_arr;
            // network_obj['nw_payout'] = nw_payout;
            // network_obj['approved'] = network.approved;
            network_arr.push(network_obj);
        });
        data['internal_ro'] = internal_ro_no;
        // data['gross_amount'] = Number($('#ro_amount').text());
        // data['agency_commission'] = Number($('#agency_commission_amount').text());
        // data['net_amount'] = Number($('#net_amount').text());
        // data['agency_rebate'] = Number($('#agency_rebate').text());
        // data['other_expenses'] = Number($('#other_expenses').text());
        // data['actual_net_amount'] = Number($('#actual_net_amount').text());
        // data['net_revenue'] = Number($('#net_revenue').text());
        // data['total_network_payout'] = Number($('#total_network_payout').text());
        // data['net_contribution_amount'] = Number($('#surewaves_share').text());
        // data['net_contribution_amount_per'] = Number($('#surewaves_share_per').text());
        data['client_name'] = client_name;
        data['make_good_type'] = make_good_type;
        data['networks'] = network_arr;

        return data;

    }

</script>
	
