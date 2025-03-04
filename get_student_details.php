<?php
// Database connection
$host = 'localhost';
$db = 'skilltrack_studentdetails';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Retrieve username from GET request
$username = $_GET['username'];

// Fetch user details from studentdetails table
$sql = "SELECT name, roll_no, class, department, dob, college, interest FROM studentdetails WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
    echo json_encode($userData); // Return user data as JSON
} else {
    echo json_encode([]); // Return an empty array if no data found
}

$conn->close();
?>
