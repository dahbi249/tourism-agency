<?php
$pageTitle = "Add Accommodation Contract";
include __DIR__ . '/../includes/header.php';
require "../includes/connect_db.php";



$agencyID = $_SESSION['AgencyID'];
$error = "";

// Get accommodations without existing contracts
$stmt = mysqli_prepare($conn, "
    SELECT a.* 
    FROM accommodation a
    LEFT JOIN accommodationcontract ac 
        ON a.AccommodationID = ac.AccommodationID 
        AND ac.AgencyID = ?
    WHERE ac.AccommodationID IS NULL
    ORDER BY a.Name
");
mysqli_stmt_bind_param($stmt, "i", $agencyID);
mysqli_stmt_execute($stmt);
$accommodations = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contract'])) {
    try {
        $accommodationID = $_POST['accommodation'];
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $availableRooms = (int)$_POST['available_rooms'];

        // Validate input
        if (empty($accommodationID) || empty($startDate) || empty($endDate)) {
            throw new Exception("All fields are required");
        }

        if ($availableRooms <= 0) {
            throw new Exception("Available rooms must be greater than 0");
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            throw new Exception("End date must be after start date");
        }

        mysqli_begin_transaction($conn);

        // Insert new contract
        $stmt = mysqli_prepare($conn, "
            INSERT INTO accommodationcontract 
                (AgencyID, AccommodationID, StartDate, EndDate, AvailableRooms)
            VALUES (?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, "iissi", 
            $agencyID, 
            $accommodationID, 
            $startDate, 
            $endDate, 
            $availableRooms
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to create contract: " . mysqli_error($conn));
        }

        mysqli_commit($conn);
        $_SESSION['success'] = "Contract created successfully!";
        header("Location: http://localhost/tourism%20agency/agency_dashboard/contract_accommodation.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}
?>

<!-- Add Contract Form -->
<section class="opacity-0 translate-y-20 transition-all duration-1000 ease-out">
    <section class="mb-8 px-4 md:px-8 lg:px-20">
        <h2 class="my-5 text-2xl font-bold md:text-3xl">Create New Accommodation Contract</h2>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="flex flex-col gap-6 max-w-2xl mx-auto bg-white/10 p-6 rounded-xl">
            <!-- Accommodation Selection -->
            <div class="w-full">
                <label class="block text-white text-lg mb-2 font-medium">Select Accommodation</label>
                <select name="accommodation" required class="w-full p-3 rounded-lg text-gray-900">
                    <?php if (count($accommodations) > 0): ?>
                        <?php foreach ($accommodations as $acc): ?>
                            <option value="<?= $acc['AccommodationID'] ?>">
                                <?= htmlspecialchars($acc['Name']) ?> - 
                                <?= htmlspecialchars($acc['City']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No available accommodations</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Contract Dates -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-white text-lg mb-2 font-medium">Start Date</label>
                    <input type="date" name="start_date" required 
                           class="w-full p-3 rounded-lg text-gray-900">
                </div>
                <div>
                    <label class="block text-white text-lg mb-2 font-medium">End Date</label>
                    <input type="date" name="end_date" required 
                           class="w-full p-3 rounded-lg text-gray-900">
                </div>
            </div>

            <!-- Available Rooms -->
            <div class="w-full">
                <label class="block text-white text-lg mb-2 font-medium">Available Rooms</label>
                <input type="number" name="available_rooms" min="1" required 
                       class="w-full p-3 rounded-lg text-gray-900">
            </div>

            <button type="submit" name="submit_contract" 
                    class="w-full bg-primary text-white py-4 rounded-xl hover:bg-primary-dark">
                Create Contract
            </button>
        </form>
    </section>
</section>

<?php include __DIR__ . "/../includes/footer.php"; ?>