<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

require_role('admin');
?>

<?php
if (!is_login())
    header('Location: /auth/login.php');

$edit_mode = isset($_GET['id']);
$firm = null;

if ($edit_mode) {
    $stmt = $db->prepare("SELECT * FROM Bus_Company WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $firm = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$firm) {
        die("Company does not exist.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $logo = null;

    if (!empty($_FILES['logo']['name'])) {
        $targetDir = "../uploads/";
        $filename = uniqid() . "_" . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $targetDir . $filename);
        $logo = $filename;
    }

    if ($edit_mode) {
        $sql = "UPDATE Bus_Company SET name = :name";
        if ($logo) $sql .= ", logo_path = :logo";
        $sql .= " WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $name);
        if ($logo) $stmt->bindValue(':logo', $logo);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->execute();
    } else {

        try {
            $stmt = $db->prepare("INSERT INTO Bus_Company (id, name, logo_path) VALUES (?, ?, ?)");
            $uuid = uuid();
            $stmt->execute([$uuid, $name, $logo]);
            echo "<p style='color:green'>Added Successfully!</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red'>An error has occured.</p>";
        }
    }

    header('Location: firms.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Companies</title>
</head>

<body>
    <h1><?= $edit_mode ? 'Company Edit' : 'Add Company' ?></h1>

    <form method="POST" enctype="multipart/form-data">
        <label>Company Name:</label><br>
        <input type="text" name="name" required value="<?= htmlspecialchars($firm['name'] ?? '') ?>"><br><br>

        <label>Logo (optional):</label><br>
        <input type="file" name="logo" accept="image/*"><br><br>

        <?php if ($edit_mode && $firm['logo_path']): ?>
            <p>Current logo:</p>
            <img src="../uploads/<?= htmlspecialchars($firm['logo_path']) ?>" width="120"><br><br>
        <?php endif; ?>

        <button type="submit"><?= $edit_mode ? 'Save' : 'Add' ?></button>
        <a href="firms.php">Cancel</a>
    </form>
</body>

</html>