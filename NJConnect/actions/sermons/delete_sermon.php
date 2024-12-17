<?php
// Include database connection
include('../../db/config.php');

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    // Get the sermon ID from the URL
    $sermon_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Check if the sermon exists before deletion
    $check_query = "SELECT title, media, image FROM sermons WHERE sermon_id = '$sermon_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If the sermon is found, fetch its details for file deletion
        $sermon_details = mysqli_fetch_assoc($check_result);

        // Delete associated files
        // Delete media file if it exists
        if (!empty($sermon_details['media']) && file_exists('../../' . $sermon_details['media'])) {
            unlink('../../' . $sermon_details['media']);
        }

        // Delete image if it exists
        if (!empty($sermon_details['image']) && file_exists('../../' . $sermon_details['image'])) {
            unlink('../../' . $sermon_details['image']);
        }

        // Delete the sermon from the database
        $delete_query = "DELETE FROM sermons WHERE sermon_id = '$sermon_id'";
        if (mysqli_query($conn, $delete_query)) {
            // Redirect back to the sermon management page with success message
            header("Location: ../../view/admin/manage_sermons.php?success=Sermon deleted successfully");
            exit();
        } else {
            // Redirect with error message if deletion fails
            header("Location: ../../view/admin/manage_sermons.php?error=" . urlencode("Error deleting sermon: " . mysqli_error($conn)));
            exit();
        }
    } else {
        // Redirect with error message if sermon not found
        header("Location: ../../view/admin/manage_sermons.php?error=Sermon not found");
        exit();
    }
} else {
    // Redirect with error message if no ID provided
    header("Location: ../../view/admin/manage_sermons.php?error=Invalid request");
    exit();
}
?>