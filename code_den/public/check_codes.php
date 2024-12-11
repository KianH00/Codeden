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

// Check if code_id is provided
if (isset($_GET['code_id'])) {
    $code_id = intval($_GET['code_id']); // Sanitize the input

    // Fetch the code details from the database
    $stmt = $conn->prepare("SELECT * FROM codes WHERE id = ?");
    $stmt->bind_param("i", $code_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $code_details = $result->fetch_assoc();
    } else {
        echo "No code found with the given ID.";
        exit();
    }
} else {
    echo "Code ID not specified.";
    exit();
}

// Function to calculate Levenshtein similarity
function levenshtein_similarity($str1, $str2) {
    $distance = levenshtein($str1, $str2);
    $maxLen = max(strlen($str1), strlen($str2));
    if ($maxLen == 0) {
        return 1.0; // Both strings are empty
    }
    return 1 - ($distance / $maxLen); // Similarity score between 0 and 1
}

// Fetch all code snippets from the database excluding the current code
$stmt = $conn->prepare("SELECT title, code_file, status, created_at FROM codes WHERE id != ?");
$stmt->bind_param("i", $code_id); // Bind the current code_id to exclude it
$stmt->execute();
$all_codes = $stmt->get_result();

// Store similar codes
$similar_codes = [];
$threshold = 0.7; // Define a threshold for similarity

while ($row = $all_codes->fetch_assoc()) {
    $similarity = levenshtein_similarity($code_details['code_file'], $row['code_file']);
    if ($similarity >= $threshold) {
        $similar_codes[] = [
            'title' => $row['title'],
            'created_at' => $row['created_at'],
            'status' => $row['status'] // Ensure this is the correct column name
        ];
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Code</title>
    <link rel="stylesheet" href="css/check_codes.css">
</head>
<body>
    <div class="container">
        <h2>CHECK CODES</h2>
        <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a>
        <div class="label">TITLE:</div>
        <div class="value"><?php echo htmlspecialchars($code_details['title']); ?></div>
        <div class="label">LANGUAGE:</div>
        <div class="value"><?php echo htmlspecialchars($code_details['language']); ?></div>
        <div class="label">STATUS:</div>
        <div class="value"><?php echo htmlspecialchars($code_details['status']); ?></div>
        <div class="label">CODE: </div>
        <pre><code><?php echo htmlspecialchars($code_details['code_file']); ?></code></pre>
        <?php if (!empty($similar_codes)): ?>
            <div class='similar-codes'>
                <h3>Similar Codes:</h3>
                <ul>
                <?php foreach ($similar_codes as $code): ?>
                    <li>
                        <strong>Title:</strong> <?php echo htmlspecialchars($code['title']); ?><br>
                        <strong>Uploaded on:</strong> <?php echo htmlspecialchars($code['created_at']); ?>
                        <strong>Status:</strong> <?php echo htmlspecialchars($code['status']); ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class='similar-codes'><strong>No similar codes found...</strong></div>
        <?php endif; ?>
        <form action="../backend/accept_code.php" method="GET" class="action-form">
            <div class="checklist">
                <h3>Select Tags:</h3>
                <label><input type="checkbox" name="tags[]" value="Looping"> Looping</label><br>
                <label><input type="checkbox" name="tags[]" value="Conditional Statements"> Conditional Statements</label><br>
                <label><input type="checkbox" name="tags[]" value="Operators"> Operators</label><br>
                <label><input type="checkbox" name="tags[]" value="Variable and Data Type"> Variable and Data Type</label><br>
                <label><input type="checkbox" name="tags[]" value="Functions and Method"> Functions and Method</label><br>
            </div>         
            <div class="action-buttons">
                <?php if ($code_details['status'] === 'posted'): ?>
                    <div class="warning">Warning: This code has already been posted!!!</div>
                <?php elseif ($code_details['status'] === 'rejected'): ?>
                    <div class="warning">Warning: This code has already been rejected!!!</div>
                <?php else: ?>
                    <input type="hidden" name="code_id" value="<?php echo $code_id; ?>" />
                    <button type="submit" class="accept-button">Accept</button>
                    <a href="../backend/reject_code.php?code_id=<?php echo $code_id; ?>" class="reject-button">Reject</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>