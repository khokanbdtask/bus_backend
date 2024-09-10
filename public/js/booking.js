var websetting = null;

$(document).ready(function () {
  // $("#detailpay" ).hide();
  // $("#payment_method").hide();

  var baseurl = $("#baseurl").val();

  $(".toggle-seat-select").on("click", function (e) {
    if (!$(this).hasClass("collapsed")) {
      var id = $("button", this).attr("id"),
        baseurl = $("#baseurl").val(),
        journeydate = $("#journeydate").val(),
        url = `${baseurl}/modules/backend/tickets/singletrip/${id}/${journeydate}`;

      var $form = $("#form_" + id),
        $seat = $("#seat_" + id),
        $loader = $("#page-loader");

      $seat.html("").append($loader.clone().removeAttr("id"));
      $form.html("").append($loader.clone().removeAttr("id"));

      setTimeout(() => {
        $.ajax({
          method: "GET",
          url: url,
          dataType: "JSON",
          success: function (result) {
            if (result.status == "error") {
              alert(result.message);
              location.reload();
              return;
            } else {
              // console.log(result);
              $("html, body").animate(
                { scrollTop: $form.offset().top - 250 },
                500
              );
              dynamicseat(result, id).done(function (response) {
                $seat.html(response);
              });

              (async (c) => {
                await c.web_settings.init();
                await c.lang.init();

                $form.html(dynamichtml(result, id));
              })(bdtaskIlmCommonJs);
            }
          },
        });
      }, 500);
    }
  });

  $("#partial").hide();

  $("#payment_status").change(function () {
    var paystats = $("#payment_status").val();

    if (paystats == "paid") {
      $("#detailpay").show();
      $("#payment_method").show();
      $("#partial").hide();
      $("#partialpay").val(0);
      $("#couponcode").show();
      $("#less").show();
    }

    if (paystats == "partial") {
      var payamount = $("#grandtotal").val();

      $("#detailpay").show();
      $("#payment_method").show();
      $("#partial").show();
      $("#partialpay").val(payamount);
      $("#couponcode").show();
      $("#discount").show();
    }

    if (paystats == "unpaid") {
      var oldGrandtotal = $("#oldgrandtotal").val();
      $("#discount").attr("readonly", false);
      $("#coupon").attr("readonly", false);
      $("#detailpay").hide();
      $("#payment_method").hide();
      $("#couponcode").hide();
      $("#less").hide();
      $("#grandtotal").val(oldGrandtotal);
      $("#discount").val(0);
      $("#partialpay").val(oldGrandtotal);
      $("#coupon").val("");
      $("#couponmessage").html("");
      $("#partial").val("");
      $("#partial").hide();
    }
  });

  $("#discount").focusout(function () {
    var discount = $("#discount").val();
    var oldtotal = $("#oldgrandtotal").val();
    discount = parseFloat(discount);
    if (discount == 0) {
      $("#grandtotal").val(oldtotal);
      $("#partialpay").val(oldtotal).prop("max", oldtotal);
    } else {
      var newgrandtotal = parseFloat(oldtotal) - discount;

      $("#grandtotal").val(newgrandtotal);
      $("#partialpay").val(newgrandtotal).prop("max", newgrandtotal);
    }
  });
});

function dynamicseat(result, id) {
  var baseurl = $("#baseurl").val(),
    subtrip = JSON.parse(result.subtrips)[0],
    seatnumber = subtrip.seat_number,
    seatbook = JSON.parse(result.bookseat);

  var seatarray = seatnumber.split(","),
    bookseatarray = seatbook.split(",");

  return $.post(`${baseurl}/modules/backend/tickets/seatlayout`, {
    id: id,
    fleet_id: subtrip.fleet_id,
    booked: bookseatarray,
  });
}

