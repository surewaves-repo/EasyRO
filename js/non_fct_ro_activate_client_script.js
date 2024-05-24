var BASE_URL = '/surewaves_easy_ro';

$('document').ready(function () {
    var validator = $('#non_fct_ro_activate_client_form').validate({
        errorClass: 'invalid_error',
        errorElement: 'div',
        rules: {
            sel_client: {
                check_client: true
            }

        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $.ajax(BASE_URL + "/non_fct_ro/post_activate_client", {
                type: 'POST',
                data: {
                    "sel_client": $('#sel_client').val()
                },
                success: function () {
                    alert('client activated');
                    $('#myModal').modal('hide');
                }

            });
        }
    });

    $.validator.addMethod("check_client", function (value, element) {
        if ($('#sel_client').val() == null) {
            return false;
        } else {
            return true;
        }
    }, "Please select a client");

});