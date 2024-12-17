<?php
include('../db/config.php');
require '../functions/mailer.php'; 

// Helper function to generate a random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2)); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $conn->real_escape_string(trim($_POST['email'])); // Sanitize email input

    // Check if the email exists
    $query = "SELECT user_id FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Generate token and expiration time
        $token = generateToken();
        $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Update the user's reset_token and token_expires_at
        $updateQuery = "UPDATE users SET reset_token = '$token', token_expires_at = '$expires_at' WHERE email = '$email'";
        if ($conn->query($updateQuery)) {
            // Send reset link via email
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/~jessica.yumu/NJConnect/actions/resetPassword.php?token=$token";
            $subject = "Reset Your Password";
            $body = "<p>Click the link below to reset your password:</p><p><a href='$resetLink'>$resetLink</a></p>";

            if (sendEmail($email, $subject, $body)) {
                echo "Password reset email sent! Check your email.";
            } else {
                echo "Failed to send password reset email. Please try again.";
            }
        } else {
            echo "Error updating reset token. Please try again.";
        }
    } else {
        echo "No account found with that email.";
    }
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Query to check if the token exists in the database and if it's expired
    $query = "SELECT user_id, token_expires_at FROM users WHERE reset_token = '$token'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if the token has expired
        $expires_at = $row['token_expires_at'];
        if (strtotime($expires_at) > time()) {
            // Token is valid, display the reset password form
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
                // Sanitize and validate new password inputs
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                if (empty($new_password) || empty($confirm_password)) {
                    echo "<p class='error'>Please fill in both password fields.</p>";
                } elseif ($new_password === $confirm_password) {
                    // Securely hash the password
                    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update the password in the database
                    $updateQuery = "UPDATE users SET password = '$new_password_hash', reset_token = NULL, token_expires_at = NULL WHERE reset_token = '$token'";
                    if ($conn->query($updateQuery)) {
                        // Success, redirect to login with message
                        echo "<p style='color: green; font-size: 14px; margin-bottom: 12px;'>Password has been successfully reset! <a href='../view/Login.php' style='color: #5c9dfd; text-decoration: none;'>Go to Login</a></p>";
                    } else {
                        echo "<p class='error'>There was an error updating your password.</p>";
                    }
                } else {
                    echo "<p class='error'>Passwords do not match. Please try again.</p>";
                }
            }
        } else {
            echo "<p class='error'>Your reset token has expired. Please request a new one.</p>";
        }
    } else {
        echo "<p class='error'>Invalid reset token.</p>";
    }
}
?>

<?php if (isset($_GET['token']) && !isset($_POST['new_password'])): ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[type="password"]:focus {
            border-color: #5c9dfd;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #5c9dfd;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #3a7bd5;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .password-container {
            position: relative;
        }

        .eye-icon {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-size: 18px;
            color: #555;
        }
    </style>

    <div class="form-container">
        <h2>Reset Your Password</h2>
        <form method="POST" action="">
            <label for="new_password">New Password:</label>
            <div class="password-container">
                <input type="password" id="new_password" name="new_password" required>
                <span id="toggleNewPassword" class="eye-icon">&#128065;</span> 
            </div>

            <label for="confirm_password">Confirm New Password:</label>
            <div class="password-container">
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span id="toggleConfirmPassword" class="eye-icon">&#128065;</span> 
            </div>

            <button type="submit">Reset Password</button>
        </form>
    </div>
<?php endif; ?>

<script>
    // Toggle new password visibility
    document.getElementById('toggleNewPassword').addEventListener('click', function() {
        var passwordField = document.getElementById('new_password');
        var type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
    });

    // Toggle confirm password visibility
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        var confirmPasswordField = document.getElementById('confirm_password');
        var type = confirmPasswordField.type === 'password' ? 'text' : 'password';
        confirmPasswordField.type = type;
    });
</script>
