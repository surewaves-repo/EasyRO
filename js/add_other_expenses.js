var BASE_URL = "/surewaves_easy_ro";
var agency_rebate_val;
var net_amount_global;
$("document").ready(function() {
  $("#add_other_expenses_form").validate({
    errorClass: "invalid_error",
    errorElement: "div",
    rules: {
      agency_rebate: {
        required: true,
        check_agency_rebate: true
      },
      marketing_promotion_amount: {
        required: true,
        check_marketing_promotion: true
      },
      field_activation_amount: {
        required: true,
        check_field_activation: true
      },
      sales_commissions_amount: {
        required: true,
        check_sales_commissions: true
      },
      creative_services_amount: {
        required: true,
        check_creative_services: true
      },
      other_expenses_amount: {
        required: true,
        check_other_expenses: true
      }
    },
    messages: {
      agency_rebate: {
        required: "Enter value for agency rebate"
      },
      marketing_promotion_amount: {
        required: "Enter value for marketing promotion"
      },
      field_activation_amount: {
        required: "Enter value for field activation"
      },
      sales_commissions_amount: {
        required: "Enter value for sales commission"
      },
      creative_services_amount: {
        required: "Enter value for creative service amount"
      },
      other_expenses_amount: {
        required: "Enter value for other expenses"
      }
    },
    submitHandler: function(form, event) {
      var ro_amount = Number($("#ro_amount").text());
      console.log("ro_amount ", ro_amount);

      var agency_commission = Number($("#agency_commission_amount").text());
      console.log("agency_commission " + agency_commission);
      check(ro_amount, agency_commission);
      event.preventDefault();
      $.ajax(BASE_URL + "/ro_manager/post_add_other_expenses", {
        type: "POST",
        data: {
          agency_rebate: $("#agency_rebate_new").val(),
          sel_type: $("#sel_type").val(),
          marketing_promotion_amount: $("#marketing_promotion_amount").val(),
          field_activation_amount: $("#field_activation_amount").val(),
          sales_commissions_amount: $("#sales_commission_amount").val(),
          creative_services_amount: $("#creative_services_amount").val(),
          other_expenses_amount: $("#other_expenses_amount").val(),
          hid_ro_valid: $("#ro_valid_field").val(),
          hid_ro_amount: $("#hid_ro_amount").val(),
          hid_agency_commission: $("#hid_agency_commission").val(),
          hid_internal_ro: $("#hid_internal_ro").val(),
          hid_customer_ro: $("#hid_customer_ro").val(),
          hid_url: $("#hid_url").val(),
          hid_edit: $("#hid_edit").val(),
          hid_id: $("#hid_id").val(),
          hid_internal_ro: $("#hid_internal_ro").val()
        },
        dataType: "json",
        beforeSend: function() {
          $("#loader_background").css("display", "block");
          $("#loader_spin").css("display", "block");
        },
        success: function(responsedata) {
          $("#loader_background").css("display", "none");
          $("#loader_spin").css("display", "none");
          if (responsedata.Status == "success") {
            alert(responsedata.Message);
            var other_expense =
              Number($("#marketing_promotion_amount").val()) +
              Number($("#field_activation_amount").val()) +
              Number($("#sales_commission_amount").val()) +
              Number($("#creative_services_amount").val()) +
              Number($("#other_expenses_amount").val());
            console.log("other_expenses " + other_expense);
            console.log("agency_rebate " + agency_rebate_val);
            $("#agency_rebate").text(agency_rebate_val);
            $("#agency_rebate").css("color", "green");
            $("#other_expenses").text(other_expense);
            $("#other_expenses").css("color", "green");
            var actual_net_amount =
              Number(net_amount_global) -
              Number(other_expense) -
              Number(agency_rebate_val);
            console.log("actual_net_amount " + actual_net_amount);
            $("#actual_net_amount").text(actual_net_amount.toFixed(2));
            var surewaves_share =
              Number(actual_net_amount) -
              Number($("#total_network_payout").text());
            var surewaves_share_per =
              (Number(surewaves_share) / Number(net_amount_global)) * 100;
            $("#surewaves_share").text(surewaves_share.toFixed(2));
            $("#surewaves_share_per").text(surewaves_share_per.toFixed(2));
            $("#confirmModal").modal("hide");
          } else if (responsedata.Status == "fail") {
            alert(responsedata.Message);
          } else {
            alert("Something went wrong!!");
          }
        },
        error: function() {
          $("#loader_background").css("display", "none");
          $("#loader_spin").css("display", "none");
          alert("error in saving values");
        }
      });
    }
  });
  $.validator.addMethod(
    "check_agency_rebate",
    function(value, element) {
      if (isNaN(Number(value))) {
        console.log("nan");
        return false;
      } else if (0 > value || value > 100) {
        return false;
      }
      return true;
    },
    "Enter value of agency rebate between 0 and 100"
  );

  $.validator.addMethod(
    "check_marketing_promotion",
    function(value, element) {
      var regExp = /^[0-9]+$/;
      if (regExp.test(value)) {
        return true;
      } else {
        return false;
      }
    },
    "Enter positive integer for marketing promotion"
  );

  $.validator.addMethod(
    "check_field_activation",
    function(value, element) {
      var regExp = /^[0-9]+$/;
      if (regExp.test(value)) {
        return true;
      } else {
        return false;
      }
    },
    "Enter positive integer for field activation"
  );

  $.validator.addMethod(
    "check_sales_commissions",
    function(value, element) {
      var regExp = /^[0-9]+$/;
      if (regExp.test(value)) {
        return true;
      } else {
        return false;
      }
    },
    "Enter positive integer for sales commission"
  );

  $.validator.addMethod(
    "check_creative_services",
    function(value, element) {
      var regExp = /^[0-9]+$/;
      if (regExp.test(value)) {
        return true;
      } else {
        return false;
      }
    },
    "Enter positive integer for creative services"
  );

  $.validator.addMethod(
    "check_other_expenses",
    function(value, element) {
      var regExp = /^[0-9]+$/;
      if (regExp.test(value)) {
        return true;
      } else {
        return false;
      }
    },
    "Enter positive integer for other expenses"
  );
});

