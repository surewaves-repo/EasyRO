// JavaScript Document
var enterprise_channel_json, excelObjForAllEnterpriseDetails, filesData;
var contact = 1;
var finance = 2;
var channel = 3;

window.onload = function () {
    new JsDatePick({
        useMode: 2,
        target: "signed_on",
        dateFormat: "%d-%M-%Y"
    });
};
$(document).ready(function () {
    $(".cssmenu_list li:eq(0)").click(function () {
        if ($('#enterprise_list').val() != 'select') {
            $('.select_enterprise').hide();
            $('.nw_content').hide();
            $(".cssmenu_list li").removeClass("active");
            $(this).addClass("active");
            var id = $(this).find("a").attr('data-href');
            //console.log(id);
            $(id).show();
            showMenus();
        }
    });
    // $(".cssmenu_list li:eq(0)").click();
    $("#genere").chosen({width: "61%"});
    $("#language").chosen({width: "61%"});
    $("#dominant_content").chosen({width: "61%"});


    $(".cssmenu_list>li").click(function () {

        if ($('#enterprise_list').val() != 'select') {
            $('.select_enterprise').hide();
            $('.nw_content').hide();
            $(".cssmenu_list li").removeClass("active");
            $(this).addClass("active");
            var id = $(this).find("a").attr('data-href');
            //console.log(id);
            $(id).show();
            if (id == '#edit_channel_details') {

                var total_ul_height = parseInt($('.vertical_menu_ul').outerHeight(), 10);
                //console.log($('.vertical_menu_ul').outerHeight());
                var div_height = $('.menu_div').outerHeight();
                //console.log(div_height);
                if (total_ul_height > div_height) {
                    $('.dropdown_marker').show();
                } else {
                    $('.dropdown_marker').hide();
                }
            }
        }
    });

    $('#dropdown_scroll').click(function () {
        //alert(1);
        var margin_top = $('.vertical_menu_ul').css('margin-top');
        margin_top = parseInt(margin_top.replace("px", ""), 10);
        //if()
        //$('.vertical_menu_ul').animate({'margin-top':(margin_top-40)+'px'},300);
        //var margin_top 		= $('.vertical_menu_ul').css('margin-top');
        //margin_top 			= parseInt(margin_top.replace("px", ""),10);
        //console.log($('.vertical_menu_ul').height()+margin_top,420);
        var total_ul_height = parseInt($('.vertical_menu_ul').outerHeight(), 10);
        var div_height = $('.menu_div').outerHeight();
        var margin_top = $('.vertical_menu_ul').css('margin-top');
        margin_top = parseInt(margin_top.replace("px", ""), 10);
        //console.log($('.vertical_menu_ul').height()+margin_top,428);
        if ($('.dropdown_marker').hasClass('Expand')) {
            if ((div_height - total_ul_height) < margin_top) {
                if (((div_height - total_ul_height) - margin_top) > -39) {
                    margin_top = margin_top + ((div_height - total_ul_height) - margin_top);
                } else {
                    margin_top = margin_top - 39;
                }
                //$('.vertical_menu_ul').css('margin-top',margin_top+'px');
                $('.vertical_menu_ul').animate({'margin-top': (margin_top) + 'px'}, 300);

                if ($('.vertical_menu_ul').height() + margin_top - 30 < $('#vertical_menu').height()) {
                    $('.pull').addClass('clockwise');
                    $('.dropdown_marker').removeClass('Expand').addClass('Close');
                }
                //return;
                //console.log($('.vertical_menu_ul').height()+margin_top-10,margin_top);
            }
        } else if ($('.dropdown_marker').hasClass('Close')) {
            var margin_top = $('.vertical_menu_ul').css('margin-top');
            margin_top = parseInt(margin_top.replace("px", ""), 10);
            if (margin_top < 0) {
                if ((margin_top + 39) > 0) {
                    margin_top = 0;
                } else {
                    margin_top = margin_top + 39;
                }
                //$('.vertical_menu_ul').css('margin-top',margin_top+'px');
            }

            $('.vertical_menu_ul').animate({'margin-top': (margin_top) + 'px'}, 300);

            if (-(margin_top) < 10) {
                $('.pull').removeClass('clockwise');
                $('.dropdown_marker').removeClass('Close').addClass('Expand');

            }
        }

    });

    $('#enterprise_list').change(function () {
        $('.showDocs').hide();
        $('.showDocs').html('');
        var enterprise_id = $('#enterprise_list').val();
        $('.select_enterprise').hide();
        $('.enterpriseId').val(enterprise_id);
        $('.contact_enterprise_exist').val('new');
        $('.finance_enterprise_exist').val('new');
        $('.check_enterprise_channel_exist').val('new');
        $('#resultLoading').show();
        $.ajax({
            type: "POST",
            url: "get_enterprise_details",
            //dataType: 'json',
            //	async:false,
            data: {enterprise_id: enterprise_id},
            success: function (res) {
                //console.log(res);
                populateData($.parseJSON(res));
                $(".cssmenu_list li:eq(0)").click();
                //$('#edit_contact_details').show();
                //$(".cssmenu_list li").removeClass("active");
                //$(".cssmenu_list li:eq(0)").addClass("active");
                $('#resultLoading').hide();
            },
            error: function (e) {
                alert("failed to Fetch the data");
            }
        });

    });
    $('#update_enterprise_contact').click(function () {

        if (($('#enterprise_contact_person').val()).trim() == '') {
            alert("Please enter contact Person.");
            $('#enterprise_contact_person').focus();
            return false;
        }

        if (($('#email_id').val()).trim() == '') {
            alert("Please enter Email.");
            $('#email_id').focus();
            return false;
        }
        /* if(($('#email_id').val()).trim() != ''){
             var email = ($('#email_id').val()).trim();
             var ret  = Utility.validateEmail(email);
             if(!ret){
                 alert("Please enter Proper Email.");
                 $('#email_id').focus();
                 return false;
             }

         }*/
        if (($('#email_id').val()).trim() != '') {
            var email = ($('#email_id').val()).trim();
            var emailArr = email.split(",");
            for (var count = 0; count < emailArr.length; count++) {
                var ret = Utility.validateEmail(emailArr[count]);
                if (!ret) {
                    break;
                }
            }
            if (!ret) {
                alert("Please enter Proper Email.");
                $('#email_id').focus();
                return false;
            }

        }

        if (($('#mobile_no').val()).trim() == '') {
            alert("Please enter mobile no.");
            $('#mobile_no').focus();
            return false;
        }
        if (($('#mobile_no').val()).trim() != '') {
            var mobileno = ($('#mobile_no').val()).trim();
            if (mobileno.length < 10) {
                alert("Mobile number should be 10 digits");
                $('#mobile_no').focus();
                return false;
            }
            var phoneno_regExp = /^[0-9]+$/;
            if (phoneno_regExp.test(mobileno) == false) {
                alert("Please enter proper mobile no.");
                $('#mobile_no').focus();
                return false;
            }
        }
        if (($('#land_no').val()).trim() != '') {
            var land_no = ($('#land_no').val()).trim();
            if (land_no.length < 11) {
                alert("Land number should be 11 digits");
                $('#land_no').focus();
                return false;
            }
            var phoneno_regExp = /^[0-9]+$/;
            if (phoneno_regExp.test(land_no) == false) {
                alert("Please enter proper Land number.");
                $('#land_no').focus();
                return false;
            }
        }
        if (($('#fax_no').val()).trim() != '') {
            var fax_no = ($('#fax_no').val()).trim();
            if (fax_no.length < 11) {
                alert("Fax number should be 11 digits");
                $('#fax_no').focus();
                return false;
            }
            var phoneno_regExp = /^[0-9]+$/;
            if (phoneno_regExp.test(fax_no) == false) {
                alert("Please enter proper Fax number.");
                $('#fax_no').focus();
                return false;
            }
        }

        if (($('#address_details').val()).trim() == '') {
            alert("Please enter address.");
            $('#address_details').focus();
            return false;
        }
        if (enterprise_channel_json.enterprise_contact_details == null) {
            enterprise_channel_json.enterprise_contact_details = Array();
            enterprise_channel_json.enterprise_contact_details[0] = {};
        }
        enterprise_channel_json.enterprise_contact_details[0].contact_person = $('#enterprise_contact_person').val();
        enterprise_channel_json.enterprise_contact_details[0].email_id = $('#email_id').val();
        enterprise_channel_json.enterprise_contact_details[0].mobile_number = $('#mobile_no').val();
        enterprise_channel_json.enterprise_contact_details[0].land_line_number = $('#land_no').val();
        enterprise_channel_json.enterprise_contact_details[0].fax_number = $('#fax_no').val();
        enterprise_channel_json.enterprise_contact_details[0].address = $('#address_details').val();
        var param = $('#enterprise_contact').serialize();
        saveData(param);
    });
    $('#update_enterprise_finance').click(function () {
        if (($('#contact_person').val()).trim() == '') {
            alert("Please enter contact Person.");
            $('#contact_person').focus();
            return false;
        }
        if (($('#finance_details_email').val()).trim() == '') {
            alert("Please enter Email.");
            $('#finance_details_email').focus();
            return false;
        }
        if (($('#finance_details_email').val()).trim() != '') {
            var email = ($('#finance_details_email').val()).trim();
            var emailArr = email.split(",");
            for (var count = 0; count < emailArr.length; count++) {
                var ret = Utility.validateEmail(emailArr[count]);
                if (!ret) {
                    break;
                }
            }
            if (!ret) {
                alert("Please enter Proper Email.");
                $('#finance_details_email').focus();
                return false;
            }

        }
        if (($('#contact_number').val()).trim() == '') {
            alert("Please enter contact number.");
            $('#contact_number').focus();
            return false;
        }

        if (($('#contact_number').val()).trim() != '') {
            var contact_no = ($('#contact_number').val()).trim();
            var phoneno_regExp = /^[0-9]+$/;
            if (contact_no.length < 11) {
                alert("Contact number should be 11 digits");
                $('#contact_number').focus();
                return false;
            }
            if (phoneno_regExp.test(contact_no) == false) {
                alert("Please enter proper contact number.");
                $('#contact_number').focus();
                return false;
            }
        }


        if (($('#billing_name').val()).trim() == '') {
            alert("Please enter Billing name.");
            $('#billing_name').focus();
            return false;
        }
        if (($('#pan_no').val()).trim() == '') {
            alert("Please enter Pan number.");
            $('#pan_no').focus();
            return false;
        }
        if (($('#acc_no').val()).trim() == '') {
            alert("Please enter Account number.");
            $('#acc_no').focus();
            return false;
        }
        if (($('#bank_name').val()).trim() == '') {
            alert("Please enter Bank name.");
            $('#bank_name').focus();
            return false;
        }
        if (($('#branch_name').val()).trim() == '') {
            alert("Please enter Bank branch name.");
            $('#branch_name').focus();
            return false;
        }
        if (($('#ifsc_code').val()).trim() == '') {
            alert("Please enter IFSC code.");
            $('#ifsc_code').focus();
            return false;
        }
        enterprise_channel_json.enterprise_finance_details[0].contact_person = $('#contact_person').val();
        enterprise_channel_json.enterprise_finance_details[0].email_id = $('#finance_details_email').val();
        enterprise_channel_json.enterprise_finance_details[0].contact_number = $('#contact_number').val();
        enterprise_channel_json.enterprise_finance_details[0].billing_name = $('#billing_name').val();
        enterprise_channel_json.enterprise_finance_details[0].pan_number = $('#pan_no').val();
        enterprise_channel_json.enterprise_finance_details[0].account_number = $('#acc_no').val();
        enterprise_channel_json.enterprise_finance_details[0].bank_name = $('#bank_name').val();
        enterprise_channel_json.enterprise_finance_details[0].branch_name = $('#branch_name').val();
        enterprise_channel_json.enterprise_finance_details[0].ifsc_code = $('#ifsc_code').val();
        enterprise_channel_json.enterprise_finance_details[0].service_tax_number = $('#service_tax_no').val();

        var param = $('#enterprise_finance').serialize();
        saveData(param);
    });

    $('#update_enterprise_channel').click(function () {

        if (($('#channel_contact_person1').val()).trim() == '') {
            alert("Please enter contact Person 1.");
            $('#channel_contact_person1').focus();
            return false;
        }

        if (($('#channel_email_id1').val()).trim() == '') {
            alert("Please enter Email-id 1 .");
            $('#channel_email_id1').focus();
            return false;
        }
        if (($('#channel_email_id1').val()).trim() != '') {
            var email = ($('#channel_email_id1').val()).trim();
            var ret = Utility.validateEmail(email);
            if (!ret) {
                alert("Please enter Proper Email-Id 1.");
                $('#channel_email_id1').focus();
                return false;
            }

        }
        if (($('#channel_email_id2').val()).trim() != '') {
            var email = ($('#channel_email_id2').val()).trim();
            var ret = Utility.validateEmail(email);
            if (!ret) {
                alert("Please enter Proper Email-Id 2.");
                $('#channel_email_id2').focus();
                return false;
            }

        }
        if (($('#channel_email_id3').val()).trim() != '') {
            var email = ($('#channel_email_id3').val()).trim();
            var ret = Utility.validateEmail(email);
            if (!ret) {
                alert("Please enter Proper Email-id 3.");
                $('#channel_email_id3').focus();
                return false;
            }

        }

        if (($('#channel_contact_number1').val()).trim() == '') {
            alert("Please enter contact number.");
            $('#channel_contact_number1').focus();
            return false;
        }
        var validateContactNumberId = msg = '';
        $(".validateContactNumber").each(function () {
            if (($(this).val()).trim() != '') {
                var contact_no = ($(this).val()).trim();
                var phoneno_regExp = /^[0-9]+$/;
                if (contact_no.length < 11) {
                    msg = "Contact number should be 11 digits";
                    validateContactNumberId = $(this).attr('id');
                    return false;
                }
                if (phoneno_regExp.test(contact_no) == false) {
                    msg = "Please enter proper contact number.";
                    validateContactNumberId = $(this).attr('id');
                    return false;
                }
            }
        });
        if (validateContactNumberId != '') {
            alert(msg);
            $('#' + validateContactNumberId).focus();
            return false;
        }

        if ($('#genere').val() == '' || $('#genere').val() == null) {
            alert("Please enter Genere.");
            $('#genere').focus();
            return false;
        }
        if ($('#language').val() == '' || $('#language').val() == null) {
            alert("Please enter Language.");
            $('#language').focus();
            return false;
        }
        if ($('#channel_address_details').val() == '') {
            alert("Please enter address.");
            $('#channel_address_details').focus();
            return false;
        }

        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].contact_person1 = $('#channel_contact_person1').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].contact_person2 = $('#channel_contact_person2').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].contact_person3 = $('#channel_contact_person3').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].email_id1 = $('#channel_email_id1').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].email_id2 = $('#channel_email_id2').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].email_id3 = $('#channel_email_id3').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].contact_number1 = $('#channel_contact_number1').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].contact_number2 = $('#channel_contact_number2').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].contact_number3 = $('#channel_contact_number3').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].genre = ($('#genere').val()).toString();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].address = $('#channel_address_details').val();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].language = ($('#language').val()).toString();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].dominant_content = ($('#dominant_content').val()).toString();
        enterprise_channel_json.enterprise_channel_details[$('#channel_index').val()].has_value = 1;

        //console.log(enterprise_channel_json);
        var param = $('#enterprise_channel').serialize();
        saveData(param);

    });
    $('.finance_details').keyup(function () {
        $('.error_msg_finance').hide();
    });
    $('.channel_details').keyup(function () {
        $('.error_msg_channel').hide();
    });
    $('.contact_details').keyup(function () {
        $('.error_msg_contact').hide();
    });

    $('.vertical_menu_ul>li>a').live('click', function () {
        $(".vertical_menu_ul li").removeClass("active");
        $(this).parent('li').addClass("active");
        var index = $(this).parent('li').index();
        $('#channel_index').val(index);
        $("#genere").val("");
        $("#genere").trigger("chosen:updated");
        $("#language").val("");
        $("#language").trigger("chosen:updated");
        if (!(enterprise_channel_json.enterprise_channel_details[index].has_value)) {
            $('.error_msg_channel').show();
            $('.check_enterprise_channel_exist').val('new');
            $('.channel_details').val('');
            $('#channel_id').val(enterprise_channel_json.enterprise_channel_details[index].channel_id);

        } else {
            $('#channel_status').val(enterprise_channel_json.enterprise_channel_details[index].channel_status);
            $('#channel_deployment_status').val(enterprise_channel_json.enterprise_channel_details[index].deployment_status);
            $('#channel_contact_person1').val(enterprise_channel_json.enterprise_channel_details[index].contact_person1);
            $('#channel_email_id1').val(enterprise_channel_json.enterprise_channel_details[index].email_id1);

            if (enterprise_channel_json.enterprise_channel_details[index].contact_person2 !== null) {
                $('#channel_contact_person2').val(enterprise_channel_json.enterprise_channel_details[index].contact_person2);
            } else {
                $('#channel_contact_person2').val('');
            }
            if (enterprise_channel_json.enterprise_channel_details[index].contact_person3 !== null) {
                $('#channel_contact_person3').val(enterprise_channel_json.enterprise_channel_details[index].contact_person3);
            } else {
                $('#channel_contact_person3').val('');
            }

            if (enterprise_channel_json.enterprise_channel_details[index].email_id2 !== null) {
                $('#channel_email_id2').val(enterprise_channel_json.enterprise_channel_details[index].email_id2);
            } else {
                $('#channel_email_id2').val('');
            }
            if (enterprise_channel_json.enterprise_channel_details[index].email_id3 !== null) {
                $('#channel_email_id3').val(enterprise_channel_json.enterprise_channel_details[index].email_id3);
            } else {
                $('#channel_email_id3').val('');
            }

            $('#channel_contact_number1').val(enterprise_channel_json.enterprise_channel_details[index].contact_number1);
            $('#channel_contact_number2').val(enterprise_channel_json.enterprise_channel_details[index].contact_number2);
            $('#channel_contact_number3').val(enterprise_channel_json.enterprise_channel_details[index].contact_number3);

            $.each((enterprise_channel_json.enterprise_channel_details[index].genre).split(","), function (i, e) {
                $("#genere option[value='" + e + "']").prop("selected", true);
            });
            $("#genere").trigger("chosen:updated");
            $.each((enterprise_channel_json.enterprise_channel_details[index].language).split(","), function (i, e) {
                $("#language option[value='" + e + "']").prop("selected", true);
            });
            $("#language").trigger("chosen:updated");

            $('#channel_address_details').val(enterprise_channel_json.enterprise_channel_details[index].address);
            $('#channel_id').val(enterprise_channel_json.enterprise_channel_details[index].channel_id);
            $('.check_enterprise_channel_exist').val('exist');
            $('.error_msg_channel').hide();
            $('.no_channel').hide();
        }

    });

    $("#excel_download").click(function (e) {
        var enterprise_id_list = '';
        $('#enterprise_list option').each(function () {
            if (enterprise_id_list == '') {
                if (($(this).val()) != 'select')
                    enterprise_id_list = $(this).val();
            } else {
                enterprise_id_list += "," + $(this).val();
            }
        });

        $('#allEnterpriseDetails').val(btoa(enterprise_id_list));
        document.post_allEnterpriseDetails.submit();
    });


    $('#update_upload_attachment').click(function () {
        var enterprise_id = $('#enterprise_list').val();
        $("#customer_id").val(enterprise_id);
        $("#upload_attachment").submit();
        /*if(enterprise_channel_json.enterprise_upload_attachment == null){
            enterprise_channel_json.enterprise_upload_attachment = Array();
            enterprise_channel_json.enterprise_upload_attachment[0] = {};
        }*/
        /*$('input[type="file"]').change(function (){
            var fileName = $(this).val();
            alert($(".file_upload").html(fileName));return ;
        }); */
        //uploadFiles();

        /*enterprise_channel_json.enterprise_upload_attachment[0].billingNumber  	= $('#billing_number').val();
        enterprise_channel_json.enterprise_upload_attachment[0].customer_name			= $('#eid').val();
        enterprise_channel_json.enterprise_upload_attachment[0].signed_on		= $('#signed_on').val();
        enterprise_channel_json.enterprise_upload_attachment[0].validity_period	=    $('#validity').val()+" "+$('#validity_values').val();
        enterprise_channel_json.enterprise_upload_attachment[0].document_status		= $('#document_status').val();
        enterprise_channel_json.enterprise_upload_attachment[0].document_attached			= $('#document_status_value').val();

        enterprise_channel_json.enterprise_upload_attachment[0].service_tax_document	=    $('#service_tax_docs').val();
        enterprise_channel_json.enterprise_upload_attachment[0].pan_card		= $('#pan_card').val();
        enterprise_channel_json.enterprise_upload_attachment[0].cancelled_cheque			= $('#file_cancelled_cheque').val();
        enterprise_channel_json.enterprise_upload_attachment[0].erf	=    $('#file_erf').val();
        enterprise_channel_json.enterprise_upload_attachment[0].household_info		= $('#file_household_info').val();
        enterprise_channel_json.enterprise_upload_attachment[0].attachment_hard_copies			= $('#attachment_hard_copies').val();
        enterprise_channel_json.enterprise_upload_attachment[0].mediastation_status			= $('#media_station').val();
        //enterprise_channel_json.enterprise_upload_attachment[0].file_attachment = data ;
        var param = $('#upload_attachment').serialize();
        saveData(param); */
    });
});

