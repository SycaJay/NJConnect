<?php
$host = 'localhost';
$dbname = 'webtech_fall2024_jessica_yumu';
$username = 'jessica.yumu';
$password = 'MightyElSyca597';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    //   echo "Database connection successful!";
} catch (Exception $e) {
    error_log($e->getMessage()); 
    die("Database connection failed. Please try again later.");
}
?>
