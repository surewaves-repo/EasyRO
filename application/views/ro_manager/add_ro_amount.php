<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?> 

	<script type="text/javascript" src="/surewaves_easy_ro/js/datetimepicker.js"></script>
	
			
			<div class="block small center login" style="margin:0px">				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Add Customer RO Amount</h2>					
				</div>	
				
				<div class="block_content">							
					<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_add_ro_amount" method="post">
						<p>
							<label>Gross Customer RO Amount:* <?php echo form_error('ro_amount'); ?></label><br />
							<input type="text" class="text" name="ro_amount" id="ro_amount" value="<?php echo set_value('ro_amount',$ro_amount_details[0]['ro_amount']); ?>" />
							<label>Media Agency Commission Amount:* <?php echo form_error('agency_amount'); ?></label><br />
                                                        <input type="text" class="text" name="agency_amount" id="agency_commission" value="<?php echo set_value('agency_amount',$ro_amount_details[0]['agency_commission_amount']); ?>" onkeyup = "check_amounts();" />
							<input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro_number; ?>">
							<input type="hidden" name="hid_customer_ro" value="<?php echo $customer_ro_number; ?>">
							<input type="hidden" name="hid_url" value="<?php echo $url_string ?>">
						</p>
						<p>
							<input type="submit" class="submitlong" name="ap" value="Add Amount" />
						</p>
					</form>
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			
<script language="javascript">		

function check_amounts() 
{
	var ro_amount = parseFloat(document.getElementById("ro_amount").value);
	var agency_commission = parseFloat(document.getElementById("agency_commission").value);

	if(ro_amount<=agency_commission) {
		alert("Please make sure that the Media Agency Commission Amount is less than the Gross Customer RO amount");
	}	
}

$(function() {

                $(".submitlong").click(function() {
                    var isValid = true;
			var ro_amount = parseFloat(document.getElementById("ro_amount").value);
        var agency_commission = parseFloat(document.getElementById("agency_commission").value);

			 if(agency_commission >= ro_amount)
		        {
				$("#agency_commission").css('background', 'red');
				isValid = false;
			} else 
			{
				$("#agency_commission").css('background', 'green');
			}
			if(isValid === false) {
                        return false;
                    } else {
			//Do everything you need to do with the form
                    }
		});
});
</script>
