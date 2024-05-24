<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>


<div class="block small center login" style="margin:0px; margin-left:200px;">


    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Add Client</h2>
    </div>

    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_add_client" method="post">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="20%">Client Name</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_client_name" id="txt_client_name"/>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Client Contact Name</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_client_contact_name"/>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Client Contact Number</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_client_contact_number"/>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Client Location</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_client_location"/>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Client Address</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_client_address"/>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Client Category</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_client_category"/>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Direct Client</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="radio" id="r1" name="rd_direct_client" value="Y"
                               onclick="javascript:$('#div_direct').show();"/> Yes
                        <input type="radio" id="r2" name="rd_direct_client" value="N"
                               onclick="javascript:$('#div_direct').hide();" checked="checked"/> No
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="display:none;margin-left:-12px;" id="div_direct">
                            <table>
                                <tr>
                                    <td>Billing Information<span style="color:#F00;"> *</span></td>
                                    <td> :</td>
                                    <td>
                                        <input type="text" id="txt_client_billing_info" name="txt_client_billing_info"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Billing Address<span style="color:#F00;"> *</span></td>
                                    <td> :</td>
                                    <td>
                                        <input type="text" id="txt_client_billing_address"
                                               name="txt_client_billing_address"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Payment Cycle<span style="color:#F00;"> *</span></td>
                                    <td> :</td>
                                    <td>
                                        <input type="text" id="txt_client_pay_cycle" name="txt_client_pay_cycle"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="hidden" name="hid_cust_ro" value="<?php echo $cust_ro; ?>"/>
                        <input type="submit" class="submit" value="Add" onclick="return validate_client();"/>
                        <input type="button" value="Back" class="submit" onclick="javascript:window.history.back();"/>
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
    function validate_client() {
        if (document.getElementById('txt_client_name').value == "") {
            alert('Please enter client name.');
            return false;
        }
        if (document.querySelector('input[name="rd_direct_client"]:checked').value == 'Y') {
            if (document.getElementById("txt_client_billing_info").value == "") {
                alert("Please enter billing information.");
                return false;
            }
            if (document.getElementById("txt_client_billing_address").value == "") {
                alert("Please enter billing address.");
                return false;
            }
            if (document.getElementById("txt_client_pay_cycle").value == "") {
                alert("Please enter payment cycle.");
                return false;
            }
        }
    }
</script>