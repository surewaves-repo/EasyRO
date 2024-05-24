<!-- Colorbox pop up for updating payment for one invoice at a time -->
<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>    
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>


            
            <div class="block small center login" style="margin:0px; width:100%;">
                
                
                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>                  
                    <h2>Record Payment</h2>                 
                </div>  
                
                <div class="block_content">
                <form action="<?php echo ROOT_FOLDER ?>/mso_payment/update_mso_payment_record_details" method="post" id="update" enctype="multipart/form-data">
                    <div id="show_error"></div>
                    <table cellpadding="0" cellspacing="0" width="100%" id="details">
                        <tr>
                            <td width="30%">Total Invoice Amount</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="hidden" id="invoice_no" name="invoice_no" value="<?php echo $payment[0]['invoice_number']?>" />
                                <input type="hidden" id="debit_note" name="debit_note" value="<?php if($payment[0]['Ro_Amount'] == 0){ echo round($payment[0]['Ro_Amount'],2); } else { echo round($payment[0]['Actual_Ro_Amount']-$payment[0]['Actual_Ro_Amount_Payable'],2); } ?>" />
                                <input type="text" id="total_amt" readonly="readonly" name="total_amt" style="width:220px;" value="<?php if($payment[0]['Ro_Amount'] == 0){ echo round($payment[0]['Ro_Amount_Payable'],2); } else { echo round($payment[0]['Ro_Amount'],2); } ?>" onfocus="this.blur()"  />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="30%">Amount Paid</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="amt_paid" readonly="readonly" name="amt_paid" style="width:220px;" value="<? echo $amount ?>" onfocus="this.blur()"/>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>                   
                        <tr>
                            <td>Remaining Amount</td>
                            <td > : </td>
                            <td width="25%">
                                <input type="text" id="rmng_amt" readonly="readonly" name="amt_paying" style="width:220px;" value="<?php if($payment[0]['Ro_Amount'] == 0){if((round($payment[0]['Ro_Amount_Payable']-$amount,2)) < 0){echo 0;}else{echo round($payment[0]['Ro_Amount_Payable']-$amount,2) ; } } else { if((round($payment[0]['Ro_Amount']-$amount,2)) < 0){echo 0;}else{echo round($payment[0]['Ro_Amount']-$amount,2) ;}} ?>" onfocus="this.blur()"/>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                            <td>New Payment<span style="color:red"> *</span></td>
                            <td >:</td>
                            <td width="25%">
                                
                                <input type="text" id="new_payment" name="new_payment" style="width:220px;" value="" onChange="javascript:calculateAmt()" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Basic Amount</td>
                            <td >:</td>
                            <td width="25%">
                                <input type="text" id="basic_amt" readonly="readonly" name="basic_amt" style="width:220px;" value="" onfocus="this.blur()"/>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="20%">TDS</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="tds" readonly="readonly" name="tds" style="width:220px;" value="" onfocus="this.blur()"/>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Service Tax</td>
                            <td >:</td>
                            <td >
                                <input type="text" id="srvc_tax" readonly="readonly" name="srvc_tax" style="width:220px;" value="" onfocus="this.blur()"/>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="20%">Mode of Payment<span style="color:red"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <select name="select_mode" id="select_mode" readonly="readonly" style="width:220px;" onChange="javascript:modeSelect()">
                                    <option value="">Please Select</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Online Transfer">Online Transfer</option>
                                </select>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tbody id="add_details"></tbody>
                        <tr>    
                            <td> 
                               <input type="submit" class="submit" value="Save" id="submit_details" onclick="return submitDetails()" />    
                            </td>                       
                        </tr>
                    
                        
                        
                    </form>
                </div>      <!-- .block_content ends -->
                    
                <div class="bendl"></div>
                <div class="bendr"></div>
                                
            </div>      <!-- .login ends -->
            
        
<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>
<script src="<?php echo base_url(); ?>assets/external_lib/dist/jquery.validate.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/external_lib/lib/jquery.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">

