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
				url: '/surewaves_easy_ro/report/getMisNonFctReport',
				dataType: 'json',
				colModel : [
					{display: 'Customer RO Number', name : 'customer_ro_number', sortable: true, width : 200, align: 'center'},
                                        {display: 'Inernal RO Number', name : 'internal_ro_number', sortable: true, width : 250, align: 'center'},
					{display: 'Agency', name : 'agency', sortable: true, width : 100, align: 'center'},
                                        {display: 'Agency Contact', name : 'agency_contact', sortable: true, width : 90, align: 'center'},
					{display: 'Advertiser Client Name', name : 'client', sortable: true, width : 120, align: 'center'},
					{display: 'Month Name', name : 'month', sortable: true, width : 85, align: 'center'},
					{display: 'Amount', name : 'price', sortable: true, width : 80, align: 'center'},
					{display: 'Agency Commision', name : 'agency_commision', sortable: true, width : 100, align: 'center'}
				],
				sortname: "customer_ro_number",
				sortorder: "asc",
				usepager: true,
				title: 'Mis Non Fct RO Report',
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
		$('#flexitable').flexOptions({url: '/surewaves_easy_ro/report/getMisNonFctReport'});
		$('#flexitable').flexOptions({newp: 1}).flexReload();
	}

	$("#export_csv").click(function() {
		//financial_year = $("#financial_year").val();
		var month_year = $("#month_name").val().split(" ");
		month_name = month_year[0] ;
                financialYear = month_year[1] ;
                
                window.location.href = "<?php echo ROOT_FOLDER ?>/report/download_mis_non_fct_report/" + month_name+'/'+financialYear;		
	});
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