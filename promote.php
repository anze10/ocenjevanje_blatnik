<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied. Admins only.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to promote a user
function promoteUser($userId)
{
    global $conn;
    $sql = "UPDATE users SET role = 'admin' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    return $stmt->execute();
}

// Function to demote a user
function demoteUser($userId)
{
    global $conn;
    $sql = "UPDATE users SET role = 'user' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    return $stmt->execute();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action == 'promote') {
        if (promoteUser($userId)) {
            echo "User promoted successfully.";
        } else {
            echo "Failed to promote user.";
        }
    } elseif ($action == 'demote') {
        if (demoteUser($userId)) {
            echo "User demoted successfully.";
        } else {
            echo "Failed to demote user.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Promote/Demote Users</title>
</head>

<body>
    <h1>Promote or Demote Users</h1>
    <form method="post" action="">
        <label for="user_id">User ID:</label>
        <input type="number" id="user_id" name="user_id" required>
        <br>
        <input type="radio" id="promote" name="action" value="promote" required>
        <label for="promote">Promote</label>
        <br>
        <input type="radio" id="demote" name="action" value="demote" required>
        <label for="demote">Demote</label>
        <br>
        <input type="submit" value="Submit">
    </form>
</body>

</html>