<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div id="hld">

    <div class="wrapper">        <!-- wrapper begins -->


        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"
                                               style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png"
                 style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a
                        href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>        <!-- #header ends -->

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>Cancel Markets</h2>
            </div>

            <div class="block_content">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <th>Customer RO Number</th>
                        <th>Agency Name</th>
                        <th>Advertise name</th>
                        <th>Brand Name</th>
                        <th>RO Start Date</th>
                        <th>RO End Date</th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <td> <?php echo $am_ext_ro ?><br/> <?php echo '(Internal RO Number:' . $internal_ro . ")"; ?>
                        </td>
                        <td><?php echo $agency ?> &nbsp;</td>
                        <td><?php echo $client ?></td>
                        <td><?php echo $brand ?></td>
                        <td><?php print date('d-M-Y', strtotime($camp_start_date)); ?> </td>
                        <td><?php print date('d-M-Y', strtotime($camp_end_date)); ?> </td>
                        <td>
                            <a href="javascript:approve('<?php echo rtrim(base64_encode($internal_ro), '=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Approval
                                Page</a></td>
                    </tr>

                </table>
                <br>

                <div id="cancel_message" style="display: none;text-align: center">
                    <span style="color:#F00;">This is the complete cancellation of market (Spot rate = 0, banner rate = 0)</span>
                </div>


            </div>    <!-- .block_content ends -->

            <div class="block_content" style="height: auto">
                <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_cancel_markets" method="post">
                    <table cellpadding="0" cellspacing="0" width="100%">

                        <tr id="date_controls" style="display: none">
                            <td>&nbsp;</td>
                            <td><span>Cancel From :</span> <span style="color:#F00;"> *</span></td>
                            <td>
                                <input type="text" id="txt_cancel_date" class="amount" readonly="readonly"
                                       name="txt_cancel_date" style="width:250px;"/>
                            </td>
                        </tr>

                        <tr>
                            <th style="width:5%"></th>
                            <th style="width:30%">Markets</th>
                            <th style="width:40%">Spot Price</th>
                            <th style="width:25%">Banner Price</th>
                        </tr>

                        <tr id="reason_controls" style="display: none">
                            <td>&nbsp;</td>
                            <td><span id="reason_label">Reason for Cancellation :</span> <span
                                        style="color:#F00;"> *</span></td>
                            <td><input type="text" name="reason_can" id="reason_can" class="amount" value=""
                                       style="width:250px;"</td>
                        </tr>

                        <tr id="req_controls" style="display: none">
                            <td>&nbsp;</td>
                            <td>
                                <input type="submit" class="submitlong" id="req_market_cancel" value="Approval Request"
                                       onclick="return check_form();"/>
                            </td>
                        </tr>

                    </table>
                </form>
            </div>

        </div>
    </div>


    <script type="text/javascript" language="javascript">
        $(document).ready(function () {
            $("#txt_cancel_date").datepicker({
                //defaultDate: "+1w",
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                onClose: function (dateText, inst) {

                }
            }).datepicker("option", "minDate", "2");
        });

        function check_form() {
            if ($('#txt_cancel_date').val() == "") {
                alert("Please enter date of cancel.");
                return false;
            }
            if ($('#reason_can').val() == "") {
                alert("Please enter Reason for Cancellation");
                $('#reason_can').focus();
                return false;
            }
            if ($('.markets_to_cancel:checked').length == $('.markets_to_cancel').length) {
                alert("Please use Cancel RO to cancel all Markets");
                return false;
            }
        }
    </script>    