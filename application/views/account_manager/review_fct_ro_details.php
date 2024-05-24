<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">

<div class="block">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Review RO Details</h2>
    </div>

    <div class="block_content" style="height: auto">
        <table cellpadding="0" cellspacing="0" width="100%">

            <!--RO details-->
            <tr>
                <td width="40%">External RO Number</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['cust_ro'] ?>
                </td>
            </tr>
            <tr>
                <td width="40%">RO Date</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['ro_date'] ?>
                </td>
            </tr>
            <tr>
                <td>Agency</td>
                <td> :</td>
                <td>
                    <?php echo $ro_details[0]['agency'] ?>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Agency Contact Name</td>
                <td> :</td>
                <td>
                    <?php if ($ro_details[0]['agency_contact_name'] != NULL) {
                        echo $ro_details[0]['agency_contact_name'];
                    } else {
                        echo "None";
                    } ?>
                </td>
            </tr>
            <tr>
                <td width="40%">Client</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['client'] ?>
                </td>
            </tr>
            <tr>
                <td>Client Contact Name</td>
                <td> :</td>
                <td>
                    <?php if ($ro_details[0]['client_contact_name'] != NULL) {
                        echo $ro_details[0]['client_contact_name'];
                    } else {
                        echo "None";
                    } ?>
                </td>
            </tr>
            <tr>
                <td width="40%">Brand</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['brand_name'] ?>
                </td>
            </tr>
            <tr>
                <td width="40%">Make Good Type</td>
                <td width="10%"> :</td>
                <td width="50%" colspan="2">
                    <input type="radio" name="rd_make_good" value="0" <?php if ($ro_details[0]['make_good_type'] == 0) {
                        echo 'checked';
                    } ?> disabled/>&nbsp;Auto MakeGood &nbsp;&nbsp;
                    <input type="radio" name="rd_make_good" value="1" <?php if ($ro_details[0]['make_good_type'] == 1) {
                        echo 'checked';
                    } ?> disabled/>&nbsp;Client Confirmed MakeGood &nbsp;&nbsp;
                    <input type="radio" name="rd_make_good" value="2" <?php if ($ro_details[0]['make_good_type'] == 2) {
                        echo 'checked';
                    } ?> disabled/>&nbsp;No MakeGood
                </td>

            </tr>

            <tr>
                <td width="40%">Account Manager Name</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_assigned_user_name; ?>
                </td>
            </tr>

            <tr>
                <td width="40%">Campaign Start Date</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['camp_start_date'] ?>
                </td>
            </tr>
            <tr>
                <td width="40%">Campaign End Date</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['camp_end_date'] ?>
                </td>
            </tr>


            <tr>
                <td width="40%">Region</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['region_name'] ?>
                </td>
            </tr>

            <tr style="" id="market_tr">
                <td colspan="4" style="padding:0px;">
                    <div id="market_div">
                        <table width="100%">
                            <tr>
                                <?php
                                $i = 0;
                                foreach ($markets_against_ro as $market) {
                                    /*if( $i==2 || ($i%2==0 && $i!=0) ){
                                        echo '</tr><tr>';
                                    }*/
                                    if ($campaign_created == 1) {
                                        echo '<tr>

                                                <td nowrap="nowrap" width="10%" style="border-bottom:none;padding:0px;text-align:right">' . $market['market'] . '</td>
                                                <td width="5%" nowrap="nowrap" style="border-bottom:none;padding:0px;"> : </td>

                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot : ' . $market['spot_price'] . '</td>
                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot FCT :' . $market['spot_fct'] . '</td>
                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot Rate :' . $market['spot_rate'] . '</td>

                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner :' . $market['banner_price'] . '</td>
                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner FCT :' . $market['banner_fct'] . '</td>
                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner Rate :' . $market['banner_rate'] . '</td>

                                             </tr>';

                                    } else {
                                        echo '<tr>

                                                <td nowrap="nowrap" width="10%" style="border-bottom:none;padding:0px;text-align:right">' . $market['market'] . '</td>
                                                <td width="5%" nowrap="nowrap" style="border-bottom:none;padding:0px;"> : </td>

                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot :' . $market['spot_price'] . '</td>
                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot FCT :' . $market['spot_fct'] . '</td>
                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot Rate :' . $market['spot_rate'] . '</td>

                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner :' . $market['banner_price'] . '</td>
                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner FCT :' . $market['banner_fct'] . '</td>
                                                <td width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner Rate :' . $market['banner_rate'] . '</td>

                                              </tr>';
                                    }
                                    $i++;
                                }
                                ?>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>

            <tr>
                <td width="40%">Gross Amount</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['gross'] ?>
                </td>
            </tr>
            <tr>
                <td width="40%" nowrap="nowrap">Agency Commission Payable</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['agency_com'] ?>
                </td>
            </tr>
            <tr>
                <td width="40%">Net After Agency Commission</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['net_agency_com'] ?>
                </td>
            </tr>
            <tr>
                <td width="40%">Special Instructions</td>
                <td width="10%"> :</td>
                <td width="50%">
                    <?php echo $ro_details[0]['spcl_inst'] ?>
                </td>
            </tr>

            <tr>
                <td width="30%">Attach RO<span style="color:#F00;"> *</span></td>
                <td width="5%"> :</td>
                <td width="25%">
                    <?php if ($file_path == '') {
                        echo 'No Attachment Found';
                    } else {
                        $file_paths = explode(",", $file_path);
                        $fileCount = 1;
                        foreach ($file_paths as $path) { ?>
                            <a href="<?php echo $path ?>" target="_blank">File <?php echo $fileCount ?> </a> <br/>
                            <?php $fileCount++;
                        }
                    } ?>
                </td>
                <td width="40%">&nbsp;</td>
            </tr>

            <tr>
                <td width="30%">Client Approval Mail Attachment<span style="color:#F00;"> *</span></td>
                <td width="5%"> :</td>
                <td width="25%">
                    <?php if ($ro_details[0]['client_approval_mail'] == '') {
                        echo 'No Attachment Found';
                    } else { ?>
                        <a href="<?php echo $ro_details[0]['client_approval_mail']; ?>" target="_blank">Download
                            Approval Mail</a><br/>
                    <?php } ?>
                </td>
                <td width="40%">&nbsp;</td>
            </tr>

        </table>
    </div>
    <div class="bendl"></div>
    <div class="bendr"></div>
