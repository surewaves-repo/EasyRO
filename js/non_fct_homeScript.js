var BASE_URL = '/surewaves_easy_ro';
$('document').ready(function(){

    //Function to handle click event on submit External button and loading the view in the modal
    $('#submit_external_ro_btn').click(function () {

        if ($("#header").attr('data') != 2) {
            $('#modal_title').text("SUBMIT EXTERNAL RO");
            $.ajax(BASE_URL + '/account_manager/create_ext_ro', {
                async: true,
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");

                },
                complete: function () {
                    $('#loading_image').css("display", "none");
                },
                success: function (data) {
                    console.log("inside success");
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    if (!data.isLoggedIn) {
                        window.location.href = BASE_URL;
                        return false;
                    } else if (data.Status == "success") {
                        $('#myModal').modal('show');
                        $("#myModal").data('bs.modal')._config.backdrop = 'static';
                        console.log(data);
                        var trimData = $.trim(data.Data.html);
                        $('<link>').attr('rel', 'stylesheet')
                            .attr('type', 'text/css')
                            .attr('href', '/surewaves_easy_ro/css/create_ext_ro_style.css')
                            .appendTo('head');

                        var parseHtml = $.parseHTML(data.Data.html);
                        $('#Modal_body').html(parseHtml);
                        $.getScript('/surewaves_easy_ro/js/create_ext_ro_script.js').done(function () {

                        }).fail(function () {
                            }
                        );

                    }


                },
                error: function (jqxhr) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    //console.log(jqxhr.status);
                    alert('Some technical error');

                }
            });
        } else {
            $('#modal_title').text('SUBMIT ADVANCED EXTERNAL RO');
            $.ajax(BASE_URL + '/advanced_ro_manager/create_advanced_ext_ro/', {
                async: true,
                beforeSend: function () {
                    $('#loader_background').css("display", "block");
                    $('#loader_spin').css("display", "block");

                },
                complete: function () {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");

                },
                success: function (data) {
                    $('#loader_background').css("display", "none");
                    $('#loader_spin').css("display", "none");
                    $('#myModal').modal('show');
                    $("#myModal").data('bs.modal')._config.backdrop = 'static';
                    $('#Modal_body').html(data);


                }
            });

        }

    });

    //function to handle click event on submit non fct Ro and loading the view in the modal
    $('#submit_non_fct_ro_btn').click(function () {
        $('#modal_title').text('Submit NON FCT RO');


        $.ajax(BASE_URL + '/account_manager/create_non_fct_ext_ro/', {
            async: true,
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");

            },
            complete: function () {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");

            },
            success: function (data) {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                $('#myModal').modal('show');
                $("#myModal").data('bs.modal')._config.backdrop = 'static';
                $('#Modal_body').html(data);

            }
        });
    });

    //clicking on fct will redirect us to account_manager/home which will load the view
    $('#show_fct').click(function () {
        window.location.href = BASE_URL + "/account_manager/home";
    });

    //clicking on non-fct will redirect us non_fct_ro/home which will load the view
    $('#show_non_fct').click(function () {
        window.location.href = BASE_URL + "/non_fct_ro/home";
    });

    //function to handle click on link ro button  and loading the view in the modal
    $('#link_ro_btn').click(function () {
        $('#modal_title').text('LINK RO');
        $.ajax(BASE_URL + '/advanced_ro_manager/link_advanced_ro/', {
            async: true,
            beforeSend: function () {
                $('#loader_background').css("display", "block");
                $('#loader_spin').css("display", "block");
            },
            complete: function () {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
            },
            success: function (data) {
                $('#loader_background').css("display", "none");
                $('#loader_spin').css("display", "none");
                $('#Modal_body').html(data);
                $('#myModal').modal('show');

            }
        });
    });
});