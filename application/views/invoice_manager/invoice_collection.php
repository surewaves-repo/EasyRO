<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 8/25/15
 * Time: 5:00 PM
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
</style>
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.colorbox.js"></script>
<div id="hld">
    <div id="cancel_loader" style="display:none;"><img src='<?php echo ROOT_FOLDER ?>/images/loader_full.GIF' height="50" width="50"/><br><br>Searching Invoices ...</div>
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


        <div class="block_head">
            <div class="bheadl"></div>
            <div class="bheadr"></div>

            <h2>Invoice Collection</h2>
            
            <span>
                <input type="button" style="width: 20%; margin-left: 850px;background: rgba(0, 0, 0, 0) url('../images/excel.png') no-repeat scroll 0 0;cursor:pointer;width:4%;border:none;height:40px;" id="excel_download" name="excel_download" >
            </span>
        </div>		<!-- .block_head ends -->

        <div class="block_content">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td>
                        <span>Search By:</span>&nbsp;&nbsp;
                    </td>

                    <td>
                        <input type="radio" name="invoice_by" value="by_agency" checked>

                        <label for="month">Month</label>
                        <input type="text" readonly="readonly" id="month" name="month" value="" />
                        &nbsp;&nbsp;
                    </td>

                    <td>
                        <span>Agency</span>&nbsp;&nbsp;
                        <select id="sel_agency" name="sel_agency" style="width:220px;" onchange="javascript:get_clients_for_invoice(this.value)">
                            <?php /*foreach($all_agencies as $agency){*/?><!--
                                <option value="<?php /*echo $agency['id']*/?>"><?php /*echo $agency['agency_name']*/?></option>
                            --><?php /*}*/?>
                        </select>
                    </td>

                    <td>
                        <span>Client</span>
                        <select id="sel_client" name="sel_client" style="width:220px;">
                            <?php /*foreach($all_agencies as $agency){*/?><!--
                                <option value="<?php /*echo $agency['id']*/?>"><?php /*echo $agency['agency_name']*/?></option>
                            --><?php /*}*/?>
                        </select>
                    </td>

                </tr>

                <tr>
                    <td></td>

                    <td>
                        <input type="radio" name="invoice_by" value="by_invoice_no">
                        <span>Invoice No.</span>
                        <input type="text" id="search_inv" value="" placeholder="Invoice Number">
                        <!--<select id="sel_invoice" name="sel_invoice" style="width:220px;">
                            <?php /*foreach($all_agencies as $agency){*/?>
                                <option value="<?php /*echo $agency['id']*/?>"><?php /*echo $agency['agency_name']*/?></option>
                            <?php /*}*/?>
                        </select>-->
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="hidden" id="monthYear" name="monthYear" value="">
                        <input type="submit" class="_button" value="Submit" onclick="javascript:getAllInvoicesForCollection()" />
                    </td>
                </tr>
            </table>
        </div>

        <div class="block_content" id="invoice_list"></div>

        <div class="bendl"></div>
        <div class="bendr"></div>
    </div>		<!-- .block ends -->
    <div id="overlay" style="display: none;"></div>
</div>						<!-- wrapper ends -->

</div>		<!-- #hld ends -->


<script language="javascript">

    $(document).ready(function() {
        $( "#month" ).datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            maxDate: "+0m",
            dateFormat: 'MM yy',
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
                get_inputs_for_search(this.value);
            }
        });
        
        $("#excel_download").click(function(e){
            window.location =  "<?php echo base_url();?>report/download_invoice_collection_report/" ;
	});

    });

    function get_inputs_for_search(month){
        //var month = $('#month').val();
        $('#monthYear').val(month);

        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/invoice_manager/get_inputs_for_search',
            data: {
                month: month
            },
            beforeSend: function(){
                $("#sel_agency").html("");
                $("#sel_agency").append("<option value=''>--</option>");
                $("#sel_client").html("");
                //$("#sel_invoice").html("");
            },
            dataType: 'json',
            success: function(data){
                if(data != ''){
                    $.each(data.agencies, function (index, item) {
                        $("#sel_agency").append("<option value='"+item.agency_name+"'>" + item.agency_name  + "</option>");
                    });
                    //commented for requirement changes
                    /*$.each(data.invoices, function (index, item) {
                        $("#sel_invoice").append("<option value='"+item.invoice_number+"'>INVOICE_" + item.invoice_number  + "</option>");
                    });*/
                }else{

                }
            }
        });

    }

    function get_clients_for_invoice(agency){
        var month = $('#month').val();
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/invoice_manager/get_clients_for_invoice',
            data: {
                agency: agency,
                month:month
            },
            beforeSend: function(){
                $("#sel_client").html("");
                $("#sel_client").append("<option value=''>--</option>");
            },
            dataType: 'json',
            success: function(data){
                if(data != ''){
                    $.each(data, function (index, item) {
                        $("#sel_client").append("<option value='"+item.client_name+"'>" + item.client_name  + "</option>");
                    });
                }else{

                }
            }
        });
    }

    function getAllInvoicesForCollection(){
        check_form();

        var month = $('#month').val();
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/invoice_manager/post_invoice_search_set',
            beforeSend : function(){
                $('#overlay').show();
                $('#cancel_loader').show();
            },
            data: {
                monthYear:month,
                sel_agency: $("#sel_agency").val(),
                inv_search_str:$("#search_inv").val(),
                sel_client:$("#sel_client").val(),
                invoice_by:$("input[type='radio'][name='invoice_by']:checked").val()
            },
            success: function(data){
                $('#overlay').hide();
                $('#cancel_loader').hide();
                if(data != ''){
                    $('#invoice_list').html(data);
                }else{
                    $('#invoice_list').html("No Result Found");
                }
            }
        });
    }

    function update_collections(invoice_no){
        //var month = $('#month').val();
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/invoice_manager/update_invoice_payment/'+invoice_no,
            iframe:true,
            width: '650px',
            height:'600px',
            onClosed:function(){ getAllInvoicesForCollection(); }
        });
    }
    function check_form() {
        var month = $('#month').val();
        var agency = $("#sel_agency").val();
        var invoice = $("#sel_invoice").val();
        var client = $("#sel_client").val();
        var invoice_by = $("input[type='radio'][name='invoice_by']:checked").val();

        if(invoice_by == 'by_agency'){
            if(month == ''){
                alert("Please select a Month");
                return false;
            }

            if(agency == ''){
                alert("Please select an Agency");
                return false;
            }
        }
    }

</script>
