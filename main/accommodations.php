<?php
$pageTitle = "Locations page";
include __DIR__ . '/../includes/header.php';
require("../other/accommodationsPHPCode.php");
$citiesRes = mysqli_query($conn, "SELECT DISTINCT City FROM accommodation ORDER BY City ASC");
?>
<!-- Hero Section -->
<section id="hero" class="h-screen text-center relative flex items-center justify-center transition-all duration-1000">
  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/50"></div>

  <!-- Content -->
  <div class="relative z-10 text-center text-white px-4 ">
    <h1 class="text-xl md:text-2xl lg:text-4xl font-bold text-center px-2 mb-8 text-white"><?php echo $lang['welcome_message_hero'] ?></h1>
    <p class=" px-10 md:px-20 lg:px-40 text-xl md:text-2xl mb-8 text-white"><?php echo $lang['hero_paragraph'] ?></p>

  </div>
</section>
<div class="container mx-auto mt-5 px-4">
<form id="accSearchForm" class="flex flex-col md:flex-row text-black items-center justify-center gap-3 mb-8">
  <input type="search" name="search" placeholder="<?= $lang['search_placeholder'] ?>" class="text-lg border rounded-full px-4 py-2 w-[285px] md:w-[300px]">
  
  <select name="city" class="text-lg border text-black rounded-full px-4 py-2 w-[200px]">
    <option value="all"><?= $lang['all_cities'] ?? 'All Cities' ?></option>
    <?php while ($row = mysqli_fetch_assoc($citiesRes)) {
      $selected = ($_GET['city'] ?? '') === $row['City'] ? 'selected' : '';
      echo "<option value=\"{$row['City']}\" $selected>{$row['City']}</option>";
    } ?>
  </select>

  <input type="hidden" name="page" value="1">
  <button type="submit" class="bg-primary text-white px-4 py-2 rounded-full"><?= $lang['search_placeholder'] ?></button>
</form>

  <div id="accommodationsContainer"></div>
</div>

<script>
document.getElementById('accSearchForm').addEventListener('submit', e => {
  e.preventDefault();
  const params = new URLSearchParams(new FormData(e.target));
  fetch('../other/accommodations_ajax.php?' + params)
    .then(r => r.text())
    .then(html => document.getElementById('accommodationsContainer').innerHTML = html);
});

// Handle pagination
document.addEventListener('click', e => {
  if (e.target.matches('.pagination-link')) {
    e.preventDefault();
    document.querySelector('input[name="page"]').value = e.target.dataset.page;
    document.getElementById('accSearchForm').dispatchEvent(new Event('submit'));
  }
});

// Initial load
document.getElementById('accSearchForm').dispatchEvent(new Event('submit'));
      // Add scroll animation script 

  // Scroll animation for sections
  const div = document.querySelectorAll('div');
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.remove('opacity-0', 'translate-y-20');
        entry.target.classList.add('opacity-100', 'translate-y-0');
      }
    });
  }, { threshold: 0.1 });

  div.forEach(section => {
    observer.observe(section);
  });


  
  // Background images array (replace with your own images)
  const backgrounds = [
    'url("../assets/hotel-601327_1920.jpg")',
    'url("../assets/hotel-6862159_1920.jpg")',
    'url("../assets/hotelroom-2205447_1920.jpg")',
    'url("../assets/hotelroom-7772422_1920.jpg")',
    'url("../assets/beds-182965_1920.jpg")'
  ];

  let currentBg = 0;
  const hero = document.getElementById('hero');

  function changeBackground() {
    currentBg = (currentBg + 1) % backgrounds.length;
    hero.style.backgroundImage = backgrounds[currentBg];
  }

  // Initialize first background
  hero.style.backgroundImage = backgrounds[0];
  hero.classList.add('bg-cover', 'bg-center');

  // Change background every 3 seconds
  setInterval(changeBackground, 3000);
</script>
<?php
mysqli_close($conn);
    
include __DIR__ . "/../includes/footer.php";
?>