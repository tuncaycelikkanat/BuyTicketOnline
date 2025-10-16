<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $db->prepare("INSERT INTO Users (id, full_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $uuid = uuid();
        $stmt->execute([$uuid, $full_name, $email, $password, 'user']);
        echo "<p style='color:green'>I Hope The Registeration Is Done! <a href='login.php'>Login</a></p>";
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'UNIQUE')) {
            echo "<p style='color:red'>This email address has already used!</p>";
        } else {
            echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <h1>Register</h1>
    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Signup</button>
    </form>
    <p>If you have already an account, please <a href="login.php">login.</a></p>
</body>

</html>