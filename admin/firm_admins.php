<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

require_role('admin');

if (!is_login())
    header('Location: /auth/login.php');

require_once '../includes/header.php';


// Fetch company
$stmt = $db->prepare("SELECT * FROM Bus_Company WHERE id = ?");
$stmt->execute([$_GET['id']]);
$firm = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$firm) die("Company does not exist.");

// Remove admin
if (isset($_GET['remove'])) {
    $removed_id = $_GET['remove'];
    $stmt = $db->prepare("UPDATE Users SET role = 'user', company_id = NULL WHERE id = ?");
    $stmt->execute([$removed_id]);
    header("Location: firm_admins.php?id=" . $firm['id']);
    exit;
}

// Add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['company_id'])) {
    $stmt = $db->prepare("UPDATE Users SET role = 'company', company_id = ? WHERE id = ?");
    $stmt->execute([$_POST['company_id'], $_POST['user_id']]);
    header("Location: firm_admins.php?id=" . $firm['id']);
    exit;
}

// Pull admins and user list
$stmt = $db->prepare("SELECT * FROM Users WHERE company_id = ? AND role = 'company'");
$stmt->execute([$firm['id']]);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

$available_users = $db->query("SELECT * FROM Users WHERE role = 'user' ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Company Admin Management</title>

    <!-- SELECT2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0d0d0d;
            color: #0ff;
            text-align: center;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1,
        h2 {
            text-shadow: 0 0 10px #00eaff;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 0 10px #0ff;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #0ff;
        }

        tr:hover {
            background: rgba(0, 255, 255, 0.07);
        }

        .btn,
        button {
            padding: 10px 22px;
            border-radius: 22px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 8px;
            transition: 0.3s;
        }

        .btn-primary {
            background-color: #0ff;
            color: #000;
            box-shadow: 0 0 6px #0ff;
        }

        .btn-primary:hover {
            background-color: #00b6cc;
            box-shadow: 0 0 14px #0ff;
        }

        .btn-danger {
            background-color: rgb(161, 0, 0);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgb(105, 0, 0);
            box-shadow: 0 0 10px red;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            background: #111;
            color: #0ff;
            border: 1px solid #0ff;
        }

        form {
            margin-top: 15px;
        }

        footer {
            margin-top: auto;
        }

        a.back-btn {
            display: inline-flex !important;
            width: max-content !important;
            max-width: max-content !important;
            padding: 10px 22px;
            border-radius: 22px;
            background-color: #272727;
            color: #0ff;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 0 6px #0ff;
            transition: .15s;
        }

        a.back-btn:hover {
            background-color: #0ff;
            color: #000;
            box-shadow: 0 0 14px #0ff;
            transform: scale(1.03);
        }

        .back-btn-container {
            text-align: center;
            margin-top: 25px;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            background-color: #1f2937 !important;
            border: 1px solid #4b5563 !important;
            color: white !important;
        }

        .select2-container--default .select2-results__option {
            color: black !important;
        }

        .select2-container--default .select2-selection__rendered {
            color: white !important;
            line-height: 38px !important;
        }

        .select2-dropdown {
            background-color: white !important;
        }
    </style>
</head>

<body>

    <h1>Company Admin Management</h1>

    <h2><?= htmlspecialchars($firm['name']) ?>
        <?php if ($firm['logo_path']): ?>
            <img src="../uploads/<?= htmlspecialchars($firm['logo_path']) ?>" width="35">
        <?php endif; ?>
    </h2>

    <table>
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
                        <a class="btn btn-danger" href="?id=<?= $firm['id'] ?>&remove=<?= $admin['id'] ?>"
                            onclick="return confirm('Remove this admin?')">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3"><em>No admin</em></td>
            </tr>
        <?php endif; ?>
    </table>

    <?php if (count($available_users) > 0): ?>
        <form method="POST">
            <input type="hidden" name="company_id" value="<?= $firm['id'] ?>">
            <select name="user_id" class="user-select" required>
                <option value="">Select User</option>
                <?php foreach ($available_users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Add Admin</button>
        </form>
    <?php endif; ?>

    <br>
    <div class="back-btn-container">
        <a href="firms.php" class="back-btn">← Back to Companies</a>
    </div>

    <?php require_once '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('.user-select').select2({
                width: '300px'
            });
        });
    </script>
</body>

</html>