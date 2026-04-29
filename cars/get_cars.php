<?php

function get_cars()
{
  $cars = json_decode(file_get_contents("cars.json"), true);
  return $cars;
}

echo json_encode(get_cars());
