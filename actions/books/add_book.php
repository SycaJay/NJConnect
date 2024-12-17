<?php
// Include the database connection
include('../../db/config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $price = mysqli_real_escape_string($conn, trim($_POST['price']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    
    // Handle image upload
    $image_path = '';

    // Check if an image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Get the image details
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_error = $_FILES['image']['error'];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);

        // Allowed file extensions
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate the image file type
        if (in_array(strtolower($image_ext), $allowed_extensions)) {
            // Generate a unique name for the image
            $image_new_name = uniqid('', true) . '.' . $image_ext;
            $target_image_dir = '../../../uploads/';
            $image_path = '../../uploads/' . $image_new_name;  // Absolute URL relative to the root

            // Create directory if it doesn't exist
            if (!is_dir($target_image_dir)) {
                mkdir($target_image_dir, 0755, true);
            }

            // Move the uploaded image
            if (move_uploaded_file($image_tmp_name, $target_image_dir . $image_new_name)) {
                // Image upload successful
            } else {
                $errors[] = "Failed to upload image.";
            }
        } else {
            $errors[] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    } else {
        $errors[] = "Please upload an image.";
    }

    // Input validation
    $errors = [];

    // Validate title
    if (empty($title)) {
        $errors[] = "Title is required.";
    }

    // Validate price
    if (empty($price)) {
        $errors[] = "Price is required.";
    }

    // Validate description
    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    // Validate image upload
    if (empty($image_path)) {
        $errors[] = "Event image is required.";
    }

    // If no errors, proceed with inserting into the database
    if (empty($errors)) {
        // Prepare the SQL query
        $query = "INSERT INTO books (title, price, image_path, description) 
                  VALUES ('$title', '$price', '$image_path', '$description')";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage books page after successful insertion
            header("Location: ../../view/admin/manage_books.php");
            exit();
        } else {
            // Error message if the insertion fails
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // If there are validation errors, display them
        $error_string = implode('; ', $errors);
        echo "Errors: " . $error_string;
    }
}
?>
