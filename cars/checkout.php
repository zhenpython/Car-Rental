<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/global.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <title>Checkout | Hertz-UTS</title>
</head>

<body>
  <header style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 16px 20px; background-color: #171717">
    <a style="text-decoration: none;" href="./index.php">
      <h1 style="font-weight: 600; color: #eab308">Hertz-UTS</h1>
    </a>
    <h2 style="font-weight: 500; color: #fafafa">Car Rental Center</h2>
    <a href="./" class="button primary" style="text-decoration: none;">
      Browse Car
    </a>
  </header>
  <main style="display: flex; flex-direction: column; align-items: center;">
    <div id="alert" style="display: none; position: absolute; padding: 16px 20px; width: 540px; top: 84px; font-weight: 500; background-color: #fecaca; border-radius: 8px; border-width: 1px; border-style: solid;">
    </div>
    <div style="margin: 48px; width: 100%; max-width: 840px;">
      <h2 style="font-weight: 500;">Customer Details and Payment</h2>
      <p style="font-weight: 400; margin: 12px 0;">Please fill in your details. <span style="color: red">*</span> indicates required field</p>
    </div>
    <style>
      .input-group {
        display: flex;
        flex-direction: row;
        align-items: center;
        height: 54px;
        margin-bottom: 16px;
      }

      .input-group label {
        font-size: 16px;
        font-weight: 500;
      }

      .input-group input {
        font-size: 14px;
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #171717;
      }

      .input-group select {
        font-size: 14px;
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #171717;
      }
    </style>
    <form id="checkout-form" style="max-width: 840px; width: 100%;" method="POST">
      <div class="input-group">
        <label style="width: 280px;">Full name<span style="color: red">*</span></label>
        <input class="input" name="fullname" type="text" required>
      </div>
      <div class="input-group">
        <label style="width: 280px;">Email address<span style="color: red">*</span></label>
        <input class="input" name="email" type="email" required>
      </div>
      <div class="input-group">
        <label style="width: 280px;">Street address<span style="color: red">*</span></label>
        <input class="input" name="address" type="text" required>
      </div>
      <div class="input-group">
        <label style="width: 280px;">City<span style="color: red">*</span></label>
        <input class="input" name="city" type="text" required>
      </div>
      <div class="input-group">
        <label style="width: 280px;">Payment type<span style="color: red">*</span></label>
        <select name="payment-type" required>
          <option selected>VISA</option>
          <option>Mastercard</option>
          <option>Paypal</option>
        </select>
      </div>
      <div style="width: 100%; margin-top: 32px;">
        <p style="color: #171717;">You required to pay <span style="font-weight: 500;" id="rent-total"></span></p>
      </div>
      <div style="width: 100%; display: flex; flex-direction: row; justify-content: right; margin-top: 32px;">
        <a href="./index" class="button primary" style="text-decoration: none; font-size: 14px;">Continue Selection</a>
        <button class="button primary" style="margin-left: 16px; font-size: 14px;" type="submit">Booking</button>
      </div>
    </form>
  </main>
  <script>
    function dateDiffInMonths(a, b) {
      const diffYear = b.getFullYear() - a.getFullYear();
      const diffMonth = b.getMonth() - a.getMonth();
      return diffYear * 12 + diffMonth;
    }

    let reservationData = {};

    let rentTotal = 0;

    let bondAmount = 0;

    $(window).on('load', function() {
      reservationData = JSON.parse(sessionStorage.getItem("reservation"));

      Object.keys(reservationData).forEach((id) => {
        $.ajax({
          async: false,
          url: "get_car.php",
          method: "POST",
          data: `id=${id}`,
          error: function(xhr, status, error) {
            console.error(error);
          },
          success: function(result, status, xhr) {
            const car = JSON.parse(result);

            rentTotal += Number(car['price_per_day']) * reservationData[id];
            console.log(rentTotal)
          }
        })
      });

      rentTotal += bondAmount;

      $(`#rent-total`).empty();
      $(`#rent-total`).prepend(`$${rentTotal}`);
    });

    $('#checkout-form').on('submit', function(e) {
      e.preventDefault();

      const carIds = Object.keys(reservationData).join(',');

      console.log(carIds);

      $.ajax({
        async: false,
        data: `email=${$('input[name="email"]').val()}&latest=true`,
        url: "get_bookings.php",
        method: "GET",
        error: function(xhr, status, error) {
          console.error(error);
        },
        success: function(result, status, xhr) {
          const booking = JSON.parse(result)['data'];

          if (booking !== null && dateDiffInMonths(new Date(booking['rent_date']), new Date()) > 3) {
            bondAmount = 200;

            alert(`You required to pay $${rentTotal + bondAmount} (extra $200) because you haven't rented yet for the past 3 months`);

            $(`#rent-total`).empty();
            $(`#rent-total`).prepend(`$${rentTotal + bondAmount}`);

            return;
          } 
          
          if (booking === null) {
            bondAmount = 200;

            alert(`You required to pay $${rentTotal + bondAmount} (extra $200) because you are new customer!`);

            $(`#rent-total`).empty();
            $(`#rent-total`).prepend(`$${rentTotal + bondAmount}`);
          }
        }
      })

      $.ajax({
        async: false,
        url: "create_booking.php",
        data: `${$('#checkout-form').serialize()}&bond-amount=${bondAmount}&rent-total=${rentTotal}&car-ids=${carIds}`,
        method: "POST",
        error: function(xhr, status, error) {
          console.error(error);
        },
        success: function(result, status, xhr) {
          const response = JSON.parse(result);

          sessionStorage.removeItem("reservation");

          if (response['status'] === 200) {
            $('body').css('pointer-events', 'none');

            $('#alert').css("display", "block");
            $('#alert').empty();
            $('#alert').css("border-color", "#84cc16");
            $('#alert').css("color", "#365314");
            $('#alert').css("background-color", "#d9f99d");
            $('#alert').prepend(`<p>Booking has been recorded</p>`);

            setTimeout(() => {
              $('#alert').css("display", "none");
              window.location.href = "./index";
            }, 3000);

          }
        }
      })
    });
  </script>
</body>

</html>