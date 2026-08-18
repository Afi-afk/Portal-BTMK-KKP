<?php
// Database configuration for XAMPP default settings
$host     = "localhost";
$username = "root";     // Default XAMPP MySQL user
$password = "";         // Default XAMPP MySQL password (empty)
$database = "yayasan_sabah_db";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection status
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper character encoding
$conn->set_charset("utf8mb4");
?>
