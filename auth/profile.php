<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';

if (!is_login())
    header('Location: /auth/login.php');

// Get live user balance from DB
$stmt = $db->prepare("SELECT * FROM Users WHERE id = ?");
$stmt->execute([$_SESSION['user']['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0b0f19;
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            text-align: center;
        }

        main {
            flex: 1;
            padding: 60px 20px 80px 20px;
        }

        h1 {
            color: #00eaff;
            text-shadow: 0 0 10px #00eaff;
            margin-bottom: 25px;
        }

        .info-box {
            background: rgba(0, 0, 0, 0.45);
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.4);
            padding: 20px;
            width: 90%;
            max-width: 500px;
            margin: 0 auto 25px auto;
            border-radius: 10px;
            text-align: left;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-box li {
            margin: 8px 0;
        }

        a.btn-green,
        a.btn-blue {
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: none;
            margin: 10px auto;
        }

        a.btn-green {
            background: #00ce00ff;
            color: #fff;
        }

        a.btn-green:hover {
            background: #009100ff;
        }

        a.btn-blue {
            background: #00eaff;
            color: #000;
        }

        a.btn-blue:hover {
            background: #00b6cc;
        }
    </style>
</head>

<body>
    <main>
        <h1>My Profile</h1>

        <div class="info-box">
            <ul>
                <li>ID: <strong><?= htmlspecialchars($user['id']) ?></strong></li>
                <li>Full Name: <strong><?= htmlspecialchars($user['full_name']) ?></strong></li>
                <li>Email: <strong><?= htmlspecialchars($user['email']) ?></strong></li>
                <li>Role: <strong><?= htmlspecialchars($user['role']) ?></strong></li>
                <li class="password-joke">Password: <span class="hidden-password">Just a joke!</span></li>
                <li>Company: <strong><?= htmlspecialchars($user['company_id'] ?? "-") ?></strong></li>
                <li>Balance: <strong><?= htmlspecialchars($user['balance']) ?> ₺</strong></li>
                <li>Created At: <strong><?= htmlspecialchars($user['created_at']) ?></strong></li>
            </ul>
        </div>

        <a class="btn-green" href="/tickets/my_tickets.php">My Tickets</a>
    </main>

    <?php require_once '../includes/footer.php'; ?>
</body>

</html>