function seacClick(seatElem, subtripid, seatlabel) {
  var seatArray = [],
    seatvalue = $("#seatnumber" + subtripid).val(),
    // id = seatindex.getAttribute('id'),
    numberseat = $(seatElem).data("seatnumber"),
    baseurl = $("#baseurl").val();

  if (seatlabel == "booked") {
    return;
  }

  if (sessionStorage.getItem("tripid")) {
    if (sessionStorage.getItem("tripid") != subtripid) {
      var removeid = sessionStorage.getItem("tripid");
      sessionStorage.removeItem("tripid");
      var removeclass = [];
      var removeseat = $("#seatnumber" + removeid).val();
      if (typeof removeseat !== "undefined" && removeseat != "") {
        removeclass = removeseat.split(",");
      }

      removeclass = $.grep(removeclass, function (value) {
        $("#seat_" + removeid + "_" + value)
          .removeClass("btn btn-success")
          .addClass("btn btn-light");
      });

      var newseatValue = "";
      $("#seatnumber" + removeid).val(newseatValue);
    }
  }

  if (seatvalue == "") {
    $("#seatnumber" + subtripid).val(numberseat);

    sessionStorage.removeItem("tripid");
    sessionStorage.setItem("tripid", subtripid);

    // highlight selected
    $(seatElem).addClass("seat-checked");
    $("img", seatElem).attr("src", `${baseurl}/public/image/seatselected.svg`);
  } else {
    if (
      sessionStorage.getItem("tripid") &&
      sessionStorage.getItem("tripid") == subtripid
    ) {
      var totalseat = seatvalue.split(",");
      var hasVal = Object.values(totalseat).includes(numberseat);

      if (hasVal) {
        totalseat = $.grep(totalseat, function (value) {
          return value != numberseat;
        });

        let newSeats = totalseat.toString();
        $("#seatnumber" + subtripid).val(newSeats);

        // highlight de-selected
        $(seatElem).removeClass("seat-checked");
        $("img", seatElem).attr(
          "src",
          `${baseurl}/public/image/seatavailable.svg`
        );
      } else {
        totalseat.push(numberseat);
        let newSeats = totalseat.toString();
        $("#seatnumber" + subtripid).val(newSeats);

        // highlight selected
        $(seatElem).addClass("seat-checked");
        $("img", seatElem).attr(
          "src",
          `${baseurl}/public/image/seatselected.svg`
        );
      }
    } else {
      sessionStorage.removeItem("tripid");
      sessionStorage.setItem("tripid", subtripid);
      let newSeats = "";
      $("#seatnumber" + subtripid).val(newSeats);
      $("#seatnumber" + subtripid).val(numberseat);

      // highlight selected
      $(seatElem).addClass("seat-checked");
      $("img", seatElem).attr(
        "src",
        `${baseurl}/public/image/seatselected.svg`
      );
    }
  }

  updateSeatCount(subtripid);
}

function updateSeatCount(subtripid) {
  var $slotElem = $("#seat_" + subtripid),
    $childInputElem = $("#child_seat" + subtripid),
    $specialInputElem = $("#special_seat" + subtripid),
    $adultInputElem = $("#adult_seat" + subtripid),
    $selectedSeats = $slotElem.find(".seat-checked"),
    totalSelectedSeats,
    totalChild,
    totalSpecial,
    totalAdult,
    seatDiff,
    actualAdultValue;

  totalSelectedSeats = parseInt($selectedSeats.length) || 0;
  totalChild = parseInt($childInputElem.val()) || 0;
  totalSpecial = parseInt($specialInputElem.val()) || 0;
  totalAdult = parseInt($adultInputElem.val()) || 0;

  seatDiff = totalSelectedSeats - (totalChild + totalSpecial + totalAdult);
  actualAdultValue = totalAdult + seatDiff;

  $adultInputElem
    .val(actualAdultValue > -1 ? actualAdultValue : 0)
    .trigger("change");
}

