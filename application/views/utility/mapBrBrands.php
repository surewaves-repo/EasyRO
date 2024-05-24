<div class="" style="margin:0px; width:870px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin:0">Add / Edit Brand</h2>
    </div>
     <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/utility/post_add_br_brand" method="post" >
            <table cellpadding="0" cellspacing="12" width="100%">
            <?php if(($activeTab==4) && isset($validation_errors) && $validation_errors!="" ) {?>
                <tr>
                	<td class="errors" colspan="3"><?php echo $validation_errors ?></td>
                </tr>
            <?php }?>
            <?php if(($activeTab==4) && isset($success_msg) && $success_msg!="" ) {?>
                <tr>
                	<td class="success" colspan="3"><?php echo $success_msg ?></td>
                </tr>
            <?php }?>
                <tr>
                    <td>Category<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td colspan="4">
                        <select name="mg_brand_category" id="mg_brand_category" class="chosen-select" style="width:220px;" onchange="javascript:getProducts(this.value, 'mg_brand')">
                             <option value="-1" selected>--Select--</option>
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
                    <td colspan="4">
                        <select name="mg_brand_product" id="mg_brand_product" class="chosen-select" style="width:220px;" onchange="javascript:getAdvertiser(this.value, 'mg_brand')">
							<option value="-1" selected>--Select--</option>
                        </select>
                    </td>
                </tr>
				<tr>
                    <td>BR Customer<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td colspan="4">
                        <select name="br_customer" id="br_customer" class="chosen-select" style="width:220px;">
							<option value="-1" selected>--Select--</option>
						
							<?php foreach($br_customer as $br_customer_key=> $br_customer_val){ ?>
							<option value="<?php echo $br_customer_key; ?>">
									<?php echo $br_customer_val['name']; ?>
							</option>
							<?php }?>
                        </select>
                        
                    </td>
                </tr>
                <tr>
                    <td>Advertiser<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                        <select name="mg_brand_advertiser" id="mg_brand_advertiser" class="chosen-select" style="width:220px;" onchange="javascript:getBrandMG(this.value, 'mg_brand')">
							<option value="-1" selected>--Select--</option>
                        </select>
                    </td>
					<td>BR Advertiser<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                         <select name="br_advertiser" id="br_advertiser" class="chosen-select" style="width:220px;">
							<option value="-1" selected>--Select--</option>
                        </select>   
                    </td>
                </tr>
                <tr>
                    <td>Brand<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                        <select name="mg_brand_brand" id="mg_brand_brand" class="chosen-select" style="width:220px;">
							<option value="-1" selected>--Select--</option>
                        </select>
                        <!-- <input type="button" class="edit" value="Edit" onclick="editBrand();"/>   -->
                    </td>
					<td>BR Brand<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                        <select name="br_brand" id="br_brand" class="chosen-select" style="width:220px;">
							<option value="-1" selected>--Select--</option>
                        </select>                        
                    </td>
                </tr>                
				
				
                <tr>
                    <td colspan="6">
                        <input type="submit" class="submit" value="Save" onclick="return check_map_brand_form();" />
                        <!--<input type="button" class="submit" value="Reset" onclick="resetAllMap();" />-->
                    </td>
                </tr>

            </table>
			<input type="hidden" name="hidMappedBROLDAdvID" id="hidMappedBROLDAdvID" />
        </form>
		<input type="hidden" name="hidMappedBRAdvID" id="hidMappedBRAdvID" />
		<input type="hidden" name="hidMappedMGAdvID" id="hidMappedMGAdvID" />
    </div>
</div>

<script>
function check_map_brand_form(){
	if($('#mg_brand_category').val() == "" || $('#mg_brand_category').val() == "-1"){
		alert("Please select Category.");
		$('#brand_category').focus();
		return false;
	}
	if($('#mg_brand_product').val() == "" || $('#mg_brand_product').val() == "-1"){
		alert("Please select Product.");
		$('#brand_product').focus();
		return false;
	}
	if($('#br_customer').val() == "" || $('#br_customer').val() == "-1"){
		alert("Please select BR custommer.");
		$('#br_customer').focus();
		return false;
	}
	if($('#mg_brand_advertiser').val() == "" || $('#mg_brand_advertiser').val() == "-1"){
		alert("Please select Advertiser.");
		$('#brand_advertiser').focus();
		return false;
	}
	
	if($('#br_advertiser').val() == "" || $('#br_advertiser').val() == "-1"){
		alert("Please select BR Advertiser.");
		$('#br_customer').focus();
		return false;
	}
	if($('#mg_brand_brand').val() == "" || $('#mg_brand_brand').val() == "-1"){
		alert("Please select Brand.");
		$('#br_customer').focus();
		return false;
	}
	if($('#br_brand').val() == "" || $('#br_brand').val() == "-1"){
		alert("Please select BR Brand.");
		$('#br_customer').focus();
		return false;
	}	
	
}
function editBrand()
{
	var id = $("#brand_brand").val();
	var value = $("#brand_brand option:selected").text();
	if(id!=null && id!="" && id!=0)
	{
		$("#addNewBrand").val($.trim(value));
		$("#BrandID").val(id);
		$("#brandLbl").html("Edit Brand");
	}
	else
		alert("Select Brand to edit the value");
}

</script>