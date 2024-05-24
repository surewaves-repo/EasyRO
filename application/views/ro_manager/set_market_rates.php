<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

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

                <h2>Market List Rates</h2>
            </div>

            <div class="block_content" style="height: auto">
                <form action="<?php echo ROOT_FOLDER ?>/ro_manager/postMarketRate" method="post" enctype="multipart/form-data">

                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td>Markets<span style="color:#F00;"> *</span></td>
                            <td>:</td>
                            <td><select name="market_id" id="market" style="width:260px;height: 30px" onchange="get_market_price(this.value)">
                                    <option value="-">-</option>
                                    <?php foreach($all_markets as $mkt) { ?>
                                        <option value="<?php echo $mkt['id'] ?> "><?php echo $mkt['sw_market_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Spot Price<span style="color:#F00;"> *</span></td>
                            <td>:</td>
                            <td><input type="text" class="rate" id="spot_rate" name="spot_rate" value="0" style="width:245px;" /></td>
                        </tr>

                        <tr>
                            <td>Banner Price<span style="color:#F00;"> *</span></td>
                            <td>:</td>
                            <td><input type="text" class="rate" id="banner_rate" name="banner_rate" value="0" style="width:245px;" /></td>
                        </tr>

                        <tr>
                            <td colspan=""><input type="submit" class="submit" value="Save" onclick="return check_input_price();" /></td>
                        </tr>
                    </table>

                </form>
            </div>		<!-- .block_content ends -->

            <div class="bendl"></div>
            <div class="bendr"></div>

        </div>		<!-- .block ends -->

    </div>
</div>


<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>

<script type="text/javascript" language="javascript">

    function get_market_price(market_id){
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/ro_manager/get_market_details',
            async: false,
            data: {
                market_id:market_id
            },
            dataType: 'json',
            success: function(data){
                if(data != ''){
                    $('#spot_rate').val(data[0].spot_rate);
                    $('#banner_rate').val(data[0].banner_rate);
                }else{
                    $('#spot_rate').val(0);
                    $('#banner_rate').val(0);
                }
            }
        });
    }

    function check_input_price(){
        var spot            = $('#spot_rate').val();
        var spot_rate      = parseFloat(spot).toFixed(2);
        var banner          = $('#banner_rate').val();
        var banner_rate    = parseFloat(banner).toFixed(2);

        var response_spot = isNormalInteger(spot);
        if(response_spot == false)
        {
            alert("Please enter a positive numerical amount");
            setTimeout("$('#spot_rate').focus()",1);
            return false;
        }

        var response_banner = isNormalInteger(banner);
        if(response_banner == false)
        {
            alert("Please enter a positive numerical amount");
            setTimeout("$('#banner_rate').focus()",1);
            return false;
        }
    }

    function isNormalInteger(str) {
        return /^\+?(0|[0-9]\d*|[0-9]\d*[.][0-9]{2})$/.test(str);
    }


</script>
