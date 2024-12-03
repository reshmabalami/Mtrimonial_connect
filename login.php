<?php

// Start the session
session_start();

// Database connection
$host = "localhost"; // Replace with your database host
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "matrimonial_connect"; // Replace with your database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

// SQL query to check if the user exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Password is correct, set session variables
        $_SESSION['user_id'] = $user['id'];  // Set the user ID in the session
        $_SESSION['user_name'] = $user['full_name'];  // Optionally set the user name

        // Redirect to homepage
        header("Location: user.php
        ");
        exit();
    } else {
        // Password is incorrect, redirect to login page with error
        header("Location: login.html?error=incorrect_password");
        exit();
    }
} else {
    // User does not exist, redirect to login page with error
    header("Location: login.html?error=user_not_found");
    exit();
}

// Close connection
$stmt->close();
$conn->close();
?>
