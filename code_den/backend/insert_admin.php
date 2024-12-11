<?php
include('connect.php');

// Admin users and hashed passwords
$admin_users = [
    'dghellie@ccc.edu.ph' => 'Shan0112',
    'jbarba@ccc.edu.ph' => 'Arnie0011',
    'njerusalem@ccc.edu.ph' => 'Nica0044',
    'jndimaano@ccc.edu.ph' => 'Dimaano00100'
];

// Prepare SQL query
$sql = "INSERT INTO users (email, password, role) VALUES (?, ?, 'admin')";

// Prepare the statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// Loop through each admin and insert into the database
foreach ($admin_users as $email => $password) {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Bind parameters (email, hashed password)
    $stmt->bind_param("ss", $email, $hashed_password);

    // Execute the query
    if (!$stmt->execute()) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }
}

echo "Admin users inserted successfully!";

// Close the prepared statement and connection
$stmt->close();
$conn->close();
?>
