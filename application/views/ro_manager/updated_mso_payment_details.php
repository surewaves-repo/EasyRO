<!-- Colorbox pop up for viewing payment for one invoice at a time -->
<!-- <?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?> -->
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> 
<!-- <script src="<?php echo base_url(); ?>assets/external_lib/lib/jquery.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/external_lib/dist/jquery.validate.js" type="text/javascript"></script> -->

<style>
    .rightborder {
    border-right: 1px solid #000000;
    }
    
    .leftborder{
        border-left: 1px solid #000000;
    }

    table {
      border-spacing: 10;
      border-top: 1px solid #ccc;
      width:100%;
    }
    
    .block table tr td, .block table tr th {
    border-bottom: 1px solid #ddd;
    line-height: normal;
    padding: 7px;
    text-align: center;
}
    
</style>
      
        <div class="block">
            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h2>Payment Details</h2>
            </div>
            
            <div class="block_content invoiceData"> 
                <form action="" method="post">
                    <table cellpadding="0" cellspacing="0" id="invoiceData">
                        <tr>
                                
                                <th class="rightborder">Payment Date</th>
                                <th>Total Amount</th>
                                <th>Basic Amount</th>
                                <th>TDS</th>
                                <th class="rightborder">Service Tax</th>
                                <th>Mode of Payment</th>
                                <th>Bank Name</th>
                                <th>Cheque Number</th>
                                <th class="rightborder">Cheque Date</th>
                                <th>Notes</th>
                        </tr>
                        <?php foreach($details as $val) { ?>
                        <tr>
                                
                                <td class="rightborder"><?php echo $val['payment_date'] ?></td>
                                <td><?php echo $val['Round(new_payment,2)'] ?></td>
                                <td><?php echo $val['Round(basic_amount,2)'] ?></td>
                                <td><?php echo $val['Round(tds,2)'] ?></td>
                                <td class="rightborder"><?php echo $val['Round(service_tax,2)'] ?></td>
                                <td><?php echo $val['mode_of_payment'] ?></td>
                                <td><?php echo $val['bank_name'] ?></td>
                                <td><?php echo $val['transaction_number'] ?></td>
                                <td class="rightborder"><?php echo $val['transaction_date'] ?></td>
                                <td><?php echo $val['remarks'] ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </form>    
            </div>  <!-- .block_content ends -->
            
       </div>    

    </div>
</div>