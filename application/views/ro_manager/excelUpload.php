<!-- colorbox pop up for bulk upload and updating payments through CSV  -->
<?php include_once dirname(__FILE__)."/../inc/header.inc.php" ?>    
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/jquery-1.7.2.min.js" type="text/javascript"></script>


            
            <div class="block small center login" style="margin:0px; width:100%;">
                
                
                <div class="block_head">
                    <div class="bheadl"></div>
                    <div class="bheadr"></div>                  
                    <h2>Upload Payment Details</h2>                 
                </div>  
                
                <div class="block_content">
                <form action="<?php echo ROOT_FOLDER ?>/mso_payment/uploadMsoPaymentCsv" method="post" id="update" enctype="multipart/form-data">
                    <div id="show_error"></div>
                    <div id="show_error"></div>
                    <table cellpadding="0" cellspacing="0" width="100%" id="details">
                        <tr>
                            <td width="30%">Select File</td>
                            <td width="5%"> : </td>
                            <td width="25%">
                                <input type="File" id="csv_file" name="csv_file" style="width:220px;" value=""   />
                            </td>
                            <td width="40%">&nbsp;</td>
                        </tr>
                        <tr>    
                            <td> 
                               <input type="submit" class="submit" value="Submit" id="submit_details" onclick="return validateExtenstion()" />    
                            </td>                       
                        </tr>
                    
                        
                        
                    </form>
                </div>      <!-- .block_content ends -->
                    
                <div class="bendl"></div>
                <div class="bendr"></div>
                                
            </div>      <!-- .login ends -->
 <script type='text/javascript'>
 
 /* *
  * Function to Validate uploading only CSV files
  */
 
    function validateExtenstion() 
    {
        myFile = document.getElementById("csv_file").files[0];
        var fileName = myFile.name;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
        if (ext != "csv") {
            $('#show_error').empty();
            $('#show_error').append("<div style='color:red'>Only CSV files allowed. Please upload again.</div>");
            return false;
        } 
    }
 </script>
     