<?php
/**
 * View to Display the Client Channel RelationShip
 */
include_once dirname(__FILE__) . "/../inc/header.inc.php";
?>
<script  type="text/javascript" src="/surewaves_easy_ro/js/spot_reach_report/chosen.jquery.js"></script>
<script  type="text/javascript" src="/surewaves_easy_ro/js/spot_reach_report/prism.js"></script>
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
                <h2>Client Channel Relation</h2>
            </div>

            <div class="block_content">
                <?php echo form_open('/network_svc_manager/establishClientChannelRelations'); ?>
                <div class="nw_main">    
                    <p><label>Select Advertiser:</label> <br/>
                       <select name="client" id="client" class="select_class" style="width:350px;">
                            <?php foreach($client as $id=>$value) { ?>
                                <option value="<?php echo $id ?>"><?php echo $value ?></option>
                            <?php } ?>    
                        </select>
                    </p>

                    <p><label>Select Market:</label> <br/>
                        <select name="market" id="market" class="select_class" style="width:350px;" onchange="javascript:getChannelsForMarket(this.value)">
                            <option value="-">-</option>
                            <?php foreach($market as $id=>$value) { ?>
                                <option value="<?php echo $id ?>"><?php echo $value ?></option>
                            <?php } ?>    
                        </select>
                    </p>

                    <p><label>Select Channels:</label> <br />
                        <select name="channel[]" id="channel" class="common_drpdwn" multiple style="width:350px;" data-placeholder="Select a Channel">
                            <option value="-">-</option>  
                        </select>
                    </p>
                </div>
                <input type="submit" class="submit" value="submit" onclick="return check_form();" />
                
                <?php echo form_close(); ?>
            </div>

            <div class="bendl"></div>
            <div class="bendr"></div>
        </div>

    </div>
</div>
<script language="javascript">
function check_form() {
    market_id = document.getElementById("market").value ;
    channel_id = document.getElementById("channel").value ;
    if(market_id == "-") {
        alert("please select market Name") ;
        return false;
    }
    if(channel_id == "-") {
        alert("please select market Name");
        return false;
    }
}
function getChannelsForMarket(market_id) {
   $.ajax({
        type: 'POST',
        url: '/surewaves_easy_ro/network_svc_manager/getChannelForMarket',
        data: {
            market_id: market_id,
            //client_id:document.getElementById("client").value ;
        },
        beforeSend: function(){
            $("#channel").html("");
            //$("#channel").append("<option value=''>--</option>");
        },
        dataType: 'json',
        success: function(data){
            if(data != ''){
                $.each(data, function (index, item) {
                    $("#channel").append("<option value='"+item.channel_id+"'>" + item.channel_name  + "</option>");
                });
            }else{

            }
        }
    });
}
</script>