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
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #0d0d0d;
            margin: 0;
            font-family: Arial, sans-serif;
            color: #0ff;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            text-shadow: 0 0 10px #0ff;
        }

        .content p {
            color: #0ff;
            text-shadow: 0 0 5px #0ff;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
            width: 200px;
        }

        ul li {
            margin: 12px 0;
        }

        ul li a {
            display: block;
            padding: 12px;
            border-radius: 25px;
            text-decoration: none;
            margin-top: 20px;
            color: #0ff;
            font-weight: bold;
            background-color: #272727ff;
            box-shadow: 0 0 6px #0ff;
            transition: 0.3s;
        }

        ul li a:hover {
            color: #000;
            background-color: #0ff;
            box-shadow: 0 0 12px #0ff, 0 0 25px #0ff;
            transform: scale(1.1);
        }

        .logout {
            display: block;
            padding: 12px;
            border-radius: 25px;
            text-decoration: none;
            margin-top: 20px;
            color: rgba(179, 0, 0, 1);
            font-weight: bold;
            background-color: #272727ff;
            box-shadow: 0 0 6px rgba(255, 0, 0, 1);
            transition: 0.3s;
        }

        .logout:hover {
            color: #fff;
            background-color: rgba(179, 0, 0, 1);
            box-shadow: 0 0 12px rgba(255, 0, 0, 1);
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <div class="content">
        <h1>Main Page</h1>

        <?php if (isset($_SESSION['user'])): ?>
            <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user']['full_name']) ?></strong>!</p>

        <?php else: ?>
            <p>Welcome, <strong>Guest</strong>!</p>
            <!-- <p>Your role: guest</p> -->
        <?php endif; ?>

        <ul>
            <ul>
                <?php if (user_role() === 'guest'): ?>
                    <li><a href="/routes/list.php">Search Routes</a></li>
                    <li><a href="/auth/login.php">Login</a></li>
                    <li><a href="/auth/register.php">Signup</a></li>
                <?php elseif (user_role() === 'user'): ?>
                    <li><a href="/routes/list.php">Search Routes</a></li>
                    <li><a href="/tickets/my_tickets.php">My Tickets</a></li>
                    <li><a href="/auth/logout.php" class="logout">Logout</a></li>
                <?php elseif (user_role() === 'company'): ?>
                    <li><a href="/routes/list.php">Search Routes</a></li>
                    <li><a href="/firm_admin/index.php">Firm Panel</a></li>
                    <li><a href="/auth/logout.php" class="logout">Logout</a></li>
                <?php elseif (user_role() === 'admin'): ?>
                    <li><a href="/routes/list.php">Search Routes</a></li>
                    <li><a href="/admin/index.php">Admin Panel</a></li>
                    <li><a href="/auth/logout.php" class="logout">Logout</a></li>
                <?php endif; ?>
            </ul>
        </ul>
    </div>
</body>

</html>
<?php require_once 'includes/footer.php'; ?>