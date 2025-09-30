<?php

require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/auth.php';

$db = pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_csrf();
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        die('Invalid email');
    if (strlen($pass) < 8)
        die('Password too short');

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $st = $db->prepare("INSERT INTO users(email,password_hash) VALUES(?,?)");
    
    try {
        $st->execute([$email, $hash]);
        login_user($db, (int) $db->lastInsertId());
        header('Location: /');
        exit;
    } catch (Throwable $e) {
        die('Registration error');
    }
    
}

?>

<?php require __DIR__ . '/inc/header.php'; ?>


<!-- tailwind component -->

<div class="h-full bg-gray-900 flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">

  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img src="../images/tpc-bear.png" alt="Your Company" class="mx-auto h-10 w-auto" />
    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white">Sign up 🇺🇸 ✅</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

    <form method="POST" class="space-y-6">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
      
      <div>
        <label for="email" class="block text-sm/6 font-medium text-gray-100">Email address</label>
        <div class="mt-2">
          <!-- email input -->
          <input id="email" type="email" name="email" required autocomplete="email" class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <!-- password input -->
          <label for="password" class="block text-sm/6 font-medium text-gray-100">Password</label>
        </div>
        <div class="mt-2">
          <input id="password" type="password" name="password" required autocomplete="current-password" class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
        </div>
      </div>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-500 px-3 py-1.5 text-sm/6 font-semibold text-white hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Sign up</button>
      </div>
    </form>

  </div>
</div>


<?php include __DIR__ . '/inc/footer.php'; ?>