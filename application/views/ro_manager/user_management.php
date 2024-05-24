<?php
/*
 * Author   : Nitish Hardeniya
 * Date     : 9 April 2015
*/
?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>SureWaves Easy RO</title>
    <link rel="icon" type="image/png" href="<?php echo ROOT_FOLDER ?>/images/SWV_Logo_Watermark.ico">

    <style type="text/css" media="all">
       /* Header */

        #header {
            height: 50px;
            line-height: 50px;
            background: url(<?php echo ROOT_FOLDER ?>/images/hdr.gif) 0 0 repeat-x;
            color: #999;
            font-weight: bold;
            margin-bottom: 20px;
            }
            
        #header .hdrl {
            width: 20px;
            height: 50px;
            float: left;
            background: url(<?php echo ROOT_FOLDER ?>/images/hdrl.gif) top left no-repeat;
            }

        #header .hdrr {
            width: 20px;
            height: 50px;
            float: right;
            background: url(<?php echo ROOT_FOLDER ?>/images/hdrr.gif) top right no-repeat;
            }
            
        #header a {
            color: #999;
            text-decoration: none;
            }

        #header a:hover { color: #fff; }
            
        #header h1 {
            float: left;
            margin-right: 80px;
            font-family: "Titillium800", "Trebuchet MS", Arial, sans-serif;
            font-size: 18px;
            font-weight: normal;
            text-transform: uppercase;
            color: #fff;
            }

        #header h1 a { color: #fff; }
        #header h1 a:hover { color: #008ee8; }
            

        /* Navigation */
        #header #nav, #header #nav * { z-index: 20; }
         
        #header #nav {
            margin: 0;
            border: 0 none;
            padding: 0;
            width: auto; /*For KHTML*/
            list-style: none;
            height: 50px;
            padding: 0;
            float: left;
            }

        #header #nav li {
            margin: 0;
            border: 0 none;
            padding: 0;
            float: left; /*For Gecko*/
            display: inline;
            list-style: none;
            position: relative;
            height: 50px;
            padding: 0 5px;
            background: url(<?php echo ROOT_FOLDER ?>/images/nsp.gif) center right no-repeat;
            line-height: 50px;
            }
            
        #header #nav li.nobg { background: none; }

        #header #nav ul {
            margin: 0;
            border: 0 none;
            padding: 0;
            width: 190px;
            list-style: none;
            display: none;
            position: absolute;
            top: 50px;
            left: 0;
            }

        #header #nav ul:after /*From IE 7 lack of compliance*/{
            clear: both;
            display: block;
            font: 1px/0px serif;
            content: ".";
            height: 0;
            visibility: hidden;
            }

        #header #nav ul li {
            width: 190px;
            float: left; /*For IE 7 lack of compliance*/
            display: block !important;
            display: inline; /*For IE*/
            position: relative;
            top: 0;
            height: 30px;
            line-height: 30px;
            padding: 0;
            background: none;
            }
                
        /* Root Menu */
        #header #nav a {
            float: none !important; /*For Opera*/
            float: left; /*For IE*/
            display: block;
            height: auto !important;
            height: 1%; /*For IE*/
            height: 30px;
            padding: 0 10px;
            }
            
        #header #nav li.active a { color: #ddd; }
        #header #nav li.active a:hover { color: #fff; }

        /* Root Menu Hover Persistence */
        #header #nav a:hover,
        #header #nav li:hover a,
        #header #nav li.iehover a {
            color: #fff;
            }

        /* 2nd Menu */
        #header #nav li:hover li a,
        #header #nav li.iehover li a {
            text-transform: none;
            padding: 0 15px;
            font-size: 11px;
            font-weight: bold;
            line-height: 30px;
            color: #999;
            background: url(<?php echo ROOT_FOLDER ?>/images/mbg.png) 0 0 repeat;
            }

        /* 2nd Menu Hover Persistence */
        #header #nav li:hover li a:hover,
        #header #nav li:hover li:hover a,
        #header #nav li.iehover li a:hover,
        #header #nav li.iehover li.iehover a {
            color: #fff;
            background: #000;
            }

        /* 3rd Menu */
        #header #nav li:hover li:hover li a,
        #header #nav li.iehover li.iehover li a {
            float: none;
            color: #999;
            background: url(<?php echo ROOT_FOLDER ?>/images/mbg.png) 0 0 repeat;
            }

        /* 3rd Menu Hover Persistence */
        #header #nav li:hover li:hover li a:hover,
        #header #nav li:hover li:hover li:hover a,
        #header #nav li.iehover li.iehover li a:hover,
        #header #nav li.iehover li.iehover li.iehover a {
            color: #fff;
            background: #000;
            }

        /* 4th Menu */
        #header #nav li:hover li:hover li:hover li a,
        #header #nav li.iehover li.iehover li.iehover li a {
            color: #999;
            background: url(<?php echo ROOT_FOLDER ?>/images/mbg.png) 0 0 repeat;
            }

        /* 4th Menu Hover */
        #header #nav li:hover li:hover li:hover li a:hover,
        #header #nav li.iehover li.iehover li.iehover li a:hover {
            color: #fff;
            background: #000;
            }

        #header #nav ul ul,
        #header #nav ul ul ul {
            display: none;
            position: absolute;
            top: 0;
            left: 190px;
            }

        /* Do Not Move - Must Come Before display:block for Gecko */
        #header #nav li:hover ul ul,
        #header #nav li:hover ul ul ul,
        #header #nav li.iehover ul ul,
        #header #nav li.iehover ul ul ul {
            display: none;
            }
            
        #header #nav li:hover ul,
        #header #nav ul li:hover ul,
        #header #nav ul ul li:hover ul,
        #header #nav li.iehover ul,
        #header #nav ul li.iehover ul,
        #header #nav ul ul li.iehover ul {
            display: block;
            }

        #header .user {
            float: right;
            font-size: 11px;
            }

        #header .user a { text-decoration: underline; }
        #header .user a:hover { text-decoration: none; }

    /* Validation class*/

    .error-class{
        color: #a94442;
        display: block;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 13px;
        padding-left: unset;
        padding-top: 5px;
    }

    /* Profile Image upload */
       .uploadProfileImage input{
           display: none;
       }
       .pure-button{
           margin: 5px 0;
       }
    </style>

    <link href="/surewaves_easy_ro/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/surewaves_easy_ro/includes/css/bootstrap-glyphicons.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link href="/surewaves_easy_ro/bootstrap/css/bootstrapValidator.min.css" rel="stylesheet">
    <link href="/surewaves_easy_ro/bootstrap/css/bootstrap-select.css" rel="stylesheet">

    <!-- Adding for cropping -->

    <link href="/surewaves_easy_ro/assets/css/cropper.min.css" rel="stylesheet">
    <link href="/surewaves_easy_ro/assets/css/cropper_main.css" rel="stylesheet">

</head>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="<?php echo ROOT_FOLDER ?>/bootstrap/js/bootstrapValidator.min.js"></script>
<script src="<?php echo ROOT_FOLDER ?>/bootstrap/js/bootstrap-select.js"></script>

<!-- Adding for cropping -->
<script src="<?php echo ROOT_FOLDER ?>/assets/js/cropper.min.js"></script>
<script src="<?php echo ROOT_FOLDER ?>/assets/js/cropper_main.js"></script>

