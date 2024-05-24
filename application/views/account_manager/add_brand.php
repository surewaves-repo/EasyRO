<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>


<div class="block small center login" style="margin:0px">


    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Add Brand</h2>
    </div>

    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_add_brand" method="post">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="20%">Brand Name</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_brand_name" id="txt_brand_name"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="hidden" name="hid_cust_ro" value="<?php echo $cust_ro; ?>"/>
                        <input type="hidden" name="hid_brand_id" value="<?php echo $brand_id; ?>"/>
                        <input type="submit" class="submit" value="Add"/>
                        <input type="button" value="Back" class="submit" onclick="javascript:window.history.back();"/>
                    </td>
                <tr>
            </table>


        </form>
    </div>        <!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>        <!-- .login ends -->


<?php include_once dirname(__FILE__) . "/inc/footer.inc.php" ?>	