<?php
    require("../includes/connect_db.php");
    $AgencyID = $_GET["AgencyID"];
    $stmt = mysqli_prepare($conn, "SELECT * FROM agency WHERE AgencyID = ?");
    mysqli_stmt_bind_param($stmt, "i", $AgencyID);
    if (mysqli_stmt_execute($stmt)) {
        $agencyResult = mysqli_stmt_get_result($stmt);
        $agencyRow = mysqli_fetch_assoc($agencyResult);
    }
    $pageTitle = $agencyRow["Name"];
    include __DIR__ . '/../includes/header.php';


    // Fetch media for THIS agency
    $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'agency' AND EntityID = ?");
    mysqli_stmt_bind_param($mediaStmt, "i", $AgencyID);
    mysqli_stmt_execute($mediaStmt);
    $mediaResult = mysqli_stmt_get_result($mediaStmt);


    $EntityID = $AgencyID;
    $EntityType = "agency";
    $pageURL = "http://localhost/tourism%20agency/main/$EntityType" . "_page.php?" . ucfirst($EntityType) . "ID=$EntityID";
    $_SESSION["redirect_url"] = $pageURL;
    require("../other/reviewPHPCode.php");


?>


<section class="flex flex-col items-center lg:flex-row lg:items-start lg:justify-center lg:gap-10  px-5 my-5 opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <?php
    // Fetch media for THIS agency
    $mediaPrimaryStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'agency' AND EntityID = ? AND IsPrimary = 1");
    mysqli_stmt_bind_param($mediaPrimaryStmt, "i", $AgencyID);
    mysqli_stmt_execute($mediaPrimaryStmt);
    $mediaPrimaryResult = mysqli_stmt_get_result($mediaPrimaryStmt);
    $mediaPrimaryRow = mysqli_fetch_assoc($mediaPrimaryResult); // Get first media entry    
    ?>
    <div><img src="../media/agencies/<?= $mediaPrimaryRow["URL"] ?? 'default.jpg'; ?>" alt="<?= $mediaPrimaryRow['Caption']; ?>" class="object-cover rounded-lg w-[600px]"></div>
    <div class=" text-xl gap-5">
        <h1 class="text-5xl font-bold mb-2"><?= $agencyRow["Name"] ?></h1>
        <p class="flex flex-col mb-2"><span class="text-primary font-semibold underline">Description: </span><?= $agencyRow["Description"] ?></p>
        <p class="flex flex-col mb-2"><span class="text-primary font-semibold underline">Address: </span><?= $agencyRow["Address"] ?></p>
        <p class="flex flex-col mb-2"><span class="text-primary font-semibold underline">Email: </span><?= $agencyRow["Email"] ?></p>
        <p class="flex flex-col mb-2"><span class="text-primary font-semibold underline">Phone: </span><?= $agencyRow["Phone"] ?></p>
    </div>
</section>
<?php if (mysqli_num_rows($mediaResult) > 0) { ?>
    <section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out">
        <img src="../assets/undraw_online-media_opxh.svg" alt="">
    </section>
<?php } ?>

<!-- --------------- Photos section start ---------------------------- -->
<?php if (mysqli_num_rows($mediaResult) > 0) { ?>
    <section class="my-8 opacity-0 translate-y-20 transition-all duration-1000 ease-out">
        <h2 class="my-5 text-2xl font-bold px-4 md:px-8 lg:px-20 md:text-3xl">Photos</h2>
        <div class="relative px-[52px] lg:px-[62px]">
            <div id="photos-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth  pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                <!-- agency Cards -->
                <?php

                while ($mediaRow = mysqli_fetch_assoc($mediaResult)) {
                ?>
                    <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02] snap-start">
                        <img src="../media/agencies/<?= $mediaRow["URL"] ?? 'default.jpg'; ?>" alt="<?= $mediaRow['Caption']; ?>" class="w-full h-[165px] object-cover rounded-t-2xl">
                    </div>

                <?php } ?>
            </div>
            <!-- Navigation Buttons -->
            <button id="photos-prev" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2 hover:bg-yellow-800">
                <img src="../assets/left-desktop-arrow-circle.png" alt="">
            </button>
            <button id="photos-next" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2 hover:bg-yellow-800">
                <img src="../assets/right-desktop-arrow-circle.png" alt="">
            </button>
        </div>
    </section>
<?php } ?>
<!-- --------------- Photos section end ---------------------------- -->


