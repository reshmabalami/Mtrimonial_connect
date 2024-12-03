<?php
// Database connection details
$host = 'localhost';
$dbname = 'matrimonial_connect';
$username = 'root';  // Replace with your database username
$password = '';  // Replace with your database password

// Establish a connection to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $full_name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $age = (int)$_POST['age'];
    $gender = htmlspecialchars($_POST['gender']);

    try {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, age, gender) 
                                VALUES (:full_name, :email, :password, :age, :gender)");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':gender', $gender);

        // Execute the query
        $stmt->execute();

        header("Location: login.html");
exit();


    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate email error
            echo "Error: This email is already registered!";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Close the connection
$conn = null;
?>
