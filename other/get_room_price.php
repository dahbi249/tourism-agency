<?php
require "../includes/connect_db.php";

$roomTypeId = $_GET['room'] ?? null;

$stmt = mysqli_prepare($conn, "
    SELECT PricePerNight 
    FROM roomtype 
    WHERE RoomTypeID = ?
");
mysqli_stmt_bind_param($stmt, "i", $roomTypeId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode(['pricePerNight' => $data['PricePerNight'] ?? 0]);