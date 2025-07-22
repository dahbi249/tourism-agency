<?php
$pageTitle = "Quiz Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");

// Process previous answer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['answer']);
    $correct = trim($_POST['correct']);
    if ($user === $correct) {
        $_SESSION['score']++;
    }
    $_SESSION['current_q']++;
}

// End of quiz
if ($_SESSION['current_q'] >= 10) {
    header("Location: quiz_result.php");
    exit;
}

// Get next question
$question = $_SESSION['quiz'][$_SESSION['current_q']];

// Create 3 wrong options
$wrongSql = "SELECT * FROM answers WHERE id != ? ORDER BY RAND() LIMIT 3";
$stmt = mysqli_prepare($conn, $wrongSql);
mysqli_stmt_bind_param($stmt, "i", $question['id']);
mysqli_stmt_execute($stmt);
$wrongResult = mysqli_stmt_get_result($stmt);
$wrong = mysqli_fetch_all($wrongResult, MYSQLI_ASSOC);

$options = $wrong;
$options[] = $question;
shuffle($options);

// Randomly select type: text → audio | audio → text
$mode = rand(0, 1) === 0 ? 'audio-to-text' : 'text-to-audio';
?>

<div class="text-center p-6">
    <h1 class="text-3xl font-bold mb-6">السؤال <?= $_SESSION['current_q'] + 1 ?> من 10</h1>

    <?php if ($mode === 'audio-to-text'): ?>
        <p class="text-xl">ما معنى هذا الصوت؟</p>
        <audio controls class="mx-auto my-4">
            <source src="../media/conversations/audio/<?= $question['audio_url']; ?>" type="audio/mp3">
        </audio>
    <?php else: ?>
        <p class="text-xl font-bold my-4">🗣️ <?= $question['answer']; ?></p>
        <p>اختر الصوت الصحيح</p>
    <?php endif; ?>

    <form action="quiz.php" method="POST" class="space-y-4 mt-6">
        <?php foreach ($options as $opt): ?>
            <label class="block border-2 border-primary py-3 px-4 rounded cursor-pointer hover:bg-primary">
                <input type="radio" name="answer" value="<?= htmlspecialchars($mode === 'audio-to-text' ? $opt['answer'] : $opt['audio_url']); ?>" required class="mr-2">

                <?php if ($mode === 'audio-to-text'): ?>
                    <?= htmlspecialchars($opt['answer']); ?>
                <?php else: ?>
                    <audio controls>
                        <source src="../media/conversations/audio/<?= $opt['audio_url']; ?>" type="audio/mp3">
                    </audio>
                <?php endif; ?>
            </label>
        <?php endforeach; ?>

        <input type="hidden" name="correct" value="<?= htmlspecialchars($mode === 'audio-to-text' ? $question['answer'] : $question['audio_url']); ?>">
        <button type="submit" class="bg-primary text-white py-2 px-6 rounded hover:bg-primary mt-4"><?php echo $lang["next"] ?></button>
    </form>
</div>




<?php
include __DIR__ . "/../includes/footer.php";
?>