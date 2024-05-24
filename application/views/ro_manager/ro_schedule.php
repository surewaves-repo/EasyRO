<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/flexigrid.pack.css" />
<script type="text/javascript" src="/surewaves_easy_ro/js/flexigrid.js"></script>
<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->
	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
				
				<h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/DigiXpress-logo.png" style="padding-top:10px;"/></h1>
				<img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			
				
				
				<ul id="nav">
					<li class="active" ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/home">Home</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/ro_report">RO Report</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/audit">Audit Trail</a></li>
					<li><a href="<?php echo ROOT_FOLDER ?>/ro_manager/user">User</a></li>
					<li ><a href="<?php echo ROOT_FOLDER ?>/ro_manager/preferences">Preferences	</a></li>
				</ul>
				
				<p class="user">Hello, <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
			</div>		<!-- #header ends -->
			

			
			
			
			
			<div class="block">
			
				<form method="post" action="<?php echo ROOT_FOLDER ?>/ro_manager/search_content">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>View Order Details</h2>
					
					<ul style="float:right;padding-left:10px;">
						<li ><label> </label> &nbsp; <input type="text" id="SearchTF" class="text" placeholder="Enter text to search ..." value="<?php if ( isset($search_str) && !empty($search_str) ) { echo $search_str; } ?>" name="search_str"   /></li>
						<li ><input type="submit" class="submit" value="search"   /></li>
						
						
					<ul>
					<ul>
						
					</ul>
				</div>		<!-- .block_head ends -->
				</form>
				
				
				<div class="block_content">				
					
						<table cellpadding="0" cellspacing="0" width="100%">
						
							<tr>
								<th>Customer RO number</th>
								<th>Agency Name</th>
								<th>Campaign Start Date</th>
								<th>Campaign End Date</th>
								<th>&nbsp;</th>
								<!--<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>-->
							</tr>			
							
								<tr>
									<td> <?php echo $content['customer_ro_number']?><br/> <?php echo '(Internal RO Number:'.$content['internal_ro_number'].")" ; ?> </td>
									<td><?php echo $content['agency_name'] ?> &nbsp;</td>
									<td><?php print date('d-M-Y', strtotime($content['start_date'])); ?> </td>
									<td><?php print date('d-M-Y', strtotime($content['end_date'])); ?> </td>
									<td><a href="javascript:downloadPDF('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Download PDF</a></td>
									<!--<td><a href="javascript:campaigns_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Campaigns Schedule</a></td>
									<td><a href="javascript:channels_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">Channels Schedule</a></td>
									<td><a href="javascript:ro_schedule('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')">RO Schedule</a></td>-->
								</tr>
													
						</table><br>		
					<div class="paggination right">
							<?php echo $page_links ?>
					</div>		<!-- .paggination ends -->
						
					
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>	<!-- .block ends -->
			<?php foreach($reports as $date=>$report) { ?>
							<div class="block">	
								
								<div class="block_head">
									<div class="bheadl"></div>
									<div class="bheadr"></div> 
									
									<h2><?php echo $date ?></h2>			
								</div>		<!-- .block_head ends -->
								
								<div class="block_content">				
									<table cellpadding="0" cellspacing="0" width="100%">						
										<tr>
											<th>Channel</th>
											<th>Creative</th>
											<th>Program</th>
											<th>Scheduled</br>Impression<br/>(MakeGood Impr.)</th>
											<th>Scheduled</br>Seconds</th>
																	
										</tr>
										<?php foreach($report as $station_id=>$station) { ?>
										<tr>
											<td><?php echo $station['station_name'] ?></br>( Last Updated At: <?php echo $station['last_updated'];//echo date('d M Y, H:i'); ?>)</td>
											<td>All Creatives</td>
											<td>All Programs</td>
											<td><?php echo $station['scheduled_impressions'].'('.$station['makegood_impressions'].')' ?></td>					
											<td><?php echo $station['scheduled_duration'] ?></td>
									
										</tr>
									<?php foreach($station['contents'] as $content_id=>$content) { ?>
										<tr>
											<td>&nbsp;</td>
											<td><?php echo $content['content_name']; ?></td>
											<td>All Programs</td>
											<td><?php echo $content['scheduled_impressions'].'('.$content['makegood_impressions'].')' ?></td>
											<td><?php echo $content['scheduled_duration'] ?></td											
										</tr>
									<?php foreach($content['programs'] as $program_id=>$program) { ?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td><?php echo $program['program_name'] ?></td>
										<!--<td>abc</td>-->
										<td><?php echo $program['scheduled_impressions'].'('.$program['makegood_impressions'].')' ?></td>
										<td><?php echo $program['scheduled_duration'] ?></td>
									</tr>
									<?php } ?>
									<?php } ?>
									<?php } ?>
									</table>
								</div>
								<div class="bendl"></div>
								<div class="bendr"></div>
								
							</div>
						<?php } ?>
					<!--<td> <a href=javascript:add_price_approve('<?php echo  rtrim(base64_encode($content['internal_ro_number']),'=') ?>')>Add Pricing and Approve</a> </td>-->					
				<div>						<!-- wrapper ends -->
		
	</div>		<!-- #hld ends -->
