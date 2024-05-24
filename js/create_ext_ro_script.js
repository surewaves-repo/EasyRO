var BASE_URL = '/surewaves_easy_ro';
var hasError= false;
//Validating the form using jquery validation plugin
var validator = $('#ext_ro_form').validate({

    errorClass: 'invalid_error',
    errorElement: 'div',
    errorPlacement: function (error, element) {
        //for positiong the error message after calendar icon
    
        if (element.attr("id") == "txt_ro_date") {
            error.insertAfter($(element).next($('.ro_date')));
        } else if (element.attr("id") == "txt_camp_start_date") {
            error.insertAfter($(element).next($('.camp_start_date')));
        } else if (element.attr("id") == "txt_camp_end_date") {
            error.insertAfter($(element).next($('.camp_end_date')));
        } else if (element.attr("name").split("_").splice(-2, 1) == 'amount') {

            $('#market_error_div').empty();
            $('#market_error_div').append(error);
            //error.insertBefore($("#MarketRow"));
        } else if (element.attr("name").split("_").splice(-2, 1) == 'FCT') {
            $('#market_error_div').empty();
            $('#market_error_div').append(error);
//            error.insertBefore($("#MarketRow"));
        }
        //for other elements
        else {

            error.insertAfter(element);
        }
    },
    rules: {
        txt_ext_ro: {
            required: true,
            check_ro_existence: true
        },
        txt_ro_date: {
            required: true,
        },
        sel_agency: {
            check_agency: true,
        },
        sel_client: {
            check_client: true,
        },
        txt_gstin: {
            required: true,
            validate_gst: true
        },
        sel_brand: {
            check_brand: true
        },
        txt_camp_start_date: {
            required: true,
        },
        txt_camp_end_date: {
            required: true,
        },
        regionSelectBox: {
            required: true
        },
        sel_market: {
            check_market: true,
        },
        txt_spcl_inst: {
            check_inst: true
        },
        file_pdf: {
            required: true,
            extension: 'pdf|doc|docx|xls|xlsx|zip|rar',
            check_file_name: true,
            check_ro_file_size:true,
            check_both_file_size:true
        },
        client_aproval_mail: {
            required: true,
            extension: 'msg|eml',
            check_attachement_name: true,
            check_approval_mail_size:true,
            check_both_file_size:true
        },
        txt_agency_com_holder: {
            check_com_value: true,
            check_agency_com_value: true,
        }
    },
    messages: {
        txt_ext_ro:
            {
                required: "Please enter external Ro",
            },
        txt_ro_date: {
            required: "Please enter Ro Date",
        },
        txt_gstin: {
            required: "Please enter gst",
        },
        txt_camp_start_date: {
            required: "Please enter Campaign start date"
        },
        regionSelectBox: {
            required: "No region for account manager"
        },
        txt_camp_end_date: {
            required: "Please enter Campaign end date"
        },
        file_pdf: {
            required: "Please select a File"
        },
        client_aproval_mail: {
            required: "Please select Client Approval Mail Attachment"
        },

    },
    //After successful validation of form submithandler event will be triggered
    submitHandler: function (form, event) {
        //To stop the default submit of submit
        event.preventDefault();
        if(hasError)
        {
            return false;
        }
        //creating json
        var payload = createObject();
        //ajax call to send form data
        $.ajax(BASE_URL + '/account_manager/post_create_ext_ro', {
            type: "POST",
            data: payload,
            processData: false,
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
                $('#create_btn').attr("disabled", true);
            },
            contentType: false,
            dataType: 'json',
            success: function (data, textstatus, jqxhr) {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                // console.log("data" +JSON.stringify(data));
                if (!data.isLoggedIn) {
                    window.location.href = BASE_URL;
                    return false;
                } else if (data.Status == 'fail') {
                    $('#create_ext_ro_error_div').empty();
                    $('#create_btn').attr("disabled", false);

                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#txt_ext_ro_error').empty();
                    $('#txt_ro_date_error').empty();
                    $('#sel_agency_error').empty();
                    $('#sel_agency_contact_error').empty();
                    $('#sel_client_error').empty();
                    $('#sel_client_contact_error').empty();
                    $('#sel_brand_error').empty();
                    $('#regionSelectBox_error').empty();
                    $('#txt_camp_start_date_error').empty();
                    $('#txt_camp_end_date_error').empty();
                    $('#markets_error').empty();
                    $('#txt_gross_error').empty();
                    $('#txt_agency_com_error').empty();
                    $('#file_pdf_error').empty();
                    $("#client_aproval_mail_error").empty();
                    $('#txt_net_agency_com_error').empty();
                    $('#loading_imag').css("display", "none");
                    $('#create_btn').attr("disabled", false);
                    $('#txt_ext_ro').removeClass('invalid_error');
                    $('#txt_ro_date').removeClass('invalid_error');
                    $('#sel_agency').removeClass('invalid_error');
                    $('#sel_agency_contact').removeClass('invalid_error');
                    $('#sel_client').removeClass('invalid_error');
                    $('#sel_client_contact').removeClass('invalid_error');
                    $('#sel_brand').removeClass('invalid_error');
                    $('#regionSelectBox').removeClass('invalid_error');
                    $('#txt_camp_start_date').removeClass('invalid_error');
                    $('#txt_camp_end_date').removeClass('invalid_error');
                    $('#sel_market').removeClass('invalid_error');
                    $('#txt_gross').removeClass('invalid_error');
                    $('#txt_agency_com_holder').removeClass('invalid_error');
                    $('#file_pdf').removeClass('invalid_error');
                    $("#client_aproval_mail").removeClass('invalid_error');
                    $('#txt_net_agency_com').removeClass('invalid_error');
                    $('#add_edit_agency_error_div').empty();

                    if (Object.keys(data.Data).length > 0) {

                        if ("formValidation" in data.Data) {

                            $.map(data.Data.formValidation, function (value) {
                                Object.keys(value).forEach(function eachKey(key) {
                                    var id = "#" + key;

                                    if (id == '#txt_agency_com') {
                                        $(id + '_error').append('<label class="invalid_error" style="margin-left: -264px;">' + value[key] + '</label>');
                                        $('#txt_agency_com_holder').addClass('invalid_error');
                                    } else {
                                        $(id + '_error').append('<label class="invalid_error">' + value[key] + '</label>');
                                        $(id).addClass('invalid_error');
                                    }
                                });
                            });
                        }
                    } else {

                        $('#create_ext_ro_error_div').append('<label class="invalid_error">' + data.Message + '</label>')
                        $('#create_ext_ro_error_div').css("display", 'block');
                    }

                } else if (data.Status == 'success') {
                    if (Object.keys(data.Data).length == 0) {
                        alert(data.Message);
                        window.location.href = BASE_URL + "/account_manager/home";

                    }
                }

            },

        });
    }
});

