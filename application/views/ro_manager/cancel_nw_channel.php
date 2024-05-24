<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
        
<div class="block small center login" style="margin:0px">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Channels</h2>
    </div>


    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_cancel_nw_channel" method="post">
            <table>
            <?php foreach($channels_detail as $channels) { ?>
                <tr>
                    <td><?php echo $channels['channel_name'] ?></td>
                    <td> <input type="checkbox" name="channel_ids[]" value="<?php echo $channels['channel_id'] ?>"> </td>
                </tr>
            <?php } ?>                
            </table>
            
            <input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro_number; ?>">            
            <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
            <input type="hidden" name="hid_id" value="<?php echo $id ?>">
            <input type="hidden" name="hid_cid" value="<?php echo $customer_id ?>">
            
            <p>
                <input type="submit" class="submit" value="confirm"/>
            </p>
        </form>    
    </div>          <!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>          <!-- .login ends -->