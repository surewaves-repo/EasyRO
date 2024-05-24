<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<div class="block small center login" style="margin:0px">


    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Assign Region and Reporting Manager:</h2>
    </div>

    <div class="block_content">

        <form action="<?php echo ROOT_FOLDER ?>/admin/assignRegionReportingManager" method="post">

            <p>
                <label>Region: </label> <br />
                <select name="region" id="region" style="	background: rgb(254, 254, 254) none repeat scroll 0 0;border: 1.5px solid rgb(187, 187, 187);height: 33px;width: 428px;border-radius: 3px;" >

                    <?php foreach( $regions as $region ) { ?>

                        <option value="<?php echo $region['id'] ?>"><?php echo $region['region_name'] ?></option>

                    <?php } ?>

                </select>
            </p>
            <p>
                <label>Reporting Manger: </label> <br />
                <select name="reporting_manager" id="reporting_manager" style="	background: rgb(254, 254, 254) none repeat scroll 0 0;border: 1.5px solid rgb(187, 187, 187);height: 33px;width: 428px;border-radius: 3px;" >
                    <?php foreach( $reportingMangers as $rm ) { ?>

                        <option value="<?php echo $rm['user_id'] ?>"><?php echo $rm['user_name'] ?></option>

                    <?php } ?>

                </select>
            </p>
            <p>
                <input type="hidden" name="userId" value="<?php echo $userId ;?>">
                <input type="hidden" name="profileId" value="<?php echo $profileId ; ?>">

                <input type="submit" class="submit" value="submit" />
            </p>
        </form>

    </div>		<!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>		<!-- .login ends -->
