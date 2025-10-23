<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include ROOT_PATH . '/includes/functions.php';

is_login();

// Trip id cehck
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

// get booked seats
$stmt = $db->prepare("
    SELECT seat_number
    FROM Booked_Seats
    JOIN Tickets ON Booked_Seats.ticket_id = Tickets.id
    WHERE Tickets.trip_id = ?
");
$stmt->execute([$trip_id]);
$bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

$capacity = (int)$trip['capacity'];
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Buy</title>
</head>

<body>

    <h2>Buy The Ticket</h2>

    <p><strong>Company:</strong> <?= htmlspecialchars($trip['company_name']) ?></p>
    <p><strong>Departure:</strong> <?= htmlspecialchars($trip['departure_city']) ?> — <?= date("d.m.Y H:i", strtotime($trip['departure_time'])) ?></p>
    <p><strong>Destination:</strong> <?= htmlspecialchars($trip['destination_city']) ?></p>
    <p><strong>Price:</strong> <?= $trip['price'] ?> TL</p>

    <p><strong>BookedSeatCount:</strong> <?= count($bookedSeats) ?></p>
    <p><strong>TripID:</strong> <?= $trip_id ?></p>

    <?php foreach ($bookedSeats as $seat): ?>
        <p><strong>Price:</strong> <?= $seats ?></p>

    <?php endforeach; ?>


    <form action="/routes/purchase.php" method="POST">
        <input type="hidden" name="trip_id" value="<?= $trip_id ?>">

        <label><strong>Select Seat:</strong></label><br>
        <select name="seat_number" required>
            <option value="">Select Seat</option>
            <?php for ($i = 1; $i <= $capacity; $i++): ?>
                <?php if (!in_array($i, $bookedSeats)): ?>
                    <option value="<?= $i ?>">Seat <?= $i ?></option>
                <?php endif; ?>
            <?php endfor; ?>
        </select>
        <br><br>

        <label><strong>Coupen Code:</strong></label><br>
        <input type="text" name="coupon" placeholder="(optional)">
        <br><br>

        <button type="submit">Buy</button>
        <a href="detail.php?id=<?= $trip_id ?>">Return to Details</a>
    </form>


</body>

</html>