<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
require_once '../includes/header.php';

$results = [];

//dynamic city list
$fromCities = $db->query("SELECT DISTINCT departure_city FROM Trips ORDER BY departure_city")->fetchAll(PDO::FETCH_COLUMN);
$toCities   = $db->query("SELECT DISTINCT destination_city FROM Trips ORDER BY destination_city")->fetchAll(PDO::FETCH_COLUMN);

//search request, tiny hack (WHERE 1=1)
$query = "
    SELECT Trips.*, Bus_Company.name AS company_name, Bus_Company.logo_path
    FROM Trips
    JOIN Bus_Company ON Trips.company_id = Bus_Company.id
    WHERE 1=1
";

$from = $_GET['from'] ?? '';
$to   = $_GET['to']   ?? '';
$date = $_GET['date'] ?? '';

$params = [];

//from
if (!empty($_GET['from'])) {
    $query .= " AND departure_city = ?";
    $params[] = $_GET['from'];
}

//to
if (!empty($_GET['to'])) {
    $query .= " AND destination_city = ?";
    $params[] = $_GET['to'];
}

//date
if (!empty($_GET['date'])) {
    $query .= " AND DATE(departure_time) = DATE(?)";
    $params[] = $_GET['date'];
}

$query .= " ORDER BY departure_time ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Search Routes</title>
</head>

<body>

    <h2>Search Routes</h2>

    <form method="GET">
        <select name="from">
            <option value="">From</option>
            <?php foreach ($fromCities as $c): ?>
                <option value="<?= $c ?>" <?= (isset($from) && $from == $c) ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
        </select>

        <select name="to">
            <option value="">To</option>
            <?php foreach ($toCities as $c): ?>
                <option value="<?= $c ?>" <?= (isset($to) && $to == $c) ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="date" value="<?= $date ?? '' ?>">
        <button type="submit">Search</button>
        <a href="list.php">Reset</a>

    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && $results): ?>
        <h3>Routes</h3>
        <table cellpadding="15">
            <tr>
                <th>Company</th>
                <th>From</th>
                <th>To</th>
                <th>Dep. Time</th>
                <th>Price</th>
                <th></th>
            </tr>
            <?php foreach ($results as $trip): ?>
                <tr>
                    <td>
                        <?php if ($trip['logo_path']): ?>
                            <img src="/uploads/<?= $trip['logo_path'] ?>" width="20">
                        <?php endif; ?>
                        <?= $trip['company_name'] ?>
                    </td>
                    <td><?= $trip['departure_city'] ?></td>
                    <td><?= $trip['destination_city'] ?></td>
                    <td><?= date("H:i", strtotime($trip['departure_time'])) ?></td>
                    <td><?= $trip['price'] ?> TL</td>
                    <td><a href="detail.php?id=<?= $trip['id'] ?>">Details</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['from'])): ?>
        <p>No routes for this search.</p>
    <?php endif; ?>

</body>

</html>
<?php require_once '../includes/footer.php'; ?>