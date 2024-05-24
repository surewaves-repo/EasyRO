
<?php include_once dirname(__FILE__)."/inc/header.inc.php" ?>

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

		<div class="block center login">
			

                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>

                    <h2>Update Market Price</h2>
                </div>
	
				<div class="block_content">
					
					<form action="<?php echo ROOT_FOLDER ?>/update/post_update_market_price" method="post" >
						<table>
							<tr>
                            	<td>Select RO:</td>
                            	<td>Gross:</td>
                                <td>Agency Commission:</td>
                            </tr> 
                            <tr>
                            	<td>
                                    <select name="sel_ro" id="sel_ro" style="width:600px;height:30px" onchange="javascript:check_invoice_status(this.value)">

                                        <option value="-">-</option>
                                        <?php foreach($ro_list as $value) { ?>
                                        <option value="<?php echo $value['id'] ?> "><?php echo $value['cust_ro'] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                	<input class="text" style="width:150px;" type="text" name="txt_gross" id="txt_gross" readonly="readonly" />
                                </td>
                                <td>
                                    <input class="text" style="width:150px;" type="text" name="txt_agency_com" id="txt_agency_com" />
                                </td>
                            </tr>
						</table>

                        <div id="invoice_error" style="display: none;text-align: center;">
                            <span style="color:#F00;">Invoice already Generated for RO.</span>
                        </div>
						<div id="date_error" style="display: none;text-align: center;">
                            <span style="color:#F00;">Update can only be done during the campaign start month</span>
                        </div>
						<div id="market_div" style="display:none;">
                        </div>
						<!-- <p>
							<label>Billing Name:</label><br />
							<input type="text" name="billing_name" class="text" value="" id="billing_name"/>
						</p> -->
                        <p>
                            <label>Remarks:<span style="color:#F00;"> *</span></label><br />
                            <input type="text" name="txt_remarks" class="text" value="" id="txt_remarks"/>
                        </p>

                        <p>
							<input type="submit"  class="submit" value="Update" onclick="return update_market_price();"/>								
							<input type="hidden" id="additional_remark" value=""/>
						</p>
					</form>	
						
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			
			
					
			

		</div>						<!-- wrapper ends -->
		
	</div>		<!-- #hld ends -->
	<script type="text/javascript" >
	$("#sel_ro").change(function(){
		$.post("<?php echo ROOT_FOLDER ?>/update/get_ro_data",{ id: $("#sel_ro").val()},
             function(data) {
				data = data.split('~');//alert(data[1])
				$( "#txt_gross" ).val( data[0] );
                 $( "#txt_agency_com" ).val( data[1] );
				$( "#market_div" ).show();
				$( "#market_div" ).html( data[2] );
				if(data[3] == 0) {
					$("#date_error").hide() ;
					$(".submit").show();
				}else{
					$("#date_error").show() ;
					$(".submit").hide();					
				}
        });
		
    });

    function check_invoice_status(ro_id){
        $.ajax({
            type: 'POST',
            url: '/surewaves_easy_ro/update/get_invoice_status_for_ro',
            async: false,
            data: {
                ro_id: ro_id
            },
            dataType: 'json',
            success: function(data){
                if(data == '1'){
                    $("#invoice_error").show();
                    $("#additional_remark").val('Invoice Generated');
					$(".submit").hide();
                }else{
                    $("#invoice_error").hide();
                    $("#additional_remark").val('');
					$(".submit").show();
                }
            }
        });
    }

    function recalculate_gross_amount(){
        var total_gross = 0;
        $(".amount").each(function(){
            total_gross += parseFloat(this.value);
        });
        $("#txt_gross").val(total_gross);
    }

	function update_market_price(){
		//alert($('#txt_gross').val());
		/*var total_gross = 0;
		var chk_form = true;
		$(":text[name^='markets']").each(function (){
			if(this.value == ""){
				alert('Please enter valid amount');
				chk_form = false;
			}
			amount = parseFloat(this.value);
			total_gross += amount;
		});//alert('outside'+count)
		if(!chk_form){
			return false;
		}//alert(total_gross)
		if(parseFloat(total_gross) != parseFloat($( "#txt_gross" ).val())){
			//alert(parseFloat(total_gross));
			//alert(parseFloat($( "#txt_gross" ).val()));
			alert('Summation of all amounts should match with gross amount');
			return false;
		}*/
		//return false;

        var remark = $("#txt_remarks").val();
        if(remark == ''){
            alert('Please enter some remarks');
            $('#txt_remarks').focus();
            return false;
        }

        /*var reg1 = /^[\w\-\s]+$/;
        if ((!reg1.test(remark)) && (remark.length > 0) ){
            alert("Invalid input for remarks");
            $('#txt_remarks').focus();
            return false;
        }*/
	}
	</script>
