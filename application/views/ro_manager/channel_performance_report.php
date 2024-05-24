
<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/flexigrid.css">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/flexigrid.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<style>
</style>
<div id="hld">

    <div class="wrapper">		<!-- wrapper begins -->

        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG"  style="height:35px;width:150px;padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <input type="hidden" id="profile_id" value="<?php echo $profile_id?>" />
            <?php echo $menu ?>

            <p class="user">Hello <?php echo $logged_in_user['user_name'] ?> | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a></p>
        </div>		<!-- #header ends -->

        <div class="block">
            <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>

                <form action="<?php echo ROOT_FOLDER ?>/ro_manager/download_channel_performance_csv/" method="post" enctype="multipart/form-data" style="float: left;">
                    
                        <label for="month">from :</label>
                        <input type="text" readonly="readonly" id="from_date" name="from_date" />
                        <span class="fa fa-calendar"></span>
                        &nbsp;&nbsp;
						<label for="month">To :</label>
                        <input type="text" readonly="readonly" id="to_date" name="to_date" disabled />
                        <span class="fa fa-calendar"></span>
                        &nbsp;&nbsp;
                        

                        <input type="submit" id="request_report" value="Export to CSV" onclick="return check_form()">
                   
                </form>

            </div>
            <div class="block_content">
                <table id="flexitable"></table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function get_serialize_value(obj){
        var p = [];

        var nw = $('#network_id');
        var df = $('#month');

        p.push({name: nw.attr('name'),value: nw.val()});
        p.push({name: df.attr('name'),value: df.val()});
        return p;
    }

    function refresh_grid(obj){
        $("#flexitable").flexOptions({params: get_serialize_value(obj)});
        $('#flexitable').flexOptions({newp: 1}).flexReload();
    }

    $("#export_csv").click(function() {
        from_date = $("#month").val();
        window.location.href = "<?php echo ROOT_FOLDER ?>/ro_manager/download_campaign_performance_csv/"+from_date;
    });

   


    function check_form(){
        var from_date 	= $("#from_date").val();
		var to_date 	= $("#to_date").val();
    
		
         if(from_date == ""){
            alert("Please select a Month");
            $("#from_date").focus();
            return false;
        }else if(to_date == ""){
            alert("Please select a Month");
            $("#from_date").focus();
            return false;
        }else{
            
            return true;
        }
    }

    $("#from_date").datepicker({
        minDate: getMinDate(),
        maxDate: '0',
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
		onClose: function(selectedDate) {
			$("#to_date").datepicker("option", "minDate", selectedDate);
			if(selectedDate.length > 0){
				$("#to_date").attr('disabled',false);
			}
			//$("#to_date").datepicker({})//;
		}
       
    });
    $("#to_date").datepicker({       
        maxDate: getMaxDate(),
        changeMonth: true,
        dateFormat: 'yy-mm-dd'
    });
    function getMinDate(){
     var today = new Date();
     var fiscalyear = new Date(today);
  		if ((today.getMonth() + 1) <= 3) {    		
			fiscalyear.setDate(1);
			fiscalyear.setMonth(3);
			fiscalyear.setFullYear(today.getFullYear() - 1); 
  		} else {
			fiscalyear.setDate(1);
			//fiscalyear.setMonth(3);       
			fiscalyear.setMonth(3); //That year from january
			fiscalyear.setFullYear(today.getFullYear() - 1);
			return fiscalyear;   			
  		}
		return fiscalyear;
    }
    function getMaxDate(){
			var today = new Date();
			var last_date = null;			
			last_date = new Date(today.getFullYear(), today.getMonth() + 1, 0);	
			return last_date;
    }
</script>
