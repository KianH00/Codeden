<?php
include('connect.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($password)) {
        echo "Username and password are required!";
    } else {
        // Hardcoded admin credentials
        $admin_username = "admin"; // Set your admin username
        $admin_password = "admin123"; // Set your admin password

        // Check if the provided credentials match the hardcoded admin credentials
        if ($username === $admin_username && $password === $admin_password) {
            session_start();
            $_SESSION['user_id'] = 0; // You can set a specific ID for admin
            $_SESSION['username'] = $admin_username; // Store admin username
            $_SESSION['role'] = 'admin'; // Set role as admin
            header("Location: /code_den/public/admin_dashboard.php");
            exit();
        } else {
            // Query to check if the username exists in the database
            $sql = "SELECT id, username, password FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }

            // Bind the parameters to the SQL query
            $stmt->bind_param("s", $username);

            // Execute the prepared statement
            if ($stmt->execute() === false) {
                die('Execute failed: ' . htmlspecialchars($stmt->error));
            }

            $stmt->store_result();

            // Check if any user exists with the given username
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $fetchedusername, $storedpassword);
                $stmt->fetch();

                if (password_verify($password, $storedpassword)) {
                    session_start();
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $fetchedusername; // Store username
                    header("Location: /code_den/public/homep.php");
                    exit();
                } else {
                    echo "<script>alert('Username or password is incorrect.');</script>";
                    header("Location: /code_den/public/index.html");
                }
            } else {
                echo "<script>alert('Username or password is incorrect.');</script>";
                header("Location: /code_den/public/index.html");
            }
            
            $stmt->close();
        }
    }
}
$conn->close();
?>
