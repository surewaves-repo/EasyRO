<?php
/**
 * View to Display 
 */
include_once dirname(__FILE__) . "/../inc/header.inc.php";
?>
<style type="text/css" media="all">
		@import url("/surewaves_easy_ro/css/nw_service.css");
/*		@import url("/surewaves_easy_ro/css/reach_report/style.css");*/
		@import url("/surewaves_easy_ro/css/reach_report/prism.css");
		@import url("/surewaves_easy_ro/css/reach_report/chosen.css");
                @import url("/surewaves_easy_ro/library/jsdatepick-calendar/jsDatePick_ltr.min.css");
		.error_msg_finance,.error_msg_contact{
			color:red;
		}
		.chosen-container {
			margin-left: 11px !important;
			margin-bottom:8px !important;
		}
		.block .block_content{
			overflow:initial !important;
			display:inline-block;
		}
		#language_chosen ul li.search-field .default{
			width:113px !important;
		}
		#genere_chosen ul li.search-field .default{
			width:113px !important;
		}
</style>
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/googleapis_jquery_1_6_4.min.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/enterprise_nw.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/js/datetimepicker.js"></script>
<script type="text/javascript" src="/surewaves_easy_ro/library/jsdatepick-calendar/jsDatePick.min.1.3.js"></script>
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/chosen.jquery.js"></script>
<script  type="text/javascript" src="<?php echo base_url(); ?>js/spot_reach_report/prism.js"></script>

<div id="hld">

	<div class="wrapper">

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

		<div class="block">

			<div class="block_head">
				<div class="bheadl"></div>
				<div class="bheadr"></div>
				<h2>Network Service</h2>
			</div>