/* *
 * Function for Mode of payment selection from drop down 
 * */

 function modeSelect()
 {
     
        $("#add_details").html("");
        var selectedMode = $("#select_mode").val();
        
        if(selectedMode == 'Cheque'){
            $("#add_details").append("<tr><td width='30%'> Cheque Number<span style='color:red'> *</span></td><td width='5%'> : </td><td></div><input type='text' id='cheque_no' name='cheque_no' style='width:220px;' value='' /></td><td width='40%'> &nbsp; </td></tr>"+
                                     "<tr><td width='20%'> Client Bank Name<span style='color:red'> *</span> </td><td width='5%'> : </td><td><input type='text' id='bank_name' name='bank_name' style='width:220px;' value='' /></td><td width='40%'> &nbsp; </td></tr>"+
                                     "<tr><td width='20%'> Cheque Date<span style='color:red'> *</span> </td><td width='5%'> : </td><td><input type='text' class='form-control' id='transaction_date' name='transaction_date' style='width:220px;' value='' onclick='javascript:getDate()'/></td><td width='40%'> &nbsp; </td></tr>"+
                                     "<tr><td width='20%'> Notes </td><td width='5%'> : </td><td><textarea id='notes' name='notes' rows='2' cols='50' /></td><td width='40%'> &nbsp; </td></tr>");
        }else{
            $("#add_details").append("<tr><td width='30%'> Transaction Number</td><td width='5%'> : </td><td><input type='text' id='transaction_no' name='transaction_no' style='width:220px;' value='' /></td><td width='40%'> &nbsp; </td></tr>"+
                                     "<tr><td width='20%'> Client Bank Name<span style='color:red'> *</span> </td><td width='5%'> : </td><td><input type='text' id='bank_name' name='bank_name' style='width:220px;' value='' /></td><td width='40%'> &nbsp; </td></tr>"+
                                     "<tr><td width='20%'> Transaction Date</td><td width='5%'> : </td><td><input type='text' class='form-control' id='transaction_date' name='transaction_date' style='width:220px;' value='' onclick='javascript:getDate()'/></td><td width='40%'> &nbsp; </td></tr>"+
                                     "<tr><td width='20%'> Notes </td><td width='5%'> : </td><td><textarea id='notes' name='notes' rows='2' cols='50' /></td><td width='40%'> &nbsp; </td></tr>");
        }
 }

/* *
 * Function For auto-calculating basic-amount, tds, service-tax 
 * */
    
function calculateAmt()
{
    
    var new_payment = document.getElementById("new_payment").value;
    var srvcTax = (14.5 * new_payment)/114.5;
    var amountWithTDS = new_payment - srvcTax;
    var tds = (2 * amountWithTDS)/100;
    var BasicAmt = amountWithTDS - tds ;
    document.getElementById("basic_amt").value = parseFloat(BasicAmt).toFixed(2);
    document.getElementById("tds").value = parseFloat(tds).toFixed(2);
    document.getElementById("srvc_tax").value = parseFloat(srvcTax).toFixed(2);
}

/* *
 * Function to Get Date for Transaction Date 
 * */

function getDate()
{
    
     $( "#transaction_date" ).datepicker({dateFormat: "yy-mm-dd"});
}

/* *
 * Function to Validate and submit data
*/

function submitDetails()
{
    
    if($("#new_payment").val() == ''){
        $("#show_error").append("<div style='color:red'>Please enter New Payment details before saving.</div>");
        return false;
    }
    
    var regexForNumber = /^[0-9]+$/;    
    if(!regexForNumber.test($("#new_payment").val())){
        $("#show_error").empty();
        $("#show_error").append("<div style='color:red'>Only numbers allowed. Please enter the correct value and try again.</div>");
        return false;
    }
    
    if($("#select_mode").val() == ''){
        $("#show_error").empty();
        $("#show_error").append("<div style='color:red'>Please select a Mode of Payment</div>");
        return false;
    }
    //var regexForCharacter = /^([\w -]+|[\d]+)$/;
    if($("#select_mode").val() == 'Cheque'){
        if($("#cheque_no").val() == ''){
            $("#show_error").empty();
            $("#show_error").append("<div style='color:red'>Please enter the Cheque Number</div>");
            return false;
        }

        if(!regexForNumber.test($("#cheque_no").val())){
            $("#show_error").empty();
            $("#show_error").append("<div style='color:red'>Only numbers allowed. Please enter the correct Cheque Number and try again.</div>");
            return false;
        }
    }
    
    if($("#bank_name").val() == ''){
        $("#show_error").empty();
        $("#show_error").append("<div style='color:red'>Please enter the Bank Name</div>");
        return false;
    }
    
    
    if($("#transaction_date").val() == ''){
        $("#show_error").empty();
        $("#show_error").append("<div style='color:red'>Please select the Transaction Date</div>");
        return false;
    }
   
    
}
 </script>   