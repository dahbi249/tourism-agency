<?php
require "../includes/connect_db.php";

$circuitId = $_GET['circuit'] ?? null;

$stmt = mysqli_prepare($conn, "
    SELECT BasePrice 
    FROM circuitperiod 
    WHERE CircuitID = ?
    ORDER BY StartDate DESC 
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $circuitId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode(['basePrice' => $data['BasePrice'] ?? 0]);