function dynamichtml(result, id) {
  // console.log(result);
  var seat = null;
  var subtrip = null;
  var picdrop = null;
  var taxval = null;
  var childseat = null;
  var childprice = null;
  var adultprice = null;
  var specialseat = null;
  var spacialprice = null;
  var luggagesetting = null;

  var paid_max_luggage_pcs = null;
  var price_pcs = null;
  var special_max_luggage_pcs = null;
  var special_price_pcs = null;
  var free_luggage_kg = null;
  var max_length = null;
  var max_weight = null;
  var special_luggage = "";
  var taxarray = [];

  subtrip = JSON.parse(result.subtrips);
  picdrop = JSON.parse(result.pickdrop);
  taxval = JSON.parse(result.tax);
  luggagesetting = JSON.parse(result.luggagesetting);
  websetting = JSON.parse(result.websetting);

  // console.log();
  $.each(subtrip, function (index, value) {
    specialseat = value.special_seat || 0;
    spacialprice = value.special_fair;

    childseat = value.child_seat || 0;
    childprice = value.child_fair;

    adultprice = value.adult_fair;

    if (websetting.luggage_service == 1) {
      // console.log(luggagesetting);
      if (value.paid_max_luggage_pcs == null) {
        value.paid_max_luggage_pcs = 0;
      }

      if (value.price_pcs == null) {
        value.price_pcs = 0.0;
      }
      if (value.special_max_luggage_pcs == null) {
        value.special_max_luggage_pcs = 0;
      }
      if (value.special_price_pcs == null) {
        value.special_price_pcs = 0.0;
      }
      if (value.free_luggage_kg == null) {
        value.free_luggage_kg = 0.0;
      }
      if (value.max_length == null) {
        value.max_length = 0.0;
      }
      if (value.max_weight == null) {
        value.max_weight = 0.0;
      }

      paid_max_luggage_pcs =
        parseInt(value.paid_max_luggage_pcs) != 0
          ? value.paid_max_luggage_pcs
          : luggagesetting.paid_max_luggage_pcs;
      price_pcs =
        parseFloat(value.price_pcs) != 0.0
          ? value.price_pcs
          : luggagesetting.price_pcs;

      special_max_luggage_pcs =
        parseInt(value.special_max_luggage_pcs) != 0
          ? value.special_max_luggage_pcs
          : luggagesetting.special_max_luggage_pcs;
      special_price_pcs =
        parseFloat(value.special_price_pcs) != 0.0
          ? value.special_price_pcs
          : luggagesetting.special_price_pcs;
      free_luggage_kg =
        parseFloat(value.free_luggage_kg) != 0.0
          ? value.free_luggage_kg
          : luggagesetting.free_luggage_kg;
      max_length =
        parseFloat(value.max_length) != 0.0
          ? value.max_length
          : luggagesetting.max_length;
      max_weight =
        parseFloat(value.max_weight) != 0.0
          ? value.max_weight
          : luggagesetting.max_weight;
    }
  });

  $.each(picdrop, function (index, pvalue) {
    // console.log(pvalue);
  });

  $.each(taxval, function (index, taxvalue) {
    taxarray.push(taxvalue.value);
  });

  var html = '<div class="row">',
    $requiredSign = $("<abbr/>");

  $requiredSign.attr("title", "Required field").text("*");

  // Seat count
  var $childSeatCount = $("<div/>"),
    $childSeatCountLbl = $("<label/>"),
    $childSeatCountInp = $("<input/>");

  $childSeatCountLbl
    .attr("for", `child_seat${id}`)
    .addClass("form-label text-capitalize")
    .text(bdtaskIlmCommonJs.lang.getPhrase("booking_page_children_seat"));

  $childSeatCountInp.attr({
    type: "number",
    id: `child_seat${id}`,
    class: "form-control",
    name: `child_seat`,
    value: 0,
    min: 0,
    max: childseat,
    tabindex: 1,
    onchange: `childseat(this, ${id})`,
  });

  $childSeatCount
    .addClass("col-lg-4")
    .append([$childSeatCountLbl, $childSeatCountInp]);

  // $childSeatCountInp.bind('change', () => { console.log(this); /* childseat_func(this, id) */ });

  var $specialSeatCount = $childSeatCount.clone();
  $("label", $specialSeatCount)
    .attr("for", `special_seat${id}`)
    .text(bdtaskIlmCommonJs.lang.getPhrase("booking_page_special_seat"));

  $("input", $specialSeatCount).attr({
    type: "number",
    id: `special_seat${id}`,
    name: `special_seat`,
    max: specialseat,
    onchange: `specialseat(this, ${id})`,
  });

  var $adultSeatCount = $childSeatCount.clone();
  $("label", $adultSeatCount)
    .attr("for", `adult_seat${id}`)
    .text(bdtaskIlmCommonJs.lang.getPhrase("booking_page_adul_seat"));

  $("input", $adultSeatCount).attr({
    type: "number",
    id: `adult_seat${id}`,
    name: `adult_seat`,
    max: null,
    onchange: `adultseat(this, ${id})`,
  });

  if (websetting.luggage_service == 1) {
    var $freeLuggagePcs = $("<div/>"),
      $paidMaxLuggagePcs = $freeLuggagePcs.clone(),
      $paidMaxLuggagePcsLbl = $("<label/>"),
      $paidMaxLuggagePcsInp = $("<input/>"),
      $specialMaxLuggagePcs = $freeLuggagePcs.clone(),
      $specialMaxLuggagePcsLbl = $("<label/>"),
      $specialMaxLuggagePcsInp = $("<input/>"),
      $specialLuggage = $freeLuggagePcs.clone(),
      $specialLuggageLbl = $("<label/>"),
      $specialLuggageInp = $("<input/>");

    $paidMaxLuggagePcsLbl
      .attr("for", `paid_max_luggage_pcs${id}`)
      .addClass("form-label text-capitalize")
      .text(
        `Paid Luggage (max ${paid_max_luggage_pcs} Pcs ) | Price Per Pcs (${price_pcs} )`
      );

    $paidMaxLuggagePcsInp.attr({
      type: "number",
      id: `paid_max_luggage_pcs${id}`,
      class: "form-control",
      name: `paid_max_luggage_pcs`,
      value: "0",
      min: 0,
      max: paid_max_luggage_pcs,
      tabindex: 1,
      // onkeyup: `paidMaxLagguagePcs(this, ${id})`,
      onchange: `paidMaxLagguagePcs(this, ${id},${price_pcs})`,
    });

    $paidMaxLuggagePcs
      .addClass("col-12")
      .append([$paidMaxLuggagePcsLbl, $paidMaxLuggagePcsInp]);

    $specialMaxLuggagePcsLbl
      .attr("for", `special_max_luggage_pcs${id}`)
      .addClass("form-label text-capitalize")
      .text(
        `Special Luggage (max ${special_max_luggage_pcs} Pcs ) | Price Per Pcs (${special_price_pcs} )`
      );

    $specialMaxLuggagePcsInp.attr({
      type: "number",
      id: `special_max_luggage_pcs${id}`,
      class: "form-control",
      name: `special_max_luggage_pcs`,
      value: "0",
      min: 0,
      max: special_max_luggage_pcs,
      tabindex: 1,
      onchange: `specialMaxLagguagePcs(this, ${id},${special_price_pcs})`,
    });

    $specialMaxLuggagePcs
      .addClass("col-12")
      .append([$specialMaxLuggagePcsLbl, $specialMaxLuggagePcsInp]);

    $specialLuggageLbl
      .attr("for", `special_luggage${id}`)
      .addClass("form-label text-capitalize")
      .text("Special Luggage Info");

    $specialLuggageInp.attr({
      type: "text",
      id: `special_luggage${id}`,
      class: "form-control",
      name: `special_luggage`,
      tabindex: 1,
    });

    $specialLuggage
      .addClass("col-12 mt-3")
      .append([$specialLuggageLbl, $specialLuggageInp]);

    // Add hidden field with value of price_pcs
    var $hiddenPricePcsField = $("<input/>").attr({
      type: "hidden",
      id: `price_pcs${id}`,
      name: "price_pcs",
      value: price_pcs,
    });

    var $hiddenSpecialPricePcsField = $("<input/>").attr({
      type: "hidden",
      id: `special_price_pcs${id}`,
      name: "special_price_pcs",
      value: special_price_pcs,
    });
  }

  html +=
    $childSeatCount.prop("outerHTML") +
    $specialSeatCount.prop("outerHTML") +
    $adultSeatCount.prop("outerHTML");

  if (websetting.luggage_service == 1) {
    html +=
      `<span class="h6 text-danger mt-2">***One hand luggage of <span class="fw-bold">${free_luggage_kg} kg </span>is free.</span> ` +
      $paidMaxLuggagePcs.prop("outerHTML") +
      `<span class="h6 text-danger mt-2">***Non-Standard Luggage(Max Length  <span class="fw-bold"> ${max_length} meters </span> and Max Weight <span class="fw-bold"> ${max_weight} kg </span>)</span> `+
      $specialMaxLuggagePcs.prop("outerHTML") +
      $specialLuggage.prop("outerHTML");
    html += $hiddenPricePcsField.prop("outerHTML")
    html += $hiddenSpecialPricePcsField.prop("outerHTML");
  }

  // Pick location and drop location
  var $pick = $("<div/>"),
    $pickLbl = $("<label/>"),
    $pickSelect = $("<select/>"),
    $selectOption = $("<option/>"),
    $drop = $pick.clone(),
    $dropLbl,
    $dropSelect;

  $pickLbl
    .attr("for", `pickupstand${id}`)
    .addClass("form-label text-capitalize")
    .text(bdtaskIlmCommonJs.lang.getPhrase("selected_boarding_title"))
    .append($requiredSign.clone());

  $dropLbl = $pickLbl
    .clone()
    .attr("for", `dropstand${id}`)
    .text(bdtaskIlmCommonJs.lang.getPhrase("selected_dropping_title"))
    .append($requiredSign.clone());

  $pickSelect
    .attr({
      name: `pickupstand${id}`,
      id: `pickupstand${id}`,
      tabindex: 1,
    })
    .addClass("form-select testselect1")
    .prop("required", true);

  $dropSelect = $pickSelect.clone().attr({
    name: `dropstand${id}`,
    id: `dropstand${id}`,
    tabindex: 1,
  });

  $.each(picdrop, function (i, v) {
    var $pickSelectOptionSingle = $selectOption
      .clone()
      .attr("value", v.pickdropid)
      .text(v.name);
    v.type == 1
      ? $pickSelect.append($pickSelectOptionSingle)
      : $dropSelect.append($pickSelectOptionSingle);
  });

  $pick.addClass("col-lg-6 my-3").append([$pickLbl, $pickSelect]);
  $drop.addClass("col-lg-6 my-3").append([$dropLbl, $dropSelect]);

  html += $pick.prop("outerHTML") + $drop.prop("outerHTML");

  html += '<div class="col-4 mt-2">';
  html +=
    '   <h6 class="form-label fw-bold">' +
    bdtaskIlmCommonJs.lang.getPhrase("type") +
    "</h6>";
  html += "</div>";

  html += '<div class="col-4 mt-2">';
  html +=
    '   <h6 class="form-label fw-bold">' +
    bdtaskIlmCommonJs.lang.getPhrase("price") +
    "</h6>";
  html += "</div>";

  html += '<div class="col-4 mt-2 d-flex justify-content-end justify-content-lg-start">';
  html +=
    '   <h6 class="form-label fw-bold">' +
    bdtaskIlmCommonJs.lang.getPhrase("total") +
    "</h6>";
  html += "</div>";

  html += '<div class="col-4 mt-1">';
  html +=
    '   <h6 class="form-label">' +
    bdtaskIlmCommonJs.lang.getPhrase("adult") +
    "</h6>";
  html += "</div>";

  html += '<div class="col-4 mt-1">';
  html +=
    '   <h6 class="form-label" id="adult_price' +
    id +
    '">' +
    adultprice +
    "</h6>";
  html += "</div>";

  html += '<div class="col-4 mt-1 d-flex justify-content-end justify-content-lg-start">';
  html += '   <h6 class="form-label" id="adult_price_show' + id + '">0</h6>';
  html += "</div>";

  html += '<div class="col-4 mt-1">';
  html +=
    '   <h6 class="form-label">' +
    bdtaskIlmCommonJs.lang.getPhrase("child") +
    "</h6>";
  html += "</div>";

  if (childprice == "") {
    html += '<div class="col-4 mt-1">';
    html +=
      '   <h6 class="form-label" id="child_price' +
      id +
      '">' +
      adultprice +
      "</h6>";
    html += "</div>";
  } else {
    html += '<div class="col-4 mt-1">';
    html +=
      '   <h6 class="form-label" id="child_price' +
      id +
      '">' +
      childprice +
      "</h6>";
    html += "</div>";
  }

  html += '<div class="col-4 mt-1 d-flex justify-content-end justify-content-lg-start">';
  html += '   <h6 class="form-label" id="child_price_show' + id + '">0</h6>';
  html += "</div>";

  html += '<div class="col-4 mt-1">';
  html +=
    '   <h6 class="form-label">' +
    bdtaskIlmCommonJs.lang.getPhrase("special") +
    "</h6>";
  html += "</div>";

  if (spacialprice == "") {
    html += '<div class="col-4 mt-1">';
    html +=
      '   <h6 class="form-label" id="special_price' +
      id +
      '">' +
      adultprice +
      "</h6>";
    html += "</div>";
  } else {
    html += '<div class="col-4 mt-1">';
    html +=
      '   <h6 class="form-label" id="special_price' +
      id +
      '">' +
      spacialprice +
      "</h6>";
    html += "</div>";
  }

  html += '<div class="col-4 mt-1 d-flex justify-content-end justify-content-lg-start">';
  html += '   <h6 class="form-label" id="special_price_show' + id + '">0</h6>';
  html += "</div>";

  html += "<hr>";

  if (websetting.luggage_service == 1) {
    html += '<div class="col-6 col-lg-4">';
    html += `   <h6 class="form-label"> ${bdtaskIlmCommonJs.lang.getPhrase(
      "paid"
    )} ${bdtaskIlmCommonJs.lang.getPhrase(
      "luggage"
    )} ( <span class="form-label" id="totalpaidluggagepcscount${id}"> 0 </span> pcs )</h6>`;
    html += "</div>";

    html += '<div class="col-6 col-lg-4 d-none d-lg-block">';
    html += '   <h6 class="form-label" ></h6>';
    html += "</div>";

    html += '<div class="col-6 col-lg-4 d-flex justify-content-end justify-content-lg-start">';
    html +=
      '   <h6 class="form-label" id="totalpaidluggagepcs' + id + '">0</h6>';
    html += "</div>";

    html += '<div class="col-6 col-lg-4">';
    html += `   <h6 class="form-label"> ${bdtaskIlmCommonJs.lang.getPhrase(
      "special"
    )} ${bdtaskIlmCommonJs.lang.getPhrase(
      "luggage"
    )} ( <span class="form-label" id="totalspecialluggagepcscount${id}"> 0 </span> pcs )</h6>`;
    html += "</div>";

    html += '<div class="col-6 col-lg-4 d-none d-lg-block">';
    html += '   <h6 class="form-label" ></h6>';
    html += "</div>";

    html += '<div class="col-6 col-lg-4 d-flex justify-content-end justify-content-lg-start">';
    html +=
      '   <h6 class="form-label" id="totalspecialluggagepcs' + id + '">0</h6>';
    html += "</div>";
  }

  html += '<div class="col-6 col-lg-4">';
  html += `   <h6 class="form-label">${bdtaskIlmCommonJs.lang.getPhrase(
    "ticket"
  )} ${bdtaskIlmCommonJs.lang.getPhrase("price")}</h6>`;
  html += "</div>";

  html += '<div class="col-6 col-lg-4 d-none d-lg-block">';
  html += '   <h6 class="form-label" ></h6>';
  html += "</div>";

  html += '<div class="col-6 col-lg-4 d-flex justify-content-end justify-content-lg-start">';
  html += '   <h6 class="form-label" id="totalprice' + id + '">0</h6>';
  html += "</div>";

  html += "<hr>";

  html += '<div class="col-6 col-lg-4">';
  html += `   <h6 class="form-label">${bdtaskIlmCommonJs.lang.getPhrase(
    "sub"
  )} ${bdtaskIlmCommonJs.lang.getPhrase("total")}</h6>`;
  html += "</div>";

  html += '<div class="col-6 col-lg-4 d-none d-lg-block">';
  html += '   <h6 class="form-label" ></h6>';
  html += "</div>";

  html += '<div class="col-6 col-lg-4 d-flex justify-content-end justify-content-lg-start">';
  html += '   <h6 class="form-label" id="subtotal' + id + '">0</h6>';
  html += "</div>";

  html += '<div class="col-6 col-lg-4">';
  html += `   <h6 class="form-label">${bdtaskIlmCommonJs.lang.getPhrase(
    "tax"
  )}</h6>`;
  html += "</div>";

  html += '<div class="col-6 col-lg-4 d-none d-lg-block">';
  html += '   <h6 class="form-label" ></h6>';
  html += "</div>";

  html += '<div class="col-6 col-lg-4 d-flex justify-content-end justify-content-lg-start">';
  html += '   <h6 class="form-label" id="totaltax' + id + '">0</h6>';
  html += "</div>";

  html += '<div class="col-6 col-lg-4">';
  html += `   <h6 class="form-label">${bdtaskIlmCommonJs.lang.getPhrase(
    "grand"
  )} ${bdtaskIlmCommonJs.lang.getPhrase("total")}</h6>`;
  html += "</div>";

  html += '<div class="col-6 col-lg-4 d-none d-lg-block">';
  html += '   <h6 class="form-label"></h6>';
  html += "</div>";

  html += '<div class="col-6 col-lg-4 d-flex justify-content-end justify-content-lg-start">';
  html += '<h6  class="form-label" id="grandtotal' + id + '">0</h6>';
  html += "</div>";

  html +=
    '<input type="hidden" id="tax' +
    id +
    '" name="tax' +
    id +
    '[]" value ="' +
    taxarray +
    '">';
  html += "</div>";

  return html;
}

