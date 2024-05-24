<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>


<div class="block small center login" style="margin:0px; margin-left:200px;">


    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Activate Client</h2>
    </div>

    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_activate_client" method="post">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="20%">Select Client</td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <select name="sel_client" id="sel_client">
                            <?php foreach ($client as $val) { ?>
                                <option value="<?php echo $val['id'] ?>"><?php echo $val['advertiser'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" class="submit" value="Activate"/>
                        <input type="hidden" id="hid_cust_ro" name="hid_cust_ro" value="<?php echo $cust_ro; ?>"/>
                        <input type="hidden" id="hid_ro_date" name="hid_ro_date" value="<?php echo $ro_date; ?>"/>
                        <input type="hidden" id="hid_agency" name="hid_agency" value="<?php echo $agency; ?>"/>
                        <input type="hidden" id="hid_agency_contact" name="hid_agency_contact"
                               value="<?php echo $agency_contact; ?>"/>
                        <input type="button" value="Back" class="submit" onclick="go_back()"/>
                    </td>
                <tr>
            </table>


        </form>
    </div>        <!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>

</div>        <!-- .login ends -->


<?php include_once dirname(__FILE__) . "/inc/footer.inc.php" ?>
<script type="text/javascript" language="javascript">
    function go_back() {
        var external_ro = encodeURIComponent($('#hid_cust_ro').val().replace(/\//g, '~'));
        window.location = '<?php echo ROOT_FOLDER ?>/account_manager/create_ext_ro/' + external_ro + '/' + $('#hid_ro_date').val() + '/' + $('#hid_agency').val() + '/' + $('#hid_agency_contact').val();
    }
</script>