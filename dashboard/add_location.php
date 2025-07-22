<?php
$pageTitle = "My Reservations Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");

$stmt = mysqli_prepare($conn, "SELECT * FROM customer WHERE Role = 'customer'");
if (mysqli_stmt_execute($stmt)) {
    $customersResult = mysqli_stmt_get_result($stmt);
}




// Initialize error array
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize inputs
    $name = trim($_POST["name"] ?? '');
    $City = trim($_POST["City"] ?? '');
    $address = trim($_POST["address"] ?? '');
    $description = trim($_POST["description"] ?? '');


    // Basic validation
    if (empty($name)) $errors[] = "name is required";
    if (empty($address)) $errors[] = "address is required";
    if (empty($City)) $errors[] = "City is required";
    if (empty($description)) $errors[] = "description is required";



    // Check if email already exists
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT LocationID FROM location WHERE Name = ?");
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Location already registered";
        }
        mysqli_stmt_close($stmt);
    }

    // If no errors, proceed with registration
    if (empty($errors)) {


        // Using transactions for safer database operations
        mysqli_begin_transaction($conn);

        try {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO location (Name, City, Address,  Description) 
                VALUES (?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "ssss", $name, $City, $address, $description);

            if (mysqli_stmt_execute($stmt)) {

                mysqli_commit($conn);
                // Redirect with success message
                $_SESSION["success"] = "Registration successful!";
                header("Location: http://localhost/tourism%20agency/dashboard/locations.php");
                exit();
            } else {
                throw new Exception("Database error: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = "Registration failed. Please try again.";
        } finally {
            mysqli_stmt_close($stmt);
        }
    }
}
mysqli_close($conn);
?>
<main class="grid grid-cols-4">
    <?php include("aside.php") ?>
    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <section class=" col-span-3 px-5 py-3">
        <h1 class="text-4xl text-center font-bold my-5 underline">Add A New location</h1>
        <section>
            <form action="" method="POST" class="flex flex-col items-center">
                <!-- Username -->
                <input type="text" name="name" id="username"
                    value="<?= htmlspecialchars($name ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Username"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">


                <!-- City -->
                <input type="text" name="City" id="username"
                    value="<?= htmlspecialchars($City ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Username"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">
                <!-- Address -->
                <input type="text" name="address" id="address"
                    value="<?= htmlspecialchars($address ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Username"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">
                <!-- description -->

                <textarea name="description" id="Description" value="<?= htmlspecialchars($description ?? '') ?>" placeholder="Description" class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900"></textarea>

                <button type="submit" name="submit"
                    class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">
                    <?= htmlspecialchars($lang["Register"]) ?>
                </button>
            </form>
        </section>
    </section>
</main>

<?php
include __DIR__ . "/../includes/footer.php";
?>