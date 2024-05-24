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

                    <form action="<?php echo ROOT_FOLDER ?>/ro_manager/request_network_remittance_csv/" method="post" enctype="multipart/form-data" style="float: none;">
                        <?php  if($profile_id == 1 or $profile_id == 2 or $profile_id == 7 or $profile_id == 10) { ?>
                            <label>Select Network:</label>
                            <select class="styled" name="network_id" id="network_id" style="width: 160px">
                                <option value="all">All</option>
                                <?php foreach($customer_detail as $c_value) { ?>
                                <option value="<?php echo $c_value['customer_id'] ?> " <?php if(isset($cid) && $c_value['customer_id'] == $cid) echo 'selected' ;?> ><?php echo $c_value['customer_name'] ?></option>
                                <?php } ?>
                            </select>
                            <label for="from">From:</label>
                            <input type="text" readonly="readonly" id="from" name="from" />
                            <label for="to">To:</label>
                            <input type="text" readonly="readonly" id="to" name="to" />&nbsp;&nbsp;&nbsp;

                            <input type="button" id="go" value="Get Report"/>
                            <!--<button id="export_csv">Export to CSV</button>-->

                            <label for="month">Mail Id:</label>
                            <input type="text" id="mail_ids" name="mail_ids" value="">

                            <input type="submit" id="request_report" value="Request Report" onclick="return check_form()">

                            <input type="checkbox" name="chk_fully_paid" id="chk_fully_paid" />
                            <span style="font-size: 11px">Not Fully Paid</span>
                            <input type="hidden" name="hid_fully_paid" id="hid_fully_paid" />
                        <?php } else { ?>
                            <p style="color:red;font-weight:bold;font-size:12px">You do not have permission to access</p>
                        <?php } ?>
                    </form>


				</div>
				<div class="block_content">
                                        <table id="flexitable"></table>
                                </div>
			</div>
		</div>
</div>	

