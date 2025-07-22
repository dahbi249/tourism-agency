<?php
require "../includes/connect_db.php";

$accommodationId = $_GET['accommodation'] ?? null;

$stmt = mysqli_prepare($conn, "
    SELECT RoomTypeID, Type, PricePerNight 
    FROM roomtype 
    WHERE AccommodationID = ?
");
mysqli_stmt_bind_param($stmt, "i", $accommodationId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$roomTypes = mysqli_fetch_all($result, MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($roomTypes);