<?php
/**
 * View to Display the Channel Status
 */
include_once dirname(__FILE__) . "/../inc/header.inc.php";
?>
<!--<link rel="stylesheet" type="text/css" href="<?php// echo base_url(); ?>css/reach_report/style.css">-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reach_report/prism.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reach_report/chosen.css">
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/googleapis_jquery_1_6_4.min.js"></script>
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/chosen.jquery.js"></script>
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/prism.js"></script>
<style type="text/css">
.block .block_content ul{
	padding-bottom :0 !important;
}
    .block form select.sw-select {
        width: 245px;
        height: 33px;
        margin-right: 20px;
        padding: 7px;
        display: inline-block;
        margin-right: 20px;
        border: 1px solid #bbb;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        /*background: url('../images/sdd.jpg') center right no-repeat;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;*/
    }
    .block form input.textsmall{
        width: 126px !important;

    }
    .block form .sw-select:hover {
        /*background: url('../images/sdd_.jpg') center right no-repeat;*/
    }
	.mainDiv {
    margin: 0 auto;
    padding: 0;
    width: 95%;
}
.headings {
    float: left;
    width: 100%;
	border-bottom:1px solid #dddddd;
}
.contents {
    float: left;
    width: 98%;
	margin-top: 5px;
	background-color: #fbfbfb;
	border-bottom:1px solid #dddddd;
}
/*.contents:hover {
    background-color: #000000;
}*/
.innerDivHeadings {    
    float: left;
    font-size: 14px;
    font-weight: bold;
    height: 17%;
    line-height: 30px;
    margin-right: 3px;
    text-align: center;
    width: 11.5%;
	/* border-bottom:1px solid #dddddd; */
	padding:0.3px;
}
.innerDiv {
    color: #000000;
    float: left;
    font-size: 12px;
    height: 16%;
    line-height: 34px;
    margin-right: 3px;
    text-align: center;
    width: 11.5%;
	
	
	padding:2px;
}
.mainDivHeadings {
    width: 100%;
}
.mainDivContents {
    width: 100%;
}
.change_for_sticky{
	margin-top:100px;
}
.contents:nth-child(odd)  {
    background-color: #fbfbfb;
}
.contents:nth-child(even){
    background-color: #FFF;
}
.filterDiv{
	margin-left: 3px;
	margin-top: 5px;
}
.fixed {
	background: #ffffff none repeat scroll 0 0;
	box-shadow: 4px 20px 37px -8px rgba(0, 0, 0, 0.42);
	left: 116px;
	position: fixed;
	top: 0px;
	width: 83%;
	z-index: 9999;
 }
.submit{
	background: rgba(0, 0, 0, 0) url("../images/btns.gif") repeat scroll center top;
	border: 0 none;
	color: #ffffff;
	cursor: pointer;
	font-family: "Titillium800","Trebuchet MS",Arial,sans-serif;
	font-size: 14px;
	font-weight: normal;
	height: 30px;
	line-height: 30px;
	margin-right: 10px;
	text-shadow: 1px 1px 0 #0a5482;
	text-transform: uppercase;
	vertical-align: middle;
	width: 85px;
}
.submit:hover {
	background: rgba(0, 0, 0, 0) url("../images/btns_.gif") no-repeat scroll center top;
	text-shadow: 1px 1px 0 #b55f10;
}

#footer_new {     
	float: left;
	margin-left: 47%;
	margin-top: 5px;
	width: 62%;
}

.block .block_content{
	overflow:initial !important;
	display:inline-block;
}
#load_more{
	background-color: #cccccc;
	border-radius: 6px;
	float: left;
	height: 40px;
	line-height: 42px;
	margin-top: 3px;
	width: 100%;
	cursor:pointer;
}
#no_load_more{
	background-color: #cccccc;
	border-radius: 6px;
	float: left;
	height: 40px;
	line-height: 42px;
	margin-top: 3px;
	width: 100%;	
}
.more{
	height: 294px;
	margin-left: 45%;
}
#load_more_animation{
	display:none;
	padding-left:36%;
	float:left;
	margin-top:1%;
	margin-bottom:1%;
	
}
#downloadExcel{
	padding-top:1%;
}
</style>

