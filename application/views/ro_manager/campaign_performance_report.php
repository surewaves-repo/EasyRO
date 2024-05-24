<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/flexigrid.css">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/flexigrid.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<style>
.ui-datepicker-calendar {
    display: none;
    }
</style>
<div id="hld">

    <div class="wrapper">		<!-- wrapper begins -->

        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <input type="hidden" id="profile_id" value="<?php echo $profile_id?>" />
            <?php echo $menu ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->

        <div class="block">
            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <form action="<?php echo ROOT_FOLDER ?>/ro_manager/request_campaign_performance_csv/" method="post" enctype="multipart/form-data" style="float: left;">
                    <?php  if($profile_id == 1 or $profile_id == 2 or $profile_id == 7 or $profile_id == 11 or $profile_id == 10) { ?>
                        <label for="month">Month:</label>
                        <input type="text" readonly="readonly" id="month" name="month" />
                        &nbsp;&nbsp;

                        <input type="button" id="go" value="Get Report"/>

                        <label for="month">Mail Id:</label>
                        <input type="text" id="mail_ids" name="mail_ids" value="">
                        <!--<button id="export_csv">Export to CSV</button>-->
                        <input type="submit" id="request_report" value="Request Report" onclick="return check_form()">
                    <?php } else { ?>
                        <p style="color:red;font-weight:bold;font-size:16px">You do not have permission to access</p>
                    <?php } ?>
                </form>

            </div>
            <div class="block_content">
                <table id="flexitable"></table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#go').click(function(){
        refresh_grid();

        if($("#profile_id").val() == 1 || $("#profile_id").val() == 2 || $("#profile_id").val() == 7 || $("#profile_id").val() == 10 || $("#profile_id").val() == 11){
            $("#flexitable").flexigrid({
                params: get_serialize_value(),
                url: '/surewaves_easy_ro/ro_manager/get_campaign_performance_report',
                dataType: 'json',
                colModel : [
                    {display: 'Network Name', name : 'mso',  width : 110, align: 'center'},
                    {display: 'Channel Name', name: 'channelName', width : 90, align: 'center'},
                    {display: 'Network Ro Number', name : 'networkRoNumber',  width : 110, align: 'center'},
                    {display: 'Billing Name', name: 'billingName', width : 90, align: 'center'},
                    {display: 'Internal Ro Number', name: 'internalRoNumber',  width : 90, align: 'center'},
                    {display: 'Start Date', name : 'startDT',  width : 110, align: 'center'},
                    {display: 'End Date', name: 'endDT', width : 90, align: 'center'},
                    {display: 'Scheduled Spot Seconds', name : 'scheduledSpotSeconds', width : 110, align: 'center'},
                    {display: 'Played Spot Seconds', name: 'playedSpotSeconds', width : 90, align: 'center'},
                    {display: 'Scheduled Banner Seconds', name: 'scheduledBannerSeconds', width : 90, align: 'center'},
                    {display: 'Played Banner Seconds', name: 'playedBannerSeconds', width : 90, align: 'center'},
                    {display: 'Spot Rate', name: 'spotRate', width : 90, align: 'center'},
                    {display: 'Banner Rate', name: 'bannerRate', width : 90, align: 'center'},
                    {display: 'Total Payable', name: 'totalPayable', width : 90, align: 'center'}
                ],

                sortname: "networkRoNumber",
                sortorder: "asc",
                usepager: true,
                title: 'Campaign Performance Report',
                useRp: true,
                rp: 15,
                showTableToggleBtn: true,
                width: 'auto',
                height: '400',
                rpOptions: [10,15,20,25,40],
                /*pagestat: 'Displaying: {from} to {to} of {total} items.',*/
                blockOpacity: 0.5,
            });
        }
    });
    function get_serialize_value(obj){
        var p = [];

        var nw = $('#network_id');
        var df = $('#month');

        p.push({name: nw.attr('name'),value: nw.val()});
        p.push({name: df.attr('name'),value: df.val()});
        return p;
    }

    function refresh_grid(obj){
        $("#flexitable").flexOptions({params: get_serialize_value(obj)});
        $('#flexitable').flexOptions({newp: 1}).flexReload();
    }

    $("#export_csv").click(function() {
        from_date = $("#month").val();
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/download_campaign_performance_csv/"+from_date;
    });

    $( "#month" ).datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM yy',
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
	    year = year - 1; 
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });


    function check_form(){
        var from_date = $("#month").val();
        var mail_ids = $("#mail_ids").val();

        if(mail_ids == ""){
            alert("Please enter Email Id");
            $("#mail_ids").focus();
            return false;
        }else if(from_date == ""){
            alert("Please select a Month");
            $("#from_date").focus();
            return false;
        }else{
            //window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/request_campaign_performance_csv/"+from_date+"/"+mail_ids;
            alert("Campaign Performance will be sent to:"+mail_ids);
            return true;
        }
    }

    /*$("#request_report").click(function() {
        var from_date = $("#month").val();
        var mail_ids = $("#mail_ids").val();
        if(mail_ids == ""){
            alert("Please enter Email Id");
            $("#mail_ids").focus();
            return false;
        }

    });*/


    // end
</script>
