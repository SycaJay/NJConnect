<?php
// Include database connection
include('../../db/config.php');

// Fetch users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <!-- Stylish Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap">
    <link rel="stylesheet" href="../../assets/css/manage_users.css">
</head>
<body>

<div class="container">
    <h1>Manage Users</h1>
    <button class="btn" id="addUserBtn">Add User</button>
    <a href="admin_dashboard.php" class="btn">Return to Dashboard</a>

    <!-- Users Table -->
    <table>
        <thead>
        <tr>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($user = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                <td><?php echo htmlspecialchars($user['middle_name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td>
                    <button class="btn editBtn" 
                        data-id="<?php echo $user['user_id']; ?>" 
                        data-first-name="<?php echo $user['first_name']; ?>" 
                        data-middle-name="<?php echo $user['middle_name'] ?? ''; ?>" 
                        data-last-name="<?php echo $user['last_name']; ?>" 
                        data-email="<?php echo $user['email']; ?>" 
                        data-role="<?php echo $user['role']; ?>">Edit</button>
                    <a href="../../actions/users/delete_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Modal for Adding a User -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Add a New User</h2>
            <form action="../../actions/users/add_user.php" method="POST">
                <label for="firstName">First Name:</label>
                <input type="text" name="first_name" id="firstName" required>

                <label for="middleName">Middle Name (Optional):</label>
                <input type="text" name="middle_name" id="middleName">

                <label for="lastName">Last Name:</label>
                <input type="text" name="last_name" id="lastName" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <label for="role">Role:</label>
                <select name="role" id="role">
                    <option value="regular">Regular</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit" class="btn">Add User</button>
            </form>
        </div>
    </div>

    <!-- Modal for Editing a User -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">Edit User</h2>
            <form id="editUserForm" action="../../actions/users/edit_user.php" method="POST">
                <input type="hidden" name="user_id" id="editUserId">

                <label for="editFirstName">First Name:</label>
                <input type="text" name="first_name" id="editFirstName" required>

                <label for="editMiddleName">Middle Name (Optional):</label>
                <input type="text" name="middle_name" id="editMiddleName">

                <label for="editLastName">Last Name:</label>
                <input type="text" name="last_name" id="editLastName" required>

                <label for="editEmail">Email:</label>
                <input type="email" name="email" id="editEmail" required>

                <label for="editPassword">New Password (Optional):</label>
                <input type="password" name="password" id="editPassword">

                <label for="editRole">Role:</label>
                <select name="role" id="editRole">
                    <option value="regular">Regular</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Javascript for Modal Popups and Form Handling -->
<script>
    // Get the modal elements
    var addUserModal = document.getElementById("addUserModal");
    var editUserModal = document.getElementById("editUserModal");

    // Get the buttons that open the modals
    var addUserBtn = document.getElementById("addUserBtn");
    var editBtns = document.querySelectorAll(".editBtn");

    // Get the <span> elements that close the modals
    var closeBtns = document.getElementsByClassName("close");

    // When the user clicks the "Add User" button, open the "Add User" modal
    addUserBtn.onclick = function() {
        addUserModal.style.display = "block";
    }

    // When the user clicks the "Edit" button, open the "Edit User" modal
    editBtns.forEach(function(btn) {
        btn.onclick = function() {
            var userId = btn.getAttribute("data-id");
            var firstName = btn.getAttribute("data-first-name");
            var middleName = btn.getAttribute("data-middle-name");
            var lastName = btn.getAttribute("data-last-name");
            var email = btn.getAttribute("data-email");
            var role = btn.getAttribute("data-role");

            // Set the values in the edit modal
            document.getElementById("editUserId").value = userId;
            document.getElementById("editFirstName").value = firstName;
            document.getElementById("editMiddleName").value = middleName;
            document.getElementById("editLastName").value = lastName;
            document.getElementById("editEmail").value = email;
            document.getElementById("editRole").value = role;

            editUserModal.style.display = "block";
        }
    });

    // When the user clicks on <span> (x), close the modals
    for (var i = 0; i < closeBtns.length; i++) {
        closeBtns[i].onclick = function() {
            addUserModal.style.display = "none";
            editUserModal.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == addUserModal) {
            addUserModal.style.display = "none";
        }
        if (event.target == editUserModal) {
            editUserModal.style.display = "none";
        }
    }

    // Handle form submission for editing
    document.getElementById("editUserForm").onsubmit = function(event) {
        // event.preventDefault();
        // AJAX to send data to backend (this will be implemented later)
        // For now, we reload the page after submission
        location.reload();
    }
</script>

</body>
</html>