function check(ro_amount, agency_commission) {
  var net_amount = parseFloat(ro_amount - agency_commission);
  net_amount_global = net_amount;
  var agency_rebate = parseFloat(
    (document.getElementById("agency_rebate_new").value * ro_amount) / 100
  );
  var markt_promotion = parseFloat(
    document.getElementById("marketing_promotion_amount").value
  );
  var filed_activation = parseFloat(
    document.getElementById("field_activation_amount").value
  );
  var sales_commission = parseFloat(
    document.getElementById("sales_commission_amount").value
  );
  var creative_services = parseFloat(
    document.getElementById("creative_services_amount").value
  );
  var other_expenses = parseFloat(
    document.getElementById("other_expenses_amount").value
  );

  //	var total_expenses = agency_rebate+ markt_promotion+filed_activation+sales_commission+creative_services+other_expenses;
  if ($("#sel_type").val() == "ro_amount") {
    var total_expenses = parseFloat(
      (document.getElementById("agency_rebate_new").value * ro_amount) / 100
    );
    agency_rebate_val = total_expenses;
    console.log("agency_rebate_val " + agency_rebate_val);
  } else {
    var total_expenses = parseFloat(
      (document.getElementById("agency_rebate_new").value * net_amount) / 100
    );
    agency_rebate_val = total_expenses;
    console.log("agency_rebate_val " + agency_rebate_val);
  }
  total_expenses += parseFloat(
    document.getElementById("marketing_promotion_amount").value,
    10
  );
  total_expenses += parseFloat(
    document.getElementById("field_activation_amount").value,
    10
  );
  total_expenses += parseFloat(
    document.getElementById("sales_commission_amount").value,
    10
  );
  total_expenses += parseFloat(
    document.getElementById("creative_services_amount").value,
    10
  );
  total_expenses += parseFloat(
    document.getElementById("other_expenses_amount").value,
    10
  );

  if (net_amount <= total_expenses) {
    alert(
      "Please make sure that the sum of all expenses is less than the Net Amount"
    );
  }
}
