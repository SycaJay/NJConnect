<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include database connection
include(__DIR__ . '/../db/config.php');


// Define 24-hour timeframe
$last_24_hours = date('Y-m-d H:i:s', strtotime('-24 hours'));

// Fetch recent sermons
$recent_sermons_query = "SELECT 'Uploaded Sermon' as activity, 
                                 CONCAT('Admin ', u.first_name, ' ', u.last_name) as user, 
                                 s.created_at as date
                         FROM sermons s
                         LEFT JOIN users u ON u.role = 'admin'
                         WHERE s.created_at >= '$last_24_hours'";
                         
// Fetch recent RSVPs
$recent_rsvps_query = "SELECT CONCAT('RSVP ', e.title) as activity, 
                              CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) as user, 
                              r.rsvp_date as date
                       FROM rsvps r
                       JOIN users u ON r.user_id = u.user_id
                       JOIN events e ON r.event_id = e.event_id
                       WHERE r.rsvp_date >= '$last_24_hours'";


// Fetch recent prayer requests
$recent_prayer_requests_query = "SELECT 'Prayer Request' as activity, 
                                         CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) as user, 
                                         pr.submitted_at as date
                                 FROM prayer_requests pr
                                 JOIN users u ON pr.user_id = u.user_id
                                 WHERE pr.submitted_at >= '$last_24_hours'";

// Fetch recent books (Admin placeholder for user)
$recent_books_query = "SELECT 'Uploaded Book' as activity, 
                               'Admin' as user, 
                               b.created_at as date
                       FROM books b
                       WHERE b.created_at >= '$last_24_hours'";

// Fetch recent events (Admin placeholder for user)
$recent_events_query = "SELECT 'Created Event' as activity, 
                                'Admin' as user, 
                                e.created_at as date
                         FROM events e
                         WHERE e.created_at >= '$last_24_hours'";

// Fetch recent departments (Admin placeholder for user)
$recent_departments_query = "SELECT 'Created Department' as activity, 
                                       'Admin' as user, 
                                       d.created_at as date
                             FROM departments d
                             WHERE d.created_at >= '$last_24_hours'";

// Fetch recent devotionals (Admin placeholder for user)
$recent_devotionals_query = "SELECT 'Uploaded Devotional' as activity, 
                                      'Admin' as user, 
                                      dev.created_at as date
                              FROM devotionals dev
                              WHERE dev.created_at >= '$last_24_hours'";

// Fetch recent growth contacts
$recent_growth_contacts_query = "SELECT 'Added Growth Contact' as activity, 
                                          gc.name as user, 
                                          gc.submitted_at as date
                                  FROM growth_contacts gc
                                  WHERE gc.submitted_at >= '$last_24_hours'";

// Fetch recent cart additions
$recent_cart_query = "SELECT 'Added to Cart' as activity, 
                              CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) as user, 
                              c.created_at as date
                       FROM cart c
                       JOIN users u ON c.user_id = u.user_id
                       WHERE c.created_at >= '$last_24_hours'";

// Fetch recent subscriptions
$recent_subscriptions_query = "SELECT 'New Subscription' as activity, 
                                         s.email as user, 
                                         s.created_at as date
                                FROM subscriptions s
                                WHERE s.created_at >= '$last_24_hours'";

//Fetch recent purchases
// $recent_purchases_query = "SELECT 'Recent Purchase' as activity, 
//                               CONCAT(u.first_name, ' ', u.last_name) as user,
//                               p.purchase_date as date
//                           FROM purchases p
//                           LEFT JOIN users u ON u.user_id = p.user_id
//                           WHERE p.purchase_date >= '$last_24_hours'";



// Combine all queries into one
$recent_activities_query = "($recent_sermons_query) UNION ALL 
                            ($recent_rsvps_query) UNION ALL 
                            ($recent_prayer_requests_query) UNION ALL 
                            ($recent_books_query) UNION ALL 
                            ($recent_events_query) UNION ALL 
                            ($recent_departments_query) UNION ALL 
                            ($recent_devotionals_query) UNION ALL 
                            ($recent_growth_contacts_query) UNION ALL 
                            ($recent_cart_query) UNION ALL 
                            ($recent_subscriptions_query) 
                            ORDER BY date DESC";

// Execute query
$recent_activities_result = mysqli_query($conn, $recent_activities_query);

?>
