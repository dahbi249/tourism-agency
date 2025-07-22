<?php
require("../includes/connect_db.php");
$searchPerformed = false;  // Renamed flag variable
$searchTerm = "";          // Separate variable for actual search term

if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);

    if (!empty($searchTerm)) {
        $searchPerformed = true;

        $stmt = mysqli_prepare($conn, "SELECT * FROM location 
            WHERE Name LIKE CONCAT('%', ?, '%')
            OR Description LIKE CONCAT('%', ?, '%')
            OR Address LIKE CONCAT('%', ?, '%')");

        // Bind the ACTUAL SEARCH TERM 3 times
        mysqli_stmt_bind_param($stmt, "sss", $searchTerm, $searchTerm, $searchTerm);

        if (mysqli_stmt_execute($stmt)) {
            $accommodationsResult = mysqli_stmt_get_result($stmt);
        } else {
            // Add error handling
            die("Query failed: " . mysqli_error($conn));
        }
    }
}

if (!$searchPerformed) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM accommodation");
    if (mysqli_stmt_execute($stmt)) {
        $accommodationsResult = mysqli_stmt_get_result($stmt);
    }
}


