<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/flexigrid.pack.css" />
<script type="text/javascript" src="/surewaves_easy_ro/js/flexigrid.js"></script>

<div id="hld">

    <div class="wrapper">		<!-- wrapper begins -->



        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>Actioned Requests</h2>

            </div>		<!-- .block_head ends -->
            <div class="block_content">
                <?php if(count($actioned_requests) > 0) {?>
                <table cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <th style="width: 25%">Customer RO Number</th>
                        <th style="width: 10%">RO Type</th>
                        <th style="width: 12%">Request Type</th>
                        <th style="width: 20%">Markets</th>
                        <th style="width: 22%">Reason</th>
                        <th style="width: 11%">Action</th>
                    </tr>

                    <?php foreach($actioned_requests as $req){?>
                    <tr>
                        <?php if($positionLevelForUser <= $req['approval_level']){ ?>
                            <?php if($req['non_fct'] == '1'){?>
                                <td> <a href="javascript:review_non_fct_ro_details('<?php echo $req['ext_ro_id']; ?>','<?php echo $req['id'] ?>','<?php echo $req['cancel_type']; ?>')"><?php echo $req['cust_ro'];?></a> <br/> (<?php echo $req['internal_ro']; ?>) </td>
                                <td>NON FCT</td>
                            <?php } else {?>
                                <td> <a href="javascript:review_fct_ro_details('<?php echo $req['ext_ro_id']; ?>','<?php echo $req['id'] ?>','<?php echo $req['cancel_type']; ?>')"><?php echo $req['cust_ro'];?></a> <br/> (<?php echo $req['internal_ro']; ?>) </td>
                                <td>FCT</td>
                            <?php } ?>

                            <td><?php
                                if($req['cancel_type'] == 'cancel_market') {?>
                                    <a href="javascript:show_revised_market_price('<?php echo $req['id'] ?>')"><?php echo str_replace("_", " ", $req['cancel_type']); ?> &nbsp;</a>
                                <?php }else
                                if($req['cancel_type'] == 'cancel_content') {?>
                                    <span>Cancel Content &nbsp;</span>
                                <?php }else

                                if($req['cancel_type'] == 'cancel_brand') {?>
                                    <span>Cancel Brand &nbsp;</span>
                                <?php }else

                                if($req['cancel_type'] == 'cancel_ro') {?>
                                    <a href="javascript:show_revised_ro_amount('<?php echo $req['id'] ?>')"><?php echo str_replace("_", " ", $req['cancel_type']); ?> &nbsp;</a>
                                <?php } else

                                if($req['cancel_type'] == 'submit_ro_approval'){?>
                                    <span>RO Approval &nbsp;</span>
                                <?php }else

                                if($req['cancel_type'] == 'ro_approval'){?>
                                    <span>Schedule Approval &nbsp;</span>
                                <?php } ?>

                            </td>
                            <td><?php if($req['cancel_type'] == 'cancel_market') {
                                    echo $req['market'];
                                }else{
                                    echo "All";
                                } ?> &nbsp;
                            </td>
                            <td><?php echo $req['reason']; ?></td>
                            <td>
                                <?php if($req['cancel_status'] ==  1 ){ ?>
                                    <span>Approved</span>
                                    <?php if($req['non_fct'] == '1'){ ?>
                                        <a href="javascript:non_fct_ro_approval('<?php echo rtrim(base64_encode($req['internal_ro']),'=') ?>')">(edit)</a>
                                    <?php } ?>
                                <?php } ?>

                                <?php if($req['cancel_status'] ==  2 ){ ?>
                                    <span>Rejected</span>
                                <?php } ?>

                                <?php if($req['cancel_status'] ==  3 ){ ?>
                                        <span>Forwarded</span>
                                    <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>

                </table>

                <div class="pagination right">
                    <?php echo $page_links ?>
                </div>		<!-- .paggination ends -->
                <?php } else {?>
                    <h2 style="text-align:center;">No Actioned Requests !</h2>
                <?php } ?>

            </div>	<!-- .block ends -->


        </div>
    </div>
</div>
<script language="javascript">

    function show_revised_market_price(cancel_id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/revised_market_price_for_ro/'+cancel_id,iframe:true, width: '800', height:'350px'});
    }

    function show_revised_ro_amount(cancel_id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/revised_ro_amount/'+cancel_id,iframe:true, width: '800', height:'350px'});
    }

    function review_fct_ro_details(order_id,cancel_id,cancel_type){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/review_fct_ro_details/'+order_id + "/" +cancel_id  + "/" +cancel_type,iframe:true, width: '1040', height:'650px'});
    }

    function review_non_fct_ro_details(order_id,cancel_id,cancel_type){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/review_non_fct_ro_details/'+order_id + "/" +cancel_id + "/" +cancel_type,iframe:true, width: '940px', height:'650px'});
    }

    function non_fct_ro_approval(order_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/non_fct_ro/approve_non_fct/" + order_id ;
    }

</script>
	
