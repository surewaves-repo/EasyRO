<div class="" style="margin:0px; width:870px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin:0">Add / Edit Category</h2>
    </div>
     <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/utility/post_add_category" method="post" enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="12" width="100%">
            <?php if(($activeTab==1) && isset($validation_errors) && $validation_errors!="" ) {?>
                <tr>
                	<td class="errors" colspan="3"><?php echo $validation_errors ?></td>
                </tr>
            <?php }?>
            <?php if(($activeTab==1) && isset($success_msg) && $success_msg!="" ) {?>
                <tr>
                	<td class="success" colspan="3"><?php echo $success_msg ?></td>
                </tr>
            <?php }?>
                <tr>
                    <td>Category
                    </td>
                    <td> : </td>
                    <td>
                        <select name="category_category" id="category_category" class="chosen-select" style="width:220px;">
                            <option value=""></option>
                            <?php foreach($CategoryList as $key=> $val){ ?>
                            <option value="<?php echo $val['category_id']?>">
                                <?php echo $val[ 'category_name']?>
                            </option>
                            <?php }?>
                        </select>
                        <!--  <input type="button" class="edit" value="Edit" onclick="editCategory();"/> -->
                    </td>
                </tr>
                <tr>
                	<td colspan="3" style="text-align:center">(OR)</td>
                </tr>
                <tr>
                	<td><span id="catLbl">Add New Category</span> <span style="color:#F00;"> *</span></td>
                	 <td> : </td>
                    <td>
                    	<input type="text" name="addNewCategory" id="addNewCategory" value="" />
                    	<input type="hidden" name="CategoryID" id="CategoryID" value="0" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" class="submit" value="Save" onclick="return check_category_form();" />
                        <input type="button" class="submit" value="Reset" onclick="resetCategory();" />
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>
<script>
function check_category_form()
{
	if($('#addNewCategory').val() == ""){
		alert("Please enter new Category.");
		$('#addNewCategory').focus();
		return false;
	}
}
function editCategory()
{
	var id = $("#category_category").val();
	var value = $("#category_category option:selected").text();
	if(id!=null && id!="" && id!=0)
	{
		$("#addNewCategory").val($.trim(value));
		$("#CategoryID").val(id);
		$("#catLbl").html("Edit Category");
	}
	else
		alert("Select Category to edit the value");
}
function resetCategory()
{
	$("#addNewCategory").val("");
	$("#CategoryID").val(0);
	$("#catLbl").html("Add New Category");
}
</script>