var baseUrl = $("#baseUrl").val();
var csrfToken = $("#csrf").val();

//form submit
$(document).on("click", ".saveBtn", function () {
  if (!confirm("Are you sure to assign this driver?")) return false;
  var form = $(".addDriverToTripForm");
  var url = form.attr("action");
  var method = form.attr("method");
  var data = form.serialize();

  $.ajax({
    url: url,
    type: method,
    data: data,
    success: function (response) {
      if (response.status == true) {
        $("#assignDriver").modal("hide");
        alert(response.message);
      } else {
        alert(response.message);
      }
    },
  });
});

function getDirverList(tripId) {
  $.ajax({
    url: baseUrl + "modules/backend/trips/tripDriverList/" + tripId,
    type: "GET",
    success: function (response) {
      $("#driverList").modal("show");
      $("#driverList .modal-body").html(response);
    },
  });
}

function assignDirver(tripId) {
  $.ajax({
    url: baseUrl + "modules/backend/trips/assignDriver/" + tripId,
    type: "GET",
    success: function (response) {
      $("#assignDriver").modal("show");
      $("#assignDriver .modal-body").html(response);
    },
  });
}

function approveDriver(id) {
  if (confirm("Are you sure to approve this driver for this trip?")) {
    $.ajax({
      url: baseUrl + "modules/backend/trips/approveDriver/" + id,
      type: "PUT",
      headers: {
        "X-CSRF-TOKEN": csrfToken,
      },
      success: function (response) {
        if (response.status == true) {
          alert(response.message);
          location.reload();
        } else {
          alert(response.message);
          location.reload();
        }
      },
    });
  } else {
    return false;
  }
}

function deleteDriver(id) {
  if (confirm("Are you sure to delete this driver for this trip?")) {
    $.ajax({
      url: baseUrl + "modules/backend/trips/deleteDriver/" + id,
      type: "PUT",
      headers: {
        "X-CSRF-TOKEN": csrfToken,
      },
      success: function (response) {
        if (response.status == true) {
          alert(response.message);
          location.reload();
        } else {
          alert(response.message);
          location.reload();
        }
      },
    });
  } else {
    return false;
  }
}
