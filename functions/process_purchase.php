<?php
include ('../db/config.php');

// Get data from the AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $bookId = isset($_POST['book_id']) ? $_POST['book_id'] : null;
    $purchaseDate = isset($_POST['purchase_date']) ? $_POST['purchase_date'] : null;
    $transactionReference = isset($_POST['transaction_reference']) ? $_POST['transaction_reference'] : null;

    if ($userId && $bookId && $purchaseDate && $transactionReference) {
        // Insert the purchase record into the database
        $query = "INSERT INTO purchases (user_id, purchase_date, book_id) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters to the SQL query
            $stmt->bind_param("iss", $userId, $purchaseDate, $bookId);
            
            // Execute the statement
            if ($stmt->execute()) {
                echo "Purchase recorded successfully!";
            } else {
                echo "Error recording purchase: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Failed to prepare the SQL statement: " . $conn->error;
        }
    } else {
        echo "Missing required data.";
    }
}

$conn->close();
?>
