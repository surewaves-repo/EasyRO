var BASE_URL = '/surewaves_easy_ro';

$('document').ready(function () {
    var operation = $("#sub_modal_title").text().split(' ').splice(0, 1);
    if (operation == 'Add') {
        $('#btn_td').append('<button type="submit" class="btn btn-primary" id="add_edit_btn">Add</button>')
    } else if (operation == 'Update') {
        $('#btn_td').append('<button type="submit" class="btn btn-primary" id="add_edit_btn">Update</button>')
    }
    $('#txt_agency_name').val($('#sel_agency').val());

    /********Validation*********/
    $('#non_fct_ro_add_edit_agency_contact_form').validate({
        errorClass: 'invalid_error',
        errorElement: 'div',
        rules: {
            txt_agency_contact_name: {
                required: true,
                check_agency_contact_name: true
            },
            txt_agency_contact_no: {
                required: true,
                check_agency_contact_no: true
            },
            txt_agency_address: {
                required: true,
                //check_for_special_character: true
            },
            txt_agency_email: {
                required: true,
                check_agency_email: true
            },
            txt_billing_info: {
                required: true,
                //check_for_special_character: true
            },
            txt_billing_address: {
                required: true,
                //check_for_special_character: true
            }
        },
        messages: {
            txt_agency_contact_name: {
                required: "Please enter agency contact name",
            },
            txt_agency_contact_no: {
                required: "Please enter agency contact no",
            },
            txt_agency_address: {
                required: "Please enter agency address",
            },
            txt_agency_email: {
                required: "Please enter agency email",
            },
            txt_billing_info: {
                required: "Please enter billing info",
            },
            txt_billing_address: {
                required: "Please enter billing address",
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $.ajax(BASE_URL + '/non_fct_ro/post_add_edit_agency_contact', {
                type: 'POST',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spinner').css('display', 'block');
                },
                data: {
                     "txt_agency_name": $('#txt_agency_name').val(),
                     "txt_agency_contact_name": $('#txt_agency_contact_name').val(),
                     "txt_agency_contact_no": $('#txt_agency_contact_no').val(),
                     "txt_agency_designation": $('#txt_agency_designation').val(),
                     "txt_agency_location": $('#txt_agency_location').val(),
                     "txt_agency_address": $('#txt_agency_address').val(),
                     "txt_agency_email": $('#txt_agency_email').val(),
                     "txt_billing_info": $('#txt_billing_info').val(),
                     "txt_billing_address": $('#txt_billing_address').val(),
                     "txt_pay_cylce": $('#txt_pay_cylce').val(),
                     "hid_cust_ro": $('#hid_cust_ro').val(),
                     "hid_ro_date": $('#hid_ro_date').val(),
                     "hid_agency": $('#hid_agency').val(),
                      "agency_contact_val":$('#sel_agency_contact').val(),
                     "hid_am_edit": $('#hid_am_edit').val()
                 },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spinner').css('display', 'none');
                    alert("data submitted");
                    $.ajax(BASE_URL + "/account_manager/get_agency_info", {
                        type: 'POST',
                        data: {"agency": $('#sel_agency').val()},
                        beforeSend: function () {
                            $('#loader_background').css("display", "block");
                            $('#loader_spin').css('display', 'block');
                        },
                        success: function (data) {
                            $('#loader_background').css("display", "none");
                            $('#loader_spin').css('display', 'none');
                            $("#sel_agency_contact").html(data);
                        }
                    });
                    $('#non_fct_ro_modal').modal('hide');
                  }
            })
        }
    });

    //For checking the name entered is correct or not
    $.validator.addMethod("check_agency_contact_name", function (value, element) {
        var regex = /^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Name entered is incorrect");

    //for checking the phone number entered is correct or not
    $.validator.addMethod("check_agency_contact_no", function (value, element) {
        var regex = /^\d{10}$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Phone Number entered is incorrect");

    $.validator.addMethod("check_agency_email", function (value, element) {
        var regex = /^(\s?[^\s,]+@[^\s,]+\.[^\s,]+\s?,)*(\s?[^\s,]+@[^\s,]+\.[^\s,]+)$/;
        var returnval = regex.test(value);
        if (returnval) {
            return true;
        } else {
            return false;
        }

    }, "Email entered is incorrect");

    // $.validator.addMethod("check_for_special_character", function (value, element) {
    //     var regex = /^[a-zA-Z0-9-_. ]*$/;
    //     if (regex.test(value)) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }, "Special Characters not allowed");

});