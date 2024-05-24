<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">

<div class="block small center login" style="margin:0px; width:870px;">


<div class="block_head">
    <div class="bheadl"></div>
    <div class="bheadr"></div>
    <h2>Review RO Details</h2>
</div>


<div class="block_content" style="height: auto">
    <table cellpadding="0" cellspacing="0" width="100%">

        <!--RO details-->
        <tr>
            <td width="40%">External RO Number</td>
            <td width="10%"> : </td>
            <td width="50%">
                <?php echo $ro_details[0]['customer_ro_number']?>
            </td>
        </tr>

        <tr>
            <td>Agency</td>
            <td > : </td>
            <td >
                <?php echo $ro_details[0]['agency']?>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Agency Contact Name</td>
            <td > : </td>
            <td >
                <?php if($agency_contact[0]['agency_contact_name'] != NULL){
                    echo $agency_contact[0]['agency_contact_name'];
                }else{
                    echo "None";
                }?>
            </td>
        </tr>
        <tr>
            <td width="40%">Client</td>
            <td width="10%"> : </td>
            <td width="50%">
                <?php echo $ro_details[0]['client']?>
            </td>
        </tr>
        <tr>
            <td>Client Contact Name</td>
            <td > : </td>
            <td >
                <?php if($client_contact[0]['client_contact_name'] != NULL){
                    echo $client_contact[0]['client_contact_name'];
                }else{
                    echo "None";
                }?>
            </td>
        </tr>
        <tr>
            <td width="40%">Account Manager Name</td>
            <td width="10%"> : </td>
            <td width="50%">
                <?php echo $ro_details[0]['account_manager_name'];?>
            </td>
        </tr>

        <tr>
            <td width="40%">Region</td>
            <td width="10%"> : </td>
            <td width="50%">
                <?php echo $region ?>
            </td>
        </tr>

        <?php
        $fin = (int)$financial_year;
        $fin_end = $fin +1;
        ?>

        <tr>
            <td width="40%">Financial Year</td>
            <td width="10%"> : </td>
            <td width="50%">
                <?php echo $financial_year."-".$fin_end ?>
            </td>
        </tr>



        <tr id="market_tr">
            <td colspan="4" style="padding:0px;">
                <div id="market_div">
                    <table width="100%">
                        <tr>
                            <td colspan="8" nowrap="nowrap" style="border-bottom:none;">Monthwise RO Amount :<span style="color:red;"></span></td>
                        </tr>
                        <tr>
                            <td>
                                <span >January</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['january'])){echo $amount['january'];}else{ echo 0;}?></span>
                            </td>


                            <td>
                                <span >February</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['february'])){echo $amount['february'];}else{ echo 0;} ?></span>
                            </td>

                            <td>
                                <span >March</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['march'])){echo $amount['march'];}else{ echo 0;} ?></span>
                            </td>

                        </tr>

                        <tr>

                            <td>
                                <span >April</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['april'])){echo $amount['april'];}else{ echo 0;} ?></span>
                            </td>

                            <td>
                                <span >May</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['may'])){echo $amount['may'];}else{ echo 0;} ?></span>
                            </td>

                            <td>
                                <span >June</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['june'])){echo $amount['june'];}else{ echo 0;} ?></span>
                            </td>

                        </tr>

                        <tr>
                            <td>
                                <span >July</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['july'])){echo $amount['july'];}else{ echo 0;} ?></span>
                            </td>

                            <td>
                                <span >August</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['august'])){echo $amount['august'];}else{ echo 0;} ?></span>
                            </td>

                            <td>
                                <span >September</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['september'])){echo $amount['september'];}else{ echo 0;} ?></span>
                            </td>
                        </tr>

                        <tr>

                            <td>
                                <span >October</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['october'])){echo $amount['october'];}else{ echo 0;} ?></span>
                            </td>

                            <td>
                                <span >November</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['november'])){echo $amount['november'];}else{ echo 0;} ?></span>
                            </td>

                            <td>
                                <span >December</span>
                            </td>

                            <td>
                                <span><?php if(isset($amount['december'])){echo $amount['december'];}else{ echo 0;} ?></span>
                            </td>

                        </tr>

                    </table>
                </div>
            </td>
        </tr>



        <tr>
            <td width="40%">Gross Amount</td>
            <td width="10%"> : </td>
            <td width="50%">
                <?php echo $ro_details[0]['gross_ro_amount']?>
            </td>
        </tr>

        <tr>
            <td width="40%" nowrap="nowrap">Total Expenses</td>
            <td width="10%"> : </td>
            <td width="50%">
                <?php echo $ro_details[0]['agency_commision']?>
            </td>
        </tr>

        <tr>
            <td width="40%">Description</td>
            <td width="10%"> : </td>
            <td width="50%">
                <?php echo $ro_details[0]['description']?>
            </td>
        </tr>


    </table>
</div>



<div class="bendl"></div>
<div class="bendr"></div>

</div>		<!-- .login ends -->


<?php include_once dirname(__FILE__)."/inc/footer.inc.php" ?>