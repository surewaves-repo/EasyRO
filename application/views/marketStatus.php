<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Surewaves MediaGrid: Agency View</title>

    <style type="text/css" media="all">
        @import url("css/style.css");
        @import url("css/jquery.wysiwyg.css");
        @import url("css/facebox.css");
        @import url("css/visualize.css");
        @import url("css/date_input.css");
        @import url("css/colorbox.css");

        .floatingHeader {
            position: fixed;
            top: 0;
            visibility: hidden;
            background-color: white;
        }

        table#filterTable td:nth-child(1){
            color:black;
        }
        table#filterTable td:nth-child(3){
            color:red;
        }
        table#filterTable td:nth-child(5){
            color:green;
        }
        table#filterTable{
            margin-left:150px;
        }
        .filterHidden{

            display:none ;

        }

    </style>
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=7" /><![endif]-->
    <!--[if lt IE 8]><style type="text/css" media="all">@import url("css/ie.css");</style><![endif]-->
    <!--[if IE]><script type="text/javascript" src="js/excanvas.js"></script><![endif]-->

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery.img.preload.js"></script>
    <script type="text/javascript" src="js/jquery.filestyle.mini.js"></script>
    <script type="text/javascript" src="js/jquery.wysiwyg.js"></script>
    <script type="text/javascript" src="js/jquery.date_input.pack.js"></script>
    <script type="text/javascript" src="js/facebox.js"></script>
    <script type="text/javascript" src="js/jquery.visualize.js"></script>
    <script type="text/javascript" src="js/jquery.select_skin.js"></script>
    <script type="text/javascript" src="js/ajaxupload.js"></script>
    <script type="text/javascript" src="js/jquery.pngfix.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>

    <script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
    <style type="text/css">

    </style>
</head>

<body >

<div id="hld">

    <div class="wrapper">		<!-- wrapper begins -->

        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>


            <?php echo $menu; ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>Market Status:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>

                <div style="float:right;margin-right:50px;";>
                <a href="<?php echo "/surewaves_easy_ro/market_status/getCsv" ; ?>">Export as CSV </a></div>

            <div style="float:right;margin-right:50px;";>
            <a href="javascript:clearFilter();">clear Filter </a></div>

        <div style="float:right;margin-right:50px;";>
        <a href="javascript:loadFilterPopUp();">Filter Table </a></div>
</div>


