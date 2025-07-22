<?php
require "../includes/connect_db.php";

$agencyId = $_GET['agency'] ?? null;
$circuitId = $_GET['circuit'] ?? null;

$stmt = mysqli_prepare($conn, "
    SELECT a.AccommodationID, a.Name 
    FROM agency_circuit_accommodation aca
    JOIN accommodation a ON  a.AccommodationID = aca.AccommodationID
    JOIN accommodationcontract ac ON aca.AccommodationID = ac.AccommodationID
    WHERE aca.AgencyID = ? AND aca.CircuitID = ?
    AND ac.Status = 'active'
");
mysqli_stmt_bind_param($stmt, "ii", $agencyId, $circuitId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$accommodations = mysqli_fetch_all($result, MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($accommodations);