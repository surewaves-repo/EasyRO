<!-- MSO Payments/Update Payments -->
<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> 
<!--<script src="<?php echo base_url(); ?>assets/external_lib/lib/jquery.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/external_lib/dist/jquery.validate.js" type="text/javascript"></script> -->

<style>
    .rightborder {
    border-right: 1px solid #000000;
    }

    table {
      border-spacing: 10;
      border-top: 1px solid #ccc;
      width:100%;
    }
    
    .block table tr td, .block table tr th {
    border-bottom: 1px solid #ddd;
    line-height: normal;
    padding: 7px;
    text-align: center;
}
    
</style>
<div id="hld">
    <div class="wrapper">       <!-- wrapper begins -->
        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1> 
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>         

            <input type="hidden" id="profile_id" value="<?php echo $profile_id?>" />
            <?php echo $menu ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>      <!-- #header ends -->
        
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
            </div>  <!-- .block_content ends -->
            
        </div>    
        
        <div class="block">
            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h2>Invoice Data</h2>
                <span>
                <input type="button" id="csv_file" value="upload" class="_button upload" style="margin-left: 930px" onclick="javascript:uploadExcel()"/>
                </span>
            </div>
            
            <div class="block_content invoiceData"> 
                <form action="" method="post">
                    <table cellpadding="0" cellspacing="0" id="invoiceData">
                        <tr>
                                <th>Invoice Number</th>
                                <th>Invoice Date</th>
                                <th>Billing Name</th>
                                <th class="rightborder">Invoice Amount</th>
                                <th>Debit Note</th>
                                <th>Basic Amount</th>
                                <th>TDS</th>
                                <th class="rightborder">Service Tax</th>
                                <th>Amount Paid</th>
                                <th>Remaining Amount</th>
                                <th>Payment Details</th>
                                <th>Update Payment</th>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                </form>    
            </div>  <!-- .block_content ends -->
            
       </div>    

    </div>
</div>
<!-- Javascript library for loading colorbox; 
     Reason: There was a conflict with other javascript library(Ex:datePicker) -->
     
