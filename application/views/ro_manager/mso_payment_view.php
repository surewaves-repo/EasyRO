<!-- MSO Payments/View MSO Payments -->
<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

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
                <h2>Filtering Invoice</h2>
            </div>

            <div class="block_content">
                <form action="" method="post">
                    <p>
                        <label>Start date:</label> 
                        <input type="text" readonly="readonly" id="start_date" name="start_date" onchange="javascript:getBillingName()"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <label style="margin-left:100px">End date:</label> 
                        <input type="text" readonly="readonly" id="end_date" name="end_date" onchange="javascript:getBillingName()"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <label style="margin-left: 100px">Billing Name:</label> 
                        <select style="width:150px" name="network_id" id="network_id">
                           <!-- <option value="0">All</option> -->
                        </select>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input style="margin-left: 50px" type="button" class="_button searchValue" id="" value="Search" onclick="" />
                    </p>
                </form>
            </div>	<!-- .block_content ends -->
            
        </div>    
        
        <div class="block">
            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h2>Invoice Data</h2>
                <span>
                    <input type="button" title="Download Invoice Details" style="float:right;width: 20%;margin-top:5px; margin-left: 35px;background: rgba(0, 0, 0, 0) url('../images/excel.png') no-repeat scroll 0 0;cursor:pointer;width:4%;border:none;height:40px;" id="excel_download" name="excel_download" >
                </span>
                <input style="float:right;margin-top:15px;margin-left:35px;" type="button" class="_button download" id="" value="Download" onclick=""/>
            </div>
            
            <div class="block_content invoiceData"> 
                <form action="" method="post">
                    <table cellpadding="0" cellspacing="0" width="100%" id="invoiceData">
                        <tr>
                                <th width="10">&nbsp;</th>
                                <th>Invoice Number</th>
                                <th>Invoice Date</th>
                                <th>Billing Name</th>
                                <th>Network RO Number </th>
                                <th>Invoice Amount</th>
                                <th>Debit Note</th>
                        </tr>

                    </table>
                </form>    
            </div>	<!-- .block_content ends -->
            
       </div>    

    </div>
</div>	
<script type="text/javascript">

$(document).ready(function() {

/* *
 * jQuery Function to Invoice Details Excel Download
 */

    $("#excel_download").click(function(e) 
    {
        var startDate = $("#start_date").val();
        var endDate = $("#end_date").val();
        var network_id = $("#network_id").val();

        window.location = "<?php echo base_url();?>report/download_payment_detail/" + startDate + "/" + endDate + "/" + network_id;
    });

    /* *
     * Date Picker Format
    */

    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    if (start_date === '') {
        start_date = new Date();
    }
    if (end_date === '') {
        end_date = new Date();
    }

    var minEndDate = start_date;
    var maxEndDate = new Date(start_date);
    maxEndDate.setMonth(maxEndDate.getMonth() + 3);

    var maxStartDate = end_date;
    var minStartDate = new Date(end_date);
    minStartDate.setMonth(minStartDate.getMonth() - 3);

    $("#start_date").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        minDate: minStartDate,
        maxDate: maxStartDate,
        onClose: function(selectedDate) {
            $("#start_date").datepicker("option");
        }
    }).datepicker("option", "maxDate", maxStartDate);

    $("#end_date").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        minDate: minEndDate,
        maxDate: maxEndDate,
        onClose: function(selectedDate) {
            $("#end_date").datepicker("option");
        }
    }).datepicker("option", "maxDate", maxEndDate);

