<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
require_once '../includes/header.php';

if (!is_login())
    header('Location: /auth/login.php');

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
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body>

    <h1>Ticket Purchase Successful</h1>
    <p><strong>Passenger:</strong> <?= htmlspecialchars($ticket['full_name']) ?></p>
    <p><strong>Route:</strong> <?= htmlspecialchars($ticket['departure_city']) ?> → <?= htmlspecialchars($ticket['destination_city']) ?></p>
    <p><strong>Departure Time:</strong> <?= htmlspecialchars($ticket['departure_time']) ?></p>
    <p><strong>Total Price:</strong> <?= htmlspecialchars($ticket['total_price']) ?> ₺</p>

    <hr>
    <a href="download_pdf.php?id=<?= $ticket_id ?>">Download PDF</a><br>
    <a href="/tickets/my_tickets.php">My Tickets</a> <br />
    <a href="/routes/list.php">Go to Routes</a>

</body>

</html>
<?php require_once '../includes/footer.php'; ?>