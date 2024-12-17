<?php
// Include database connection
include('../../db/config.php');

// Fetch devotionals from the database
$query = "SELECT * FROM devotionals";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Devotionals</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/manage_devotional.css">
</head>
<body>

<div class="container">
    <h1>Manage Devotionals</h1>
    <button class="btn" id="addDevotionalBtn">Add Devotional</button>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Devotionals Table -->
    <table>
        <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Type</th>
            <th>File/Audio</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($devotional = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($devotional['title']); ?></td>
                <td><?php echo htmlspecialchars(substr($devotional['description'], 0, 100) . '...'); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($devotional['type'])); ?></td>
                <td>
                    <?php 
                    if ($devotional['type'] == 'audio') {
                        echo !empty($devotional['audio_url']) ? 'Audio Available' : 'No Audio';
                    } else {
                        echo !empty($devotional['file_url']) ? 'Document Available' : 'No Document';
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($devotional['created_at']); ?></td>
                <td>
                    <button class="btn editBtn" 
                        data-id="<?php echo $devotional['devotional_id']; ?>" 
                        data-title="<?php echo htmlspecialchars($devotional['title']); ?>" 
                        data-description="<?php echo htmlspecialchars($devotional['description']); ?>" 
                        data-type="<?php echo htmlspecialchars($devotional['type']); ?>" 
                        data-audio-url="<?php echo htmlspecialchars($devotional['audio_url'] ?? ''); ?>" 
                        data-file-url="<?php echo htmlspecialchars($devotional['file_url'] ?? ''); ?>">Edit</button>
                    <a href="../../actions/devotionals/delete_devotional.php?id=<?php echo $devotional['devotional_id']; ?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Modal for Adding a Devotional -->
    <div id="addDevotionalModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Add a New Devotional</h2>
            <form action="../../actions/devotionals/add_devotional.php" method="POST" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea>

                <label for="type">Type:</label>
                <select name="type" id="type" required>
                    <option value="audio">Audio</option>
                    <option value="document">Document</option>
                </select>

                <div id="audioUploadSection">
                    <label for="audioFile">Audio File:</label>
                    <input type="file" name="audioFile" id="audioFile" accept="audio/*">
                </div>

                <div id="documentUploadSection" style="display:none;">
                    <label for="documentFile">Document File:</label>
                    <input type="file" name="documentFile" id="documentFile" accept=".pdf,.doc,.docx,.txt">
                </div>

                <button type="submit" class="btn">Add Devotional</button>
            </form>
        </div>
    </div>

    <!-- Modal for Editing a Devotional -->
    <div id="editDevotionalModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Edit Devotional</h2>
            <form id="editDevotionalForm" action="../../actions/devotionals/edit_devotional.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="devotional_id" id="editDevotionalId">

                <label for="editTitle">Title:</label>
                <input type="text" name="title" id="editTitle" required>

                <label for="editDescription">Description:</label>
                <textarea name="description" id="editDescription" rows="4" required></textarea>

                <label for="editType">Type:</label>
                <select name="type" id="editType" required>
                    <option value="audio">Audio</option>
                    <option value="document">Document</option>
                </select>

                <div id="editAudioUploadSection">
                    <label for="editAudioFile">Audio File (Optional):</label>
                    <input type="file" name="audioFile" id="editAudioFile" accept="audio/*">
                    <p id="currentAudioStatus"></p>
                </div>

                <div id="editDocumentUploadSection" style="display:none;">
                    <label for="editDocumentFile">Document File (Optional):</label>
                    <input type="file" name="documentFile" id="editDocumentFile" accept=".pdf,.doc,.docx,.txt">
                    <p id="currentDocumentStatus"></p>
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Javascript for Modal Popups and Form Handling -->
<script>
    // Get the modal elements
    var addDevotionalModal = document.getElementById("addDevotionalModal");
    var editDevotionalModal = document.getElementById("editDevotionalModal");

    // Get the buttons that open the modals
    var addDevotionalBtn = document.getElementById("addDevotionalBtn");
    var editBtns = document.querySelectorAll(".editBtn");

    // Get the <span> elements that close the modals
    var closeBtns = document.getElementsByClassName("close");

    // Type selection handling for Add Modal
    var typeSelect = document.getElementById("type");
    var audioUploadSection = document.getElementById("audioUploadSection");
    var documentUploadSection = document.getElementById("documentUploadSection");

    typeSelect.addEventListener('change', function() {
        if (this.value === 'audio') {
            audioUploadSection.style.display = 'block';
            documentUploadSection.style.display = 'none';
        } else {
            audioUploadSection.style.display = 'none';
            documentUploadSection.style.display = 'block';
        }
    });

    // Type selection handling for Edit Modal
    var editTypeSelect = document.getElementById("editType");
    var editAudioUploadSection = document.getElementById("editAudioUploadSection");
    var editDocumentUploadSection = document.getElementById("editDocumentUploadSection");

    editTypeSelect.addEventListener('change', function() {
        if (this.value === 'audio') {
            editAudioUploadSection.style.display = 'block';
            editDocumentUploadSection.style.display = 'none';
        } else {
            editAudioUploadSection.style.display = 'none';
            editDocumentUploadSection.style.display = 'block';
        }
    });

    // When the user clicks the "Add Devotional" button, open the "Add Devotional" modal
    addDevotionalBtn.onclick = function() {
        addDevotionalModal.style.display = "block";
    }

    // When the user clicks the "Edit" button, open the "Edit Devotional" modal
    editBtns.forEach(function(btn) {
        btn.onclick = function() {
            var devotionalId = btn.getAttribute("data-id");
            var title = btn.getAttribute("data-title");
            var description = btn.getAttribute("data-description");
            var type = btn.getAttribute("data-type");
            var audioUrl = btn.getAttribute("data-audio-url");
            var fileUrl = btn.getAttribute("data-file-url");

            // Set the values in the edit modal
            document.getElementById("editDevotionalId").value = devotionalId;
            document.getElementById("editTitle").value = title;
            document.getElementById("editDescription").value = description;
            document.getElementById("editType").value = type;

            // Show/hide upload sections based on type
            if (type === 'audio') {
                editAudioUploadSection.style.display = 'block';
                editDocumentUploadSection.style.display = 'none';
                document.getElementById("currentAudioStatus").textContent = audioUrl ? "Current audio: " + audioUrl : "No current audio";
            } else {
                editAudioUploadSection.style.display = 'none';
                editDocumentUploadSection.style.display = 'block';
                document.getElementById("currentDocumentStatus").textContent = fileUrl ? "Current document: " + fileUrl : "No current document";
            }

            editDevotionalModal.style.display = "block";
        }
    });

    // When the user clicks on <span> (x), close the modals
    for (var i = 0; i < closeBtns.length; i++) {
        closeBtns[i].onclick = function() {
            addDevotionalModal.style.display = "none";
            editDevotionalModal.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == addDevotionalModal) {
            addDevotionalModal.style.display = "none";
        }
        if (event.target == editDevotionalModal) {
            editDevotionalModal.style.display = "none";
        }
    }
</script>

</body>
</html>