function childseat(cseat, subtripid) {
  var $field = $(cseat),
    maxChild = $field.prop("max"),
    filteredValue = parseInt($field.val()) || 0;

  if (filteredValue > maxChild) {
    alert(`Maximum child seat selection is: ${maxChild}`);
    filteredValue = maxChild;
  }

  $field.val(filteredValue);

  var price = $("#child_price" + subtripid).text();
  var totalprice = filteredValue * parseInt(price);
  $("#child_price_show" + subtripid).text(totalprice);

  toprice(subtripid);
  updateSeatCount(subtripid);
}

function adultseat(cseat, subtripid) {
  var $field = $(cseat),
    filteredValue = parseInt($field.val()) || 0;

  $field.val(filteredValue);

  var id = cseat.getAttribute("id");
  var numberseat = $("#" + id).val();
  var price = $("#adult_price" + subtripid).text();
  var totalprice = parseInt(numberseat) * parseInt(price);
  $("#adult_price_show" + subtripid).text(totalprice);
  toprice(subtripid);
}

function freeLagguageKG(cseat, subtripid) {
  var $field = $(cseat),
    maxfreeLagguageKg = parseFloat($field.prop("max")),
    originalValue = parseFloat($field.val()) || 0.0;

  if (originalValue > maxfreeLagguageKg) {
    alert(`Maximum free luggage (in kg) : ${maxfreeLagguageKg.toFixed(2)}`);
    originalValue = maxfreeLagguageKg;
  }

  // Display the formatted value with two decimal places
  $field.val(originalValue.toFixed(2));

  // Display the total free luggage with two decimal places
  $("#totalfreeluggagekg" + subtripid).text(originalValue.toFixed(2));
}

