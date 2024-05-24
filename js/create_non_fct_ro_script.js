var BASE_URL = '/surewaves_easy_ro';

var validator = $('#non_fct_ro_form').validate({
    errorClass: 'invalid_error',
    errorElement: 'div',
    rules: {
        txt_ext_ro: {
            required: true,
            check_ro_existence: true
        },
        sel_agency: {
            check_agency: true,
        },
        sel_client: {
            check_client: true,
        },
        fin_year: {
            check_year: true,
        },
        "amount[january]": {
            required: true,
            check_amount: true,
            check_for_zero: true
        },
        "amount[february]": {
            required: true,
            check_amount: true,
            check_for_zero: true
        },
        "amount[march]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        "amount[april]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        "amount[may]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        "amount[june]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        "amount[july]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        "amount[august]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        "amount[september]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        "amount[october]": {
            required: true,
            check_amount: true,
            check_for_zero: true
        },
        "amount[november]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        "amount[december]": {
            required: true,
            check_amount: true,
            check_for_zero: true

        },
        regionSelectBox: {
            required: true,
        },
        txt_spcl_inst: {
            check_inst: true
        },
        txt_agency_com: {
            required: true,
            check_com: true
        }
    },
    messages: {
        txt_ext_ro: {
            required: "Please enter external Ro",

        },
        regionSelectBox: {
            required: "region required for account manager",
        },

        "amount[january]": {
            required: "Please enter amount for january",
            check_amount: "Please enter valid amount for january",
            check_for_zero: "All month amount value cannot be zero"
        },
        "amount[february]": {
            required: "Please enter amount for feburary",
            check_amount: "Please enter valid amount for february",
            check_for_zero: "All month amount value cannot be zero"
        },
        "amount[march]": {
            required: "Please enter amount for march",
            check_amount: "Please enter valid amount for march",
            check_for_zero: "All month amount value cannot be zero"


        },
        "amount[april]": {
            required: "Please enter amount for april",
            check_amount: "Please enter valid amount for april",
            check_for_zero: "All month amount value cannot be zero"

        },
        "amount[may]": {
            required: "Please enter amount for may",
            check_amount: "Please enter valid amount for may",
            check_for_zero: "All month amount value cannot be zero"

        },
        "amount[june]": {
            required: "Please enter amount for june",
            check_amount: "Please enter valid amount for june",
            check_for_zero: "All month amount value cannot be zero"

        },
        "amount[july]": {
            required: "Please enter amount for july",
            check_amount: "Please enter valid amount for july",
            check_for_zero: "All month amount value cannot be zero"

        },
        "amount[august]": {
            required: "Please enter amount for august",
            check_amount: "Please enter valid amount for august",
            check_for_zero: "All month amount value cannot be zero"

        },
        "amount[september]": {
            required: "Please enter amount for september",
            check_amount: "Please enter valid amount for september",
            check_for_zero: "All month amount value cannot be zero"
        },
        "amount[october]": {
            required: "Please enter amount for october",
            check_amount: "Please enter valid amount for october",
            check_for_zero: "All month amount value cannot be zero"
        },
        "amount[november]": {
            required: "Please enter amount for november",
            check_amount: "Please enter valid amount for november",
            check_for_zero: "All month amount value cannot be zero"

        },
        "amount[december]": {
            required: "Please enter amount for december",
            check_amount: "Please enter valid amount for december",
            check_for_zero: "All month amount value cannot be zero"

        },
        "txt_agency_com": {
            required: "Please enter expense amount"
        }
    },
    submitHandler: function (form, event) {
        event.preventDefault();
        $.ajax(BASE_URL + '/non_fct_ro/postCreateNonFctRo', {
            type: 'POST',
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");

            },
            complete: function () {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");

            },
            data: {
                "txt_ext_ro": $('#txt_ext_ro').val(),
                "sel_agency": $('#sel_agency').val(),
                "sel_agency_contact": $('#sel_agency_contact').val(),
                "sel_client": $('#sel_client').val(),
                "sel_client_contact": $('#sel_client_contact').val(),
                "txt_am_name": $('#txt_am_name').val(),
                "hid_user_id": $('#hid_user_id').val(),
                "user_type": $('#user_type').val(),
                "regionSelectBox": $('#regionSelectBox').attr('data'),
                "fin_year": $('#fin_year').val(),
                "amount[january]": $('#jan_amount').val(),
                "amount[february]": $('#feb_amount').val(),
                "amount[march]": $('#march_amount').val(),
                "amount[april]": $('#april_amount').val(),
                "amount[may]": $('#may_amount').val(),
                "amount[june]": $('#june_amount').val(),
                "amount[july]": $('#july_amount').val(),
                "amount[august]": $('#aug_amount').val(),
                "amount[september]": $('#sep_amount').val(),
                "amount[october]": $('#oct_amount').val(),
                "amount[november]": $('#nov_amount').val(),
                "amount[december]": $('#dec_amount').val(),
                "txt_gross": $('#txt_gross').val(),
                "txt_agency_com": $('#txt_agency_com').val(),
                "txt_spcl_inst": $('#txt_spcl_inst').val()

            },

            success: function (data) {
                alert("ro submitted");
                $('#myModal').modal('hide');
                window.location.href=BASE_URL+'/non_fct_ro/home';
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");

            },
            error: function (jqxhr) {
                alert("something wrong");
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                // $('#myModal').modal('hide');
            }
        })
    }
});

