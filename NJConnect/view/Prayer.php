<?php
session_start(); // Ensure session is started

if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role']; // This could be 'admin' or 'regular'
} else {
    $user_role = 'guest'; // Default, in case the user is not logged in
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prayer Requests</title>
    <link href="https://fonts.googleapis.com/css2?family=Courgette&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Prayer.css">
</head>
<style>
 
</style>
<body>

    <!-- First Background Video -->
    <div class="background-video">
        <video autoplay muted loop>
            <source src="../assets/images/Prayer.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- Second Background Video with Reduced Transparency -->
    <div class="background-video-second">
        <video autoplay muted loop>
            <source src="../assets/images/Golden glitter flight with sparkling light .mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="logo-name">
            <img src="../assets/images/GL logo.png" alt="Logo" class="logo">    
            <span class="ministry-name">Glory Life New Jerusalem Generation</span>
        </div>
        <div class="nav-links">
                <a href="About.php">About Us</a>
                <a href="Events.php">Events</a>
                <a href="Sermons.php">Sermons</a>
                <a href="Departments.php">Ministries/Departments</a>
                <a href="Book.php">Books</a>
                <a href="Devotional.php">Devotional</a>
                <a href="#">Prayer Wall</a>
                <?php if ($user_role == 'admin'): ?>
        <li><a href="admin/admin_dashboard.php">My Account</a></li>  <!-- Admin Dashboard -->
    <?php elseif ($user_role == 'regular'): ?>
        <li><a href="admin/user_dashboard.php">My Account</a></li>    <!-- Regular User Dashboard -->
    <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
    <div class="growth-contact-left">
    <h2>Have a Prayer Request?</h2>
    <form id="prayerRequestForm" action="../actions/prayer/submit_prayer.php" method="POST">
        <textarea name="prayer_request" placeholder="Enter your prayer request here..." required></textarea>
        <button type="submit">Submit Prayer Request</button>
    </form>
    <div id="successMessage" style="display:none; color: green; margin-top: 10px;">
        Prayer request submitted successfully.
    </div>
</div>

        <!-- Growth and Contact Form Section (Right, Diagonal) -->
    <div class="growth-contact-right">
    <h2>Do you desire to grow in the faith?</h2>
        <p>Want to be a part of a family of believers?</p>
    <form id="growthContactForm" action="../actions/prayer/submit_growth_contact.php" method="POST">
        <input type="text" name="name" id="name" placeholder="Your Name" required>
        <input type="text" name="phone" id="phone" placeholder="Your Phone Number" required>
        <label>How would you like to be contacted?</label>
        <select name="contact_method" id="contact_method" required>
            <option value="call">Call</option>
            <option value="whatsapp">WhatsApp</option>
            </select>
    <button type="submit">Submit</button>
  </form>
  <div id="responseMessage"></div> <!-- Div to display success/error messages -->
</div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="social-media">
            <p>Follow us on:</p>
            <a href="https://facebook.com" target="_blank"><img src="../assets/images/fb.png" alt="Facebook"></a>
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
</body>
<script>
    // Handle the form submission using AJAX
    document.getElementById('prayerRequestForm').addEventListener('submit', function(event) {
        event.preventDefault();  // Prevent the form from submitting the traditional way
        
        const form = event.target;
        const formData = new FormData(form);  // Get the form data

        // Send the form data via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())  // Parse the JSON response from the server
        .then(data => {
            if (data.status === 'success') {
                // Show the success message
                document.getElementById('successMessage').style.display = 'block';
                
                setTimeout(function() {
                    // Hide the success message
                    document.getElementById('successMessage').style.display = 'none';

                    // Reload the page after showing the success message
                    location.reload();
                }, 3000);  // 3 seconds delay
            } else {
                // Show the error message (if any)
                document.getElementById('errorMessage').innerText = data.message;
                document.getElementById('errorMessage').style.display = 'block';
            }

            // Optionally, reset the form after submission
            form.reset();
        })
        .catch(error => {
            console.error('Error submitting prayer request:', error);
        });
    });

    document.getElementById('growthContactForm').addEventListener('submit', function(event) {
    event.preventDefault();  // Prevent form submission and page reload
    
    // Prepare form data
    let formData = new FormData(this);

    // Create a new XMLHttpRequest (AJAX)
    let xhr = new XMLHttpRequest();
    xhr.open('POST', this.action, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
      if (xhr.status === 200) {
        // Parse the JSON response
        let response = JSON.parse(xhr.responseText);
        
        // Check success or failure
        let responseMessage = document.getElementById('responseMessage');
        if (response.success) {
          responseMessage.style.color = 'green';
          responseMessage.textContent = response.message;
        } else {
          responseMessage.style.color = 'red';
          responseMessage.textContent = response.message;
        }

        // Reload the page after 3 seconds
        setTimeout(function() {
          location.reload(); // This reloads the page
        }, 500); 
      } else {
        console.error('Error: ' + xhr.status);
      }
    };

    // Send form data
    xhr.send(new URLSearchParams(formData).toString());
});

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

</html>


