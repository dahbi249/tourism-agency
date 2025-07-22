<form method="GET" class="inline-block">
<?php
    // Preserve all existing GET parameters except 'lang'
    foreach ($_GET as $key => $value) {
        if ($key !== 'lang') {
            echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
        }
    }
    ?>
    <select 
        name="lang" 
        onchange="this.form.submit()"
        class="bg-primary px-3 py-1 rounded-md border border-gray-300"
    >
        <option class="text-sm lg:text-md" value="en" <?= $current_lang === 'en' ? 'selected' : '' ?>>EN</option>
        <option class="text-sm lg:text-md" value="fr" <?= $current_lang === 'fr' ? 'selected' : '' ?>>FR</option>
        <option class="text-sm lg:text-md" value="ar" <?= $current_lang === 'ar' ? 'selected' : '' ?>>AR</option>
        <option class="text-sm lg:text-md" value="es" <?= $current_lang === 'es' ? 'selected' : '' ?>>ES</option>
    </select>
</form>