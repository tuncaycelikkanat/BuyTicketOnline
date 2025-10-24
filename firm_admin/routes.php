<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';
require_role('company');
?>

<?php

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

$stmt = $db->query("SELECT * FROM Trips WHERE company_id = ? ORDER BY created_date DESC");
$stmt->execute([$firm['id']]);
$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($firm['name']) ?></title>
</head>

<body>
    <h1>Trips for <?= htmlspecialchars($firm['name']) ?></h1>
    <a href="route_edit.php" class="btn">Add New Trip</a>

    <table cellpadding="10">
        <thead>
            <tr>
                <th>Destinaiton City</th>
                <th>Arrivel Time</th>
                <th>Departure City</th>
                <th>Departure Time</th>
                <th>Price</th>
                <th>Capacity</th>
                <th>Ops</th>
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
                        <a href="route_edit.php?id=<?= $trip['id'] ?>">Edit</a> |
                        <a href="routes.php?delete=<?= $trip['id'] ?>" onclick="return confirm('You are deleting this company...')">Remove</a> |
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
<?php require_once '../includes/footer.php'; ?>