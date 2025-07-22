<?php
require "../includes/connect_db.php";

header('Content-Type: application/json');

// Validate input
if (!isset($_GET['agency_id']) || !isset($_GET['circuit_id'])) {
    die(json_encode(['error' => 'Missing parameters']));
}

$agencyId = (int)$_GET['agency_id'];
$circuitId = (int)$_GET['circuit_id'];

// Get agency-specific periods with pricing
$stmt = mysqli_prepare($conn, "
    SELECT 
        ac.StartDate AS start_date,
        ac.EndDate AS end_date,
        cp.BasePrice,
        cp.availablePlaces
    FROM agency_circuit ac
    JOIN circuitperiod cp 
        ON ac.CircuitID = cp.CircuitID 
        AND ac.StartDate = cp.StartDate
    WHERE ac.AgencyID = ?
    AND ac.CircuitID = ?
    AND ac.IsActive = 1
    ORDER BY ac.StartDate
");

mysqli_stmt_bind_param($stmt, "ii", $agencyId, $circuitId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$periods = [];
while ($row = mysqli_fetch_assoc($result)) {
    $periods[] = [
        'start' => $row['start_date'],
        'end' => $row['end_date'],
        'price' => $row['BasePrice'],
        'available' => $row['availablePlaces']
    ];
}

echo json_encode($periods);