<script src="https://cdn.jsdelivr.net/colorbox/1.6.3/jquery.colorbox-min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/colorbox/1.6.3/jquery.colorbox.js" type="text/javascript"></script>
<script type="text/javascript">

    /* * 
     * colorbox pop up for updating payments 
    */
   
    function update_payment_record(invoiceNumber)
    {
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/mso_payment/update_mso_payment_record/'+invoiceNumber,iframe:true, width: '1040px', height:'650px', data:{invoice_no:invoiceNumber}, type:"POST",});
    }
    
    /* *
     * colorbox pop up for viewing payments details  
    */
   
    function getDetails(invoiceNumber){
        
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/mso_payment/getDetails/'+invoiceNumber,iframe:true, width: '1040px', height:'650px', data:{invoice_no:invoiceNumber}, type:"POST",});
    }
    
    /* *
     * colorbox pop up for CSV upload
    */
   
    function uploadExcel(){
        
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/mso_payment/excelUpload/',iframe:true, width: '540px', height:'250px'});
    }
    
    /* *
     * Get Billing Name Based on the network ID of uploaded Invoice 
    */
     
    function getBillingName() {
        
       var start_date = $("#start_date").val() ;
       var end_date = $("#end_date").val() ;
       
       if(start_date !== '' && end_date !== '') {
           
           $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/mso_payment/getBillingName',
            dataType: 'json',
            data: {
                start_date : start_date,
                end_date : end_date
            },
            
            beforeSend: function(){
                $("#network_id").html("");
            },
                    
            success: function(data){
                
                if(data !== ''){
                    $("#network_id").append("<option value='0'>All</option>");
                    $.each(data, function (index, item) {
                        $("#network_id").append("<option value='"+item.customer_id+"'>" + item.billing_name  + "</option>");
                    });
                }else{
    
                }
            }
        });
       }
       
    }   
    
    $(document).ready(function(){
        
        var start_date = $("#start_date").val() ;
        var end_date = $("#end_date").val() ;
        
        if(start_date === '' ){
            start_date = new Date() ;
        }
        if(end_date === '') {
            end_date = new Date() ;
        }
        
        var minEndDate = start_date ;
        var maxEndDate = new Date(start_date);
        maxEndDate.setMonth(maxEndDate.getMonth() + 3);
        
        var maxStartDate = end_date ;
        var minStartDate = new Date(end_date );
        minStartDate.setMonth(minStartDate.getMonth() - 3);
        
        $("#start_date").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            minDate : minStartDate,
            maxDate : maxStartDate,
            onClose: function( selectedDate ) {
                $( "#start_date" ).datepicker( "option" );
            }
        }).datepicker("option", "maxDate", maxStartDate);
        
        $("#end_date").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            minDate : minEndDate,
            maxDate : maxEndDate,
            onClose: function( selectedDate ) {
                $( "#end_date" ).datepicker( "option"  );
            }
        }).datepicker("option", "maxDate", maxEndDate);
        
        $(".searchValue").click(function(){
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            var network_id = $("#network_id").val() ;
            
            $("td").remove();
            
            if(start_date !== '' && end_date !== '') {
                
                $.ajax({
                    type: 'POST',
                    url: '/surewaves_easy_ro/mso_payment/getUpdatedInvoiceData',
                    dataType: 'json',
                    data: {
                        start_date : startDate,
                        end_date : endDate,
                        network_id : network_id
                    },
                    
                    beforeSend: function(){}, 
                           
                    success: function(data){
                        
                        if(data !== ''){
                            $.each(data, function (index, item) {
                                if( item.ro_amount == 0){
                                    var debitNote = 0 ;
                                    var srvcTax = (14.5 * item.ro_amount_payable)/114.5;
                                    var amountWithTDS = item.ro_amount_payable - srvcTax;
                                    var tds = (2 * amountWithTDS)/100;
                                    var BasicAmt = amountWithTDS - tds ;
                                    var remainingAmt = item.ro_amount_payable - item.new_payment ;
                                    if(remainingAmt < 0){
                                        remainingAmt = 0;
                                    }       
                                    $("#invoiceData").append("<tr><td>"+ item.invoice_number +"</td>"+
                                                                 "<td>"+ item.invoice_date +"</td>"+
                                                                 "<td><p>"+ item.billing_name +"</p></td>"+
                                                                 "<td class='rightborder'>"+ parseFloat(item.ro_amount_payable).toFixed(2) +"</td>"+
                                                                 "<td>"+ debitNote.toFixed(2) +"</td>"+
                                                                 "<td>"+ BasicAmt.toFixed(2) +"</td>"+
                                                                 "<td>"+ tds.toFixed(2) +"</td>"+
                                                                 "<td class='rightborder'>"+ srvcTax.toFixed(2) +"</td>"+
                                                                 "<td>"+ item.new_payment +"</td>"+
                                                                 "<td>"+ parseFloat(remainingAmt).toFixed(2) +"</td>"+
                                                                 "<td><a href='#' onclick='javascript:getDetails(\"" + item.invoice_number + "\")'><input type='button' class='_button' value='View'></a></td>"+
                                                                 "<td><input type='button' class='_button' value='Update' onclick='javascript:update_payment_record(\"" + item.invoice_number + "\")'></td>"+                                                        
                                                              "</tr>");
                                }else{
                                    var debitNote = item.actual_ro_amount - item.actual_ro_amount_payable ;
                                    var srvcTax = (14.5 * item.actual_ro_amount_payable)/114.5;
                                    var amountWithTDS = item.actual_ro_amount_payable - srvcTax;
                                    var tds = (2 * amountWithTDS)/100;
                                    var BasicAmt = amountWithTDS - tds ;
                                    var remainingAmt = item.ro_amount- item.new_payment ;
                                    if(remainingAmt < 0){
                                        remainingAmt = 0;
                                    }
                                    $("#invoiceData").append("<tr><td>"+ item.invoice_number +"</td>"+
                                                                 "<td> "+ item.invoice_date +"</td>"+
                                                                 "<td><p>"+ item.billing_name +"</p></td>"+
                                                                 "<td class='rightborder'>"+ parseFloat(item.ro_amount).toFixed(2) +"</td>"+
                                                                 "<td>"+ debitNote.toFixed(2) +"</td>"+
                                                                 "<td>"+ BasicAmt.toFixed(2) +"</td>"+
                                                                 "<td>"+ tds.toFixed(2) +"</td>"+
                                                                 "<td class='rightborder'>"+ srvcTax.toFixed(2) +"</td>"+
                                                                 "<td>"+ item.new_payment +"</td>"+
                                                                 "<td>"+ parseFloat(remainingAmt).toFixed(2) +"</td>"+
                                                                 "<td><a href='#' onclick='javascript:getDetails(\"" + item.invoice_number + "\")'><input type='button' class='_button' value='View'></a></td>"+
                                                                 "<td><input type='button' class='_button' value='Update' onclick='javascript:update_payment_record(\"" + item.invoice_number + "\")'></td>"+ 
                                                              "</tr>");
                                }
                            });
                        }else{
    
                        }
                    }    
                })
            }
        });
        
    });
</script>
