<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

require_role('admin');
?>

<?php
if (!is_login())
    header('Location: /auth/login.php');

if (isset($_GET['delete'])) { //delete uploaded photo also.
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
    <title>Company</title>
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
                        <a href="firm_edit.php?id=<?= $firm['id'] ?>">Edit</a> |
                        <a href="firms.php?delete=<?= $firm['id'] ?>" onclick="return confirm('You are deleting this company...')">Remove</a> |
                        <a href="firm_admins.php?id=<?= $firm['id'] ?>">Admins</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>