<style type="text/css">
    .well {
        border-left-width: 5px;
        height: 100px;
        cursor: pointer;
    }
    .active{
        color: #d9534f;
    }
    .active > div {
        border: 1px solid #d9534f;
    }
	
	.nav-stacked > li {
		padding-left:0px;
	}
    .bh{
        width: 295px;
        float: left;
        margin-right: 45px;
        border-left-color: #ce4844;
    }
    .nh{
        width: 295px;
        float: left;
        margin-right: 45px;
        border-left-color: #428bca;
    }
    .rd{
        width: 300px;
        float: left;
        margin-right: 10px;
        border-left-color: #5bc0de;
    }
    .gh{
        width: 300px;
        float: left;
        margin-right: 10px;
        margin-left: 75px;
        border-left-color: #5cb85c;
        /*display: none;*/
    }
    .gm_to_nh{
        width: 300px;
        float: left;
        margin-right: 10px;
        margin-left: 75px;
        border-left-color: #5cb85c;
    }
    .am_to_nh{
        width: 300px;
        float: left;
        margin-right: 10px;
        margin-left: 31px;
        border-left-color: #f0ad4e;
    }
    .am{
        width: 300px;
        float: left;
        margin-right: 10px;
        border-left-color: #f0ad4e;
        margin-left: 31px;
        /*display: none;*/
    }

    .bs-callout {
        padding: 20px;
        margin: 20px 0;
        border: 1px solid #eee;
        border-left-width: 5px;
        border-radius: 3px;
    }
    .bs-callout h4 {
        margin-top: 0;
        margin-bottom: 5px;
    }
    .bs-callout p:last-child {
        margin-bottom: 0;
    }
    .bs-callout code {
        border-radius: 3px;
    }
    .bs-callout+.bs-callout {
        margin-top: -5px;
    }
    .bs-callout-danger {
        border-left-color: #d9534f;
    }
    .bs-callout-danger h4 {
        color: #d9534f;
    }
</style>

<div id="header">
    <div class="hdrl"></div>
    <div class="hdrr"></div>


    <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; /></h1>
    <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>


    <?php echo $menu; ?>

    <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
</div>      <!-- #header ends -->

