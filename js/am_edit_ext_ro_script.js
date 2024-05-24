var BASE_URL = '/surewaves_easy_ro';
var hasError =false;
//Validating the form using jquery validation plugin
var validator = $('#edit_ext_ro_form').validate({

    errorClass: 'invalid_error',
    errorElement: 'div',
    errorPlacement: function (error, element) {
        //for positiong the error message after calendar icon
        if (element.attr("id") == "am_edit_txt_ro_date") {
            error.insertAfter($(element).next($('.ro_date')));
        } else if (element.attr("id") == "am_edit_txt_camp_start_date") {
            error.insertAfter($(element).next($('.camp_start_date')));
        } else if (element.attr("id") == "am_edit_txt_camp_end_date") {
            error.insertAfter($(element).next($('.camp_end_date')));
        } else if (element.attr("name").split("_").splice(-1, 1) == 'amount') {

            $('#am_edit_market_error_div').empty();
            $('#am_edit_market_error_div').append(error);
            //error.insertBefore($("#MarketRow"));
        } else if (element.attr("name").split("_").splice(-1, 1) == 'FCT') {
            $('#am_edit_market_error_div').empty();
            $('#am_edit_market_error_div').append(error);
//            error.insertBefore($("#MarketRow"));
        }
        //for other elements
        else {

            error.insertAfter(element);
        }
    },
    rules: {
        am_edit_sel_market: {
            check_market: true,
        },
        am_edit_txt_spcl_inst: {
            check_inst: true
        },
        am_edit_file_pdf: {           
            extension: 'pdf|doc|docx|xls|xlsx|zip|rar',
            check_file_name: true,
            check_file_size: true,
            check_both_file_size:true

        },
        am_edit_client_aproval_mail: {
            extension: 'msg|eml',
            check_attachement_name: true,
            check_attachement_size:true,
            check_both_file_size:true
        },
        am_edit_txt_agency_com: {
            check_com_value: true,
            check_agency_com_value: true,
        }
    },
    //After successful validation of form submithandler event will be triggered
    submitHandler: function (form, event) {
        //To stop the default submit of submit
        event.preventDefault();
        console.log("submitHandler");
        if(hasError)
        {
            return false;
        }
        //creating json
        var payload = am_edit_createObject();
        //ajax call to send form data
        $.ajax(BASE_URL + '/account_manager/post_am_edit_ext_ro', {
            type: "POST",
            data: payload,
            processData: false,
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
                $('#Update_btn').attr("disabled", true);
            },
            contentType: false,
            dataType: 'json',
            success: function (data, textstatus, jqxhr) {
                
                // console.log("data" +JSON.stringify(data));
                if (!data.isLoggedIn) {
                    window.location.href = BASE_URL;
                    return false;
                } else if (data.Status == 'fail') {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    alert("Error in Updating!!");                    
                    $('#Update_btn').attr("disabled", false);

                } else if (data.Status == 'success') {

                    if (Object.keys(data.Data).length == 0) {
                        alert(data.Message);
                        window.location.href = BASE_URL + "/account_manager/home";

                    }
                }

            },
            error:function()
            {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                $('#Update_btn').attr("disabled", false);
                alert("could not update");
            }

        });
    }
});

