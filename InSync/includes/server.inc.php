<?php
// $servername = "localhost";
// $username = "phpmyadmin";
// $password = "Monkey";
// $dbname = "WSDProject";



$servername = "localhost";
$username = "root";
$password = "";
$dbname = "WSDproject";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  echo "Failed to connect to MySQL: " . $conn->connect_error;
}