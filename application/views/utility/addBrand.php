<div class="" style="margin:0px; width:870px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin:0">Add / Edit Brand</h2>
    </div>
     <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/utility/post_add_brand" id="postAddBrand" method="post" enctype="multipart/form-data">
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
                    <td>
                        <select name="brand_category" id="brand_category" class="chosen-select" style="width:220px;" onchange="javascript:getProducts(this.value, 'brand')">
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
                        <select name="brand_product" id="brand_product" class="chosen-select" style="width:220px;" onchange="javascript:getAdvertiser(this.value, 'brand')">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Advertiser<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                        <select name="brand_advertiser" id="brand_advertiser" class="chosen-select" style="width:220px;" onchange="javascript:getBrand(this.value, 'brand')">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Brand
                    </td>
                    <td> : </td>
                    <td>
                        <select name="brand_brand" id="brand_brand" class="chosen-select" style="width:220px;">
                        </select>
                        <!-- <input type="button" class="edit" value="Edit" onclick="editBrand();"/>   -->
                    </td>
                </tr>
                <tr>
                	<td colspan="3" style="text-align:center">(OR)</td>
                </tr>
                <tr>
                	<td><span id="brandLbl">Add New Brand</span> <span style="color:#F00;"> *</span></td>
                	 <td> : </td>
                    <td>
                    	<input type="text" name="addNewBrand" id="addNewBrand" value="" />
                    	<input type="hidden" name="BrandID" id="BrandID" value="0" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="button" class="submit" value="Save" onclick="return check_brand_form();"  id="save_btn" />
                        <input type="button" class="submit" value="Reset" onclick="resetBrand();" />
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>
<script>
function check_brand_form(){	
	if($('#brand_category').val() == ""){
		alert("Please select Category.");
		$('#brand_category').focus();
		return false;
	}
	if($('#brand_product').val() == ""){
		alert("Please select Product.");
		$('#brand_product').focus();
		return false;
	}
	if($('#brand_advertiser').val() == ""){
		alert("Please select Advertiser.");
		$('#brand_advertiser').focus();
		return false;
	}
	if($('#addNewBrand').val() == ""){
		alert("Please enter new Brand.");
		$('#addNewBrand').focus();
		return false;
	}
    if(brandNameExist == true){
        alert('Brand Name Exist');
        $('#addNewBrand').focus();
        return false;
    }
    document.getElementById("postAddBrand").submit();
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
function resetBrand()
{
	$("#addNewBrand").val("");
	$("#BrandID").val(0);
	$("#brandLbl").html("Add New Brand");
    brandNameExist = false;
}

</script>