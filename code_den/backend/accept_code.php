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

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If session user_id is not available, try to fetch user_id using persistent cookie or other stored data
    if (isset($_COOKIE['username'])) {
        $cookie_username = $_COOKIE['username'];

        // Fetch user_id from the database based on the username from cookies
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE username = ?");
        $stmt->bind_param("s", $cookie_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $user_id = $user_data['id'];
            $username = $user_data['username'];

            // Set session values to maintain login state
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
        } else {
            echo "User  not found in the database.";
            exit();
        }
    } else {
        echo "User  is not logged in or session has expired.";
        exit();
    }
} else {
    // If session is available, use it
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
}

// Check if code_id is provided
if (isset($_GET['code_id'])) {
    $code_id = intval($_GET['code_id']); // Sanitize the input

    // Fetch the code details from the database
    $stmt = $conn->prepare("SELECT c.title, c.language, c.code_file, u.username 
                            FROM codes c 
                            INNER JOIN users u ON c.user_id = u.id 
                            WHERE c.id = ?");
    $stmt->bind_param("i", $code_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $code_details = $result->fetch_assoc();

        // Prepare to insert into posted_codes table
        $tags = isset($_GET['tags']) ? $_GET['tags'] : [];
        $tags_string = implode(',', $tags); // Convert array to a string for database storage

        $insert_stmt = $conn->prepare("INSERT INTO posted_codes (user_id, username, title, language, code_file, code_tags) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("isssss", $user_id, $code_details['username'], $code_details['title'], $code_details['language'], $code_details['code_file'], $tags_string);

        // Execute the insert
        if ($insert_stmt->execute()) {
            // Update the status in the codes table
            $update_stmt = $conn->prepare("UPDATE codes SET status = 'posted' WHERE id = ?");
            $update_stmt->bind_param("i", $code_id);
            $update_stmt->execute();

            // Store details in session to pass to admin_dashboard.php
            $_SESSION['accepted_code'] = $code_details;

            // Redirect to admin_dashboard.php
            header("Location: ../public/admin_dashboard.php");
            exit();
        } else {
            echo "Error posting the code: " . $insert_stmt->error;
            exit();
        }
    } else {
        echo "No code found with the given ID.";
        exit();
    }
} else {
    echo "Code ID not specified.";
    exit();
}

// Close the database connection
$conn->close();
?>