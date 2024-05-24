<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
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
                <li class="active"><a href="<?php echo ROOT_FOLDER ?>/account_manager/home">Home</a>
                    <?php if ($logged_in_user['profile_id'] != 6) { ?>
                        <ul id="nav">
                            <li class="active"><a href="<?php echo ROOT_FOLDER ?>/ro_manager/home">Old Home</a></li>
                        </ul>
                    <?php } ?>
                </li>
                <?php if ($logged_in_user['profile_id'] != 6) { ?>


                    <li><a href="<?php echo ROOT_FOLDER ?>/account_manager/approved_ros">Approved RO's</a></li>
                    <li><a href="<?php echo ROOT_FOLDER ?>/account_manager/user_details">User Details</a></li>
                    <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_ro_report">Reports</a>
                        <ul id="nav">
                            <li class="active"><a href="<?php echo ROOT_FOLDER ?>/ro_manager/external_ro_report">External
                                    RO Report</a></li>
                            <li class="active"><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_ro_report">Network
                                    RO Report</a></li>
                            <li class="active"><a href="<?php echo ROOT_FOLDER ?>/account_manager/am_invoice_report">Invoice
                                    Report</a></li>
                            <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_remittance_report">N/w
                                    Remittance</a></li>
                        </ul>
                    </li>
                <?php } ?>
                <!--<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/ro_report">RO Report</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/audit">Audit Trail</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user">User</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/preferences">Preferences	</a></li>-->
            </ul>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a
                        href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>        <!-- #header ends -->


        <div class="block">

            <form method="post" action="<?php echo ROOT_FOLDER ?>/account_manager/search_content">
                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2>Customer RO Number</h2>
                    <ul style="float:left;padding-left:10px; width:200px;">
                        <li>

                        </li>
                    </ul>
                    <ul style="float:right;padding-left:10px;">
                        <li><label> </label> &nbsp; <input type="text" id="SearchTF" class="text"
                                                           placeholder="Enter text to search ..."
                                                           value="<?php if (isset($search_str) && !empty($search_str)) {
                                                               echo $search_str;
                                                           } ?>" name="search_str"/></li>
                        <li><input type="submit" class="submit" value="search"/></li>

                        <ul>
                            <ul>

                            </ul>
                </div>        <!-- .block_head ends -->
            </form>


            <div class="block_content">

                <table cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <th>&nbsp;</th>
                        <th>Customer RO Number</th>
                        <th>Agency Name</th>
                        <th>Advertiser Name</th>
                        <th>Brand Name</th>
                        <th>RO Start Date</th>
                        <th>RO End Date</th>
                        <th>RO Status</th>
                        <th>&nbsp;</th>
                        <!--<td>&nbsp;</td>-->
                    </tr>

                    <?php foreach ($ro_list as $content) { ?>
                        <tr>
                            <!--<td> <?php echo $content['internal_ro_number'] ?><br/> <?php if (isset($content['client_name'])) {
                                echo '(Client:' . $content['client_name'] . " ,Brand:" . $content['brand_new'] . " ,Product:" . $content['product_new'] . " )";
                            } ?> </td>-->
                            <td>
                                <?php if ($edit != 1 && ($logged_in_user['profile_id'] == 1) || $logged_in_user['profile_id'] == 2) { ?>
                                <?php if ($is_cancelled == 'true') {
                                    echo '&nbsp;';
                                } else { ?>
                                    <a href="javascript:edit_ext_ro('<?php echo $content['id'] ?>')">
                                        <img src="<?php echo ROOT_FOLDER ?>/images/add-one.png"/>
                                    </a>
                                <?php } ?>
                            </td>
                            <?php } ?>
                            <td>

                                <a href="javascript:show_details('<?php echo rtrim(base64_encode($content['internal_ro']), '=') ?>')"><?php echo $content['cust_ro'] ?>
                                    <br/> <?php echo '(Internal RO Number:' . $content['internal_ro'] . ")"; ?></a></td>
                            <td><?php echo $content['agency'] ?> &nbsp;</td>
                            <td><?php echo $content['client'] ?> &nbsp;</td>
                            <td><?php echo $content['brand_name'] ?></td>
                            <td><?php print date('d-M-Y', strtotime($content['camp_start_date'])); ?> </td>
                            <td><?php print date('d-M-Y', strtotime($content['camp_end_date'])); ?> </td>
                            <?php if ($edit != 1) {
                                if ($is_cancelled == 'true') {
                                    ?>
                                    <td>Cancelled</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>In Process</td>
                                <?php }
                            } else {
                                if ($is_cancelled == 'true') {
                                    ?>
                                    <td>Cancelled</td>
                                <?php } else { ?>
                                    <td>Approved</td>
                                <?php }
                            } ?>
                            <td>
                                <?php if ($is_cancelled == 'true') {
                                    echo '&nbsp;';
                                } else { ?>
                                    <a href="javascript:cancel_ro('<?php echo $content['id'] ?>','<?php echo rtrim(base64_encode($content['cust_ro']), '=') ?>')">
                                        <img src="<?php echo ROOT_FOLDER ?>/images/cancel.jpg" width="50" height="50"/>
                                    </a>
                                <?php } ?>
                            </td>

                            <td>
                                <a href="<?php echo ROOT_FOLDER ?>/account_manager/invoice_collection/<?php echo rtrim(base64_encode($content['cust_ro']), '=') ?>">Invoice</a>
                            </td>

                            <!--<td class="delete" ><a href="javascript:order_details('<?php echo rtrim(base64_encode($content['internal_ro_number']), '=') ?>')">view Details</a></td>			-->
                            <!--<td><a href="javascript:approve('<?php echo rtrim(base64_encode($content['internal_ro_number']), '=') ?>')"><img src="<?php echo ROOT_FOLDER ?>/images/add-one.png" /></a></td>-->

                            <!--									<td><a href="javascript:show_channels('<?php echo rtrim(base64_encode($content['internal_ro_number']), '=') ?>')">Show Channels</a></td>
									<td><a href="javascript:add_ro_amount('<?php echo rtrim(base64_encode($content['customer_ro_number']), '=') ?>')">Add CRO Amount</a></td>
									<td><a href="javascript:approval_request('<?php echo rtrim(base64_encode($content['internal_ro_number']), '=') ?>')">Approval Request</a></td>-->

                        </tr>
                    <?php } ?>
                </table>

                <div class="paggination right">
                    <?php echo $page_links ?>
                </div>        <!-- .paggination ends -->
            </div>        <!-- .block_content ends -->

            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>        <!-- .block ends -->

    </div>                        <!-- wrapper ends -->

</div>        <!-- #hld ends -->


<script language="javascript">
    function edit_ext_ro(id) {
        $.colorbox({
            href: '<?php echo ROOT_FOLDER ?>/account_manager/edit_ext_ro/' + id,
            iframe: true,
            width: '940px',
            height: '650px'
        });
    }

    function cancel_ro(id, cust_ro) {
        $.colorbox({
            href: '<?php echo ROOT_FOLDER ?>/account_manager/cancel_ro/' + id + '/' + cust_ro,
            iframe: true,
            width: '940px',
            height: '650px'
        });
    }

    function show_details(order_id) {
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approve/" + order_id;
    }
</script>
