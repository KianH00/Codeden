<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'] ?? 'Guest';

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

// Initialize an empty array for posted codes
$posted_codes = [];

// Check if a search query is present
$search_query = isset($_GET['query']) ? $_GET['query'] : '';
$category_query = isset($_GET['category']) ? $_GET['category'] : '';
$language_query = isset($_GET['language']) ? $_GET['language'] : '';

// Prepare the SQL query based on category or search
if ($category_query) {
    // If a category is selected, filter by category
    $stmt = $conn->prepare("SELECT * FROM posted_codes WHERE code_tags LIKE ? ORDER BY created_at DESC");
    $category_param = "%" . $category_query . "%"; // Allow for partial matches
    $stmt->bind_param("s", $category_param);
} elseif ($search_query) {
    // If a search query is present, filter by title
    $stmt = $conn->prepare("SELECT * FROM posted_codes WHERE title LIKE ? ORDER BY created_at DESC");
    $search_param = "%" . $search_query . "%"; // Allow for partial matches
    $stmt->bind_param("s", $search_param);
} elseif ($language_query) {
    $stmt = $conn->prepare("SELECT * FROM posted_codes WHERE language LIKE ? ORDER BY created_at DESC");
    $language_param = "%" . $language_query . "%"; // Allow for partial matches
    $stmt->bind_param("s", $language_param);
} else {
    // Default query to fetch all codes if no category or search query
    $stmt = $conn->prepare("SELECT * FROM posted_codes ORDER BY created_at DESC");
}

// Fetch tutorial links based on category and language
$tutorial_links = [];
if ($category_query) {
    // If a category is selected, filter by category
    $tutorial_stmt = $conn->prepare("SELECT title, url FROM tutorial_links WHERE tags LIKE ?"); 
    $category_param = "%" . $category_query . "%"; // Allow for partial matches
    $tutorial_stmt->bind_param("s", $category_param);
} elseif ($language_query) {
    // If a language is selected, filter by language
    $tutorial_stmt = $conn->prepare("SELECT title, url FROM tutorial_links WHERE language = ?");
    $tutorial_stmt->bind_param("s", $language_query);
} else {
    // Default query to fetch all tutorial links if no category or language is selected
    $tutorial_stmt = $conn->prepare("SELECT title, url FROM tutorial_links");
}

$tutorial_stmt->execute();
$tutorial_result = $tutorial_stmt->get_result();

if ($tutorial_result->num_rows > 0) {
    while ($row = $tutorial_result->fetch_assoc()) {
        $tutorial_links[] = $row;
    }
}

$tutorial_stmt->execute();
$tutorial_result = $tutorial_stmt->get_result();

if ($tutorial_result->num_rows > 0) {
    while ($row = $tutorial_result->fetch_assoc()) {
        $tutorial_links[] = $row;
    }
}

// Execute the prepared statement
$stmt->execute();
$result = $stmt->get_result();

