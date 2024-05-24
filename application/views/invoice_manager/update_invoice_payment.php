<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 9/9/15
 * Time: 12:31 PM
 */

include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<style>
    .cheque_details {
        display: none;
    }
</style>

<div class="block">


    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>

        <h2>Invoice Collection</h2>

    </div>		<!-- .block_head ends -->



    <div class="block_content">

            <form action="<?php echo ROOT_FOLDER ?>/invoice_manager/post_invoice_collection" method="post">
                <table cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <td width="30%">Invoice No.</td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <!--<span><?php /*echo "INV_".str_replace("/","_",$invoice_details[0]['internal_ro'])."_".$invoice_details[0]['invoice_number']*/?></span>-->
                            <span><?php echo $invoice_details[0]['alias_invoice_number']?></span>
                            <input type="hidden" id="txt_inv_no" name="txt_inv_no" style="width:220px;" value="<?php echo $invoice_details[0]['invoice_number']?>" readonly />
                            <input type="hidden" id="txt_inv_alias" name="txt_inv_alias" style="width:220px;" value="<?php echo $invoice_details[0]['alias_invoice_number']?>" readonly />
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">Invoice Amount</td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <span><?php echo $invoice_details[0]['invoice_amount']?></span>
                            <input type="hidden" id="txt_inv_amt" name="txt_inv_amt" style="width:220px;" value="<?php echo $invoice_details[0]['invoice_amount']?>" readonly />
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">Remaining Amount</td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <span><?php echo $amount_remaining ?></span>
                            <input type="hidden" id="txt_rem_amt" name="txt_rem_amt" style="width:220px;" value="<?php echo $amount_remaining ?>" readonly />
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">Mode Of Collection</td>
                        <td width="5%"> : </td>
                        <td width="55%">
                            <input type="radio" name="radio_mode" value="cash" checked onchange="javascript:payment_recieved_by()"/> &nbsp; Cash

                            <input type="radio" name="radio_mode" value="cheque" onchange="javascript:payment_recieved_by()"/> &nbsp; Cheque

                            <input type="radio" name="radio_mode" value="online" onchange="javascript:payment_recieved_by()"/> &nbsp; Online Payment
                        </td>
                        <td width="10%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">Collection Date<span style="color:#F00;"> *</span></td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <input type="text" id="txt_collection_date" readonly="readonly" name="txt_collection_date" style="width:220px;"/>
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr class="cheque_details">
                        <td width="30%">Cheque No.<span style="color:#F00;"> *</span></td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <input type="text" id="txt_cheque_no" name="txt_cheque_no" style="width:220px;" value="" maxlength="6"/>
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr class="cheque_details">
                        <td width="30%">Issued by<span style="color:#F00;"> *</span></td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <input type="text" id="txt_issued_by" name="txt_issued_by" style="width:220px;" value="" />
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr class="cheque_details">
                        <td width="30%">Cheque Date<span style="color:#F00;"> *</span></td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <input type="text" id="txt_cheque_date" readonly="readonly" name="txt_cheque_date" style="width:220px;" />
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">Amount Collected<span style="color:#F00;"> *</span></td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <input type="text" id="txt_amnt_collected" name="txt_amnt_collected" style="width:220px;" value="<?php echo $amount_remaining ?>" onblur="javascript:calculate_net_amount()"  />
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">TDS Amount Deducted</td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <input type="text" id="txt_tds" name="txt_tds" style="width:220px;" value="0" onblur="javascript:calculate_net_amount()"/>
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">Net Amount Received</td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <input type="text" id="txt_net_amt" name="txt_net_amt" style="width:220px;" value="" readonly />
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">Comment<span style="color:#F00;"> *</span></td>
                        <td width="5%"> : </td>
                        <td width="25%">
                            <input type="text" id="txt_comment" name="txt_comment" style="width:220px;" value="" onblur="return check_form();"/>
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>

                    <tr>
                        <td>
                            <input type="hidden" name="hid_user_id" id="hid_user_id" value="<?php echo $logged_in_user['user_id']?>" />
                            <input type="submit" class="submit" value="Submit" onclick="return check_form();" />
                        </td>
                    </tr>
                </table>
            </form>

        <!-- .paggination ends -->
    </div>		<!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>
</div>		<!-- .block ends -->


<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>

<script>
    $(document).ready(function(){
        $( "#txt_collection_date" ).datepicker({
            //defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true
        }).datepicker("option", "maxDate", "0");

        $( "#txt_cheque_date" ).datepicker({
            //defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true
        }).datepicker("option", "maxDate", "0");
    });

    function payment_recieved_by(){
        var mode_payment = $("input[type='radio'][name='radio_mode']:checked").val();
        if(mode_payment == 'cheque'){
            $(".cheque_details").show();
        }else{
            $(".cheque_details").hide();
        }
    }

    function calculate_net_amount(){
        var amnt_collected = $("#txt_amnt_collected").val();
        var tds = $("#txt_tds").val();
        var net_recieved = amnt_collected - tds;
        $("#txt_net_amt").val(net_recieved);

        //validating inputs
        var objRegExp  = /^[0-9.]*$/;
        if ((!objRegExp.test(amnt_collected)) && (amnt_collected.length > 0) ){
            alert("Please enter a valid amount");
            $('#txt_amnt_collected').focus();
            return false;
        }

        /*if(tds == ''){
         alert("Please enter an amount");
         return false;
         }*/

        if ((!objRegExp.test(tds)) && (tds.length > 0) ){
            alert("Please enter a valid amount");
            $('#txt_tds').focus();
            return false;
        }
    }

    function check_form() {
        var collection_date = $('#txt_collection_date').val();
        var cheque_no = $("#txt_cheque_no").val();
        var issued_by = $("#txt_issued_by").val();
        var cheque_date = $("#txt_cheque_date").val();
        var amnt_remaining = $("#txt_rem_amt").val();
        var amnt_collected = $("#txt_amnt_collected").val();
        var tds = $("#txt_tds").val();
        var comment = $("#txt_comment").val();

        if(collection_date == ''){
            alert("Please select a collection date");
            $('#collection_date').focus();
            return false;
        }

        var mode_payment = $("input[type='radio'][name='radio_mode']:checked").val();
        if(mode_payment == 'cheque'){

            if(cheque_no == ''){
                alert("Please enter a cheque number");
                $('#txt_cheque_no').focus();
                return false;
            }
            var reg1 = /^[0-9]{1,6}$/;
            if ((!reg1.test(cheque_no)) && (cheque_no.length > 0) ){
                alert("Invalid input");
                $('#txt_cheque_no').focus();
                return false;
            }

            if(issued_by == ''){
                alert("Please enter a bank name");
                $('#txt_issued_by').focus();
                return false;
            }

            var reg1 = /.*[a-zA-Z0-9].*/;
            if ((!reg1.test(issued_by)) && (issued_by.length > 0) ){
                alert("Invalid input");
                $('#txt_issued_by').focus();
                return false;
            }

            if(cheque_date == ''){
                alert("Please select a cheque date");
                $('#txt_cheque_date').focus();
                return false;
            }
        }

        if(amnt_collected == ''){
            alert("Please enter an amount");
            $('#txt_amnt_collected').focus();
            return false;
        }

        if(comment == ''){
            alert("Please enter some comment");
            $('#txt_comment').focus();
            return false;
        }

        /*if(amnt_collected > amnt_remaining){
            alert("Please enter amount less than remaining amount");
            return false;
        }*/
    }
</script>