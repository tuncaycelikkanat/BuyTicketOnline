<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
require_once '../includes/header.php';
include '../includes/functions.php';

if (!is_login()) {
    header('Location: /auth/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("Trip ID not provided.");
}

$trip_id = $_GET['id'];

$stmt = $db->prepare("
    SELECT Trips.*, Bus_Company.name AS company_name, Bus_Company.logo_path
    FROM Trips
    JOIN Bus_Company ON Trips.company_id = Bus_Company.id
    WHERE Trips.id = ?
");
$stmt->execute([$trip_id]);
$trip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    die("Trip not found.");
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Route Details</title>

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
            padding-top: 60px;
        }

        h2 {
            color: #00eaff;
            text-shadow: 0 0 10px #00eaff;
            margin-bottom: 25px;
        }

        table {
            margin: 0 auto;
            width: 70%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.4);
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 15px;
            border-bottom: 1px solid #00eaff;
        }

        ul {
            list-style: none;
            margin-top: 30px;
            padding: 0;
        }

        ul li {
            margin: 10px 0;
        }

        .btn-blue {
            padding: 10px 20px;
            background: #00eaff;
            color: #000;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            width: 180px;
        }

        .btn-blue:hover {
            background: #00b6cc;
        }

        .btn-green {
            padding: 10px 20px;
            background: #00ce00ff;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            width: 180px;
        }

        .btn-green:hover {
            background: #009100ff;
        }
    </style>
</head>

<body>

    <main>
        <h2>Route Details</h2>

        <table>
            <tr>
                <th>Departure City</th>
                <th>Destination City</th>
                <th>Dep. Time</th>
                <th>Arrival Time</th>
                <th>Capacity</th>
                <th>Price</th>
                <th>Company</th>
            </tr>

            <tr>
                <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                <td><?= date("d.m.Y H:i", strtotime($trip['departure_time'])) ?></td>
                <td><?= date("d.m.Y H:i", strtotime($trip['arrival_time'])) ?></td>
                <td><?= htmlspecialchars($trip['capacity']) ?></td>
                <td><strong><?= $trip['price'] ?> TL</strong></td>
                <td>
                    <?php if ($trip['logo_path']): ?>
                        <img src="/uploads/<?= $trip['logo_path'] ?>" width="20">
                    <?php endif; ?>
                    <?= $trip['company_name'] ?>
                </td>
            </tr>
        </table>

        <ul>
            <li><a class="btn-green" href="buy.php?id=<?= $trip['id'] ?>">Buy</a></li>
            <li><a class="btn-blue" href="list.php">Back to Search</a></li>
        </ul>
    </main>

    <?php require_once '../includes/footer.php'; ?>

</body>

</html>