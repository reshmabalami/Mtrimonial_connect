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

// Check if request ID is provided
if (isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);

    // Debugging: Log request_id
    error_log("Request ID received: $request_id");

    // Ensure the connection is valid
    if (!$conn) {
        echo "error: Database connection failed.";
        error_log("Database connection error: " . mysqli_connect_error());
        exit();
    }

    // Update the request status to accepted (Accept = 1)
    $query = "UPDATE Request SET Accept = 1 WHERE R_id = ?";
    $stmt = $conn->prepare($query);

    // Check if statement preparation was successful
    if (!$stmt) {
        echo "error: " . $conn->error;
        error_log("Statement preparation error: " . $conn->error);
        exit();
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("i", $request_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
        error_log("Statement execution error: " . $stmt->error);
    }
} else {
    echo "missing_data";
    error_log("Request ID is missing.");
}
?>
