<footer class="bg-primary text-white px-5 py-10 text-[24px] font-semibold  lg:h-[200px] gap-10 mt-auto">
    <div class="flex flex-col items-center lg:flex-row lg:justify-evenly gap-5">
        <div class="w-[61px]"><a href="http://localhost/tourism%20agency/main/"><img src="../assets/logo.png" alt=""></a></div>
        <div class="flex items-center text-lg gap-3 lg:gap-6 ">
            <a href="http://localhost/tourism%20agency/main/reviews.php"><?php echo $lang["Reviews"] ?></a>
            <a href="http://localhost/tourism%20agency/main/aboutUs.php"><?php echo $lang["AboutUs"] ?></a>
            <a href="http://localhost/tourism%20agency/main/contact.php"><?php echo $lang["contact"] ?></a>
        </div>
        <div class="text-4xl">
            <a href=""><i class='bx bxl-facebook-circle'></i></a>
            <a href=""><i class='bx bxl-instagram-alt' ></i></a>
            <a href=""><i class='bx bxl-whatsapp' ></i></a>
        </div>
    </div>
    <div class="text-[16px] flex flex-col items-center lg:flex-row lg:justify-evenly lg:items-center mt-3 gap-5">
        <a href="http://localhost/tourism%20agency/main/agencies.php"><?php echo $lang["Terms of Service"] ?></a>
        <a href="http://localhost/tourism%20agency/main/agencies.php"><?php echo $lang["Privacy Policy"] ?></a>
        <span><?php echo $lang["© 2024 Jawla. All rights reserved"] ?></span>
    </div>

</footer>

<script>
    const sections = document.querySelectorAll('section');
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.remove('opacity-0', 'translate-y-20');
        entry.target.classList.add('opacity-100', 'translate-y-0');
      }
    });
  }, { threshold: 0.1 });

  sections.forEach(section => {
    observer.observe(section);
  });
</script>

</body>
</html>