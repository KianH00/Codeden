<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost'; // or '127.0.0.1'
$user = 'root'; // default XAMPP username
$pass = ''; // default XAMPP password is empty
$dbname = 'repository'; // your database name
$port = 3306; // specify the port

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Handle form submission to delete code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_code_id'])) {
    $delete_code_id = $_POST['delete_code_id'];

    // Prepare and execute the delete statement
    $delete_stmt = $conn->prepare("DELETE FROM codes WHERE id = ?");
    $delete_stmt->bind_param("i", $delete_code_id);

    if ($delete_stmt->execute()) {
        // Redirect to profile.php after deleting
        header("Location: profile.php");
        exit();
    } else {
        echo "Error deleting code: " . $delete_stmt->error;
    }

    $delete_stmt->close();
}

// Fetch user data
$user = null;
$codes = [];
try {
    $user_id = $_SESSION['user_id'];

    // Fetch user info
    $sql = "SELECT name, username, year_level FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $error_message = "User  not found.";
    }

    // Fetch user's codes
    $sql_codes = "SELECT id, title, language, status, created_at, code_file FROM codes WHERE user_id = ? ORDER BY created_at DESC";
    $stmt_codes = $conn->prepare($sql_codes);
    $stmt_codes->bind_param("i", $user_id);
    $stmt_codes->execute();
    $result_codes = $stmt_codes->get_result();

    while ($row = $result_codes->fetch_assoc()) {
        $codes[] = $row; // Store each code in the array
    }
    $stmt->close();
    $stmt_codes->close();
    $conn->close();
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

$username = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/profile.css" />
    <title> <?php echo htmlspecialchars($username); ?> Dashboard</title>
</head>
<body>
    <div class="dashboard">
        <div class="user-info-container">
            <a href="homep.php" class="btn">Back to Homepage</a>
            <div class="user-info">
                <?php if ($user): ?>
                    <h1 class="name" id="user-name"><?= htmlspecialchars(ucwords($user['name'])) ?></h1>
                    <p class="username" id="user-username">Username: <?= htmlspecialchars($user['username']) ?></p>
                    <p class="year-level" id="user-year-level">Year Level: <?= htmlspecialchars($user['year_level']) ?></p>
                <?php else: ?>
                    <h1 class="name" id="user-name">Error loading data</h1>
                    <p class="username" id="user-username"><?= htmlspecialchars($error_message ?? "Unknown error occurred.") ?></p>
                    <p class="year-level" id="user-year-level"></p>
                <?php endif; ?>
            </div>
            <div class="posted-codes-container">
                <h2>Your Codes</h2>
                <div class="codes-wrapper">
                    <?php if (!empty($codes)): ?>
                        <?php foreach ($codes as $code): ?>
                            <div class="code-container">
                                <h3><?= htmlspecialchars($code['title']) ?></h3>
                                <p><strong>Language:</strong> <?= htmlspecialchars($code['language']) ?></p>
                                <p><strong>Status:</strong> <?= htmlspecialchars($code['status']) ?></p>
                                <p><strong>Submitted Date:</strong> <?php echo htmlspecialchars($code['created_at']); ?></p>
                                <p><strong>Code:</strong><button class="copy-button" data-code-id="<?= $code['id'] ?>">Copy</button></p>
                                <pre id="code-<?= $code['id'] ?>"><?= htmlspecialchars($code['code_file']) ?></pre>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="delete_code_id" value="<?= $code['id'] ?>">
                                    <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this code?');">Remove Code</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No codes found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <a href="homep.php" class="btn">Home</a>
    </div>
    <script src="js/profile.js"></script>
</body>
</html>