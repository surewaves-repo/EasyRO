<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
<style type="text/css">
    .block table tr td, .block table tr th {
        border-bottom :0px !important;
    }
    #overlay {
width:100%;
height:100%;
opacity:1.95;
top:0;
left:0;
display:none;
position:fixed;
background-color:#313131;
overflow:auto
}
img#close {
position:absolute;
right:-14px;
top:-14px;
cursor:pointer
}
div#popupContact {
position:absolute;
left:50%;
top:17%;
margin-left:-202px;
font-family:'Raleway',sans-serif
max-width:500px;
min-width:325px;
padding:10px 9px;
border:2px solid gray;
border-radius:10px;
font-family:raleway;
background-color:#fff
}
#selectExistingAdvertiser { 
    padding: 7px 13px;
    font-size: 18px;
    text-align: center;
    cursor: pointer;
    outline: none;
    color: #fff;
    background-color: #219AE7;
    border: none;
    border-radius: 5px;

  
}
#overlay #popupContact .block_head .bheadl {
    width: 20px;
    height: 54px;
    float: left;
    background: url(../images/bheadl.gif) top left no-repeat;
}
#overlay #popupContact .block_head {
    background: url(../images/bhead.gif) 0 0 repeat-x;
    height: 54px;
line-height: 54px;
background: url(../images/bhead.gif) 0 0 repeat-x;
overflow: hidden;
}
#overlay #popupContact .block_head .bheadl {
    width: 20px;
    height: 54px;
    float: left;
    background: url(../images/bheadl.gif) top left no-repeat;
}
#overlay #popupContact .block_head .bheadr {
    width: 20px;
    height: 54px;
    float: right;
    background: url(../images/bheadr.gif) top right no-repeat;
}
.submit {
    width: 85px;
    height: 30px;
    line-height: 30px;
    background: url(../images/btns.gif) top center;
    border: 0;
    font-family: "Titillium800", "Trebuchet MS", Arial, sans-serif;
    font-size: 14px;
    font-weight: normal;
    text-transform: uppercase;
    color: #fff;
    text-shadow: 1px 1px 0 #0a5482;
    cursor: pointer;
    margin-right: 10px;
    vertical-align: middle;
}
#overlay #popupContact .block_head h2 {
    font-family: "Titillium999", "Trebuchet MS", Arial, sans-serif;
    font-size: 18px;
    font-weight: normal;
    text-transform: uppercase;
    color: #555;
    text-shadow: 1px 1px 0 #fff;
    float: left;
}
.footer{
    margin-left: 30px;
    margin-top: 10px;
}
.popupContact table{
    margin-top: 10px;
    margin-left: 35px;
}
#header #nav, #header #nav * {
     z-index: 0 !important; 
}
input[type="radio"][readonly] {
  pointer-events: none;
}
</style>