</div>

<?php if ($cancel_type == 'submit_ro_approval') { ?>
    <div class="block">
        <div class="block_head">
            <div class="bheadl"></div>
            <div class="bheadr"></div>
            <h2>Review Discounts</h2>
        </div>

        <div class="block_content" style="height: auto">

            <table style="width: 100%">
                <!--Monthly Discounts-->
                <tr>
                    <th style="width:40%">Markets</th>
                    <th style="width:30%">Spot Discount(%)</th>
                    <th style="width:30%">Banner Discount(%)</th>
                </tr>
                <?php foreach ($markets as $mkt) { ?>
                    <tr>
                        <td><span><?php echo $mkt['market'] ?></span></td>
                        <td><span><?php echo $mkt['spot_discount'] ?></span></td>
                        <td><span><?php echo $mkt['banner_discount'] ?></span></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td width="40%" nowrap="nowrap">Net Contribution Percent</td>
                    <td width="10%"> :</td>
                    <td width="50%">
                        <?php echo $net_contribution_percent ?>
                    </td>

                </tr>
            </table>

        </div>        <!-- .block_content ends -->

        <div class="bendl"></div>
        <div class="bendr"></div>

    </div>        <!-- .login ends -->
<?php } ?>


<?php include_once dirname(__FILE__) . "/inc/footer.inc.php" ?>
