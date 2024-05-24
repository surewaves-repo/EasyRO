var BASE_URL = "/surewaves_easy_ro";
var schedule_data;
var SERVICE_TAX = 1.0;
var submit_ext;
var ro_id;
var internal_ro_no;
var external_ro;

$("document").ready(function() {
  $(".modal").on("shown.bs.modal", function() {
    $(".modal-body").animate({ scrollTop: 0 }, "fast");
  });

  $(document).on("click", "#req_market_can_content", function() {
    console.log("cancel");
    $("#req_market_can_content").prop("disabled", true);
    if ($("#txt_cancel_date_content").val() == "") {
      $("#req_market_can_content").prop("disabled", false);
      alert("Please enter date of cancel.");
      return false;
    }
    if ($("#reason_can").val() == "") {
      $("#req_market_can_content").prop("disabled", false);
      alert("Please enter Reason for Cancellation");
      $("#reason_can").focus();
      return false;
    }
    var data = $("#cancel_market_content_form").serializeArray();
    $.ajax(BASE_URL + "/account_manager/post_cancel_content_brand/", {
      type: "POST",
      data: data,
      dataType: "json",
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(data) {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        $("#cancellation_div").css("display", "block");
        $("#status_td").text("Cancel Requested");
        $("#cancel_ro_request_span").css("display", "none");
        $("#cancel_markets_span").css("display", "none");
        $("#cancel_markets_by_brand_span").css("display", "none");
        $("#cancel_markets_by_content_span").css("display", "none");
        alert(data.Message);
        $("#optionModal").modal("hide");
      },
      error: function() {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        alert("could not cancel market");
        $("#req_market_can_content").prop("disabled", false);
      }
    });
  });

  $(document).on("click", "#req_market_can_market", function() {
    $("#req_market_can_market").prop("disabled", true);
    if ($("#txt_cancel_date_market").val() == "") {
      $("#req_market_can_market").prop("disabled", false);
      alert("Please enter date of cancel.");
      return false;
    }
    if ($("#reason_can").val() == "") {
      $("#req_market_can_market").prop("disabled", false);
      alert("Please enter Reason for Cancellation");
      $("#reason_can").focus();
      return false;
    }
    var data = $("#cancel_market_form").serializeArray();
    $.ajax(BASE_URL + "/account_manager/post_cancel_markets/", {
      type: "POST",
      data: data,
      dataType: "json",
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(data) {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        $("#cancellation_div").css("display", "block");
        $("#status_td").text("Cancel Requested");
        $("#cancel_ro_request_span").css("display", "none");
        $("#cancel_markets_span").css("display", "none");
        $("#cancel_markets_by_brand_span").css("display", "none");
        $("#cancel_markets_by_content_span").css("display", "none");
        alert(data.Message);
        $("#optionModal").modal("hide");
      },
      error: function() {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        alert("could not cancel market");
        $("#req_market_can_market").prop("disabled", false);
      }
    });
  });

  $(document).on("click", "#req_market_can_brand", function() {
    console.log("cancelled");
    $("#req_market_can_brand").prop("disabled", true);
    if ($("#txt_cancel_date_brand").val() == "") {
      $("#req_market_can_brand").prop("disabled", false);
      alert("Please enter date of cancel.");
      return false;
    }
    if ($("#reason_can").val() == "") {
      $("#req_market_can_brand").prop("disabled", false);
      alert("Please enter Reason for Cancellation");
      $("#reason_can").focus();
      return false;
    }
    var data = $("#cancel_market_brand_form").serializeArray();
    console.log(data);
    // return false;
    $.ajax(BASE_URL + "/account_manager/post_cancel_content_brand/", {
      type: "POST",
      data: data,
      dataType: "json",
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(data) {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        $("#cancellation_div").css("display", "block");
        $("#status_td").text("Cancel Requested");
        $("#cancel_ro_request_span").css("display", "none");
        $("#cancel_markets_span").css("display", "none");
        $("#cancel_markets_by_brand_span").css("display", "none");
        $("#cancel_markets_by_content_span").css("display", "none");
        alert(data.Message);
        $("#optionModal").modal("hide");
      },
      error: function() {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        alert("could not cancel market");
        $("#req_market_can_brand").prop("disabled", false);
      }
    });
  });

  $(".modal").on("hidden.bs.modal", function(e) {
    if ($(".modal:visible").length) {
      $("body").addClass("modal-open");
    }
  });

  $(document).on("click", "#network_detail_div", function() {
    if ($("#network_detail_div").hasClass("collapsed")) {
      $("#network_i").removeClass("up");
      $("#network_i").addClass("down");
    } else {
      $("#network_i").removeClass("down");
      $("#network_i").addClass("up");
    }
  });

  //Clicking on edit btn and removing disable attribute
  $(document).on("click", ".editbtnclass", function(e) {
    var id = e.currentTarget.id;
    var id_arr = id.split("_");
    var nw_index = id_arr[0];
    $.map(schedule_data[nw_index].channels_data_array, function(
      channel,
      index
    ) {
      $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
        "disabled",
        false
      );
      $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
        "data-save",
        "1"
      );
      if (channel.total_spot_ad_seconds > 0) {
        $("#" + nw_index + "_" + channel.channel_id + "_spot_rate").attr(
          "disabled",
          false
        );
        $("#" + nw_index + "_" + channel.channel_id + "_spot_amount").attr(
          "disabled",
          false
        );
      }
      if (channel.total_banner_ad_seconds > 0) {
        $("#" + nw_index + "_" + channel.channel_id + "_banner_rate").attr(
          "disabled",
          false
        );
        $("#" + nw_index + "_" + channel.channel_id + "_banner_amount").attr(
          "disabled",
          false
        );
      }
    });
    $("#" + nw_index + "_network_share").attr("disabled", false);
    $("#" + nw_index + "_network_payout").attr("disabled", false);
  });

  //for opening campaign schedule
  $(document).on("click", "#campaigns_schedule_span", function() {
    $.ajax(BASE_URL + "/ro_manager/campaigns_schedule", {
      type: "POST",
      data: {
        order_id: internal_ro_no,
        edit: "1",
        id: ro_id,
        search_by: "",
        search_value: ""
      },
      dataType: "json",
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(responsedata) {
        var data = responsedata.Data.jsonData;
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        $("#option_modal_title").text("Campaign Wise Schedule Summary");
        $("#optionModal").modal("show");
        $("#optionModal").data("bs.modal")._config.backdrop = "static";
        var ro_detail =
          "<form id='campaign_form'>" +
          "<div class='container mycustomclass' style='background-color:#F0EEE9;height:46px;'>" +
          "<div class='row'>" +
          "<div class='col-6'><h4>Ro Detail</h4></div>" +
          "<div class='col-6'>Search By:<select style='margin-right: 5px' name='search_by' id='search_by'>" +
          "<option value=''>-</option>" +
          "<option value='campaign_name'>Campaign Name</option>" +
          "<option value='brand_new'>Brand Name</option>" +
          "<option value='agency_name'>Agency Name</option>" +
          "<option value='caption_name'>Caption Name</option>" +
          "<option value='derived_campaign_status'>Campaign Status</option></select>" +
          "<input type='text' style='margin-right: 5px' id='search_camp' placeholder='Enter text to Search.....'>" +
          "<input type='button' class='btn btn-info' style='font-size: 11px' id='search_campaign_btn' value='Search'> " +
          "<input type='button' class='btn btn-info' style='font-size: 11px' id='load_campaign_btn' value='Load'>" +
          "</div></div>" +
          "</div>" +
          "</form>";
        ro_detail =
          ro_detail +
          "<div class='table_div' style='border: 1px solid lightgrey'>" +
          "<table class='table '>" +
          "<tr>" +
          "<th>Customer RO Number</th>" +
          "<th>Agency Name</th>" +
          "<th>Advertise name</th>" +
          "<th>Brand Name</th>" +
          "<th>RO Start Date</th>" +
          "<th>RO End Date</th>" +
          " </tr>" +
          "<tr>" +
          "<td>" +
          data.am_ext_ro +
          "<br/>(Internal RO Number:" +
          data.internal_ro +
          ")</td>" +
          "<td>" +
          data.agency +
          "</td>" +
          "<td>" +
          data.client +
          "</td>" +
          "<td>" +
          data.brand +
          "</td>" +
          "<td>" +
          data.camp_start_date.split(" ")[0] +
          "</td>" +
          "<td>" +
          data.camp_end_date.split(" ")[0] +
          "</td>" +
          "</tr>" +
          "</table>" +
          "</div>" +
          "<br>";
        ro_detail =
          ro_detail +
          "<div id='overflow_div' style='border-top-left-radius:5px;border-top-right-radius:5px;'>" +
          "    <input type='button' class='submitlong' id='cancel_campaigns' style='display: none' value='Cancel Campaign'>";
        if (data.campaigns.length > 0) {
          ro_detail =
            ro_detail +
            '<table class="table table-sm"  cellpadding="0" cellspacing="0"  style="font-size:11px;"><tr style="background-color: white">';
          if (data.logged_in_user.profile_id == 3) {
            ro_detail = ro_detail + "<th>&nbsp;</th>";
          }
          ro_detail =
            ro_detail +
            "<th style='background-color:#F0EEE9;border-top-left-radius:5px;'>Campaign Name</th>" +
            "                <th style='background-color:#F0EEE9'>Brand Name</th>" +
            "                <th style='background-color:#F0EEE9'>Agency Name</th>" +
            "                <th style='background-color:#F0EEE9'>Campaign Start Date</th>" +
            "                <th style='background-color:#F0EEE9'>Campaign End Date</th>" +
            "                <th style='background-color:#F0EEE9'>Caption Name</th>" +
            "                <th style='background-color:#F0EEE9'>Caption Duration(Sec)</th>" +
            "                <th style='background-color:#F0EEE9'>Channel Name</th>" +
            "                <th style='background-color:#F0EEE9'>Scheduled / Booked</th>" +
            "                <th style='background-color:#F0EEE9'>Campaign Status</th>" +
            "                <th style='background-color:#F0EEE9'>Campaign ID</th>" +
            "                <th style='background-color:#F0EEE9;border-top-right-radius:5px;'>Market</th>" +
            "            </tr>" +
            "            <tbody id='campaign_tbody'>";
          $.map(data.campaigns, function(campaign, index) {
            var csv = campaign.csv_input.split(",");
            var channels = csv[12].split("#");
            var channels_name = channels.join();
            var color;
            if (campaign["mismatch_impression"] == 0) {
              color = "rgba(200, 247, 197, 0.5)";
            } else if (campaign["mismatch_impression"] == 1) {
              color = "rgba(231, 76, 60, 0.5)";
            } else {
              color = "rgba(245, 230, 83, 0.5)";
            }
            if (data.logged_in_user.profile_id == 3) {
              ro_detail = ro_detail + "<td>";
              if (campaign["derived_campaign_status"] == "pending_approval") {
                ro_detail =
                  ro_detail +
                  "<input type='checkbox' class='campaigns_to_cancel' value='" +
                  campaign["campaign_id"] +
                  "'>";
              }
              ro_detail = ro_detail + "</td>";
            }
            ro_detail =
              ro_detail +
              "<td style='background-color:" +
              color +
              " ' ><div style='width:90px;word-wrap:break-word;'>" +
              campaign["campaign_name"] +
              "</div></td>" +
              "                <td ><div style='width:90px;word-wrap:break-word;'>" +
              campaign["brand_new"] +
              "</div></td>" +
              "                <td > <div style='width:67px;word-wrap:break-word;'>" +
              campaign["agency_name"] +
              "</div></td>" +
              "                <td > <div style='width:77px;word-wrap:break-word;'>" +
              campaign["start_date"].split(" ")[0] +
              "</div></td>" +
              "                <td > <div style='width:77px;word-wrap:break-word;'>" +
              campaign["end_date"].split(" ")[0] +
              "</div></td>" +
              "                <td >  <div style='width:74px;word-wrap:break-word;'>" +
              campaign["caption_name"] +
              "</div></td>" +
              "                <td > <div style='width:50px;word-wrap:break-word;'>" +
              campaign["ro_duration"] +
              "</div></td>" +
              "                <td > <div style='width:70px;word-wrap:break-word;'>" +
              channels_name +
              "</div></td>" +
              "                <td > <div style='width:70px;word-wrap:break-word;'>" +
              campaign["scheduled_impression"] +
              "/" +
              campaign["booked_impression"] +
              " </div></td>" +
              "                <td ><div style='width:73px;word-wrap:break-word;'>" +
              campaign["derived_campaign_status"] +
              "</div></td>" +
              "                <td > <div style='width:93px;word-wrap:break-word;'>" +
              campaign["campaign_id"] +
              "</div></td>" +
              "                <td > <div style='width:71px;word-wrap:break-word;'>" +
              campaign["sw_market_name"] +
              "</div> </td>" +
              "            </tr>";
          });
          ro_detail = ro_detail + "</tbody>";
          $("#Option_Modal_body").html(ro_detail);
          $("#overflow_div").addClass("overflow_div");
        } else if (data.search_value_set == 1) {
          ro_detail =
            ro_detail +
            "<p style='text-align:center;font-size:18px'>No Result Found !</p>";
          $("#Option_Modal_body").html(ro_detail);
          $("#overflow_div").removeClass("overflow_div");
        } else {
          ro_detail =
            ro_detail +
            '<p style="text-align:center;font-size:18px">No Active Campaign !</p>';
          $("#Option_Modal_body").html(ro_detail);
          $("#overflow_div").removeClass("overflow_div");
        }
      },
      error: function(data) {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        alert("could not load");
      }
    });
  });
  //for opening channels schedule
  $(document).on("click", "#channels_schedule_span", function() {
    $.ajax(BASE_URL + "/ro_manager/channels_schedule", {
      type: "POST",
      data: {
        order_id: internal_ro_no,
        edit: "1",
        id: ro_id
      },
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(responsedata) {
        var data = responsedata.Data.html;
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        $("#option_modal_title").text("Channels Schedule");
        $("#optionModal").modal("show");
        $("#optionModal").data("bs.modal")._config.backdrop = "static";
        $("#Option_Modal_body").html(data);
      },
      error: function(data) {
        alert("error in loading");
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
      }
    });
  });
  //for opening nw_ro_payment status
  $(document).on("click", "#nw_ro_payment_span", function() {
    $.ajax(BASE_URL + "/ro_manager/nw_ro_payment", {
      type: "POST",
      data: {
        order_id: internal_ro_no,
        edit: "1",
        id: ro_id
      },
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(responsedata) {
        console.log(responsedata);
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        $("#option_modal_title").text("Network Ro Payment");
        $("#optionModal").modal("show");
        $("#optionModal").data("bs.modal")._config.backdrop = "static";
        $("#Option_Modal_body").html(responsedata.Data.html);
      },
      error: function(data) {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
      }
    });
  });

  $(document).on("click", "#load_campaign_btn", function() {
    $.ajax(BASE_URL + "/ro_manager/campaigns_schedule", {
      type: "POST",
      data: {
        order_id: internal_ro_no,
        edit: "1",
        id: ro_id
      },
      dataType: "json",
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(responsedata) {
        var data = responsedata.Data.jsonData;
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        console.log(data.campaigns);
        $("#campaign_tbody").empty();
        var ro_detail = "";
        if (data.campaigns.length > 0) {
          $.map(data.campaigns, function(campaign, index) {
            ro_detail = ro_detail + "<tr>";
            var csv = campaign.csv_input.split(",");
            var channels = csv[12].split("#");
            var channels_name = channels.join();
            var color;
            if (campaign["mismatch_impression"] == 0) {
              color = "rgba(200, 247, 197, 0.5)";
            } else if (campaign["mismatch_impression"] == 1) {
              color = "rgba(231, 76, 60, 0.5)";
            } else {
              color = "rgba(245, 230, 83, 0.5)";
            }
            if (data.logged_in_user.profile_id == 3) {
              ro_detail = ro_detail + "<td>";
              if (campaign["derived_campaign_status"] == "pending_approval") {
                ro_detail =
                  ro_detail +
                  "<input type='checkbox' class='campaigns_to_cancel' value='" +
                  campaign["campaign_id"] +
                  "'>";
              }
              ro_detail = ro_detail + "</td>";
            }
            ro_detail =
              ro_detail +
              "<td style='background-color:" +
              color +
              " '><div style='width:90px;word-wrap:break-word;'>" +
              campaign["campaign_name"] +
              "</div></td>" +
              "                <td ><div style='width:90px;word-wrap:break-word;'>" +
              campaign["brand_new"] +
              "</div></td>" +
              "                <td > <div style='width:67px;word-wrap:break-word;'>" +
              campaign["agency_name"] +
              "</div></td>" +
              "                <td > <div style='width:77px;word-wrap:break-word;'>" +
              campaign["start_date"].split(" ")[0] +
              "</div></td>" +
              "                <td > <div style='width:77px;word-wrap:break-word;'>" +
              campaign["end_date"].split(" ")[0] +
              "</div></td>" +
              "                <td >  <div style='width:74px;word-wrap:break-word;'>" +
              campaign["caption_name"] +
              "</div></td>" +
              "                <td > <div style='width:50px;word-wrap:break-word;'>" +
              campaign["ro_duration"] +
              "</div></td>" +
              "                <td > <div style='width:70px;word-wrap:break-word;'>" +
              channels_name +
              "</div></td>" +
              "                <td > <div style='width:70px;word-wrap:break-word;'>" +
              campaign["scheduled_impression"] +
              "/" +
              campaign["booked_impression"] +
              " </div></td>" +
              "                <td ><div style='width:73px;word-wrap:break-word;'>" +
              campaign["derived_campaign_status"] +
              "</div></td>" +
              "                <td > <div style='width:93px;word-wrap:break-word;'>" +
              campaign["campaign_id"] +
              "</div></td>" +
              "                <td > <div style='width:71px;word-wrap:break-word;'>" +
              campaign["sw_market_name"] +
              "</div> </td>" +
              "            </tr>";
          });

          $("#campaign_tbody").html(ro_detail);
        }
      }
    });
  });

  $(document).on("click", "#search_campaign_btn", function() {
    if ($("#search_by").val() == "") {
      alert("select search by");
      return false;
    }
    var search_by = "";
    var search_camp = "";
    search_by = $("#search_by").val();
    search_camp = $("#search_camp").val();
    $.ajax(BASE_URL + "/ro_manager/campaigns_schedule", {
      type: "POST",
      data: {
        order_id: internal_ro_no,
        edit: "1",
        id: ro_id,
        search_by: search_by,
        search_value: search_camp
      },
      dataType: "json",
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(responsedata) {
        var data = responsedata.Data.jsonData;
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        console.log(data.campaigns);
        $("#campaign_tbody").empty();
        var ro_detail = "";
        if (data.campaigns.length > 0) {
          $.map(data.campaigns, function(campaign, index) {
            ro_detail = ro_detail + "<tr>";
            var csv = campaign.csv_input.split(",");
            var channels = csv[12].split("#");
            var channels_name = channels.join();
            var color;
            if (campaign["mismatch_impression"] == 0) {
              color = "rgba(200, 247, 197, 0.5)";
            } else if (campaign["mismatch_impression"] == 1) {
              color = "rgba(231, 76, 60, 0.5)";
            } else {
              color = "rgba(245, 230, 83, 0.5)";
            }
            if (data.logged_in_user.profile_id == 3) {
              ro_detail = ro_detail + "<td>";
              if (campaign["derived_campaign_status"] == "pending_approval") {
                ro_detail =
                  ro_detail +
                  "<input type='checkbox' class='campaigns_to_cancel' value='" +
                  campaign["campaign_id"] +
                  "'>";
              }
              ro_detail = ro_detail + "</td>";
            }
            ro_detail =
              ro_detail +
              "<td  style='background-color:" +
              color +
              " '><div style='width:90px;word-wrap:break-word;'>" +
              campaign["campaign_name"] +
              "</div></td>" +
              "                <td ><div style='width:90px;word-wrap:break-word;'>" +
              campaign["brand_new"] +
              "</div></td>" +
              "                <td > <div style='width:67px;word-wrap:break-word;'>" +
              campaign["agency_name"] +
              "</div></td>" +
              "                <td > <div style='width:77px;word-wrap:break-word;'>" +
              campaign["start_date"].split(" ")[0] +
              "</div></td>" +
              "                <td > <div style='width:77px;word-wrap:break-word;'>" +
              campaign["end_date"].split(" ")[0] +
              "</div></td>" +
              "                <td >  <div style='width:74px;word-wrap:break-word;'>" +
              campaign["caption_name"] +
              "</div></td>" +
              "                <td > <div style='width:50px;word-wrap:break-word;'>" +
              campaign["ro_duration"] +
              "</div></td>" +
              "                <td > <div style='width:70px;word-wrap:break-word;'>" +
              channels_name +
              "</div></td>" +
              "                <td > <div style='width:70px;word-wrap:break-word;'>" +
              campaign["scheduled_impression"] +
              "/" +
              campaign["booked_impression"] +
              " </div></td>" +
              "                <td ><div style='width:73px;word-wrap:break-word;'>" +
              campaign["derived_campaign_status"] +
              "</div></td>" +
              "                <td > <div style='width:93px;word-wrap:break-word;'>" +
              campaign["campaign_id"] +
              "</div></td>" +
              "                <td > <div style='width:71px;word-wrap:break-word;'>" +
              campaign["sw_market_name"] +
              "</div> </td>" +
              "            </tr>";
          });

          $("#campaign_tbody").html(ro_detail);
        }
      }
    });
  });

  //for opening campaign schedule
  $(document).on("click", "#campaigns_schedule_span", function() {
    $.ajax(BASE_URL + "/ro_manager/campaigns_schedule", {
      type: "POST",
      data: {
        order_id: internal_ro_no,
        edit: "1",
        id: ro_id,
        search_by: "",
        search_value: ""
      },
      dataType: "json",
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(responsedata) {
        var data = responsedata.Data.jsonData;
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        $("#option_modal_title").text("Campaign Wise Schedule Summary");
        $("#optionModal").modal("show");
        $("#optionModal").data("bs.modal")._config.backdrop = "static";
        console.log(data);
        var ro_detail =
          "<form id='campaign_form'>" +
          "<div class='container mycustomclass' style='background-color:#F0EEE9;height:46px;'>" +
          "<div class='row'>" +
          "<div class='col-6'><h4>Ro Detail</h4></div>" +
          "<div class='col-6'>Search By:<select style='margin-right: 5px' name='search_by' id='search_by'>" +
          "<option value=''>-</option>" +
          "<option value='campaign_name'>Campaign Name</option>" +
          "<option value='brand_new'>Brand Name</option>" +
          "<option value='agency_name'>Agency Name</option>" +
          "<option value='caption_name'>Caption Name</option>" +
          "<option value='derived_campaign_status'>Campaign Status</option></select>" +
          "<input type='text' style='margin-right: 5px' id='search_camp' placeholder='Enter text to Search.....'>" +
          "<input type='button' class='btn btn-info' style='font-size: 11px' id='search_campaign_btn' value='Search'> " +
          "<input type='button' class='btn btn-info' style='font-size: 11px' id='load_campaign_btn' value='Load'>" +
          "</div></div>" +
          "</div>" +
          "</form>";
        ro_detail =
          ro_detail +
          "<div class='table_div' style='border: 1px solid lightgrey'>" +
          "<table class='table'>" +
          "<tr>" +
          "<th>Customer RO Number</th>" +
          "<th>Agency Name</th>" +
          "<th>Advertise name</th>" +
          "<th>Brand Name</th>" +
          "<th>RO Start Date</th>" +
          "<th>RO End Date</th>" +
          " </tr>" +
          "<tr>" +
          "<td>" +
          data.content.customer_ro_number +
          "<br/>(Internal RO Number:" +
          data.content.internal_ro_number +
          ")</td>" +
          "<td>" +
          data.content.agency_name +
          "</td>" +
          "<td>" +
          data.content.client_name +
          "</td>" +
          "<td>" +
          data.content.brand_new +
          "</td>" +
          "<td>" +
          data.content.start_date +
          "</td>" +
          "<td>" +
          data.content.end_date +
          "</td>" +
          "</tr>" +
          "</table>" +
          "</div>" +
          "<br>";
        ro_detail =
          ro_detail +
          "<div>" +
          "    <input type='button' class='submitlong' id='cancel_campaigns' style='display: none' value='Cancel Campaign'>";
        if (data.campaigns.length > 0) {
          ro_detail =
            ro_detail +
            '<table class="table table-sm"  cellpadding="0" cellspacing="0"  style="font-size:11px;"><tr style="background-color: white">';
          if (data.logged_in_user.profile_id == 3) {
            ro_detail = ro_detail + "<th>&nbsp;</th>";
          }
          ro_detail =
            ro_detail +
            "<th style='background-color:#F0EEE9;border-top-left-radius:5px;'>Campaign Name</th>" +
            "                <th style='background-color:#F0EEE9'>Brand Name</th>" +
            "                <th style='background-color:#F0EEE9'>Agency Name</th>" +
            "                <th style='background-color:#F0EEE9'>Campaign Start Date</th>" +
            "                <th style='background-color:#F0EEE9'>Campaign End Date</th>" +
            "                <th style='background-color:#F0EEE9'>Caption Name</th>" +
            "                <th style='background-color:#F0EEE9'>Caption Duration(Sec)</th>" +
            "                <th style='background-color:#F0EEE9'>Channel Name</th>" +
            "                <th style='background-color:#F0EEE9'>Scheduled / Booked</th>" +
            "                <th style='background-color:#F0EEE9'>Campaign Status</th>" +
            "                <th style='background-color:#F0EEE9'>Campaign ID</th>" +
            "                <th style='background-color:#F0EEE9'>Market</th>" +
            "            </tr>" +
            "            <tbody id='campaign_tbody'>";
          $.map(data.campaigns, function(campaign, index) {
            var csv = campaign.csv_input.split(",");
            var channels = csv[12].split("#");
            var channels_name = channels.join();
            var color;
            if (campaign["mismatch_impression"] == 0) {
              color = "rgba(200, 247, 197, 0.5)";
            } else if (campaign["mismatch_impression"] == 1) {
              color = "rgba(231, 76, 60, 0.5)";
            } else {
              color = "rgba(245, 230, 83, 0.5)";
            }
            if (data.logged_in_user.profile_id == 3) {
              ro_detail = ro_detail + "<td>";
              if (campaign["derived_campaign_status"] == "pending_approval") {
                ro_detail =
                  ro_detail +
                  "<input type='checkbox' class='campaigns_to_cancel' value='" +
                  campaign["campaign_id"] +
                  "'>";
              }
              ro_detail = ro_detail + "</td>";
            }
            ro_detail =
              ro_detail +
              "<td  style='background-color:" +
              color +
              " '><div style='width:90px;word-wrap:break-word;'>" +
              campaign["campaign_name"] +
              "</div></td>" +
              "                <td ><div style='width:90px;word-wrap:break-word;'>" +
              campaign["brand_new"] +
              "</div></td>" +
              "                <td > <div style='width:67px;word-wrap:break-word;'>" +
              campaign["agency_name"] +
              "</div></td>" +
              "                <td > <div style='width:77px;word-wrap:break-word;'>" +
              campaign["start_date"].split(" ")[0] +
              "</div></td>" +
              "                <td > <div style='width:77px;word-wrap:break-word;'>" +
              campaign["end_date"].split(" ")[0] +
              "</div></td>" +
              "                <td >  <div style='width:74px;word-wrap:break-word;'>" +
              campaign["caption_name"] +
              "</div></td>" +
              "                <td > <div style='width:50px;word-wrap:break-word;'>" +
              campaign["ro_duration"] +
              "</div></td>" +
              "                <td > <div style='width:70px;word-wrap:break-word;'>" +
              channels_name +
              "</div></td>" +
              "                <td > <div style='width:70px;word-wrap:break-word;'>" +
              campaign["scheduled_impression"] +
              "/" +
              campaign["booked_impression"] +
              " </div></td>" +
              "                <td ><div style='width:73px;word-wrap:break-word;'>" +
              campaign["derived_campaign_status"] +
              "</div></td>" +
              "                <td > <div style='width:93px;word-wrap:break-word;'>" +
              campaign["campaign_id"] +
              "</div></td>" +
              "                <td > <div style='width:71px;word-wrap:break-word;'>" +
              campaign["sw_market_name"] +
              "</div> </td>" +
              "            </tr>";
          });
          ro_detail = ro_detail + "</tbody>";
        } else if (data.search_value_set == 1) {
          ro_detail =
            ro_detail + "<h2 style='text-align:center;'>No Result Found !</h2>";
        } else {
          ro_detail =
            ro_detail +
            '<h2 style="text-align:center;">No Active Campaign !</h2>';
        }

        $("#Option_Modal_body").html(ro_detail);
      },
      error: function(data) {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        alert("could not load");
      }
    });
  });

  //view/modify
  $(document).on("click", "#view_modify_span", function() {
    $.ajax(BASE_URL + "/ro_manager/add_other_expenses", {
      type: "POST",
      data: {
        external_ro: submit_ext[0].cust_ro,
        internal_ro: internal_ro_no,
        edit: "1",
        am_ro_id: ro_id
      },
      beforeSend: function() {
        $("#loader_background").css("display", "block");
        $("#loader_spin").css("display", "block");
      },
      success: function(data) {
        $("#view_Modal_body").html("");
        $("#view_modal_title").text("View/Modify");
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        $("#confirmModal").modal("show");
        $("#confirmModal").data("bs.modal")._config.backdrop = "static";
        $("#view_Modal_body").html(data.Data.html);
      },
      error: function() {
        $("#loader_background").css("display", "none");
        $("#loader_spin").css("display", "none");
        alert("error in loading");
      }
    });
  });

  $(document).on("click", ".savebtnclass", function(e) {
    var id = e.currentTarget.id;
    var id_arr = id.split("_");
    var nw_index = id_arr[0];
    $.map(schedule_data[nw_index].channels_data_array, function(
      channel,
      index
    ) {
      if (channel.approved == 0) {
        $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
          "data-save",
          "0"
        );
        if (channel.total_spot_ad_seconds > 0) {
          $("#" + nw_index + "_" + channel.channel_id + "_spot_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel.channel_id + "_spot_amount").attr(
            "disabled",
            true
          );
        }
        if (channel.total_banner_ad_seconds > 0) {
          $("#" + nw_index + "_" + channel.channel_id + "_banner_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel.channel_id + "_banner_amount").attr(
            "disabled",
            true
          );
        }
      } else {
        $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
          "data-save",
          "0"
        );
        // if (channel.total_spot_ad_seconds > 0) {
        //     var new_rate_val = Number($('#' + nw_index + '_' + channel.channel_id + '_spot_rate').val());
        //     if (channel.channel_spot_avg_rate != new_rate_val) {
        //         $('#' + nw_index + '_' + channel.channel_id + '_checkbox').attr('data-change', '1');
        //     } else {
        //         $('#' + nw_index + '_' + channel.channel_id + '_checkbox').attr('data-change', '0');
        //     }
        //     $('#' + nw_index + '_' + channel.channel_id + '_spot_rate').attr('disabled', true);
        //     $('#' + nw_index + '_' + channel.channel_id + '_spot_amount').attr('disabled', true);
        // }
        // if (channel.total_banner_ad_seconds > 0) {
        //     var new_rate_val = Number($('#' + nw_index + '_' + channel.channel_id + '_banner_rate').val());
        //     if (channel.channel_banner_avg_rate != new_rate_val) {
        //         $('#' + nw_index + '_' + channel.channel_id + '_checkbox').attr('data-change', '1');
        //     } else {
        //         $('#' + nw_index + '_' + channel.channel_id + '_checkbox').attr('data-change', '0');
        //     }
        //     $('#' + nw_index + '_' + channel.channel_id + '_banner_rate').attr('disabled', true);
        //     $('#' + nw_index + '_' + channel.channel_id + '_banner_amount').attr('disabled', true);
        //
        // }

        if (
          channel.total_spot_ad_seconds > 0 &&
          channel.total_banner_ad_seconds > 0
        ) {
          var new_spot_rate_val = Number(
            $("#" + nw_index + "_" + channel.channel_id + "_spot_rate").val()
          );
          var new_banner_rate_val = Number(
            $("#" + nw_index + "_" + channel.channel_id + "_banner_rate").val()
          );
          if (
            channel.channel_spot_avg_rate != new_spot_rate_val ||
            channel.channel_banner_avg_rate != new_banner_rate_val
          ) {
            $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
              "data-change",
              "1"
            );
          } else {
            $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
              "data-change",
              "0"
            );
          }
          $("#" + nw_index + "_" + channel.channel_id + "_banner_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel.channel_id + "_banner_amount").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel.channel_id + "_spot_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel.channel_id + "_spot_amount").attr(
            "disabled",
            true
          );
        } else if (
          channel.total_spot_ad_seconds > 0 &&
          channel.total_banner_ad_seconds == 0
        ) {
          var new_spot_rate_val = Number(
            $("#" + nw_index + "_" + channel.channel_id + "_spot_rate").val()
          );
          if (channel.channel_spot_avg_rate != new_spot_rate_val) {
            $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
              "data-change",
              "1"
            );
          } else {
            $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
              "data-change",
              "0"
            );
          }
          $("#" + nw_index + "_" + channel.channel_id + "_spot_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel.channel_id + "_spot_amount").attr(
            "disabled",
            true
          );
        } else if (
          channel.total_spot_ad_seconds == 0 &&
          channel.total_banner_ad_seconds > 0
        ) {
          var new_banner_rate_val = Number(
            $("#" + nw_index + "_" + channel.channel_id + "_banner_rate").val()
          );
          if (channel.channel_banner_avg_rate != new_banner_rate_val) {
            $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
              "data-change",
              "1"
            );
          } else {
            $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
              "data-change",
              "0"
            );
          }
          $("#" + nw_index + "_" + channel.channel_id + "_banner_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel.channel_id + "_banner_amount").attr(
            "disabled",
            true
          );
        }
      }
      $("#" + nw_index + "_" + channel.channel_id + "_checkbox").attr(
        "disabled",
        true
      );
    });
    $("#" + nw_index + "_network_share").attr("disabled", true);
    $("#" + nw_index + "_network_payout").attr("disabled", true);
    $("#final_save_btn").css("display", "block");
  });

  //clicking on final save
  $(document).on("click", "#final_save_btn", function() {
    var exit = 0;
    $.map(schedule_data, function(network, nw_index) {
      channel = network.channels_data_array[0];
      var channel_id = channel.channel_id;
      var to_save = $("#" + nw_index + "_" + channel_id + "_checkbox").attr(
        "data-save"
      );
      if (to_save == 1) {
        console.log("alert");
        alert("network " + network.network_name + " has not been saved");
        exit = 1;
      }
    });
    if (exit == 0) {
      var payload = createfinaldata();
      console.log(payload);
      $.ajax(BASE_URL + "/ro_manager/process_edited_network", {
        type: "POST",
        beforeSend: function() {
          $("#loader_background").css("display", "block");
          $("#loader_spin").css("display", "block");
        },
        data: payload,
        success: function(responsedata) {
          $("#loader_background").css("display", "none");
          $("#loader_spin").css("display", "none");
          if (!responsedata.isLoggedIn) {
            window.location.href = BASE_URL;
            return false;
          } else if (responsedata.Status == "fail") {
            alert(responsedata.Message);
            return false;
          } else if ("html" in responsedata.Data) {
            $("#Modal_body").html("");
            $("#Modal_body").html(responsedata.Data.html);
          } else {
            alert("Some error occurred!!");
            window.location.href = BASE_URL + "/account_manager/home";
          }
        },
        error: function() {
          $("#loader_background").css("display", "block");
          $("#loader_spin").css("display", "block");
        }
      });
    }
  });

  //cancel channel
  $(document).on("change", ".checkboxclass", function(e) {
    var checkbox_id = e.currentTarget.id;
    var checkbox_id_arr = checkbox_id.split("_");
    var approved = $("#" + checkbox_id).attr("data-approved");
    var nw_index = checkbox_id_arr[0];
    var channel_id = checkbox_id_arr[1];
    var checkbox_td_id = nw_index + "_" + channel_id + "_" + "checkbox_td";
    var total_spot_seconds = Number(
      $("#" + checkbox_id).attr("data-total_spot_seconds")
    );
    var total_banner_seconds = Number(
      $("#" + checkbox_id).attr("data-total_banner_seconds")
    );
    if (approved == 1) {
      if (isNaN(total_spot_seconds)) {
        total_spot_seconds = 0;
      }
      if (isNaN(total_banner_seconds)) {
        total_banner_seconds = 0;
      }
      if ($("#" + checkbox_id).is(":checked")) {
        $("#" + checkbox_id).attr("data-cancel_channel", "0");
        $("#" + checkbox_td_id).css("background-color", "white");
        var old_nw_amount = Number(
          $("#" + nw_index + "_network_amount").text()
        );
        var new_nw_amount = 0;
        var old_channel_spot_amount = 0;
        var old_channel_banner_amount = 0;
        var addition_spot_amount = 0;
        var addition_banner_amount = 0;
        if (
          $("#" + nw_index + "_" + channel_id + "_total_spot_ad_seconds")
            .length > 0
        ) {
          var to_play_spot = Number(
            $("#" + checkbox_id).attr("data-to_play_spot")
          );
          if (isNaN(to_play_spot)) {
            to_play_spot = 0;
          }
          var new_channel_spot_amount =
            (to_play_spot *
              Number(
                $("#" + nw_index + "_" + channel_id + "_spot_rate").val()
              )) /
            10;
          old_channel_spot_amount =
            (Number(total_spot_seconds) *
              Number(
                $("#" + nw_index + "_" + channel_id + "_spot_rate").val()
              )) /
            10;
          addition_spot_amount =
            old_channel_spot_amount - new_channel_spot_amount;
          $("#" + nw_index + "_" + channel_id + "_total_spot_ad_seconds").text(
            total_spot_seconds
          );
          $("#" + nw_index + "_" + channel_id + "_spot_amount").val(
            old_channel_spot_amount
          );
          $("#" + nw_index + "_" + channel_id + "_spot_rate").attr(
            "disabled",
            false
          );
          $("#" + nw_index + "_" + channel_id + "_spot_amount").attr(
            "disabled",
            false
          );
        }
        if (
          $("#" + nw_index + "_" + channel_id + "_total_banner_ad_seconds")
            .length > 0
        ) {
          var to_play_banner = Number(
            $("#" + checkbox_id).attr("data-to_play_banner")
          );
          if (isNaN(to_play_banner)) {
            to_play_spot = 0;
          }
          var new_channel_banner_amount =
            (to_play_banner *
              Number(
                $("#" + nw_index + "_" + channel_id + "_banner_rate").val()
              )) /
            10;
          old_channel_banner_amount =
            (Number(total_banner_seconds) *
              Number(
                $("#" + nw_index + "_" + channel_id + "_banner_rate").val()
              )) /
            10;
          addition_banner_amount =
            old_channel_banner_amount - new_channel_banner_amount;
          $(
            "#" + nw_index + "_" + channel_id + "_total_banner_ad_seconds"
          ).text(total_banner_seconds);
          $("#" + nw_index + "_" + channel_id + "_banner_amount").val(
            old_channel_banner_amount
          );
          $("#" + nw_index + "_" + channel_id + "_banner_rate").attr(
            "disabled",
            false
          );
          $("#" + nw_index + "_" + channel_id + "_banner_amount").attr(
            "disabled",
            false
          );
        }
        var old_nw_payout = Number($("#" + nw_index + "_network_payout").val());
        console.log("addition_banner_amount " + addition_banner_amount);
        console.log("addition_spot_amount " + addition_spot_amount);
        console.log("old_nw_amount " + old_nw_amount);
        new_nw_amount =
          old_nw_amount + addition_banner_amount + addition_spot_amount;
        $("#" + nw_index + "_network_amount").text(new_nw_amount.toFixed(2));
        var nw_share = Number($("#" + nw_index + "_network_share").val());
        var new_nw_payout = (new_nw_amount * nw_share) / 100;
        $("#" + nw_index + "_network_payout").val(new_nw_payout);
        var addition_amount = new_nw_payout - old_nw_payout;
        var old_total_nw_payout = Number($("#total_network_payout").text());
        var new_total_nw_payout = old_total_nw_payout + addition_amount;
        $("#total_network_payout").text(new_total_nw_payout.toFixed(2));
        var actual_net_amount = Number($("#actual_net_amount").text());
        var surewaves_share = actual_net_amount - new_total_nw_payout;
        var net_revenue = Number($("#net_revenue").text()) * SERVICE_TAX;
        var surewaves_share_per = (surewaves_share / net_revenue) * 100;
        $("#surewaves_share").text(surewaves_share.toFixed(2));
        $("#surewaves_share_per").text(surewaves_share_per.toFixed(2));
        $("#" + nw_index + "_network_share").attr("disabled", false);
        $("#" + nw_index + "_network_payout").attr("disabled", false);
      } else {
        $("#" + checkbox_id).attr("data-cancel_channel", "1");
        $("#" + checkbox_td_id).css(
          "background-color",
          "rgba(246, 71, 71,0.6)"
        );
        var old_nw_amount = Number(
          $("#" + nw_index + "_network_amount").text()
        );
        var new_nw_amount = 0;
        var old_channel_spot_amount = 0;
        var old_channel_banner_amount = 0;
        var removal_spot_amount = 0;
        var removal_banner_amount = 0;
        if (
          $("#" + nw_index + "_" + channel_id + "_total_spot_ad_seconds")
            .length > 0
        ) {
          var to_play_spot = Number(
            $("#" + checkbox_id).attr("data-to_play_spot")
          );
          if (isNaN(to_play_spot)) {
            to_play_spot = 0;
          }
          var new_channel_spot_amount =
            (to_play_spot *
              Number(
                $("#" + nw_index + "_" + channel_id + "_spot_rate").val()
              )) /
            10;
          old_channel_spot_amount = Number(
            $("#" + nw_index + "_" + channel_id + "_spot_amount").val()
          );
          removal_spot_amount =
            old_channel_spot_amount - new_channel_spot_amount;
          $("#" + nw_index + "_" + channel_id + "_total_spot_ad_seconds").text(
            to_play_spot
          );
          $("#" + nw_index + "_" + channel_id + "_spot_amount").val(
            new_channel_spot_amount
          );
          $("#" + nw_index + "_" + channel_id + "_spot_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel_id + "_spot_amount").attr(
            "disabled",
            true
          );
        }
        if (
          $("#" + nw_index + "_" + channel_id + "_total_banner_ad_seconds")
            .length > 0
        ) {
          var to_play_banner = Number(
            $("#" + checkbox_id).attr("data-to_play_banner")
          );
          if (isNaN(to_play_banner)) {
            to_play_banner = 0;
          }
          var new_channel_banner_amount =
            (to_play_banner *
              Number(
                $("#" + nw_index + "_" + channel_id + "_banner_rate").val()
              )) /
            10;
          old_channel_banner_amount = Number(
            $("#" + nw_index + "_" + channel_id + "_banner_amount").val()
          );
          removal_banner_amount =
            old_channel_banner_amount - new_channel_banner_amount;
          $(
            "#" + nw_index + "_" + channel_id + "_total_banner_ad_seconds"
          ).text(to_play_banner);
          $("#" + nw_index + "_" + channel_id + "_banner_amount").val(
            new_channel_banner_amount
          );
          $("#" + nw_index + "_" + channel_id + "_banner_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel_id + "_banner_amount").attr(
            "disabled",
            true
          );
        }
        var old_nw_payout = Number($("#" + nw_index + "_network_payout").val());
        new_nw_amount =
          Number(old_nw_amount) -
          Number(removal_spot_amount) -
          Number(removal_banner_amount);
        $("#" + nw_index + "_network_amount").text(new_nw_amount.toFixed(2));
        var nw_share = Number($("#" + nw_index + "_network_share").val());
        var new_nw_payout = (new_nw_amount * nw_share) / 100;
        $("#" + nw_index + "_network_payout").val(new_nw_payout);
        var removal_amount = old_nw_payout - new_nw_payout;
        var old_total_nw_payout = Number($("#total_network_payout").text());
        var new_total_nw_payout = old_total_nw_payout - removal_amount;
        $("#total_network_payout").text(new_total_nw_payout.toFixed(2));
        var actual_net_amount = Number($("#actual_net_amount").text());
        var surewaves_share = actual_net_amount - new_total_nw_payout;
        var net_revenue = Number($("#net_revenue").text()) * SERVICE_TAX;
        var surewaves_share_per = (surewaves_share / net_revenue) * 100;
        $("#surewaves_share").text(surewaves_share.toFixed(2));
        $("#surewaves_share_per").text(surewaves_share_per.toFixed(2));
        //   console.log(nw_index+'_network_share');
        $("#" + nw_index + "_network_share").attr("disabled", true);
        $("#" + nw_index + "_network_payout").attr("disabled", true);
      }
    } else {
      if ($("#" + checkbox_id).is(":checked")) {
        $("#" + checkbox_id).attr("data-cancel_channel", "0");
        var additional_campaign = $("#" + checkbox_id).attr(
          "data-additional_campaign"
        );
        if (additional_campaign == 1) {
          $("#" + checkbox_td_id).css(
            "background-color",
            "rgba(244, 208, 63,0.5)"
          );
        } else {
          $("#" + checkbox_td_id).css(
            "background-color",
            "rgba(220, 198, 224, 0.6)"
          );
        }
        var old_nw_amount = Number(
          $("#" + nw_index + "_network_amount").text()
        );
        var new_nw_amount = 0;
        var channel_spot_amount = 0;
        var channel_banner_amount = 0;
        if (
          $("#" + nw_index + "_" + channel_id + "_total_spot_ad_seconds")
            .length > 0
        ) {
          channel_spot_amount = Number(
            $("#" + nw_index + "_" + channel_id + "_spot_amount").val()
          );
          $("#" + nw_index + "_" + channel_id + "_spot_rate").attr(
            "disabled",
            false
          );
          $("#" + nw_index + "_" + channel_id + "_spot_amount").attr(
            "disabled",
            false
          );
        }
        if (
          $("#" + nw_index + "_" + channel_id + "_total_banner_ad_seconds")
            .length > 0
        ) {
          channel_banner_amount = Number(
            $("#" + nw_index + "_" + channel_id + "_banner_amount").val()
          );
          $("#" + nw_index + "_" + channel_id + "_banner_rate").attr(
            "disabled",
            false
          );
          $("#" + nw_index + "_" + channel_id + "_banner_amount").attr(
            "disabled",
            false
          );
        }
        var old_nw_payout = Number($("#" + nw_index + "_network_payout").val());
        new_nw_amount =
          old_nw_amount + channel_spot_amount + channel_banner_amount;
        $("#" + nw_index + "_network_amount").text(new_nw_amount.toFixed(2));
        var nw_share = Number($("#" + nw_index + "_network_share").val());
        var new_nw_payout = (new_nw_amount * nw_share) / 100;
        $("#" + nw_index + "_network_payout").val(new_nw_payout);
        var addition_amount = new_nw_payout - old_nw_payout;
        var old_total_nw_payout = Number($("#total_network_payout").text());
        var new_total_nw_payout = old_total_nw_payout + addition_amount;
        $("#total_network_payout").text(new_total_nw_payout.toFixed(2));
        var actual_net_amount = Number($("#actual_net_amount").text());
        var surewaves_share = actual_net_amount - new_total_nw_payout;
        var net_revenue = Number($("#net_revenue").text()) * SERVICE_TAX;
        var surewaves_share_per = (surewaves_share / net_revenue) * 100;
        $("#surewaves_share").text(surewaves_share.toFixed(2));
        $("#surewaves_share_per").text(surewaves_share_per.toFixed(2));
        $("#" + nw_index + "_network_share").attr("disabled", false);
        $("#" + nw_index + "_network_payout").attr("disabled", false);
      } else {
        $("#" + checkbox_id).attr("data-cancel_channel", "1");
        $("#" + checkbox_td_id).css(
          "background-color",
          "rgba(246, 71, 71,0.6)"
        );
        var old_nw_amount = Number(
          $("#" + nw_index + "_network_amount").text()
        );

        var new_nw_amount = 0;
        var channel_spot_amount = 0;
        var channel_banner_amount = 0;
        if (
          $("#" + nw_index + "_" + channel_id + "_total_spot_ad_seconds")
            .length > 0
        ) {
          channel_spot_amount = Number(
            $("#" + nw_index + "_" + channel_id + "_spot_amount").val()
          );
          $("#" + nw_index + "_" + channel_id + "_spot_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel_id + "_spot_amount").attr(
            "disabled",
            true
          );
        }
        if (
          $("#" + nw_index + "_" + channel_id + "_total_banner_ad_seconds")
            .length > 0
        ) {
          channel_banner_amount = Number(
            $("#" + nw_index + "_" + channel_id + "_banner_amount").val()
          );
          $("#" + nw_index + "_" + channel_id + "_banner_rate").attr(
            "disabled",
            true
          );
          $("#" + nw_index + "_" + channel_id + "_banner_amount").attr(
            "disabled",
            true
          );
        }
        var old_nw_payout = Number($("#" + nw_index + "_network_payout").val());
        new_nw_amount =
          Number(old_nw_amount) -
          Number(channel_spot_amount) -
          Number(channel_banner_amount);
        $("#" + nw_index + "_network_amount").text(new_nw_amount.toFixed(2));
        var nw_share = Number($("#" + nw_index + "_network_share").val());
        var new_nw_payout = (new_nw_amount * nw_share) / 100;
        $("#" + nw_index + "_network_payout").val(new_nw_payout);
        var removal_amount = old_nw_payout - new_nw_payout;
        var old_total_nw_payout = Number($("#total_network_payout").text());
        var new_total_nw_payout = old_total_nw_payout - removal_amount;
        $("#total_network_payout").text(new_total_nw_payout.toFixed(2));
        var actual_net_amount = Number($("#actual_net_amount").text());
        var surewaves_share = actual_net_amount - new_total_nw_payout;
        var net_revenue = Number($("#net_revenue").text()) * SERVICE_TAX;
        var surewaves_share_per = (surewaves_share / net_revenue) * 100;
        $("#surewaves_share").text(surewaves_share.toFixed(2));
        $("#surewaves_share_per").text(surewaves_share_per.toFixed(2));

        $("#" + nw_index + "_network_share").attr("disabled", true);
        $("#" + nw_index + "_network_payout").attr("disabled", true);
      }
    }
  });

  //To calculate the value of spot amount using last rate
  $(document).on("blur", ".spotclass", function(e) {
    var spot_rate = Number(e.currentTarget.value);
    var spot_rate_id = e.currentTarget.id;
    var id_arr = spot_rate_id.split("_");
    var channel_id = id_arr[1];
    var index = id_arr[0];
    var spot_amount_id = index + "_" + channel_id + "_spot_amount";
    var total_spot_ad_sec_id =
      index + "_" + channel_id + "_total_spot_ad_seconds";
    var total_spot_ad_seconds = Number($("#" + total_spot_ad_sec_id).text());

    if (
      spot_rate < 0 ||
      isNaN(spot_rate) ||
      $("#" + spot_rate_id).val().length == 0
    ) {
      spot_rate = 0;
      console.log(spot_rate);
      $("#" + spot_rate_id).val(0);
    }
    var new_spot_amount = (total_spot_ad_seconds * spot_rate) / 10;
    $("#" + spot_amount_id).val(new_spot_amount.toFixed(2));
    calculate_network_amount(schedule_data[index].channels_data_array, index);
  });

  //to calculate the value of last rate using spot amount
  $(document).on("blur", ".spotamountclass", function(e) {
    var spot_amount = Number(e.currentTarget.value);
    var spot_amount_id = e.currentTarget.id;
    var id_arr = spot_amount_id.split("_");
    var channel_id = id_arr[1];
    var index = id_arr[0];
    var spot_rate_id = index + "_" + channel_id + "_spot_rate";
    var total_spot_ad_sec_id =
      index + "_" + channel_id + "_total_spot_ad_seconds";
    var total_spot_ad_seconds = Number($("#" + total_spot_ad_sec_id).text());
    if (
      spot_amount < 0 ||
      isNaN(spot_amount) ||
      $("#" + spot_amount_id).val().length == 0
    ) {
      spot_amount = 0;
      $("#" + spot_amount_id).val(0.0);
    }
    var new_spot_rate = (spot_amount * 10) / total_spot_ad_seconds;
    $("#" + spot_rate_id).val(new_spot_rate.toFixed(2));
    calculate_network_amount(schedule_data[index].channels_data_array, index);
  });

  //to calculate the value of banner amount using last rate
  $(document).on("blur", ".bannerclass", function(e) {
    var banner_rate = Number(e.currentTarget.value);
    var banner_rate_id = e.currentTarget.id;
    var id_arr = banner_rate_id.split("_");
    var channel_id = id_arr[1];
    var index = id_arr[0];
    var banner_amount_id = index + "_" + channel_id + "_banner_amount";
    var total_banner_ad_sec_id =
      index + "_" + channel_id + "_total_banner_ad_seconds";
    var total_banner_ad_seconds = Number(
      $("#" + total_banner_ad_sec_id).text()
    );
    if (
      banner_rate < 0 ||
      isNaN(banner_rate) ||
      $("#" + banner_rate_id).val().length == 0
    ) {
      banner_rate = 0;
      $("#" + banner_rate_id).val(0);
    }
    var new_banner_amount = (total_banner_ad_seconds * banner_rate) / 10;
    $("#" + banner_amount_id).val(new_banner_amount.toFixed(2));
    calculate_network_amount(schedule_data[index].channels_data_array, index);
  });

  //to calculate the value of last rate using banner amount
  $(document).on("blur", ".banneramountclass", function(e) {
    var banner_amount = Number(e.currentTarget.value);
    var banner_amount_id = e.currentTarget.id;
    var id_arr = banner_amount_id.split("_");
    var channel_id = id_arr[1];
    var index = id_arr[0];
    var banner_rate_id = index + "_" + channel_id + "_banner_rate";
    var total_banner_ad_sec_id =
      index + "_" + channel_id + "_total_banner_ad_seconds";
    var total_banner_ad_seconds = Number(
      $("#" + total_banner_ad_sec_id).text()
    );
    if (
      banner_amount < 0 ||
      isNaN(banner_amount) ||
      $("#" + banner_amount_id).val().length == 0
    ) {
      banner_amount = 0;
      $("#" + banner_amount_id).val(0.0);
    }
    var new_banner_rate = (banner_amount * 10) / total_banner_ad_seconds;
    $("#" + banner_rate_id).val(new_banner_rate.toFixed(2));
    calculate_network_amount(schedule_data[index].channels_data_array, index);
  });

  //calculating network payout  using network share
  $(document).on("blur", ".networkshareclass", function(e) {
    var network_share_id = e.currentTarget.id;
    var nw_share_id_arr = network_share_id.split("_");
    var nw_index = nw_share_id_arr[0];
    calculate_network_payout(nw_index);
  });

  //calculating amount using network payout
  $(document).on("blur", ".networkpayoutclass", function(e) {
    var nw_payout_id = e.currentTarget.id;
    var nw_payout = Number(e.currentTarget.value);
    var nw_id_arr = nw_payout_id.split("_");
    var nw_index = nw_id_arr[0];
    var old_nw_amount = Number($("#" + nw_index + "_network_amount").text());
    var nw_share = Number($("#" + nw_index + "_network_share").val());
    if (nw_payout > 0 && nw_share == 0) {
      $("#" + nw_payout_id).val("0");
    } else if (nw_payout == 0 && nw_share > 0) {
      calculate_network_payout(nw_index);
    } else if (nw_payout > 0 && nw_share > 0) {
      var new_nw_amount = (nw_payout * 100) / nw_share;
      $("#" + nw_index + "_network_amount").text(new_nw_amount.toFixed(2));
      $.map(schedule_data[nw_index].channels_data_array, function(
        channel,
        index
      ) {
        if (channel.total_spot_ad_seconds > 0) {
          var old_spot_amount = Number(
            $("#" + nw_index + "_" + channel.channel_id + "_spot_amount").val()
          );
          var percentage = old_spot_amount / old_nw_amount;
          var new_spot_amount = percentage * new_nw_amount;
          $("#" + nw_index + "_" + channel.channel_id + "_spot_amount").val(
            new_spot_amount.toFixed(2)
          );
          var total_spot_ad_seconds = Number(
            $(
              "#" +
                nw_index +
                "_" +
                channel.channel_id +
                "_total_spot_ad_seconds"
            ).text()
          );
          var new_spot_rate = (new_spot_amount * 10) / total_spot_ad_seconds;
          $("#" + nw_index + "_" + channel.channel_id + "_spot_rate").val(
            new_spot_rate.toFixed(2)
          );
        }
        if (channel.total_banner_ad_seconds > 0) {
          var old_banner_amount = Number(
            $(
              "#" + nw_index + "_" + channel.channel_id + "_banner_amount"
            ).val()
          );
          var percentage = old_banner_amount / old_nw_amount;
          var new_banner_amount = percentage * new_nw_amount;
          $("#" + nw_index + "_" + channel.channel_id + "_banner_amount").val(
            new_banner_amount.toFixed(2)
          );
          var total_banner_ad_seconds = Number(
            $(
              "#" +
                nw_index +
                "_" +
                channel.channel_id +
                "_total_banner_ad_seconds"
            ).text()
          );
          var new_banner_rate =
            (new_banner_amount * 10) / total_banner_ad_seconds;
          $("#" + nw_index + "_" + channel.channel_id + "_banner_rate").val(
            new_banner_rate.toFixed(2)
          );
        }
      });
      calculate_total_network_payout();
    } else {
      $("#" + nw_payout_id).val(0);
      calculate_network_payout(nw_index);
    }
  });
});

