<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'repository';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch saved codes for the logged-in user, ordered by created_at descending
$stmt = $conn->prepare("SELECT * FROM saved_codes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$codes = [];
if ($result->num_rows > 0) {
    // Fetch all codes
    while ($row = $result->fetch_assoc()) {
        $codes[] = $row;
    }
}

$stmt->close();

// Handle form submission to save new code or delete code
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for delete request first
    if (isset($_POST['delete_code_id'])) {
        $delete_code_id = $_POST['delete_code_id'];
        
        // Prepare and execute the delete statement
        $delete_stmt = $conn->prepare("DELETE FROM saved_codes WHERE user_id = ? AND id = ?");
        $delete_stmt->bind_param("ii", $user_id, $delete_code_id);
        
        if ($delete_stmt->execute()) {
            // Redirect to saved_codes.php after deleting
            header("Location: saved_codes.php");
            exit();
        } else {
            echo "Error deleting code: " . $delete_stmt->error;
        }
        
        $delete_stmt->close();
    } else {
        // Handle new code submission
        // Get data from the form
        $code_id = $_POST['code_id'] ?? null; // Use null coalescing operator to avoid undefined index
        $title = $_POST['title'] ?? null;
        $language = $_POST['language'] ?? null;
        $code_file = $_POST['code_file'] ?? null;

        // Check for null values
        if (is_null($code_id) || is_null($title) || is_null($language) || is_null($code_file)) {
            die("Error: Required fields are missing.");
        }

        $stmt = $conn->prepare("INSERT INTO saved_codes (user_id, code_id, title, language, code_file) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("iisss", $user_id, $code_id, $title, $language, $code_file);

        if ($stmt->execute()) {
            // Redirect to saved_codes.php after saving
            header("Location: saved_codes.php");
            exit(); // Make sure to exit after the redirect
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/profile.css" />
    <title>Saved Codes</title>
</head>
<body>
    <div class="dashboard">
        <div class="user-info-container">
            <a href="homep.php" class="btn">Back to Homepage</a>
            <div class="posted-codes-container">
                <h2>Saved Codes</h2>
                <div class="codes-wrapper">
                    <?php if (!empty($codes)): ?>
                        <?php foreach ($codes as $code): ?>
                            <div class="code-container">
                                <h3><?= htmlspecialchars($code['title']) ?></h3>
                                <p><strong>Language:</strong> <?= htmlspecialchars($code['language']) ?></p>
                                <p><strong>Saved Date:</strong> <?= htmlspecialchars($code['created_at']) ?></p>
                                <p><strong>Code:</strong> <button class="copy-button" data-code-id="<?= $code['id'] ?>">Copy</button></p>
                                <pre id="code-<?= $code['id'] ?>"><?= htmlspecialchars($code['code_file']) ?></pre>
                            </div>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="delete_code_id" value="<?= $code['id'] ?>">
                                <button type="submit" class="delete-button">Remove Saved Code</button>
                            </form>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No codes found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <a href="homep.php" class="btn">Home</a>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>