<?php
session_start();

// Include database connection
include('../../db/config.php');

header('Content-Type: application/json');  // Set the content type to JSON

$response = [];  // Initialize response array

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $prayer_request = mysqli_real_escape_string($conn, $_POST['prayer_request']);
        
        // Insert the prayer request into the database
        $query = "INSERT INTO prayer_requests (user_id, prayer_request, submitted_at) VALUES ('$user_id', '$prayer_request', NOW())";
        
        if (mysqli_query($conn, $query)) {
            // Success: Prayer request submitted
            $response['status'] = 'success';
            $response['message'] = 'Prayer request submitted successfully!';
        } else {
            // Error: Failed to insert prayer request
            $response['status'] = 'error';
            $response['message'] = 'Error: ' . mysqli_error($conn);
        }
    } else {
        // Error: User not logged in
        $response['status'] = 'error';
        $response['message'] = 'Please log in to submit a prayer request.';
    }
}

mysqli_close($conn);

// Return the JSON response
echo json_encode($response);
?>
