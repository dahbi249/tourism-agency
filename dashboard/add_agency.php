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
    $description = trim($_POST["description"] ?? '');
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST["phone"] ?? '');
    $address = trim($_POST["address"] ?? '');
    $status = trim($_POST["status"] ?? '');
    $admin = trim($_POST["admin"] ?? '');


    // Basic validation
    if (empty($name)) $errors[] = "name is required";
    if (empty($description)) $errors[] = "description is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone) || !preg_match("/^\+?[0-9\s\-]+$/", $phone)) $errors[] = "Valid phone number is required";
    if (empty($address)) $errors[] = "address is required";
    if (empty($status)) $errors[] = "status is required";
    if (empty($admin)) $errors[] = "admin is required";



    // Check if email already exists
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT AgencyID FROM agency WHERE Email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Email already registered";
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
                "INSERT INTO agency (Name, Description, Email, Phone, Address, Status) 
                VALUES (?, ?, ?, ? , ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "ssssss", $name, $description, $email, $phone, $address, $status);

            if (mysqli_stmt_execute($stmt)) {
                $agencyID = mysqli_insert_id($conn);
                $stmt = mysqli_prepare($conn, "UPDATE customer SET AgencyID = ?, Role = 'agency_admin' WHERE CustomerID = ?");
                mysqli_stmt_bind_param($stmt, "ii", $agencyID, $admin);
                mysqli_stmt_execute($stmt);
                
                mysqli_commit($conn);
                // Redirect with success message
                $_SESSION["success"] = "Registration successful!";
                header("Location: http://localhost/tourism%20agency/dashboard/agencies.php");
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
        <h1 class="text-4xl text-center font-bold my-5 underline">Add A New Agency</h1>
        <section>
            <form action="" method="POST" class="flex flex-col items-center">
                <!-- Username -->
                <input type="text" name="name" id="username"
                    value="<?= htmlspecialchars($name ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Username"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">

                <textarea name="description" id="Description"  value="<?= htmlspecialchars($description ?? '') ?>" placeholder="Description" class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900"></textarea>
                <!-- Email -->
                <input type="email" name="email" id="email"
                    value="<?= htmlspecialchars($email ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Email"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">

                <!-- Phone -->
                <input type="text" name="phone" id="phone"
                    value="<?= htmlspecialchars($phone ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Phone"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">

                <!-- Address -->
                <input type="text" name="address" id="address"
                    value="<?= htmlspecialchars($address ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Username"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">
                <!-- Status -->
                <input type="text" name="status" id="username"
                    value="<?= htmlspecialchars($status ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Username"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">
                <!-- assign admin -->
                 <select name="admin" id="" 
                 class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">
                 <option value="<?= $row["CustomerID"] ?>">assign an admin</option>
                 <?php if (mysqli_num_rows($customersResult) > 0) {
                        while ($row = mysqli_fetch_assoc($customersResult)) {
                    ?>
                    <option value="<?= $row["CustomerID"] ?>"><?= $row["CustomerID"] ?> - <?= $row["Name"] ?></option>
                    <?php }
                    } else { ?>
                        <h1>No customers</h1>
                    <?php } ?>
                 </select>
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