<?php
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/includes/config.php';
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>MainPage</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Buy Ticket</h1>

    <?php if (isset($_SESSION['user'])): ?>
        <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user']['full_name']) ?></strong>!</p>
        <p>Your role: <?= $_SESSION['user']['role'] ?></p>
        <a href="/auth/logout.php">Logout</a>
    <?php else: ?>
        <p>Welcome, <strong>Guest</strong>!</p>
        <p>Your role: guest</p>
        <p><a href="/auth/login.php">Login</a> or <a href="/auth/register.php">Signup</a></p>
    <?php endif; ?>

</body>

</html>