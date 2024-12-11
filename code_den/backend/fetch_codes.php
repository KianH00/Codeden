<?php
include('connect.php');

// Fetch codes from the database
$sql = "SELECT id, user_id, title, language, status, created_at FROM codes"; // Include created_at
$result = $conn->query($sql);

$codes = array();
if ($result->num_rows > 0) {
    // Fetch all codes into an array
    while ($row = $result->fetch_assoc()) {
        $codes[] = $row;
    }
}

// Debugging: Log data for backend testing
error_log("Fetched codes: " . print_r($codes, true));

// Set the content type to JSON and allow cross-origin requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Output the JSON encoded array
echo json_encode($codes);

// Close the database connection
$conn->close();
?>