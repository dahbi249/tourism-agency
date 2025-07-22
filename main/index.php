<?php
$pageTitle = "Home page";
include __DIR__ . '/../includes/header.php';
require("../other/agenciesPHPCode.php");
require("../other/locationsPHPCode.php");
require("../other/accommodationsPHPCode.php");
require("../other/circuitsPHPCode.php");

// Fetch cities
$cityQuery = "SELECT DISTINCT City FROM location ORDER BY City ASC";
$cityResult = mysqli_query($conn, $cityQuery);

// Fetch location names
$locationQuery = "SELECT DISTINCT Name FROM location ORDER BY Name ASC";
$locationResult = mysqli_query($conn, $locationQuery);
?>
<main>
    <!-- Hero Section -->
    <section id="hero" class="h-screen text-center relative flex items-center justify-center transition-all duration-1000">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Content -->
        <div class="relative z-10 text-center text-white px-4 ">
            <h1 class="text-xl md:text-2xl lg:text-4xl font-bold text-center px-2 mb-8 text-white"><?php echo $lang['welcome_message_hero'] ?></h1>
            <p class=" px-10 md:px-20 lg:px-40 text-xl md:text-2xl mb-8 text-white"><?php echo $lang['hero_paragraph'] ?></p>
        </div>
    </section>

    <section class="flex flex-col lg:flex-row items-center justify-evenly">
        <div class="flex flex-col items-center">
            <i class='bx text-5xl bx-building-house'></i>
            <h3 class=" text-xl font-medium">Backed by travelers</h3>
            <p class="text-center w-40">Book confidently thanks to reviews from travelers who have been there.</p>
        </div>
        <div class="flex flex-col items-center">
            <i class='bx text-5xl bx-star'></i>
            <h3 class=" text-xl font-medium">Best rates around</h3>
            <p class="text-center w-40">Compare prices from 200+ booking sites to book with the best deal.</p>
        </div>
        <div class="flex flex-col items-center">
            <i class='bx text-5xl bx-search'></i>
            <h3 class=" text-xl font-medium">Search without worry</h3>
            <p class="text-center w-40">We’re completely free to use—no hidden charges or fees on flight prices at all.</p>
        </div>
    </section>

    <section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out ">
        <img src="../assets/undraw_choose-card_es1o.svg" alt="">
    </section>

    <hr>


    <!-------------------------------------- Agencies section  start--------------------------------->
    <section class="py-16 opacity-0 translate-y-20 transition-all duration-1000 ease-out ">
        <div class="container mx-auto text-center px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Your trusted travel partner for unforgettable journeys.</h2>
            <div class="relative px-[52px] lg:px-[62px]">
                <div id="agencies-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth  pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                    <!-- Agency Cards -->
                    <?php while ($agenciesRows = mysqli_fetch_assoc($agenciesResult)) {
                        // Fetch media for THIS agency
                        $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'agency' AND EntityID = ? AND IsPrimary = 1");
                        mysqli_stmt_bind_param($mediaStmt, "i", $agenciesRows['AgencyID']);
                        mysqli_stmt_execute($mediaStmt);
                        $mediaResult = mysqli_stmt_get_result($mediaStmt);
                        $mediaRow = mysqli_fetch_assoc($mediaResult); // Get first media entry    
                    ?>
                        <a href="http://localhost/tourism%20agency/main/agency_page.php?AgencyID=<?= $agenciesRows['AgencyID'] ?>">
                            <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px]  rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02]">
                                <img src="../media/agencies/<?= $mediaRow["URL"] ?? 'default.jpg'; ?>" alt="<?= $mediaRow['Caption']; ?>" class="w-full h-[175px] object-cover rounded-t-2xl">

                                <div class="px-4 py-4 text-center">
                                    <h3 class="font-semibold text-lg lg:text-xl  mb-1"><?= $agenciesRows["Name"]; ?></h3>
                                    <p class="text-sm  mb-3"><?= $agenciesRows["Address"]; ?></p>
                                </div>


                            </div>
                        </a>

                    <?php } ?>
                </div>

                <!-- Navigation Buttons -->
                <button id="agencies-prev" class="absolute w-[50px] h-[50px] hover:scale-105 transition-all duration-100 ease-out lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2">
                    <img src="../assets/left-desktop-arrow-circle.png" alt="">
                </button>
                <button id="agencies-next" class="absolute w-[50px] h-[50px] hover:scale-105 transition-all duration-100 ease-out lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2">
                    <img src="../assets/right-desktop-arrow-circle.png" alt="">
                </button>
            </div>
            <button class="text-white mx-auto  w-[250px] h-[53.84px] lg:w-[362px] lgh-[73px]  bg-primary rounded-full lg:text-[36px] text-[24px] font-semibold hover:scale-105  transition-all duration-1000 ease-out">
                <a href="http://localhost/tourism%20agency/main/agencies.php"><?php echo $lang['see more'] ?></a>
            </button>
        </div>
    </section>
    <!-------------------------------------- Agencies section  end--------------------------------->


    <div id="map" class="w-full h-[500px] rounded-lg shadow-lg mb-10"></div>


    <hr>
    <a href="">
        <section class="p-3 flex text-white flex-col lg:flex-row justify-evenly items-center opacity-0  translate-y-20 transition-all duration-1000 ease-out bg-primary hover:scale-105">
            <h1 class="text-6xl font-bold"><span class="text-green-600">Learn</span> more about <span class="lg:text-9xl block text-red-600">Algeria</span></h1>
            <img src="../assets/ALGLOGO.png" alt="" class="w-[500px]">
        </section>
    </a>
    <hr>

    <hr>
    <section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out ">
        <img src="../assets/undraw_travel-mode_ydxo.svg" alt="">
    </section>

    <hr>

    <!-------------------------------------- Circuits section  start--------------------------------->
    <section class="py-16 opacity-0 translate-y-20 transition-all duration-1000 ease-out ">
        <div class="container mx-auto text-center px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">From thrilling adventures to peaceful escapes – we’ve got it all!</h2>
            <div class="relative px-[52px] lg:px-[62px]">
                <div id="circuits-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth  pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                    <!-- Location Cards -->
                    <?php while ($circuitsRows = mysqli_fetch_assoc($circuitsResult)) {
                        // Fetch media for THIS agency
                        $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'circuit' AND EntityID = ? AND IsPrimary = 1");
                        mysqli_stmt_bind_param($mediaStmt, "i", $circuitsRows['CircuitID']);
                        mysqli_stmt_execute($mediaStmt);
                        $mediaResult = mysqli_stmt_get_result($mediaStmt);
                        $mediaRow = mysqli_fetch_assoc($mediaResult); // Get first media entry    
                    ?>
                        <a href="http://localhost/tourism%20agency/main/circuit_page.php?CircuitID=<?= $circuitsRows['CircuitID'] ?>">
                            <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02]">
                                <img src="../media/circuits/<?= $mediaRow["URL"] ?? 'default.jpeg'; ?>" alt="<?= $mediaRow['Caption']; ?>" class="w-full h-[165px] object-cover rounded-t-2xl">

                                <div class="px-4 py-4 text-center">
                                    <h3 class="font-semibold text-lg lg:text-xl  mb-1"><?= $circuitsRows["c.Name"]; ?></h3>
                                    <p class="text-sm  mb-2"><?= $circuitsRows["c.Description"]; ?></p>
                                    <div class="text-lg font-semibold text-primary mb-3">
                                        <?= number_format($circuitsRows['StartingPrice'], 2) ?> DZD
                                    </div>
                                </div>

                            </div>
                        </a>

                    <?php } ?>
                </div>

                <!-- Navigation Buttons -->
                <button id="circuits-prev" class="absolute w-[50px] h-[50px] hover:scale-105 transition-all duration-100 ease-out lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2">
                    <img src="../assets/left-desktop-arrow-circle.png" alt="">
                </button>
                <button id="circuits-next" class="absolute w-[50px] h-[50px] hover:scale-105 transition-all duration-100 ease-out lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2">
                    <img src="../assets/right-desktop-arrow-circle.png" alt="">
                </button>
            </div>
            <button class="text-white mx-auto  w-[250px] h-[53.84px] lg:w-[362px] lgh-[73px]  bg-primary rounded-full lg:text-[36px] text-[24px] font-semibold hover:scale-105  transition-all duration-1000 ease-out">
                <a href="http://localhost/tourism%20agency/main/circuits.php"><?php echo $lang['see more'] ?></a>
            </button>
        </div>
    </section>
    <!-------------------------------------- Circuits section  end--------------------------------->
    <hr>
    <a href="">
        <section class="p-3 flex text-white flex-col lg:flex-row justify-evenly items-center opacity-0  translate-y-20 transition-all duration-1000 ease-out bg-primary hover:scale-105">
            <h1 class="text-6xl font-bold w-96">Special <span class=" text-5xl text-red-600">Discount</span> for Early <span class="text-5xl text-green-600">Birds</span> – Reserve your spot today!</h1>
            <img src="../assets/Discount.png" alt="" class="w-[500px]">
            <img src="../assets/maqam.png" alt="" class="w-[500px]">
        </section>
    </a>
    <hr>

    <!-- <section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out ">
        <img src="../assets/undraw_eiffel-tower_ju2s.svg" alt="">
    </section>
    <hr> -->

    <!-------------------------------------- Locations section  start--------------------------------->
    <section class="py-16 opacity-0 translate-y-20 transition-all duration-1000 ease-out ">
        <div class="container mx-auto text-center px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Where nature and heritage meet in perfect harmony.</h2>
            <div class="relative px-[52px] lg:px-[62px]">
                <div id="locations-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth  pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                    <!-- Location Cards -->
                    <?php while ($locationsRows = mysqli_fetch_assoc($locationsResult)) {
                        // Fetch media for THIS agency
                        $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'location' AND EntityID = ? AND IsPrimary = 1");
                        mysqli_stmt_bind_param($mediaStmt, "i", $locationsRows['LocationID']);
                        mysqli_stmt_execute($mediaStmt);
                        $mediaResult = mysqli_stmt_get_result($mediaStmt);
                        $mediaRow = mysqli_fetch_assoc($mediaResult); // Get first media entry    
                    ?>
                        <a href="http://localhost/tourism%20agency/main/location_page.php?LocationID=<?= $locationsRows['LocationID'] ?>">
                            <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02]">
                                <img src="../media/locations/<?= $mediaRow["URL"] ?? 'default.jpg'; ?>" alt="<?= $mediaRow['Caption']; ?>" class="w-full h-[165px] object-cover rounded-t-2xl">

                                <div class="px-4 py-4 text-center">
                                    <h3 class="font-semibold text-lg lg:text-xl mb-1"><?= $locationsRows["Name"]; ?></h3>
                                    <p class="text-sm mb-3"><?= $locationsRows["Address"]; ?></p>
                                </div>


                            </div>
                        </a>

                    <?php } ?>
                </div>

                <!-- Navigation Buttons -->
                <button id="locations-prev" class="absolute w-[50px] h-[50px] hover:scale-105 transition-all duration-100 ease-out lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2">
                    <img src="../assets/left-desktop-arrow-circle.png" alt="">
                </button>
                <button id="locations-next" class="absolute w-[50px] h-[50px] hover:scale-105 transition-all duration-100 ease-out lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2">
                    <img src="../assets/right-desktop-arrow-circle.png" alt="">
                </button>
            </div>
            <button class="text-white mx-auto  w-[250px] h-[53.84px] lg:w-[362px] lgh-[73px]  bg-primary rounded-full lg:text-[36px] text-[24px] font-semibold hover:scale-105  transition-all duration-1000 ease-out">
                <a href="http://localhost/tourism%20agency/main/locations.php"><?php echo $lang['see more'] ?></a>
            </button>
        </div>
    </section>
    <!-------------------------------------- Locations section  end--------------------------------->


    <hr>
    <section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out ">
        <img src="../assets/undraw_house-searching_g2b8.svg" alt="">
    </section>

    <hr>
    <hr>
    <a href="">
        <section class="p-3 flex text-white flex-col lg:flex-row justify-evenly items-center opacity-0  translate-y-20 transition-all duration-1000 ease-out bg-primary hover:scale-105">
            <img src="../assets/ads.png" alt="" class="w-[700px]">
        </section>
    </a>
    <hr>
    <!-------------------------------------- Accommodations section  start--------------------------------->
    <section class="py-16 opacity-0 translate-y-20 transition-all duration-1000 ease-out ">
        <div class="container mx-auto text-center px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Comfort meets authenticity.</h2>
            <div class="relative px-[52px] lg:px-[62px]">
                <div id="accommodations-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth  pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                    <!-- Location Cards -->
                    <?php while ($accommodationsRows = mysqli_fetch_assoc($accommodationsResult)) {
                        // Fetch media for THIS agency
                        $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'accommodation' AND EntityID = ? AND IsPrimary = 1");
                        mysqli_stmt_bind_param($mediaStmt, "i", $accommodationsRows['AccommodationID']);
                        mysqli_stmt_execute($mediaStmt);
                        $mediaResult = mysqli_stmt_get_result($mediaStmt);
                        $mediaRow = mysqli_fetch_assoc($mediaResult); // Get first media entry    
                    ?>
                        <a href="http://localhost/tourism%20agency/main/accommodation_page.php?AccommodationID=<?= $accommodationsRows['AccommodationID'] ?>">
                            <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02]">
                                <img src="../media/accommodations/<?= $mediaRow["URL"] ?? 'default.jpg'; ?>" alt="<?= $mediaRow['Caption']; ?>" class="w-full h-[165px] object-cover rounded-t-2xl">

                                <div class="px-4 py-4 text-center">
                                    <h3 class="font-semibold text-lg lg:text-xl mb-1"><?= $accommodationsRows["Name"]; ?></h3>
                                    <p class="text-sm mb-3"><?= $accommodationsRows["Address"]; ?></p>
                                </div>
                            </div>
                        </a>

                    <?php } ?>
                </div>

                <!-- Navigation Buttons -->
                <button id="accommodations-prev" class="absolute w-[50px] h-[50px] hover:scale-105 transition-all duration-100 ease-out lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2">
                    <img src="../assets/left-desktop-arrow-circle.png" alt="">
                </button>
                <button id="accommodations-next" class="absolute w-[50px] h-[50px] hover:scale-105 transition-all duration-100 ease-out lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer  rounded-full shadow-md p-2">
                    <img src="../assets/right-desktop-arrow-circle.png" alt="">
                </button>
            </div>
            <button class="text-white mx-auto  w-[250px] h-[53.84px] lg:w-[362px] lgh-[73px]  bg-primary rounded-full lg:text-[36px] text-[24px] font-semibold hover:scale-105  transition-all duration-1000 ease-out">
                <a href="http://localhost/tourism%20agency/main/accommodations.php"><?php echo $lang['see more'] ?></a>
            </button>
        </div>
    </section>
    <!-------------------------------------- Accommodations section  end--------------------------------->



