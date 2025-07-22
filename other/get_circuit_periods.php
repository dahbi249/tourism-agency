<?php
// get_periods.php
require "../includes/connect_db.php";

header('Content-Type: application/json');

if (!isset($_GET['circuit_id']) || !isset($_GET['agency_id'])) {
    die(json_encode(['error' => 'Missing parameters']));
}

$circuitID = (int)$_GET['circuit_id'];
$agencyID = (int)$_GET['agency_id'];

$stmt = mysqli_prepare($conn, "
    SELECT cp.StartDate AS start, cp.EndDate AS end, cp.BasePrice AS price 
    FROM circuitperiod cp
    LEFT JOIN agency_circuit ac 
        ON cp.CircuitID = ac.CircuitID 
        AND cp.StartDate = ac.StartDate 
        AND ac.AgencyID = ?
    WHERE cp.CircuitID = ?
    AND ac.CircuitID IS NULL  // Changed to check for NULL in joined table
    ORDER BY cp.StartDate
");

mysqli_stmt_bind_param($stmt, "ii", $agencyID, $circuitID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$periods = [];
while ($row = mysqli_fetch_assoc($result)) {
    $periods[] = [
        'start' => $row['start'],
        'end' => $row['end'],
        'price' => $row['price']
    ];
}

echo json_encode($periods);
?>