    <?php if($payment_done == 0) { ?>
            <form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_network_ro_payment" method="post">
                <table  class="table">
                    <tr>
                        <th style="display: none"></th>
                        <th style="display: none"></th>
                        <th style="display: none"></th>
                    </tr>
                    <tr>
                        <td>Remittance Advice Number<span style="color:#F00;"> *</span></td>
                        <td>:</td>
                        <td><input type="text" id="advice_number" name="advice_number" style="width:245px;" /></td>
                    </tr>

                    <tr>
                        <td>Network Name<span style="color:#F00;"> *</span></td>
                        <td>:</td>
                        <td><select  name="network_id" style="width:245px;" id="network_id">
                                <option value="-">-</option>
                                <?php foreach($customer_detail as $c_value) { ?>
                                    <option value="<?php echo $c_value['customer_id'] ?> "><?php echo $c_value['customer_name'] ?></option>
                                <?php } ?>
                            </select> </td>
                    </tr>
                    <tr>
                        <td>Amount Paid<span style="color:#F00;"> *</span></td>
                        <td>:</td>
                        <td><input type="text"  id="amount_paid" name="amount_paid" style="width:245px;" /></td>
                    </tr>
                    <tr>
                        <td>Date Paid<span style="color:#F00;"> *</span></td>
                        <td>:</td>
                        <td><input type="text"  id="date_paid" readonly="readonly" name="date_paid" style="width:245px;" /> </td>
                    </tr>
                    <tr>
                        <td>Cheque Number<span style="color:#F00;"> *</span></td>
                        <td>:</td>
                        <td><input type="text"  id="cheque_number" name="cheque_number" style="width:245px;" /> 	</td>
                    </tr>
                    <tr>
                        <td>Cheque Date</label><span style="color:#F00;"> *</span></td>
                        <td>:</td>
                        <td><input type="text"  id="cheque_date" readonly="readonly" name="cheque_date" style="width:245px;" /></td>
                    </tr>
                    <tr>
                        <td>Payment Fully Paid<span style="color:#F00;"> *</span></td>
                        <td>:</td>
                        <td><input type="radio" checked="checked" class="radio" id="payment_paid_no" name="payment_paid" value="0"/> <label>No</label>
                            &nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio" name="payment_paid" id="payment_paid_yes" value="1"/> <label>yes</label>	</td>
                    </tr>
                    <tr>
                        <td>Instructions</label></td>
                        <td>:</td>
                        <td><input type="text"  id="spl_instr" name="instruction" style="width:245px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="3"><input type="submit" class="submit" value="Submit" onclick="return submit_form();" /></td>
                    </tr>
                </table>
                <!--<input type="hidden" name="hid_payment_paid" id="hid_payment_paid" />-->
                <input type="hidden" name="order_id" value="<?php echo $order_id ?>" />
                <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
                <input type="hidden" name="hid_id" value="<?php echo $id ?>">
                <input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro ?>">
            </form>

        </div><!-- .block_content ends -->
    <?php } ?>

    <div class="bendl"></div>
    <div class="bendr"></div>
</div>		<!-- .block ends -->


<div class="block">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Network Ro Payment Detail</h2> <!--<button id="export_csv" style="margin-left:50px;">Export to CSV</button></h2> -->

    </div>		<!-- .block_head ends -->

        <table class="table">
            <tr>
                <td>Remittance Advice Number</td>
                <td>Network Name </td>
                <td>Amount Paid </td>
                <td>Date Paid </td>
                <td>Cheque Number </td>
                <td>Cheque Date </td>
                <td>Payment Fully Paid </td>
                <td>Instructions </td>
            </tr>
            <?php foreach($remittance_payment_detail as $val) { ?>
                <tr>
                    <td><?php echo $val['remittance_advice_number'] ?></td>
                    <td><?php echo $val['network_name'] ?></td>
                    <td><?php echo $val['amount_paid'] ?></td>
                    <td><?php echo $val['date'] ?></td>
                    <td><?php echo $val['cheque_number'] ?></td>
                    <td><?php echo $val['cheque_date'] ?></td>
                    <td><?php echo $val['payment_paid'] ?></td>
                    <td><?php echo $val['instructions'] ?></td>
                </tr>
            <?php } ?>

        </table>



<script type="text/javascript" language="javascript" >
    function submit_form(){
        /* For Sample Format
        if ( !Utility.checkSpecialCharacter($('#advice_number').val()) ) {
            alert('Advice Number Contains Special Character.');
            $('#advice_number').focus();
            return false;
        } */

        if($.trim($('#advice_number').val()) == ""){
            alert('Please enter remittance advice number.');
            $('#advice_number').focus();
            return false;
        }

        var advice_number = document.getElementById('advice_number').value;
        var test_for_char = Utility.checkInvoice(advice_number);
        if(!test_for_char)	{
            alert("Invalid Remittance Advice Number ");
            document.getElementById('advice_number').focus();
            return false;
        }

        if($('#network_id').val() == "-"){
            alert('Please select network.');
            $('#amount_paid').focus();
            return false;
        }
        if($.trim($('#amount_paid').val()) == ""){
            alert('Please enter amount paid.');
            $('#amount_paid').focus();
            return false;
        }

        var amount_paid = document.getElementById('amount_paid').value;
        var test_amount = Utility.checkAmount(amount_paid);
        if(!test_amount)	{
            alert("Invalid Amount");
            document.getElementById('amount_paid').value = "";
            document.getElementById('amount_paid').focus();
            return false;
        }
        /*if ( $('#amount_paid').blank() ) {
            alert('Please enter amount paid.');
            $('#amount_paid').focus();
            return false;
        }*/
        if($.trim($('#date_paid').val()) == ""){
            alert('Please select date paid.');
            $('#date_paid').focus();
            return false;
        }
        if($.trim($('#cheque_number').val()) == ""){
            alert('Please enter cheque number.');
            $('#cheque_number').focus();
            return false;
        }
        /*if ( $('#cheque_number').blank() ) {
            alert('Please enter cheque number.');
            $('#cheque_number').focus();
            return false;
        }*/
        if($.trim($('#cheque_date').val()) == ""){
            alert('Please select cheque date.');
            $('#cheque_date').focus();
            return false;
        }
        var spl_instr = document.getElementById('spl_instr').value;
        var test_amount = Utility.checkSingleDoubleQuotes_v1(spl_instr);
        if(!test_amount)	{
            alert("Invalid Instruction");
            document.getElementById('spl_instr').focus();
            return false;
        }
        //$("#hid_payment_paid").val($( "input:radio[name=payment_paid]:checked" ).val());
    }
    $(document).ready(function(){

        $( "#date_paid" ).datepicker({
            defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true
        });
        $( "#cheque_date" ).datepicker({
            defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true
        });

    });
    $("#export_csv").click(function() {
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/download_nw_payment_csv/";
    });

</script>