<div class="" style="margin:0px; width:870px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin:0">Add / Edit Advertiser</h2>
    </div>
      <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/utility/post_add_advertiser" id="advertiser_form" method="post" enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="12" width="100%">
            <?php if(($activeTab==3) && isset($validation_errors) && $validation_errors!="" ) {?>
                <tr>
                	<td class="errors" colspan="3"><?php echo $validation_errors ?></td>
                </tr>
            <?php }?>
            <?php if(($activeTab==3) && isset($success_msg) && $success_msg!="" ) {?>
                <tr>
                	<td class="success" colspan="3"><?php echo $success_msg ?></td>
                </tr>
            <?php }?>
                <tr>
                    <td>Category<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                        <select name="advertiser_category" id="advertiser_category" class="chosen-select" style="width:220px;" onchange="javascript:getProducts(this.value, 'advertiser')">
                             <option value=""></option>
							<?php foreach($CategoryList as $key=> $val){ ?>
                            <option value="<?php echo $val['category_id']?>">
                                <?php echo $val[ 'category_name']?>
                            </option>
                            <?php }?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Product<span style="color:#F00;"> *</span>
                    </td>
                    <td>:</td>
                    <td>
                        <select name="advertiser_product" id="advertiser_product" class="chosen-select" style="width:220px;" onchange="javascript:getAdvertiser(this.value, 'advertiser')">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Advertiser
                    </td>
                    <td> : </td>
                    <td>
                        <select name="advertiser_advertiser" id="advertiser_advertiser" class="chosen-select" style="width:220px;" onchange="javascript:getAdvertiserDisplays(this.value);resetAd(0);$('#addNewAdvertiser').val(this.options[this.selectedIndex].text)">
                        </select>
                        <!-- <input type="button" class="edit" value="Edit" onclick="editAdvertiser();"/>-->
                    </td>
                </tr>

                <tr>
                    <td>Advertiser Display Name
                    </td>
                    <td> : </td>
                    <td>
                        <select name="advertiser_display_advertiser" id="advertiser_display_advertiser" class="chosen-select" style="width:220px;" onchange="javascript:getAdvertiserDetails(this.value)">
                        </select>
                        <!-- <input type="button" class="edit" value="Edit" onclick="editAdvertiser();"/>-->
                    </td>
                </tr>

                 <tr>
                	<td colspan="3" style="text-align:center">(OR)</td>
                </tr>
                <tr>
                	<td><span id="AdLbl"> Advertiser</span> <span style="color:#F00;"> *</span></td>
                	 <td> : </td>
                    <td>
                    	<input type="text" name="addNewAdvertiser" id="addNewAdvertiser" value="" onblur="checkAdvertiserName()" />
                    	<input type="hidden" name="AdvertiserID" id="AdvertiserID" value="0" />
                    </td>
                    <td rowspan="2" style="border-left: 1px solid #ccc;"><input style="margin-left: 9%" type="button" value="Select existing Advertiser" onclick="callForAddingExistingAdvertiser()"  id="selectExistingAdvertiser"></td>
                </tr>

                <tr>
                    <td><span id="AdLbl"> Advertiser Display Name</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <input type="text" name="addNewDisplayAdvertiser" id="addNewDisplayAdvertiser" value="" onblur="checkAdvertiserDisplayName()" />
                        <input type="hidden" name="AdvertiserID" id="AdvertiserID" value="0" />
                    </td>
                </tr>

                <tr>
                    <td><span> Direct Client</span> <span style="color:#F00;"> *</span></td>
                    <td> </td>
                    <td>
                        <input type="radio" id="r1" name="rd_direct_client" value="1" onclick="javascript:$('.div_direct').show();" /> Yes
                        <input type="radio" id="r0" name="rd_direct_client" value="0" onclick="javascript:$('.div_direct').hide();" checked="checked" /> No
                    </td>
                </tr>

                <tr style="display:none;" class="div_direct">
                    <td><span id="postAddress">Postal Address</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <input type="text" name="postalClientAddress" id="postalClientAddress" value="" />
                    </td>
                </tr>

                <tr style="display:none;" class="div_direct">
                    <td><span id="billInfo">Billing Info</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <select name="billingClientInfo" id="billingClientInfo">
                            <option value="">-</option>
                            <option value="Brand wise">Brand wise</option>
                            <option value="Market wise">Market wise</option>
                            <option value="Content wise">Content wise</option>
                        </select>
                    </td>
                </tr>

                <tr style="display:none;" class="div_direct">
                    <td><span id="billAddress">Billing Address</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <input type="text" name="billingClientAddress" id="billingClientAddress" value="" />
                    </td>
                </tr>

                <tr style="display:none;" class="div_direct">
                    <td><span id="billingCycle">Billing Cycle</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <select name="billClientCycle" id="billClientCycle">
                            <option value="">-</option>

                            <option value="Monthly">Monthly</option>
                            <option value="Consolidated">Consolidated</option>
                        </select>

                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <input type="button" class="submit" value="Save" onclick="return check_advertiser_form();" />
                        <input type="button" class="submit" value="Reset" onclick="resetAd(1);" />
                    </td>
                </tr>

            </table>
            <input type="hidden" name="hid_advertiser_name" id="hid_advertiser_name" value="">
        </form>
    </div>
</div>
<div id="overlay">
<!-- Popup Div Starts Here -->
    <div id="popupContact">
<!-- Contact Us Form -->
        <div class="block_head">
            <div class="bheadl"></div>
            <div class="bheadr"></div>
            <h2 style="margin:0">SELECT ADVERTISER</h2>
        </div>
        <table width="100%" cellpadding="0" cellspacing="5" class="" style="margin-top: 10px;margin-left: 35px;">
            <tr>
                <td>
                    <select data-placeholder="Choose advertiser" name="allAdvertisers" id="allAdvertisers" class="chosen-select" style="width:220px;" onchange="getAllAdvertiserDisplayNames()" >
                        <option value=""></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <select data-placeholder="Choose advertiser display name" name="choosenAdvertiserDisplayName" id="choosenAdvertiserDisplayName" class="chosen-select" style="width:220px;">
                        <option value=""></option>
                    </select>
                </td>
            </tr>
        </table>
            
        
        <div class="footer" style="margin-left: 30px;margin-top: 10px;">
            <div style="margin-left: 30px;">
                <input type="button" class="submit" value="Save" onclick="saveData()" />
                <input type="button" class="submit" value="Close" onclick="javascript:$('#overlay').hide();" />
            </div>
        </div>
