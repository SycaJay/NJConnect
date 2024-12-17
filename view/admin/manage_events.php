<?php
// Include database connection
include('../../db/config.php');

// Fetch events from the database
$query = "SELECT * FROM events";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/manage_events.css">
</head>
<body>

<div class="container">
    <h1>Manage Events</h1>
    <button class="btn" id="addEventBtn">Add Event</button>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Events Table -->
    <table>
        <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Event Date</th>
            <th>Image</th>
            <th>Video</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($event = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($event['title']); ?></td>
                <td><?php echo htmlspecialchars(substr($event['description'], 0, 100) . '...'); ?></td>
                <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                <td>
                    <?php if (!empty($event['image_path'])): ?>
                        <img src="../../uploads/<?php echo htmlspecialchars($event['image_path']); ?>" 
                             alt="Event Image" style="max-width: 100px; max-height: 100px;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo !empty($event['video_path']) ? 'Available' : 'No Video'; ?></td>
                <td><?php echo htmlspecialchars($event['created_at']); ?></td>
                <td><?php echo htmlspecialchars($event['updated_at']); ?></td>
                <td>
                    <button class="btn editBtn" 
                        data-id="<?php echo $event['event_id']; ?>" 
                        data-title="<?php echo htmlspecialchars($event['title']); ?>" 
                        data-description="<?php echo htmlspecialchars($event['description']); ?>" 
                        data-event-date="<?php echo $event['event_date']; ?>" 
                        data-image-path="<?php echo htmlspecialchars($event['image_path']); ?>" 
                        data-video-path="<?php echo htmlspecialchars($event['video_path'] ?? ''); ?>">Edit</button>
                    <a href="../../actions/events/delete_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Modal for Adding an Event -->
    <div id="addEventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Add a New Event</h2>
            <form action="../../actions/events/add_event.php" method="POST" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea>

                <label for="eventDate">Event Date:</label>
                <input type="date" name="event_date" id="eventDate" required>

                <label for="image">Event Image:</label>
                <input type="file" name="image" id="image" accept="image/*" required>
                <img id="imagePreview" class="image-preview" style="display:none;">

                <label for="video">Video (Optional):</label>
                <input type="file" name="video" id="video" accept="video/*">

                <button type="submit" class="btn">Add Event</button>
            </form>
        </div>
    </div>

    <!-- Modal for Editing an Event -->
    <div id="editEventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Edit Event</h2>
            <form id="editEventForm" action="../../actions/events/edit_event.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="event_id" id="editEventId">
                <input type="hidden" name="existing_image" id="editExistingImage">

                <label for="editTitle">Title:</label>
                <input type="text" name="title" id="editTitle" required>

                <label for="editDescription">Description:</label>
                <textarea name="description" id="editDescription" rows="4" required></textarea>

                <label for="editEventDate">Event Date:</label>
                <input type="date" name="event_date" id="editEventDate" required>

                <label for="editImage">Event Image (Optional):</label>
                <input type="file" name="image" id="editImage" accept="image/*">
                <img id="editImagePreview" class="image-preview">

                <label for="editVideo">Video (Optional):</label>
                <input type="file" name="video" id="editVideo" accept="video/*">
                <p id="currentVideoStatus"></p>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Javascript for Modal Popups and Form Handling -->
<script>
    // Get the modal elements
    var addEventModal = document.getElementById("addEventModal");
    var editEventModal = document.getElementById("editEventModal");

    // Get the buttons that open the modals
    var addEventBtn = document.getElementById("addEventBtn");
    var editBtns = document.querySelectorAll(".editBtn");

    // Get the <span> elements that close the modals
    var closeBtns = document.getElementsByClassName("close");

    // Image preview for add event
    document.getElementById('image').addEventListener('change', function(e) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var imgPreview = document.getElementById('imagePreview');
            imgPreview.src = event.target.result;
            imgPreview.style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    });

    // When the user clicks the "Add Event" button, open the "Add Event" modal
    addEventBtn.onclick = function() {
        addEventModal.style.display = "block";
    }

    // When the user clicks the "Edit" button, open the "Edit Event" modal
    editBtns.forEach(function(btn) {
        btn.onclick = function() {
            var eventId = btn.getAttribute("data-id");
            var title = btn.getAttribute("data-title");
            var description = btn.getAttribute("data-description");
            var eventDate = btn.getAttribute("data-event-date");
            var imagePath = btn.getAttribute("data-image-path");
            var videoPath = btn.getAttribute("data-video-path");

            // Set the values in the edit modal
            document.getElementById("editEventId").value = eventId;
            document.getElementById("editTitle").value = title;
            document.getElementById("editDescription").value = description;
            document.getElementById("editEventDate").value = eventDate;
            document.getElementById("editExistingImage").value = imagePath;

            // Set image preview
            var editImagePreview = document.getElementById("editImagePreview");
            editImagePreview.src = "../../uploads/" + imagePath;
            editImagePreview.style.display = "block";

            // Set video status
            var currentVideoStatus = document.getElementById("currentVideoStatus");
            currentVideoStatus.textContent = videoPath ? "Current video: " + videoPath : "No current video";

            editEventModal.style.display = "block";
        }
    });

    // When the user clicks on <span> (x), close the modals
    for (var i = 0; i < closeBtns.length; i++) {
        closeBtns[i].onclick = function() {
            addEventModal.style.display = "none";
            editEventModal.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == addEventModal) {
            addEventModal.style.display = "none";
        }
        if (event.target == editEventModal) {
            editEventModal.style.display = "none";
        }
    }

    // Handle form submission for editing
    document.getElementById("editEventForm").onsubmit = function(event) {
        // event.preventDefault();
        // AJAX to send data to backend (this will be implemented later)
        // For now, we reload the page after submission
        location.reload();
    }
</script>

</body>
</html>