<div>

    <div class="bs-callout bs-callout-danger">
        <h4>Easy RO User Management</h4>
    </div>

    <?php if($user_action == 1){ ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Successful!</strong> New User has been Created.
        </div>
    <?php } ?>

    <?php if($user_action == 2){ ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Deleted!</strong> User has been Deleted.
        </div>
    <?php } ?>

    <?php if($user_action == 3){ ?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Edited!</strong> User data has been updated.
        </div>
    <?php } ?>

    <?php if($user_action == 4){ ?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Successful!</strong> Targets updated for User.
        </div>
    <?php } ?>

    <?php if($user_action == 5){ ?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Successful!</strong> Discounts updated.
        </div>
    <?php } ?>

    <?php if($user_action == 6){ ?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Successful!</strong> Targets updated.
        </div>
    <?php } ?>

    <div style="margin-top:20px" class="row">

        <div class="col-sm-3" style="margin-left: 14px">
            <a href="" data-toggle="modal" class="btn btn-sm btn-default open_create_modal"><span class="glyphicon glyphicon-plus"></span> Create User</a>
        </div>

        <?php

        $imagePathBH = ROOT_FOLDER."/images/user.png" ;
        $imagePathNH = ROOT_FOLDER."/images/user.png" ;

        foreach($business_head as $bh) {

            if( !empty( $bh["profile_image"]) ){

                $imagePathBH = $bh["profile_image"] ;
            }

        ?>
            <div class="col-sm-3 well well-sm bh" id="bh-allindia" style="border-left-color: #ce4844;">
                <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $bh['user_id'] ?>" data-toggle="modal" >
                    <img id="<?php echo $bh['user_id'] ?>_IMG" src="<?php echo $imagePathBH; ?>" alt="HTML" class="img-circle" height="50px" width="50px">
                </a>
                <h4 class="media-heading"><?php echo $bh['user_name']?></h4>
                <p><span class="label label-danger"><?php echo $bh['designation']?></span> <span class="label label-danger">All India</span> </p>
            </div>
        <?php }

        if( !empty( $national_head["profile_image"]) ){

            $imagePathNH = $national_head["profile_image"] ;
        }

        ?>


        <div role="<?php echo $national_head['user_id']?>" class="col-sm-3 well well-sm nh" id="nh-allindia" style="border-left-color: #428bca;">
            <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $national_head['user_id'] ?>" data-toggle="modal" >
                <img id="<?php echo $national_head['user_id'] ?>_IMG" src="<?php echo $imagePathNH; ?>" alt="HTML" class="img-circle" height="50px" width="50px">
            </a>
            <h4 class="media-heading"><?php echo $national_head['user_name']?></h4>
            <p><span class="label label-primary"><?php echo $national_head['designation']?></span> <span class="label label-primary">All India</span> </p>
        </div>

    </div>

    <div style="margin-bottom:10px" class="row well well-sm">
        <div class="col-md-3"><h4>Regions</h4><a href="" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_reg_targets_modal"><span class="glyphicon glyphicon-screenshot"></span>Region Targets</a></div>
        <div class="col-md-3"><h4>Regional Directors</h4><a href="" data-id="11"  data-toggle="modal" class="btn btn-xs btn-default open_discounts_modal"><span class="glyphicon glyphicon-flag"></span> Discounts</a></div>
        <div class="col-md-3"><h4>Group Managers</h4><a href="" data-id="12"  data-toggle="modal" class="btn btn-xs btn-default open_discounts_modal"><span class="glyphicon glyphicon-flag"></span> Discounts</a></div>
        <div class="col-md-3"><h4>Account Managers</h4><a href="" data-id="6"  data-toggle="modal" class="btn btn-xs btn-default open_discounts_modal"><span class="glyphicon glyphicon-flag"></span> Discounts</a></div>
    </div>




    <!--New code starts here-->

    <div class="row">

        <div class="col-md-3 ">
            <!--for regions-->

            <ul class="nav nav-pills nav-stacked region-nav" >
                <li role="show_all_users" class="col-sm-2 show_all_users active"><a href="javascript:void(0)" style="width: 300px;">All</a></li>
                <?php foreach($regions as $reg){ ?>
                    <li role="<?php echo $reg['region_name']?>" class="col-sm-2"><a href="#" style="width: 300px;"><?php echo ucfirst($reg['region_name'])?></a></li>
                <?php } ?>
            </ul>

        </div>


        <div class="col-md-9">
            <!--for users-->

            <div class="row ">
                <div class="col-md-12">
                    <div class="row ">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <div class="row ">

                                <div class="col-md-6"></div>

                                <div class="col-md-6">
                                    <?php foreach($account_managers as $am) {
                                        if($am['reporting_manager_id'] == $national_head['user_id']) {

                                            $imagePath = ROOT_FOLDER."/images/user.png" ;

                                            if( !empty( $am["profile_image"]) ){

                                                $imagePath = $am["profile_image"] ;
                                            }
                                            ?>
                                            <div class="<?php echo $am['region_name'] ?> well well-sm am_to_nh">
                                                <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $am['user_id'] ?>" data-toggle="modal" >
                                                    <img id="<?php echo $am['user_id'] ?>_IMG" src="<?php echo $imagePath; ?>" alt="HTML" class="img-circle" height="50px" width="50px">
                                                </a>
                                                <h4 class="media-heading"><?php echo $am['user_name']?></h4>
                                                <p><span class="label label-warning"><?php echo $am['designation']?></span> <span class="label label-warning"><?php echo ucfirst($am['region_name'])?></span> </p>
                                                <p>
                                                    <a href="" data-id="<?php echo $am['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_edit_modal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                                    <a href="#delete_user" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_delete_modal"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                                <?php if($am['region_id'] != 9){?>
                                                    <a href="" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_targets_modal"><span class="glyphicon glyphicon-record"></span> Targets</a>
                                                <?php } ?>
                                                </p>
                                            </div>

                                        <?php } } ?> <!--account manager to nh-->
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>


            <div class="row ">
                <div class="col-md-12">
                    <div class="row ">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <?php foreach($group_managers as $gm) {
                                if($gm['reporting_manager_id'] == $national_head['user_id']) {

                                    $imagePath = ROOT_FOLDER."/images/user.png" ;

                                    if( !empty( $gm["profile_image"]) ){

                                        $imagePath = $gm["profile_image"] ;
                                    }
                                    ?>
                                    <div class="row ">
                                        <div class="col-md-6 <?php echo $reg['region_name'] ?>">
                                            <div class="<?php echo $gm['region_name'] ?> well well-sm gm_to_nh">
                                                <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $gm['user_id'] ?>" data-toggle="modal" >
                                                    <img id="<?php echo $gm['user_id'] ?>_IMG" src="<?php echo $imagePath ;?>" alt="HTML" class="img-circle" height="50px" width="50px">
                                                </a>
                                                <h4 class="media-heading"><?php echo $gm['user_name']?></h4>
                                                <p><span class="label label-success"><?php echo $gm['designation']?></span> <span class="label label-success"><?php echo ucfirst($gm['region_name'])?></span> </p>
                                                <p>
                                                    <a href="" data-id="<?php echo $gm['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_edit_modal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                                    <a href="#delete_user" data-id="<?php echo $gm['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_delete_modal"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                                <?php if($gm['region_id'] != 9){?>
                                                    <a href="" data-id="<?php echo $gm['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_targets_modal"><span class="glyphicon glyphicon-record"></span> Targets</a>
                                                <?php } ?>
                                            </p>
                                            </div>
                                        </div>

                                        <!--Account Manager-->
                                        <div class="col-md-6 ">
                                            <?php foreach($account_managers as $am) {
                                                if($am['reporting_manager_id'] == $gm['user_id']) {

                                                    $imagePath = ROOT_FOLDER."/images/user.png" ;

                                                    if( !empty( $am["profile_image"]) ){

                                                        $imagePath = $am["profile_image"] ;
                                                    }
                                                    ?>
                                                    <div class="<?php echo $am['region_name'] ?> well well-sm am_to_nh ">
                                                        <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $am['user_id'] ?>" data-toggle="modal" >
                                                            <img id="<?php echo $am['user_id'] ?>_IMG" src="<?php echo $imagePath; ?>" alt="HTML" class="img-circle" height="50px" width="50px">
                                                        </a>
                                                        <h4 class="media-heading"><?php echo $am['user_name']?></h4>
                                                        <p><span class="label label-warning"><?php echo $am['designation']?></span> <span class="label label-warning"><?php echo ucfirst($am['region_name'])?></span> </p>
                                                        <p>
                                                            <a href="" data-id="<?php echo $am['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_edit_modal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                                            <a href="#delete_user" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_delete_modal"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                                    <?php if($am['region_id'] != 9){?>
                                                            <a href="" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_targets_modal"><span class="glyphicon glyphicon-record"></span> Targets</a>
                                                    <?php } ?>
                                                        </p>
                                                    </div>

                                                <?php } } ?> <!--account manager-->
                                        </div>
                                    </div>
                                <?php } } ?> <!--group manager to nh-->

                        </div>
                    </div>
                </div>


            </div>


        <?php foreach($regions as $reg){ 
				if($reg['rd_exists'] == 1){	
		?>

            <div class="row">

                <!--Regional Director-->
                <div class="col-md-12">
                    <div class="row ">
                        <div class="col-md-3 <?php echo $reg['region_name'] ?>">
                            <?php foreach($regional_directors as $rd) {
                            if($rd['region_id'] == $reg['id']) {

                            $imagePath = ROOT_FOLDER."/images/user.png" ;

                            if( !empty( $rd["profile_image"]) ){

                                $imagePath = $rd["profile_image"] ;
                            }
                            ?>
                            <div class="<?php echo $rd['region_name']?> well well-sm rd">
                                <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $rd['user_id'] ?>" data-toggle="modal" >
                                    <img id="<?php echo $rd['user_id'] ?>_IMG" src="<?php echo $imagePath; ?>" alt="HTML" class="img-circle" height="50px" width="50px">
                                </a>
                                <h4 class="media-heading"><?php echo $rd['user_name']?></h4>
                                <p><span class="label label-info"><?php echo $rd['designation']?></span> <span class="label label-info"><?php echo ucfirst($rd['region_name'])?></span> </p>
                                <p>
                                    <a href="" data-id="<?php echo $rd['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_edit_modal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                    <a href="#delete_user" data-id="<?php echo $rd['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_delete_modal"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                <?php if($rd['region_id'] != 9){?>
                                    <a href="" data-id="<?php echo $rd['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_targets_modal"><span class="glyphicon glyphicon-record"></span> Targets</a>
                                <?php } ?>
                                </p>
                            </div>

                        </div>

                        <div class="col-md-9">

                            <!--Account Manager reporting RD-->

                            <?php foreach($account_managers as $am) {
                                if($am['region_id'] == $reg['id']) {
                                    if($am['reporting_manager_id'] == $rd['user_id']) {

                                        $imagePath = ROOT_FOLDER."/images/user.png" ;

                                        if( !empty( $am["profile_image"]) ){

                                            $imagePath = $am["profile_image"] ;
                                        }
                                        ?>
                                        <div class="row ">
                                            <div class="col-md-6 <?php echo $reg['region_name'] ?>">
                                                <!--blank for no GM-->
                                            </div>

                                            <div class="col-md-6 <?php echo $reg['region_name'] ?>">
                                                <div class="<?php echo $am['region_name'] ?> well well-sm am">
                                                    <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $am['user_id'] ?>" data-toggle="modal" >
                                                        <img id="<?php echo $am['user_id'] ?>_IMG" src="<?php echo $imagePath; ?>" alt="HTML" class="img-circle" height="50px" width="50px">
                                                    </a>
                                                    <h4 class="media-heading"><?php echo $am['user_name']?></h4>
                                                    <p><span class="label label-warning"><?php echo $am['designation']?></span> <span class="label label-warning"><?php echo ucfirst($am['region_name'])?></span> </p>
                                                    <p>
                                                        <a href="" data-id="<?php echo $am['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_edit_modal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                                        <a href="#delete_user" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_delete_modal"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                                <?php if($am['region_id'] != 9){?>
                                                        <a href="" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_targets_modal"><span class="glyphicon glyphicon-record"></span> Targets</a>
                                                <?php } ?>
                                                    </p>
                                                </div>
                                            </div>

                                        </div>

                                    <?php } } } ?> <!--account manager-->


                            <!--Group Manager-->

                            <?php foreach($group_managers as $gm) {
                                if($gm['region_id'] == $reg['id']) {
                                    if($gm['reporting_manager_id'] == $rd['user_id']) {

                                        $imagePath = ROOT_FOLDER."/images/user.png" ;

                                        if( !empty( $gm["profile_image"]) ){

                                            $imagePath = $gm["profile_image"] ;
                                        }
                                        ?>
                                        <div class="row ">
                                            <div class="col-md-6 <?php echo $reg['region_name'] ?>">
                                                <div class="<?php echo $gm['region_name'] ?> well well-sm gh">
                                                    <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $gm['user_id'] ?>" data-toggle="modal" >
                                                        <img id="<?php echo $gm['user_id'] ?>_IMG" src="<?php echo $imagePath; ?>" alt="HTML" class="img-circle" height="50px" width="50px">
                                                    </a>
                                                    <h4 class="media-heading"><?php echo $gm['user_name']?></h4>
                                                    <p><span class="label label-success"><?php echo $gm['designation']?></span> <span class="label label-success"><?php echo ucfirst($gm['region_name'])?></span> </p>
                                                    <p>
                                                        <a href="" data-id="<?php echo $gm['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_edit_modal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                                        <a href="#delete_user" data-id="<?php echo $gm['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_delete_modal"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                                <?php if($gm['region_id'] != 9){?>
                                                        <a href="" data-id="<?php echo $gm['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_targets_modal"><span class="glyphicon glyphicon-record"></span> Targets</a>
                                                <?php } ?>
                                                    </p>
                                                </div>

                                            </div>

                                            <!--Account Manager-->
                                            <div class="col-md-6 <?php echo $reg['region_name'] ?>">
                                                <?php foreach($account_managers as $am) {
                                                    if($am['region_id'] == $reg['id']) {
                                                        if($am['reporting_manager_id'] == $gm['user_id']) {

                                                            $imagePath = ROOT_FOLDER."/images/user.png" ;

                                                            if( !empty( $am["profile_image"]) ){

                                                                $imagePath = $am["profile_image"] ;
                                                            }

                                                            ?>
                                                            <div class="<?php echo $am['region_name'] ?> well well-sm am">
                                                                <a class="thumbnail pull-left open_profile_image_modal" href="#avatar-modal" data-id="<?php echo $am['user_id'] ?>" data-toggle="modal" >
                                                                    <img id="<?php echo $am['user_id'] ?>_IMG" src="<?php echo $imagePath; ?>" alt="HTML" class="img-circle" height="50px" width="50px">
                                                                </a>
                                                                <h4 class="media-heading"><?php echo $am['user_name']?></h4>
                                                                <p><span class="label label-warning"><?php echo $am['designation']?></span> <span class="label label-warning"><?php echo ucfirst($am['region_name'])?></span> </p>
                                                                <p>
                                                                    <a href="" data-id="<?php echo $am['user_id'] ?>" data-toggle="modal" class="btn btn-xs btn-default open_edit_modal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                                                    <a href="#delete_user" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_delete_modal"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                                                <?php if($am['region_id'] != 9){?>
                                                                    <a href="" data-id="<?php echo $am['user_id'] ?>"  data-toggle="modal" class="btn btn-xs btn-default open_targets_modal"><span class="glyphicon glyphicon-record"></span> Targets</a>
                                                                <?php } ?>
                                                                </p>
                                                            </div>

                                                        <?php } } } ?> <!--account manager-->
                                            </div>
                                        </div>  <!--GH row-->

                                    <?php } } } ?> <!--group manager-->

                        </div> <!--GM column-->

                        <?php }?>

                    <?php } ?> <!--regional director-->

                </div> </div><!--RD row-->

        </div>
        <?php } 
		}?>


            <!--for users-->
        </div>

    </div>


    <!-- Modal for creating a new user & editing user -->
    <div class="modal" id="user_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h4 class="modal-title" id="modal_heading"></h4>
                </div>
                <div class="modal-body">

                    <div class="alert alert-danger" id="parent_alert" style="display: none" role="alert">
                        <strong>Regional Director already exists!</strong>
                    </div>

                    <form class="form-horizontal" id="user_form" role="form" method="post" action="<?php echo ROOT_FOLDER ?>/user_manager/post_create_user_data">
                        <div class="form-group user_contact_details">
                            <label for="name" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control user_contact_details" id="name" name="name" placeholder="First Name" value="" maxlength="15">
                                <label class="error-class" id="name-error" style="display: none;"></label>

                            </div>
                        </div>
                        <div class="form-group user_contact_details">
                            <label for="email" class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control user_contact_details" id="email" name="email" placeholder="example@domain.com" value="">
                                <label class="error-class" id="email-error" style="display: none;"></label>
                            </div>

                        </div>

                        <div class="form-group user_contact_details">
                            <label for="contact" class="col-sm-2 control-label">Contact</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control user_contact_details" id="contact" name="contact" placeholder="Contact Number" value="" maxlength="10">
                                <label class="error-class" id="contact-error" style="display: none;"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_type" class="col-sm-2 control-label">Profile</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="profile_id" id="user_type" onchange="validateRDExistence();select_user_type(this.value)">
                                    <option value="" selected disabled> Select Profile</option>
                                    <option value="6">Account Manager</option>
                                    <option value="12">Group Manager</option>
                                    <option value="11">Regional Director</option>
                                </select>
                                <label class="error-class" id="user_type-error" style="display: none;"></label>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="region" class="col-sm-2 control-label">Region</label>
                            <div class="col-sm-10">
                                <select name="region" id="region" class="form-control region" onchange="validateRDExistence();filter_by_region(this.value)">
                                    <option value="" selected disabled>Select Region</option>
                                    <?php foreach($regions as $reg){ ?>
                                        <option value="<?php echo $reg['id']?>" role="<?php echo $reg['rd_exists'] ?>" ><?php echo ucfirst($reg['region_name'])?></option>
                                    <?php } ?>
                                </select>
                                <label class="error-class" id="region-error" style="display: none;"></label>

                            </div>
                        </div>

                        <!--Radio button to select Reporting Manager type-->

                        <div class="form-group" id="radio_user_type" style="display: none">
                            <label for="email" id="reporting_manager_type" class="col-sm-2 control-label">Reporting Manager</label>
                            <div class="col-sm-10">
                                <div class="radio" id="radio-selection">
                                    <label id="radio_12" class="radio_control" style="display: none"><input name="rm" id="rm_12" type="radio" onchange="select_reporting_manager('report_gm','report_rd','report_nh')">Group Manager</label>
                                    <label id="radio_11" class="radio_control" style="display: none"><input name="rm" id="rm_11" type="radio" onchange="select_reporting_manager('report_rd','report_gm','report_nh')">Regional Director</label>
                                    <label id="radio_10" class="radio_control" style="display: none"><input name="rm" id="rm_10" type="radio" onchange="select_reporting_manager('report_nh','report_rd','report_gm')">National Head</label>
                                </div>
                            </div>
                            <label class="error-class" id="reporting-error" style="display: none;"></label>

                        </div>

                        <div class="form-group report_gm" id="group_manager_div" style="display: none">
                            <label for="group_manager" class="col-sm-2 control-label">Group Manager</label>
                            <div class="col-sm-10">
                                <select name="reporting_manager_id" class="form-control" id="select_report_gm">
                                    <option value="" disabled selected>Select Group Manager</option>
                                    <?php foreach($group_managers as $gm) {?>
                                        <option value="<?php echo $gm['user_id']?>" class="<?php echo $gm['region_id']?>"><?php echo $gm['user_name']?></option>
                                    <?php } ?>
                                </select>
                                <label class="error-class" id="group_manager-error" style="display: none;"></label>

                            </div>
                        </div>

                        <div class="form-group report_rd" id="regional_director_div" style="display: none">
                            <label for="regional_director" class="col-sm-2 control-label">Regional Director</label>
                            <div class="col-sm-10">
                                <select name="reporting_manager_id" class="form-control" id="select_report_rd">
                                    <option value="" disabled selected>Select Regional Director</option>
                                    <?php foreach($regional_directors as $rd) {?>
                                        <option value="<?php echo $rd['user_id']?>" class="<?php echo $rd['region_id']?>"><?php echo $rd['user_name']?></option>
                                    <?php } ?>
                                </select>
                                <label class="error-class" id="regional_director-error" style="display: none;"></label>

                            </div>
                        </div>

                        <div class="form-group report_nh" id="national_head_div" style="display: none">
                            <label for="national_head" class="col-sm-2 control-label">National Head</label>
                            <div class="col-sm-10">
                                <select name="reporting_manager_id" class="form-control" id="select_report_nh">
                                    <option value="" disabled selected>Select National Head</option>
                                    <option value="<?php echo $national_head['user_id']?>"><?php echo $national_head['user_name']?></option>
                                </select>
                                <label class="error-class" id="national_head-error" style="display: none;"></label>

                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <a href="#" data-dismiss="modal" class="btn" id="closeUserModel">Close</a>
                            <input id="submit" name="submit" type="submit" value="Create" class="btn btn-primary" onclick="return check_rd_existence()">
                            <input id="nh_user_id" name="nh_user_id" type="hidden" value="<?php echo $national_head['user_id']?>">
                            <input id="edit_user_id" name="hid_user_id" type="hidden" value="">
                            <input id="hid_rd_exist" name="hid_rd_exist" type="hidden" value="">
                        </div>
                    </div>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!--Modal create user ends-->


    <!-- Modal for deleting a user -->
    <div class="modal" id="delete_user">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h4 class="modal-title">Delete User</h4>
                </div>

                <div class="modal-body">
                    <form class="form-horizontal" role="form" method="post" action="<?php echo ROOT_FOLDER ?>/user_manager/post_delete_user">
                        <div class="col-sm-10">
                            Are you sure you want to delete this user?
                        </div>
                        <input type="hidden" name="hid_delete_user_id" id="hid_delete_user_id" value=""/>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group">
                            <div class="col-sm-10 col-sm-offset-2">
                                <a href="#" data-dismiss="modal" class="btn">Close</a>
                                <input id="delete" name="submit" type="submit" value="Delete" class="btn btn-danger">
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!--Modal delete user ends-->


    <!-- Modal for setting targets for a user -->
    <div class="modal" id="user_targets">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h4 class="modal-title">Targets for User</h4>
                </div>

                <div class="modal-body">

                    <form class="form-horizontal" role="form" id="target_form" method="post" action="<?php echo ROOT_FOLDER ?>/user_manager/post_user_targets">

                        <div class="form-group" id="">
                            <label for="" class="col-sm-2 control-label">Year</label>
                            <div class="col-sm-10">
                                <select name="fin_year" id="fin_year" class="form-control" style="width:220px;" onchange="get_user_targets(this.value)">
                                    <option value="" disabled selected>Select Financial Year</option>
                                    <?php $year = 2015;
                                    while($year < 2030){ $z= $year + 1;?>
                                        <option value="<?php echo $year."-".$z ?>" ><?php echo $year."-".$z ?></option>
                                        <?php $year++;
                                    }?>
                                </select>
                                <label class="error-class" id="year-error" style="display: none;"></label>

                            </div>
                        </div>

                        <div class="form-group" id="" >
                            <!--<label for="" class="col-sm-2 control-label">Months</label>-->

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >April</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="april_amount" name="target[april]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >May</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="may_amount" name="target[may]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >June</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="june_amount" name="target[june]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >July</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="july_amount" name="target[july]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >August</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="august_amount" name="target[august]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >September</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="september_amount" name="target[september]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >October</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="october_amount" name="target[october]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >November</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="november_amount" name="target[november]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >December</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="december_amount" name="target[december]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >January</span></div>
                                    <div class="col-sm-6"><span ><input type="text" id="january_amount" name="target[january]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >February</span></div>
                                    <div class="col-sm-6"><span ><input type="text" id="february_amount" name="target[february]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >March</span></span></div>
                                    <div class="col-sm-6"><span ><input type="text" id="march_amount" name="target[march]" class="monthly_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <label class="error-class amount-error" id="user-amount-error" style="display: none;margin-left:250px"></label>
                            </div>

                        </div>

                </div>

                <div class="modal-footer">
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <a href="#" data-dismiss="modal" class="btn">Close</a>
                            <input type="hidden" name="user_id" id="target_user_id" value=""/>
                            <input id="" name="submit" type="submit" value="Save" class="btn btn-success" onclick="return checkAllUserTargets('monthly_target')">
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
    <!--Modal for setting Targets for user ends-->

    <!-- Modal for setting targets for a region -->
    <div class="modal" id="region_targets">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h4 class="modal-title">Targets for Regions</h4>
                </div>

                <div class="modal-body">

                    <form class="form-horizontal" role="form" id="reg_target_form" method="post" action="<?php echo ROOT_FOLDER ?>/user_manager/post_region_targets">

                        <div class="form-group" id="">
                            <label for="" class="col-sm-2 control-label">Region</label>
                            <div class="col-sm-10">
                                <select name="region_id" id="target_region_id" class="form-control" style="width:220px;" onchange="get_region_targets()">
                                    <option value="" selected disabled>Select Region</option>
                                    <?php foreach($regions as $reg){
                                        if($reg['id'] != 9){?>
                                        <option value="<?php echo $reg['id']?>" role="<?php echo $reg['rd_exists'] ?>" ><?php echo ucfirst($reg['region_name'])?></option>
                                    <?php } } ?>
                                </select>
                                <label class="error-class" id="reg-region-error" style="display: none;"></label>

                            </div>
                        </div>

                        <div class="form-group" id="">
                            <label for="" class="col-sm-2 control-label">Year</label>
                            <div class="col-sm-10">
                                <select name="fin_year" id="reg_fin_year" class="form-control" style="width:220px;" onchange="get_region_targets()">
                                    <option value="" disabled selected>Select Financial Year</option>
                                    <?php $year = 2015;
                                    while($year < 2030){ $z= $year + 1;?>
                                        <option value="<?php echo $year."-".$z ?>" ><?php echo $year."-".$z ?></option>
                                        <?php $year++;
                                    }?>
                                </select>
                                <label class="error-class" id="reg-year-error" style="display: none;"></label>

                            </div>
                        </div>

                        <div class="form-group" id="" >

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >April</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_april_amount" name="target[april]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >May</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_may_amount" name="target[may]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >June</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_june_amount" name="target[june]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >July</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_july_amount" name="target[july]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >August</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_august_amount" name="target[august]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >September</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_september_amount" name="target[september]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >October</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_october_amount" name="target[october]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >November</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_november_amount" name="target[november]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >December</span></div>
                                    <div class="col-sm-6"><span><input type="text" id="reg_december_amount" name="target[december]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >January</span></div>
                                    <div class="col-sm-6"><span ><input type="text" id="reg_january_amount" name="target[january]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >February</span></div>
                                    <div class="col-sm-6"><span ><input type="text" id="reg_february_amount" name="target[february]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-6"><span >March</span></span></div>
                                    <div class="col-sm-6"><span ><input type="text" id="reg_march_amount" name="target[march]" class="monthly_reg_target form-control" style="width:80px;" value="0" onblur="" /></span></div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <label class="error-class amount-error" id="region-amount-error" style="display: none;margin-left:250px"></label>
                            </div>

                        </div>

                </div>

                <div class="modal-footer">
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <a href="#" data-dismiss="modal" class="btn">Close</a>
                            <input id="" name="submit" type="submit" value="Save" class="btn btn-success" onclick="return checkAllRegionTargets('monthly_reg_target')">
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
    <!--Modal for setting Targets for user region ends-->


    <!-- Modal for setting profile discounts -->
    <div class="modal" id="profile_discount">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h4 class="modal-title">Discounts for Profile</h4>
                </div>

                <div class="modal-body">

                    <form class="form-horizontal" id="discount_form" role="form" method="post" action="<?php echo ROOT_FOLDER ?>/user_manager/post_profile_discount">
                        <div class="form-group">
                            <label for="name" id="profile_name" class="col-sm-4 control-label"></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="discount" name="discount" placeholder="Discount" value="" onblur="return checkDiscount()" >
                                <label class="error-class" id="discount-error" style="display: none;"></label>

                                <input type="hidden" name="parent_discount" id="hid_parent_discount" value=""/>
                                <input type="hidden" name="child_discount" id="hid_child_discount" value=""/>
                            </div>
                            <div class="col-sm-2">%</div>
                        </div>
                </div>

                <div class="modal-footer">
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <a href="#" data-dismiss="modal" class="btn">Close</a>
                            <input id="" name="submit" type="submit" value="Save" class="btn btn-info" onclick="return checkDiscount()">
                            <input type="hidden" name="profile_id" id="hid_profile_id" value=""/>
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
    <!--Modal for setting profile discounts ends-->



    <!--Modal for upload profile picture Version 0.2-->

    <!-- Cropping modal -->
    <div id="crop-avatar">
    <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="avatar-form" action="<?php echo base_url('user_manager/uploadProfileImage/'); ?>" enctype="multipart/form-data" method="post">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal" type="button">&times;</button>
                        <h4 class="modal-title" id="avatar-modal-label">Change Profile Picture</h4>
                    </div>
                    <div class="modal-body">
                        <div class="avatar-body">
                            <div id="uploadImageProgress" style="display: none;" class="alert alert-info" role="alert">Image is adding please wait...</div>
                            <div id="uploadImageError" style="display: none;" class="alert alert-info" role="alert">Please select Images only!</div>

                            <!-- Upload image and data -->
                            <div class="avatar-upload">
                                <input class="avatar-src" name="avatar_src" type="hidden">
                                <input class="avatar-data" name="avatar_data" type="hidden">
                                <label for="avatarInput">Select File</label>
                                <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">

                            </div>

                            <!-- Crop and preview-->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="avatar-wrapper"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="avatar-preview preview-lg"></div>
                                    <!--<div class="avatar-preview preview-md"></div>
                                    <div class="avatar-preview preview-sm"></div>
                                --></div>
                            </div>

                            <div class="row avatar-btns">

                                <input type="hidden" id="hid_profile_image_user_id" name="hid_profile_image_user_id" value="">

                            </div>
                        </div>
                    </div>
                     <div class="modal-footer">
                         <button id="submitImageUploadButton" disabled class="btn btn-primary avatar-save" type="submit">Done</button>
                         </div>

                </form>
            </div>
        </div>
    </div><!-- /.modal -->
    </div>
    <!--Modal for upload profile picture Version 0.2-->


