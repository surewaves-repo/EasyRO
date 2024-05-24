<html>
<head>
    <script type="text/javascript" language="javascript">

        $(document).ready(function () {
            $("#txt_cancel_date_market").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                onClose: function (dateText, inst) {

                }
            }).datepicker("option", "minDate", "1");
        });

        function getMarketsForCancellation() {
            var internal_ro = $('#hid_internal_ro').val();
            var cancelDate = $('#txt_cancel_date_market').val();
            $.ajax({
                type: 'POST',
                url: '/surewaves_easy_ro/account_manager/getMarketsForCancellation',
                data: {
                    internal_ro: internal_ro,
                    cancelDate: cancelDate
                },
                beforeSend:function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    if (data != '') {
                        $('#filtered_markets').html(data);
                    } else {
                        $('#filtered_markets').html("No Markets");
                    }
                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert("could not fetch markets");
                }
            });
        }

        function show_hide_req_button() {
            if ($('.markets_to_cancel:checked').length > 0) {
                $('#reason_controls').show();
                $('#req_controls').show();
            } else {
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

                $('#spot_' + market).val(spot_after_cancel);
                $('#banner_' + market).val(banner_after_cancel);

                $('#spot_fct_' + market).val(spot_fct_after_cancel);
                $('#banner_fct_' + market).val(banner_fct_after_cancel);
                //}
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


    <div class="wrapper">        <!-- wrapper begins -->

        <!-- #header ends -->

        <div class="block">
            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>Customer Release Order</h2>



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

             <!-- .block_content ends -->

            <div class="block_content" style="height: auto">
                <form id='cancel_market_form' >
                    <p>
                        <label>Cancel From :<span style='color:#F00;'> *</span> </label>
                        <input type='text' id='txt_cancel_date_market' class='text date_picker' readonly='readonly'
                               name='txt_cancel_date' style='width:250px;'
                               onchange="javascript:getMarketsForCancellation()"/>
                        <span class="fa fa-calendar"></span>
                    </p>
                    <div id="filtered_markets"></div>
                    <input type="hidden" name="hid_cancel_market_type" value="cancel_market">
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


</body>
</html>
