<?php
session_start();
include('../db/config.php');

if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role']; // This could be 'admin' or 'regular'
} else {
    $user_role = 'guest'; // Default, in case the user is not logged in
}

// Fetch sermons from database with search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM sermons WHERE 1=1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}

$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);
$sermons = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sermons[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sermons Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
/* General styles */
body {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    background-color: #f4f4f4;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* Background video and overlay */
.background-video {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -1;
}

.video-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: -1;
}

/* Header styles */
header {
    background-color: #4a148c;
    opacity: 0.8;
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

header .logo {
    display: flex;
    align-items: center;
    font-size: 24px;
    color: white;
}

header .logo img {
    height: 50px;
    margin-right: 10px;
}

header nav a {
    color: white;
    margin: 0 15px;
    text-decoration: none;
    font-size: 16px;
}

header nav a:hover {
    text-decoration: underline;
}

/* Search form styles */
.search-form {
    text-align: center;
    margin: 20px;
}

.search-input {
    width: 50%;
    padding: 10px;
    font-size: 16px;
    border: 2px solid #4a148c;
    border-radius: 5px;
    margin-right: 10px;
}

.search-button {
    padding: 10px 20px;
    background-color: #4a148c;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

/* Sermons grid */
.sermons {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 20px;
    padding: 20px;
    margin-bottom: 40px;
}

/* Sermon card styles */
.sermon {
    background-color: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.sermon.audio {
    aspect-ratio: 4/3;
}

.sermon-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sermon:hover {
    transform: scale(1.02);
}

.sermon-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.sermon-thumbnail {
    width: 100%;
    height: 60%;
    object-fit: cover;
}

.sermon-info {
    padding: 15px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.sermon-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 8px;
}

.sermon-description {
    font-size: 14px;
    margin-bottom: 8px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.sermon-date {
    font-size: 12px;
    color: #ccc;
    margin-bottom: 10px;
}

.sermon-controls {
    display: flex;
    gap: 10px;
    align-items: center;
}

.sermon-media {
    flex-grow: 1;
}

.sermon-media audio,
.sermon-media video {
    width: 100%;
    max-height: 40px;
}

.download-btn {
    background-color: #4a148c;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.download-btn:hover {
    background-color: #3a1078;
}

/* Footer styles */
footer {
    background-color: #4a148c;
    color: white;
    padding: 20px;
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

footer .social-media a {
    margin: 0 10px;
}

footer .social-media img {
    width: 30px;
    height: 30px;
}

footer .subscribe input {
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    margin-right: 10px;
}

footer .subscribe button {
    padding: 10px 15px;
    background-color: #ff6f00;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

footer .subscribe button:hover {
    background-color: #e65100;
}

/* Media Queries */

/* For tablets */
@media (max-width: 768px) {
    .search-input {
        width: 80%;
    }

    header nav a {
        margin: 0 10px;
        font-size: 14px;
    }

    .sermons {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

/* For phones */
@media (max-width: 480px) {
    header {
        flex-direction: column;
        text-align: center;
    }

    .search-input {
        width: 100%;
        margin-bottom: 10px;
    }

    .sermons {
        grid-template-columns: 1fr;
    }

    footer {
        flex-direction: column;
        text-align: center;
    }

    footer .subscribe {
        flex-direction: column;
    }

    footer .subscribe input {
        margin-bottom: 10px;
        width: 100%;
    }
}

    </style>
</head>
<body>
    <!-- Background video -->
    <video class="background-video" autoplay muted loop>
        <source src="../assets/images/Ophanim - Biblically Accurate Angel.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="video-overlay"></div>

    <!-- Header -->
    <header>
        <div class="logo">
            <img src="../assets/images/GL logo.png" alt="Ministry Logo"> Glory Life New Jerusalem Generation
        </div>
        <nav>
            <a href="About.php">About Us</a>
            <a href="Events.php">Events</a>
            <a href="#">Sermons</a>
            <a href="Departments.php">Ministries/Departments</a>
            <a href="Book.php">Books</a>
            <a href="Devotional.php">Devotional</a>
            <a href="Prayer.php">Prayer Wall</a>
            <?php if ($user_role == 'admin'): ?>
        <li><a href="admin/admin_dashboard.php">My Account</a></li>  <!-- Admin Dashboard -->
    <?php elseif ($user_role == 'regular'): ?>
        <li><a href="admin/user_dashboard.php">My Account</a></li>    <!-- Regular User Dashboard -->
    <?php endif; ?>
        </nav>
    </header>

    <!-- Search bar -->
    <form class="search-form" method="GET">
        <input type="text" name="search" class="search-input" 
               placeholder="Search sermons, topics, or speakers..." 
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="search-button">Search</button>
    </form>

    <!-- Sermons Section -->
    <div class="sermons">
        <?php if (!empty($sermons)): ?>
            <?php foreach ($sermons as $sermon): ?>
              <div class="sermon <?php echo $sermon['sermon_type']; ?>">
    <div class="sermon-content">
        <?php if ($sermon['sermon_type'] === 'video' && !empty($sermon['media'])): ?>
            <!-- Video fills the whole box -->
            <video class="sermon-video" controls>
                <source src="<?php echo '../uploads/' . htmlspecialchars($sermon['media']); ?>" type="video/mp4">
                Your browser does not support the video element.
            </video>
        <?php else: ?>
            <!-- Default image for other sermon types -->
            <img src="<?php echo !empty($sermon['image']) ? '../uploads/' . htmlspecialchars($sermon['image']) : '../assets/images/default-sermon.jpg'; ?>" 
                 alt="<?php echo htmlspecialchars($sermon['title']); ?>" 
                 class="sermon-thumbnail">
        <?php endif; ?>

        <div class="sermon-info">
            <div class="sermon-title"><?php echo htmlspecialchars($sermon['title']); ?></div>
            <div class="sermon-description"><?php echo htmlspecialchars($sermon['description']); ?></div>
            <div class="sermon-date">
                <?php echo date('F j, Y', strtotime($sermon['created_at'])); ?>
            </div>
            
            <div class="sermon-controls">
                <div class="sermon-media">
                    <?php if ($sermon['sermon_type'] === 'audio' && !empty($sermon['media'])): ?>
                        <audio controls>
                            <source src="<?php echo '../uploads/' . htmlspecialchars($sermon['media']); ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    <?php elseif ($sermon['sermon_type'] === 'video' && !empty($sermon['media'])): ?>
                        <video controls>
                            <source src="<?php echo '../uploads/' . htmlspecialchars($sermon['media']); ?>" type="video/mp4">
                            Your browser does not support the video element.
                        </video>
                    <?php endif; ?>
                </div>
                <?php if (!empty($sermon['media'])): ?>
                    <a href="<?php echo '../uploads/' . htmlspecialchars($sermon['media']); ?>" 
                       download 
                       class="download-btn">
                        <i class="fas fa-download"></i>
                        Download
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; width: 100%; padding: 20px; color: white;">
                <?php echo empty($search) ? 'No sermons available.' : 'No sermons found matching your search.'; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="social-media">
            <p>Follow us on:</p>
            <a href="https://www.facebook.com/groups/1454613778324060" target="_blank"><img src="../assets/images/fb.png" alt="Facebook"></a>
            <a href="https://youtube.com/@gloriousvisionsherrnhuttel4605" target="_blank"><img src="../assets/images/yt.png" alt="Youtube"></a>
            <a href="https://instagram.com/glorylife_today/" target="_blank"><img src="../assets/images/ig.jpeg" alt="Instagram"></a>
        </div>
    </footer>
</body>
</html>