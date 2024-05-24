<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?> 
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
			
			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Edit Network Details</h2>					
				</div>	
				
				<div class="block_content">
				<form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_edit_network" method="post" id="myForm">
					<table cellpadding="0" cellspacing="0" width="100%">
                        <?php 
						// count no. of rows
						$total_rows = 0;
						foreach($get_all_channel_info as $channel_values){ 
							if($channel_values['total_spot_ad_seconds'] != 0){
								$total_rows++;
							}
							if($channel_values['total_banner_ad_seconds'] != 0){
								$total_rows++;
							}
						}
						// end
						
						$nw_ro_amount = 0;
						$row_id = 0; 
						
						?>	
                        
                    	<?php foreach($get_all_channel_info as $channel_values){ ?>
                        <tr>
							<td colspan="4"><strong>Channel Name : <?PHP echo $channel_values['channel_name'];?></strong></td>
                        </tr>
                        <tr>
							<td ><strong>Ad Type</strong></td>
                            <td ><strong>Total Ad Seconds</strong></td>
                            <td ><strong>Historical Avg Rate/10Sec</strong></td>
                            <td ><strong>Amount</strong></td>
                        </tr>
                        <?php if($channel_values['total_spot_ad_seconds'] != 0){ ?>
                            <tr>
                                <td >Spot Ad</td>
                                <td id="<?php echo network_0_total_sec_.$row_id.'_0'; ?>" ><?php echo round($channel_values['total_spot_ad_seconds'],2); ?></td>
                                <td ><input type="text" name="post_channel_avg_rate[<?php echo $channel_values['tv_channel_id']; ?>][1]" id="<?php echo network_0_channel_.$row_id.'_1'; ?>" value="<?php echo $channel_values['channel_spot_avg_rate']; ?>" onchange="find_price('<?php echo $row_id; ?>','<?php echo $total_rows; ?>',0)"/></td>
                                <td >
                                <input type="text" name="post_channel_amount[<?php echo $channel_values['tv_channel_id']; ?>][1]" id="<?php echo network_0_amount_.$row_id.'_2'; ?>" onchange = "find_rate('<?php echo $row_id ?>','<?php echo $total_rows; ?>',0);"  value="<?php echo round($channel_values['channel_spot_amount'],2); ?>"/>
                                </td>
                            </tr>
                            <input type="hidden" name="post_channel_total_add_sec[<?php echo $channel_values['tv_channel_id']; ?>][1]" value="<?php echo round($channel_values['total_spot_ad_seconds'],2); ?>" />
						<?php $nw_ro_amount = $nw_ro_amount + $channel_values['channel_spot_amount']; ?>
                        <?php $row_id++;} ?>
                       	<?php if($channel_values['total_banner_ad_seconds'] != 0){ ?>
                                <tr>
                                    <td >Banner Ad</td>
                                    <td id="<?php echo network_0_total_sec_.$row_id.'_0'; ?>" ><?php echo $channel_values['total_banner_ad_seconds']; ?></td>
                                    <td ><input type="text" value="<?php echo $channel_values['channel_banner_avg_rate']; ?>" name="post_channel_avg_rate[<?php echo $channel_values['tv_channel_id']; ?>][3]" id="<?php echo network_0_channel_.$row_id.'_1'; ?>" onchange="find_price('<?php echo $row_id; ?>','<?php echo $total_rows; ?>',0)" /></td>
                                    <td >
                                    <input type="text" value="<?php echo $channel_values['channel_banner_amount']; ?>" name="post_channel_amount[<?php echo $channel_values['tv_channel_id']; ?>][3]" id="<?php echo network_0_amount_.$row_id.'_2'; ?>" onchange = "find_rate('<?php echo $row_id ?>','<?php echo $total_rows; ?>',0);" />
                                    </td>
                                </tr>
                                <input type="hidden" name="post_channel_total_add_sec[<?php echo $channel_values['tv_channel_id']; ?>][3]" value="<?php echo round($channel_values['total_banner_ad_seconds'],2); ?>" />
						<?php $nw_ro_amount = $nw_ro_amount + $channel_values['channel_banner_amount']; ?>
                        <?php $row_id++;} ?>		
						<?php } ?>
                        <tr>
							<td >Network RO Amount</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td id="<?php echo network_0_total_amount; ?>" ><?php echo round($nw_ro_amount,2); ?></td>
                        </tr>
                        <tr>
							<td >Network Share(%)</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td ><input type="text" value="<?php echo $channel_values['customer_share']; ?>" name="post_network_share[0]" id="<?php echo network_0_network_share; ?>" onchange ="find_final_amount(0,1);"/></td>
                        </tr>
                        <tr>
							<td >Network Payout(Net)</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >
                            <?php $nw_payout = $nw_ro_amount * ($channel_values['customer_share']/100);?>
                            <input type="text" value="<?php echo round($nw_payout); ?>" name="post_final_amount[0]" id="<?php echo network_0_final_amount; ?>" onchange ="find_channel_amounts(0,<?php echo $total_rows; ?>);" /></td>
                        </tr>
						
						<tr>	
							<td> 
                            <input type="hidden" name="hid_id" id="hid_id" value="<?php echo $am_ro_id ?>" />
                            <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
                            <input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro_number ?>">
                            <input type="hidden" name="hid_customer_id" value="<?php echo $customer_id ?>">
                           <!-- <input type="hidden" name="hid_user_id" id="hid_user_id" value="<?php echo $logged_in_user['user_id'] ?>" />
                            <input type="hidden" name="hid_ext_ro" id="hid_ext_ro" value="<?php echo $external_ro ?>" />
                            
                            -->
                            <input type="submit" class="submit" value="Submit" onclick="return check_form();" />	
                            </td>						
						<tr>
					
					</table>
						
						
				</form>
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			
		
	<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>	
