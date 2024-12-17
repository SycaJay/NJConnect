<?php
// Include the database configuration file
include('../db/config.php');

// Collect and sanitize form data
$firstName = $conn->real_escape_string($_POST['first_name']);
$lastName = $conn->real_escape_string($_POST['last_name']);
$email = $conn->real_escape_string($_POST['email']);
$password = $conn->real_escape_string($_POST['password']);
$confirmPassword = $conn->real_escape_string($_POST['confirm_password']);
$middleName = isset($_POST['middle_name']) ? $conn->real_escape_string($_POST['middle_name']) : NULL; // Optional middle name

// Validate passwords
if ($password !== $confirmPassword) {
    echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
    exit();
}

// Hash the password for security
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Default role for new users
$defaultRole = 'regular';

// Check if email already exists
$emailCheckQuery = "SELECT * FROM users WHERE email = '$email'";
$emailCheckResult = $conn->query($emailCheckQuery);

if ($emailCheckResult->num_rows > 0) {
    echo "<script>alert('Email already exists!'); window.history.back();</script>";
    exit();
}

// Insert user data into the database
$insertQuery = "INSERT INTO users (first_name, middle_name, last_name, email, password, role) 
                VALUES ('$firstName', '$middleName', '$lastName', '$email', '$hashedPassword', '$defaultRole')";

if ($conn->query($insertQuery) === TRUE) {
    echo "<script>alert('Registration successful! Please log in.'); window.location.href='../view/Login.php';</script>";
} else {
    echo "Error: " . $insertQuery . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>
