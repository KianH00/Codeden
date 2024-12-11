<?php
session_start();

// Include database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'repository';
$port = 3306;

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if code_id is provided
if (isset($_GET['code_id'])) {
    $code_id = intval($_GET['code_id']); // Sanitize the input

    // Update the status to 'rejected'
    $stmt = $conn->prepare("UPDATE codes SET status = ? WHERE id = ?");
    $status = 'rejected';
    $stmt->bind_param("si", $status, $code_id);

    if ($stmt->execute()) {
        // Redirect back to the check codes page or dashboard with a success message
        $_SESSION['message'] = "Code has been rejected successfully.";
        header("Location: ../public/check_codes.php?code_id=" . $code_id);
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Code ID not specified.";
}

// Close the database connection
$conn->close();
?>