/*function fileUpload() { alert("1");return ;
    filesData = event.target.files;
    alert(filesData) ;
}
function prepareUpload(event) {
    filesData = event.target.files;
    alert(filesData) ;
}

function uploadFiles() {
    var data = new FormData();
    $.each(filesData, function(key, value){
        data.append(key, value);
    });
    
    $.ajax({
        url: 'submitFiles',
        type: 'POST',
        data: data,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data, textStatus, jqXHR)
        {
            if(typeof data.error === 'undefined')
            {
                // Success so call function to process the form
                submitForm(event, data);
            }
            else
            {
                // Handle errors here
                console.log('ERRORS: ' + data.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Handle errors here
            console.log('ERRORS: ' + textStatus);
            // STOP LOADING SPINNER
        }
    });
}*/
function populateData(enterpriseData) {
    //console.log(enterpriseData);
    excelObjForAllEnterpriseDetails = enterprise_channel_json = enterpriseData;
    if (enterpriseData.enterprise_contact_details == null) {
        $('.error_msg_contact').show();
        $('.error_msg_contact').html("No contact details for this Customer.Kindly Update");
        $('.contact_enterprise_exist').val('new');
        $('.contact_details').val('');
    } else if (enterpriseData.enterprise_contact_details != null) {
        $('#enterprise_contact_person').val(enterpriseData.enterprise_contact_details[0].contact_person);
        $('#email_id').val(enterpriseData.enterprise_contact_details[0].email_id);
        $('#mobile_no').val(enterpriseData.enterprise_contact_details[0].mobile_number);
        $('#land_no').val(enterpriseData.enterprise_contact_details[0].land_line_number);
        $('#fax_no').val(enterpriseData.enterprise_contact_details[0].fax_number);
        $('#address_details').val(enterpriseData.enterprise_contact_details[0].address);
        $('.contact_enterprise_exist').val('exist');
        $('.error_msg_contact').hide();

    }
    if (enterpriseData.enterprise_finance_details == null) {
        $('.error_msg_finance').show();
        $('.error_msg_finance').html("No finance details for this Customer.Kindly Update");
        $('.finance_enterprise_exist').val('New');
        $('.finance_details').val('');
    } else if (enterpriseData.enterprise_finance_details != null) {
        $('#contact_person').val(enterpriseData.enterprise_finance_details[0].contact_person);
        $('#finance_details_email').val(enterpriseData.enterprise_finance_details[0].email_id);
        $('#contact_number').val(enterpriseData.enterprise_finance_details[0].contact_number);
        $('#billing_name').val(enterpriseData.enterprise_finance_details[0].billing_name);
        $('#pan_no').val(enterpriseData.enterprise_finance_details[0].pan_number);
        $('#acc_no').val(enterpriseData.enterprise_finance_details[0].account_number);
        $('#bank_name').val(enterpriseData.enterprise_finance_details[0].bank_name);
        $('#branch_name').val(enterpriseData.enterprise_finance_details[0].branch_name);
        $('#ifsc_code').val(enterpriseData.enterprise_finance_details[0].ifsc_code);
        $('#service_tax_no').val(enterpriseData.enterprise_finance_details[0].service_tax_number);
        $('.finance_enterprise_exist').val('exist');
        $('.error_msg_finance').hide();
    }

    if (enterpriseData.enterprise_upload_attachment == null) {
        $('.error_upload_attachment').show();
        $('.error_upload_attachment').html("No Data for this Customer.Kindly Update");
    } else if (enterpriseData.enterprise_upload_attachment != null) {
        $('#billing_number').val(enterpriseData.enterprise_upload_attachment[0].billingNumber);
        $('#eid').val(enterpriseData.enterprise_upload_attachment[0].customer_name);
        $('#signed_on').val(enterpriseData.enterprise_upload_attachment[0].signed_on);
        $('#validity').val(enterpriseData.enterprise_upload_attachment[0].validity_number);
        $('#validity_values').val(enterpriseData.enterprise_upload_attachment[0].validity_period);
        $('#document_status').val(enterpriseData.enterprise_upload_attachment[0].document_status);
        var document_attached = enterpriseData.enterprise_upload_attachment[0].document_attached;
        if (document_attached == 0) {
            $('#document_status_no').attr("checked", true);
        } else {
            $('#document_status_yes').attr("checked", true);
        }
        /* $('#service_tax_docs').val(enterpriseData.enterprise_upload_attachment[0].service_tax_document);
        $('#pan_card').val(enterpriseData.enterprise_upload_attachment[0].pan_card);
        $('#file_cancelled_cheque').val(enterpriseData.enterprise_upload_attachment[0].cancelled_cheque);
        $('#file_erf').val(enterpriseData.enterprise_upload_attachment[0].erf);
        $('#file_household_info').val(enterpriseData.enterprise_upload_attachment[0].household_info); */
        if (enterpriseData.enterprise_upload_attachment[0].service_tax_document != null) {
            $('#show_service_tax_docs').show();
            $('#show_service_tax_docs').html('<a href="' + enterpriseData.enterprise_upload_attachment[0].service_tax_document + '" target="_blank">Link For Document</a>');
        }
        if (enterpriseData.enterprise_upload_attachment[0].pan_card != null) {
            $('#show_pan_card').show();
            $('#show_pan_card').html('<a href="' + enterpriseData.enterprise_upload_attachment[0].pan_card + '" target="_blank">Link For Document</a>');
        }
        if (enterpriseData.enterprise_upload_attachment[0].cancelled_cheque != null) {
            $('#show_cancelled_chque').show();
            $('#show_cancelled_chque').html('<a href="' + enterpriseData.enterprise_upload_attachment[0].cancelled_cheque + '" target="_blank">Link For Document</a>');
        }
        if (enterpriseData.enterprise_upload_attachment[0].erf != null) {
            $('#show_file_erf').show();
            $('#show_file_erf').html('<a href="' + enterpriseData.enterprise_upload_attachment[0].erf + '" target="_blank">Link For Document</a>');
        }
        if (enterpriseData.enterprise_upload_attachment[0].household_info != null) {
            $('#show_household_info').show();
            $('#show_household_info').html('<a href="' + enterpriseData.enterprise_upload_attachment[0].household_info + '" target="_blank">Link For Document</a>');
        }
        var attachment_hard_copies = enterpriseData.enterprise_upload_attachment[0].attachment_hard_copies;
        if (document_attached == 0) {
            $('#hard_copies_no').attr("checked", true);
        } else {
            $('#hard_copies_yes').attr("checked", true);
        }
        $('#media_station').val(enterpriseData.enterprise_upload_attachment[0].mediastation_status);
    }

    if (enterpriseData.enterprise_channel_details == null) {
        $('.no_channel').show();
        $('.check_enterprise_channel_exist').val('');
        $('.channel_details').hide();
        $('#vertical_menu').hide();
        $('#dropdown_scroll').hide();
    } else if (enterpriseData.enterprise_channel_details != null) {
        //console.log(typeof  enterpriseData.enterprise_channel_details +"--"+ enterpriseData.enterprise_channel_details.length);
        var htmlStr = '';
        for (var count = 0; count < enterpriseData.enterprise_channel_details.length; count++) {
            var class_param = '';
            if (count == 0) {
                class_param = 'class ="active"';
            }
            htmlStr += "<li " + class_param + "><a href='javascript:void(0);'><span>" + enterpriseData.enterprise_channel_details[count].channel_name + "</span></a></li>";
        }

        $('#vertical_menu').show();
        $('.channel_details').show();
        $('.vertical_menu_ul').html(htmlStr);
        $('.vertical_menu_ul').css("margin-top", "0px");
        $('#channel_index').val(0);
        $("#genere").val("");
        $("#genere").trigger("chosen:updated");
        $("#language").val("");
        $("#language").trigger("chosen:updated");
        if (!(enterpriseData.enterprise_channel_details[0].has_value)) {
            $('.no_channel').hide();
            $('.check_enterprise_channel_exist').val('new');
            $('.channel_details').val('');
            $('.error_msg_channel').show();
            $('#channel_id').val(enterpriseData.enterprise_channel_details[0].channel_id);
        } else {
            $('#channel_status').val(enterpriseData.enterprise_channel_details[0].channel_status);
            $('#channel_deployment_status').val(enterpriseData.enterprise_channel_details[0].deployment_status);
            $('#channel_contact_person1').val(enterpriseData.enterprise_channel_details[0].contact_person1);
            $('#channel_email_id1').val(enterpriseData.enterprise_channel_details[0].email_id1);

            if (enterpriseData.enterprise_channel_details[0].contact_person2 !== null) {
                $('#channel_contact_person2').val(enterpriseData.enterprise_channel_details[0].contact_person2);
            } else {
                $('#channel_contact_person2').val('');
            }
            if (enterpriseData.enterprise_channel_details[0].contact_person3 !== null) {
                $('#channel_contact_person3').val(enterpriseData.enterprise_channel_details[0].contact_person3);
            } else {
                $('#channel_contact_person3').val('');
            }

            if (enterpriseData.enterprise_channel_details[0].email_id2 !== null) {
                $('#channel_email_id2').val(enterpriseData.enterprise_channel_details[0].email_id2);
            } else {
                $('#channel_email_id2').val('');
            }
            if (enterpriseData.enterprise_channel_details[0].email_id3 !== null) {
                $('#channel_email_id3').val(enterpriseData.enterprise_channel_details[0].email_id3);
            } else {
                $('#channel_email_id3').val('');
            }

            $('#channel_contact_number1').val(enterpriseData.enterprise_channel_details[0].contact_number1);
            $('#channel_contact_number2').val(enterpriseData.enterprise_channel_details[0].contact_number2);
            $('#channel_contact_number3').val(enterpriseData.enterprise_channel_details[0].contact_number3);

            $.each((enterpriseData.enterprise_channel_details[0].genre).split(","), function (i, e) {
                $("#genere option[value='" + e + "']").prop("selected", true);
            });
            $("#genere").trigger("chosen:updated");
	

	    if (enterpriseData.enterprise_channel_details[0].dominant_content !== null) {
            	$.each((enterpriseData.enterprise_channel_details[0].dominant_content).split(","), function (i, e) {
                	$("#dominant_content option[value='" + e + "']").prop("selected", true);
            	});
	    }
            $("#dominant_content").trigger("chosen:updated");

            $.each((enterpriseData.enterprise_channel_details[0].language).split(","), function (i, e) {
                $("#language option[value='" + e + "']").prop("selected", true);
            });
            $("#language").trigger("chosen:updated");
            /*$('#genere').val(enterpriseData.enterprise_channel_details[0].genre);
			$('#language').val(enterpriseData.enterprise_channel_details[0].language);*/
            $('#channel_address_details').val(enterpriseData.enterprise_channel_details[0].address);
            $('#channel_id').val(enterpriseData.enterprise_channel_details[0].channel_id);
            $('.check_enterprise_channel_exist').val('exist');
            $('.error_msg_channel').hide();
            $('.no_channel').hide();
        }
    }
}

