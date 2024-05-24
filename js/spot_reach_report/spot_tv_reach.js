$(document).ready(function () {
    //$('#market_list option').prop('selected', true);

    var stickyOffset = $('.sub_content2_child_sub2_heading').offset().top;

    $(window).scroll(function () {
        var sticky = $('.sub_content2_child_sub2_heading'),
            scroll = $(window).scrollTop();

        if (scroll >= stickyOffset) sticky.addClass('fixed');
        else sticky.removeClass('fixed');
    });

    $(".channel_list").chosen({width: "100%"});
    $('.channel_list option:not(:selected)').prop("disabled", true);
    $(".channel_list").trigger("chosen:updated");

    $('.channel_list').change(function () {

        //selectedVal = $(this).val();
        var selected_id = $(this).attr("id");
        var selectedVal = $("#" + selected_id + " option:selected").val();

        if (selectedVal == 'all') {

            $("#" + selected_id + " option[value!='all']").attr("disabled", "disabled");
            $("#" + selected_id).trigger("chosen:updated");

        } else if (selectedVal != 'all') {

            $("#" + selected_id + " option").attr("disabled", false);
            $("#" + selected_id).trigger("chosen:updated");
        }
        if (selectedVal != 'all' && selectedVal != null) {

            $("#" + selected_id + " option[value='all']").attr("disabled", true);
            $("#" + selected_id).trigger("chosen:updated");

        }
        if (selectedVal == null || selectedVal == '') {
            $("#" + selected_id + " option").prop("disabled", false);
            $("#" + selected_id).trigger("chosen:updated");
        }

    });

    $('#report_data').click(function () {
        var filterSheetForDownload = '';
        $('.sheetFilter').each(function () {
            if ($(this).is(':checked')) {
                if (filterSheetForDownload == '') {
                    filterSheetForDownload = $(this).attr('data-marketId') + '_' + $(this).val();
                } else {
                    filterSheetForDownload += ',' + $(this).attr('data-marketId') + '_' + $(this).val();
                }
            }
        });
        $("#filtered_sheets").val(filterSheetForDownload);
        var excludeChannelId_list = '';
        var errorInselect = '';
        //var errorStat = 0;
        $('.tvh').removeClass('error');
        $('.cns').removeClass('error');
        $('.cabletv').removeClass('error');
        $('.sw_partner').removeClass('error');
        $('.share').removeClass('error');
        $('#total_households').removeClass('error');
        $('#total_tv_households').removeClass('error');
        $('#total_cable_sat_households').removeClass('error');
        $('.chosen-choices').removeClass('error_select');
        $('.channel_list').each(function () {
            var id = $(this).attr('id');
            if ($(this).val() == null || $(this).val() == '') {
                var id = $(this).attr('id');
                $('#' + id + '_chosen').find('.chosen-choices').addClass('error_select');
                errorInselect = 1;
                return false;
            } else {
                var valObj = $(this).val();
                if (valObj[0] !== 'all') {
                    for (var count = 0; count < valObj.length; count++) {
                        if (excludeChannelId_list == '') {
                            excludeChannelId_list = valObj[count];
                        } else {
                            excludeChannelId_list += "," + valObj[count];
                        }
                    }
                }
            }
        });
        if (errorInselect) {
            alert("Please enter the  values in field(s) marked red");
            $('html, body').animate({
                scrollTop: ($('.error_select').offset().top - 100)
            }, 2000);
            return false;
        }
        //console.log(excludeChannelId_list);
        $("#excluded_Channel_ids").val(excludeChannelId_list);
        document.generate_reach_data.submit();
    });
    $('#submit_data').click(function () {
        //var dataArr = {};
        var errorStat;
        var errorInselect = 0;
        var excludeChannelId_list = '';

        //$('.channel_list').removeClass('error');
        $('.tvh').removeClass('error');
        $('.cns').removeClass('error');
        $('.cabletv').removeClass('error');
        $('.sw_partner').removeClass('error');
        $('.share').removeClass('error');
        $('#total_households').removeClass('error');
        $('#total_tv_households').removeClass('error');
        $('#total_cable_sat_households').removeClass('error');
        $('.chosen-choices').removeClass('error_select');

        var regexp = /^[0-9]\d*(\.\d+)?$/g;

        if (($('#total_households').val()).trim() === null || ($('#total_households').val()).trim() === '') {
            alert("Please enter Total Household data.");
            $('#total_households').addClass('error');
            $('#total_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_households').offset().top - 100)
            }, 2000);
            return false;
        }

        if (($('#total_households').val()).match(regexp) === null) {
            alert("Please enter proper Total Household data.");
            $('#total_households').addClass('error');
            $('#total_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_households').offset().top - 100)
            }, 2000);
            return false;
        }
        if (Number(($('#total_households').val()).trim()) === 0) {
            alert("The Total Household data cannot be 0.");
            $('#total_households').addClass('error');
            $('#total_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_households').offset().top - 100)
            }, 2000);
            return false;
        }

        if (($('#total_tv_households').val()).trim() === null || ($('#total_tv_households').val()).trim() === '') {
            alert("Please enter Total Tv Household data.");
            $('#total_tv_households').addClass('error');
            $('#total_tv_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_tv_households').offset().top - 100)
            }, 2000);
            return false;
        }

        if (($('#total_tv_households').val()).match(regexp) === null) {
            alert("Please enter proper Total Tv Household data.");
            $('#total_tv_households').addClass('error');
            $('#total_tv_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_tv_households').offset().top - 100)
            }, 2000);
            return false;
        }
        if (Number(($('#total_tv_households').val()).trim()) === 0) {
            alert("The Total Tv Household data cannot be 0.");
            $('#total_tv_households').addClass('error');
            $('#total_tv_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_tv_households').offset().top - 100)
            }, 2000);
            return false;
        }
        if (($('#total_cable_sat_households').val()).trim() === null || ($('#total_cable_sat_households').val()).trim() === '') {
            alert("Please enter Total Cable and Satelite Household data.");
            $('#total_cable_sat_households').addClass('error');
            $('#total_cable_sat_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_cable_sat_households').offset().top - 100)
            }, 2000);
            return false;
        }

        if (($('#total_cable_sat_households').val()).match(regexp) === null) {
            alert("Please enter proper Total Cable and Satelite Household data.");
            $('#total_cable_sat_households').addClass('error');
            $('#total_cable_sat_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_cable_sat_households').offset().top - 100)
            }, 2000);
            return false;
        }
        if (Number(($('#total_cable_sat_households').val()).trim()) === 0) {
            alert("The Total Cable Satellite Household data cannot be 0.");
            $('#total_cable_sat_households').addClass('error');
            $('#total_cable_sat_households').focus();
            $('html, body').animate({
                scrollTop: ($('#total_cable_sat_households').offset().top - 100)
            }, 2000);
            return false;
        }

        $('.tvh').each(function () {

            if (!errorStat) {
                if (($(this).val()).trim() == null || ($(this).val()).trim() == '') {
                    $(this).addClass('error');
                    errorStat = 1;
                } else {
                    if (($(this).val()).match(regexp) === null) {
                        $(this).addClass('error');
                        errorStat = 2;
                    }
                }

            } else {
                return false;
            }
        });
        $('.cns').each(function () {
            if (!errorStat) {
                if (($(this).val()).trim() == null || ($(this).val()).trim() == '') {
                    $(this).addClass('error');
                    errorStat = 1;
                } else {
                    if (($(this).val()).match(regexp) === null) {
                        $(this).addClass('error');
                        errorStat = 2;
                    }
                }
            } else {
                return false;
            }

        });
        $('.cabletv').each(function () {
            if (!errorStat) {
                if (($(this).val()).trim() == null || ($(this).val()).trim() == '') {
                    $(this).addClass('error');
                    errorStat = 1;
                } else if (Number(($(this).val()).trim()) === 0) {
                    $(this).addClass('error');
                    errorStat = 3;
                } else {
                    if (($(this).val()).match(regexp) === null) {
                        $(this).addClass('error');
                        errorStat = 2;
                    }
                }
            } else {
                return false;
            }

        });
        $('.sw_partner').each(function () {
            if (!errorStat) {
                if (($(this).val()).trim() == null || ($(this).val()).trim() == '') {
                    $(this).addClass('error');
                    errorStat = 1;
                } else if (Number(($(this).val()).trim()) === 0) {
                    $(this).addClass('error');
                    errorStat = 3;
                } else {
                    if (($(this).val()).match(regexp) === null) {
                        $(this).addClass('error');
                        errorStat = 2;
                    }
                }
            } else {
                return false;
            }

        });
        $('.share').each(function () {
            if (!errorStat) {
                if (($(this).val()).trim() == null || ($(this).val()).trim() == '') {
                    $(this).addClass('error');
                    errorStat = 1;
                } else if (Number(($(this).val()).trim()) === 0) {
                    $(this).addClass('error');
                    errorStat = 3;
                } else {
                    if (($(this).val()).match(regexp) === null) {
                        $(this).addClass('error');
                        errorStat = 2;
                    }
                }
            } else {
                return false;
            }
        });

        //console.log(excludeChannelId_list);
        if (errorStat == 1) {
            $('html, body').animate({
                scrollTop: ($('.error').offset().top - 100)
            }, 2000);
            alert("Please enter the  values in field(s) marked red");
            return false;
        }
        if (errorStat == 2) {
            $('html, body').animate({
                scrollTop: ($('.error').offset().top - 100)
            }, 2000);
            alert("Please enter the proper values in field(s) marked red");
            return false;
        }
        if (errorStat == 3) {
            $('html, body').animate({
                scrollTop: ($('.error').offset().top - 100)
            }, 2000);
            alert("The value(s) of the field marked red,cannot be 0");
            return false;
        }
        //return false;
        document.spot_tv_reach_data.submit();

    });
});