function approve(id) {
  $("#modal_title").text("APPROVED RO");
  $("#Modal_body").html("");
  console.log("approve");
  $.ajax(BASE_URL + "/ro_manager/ro_approved", {
    async: true,
    data: {
      id: id
    },
    dataType: "json",
    type: "POST",
    beforeSend: function() {
      $("#loader_background").css("display", "block");
      $("#loader_spin").css("display", "block");
    },
    complete: function() {
      $("#loader_background").css("display", "none");
      $("#loader_spin").css("display", "none");
    },
    success: function(responsedata) {
      console.log(responsedata);
      $("#loader_background").css("display", "none");
      $("#loader_spin").css("display", "none");
      if (!responsedata.isLoggedIn) {
        window.location.href = BASE_URL;
        return false;
      } else if (responsedata.Status == "fail") {
        alert(responsedata.Message);
        return false;
      } else if ("jsonData" in responsedata.Data) {
        var data = responsedata.Data.jsonData;
        $("#myModal").modal("show");
        $("#myModal").data("bs.modal")._config.backdrop = "static";
        var cust_ro = data.submit_ext_ro_data[0].cust_ro;
        var internal_ro = data.submit_ext_ro_data[0].internal_ro;
        var submitted_by = data.submit_ext_ro_data[0].submitted_by;
        var approved_by = data.submit_ext_ro_data[0].approved_by;
        var agency_name = data.submit_ext_ro_data[0].agency;
        var advertiser_name = data.submit_ext_ro_data[0].client;
        var brand_name = data.submit_ext_ro_data[0].brand_name;
        var ro_start_date = data.submit_ext_ro_data[0].camp_start_date;
        var ro_end_date = data.submit_ext_ro_data[0].camp_end_date;
        var ro_status = data.ro_status_entry;
        var gross_amount = data.ro_amount_detail[0].ro_amount;
        internal_ro_no = internal_ro;
        submit_ext = data.submit_ext_ro_data;
        ro_id = data.submit_ext_ro_data[0].id;

        //Ro details
        var customer_release_order =
          "<div class='custom_head'>" +
          "<h2>CUSTOMER RELEASE ORDER</h2>" +
          "</div>" +
          "<div class='table_div'>" +
          "<table class='table' width='100%'>" +
          "<tr>" +
          "<th>Customer RO Number</th>" +
          "<th>Submitted By</th>" +
          "<th>Approved By</th>" +
          "<th>Agency Name</th>" +
          "<th>Advertiser Name</th>" +
          "<th>Brand Name</th>" +
          "<th>RO start date</th>" +
          "<th>Ro end Date</th>" +
          "<th>Ro status</th>" +
          "<th>Gross CRO Amount</th>" +
          "</tr>" +
          "<tr>" +
          "<td>" +
          cust_ro +
          "<br>" +
          "(" +
          internal_ro +
          ")" +
          "</td>" +
          "<td>" +
          submitted_by +
          "</td>" +
          "<td>" +
          approved_by +
          "</td>" +
          "<td>" +
          agency_name +
          "</td>" +
          "<td>" +
          advertiser_name +
          "</td>" +
          "<td>" +
          brand_name +
          "</td>" +
          "<td>" +
          ro_start_date +
          "</td>" +
          "<td>" +
          ro_end_date +
          "</td>" +
          "<td>" +
          ro_status +
          "</td>" +
          "<td>" +
          gross_amount +
          "</td>" +
          "</tr>" +
          "</table>" +
          "</div>";
        $("#Modal_body").append(customer_release_order);
        var option =
          "<div id='option_div'>" +
          "<span class='link' id='campaigns_schedule_span'>Campaign Schedule</span>" +
          "<span class='link' id='channels_schedule_span'>Channels Schedule</span>" +
          "<span class='link' id='nw_ro_payment_span'>Network Ro Payment</span>";
        if (data.is_ro_completed == 1) {
          option =
            option +
            "<span class='invalid' id='ro_completed_div'>Ro Completed</span>";
        } else if (
          data.is_cancel_market_requested == 0 &&
          data.cancel_request_status == 1
        ) {
          option =
            option +
            "<span class='invalid' class='cancelled_span' >Cancelled</span></div>";
        }
        var cancel = "";
        if (data.is_cancel_market_requested == 1) {
          cancel =
            "<div class='cancel_div'>Can't Cancel RO! Market Cancellation Request is Pending !! </div>";
        }
        $("#Modal_body").append(option);
        $("#Modal_body").append(cancel);
        var network_summary = "";

        //Network Summary
        if (data.scheduled_data.length == 0) {
          network_summary =
            "<div class='custom_head' ><h2>No Active Campaign</h2></div>";
        } else {
          schedule_data = data.scheduled_data;
          network_summary =
            "<form id='approved_form'><div  class='custom_head'>" +
            "<h2>Network Ro Summary</h2>" +
            "</div>" +
            "<div id='network_summary_table' class='table_div'>" +
            "<div class='row custom_margin'>" +
            "<div class='col-1'><b>Gross CRO Amount</b></div>" +
            "<div class='col-1'><b>Media Agency Commission</b></div>" +
            "<div class='col-1'><b>Net Amount</b></div>" +
            "<div class='col-1'><b>Agency Rebate</b></div>" +
            "<div class='col-1'><b>Other Expenses</b></div>" +
            "<div class='col-1'><b>Actual Net Amount</b></div>" +
            "<div class='col-1'><b>Net Revenue</b></div>" +
            "<div class='col-1'><b>Total Network's Payout</b></div>" +
            "<div class='col-1'><b>Net Contribution Amount</b></div>" +
            "<div class='col-1'><b>Net Contribution Almount(%)</b></div>" +
            "</div>";
          if (typeof data.ro_amount_detail[0].ro_amount == undefined) {
            var other_expenses =
              Number(data.ro_amount_detail[0].marketing_promotion_amount) +
              Number(data.ro_amount_detail[0].field_activation_amount) +
              Number(data.ro_amount_detail[0].sales_commissions_amount) +
              Number(data.ro_amount_detail[0].creative_services_amount) +
              Number(data.ro_amount_detail[0].other_expenses_amount);

            network_summary =
              network_summary +
              "<div class='row custom_margin '>" +
              "<div class='col-1 custom_border'>In process</div>" +
              "<div class='col-1 custom_border'>In process</div>" +
              "<div class='col-1 custom_border'>In process</div>" +
              "<div class='col-1 custom_border'>In process</div>" +
              "<div class='col-1 custom_border'>" +
              other_expenses;
            if (data.logged_in_user.profile_id == 1) {
              network_summary =
                network_summary +
                "<div class='link' id='view_modify_span'>View/Modify</div></div>";
            } else {
              network_summary = network_summary + "</div>";
            }
            network_summary =
              network_summary +
              "<div class='col-1 custom_border'>In process</div>" +
              "<div class='col-1 custom_border'>In process</div>" +
              "<div class='col-1 custom_border'>" +
              data.total_nw_payout +
              "</div>" +
              "<div class='col-1 custom_border'>In process</div>" +
              "<div class='col-1 custom_border'>In process</div>" +
              "</div>";
          } else {
            //agency rebate and other expenses haven't been used since the beginning of the easy ro.
            var agency_rebate = 0;
            var other_expenses = 0;
            var net_amount =
              Number(data.ro_amount_detail[0].ro_amount) -
              Number(data.ro_amount_detail[0].agency_commission_amount);
            network_summary =
              network_summary +
              "<div class='row custom_margin '>" +
              "<div class='col-1 custom_border' id='ro_amount'>" +
              data.ro_amount_detail[0].ro_amount +
              "</div>" +
              "<div class='col-1 custom_border' id='agency_commission_amount'>" +
              data.ro_amount_detail[0].agency_commission_amount +
              "</div>" +
              "<div class='col-1 custom_border' id='net_amount'>" +
              net_amount +
              "</div>";
            if (Number(data.ro_amount_detail[0].ro_valid_field) == 1) {
              network_summary =
                network_summary +
                "<div class='col-1 custom_border' style='color:green'><span  id='agency_rebate'>0</span></div>" +
                "<div class='col-1 custom_border'   style='color:green'><span  id='other_expenses'>0</span>";
              if (data.logged_in_user.profile_id == 1) {
                network_summary =
                  network_summary +
                  "<div class='link' id='view_modify_span' >View/Modify</div></div>";
              }
            } else {
              other_expenses =
                Number(data.ro_amount_detail[0].marketing_promotion_amount) +
                Number(data.ro_amount_detail[0].field_activation_amount) +
                Number(data.ro_amount_detail[0].sales_commissions_amount) +
                Number(data.ro_amount_detail[0].creative_services_amount) +
                Number(data.ro_amount_detail[0].other_expenses_amount);

              if (data.ro_amount_detail[0].agency_rebate_on != "net_amount") {
                agency_rebate =
                  (Number(data.ro_amount_detail[0].ro_amount) *
                    Number(data.ro_amount_detail[0].agency_rebate)) /
                  100;
              } else {
                agency_rebate =
                  ((Number(data.ro_amount_detail[0].ro_amount) -
                    Number(data.ro_amount_detail[0].agency_commission_amount)) *
                    Number(data.ro_amount_detail[0].agency_rebate)) /
                  100;
              }
              network_summary =
                network_summary +
                "<div class='col-1 custom_border' name='agency_rebate' id='agency_rebate' style='color:green'>" +
                agency_rebate +
                "</div>" +
                "<div class='col-1 custom_border'  name='other_expenses' style='color:green'><span id='other_expenses'>" +
                other_expenses +
                "</span>";
              if (data.logged_in_user.profile_id == 1) {
                network_summary =
                  network_summary +
                  "<div id='view_modify_span' class='link'>View/Modify</div></div>";
              } else {
                network_summary = network_summary + "</div>";
              }
            }
            var actual_net_amount =
              Number(data.ro_amount_detail[0].ro_amount) -
              Number(data.ro_amount_detail[0].agency_commission_amount) -
              Number(agency_rebate) -
              Number(other_expenses);
            var net_revenue =
              Number(data.ro_amount_detail[0].ro_amount) -
              Number(data.ro_amount_detail[0].agency_commission_amount);
            net_revenue = net_revenue * 1.0;
            var total_nw_payout = data.total_nw_payout;
            var surewaves_share = actual_net_amount - total_nw_payout;
            var surewaves_share_per = (surewaves_share / net_revenue) * 100;
            network_summary =
              network_summary +
              "<div class='col-1 custom_border' id='actual_net_amount'>" +
              actual_net_amount +
              "</div>" +
              "<div class='col-1 custom_border' id='net_revenue'>" +
              net_revenue +
              "</div>" +
              "<div class='col-1 custom_border' ><span id='total_network_payout'>" +
              total_nw_payout.toFixed(2) +
              "</span></div>" +
              "<div class='col-1 custom_border' id='surewaves_share'>" +
              surewaves_share.toFixed(2) +
              "</div>" +
              "<div class='col-1 custom_border' id='surewaves_share_per'>" +
              surewaves_share_per.toFixed(2) +
              "</div></div>" +
              "<div class='row'><div class='col-12'><input type='button' style='display: none' id='final_save_btn' value='Final Save'  class='btn btn-primary'></div></div>";
          }
          network_summary = network_summary + "</div>";

          //Network Details
          network_summary =
            network_summary +
            "<div class='market_priority_div' id='network_detail_div' data-toggle='collapse' data-target='#network_detail_table_div'>" +
            "<h2>Network Details</h2>" +
            "<i id='network_i' class='down'></i></div>" +
            "<div id='network_detail_table_div' class='table_div collapse'>" +
            "<table class='table' id='channel_table'>" +
            "<tr>" +
            "<th style='display: none'>&nbsp;</th>" +
            "<th style='display: none'>&nbsp;</th>" +
            "<th style='display: none'>&nbsp;</th>" +
            "<th style='display: none'>&nbsp;</th>" +
            "<th style='display: none'>&nbsp;</th>" +
            "<th style='display: none'>&nbsp;</th>" +
            "<th style='display: none'>&nbsp;</th>" +
            "<th style='display: none'>&nbsp;</th>" +
            "<th style='display: none'>&nbsp;</th>" +
            "</tr>";
          $.map(data.scheduled_data, function(network, index) {
            var additional_campaign = 0;
            var approved = 1;
            network_summary =
              network_summary +
              "<tr>" +
              "<td colspan='9' class='network_name_style'>Network Name:" +
              network.network_name +
              "(" +
              network.market_name +
              ")";
            if (
              data.is_ro_completed == 0 &&
              (network.approved == 1 || data.verify_ro_approved == 1) &&
              data.logged_in_user.profile_id == 1
            ) {
              network_summary =
                network_summary +
                "<input type='button' class='btn btn-primary editbtnclass' id='" +
                index +
                "_edit_btn' value='EDIT'><input type='button' class='btn btn-primary savebtnclass' id='" +
                index +
                "_save_btn' value='SAVE'>";
            }
            network_summary = network_summary + "</td>" + "</tr>";
            var nw_amount = 0;
            $.map(network.channels_data_array, function(channel, ind) {
              if (channel.approved == 0) {
                approved = 0;
              }
              if (channel.additional_campaign == 1) {
                additional_campaign = 1;
                network_summary =
                  network_summary +
                  "<tr>" +
                  "<td style='background-color:rgba(244, 208, 63,0.5)' colspan='9' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_checkbox_td'>" +
                  "<input type='checkbox'";
                if (data.is_ro_completed == 1) {
                  network_summary = network_summary + " disabled ";
                }
                network_summary =
                  network_summary +
                  "data-approved='0' data-change='1' data-save='1' data-total_spot_seconds='" +
                  channel.total_spot_ad_seconds +
                  "' " +
                  "data-total_banner_seconds='" +
                  channel.total_banner_ad_seconds +
                  "' data-to_play_spot='" +
                  channel.to_play_spot +
                  "'" +
                  " data-to_play_banner='" +
                  channel.to_play_banner +
                  "'  data-cancel_channel='0' data-additional_campaign='1'" +
                  " class='checkboxclass' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_checkbox'  checked='checked' value='" +
                  channel.channel_id +
                  "'>Channel Name:" +
                  channel.channel_name +
                  "</td>" +
                  "</tr>";
              } else if (
                channel.approved == 0 &&
                channel.additional_campaign == 0
              ) {
                network_summary =
                  network_summary +
                  "<tr>" +
                  "<td style='background-color: rgba(220, 198, 224, 0.6)' colspan='9' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_checkbox_td'>" +
                  "<input type='checkbox'";
                if (data.is_ro_completed == 1) {
                  network_summary = network_summary + " disabled ";
                }
                network_summary =
                  network_summary +
                  "data-approved='0' data-change='1' data-save='1' data-total_spot_seconds='" +
                  channel.total_spot_ad_seconds +
                  "' " +
                  "data-total_banner_seconds='" +
                  channel.total_banner_ad_seconds +
                  "' data-to_play_spot='" +
                  channel.to_play_spot +
                  "'" +
                  " data-to_play_banner='" +
                  channel.to_play_banner +
                  "'  data-cancel_channel='0' data-additional_campaign='0'" +
                  " class='checkboxclass' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_checkbox'  checked='checked' value='" +
                  channel.channel_id +
                  "'>Channel Name:" +
                  channel.channel_name +
                  "</td>" +
                  "</tr>";
              } else {
                network_summary =
                  network_summary +
                  "<tr>" +
                  "<td  colspan='9' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_checkbox_td'>" +
                  "<input type='checkbox'";
                if (data.is_ro_completed == 1) {
                  network_summary = network_summary + " disabled ";
                }
                network_summary =
                  network_summary +
                  "data-approved='1' data-change='0' data-save='0' data-total_spot_seconds='" +
                  channel.total_spot_ad_seconds +
                  "'" +
                  " data-total_banner_seconds='" +
                  channel.total_banner_ad_seconds +
                  "' data-to_play_spot='" +
                  channel.to_play_spot +
                  "'" +
                  " data-to_play_banner='" +
                  channel.to_play_banner +
                  "' disabled data-cancel_channel='0' data-additional_campaign='0'" +
                  " class='checkboxclass' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_checkbox'  checked='checked' value='" +
                  channel.channel_id +
                  "'>Channel Name:" +
                  channel.channel_name +
                  "</td>" +
                  "</tr>";
              }
              network_summary =
                network_summary +
                "<tr>" +
                "<td colspan='3'><b>Ad Type</b></td>" +
                "<td colspan='2'><b>Total Ad Seconds</b></td>" +
                "<td colspan='1'><b>Last Rate</b></td>" +
                "<td colspan='2'><b>Reference Rate</b></td>" +
                "<td colspan='1'><b>Amount</b></td>" +
                "</tr>";
              if (channel.total_spot_ad_seconds > 0) {
                console.log("spot");
                network_summary =
                  network_summary +
                  "<tr>" +
                  "<td colspan='3'>Spot Ad</td>" +
                  "<td colspan='2' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_total_spot_ad_seconds'>" +
                  channel.total_spot_ad_seconds +
                  "</td>" +
                  "<td ><input type='text'";
                if (
                  (channel.additional_campaign == 0 && channel.approved == 1) ||
                  data.is_ro_completed == 1
                ) {
                  console.log("inside first if");
                  network_summary = network_summary + " disabled='disabled' ";
                }
                network_summary =
                  network_summary +
                  "class='form-control stylewidth spotclass' name='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_spot_rate' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_spot_rate' value='" +
                  channel.channel_spot_avg_rate +
                  "'></td>" +
                  "<td colspan='2'>" +
                  channel.channel_spot_reference_rate +
                  "</td>" +
                  "<td ><input type='text'";
                if (
                  (channel.additional_campaign == 0 && channel.approved == 1) ||
                  data.is_ro_completed == 1
                ) {
                  console.log("inside second if");
                  network_summary = network_summary + " disabled='disabled' ";
                }
                network_summary =
                  network_summary +
                  "class='form-control stylewidth spotamountclass' name='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_spot_amount' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_spot_amount' value='" +
                  channel.channel_spot_amount +
                  "'></td>";
                nw_amount = nw_amount + channel.channel_spot_amount;
              }
              if (channel.total_banner_ad_seconds > 0) {
                network_summary =
                  network_summary +
                  "<tr>" +
                  "<td colspan='3'>Banner Ad</td>" +
                  "<td colspan='2' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_total_banner_ad_seconds'>" +
                  channel.total_banner_ad_seconds +
                  "</td>" +
                  "<td><input type='text'";
                if (
                  (channel.additional_campaign == 0 && channel.approved == 1) ||
                  data.is_ro_completed == 1
                ) {
                  console.log("inside second if");
                  network_summary = network_summary + " disabled='disabled' ";
                }
                network_summary =
                  network_summary +
                  "class='form-control stylewidth bannerclass' name='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_banner_rate' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_banner_rate' value='" +
                  channel.channel_banner_avg_rate +
                  "'></td>" +
                  "<td colspan='2'>" +
                  channel.channel_banner_reference_rate +
                  "</td>" +
                  "<td><input type='text'";
                if (
                  (channel.additional_campaign == 0 && channel.approved == 1) ||
                  data.is_ro_completed == 1
                ) {
                  console.log("inside second if");
                  network_summary = network_summary + " disabled='disabled' ";
                }
                network_summary =
                  network_summary +
                  "class='form-control stylewidth banneramountclass' name='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_banner_amount' id='" +
                  index +
                  "_" +
                  channel.channel_id +
                  "_banner_amount' value='" +
                  channel.channel_banner_amount +
                  "'></td>";
                nw_amount = nw_amount + channel.channel_banner_amount;
                console.log("nw_amount " + nw_amount);
              }
            });
            var nw_payout = 0;
            if (nw_amount > 0 && network.revenue_sharing > 0) {
              nw_payout = (nw_amount * network.revenue_sharing) / 100;
            }
            network_summary =
              network_summary +
              "<tr>" +
              "<td colspan='3'><b>Network Ro Amount</b></td>" +
              "<td colspan='2'>&nbsp;</td>" +
              "<td>&nbsp;</td>" +
              "<td colspan='2'>&nbsp;</td>" +
              "<td  id='" +
              index +
              "_network_amount'>" +
              nw_amount +
              "</td>" +
              "</tr>" +
              "<tr>" +
              "<td colspan='3'><b>Network Share(%)</b></td>" +
              "<td colspan='2'>&nbsp;</td>" +
              "<td>&nbsp;</td>" +
              "<td colspan='2'></td>" +
              "<td><input type='text'";
            if (
              (additional_campaign == 0 && approved == 1) ||
              data.is_ro_completed == 1
            ) {
              network_summary = network_summary + " disabled='disabled' ";
            }
            network_summary =
              network_summary +
              "class='form-control stylewidth networkshareclass' name='" +
              index +
              "_network_share' id='" +
              index +
              "_network_share' value='" +
              network.revenue_sharing +
              "'></td>" +
              "</tr>" +
              "<tr>" +
              "<td colspan='3'><b>Network Payout(Net)</b></td>" +
              "<td colspan='2'>&nbsp;</td>" +
              "<td>&nbsp;</td>" +
              "<td colspan='2'>&nbsp;</td>" +
              "<td><input type='text'";
            if (
              (additional_campaign == 0 && approved == 1) ||
              data.is_ro_completed == 1
            ) {
              network_summary = network_summary + " disabled='disabled' ";
            }

            network_summary =
              network_summary +
              "name='" +
              index +
              "_network_payout' id='" +
              index +
              "_network_payout' class='form-control stylewidth networkpayoutclass' value='" +
              nw_payout.toFixed(2) +
              "'</td> " +
              "</tr>";
          });
          network_summary = network_summary + "</table>" + "</div>";
          network_summary = network_summary + "</form>";
        }
        $("#Modal_body").append(network_summary);
      } else if ("html" in responsedata.Data) {
        $("#myModal").modal("show");
        $("#myModal").data("bs.modal")._config.backdrop = "static";
        $("#Modal_body").html(responsedata.Data.html);
      } else {
        alert("Something Went Wrong!!");
      }
    }
  });
}

