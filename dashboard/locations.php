<?php
$pageTitle = "Manage Agencies Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");
$searchPerformed = false;  // Renamed flag variable
$searchTerm = "";          // Separate variable for actual search term

if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);

    if (!empty($searchTerm)) {
        $searchPerformed = true;

        $stmt = mysqli_prepare($conn, "SELECT * FROM location 
            WHERE
            LocationID LIKE CONCAT('%', ?, '%')
            OR Name LIKE CONCAT('%', ?, '%')
            OR Address LIKE CONCAT('%', ?, '%')
            OR Description LIKE CONCAT('%', ?, '%')");

        // Bind the ACTUAL SEARCH TERM 3 times
        mysqli_stmt_bind_param($stmt, "ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        if (mysqli_stmt_execute($stmt)) {
            $locationsResult = mysqli_stmt_get_result($stmt);
        } else {
            // Add error handling
            die("Query failed: " . mysqli_error($conn));
        }
    }
}

if (!$searchPerformed) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM location");
    if (mysqli_stmt_execute($stmt)) {
        $locationsResult = mysqli_stmt_get_result($stmt);
    }
}




?>
<main class="grid grid-cols-4">
    <?php include("aside.php") ?>

    <section class=" col-span-3 px-5 py-3">
        <section class="flex flex-col items-center justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out bg-hero-pattern-locations  bg-cover bg-no-repeat bg-center h-[517px]">
            <form action="" method="get" class="relative">
                <input type="search" name="search" id="" placeholder="<?php echo $lang['search_placeholder'] ?>" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>" class="text-lg md:text-xl outline-none border-none text-black w-[285px] h-[41px] lg:w-[482px] lg:h-[70px] rounded-full px-2">
                <input type="submit" value="<?php echo $lang['search_placeholder'] ?>" class=" absolute <?= $is_rtl ? 'left-0' : 'right-0' ?>  cursor-pointer text-white px-1 lg:px-3 h-[41px] lg:h-[70px] bg-primary rounded-full text-[18px] lg:text-[20px] font-semibold ">
            </form>
        </section>
        <section class="relative overflow-x-auto shadow-md sm:rounded-lg opacity-0 translate-y-20 transition-all duration-1000 ease-out text-center">
            <a href="add_location.php"><button  class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-green-500 mt-5 rounded-[10px] text-[24px] font-semibold mb-4">Add A New Location</button></a>
            <h1 class="text-4xl text-center font-bold my-5 underline">Manage Locations</h1>
            <table class="w-full text-sm text-left rtl:text-right text-white ">
                <thead class="text-xs text-white uppercase border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            LocationID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            City
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Address
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($locationsResult) > 0) {
                        while ($row = mysqli_fetch_assoc($locationsResult)) {
                    ?>
                            <tr class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap dark:text-white">
                                    <?= $row["LocationID"] ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $row["Name"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["City"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["Address"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["Description"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <h1>No Location Found</h1>
                    <?php } ?>
                </tbody>
            </table>
        </div>


    </section>
</main>

<?php
include __DIR__ . "/../includes/footer.php";
?>