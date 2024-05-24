<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 12/10/15
 * Time: 1:43 PM
 */

include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>

<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div id="hld">

    <div class="wrapper">        <!-- wrapper begins -->


        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"
                                               style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png"
                 style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a
                        href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>        <!-- #header ends -->

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>Cancel Markets By Brand</h2>
            </div>

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
                    </tr>
                    <tr>
                        <td> <?php echo $am_ext_ro ?><br/> <?php echo '(Internal RO Number:' . $internal_ro . ")"; ?>
                        </td>
                        <td><?php echo $agency ?> &nbsp;</td>
                        <td><?php echo $client ?></td>
                        <td><?php echo $brand ?></td>
                        <td><?php print date('d-M-Y', strtotime($camp_start_date)); ?> </td>
                        <td><?php print date('d-M-Y', strtotime($camp_end_date)); ?> </td>
                        <td>
                            <a href="javascript:approve('<?php echo rtrim(base64_encode($internal_ro), '=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Approval
                                Page</a></td>
                    </tr>

                </table>
                <br>

                <div id="cancel_message" style="display: none;text-align: center">
                    <span style="color:#F00;">This is the complete cancellation of market (Spot rate = 0, banner rate = 0)</span>
                </div>


            </div>    <!-- .block_content ends -->

            <div class="block_content" style="height: auto">
                <label>Select Brand:</label>
                <select id="content" onchange="javascript:getMarketsForBrand(this.value)">
                    <option value="">--</option>
                    <?php foreach ($all_brands as $brand) { ?>
                        <option value="<?php echo $brand['brand_id'] ?>"><?php echo $brand['brand_new'] ?></option>
                    <?php } ?>
                </select>
            </div>    <!-- .block_content ends -->

            <div class="block_content" style="height: auto">
                <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_cancel_markets" method="post"
                      enctype="multipart/form-data">

                    <div id="filtered_markets">

                    </div>

                    <!--<table cellpadding="0" cellspacing="0" width="100%">

                        <tr>
                            <th style="width:5%"></th>
                            <th style="width:30%">Markets</th>
                            <th style="width:40%">Spot Price</th>
                            <th style="width:25%">Banner Price</th>
                        </tr>
                        <?php /*foreach($markets as $mkt){
                            if(!in_array($mkt['market'], $cancelled_markets)){ */ ?>

                                <tr>
                                    <td>
                                        <input type="checkbox" id="cancel_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['market']*/ ?>" onclick="javascript:price_handler('<?php /*echo str_replace(" ", "_", $mkt['market']) */ ?>');show_hide_req_button()"
                                            <?php /*if(in_array($mkt['market'], $cancel_requested_markets)){ echo 'disabled="disabled"';}
                                            else if(in_array($mkt['market'], $cancelled_markets)){echo 'class="markets_cancelled"';}
                                            else { echo 'class="markets_to_cancel"';} */ ?> >
                                    </td>
                                    <td><?php /*echo $mkt['market']*/ ?></td>
                                    <td>
                                        <input type="text" name="markets[<?php /*echo str_replace(" ", "_", $mkt['market']) */ ?>][spot]" readonly class="amount"  id="spot_<?php /*echo str_replace(" ", "_", $mkt['market']) */ ?>"
                                            <?php /*if(in_array($mkt['market'], $cancel_requested_markets)){ echo 'disabled="disabled"';}*/ ?>
                                               value="<?php /*echo $mkt['spot_price']*/ ?>" >
                                    </td>

                                    <td>
                                        <input type="text" name="markets[<?php /*echo str_replace(" ", "_", $mkt['market']) */ ?>][banner]" readonly class="amount" id="banner_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>"
                                            <?php /*if(in_array($mkt['market'], $cancel_requested_markets)){ echo 'disabled="disabled"';}*/ ?>
                                               value="<?php /*echo $mkt['banner_price']*/ ?>" >
                                    </td>
                                </tr>
                                <?php /* */ ?>
                                <input type="hidden" name="markets[<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>][spot_fct]" id="spot_fct_<?php /*echo str_replace(" ", "_", $mkt['market']) */ ?>" value="<?php /*echo $mkt['spot_fct']*/ ?>">
                                <input type="hidden" name="markets[<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>][banner_fct]" id="banner_fct_<?php /*echo str_replace(" ", "_", $mkt['market']) */ ?>" value="<?php /*echo $mkt['banner_fct']*/ ?>">

                                <input type="hidden" id="hid_spot_fct_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['spot_fct']*/ ?>">
                                <input type="hidden" id="hid_banner_fct_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['banner_fct']*/ ?>">

                                <input type="hidden" id="hid_spot_fct_cancel_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['spot_fct_after_cancel']*/ ?>">
                                <input type="hidden" id="hid_banner_fct_cancel_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['banner_fct_after_cancel']*/ ?>">

                                <input type="hidden" id="hid_spot_price_cancel_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['spot_price_after_cancel']*/ ?>">
                                <input type="hidden" id="hid_banner_price_cancel_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['banner_price_after_cancel']*/ ?>">

                                <input type="hidden" id="hid_spot_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['spot_price']*/ ?>">
                                <input type="hidden" id="hid_banner_<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>" value="<?php /*echo $mkt['banner_price']*/ ?>">

                                <input type="hidden" name="markets[<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>][is_cancelled]" id="is_cancelled_<?php /*echo str_replace(" ", "_", $mkt['market']) */ ?>" value="0">
                                <input type="hidden" name="markets[<?php /*echo str_replace(" ", "_", $mkt['market'])*/ ?>][has_campaigns]" id="has_campaigns_<?php /*echo str_replace(" ", "_", $mkt['market']) */ ?>" value="<?php /*echo $mkt['has_campaigns']*/ ?>">
                            <?php /*}} */ ?>

                        <tr id="reason_controls" style="display: none">
                            <td>&nbsp;</td>
                            <td><span id="reason_label">Reason for Cancellation:</span> <span style="color:#F00;"> *</span></td>
                            <td><input type="text" name="reason_can" id="reason_can" class="amount" value="" style="width:250px;"</td>
                        </tr>
                        <tr id="req_controls" style="display: none">
                            <td>&nbsp;</td>
                            <td>
                                <input type="submit" class="submitlong" id="req_market_can" value="Approval Request" onclick="return check_form();" />
                            </td>
                        <tr>

                    </table>-->

                    <input type="hidden" name="hid_cancel_market_type" value="cancel_brand">
                    <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
                    <input type="hidden" name="hid_id" value="<?php echo $id ?>">
                    <input type="hidden" name="hid_internal_ro" id="hid_internal_ro"
                           value="<?php echo $internal_ro; ?>">
                    <input type="hidden" name="hid_order_id"
                           value="<?php echo rtrim(base64_encode($internal_ro), '=') ?>">

                </form>
            </div>        <!-- .block_content ends -->

            <div class="bendl"></div>
            <div class="bendr"></div>

        </div>        <!-- .block ends -->

    </div>
