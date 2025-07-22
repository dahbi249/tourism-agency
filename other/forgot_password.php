<?php
$pageTitle = "Forgot Password Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendEmail($to, $subject, $message, $headers){
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP(); 
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dahbiabdou249@gmail.com';
        $mail->Password   = 'iqzu fplb kver erop';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($headers, 'JAWLA');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "
            <p>$message</p>
        ";
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}



$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    if (empty($email)) $errors[] = "email is required";
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT CustomerID FROM customer WHERE Email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $token = bin2hex(random_bytes(16));
                $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

                $stmt = mysqli_prepare($conn, "UPDATE customer SET reset_token = ?, reset_expires = ? WHERE Email = ?");
                mysqli_stmt_bind_param($stmt, "sss", $token, $expires, $email);
                mysqli_stmt_execute($stmt);
                // Send reset email
                $resetLink = "http://localhost/tourism%20agency/other/reset_password.php?token=$token";
                $to = $email;
                $subject = "Password Reset Request";
                $message = "Click this link to reset your password: $resetLink";
                $headers = "dahbiabdou249@gmail.com";
                if (sendEmail($to, $subject, $message, $headers)) {
                    echo "<script>alert('Reset link sent to your email')</script>";
                } else {
                    echo "<script>alert('Error sending email')</script>";
                }

            } else {
                
                echo  "Email not found";
            }
        }
    }
}
?>
<section class="">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-0">
        <a href="#" class="flex items-center my-10 text-4xl font-semibold ">
            <img class=" w-16 mr-2" src="../assets/LOGO-orange.png" alt="logo">
            JAWLA
        </a>
        <div class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
            <h2 class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                Change Password
            </h2>
            <form class="mt-4 space-y-4 lg:mt-5 md:space-y-5" action="" method="POST">
                <div>
                    <label for="email" class="block mb-2 text-lg font-medium text-gray-900 dark:text-white">Your email</label>
                    <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@company.com" required="">
                </div>

                <button type="submit" class="w-full text-[24px] font-semibold text-white bg-primary  focus:ring-4 focus:outline-none focus:ring-primary-300  rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Submit</button>
            </form>
        </div>
    </div>
</section>
<?php
include __DIR__ . "/../includes/footer.php";
?>