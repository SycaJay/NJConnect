<?php
// Include the database connection
include('../../db/config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $sermon_type = mysqli_real_escape_string($conn, trim($_POST['sermon_type']));
    
    // Handle file uploads for media and image
    $media_path = '';
    $image_path = '';
    $errors = [];

    // Validate sermon type
    $allowed_sermon_types = ['audio', 'video'];
    if (!in_array($sermon_type, $allowed_sermon_types)) {
        $errors[] = "Invalid sermon type.";
    }

    // Media upload handling
    if (!empty($_FILES['media']['name'])) {
        $target_media_dir = '../../../uploads/';
        $media_filename = uniqid() . '_' . basename($_FILES['media']['name']);
        $media_path = '../../uploads/' . $media_filename;  // Absolute URL relative to the root
        
        // Create directory if it doesn't exist
        if (!is_dir($target_media_dir)) {
            mkdir($target_media_dir, 0755, true);
        }

        // Validate media file type based on sermon type
        $allowed_audio_types = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
        $allowed_video_types = ['video/mp4', 'video/mpeg', 'video/quicktime'];
        $file_type = $_FILES['media']['type'];

        $is_valid_media = 
            ($sermon_type === 'audio' && in_array($file_type, $allowed_audio_types)) ||
            ($sermon_type === 'video' && in_array($file_type, $allowed_video_types));

        if (!$is_valid_media) {
            $errors[] = "Invalid media file type for the selected sermon type.";
        } else {
            // Move uploaded media
            if (!move_uploaded_file($_FILES['media']['tmp_name'], $target_media_dir . $media_filename)) {
                $errors[] = "Failed to upload media.";
            }
        }
    }

    // Image upload handling (optional)
    if (!empty($_FILES['image']['name'])) {
        $target_image_dir = '../../../uploads/';
        $image_filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $image_path = '../../uploads/' . $image_filename;  // Absolute URL relative to the root
        
        // Create directory if it doesn't exist
        if (!is_dir($target_image_dir)) {
            mkdir($target_image_dir, 0755, true);
        }

        // Validate image type
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed_image_types)) {
            $errors[] = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } else {
            // Move uploaded image
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_image_dir . $image_filename)) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // Input validation
    // Validate title
    if (empty($title)) {
        $errors[] = "Sermon title is required.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Title cannot exceed 255 characters.";
    }

    // Validate description
    if (empty($description)) {
        $errors[] = "Sermon description is required.";
    }

    // Validate media upload
    if (empty($media_path)) {
        $errors[] = "Media file is required.";
    }

    // If no errors, proceed with sermon creation
    if (empty($errors)) {
        // Prepare the SQL query 
        $query = "INSERT INTO sermons (title, description, sermon_type, media, image) 
                  VALUES ('$title', '$description', '$sermon_type', '$media_path', " . 
                  (!empty($image_path) ? "'$image_path'" : "NULL") . 
                  ")";
        
        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage sermons page after successful insertion
            header("Location: ../../view/admin/manage_sermons.php?success=1");
            exit();
        } else {
            // Error message if the insertion fails
            header("Location: ../../view/admin/manage_sermons.php?error=" . urlencode(mysqli_error($conn)));
            exit();
        }
    } else {
        // If there are validation errors, redirect back with error messages
        $error_string = implode('; ', $errors);
        header("Location: ../../view/admin/manage_sermons.php?error=" . urlencode($error_string));
        exit();
    }
}
?>
