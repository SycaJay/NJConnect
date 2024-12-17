<?php
// Include database connection
include('../../db/config.php');

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    // Get the devotional ID from the URL
    $devotional_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Check if the devotional exists before deletion
    $check_query = "SELECT title, audio_url, file_url FROM devotionals WHERE devotional_id = '$devotional_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If the devotional is found, fetch its details for file deletion
        $devotional_details = mysqli_fetch_assoc($check_result);

        // Delete associated files
        // Delete audio file if it exists
        if (!empty($devotional_details['audio_url']) && file_exists('../../' . $devotional_details['audio_url'])) {
            unlink('../../' . $devotional_details['audio_url']);
        }

        // Delete document file if it exists
        if (!empty($devotional_details['file_url']) && file_exists('../../' . $devotional_details['file_url'])) {
            unlink('../../' . $devotional_details['file_url']);
        }

        // Delete the devotional from the database
        $delete_query = "DELETE FROM devotionals WHERE devotional_id = '$devotional_id'";
        if (mysqli_query($conn, $delete_query)) {
            // Redirect back to the devotional management page with success message
            header("Location: ../../view/admin/manage_devotionals.php?success=Devotional deleted successfully");
            exit();
        } else {
            // Redirect with error message if deletion fails
            header("Location: ../../view/admin/manage_devotionals.php?error=" . urlencode("Error deleting devotional: " . mysqli_error($conn)));
            exit();
        }
    } else {
        // Redirect with error message if devotional not found
        header("Location: ../../view/admin/manage_devotionals.php?error=Devotional not found");
        exit();
    }
} else {
    // Redirect with error message if no ID provided
    header("Location: ../../view/admin/manage_devotionals.php?error=Invalid request");
    exit();
}
?>