<script type="text/javascript">
	$('#go').click(function(){
		refresh_grid();
		
	if($("#profile_id").val() == 1 || $("#profile_id").val() == 2  || $("#profile_id").val() == 7 || $("#profile_id").val() == 10){
	$("#flexitable").flexigrid({
				params: get_serialize_value(),
				url: '/surewaves_easy_ro/ro_manager/get_network_remittance_report_v1',
				dataType: 'json',
				colModel : [
					{display: 'Network Name', name : 'network_name', sortable: true, width : 110, align: 'center'},
                                        {display: 'Network Ro Fully Paid', name: 'nw_ro_fully_paid', sortable: true, width : 90, align: 'center'},
                                        {display: 'Full Payment Received', name: 'full_payment_received', sortable: true, width : 70, align: 'center'},
                                        {display: 'Client Name', name : 'client_name', sortable: true, width : 65, align: 'center'},
                                        {display: 'Network RO Number', name : 'network_ro_number', sortable: true, width : 110, align: 'center'},
                                        {display: 'Network Ro Release Date', name: 'nw_ro_release_date', sortable: true, width : 90, align: 'center'},
                                        {display: 'Network Ro Paid Amount', name: 'nw_ro_paid_amount', sortable: true, width : 90, align: 'center'},
                                        {display: 'Surewaves Share Amount', name: 'swaves_share_amount', sortable: true, width : 90, align: 'center'},                                        
					{display: 'Network Billing Name', name : 'network_billing_name', sortable: true, width : 65, align: 'center'},					
					{display: 'External RO Number', name : 'external_ro_number', sortable: true, width : 65, align: 'center'},					
                                        {display: 'Agency Name', name : 'agency_name', sortable: true, width : 65, align: 'center'},
					{display: 'Activity Start Date', name : 'activity_start_date', sortable: true, width : 65, align: 'center'},
					{display: 'Activity End Date', name : 'activity_end_date', sortable: true, width : 40, align: 'center'},
		{display: 'Report Start Date', name : 'report_start_date', sortable: true, width : 65, align: 'center'},
					{display: 'Report End Date', name : 'report_end_date', sortable: true, width : 40, align: 'center'},
										{display: 'Activity spot seconds', name : 'activity_spot_seconds', sortable: true, width : 50, align: 'center'},
										{display: 'Activity spot amount', name : 'activity_spot_amount', sortable: true, width : 50, align: 'center'},
										{display: 'Activity banner seconds', name : 'activity_banner_seconds', sortable: true, width : 50, align: 'center'},
										{display: 'Activity banner amount', name : 'activity_banner_amount', sortable: true, width : 50, align: 'center'},
										{display: 'Scheduled spot seconds', name : 'scheduled_spot_seconds', sortable: true, width : 50, align: 'center'},
										{display: 'Scheduled spot amount', name : 'scheduled_spot_amount', sortable: true, width : 50, align: 'center'},
										{display: 'Scheduled banner seconds', name : 'scheduled_banner_seconds', sortable: true, width : 50, align: 'center'},
										{display: 'Scheduled banner amount', name : 'scheduled_banner_amount', sortable: true, width : 50, align: 'center'},
										{display: 'Total scheduled spot seconds', name : 'total_scheduled_spot_seconds', sortable: true, width : 50, align: 'center'},
										{display: 'Total scheduled spot amount', name : 'total_scheduled_spot_amount', sortable: true, width : 50, align: 'center'},
										{display: 'Total scheduled banner seconds', name : 'total_scheduled_banner_seconds', sortable: true, width : 50, align: 'center'},
										{display: 'Total scheduled banner amount', name : 'total_scheduled_banner_amount', sortable: true, width : 50, align: 'center'},
					{display: 'Payment Collected Amount', name: 'amount_collected', sortable: true, width : 110, align: 'center'},					
					{display: 'External RO Amount', name: 'external_ro_amount', sortable: true, width : 90, align: 'center'},
					{display: 'Network RO Amount', name: 'network_ro_amount', sortable: true, width : 90, align: 'center'},					
					{display: 'Service Tax', name: 'service_tax', sortable: true, width : 90, align: 'center'},				
                                        {display: 'Cancelled', name: 'cancel_date', sortable: true, width : 90, align: 'center'}
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
		}	
});
	function get_serialize_value(obj){
		var p = [];
		
		var nw = $('#network_id'); 
		var df = $('#from');
		var dt = $('#to');
		var fully_paid = $('#hid_fully_paid');
		
		p.push({name: nw.attr('name'),value: nw.val()});
		p.push({name: df.attr('name'),value: df.val()});
		p.push({name: dt.attr('name'),value: dt.val()});
		p.push({name: fully_paid.attr('name'),value: fully_paid.val()});
		return p;
	}

	function refresh_grid(obj){
		$("#flexitable").flexOptions({params: get_serialize_value(obj)});
		//$('#flexitable').flexOptions({url: '/surewaves_easy_ro/ro_manager/get_network_remittance_report'});
		$('#flexitable').flexOptions({newp: 1}).flexReload();		
	}

    function check_form(){
        var from_date = $("#from").val();
        var to_date = $("#to").val();
        var mail_ids = $("#mail_ids").val();

        if(mail_ids == ""){
            alert("Please enter Email Id");
            $("#mail_ids").focus();
            return false;
        }else if(from_date == ""){
            alert("Please select start date");
            $("#from_date").focus();
            return false;
        }else if(to_date == ""){
            alert("Please select end date");
            $("#to_date").focus();
            return false;
        }else{
            alert("Network Remittance Report will be sent to:"+mail_ids);
            return true;
        }
    }

	$("#export_csv").click(function() {
		network_id = $("#network_id").val();
		from_date = $("#from").val();
		to_date = $("#to").val();
                fully_paid = $("#hid_fully_paid").val();
		
		window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/download_network_remittance_csv/" + network_id +'/'+from_date +'/'+ to_date +'/'+ fully_paid;
	});
	
	$( "#network_id" ).change(function() {
	  //refresh_grid() ;
	});
	
	$( "#from" ).datepicker({
		defaultDate: "+1w",
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		numberOfMonths: 3,
		onClose: function( selectedDate, instance ) {
            date = $.datepicker.parseDate(instance.settings.dateFormat, selectedDate, instance.settings);
            date.setMonth(date.getMonth() + 2);
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
            $( "#to" ).datepicker( "option", "maxDate", date );
		}
	}).datepicker("setDate", "0");

	$( "#to" ).datepicker({
		defaultDate: "+1w",
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		numberOfMonths: 3,
		onClose: function( selectedDate , instance) {
            date = $.datepicker.parseDate(instance.settings.dateFormat, selectedDate, instance.settings);
            date.setMonth(date.getMonth() - 2);
			$( "#from" ).datepicker( "option", "maxDate", selectedDate );
            $( "#from" ).datepicker( "option", "minDate", date );
		}
	}).datepicker("setDate", "0");
// added by lokanath
$('#chk_fully_paid').click(function (){
	if($('#chk_fully_paid').attr('checked')){
		$('#hid_fully_paid').val("1");
		//refresh_grid();
	}
	else{
		$('#hid_fully_paid').val("0");
		//refresh_grid();
	}
});
// end
</script>
