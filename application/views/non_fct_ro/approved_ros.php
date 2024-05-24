<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 7/6/15
 * Time: 4:26 PM
 */

include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<div id="hld">

    <div class="wrapper">		<!-- wrapper begins -->



        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>


            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>


            <?php echo $menu ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->






        <div class="block">

            <form method="post" action="<?php echo ROOT_FOLDER ?>/non_fct_ro/search_content_approved">
                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2> Approved Release Orders</h2>

                    <ul class="" style="float:left">
                        <li class=""><a href="javascript:show_fct_ros()">FCT</a></li>
                        <li class=" nobg active"><a href="javascript:show_non_fct_ros()">NON FCT</a></li>
                    </ul>

                    <ul style="float:right;padding-left:10px;">
                        <li ><label> </label> &nbsp; <input type="text" id="SearchTF" class="text" style="width: 300px" placeholder="Enter text to search ..." value="<?php if ( isset($search_str) && !empty($search_str) ) { echo $search_str; } ?>" name="search_str"   /></li>
                        <li ><input type="submit" class="submit" value="search"   /></li>
                    </ul>

                </div>		<!-- .block_head ends -->
            </form>


            <div class="block_content">
                <?php if($value == 0) { ?>
                    <h2 style="text-align:center;"><!--Schedule is not prepared due to lack of Inventory--></h2>
                <?php } else { ?>
                <table cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <th>Customer RO Number</th>
                        <th>Submitted By</th>
                        <th>Approved By</th>
                        <th>Agency Name</th>
                        <th>Advertiser Name</th>
                        <th>Financial Year</th>
                        <td>&nbsp;</td>
                    </tr>

                    <?php foreach ( $ro_list as $content) { ?>
                        <tr>
                            <td> <?php echo $content['customer_ro_number']?><br/> <?php echo '(Internal RO Number:'.$content['internal_ro_number'].")" ; ?> </td>
                            <td><?php echo $content['account_manager_name'] ?> &nbsp;</td>
                            <td><?php echo "National Head" ?> &nbsp;</td>
                            <td><?php echo $content['agency'] ?> &nbsp;</td>
                            <td><?php echo $content['client'] ?> &nbsp;</td>
                            <td><?php echo $content['financial_year']; ?> </td>
                            <td><a href="javascript:non_fct_ro_approval('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')"><img src="<?php echo ROOT_FOLDER ?>/images/add-one.png" /></a></td>
                        </tr>
                    <?php } ?>
                </table>

                <div class="paggination right">
                    <?php echo $page_links ?>
                </div>		<!-- .paggination ends -->



            </div>		<!-- .block_content ends -->
            <?php } ?>
            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>		<!-- .block ends -->












    </div>						<!-- wrapper ends -->

</div>		<!-- #hld ends -->


<script language="javascript">

    function add_new_ro() {

        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_ro',iframe:true, width: '530px', height:'820px'});
    }
    function change_user_password(id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/change_user_password/' + id,iframe:true, width: '530px', height:'280px'});
    }

    function show_fct_ros(){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approved_ros";
    }
    function show_non_fct_ros(){
        window.location.href = "<?php echo ROOT_FOLDER ?>/non_fct_ro/approved_ros";
    }

    function order_details(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/order_details/" + order_id;
    }

    function show_channels(order_id) {

        $.colorbox({href:"<?php echo ROOT_FOLDER ?>/ro_manager/show_channels/" + order_id,iframe:true,width:'515px',height:'374px'});
    }
    function approval_request(order_id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approval_request/" + order_id ;
    }

    function non_fct_ro_approval(order_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/non_fct_ro/approve_non_fct/" + order_id ;
    }

    function add_caption_tape_id(id){
        $.colorbox({href:"<?php echo ROOT_FOLDER ?>/agency/add_caption_tape_id/" + id,iframe:true,width:'550px',height:'540px'});
    }

    $(document).ready(function() {
        <?php
          $selected_user_id = $this->session->userdata('selected_user_id');
          if ( isset($selected_user_id) && !empty($selected_user_id) ) { ?>
        change_user_password('<?php echo $selected_user_id ?>');
        <?
        $this->session->unset_userdata("selected_user_id");
      } ?>
    });

    function add_ro_amount(external_ro) {
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/ro_manager/add_ro_amount/'+external_ro,iframe:true, width: '540px', height:'250px'});
    }
</script>