function calculate_network_amount(channel_arr, nw_index) {
  var amount = 0;
  var spot_amount_id;
  var banner_amount_id;
  $.map(channel_arr, function(value, index) {
    var channel_id = value.channel_id;
    if (value.total_spot_ad_seconds > 0) {
      spot_amount_id = nw_index + "_" + value.channel_id + "_spot_amount";
      var spot_amount = Number($("#" + spot_amount_id).val());
      amount = amount + spot_amount;
    }
    if (value.total_banner_ad_seconds > 0) {
      banner_amount_id = nw_index + "_" + value.channel_id + "_banner_amount";
      var banner_amount = Number($("#" + banner_amount_id).val());
      amount = amount + banner_amount;
    }
  });
  $("#" + nw_index + "_network_amount").text(amount);
  calculate_network_payout(nw_index);
}

function calculate_network_payout(nw_index) {
  var nw_payout = 0;
  var nw_payout_id = nw_index + "_network_payout";
  var nw_amount_id = nw_index + "_network_amount";
  var nw_share_id = nw_index + "_network_share";
  var nw_share = Number($("#" + nw_share_id).val());
  var nw_amount = Number($("#" + nw_amount_id).text());
  if (nw_amount > 0 && nw_share > 0) {
    nw_payout = (nw_amount * nw_share) / 100;
  } else {
    nw_share = 0;
    $("#" + nw_share_id).val(0);
  }
  $("#" + nw_payout_id).val(nw_payout);
  calculate_total_network_payout();
}

