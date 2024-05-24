<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>SureWaves Easy RO</title>
    <link rel="icon" type="image/png" href="<?php echo ROOT_FOLDER ?>/images/SWV_Logo_Watermark.ico">

    <link href="/surewaves_easy_ro/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/surewaves_easy_ro/includes/css/bootstrap-glyphicons.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

    <style>
        @import url("/surewaves_easy_ro/css/colorbox.css");

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
            <li class="active" ><a href="<?php /*echo ROOT_FOLDER */?>/strategy_manager/home">Home</a></li>
            <li><a href="<?php /*echo ROOT_FOLDER */?>/strategy_manager/download_ro_data">Download</a></li>
            <li><a href="<?php /*echo ROOT_FOLDER */?>/strategy_manager/user_details">User Details</a></li>
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

    <table class="table">
        <tr class="active">
            <td></td>
            <td>User Name</td>
            <td>Designation</td>
            <td>Region</td>
            <td>Reporting Manager</td>
            <td>Monthly Target</td>
            <td>Target Achieved</td>
            <td></td>
        </tr>

        <?php foreach($all_users as $user) {
            $imagePath = ROOT_FOLDER."/images/user.png" ;

            if(!empty( $user["profile_image"]) ){

                $imagePath = $user["profile_image"] ;
            }
            ?>
        <tr>
            <td><img src="<?php echo $imagePath; ?>" alt="HTML" height="50px" width="50px"></td>
            <td><?php echo $user['user_name']?></td>
            <td>
                <?php if($user['designation'] == 'Account Manager') {?>
                    <span class="label label-warning"><?php echo $user['designation'] ?></span></td>
                <?php } else if($user['designation'] == 'Group Manager') { ?>
                    <span class="label label-success"><?php echo $user['designation'] ?></span></td>
                <?php } else {?>
                    <span class="label label-info"><?php echo $user['designation'] ?></span></td>
                <?php } ?>

            <td><?php echo ucfirst($user['region_name'])?></td>
            <td><?php echo $user['reporting_to']?></td>
            <td><?php echo $user['monthly_target']?></td>
            <td><?php echo $user['monthly_achieved']?></td>
            <td><input id="submit" name="submit" type="button" value="View ROs" class="btn btn-primary" onclick="show_ros_for_user(<?php echo $user['user_id']?>)"></td>
        </tr>
        <?php } ?>

    </table>
</div>

<script>
    function show_ros_for_user(user_id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/strategy_manager/get_ros_submitted/'+user_id,
            iframe:true,
            width: '1050px',
            height:'600px'
        });
    }
</script>
