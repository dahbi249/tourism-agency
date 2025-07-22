<?php
session_start();
require("../includes/connect_db.php");

$id = $_GET['id'] ?? null;
$errors = [];
$name = '';
$photo_url = '';

if (!$id || !is_numeric($id)) die("Invalid conversation ID");

// Fetch data
$stmt = mysqli_prepare($conn, "SELECT name, photo_url FROM conversations WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $name, $photo_url);
if (!mysqli_stmt_fetch($stmt)) die("Conversation not found.");
mysqli_stmt_close($stmt);

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_name = trim($_POST["name"] ?? '');

    if (empty($new_name)) $errors[] = "Name is required.";

    // Image upload
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES["photo"]["tmp_name"];
        $extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "webp"];
        if (!in_array($extension, $allowed)) {
            $errors[] = "Invalid image format.";
        } else {
            $newFile = uniqid() . "." . $extension;
            $uploadPath = "../media/conversations/" . $newFile;
            if (move_uploaded_file($imageTmp, $uploadPath)) {
                $photo_url = $newFile;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "UPDATE conversations SET name = ?, photo_url = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $new_name, $photo_url, $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["success"] = "Conversation updated.";
            header("Location: conversations.php");
            exit;
        } else {
            $errors[] = "Update failed.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="grid grid-cols-4">
    <?php include("aside.php") ?>
    <section class="col-span-3 px-5 py-3">
        <h1 class="text-4xl text-center font-bold my-5 underline">Edit Conversation</h1>

        <?php if (!empty($errors)): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="flex flex-col items-center">
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="mb-4 w-80 px-3 h-12 rounded-md text-black" placeholder="Name">
            <?php if ($photo_url): ?>
                <p>Current Photo: <a href="../media/conversations/<?= $photo_url ?>" target="_blank" class="underline text-blue-600"><?= $photo_url ?></a></p>
            <?php endif; ?>
            <input type="file" name="photo" class="mb-4">
            <button class="bg-primary text-white px-6 py-2 rounded-md">Update</button>
        </form>
    </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
