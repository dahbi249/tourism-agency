<?php
    $pageTitle = "Reset Password Page";
    include __DIR__ . '/../includes/header.php';
    require("../includes/connect_db.php");
    
    // Add error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        
        // Check token validity
        $stmt = mysqli_prepare($conn, "SELECT CustomerID, reset_expires FROM customer WHERE reset_token = ? AND reset_expires > NOW()");
        mysqli_stmt_bind_param($stmt, "s", $token);
        
        if (mysqli_stmt_execute($stmt)) {
            // Store result and bind variables
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $customerID, $reset_expires);
                mysqli_stmt_fetch($stmt);
                
                // Handle form submission
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $password = $_POST['password'];
                    $confirm = $_POST['confirm_password']; // Fixed field name
                    
                    if ($password !== $confirm) {
                        die("Passwords do not match");
                    }
    
                    // Update password
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = mysqli_prepare($conn, 
                        "UPDATE customer SET 
                         PasswordHash = ?, 
                         reset_token = NULL, 
                         reset_expires = NULL 
                         WHERE CustomerID = ?");
                    mysqli_stmt_bind_param($update_stmt, "si", $hash, $customerID);
                    
                    if (mysqli_stmt_execute($update_stmt)) {
                        echo "Password updated successfully! You can now <a href='login.php'>login</a>";
                    } else {
                        echo "Error updating password: " . mysqli_error($conn);
                    }
                }
            } else {
                die("Invalid or expired token. Please request a new reset link.");
            }
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    } else {
        die("No token provided");
    }
?>
<section class="">
  <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
          <img class="w-8 h-8 mr-2" src="../assets/LOGO-orange.png" alt="logo">
          JAWLA   
      </a>
      <div class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
          <h2 class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
              Change Password
          </h2>
          <form class="mt-4 space-y-4 lg:mt-5 md:space-y-5" action="" method="POST">
              
              <div>
                  <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New Password</label>
                  <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
              </div>
              <div>
                  <label for="confirm-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm password</label>
                  <input type="password" name="confirm_password" id="confirm-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
              </div>
              <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input id="newsletter" aria-describedby="newsletter" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800" required="">
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="newsletter" class="font-light text-gray-500 dark:text-gray-300">I accept the <a class="font-medium text-primary-600 hover:underline dark:text-primary-500" href="#">Terms and Conditions</a></label>
                  </div>
              </div>
              <button type="submit" class="w-full text-white bg-primary  focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Reset passwod</button>
          </form>
      </div>
  </div>
</section>
<?php 
    include __DIR__ . "/../includes/footer.php";
?>