<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div class="block small center login" style="margin:0px; width:870px;">


<div class="block_head">
    <div class="bheadl"></div>
    <div class="bheadr"></div>
    <h2>Edit NON FCT RO</h2>
</div>

<div class="block_content">
<form action="<?php echo ROOT_FOLDER ?>/non_fct_ro/post_edit_non_fct_ext_ro" method="post" enctype="multipart/form-data">
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
    <td colspan="4"><?php if(isset($result) && $result == 'exists'){echo '<span style="color:#F00;">Agency all ready exists for this RO.</span>';}?>
        <?php if(isset($client_result) && $client_result == 'exists'){echo '<span style="color:#F00;">Client all ready exists for this RO.</span>';}?>
        <?php if(isset($res_am_edit_ext_ro) && $res_am_edit_ext_ro == 'exists'){echo '<span style="color:#F00;">The external ro already exists.</span>';}?>
    </td>

</tr>
<tr>
    <td width="30%">RO Number<span style="color:#F00;"> *</span></td>
    <td width="5%"> : </td>
    <td width="25%">
        <input type="text" id="txt_ext_ro" name="txt_ext_ro" style="width:220px;" value="<?php echo $ro_details[0]['customer_ro_number']?>" readonly="readonly" />
        <input type="hidden" name="hid_prev_cust_ro" id="hid_prev_cust_ro" value="<?php echo $ro_details[0]['cust_ro']?>" />
    </td>
    <td width="40%">&nbsp;</td>
</tr>

<tr>
    <td>Agency<span style="color:#F00;"> *</span></td>
    <td > : </td>
    <td >
        <input type="text" id="txt_agency" name="txt_agency" style="width:220px;" value="<?php echo $ro_details[0]['agency']?>" readonly="readonly" />
    </td>
    <td>&nbsp;</td>
</tr>
<tr>
    <td>Agency Contact Name<span style="color:#F00;"> *</span></td>
    <td > : </td>
    <td >
        <!--<input type="text" id="txt_agency_contact" name="txt_agency_contact" style="width:220px;" value="<?php echo $ro_details[0]['agency_contact_name']?>" readonly="readonly" />-->

        <select name="sel_agency_contact_name" id="sel_agency_contact_name" style="width:220px;" <?php if($approval_status[0]['approved_by'] == 1){echo "disabled"; }?> >
            <option value="new">New</option>
            <?php foreach($agency_contact as $agencies){
                if($agencies['id'] == $ro_details[0]['agency_contact'])	{
                    ?>
                    <option selected="selected" value="<?php echo $agencies['id']?>"><?php echo $agencies['agency_contact_name']?></option>
                <?php }else{?>
                    <option value="<?php echo $agencies['id']?>"><?php echo $agencies['agency_contact_name']?></option>
                <?php }
            }?>
        </select>


    </td>
    <td><a href="javascript:add_agency_contact()">Add/Edit</a></td>
</tr>
<tr>
    <td width="20%">Client<span style="color:#F00;"> *</span></td>
    <td width="5%"> : </td>
    <td width="25%">
        <input type="text" id="txt_client" name="txt_client" style="width:220px;" value="<?php echo $ro_details[0]['client']?>" readonly="readonly" />
    </td>
    <td width="50%">&nbsp;</td>
</tr>
<tr>
    <td>Client Contact Name</td>
    <td > : </td>
    <td >

        <select name="sel_client_contact_name" id="sel_client_contact_name" style="width:220px;" <?php if($approval_status[0]['approved_by'] == 1){echo "disabled"; }?> >
            <option value="new">New</option>
            <?php foreach($client_contact as $contacts){
                if($contacts['id'] == $ro_details[0]['client_contact'])	{
                    ?>
                    <option selected="selected" value="<?php echo $contacts['id']?>"><?php echo $contacts['client_contact_name']?></option>
                <?php }else{?>
                    <option value="<?php echo $contacts['id']?>"><?php echo $contacts['client_contact_name']?></option>
                <?php }
            }?>
        </select>


    </td>
    <td><a href="javascript:add_client_contact()">Add/Edit</a></td>
</tr>

<tr>
    <td width="30%">Account Manager Name<span style="color:#F00;"> *</span></td>
    <td width="5%"> : </td>
    <td width="25%">
        <input type="text" name="sel_am" value="<?php echo $ro_details[0]['account_manager_name']?>" readonly="readonly">
        <input type="hidden" id="hid_user_id" name="hid_user_id" style="width:220px;" value="<?php if(isset($user_id)){echo $user_id;}else{echo $logged_in_user['user_id'];} ?>" />
    </td>
    <td width="40%">&nbsp;</td>
</tr>
<!--
                     *** Code for Region
                     -->

