<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/connect_db.php';

$response = [];

if (!isset($_SESSION['CustomerID'])) {
    $response = ['success' => false, 'message' => 'Not logged in'];
    echo json_encode($response);
    exit;
}

$customerID = $_SESSION['CustomerID'];
$entityType = $_POST['entityType'] ?? null;
$entityID = $_POST['entityID'] ?? null;

// TEMP DEBUG: Log the incoming data
file_put_contents("debug_wishlist.txt", json_encode([
    'CustomerID' => $customerID,
    'entityType' => $entityType,
    'entityID' => $entityID,
    'POST' => $_POST,
    'SESSION' => $_SESSION
]) . PHP_EOL, FILE_APPEND);

$validTypes = ['agency', 'circuit', 'location', 'accommodation'];

if (!$entityType || !$entityID || !in_array($entityType, $validTypes)) {
    $response = ['success' => false, 'message' => 'Invalid request'];
    echo json_encode($response);
    exit;
}

// Check if already wishlisted
$query = "SELECT * FROM wishlist WHERE CustomerID = ? AND EntityType = ? AND EntityID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "isi", $customerID, $entityType, $entityID);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    $delete = mysqli_prepare($conn, "DELETE FROM wishlist WHERE CustomerID = ? AND EntityType = ? AND EntityID = ?");
    mysqli_stmt_bind_param($delete, "isi", $customerID, $entityType, $entityID);
    mysqli_stmt_execute($delete);
    $response = ['success' => true, 'wishlisted' => false];
} else {
    $insert = mysqli_prepare($conn, "INSERT INTO wishlist (CustomerID, EntityType, EntityID) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insert, "isi", $customerID, $entityType, $entityID);
    mysqli_stmt_execute($insert);
    $response = ['success' => true, 'wishlisted' => true];
}

echo json_encode($response);
exit;
