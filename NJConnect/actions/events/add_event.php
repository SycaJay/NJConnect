<?php
// Include the database connection
include('../../db/config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $event_date = mysqli_real_escape_string($conn, trim($_POST['event_date']));
    
    // Handle file uploads for image and video
    $image_path = '';
    $video_path = '';

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

    // Video upload handling (optional)
    if (!empty($_FILES['video']['name'])) {
        // Use an absolute path for the video upload directory (relative to the web root)
        $target_video_dir = '../../../uploads/';
        $video_filename = uniqid() . '_' . basename($_FILES['video']['name']);
        // Update to use an absolute path in the URL context
        $video_path = '../../uploads/' . $video_filename;  // Absolute URL relative to the root
        
        // Create directory if it doesn't exist
        if (!is_dir($target_video_dir)) {
            mkdir($target_video_dir, 0755, true);
        }

        // Move uploaded video
        if (!move_uploaded_file($_FILES['video']['tmp_name'], $target_video_dir . $video_filename)) {
            $errors[] = "Failed to upload video.";
        }
    }

    // Input validation
    $errors = [];

    // Validate title
    if (empty($title)) {
        $errors[] = "Event title is required.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Title cannot exceed 255 characters.";
    }

    // Validate description
    if (empty($description)) {
        $errors[] = "Event description is required.";
    }

    // Validate event date
    if (empty($event_date)) {
        $errors[] = "Event date is required.";
    } else {
        // Validate date format
        $date_obj = date_create($event_date);
        if (!$date_obj || date_format($date_obj, 'Y-m-d') !== $event_date) {
            $errors[] = "Invalid date format. Use YYYY-MM-DD.";
        }
    }

    // Validate image upload
    if (empty($image_path)) {
        $errors[] = "Event image is required.";
    }

    // If no errors, proceed with event creation
    if (empty($errors)) {
        // Prepare the SQL query (handle optional video path)
        $query = "INSERT INTO events (title, description, event_date, image_path, video_path) 
                  VALUES ('$title', '$description', '$event_date', '$image_path', " . 
                  (!empty($video_path) ? "'$video_path'" : "NULL") . 
                  ")";
        
        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage events page after successful insertion
            header("Location: ../../view/admin/manage_events.php?success=1");
            exit();
        } else {
            // Error message if the insertion fails
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
