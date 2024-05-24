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
				
				<ul id="nav">
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/home">Home</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/approved_ros">Approved RO's</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user_details">User Details</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_ro_report">Reports</a>
						<ul id="nav">
							<li class="active" ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/external_ro_report">External RO Report</a></li>
							<li class="active" ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_ro_report">Network RO Report</a></li>
						</ul>
					</li>
					<li class="active" ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/network_remittance">Network Remittance</a>						
					</li>
				</ul>
				
				<p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
			
			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<?php  if($profile_id == 1 or $profile_id == 2) { ?>
					<label for="from">From:</label>
					<input type="text" id="from" name="from" />
					<label for="to">To:</label>
					<input type="text" id="to" name="to" />&nbsp;&nbsp;&nbsp;
					<label for"selectro">Select RO:</label>
					<?php echo form_dropdown('ro_number',array('all' => ''), '', 'id="ro"'); ?>&nbsp;&nbsp;&nbsp;
					<button id="go">Get Report</button>&nbsp;&nbsp;&nbsp;
					<button id="export_csv">Export to CSV</button>
					<?php } else { ?> 
						<p style="color:red;font-weight:bold;font-size:16px">You do not have permission to access</p>
					<?php } ?>
				</div>
				<div class="block_content">
                                        <table id="flexitable"></table>
                                </div>
			</div>
		</div>
</div>	

<script type="text/javascript">
$(document).ready(function(){

	get_ro_items();

	$('#go').click(function(){
		refresh_grid($('#ro'));
	});

	
	$("#flexitable").flexigrid({
				params: get_serialize_date(),
				url: '/surewaves_easy_ro/ro_manager/get_nw_remittance_report',
				dataType: 'json',
				colModel : [
					{display: 'Customer RO Number', name : 'customer_ro_number', sortable: true, width : 110, align: 'center'},
					{display: 'Cheque Number', name : 'cheque_number', sortable: true, width : 110, align: 'center'},
					{display: 'Collection Date', name : 'collection_date', sortable: true, width : 65, align: 'center'},
					{display: 'Payment Summary', name : 'payment_summary', sortable: true, width : 65, align: 'center'},
					{display: 'Total Amount Collected', name : 'total_amount_collected', sortable: true, width : 65, align: 'center'},
					{display: 'Service Tax', name : 'service_tax', sortable: true, width : 40, align: 'center'}				
				],				
				sortname: "customer_ro_number",
				sortorder: "asc",
				usepager: true,
				title: 'Network Remittance Report',
				useRp: true,
				rp: 15,
				showTableToggleBtn: true,
				width: 'auto',
				height: '400',
				rpOptions: [10,15,20,25,40],
				pagestat: 'Displaying: {from} to {to} of {total} items.',
				blockOpacity: 0.5,
			}); 
			
});
	function get_serialize_date(obj){
		var p = [];
		if(obj != undefined || obj == ''){
			p.push({name: obj.attr('name'),value: obj.val()});
		}
		var df = $('#from');
		var dt = $('#to');
		p.push({name: df.attr('name'),value: df.val()});
		p.push({name: dt.attr('name'),value: dt.val()});
		return p;
	}

	function refresh_grid(obj){
		$("#flexitable").flexOptions({params: get_serialize_date(obj)});
		$('#flexitable').flexOptions({url: '/surewaves_easy_ro/ro_manager/get_nw_remittance_report'});
		$('#flexitable').flexOptions({newp: 1}).flexReload();
		get_ro_items();
	}

	$("#export_csv").click(function() {
		from_date = $("#from").val();
		to_date = $("#to").val();
		
		ro_name = escape($("#ro").val());
		ro_name = ro_name.replace (/\//g, "~");			
		window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/download_csv_nw/" + from_date+'/'+to_date+'/'+ro_name;
	});
	function get_ro_items(){
			$.ajax({
				type: 'POST',
				url: '/surewaves_easy_ro/ro_manager/get_ros',
				data: {
					from: $('#from').val(),
					to: $('#to').val()
				},
				dataType: 'json',
				success: function(data){
					if(data.row != undefined){
						var items = [];
						items.push('<option value="all"></option>');
						
	 					var roVal = $('#ro').val();	 					
						$.each(data.row, function(key, val) {
							if(roVal == val){
								items.push('<option value="' + key + '" selected="selected">' + val + '</option>');
							}else{
								items.push('<option value="' + key + '">' + val + '</option>');
							} 
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
</script>	
			
