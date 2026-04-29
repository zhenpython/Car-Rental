<?php

$mysqli = new mysqli("localhost", "root", "", "car-rental");

if ($mysqli->connect_errno) {
  echo json_encode([
    "status" => 500,
    "message" => "Something wrong happened"
  ]);
  return;
}

$mysqli->autocommit(false);
