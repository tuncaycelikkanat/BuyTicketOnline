<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';
require_role('company');
?>

<?php
if (!is_login())
    header('Location: /auth/login.php');

require_once '../includes/header.php';


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
    $stmt = $db->prepare("DELETE FROM Trips WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: routes.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM Trips WHERE company_id = ? ORDER BY created_date DESC");
$stmt->execute([$firm['id']]);
$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($firm['name']) ?> - Trips</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0b0f19;
            color: #e0e0e0;
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
            display: inline-block;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            background-color: #272727ff;
            color: #0ff;
            font-weight: bold;
            box-shadow: 0 0 6px #0ff;
            transition: 0.3s;
            margin-bottom: 20px;
        }

        .btn:hover {
            color: #000;
            background-color: #0ff;
            box-shadow: 0 0 12px #0ff, 0 0 25px #0ff;
            transform: scale(1.05);
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 90%;
            max-width: 1000px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #00eaff;
            text-align: center;
        }

        th {
            color: #00eaff;
            text-shadow: 0 0 5px #00eaff;
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
    <main>
        <h1>Trips for <?= htmlspecialchars($firm['name']) ?></h1>
        <a href="route_edit.php" class="btn">Add New Trip</a>

        <?php if (count($trips) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Destination City</th>
                        <th>Arrival Time</th>
                        <th>Departure City</th>
                        <th>Departure Time</th>
                        <th>Price</th>
                        <th>Capacity</th>
                        <th>Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trips as $trip): ?>
                        <tr>
                            <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                            <td><?= htmlspecialchars($trip['arrival_time']) ?></td>
                            <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                            <td><?= htmlspecialchars($trip['departure_time']) ?></td>
                            <td><?= htmlspecialchars($trip['price']) ?></td>
                            <td><?= htmlspecialchars($trip['capacity']) ?></td>
                            <td>
                                <a href="route_edit.php?id=<?= $trip['id'] ?>" class="action-btn">Edit</a>
                                <a href="routes.php?delete=<?= $trip['id'] ?>" onclick="return confirm('You are deleting this trip!')" class="action-btn delete-btn">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No trips found.</p>
        <?php endif; ?>
    </main>

    <?php require_once '../includes/footer.php'; ?>
</body>

</html>