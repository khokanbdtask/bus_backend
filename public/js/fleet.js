function myFunction() {
  var total = 0;
  var layout = $("#layout").val();

  if (!layout) {
    return false;
  }

  var totalseat = $("#total_seat").val();
  var totallayout = layout.split("-");
  var total = 0;
  var alpha = 65;
  var seatarray = [];

  for (var i = 0; i < totallayout.length; i++) {
    total += totallayout[i] << 0;
  }

  var lineseat = totalseat / total;
  var afterlineseat = totalseat % total;

  for (var j = 1; j <= lineseat; j++) {
    for (let x = 1; x <= total; x++) {
      var seat = String.fromCharCode(alpha) + x;
      seatarray.push(seat);
    }

    alpha += 1;
  }

  for (let k = 1; k <= afterlineseat; k++) {
    var remainseat = String.fromCharCode(alpha) + k;
    seatarray.push(remainseat);
  }

  if ($("#last_seat").is(":checked")) {
    var lastseat = String.fromCharCode(90);

    seatarray.push(lastseat);
  }

  let seatsnumber = seatarray.toString();
  $("#seat_number").val(seatsnumber);
}

$("#layout").change(function () {
  var layout = $("#layout").val();
  // console.log(layout);
  // return false;
  var baseurl = $("#baseurl").val();
  var url = baseurl + "/ajax/layout/details/" + layout;
  var layoutDeatails = [];
  $.ajax({
    method: "GET",
    url: url,
    dataType: "JSON",

    success: function (result) {
      if (result.response == 200) {
        layoutDeatails = JSON.parse(result.data);
        // console.log(layoutDeatails.seat_number);
        $("#total_seat").val(layoutDeatails.total_seat);
        $("#seat_number").val(layoutDeatails.seat_number);

        //disalbe seat number
        $("#total_seat").attr("readonly", true);
        $("#seat_number").attr("readonly", true);

      }

      if (result.response == 204) {
      }
    },
  });
});