</div>


<script type="text/javascript">

    $('.region-nav li').click(function(){
        var id = $(this).attr('role');
        $('.am').hide(500);
        $('.rd').hide(500);
        $('.gh').hide(500);
        $('.am_to_nh').hide(500);
        $('.gm_to_nh').hide(500);
        $('.'+id).show(500);
        /*$('#nh-allindia').removeClass('active');
        $('.rd-nav li').removeClass('active');*/
        $('.region-nav li').removeClass('active');
        $(this).addClass('active');
    });

    $('.show_all_users').click(function(){
        $('.region-nav li').removeClass('active');
        $('.South, .West, .East, .North, .None').show(500);
		$('.region-nav li').removeClass('active');
        $(this).addClass('active');
    });

    $('.open_create_modal').click(function(){
        $("#user_form").get(0).reset()
        $("#user_form").attr("action", "<?php echo ROOT_FOLDER ?>/user_manager/post_create_user_data");
        $("#hid_rd_exist").val(0);
        $("#parent_alert").hide();
        $("#modal_heading").text("Create User");
        $("#email").removeAttr("disabled");
        $("#user_type").removeAttr('disabled');
        $("#radio_user_type").hide();
        $("#group_manager_div").hide();
        $(".user_contact_details").show();
        $("#submit").val('Create');
        $('#user_modal').modal('show');
    });

    $('.open_edit_modal').click(function(){
        var user_id = $(this).data('id');
        $("#hid_rd_exist").val(0);
        $("#parent_alert").hide();
        $("#user_type").prop("disabled",false);
        $("#user_type option").prop("selected",false);

        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/user_manager/get_user_details',
            async: false,
            data: {
                user_id: user_id
            },
            dataType: 'json',
            success: function(data){

                var username = data[0].user_name;
                var email = data[0].user_email;
                var contact = data[0].user_phone;
                var reporting_manager_id = data[0].reporting_manager_id;
                var region_id = data[0].region_id;
                var profile_id = data[0].profile_id;
                var parent_profile_id = data[0].parent_profile_id;
                var reporting_manager_id = data[0].reporting_manager_id;
                var reporting_manager_profile_id = data[0].reporting_manager_profile_id;

                $("#name").val(username);
                $("#email").val(email).attr("disabled","disabled");
                $("#contact").val(contact);
                $("#edit_user_id").val(user_id);

                $("#region option[value= "+region_id+"]").prop("selected",true);
                $("#user_type option[value= "+profile_id+"]").prop("selected",true);
                $("#user_type").prop("disabled",true);
                $("#user_form").attr("action", "<?php echo ROOT_FOLDER ?>/user_manager/post_edit_user_data");
                $("#modal_heading").text("Edit User");
                $(".user_contact_details").show();
                $("#submit").val('Update');

                filter_by_region(region_id);
                select_user_type(profile_id);

                $("#rm_"+reporting_manager_profile_id).prop("checked",true);

                if(reporting_manager_profile_id == '12'){
                    select_reporting_manager('report_gm','report_rd','report_nh');
                    $("#select_report_gm option[value= "+reporting_manager_id+"]").prop("selected",true);
                }else
                if(reporting_manager_profile_id == '11'){
                    select_reporting_manager('report_rd','report_gm','report_nh');
                    $("#select_report_rd option[value= "+reporting_manager_id+"]").prop("selected",true);
                }else
                if(reporting_manager_profile_id == '10'){
                    select_reporting_manager('report_nh','report_rd','report_gm');
                    $("#select_report_nh option[value= "+reporting_manager_id+"]").prop("selected",true);
                }

                $('#user_modal').modal('show');
            }
        });
    });

    $('.open_delete_modal').click(function(){
        var user_id = $(this).data('id');
        $("#hid_delete_user_id").val(user_id);
    });

    $('.open_targets_modal').click(function(){
        cleanValidation();
        var user_id = $(this).data('id');
        $("#target_form").get(0).reset();
        $('#target_user_id').val(user_id);
        $('.monthly_target').val(0);
        $('#user_targets').modal('show');

    });

    $('.open_reg_targets_modal').click(function(){
        cleanValidation();
        var user_id = $(this).data('id');
        $("#reg_target_form").get(0).reset();
        $('.monthly_reg_target').val(0);
        $('#region_targets').modal('show');

    });

    $('.open_discounts_modal').click(function(){
        cleanValidation();
        var profile_id = $(this).data('id');
        $("#discount_form").get(0).reset();
        $("#hid_profile_id").val(profile_id);

        if(profile_id == 6){
            $("#profile_name").text('Account Manager');
        }else if(profile_id == 11){
            $("#profile_name").text('Regional Director');
        }else if(profile_id == 12){
            $("#profile_name").text('Group Manager');
        }

        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/user_manager/get_profile_discounts',
            async: false,
            data: {
                profile_id: profile_id
            },
            dataType: 'json',
            success: function(data){
                if(data != ''){
                    var discount = data.discount;
                    var parent_discount = data.parent_discount;
                    var child_discount = data.child_discount;

                    $('#discount').val(discount);
                    $('#hid_child_discount').val(child_discount);
                    $('#hid_parent_discount').val(parent_discount);

                    if(child_discount == null){
                        $('#hid_child_discount').val(0);
                    }
                    if(parent_discount == null){
                        $('#hid_parent_discount').val(100);
                    }

                    $('#profile_discount').modal('show');

                }else{
                    $('.monthly_target').val(0);
                }
            }
        });

    });


    function filter_by_region(region){

        $("#select_report_gm > option[class!= "+region+"]").hide();
        $("#select_report_gm > option[class= "+region+"]").show();

        $("#select_report_rd > option[class!= "+region+"]").hide();
        $("#select_report_rd > option[class= "+region+"]").show();

        $("#select_report_gm option[value= '']").prop("selected",true);
        $("#select_report_rd option[value= '']").prop("selected",true);

    }


    function validateRDExistence(){
        var rd_existence = $('.region option:selected').attr('role');
        var user_type = $("#user_type").val();

        if(user_type == 11){
            if(rd_existence == 1){
                $("#parent_alert").show();
                $("#hid_rd_exist").val(1);
            }else{
                $("#parent_alert").hide();
                $("#hid_rd_exist").val(0);
            }
        }else{
            $("#parent_alert").hide();
            $("#hid_rd_exist").val(0);
        }
    }

    function select_reporting_manager(show_id,hide_id1,hide_id2){
        $("."+show_id).show();
        $("#select_"+show_id).removeAttr('disabled');
        $("."+hide_id1).hide();
        $("#select_"+hide_id1).attr("disabled","disabled");
        $("."+hide_id2).hide();
        $("#select_"+hide_id2).attr("disabled","disabled");
    }

    function select_user_type(user_type){

        $("#radio_user_type").show();
        $(".radio_control").hide();
        $(".report_gm, .report_rd, .report_nh ").hide();

        if(user_type == 6){
            //if Account Manager
            $("#radio_11").show();
            $("#radio_12").show();
            $("#radio_10").show();
        }
        if(user_type == 12){
            //if Group Head
            $("#radio_11").show();
            $("#radio_10").show();
        }
        if(user_type == 11){
            //if Regional Director
            $("#radio_10").show();
        }
    }

    function check_rd_existence(){
        var rd_existence = $("#hid_rd_exist").val();
        var user_type = $("#user_type").val();

        if(user_type == 11){
            if(rd_existence == 1){
                $("#parent_alert").show();
                return false;
            }else{
                $("#parent_alert").hide();
                return true;
            }
        }
    }

