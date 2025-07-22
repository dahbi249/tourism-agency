<?php
$pageTitle = "Conversations Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");
$id = $_GET["id"];
$stmt = mysqli_prepare($conn, "SELECT * FROM conversations WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    $conversationsResult = mysqli_stmt_get_result($stmt);
    $conversationsRows = mysqli_fetch_assoc($conversationsResult);
}
$stmt = mysqli_prepare($conn, "SELECT * FROM answers WHERE conversation_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $answersResult = mysqli_stmt_get_result($stmt);
}
?>
<section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <img src="../media/conversations/<?= $conversationsRows["photo_url"] ?? 'default.jpg'; ?>" alt="" class="mt-2">
</section>
<div class="flex text-right items-center gap-20 px-10 lg:px-96 my-4" dir="rtl">
    <h1 class="text-right text-5xl md:text-6xl lg:text-9xl font-bold mb-10"><?= $conversationsRows["name"]; ?></h1>
    <button onclick="translateText(this, '<?= $conversationsRows['name']; ?>')" class="text-blue-600 underline">🌍 ترجمة</button>
    <p class="translated-text mt-2 hidden"></p>
</div>
<?php while ($answersRows = mysqli_fetch_assoc($answersResult)) { ?>
    <div class="flex text-right items-center justify-between px-10 lg:px-96 my-4" dir="rtl">
        <h3 class="font-bold text-2xl text-right"><?= $answersRows["answer"]; ?></h3>

        <!-- Hidden audio -->
        <audio id="audio<?= $answersRows['id']; ?>" src="../media/conversations/audio/<?= $answersRows["audio_url"] ?? 'default.mp3'; ?>"></audio>

        <button onclick="translateText(this, '<?= htmlspecialchars($answersRows['answer']); ?>')" class="text-blue-600 underline">🌍 ترجمة</button>
        <p class="translated-text mt-2 hidden"></p>
        <!-- Clickable voice icon -->
        <img src="../media/conversations/Voice.png" alt="Play audio"
            class="w-8 h-8 cursor-pointer"
            onclick="document.getElementById('audio<?= $answersRows['id']; ?>').play();">
    </div>
<?php } ?>


<button class="text-white mx-auto  w-[250px] h-[53.84px] lg:w-[362px] lgh-[73px]  bg-primary rounded-full lg:text-[30px] text-[24px] font-semibold my-10">
    <a href="quiz_start.php?id=<?= $id ?>"><?php echo $lang["Test your language"] ?></a>
</button>


<script>
    function translateText(button, text) {
        const p = button.nextElementSibling;
        p.textContent = "جارٍ الترجمة...";
        p.classList.remove('hidden');

        fetch('https://api.mymemory.translated.net/get?q=' + encodeURIComponent(text) + '&langpair=ar|<?= $current_lang ?>')
            .then(response => response.json())
            .then(data => {
                const translation = data.responseData.translatedText;
                p.textContent = translation;
            })
            .catch(() => {
                p.textContent = "حدث خطأ أثناء الترجمة";
            });
    }
</script>

<?php
include __DIR__ . "/../includes/footer.php";
?>