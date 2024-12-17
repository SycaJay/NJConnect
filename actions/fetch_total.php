<?php
// Include database connection
include(__DIR__ . '/../db/config.php');

// Fetch counts from the database
$sermons_count_query = "SELECT COUNT(*) as count FROM sermons";
$sermons_count_result = mysqli_query($conn, $sermons_count_query);
$sermons_count = mysqli_fetch_assoc($sermons_count_result)['count'];

$events_count_query = "SELECT COUNT(*) as count FROM events WHERE event_date >= CURDATE()";
$events_count_result = mysqli_query($conn, $events_count_query);
$events_count = mysqli_fetch_assoc($events_count_result)['count'];

$users_count_query = "SELECT COUNT(*) as count FROM users";
$users_count_result = mysqli_query($conn, $users_count_query);
$users_count = mysqli_fetch_assoc($users_count_result)['count'];

$prayer_requests_count_query = "SELECT COUNT(*) as count FROM prayer_requests";
$prayer_requests_count_result = mysqli_query($conn, $prayer_requests_count_query);
$prayer_requests_count = mysqli_fetch_assoc($prayer_requests_count_result)['count'];

$books_count_query = "SELECT COUNT(*) as count FROM books";
$books_count_result = mysqli_query($conn, $books_count_query);
$books_count = mysqli_fetch_assoc($books_count_result)['count'];

$departments_count_query = "SELECT COUNT(*) as count FROM departments";
$departments_count_result = mysqli_query($conn, $departments_count_query);
$departments_count = mysqli_fetch_assoc($departments_count_result)['count'];

$devotionals_count_query = "SELECT COUNT(*) as count FROM devotionals";
$devotionals_count_result = mysqli_query($conn, $devotionals_count_query);
$devotionals_count = mysqli_fetch_assoc($devotionals_count_result)['count'];

$growth_contacts_count_query = "SELECT COUNT(*) as count FROM growth_contacts";
$growth_contacts_count_result = mysqli_query($conn, $growth_contacts_count_query);
$growth_contacts_count = mysqli_fetch_assoc($growth_contacts_count_result)['count'];

$rsvps_count_query = "SELECT COUNT(*) as count FROM rsvps";
$rsvps_count_result = mysqli_query($conn, $rsvps_count_query);
$rsvps_count = mysqli_fetch_assoc($rsvps_count_result)['count'];

$subscriptions_count_query = "SELECT COUNT(*) as count FROM subscriptions";
$subscriptions_count_result = mysqli_query($conn, $subscriptions_count_query);
$subscriptions_count = mysqli_fetch_assoc($subscriptions_count_result)['count'];

// $purchases_count_query = "SELECT COUNT(*) as count FROM purchases";
// $purchases_count_result = mysqli_query($conn, $purchases_count_query);
// $purchases_count = mysqli_fetch_assoc($purchases_count_result)['count'];


?>