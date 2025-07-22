<?php
$pageTitle = "Locations page";
include __DIR__ . '/../includes/header.php';
require("../other/locationsPHPCode.php");
?>
<!-- Hero Section -->
<section id="hero" class="h-screen text-center relative flex items-center justify-center transition-all duration-1000">
  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/50"></div>

  <!-- Content -->
  <div class="relative z-10 text-center text-white px-4 ">
    <h1 class="text-xl md:text-2xl lg:text-4xl font-bold text-center px-2 mb-8 text-white"><?php echo $lang['welcome_message_hero'] ?></h1>
    <p class=" px-10 md:px-20 lg:px-40 text-xl md:text-2xl mb-8 text-white"><?php echo $lang['hero_paragraph'] ?></p>
    <section class="flex flex-col items-center justify-center my-5">


    </section>
  </div>
</section>

<div class="container mx-auto mt-5 px-4">
  <div class="flex flex-wrap gap-3 justify-center my-4">
    <button data-city="all" class="tab-btn px-4 py-2 bg-primary text-white rounded-full">All</button>
    <?php 
      $cr = mysqli_query($conn, "SELECT DISTINCT City FROM location ORDER BY City ASC");
      while ($city = mysqli_fetch_assoc($cr)) {
        echo '<button data-city="'.htmlspecialchars($city['City']).'" class="tab-btn px-4 py-2 bg-gray-200 text-gray-800 rounded-full hover:bg-primary hover:text-white transition">'.htmlspecialchars($city['City']).'</button>';
      }
    ?>
  </div>

  <form id="locSearchForm" class="flex justify-center mb-8">
    <input type="search" name="search" placeholder="<?= $lang['search_placeholder'] ?>" class="text-lg border rounded-full px-4 py-2 w-full max-w-lg">
    <input type="hidden" name="city" value="all">
    <input type="hidden" name="page" value="1">
    <button type="submit" class="ml-2 bg-primary text-white px-4 py-2 rounded-full"><?= $lang['search_placeholder'] ?></button>
  </form>

  <div id="locationsContainer"></div>
</div>







<script>
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
    'url("../assets/alger-2471634_1920.jpg")',
    'url("../assets/bejaia-2433836_1920.jpg")',
    'url("../assets/garden-2075069_1920.jpg")',
    'url("../assets/alger-2471643_1920.jpg")',
    'url("../assets/mediterranean-2642995_1920.jpg")'
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






  document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('bg-primary','text-white'));
    btn.classList.add('bg-primary','text-white');
    document.querySelector('input[name=city]').value = btn.dataset.city;
    document.querySelector('input[name=page]').value = 1;
    document.getElementById('locSearchForm').dispatchEvent(new Event('submit'));
  });
});

document.getElementById('locSearchForm').addEventListener('submit', e => {
  e.preventDefault();
  const params = new URLSearchParams(new FormData(e.target));
   const currentLang = new URLSearchParams(window.location.search).get('lang');
  if (currentLang) params.append('lang', currentLang);  
  fetch('../other/locations_ajax.php?' + params)
    .then(r => r.text())
    .then(html => document.getElementById('locationsContainer').innerHTML = html);
});

document.addEventListener('click', e => {
  if (e.target.matches('.pagination-link')) {
    e.preventDefault();
    document.querySelector('input[name=page]').value = e.target.dataset.page;
    document.getElementById('locSearchForm').dispatchEvent(new Event('submit'));
  }
});

// Initial load
document.getElementById('locSearchForm').dispatchEvent(new Event('submit'));
</script>
<?php
mysqli_close($conn);
    
include __DIR__ . "/../includes/footer.php";
?>