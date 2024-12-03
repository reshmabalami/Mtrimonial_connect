<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Include database connection file
include 'dbconnection.php';

// Get the form data
$name = $_POST['name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$nationality = $_POST['nationality'];
$religion = $_POST['religion'];
$living_country = $_POST['living_country'];
$contact = $_POST['contact'];
$profession = $_POST['profession'];
$study_level = $_POST['study_level'];
$degree_obtained = $_POST['degree_obtained'];
$height = $_POST['height'];
$age_range_min = $_POST['age_range_min'];
$age_range_max = $_POST['age_range_max'];
$hobbies = implode(", ", $_POST['hobbies']); // Storing hobbies as a comma-separated string

// Handle the uploaded profile image
$profile_picture = "";
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    $file_name = $_FILES['profile_picture']['name'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_size = $_FILES['profile_picture']['size'];
    $file_type = $_FILES['profile_picture']['type'];

    if (!in_array($file_type, $allowed_types)) {
        echo "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
        exit();
    }

    if ($file_size > $max_size) {
        echo "File size exceeds the allowed limit of 5MB.";
        exit();
    }

    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = "profile_" . time() . "." . $file_extension;

    $upload_dir = 'images/';

    if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
        $profile_picture = $upload_dir . $new_file_name;
    } else {
        echo "Failed to upload image.";
        exit();
    }
}

// Prepare and execute the SQL query for insertion
$user_id = $_SESSION['user_id'];
$query = "INSERT INTO profile 
    (user_id, name, dob, gender, nationality, religion, living_country, contact, profession, study_level, degree_obtained, height, age_range_min, age_range_max, hobbies, profile_picture) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);

// Check if the statement is prepared successfully
if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

// Bind the parameters
$stmt->bind_param(
    "isssssssssssssss",
    $user_id,
    $name,
    $dob,
    $gender,
    $nationality,
    $religion,
    $living_country,
    $contact,
    $profession,
    $study_level,
    $degree_obtained,
    $height,
    $age_range_min,
    $age_range_max,
    $hobbies,
    $profile_picture
);

// Execute the query
if ($stmt->execute()) {
    echo "Profile created successfully!";
    header("Location: user.php"); // Redirect to the profile page after successful insertion
    exit();
} else {
    echo "Error inserting profile: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
