<?php

include('db.php');

$cars = json_decode(file_get_contents("cars.json"), true);

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$address = $_POST['address'];
$city = $_POST['city'];
$payment_type = $_POST['payment-type'];
$bond_amount = $_POST['bond-amount'];
$rent_total = $_POST['rent-total'];
$car_ids = $_POST['car-ids'];

$car_ids = explode(",", $car_ids);

foreach ($cars as $i => $car) {
  if (in_array($car['id'], $car_ids)) {
    $cars[$i]['availability'] = false;
  }
}

file_put_contents("cars.json", json_encode($cars));

$sql = "INSERT INTO renting_history (fullname, email, address, city, rent_date, bond_amount, rent_total, payment_type) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ssssdds", $fullname, $email, $address, $city, $bond_amount, $rent_total, $payment_type);
$stmt->execute();

$mysqli->commit();

echo json_encode([
  "status" => 200,
  "success" => true
]);