<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 7/17/15
 * Time: 12:30 PM
 */
include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<style>
    .ui-datepicker-calendar {
        display: none;
    }
    #cancel_loader	{
        background: none repeat scroll 0 0 #FFFFFF;
        border-radius: 10px;
        border: 5px solid #A2A2A2;
        display: block;
        font-weight: bold;
        height: 100px;
        left: 38%;
        padding-top: 15px;
        position: absolute;
        text-align: center;
        top: 45%;
        width: 280px;
        z-index: 9999;
    }
    #overlay {
        background-color: #000;
        filter:alpha(opacity=60); /* IE */
        opacity: 0.6; /* Safari, Opera */
        -moz-opacity:0.66; /* FireFox */
        z-index: 20;
        height: 200%;
        width: 100%;
        background-repeat:no-repeat;
        background-position:center;
        position:absolute;
        top: 0px;
        left: 0px;
    }
   .fixed {
      position: fixed;
      top:0; left:5%;
      width: 90%;

      background:#fff none repeat scroll 0 0;
      box-shadow: 4px 20px 37px -8px rgba(0,0,0,0.42);
      z-index:9999;
     }
    .symbol {
        font-size: 0.9em;
        font-family: Times New Roman;
        border-radius: 1em;
        padding: .1em .6em .1em .6em;
        font-weight: bolder;
        color: white;
        background-color: #4E5A56;
    }

    .del_stat,.icon-info { background-color: #3229CF; }
    .icon-error { background: #e64943; font-family: Consolas; }
    .icon-tick { background: #13c823; }
    .icon-excl { background: #ffd54b; color: black; }

    .icon-info:before { content: 'i'; }
    .icon-error:before { content: 'x'; }
    .icon-tick:before { content: '\002713'; }
    .icon-excl:before { content: '!'; }
    .del_stat:before{
        content:'X';
        padding: .4em .8em !important;
    }
    .del_stat{
           cursor:pointer;
            padding: .4em .8em !important;
    }

    .notify {
        background-color:#e3f7fc;
        color:#555;
        border:.1em solid;
        border-color: #8ed9f6;
        border-radius:10px;
        font-family:Verdana,Arial,sans-serif;
        font-size:1em;
        padding:10px 10px 10px 10px;
        margin:10px;
        cursor: default;
    }
    .change_for_sticky{
        margin-top:150px;
    }
</style>
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<div id="hld">

    <div id="cancel_loader" style="display:none;"><img src='<?php echo ROOT_FOLDER ?>/images/loader_full.GIF' height="50" width="50"/><br><br>Generating Invoice ...</div>
    <div class="wrapper">		<!-- wrapper begins -->



        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->

        <div class="block">
		<div class="div_cnt">
                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2>Generate Invoice</h2>
		    <div  style="float:left;margin-top:15px;margin-left:527px;"><input type="button" class="_button generate" id="" value="GENERATE" onclick="" /></div>
                    <form action="<?php echo ROOT_FOLDER ?>/invoice_manager/generate_invoice/" method="post" enctype="multipart/form-data" style="float: right;">
                        <div align="right">
                            <label for="month">Month:</label>
                            <input type="text" readonly="readonly" id="month" name="month" value="<?php echo $month?>"/>
                            &nbsp;&nbsp;

                            <input type="submit" class="submit mid" id="" value="Get ROs" onclick="return check_form()">
                        </div>
                    </form>

                </div>

                
		    <div class="tbl_hdr block_content">
			 <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <th style="width: 4%">#</th>
                            <th style="width: 20%">Internal RO</th>
                            <th style="width: 10%">External RO</th>
                            <th style="width: 10%">Client</th>
                            <th style="width: 10%">Buyer</th>
                            <th style="width: 10%">Invoice Amount</th>
                            <th style="width: 10%">Billing Cycle</th>
                            <th style="width: 16%">Invoice Status</th>
                            <th style="width: 10%">Campaign Status</th>
                        </tr>
		     </table>
		    </div>
		</div>
		    <div class="block_content all_contents">
		    <form action="" name="generate_split_data" id ="generate_split_data" method="POST">	
                    <table cellpadding="0" cellspacing="0" width="100%">

                        <?php  
				foreach($ro_list as $ro){?>
                            <tr>
				<td style="width: 4%">
					<?php
						$status  = '';
						if($ro['status'] != 0){ 
							$status = ' disabled="disabled"';
						}

                    $amount_after_commission = $ro['gross'] - $ro['agency_com'] ;
                    $service_tax = $amount_after_commission * 15.00/100 ;
                    $invoice_amount = $amount_after_commission + $service_tax;

					?>
					<input type="checkbox" name="ro_id[]"  <?php echo $status;  ?>  value="<?php echo $ro['id']; ?>" class="chk_box"  id="chk_box_<?php echo $ro['id'];  ?>"/>
				</td>
                                <td style="width: 20%"><?php echo $ro['internal_ro'];?></td>
                                <td style="width: 10%"><?php echo $ro['cust_ro'];?></td>
                                <td style="width: 10%"><?php echo $ro['client'];?></td>
                                <td style="width: 10%"><?php if($ro['agency'] != 'Surewaves') { echo $ro['agency']; } else{ echo $ro['client']; }?></td>
                                <td style="width: 10%"><?php echo round($invoice_amount,2);?></td>
                                <td style="width: 10%"><?php echo $ro['billingCycle'] ;?></td>
                                <td style="width: 16%" id="status_<?php echo $ro['id'];  ?>">


					<?php 
						if($ro['status'] == 0){
							echo '<b>NEW</b>';
                            echo '<img src="'.ROOT_FOLDER.'/images/processing.gif" id="processing_'.$ro['id'].'" style="display:none;"/>';
						}
                        else  if($ro['status'] == 2 || $ro['status'] == 4){
                            //echo '<b>PENDING</b>'; ?>

                            <img src="<?php echo ROOT_FOLDER ?>/images/processing.gif" id="processing_<?php echo $ro['id'];  ?>"/>

                        <?php }

                        else  if($ro['status'] == 1){
                                        echo '<b>GENERATED </b>';
                                            if($ro['collection_count'] == 0){
                                                echo '<span class="del_stat symbol" data-for="'.$ro['id'].'" onclick="cancel_invoice_for_ro(this)"></span>';
                                            }
                                } ?>
				</td>
                                <td style="width: 10%"><?php echo $ro['campaignStatus'] ;?></td>
                            </tr>
                        <?php } ?>
                    </table>
			<input type="hidden" name="hid_split_by_market" id="hid_split_by_market" value="0" />
			<input type="hidden" name="hid_split_by_brand" id="hid_split_by_brand" value="0" />
			<input type="hidden" name="hid_split_by_content" id="hid_split_by_content" value="0" />
		    </form>
                    <div id="dialog" title="Generate" style="display: none">
                        <!--<input id="split_by_market" type="checkbox" name="" checked disabled value="0">&nbsp;Market</br>-->
                        <!--<p>RO will be split by Markets</p>-->
			<div id="split_opt" style="margin-left: 64px; margin-bottom: 10px;">
                                        <input id="unsplit_opt" type="radio" checked name="unsplit_opt" class="ro_split_unsplit" value="unsplit">&nbsp;Un-Split&nbsp;&nbsp;
                                        <input id="split_opt" type="radio" name="split_opt" class="ro_split_unsplit" value="split">&nbsp;Split
                        </div>
			
			<div id="split_choose" style="display:none">
				<div class="notify" ><span class="symbol icon-info"></span> By Default every RO will be split by Markets !</div> 
                        	<div style="margin-top: 12px; margin-left: 13px; margin-bottom: 11px;">
					<input id="split_by_more" type="checkbox" name="" value="0" onchange="javascript:switch_control()">&nbsp;(Click here to Split market by Brand or Content.)
				</div>

                        	<div id="split_more" style="display: none;margin-left: 62px; margin-bottom: 10px;">
                            		<input id="split_by_brand" type="radio" checked name="split_by"  value="0">&nbsp;Brand&nbsp;&nbsp;
                            		<input id="split_by_content" type="radio" name="split_by"  value="0">&nbsp;Content</br>
                        	</div>

			</div>
				<input type="button" value="Save" id="close_dialogue" class="_button" style="font-family:Titillium800,Trebuchet MS,Arial,sans-serif; margin-left:83px;margin-top:20px;">

                    </div>

                    <!--<div class="paggination right">
                        <?php /*echo $page_links */?>
                    </div>	-->	<!-- .paggination ends -->

                </div>

                <div class="bendl"></div>
                <div class="bendr"></div>
        </div>		<!-- .block ends -->

        <div id="overlay" style="display: none;"></div>
    </div>
</div>

<script type="text/javascript" language="javascript">

    $(document).ready(function(){
	var stickyOffset = $('.div_cnt').offset().top;
	$(window).scroll(function(){
  		var sticky = $('.div_cnt'),
      		scroll = $(window).scrollTop();

        if (scroll >= stickyOffset){
            sticky.addClass('fixed');
            $('.all_contents').addClass('change_for_sticky');
        }else{
            sticky.removeClass('fixed');
            $('.all_contents').removeClass('change_for_sticky');
        }
	});

    setInterval(checkGenerationStatus, 30000);
   
    $( "#month" ).datepicker({
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
    });
    $('.generate').click(function(){
/*		if($('.chk_box').length > 0){
			alert("Please list the Ro(s).");
			return false;
		}*/
		if($('.chk_box:checked').length <= 0){
                	alert("Please Select Or list any Ro(s).");
                	return false;
        	}
        	/*if($('.chk_box:disabled').length == $('.chk_box:checked').length){
                	alert("Please Select any new Ro(s)");
                	return false;
        	}  */

                $( "#dialog" ).dialog({
                    modal: true
                });
		$(".ro_split_unsplit").prop('checked', false);
		$('#split_choose').hide();
                $('#split_by_more').prop('checked', false);
                $('#split_more').hide();
                $("input[name='split_by']").prop('checked', false);
		$("#unsplit_opt").prop('checked', true);
                $('#hid_split_by_brand').val('0');
                $('#hid_split_by_content').val('0');
                $('#hid_split_by_market').val('0');


    });

    $('#close_dialogue').click(function(){
	if($(".ro_split_unsplit:checked").val() === "unsplit"){
		//alert("unsplit checked");
		$('#hid_split_by_brand').val('0');
                $('#hid_split_by_content').val('0');
                $('#hid_split_by_market').val('0');
	}else{
		$('#hid_split_by_market').val('1');	
		if($('#split_by_brand').is(':checked')){
		 	$('#hid_split_by_brand').val('1');
		}	
		if($('#split_by_content').is(':checked')){
			$('#hid_split_by_content').val('1');
		}
	}
	//	console.log($("#generate_split_data").serialize());
	$( "#dialog" ).dialog( "close" );
	$.ajax({
            type: 'POST',
	    dataType: 'json',
            //url: '/surewaves_easy_ro/invoice_manager/generate_invoice_for_ro',
            url: '/surewaves_easy_ro/invoice_manager/post_InvoiceDataForGeneration',
            data: $("#generate_split_data").serialize()+"&month="+($(".hasDatepicker").val()).trim(),//Array(),
            beforeSend : function(){
                $('#overlay').show();
                $('#cancel_loader').show();
            },
            //dataType: 'json',
            success: function(data){
		$('#overlay').hide();
                $('#cancel_loader').hide();

		if(data.status == "success"){
			console.log("inside success");
			var ro_ids = (data.ro_id).split(",");
			for(var key in ro_ids){
				//console.log(data.internal_ro[key]);
				$(":checkbox[value="+ro_ids[key]+"]").attr("disabled", true);
                //$('#processing_'+ro_ids[key]).show();
                $("#status_"+ro_ids[key]).html('<img src="<?php echo ROOT_FOLDER ?>/images/processing.gif" id="processing_'+ro_ids[key]+'" />');

				//$("#status_"+ro_ids[key]).html('<b>GENERATED </b> <span class="del_stat symbol" data-for="'+ro_ids[key]+'" onclick="cancel_invoice_for_ro(this)"></span>');
				//$("#status_"+ro_ids[key]).html('<b>PENDING</b>');
            }
		}
            }
        });

    });

    function checkGenerationStatus() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/surewaves_easy_ro/invoice_manager/checkGenerationStatus',
            data: {
					month:$("#month").val()
				},//Array(),
            success: function(data){
                if(data != 0){
                    //var ro_ids = data.ro_id;
                    for(var key in data){
                        var ro_id = data[key].ro_id;
                        if(data[key].is_generated == 1){
                            $('#processing_'+ro_id).hide();
                            $("#status_"+ro_id).html('<b>GENERATED </b> <span class="del_stat symbol" data-for="'+ro_id+'" onclick="cancel_invoice_for_ro(this)"></span>');
                        }
                        else{
                            $('#processing_'+ro_id).show();
                        }
                    }
                }
            }
        });
    }

    $(".ro_split_unsplit").click(function(){
		$(".ro_split_unsplit").prop('checked', false);
		$(this).prop('checked', true);
//		alert($(this).val());
		if($(this).val() === "split"){
			$('#split_choose').show();
			$('#hid_split_by_brand').val('0');
			$('#hid_split_by_content').val('0');
			$('#hid_split_by_market').val('0');
			
			
		}else{
			$('#split_choose').hide();
			$('#split_by_more').prop('checked', false);
			$('#split_more').hide();
			$("input[name='split_by']").prop('checked', false);		
		}
	
    });
   /* $(".del_stat").click(function(){
        var roId = $(".del_stat").attr('data-for');
	console.log();
        $.ajax({
			type: 'POST',
			dataType: 'json',
			url: '/surewaves_easy_ro/invoice_manager_temp/cancel_invoice_for_ro',
			data: ro_id = roId,
			beforeSend : function(){
				$('#overlay').show();
				$('#cancel_loader').show();
			},            
			success: function(data){
				$('#overlay').hide();
				$('#cancel_loader').hide();

				if(data.status == "success"){
					console.log("inside success");							
                    //console.log(data.internal_ro[key]);
                    			$(":checkbox[value="+roId+"]").attr("disabled", false);
					$(":checkbox[value="+roId+"]").attr("checked", false);
                    			$("#status_"+roId).html('NEW');
							
				}
			}	
	});
     });*/
    
   });// -END OF DOCUMENT READY
    function switch_control(){
        if($('#split_by_more').is(":checked")){
            $('#split_more').show();
            $('#split_by_brand').prop('disabled', false);
            $('#split_by_content').prop('disabled', false);
        }else{
            $('#split_more').hide();
            $('#split_by_brand').prop('disabled', true);
            $('#split_by_content').prop('disabled', true);
        }
    }

    function check_form(){
        var month = $("#month").val();

        if(month == ""){
            alert("Please select a Month");
            $("#month").focus();
            return false;
        }
    }
    function cancel_invoice_for_ro(e){
	var roId = $(e).attr('data-for');
        $.ajax({
			type: 'POST',
			dataType: 'json',
			url: '/surewaves_easy_ro/invoice_manager/cancel_invoice_for_ro',
			data: "ro_id="+roId+"&month="+($(".hasDatepicker").val()).trim(),
			beforeSend : function(){
				$('#overlay').show();
				$('#cancel_loader').show();
			},            
			success: function(data){
				$('#overlay').hide();
				$('#cancel_loader').hide();

				if(data.status == "success"){
					console.log("inside success");							
                    //console.log(data.internal_ro[key]);
                    $(":checkbox[value="+roId+"]").attr("disabled", false);
					$(":checkbox[value="+roId+"]").attr("checked", false);
                    $("#status_"+roId).html('<b>NEW</b>');
							
				}
			}	
		});
    }
    function generate_invoice_for_ro(ro_id){

        /*$('#toggle_unsplit_'+ro_id).prop('disabled',true);
        $('#toggle_split_'+ro_id).prop('disabled',true);*/

        var month = $("#month" ).val();
        if($('#toggle_split_'+ro_id).is(":checked")){
            var split_by_market = 1;

            if($('#split_by_more').is(":checked")){
                if($('#split_by_brand').is(":checked")){
                    var split_by_brand = 1;
                }else{
                    var split_by_brand = 0;
                }
                if($('#split_by_content').is(":checked")){
                    var split_by_content = 1;
                }
                else{
                    var split_by_content = 0;
                }
            }else{
                var split_by_brand = 0;
                var split_by_content = 0;
            }
        }

        if($('#toggle_unsplit_'+ro_id).is(":checked")){
            var split_by_market = 0;
            var split_by_brand = 0;
            var split_by_content = 0;
        }



        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/invoice_manager/generate_invoice_for_ro',
            data: {
                ro_id: ro_id,
                split_by_market: split_by_market,
                split_by_brand: split_by_brand,
                split_by_content: split_by_content,
                month:month
            },
            beforeSend : function(){
                $('#overlay').show();
                $('#cancel_loader').show();
            },
            //dataType: 'json',
            success: function(data){
                $('#overlay').hide();
                $('#cancel_loader').hide();
                /*if(data != ''){
                    alert('Generated Sucessfully');
                    //$('#generate_'+ro_id).prop('disabled',true);
                    $('#generate_'+ro_id).replaceWith( "Generated" );
                }else{

                }*/
            }
        });

    }

    /*$('.generate').click(function(){
        var status = this.value;
        if(status == 1){
            $(function() {
                $( "#dialog" ).dialog({
                    modal: true
                });
            });
        }/*else{
            $('#split_more').hide();
            $('#split_by_more').prop('checked', false);
            $('#split_by_brand').prop('checked', false);
            $('#split_by_content').prop('checked', false);

            $('#split_by_brand').prop('disabled', true);
            $('#split_by_content').prop('disabled', true);
        }
    });

    $('#close_dialogue').click(function(){
        $( "#dialog" ).dialog( "close" );
    });*/

</script>