$('document').ready(function () {
    var market_array = new Array();
    var market_val;

    $("#myModal").on("scroll", function () {
        $("#ui-datepicker-div").css("display", "none");
        $('.hasDatepicker').blur();
    });

    $('.modal-body').on("scroll", function () {
        $("#ui-datepicker-div").css("display", "none");
        $('.hasDatepicker').blur();
    });

    $('.modal-body').on("scroll", function () {
        $(".dropdown-menu").removeClass('show');
        $(".dropdown-menu").removeClass('hide');
        $(".multiselect").blur();
    });
    $('#create_btn').click(function () {
        $("#ext_ro_form").validate();
        $('#sel_agency_contact').rules('add', {
            check_agency_contact: true
        })
    });
    /******************************************************Datepicker***************************************************************/

    //DatePicker for RODate
    $("#txt_ro_date").datepicker(
        //options
        {
            changeMonth: true,
            dateFormat: 'yy-mm-dd',
            maxDate: '15',
            minDate: '-1m',
            onSelect: function () {
                validator.element("#txt_ro_date");
            }


        }
    );
    //opening datepicker when clicking on calendar icon having class ro_date
    $('.ro_date').click(function () {
        $("#txt_ro_date").focus();
    });

    //Datepicker for Campaign Start Date
    $('#txt_camp_start_date').datepicker({
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        maxDate: '+1m',
        minDate: '+2',
        //Changing the minDate to the selected Date
        onSelect: function () {
            validator.element("#txt_camp_start_date");
            var date = $(this).datepicker('getDate');
            $('#txt_camp_end_date').datepicker("option", "minDate", date);

        },

    });
    //opening datepicker when clicking on calendar having class camp_start_date
    $('.camp_start_date').click(function () {
        $("#txt_camp_start_date").focus();
    });

    //Datepicker for campaign end date
    $('#txt_camp_end_date').datepicker(
        //options
        {
            changeMonth: true,
            minDate: '+2',
            dateFormat: 'yy-mm-dd',
            onSelect: function () {
                validator.element("#txt_camp_end_date");
            }

        }
    );

    //opening datepicker when clicking on calendar having class camp_end _date
    $('.camp_end_date').click(function () {
        $('#txt_camp_end_date').focus();
    });


    /*****************************************************Multiselect***********************************************************/

    //multiselect for market
    $('#sel_market').multiselect({
        maxHeight: 200,
        buttonWidth: 220,
        includeSelectAllOption: true,
        onDropdownHide: function () //When dropdown closes then this event is triggered and it will add row for Various Market selected
        {
            //validating if user selected any market or not

            var selected_market = $("#sel_market :checked");
            $('#market_tr').show();
            table_html = '<tr id="MarketRow"><td colspan="8" nowrap="nowrap" class="no_border">Market wise RO Amount <span style="color:darkblue;">(If you enter amount \'0\' for both spot and banner for a market then it will be discarded)</span></td></tr>' +
                '<tr><th width="14%" style="text-align: center;width:14%" class="no_border">Market Name</th><th style="width:0.1%" width="0.1%">&nbsp;</th><th width="14%" style="width:14%" class="no_border">Spot Amount</th><th width="14%" style="width:14%" class="no_border">Spot Fct</th><th width="14%" style="width:14%" class="no_border">Spot Rate</th><th width="14%" style="width:14%" class="no_border">Banner Amount</th><th width="14%" style="width:14%" class="no_border">Banner Fct</th><th width="14%" style="width:14%" class="no_border">Banner Rate</th></tr>';

            //creating rows for no. of markets
            $.map(selected_market, function (element) {
                market_name = $(element).val();
                market_id = market_name.split(" ").join("_");
                market_val_spot = $('#' + market_id + '_spot_amount_create').val();
                market_val_banner = $('#' + market_id + '_banner_amount_create').val();
                console.log("market_val_spot "+market_val_spot);
                console.log("market_val_banner "+market_val_banner);
                market_array[market_id + '_spot_amount'] = market_val_spot;
                market_array[market_id + '_banner_amount'] = market_val_banner;

                /********************** Adding code for FCT */

                market_val_spot_FCT = $('#' + market_id + '_spot_FCT_create').val();
                market_val_banner_FCT = $('#' + market_id + '_banner_FCT_create').val();

                market_array[market_id + '_spot_FCT'] = market_val_spot_FCT;
                market_array[market_id + '_banner_FCT'] = market_val_banner_FCT;

                /******************** Adding code for FCT */

                /*code added for rates*/
                market_val_spot_rate = $('#' + market_id + '_spot_rate_create').val();
                market_val_banner_rate = $('#' + market_id + '_banner_rate_create').val();

                market_array[market_id + '_spot_rate'] = market_val_spot_rate;
                market_array[market_id + '_banner_rate'] = market_val_banner_rate;
                /************Spot Amount*******/
                if (market_array[market_id + '_spot_amount'] != null) {

                    table_html += '<tr padding="4px">';
                    table_html += '<td class="no_border" nowrap="nowrap" width="14%" style="border-bottom:none;padding:0px;text-align:center;width:14%">' + market_name + '<span style="color:#F00;"> *</span></td><td width="0.1%" nowrap="nowrap" class="no_border"  style="border-bottom:none;padding:0px;"> : </td><td class="no_border" width="10%" nowrap="nowrap" style="border-bottom:none;padding:0px;"><input type="text" id="' + market_id + '_spot_amount_create" name="' + market_id + '_spot_amount_create" style="width:80px; margin-bottom: 10px;margin-right: 10px;" onblur="price_check_for_each_market(\'' + market_id + '_spot_amount_create\',this.value);" value="' + market_array[market_id + '_spot_amount'] + '" /></td>';
                } else {

                    table_html += '<tr padding="4px">';
                    table_html += '<td class="no_border" nowrap="nowrap" width="14%" style="border-bottom:none;padding:0px;text-align:right;width:14%">' + market_name + '<span style="color:#F00;"> *</span></td><td width="0.1%" nowrap="nowrap" class="no_border"  style="border-bottom:none;padding:0px;"> : </td><td width="10%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;"><input type="text" id="' + market_id + '_spot_amount_create" name="' + market_id + '_spot_amount_create"  style="width:80px; margin-bottom: 10px;margin-right: 10px;" onblur="price_check_for_each_market(\'' + market_id + '_spot_amount_create\',this.value);"  value="0" /></td>';
                }
                /************Spot Fct***********/
                if (market_array[market_id + '_spot_FCT'] != null) {
                    table_html += '<td width="14%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;width:14%"><input type="text" id="' + market_id + '_spot_FCT_create" name="' + market_id + '_spot_FCT_create" style="width:70px; margin-bottom: 10px;margin-right: 10px;" onblur="validateFCTNumber(\'' + market_id + '_spot_FCT_create\',this.value)" value="' + market_array[market_id + '_spot_FCT'] + '" /></td>';
                } else {
                    table_html += '<td width="14%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;width:14%"><input type="text" onblur="validateFCTNumber(\'' + market_id + '_spot_FCT_create\',this.value)" id="' + market_id + '_spot_FCT_create" name="' + market_id + '_spot_FCT_create" style="width:70px;margin-bottom: 10px;margin-right: 10px;"  value="0" /></td>';
                }
                /**************Spot Rate*********/
                if (market_array[market_id + '_spot_rate'] != null) {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:14%"><input type="text" id="' + market_id + '_spot_rate_create" name="' + market_id + '_spot_rate_create" readonly style="width:70px; margin-bottom: 10px;margin-right: 10px;"  value="' + market_array[market_id + '_spot_rate'] + '" /></td>';
                } else {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:14%"><input type="text"  id="' + market_id + '_spot_rate_create" name="' + market_id + '_spot_rate_create" readonly style="width:70px; margin-bottom: 10px;margin-right: 10px;"  value="0" /></td>';
                }
                /****************Banner Amount*******/
                if (market_array[market_id + '_banner_amount'] != null) {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:14%"><input type="text" id="' + market_id + '_banner_amount_create" name="' + market_id + '_banner_amount_create" style="width:80px; margin-bottom: 10px;margin-right: 10px;" onblur="price_check_for_each_market(\'' + market_id + '_banner_amount_create\',this.value);" value="' + market_array[market_id + '_banner_amount'] + '" /></td>';
                } else {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:14%"><input type="text" id="' + market_id + '_banner_amount_create" name="' + market_id + '_banner_amount_create" onblur="price_check_for_each_market(\'' + market_id + '_banner_amount_create\',this.value);" style="width:80px; margin-bottom: 10px;margin-right: 10px;"  value="0" /></td>';
                }
                /************Banner FCT********/
                if (market_array[market_id + '_banner_FCT'] != null) {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:14%"><input type="text" id="' + market_id + '_banner_FCT_create" name="' + market_id + '_banner_FCT_create" style="width:70px; margin-bottom: 10px;margin-right: 10px;" onblur="validateFCTNumber(\'' + market_id + '_banner_FCT_create\',this.value)" value="' + market_array[market_id + '_banner_FCT'] + '" /></td>';
                } else {
                    table_html += '<td width="14%" nowrap="nowrap" class="no_border" style="border-bottom:none;padding:0px;width:14%"><input onblur="validateFCTNumber(\'' + market_id + '_banner_FCT_create\',this.value)" type="text" id="' + market_id + '_banner_FCT_create" name="' + market_id + '_banner_FCT_create" style="width:70px; margin-bottom: 10px;margin-right: 10px;"  value="0" /></td>';

                }
                /***********Banner rate*******/
                if (market_array[market_id + '_banner_rate'] != null) {
                    table_html += '<td width="14%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;"><input type="text" id="' + market_id + '_banner_rate_create" name="' + market_id + '_banner_rate_create" readonly style="width:70px; margin-bottom: 10px;margin-right: 10px;"  value="' + market_array[market_id + '_banner_rate'] + '" /></td>';
                    table_html += '</tr>';
                } else {
                    table_html += '<td width="14%" class="no_border" nowrap="nowrap" style="border-bottom:none;padding:0px;"><input type="text" id="' + market_id + '_banner_rate_create" name="' + market_id + '_banner_rate_create" readonly style="width:70px; margin-bottom: 10px;margin-right: 10px;"  value="0" /></td>';
                    table_html += '</tr>';
                }


            });
            table_html += '</tr>';
            if (selected_market.length == 0) {
                $("#market_tr").hide();
            } else {
                validator.element("#sel_market");
                $("#market_div").html(table_html);
            }

            //adding rules to dynamically created market and banner
            $.map(selected_market, function (element) {
                market_name = $(element).val();
                market_id = market_name.split(" ").join("_");
                $("#ext_ro_form").validate();
                $("#" + market_id + "_spot_amount_create").rules("add", {
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
                $('#' + market_id + '_spot_FCT_create').rules("add", {
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
                $("#" + market_id + "_banner_amount_create").rules("add", {
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

                $('#' + market_id + '_banner_FCT_create').rules("add", {
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

                //To check various rules associated with the elements in the form

            });


            if (selected_market.length == 0) {//alert(1)
                $('#txt_gross').val('0');
                $('#txt_agency_com').val('0');
                $('#txt_net_agency_com').val('0');
            } else {
                // calculate all amount
                var gross = 0;
                var gross_for_each_market;
                var commission;

                //selecting all input element whose name ends with "amount" and calculating the value of gross
                $("input[name$='amount_create']").each(function () {
                    gross_for_each_market = parseFloat(this.value);
                    commission = parseFloat($('#txt_agency_com').val());
                    commission = commission.toFixed(2);
                    gross += gross_for_each_market;
                    if (commission > gross) {
                        $('#txt_agency_com').val("0");
                        commission = 0;
                    }
                    if (isNaN(gross - commission)) {
                        $('#txt_net_agency_com').val('0');
                    } else {
                        net_agency_com = (gross - commission).toFixed(2);
                        $('#txt_net_agency_com').val(net_agency_com);
                    }
                });


                $('#txt_gross').val(gross.toFixed(2));
            }


        }
    });

    //multiselect for brand
    $('#sel_brand').multiselect({
        maxHeight: 200,
        buttonWidth: 220,
        includeSelectAllOption: true,
        onDropdownHide: function () //on close of dropdown
        {
            //checking if client is selected or not
            if (!validator.element("#sel_client")) {

            } else {

                if ($('#sel_brand').val().length > 0) {
                    validator.element('#sel_brand');
                }

            }
        }

    });

    $('#sel_brand').multiselect("disable");

    //Ids multiselect
    $('#Order_history_recipient_ids').multiselect(
        {
            maxHeight: 200,
            buttonWidth: 220,
            includeSelectAllOption: true
        }
    );

    $('#Order_history_recipient_ids').multiselect("disable");

    $('#sel_client').change(function () {
        $('#sel_brand').multiselect("enable");

    });
    $('#sel_agency_contact').change(function () {
        $('#Order_history_recipient_ids').multiselect("enable");
    });
    /************************************************Agency Contact**************************************************************/

    //Gives agency contact based on the selected agency
    $('#sel_agency').change(function () {
        var selected_agency = $('#sel_agency').val();
        $.ajax(BASE_URL + '/account_manager/get_agency_info', {
            type: 'POST',
            async: true,
            data: {agency: selected_agency},
            success: function (data) {
                $("#sel_agency_contact").html(data);
            }
        });
        //Get gst value
        getGst();
    });

    //on selecting the value of agency contact create mail ids
    $('#sel_agency_contact').change(function () {
        buildMailIds();
    });

    /***************************************************Client Contact***********************************************************/

    //on selecting the client get its contacts and also the brand
    $('#sel_client').change(function () {
        var selected_client = $('#sel_client').val();
        $.ajax(BASE_URL + '/account_manager/get_client_info', {
            type: 'POST',
            async: true,
            data: {client: selected_client},
            success: function (data) {
                $('#sel_client_contact').html(data);
                getBrand(selected_client);
            }
        });
        getGst();
    });

    $('#sel_client_contact').change(function () {
        buildMailIds();
    });

    /***************************************************Brand*****************************************************************/
    //getting brand based on the selected client
    function getBrand(selected_client) {
        $('#sel_brand').empty();
        $.ajax(BASE_URL + '/account_manager/get_brand_ajax', {
            type: 'POST',
            async: true,
            data: {adv: selected_client},
            success: function (data) {

                var html;
                var brand_data = JSON.parse(data);

                $.map(brand_data, function (value) {

                    html = html + '<option value="' + value.id + '">' + value.brand + '</option>';
                });

                $('#sel_brand').html(html);
                $('#sel_brand').multiselect('rebuild');
            }
        });
    }

    /*****************************************************GST*****************************************************************/
    //to get the gst number if available
    function getGst() {
        var common_name;
        var isAgencyOrClient;
        var name = $('#sel_agency').val();

        if (name == 'SureWaves') {
            common_name = $('#sel_client').val() == undefined ? '' : $("#sel_client").val().trim();
            isAgencyOrClient = 'advertiser';
        } else {
            common_name = $('#sel_agency').val() == undefined ? '' : $("#sel_agency").val().trim();
            isAgencyOrClient = 'agency';
        }
        $.ajax({
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
            data: {isClientOrAgency: isAgencyOrClient, name: common_name},
            url: BASE_URL + '/account_manager/getGst',
            dataType: "json",
            success: function (data) {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                if (data.status == 'success') {
                    if (data.gst == null) {
                        $("#txt_gstin").val('');
                        $("#txt_gstin").removeAttr('disabled');
                    } else {
                        $("#txt_gstin").val(data.gst);
                        if (!validator.element("#txt_gstin")) {

                        }

                        $("#txt_gstin").attr("disabled", "disabled");
                    }


                } else {
                    $("#txt_gstin").val('');
                    $("#txt_gstin").removeAttr('disabled');
                }
            },
            error: function () {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                alert('Error Occurred.Please check your network connection.');
            }
        });
    }

    //to add gst in the db
    $("#add_gst").click(function () {

        if (!validator.element("#txt_gstin")) {

        } else {
            var isClientOrAgency;
            var name;
            if ($('#txt_gstin').is(':disabled')) {
                return false;
            }
            if ($('#sel_agency').val() === 'SureWaves') {
                isClientOrAgency = 'advertiser';
                name = $('#sel_client').val();
            } else {
                isClientOrAgency = 'agency';
                name = $('#sel_agency').val();
            }
            $.ajax({
                type: "POST",
                async: true,
                data: {gst: ($('#txt_gstin').val()).trim(), isClientOrAgency: isClientOrAgency, name: name},
                url: BASE_URL + '/account_manager/addGst',
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        alert('GST number added sucessfully');
                        $("#txt_gstin").attr("disabled", "disabled");
                    }
                },
                error: function () {
                    alert('Error Occurred.Please check your network connection.');
                }
            });

        }

    });

    /***************************************************MailIds***********************************************************/
    //building mailids from client contact and agency contact
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

                        $('#Order_history_recipient_ids').append('<option value=' + value.id + '>' + value.id + '</option>');

                    });
                    //Reloading the multiselect
                    $('#Order_history_recipient_ids').multiselect('rebuild');

                }
            }
        });
    }


    /**************************************************Validation*********************************************************/
    var response = true;

    //method to check wheter the ro exist in db or not
    $.validator.addMethod("check_ro_existence", function (value, element) {
        $.ajax(BASE_URL + '/account_manager/check_for_ro_existence', {
            type: "POST",
            async: false,
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");

            },
            complete: function () {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");

            },

            data: {ext_ro: $('#txt_ext_ro').val()},
            success: function (data) {

                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                if (data == 0) {

                    response = true;
                } else {
                    response = false;
                }

            }
        });
        return response;
    }, "RO ALREADY EXIST");

    //method to check wheter the agency contact is selected or not
    $.validator.addMethod("check_agency_contact", function (value, element) {

        if ($('#sel_agency_contact').val() == "new") {
            return false;
        } else {
            return true;
        }
    }, "Please select agency Contact Name");


    //method to check wheter agency is selected or not
    $.validator.addMethod("check_agency", function (value, element) {
        if ($('#sel_agency').val() == null) {
            return false;
        } else {
            return true;
        }
    }, "Please select an agency");

    //method to check wheter client is selected or not
    $.validator.addMethod("check_client", function (value, element) {
        if ($('#sel_client').val() == null) {
            return false;
        } else {
            return true;
        }
    }, "Please select a client");

    //method to check wheter the brand is selected or not
    $.validator.addMethod("check_brand", function (value, element) {

        if ($('#sel_brand :checked').length <= 0) {
            return false;
        } else {
            return true;
        }
    }, "Please select a brand");

    //method to check wheter the region is selected or not
    $.validator.addMethod("check_region", function (value, element) {
        if ($("#regionSelectBox").val() == null) {
            return false;
        } else {
            return true;
        }
    }, "Please select a region");

    //method to check wheter market is selected or not
    $.validator.addMethod("check_market", function (value, element) {
        if ($('#sel_market :checked').length <= 0) {
            return false;
        } else {
            return true;
        }
    }, "Please select a market");

    $.validator.addMethod("check_ro_file_size",function(value,element){
        
        let retrunValue=true;
        if($('#file_pdf')[0].files.length > 0 )
        {
                if($('#file_pdf')[0].files[0].size  > 18000000)
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
    },"RO Mail file size is greater than 18mb");

    $.validator.addMethod("check_approval_mail_size",function(value,element){
        let retrunValue=true;
        if($('#client_aproval_mail')[0].files.length > 0)
        {
                if($('#client_aproval_mail')[0].files[0].size > 18000000)
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
    },"Client Approval Mail file size is greater than 18mb");

    //method to check if special character is there or not in spcl_inst
    $.validator.addMethod("check_inst", function (value, element) {
        var spcl_inst = $('#txt_spcl_inst').val();

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
        file_path = $('#file_pdf').val().replace(/^C:\\fakepath\\/i, '');
        file_regex = /[^a-zA-Z0-9 ._-]/;
        if (file_regex.test(file_path)) {

            return false;
        } else {
            return true;
        }


    }, "Special Characters are not allowed in RO attachements");

    $.validator.addMethod("check_both_file_size",function(value,element){
        
        if($('#file_pdf')[0].files.length > 0 && $('#client_aproval_mail')[0].files.length > 0)
        {
                if(($('#file_pdf')[0].files[0].size + $('#client_aproval_mail')[0].files[0].size) > 18000000)
                {
                    $('#file_error').css('display','contents');
                    hasError=true;
                    return true;
                }
                else
                {
                    $('#file_error').css('display','none');
                    hasError=false;
                    return true; 
                }
        }
        else
        {
            return true;
        } 

    },"");

    //method to check wheter there is special character or not in the attach_ro
    $.validator.addMethod("check_attachement_name", function (value, element) {
        file_path = $('#client_aproval_mail').val().replace(/^C:\\fakepath\\/i, '');
        file_regex = /[^a-zA-Z0-9 ._-]/;
        if (file_regex.test(file_path)) {

            return false;
        } else {
            return true;
        }


    }, "Special Characters are not allowed in client attachements");

    //Check whether the gst entered is valid or not
    $.validator.addMethod("validate_gst", function (value, element) {
        var pattern = /^([0]{1}[1-9]{1}|[1-2]{1}[0-9]{1}|[3]{1}[0-7]{1})([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/;
        console.log(value);
        console.log(pattern.test(value.trim()));
        if (pattern.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "GST entered is invalid");

    //check whether the spot amount is zero or not
    $.validator.addMethod("check_for_zero_spot", function (value, element) {
        var id = $(element).attr('id');
        var market_id;
        //console.log('id '+id);
        var splitId = id.split("_");
        var length = splitId.length;
        //console.log('length '+length);
        //console.log("check_for_zero_spot "+splitId.splice(-length, length - 3));
         
         splitId.pop();
         splitId.pop();
         splitId.pop();
         //console.log('market_id '+market_id);
        market_id = splitId.join('_');
        var spotamount = $("#" + market_id + "_spot_amount_create").val();
        
        var spotfct = $("#" + market_id + "_spot_FCT_create").val();
         
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
        var market_id;
        splitId.pop();
         splitId.pop();
         splitId.pop();
        market_id = splitId.join('_');
        var banneramount = $("#" + market_id + "_banner_amount_create").val();
        var bannerfct = $("#" + market_id + "_banner_FCT_create").val();
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
        var market_id ;
        splitId.pop();
        splitId.pop();
        splitId.pop();
        market_id = splitId.join('_');
        var spotamount = $("#" + market_id + "_spot_amount_create").val();
        var spotfct = $("#" + market_id + "_spot_FCT_create").val();
        var banneramount = $("#" + market_id + "_banner_amount_create").val();
        var bannerfct = $("#" + market_id + "_banner_FCT_create").val();
        if (spotamount == 0 && spotfct == 0 && banneramount == 0 && bannerfct == 0) {
            return false;
        } else {
            return true;
        }
    }, "Both spot and banner value cannot be zero");

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
        var gross = $("#txt_gross").val();
        var rd_Val = $("input[name='rd_com']:checked").val();

        var com = 0;
        if (rd_Val == 0) {
            com = gross * (agency_com / 100);
        } else if (rd_Val == 1) {
            com = agency_com;
        }

        if (Number(com) > Number(gross)) {
            return false;
        } else {
            return true;
        }
    }, "Commission cannot be greater than gross amount");


    //checking validation for agency on blur
    $('#sel_agency').focusout(function () {
        validator.element("#sel_agency")

    });

    //checking validation for client on blur
    $('#sel_client').focusout(function () {
        validator.element("#sel_client")
    });

    //checking validation for region on blur
    $('#regionSelectBox').focusout(function () {
        validator.element("#regionSelectBox")
    });

    //checking validation for spcl_inst on blur
    $('#txt_spcl_inst').focusout(function () {
        validator.element("#txt_spcl_inst")

    });

    //checking validation for file_pdf on selecting a file
    $("#file_pdf").change(function () {
        validator.element("#file_pdf")
    });

    //checking validation for ro_mail on selecting a file
    $("#client_aproval_mail").change(function () {
        validator.element("#client_aproval_mail")
    });


    /******************************************************Sub Modal***********************************************************/
    //modal for add-edit agency
    $("#sub_modal_btn").click(function () {

        $('#create_ext_Ro_modal').modal('hide');
    });

    $('#add_edit_agency_contact_span').click(function () {
        if (validator.element("#sel_agency")) {
            var selected_agency = $('#sel_agency').val();
            var selected_agency_contact = $('#sel_agency_contact').val();
            if (selected_agency_contact == 'new') {
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

        }

    });
    $('#reset_btn').click(function () {

        $('#ext_ro_form').trigger("reset");

        $('#sel_brand').empty();
        $('#Order_history_recipient_ids').empty();
        $('#sel_brand').multiselect('rebuild');
        $('#Order_history_recipient_ids').multiselect('rebuild');
        $('#sel_market').multiselect('rebuild');
        $('#market_div').empty();
        $('#market_tr').css('display', 'none');
        $('#txt_gstin').attr('disabled', false);
        $('#sel_brand').multiselect('disable');
        validator.resetForm();
        $('#create_ext_ro_error_div').empty();
        $('#create_ext_ro_error_div').css("display", "none");

        $('#Order_history_recipient_ids').multiselect("disable");
    });

    $("#add_edit_client_contact_span").click(function () {
        if (validator.element("#sel_client")) {
            var selected_client = $('#sel_client').val();
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
        }
    });

    $('#create_ext_Ro_modal').on("hidden.bs.modal", function () {
        getGst();
    });


});

//retrieve the value of form and create jsonobject
function createObject() {
    //creating formdata object
    var form_data = new FormData();
    var ext_ro_value = $("#txt_ext_ro").val();
    form_data.append("txt_ext_ro", ext_ro_value);
    var ro_date = $("#txt_ro_date").val();
    form_data.append("txt_ro_date", ro_date);
    var agency_value = $("#sel_agency").val();
    form_data.append("sel_agency", agency_value);
    var agency_contact_value = $('#sel_agency_contact').val();
    form_data.append("sel_agency_contact", agency_contact_value);
    var client_value = "";
    if ($('#sel_client').val() != null) {
        client_value = $('#sel_client').val();
    }
    form_data.append("sel_client", client_value);
    var client_contact_value = "";
    if ($('#sel_client_contact').val() != "new") {
        client_contact_value = $('#sel_client_contact').val();
    }
    form_data.append("sel_client_contact", client_contact_value);
    var gst_value = $("#txt_gstin").val();
    form_data.append("txt_gstin", gst_value);
    var sel_brand_arr = [];
    var selected_brand = $("#sel_brand :checked");
    $.map(selected_brand, function (element) {
        sel_brand_arr.push($(element).val());
    });
    form_data.append("sel_brand", sel_brand_arr);
    var make_good_value = $("input[name='rd_make_good']:checked").val();
    form_data.append("rd_make_good", make_good_value);
    var am_name = $("#txt_am_name").val();
    form_data.append("txt_am_name", am_name);
    var camp_start_date = $("#txt_camp_start_date").val();
    form_data.append("txt_camp_start_date", camp_start_date);
    var camp_end_date = $("#txt_camp_end_date").val();
    form_data.append("txt_camp_end_date", camp_end_date);
    var region = "";
    if ($("#regionSelectBox").attr('data') != null) {
        region = $("#regionSelectBox").attr('data');
    }
    form_data.append("regionSelectBox", region);
    var market_obj = {};
    var market_arr = [];
    var market_name_arr = [];
    var selected_market = $("#sel_market :checked");
    $.map(selected_market, function (element) {
        var market_name = $(element).val();
        market_name_arr.push(market_name);
        var market_id = market_name.split(' ').join('_');
        var market_spot_amount = $("#" + market_id + "_spot_amount_create").val();
        var market_spot_FCT = $("#" + market_id + "_spot_FCT_create").val();
        var market_spot_rate = $("#" + market_id + "_spot_rate_create").val();
        var market_banner_amount = $("#" + market_id + "_banner_amount_create").val();
        var market_banner_FCT = $("#" + market_id + "_banner_FCT_create").val();
        var market_banner_rate = $("#" + market_id + "_banner_rate_create").val();
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
    var gross_value = $("#txt_gross").val();
    form_data.append("txt_gross", gross_value);
    var commission_value = $("#txt_agency_com").val();
    form_data.append("txt_agency_com", commission_value);

    var rd_value = $("input[name='rd_com']:checked").val();
    form_data.append("rd_com", rd_value);
    var net_agency_value = $("#txt_net_agency_com").val();
    form_data.append("txt_net_agency_com", net_agency_value);
    var spcl_inst_value = "";
    if ($("#txt_spcl_inst").val() != null) {
        spcl_inst_value = $("#txt_spcl_inst").val();
    }
    form_data.append("txt_spcl_inst", spcl_inst_value);
    var mail_arr = [];
    var selected_mail = $("#Order_history_recipient_ids :checked");
    $.map(selected_mail, function (element) {
        mail_arr.push($(element).val());
    });
    form_data.append("Order_history_recipient_ids", mail_arr);
    var ro_file = $("input[name='file_pdf']")[0].files[0];
    form_data.append("file_pdf", ro_file);
    var mail_file = $("input[name='client_aproval_mail']")[0].files[0];
    form_data.append("client_aproval_mail", mail_file);
    var user_id = $('#hid_user_id').val();
    var user_type = $('#user_type').val();
    form_data.append("hid_user_id", user_id);
    form_data.append("user_type", user_type);
    form_data.append('hid_brand', sel_brand_arr);
    form_data.append('hid_market', market_name_arr);
    return form_data;
}

/**********************************************************Market******************************************************/

//getting gross value and  rate
function price_check_for_each_market(id, value) {
    var gross = value;
    var splitId = id.split("_");
    var isBannerOrSpot = splitId.splice(-3, 1);
    console.log(isBannerOrSpot);
    var removecreate = splitId.splice(-1, 1);
    var removeamount=splitId.splice(-1, 1);
    console.log('removeamount '+removeamount );
    var marketName = splitId.join("_");
    console.log('market_name '+marketName);
    var fct = Number($('#' + marketName + '_' + isBannerOrSpot + '_FCT_create').val());

    if (fct != 0 && !isNaN(fct)) {
        setRate(marketName, gross, fct, isBannerOrSpot);
    }
    var gross = 0;
    var gross_for_each_market;
    var commission = 0;

    $("input[name$='amount_create']").each(function () {

        gross_for_each_market = parseFloat(this.value);
        gross += gross_for_each_market;
    });
    gross = Number(gross).toFixed(2);
    if (isNaN(gross)) {
        $('#txt_gross').val('0');
    } else {
        $('#txt_gross').val(gross);
    }

    var agency_com_entered = Number($('#txt_agency_com_holder').val());
    var isCommisionByAmountOrPercentage = Number($('input[name=rd_com]:checked').val());
    if (agency_com_entered > 0) {
        commission = calculateAgencyCommision(agency_com_entered, gross, isCommisionByAmountOrPercentage);
        commission = commission.toFixed(2);
        if (commission > gross) {
            $('#txt_agency_com_holder').val("0.00");
            commission = 0;
            return false;
        }
    }

    var net_agency_com = (gross - commission).toFixed(2);
    $('#txt_agency_com').val(commission);
    if (isNaN(gross)) {
        $('#txt_net_agency_com').val('0');
    } else {
        $('#txt_net_agency_com').val(net_agency_com);
    }


}

//getting rate
function validateFCTNumber(id, value) {


    var fctValue = $('#' + id).val();
    var splitId = id.split("_");
    var isBannerOrSpot = splitId.splice(-3, 1);
    var removeamount = splitId.splice(-1, 1);
    var removecreate=splitId.splice(-1, 1);
    var marketName = splitId.join("_");
    var gross = Number($('#' + marketName + '_' + isBannerOrSpot + '_amount_create').val());
    if (fctValue != 0 && !isNaN(fctValue)) {
        setRate(marketName, gross, fctValue, isBannerOrSpot);
    } else {
        $('#' + marketName + '_' + isBannerOrSpot + '_rate_create').val('0');
    }

}

//to get the rate for banner and amount based on fct and amount
function setRate(marketName, amount, fct, condition) {
    //console.log("marketName " + marketName);
    //console.log("amount " + amount);
    var foundRate = amount * 10 / fct;
    console.log("foundRate ", foundRate);
    foundRate = foundRate.toFixed(2);
    $('#' + marketName + '_' + condition + '_rate_create').val(foundRate);
    validator.element("#" + marketName + "_spot_amount_create");
    validator.element("#" + marketName + "_spot_FCT_create");
    validator.element("#" + marketName + "_banner_amount_create");
    validator.element("#" + marketName + "_banner_FCT_create");
}

//to calculate the commision value and net agency commission
function calculate_commission() {

    var value = $('#txt_agency_com_holder').val();
    var com_by = Number($('input[name=rd_com]:checked').val());
    var gross = Number($('#txt_gross').val());
    var commission = 0;
    value = Number(value);
    if (gross > 0) {
        commission = calculateAgencyCommision(value, gross, com_by);
    }
    var net_agency_com = (gross - commission).toFixed(2);
    $('#txt_agency_com').val(commission);
    $('#txt_net_agency_com').val(net_agency_com);
    validator.element("#txt_agency_com_holder");
}

//function calculating value of agency commission based on percentage or amount
function calculateAgencyCommision(agencyCommissionVal, gross, commission_by) {
    var agency_amount = 0;
    if (commission_by == 0) {
        agency_amount = gross * (agencyCommissionVal / 100);
    } else {
        agency_amount = agencyCommissionVal;
    }
    return agency_amount.toFixed(2);
}


