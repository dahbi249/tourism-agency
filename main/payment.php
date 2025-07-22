<?php
   $pageTitle = "Payment Page";
   include __DIR__ . '/../includes/header.php';
   require("../includes/connect_db.php");
   
   $reservationId = $_GET["ReservationID"];
   
   try {
       if (!$reservationId) throw new Exception("Invalid reservation ID");
   
       // Get reservation details with price calculation
       $query = "
           SELECT 
               r.*,
               cus.Name AS CustomerName,
               cus.Email AS CustomerEmail,
               a.Name AS AgencyName,
               a.Address AS AgencyAddress,
               c.Name AS CircuitName,
               rt.Type AS RoomType,
               acc.Name AS AccommodationName,
               acc.Address AS AccommodationAddress
               
           FROM reservation r
           JOIN customer cus ON r.CustomerID = cus.CustomerID
           JOIN agency a ON r.AgencyID = a.AgencyID
           JOIN circuit c ON r.CircuitID = c.CircuitID
           JOIN roomtype rt ON r.RoomTypeID = rt.RoomTypeID
           JOIN accommodation acc ON rt.AccommodationID = acc.AccommodationID
           JOIN circuitperiod cp ON 
               r.CircuitID = cp.CircuitID AND 
               r.StartDate BETWEEN cp.StartDate AND cp.EndDate
           WHERE r.ReservationID = ?
       ";
   
       $stmt = mysqli_prepare($conn, $query);
       mysqli_stmt_bind_param($stmt, "i", $reservationId);
       mysqli_stmt_execute($stmt);
       $result = mysqli_stmt_get_result($stmt);
       $reservationData = mysqli_fetch_assoc($result);
   
       if (!$reservationData) throw new Exception("Reservation not found");
   
   } catch (Exception $e) {
       die("Error: " . $e->getMessage());
   }
   require("../other/paymentPHPCode.php");

?>
   
<?php if (isset($_SESSION['flash'])): ?>
<div class="container mx-auto px-4 py-2">
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?> p-4 rounded-lg">
        <?php $_SESSION['flash']['message'] ?>
    </div>
</div>
<?php unset($_SESSION['flash']); endif; ?>
<main class="container mx-auto px-4 py-8">
    <!-- Reservation Details Section -->
    <section class=" rounded-lg shadow-md p-6 mb-8">
        <h1 class="text-4xl underline  font-bold text-white mb-6">Reservation Details</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Price Breakdown -->
            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-blue-600 mb-3">Price Breakdown</h2>
                <div class="space-y-1">
                    <p class="flex justify-between">
                        <span class="font-medium">Circuit Period Price:</span>
                        $<?= number_format($_SESSION['reservation_priesc']['periodPrice'], 2) ?>
                    </p>
                    <p class="flex justify-between">
                        <span class="font-medium">Room Price Per Night:</span>
                        $<?= number_format($_SESSION['reservation_priesc']['roomPricePerNight'], 2) ?>
                    </p>
                    <p class="flex justify-between">
                        <span class="font-medium">Total Room Price:</span>
                        $<?= number_format($_SESSION['reservation_priesc']['totalRoomPrice'], 2) ?>
                    </p>


                    <p class="flex justify-between text-red-600">
                        <span class="font-medium">Discount:</span>
                        -$<?= number_format($_SESSION['reservation_priesc']['discountValue'], 2) ?>
                    </p>
                    <hr class="my-2">
                    <p class="flex justify-between text-lg font-bold">
                        <span>Total Price:</span>
                        $<?= number_format($_SESSION['reservation_priesc']['totalPrice'], 2) ?>
                    </p>
                </div>
            </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Customer Information -->
            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-blue-600 mb-3">Customer Information</h2>
                <p class=""><span class="font-medium">Name:</span> <?= htmlspecialchars($reservationData['CustomerName']) ?></p>
                <p class=""><span class="font-medium">Email:</span> <?= htmlspecialchars($reservationData['CustomerEmail']) ?></p>
            </div>

            <!-- Trip Details -->
            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-blue-600 mb-3">Trip Details</h2>
                <p class=""><span class="font-medium">Agency:</span> <?= htmlspecialchars($reservationData['AgencyName']) ?></p>
                <p class=""><span class="font-medium">Agency Address:</span> <?= htmlspecialchars($reservationData['AgencyAddress']) ?></p>
                <p class=""><span class="font-medium">Circuit:</span> <?= htmlspecialchars($reservationData['CircuitName']) ?></p>
                <p class=""><span class="font-medium">Dates:</span> 
                    <?= date('M j, Y', strtotime($reservationData['StartDate'])) ?> - 
                    <?= date('M j, Y', strtotime($reservationData['EndDate'])) ?>
                </p>
            </div>

            <!-- Accommodation Details -->
            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-blue-600 mb-3">Accommodation</h2>
                <p class=""><span class="font-medium">Type:</span> <?= htmlspecialchars($reservationData['RoomType']) ?></p>
                <p class=""><span class="font-medium">Name:</span> <?= htmlspecialchars($reservationData['AccommodationName']) ?></p>
                <p class=""><span class="font-medium">Address:</span> <?= htmlspecialchars($reservationData['AccommodationAddress']) ?></p>
            </div>

            <!-- Reservation Status -->
            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-blue-600 mb-3">Reservation Status</h2>
                <span class="px-4 py-2 rounded-full 
                    <?= $reservationData['Status'] === 'Confirmed' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' ?>">
                    <?= htmlspecialchars($reservationData['Status']) ?>
                </span>
                <p class=" mt-2"><span class="font-medium">Reservation Date:</span> 
                    <?= date('M j, Y H:i', strtotime($reservationData['ReservationDate'])) ?>
                </p>
            </div>
        </div>
    </section>
    <section class="p-3 flex justify-center opacity-0 translate-y-20 transition-all duration-1000 ease-out">
        <img src="../assets/undraw_transfer-money_h9s3.svg" alt="">
    </section>
    <!-- Payment Section -->
    <section class=" rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-white mb-6">Payment Information</h1>
        
        <div class="max-w-lg mx-auto">
            <!-- Payment Method Tabs -->
            <div class="flex mb-6 border-b">
                <button type="button" class="tab-button px-4 py-2 font-medium text-blue-600 border-b-2 border-blue-600" data-method="card">
                    Credit/Debit Card
                </button>
                <button type="button" class="tab-button px-4 py-2 font-medium text-white" data-method="cash">
                    Cash Payment
                </button>
            </div>