function paidMaxLagguagePcs(cseat, subtripid, price_pcs) {
  var $field = $(cseat),
    maxpaidMaxLagguagePcs = $field.prop("max"),
    filteredValue = parseInt($field.val()) || 0;

  if (filteredValue > maxpaidMaxLagguagePcs) {
    alert(`Maximum paid luggage (in pcs) : ${maxpaidMaxLagguagePcs}`);
    filteredValue = maxpaidMaxLagguagePcs;
  }
  $field.val(filteredValue);
  $("#totalpaidluggagepcscount" + subtripid).text(filteredValue);
  var totalprice = filteredValue * price_pcs;
  $("#totalpaidluggagepcs" + subtripid).text(parseFloat(totalprice).toFixed(2));
  toprice(subtripid);
}

function specialMaxLagguagePcs(cseat, subtripid, price_pcs) {
  var $field = $(cseat),
    maxspecialMaxLagguagePcs = $field.prop("max"),
    filteredValue = parseInt($field.val()) || 0;

  if (filteredValue > maxspecialMaxLagguagePcs) {
    alert(`Maximum paid luggage (in pcs) : ${maxspecialMaxLagguagePcs}`);
    filteredValue = maxspecialMaxLagguagePcs;
  }
  $field.val(filteredValue);
  $("#totalspecialluggagepcscount" + subtripid).text(filteredValue);
  var totalprice = filteredValue * price_pcs;
  $("#totalspecialluggagepcs" + subtripid).text(
    parseFloat(totalprice).toFixed(2)
  );
  toprice(subtripid);
}

