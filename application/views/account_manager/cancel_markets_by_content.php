<html>
<head>
    <script type="text/javascript" language="javascript">

        $(document).on('click','#req_market_can',function(){
            if ($('#txt_cancel_date').val() == "") {
                alert("Please enter date of cancel.");
                return false;
            }
            if ($('#reason_can').val() == "") {
                alert("Please enter Reason for Cancellation");
                $('#reason_can').focus();
                return false;
            }
            var data=$('#cancel_market_form').serializeArray();
            $.ajax(BASE_URL+'/account_manager/cancel_market_content_form/',{
                type:'POST',
                data:data,
                dataType:'json',
                beforeSend:function(){
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success:function(data)
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#dateModal').modal('hide');
                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert('could not cancel market');
                }
            });
        });

        $(document).ready(function () {
            $("#txt_cancel_date").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                onClose: function (dateText, inst) {

                }
            }).datepicker("option", "minDate", "2");
        });


        function getMarketsForContent(content) {
            var internal_ro = $('#hid_internal_ro').val();
            $.ajax({
                type: 'POST',
                url: '/surewaves_easy_ro/account_manager/getMarketsForContent',
                data: {
                    content: content,
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

                $('#spot_' + market).val(spot_price);
                $('#banner_' + market).val(banner_price);

                $('#spot_fct_' + market).val(spot_fct);
                $('#banner_fct_' + market).val(banner_fct);

                if ($('.markets_to_cancel:checked').length == 0) {
                    $('#cancel_message').hide();
                }
            }
        }


    </script>

</head>
<body>
<div class="block">


    <div class="block_content">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <th>Customer RO Number</th>
                <th>Agency Name</th>
                <th>Advertise name</th>
                <th>Brand Name</th>
                <th>RO Start Date</th>
                <th>RO End Date</th>
            </tr>
            <tr>
                <td> <?php echo $am_ext_ro ?><br/> <?php echo '(Internal RO Number:' . $internal_ro . ")"; ?>
                </td>
                <td><?php echo $agency ?> &nbsp;</td>
                <td><?php echo $client ?></td>
                <td><?php echo $brand ?></td>
                <td><?php print date('d-M-Y', strtotime($camp_start_date)); ?> </td>
                <td><?php print date('d-M-Y', strtotime($camp_end_date)); ?> </td>

            </tr>

        </table>
        <br>

        <div id="cancel_message" style="display: none;text-align: center">
            <span style="color:#F00;">This is the complete cancellation of market (Spot rate = 0, banner rate = 0)</span>
        </div>


    </div>    <!-- .block_content ends -->

    <div class="block_content" style="height: auto">
        <label>Select Content:</label>
        <select id="content" onchange="javascript:getMarketsForContent(this.value)">
            <option value="">--</option>
            <?php foreach ($all_contents as $content) { ?>
                <option value="<?php echo $content['caption_name'] ?>"><?php echo $content['caption_name'] ?></option>
            <?php } ?>
        </select>
    </div>    <!-- .block_content ends -->

    <div class="block_content" style="height: auto">
        <form id='cancel_market_content_form' >

            <div id="filtered_markets">

            </div>

            <input type="hidden" name="hid_cancel_market_type" value="cancel_content">
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

</body>
</html>







