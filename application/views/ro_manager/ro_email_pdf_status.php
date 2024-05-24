<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<body  >

<div id="hld">

    <div class="wrapper">		<!-- wrapper begins -->
        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>


            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>


            <?php echo $menu ?>

            <p class="user">Hello, <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->

        <div class="block" >

            <label id="errorLabel" style="margin-left: 10px;color: red;display: none;"></label>

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <label style="font-weight: bold;float: left;margin-right:15px;">Email And PDF Status:</label>
                <div style="float: left;" >

                    <label>Start Date:</label>
                    <input  type="text" readonly="readonly" id="startDate" name="startDate" style="width:125px;height:21px;"/>

                    <label style="margin-left: 20px;">End Date:</label>
                    <input  type="text" readonly="readonly" id="endDate" name="endDate" style="width:125px;height:21px;"/>&nbsp;&nbsp;

                    <label style="margin-left: 20px;">Ro:</label>
                    <select name="roSelectBox" id="roSelectBox" style="width:370px;height:21px;">
                        <option value="">-</option>
                    </select>&nbsp;&nbsp;

                    <input id="getStatus" type="button" style="margin-left: 20px;" value="Get Status" />

                </div>

                <!-- form ends -->

            </div>		<!-- .block_head ends -->

            <div id= "blockContent" class="block_content">



            </div>	<!-- .block_content ends -->

            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>		<!-- .block ends -->
        <form id="viewPdf" action="<?php echo base_url('report/getPdfPrivateUrl'); ?>" method="post" target="_blank">
            <input type="hidden" id="fileName" name="fileName" value="" >
        </form>
    </div>  <!--wrapper ends-->
</div> 	<!--hld ends -->

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script src="/surewaves_easy_ro/assets/js/waitCursor.js"></script>

<script type="text/javascript">

$(document).ready(function (){

    var currentDate = new Date() ;

    $( "#startDate,#endDate" ).datepicker({

        dateFormat: "d MM yy",
        showOtherMonths: true,
        selectOtherMonths: true,
        yearRange: '-1:+1',
        maxDate: "5D"

    }).datepicker("setDate", currentDate);

    $("#startDate, #endDate").trigger('change');
});

$("#startDate, #endDate").change(function(){

    var startDate = $("#startDate").val() ;
    var endDate   = $("#endDate").val() ;

    if(validateForm( startDate, endDate ) ){

        var data = "startData="+startDate+"&endDate="+endDate;

        $.ajax({
            type: "POST",
            url:  "<?php echo base_url('report/getRosForEmailAndPdfStatus'); ?>",
            data: data,
            beforeSend: function() {
                ajaxindicatorstart('loading data.. please wait..');
            },
            complete: function() {
                ajaxindicatorstop();
            },
            success: function(response){

                $('#roSelectBox').find('option').remove();
                $('#errorLabel').css('display', 'none') ;

                if( response.trim() != "No_Ros" ){

                    var object  = $.parseJSON(response);

                    for(var n=0; n< object.ros.length; n++){

                        $("#roSelectBox").append('<option value='+object.ros[n].ro.trim()+'><div style="width:370px;">'+object.ros[n].ro.trim()+'</div></option>');

                    }
                }else{

                    $('#errorLabel').css("display", "block") ;
                    $("#errorLabel").html("No Ros found..!");
                }
            }

        });

    }

});

$("#getStatus").click(function(){

    var startDate = $("#startDate").val() ;
    var endDate   = $("#endDate").val() ;
    var roNumber  = $("#roSelectBox option:selected").text();

    var data = "internalRoNumber=" + roNumber ;
    var count = 1 ;

    if(validateForm( startDate, endDate ) && validateRos() ){

        $.ajax({
            type: "POST",
            url:  "<?php echo base_url('report/getRoDetails'); ?>",
            data: data,
            beforeSend: function() {
                ajaxindicatorstart('loading data.. please wait..');
            },
            complete: function() {
                ajaxindicatorstop();
            },
            success: function(response){

                emptyHtml( "blockContent" ) ;

                if( response.trim() != "Not_Set" ) {

                    $('#errorLabel').css('display', 'none') ;

                    appendHtml( "blockContent", getBuildBodyHeader()) ;

                    // Parsing json
                    $.each(JSON.parse(response), function (customerId, customerObject) {

                        $.each(customerObject, function (index, customerValue) {

                            var returnHtml = getBuildBodyData(count, customerValue.Network,
                                customerValue.Channels, customerValue.mailSent,
                                customerValue.PdfLink, customerValue.MailCount,
                                customerValue.buttonStatus, roNumber + '#' + customerId+'#'+customerValue.Network, customerValue.buttonValue);

                            appendHtml("blockContent", returnHtml);

                            count++;

                        });
                    });

                }else{

                    $('#errorLabel').css("display", "block") ;
                    $("#errorLabel").html("No data found..!");
                }
            }

        });

    }

});

function validateRos(){

    var ro = $("#roSelectBox option:selected").text() ;

    if( ro == ""){

        $('#errorLabel').css("display", "block") ;
        $("#errorLabel").html("Please select ro..!");

        return false;

    }else{

        $('#errorLabel').css("display", "none") ;
        return true;
    }
}

