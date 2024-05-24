<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<style>
.bar{
                height:20px;
                width:auto;
                text-align: center;
                border:1px solid #999;   
                margin:12px;
                border-radius: 10px;
                background: #b25bead;
                background: -moz-linear-gradient(top,  #b3bead 0%, #dfe5d7 60%, #fcfff4 100%);
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#b3bead), color-stop(60%,#dfe5d7), color-stop(100%,#fcfff4));
                background: -webkit-linear-gradient(top,  #b3bead 0%,#dfe5d7 60%,#fcfff4 100%);
                background: -o-linear-gradient(top,  #b3bead 0%,#dfe5d7 60%,#fcfff4 100%);
                background: -ms-linear-gradient(top,  #b3bead 0%,#dfe5d7 60%,#fcfff4 100%);
                background: linear-gradient(to bottom,  #b3bead 0%,#dfe5d7 60%,#fcfff4 100%);
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b3bead', endColorstr='#fcfff4',GradientType=0 );
            }
            .bar .value{
                height:100%;
                border-radius: 8px;
                background: #8fd;
                background: -moz-linear-gradient(top,  #87e0fd 0%, #53cbf1 40%, #05abe0 100%);
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#80d), color-stop(40%,#53cbf1), color-stop(100%,#05abe0));
                background: -webkit-linear-gradient(top,  #87e0fd 0%,#53cbf1 40%,#05abe0 100%);
                background: -o-linear-gradient(top,  #85d 0%,#53cbf1 40%,#05abe0 100%);
                background: -ms-linear-gradient(top,  #87e0fd 0%,#53cbf1 40%,#05abe0 100%);
                background: linear-gradient(to bottom,  #87e0fd 0%,#53cbf1 40%,#39C 100%);
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#87e0fd', endColorstr='#05abe0',GradientType=0 );  
            }
            
#Performance_scroller {
  height: 450px;
  overflow: hidden;
  position: relative;
}


</style>

<div id="hld">
    <div class="wrapper">       <!-- wrapper begins -->
        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1> 
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>         

            <input type="hidden" id="profile_id" value="<?php echo $profile_id?>" />
            <?php echo $menu ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>      <!-- #header ends -->
        
        <div class="block">
            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h2>Get Performance</h2>
            </div>

            <div class="block_content">
                <form action="" method="post">
                    <p>
                        <label>Start date:</label> 
                        <input type="text" readonly="readonly" id="start_date" name="start_date" onchange="javascript:getNetwork()"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <label style="margin-left:100px">End date:</label> 
                        <input type="text" readonly="readonly" id="end_date" name="end_date" onchange="javascript:getNetwork()"/>
                        <label style="margin-left:100px">Network:</label> 
                        <select id="networkId" name="networkID" style="width:140px;" class="sel_network">
                        </select>
                        <input style="margin-left: 50px" type="button" class="_button searchValue" id="" value="Submit" onclick="" />
                    </p>
                </form>
            </div>  <!-- .block_content ends -->
            
        </div>    
        
        <div class="block">
            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h2>Performance Report</h2>                
            </div>
            
            <div class="block_content invoiceData"> 
                <form action="" method="post">
                    <table cellpadding="0" cellspacing="0" width="100%" id="roData">
                        <tr>
                                <th>RO Number</th>
                                <th style="text-align:center">RO Performance</th>
                                <th style='text-align:center'>Mail Status</th>
                                <th>&nbsp;&nbsp;</th>
                        </tr>

                    </table>
                </form>    
            </div>  <!-- .block_content ends -->
            
       </div>    

    </div>
</div> 
<script src="https://cdn.jsdelivr.net/colorbox/1.6.3/jquery.colorbox-min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/colorbox/1.6.3/jquery.colorbox.js" type="text/javascript"></script> 
<script type="text/javascript">
 $(document).ready(function(){ 

    var start_date = $("#start_date").val() ;
    var end_date = $("#end_date").val() ;
    if(start_date === '' ){
        start_date = new Date() ;
    }
    if(end_date === '') {
        end_date = new Date() ;
    }
    
    var minEndDate = start_date ;
    var maxEndDate = new Date(start_date);
    maxEndDate.setMonth(maxEndDate.getMonth() + 3);
    
    var maxStartDate = end_date ;
    var minStartDate = new Date(end_date );
    minStartDate.setMonth(minStartDate.getMonth() - 3);
    
    $("#start_date").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        minDate : minStartDate,
        maxDate : maxStartDate,
        onClose: function( selectedDate ) {
            $( "#start_date" ).datepicker( "option" );
        }
    }).datepicker("option", "maxDate", maxStartDate);
    
    $("#end_date").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        minDate : minEndDate,
        maxDate : maxEndDate,
        onClose: function( selectedDate ) {
            $( "#end_date" ).datepicker( "option"  );
        }
    }).datepicker("option", "maxDate", maxEndDate);
    
    $('.searchValue').click(function(){
        var start_date = $("#start_date").val() ;
        var end_date = $("#end_date").val() ;
        var network_id = $("#networkId").val() ;
        $("td").remove();
        if(start_date !== '' && end_date !== '') {
            $.ajax({
                type: 'POST',
                url: '/surewaves_easy_ro/mso_payment/getRoData',
                data: {
                    start_date : start_date,
                    end_date : end_date,
                    networkID : network_id
                },
                beforeSend: function(){},        
                success: function(data){
                    if(data !== 'null'){
                        var object = $.parseJSON(data);
                        for (var n = 0; n < object.values.length; n++) {
                            if(object.values[n].mailStatus != 1){
                            $("#roData").append("<tr><td style='width:40%'>"+ object.values[n].roNumber +"</td>"+
                                                    "<td style='width:30%'><div class='bar'><div class='value' style=width:" + object.values[n].per + "%;>" + object.values[n].per + "%</div></td>" +
                                                    "<td style='text-align:center' id='status_"+ object.values[n].roId + "'><input type='button' class='_button sendmail' id='sendmail_" + object.values[n].roId + "' value='Send Mail' onclick='javascript:sendMail(\"" + object.values[n].roId + "\",\"" + network_id + "\")' /></td>"+
                                                    "<td style='text-align:center' id='view_"+ object.values[n].roId + "'></td>"+
                                                 "</tr>");
                            }else{
                            $("#roData").append("<tr><td style='width:40%'>"+ object.values[n].roNumber +"</td>"+
                                                    "<td style='width:30%'><div class='bar'><div class='value' style=width:" + object.values[n].per + "%;>" + object.values[n].per + "%</div></td>" +
                                                    "<td style='text-align:center' id='status_"+ object.values[n].roId + "'><span>Mail Sent &nbsp;</span></td>"+
                                                    "<td style='text-align:center' id='view_"+ object.values[n].roId + "'><a href='#' onclick='javascript:viewMail(\"" + object.values[n].roId + "\")'>View</a></td>"+
                                                 "</tr>");
                            }
                            
                         }   
                    }else{

                    }
                }
            });
        }
    
    });

    $(".download").click(function(){
        var arrInvoice = new Array();
        $("input:checked").each(function(){
                        arrInvoice.push($(this).val());
        });
        $.ajax({

            type: "POST",
            url: "<?php echo base_url('mso_payment/downloadInvoicePdf');?>",
            data: "InvoiceNo="+arrInvoice,

            success: function(response) {
                var obj = $.parseJSON(response);
                var archiveFile = obj.zip;
                location.href = archiveFile;
            }
            
        });
    }); 
    
});

 function changeDate() {
    var start_date = $("#start_date").val() ;
    var end_date = $("#end_date").val() ;
    if(start_date === '' ){
        start_date = new Date() ;
    }
    if(end_date === '') {
        end_date = new Date() ;
    }
    
    var minEndDate = start_date ;
    var maxEndDate = new Date(start_date);
    maxEndDate.setMonth(maxEndDate.getMonth() + 3);
    
    var maxStartDate = end_date ;
    var minStartDate = new Date(end_date );
    minStartDate.setMonth(minStartDate.getMonth() - 3);
    
    $("#start_date").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        minDate : minStartDate,
        maxDate : maxStartDate,
        onClose: function( selectedDate ) {
            $( "#start_date" ).datepicker( "option" );
        }
    });
    
    $("#end_date").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        minDate : minEndDate,
        maxDate : maxEndDate,
        onClose: function( selectedDate ) {
            $( "#end_date" ).datepicker( "option"  );
        }
    }) ;
 }
 
 function getNetwork() {
   changeDate() ; 
   var start_date = $("#start_date").val() ;
   var end_date = $("#end_date").val() ;
   if(start_date !== '' && end_date !== '') {
       $.ajax({
        type: 'POST',
        url: '/surewaves_easy_ro/mso_payment/getNetwork',
        dataType: 'json',
        beforeSend: function(){
            $("#network_id").html("");
        },        
        success: function(data){
            if(data !== ''){
                $.each(data, function (index, item) {
                    $("#networkId").append("<option value='"+item.customer_id+"'>" + item.customer_name  + "</option>");
                });
            }else{

            }
        }
    });
   }
   
}

function sendMail(roId, networkId)
{
        if(roId !== '' && networkId !== ''){
        $.ajax({
            type : 'POST',
            url  : '/surewaves_easy_ro/mso_payment/sendMail',
            data : {
                    ro_id : roId,
                    network_id :  networkId
                },
            beforeSend : function(){},
            success : function(){
                $('#sendmail_'+roId).remove();
                $('#status_'+roId).html("<span>Mail Sent &nbsp;</span>");
                $('#view_'+roId).html("<a href='#' onclick='javascript:viewMail(\"" + roId + "\")'>View</a>");
            }    
            
        })
        }
        
}

function viewMail(roId){
    
    $.colorbox({href:'<?php echo ROOT_FOLDER ?>/mso_payment/update_mso_payment_record/'+roId,iframe:true, width: '1040px', height:'650px', data:{ro_id:roId}});
    
}
</script>
