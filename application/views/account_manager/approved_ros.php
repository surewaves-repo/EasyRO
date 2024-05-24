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
                <?php if ($logged_in_user['profile_id'] != 6) { ?>
                    <li><a href="<?php echo ROOT_FOLDER ?>/account_manager/home">Home</a></li>
                    <?php if ($logged_in_user['profile_id'] != 7) { ?>
                        <li class="active"><a href="<?php echo ROOT_FOLDER ?>/ro_manager/approved_ros">Approved RO's</a>
                        </li>
                    <?php } ?>
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
                                <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/update_network_payment">Update Network
                                        Payment</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li><a href="<?php echo ROOT_FOLDER ?>/account_manager/home">Home</a></li>
                    <li class="active"><a href="<?php echo ROOT_FOLDER ?>/ro_manager/approved_ros">Approved RO's</a>
                    </li>
                    <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user_details">User Details</a></li>
                <?php } ?>
            </ul>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a
                        href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>        <!-- #header ends -->


        <div class="block">

            <form method="post" action="<?php echo ROOT_FOLDER ?>/ro_manager/search_content">
                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2> Approved Release Orders</h2>

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
                <?php if ($value == 0) { ?>
                    <h2 style="text-align:center;">Schedule is not prepared due to lack of Inventory</h2>
                <?php } else { ?>
                <table cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <th>Customer RO Number</th>
                        <th>Agency Name</th>
                        <th>Advertiser Name</th>
                        <th>Brand Name</th>
                        <th>RO Start Date</th>
                        <th>RO End Date</th>
                        <th>RO Status</th>
                        <td>&nbsp;</td>
                    </tr>

                    <?php foreach ($ro_list as $content) { ?>
                        <tr>
                            <!--<td> <?php echo $content['internal_ro_number'] ?><br/> <?php if (isset($content['client_name'])) {
                                echo '(Client:' . $content['client_name'] . " ,Brand:" . $content['brand_new'] . " ,Product:" . $content['product_new'] . " )";
                            } ?> </td>-->
                            <td> <?php echo $content['cust_ro'] ?>
                                <br/> <?php echo '(Internal RO Number:' . $content['internal_ro'] . ")"; ?> </td>
                            <td><?php echo $content['agency'] ?> &nbsp;</td>
                            <td><?php echo $content['client'] ?> &nbsp;</td>
                            <td><?php echo $content['brand_name'] ?></td>
                            <td><?php print date('d-M-Y', strtotime($content['camp_start_date'])); ?> </td>
                            <td><?php print date('d-M-Y', strtotime($content['camp_end_date'])); ?> </td>
                            <?php if ($content['ro_approval_status'] == 0) { ?>
                                <td>In Process</td>
                            <?php } elseif ($content['ro_approval_status'] == 1) { ?>
                                <td>Approved</td>
                            <?php } ?>
                            <!--<td class="delete" ><a href="javascript:order_details('<?php echo rtrim(base64_encode($content['internal_ro']), '=') ?>')">view Details</a></td>			-->
                            <td>
                                <a href="javascript:approve('<?php echo rtrim(base64_encode($content['internal_ro']), '=') ?>','<?php echo $content['ro_approval_status'] ?>','<?php echo $content['id'] ?>')"><img
                                            src="<?php echo ROOT_FOLDER ?>/images/add-one.png"/></a></td>

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
            <?php } ?>
            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>        <!-- .block ends -->


    </div>                        <!-- wrapper ends -->

</div>        <!-- #hld ends -->


<script language="javascript">

    function add_new_ro() {

        $.colorbox({
            href: '<?php echo ROOT_FOLDER ?>/ro_manager/add_ro',
            iframe: true,
            width: '530px',
            height: '820px'
        });
    }

    function change_user_password(id) {
        $.colorbox({
            href: '<?php echo ROOT_FOLDER ?>/ro_manager/change_user_password/' + id,
            iframe: true,
            width: '530px',
            height: '280px'
        });
    }

    function order_details(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/order_details/" + order_id;
    }

    function show_channels(order_id) {

        $.colorbox({
            href: "<?php echo ROOT_FOLDER ?>/ro_manager/show_channels/" + order_id,
            iframe: true,
            width: '515px',
            height: '374px'
        });
    }

    function approval_request(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approval_request/" + order_id;
    }

    function approve(order_id, edit, id) {
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approve/" + order_id + "/" + edit + "/" + id;
    }

    function add_caption_tape_id(id) {
        $.colorbox({
            href: "<?php echo ROOT_FOLDER ?>/agency/add_caption_tape_id/" + id,
            iframe: true,
            width: '550px',
            height: '540px'
        });
    }

    $(document).ready(function () {
        <?php
        $selected_user_id = $this->session->userdata('selected_user_id');
        if ( isset($selected_user_id) && !empty($selected_user_id) ) { ?>
        change_user_password('<?php echo $selected_user_id ?>');
        <?
        $this->session->unset_userdata("selected_user_id");
        } ?>
    });

    function add_ro_amount(external_ro) {
        $.colorbox({
            href: '<?php echo ROOT_FOLDER ?>/ro_manager/add_ro_amount/' + external_ro,
            iframe: true,
            width: '540px',
            height: '250px'
        });
    }
</script>
