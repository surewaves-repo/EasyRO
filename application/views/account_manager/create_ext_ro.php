
<!--------------------------------------Modal------------------------------------------------->

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
<!---------------------------------------------------Form---------------------------------------->
<img src="<?php echo ROOT_FOLDER ?>/images/loading_image.gif" id="loading_imag" style="display: none">
<form id="ext_ro_form" enctype="multipart/form-data">


    <table id="create_ext_ro_table" class="table ">

        <!-------------------------------------External RO Row---------------------------------------->
        <tr>
            <td class="first_row" width="30%">External RO Number<span class="Asterik"> *</span></td>
            <td class="first_row" width="5%"> :</td>
            <td class="first_row" width="25%">
                <input class="stylingwidth" type="text" id="txt_ext_ro" name="txt_ext_ro"
                       value="<?php if (isset($cust_ro) && $cust_ro != '') {
                           echo $cust_ro;
                       } ?>"/>
            </td>
            <td class="first_row invalid" id="txt_ext_ro_error" width="40%">&nbsp;</td>
        </tr>

        <!-----------------------------------------RO Date Row------------------------------------------>

        <tr>
            <td width="30%">RO Date<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">

                <input type="text" class="stylingwidth" id="txt_ro_date" readonly="readonly"
                       name="txt_ro_date" value="<?php if (isset($ro_date)) {
                    echo $ro_date;
                } ?>"/>
                <span class="fa fa-calendar ro_date"></span>
            </td>
            <td width="40%" id="txt_ro_date_error">&nbsp;</td>
        </tr>

        <!------------------------------------------Agency Row-------------------------------------->

        <tr>
            <td>Agency<span class="Asterik"> *</span></td>
            <td> :</td>
            <td>
                <select class="form-control" name="sel_agency" class="form-control stylingwidth" style="width:220px;" id="sel_agency">
                    <option selected disabled>Choose Agency</option>
                    <?php

                    foreach ($all_agency as $agencies) {
                        if (empty($agencies['agency_display_name']) || !isset($agencies['agency_display_name'])) {
                            continue;
                        }
                        if ($agency == $agencies['agency_display_name']) {
                            ?>
                            <option value="<?php echo $agencies['agency_display_name'] ?>"
                                    selected="selected"><?php echo $agencies['agency_display_name'] ?></option>
                        <?php } else {
                            ?>
                            <option value="<?php echo $agencies['agency_display_name'] ?>"><?php echo $agencies['agency_display_name'] ?></option>
                        <?php }
                    } ?>
                </select>
            <td width="40%" id="sel_agency_error">
            </td>

        </tr>

        <!-----------------------------------Agency Contact Row--------------------------------------->
        <tr>
            <td>Agency Contact<span class="Asterik"> *</span></td>
            <td>:</td>
            <td>
                <select name="sel_agency_contact" class="form-control stylingwidth" id="sel_agency_contact">
                    <option value="new">New</option>
                </select>
            </td>
            <td width="20%" id="sel_agency_contact_error"><span class="link"
                                                                id="add_edit_agency_contact_span">Add/Edit</span></td>
        </tr>

        <!-----------------------------------------Client Row------------------------------------------>
        <tr>
            <td width="20%">Client<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <select name="sel_client" class="form-control stylingwidth" id="sel_client">
                    <option selected disabled>Choose Client</option>
                    <?php foreach ($all_client as $advs) {
                        if ($client == $advs['advertiser_display_name']) {
                            ?>
                            <option value="<?php echo $advs['advertiser_display_name'] ?>"
                                    selected="selected"><?php echo $advs['advertiser_display_name'] ?></option>
                        <?php } else {
                            ?>
                            <option value="<?php echo $advs['advertiser_display_name'] ?>"><?php echo $advs['advertiser_display_name'] ?></option>
                        <?php }
                    } ?>
                </select>
            </td>
            <td width="50%" id="sel_client_error"></td>
        </tr>

        <!-----------------------------------------Client Contact Row----------------------------------->
        <tr>
            <td>Client Contact<span id="int_valid" style="display:none;color:#F00;"> *</span></td>
            <td>:</td>
            <td>
                <select name="sel_client_contact" class="form-control stylingwidth" id="sel_client_contact">
                    <option value="new">New</option>
                </select>
            </td>
            <td width="50%" id="sel_client_contact_error"><span class="link"
                                                                id="add_edit_client_contact_span">Add/Edit</span></td>
        </tr>

        <!-------------------------------------------GSTIN Row------------------------------------------->
        <tr>
            <td>GSTIN<span class="Asterik"> *</span></td>
            <td>:</td>
            <td>
                <input type="text" class="stylingwidth" id="txt_gstin" name="txt_gstin"/>

            </td>
            <td width="50%"><span class="link" id="add_gst">Add</td>
            </td>
        </tr>

        <!---------------------------------------Brand Row----------------------------------------------->
        <tr>
            <td width="20%">Brand<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <select name="sel_brand" class="stylingwidth" id="sel_brand" multiple="multiple">
                    <option selected disabled>NO BRAND</option>
                </select>
            </td>
            <td width="50%" id="sel_brand_error">
            </td>
        </tr>

        <!---------------------------------------Make Good Row--------------------------------------->
        <tr>
            <td width="20%">Make Good Type<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="" id="rd_make_good_error" colspan="2">
                <input type="radio" name="rd_make_good" value="0" checked="checked"/>&nbsp;Auto MakeGood &nbsp;&nbsp;
                <input type="radio" name="rd_make_good" value="1"/>&nbsp;Client Confirmed MakeGood &nbsp;&nbsp;
                <input type="radio" name="rd_make_good" value="2"/>&nbsp;No MakeGood
            </td>

        </tr>

        <!---------------------------------Account Manager Row-------------------------------------->
        <tr>
            <td width="30%">Account Manager Name<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="stylingwidth" id="txt_am_name" name="txt_am_name" readonly="readonly"
                       value="<?php if (isset($user_name)) {
                           echo $user_name;
                       } else {
                           echo $logged_in_user['user_name'];
                       } ?>"/>
                <input type="hidden" id="hid_user_id" name="hid_user_id" style="width:220px;"
                       value="<?php if (isset($user_id)) {
                           echo $user_id;
                       } else {
                           echo $logged_in_user['user_id'];
                       } ?>"/>
                <input type="hidden" id="user_type" name="user_type"
                       value="<?php echo $logged_in_user['is_test_user']; ?>"/>
            </td>
            <td width="40%">&nbsp;</td>
        </tr>
        <!-----------------------------------Region------------------------------------>
        <tr>
            <td width="20%">Region<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input id="regionSelectBox" readonly name="regionSelectBox" class=" stylingwidth"
                       data="<?php echo $regionArray[0]['region_id'] ?>"
                       value="<?php echo $regionArray[0]['region_name'] ?>">
            </td>
            <td width="40%" id="regionSelectBox_error">&nbsp;</td>
        </tr>
        <!---------------------------------Campaign Start Date------------------------------------->
        <tr>
            <td width="30%">Campaign Start Date<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="stylingwidth" class="form-control" id="txt_camp_start_date"
                       readonly="readonly" name="txt_camp_start_date"/>
                <span class="fa fa-calendar camp_start_date"></span>
            </td>
            <td width="40%" id="txt_camp_start_date_error">&nbsp;</td>
        </tr>

        <!--------------------------------Campaign End Date--------------------------------------->
        <tr>
            <td width="30%">Campaign End Date<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="stylingwidth" id="txt_camp_end_date" readonly="readonly"
                       name="txt_camp_end_date"/>
                <span class="fa fa-calendar camp_end_date"></span>
            </td>
            <td width="40%" id="txt_camp_end_date_error">&nbsp;</td>
        </tr>


        <!------------------------Markets-------------------------------------->
        <tr>
            <td width="30%">Markets<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <select multiple class="stylingwidth" id="sel_market" name="sel_market" style="width:240px;">
                    <optgroup label="Market Clusters">
                        <?php foreach ($all_clusters as $clusters) { ?>
                            <option value="<?php echo $clusters['sw_market_name'] ?>"><?php echo $clusters['sw_market_name'] ?></option>
                        <?php } ?>
                    </optgroup>
                    <optgroup label="Markets">
                        <?php foreach ($all_markets as $markets) { ?>
                            <option value="<?php echo $markets['sw_market_name'] ?>"><?php echo $markets['sw_market_name'] ?></option>
                        <?php } ?>
                    </optgroup>
                </select>
            </td>
            <td width="40%" id="markets_error">&nbsp;</td>

        </tr>

        <!-------------------------------------Markets Div---------------------------------->
        <tr style="display:none;" id="market_tr">
            <td colspan="100%">
            	<div id="market_error_div"></div>
                <div id="market_div" style="width: 100%">
                </div>
            </td>
        </tr>

        <!--------------------------------------Gross Ro Amount--------------------------------->
        <tr>
            <td width="30%">Gross RO Amount<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="stylingwidth" id="txt_gross" name="txt_gross" value="0" readonly="readonly"/>
            </td>
            <td width="40%" id="txt_gross_error">&nbsp;</td>
        </tr>

        <!----------------------------------Agency Commission Payable------------------------->
        <tr>
            <td width="30%">Agency Commission Payable</td>
            <td width="5%"> :</td>
            <td width="25%">

                <input type="text" class="stylingwidth" name="txt_agency_com_holder" id="txt_agency_com_holder"
                       onblur="calculate_commission();" value="0"/>
                <input type="hidden" id="txt_agency_com" name="txt_agency_com" style="width:220px;" value="0"/>
            </td>
            <td width="20%">
                <input type="radio" class="rd_com" name="rd_com" value="0" checked="checked"
                       onclick="calculate_commission();"/>&nbsp;Percent &nbsp;&nbsp;
                <input type="radio" class="rd_com" name="rd_com" value="1" onclick="calculate_commission();"/>&nbsp;Amount
                &nbsp;&nbsp;
            </td>
            <td colspan="2" id="txt_agency_com_error">

            </td>
        </tr>

        <!----------------------------------Net After Agency Commission----------------------------->
        <tr>
            <td width="30%">Net After Agency Commission</td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" class="stylingwidth" readonly="readonly" id="txt_net_agency_com"
                       name="txt_net_agency_com"/>
            </td>
            <td width="40%" id="txt_net_agency_com_error" style="margin-left: -264px;">&nbsp;</td>
        </tr>

        <!------------------------------------Special Instructions------------------------------------->
        <tr>
            <td width="30%">Special Instructions</td>
            <td width="5%"> :</td>
            <td width="25%">
                <textarea class="stylingwidth" id="txt_spcl_inst" name="txt_spcl_inst" style=" height:70px;"
                          placeholder="Any instruction including Billing Instruction"></textarea>
            </td>
            <td width="40%">&nbsp;</td>
        </tr>

        <!----------------------------------Order Status recipient Mail id/s------------------------------>
        <tr>
            <td>Order status recipient mail id/s<span id="OrderHRIds" style="display:none;color:#F00;"> *</span></td>
            <td>:</td>
            <td>
                <select name="Order_history_recipient_ids" class="stylingwidth" id="Order_history_recipient_ids"
                        multiple="multiple">
                    <option selected disabled>NO IDs</option>
                </select>
            </td>
            <td width="40%"></td>
        </tr>
        <tr id='file_error' style='display:none'>
           <td colspan='11'>
                <span class='Asterik'>Ro Attachement and Client Approval Mail size should not exceed 18mb<span>
           </td>                 
        </tr>                        
        <!-------------------------------------Attach Ro---------------------------------------------->
        <tr>
            <td width="30%">Attach RO<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="file" id="file_pdf" name="file_pdf" class="file_class" multiple/>
            </td>
            <td width="40%" id="file_pdf_error">&nbsp;</td>
        </tr>


        <!------------------------------Client Approval Mail Attachement --------------------------->
        <tr>
            <td width="30%">Client Approval Mail Attachment<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="file" id="client_aproval_mail" name="client_aproval_mail" class="file_class"/>
            </td>
            <td width="40%" id="client_aproval_mail_error">&nbsp;</td>
        </tr>

        <!---------------------------------Submit Button----------------------------------------->
        <tr>
            <td colspan="4">
                <div id="create_ext_ro_error_div" style="display: none"></div>
                <button type="button" id="reset_btn" class="btn btn-primary">RESET</button>
                <button type="submit" id="create_btn" class="submit btn btn-primary">CREATE</button>
            </td>
        <tr>
    </table>
</form>
