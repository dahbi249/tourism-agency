<?php
    $pageTitle = "Conversations Page";
    include __DIR__ . '/../includes/header.php';
    require("../includes/connect_db.php");


    $searchPerformed = false;  // Renamed flag variable
    $searchTerm = "";          // Separate variable for actual search term

    if (isset($_GET['search'])) {
        $searchTerm = trim($_GET['search']);

        if (!empty($searchTerm)) {
            $searchPerformed = true;

            $stmt = mysqli_prepare($conn, "SELECT * FROM conversations WHERE name LIKE CONCAT('%', ?, '%')");

            // Bind the ACTUAL SEARCH TERM 4 times
            mysqli_stmt_bind_param($stmt, "s", $searchTerm);

            if (mysqli_stmt_execute($stmt)) {
                $conversationsResult = mysqli_stmt_get_result($stmt);
            } else {
                // Add error handling
                die("Query failed: " . mysqli_error($conn));
            }
        }
    }

    if (!$searchPerformed) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM conversations");
        if (mysqli_stmt_execute($stmt)) {
            $conversationsResult = mysqli_stmt_get_result($stmt);
        }
    }
?>
<!-- Hero Section -->
<section class="flex flex-col items-center justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out bg-hero-pattern-conversations bg-cover bg-no-repeat bg-center h-[517px]">
    <form action="" method="get" class="relative">
        <input type="search" name="search" placeholder="<?php echo $lang['search_placeholder'] ?>"
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            class="text-lg md:text-xl outline-none border-none text-black w-[285px] h-[41px] lg:w-[482px] lg:h-[70px] rounded-full px-2 shadow-2xl">
        <input type="submit" value="<?php echo $lang['search_placeholder'] ?>"
            class="absolute <?= $is_rtl ? 'left-0' : 'right-0' ?> cursor-pointer text-white px-1 lg:px-3 h-[41px] lg:h-[70px] bg-primary rounded-full text-[18px] lg:text-[20px] font-semibold">
    </form>
</section>

<h1 class="text-xl md:text-2xl lg:text-4xl font-bold text-center px-2 my-5">Learn how to communicate with locals</h1>


<!-------------------------------------- conversations section  start--------------------------------->
<section class="py-16 opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <div class="container mx-auto text-center px-4">
        <div class="relative px-[52px] lg:px-[62px]">
            <div id="card-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth  pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                <!-- conversations Cards -->
                <?php while ($conversationsRows = mysqli_fetch_assoc($conversationsResult)) { ?>
                    <a href="http://localhost/tourism%20agency/conversations/conversation_page.php?id=<?= $conversationsRows['id'] ?>">
                        <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02] snap-start">

                            <img src="../media/conversations/<?= $conversationsRows["photo_url"] ?? 'default.jpg'; ?>" alt="" class="w-[265px] h-[175px] object-cover rounded-t-lg mt-2">
                            <div class="px-4 py-1 mt-2 text-center">
                                <h3 class="font-bold text-2xl mb-2"><?= $conversationsRows["name"]; ?></h3>
                            </div>

                        </div>
                    </a>
                <?php } ?>
            </div>

            <!-- Navigation Buttons -->
            <button id="card-prev" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2 hover:bg-yellow-800">
                <img src="../assets/left-desktop-arrow-circle.png" alt="">
            </button>
            <button id="card-next" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2 hover:bg-yellow-800">
                <img src="../assets/right-desktop-arrow-circle.png" alt="">
            </button>
        </div>
    </div>
</section>
<!-------------------------------------- conversations section  end--------------------------------->




<script>
    function initializeSlider(sliderSelector, prevSelector, nextSelector) {
        const slider = document.querySelector(sliderSelector);
        const prevBtn = document.querySelector(prevSelector);
        const nextBtn = document.querySelector(nextSelector);
        const cards = slider.querySelectorAll('.card');

        if (cards.length > 0) {
            const card = cards[0];
            const gap = parseInt(window.getComputedStyle(slider).gap) || 0;

            nextBtn.addEventListener('click', () => {
                slider.scrollBy({
                    left: card.offsetWidth + gap,
                    behavior: 'smooth'
                });
            });

            prevBtn.addEventListener('click', () => {
                slider.scrollBy({
                    left: -(card.offsetWidth + gap),
                    behavior: 'smooth'
                });
            });
        }
    }

    // Initialize both sliders
    initializeSlider('#card-slider', '#card-prev', '#card-next');
</script>
<?php
include __DIR__ . "/../includes/footer.php";

?>