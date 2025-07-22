<?php
$pageTitle = "Profile Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");

$stmt = mysqli_prepare($conn, "SELECT 
  r.Comment,
  r.Rate,
  r.EntityType,
  r.EntityID,
  CASE
    WHEN r.EntityType = 'Agency' THEN a.Name
    WHEN r.EntityType = 'Location' THEN l.Name
    WHEN r.EntityType = 'Circuit' THEN c.Name
    WHEN r.EntityType = 'Accommodation' THEN acc.Name
  END AS EntityName
FROM review r
LEFT JOIN agency a 
  ON r.EntityType = 'Agency' AND r.EntityID = a.AgencyID
LEFT JOIN location l 
  ON r.EntityType = 'Location' AND r.EntityID = l.LocationID
LEFT JOIN circuit c 
  ON r.EntityType = 'Circuit' AND r.EntityID = c.CircuitID
LEFT JOIN accommodation acc 
  ON r.EntityType = 'Accommodation' AND r.EntityID = acc.AccommodationID
 WHERE CustomerID = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION["CustomerID"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>
<main class="grid grid-cols-4">
    <aside class="bg-primary h-full text-xl font-bold ">
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
        <h1 class="text-4xl text-center font-bold my-5 underline">My Reviews</h1>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-white ">
                <thead class="text-xs text-white uppercase border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Entity Type
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Entity Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Comment
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Rate
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>

                            <tr class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap dark:text-white">
                                    <?= $row["EntityType"] ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $row["EntityName"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["Comment"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["Rate"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline px-1">Edit</a>
                                    <a href="#" class="font-medium text-red-600 dark:text-red-500 hover:underline px-1">Delete</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <h1>No Reviews</h1>
                    <?php } ?>
                </tbody>
            </table>
    </section>
</main>

<?php
include __DIR__ . "/../includes/footer.php";
?>