<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

require_role('admin');

if (!is_login())
    header('Location: /auth/login.php');

require_once '../includes/header.php';


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM Bus_Company WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: firms.php');
    exit;
}

$stmt = $db->query("SELECT * FROM Bus_Company ORDER BY created_at DESC");
$firms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies</title>

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
            margin: 0 auto 25px auto;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            border: none;
            background-color: #272727;
            color: #0ff;
            box-shadow: 0 0 6px #0ff;
            transition: 0.3s;
        }

        .btn:hover {
            color: #000;
            background-color: #0ff;
            box-shadow: 0 0 14px #0ff, 0 0 28px #0ff;
            transform: scale(1.05);
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background: rgba(0, 255, 255, 0.03);
            box-shadow: 0 0 8px #0ff;
            border-radius: 10px;
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
            background-color: #009bb3;
            color: #fff;
        }

        a.delete-btn {
            background-color: #b30000;
            color: #fff;
        }

        a.delete-btn:hover {
            background-color: #700000;
        }

        footer {
            margin-top: auto;
        }
    </style>
</head>

<body>
    <h1>Bus Companies</h1>
    <a href="firm_edit.php" class="btn">Add New Company</a>

    <table cellpadding="10">
        <thead>
            <tr>
                <th>Name</th>
                <th>Logo</th>
                <th>Created</th>
                <th>Ops</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($firms as $firm): ?>
                <tr>
                    <td><?= htmlspecialchars($firm['name']) ?></td>
                    <td>
                        <?php if ($firm['logo_path']): ?>
                            <img src="../uploads/<?= htmlspecialchars($firm['logo_path']) ?>" width="80">
                        <?php else: ?>
                            <em>No Logo</em>
                        <?php endif; ?>
                    </td>
                    <td><?= $firm['created_at'] ?></td>
                    <td>
                        <a class="action-btn" href="firm_edit.php?id=<?= $firm['id'] ?>">Edit</a>
                        <a class="action-btn delete-btn" href="firms.php?delete=<?= $firm['id'] ?>" onclick="return confirm('You are deleting this company...')">Remove</a>
                        <a class="action-btn" href="firm_admins.php?id=<?= $firm['id'] ?>">Admins</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php require_once '../includes/footer.php'; ?>
</body>

</html>