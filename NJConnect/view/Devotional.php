<?php
session_start();
include('../db/config.php');

if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role']; // This could be 'admin' or 'regular'
} else {
    $user_role = 'guest'; // Default, in case the user is not logged in
}

// Fetch the latest devotional content (both audio and document)
$sql = "SELECT * FROM devotionals 
        WHERE created_at >= CURDATE() 
        ORDER BY created_at DESC 
        LIMIT 2";
$result = $conn->query($sql);

$audio_devotional = null;
$document_devotional = null;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'audio') {
            $audio_devotional = $row;
        } else if ($row['type'] === 'document') {
            $document_devotional = $row;
        }
    }
}

// Function to safely read file contents
function getDocumentContent($filepath) {
    $fullPath = '../uploads/' . $filepath;
    if (file_exists($fullPath)) {
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        
        // Handle different file types
        if ($extension === 'txt') {
            return nl2br(htmlspecialchars(file_get_contents($fullPath)));
        } elseif ($extension === 'pdf') {
            // For PDFs, you might want to use a PDF viewer or converter
            return '<iframe src="../uploads/' . htmlspecialchars($filepath) . '" width="100%" height="400px"></iframe>';
        }
    }
    return false;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trumpets of the Ages Daily Devotional</title>
    <link rel="stylesheet" href="../assets/css/Devotional.css">
</head>
<body>
<header>
        <div class="logo-name">
            <img src="../assets/images/TOTA logo.png" alt="Ministry Logo" class="logo">
            <span class="ministry-name">Glory Life New Jerusalem Generation</span>
        </div>
        <nav class="top-nav">
        <ul>
                <li><a href="About.php">About Us</a></li>
                <li><a href="Events.php">Events</a></li>
                <li><a href="Sermons.php">Sermons</a></li>
                <li><a href="Departments.php">Ministries/Departments</a></li>
                <li><a href="Book.php">Books</a></li>
                <li><a href="#">Devotional</a></li>
                <li><a href="Prayer.php">Prayer Wall</a></li>
                <?php if ($user_role == 'admin'): ?>
        <li><a href="admin/admin_dashboard.php">My Account</a></li>  <!-- Admin Dashboard -->
    <?php elseif ($user_role == 'regular'): ?>
        <li><a href="admin/user_dashboard.php">My Account</a></li>    <!-- Regular User Dashboard -->
    <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
    <section class="devotional">
    <h1>Today's Devotional</h1>
    <?php if ($audio_devotional): ?>
        <h2><?php echo htmlspecialchars($audio_devotional['title']); ?></h2>
        <p class="devotional-text">
            <?php echo htmlspecialchars($audio_devotional['description']); ?>
        </p>
        
        <!-- Audio Section -->
        <?php if ($audio_devotional['audio_url']): ?>
            <!-- Display the audio file like an image -->
            <div class="audio-preview">
                <audio controls>
                    <source src="<?php echo '../uploads/' . htmlspecialchars($audio_devotional['audio_url']); ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
            
            <div class="download-buttons">
                <a href="<?php echo '../uploads/' . htmlspecialchars($audio_devotional['audio_url']); ?>" 
                   class="download-btn" 
                   download>
                    Download Audio
                </a>
            </div>
        <?php else: ?>
            <p>Audio content is not available for today's devotional.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>No audio devotional available for today.</p>
    <?php endif; ?>
</section>

<section class="upload-devotional">
    <h2>Today's Devotional</h2>
    
    <?php if ($document_devotional): ?>
        <h3><?php echo htmlspecialchars($document_devotional['title']); ?></h3>
        
        <div class="document-preview">
            <?php
            if ($document_devotional['file_url']) {
                $content = getDocumentContent($document_devotional['file_url']);
                if ($content) {
                    echo $content;
                } else {
                    echo '<p>' . htmlspecialchars($document_devotional['description']) . '</p>';
                }
            } else {
                echo '<p>' . htmlspecialchars($document_devotional['description']) . '</p>';
            }
            ?>
        </div>
        
        <?php if ($document_devotional['file_url']): ?>
            <div class="download-buttons">
                <a href="<?php echo '../uploads/' . htmlspecialchars($document_devotional['file_url']); ?>" 
                   class="download-btn" 
                   download>
                    Download Document
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p>No document available for today's devotional.</p>
    <?php endif; ?>
</section>


    </main>
  <!-- Footer -->
  <footer>
        <div class="social-media">
            <p>Follow us on:</p>
            <a href="https://www.facebook.com/groups/1454613778324060" target="_blank"><img src="../assets/images/fb.png" alt="Facebook"></a>
            <a href="https://youtube.com/@gloriousvisionsherrnhuttel4605" target="_blank"><img src="../assets/images/yt.png" alt="Youtube"></a>
            <a href="https://instagram.com/glorylife_today/" target="_blank"><img src="../assets/images/ig.jpeg" alt="Instagram"></a>
        </div>
        <div class="subscribe">
        <form id="subscriptionForm">
        <form action="../actions/subscribe.php" method="POST">
    <label for="email">Subscribe:</label> 
    <input type="email" id="email" name="email" placeholder="Enter your email" required> 
    <button type="submit">Subscribe For Daily News From Zion</button>
  </form>
  <div id="response"></div>
</div>
    </footer>

    <script>
        // Handle the subscription form submission with AJAX
 document.getElementById('subscriptionForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Get the email value
    var email = document.getElementById('email').value;

    // Create a FormData object to send the email
    var formData = new FormData();
    formData.append('email', email);

    // Create the XMLHttpRequest object for AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../actions/subscribe.php', true);

    // Set up a callback to handle the response
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Show the success message
            document.getElementById('response').innerHTML = xhr.responseText;

            setTimeout(function() {
                document.getElementById('subscriptionForm').reset(); 
                document.getElementById('response').innerHTML = '';
            }, 500); 
        } else {
            document.getElementById('response').innerHTML = "Error: " + xhr.statusText;
        }
    };

    // Send the form data
    xhr.send(formData);
});
    </script>
</body>
</html>