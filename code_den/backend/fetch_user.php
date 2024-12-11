<?php
include('connect.php');

// Fetch users from the database
$sql = "SELECT id, name, username, password, year_level FROM users";
$result = $conn->query($sql);

$users = array();
if ($result->num_rows > 0) {
    // Fetch all users into an array
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Debugging: Log data for backend testing
error_log("Fetched users: " . print_r($users, true));

// Set the content type to JSON and allow cross-origin requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Output the JSON encoded array
echo json_encode($users);

// Close the database connection
$conn->close();
?>
