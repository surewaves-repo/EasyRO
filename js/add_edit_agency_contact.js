$('document').ready(function () {

    var operation = $("#sub_modal_title").text().split(' ').splice(0, 1);
    if (operation == 'Add') {
        $('#btn_td').append('<button type="submit" class="btn btn-primary" id="add_edit_btn">Add</button>')
    } else if (operation == 'Update') {
        $('#btn_td').append('<button type="submit" class="btn btn-primary" id="add_edit_btn">Update</button>')
    }

    /*********************validation**********/
    $('#add_edit_agency_contact_form').validate({
        errorClass: 'invalid_error',
        errorElement: 'div',
        rules:
            {
                txt_add_edit_agency_contact_name: {
                    required: true,
                    check_agency_contact_name: true,
                },
                txt_add_edit_agency_contact_no: {
                    required: true,
                    check_agency_contact_no: true
                },
                txt_add_edit_agency_email: {
                    required: true,
                    check_agency_email: true
                },
                add_edit_agency_state: {
                    required: true,
                }
            },
        messages: {
            txt_add_edit_agency_contact_name: {
                required: "Please enter Contact name",
            },
            txt_add_edit_agency_contact_no: {
                required: "Please enter contact number",
            },
            txt_add_edit_agency_email: {
                required: "Please enter email",
            },
            add_edit_agency_state: {
                required: "Please select a state",
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            //console.log("working");
            var add_edit_agency_name = $('#txt_add_edit_agency_name').val();
            var add_edit_agency_contact_name = $('#txt_add_edit_agency_contact_name').val();
            var add_edit_agency_contact_no = $('#txt_add_edit_agency_contact_no').val();
            var add_edit_agency_contact_email = $('#txt_add_edit_agency_email').val();
            var add_edit_agency_state = $("#add_edit_agency_state").val();
            var hid_add_edit_cust_ro_value = $("#hid_add_edit_cust_ro").val();
            var hid_add_edit_ro_date_value = $("#hid_add_edit_ro_date").val();
            var hid_add_edit_agency_value = $("#hid_add_edit_agency").val();
            var hid_add_edit_agency_contact_value = $('#hid_add_edit_agency_contact').val();
            var hid_add_edit_client_value = $('#hid_add_edit_client').val();
            var hid_add_edit_client_contact_value = $("#hid_add_edit_client_contact").val();
            $.ajax(BASE_URL + '/account_manager/post_add_edit_agency_contact', {
                type: 'POST',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");
                    $("#add_edit_btn").attr("disabled", true);
                },
                complete: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#add_edit_btn').removeAttr("disabled");
                },
                dataType: 'json',
                async: 'true',
                data: {
                    "txt_agency_name": add_edit_agency_name,
                    "txt_agency_contact_name": add_edit_agency_contact_name,
                    "txt_agency_contact_no": add_edit_agency_contact_no,
                    "txt_agency_email": add_edit_agency_contact_email,
                    "agency_state": add_edit_agency_state,
                    "hid_cust_ro": hid_add_edit_cust_ro_value,
                    "hid_ro_date": hid_add_edit_ro_date_value,
                    "hid_agency": hid_add_edit_agency_value,
                    "hid_agency_contact": hid_add_edit_agency_contact_value,
                    "hid_client": hid_add_edit_client_value,
                    "hid_client_contact": hid_add_edit_client_contact_value
                },
                success: function (data, text, jqxhr) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    console.log(data);
                    if (!data.isLoggedIn) {
                        window.location.href = BASE_URL;
                        return false;
                    } else if (data.Status == "fail") {
                        $('#txt_agency_contact_name_error').empty();
                        $('#txt_agency_contact_no_error').empty();
                        $('#txt_agency_email_error').empty();
                        $('#agency_state_error').empty();

                        $('#add_edit_agency_error_div').empty();
                        if (Object.keys(data.Data).length > 0) {
                            // console.log("if");

                            if ("formValidation" in data.Data) {

                                $.map(data.Data.formValidation, function (value) {
                                    Object.keys(value).forEach(function eachKey(key) {
                                        var id = "#" + key;

                                        $(id + '_error').append('<label class="invalid">' + value[key] + '</label>');

                                    });
                                });
                            }
                        } else {

                            $('#add_edit_agency_error_div').append('<label class="invalid">' + data.Message + '</label>')
                            $('#add_edit_agency_error_div').css("display", 'block');
                        }
                    } else if (data.Status == 'success') {

                        if (Object.keys(data.Data).length == 0) {
                            alert(data.Message);
                            var selected_agency = $('#sel_agency').val();
                            $.ajax(BASE_URL + '/account_manager/get_agency_info', {
                                type: 'POST',
                                async: true,
                                data: {agency: selected_agency},
                                success: function (data) {
                                    $("#sel_agency_contact").html(data);
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

});