<div id="hld">

    <div class="wrapper">

        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">
                Hello, <?php echo $logged_in_user['user_name'] ?>
                | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a>
            </p>
        </div>

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h2>Edit Genere and Langauges</h2>
            </div>

            <div class="block_content">
            <div class="mainDiv">
				<div  class="mainDivHeadings">
				<div id="downloadExcel" style="padding-top:1%">
				<form name="download_displayed_genre_language" action="<?php echo ROOT_FOLDER ?>/network_svc_manager/displayGenereAndLanguageByChannelWise" method="post" target="_blank">
					<input type="hidden" value="1" name="set_download">
					<input type="submit" value="" id="btn_dwnload" style="width: 20%; margin-left: 92%;background: rgba(0, 0, 0, 0) url('../images/excel.png') no-repeat scroll 0 0;cursor:pointer;width:4%;border:none;height:40px;">
				</form>
				</div>
					<div class="headings">
						<div class="innerDivHeadings">
							State
						</div>
						<div class="innerDivHeadings">
							Network
						</div>
						<div class="innerDivHeadings">
							Channel Name
						</div>
						<div class="innerDivHeadings">
							Genre
						</div>
						<div class="innerDivHeadings" style="width:12%">
							Dominant Content
						</div>
						<div class="innerDivHeadings">
							Language
						</div>
						<div class="innerDivHeadings">
							Channel Status
						</div>
						<div class="innerDivHeadings">
							Deployment Status
						</div>
					</div>
				</div>
			<form name="updateGenrelanguage" action="<?php echo ROOT_FOLDER ?>/network_svc_manager/update_language_genre" method="post">
			<div  class="mainDivContents" >
			
				<?php
				if(count($displayGenereLanguageArr > 0)){
					foreach($displayGenereLanguageArr as $val){
						switch($val['id']){
							case 12:
								$val['state'] .= ' and Telengana';
								break;
							case 14:
								$val['state'] .= ' and Suburbs';
								break;
							case 15:
								$val['state'] .= ' and NCR';
								break;
						}
				?>
					<div class="contents">
					<div class="innerDiv">
							<?php echo $val['state']; ?>
						</div>
						<div class="innerDiv">
							<?php echo $val['network_name']; ?>
						</div>
						<div class="innerDiv">
							<?php echo $val['channel_names']; ?>
						</div>
						<div class="innerDiv">
							<select id="" name="select_genre[<?php echo $val['channel_id']; ?>][]" class="multiple_select sel_genre" multiple data-placeholder="Select a Genre" data-oldval="<?php echo $val['genre']; ?>"   data-id="<?php echo $val['channel_id']; ?>">
							<?php
								
								$saved_genreIds = explode(",",$val['genre']);
								foreach($all_genre as $key=>$arr_genre_val){
									$selected  = '';
									if(in_array($arr_genre_val['id'],$saved_genreIds)){
										$selected = 'selected="selected"';
									}
							?>
									<option value="<?php echo $arr_genre_val['id']; ?>" <?php echo $selected; ?> > <?php echo $arr_genre_val['genre']; ?></option>
							<?php
								}
							?>
							</select>							
						</div>
						<div class="innerDiv" style="width:12%">
							<select id="" name="select_dominant_content[<?php echo $val['channel_id']; ?>][]" class="multiple_select sel_dominant_content" multiple data-placeholder="Select Dominant Content" data-oldval="<?php echo $val['dominant_content']; ?>"   data-id="<?php echo $val['channel_id']; ?>">
							<?php
								
								$saved_dominant_contents = explode(",",$val['dominant_content']);
								foreach($all_dominantContent as $key=>$arr_dominantContent_val){
									$selected  = '';
									if(in_array($arr_dominantContent_val['id'],$saved_dominant_contents)){
										$selected = 'selected="selected"';
									}
							?>
									<option value="<?php echo $arr_dominantContent_val['id']; ?>" <?php echo $selected; ?> > <?php echo $arr_dominantContent_val['dominant_content']; ?></option>
							<?php
								}
							?>
							</select>							
						</div>
						<div class="innerDiv">
							<select id="" name="select_langauge[<?php echo $val['channel_id']; ?>][]" class="multiple_select sel_langauge" multiple data-placeholder="Select a Language" data-oldval="<?php echo $val['language']; ?>" data-id="<?php echo $val['channel_id']; ?>">
							<?php
								
								$saved_languageIds = explode(",",$val['language']);
								foreach($all_language as $key=>$arr_langauge_val){
									$selected  = '';
									if(in_array($arr_langauge_val['id'],$saved_languageIds)){
										$selected = 'selected="selected"';
									}
							?>
									<option value="<?php echo $arr_langauge_val['id']; ?>" <?php echo $selected; ?> > <?php echo $arr_langauge_val['language']; ?></option>
							<?php
								}
							?>
							</select>		
						
						</div>
						<div class="innerDiv">
							<?php 
								if(in_array($val['channel_id'],$channel_list_with_ping)){
									echo 'ONLINE';
								}else{
									echo 'OFFLINE';
								}
							?>
						</div>
						<div class="innerDiv">
							<?php echo $val['deployment_status']; ?>
						</div>
					</div>
				<?php
					}
				}else{
				?>
					<div class="contents">
						<span style="color:red">
							<?php
								echo 'No records Present';
							?>
						</span>
					</div>
				<?php
				}
				?>
				
				
			</div>
			<?php 
			if($fetchRecords == 0){
				$styleForNoRecords="display:block";
				$styleForRecords="display:none";
			}else{
				$styleForNoRecords="display:none";
				$styleForRecords="display:block";				
			}
			?>
			<div id="no_load_more" style="<?php echo $styleForNoRecords; ?>">
				
					<span class="more">NO More Contents</span>	
				
			</div>
			
			<div id="load_more" style="<?php echo $styleForRecords; ?>">
				
					<span class="more">Load More Contents</span>	
				
			</div>
			<div id="load_more_animation">
				
				<span>Loading.. Please Wait.</span>
				<span><img src="../images/ajax-loader_big.gif"></span>
				
			</div>

			<div id="footer_new">
				
					<input type="button" name="" value=" Update " class="submit" id="btn_submit" />	
				
			</div>
			
			<input type="hidden" name="changedChIDforGenre" id="changedChIDforGenre"/>
			<input type="hidden" name="changedChIDforlanguage" id="changedChIDforlanguage"/>
			<input type="hidden" name="changedChIDfordominanatContent" id="changedChIDfordominanatContent"/>
		</form>	
			<input type="hidden" name="isIndirectLoad" id="isIndirectLoad" />
			<input type="hidden" name="fromRecordNo" id="fromRecordNo" value="<?php echo $fromRecordNo; ?>"/>
		</div>
	
    </div>

    <div class="bendl"></div>
    <div class="bendr"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
