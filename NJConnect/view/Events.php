<?php
session_start(); 
include('../db/config.php');

if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role']; // This could be 'admin' or 'regular'
} else {
    $user_role = 'guest'; // Default, in case the user is not logged in
}

if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role']; // This could be 'admin' or 'regular'
} else {
    $user_role = 'guest'; // Default, in case the user is not logged in
}

// Fetch upcoming events from the database
try {
    // Prepare and execute the query using the mysqli connection
    $query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
    $result = $conn->query($query); // Use $conn to run the query

    if ($result) {
        // Fetch all rows as an associative array
        $upcomingEvents = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // Handle query failure (if the result is false)
        throw new Exception("Query failed: " . $conn->error);
    }
} catch(Exception $e) {
    // Log or handle the error appropriately
    error_log($e->getMessage());
    $upcomingEvents = []; // Empty array if no events or error occurs
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministry Events</title>
    <!-- Google Fonts for stylish fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/events.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <img src="../assets/images/GL logo.png" alt="Ministry Logo">
            <h1>Glory Life New Jerusalem Generation</h1>
        </div>
        <nav>
        <ul>
                <li><a href="About.php">About Us</a></li>
                <li><a href="#">Events</a></li>
                <li><a href="Sermons.php">Sermons</a></li>
                <li><a href="Departments.php">Ministries/Departments</a></li>
                <li><a href="Book.php">Books</a></li>
                <li><a href="Devotional.php">Devotional</a></li>
                <li><a href="Prayer.php">Prayer Wall</a></li>
                <?php if ($user_role == 'admin'): ?>
        <li><a href="admin/admin_dashboard.php">My Account</a></li>  <!-- Admin Dashboard -->
    <?php elseif ($user_role == 'regular'): ?>
        <li><a href="admin/user_dashboard.php">My Account</a></li>    <!-- Regular User Dashboard -->
    <?php endif; ?>
            </ul>
        </nav>
    </header>
    <section id="events">
    <u><h2>Upcoming Events</h2></u>

    <div class="events-container">
        <?php if (!empty($upcomingEvents)): ?>
            <?php foreach ($upcomingEvents as $event): ?>
                <div class="event">
                    <!-- Display the event image -->
                    <img src="<?php echo '../uploads/' . htmlspecialchars($event['image_path']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                    <div class="countdown" id="countdown<?php echo $event['event_id']; ?>"></div>
                    <!-- Display the event title -->
                    <h3 style="color: white; background-color: rgba(0, 0, 0, 0.6);border-radius: 10px;"><?php echo htmlspecialchars($event['title']); ?></h3>
                    <!-- Add link to seat selection page -->
                    <a href="seats.php?event_id=<?php echo $event['event_id']; ?>" class="rsvp-btn">RSVP</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No upcoming events at the moment.</p>
        <?php endif; ?>
    </div>
</section>

    <!-- Past Event Section -->
    <section id="past-events">
        <div class="past-event">
            <div class="description">
                <h3>BATTLE OF THE AGES CONFERENCE 2023 HIGHLIGHTS</h3>
                <p>We are in the last days, and as believers, we are faced with a choice: to propagate a good course or an evil one. There exists no middle ground. The battle of the ages conference annually held by New Jerusalem Generation is to prepare believers and individuals all around the world for the soon coming of the Lord and how best they can live in these last days </p>
            </div>
            <video width="100%" controls>
                <source src="../assets/images/BOTA.mp4" type="video/mp4">
            </video>
        </div>
    </section>

    <!-- FAQs Section -->
    <!-- <div id="faq">
        <h2>FAQs</h2>
        <div class="faq-item">
            <h3>What is the event about? <span class="arrow">&#9662;</span></h3>
            <p>The event is about bringing together the ministry members for worship and fellowship.</p>
        </div>
        <div class="faq-item">
            <h3>When is the event? <span class="arrow">&#9662;</span></h3>
            <p>The event will be held on December 15th, 2024.</p>
        </div>
        <div class="faq-item">
            <h3>How can I participate? <span class="arrow">&#9662;</span></h3>
            <p>You can participate by registering through the online form on our website.</p>
        </div>
    </div> -->
    

    <!-- Footer Section -->
    <footer>
        <div class="social-media">
            <p>Follow us on:</p>
            <a href="https://facebook.com" target="_blank"><img src="../assets/images/fb.png" alt="Facebook"></a>
            <a href="https://youtube.com" target="_blank"><img src="../assets/images/yt.png" alt="Youtube"></a>
            <a href="https://instagram.com" target="_blank"><img src="../assets/images/ig.jpeg" alt="Instagram"></a>
        </div>
    </footer>

</body>
<script src="../assets/js/events.js"></script>
</html>