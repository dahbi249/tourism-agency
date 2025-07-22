<?php
$pageTitle = "Contact Us Page";
include __DIR__ . '/../includes/header.php';
require("../includes/connect_db.php");
include("../includes/send_mailer.php");
$stmt = mysqli_prepare($conn, "SELECT * FROM customer WHERE Role = 'super_admin'");
mysqli_stmt_execute($stmt);
$rowSuperAdmin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST["subject"] ?? '');
    $comment = trim($_POST["comment"] ?? '');
    if (empty($subject)) $errors[] = "subject is required";
    if (empty($comment)) $errors[] = "comment is required";
    if (empty($errors)) {
        if (isset($_SESSION["CustomerID"])) {
            if (!sendEmail('dahbiabdou249@gmail.com', $_SESSION["CustomerName"], $_SESSION["CustomerPhone"], $subject, $comment, $_SESSION["CustomerEmail"])) {
                echo "error";
            }
             header("Location: contactTHNX.php");
        }
    }
}
?>
<section class="flex flex-col items-center lg:flex-row lg:items-start lg:justify-center gap-5 px-10">
    <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
        <h1 class="mb-4 text-4xl tracking-tight font-extrabold  text-center">Our Contact Information</h1>
        <div class=" text-center mb-5">
            <p class="mb-5">Our Sociel Media</p>
            <div class="text-4xl mb-10">
                <a href=""><i class='bx bxl-facebook-circle'></i></a>
                <a href=""><i class='bx bxl-instagram-alt' ></i></a>
                <a href=""><i class='bx bxl-whatsapp' ></i></a>
            </div>

        </div>
        <div >
            <p class="mb-10"><span>Email: </span><?= $rowSuperAdmin["Email"] ?></p>
            <p><span>Phone: </span><?= $rowSuperAdmin["Phone"] ?></p>
        </div>
    </div>
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <p class="font-medium"><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['CustomerID'])): ?>
        <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-center ">Contact Us</h2>
            <p class="mb-8 lg:mb-16 font-light text-center text-gray-500 dark:text-gray-400 sm:text-xl">Got a technical issue? Want to send feedback about a beta feature? Need details about our Business plan? Let us know.</p>
            <?php if (!isset($_SESSION['CustomerID'])): ?>
                <div class="text-center mb-4 text-red-500">
                    <?php echo $lang["login_to_reserve"]; ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST" class="space-y-8">
                <div>
                    <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Subject</label>
                    <input type="text" id="subject" name="subject" class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light" placeholder="Let us know how we can help you" required>
                </div>
                <div class="sm:col-span-2">
                    <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Your message</label>
                    <textarea id="message" rows="6" name="comment" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Leave a comment..."></textarea>
                </div>
                <button type="submit" class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">Send message</button>
            </form>
        </div>
    <?php else: ?>
        <div class="text-center py-4">
            <a href="http://localhost/tourism%20agency/auth/login.php" class="text-primary underline"><?php echo $lang["login_to_reserve_prompt"]; ?></a>
        </div>
    <?php endif; ?>
</section>
<?php
include __DIR__ . "/../includes/footer.php";
?>