function saveData(params) {
    $('#resultLoading').show();
    $.ajax({
        type: "POST",
        url: "save_enterprise_data",
        dataType: 'text',
        //	async:false,
        data: params,
        success: function (res) {
            $('#resultLoading').hide();
            var sucess = (res.split("~")[0]).trim();
            var status = (res.split("~")[1]).trim();
            if (sucess) {
                excelObjForAllEnterpriseDetails = enterprise_channel_json;
                if (finance == status) {
                    $('.finance_enterprise_exist').val('exist');
                    alert("finance data sucesfully Saved");
                }
                if (contact == status) {
                    $('.contact_enterprise_exist').val('exist');
                    alert("Customer contact data sucessfully Saved");
                }
                if (channel == status) {
                    $('.check_enterprise_channel_exist').val('exist');
                    alert("Channel contact  data sucesfully Saved");
                }

            }
        },
        error: function (e) {
            alert("failed to Send the data");
        }
    });
}

function showMenus() {
    //console.log($('.vertical_menu_ul>li').length);
}

function expandall() {
    $('.vertical_menu_ul>li').show();
}

//File Upload
function add_file(id, file) {
    var template = '' +
        '<div class="file" id="uploadFile' + id + '">' +
        '<div class="info">' +
        '#1 - <span class="filename" title="Size: ' + file.size + 'bytes - Mimetype: ' + file.type + '">' + file.name + '</span><br /><small>Status: <span class="status">Waiting</span></small>' +
        '</div>' +
        '<div class="bar">' +
        '<div class="progress" style="width:0%"></div>' +
        '</div>' +
        '</div>';

    $('#fileList').prepend(template);
}

