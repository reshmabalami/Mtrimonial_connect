<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Include database connection
include 'dbconnection.php';

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Query the Request table for requests where the logged-in user is the recipient (received_by)
$query = "SELECT * FROM Request WHERE received_by = ? AND Accept = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$requests = $stmt->get_result();

// If there are no requests, show a message
if ($requests->num_rows == 0) {
    echo "<p>No new requests.</p>";
    exit();
}

// Fetch the details of the users who sent the request
$matches = [];
while ($request = $requests->fetch_assoc()) {
    $sender_id = $request['sent_by'];

    // Fetch the profile of the user who sent the request
    $user_query = "SELECT * FROM profile WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $sender_id);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();

    // Add the user details to the matches array
    $matches[] = [
        'user' => $user,
        'request_id' => $request['R_id'], // Store the request ID
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Match Requests</title>
    <link rel="stylesheet" href="explore_match.css">
    <script>
        // Function to handle the action when the user accepts the request
        function handleAccept(requestId, button) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "accept_request.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === "success") {
                        alert("Request accepted!");
                        button.closest('.match-card').style.display = 'none'; // Hide the card
                    } else {
                        alert("Response: " + xhr.responseText);
                        button.closest('.match-card').style.display = 'none';
                        alert("Check Messages!");

                    }
                }
            };
            xhr.send("request_id=" + requestId);
        }
    </script>
</head>
<body>
<div class="match-container">
    <?php
    // Display the profiles of users who sent the request
    foreach ($matches as $match) {
        $user = $match['user'];
        $profile_picture = !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default-profile.png';
        $name = htmlspecialchars($user['name']);
        $profession = htmlspecialchars($user['profession']);
        $age = htmlspecialchars($user['age']);
        $country = htmlspecialchars($user['living_country']);
        $request_id = $match['request_id']; // Get the request ID

        echo <<<HTML
        <div class="match-card">
            <img class="profile-picture" src="$profile_picture" alt="$name">
            <h3>$name</h3>
            <p>$profession, $age | $country</p>
            <p><strong>Request sent by:</strong> $name</p>
            <button class="btn accept-button" onclick="handleAccept($request_id, this)">Accept Request</button>
        </div>
HTML;
    }
    ?>
</div>
</body>
</html>
