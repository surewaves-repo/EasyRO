<?php
/**
 * View to Display the Channel Status
 */
include_once dirname(__FILE__) . "/../inc/header.inc.php";
?>
<style type="text/css">
    .block form select.sw-select {
        width: 245px;
        height: 33px;
        margin-right: 20px;
        padding: 7px;
        display: inline-block;
        margin-right: 20px;
        border: 1px solid #bbb;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        /*background: url('../images/sdd.jpg') center right no-repeat;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;*/
    }
    .block form input.textsmall{
        width: 126px !important;

    }
    .block form .sw-select:hover {
        /*background: url('../images/sdd_.jpg') center right no-repeat;*/
    }
</style>

<div id="hld">

    <div class="wrapper">

        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">
                Hello, <?php echo $logged_in_user['user_name'] ?>
                | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a>
            </p>
        </div>

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h2>Edit Channel Display Names</h2>
            </div>

            <div class="block_content">

                <?php if(isset($msg)): ?>
                    <div class="message info">
                        <p>
                            <?php echo $msg ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div>
                    <form method="post" action="<?php base_url('network_svc_manager/manageChannelName'); ?>">

                        <label>Filter : </label>

                        <?php echo form_dropdown('filterBy', $filterOptions['options'], ($filterOptions['filterBy'] == '0' ? $filterOptions['options'][0] : $filterOptions['filterBy']), 'id = "selFilter" class="sw-select"'); ?>

                        <div id="filterKeyContainer" style="display: inline;">
                            <?php
                            echo form_input('filterKeyText', ($filterOptions['filterKey'] == '0' ? '' : $filterOptions['filterKey']), 'id = "filterKeyText" placeholder="Enter Keyword" required="required" class="textsmall"');
                            ?>
                        </div>

                        <input type="submit" name="filter" value=" Filter " class="submit" />
                        <input type="button" name="reset" value=" Reset " class="submit" id="resetBtn" data-href="<?php echo base_url('network_svc_manager/manageChannelName') ?>" />

                    </form>
                </div>
                <form id="submitUpdateForm" method="post" action="<?php echo base_url('network_svc_manager/updateChannelNames'); ?>">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 10%">Network</th>
                            <th style="">Display Name</th>
                            <th style="width: 10%">Channel Id</th>
                            <th style="width: 10%">Channel Name</th>
                            <th style="width: 15%">Display Name</th>
                            <th style="width: 15%">Locale</th>
                            <th style="width: 10%">Deployment Status</th>
                            <th style="width: 25%">Markets/Clusters</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = $links['from'];
                        $previous_customer_id  = '';
                        foreach($data as $d):
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $d['network_name']; ?></td>
                                <td>
                                    <?php if($previous_customer_id == '' || $previous_customer_id != $d['customer_id']) {?>
                                    <input id="newCustomerDisplayName_<?php echo $d['channel_id']; ?>"  type="text" class="textsmall" name="newCustomerDisplayName_[<?php echo $d['channel_id']; ?>]" value="<?php echo $d['customer_display_name']; ?>">
                                    <input type="hidden" name="oldCustomerDisplayName_[<?php echo $d['channel_id']; ?>]" value="<?php echo $d['customer_display_name']; ?>">
                                    <label id="newCustomerDisplayName_<?php echo $d['channel_id']; ?>_label" style="color: red; font-family: 'Times New Roman, Times, serif'; font-size: 11px;display: none; margin-top: 5px;">Special characters are not allowed. </label>
                                    <?php  $previous_customer_id = $d['customer_id'];}?>
                                </td>
                                <td><?php echo $d['channel_id']; ?></td>
                                <td><?php echo $d['channel_name']; ?></td>

                                <td>
                                    <input id="newChannelDisplayName_<?php echo $d['channel_id']; ?>"  type="text" class="textsmall" name="newChannelDisplayName_[<?php echo $d['channel_id']; ?>]" value="<?php echo $d['display_name']; ?>">
                                    <input type="hidden" name="oldChannelDisplayName_[<?php echo $d['channel_id']; ?>]" value="<?php echo $d['display_name']; ?>">
                                    <label id="newChannelDisplayName_<?php echo $d['channel_id']; ?>_label" style="color: red; font-family: 'Times New Roman, Times, serif'; font-size: 11px;display: none; margin-top: 5px;">Special characters are not allowed. </label>
                                </td>
                                <td>
                                    <input id="newLocale_<?php echo $d['channel_id']; ?>"  type="text" class="textsmall" name="newLocale_[<?php echo $d['channel_id']; ?>]" value="<?php echo $d['locale']; ?>">
                                    <input type="hidden" name="oldLocale_[<?php echo $d['channel_id']; ?>]" value="<?php echo $d['locale']; ?>">
                                    <label id="newLocale_<?php echo $d['channel_id']; ?>_label" style="color: red; font-family:  'Times New Roman, Times, serif'; font-size: 11px;display: none; margin-top: 5px;">Special characters are not allowed. </label>

                                </td>
                                <td><?php echo $d['deployment_status']; ?></td>
                                <td><?php echo $d['market_name']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                   <!-- <div style="display: inline-block; float: left;">
                        <?php echo $links['links']; ?> <br />
                        <?php echo 'Showing ' . $links['from'] . ' - ' . $links['to'] . ' out of ' . $links['count'] . ' result(s)'; ?>
                    </div> -->
                    <div style="display: inline-block; float: right;">
                        <input type="hidden" name="channels" value="<?php echo $channels; ?>" />
                        <input id="channelsToUpdate" type="hidden" name="channelsToUpdate" value="" />

                        <input id="submitUpdate" type="submit" name="update" value=" Update " class="submit" />
                    </div>
                </form>
            </div>

            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>

    </div>
</div>
<script type="text/javascript">
    $("#resetBtn").click(function(){
        window.location = $(this).attr('data-href');
    });

    $("input").change(function(){
        var elementId = $(this).attr("id") ;

        if((elementId.split("_")[0]) != 'newCustomerDisplayName'){
            //var regex = new RegExp("^[a-zA-Z0-9\s]+$");
			var regex = /^[a-zA-Z0-9\s]+$/;
        }else{
            var regex = /^[a-zA-Z0-9\s\(\)]+$/;
        }
        
        var key = $(this).val();
            if( key.trim() != "" ){
                if (!regex.test(key)) {

                    $("#"+elementId+"_label").css('display', 'block') ;
                    $("#"+elementId).css("border", "1px solid red");
		            $( "#submitUpdate" ).attr('disabled', 'disabled');
		            $("#submitUpdate" ).css("cursor","not-allowed") ;
                }else{

                    $("#"+elementId+"_label").css('display', 'none') ;
                    $("#"+elementId).removeAttr("style");
		            $( "#submitUpdate" ).removeAttr('disabled');
		            $("#submitUpdate" ).css("cursor","pointer") ;
                }

            }else{

                $("#"+elementId+"_label").css('display', 'none') ;
                $("#"+elementId).removeAttr("style");
	            $( "#submitUpdate" ).removeAttr('disabled');
		        $("#submitUpdate" ).css("cursor","pointer") ;
            }

    }) ;


</script>
