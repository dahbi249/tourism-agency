<?php
    require("../includes/connect_db.php");
    $CircuitID = $_GET["CircuitID"];
    $stmt = mysqli_prepare($conn, "SELECT c.CircuitID, c.Name AS `c.Name`, 
        c.Description AS `c.Description`, 
        MIN(cp.BasePrice) AS StartingPrice,
            cl.LocationID 
    FROM circuit c 
    LEFT JOIN circuitperiod cp ON c.CircuitID = cp.CircuitID
    JOIN circuitlocation cl ON c.CircuitID = cl.CircuitID
    WHERE c.CircuitID = ?
    GROUP BY c.CircuitID");
    mysqli_stmt_bind_param($stmt, "i", $CircuitID);
    if (mysqli_stmt_execute($stmt)) {
        $circuitResult = mysqli_stmt_get_result($stmt);
        $circuitRow = mysqli_fetch_assoc($circuitResult);
    }

    // Fetch media for THIS circuit
    $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'circuit' AND EntityID = ?");
    mysqli_stmt_bind_param($mediaStmt, "i", $CircuitID);
    mysqli_stmt_execute($mediaStmt);
    $mediaResult = mysqli_stmt_get_result($mediaStmt);

    $pageTitle = $circuitRow["c.Name"];
    include __DIR__ . '/../includes/header.php';
    $EntityID = $CircuitID;
    $EntityType = "circuit";
    $pageURL = "http://localhost/tourism%20agency/main/$EntityType" . "_page.php?" . ucfirst($EntityType) . "ID=$EntityID";
    $_SESSION["redirect_url"] = $pageURL;
    require("../other/reviewPHPCode.php");
    require("../other/reservationPHPCode.php");

?>

<section class="flex flex-col items-center lg:flex-row lg:items-start lg:justify-center lg:gap-10  px-5 my-5 opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <?php
    // Fetch media for THIS agency
    $mediaPrimaryStmt = mysqli_prepare($conn, "SELECT * FROM media WHERE EntityType = 'circuit' AND EntityID = ? AND IsPrimary = 1");
    mysqli_stmt_bind_param($mediaPrimaryStmt, "i", $CircuitID);
    mysqli_stmt_execute($mediaPrimaryStmt);
    $mediaPrimaryResult = mysqli_stmt_get_result($mediaPrimaryStmt);
    $mediaPrimaryRow = mysqli_fetch_assoc($mediaPrimaryResult); // Get first media entry    
    ?>
    <div><img src="../media/locations/<?= $mediaPrimaryRow["URL"] ?? 'default.jpg'; ?>" alt="<?= $mediaPrimaryRow['Caption']; ?>" class="object-cover rounded-lg"></div>
    <div class=" text-xl gap-5">
        <h1 class="text-5xl font-bold mb-2"><?= $circuitRow["c.Name"] ?></h1>
        <p class="flex flex-col mb-2"><span class="text-primary font-semibold underline">Description: </span><?= $circuitRow["c.Description"] ?></p>
        <p class="flex flex-col mb-2"><span class="text-primary font-semibold underline">Base Price: </span><?= $circuitRow["StartingPrice"] ?></p>
    </div>
</section>

<section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <img src="../assets/undraw_destination_fkst.svg" alt="">
</section>
<!-- --------------- Locations section start ---------------------------- -->
<?php
// Fetch all locations for this circuit
$locationsStmt = mysqli_prepare($conn, "SELECT l.* 
                                        FROM circuitlocation cl
                                        JOIN location l ON cl.LocationID = l.LocationID
                                        WHERE cl.CircuitID = ?");
mysqli_stmt_bind_param($locationsStmt, "i", $CircuitID);
mysqli_stmt_execute($locationsStmt);
$locationsResult = mysqli_stmt_get_result($locationsStmt);

if (mysqli_num_rows($locationsResult) > 0) { ?>
    <section class="my-8 opacity-0 translate-y-20 transition-all duration-1000 ease-out">
        <h2 class="my-5 text-2xl font-bold px-4 md:px-8 lg:px-20 md:text-3xl">Locations</h2>
        <div class="relative px-[52px] lg:px-[62px]">
            <div id="Locations-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                <?php while ($location = mysqli_fetch_assoc($locationsResult)) {
                    // Get primary photo for this location
                    $mediaStmt = mysqli_prepare($conn, "SELECT * FROM media 
                                                      WHERE EntityType = 'location' 
                                                      AND EntityID = ? 
                                                      AND IsPrimary = 1");
                    mysqli_stmt_bind_param($mediaStmt, "i", $location['LocationID']);
                    mysqli_stmt_execute($mediaStmt);
                    $mediaResult = mysqli_stmt_get_result($mediaStmt);
                    $primaryPhoto = mysqli_fetch_assoc($mediaResult);
                ?>
                    <a href="http://localhost/tourism%20agency/main/location_page.php?lang=<?= $current_lang ?>&LocationID=<?= $location['LocationID'] ?>">
                        <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02] snap-start">

                            <img src="../media/locations/<?= $primaryPhoto['URL'] ?? 'default.jpg'; ?>"
                                alt="<?= $primaryPhoto['Caption'] ?>"
                                class="w-full h-[165px] object-cover rounded-t-2xl">

                            <div class="p-4 w-full text-center">
                                <h3 class="text-lg lg:text-xl font-semibold mb-2"><?= $location['Name'] ?></h3>

                            </div>
                    </a>
            </div>

        <?php } ?>
        </div>

        <!-- Navigation Buttons -->
        <button id="Locations-prev" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] left-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer rounded-full shadow-md p-2 hover:bg-yellow-800">
            <img src="../assets/left-desktop-arrow-circle.png" alt="">
        </button>
        <button id="Locations-next" class="absolute w-[50px] h-[50px] lg:w-[70px] lg:h-[70px] right-0 top-1/2 -translate-y-1/2 bg-primary cursor-pointer rounded-full shadow-md p-2 hover:bg-yellow-800">
            <img src="../assets/right-desktop-arrow-circle.png" alt="">
        </button>
        </div>
    </section>
<?php } ?>
<!-- --------------- Locations section end ---------------------------- -->

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
                <!-- Location Cards -->
                <?php

                while ($mediaRow = mysqli_fetch_assoc($mediaResult)) {
                ?>
                    <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02] snap-start">
                        <img src="../media/locations/<?= $mediaRow["URL"] ?? 'default.jpg'; ?>" alt="<?= $mediaRow['Caption']; ?>" class="w-full h-[165px] object-cover rounded-t-2xl">
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
                <div id="agencies-slider" class="flex gap-[30px] lg:gap-[150px] overflow-x-auto snap-x snap-mandatory scroll-smooth  pb-4 [scrollbar-width:none] [-ms-overflow-style:none] [-webkit-overflow-scrolling:touch]">
                    <!-- Reviews Cards -->
                    <?php while ($reviewsRows = mysqli_fetch_assoc($reviewsResult)) {

                    ?>
                        <div class="card flex flex-col items-center flex-shrink-0 w-[238px] lg:w-[360px] rounded-2xl shadow-2xl border border-primary transition-transform duration-300 hover:scale-[1.02] snap-start p-1">
                            <div class="flex items-center gap-2">
                                <img src="../media/profile_photo_url/<?= $reviewsRows["ProfilePhoto"] ?? 'default.svg'; ?>" alt="<?= $mediaRow['Caption']; ?>" class="rounded-full w-40 h-40 mt-2">
                            </div>

                            <div class="py-5 border-t-2 border-white rounded-lg w-full my-1">
                                <div class="px-4 py-1 mt-2 text-center">
                                    <h3 class="font-semibold text-lg lg:text-xl mb-2"><?= $reviewsRows["Name"]; ?></h3>
                                    <h3 class="font-semibold text-lg lg:text-xl mb-2">
                                        <span class="text-yellow-400"><i class='bx bxs-star'></i></span><?= $reviewsRows["Rate"]; ?>
                                    </h3>
                                </div>
                                <p class="px-4 text-center"><?= $reviewsRows["Comment"]; ?></p>
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
    <?php } else { ?>
        <div class="px-4 md:px-8 lg:px-20 text-gray-500">
            No photos available for this circuit
        </div>
    <?php } ?>
