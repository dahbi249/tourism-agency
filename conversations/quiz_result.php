<?php
$pageTitle = "Quiz Result Page";
include __DIR__ . '/../includes/header.php';
$score = $_SESSION['score'] ?? 0;
session_destroy(); // reset quiz
?>

<div class="text-center py-20">
    <h1 class="text-4xl font-bold mb-4">🎉 انتهى الاختبار</h1>
    <p class="text-xl">لقد حصلت على <?= $score ?> من 10</p>
    <a href="quiz_start.php?id=1" class="mt-4 inline-block bg-primary text-white py-2 px-6 rounded">أعد المحاولة</a>
</div>


<?php
include __DIR__ . "/../includes/footer.php";
?>