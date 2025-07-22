<?php
ob_start();
session_start();
include __DIR__ . '/../lang/language.php';
$theme = $_COOKIE['theme'] ?? 'dark';
$theme_class = $theme === 'dark' ? 'bg-background-dark text-white' : 'bg-background-light text-black';
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>" dir="<?= $is_rtl ? 'rtl' : 'ltr' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Jawla | " . $pageTitle; ?></title>
    <link href="http://localhost/tourism%20agency/public/css/output.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<body class="<?= $theme_class ?> font-inter min-h-screen flex flex-col transition-colors duration-300 ease-in-out">



    <nav class="bg-primary text-white flex justify-between items-center px-5 py-2 text-[24px] font-semibold">

        <!---------------------------------------- Desktop Menu  ---------------------------------------------->

        <div class="flex justify-between items-center gap-4 md:gap-5 lg:gap-6">
            <div class="w-[61px] transition-all duration-1000 ease-out hover:scale-105"><a href="http://localhost/tourism%20agency/main/"><img src="../assets/logo.png" alt=""></a></div>
            <div>
                <ul class="hidden lg:flex justify-between items-center lg:gap-3">
                    <li class=""><a class="hover:text-[26px] transition-all duration-1000 ease-out" href="http://localhost/tourism%20agency/main/agencies.php"><?php echo $lang["Agencies"] ?></a></li>
                    <li class=""><a class="hover:text-[26px] transition-all duration-1000 ease-out" href="http://localhost/tourism%20agency/main/circuits.php"><?php echo $lang["circuits"] ?></a></li>
                    <li class=""><a class="hover:text-[26px] transition-all duration-1000 ease-out" href="http://localhost/tourism%20agency/main/locations.php"><?php echo $lang["locations"] ?></a></li>
                    <li class=""><a class="hover:text-[26px] transition-all duration-1000 ease-out" href="http://localhost/tourism%20agency/main/accommodations.php"><?php echo $lang["Accommodations"] ?></a></li>
                    <li class=""><a class="hover:text-[26px] transition-all duration-1000 ease-out" href="http://localhost/tourism%20agency/conversations/conversations.php"><?php echo $lang["conversations"] ?></a></li>
                </ul>
            </div>
        </div>
        <div class="hidden lg:flex justify-between items-center lg:gap-4">
            <?php if (isset($_SESSION["success"]) && !empty($_SESSION["success"])) { ?>
                <?php if ($_SESSION["CustomerRole"] == "super_admin") { ?>
                    <div class=" border-white border-2 rounded-md px-1 ">
                        <a href="http://localhost/tourism%20agency/dashboard/super_admin.php" class=" flex items-center text-center"><img class="w-8 rounded-full h-8" src="../media/profile_photo_url/<?php echo $_SESSION["CustomerProfilePhoto"] ?? 'default.svg'; ?>" alt="">
                            Admin Panel-<?php echo $_SESSION["CustomerName"]; ?></a>
                    </div>
                <?php } elseif ($_SESSION["CustomerRole"] == "agency_admin") {  ?>
                    <div class=" border-white border-2 rounded-md px-1 ">
                        <a href="http://localhost/tourism%20agency/agency_dashboard/agency_info.php" class=" flex items-center text-center"><img class="w-8 rounded-full h-8" src="../media/profile_photo_url/<?php echo $_SESSION["CustomerProfilePhoto"] ?? 'default.svg'; ?>" alt="">
                            Dashboard-<?php echo $_SESSION["CustomerName"]; ?></a>
                    </div>
                <?php } else {  ?>
                    <div class=" border-white border-2 rounded-md px-1 ">
                        <a href="http://localhost/tourism%20agency/main/userprofile.php" class=" flex items-center text-center"><img class="w-8 rounded-full h-8" src="../media/profile_photo_url/<?php echo $_SESSION["CustomerProfilePhoto"] ?? 'default.svg'; ?>" alt="">
                            <?php echo $_SESSION["CustomerName"]; ?></a>
                    </div>
                <?php }  ?>

                <a href="http://localhost/tourism%20agency/auth/logout.php" class="border-white border-2 rounded-md px-1"><?php echo $lang["Logout"] ?></a>
            <?php } else { ?>
                <a href="http://localhost/tourism%20agency/auth/register.php" class="border-white border-2 rounded-md px-1"><?php echo $lang["Register"] ?></a>
                <a href="http://localhost/tourism%20agency/auth/login.php" class="border-white border-2 rounded-md px-1"><?php echo $lang["Login"] ?></a>
            <?php } ?>
            <?php include __DIR__ . '/../lang/language-switcher.php'; ?>
            <i id="themeIconDesktop" class='bx bx-moon text-2xl cursor-pointer' onclick="toggleTheme()"></i>
        </div>
        <div class=" block lg:hidden cursor-pointer">
            <i class='bx bx-menu' onclick="showMenu()"></i>
        </div>






        <!---------------------------------------- Mobile Menu  ---------------------------------------------->

        <div class="hidden bg-primary  flex-col px-10 py-6 gap-8 absolute left-0 top-0 w-screen h-full z-50" id="mobileMenu">

            <div class="flex justify-between items-center">
                <a class="w-[61px]" href="http://localhost/tourism%20agency/main/"><img src="../assets/logo.png" alt=""></a>
                <div class=" cursor-pointer text-4xl" onclick="showMenu()">
                    <i class='bx bx-x'></i>
                </div>
            </div>
            <hr>
            <div>
                <ul class="">
                    <li class=""><a href="http://localhost/tourism%20agency/main/agencies.php"><?php echo $lang["Agencies"] ?></a></li>
                    <li class=""><a href="http://localhost/tourism%20agency/main/circuits.php"><?php echo $lang["circuits"] ?></a></li>
                    <li class=""><a href="http://localhost/tourism%20agency/main/locations.php"><?php echo $lang["locations"] ?></a></li>
                    <li class=""><a href="http://localhost/tourism%20agency/main/accommodations.php"><?php echo $lang["Accommodations"] ?></a></li>
                    <li class=""><a class="hover:text-[26px]" href="http://localhost/tourism%20agency/conversations/conversations.php"><?php echo $lang["conversations"] ?></a></li>
                    <li class=""><a href="http://localhost/tourism%20agency/main/aboutUs.php"><?php echo $lang["AboutUs"] ?></a></li>
                    <li class=""><a href="http://localhost/tourism%20agency/main/contact.php"><?php echo $lang["contact"] ?></a></li>

                </ul>
            </div>
            <hr>
            <div class="flex flex-col gap-3">
                <?php if (isset($_SESSION["success"]) && !empty($_SESSION["success"])) { ?>
                    <?php if ($_SESSION["CustomerRole"] == "super_admin") { ?>
                        <div class=" border-white border-2 rounded-md px-1 ">
                            <a href="http://localhost/tourism%20agency/dashboard/super_admin.php" class=" flex items-center text-center"><img class="w-8 rounded-full h-8" src="../media/profile_photo_url/<?php echo $_SESSION["CustomerProfilePhoto"] ?? 'default.svg'; ?>" alt="">
                                Admin Panel-<?php echo $_SESSION["CustomerName"]; ?></a>
                        </div>
                    <?php } elseif ($_SESSION["CustomerRole"] == "agency_admin") {  ?>
                        <div class=" border-white border-2 rounded-md px-1 ">
                            <a href="http://localhost/tourism%20agency/agency_dashboard/agency_info.php" class=" flex items-center text-center"><img class="w-8 rounded-full h-8" src="../media/profile_photo_url/<?php echo $_SESSION["CustomerProfilePhoto"] ?? 'default.svg'; ?>" alt="">
                                Dashboard-<?php echo $_SESSION["CustomerName"]; ?></a>
                        </div>
                    <?php } else {  ?>
                        <div class=" border-white border-2 rounded-md px-1 ">
                            <a href="http://localhost/tourism%20agency/main/userprofile.php" class=" flex items-center text-center"><img class="w-8 rounded-full h-8" src="../media/profile_photo_url/<?php echo $_SESSION["CustomerProfilePhoto"] ?? 'default.svg'; ?>" alt="">
                                <?php echo $_SESSION["CustomerName"]; ?></a>
                        </div>
                    <?php }  ?>
                    <a href="http://localhost/tourism%20agency/auth/logout.php" class="border-white border-2 rounded-md px-1"><?php echo $lang["Logout"] ?></a>

                <?php } else { ?>
                    <a href="http://localhost/tourism%20agency/auth/register.php" class="border-white border-2 rounded-md px-1"><?php echo $lang["Register"] ?></a>
                    <a href="http://localhost/tourism%20agency/auth/login.php" class="border-white border-2 rounded-md px-1"><?php echo $lang["Login"] ?></a>
                <?php } ?>
                <?php include __DIR__ . '/../lang/language-switcher.php'; ?>
                <i id="themeIconMobile" class='bx bx-moon text-2xl cursor-pointer' onclick="toggleTheme()"></i>
            </div>
        </div>

    </nav>


