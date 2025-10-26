<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

require_role('admin');
if (!is_login())
    header('Location: /auth/login.php');

require_once '../includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Main Page</title>
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

        h1 {
            color: #00eaff;
            text-shadow: 0 0 10px #00eaff;
            margin-bottom: 40px;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
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
        <h1>Admin Panel</h1>
        <ul>
            <li><a href="/admin/firms.php">Firms</a></li>
            <li><a href="/admin/coupons.php">Coupons</a></li>
            <li><a class="logout" href="/auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <?php require_once '../includes/footer.php'; ?>

</body>

</html>