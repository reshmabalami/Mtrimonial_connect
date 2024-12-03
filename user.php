<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.html");
    exit();
}

// Include database connection
include 'dbconnection.php';

// Retrieve the user's name from session
$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

// Fetch the profile picture from the database
$profile_picture = "";
$query = "SELECT profile_picture FROM profile WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_picture);
$stmt->fetch();
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrimonial Connect - Dashboard</title>
    <link rel="stylesheet" href="user.css">
    <style>
        /* Styling the circular profile picture */
        .profile-picture-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ccc;
        }

        .welcome-section {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <header class="dashboard-header">
            <h1>Matrimonial Connect</h1>
            <nav>
                <ul class="nav-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Matches</a></li>
                    <li><a href="#">Messages</a></li>
                    <li><a href="user_profile.html">Profile</a></li>
                    <li><a href="logout.php" class="logout">Logout</a></li>
                </ul>
            </nav>
        </header>       

        <!-- Welcome Section -->
        <section class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
            
            <!-- Profile Picture Section -->
            <?php if (!empty($profile_picture)): ?>
                <div class="profile-picture-container">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-picture">
                </div>
            <?php endif; ?>

            <p>Discover your perfect match today. Start exploring!</p>
<a href="match.php" class="btn explore-button">Explore Matches</a>
        </section>

        <!-- Featured Matches Section -->
        <section class="matches-section">
            <h2>Featured Matches</h2>
            <div class="matches-grid">
                <div class="match-card">
                    <img src="user1.jpg" alt="User 1">
                    <h3>Aarav</h3>
                    <p>Engineer, 29 | Kathmandu</p>
                    <button class="btn connect-button">Connect</button>
                </div>
                <div class="match-card">
                    <img src="user2.jpg" alt="User 2">
                    <h3>Anika</h3>
                    <p>Doctor, 27 | Pokhara</p>
                    <button class="btn connect-button">Connect</button>
                </div>
                <div class="match-card">
                    <img src="user3.jpg" alt="User 3">
                    <h3>Manish</h3>
                    <p>Teacher, 30 | Lalitpur</p>
                    <button class="btn connect-button">Connect</button>
                </div>
                <div class="match-card">
                    <img src="user4.jpg" alt="User 4">
                    <h3>Priya</h3>
                    <p>Artist, 25 | Bhaktapur</p>
                    <button class="btn connect-button">Connect</button>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
