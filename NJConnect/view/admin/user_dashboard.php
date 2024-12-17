<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include database connection
include('../../db/config.php');

// Assume the user is logged in and their user_id is stored in a session variable
session_start();
$user_id = $_SESSION['user_id'];

// Fetch user's first name from the database
$user_query = "SELECT first_name FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
$user_first_name = $user['first_name'];



// Query for Upcoming Events (RSVPs)
$rsvp_query = "SELECT COUNT(*) as rsvp_count, 
                      e.title, 
                      e.event_date,
                      r.seat_number  -- Include the seat_number
               FROM rsvps r
               JOIN events e ON r.event_id = e.event_id
               WHERE r.user_id = ? AND e.event_date >= CURDATE()
               GROUP BY e.title, e.event_date, r.seat_number  -- Add GROUP BY for title, event_date, and seat_number
               ORDER BY e.event_date ASC 
               LIMIT 1";
               $rsvp_stmt = $conn->prepare($rsvp_query);
               $rsvp_stmt->bind_param("i", $user_id);
               $rsvp_stmt->execute();
               $rsvp_result = $rsvp_stmt->get_result();
               $rsvp_data = $rsvp_result->fetch_assoc();


// Query for Prayer Requests
$prayer_request_query = "SELECT COUNT(*) as prayer_count 
                         FROM prayer_requests 
                         WHERE user_id = ?";
$prayer_request_stmt = $conn->prepare($prayer_request_query);
$prayer_request_stmt->bind_param("i", $user_id);
$prayer_request_stmt->execute();
$prayer_request_result = $prayer_request_stmt->get_result();
$prayer_request_data = $prayer_request_result->fetch_assoc();

// Query for Cart Items
$cart_query = "SELECT COUNT(*) as cart_count 
               FROM cart 
               WHERE user_id = ?";
$cart_stmt = $conn->prepare($cart_query);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
$cart_data = $cart_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Glory Life</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<style>
    body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

header {
    background-color: #0077b6;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-container {
    display: flex;
    justify-content: flex-start;  /* Aligns navigation links to the left */
    width: 100%;
}

.nav-links {
    display: flex;
    gap: 15px;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 400;
}

.nav-links a:hover {
    text-decoration: underline;
}

.profile-picture {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;  /* This pushes the profile picture to the far right */
}

.profile-picture img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid white;
    cursor: pointer;
}

.container {
    display: flex;
    margin: 20px;
}

.sidebar {
    width: 25%;
    background-color: #0077b6;
    padding: 15px;
    border-right: 1px solid #ddd;
}

.sidebar h3 {
    margin-top: 0;
    font-size: 20px;
    color:rgb(255, 255, 255);
}

.sidebar a {
    display: block;
    color:rgb(255, 255, 255);
    text-decoration: none;
    margin: 10px 0;
}

.sidebar a:hover {
    text-decoration: underline;
}

.main-content {
    flex-grow: 1;
    padding: 20px;
}

.card {
    background-color: #f1f1f1;
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.card h3 {
    margin-top: 0;
    color: #333;
}

.card p {
    margin: 5px 0;
}

.card a {
    color: #0077b6;
}

</style>
<body>
<header>
    <div class="nav-container">
        <div class="nav-links">
            <a href="../About.php">About Us</a>
            <a href="../Events.php">Events</a>
            <a href="../Sermons.php">Sermons</a>
            <a href="../Departments.php">Ministries/Departments</a>
            <a href="../Book.php">Books</a>
            <a href="../Devotional.php">Devotional</a>
            <a href="../Prayer.php">Prayer Wall</a>
            <a href="<?php echo htmlspecialchars($my_account); ?>">My Account</a>
        </div>
        
        <!-- Profile Picture -->
        <div class="profile-picture">
            <img src="../../assets/images/default-profile.jpg" alt="Profile Picture" id="profile-pic">
            <input type="file" id="change-pic" style="display: none;">
        </div>
    </div>
</header>


    <div class="container">
        <div class="sidebar">
        <h3>Welcome, <?php echo htmlspecialchars($user_first_name); ?></h3>
            <a href="#">Profile</a>
            <a href="#events">Event RSVPs</a>
            <a href="#prayer-requests">Prayer Requests</a>
            <li><a href="../../actions/NJLogout.php">Logout</a></li>
        </div>

        <div class="main-content">
   <!-- Dashboard Overview -->
<div class="card">
    <h3>Upcoming Events</h3>
    <p>You have RSVPed for <?php echo $rsvp_data['rsvp_count'] ?? 0; ?> event(s)</p>
    <?php if (!empty($rsvp_data['title'])): ?>
        <p><strong>Next Event:</strong> <?php echo htmlspecialchars($rsvp_data['title']); ?> - 
        <?php echo date('l, F j', strtotime($rsvp_data['event_date'])); ?></p>
        <!-- Display the seat number -->
        <?php if (!empty($rsvp_data['seat_number'])): ?>
            <p><strong>Your Seat Number:</strong> <?php echo htmlspecialchars($rsvp_data['seat_number']); ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>


    <div class="card">
        <h3>Prayer Requests</h3>
        <p>You have <?php echo $prayer_request_data['prayer_count'] ?? 0; ?> prayer request(s) being prayed for.</p>
        <p><a href="../Prayer.php" style="color: #0077b6;">Submit a New Request</a></p>
    </div>

    <!-- <div class="card">
        <h3>Shopping Cart</h3>
        <p>You have <?php echo $cart_data['cart_count'] ?? 0; ?> item(s) in your cart.</p>
        <p><a href="#cart" style="color: #0077b6;">View Cart</a></p>
    </div> -->
</div>
    <script>
        // Get the image element and the file input element
const profilePic = document.getElementById('profile-pic');
const changePic = document.getElementById('change-pic');

// Load the saved profile picture from localStorage if it exists
window.onload = function() {
    const savedPic = localStorage.getItem('profilePic');
    if (savedPic) {
        profilePic.src = savedPic;
    }
};

// When the file input changes (a new file is selected)
changePic.addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onloadend = function() {
            const imgUrl = reader.result;
            profilePic.src = imgUrl; // Change the profile picture to the selected one
            localStorage.setItem('profilePic', imgUrl); // Save the image URL in localStorage
        };
        reader.readAsDataURL(file); // Convert the file to a base64 string
    }
});

// Trigger file input when the profile image is clicked
profilePic.addEventListener('click', function() {
    changePic.click();
});

        
    </script>
</body>
</html>