$(".multiple_select").chosen({width:"100%"});

var stickyOffset = $('.mainDivHeadings').offset().top;

$(window).scroll(function(){
  var sticky = $('.mainDivHeadings'),
      scroll = $(window).scrollTop();
    
  if (scroll >= stickyOffset){
	sticky.addClass('fixed');
	$('.mainDivContents').addClass('change_for_sticky');
  }else{
	sticky.removeClass('fixed');
	$('.mainDivContents').removeClass('change_for_sticky');
  }
});
$('#resultLoading').hide();
$('#load_more').click(function(){
	$(this).hide();
	$('#isIndirectLoad').val(1);
	$('#load_more_animation').show();
	$.ajax({
							type: "POST",
							url: "<?php echo ROOT_FOLDER ?>/network_svc_manager/getGenereAndLanguageByChannelWise",
							//dataType: 'json',
							//async:false,
							data: {isIndirectLoad: 1,fromRecordNo:$('#fromRecordNo').val()},
							success: function(res) {
								var contentStr  = '';
								resJsonObj = JSON.parse(res);
								//console.log(resJsonObj);
								
								
								for(var key in resJsonObj.displayGenereLanguageArr){
									var genreSelectBoxOptions = '';
									var languageSelectBoxOptions = '';
									var dominantSelectBoxOptions = '';
									contentStr += '<div class="contents">';
									contentStr += '<div class="innerDiv">'+resJsonObj.displayGenereLanguageArr[key].state+'</div>';
									contentStr += '<div class="innerDiv">'+resJsonObj.displayGenereLanguageArr[key].network_name+'</div>';
									contentStr += '<div class="innerDiv">'+resJsonObj.displayGenereLanguageArr[key].channel_names+'</div>';
									var genreSelectedArr = Array();
									var languageSelectedArr = Array();
									var dominantContentArr	= Array();
									if(resJsonObj.displayGenereLanguageArr[key].genre != null){
										genreSelectedArr 		= (resJsonObj.displayGenereLanguageArr[key].genre).split(",");
									}
									if(resJsonObj.displayGenereLanguageArr[key].language != null){
										languageSelectedArr 	= (resJsonObj.displayGenereLanguageArr[key].language).split(",");
									}
									if(resJsonObj.displayGenereLanguageArr[key].dominant_content != null){
										dominantContentArr 	 	= (resJsonObj.displayGenereLanguageArr[key].dominant_content).split(",");
									}
									contentStr += '<div class="innerDiv"><select name="select_genre['+resJsonObj.displayGenereLanguageArr[key].channel_id+'][]" class="multiple_select sel_genre" multiple data-placeholder="Select a Genre" data-oldval="'+resJsonObj.displayGenereLanguageArr[key].genre+'" data-id="'+resJsonObj.displayGenereLanguageArr[key].channel_id+'">';
									for(var genreKey in resJsonObj.all_genre){
										var selectedGenre = '';
										if(genreSelectedArr.indexOf(resJsonObj.all_genre[genreKey].id) != -1){
											selectedGenre = 'selected="selected"';
										}
										genreSelectBoxOptions += '<option '+selectedGenre+'  value="'+resJsonObj.all_genre[genreKey].id+'">'+resJsonObj.all_genre[genreKey].genre+'</option>';
									}
									contentStr += genreSelectBoxOptions+'</select></div>';
									
									contentStr += '<div class="innerDiv"><select name="select_dominant_content['+resJsonObj.displayGenereLanguageArr[key].channel_id+'][]" class="multiple_select sel_dominant_content" multiple data-placeholder="Select Dominant Content" data-oldval="'+resJsonObj.displayGenereLanguageArr[key].dominant_content+'" data-id="'+resJsonObj.displayGenereLanguageArr[key].channel_id+'">';
									for(var dominantContentKey in resJsonObj.all_dominantContent){
										var selectedDominantContent = '';
										if(dominantContentArr.indexOf(resJsonObj.all_dominantContent[dominantContentKey].id) != -1){
											selectedDominantContent = 'selected="selected"';
										}
										dominantSelectBoxOptions += '<option '+selectedDominantContent+'  value="'+resJsonObj.all_dominantContent[dominantContentKey].id+'">'+resJsonObj.all_dominantContent[dominantContentKey].dominant_content+'</option>';
									}
									contentStr += dominantSelectBoxOptions+'</select></div>';
									
									contentStr += '<div class="innerDiv"><select name="select_langauge['+resJsonObj.displayGenereLanguageArr[key].channel_id+'][]" class="multiple_select sel_langauge" multiple data-placeholder="Select a Language" data-oldval="'+resJsonObj.displayGenereLanguageArr[key].language+'" data-id="'+resJsonObj.displayGenereLanguageArr[key].channel_id+'">';
									
									for(var languageKey in resJsonObj.all_language){
										var selectedLanguage = '';
										if(languageSelectedArr.indexOf(resJsonObj.all_language[languageKey].id) != -1){
											selectedLanguage = 'selected="selected"';
										}
										languageSelectBoxOptions += '<option  '+selectedLanguage+' value="'+resJsonObj.all_language[languageKey].id+'">'+resJsonObj.all_language[languageKey].language+'</option>';
									}
									
									contentStr += languageSelectBoxOptions+'</select></div>';
									
									var statusText = 'OFFLINE';
									if(resJsonObj.channel_list_with_ping != null){
										if((resJsonObj.channel_list_with_ping).indexOf(resJsonObj.displayGenereLanguageArr[key].channel_id) != -1){
											statusText = 'ONLINE';
										}
									}
									contentStr += '<div class="innerDiv">'+statusText+'</div>';
									contentStr += '<div class="innerDiv">'+resJsonObj.displayGenereLanguageArr[key].deployment_status+'</div>';
									contentStr += '</div>';
								}
								$('.mainDivContents').append(contentStr);
								$("#fromRecordNo").val(resJsonObj.fromRecordNo);
								if(resJsonObj.fetchRecords){
									$('#load_more').show();
									$('#load_more_animation').hide();
									$('#no_load_more').hide();
								}else{									
									$('#load_more_animation').hide();
									$('#no_load_more').show();
								}
								$(".multiple_select").chosen({width:"100%"});
							},
							error:	function (e) {
       							alert("failed to Fetch the data");
								$('#load_more').show();
								$('#load_more_animation').hide();
								$('#no_load_more').hide();
    						}
	});
	
});
$('#btn_submit').click(function(){
	var changed_genreArr 		= Array();
	var changed_languageArr 	= Array();
	var changed_dominantContent = Array();
	$(".sel_genre").each(function(){
		var genreVal = $(this).val();
		if(genreVal != null){
			//alert(genreVal.toString());
			if((genreVal.toString()) != $(this).attr('data-oldval')){
				changed_genreArr.push($(this).attr('data-id'));
			}
		}
	});
	$(".sel_langauge").each(function(){
		var languageVal = $(this).val();
		if(languageVal != null){
			//alert(genreVal.toString());
			if((languageVal.toString()) != $(this).attr('data-oldval')){
				changed_languageArr.push($(this).attr('data-id'));
			}
		}
	});
	$(".sel_dominant_content").each(function(){
		var dominantContentVal = $(this).val();
		if(dominantContentVal != null){
			//alert(genreVal.toString());
			if((dominantContentVal.toString()) != $(this).attr('data-oldval')){
				changed_dominantContent.push($(this).attr('data-id'));
			}
		}
	});
	if(changed_genreArr.length > 0){
		$("#changedChIDforGenre").val(changed_genreArr.toString());
	}
	if(changed_languageArr.length > 0){
		$("#changedChIDforlanguage").val(changed_languageArr.toString());
	}
	if(changed_dominantContent.length > 0){
		$("#changedChIDfordominanatContent").val(changed_dominantContent.toString());
	}
	document.updateGenrelanguage.submit();
});


});//end of document ready


</script>
