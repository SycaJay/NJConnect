<?php
// Include database connection
include('../../db/config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the sermon details from the form
    $sermon_id = mysqli_real_escape_string($conn, $_POST['sermon_id']);
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $sermon_type = mysqli_real_escape_string($conn, $_POST['sermon_type']);
    
    // Initialize errors array
    $errors = [];

    // Validate sermon type
    $allowed_sermon_types = ['audio', 'video'];
    if (!in_array($sermon_type, $allowed_sermon_types)) {
        $errors[] = "Invalid sermon type.";
    }

    // Validate title
    if (empty($title)) {
        $errors[] = "Title is required.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Title cannot exceed 255 characters.";
    }

    // Validate description
    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    // Handle media upload if a new media file is provided
    $media_path = null;
    if (!empty($_FILES['media']['name'])) {
        $allowed_audio_types = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
        $allowed_video_types = ['video/mp4', 'video/mpeg', 'video/quicktime'];
        $max_size = 50 * 1024 * 1024; // 50MB

        $file_type = $_FILES['media']['type'];
        $is_valid_media = 
            ($sermon_type === 'audio' && in_array($file_type, $allowed_audio_types)) ||
            ($sermon_type === 'video' && in_array($file_type, $allowed_video_types));

        if (!$is_valid_media) {
            $errors[] = "Invalid media type for the selected sermon type.";
        } elseif ($_FILES['media']['size'] > $max_size) {
            $errors[] = "Media file size must be less than 50MB.";
        } else {
            $upload_dir = '../../uploads/sermons/media/';
            $file_extension = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
            $media_path = 'uploads/sermons/media/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['media']['tmp_name'], $upload_dir . basename($media_path))) {
                $errors[] = "Failed to upload media file.";
            }
        }
    }

    // Handle image upload if a new image is provided
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_image_types)) {
            $errors[] = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors[] = "Image size must be less than 5MB.";
        } else {
            $upload_dir = '../../uploads/sermons/images/';
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_path = 'uploads/sermons/images/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . basename($image_path))) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // If no errors, proceed with sermon update
    if (empty($errors)) {
        // Start building the query
        $query = "UPDATE sermons SET 
                    title = '$title', 
                    description = '$description', 
                    sermon_type = '$sermon_type'";

        // Add media path to query if new media was uploaded
        if ($media_path) {
            // First, get the old media path to delete the file
            $old_media_query = "SELECT media FROM sermons WHERE sermon_id = '$sermon_id'";
            $old_media_result = mysqli_query($conn, $old_media_query);
            $old_media_row = mysqli_fetch_assoc($old_media_result);
            
            if ($old_media_row && file_exists('../../' . $old_media_row['media'])) {
                unlink('../../' . $old_media_row['media']);
            }
            
            $query .= ", media = '$media_path'";
        }

        // Add image path to query if new image was uploaded
        if ($image_path) {
            // First, get the old image path to delete the file
            $old_image_query = "SELECT image FROM sermons WHERE sermon_id = '$sermon_id'";
            $old_image_result = mysqli_query($conn, $old_image_query);
            $old_image_row = mysqli_fetch_assoc($old_image_result);
            
            if ($old_image_row && file_exists('../../' . $old_image_row['image'])) {
                unlink('../../' . $old_image_row['image']);
            }
            
            $query .= ", image = '$image_path'";
        }

        // Complete the query
        $query .= " WHERE sermon_id = '$sermon_id'";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage sermons page after successful update
            header("Location: ../../view/admin/manage_sermons.php?success=1");
            exit();
        } else {
            // Error message if the update fails
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