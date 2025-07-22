<?php
    $pageTitle = "Payment Success Page";
    include __DIR__ . '/../includes/header.php';
    require("../includes/connect_db.php");
    
    ?>
<h1 class=" text-center text-4xl">Thank you for contacting JAWLA</h1>
<h1 class=" text-center text-2xl">Our team will review your message and respond as soon as possible</h1>
<section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <img src="../assets/undraw_order-confirmed_m9e9.svg" alt="">
</section>

<?php 
    include __DIR__ . "/../includes/footer.php";
?>