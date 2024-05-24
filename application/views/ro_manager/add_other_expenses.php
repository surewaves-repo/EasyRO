<html>
<head>
    <script src="/surewaves_easy_ro/js/add_other_expenses.js"></script>
</head>
<body>
<?php
 $ro_amount = $ro_amount_details[0]['ro_amount'];
 $agency_commission = $ro_amount_details[0]['agency_commission_amount'];
 $sel_type = $ro_amount_details[0]['agency_rebate_on'];
?>
  <form id="add_other_expenses_form">
     <table class="table">
         <tr>
             <th style="display: none"></th>
             <th style="display: none"></th>
         </tr>
         <tr>
             <td>Agency Rebate Amount</td>
             <td><input type="text" name="agency_rebate" id="agency_rebate_new" onkeyup ="check(<?php echo $ro_amount ?>,<?php echo $agency_commission ?>);" value="<?php echo set_value('agency_rebate',$ro_amount_details[0]['agency_rebate']); ?>" ></td>
         </tr>
         <tr>
             <td colspan="2"><select name="sel_type" id="sel_type">
                     <option value="ro_amount" <?php if($sel_type != 'net_amount'){echo 'selected';}?> >Ro Amount</option>
                     <option value="net_amount" <?php if($sel_type == 'net_amount'){echo 'selected';}?> >Net Amount</option>
                 </select></td>
         </tr>
         <tr>
             <td>Marketing Promotion Amount</td>
             <td><input type="text" class="text" name="marketing_promotion_amount" id="marketing_promotion_amount" onkeyup ="check(<?php echo $ro_amount ?>,<?php echo $agency_commission ?>);" value="<?php echo set_value('marketing_promotion_amount',$ro_amount_details[0]['marketing_promotion_amount']); ?>"></td>
         </tr>
         <tr>
             <td>Field Activation and Research Support Amount</td>
             <td><input type="text" class="text" name="field_activation_amount" id="field_activation_amount" onkeyup ="check(<?php echo $ro_amount ?>,<?php echo $agency_commission ?>);" value="<?php echo set_value('field_activation_amount',$ro_amount_details[0]['field_activation_amount']); ?>"></td>
         </tr>
         <tr>
             <td>Sales Commission Amount</td>
             <td><input type="text" name="sales_commissions_amount" id="sales_commission_amount" onkeyup ="check(<?php echo $ro_amount ?>,<?php echo $agency_commission ?>);" value="<?php echo set_value('sales_commissions_amount',$ro_amount_details[0]['sales_commissions_amount']); ?>"></td>
         </tr>
         <tr>
             <td>Creative Services Amount</td>
             <td><input type="text" name="creative_services_amount" id="creative_services_amount" onkeyup ="check(<?php echo $ro_amount ?>,<?php echo $agency_commission ?>);" value="<?php echo set_value('creative_services_amount',$ro_amount_details[0]['creative_services_amount']); ?>"></td>
         </tr>
         <tr>
             <td>Other Expenses Amount</td>
             <td><input type="text" name="other_expenses_amount" id="other_expenses_amount" onkeyup ="check(<?php echo $ro_amount ?>,<?php echo $agency_commission ?>);" value="<?php echo set_value('other_expenses_amount',$ro_amount_details[0]['other_expenses_amount']); ?>"></td>
         </tr>
         <tr>
             <td colspan="2">
                 <input type="submit" value="Add Expenses Amount">
             </td>
         </tr>

         <input type="hidden" name="hid_ro_valid" id="ro_valid_field" value="0" />
         <input type ="hidden" name="hid_ro_amount" id="hid_ro_amount" value="<?php echo $ro_amount; ?>">
         <input type ="hidden" name="hid_agency_commission" id="hid_agency_commission" value="<?php echo $agency_commission; ?>">
         <input type="hidden" name="hid_internal_ro" id='hid_internal_ro' value="<?php echo $internal_ro_number; ?>">
         <input type="hidden" name="hid_customer_ro" id="hid_customer_ro" value="<?php echo $customer_ro_number; ?>">
         <input type="hidden" name="hid_url" id='hid_url' value="<?php echo $url_string ?>">
        <input type="hidden" name="hid_edit" id="hid_edit" value="<?php echo $edit ?>">
         <input type="hidden" name="hid_id" id="hid_id" value="<?php echo $id ?>">
         <input type="hidden" name="hid_internal_ro" id="hid_internal_ro" value="<?php echo $internal_ro ?>">

     </table>
  </form>
</body>
</html>