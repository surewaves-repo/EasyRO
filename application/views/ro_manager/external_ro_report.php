<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/flexigrid.css">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/flexigrid.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->
	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
			<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>	
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				<input type="hidden" id="profile_id" value="<?php echo $profile_id?>" />
				<?php echo $menu ?>
				
				<p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
			
			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<?php  if($profile_id == 1 or $profile_id == 2 or $profile_id == 7 or $profile_id == 10) { ?>
					<label for="from">From:</label>
					<input type="text" id="from" readonly="readonly" name="from" />
					<label for="to">To:</label>
					<input type="text" id="to" readonly="readonly" name="to" />&nbsp;&nbsp;
                                        <select name="sel_view_type" id="sel_view_type" style="width:130px;">
                                            <option value="1">Campaign Date</option>
                                            <option value="2">RO Date</option>
                                        </select>&nbsp;&nbsp;
                                        <button id="export_csv">Export to CSV</button>
                                        <?php if($logged_in_user['is_test_user'] != 2) { ?>
                                            <button id="invoice_generator">Download Invoice Generator CSV</button>
                                            <input type="radio" name="rd_invoice" value="all" checked="checked" /> All 
                                            <input type="radio" name="rd_invoice" value="completed" /> Completed 
                                            <strong>(This option is only for invoice report)</strong>
                                        <?php } ?>    
                                        <?php } else { ?> 
						<p style="color:red;font-weight:bold;font-size:16px">You do not have permission to access</p>
					<?php } ?>
				</div>
				<div class="block_content">
                                        <table id="flexitable" style="display:none"></table>
                                </div>
			</div>
		</div>
</div>	

<script type="text/javascript">
$(document).ready(function(){

	//get_ro_items();

	$('#go').click(function(){
		refresh_grid($('#ro'));
	});
	
	if($("#profile_id").val() == 1 || $("#profile_id").val() == 2 || $("#profile_id").val() == 7 || $("#profile_id").val() == 10){
	
		$("#flexitable").flexigrid({
				params: get_serialize_date(),
				url: '/surewaves_easy_ro/ro_manager/get_external_report',
				dataType: 'json',
				colModel : [
					{display: 'Customer RO Number', name : 'customer_ro_number', sortable: true, width : 100, align: 'center'},
					{display: 'Internal RO Number', name : 'internal_ro_number', sortable: true, width : 100, align: 'center'},
					{display: 'Client Name', name : 'client_name', sortable: true, width : 100, align: 'center'},
					{display: 'Agency Name', name : 'agency_name', sortable: true, width : 100, align: 'center'},
					{display: 'Start Date', name : 'start_date', sortable: true, width : 100, align: 'center'},
					{display: 'End Date', name : 'end_date', sortable: true, width : 100, align: 'center'},
					{display: 'Gross RO Amount', name: 'gross_ro_amount', sortable: true, width : 100, align: 'center'},
					{display: 'Media Agency Commission', name: 'agency_commission_amount', sortable: true, width : 100, align: 'center'},
					{display: 'Agency Rebate', name : 'agency_rebate', sortable: true, width : 100, align: 'center'},
                                        {display: 'Other Expenses', name : 'other_expenses', sortable: true, width : 100, align: 'center'},
                                        {display: 'Total Seconds Scheduled', name: 'total_seconds_scheduled', sortable: true, width : 100, align: 'center'},
                                        {display: 'Total Network Payout', name: 'total_network_payout', sortable: true, width : 100, align: 'center'},
					{display: 'Net Revenue', name: 'net_revenue', sortable: true, width : 100, align: 'center'},					
					{display: 'Net Contribution Amount', name: 'net_contribution_amount', sortable: true, width : 100, align: 'center'},
					{display: 'Net Contribution Amount(%)', name: 'net_contribution_amount_per', sortable: true, width : 100, align: 'center'},
					{display: 'Total Amount Collected', name: 'total_amount_collected', sortable: true, width : 100, align: 'center'},
					{display: 'TDS', name: 'tds', sortable: true, width : 100, align: 'center'},
					{display: 'Complete Payment', name: 'total_payment_received', sortable: true, width : 100, align: 'center'}
				],
				// buttons : [
				// 	{name: 'Add', bclass: 'add', onpress : test},
				// 	{name: 'Delete', bclass: 'delete', onpress : test},
				// 	{separator: true}
				// ],
				// searchitems : [
				// 	{display: 'Customer RO Number', name : 'customer_ro_number'},
				// 	{display: 'Network Name', name : 'customer_name', isdefault: true}
				// ],
				sortname: "customer_ro_number",
				sortorder: "asc",
				usepager: true,
				title: 'External RO Report',
				useRp: true,
				rp: 15,
				showTableToggleBtn: true,
				width: 'auto',
				height: '400',
				rpOptions: [10,15,20,25,40],
				pagestat: 'Displaying: {from} to {to} of {total} items.',
				blockOpacity: 0.5,
			});
		}	
});
	function get_serialize_date(obj){
		var p = [];
		if(obj != undefined || obj == ''){
			p.push({name: obj.attr('name'),value: obj.val()});
		}
		var df = $('#from');
		var dt = $('#to');
                var report_type = $('#sel_view_type');
		p.push({name: df.attr('name'),value: df.val()});
		p.push({name: dt.attr('name'),value: dt.val()});
                p.push({name: report_type.attr('name'),value: report_type.val()});
		return p;
	}

	function refresh_grid(obj){
		$("#flexitable").flexOptions({params: get_serialize_date(obj)});
		$('#flexitable').flexOptions({url: '/surewaves_easy_ro/ro_manager/get_external_report'});
		$('#flexitable').flexOptions({newp: 1}).flexReload();
		//get_ro_items();
	}

	$("#export_csv").click(function() {
                from_date = $("#from").val();
                to_date = $("#to").val();

                window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/download_export_ro_report_csv/" + from_date+'/'+to_date + '/' + $('#sel_view_type').val();
        });
	$("#invoice_generator").click(function() {
                from_date = $("#from").val();
                to_date = $("#to").val();
                rd_invoice = $("input:radio[name=rd_invoice]:checked").val();
		window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/download_invoice_generator/" + from_date+'/'+to_date+'/'+rd_invoice + '/' + $('#sel_view_type').val();
        });
	function get_ro_items(){
			$.ajax({
				type: 'POST',
				url: '/surewaves_easy_ro/ro_manager/get_external_ros',
				data: {
					from: $('#from').val(),
					to: $('#to').val()
				},
				dataType: 'json',
				success: function(data){
					if(data.row != undefined){
						var items = [];
	 					
	 					items.push('<option value="">ALL</option>');
						$.each(data.row, function(key, val) {
							items.push('<option value="' + key + '">' + val + '</option>');
						});

						$('#ro').empty().append(items.join(''));
					}
				}
			});
	}

$( "#from" ).datepicker({
		defaultDate: "+1w",
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		numberOfMonths: 3,
		onClose: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
		}
	}).datepicker("setDate", "0").change(function(){refresh_grid()});

$( "#to" ).datepicker({
		defaultDate: "+1w",
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		numberOfMonths: 3,
		onClose: function( selectedDate ) {
			$( "#from" ).datepicker( "option", "maxDate", selectedDate );
		}
	}).datepicker("setDate", "0").change(function(){refresh_grid()});
$('#sel_view_type').change(function (){
	refresh_grid();
});        
</script>	
			

