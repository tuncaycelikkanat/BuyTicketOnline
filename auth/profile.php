<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

if (!is_login())
    header('Location: /auth/login.php');
require_role(['user', 'company']);

require_once '../includes/header.php';

// Get live user balance from DB
$stmt = $db->prepare("SELECT * FROM Users WHERE id = ?");
$stmt->execute([$_SESSION['user']['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//add balance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);
    if ($amount > 0) {
        $newBalance = $user['balance'] + $amount;
        $update = $db->prepare("UPDATE Users SET balance = ? WHERE id = ?");
        $update->execute([$newBalance, $user['id']]);
        $user['balance'] = $newBalance;
    }
}

$stmt = $db->prepare("
    SELECT Users.*, Bus_Company.name
    FROM Users
    LEFT JOIN Bus_Company ON Bus_Company.id = Users.company_id
    WHERE Users.id = ?
");
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

        .balance-form {
            margin: 20px 0;
        }

        .balance-form input {
            width: 250px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #00eaff;
            background: #111;
            color: #0ff;
            text-align: center;
            box-shadow: 0 0 8px #00eaff;
        }

        .balance-form input:focus {
            outline: none;
            box-shadow: 0 0 12px #00eaff;
        }

        .balance-form button {
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: bold;
            margin-left: 10px;
            cursor: pointer;
            border: none;
            background: #00eaff;
            color: #000;
            transition: 0.3s;
            box-shadow: 0 0 8px #00eaff;
        }

        .balance-form button:hover {
            background: #00b6cc;
            color: #fff;
            box-shadow: 0 0 14px #00eaff;
            transform: scale(1.05);
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
                <li>Company: <strong><?= htmlspecialchars($user['name'] ?? "-") ?></strong></li>
                <li>Balance: <strong><?= htmlspecialchars($user['balance']) ?> ₺</strong></li>
                <li>Created At: <strong><?= htmlspecialchars($user['created_at']) ?></strong></li>
            </ul>
        </div>

        <a class="btn-green" href="/tickets/my_tickets.php">My Tickets</a>

        <form method="POST" class="balance-form">
            <input type="number" step="0.01" name="amount" placeholder="Balance Amount (₺)" required>
            <button type="submit" class="btn-blue">Add Balance</button>
        </form>
    </main>

    <?php require_once '../includes/footer.php'; ?>
</body>

</html>