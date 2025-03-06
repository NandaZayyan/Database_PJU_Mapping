<?php
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");

  include "connection-pdo.php";

  $username = $_GET['username'];
  $password = $_GET['password'];

  $sql = "SELECT * FROM tabel_user WHERE Username_usr = :username ";
  $sql .= "AND Password_usr = :password ";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":username", $username);
  $stmt->bindParam(":password", $password);
  $stmt->execute();
  $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo json_encode($returnValue);
?>