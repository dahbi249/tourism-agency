<?php
    $pageTitle = "About Us Page";
    include __DIR__ . '/../includes/header.php';
    require("../includes/connect_db.php");

?>
<main class="">
    <section class="px-5 py-10 flex justify-center gap-60">
        <div><img src="../assets/LOGO-orange.png" alt=""></div>
        <div class="flex flex-col gap-10">
            <h1 class="text-4xl text-primary font-bold">JAWLA</h1>
            <p class="text-2xl w-96"> A modern, interactive tourism platform designed to showcase Algeria’s cultural heritage and landscapes.</p>
            <p class="text-2xl w-96"><span class="text-xl text-primary font-semibold">Our Goal: </span>Simplify travel planning by offering centralized access to curated circuits, secure bookings, and real-time updates.</p>
            <p class="text-2xl w-96"><span class="text-xl text-primary font-semibold">Our Vision: </span>Position Algeria as a top-tier tourism destination through technology and cultural promotion.</p>
        </div>
    </section>
</main>

<?php 
    include __DIR__ . "/../includes/footer.php";
?>