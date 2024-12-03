<?php
// Start session
session_start();

// Include database connection
include 'dbconnection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "unauthorized";
    exit();
}

// Debugging: Add logging to check the request values
if (isset($_POST['sent_by']) && isset($_POST['received_by'])) {
    $requester_id = intval($_POST['sent_by']);
    $receiver_id = intval($_POST['received_by']);

    // Prevent duplicate requests (same user cannot send multiple requests to the same user)
    $check_query = "SELECT * FROM request WHERE sent_by = ? AND received_by = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $requester_id, $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "Request already sent.";
        exit();
    }

    // Insert the request into the database
    $insert_query = "INSERT INTO request (sent_by, received_by) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ii", $requester_id, $receiver_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
