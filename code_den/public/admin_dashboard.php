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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variable for search
$search_query = '';
$codes = [];

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which button was clicked
    if (isset($_POST['codeSearch'])) {
        $search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';
        
        // Fetch code snippets based on search criteria
        $sql = "SELECT * FROM codes WHERE title LIKE ? OR language LIKE ? OR status LIKE ?";
        $search_param = "%" . $conn->real_escape_string($search_query) . "%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $search_param, $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all matching code snippets
        while ($row = $result->fetch_assoc()) {
            $codes[] = $row;
        }
        
        // Close the statement
        $stmt->close();
    }
}

// Initialize variable for user search
$user_search_query = '';

// Check if the user search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['userSearch'])) {
        $user_search_query = isset($_POST['user_search_query']) ? $_POST['user_search_query'] : '';
        
        // Fetch user information based on search criteria
        $sql = "SELECT * FROM users WHERE name LIKE ? OR username LIKE ?";
        $user_search_param = "%" . $conn->real_escape_string($user_search_query) . "%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user_search_param, $user_search_param);
        $stmt->execute();
        $user_result = $stmt->get_result();

        // Fetch all matching users
        $users = [];
        while ($row = $user_result->fetch_assoc()) {
            $users[] = $row;
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css" />
  </head>
  <body>
    <h1>ADMIN DASHBOARD</h1>

    <div class="dashboard-container">
        <div class="codes-section">
            <h3>Code Snippets</h3>
            <form action="" method="POST">
                <div class="search-form">
                    <input type="text" name="search_query" class="search-input" placeholder="Search Code Snippets..." value="<?php echo htmlspecialchars($search_query); ?>" />
                    <button type="submit" name="codeSearch" class="search-button">Search</button>
                </div>
            </form>
            <table id="codesTable">
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Title</th>
                    <th>Language</th>
                    <th>Status</th>
                    <th>Submitted Data</th>
                    <th>Actions</th>
                </tr>
            </table>
        </div>

      <div class="users-section">
        <h3>User Information</h3>
        <div class="search-form">
          <input type="text" id="userSearch" class="search-input" placeholder="Search Users..." />
          <button id="userSearchBtn" class="search-button">Search</button>
        </div>
        <table id="usersTable">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Username</th>
            <th>Password</th>
            <th>Year Level</th>
          </tr>
          <!-- Data rows will be dynamically generated here -->
        </table>
      </div>
    </div>

    <!-- Homepage Button -->
    <a href="index.html" class="homepage-btn">Log Out</a>
    <script src="js/admin.js" defer></script>
  </body>
</html>