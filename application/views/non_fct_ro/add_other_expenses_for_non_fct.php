<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<script type="text/javascript" src="/surewaves_easy_ro/js/datetimepicker.js"></script>


<div class="block small center login" style="margin:0px">

    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Add Other Expenses Amounts</h2>
    </div>
    <?php
    $ro_amount = $ro_details[0]['ro_amount'];
    $agency_commission = $ro_details[0]['agency_commission_amount'];
    ?>
    <div class="block_content">

        <form action="<?php echo ROOT_FOLDER ?>/non_fct_ro/updateNonFctOtherExpense" method="post" id="myform">
            <p>
                <label>Agency Rebate Amount(%):* </label><br />
                <input type="text" class="text" name="agency_rebate" id="agency_rebate"  value="<?php echo $ro_details[0]['agency_rebate'] ?>" style="width:250px;"
                       onblur ="check(<?php echo $ro_details[0]['gross_ro_amount'] ?>,<?php echo $ro_details[0]['other_expenses_amount'] ?>);" />
                </br>
                <label>Marketing Promotion Amount:* </label><br />
                <input type="text" class="text" name="marketing_promotion_amount" id ="markt_promotion" value="<?php echo $ro_details[0]['marketing_promotion_amount'] ?>"
                       onblur ="check(<?php echo $ro_details[0]['gross_ro_amount'] ?>,<?php echo $ro_details[0]['other_expenses_amount'] ?>);" />
                <label>Field Activation and Research Support Amount:* </label><br />
                <input type="text" class="text" name="field_activation_amount" id="field_activation" value="<?php echo $ro_details[0]['field_activation_amount'] ?>"
                       onblur ="check(<?php echo $ro_details[0]['gross_ro_amount'] ?>,<?php echo $ro_details[0]['other_expenses_amount'] ?>);" />
                <label>Sales Commissions Amount:* </label><br />
                <input type="text" class="text" name="sales_commissions_amount" id="sales_commission" value="<?php echo $ro_details[0]['sales_commissions_amount'] ?>"
                       onblur ="check(<?php echo $ro_details[0]['gross_ro_amount'] ?>,<?php echo $ro_details[0]['other_expenses_amount'] ?>);" />
                <label>Creative Services Amount:* </label><br />
                <input type="text" class="text" name="creative_services_amount" id="creative_services" value="<?php echo $ro_details[0]['creative_services_amount'] ?>"  />
                <label>Other Expenses Amount:* </label><br />
                <input type="text" class="text" name="other_expenses_amount" id ="other_expenses" value="<?php echo $ro_details[0]['other_expenses_amount'] ?>"
                       onblur ="check(<?php echo $ro_details[0]['gross_ro_amount'] ?>,<?php echo $ro_details[0]['other_expenses_amount'] ?>);" />

                <input type="hidden" name="hid_ro_valid" id="ro_valid_field" value="0" />
                <input type ="hidden" name="hid_ro_amount" id="ro_amount" value="6000">
                <input type ="hidden" name="hid_agency_commission" id="agency_commission" value="0">
                <input type="hidden" name="hid_internal_ro" value="">
                <input type="hidden" name="hid_customer_ro" value="TestROND">
                <input type="hidden" name="hid_url" value="">

                <input type="hidden" name="hid_edit" value="1">
                <input type="hidden" name="hid_id" value="<?php echo $ro_details[0]['id'] ?>">
                <input type="hidden" name="hid_internal_ro" value="">
            </p>
            <p>
                <input type="submit" class="submitlong" name="ap" value="Add Expenses Amounts" />
            </p>
        </form>

    </div>		<!-- .block_content ends -->
    <div class="bendl"></div>
    <div class="bendr"></div>

</div>		<!-- .login ends -->
<script language="javascript">

    function check(ro_amount,agency_commission)
    {
        var net_amount = parseFloat(ro_amount - agency_commission);
        var agency_rebate = parseFloat(document.getElementById("agency_rebate").value * ro_amount/100);
        var markt_promotion = parseFloat(document.getElementById("markt_promotion").value);
        var filed_activation = parseFloat(document.getElementById("field_activation").value);
        var sales_commission = parseFloat(document.getElementById("sales_commission").value);
        var creative_services = parseFloat(document.getElementById("creative_services").value);
        var other_expenses = parseFloat(document.getElementById("other_expenses").value);

        var total_expenses = parseFloat(document.getElementById("agency_rebate").value * ro_amount/100);

        total_expenses += parseFloat(document.getElementById("markt_promotion").value,10);
        total_expenses += parseFloat(document.getElementById("field_activation").value,10);
        total_expenses += parseFloat(document.getElementById("sales_commission").value,10);
        total_expenses += parseFloat(document.getElementById("creative_services").value,10);
        total_expenses += parseFloat(document.getElementById("other_expenses").value,10);

        if(net_amount <= total_expenses)
        {
            alert("Please make sure that the sum of all expenses is less than the Gross CRO Amount");
        }
    }
    $(function()
    {
        $(".submitlong").click(function()
        {
            var isValid = true;
            var ro_amount = parseFloat(document.getElementById("ro_amount").value);
            var agency_commission = parseFloat(document.getElementById("agency_commission").value);
            var net_amount = ro_amount - agency_commission;

            var total_expenses = parseFloat(document.getElementById("agency_rebate").value * ro_amount/100);

            total_expenses += parseFloat(document.getElementById("markt_promotion").value,10);
            total_expenses += parseFloat(document.getElementById("field_activation").value,10);
            total_expenses += parseFloat(document.getElementById("sales_commission").value,10);
            total_expenses += parseFloat(document.getElementById("creative_services").value,10);
            total_expenses += parseFloat(document.getElementById("other_expenses").value,10);

            if(isNaN(net_amount))
            {
                alert("Please enter Gross CRO amount details ");
                $("#myform input[type=text]").each(function() {
                    $(this).css('background', 'red');
                    isValid = false;
                });
            } else if(net_amount <= total_expenses)
            {
                alert("Please make sure that the sum of all expenses is less than the Gross CRO Amount");
                $("#myform input[type=text]").each(function() {
                    $(this).css('background', 'red');
                    isValid = false;
                });
            }else {
                $("#myform input[type=text]").each(function() {
                    $(this).css('background', '#31B404');
                });
            }
            if(isValid === false) {
                return false;
            } else {
                //Do everything you need to do with the form
            }
        });
    });
</script>	
