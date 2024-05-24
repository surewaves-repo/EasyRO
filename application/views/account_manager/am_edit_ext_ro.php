<html>
<head>
<script src="/surewaves_easy_ro/js/bootstrap-multiselect.js"></script>
<script src="/surewaves_easy_ro/js/am_edit_ext_ro_script.js"></script>
<style>
    .btn
{
    line-height: 8px;
}
.form-control.form-control.form-control{
    font-size:12px;
}
.btn-default
{
    width: 220px !important;
    height: 28px !important;
    font-size: 0.78rem !important;
    color: black !important;
    border: 1px solid gray !important;
}
#Update_btn
{
    line-height:24px;
}
#am_edit_txt_am,#am_edit_regionSelectBox,#am_edit_txt_ext_ro,#am_edit_txt_agency,#am_edit_txt_client
{
    border: none;
    outline: none;
}

    .fa-calendar
{
    cursor: pointer;
}
b
{
    color:white !important;

}
.multiselect-group
{
    background-color:#0B85A1 !important;

}
.multiselect-group
{
    color:white;
}
td.no_border
{
    border:none !important;
}
th.no_border
{
    border:none !important;
}
.checkbox
{
    color:black !important;
}
.checkbox:hover
{
    color: white !important;
}
.multiselect-container li:hover
{
    background-color: darkgray !important;
    width: 220px !important;
    color: white !important;

}
.multiselect-container li
{
    font-size: 12px !important;
    width:220px;
}

.multiselect-selected-text
{
    font-size: 12px;
}
</style>
</head>
<body>
<!---------------------------------------Modal------------------------------------------------------>
<div class="modal fade " id="create_ext_Ro_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header" id="create_ext_ro_modal_header">
                <h5 id="sub_modal_title" class="modal-title"></h5>
                <button type="button" class="btn close" id="sub_modal_btn">&times;</button>
                <img src="<?php echo ROOT_FOLDER ?>/images/loading_image.gif" id="loading_img" class="center"
                     style="display: none">
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="sub_modal_body">
            </div>
        </div>
    </div>
