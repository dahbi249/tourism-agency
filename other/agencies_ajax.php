<?php
session_start();
require("../includes/connect_db.php");
include __DIR__ . '/../lang/language.php';
require("agenciesPHPCode.php");


// Pagination settings
$limit = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search filter
$searchTerm = !empty($_GET['search']) ? trim($_GET['search']) : '';

$whereClause = '';
$params = [];
$paramTypes = '';

if ($searchTerm !== '') {
    $whereClause = "WHERE a.Name LIKE ? OR a.Description LIKE ? OR a.Address LIKE ?";
    $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
    $paramTypes = 'sss';
}

$query = "
SELECT SQL_CALC_FOUND_ROWS a.AgencyID, a.Name, a.Description, a.Address
FROM agency a
$whereClause
LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$paramTypes .= 'ii';

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$totalRes = mysqli_query($conn, "SELECT FOUND_ROWS() AS total");
$total = mysqli_fetch_assoc($totalRes)['total'];
$totalPages = ceil($total / $limit);

$agenciesRows = mysqli_fetch_assoc($agenciesResult);
$customerID = $_SESSION['CustomerID'] ?? null;



// Render cards
echo '<main class="' . ($is_rtl ? 'pr-[73px] md:pr-[45px] lg:pr-0' : 'pl-[73px] md:pl-[45px] lg:pl-0') . ' container mx-auto grid grid-cols-1 gap-5 lg:gap-20 md:grid-cols-2 lg:grid-cols-3 my-5">';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $mediaStmt = mysqli_prepare($conn, "SELECT URL, Caption FROM media WHERE EntityType = 'agency' AND EntityID = ? AND IsPrimary = 1 LIMIT 1");
        mysqli_stmt_bind_param($mediaStmt, "i", $row['AgencyID']);
        mysqli_stmt_execute($mediaStmt);
        $media = mysqli_stmt_get_result($mediaStmt);
        $mediaRow = mysqli_fetch_assoc($media);

        $url = $mediaRow['URL'] ?? 'default.jpg';
        $caption = htmlspecialchars($mediaRow['Caption'] ?? '');


        $agencyID = $row['AgencyID'];
$wishlisted = false;

// Check if this agency is wishlisted
if ($customerID) {
    $checkSql = "SELECT 1 FROM wishlist WHERE CustomerID = ? AND EntityType = 'agency' AND EntityID = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($stmt, "ii", $customerID, $agencyID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $wishlisted = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
}
        echo '
                <section class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary hover:scale-[1.02] opacity-100 translate-y-0 transition-all duration-1000 ease-out snap-start">
                                  <!-- Heart icon inside the card -->
                           <button
        class="absolute top-3 right-3 text-4xl  focus:outline-none wishlist-btn z-10 transition-colors duration-300"
        data-entity-id="' . $agencyID . '"
        data-entity-type="agency"
        style="color: ' . ($wishlisted ? 'red' : 'gray') . '"
    >
        <i class="bx bxs-heart"></i>
    </button>
                    <a href="agency_page.php?AgencyID=' . $row['AgencyID'] . '">
                    <img src="../media/agencies/' . $url . '" alt="' . $caption . '" class="w-[575px]  h-[165px] object-cover rounded-t-2xl">
                    <section class="px-4 py-4 text-center">
                        <h3 class="font-semibold text-lg lg:text-xl mb-1">' . htmlspecialchars($row['Name']) . '</h3>
                        <p class="text-sm mb-2">' . htmlspecialchars($row['Description']) . '</p>
                        <p class="text-sm mb-3">' . htmlspecialchars($row['Address']) . '</p>
                    </section>
                    </a>
                </section>
              ';
    }
} else {
    echo '<p class="text-center text-gray-500 col-span-full">No agencies found.</p>';
}

echo '</main>';

// Pagination tabs
if ($totalPages > 1) {
    echo '<div class="flex justify-center mt-6 space-x-2">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i === $page ? 'bg-primary  text-white' : 'bg-gray-200 text-gray-700';
        echo '<a href="#" data-page="' . $i . '" class="pagination-link px-3 py-1 rounded-lg border ' . $active . '">' . $i . '</a>';
    }
    echo '</div>';
}
?>
<?php mysqli_close($conn); ?>

