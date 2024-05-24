<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 9/18/15
 * Time: 3:53 PM
 */ ?>

<link href="/surewaves_easy_ro/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="/surewaves_easy_ro/includes/css/bootstrap-glyphicons.css" rel="stylesheet">

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<?php if(count($ro_list) > 0) { ?>
    <table class="table">
        <tr class="active">
            <td>Customer RO Number</td>
            <td>Internal RO Number</td>
            <td>Client</td>
            <td>RO Date</td>
            <td>RO Start Date</td>
            <td>RO End Date</td>
            <td>RO Amount</td>
            <td></td>
        </tr>

        <?php foreach($ro_list as $ro) { ?>
            <tr>
                <td><?php echo $ro['cust_ro']?></td>
                <td><?php echo $ro['internal_ro'] ?></td>
                <td><?php echo $ro['client']?></td>
                <td><?php print date('d-M-Y', strtotime($ro['ro_date'])); ?> </td>
                <td><?php print date('d-M-Y', strtotime($ro['camp_start_date'])); ?> </td>
                <td><?php print date('d-M-Y', strtotime($ro['camp_end_date'])); ?> </td>
                <td><?php echo $ro['gross']?></td>
            </tr>
        <?php } ?>

    </table>
<?php } else { ?>
    <div class="alert alert-danger text-center" style="margin-top: 250px" role="alert"><strong>No ROs for current month</strong></div>
<?php } ?>