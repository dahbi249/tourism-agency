<?php
// 1. Define allowed languages
$allowed_langs = ['en', 'fr', 'ar', 'es'];

// 2. Get language from URL parameter
if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed_langs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// 3. Set default language if not in session
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en'; // Default language
}

// 4. Store current language in variable
$current_lang = $_SESSION['lang'];

// 5. Load translations
$translations = json_decode(file_get_contents(__DIR__.'/language.json'), true);
$lang = $translations[$current_lang];

// 6. Set RTL direction for Arabic
$is_rtl = ($current_lang === 'ar');
