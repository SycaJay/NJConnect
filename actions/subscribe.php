<?php
include('../db/config.php');

// Check if form is submitted via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the form
    $email = $_POST['email'];

    // Validate the email (basic validation)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
    } else {
        // Prevent SQL injection by escaping the email input
        $email = $conn->real_escape_string($email);

        // Prepare the SQL query
        $sql = "INSERT INTO subscriptions (email) VALUES ('$email')";

        // Execute the query
        if ($conn->query($sql) === TRUE) {
            echo "You have successfully subscribed!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>
