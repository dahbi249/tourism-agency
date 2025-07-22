<?php
require "../includes/connect_db.php";


// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch data for reservation form (as in the previous correct version)
$circuitId = $_GET['CircuitID'] ?? null;





// Get agencies offering this circuit with potential discounts
$agencyStmt = mysqli_prepare($conn, "
    SELECT
        a.AgencyID,
        a.Name,
        d.DiscountType,
        d.DiscountValue
    FROM agency_circuit ac
    JOIN agency a ON ac.AgencyID = a.AgencyID
    LEFT JOIN discount d ON a.AgencyID = d.AgencyID AND d.CircuitID = ?
    WHERE ac.CircuitID = ?
    AND ac.IsActive = 1
    AND a.Status = 'active'
");
mysqli_stmt_bind_param($agencyStmt, "ii", $circuitId, $circuitId);
mysqli_stmt_execute($agencyStmt);
$agencies = mysqli_fetch_all(mysqli_stmt_get_result($agencyStmt), MYSQLI_ASSOC);

// Get circuit periods
$periodStmt = mysqli_prepare($conn, "
    SELECT
        cp.StartDate AS period_start,
        cp.EndDate AS period_end,
        cp.BasePrice,
        cp.availablePlaces
    FROM circuitperiod cp
    WHERE cp.CircuitID = ?
    ORDER BY cp.StartDate
");
mysqli_stmt_bind_param($periodStmt, "i", $circuitId);
mysqli_stmt_execute($periodStmt);
$periods = mysqli_fetch_all(mysqli_stmt_get_result($periodStmt), MYSQLI_ASSOC);

// Calculate total duration
$durationStmt = mysqli_prepare($conn, "
    SELECT SUM(StayDuration) AS TotalDuration
    FROM circuitlocation
    WHERE CircuitID = ?
");
mysqli_stmt_bind_param($durationStmt, "i", $circuitId);
mysqli_stmt_execute($durationStmt);
$totalDuration = mysqli_fetch_assoc(mysqli_stmt_get_result($durationStmt))['TotalDuration'] ?? 0;

// Add validation functions
function validateReservationData($data)
{
    $errors = [];

    if (empty($data['agency'])) {
        $errors[] = "Please select an agency";
    }
    if (empty($data['accommodation'])) {
        $errors[] = "Please select an accommodation";
    }
    if (empty($data['room_type'])) {
        $errors[] = "Please select a room type";
    }

    if (empty($data['num_adults']) || $data['num_adults'] < 1) {
        $errors[] = "At least one adult is required";
    }
    if (!isset($data['num_children']) || $data['num_children'] < 0) {
        $errors[] = "Number of children cannot be negative";
    }

    return $errors;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reservation'])) {
    if (!isset($_SESSION['CustomerID'])) {
        $error = "You must be logged in to make a reservation.";
    } else {
        $validationErrors = validateReservationData($_POST);

        if (empty($validationErrors)) {
            try {
                // Start transaction
                mysqli_begin_transaction($conn);

                $customerID = $_SESSION['CustomerID'];
                $agencyID = $_POST['agency'];
                // Extract startDate and endDate from the selected period value
                $selectedPeriod = $_POST['period'];
                list($startDate, $endDate) = explode('|', $selectedPeriod);
                $numAdults = $_POST['num_adults'];
                $numChildren = $_POST['num_children'] ?? 0;
                $roomTypeID = $_POST['room_type'];
                $requestedPlaces = $numAdults + ceil($numChildren * 0.5);

                // 1. Get room price
                $roomQuery = mysqli_prepare($conn, "SELECT PricePerNight FROM roomtype WHERE RoomTypeID = ?");
                mysqli_stmt_bind_param($roomQuery, "i", $roomTypeID);
                mysqli_stmt_execute($roomQuery);
                $roomPrice = mysqli_fetch_assoc(mysqli_stmt_get_result($roomQuery))['PricePerNight'] ?? 0;

                // In your reservation processing code, replace the period validation with:
                $selectedPeriod = $_POST['period'] ?? '';
                [$startDate, $endDate] = explode('|', $selectedPeriod);
                if (empty($startDate) || empty($endDate)) {
                    die("Invalid period selection");
                }
                // Validate period format
                if (empty($startDate) || empty($endDate) || !strtotime($startDate) || !strtotime($endDate)) {
                    $errors[] = "Invalid period format";
                } else {
                    // Check agency-period relationship
                    $periodCheck = mysqli_prepare($conn, "
        SELECT 1 
        FROM agency_circuit 
        WHERE AgencyID = ?
        AND CircuitID = ?
        AND StartDate = ?
        AND EndDate = ?
        AND IsActive = 1
    ");

                    mysqli_stmt_bind_param(
                        $periodCheck,
                        "iiss",
                        $_POST['agency'],
                        $circuitId,
                        $startDate,
                        $endDate
                    );

                    mysqli_stmt_execute($periodCheck);

                    if (mysqli_num_rows(mysqli_stmt_get_result($periodCheck)) === 0) {
                        $errors[] = "Selected period is not available for this agency";
                    }
                }
                // 2. Get period base price
                // Get price for selected agency period
                $priceQuery = mysqli_prepare($conn, "
    SELECT cp.BasePrice 
    FROM agency_circuit ac
    INNER JOIN circuitperiod cp 
        ON ac.CircuitID = cp.CircuitID 
        AND ac.StartDate = cp.StartDate
    WHERE ac.AgencyID = ?
    AND ac.CircuitID = ?
    AND ac.StartDate = ?
    AND ac.EndDate = ?
");

                mysqli_stmt_bind_param(
                    $priceQuery,
                    "iiss",
                    $agencyID,
                    $circuitId,
                    $startDate,
                    $endDate

                );

                mysqli_stmt_execute($priceQuery);
                $periodPrice = mysqli_fetch_assoc(mysqli_stmt_get_result($priceQuery))['BasePrice'] ?? 0;

                // 3. Calculate total room price
                $totalRoomPrice = $roomPrice * $totalDuration;

                // 4. Calculate base total
                $participants = $numAdults + ($numChildren * 0.5);
                $baseTotal = ($periodPrice + $totalRoomPrice) * $participants;

                // 5. Get active discounts
                // Corrected date range check (discount active during ANY part of the reservation period)
                $discountQuery = mysqli_prepare($conn, "
                    SELECT DiscountType, DiscountValue 
                    FROM discount 
                    WHERE AgencyID = ? 
                    AND CircuitID = ? 
                    ");
                // Bind $endDate first, then $startDate
                mysqli_stmt_bind_param($discountQuery, "ii", $agencyID, $circuitId);
                mysqli_stmt_execute($discountQuery);
                $discounts = mysqli_fetch_all(mysqli_stmt_get_result($discountQuery), MYSQLI_ASSOC);

                // 6. Apply discounts
                $totalPrice = $baseTotal;
                foreach ($discounts as $discount) {
                    if ($discount['DiscountType'] === 'percentage') {
                        $totalPrice *= (1 - ($discount['DiscountValue'] / 100));
                    } else {
                        $totalPrice -= $discount['DiscountValue'];
                    }
                }


                // Store in session
                $_SESSION['reservation_priesc'] = [
                    'periodPrice'     => $periodPrice,
                    'roomPricePerNight' => $roomPrice,
                    'totalRoomPrice'   => $totalRoomPrice,
                    'totalPrice'       => $totalPrice,
                    'discountValue'    => $baseTotal - $totalPrice
                ];



                // First check for overlapping reservations
                $checkOverlap = mysqli_prepare($conn, "
    SELECT ReservationID 
    FROM reservation 
    WHERE CircuitID = ? 
    AND (
        (StartDate <= ? AND EndDate >= ?) OR      -- Existing reservation overlaps with new dates
        (StartDate BETWEEN ? AND ?) OR            -- New reservation starts during existing
        (EndDate BETWEEN ? AND ?)                 -- New reservation ends during existing
    )
        AND CustomerID = ?
");
                mysqli_stmt_bind_param(
                    $checkOverlap,
                    "issssssi",
                    $circuitId,
                    $endDate,
                    $startDate,  // For first condition
                    $startDate,
                    $endDate,  // For second condition
                    $startDate,
                    $endDate,   // For third condition
                    $_SESSION["CustomerID"]
                );

                if (!mysqli_stmt_execute($checkOverlap)) {
                    throw new Exception("Availability check failed");
                }

                $result = mysqli_stmt_get_result($checkOverlap);

                if (mysqli_num_rows($result) > 0) {
                    throw new Exception("This circuit is already booked for the selected dates");
                }

                mysqli_stmt_close($checkOverlap);

                // First get total participants from the reservation
                $totalParticipants = $numAdults + $numChildren;

                // Start transaction
                mysqli_begin_transaction($conn);

                // 1. Lock and check availability
                $checkCapacity = mysqli_prepare($conn, "
        SELECT availablePlaces, MaxParticipants 
        FROM circuitperiod 
        WHERE CircuitID = ? AND StartDate = ?
        FOR UPDATE
    ");
                mysqli_stmt_bind_param($checkCapacity, "is", $circuitId, $startDate);
                mysqli_stmt_execute($checkCapacity);
                $capacityResult = mysqli_stmt_get_result($checkCapacity);
                $capacityData = mysqli_fetch_assoc($capacityResult);

                if (!$capacityData) {
                    throw new Exception("Invalid circuit period selected");
                }

                $availablePlaces = $capacityData['availablePlaces'];
                $maxParticipants = $capacityData['MaxParticipants'];

                // 2. Validate capacity
                if ($availablePlaces < $totalParticipants) {
                    throw new Exception("Not enough available places. Remaining: $availablePlaces");
                }

                // 3. Update available places
                $updatePlaces = mysqli_prepare($conn, "
        UPDATE circuitperiod 
        SET availablePlaces = availablePlaces - ?
        WHERE CircuitID = ? AND StartDate = ?
    ");
                mysqli_stmt_bind_param($updatePlaces, "iis", $totalParticipants, $circuitId, $startDate);
                mysqli_stmt_execute($updatePlaces);

                if (mysqli_affected_rows($conn) === 0) {
                    throw new Exception("Capacity update failed");
                }


                // Insert reservation
                $insertReservation = mysqli_prepare($conn, "
                    INSERT INTO reservation (
                        CustomerID, AgencyID, CircuitID,
                        StartDate,   EndDate, RoomTypeID, NumAdults, NumChildren, Status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
                ");
                mysqli_stmt_bind_param(
                    $insertReservation,
                    "iiissiii",
                    $customerID,
                    $agencyID,
                    $circuitId,
                    $startDate,
                    $endDate,
                    $roomTypeID,
                    $numAdults,
                    $numChildren
                );

                if (!mysqli_stmt_execute($insertReservation)) {
                    throw new Exception("Failed to create reservation");
                }

                $reservationID = mysqli_insert_id($conn);



                mysqli_commit($conn);
                header("Location: ../main/payment.php?ReservationID=" . urlencode($reservationID));
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = "Reservation failed: " . $e->getMessage();
            }
        } else {
            $error = implode("<br>", $validationErrors);
        }
    }
}
