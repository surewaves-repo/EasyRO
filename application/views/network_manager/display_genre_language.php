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
}
.contents {
    float: left;
    width: 99%;
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
    line-height: 50px;
    margin-right: 3px;
    text-align: center;
    width: 13.5%;
	border-bottom:1px solid #dddddd;
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
    width: 13.5%;	
	padding:2px;
}
.mainDivHeadings {
    width: 100%;
}
.mainDivContents {
    width: 100%;
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
#downloadExcel{
	padding-top:1%;
}
.btn_dwnload{
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
                <h2>Display Genere and Langauges</h2>
            </div>

            <div class="block_content">
            <div class="mainDiv">
				<div  class="mainDivHeadings">
				<div id="downloadExcel">
				<form name="download_displayed_genre_language" action="<?php echo ROOT_FOLDER ?>/network_svc_manager/displayGenereAndLanguageByChannelWise" method="post" target="_blank">
					<input type="hidden" value="1" name="set_download">
					<input type="submit" value="" id="btn_dwnload" style="width: 20%; margin-left: 92%;background: rgba(0, 0, 0, 0) url('../images/excel.png') no-repeat scroll 0 0;cursor:pointer;width:4%;border:none;height:40px;">
				</form>
				</div>
					<!--<div class="filterDiv">
						<form method="post" action="">
                        <label>Filter : </label>
						<select id = "selFilter" class="sw-select" name="filterBy">
						<option>----SELECT---</option>
						<?php/*
							$filterArr = array(1 => 'Network', 2 => 'Channel Name',3 => 'Genre',4 => 'Language');
							$selected = '';
							//if(count($filterParam) > 0){
								foreach($filterArr as $key => $val){
									if($key == $filterParam['key']){
										$selected = 'selected="selected"';
									}*/
						?>
								<option value = "<?php// echo $key; ?>" <?php// echo $selected; ?>><?php// echo $val; ?></option>
						
						<?php
								/*}*/
							//}
						?>
						</select>                        

                        <div id="filterKeyContainer" style="display: inline;">
						<?php
							/*$searchText = '';
							if(count($filterParam) > 0){
								$searchText = $filterParam['key'];
							}*/
						?>
							<input id = "filterKeyText" name="'filterKeyText'" class="textsmall" placeholder="Enter Keyword" required="required" <?php// echo $searchText; ?>/>
                        </div>
                        <input type="submit" name="filter" value=" Filter " class="submit" />
                        <input type="button" name="reset" value=" Reset " class="submit" id="resetBtn" data-href="" />

                    </form>
                </div>-->
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
						Genere
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
							
							<?php								
								$saved_genreIds = explode(",",$val['genre']);
								$genreStr  = '';
								foreach($all_genre as $key=>$arr_genre_val){									
									if(in_array($arr_genre_val['id'],$saved_genreIds)){
										if($genreStr == ''){
											$genreStr = $arr_genre_val['genre'];
										}else{
											$genreStr .= ','.$arr_genre_val['genre'];
										}
									}							
								}
								echo $genreStr;
							?>
												
						</div>
						<div class="innerDiv">							
							<?php
								$languageStr  = '';
								$saved_languageIds = explode(",",$val['language']);
								foreach($all_language as $key=>$arr_langauge_val){									
									if(in_array($arr_langauge_val['id'],$saved_languageIds)){
										if($languageStr == ''){
											$languageStr = $arr_langauge_val['language'];
										}else{
											$languageStr .= ','.$arr_langauge_val['language'];
										}
									}							
								}
								echo $languageStr;
							?>								
						
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
		</div>                
    </div>

    <div class="bendl"></div>
    <div class="bendr"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){


var stickyOffset = $('.mainDivHeadings').offset().top;

$(window).scroll(function(){
  var sticky = $('.mainDivHeadings'),
      scroll = $(window).scrollTop();
    
  if (scroll >= stickyOffset) sticky.addClass('fixed');
  else sticky.removeClass('fixed');
});

});//end of document ready


</script>
