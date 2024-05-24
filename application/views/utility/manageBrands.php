<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/jquery.chosen.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css" />
<style>
.block, .block_content { overflow:initial !important; }
.success {
	border: 1px solid #a2d246 !important;
	background-color: #a2d246;
}
.errors {
	border: 1px solid maroon !important;
	background-color: pink;
}
.success, .errors{
	background-position: 10px 50%;
	background-repeat: no-repeat;
	box-shadow: 0 1px 1px #fff inset;
	margin: 0.5em 0 1.3em;
	padding: 10px;
	color: #000;
}
input[type="text"]{ height: 24px; width:74%; }
</style>
<div id="hld">
	<div class="wrapper">		<!-- wrapper begins -->
		<div id="header">
			<div class="hdrl"></div>
			<div class="hdrr"></div>
			<h1 style="margin:0 10px 0 0"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>	
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				<input type="hidden" id="profile_id" value="<?php echo $profile_id?>" />
				<?php echo $menu ?>
				
				<p class="user" style="margin:0" >Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
			<div id="tabs">
				 <ul>
					<li><a href="#tabs-1">Activate Advertiser</a></li>
					<li><a href="#tabs-2">Category</a></li>
					<li><a href="#tabs-3">Product</a></li>
					<li><a href="#tabs-4">Advertiser</a></li>
					<li><a href="#tabs-5">Brand</a></li>
                    <li><a href="#tabs-6">Agency</a></li>
					<li><a href="#tabs-7">Map BR Brand</a></li>					
				</ul>
				<div id="tabs-1">
					<?php echo $activateAdvertiser; ?>
				</div>
				<div id="tabs-2">
					<?php echo $addCategory; ?>
				</div>
				<div id="tabs-3">
					<?php echo $addProduct; ?>
				</div>
				<div id="tabs-4">
					<?php echo $addAdvertiser; ?>
				</div>
				<div id="tabs-5">
					<?php echo $addBrands; ?>
				</div>
                <div id="tabs-6">
                    <?php echo $addAgency; ?>
                </div>
				<div id="tabs-7">
                    <?php echo $mapBrands; ?>
                </div>
				
			</div>
	</div>
