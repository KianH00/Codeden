<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('connect.php');

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $year_level = $_POST['year_level'];

    // Basic validation
    if (empty($name) || empty($username) || empty($password) || empty($confirm_password) || empty($year_level)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (name, username, password, year_level) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo "Error preparing statement: " . htmlspecialchars($conn->error);
        exit;
    }

    // Bind parameters
    $stmt->bind_param("ssss", $name, $username, $hashed_password, $year_level);

    if ($stmt->execute()) {
        // Redirect to index.html after successful registration
        header("Location: /code_den/public/index.html"); // Adjust path as needed
        exit(); // Ensure you call exit() after header to stop further execution
    } else {
        echo "Error: " . htmlspecialchars($stmt->error);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>