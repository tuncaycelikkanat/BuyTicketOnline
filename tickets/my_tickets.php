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

    //get ticket
    $stmt = $db->prepare("SELECT * FROM Tickets WHERE id = ? AND user_id = ? AND status = 'active'");
    $stmt->execute([$ticket_id, $user_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        $db->beginTransaction();

        //ticket canceled
        $stmt = $db->prepare("UPDATE Tickets SET status = 'canceled' WHERE id = ?");
        $stmt->execute([$ticket_id]);

        //remove seats
        $stmt = $db->prepare("DELETE FROM Booked_Seats WHERE ticket_id = ?");
        $stmt->execute([$ticket_id]);

        //add to balance
        $stmt = $db->prepare("UPDATE Users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$ticket['total_price'], $user_id]);

        //add to coupon usage limit

        $db->commit();
    }

    header("Location: my_tickets.php");
    exit;
}


//get tickets
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
</head>

<body>

    <h1>My Tickets</h1>
    <hr>

    <?php if (count($tickets) === 0): ?>
        <p>You have no tickets.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
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
                    <td><?= htmlspecialchars($t['departure_city']) ?> -> <?= htmlspecialchars($t['destination_city']) ?></td>
                    <td><?= htmlspecialchars($t['departure_time']) ?></td>
                    <td><?= htmlspecialchars($t['seats']) ?></td>
                    <td><?= htmlspecialchars($t['total_price']) ?> ₺</td>
                    <td><?= htmlspecialchars($t['status']) ?></td>
                    <td>
                        <a href="/tickets/ticket_view.php?id=<?= $t['ticket_id'] ?>">View</a>

                        <?php if ($t['status'] === 'active'): ?>
                            | <a href="?cancel=<?= $t['ticket_id'] ?>" onclick="return confirm('Cancel this ticket?')">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="index.php"><- Back to Home</a>

</body>

</html>
<?php require_once '../includes/footer.php'; ?>