function changeReachPercentage(id) {
    var regexp = /^[0-9]+\.?[0-9]*$/;
    var param1 = $('#cabletv_' + id).val();
    var param2 = $('#sw_partner_' + id).val();
    $('.tvh').removeClass('error');
    $('.cns').removeClass('error');
    $('.cabletv').removeClass('error');
    $('.sw_partner').removeClass('error');
    $('.share').removeClass('error');
    $('#total_households').removeClass('error');
    $('#total_tv_households').removeClass('error');
    $('#total_cable_sat_households').removeClass('error');
    $('.chosen-choices').removeClass('error_select');
    if (param1.match(regexp) === null) {
        $('#cabletv_' + id).addClass('error');
        alert("The value should be proper number or decimal number");
        $('#cabletv_' + id).val('');
        return false;
    }
    if (param2.match(regexp) === null) {
        $('#sw_partner_' + id).addClass('error');
        alert("The value should be proper number or decimal number");
        $('#sw_partner_' + id).val('');
        return false;
    }
    if (Number(param1) === 0) {
        $('#cabletv_' + id).addClass('error');
        alert("The field value cannot be 0.");
        $('#cabletv_' + id).val('');
        return false;
    }

    param1 = parseFloat(param1);
    param2 = parseFloat(param2);

    $('#share_' + id).val(Math.ceil((param2 / param1) * 100));

}
