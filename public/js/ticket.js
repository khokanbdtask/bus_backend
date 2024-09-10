function clicked(thisObj) {
  if (confirm("Are you sure you want to generate a new tag?")) {
    sendAjaxRequest(thisObj);
  }
}

function sendAjaxRequest(thisObj) {
  var baseurl = $("#baseurl").val(),
    ticket_id = $(thisObj).attr("data-id"),
    url = baseurl + "modules/backend/tickets/generatetag";
  var csrfToken = $("#csrf").val();
  $.ajax({
    type: "POST",
    url: url,
    dataType: "json",
    headers: {
      "X-CSRF-TOKEN": csrfToken,
    },
    data: {
      ticket_id: ticket_id,
    },
    success: function (response) {
      alert(response.message);
      location.reload();
    },
  });
}
