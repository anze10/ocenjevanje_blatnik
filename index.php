<?php
// Start the session
session_start();
$servername = "localhost";
$dbname = "your_database_name";
$dbusername = "your_database_username";
$dbpassword = "your_database_password";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dummy credentials for demonstration
    // Get the submitted form data
    $submittedUsername = $_POST['username'];
    $submittedPassword = $_POST['password'];

    // Prepare and execute the query to fetch user data
    $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = :username");
    $stmt->bindParam(':username', $submittedUsername);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validate the credentials
    if ($user && password_verify($submittedPassword, $user['password'])) {
        // Set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];

        // Redirect to chat.php
        header("Location: chat.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }

    // Get the submitted form data
    $submittedUsername = $_POST['username'];
    $submittedPassword = $_POST['password'];

    // Validate the credentials
    if ($submittedUsername == $username && $submittedPassword == $password) {
        // Set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        // Redirect to chat.php
        header("Location: chat.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h2>Login Page</h2>
    <?php
    if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    }
    ?>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>

</html>