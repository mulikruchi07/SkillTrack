<?php
session_start(); // Start the session

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
    $password = $_POST['password']; // Plain text password

    // Check if the username already exists
    $checkUsername = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkUsername);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username already exists
        echo "<script>
                alert('Username already exists!');
                window.location.href = 'index.html'; // Redirect back to register page
              </script>";
    } else {
        // Insert user into the database with the plain-text password
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            // Registration successful, update the session for the newly registered user
            $_SESSION['username'] = $username; // Set the session with the new username
            
            // Show modal and redirect to home
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
                            <h2>Welcome to SkillTrack!</h2>
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
            // Registration failed
            echo "<script>
                    alert('Error: " . $stmt->error . "');
                    window.location.href = 'index.html'; // Redirect back to register page
                  </script>";
        }
    }

    $stmt->close();
}
header("Location: register2.html");
exit();
$conn->close();
?>
