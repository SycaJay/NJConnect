<?php
// Include database connection
include('../../db/config.php');

// Fetch books from the database
$query = "SELECT * FROM books";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/manage_books.css">
</head>
<body>

<div class="container">
    <h1>Manage Books</h1>
    <button class="btn" id="addBookBtn">Add Book</button>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Books Table -->
    <table>
        <thead>
        <tr>
            <th>Title</th>
            <th>Price</th>
            <th>Image</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($book = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($book['title']); ?></td>
                <td><?php echo number_format($book['price'], 2); ?></td>
                <td><img src="uploads/<?php echo $book['image_path']; ?>" alt="<?php echo $book['title']; ?>" width="50"></td>
                <td><?php echo htmlspecialchars($book['description']); ?></td>
                <td>
                    <button class="btn editBtn" data-id="<?php echo $book['book_id']; ?>" data-title="<?php echo $book['title']; ?>" data-price="<?php echo $book['price']; ?>" data-description="<?php echo $book['description']; ?>" data-image="<?php echo $book['image_path']; ?>">Edit</button>
                    <a href="../../actions/books/delete_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Modal for Adding a Book -->
    <div id="addBookModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Add a New Book</h2>
            <form action="../../actions/books/add_book.php" method="POST" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>

                <label for="price">Price:</label>
                <input type="number" name="price" id="price" step="0.01" required>

                <label for="image">Image:</label>
                <input type="file" name="image" id="image" accept="image/*" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4"></textarea>

                <button type="submit" class="btn">Add Book</button>
            </form>
        </div>
    </div>

    <!-- Modal for Editing a Book -->
    <div id="editBookModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Edit Book</h2>
            <form id="editBookForm" action="../../actions/books/edit_book.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="book_id" id="editBookId">

                <label for="editTitle">Title:</label>
                <input type="text" name="title" id="editTitle" required>

                <label for="editPrice">Price:</label>
                <input type="number" name="price" id="editPrice" step="0.01" required>

                <label for="editImage">Image:</label>
                <input type="file" name="image" id="editImage" accept="image/*">

                <label for="editDescription">Description:</label>
                <textarea name="description" id="editDescription" rows="4"></textarea>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Javascript for Modal Popups and Form Handling -->
<script>
    // Get the modal elements
    var addBookModal = document.getElementById("addBookModal");
    var editBookModal = document.getElementById("editBookModal");

    // Get the buttons that open the modals
    var addBookBtn = document.getElementById("addBookBtn");
    var editBtns = document.querySelectorAll(".editBtn");

    // Get the <span> elements that close the modals
    var closeBtns = document.getElementsByClassName("close");

    // When the user clicks the "Add Book" button, open the "Add Book" modal
    addBookBtn.onclick = function() {
        addBookModal.style.display = "block";
    }

    // When the user clicks the "Edit" button, open the "Edit Book" modal
    editBtns.forEach(function(btn) {
        btn.onclick = function() {
            var bookId = btn.getAttribute("data-id");
            var title = btn.getAttribute("data-title");
            var price = btn.getAttribute("data-price");
            var description = btn.getAttribute("data-description");
            var image = btn.getAttribute("data-image");

            // Set the values in the edit modal
            document.getElementById("editBookId").value = bookId;
            document.getElementById("editTitle").value = title;
            document.getElementById("editPrice").value = price;
            document.getElementById("editDescription").value = description;
            document.getElementById("editImage").value = ""; // Reset image field

            editBookModal.style.display = "block";
        }
    });

    // When the user clicks on <span> (x), close the modals
    for (var i = 0; i < closeBtns.length; i++) {
        closeBtns[i].onclick = function() {
            addBookModal.style.display = "none";
            editBookModal.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == addBookModal) {
            addBookModal.style.display = "none";
        }
        if (event.target == editBookModal) {
            editBookModal.style.display = "none";
        }
    }

    // Handle form submission for editing
    document.getElementById("editBookForm").onsubmit = function(event) {
        // event.preventDefault();
        // AJAX to send data to backend (this will be implemented later)
        // For now, we reload the page after submission
        location.reload();
    }
</script>

</body>
</html>