</div>
<div style="width: 100%; height: 100%; z-index: 10000000; top: 0px; left: 0px; right: 0px; bottom: 0px; margin: auto; position: fixed; display: none;" id="resultLoading"><div style="width: 250px; height: 75px; text-align: center; position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; margin: auto; font-size: 16px; z-index: 10; color: rgb(255, 255, 255);"><img src="/surewaves_easy_ro/images/waitCursor.gif"><div>Managing data.. please wait..</div></div><div class="bg" style="background: none repeat scroll 0% 0% rgb(0, 0, 0); opacity: 0.7; width: 100%; height: 100%; position: absolute; top: 0px;"></div></div>	
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/chosen.jquery.min.js"></script>	
<script>
var gbl_advBrandList = {};
var gblBrandListWithBR = Array();
$(document).ready(function(){
	$(".chosen-select").chosen({ width: '75%' });
	$( "#tabs" ).tabs({
		active: <?php echo $activeTab; ?>
	});
	setTimeout(function() {
        $(".success").hide();
    }, 5000);
	setTimeout(function() {
        $(".error").hide();
    }, 5000);
	$('#br_customer').change(function(){
		if($('#mg_brand_product').val() == -1 || $('#mg_brand_product').val() == null || $('#mg_brand_product').val() == ''){
			alert("please select Category");			
			return false;
		}	
		var customer_id = $(this).val();
		if(customer_id !=  -1 || customer_id != ''){
			$('#resultLoading').show();
			$.ajax({
				type: "POST",
				dataType:"json",
				data: {customerId: customer_id},
				async: true,
				url: "<?php echo ROOT_FOLDER ?>/utility/getBRAdvertisers",
				success:function(data){	
					var OptionStr = '<option value="-1" selected>--Select--</option>';
					if(data.length >0){
						gbl_advBrandList = data;
						for(var key in data){
							OptionStr+='<option value="'+data[key]['advertiser_id']+'">'+data[key]['advertiser_name']+'</option>';
						}
					}
					$( "#br_advertiser" ).html(OptionStr);
					$(".chosen-select").trigger("chosen:updated");
					$('#resultLoading').hide();
				},
				error: function(jqxhr,textStatus,errorThrown){
					$('#resultLoading').hide();
					alert("Error occured");
				}
			});
		}else{
			$( "#br_advertiser" ).html('<option value="-1" selected>--Select--</option>');
			$( "#br_brand" ).html('<option value="-1" selected>--Select--</option>');
			$( "#mg_brand_advertiser option" ).attr('selected',false);
			$( "#mg_brand_brand option" ).attr('selected',false);			
			$(".chosen-select").trigger("chosen:updated");
		}
	});
	
	$('#br_advertiser').change(function(){	
		if($('#mg_brand_advertiser').val() == -1 || $('#mg_brand_advertiser').val() == null || $('#mg_brand_advertiser').val() == ''){
			alert("please Select Advertiser");			
			return false;
		}
		
		var br_advertiser_id = $(this).val();
		var optionStr = '<option value="-1" selected>--select--</value>';
		if(br_advertiser_id != -1 || br_advertiser_id != ''){
			
			var hidMappedBRAdvID = $('#hidMappedBRAdvID').val();
			var hidMappedMGAdvID = $('#hidMappedMGAdvID').val();
			var mg_brand_advertiser	= $('#mg_brand_advertiser').val();
			if(hidMappedBRAdvID != '' && hidMappedBRAdvID != br_advertiser_id && hidMappedMGAdvID == mg_brand_advertiser){
				var status = confirm("If you change BR Advertiser,All existing Advertiser and brands mapped will be overridden with selected BR Advertiser and its brands");
				if (status == true) {	
					getAllBrand($('#mg_brand_advertiser').val(), 'mg_brand');
					$('#resultLoading').show();
					$('#hidMappedBROLDAdvID').val(hidMappedBRAdvID);
					listAllBrBrand(hidMappedBRAdvID,1);
					listAllBrBrand(br_advertiser_id,1);	
					$('#resultLoading').hide();
				}else{// reset all selection					
					$('#br_advertiser').val(hidMappedBRAdvID);
					listAllBrBrand(hidMappedBRAdvID,0);
				}					
			}else{
				listAllBrBrand(br_advertiser_id,0);
			}
		}else{
			$( "#br_brand" ).html(optionStr);
			$(".chosen-select").trigger("chosen:updated");	
		}
	});
	$('#mg_brand_brand').change(function(){
		if($('#mg_brand_advertiser').val() == -1 || $('#mg_brand_advertiser').val() == null || $('#mg_brand_advertiser').val() == ''){
			alert("please select Advertiser");			
			return false;
		}
	});
	$('#br_brand').change(function(){
		if($('#br_advertiser').val() == -1 || $('#br_advertiser').val() == null || $('#br_advertiser').val() == ''){
			alert("please select BR Advertiser");		
			return false;
		}
	});
});
function getProducts(CategoryID, selectStr)
{
	enableFields();
	brandNameExist = false;
	$('#resultLoading').show();
	$.ajax({
        type: "POST",
        data: {CategoryID: CategoryID},
        async: false,
        url: "<?php echo ROOT_FOLDER ?>/utility/getProducts",
        success:function(data){
                $( "#"+selectStr+"_product" ).html( data );
                $(".chosen-select").trigger("chosen:updated");
				$('#resultLoading').hide();
        },
		error: function(jqxhr,textStatus,errorThrown){
				$('#resultLoading').hide();
				alert("Error occured");
		}
    });
}