<!-- --------------- Circuits section start ---------------------------- -->
    <?php
    $currentDate = date("Y-m-d");
    // Fetch all Circuits for this agency
    $circuitsStmt = mysqli_prepare($conn, "SELECT 
            c.CircuitID,
            c.Name AS `c.Name`,
            ac.StartDate AS `ac.StartDate`,
            ac.EndDate AS `ac.EndDate` 
        FROM agency a
        JOIN agency_circuit ac 
            ON a.AgencyID = ac.AgencyID
        JOIN circuit c 
            ON ac.CircuitID = c.CircuitID
        WHERE 
        a.AgencyID = ? 
        AND ac.IsActive = 1 
        ");
    mysqli_stmt_bind_param($circuitsStmt, "i", $AgencyID);
    mysqli_stmt_execute($circuitsStmt);
    $circuitsResult = mysqli_stmt_get_result($circuitsStmt);

    if (mysqli_num_rows($circuitsResult) > 0) { ?>
        <section class="my-8 opacity-0 translate-y-20 transition-all duration-1000 ease-out">
            <h2 class="my-5 text-2xl font-bold px-4 md:px-8 lg:px-20 md:text-3xl">Circuits</h2>
            <div class="relative px-[52px] lg:px-[62px]">
                <div id="Circuits-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                    <?php while ($circuit = mysqli_fetch_assoc($circuitsResult)) {
                        // Get primary photo for this circuit
                        $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media 
                                                        WHERE EntityType = 'circuit' 
                                                        AND EntityID = ? 
                                                        AND IsPrimary = 1");
                        mysqli_stmt_bind_param($mediaStmt, "i", $circuit['CircuitID']);
                        mysqli_stmt_execute($mediaStmt);
                        $mediaResult = mysqli_stmt_get_result($mediaStmt);
                        $primaryPhoto = mysqli_fetch_assoc($mediaResult);

                    ?>
                        <a href="http://localhost/tourism%20agency/main/circuit_page.php?lang=<?= $current_lang ?>&CircuitID=<?= $circuit['CircuitID'] ?>">
                            <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02] snap-start">
                                <img src="../media/circuits/<?= $primaryPhoto['URL'] ?? 'default.jpeg'; ?>"
                                    alt="<?= $primaryPhoto['Caption'] ?>"
                                    class="w-[265px] h-[165px] object-cover rounded-t-2xl mt-2" />

                                <div class="p-4 w-full text-center">
                                    <h3 class="text-xl font-bold mb-2"><?= $circuit['c.Name'] ?></h3>
                                    <p class="text-sm font-medium mb-1">Start Date: <?= $circuit['ac.StartDate'] ?></p>
                                    <p class="text-sm font-medium mb-3">End Date: <?= $circuit['ac.EndDate'] ?></p>

                                </div>
                            </div>
                        </a>

                    <?php } ?>
                </div>

                <!-- Navigation Buttons -->
                <button id="Circuits-prev" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer rounded-full shadow-md p-2 hover:bg-yellow-800">
                    <img src="../assets/left-desktop-arrow-circle.png" alt="">
                </button>
                <button id="Circuits-next" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer rounded-full shadow-md p-2 hover:bg-yellow-800">
                    <img src="../assets/right-desktop-arrow-circle.png" alt="">
                </button>
            </div>
        </section>
    <?php } ?>
<!-- --------------- Circuits section end ---------------------------- -->


<section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <img src="../assets/undraw_opinion_bp12.svg" alt="">
</section>

