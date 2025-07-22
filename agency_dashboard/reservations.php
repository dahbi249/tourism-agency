<?php
$pageTitle = "Manage Agencies Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");

$searchPerformed = false;  
$searchTerm = "";          
$agencyID = $_SESSION['AgencyID']; // Get current agency's ID

if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);

    if (!empty($searchTerm)) {
        $searchPerformed = true;
        $stmt = mysqli_prepare($conn, "SELECT 
                    r.ReservationID,
                    c.Name,
                    c.Email,
                    c.Nationality,
                    r.StartDate,
                    r.EndDate,
                    cir.Name AS CircuitName,
                    rt.Type AS RoomType,
                    p.Amount,
                    r.NumAdults,
                    r.NumChildren,
                    r.AgencyID
                FROM reservation r
                INNER JOIN customer c ON r.CustomerID = c.CustomerID
                INNER JOIN circuit cir ON r.CircuitID = cir.CircuitID
                INNER JOIN roomtype rt ON r.RoomTypeID = rt.RoomTypeID
                LEFT JOIN payment p ON r.ReservationID = p.ReservationID
                WHERE r.AgencyID = ?
                AND (
                    r.ReservationID LIKE CONCAT('%', ?, '%')
                    OR c.Name LIKE CONCAT('%', ?, '%') 
                    OR c.Email LIKE CONCAT('%', ?, '%')
                    OR c.Nationality LIKE CONCAT('%', ?, '%')
                    OR cir.Name LIKE CONCAT('%', ?, '%')
                    OR rt.Type LIKE CONCAT('%', ?, '%')
                )");

        mysqli_stmt_bind_param($stmt, "isssssss", $agencyID, 
            $searchTerm, $searchTerm, $searchTerm, 
            $searchTerm, $searchTerm, $searchTerm
        );

        if (mysqli_stmt_execute($stmt)) {
            $reservationsResult = mysqli_stmt_get_result($stmt);
        } else {
            die("Query failed: " . mysqli_error($conn));
        }
    }
}

if (!$searchPerformed) {
    $stmt = mysqli_prepare($conn, "SELECT 
                r.ReservationID,
                c.Name,
                c.Email,
                c.Nationality,
                r.StartDate,
                r.EndDate,
                cir.Name AS CircuitName,
                rt.Type AS RoomType,
                p.Amount,
                r.NumAdults,
                r.NumChildren,
                r.AgencyID
            FROM reservation r
            INNER JOIN customer c ON r.CustomerID = c.CustomerID
            INNER JOIN circuit cir ON r.CircuitID = cir.CircuitID
            INNER JOIN roomtype rt ON r.RoomTypeID = rt.RoomTypeID
            LEFT JOIN payment p ON r.ReservationID = p.ReservationID
            WHERE r.AgencyID = ?");
    
    mysqli_stmt_bind_param($stmt, "i", $agencyID);
    if (mysqli_stmt_execute($stmt)) {
        $reservationsResult = mysqli_stmt_get_result($stmt);
    }
}
?>

<main class="grid grid-cols-4">
    <?php include("aside.php") ?>

    <section class="col-span-3 px-5 py-3">
        <section class="flex flex-col items-center justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out bg-hero-pattern-customers bg-cover bg-no-repeat bg-center h-[517px]">
            <form action="" method="get" class="relative">
                <input type="search" name="search" placeholder="<?php echo $lang['search_placeholder'] ?>" 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                       class="text-lg md:text-xl outline-none border-none text-black w-[285px] h-[41px] lg:w-[482px] lg:h-[70px] rounded-full px-2">
                <input type="submit" value="<?php echo $lang['search_placeholder'] ?>" 
                       class="absolute <?= $is_rtl ? 'left-0' : 'right-0' ?> cursor-pointer text-white px-1 lg:px-3 h-[41px] lg:h-[70px] bg-primary rounded-full text-[18px] lg:text-[20px] font-semibold">
            </form>
        </section>

        <section class="relative overflow-x-auto shadow-md sm:rounded-lg opacity-0 translate-y-20 transition-all duration-1000 ease-out text-center">
            <h1 class="text-4xl text-center font-bold my-5 underline">Manage reservations</h1>
            <table class="w-full text-sm text-left rtl:text-right text-white">
                <thead class="text-xs text-white uppercase border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3">ReservationID</th>
                        <th scope="col" class="px-6 py-3">Customer</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Nationality</th>
                        <th scope="col" class="px-6 py-3">Circuit</th>
                        <th scope="col" class="px-6 py-3">Room Type</th>
                        <th scope="col" class="px-6 py-3">Amount</th>
                        <th scope="col" class="px-6 py-3">Adults</th>
                        <th scope="col" class="px-6 py-3">Children</th>
                        <th scope="col" class="px-6 py-3">Start Date</th>
                        <th scope="col" class="px-6 py-3">End Date</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($reservationsResult) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($reservationsResult)): ?>
                            <tr class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <td class="px-6 py-4 font-medium whitespace-nowrap"><?= $row['ReservationID'] ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['Name']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['Email']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['Nationality']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['CircuitName']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['RoomType']) ?></td>
                                <td class="px-6 py-4"><?= number_format($row['Amount'] ?? 0, 2) ?> DZD</td>
                                <td class="px-6 py-4"><?= $row['NumAdults'] ?></td>
                                <td class="px-6 py-4"><?= $row['NumChildren'] ?></td>
                                <td class="px-6 py-4"><?= $row['StartDate'] ?></td>
                                <td class="px-6 py-4"><?= $row['EndDate'] ?></td>
                                <td class="px-6 py-4">
                                    <a href="#" class="font-medium text-blue-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="px-6 py-4 text-center">
                                <h1 class="text-xl">No reservations found</h1>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </section>
</main>

<?php
include __DIR__ . "/../includes/footer.php";
?>