<tr>
    <td width="20%">Region<span style="color:#F00;"> *</span></td>
    <td width="5%"> : </td>
    <td width="25%">
        <?php echo $region?>
    </td>
    <td width="40%">&nbsp;</td>
</tr>

<?php
$fin = (int)$financial_year;
$fin_end = $fin +1;
?>

<tr>
    <td width="20%">Financial Year</td>
    <td width="5%"> : </td>
    <td width="25%">
        <?php echo $financial_year."-".$fin_end ?>
    </td>
    <td width="50%">&nbsp;</td>
</tr>

<tr id="market_tr">
    <td colspan="4" style="padding:0px;">
        <div id="market_div">
            <table width="100%">
                <tr>
                    <td colspan="8" nowrap="nowrap" style="border-bottom:none;">Monthwise RO Amount :<span style="color:red;"></span></td>
                </tr>
                <tr>
                    <td>
                        <span >January</span>
                    </td>

                    <td>
                        <span><input type="text" id="jan_amount" name="amount[january]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['january'])){echo $amount['january'];}else{ echo 0;}?>" onblur="price_check_for_each_market_on_blur('jan_amount',this.value);calculate_final_amount()" /></span>
                    </td>


                    <td>
                        <span >February</span>
                    </td>

                    <td>
                        <span><input type="text" id="feb_amount" name="amount[february]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['february'])){echo $amount['february'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('feb_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                    <td>
                        <span >March</span>
                    </td>

                    <td>
                        <span><input type="text" id="march_amount" name="amount[march]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['march'])){echo $amount['march'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('march_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                </tr>

                <tr>

                    <td>
                        <span >April</span>
                    </td>

                    <td>
                        <span><input type="text" id="april_amount" name="amount[april]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['april'])){echo $amount['april'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('april_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                    <td>
                        <span >May</span>
                    </td>

                    <td>
                        <span><input type="text" id="may_amount" name="amount[may]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['may'])){echo $amount['may'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('may_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                    <td>
                        <span >June</span>
                    </td>

                    <td>
                        <span><input type="text" id="june_amount" name="amount[june]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['june'])){echo $amount['june'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('june_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                </tr>

                <tr>
                    <td>
                        <span >July</span>
                    </td>

                    <td>
                        <span><input type="text" id="july_amount" name="amount[july]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['july'])){echo $amount['july'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('july_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                    <td>
                        <span >August</span>
                    </td>

                    <td>
                        <span><input type="text" id="aug_amount" name="amount[august]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['august'])){echo $amount['august'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('aug_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                    <td>
                        <span >September</span>
                    </td>

                    <td>
                        <span><input type="text" id="sep_amount" name="amount[september]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['september'])){echo $amount['september'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('sep_amount',this.value);calculate_final_amount()" /></span>
                    </td>
                </tr>

                <tr>

                    <td>
                        <span >October</span>
                    </td>

                    <td>
                        <span><input type="text" id="oct_amount" name="amount[october]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['october'])){echo $amount['october'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('oct_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                    <td>
                        <span >November</span>
                    </td>

                    <td>
                        <span><input type="text" id="nov_amount" name="amount[november]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['november'])){echo $amount['november'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('nov_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                    <td>
                        <span >December</span>
                    </td>

                    <td>
                        <span><input type="text" id="dec_amount" name="amount[december]" class="fin_amount" style="width:80px;" value="<?php if(isset($amount['december'])){echo $amount['december'];}else{ echo 0;} ?>" onblur="price_check_for_each_market_on_blur('dec_amount',this.value);calculate_final_amount()" /></span>
                    </td>

                </tr>

            </table>
        </div>
    </td>
</tr>


<tr>
    <td width="30%">Gross Amount<span style="color:#F00;"> *</span></td>
    <td width="5%"> : </td>
    <td width="25%">
        <input type="text" id="txt_gross" name="txt_gross" style="width:220px;" value="<?php echo $ro_details[0]['gross_ro_amount']?>" readonly="readonly" />
        <input type="hidden" id="ro_initial_gross" value="<?php echo $ro_details[0]['gross_ro_amount']?>" />
    </td>
    <td width="40%">&nbsp;</td>
</tr>
<tr>
    <td width="30%" nowrap="nowrap">Total Expenses</td>
    <td width="5%"> : </td>
    <td width="25%">
        <input type="text" id="txt_agency_com" name="txt_agency_com" style="width:220px;" value="<?php echo $ro_details[0]['agency_commision']?>" />
        <input type="hidden" id="initial_agency_commission" value="<?php echo $ro_details[0]['agency_commision']?>" />
    </td>
    <td width="40%">&nbsp;</td>
    <!--commented to hide Re-calculate button as per new requirement (should calculate automatically)-->
    <!--<td><a href="javascript:recalculate_agency_commission()">Re-calculate</a></td>-->
