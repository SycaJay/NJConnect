<?php
// Include database connection
include('../../db/config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the book details from the form
    $book_id = $_POST['book_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Process the uploaded image (if there's a new one)
    if (!empty($_FILES['image']['name'])) {
        // Handle image upload
        $target_dir = "../../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if the file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }
        
        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            die("Sorry, there was an error uploading your file.");
        }
        
        // Get the image path (filename) for storing in the database
        $image_path = basename($_FILES["image"]["name"]);
    } else {
        // If no new image, keep the old image (fetch it from the database)
        $query = "SELECT image_path FROM books WHERE book_id = '$book_id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $image_path = $row['image_path'];
    }

    // Update the book details in the database
    $query = "UPDATE books SET
                title = '$title',
                price = '$price',
                description = '$description',
                image_path = '$image_path'
              WHERE book_id = '$book_id'";

    if (mysqli_query($conn, $query)) {
        // Redirect back to the book management page (or wherever needed)
        header("Location: ../../view/admin/manage_books.php");
        exit();
    } else {
        // If there's an error updating the book, display the error message
        echo "Error updating book: " . mysqli_error($conn);
    }
}

?>
