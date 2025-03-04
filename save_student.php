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

// Retrieve data from POST request
$username = $_POST['username'];
$name = $_POST['name'];
$roll_no = $_POST['roll_no'];
$class = $_POST['class'];
$department = $_POST['department'];
$dob = $_POST['dob'];
$college = $_POST['college'];
$interest = $_POST['interest'];

// Check if username exists in users table
$userCheckSql = "SELECT * FROM skilltrack.users WHERE username = '$username'";
$userCheckResult = $conn->query($userCheckSql);

if ($userCheckResult->num_rows > 0) {
    // Insert data into the database
    $sql = "INSERT INTO studentdetails (name, roll_no, class, department, dob, college, interest, username) 
            VALUES ('$name', '$roll_no', '$class', '$department', '$dob', '$college', '$interest', '$username')";


if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
} 
} else {
    echo "Error: Username does not exist in users table.";
}

$conn->close();

header("Location: index.html");
exit();
?>
