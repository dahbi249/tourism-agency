<?php
$pageTitle = "Manage Contracts Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");
$searchPerformed = false;  // Renamed flag variable
$searchTerm = "";          // Separate variable for actual search term

if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);

    if (!empty($searchTerm)) {
        $searchPerformed = true;
    }
}


$stmt = mysqli_prepare($conn, "SELECT 
    ac.AccommodationID AS AccommodationID,
    a.Name AS accommodation_name,
    a.Description AS accommodation_description,
    a.Address AS accommodation_address,
    ac.StartDate AS StartDate,
    ac.EndDate AS EndDate ,
    ac.Status AS Status
FROM accommodationcontract ac
JOIN accommodation a ON ac.AccommodationID = a.AccommodationID
WHERE ac.AgencyID = ?");
mysqli_stmt_bind_param($stmt, "i",  $_SESSION["AgencyID"]);
if (mysqli_stmt_execute($stmt)) {
    $agenciesResult = mysqli_stmt_get_result($stmt);
} else {
    echo "error";
}


?>
<main class="grid grid-cols-4">
    <?php include("aside.php") ?>

    <section class=" col-span-3 px-5 py-3">
        <section class="relative overflow-x-auto shadow-md sm:rounded-lg opacity-0 translate-y-20 transition-all duration-1000 ease-out text-center">
            <a href="add_contract.php"><button class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-green-500 mt-5 rounded-[10px] text-[24px] font-semibold mb-4">Add A New Contract</button></a>
            <h1 class="text-4xl text-center font-bold my-10 underline">Manage circuits</h1>
            <table class="w-full text-sm text-left rtl:text-right text-white ">
                <thead class="text-xs text-white uppercase border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            CircuitID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Start Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            End Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IsValidated
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php



                    if (mysqli_num_rows($agenciesResult) > 0) {
                        while ($row = mysqli_fetch_assoc($agenciesResult)) {
                    ?>
                            <tr class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <!-- In the table rows -->
                                <td class="px-6 py-4">
                                    <?= $row["AccommodationID"] ?? 'N/A' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["accommodation_name"] ?? 'No name' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["accommodation_description"] ?? 'No description' ?>
                                </td>
                                 <td class="px-6 py-4">
                                    <?= $row["StartDate"] ?? 'No Start Date' ?>
                                </td>
                                 <td class="px-6 py-4">
                                    <?= $row["EndDate"] ?? 'No End Date' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["Status"] ? 'Yes' : 'No' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <h1>No agencies</h1>
                    <?php } ?>
                </tbody>
            </table>
            </div>


        </section>
</main>

<?php
include __DIR__ . "/../includes/footer.php";
?>