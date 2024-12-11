// Fetch the submitted codes and user information using AJAX
window.onload = function () {
    fetchCodes();
    fetchUsers(); // Fetch user data when the page loads
};

function fetchCodes() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "http://127.0.0.1:80/code_den/backend/fetch_codes.php", true);
    xhr.onload = function () {
        console.log("XHR Status: " + xhr.status); // Log the status
        console.log("Response Text: ", xhr.responseText); // Log raw response
        if (xhr.status == 200) {
            try {
                var codes = JSON.parse(xhr.responseText);
                console.log("Codes Data: ", codes); // Log the codes data

                var table = document.getElementById("codesTable");
                if (!table) {
                    console.error("Table with id 'codesTable' not found.");
                    return;
                }

                // Clear existing rows except for the header
                while (table.rows.length > 1) {
                    table.deleteRow(1);
                }

                // Populate the table with code data
                for (var i = 0; i < codes.length; i++) {
                    console.log("Populating row for code: ", codes[i]);
                    var row = table.insertRow();
                    row.innerHTML = `
                        <td>${codes[i].id}</td>
                        <td>${codes[i].user_id}</td>
                        <td>${codes[i].title}</td>
                        <td>${codes[i].language}</td>
                        <td>${codes[i].status}</td>
                        <td>${codes[i].created_at}</td> <!-- New Column for Submitted Data -->
                        <td>
                            <button class="check-btn" onclick="checkCode(${codes[i].id})">Check</button>
                        </td>
                    `;
                }
            } catch (e) {
                console.error("Failed to parse JSON response: ", e);
            }
        } else {
            console.error("Failed to fetch codes: " + xhr.statusText);
        }
    };
    xhr.onerror = function () {
        console.error("Request failed");
    };
    xhr.send();
}

function checkCode(codeId) {
    // Redirect to check_codes.php with the codeId as a query parameter
    window.location.href = `check_codes.php?code_id=${codeId}`;
}


function fetchUsers() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "http://127.0.0.1:80/code_den/backend/fetch_user.php", true);
    xhr.onload = function () {
        console.log("XHR Status: " + xhr.status); // Log the status
        console.log("Response Text: ", xhr.responseText); // Log raw response
        if (xhr.status == 200) {
            try {
                var users = JSON.parse(xhr.responseText);
                console.log("Users Data: ", users); // Log the users data

                var table = document.getElementById("usersTable");
                if (!table) {
                    console.error("Table with id 'usersTable' not found.");
                    return;
                }

                // Clear existing rows except for the header
                while (table.rows.length > 1) {
                    table.deleteRow(1);
                }

                // Populate the table with user data
                for (var i = 0; i < users.length; i++) {
                    console.log("Populating row for user: ", users[i]);
                    var row = table.insertRow();
                    row.innerHTML = `
                        <td>${users[i].id}</td>
                        <td>${users[i].name}</td>
                        <td>${users[i].username}</td>
                        <td>******</td> <!-- Hide password -->
                        <td>${users[i].year_level}</td>
                    `;
                }
            } catch (e) {
                console.error("Failed to parse JSON response: ", e);
            }
        } else {
            console.error("Failed to fetch users: " + xhr.statusText);
        }
    };
    xhr.onerror = function () {
        console.error("Request failed");
    };
    xhr.send();
}