function getAdvertiser(ProductID, selectStr)
{
	enableFields();
	brandNameExist = false;
	var url				= '';
	var findStr 		= 'mg_';
	var selector		= selectStr;
	if(selectStr.indexOf(findStr) != -1){
		if($('#mg_brand_category').val() == -1 || $('#mg_brand_category').val() == null || $('#mg_brand_category').val() == ''){
			alert("please select Category");
			return false;
		}
		var selectStr_len 	= selectStr.length;
		var findStrLen 		= findStr.length;
		selector 			= selectStr.substring(findStrLen, selectStr_len);
		url 				= getAdvertiserUrl(selector);		
	}else{
		url 				= getAdvertiserUrl(selector);	
	}
	$('#resultLoading').show();
	$.ajax({
        type: "POST",
        data: { ProductID: ProductID },
        async: false,
        url: url,
        success:function(data){
                $( "#"+selectStr+"_advertiser" ).html( data );
                $(".chosen-select").trigger("chosen:updated");
				$('#resultLoading').hide();
        },
		error: function(jqxhr,textStatus,errorThrown){
				$('#resultLoading').hide();
				alert("Error occured");
		}
    });
}
function getAdvertiserDetails(advertiser_display_name){
	$('#resultLoading').show();
    var url = "<?php echo ROOT_FOLDER ?>/utility/getAdvertiserDetails";
    $.ajax({
        type: "POST",
        data: { advertiser_display_name: advertiser_display_name },
        async: false,
        dataType: 'json',
        url: url,
        success:function(data){

            if (data[0] == undefined){
                $('#addNewAdvertiser').val('');
                $('#addNewDisplayAdvertiser').val('');
                $('#postalClientAddress').val('');
                $('#r0').prop('checked',true);
                $('.div_direct').hide();
                $("#billingClientInfo option[value= '']").prop("selected",true);
                $('#billingClientAddress').val('');
                $("#billClientCycle option[value= '']").prop("selected",true);
            }else{
                $('#addNewAdvertiser').val(data[0].advertiser);
                $('#addNewDisplayAdvertiser').val(data[0].advertiser_display_name);
                $('#postalClientAddress').val(data[0].client_address);
                $('#r'+data[0].direct_client).prop('checked',true);
                if(data[0].direct_client == 1){
                    $('.div_direct').show();
                }else{
                    $('.div_direct').hide();
                }
                $("#billingClientInfo option[value= '"+data[0].billing_info+"']").prop("selected",true);
                $('#billingClientAddress').val(data[0].billing_address);
                $("#billClientCycle option[value= '"+data[0].billing_cycle+"']").prop("selected",true);
            }
			$('#resultLoading').hide();
        },
		error: function(jqxhr,textStatus,errorThrown){
				$('#resultLoading').hide();
				alert("Error occured");
		}
    });
}