function update_file_status(id, status, message) {
    $('#uploadFile' + id).find('span.status').html(message).addClass(status);
}

function update_file_progress(id, percent) {
    $('#uploadFile' + id).find('div.progress').width(percent);
}

// Upload Plugin itself
$('#drag-and-drop-zone').dmUploader({
    url: '/demos/dnd/upload.php',
    dataType: 'json',
    allowedTypes: 'image/*',
    /*extFilter: 'jpg;png;gif',*/
    onInit: function () {
        add_log('Penguin initialized :)');
    },
    onBeforeUpload: function (id) {
        add_log('Starting the upload of #' + id);

        update_file_status(id, 'uploading', 'Uploading...');
    },
    onNewFile: function (id, file) {
        add_log('New file added to queue #' + id);

        add_file(id, file);
    },
    onComplete: function () {
        add_log('All pending tranfers finished');
    },
    onUploadProgress: function (id, percent) {
        var percentStr = percent + '%';

        update_file_progress(id, percentStr);
    },
    onUploadSuccess: function (id, data) {
        add_log('Upload of file #' + id + ' completed');

        add_log('Server Response for file #' + id + ': ' + JSON.stringify(data));

        update_file_status(id, 'success', 'Upload Complete');

        update_file_progress(id, '100%');
    },
    onUploadError: function (id, message) {
        add_log('Failed to Upload file #' + id + ': ' + message);

        update_file_status(id, 'error', message);
    },
    onFileTypeError: function (file) {
        add_log('File \'' + file.name + '\' cannot be added: must be an image');

    },
    onFileSizeError: function (file) {
        add_log('File \'' + file.name + '\' cannot be added: size excess limit');
    },
    /*onFileExtError: function(file){
      $.danidemo.addLog('#demo-debug', 'error', 'File \'' + file.name + '\' has a Not Allowed Extension');
    },*/
    onFallbackMode: function (message) {
        alert('Browser not supported(do something else here!): ' + message);
    }
});
