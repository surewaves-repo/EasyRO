<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/flexigrid.css">
<style>
.block table tr td, .block table tr th { padding:2px; }
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
					<?php  if($profile_id == 3) { ?>
					<div id="SearchFilter" style="float:left;">
						<label for="from">Search by:</label>
						<select name="SearchType" id="SearchType" style="width: 150px;">
							<option value="category_name">Category</option>
							<option value="product_group">Product</option>
							<option value="advertiser">Advertiser</option>
							<option value="brand">Brand</option>
						</select>
						<label for="from">Search text:</label>
						<input type="text" id="search" value="" name="Search"  style="width: 150px;" />
						<input type="button" id="searchbtn" value="Search" />
					</div>
					<div id="AddButtons" style="float:right; padding: 15px;">
						<input type="button" id="manage" value="Manage" onclick="window.location='/surewaves_easy_ro/utility/manager'" />
					</div>
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
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>js/flexigrid.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(function(){
		$("#flexitable").flexigrid({
				params: get_serialize(),
				url: '/surewaves_easy_ro/utility/utilityGrid',
				dataType: 'json',
				colModel : [
					{display: 'Category', name : 'category_name', sortable: true, width : 270, align: 'center'},
					{display: 'Product', name : 'product_group', sortable: true, width : 280, align: 'center'},
					{display: 'Advertiser', name : 'advertiser', sortable: true, width : 280, align: 'center'},
					{display: 'Brand', name : 'brand', sortable: true, width : 270, align: 'center'},
				],
				sortname: "category_name",
				sortorder: "asc",
				usepager: true,
				title: 'Utility',
				useRp: true,
				rp: 15,
				showTableToggleBtn: true,
				width: 'auto',
				height: '400',
				rpOptions: [10,15,20,25,40],
				pagestat: 'Displaying: {from} to {to} of {total} items.',
				blockOpacity: 0.5,
			});

		$("#searchbtn").click(function() {
			refresh_grid();
		});

});
function get_serialize(obj){
	var p = [];
	if(obj != undefined || obj == ''){
		p.push({name: obj.attr('name'),value: obj.val()});
	}
	var search = $('#search');
	p.push({name: search.attr('name'),value: search.val()});
	var SearchType = $('#SearchType');
	p.push({name: SearchType.attr('name'),value: SearchType.val()});
	return p;
}

function refresh_grid(obj){
	$("#flexitable").flexOptions({params: get_serialize(obj)});
	$('#flexitable').flexOptions({newp: 1}).flexReload();
}
</script>