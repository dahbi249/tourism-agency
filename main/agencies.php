<?php
$pageTitle = "Agencies page";
include __DIR__ . '/../includes/header.php';
require("../other/agenciesPHPCode.php");
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
      <form id="agencySearchForm" action="" method="get" class="relative">
        <input type="search" name="search" id="" placeholder="<?php echo $lang['search_placeholder'] ?>" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>" class="text-lg md:text-xl outline-none border-none text-black w-[285xp] h-[41px] lg:w-[482px] lg:h-[70px] rounded-full px-2">
        <input type="submit" value="<?php echo $lang['search_placeholder'] ?>" class=" absolute <?= $is_rtl ? 'left-0' : 'right-0' ?>  cursor-pointer text-white px-1 lg:px-3 h-[41px] lg:h-[70px] bg-primary rounded-full text-[18px] lg:text-[20px] font-semibold ">
        <input type="hidden" name="page" value="1">
      </form>

    </section>
  </div>
</section>
<section class="flex flex-col lg:flex-row items-center justify-evenly">
  <div class="flex flex-col items-center">
    <i class='bx text-5xl bx-building-house'></i>
    <h3 class=" text-xl font-medium">Backed by travelers</h3>
    <p class="text-center w-40">Book confidently thanks to reviews from travelers who have been there.</p>
  </div>
  <div class="flex flex-col items-center">
    <i class='bx text-5xl bx-star'></i>
    <h3 class=" text-xl font-medium">Best rates around</h3>
    <p class="text-center w-40">Compare prices from 200+ booking sites to book with the best deal.</p>
  </div>
  <div class="flex flex-col items-center">
    <i class='bx text-5xl bx-search'></i>
    <h3 class=" text-xl font-medium">Search without worry</h3>
    <p class="text-center w-40">We’re completely free to use—no hidden charges or fees on flight prices at all.</p>
  </div>
</section>

<hr>
<a href="">
  <section class="p-3 flex text-white flex-col lg:flex-row justify-evenly items-center opacity-0  translate-y-20 transition-all duration-1000 ease-out bg-primary hover:scale-105">
    <h1 class="text-6xl font-bold"><span class="text-green-600">Learn</span> more about <span class="lg:text-9xl block text-red-600">Algeria</span></h1>
    <img src="../assets/ALGLOGO.png" alt="" class="w-[500px]">
  </section>
</a>
<hr>
<h1 class="text-xl md:text-2xl lg:text-4xl font-bold text-center px-2 my-8">Most trusted Agencies</h1>

<div id="agenciesContainer" class="mb-10"></div>





<script>
  // Background images array (replace with your own images)
  const backgrounds = [
    'url("../assets/office-730681_1920.jpg")',
    'url("../assets/reception-2507752_1920.jpg")',
    'url("../assets/secretary-338561_1920.jpg")'
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




  document.getElementById('agencySearchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const params = new URLSearchParams(new FormData(this));
    fetch('../other/agencies_ajax.php?' + params)
      .then(r => r.text())
      .then(html => document.getElementById('agenciesContainer').innerHTML = html);
  });

  // Handle pagination
  document.addEventListener('click', e => {
    if (e.target.matches('.pagination-link')) {
      e.preventDefault();
      const page = e.target.dataset.page;
      document.querySelector('input[name=page]').value = page;
      document.getElementById('agencySearchForm').dispatchEvent(new Event('submit'));
    }
  });

  // Load on page load
  document.getElementById('agencySearchForm').dispatchEvent(new Event('submit'));
</script>
<?php
mysqli_close($conn);

include __DIR__ . "/../includes/footer.php";
?>