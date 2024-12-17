<?php
// Include database connection
include('../../db/config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the department details from the form
    $department_id = mysqli_real_escape_string($conn, $_POST['department_id']);
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    
    // Initialize errors array
    $errors = [];

    // Validate name
    if (empty($name)) {
        $errors[] = "Department name is required.";
    } elseif (strlen($name) > 255) {
        $errors[] = "Department name cannot exceed 255 characters.";
    }

    // Validate description
    if (!empty($description) && strlen($description) > 65535) { // MySQL text field limit
        $errors[] = "Description is too long.";
    }

    // Handle image upload if a new image is provided
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors[] = "Image size must be less than 5MB.";
        } else {
            $upload_dir = '../../uploads/departments/';
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_path = 'uploads/departments/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . basename($image_path))) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // If no errors, proceed with department update
    if (empty($errors)) {
        // Start building the query
        $query = "UPDATE departments SET 
                    name = '$name'";
        
        // Add description if provided
        if (isset($description)) {
            $query .= ", description = '$description'";
        }

        // Add image path to query if new image was uploaded
        if ($image_path) {
            // First, get the old image path to delete the file
            $old_image_query = "SELECT image FROM departments WHERE department_id = '$department_id'";
            $old_image_result = mysqli_query($conn, $old_image_query);
            $old_image_row = mysqli_fetch_assoc($old_image_result);
            
            if ($old_image_row && !empty($old_image_row['image']) && file_exists('../../' . $old_image_row['image'])) {
                unlink('../../' . $old_image_row['image']);
            }
            
            $query .= ", image = '$image_path'";
        }

        // Complete the query
        $query .= " WHERE department_id = '$department_id'";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage departments page after successful update
            header("Location: ../../view/admin/manage_departments.php?success=1");
            exit();
        } else {
            // Error message if the update fails
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