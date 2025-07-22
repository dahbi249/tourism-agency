<?php
    $pageTitle = "Profile Page";
    include __DIR__ . '/../includes/header.php';
    require("../includes/connect_db.php");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            (isset($_POST["Name"]) && !empty($_POST["Name"])) &&
            (isset($_POST["phone"]) && !empty($_POST["phone"]))
        ) {
            $Name = $_POST["Name"];
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
                $sql = "UPDATE customer SET Name = ?, Phone = ?, ProfilePhoto = ? WHERE CustomerID = ?";
            } else {
                $sql = "UPDATE customer SET Name = ?, Phone = ? WHERE CustomerID = ?";
            }

            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                echo "SQL error: " . mysqli_error($conn);
                exit;
            }

            // Bind parameters
            if ($photoUpdated) {
                mysqli_stmt_bind_param($stmt, "sssi", $Name, $phone, $photo, $customerId);
            } else {
                mysqli_stmt_bind_param($stmt, "ssi", $Name, $phone, $customerId);
            }

            if (mysqli_stmt_execute($stmt)) {
                // Update session variables
                $_SESSION["CustomerName"] = $Name;
                $_SESSION["CustomerPhone"] = $phone;
                if ($photoUpdated) {
                    $_SESSION["CustomerProfilePhoto"] = $photo;
                }
                
                header("Location: http://localhost/tourism%20agency/main/userprofile.php");
                exit;
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "All fields are required";
        }
    }
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
                    <input type="text" name="Name" value="<?= htmlspecialchars($_SESSION["CustomerName"])?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] bg-transparent border border-1 lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-white" id="">
                    <label for="email" class="text-lg font-semibold">Email:</label>
                    <input type="email" name="email" readonly  value="<?= htmlspecialchars($_SESSION["CustomerEmail"])  ?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] bg-transparent border border-1 rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-white" id="">
                    <label for="Phone Number" class="text-lg font-semibold">Phone Number:</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_SESSION["CustomerPhone"] ?? 'You have no phone number')  ?>"  class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] bg-transparent border border-1 lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-white" id="">
                    <button type="submit" class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">Save Changes</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php 
    include __DIR__ . "/../includes/footer.php";
?>