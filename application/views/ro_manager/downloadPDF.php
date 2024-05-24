<?php 
require_once("dompdf/dompdf_config.inc.php");

$html =
	$dompdf = new DOMPDF();   
	$html = 
		"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\">
		<head>

			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />		
			<title>Surewaves MediaGrid: Agency View</title>
		
			<style type=\"text/css\" media=\"all\">
				@import url(\"css/style.css\");
				@import url(\"css/jquery.wysiwyg.css\");
				@import url(\"css/facebox.css\");
				@import url(\"css/visualize.css\");
				@import url(\"css/date_input.css\");
				@import url(\"css/colorbox.css\");
			</style>
		
			<style>			
				#response{
					display: none;
					border: 1px solid #ccc;
					background: #FFFFA0;
					padding: 10px;
					width: 300px;
				}
			</style>	
		
			<script type=\"text/javascript\" src=\"js/jquery.js\"></script>
			<script type=\"text/javascript\" src=\"js/jquery.img.preload.js\"></script>
			<script type=\"text/javascript\" src=\"js/jquery.filestyle.mini.js\"></script>
			<script type=\"text/javascript\" src=\"js/jquery.wysiwyg.js\"></script>
			<script type=\"text/javascript\" src=\"js/jquery.date_input.pack.js\"></script>
			<script type=\"text/javascript\" src=\"js/facebox.js\"></script>
			<script type=\"text/javascript\" src=\"js/jquery.visualize.js\"></script>
			<script type=\"text/javascript\" src=\"js/jquery.select_skin.js\"></script>
			<script type=\"text/javascript\" src=\"js/ajaxupload.js\"></script>
			<script type=\"text/javascript\" src=\"js/jquery.pngfix.js\"></script>
			<script type=\"text/javascript\" src=\"js/custom.js\"></script>		
			<script type=\"text/javascript\" src=\"js/jquery.colorbox-min.js\"></script>	
	
		</head>
		<body>
			<div id=\"hld\" style=\"font-size:10px\">	
				<div class=\"wrapper\">	
					<h2>Channel Wise Schedule</h2>";
				$html .= "<div class=\"block_content\" style=\"margin-bottom:10px\"></div> ";
			foreach($reports as $date=>$report) {
				$html .= "<div class=\"block\" style=\"padding-bottom:180px;margin-top:40px\">					
							<div class=\"block_head\">
								<h2>".$date ."</h2>								
							</div>";
				$html .= "<div class=\"block_content\">				
						<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">						
							<tr>
								<th>Channel</th>
								<th>Creative</th>
								<th>Program</th>
								<th>Scheduled Impression</th>
								<th>Scheduled Seconds</th>						
							</tr>";
				foreach($report as $station_id=>$station) {
						$html .= "<tr>
							<td>".$station['station_name'] ."</br>( Last Updated At:".  
							$station['last_updated']."							
							<td>All Creatives</td>
							<td>All Programs</td>
							<td>".$station['scheduled_impressions']."</td>
							<td>".$station['scheduled_duration']."</td>";
						$html .="</tr>";
					foreach($station['contents'] as $content_id=>$content) {
						$html .="<tr>
							<td>&nbsp;</td>
							<td>".$content['content_name'] ."</td>
							<td>All Programs</td>
							<td>".$content['scheduled_impressions']."</td>
							<td>".$content['scheduled_duration']."</td>";
							$html .="</tr>";
				foreach($content['programs'] as $program_id=>$program) { 
							$html.="<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>".$program['program_name']."</td>										
								<td>".$program['scheduled_impressions']."</td>
								<td>".$program['scheduled_duration']."</td>";						
					$html .= "</tr>";
							 }
						}
					}
						$html .="</table>
						  </div></div>";
				}
				$html .="</div></div></body></html>";

$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("sample.pdf", array("Attachment" => 0));		
?>
