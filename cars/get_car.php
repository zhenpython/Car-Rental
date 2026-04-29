<?php

function get_car($id)
{
  $cars = json_decode(file_get_contents("cars.json"), true);

  foreach ($cars as $car) {
    if ($car['id'] == $id) {
      return $car;
    }
  }
}

$car = get_car($_POST['id']);

echo json_encode($car);
