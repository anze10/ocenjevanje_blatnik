<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied. Admins only.");
}

require 'db_connection.php';

// Function to promote a user
function promoteUser($userId, $role)
{
    global $conn;
    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $role, $userId);
    return $stmt->execute();
}

// Fetch all users
function fetchAllUsers()
{
    global $conn;
    $sql = "SELECT id, username, role FROM users";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action == 'promote_admin') {
        if (promoteUser($userId, 'admin')) {
            echo "User promoted to admin successfully.";
        } else {
            echo "Failed to promote user to admin.";
        }
    } elseif ($action == 'promote_moderator') {
        if (promoteUser($userId, 'moderator')) {
            echo "User promoted to moderator successfully.";
        } else {
            echo "Failed to promote user to moderator.";
        }
    } elseif ($action == 'demote') {
        if (promoteUser($userId, 'user')) {
            echo "User demoted successfully.";
        } else {
            echo "Failed to demote user.";
        }
    }
}

$users = fetchAllUsers();
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
        <label for="user_id">Select User:</label>
        <select id="user_id" name="user_id" required>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= $user['username'] ?> (<?= $user['role'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="radio" id="promote_admin" name="action" value="promote_admin" required>
        <label for="promote_admin">Promote to Admin</label>
        <br>
        <input type="radio" id="promote_moderator" name="action" value="promote_moderator" required>
        <label for="promote_moderator">Promote to Moderator</label>
        <br>
        <input type="radio" id="demote" name="action" value="demote" required>
        <label for="demote">Demote to User</label>
        <br>
        <input type="submit" value="Submit">
    </form>
</body>

</html>