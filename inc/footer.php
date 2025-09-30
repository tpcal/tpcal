<!-- Footer -->
<footer class="py-16 bg-veryDarkBlue">
    <!-- Footer Flex Container -->
    <div class="container flex flex-col items-center justify-between mx-auto space-y-16 px-6 md:flex-row md:space-y-0">

        <!-- Logo/Menu Container -->
        <div
            class="flex flex-col items-center justify-between space-y-8 text-lg font-light md:flex-row md:space-y-0 md:space-x-14 text-grayishBlue">
            
            <a href="/" class="uppercase hover:text-softRed">Home</a>

            <a href="about.php" class="uppercase hover:text-softRed">About</a>

            <!-- <a href="#research" class="uppercase hover:text-softRed">Research</a>
            <a href="#faq" class="uppercase hover:text-softRed">FAQ</a> -->

        </div>

        <!-- Social Container -->
        <div class="flex space-x-10">
            <a href="https://calgeek.com" target="_blank" class="uppercase hover:text-softRed" style="color: white;">
                Built by Cal Geek
            </a>
        </div>
    </div>
</footer>

<?php if (getenv('APP_ENV') === 'development'): ?>
  <!-- This script enables automatic browser reloading -->
  <script src="/js/live-reload-client.js"></script>
<?php endif; ?>

</body>

</html>