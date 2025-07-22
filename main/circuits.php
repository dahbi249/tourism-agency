<?php
$pageTitle = "Circuits page";
include __DIR__ . '/../includes/header.php';
require("../other/circuitsPHPCode.php");


// Fetch cities
$cityQuery = "SELECT DISTINCT City FROM location ORDER BY City ASC";
$cityResult = mysqli_query($conn, $cityQuery);

// Fetch location names
$locationQuery = "SELECT DISTINCT Name FROM location ORDER BY Name ASC";
$locationResult = mysqli_query($conn, $locationQuery);
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
      <form action="" method="get" class="relative">
        <input type="search" name="search" id="" placeholder="<?php echo $lang['search_placeholder'] ?>" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>" class="text-lg md:text-xl outline-none border-none text-black w-[285xp] h-[41px] lg:w-[482px] lg:h-[70px] rounded-full px-2">
        <input type="submit" value="<?php echo $lang['search_placeholder'] ?>" class=" absolute <?= $is_rtl ? 'left-0' : 'right-0' ?>  cursor-pointer text-white px-1 lg:px-3 h-[41px] lg:h-[70px] bg-primary rounded-full text-[18px] lg:text-[20px] font-semibold ">
      </form>

    </section>
  </div>
</section>

<div class="max-w-5xl mx-auto  p-6 rounded-2xl shadow-lg">
  <h1 class="text-2xl font-bold mb-4 text-center text-primary">Search Circuits</h1>

  <!-- Search Form -->
  <form id="searchForm" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-black">
    <div>
      <label class="block mb-1 text-sm font-medium">City</label>
      <select name="city" class="w-full border rounded-lg p-2">
        <option value="">-- All Cities --</option>
        <?php while ($row = mysqli_fetch_assoc($cityResult)): ?>
          <option value="<?= htmlspecialchars($row['City']) ?>"><?= htmlspecialchars($row['City']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div>
      <label class="block mb-1 text-sm font-medium">Location</label>
      <select name="location" class="w-full border rounded-lg p-2">
        <option value="">-- All Locations --</option>
        <?php while ($row = mysqli_fetch_assoc($locationResult)): ?>
          <option value="<?= htmlspecialchars($row['Name']) ?>"><?= htmlspecialchars($row['Name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div>
      <label class="block mb-1 text-sm font-medium">Min Price</label>
      <input type="number" step="0.01" name="min_price" class="w-full border rounded-lg p-2" placeholder="e.g. 100">
    </div>

    <div>
      <label class="block mb-1 text-sm font-medium">Max Price</label>
      <input type="number" step="0.01" name="max_price" class="w-full border rounded-lg p-2" placeholder="e.g. 1000">
    </div>

    <input type="hidden" name="page" value="1">
    <div class="md:col-span-2 flex justify-center mt-4">
      <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary">Search</button>
    </div>
  </form>
</div>


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

<!-- Results -->
<div id="resultsContainer" class="max-w-5xl mb-10 mx-auto mt-6"></div>











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
  }, {
    threshold: 0.1
  });

  div.forEach(section => {
    observer.observe(section);
  });











  const form = document.getElementById('searchForm');
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../other/search_ajax.php?' + new URLSearchParams(formData), {
        method: 'GET'
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('resultsContainer').innerHTML = data;
      })
      .catch(err => console.error(err));
  });

  // Pagination handling
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('pagination-link')) {
      e.preventDefault();
      const page = e.target.dataset.page;
      document.querySelector('input[name="page"]').value = page;
      form.dispatchEvent(new Event('submit'));
    }
  });

  // Auto-load first page on start
  form.dispatchEvent(new Event('submit'));
















  // Background images array (replace with your own images)
  const backgrounds = [
    'url("../assets/bejaia-2433836_1920.jpg")',
    'url("../assets/mediterranean-2642995_1920.jpg")',
    'url("../assets/garden-2075069_1920.jpg")'
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