<?php
session_start();
?>
     
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Glory Life New Jerusalem Generation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Login.css">
</head>
<body>

    <video autoplay muted loop>
        <source src="../assets/images/Sky Timelapse 4K.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="overlay"></div>

    <div class="logo-container">
        <img src="../assets/images/GL logo.png" alt="Logo">
        <div class="logo">
            Glory Life New Jerusalem Generation
        </div>
    </div>

    <div class="login-container">

    <?php
    if (isset($_SESSION['login_error'])) {
        echo "<div style='color: red; font-weight: bold; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin-top: 10px; border-radius: 5px;'>" . $_SESSION['login_error'] . "</div>";
        unset($_SESSION['login_error']); // Clear the error message after displaying
    }
?>
        <h2>Join Us in Fellowship</h2>
        <form action="../actions/NJLogin.php" method="POST" id="loginForm" onsubmit="return validateForm()">
            <input type="email" id="email" name="email" class="input-field" placeholder="Email" required>
            <input type="password" id="password" name="password" class="input-field" placeholder="Password" required minlength="8">
    <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="footer">
        <p><a href="forgotPassword.php">Forgot password?</a></p>
            <p>Don't have an account? <a href="Register.php">Sign up</a></p>
        </div>
    </div>

    <script>
        function validateForm() {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!email.value.match(emailPattern)) {
                alert("Please enter a valid email address.");
                email.focus();
                return false;
            }

            if (password.value.length < 8) {
                alert("Password must be at least 8 characters long.");
                password.focus();
                return false;
            }

            return true;
        }
    </script>

</body>
</html>
