<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once '../includes/header.php';

if (!is_login())
    header('Location: /auth/login.php');

$user_id = $_SESSION['user']['id'];

if (isset($_GET['cancel'])) {
    $ticket_id = $_GET['cancel'];

    $stmt = $db->prepare("SELECT * FROM Tickets WHERE id = ? AND user_id = ? AND status = 'active'");
    $stmt->execute([$ticket_id, $user_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE Tickets SET status = 'canceled' WHERE id = ?");
        $stmt->execute([$ticket_id]);

        $stmt = $db->prepare("DELETE FROM Booked_Seats WHERE ticket_id = ?");
        $stmt->execute([$ticket_id]);

        $stmt = $db->prepare("UPDATE Users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$ticket['total_price'], $user_id]);

        $db->commit();
    }

    header("Location: my_tickets.php");
    exit;
}

$stmt = $db->prepare("
    SELECT T.id AS ticket_id, T.total_price, T.status, T.created_at,
           Tr.departure_city, Tr.destination_city, Tr.departure_time,
           GROUP_CONCAT(BS.seat_number, ', ') AS seats
    FROM Tickets T
    JOIN Trips Tr ON T.trip_id = Tr.id
    LEFT JOIN Booked_Seats BS ON BS.ticket_id = T.id
    WHERE T.user_id = ?
    GROUP BY T.id
    ORDER BY T.created_at DESC
");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>My Tickets</title>

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

        table {
            margin: 30px auto;
            border-collapse: collapse;
            width: 90%;
            max-width: 900px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #00eaff;
            text-align: center;
        }

        tr:hover {
            background: rgba(0, 255, 255, 0.07);
        }

        a.details-btn,
        a.cancel-btn,
        a.back-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            cursor: pointer;
            margin: 3px;
        }

        a.details-btn {
            background: #00bddfff;
            color: black;
        }

        a.details-btn:hover {
            background: #007e94ff;
        }

        a.cancel-btn {
            background: rgba(179, 0, 0, 1);
            color: #fff;
        }

        a.cancel-btn:hover {
            background: rgba(126, 0, 0, 1);
        }

        a.back-btn {
            background: #00eaff;
            color: #000;
            margin-top: 20px;
        }

        a.back-btn:hover {
            background: #00b6cc;
        }
    </style>
</head>

<body>

    <main>
        <h1>My Tickets</h1>

        <?php if (count($tickets) === 0): ?>
            <p>You have no tickets.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Route</th>
                    <th>Date</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Operation</th>
                </tr>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['departure_city']) ?> → <?= htmlspecialchars($t['destination_city']) ?></td>
                        <td><?= date("d.m.Y H:i", strtotime($t['departure_time'])) ?></td>
                        <td><?= htmlspecialchars($t['seats']) ?></td>
                        <td><?= htmlspecialchars($t['total_price']) ?> ₺</td>
                        <td><?= htmlspecialchars($t['status']) ?></td>
                        <td>
                            <a class="details-btn" href="/tickets/ticket_view.php?id=<?= $t['ticket_id'] ?>">View</a>
                            <?php if ($t['status'] === 'active'): ?>
                                <a class="cancel-btn" href="?cancel=<?= $t['ticket_id'] ?>" onclick="return confirm('Cancel this ticket?')">Cancel</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <a class="back-btn" href="../index.php">&larr; Back to Home</a>
    </main>

    <?php require_once '../includes/footer.php'; ?>

</body>

</html>