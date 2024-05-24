<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div class="block">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Inernal Ro Number</h2>
    </div>

    <div class="block_content" style="height: auto">
        <table cellpadding="0" cellspacing="0" width="100%">

            <tr>
                <th style="width: 50%">Account manager Reason</th>
                <th style="width: 50%">Status</th>
            </tr>

            <?php foreach($messages as $mes){      ?>
                <tr>
                    <td><?php echo $mes['reason']?></td>
                    <?php if($mes['cancel_ro_by_admin'] == 0){?>
                        <td><?php echo 'Request Sent'?></td>
                    <?php } else if($mes['cancel_ro_by_admin'] == 1) {?>
                        <td><?php echo 'Cancel Approved'?></td>
                    <?php } else{?>
                        <td><?php echo 'Rejected'?></td>
                    <?php }?>
                </tr>
            <?php }?>

        </table>
    </div>		<!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>		<!-- .login ends -->
