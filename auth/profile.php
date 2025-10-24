<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h1>My Profile</h1>
    <ul>
        <li>ID: <strong><?= htmlspecialchars($_SESSION['user']['id']) ?></strong></li>
        <li>Full Name: <strong><?= htmlspecialchars($_SESSION['user']['full_name']) ?></strong></li>
        <li>Email: <strong><?= htmlspecialchars($_SESSION['user']['email']) ?></strong></li>
        <li>Role: <strong><?= htmlspecialchars($_SESSION['user']['role']) ?></strong></li>
        <li class="password-joke">
            Password:
            <span class="hidden-password">Just a joke!</span>
        </li>
        <li>Company: <strong><?= htmlspecialchars($_SESSION['user']['company_id'] ?? "-") ?></strong></li>
        <li>Balance: <strong><?= htmlspecialchars($_SESSION['user']['balance'] . "$") ?></strong></li>
        <li>Created At: <strong><?= htmlspecialchars($_SESSION['user']['created_at']) ?></strong></li> <br />
    </ul>
</body>

</html>

<?php require_once '../includes/footer.php'; ?>