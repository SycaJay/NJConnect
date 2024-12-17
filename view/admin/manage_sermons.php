<?php
// Include database connection
include('../../db/config.php');

// Fetch sermons from the database
$query = "SELECT * FROM sermons";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sermons</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/manage_sermons.css">
</head>
<body>

<div class="container">
    <h1>Manage Sermons</h1>
    <button class="btn" id="addSermonBtn">Add Sermon</button>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Sermons Table -->
    <table>
        <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Sermon Type</th>
            <th>Media</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($sermon = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($sermon['title']); ?></td>
                <td><?php echo htmlspecialchars(substr($sermon['description'], 0, 100) . '...'); ?></td>
                <td><?php echo htmlspecialchars($sermon['sermon_type']); ?></td>
                <td>
                    <?php if (!empty($sermon['media'])): ?>
                        <?php echo $sermon['sermon_type'] === 'audio' ? 'Audio' : 'Video'; ?> available
                    <?php else: ?>
                        No Media
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($sermon['image'])): ?>
                        <img src="../../uploads/<?php echo htmlspecialchars($sermon['image']); ?>" 
                             alt="Sermon Image" style="max-width: 100px; max-height: 100px;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($sermon['created_at']); ?></td>
                <td>
                    <button class="btn editBtn" 
                        data-id="<?php echo $sermon['sermon_id']; ?>" 
                        data-title="<?php echo htmlspecialchars($sermon['title']); ?>" 
                        data-description="<?php echo htmlspecialchars($sermon['description']); ?>" 
                        data-sermon-type="<?php echo $sermon['sermon_type']; ?>" 
                        data-media="<?php echo htmlspecialchars($sermon['media']); ?>" 
                        data-image="<?php echo htmlspecialchars($sermon['image'] ?? ''); ?>">Edit</button>
                    <a href="../../actions/sermons/delete_sermon.php?id=<?php echo $sermon['sermon_id']; ?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Modal for Adding a Sermon -->
    <div id="addSermonModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Add a New Sermon</h2>
            <form action="../../actions/sermons/add_sermon.php" method="POST" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea>

                <label for="sermonType">Sermon Type:</label>
                <select name="sermon_type" id="sermonType" required>
                    <option value="audio">Audio</option>
                    <option value="video">Video</option>
                </select>

                <label for="media">Media File:</label>
                <input type="file" name="media" id="media" accept="audio/*,video/*" required>
                
                <label for="image">Sermon Image (Optional):</label>
                <input type="file" name="image" id="image" accept="image/*">
                <img id="imagePreview" class="image-preview" style="display:none;">

                <button type="submit" class="btn">Add Sermon</button>
            </form>
        </div>
    </div>

    <!-- Modal for Editing a Sermon -->
    <div id="editSermonModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Edit Sermon</h2>
            <form id="editSermonForm" action="../../actions/sermons/edit_sermon.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="sermon_id" id="editSermonId">
                <input type="hidden" name="existing_media" id="editExistingMedia">
                <input type="hidden" name="existing_image" id="editExistingImage">

                <label for="editTitle">Title:</label>
                <input type="text" name="title" id="editTitle" required>

                <label for="editDescription">Description:</label>
                <textarea name="description" id="editDescription" rows="4" required></textarea>

                <label for="editSermonType">Sermon Type:</label>
                <select name="sermon_type" id="editSermonType" required>
                    <option value="audio">Audio</option>
                    <option value="video">Video</option>
                </select>

                <label for="editMedia">Media File (Optional):</label>
                <input type="file" name="media" id="editMedia" accept="audio/*,video/*">
                <p id="currentMediaStatus"></p>

                <label for="editImage">Sermon Image (Optional):</label>
                <input type="file" name="image" id="editImage" accept="image/*">
                <img id="editImagePreview" class="image-preview">

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Javascript for Modal Popups and Form Handling -->
<script>
    // Get the modal elements
    var addSermonModal = document.getElementById("addSermonModal");
    var editSermonModal = document.getElementById("editSermonModal");

    // Get the buttons that open the modals
    var addSermonBtn = document.getElementById("addSermonBtn");
    var editBtns = document.querySelectorAll(".editBtn");

    // Get the <span> elements that close the modals
    var closeBtns = document.getElementsByClassName("close");

    // Image preview for add sermon
    document.getElementById('image').addEventListener('change', function(e) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var imgPreview = document.getElementById('imagePreview');
            imgPreview.src = event.target.result;
            imgPreview.style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    });

    // When the user clicks the "Add Sermon" button, open the "Add Sermon" modal
    addSermonBtn.onclick = function() {
        addSermonModal.style.display = "block";
    }

    // When the user clicks the "Edit" button, open the "Edit Sermon" modal
    editBtns.forEach(function(btn) {
        btn.onclick = function() {
            var sermonId = btn.getAttribute("data-id");
            var title = btn.getAttribute("data-title");
            var description = btn.getAttribute("data-description");
            var sermonType = btn.getAttribute("data-sermon-type");
            var media = btn.getAttribute("data-media");
            var image = btn.getAttribute("data-image");

            // Set the values in the edit modal
            document.getElementById("editSermonId").value = sermonId;
            document.getElementById("editTitle").value = title;
            document.getElementById("editDescription").value = description;
            document.getElementById("editSermonType").value = sermonType;
            document.getElementById("editExistingMedia").value = media;
            document.getElementById("editExistingImage").value = image;

            // Set media status
            var currentMediaStatus = document.getElementById("currentMediaStatus");
            currentMediaStatus.textContent = media ? "Current media: " + media : "No current media";

            // Set image preview
            var editImagePreview = document.getElementById("editImagePreview");
            if (image) {
                editImagePreview.src = "../../uploads/" + image;
                editImagePreview.style.display = "block";
            } else {
                editImagePreview.style.display = "none";
            }

            editSermonModal.style.display = "block";
        }
    });

    // When the user clicks on <span> (x), close the modals
    for (var i = 0; i < closeBtns.length; i++) {
        closeBtns[i].onclick = function() {
            addSermonModal.style.display = "none";
            editSermonModal.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == addSermonModal) {
            addSermonModal.style.display = "none";
        }
        if (event.target == editSermonModal) {
            editSermonModal.style.display = "none";
        }
    }

    // Handle form submission for editing
    document.getElementById("editSermonForm").onsubmit = function(event) {
        location.reload();
    }
</script>

</body>
</html>