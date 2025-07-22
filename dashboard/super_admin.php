<?php
    $pageTitle = "Dashboard Page";
    include __DIR__ . '/../includes/header.php';
    require("../includes/connect_db.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            (isset($_POST["Name"]) && !empty($_POST["Name"])) &&
            (isset($_POST["email"]) && !empty($_POST["email"])) &&
            (isset($_POST["phone"]) && !empty($_POST["phone"]))
        ) {
            $Name = $_POST["Name"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $customerId = $_SESSION["CustomerID"]; // Assuming CustomerID is stored in session

            // Handle file upload only if a file was uploaded without errors
            $photoUpdated = false;
            $photo = null;
            if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
                $photo = $_FILES["photo"]["name"];
                $photoTmp = $_FILES["photo"]["tmp_name"];
                $extension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
                $allowedExtensions = ["jpg", "png", "jpeg", "svg"];
                
                if (!in_array($extension, $allowedExtensions)) {
                    echo "Image extension not allowed";
                    exit;
                }

                // Generate unique filename to prevent conflicts
                $newFilename = uniqid() . '.' . $extension;
                $photoPath = "../media/profile_photo_url/" . $newFilename;

                if (move_uploaded_file($photoTmp, $photoPath)) {
                    $photo = $newFilename; // Use the new filename for the database
                    $photoUpdated = true;
                } else {
                    echo "Error moving file";
                    exit;
                }
            }

            // Prepare SQL query based on whether photo is updated
            if ($photoUpdated) {
                $sql = "UPDATE customer SET Name = ?, Email = ?, Phone = ?, ProfilePhoto = ? WHERE CustomerID = ?";
            } else {
                $sql = "UPDATE customer SET Name = ?, Email = ?, Phone = ? WHERE CustomerID = ?";
            }

            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                echo "SQL error: " . mysqli_error($conn);
                exit;
            }

            // Bind parameters
            if ($photoUpdated) {
                mysqli_stmt_bind_param($stmt, "ssssi", $Name, $email, $phone, $photo, $customerId);
            } else {
                mysqli_stmt_bind_param($stmt, "sssi", $Name, $email, $phone, $customerId);
            }

            if (mysqli_stmt_execute($stmt)) {
                // Update session variables
                $_SESSION["CustomerName"] = $Name;
                $_SESSION["CustomerEmail"] = $email;
                $_SESSION["CustomerPhone"] = $phone;
                if ($photoUpdated) {
                    $_SESSION["CustomerProfilePhoto"] = $photo;
                }
                
                header("Location: http://localhost/tourism%20agency/dashboard/super_admin.php");
                exit;
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "All fields are required";
        }
    }
    $stmt = mysqli_prepare($conn, "SELECT CustomerID FROM customer");
    mysqli_stmt_execute($stmt);
    $customerResult = mysqli_stmt_get_result($stmt);
    $customerNuwRows = mysqli_num_rows($customerResult);

    $stmt = mysqli_prepare($conn, "SELECT ReservationID FROM reservation");
    mysqli_stmt_execute($stmt);
    $reservationResult = mysqli_stmt_get_result($stmt);
    $reservationNuwRows = mysqli_num_rows($reservationResult);

    $stmt = mysqli_prepare($conn, "SELECT AgencyID FROM agency");
    mysqli_stmt_execute($stmt);
    $agencyResult = mysqli_stmt_get_result($stmt);
    $agencyNuwRows = mysqli_num_rows($agencyResult);

    $stmt = mysqli_prepare($conn, "SELECT LocationID FROM location");
    mysqli_stmt_execute($stmt);
    $locationResult = mysqli_stmt_get_result($stmt);
    $locationNuwRows = mysqli_num_rows($locationResult);

    $stmt = mysqli_prepare($conn, "SELECT CircuitID FROM circuit");
    mysqli_stmt_execute($stmt);
    $circuitResult = mysqli_stmt_get_result($stmt);
    $circuitNuwRows = mysqli_num_rows($circuitResult);

    $stmt = mysqli_prepare($conn, "SELECT id FROM conversations");
    mysqli_stmt_execute($stmt);
    $conversationsResult = mysqli_stmt_get_result($stmt);
    $conversationsNuwRows = mysqli_num_rows($conversationsResult);