</div>


<?php include_once dirname(__FILE__) . "/inc/footer.inc.php" ?>

<script type="text/javascript" language="javascript">

    $(document).ready(function () {
        $("#txt_cancel_date").datepicker({
            //defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            onClose: function (dateText, inst) {

            }
        }).datepicker("option", "minDate", "2");
    });

    /*function check_input_price_spot(market){
     var spot            = $('#spot_'+market).val();
     var spot_price      = parseFloat(spot).toFixed(2);

     var response_spot = isNormalInteger(spot);
     if(response_spot == false)
     {
     alert("Please enter a positive numerical value");
     setTimeout("$('#spot_"+market+"').focus()",1);
     return false;
     }
     }

     function check_input_price_banner(market){
     var banner          = $('#banner_'+market).val();
     var banner_price    = parseFloat(banner).toFixed(2);

     var response_banner = isNormalInteger(banner);
     if(response_banner == false)
     {
     alert("Please enter a positive numerical value");
     setTimeout("$('#banner_"+market+"').focus()",1);
     return false;
     }
     }

     function isNormalInteger(str) {
     return /^\+?(0|[0-9]\d*|[0-9]\d*[.][0-9]{2})$/.test(str);
     }*/

    function getMarketsForBrand(brand) {
        var internal_ro = $('#hid_internal_ro').val();
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/account_manager/getMarketsForBrand',
            data: {
                brand: brand,
                internal_ro: internal_ro
            },
            success: function (data) {
                if (data != '') {
                    $('#filtered_markets').html(data);
                } else {
                    $('#filtered_markets').html("No Markets");
                }
            }
        });
    }

    function show_hide_req_button() {
        if ($('.markets_to_cancel:checked').length > 0) {
            $('#date_controls').show();
            $('#reason_controls').show();
            $('#req_controls').show();
        } else {
            $('#date_controls').hide();
            $('#reason_controls').hide();
            $('#req_controls').hide();
        }
    }

    function check_form() {
        if ($('#txt_cancel_date').val() == "") {
            alert("Please enter date of cancel.");
            return false;
        }
        if ($('#reason_can').val() == "") {
            alert("Please enter Reason for Cancellation");
            $('#reason_can').focus();
            return false;
        }
        /*if($('.markets_to_cancel:checked').length == $('.markets_to_cancel').length){
         alert("Please use Cancel RO to cancel all Markets");
         return false;
         }*/
    }

    function price_handler(market) {
        var spot_price = $('#hid_spot_' + market).val();
        var banner_price = $('#hid_banner_' + market).val();
        var spot_fct = $('#hid_spot_fct_' + market).val();
        var banner_fct = $('#hid_banner_fct_' + market).val();

        //values after cancelling a market
        var spot_after_cancel = $('#hid_spot_price_cancel_' + market).val();
        var banner_after_cancel = $('#hid_banner_price_cancel_' + market).val();
        var spot_fct_after_cancel = $('#hid_spot_fct_cancel_' + market).val();
        var banner_fct_after_cancel = $('#hid_banner_fct_cancel_' + market).val();

        if ($('#cancel_' + market).is(":checked")) {

            $('#is_cancelled_' + market).val(1);
            if ($('#has_campaigns_' + market).val() == 0) {
                $('#spot_' + market).val(0);
                //$('#spot_'+market).attr('readonly','readonly');
                $('#banner_' + market).val(0);
                //$('#banner_'+market).attr('readonly','readonly');

                if ($('.markets_to_cancel:checked').length > 0) {
                    $('#cancel_message').show();
                }
            } else {
                $('#spot_' + market).val(spot_after_cancel);
                $('#banner_' + market).val(banner_after_cancel);

                $('#spot_fct_' + market).val(spot_fct_after_cancel);
                $('#banner_fct_' + market).val(banner_fct_after_cancel);
            }
        } else {
            $('#is_cancelled_' + market).val(0);
            //$('#spot_'+market).attr("readonly", false);
            //$('#banner_'+market).attr("readonly", false);
            $('#spot_' + market).val(spot_price);
            $('#banner_' + market).val(banner_price);

            $('#spot_fct_' + market).val(spot_fct);
            $('#banner_fct_' + market).val(banner_fct);

            if ($('.markets_to_cancel:checked').length == 0) {
                $('#cancel_message').hide();
            }
        }
    }

    function approve(order_id, edit, id) {
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approve/" + order_id + "/" + edit + "/" + id;
    }
</script>
