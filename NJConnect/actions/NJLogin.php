<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Start the session

// Include database connection
include('../db/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = 'Invalid email format';
        header("Location: ../view/Login.php");
        exit();
    }

    // Query the database to get the user's information
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email); // "s" denotes string type for email
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the account is locked
        if ($user['account_locked_until'] !== NULL && strtotime($user['account_locked_until']) > time()) {
            $lock_time = strtotime($user['account_locked_until']);
            $time_left = $lock_time - time(); // Time left in seconds
            
            $lock_message = "Your account is locked. Try again later.";
            $_SESSION['login_error'] = $lock_message;
            header("Location: ../view/Login.php");
            exit();
        }

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Reset failed attempts on successful login
            $update_query = "UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL WHERE email = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("s", $email);
            $update_stmt->execute();

            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Set session variables to track the logged-in user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($_SESSION['role'] === 'admin') {
                header("Location: ../view/admin/admin_dashboard.php");
            } else {
                header("Location: ../view/admin/user_dashboard.php");
            }
            exit();
        } else {
            // Increment failed attempts and update the timestamp
            $failed_attempts = $user['failed_attempts'] + 1;
            $last_failed_attempt = date('Y-m-d H:i:s');
            $account_locked_until = NULL;

            // Lock the account for 24 hours after 5 failed attempts
            if ($failed_attempts >= 5) {
                $account_locked_until = date('Y-m-d H:i:s', strtotime('+24 hours'));
                $failed_attempts = 0; // Reset failed attempts after lock
            }

            // Update the user’s failed attempts and last failed attempt timestamp
            $update_query = "UPDATE users SET failed_attempts = ?, last_failed_attempt = ?, account_locked_until = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("isss", $failed_attempts, $last_failed_attempt, $account_locked_until, $email);
            $update_stmt->execute();

            // Check if the user is close to locking the account
            if ($failed_attempts >= 3 && $failed_attempts < 5) {
                $_SESSION['login_error'] = "Invalid password. Warning: Your account will be locked after 5 failed attempts.";
            } else {
                $_SESSION['login_error'] = 'Invalid password. Please try again.';
            }

            header("Location: ../view/Login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = 'No user found with this email';
        header("Location: ../view/Login.php");
        exit();
    }
}
?>
