
<form id="add_edit_client_contact_form">
    <table id="add_edit_client_contact_table" class="table">
        <!---------Client Display Name--->
        <tr>
            <td width="20%">Client Display Name</td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="input_width" readonly="readonly" name="txt_add_edit_client_display_name"
                       id="txt_add_edit_client_display_name" value="<?php echo $client_display_name; ?>"/>
            </td>
        </tr>
        <!------Client Contact Name--->
        <tr>
            <td width="20%" nowrap="nowrap">Client Contact Name<span style="color:#F00;"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="input_width" name="txt_add_edit_client_contact_name"
                       id="txt_add_edit_client_contact_name" value="<?php echo $client_contact_name; ?>"/>
                <label id="txt_client_contact_name_error"></label>
            </td>
        </tr>
        <!------------Client Contact No----->
        <tr>
            <td width="20%">Client Contact No.<span style="color:#F00;"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="input_width" id="txt_add_edit_client_contact_no"
                       name="txt_add_edit_client_contact_no" value="<?php echo $client_contact_no; ?>"/>
                <label id="txt_client_contact_no_error"></label>
            </td>
        </tr
        <!----------Client Email------>
        <tr>
            <td width="20%">Client Email<span style="color:#F00;"> *</span><Br/>
                (Add multiple email id separated by "<strong>,</strong>")
            </td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="input_width" name="txt_add_edit_client_email" id="txt_add_edit_client_email"
                       value="<?php echo $client_email; ?>"/>
                <label id="txt_client_email_error"></label>
            </td>
        </tr>
        <!--------CLient Address----->
        <tr>
            <td width="20%">Client Address<span style="color:#F00;"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <?php if ($client_address != NULL) {
                    echo $client_address;
                } else {
                    echo "Not Applicable";
                }
                ?>
            </td>
        </tr>
        <!------Client State------>
        <tr>
            <td width="20%">Client State<span style="color:#F00;"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <select name="add_edit_client_state" class="form-control input_width" id="add_edit_client_state">
                    <?php foreach ($all_states as $eachState) { ?>
                        <?php if ($eachState['state_name'] == $client_state) {
                            $selected = ' selected="selected" ';
                        } ?>
                        <option value="<?php echo $eachState['state_name']; ?>" <?php echo $selected; ?>><?php echo $eachState['state_name']; ?> </option>
                    <?php } ?>
                </select>
                <label id="client_state_error"></label>
            </td>
        </tr>
        <!-------Add/update----->
        <tr>
            <td colspan="3" id="client_btn_td">
                <div id="add_edit_client_error_div" style="display: none"></div>
                <input type="hidden" id="hid_add_edit_cust_ro" name="hid_cust_ro" value="<?php echo $cust_ro; ?>"/>
                <input type="hidden" id="hid_add_edit_ro_date" name="hid_ro_date" value="<?php echo $ro_date; ?>"/>
                <input type="hidden" id="hid_add_edit_agency" name="hid_agency" value="<?php echo $agency; ?>"/>
                <input type="hidden" id="hid_add_edit_agency_contact" name="hid_agency_contact"
                       value="<?php echo $agency_contact; ?>"/>
                <input type="hidden" id="hid_add_edit_client" name="hid_client"
                       value="<?php echo $client_display_name; ?>"/>
                <input type="hidden" id="hid_add_edit_client_contact" name="hid_client_contact"
                       value="<?php echo $client_contact; ?>"/>
                <input type="hidden" id="hid_add_edit_am_edit" name="hid_am_edit" value="<?php echo $am_edit; ?>"/>
            </td>
        <tr>

    </table>
</form>
