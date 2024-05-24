<div class="" style="margin:0px; width:870px;">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2 style="margin:0">Add/Edit Agency</h2>
    </div>
    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/utility/post_add_agency" method="post" enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="12" width="100%">
                <?php if(($activeTab==5) && isset($validation_errors) && $validation_errors!="" ) {?>
                    <tr>
                        <td class="errors" colspan="3"><?php echo $validation_errors ?></td>
                    </tr>
                <?php }?>
                <?php if(($activeTab==5) && isset($success_msg) && $success_msg!="" ) {?>
                    <tr>
                        <td class="success" colspan="3"><?php echo $success_msg ?></td>
                    </tr>
                <?php }?>


                <tr>
                    <td>Agency
                    </td>
                    <td> : </td>
                    <td>
                        <select name="editAgency" id="editAgency" class="chosen-select" style="width:220px;" onchange="javascript:getAgencyDisplays(this.value);resetAgency(0);$('#agencyName').val(this.options[this.selectedIndex].text)">
                            <option value=""></option>
                            <?php foreach($allAgency as $key=> $val){ ?>
                                <option value="<?php echo $val['id']?>">
                                    <?php echo $val['agency_name']?>
                                </option>
                            <?php }?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Agency Display Name
                    </td>
                    <td> : </td>
                    <td>
                        <select name="editDisAgency" id="editDisAgency" class="chosen-select" style="width:220px;" onchange="javascript:getAgencyDetails(this.value)">

                        </select>
                        <!-- <input type="button" class="edit" value="Edit" onclick="editAdvertiser();"/>-->
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:center">(OR)</td>
                </tr>

                <tr>
                    <td><span id="addAgency">Agency Name</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <input type="text" name="agencyName" id="agencyName" value="" />
                    </td>
                </tr>

                <tr>
                    <td><span id="addDisAgency">Agency Display Name</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <input type="text" name="agencyDisName" id="agencyDisName" value="" />
                    </td>
                </tr>

                <tr>
                    <td><span id="postAddress">Postal Address</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <input type="text" name="postalAgencyAddress" id="postalAgencyAddress" value="" />
                    </td>
                </tr>

                <tr>
                    <td><span id="billInfo">Billing Info</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <select name="billingAgencyInfo" id="billingAgencyInfo">
                            <option value="">-</option>
                            <option value="Brand wise">Brand wise</option>
                            <option value="Market wise">Market wise</option>
                            <option value="Content wise">Content wise</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><span id="billAddress">Billing Address</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <input type="text" name="billingAgencyAddress" id="billingAgencyAddress" value="" />
                    </td>
                </tr>

                <tr>
                    <td><span id="billingCycle">Billing Cycle</span> <span style="color:#F00;"> *</span></td>
                    <td> : </td>
                    <td>
                        <select name="billAgencyCycle" id="billAgencyCycle">
                            <option value="">-</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Consolidated">Consolidated</option>
                        </select>

                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <input type="submit" class="submit" value="Save" onclick="return check_agency_form();" />
                        <input type="button" class="submit" value="Reset" onclick="resetAgency(1);" />
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>
<script>
    function check_agency_form()
    {
        if($('#agencyName').val() == ""){
            alert("Please enter an Agency Name");
            $('#agencyName').focus();
            return false;
        }
        var agency_name = $('#agencyName').val();
        var test_for_char = Utility.checkInput1(agency_name);
        if(!test_for_char)	{
            alert("Invalid Agency Name");
            $('#agencyName').focus();
            return false;
        }
        var check_input = Utility.checkTextWordLengthForAgency(agency_name);
        if(!check_input) {
            alert('Agency Name cannot be more than 75 characters.');
            $('#agencyName').focus();
            return false;
        }


        if($('#agencyDisName').val() == ""){
            alert("Please enter an Agency Display Name");
            $('#agencyDisName').focus();
            return false;
        }
        var agency_dis_name = $('#agencyDisName').val();
        var test_for_char = Utility.checkInput1(agency_dis_name);
        if(!test_for_char)	{
            alert("Invalid Agency Display Name");
            $('#agencyDisName').focus();
            return false;
        }
        var check_input = Utility.checkTextWordLengthForAgency(agency_dis_name);
        if(!check_input) {
            alert('Agency Display Name cannot be more than 75 characters.');
            $('#agencyDisName').focus();
            return false;
        }


        if($('#postalAgencyAddress').val() == ""){
            alert("Please enter an Agency Postal Address");
            $('#postalAgencyAddress').focus();
            return false;
        }
        var agency_address = $('#postalAgencyAddress').val();
        var test_for_char = Utility.checkInputAddress(agency_address);
        if(!test_for_char)	{
            alert("Invalid Agency Postal Address");
            $('#postalAgencyAddress').focus();
            return false;
        }

        if($('#billingAgencyAddress').val() == ""){
            alert("Please enter Agency Billing Address");
            $('#billingAgencyAddress').focus();
            return false;
        }
        var billing_address = $('#billingAgencyAddress').val();
        var test_for_char = Utility.checkInputAddress(billing_address);
        if(!test_for_char)	{
            alert("Invalid Agency Billing Address");
            $('#billingAgencyAddress').focus();
            return false;
        }

        if($('#billAgencyCycle').val() == ""){
            alert("Please enter a Payment cycle");
            $('#billAgencyCycle').focus();
            return false;
        }
    }
    /*function editProduct()
    {
        var id = $("#product_product").val();
        var value = $("#product_product option:selected").text();
        if(id!=null && id!="" && id!=0)
        {
            $("#addNewProduct").val($.trim(value));
            $("#ProductID").val(id);
            $("#ProductLbl").html("Edit Product");
        }
        else
            alert("Select Product to edit the value");
    }
    function resetProduct()
    {
        $("#addNewProduct").val("");
        $("#ProductID").val(0);
        $("#ProductLbl").html("Add New Product");
    }*/
    function resetAgency(tag)
    {
        if(tag == 1){
            $("#editAgency option[value= '']").prop("selected",true);
            $("#editDisAgency option[value= '']").prop("selected",true);
            $(".chosen-select").trigger("chosen:updated");
        }
        $('#agencyName').val('');
        $('#agencyDisName').val('');
        $('#postalAgencyAddress').val('');
        $("#billingAgencyInfo option[value= '']").prop("selected",true);
        $('#billingAgencyAddress').val('');
        $("#billAgencyCycle option[value= '']").prop("selected",true);
    }
</script>