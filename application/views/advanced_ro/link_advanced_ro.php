<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 2/11/15
 * Time: 10:18 AM
 */
?>

<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
<style type="text/css">
	.block form select.sw-select {
		width: 245px;
		height: 33px;
		margin-right: 20px;
		padding: 7px;
		display: inline-block;
		margin-right: 20px;
		border: 1px solid #bbb;
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
	}
</style>
<link rel="stylesheet" type="text/css" href="/surewaves_easy_ro/css/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" />

<div class="block small center login" style="margin:0px; width:702px;">


    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Link Advanced External RO</h2>
    </div>

    <div class="block_content">
        <form action="<?php echo ROOT_FOLDER ?>/advanced_ro_manager/post_link_advanced_ext_ro" method="post" enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="30%">External RO Number<span style="color:#F00;"> *</span></td>
                    <td width="5%"> : </td>
                    <td width="25%">
                        <select name="sel_ext_ro" id="sel_ext_ro" class="sw-select" onchange="javascript:get_linked_advanced_ros(this.value)">
                            <option value="">--Select RO--</option>
                            <?php foreach($cust_ros as $cro){?>
                                <option value="<?php echo $cro['id']?>"><?php echo $cro['cust_ro']?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td width="40%"><span id="ext_ro_error" style="color:red;display:none">The external ro already exists</span>
                        <input type="hidden" id="ext_ro_error_flag" value="0">
                    </td>
                </tr>

                <tr>
                    <td width="20%">Linked Advanced Ros</td>
                    <td width="5%"> : </td>
                    <td width="25%">
                        <div id="linked_ros" style="overflow-y: scroll; height:100px;width:242px;border:1px solid #bbb">

                        </div>
                    </td>
                    <td width="50%"><!--<a href="javascript:add_brand()">Add Brand</a>-->
                        <!--<span id="ro_error" style="color:red;display:none">Please Select Advanced RO</span>-->
                    </td>
                </tr>

                <tr>
                    <td width="20%">Advanced RO Number<span style="color:#F00;"> *</span></td>
                    <td width="5%"> : </td>
                    <td width="25%">
                        <select name="sel_adv_ro[]" id="sel_adv_ro" style="width:248px;" multiple="multiple">
                            <?php foreach($advanced_ros as $aro){?>
                                <option value="<?php echo $aro['id']?>"><?php echo $aro['cust_ro']?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td width="50%"><!--<a href="javascript:add_brand()">Add Brand</a>-->
                        <span id="ro_error" style="color:red;display:none">Please Select Advanced RO</span>
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="submit" class="submit" value="Link Ro" onclick="return check_form();" />
                    </td>
                <tr>

            </table>
        </form>
    </div>		<!-- .block_content ends -->

    <div class="bendl"></div>
    <div class="bendr"></div>


</div>	<!-- .login ends -->
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/jquery.multiselect.js"></script>
<script type="application/javascript">
    $("select#sel_adv_ro")
	.filter(".single")
	.multiselect({
		multiple: false,
		noneSelectedText: 'Please select a radio',
		header: false
	})
	.end()
	.not(".single")
        .multiselect({
            noneSelectedText: 'Please select advanced RO'
        });
    $("select#sel_adv_ro").multiselect("refresh");

    function check_form(){
        if($('#sel_adv_ro').val() == null){
            $('#ro_error').show();
            $('#sel_adv_ro').focus();
            return false;
        }
    }
    function get_linked_advanced_ros(ro_id){
        $("#sel_adv_ro option:selected").removeAttr("selected");
        $("select#sel_adv_ro").multiselect("refresh");
        $.ajax({
            type: "POST",
            data: 'cust_ro_id='+ro_id,
            async: false,
            url: "<?php echo ROOT_FOLDER ?>/advanced_ro_manager/get_linked_advanced_ros",
            success:function(data){
                //alert(data);
                if(data == 'No Result'){
                    var list_box = 'No Advanced RO Linked';
                }else{
                    var data = JSON.parse(data);
                    var list_box = '<ul>';
                    for(var i in data)
                    {
                        var adv_ro = data[i].cust_ro;
                        var adv_ro_id = data[i].advance_ro_id;
                        list_box += '<li>'+adv_ro+'</li>';

                        $('#sel_adv_ro option[value='+adv_ro_id+']').attr('selected', 'selected');
                        $("select#sel_adv_ro").multiselect("refresh");
                    }
                    list_box += '</ul>';
                }
                $('#linked_ros').html(list_box);
            }
        });
    }
</script>


