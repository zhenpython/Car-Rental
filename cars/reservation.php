<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/global.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <title>Reservation | Hertz-UTS</title>
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
    <div style="margin: 48px; width: 100%; max-width: 1280px;">
      <h2 style="font-weight: 500;">Car Reservation</h2>
    </div>
    <style>
      .table {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
      }

      .thead {
        display: flex;
        flex-direction: row;
        align-items: center;
        width: 100%;
      }

      .tbody {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
      }

      .tr {
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
      }

      .th,
      .td {
        width: 100%;
        text-align: center;
        font-weight: 400;
        margin: 16px 0;
      }
    </style>
    <form id="reservation-form" style="max-width: 1280px; width: 100%;" method="POST">
      <div class="table" style="max-width: 1280px;">
        <div class="thead">
          <div class="th">Thumbnail</div>
          <div class="th">Vehicle</div>
          <div class="th">Price per Day</div>
          <div class="th">Rental Days</div>
          <div class="th">Actions</div>
        </div>
        <div id="reservation-list" class="tbody">
        </div>
      </div>
      <div style="width: 100%; max-width: 1280px; display: flex; justify-content: flex-end">
        <button class="button primary" style="font-size: 14px; margin-top: 32px; margin-right: 88px;" type="submit">Proceeding to checkout</button>
      </div>
    </form>
  </main>
  <script>
    let reservationData = {};

    $(window).on('load', function() {
      reservationData = JSON.parse(sessionStorage.getItem("reservation"));

      if (reservationData === null || Object.keys(reservationData).length === 0) {
        $('#reservation-list').empty();
        $('#reservation-list').prepend(`<div style="margin: 48px;"><h3 style="font-weight: 500;">Reservation list is empty!</h3></div>`);
      } else {
        $('#reservation-list').empty();
        Object.keys(reservationData).forEach((id) => {
          $.ajax({
            url: "get_car.php",
            method: "POST",
            data: `id=${id}`,
            error: function(xhr, status, error) {
              console.error(error);
            },
            success: function(result, status, xhr) {
              const car = JSON.parse(result);

              $('#reservation-list').prepend(`
                <div id="reservation-row-${id}" class="tr">
                  <div class="td">
                    <img width="204" src="${car['link']}">
                  </div>
                  <div class="td">
                    <p>${car['brand']} ${car['model']} - ${car['year']}</p>
                  </div>
                  <div class="td">
                    <p>$${car['price_per_day']}</p>
                  </div>
                  <div class="td">
                    <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                      <input id="input-reservation-${id}" style="width: 120px;" name="days-${id}" value="${reservationData[id]}" type="number" min="1" max="30" required>
                    </div>
                  </div>
                  <div class="td">
                    <button id="button-remove-reservation-${id}" class="button primary" style="font-size: 14px;" type="button">Delete</button>
                  </div>
                </div>
              `);

              $(`#button-remove-reservation-${id}`).on('click', function(e) {
                $(`#reservation-row-${id}`).remove();
                delete reservationData[id];
                sessionStorage.setItem("reservation", JSON.stringify(reservationData));
                if (Object.keys(reservationData).length === 0) {
                  sessionStorage.removeItem("reservation");
                }
              })

              $(`#input-reservation-${id}`).on('change', function(e) {
                reservationData[id] = $(`#input-reservation-${id}`).val();
                sessionStorage.setItem("reservation", JSON.stringify(reservationData));
              })
            }
          })
        })
      }
    });

    $(`#reservation-form`).on('submit', function(e) {
      e.preventDefault();
      if (reservationData === null || Object.keys(reservationData).length === 0) {
        $('#alert').css("display", "block");
        $('#alert').empty();
        $('#alert').css("border-color", "#ef4444");
        $('#alert').css("color", "#7f1d1d");
        $('#alert').css("background-color", "#fecaca");
        $('#alert').prepend(`<p>No car has been reserved.</p>`);
      } else {
        window.location.href = "./checkout.php";
      }

      setTimeout(() => {
        $('#alert').css("display", "none");
      }, 3000);
    });
  </script>
</body>

</html>