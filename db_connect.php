<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "cabconnect";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
 
    die("❌ Connection failed: " . $conn->connect_error . "<br>Make sure MySQL is running and the database 'cabconnect' exists.");
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Function to generate unique ID
function generateUniqueId($prefix, $table, $column, $conn) {
    do {
        $number = rand(100, 999);
        $id = $prefix . $number;
        $stmt = $conn->prepare("SELECT $column FROM $table WHERE $column = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0);
    return $id;
}

// Function to check if email already exists
function emailExists($email, $conn) {
    $stmt = $conn->prepare("SELECT email FROM driver WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return true;
    }
    return false;
}

