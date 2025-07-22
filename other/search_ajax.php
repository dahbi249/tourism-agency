<?php
require("../includes/connect_db.php");

$limit = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];
$paramTypes = '';

$query = "
SELECT SQL_CALC_FOUND_ROWS c.CircuitID, c.Name AS `c.Name`, c.Description AS `c.Description`, 
       MIN(cp.BasePrice) AS StartingPrice
FROM circuit c
LEFT JOIN circuitperiod cp ON c.CircuitID = cp.CircuitID
LEFT JOIN circuitlocation cl ON c.CircuitID = cl.CircuitID
LEFT JOIN location l ON cl.LocationID = l.LocationID
";

if (!empty($_GET['city'])) {
    $whereClauses[] = "l.City = ?";
    $params[] = $_GET['city'];
    $paramTypes .= 's';
}

if (!empty($_GET['location'])) {
    $whereClauses[] = "l.Name = ?";
    $params[] = $_GET['location'];
    $paramTypes .= 's';
}


if (!empty($_GET['min_price'])) {
    $whereClauses[] = "cp.BasePrice >= ?";
    $params[] = $_GET['min_price'];
    $paramTypes .= 'd';
}

if (!empty($_GET['max_price'])) {
    $whereClauses[] = "cp.BasePrice <= ?";
    $params[] = $_GET['max_price'];
    $paramTypes .= 'd';
}

if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$query .= " GROUP BY c.CircuitID LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$paramTypes .= 'ii';

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
mysqli_stmt_execute($stmt);
$circuitsResult = mysqli_stmt_get_result($stmt);

$totalResult = mysqli_query($conn, "SELECT FOUND_ROWS() AS total");
$total = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($total / $limit);

$is_rtl = false; // Set this to true if using RTL layout

echo '<main class="' . ($is_rtl ? 'pr-[73px] md:pr-[45px] lg:pr-0' : 'pl-[73px] md:pl-[45px] lg:pl-0') . '  container mx-auto grid grid-cols-1 gap-5 lg:gap-20 md:grid-cols-2 lg:grid-cols-3 my-5">';

if (mysqli_num_rows($circuitsResult) > 0) {
    while ($circuitsRows = mysqli_fetch_assoc($circuitsResult)) {
        // Fetch primary media for the circuit
        $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'circuit' AND EntityID = ? AND IsPrimary = 1 LIMIT 1");
        mysqli_stmt_bind_param($mediaStmt, "i", $circuitsRows['CircuitID']);
        mysqli_stmt_execute($mediaStmt);
        $mediaResult = mysqli_stmt_get_result($mediaStmt);
        $mediaRow = mysqli_fetch_assoc($mediaResult);

        $imageURL = "../media/circuits/" . ($mediaRow["URL"] ?? "default.jpeg");
        $imageCaption = htmlspecialchars($mediaRow["Caption"] ?? "");

        echo '<a href="http://localhost/tourism%20agency/main/circuit_page.php?CircuitID=' . $circuitsRows['CircuitID'] . '">
        <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02] ">
            <img src="' . $imageURL . '" alt="' . $imageCaption . '" class="w-full h-[165px] object-cover rounded-t-2xl">
            <div class="px-4 py-4 text-center">
                <h3 class="font-semibold text-lg lg:text-xl mb-1">' . htmlspecialchars($circuitsRows["c.Name"]) . '</h3>
                <p class="text-sm mb-2">' . htmlspecialchars($circuitsRows["c.Description"]) . '</p>
                <div class="text-lg font-semibold text-primary mb-3">'
                    . number_format($circuitsRows['StartingPrice'], 2) . ' DZD
                </div>
            </div>
        </div>
        </a>';
    }
} else {
    echo '<p class="text-center text-gray-500 col-span-full">No circuits found matching your filters.</p>';
}

echo '</main>';

// Pagination
if ($totalPages > 1) {
    echo '<div class="flex justify-center mt-6 space-x-2">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="#" data-page="' . $i . '" class="pagination-link px-3 py-1 rounded-lg border ' . ($i == $page ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700') . '">' . $i . '</a>';
    }
    echo '</div>';
}
?>