$(document).ready(function(){
   
    console.log("am_edit_ext_ro");
      //method to check wheter market is selected or not
    $.validator.addMethod("check_market", function (value, element) {
        if ($('#am_edit_sel_market :checked').length <= 0) {
            return false;
        } else {
            return true;
        }
    }, "Please select a market");
    $.validator.addMethod("check_agency_contact", function (value, element) {

        if ($('#sel_agency_contact').val() == "new") {
            return false;
        } else {
            return true;
        }
    }, "Please select agency Contact Name");

    //method to check if special character is there or not in spcl_inst
    $.validator.addMethod("check_inst", function (value, element) {
        var spcl_inst = $('#am_edit_txt_spcl_inst').val();

        function check_input(inst) {
            var objRegExp = /^[^'"]*$/;
            if ((objRegExp.test(inst)) && (inst.length > 0)) {
                return true;
            } else {
                return false;
            }
            return true;
        }

        if (spcl_inst != "" && !check_input(spcl_inst)) {
            return false;
        } else {
            return true;
        }
    }, "Invalid input for special instructions");

    //method to check wheter there is special character or not in file_name
    $.validator.addMethod("check_file_name", function (value, element) {
        file_path = $('#am_edit_file_pdf').val().replace(/^C:\\fakepath\\/i, '');
        file_regex = /[^a-zA-Z0-9 ._-]/;
        if (file_regex.test(file_path)) {

            return false;
        } else {
            return true;
        }


    }, "Special Characters are not allowed in RO attachements");

    //method to check wheter there is special character or not in the attach_ro
    $.validator.addMethod("check_attachement_name", function (value, element) {
        file_path = $('#am_edit_client_aproval_mail').val().replace(/^C:\\fakepath\\/i, '');
        file_regex = /[^a-zA-Z0-9 ._-]/;
        if (file_regex.test(file_path)) {

            return false;
        } else {
            return true;
        }


    }, "Special Characters are not allowed in client attachements");

    

    //check whether the spot amount is zero or not
    $.validator.addMethod("check_for_zero_spot", function (value, element) {
        var id = $(element).attr('id');
        var splitId = id.split("_");
        var length = splitId.length;
        var market_id = splitId.splice(-length, length - 2);
        market_id = market_id.join('_');
        var spotamount = $("#" + market_id + "_spot_amount").val();

        var spotfct = $("#" + market_id + "_spot_FCT").val();
        if ((spotamount != 0 && spotfct == 0) || (spotamount == 0 && spotfct != 0)) {
            return false;
        } else {
            return true;
        }
    }, "Enter all value for spot");

    //check wheter the banner amount and banner fct are zero or not
    $.validator.addMethod("check_for_zero_banner", function (value, element) {
        var id = $(element).attr('id');
        var splitId = id.split("_");
        var length = splitId.length;
        var market_id = splitId.splice(-length, length - 2);
        market_id = market_id.join('_');
        var banneramount = $("#" + market_id + "_banner_amount").val();
        var bannerfct = $("#" + market_id + "_banner_FCT").val();
        if ((banneramount != 0 && bannerfct == 0) || (banneramount == 0 && bannerfct != 0)) {
            return false;
        } else {
            return true;
        }
    }, "Enter all value for banner ");

    //check wheter all the values of market are zero or not
    $.validator.addMethod("check_for_zero", function (value, element) {
        var id = $(element).attr('id');
        var splitId = id.split("_");
        var length = splitId.length;
        var market_id = splitId.splice(-length, length - 2);
        market_id = market_id.join('_');
        var spotamount = $("#" + market_id + "_spot_amount").val();
        var spotfct = $("#" + market_id + "_spot_FCT").val();
        var banneramount = $("#" + market_id + "_banner_amount").val();
        var bannerfct = $("#" + market_id + "_banner_FCT").val();
        if (spotamount == 0 && spotfct == 0 && banneramount == 0 && bannerfct == 0) {
            return false;
        } else {
            return true;
        }
    }, "Both spot and banner value cannot be zero");

    $.validator.addMethod("check_file_size",function(value,element){
        let retrunValue=true;
        if($('#am_edit_file_pdf')[0].files.length > 0)
        {
                if($('#am_edit_file_pdf')[0].files[0].size > 18000000)
                {
                    retrunValue=false;
                }
                else
                {
                    retrunValue=true;
                }
        }
        else
        {
            retrunValue=true;
        }
        return retrunValue;
    },"RO file size is greater than 18mb");

    $.validator.addMethod("check_attachement_size",function(value,element){
        let returnValue=true;
        if( $('#am_edit_client_aproval_mail')[0].files.length > 0)
        {
                if(($('#am_edit_client_aproval_mail')[0].files[0].size) > 18000000)
                {
                    returnValue=false;
                }
                else
                {
                    returnValue=true;
                }
        }
        else
        {
            returnValue=true;
        }
        return retrunValue;
    },"Client Approval Mail file size is greater than 18mb");

    $.validator.addMethod("check_both_file_size",function(value,element){
        
        if($('#am_edit_file_pdf')[0].files.length > 0 && $('#am_edit_client_aproval_mail')[0].files.length > 0)
        {
                if(($('#am_edit_file_pdf')[0].files[0].size + $('#am_edit_client_aproval_mail')[0].files[0].size) > 18000000)
                {
                    $('#am_edit_file_error').css('display','contents');
                    hasError=true;
                    return true;
                }
                else
                {
                    $('#am_edit_file_error').css('display','none');
                    hasError=false;
                    return true; 
                }
        }
        else
        {
            return true;
        } 

    },"");
    

    //check wheter the amount entered is correct or not
    $.validator.addMethod("check_amount", function (value, element) {

        var regExp = /^(\d*\.)?\d+$/;
        if (regExp.test(value)) {
            return true;
        } else {
            return false;
        }

    }, "Invalid input");

    //check wheter the fct value entered is correct or not
    $.validator.addMethod("check_fct", function (value, element) {
        var regExp = /^[0-9]+$/;
        if (regExp.test(value)) {
            return true;
        } else {
            return false;
        }

    }, "Enter only positive integer ");

    //Check wheter the value entered is in correct format
    $.validator.addMethod("check_com_value", function (value, element) {

        var regex = /^\d+\.\d{1,2}$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }


    }, "Enter positive decimal value upto 2 digit places");

    //check wheter the value entered is less than the gross value or not
    $.validator.addMethod("check_agency_com_value", function (value, element) {
        var agency_com = value;
        var gross = $("#am_edit_txt_gross").val();


        var com = 0;
        com = agency_com;
        

        if (Number(com) > Number(gross)) {
            return false;
        } else {
            return true;
        }
    }, "Commission cannot be greater than gross amount");

    $('#Update_btn').click(function(){
        
        $("#edit_ext_ro_form").validate();
        $('#sel_agency_contact').rules('add', {
            check_agency_contact: true
        })

        var selected_market=$('#am_edit_sel_market :checked');
      //adding rules to dynamically created market and banner
            
        $.map(selected_market, function (element) {
                market_name = $(element).val();
                market_id = market_name.split(" ").join("_");
                $("#ext_ro_form").validate();
                $("#" + market_id + "_spot_amount").rules("add", {
                    //rules for spot_amount
                    required: true,
                    check_amount: true,
                    check_for_zero_spot: true,
                    check_for_zero: true,
                    messages: {
                        required: "Enter 0 or some other value of spot amount for " + market_name,
                        check_amount: "Enter correct value of spot amount for " + market_name,
                        check_for_zero_spot: "Enter  spot amount or spot fct for " + market_name,
                        check_for_zero: "Both spot and banner cannot be zero for " + market_name
                    }

                });
                $('#' + market_id + '_spot_FCT').rules("add", {
                    //rules for spot_fct
                    required: true,
                    check_fct: true,
                    check_for_zero_spot: true,
                    check_for_zero: true,
                    messages: {
                        required: "Enter 0 or some other value of spot fct for " + market_name,
                        check_fct: "Enter correct value of spot fct for " + market_name,
                        check_for_zero_spot: "Enter spot amount or spot fct for " + market_name,
                        check_for_zero: "Both spot and banner cannot be zero for " + market_name
                    }
                });
                $("#" + market_id + "_banner_amount").rules("add", {
                    //rules for banner amount
                    required: true,
                    check_amount: true,
                    check_for_zero_banner: true,
                    check_for_zero: true,
                    messages: {
                        required: "Enter 0 or some other value of banner amount for " + market_name,
                        check_amount: "Enter correct value of banner amount for " + market_name,
                        check_for_zero_banner: "Enter  banner amount or banner fct for " + market_name,
                        check_for_zero: "Both spot and banner cannot be zero for " + market_name
                    }
                });

                $('#' + market_id + '_banner_FCT').rules("add", {
                    //rules for banner FCT
                    check_fct: true,
                    check_for_zero_banner: true,
                    check_for_zero: true,
                    messages: {
                        required: "Enter 0 or some other value of banner fct for " + market_name,
                        check_fct: "Enter correct value of banner amount for " + market_name,
                        check_for_zero_banner: "Enter banner amount or banner fct for " + market_name,
                        check_for_zero: "Both spot and banner cannot be zero for " + market_name
                    }
                });

                

            });

    });
    

    $('#am_edit_Order_history_recipient_ids').multiselect();
   
    $('#sel_agency_contact').change(function () {
        buildMailIds();
    });
    
    $('#sel_client_contact').change(function () {
        buildMailIds();
    });

    buildMailIds();

    $("#am_edit_txt_ro_date").datepicker(
        //options
        {
            changeMonth: true,
            dateFormat: 'yy-mm-dd',
            maxDate: '15',
            minDate: '-1m',
            onSelect: function () {
            //    validator.element("#txt_ro_date");
            }


        }
    );
    //opening datepicker when clicking on calendar icon having class ro_date
    $('.am_edit_ro_date').click(function () {
        $("#am_edit_txt_ro_date").focus();
    });

    //Datepicker for Campaign Start Date
    $('#am_edit_txt_camp_start_date').datepicker({
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        maxDate: '+1m',
        minDate: '+2',
        //Changing the minDate to the selected Date
        onSelect: function () {
           // validator.element("#txt_camp_start_date");
            var date = $(this).datepicker('getDate');
            $('#am_edit_txt_camp_end_date').datepicker("option", "minDate", date);

        },

    });
    //opening datepicker when clicking on calendar having class camp_start_date
    $('.am_edit_camp_start_date').click(function () {
        $("#am_edit_txt_camp_start_date").focus();
    });

    //Datepicker for campaign end date
    $('#am_edit_txt_camp_end_date').datepicker(
        //options
        {
            changeMonth: true,
            minDate: '+2',
            dateFormat: 'yy-mm-dd',
            onSelect: function () {
             //   validator.element("#txt_camp_end_date");
            }

        }
    );

    //opening datepicker when clicking on calendar having class camp_end _date
    $('.am_edit_camp_end_date').click(function () {
        $('#am_edit_txt_camp_end_date').focus();
    });

    $("#sub_modal_btn").click(function () {

        $('#create_ext_Ro_modal').modal('hide');
    });

    $('#am_edit_add_edit_agency_contact_span').click(function () {
            var selected_agency = $('#am_edit_txt_agency').val();
            var selected_agency_contact = $('#sel_agency_contact').val();
            if (selected_agency_contact == 'new') 
            {
                $('#sub_modal_title').text('Add Agency Contact');
            } else {
                $('#sub_modal_title').text('Update Agency Contact');
            }
            $.ajax(BASE_URL + '/account_manager/add_edit_agency_contact', {
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");

                },
                complete: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");

                },
                data: {
                    "sel_agency": selected_agency,
                    "sel_agency_contact": selected_agency_contact,
                },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    if (!data.isLoggedIn) {
                        window.location.href = BASE_URL;
                        return false;
                    } else if (data.Status == 'success') {
                        $('#create_ext_Ro_modal').modal('show');
                        $("#create_ext_Ro_modal").data('bs.modal')._config.backdrop = 'static';
                        var trimData = $.trim(data.Data.html);
                        var parseHtml = $.parseHTML(data.Data.html);
                        $('<link>').attr('rel', 'stylesheet')
                            .attr('type', 'text/css')
                            .attr('href', '/surewaves_easy_ro/css/add_edit_agency_contact_style.css')
                            .appendTo('head');

                        $('#sub_modal_body').html(parseHtml);
                        $.getScript('/surewaves_easy_ro/js/add_edit_agency_contact.js').done(function () {

                        }).fail(function () {
                            }
                        );
                    }


                }

            });

        

    });


    $("#am_edit_add_edit_client_contact_span").click(function () {
        
            var selected_client = $('#am_edit_txt_client').val();
            var selected_client_contact = $("#sel_client_contact").val();
            if (selected_client_contact == 'new') {
                $('#sub_modal_title').text('Add Client Contact');
            } else {
                $('#sub_modal_title').text("Update Client Contact");
            }
            $.ajax(BASE_URL + '/account_manager/add_edit_client_contact', {
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");

                },
                complete: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");

                },
                data: {
                    "sel_client": selected_client,
                    "sel_client_contact": selected_client_contact
                },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    if (!data.isLoggedIn) {
                        alert('Session Expired, Please Login');
                        window.location.href = BASE_URL;
                        return false;
                    } else if (data.Status == 'success') {
                        $('#create_ext_Ro_modal').modal('show');
                        $("#create_ext_Ro_modal").data('bs.modal')._config.backdrop = 'static';
                        var trimData = $.trim(data.Data.html);
                        var parseHtml = $.parseHTML(data.Data.html);
                        $('<link>').attr('rel', 'stylesheet')
                            .attr('type', 'text/css')
                            .attr('href', '/surewaves_easy_ro/css/add_edit_client_contact_style.css')
                            .appendTo('head');
                        $('#sub_modal_body').html(parseHtml);
                        $.getScript('/surewaves_easy_ro/js/add_edit_client_contact.js').done(function () {

                        }).fail(function () {
                            }
                        );
                    }


                }

            });
        
    });


    var am_edit_market_array = new Array();
    var am_edit_market_val;

    //multiselect for market
    $('#am_edit_sel_market').multiselect({
        maxHeight: 200,
        buttonWidth: 220,
        includeSelectAllOption: true,
        onDropdownHide: function () //When dropdown closes then this event is triggered and it will add row for Various Market selected
        {
            //validating if user selected any market or not

            var selected_market = $("#am_edit_sel_market :checked");
            $('#am_edit_market_tr').show();
            table_html = '<tr id="MarketRow"><td colspan="8" nowrap="nowrap" class="no_border">Market wise RO Amount <span style="color:darkblue;">(If you enter amount \'0\' for both spot and banner for a market then it will be discarded)</span></td></tr>' +
                '<tr><th  style="text-align: center;width:15%" class="no_border">Market Name</th><th width="0.1%" style="width:0.1%;border-top:none">&nbsp;</th><th style="width:15%" class="no_border">Spot Amount</th><th style="width:15%" class="no_border">Spot Fct</th><th style="width:15%" class="no_border">Spot Rate</th><th style="width:15%" class="no_border">Banner Amount</th><th style="width:15%" class="no_border">Banner Fct</th><th style="width:15%" class="no_border">Banner Rate</th></tr>';

            //creating rows for no. of markets
            $.map(selected_market, function (element) {
                market_name = $(element).val();
                market_id = market_name.split(" ").join("_");
                market_val_spot = $('#' + market_id + '_spot_amount').val();
                market_val_banner = $('#' + market_id + '_banner_amount').val();
                am_edit_market_array[market_id + '_spot_amount'] = market_val_spot;
                am_edit_market_array[market_id + '_banner_amount'] = market_val_banner;

                /********************** Adding code for FCT */

                market_val_spot_FCT = $('#' + market_id + '_spot_FCT').val();
                market_val_banner_FCT = $('#' + market_id + '_banner_FCT').val();

                am_edit_market_array[market_id + '_spot_FCT'] = market_val_spot_FCT;
                am_edit_market_array[market_id + '_banner_FCT'] = market_val_banner_FCT;

                /******************** Adding code for FCT */

                /*code added for rates*/
                market_val_spot_rate = $('#' + market_id + '_spot_rate').val();
                market_val_banner_rate = $('#' + market_id + '_banner_rate').val();

                am_edit_market_array[market_id + '_spot_rate'] = market_val_spot_rate;
                am_edit_market_array[market_id + '_banner_rate'] = market_val_banner_rate;
                /************Spot Amount*******/
                if (am_edit_market_array[market_id + '_spot_amount'] != null) {

                    table_html += '<tr padding="4px">';
                    table_html += '<td class="no_border" nowrap="nowrap"  style="border-bottom:none;padding:0px;width:15%;text-align:center">' + market_name + '<span style="color:#F00;"> *</span></td><td width="0.1%" nowrap="nowrap" class="no_border"  style="border-bottom:none;padding:0px;"> : </td><td class="no_border" width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;width:10%"><input type="text" id="' + market_id + '_spot_amount" name="' + market_id + '_spot_amount" style="width:90px; margin-bottom: 10px;margin-right: 20px;" onblur="price_check_for_each_market(\'' + market_id + '_spot_amount\',this.value);" value="' + am_edit_market_array[market_id + '_spot_amount'] + '" /></td>';
                } else {

                    table_html += '<tr padding="4px">';
                    table_html += '<td class="no_border" nowrap="nowrap" width="14%" style="border-bottom:none;width:15%;padding:0px;text-align:right">' + market_name + '<span style="color:#F00;"> *</span></td><td width="0.1%" nowrap="nowrap" class="no_border"  style="border-bottom:none;padding:0px;"> : </td><td width="10%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;width:10%;"><input type="text" id="' + market_id + '_spot_amount" name="' + market_id + '_spot_amount"  style="width:90px; margin-bottom: 10px;margin-right: 20px;" onblur="price_check_for_each_market(\'' + market_id + '_spot_amount\',this.value);"  value="0" /></td>';
                }
                /************Spot Fct***********/
                if (am_edit_market_array[market_id + '_spot_FCT'] != null) {
                    table_html += '<td width="14%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;width:15%"><input type="text" id="' + market_id + '_spot_FCT" name="' + market_id + '_spot_FCT" style="width:90px; margin-bottom: 10px;margin-right: 20px;" onblur="validateFCTNumber(\'' + market_id + '_spot_FCT\',this.value)" value="' + am_edit_market_array[market_id + '_spot_FCT'] + '" /></td>';
                } else {
                    table_html += '<td width="14%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;width:15%"><input type="text" onblur="validateFCTNumber(\'' + market_id + '_spot_FCT\',this.value)" id="' + market_id + '_spot_FCT" name="' + market_id + '_spot_FCT" style="width:90px;margin-bottom: 10px;margin-right: 20px;"  value="0" /></td>';
                }
                /**************Spot Rate*********/
                if (am_edit_market_array[market_id + '_spot_rate'] != null) {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:15%"><input type="text" id="' + market_id + '_spot_rate" name="' + market_id + '_spot_rate" readonly style="width:90px; margin-bottom: 10px;margin-right: 20px;"  value="' + am_edit_market_array[market_id + '_spot_rate'] + '" /></td>';
                } else {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:15%"><input type="text"  id="' + market_id + '_spot_rate" name="' + market_id + '_spot_rate" readonly style="width:90px; margin-bottom: 10px;margin-right: 20px;"  value="0" /></td>';
                }
                /****************Banner Amount*******/
                if (am_edit_market_array[market_id + '_banner_amount'] != null) {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:15%"><input type="text" id="' + market_id + '_banner_amount" name="' + market_id + '_banner_amount" style="width:90px; margin-bottom: 10px;margin-right: 20px;" onblur="price_check_for_each_market(\'' + market_id + '_banner_amount\',this.value);" value="' + am_edit_market_array[market_id + '_banner_amount'] + '" /></td>';
                } else {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:15%"><input type="text" id="' + market_id + '_banner_amount" name="' + market_id + '_banner_amount" onblur="price_check_for_each_market(\'' + market_id + '_banner_amount\',this.value);" style="width:90px; margin-bottom: 10px;margin-right: 20px;"  value="0" /></td>';
                }
                /************Banner FCT********/
                if (am_edit_market_array[market_id + '_banner_FCT'] != null) {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:15%"><input type="text" id="' + market_id + '_banner_FCT" name="' + market_id + '_banner_FCT" style="width:90px; margin-bottom: 10px;margin-right: 20px;" onblur="validateFCTNumber(\'' + market_id + '_banner_FCT\',this.value)" value="' + am_edit_market_array[market_id + '_banner_FCT'] + '" /></td>';
                } else {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:15%"><input onblur="validateFCTNumber(\'' + market_id + '_banner_FCT\',this.value)" type="text" id="' + market_id + '_banner_FCT" name="' + market_id + '_banner_FCT" style="width:90px; margin-bottom: 10px;margin-right: 20px;"  value="0" /></td>';

                }
                /***********Banner rate*******/
                if (am_edit_market_array[market_id + '_banner_rate'] != null) {
                    table_html += '<td width="14%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;width:15%"><input type="text" id="' + market_id + '_banner_rate" name="' + market_id + '_banner_rate" readonly style="width:90px; margin-bottom: 10px;margin-right: 20px;"  value="' + am_edit_market_array[market_id + '_banner_rate'] + '" /></td>';
                    table_html += '</tr>';
                } else {
                    table_html += '<td width="14%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;width:15%"><input type="text" id="' + market_id + '_banner_rate" name="' + market_id + '_banner_rate" readonly style="width:90px; margin-bottom: 10px;margin-right: 20px;"  value="0" /></td>';
                    table_html += '</tr>';
                }


            });
            table_html += '</tr>';
            if (selected_market.length == 0) {
                $("#am_edit_market_tr").hide();
            } else {
                $("#am_edit_market_div").html(table_html);
            }

            

            if (selected_market.length == 0) {//alert(1)
                $('#am_edit_txt_gross').val('0');
                $('#am_edit_txt_agency_com').val('0');
                $('#am_edit_txt_net_agency_com').val('0');
            } else {
                // calculate all amount
                var gross = 0;
                var gross_for_each_market;
                var commission;

                //selecting all input element whose name ends with "amount" and calculating the value of gross
                $("input[name$='amount']").each(function () {
                    gross_for_each_market = parseFloat(this.value);
                    gross += gross_for_each_market;
                    
                });
                commission = parseFloat($('#am_edit_txt_agency_com').val());
                //console.log("commission "+commission); 
                commission = commission.toFixed(2);
                //console.log("commission "+commission);
                if (Number(commission) > gross) {
                    $('#am_edit_txt_agency_com').val("0");
                    commission = 0;
                }
                if (isNaN(gross - commission)) {
                    $('#am_edit_txt_net_agency_com').val('0');
                } else {
                    net_agency_com = (gross - commission).toFixed(2);
                    $('#am_edit_txt_net_agency_com').val(net_agency_com);
                }
                $('#am_edit_txt_gross').val(gross.toFixed(2));
            }


        }
    });
})

function price_check_for_each_market(id, value) {
    var gross = value;
    //console.log("gross "+gross);
    var splitId = id.split("_");
    var isBannerOrSpot = splitId.splice(-2, 1);
    //console.log("isBannerOrSpot "+isBannerOrSpot);
    var removeamount = splitId.splice(-1, 1);
    //console.log("removeamount "+removeamount);
    var marketName = splitId.join("_");
    //console.log('marketName '+marketName);
    var fct = Number($('#' + marketName + '_' + isBannerOrSpot + '_FCT').val());
     //console.log("fct "+fct);
    if (fct != 0 && !isNaN(fct)) {
        setRate(marketName, gross, fct, isBannerOrSpot);
    }
    var gross = 0;
    var gross_for_each_market;
    var commission = 0;

    $("input[name$='amount']").each(function () {

        gross_for_each_market = parseFloat(this.value);
        gross += gross_for_each_market;
    });
    gross = Number(gross).toFixed(2);
    if (isNaN(gross)) {
        $('#am_edit_txt_gross').val('0');
    } else {
        $('#am_edit_txt_gross').val(gross);
    }

    var agency_com_entered = Number($('#am_edit_txt_agency_com').val());
  // console.log("agency_com_entered "+agency_com_entered);
    if (agency_com_entered > 0) {
        commission = agency_com_entered;
        commission = commission.toFixed(2);
        console.log("commission "+commission);
        console.log("gross "+gross);
        if (Number(commission) > Number(gross)) {

            //console.log("inside");
            $('#am_edit_txt_agency_com').val("0.00");
            commission = 0;
            return false;
        }
    }
    console.log("commission "+commission);

    var net_agency_com = (gross - commission).toFixed(2);
    $('#am_edit_txt_agency_com').val(commission);
    if (isNaN(gross)) {
        $('#am_edit_txt_net_agency_com').val('0');
    } else {
        $('#am_edit_txt_net_agency_com').val(net_agency_com);
    }


}

//getting rate
function validateFCTNumber(id, value) 
{


    var fctValue = $('#' + id).val();
    var splitId = id.split("_");
    var isBannerOrSpot = splitId.splice(-2, 1);
    var removeamount = splitId.splice(-1, 1);
    var marketName = splitId.join("_");
    var gross = Number($('#' + marketName + '_' + isBannerOrSpot + '_amount').val());
    if (fctValue != 0 && !isNaN(fctValue)) {
        setRate(marketName, gross, fctValue, isBannerOrSpot);
    } else {
        $('#' + marketName + '_' + isBannerOrSpot + '_rate').val('0');
    }

}

//to get the rate for banner and amount based on fct and amount
function setRate(marketName, amount, fct, condition) {
    //console.log("marketName " + marketName);
    //console.log("amount " + amount);
    var foundRate = amount * 10 / fct;
    //console.log("foundRate ", foundRate);
    foundRate = foundRate.toFixed(2);
    $('#' + marketName + '_' + condition + '_rate').val(foundRate);
}

//to calculate the commision value and net agency commission
function calculate_net_after_commission() {

    var value = $('#am_edit_txt_agency_com').val();
    var gross = Number($('#am_edit_txt_gross').val());
    
    value = Number(value);
    if(value<0 && isNaN(value))
    {
        $('#am_edit_txt_agency_com').val('0.0');
        return false;
    }
    if (gross > 0) {
        var net_agency_com = (gross - value).toFixed(2);
        $('#am_edit_txt_net_agency_com').val(net_agency_com);
     
    }
    else
    {
        $('#am_edit_txt_net_agency_com').val('0.0');
    }
   
}




function buildMailIds() {

    var selected_agency_contact = $('#sel_agency_contact').val();
    var selected_client_contact = $('#sel_client_contact').val();
    var agenctContactIds = selected_agency_contact;
    var clientContactIds = selected_client_contact;

    $.ajax(BASE_URL + '/account_manager/getMailIdsUsingContactIds', {
        type: "POST",
        async: true,
        beforeSend: function () {
            $('#loader_background').css("display", "block");
            $('#loader_spin').css("display", "block");
        },
        complete: function () {
            $('#loader_background').css("display", "none");
            $('#loader_spin').css("display", "none");

        },
        data: {agenctContactIds: agenctContactIds, clientContactIds: clientContactIds},
        async: true,
        success: function (data) {
            $('#loader_background').css("display", "none");
            $('#loader_spin').css("display", "none");
            $('#Order_history_recipient_ids').empty();
            var id_json = JSON.parse(data);
            if (data.trim() != "null") {

                $.map(id_json.mailId, function (value, index) {

                    $('#am_edit_Order_history_recipient_ids').append('<option value=' + value.id + '>' + value.id + '</option>');

                });
                //Reloading the multiselect
                $('#am_edit_Order_history_recipient_ids').multiselect('rebuild');

            }
        }
    });
}

//retrieve the value of form and create jsonobject
function am_edit_createObject() {
    //creating formdata object
    var form_data = new FormData();
    var ext_ro_value = $("#am_edit_txt_ext_ro").val();
    form_data.append("txt_ext_ro", ext_ro_value);
    var ro_date = $("#am_edit_txt_ro_date").val();
    form_data.append("txt_ro_date", ro_date);
    var agency_value = $("#am_edit_txt_agency").val();
    form_data.append("txt_agency", agency_value);
    var agency_contact_value = $('#sel_agency_contact').val();
    form_data.append("sel_agency_contact", agency_contact_value);
    var client_value = "";
    if ($('#am_edit_txt_client').val() != null) {
        client_value = $('#am_edit_txt_client').val();
    }
    form_data.append("txt_client", client_value);
    var client_contact_value = "";
    if ($('#sel_client_contact').val() != "new") {
        client_contact_value = $('#sel_client_contact').val();
    }
    form_data.append("sel_client_contact", client_contact_value);
    var make_good_value = $("input[name='am_edit_rd_make_good']:checked").val();
    form_data.append("rd_make_good", make_good_value);
    var selected_brand = $("#am_edit_txt_brand").text();
    form_data.append("txt_brand", selected_brand);
    var am_name = $("#am_edit_txt_am").val();
    form_data.append("txt_am_name", am_name);
    var camp_start_date = $("#am_edit_txt_camp_start_date").val();
    form_data.append("txt_camp_start_date", camp_start_date);
    var camp_end_date = $("#am_edit_txt_camp_end_date").val();
    form_data.append("txt_camp_end_date", camp_end_date);
    var region = "";
    if ($("#am_edit_regionSelectBox").attr('data') != null) {
        region = $("#am_edit_regionSelectBox").attr('data');
    }
    form_data.append("regionSelectBox", region);
    var market_obj = {};
    var market_arr = [];
    var market_name_arr = [];
    var selected_market = $("#am_edit_sel_market :checked");
    $.map(selected_market, function (element) {
        var market_name = $(element).val();
        market_name_arr.push(market_name);
        var market_id = market_name.split(' ').join('_');
        var market_spot_amount = $("#" + market_id + "_spot_amount").val();
        var market_spot_FCT = $("#" + market_id + "_spot_FCT").val();
        var market_spot_rate = $("#" + market_id + "_spot_rate").val();
        var market_banner_amount = $("#" + market_id + "_banner_amount").val();
        var market_banner_FCT = $("#" + market_id + "_banner_FCT").val();
        var market_banner_rate = $("#" + market_id + "_banner_rate").val();
        market_obj["marketName"] = market_name;
        market_obj["spotAmount"] = market_spot_amount;
        market_obj["spotFCT"] = market_spot_FCT;
        market_obj["spotRate"] = market_spot_rate;
        market_obj["bannerAmount"] = market_banner_amount;
        market_obj["bannerFCT"] = market_banner_FCT;
        market_obj["bannerRate"] = market_banner_rate;
        market_arr.push(JSON.stringify((market_obj)));
    });

    form_data.append("markets", '[' + market_arr + ']');
    var gross_value = $("#am_edit_txt_gross").val();
    form_data.append("txt_gross", gross_value);
    var commission_value = $("#am_edit_txt_agency_com").val();
    form_data.append("txt_agency_com", commission_value);
    var net_agency_value = $("#am_edit_txt_net_agency_com").val();
    form_data.append("txt_net_agency_com", net_agency_value);
    var spcl_inst_value = "";
    if ($("#am_edit_txt_spcl_inst").val() != null) {
        spcl_inst_value = $("#am_edit_txt_spcl_inst").val();
    }
    form_data.append("txt_spcl_inst", spcl_inst_value);
    var mail_arr = [];
    var selected_mail = $("#am_edit_Order_history_recipient_ids :checked");
    $.map(selected_mail, function (element) {
        mail_arr.push($(element).val());
    });
    form_data.append("Order_history_recipient_ids", mail_arr);
    if($('#am_edit_file_pdf')[0].files.length==0)
    {
        form_data.append("ro_attach_edit",0);
        var url=$('#attach_ro_id').attr('href');
        form_data.append("file_pdf",url);
      
   }
    else
    {
        form_data.append("ro_attach_edit",1);
        var ro_file = $("input[name='am_edit_file_pdf']")[0].files[0];
        form_data.append("file_pdf", ro_file);
        
    }
    if($('#am_edit_client_aproval_mail')[0].files.length==0)
    {
        form_data.append("client_approval_edit",0);
        var url=$('#client_approval_file').attr('href');
        form_data.append("client_approval_mail",url);
    }
    else
    {
        form_data.append("client_approval_edit",1);
        var mail_file = $("input[name='am_edit_client_aproval_mail']")[0].files[0];
        form_data.append("client_approval_mail", mail_file);
        
    }
    var user_id = $('#am_edit_hid_user_id').val();
    form_data.append("hid_user_id", user_id);
    return form_data;
}