function specialseat(cseat, subtripid) {
  var $field = $(cseat),
    maxSpecial = $field.prop("max"),
    filteredValue = parseInt($field.val()) || 0;

  if (filteredValue > maxSpecial) {
    alert(`Maximum special seat selection is: ${maxSpecial}`);
    filteredValue = maxSpecial;
  }

  $field.val(filteredValue);

  var id = cseat.getAttribute("id");
  var numberseat = $("#" + id).val();
  var price = $("#special_price" + subtripid).text();
  var totalprice = parseInt(numberseat) * parseInt(price);
  $("#special_price_show" + subtripid).text(totalprice);

  toprice(subtripid);
  updateSeatCount(subtripid);
}

function toprice(subtripid) {
  var cprice = $("#special_price_show" + subtripid).text();
  var sprice = $("#child_price_show" + subtripid).text();
  var aprice = $("#adult_price_show" + subtripid).text();
  var totalLuggagePricePcs = $("#totalpaidluggagepcs" + subtripid).text();
  var totalSpecialLuggagePricePcs = $(
    "#totalspecialluggagepcs" + subtripid
  ).text();
  var totalprice = parseInt(cprice) + parseInt(sprice) + parseInt(aprice);
  var subbtotal = 0;
  if (websetting.luggage_service == 1) {
    subbtotal =
      totalprice +
      parseFloat(totalLuggagePricePcs) +
      parseFloat(totalSpecialLuggagePricePcs);
  } else {
    subbtotal = totalprice;
  }
  // console.log(parseInt(cprice),parseInt(sprice),parseInt(aprice),parseFloat(totalLuggagePricePcs),parseFloat(totalLuggagePriceKg));
  $("#totalprice" + subtripid).text(totalprice);

  $("#subtotal" + subtripid).text(parseFloat(subbtotal).toFixed(2));
  taxcaltulation(subtripid, subbtotal);
}