</tr>

<tr>
    <td width="30%">Description</td>
    <td width="5%"> : </td>
    <td width="25%">
        <textarea id="txt_spcl_inst" name="txt_spcl_inst" style="width:207px; height:70px;" placeholder="Any instruction including Billing Instruction" ><?php echo $ro_details[0]['description']?></textarea>
    </td>
    <td width="40%">&nbsp;</td>
</tr>

<tr>
    <td>
        <input type="hidden" name="hid_id" id="hid_id" value="<?php echo $ro_details[0]['id']?>" />
        <input type="hidden" name="hid_campaign_created" id="hid_campaign_created" value="<?php echo $campaign_created?>" />
        <input type="hidden" name="hid_sel_agency_contact_name" id="hid_sel_agency_contact_name" value="<?php echo $ro_details[0]['agency_contact']?>" />
        <input type="hidden" name="hid_sel_client_contact_name" id="hid_sel_client_contact_name" value="<?php echo $ro_details[0]['client_contact']?>" />
        <input type="submit" class="submit" value="Update" onclick="return check_form();" />
    </td>
<tr>

</table>


</form>
</div>		<!-- .block_content ends -->

<div class="bendl"></div>
<div class="bendr"></div>

</div>		<!-- .login ends -->


<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>
<script type="text/javascript" language="javascript">

function check_form(){
    if($('#txt_ext_ro').val() == ""){
        alert("Please enter External RO Number.");
        $('#txt_ext_ro').focus();
        return false;
    }

    var ext_ro = document.getElementById('txt_ext_ro').value;
    var check_input = Utility.checkInput4(ext_ro);
    if(!check_input)	{
        alert("Invalid Ext RO");
        document.getElementById('txt_ext_ro').focus();
        return false;
    }

    if($('#txt_gross').val() == ""){
        alert("Please enter gross RO amount.");
        $('#txt_gross').focus();
        return false;
    }
    if($('#txt_gross').val() == "."){
        alert("Please enter valid amount.");
        $('#txt_gross').focus();
        return false;
    }
    if($('#txt_gross').val() == 0){
        alert("Gross amount cannot be 0");
        $('#txt_gross').focus();
        return false;
    }

    var spcl_inst = document.getElementById('txt_spcl_inst').value;

    var check_input = Utility.checkSingleDoubleQuotes(spcl_inst);
    if(spcl_inst !="" && !check_input)	{
        alert("Invalid Input for special instruction");
        document.getElementById('txt_spcl_inst').focus();
        return false;
    }

    var check_input = Utility.checkTextWordLength(spcl_inst);
    if(spcl_inst !="" && !check_input)	{
        alert("Special Instructions cannot have more than 500 Characters");
        document.getElementById('txt_spcl_inst').focus();
        return false;
    }
}
function price_check(value){
    var regex = /^[0-9.]+$/;
    gross = document.getElementById('txt_gross').value;
    agency_com = document.getElementById('txt_agency_com').value;

    var test_amount = Utility.checkAmount(gross);
    if(!test_amount)	{
        alert("Invalid Amount");
        document.getElementById('txt_gross').value = "";
        document.getElementById('txt_gross').focus();
        return false;
    }
    if(!regex.test(agency_com)){
        alert('not allowed');
        document.getElementById('txt_agency_com').value = 0;
        document.getElementById('txt_agency_com').focus();
    }
    if (hasDecimalPlace(value, 3)) {
        $('#txt_agency_com').val(value.slice(0, -1));
        return false;
    }
}
function price_check_for_each_market(id,value){
    var regex = /^[0-9.]+$/;
    var gross = document.getElementById(id).value;
    agency_com = document.getElementById('txt_agency_com').value;

    var test_amount = Utility.checkAmount(gross);
    if(!test_amount)	{
        alert("Invalid Amount");
        document.getElementById(id).value = 0;
        document.getElementById(id).focus();
        return false;
    }
    if (hasDecimalPlace(value, 3)) {
        $('#'+id).val(value.slice(0, -1));
        return false;
    }
}
function hasDecimalPlace(value, x) {//alert(value)
    var pointIndex = value.indexOf('.');
    return  pointIndex >= 0 && pointIndex < value.length - x;
}

