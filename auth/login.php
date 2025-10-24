<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: /../index.php');
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0d0d0d;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #0ff;
        }

        .login-container {
            background-color: #272727ff;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            width: 320px;
        }

        h1 {
            margin-bottom: 25px;
            text-shadow: 0 0 10px #0ff;
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            border: none;
            background-color: #1a1a1a;
            color: #0ff;
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 10px #0ff inset;
        }

        button {
            width: 95%;
            padding: 10px;
            margin-top: 15px;
            border-radius: 25px;
            border: none;
            background-color: #0ff;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 0 10px #0ff;
            transition: 0.3s;
        }

        button:hover {
            box-shadow: 0 0 20px #0ff;
            transform: scale(1.05);
        }

        p {
            margin-top: 15px;
            font-size: 14px;
        }

        p a {
            color: #0ff;
            text-decoration: none;
            font-weight: bold;
        }

        p a:hover {
            text-shadow: 0 0 10px #0ff;
        }

        .error-msg {
            margin-top: 10px;
            color: #ff0066;
            font-weight: bold;
            text-shadow: 0 0 8px #ff0066;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Login</h1>
        <form method="POST">
            <input type='email' name='email' placeholder="Email" required><br />
            <input type='password' name='password' placeholder="Password" required><br />
            <button type="submit">Login</button>
        </form>
        <p class="signup-text">
            If you don't have an account, please <a href="register.php">signup.</a>
        </p>
        <?php if (!empty($error)): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </div>
</body>

</html>