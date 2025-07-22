<?php
$pageTitle = "My Reservations Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");
$stmt = mysqli_prepare($conn, "SELECT * FROM reservation WHERE CustomerID = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION["CustomerID"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>
<main class="grid grid-cols-4">
    <aside class="bg-primary h-full text-xl font-bold">
        <ul class=" px-10 py-5 space-y-5">
            <li class="px-2"><a href="http://localhost/tourism%20agency/main/userprofile.php">General</a></li>
            <hr>
            <li class="px-2"><a href="http://localhost/tourism%20agency/main/customerReservations.php">My Reservations</a></li>
            <hr>
            <li class="px-2"><a href="http://localhost/tourism%20agency/main/customerReviews.php">My reviews</a></li>
            <hr>
        </ul>
    </aside>
    <section class=" col-span-3 px-5 py-3">
    <h1 class="text-4xl text-center font-bold my-5 underline">My Reservations</h1>


        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-white ">
                <thead class="text-xs text-white uppercase border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Reservation ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Circuit Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Agency Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            StartDate
                        </th>
                        <th scope="col" class="px-6 py-3">
                            EndDate
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $stmt = mysqli_prepare($conn, "SELECT 
               r.*,
               a.Name AS AgencyName,
               a.Address AS AgencyAddress,
               c.Name AS CircuitName,
               rt.Type AS RoomType,
               acc.Name AS AccommodationName,
               acc.Address AS AccommodationAddress
               
           FROM reservation r
           JOIN customer cus ON r.CustomerID = cus.CustomerID
           JOIN agency a ON r.AgencyID = a.AgencyID
           JOIN circuit c ON r.CircuitID = c.CircuitID
           JOIN roomtype rt ON r.RoomTypeID = rt.RoomTypeID
           JOIN accommodation acc ON rt.AccommodationID = acc.AccommodationID
           JOIN circuitperiod cp ON 
               r.CircuitID = cp.CircuitID AND 
               r.StartDate BETWEEN cp.StartDate AND cp.EndDate
           WHERE r.ReservationID = ?");
                            mysqli_stmt_bind_param($stmt, "i", $row['ReservationID']);
                            mysqli_stmt_execute($stmt);
                            $agencyResult = mysqli_stmt_get_result($stmt);
                            $reservationRow = mysqli_fetch_assoc($agencyResult);
                    ?>
                            <tr class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap dark:text-white">
                                    <?= $reservationRow["ReservationID"] ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $reservationRow["CircuitName"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $reservationRow["AgencyName"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    $<?= $reservationRow["AccommodationName"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <h1>No reservations</h1>
                    <?php } ?>
                </tbody>
            </table>
        </div>


    </section>
</main>

<?php
include __DIR__ . "/../includes/footer.php";
?>