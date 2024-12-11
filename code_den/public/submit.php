<?php
session_start();

// Include database connection
$host = 'localhost'; // or '127.0.0.1'
$user = 'root'; // default XAMPP username
$pass = ''; // default XAMPP password is empty
$dbname = 'repository'; // your database name
$port = 3306; // specify the port

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Function to validate code based on the selected language
function validateCode($language, $code) {
    switch ($language) {
        case 'Java':
            return preg_match('/public\s+class\s+\w+\s*{/', $code);

        case 'C++':
            return preg_match('/#include\s*<.+?>.*int\s+main\s*\(\s*\)\s*{.*}/s', $code);
            
        case 'C':
            $hasInclude = preg_match('/#include\s*<.+?>/', $code);
            $hasMain = preg_match('/int\s+main\s*\(\s*(void)?\s*\)\s*{/', $code);
            $notCpp = !preg_match('/#include\s*<iostream>|std::/', $code);
            return $hasInclude && $hasMain && $notCpp;

        case 'Python':
            // Basic validation: check for function definitions or class definitions
            return $code;

        case 'PHP':
            // Basic validation: check for opening PHP tag and function definition
            return preg_match('/<\?php.*function\s+\w+\s*\(.*\)\s*{/', $code) || preg_match('/<\?php/', $code);

        default:
            return false;
    }
}


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted data
    $title = $_POST['title'];
    $language = $_POST['language'];
    $code = $_POST['code'];
    $status = 'pending'; // Default status

    // Validate the code
    if (strlen($code) > 2000) {
        echo "<script>alert('The code must not exceed 500 characters.');</script>";
    } elseif (!validateCode($language, $code)) {
        echo "<script>alert('The code is not valid for the selected programming language.');</script>";
    } else {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO codes (user_id, title, language, status, code_file) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $title, $language, $status, $code);

        // Execute the statement
        if ($stmt->execute()) {
            header('Location: profile.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Submit Code</title>
    <link rel="stylesheet" href="css/submit.css" />
</head>
<body>
    <a href="homep.php" class="back-button">Back</a>
    <form action="" method="POST">
        <h1>Submit your code snippets...</h1>
        <label for="title">Title:</label>
        <input type="text" name="title" required placeholder="Enter a title..." />

        <label for="language">Language:</label>
        <select name="language" required>
            <option value="Python">Python</option>
            <option value="Java">Java</option>
            <option value="C++">C++</option>
            <option value="PHP">PHP</option>
            <option value="C">C</option>
        </select>

        <label for="code">Code:</label>
        <textarea name="code" rows="8" required placeholder="Paste your code here (Maximum of 2000 characters including spaces and tabs)..."></textarea>
        <button type="submit" class="submit-button">Submit</button>
    </form>
</body>
</html>