function validateForm( startDate, endDate ){

    var startDateArr = startDate.split(" ");
    var dStart = new Date(startDateArr[2], get_month(startDateArr[1]), startDateArr[0]);

    var endDateArr = endDate.split(" ");
    var dEnd = new Date(endDateArr[2], get_month(endDateArr[1]), endDateArr[0]);

    if( dEnd.getTime() < dStart.getTime() ) {

        $('#errorLabel').css("display", "block") ;
        $("#errorLabel").html('End date should greater than start date.! ');

        return false ;

    }else if((dEnd.getTime() -  dStart.getTime())/86400000 > 90){

        $('#errorLabel').css("display", "block") ;
        $("#errorLabel").html('Date duration should between 3 months');

        return false ;

    }else{

        $('#errorLabel').css('display', 'none') ;
        return true ;

    }

}
function get_month (str_val) {
    if ( str_val == 'January' ) {
        return 0;
    } else if ( str_val == 'February' ) {
        return 1;
    } else if ( str_val == 'March' ) {
        return 2;
    } else if ( str_val == 'April' ) {
        return 3;
    } else if ( str_val == 'May' ) {
        return 4;
    } else if ( str_val == 'June' ) {
        return 5;
    } else if ( str_val == 'July' ) {
        return 6;
    } else if ( str_val == 'August' ) {
        return 7;
    } else if ( str_val == 'September' ) {
        return 8;
    } else if ( str_val == 'October' ) {
        return 9;
    } else if ( str_val == 'November' ) {
        return 10;
    } else if ( str_val == 'December' ) {
        return 11;
    }
}

function appendHtml( id, html ){

    $("#"+ id ).append(html) ;
}

function emptyHtml( id ){

    $("#"+ id ).empty() ;

}
function getBuildBodyHeader(){

    var returnHtml = ' <div id="DataDiv" style="margin-left: auto;margin-right: auto;width: 100%; margin-top: 30px;font-weight: bold;">' +
        '<div style="float: left;width:62px;margin-left: 40px;">Sr no.</div>' +
        '<div style="float: left;width:195px;">Network Name</div>' +
        '<div style="float: left;width:222px;">Channel Name</div>' +
        '<div style="float: left;width:156px;margin-left: 10px;">Email Status</div>' +
        '<div style="float: left;width:156px;">Pdf Status</div>' +
        '<div style="float: left;width:180px;">Revision No</div>' +
        '</div>' ;

    return returnHtml ;

}
function getBuildBodyData( srNo, networkName, channels, mailStatus, pdfStatus, revision, buttonVisibility, valueToSend, buttonValue ){

    var pdfDetails ;
    var pdfColor = "";
    var mailColor = "" ;
    var baseUrl = "<?php echo base_url('report/getPdfPrivateUrl/'); ?>" ;

    if( pdfStatus != "-" && pdfStatus != "Not Available" ){

        pdfDetails = " <a href='#' onclick='getPdf(\""+pdfStatus+"\");' >View Pdf</a> ";

    }else{

        pdfDetails = pdfStatus ;
        pdfColor = "red" ;
    }
    if( mailStatus.trim() == "Mail Sent" ){

        mailColor = "green" ;

    }else if( mailStatus.trim() == "Not Sent" ){

        mailColor = "red" ;
    }

    var customerId = valueToSend.split("#") ;

    var returnHtml = ' <br style="margin-top:-13px;margin-bottom:0px;\">' +
        '<div id="DataDiv" style="margin-left: auto;margin-right: auto;width: 100%; margin-top: 30px;height: 40px;">' +
        '<div style="float: left;width:62px;margin-left: 40px;color:saddlebrown;">'+ srNo +'</div>' +
        '<div style="float: left;width:195px;color:saddlebrown;">'+ networkName +'</div>' +
        '<div style="float: left;width:223px;overflow:auto;height:65px;color:saddlebrown;">'+ channels +'</div>' +
        '<div style="float: left;width:156px;margin-left: 10px;color:'+mailColor+'">'+ mailStatus +'</div>' +
        '<div style="float: left;width:156px; color:'+pdfColor+'">'+ pdfDetails +'</div>' +
        '<div style="float: left;width:115px;">'+ revision +'</div>' +
        '<div style="float: left;width:180px;"><input id="'+customerId[1]+'" type="button" value="'+buttonValue+'" onClick="regenerateCustomer(\''+valueToSend+'\');" '+  buttonVisibility +'></div>' +
        '</div> ' ;

    return returnHtml ;
}

function regenerateCustomer(valueToSend){

    var data = "internalRoAndCustomerId=" + valueToSend ;
    var customerId = valueToSend.split("#") ;

    $.ajax({
        type: "POST",
        url:  "<?php echo base_url('ro_manager/regeneratePDF'); ?>",
        data: data,
        success: function(response) {

            if( response.trim() == "TRUE" ){

                $("#"+ customerId[1]).val("Processing..") ;
                $("#"+ customerId[1]).prop('disabled', true);
                $('#errorLabel').css('display', 'none') ;

            }else{

                $('#errorLabel').css("display", "block") ;
                $("#errorLabel").html('Cannot Re Generate..!');
            }
        },
	error:function(  jqXHR,textStatus,errorThrown){
	  console.log(jqXHR);
	  console.log(textStatus);
	  cosnole.log(errorThrown);
	}

    });
}
    function getPdf( fileName ){

        $("#fileName").val( fileName ) ;
        $("#viewPdf").submit() ;
    }


</script>
</body>
