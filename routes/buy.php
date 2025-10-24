<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include ROOT_PATH . '/includes/functions.php';
require_once '../includes/header.php';

if (!is_login()) {
    header('Location: /auth/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("Trip ID not provided.");
}

$stmt = $db->prepare("SELECT balance FROM Users WHERE id = ?");
$stmt->execute([$_SESSION['user']['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $user['balance'];

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
    <title>Buy Ticket</title>

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

        h2 {
            color: #00eaff;
            text-shadow: 0 0 10px #00eaff;
            margin-bottom: 25px;
        }

        .info-box {
            background: rgba(0, 0, 0, 0.45);
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.4);
            padding: 20px;
            width: 420px;
            margin: 0 auto 25px auto;
            border-radius: 10px;
            text-align: left;
        }

        .info-box p {
            margin: 8px 0;
        }

        form {
            background: rgba(0, 0, 0, 0.45);
            box-shadow: 0 0 12px rgba(255, 0, 170, 0.4);
            padding: 20px;
            width: 420px;
            margin: auto;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        select,
        input[type="text"] {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #00eaff;
            background: #111624;
            color: #00eaff;
            box-sizing: border-box;
            width: 100%;
        }

        .btn-green,
        .btn-blue {
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            display: block;
            width: 100%;
            text-align: center;
            cursor: pointer;
            border: none;
            box-sizing: border-box;
        }

        .btn-green {
            background: #00ce00ff;
            color: #fff;
        }

        .btn-green:hover {
            background: #009100ff;
        }

        .btn-blue {
            background: #00eaff;
            color: #000;
        }

        .btn-blue:hover {
            background: #00b6cc;
        }
    </style>
</head>

<body>

    <main>
        <h2>Buy The Ticket</h2>

        <div class="info-box">
            <p><strong>Company:</strong> <?= htmlspecialchars($trip['company_name']) ?></p>
            <p><strong>Departure:</strong> <?= htmlspecialchars($trip['departure_city']) ?> — <?= date("d.m.Y H:i", strtotime($trip['departure_time'])) ?></p>
            <p><strong>Destination:</strong> <?= htmlspecialchars($trip['destination_city']) ?></p>
            <p><strong>Price:</strong> <?= $trip['price'] ?> TL</p>
            <p><strong>Your Balance:</strong> <?= $balance ?> TL</p>
        </div>

        <form action="/routes/purchase.php" method="POST">
            <input type="hidden" name="trip_id" value="<?= $trip_id ?>">

            <label><strong>Select Seat:</strong></label>
            <select name="seat_number" required>
                <option value="">Select Seat</option>
                <?php for ($i = 1; $i <= $capacity; $i++): ?>
                    <?php if (!in_array($i, $bookedSeats)): ?>
                        <option value="<?= $i ?>">Seat <?= $i ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
            </select>

            <label style="margin-top:12px; display:block;"><strong>Coupon Code:</strong></label>
            <input type="text" name="coupon" placeholder="(optional)">

            <button class="btn-green" type="submit">Buy</button>
            <a class="btn-blue" href="detail.php?id=<?= $trip_id ?>">Return to Details</a>
        </form>

    </main>

    <?php require_once '../includes/footer.php'; ?>

</body>

</html>