<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?> 

    <div class="block small center login" style="margin:0px">
        <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>					
                <h2>Confirm Add Price</h2>					
        </div>	

        
        <form action="<?php echo ROOT_FOLDER ?>/ro_manager/post_add_price_approve" id="addPriceApprove" method="post">
            <div class="block_content">
                <table cellpadding="0" cellspacing="0" width="100%">						
                    <tr>
                            <th>Network</th>
                            <th>Total Payout</th>	
                    </tr>	



                    <?php foreach($network_amount as $val) { ?>
                    <tr>
                            <td><?php echo $val['network'] ; ?> </td>
                            <td><?php echo $val['net_amount'] ; ?></td>
                    </tr>
                    <?php } ?>
                    <tr> 
                            <td> <span style="font-weight:bold">Total Network Payout </span></td>
                            <td> <span style="font-weight:bold"><?php echo $total_amount ?></span></td>
                    </tr>
                    
                    <tr> 
                            <td> <span style="font-weight:bold">Net Revenue </span></td>
                            <td> <span style="font-weight:bold"><?php echo $net_revenue ?></span></td>
                    </tr>
                    
                    <tr> 
                            <td> <span style="font-weight:bold">Net Contribution Percent </span></td>
                            <td> <span style="font-weight:bold"><?php echo $net_cont_percent ?></span></td>
                    </tr>
                    
                    <?php if($net_cont_percent < 25) { ?>
                    <tr>
                        <td>
                            <span style="color:#F00;" id="show_error_for_justification_approval" style="display:none"></span>
                        </td>
                    </tr>
                    <tr> 
                            <td> Justification For Approval <span style="color:#F00;"> *</span></td>
                            <td>  <input type="text" id="justification_for_approval" name="justification_for_approval"/> </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="color:#F00;" id="show_error_for_corrective_plan" style="display:none"></span>
                        </td>
                    </tr>
                    <tr> 
                            <td> Corrective Action Plan For Future <span style="color:#F00;"> *</span> </td>
                            <td> <input type="text" id="corrective_action_plan" name="corrective_action_plan" /> </td>
                    </tr>
                    <input type="hidden" id="take_action" value="1" />
                    <?php } ?>
                    <tr>	
                            <td> <input type="button" class="submit" value="confirm" onclick="validateTextField();" />	</td>						
                    <tr>

                </table>
            </div>		<!-- .block_content ends -->
            <input type="hidden" name="order_id" value="<?php echo $order_id ?>" />
            <input type="hidden" name="hid_edit" value="<?php echo $edit ?>">
            <input type="hidden" name="hid_id" value="<?php echo $id ?>">
            <input type="hidden" name="hid_internal_ro" value="<?php echo $internal_ro ?>">
        </form>
        

        <div class="bendl"></div>
        <div class="bendr"></div>

    </div>		<!-- .login ends -->
			
		
    <?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>
    
    <script type="text/javascript" language="javascript">

    function validateTextField(){
        if($("#take_action").val() == 1) {
            var objRegExp = /^[\w -.]+$/;
            
            var justification_for_approval = $("#justification_for_approval").val() ;
            if (! objRegExp.test(justification_for_approval)){
                $("#show_error_for_corrective_plan").html("") ;
                $("#show_error_for_justification_approval").html("") ;
                $("#show_error_for_justification_approval").html("Invalid Justification") ;
                $("#show_error_for_justification_approval").show() ;
                $("#justification_for_approval").focus() ;
                return false ;
            }else{
                $("#show_error_for_justification_approval").html("") ;
            }
            
            var corrective_action_plan = $("#corrective_action_plan").val() ;
            if (! objRegExp.test(corrective_action_plan)){
                $("#show_error_for_corrective_plan").html("") ;
                $("#show_error_for_justification_approval").html("") ;
                $("#show_error_for_corrective_plan").html("Invalid Corrective Action Plan") ;
                $("#show_error_for_corrective_plan").show() ;
                $("#corrective_action_plan").focus() ;
                return false ;
            }else{
                $("#show_error_for_corrective_plan").html("") ;
            }
        }
		$('.submit').attr('disabled','disabled');
		document.getElementById("addPriceApprove").submit();
    }
    </script>