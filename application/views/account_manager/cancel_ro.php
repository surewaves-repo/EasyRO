<html>
<head>
    <script type="text/javascript" src="/surewaves_easy_ro/js/Utils.js"></script>
    <script type="text/javascript" language="javascript">

        $('#cancel_ro_btn').click(function(){
           
            $('#cancel_ro_btn').prop('disabled',true);
            if ($('#txt_cancel_date_ro').val() == "")
            {
                $('#cancel_ro_btn').prop('disabled',false);
           
                alert("Please enter date of cancel.");
                return false;
            }
            if ($('#txt_reason').val() == "")
            {
                $('#cancel_ro_btn').prop('disabled',false);
                alert("Please enter reason of cancel.");
                return false;
            }
            var txt_reason = $('#txt_reason').val();
            var check_input = Utility.checkInvoice(txt_reason);

            if (!check_input)
            {
                $('#cancel_ro_btn').prop('disabled',false);
                alert("Invalid Reason for Cancellation");
                $('#txt_reason').focus();
                return false;
            }

            if ($('#txt_inv_inst').val() == "") {
                $('#cancel_ro_btn').prop('disabled',false);
                alert("Please enter invoice instruction.");
                return false;
            }
            var txt_inv_inst = document.getElementById('txt_inv_inst').value;
            var check_input = Utility.checkInvoice(txt_inv_inst);
            if (!check_input) {
                $('#cancel_ro_btn').prop('disabled',false);
                alert("Invalid Invoice Instruction");
                $('#txt_inv_inst').focus();
                return false;
            }
            var data=$('#cancel_ro_form').serializeArray();
            $.ajax(BASE_URL+'/account_manager/post_cancelt_ro',{
                type:'POST',
                data:data,
                dataType:'json',
                beforeSend:function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                success:function(data)
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#cancellation_div').css('display','block');
                    $('#status_td').text('Cancel Requested');
                    $('#cancel_ro_request_span').css('display','none');
                    $('#cancel_markets_span').css('display','none');
                    $('#cancel_markets_by_brand_span').css('display','none');
                    $('#cancel_markets_by_content_span').css('display','none');
                    alert(data.Message);
                    $('#dateModal').modal('hide');

                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert("Cancel request could not proceed");
                    $('#cancel_ro_btn').prop('disabled',false);
                }


            })
        });

        $(document).ready(function () {
            console.log("inside");
            $("#txt_cancel_date_ro").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                onClose: function (dateText, inst) {
                    get_gross_after_cancellation(this.value);
                }
            }).datepicker("option", "minDate", "1");
        });

        function get_gross_after_cancellation(cancel_date) {

            var ro_id = $("#hid_id").val();
            $.ajax({
                type: 'POST',
                url: '/surewaves_easy_ro/account_manager/get_gross_after_cancellation',
                data: {
                    cancel_date: cancel_date,
                    ro_id: ro_id
                },
                success: function (data) {
                    if (data != '' ) {
                        $("#txt_inv_amnt").val(data);
                    }
                }
            });

        }
    </script>

</head>
<body>
<div class="block">

    <div >
        <form id="cancel_ro_form">
            <table cellpadding="0" cellspacing="0" width="100%">

                <tr>
                    <td width="30%">Cancel From<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_cancel_date_ro" readonly="readonly" name="txt_cancel_date"
                               style="width:220px;"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td>Reason for Cancellation<span style="color:#F00;"> *</span></td>
                    <td> :</td>
                    <td>
                        <input type="text" id="txt_reason" name="txt_reason" style="width:220px;"/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="20%">Invoice Instruction<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_inv_inst" name="txt_inv_inst" style="width:220px;"/>
                    </td>
                    <td width="50%">&nbsp;</td>
                </tr>

                <tr>
                    <td width="20%">Invoice Amount(Gross)</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_inv_amnt" name="txt_inv_amnt" style="width:220px;" readonly/>
                    </td>
                    <td width="50%">&nbsp;</td>
                </tr>


                <tr>
                    <td style="border-bottom: none;">
                        <input type="hidden" name="hid_id" id="hid_id" value="<?php echo $id ?>"/>
                        <input type="hidden" name="hid_user_id" id="hid_user_id"
                               value="<?php echo $logged_in_user['user_id'] ?>"/>
                        <input type="hidden" name="hid_ext_ro" id="hid_ext_ro" value="<?php echo $external_ro ?>"/>
                        <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
                        <input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro ?>">
                        <input type="button" id='cancel_ro_btn' class="submit" value="Submit"/>
                    </td>
                <tr>

            </table>


        </form>
    </div>        <!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>        <!-- .login ends -->

</body>
</html>

