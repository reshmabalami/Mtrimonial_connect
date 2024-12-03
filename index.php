<?php
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrimonial/Dating Site</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-logo">Matrimonial Connect</div>
        <ul class="navbar-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#contact">Contact</a></li>
            <?php if ($isLoggedIn): ?>
                <!-- Show Logout if the user is logged in -->
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <!-- Show Sign In and Sign Up if the user is not logged in -->
                <li><a href="signup.html">Sign Up</a></li>
                <li><a href="login.html">Sign In</a></li>
            <?php endif; ?>

        </ul>
    </nav>

    <!-- Fullscreen Image Slider -->
    <section class="slider" id="home">
        <div class="slide active" style="background-image: url('images/couple2.jpg');"></div>
        <div class="slide" style="background-image: url('images/couple1.jpg');"></div>
        <div class="slide" style="background-image: url('images/couple3.jpg');"></div>
        
        <!-- Slide Arrows -->
        <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
        <button class="next" onclick="changeSlide(1)">&#10095;</button>

        <!-- Call-to-Action Text -->
        <div class="cta">
            <h1>Find Your Perfect Match</h1>
            <p>Connecting hearts across boundaries</p>
            <a href="<?php echo $isLoggedIn ? 'match.php' : 'signup.html'; ?>" class="cta-button">Get Started</a>

        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <h2>About Us</h2>
        <p>Welcome to Matrimonial Connect, a trusted platform where we help individuals find meaningful connections. Our mission is to bring people together for lifelong companionship. Start your journey with us today!</p>
    </section>

    <!-- Footer Section -->
    <footer id="contact">
        <p>Contact us: matrimonialconnect@gmail.com |9808000000</p>
        <p>&copy; 2024 Matrimonial Connect. All Rights Reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
