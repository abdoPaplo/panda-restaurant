<?php
date_default_timezone_set('Africa/Cairo');
// Database connection parameters
$servername = "localhost";
$username = "admin"; // Default username for XAMPP MySQL
$password = "admin123"; // Default password for XAMPP MySQL is empty
$dbname = "orders"; // our database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
