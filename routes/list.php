<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';

if (!is_login()) {
    header('Location: /auth/login.php');
    exit;
}

$results = [];

//dynamic city list
$fromCities = $db->query("SELECT DISTINCT departure_city FROM Trips ORDER BY departure_city")->fetchAll(PDO::FETCH_COLUMN);
$toCities   = $db->query("SELECT DISTINCT destination_city FROM Trips ORDER BY destination_city")->fetchAll(PDO::FETCH_COLUMN);

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

if (!empty($from)) {
    $query .= " AND departure_city = ?";
    $params[] = $from;
}

if (!empty($to)) {
    $query .= " AND destination_city = ?";
    $params[] = $to;
}

if (!empty($date)) {
    $query .= " AND DATE(departure_time) = DATE(?)";
    $params[] = $date;
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

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0b0f19;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
            text-align: center;
            padding-top: 60px;
        }

        h2 {
            color: #00eaff;
            text-shadow: 0 0 10px #00eaff;
            margin-bottom: 25px;
        }

        form {
            background: rgba(0, 0, 0, 0.55);
            padding: 25px;
            display: inline-block;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.4);
        }

        select,
        input[type="date"] {
            padding: 10px;
            margin: 5px;
            border-radius: 6px;
            border: 1px solid #00eaff;
            background: #111624;
            color: #00eaff;
        }

        button,
        a.btn {
            padding: 10px 18px;
            margin: 6px;
            border: none;
            border-radius: 6px;
            background: #00eaff;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        button:hover,
        a.btn:hover {
            color: #ffffffff;
            background: #00b6cc;
        }

        table {
            margin: 30px auto;
            border-collapse: collapse;
            width: 80%;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #00eaff;
        }

        tr:hover {
            background: rgba(0, 255, 255, 0.07);
        }

        .details-btn {
            padding: 6px 12px;
            border-radius: 6px;
            background: #00bddfff;
            color: black;
            text-decoration: none;
        }

        .details-btn:hover {
            background: #007e94ff;
        }

        .reset-btn {
            padding: 10px 18px;
            margin: 6px;
            border: none;
            border-radius: 6px;
            background: #be1818ff;
            color: #ffffffff;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .reset-btn:hover {
            background-color: #831111ff;
        }
    </style>
</head>

<body>

    <main>
        <h2>Search Routes</h2>

        <form method="GET">
            <select name="from">
                <option value="">From</option>
                <?php foreach ($fromCities as $c): ?>
                    <option value="<?= $c ?>" <?= ($from == $c) ? 'selected' : '' ?>><?= $c ?></option>
                <?php endforeach; ?>
            </select>

            <select name="to">
                <option value="">To</option>
                <?php foreach ($toCities as $c): ?>
                    <option value="<?= $c ?>" <?= ($to == $c) ? 'selected' : '' ?>><?= $c ?></option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="date" value="<?= $date ?>">

            <button type="submit">Search</button>
            <a class="reset-btn" href="list.php">Reset</a>
        </form>

        <?php if ($results): ?>
            <table>
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
                        <td><a class="details-btn" href="detail.php?id=<?= $trip['id'] ?>">Details</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php else: ?>
            <p>No routes found.</p>
        <?php endif; ?>
    </main>

    <?php require_once '../includes/footer.php'; ?>

</body>

</html>