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

// Get the logged-in user's profile
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM profile WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$logged_in_user = $stmt->get_result()->fetch_assoc();

// Fetch all other users excluding those already sent requests
$query = "
    SELECT * 
    FROM profile 
    WHERE user_id != ? 
    AND user_id NOT IN (
        SELECT received_by 
        FROM Request 
        WHERE sent_by = ?
    )
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$other_users = $stmt->get_result();

// Define weights for different fields
$weights = [
    'age' => 0.2,
    'gender' => 0.1,
    'nationality' => 0.1,
    'religion' => 0.1,
    'living_country' => 0.1,
    'profession' => 0.1,
    'study_level' => 0.1,
    'degree_obtained' => 0.1,
    'height' => 0.1,
    'hobbies' => 0.1,
];

// Calculate match percentages
$matches = [];
while ($row = $other_users->fetch_assoc()) {
    $match_score = 0;

    // Match criteria and calculations
    $age_match = ($row['age'] >= $logged_in_user['age_range_min'] && $row['age'] <= $logged_in_user['age_range_max']) ? $weights['age'] * 100 : 0;
    $match_score += $age_match;

    $gender_match = ($row['gender'] == $logged_in_user['gender']) ? $weights['gender'] * 100 : 0;
    $match_score += $gender_match;

    $nationality_match = ($row['nationality'] == $logged_in_user['nationality']) ? $weights['nationality'] * 100 : 0;
    $match_score += $nationality_match;

    $religion_match = ($row['religion'] == $logged_in_user['religion']) ? $weights['religion'] * 100 : 0;
    $match_score += $religion_match;

    $living_country_match = ($row['living_country'] == $logged_in_user['living_country']) ? $weights['living_country'] * 100 : 0;
    $match_score += $living_country_match;

    $profession_match = ($row['profession'] == $logged_in_user['profession']) ? $weights['profession'] * 100 : 0;
    $match_score += $profession_match;

    $study_level_match = ($row['study_level'] == $logged_in_user['study_level']) ? $weights['study_level'] * 100 : 0;
    $match_score += $study_level_match;

    $degree_match = ($row['degree_obtained'] == $logged_in_user['degree_obtained']) ? $weights['degree_obtained'] * 100 : 0;
    $match_score += $degree_match;

    $height_match = (abs($row['height'] - $logged_in_user['height']) <= 5) ? $weights['height'] * 100 : 0;
    $match_score += $height_match;

    // Hobbies match calculation
    $logged_hobbies = explode(',', $logged_in_user['hobbies']);
    $other_hobbies = explode(',', $row['hobbies']);
    $common_hobbies = array_intersect($logged_hobbies, $other_hobbies);
    $hobby_match = (count($common_hobbies) / max(count($logged_hobbies), 1)) * $weights['hobbies'] * 100;
    $match_score += $hobby_match;

    // Store the match score and field-wise matches
    $matches[] = [
        'user' => $row,
        'score' => $match_score,
        'field_matches' => [
            'Age' => $row['age'],
            'Gender' => $row['gender'],
            'Nationality' => $row['nationality'],
            'Religion' => $row['religion'],
            'Living Country' => $row['living_country'],
            'Profession' => $row['profession'],
            'Study Level' => $row['study_level'],
            'Degree Obtained' => $row['degree_obtained'],
            'Height' => $row['height'],
            'Hobbies' => implode(', ', $common_hobbies),
        ],
    ];
}

// Sort matches by score
usort($matches, function ($a, $b) {
    return $b['score'] <=> $a['score'];
});

// Check if there are any matches
if (empty($matches)) {
    header("Location: no_match_found.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches</title>
    <link rel="stylesheet" href="explore_match.css">
    
   <script>
function handleConnect(button, requestedId) {
    const requesterId = <?php echo json_encode($user_id); ?>; // Current logged-in user ID

    // Send AJAX request to insert data into the table
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "handle_connect.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText === "success") {
                // Change button text to "Requested.." and disable it
                button.innerHTML = "Requested..";
                button.disabled = true;
            } else {
                alert("Response: " + xhr.responseText); 
                button.innerHTML = "Requested..";
                button.disabled = true;// Only show alert for errors
                button.closest('.match-card').style.display = 'none';

            }
        }
    };
    xhr.send(`sent_by=${requesterId}&received_by=${requestedId}`);
}
</script>

    
</head>
<body>
<div class="match-container">
    <?php
    // Display matches
    foreach ($matches as $match) {
        $user = $match['user'];
        $profile_picture = !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default-profile.png';
        $name = htmlspecialchars($user['name']);
        $profession = htmlspecialchars($user['profession']);
        $age = htmlspecialchars($user['age']);
        $country = htmlspecialchars($user['living_country']);
        $match_score = round($match['score'], 2);  // Round the score to 2 decimal places
        $field_matches = $match['field_matches'];

        echo <<<HTML
        <div class="match-card">
            <img class="profile-picture" src="$profile_picture" alt="$name">
            <h3>$name</h3>
            <p>$profession, $age | $country</p>
            <p><strong>Overall Match Percentage: $match_score%</strong></p>
            <p><strong>Field-wise Matches:</strong></p>
HTML;
        // Display actual feature values (not percentages)
        foreach ($field_matches as $field => $value) {
            echo "<p><strong>$field:</strong> $value</p>";
        }
        echo <<<HTML
    <button class="btn connect-button" onclick="handleConnect(this, {$user['user_id']})">Connect</button>
    </div>
HTML;
    }
    ?>
</div>
</body>
</html>

