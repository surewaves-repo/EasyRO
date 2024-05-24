<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>SureWaves Easy RO</title>

    <style type="text/css" media="all">
		@import url("/surewaves_easy_ro/css/style.css");
		@import url("/surewaves_easy_ro/css/jquery.wysiwyg.css");
		@import url("/surewaves_easy_ro/css/facebox.css");
		@import url("/surewaves_easy_ro/css/visualize.css");
		@import url("/surewaves_easy_ro/css/colorbox.css");
    </style>

	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=7" /><![endif]-->
	<!--[if lt IE 8]><style type="text/css" media="all">@import url("/surewaves_easy_ro/css/ie.css");</style><![endif]-->
	<!--[if IE]><script type="text/javascript" src="/surewaves_easy_ro/js/excanvas.js"></script><![endif]-->

		<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.img.preload.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.filestyle.mini.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.wysiwyg.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.date_input.pack.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/facebox.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.visualize.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.select_skin.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/ajaxupload.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.pngfix.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/custom.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/Utils.js"></script>

	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.colorbox-min.js"></script>
	<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.colorbox.js"></script>
	<link rel="icon" type="image/png" href="/surewaves_easy_ro/images/SWV_Logo_Watermark.ico">
</head>




<body>
 

	<script type="text/javascript" src="/surewaves_easy_ro/js/datetimepicker.js"></script>
	
			
			<div class="block small center login" style="margin:0px">				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Add Other Expenses Amounts</h2>					
				</div>	
					  				<div class="block_content">							
					<form action="<?php echo ROOT_FOLDER ?>/non_fct_ro/updateNonFctOtherExpense" method="post" id="myform">
						<p>
							<label>Agency Rebate Amount(%):* </label><br />
							<input type="text" class="text" name="agency_rebate" id="agency_rebate"  value="0"  onkeyup =""  style="width:250px;"/>
                            <select name="sel_type" id="sel_type" style="width:155px; height:32px; vertical-align:5px;">
                            	<option value="ro_amount" selected>RO Amount</option>
                                <option value="net_amount" >Net Amount</option>
                            </select>							
							<label>Marketing Promotion Amount:* </label><br />
                                                        <input type="text" class="text" name="marketing_promotion_amount" id ="markt_promotion" value="0"  onkeyup =""/>
							 <label>Field Activation and Research Support Amount:* </label><br />
 							<input type="text" class="text" name="field_activation_amount" id="field_activation" value="0"  onkeyup =""/>
							 <label>Sales Commissions Amount:* </label><br />
			 				<input type="text" class="text" name="sales_commissions_amount" id="sales_commission" value="0"  onkeyup =""/>
							 <label>Creative Services Amount:* </label><br />
 							<input type="text" class="text" name="creative_services_amount" id="creative_services" value="0"  onkeyup ="" />
							 <label>Other Expenses Amount:* </label><br />
 							 <input type="text" class="text" name="other_expenses_amount" id ="other_expenses" value="0" onkeyup ="" />

							<input type="hidden" name="hid_ro_valid" id="ro_valid_field" value="0" />
							<input type ="hidden" name="hid_ro_amount" id="ro_amount" value="6000">
							<input type ="hidden" name="hid_agency_commission" id="agency_commission" value="0">
							<input type="hidden" name="hid_internal_ro" value="">
							<input type="hidden" name="hid_customer_ro" value="TestROND">
							<input type="hidden" name="hid_url" value="">
                            
                            <input type="hidden" name="hid_edit" value="1">
                            <input type="hidden" name="hid_id" value="1846">
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

</script>	