<div class="persist-area">

    <table class="floatingHeader" width="90%" border="1" id="floatingTable" style="border-collapse: collapse; border: 1px solid #DDDDDD;">

        <thead>
        <tr style=" font-weight: bold;"  class="parent">
            <td rowspan="3" colspan="1" style="text-align:center;width:180px;"><div>Market</div></td>
            <td colspan="5" style="text-align:center"><div>Today</div></td>
            <td colspan="5" style="text-align:center"><div>Last Week</div></td>
            <td colspan="5" style="text-align:center"><div>Last Month</div></td>
        </tr>

        <tr style="font-weight: bold;"  class="parent">

            <td style="width:21px;"><div>Ro</div></td>
            <td><div>DEP</div></td>
            <td><div>Ro & DEP</div></td>
            <td><div>Ping</div></td>
            <td><div>Report</div></td>


            <td><div>Ro</div></td>
            <td><div>DEP</div></td>
            <td><div>Ro & DEP</div></td>
            <td><div>Ping</div></td>
            <td><div>Report</div></td>

            <td><div>Ro</div></td>
            <td><div>DEP</div></td>
            <td><div>Ro & DEP</div></td>
            <td><div>Ping</div></td>
            <td><div>Report</div></td>

        </tr>
        </thead>
    </table>


    <table id="dataTable" width="100%" border="1" style="border-collapse: collapse; border: 1px solid #DDDDDD;">

        <thead>
        <tr style=" font-weight: bold;"  class="parent">
            <td rowspan="3" colspan="1" style="text-align:center;width:180px;">Market</td>
            <td colspan="5" style="text-align:center">Today</td>
            <td colspan="5" style="text-align:center">Last Week</td>
            <td colspan="5" style="text-align:center">Last Month</td>
        </tr>

        <tr style="font-weight: bold;"  class="parent">

            <td><div>Ro</div></td>
            <td><div>DEP</div></td>
            <td><div>Ro & DEP</div></td>
            <td><div>Ping</div></td>
            <td><div>Report</div></td>


            <td><div>Ro</div></td>
            <td><div>DEP</div></td>
            <td><div>Ro & DEP</div></td>
            <td><div>Ping</div></td>
            <td><div>Report</div></td>

            <td><div>Ro</div></td>
            <td><div>DEP</div></td>
            <td><div>Ro & DEP</div></td>
            <td><div>Ping</div></td>
            <td><div>Report</div></td>

        </tr>
        </thead>

        <?php foreach( $dataArray as $key=>$row ) { ?>

            <tr class="parent" style=" font-weight: bold;" id="<?php echo $key; ?>">

                <td><div><button id="B<?php echo $key; ?>" style="width:22px;">+</button>       <?php echo $row['details']['market_name']; ?></div></td>

                <td style="width:21px;"><div><?php echo $row['details']['today_ro_count']; ?></div></td>
                <td><div><?php echo $row['details']['today_deployed_count']; ?></div></td>
                <td><div><?php echo $row['details']['today_in_deployed_ro_count']; ?></div></td>
                <td><div><?php echo $row['details']['today_ping_count']; ?></div></td>
                <td><div><?php echo $row['details']['today_report_count']; ?></div></td>

                <td><div><?php echo $row['details']['week_ro_count']; ?></div></td>
                <td><div><?php echo $row['details']['week_deployed_count']; ?></div></td>
                <td><div><?php echo $row['details']['week_in_deployed_ro_count']; ?></div></td>
                <td><div><?php echo $row['details']['week_ping_count']; ?></div></td>
                <td><div><?php echo $row['details']['week_report_count']; ?></div></td>

                <td><div><?php echo $row['details']['month_ro_count']; ?></div></td>
                <td><div><?php echo $row['details']['month_deployed_count']; ?></div></td>
                <td><div><?php echo $row['details']['month_in_deployed_ro_count']; ?></div></td>
                <td><div><?php echo $row['details']['month_ping_count']; ?></div></td>
                <td><div><?php echo $row['details']['month_report_count']; ?></div></td>

            </tr>

            <?php foreach ($row as $fKey=>$value) {
                if($fKey != "details") { ?>

                    <tr class="C<?php echo $key; ?>" style="font-size:11px">
                        <td style="width:176px;word-wrap:break-word;"><div><div><span style="color:blue">[CN]</span> <?php echo $value['channel_name']; ?></div><div style="padding-top:2px;"><span style="color:blue">[DN]</span> <?php echo $value['display_name']; ?></div><div style="padding-top:2px;"><span style="color:blue">[ID]</span>&nbsp; <?php echo $value['tv_channel_id']; ?></div></div>  </td>

                        <td style="width:21px;"><div><?php echo $value['today_ro_channel_status']; ?></div></td>
                        <td><div><?php echo $value['today_deployment_status']; ?></div></td>
                        <td><div><?php echo $value['today_in_deployment_ro_status']; ?></div></td>
                        <td><div><?php echo $value['today_pinging_channel_status']; ?></div></td>
                        <td><div><?php echo $value['today_reporting_channel_status']; ?></div></td>

                        <td><div><?php echo $value['week_ro_channel_status']; ?></div></td>
                        <td><div><?php echo $value['week_deployment_status']; ?></div></td>
                        <td><div><?php echo $value['week_in_deployment_ro_status']; ?></div></td>
                        <td><div><?php echo $value['week_pinging_channel_status']; ?></div></td>
                        <td><div><?php echo $value['week_reporting_channel_status']; ?></div></td>

                        <td><div><?php echo $value['month_ro_channel_status']; ?></div></td>
                        <td><div><?php echo $value['month_deployment_status']; ?></div></td>
                        <td><div><?php echo $value['month_in_deployment_ro_status']; ?></div></td>
                        <td><div><?php echo $value['month_pinging_channel_status']; ?></div></td>
                        <td><div><?php echo $value['month_reporting_channel_status']; ?></div></td>

                    </tr>

                <?php
                }
            }

        }
        ?>
        <tr id="parent">

        </tr>

        <?php

        foreach( $totalColumnCountArray as $total ){

            ?>
            <tr id="1_2" class="parent">
                <th><div>Total </th>
                <th><div><?php echo $total['today_ro_count']; ?></div></th>
                <th><div><?php echo $total['today_deployed_count']; ?></div></th>
                <th><div><?php echo $total['today_in_deployed_ro_count']; ?></div></th>
                <th><div><?php echo $total['today_ping_count']; ?></div></th>
                <th><div><?php echo $total['today_report_count']; ?></div></th>

                <th><div><?php echo $total['week_ro_count']; ?></div></th>
                <th><div><?php echo $total['week_deployed_count']; ?></div></th>
                <th><div><?php echo $total['week_in_deployed_ro_count']; ?></div></th>
                <th><div><?php echo $total['week_ping_count']; ?></div></th>
                <th><div><?php echo $total['week_report_count']; ?></div></th>

                <th><div><?php echo $total['month_ro_count']; ?></div></th>
                <th><div><?php echo $total['month_deployed_count']; ?></div></th>
                <th><div><?php echo $total['month_in_deployed_ro_count']; ?></div></th>
                <th><div><?php echo $total['month_ping_count']; ?></div></th>
                <th><div><?php echo $total['month_report_count']; ?></div></th>
            </tr>
        <?php }
        ?>
    </table>


