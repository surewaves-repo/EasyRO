<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 7/17/15
 * Time: 12:31 PM
 */
include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<style>
    .ui-datepicker-calendar {
        display: none;
    }
    .fixed {
        position: fixed;
        top:0; left:5%;
        width: 90%;

        background:#fff none repeat scroll 0 0;
        box-shadow: 4px 20px 37px -8px rgba(0,0,0,0.42);
        z-index:9999;
    }
    .change_for_sticky{
        margin-top:150px;
    }
</style>
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<div id="hld">

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

                    <h2>Cancelled Invoices</h2>

                    <form action="<?php echo ROOT_FOLDER ?>/invoice_manager/cancel_download_invoice/" method="post" enctype="multipart/form-data" style="float: right;">
                        <div align="right">
                            <label for="month">Month:</label>
                            <input type="text" readonly="readonly" id="month" name="month" value="<?php echo $month?>"/>
                            &nbsp;&nbsp;

                            <input type="submit" class="submit" id="" value="Search" onclick="return check_form()">
                        </div>
                    </form>

                </div>

                <div class="tbl_hdr block_content">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <th style="width: 5%"><input type="checkbox" id="selectAll"></th>
                            <th style="width: 30%">Invoice No.</th>
                            <th style="width: 20%">Internal RO No.</th>
                            <th style="width: 10%">External RO No.</th>
                            <th style="width: 8%">Client</th>
                            <th style="width: 8%">Buyer</th>
                            <th style="width: 8%">Invoice Amount</th>
                            <th style="width: 10%">Remarks</th>
                            <!--<th style="width: 11%">Status</th>-->
                        </tr>
                        </tr>
                    </table>
                </div>

            </div>

                <div class="block_content all_contents">
				<form name="invoiceDownload" action="<?php echo ROOT_FOLDER ?>/invoice_manager/downloadAllInvoice/" id="invoiceDownload" method="POST" target="_blank">

                    <table cellpadding="0" cellspacing="0" width="100%">

                        <?php foreach($invoice_list as $invoice){
                                /*if($invoice['split_by'] == 0){
                                    $invoice_no = explode(",",$invoice['id']);
                                    $invoice['id'] = $invoice_no[0];
                                    $invoice_name = $invoice_no[0];

                                }else if($invoice['split_by'] == 2){
                                    $invoice_no = str_replace(",","~",$invoice['id']);
                                    $invoice_number$invoice_number = explode(",",$invoice['id']);
                                    $invoice_name = $invoice_number[0];
                                    $invoice['id'] = $invoice_no;

                                }*/
                            $invoice_no = "INV_".str_replace('/','_',$invoice['internal_ro'])."_".$invoice['invoice_number'];
								
                            ?>
                            <tr>
                                 <td style="width: 5%"><input type="checkbox" name="invoice_no[]" data-for="<?php echo $invoice['ro_id'] ?>" value="<?php echo $invoice['invoice_number']; ?>" class="<?php echo $invoice['invoice_number']; ?> invoices"></td>
                                <td style="width: 30%"><?php echo $invoice['alias_invoice_number'];?></td>
                                <td style="width: 20%"><?php echo $invoice['internal_ro'];?></td>
                                <td style="width: 10%"><?php echo $invoice['cust_ro'];?></td>
                                <td style="width: 8%"><?php echo $invoice['client_name'];?></td>
                                <td style="width: 8%"><?php if($invoice['agency_name'] != 'Surewaves') { echo $invoice['agency_name']; } else{ echo $invoice['client_name']; }?></td>
                                <td style="width: 8%"><?php echo $invoice['invoice_amount'];?></td>
                                <td style="width: 10%">
                                    <?php
										$str = '';
										$split = str_split($invoice['split_criteria']);
										if($split[0] == 0){
											$str = 'Unsplit';
										}else if($split[0] == 1){
											if($split[1] == 1){
												$str = 'Splitted by Market and Brand';
											}else if($split[2] == 1){
												$str = 'Splitted by Market and Content';
											}else{
												$str = 'Splitted by Market';
											}
											
										}
										echo $str;
									?>
                                </td>
                                <!--<td>
                                    <?php /*if($invoice['mail_sent'] == 1) {*/?>
                                        <img src="<?php /*echo ROOT_FOLDER */?>/images/mail_sent.png" onclick="javascript:mail_sent('<?php /*echo $invoice['id'] */?>')" style="cursor: pointer" />
                                    <?php /*} else { */?>
                                        <img src="<?php /*echo ROOT_FOLDER */?>/images/mail_not_sent.png" onclick="javascript:mail_sent('<?php /*echo $invoice['id'] */?>')" style="cursor: pointer" />
                                    <?php /*} */?>

                                    <?php /*if($invoice['money_received'] == 1) {*/?>
                                        <img src="<?php /*echo ROOT_FOLDER */?>/images/money_sent.png" onclick="javascript:money_received('<?php /*echo $invoice['id'] */?>')" style="cursor: pointer"/>
                                    <?php /*} else { */?>
                                        <img src="<?php /*echo ROOT_FOLDER */?>/images/money_not_sent.png" onclick="javascript:money_received('<?php /*echo $invoice['id'] */?>')" style="cursor: pointer"/>
                                    <?php /*} */?>

                                </td>-->
                            </tr>
                        <?php } ?>
                    </table>
					</form>
                    <input type="button" class="_button" value="Download" onclick="javascript:download_invoice_pdf()">

                   <!-- <div class="paggination right">
                        <?php /*echo $page_links */?>
                    </div>		<!-- .paggination ends -->

                </div>

                <div class="bendl"></div>
                <div class="bendr"></div>


        </div>		<!-- .block ends -->

    </div>
