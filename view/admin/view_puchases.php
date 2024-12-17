<?php
// admin_purchases.php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../../db/config.php');

// Query to fetch purchases data
$query = "SELECT p.purchase_id, p.purchase_date, p.user_id, p.book_id, 
                 p.status,  -- Include status in the query
                 b.title, 
                 CONCAT(u.first_name, ' ', IFNULL(u.middle_name, ''), ' ', u.last_name) AS full_name 
          FROM purchases p 
          JOIN books b ON p.book_id = b.book_id
          JOIN users u ON p.user_id = u.user_id
          ORDER BY p.purchase_date DESC";

// Prepare and execute query
$result = $conn->query($query);

// Check if there are any results
if ($result->num_rows > 0) {
    $purchases = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $purchases = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Purchases</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/viewpurchase.css">
</head>
<body>

<div class="container">
    <h1>Admin - Purchase Records</h1>

    <!-- Purchase Table -->
    <table>
        <thead>
            <tr>
                <th>Purchase ID</th>
                <th>User</th>
                <th>Book Title</th>
                <th>Purchase Date</th>
                <th>Status</th> <!-- Add a column for Status -->
                <th>Actions</th> <!-- For editing the status -->
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($purchases) > 0) {
                foreach ($purchases as $purchase) {
                    echo "<tr>
                            <td>{$purchase['purchase_id']}</td>
                            <td>{$purchase['full_name']}</td>
                            <td>{$purchase['title']}</td>
                            <td>{$purchase['purchase_date']}</td>
                            <td>{$purchase['status']}</td>
                            <td>
                                <form action='update_status.php' method='POST'>
                                    <select name='status' onchange='this.form.submit()'>
                                        <option value='pending' ".($purchase['status'] == 'pending' ? 'selected' : '').">Pending</option>
                                        <option value='completed' ".($purchase['status'] == 'completed' ? 'selected' : '').">Completed</option>
                                        <option value='shipped' ".($purchase['status'] == 'shipped' ? 'selected' : '').">Shipped</option>
                                        <!-- Add other status options as needed -->
                                    </select>
                                    <input type='hidden' name='purchase_id' value='{$purchase['purchase_id']}'>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No purchases found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
</div>

</body>
</html>
