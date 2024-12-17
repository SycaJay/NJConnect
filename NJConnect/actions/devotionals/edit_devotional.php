<?php
// Include database connection
include('../../db/config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the devotional details from the form
    $devotional_id = mysqli_real_escape_string($conn, $_POST['devotional_id']);
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $type = mysqli_real_escape_string($conn, $_POST['type']);

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

    // Validate type
    if (empty($type) || !in_array($type, ['audio', 'document'])) {
        $errors[] = "Invalid devotional type.";
    }

    // Handle audio file upload
    $audio_path = null;
    if (!empty($_FILES['audio']['name'])) {
        $allowed_audio_types = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav'];
        $max_audio_size = 50 * 1024 * 1024; // 50MB

        if (!in_array($_FILES['audio']['type'], $allowed_audio_types)) {
            $errors[] = "Invalid audio type. Only MP3 and WAV are allowed.";
        } elseif ($_FILES['audio']['size'] > $max_audio_size) {
            $errors[] = "Audio file size must be less than 50MB.";
        } else {
            $upload_dir = '../../uploads/devotionals/audio/';
            $file_extension = pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION);
            $audio_path = 'uploads/devotionals/audio/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['audio']['tmp_name'], $upload_dir . basename($audio_path))) {
                $errors[] = "Failed to upload audio file.";
            }
        }
    }

    // Handle document file upload
    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $allowed_file_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_file_size = 50 * 1024 * 1024; // 50MB

        if (!in_array($_FILES['file']['type'], $allowed_file_types)) {
            $errors[] = "Invalid file type. Only PDF and DOC/DOCX are allowed.";
        } elseif ($_FILES['file']['size'] > $max_file_size) {
            $errors[] = "File size must be less than 50MB.";
        } else {
            $upload_dir = '../../uploads/devotionals/documents/';
            $file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $file_path = 'uploads/devotionals/documents/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . basename($file_path))) {
                $errors[] = "Failed to upload document file.";
            }
        }
    }

    // If no errors, proceed with devotional update
    if (empty($errors)) {
        // Start building the query
        $query = "UPDATE devotionals SET 
                    title = '$title', 
                    description = '$description',
                    type = '$type'";

        // Add audio path to query if new audio was uploaded
        if ($audio_path) {
            // First, get the old audio path to delete the file
            $old_audio_query = "SELECT audio_url FROM devotionals WHERE devotional_id = '$devotional_id'";
            $old_audio_result = mysqli_query($conn, $old_audio_query);
            $old_audio_row = mysqli_fetch_assoc($old_audio_result);
            
            if ($old_audio_row && file_exists('../../' . $old_audio_row['audio_url'])) {
                unlink('../../' . $old_audio_row['audio_url']);
            }
            
            $query .= ", audio_url = '$audio_path'";
        }

        // Add file path to query if new file was uploaded
        if ($file_path) {
            // First, get the old file path to delete the file
            $old_file_query = "SELECT file_url FROM devotionals WHERE devotional_id = '$devotional_id'";
            $old_file_result = mysqli_query($conn, $old_file_query);
            $old_file_row = mysqli_fetch_assoc($old_file_result);
            
            if ($old_file_row && file_exists('../../' . $old_file_row['file_url'])) {
                unlink('../../' . $old_file_row['file_url']);
            }
            
            $query .= ", file_url = '$file_path'";
        }

        // Complete the query
        $query .= " WHERE devotional_id = '$devotional_id'";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage devotionals page after successful update
            header("Location: ../../view/admin/manage_devotionals.php?success=1");
            exit();
        } else {
            // Error message if the update fails
            header("Location: ../../view/admin/manage_devotionals.php?error=" . urlencode(mysqli_error($conn)));
            exit();
        }
    } else {
        // If there are validation errors, redirect back with error messages
        $error_string = implode('; ', $errors);
        header("Location: ../../view/admin/manage_devotionals.php?error=" . urlencode($error_string));
        exit();
    }
}
?>