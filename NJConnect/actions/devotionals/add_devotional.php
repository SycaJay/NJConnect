<?php
// Include the database connection
include('../../db/config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $type = mysqli_real_escape_string($conn, $_POST['type']);

    // Initialize errors array
    $errors = [];

    // Validate title
    if (empty($title)) {
        $errors[] = "Devotional title is required.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Title cannot exceed 255 characters.";
    }

    // Validate description
    if (empty($description)) {
        $errors[] = "Devotional description is required.";
    }

    // Validate type
    if (!in_array($type, ['audio', 'document'])) {
        $errors[] = "Invalid devotional type.";
    }

    // Initialize file paths
    $file_path = null;

    // Audio file upload handling
    if ($type === 'audio' && !empty($_FILES['audioFile']['name'])) {
        $allowed_audio_types = ['audio/mpeg', 'audio/wav', 'audio/mp3', 'audio/x-wav'];
        $max_audio_size = 50 * 1024 * 1024; // 50MB

        if (!in_array($_FILES['audioFile']['type'], $allowed_audio_types)) {
            $errors[] = "Invalid audio type. Only MP3 and WAV are allowed.";
        } elseif ($_FILES['audioFile']['size'] > $max_audio_size) {
            $errors[] = "Audio file size must be less than 50MB.";
        } else {
            $upload_dir = '../../../uploads/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_extension = pathinfo($_FILES['audioFile']['name'], PATHINFO_EXTENSION);
            $file_path = '../../uploads/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['audioFile']['tmp_name'], $upload_dir . basename($file_path))) {
                $errors[] = "Failed to upload audio file.";
            }
        }
    } 
    // Document file upload handling
    elseif ($type === 'document' && !empty($_FILES['documentFile']['name'])) {
        $allowed_doc_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
        $max_doc_size = 20 * 1024 * 1024; // 20MB

        if (!in_array($_FILES['documentFile']['type'], $allowed_doc_types)) {
            $errors[] = "Invalid document type. Only PDF, DOC, DOCX, and TXT are allowed.";
        } elseif ($_FILES['documentFile']['size'] > $max_doc_size) {
            $errors[] = "Document file size must be less than 20MB.";
        } else {
            $upload_dir = '../../../uploads/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_extension = pathinfo($_FILES['documentFile']['name'], PATHINFO_EXTENSION);
            $file_path = '../../uploads/' . uniqid() . '.' . $file_extension;
            
            if (!move_uploaded_file($_FILES['documentFile']['tmp_name'], $upload_dir . basename($file_path))) {
                $errors[] = "Failed to upload document file.";
            }
        }
    } else {
        // Require file for both types
        $errors[] = "A file is required for the devotional.";
    }

    // If no errors, proceed with devotional creation
    if (empty($errors)) {
        // Prepare the SQL query for insertion (handle optional file path)
        $query = "INSERT INTO devotionals (title, description, type, " . 
                 ($type === 'audio' ? 'audio_url' : 'file_url') . 
                 ") VALUES ('$title', '$description', '$type', " . 
                 (!empty($file_path) ? "'$file_path'" : "NULL") . 
                 ")";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Redirect to the manage devotionals page after successful insertion
            header("Location: ../../view/admin/manage_devotionals.php?success=1");
            exit();
        } else {
            // Error message if the insertion fails
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
