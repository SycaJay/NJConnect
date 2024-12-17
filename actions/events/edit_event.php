<?php
// Include database connection
include('../../db/config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the event details from the form
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
    
    // Initialize errors array
    $errors = [];

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

    // Validate event date
    if (empty($event_date)) {
        $errors[] = "Event date is required.";
    } elseif (!strtotime($event_date)) {
        $errors[] = "Invalid event date format.";
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
            $upload_dir = '../../uploads/events/images/';
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_path = 'uploads/events/images/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . basename($image_path))) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // Handle video upload if a new video is provided
    $video_path = null;
    if (!empty($_FILES['video']['name'])) {
        $allowed_video_types = ['video/mp4', 'video/mpeg', 'video/quicktime'];
        $max_video_size = 50 * 1024 * 1024; // 50MB

        if (!in_array($_FILES['video']['type'], $allowed_video_types)) {
            $errors[] = "Invalid video type. Only MP4, MPEG, and QuickTime are allowed.";
        } elseif ($_FILES['video']['size'] > $max_video_size) {
            $errors[] = "Video size must be less than 50MB.";
        } else {
            $upload_dir = '../../uploads/events/videos/';
            $file_extension = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
            $video_path = 'uploads/events/videos/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['video']['tmp_name'], $upload_dir . basename($video_path))) {
                $errors[] = "Failed to upload video.";
            }
        }
    }

    // If no errors, proceed with event update
    if (empty($errors)) {
        // Start building the query
        $query = "UPDATE events SET 
                    title = '$title', 
                    description = '$description', 
                    event_date = '$event_date'";

        // Add image path to query if new image was uploaded
        if ($image_path) {
            // First, get the old image path to delete the file
            $old_image_query = "SELECT image_path FROM events WHERE event_id = '$event_id'";
            $old_image_result = mysqli_query($conn, $old_image_query);
            $old_image_row = mysqli_fetch_assoc($old_image_result);
            
            if ($old_image_row && file_exists('../../' . $old_image_row['image_path'])) {
                unlink('../../' . $old_image_row['image_path']);
            }
            
            $query .= ", image_path = '$image_path'";
        }

        // Add video path to query if new video was uploaded
        if ($video_path) {
            // First, get the old video path to delete the file
            $old_video_query = "SELECT video_path FROM events WHERE event_id = '$event_id'";
            $old_video_result = mysqli_query($conn, $old_video_query);
            $old_video_row = mysqli_fetch_assoc($old_video_result);
            
            if ($old_video_row && file_exists('../../' . $old_video_row['video_path'])) {
                unlink('../../' . $old_video_row['video_path']);
            }
            
            $query .= ", video_path = '$video_path'";
        }

        // Complete the query
        $query .= " WHERE event_id = '$event_id'";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage events page after successful update
            header("Location: ../../view/admin/manage_events.php?success=1");
            exit();
        } else {
            // Error message if the update fails
            header("Location: ../../view/admin/manage_events.php?error=" . urlencode(mysqli_error($conn)));
            exit();
        }
    } else {
        // If there are validation errors, redirect back with error messages
        $error_string = implode('; ', $errors);
        header("Location: ../../view/admin/manage_events.php?error=" . urlencode($error_string));
        exit();
    }
}
?>