/* *
 * jQuery Function to Fetch all Invoice Details
*/

    $('.searchValue').click(function() 
    {
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var network_id = $("#network_id").val();
        
        $("td").remove();
        
        if (start_date !== '' && end_date !== '') {
            $.ajax({
                type: 'POST',
                url: '/surewaves_easy_ro/mso_payment/getAllInvoiceData',
                dataType: 'json',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    network_id: network_id
                },
                beforeSend: function() {},
                
                success: function(data) {
                    
                    if (data !== '') {
                        
                        $.each(data, function(index, item) {
                            
                            if (item.Ro_Amount == 0) {
                                var debitNote = 0;
                                $("#invoiceData").append("<tr>" +
                                                            "<td><input value = " + item.invoice_number + " type='checkbox'/></td>" +
                                                            "<td><a target='_blank' href='" + item.file + "'><p>" + item.invoice_number + "</p></a></td>" +
                                                            "<td>" + item.invoice_date + "</td>" +
                                                            "<td>" + item.billing_name + "</td>" +
                                                            "<td>" + item.network_ro_number + "</td>" +
                                                            "<td>" + parseFloat(item.Ro_Amount_Payable).toFixed(2) + "</td>" +
                                                            "<td>" + debitNote.toFixed(2) + "</td>" +
                                                        "</tr>");
                            } else {
                                var debitNote = item.Actual_Ro_Amount - item.Actual_Ro_Amount_Payable;
                                $("#invoiceData").append("<tr>" +
                                                            "<td><input value = " + item.invoice_number + " type='checkbox'/></td>" +
                                                            "<td><a target='_blank' href='" + item.file + "'><p>" + item.invoice_number + "</p></a></td>" +
                                                            "<td>" + item.invoice_date + "</td><td>" + item.billing_name + "</td>" +
                                                            "<td>" + item.network_ro_number + "</td><td>" + parseFloat(item.Ro_Amount).toFixed(2) + "</td>" +
                                                            "<td>" + debitNote.toFixed(2) + "</td>" +
                                                        "</tr>");
                            }
                        });
                        
                    } else {

                    }
                }
            });
        }

    });

/* *
 * jQuery Function to Download All Invoices
*/

    $(".download").click(function() {
        
        var arrInvoice = new Array();
        $("input:checked").each(function() {
            arrInvoice.push($(this).val());
        });
        if(arrInvoice == ''){
                alert('Please select atleast 1 invoice');
                return false;
        }
       $.ajax({

            type: "POST",
            url: "<?php echo base_url('mso_payment/downloadInvoicePdf');?>",
            data: "InvoiceNo=" + arrInvoice,

            beforeSend: function() {},

            success: function(response) {
                
                if (response != 0) {
                    var obj = $.parseJSON(response);
                    var archiveFile = obj.zip;
                    location.href = archiveFile;
                } else {
                    alert('Please select atleast 1 Invoice');
                    return false;
                }
            }

        });
    });

});

/* *
 * Function to Fetch Changed Date Values
*/

function changeDate() {
    
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    
    if (start_date === '') {
        start_date = new Date();
    }
    if (end_date === '') {
        end_date = new Date();
    }

    var minEndDate = start_date;
    var maxEndDate = new Date(start_date);
    maxEndDate.setMonth(maxEndDate.getMonth() + 3);

    var maxStartDate = end_date;
    var minStartDate = new Date(end_date);
    minStartDate.setMonth(minStartDate.getMonth() - 3);

    $("#start_date").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        minDate: minStartDate,
        maxDate: maxStartDate,
        onClose: function(selectedDate) {
            $("#start_date").datepicker("option");
        }
    });

    $("#end_date").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        minDate: minEndDate,
        maxDate: maxEndDate,
        onClose: function(selectedDate) {
            $("#end_date").datepicker("option");
        }
    });
}

/* *
 * Function to Get Billing Name Based on the network ID of uploaded Invoice
*/

function getBillingName() {

    changeDate();
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    if (start_date !== '' && end_date !== '') {
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/mso_payment/getBillingName',
            dataType: 'json',
            data: {
                start_date: start_date,
                end_date: end_date
            },
            
            beforeSend: function() {
                $("#network_id").html("");
            },
            
            success: function(data) {
                
                if (data !== '') {
                    $("#network_id").append("<option value='0'>All</option>");
                    $.each(data, function(index, item) {
                        $("#network_id").append("<option value='" + item.customer_id + "'>" + item.billing_name + "</option>");
                    });
                } else {

                }
            }
        });
    }

}   
</script>