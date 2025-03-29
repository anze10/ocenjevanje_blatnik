<?php
require 'vendor/autoload.php';
require 'db_connection.php';
use DamBal\VercelBlob\VercelBlobClient;
$client = new VercelBlobClient(getenv('BLOB_READ_WRITE_TOKEN'));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT username FROM users WHERE username = '$username'");

    if ($result->num_rows > 0) {
        echo "Username already exists.";
    } else {
        $conn->query("INSERT INTO users (username, password) VALUES ('$username', '$password')");
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <form action="" method="post">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Register</button>
        <a href="index.php">Login</a>
    </form>
</body>

</html>