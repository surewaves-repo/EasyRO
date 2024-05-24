
<?php include_once dirname(__FILE__)."/inc/header.inc.php" ?>
	<style>
	.cmf-skinned-select{
	position: absolute !important;
	margin-left: 4px !important;
	}
	</style>
	<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->

        <div id="header">
            <div class="hdrl"></div>
            <div class="hdrr"></div>

            <h1 style="margin-right:10px"><img src="<?php echo ROOT_FOLDER ?>/images/EasyRO-Logo.PNG" width=150px; height=35px; style="padding-top:10px;"/></h1>
            <img src="<?php echo ROOT_FOLDER ?>/images/Surewaves.png" style="padding-top:10px;float:right;padding-left:40px;"/>

            <?php echo $menu; ?>

            <p class="user">
                Hello, <?php echo $logged_in_user['user_name'] ?>
                | <a href="<?php echo ROOT_FOLDER ?>/ro_manager/logout">Logout</a>
            </p>
        </div>

		<div class="block center login">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>SureWaves Enterprise Email Entry</h2>
					<!--<img src="<?php /*echo ROOT_FOLDER */?>/images/EasyR0-logo-Black.PNG" width="200" height="35px"style="float:right; padding-top:13px;" />-->

				</div>		<!-- .block_head ends -->
				
	
				<div class="block_content">
					
					<form action="<?php echo ROOT_FOLDER ?>/update/update_customer_email" method="post" >
						<p style="padding-top: 15px">
							<label>Enterprise Name:</label>
							<select class="styled" name="cid" id="cid" >
								<option value="-">-</option>
								<?php foreach($customer_detail as $c_value) { ?>
								<option value="<?php echo $c_value['customer_id'] ?> "><?php echo $c_value['customer_name'] ?></option>
								<?php } ?>
							</select>
						</p>
						
						<!-- <p>
							<label>Billing Name:</label><br />
							<input type="text" name="billing_name" class="text" value="" id="billing_name"/>
						</p> -->
						<p style="padding-top: 10px">
							<label style="padding-right: 76px">Email:</label>
							<span style="color:#990000;font-weight:normal;font-size:10px;display:none" id="show_email_error"></span>
							<input type="text" name="customer_email" class="text" value="" id="customer_email"/>
                        </p>
                        <p style="padding-top: 10px">    (Enter Multiple Email IDs separated by a comma)
                        </p>

						<p style="padding-top: 15px">
							<input type="submit"  class="submit" value="Update" onclick="return verify_email();"/>								
							
						</p>
					</form>	
						
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->
			
			
			<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Customer Details</h2>
					
				</div>		<!-- .block_head ends -->
				
				
				
				<div class="block_content" style="width:96.5%;; height:270px; overflow:auto;>
					<form action="" method="post">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<th>Customer Name</th>
								<th>Email</th>				
							</tr>
							<?php foreach($customer_detail as $c_value) { ?>
							<tr>
								<td> <?php echo $c_value['customer_name'] ?></td>
								<td> <?php echo $c_value['customer_email'] ?></td>
							</tr>	
							<?php } ?>
						</table>
						<!--<div class="paggination right">
							<?php echo $page_links ?>
						</div>	-->	<!-- .paggination ends -->	
					</form>	
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .block ends -->
			

		</div>						<!-- wrapper ends -->
		
	</div>		<!-- #hld ends -->
	<script type="text/javascript" >
		$("#cid").change(function(){
        //$.post("<?php echo ROOT_FOLDER ?>/update/get_billing_name",{ cid: $("#cid").val()},
		$.post("<?php echo ROOT_FOLDER ?>/update/get_customer_email",{ cid: $("#cid").val()},
             function(data) {
            //$( "#billing_name" ).val( data );
			$( "#customer_email" ).val( data );
        });
		
    });
	function verify_email() {
		var email_values = $('#customer_email').val() ;
		var emails = email_values.split(",") ;
		for(var i=0;i<emails.length;i++) {
			if(! validateEmail(emails[i])) {
				//$("show_email_error").innerHTML = "Please Enter Correct Email Values";
				//Element.show("show_email_error");
				//$("customer_email").focus();
				alert("Please Enter Correct Email Values") ;
				return false;
			}
		}			
	}
	function validateEmail(str) {
		if ( str.indexOf("<") >=0 && str.indexOf(">") >= 0 ) {
			str = str.substring(str.indexOf("<")+1, str.indexOf(">"));
			//alert(str);
		}
		//var objRegExp  = /^[A-Za-z0-9]+[\w.-]*?[A-Za-z0-9]+@[A-Za-z0-9]+[\w]*?\.[A-Za-z0-9]\.[A-Za-z0-9]{2,5}$/;

		var objRegExp  = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+){1}(.[a-z]{2,3})$/;
  		return objRegExp.test(str);
	}
	</script>
