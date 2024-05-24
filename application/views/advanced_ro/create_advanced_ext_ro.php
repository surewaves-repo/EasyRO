<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?> 
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/style_multiselect.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" />

			<div class="block small center login" style="margin:0px; width:870px;">		
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Submit Advanced External RO</h2>
				</div>	
				
				<div class="block_content">
				<form action="<?php echo ROOT_FOLDER ?>/advanced_ro_manager/post_create_advanced_ext_ro" method="post" enctype="multipart/form-data">
					<table cellpadding="0" cellspacing="0" width="100%">
                    	<tr>
							<td colspan="4"><?php if(isset($result) && $result == 'exists'){echo '<span style="color:#F00;">Agency all ready exists for this RO.</span>';}?>
                            <?php if(isset($client_result) && $client_result == 'exists'){echo '<span style="color:#F00;">Client all ready exists for this RO.</span>';}?>
                            <?php if(isset($brand_result) && $brand_result == 'exists'){echo '<span style="color:#F00;">Brand all ready exists</span>';}?>
                            </td>
                            
                        </tr>	
                    	<tr>
							<td width="30%">External RO Number<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_ext_ro" name="txt_ext_ro" style="width:220px;" value="<?php if(isset($cust_ro) && $cust_ro != ''){echo $cust_ro;}?>"
                                  onblur="javascript:check_for_ro_existence()"  />
                            </td>
                            <td width="40%"><span id="ext_ro_error" style="color:red;display:none">The external ro already exists</span>
                                <input type="hidden" id="ext_ro_error_flag" value="0">
                            </td>
                        </tr>
                        <tr>
							<td width="30%">RO Date<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_ro_date" readonly="readonly" name="txt_ro_date" style="width:220px;" value="<?php if(isset($ro_date)){echo $ro_date;}?>" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>					
						<tr>
							<td>Agency<span style="color:#F00;"> *</span></td>
                            <td > : </td>
                            <td >
                                <select name="sel_agency" id="sel_agency" style="width:220px;" onchange="javascript:internal_agency_validate(this.value)">
                                <?php 
                                    $internal = '';
                                    foreach($all_agency as $agencies){
					if($agencies['internal_agency'] == 1){
                                            if(isset($internal) && !empty($internal)){
                                                $internal = $internal.",".$agencies['agency_name'];
                                            }else{
                                                $internal = $agencies['agency_name'];
                                            }
                                        }
	
					if($agency == $agencies['agency_name']){
									?>
                                    <option value="<?php echo $agencies['agency_name']?>" selected="selected"><?php echo $agencies['agency_name']?></option>
                                    <?php }else{?>
                                	<option value="<?php echo $agencies['agency_name']?>"><?php echo $agencies['agency_name']?></option>
                                <?php }}?>
                                </select>
                                <input type="hidden" id="internal_ag_val" value="<?php echo $internal; ?>">
                                <input type="hidden" id="int_ag_flag" value="">
                            </td>
                            <td><a href="javascript:add_agency()">Add Agency</a></td>
                        </tr>
                        <!--<tr>
							<td>Agency Billing Address</td>
                            <td >:</td>
                            <td colspan="2"><input type="text" name="txt_agency_billing_address" id="txt_agency_billing_address" style="width:220px;" /></td>
                        </tr>
                        <tr>
							<td>Agency Contact Number</td>
                            <td >:</td>
                            <td colspan="2"><input type="text" name="txt_agency_contact_no" id="txt_agency_contact_no" style="width:220px;" /> </td>
                        </tr>-->
                        <tr>
							<td>Agency Contact<span style="color:#F00;"> *</span></td>
                            <td >:</td>
                            <td >
                            	<select name="sel_agency_contact" id="sel_agency_contact" style="width:220px;">
                                	<option value="new">New</option>
                            	</select>
                            </td>
                            <td width="50%"><a href="javascript:add_agency_contact()">Add/Edit</a></td>
                        </tr>
                        <tr>
                            <td width="20%">Client<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                            	<select name="sel_client" id="sel_client" style="width:220px;">
                                	<?php foreach($all_client as $advs){
									if($client == $advs['advertiser']){
									?>
                                    <option value="<?php echo $advs['advertiser']?>" selected="selected"><?php echo $advs['advertiser']?></option>
                                    <?php }else{?>
                                	<option value="<?php echo $advs['advertiser']?>"><?php echo $advs['advertiser']?></option>
                                <?php }}?>
                                </select>
                            </td>
                            <td width="50%"><!--<a href="javascript:add_client()">Add Client</a>-->
                            <a href="javascript:activate_client()">Activate Client</a></td>
						</tr>
                        <tr>
							<td>Client Contact<span id="int_valid" style="display:none;color:#F00;"> *</span></td>
                            <td >:</td>
                            <td >
                            	<select name="sel_client_contact" id="sel_client_contact" style="width:220px;">
                                	<option value="new">New</option>
                            	</select>
                            </td>
                            <td width="50%"><a href="javascript:add_client_contact()">Add/Edit</a></td>
                        </tr>
                       <!-- <tr>
							<td>Client Billing Address</td>
                            <td >:</td>
                            <td colspan="2"><input type="text" name="txt_client_billing_address" id="txt_client_billing_address" style="width:220px;" /></td>
                        </tr>
                        <tr>
							<td>Client Contact Number</td>
                            <td >:</td>
                            <td colspan="2"><input type="text" name="txt_client_contact_no" id="txt_client_contact_no" style="width:220px;" /> </td>
                        </tr>-->
                        <tr>
                            <td width="20%">Brand<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                            	<select name="sel_brand" id="sel_brand" style="width:220px;" multiple="multiple">
                                </select>
                            </td>
                            <td width="50%"><!--<a href="javascript:add_brand()">Add Brand</a>-->
                            <div id="brand_loader" style="background-color:#dfeffc;border-radius:5px;padding:5px;display:none;font-weight:bold;font-family: Lucida Grande,Lucida Sans,Arial,sans-serif;color: #2e6e9e;">Loading...</div>
                            <span id="brand_error" style="color:red;display:none">Please Select a Brand</span>
                            </td>
						</tr>
                        <!--<tr>
							<td width="30%">Industry</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_industry" name="txt_industry" style="width:220px;" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
							<td width="30%">Category</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_category" name="txt_category" style="width:220px;" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>-->	
						<tr>
                            <td width="20%">Make Good Type<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="" colspan="2">
                            	<input type="radio" name="rd_make_good" value="0" checked="checked" />&nbsp;Auto MakeGood &nbsp;&nbsp;
                                <input type="radio" name="rd_make_good" value="1"  />&nbsp;Client Confirmed MakeGood &nbsp;&nbsp;
                                <input type="radio" name="rd_make_good" value="2"  />&nbsp;No MakeGood
                            </td>
                            
						</tr>
						<tr>
							<td width="30%">Account Manager Name<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_am_name" name="txt_am_name" style="width:220px;" readonly="readonly" value="<?php if(isset($user_name)){echo $user_name;}else{echo $logged_in_user['user_name'];} ?>" />
                                <input type="hidden" id="hid_user_id" name="hid_user_id" style="width:220px;" value="<?php if(isset($user_id)){echo $user_id;}else{echo $logged_in_user['user_id'];} ?>" />
                                <input type="hidden" id="user_type" name="user_type" value="<?php echo $logged_in_user['is_test_user']; ?>" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="30%">Content Name(Spot)</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_content_name_spot" name="txt_content_name_spot" style="width:220px;" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="30%">Content Duration(Spot)</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_content_duration_spot" name="txt_content_duration_spot" style="width:220px;" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="30%">Content Name(Banner)</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_content_name_banner" name="txt_content_name_banner" style="width:220px;" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="30%">Content Duration(Banner)</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_content_duration_banner" name="txt_content_duration_banner" style="width:220px;" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
							<td width="30%">Campaign Start Date<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_camp_start_date" readonly="readonly" name="txt_camp_start_date" style="width:220px;" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
							<td width="30%">Campaign End Date<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_camp_end_date" readonly="readonly" name="txt_camp_end_date" style="width:220px;" />
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
                                <select name="regionSelectBox" id="regionSelectBox" style="width:220px;">
                                    <?php foreach( $regionArray as $region){ ?>

                                    <option value="<?php echo $region['region_id']?>" ><?php echo $region['region_name']?>
                                    </option>

                                    <?php } ?>
                                </select>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
							<td width="30%">Markets<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <select multiple="multiple" id="sel_market" name="sel_market" style="width:240px;">
                                    <optgroup label="Market Clusters">
                                    <?php foreach($all_clusters as $clusters){?>
                                        <option value="<?php echo $clusters['sw_market_name']?>"><?php echo $clusters['sw_market_name']?></option>
                                    <?php }?>
                                    </optgroup>

                                    <optgroup label="Markets">
                                    <?php foreach($all_markets as $markets){?>
                                        <option value="<?php echo $markets['sw_market_name']?>"><?php echo $markets['sw_market_name']?></option>
                                    <?php } ?>
                                    </optgroup>
                                </select>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <!--<tr>
							<td width="30%">Booking Year and Month</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_booking" name="txt_booking" style="width:220px;" readonly="readonly" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>-->
                        <tr style="display:none;" id="market_tr">
							<td colspan="4" style="padding:0px;">
                            <div id="market_div">
                            </div>
                            </td>
                        </tr>
                        
                        
                        <tr>
							<td width="30%">Gross RO Amount<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_gross" name="txt_gross" style="width:220px;" onblur="calculate_net_after_commission()" onkeyup="price_check(this.value)" value="0" readonly="readonly" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
							<td width="30%">Agency Commission Payable</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" id="txt_agency_com" name="txt_agency_com" style="width:220px;" onblur="calculate_net_after_commission()" value="0" onkeyup="price_check(this.value)" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>
							<td width="30%">Net After Agency Commission</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="text" readonly="readonly" id="txt_net_agency_com" name="txt_net_agency_com" style="width:220px;" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr> 
                        <tr>
							<td width="30%">Special Instructions</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <!--<input type="text" id="txt_spcl_inst" name="txt_spcl_inst" style="width:220px;" placeholder="Any instruction including Billing Instruction" />-->
                                <textarea id="txt_spcl_inst" name="txt_spcl_inst" style="width:207px; height:70px;" placeholder="Any instruction including Billing Instruction"></textarea>
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
						<!--
                        <tr>
                            <td>Order history recipient mail id/s<span id="OrderHRIds" style="display:none;color:#F00;"> *</span></td>
                            <td >:</td>
                            <td >
                                <select name="Order_history_recipient_ids[]" id="Order_history_recipient_ids" style="width:220px;" multiple="multiple">
                                </select>
                            </td>

                        </tr>
						-->
                        <tr>
							<td width="30%">Attach RO<span style="color:#F00;"> *</span></td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="file" id="file_pdf" name="file_pdf" class="file_class" />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
						
						<tr>	
							<td> 
                            <input type="hidden" name="hid_brand" id="hid_brand" />
                            <input type="hidden" name="hid_market" id="hid_market" />
                            <input type="submit" class="submit" value="Create" onclick="return check_form();" />	
                            </td>						
						<tr>
					
					</table>
						
						
					</form>
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			
<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>

