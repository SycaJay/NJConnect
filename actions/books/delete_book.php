<?php
// Include database connection
include('../../db/config.php');

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    // Get the book ID from the URL
    $book_id = $_GET['id'];

    // Fetch the image path of the book from the database before deletion
    $query = "SELECT image_path FROM books WHERE book_id = '$book_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // If the book is found, get the image path
        $row = mysqli_fetch_assoc($result);
        $image_path = $row['image_path'];

        // Delete the book from the database
        $delete_query = "DELETE FROM books WHERE book_id = '$book_id'";
        if (mysqli_query($conn, $delete_query)) {
            // If deletion is successful, delete the image file (if it exists)
            $image_path_full = "../../uploads/" . $image_path;
            if (file_exists($image_path_full) && is_file($image_path_full)) {
                unlink($image_path_full);  // Delete the image file from the server
            }

            // Redirect back to the book management page
            header("Location: ../../view/admin/manage_books.php");
            exit();
        } else {
            echo "Error deleting book: " . mysqli_error($conn);
        }
    } else {
        echo "Book not found.";
    }
} else {
    echo "Invalid request.";
}
?>
