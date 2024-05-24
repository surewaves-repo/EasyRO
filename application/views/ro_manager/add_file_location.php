<html>
<head>
    <script>
      
        $(document).on('click','.applybtn',function(e){
            console.log("clicked");
          var arr=e.currentTarget.id.split('_');
          arr.splice(-1,1);
          var market_text_id=arr.join('_');
          console.log("market_text_id "+market_text_id);
          var market_link=$('#'+market_text_id+'_text').val();
          console.log("market_link "+market_link);
          $.map($("input[class^='"+market_text_id+"']"),function(value){
                
                    if($('#'+value.id).is(':checked'))
                    {
                        var channel_ar=value.id.split('_');
                        channel_ar.splice(-1,1);
                        var channel_ar_id=channel_ar.join('_');
                        console.log("channel_ar_id "+channel_ar_id);
                        $('#'+channel_ar_id+'_text').val(market_link);
                    }
                
          }) 
        
        })

        

        $(document).on('change','.check',function(e){
            console.log("changed");
            if(!$('#'+e.currentTarget.id).is(':checked'))
            {
                 var arr=e.currentTarget.id.split('_');
                 arr.splice(-1,1);
                 channel_ar_id=arr.join('_');
                 console.log("channel_ar_id "+channel_ar_id);
                 $('#'+channel_ar_id+'_text').prop('disabled',false);
                 $('#'+channel_ar_id+'_text').val('');

            }
            else
            {
                 var arr=e.currentTarget.id.split('_');
                 arr.splice(-1,1);
                 channel_ar_id=arr.join('_');
                 console.log("channel_ar_id "+channel_ar_id);
                 
                 console.log({'arr':arr});
                 arr.splice(-1,1);
                 arr.splice(-1,1);
                 console.log({'arr':arr});
                 var market_id=arr.join('_');
                 console.log("market_id "+market_id);
                 console.log("channel_id "+channel_ar_id);
                 var market_text=$('#'+market_id+'_text').val();
                 console.log('market_text '+market_text);
                 $('#'+channel_ar_id+'_text').val(market_text);
                 $('#'+channel_ar_id+'_text').prop('disabled',true);
            }
        })

        $('#add_location_btn').click(function(){

            var data={};
            var exit=0;
            $.each($("input[name^='location']"),function(index,value){
                if($('#'+value.id).val()==="")
                {
                    alert("Enter value for "+$('#'+value.name).text());
                    exit=1;
                    return false;
                }
            })
            if(exit===0)
            {
                $.map($( "input[name^='location']" ),function(value){
                data[value.name]=$('#'+value.id).val();
            })
            data['hid_internal_ro']=$('#hid_intenal_ro').val();
            data['hid_id']=$('#hid_id').val();
            data['hid_edit']=$('#hid_edit').val();
            $.ajax(BASE_URL+'/ro_manager/post_add_file_location',{
                type:'POST',
                beforeSend:function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                },
                data:data,
                success:function(data)
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    if(data.Status == 'success') {
                        alert(data.Message);
                        $('#dateModal').modal('hide');
                    } else {
                        alert('Something Went Wrong!!');
                    }
                },
                error:function()
                {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert("file could not be saved");
                }

            })
            }
            
        })
    </script>
</head>
<body>
<?php if($value == 0) { ?>
    <div class="block_content">
        <h2 style="text-align:center;font-weight:200;font-size:16px;">Schedule is not prepared due to lack of Inventory</h2>
    </div>
<?php } else { ?>
    <div class="block_content">
        <form id='add_file_location_form' >
          
                
                <?php foreach($channels as $mkt=>$val) { ?>
                 <div data-toggle='collapse'  class='market_priority_div' data-target='.<?php echo implode('_',explode(' ',$mkt));?>_div'>
                 <h2 style='line-height:37px;'><?php echo $mkt;?></h2>
                 <i id='network_i' class='down'></i>    
                 </div>
                 <div class='collapse <?php echo implode('_',explode(' ',$mkt)) ;?>_div'>
                 <table class="table" style="border-left:1px solid grey;border-right:1px solid grey;border-bottom:1px solid grey;cellpadding:0;cellspacing:0;">
                    <tr>
                        <td>File Location for selected Channel(s)</td>
                        <td><input type='text' id='<?php echo implode('_',explode(' ',$mkt));?>_text'><input type="button" style="margin-left:8px;"  id='<?php echo implode('_',explode(' ',$mkt));?>_btn' class="btn btn-sm btn-info applybtn" value="Apply"></td>
                    </tr>
                    <?php foreach($val as $chnls) {?>
                    
                       <tr>
                         <td id="location_<?php echo $chnls['channel_id'];?>"><input style="margin-left:12px;margin-right:8px;" class='<?php echo implode('_',explode(' ',$mkt));?>_class check'  id='<?php echo implode('_',explode(' ',$mkt));?>_<?php echo $chnls['channel_id'];?>_channel_cb' type='checkbox' checked='checked'><?php echo $chnls['channel_name'];?></td>
                         <td><input type='text' id='<?php echo implode('_',explode(' ',$mkt));?>_<?php echo $chnls['channel_id'];?>_channel_text' name="location_<?php echo $chnls['channel_id'];?>" value='<?php echo $chnls['file_location'];?>' disabled="disabled"></td>
                       </tr> 
                    
                <?php }?>
                </table>                           
                </div>
                <?php } ?>

            

            <input type="hidden" name="hid_internal_ro" id='hid_intenal_ro' value="<?php echo $internal_ro; ?>">
            <input type="hidden" name="hid_edit" id='hid_edit' value="<?php echo $edit ?>">
            <input type="hidden" name="hid_id" id='hid_id' value="<?php echo $id ?>">
        
            <p style="display:flex;justify-content:center;margin-top:10px;">
                <input type="button" id='add_location_btn' class="submitlong btn btn-sm btn-info" name="add" value="Add Location" />
            </p>
        </form>
    </div><!-- .block_content ends -->



<?php } ?>

</body>
</html>
		

					

      <!-- .login ends -->