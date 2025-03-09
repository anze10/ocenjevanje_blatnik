<?php
session_start();
require 'db_connection.php';

// Check if user is logged in and get user role
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO chats (message, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $message, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: chat.php');
    exit();
}

// Fetch all chats from the database
$query = "SELECT chats.id, chats.message, chats.user_id, users.username FROM chats JOIN users ON chats.user_id = users.id";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='chat-message'>";
        echo "<p><strong>" . htmlspecialchars($row['username']) . ":</strong> " . htmlspecialchars($row['message']) . "</p>";
        
        // If user is admin, show ban and delete options
        if ($user_role == 'admin') {
            echo "<a href='ban_user.php?user_id=" . $row['user_id'] . "'>Ban User</a> | ";
            echo "<a href='delete_chat.php?chat_id=" . $row['id'] . "'>Delete Chat</a>";
        }
        // If user is moderator, show delete option only
        elseif ($user_role == 'moderator') {
            echo "<a href='delete_chat.php?chat_id=" . $row['id'] . "'>Delete Chat</a>";
        }
        
        echo "</div>";
    }
} else {
    echo "No chats available.";
}

$conn->close();
?>

<!-- Form for submitting new messages -->
<form method="POST" action="chat.php">
    <textarea name="message" required></textarea>
    <button type="submit">Send</button>
</form>