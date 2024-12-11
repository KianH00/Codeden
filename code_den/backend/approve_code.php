<?php
include('connect.php');
session_start();

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

// Handle approval or rejection
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Validate status to avoid SQL injection
    if ($status === 'approved' || $status === 'rejected') {
        $sql_update = "UPDATE codes SET status = '$status' WHERE id = $id";
        if (mysqli_query($conn, $sql_update)) {
            echo "Code has been $status successfully.";
        } else {
            echo "Error updating code status: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid status.";
    }
}

// Query to get all codes
$sql = "SELECT * FROM codes"; 
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>Code ID</th><th>Code Title</th><th>Status</th><th>Action</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td><a href='approve_code.php?id=" . $row['id'] . "&status=approved'>Approve</a> | <a href='approve_code.php?id=" . $row['id'] . "&status=rejected'>Reject</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No codes submitted.";
}
?>
