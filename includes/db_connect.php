<?php
if (!defined("SECURE_ACCESS")) {
    die("Access denied.");
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default is usually no password
$dbname = "ecommerce_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Stop the script and display an error message if the connection fails
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set the character set to UTF-8 for proper handling of all characters
$conn->set_charset("utf8mb4");

// The $conn variable now holds the active database connection.
// This file will be included in every PHP file that needs to talk to the database.
?>