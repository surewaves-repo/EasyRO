<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>
	
	
			
			<div class="block small center login" style="margin:0px">		
				
				
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>					
					<h2>Edit  EasyRO User</h2>					
				</div>	
				
				<div class="block_content">
					
					<form action="<?php echo ROOT_FOLDER ?>/admin/post_edit_user" method="post">
						
						<p>
							<label>User Name: <?php echo form_error('user_name'); ?></label> <br />
							<input type="text" class="text" name="user_name" value="<?php echo set_value('user_name',$user_details['user_name']); ?>" />
							
						</p>
						<p>
							<label>Mobile: <?php echo form_error('user_phone'); ?> </label> <br />
							<input type="text" class="text" name="user_phone" value="<?php echo set_value('user_phone',$user_details['user_phone']); ?>" />
						</p>
                                                <!--<p>
							<input type="radio" checked="checked" class="radio" name="user_type" value="0" <?php echo set_radio('user_type', '0', $user_details['is_test_user'] == '1'); ?>/> <label>Normal User</label>
                            <input type="radio" class="radio" name="user_type" value="1" <?php echo set_radio('user_type', '1', $user_details['is_test_user'] == '1'); ?>/> <label>Test User</label>
							<input type="radio" class="radio" name="user_type" value="2" <?php echo set_radio('user_type', '2', $user_details['is_test_user'] == '2'); ?>/> <label>Advanced RO User</label>
						</p>-->
						<p>
							<label>Profile: <?php echo form_error('profile'); ?></label> <br />

                            <select name="profile_id" id="profile_id" style="	background: rgb(254, 254, 254) none repeat scroll 0 0;border: 1.5px solid rgb(187, 187, 187);height: 33px;width: 428px;border-radius: 3px;">

                                <?php foreach($profiles as $profile) {

                                    $selected = '' ;
                                    if( $profile['profile_id'] == $user_details['profile_id'] ){

                                        $selected = "selected" ;
                                    } ?>

                                    <option value="<?php echo $profile['profile_id']?>" <?php echo $selected ; ?> ><?php echo $profile['profile_name']?></option>

                                <?php
                                } ?>

                            </select>

							<input  type="hidden" name="user_id" value="<?php echo $user_details['user_id']; ?>" /> 
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
    var selectedReportingManager = <?php echo $selectedReportingManager ; ?> ;

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

                if( userId == selectedReportingManager ){

                    $('#'+ id ).append("<option value='"+ userId + "' selected >"+ userName +"</option>") ;

                }else{

                    $('#'+ id ).append("<option value='"+ userId + "'>"+ userName +"</option>") ;

                }
            }
        }

        $( '#'+ id ).val($("#" + id + "option:first").val());
        $( '#'+ id ).trigger("chosen:updated");
        return true ;
    }

</script>
		