</main>


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
    initializeSlider('#agencies-slider', '#agencies-prev', '#agencies-next');
    initializeSlider('#locations-slider', '#locations-prev', '#locations-next');
    initializeSlider('#accommodations-slider', '#accommodations-prev', '#accommodations-next');
    initializeSlider('#circuits-slider', '#circuits-prev', '#circuits-next');




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



    // Background images array (replace with your own images)

    const backgrounds = [
        'url("../assets/pexels-eberhardgross-1612351.jpg")',
        'url("../assets/hotel-6862159_1920.jpg")',
        'url("../assets/camel-8430227_1920.jpg")',
        'url("../assets/alger-2471634_1920.jpg")',
        'url("../assets/lake-9585821_1920.jpg")',
        'url("../assets/reception-2507752_1920.jpg")',
        'url("../assets/hallstatt-3609863_1920.jpg")'
    ];

    let currentBg = 0;
    const hero = document.getElementById('hero');

    function changeBackground() {
        currentBg = (currentBg + 1) % backgrounds.length;
        hero.style.backgroundImage = backgrounds[currentBg];
    }

    // Initialize first background
    hero.style.backgroundImage = backgrounds[0];
    hero.classList.add('bg-cover', 'bg-center');

    // Change background every 3 seconds
    setInterval(changeBackground, 3000);



    // Initialize the map
    const map = L.map('map').setView([28.0339, 1.6596], 5); // Centered on Algeria

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Optional: add marker for Algiers
    L.marker([36.7538, 3.0588]).addTo(map)
        .bindPopup('Welcome to Algiers!')
        .openPopup();
</script>



<?php
include __DIR__ . "/../includes/footer.php";
?>