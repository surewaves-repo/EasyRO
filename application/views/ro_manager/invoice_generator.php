<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/flexigrid.css">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<!--<script src="<?php echo base_url(); ?>assets/js/flexigrid.js" type="text/javascript"></script>-->
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

                <form name="generate_invoice" action="<?php echo ROOT_FOLDER ?>/ro_manager/request_invoice_generator/" method="post" target="_blank" style="float: left;">
                    <?php  if($profile_id == 1 or $profile_id == 2 or $profile_id == 7 or $profile_id == 11 or $profile_id == 10) { ?>
                        <label for="month">Month:</label>
                        <input type="text" readonly="readonly" id="month" name="month" />
                        &nbsp;&nbsp;
                        <input type="button" id="download_csv" value="Download Invoice Generator CSV" onclick="check_form()"/>
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
$(document).ready(function(){
    $( "#month" ).datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM yy',
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        },
        beforeShow: function() {
            if ((selDate = $(this).val()).length > 0)
            {
                iYear = selDate.substring(selDate.length - 4, selDate.length);
                iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5),
                    $(this).datepicker('option', 'monthNames'));
                $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
            }
        }
    });
});

    function check_form(){
        var from_date = $("#month").val();
        if(from_date == ""){
            alert("Please select a Month");
            $("#from_date").focus();
            return false;
        }
        document.generate_invoice.submit();
    }

    // end
</script>
