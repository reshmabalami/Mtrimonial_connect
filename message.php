<?php
session_start();
include 'dbconnection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$logged_in_user = $_SESSION['user_id'];

// Fetch the list of accepted users
$query = "
    SELECT p.user_id, p.profile_picture, p.name 
    FROM profile p 
    JOIN Request r ON p.user_id = r.sent_by OR p.user_id = r.received_by
    WHERE (r.sent_by = ? OR r.received_by = ?)
    AND r.Accept = 1
    AND p.user_id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $logged_in_user, $logged_in_user, $logged_in_user);
$stmt->execute();
$result = $stmt->get_result();

$accepted_users = [];
while ($row = $result->fetch_assoc()) {
    $accepted_users[] = $row;
}

$matched_user = isset($_GET['matched_user']) ? $_GET['matched_user'] : null;
if ($matched_user) {
    // Fetch the matched user's profile
    $query = "SELECT * FROM profile WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $matched_user);
    $stmt->execute();
    $matched_profile = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="message.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="accepted-users-list">
        <?php foreach ($accepted_users as $user): ?>
            <div class="user-row" onclick="loadChat(<?php echo $user['user_id']; ?>)">
                <img class="profile-picture" src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                <p class="user-name"><?php echo htmlspecialchars($user['name']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="chat-container" id="chat-container" style="display: <?php echo $matched_user ? 'block' : 'none'; ?>;">
        <?php if ($matched_user): ?>
            <div class="chat-header">
                <img src="<?php echo htmlspecialchars($matched_profile['profile_picture']); ?>" alt="Profile Picture">
                <h3><?php echo htmlspecialchars($matched_profile['name']); ?></h3>
            </div>
            <div class="chat-messages" id="chat-messages"></div>
            <div class="chat-input">
                <input type="text" id="message-input" placeholder="Type your message...">
                <button id="send-button">Send</button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        let matchedUserId = <?php echo json_encode($matched_user); ?>;

        function fetchMessages() {
            if (matchedUserId) {
                $.post('message_fetch.php', { matched_user: matchedUserId }, function (data) {
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.innerHTML = data;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
            }
        }

        document.getElementById('send-button').addEventListener('click', function () {
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();

            if (message !== '') {
                const chatMessages = document.getElementById('chat-messages');
                const messageElement = document.createElement('div');
                messageElement.classList.add('message');
                messageElement.textContent = message + ' (' + new Date().toLocaleTimeString() + ')';
                chatMessages.appendChild(messageElement);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                $.post('message_send.php', { incoming_id: matchedUserId, msg: message }, function (response) {
                    if (response === 'success') {
                        messageInput.value = '';
                        fetchMessages();
                    }
                });
            }
        });

        function loadChat(userId) {
            matchedUserId = userId;
            // Show chat container and hide the user list
            document.getElementById('chat-container').style.display = 'block';
            fetchMessages();
        }

        setInterval(fetchMessages, 2000);
        fetchMessages();
    </script>
</body>
</html>
