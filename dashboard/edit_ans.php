<?php
require("../includes/connect_db.php");

$answer_id = $_GET["id"] ?? null;
$errors = [];
$answer = '';
$audio_url = '';

if (!$answer_id || !is_numeric($answer_id)) {
    die("Invalid answer ID");
}

// Step 1: Fetch current data
$stmt = mysqli_prepare($conn, "SELECT conversation_id, answer, audio_url  FROM answers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $answer_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $conversation_id ,$answer, $audio_url);
if (!mysqli_stmt_fetch($stmt)) {
    die("Answer not found.");
}
mysqli_stmt_close($stmt);

// Step 2: Handle update form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_answer = trim($_POST["answer"] ?? '');
    $audio_updated = false;

    if (empty($new_answer)) {
        $errors[] = "Answer is required.";
    }

    // Handle optional new audio upload
    if (isset($_FILES["audio"]) && $_FILES["audio"]["error"] === UPLOAD_ERR_OK) {
        $audio = $_FILES["audio"]["name"];
        $audioTmp = $_FILES["audio"]["tmp_name"];
        $extension = strtolower(pathinfo($audio, PATHINFO_EXTENSION));
        $allowedExtensions = ["mp3", "m4a"];

        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = "Audio extension not allowed.";
        } else {
            $newFilename = uniqid() . '.' . $extension;
            $audioPath = "../media/conversations/audio/" . $newFilename;

            if (move_uploaded_file($audioTmp, $audioPath)) {
                $audio_url = $newFilename;
                $audio_updated = true;
            } else {
                $errors[] = "Error uploading audio file.";
            }
        }
    }

    // Update in database
    if (empty($errors)) {
        if ($audio_updated) {
            $stmt = mysqli_prepare($conn, "UPDATE answers SET answer = ?, audio_url = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $new_answer, $audio_url, $answer_id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE answers SET answer = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_answer, $answer_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["success"] = "Answer updated successfully!";
            header("Location: http://localhost/tourism%20agency/dashboard/con_ans.php?id=" . urlencode($conversation_id));
            exit;
        } else {
            $errors[] = "Database update failed.";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!-- HTML Form -->
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="grid grid-cols-4">
    <?php include("aside.php") ?>
    <section class="col-span-3 px-5 py-3">
        <h1 class="text-4xl text-center font-bold my-5 underline">Edit Answer</h1>

        <?php if (!empty($errors)): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="flex flex-col items-center">
            <input type="text" name="answer" value="<?= htmlspecialchars($answer) ?>"
                class="w-[300px] px-1 h-[55px] md:w-[390px] lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-6 text-gray-900"
                placeholder="Answer">

            <?php if ($audio_url): ?>
                <p class="text-sm text-gray-600 mb-2">Current audio: <a href="../media/conversations/audio/<?= htmlspecialchars($audio_url) ?>" target="_blank" class="text-blue-600 underline"><?= htmlspecialchars($audio_url) ?></a></p>
            <?php endif; ?>

            <input type="file" name="audio" class="bg-gray-100 bg-opacity-30 mb-6">

            <button type="submit" class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">
                Update
            </button>
        </form>
    </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