<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.multiselect.js"></script>

<script type="text/javascript" language="javascript">
function add_agency(){
		var ext_ro = document.getElementById('txt_ext_ro').value;
		var check_input = Utility.checkInput4(ext_ro);
	
		if($('#txt_ext_ro').val() == ""){
			alert("Please enter External RO Number.");
			$('#txt_ext_ro').focus();
		}
		else if(!check_input)	{
			alert("Invalid Ext RO");
			document.getElementById('txt_ext_ro').focus();
		}
		else if($('#txt_ro_date').val() == ""){
			alert("Please select RO date.");
			$('#txt_ro_date').focus();
		}
		else{
			var external_ro = encodeURIComponent($('#txt_ext_ro').val().replace(/\//g, '~'));
			window.location = "<?php echo ROOT_FOLDER ?>/account_manager/add_agency/"+external_ro+"/"+$('#txt_ro_date').val();
		}
}
function internal_agency_validate(agency_name){
        var internal_agency_name = $('#internal_ag_val').val();
        var int_ag_array = internal_agency_name.split(",");
        if(!$.inArray(agency_name, int_ag_array)){
            $('#int_valid').show();
            $('#int_ag_flag').val(1);
        }else{
            $('#int_valid').hide();
            $('#int_ag_flag').val(0);
        }
}
function add_client(){
		if($('#txt_ext_ro').val() == ""){
			alert("Please enter External RO Number.");
			$('#txt_ext_ro').focus();
		}
		else{
			window.location = "<?php echo ROOT_FOLDER ?>/account_manager/add_client/"+$('#txt_ext_ro').val();
		}
}
function activate_client(){
	var ext_ro = document.getElementById('txt_ext_ro').value;
	var check_input = Utility.checkInput4(ext_ro);
	var test_for_char = Utility.checkInput1($('#sel_agency').val());
	
	if($('#txt_ext_ro').val() == ""){
		alert("Please enter External RO Number.");
		$('#txt_ext_ro').focus();
	}
	else if(!check_input)	{
		alert("Invalid Ext RO");
		document.getElementById('txt_ext_ro').focus();
	}
	else if($('#txt_ro_date').val() == ""){
		alert("Please select RO date.");
		$('#txt_ro_date').focus();
	}
	else if(!test_for_char)	{
		alert("Invalid Agency Name");
		$('#sel_agency').focus();
	}
	else if(!checkSpecialCharacter($('#sel_agency_contact').val())){
		alert('Invalid Agency Contact Name ');
		$('#sel_agency_contact').focus();
	}
	else{
		var external_ro = encodeURIComponent($('#txt_ext_ro').val().replace(/\//g, '~'));
		window.location = "<?php echo ROOT_FOLDER ?>/account_manager/activate_client/"+external_ro+"/"+$('#txt_ro_date').val()+"/"+$('#sel_agency').val()+"/"+$('#sel_agency_contact').val();
	}
}
function add_brand(){
	if($('#txt_ext_ro').val() == ""){
			alert("Please enter External RO Number.");
			$('#txt_ext_ro').focus();
		}
		else{
			brand_id = $('#sel_brand option:first').val()
			window.location = "<?php echo ROOT_FOLDER ?>/account_manager/add_brand/"+$('#txt_ext_ro').val()+'/'+brand_id;
			}
}
function price_check(value){//alert(value)
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
	/*if(!regex.test(gross)){
		alert('Please enter valid amount.');
		document.getElementById('txt_gross').value = "";
		document.getElementById('txt_gross').focus();
	}*/
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
function price_check_for_each_market(id,value){//alert(value)
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
function price_check_for_each_market_on_blur(id,value){//alert(id)
	var regex = /^[0-9.]+$/;
	var gross = document.getElementById(id).value;
	agency_com = document.getElementById('txt_agency_com').value;
	
	var test_amount = Utility.checkAmount(gross);
	if(!test_amount)	{
		//alert("Invalid Amount");
		document.getElementById(id).value = 0;
		document.getElementById(id).focus();
		return false;
	}
	if(value.indexOf('.') == 0 || value.lastIndexOf('.') == value.length - 1){
		alert("Invalid Amount");
		document.getElementById(id).value = 0;
		return false;
	}
}

    /********************************** Validation for FCT ***/

    function validateFCTNumber(id,value){//alert(id)

        var regex = /^\d+$/;
        var fctValue = document.getElementById(id).value;

        if(!regex.test(fctValue)){

            alert("Invalid Number Please Enter Only Integer..!") ;
            document.getElementById(id).value = 0;
            document.getElementById(id).focus();
            return false;
        }

    }
    /*********************************** Validation For FCT *********/

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
	
	if($('#txt_ro_date').val() == ""){
		alert("Please select RO date.");
		$('#txt_ro_date').focus();
		return false;
	}
	if($('#sel_agency_contact').val() == "new"){
		alert("Please select agency contact name.");
		$('#sel_agency_contact').focus();
		return false;
	}
    if($('#int_ag_flag').val() == 1){
        if($('#sel_client_contact').val() == "new"){
            alert("Please select client contact.");
            $('#sel_client_contact').focus();
            return false;
        }
    }
	if($('#sel_brand').val() == null){
		$('#brand_error').show();
        $('#sel_brand').focus();
        return false;
	}
    if($('#ext_ro_error_flag').val() == 1){
        $('#ext_ro_error').show();
        $('#txt_ext_ro').focus();
        return false;
    }else{
        $('#ext_ro_error').hide();
    }

    if($('#txt_content_name_spot').val() == ""){
        if($('#txt_content_name_banner').val() == ""){
            alert("Please enter content name(Spot/Banner).");
            $('#txt_content_name_spot').focus();
            return false;
        }
        /*alert("Please enter content name(spot).");
        $('#txt_content_name_spot').focus();
        return false;*/
    }else{
        var content_name_spot = document.getElementById('txt_content_name_spot').value;
        //check input for alphanumeric and -  _  /  . sp
        var check_input = Utility.checkInput4(content_name_spot);
        if(!check_input)	{
            alert("Invalid content name(spot)");
            document.getElementById('txt_content_name_spot').focus();
            return false;
        }

        //validate content spot duration only if content spot name is there
        if($('#txt_content_duration_spot').val() == ""){
            alert("Please enter content duration(spot).");
            $('#txt_content_duration_spot').focus();
            return false;
        }else{
            var content_duration_spot = document.getElementById('txt_content_duration_spot').value;
            var check_duration = Utility.checkAmount(content_duration_spot);
            if(!check_duration)	{
                alert("Invalid content duration(spot)");
                document.getElementById('txt_content_duration_spot').value = "";
                document.getElementById('txt_content_duration_spot').focus();
                return false;
            }
        }
    }


    if($('#txt_content_name_banner').val() == ""){
        if($('#txt_content_name_spot').val() == ""){
            alert("Please enter content name(Spot/Banner).");
            $('#txt_content_name_spot').focus();
            return false;
        }

    }else{
        var content_name_banner = document.getElementById('txt_content_name_banner').value;
        //check input for alphanumeric and -  _  /  . sp
        var check_input = Utility.checkInput4(content_name_banner);
        if(!check_input)	{
            alert("Invalid content name(Banner)");
            document.getElementById('txt_content_name_banner').focus();
            return false;
        }

        //validate content banner duration only if content banner name is there
        if($('#txt_content_duration_banner').val() == ""){
            alert("Please enter content duration(Banner).");
            $('#txt_content_duration_banner').focus();
            return false;
        }else{
            var content_duration_banner = document.getElementById('txt_content_duration_banner').value;
            var check_duration = Utility.checkAmount(content_duration_banner);
            if(!check_duration)	{
                alert("Invalid content duration(Banner)");
                document.getElementById('txt_content_duration_banner').value = "";
                document.getElementById('txt_content_duration_banner').focus();
                return false;
            }
        }

    }








	if($('#txt_camp_start_date').val() == ""){
		alert("Please select start date.");
		$('#txt_camp_start_date').focus();
		return false;
	}
	if($('#txt_camp_end_date').val() == ""){
		alert("Please select end date.");
		$('#txt_camp_end_date').focus();
		return false;
	}

    if($('#sel_market').val() == null){
        alert("Please select market.");
        $('#sel_market').focus();
        return false;
    }


        /* Validating FCT */

        var returnArray = validateFCTAccordingToImpressions() ;

        if( returnArray[1] == "F" ){
            alert( returnArray[0] ) ;
            return false ;
        }

        /* Validating FCT */

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
	if($('#file_pdf').val() == ""){
		alert("Please select RO attachment.");
		$('#file_pdf').focus();
		return false;
	}
	if($('#file_pdf').val() != ""){
		file_path = $('#file_pdf').val().replace(/^C:\\fakepath\\/i, '');
		file_regex = /[^a-zA-Z0-9 ._-]/;
		if (file_regex.test(file_path)) {
			alert("Special characters are not allowed in RO attachment");
			return false;
		}
	}


        /*if($('#file_pdf').val() != ""){
            if($('#file_pdf').val().substring($('#file_pdf').val().lastIndexOf('.')+1).toLowerCase() != 'pdf' && $('#file_pdf').val().substring($('#file_pdf').val().lastIndexOf('.')+1).toLowerCase() != 'xlsx' && $('#file_pdf').val().substring($('#file_pdf').val().lastIndexOf('.')+1).toLowerCase() != 'xls' && $('#file_pdf').val().substring($('#file_pdf').val().lastIndexOf('.')+1).toLowerCase() != 'csv'){
                alert("Please select pdf/excel/csv file.");
                return false;
            }
        }*/
	//parent.jQuery.colorbox.close();
	//alert($("select#hid_brand").val());return;
	$('#hid_brand').val($("select#sel_brand").val());//alert('suc');return false;
	$('#hid_market').val($("select#sel_market").val());
}

$("#sel_agency").change(function(){
		$.post("<?php echo ROOT_FOLDER ?>/account_manager/get_agency_info",{ agency: $("#sel_agency").val()},
		 	function(data) {
			$( "#sel_agency_contact" ).html( data );
                buildMailIds() ;
		});
		
	});
$("#sel_client").change(function(){
		$.post("<?php echo ROOT_FOLDER ?>/account_manager/get_client_info",{ client: $("#sel_client").val()},
		 	function(data) {
			$( "#sel_client_contact" ).html( data );
		});
        $("#brand_error").hide();
        $("#brand_loader").show();
        jQ_m("select#sel_brand").multiselect('disable');
        $.post("<?php echo ROOT_FOLDER ?>/account_manager/get_brand_ajax",{ adv: $("#sel_client").val()},
		 	function(data) {
			$( "#sel_brand" ).html( data );
			force_brand_multiple();
            jQ_m("select#sel_brand").multiselect('enable');
            $("#brand_loader").hide();
        });
		
	});
function add_agency_contact(){
	var ext_ro = document.getElementById('txt_ext_ro').value;
	var check_input = Utility.checkInput4(ext_ro);
	var test_for_char = Utility.checkInput1($('#sel_agency').val());
	if($('#txt_ext_ro').val() == ""){
			alert("Please enter External RO Number.");
			$('#txt_ext_ro').focus();
	}
	else if(!check_input)	{
		alert("Invalid Ext RO");
		document.getElementById('txt_ext_ro').focus();
	}
	else if($('#txt_ro_date').val() == ""){
			alert("Please select RO date.");
			$('#txt_ro_date').focus();
	}
	else if(!test_for_char)	{
		alert("Invalid Agency Name");
		$('#sel_agency').focus();
	}
	else if(!checkSpecialCharacter($('#sel_agency_contact').val())){
		alert('Invalid Agency Contact Name ');
		$('#sel_agency_contact').focus();
	}
	else{
		var external_ro = encodeURIComponent($('#txt_ext_ro').val().replace(/\//g, '~'));
		window.location = "<?php echo ROOT_FOLDER ?>/account_manager/add_edit_agency_contact/"+"/"+$('#sel_agency').val()+"/"+$('#sel_agency_contact').val()+'/'+external_ro+'/'+$('#txt_ro_date').val();
	}
}
function add_client_contact(){
	var ext_ro = document.getElementById('txt_ext_ro').value;
	var check_input = Utility.checkInput4(ext_ro);
	var test_for_char = Utility.checkInput1($('#sel_agency').val());
	
	if($('#txt_ext_ro').val() == ""){
			alert("Please enter External RO Number.");
			$('#txt_ext_ro').focus();
	}
	else if(!check_input)	{
		alert("Invalid Ext RO");
		document.getElementById('txt_ext_ro').focus();
	}
	else if($('#txt_ro_date').val() == ""){
		alert("Please select RO date.");
		$('#txt_ro_date').focus();
	}
	else if(!test_for_char)	{
		alert("Invalid Agency Name");
		$('#sel_agency').focus();
	}
	else if(!checkSpecialCharacter($('#sel_agency_contact').val())){
		alert('Invalid Agency Contact Name ');
		$('#sel_agency_contact').focus();
	}
	else if(!checkSpecialCharacter($('#sel_client_contact').val())){
		alert('Invalid Client Contact Name ');
		$('#sel_client_contact').focus();
	}
	else{
		var external_ro = encodeURIComponent($('#txt_ext_ro').val().replace(/\//g, '~'));
		var client = encodeURIComponent($('#sel_client').val());
		window.location = "<?php echo ROOT_FOLDER ?>/account_manager/add_edit_client_contact/"+client+"/"+$('#sel_client_contact').val()+"/"+external_ro+'/'+$('#txt_ro_date').val()+'/'+$('#sel_agency').val()+'/'+$('#sel_agency_contact').val();
	}
}

$(document).ready(function(){

    /*
    $.post("<?php echo ROOT_FOLDER ?>/account_manager/get_agency_info",{ agency: $("#sel_agency").val()},

		 	function(data) {
			$( "#sel_agency_contact" ).html( data );
			$("#sel_agency_contact option[value='<?php echo $agency_contact;?>']").attr('selected', 'selected');
		});

	$.post("<?php echo ROOT_FOLDER ?>/account_manager/get_client_info",{ client: $("#sel_client").val()},
		 	function(data) {
			$( "#sel_client_contact" ).html( data );
			$("#sel_client_contact option[value='<?php echo $client_contact;?>']").attr('selected', 'selected');
		});
	$.post("<?php echo ROOT_FOLDER ?>/account_manager/get_brand_ajax",{ adv: $("#sel_client").val()},
		 	function(data) {
			$( "#sel_brand" ).html( data );
			force_brand_multiple();
		});

	*/

    $.ajax({
        type: "POST",
        data: 'agency='+$("#sel_agency").val(),
        async: false,
        url: "<?php echo ROOT_FOLDER ?>/account_manager/get_agency_info",
        success:function(data){

            $( "#sel_agency_contact" ).html( data );
            $("#sel_agency_contact option[value='<?php echo $agency_contact;?>']").attr('selected', 'selected');
        }
    });

    $.ajax({
        type: "POST",
        data: 'client='+$("#sel_client").val(),
        async: false,
        url: "<?php echo ROOT_FOLDER ?>/account_manager/get_client_info",
        success:function(data){
                $( "#sel_client_contact" ).html( data );
                $("#sel_client_contact option[value='<?php echo $client_contact;?>']").attr('selected', 'selected');
        }
    });

    $.ajax({
        type: "POST",
        data: 'adv='+$("#sel_client").val(),
        async: false,
        url: "<?php echo ROOT_FOLDER ?>/account_manager/get_brand_ajax",
        success:function(data){

            $( "#sel_brand" ).html( data );
            force_brand_multiple();
        }
    });
    $( "#txt_ro_date" ).datepicker({
		//defaultDate: "+1w",
		dateFormat: "yy-mm-dd",
		changeMonth: true
		/*onClose: function( selectedDate ) {
			$( "#txt_camp_start_date" ).datepicker( "option", "minDate", selectedDate );
			$( "#txt_camp_end_date" ).datepicker( "option", "minDate", selectedDate );
		}*/
	}).datepicker("option", "maxDate", "-1");
	$( "#txt_camp_start_date" ).datepicker({
		//defaultDate: "+1w",
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		onClose: function( selectedDate ) {
			$( "#txt_camp_end_date" ).datepicker( "option", "minDate", selectedDate );
		}
	}).datepicker("option", "maxDate", "-1");
	$( "#txt_camp_end_date" ).datepicker({
		//defaultDate: "+1w",
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		onClose: function( selectedDate ) {
			//$( "#txt_camp_start_date" ).datepicker( "option", "minDate", selectedDate );
			$( "#txt_camp_start_date" ).datepicker( "option", "maxDate", selectedDate );
		}
	}).datepicker("option", "maxDate", "-1");


    /*--------------------------------- Order history recipient ids--------------------*/

    jQ_m("select#Order_history_recipient_ids").multiselect();
    buildMailIds() ;
});


<!--Added by Lokanath for multiselect plugin-->

var jQ_m = jQuery.noConflict();
jQ_m.noConflict();
var market_array = new Array();
var market_val;
jQ_m(function(){
	jQ_m("select#sel_market")
		.filter(".single")
		.multiselect({
			multiple: false,
			noneSelectedText: 'Please select a radio',
			header: false
		})
		.end()
		.not(".single")
		.multiselect({
			noneSelectedText: 'Please select markets',
			close: function(){
				market_option = jQ_m("select#sel_market").val();
				if(market_option == null){
					market_option = '';
				}
				market_option = market_option.toString();//alert(market_option)
				market_option_array = market_option.split(',');//alert(market_option_array.length)
				$('#market_tr').show();
				table_html = '<table width="100%" ><tr><td colspan="8" nowrap="nowrap" style="border-bottom:none;">Market wise RO Amount <span style="color:red;">(If you enter amount \'0\' for both spot and banner for a market then it will be discarded)</span></td></tr><tr>';
				for(i=0;i<market_option_array.length;i++){
					// creating array
					id = market_option_array[i].toString();
					id = id.split(" ").join("_");
					//market_val = $('#'+id).val();
					market_val_spot = $('#'+id+'_spot').val();
					market_val_banner = $('#'+id+'_banner').val();//alert(market_val_spot);alert(market_val_banner);

                    market_array[id+'_spot']=market_val_spot;
					market_array[id+'_banner']=market_val_banner;

                    /********************** Adding code for FCT */

                    market_val_spot_FCT = $('#'+id+'_spot_FCT').val();
                    market_val_banner_FCT = $('#'+id+'_banner_FCT').val();//alert(market_val_spot);alert(market_val_banner);

                    market_array[id+'_spot_FCT']=market_val_spot_FCT;
                    market_array[id+'_banner_FCT']=market_val_banner_FCT;

                    /******************** Adding code for FCT */
					// end
					/*if( i==2 || (i%2==0 && i!=0) ){
						table_html += '</tr><tr>';
					}
					*/
					
					if(market_array[id+'_spot'] != null){//alert('nt eq null')

                        table_html += '<tr>';
                        table_html += '<td nowrap="nowrap" width="10%" style="border-bottom:none;padding:0px;text-align:right">'+market_option_array[i]+'<span style="color:#F00;"> *</span></td><td width="5%" nowrap="nowrap" style="border-bottom:none;padding:0px;"> : </td><td width="15%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot <input type="text" id="'+id+'_spot" name="markets['+id+'][spot]" style="width:80px;" onblur="price_check_for_each_market_on_blur(\''+id+'_spot\',this.value);calculate_gross_and_net_after_commission(\''+id+'_spot\','+market_option_array.length+')" onkeyup="price_check_for_each_market(\''+id+'_spot\',this.value)" value="'+market_array[id+'_spot']+'" /></td>';
					}
					else{

                        table_html += '<tr>';
                        table_html += '<td nowrap="nowrap" width="10%" style="border-bottom:none;padding:0px;text-align:right">'+market_option_array[i]+'<span style="color:#F00;"> *</span></td><td width="5%" nowrap="nowrap" style="border-bottom:none;padding:0px;"> : </td><td width="15%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot <input type="text" id="'+id+'_spot" name="markets['+id+'][spot]" style="width:80px;" onblur="price_check_for_each_market_on_blur(\''+id+'_spot\',this.value);calculate_gross_and_net_after_commission(\''+id+'_spot\','+market_option_array.length+')" onkeyup="price_check_for_each_market(\''+id+'_spot\',this.value)" value="0" /></td>';
					}


                    /**************************** Adding code for FCT */

                    if(market_array[id+'_spot_FCT'] != null){//alert('nt eq null')
                        table_html += '<td width="15%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot FCT<input type="text" id="'+id+'_spot_FCT" name="FCT['+id+'][spot_FCT]" style="width:80px;" onkeyup="validateFCTNumber(\''+id+'_spot_FCT\',this.value)" value="'+market_array[id+'_spot_FCT']+'" /></td>';
                    }
                    else{
                        table_html += '<td width="15%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Spot FCT<input type="text" id="'+id+'_spot_FCT" name="FCT['+id+'][spot_FCT]" style="width:80px;" onkeyup="validateFCTNumber(\''+id+'_spot_FCT\',this.value)" value="0" /></td>';
                    }
                    /*************************** Adding code for FCT */


                    if(market_array[id+'_banner'] != null){//alert('nt eq null')
                        table_html += '<td width="15%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner <input type="text" id="'+id+'_banner" name="markets['+id+'][banner]" style="width:80px;" onblur="price_check_for_each_market_on_blur(\''+id+'_banner\',this.value);calculate_gross_and_net_after_commission(\''+id+'_banner\','+market_option_array.length+')" onkeyup="price_check_for_each_market(\''+id+'_banner\',this.value)" value="'+market_array[id+'_banner']+'" /></td>';
                    }
                    else{
                        table_html += '<td width="15%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner <input type="text" id="'+id+'_banner" name="markets['+id+'][banner]" style="width:80px;" onblur="price_check_for_each_market_on_blur(\''+id+'_banner\',this.value);calculate_gross_and_net_after_commission(\''+id+'_banner\','+market_option_array.length+')" onkeyup="price_check_for_each_market(\''+id+'_banner\',this.value)" value="0" /></td>';
                    }

                    /*************************** Adding code for FCT */

                    if(market_array[id+'_banner_FCT'] != null){//alert('nt eq null')
                        table_html += '<td width="15%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner FCT<input type="text" id="'+id+'_banner_FCT" name="FCT['+id+'][banner_FCT]" style="width:80px;" onkeyup="validateFCTNumber(\''+id+'_banner_FCT\',this.value)" value="'+market_array[id+'_banner_FCT']+'" /></td>';
                        table_html += '</tr>';
                    }
                    else{
                        table_html += '<td width="15%" nowrap="nowrap" style="border-bottom:none;padding:0px;">Banner FCT<input type="text" id="'+id+'_banner_FCT" name="FCT['+id+'][banner_FCT]" style="width:80px;" onkeyup="validateFCTNumber(\''+id+'_banner_FCT\',this.value)" value="0" /></td>';
                        table_html += '</tr>';
                    }

                    /*************************** Adding code for FCT */

                }
				if(market_option_array.length % 1 == 0){
					table_html += '<td nowrap="nowrap" style="border-bottom:none;padding:0px;" colspan="4">&nbsp;</td>';
				}
				/*if(market_option_array.length % 2 == 0){
					table_html += '<td width="25%" nowrap="nowrap" style="border-bottom:none;">&nbsp;</td><td width="25%" nowrap="nowrap" style="border-bottom:none;">&nbsp;</td>';
				}
				if(market_option_array.length % 3 == 0){
					table_html += '<td width="25%" nowrap="nowrap" style="border-bottom:none;">&nbsp;</td>';
				}*/
				table_html += '</tr></table>';//alert($table_html)
				if((market_option_array[0] == '') && market_option_array.length == 1){
					$('#market_tr').hide();
				}
				else{
					$('#market_div').html(table_html);
				}
				// calculate all amount
				var gross = 0;
				var gross_for_each_market;
				var commission;
				/*for(var i=0;i<total_market;i++){
					gross += $('#'+id).val()
				}*/
				$(":text[name^='markets']").each(function (){//alert(this.value)
					//gross += this.value;
					
					gross_for_each_market = parseFloat(this.value);
					/*if(isNaN("NaN")){
						continue;
					}*/
					commission = parseFloat($('#txt_agency_com').val()) ;
					commission = commission.toFixed(2)
					gross += gross_for_each_market;
					if(commission > gross){
						alert('Agency commission can not be greater than gross ro amount.');
						$('#txt_agency_com').val("0");
						commission = 0;
					}
					if(isNaN(gross - commission)) {
						$('#txt_net_agency_com').val('0');
					}else {
						net_agency_com = (gross - commission).toFixed(2);//alert(net_agency_com)
						$('#txt_net_agency_com').val(net_agency_com);
					}
				});
				
				$('#txt_gross').val(gross.toFixed(2));
				// end calculating
				//alert(typeof(market_option))
				if(market_option == ''){//alert(1)
					$('#txt_gross').val('0');
					$('#txt_agency_com').val('0');
					$('#txt_net_agency_com').val('0');
				}
			}
		});
		
		
		});
function force_brand_multiple(){
	jQ_m("select#sel_brand")
		.filter(".single")
		.multiselect({
			multiple: false,
			noneSelectedText: 'Please select a radio',
			header: false
		})
		.end()
		.not(".single")
		.multiselect({
			noneSelectedText: 'Please select a brand'
		});
		jQ_m("select#sel_brand").multiselect("refresh");
}
function calculate_net_after_commission(){
	gross_arr = $('#txt_gross').val().split(".");
	if(gross_arr.length > 2){
		alert("Please enter valid amount");
		$('#txt_gross').val("");
		$('#txt_gross').focus();
		return false;
	}
	agency_com_arr = $('#txt_agency_com').val().split(".");
	if(agency_com_arr.length > 2){
		alert("Please enter valid amount");
		$('#txt_agency_com').val("0");
		$('#txt_agency_com').focus();
		return false;
	}
	var gross = parseFloat($('#txt_gross').val());
	var commission = parseFloat($('#txt_agency_com').val()) ;
	if(commission > gross){
		alert('Agency commission can not be greater than gross ro amount.');
		$('#txt_agency_com').val("0");
		commission = 0;
	}
	if(isNaN(gross - commission)) {
		$('#txt_net_agency_com').val('0');
	}else {
		$('#txt_net_agency_com').val((gross - commission).toFixed(2));
	}
}
function calculate_gross_and_net_after_commission(id,total_market){//alert(typeof(value))
	//alert(id);
	//alert(value)
	var market_gross_arr = $('#'+id).val().split(".");//alert(market_gross_arr.length)
	if(market_gross_arr.length > 2){
		alert("Please enter valid amount");
		$('#'+id).val("");
		$('#'+id).focus();
		return false;
	}
	if($('#'+id).val() == "."){
		alert("Please enter valid amount");
		$('#'+id).val(0);
		$('#'+id).focus();
		return false;
	}
	/*if($('#'+id).val() == 0){
		alert("Please enter valid amount");
		$('#'+id).val(0);
		$('#'+id).focus();
		return false;
	}*/
	var gross = 0;
	var gross_for_each_market;
	var commission;
	/*for(var i=0;i<total_market;i++){
		gross += $('#'+id).val()
	}*/
	$(":text[name^='markets']").each(function (){//alert(this.value)
		//gross += this.value;
		
		gross_for_each_market = parseFloat(this.value);
		/*if(isNaN("NaN")){
			continue;
		}*/
		commission = parseFloat($('#txt_agency_com').val()) ;
		commission = commission.toFixed(2)
		gross += gross_for_each_market;
		if(commission > gross){
			alert('Agency commission can not be greater than gross ro amount.');
			$('#txt_agency_com').val("0");
			commission = 0;
		}
		if(isNaN(gross - commission)) {
			$('#txt_net_agency_com').val('0');
		}else {
			net_agency_com = (gross - commission).toFixed(2);//alert(net_agency_com)
			$('#txt_net_agency_com').val(net_agency_com);
		}
	});
	$('#txt_gross').val(gross.toFixed(2));
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

function check_for_ro_existence(){
    var ext_ro = $('#txt_ext_ro').val();
    if(ext_ro == ''){
        $('#ext_ro_error').hide();
        alert("Please enter External RO Number.");
        $('#txt_ext_ro').focus();
    }else{
        $.ajax({
            type: "POST",
            data: 'ext_ro='+ext_ro,
            url: '<?php echo ROOT_FOLDER ?>/account_manager/check_for_ro_existence',
            success:function(data){
                if(data == 1){
                    $('#ext_ro_error').show();
                    $('#ext_ro_error_flag').val(1);
                }else{
                    $('#ext_ro_error').hide();
                    $('#ext_ro_error_flag').val(0);
                }
            }
        });
    }
}

    /* Build mail ids */

    function buildMailIds(){

            var idContactIdsArray = [] ;

            $('#sel_agency_contact').find('option').each(function() {

                 if( $(this).val() != 'new' ){

                     idContactIdsArray.push( $(this).val() ) ;
                 }

            });
            var contactIds = idContactIdsArray.join();

            $.ajax({
                type: "POST",
                data: 'contactIds='+contactIds,
                async: false,
                url: '<?php echo ROOT_FOLDER ?>/account_manager/getMailIdsUsingContactIds',
                success:function(data){

                    $('#Order_history_recipient_ids').find('option').remove();

                    if(data.trim() != "null"){

                        var object    = eval("(" + data + ")");

                        for(var i=0; i< object.mailId.length; i++){

                            $('#Order_history_recipient_ids').append('<option value='+object.mailId[i].id+'>'+object.mailId[i].id+'</option>');

                        }

                    }

                    jQ_m("select#Order_history_recipient_ids").multiselect("refresh");

                }
            });
    }

    function validateFCTAccordingToImpressions(){

        var returnMessageArray = [] ;

        returnMessageArray[0] = "No MSG" ;
        returnMessageArray[1] = "T" ;

        $(":text[name^='FCT']").each(function (){

            var id  = this.id ;
            var fctVal = this.value ;

            var splitId = id.split("_") ;
            var prefixIdArray = [] ;
            var postfixIdArray = [] ;

            var iToRun = splitId.length-2 ;

            for( var i = 0 ;i < splitId.length ; i++ ){

                if( i < iToRun ){

                    prefixIdArray.push( splitId[i] ) ;

                }else{

                    postfixIdArray.push( splitId[i] ) ;
                }

            }
            var finalId = prefixIdArray.join("_") ;
            var postfixId =  postfixIdArray.join("_") ;

            var spotInputId   = finalId + "_spot" ;
            var bannerInputId = finalId + "_banner" ;

            var spotInputValue = $("#"+spotInputId).val() ;
            var bannerInputValue = $("#"+bannerInputId).val() ;


            if( spotInputValue > 0 && fctVal == 0 && postfixId == "spot_FCT" ){

                var msg = "Please enter Spot FCT Value of market: "+ finalId.replace("_"," ") ;
                var type = "F" ;

                returnMessageArray[0] = msg ;
                returnMessageArray[1] = type ;

                return false ;
            }
            if( bannerInputValue > 0 && fctVal == 0 && postfixId == "banner_FCT" ){

                var msg = "Please enter Banner FCT Value of market: "+ finalId.replace("_"," ") ;
                var type = "F" ;

                returnMessageArray[0] = msg ;
                returnMessageArray[1] = type ;

                return false ;

            }

        });
        return returnMessageArray ;
    }
</script>
