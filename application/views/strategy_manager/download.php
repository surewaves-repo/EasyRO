<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>SureWaves Easy RO</title>
    <link rel="icon" type="image/png" href="<?php echo ROOT_FOLDER ?>/images/SWV_Logo_Watermark.ico">

    <link href="/surewaves_easy_ro/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/surewaves_easy_ro/includes/css/bootstrap-glyphicons.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.3/css/bootstrap-select.min.css">


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
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.3/js/bootstrap-select.min.js"></script>


<div class="container">

    <nav class="navbar navbar-inverse" style="margin-top: 12px">

        <ul class="nav navbar-nav">
            <img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="width:150px;padding-top: 8px"/>
        </ul>
        <!--<ul class="nav navbar-nav">
            <li><a href="<?php /*echo ROOT_FOLDER */?>/strategy_manager/home">Home</a></li>
            <li class="active"><a href="<?php /*echo ROOT_FOLDER */?>/strategy_manager/download_ro_data">Download</a></li>
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

<div class="bs-callout bs-callout-danger">
    <form class="form-horizontal" role="form">
        <fieldset>

            <!-- Form Name -->
            <legend>Download User Performance</legend>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="textinput">Start Month</label>
                <div class="col-sm-10">
                    <input type="text" readonly="readonly" id="start_month" name="start_month" placeholder="Start Month" value=""/>
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="textinput">End Month</label>
                <div class="col-sm-10">
                    <input type="text" readonly="readonly" id="end_month" name="end_month" placeholder="End Month" value=""/>
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="textinput">Region</label>
                <div class="col-sm-10">
                    <select class="selectpicker" id="region" name="region" onchange="javascript:get_users_for_region(this.value)">
                        <option value="all">All</option>
                        <?php foreach($regions as $reg) {
                        if($reg['id'] != 9){?>
                            <option value="<?php echo $reg['id'] ?>"><?php echo ucfirst($reg['region_name']) ?></option>
                        <?php } }?>
                    </select>
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="textinput">User</label>
                <div class="col-sm-10">
                    <select class="selectpicker" id="user" name="user">
                        <option value="all">All</option>
                        <?php foreach($all_users as $user) {?>
                                <option value="<?php echo $user['user_id'] ?>"><?php echo $user['user_name'] ?></option>
                            <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2">
                    <div>
                        <button type="button" id="download" class="btn btn-success" onclick="download_data_for_user()">Download</button>
                    </div>
                </div>
            </div>

            </fieldset>
        </form>
    </div>
</div>

<script>

    $(document).ready(function(){
        $("#start_month").datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            maxDate: "+0m",
            dateFormat: 'MM yy',
            beforeShow: function () {
                if ((selDate = $(this).val()).length > 0)
                {
                    iYear = selDate.substring(selDate.length - 4, selDate.length);

                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));

                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                }
            },
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
        }).datepicker('setDate', new Date());

        $("#end_month").datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            maxDate: "+0m",
            dateFormat: 'MM yy',
            beforeShow: function () {
                if ((selDate = $(this).val()).length > 0)
                {
                    iYear = selDate.substring(selDate.length - 4, selDate.length);

                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));

                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                }
            },
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
        }).datepicker('setDate', new Date());

        $('select').selectpicker();
    });

    function get_users_for_region($region_id){
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/strategy_manager/get_users_for_region',
            async: false,
            data: {
                region_id: $('#region').val()
            },
            beforeSend: function(){
                $("#user").html("");
                $("#user").append("<option value='all'>All</option>");
            },
            dataType: 'json',
            success: function(data){
                if(data != ''){
                    $.each(data, function (index, item) {
                        $("#user").append("<option value='"+item.user_id+"'>" + item.user_name  + "</option>");
                    });
                    $('select').selectpicker('refresh');
                }else{

                }
            }
        });
    }

    function download_data_for_user(){
        var start_month = $('#start_month').val();
        var end_month   = $('#end_month').val();
        var region_id      = $('#region').val();
        var user_id        = $('#user').val();

        var start_month_timestamp = new Date("01 "+start_month).getTime() / 1000;
        var end_month_timestamp = new Date("30 "+end_month).getTime() / 1000;

        if(start_month_timestamp > end_month_timestamp){
            alert("Start Month should be less than End Month");
            return false;
        }

        document.location.href = "<?php echo ROOT_FOLDER ?>/strategy_manager/download_ro_data_for_user/" + start_month + "/" + end_month + "/" + region_id + "/" + user_id ;

        /*$.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/strategy_manager/download_ro_data_for_user',
            async: false,
            data: {
                start_month:start_month,
                end_month:end_month,
                region_id:region,
                user_id:user
            },
            //dataType: 'json',
            success: function(data){
                if(data != ''){

                }else{

                }
            }
        });*/
    }
</script>
