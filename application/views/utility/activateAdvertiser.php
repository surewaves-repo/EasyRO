<div class="" style="margin:0px; width:870px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin:0">Activate Advertiser</h2>
    </div>
     <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/utility/post_activate_advertiser" method="post" enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="12" width="100%">
            <?php if(($activeTab==0) && isset($validation_errors) && $validation_errors!="" ) {?>
                <tr>
                	<td class="errors" colspan="3"><?php echo $validation_errors ?></td>
                </tr>
            <?php }?>
            <?php if(($activeTab==0) && isset($success_msg) && $success_msg!="" ) {?>
                <tr>
                	<td class="success" colspan="3"><?php echo $success_msg ?></td>
                </tr>
            <?php }?>
                <tr>
                    <td>Category<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                        <select name="act_category" id="act_category" class="chosen-select" style="width:220px;" onchange="javascript:getProducts(this.value, 'act')">
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
                        <select name="act_product" id="act_product" class="chosen-select" style="width:220px;" onchange="javascript:getAdvertiser(this.value, 'act')">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Advertiser<span style="color:#F00;"> *</span>
                    </td>
                    <td> : </td>
                    <td>
                        <select name="act_advertiser" id="act_advertiser" class="chosen-select" style="width:220px;">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" class="submit" value="Activate" onclick="return check_activate_form();" />
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>
<script>
    function check_activate_form() {
        if ($('#act_category').val() == "") {
            alert("Please select Category.");
            $('#act_category').focus();
            return false;
        }
        if ($('#act_product').val() == "") {
            alert("Please select Product.");
            $('#act_product').focus();
            return false;
        }
        if ($('#act_advertiser').val() == "") {
            alert("Please select Advertiser.");
            $('#act_advertiser').focus();
            return false;
        }
    }
</script>