</div>



<script type="text/javascript" language="javascript">

    $(document).ready(function() {
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

        $('#selectAll').click(function(event) {  //on click
            if(this.checked) { // check select status
                $('.invoices').each(function() { //loop through each checkbox
                    this.checked = true;  //select all checkboxes with class "checkbox1"
                });
            }else{
                $('.invoices').each(function() { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
                });
            }
        });

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
    });


    function download_invoice_pdf(){

        /*var invoice_details = $('.invoices:checked').val();
        var invoice_info = invoice_details.split("_");
        var ro_id = invoice_info[0];
        var invoice_id = invoice_info[1];
        var split_by = invoice_info[2];
        var month = $("#month" ).val();

        if($('.invoices:checked').length > 1){

            var status = 1;
            var invoice_ids = '';
            $('.invoices:checked').each(function(){

                if(invoice_ids == ''){
                    invoice_ids = $(this).attr('class').split(' ')[0];
                }else{
                    invoice_ids = invoice_ids + "_" + $(this).attr('class').split(' ')[0];
                }

                var ro_no = $(this).attr('data-for');
                if(ro_no != ro_id){
                    alert("Please select Invoices from same RO");
                    status = 0;
                    return false;
                }
            });

            //alert(invoice_ids);
            if(status != 0){
                window.location = "<?php echo ROOT_FOLDER ?>/invoice_manager/download_invoice_zipped/"+ro_id+"/"+invoice_ids+"/"+"yes"+"/"+month;
            }

        } else {
            window.location = "<?php echo ROOT_FOLDER ?>/invoice_manager/download_invoice_pdf/"+ro_id+"/"+invoice_id+"/"+split_by+"/"+month;
        }*/
		document.invoiceDownload.submit();

    }
    function mail_sent(invoice_id){
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/invoice_manager/record_mail_sent',
            async: false,
            data: {
                invoice_id: invoice_id
            },
            //dataType: 'json',
            success: function(data){
                if(data != ''){
                    alert("Money received marked successfully");
                }else{

                }
            }
        });
    }
    function money_received(invoice_id){
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/invoice_manager/record_money_received',
            async: false,
            data: {
                invoice_id: invoice_id
            },
            //dataType: 'json',
            success: function(data){
                if(data != ''){
                    alert("Mail sent marked successfully");
                }else{

                }
            }
        });
    }
</script>
