<?php
// db_mysqli.php - MySQLi Database Connection

$host = "localhost";
$dbname = "chatbot_db";
$username = "root";
$password = "";

// Create a MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("MySQLi Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
