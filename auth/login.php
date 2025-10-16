<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: /../index.php');
        exit;
    } else {
        echo "<p style='color=red'>Invalid email or password</p>";
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
    <h1>Login</h1>
    <form method="POST">
        <input type='email' name='email' placeholder="Email" required><br />
        <input type='password' name='password' placeholder="Password" required><br />
        <button type="submit">Login</button>
    </form>
    <p>If you don't have an account, please <a href="register.php">signup.</a></p>
</body>

</html>