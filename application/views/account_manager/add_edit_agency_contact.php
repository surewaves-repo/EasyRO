

    <form id="add_edit_agency_contact_form">
       <table id="add_edit_agency_contact_table" class="table">

           <!-------------Agency Display Name-->
           <tr>
               <td width="20%" class="first_row">Agency Display Name</td>
               <td width="5%" class="first_row"> : </td>
               <td width="25%" class="first_row">
                   <input type="text" readonly="readonly" class="input_width" name="txt_add_edit_agency_name" id="txt_add_edit_agency_name" value="<?php echo $agency_display;?>" />
               </td>

           </tr>
           <!----------Agency Contact Name--->
           <tr>
               <td width="20%">Agency Contact Name<span class="Asterik"> *</span></td>
               <td width="5%"> : </td>
               <td width="25%">
                   <input type="text" name="txt_add_edit_agency_contact_name" class="input_width" id="txt_add_edit_agency_contact_name" value="<?php echo $agency_contact_name;?>" />
                    <label id="txt_agency_contact_name_error"></label>
               </td>

           </tr>
           <!------Agency Contact Number---->
           <tr>
               <td width="20%">Agency Contact No.<span class="Asterik"> *</span></td>
               <td width="5%"> : </td>
               <td width="25%">
                   <input type="text" id="txt_add_edit_agency_contact_no" class="input_width" name="txt_add_edit_agency_contact_no" value="<?php echo $agency_contact_no;?>" maxlength="12" />
                   <label id="txt_agency_contact_no_error"></label>
               </td>
            </tr>

           <!-----------Agency Email---->
           <tr>
               <td width="20%">Agency Email<span class="Asterik"> *</span><Br />
                   (Add multiple email id separated by "<strong>,</strong>")
               </td>
               <td width="5%"> : </td>
               <td width="25%">
                   <input type="text" class="input_width" name="txt_add_edit_agency_email" id="txt_add_edit_agency_email" value="<?php echo $agency_email;?>" />
                   <label id="txt_agency_email_error"></label>
               </td>
           </tr>

           <!------------Agency Address--->
           <tr>
               <td width="20%">Agency Address<span class="Asterik"> *</span></td>
               <td width="5%"> : </td>
               <td width="25%">
                   <?php echo $agency_address;?>
               </td>
           </tr>
           <!---------Agency State------->
           <tr>
           <td width="20%">Agency State<span class="Asterik"> *</span></td>
           <td width="5%"> : </td>
           <td width="25%">
               <select class="form-control input_width" name="add_edit_agency_state" id ="add_edit_agency_state" >
                   <?php foreach($all_states as $eachState) {  ?>
                       <?php if ($eachState['state_name'] == $agency_state  ) {
                           $selected  = ' selected="selected" ';
                       } ?>
                       <option value="<?php echo $eachState['state_name']; ?>" <?php echo $selected; ?>><?php echo $eachState['state_name']; ?> </option>
                   <?php } ?>
               </select>
               <label id="agency_state_error"></label>
           </td>
           </tr>

           <!------add or update Button-->
           <tr>
               <td colspan="3" id="btn_td">
                   <div id="add_edit_agency_error_div" style="display: none"></div>
                   <input type="hidden" id="hid_add_edit_cust_ro" name="hid_add_edit_cust_ro" value="<?php echo $cust_ro;?>" />
                   <input type="hidden" id="hid_add_edit_ro_date" name="hid_add_edit_ro_date" value="<?php echo $ro_date;?>" />
                   <input type="hidden" id="hid_add_edit_agency" name="hid_add_edit_agency" value="<?php echo $agency_display;?>" />
                   <input type="hidden" id="hid_add_edit_agency_contact" name="hid_add_edit_agency_contact" value="<?php echo $agency_contact;?>" />
                   <input type="hidden" id="hid_add_edit_client" name="hid_add_edit_client" value="<?php echo $client_display;?>" />
                   <input type="hidden" id="hid_add_edit_client_contact" name="hid_add_edit_client_contact" value="<?php echo $client_contact;?>" />
                </td>
           <tr>

       </table>
    </form>

	
			
