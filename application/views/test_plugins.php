<style type="text/css" media="all">
    @import url("/surewaves_easy_ro/css/style.css");
    @import url("/surewaves_easy_ro/css/jquery.wysiwyg.css");
    @import url("/surewaves_easy_ro/css/facebox.css");
    @import url("/surewaves_easy_ro/css/visualize.css");
    @import url("/surewaves_easy_ro/css/colorbox.css");
</style>

<div id="hld">

    <div class="wrapper">       <!-- wrapper begins -->



        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>      <!-- #header ends -->

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h2>Submit Advanced External RO</h2>
            </div>

            <a href="javascript:open_colorbox()">Open colorbox</a>

        </div>
    </div>
</div>

<script type="application/javascript">
    function open_colorbox(){
        $.colorbox({href:'https://www.google.co.in',iframe:true, width: '520px', height:'700px'});
    }
</script>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.colorbox.js"></script>