</section>
<!-- --------------- Reviews section end ---------------------------- -->

<section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <img src="../assets/undraw_travel-booking_1t44.svg" alt="">
</section>
<!-- --------------- Reservation section start ---------------------------- -->
<section class="opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <section class="mb-8 px-4 md:px-8 lg:px-20">
        <h2 class="my-5 text-2xl font-bold md:text-3xl"><?= $lang["reserve your place now"] ?></h2>
        <?php if (!isset($_SESSION['CustomerID'])): ?>
            <div class="text-center mb-4 text-red-500">
                <?php echo $lang["login_to_reserve"]; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <p class="font-medium"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['CustomerID'])): ?>

            <form action="" method="POST" class="flex flex-col items-center gap-6 max-w-2xl mx-auto bg-white/10 p-6 rounded-xl backdrop-blur-sm">
                <!-- Agency Selection -->
                <div class="w-full">
                    <label class="block text-white text-lg mb-2 font-medium" for="agencySelect">
                        Select Agency <span class="text-red-500">*</span>
                    </label>
                    <select name="agency" id="agencySelect" required
                        class="w-full px-3 h-14 md:h-16 lg:h-[67px] rounded-lg md:rounded-xl lg:rounded-[10px] 
                       text-lg md:text-xl lg:text-2xl outline-none text-gray-900 
                       border-2 border-gray-200 focus:border-primary transition-all">
                        <option value="">Select an agency</option>
                        <?php foreach ($agencies as $agency): ?>
                            <option value="<?= htmlspecialchars($agency['AgencyID']) ?>"
                                data-discount-type="<?= htmlspecialchars($agency['DiscountType'] ?? '') ?>"
                                data-discount-value="<?= htmlspecialchars($agency['DiscountValue'] ?? 0) ?>">
                                <?= htmlspecialchars($agency['Name']) ?>
                                <?php if (!empty($agency['DiscountType'])): ?>
                                    (<?= htmlspecialchars($agency['DiscountValue']) ?>
                                    <?= $agency['DiscountType'] === 'percentage' ? '%' : 'DZD' ?> off)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="w-full" id="accommodationSection" style="display: none;">
                    <label class="block text-white text-lg mb-2 font-medium">Select Accommodation <span class="text-red-500">*</span></label>
                    <select name="accommodation" id="accommodationSelect" required
                        class="w-full px-3 h-14 md:h-16 lg:h-[67px] rounded-lg md:rounded-xl lg:rounded-[10px] 
                       text-lg md:text-xl lg:text-2xl outline-none text-gray-900 
                       border-2 border-gray-200 focus:border-primary transition-all">
                        <option value="">Select an accommodation</option>
                    </select>
                </div>

                <div class="w-full" id="roomTypeSection" style="display: none;">
                    <label class="block text-white text-lg mb-2 font-medium">Room Type <span class="text-red-500">*</span> </label>
                    <select name="room_type" id="roomTypeSelect" required
                        class="w-full px-3 h-14 md:h-16 lg:h-[67px] rounded-lg md:rounded-xl lg:rounded-[10px] 
                       text-lg md:text-xl lg:text-2xl outline-none text-gray-900 
                       border-2 border-gray-200 focus:border-primary transition-all">
                        <option value="">Select a room type</option>
                    </select>
                </div>

                <div class="w-full">
                    <label class="block text-white text-lg mb-2 font-medium">Select Tour Period <span class="text-red-500">*</span></label>
                    <select name="period" id="periodSelect" required
                        class="w-full px-3 h-14 md:h-16 lg:h-[67px] rounded-lg md:rounded-xl lg:rounded-[10px] 
                       text-lg md:text-xl lg:text-2xl outline-none text-gray-900 
                       border-2 border-gray-200 focus:border-primary transition-all">
                        <option value="">Select a period</option>
                        <?php foreach ($periods as $period): ?>
                            <option value="<?= $period['period_start'] ?>|<?= $period['period_end'] ?>"
                                data-price="<?= $period['BasePrice'] ?>">
                                <?= date('M j, Y', strtotime($period['period_start'])) ?> -
                                <?= date('M j, Y', strtotime($period['period_end'])) ?>
                                (<?= $period['BasePrice'] ?> DZD)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="start_date" id="start_date">
                    <input type="hidden" name="end_date" id="end_date">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-white text-lg mb-2 font-medium">Adults <span class="text-red-500">*</span></label>
                        <input type="number" name="num_adults" min="1" required id="num_adults"
                            class="block text-black px-4 py-3 text-lg rounded-xl border-2 border-gray-200 focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-white text-lg mb-2 font-medium">Children <span class="text-red-500">*</span></label>
                        <input type="number" name="num_children" min="0" id="num_children"
                            class="text-black px-4 py-3 text-lg rounded-xl border-2 border-gray-200 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="w-full bg-gray-800/80 p-6 rounded-xl">
                    <h3 class="text-white text-xl font-bold mb-4">Price Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-white text-lg">Base Price:</span>
                            <span class="text-white text-lg" id="base-price">0 DZD</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-white text-lg">Room Price:</span>
                            <span class="text-white text-lg" id="room-price">0 DZD</span>
                        </div>
                        <?php if (!empty($discountInfo)): ?>
                            <div class="flex justify-between items-center text-green-400">
                                <span class="text-lg">Discount:</span>
                                <span class="text-lg" id="discount-amount">0 DZD</span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-600">
                            <span class="text-white text-xl font-bold">Total:</span>
                            <span class="text-primary text-2xl font-bold" id="total-price">0 DZD</span>
                        </div>
                    </div>
                </div>

                <button type="submit" name="submit_reservation" id="confirm-reservation"
                    class="w-full bg-primary text-white text-xl font-semibold py-4 rounded-xl hover:bg-primary-dark transition-colors">
                    Confirm Reservation
                </button>

                <script>
                    const agencySelect = document.getElementById('agencySelect');
                    const accommodationSelect = document.getElementById('accommodationSelect');
                    const accommodationSection = document.getElementById('accommodationSection');
                    const roomTypeSelect = document.getElementById('roomTypeSelect');
                    const roomTypeSection = document.getElementById('roomTypeSection');
                    const periodSelect = document.getElementById('periodSelect');
                    const startDateInput = document.getElementById('start_date');
                    const endDateInput = document.getElementById('end_date');
                    const numAdultsInput = document.getElementById('num_adults');
                    const numChildrenInput = document.getElementById('num_children');
                    const basePriceDisplay = document.getElementById('base-price');
                    const roomPriceDisplay = document.getElementById('room-price');
                    const totalPriceDisplay = document.getElementById('total-price');


                    // Add this function to format dates
                    function formatDate(dateString) {
                        const options = {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        };
                        return new Date(dateString).toLocaleDateString('en-US', options);
                    }

                    // Update period dropdown when agency changes
                    const updatePeriods = async () => {
                        const agencyId = agencySelect.value;
                        if (!agencyId) return;

                        try {
                            // Clear existing options
                            periodSelect.innerHTML = '<option value="">Loading periods...</option>';

                            // Fetch agency-specific periods
                            const response = await fetch(`../other/get_periods.php?agency_id=${agencyId}&circuit_id=<?= $circuitId ?>`);
                            const periods = await response.json();

                            // Populate new options
                            periodSelect.innerHTML = periods.length > 0 ?
                                periods.map(p => `
                    <option value="${p.start}|${p.end}"
                        data-price="${p.price}"
                        data-available="${p.available}">
                        ${formatDate(p.start)} - ${formatDate(p.end)}
                        (${parseFloat(p.price).toFixed(2)} DZD)
                    </option>
                `).join('') :
                                '<option value="">No available periods</option>';

                            // Add default option
                            periodSelect.insertAdjacentHTML('afterbegin', '<option value="">Select a period</option>');

                            // Trigger price calculation
                            calculateTotalPrice();

                        } catch (error) {
                            console.error('Error loading periods:', error);
                            periodSelect.innerHTML = '<option value="">Error loading periods</option>';
                        }
                    };

                    // Add event listeners
                    agencySelect.addEventListener('change', updatePeriods);
                    periodSelect.addEventListener('change', function() {
                        const [start, end] = this.value.split('|');
                        startDateInput.value = start;
                        endDateInput.value = end;
                        calculateTotalPrice();
                    });

                    // Initial load if agency is preselected
                    <?php if (!empty($_POST['agency'])): ?>
                        updatePeriods();
                    <?php endif; ?>


                    periodSelect.addEventListener('change', function() {
                        const [start, end] = this.value.split('|');
                        startDateInput.value = start;
                        endDateInput.value = end;
                    });

                    agencySelect.addEventListener('change', function() {
                        const agencyId = this.value;
                        fetch(`../other/get_accommodations.php?agency=${agencyId}&circuit=<?= $circuitId ?>`)
                            .then(response => response.json())
                            .then(accommodations => {
                                accommodationSelect.innerHTML = '<option value="">Select an accommodation</option>' +
                                    accommodations.map(a => `
                                        <option value="${a.AccommodationID}">${a.Name}</option>
                                    `).join('');
                                accommodationSection.style.display = 'block';
                                calculateTotalPrice();
                            });
                    });

                    accommodationSelect.addEventListener('change', function() {
                        const accommodationId = this.value;
                        fetch(`../other/get_roomtypes.php?accommodation=${accommodationId}`)
                            .then(response => response.json())
                            .then(roomTypes => {
                                roomTypeSelect.innerHTML = '<option value="">Select a room type</option>' +
                                    roomTypes.map(rt => `
                                        <option value="${rt.RoomTypeID}" data-price="${rt.PricePerNight}">
                                            ${rt.Type} - ${rt.PricePerNight} DZD/night
                                        </option>
                                    `).join('');
                                roomTypeSection.style.display = 'block';
                                calculateTotalPrice();
                            });
                    });

                    async function calculateTotalPrice() {
                        const agencyId = agencySelect.value;
                        const roomTypeId = roomTypeSelect.value;
                        const numAdults = parseInt(numAdultsInput.value) || 0;
                        const numChildren = parseInt(numChildrenInput.value) || 0;
                        const periodPrice = parseFloat(periodSelect.selectedOptions[0].dataset.price) || 0;
                        const roomPricePerNight = parseFloat(roomTypeSelect.selectedOptions[0]?.dataset.price) || 0;
                        const totalRoomPrice = roomPricePerNight * <?= $totalDuration ?>;
                        let totalPrice = (periodPrice + totalRoomPrice) * (numAdults + (numChildren * 0.5));

                        const selectedAgencyOption = agencySelect.selectedOptions[0];
                        const discountType = selectedAgencyOption.dataset.discountType;
                        const discountValue = parseFloat(selectedAgencyOption.dataset.discountValue) || 0;

                        if (discountType) {
                            if (discountType === 'percentage') {
                                totalPrice *= (1 - (discountValue / 100));
                            } else {
                                totalPrice -= discountValue;
                            }
                        }

                        basePriceDisplay.textContent = periodPrice.toFixed(2) + ' DZD';
                        roomPriceDisplay.textContent = totalRoomPrice.toFixed(2) + ' DZD';
                        totalPriceDisplay.textContent = totalPrice.toFixed(2) + ' DZD';
                    }

                    [agencySelect, accommodationSelect, roomTypeSelect, numAdultsInput, numChildrenInput, periodSelect].forEach(el => {
                        el.addEventListener('change', calculateTotalPrice);
                    });

                    calculateTotalPrice(); // Initial calculation
                </script>
            </form>
        <?php else: ?>
            <div class="text-center py-4">
                <a href="http://localhost/tourism%20agency/auth/login.php" class="text-primary underline"><?php echo $lang["login_to_reserve_prompt"]; ?></a>
            </div>
        <?php endif; ?>
    </section>
</section>
<!-- --------------- Reservation section end ---------------------------- -->



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
        if (!slider || !prevBtn || !nextBtn) return;
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
    initializeSlider('#Locations-slider', '#Locations-prev', '#Locations-next');
</script>
<?php
mysqli_close($conn);

include __DIR__ . "/../includes/footer.php";
?>