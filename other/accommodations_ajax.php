<?php
require("../includes/connect_db.php");
include __DIR__ . '/../lang/language.php';

$is_rtl = $is_rtl ?? false;
$limit = 6;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$filters = [];
$params = [];
$types = '';

// Add filter by search input
if (!empty($_GET['search'])) {
    $filters[] = "(Name LIKE ? OR Description LIKE ? OR Address LIKE ?)";
    $term = '%' . $_GET['search'] . '%';
    $params = array_merge($params, [$term, $term, $term]);
    $types .= 'sss';
}
// City filter from dropdown
if (!empty($_GET['city']) && $_GET['city'] !== 'all') {
    $filters[] = "City = ?";
    $params[] = $_GET['city'];
    $types .= 's';
}

// Query
$where = $filters ? 'WHERE ' . implode(' AND ', $filters) : '';
$sql = "SELECT SQL_CALC_FOUND_ROWS AccommodationID, Name, Description, Address
        FROM accommodation
        $where
        LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= 'ii';

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

// Get total count
$totalRes = mysqli_query($conn, "SELECT FOUND_ROWS() AS total");
$total = mysqli_fetch_assoc($totalRes)['total'];
$pages = ceil($total / $limit);

// Output cards
echo '<main class="' . ($is_rtl ? 'pr-[73px]' : 'pl-[73px]') . ' container mx-auto grid gap-5 lg:gap-20 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 my-5">';
if (mysqli_num_rows($res)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $id = $row['AccommodationID'];
        $mediaStmt = mysqli_prepare($conn, "
            SELECT URL, Caption FROM media
            WHERE EntityType='accommodation' AND EntityID=? AND IsPrimary=1
            LIMIT 1");
        mysqli_stmt_bind_param($mediaStmt, "i", $id);
        mysqli_stmt_execute($mediaStmt);
        $mr = mysqli_stmt_get_result($mediaStmt);
        $mr = mysqli_fetch_assoc($mr) ?: [];
        $url = $mr['URL'] ?? 'default.jpg';
        $caption = htmlspecialchars($mr['Caption'] ?? '');

        echo <<<HTML
<a href="accommodation_page.php?AccommodationID={$id}">
  <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary  hover:scale-[1.02] opacity-100 translate-y-0 transition-all duration-700 ease-out snap-start">
    <img src="../media/accommodations/{$url}" alt="{$caption}" class="w-full h-[165px] object-cover rounded-t-2xl">
    <div class="px-4 py-4 text-center">
      <h3 class="font-semibold text-lg lg:text-xl mb-1">{$row['Name']}</h3>
      <p class="text-sm mb-2">{$row['Description']}</p>
      <p class="text-sm mb-3">{$row['Address']}</p>
    </div>
  </div>
</a>
HTML;
    }
} else {
    echo '<p class="text-center text-gray-500 col-span-full">No accommodations found.</p>';
}
echo '</main>';

// Pagination links
if ($pages > 1) {
    echo '<div class="flex justify-center mt-6 space-x-2">';
    for ($i = 1; $i <= $pages; $i++) {
        $active = ($i === $page) ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700';
        echo "<a href='#' data-page='$i' class='pagination-link px-3 py-1 rounded-lg border {$active}'>$i</a>";
    }
    echo '</div>';
}

mysqli_close($conn);
?>
