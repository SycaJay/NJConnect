<?php
session_start();
include('../../db/config.php');

// Check if event_id is provided
if (!isset($_GET['event_id'])) {
    echo json_encode([]);
    exit();
}

$event_id = $_GET['event_id'];

try {
    // Fetch all booked seat numbers for this specific event
    $stmt = $conn->prepare("SELECT seat_number FROM rsvps WHERE event_id = ?");
    $stmt->bind_param("i", $event_id); // Bind event_id as integer
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookedSeats = [];
    while ($row = $result->fetch_assoc()) {
        $bookedSeats[] = $row['seat_number'];
    }
    
    // Return as JSON
    echo json_encode($bookedSeats);
} catch (Exception $e) {
    // Log error or handle it appropriately
    error_log($e->getMessage());
    echo json_encode([]);
}
exit();
?>
