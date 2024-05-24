var BASE_URL = '/surewaves_easy_ro';
$('document').ready(function () {

    var operation = $("#sub_modal_title").text().split(' ').splice(0, 1);
    if (operation == 'Add') {
        $('#btn_td').append('<button type="submit" class="btn btn-primary" id="add_edit_btn">Add</button>')
    } else if (operation == 'Update') {
        $('#btn_td').append('<button type="submit" class="btn btn-primary" id="add_edit_btn">Update</button>')
    }
    $('#txt_client_name').val($('#sel_client').val());
    /********Validation********/
    var validator = $('#non_add_edit_client_contact_form').validate({
        errorClass: 'invalid_error',
        errorElement: 'div',
        rules: {
            txt_client_contact_name: {
                required: true,
                check_client_contact_name: true
            },
            txt_client_contact_no: {
                required: true,
                check_client_contact_no: true
            },
            txt_client_address: {
                required: true,
                //check_for_special_character: true
            },
            txt_client_email: {
                required: true,
                check_client_email: true
            },
            txt_client_billing_info: {
                required: true,
              //  check_for_special_character: true
            },
            txt_client_billing_address: {
                required: true,
                //check_for_special_character: true
            },
            txt_client_pay_cycle: {
                required: true,
                //check_for_special_character: true
            }
        },
        messages: {
            txt_client_contact_name: {
                required: "Please enter client contact name",
            },
            txt_client_contact_no: {
                required: "Please enter client contact no",
            },
            txt_client_address: {
                required: "Please enter client address",
            },
            txt_client_email: {
                required: "Please enter client email",
            },
            txt_client_billing_info: {
                required: "Please enter billing info",
            },
            txt_client_billing_address: {
                required: "Please enter billing address",
            },
            txt_client_pay_cycle: {
                required: "Please enter client pay cycle",
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $.ajax(BASE_URL + '/non_fct_ro/post_add_edit_client_contact', {
                type: 'POST',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spinner').css('display', 'block');
                },
                data: {
                    "txt_client_name": $('#txt_client_name').val(),
                    "txt_client_contact_name": $('#txt_client_contact_name').val(),
                    "txt_client_contact_no": $('#txt_client_contact_no').val(),
                    "txt_client_display_name": $('#txt_client_name').val(),
                    "txt_client_designation": $('#txt_client_designation').val(),
                    "txt_client_location": $('#txt_client_location').val(),
                    "txt_client_address": $('#txt_client_address').val(),
                    "txt_client_email": $('#txt_client_email').val(),
                    "txt_client_billing_info": $('#txt_client_billing_info').val(),
                    "txt_client_billing_address": $('#txt_client_billing_address').val(),
                    "txt_client_pay_cycle": $('#txt_client_pay_cycle').val(),
                    "hid_cust_ro": $('#hid_cust_ro').val(),
                    "hid_ro_date": $('#hid_ro_date').val(),
                    "hid_agency": $('#hid_agency').val(),
                    "hid_agency_contact": $('#hid_agency_contact').val(),
                    "hid_am_edit": $('#hid_am_edit').val(),
                    "hid_client": $('#hid_client').val(),
                    "client_contact_val": $('#sel_client_contact').val()
                },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spinner').css('display', 'none');
                    alert("data submitted");
                    $.ajax(BASE_URL + "/account_manager/get_client_info", {
                        type: 'POST',
                        data: {"client": $("#sel_client").val()},
                        beforeSend: function () {
                            $('#loader_background').css("display", "block");
                            $('#loader_spin').css('display', 'block');
                        },
                        success: function (data) {
                            $('#loader_background').css("display", "none");
                            $('#loader_spin').css('display', 'none');
                            $("#sel_client_contact").html(data);
                        }
                    });
                    $('#non_fct_ro_modal').modal('hide');
                    }
            })
        }
    });
    //For checking the name entered is correct or not
    $.validator.addMethod("check_client_contact_name", function (value, element) {
        var regex = /^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Name entered is incorrect");

    //for checking the phone number entered is correct or not
    $.validator.addMethod("check_client_contact_no", function (value, element) {
        var regex = /^\d{10}$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Phone Number entered is incorrect");

    $.validator.addMethod("check_client_email", function (value, element) {
        var regex = /^(\s?[^\s,]+@[^\s,]+\.[^\s,]+\s?,)*(\s?[^\s,]+@[^\s,]+\.[^\s,]+)$/;
        var returnval = regex.test(value);
        if (returnval) {
            return true;
        } else {
            return false;
        }

    }, "Email entered is incorrect");

    $.validator.addMethod("check_for_special_character", function (value, element) {
        var regex = /^[a-zA-Z0-9-_. ]*$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Special Characters not allowed");

});