?>
<main class="grid grid-cols-4">
    <?php include("aside.php") ?>
    <section class=" col-span-3 px-5 py-3 opacity-0 translate-y-20 transition-all duration-1000 ease-out">
        <h1 class="text-center text-4xl font-bold my-5">Welcome <?= $_SESSION["CustomerName"]  ?></h1>
        <div>
            <form action="" method="POST" class="flex flex-col items-center gap-5 lg:flex-row lg:items-center lg:justify-center" enctype="multipart/form-data">
                <div class="text-center   ">
                    <?php if($_SESSION["CustomerProfilePhoto"] != NULL){ ?>
                        <img src="../media/profile_photo_url/<?= $_SESSION["CustomerProfilePhoto"]  ?>" alt="" class="w-[500px] h-[500px] object-cover rounded-full">
                    <?php }else{ ?>
                        <img src="../media/profile_photo_url/default.svg" alt="" class="w-[500px] h-[500px] object-cover rounded-full">
                    <?php } ?>
                    <input type="file" name="photo" value="<?=htmlspecialchars($_SESSION["CustomerProfilePhoto"] ?? 'default.svg') ?>" class="bg-gray-100 bg-opacity-30 my-1" id="" placeholder="Change">
                </div>
                <div class="flex flex-col px-5">
                    <label for="Name" class="text-lg font-semibold">Name:</label>
                    <input type="text" name="Name" value="<?= htmlspecialchars($_SESSION["CustomerName"])?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] bg-transparent border border-1 lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 " id="">
                    <label for="email" class="text-lg font-semibold">Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_SESSION["CustomerEmail"])  ?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] bg-transparent border border-1 rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 " id="">
                    <label for="Phone Number" class="text-lg font-semibold">Phone Number:</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_SESSION["CustomerPhone"])  ?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] bg-transparent border border-1 lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 " id="">
                    <button type="submit" class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">Save Changes</button>
                </div>
            </form>
        </div>
        
        <h1 class="text-center text-4xl font-bold my-5">Statistics</h1>
        <section class="opacity-0 translate-y-20 transition-all duration-1000 ease-out flex justify-center ">
            <div class="grid grid-cols-4 text-center gap-10">
                <a href="http://localhost/tourism%20agency/dashboard/customers.php"><div class="w-44 h-20 flex flex-col justify-center items-center text-3xl bg-green-500 rounded-lg"> <span>Customers</span><?= $customerNuwRows ?></div></a>
                <a href="http://localhost/tourism%20agency/dashboard/reservations.php"><div class="w-44 h-20 flex flex-col justify-center items-center text-3xl bg-primary rounded-lg"> <span>Reservations</span><?= $reservationNuwRows ?></div></a>
                <a href="http://localhost/tourism%20agency/dashboard/agencies.php"><div class="w-44 h-20 flex flex-col justify-center items-center text-3xl bg-red-500 rounded-lg"> <span>Agencies</span><?= $agencyNuwRows ?></div></a>
                <a href="http://localhost/tourism%20agency/dashboard/locations.php"><div class="w-44 h-20 flex flex-col justify-center items-center text-3xl bg-blue-500 rounded-lg"> <span>Locations</span><?= $locationNuwRows ?></div></a>
                <a href="http://localhost/tourism%20agency/dashboard/circuits.php"><div class="w-44 h-20 flex flex-col justify-center items-center text-3xl bg-purple-500 rounded-lg"> <span>Circuits</span><?= $circuitNuwRows ?></div></a>
                <a href="http://localhost/tourism%20agency/dashboard/conversations.php"><div class=" w-48 h-20 flex flex-col justify-center items-center text-3xl bg-orange-500 rounded-lg"> <span>Conversations</span><?= $conversationsNuwRows ?></div></a>
            </div>
        </section>
    </section>
</main>

<?php 
    include __DIR__ . "/../includes/footer.php";
?>