function taxcaltulation(subtripid, totalvalue) {
  var taxvalues = $("#tax" + subtripid).val();
  var totaltaxval = [];
  var newtaxarray = taxvalues.split(",");

  newtaxarray.forEach((value) => {
    var caltax = (value * totalvalue) / 100;
    totaltaxval.push(caltax);
  });

  var total = 0;
  totaltaxval.forEach((value) => {
    // console.log(value);
    total += parseFloat(value);
  });
  var taxtype = $("#tax_type").val();
  var grandtotal;

  if (taxtype == "inclusive") {
    grandtotal = parseFloat(totalvalue) - parseFloat(total);
    grandtotal = totalvalue;
  } else {
    grandtotal = parseFloat(total) + parseFloat(totalvalue);
  }

  $("#totaltax" + subtripid).text(parseFloat(total).toFixed(2));
  $("#grandtotal" + subtripid).text(parseFloat(grandtotal).toFixed(2));
}

function formsubmit(index, subtripid) {
  var allseatnumber = $("#seatnumber" + subtripid).val(),
    childseat = $("#child_seat" + subtripid).val(),
    specialseat = $("#special_seat" + subtripid).val(),
    adultseat = $("#adult_seat" + subtripid).val();

  var totalprice = $("#totalprice" + subtripid).text(),
    tax = $("#totaltax" + subtripid).text(),
    grandtotal = $("#grandtotal" + subtripid).text(),
    pickstand = $("#pickupstand" + subtripid).val(),
    dropstand = $("#dropstand" + subtripid).val(),
    journeydate = $("#journeydate").val();

  if (websetting.luggage_service == 1) {
    var paid_max_luggage_pcs = parseInt(
        $("#paid_max_luggage_pcs" + subtripid).val()
      ),
      special_max_luggage_pcs = parseInt(
        $("#special_max_luggage_pcs" + subtripid).val()
      ),
      price_pcs = parseFloat($("#price_pcs" + subtripid).val()).toFixed(2),
      special_price_pcs = parseFloat(
        $("#special_price_pcs" + subtripid).val()
      ).toFixed(2),
      special_luggage = $("#special_luggage" + subtripid).val();
  }

  totalprice = parseFloat(totalprice);
  tax = parseFloat(tax);
  grandtotal = parseFloat(grandtotal);
  childseat = parseInt(childseat);
  specialseat = parseInt(specialseat);
  adultseat = parseInt(adultseat);

  var totalpassanger = parseInt(childseat + adultseat + specialseat);

  if (allseatnumber) {
    $("#seatnumbers").val(allseatnumber);
  } else {
    alert("no seat selected");
    return false;
  }

  if (totalpassanger <= 0) {
    alert("please enter passagenr");
    return false;
  } else {
    $("#aseat").val(adultseat);
    $("#spseat").val(specialseat);
    $("#cseat").val(childseat);
  }

  if (pickstand) {
    $("#pickstand").val(pickstand);
  } else {
    setTimeout(() => $("#pickupstand" + subtripid).focus(), 400);
    alert("Please select pick stand");
    return false;
  }

  if (dropstand) {
    $("#dropstand").val(dropstand);
  } else {
    alert("Please select drop stand");
    setTimeout(() => $("#dropstand" + subtripid).focus(), 400);
    return false;
  }

  var totalseatarray = allseatnumber.split(","),
    seatlength = totalseatarray.length;

  if (totalpassanger != seatlength) {
    alert("Selected seat and passenger number are not matching");
    return false;
  }
  var singeltriptotalseat = parseInt($("#totalseat").val());

  if (isNaN(singeltriptotalseat)) {
    singeltriptotalseat = 0;
  }

  if (singeltriptotalseat > 0) {
    if (totalpassanger != singeltriptotalseat) {
      alert(
        "Your single trip and round trip passenger number are not matching. Your single trip Passanger " +
          singeltriptotalseat +
          " Your Round trip Passanger " +
          totalpassanger +
          ""
      );
      return false;
    }
  }

  $("#subtripId").val(subtripid);
  $("#totalprice").val(totalprice);
  $("#tax").val(tax);
  $("#grandtotal").val(grandtotal);

  if (websetting.luggage_service == 1) {
    $("#paid_max_luggage_pcs").val(paid_max_luggage_pcs);
    $("#price_pcs").val(price_pcs);
    $("#special_max_luggage_pcs").val(special_max_luggage_pcs);
    $("#special_price_pcs").val(special_price_pcs);
    $("#special_luggage").val(special_luggage);
  } else {
    $("#paid_max_luggage_pcs").val(0);
    $("#price_pcs").val(0);
  }

  // check selected seats
  var base_url = $("#baseurl").val(),
    random_token =
      localStorage.getItem("_chkseats_token") || get_random_token(16),
    seats_check_api_endpoint = `${base_url}/modules/api/v1/tickets/checkseats`;

  $.ajax({
    type: "POST",
    url: seats_check_api_endpoint,
    dataType: "json",
    data: {
      subtrip_id: subtripid,
      ticket_token: random_token,
      seat_names: allseatnumber,
      journey_date: journeydate,
      paid_max_luggage_pcs: paid_max_luggage_pcs,
      price_pcs: price_pcs,
      special_max_luggage_pcs: special_max_luggage_pcs,
      special_price_pcs: special_price_pcs,
      special_luggage: special_luggage,
    },
    success: function (response) {
      if (response.status == "success") {
        localStorage.setItem("_chkseats_token", random_token);
        $("#booking").submit();
        return true;
      }

      alert(response.message);
    },
  });
}

function get_random_token(length) {
  let result = "";
  const characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  const charactersLength = characters.length;
  let counter = 0;
  while (counter < length) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
    counter += 1;
  }
  return "backend" + result;
}
