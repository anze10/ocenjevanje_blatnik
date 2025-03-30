<?php

session_start();
require 'db_connection.php';
// if (!isset($_SESSION['user_id'])) {
//     header('Location: index.php');
//     exit();
// }

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];


function deleteChat($chat_id)
{
    global $conn;

    $conn->query("DELETE FROM chats WHERE id = $chat_id");
}

function banUser($user_id)
{
    global $conn;

    $conn->query("DELETE FROM chats WHERE user_id = $user_id");

    $conn->query("DELETE FROM users WHERE id = $user_id");
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $conn->query("INSERT INTO chats (message, user_id) VALUES ('$message', $user_id)");
    header('Location: chat.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['delete_chat_id']) && ($user_role == 'admin' || $user_role == 'moderator')) {
        deleteChat($_GET['delete_chat_id']);
        header('Location: chat.php');
        exit();
    }
    if (isset($_GET['ban_user_id']) && $user_role == 'admin') {
        banUser($_GET['ban_user_id']);
        header('Location: chat.php');
        exit();
    }
}


$query = "SELECT chats.id, chats.message, chats.user_id, users.username FROM chats JOIN users ON chats.user_id = users.id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .chat-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-height: 500px;
            overflow-y: auto;
        }

        .chat-message {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }

        .chat-message strong {
            color: #007bff;
        }

        .chat-actions a {
            color: red;
            margin-left: 10px;
            text-decoration: none;
            font-size: 0.9em;
        }

        .chat-actions a:hover {
            text-decoration: underline;
        }

        .message-form {
            margin-top: 10px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        textarea {
            width: 90%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="chat-container">
        <h2>Chat</h2>
        <a href="chat.php?logout=true" style="color: red; text-decoration: none; font-size: 0.9em;">Logout</a>
        <?php if ($user_role == 'admin'): ?>
            <a href="promote.php" style="color: blue; text-decoration: none; font-size: 0.9em;">Promote Users</a>
        <?php endif; ?>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='chat-message'>";
                echo "<p><strong>" . htmlspecialchars($row['username']) . ":</strong> " . htmlspecialchars($row['message']) . "</p>";
                echo "<div class='chat-actions'>";
                if ($user_role == 'admin') {
                    echo "<a href='chat.php?ban_user_id=" . $row['user_id'] . "'>Ban</a> | ";
                    echo "<a href='chat.php?delete_chat_id=" . $row['id'] . "'>Delete</a>";
                } elseif ($user_role == 'moderator') {
                    echo "<a href='chat.php?delete_chat_id=" . $row['id'] . "'>Delete</a>";
                }
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No chats available.</p>";
        }
        ?>
    </div>
    <form class="message-form" method="POST" action="chat.php">
        <textarea name="message" placeholder="Type your message..." required></textarea>
        <button type="submit">Send</button>
    </form>
</body>

</html>