</div>
</div>

<div id="footer">
    <p class="left"><a href="http://surewaves.com/" TARGET="_blank">surewaves.com</a></p>
</div>

<input type="hidden" value="" id="filterState" />
</div>						<!-- wrapper ends -->

</div>		<!-- #hld ends -->

<script src="/surewaves_easy_ro/js/tableFilter.js"></script>
<script src="/surewaves_easy_ro/js/showPopUp.js"></script>
<script type="text/javascript ">

    $(document).ready(function(){

        $('tr:not(.parent)').hide();
    });

    $('.parent').click(function(){

        var id = $(this).attr('id')  ;

        if( $("#B"+id).text().trim()  == "-" ){

            $("#B"+id).text('+') ;
            $('.C'+id).hide();
        }else{

            $("#B"+id).text(' - ') ;
            $('.C'+id).show();
            $('.filterHidden').hide();
        }


    });

    /* For table header */

    function UpdateTableHeaders() {
        $(".persist-area").each(function() {

            var el             = $(this),
                offset         = el.offset(),
                scrollTop      = $(window).scrollTop(),
                floatingHeader = $(".floatingHeader", this) ;

            if ((scrollTop > offset.top) && (scrollTop < offset.top + el.height())) {
                floatingHeader.css({
                    "visibility": "visible"
                });

            } else {
                floatingHeader.css({
                    "visibility": "hidden"
                });

            };
        });
    }

    // DOM Ready
    $(function() {

        $(window).scroll(UpdateTableHeaders).trigger("scroll");

    });


    function loadFilterPopUp() {

        $("#filterMessage").empty() ;

        var popUpBody =

            ' <label style="margin-left:194px;" > Filter Table Data: </label><hr><table id="filterTable" style="margin-left:122px;margin-top:4px;"><tbody><tr> <td> Ro: </td> ' +
            '<td> <input type="radio" name="ro" value="0" /> </td>' +
            ' <td> 0 &nbsp;&nbsp;</td> ' +
            '<td> <input type="radio" name="ro" value="1" /> </td> <td> 1 &nbsp;&nbsp;</td> ' +
            '<td> <input type="checkbox" name="ro~checkBox" value="1" onchange="validateRadioButton(this)"/> </td><td> NA </td></tr> ' +


            ' <tr> <td> Deployment: </td>  <td> <input type="radio" name="dep" value="0"/> </td> ' +
            '<td> 0 &nbsp;&nbsp;</td>  <td> <input type="radio" name="dep" value="1" /> </td> <td> 1 &nbsp;&nbsp;</td> ' +
            '<td> <input type="checkbox" name="dep~checkBox" value="1" onchange="validateRadioButton(this)"/> </td><td> NA </td></tr> ' +

            '<tr> <td> Ro And Deployment: </td> <td> <input type="radio" name="ro_dep" value="0"/> </td>' +
            ' <td> 0 &nbsp;&nbsp;</td> <td> <input type="radio" name="ro_dep" value="1" /> </td> <td> 1 &nbsp;&nbsp;</td> ' +
            ' <td> <input type="checkbox" name="ro_dep~checkBox" value="1" onchange="validateRadioButton(this)"/> </td><td> NA </td></tr>' +

            '<tr> <td> Ping: </td> <td> <input type="radio" name="ping" value="0" /> </td> ' +
            '<td> 0 &nbsp;&nbsp;</td> <td> <input  type="radio" name="ping" value="1" /> </td> <td> 1 &nbsp;&nbsp;</td>' +
            ' <td> <input type="checkbox" name="ping~checkBox" value="1" onchange="validateRadioButton(this)"/> </td><td> NA </td></tr>' +

            '  <tr> <td> Report: </td> <td> <input type="radio" name="report" value="0" /> </td>' +
            ' <td> 0 &nbsp;&nbsp;</td> <td> <input type="radio" name="report" value="1" /> </td> <td> 1 &nbsp;&nbsp;</td> ' +
            '<td> <input type="checkbox" name="report~checkBox" value="1" onchange="validateRadioButton(this)"/> </td><td> NA </td></tr></tr> ' +
            ' </tbody> </table> </form>' +
            '' +
            '' +
            ' <hr> <input style="margin-left:313px; margin-top: 10px;" id="filter" type="button" value="Done" onclick="beforeFilter();" /></br> <label id="filterMessage" style="margin-left:211px; margin-top:10px;color:green;display: none">Processing.... </label>' +
            '<label id="progressId" style="margin-left:1px; margin-top:20px;color:red;display: none">0</label>' ;

        //showFilterPOPUp( popUpBody ) ;

        $.colorbox({html:popUpBody, height:"40%", width:"40%",

            onComplete:function(){
                var state = getCheckedBoxState() ;
                setLastCheckBoxState( state ) ;
            }
        });

    }

    function validateRadioButton(ref) {

        var checkBoxName = $(ref).attr("name") ;
        var newName      = checkBoxName.split("~") ;

        if( $('input[name="'+checkBoxName+'"]').is(":checked") ){

            $("input[name='"+newName[0]+"']").each(function(i) {

                $(this).attr('disabled', true) ;
                $(this).attr('checked', false) ;
            });

        }else{

            $("input[name='"+newName[0]+"']").each(function(i) {

                $(this).attr('disabled', false) ;

            });

        }
    }


    function beforeFilter(){

        $("#filter").css("display","none");
        $("#filterMessage").css("display","block");

        setTimeout(function(){ filterTable();  }, 100);
    }

    function filterTable(){

        freeTdClass() ;
        //waitCursor();

        $("#dataTable :not(.parent)tr").each(function(){

            var tr = $(this);

            var reqCondition = getFilterCondition( tr ) ;
            var condition ;

            if( reqCondition[0] != "" && reqCondition[0] != null && reqCondition.length > 0 ){

                condition = reqCondition.join(" && ");


                var prepareCondition = "if("+ condition+" ){ true; }else{ false ;}" ;

                if( eval(prepareCondition) ){
                    console.log("IN") ;
                }else{
                    console.log("OUT") ;
                    tr.css("display", "none") ;
                    tr.addClass("filterHidden") ;
                }

            }

        }) ;

        setCheckedBoxState();
        countMarketChilds() ;
        countMarketHeader() ;

        removeWaitCursor();

    }

    function clearFilter(){

        waitCursor() ;

        freeTdClass() ;
        countMarketChilds() ;
        countMarketHeader() ;

        removeWaitCursor()
    }

    function waitCursor(){

    }
    function removeWaitCursor(){

        $.colorbox.close();
    }
</script>



</body>
</html>
