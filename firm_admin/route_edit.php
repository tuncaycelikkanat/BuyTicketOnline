<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';
require_role('company');
?>

<?php
if (!is_login())
    header('Location: /auth/login.php');

$edit_mode = isset($_GET['id']);
$trip = null;

if ($edit_mode) {
    $stmt = $db->prepare("SELECT * FROM Trips WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trip) {
        die("Trip does not exist.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['user']['company_id'];
    $destination_city = trim($_POST['destination_city']);
    $arrival_time = $_POST['arrival_time'];
    $departure_time = $_POST['departure_time'];
    $departure_city = trim($_POST['departure_city']);
    $price = (int)$_POST['price'];
    $capacity = (int)$_POST['capacity'];

    if ($edit_mode) {
        $sql = "UPDATE Trips 
                SET company_id = :company_id,
                    destination_city = :destination_city,
                    arrival_time = :arrival_time,
                    departure_time = :departure_time,
                    departure_city = :departure_city,
                    price = :price,
                    capacity = :capacity
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->bindValue(':company_id', $company_id);
        $stmt->bindValue(':destination_city', $destination_city);
        $stmt->bindValue(':arrival_time', $arrival_time);
        $stmt->bindValue(':departure_time', $departure_time);
        $stmt->bindValue(':departure_city', $departure_city);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':capacity', $capacity);
        $stmt->execute();
    } else {
        $sql = "INSERT INTO Trips (id, company_id, destination_city, arrival_time, departure_time, departure_city, price, capacity)
                VALUES (:id, :company_id, :destination_city, :arrival_time, :departure_time, :departure_city, :price, :capacity)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', uuid());
        $stmt->bindValue(':company_id', $company_id);
        $stmt->bindValue(':destination_city', $destination_city);
        $stmt->bindValue(':arrival_time', $arrival_time);
        $stmt->bindValue(':departure_time', $departure_time);
        $stmt->bindValue(':departure_city', $departure_city);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':capacity', $capacity);
        $stmt->execute();
    }

    header('Location: routes.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_mode ? 'Edit Trip' : 'Add Trip' ?></title>
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

        form {
            background: rgba(0, 0, 0, 0.45);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.4);
            display: inline-block;
            text-align: left;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        input[type="datetime-local"] {
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
            border-radius: 6px;
            border: 1px solid #00eaff;
            background: #111624;
            color: #00eaff;
        }

        button,
        a.btn {
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: none;
            margin-top: 20px;
            margin-right: 10px;
            background: #00ce00ff;
            color: white;
            box-shadow: 0 0 6px #0ff;
            transition: 0.3s;
        }

        button:hover,
        a.btn:hover {
            color: #ffffffff;
            background: #009100ff;
            box-shadow: 0 0 12px #009100ff, 0 0 25px #009100ff;
            transform: scale(1.05);
        }

        a.btn-cancel {
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: none;
            margin-top: 20px;
            margin-right: 10px;
            background: #be1818ff;
            color: #ffffffff;
            transition: 0.3s;
        }

        a.btn-cancel:hover {
            color: #ffffffff;
            background-color: #831111ff;
            box-shadow: 0 0 12px #be1818ff, 0 0 25px #be1818ff;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <main>
        <h1><?= $edit_mode ? 'Edit Trip' : 'Add Trip' ?></h1>

        <form method="POST" enctype="multipart/form-data">
            <label>Destination City:</label>
            <input type="text" name="destination_city" required value="<?= htmlspecialchars($trip['destination_city'] ?? '') ?>">

            <label>Arrival Time:</label>
            <input type="datetime-local" name="arrival_time" required value="<?= htmlspecialchars($trip['arrival_time'] ?? '') ?>">

            <label>Departure City:</label>
            <input type="text" name="departure_city" required value="<?= htmlspecialchars($trip['departure_city'] ?? '') ?>">

            <label>Departure Time:</label>
            <input type="datetime-local" name="departure_time" required value="<?= htmlspecialchars($trip['departure_time'] ?? '') ?>">

            <label>Price:</label>
            <input type="number" name="price" required value="<?= htmlspecialchars($trip['price'] ?? '') ?>">

            <label>Capacity:</label>
            <input type="number" name="capacity" required value="<?= htmlspecialchars($trip['capacity'] ?? '') ?>">

            <button type="submit"><?= $edit_mode ? 'Save' : 'Add' ?></button>
            <a href="routes.php" class="btn-cancel">Cancel</a>
        </form>
    </main>

    <?php require_once '../includes/footer.php'; ?>
</body>

</html>