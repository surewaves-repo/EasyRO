<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>

<div id="hld">

    <div class="wrapper">		<!-- wrapper begins -->



        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->

        <div class="block">

            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <h2>CUSTOMER RELEASE ORDER (NON FCT)</h2>
            </div>

            <div class="block_content">

                <?php
                $fin = (int)$ro_details['financial_year'];
                $fin_end = $fin +1;
                ?>
                <table >
                    <tr>
                        <td>Customer RO Number</td>
                        <td>:</td>
                        <td><?php echo $ro_details['customer_ro_number']?> &nbsp;&nbsp;
                        <?php if($is_international == 1) { ?>
                            <?php if($logged_in_user['user_name'] == $ro_details['account_manager_name'] || $logged_in_user['profile_id'] == 1 || $logged_in_user['profile_id'] == 2 ){?>
                                <a href="javascript:am_edit_non_fct_ext_ro('<?php echo $ro_details['id'] ?>')">View/Modify</a>
                            <?php } ?>
                       <?php  } else { ?>
                            <?php if($logged_in_user['user_name'] == $ro_details['account_manager_name'] || $logged_in_user['user_name'] == $national_head['user_name']){?>
                                <a href="javascript:am_edit_non_fct_ext_ro('<?php echo $ro_details['id'] ?>')">View/Modify</a>
                            <?php } ?>
                       <?php  } ?>   
                        
                    </tr>

                    <tr>
                        <td>Internal RO Number</td>
                        <td>:</td>
                        <td><?php echo $ro_details['internal_ro_number'] ; ?></td>
                    </tr>

                    <tr>
                        <td>Agency Name</td>
                        <td>:</td>
                        <td><?php echo $ro_details['agency'] ?></td>
                    </tr>

                    <tr>
                        <td>Advertise name</td>
                        <td>:</td>
                        <td><?php echo $ro_details['client'] ?> </td>
                    </tr>

                    <tr>
                        <td>Financial Year</td>
                        <td>:</td>
                        <td><?php echo $ro_details['financial_year'];echo "-".$fin_end ?></td>
                    </tr>

                    <tr>
                        <td>Net Revenue</td>
                        <td>:</td>
                        <td><?php echo $ro_details['gross_ro_amount'] ?></td>
                    </tr>

                    <tr>
                        <td>Total Expenses</td>
                        <td>:</td>
                        <td id="total_expenses"><?php echo $ro_details['agency_commision']; ?>&nbsp;&nbsp;
                            <!--commented to display total expences only-->
                            <!--<a href="javascript:add_other_expenses('<?php /*echo  rtrim(base64_encode($ro_details['customer_ro_number']),'=') */?>','<?php /*echo  rtrim(base64_encode($ro_details['internal_ro_number']),'=') */?>','<?php /*echo $ro_details['id'] */?>')">View/Modify</a>-->
                        </td>
                    </tr>

                    <tr>
                        <td>Net Contribution</td>
                        <td>:</td>
                        <td><?php echo round($net_contribution,2) ?></td>
                    </tr>

                    <tr>
                        <td>Net Contribution Amount(%)</td>
                        <td>:</td>
                        <td><?php echo round($net_contribution_percentage,2) ?></td>
                    </tr>

                    <tr>
                        <td>Description</td>
                        <td>:</td>
                        <td><?php if(isset($ro_details['description']) && $ro_details['description'] != "" ){
                                echo $ro_details['description'];
                            } else {
                                echo "None";
                            }?>
                        </td>
                    </tr>
                    
                    <?php if($is_international == 1) {
                         if($logged_in_user['profile_id'] == 1 || $logged_in_user['profile_id'] == 2){ ?>
						<tr>
							<?php if ($ro_details['approved_by'] == 0) {?>
								<td><input type="button" id="submit_approve1" class="submitlong" name="apa" value="Approve" onclick="approve('<?php echo  rtrim(base64_encode($ro_details['internal_ro_number']),'=') ?>');show_processing()" />
									<span id="submit_processing1" style="display:none;color:#008000">Processing..</span>
								</td>
							<?php }else{?>

								<td>RO status</td>
								<td>:</td>
								<td>
									<span>Approved</span>
								</td>

							<?php }?>
						</tr>
					<?php } ?>
                    <?php } else { ?>
                    
					
					<?php if($logged_in_user['profile_id'] == 10){ ?>
						<tr>
							<?php if ($ro_details['approved_by'] == 0) {?>
								<td><input type="button" id="submit_approve1" class="submitlong" name="apa" value="Approve" onclick="approve('<?php echo  rtrim(base64_encode($ro_details['internal_ro_number']),'=') ?>');show_processing()" />
									<span id="submit_processing1" style="display:none;color:#008000">Processing..</span>
								</td>
							<?php }else{?>

								<td>RO status</td>
								<td>:</td>
								<td>
									<span>Approved</span>
								</td>

							<?php }?>
						</tr>
					<?php } ?>
                    <?php } ?>        

                </table>



            </div>	<!-- .block_content ends -->


            <div class="bendl"></div>
            <div class="bendr"></div>

        </div>		<!-- .block ends -->

    </div>
</div>



<script type="text/javascript" language="javascript">
    function approve(order_id){
        window.location.href = "<?php echo ROOT_FOLDER ?>/non_fct_ro/non_fct_approve/" + order_id ;
    }

    function show_processing(){
        $("#submit_approve1").hide();
        $("#submit_approve2").hide();
        $("#submit_processing1").show();
        $("#submit_processing2").show();
    }

    function add_other_expenses(external_ro,order_id,id){
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/non_fct_ro/add_other_expenses_non_fct/'+external_ro+'/'+ order_id + "/" + id,iframe:true, width: '520px', height:'700px'});
    }

    function am_edit_non_fct_ext_ro(id) {
        $.colorbox({href:'<?php echo ROOT_FOLDER ?>/account_manager/am_edit_non_fct_ext_ro/'+id,iframe:true, width: '940px', height:'650px'});
    }
</script>
