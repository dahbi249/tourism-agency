<?php
    $pageTitle = "Dashboard Page";
    include __DIR__ . '/../includes/header.php';
    require("../includes/connect_db.php");

    $stmt = mysqli_prepare($conn, "SELECT AgencyID FROM customer WHERE CustomerID = ?");
    mysqli_stmt_bind_param($stmt, "i",  $_SESSION["CustomerID"]);
    mysqli_stmt_execute($stmt);
    $agencyResult = mysqli_stmt_get_result($stmt);
    $agencyRows = mysqli_fetch_assoc($agencyResult);
    $_SESSION["AgencyID"] = $agencyRows["AgencyID"];

    
    $stmt = mysqli_prepare($conn, "SELECT * FROM agency WHERE AgencyID = ?");
    mysqli_stmt_bind_param($stmt, "i",  $agencyRows["AgencyID"]);
    mysqli_stmt_execute($stmt);
    $agencyResult = mysqli_stmt_get_result($stmt);
    $agencyInfo = mysqli_fetch_assoc($agencyResult);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            (isset($_POST["Name"]) && !empty($_POST["Name"])) &&
            (isset($_POST["Description"]) && !empty($_POST["Description"])) &&
            (isset($_POST["email"]) && !empty($_POST["email"])) &&
            (isset($_POST["phone"]) && !empty($_POST["phone"])) &&
            (isset($_POST["Address"]) && !empty($_POST["Address"]))
        ) {
            $Name = $_POST["Name"];
            $Description = $_POST["Description"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $Address = $_POST["Address"];

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
                $photoPath = "../media/agencies/" . $newFilename;

                if (move_uploaded_file($photoTmp, $photoPath)) {
                    $photo = $newFilename; // Use the new filename for the database
                    $photoUpdated = true;
                    if ($photoUpdated) {
                        $_SESSION["AgencyPhoto"] = $photo;
                    }
                } else {
                    echo "Error moving file";
                    exit;
                }
            }

            // Prepare SQL query based on whether photo is updated
            if ($photoUpdated) {
                $mediaStmt = mysqli_prepare($conn,"UPDATE media SET URL = ? WHERE EntityType = 'agency' AND EntityID = ? AND Type = 'photo'");
                mysqli_stmt_bind_param($mediaStmt, "si", $photo, $_SESSION["AgencyID"]);
                mysqli_stmt_execute($mediaStmt);
                $_SESSION["AgencyPhoto"] = $photo;
            }
            $sql = "UPDATE agency SET Name = ?,Description = ?, Email = ?, Phone = ?, Address = ? WHERE AgencyID = ?";
            

            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                echo "SQL error: " . mysqli_error($conn);
                exit;
            }

            mysqli_stmt_bind_param($stmt, "sssssi", $Name,$Description, $email, $phone,$Address, $_SESSION["AgencyID"]);

            if (mysqli_stmt_execute($stmt)) {
              
                
                header("Location: http://localhost/tourism%20agency/agency_dashboard/agency_info.php");
                exit;
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "All fields are required";
        }
    }


    $stmt = mysqli_prepare($conn, "SELECT ReservationID FROM reservation WHERE AgencyID = ?");
    mysqli_stmt_bind_param($stmt, "i",  $agencyRows["AgencyID"]);
    mysqli_stmt_execute($stmt);
    $reservationResult = mysqli_stmt_get_result($stmt);
    $reservationNuwRows = mysqli_num_rows($reservationResult);


    $stmt = mysqli_prepare($conn, "SELECT CircuitID FROM agency_circuit WHERE AgencyID = ?");
    mysqli_stmt_bind_param($stmt, "i",  $agencyRows["AgencyID"]);
    mysqli_stmt_execute($stmt);
    $circuitResult = mysqli_stmt_get_result($stmt);
    $circuitNuwRows = mysqli_num_rows($circuitResult);
?>
<main class="grid grid-cols-4">
    <?php include("aside.php") ?>
    <section class=" col-span-3 px-5 py-3 opacity-0 translate-y-20 transition-all duration-1000 ease-out">
        <h1 class="text-center text-4xl font-bold my-5">Welcome To <?= htmlspecialchars($agencyInfo["Name"])  ?></h1>
        <div>
            <form action="" method="POST" class="flex flex-col items-center gap-5 lg:flex-row lg:items-center lg:justify-center" enctype="multipart/form-data">
                <div class="text-center   ">
                    <?php if($_SESSION["AgencyPhoto"] != NULL){ ?>
                        <img src="../media/agencies/<?= $_SESSION["AgencyPhoto"]  ?>" alt="" class="w-[500px] h-[500px] object-cover rounded-full">
                    <?php }else{ ?>
                        <img src="../media/agencies/default.jpg" alt="" class="w-[500px] h-[500px] object-cover rounded-full">
                    <?php } ?>
                    <input type="file" name="photo" value="<?=htmlspecialchars($_SESSION["AgencyPhoto"] ?? 'default.svg') ?>" class="bg-gray-100 bg-opacity-30 my-1" id="" placeholder="Change">
                </div>
                <div class="flex flex-col px-5">
                    <label for="Name" class="text-lg font-semibold">Name:</label>
                    <input type="text" name="Name" value="<?= htmlspecialchars($agencyInfo["Name"])?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] bg-transparent border border-1 lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 " id="">
                    <label for="Phone Number" class="text-lg font-semibold">Description</label>
                    <input type="text" name="Description" value="<?= htmlspecialchars($agencyInfo["Description"])  ?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] bg-transparent border border-1 lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 " id="">
                    <label for="email" class="text-lg font-semibold">Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($agencyInfo["Email"])  ?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] bg-transparent border border-1 rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 " id="">
                    <label for="Phone Number" class="text-lg font-semibold">Phone Number:</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($agencyInfo["Phone"])  ?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] bg-transparent border border-1 lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 " id="">
                    <label for="Phone Number" class="text-lg font-semibold">Address</label>
                    <input type="text" name="Address" value="<?= htmlspecialchars($agencyInfo["Address"])  ?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] bg-transparent border border-1 lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 " id="">
                    <button type="submit" class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">Save Changes</button>
                </div>
            </form>
        </div>
        
        <h1 class="text-center text-4xl font-bold my-5">Static</h1>
        <section class="opacity-0 translate-y-20 transition-all duration-1000 ease-out flex justify-center ">
            <div class="grid grid-cols-4 text-center gap-10">
                <a href="http://localhost/tourism%20agency/dashboard/reservations.php"><div class=" px-5 h-20 flex flex-col justify-center items-center text-3xl bg-primary rounded-lg"> <span>Reservations</span><?= $reservationNuwRows ?></div></a>
                <a href="http://localhost/tourism%20agency/dashboard/circuits.php"><div class=" px-5 h-20 flex flex-col justify-center items-center text-3xl bg-purple-500 rounded-lg"> <span>Adopted Circuits</span><?= $circuitNuwRows ?></div></a>
                <a href="http://localhost/tourism%20agency/dashboard/circuits.php"><div class=" px-5 h-24 flex flex-col justify-center items-center text-3xl bg-blue-500 rounded-lg"> <span>Contracted Accommodations</span><?= $circuitNuwRows ?></div></a>
            </div>
        </section>
    </section>
</main>


<?php 
    include __DIR__ . "/../includes/footer.php";
?>