<!-- Card Payment Form -->
<form id="cardForm" class="payment-method" method="POST" action="">
    <input type="hidden" name="reservation_id" value="<?= $reservationId ?>">
    <input type="hidden" name="payment_method" value="card">

    <div class="space-y-4">
        <?php if($_SESSION["CustomerNationality"] == "Algeria"){?>
        <div>
            <label class="block text-sm font-medium text-white mb-1">Identity Number</label>
            <input type="text" name="identity_number" class="w-full px-3 py-2 text-black border rounded-md" 
                   maxlength="13" pattern="\d{13}" placeholder="1234567890123"
                   title="13-digit Algerian Identity Number" required
                   autocomplete="off">
        </div>
        <?php }else{  ?>
        <div>
            <label class="block text-sm font-medium text-white mb-1">Passport Number</label>
            <input type="text" name="identity_number" class="w-full px-3 py-2 text-black border rounded-md" 
                   maxlength="20" pattern="[A-Za-z0-9]{6,20}" placeholder="AB123456"
                   title="6-20 character passport number (letters and numbers)" required
                   autocomplete="off">
        </div>
        <?php } ?>

        <div>
            <label class="block text-sm font-medium text-white mb-1">Card Number</label>
            <input type="text" name="card_number" class="w-full px-3 py-2 text-black border rounded-md" 
                   maxlength="19" pattern="\d{4} \d{4} \d{4} \d{4}" placeholder="XXXX XXXX XXXX XXXX"
                   title="Enter a valid 16-digit card number (e.g., 1234 5678 9012 3456)" required
                   autocomplete="cc-number">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-white mb-1">Expiration Date</label>
                <input type="text" name="exp_date" class="w-full px-3 py-2 text-black border rounded-md" 
                       maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" placeholder="MM/YY"
                       title="Enter expiration date as MM/YY (e.g., 02/25)" required
                       autocomplete="cc-exp">
            </div>
            <div>
                <label class="block text-sm font-medium text-white mb-1">CVV/CVC</label>
                <input type="text" name="cvv" class="w-full px-3 py-2 text-black border rounded-md" 
                       placeholder="CVV/CVC" maxlength="4" pattern="\d{3,4}"
                       title="Enter the 3 or 4 digit CVV/CVC code" required
                       autocomplete="cc-csc">
            </div>
        </div>

        <button type="submit" class="w-full bg-primary font-semibold text-white py-2 px-4 rounded-md hover:bg-green-500 transition">
            Pay with Card
        </button>
    </div>
</form>
            <!-- Cash Payment Form -->
            <form id="cashForm" class="payment-method hidden" method="POST" action="">
                <input type="hidden" name="reservation_id" value="<?= $reservationId ?>">
                <input type="hidden" name="payment_method" value="cash">

                <div class="text-center p-6 border rounded-md bg-gray-50">
                    <p class="text-gray-700 mb-4">Please bring cash payment to our office location</p>
                    <button type="submit" class="bg-primary font-semibold text-white py-2 px-4 rounded-md hover:bg-green-700 transition">
                        Confirm Cash Payment
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

<script>
// Tab switching functionality
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', () => {
        // Remove active styles from all tabs
        document.querySelectorAll('.tab-button').forEach(tab => {
            tab.classList.remove('border-blue-600', 'text-blue-600');
            tab.classList.add('text-white');
        });

        // Add active styles to clicked tab
        button.classList.add('border-blue-600', 'text-blue-600');
        button.classList.remove('text-white');

        // Show corresponding form
        const method = button.dataset.method;
        document.querySelectorAll('.payment-method').forEach(form => {
            form.classList.toggle('hidden', form.id !== `${method}Form`);
        });
    });
});
</script>


<?php 
    include __DIR__ . "/../includes/footer.php";
?>