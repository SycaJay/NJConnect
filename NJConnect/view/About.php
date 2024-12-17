<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Glory Life New Jerusalem Generation</title>
    <link rel="stylesheet" href="../assets/css/About.css">
</head>
<body>
    <header>
        <div style="display: flex; align-items: center;">
            <img src="../assets/images/GL logo.png" alt="Glory Life Logo" class="logo">
            <h1>Glory Life New Jerusalem Generation</h1>
        </div>
        <div class="login-register">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="">About Us</a>
                <a href="Events.php">Events</a>
                <a href="Sermons.php">Sermons</a>
                <a href="Departments.php">Ministries/Departments</a>
                <a href="Book.php">Books</a>
                <a href="Devotional.php">Devotional</a>
                <a href="Prayer.php">Prayer Wall</a>
                <?php if ($user_role == 'admin'): ?>
        <li><a href="admin/admin_dashboard.php">My Account</a></li>  <!-- Admin Dashboard -->
    <?php elseif ($user_role == 'regular'): ?>
        <li><a href="admin/user_dashboard.php">My Account</a></li>    <!-- Regular User Dashboard -->
    <?php endif; ?>
                <a href="Logout.php">Logout</a>
            <?php else: ?>
                <a href="Login.php">Login</a> | <a href="Register.php">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="content-container">
        <section>
            <div class="left-text">
                <h2>About Us</h2>
                <p>Glory Life New Jerusalem Generation</p>
            </div>
            <img src="../assets/images/IMG_1134.jpg" alt="About Us Image">
        </section>

        <section>
            <img src="../assets/images/IMG_1058.jpg" alt="Mission & Vision Image">
            <div class="left-text">
                <h2>Mission & Vision</h2>
                <p>Our mission is to bring a death to evil age even as we usher in the day of the Lord. We are set to dispel darkness and bring light. To teach the world that the Lord has a city and put the hope of the city in their hearts</p>
            </div>
        </section>

        <section>
            <div class="left-text">
                <h2>The Man of God</h2>
                <p>Our Highly Esteemed Man of God, Rev Dr Elliot Abraham</p>
            </div>
            <img src="../assets/images/IMG_0417.jpg" alt="Pastor Image">
        </section>
    </div>
</body>
</html>
