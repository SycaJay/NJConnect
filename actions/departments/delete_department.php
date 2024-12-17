<?php
// Include database connection
include('../../db/config.php');

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    // Get the department ID from the URL
    $department_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Check if the department exists before deletion
    $check_query = "SELECT name, image FROM departments WHERE department_id = '$department_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If the department is found, fetch its details for file deletion
        $department_details = mysqli_fetch_assoc($check_result);

        // Delete associated image file if it exists
        if (!empty($department_details['image']) && file_exists('../../' . $department_details['image'])) {
            unlink('../../' . $department_details['image']);
        }

        // Delete the department from the database
        $delete_query = "DELETE FROM departments WHERE department_id = '$department_id'";
        if (mysqli_query($conn, $delete_query)) {
            // Optional: Log the deletion or perform additional actions
            // For example, you might want to log which admin deleted the department
            
            // Redirect back to the department management page with success message
            header("Location: ../../view/admin/manage_departments.php?success=Department deleted successfully");
            exit();
        } else {
            // Redirect with error message if deletion fails
            header("Location: ../../view/admin/manage_departments.php?error=" . urlencode("Error deleting department: " . mysqli_error($conn)));
            exit();
        }
    } else {
        // Redirect with error message if department not found
        header("Location: ../../view/admin/manage_departments.php?error=Department not found");
        exit();
    }
} else {
    // Redirect with error message if no ID provided
    header("Location: ../../view/admin/manage_departments.php?error=Invalid request");
    exit();
}
?>