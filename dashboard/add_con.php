<?php
$pageTitle = "My Reservations Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");

// Initialize error array
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize inputs
    $name = trim($_POST["name"] ?? '');


    // Basic validation
    if (empty($name)) $errors[] = "name is required";

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
        $photoPath = "../media/conversations/" . $newFilename;

        if (move_uploaded_file($photoTmp, $photoPath)) {
            $photo = $newFilename; // Use the new filename for the database
            $photoUpdated = true;
        } else {
            echo "Error moving file";
            exit;
        }
    }


    // Check if email already exists
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM conversations WHERE name = ?");
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
                "INSERT INTO conversations (name, photo_url) 
                VALUES (?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "ss", $name, $photo);

            if (mysqli_stmt_execute($stmt)) {

                mysqli_commit($conn);
                // Redirect with success message
                $_SESSION["success"] = "Registration successful!";
                header("Location: http://localhost/tourism%20agency/dashboard/conversations.php");
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
        <h1 class="text-4xl text-center font-bold my-5 underline">Add A New Conversation</h1>
        <section>
            <form action="" method="POST" class="flex flex-col items-center" enctype="multipart/form-data">
                <!-- name -->
                <input type="text" name="name" id="name"
                    value="<?= htmlspecialchars($name ?? '') ?>"
                    placeholder="Name"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">

                <input type="file" name="photo" class="bg-gray-100 bg-opacity-30 my-1" id="" placeholder="Add">

                <button type="submit" name="submit"
                    class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">
                    ADD
                </button>
            </form>
        </section>
    </section>
</main>

<?php
include __DIR__ . "/../includes/footer.php";
?>