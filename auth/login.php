<?php
$pageTitle = "Login page";
include("../includes/header.php");
require("../includes/connect_db.php");
// Initialize error array
$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize inputs
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"] ?? '';

    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM Customer WHERE Email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $row["PasswordHash"])) {
                    // Check if account is banned
                    if ($row['banned']) {
                        $errors[] = "Your account has been banned. Please contact support.";
                    } else {
                        // Regenerate session ID to prevent fixation
                        session_regenerate_id(true);

                        // Set session variables
                        $_SESSION["CustomerID"] = $row["CustomerID"];
                        $_SESSION["CustomerName"] = $row["Name"];
                        $_SESSION["CustomerEmail"] = $row["Email"];
                        $_SESSION["CustomerPhone"] = $row["Phone"];
                        $_SESSION["CustomerNationality"] = $row["Nationality"];
                        $_SESSION["CustomerProfilePhoto"] = $row["ProfilePhoto"];
                        $_SESSION["CustomerRole"] = $row["Role"];
                        $_SESSION["success"] = "Login successful!";
                        if (isset($_SESSION['redirect_url'])) {
                            $redirect_url = $_SESSION['redirect_url'];
                            unset($_SESSION['redirect_url']);
                            header("Location: $redirect_url");
                            exit();
                        } else {
                            header("Location: http://localhost/tourism%20agency/main/");
                            exit();
                        }
                    }
                } else {
                    $errors[] = "Invalid email or password";
                }
            } else {
                $errors[] = "Invalid email or password";
            }
        } else {
            $errors[] = "Database error. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);


?>
<section class="flex flex-col items-center lg:justify-center lg:flex-row px-5 pb-3">
    <div class=" w-80 lg:w-1/2 mt-3"><img src="../assets/undraw_login_weas.svg" alt=""></div>
    <section class="flex flex-col items-center lg:w-1/2 mt-3">
        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded w-full max-w-md">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div>
            <h1 class="text-[35px] md:text-[40px] lg:text-[48px] font-bold mb-6">
                <?= htmlspecialchars($lang["Login"]) ?>
            </h1>
        </div>
        <div class="w-full max-w-md">
            <form action="" method="POST" class="flex flex-col items-center">
                <!-- Email Input -->
                <input type="email" name="email" id="email"
                    value="<?= htmlspecialchars($email ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Email"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 bg-gray-300 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] mb-12 text-gray-900"
                    autocomplete="email" required>

                <!-- Password Input -->
                <input type="password" name="password" id="password"
                    placeholder="<?= htmlspecialchars($lang["Password"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] bg-gray-300 rounded-[10px] lg:px-5 lg:text-[24px] mb-12 text-gray-900"
                    autocomplete="current-password" required>

                <!-- Submit Button -->
                <button type="submit" name="submit"
                    class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">
                    <?= htmlspecialchars($lang["Login"]) ?>
                </button>
            </form>
        </div>
        <div class="text-center">
            <a href="http://localhost/tourism%20agency/other/forgot_password.php" class="text-blue-500 hover:text-blue-700">
                <?= htmlspecialchars($lang["register now"]) ?>
            </a>
        </div>
        <div class="text-center">
            <p><?= htmlspecialchars($lang["Don't have an account?"]) ?>
                <a href="http://localhost/tourism%20agency/auth/register.php" class="text-blue-500 hover:text-blue-700">
                    <?= htmlspecialchars($lang["register now"]) ?>
                </a>
            </p>
        </div>
    </section>
</section>