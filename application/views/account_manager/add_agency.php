<?php include_once dirname(__FILE__) . "/../inc/header.inc.php" ?>


<div class="block small center login" style="margin:0px; margin-left:200px;">


    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Add Agency</h2>
    </div>

    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/account_manager/post_add_agency" method="post">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="20%">Agency Name<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_agency_name" id="txt_agency_name"/>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Agency Billing Name<span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_agency_billing_name" id="txt_agency_billing_name"/>
                    </td>
                </tr>
                <!--<tr>
                    <td width="20%">Billing Address</td>
                    <td width="5%"> : </td>
                    <td width="25%">
                        <input type="text" name="txt_billing_address" />
                    </td>
                </tr>
                <tr>
                    <td width="20%">Payment Cycle</td>
                    <td width="5%"> : </td>
                    <td width="25%">
                        <input type="text" name="txt_pay_cylce" />
                    </td>
                </tr>	
                <tr>
                    <td width="20%">Agency Contact Name<span style="color:#F00;"> *</span</td>
                    <td width="5%"> : </td>
                    <td width="25%">
                        <input type="text" id="txt_agency_contact_name" name="txt_agency_contact_name" />
                    </td>
                </tr>
                <tr>
                    <td width="20%" nowrap="nowrap">Agency Contact No.<span style="color:#F00;"> *</span></td>
                    <td width="5%"> : </td>
                    <td width="25%">
                        <input type="text" id="txt_agency_contact_no" name="txt_agency_contact_no" maxlength="10" />
                    </td>
                </tr>
                <tr>
                    <td width="20%">Agency Location</td>
                    <td width="5%"> : </td>
                    <td width="25%">
                        <input type="text" name="txt_agency_location" />
                    </td>
                </tr>-->
                <tr>
                    <td width="20%">Agency Address <span style="color:#F00;"> *</span></td>
                    <td width="5%"> :</td>
                    <td width="25%">
                        <input type="text" name="txt_agency_address" id="txt_agency_address"/>
                    </td>
                </tr>
                <!-- <tr>
                     <td width="20%">Agency Category</td>
                     <td width="5%"> : </td>
                     <td width="25%">
                         <input type="text" name="txt_agency_category" />
                     </td>
                 </tr> -->
                <tr>
                    <td colspan="3">
                        <input type="hidden" id="hid_cust_ro" name="hid_cust_ro" value="<?php echo $cust_ro; ?>"/>
                        <input type="hidden" id="hid_ro_date" name="hid_ro_date" value="<?php echo $ro_date; ?>"/>
                        <input type="submit" class="submit" value="Add" onclick="return validate_agency();"/>
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
    function validate_agency() {
        if ($.trim(document.getElementById('txt_agency_name').value) == "") {
            alert('Please enter agency name.');
            document.getElementById('txt_agency_name').focus();
            return false;
        }

        var agency_name = document.getElementById('txt_agency_name').value;
        /*var test_alphanumeric = Utility.checkAlphaNumeric(agency_name);
        if(!test_alphanumeric)	{
            alert("Invalid Agency Name");
            document.getElementById('txt_agency_name').focus();
            return false;
        }
        */
        var test_for_char = Utility.checkInput1(agency_name);
        if (!test_for_char) {
            alert("Invalid Agency Name");
            document.getElementById('txt_agency_name').focus();
            return false;
        }

        var check_input = Utility.checkTextWordLengthForAgency(agency_name);
        if (!check_input) {
            alert('Agency Name cannot be more than 75 characters.');
            document.getElementById('txt_agency_name').focus();
            return false;
        }

        if ($.trim(document.getElementById('txt_agency_billing_name').value) == "") {
            alert('Please enter agency billing name.');
            document.getElementById('txt_agency_billing_name').focus();
            return false;
        }

        var agency_billing_name = document.getElementById('txt_agency_billing_name').value;
        var test_for_char = Utility.checkInput2(agency_billing_name);
        if (!test_for_char) {
            alert("Invalid Agency Billing Name");
            document.getElementById('txt_agency_billing_name').focus();
            return false;
        }


        if ($.trim(document.getElementById('txt_agency_address').value) == "") {
            alert('Please enter agency address.');
            document.getElementById('txt_agency_address').focus();
            return false;
        }

        var agency_address = document.getElementById('txt_agency_address').value;
        var test_for_char = Utility.checkInputAddress(agency_address);
        if (!test_for_char) {
            alert("Invalid Agency Address");
            document.getElementById('txt_agency_address').focus();
            return false;
        }


        /*if ( $('#txt_agency_name').blank() ) {
            alert('Please enter agency name.');
            $('#txt_agency_name').focus();
            return false;
        }*/
        /*if($.trim(document.getElementById('txt_agency_name').value) != ""){
            if(!checkSpecialCharacter(document.getElementById('txt_agency_name').value)){
                alert('Special characters are not allowed.');
                document.getElementById('txt_agency_name').focus();
                return false;
            }
        }
        */
        /*if(document.getElementById('txt_agency_contact_name').value == ""){
            alert('Please enter agency contact name.');
            document.getElementById('txt_agency_contact_name').focus();
            return false;
        }*/
        /*if(document.getElementById('txt_agency_contact_no').value == ""){
            alert('Please enter agency contact number.');
            document.getElementById('txt_agency_contact_no').focus();
            return false;
        }
        var regex = /^[0-9]+$/;
        contact_no = document.getElementById('txt_agency_contact_no').value;
        if(!regex.test(contact_no)){
            alert('Please enter only numbers.');
            document.getElementById('txt_agency_contact_no').value = "";
            document.getElementById('txt_agency_contact_no').focus();
            return false;
        } */

        /*if ( $('#txt_agency_billing_name').blank() ) {
            alert('Please enter agency billing name.');
            $('#txt_agency_billing_name').focus();
            return false;
        }*/
        /*if($.trim(document.getElementById('txt_agency_billing_name').value) != ""){
            if(!checkSpecialCharacter(document.getElementById('txt_agency_billing_name').value)){
                alert('Special characters are not allowed.');
                document.getElementById('txt_agency_billing_name').focus();
                return false;
            }
        }*/

        /*if ( $('#txt_agency_address').blank() ) {
            alert('Please enter agency address.');
            $('#txt_agency_address').focus();
            return false;
        }*/
        /*if($.trim(document.getElementById('txt_agency_address').value) != ""){
            if(!checkSpecialCharacter(document.getElementById('txt_agency_address').value)){
                alert('Special characters are not allowed.');
                document.getElementById('txt_agency_address').focus();
                return false;
            }
        }
        */
    }

    function go_back() {
        var external_ro = encodeURIComponent($('#hid_cust_ro').val().replace(/\//g, '~'));
        window.location = '<?php echo ROOT_FOLDER ?>/account_manager/create_ext_ro/' + external_ro + '/' + $('#hid_ro_date').val();
    }

    function checkSpecialCharacter(a) {
        //97 122 65 90 48 57 46 95
        for (var i = 0; i < a.length; i++) {
            if ((a.charCodeAt(i) >= 97 && a.charCodeAt(i) <= 122) ||
                (a.charCodeAt(i) >= 65 && a.charCodeAt(i) <= 90) ||
                (a.charCodeAt(i) >= 48 && a.charCodeAt(i) <= 57) ||
                (a.charCodeAt(i) == 32 || a.charCodeAt(i) == 10 || a.charCodeAt(i) == 09
        ))
            {
            }
        else
            {
                return false;
            }
        }
        return true;

    }

</script>-
