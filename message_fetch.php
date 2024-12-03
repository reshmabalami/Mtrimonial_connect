<?php
session_start();
include 'dbconnection.php';

// Fetch messages between logged-in user and matched user
if (isset($_POST['matched_user'])) {
    $matched_user = $_POST['matched_user'];
    $logged_in_user = $_SESSION['user_id'];

    $query = "
        SELECT msg
        FROM message
        WHERE (incoming_id = ? AND outgoing_id = ?) OR (incoming_id = ? AND outgoing_id = ?)
        ORDER BY M_id ASC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $logged_in_user, $matched_user, $matched_user, $logged_in_user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display messages with server-side timestamp
    while ($row = $result->fetch_assoc()) {
        // Get the current timestamp and format it
        $formatted_time = date('h:i A');
        echo "<div class='message'>" . htmlspecialchars($row['msg']) . " (" . $formatted_time . ")</div>";
    }
}
?>
