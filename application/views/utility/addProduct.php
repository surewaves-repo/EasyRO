<div class="" style="margin:0px; width:870px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin:0">Add / Edit Product</h2>
    </div>
     <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/utility/post_add_product" method="post" enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="12" width="100%">
            <?php if(($activeTab==2) && isset($validation_errors) && $validation_errors!="" ) {?>
                <tr>
                	<td class="errors" colspan="3"><?php echo $validation_errors ?></td>
                </tr>
            <?php }?>
            <?php if(($activeTab==2) && isset($success_msg) && $success_msg!="" ) {?>
                <tr>
                	<td class="success" colspan="3"><?php echo $success_msg ?></td>
                </tr>
            <?php }?>
                <tr>
                    <td>Category<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                        <select name="product_category" id="product_category" class="chosen-select" style="width:220px;" onchange="javascript:getProducts(this.value, 'product')">
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
                    <td>Product
                    </td>
                    <td>:</td>
                    <td>
                        <select name="product_product" id="product_product" class="chosen-select" style="width:220px;">
                        </select>
                        <!-- <input type="button" class="edit" value="Edit" onclick="editProduct();"/> -->
                    </td>
                </tr>
                 <tr>
                	<td colspan="3" style="text-align:center">(OR)</td>
                </tr>
                <tr>
                	<td><span id="ProductLbl">Add New Product</span> <span style="color:#F00;"> *</span></td>
                	 <td> : </td>
                    <td>
                    	<input type="text" name="addNewProduct" id="addNewProduct" value="" />
                    	<input type="hidden" name="ProductID" id="ProductID" value="0" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" class="submit" value="Save" onclick="return check_product_form();" />
                        <input type="button" class="submit" value="Reset" onclick="resetProduct();" />
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>
<script>
function check_product_form()
{
	if($('#product_category').val() == ""){
		alert("Please select Category.");
		$('#product_category').focus();
		return false;
	}
	if($('#addNewProduct').val() == ""){
		alert("Please enter new Product.");
		$('#addNewProduct').focus();
		return false;
	}
}
function editProduct()
{
	var id = $("#product_product").val();
	var value = $("#product_product option:selected").text();
	if(id!=null && id!="" && id!=0)
	{
		$("#addNewProduct").val($.trim(value));
		$("#ProductID").val(id);
		$("#ProductLbl").html("Edit Product");
	}
	else
		alert("Select Product to edit the value");
}
function resetProduct()
{
	$("#addNewProduct").val("");
	$("#ProductID").val(0);
	$("#ProductLbl").html("Add New Product");
}
</script>