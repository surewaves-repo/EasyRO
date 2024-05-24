
            <div class="block_content" style="border:1px solid lightgrey;border-top-right-radius:5px;border-top-left-radius:5px;">

                <table class='table' cellpadding="0" cellspacing="0" width="100%">

                    <tr>
                        <th style="background-color:#F0EEE9;border-top-left-radius:5px;">Customer RO Number</th>
                        <th style="background-color:#F0EEE9">Agency Name</th>
                        <th style="background-color:#F0EEE9">Brand Name</th>
                        <th style="background-color:#F0EEE9">RO Start Date</th>
                        <th style="background-color:#F0EEE9;border-top-right-radius:5px;">RO End Date</th>
                     </tr>

                    <tr>
                        <td> <?php echo $am_ext_ro?><br/> <?php echo '(Internal RO Number:'.$internal_ro.")" ; ?> </td>
                        <td><?php echo $agency ?> &nbsp;</td>
                        <td><?php echo $brand ?></td>
                        <td><?php print date('d-M-Y', strtotime($camp_start_date)); ?> </td>
                        <td><?php print date('d-M-Y', strtotime($camp_end_date)); ?> </td>
                    </tr>

                </table>

            </div>
            <br>
            <!-- .block_content ends -->
            <?php foreach($channel_summary as $key => $ch)
            {
                foreach($ch as $key1 => $c)
                {
                    if(empty($c))
                    {
                        $value = 0;
                    } else {
                        $value = 1;
                    }
                }
            }
            if($value == 0) { ?>
                <div class="block_content">
                    <p style="text-align:center;font-size:18px;">No Active Campaign !</h2>
                </div>
            <?php } elseif($value ==1) { ?>
                <div class="block_content" style="border:1px solid lightgrey;border-top-right-radius:5px;border-top-left-radius:5px;">
                    <table class="table" cellpadding="0" cellspacing="0" width="100%">

                        <tr>
                            <th style="background-color:#F0EEE9;border-top-left-radius:5px;">Channel Name</th>
                            <th style="background-color:#F0EEE9">Network Name</th>
                            <th style="background-color:#F0EEE9">Caption Name</th>
                            <th style="background-color:#F0EEE9">Start Date</th>
                            <th style="background-color:#F0EEE9">End Date</th>
                            <th style="background-color:#F0EEE9">Total Impressions</th>
                            <th style="background-color:#F0EEE9">Caption Duration(Sec)</th>
                            <th style="background-color:#F0EEE9;border-top-right-radius:5px;">Total AdSeconds </th>
                        </tr>

                        <tr><?php foreach($channel_summary as $c) { ?>
                            <td><?php echo $c['channel_name'] ?></td>
                            <td><?php echo $c['customer_name'] ?></td>
                            <td><?php echo $c['Caption_name'] ?></td>
                            <td><?php print date('d-M-Y', strtotime( $c['start_date']))  ?></td>
                            <td><?php print date('d-M-Y', strtotime( $c['end_date']))     ?></td>
                            <td><?php echo $c['timp']  ?></td>
                            <td><?php echo round($c['ro_duration'],2)?></td>
                            <td> <?php echo $c['ro_duration']* $c['timp']?>
                        </tr><?php }?>
                    </table>
                </div>
            <?php } ?>

