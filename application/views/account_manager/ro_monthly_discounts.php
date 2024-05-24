<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div class="block">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Monthly Discounts</h2>
    </div>

    <div class="block_content" style="height: auto">
        <table cellpadding="0" cellspacing="0" width="100%">

            <tr>
                <th style="width:30%">Markets</th>
                <th style="width:20%">Spot</th>
                <th style="width:20%">Banner</th>
            </tr>
            <?php foreach ($markets as $mkt) { ?>
                <tr>
                    <td><span><?php echo $mkt['market'] ?></span></td>
                    <td><span><?php echo $mkt['spot_discount'] ?></span></td>
                    <td><span><?php echo $mkt['banner_discount'] ?></span></td>
                </tr>
            <?php } ?>

            <tr>
                <td>Net Contribution Percent</td>
                <td><?php echo $net_contribution_percent; ?></td>
            </tr>


        </table>

    </div>        <!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>        <!-- .login ends -->


<?php include_once dirname(__FILE__) . "/inc/footer.inc.php" ?>