// Fetch results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posted_codes[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Homepage</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="css/user.css" />
  </head>

  <body>
    <div class="wrapper">
      <aside id="sidebar">
        <div class="d-flex">
          <button class="toggle-btn" type="button">
            <i class="lni lni-grid-alt"></i>
          </button>
          <div class="sidebar-logo">
            <a href="#">CodeDen</a>
          </div>
        </div>
        <ul class="sidebar-nav">
          <li class="sidebar-item">
            <a href="profile.php" class="sidebar-link">
              <i class="lni lni-user"></i>
              <span>Profile</span>
            </a>
          </li>

          <li class="sidebar-item">
            <a
              href="#"
              class="sidebar-link collapsed has-dropdown"
              data-bs-toggle="collapse"
              data-bs-target="#auth"
              aria-expanded="false"
              aria-controls="auth"
            >
              <i class="lni lni-protection"></i>
              <span>Language</span>
            </a>
            <ul
              id="auth"
              class="sidebar-dropdown list-unstyled collapse"
              data-bs-parent="#sidebar"
            >
              <li class="sidebar-item">
                <form action="homep.php" method="GET">
                  <input type="hidden" name="language" value="Python">
                  <button type="submit" class="btn">Python</button>
                </form>
              </li>
              <li class="sidebar-item">
                <form action="homep.php" method="GET">
                  <input type="hidden" name="language" value="Java">
                  <button type="submit" class="btn">Java</button>
                </form>
              </li>
              <li class="sidebar-item">
                <form action="homep.php" method="GET">
                  <input type="hidden" name="language" value="C">
                  <button type="submit" class="btn">C</button>
                </form>
              </li>
              <li class="sidebar-item">
                <form action="homep.php" method="GET">
                  <input type="hidden" name="language" value="C++">
                  <button type="submit" class="btn">C++</button>
                </form>
              </li>
              <li class="sidebar-item">
                <form action="homep.php" method="GET">
                  <input type="hidden" name="language" value="PHP">
                  <button type="submit" class="btn">PHP</button>
                </form>
              </li>
            </ul>
          </li>
          <li class="sidebar-item">
            <a href="submit.php" class="sidebar-link">
              <i class="lni lni-upload"></i>
              <span>Submit Code</span>
            </a>
            <ul
              id="auth"
              class="sidebar-dropdown list-unstyled collapse"
              data-bs-parent="#sidebar"
            >
              <li class="sidebar-item">
                <a
                  href="submit.php"
                  class="sidebar-link"
                  >Submit Code</a
                >
              </li>
            </ul>
          </li>
          <li class="sidebar-item">
            <a href="saved_codes.php" class="sidebar-link">
              <i class="lni lni-save"></i>
              <span>Saved Code</span>
            </a>
          </li>
        </ul>
        <div class="sidebar-footer">
          <a href="index.html" class="sidebar-link">
            <i class="lni lni-exit"></i>
            <span>Logout</span>
          </a>
        </div>
      </aside>
      <aside id="other-sidebar">

      </aside>
      <div class="main p-3">
        <div class="text-center">
          <h1>Welcome to CodeDen, <?php echo htmlspecialchars($username); ?>!!!</h1>
        </div>
        <form action="homep.php" method="GET" class="search-form mt-3">
          <input type="text" name="query" placeholder="Search the title of the code..." class="search-input">
          <button type="submit" class="search-button">Search</button>
        </form>
        <div class="d-flex justify-content-center">
          <form action="homep.php" method="GET">
            <input type="hidden" name="category" value="Looping">
            <button type="submit" class="btn btn-primary mx-2">Looping</button>
          </form>
          <form action="homep.php" method="GET">
            <input type="hidden" name="category" value="Conditional Statements">
            <button type="submit" class="btn btn-primary mx-2">Conditional Statements</button>
          </form>
          <form action="homep.php" method="GET">
            <input type="hidden" name="category" value="Variable and Data Type">
            <button type="submit" class="btn btn-primary mx-2">Variable and Data Type</button>
          </form>
          <form action="homep.php" method="GET">
            <input type="hidden" name="category" value="Operators">
            <button type="submit" class="btn btn-primary mx-2">Operations</button>
          </form>
          <form action="homep.php" method="GET">
            <input type="hidden" name="category" value="Functions and Method">
            <button type="submit" class="btn btn-primary mx-2">Functions and Methods</button>
          </form>
        </div>
        <div class="tutorial-links-container mt-3">
          <h2>Tutorial Links</h2>
          <div class="links-container">
            <ul>
              <?php foreach ($tutorial_links as $link): ?>
                <li>
                  <div class="link-wrapper">
                    <a href="<?php echo htmlspecialchars($link['url']); ?>"><?php echo htmlspecialchars($link['title']); ?></a>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="posted-codes-container">
        <div class="posted-codes">
            <div class="codes-wrapper">
                <?php foreach ($posted_codes as $code): ?>
                    <div class="code-container">
                        <h3><?php echo htmlspecialchars($code['title']); ?></h3>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($code['username']); ?></p>
                        <p><strong>Language:</strong> <?php echo htmlspecialchars($code['language']); ?></p>
                        <p><strong>Posted Date:</strong> <?php echo htmlspecialchars($code['created_at']); ?></p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                          <p><strong>Code:</strong></p>
                          <button class="copy-button" data-code-id="<?= $code['id'] ?>">Copy</button>
                        </div>
                        <pre id="code-<?= $code['id'] ?>"><?= htmlspecialchars($code['code_file']) ?></pre>
                        <form action="saved_codes.php" method="POST">
                          <input type="hidden" name="code_id" value="<?= $code['id'] ?>">
                          <input type="hidden" name="title" value="<?= htmlspecialchars($code['title']) ?>">
                          <input type="hidden" name="language" value="<?= htmlspecialchars($code['language']) ?>">
                          <input type="hidden" name="code_file" value="<?= htmlspecialchars($code['code_file']) ?>">
                          <button type="submit" class="saved-button">Saved Code</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
      </div>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
      crossorigin="anonymous"
    ></script>
    <script src="js/user.js"></script>
  </body>
</html>
