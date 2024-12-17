<?php
// Include database connection
include('../../db/config.php');

// Fetch growth contacts from the database
$query = "SELECT * FROM growth_contacts";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Growth Contacts</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/growthcontacts.css">
</head>
<body>

<div class="container">
    <h1>View Growth Contacts</h1>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Growth Contacts Table -->
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Contact Method</th>
            <th>Submitted At</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($contact = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                <td><?php echo htmlspecialchars($contact['contact_method']); ?></td>
                <td><?php echo htmlspecialchars($contact['submitted_at']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
