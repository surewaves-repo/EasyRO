var BASE_URL = '/surewaves_easy_ro';

$('document').ready(function () {
    var validator = $('#non_fct_ro_add_agency_form').validate({
        errorClass: 'invalid_error',
        errorElement: 'div',
        rules: {
            txt_agency_name: {
                required: true,
                check_for_special_character: true,
                maxlength: 75
            },
            txt_agency_billing_name: {
                required: true,
                check_for_special_character: true
            },
            txt_agency_address: {
                required: true,
                check_for_special_character: true
            },

        },
        messages: {
            txt_agency_name: {
                required: "Please enter agency name",
                maxlength: "Agency name should be less than 75 characters"
            },
            txt_agency_billing_name: {
                required: "Please enter agency billing name",
            },
            txt_agency_address: {
                required: "Please enter agency address",
            },
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $.ajax(BASE_URL + '/non_fct_ro/post_add_agency', {
                type: 'POST',
                data: {
                    "txt_agency_name": $('#txt_agency_name').val(),
                    "txt_agency_billing_name": $('#txt_agency_billing_name').val(),
                    "txt_agency_address": $("#txt_agency_address").val()
                },
                success: function (data) {
                    alert("Agency submitted");
                    $('#non_fct_ro_modal').modal('hide');
                 }

            });
        }

    });
    $.validator.addMethod("check_for_special_character", function (value, element) {
        var regex = /^[a-zA-Z0-9-_. ]*$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Special Characters not allowed");
});