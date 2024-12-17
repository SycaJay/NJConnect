<?php
// Include database connection
include('../../db/config.php');

// Fetch subscriptions from the database
$query = "SELECT * FROM subscriptions";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscriptions</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/viewsubs.css">
</head>
<body>

<div class="container">
    <h1>Manage Subscriptions</h1>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Subscriptions Table -->
    <table>
        <thead>
        <tr>
            <th>Email</th>
            <th>Subscribed At</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($subscription = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($subscription['email']); ?></td>
                <td><?php echo htmlspecialchars($subscription['created_at']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