</script>

<script type="application/javascript">

/* Form Validation v-1.0 */

    var existEmailState = "TRUE" ;

$(document).ready(function(){

    $("#submit").click(function(){

        if( !doValidation() ){

            return false ;

        }

    }) ;

    $("#user_type").change(function(){

        $("input:radio[name='rm']").each(function(){
            $(this).removeAttr('checked');
        });

    }) ;
    $("#name ,#email, #contact, #region, #user_type, input[name=rm], #select_report_gm, #select_report_rd, #select_report_nh ").change(function(){

        doValidation() ;

    }) ;
    $( "#email" ).focusout(function() {
        checkEmailExists() ;
    }) ;
    $('#user_modal').on('hide.bs.modal', function (e) {
        cleanValidation() ;
    });

    $('.monthly_target').blur(function() {
        validateUserTargets(this.id,this.value);
    }) ;

    $('.monthly_reg_target').blur(function() {
        validateRegionTargets(this.id,this.value);
    }) ;

});

function doValidation(){

    var specialCharacterRegx = /^[A-Za-z\s]+$/ ;

    var phoneRegx = /^\d{3}-?\d{3}-?\d{4}$/g ;

    var userName = $("#name").val() ;

    var contact  = $("#contact").val() ;

    var region   = $("#region option:selected") ;
    var userType = $("#user_type option:selected") ;
    var reportingManager  = $("input[name=rm]:checked").val() ;
    var groupManager      = $("#select_report_gm option:selected") ;
    var regionalDirector  = $("#select_report_rd option:selected") ;
    var nationalHead      = $("#select_report_nh option:selected") ;

    if( userName == "" ){

        $("#name").css("border", "1px solid red");
        $("#name-error").css("display", "block");
        $("#name-error").text('Enter username.');

        return false ;

    }else if( !specialCharacterRegx.test(userName) ){

        $("#name").css("border", "1px solid red");
        $("#name-error").css("display", "block");
        $("#name-error").text('Username should not have special characters.');

        return false ;

    }else{

        $("#name").removeAttr("style");
        $("#name-error").text('');

    }

    if( $("#email").val() == "" ){

        return checkEmailExists() ;
    }

    if( contact == "" ){

        $("#contact").css("border", "1px solid red");
        $("#contact-error").css("display", "block");
        $("#contact-error").text('Enter contact number.');

        return false ;

    }else if( !phoneRegx.test(contact) ){

        $("#contact").css("border", "1px solid red");
        $("#contact-error").css("display", "block");
        $("#contact-error").text('Contact number should have numbers and 10 digits only.');

        return false ;

    }else{

        $("#contact").removeAttr("style");
        $("#contact-error").text('');

    }
    if( userType.is(":disabled") ){

        $("#user_type").css("border", "1px solid red");
        $("#user_type-error").css("display", "block");
        $("#user_type-error").text('Select user type.');

        return false ;

    }else{

        $("#user_type").removeAttr("style");
        $("#user_type-error").text('');
    }
    if( region.is(":disabled") ){

        $("#region").css("border", "1px solid red");
        $("#region-error").css("display", "block");
        $("#region-error").text('Select region.');

        return false ;

    }else{

        $("#region").removeAttr("style");
        $("#region-error").text('');
    }



    if( !reportingManager ){

        $("#radio-selection").css("color", "red");
        return false ;

    }else{

        $("#radio-selection").removeAttr("style");
        $("#reporting").text('');
    }

    if( groupManager.is(":disabled") && $("#group_manager_div").css('display') != 'none'  ){

        $("#select_report_gm").css("border", "1px solid red");
        $("#group_manager-error").css("display", "block");
        $("#group_manager-error").text('Select group manager.');
        return false ;

    }else{

        $("#select_report_gm").removeAttr("style");
        $("#group_manager-error").text('');
    }
    if( regionalDirector.is(":disabled") && $("#regional_director_div").css('display') != 'none'  ){

        $("#select_report_rd").css("border", "1px solid red");
        $("#regional_director-error").css("display", "block");
        $("#regional_director-error").text('Select regional director.');
        return false ;

    }else{

        $("#select_report_rd").removeAttr("style");
        $("#regional_director-error").text('');
    }

    if( nationalHead.is(":disabled") && $("#national_head_div").css('display') != 'none'  ){

        $("#select_report_nh").css("border", "1px solid red");
        $("#national_head-error").css("display", "block");
        $("#national_head-error").text('Select national head.');
        return false ;

    }else{

        $("#select_report_nh").removeAttr("style");
        $("#national_head-error").text('');
    }
    return true ;
}

