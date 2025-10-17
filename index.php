<?php
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/includes/config.php';
include 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>MainPage</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Main Page</h1>

    <?php if (isset($_SESSION['user'])): ?>
        <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user']['full_name']) ?></strong>!</p>
        <p>Your role: <?= user_role() ?></p>
        <!-- <a href="/auth/logout.php">Logout</a> -->

    <?php else: ?>
        <p>Welcome, <strong>Guest</strong>!</p>
        <p>Your role: guest</p>
        <!-- <p><a href="/auth/login.php">Login</a> or <a href="/auth/register.php">Signup</a></p> -->
    <?php endif; ?>

    <ul>
        <?php if (user_role() === 'guest'): ?>
            <li><a href="/auth/login.php">Login</a></li>
            <li><a href="/auth/register.php">Signup</a></li>

        <?php elseif (user_role() === 'user'): ?>
            <li><a href="routes.php">Search</a></li>
            <li><a href="my_tickets.php">My Tickets</a></li>
            <li><a href="/auth/profile.php">My Account</a></li>
            <li><a href="/auth/logout.php">Logout</a></li>

        <?php elseif (user_role() === 'company'): ?>
            <li><a href="firma_panel.php">Firm Panel</a></li>
            <li><a href="/auth/profile.php">My Account</a></li>
            <li><a href="/auth/logout.php">Logout</a></li>

        <?php elseif (user_role() === 'admin'): ?>
            <li><a href="/admin/index.php">Admin Panel</a></li>
            <li><a href="/auth/logout.php">Logout</a></li>
            <!-- <li><a href="/test/testSession.php">Test Session</a></li> -->
        <?php endif; ?>
    </ul>
</body>

</html>