<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let mobileMenu = document.getElementById("mobileMenu");

        function showMenu() {
            mobileMenu.classList.toggle("hidden");
            mobileMenu.classList.toggle("flex");
        }






        function toggleTheme() {
            const body = document.body;
            const isDarkMode = body.classList.contains('bg-background-dark');

            // Toggle classes
            body.classList.toggle('bg-background-dark', !isDarkMode);
            body.classList.toggle('bg-background-light', isDarkMode);
            body.classList.toggle('text-white', !isDarkMode);
            body.classList.toggle('text-black', isDarkMode);

            // Update icons
            const icons = document.querySelectorAll('#themeIconDesktop, #themeIconMobile');
            icons.forEach(icon => {
                icon.className = isDarkMode ?
                    'bx bx-moon text-2xl cursor-pointer' :
                    'bx bx-sun text-2xl cursor-pointer';
            });

            // Save to cookie (30 days expiration)
            document.cookie = `theme=${isDarkMode ? 'light' : 'dark'}; path=/; max-age=${60*60*24*30}`;
        }

        // Initialize theme - DARK MODE DEFAULT
        document.addEventListener('DOMContentLoaded', () => {
            const cookieValue = document.cookie
                .split('; ')
                .find(row => row.startsWith('theme='))
                ?.split('=')[1] || 'dark'; // Force dark as default

            const body = document.body;

            // Set initial classes
            if (cookieValue === 'dark') {
                body.classList.add('bg-background-dark', 'text-white');
                document.querySelectorAll('#themeIcon, #mobileThemeIcon').forEach(icon => {
                    icon.className = 'bx bx-sun text-2xl cursor-pointer';
                });
            } else {
                body.classList.add('bg-background-light', 'text-black');
                document.querySelectorAll('#themeIcon, #mobileThemeIcon').forEach(icon => {
                    icon.className = 'bx bx-moon text-2xl cursor-pointer';
                });
            }
        });
    </script>