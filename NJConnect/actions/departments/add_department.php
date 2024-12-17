<?php
// Include the database connection
include('../../db/config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    
    // Handle file upload for image
    $image_path = '';

    // Image upload handling
    if (!empty($_FILES['image']['name'])) {
        // Use an absolute path for the image upload directory (relative to the web root)
        $target_image_dir = '../../../uploads/';
        $image_filename = uniqid() . '_' . basename($_FILES['image']['name']);
        // Update to use an absolute path in the URL context
        $image_path = '../../uploads/' . $image_filename;  // Absolute URL relative to the root
        
        // Create directory if it doesn't exist
        if (!is_dir($target_image_dir)) {
            mkdir($target_image_dir, 0755, true);
        }

        // Move uploaded image
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_image_dir . $image_filename)) {
            $errors[] = "Failed to upload image.";
        }
    }

    // Input validation
    $errors = [];

    // Validate name
    if (empty($name)) {
        $errors[] = "Department name is required.";
    } elseif (strlen($name) > 255) {
        $errors[] = "Name cannot exceed 255 characters.";
    }

    // Validate description
    if (empty($description)) {
        $errors[] = "Department description is required.";
    }

    // Check if department name already exists
    $check_query = "SELECT name FROM departments WHERE name = '$name'";
    $result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "A department with this name already exists.";
    }

    // If no errors, proceed with department creation
    if (empty($errors)) {
        // Prepare the SQL query
        $query = "INSERT INTO departments (name, description" . 
                 (!empty($image_path) ? ", image" : "") . 
                 ") VALUES ('$name', '$description'" . 
                 (!empty($image_path) ? ", '$image_path'" : "") . 
                 ")";
        
        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage departments page after successful insertion
            header("Location: ../../view/admin/manage_departments.php?success=1");
            exit();
        } else {
            // Error message if the insertion fails
            header("Location: ../../view/admin/manage_departments.php?error=" . urlencode(mysqli_error($conn)));
            exit();
        }
    } else {
        // If there are validation errors, redirect back with error messages
        $error_string = implode('; ', $errors);
        header("Location: ../../view/admin/manage_departments.php?error=" . urlencode($error_string));
        exit();
    }
}
?>
