<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/flexigrid.css">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/flexigrid.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<style>
.ui-datepicker-calendar {
    display: none;
    }
</style>
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
					<!-- <label for="financial_year">Financial Year:</label>
					<input type="text" id="financial_year" readonly="readonly" name="financial_year" /> -->
					<label for="month_name">Activity Month:</label>
					<input type="text" id="month_name" readonly="readonly" name="month_name" />&nbsp;&nbsp;&nbsp;
					
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

    $('#go').click(function(){
            refresh_grid($('#ro'));
    });

	if($("#profile_id").val() == 1 || $("#profile_id").val() == 2 || $("#profile_id").val() == 7 || $("#profile_id").val() == 10){
	$("#flexitable").flexigrid({
				params: get_serialize_date(),
				url: '/surewaves_easy_ro/report/getMisReport',
				dataType: 'json',
				colModel : [
					{display: 'Customer RO Number', name : 'customer_ro_number', sortable: true, width : 110, align: 'center'},
                                        {display: 'Inernal RO Number', name : 'internal_ro_number', sortable: true, width : 110, align: 'center'},
					{display: 'Network RO Number', name : 'network_ro_number', sortable: true, width : 110, align: 'center'},
					{display: 'Network Name', name : 'customer_name', sortable: true, width : 65, align: 'center'},
                                        {display: 'Network id', name : 'customer_id', sortable: true, width : 65, align: 'center'},
					{display: 'Advertiser Client Name', name : 'client', sortable: true, width : 65, align: 'center'},
					{display: 'Agency Name', name : 'agency_name', sortable: true, width : 65, align: 'center'},
					{display: 'Market', name : 'market', sortable: true, width : 40, align: 'center'},
					{display: 'Start Date', name : 'start_date', sortable: true, width : 50, align: 'center'},
					{display: 'End Date', name : 'end_date', sortable: true, width : 50, align: 'center'},
					{display: 'Activity Months', name: 'month_name', sortable: true, width : 65, align: 'center'},
					{display: 'Network Share', name: 'customer_share', sortable: true, width : 70, align: 'center'},
					{display: 'Net Amount Payable', name: 'channel_payout', sortable: true, width : 90, align: 'center'},
                                        {display: 'Channel ID', name: 'channel_id', sortable: true, width : 90, align: 'center'},
                                        {display: 'Spot Amount', name: 'spot_amount', sortable: true, width : 90, align: 'center'},
                                        {display: 'Banner Amount', name: 'banner_amount', sortable: true, width : 90, align: 'center'},
                                        {display: 'Spot Fct', name: 'scheduled_spot_impression', sortable: true, width : 90, align: 'center'},
                                        {display: 'Banner Fct', name: 'scheduled_banner_impression', sortable: true, width : 90, align: 'center'},
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
				title: 'Mis RO Report',
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
		//var df = $('#financial_year');
		var dt = $('#month_name');
		//p.push({name: df.attr('name'),value: df.val()});
		p.push({name: dt.attr('name'),value: dt.val()});
		return p;
	}

	function refresh_grid(obj){
		$("#flexitable").flexOptions({params: get_serialize_date(obj)});
		$('#flexitable').flexOptions({url: '/surewaves_easy_ro/report/getMisReport'});
		$('#flexitable').flexOptions({newp: 1}).flexReload();
	}

	$("#export_csv").click(function() {
		//financial_year = $("#financial_year").val();
		var month_year = $("#month_name").val().split(" ");
		month_name = month_year[0] ;
                financialYear = month_year[1] ;
                
                window.location.href = "<?php echo ROOT_FOLDER ?>/report/download_mis_report/" + month_name+'/'+financialYear;		
	});
        
/*$( "#financial_year" ).datepicker({
		changeYear:true,
                yearRange: "2014:2025"		
	}).datepicker("setDate", "0").change(function(){refresh_grid()}); */

$( "#month_name" ).datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM yy',
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1)).change(function(){refresh_grid()});
        }
	})
</script>	
			
