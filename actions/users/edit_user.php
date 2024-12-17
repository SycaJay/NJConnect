<?php
// Include database connection
include('../../db/config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the user details from the form
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $middle_name = !empty($_POST['middle_name']) ? mysqli_real_escape_string($conn, trim($_POST['middle_name'])) : NULL;
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Input validation
    $errors = [];

    // Validate first name
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    } elseif (strlen($first_name) > 50) {
        $errors[] = "First name cannot exceed 50 characters.";
    }

    // Validate last name
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    } elseif (strlen($last_name) > 50) {
        $errors[] = "Last name cannot exceed 50 characters.";
    }

    // Validate middle name (if provided)
    if (!empty($middle_name) && strlen($middle_name) > 50) {
        $errors[] = "Middle name cannot exceed 50 characters.";
    }

    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if email already exists (excluding current user)
    $email_check_query = "SELECT * FROM users WHERE email = '$email' AND user_id != '$user_id'";
    $email_check_result = mysqli_query($conn, $email_check_query);
    if (mysqli_num_rows($email_check_result) > 0) {
        $errors[] = "Email already exists.";
    }

    // Validate role
    if (!in_array($role, ['admin', 'regular'])) {
        $errors[] = "Invalid role selected.";
    }

    // Check if password is being updated
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        
        // Password validation
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
    } else {
        // If no new password, fetch the existing password
        $password_query = "SELECT password FROM users WHERE user_id = '$user_id'";
        $password_result = mysqli_query($conn, $password_query);
        $password_row = mysqli_fetch_assoc($password_result);
        $password = $password_row['password'];
    }

    // If no errors, proceed with user update
    if (empty($errors)) {
        // Prepare the SQL query (handle potential NULL for middle_name)
        if ($middle_name !== NULL) {
            $query = "UPDATE users SET 
                        first_name = '$first_name', 
                        middle_name = '$middle_name', 
                        last_name = '$last_name', 
                        email = '$email', 
                        password = '$password', 
                        role = '$role' 
                      WHERE user_id = '$user_id'";
        } else {
            $query = "UPDATE users SET 
                        first_name = '$first_name', 
                        middle_name = NULL, 
                        last_name = '$last_name', 
                        email = '$email', 
                        password = '$password', 
                        role = '$role' 
                      WHERE user_id = '$user_id'";
        }

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage users page after successful update
            header("Location: ../../view/admin/manage_users.php?success=1");
            exit();
        } else {
            // Error message if the update fails
            header("Location: ../../view/admin/manage_users.php?error=" . urlencode(mysqli_error($conn)));
            exit();
        }
    } else {
        // If there are validation errors, redirect back with error messages
        $error_string = implode('; ', $errors);
        header("Location: ../../view/admin/manage_users.php?error=" . urlencode($error_string));
        exit();
    }
}
?>