$('document').ready(function () {

    /***********Add Agency****************/
    $('#non_fct_add_agency_span').click(function () {
        var ext_ro = $('#txt_ext_ro').val();
        $('#sub_modal_title').text('Add Agency');
        $.ajax(BASE_URL + '/non_fct_ro/add_agency/', {
            type: 'POST',
            data: {
                "txt_ext_ro": ext_ro,
            },
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spinner').css('display', 'block');
            },
            success: function (data) {
                $('#loader_background').css("display", "none");
                $('#loader_spinner').css('display', 'none');
                $('#non_fct_ro_modal').modal('show');
                $("#non_fct_ro_modal").data('bs.modal')._config.backdrop = 'static';
                $('#sub_modal_body').html(data);
            }
        })


    });
    /**********Activate Client*************/
    $('#non_fct_activate_client_span').click(function () {

        var ext_ro = $('#txt_ext_ro').val();
        $('#sub_modal_title').text('Activate Client');
        $.ajax(BASE_URL + '/non_fct_ro/activate_client/', {
            type: 'POST',
            data: {
                "txt_ext_ro": ext_ro,
            },
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spinner').css('display', 'block');
            },
            success: function (data) {
                $('#loader_background').css("display", "none");
                $('#loader_spinner').css('display', 'none');
                $('#non_fct_ro_modal').modal('show');
                $("#non_fct_ro_modal").data('bs.modal')._config.backdrop = 'static';
                $('#sub_modal_body').html(data);
            }
        })


    });
    /*************Agency Contact*********/
    $("#sel_agency").change(function () {
        $.ajax(BASE_URL + "/account_manager/get_agency_info", {
            type: 'POST',
            data: {"agency": $('#sel_agency').val()},
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spinner').css('display', 'block');
            },
            success: function (data) {
                $('#loader_background').css("display", "none");
                $('#loader_spinner').css('display', 'none');
                $("#sel_agency_contact").html(data);
            }
        });

    });

    /**********Client Contact*********/

    $("#sel_client").change(function () {
        $.ajax(BASE_URL + "/account_manager/get_client_info", {
            type: 'POST',
            data: {"client": $("#sel_client").val()},
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spinner').css('display', 'block');
            },
            success: function (data) {
                $('#loader_background').css("display", "none");
                $('#loader_spinner').css('display', 'none');
                $("#sel_client_contact").html(data);
            }
        });

    });

    /**********Add Edit Agency Contact************/
    $('#non_fct_add_edit_agency_contact_span').click(function () {
        if (validator.element("#sel_agency")) {
            var ro_number = $('#txt_ext_ro').val();
            var selected_agency = $('#sel_agency').val();
            var selected_client = $('#sel_client').val();
            var selected_client_contact = $('#sel_client_contact').val();
            var selected_agency_contact = $('#sel_agency_contact').val();
            if (selected_agency_contact == 'new') {
                $('#sub_modal_title').text('Add Agency Contact');
            } else {
                $('#sub_modal_title').text('Update Agency Contact');
            }
            $.ajax(BASE_URL + '/non_fct_ro/add_edit_agency_contact/', {
                type: 'POST',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spinner').css('display', 'block');
                },
                complete: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spinner').css('display', 'none');
                },
                data: {
                    "txt_ext_ro": ro_number,
                    "sel_agency": selected_agency,
                    "sel_agency_contact": selected_agency_contact,
                    "sel_client": selected_client,
                    "sel_client_contact": selected_client_contact
                },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spinner').css('display', 'none');
                    $('#non_fct_ro_modal').modal('show');
                    $("#non_fct_ro_modal").data('bs.modal')._config.backdrop = 'static';
                    $('#sub_modal_body').html(data);
                }

            });

        }

    });

    /********Add Edit Client Contact************/
    $('#non_fct_add_edit_client_contact_span').click(function () {
        if (validator.element('#sel_client')) {
            var ro_number = $('#txt_ext_ro').val();
            var selected_agency = $('#sel_agency').val();
            var selected_client = $('#sel_client').val();
            var selected_client_contact = $('#sel_client_contact').val();
            var selected_agency_contact = $('#sel_agency_contact').val();
            if (selected_client_contact == 'new') {
                $('#sub_modal_title').text('Add Client Contact');
            } else {
                $('#sub_modal_title').text('Update Client Contact');
            }
            $.ajax(BASE_URL + '/non_fct_ro/add_edit_client_contact/', {
                type: 'POST',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spinner').css('display', 'block');
                },
                complete: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spinner').css('display', 'none');
                },
                data: {
                    "txt_ext_ro": ro_number,
                    "sel_agency": selected_agency,
                    "sel_agency_contact": selected_agency_contact,
                    "sel_client": selected_client,
                    "sel_client_contact": selected_client_contact
                },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spinner').css('display', 'none');
                    $('#non_fct_ro_modal').modal('show');
                    $("#non_fct_ro_modal").data('bs.modal')._config.backdrop = 'static';
                    $('#sub_modal_body').html(data);
                }

            });

        }
    });

    var response = true;

    //method to check wheter the ro exist in db or not
    $.validator.addMethod("check_ro_existence", function (value, element) {
        $.ajax(BASE_URL + '/non_fct_ro/check_for_non_fct_ro_existence', {
            type: "POST",
            async: true,
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            complete: function () {
                $('#loading_imag').css("display", "none");
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

    $.validator.addMethod("check_year", function (value, element) {
        if ($('#fin_year').val() == null) {
            return false;
        } else {
            return true;
        }
    }, "Please select a year");

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

    $.validator.addMethod("check_amount", function (value, element) {
        //console.log(value);
        var regExp = /^(\d*\.)?\d+$/;
        if (regExp.test(value)) {
            return true;
        } else {
            return false;
        }

    }, "Invalid input");

    $.validator.addMethod("check_for_zero", function (value, element) {
        if ($('#jan_amount').val() == 0 && $('#feb_amount').val() == 0 && $('#march_amount').val() == 0 && $('#april_amount').val() == 0 && $('#may_amount').val() == 0 && $('#june_amount').val() == 0 && $('#july_amount').val() == 0 && $('#aug_amount').val() == 0 && $('#sep_amount').val() == 0 && $('#oct_amount').val() == 0 && $('#nov_amount').val() == 0 && $('#dec_amount').val() == 0) {
            return false;
        } else {
            return true;
        }
    }, "Value cannot be zero");

    $.validator.addMethod("check_com", function (value, element) {
        var regex = /^\d+\.\d{1,2}$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Enter positive decimal Value upto  2 places");

    /*******Modal Close Button*****/
    $('#sub_modal_btn').click(function () {
        $('#non_fct_ro_modal').modal('hide');
    });
});

function calculate_gross() {
    var monthly_amount = 0;
    var final_amount = 0;
    $('.fin_amount').each(function () {

        monthly_amount = parseFloat(this.value);
        if (monthly_amount < 0) {
            final_amount = 0;
            return false;

        } else {
            final_amount += monthly_amount;
        }

    });
    validator.element('#jan_amount');
    validator.element('#feb_amount');
    validator.element('#march_amount');
    validator.element('#april_amount');
    validator.element('#may_amount');
    validator.element('#june_amount');
    validator.element('#july_amount');
    validator.element('#aug_amount');
    validator.element('#sep_amount');
    validator.element('#oct_amount');
    validator.element('#nov_amount');
    validator.element('#dec_amount');
    $('#txt_gross').val(final_amount.toFixed(2));
}

