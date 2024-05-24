<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div class="block">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Review Cancel RO</h2>
    </div>

    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_cancel_ro_admin_v1" method="post"
              enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="0" width="100%">

                <tr>
                    <td width="30%">Cancel From</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_cancel_date" readonly="readonly" name="txt_cancel_date" readonly
                               value="<?php echo $cancel_start_date; ?>" style="width:220px;"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td>Reason for Cancellation</td>
                    <td> :</td>
                    <td>
                        <input type="text" id="txt_reason" name="txt_reason" style="width:220px;" readonly
                               value="<?php echo $reason; ?>"/>
                    </td>
                    <td>&nbsp;</td>
                </tr>

                <tr>
                    <td width="20%">Invoice Instruction</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_inv_inst" name="txt_inv_inst" style="width:220px;" readonly
                               value="<?php echo $invoice_instruction; ?>"/>
                    </td>
                    <td width="50%">&nbsp;</td>
                </tr>

                <tr>
                    <td width="20%">Invoice Amount(Gross)</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_inv_amnt" name="txt_inv_amnt" style="width:220px;" readonly
                               value="<?php echo $ro_amount; ?>"/>
                    </td>
                    <td width="50%">&nbsp;</td>
                </tr>


                <tr>
                    <td>
                        <input type="hidden" name="hid_id" id="hid_id" value="<?php echo $id ?>"/>
                        <input type="hidden" name="hid_user_id" id="hid_user_id"
                               value="<?php echo $logged_in_user['user_id'] ?>"/>
                        <input type="hidden" name="hid_ext_ro" id="hid_ext_ro" value="<?php echo $external_ro ?>"/>
                        <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
                        <input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro ?>">

                        <input type="submit" class="submit" value="Approve"/>
                    </td>
                <tr>

            </table>


        </form>
    </div>        <!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>        <!-- .login ends -->


<?php include_once dirname(__FILE__) . "/inc/footer.inc.php" ?>
<script type="text/javascript" language="javascript">

    /*function check_form(){
        if($('#txt_cancel_date').val() == ""){
            alert("Please enter date of cancel.");
            return false;
        }
        if($('#txt_reason').val() == ""){
            alert("Please enter reason of cancel.");
            return false;
        }
            var txt_reason = document.getElementById('txt_reason').value;
        var check_input = Utility.checkInvoice(txt_reason);
        if(!check_input)	{
            alert("Invalid Reason for Cancellation");
            document.getElementById('txt_reason').focus();
            return false;
        }
            
            if($('#txt_inv_inst').val() == ""){
            alert("Please enter invoice instruction.");
            return false;
        }
            var txt_inv_inst = document.getElementById('txt_inv_inst').value;
        var check_input = Utility.checkInvoice(txt_inv_inst);
        if(!check_input)	{
            alert("Invalid Invoice Instruction");
            document.getElementById('txt_inv_inst').focus();
            return false;
        }
            
        if($('#txt_inv_amnt').val() == ""){
            alert("Please enter invoice amount.");
            return false;
        }
            var txt_inv_amnt = document.getElementById('txt_inv_amnt').value;
        var check_input = Utility.checkAmount(txt_inv_amnt);
        if(!check_input)	{
            alert("Invalid Invoice Amount");
            document.getElementById('txt_inv_amnt').focus();
            return false;
        }
    }
    $(document).ready(function(){
        $( "#txt_cancel_date" ).datepicker({
            //defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true
        }).datepicker("option", "minDate", "2");
    });*/
</script>