<script type="text/javascript" language="javascript">

function check_form(){
	var isValid = true;
	$("#myForm input[type=text]").each(function() {

		if(isNaN(this.value) || (this.value < 0)|| (this.value ==='')) {
			$(this).css('background', 'red');
			isValid = false;
		} else {
			$(this).css('background', 'green');
		}
	});
	if(isValid === false) {
		return false;
	}
}
function find_price(row_id,total_rows,network_id) {

//	alert(total_rows);
	var total_amount = 0;
	for(var i = 0;i<total_rows;i++)
	{
//		alert(i);
	//	if(i == row_id) {
		var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
		var total_ad_seconds = document.getElementById(total_ad_sec_id).innerHTML;
		var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
		var temp_channel_avg_rate = document.getElementById(channel_avg_rate_id).value;
		/* for bug: 426 code change starts here */
		if(temp_channel_avg_rate === "" || isNaN(temp_channel_avg_rate) || temp_channel_avg_rate < 0)
		{
			alert(" Please enter a positive numerical value for channel Historical Avg Rate/10Sec");
		/* for bug: 426 code change done here*/
		} else 
		{
		var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
		var temp_amount = temp_channel_avg_rate * total_ad_seconds/10;
                document.getElementById(amount_id).value = temp_amount.toFixed(2);
                total_amount += parseFloat(temp_amount.toFixed(2),10);
		}
	}
	var total_amount_id = 'network_'+network_id+'_total_amount';
	document.getElementById(total_amount_id).innerHTML = total_amount.toFixed(2);
	var network_share_id = 'network_'+network_id+'_network_share';
	var network_share = document.getElementById(network_share_id).value;
	var final_amount_id = 'network_'+network_id+'_final_amount';
	var final_amount = total_amount.toFixed(2) * network_share/100;
	document.getElementById(final_amount_id).value = final_amount.toFixed(2);
}
function find_rate(row_id,total_rows,network_id) {
	var total_amount = 0;
	for(var i = 0;i<total_rows;i++) 
	{
			if(i == row_id) {
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
			var amount = document.getElementById(amount_id).value;
			if(amount === "" || isNaN(amount) || amount < 0)
     		{       
				alert("Please enter a positive numerical value for channel Amount");
			}else {
			var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
                //      alert(total_ad_sec_id);
                        var total_ad_seconds = document.getElementById(total_ad_sec_id).innerHTML;
			var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
//                      alert(channel_avg_rate_id);
                        var channel_avg_rate = amount/total_ad_seconds * 10;
			document.getElementById(channel_avg_rate_id).value = channel_avg_rate.toFixed(2);
			total_amount += parseFloat(amount,10);
			} }  else { 
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
                        var amount = document.getElementById(amount_id).value;
			total_amount += parseFloat(amount,10);
			}
	}
	 var total_amount_id = 'network_'+network_id+'_total_amount';
        document.getElementById(total_amount_id).innerHTML = total_amount.toFixed(2);
        var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = document.getElementById(network_share_id).value;
        var final_amount_id = 'network_'+network_id+'_final_amount';
        var final_amount = total_amount.toFixed(2) * network_share/100;
        document.getElementById(final_amount_id).value = final_amount.toFixed(2);
}
function find_final_amount(network_id,total_networks) {
	
	var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = document.getElementById(network_share_id).value;
//	alert(network_share);
	if(network_share==="" || isNaN(network_share) || network_share >100 || network_share < 0)
	{       
		alert("Please enter Network share greater than or equal to 0 and less than or equal to 100");
	} else {
	var total_amount_id = 'network_'+network_id+'_total_amount';
	var total_amount =  document.getElementById(total_amount_id).innerHTML;
//	alert(total_amount);
	var final_amount_id = 'network_'+network_id+'_final_amount';
        var final_amount = total_amount * network_share/100;
        document.getElementById(final_amount_id).value = final_amount.toFixed(2);
	}
}
function find_channel_amounts(network_id,total_rows){

	var final_amount_id = 'network_'+network_id+'_final_amount';	
 	var final_amount = parseFloat(document.getElementById(final_amount_id).value);
	var total_amount_id = 'network_'+network_id+'_total_amount';
        var total_amount_old = parseFloat(document.getElementById(total_amount_id).innerHTML);
        var network_share_id = 'network_'+network_id+'_network_share';
        var network_share = parseFloat(document.getElementById(network_share_id).value);
	/*------------------- For Bug 427 to make sure values not struck at character values -----------------*/
	if(final_amount==="" || isNaN(final_amount) || final_amount < 0)
       	{       
		alert("Please enter a positive numerical value for Network Payout(Net)");
	/* for bug: 427 code changes done here */
	} else {
		var total_amount_new = final_amount * 100/network_share;
		document.getElementById(total_amount_id).innerHTML = total_amount_new.toFixed(2);
		for(var i = 0;i<total_rows;i++)
		{
			var total_amount_id = 'network_'+network_id+'_total_amount';
		        var total_amount =  parseFloat(document.getElementById(total_amount_id).innerHTML);
			var amount_id = 'network_'+network_id+'_amount_'+i+'_2';
                        var amount_old = parseFloat(document.getElementById(amount_id).value);
			var amount_new = total_amount * amount_old/total_amount_old;
			//alert('total amount:'+total_amount+'channel amount old:'+amount_old+'tot amount old:'+total_amount_old+'amount new:'+amount_new);
			document.getElementById(amount_id).value = amount_new.toFixed(2);
			var total_ad_sec_id = 'network_'+network_id+'_total_sec_'+i+'_0';
                //      alert(total_ad_sec_id);
                        var total_ad_seconds = parseFloat(document.getElementById(total_ad_sec_id).innerHTML);
			var channel_avg_rate_id = 'network_'+network_id+'_channel_'+i+'_1';
//                      alert(channel_avg_rate_id);
			var amount = amount_new.toFixed(2);
                        var channel_avg_rate = amount/total_ad_seconds * 10;
                        document.getElementById(channel_avg_rate_id).value = channel_avg_rate.toFixed(2);
		}
	}
}
</script>