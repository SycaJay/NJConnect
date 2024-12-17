<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include('../../db/config.php');

// Get the user ID, event ID, and seat number from the form
$user_id = $_SESSION['user_id']; // Assume the user ID is stored in the session
$event_id = isset($_POST['event_id']) ? $_POST['event_id'] : null; // Retrieve event_id from POST
$seat_number = isset($_POST['seat_number']) ? $_POST['seat_number'] : null; // Get seat number from POST

// Check if a seat was selected and if event_id is set
if (empty($seat_number)) {
    echo "No seat selected!";
    exit();
}

if (!$event_id) {
    echo "Event ID is missing!";
    exit();
}

// Check if the user has already reserved a seat for this event
$sql_check = "SELECT * FROM rsvps WHERE user_id = ? AND event_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $event_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "You have already reserved a seat for this event!";
    exit();
}

// Prepare the SQL query to insert the RSVP
$sql = "INSERT INTO rsvps (user_id, event_id, seat_number) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $event_id, $seat_number);

// Execute the query and check for success
if ($stmt->execute()) {
    echo "Seat reservation confirmed! You have selected seat number: $seat_number";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
