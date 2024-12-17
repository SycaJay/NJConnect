<?php
// Include database connection
include('../../db/config.php');

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    // Get the event ID from the URL
    $event_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Check if the event exists before deletion
    $check_query = "SELECT title, image_path, video_path FROM events WHERE event_id = '$event_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If the event is found, fetch its details for file deletion
        $event_details = mysqli_fetch_assoc($check_result);

        // Delete associated files
        // Delete image if it exists
        if (!empty($event_details['image_path']) && file_exists('../../' . $event_details['image_path'])) {
            unlink('../../' . $event_details['image_path']);
        }

        // Delete video if it exists
        if (!empty($event_details['video_path']) && file_exists('../../' . $event_details['video_path'])) {
            unlink('../../' . $event_details['video_path']);
        }

        // Delete the event from the database
        $delete_query = "DELETE FROM events WHERE event_id = '$event_id'";
        if (mysqli_query($conn, $delete_query)) {
            // Optional: Log the deletion or perform additional actions
            // For example, you might want to log which admin deleted the event
            
            // Redirect back to the event management page with success message
            header("Location: ../../view/admin/manage_events.php?success=Event deleted successfully");
            exit();
        } else {
            // Redirect with error message if deletion fails
            header("Location: ../../view/admin/manage_events.php?error=" . urlencode("Error deleting event: " . mysqli_error($conn)));
            exit();
        }
    } else {
        // Redirect with error message if event not found
        header("Location: ../../view/admin/manage_events.php?error=Event not found");
        exit();
    }
} else {
    // Redirect with error message if no ID provided
    header("Location: ../../view/admin/manage_events.php?error=Invalid request");
    exit();
}
?>