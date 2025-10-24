<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
include ROOT_PATH . '/includes/functions.php';

if (!is_login())
    header('Location: /auth/login.php');

//POST
$trip_id = $_POST['trip_id'] ?? null;
$seat_number = $_POST['seat_number'] ?? null;
$coupon_code = trim($_POST['coupon'] ?? "");
$user_id = $_SESSION['user']['id'];

// empty
if (!$trip_id || !$seat_number) {
    die("Invalid inputs.");
}

// get route
$stmt = $db->prepare("SELECT * FROM Trips WHERE id = ?");
$stmt->execute([$trip_id]);
$trip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    die("No Route.");
}

$base_price = $trip['price'];
$total_price = $base_price;

// ccheck seat availability
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM Booked_Seats
    JOIN Tickets ON Booked_Seats.ticket_id = Tickets.id
    WHERE Tickets.trip_id = ? AND Booked_Seats.seat_number = ?
");
$stmt->execute([$trip_id, $seat_number]);
$isBooked = $stmt->fetchColumn();

if ($isBooked) {
    die("This seat is full.");
}

//check coupıon
$applied_coupon_id = null;

if ($coupon_code !== "") {
    $stmt = $db->prepare("SELECT * FROM Coupons WHERE code = ? AND (usage_limit > 0) AND expire_date > datetime('now')");
    $stmt->execute([$coupon_code]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon) {
        $discount = (int)$coupon['discount'];
        $total_price = $base_price - $discount;
        $applied_coupon_id = $coupon['id'];
    } else {
        die("Invalid Coupon.");
    }
}

//check balance
$stmt = $db->prepare("SELECT balance FROM Users WHERE id = ?");
$stmt->execute([$user_id]);
$user_balance = $stmt->fetchColumn();

if ($user_balance < $total_price) {
    die("Not Enough Balance. Current Balance: {$user_balance} TL");
}

//add ticker
$ticket_id = uuid();
$stmt = $db->prepare("INSERT INTO Tickets (id, trip_id, user_id, total_price) VALUES (?, ?, ?, ?)");
$stmt->execute([$ticket_id, $trip_id, $user_id, $total_price]);

//add seat
$stmt = $db->prepare("INSERT INTO Booked_Seats (id, ticket_id, seat_number) VALUES (?, ?, ?)");
$stmt->execute([uuid(), $ticket_id, $seat_number]);

//decreise balance
$stmt = $db->prepare("UPDATE Users SET balance = balance - ? WHERE id = ?");
$stmt->execute([$total_price, $user_id]);

//-1 couppn usage
if ($applied_coupon_id != null) {
    $stmt = $db->prepare("UPDATE Coupons SET usage_limit = usage_limit - 1 WHERE id = ?");
    $stmt->execute([$applied_coupon_id]);
}

header("Location: /tickets/ticket_view.php?id=" . $ticket_id);
exit;
