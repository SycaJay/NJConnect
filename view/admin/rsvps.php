<?php
// Include database connection
include('../../db/config.php');

// Fetch RSVPs from the database
$query = "SELECT r.rsvp_id, r.user_id, r.event_id, r.rsvp_date, r.seat_number, 
                 CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS user_name,
                 e.title
          FROM rsvps r
          JOIN users u ON r.user_id = u.user_id
          JOIN events e ON r.event_id = e.event_id";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage RSVPs</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/rsvp.css">
</head>
<body>

<div class="container">
    <h1>Manage RSVPs</h1>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- RSVPs Table -->
    <table>
        <thead>
        <tr>
            <th>User Name</th>
            <th>Event Name</th>
            <th>RSVP Date</th>
            <th>Seat Number</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($rsvp = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($rsvp['user_name']); ?></td>
                <td><?php echo htmlspecialchars($rsvp['title']); ?></td>
                <td><?php echo htmlspecialchars($rsvp['rsvp_date']); ?></td>
                <td><?php echo htmlspecialchars($rsvp['seat_number']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
