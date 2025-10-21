<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

require_role('admin');

// remove admin
if (isset($_GET['remove'])) {
    $removed_id = $_GET['remove'];
    $stmt = $db->prepare("UPDATE Users SET role = 'user', company_id = NULL WHERE id = ?");
    $stmt->execute([$removed_id]);
    header("Location: firm_admins.php");
    exit;
}

// add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['company_id'])) {
    $user_id = $_POST['user_id'];
    $company_id = $_POST['company_id'];

    $stmt = $db->prepare("UPDATE Users SET role = 'company', company_id = ? WHERE id = ?");
    $stmt->execute([$company_id, $user_id]);

    header("Location: firm_admins.php");
    exit;
}

// get firms
$firms = $db->query("SELECT * FROM Bus_Company ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Company Admin Management</title>
    <!-- SELECT2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
</head>

<body>

    <h1>Company Admin Management</h1>
    <hr>

    <?php foreach ($firms as $firm): ?>

        <h2><?= htmlspecialchars($firm['name']) ?>
            <?php if ($firm['logo_path']): ?>
                <img src="../uploads/<?= htmlspecialchars($firm['logo_path']) ?>" width="35">
            <?php endif; ?>
        </h2>

        <?php
        // get admin for the current firm
        $stmt = $db->prepare("SELECT * FROM Users WHERE company_id = ? AND role = 'company'");
        $stmt->execute([$firm['id']]);
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // get users who role=user
        $available_users = $db->query("SELECT * FROM Users WHERE role = 'user' ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <table border="1" cellpadding="5">
            <tr>
                <th>Admin Name</th>
                <th>Email</th>
                <th>Operation</th>
            </tr>

            <?php if (count($admins) > 0): ?>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['full_name']) ?></td>
                        <td><?= htmlspecialchars($admin['email']) ?></td>
                        <td>
                            <a href="?remove=<?= $admin['id'] ?>" onclick="return confirm('Remove this admin?')">
                                Remove Admin
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No admin</td>
                </tr>
            <?php endif; ?>
        </table>

        <?php if (count($available_users) > 0): ?>
            <form method="POST" style="margin-top: 10px;">
                <input type="hidden" name="company_id" value="<?= $firm['id'] ?>">
                <label>Add Admin:</label>
                <select name="user_id" class="user-select" required>
                    <option value="">Select User</option>
                    <?php foreach ($available_users as $u): ?>
                        <option value="<?= $u['id'] ?>">
                            <?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Add</button>
            </form>
        <?php else: ?>
            <p><em>No available users to assign.</em></p>
        <?php endif; ?>

        <br>

    <?php endforeach; ?>

    <a href="firms.php">Companies -></a>


    <script>
        $(document).ready(function() {
            $('.user-select').select2({
                placeholder: "Search user by name or email...",
                width: '300px'
            });
        });
    </script>

</body>

</html>