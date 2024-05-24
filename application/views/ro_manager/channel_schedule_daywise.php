<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
	
	<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/flexigrid.pack.css" />
<script type="text/javascript" src="/surewaves_easy_ro/js/flexigrid.js"></script>
			<script type="text/javascript" src="/surewaves_easy_ro/js/datetimepicker.js"></script>
			
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Channel_schedule</h2>					
				</div>	
				
				<div class="block_content">
					
					<div class="block_content">				
					
					<table class="flexme" ></table>
					<script type="text/javascript">
					
						
						$(".flexme").flexigrid({
							url : '/surewaves_easy_ro/ro_manager/get_flexi_schedule',
							dataType : 'xml',
							colModel : [ {
								display : 'Client Name',
								name : 'client_name',
								width : 100,
								sortable : true,
								align : 'left'
							},{
								display : 'Product Name',
								name : 'product_new',
								width : 100,
								sortable : true,
								align : 'left'
							},{
								display : 'Brand Name',
								name : 'brand_new',
								width : 100,
								sortable : true,
								align : 'left'
							},{
								display : 'Channel No',
								name : 'channel_id',
								width : 50,
								sortable : true,
								align : 'left'
							},{
								display : 'Channel Name',
								name : 'channel_name',
								width : 100,
								sortable : true,
								align : 'left'
							},{
								display : 'Customer Name',
								name : 'customer_name',
								width : 100,
								sortable : true,
								align : 'left'
							},{
								display : 'Campaign Date',
								name : 'date',
								width : 120,
								sortable : true,
								align : 'left'
							}, {
								display : 'Impressions',
								name : 'day_imp',
								width : 50,
								sortable : true,
								align : 'left'
							} ,{
								display : 'Duration',
								name : 'duration_to_play',
								width : 50,
								sortable : true,
								align : 'left'
							} ,{
								display : 'order_id',
								name : 'order_id',
								width : 100,
								sortable : true,
								align : 'left'
							} 
							],
							buttons : [],
							searchitems : [ {
								display : 'date',
								name : 'date',
								isdefault : true
							},  {
								display : 'Channel Name',
								name : 'channel_name'
							} ],
							sortname : "channel_id",
							sortorder : "asc",
							
							usepager : true,
							title : '',
							useRp : true,
							rp : 50,
							showTableToggleBtn : true,
							width : 1120,
							height : 450
						});

						
					</script>
					
				</div>		
					
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								

		
