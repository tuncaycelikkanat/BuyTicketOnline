<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php'; //don't forget to import config.php
include '../includes/functions.php';

require_role('admin'); //I just spent 1 hour to find it !!! require_role('adimn');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Main Page</title>
</head>

<body>
    <h1>Only Admins Can See This Page.</h1>
    <ul>
        <li><a href="/admin/firms.php">Firms</a></li>
        <li><a href="/admin/coupons.php">Coupons</a></li>
    </ul>
</body>

</html>