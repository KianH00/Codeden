<?php
include('connect.php');  // Include the correct connect.php file
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $language = $_POST['language'];
    $code = $_POST['code'];
    $user_id = $_SESSION['user_id'];

    // Prepare SQL query using MySQLi connection
    $sql = "INSERT INTO codes (user_id, title, language, code) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);  // Use $conn here instead of $mysqli

    // Bind parameters to the prepared statement
    $stmt->bind_param("isss", $user_id, $title, $language, $code);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Code submitted for approval!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the MySQLi connection
$conn->close();
?>
