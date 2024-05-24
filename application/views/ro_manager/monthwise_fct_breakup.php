<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<style type="text/css">
    .sw-table td{
        text-align: center;
    }
</style>

<div id="hld">
   <div class="wrapper">
    <div id="header">
        <div class="hdrl"></div>
        <div class="hdrr"></div>

        <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>	
        <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>			


        <?php echo $menu; ?>

        <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
    </div><!-- #header ends -->

    <div class="block">		
        <div class="block_content">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <th>Customer RO Number</th>
                    <th>Agency Name</th>
                    <th>Advertise name</th>
                    <th>Brand Name</th>
                    <th>RO Start Date</th>
                    <th>RO End Date</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>			
                <tr>
                    <td> <?php echo $am_ext_ro?><br/> <?php echo '(Internal RO Number:' . $internal_ro . ")"; ?> </td>
                    <td><?php echo $agency ?> &nbsp;</td>
                    <td><?php echo $client ?></td>
                    <td><?php echo $brand ?></td>
                    <td><?php print date('d-M-Y', strtotime($camp_start_date)); ?> </td>
                    <td><?php print date('d-M-Y', strtotime($camp_end_date)); ?> </td>
                    <td><a href="javascript:channels_schedule('<?php echo  rtrim(base64_encode($internal_ro),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Channels Schedule</a></td>
                    <td><a href="javascript:approve('<?php echo  rtrim(base64_encode($internal_ro),'=') ?>','<?php echo $edit ?>','<?php echo $id ?>')">Approval Page</a></td>	
                </tr>

            </table><br>		

        </div>	<!-- .block_content ends -->

        <div class="block_content">
            <table cellpadding="0" cellspacing="0" width="100%">
             <tr>	
                <td> &nbsp; </td>
                <th>Revenue</th>
                <th>Network Payout</th>
                <th>Net Contribution</th>
                <th>Net Contribution %</th>
            </tr>
            <?php foreach($monthly_values as $key=>$values)  { ?>

            <tr>
                <td style="font-weight:bold"><?php echo $key ?> </td>
                <td><?php echo $values['revenue'] ?></td>
                <td><?php echo $values['network_payout'] ?></td>
                <td><?php echo $values['net_contribution'] ?></td>
                <td><?php echo $values['net_contribution_percent'] ?></td>
            </tr> 

            <?php } ?>
        </table>    
    </div> <!-- .block_content ends --> 

    <div class="block_content">
        <table cellpadding="0" cellspacing="0" width="100%">
         <tr>				
            <th>Market Name</th>
            <th>Spot Price</th>
            <th>Banner Price</th>
            <th>Channel Name</th>
        </tr>
        <?php foreach($market_channels_scheduled as $mkts)  : ?>
            <tr>
                <td><?php echo $mkts['market_name'] ?></td>
                <td><?php echo $mkts['spot_price'] ?></td>
                <td><?php echo $mkts['banner_price'] ?></td>

                <?php $channel_name = '';
                foreach ($mkts['channels_scheduled'] as $key => $value) {
                   if (!isset($channel_name) || empty($channel_name)) {
                      $channel_name = $value['channel_name'];
                  } else {
                      $channel_name = $channel_name . "," . $value['channel_name'];
                  }

              }
              ?>
              <td><?php echo $channel_name ?></td>
          </tr>
      <?php endforeach; ?>
  </table>    
</div> <!-- .block_content ends -->      

