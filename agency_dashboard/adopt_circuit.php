<?php
$pageTitle = "Adopt Circuit";
include __DIR__ . '/../includes/header.php';
require "../includes/connect_db.php";


$agencyID = $_SESSION['AgencyID'];

// Get all available circuits
$circuitStmt = mysqli_prepare($conn, "SELECT * FROM circuit WHERE IsValidated = 1");
mysqli_stmt_execute($circuitStmt);
$circuits = mysqli_fetch_all(mysqli_stmt_get_result($circuitStmt), MYSQLI_ASSOC);

// Get agency's active accommodations
$accommodationStmt = mysqli_prepare($conn, "
    SELECT a.AccommodationID, a.Name 
    FROM accommodationcontract ac
    JOIN accommodation a ON ac.AccommodationID = a.AccommodationID
    WHERE ac.AgencyID = ? AND ac.Status = 'active'
");
mysqli_stmt_bind_param($accommodationStmt, "i", $agencyID);
mysqli_stmt_execute($accommodationStmt);
$accommodations = mysqli_fetch_all(mysqli_stmt_get_result($accommodationStmt), MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_adoption'])) {
    try {
        mysqli_begin_transaction($conn);

        // Get form data
        $circuitID = $_POST['circuit'];
        $selectedPeriods = $_POST['periods'] ?? [];
        $selectedAccommodations = $_POST['accommodations'] ?? [];
        $discountType = $_POST['discount_type'] ?? null;
        $discountValue = $_POST['discount_value'] ?? 0;

        // Process each selected period
        foreach ($selectedPeriods as $period) {
            list($startDate, $endDate) = explode('|', $period);

            // Insert into agency_circuit
            $insertCircuit = mysqli_prepare($conn, "
                INSERT INTO agency_circuit (AgencyID, CircuitID, StartDate, EndDate)
                VALUES (?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param($insertCircuit, "iiss", $agencyID, $circuitID, $startDate, $endDate);
            mysqli_stmt_execute($insertCircuit);

            // Insert accommodations for this period
            foreach ($selectedAccommodations as $accID) {
                $insertAccommodation = mysqli_prepare($conn, "
                    INSERT INTO agency_circuit_accommodation (AgencyID, CircuitID, StartDate, AccommodationID)
                    VALUES (?, ?, ?, ?)
                ");
                mysqli_stmt_bind_param($insertAccommodation, "iisi", $agencyID, $circuitID, $startDate, $accID);
                mysqli_stmt_execute($insertAccommodation);
            }
        }

        // Insert discount if provided
        if ($discountType && $discountValue > 0) {
            $insertDiscount = mysqli_prepare($conn, "
                INSERT INTO discount (AgencyID, CircuitID, DiscountType, DiscountValue)
                VALUES (?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param($insertDiscount, "iisd", $agencyID, $circuitID, $discountType, $discountValue);
            mysqli_stmt_execute($insertDiscount);
        }

        mysqli_commit($conn);
        $_SESSION['success'] = "Circuit adopted successfully!";
        header("Location: http://localhost/tourism%20agency/agency_dashboard/adopted_circuits.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Error adopting circuit: " . $e->getMessage();
    }
}
?>

<!-- Adoption Form -->
<section class="opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <section class="mb-8 px-4 md:px-8 lg:px-20">
        <h2 class="my-5 text-2xl font-bold md:text-3xl">Adopt New Circuit</h2>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="flex flex-col gap-6 max-w-2xl mx-auto bg-white/10 p-6 rounded-xl">
            <!-- Circuit Selection -->
            <div class="w-full">
                <label class="block text-white text-lg mb-2 font-medium">Select Circuit</label>
                <select name="circuit" id="circuitSelect" required class="w-full p-3 rounded-lg text-gray-900">
                    <option value="">Select a Circuit</option>
                    <?php foreach ($circuits as $circuit): ?>
                        <option value="<?= $circuit['CircuitID'] ?>"><?= htmlspecialchars($circuit['Name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Period Selection -->
            <div class="w-full" id="periodSection" style="display: none;">
                <label class="block text-white text-lg mb-2 font-medium">Select Periods</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="periodsContainer">
                    <!-- Periods will be loaded here -->
                </div>
            </div>

            <!-- Accommodation Selection -->
            <div class="w-full">
                <label class="block text-white text-lg mb-2 font-medium">Select Accommodations</label>
                <div class="grid grid-cols-2 gap-4">
                    <?php foreach ($accommodations as $acc): ?>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="accommodations[]" value="<?= $acc['AccommodationID'] ?>">
                            <span><?= htmlspecialchars($acc['Name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Discount Section -->
            <div class="w-full bg-gray-800/80 p-6 rounded-xl">
                <h3 class="text-white text-xl font-bold mb-4">Add Discount (Optional)</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <select name="discount_type" class="p-3 rounded-lg text-gray-900">
                        <option value="">Select Discount Type</option>
                        <option value="percentage">Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                    <input type="number" name="discount_value" step="0.01" 
                           placeholder="Discount Value" 
                           class="p-3 rounded-lg text-gray-900">
                </div>
            </div>

            <button type="submit" name="submit_adoption" 
                    class="w-full bg-primary text-white py-4 rounded-xl hover:bg-primary-dark">
                Adopt Circuit
            </button>
        </form>
    </section>
</section>

<script>
// Add agency ID to your HTML
const agencyID = <?= json_encode($_SESSION['AgencyID'] ?? 0) ?>;

document.getElementById('circuitSelect').addEventListener('change', function() {
    const circuitID = this.value;
    const periodSection = document.getElementById('periodSection');
    const periodsContainer = document.getElementById('periodsContainer');

    if (!circuitID || !agencyID) {
        periodSection.style.display = 'none';
        return;
    }

    // Fetch periods including agency ID
    fetch(`get_periods.php?circuit_id=${circuitID}&agency_id=${agencyID}`)
        .then(response => response.json())
        .then(periods => {
            if (periods.length === 0) {
                periodsContainer.innerHTML = `
                    <div class="col-span-full text-center py-4 text-gray-400">
                        No available periods for this circuit
                    </div>
                `;
            } else {
                periodsContainer.innerHTML = periods.map(period => `
                    <label class="flex items-center space-x-2 p-3 bg-white/5 rounded-lg">
                        <input type="checkbox" name="periods[]" 
                               value="${period.start}|${period.end}"
                               class="form-checkbox">
                        <span>
                            ${new Date(period.start).toLocaleDateString()} - 
                            ${new Date(period.end).toLocaleDateString()}
                            (${period.price} DZD)
                        </span>
                    </label>
                `).join('');
            }
            
            periodSection.style.display = 'block';
        })
        .catch(error => {
            console.error('Error loading periods:', error);
            periodsContainer.innerHTML = `
                <div class="col-span-full text-center py-4 text-red-400">
                    Error loading periods
                </div>
            `;
        });
});
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>