function getAdvertiserDisplays(advertiser){
	enableFields();
	$('#resultLoading').show();
    $.ajax({
        type: "POST",
        data: { advertiser_id: advertiser },
        async: false,
        url: "<?php echo ROOT_FOLDER ?>/utility/getAdvertiserDisplays",
        success:function(data){
            $( "#advertiser_display_advertiser" ).html( data );
            $(".chosen-select").trigger("chosen:updated");
			$('#resultLoading').hide();
        },
		error: function(jqxhr,textStatus,errorThrown){
				$('#resultLoading').hide();
				alert("Error occured");
		}
    });
}
function getBrand(AdvertiserID, selectStr)
{
	brandNameExist = false;
	$.ajax({
        type: "POST",
        data: { AdvertiserID: AdvertiserID },
        async: false,
        url: "<?php echo ROOT_FOLDER ?>/utility/getBrand",
        success:function(data){
                $( "#"+selectStr+"_brand" ).html( data );
                $(".chosen-select").trigger("chosen:updated");
        }
    });
}
function getBrandMG(AdvertiserID, selectStr)
{
	if($('#br_customer').val() == -1 || $('#br_customer').val() == null || $('#br_customer').val() == ''){
			alert("please select BR Customer");
			return false;
	}
	gblBrandListWithBR = Array();
	$('#hidMappedBRAdvID').val('');
	$('#hidMappedMGAdvID').val('');
	$('#hidMappedBROLDAdvID').val('');
	$('#resultLoading').show();
	$.ajax({
        type: "POST",
		dataType:"json",
        data: { AdvertiserID: AdvertiserID },
        async: true,
        url: "<?php echo ROOT_FOLDER ?>/utility/getBrand_Br",
        success:function(data){
				var option_str = '<option value="-1" selected="selected">--Select--</option>';
				if((data.brandList.length)>0){
					for(var mgBrandKey in data.brandList){
						option_str+='<option value="'+data.brandList[mgBrandKey]['id']+'">'+data.brandList[mgBrandKey]['brand']+'</option>';
					}					
				}
				$( "#"+selectStr+"_brand" ).html(option_str);
				$(".chosen-select").trigger("chosen:updated");
				if((data.brbrandList.length)>0){
					for(var brBrandKey in data.brbrandList){
						gblBrandListWithBR[brBrandKey] = data.brbrandList[brBrandKey]['br_brand_id'];
					}
				}
				console.log(gblBrandListWithBR);
				if((data.mappedBrAdvId.length)>0){
					$("#br_advertiser").val(data.mappedBrAdvId[0]['br_advertiser_id']);
					$("#br_advertiser").trigger("change");
					$('#hidMappedBRAdvID').val(data.mappedBrAdvId[0]['br_advertiser_id']);
					$('#hidMappedMGAdvID').val(AdvertiserID);
					$(".chosen-select").trigger("chosen:updated");
				}else{
					$("#br_advertiser").val('-1');
					$('#hidMappedMGAdvID').val('');
					$('#hidMappedBRAdvID').val('');
					$("#br_brand").val('-1');
					$(".chosen-select").trigger("chosen:updated");					
				}
				$('#resultLoading').hide();
        },
		error: function(jqxhr,textStatus,errorThrown){
				$('#resultLoading').hide();
				alert("Error occured");
		}
    });
}
function getAgencyDisplays(agency_id){
	$('#resultLoading').show();
    $.ajax({
        type: "POST",
        data: { agency_id: agency_id },
        async: false,
        url: "<?php echo ROOT_FOLDER ?>/utility/getAgencyDisplays",
        success:function(data){
            $( "#editDisAgency" ).html( data );
            $(".chosen-select").trigger("chosen:updated");
			$('#resultLoading').hide();
        },
		error: function(jqxhr,textStatus,errorThrown){
				$('#resultLoading').hide();
				alert("Error occured");
		}
    });
}
function getAgencyDetails(agencyDisplay){
	$('#resultLoading').show();
    $.ajax({
        type: "POST",
        data: { agency_dis_name: agencyDisplay },
        async: false,
        dataType: 'json',
        url: "<?php echo ROOT_FOLDER ?>/utility/getAgencyDetails",
        success:function(data){
            if (data[0] == undefined){
                $('#agencyName').val('');
                $('#agencyDisName').val('');
                $('#postalAgencyAddress').val('');
                $("#billingAgencyInfo option[value= '']").prop("selected",true);
                $('#billingAgencyAddress').val('');
                $("#billAgencyCycle option[value= '']").prop("selected",true);
            }else{
                $('#agencyName').val(data[0].agency_name);
                $('#agencyDisName').val(data[0].agency_display_name);
                $('#postalAgencyAddress').val(data[0].agency_address);
                $("#billingAgencyInfo option[value= '"+data[0].billing_info+"']").prop("selected",true);
                $('#billingAgencyAddress').val(data[0].billing_address);
                $("#billAgencyCycle option[value= '"+data[0].billing_cycle+"']").prop("selected",true);
            }
				$('#resultLoading').hide();
        },
		error: function(jqxhr,textStatus,errorThrown){
				$('#resultLoading').hide();
				alert("Error occured");
		}
    });
}
function getAdvertiserUrl(selector){
	var url = "<?php echo ROOT_FOLDER ?>/utility/getInActiveAdvertiser";
	if(selector=="brand")
		url = "<?php echo ROOT_FOLDER ?>/utility/getActiveAdvertiser";
	if(selector == "advertiser")
		url = "<?php echo ROOT_FOLDER ?>/utility/getAllAdvertiser";
	
	return url;
}
function resetAllMap(){
	$(".chosen-select option:first").prop('selected','selected');
	$(".chosen-select").trigger("chosen:updated");
}
function listAllBrBrand(br_advertiser_id,deleteFromBrandArr){
	var optionStr = '<option value="-1" selected>--select--</value>';
	if(gbl_advBrandList.length > 0){
		for(var key in gbl_advBrandList){
			if(gbl_advBrandList[key]['advertiser_id'] == br_advertiser_id){
				if(gbl_advBrandList[key]['brand'].length > 0){
					for(var brand_index in gbl_advBrandList[key]['brand']){
						var value 	= gbl_advBrandList[key]['brand'][brand_index]['brand_id'];
						var text 	= gbl_advBrandList[key]['brand'][brand_index]['brand_name'];
						var index	= (gblBrandListWithBR.indexOf(value));
						if(index == -1){
								optionStr+='<option value="'+value+'">'+text+'</option>';
						}else if(deleteFromBrandArr){							
							gblBrandListWithBR.splice(index,1);
						}
					}
				}
				break;
			}
		}
	}
	$( "#br_brand" ).html(optionStr);
	$(".chosen-select").trigger("chosen:updated");	
}
function getAllBrand(AdvertiserID, selectStr)
{
	$('#resultLoading').show();
	$.ajax({
        type: "POST",
        data: { AdvertiserID: AdvertiserID},
        async: false,
        url: "<?php echo ROOT_FOLDER ?>/utility/getAllBrand",
        success:function(data){
                $( "#"+selectStr+"_brand" ).html( data );
                $(".chosen-select").trigger("chosen:updated");
				$('#resultLoading').hide();
        },
		error: function(jqxhr,textStatus,errorThrown){
				$('#resultLoading').hide();
				alert("Error occured");
		}
    });
}
</script>
