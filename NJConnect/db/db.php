<?php
$host = 'localhost';
$dbname = 'nj';
$username = 'root';
$password = '';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Success message
    // echo "Database connection successful!";
} catch (Exception $e) {
    error_log($e->getMessage()); 
    die("Database connection failed. Please try again later.");
}
?>
