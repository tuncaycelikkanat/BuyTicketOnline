<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';
require_role('company');
?>

<?php
if (!is_login())
    header('Location: /auth/login.php');

$edit_mode = isset($_GET['id']);
$coupon = null;

if ($edit_mode) {
    $stmt = $db->prepare("SELECT * FROM Coupons WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$coupon) {
        die("Coupon does not exist.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['user']['company_id'];
    $code = trim($_POST['code']);
    $discount = $_POST['discount'];
    $usage_limit = $_POST['usage_limit'];
    $expire_date = $_POST['expire_date'];

    if ($edit_mode) {
        $sql = "UPDATE Coupons 
                SET company_id = :company_id,
                    code = :code,
                    discount = :discount,
                    usage_limit = :usage_limit,
                    expire_date = :expire_date
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->bindValue(':company_id', $company_id);
        $stmt->bindValue(':code', $code);
        $stmt->bindValue(':discount', $discount);
        $stmt->bindValue(':usage_limit', $usage_limit);
        $stmt->bindValue(':expire_date', $expire_date);
        $stmt->execute();

        echo "<p style='color:green'>Coupon updated successfully!</p>";
    } else {
        try {
            $sql = "INSERT INTO Coupons (id, code, discount, company_id, usage_limit, expire_date)
                    VALUES (:id, :code, :discount, :company_id, :usage_limit, :expire_date)";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', uuid());
            $stmt->bindValue(':company_id', $company_id);
            $stmt->bindValue(':code', $code);
            $stmt->bindValue(':discount', $discount);
            $stmt->bindValue(':usage_limit', $usage_limit);
            $stmt->bindValue(':expire_date', $expire_date);
            $stmt->execute();

            echo "<p style='color:green'>Coupon added successfully!</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red'>An error occurred.</p>";
        }
    }

    header('Location: coupons.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coupon</title>
</head>

<body>
    <h1><?= $edit_mode ? 'Coupon Edit' : 'Add Coupon' ?></h1>

    <form method="POST" enctype="multipart/form-data">
        <label>Code:</label><br>
        <input type="text" name="code" required value="<?= htmlspecialchars($coupon['code'] ?? '') ?>"><br><br>

        <label>Discount:</label><br>
        <input type="number" name="discount" required value="<?= htmlspecialchars($coupon['discount'] ?? '') ?>"><br><br>

        <label>Usage Limit:</label><br>
        <input type="number" name="usage_limit" required value="<?= htmlspecialchars($coupon['usage_limit'] ?? '') ?>"><br><br>

        <label>Expire Date:</label><br>
        <input type="datetime-local" name="expire_date" required value="<?= htmlspecialchars($coupon['expire_date'] ?? '') ?>"><br><br>

        <button type="submit"><?= $edit_mode ? 'Save' : 'Add' ?></button>
        <a href="coupons.php">Cancel</a>
    </form>
</body>

</html>

<?php require_once '../includes/footer.php'; ?>