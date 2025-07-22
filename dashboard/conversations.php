<?php
$pageTitle = "Manage Conversations Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");
$searchPerformed = false;  // Renamed flag variable
$searchTerm = "";          // Separate variable for actual search term

if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);

    if (!empty($searchTerm)) {
        $searchPerformed = true;

        $stmt = mysqli_prepare($conn, "SELECT * FROM conversations WHERE name LIKE CONCAT('%', ?, '%')");

        // Bind the ACTUAL SEARCH TERM 4 times
        mysqli_stmt_bind_param($stmt, "s", $searchTerm);

        if (mysqli_stmt_execute($stmt)) {
            $conversationsResult = mysqli_stmt_get_result($stmt);
        } else {
            // Add error handling
            die("Query failed: " . mysqli_error($conn));
        }
    }
}

if (!$searchPerformed) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM conversations");
    if (mysqli_stmt_execute($stmt)) {
        $conversationsResult = mysqli_stmt_get_result($stmt);
    }
}

?>
<main class="grid grid-cols-4">
    <?php include("aside.php") ?>

    <section class=" col-span-3 px-5 py-3">
        <section class="flex flex-col items-center justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out bg-hero-pattern-conversations  bg-cover bg-no-repeat bg-center h-[517px]">
            <form action="" method="get" class="relative">
                <input type="search" name="search" id="" placeholder="<?php echo $lang['search_placeholder'] ?>" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>" class="text-lg md:text-xl outline-none border-none text-black w-[285px] h-[41px] lg:w-[482px] lg:h-[70px] rounded-full px-2">
                <input type="submit" value="<?php echo $lang['search_placeholder'] ?>" class=" absolute <?= $is_rtl ? 'left-0' : 'right-0' ?>  cursor-pointer  px-1 lg:px-3 h-[41px] lg:h-[70px] bg-primary rounded-full text-[18px] lg:text-[20px] font-semibold ">
            </form>
        </section>
        <section class="relative overflow-x-auto shadow-md sm:rounded-lg opacity-0 translate-y-20 transition-all duration-1000 ease-out text-center">
            <a href="add_con.php"><button class=" w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-green-500 mt-5 rounded-[10px] text-[24px] font-semibold mb-4">Add A New Conversation</button></a>
            <h1 class="text-4xl text-center font-bold my-5 underline">Manage Conversations</h1>
            <table class="w-full text-sm text-left rtl:text-right  ">
                <thead class="text-xs  uppercase border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Photo_URL
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Phrases Number
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($conversationsResult) > 0) {
                        while ($row = mysqli_fetch_assoc($conversationsResult)) {
                    ?>
                            <tr
                                onclick="window.location.href='con_ans.php?id=<?= $row['id'] ?>';"
                                class="cursor-pointer hover:bg-primary transition duration-300 border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 text-white">

                                <th scope="row" class="px-6 py-4 font-medium  whitespace-nowrap ">
                                    <?= $row["id"] ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $row["name"] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $row["photo_url"] ?>
                                </td>
                                <?php
                                $answersStmt = mysqli_prepare($conn, "SELECT * FROM answers WHERE conversation_id = ?");
                                mysqli_stmt_bind_param($answersStmt, "i", $row["id"]);
                                if (mysqli_stmt_execute($answersStmt)) {
                                    $answersResult = mysqli_stmt_get_result($answersStmt);
                                    $rowsNum = mysqli_num_rows($answersResult);
                                }
                                ?>
                                <td class="px-6 py-4">
                                    <?= $rowsNum; ?>
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <a href="edit_con.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                                    <a href="delete_con.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')" class="text-red-500 hover:underline">Delete</a>
                                </td>

                            </tr>
                        <?php }
                    } else { ?>
                        <h1>No Conversations</h1>
                    <?php } ?>
                </tbody>
            </table>
        </section>


    </section>
</main>

<?php
include __DIR__ . "/../includes/footer.php";
?>