<div class="block_content">
	<div class="nw_main">
		<div class="nw_ent">
			<label>Enterprise Name</label>
        	<span>
        		<select name="enterprise" class="select_class" id="enterprise_list">
            		<option value="select">--- select ----</option>
                    <?php
					foreach($all_enterprise as $enterprise){
					?>
                     <option value="<?php echo $enterprise['customer_id'];?>"><?php echo $enterprise['customer_name']; ?></option>
                     <?php
					 }
					 ?>
            	</select>        
        	</span>
			<span>
					<input type="button" style="width: 20%; margin-left: 385px;background: rgba(0, 0, 0, 0) url('../images/excel.png') no-repeat scroll 0 0;cursor:pointer;width:4%;border:none;height:40px;" id="excel_download" name="excel_download" value="" name="">
			</span>
		</div>
    <div class="menu_block">
    	<div id='cssmenu'>
			<ul class="cssmenu_list">
            	<li ><a href='javascript:void(0);' data-href="#edit_contact_details" >Edit Enterprise Contact Details</a></li>
   				<li ><a href='javascript:void(0);' data-href="#edit_finance_details">Edit Enterprise Financial Details</a></li>
                <li ><a href='javascript:void(0);' data-href="#edit_channel_details">Edit Channel Details</a></li>
   		<li ><a href='javascript:void(0);' data-href="#edit_upload_attachment">Upload Attachment</a></li>		   				
			</ul>
		</div>
        <div  class="select_enterprise"> Please Select Enterprise</div>
        <div id="edit_channel_details" class="nw_content" style="display:none">
            <div class="no_channel" style="display: none; color: #ff0000;padding:20px 0 0 83px;">No channel(s) for this enterprise</div>
            <div id="vertical_menu" class="menu_div" style="overflow:hidden; height:428px;">
                <ul class="vertical_menu_ul" style="margin:0 0 0 0">

                </ul>
            </div>
            <div id="dropdown_scroll" class="dropdown_marker Expand" style="display: none;"><span class="pull"></span></div>

            <div class="channel_details">
                <form name="enterprise_channel" action="" id="enterprise_channel" method="post">
                    <span class="contactChannel error_msg_channel" style="display:none; color: #ff0000;float: left;padding: 3px 0 10px 10px;">
                        No contact details for the channel.Kindly Update
                    </span>
					<span class="contact">
                	    <label class="contact_label">Channel Status</label>
          			    <input type="text" name="channel_status" id="channel_status" class="contact_val channel_details" readonly />
                    </span>
					<span class="contact">
                	    <label class="contact_label">Deployment Status</label>
          			    <input type="text" name="channel_deployment_status" id="channel_deployment_status" class="contact_val channel_details" readonly />
                    </span>
            	    <span class="contact">
                	    <label class="contact_label"><span style="color:red">*</span>Contact Person 1</label>
          			    <input type="text" name="channel_contact_person[]" id="channel_contact_person1" class="contact_val channel_details" />
                    </span>
                   <span class="contact">
          	            <label class="contact_label"><span style="color:red">*</span>Email-Id 1</label>
          	            <input type="text" name="channel_email_id[]" id="channel_email_id1" class="email contact_val channel_details">
                   </span>
				   <span class="contact">
                	    <label class="contact_label"><span style="color:red">*</span>Contact Number 1</label>
          			    <input type="text" name="channel_contact_number[]" id="channel_contact_number1" class="contact_val channel_details validateContactNumber" maxlength="11" />
                    </span>
                    <span class="contact">
                	    <label class="contact_label">Contact Person 2</label>
          			    <input type="text" name="channel_contact_person[]" id="channel_contact_person2" class="contact_val channel_details" />
                    </span>
                   <span class="contact">
          	            <label class="contact_label">Email-Id 2</label>
          	            <input type="text" name="channel_email_id[]" id="channel_email_id2" class="email contact_val channel_details">
                   </span>
				   <span class="contact">
                	    <label class="contact_label">Contact Number 2</label>
          			    <input type="text" name="channel_contact_number[]" id="channel_contact_number2" class="contact_val channel_details validateContactNumber" maxlength="11" />
                    </span>
                     <span class="contact">
                	    <label class="contact_label">Contact Person 3</label>
          			    <input type="text" name="channel_contact_person[]" id="channel_contact_person3" class="contact_val channel_details" />
                    </span>
                   <span class="contact">
          	            <label class="contact_label">Email-Id 3</label>
          	            <input type="text" name="channel_email_id[]" id="channel_email_id3" class="email contact_val channel_details">
                   </span>
          		    <span class="contact">
                	    <label class="contact_label">Contact Number 3</label>
          			    <input type="text" name="channel_contact_number[]" id="channel_contact_number3" class="contact_val channel_details validateContactNumber" maxlength="11" />
                    </span>
          		    <span class="contact">
                	    <label class="contact_label"><span style="color:red">*</span>Genre</label>
          			    <!--<input type="text" name="genere" id="genere" class="contact_val channel_details" /> -->
						<select name="genere[]" id="genere" class="common_drpdwn" multiple style="width:61% ;margin-left:3px;" data-placeholder="Select a Genre">
						
						<?php 
							foreach($all_genre as $key => $val){
						?>
								<option value="<?php echo $val['id']; ?>"><?php echo $val['genre']; ?></option>
						<?php
							}
						?>
						</select>
						
                    </span>
					<span class="contact">
                	    <label class="contact_label"><span style="color:red"></span>Dominant Content</label>
          			    <!--<input type="text" name="genere" id="genere" class="contact_val channel_details" /> -->
						<select name="dominant_content[]" id="dominant_content" class="common_drpdwn" multiple style="width:61% ;margin-left:3px;" data-placeholder="Select Dominant Content">
						
						<?php 
							foreach($all_dominantContent as $key => $val){
						?>
								<option value="<?php echo $val['id']; ?>"><?php echo $val['dominant_content']; ?></option>
						<?php
							}
						?>
						</select>
						
                    </span>
                    <span class="contact">
                	    <label class="contact_label"><span style="color:red">*</span>Language</label>
          			    <!--<input type="text" name="language" id="language" class="contact_val channel_details" />-->
						
						<select name="language[]" id="language" class="common_drpdwn" multiple style="width:61%;margin-left:3px;" data-placeholder="Select a Langauge">
						
						<?php 
							foreach($all_language as $key => $val){
						?>
								<option value="<?php echo $val['id']; ?>"><?php echo $val['language']; ?></option>
						<?php
							}
						?>
						</select>
						
                    </span>

          		    <span class="contact">
                	    <label class="contact_label"><span style="color:red">*</span>Address</label>
          			    <textarea type="text" name="channel_address_details" id="channel_address_details" class="address contact_val channel_details" style="width: 60%;height: 86px"></textarea>
          		    </span>

                    <!--<span class="submit_button" id="update_enterprise_channel" style=" width:50px; background:linear-gradient(#36aae7, #1fa0e4) repeat scroll 0 0 rgba(0, 0, 0, 0); padding:12px 20px; cursor:pointer; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; display:block;">Update</span></span>-->
                        <div class="button_save"><input type="button" align="middle" id="update_enterprise_channel" value="Update"  class="blue_btn"></div>

                    <input type="hidden" name="channel_id" id="channel_id" class="enterprise_channel_details" value=""/>
                    <input type="hidden" name="set_enterprise_channel" value="1"  class="contact_val"/>
                    <input type="hidden" name="channel_index" value=""  id="channel_index" class="contact_val"/>
                    <input type="hidden" name="check_enterprise_channel_exist" class="check_enterprise_channel_exist contact_val" value="new" />
                </form>
            </div>

        </div>
    	<div id="edit_finance_details" class="nw_content" style="display:none">
        <form name="enterprise_finance" id="enterprise_finance" method="post" action="">
           <div class="tab_details" id="enterprise_finance">
           		<span class="contact error_msg_finance" style="display:none; padding-bottom: 12px;">
                	
                </span>
          		<span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>Contact Person</label>
          			<input type="text" name="contact_person" id="contact_person" class="contact_val finance_details" />
                </span>
               <span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>Email-Id</label>
          			<input type="text" name="finance_details_email" id="finance_details_email" class="contact_val finance_details" />
                </span>
          		<span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>Contact Number</label>
          			<input type="text" name="contact_number" id="contact_number" class="contact_val finance_details"  maxlength="11"/>
                </span>
          		<span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>Billing Name</label>
          			<input type="text" name="billing_name" id="billing_name" class="contact_val finance_details" />
                </span>
          		<span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>Pan Number</label>
          			<input type="text" name="pan_no" id="pan_no" class="contact_val finance_details" />
                </span>
          		<span class="contact">
                	<label class="contact_label">&nbsp; Service tax no</label>
           			<input type="text" name="service_tax_no" id="service_tax_no" class="contact_val finance_details" />
                </span>
           		<span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>Account No</label>
           			<input type="text" name="acc_no" id="acc_no" class="contact_val finance_details" />
                </span>
           		<span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>Bank Name</label>
           			<input type="text" name="bank_name" id="bank_name" class="contact_val finance_details" />
                </span>
           		<span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>Branch Name</label>
           			<input type="text" name="branch_name" id="branch_name" class="contact_val finance_details" />
                </span>
           		<span class="contact">
                	<label class="contact_label"><span style="color:red">*</span>IFSC Code</label>
           			<input type="text" name="ifsc_code" id="ifsc_code" class="contact_val finance_details" />
                </span>
                <!--<span class="submit_button" id="update_enterprise_finance" data="expand" style="margin:420px  0 0 0px; width:50px; background:linear-gradient(#36aae7, #1fa0e4) repeat scroll 0 0 rgba(0, 0, 0, 0); padding:12px 20px; cursor:pointer; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; display:block;">Update</span>-->
                <div class="button_save"><input type="button" align="middle" id="update_enterprise_finance" value="Update"  class="blue_btn"></div>
          </div>
          <input type="hidden" name="set_enterprise_finance" value="1"  class="contact_val"/>
          <input type="hidden" name="finance_enterprise_id" class="enterpriseId contact_val" value="" />
          <input type="hidden" name="check_enterprise_exist" class="finance_enterprise_exist contact_val" value="new" />

          </form>
        </div>
        <div id="edit_contact_details" class="nw_content" style="display:none">
        <form name="enterprise_contact"  method="post" id="enterprise_contact" action="">
          <div class="tab_details" id="enterprise_contact">
          <span class="contact error_msg_contact" style="display:none; padding-bottom: 12px;">
                	
          </span>
          <span class="contact">
          	<label class="contact_label"><span style="color:red">*</span>Contact Person</label>
          	<input type="text" name="enterprise_contact_person" id="enterprise_contact_person" class="email contact_val contact_details" />
          </span>
          <span class="contact">
          	<label class="contact_label"><span style="color:red">*</span>Email-Id</label>
          	<input type="text" name="email_id" id="email_id" class="email contact_val contact_details" />
          </span>
          <span class="contact">
          	<label class="contact_label"><span style="color:red">*</span>Mobile No</label>
          	<input type="text" name="mobile_no" id="mobile_no" class="mobile contact_val contact_details" maxlength="10" />
          </span>
          <span class="contact">
          	<label class="contact_label">Land Line No</label>
          	<input type="text" name="land_no" id="land_no" class="land_ph contact_val contact_details" maxlength="11" />
          </span>
          <span class="contact">
          	<label class="contact_label">&nbsp; Fax No</label>
          	<input type="text" name="fax_no" id="fax_no" class="fax contact_val contact_details" maxlength="11" />
          </span>
          <span class="contact">
          	<label class="contact_label"><span style="color:red">*</span>Address</label>
          	<textarea type="text" name="address_details" id="address_details" class="address contact_val contact_details" style="width: 50% ! important;"></textarea>
          </span>
              <!--<span class="submit_button" id="update_enterprise_finance" data="expand" style="margin:420px  0 0 0px; width:50px; background:linear-gradient(#36aae7, #1fa0e4) repeat scroll 0 0 rgba(0, 0, 0, 0); padding:12px 20px; cursor:pointer; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; display:block;">Update</span>-->
              <div class="button_save"><input type="button" align="middle" id="update_enterprise_contact" value="Update"  class="blue_btn"></div>
          </div>
          <input type="hidden" name="contact_enterprise_id" class="enterpriseId contact_val" value="" />
          <input type="hidden" name="set_enterprise_contact" value="1"  class="contact_val"/>
          <input type="hidden" name="check_enterprise_exist" class="contact_enterprise_exist contact_val" value="new" />
          </form>
        </div>
        
        <div id="edit_upload_attachment" class="nw_content" style="display:none">
        <form name="upload_attachment" id="upload_attachment" method="post" enctype="multipart/form-data" action="<?php echo ROOT_FOLDER ?>/network_svc_manager/save_enterprise_data">
           <div class="tab_details" id="upload_attachment">
           		<span class="contact error_upload_attachment" style="display:none; padding-bottom: 12px;">
                	
                </span>
          		<span class="contact">
                	<label class="contact_label">Billing Number</label>
                        <input type="text" name="billing_number" id="billing_number" class="contact_val upload_attachment" readonly="readonly"/>
                </span>
               <span class="contact">
                	<label class="contact_label">Same as</label>
                        <input type="text" name="eid" id="eid" class="contact_val upload_attachment" readonly="readonly"/>
                </span>
                <span class="contact">
                    <label class="contact_label">Signed On</label>
                    <input type="text" name="signed_on" id="signed_on" class="contact_val upload_attachment"  readonly="readonly"/>
                </span>
               
                <span class="contact">
                <label class="contact_label">Validity</label>
                <input type="text" name="validity" id="validity" class="contact_val upload_attachment" /> &nbsp;
                <select class="contact_val upload_attachment" style="width:200px" name="validity_values" id="validity_values">
                        <option value="day">days</option>
                        <option value="month">months</option>
                        <option value="year">year</option>
                    </select>
                </span>
                <span class="contact">
                    <label class="contact_label">Document Status</label>
                    <select class="contact_val upload_attachment" name="document_status" id="document_status">
                        <option value="Scanned">Scanned</option>
                    </select>
                </span>
                <span class="contact">
                    <label class="contact_label">Document Status</label>
                    <div class="contact_radio upload_attachment">
                        <input type="radio" name="document_status_value" id="document_status_yes" value="1">Yes
                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="document_status_value" id="document_status_no" style="margin-left:40px" value="0">No
                    </div>
                    
                </span>
           		<span class="contact">
                	<label class="contact_label">Service Tax Document</label>
                        <input type="file" name="service_tax_docs" id="service_tax_docs" class="contact_val upload_attachment file_upload" />
                        <span id="show_service_tax_docs" class = "showDocs" style="display:none"></span>
                </span>
           		<span class="contact">
                	<label class="contact_label">Pan Card</label>
                        <input type="file" name="pan_card" id="pan_card" class="contact_val upload_attachment file_upload"/>
                        <span id="show_pan_card" class = "showDocs" style="display:none"></span>
                </span>
                <span class="contact">
                	<label class="contact_label">Cancelled Cheque</label>
                        <input type="file" name="file_cancelled_cheque" id="file_cancelled_cheque" class="contact_val upload_attachment" />
                        <span id="show_cancelled_chque" class = "showDocs" style="display:none"></span>
                </span>
               
                <span class="contact">
                    <label class="contact_label">ERF</label>
                    <input type="file" name="file_erf" id="file_erf" class="contact_val upload_attachment file_upload" />
                    <span id="show_file_erf" class = "showDocs" style="display:none"></span>
                </span>
               
                <span class="contact">
                    <label class="contact_label">Household Info</label>
                    <input type="file" name="file_household_info" id="file_household_info" class="contact_val upload_attachment" />
                    <span id="show_household_info" class = "showDocs" style="display:none"></span>
                </span>
               
                <span class="contact">
                    <label class="contact_label">Attachment Hard Copies</label>
                    <div class="contact_radio upload_attachment">
                        <input type="radio" name="attachment_hard_copies" id="hard_copies_yes" value="1" >Yes
                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="attachment_hard_copies" id="hard_copies_no" style="margin-left:40px" value="0">No
                    </div>
                    
                </span>
                
                <span class="contact">
                    <label class="contact_label">MediaStation</label>
                    <select class="contact_val upload_attachment" name="media_station" id="media_station">
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                    </select>
                </span>
               
                <div class="button_save"><input type="button" align="middle" id="update_upload_attachment" value="Save"  class="blue_btn"></div>
            </div>
        <input type="hidden" name="customer_id" id="customer_id" value="" />
        <input type="hidden" name="upload_attachment" value="1" />
          </form>
        </div>
       
    </div>	
</div>
</div>
</div>
</div>
</div>
<div style="width: 100%; height: 100%; z-index: 10000000; top: 0px; left: 0px; right: 0px; bottom: 0px; margin: auto; position: fixed; display: none;" id="resultLoading"><div style="width: 250px; height: 75px; text-align: center; position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; margin: auto; font-size: 16px; z-index: 10; color: rgb(255, 255, 255);"><img src="/surewaves_easy_ro/images/waitCursor.gif"><div>loading data.. please wait..</div></div><div class="bg" style="background: none repeat scroll 0% 0% rgb(0, 0, 0); opacity: 0.7; width: 100%; height: 100%; position: absolute; top: 0px;"></div></div>
<form name="post_allEnterpriseDetails" action="<?php echo ROOT_FOLDER ?>/network_svc_manager/generate_allenterprise_details" target="_blank" method="post">
<input type="hidden" name="allEnterpriseDetails" id="allEnterpriseDetails" value="" />
</form>
</body>
</html>

