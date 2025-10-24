<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php'; //don't forget to import config.php
include '../includes/functions.php';
require_role('company'); //I just spent 1 hour to find it !!! require_role('adimn');
if (!is_login())
    header('Location: /auth/login.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Admin Main Page</title>
</head>

<body>
    <ul>
        <li><a href="/firm_admin/routes.php">Routes</a></li>
        <li><a href="/firm_admin/coupons.php">Coupons</a></li>
    </ul>

</body>

</html>