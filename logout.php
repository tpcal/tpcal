<?php 

require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/auth.php';

logout_user();

header('Location: /'); 