function cleanValidation(){

    $("#name").removeAttr("style");
    $("#name-error").text('');

    $("#email").removeAttr("style");
    $("#email-error").text('');

    $("#contact").removeAttr("style");
    $("#contact-error").text('');

    $("#region").removeAttr("style");
    $("#region-error").text('');

    $("#user_type").removeAttr("style");
    $("#user_type-error").text('');

    $("#radio-selection").removeAttr("style");
    $("#reporting").text('');

    $("#select_report_gm").removeAttr("style");
    $("#group_manager-error").text('');

    $("#select_report_rd").removeAttr("style");
    $("#regional_director-error").text('');

    $("#select_report_nh").removeAttr("style");
    $("#national_head-error").text('');

    $("#fin_year").removeAttr("style");
    $("#year-error").text('');

    $(".monthly_target").removeAttr("style");
    $(".amount-error").text('');

    $("#reg_fin_year").removeAttr("style");
    $("#reg-year-error").text('');

    $("#target_region_id").removeAttr("style");
    $("#reg-region-error").text('');

    $(".monthly_reg_target").removeAttr("style");
    $(".amount-error").text('');

    $("#discount").removeAttr("style");
    $("#discount-error").text('');
}

function checkEmailExists(){

    var email    = $("#email").val() ;
    var data = "emailId=" + email.trim() ;
    var url = "<?php echo base_url('user_manager/duplicate_user_email_check') ?>" ;
    var email    = $("#email").val() ;
    var emilRegx = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

    $("#email").removeAttr("style");
    $("#email-error").text('');

    if( $("#email").is(":enabled") ){

        if( email == "" ){

            $("#email").css("border", "1px solid red");
            $("#email-error").css("display", "block");
            $("#email-error").text('Enter email.');

            return false ;

        }else if( !emilRegx.test(email) ){

            $("#email").css("border", "1px solid red");
            $("#email-error").css("display", "block");
            $("#email-error").text('Enter correct email.');

            return false ;

        }else{

            $.ajax({

                url: url,
                type:"POST",
                data: data,
                success: function(response){

                    if( response.trim() == "FALSE" ){

                        $("#email").css("border", "1px solid red");
                        $("#email-error").css("display", "block");
                        $("#email-error").text('Oops, email already exist!!');

                        return false ;
                    }else{

                        $("#email").removeAttr("style");
                        $("#email-error").text('');

                    }
                }

            });

        }

    }
}

