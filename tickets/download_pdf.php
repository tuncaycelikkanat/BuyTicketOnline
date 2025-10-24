<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/tfpdf/tfpdf.php';

if (!is_login()) header('Location: /auth/login.php');
if (!isset($_GET['id'])) die("Ticket ID missing.");

$user_id = $_SESSION['user']['id'];
$ticket_id = $_GET['id'];

// Get ticket
$stmt = $db->prepare("
    SELECT T.id AS ticket_id, T.total_price, T.status, T.created_at,
           U.full_name, 
           BC.name AS company_name,
           Tr.departure_city, Tr.destination_city, Tr.departure_time, Tr.arrival_time
    FROM Tickets T
    JOIN Users U ON U.id = T.user_id
    JOIN Trips Tr ON Tr.id = T.trip_id
    JOIN Bus_Company BC ON BC.id = Tr.company_id
    WHERE T.id = ? AND T.user_id = ?
");
$stmt->execute([$ticket_id, $user_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ticket) die("Ticket not found or unauthorized.");

// Seat list
$stmt = $db->prepare("SELECT seat_number FROM Booked_Seats WHERE ticket_id = ?");
$stmt->execute([$ticket_id]);
$seats = implode(", ", $stmt->fetchAll(PDO::FETCH_COLUMN));

//assign pdf 
$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'DejaVuSerifCondensed.ttf', true);
$pdf->AddFont('DejaVu', 'B', 'DejaVuSerifCondensed-Bold.ttf', true);

// Minimal Header
$pdf->SetFont('DejaVu', 'B', 20);
$pdf->Cell(0, 10, "Bus Ticket", 0, 1, 'C');
$pdf->Ln(5);

// Ticket Info
$pdf->SetFont('DejaVu', '', 14);
$pdf->Cell(50, 8, "Passenger:", 0, 0);
$pdf->Cell(0, 8, $ticket['full_name'], 0, 1);

$pdf->Cell(50, 8, "Company:", 0, 0);
$pdf->Cell(0, 8, $ticket['company_name'], 0, 1);

$pdf->Cell(50, 8, "Route:", 0, 0);
$pdf->Cell(0, 8, $ticket['departure_city'] . " → " . $ticket['destination_city'], 0, 1);

$pdf->Cell(50, 8, "Departure:", 0, 0);
$pdf->Cell(0, 8, $ticket['departure_time'], 0, 1);

$pdf->Cell(50, 8, "Arrival:", 0, 0);
$pdf->Cell(0, 8, $ticket['arrival_time'], 0, 1);

$pdf->Cell(50, 8, "Seats:", 0, 0);
$pdf->Cell(0, 8, $seats, 0, 1);

$pdf->Cell(50, 8, "Total Price:", 0, 0);
$pdf->Cell(0, 8, $ticket['total_price'] . " TL", 0, 1);

$pdf->Cell(50, 8, "Status:", 0, 0);
$pdf->Cell(0, 8, $ticket['status'], 0, 1);

// Footer
$pdf->Ln(10);
$pdf->SetFont('DejaVu', '', 12);
$pdf->Cell(0, 10, "Thank you for traveling with us!", 0, 1, 'C');

$pdf->Output("D", "ticket_$ticket_id.pdf");
exit;