<!-- --------------- Reviews section start ---------------------------- -->
<section class="opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <section class="mb-8 px-4 md:px-8 lg:px-20">
        <h2 class="my-5 text-2xl font-bold  md:text-3xl"><?php echo $lang["Reviews"] ?></h2>
        <?php if (!isset($_SESSION['CustomerID'])): ?>
            <div class="text-center mb-4 text-red-500">
                <?php echo $lang["login_to_review"]; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['CustomerID'])): ?>
            <form action="" method="POST" class="flex flex-col items-center">
                <!-- Comment Input -->
                <div class="w-full max-w-[537px] mb-8">
                    <input
                        type="text"
                        name="comment"
                        id="comment"
                        value="<?= htmlspecialchars($comment ?? '') ?>"
                        placeholder="Add Your Review!"
                        class="w-full px-3 h-14 md:h-16 lg:h-[67px] rounded-lg md:rounded-xl lg:rounded-[10px] 
                       text-lg md:text-xl lg:text-2xl outline-none text-gray-900 
                       border-2 border-gray-200 focus:border-primary transition-all">
                </div>

                <!-- Star Rating System -->
                <div class="w-full max-w-[537px] mb-12">
                    <div class="flex flex-col items-center">
                        <!-- Rating Display -->
                        <div class="flex items-center mb-4">
                            <span class="text-2xl font-bold text-yellow-500 mr-2" id="ratingValue">0</span>
                            <span class="text-gray-500">/ 5</span>
                            <span class="ml-2 text-2xl" id="ratingEmoji">😐</span>
                        </div>

                        <!-- Star Inputs -->
                        <div class="flex gap-2 md:gap-3 lg:gap-4" id="starRating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="button"
                                    data-rating="<?= $i ?>"
                                    class="star text-4xl md:text-5xl lg:text-6xl text-gray-300 
                                   transition-all duration-200 hover:scale-110"
                                    aria-label="Rate <?= $i ?> stars">
                                    ★
                                </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rate" id="selectedRating" value="0">

                        <!-- Labels -->
                        <div class="flex gap-20 lg:gap-80  text-gray-100 text-sm mt-3 px-1">
                            <span>Poor</span>
                            <span>Excellent</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    name="submit"
                    class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">
                    Add
                </button>
            </form>
        <?php else: ?>
            <div class="text-center py-4">
                <a href="http://localhost/tourism%20agency/auth/login.php" class="text-primary underline"><?php echo $lang["login_to_review_prompt"]; ?></a>
            </div>
        <?php endif; ?>
    </section>

    <style>
        .star.active {
            color: #eab308;
            filter: drop-shadow(0 0 4px rgba(234, 179, 8, 0.5));
        }
    </style>
    <?php if (mysqli_num_rows($reviewsResult) > 0) { ?>
        <div>
            <div class="relative px-[52px] lg:px-[62px]">
                <div id="reviews-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth  pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                    <!-- Reviews Cards -->
                    <?php while ($reviewsRows = mysqli_fetch_assoc($reviewsResult)) {

                    ?>
                        <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02] p-4">
    <div class="flex items-center gap-2 justify-center">
        <img src="../media/profile_photo_url/<?= $reviewsRows["ProfilePhoto"] ?? 'default.svg'; ?>" alt="<?= $mediaRow['Caption']; ?>" class="w-40 h-40 rounded-full mt-2">
    </div>

    <div class="py-5 border-t border-gray-200 w-full mt-4">
        <div class="px-4 py-1 text-center">
            <h3 class="font-semibold text-lg lg:text-xl mb-1"><?= $reviewsRows["Name"]; ?></h3>
            <h3 class="font-semibold text-lg lg:text-xl mb-2">
                <span class="text-yellow-400"><i class='bx bxs-star'></i></span> <?= $reviewsRows["Rate"]; ?>
            </h3>
        </div>
        <p class="text-sm px-4 text-center"><?= $reviewsRows["Comment"]; ?></p>
    </div>
</div>

                    <?php } ?>
                </div>

                <!-- Navigation Buttons -->
                <button id="reviews-prev" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2 hover:bg-yellow-800">
                    <img src="../assets/left-desktop-arrow-circle.png" alt="">
                </button>
                <button id="reviews-next" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2 hover:bg-yellow-800">
                    <img src="../assets/right-desktop-arrow-circle.png" alt="">
                </button>
            </div>
        </div>
    <?php } ?>
</section>
<!-- --------------- Reviews section end ---------------------------- -->

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const stars = document.querySelectorAll('.star');
        const ratingValue = document.getElementById('ratingValue');
        const ratingEmoji = document.getElementById('ratingEmoji');
        const selectedRating = document.getElementById('selectedRating');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = parseInt(star.dataset.rating);
                selectedRating.value = rating;

                // Update star display
                stars.forEach((s, index) => {
                    s.classList.toggle('active', index < rating);
                });

                // Update rating display
                ratingValue.textContent = rating;

                // Update emoji
                if (rating < 2) {
                    ratingEmoji.textContent = '😞';
                } else if (rating < 4) {
                    ratingEmoji.textContent = '😐';
                } else {
                    ratingEmoji.textContent = '😊';
                }
            });

            // Add hover effect
            star.addEventListener('mouseover', () => {
                const hoverRating = parseInt(star.dataset.rating);
                stars.forEach((s, index) => {
                    s.style.color = index < hoverRating ? '#facc15' : '#e5e7eb';
                });
            });

            star.addEventListener('mouseout', () => {
                const currentRating = parseInt(selectedRating.value);
                stars.forEach((s, index) => {
                    s.style.color = index < currentRating ? '#eab308' : '#e5e7eb';
                });
            });
        });

        // Add scroll animation script 

        // Scroll animation for sections
        const sections = document.querySelectorAll('section');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('opacity-0', 'translate-y-20');
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                }
            });
        }, {
            threshold: 0.1
        });

        sections.forEach(section => {
            observer.observe(section);
        });

    });

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
    initializeSlider('#photos-slider', '#photos-prev', '#photos-next');
    initializeSlider('#reviews-slider', '#reviews-prev', '#reviews-next');
    initializeSlider('#Circuits-slider', '#Circuits-prev', '#Circuits-next');
</script>
<?php
mysqli_close($conn);

include __DIR__ . "/../includes/footer.php";
?>