<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

require_role('admin');
?>

<?php
if (!is_login())
    header('Location: /auth/login.php');

require_once '../includes/header.php';


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM Coupons WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: coupons.php');
    exit;
}

$stmt = $db->query("SELECT * FROM Coupons ORDER BY created_at DESC");
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0d0d0d;
            color: #0ff;
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

        .btn {
            padding: 10px 25px;
            width: auto;
            display: inline-block;
            margin: 0 auto 20px auto;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            border: none;
            background-color: #272727ff;
            color: #0ff;
            box-shadow: 0 0 6px #0ff;
            transition: 0.3s;
        }

        /* 🔹 Footer her zaman en altta */
        footer {
            margin-top: auto;
        }

        .btn:hover {
            color: #000;
            background-color: #0ff;
            box-shadow: 0 0 12px #0ff, 0 0 25px #0ff;
            transform: scale(1.05);
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #0ff;
        }

        th {
            color: #00eaff;
            text-align: left;
        }

        tr:hover {
            background: rgba(0, 255, 255, 0.07);
        }

        a.action-btn {
            padding: 6px 12px;
            border-radius: 15px;
            text-decoration: none;
            background-color: #00eaff;
            color: #000;
            font-weight: bold;
            transition: 0.3s;
            margin: 0 3px;
        }

        a.action-btn:hover {
            background-color: #00b6cc;
            color: #fff;
        }

        a.delete-btn {
            background-color: rgba(179, 0, 0, 1);
            color: #fff;
        }

        a.delete-btn:hover {
            background-color: #831111;
        }
    </style>
</head>

<body>
    <h1>Coupons</h1>
    <a href="coupon_edit.php" class="btn">Add New Coupon</a>

    <table cellpadding="10">
        <table cellpadding="10">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Usage Limit</th>
                    <th>Expire Date</th>
                    <th>Ops</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>

                    <?php

                    $firm_id = $coupon['company_id'];
                    $firm = null;

                    if ($firm_id != null) {
                        $stmt = $db->prepare("SELECT * FROM Bus_Company WHERE id = ?");
                        $stmt->execute([$firm_id]);
                        $firm = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (!$firm) {
                            die("Company does not exist.");
                        }
                    } ?>

                    <tr>
                        <td><?= htmlspecialchars($firm['name']) ?></td>
                        <td><?= htmlspecialchars($coupon['code']) ?></td>
                        <td><?= htmlspecialchars($coupon['discount']) ?></td>
                        <td><?= htmlspecialchars($coupon['usage_limit']) ?></td>
                        <td><?= htmlspecialchars($coupon['expire_date']) ?></td>

                        <td>
                            <a class="action-btn" href="coupon_edit.php?id=<?= $coupon['id'] ?>">Edit</a>
                            <a class="action-btn delete-btn" href="coupons.php?delete=<?= $coupon['id'] ?>" onclick="return confirm('You are deleting this coupon...')">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </table>

    <?php require_once '../includes/footer.php'; ?>
</body>

</html>