<?php
$host = 'localhost'; // or '127.0.0.1'
$user = 'root'; // default XAMPP username
$pass = ''; // default XAMPP password is empty
$dbname = 'repository'; // your database name
$port = 3306; // specify the port

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>