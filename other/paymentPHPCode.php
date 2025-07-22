<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendEmail($fromEmail, $name, $subject, $toEmail, $comment)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dahbiabdou249@gmail.com';
        $mail->Password   = 'iqzu fplb kver erop';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($fromEmail, 'JAWLA');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "
        <p>Thank you $name for Your Reservation!</p>
        <p>$comment</p>
        ";
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
try {
    if (($_SERVER["REQUEST_METHOD"] === "POST")) {
        // Validate input
        if (!isset($_POST['payment_method'])) {
            throw new Exception("No payment method selected");
        }
        $paymentMethod = $_POST['payment_method'];


        // Simulate random card balance (between 1000DA and 50000DA)
        $cardBalance = mt_rand(100000, 5000000) / 100; // Stored as decimal in DB

        // Process payment
        if ($paymentMethod === 'card') {
            // Validate card details
            $required = ['identity_number', 'card_number', 'exp_date', 'cvv'];
            foreach ($required as $field) {
                $value = trim($_POST[$field] ?? '');
                if (empty($value)) {
                    throw new Exception("Missing card details: " . ucfirst($field));
                }
            }


            // Get total price in DA
            $totalPriceDA = $_SESSION['reservation_priesc']['totalPrice'];

            // Generate weighted random balance in DA
            if (mt_rand(1, 100) <= 80) {
                // 80% chance: sufficient balance (totalPriceDA to 2x totalPriceDA)
                $cardBalance = mt_rand(
                    $totalPriceDA * 100,          // Convert to cents
                    ($totalPriceDA * 2) * 100
                ) / 100;
            } else {
                // 20% chance: insufficient balance (100.00 DA to totalPriceDA - 0.01 DA)
                $cardBalance = mt_rand(
                    10000,                       // 100.00 DA minimum
                    ($totalPriceDA * 100) - 1     // Max 1 cent below total price
                ) / 100;
            }

            // Validate balance
            if ($paymentMethod === 'card' && $cardBalance < $totalPriceDA) {
                throw new Exception("Insufficient funds. Your balance: " .
                    number_format($cardBalance, 2) . " DA | Needed: " .
                    number_format($totalPriceDA, 2) . " DA");
            }


            $paymentStatus = 'Completed';
            $transactionId = 'CARD-' . bin2hex(random_bytes(4));
        } else {
            // Cash payment
            $paymentStatus = 'Pending';
            $transactionId = 'CASH-' . date('YmdHis');
        }

        // Start transaction
        mysqli_begin_transaction($conn);

        // Insert payment
        $insertPayment = "INSERT INTO payment (
            ReservationID, 
            Amount, 
            PaymentMethod, 
            PaymentStatus, 
            TransactionID
        ) VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $insertPayment);
        mysqli_stmt_bind_param(
            $stmt,
            "idsss",
            $reservationId,
            $_SESSION['reservation_priesc']['totalPrice'],
            $paymentMethod,
            $paymentStatus,
            $transactionId
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Payment processing failed");
        }
        if ($paymentMethod === 'card') {
            // Update reservation status
            $updateReservation = "UPDATE reservation SET Status = 'Confirmed' WHERE ReservationID = ?";
            $stmt = mysqli_prepare($conn, $updateReservation);
            mysqli_stmt_bind_param($stmt, "i", $reservationId);
            mysqli_stmt_execute($stmt);
        }
        // Commit transaction
        mysqli_commit($conn);

        // Success message with simulated balance
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => "Payment processed successfully!" .
                ($paymentMethod === 'card' ?
                    " Card balance: $" . number_format($cardBalance, 2) :
                    " Please bring cash to our office")
        ];
        if ($paymentMethod === 'card') {
            $subject = "JAWLA, Payment done!";
            $comment = "";
        } else {
            $subject = "JAWLA, Reservation Done!";
            $comment = "Please bring cash payment to our agency's office location";
        }

        if (!sendEmail('dahbiabdou249@gmail.com', $_SESSION["CustomerName"], $subject, $_SESSION["CustomerEmail"], $comment)) {
            echo "error sending email";
        }
        header("Location: ../other/paymentSuccess.php?ReservationID=" . $reservationId);
        exit();
    }
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => $e->getMessage()
    ];
    //header("Location: ../other/paymentFaild.php?ReservationID=" . ($reservationId ?? ''));
    //exit();
}
