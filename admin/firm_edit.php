<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
require_once '../includes/header.php';
include '../includes/functions.php';

require_role('admin');

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
        } catch (PDOException $e) {
            echo "<p style='color:red'>An error has occurred.</p>";
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
    <title><?= $edit_mode ? 'Edit Company' : 'Add Company' ?></title>

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
            width: 380px;
            margin: 0 auto;
            background: rgba(0, 255, 255, 0.08);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px #0ff;
            text-align: left;
        }

        label {
            font-weight: bold;
            color: #00eaff;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 18px 0;
            border-radius: 8px;
            border: 1px solid #0ff;
            background: #111;
            color: #0ff;
        }

        button,
        .btn-cancel {
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: bold;
            text-decoration: none;
            border: none;
            cursor: pointer;
            margin-right: 10px;
            box-shadow: 0 0 6px #0ff;
            transition: 0.3s;
        }

        button {
            background-color: #0ff;
            color: #000;
        }

        button:hover {
            background-color: #00b6cc;
            box-shadow: 0 0 14px #0ff, 0 0 28px #0ff;
        }

        .btn-cancel {
            background-color: rgb(161, 0, 0);
            color: #fff;
        }

        .btn-cancel:hover {
            background-color: rgb(102, 0, 0);
            box-shadow: 0 0 14px red;
        }

        footer {
            margin-top: auto;
        }
    </style>
</head>

<body>
    <h1><?= $edit_mode ? 'Edit Company' : 'Add Company' ?></h1>

    <form method="POST" enctype="multipart/form-data">
        <label>Company Name:</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($firm['name'] ?? '') ?>">

        <label>Logo (optional):</label>
        <input type="file" name="logo" accept="image/*">

        <?php if ($edit_mode && $firm['logo_path']): ?>
            <p>Current logo:</p>
            <img src="../uploads/<?= htmlspecialchars($firm['logo_path']) ?>" width="120"><br><br>
        <?php endif; ?>

        <button type="submit"><?= $edit_mode ? 'Save' : 'Add' ?></button>
        <a class="btn-cancel" href="firms.php">Cancel</a>
    </form>

    <?php require_once '../includes/footer.php'; ?>
</body>

</html>