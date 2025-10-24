<?php
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/includes/config.php';
include 'includes/functions.php';
require_once 'includes/header.php';

?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Main Page</h1>

    <?php if (isset($_SESSION['user'])): ?>
        <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user']['full_name']) ?></strong>!</p>

    <?php else: ?>
        <p>Welcome, <strong>Guest</strong>!</p>
        <!-- <p>Your role: guest</p> -->
    <?php endif; ?>

    <ul>
        <?php if (user_role() === 'guest'): ?>
            <li><a href="/routes/list.php">Search Routes</li>
            <li><a href="/auth/login.php">Login</a></li>
            <li><a href="/auth/register.php">Signup</a></li>

        <?php elseif (user_role() === 'user'): ?>
            <li><a href="/routes/list.php">Search Routes</li>
            <li><a href="my_tickets.php">My Tickets</a></li>
            <li><a href="/auth/logout.php">Logout</a></li>

        <?php elseif (user_role() === 'company'): ?>
            <li><a href="/routes/list.php">Search Routes</li>
            <li><a href="/firm_admin/index.php">Firm Panel</a></li>
            <li><a href="/auth/logout.php">Logout</a></li>

        <?php elseif (user_role() === 'admin'): ?>
            <li><a href="/routes/list.php">Search Routes</li>
            <li><a href="/admin/index.php">Admin Panel</a></li>
            <li><a href="/auth/logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</body>

</html>
<?php require_once 'includes/footer.php'; ?>