<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
        <script type="text/javascript" src="/surewaves_easy_ro/js/datetimepicker.js"></script>


                        <div class="block small center login" style="margin:0px">


                                <div class="block_head">
                                        <div class="bheadl"></div>
                                        <div class="bheadr"></div>
                                        <h2>Channels</h2>
                                </div>

				<?php if($value == 0) { ?>
				<div class="block_content">
					<h2 style="text-align:center;">No Active Campaign !</h2>	
				</div>
				<?php } else { ?>
                                <div class="block_content">

                                <table>
                                	<?php   $keys = array_keys($channels);
                        			$c = count($keys);
                        			for($i = 0;$i<$c;$i++) {
                                			$key = $keys[$i]; ?>
                                			<tr>
								<td><?php echo $channels[$key]; ?></td>
                                				<?php   $i++; 
                                					$key = $keys[$i]; ?>
                                				<td><?php echo  $channels[$key]; ?></td>
								<?php $i++; $key = $keys[$i]; ?>
								<td><?php echo $channels[$key] ?></td>
							</tr>
			<?php             			} 
			}		?>
				</table>
                                </div>          <!-- .block_content ends -->

                                <div class="bendl"></div>
                                <div class="bendr"></div>

                        </div>          <!-- .login ends -->
