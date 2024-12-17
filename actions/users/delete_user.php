<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Set the error reporting level (e.g., all errors, warnings, notices)
error_reporting(E_ALL);
// Include database connection
include('../../db/config.php');

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    // Get the user ID from the URL
    $user_id = $_GET['id'];

    // Check if the user exists before deletion
    $check_query = "SELECT first_name, last_name, email FROM users WHERE user_id = '$user_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If the user is found, fetch their details for logging or additional processing if needed
        $user_details = mysqli_fetch_assoc($check_result);

        // Delete the user from the database
        $delete_query = "DELETE FROM users WHERE user_id = '$user_id'";
        if (mysqli_query($conn, $delete_query)) {
            // Optional: Log the deletion or perform additional actions
            // For example, you might want to log which admin deleted the user
            
            // Redirect back to the user management page
            header("Location: ../../view/admin/manage_users.php");
            exit();
        } else {
            echo "Error deleting user: " . mysqli_error($conn);
        }
    } else {
        echo "User not found.";
    }
} else {
    echo "Invalid request.";
}
?>