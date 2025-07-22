<?php
$pageTitle = "My Reservations Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");

$conID = $_GET["id"];
// Initialize error array
$errors = [];


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize inputs
    $answer = trim($_POST["answer"] ?? '');


    // Basic validation
    if (empty($answer)) $errors[] = "answer is required";

    // Handle file upload only if a file was uploaded without errors
    $audioUpdated = false;
    $audio = null;
    if (isset($_FILES["audio"]) && $_FILES["audio"]["error"] === UPLOAD_ERR_OK) {
        $audio = $_FILES["audio"]["name"];
        $audioTmp = $_FILES["audio"]["tmp_name"];
        $extension = strtolower(pathinfo($audio, PATHINFO_EXTENSION));
        $allowedExtensions = ["mp3", "m4a"];

        if (!in_array($extension, $allowedExtensions)) {
            echo "Audio file extension not allowed";
            exit;
        }

        // Generate unique filename to prevent conflicts
        $newFilename = uniqid() . '.' . $extension;
        $audioPath = "../media/conversations/audio/" . $newFilename;

        if (move_uploaded_file($audioTmp, $audioPath)) {
            $audio = $newFilename; // Use the new filename for the database
            $audioUpdated = true;
        } else {
            echo "Error moving file";
            exit;
        }
    }


    // Check if answer already exists
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM answers WHERE answer = ? AND conversation_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $answer, $conID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Answer already exists";
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
                "INSERT INTO answers (conversation_id, answer, audio_url) 
                VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "iss", $conID ,$answer, $audio);

            if (mysqli_stmt_execute($stmt)) {

                mysqli_commit($conn);
                // Redirect with success message
                $_SESSION["success"] = "Registration successful!";
                $redirect = $_SESSION["con_id"];
                header("Location: http://localhost/tourism%20agency/dashboard/con_ans.php?id=$conID");
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
        <h1 class="text-4xl text-center font-bold my-5 underline">Add A New Answer</h1>
        <section>
            <form action="" method="POST" class="flex flex-col items-center" enctype="multipart/form-data">
                <!-- name -->
                <input type="text" name="answer" id="answer"
                    value="<?= htmlspecialchars($answer ?? '') ?>"
                    placeholder="Answer"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 text-gray-900">

                <input type="file" name="audio" class="bg-gray-100 bg-opacity-30 my-1" id="" placeholder="Add">

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