/* Form Validation v-1.0 */

    /* Image upload */

    function select_file(){
        document.getElementById('profile_image').click();
        return false;
    }
    $('.open_profile_image_modal').click(function(){
        var user_id = $(this).data('id');
        $("#hid_profile_image_user_id").val(user_id);

    });

    $( "#avatarInput" ).change(function(){

        var ext_file_name   = $("#avatarInput")[0].files[0].name;
        var Extension   = ext_file_name.split('.').pop().toLowerCase() ;

        if( (Extension != "gif" && Extension != "png") && (Extension != "bmp") && (Extension != "jpeg" && Extension != "jpg") ){

            $("#uploadImageError").show() ;
            $("#submitImageUploadButton").prop( "disabled", true ) ;

        }else{

            $('#uploadImageError').hide();
            $("#submitImageUploadButton").prop( "disabled", false ) ;
        }

    }) ;

    function get_user_targets(fin_year){
        cleanValidation();
        var user_id = $('#target_user_id').val();
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/user_manager/get_user_targets',
            async: false,
            data: {
                user_id: user_id,
                fin_year:fin_year
            },
            dataType: 'json',
            success: function(data){
                if(data != ''){
                    $('#january_amount').val(data.january);
                    $('#february_amount').val(data.february);
                    $('#march_amount').val(data.march);
                    $('#april_amount').val(data.april);
                    $('#may_amount').val(data.may);
                    $('#june_amount').val(data.june);
                    $('#july_amount').val(data.july);
                    $('#august_amount').val(data.august);
                    $('#september_amount').val(data.september);
                    $('#october_amount').val(data.october);
                    $('#november_amount').val(data.november);
                    $('#december_amount').val(data.december);
                }else{
                    $('.monthly_target').val(0);
                }
            }
        });
    }

    function get_region_targets(fin_year){
        cleanValidation();
        var fin_year = $('#reg_fin_year').val();
        var region_id = $('#target_region_id').val();
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/user_manager/get_region_targets',
            async: false,
            data: {
                region_id: region_id,
                fin_year:fin_year
            },
            dataType: 'json',
            success: function(data){
                if(data != ''){
                    $('#reg_january_amount').val(data.january);
                    $('#reg_february_amount').val(data.february);
                    $('#reg_march_amount').val(data.march);
                    $('#reg_april_amount').val(data.april);
                    $('#reg_may_amount').val(data.may);
                    $('#reg_june_amount').val(data.june);
                    $('#reg_july_amount').val(data.july);
                    $('#reg_august_amount').val(data.august);
                    $('#reg_september_amount').val(data.september);
                    $('#reg_october_amount').val(data.october);
                    $('#reg_november_amount').val(data.november);
                    $('#reg_december_amount').val(data.december);
                }else{
                    $('.monthly_reg_target').val(0);
                }
            }
        });
    }

    function checkAllUserTargets(class_name){
        var ret = true;
        $('.'+class_name).each(function() {
            var id = $(this).attr('id');
            var amount = $(this).val();
            var result = validateUserTargets(id,amount);
            if(!result){
                ret = false;
                return ret;
            }
        });
        return ret;
    }

    function checkAllRegionTargets(class_name){
        var ret = true;
        $('.'+class_name).each(function() {
            var id = $(this).attr('id');
            var amount = $(this).val();
            var result = validateRegionTargets(id,amount);
            if(!result){
                ret = false;
                return ret;
            }
        });
        return ret;
    }

    function validateRegionTargets(id,amount){

        var region_id = $('#target_region_id').val();
        if(region_id == null){
            $("#target_region_id").css("border", "1px solid red");
            $("#reg-region-error").css("display", "block");
            $("#reg-region-error").text('Please select a region');
            return false;
        }else{
            $("#target_region_id").removeAttr("style");
            $("#reg-region-error").text('');
        }

        var fin_year = $('#reg_fin_year').val();
        if(fin_year == null){
            $("#reg_fin_year").css("border", "1px solid red");
            $("#reg-year-error").css("display", "block");
            $("#reg-year-error").text('Please select a financial year');
            return false;
        }else{
            $("#reg_fin_year").removeAttr("style");
            $("#reg-year-error").text('');
        }

        //check input for amount
        var objRegExp  = /^[0-9]\d*(\.\d+)?$/g;
        if ((amount.match(objRegExp) != null ) && (amount.length > 0) ){
            $("#"+id).removeAttr("style");
            $(".amount-error").text('');
            return true ;
        }
        else {
            $("#"+id).css("border", "1px solid red");
            $(".amount-error").css("display", "block");
            $(".amount-error").text('Invalid Amount');
            return false;
        }

    }

    function validateUserTargets(id,amount){

        var fin_year = $('#fin_year').val();
        if(fin_year == null){
            $("#fin_year").css("border", "1px solid red");
            $("#year-error").css("display", "block");
            $("#year-error").text('Please select a financial year');
            return false;
        }else{
            $("#fin_year").removeAttr("style");
            $("#year-error").text('');
        }

        //check input for amount
        var objRegExp  = /^[0-9]\d*(\.\d+)?$/g;
         if ((amount.match(objRegExp) != null ) && (amount.length > 0) ){
             $("#"+id).removeAttr("style");
             $(".amount-error").text('');
             return true ;
         }
         else {
             $("#"+id).css("border", "1px solid red");
             $(".amount-error").css("display", "block");
             $(".amount-error").text('Invalid Amount');
             return false;
         }

    }

    function checkDiscount(id,amount){

        var id = 'discount';
        var amount = $('#discount').val();
        var parent_discount = $('#hid_parent_discount').val();
        var child_discount = $('#hid_child_discount').val();

        //check input for amount
        var objRegExp  = /^[0-9]\d*(\.\d+)?$/g;
        if ((amount.match(objRegExp) != null ) && (amount.length > 0) ){
            $("#discount").removeAttr("style");
            $("#discount-error").text('');
        }
        else {
            $("#discount").css("border", "1px solid red");
            $("#discount-error").css("display", "block");
            $("#discount-error").text('Invalid Discount');
            return false;
        }

        //validations for discount
        var amount = parseFloat(amount);
        var parent_discount = parseFloat(parent_discount);
        var child_discount = parseFloat(child_discount);

        if(amount > 100 || amount < 0){
            $("#discount-error").css("display", "block");
            $("#discount-error").text('Discount should be 0-100');
            return false;
        }
        else if(amount >= parent_discount){
            $("#discount-error").css("display", "block");
            $("#discount-error").text('Discount should be Regional Director > Group Manager > Account Manager');
            return false;
        }

        else if(amount <= child_discount){
            $("#discount-error").css("display", "block");
            $("#discount-error").text('Discount should be Regional Director > Group Manager > Account Manager');
            return false;
        }

    }

</script>