function price_check_for_each_market_on_blur(id,value){
    var regex = /^[0-9.]+$/;
    var gross = document.getElementById(id).value;
    agency_com = document.getElementById('txt_agency_com').value;

    var test_amount = Utility.checkAmount(gross);
    if(!test_amount)	{
        alert("Invalid Amount");
        document.getElementById(id).value = 0;
        document.getElementById(id).focus();
        return false;
    }
    if(value.indexOf('.') == 0 || value.lastIndexOf('.') == value.length - 1){
        alert("Invalid Amount");
        document.getElementById(id).value = 0;
        return false;
    }
    var monthly_gross = $('#'+id).val().split(".");
    if(monthly_gross.length > 2){
        alert("Invalid Amount");
        $('#'+id).val(0);
        $('#'+id).focus();
        return false;
    }
    if($('#'+id).val() == "."){
        alert("Invalid Amount");
        $('#'+id).val(0);
        $('#'+id).focus();
        return false;
    }
}

//function used to re-calculate agency commission based on Gross ro amount : nitish
function recalculate_agency_commission(){
    var new_gross = parseFloat($('#txt_gross').val());
    var old_gross = parseFloat($('#ro_initial_gross').val());
    var commission = parseFloat($('#initial_agency_commission').val());

    if(new_gross > old_gross){
        var gross_change = new_gross - old_gross;
        var fraction_gross_change = gross_change/old_gross;
        var calculated_commission = commission + commission * fraction_gross_change;
    }else{
        var gross_change = old_gross - new_gross;
        var fraction_gross_change = gross_change/old_gross;
        var calculated_commission = commission - commission * fraction_gross_change;
    }
    $('#txt_agency_com').val(calculated_commission.toFixed(2));
}
function add_client_contact(){
    var ext_ro = document.getElementById('txt_ext_ro').value;
    var check_input = Utility.checkInput4(ext_ro);

    if(!check_input)	{
        alert("Invalid Ext RO");
        document.getElementById('txt_ext_ro').focus();
    }
    else if($('#txt_ro_date').val() == ""){
        alert("Please select RO date.");
        $('#txt_ro_date').focus();
    }
    else if(!checkSpecialCharacter($('#sel_client_contact_name').val())){
        alert('Invalid Client Contact Name ');
        $('#sel_client_contact').focus();
    }
    else{
        var external_ro = encodeURIComponent($('#txt_ext_ro').val().replace(/\//g, '~'));
        var client = encodeURIComponent("<?php echo $ro_details[0]['client']?>");
        window.location = "<?php echo ROOT_FOLDER ?>/non_fct_ro/add_edit_client_contact/"+client+"/"+$('#sel_client_contact_name').val()+"/"+external_ro+'/'+$('#txt_ro_date').val()+'/'+$('#txt_agency').val()+'/'+$('#txt_agency_contact').val()+"/<?php echo $id ?>";
    }
}
function add_agency_contact(){
    var ext_ro = document.getElementById('txt_ext_ro').value;
    var check_input = Utility.checkInput4(ext_ro);

    if(!check_input)	{
        alert("Invalid Ext RO");
        document.getElementById('txt_ext_ro').focus();
    }
    else if($('#txt_ro_date').val() == ""){
        alert("Please select RO date.");
        $('#txt_ro_date').focus();
    }
    else if(!checkSpecialCharacter($('#sel_agency_contact_name').val())){
        alert('Invalid Agency Contact Name ');
        $('#sel_agency_contact').focus();
    }
    else if(!checkSpecialCharacter($('#sel_client_contact_name').val())){
        alert('Invalid Client Contact Name ');
        $('#sel_client_contact').focus();
    }
    else{
        var external_ro = encodeURIComponent($('#txt_ext_ro').val().replace(/\//g, '~'));
        var client = encodeURIComponent("<?php echo $ro_details[0]['client']?>");
        window.location = "<?php echo ROOT_FOLDER ?>/non_fct_ro/add_edit_agency_contact/"+"<?php echo $ro_details[0]['agency']?>/"+$('#sel_agency_contact_name').val()+'/'+external_ro+'/'+$('#txt_ro_date').val()+"/"+client+"/"+$('#sel_client_contact_name').val()+"/<?php echo $id ?>";
    }
}

function checkSpecialCharacter(a) {
//97 122 65 90 48 57 46 95
    for(var i = 0; i < a.length; i++){
        if((a.charCodeAt(i) >= 97 && a.charCodeAt(i) <= 122) ||
            (a.charCodeAt(i) >= 65 && a.charCodeAt(i) <= 90) ||
            (a.charCodeAt(i) >= 48 && a.charCodeAt(i) <= 57) ||
            (a.charCodeAt(i) === 32||a.charCodeAt(i) === 10 ||a.charCodeAt(i) === 09)){
        }
        else{
            return false;
        }
    }
    return true;
}
</script>

<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>

<script type="text/javascript">

function calculate_final_amount(){
    var monthly_amount = 0;
    var final_amount = 0;
    $(".fin_amount").each(function (){//alert(this.value)

        monthly_amount = parseFloat(this.value);

        final_amount += monthly_amount;
    });
    $('#txt_gross').val(final_amount.toFixed(2));
}

</script>
