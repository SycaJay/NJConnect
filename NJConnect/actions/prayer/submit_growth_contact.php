<?php
session_start();

// Include database connection
include('../../db/config.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $contact_method = mysqli_real_escape_string($conn, $_POST['contact_method']);
    
    // Insert the growth contact into the database
    $query = "INSERT INTO growth_contacts (name, phone, contact_method, submitted_at) VALUES ('$name', '$phone', '$contact_method', NOW())";
    
    if (mysqli_query($conn, $query)) {
        $response['success'] = true;
        $response['message'] = 'Contact request submitted successfully.';
    } else {
        $response['message'] = 'Error: ' . mysqli_error($conn);
    }
}

mysqli_close($conn);

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
