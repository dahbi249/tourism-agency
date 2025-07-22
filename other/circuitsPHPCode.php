<?php
require("../includes/connect_db.php");
$searchPerformed = false;  // Renamed flag variable
$searchTerm = "";          // Separate variable for actual search term

if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);

    if (!empty($searchTerm)) {
        $searchPerformed = true;

        $stmt = mysqli_prepare($conn, "SELECT c.CircuitID, c.Name AS `c.Name`, 
       c.Description AS `c.Description`, 
       MIN(cp.BasePrice) AS StartingPrice 
FROM circuit c 
LEFT JOIN circuitperiod cp ON c.CircuitID = cp.CircuitID
WHERE c.Name LIKE CONCAT('%', ?, '%') 
   OR c.Description LIKE CONCAT('%', ?, '%')
   
GROUP BY c.CircuitID");

        mysqli_stmt_bind_param($stmt, "ss",  $searchTerm, $searchTerm);

        if (mysqli_stmt_execute($stmt)) {
            $circuitsResult = mysqli_stmt_get_result($stmt);
        } else {
            die("Query failed: " . mysqli_error($conn));
        }
    }
}

if (!$searchPerformed) {
    $stmt = mysqli_prepare($conn, "SELECT c.CircuitID, c.Name AS `c.Name`, 
       c.Description AS `c.Description`, 
       MIN(cp.BasePrice) AS StartingPrice 
FROM circuit c 
LEFT JOIN circuitperiod cp ON c.CircuitID = cp.CircuitID
GROUP BY c.CircuitID");
    if (mysqli_stmt_execute($stmt)) {
        $circuitsResult = mysqli_stmt_get_result($stmt);
    }
}
