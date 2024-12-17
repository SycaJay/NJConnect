<?php
// Include database connection
include('../../db/config.php');

// Fetch departments from the database
$query = "SELECT * FROM departments";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/manage_dept.css">
</head>
<body>

<div class="container">
    <h1>Manage Departments</h1>
    <button class="btn" id="addDepartmentBtn">Add Department</button>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Departments Table -->
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($department = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($department['name']); ?></td>
                <td><?php echo htmlspecialchars(substr($department['description'], 0, 100) . '...'); ?></td>
                <td>
                    <?php if (!empty($department['image'])): ?>
                        <img src="../../uploads/<?php echo htmlspecialchars($department['image']); ?>" 
                             alt="Department Image" style="max-width: 100px; max-height: 100px;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($department['created_at']); ?></td>
                <td><?php echo htmlspecialchars($department['updated_at']); ?></td>
                <td>
                    <button class="btn editBtn" 
                        data-id="<?php echo $department['department_id']; ?>" 
                        data-name="<?php echo htmlspecialchars($department['name']); ?>" 
                        data-description="<?php echo htmlspecialchars($department['description']); ?>" 
                        data-image="<?php echo htmlspecialchars($department['image']); ?>">Edit</button>
                    <a href="../../actions/departments/delete_department.php?id=<?php echo $department['department_id']; ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Are you sure you want to delete this department?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Modal for Adding a Department -->
    <div id="addDepartmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Add a New Department</h2>
            <form action="../../actions/departments/add_department.php" method="POST" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea>

                <label for="image">Department Image:</label>
                <input type="file" name="image" id="image" accept="image/*">
                <img id="imagePreview" class="image-preview" style="display:none;">

                <button type="submit" class="btn">Add Department</button>
            </form>
        </div>
    </div>

    <!-- Modal for Editing a Department -->
    <div id="editDepartmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Edit Department</h2>
            <form id="editDepartmentForm" action="../../actions/departments/edit_department.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="department_id" id="editDepartmentId">
                <input type="hidden" name="existing_image" id="editExistingImage">

                <label for="editName">Name:</label>
                <input type="text" name="name" id="editName" required>

                <label for="editDescription">Description:</label>
                <textarea name="description" id="editDescription" rows="4" required></textarea>

                <label for="editImage">Department Image:</label>
                <input type="file" name="image" id="editImage" accept="image/*">
                <img id="editImagePreview" class="image-preview">

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Get modal elements
    var addDepartmentModal = document.getElementById("addDepartmentModal");
    var editDepartmentModal = document.getElementById("editDepartmentModal");
    var addDepartmentBtn = document.getElementById("addDepartmentBtn");
    var editBtns = document.querySelectorAll(".editBtn");
    var closeBtns = document.getElementsByClassName("close");

    // Image preview for add department
    document.getElementById('image').addEventListener('change', function(e) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var imgPreview = document.getElementById('imagePreview');
            imgPreview.src = event.target.result;
            imgPreview.style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    });

    // Open add department modal
    addDepartmentBtn.onclick = function() {
        addDepartmentModal.style.display = "block";
    }

    // Handle edit button clicks
    editBtns.forEach(function(btn) {
        btn.onclick = function() {
            var departmentId = btn.getAttribute("data-id");
            var name = btn.getAttribute("data-name");
            var description = btn.getAttribute("data-description");
            var image = btn.getAttribute("data-image");

            // Set form values
            document.getElementById("editDepartmentId").value = departmentId;
            document.getElementById("editName").value = name;
            document.getElementById("editDescription").value = description;
            document.getElementById("editExistingImage").value = image;

            // Set image preview
            var editImagePreview = document.getElementById("editImagePreview");
            if (image) {
                editImagePreview.src = "../../uploads/" + image;
                editImagePreview.style.display = "block";
            } else {
                editImagePreview.style.display = "none";
            }

            editDepartmentModal.style.display = "block";
        }
    });

    // Close modal handlers
    for (var i = 0; i < closeBtns.length; i++) {
        closeBtns[i].onclick = function() {
            addDepartmentModal.style.display = "none";
            editDepartmentModal.style.display = "none";
        }
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target == addDepartmentModal) {
            addDepartmentModal.style.display = "none";
        }
        if (event.target == editDepartmentModal) {
            editDepartmentModal.style.display = "none";
        }
    }

    // Preview image in edit form
    document.getElementById('editImage').addEventListener('change', function(e) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var imgPreview = document.getElementById('editImagePreview');
            imgPreview.src = event.target.result;
            imgPreview.style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    });
</script>

</body>
</html>