<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/global.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <title>Home | Hertz-UTS</title>
</head>

<body>
  <header style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 16px 20px; background-color: #171717">
    <a style="text-decoration: none;" href="./index.php">
      <h1 style="font-weight: 600; color: #eab308">Hertz-UTS</h1>
    </a>
    <h2 style="font-weight: 500; color: #fafafa">Car Rental Center</h2>
    <a href="./reservation" class="button primary" style="text-decoration: none;">
      Car Reservation
    </a>
  </header>
  <main style="display: flex; flex-direction: column; align-items: center;">
    <div id="alert" style="display: none; position: absolute; padding: 16px 20px; width: 540px; top: 84px; font-weight: 500; background-color: #fecaca; border-radius: 8px; border-width: 1px; border-style: solid;">
    </div>
    <div id="car-list" style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 24px 0;">
    </div>
  </main>
  <script>
    $(document).ready(function() {
      function renderCarCard(car) {
        return (
          `<div style="display: flex; flex-direction: column; align-items: center; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1); border: 1px solid #e5e5e5; border-radius: 12px; margin: 16px;">
            <img style="border-radius: 12px 12px 0 0;" width="270" height="180" src="${car['link']}"> 
            <div style="border: 1px solid #e5e5e5; width: 100%;"></div>
            <h3 style="font-weight: 500; margin: 8px 0;">${car['brand']} ${car['model']} - ${car['year']}</h3>
            <div style="display: flex; flex-direction: column; width: 100%; font-size: 14px; margin: 12px 0 24px 0;">
              <div style="display: flex; align-items: center; justify-content: space-between; padding: 0 24px; margin-bottom: 6px;">
                <p style="font-weight: 600;">Mileage</p>
                <p>${car['mileage']} km</p>
              </div>
              <div style="display: flex; align-items: center; justify-content: space-between; padding: 0 24px; margin-bottom: 6px;">
                <p style="font-weight: 600;">Fuel Type</p>
                <p>${car['fuel_type']}</p>
              </div>
              <div style="display: flex; align-items: center; justify-content: space-between; padding: 0 24px; margin-bottom: 6px;">
                <p style="font-weight: 600;">Seats</p>
                <p>${car['seat']}</p>
              </div>
              <div style="display: flex; align-items: center; justify-content: space-between; padding: 0 24px; margin-bottom: 6px;">
                <p style="font-weight: 600;">Rent price (day)</p>
                <p>$${car['price_per_day']}</p>
              </div>
              <div style="display: flex; align-items: center; justify-content: space-between; padding: 0 24px;">
                <p style="font-weight: 600;">Availability</p>
                <p>${car['availability'] ? 'True' : 'False'}</p>
              </div>
              <form id="form-${car['id']}" style="padding: 0 24px; margin-top: 16px;" method="POST">
                <input name="id" type="hidden" value="${car['id']}">
                <button class="button primary" style="width: 100%;" type="submit">Reservation</button>
              </form>
            </div>
          </div>`
        )
      }

      let reservationData = JSON.parse(sessionStorage.getItem("reservation"));

      $.ajax({
        url: "get_cars.php",
        method: 'GET',
        error: function(xhr, status, error) {
          console.error(error);
        },
        success: function(result, status, xhr) {
          const cars = JSON.parse(result);

          const carCards = cars.map((car) => renderCarCard(car));

          $('#car-list').prepend(carCards);

          cars.forEach((car) => {
            $(`#form-${car['id']}`).on('submit', function(e) {
              e.preventDefault();

              $.ajax({
                url: "get_car.php",
                method: "POST",
                data: $(`#form-${car['id']}`).serialize(),
                error: function(xhr, status, error) {
                  console.error(error);
                },
                success: function(result, status, xhr) {
                  const car = JSON.parse(result);

                  $('#alert').css("display", "block");
                  $('#alert').empty();

                  if (!car['availability']) {
                    $('#alert').css("border-color", "#ef4444");
                    $('#alert').css("color", "#7f1d1d");
                    $('#alert').css("background-color", "#fecaca");
                    $('#alert').prepend(`<p>Sorry, the car is not available now. Please try other cars.</p>`);
                  } else {
                    $('#alert').css("border-color", "#84cc16");
                    $('#alert').css("color", "#365314");
                    $('#alert').css("background-color", "#d9f99d");
                    $('#alert').prepend(`<p>Car ${car['brand']} ${car['model']} - ${car['year']} has been added to reservation list</p>`);

                    if (reservationData === null) {
                      reservationData = {
                        [car['id']]: 1
                      }
                    } else {
                      if (!(car['id'] in reservationData)) {
                        reservationData[car['id']] = 1;
                      }
                    }

                    sessionStorage.setItem("reservation", JSON.stringify(reservationData));
                  }

                  setTimeout(() => {
                    $('#alert').css("display", "none");
                  }, 3000);
                }
              })
            })
          })
        }
      })
    })
  </script>
</body>

</html>