<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<div id="hld">

    <div class="wrapper">        <!-- wrapper begins -->


        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>


            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px;
                                               height=35px; style="padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png"
                 style="padding-top:10px;float:right;padding-left:40px;"/>


            <ul id="nav">
                <?php if ($logged_in_user['profile_id'] != 6) { ?>
                    <?php if ($logged_in_user['profile_id'] != 7) { ?>
                        <li class="active"><a href="<?php echo ROOT_FOLDER ?>/account_manager/home">Home</a></li>
                    <?php } ?>
                    <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/approved_ros">Approved RO's</a></li>

                    <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user_details">User Details</a></li>
                    <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_ro_report">Reports</a>
                        <ul id="nav">
                            <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/external_ro_report">External RO Report</a>
                            </li>
                            <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_ro_report">Network RO Report</a>
                            </li>
                            <li><a href="<?php echo ROOT_FOLDER ?>/account_manager/am_invoice_report">Collection
                                    Report</a></li>
                            <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_remittance_report">N/w
                                    Remittance</a></li>
                            <?php if ($logged_in_user['profile_id'] == 1 or $logged_in_user['profile_id'] == 2 or $logged_in_user['profile_id'] == 7) { ?>
                                <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/update_network_payment">Update N/w
                                        Payment</a></li>
                            <?php } ?>
                        </ul>
                    </li>

                <?php } else { ?>
                    <li class="active"><a href="<?php echo ROOT_FOLDER ?>/account_manager/home">Home</a></li>
                    <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/approved_ros">Approved RO's</a></li>
                    <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user_details">User Details</a></li>
                    <li><a href="<?php echo ROOT_FOLDER ?>/account_manager/am_invoice_report">Reports</a>
                        <ul id="nav">
                            <li><a href="<?php echo ROOT_FOLDER ?>/account_manager/am_invoice_report">Collection
                                    Report</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a
                        href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>        <!-- #header ends -->


        <div class="block">


            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>Invoice Collection</h2>

            </div>        <!-- .block_head ends -->


            <div class="block_content">

                <?php if ($chk_add_inv == '1') { ?>
                    <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_invoice_collection" method="post">
                        <table cellpadding="0" cellspacing="0" width="100%">

                            <tr>
                                <td width="30%">External RO Number<span style="color:#F00;"> *</span></td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <!--<select id="sel_ro" name="sel_ro" style="width:220px;">
                                <?php //foreach($all_ro as $ros){?>
                                	<option value="<?php //echo $ros['cust_ro']?>"><?php echo $ros['cust_ro'] ?></option>
                                <?php //}?>
                                </select>-->

                                    <?php echo $external_ro ?>

                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">Invoice No.<span style="color:#F00;"> *</span></td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <input type="text" id="txt_inv_no" name="txt_inv_no" style="width:220px;"/>
                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">Complete Payment Collected</td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <input type="checkbox" id="chk_complete" name="chk_complete"/>
                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">Collection Date<span style="color:#F00;"> *</span></td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <input type="text" id="txt_collection_date" readonly="readonly"
                                           name="txt_collection_date" style="width:220px;"/>
                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">Cheque No.<span style="color:#F00;"> *</span></td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <input type="text" id="txt_cheque_no" name="txt_cheque_no" style="width:220px;"/>
                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">Cheque Date</td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <input type="text" id="txt_cheque_date" readonly="readonly" name="txt_cheque_date"
                                           style="width:220px;"/>
                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">Amount Collected<span style="color:#F00;"> *</span></td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <input type="text" id="txt_amnt_collected" name="txt_amnt_collected"
                                           style="width:220px;"/>
                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">TDS Amount Deducted<span style="color:#F00;"> *</span></td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <input type="text" id="txt_tds" name="txt_tds" style="width:220px;"/>
                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="30%">Comment<span style="color:#F00;"> *</span></td>
                                <td width="5%"> :</td>
                                <td width="25%">
                                    <input type="text" id="txt_comment" name="txt_comment" style="width:220px;"/>
                                </td>
                                <td width="50%">&nbsp;</td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="hidden" name="hid_user_id" id="hid_user_id"
                                           value="<?php echo $logged_in_user['user_id'] ?>"/>
                                    <input type="hidden" name="hid_ext_id" id="hid_ext_id"
                                           value="<?php echo $external_ro ?>"/>
                                    <input type="submit" class="submit" value="Submit" onclick="return check_form();"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                <?php } ?>

                <!-- .paggination ends -->
            </div>        <!-- .block_content ends -->

            <div class="block_content">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <th>Invoice No</th>
                        <th>Complete Payment Collected</th>
                        <th>Collection Date</th>
                        <th>Cheque No</th>
                        <th>Cheque Date</th>
                        <th>Amount Collected</th>
                        <th>TDS</th>
                        <th>Comment</th>
                    </tr>
                    <?php
                    foreach ($all_invoice_for_ro as $inv) {
                        ?>
                        <tr>
                            <td><?php echo $inv['invoice_no'] ?></td>
                            <td><?php if ($inv['chk_complete'] == 0) echo 'No'; else echo 'Yes'; ?></td>
                            <td><?php echo $inv['collection_date'] ?></td>
                            <td><?php echo $inv['cheque_no'] ?></td>
                            <td><?php echo $inv['cheque_date'] ?></td>
                            <td><?php echo $inv['amnt_collected'] ?></td>
                            <td><?php echo $inv['tds'] ?></td>
                            <td><?php echo $inv['comment'] ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

            </div>

            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>        <!-- .block ends -->

    </div>                        <!-- wrapper ends -->

</div>        <!-- #hld ends -->


<script language="javascript">

    function check_form() {
        /*if($.trim($('#sel_ro').val()) == ""){
                alert("Please select External RO.");
                $('#sel_ro').focus();
                return false;
        }*/
        if ($('#txt_inv_no').val() == "") {
            alert("Please enter Invoice no.");
            $('#txt_inv_no').focus();
            return false;
        }
        var invoice_no = document.getElementById('txt_inv_no').value;
        var check_input = Utility.checkInvoice(invoice_no);
        if (!check_input) {
            alert("Invalid Invoice No");
            document.getElementById('txt_inv_no').focus();
            return false;
        }

        /*
        var reg_anum = /^[a-zA-Z0-9\/_]+$/;
        if (!reg_anum.test(invoice_no)){
            alert("Invalid Invoice No.");
            document.getElementById('txt_inv_no').value = "";
            document.getElementById('txt_inv_no').focus();
            return false;		
        }*/
        if ($('#txt_collection_date').val() == "") {
            alert("Please select collection date.");
            $('#txt_collection_date').focus();
            return false;
        }
        if ($('#txt_cheque_no').val() == "") {
            alert("Please enter Cheque No.");
            $('#txt_cheque_no').focus();
            return false;
        }

        var cheque_no = document.getElementById('txt_cheque_no').value;
        var check_input = Utility.checkInput3(cheque_no);
        if (!check_input) {
            alert("Invalid Cheque No");
            document.getElementById('txt_cheque_no').focus();
            return false;
        }

        /*var regx = /^[a-zA-Z0-9\/_]+$/;
        if(!regx.test(cheque_no)){
            alert('Invalid Cheque No.');
            document.getElementById('txt_cheque_no').value = "";
            document.getElementById('txt_cheque_no').focus();
            return false;
        }*/
        if ($('#txt_amnt_collected').val() == "") {
            alert("Please enter amount collected.");
            $('#txt_amnt_collected').focus();
            return false;
        }

        var amnt_collected = document.getElementById('txt_amnt_collected').value;
        var test_amount = Utility.checkAmount(amnt_collected);
        if (!test_amount) {
            alert("Invalid Amount");
            document.getElementById('txt_amnt_collected').value = "";
            document.getElementById('txt_amnt_collected').focus();
            return false;
        }

        /*
        var reg_num = /^[0-9]+$/;
        cheque_no = document.getElementById('txt_amnt_collected').value;
        if(!reg_num.test(cheque_no)){
            alert('Please enter only numeric chanracters');
            document.getElementById('txt_amnt_collected').value = "";
            document.getElementById('txt_amnt_collected').focus();
            return false;
        }*/

        if ($('#txt_tds').val() == "") {
            alert("Please enter TDS Amount Deducted.");
            $('#txt_tds').focus();
            return false;
        }

        var tds = document.getElementById('txt_tds').value;
        var test_amount = Utility.checkAmount(tds);
        if (!test_amount) {
            alert("Invalid Amount");
            document.getElementById('txt_tds').value = "";
            document.getElementById('txt_tds').focus();
            return false;
        }

        if ($("#chk_complete").is(':checked')) {
            $('#chk_complete').val("1");
        }

        var txt_comment = document.getElementById('txt_comment').value;
        var txt_comment = Utility.checkSingleDoubleQuotes(txt_comment);
        if (!txt_comment) {
            alert("Invalid Comment");
            document.getElementById('txt_comment').focus();
            return false;
        }
        //else{alert(123)
        //$('#chk_complete').val("0");alert($('#chk_complete').val())
        //}

    }

    $(document).ready(function () {
        $("#txt_collection_date").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true
        });
        $("#txt_cheque_date").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true
        });
    });
</script>
