<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
	
	
			
			<div class="block small center login" style="margin:0px">		
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Add EasyRO User</h2>					
				</div>	
				
				<div class="block_content">
					
					<form action="<?php echo ROOT_FOLDER ?>/admin/post_add_user" method="post">
						
						<p>
							<label>User Name: <?php echo form_error('user_name'); ?></label> <br />
							<input type="text" class="text" name="user_name" value="<?php echo set_value('user_name'); ?>" />
							
						</p>
						<p>
							<label>Email:<?php echo form_error('user_email'); ?></label> <br />
							<input type="text" class="text" name="user_email" value="<?php echo set_value('user_email'); ?>" />
						</p>
						
						<p>
							<label>Mobile: <?php echo form_error('user_phone'); ?> </label> <br />
							<input type="text" class="text" name="user_phone" value="<?php echo set_value('user_phone'); ?>" />
						</p>
                                                <p>
							<input type="radio" checked="checked" class="radio" name="user_type" value="0"/> <label>Normal User</label>
                            <input type="radio" class="radio" name="user_type" value="1"/> <label>Test User</label>
							<input type="radio" class="radio" name="user_type" value="2"/> <label>Advanced RO User</label>
						</p>
						<p>
							<label>Profile: <?php echo form_error('profile'); ?></label> <br />
							<select name="profile_id" id="profile_id" style="	background: rgb(254, 254, 254) none repeat scroll 0 0;border: 1.5px solid rgb(187, 187, 187);height: 33px;width: 428px;border-radius: 3px;">
								<?php foreach($profiles as $profile) { ?>
									<option value="<?php echo $profile['profile_id']?>" <?php if ( set_value('profile_id') == $profile['profile_id']) {?>selected="selected"<? } ?>><?php echo $profile['profile_name']?></option>
								<?php } ?>
							</select>
						</p>
                        <p>
                            <label>Reporting Manger: </label> <br />
                            <select name="reporting_manager" id="reporting_manager" style="	background: rgb(254, 254, 254) none repeat scroll 0 0;border: 1.5px solid rgb(187, 187, 187);height: 33px;width: 428px;border-radius: 3px;" >
                            </select>
                        </p>
                        <p>
                            <input type="hidden" id="regions" name="regions" value="" />
                        </p>
						<p>
							<input type="submit" class="submit" value="submit" /> 
							
						</p>
					</form>
					
				</div>		<!-- .block_content ends -->
					
				<div class="bendl"></div>
				<div class="bendr"></div>
								
			</div>		<!-- .login ends -->

    <script type="application/javascript">

        var regionsJson = <?php echo $regionsJson; ?> ;
        var reportingManagersJson = <?php echo $reportingManagersJson; ?> ;

        $(document).ready(function(){

            $("#profile_id").trigger("change") ;

        });

        $("#profile_id").change(function(){

            var profileId = $("#profile_id").val() ;
            var regionIds = 0 ;

            // setting regions according to profile

            if( profileId == 10 || profileId == 1 ){

                regionIds = getJsonFieldsId( regionsJson ) ;

            }

            $("#regions").val( regionIds ) ;

            //setting reporting manager according to profile

            if( profileId != 10 ){
                $('#reporting_manager' ).find('option').remove()

            }else{

                buildOption( "reporting_manager", reportingManagersJson, profileId ) ;
            }

        }) ;

        function getJsonFieldsId( json ){

            var tempArray = [] ;

            for( var i= 0; i< json.length; i++ ){

                 tempArray.push( json[i].id) ;
            }

            return tempArray.join(",") ;
        }

        function buildOption( id, json, selProfileId ){

            $('#'+ id ).find('option').remove() ;

            for( var i= 0; i< json.length; i++ ){

               var userId   =  json[i].user_id ;
               var userName =  json[i].user_name ;
               var profileId = json[i].profile_id ;

                if( profileId != selProfileId ){

                    $('#'+ id ).append("<option value='"+ userId + "'>"+ userName +"</option>") ;

                }
            }

            $( '#'+ id ).val($("#" + id + "option:first").val());
            $( '#'+ id ).trigger("chosen:updated");
            return true ;
        }

    </script>
		
