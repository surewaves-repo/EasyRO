<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div class="block">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Revised Market Price</h2>
    </div>

    <div class="block_content" style="height: auto">
        <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_cancel_markets" method="post"
              enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="0" width="100%">

                <tr>
                    <th style="width:30%">Markets</th>
                    <th style="width:20%">Spot Price</th>
                    <th style="width:20%">Banner Price</th>
                </tr>
                <?php foreach ($markets as $mkt) { ?>
                    <tr <?php if ($mkt['is_cancelled'] == 1) {
                        echo "style='background-color:#ff9999'";
                    } ?> >
                        <td><span><?php echo $mkt['market'] ?></span></td>
                        <td><span><?php echo $mkt['spot_price'] ?></span></td>
                        <td><span><?php echo $mkt['banner_price'] ?></span></td>
                    </tr>
                <?php } ?>


            </table>

            <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
            <input type="hidden" name="hid_id" value="<?php echo $id ?>">
            <input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro; ?>">
            <input type="hidden" id="hid_order_id" value="<?php echo rtrim(base64_encode($internal_ro), '=') ?>">

        </form>
    </div>        <!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>        <!-- .login ends -->


<?php include_once dirname(__FILE__) . "/inc/footer.inc.php" ?>
