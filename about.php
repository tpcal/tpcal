<?php require __DIR__ . '/inc/header.php'; ?>


  <!-- Hero Section -->
  <section id="hero">
    <!-- Container For Image & Content -->
    <div class="container flex flex-col-reverse mx-auto p-6 lg:flex-row lg:mb-0">
      <!-- Content -->
      <div class="flex flex-col space-y-10 lg:mt-16 lg:w-1/2">
        <h1 class="text-3xl font-semibold text-center lg:text-6xl lg:text-left">
          Rank California Policies
        </h1>
        <p class="max-w-md mx-auto text-lg text-center text-gray-400 lg:text-2xl lg:text-left lg:mt-0 lg:mx-0">
          Turning Point California will allow Californians to rank policies that matter most to them
          and it will be the hub for The Conservative Movement in California.
        </p>

        <!-- Buttons Container -->
        <div class="flex items-center justify-center w-full space-x-4 lg:justify-start">
          <a href="/"
            class="p-4 text-sm font-semibold text-white bg-softBlue rounded shadow-md border-2 border-softBlue md:text-base hover:bg-white hover:text-softBlue">
            Get Started
          </a>

          <a href="https://tpusa.com" target="_blank"
            class="p-4 text-sm font-semibold text-black bg-gray-300 rounded shadow-md border-2 border-gray-300 md:text-base hover:bg-white hover:text-gray-600">
            Turning Point USA
          </a>
        </div>
      </div>

      <!-- Image -->
      <div class="relative mx-auto lg:mx-0 lg:mb-0 lg:w-1/2">
        <div class="bg-hero"></div>
        <img src="images/tpc2.png" alt="" class="relative z-10 lg:top-24 xl:top-0 overflow-x-visible" />
      </div>
    </div>
  </section>


  <!-- Newsletter Section -->
  <section id="newsletter" class="bg-softBlue">
    <!-- Main Container -->
    <div class="max-w-lg mx-auto py-24">
      <p class="mb-6 text-lg tracking-widest text-center text-white uppercase">
        1,000+ Already Joined
      </p>
      <h2 class="px-3 mb-6 text-3xl font-semibold text-center text-white md:text-4xl">
        Stay up-to-date with what we're doing
      </h2>

      <!-- ArcGIS form -->
      <iframe src="https://survey123.arcgis.com/share/3e1988bd749949d1867542918cebb5c1" frameborder="0" width="100%"
        height="780px"></iframe>
    </div>
  </section>

  <?php include __DIR__ . '/inc/footer.php'; ?> 