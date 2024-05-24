<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div class="block">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Reject Request</h2>
    </div>

    <form action="<?php if($cancel_type == 'submit_ro_approval'){
        echo ROOT_FOLDER ?>/ro_manager/submit_ro_reject
    <?php }else{
        echo ROOT_FOLDER ?>/ro_manager/reject_request_for_cancellation
    <?php } ?> " method="post" enctype="multipart/form-data">

        <div class="block_content" style="height: auto">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td>&nbsp;</td>
                    <td><span>Reason for Rejection:</span> <span style="color:#F00;"> *</span></td>
                    <td><input type="text" name="reason_rej" id="reason_rej" class="amount" value="" style="width:250px;"</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" class="submitlong" id="req_market_can" value="Reject Request" onclick="return check_form();" />
                    </td>
                <tr>
            </table>

            <input type="hidden" id="hid_am_ro_id" name="hid_am_ro_id" value="<?php echo $am_ro_id ?>">
            <input type="hidden" id="hid_status" name="hid_status" value="<?php echo $status ?>">
            <input type="hidden" id="hid_cancel_type" name="hid_cancel_type" value="<?php echo $cancel_type ?>">
            <input type="hidden" id="hid_cancel_id" name="hid_cancel_id" value="<?php echo $cancel_id ?>">

        </div>		<!-- .block_content ends -->
    </form>

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>		<!-- .login ends -->


<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>


<script type="text/javascript" language="javascript">
    function check_form(){
        if($('#reason_rej').val().trim() == ""){
            alert("Please enter Reason for Rejection");
            $('#reason_rej').focus();
            return false;
        }
        
    }
</script>
