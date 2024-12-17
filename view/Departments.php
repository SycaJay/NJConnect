<?php
session_start(); // Ensure session is started
if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role']; // This could be 'admin' or 'regular'
} else {
    $user_role = 'guest'; // Default, in case the user is not logged in
}

include('../db/config.php');

// Fetch departments from database
$departments_query = "SELECT * FROM departments ORDER BY created_at";
$departments_result = $conn->query($departments_query);

// Store manually defined departments and database departments
$manual_departments = [
    ['name' => 'HERRNHUT', 'description' => 'The Lord\'s Watch', 'image' => '../assets/images/Herrnhut.jpg'],
    ['name' => 'Trumpets Of The Ages Department', 'description' => '', 'image' => '../assets/images/TOTA.jpg'],
    ['name' => 'Campus Ministry', 'description' => '', 'image' => '../assets/images/Campus.jpg'],
    ['name' => 'Glorious Visions Networks', 'description' => '', 'image' => '../assets/images/Glorious visions.jpg'],
    ['name' => 'Heralds Of New Jerusalem', 'description' => '', 'image' => '../assets/images/Heralds.jpg'],
    ['name' => 'Teens & Kids Ministry', 'description' => '', 'image' => '../assets/images/Teens.JPG'],
    ['name' => 'Publishing Ministry', 'description' => '', 'image' => '../assets/images/Publish.JPG'],
    ['name' => 'Media Department', 'description' => '', 'image' => '../assets/images/Media.JPG'],
    ['name' => 'Healing Wings', 'description' => '', 'image' => '../assets/images/Healing.JPG'],
    ['name' => 'Glory Love Foundation', 'description' => '', 'image' => '../assets/images/GL foundation.jpg']
];

// Merge database departments with manual departments
$all_departments = $manual_departments;

// Add database departments to the list, avoiding duplicates
if ($departments_result->num_rows > 0) {
    while ($dept = $departments_result->fetch_assoc()) {
        // Check if department already exists in manual list
        $exists = false;
        foreach ($manual_departments as $manual_dept) {
            if (strtolower($manual_dept['name']) === strtolower($dept['name'])) {
                $exists = true;
                break;
            }
        }
        
        // If not a duplicate, add to departments
        if (!$exists) {
            $all_departments[] = [
                'name' => $dept['name'],
                'description' => $dept['description'] ?? '',
                'image' => $dept['image'] ?? '../assets/images/default.jpg' // Add a default image if none provided
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministries | Glory Life New Jerusalem Generation</title>
    <!-- Existing head content -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&family=Open+Sans:wght@300;400;600&family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Dept.css">
</head>
<body>
    <!-- Existing header section -->
    <header>
        <div class="logo-name">
            <img src="../assets/images/GL logo.png" alt="Ministry Logo" class="logo">
            <span class="ministry-name">Glory Life New Jerusalem Generation</span>
        </div>
        <nav class="top-nav">
        <ul>
                <li><a href="About.php">About Us</a></li>
                <li><a href="Events.php">Events</a></li>
                <li><a href="Sermons.php">Sermons</a></li>
                <li><a href="#">Ministries/Departments</a></li>
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

    <!-- Slideshow Section for Departments -->
    <div class="slideshow-container">
        <?php foreach ($all_departments as $index => $dept): ?>
            <div class="slide">
            <img src="<?php echo '../uploads/' . htmlspecialchars($dept['image']); ?>" alt="<?php echo htmlspecialchars($dept['name']); ?>">
                <div class="dept-info">
                    <h2><?php echo htmlspecialchars($dept['name']); ?></h2>
                    <?php if (!empty($dept['description'])): ?>
                        <p><?php echo htmlspecialchars($dept['description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Existing footer section -->
    <footer>
    <footer>
        <div class="social-media">
            <p>Follow us on:</p>
            <a href="https://facebook.com" target="_blank"><img src="../assets/images/fb.png" alt="Facebook"></a>
            <a href="https://youtube.com" target="_blank"><img src="../assets/images/yt.png" alt="YouTube"></a>
            <a href="https://instagram.com" target="_blank"><img src="../assets/images/ig.jpeg" alt="Instagram"></a>
        </div>
    </footer>
    </footer>

<script>
    let slideIndex = 0;

    function showSlides() {
        let slides = document.querySelectorAll('.slide');
        
        // Hide all slides
        slides.forEach(slide => slide.style.display = 'none');
        
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}
        
        slides[slideIndex-1].style.display = 'block'; // Show the current slide
        setTimeout(showSlides, 3000); // Change slide every 3 seconds
    }

    document.addEventListener('DOMContentLoaded', showSlides);
</script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>