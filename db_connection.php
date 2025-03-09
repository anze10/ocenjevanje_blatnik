<?php
$servername = "158.180.230.254:3306";
$dbname = "ocvenjevanej_nsa";
$username = "username";
$password = "Kaks123!@";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>