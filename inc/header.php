<!DOCTYPE html>
<?php require_once __DIR__ . "/auth.php"; ?>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600;700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="output.css" />
  <script src="js/script.js" defer></script>
  <title>Turning Point California</title>
</head>


<body class="overflow-x-hidden">

  <nav class="container relative mx-auto p-6">
    <!-- Flex Container For Nav Items -->
    <div class="flex items-center justify-between space-x-20 my-6">
      <!-- Logo -->
      <div class="z-30">
        
        <a href="/" class="flex items-center space-x-2">
          <img src="../images/tpc-bear.png" alt="tpc bear" id="logo" class="h-8 w-auto align-middle" style="height: 2rem;" />
          <span class="text-lg font-semibold align-middle pt-1">Turning Point California</span>
        </a>
        
      </div>

      <!-- Menu Items -->
      <div class="hidden items-center space-x-10 uppercase text-grayishBlue md:flex">

      
        <a href="/" class="block hover:text-softRed">Home</a>
    
        <a href="about.php" class="tracking-widest hover:text-softRed">About</a>

        <!-- <a href="#research" class="tracking-widest hover:text-softRed">Research</a>

        <a href="#faq" class="tracking-widest hover:text-softRed">FAQ</a> -->

        <?php if (current_user_id()): ?>
          <span class="text-softRed"><?php echo htmlspecialchars(current_user_email(pdo())); ?></span>
          <a href="logout.php" class="px-8 py-2 border-2 rounded-lg shadow-md hover:text-softRed hover:bg-white">
            Logout
          </a>
        <?php else: ?>
          <a href="login.php" class="px-8 py-2 border-2 rounded-lg shadow-md hover:text-softRed hover:bg-white">
            Sign in
          </a>

          <a href="register.php" class="px-8 py-2 text-white bg-softRed border-2 border-softRed rounded-lg shadow-md hover:text-softRed hover:bg-white">
            Sign up
          </a>
        <?php endif; ?>
      </div>

      <!-- Hamburger Button -->
      <button id="menu-btn" class="z-30 block md:hidden focus:outline-none hamburger">
        <span class="hamburger-top"></span>
        <span class="hamburger-middle"></span>
        <span class="hamburger-bottom"></span>
      </button>
    </div>


    <!-- Mobile Menu -->
    <div id="menu"
      class="fixed inset-0 z-20 hidden flex-col items-center self-end w-full h-full m-h-screen px-6 py-1 pt-24 pb-4 tracking-widest text-white uppercase divide-y divide-gray-500 opacity-90 bg-veryDarkBlue">
      
      <div class="w-full py-3 text-center">
        <a href="/" class="block hover:text-softRed">Home</a>
      </div>
      <div class="w-full py-3 text-center">
        <a href="about.php" class="block hover:text-softRed">About</a>
      </div>

      <!-- <div class="w-full py-3 text-center">
        <a href="#research" class="block hover:text-softRed">Research</a>
      </div>
      
      <div class="w-full py-3 text-center">
        <a href="#faq" class="block hover:text-softRed">FAQ</a>
      </div> -->

      <!-- sign up/in -->
      <div class="w-full py-3 text-center">
        <?php if (current_user_id()): ?>
          <span class="text-softRed block"><?php echo htmlspecialchars(current_user_email(pdo())); ?></span>
          <a href="logout.php" class="px-8 py-2 border-2 rounded-lg shadow-md hover:text-softRed hover:bg-white block mt-2">
            Logout
          </a>
        <?php else: ?>
          <a href="login.php" class="px-8 py-2 border-2 rounded-lg shadow-md hover:text-softRed hover:bg-white block">
            Sign in
          </a>
        <?php endif; ?>
      </div>

      <div class="w-full py-3 text-center">
        <a href="register.php" class="px-8 py-2 text-white bg-softRed border-2 border-softRed rounded-lg shadow-md hover:text-softRed hover:bg-white">
          Sign up
        </a>
      </div>
    </div>
  </nav>