<div class="block_content">
    <table width="100%">
        <thead>
            <tr>
                <th width="35%">Channel Name</th>
                <th width="65%">Content Details</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach($channels_scheduled as $chnl) :  
                ?>
            <tr>
                <td><?php echo $chnl['channel_name']; ?></td>
                <td>
                    <?php 
                    $data = array(
                        'spots' => array(
                            'count' => 0,
                            ),
                        'banners' => array(
                            'count' => 0,
                            )
                        );

                    $report_spot = '';
                    $report_banner = '';

                    if(isset($chnl['monthwise']['spot_fct'])){
                        foreach ($chnl['monthwise']['spot_fct'] as $content => $details) {

                            $report_spot .= '<tr>';
                            $report_spot .= "<td>$content</td>";

                            foreach ($details as $month => $number) {
                                if(!isset($data['spots'][$month]))
                                    $data['spots'][$month] = 0;
                                $data['spots'][$month] += $number;

                                $report_spot .= "<td><span align=\"center\">$month<br />$number</span></td>";
                            }

                            $report_spot .= '</tr>';
                            $data['spots']['count']++;
                        }
                    }
                    if(isset($chnl['monthwise']['banner_fct'])){
                        foreach ($chnl['monthwise']['banner_fct'] as $content => $details) {

                            $report_banner .= '<tr>';
                            $report_banner .= "<td>$content</td>";

                            foreach ($details as $month => $number) {
                                if(!isset($data['banners'][$month]))
                                    $data['banners'][$month] = 0;
                                $data['banners'][$month] += $number;

                                $report_banner .= "<td><span align=\"center\">$month<br />$number</span></td>";
                            }

                            $report_banner .= '</tr>';
                            $data['banners']['count']++;
                        }
                    }
                    ?>
                    <strong>Spot Ads</strong> : <?php echo $data['spots']['count']; // . '|' . $data['spots'];  ?> 
                    <strong>Banner Ads</strong> : <?php echo $data['banners']['count']; // . '|' . $data['spots'];  ?> 
                    <button class="swAccordionBtn submit" data-target-id="<?php echo $chnl['channel_id']; ?>">Details</button>
                    <div class="swAccordion" id="swAccordion<?php echo $chnl['channel_id']; ?>">
                        <?php if($data['spots']['count'] != 0): ?>
                        <table class="sw-table">
                            <thead>
                                <tr>
                                    <th>Content Name</th>
                                    <th>Spot Ad Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  echo $report_spot; ?>                            
                        </tbody>
                        </table>
                        <?php endif; ?>
                        <?php if($data['banners']['count'] != 0): ?>
                        <table class="sw-table">
                            <thead>
                                <tr>
                                    <th>Content Name</th>
                                    <th>Banner Ad Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  echo $report_banner; ?>                            
                        </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php 
            endforeach; 
            ?>
        </tbody>
    </table>
</div>
<?php  /* ?>
<table cellpadding="0" cellspacing="0" width="100%">
 <tr>				
    <th>Channel Name</th>
    <th>Content Name</th>
</tr>

<?php foreach($channels_scheduled as $chnl) { ?>
<tr>
    <td><?php echo $chnl['channel_name']; ?></td>
    <td>

       <!-- <table> -->
                            <?php /*if(isset($chnl['spot_fct'])) { ?>
									 <?php foreach($chnl['spot_fct'] as $contents=>$month_impression) { ?>
									 <tr><td><?php echo $contents ." (Spot)";?></td>
									 <?php  foreach($month_impression as $month=>$impression) { ?>
									 <td><?php echo $month."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$impression ;?></td>
									 <?php } ?>
									 </tr>
									 <?php } ?>
									 <?php } ?>

									 <?php if(isset($chnl['banner_fct'])) { ?>
									 <?php foreach($chnl['banner_fct'] as $content_bnr=>$month_impression_bnr) { ?>
									 <tr><td><?php echo $content_bnr ." (Banner)";?></td>
									 <?php  foreach($month_impression_bnr as $month_bnr=>$impression_bnr) { ?>
									 <td><?php echo $month_bnr."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$impression_bnr ;?></td>
									 <?php } ?>
									 </tr>
									 <?php } ?>
									 <?php }
                                    ?>
                                    <!-- </table>  -->
                                    <button class="swAccordionBtn" data-target-id="<?php echo $chnl['channel_id']; ?>">Details</button>
                                </td>                   
                            </tr>
                            <tr>
                               <td colspan="2" class="swAccordion" id="swAccordion<?php echo $chnl['channel_id']; ?>">
                                  This is my Conten<br />
                                  <p>
                                     Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                 </p>
                             </td>
                         </tr>
                         <?php } ?>
                     </table>   */ ?>
                 </div> <!-- #block_content ends -->
             </div> <!-- #block ends -->
         </div> <!-- #wrapper ends -->
     </div><!-- #hld ends -->
     

     <script language="javascript">
      function approve(order_id,edit,id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/approve/" + order_id + "/" + edit + "/" + id;
    }
    function channels_schedule(order_id,edit,id) {

        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/channels_schedule/" + order_id + "/" + edit + "/" + id;
    }
    $(document).ready(function(){
    	$(".swAccordion").animate({
            height: 'toggle'
        }, 10);
    });
    $(".swAccordionBtn").click(function(){
    	var targetTr = '#swAccordion' + $(this).attr('data-target-id');
    	$(targetTr).animate({
    		height: 'toggle'
        }, 250);
    });
</script>