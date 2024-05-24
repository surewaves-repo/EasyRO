<html>
<head>
    <script src="/surewaves_easy_ro/js/non_add_edit_agency_contact_script.js"></script>
    <link rel="stylesheet" href="/surewaves_easy_ro/css/non_add_edit_agency_contact.css">
</head>
<body>
<form id="non_fct_ro_add_edit_agency_contact_form">
    <table id="non_fct_ro_add_edit_agency_contact_table" class="table" width="100%">
        <!---Agency Name-->
        <tr>
            <td width="20%">Agency Name</td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" readonly="readonly" class="input_width" name="txt_agency_name" id="txt_agency_name"  />
            </td>
        </tr>
        <!---Agency Contact Name--->
        <tr>
            <td width="20%">Agency Contact Name<span class="Asterik"> *</span></td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" name="txt_agency_contact_name" class="input_width" id="txt_agency_contact_name" value="<?php echo $agency_contact_name;?>" />
            </td>
        </tr>
        <!----Agency Contact No.----->
        <tr>
            <td width="20%">Agency Contact No.<span class="Asterik"> *</span></td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_agency_contact_no" name="txt_agency_contact_no" value="<?php echo $agency_contact_no;?>" />
            </td>
        </tr>
        <!---Agency Destination----->
        <tr>
            <td width="20%">Agency Designation</td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_agency_designation" name="txt_agency_designation" value="<?php echo $agency_designation;?>" />
            </td>
        </tr>
        <!----Agency Location----->
        <tr>
            <td width="20%">Agency Location</td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_agency_location" name="txt_agency_location" value="<?php echo $agency_location;?>" />
            </td>
        </tr>
        <!----Agency address---->
        <tr>
            <td width="20%">Agency Address<span class="Asterik"> *</span></td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_agency_address" name="txt_agency_address" value="<?php echo $agency_address;?>" />
            </td>
        </tr>
        <!----Agency Email------>
        <tr>
            <td width="20%">Agency Email<span class="Asterik"> *</span><Br />
                (Add multiple email id separated by "<strong>,</strong>")
            </td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" name="txt_agency_email" id="txt_agency_email" value="<?php echo $agency_email;?>" />
            </td>
        </tr>
        <!----Billing Information--->
        <tr>
            <td width="20%">Billing Information<span class="Asterik"> *</span></td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_billing_info" name="txt_billing_info" value="<?php echo $billing_info;?>" />
            </td>
        </tr>
        <!----Billing Address--->
        <tr>
            <td width="20%">Billing Address<span style="color:#F00;"> *</span></td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_billing_address" name="txt_billing_address" value="<?php echo $billing_address;?>" />
            </td>
        </tr>
        <!----Payment Cycle---->
        <tr>
            <td width="20%">Payment Cycle</td>
            <td width="5%"> : </td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_pay_cylce" name="txt_pay_cylce" value="<?php echo $pay_cylce;?>" />
            </td>
        </tr>
        <!---Add Update button-->
        <tr>
            <td id="btn_td" colspan="3">
                <input type="hidden" id="hid_cust_ro" name="hid_cust_ro" value="<?php echo $cust_ro;?>" />
                <input type="hidden" id="hid_ro_date" name="hid_ro_date" value="<?php echo $ro_date;?>" />
                <input type="hidden" id="hid_agency" name="hid_agency" value="<?php echo $agency;?>" />
                <input type="hidden" name="hid_agency_contact" value="<?php echo $agency_contact;?>" />
                <input type="hidden" id="hid_am_edit" name="hid_am_edit" value="<?php echo $am_edit;?>" />
            </td>
        <tr>
    </table>


</form>
</body>
</html>