</div>
</div>

<script>
var errorJson = {isAdvertiserNameExist:false,isAdvertiserDisplayNameExist:false};
var advertiserDisplayNameInfo = {};
var selectedAnExistingAdvertiser = false;
function check_advertiser_form()
{
	if($('#advertiser_category').val() == ""){
		alert("Please select Category.");
		$('#advertiser_category').focus();
		return false;
	}
	if($('#advertiser_product').val() == ""){
		alert("Please select Product.");
		$('#advertiser_product').focus();
		return false;
	}
	if($('#addNewAdvertiser').val() == ""){
		alert("Please enter new Advertiser.");
		$('#addNewAdvertiser').focus();
		return false;
	}

    var adv_name = $('#addNewAdvertiser').val();
    var test_for_char = Utility.checkInput1(adv_name);
    if(!test_for_char)	{
        alert("Invalid Advertiser Name");
        $('#addNewAdvertiser').focus();
        return false;
    }


    /*var check_input = Utility.checkTextWordLengthForAgency(adv_name);
    if(!check_input) {
        alert('Advertiser Name cannot be more than 75 characters.');
        $('#addNewAdvertiser').focus();
        return false;
    }*/


    if($('#addNewDisplayAdvertiser').val() == ""){
        alert("Please enter new Advertiser Display.");
        $('#addNewDisplayAdvertiser').focus();
        return false;
    }

    var adv_dis_name = $('#addNewDisplayAdvertiser').val();
    var test_for_char = Utility.checkInput1(adv_dis_name);
    if(!test_for_char)	{
        alert("Invalid Advertiser Display Name");
        $('#addNewDisplayAdvertiser').focus();
        return false;
    }

    var client_direct = $('input[name=rd_direct_client]:checked').val();
    if(client_direct == 1){
        if($('#postalClientAddress').val() == ""){
            alert("Please enter Client Postal Address");
            $('#postalClientAddress').focus();
            return false;
        }
        var agency_address = $('#postalClientAddress').val();
        var test_for_char = Utility.checkInputAddress(agency_address);
        if(!test_for_char)	{
            alert("Invalid Client Postal Address");
            $('#postalClientAddress').focus();
            return false;
        }

        if($('#billingClientAddress').val() == ""){
            alert("Please enter Client Billing Address");
            $('#billingClientAddress').focus();
            return false;
        }
        var billing_address = $('#billingClientAddress').val();
        var test_for_char = Utility.checkInputAddress(billing_address);
        if(!test_for_char)	{
            alert("Invalid Client Billing Address");
            $('#billingClientAddress').focus();
            return false;
        }

        if($('#billClientCycle').val() == ""){
            alert("Please enter a Payment cycle");
            $('#billClientCycle').focus();
            return false;
        }
    }
    

   
    if(errorJson.isAdvertiserNameExist == true){
         alert('The advertiser name already exist.Kindly give alternate advertiser name or choose select existing advertiser option');
         $('#addNewAdvertiser').focus();
         return false;

    }
    if(errorJson.isAdvertiserDisplayNameExist == true){
        alert('Advertiser display name already taken . Kindly give alternate advertiser display name or choose <b>select existing advertiser </b> option');
         $('#addNewDisplayAdvertiser').focus();
        return false;
    }
    if($('#advertiser_advertiser').val() != ''){
        var advertiser_name = $("#advertiser_advertiser option:selected").text();
        $('#hid_advertiser_name').val(advertiser_name);
    }
    document.getElementById("advertiser_form").submit();
}
function editAdvertiser()
{
	var id = $("#advertiser_advertiser").val();
	var value = $("#advertiser_advertiser option:selected").text();
	if(id!=null && id!="" && id!=0)
	{
		$("#addNewAdvertiser").val($.trim(value));
		$("#AdvertiserID").val(id);
		$("#AdLbl").html("Edit Advertiser");
	}
	else
		alert("Select Advertiser to edit the value");
}
function resetAd(tag)
{
    if(tag == 1){
        $("#advertiser_category option[value= '']").prop("selected",true);
        $("#advertiser_product option[value= '']").prop("selected",true);
        $("#advertiser_advertiser option[value= '']").prop("selected",true);
        $("#advertiser_display_advertiser option[value= '']").prop("selected",true);
        $(".chosen-select").trigger("chosen:updated");
    }

	$("#AdvertiserID").val(0);
	$("#AdLbl").html("Add New Advertiser");
    $('#addNewAdvertiser').val('');
    $('#addNewDisplayAdvertiser').val('');
    $('#postalClientAddress').val('');
    $('#r0').prop('checked',true);
    $('.div_direct').hide();
    $("#billingClientInfo option[value= '']").prop("selected",true);
    $('#billingClientAddress').val('');
    $("#billClientCycle option[value= '']").prop("selected",true);
    enableFields();
}
function callForAddingExistingAdvertiser(){
    if($('#advertiser_category').val() == ""){
        alert("Please select Category.");
        $('#advertiser_category').focus();
        return false;
    }
    if($('#advertiser_product').val() == ""){
        alert("Please select Product.");
        $('#advertiser_product').focus();
        return false;
    }
     $('#choosenAdvertiserDisplayName').html('<option value=""></option>');
    $(".chosen-select").trigger("chosen:updated");
    var val = $('#advertiser_advertiser').val();
    if(val == ''){
         $('#resultLoading').show();
            $.ajax({
                type: "GET",
                dataType:"json",
                data: {productId: $('#advertiser_product').val()},
                
                url: "<?php echo ROOT_FOLDER ?>/utility/getAllAdvertisersList",
                success:function(data){ 
                    var optionStr = '';
                    var selected = '';
                    
                    
                    for(var key in data.allAdvertiserList){
                        selected = '';
                        for (var index in data.advertiserListFromProduct) {
                            if(data.advertiserListFromProduct[index].advertiser == data.allAdvertiserList[key].advertiser){
                                selected = 'disabled="disabled"';
                            }
                        }
                        optionStr += '<option value=""></option><option '+selected+' value="'+data.allAdvertiserList[key].advertiser+'">'+data.allAdvertiserList[key].advertiser+'</option>';
                    }
                    //alert(optionStr);
                    $('#allAdvertisers').html(optionStr);
                    $(".chosen-select").trigger("chosen:updated");
                    $('#overlay').show();
                    $('#resultLoading').hide();
                },
                error: function(jqxhr,textStatus,errorThrown){
                    $('#resultLoading').hide();
                    //var err = eval("(" + jqxhr.responseText + ")");
                    alert(errorThrown);
                }
            });
        
    }else{
        alert("Already you have selected a advertiser");
    }
}
function checkAdvertiserName(){
    console.log(selectedAnExistingAdvertiser);
    errorJson.isAdvertiserNameExist = false;
    if(!selectedAnExistingAdvertiser){
        var advertiserName = ($('#addNewAdvertiser').val()).trim();
        if(advertiserName != '' ){

            var test_for_char = Utility.checkInput1(advertiserName);
            if(!test_for_char)  {
                alert("Invalid Advertiser Name");
                $('#addNewAdvertiser').focus();
                return false;
            } 
            var value = $("#advertiser_advertiser option:selected").text();
            if($('#advertiser_advertiser').val() != '' && value == advertiserName){
                return false;
            }
    
            
            console.log(errorJson);
            $('#resultLoading').show();
            $.ajax({
                type: "GET",
                dataType:"json",
                data: {advertiser_name: advertiserName},
                async: true,
                url: "<?php echo ROOT_FOLDER ?>/utility/getOnlyAdvertisers",
                success:function(data){ 
                    $('#resultLoading').hide();
                    if(data.status == false){
                        errorJson.isAdvertiserNameExist = true;
                        alert('The advertiser name already exist.Kindly give alternate advertiser name or choose select existing advertiser  option');
                    }
                },
                error: function(jqxhr,textStatus,errorThrown){
                    $('#resultLoading').hide();
                    alert("Error occured");
                }
            });
        }
    }
    
}
function checkAdvertiserDisplayName(){
    
   if(!selectedAnExistingAdvertiser){
        var advertiser_display_name = ($('#addNewDisplayAdvertiser').val()).trim();
        if(advertiser_display_name != ''){
            var test_for_char = Utility.checkInput1(advertiser_display_name);
            if(!test_for_char)  {
                alert("Invalid Advertiser Display Name");
                $('#addNewDisplayAdvertiser').focus();
                return false;
            } 
            var value = $("#advertiser_display_advertiser option:selected").text();
            if($("#advertiser_display_advertiser").val() != '' && value == advertiser_display_name) {
                return false;
            }

            $('#resultLoading').show();
            var url = "<?php echo ROOT_FOLDER ?>/utility/getAdvertiserDetails";
            $.ajax({
                type: "POST",
                data: { advertiser_display_name: advertiser_display_name },
                async: false,
                dataType: 'json',
                url: url,
                success:function(data){
                    $('#resultLoading').hide();
                    if (data[0] != undefined){
                        alert('Advertiser display name already taken . Kindly give alternate advertiser display name or choose select existing advertiser  option');
                        errorJson.isAdvertiserNameExist = true;
                    }
            
                },
                error: function(jqxhr,textStatus,errorThrown){
                    $('#resultLoading').hide();
                    alert("Error occured");
                }
            });
        }
   }
    
    
}
function getAllAdvertiserDisplayNames(){
    //alert(1);
  
        $('#resultLoading').show();
   
        var url = "<?php echo ROOT_FOLDER ?>/utility/getAllAdvertiserDisplayNames";
        $.ajax({
            type: "GET",
            data: { advertiser_name: $('#allAdvertisers').val() },
            async: false,
            dataType: 'json',
            url: url,
            success:function(data){
                var optionStr = '<option value=""></option>';
                $('#resultLoading').hide();
                advertiserDisplayNameInfo = data;
                for(var key in advertiserDisplayNameInfo){
                    optionStr += '<option value="'+advertiserDisplayNameInfo[key].advertiser_display_name+'">'+advertiserDisplayNameInfo[key].advertiser_display_name+'</option>';
                }
                $('#choosenAdvertiserDisplayName').html(optionStr);
                $(".chosen-select").trigger("chosen:updated");
            },
            error: function(jqxhr,textStatus,errorThrown){
                $('#resultLoading').hide();
                alert("Error occured");
            }
        });
    
    
}
function saveData(){
    if($('#allAdvertisers').val() == ""){
        alert("Please select a advertiser.");       
        return false;
    }
    if($('#choosenAdvertiserDisplayName').val() == ""){
        alert("Please select a advertiser display name.");       
        return false;
    }

    var choosenAdvertiserDisplayName =  $('#choosenAdvertiserDisplayName').val();
    var selectedAdv = $('#allAdvertisers').val();
    console.log(selectedAdv);
    console.log(choosenAdvertiserDisplayName);
    console.log(advertiserDisplayNameInfo);

    for(var key in advertiserDisplayNameInfo){
        if(advertiserDisplayNameInfo[key].advertiser_display_name == choosenAdvertiserDisplayName){
            $('#addNewAdvertiser').val(selectedAdv);
            $('#addNewDisplayAdvertiser').val(advertiserDisplayNameInfo[key].advertiser_display_name);
            $('#r'+advertiserDisplayNameInfo[key].direct_client).prop('checked',true);
            if(advertiserDisplayNameInfo[key].direct_client == 1){
                 $('.div_direct').show();
            }else{
                $('.div_direct').hide();
            }
            $("#billingClientInfo option[value= '"+advertiserDisplayNameInfo[key].billing_info+"']").prop("selected",true);
            $('#billingClientAddress').val(advertiserDisplayNameInfo[key].billing_address);
            $("#billClientCycle option[value= '"+advertiserDisplayNameInfo[key].billing_cycle+"']").prop("selected",true);
            disableFields();
        }
    }
    selectedAnExistingAdvertiser = true;
    $('#overlay').hide();
}
function disableFields(){
    $('#addNewAdvertiser').attr('readonly',true);
    $('#addNewDisplayAdvertiser').attr('readonly',true);
    $("#billingClientInfo").attr('readonly',true);
    $('#billingClientAddress').attr('readonly',true);
    $("#billClientCycle").attr('readonly',true);
    $("input[type='radio']").attr('readonly',true);
}
function enableFields(){
    $('#addNewAdvertiser').attr('readonly',false);
    $('#addNewDisplayAdvertiser').attr('readonly',false);
    $('#addNewAdvertiser').val('');
    $('#addNewDisplayAdvertiser').val('');
    selectedAnExistingAdvertiser = false;
    $("#billingClientInfo").attr('readonly',false);
    $('#billingClientAddress').attr('readonly',false);
    $("#billClientCycle").attr('readonly',false);
    $("input[type='radio']").attr('readonly',false);
    $('#postalClientAddress').val('');
    $('#r0').prop('checked',true);
    $('.div_direct').hide();
    $("#billingClientInfo option[value= '']").prop("selected",true);
    $('#billingClientAddress').val('');
    $("#billClientCycle option[value= '']").prop("selected",true);

}
</script>