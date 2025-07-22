<?php
$pageTitle = "Register page";
require("../includes/connect_db.php");
include("../includes/header.php");



// Function to fetch data from REST Countries API using cURL
function fetchCountryNames()
{
    $url = 'https://restcountries.com/v3.1/all?fields=name'; // Only fetch the name field
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        // Log the error or handle it gracefully, but don't stop the page load
        error_log('cURL error fetching countries: ' . curl_error($ch));
        return []; // Return an empty array on error
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        error_log("API request failed with status code: {$httpCode} for URL: {$url}");
        return []; // Return an empty array on non-200 status
    }
    curl_close($ch);
    $countriesData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error for countries: " . json_last_error_msg());
        return []; // Return an empty array on JSON decode error
    }
    $countryNames = [];
    foreach ($countriesData as $country) {
        if (isset($country['name']['common'])) {
            $countryNames[] = $country['name']['common'];
        }
    }
    // Sort the country names alphabetically for better user experience
    sort($countryNames);
    return $countryNames;
}
// Fetch the list of countries when the page loads
$countries = fetchCountryNames();



// Initialize error array
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize inputs
    $username = trim($_POST["username"] ?? '');
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST["phone"] ?? '');
    $Nationality = trim($_POST["Nationality"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirmPassword = $_POST["ConfirmPassword"] ?? '';

    // Basic validation
    if (empty($username)) $errors[] = "Username is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone) || !preg_match("/^\+?[0-9\s\-]+$/", $phone)) $errors[] = "Valid phone number is required";
    if (empty($Nationality)) $errors[] = "Nationality is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirmPassword) $errors[] = "Passwords do not match";

    // Password strength validation
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    if (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one number";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT CustomerID FROM Customer WHERE Email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Email already registered";
        }
        mysqli_stmt_close($stmt);
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Using transactions for safer database operations
        mysqli_begin_transaction($conn);

        try {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO Customer (Name, Email, Phone, Nationality, PasswordHash) 
                VALUES (?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $phone, $Nationality, $passwordHash);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_commit($conn);

                // Set session variables
                $_SESSION["CustomerID"] = mysqli_insert_id($conn);
                $_SESSION["CustomerName"] = $username;
                $_SESSION["CustomerEmail"] = $email;
                $_SESSION["CustomerPhone"] = $phone;
                $_SESSION["CustomerNationality"] = $Nationality;
                $_SESSION["CustomerProfilePhoto"] = NULL;
                $_SESSION["CustomerRole"] = 'customer';

                // Redirect with success message
                $_SESSION["success"] = "Registration successful!";
                header("Location: http://localhost/tourism%20agency/main/");
                exit();
            } else {
                throw new Exception("Database error: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = "Registration failed. Please try again.";
        } finally {
            mysqli_stmt_close($stmt);
        }
    }
}
mysqli_close($conn);

?>



<main class="flex flex-col items-center lg:justify-center lg:flex-row px-5 pb-3">
    <div class=" w-80 lg:w-1/2 mt-3"><img src="../assets/undraw_login_weas.svg" alt=""></div>


    <section class="flex flex-col items-center mx-auto mt-28">
        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div>
            <h1 class="text-[35px] md:text-[40px] lg:text-[48px] font-bold mb-6"><?= htmlspecialchars($lang["Register"]) ?></h1>
        </div>
        <div>
            <form action="" method="POST" class="flex flex-col items-center">

                <!-- Username -->
                <input type="text" name="username" id="username"
                    value="<?= htmlspecialchars($username ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Username"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 bg-gray-200 text-gray-900">

                <!-- Email -->
                <input type="email" name="email" id="email"
                    value="<?= htmlspecialchars($email ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Email"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 bg-gray-200 text-gray-900">

                <!-- Phone -->
                <input type="text" name="phone" id="phone"
                    value="<?= htmlspecialchars($phone ?? '') ?>"
                    placeholder="<?= htmlspecialchars($lang["Phone"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 bg-gray-200 text-gray-900">
                <!-- Nationality -->
                <select name="Nationality" id="Nationality"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 bg-gray-200 text-gray-900">
                    <option value="" disabled selected><?= htmlspecialchars($lang["Select Nationality"]) ?></option>
                    <?php foreach ($countries as $countryName): ?>
                        <option value="<?= htmlspecialchars($countryName) ?>"
                            <?= (isset($Nationality) && $Nationality === $countryName) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($countryName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Password -->
                <input type="password" name="password" id="password"
                    placeholder="<?= htmlspecialchars($lang["Password"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 bg-gray-200 text-gray-900">

                <!-- Confirm Password -->
                <input type="password" name="ConfirmPassword" id="ConfirmPassword"
                    placeholder="<?= htmlspecialchars($lang["ConfirmPassword"]) ?>"
                    class="w-[300px] px-1 h-[55px] md:w-[390px] md:px-3 lg:w-[537px] lg:h-[67px] rounded-[10px] lg:px-5 lg:text-[24px] outline-none mb-12 bg-gray-200 text-gray-900">
                <div class="w-[300px] md:w-[390px] lg:w-[537px] mb-4">
                    <div id="password-strength-bar" class="h-1 bg-gray-300 rounded transition-all"></div>
                    <small id="password-strength-text" class="text-sm text-gray-700"></small>
                </div>
                <button type="submit" name="submit"
                    class="text-white w-[200px] md:w-[228.04px] lg:w-[314px] h-[48px] bg-primary rounded-[10px] text-[24px] font-semibold mb-4">
                    <?= htmlspecialchars($lang["Register"]) ?>
                </button>
            </form>
        </div>
        <div class="text-center mb-10">
            <p><?= htmlspecialchars($lang["You have an account?"]) ?> <a href="http://localhost/tourism%20agency/auth/login.php" class="text-blue-500"><?= htmlspecialchars($lang["Login"]) ?></a></p>
        </div>

    </section>
</main>
<!-- Add password strength indicator script -->
<script>
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');

        // Reset classes
        strengthBar.className = 'h-1 rounded transition-all';

        // Calculate strength
        let strength = 0;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^A-Za-z0-9]/)) strength++;
        if (password.length >= 8) strength++;

        // Update display
        const strengthLevels = [{
                width: '20%',
                color: 'bg-red-500',
                text: 'Very Weak'
            },
            {
                width: '40%',
                color: 'bg-orange-500',
                text: 'Weak'
            },
            {
                width: '60%',
                color: 'bg-yellow-500',
                text: 'Medium'
            },
            {
                width: '80%',
                color: 'bg-green-500',
                text: 'Strong'
            },
            {
                width: '100%',
                color: 'bg-green-700',
                text: 'Very Strong'
            }
        ];

        const level = Math.min(strength, 4);
        strengthBar.style.width = strengthLevels[level].width;
        strengthBar.classList.add(strengthLevels[level].color);
        strengthText.textContent = strengthLevels[level].text;
    });
</script>