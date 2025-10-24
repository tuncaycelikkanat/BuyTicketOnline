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
        } catch (PDOException $e) {
            echo "<p style='color:red'>An error occurred.</p>";
        }
    }

    header('Location: coupons.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_mode ? 'Edit Coupon' : 'Add Coupon' ?></title>
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

        form {
            background: rgba(0, 0, 0, 0.45);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.4);
            display: inline-block;
            text-align: left;
        }

        label {
            display: block;
            margin-top: 12px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="datetime-local"] {
            width: 250px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #00eaff;
            background: #0d0d0d;
            color: #0ff;
            margin-bottom: 15px;
        }

        .form-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        button,
        a.btn {
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: none;
            margin-top: 20px;
            margin-right: 10px;
            background: #00ce00ff;
            color: white;
            box-shadow: 0 0 6px #0ff;
            transition: 0.3s;
        }

        button:hover,
        a.btn:hover {
            color: #ffffffff;
            background: #009100ff;
            box-shadow: 0 0 12px #009100ff, 0 0 25px #009100ff;
            transform: scale(1.05);
        }

        a.btn-cancel {
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: none;
            margin-top: 20px;
            margin-right: 10px;
            background: #be1818ff;
            color: #ffffffff;
            transition: 0.3s;
        }

        a.btn-cancel:hover {
            color: #ffffffff;
            background-color: #831111ff;
            box-shadow: 0 0 12px #be1818ff, 0 0 25px #be1818ff;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <main>
        <h1><?= $edit_mode ? 'Edit Coupon' : 'Add Coupon' ?></h1>

        <form method="POST">
            <label>Code:</label>
            <input type="text" name="code" required value="<?= htmlspecialchars($coupon['code'] ?? '') ?>">

            <label>Discount:</label>
            <input type="number" name="discount" required value="<?= htmlspecialchars($coupon['discount'] ?? '') ?>">

            <label>Usage Limit:</label>
            <input type="number" name="usage_limit" required value="<?= htmlspecialchars($coupon['usage_limit'] ?? '') ?>">

            <label>Expire Date:</label>
            <input type="datetime-local" name="expire_date" required value="<?= htmlspecialchars($coupon['expire_date'] ?? '') ?>">

            <div class="form-buttons">
                <button type="submit"><?= $edit_mode ? 'Save' : 'Add' ?></button>
                <a href="coupons.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </main>

    <?php require_once '../includes/footer.php'; ?>
</body>

</html>