<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Glory Life</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/admindashboard.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            New Jerusalem Admin Dashboard
        </div>
        <ul>
            <li><a href="#">Dashboard Home</a></li>
            <li><a href="../Sermons.php">Sermons</a></li>
            <li><a href="../Events.php">Events</a></li>
            <li><a href="../Departments.php">Ministries/Departments</a></li>
            <li><a href="../Prayer.php">Prayer Requests</a></li>
            <li><a href="../Book.php">Book</a></li>
            <li><a href="../Devotional.php">Devotional</a></li>
            <li><a href="../../actions/NJLogout.php">Logout</a></li>
        </ul>
    </div>


    <!-- Main Content -->
    <div class="main-content">
    <?php
// Include database connection
include('../../db/config.php');

// Fetch admin's first name from the database
$admin_query = "SELECT first_name FROM users WHERE role = 'admin'";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);
$admin_first_name = $admin['first_name'];
?>
        <!-- Header -->
        <div class="header">
        <h1>Welcome, Admin <?php echo htmlspecialchars($admin_first_name); ?></h1>
            <div class="profile">
                <img src="../../assets/images/angel.jpeg" alt="Profile Picture">
                <span><?php echo htmlspecialchars($admin_first_name); ?></span>
            </div>
        </div>

        <?php 
include('../../db/config.php');
include('../../actions/fetch_total.php'); 
include('../../actions/recent_activities.php');
?>
        <!-- Admin Overview Section -->
        <div class="admin-overview">
            <div class="card">
                <h2>Sermons</h2>
                <p><?php echo $sermons_count; ?> uploaded</p>
                <a href="manage_sermons.php" class="btn">Manage Sermons</a>
            </div>
            <div class="card">
                <h2>Events</h2>
                <p><?php echo $events_count; ?> upcoming</p>
                <a href="manage_events.php" class="btn">Manage Events</a>
            </div>
            <div class="card">
                <h2>Users</h2>
                <p><?php echo $users_count; ?> registered</p>
                <a href="manage_users.php" class="btn">Manage Users</a>
            </div>
            <div class="card">
                <h2>Prayer Requests</h2>
                <p><?php echo $prayer_requests_count; ?> requests</p>
                <a href="view_prayers.php" class="btn">View Requests</a>
            </div>
            <div class="card">
                <h2>Books</h2>
                <p><?php echo $books_count; ?> uploaded</p>
                <a href="manage_books.php" class="btn">Manage Books</a>
            </div>
            <div class="card">
                <h2>Departments</h2>
                <p><?php echo $departments_count; ?> uploaded</p>
                <a href="manage_departments.php" class="btn">Manage Departments</a>
            </div>
            <div class="card">
                <h2>Devotionals</h2>
                <p><?php echo $devotionals_count; ?> uploaded</p>
                <a href="manage_devotionals.php" class="btn">Manage Devotionals</a>
            </div>
            <div class="card">
                <h2>Growth Form</h2>
                <p><?php echo $growth_contacts_count; ?> sign ups</p>
                <a href="view_growthcontacts.php" class="btn">View Growth Form</a>
            </div>
            <div class="card">
                <h2>RSVPS For Events</h2>
                <p><?php echo $rsvps_count; ?> registered</p>
                <a href="rsvps.php" class="btn">View Event RSVPs</a>
            </div>
            <div class="card">
                <h2>Subscriptions</h2>
                <p><?php echo $subscriptions_count; ?> subscribed</p>
                <a href="view_subs.php" class="btn">View Subscriptions</a>
            </div>
            <!-- <div class="card">
                <h2>Purchases</h2>
                <p><?php echo $purchases_count; ?> purchases</p>
                <a href="view_puchases.php" class="btn">View Purchases</a>
            </div>
             -->


        </div>

       <!-- Recent Activity Table -->
<div class="table-container">
    <h2>Recent Activity</h2> 
    <table>
        <thead>
            <tr>          
                <th>User</th>
                <th>Activity</th>
                <th>Date</th>
            </tr>
        </thead> 
        <tbody> 
            <?php while ($activity = mysqli_fetch_assoc($recent_activities_result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($activity['user']); ?></td>
                    <td><?php echo htmlspecialchars($activity['activity']); ?></td>
                    <td><?php echo htmlspecialchars($activity['date']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</div>
    <script src="../../assets/js/admindashboard.js"></script>
</body>
</html>
