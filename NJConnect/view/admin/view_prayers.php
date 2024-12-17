<?php
// Include database connection
include('../../db/config.php');

// Fetch prayer requests from the database
$query = "SELECT pr.prayer_request_id, pr.user_id, pr.prayer_request, pr.submitted_at,
                 CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS user_name
          FROM prayer_requests pr
          JOIN users u ON pr.user_id = u.user_id";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prayer Requests</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/viewprayer.css">
</head>
<body>

<div class="container">
    <h1>Manage Prayer Requests</h1>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Prayer Requests Table -->
    <table>
        <thead>
        <tr>
            <th>User Name</th>
            <th>Prayer Request</th>
            <th>Submitted At</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($request = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                <td><?php echo htmlspecialchars($request['prayer_request']); ?></td>
                <td><?php echo htmlspecialchars($request['submitted_at']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
