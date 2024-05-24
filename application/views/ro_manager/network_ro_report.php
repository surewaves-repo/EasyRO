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
					<input type="text" id="to" readonly="readonly" name="to" />&nbsp;&nbsp;&nbsp;
					<label for"selectro">Select RO:</label>
					<?php echo form_dropdown('ro_number',array('all' => ''), '', 'id="ro"'); ?>&nbsp;&nbsp;&nbsp;
					<button id="go">Get Report</button>&nbsp;&nbsp;&nbsp;
					<button id="export_csv">Export to CSV</button>
					<?php } else { ?> 
						<p style="color:red;font-weight:bold;font-size:16px">You do not have permission to access</p>
					<?php } ?>
					<label for="month">Mail Id:</label>
					<input type="text" value="" name="mail_ids" id="mail_ids">
					<input type="button" value="Request Report" id="chk_mail_report" name="chk_mail_report">
                    <!--<input type="checkbox" name="chk_mail_report" id="chk_mail_report" />
                    <span style="font-size: 11px">Mail Report</span>
                    -->
					<input type="hidden" name="hid_mail_report" id="hid_mail_report" />
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

	if($("#profile_id").val() == 1 || $("#profile_id").val() == 2 || $("#profile_id").val() == 7 || $("#profile_id").val() == 10){
	$("#flexitable").flexigrid({
				params: get_serialize_date(),
				url: '/surewaves_easy_ro/ro_manager/get_record',
				dataType: 'json',
				colModel : [
					{display: 'Customer RO Number', name : 'customer_ro_number', sortable: true, width : 110, align: 'center'},
					{display: 'Network RO Number', name : 'network_ro_number', sortable: true, width : 110, align: 'center'},
					{display: 'Network Name', name : 'customer_name', sortable: true, width : 65, align: 'center'},
					{display: 'Advertiser Client Name', name : 'client_name', sortable: true, width : 65, align: 'center'},
					{display: 'Agency Name', name : 'agency_name', sortable: true, width : 65, align: 'center'},
					{display: 'Market', name : 'market', sortable: true, width : 40, align: 'center'},
					{display: 'Start Date', name : 'start_date', sortable: true, width : 50, align: 'center'},
					{display: 'End Date', name : 'end_date', sortable: true, width : 50, align: 'center'},
					{display: 'Activity Months', name: 'activity_months', sortable: true, width : 65, align: 'center'},
					{display: 'Gross Network RO Amount', name: 'gross_network_ro_amount', sortable: true, width : 110, align: 'center'},
					{display: 'Network Share', name: 'customer_share', sortable: true, width : 70, align: 'center'},
					{display: 'Net Amount Payable', name: 'net_amount_payable', sortable: true, width : 90, align: 'center'},
					{display: 'Release Date', name: 'release_date', sortable: true, width : 70, align: 'center'},
					{display: 'Billing Name', name: 'billing_name', sortable: true, width : 70, align: 'center'}
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
				title: 'Network RO Report',
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
		p.push({name: df.attr('name'),value: df.val()});
		p.push({name: dt.attr('name'),value: dt.val()});
		return p;
	}

	function refresh_grid(obj){
		$("#flexitable").flexOptions({params: get_serialize_date(obj)});
		$('#flexitable').flexOptions({url: '/surewaves_easy_ro/ro_manager/get_record'});
		$('#flexitable').flexOptions({newp: 1}).flexReload();
		get_ro_items();
	}

	$("#export_csv").click(function() {
		from_date = $("#from").val();
		to_date = $("#to").val();
		
		ro_name = escape($("#ro").val());
		ro_name = ro_name.replace (/\//g, "~");			
		window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/download_csv/" + from_date+'/'+to_date+'/'+ro_name;
	});
	function get_ro_items(){
			if($('#to').val() <= $('#from').val()){
				$('#to').val($('#from').val());
			}
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

$("#chk_mail_report").click(function(){
	if(($('#mail_ids').val()).trim() == "" || ($('#mail_ids').val()).trim() === null){
		alert("Please provide Mail Id(s)");
		$('#mail_ids').focus();
		return false;
	}
	var mail_idArr = (($('#mail_ids').val()).trim()).split(",");
	var properEmail = true;
	for(var arrcount = 0 ;arrcount < mail_idArr.length;arrcount++){
		properEmail =  Utility.validateEmail(mail_idArr[arrcount]);
		break;
	}
	if(!properEmail){
		alert("Please provide proper mail Id(s)");
		$('#mail_ids').focus();
		return false;
	}
    $.ajax({
        type: 'POST',
        url: '/surewaves_easy_ro/ro_manager/mail_network_ro_report',
        data: {
            from: $('#from').val(),
            to: $('#to').val(),
            ro: $('#ro').val(),
			maild_ids : ($('#mail_ids').val()).trim()
        },
        dataType: 'json',
        success: function(data){
            //if(data.row != undefined){
              //  alert("Mail Requested");
           // }
		alert(data.msg);
        }
    });
});

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
			