</div> <!--End Of Modal-->
       <!---------------------------------------------Form------------------------------------------>
        <form id='edit_ext_ro_form'  enctype="multipart/form-data">

            <table id="edit_ext_ro_table" class="table" cellpadding="0" cellspacing="0" width="100%">

            <!------------------------------External Ro Number-------------------------->
                <tr>
                    <td width="30%">External RO Number<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" readonly id="am_edit_txt_ext_ro" name="am_edit_txt_ext_ro" style="width:220px;"
                               value="<?php echo $ro_details[0]['cust_ro'] ?>"
                                />
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <!-----------------------------Ro Date--------------------------------------->
                <tr>
                    <td width="30%">RO Date<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="am_edit_txt_ro_date" name="am_edit_txt_ro_date" style="width:220px;"
                               value="<?php echo $ro_details[0]['ro_date'] ?>"/>
                        <span class="fa fa-calendar am_edit_ro_date">
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <!--------------------------------Agency----------------------------------->
                <tr>
                    <td>Agency<span style="color:#F00;"> *</span></td>
                    <td> :</td>
                    <td>
                        <input type="text" id="am_edit_txt_agency" class="form-control" name="am_edit_txt_agency" style="width:220px;"
                               value="<?php echo $ro_details[0]['agency'] ?>" readonly="readonly"/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <!--------------------------------Agency Contact Name-------------------->
                <tr>
                    <td>Agency Contact Name<span style="color:#F00;"> *</span></td>
                    <td> :</td>
                    <td>
                     
                        <select name="sel_agency_contact" id="sel_agency_contact" class="form-control" style="width:220px;">
                            <option value="new">New</option>
                            <?php foreach ($agency_contact as $agencies) {
                                if ($agencies['id'] == $ro_details[0]['agency_contact_id']) {
                                    ?>
                                    <option selected="selected"
                                            value="<?php echo $agencies['id'] ?>"><?php echo $agencies['agency_contact_name'] ?></option>
                                <?php } else {
                                    ?>
                                    <option value="<?php echo $agencies['id'] ?>"><?php echo $agencies['agency_contact_name'] ?></option>
                                <?php }
                            } ?>
                        </select>
                    </td>
                    <td><span class="link" id="am_edit_add_edit_agency_contact_span">Add/Edit</span></td>
                </tr>

                <!---------------------------------CLient------------------------------>
                <tr>
                    <td width="20%">Client<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="am_edit_txt_client" class='form-control' name="am_edit_txt_client" style="width:220px;"
                               value="<?php echo $ro_details[0]['client'] ?>" readonly="readonly"/>
                    </td>
                    <td width="50%">&nbsp;</td>
                </tr>
                
                <!--------------------------------------Client Contact Name---------------->
                <tr>
                    <td>Client Contact Name</td>
                    <td> :</td>
                    <td>

                        <select name="sel_client_contact" class='form-control' id="sel_client_contact" style="width:220px;">
                            <option value="new">New</option>
                            <?php foreach ($client_contact as $contacts) {
                                if ($contacts['id'] == $ro_details[0]['client_contact_id']) {
                                    ?>
                                    <option selected="selected"
                                            value="<?php echo $contacts['id'] ?>"><?php echo $contacts['client_contact_name'] ?></option>
                                <?php } else {
                                    ?>
                                    <option value="<?php echo $contacts['id'] ?>"><?php echo $contacts['client_contact_name'] ?></option>
                                <?php }
                            } ?>
                        </select>


                    </td>
                    <td><span class="link" id="am_edit_add_edit_client_contact_span" >Add/Edit</span></td>
                </tr>

                <tr>
                    <td width="20%">Brand<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%"  id='am_edit_txt_brand'>
                        <?php echo $ro_details[0]['brand_name'] ?>
                    </td>
                    <td width="50%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="20%">Make Good Type<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="" colspan="2">


                        <input type="radio" name="am_edit_rd_make_good"
                               value="0" <?php if ($ro_details[0]['make_good_type'] == 0) {
                            echo 'checked';
                        }
                        if ($campaign_created == 1) {
                            echo " disabled";
                        } ?>/>&nbsp;Auto MakeGood &nbsp;&nbsp;
                        <input type="radio" name="am_edit_rd_make_good"
                               value="1" <?php if ($ro_details[0]['make_good_type'] == 1) {
                            echo 'checked';
                        }
                        if ($campaign_created == 1) {
                            echo " disabled";
                        } ?>/>&nbsp;Client Confirmed MakeGood &nbsp;&nbsp;
                        <input type="radio" name="am_edit_rd_make_good"
                               value="2" <?php if ($ro_details[0]['make_good_type'] == 2) {
                            echo 'checked';
                        }
                        if ($campaign_created == 1) {
                            echo " disabled";
                        } ?> />&nbsp;No MakeGood
                    </td>

                </tr>

                <tr>
                    <td width="30%">Account Manager Name<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <?php if ($logged_in_user['profile_id'] == 1 || $logged_in_user['profile_id'] == 2) {
                            if ($ro_status != 1) {
                                ?>
                                <select name="am_edit_sel_am" style="width:220px;">
                                    <?php foreach ($all_am as $am) {
                                        if ($am['user_id'] == $ro_details[0]['user_id']) {
                                            ?>
                                            <option selected="selected"
                                                    value="<?php echo $am['user_id'] ?>"><?php echo $am['user_name'] ?></option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value="<?php echo $am['user_id'] ?>"><?php echo $am['user_name'] ?></option>
                                        <?php }
                                    } ?>
                                </select>

                            <?php } else { ?>
                                <input type="text" name="am_edit_txt_am" id="am_edit_txt_am" readonly="readonly"
                                       value="<?php echo $ro_assigned_user_name; ?>"/>
                                <input type="hidden" id="am_edit_sel_am" name="sel_am" style="width:220px;"
                                       value="<?php echo $ro_details['user_id']; ?>"/>

                            <?php }
                        } else { ?>

                            <input type="text" name="am_edit_txt_am" id="am_edit_txt_am" readonly="readonly"
                                   value="<?php if (isset($user_name)) {
                                       echo $user_name;
                                   } else {
                                       echo $logged_in_user['user_name'];
                                   } ?>"/>
                            <input type="hidden" id="sel_am" name="sel_am" style="width:220px;"
                                   value="<?php echo $logged_in_user['user_id']; ?>"/>
                        <?php } ?>
                        <input type="hidden" id="am_edit_hid_user_id" name="am_edit_hid_user_id" style="width:220px;"
                               value="<?php if (isset($user_id)) {
                                   echo $user_id;
                               } else {
                                   echo $logged_in_user['user_id'];
                               } ?>"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td width="20%">Region<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input name="am_edit_regionSelectBox"  type="text" readonly data='<?php echo $ro_details[0]['region_id'] ?>' id="am_edit_regionSelectBox" style="width:220px;"
                            value="<?php echo $ro_details[0]['region_name'] ?>">
                        
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
               
                <tr>
                    <td width="30%">Campaign Start Date<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="am_edit_txt_camp_start_date" name="am_edit_txt_camp_start_date" style="width:220px;"
                               value="<?php echo $ro_details[0]['camp_start_date'] ?>" />
                               <span class="fa fa-calendar am_edit_camp_start_date"></span>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Campaign End Date<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="am_edit_txt_camp_end_date" name="am_edit_txt_camp_end_date" style="width:220px;"
                               value="<?php echo $ro_details[0]['camp_end_date'] ?>" />
                        <span class="fa fa-calendar am_edit_camp_end_date"></span>       
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

               

                <tr>
                    <td width="30%">Markets<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <?php $saved_market = explode(",", $ro_details[0]['market']) ?>
                        <select multiple="multiple" id="am_edit_sel_market" name="am_edit_sel_market"
                                style="width:220px;" <?php if ($campaign_created == 1) { ?> disabled="disabled" <?php } ?>>
                            <optgroup label="Market Clusters">
                                <?php foreach ($all_clusters as $clusters) {
                                    if (in_array($clusters['sw_market_name'], $saved_market)) { ?>
                                        <option value="<?php echo $clusters['sw_market_name'] ?>"
                                                selected="selected"><?php echo $clusters['sw_market_name'] ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $clusters['sw_market_name'] ?>"><?php echo $clusters['sw_market_name'] ?></option>
                                    <?php }
                                } ?>
                            </optgroup>

                            <optgroup label="Markets">
                                <?php foreach ($all_markets as $markets) {
                                    if (in_array($markets['sw_market_name'], $saved_market)) { ?>
                                        <option value="<?php echo $markets['sw_market_name'] ?>"
                                                selected="selected"><?php echo $markets['sw_market_name'] ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $markets['sw_market_name'] ?>"><?php echo $markets['sw_market_name'] ?></option>
                                    <?php }
                                } ?>
                            </optgroup>

                        </select>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr style="" id="am_edit_market_tr">
                    <td colspan="4" style="padding:0px;">
                    <div id="am_edit_market_error_div"></div>
                        <div id="am_edit_market_div">
                            <table width="100%">
                                <tr>
                                    <td colspan="8" nowrap="nowrap" style="border-bottom:none;border-top:none;">Marketwise RO Amount
                                        <span style="color:red;">(If you enter amount '0' for both spot and banner for a market then it will be discarded)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <?php
                                    echo '<td style="text-align:right;border-top:none;">Market Name</td><td width="5%" style="border-top:none;"></td><td style="border-top:none;">Spot Amount</td><td style="border-top:none;">Spot FCT</td><td style="border-top:none;">Spot Rate</td><td style="border-top:none;">Banner Amount</td><td style="border-top:none;">Banner Fct</td><td style="border-top:none;">Banner Rate</td>';
                                    $i = 0;
                                    foreach ($markets_against_ro as $market) {
                                     
                                            echo '<tr><td nowrap="nowrap" width="10%" style="border-bottom:none;border-top:none;padding:0px;text-align:right;margin-bottom:8px">' . $market['market'] . '<span style="color:#F00;"> *</span></td><td width="5%" nowrap="nowrap" style="border-bottom:none;margin-bottom:8px;border-top:none;padding:0px;"> : </td>
                                                      <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;border-top:none;margin-bottom:8px"><input type="text" id="' . str_replace(" ", "_", $market['market']) . '_spot_amount" name="' . str_replace(" ", "_", $market['market']) . '_spot_amount" style="width:80px;margin-bottom:8px;" onblur="price_check_for_each_market(\'' . str_replace(" ", "_", $market['market']) . '_spot_amount\',this.value);" value="' . $market['spot_price'] . '" /></td>
		     										  <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;border-top:none;margin-bottom:8px"><input type="text" id="' . str_replace(" ", "_", $market['market']) . '_spot_FCT" name="' . str_replace(" ", "_", $market['market']) . '_spot_FCT" style="width:80px;margin-bottom:8px;" onblur="validateFCTNumber(\'' . str_replace(" ", "_", $market['market']) . '_spot_FCT\',this.value);" value="' . $market['spot_fct'] . '" /></td>
		     										  <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;border-top:none;margin-bottom:8px"><input type="text" id="' . str_replace(" ", "_", $market['market']) . '_spot_rate" name="' . str_replace(" ", "_", $market['market']) . '_spot_rate" style="width:80px;margin-bottom:8px;" value="' . $market['spot_rate'] . '"  readonly="readonly"/></td>
                                                      <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;border-top:none;margin-bottom:8px"><input type="text" id="' . str_replace(" ", "_", $market['market']) . '_banner_amount" name="' . str_replace(" ", "_", $market['market']) . '_banner_amount" style="width:80px;margin-bottom:8px;" onblur="price_check_for_each_market(\'' . str_replace(" ", "_", $market['market']) . '_banner_amount\',this.value);" value="' . $market['banner_price'] . '" /></td>
                                                      <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;border-top:none;margin-bottom:8px"><input type="text" id="' . str_replace(" ", "_", $market['market']) . '_banner_FCT" name="' . str_replace(" ", "_", $market['market']) . '_banner_FCT" style="width:80px;margin-bottom:8px;" onblur="validateFCTNumber(\'' . str_replace(" ", "_", $market['market']) . '_banner_FCT\',this.value);" value="' . $market['banner_fct'] . '"/></td>
                                                      <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;border-top:none;margin-bottom:8px"><input type="text" id="' . str_replace(" ", "_", $market['market']) . '_banner_rate" name="' . str_replace(" ", "_", $market['market']) . '_banner_rate" style="width:80px;margin-bottom:8px;"  value="' . $market['banner_rate'] . '" readonly="readonly"/></td></tr>';
                                        $i++;
                                    }
                                    ?>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td width="30%">Gross Amount<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="am_edit_txt_gross" name="am_edit_txt_gross" style="width:220px;"
                               value="<?php echo $ro_details[0]['gross'] ?>"
                                readonly="readonly"/>
                        <input type="hidden" id="ro_initial_gross" value="<?php echo $ro_details[0]['gross'] ?>"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td width="30%" nowrap="nowrap">Agency Commission Payable</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="am_edit_txt_agency_com" name="am_edit_txt_agency_com" style="width:220px;"
                               value="<?php echo $ro_details[0]['agency_com'] ?>"
                               onblur="calculate_net_after_commission()"  />
                        <input type="hidden" id="initial_agency_commission"
                               value="<?php echo $ro_details[0]['agency_com'] ?>"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Net After Agency Commission</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="am_edit_txt_net_agency_com" name="am_edit_txt_net_agency_com" style="width:220px;"
                               value="<?php echo $ro_details[0]['net_agency_com'] ?>" readonly="readonly"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Special Instructions</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <textarea id="am_edit_txt_spcl_inst" name="am_edit_txt_spcl_inst" style="width:207px; height:70px;"
                                  placeholder="Any instruction including Billing Instruction"
                                  <?php if ($campaign_created == 1){ ?>readonly="readonly" <?php } ?>><?php echo $ro_details[0]['spcl_inst'] ?></textarea>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td>Order history recipient mail id/s<span id="OrderHRIds"
                                                               style="display:none;color:#F00;"> *</span></td>
                    <td>:</td>
                    <td>
                        <select name="am_edit_Order_history_recipient_ids[]" id="am_edit_Order_history_recipient_ids"
                                style="width:220px;" multiple="multiple">
                        </select>
                    </td>
                    <td colspan='3'>&nbsp;</td>
                </tr>

                <tr>
                    <td width="30%">Old Attach RO file<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <?php if ($file_path == '') {
                            echo 'No Attachment Found';
                        } else {
                            $file_paths = explode(",", $file_path);
                            $fileCount = 1;
                            foreach ($file_paths as $path) { ?>
                                <a id='attach_ro_id' href="<?php echo $path ?>" target="_blank">File <?php echo $fileCount ?> </a> <br/>
                                <?php $fileCount++;
                            }
                        } ?>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td width="30%">Old Client Approval Mail Attachment File<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <?php if ($ro_details[0]['client_approval_mail'] == '') {
                            echo 'No Attachment Found';
                        } else { ?>
                            <a id='client_approval_file' href="<?php echo $ro_details[0]['client_approval_mail']; ?>" target="_blank">Download
                                Approval Mail</a><br/>
                        <?php } ?>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr id='am_edit_file_error' style='display:none'> 
                    <td colspan='11'>
                        <span class='Asterik'>Ro Attachement and Client Approval Mail size should not exceed 18mb<span>
                    </td>                 
                </tr> 
                
                <tr>
                    <td width="30%">Attach RO file</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type='file' id="am_edit_file_pdf" name="am_edit_file_pdf" class="file_class">
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td width="30%">Client Approval Mail Attachment</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type='file'  id="am_edit_client_aproval_mail" name="am_edit_client_aproval_mail" class="file_class">
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <?php
                if(!$disableUpdate){
                    echo "<tr>".
                        "<td colspan='4'>".
                            "<input type='hidden' name='am_edit_hid_id' id='am_edit_hid_id' value='"?><?php echo $ro_details[0]['id'];?><?php echo "'/>".
                            "<input type='hidden' name='am_edit_hid_market' id='am_edit_hid_market'/>".
                            "<input type='submit' id='Update_btn' class='btn btn-primary' style='float:right' value='Update'/>".
                        "</td>".
                    "<tr>";
                }
                ?>

            </table>


        </form>


</body>
</html>


