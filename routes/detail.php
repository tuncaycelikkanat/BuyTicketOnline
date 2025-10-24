<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
require_once '../includes/header.php';

if (!is_login())
    header('Location: /auth/login.php');

if (!isset($_GET['id'])) {
    die("Trip ID not provided.");
}

$trip_id = $_GET['id'];

// get trip infos
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
    <title>Details</title>
</head>

<body>


    <h2>Route Details</h2>

    <table cellpadding="15">
        <tr>
            <th>Deparute City</th>
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

    <ul style="list-style-type: none;">
        <li><a href="buy.php?id=<?= $trip['id'] ?>">Buy</a></li><br />
        <li><a href="list.php">Go Route Search</a></li>
    </ul>
</body>

</html>
<?php require_once '../includes/footer.php'; ?>