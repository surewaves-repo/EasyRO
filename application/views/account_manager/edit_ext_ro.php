<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div class="block small center login" style="margin:0px; width:796px;">


    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Edit External RO</h2>
    </div>

    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_edit_ext_ro" method="post"
              enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td colspan="4"><?php if (isset($result) && $result == 'exists') {
                            echo '<span style="color:#F00;">Agency all ready exists for this RO.</span>';
                        } ?>
                        <?php if (isset($client_result) && $client_result == 'exists') {
                            echo '<span style="color:#F00;">Client all ready exists for this RO.</span>';
                        } ?>
                        <?php if (isset($brand_result) && $brand_result == 'exists') {
                            echo '<span style="color:#F00;">Brand all ready exists</span>';
                        } ?>
                    </td>

                </tr>
                <tr>
                    <td width="30%">External RO Number<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_ext_ro" name="txt_ext_ro" style="width:220px;"
                               value="<?php echo $ro_details[0]['cust_ro'] ?>"
                               <?php if ($campaign_created == 1){ ?>readonly="readonly" <?php } ?> />
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">RO Date<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_ro_date" name="txt_ro_date" readonly="readonly" style="width:220px;"
                               value="<?php echo $ro_details[0]['ro_date'] ?>"
                               <?php if ($campaign_created == 1){ ?>readonly="readonly" <?php } ?> />
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td>Agency<span style="color:#F00;"> *</span></td>
                    <td> :</td>
                    <td>
                        <input type="text" id="txt_agency" name="txt_agency" style="width:220px;"
                               value="<?php echo $ro_details[0]['agency'] ?>" readonly="readonly"/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Agency Contact Name<span style="color:#F00;"> *</span></td>
                    <td> :</td>
                    <td>
                        <input type="text" id="txt_agency_contact" name="txt_agency_contact" style="width:220px;"
                               value="<?php echo $ro_details[0]['agency_contact_name'] ?>" readonly="readonly"/>
                    </td>
                    <td><a href="javascript:add_agency_contact()">Add/Edit</a></td>
                </tr>
                <tr>
                    <td width="20%">Client<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_client" name="txt_client" style="width:220px;"
                               value="<?php echo $ro_details[0]['client'] ?>" readonly="readonly"/>
                    </td>
                    <td width="50%">&nbsp;</td>
                </tr>
                <tr>
                    <td>Client Contact Name<span style="color:#F00;"> *</span></td>
                    <td> :</td>
                    <td>
                        <select name="txt_client_contact" id="txt_client_contact" style="width:220px;">
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
                    <td><a href="javascript:add_client_contact()">Add/Edit</a></td>
                </tr>
                <tr>
                    <td width="20%">Brand<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <?php echo $ro_details[0]['brand_name'] ?>
                    </td>
                    <td width="50%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="20%">Make Good Type<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="" colspan="2">


                        <input type="radio" name="rd_make_good" value="0"
                               <?php if ($ro_details[0]['make_good_type'] == 0) {
                                   echo 'checked';
                               }
                               if ($campaign_created == 1){ ?>readonly="readonly" <?php } ?>/>&nbsp;Auto MakeGood &nbsp;&nbsp;
                        <input type="radio" name="rd_make_good" value="1"
                               <?php if ($ro_details[0]['make_good_type'] == 1) {
                                   echo 'checked';
                               }
                               if ($campaign_created == 1){ ?>readonly="readonly" <?php } ?>/>&nbsp;Client Confirmed
                        MakeGood &nbsp;&nbsp;
                        <input type="radio" name="rd_make_good" value="2"
                               <?php if ($ro_details[0]['make_good_type'] == 2) {
                                   echo 'checked';
                               }
                               if ($campaign_created == 1){ ?>readonly="readonly" <?php } ?> />&nbsp;No MakeGood
                    </td>

                </tr>
                <!--<tr>
							<td width="30%">Industry</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_industry" name="txt_industry" style="width:220px;" value="<?php echo $ro_details[0]['industry'] ?>" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>-->
                <tr>
                    <td width="30%">Account Manager Name<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <select name="sel_am" style="width:220px;">
                            <?php foreach ($all_am as $am) {
                                if ($am['user_id'] == $ro_details[0]['user_id']) {
                                    ?>
                                    <option selected="selected"
                                            value="<?php echo $am['user_id'] ?>"><?php echo $am['user_name'] ?></option>
                                <?php } else {
                                    ?>
                                    <option value="<?php echo $am['user_id'] ?>"><?php echo $am['user_name'] ?></option>
                                <?php }
                            } ?>
                        </select>

                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <!--<tr>
							<td width="30%">Vertical</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_vertical" name="txt_vertical" style="width:220px;" value="<?php echo $ro_details[0]['vertical'] ?>" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
						<tr>
							<td width="30%">Region</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_region" name="txt_region" style="width:220px;" value="<?php echo $ro_details[0]['region'] ?>" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>-->
                <tr>
                    <td width="30%">Market<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <?php echo $ro_details[0]['market'] ?>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td width="30%">Campaign Start Date<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_camp_start_date" readonly="readonly" name="txt_camp_start_date"
                               style="width:220px;" value="<?php echo $ro_details[0]['camp_start_date'] ?>"
                               <?php if ($campaign_created == 1){ ?>readonly="readonly" <?php } ?> />
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Campaign End Date<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_camp_end_date" readonly="readonly" name="txt_camp_end_date"
                               style="width:220px;" value="<?php echo $ro_details[0]['camp_end_date'] ?>"
                               <?php if ($campaign_created == 1){ ?>readonly="readonly" <?php } ?> />
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <!--<tr>
							<td width="30%">Booking Year and Month</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_booking" name="txt_booking" style="width:220px;" readonly="readonly" value="<?php echo $ro_details[0]['booking'] ?>" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
							<td width="30%">Category</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_category" name="txt_category" style="width:220px;" value="<?php echo $ro_details[0]['category'] ?>" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>-->
                <tr>
                    <td width="30%">Gross Amount<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_gross" name="txt_gross" style="width:220px;"
                               value="<?php echo $ro_details[0]['gross'] ?>" onblur="calculate_net_after_commission()"
                               onkeyup="price_check(this.value)"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Agency Commission Payable</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_agency_com" name="txt_agency_com" style="width:220px;"
                               value="<?php echo $ro_details[0]['agency_com'] ?>"
                               onblur="calculate_net_after_commission()" onkeyup="price_check(this.value)"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Net After Agency Commission</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_net_agency_com" name="txt_net_agency_com" style="width:220px;"
                               value="<?php echo $ro_details[0]['net_agency_com'] ?>" readonly="readonly"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Special Instructions</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" id="txt_spcl_inst" name="txt_spcl_inst" style="width:220px;"
                               value="<?php echo $ro_details[0]['spcl_inst'] ?>"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Browse PDF/Excel</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <?php if ($ro_details[0]['file_path'] == '') {
                            echo 'No Attachment Found';
                        } else { ?>
                            <a href="<?php echo $ro_details[0]['file_path'] ?>">Download RO Attachment</a>
                        <?php } ?>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td>
                        <input type="hidden" name="hid_id" id="hid_brand" value="<?php echo $ro_details[0]['id'] ?>"/>
                        <input type="hidden" name="hid_campaign_created" id="hid_campaign_created"
                               value="<?php echo $campaign_created ?>"/>
                        <input type="submit" class="submit" value="Update" onclick="return check_form();"/>
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
    function price_check(value) {
        var regex = /^[0-9.]+$/;
        gross = document.getElementById('txt_gross').value;
        agency_com = document.getElementById('txt_agency_com').value;
        if (!regex.test(gross)) {
            alert('not allowed');
            document.getElementById('txt_gross').value = "";
            document.getElementById('txt_gross').focus();
        }
        if (!regex.test(agency_com)) {
            alert('not allowed');
            document.getElementById('txt_agency_com').value = 0;
            document.getElementById('txt_agency_com').focus();
        }
    }

    function check_form() {
        if ($('#txt_ext_ro').val() == "") {
            alert("Please enter External RO Number.");
            $('#txt_ext_ro').focus();
            return false;
        }
    }

    function calculate_net_after_commission() {
        gross_arr = $('#txt_gross').val().split(".");
        if (gross_arr.length > 2) {
            alert("Please enter vallid amount");
            $('#txt_gross').val("");
            $('#txt_gross').focus();
            return false;
        }
        agency_com_arr = $('#txt_agency_com').val().split(".");
        if (agency_com_arr.length > 2) {
            alert("Please enter vallid amount");
            $('#txt_agency_com').val("0");
            $('#txt_agency_com').focus();
            return false;
        }

        var gross = parseFloat($('#txt_gross').val());
        var commission = parseFloat($('#txt_agency_com').val());
        if (commission > gross) {
            alert('Agency commission can not be greater than gross ro amount.');
            $('#txt_agency_com').val("0");
            commission = 0;
        }
        if (isNaN(gross - commission)) {
            $('#txt_net_agency_com').val('0');
        } else {
            $('#txt_net_agency_com').val(gross - commission);
        }
    }

    $(document).ready(function () {

        $("#txt_ro_date").datepicker({
            defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            onClose: function (selectedDate) {
                $("#txt_camp_start_date").datepicker("option", "minDate", selectedDate);
                $("#txt_camp_end_date").datepicker("option", "minDate", selectedDate);
            }
        }).datepicker("option", "minDate", "0");
        $("#txt_camp_start_date").datepicker({
            defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            onClose: function (selectedDate) {
                $("#txt_camp_end_date").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#txt_camp_end_date").datepicker({
            defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            onClose: function (selectedDate) {
                $("#txt_camp_start_date").datepicker("option", "maxDate", selectedDate);
            }
        });

    });

    function add_client_contact() {
        var client = encodeURIComponent("<?php echo $ro_details[0]['client']?>");
        window.location = "<?php echo ROOT_FOLDER ?>/account_manager/add_edit_client_contact/" + client + "/" + $('#sel_client_contact_name').val() + "/" + $('#txt_ext_ro').val() + '/' + $('#txt_ro_date').val() + '/' + $('#txt_agency').val() + '/' + $('#txt_agency_contact').val() + "/<?php echo $id ?>";
    }

    function add_agency_contact() {
        var client = encodeURIComponent("<?php echo $ro_details[0]['client']?>");
        window.location = "<?php echo ROOT_FOLDER ?>/account_manager/add_edit_agency_contact/" + "<?php echo $ro_details[0]['agency']?>/" + $('#sel_agency_contact_name').val() + '/' + $('#txt_ext_ro').val() + '/' + $('#txt_ro_date').val() + "/" + client + "/" + $('#sel_client_contact_name').val() + "/<?php echo $id ?>";
    }

</script>
