<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>SureWaves Easy RO</title>
    <link rel="icon" type="image/png" href="<?php echo ROOT_FOLDER ?>/images/SWV_Logo_Watermark.ico">

    <link href="/surewaves_easy_ro/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/surewaves_easy_ro/includes/css/bootstrap-glyphicons.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">


    <style>
        @import url("/surewaves_easy_ro/css/colorbox.css");

        .ui-datepicker-calendar {
            display: none;
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

        /*for header navigation menu*/
        .dropdown-menu .sub-menu {
            left: 100%;
            position: absolute;
            top: 0;
            visibility: hidden;
            margin-top: -1px;
        }

        .dropdown-menu li:hover .sub-menu {
            visibility: visible;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .nav-tabs .dropdown-menu, .nav-pills .dropdown-menu, .navbar .dropdown-menu {
            margin-top: 0;
        }

    </style>

</head>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.colorbox.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>


<div class="container">

    <nav class="navbar navbar-inverse" style="margin-top: 12px">

        <ul class="nav navbar-nav">
            <img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="width:150px;padding-top: 8px"/>
        </ul>
        <!--<ul class="nav navbar-nav">
            <li><a href="<?php /*echo ROOT_FOLDER */?>/strategy_manager/home">Home</a></li>
            <li><a href="<?php /*echo ROOT_FOLDER */?>/strategy_manager/download_ro_data">Download</a></li>
            <li class="active"><a href="<?php /*echo ROOT_FOLDER */?>/strategy_manager/user_details">User Details</a></li>
        </ul>-->
        <?php echo $menu ?>
        <ul class="nav navbar-nav" style="float: right">
            <li>Hello <?php echo $logged_in_user['user_name'] ?></li>
            <li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></li>
            <li><img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="float:right;padding-top:5px;"/></li>
        </ul>
    </nav>

    <div class="bs-callout bs-callout-danger">
        <h4>Easy RO Strategy Management</h4>
    </div>

    <div class="bs-callout bs-callout-danger" style="height:200px">

        <legend>User Details</legend>

        <div class="col-sm-2" >Name</div>
        <div class="col-sm-10">
            <?php echo $logged_in_user['user_name'] ?>
        </div>

        <div class="col-sm-2">Email</div>
        <div class="col-sm-10">
            <?php echo $logged_in_user['user_email'] ?>
        </div>

        <div class="col-sm-2">Phone</div>
        <div class="col-sm-10">
            <?php echo $logged_in_user['user_phone'] ?>
        </div>

        <div class="col-sm-2">Profile Type</div>
        <div class="col-sm-10">
            <?php echo $profile_details['profile_name'] ?>
        </div>

        <div class="col-sm-2">Password</div>
        <div class="col-sm-10">
            <a href="javascript:change_user_password('<?php echo $logged_in_user['user_id'] ?>')" class="btn btn-success">Change User Password</a>
        </div>

    </div>
</div>

<script>
    function change_user_password(id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/change_user_password/' + id,iframe:true, width: '530px', height:'280px'});
    }
</script>