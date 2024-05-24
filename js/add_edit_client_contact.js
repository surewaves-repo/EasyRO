$('document').ready(function () {
    var operation = $("#sub_modal_title").text().split(' ').splice(0, 1);
    if (operation == 'Add') {
        $('#client_btn_td').append('<button type="submit" class="btn btn-primary" id="add_edit_client_btn">Add</button>')
    } else if (operation == 'Update') {
        $('#client_btn_td').append('<button type="submit" class="btn btn-primary" id="add_edit_client_btn">Update</button>')
    }
    /*********************validation**********/
    $('#add_edit_client_contact_form').validate({
        errorClass: 'invalid_error',
        errorElement: 'div',
        rules:
            {
                txt_add_edit_client_contact_name: {
                    required: true,
                    check_client_contact_name: true,
                },
                txt_add_edit_client_contact_no: {
                    required: true,
                    check_client_contact_no: true
                },
                txt_add_edit_client_email: {
                    required: true,
                    check_client_email: true
                },
                add_edit_client_state: {
                    required: true,
                }
            },
        messages: {
            txt_add_edit_client_contact_name: {
                required: "Please enter Contact name",
            },
            txt_add_edit_client_contact_no: {
                required: "Please enter contact number",
            },
            txt_add_edit_client_email: {
                required: "Please enter email",
            },
            add_edit_client_state: {
                required: "Please select a state",
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            //console.log("working");
            var add_edit_client_name = $('#txt_add_edit_client_display_name').val();
            var add_edit_client_contact_name = $('#txt_add_edit_client_contact_name').val();
            var add_edit_client_contact_no = $('#txt_add_edit_client_contact_no').val();
            var add_edit_client_contact_email = $('#txt_add_edit_client_email').val();
            var add_edit_client_state = $("#add_edit_client_state").val();
            var hid_add_edit_cust_ro_value = $("#hid_add_edit_cust_ro").val();
            var hid_add_edit_ro_date_value = $("#hid_add_edit_ro_date").val();
            var hid_add_edit_agency_value = $("#hid_add_edit_agency").val();
            var hid_add_edit_agency_contact_value = $('#hid_add_edit_agency_contact').val();
            var hid_add_edit_client_value = $('#hid_add_edit_client').val();
            var hid_add_edit_client_contact_value = $("#hid_add_edit_client_contact").val();

            $.ajax(BASE_URL + '/account_manager/post_add_edit_client_contact', {
                type: 'POST',
                data: {
                    "txt_client_display_name": add_edit_client_name,
                    "txt_client_contact_name": add_edit_client_contact_name,
                    "txt_client_contact_no": add_edit_client_contact_no,
                    "txt_client_email": add_edit_client_contact_email,
                    "client_state": add_edit_client_state,
                    "hid_cust_ro": hid_add_edit_cust_ro_value,
                    "hid_ro_date": hid_add_edit_ro_date_value,
                    "hid_agency": hid_add_edit_agency_value,
                    "hid_agency_contact": hid_add_edit_agency_contact_value,
                    "hid_client": hid_add_edit_client_value,
                    "hid_client_contact": hid_add_edit_client_contact_value
                },
                dataType: 'json',
                async: true,
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                    $('#add_edit_client_btn').attr("disabled", true);
                },
                complete: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#add_edit_client_btn').attr("disabled", false);

                },
                success: function (data, text, jqxhr) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    if (!data.isLoggedIn) {
                        window.location.href = BASE_URL;
                        return false;
                    } else if (data.Status == "fail") {
                        $('#txt_client_contact_name_error').empty();
                        $('#txt_client_contact_no_error').empty();
                        $('#txt_client_email_error').empty();
                        $('#client_state_error').empty();
                        $('#add_edit_client_error_div').empty();
                        if (Object.keys(data.Data).length > 0) {


                            if ("formValidation" in data.Data) {

                                $.map(data.Data.formValidation, function (value) {
                                    Object.keys(value).forEach(function eachKey(key) {
                                        var id = "#" + key;

                                        $(id + '_error').append('<label class="invalid">' + value[key] + '</label>');

                                    });
                                });
                            }
                        } else {
                            $('#add_edit_client_error_div').append('<label class="invalid">' + data.Message + '</label>')
                            $('#add_edit_client_error_div').css("display", 'block');
                        }
                    } else if (data.Status == 'success') {

                        if (Object.keys(data.Data).length == 0) {
                            alert(data.Message);
                            var selected_client = $('#sel_client').val();
                            $.ajax(BASE_URL + '/account_manager/get_client_info', {
                                type: 'POST',
                                async: true,
                                data: {client: selected_client},
                                success: function (data) {
                                    $('#sel_client_contact').html(data);

                                }
                            });
                            $('#create_ext_Ro_modal').modal('hide');
                        }
                    }

                },
            });
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


});