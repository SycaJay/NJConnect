<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'] ?? '';
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        echo "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
    } else {
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Glory Life</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Register.css">
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

    <div class="signup-container">
        <h2>Start Your Journey with Us</h2>
        <form action="../actions/NJRegister.php" method="POST" onsubmit="return validateForm()">
            <input type="text" name="first_name" class="input-field" placeholder="First Name" required>
            <input type="text" name="middle_name" class="input-field" placeholder="Middle Name (Optional)">
            <input type="text" name="last_name" class="input-field" placeholder="Last Name" required>
            <input type="email" name="email" class="input-field" placeholder="Email" required>
            <input type="password" name="password" class="input-field" placeholder="Password" required minlength="8">
            <input type="password" name="confirm_password" class="input-field" placeholder="Confirm Password" required>
            <button type="submit" class="btn-signup">Sign Up</button>
        </form>

        <div class="footer">
            <p>Already have an account? <a href="Login.php">Login here</a></p>
        </div>
    </div>

    <script>
        function validateForm() {
    const password = document.querySelector('input[name="password"]');
    const confirmPassword = document.querySelector('input[name="confirm_password"]');
    const middleName = document.querySelector('input[name="middle_name"]');

    // Check if passwords match
    if (password.value !== confirmPassword.value) {
        alert('Passwords do not match!');
        return false;
    }

    // Check if password length is valid
    if (password.value.length < 8) {
        alert('Password must be at least 8 characters long.');
        return false;
    }

    // Check if middle name is not an empty string (optional field)
    if (middleName && middleName.value === "") {
        // No need to do anything since it's optional
    }

    return true;
}

    </script>

</body>
</html>
