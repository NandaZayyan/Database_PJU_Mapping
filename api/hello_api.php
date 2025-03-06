<?php
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");

  $name = $_GET['name'];
  $address = $_GET['address'];

  echo "Hello $name, you are from $address!";

?>