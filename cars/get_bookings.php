<?php

include('db.php');

$cars = json_decode(file_get_contents("cars.json"), true);

if (array_key_exists("latest", $_GET)) {
  $latest = $_GET['latest'];
  $email = $_GET['email'];

  $sql = "SELECT * FROM renting_history AS rh WHERE email = ? ORDER BY rh.rent_date DESC LIMIT 1";

  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();

  $result = $stmt->get_result();
  $booking = $result->fetch_assoc();

  echo json_encode([
    "data" => $booking,
  ]);
} else {
  $sql = "SELECT * FROM renting_history AS rh ORDER BY rh.rent_date DESC";

  $result = $mysqli->query($sql);
  $bookings = $result->fetch_all(MYSQLI_ASSOC);

  echo json_encode([
    "data" => $bookings,
  ]);
}
