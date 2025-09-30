<?php

declare(strict_types=1);

require_once __DIR__ . "/bootstrap.php";


// Auth specific 
function current_user_id(): ?int
{
    return $_SESSION['uid'] ?? null;
}

function current_user_email(PDO $db): ?string
{
    $uid = current_user_id();
    if (!$uid) return null;
    $s = $db->prepare("SELECT email FROM users WHERE id=?");
    $s->execute([$uid]);
    return $s->fetchColumn();
}

function ensure_anon_user(PDO $db): int
{
    if (isset($_COOKIE['tpcal_anon'])) {
        $k = $_COOKIE['tpcal_anon'];
    } else {
        $k = uuidv4();
        setcookie('tpcal_anon', $k, time() + 60 * 60 * 24 * 365, 
                  '/', '', !is_local(), true);
    }

    // find or create user with this anon_key
    $s = $db->prepare("SELECT id FROM users WHERE anon_key=?");
    
    $s->execute([$k]);
    
    $id = $s->fetchColumn();
    
    if (!$id) {
        $s = $db->prepare("INSERT INTO users(anon_key) VALUES(?)");
        $s->execute([$k]);
        $id = (int) $db->lastInsertId();
    }
    
    return (int) $id;
}

function login_user(PDO $db, int $uid): void
{
    $_SESSION['uid'] = $uid;
}

function logout_user(): void
{
    session_destroy();
}
