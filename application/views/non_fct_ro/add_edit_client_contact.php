<html>
<head>
        <script src="/surewaves_easy_ro/js/non_add_edit_client_contact_script.js"></script>
        <link rel="stylesheet" href="/surewaves_easy_ro/css/non_add_edit_client_contact.css">
</head>
<body>
<form id="non_add_edit_client_contact_form" >
    <table class="table" id="non_add_edit_client_contact_table" width="100%">
        <!----Client Name---->
        <tr>
            <td width="20%">Client Name</td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" readonly="readonly" name="txt_client_name" id="txt_client_name"  />
            </td>
        </tr>
        <!-----Client Contact Name---->
        <tr>
            <td width="20%" nowrap="nowrap">Client Contact Name<span class="Asterik"> *</span></td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" name="txt_client_contact_name" id="txt_client_contact_name" value="<?php echo $client_contact_name;?>" />
            </td>
        </tr>
        <!------Client Contact No----->
        <tr>
            <td width="20%">Client Contact No.<span class="Asterik"> *</span></td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_client_contact_no" name="txt_client_contact_no" value="<?php echo $client_contact_no;?>" />
            </td>
        </tr>
        <!----Client Designation----->
        <tr>
            <td width="20%">Client Designation</td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" name="txt_client_designation" id="txt_client_designation" value="<?php echo $client_designation;?>" />
            </td>
        </tr>
        <!-----Client Location---->
        <tr>
            <td width="20%">Client Location</td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" name="txt_client_location" id="txt_client_location" value="<?php echo $client_location;?>" />
            </td>
        </tr>
        <tr>
            <td width="20%">Client Address<span class="Asterik"> *</span></td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_client_address" name="txt_client_address" value="<?php echo $client_address;?>" />
            </td>
        </tr>
        <tr>
            <td width="20%">Client Email<span class="Asterik"> *</span><Br />
                (Add multiple email id separated by "<strong>,</strong>")</td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" name="txt_client_email" id="txt_client_email" value="<?php echo $client_email;?>" />
            </td>
        </tr>
        <tr>
            <td width="20%">Direct Client</td>
            <td width="5%"> : </td>
            <td width="25%">
                <?php if(isset($direct_client)){
                    ?>
                    <input type="radio" id="r1" name="rd_direct_client" value="Y" onclick="javascript:$('#div_direct').show();" <?php if($direct_client == 'Y'){echo 'checked';}?> /> Yes
                    <input type="radio" id="r2" name="rd_direct_client" value="N" onclick="javascript:$('#div_direct').hide();" <?php if($direct_client == 'N'){echo 'checked';}?>  /> No

                    <?php
                }else { ?>

                    <input type="radio" id="r1" name="rd_direct_client" value="Y" onclick="javascript:$('#div_direct').show();" /> Yes
                    <input type="radio" id="r2" name="rd_direct_client" value="N" onclick="javascript:$('#div_direct').hide();" checked="checked" /> No
                    <?php
                } ?>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <?php if(isset($direct_client) && $direct_client == 'Y'){?>
                <div  id="div_direct">
                    <?php
                    }else { ?>
                    <div style="display:none;" id="div_direct">
                        <?php
                        } ?>
                        <table>
                            <tr>
                                <td class="first_row">Billing Information<span class="Asterik"> *</span></td>
                                <td class="first_row"> : </td>
                                <td class="first_row">
                                    <input type="text" class="input_width" id="txt_client_billing_info" name="txt_client_billing_info" value="<?php echo $billing_info;?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="first_row">Billing Address<span class="Asterik"> *</span></td>
                                <td class="first_row"> : </td>
                                <td class="first_row">
                                    <input type="text" class="input_width" id="txt_client_billing_address" name="txt_client_billing_address" value="<?php echo $billing_address;?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="first_row">Payment Cycle<span class="Asterik"> *</span></td>
                                <td class="first_row"> : </td>
                                <td class="first_row">
                                    <input type="text" class="input_width" id="txt_client_pay_cycle" name="txt_client_pay_cycle" value="<?php echo $pay_cylce;?>" />
                                </td>
                            </tr>
                        </table>
                    </div>
            </td>
        </tr>

        <tr>
            <td id="btn_td" colspan="3">
                <input type="hidden" id="hid_cust_ro" name="hid_cust_ro" value="<?php echo $cust_ro;?>" />
                <input type="hidden" id="hid_client_contact" name="hid_client_contact" value="<?php echo $client_contact;?>" />
                <input type="hidden" id="hid_ro_date" name="hid_ro_date" value="<?php echo $ro_date;?>" />
                <input type="hidden" id="hid_agency" name="hid_agency" value="<?php echo $agency;?>" />
                <input type="hidden" id="hid_agency_contact" name="hid_agency_contact" value="<?php echo $agency_contact;?>" />
                <input type="hidden" id="hid_am_edit" name="hid_am_edit" value="<?php echo $am_edit;?>" />
                <input type="hidden" id="hid_client" name="hid_client" value="<?php echo $client;?>" />
            </td>
        <tr>
    </table>


</form>

</body>
</html>
