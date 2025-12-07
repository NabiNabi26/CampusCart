<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_cart_db";

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// set charset to utf8
$conn->set_charset("utf8");
?>