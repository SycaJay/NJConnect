<?php
// Include the database connection
include('../../db/config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $middle_name = !empty($_POST['middle_name']) ? mysqli_real_escape_string($conn, trim($_POST['middle_name'])) : NULL;
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
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

    // Check if email already exists
    $email_check_query = "SELECT * FROM users WHERE email = '$email'";
    $email_check_result = mysqli_query($conn, $email_check_query);
    if (mysqli_num_rows($email_check_result) > 0) {
        $errors[] = "Email already exists.";
    }

    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Validate role
    if (!in_array($role, ['admin', 'regular'])) {
        $errors[] = "Invalid role selected.";
    }

    // If no errors, proceed with user creation
    if (empty($errors)) {
        // Prepare the SQL query (handle potential NULL for middle_name)
        if ($middle_name !== NULL) {
            $query = "INSERT INTO users (first_name, middle_name, last_name, email, password, role) 
                      VALUES ('$first_name', '$middle_name', '$last_name', '$email', '$password', '$role')";
        } else {
            $query = "INSERT INTO users (first_name, last_name, email, password, role) 
                      VALUES ('$first_name', '$last_name', '$email', '$password', '$role')";
        }
        
        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage users page after successful insertion
            header("Location: ../../view/admin/manage_users.php?success=1");
            exit();
        } else {
            // Error message if the insertion fails
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