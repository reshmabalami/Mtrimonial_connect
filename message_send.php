<?php
session_start();
include 'dbconnection.php';

if (isset($_POST['incoming_id'], $_POST['msg'])) {
    $incoming_id = $_POST['incoming_id'];
    $outgoing_id = $_SESSION['user_id']; // Logged-in user
    $msg = $_POST['msg'];

    // Insert the message into the database
    $query = "INSERT INTO message (incoming_id, outgoing_id, msg) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $incoming_id, $outgoing_id, $msg);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