function calculate_total_network_payout() {
  var total_nw_payout = 0;
  $.map(schedule_data, function(value, index) {
    var nw_payout_id = index + "_" + "network_payout";
    var nw_payout = Number($("#" + nw_payout_id).val());
    if (nw_payout > 0) {
      total_nw_payout = total_nw_payout + nw_payout;
    }
  });
  $("#total_network_payout").text(total_nw_payout.toFixed(2));
  var actual_net_amount = Number($("#actual_net_amount").text());
  var surewaves_share = actual_net_amount - total_nw_payout;
  var agency_commission_amount = Number($("#agency_commission_amount").text());
  var net_revenue = Number($("#net_revenue").text()) * SERVICE_TAX;
  var surewaves_share_per = (surewaves_share / net_revenue) * 100;
  $("#surewaves_share").text(surewaves_share.toFixed(2));
  $("#surewaves_share_per").text(surewaves_share_per.toFixed(2));
}

function createfinaldata() {
  var data = {};
  var internal_ro = internal_ro_no;
  var network_arr = [];
  $.map(schedule_data, function(network, nw_index) {
    var channels_data_arr = [];
    $.map(network.channels_data_array, function(channel, ind) {
      var channel_id = channel.channel_id;
      var additional_campaign = $(
        "#" + nw_index + "_" + channel_id + "_checkbox"
      ).attr("data-additional_campaign");
      var cancel_channel = $(
        "#" + nw_index + "_" + channel_id + "_checkbox"
      ).attr("data-cancel_channel");
      var data_change = $("#" + nw_index + "_" + channel_id + "_checkbox").attr(
        "data-change"
      );
      if (additional_campaign == 1 || cancel_channel == 1 || data_change == 1) {
        var channel_obj = {};
        channel_obj["channel_id"] = channel_id;
        channel_obj["channel_name"] = channel.channel_name;
        channel_obj["total_spot_ad_seconds"] = channel.total_spot_ad_seconds;
        channel_obj["total_banner_ad_seconds"] =
          channel.total_banner_ad_seconds;
        var channel_spot_avg_rate = 0;
        var channel_banner_avg_rate = 0;
        var channel_spot_amount = 0;
        var channel_banner_amount = 0;
        if (channel.total_spot_ad_seconds > 0) {
          channel_spot_avg_rate = $(
            "#" + nw_index + "_" + channel_id + "_spot_rate"
          ).val();
          channel_spot_amount = $(
            "#" + nw_index + "_" + channel_id + "_spot_amount"
          ).val();
        }
        if (channel.total_banner_ad_seconds > 0) {
          channel_banner_avg_rate = $(
            "#" + nw_index + "_" + channel_id + "_banner_rate"
          ).val();
          channel_banner_amount = $(
            "#" + nw_index + "_" + channel_id + "_banner_amount"
          ).val();
        }
        channel_obj["channel_spot_avg_rate"] = channel_spot_avg_rate;
        channel_obj["channel_banner_avg_rate"] = channel_banner_avg_rate;
        channel_obj["channel_spot_amount"] = channel_spot_amount;
        channel_obj["channel_banner_amount"] = channel_banner_amount;
        if (cancel_channel == 1) {
          channel_obj["cancel_channel"] = 1;
        } else {
          channel_obj["cancel_channel"] = 0;
        }
        channels_data_arr.push(channel_obj);
      }
    });

    if (channels_data_arr.length > 0) {
      var network_obj = {};
      network_obj["network_id"] = network.network_id;
      network_obj["network_name"] = network.network_name;
      network_obj["network_share"] = $("#" + nw_index + "_network_share").val();
      network_obj["channels_data_array"] = channels_data_arr;
      network_arr.push(network_obj);
    }
  });
  data["internalRoId"] = internal_ro;
  data["client_name"] = submit_ext[0].client;
  data["networks"] = network_arr;
  return data;
}
