<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 10/9/15
 * Time: 5:37 PM
 */
?>
<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<div class="block">

    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Edit RO Dates</h2>
    </div>

    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_changed_ro_dates" method="post"
              enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="0" width="100%">

                <tr>
                    <td width="30%">RO Date</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" class="text" id="txt_ro_date" name="txt_ro_date" readonly="readonly" disabled
                               style="width:220px;" value="<?php echo $ro_details[0]['ro_date'] ?>"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td width="30%">Campaign Start Date<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" class="text" id="txt_camp_start_date" name="txt_camp_start_date"
                               style="width:220px;" value="<?php echo $ro_details[0]['camp_start_date'] ?>"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Campaign End Date<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" class="text" id="txt_camp_end_date" name="txt_camp_end_date"
                               style="width:220px;" value="<?php echo $ro_details[0]['camp_end_date'] ?>"/>
                    </td>
                    <td width="40%">&nbsp;</td>
                </tr>

                <tr>
                    <td>
                        <input type="hidden" name="hid_ro_id" id="hid_ro_id"
                               value="<?php echo $ro_details[0]['id'] ?>"/>
                        <input type="submit" class="submit" value="Submit" onclick="return check_form();"/>
                    </td>
                <tr>

            </table>
        </form>
    </div>
    <div class="bendl"></div>
    <div class="bendr"></div>
</div>

<?php include_once dirname(__FILE__) . "/inc/footer.inc.php" ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        $("#txt_camp_start_date").datepicker({
            defaultDate: "+1w",
            minDate: 1,
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            onClose: function (selectedDate) {
                $("#txt_camp_end_date").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#txt_camp_end_date").datepicker({
            defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            onClose: function (selectedDate) {
                $("#txt_camp_start_date").datepicker("option", "maxDate", selectedDate);
            }
        });
    });

    function check_form() {
        var start_date = $("#txt_camp_end_date").val();
        var end_date = $("#txt_camp_end_date").val();

        if (start_date == '') {
            alert("Please select Start date");
            return false;
        }
        if (end_date == '') {
            alert("Please select End date");
            return false;
        }
    }
</script>

