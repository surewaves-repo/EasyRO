<!DOCTYPE HTML>
<html lang="en">
<head>

    <title>SureWaves Easy RO</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/surewaves_easy_ro/css/bootstrapstyle.css">
    <link rel="stylesheet" href="/surewaves_easy_ro/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Lexend+Deca&display=swap" rel="stylesheet">
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
    <script src="/surewaves_easy_ro/js/non_fct_homeScript.js"></script>

</head>
<body>
<!--------------------------------------Modal------------------------------------------------->
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
</div> <!--End Of Modal-->



<div class="loader_overlay" id="loader_background" style="display:none;"></div>
<div class="page_loader_mini" id="loader_spin" style="display:none;" ></div>

<!----------------------------------------------#hld div------------------------------------------------>
<div id="hld">

    <!-------------------------------------------.wrapper div--------------------------------------->
    <div class="wrapper">


         <!------------------------------------#header div------------------------------------------>
        <div id="header" data="<?php echo $logged_in_user['is_test_user'] ?>">
            <div class="hdrl"></div>
            <div class="hdrr"></div>


            <h1 style="margin-right:10px">
                <img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/>
            </h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>


            <?php echo $menu ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->

        <!----------------------------------------.block div------------------------------------->
        <div class="block">

            <form method="post" action="<?php echo ROOT_FOLDER ?>/non_fct_ro/search_content">

                <!----------------------------.block_head div----------------------------------->
                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2>Unapproved ROs</h2>

                    <ul  style="float:left">
                        <li><span id="show_fct">FCT</span></li>
                        <li class="nobg active"><span id="show_non_fct">NON FCT</span></li>
                    </ul>

                    <!-------------------------------------------Submit External Ro button------------------------------>
                    <ul style="float:left;padding-left:10px; width:190px;">
                        <li>

                            <?php
                            //Checking null region
                            if(($logged_in_user['profile_id'] == 6 ||
                                    $logged_in_user['profile_id'] == 11 ||
                                    $logged_in_user['profile_id'] == 12) && $userRegion != 9 )
                            {?>
                                <input type="button" id="submit_external_ro_btn" class="submit_modified"  value="Submit External RO" />

                            <?php } ?>
                        </li>
                    </ul>

                    <!--------------------------------------------Submit Non fct ro button------------------------------->
                    <ul style="float:left;padding-left:10px; width:190px;">
                        <li>
                            <?php
                            if(($logged_in_user['profile_id'] == 6 ||
                                    $logged_in_user['profile_id'] == 11 ||
                                    $logged_in_user['profile_id'] == 12) &&
                                $userRegion != 9 )
                            {?>

                                <input type="button" id="submit_non_fct_ro_btn" class="submit_modified"  value="Submit Non FCT RO" />

                            <?php } ?>
                        </li>
                    </ul>

                    <!-------------------------------------------Link Ro button------------------------------------------>
                    <?php
                    if($logged_in_user['is_test_user'] == 2)
                    { ?>
                        <ul style="float:left;padding-left:10px; width:190px;">
                            <li>
                                <?php
                                if($logged_in_user['profile_id'] == 1 || $logged_in_user['profile_id'] == 2 ||
                                    $logged_in_user['profile_id'] == 6 || $logged_in_user['profile_id'] == 11 ||
                                    $logged_in_user['profile_id'] == 12)
                                {?>
                                    <input type="button" id="link_ro_btn" class="submit_modified" data-backdrop="static" data-keyboard="false" value="Link Ro" />

                                <?php } ?>
                            </li>
                        </ul>
                    <?php } ?>


                    <ul style="float:none;padding-left:10px">
                        <li >
                            <input type="text" id="SearchTF" class="text" style="width: 300px" placeholder="Enter text to search ..."
                                   value="<?php if ( isset($search_str) && !empty($search_str) )
                                   {
                                       echo $search_str;
                                   } ?>"
                                   name="search_str" /></li>
                        <li ><input type="submit" class="submit" value="search"/></li>

                    </ul>
                </div>		<!-- .block_head ends -->
            </form>

            <!----------------------------------.block-content div---------------------------->
            <div class="block_content">

                <table cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <th>Customer RO Number</th>
                        <th>Submitted By</th>
                        <th>Approved By</th>
                        <th>Agency Name</th>
                        <th>Advertiser Name</th>
                        <th>Financial Year</th>
                        <th>RO Status</th>

                    </tr>

                    <?php foreach ( $ro_list as $content) { ?>
                        <tr>
                            <td> <a href="javascript:non_fct_ro_approval('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')"><?php echo $content['customer_ro_number']?><br/> <?php echo '(Internal RO Number:'.$content['internal_ro_number'].")" ; ?></a> </td>
                            <td><?php echo $content['account_manager_name'] ?> &nbsp;</td>
                            <td><?php echo "Not Approved" ?> &nbsp;</td>
                            <td><?php echo $content['agency'] ?> &nbsp;</td>
                            <td><?php echo $content['client'] ?> &nbsp;</td>
                            <td><?php echo $content['financial_year']; ?> </td>
                            <td>Approval Requested</td>
                        </tr>
                    <?php } ?>
                </table>

                <div class="paggination right">
                    <?php echo $page_links ?>
                </div>		<!-- .paggination ends -->
            </div>		<!-- .block_content ends -->

            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>		<!-- .block ends -->

    </div>						<!-- wrapper ends -->

</div>		<!-- #hld ends -->


<script language="javascript">
    function create_ext_ro() {
        if($("#user_type").val() != 2) {
            $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/create_ext_ro/',iframe:true, width: '940px', height:'650px'});
        }else{
            $.colorbox({href:'<?php echo ROOT_FOLDER ?>/advanced_ro_manager/create_advanced_ext_ro/',iframe:true, width: '940px', height:'650px'});
        }

    }
    function create_non_fct_ext_ro(){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/create_non_fct_ext_ro/',iframe:true, width: '940px', height:'650px'});
    }
    function link_ro() {
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/advanced_ro_manager/link_advanced_ro/',iframe:true, width: '747px', height:'570px'});
    }
    function show_details(order_id,edit,id){
        //window.location.href = "<?php echo ROOT_FOLDER ?>/account_manager/show_details/" + order_id + "/" + edit ;
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approve/" + order_id + "/" + edit + "/" + id ;
    }
    function change_user_password(id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/change_user_password/' + id,iframe:true, width: '530px', height:'280px'});
    }
    function non_fct_ro_approval(order_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/non_fct_ro/approve_non_fct/" + order_id ;
    }
    function show_fct_ros(){
        window.location.href = "<?php echo ROOT_FOLDER ?>/account_manager/home";
    }
    function show_non_fct_ros(){
        window.location.href = "<?php echo ROOT_FOLDER ?>/non_fct_ro/home";
    }


</script>
