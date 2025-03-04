<?php
// Database connection details
$host = "localhost";
$username = "root";
$password = "";
$dbname = "skilltrack";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Raw password entered by the user

    // Fetch user from the database by username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Compare the plain text password
        if ($password === $user['password']) {
            session_start();
            $_SESSION['username'] = $username; // Store the username in the PHP session

            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Store username in sessionStorage for client-side use
                        sessionStorage.setItem('username', '$username');

                        // Create overlay and modal for welcome message
                        let overlay = document.createElement('div');
                        overlay.style.position = 'fixed';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100%';
                        overlay.style.height = '100%';
                        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                        overlay.style.zIndex = '9998';
                        
                        let modal = document.createElement('div');
                        modal.style.position = 'fixed';
                        modal.style.top = '50%';
                        modal.style.left = '50%';
                        modal.style.transform = 'translate(-50%, -50%)';
                        modal.style.padding = '20px';
                        modal.style.backgroundColor = 'white';
                        modal.style.borderRadius = '10px';
                        modal.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                        modal.style.textAlign = 'center';
                        modal.style.zIndex = '9999';

                        modal.innerHTML = `
                            <h2>Welcome to SkillTrack, $username!</h2>
                            <button onclick='closeModal()' style='
                                padding: 10px 20px;
                                background-color: #4CAF50;
                                color: white;
                                border: none;
                                cursor: pointer;
                                border-radius: 5px;'>OK</button>
                        `;

                        document.body.appendChild(overlay);
                        document.body.appendChild(modal);

                        window.closeModal = function() {
                            modal.style.display = 'none';
                            overlay.style.display = 'none';
                            window.location.href = 'home.html'; // Redirect to home after success
                        };
                    });
                  </script>";
        } else {
            // Handle invalid password
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let overlay = document.createElement('div');
                        overlay.style.position = 'fixed';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100%';
                        overlay.style.height = '100%';
                        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                        overlay.style.zIndex = '9998';
                        
                        let modal = document.createElement('div');
                        modal.style.position = 'fixed';
                        modal.style.top = '50%';
                        modal.style.left = '50%';
                        modal.style.transform = 'translate(-50%, -50%)';
                        modal.style.padding = '20px';
                        modal.style.backgroundColor = 'white';
                        modal.style.borderRadius = '10px';
                        modal.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                        modal.style.textAlign = 'center';
                        modal.style.zIndex = '9999';

                        modal.innerHTML = `
                            <h2>Invalid Password!</h2>
                            <button onclick='closeModal()' style='
                                padding: 10px 20px;
                                background-color: #4CAF50;
                                color: white;
                                border: none;
                                cursor: pointer;
                                border-radius: 5px;'>OK</button>
                        `;

                        document.body.appendChild(overlay);
                        document.body.appendChild(modal);

                        window.closeModal = function() {
                            modal.style.display = 'none';
                            overlay.style.display = 'none';
                            window.location.href = 'index.html'; // Redirect to index after error
                        };
                    });
                  </script>";
        }
    } else {
        // Handle no user found
        echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let overlay = document.createElement('div');
                        overlay.style.position = 'fixed';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100%';
                        overlay.style.height = '100%';
                        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                        overlay.style.zIndex = '9998';
                        
                        let modal = document.createElement('div');
                        modal.style.position = 'fixed';
                        modal.style.top = '50%';
                        modal.style.left = '50%';
                        modal.style.transform = 'translate(-50%, -50%)';
                        modal.style.padding = '20px';
                        modal.style.backgroundColor = 'white';
                        modal.style.borderRadius = '10px';
                        modal.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                        modal.style.textAlign = 'center';
                        modal.style.zIndex = '9999';

                        modal.innerHTML = `
                            <h2>No user found with this username!</h2>
                            <button onclick='closeModal()' style='
                                padding: 10px 20px;
                                background-color: #4CAF50;
                                color: white;
                                border: none;
                                cursor: pointer;
                                border-radius: 5px;'>OK</button>
                        `;

                        document.body.appendChild(overlay);
                        document.body.appendChild(modal);

                        window.closeModal = function() {
                            modal.style.display = 'none';
                            overlay.style.display = 'none';
                            window.location.href = 'index.html'; // Redirect to index after error
                        };
                    });
                  </script>";
    }

    $stmt->close();
}

$conn->close();
?>
