<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $db->prepare("INSERT INTO Users (id, full_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $uuid = uuid();
        $stmt->execute([$uuid, $full_name, $email, $password, 'user']);
        echo "<p style='color:#0f0; text-align:center;'>Registration successful! <a style='color:#0ff;' href='login.php'>Login</a></p>";
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'UNIQUE')) {
            echo "<p style='color:#f00; text-align:center;'>This email is already in use!</p>";
        } else {
            echo "<p style='color:#f00; text-align:center;'>Error: " . $e->getMessage() . "</p>";
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

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0d0d0d;
            color: #0ff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            text-align: center;
        }

        main {
            flex: 1;
            padding: 60px 20px;
        }

        h1 {
            color: #00eaff;
            text-shadow: 0 0 10px #00eaff;
        }

        form {
            margin-top: 20px;
        }

        input {
            width: 260px;
            padding: 12px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #00eaff;
            background: #111;
            color: #0ff;
            text-align: center;
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 8px #00eaff;
        }

        button {
            padding: 12px 25px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            background-color: #00eaff;
            color: #000;
            font-weight: bold;
            transition: 0.3s;
            box-shadow: 0 0 8px #00eaff;
        }

        button:hover {
            background-color: #0088aa;
            color: #fff;
            box-shadow: 0 0 14px #00eaff;
            transform: scale(1.05);
        }

        a {
            color: #0ff;
        }

        a:hover {
            color: #fff;
        }

        footer {
            background: #111;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #00eaff;
            box-shadow: 0 0 10px #00eaff;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <main>
        <h1>Register</h1>

        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Signup</button>
        </form>

        <p>If you already have an account, <a href="login.php">login here.</a></p>
    </main>

    <footer>
        © 2025 Bus Ticket System
    </footer>

</body>

</html>