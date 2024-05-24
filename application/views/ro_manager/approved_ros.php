<!DOCTYPE HTML>
<html lang="en">
<head>

    <title>SureWaves Easy RO</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/surewaves_easy_ro/css/bootstrapstyle.css">
    <link rel="stylesheet" href="/surewaves_easy_ro/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Lexend+Deca&display=swap" rsel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Catamaran&display=swap" rel="stylesheet">
    <script src="/surewaves_easy_ro/js/popperscript.js"></script>
    <script src="/surewaves_easy_ro/js/jquery3_4_1.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/surewaves_easy_ro/css/bootstrap-multiselect.css">
    <script src="/surewaves_easy_ro/js/bootstrap-multiselect.js"></script>
    <script src="/surewaves_easy_ro/js/bootstrapscript.js"></script>
    <script src="/surewaves_easy_ro/js/approved_ros_Script.js"></script>


</head>
<body>
<div class="loader_overlay" id="loader_background" style="display:none;"></div>
<div class="page_loader_mini" id="loader_spin" style="display:none;" ></div>

<!--------------------------------------------Modal-------------------------------------------------------->
<div class="modal fade"  id="myModal">
    <div class="modal-dialog modal-xl modal-lg" >
        <div class="modal-content"  >

            <!-- Modal Header -->
            <div class="modal-header" id="home_modal_header">
                <h5 id="modal_title" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="Modal_body">

            </div>
        </div>
    </div>
</div>

<div class="modal fade"  id="optionModal">
    <div class="modal-dialog modal-xl modal-lg" >
        <div class="modal-content"  >

            <!-- Modal Header -->
            <div class="modal-header" id="home_modal_header">
                <h5 id="option_modal_title" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="Option_Modal_body">

            </div>
        </div>
    </div>
</div>

<div class="modal fade"  id="confirmModal">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content"  >

            <!-- Modal Header -->
            <div class="modal-header" id="home_modal_header">
                <h5 id="view_modal_title" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="view_Modal_body">

            </div>
        </div>
    </div>
</div>
<div class="modal fade"  id="dateModal">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content"  >

            <!-- Modal Header -->
            <div class="modal-header" id="home_modal_header">
                <h5 id="date_modal_title" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="date_Modal_body">

            </div>
        </div>
    </div>
</div>

<!---------------------------------hld div----------------------------------->
<div id="hld">
    <!----------------------------------------wrapper div--------------------------->
    <div class="wrapper">

        <!---------------------------------------------header div---------------------------->
        <div id="header">

            <div class="hdrl"></div>
            <div class="hdrr"></div>


            <h1 style="margin-right:10px">
                <img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/>
            </h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

             <!----------------------------Menu----------------------------->
            <?php echo $menu ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a
                        href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>    <!-- #header ends -->


        <div class="block">

            <form method="post" action="<?php echo ROOT_FOLDER ?>/ro_manager/search_content">
                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2> Approved Release Orders</h2>

                    <ul  style="float:left">
                        <li><span id="show_fct">FCT</span></li>
                        <li class=" nobg active"><span id="show_non_fct">NON FCT</span></li>
                    </ul>

                    <ul style="float:right;padding-left:10px;">
                        <li><input type="text" id="SearchTF" class="text" style="width: 300px" placeholder="Enter text to search ..."
                                                           value="<?php if (isset($search_str) && !empty($search_str)) {
                                                               echo $search_str;
                                                           } ?>" name="search_str"/>
                        </li>
                        <li><input type="submit" class="submit" value="search"/></li>


                    </ul>
                </div>
            </form>


            <div class="block_content">
                <?php if ($value == 0) { ?>
                    <h2 style="text-align:center;"><!--Schedule is not prepared due to lack of Inventory--></h2>
                <?php } else { ?>
                <table cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <th>Customer RO Number</th>
                        <th>Submitted By</th>
                        <th>Approved By</th>
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
                            <td> <?php echo $content['cust_ro'] ?>
                                <br/> <?php echo '(Internal RO Number:' . $content['internal_ro'] . ")"; ?>
                            </td>
                            <td><?php echo $content['submitted_by'] ?> &nbsp;</td>
                            <td><?php echo $content['approved_by'] ?> &nbsp;</td>
                            <td><?php echo $content['agency'] ?> &nbsp;</td>
                            <td><?php echo $content['client'] ?> &nbsp;</td>
                            <td><?php echo $content['brand_name'] ?></td>
                            <td><?php print date('d-M-Y', strtotime($content['camp_start_date'])); ?> </td>
                            <td><?php print date('d-M-Y', strtotime($content['camp_end_date'])); ?> </td>
                            <td><?php echo $content['ro_status_entry']; ?></td>
                            <td >
                                <a href="javascript:approve('<?php echo $content['id'] ?>')"><img
                                            src="<?php echo ROOT_FOLDER ?>/images/add-one.png"/></a>
                            </td>


                        </tr>
                    <?php } ?>
                </table>

                <div class="paggination right">
                    <?php echo $page_links ?>
                </div>    <!-- .paggination ends -->


            </div>    <!-- .block_content ends -->
            <?php } ?>
            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>    <!-- .block ends -->


    </div>                <!-- wrapper ends -->

</div>    <!-- #hld ends -->


</body>
</html>