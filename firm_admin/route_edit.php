<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

require_role('company');
?>

<?php
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

        echo "<p style='color:green'>Trip updated successfully!</p>";
    } else {
        try {
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

            echo "<p style='color:green'>Trip added successfully!</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red'>An error occurred.</p>";
        }
    }

    header('Location: routes.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trip</title>
</head>

<body>
    <h1><?= $edit_mode ? 'Trip Edit' : 'Add Trip' ?></h1>

    <form method="POST" enctype="multipart/form-data">
        <label>Destination City:</label><br>
        <input type="text" name="destination_city" required value="<?= htmlspecialchars($trip['destination_city'] ?? '') ?>"><br><br>

        <label>Arrivel Time:</label><br>
        <input type="datetime-local" name="arrival_time" required value="<?= htmlspecialchars($trip['arrival_time'] ?? '') ?>"><br><br>

        <label>Departure City:</label><br>
        <input type="text" name="departure_city" required value="<?= htmlspecialchars($trip['departure_city'] ?? '') ?>"><br><br>

        <label>Departure Time:</label><br>
        <input type="datetime-local" name="departure_time" required value="<?= htmlspecialchars($trip['departure_time'] ?? '') ?>"><br><br>

        <label>Price:</label><br>
        <input type="number" name="price" required value="<?= htmlspecialchars($trip['price'] ?? '') ?>"><br><br>

        <label>Capacity:</label><br>
        <input type="number" name="capacity" required value="<?= htmlspecialchars($trip['capacity'] ?? '') ?>"><br><br>

        <button type="submit"><?= $edit_mode ? 'Save' : 'Add' ?></button>
        <a href="routes.php">Cancel</a>
    </form>
</body>

</html>