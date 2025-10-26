<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include '../includes/functions.php';

if (!is_login())
    header('Location: /auth/login.php');

require_once '../includes/header.php';

//check id
if (!isset($_GET['id'])) {
    die("Ticket ID is missing.");
}

$ticket_id = $_GET['id'];

//get ticket
$stmt = $db->prepare("SELECT T.*, U.full_name, Tr.destination_city, Tr.departure_city, Tr.departure_time
                      FROM Tickets T
                      JOIN Users U ON T.user_id = U.id
                      JOIN Trips Tr ON T.trip_id = Tr.id
                      WHERE T.id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("Ticket not found.");
}

// Get updated user balance
$stmt = $db->prepare("SELECT balance FROM Users WHERE id = ?");
$stmt->execute([$_SESSION['user']['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $user['balance'];
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Ticket Purchase Successful</title>
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

        .btn-green,
        .btn-blue {
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: none;
            box-sizing: border-box;
            margin: 10px auto;
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
        <h1>Ticket Purchase Successful</h1>

        <div class="info-box">
            <p><strong>Passenger:</strong> <?= htmlspecialchars($ticket['full_name']) ?></p>
            <p><strong>Route:</strong> <?= htmlspecialchars($ticket['departure_city']) ?> → <?= htmlspecialchars($ticket['destination_city']) ?></p>
            <p><strong>Departure Time:</strong> <?= date("d.m.Y H:i", strtotime($ticket['departure_time'])) ?></p>
            <p><strong>Total Price:</strong> <?= htmlspecialchars($ticket['total_price']) ?> ₺</p>
            <p><strong>Your Balance:</strong> <?= $balance ?> TL</p>
        </div>

        <a class="btn-green" href="download_pdf.php?id=<?= $ticket_id ?>">Download PDF</a>
        <a class="btn-blue" href="/tickets/my_tickets.php">My Tickets</a>
        <a class="btn-blue" href="/routes/list.php">Go to Routes</a>
    </main>

    <?php require_once '../includes/footer.php'; ?>

</body>

</html>