<?php
// update_status.php
session_start();
include('../../db/config.php');

// Check if the form was submitted
if (isset($_POST['purchase_id']) && isset($_POST['status'])) {
    $purchase_id = $_POST['purchase_id'];
    $status = $_POST['status'];

    // Prepare and execute the update query
    $query = "UPDATE purchases SET status = ? WHERE purchase_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $purchase_id); // 's' for string, 'i' for integer
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
        // Redirect back to the purchase page after success
        header('Location: admin_purchases.php');
        exit();
    } else {
        // Handle errors if the update fails
        echo "Failed to update the status.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
