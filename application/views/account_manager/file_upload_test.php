<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<div id="hld">

    <div class="wrapper">        <!-- wrapper begins -->


        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>


            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px;
                                               height=35px; style="padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png"
                 style="padding-top:10px;float:right;padding-left:40px;"/>


            <p class="user">Hello| <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>

        <div class="block">
            <form method="post" action="<?php echo ROOT_FOLDER ?>/account_manager/multipleFileUpload"
                  enctype="multipart/form-data">
                <label> Attach RO </label> <br>
                <input type="file" id="file_pdf" name="file_pdf" class="file_class"/>
                <br/><br/>
                <input type="submit" class="submit" value="Create"/>
            </form>
        </div>
    </div>
</div>