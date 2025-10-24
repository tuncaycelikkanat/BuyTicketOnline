<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';
require_role('company');
?>

<?php

$firm_id = $_SESSION['user']['company_id'];
$firm = null;

if ($firm_id != null) {
    $stmt = $db->prepare("SELECT * FROM Bus_Company WHERE id = ?");
    $stmt->execute([$firm_id]);
    $firm = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$firm) {
        die("Company does not exist.");
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM Coupons WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: coupons.php');
    exit;
}

$stmt = $db->query("SELECT * FROM Coupons WHERE company_id = ? ORDER BY created_at DESC");
$stmt->execute([$firm['id']]);
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($firm['name']) ?></title>
</head>

<body>
    <h1>Coupons for <?= htmlspecialchars($firm['name']) ?></h1>
    <a href="coupon_edit.php" class="btn">Add New Coupon</a>

    <table cellpadding="10">
        <thead>
            <tr>
                <th>Code</th>
                <th>Discount</th>
                <th>Usage Limit</th>
                <th>Expire Date</th>
                <th>Ops</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coupons as $coupon): ?>
                <tr>
                    <td><?= htmlspecialchars($coupon['code']) ?></td>
                    <td><?= htmlspecialchars($coupon['discount']) ?></td>
                    <td><?= htmlspecialchars($coupon['usage_limit']) ?></td>
                    <td><?= htmlspecialchars($coupon['expire_date']) ?></td>

                    <td>
                        <a href="coupon_edit.php?id=<?= $coupon['id'] ?>">Edit</a> |
                        <a href="coupons.php?delete=<?= $coupon['id'] ?>" onclick="return confirm('You are deleting this coupon...')">Remove</a> |
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
<?php require_once '../includes/footer.php'; ?>