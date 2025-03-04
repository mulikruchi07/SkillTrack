<?php
session_start(); // Start the session

// Database connection settings
$host = 'localhost'; // Change if your host is different
$dbName = 'skilltrack_skills'; // Database name
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the logged-in username from the session
    $loggedInUser = isset($_SESSION['username']) ? $_SESSION['username'] : '';

    // Check if the request method is POST (for adding or editing skills)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $skill_name = $_POST['skillName'];
        $category = $_POST['category'];
        $progress = $_POST['progress'];
        $organization = $_POST['organization'];
        $credits = $_POST['credits'];
        $issueDate = $_POST['issueDate'];

        // Handle file upload for certificate
        $certificate = '';
        if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
            $certificate = $_FILES['certificate']['name'];
            $target_dir = "uploads/";  // Directory to store uploaded certificates
            $target_file = $target_dir . basename($certificate);

            // Move uploaded file to 'uploads' folder
            if (move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
                $certificate = $target_file;  // Store the file path in the variable
            } else {
                echo "Error uploading the certificate file.";
            }
        }

        // Handle the edit action (update an existing skill)
        if (isset($_GET['action']) && $_GET['action'] === 'edit') {
            $skillId = $_POST['skillId']; // Get skill ID

            // Update the skill in the database
            $stmt = $pdo->prepare("UPDATE skills SET skill_name = ?, category = ?, progress = ?, organization = ?, credits = ?, issueDate = ? WHERE id = ?");
            $stmt->execute([$skill_name, $category, $progress, $organization, $credits, $issueDate, $skillId]);

            if ($stmt->rowCount() > 0) {
                echo "Skill updated successfully!";
            } else {
                echo "Error updating skill.";
            }
        }
        // Insert skill data into the database, including the certificate path (for adding a new skill)
        else if ($loggedInUser) {
            // Prepare and execute the insert statement
            $stmt = $pdo->prepare("INSERT INTO skills (username, skill_name, category, progress, organization, credits, issueDate, certificate_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$loggedInUser, $skill_name, $category, $progress, $organization, $credits, $issueDate, $certificate]);
            echo "Skill added successfully!";
        } else {
            echo "User not logged in.";
        }
    }

    // Handle delete request for skills
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $skill_id = $_GET['id'];

        // Retrieve the file path of the certificate from the database
        $stmt = $pdo->prepare("SELECT certificate_path FROM skills WHERE id = ?");
        $stmt->execute([$skill_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $certificate_path = $row['certificate_path'];

            // Delete the certificate file from the server if it exists
            if (file_exists($certificate_path)) {
                unlink($certificate_path);
            }

            // Prepare and execute the delete statement
            $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
            $stmt->execute([$skill_id]);

            if ($stmt->rowCount() > 0) {
                echo "Skill deleted successfully!";
            } else {
                echo "Error deleting skill.";
            }
        }
    }

    // Handle view action (fetch a single skill by ID)
    if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
        $skillId = $_GET['id'];

        // Fetch skill details from the database
        $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
        $stmt->execute([$skillId]);
        $skill = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($skill) {
            echo json_encode($skill); // Return the skill data in JSON format
        } else {
            echo json_encode(['error' => 'Skill not found']);
        }
    }

    // Handle fetching skills for the logged-in user
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
        if ($loggedInUser) {
            // Prepare and execute the fetch statement
            $stmt = $pdo->prepare("SELECT * FROM skills WHERE username = ?");
            $stmt->execute([$loggedInUser]);
            $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($skills); // Return skills as JSON
        } else {
            echo "User not logged in.";
        }
    }
} catch (PDOException $e) {
    // Handle database connection errors
    echo "Database error: " . $e->getMessage();
}
