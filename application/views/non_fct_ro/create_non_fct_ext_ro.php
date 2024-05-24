<html>
<head>

    <script src="/surewaves_easy_ro/js/create_non_fct_ro_script.js"></script>
    <link rel="stylesheet" href="/surewaves_easy_ro/css/create_non_fct_ext_ro.css">
</head>
<body>
<!--------------------------------------Modal------------------------------------------------->
<div class="modal fade " id="non_fct_ro_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header" id="non_fct_ro_modal_header">
                <h5 id="sub_modal_title" class="modal-title"></h5>
                <button type="button" class="btn close" id="sub_modal_btn">&times;</button>
                </div>

            <!-- Modal body -->
            <div class="modal-body" id="sub_modal_body">
            </div>
        </div>
    </div>
</div> <!--End Of Modal-->
<!---------------------------------------------Non fct ro form------------------------------------------->
<form id="non_fct_ro_form">
    <table class="table" id="non_fct_ext_ro_table">

        <!----------Ro Number--------->
        <tr>
            <td width="30%">RO Number<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" id="txt_ext_ro" name="txt_ext_ro" class="stylingwidth"
                       value="<?php if (isset($cust_ro) && $cust_ro != '') {
                           echo $cust_ro;
                       } ?>"
                       />
            </td>
            <td width="40%">&nbsp;</td>
        </tr>
        <!------Agency-------------->
        <tr>
            <td>Agency<span class="Asterik"> *</span></td>
            <td> :</td>
            <td>
                <select name="sel_agency" class="form-control stylingwidth" id="sel_agency" >
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
            </td>
            <td><span id="non_fct_add_agency_span" class="link">Add Agency</span></td>
        </tr>

        <!-------Agency contact------>
        <tr>
            <td>Agency Contact<span class="Asterik"> *</span></td>
            <td>:</td>
            <td>
                <select name="sel_agency_contact" class="form-control stylingwidth" id="sel_agency_contact" >
                    <option value="new">New</option>
                </select>
            </td>
            <td width="50%"><span id="non_fct_add_edit_agency_contact_span" class="link">Add/Edit</span></td>
        </tr>

        <!------Client---------->
        <tr>
            <td width="20%">Client<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <select name="sel_client" class="form-control stylingwidth" id="sel_client" >
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
            <td width="50%">
                <span id="non_fct_activate_client_span" class="link">Activate Client</span></td>
        </tr>

        <!------Client Contact----->
        <tr>
            <td>Client Contact</td>
            <td>:</td>
            <td>
                <select name="sel_client_contact" class="form-control stylingwidth" id="sel_client_contact" >
                    <option value="new">New</option>
                </select>
            </td>
            <td width="50%"><span id="non_fct_add_edit_client_contact_span" class="link">Add/Edit</span></td>
        </tr>

        <!-----Account Manager Name--->
        <tr>
            <td width="30%">Account Manager Name<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" id="txt_am_name" name="txt_am_name"  class="stylingwidth" readonly="readonly"
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

        <!----Region--->
        <tr>
            <td width="20%">Region<span style="color:#F00;"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input id="regionSelectBox" class="stylingwidth" readonly name="regionSelectBox" data="<?php echo $regionArray[0]['region_id'] ?>"
                        value="<?php echo $regionArray[0]['region_name'] ?>" >
            </td>
            <td width="40%">&nbsp;</td>
        </tr>
        <!---Financial Year--->
        <tr>
            <td width="20%">Financial Year<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <select class="form-control stylingwidth" name="fin_year" id="fin_year" >
                    <option selected disabled>Choose Year</option>
                    <?php $year = 2019;
                    while ($year < 2024) {
                        $z = $year + 1; ?>
                        <option value="<?php echo $year . "-" . $z ?>"><?php echo $year . "-" . $z ?></option>
                        <?php $year++;
                    } ?>
                </select>
            </td>
            <td colspan="3">&nbsp;</td>
        </tr>

        <!---Market row---->
        <tr id="market_tr">
            <td colspan="4" style="padding:0px;">
                <div id="market_error_div"></div>
                <div id="market_div">
                    <table width="100%">
                        <tr>
                            <td colspan="8" nowrap="nowrap" class="monthwise_ro_amount" >
                                Monthwise RO Amount :
                            </td>
                        </tr>
                        <tr>
                            <td class="no_border">
                                <span>January</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="jan_amount" name="amount[january]"
                                                     class="fin_amount" style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>


                            <td class="no_border">
                                <span>February</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="feb_amount" name="amount[february]"
                                                     class="fin_amount" style="width:80px;" value="0"onblur="calculate_gross()"/></span>
                            </td>

                            <td class="no_border">
                                <span>March</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="march_amount" name="amount[march]"
                                                     class="fin_amount" style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>

                        </tr>

                        <tr>

                            <td class="no_border">
                                <span>April</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="april_amount" name="amount[april]"
                                                     class="fin_amount" style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>

                            <td class="no_border">
                                <span>May</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="may_amount" name="amount[may]" class="fin_amount"
                                                     style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>

                            <td class="no_border">
                                <span>June</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="june_amount" name="amount[june]" class="fin_amount"
                                                     style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>

                        </tr>

                        <tr>
                            <td class="no_border">
                                <span>July</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="july_amount" name="amount[july]" class="fin_amount"
                                                     style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>

                            <td class="no_border">
                                <span>August</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="aug_amount" name="amount[august]"
                                                     class="fin_amount" style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>

                            <td class="no_border">
                                <span>September</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="sep_amount" name="amount[september]"
                                                     class="fin_amount" style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>
                        </tr>

                        <tr>

                            <td class="no_border">
                                <span>October</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="oct_amount" name="amount[october]"
                                                     class="fin_amount" style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>

                            <td class="no_border">
                                <span>November</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="nov_amount" name="amount[november]"
                                                     class="fin_amount" style="width:80px;" value="0" onblur="calculate_gross()"
                                                     /></span>
                            </td>

                            <td class="no_border">
                                <span>December</span>
                            </td>

                            <td class="no_border">
                                        <span><input type="text" id="dec_amount" name="amount[december]"
                                                     class="fin_amount" style="width:80px;" value="0" onblur="calculate_gross()"/></span>
                            </td>

                        </tr>

                    </table>
                </div>
            </td>
        </tr>

        <!-----Gross Ro Amount------>
        <tr>
            <td width="30%">Gross RO Amount<span style="color:#F00;"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" id="txt_gross" name="txt_gross" class="stylingwidth" value="0" readonly="readonly"/>
            </td>
            <td width="40%">&nbsp;</td>
        </tr>

        <!----Total Expense------>
        <tr>
            <td width="30%">Total Expenses</td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" id="txt_agency_com" name="txt_agency_com" class="stylingwidth" value="0.00"/>
            </td>
            <td width="40%">&nbsp;</td>
        </tr>

        <!---Description------->
        <tr>
            <td width="30%">Description</td>
            <td width="5%"> :</td>
            <td width="25%">
                        <textarea id="txt_spcl_inst" name="txt_spcl_inst" class="stylingwidth" style="height:70px;"
                                  placeholder="Any instruction including Billing Instruction"></textarea>
            </td>
            <td width="40%">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="4">
                <input type="submit" class="submit btn btn-primary" value="Create" id="create_non_btn"/>
            </td>
        <tr>

    </table>


</form>
</body>
</html>
