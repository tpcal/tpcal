<?php

declare(strict_types=1);

/**
 * A very simple .env file loader for local development.
 * In production, you should set these as "real" environment variables on your server.
 */
function load_env(): void
{
    // Only load .env file if it exists and we are in a local environment
    $envFile = __DIR__ . '/../.env';
    if (!is_local() || !file_exists($envFile)) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split on the first '='
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Set the environment variable
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

function is_local(): bool
{
    if (getenv('TPCAL_ENV')) {
        return strtolower(getenv('TPCAL_ENV')) === 'local';
    }

    $h = $_SERVER['SERVER_NAME'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    return in_array($h, ['localhost', '127.0.0.1'])
            || in_array($ip, ['127.0.0.1', '::1'])
            || PHP_SAPI === 'cli-server';
}

// Load environment variables *before* any other functions might need them.
load_env();


session_start();

ini_set('display_errors', '1');
error_reporting(E_ALL);


function pdo(): PDO
{
    static $pdo = null;

    if ($pdo) {
        return $pdo;
    }

    // Fetch credentials from environment variables.
    $dbHost = getenv('DB_HOST');
    $dbPort = getenv('DB_PORT');
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

    // Fail gracefully if variables aren't set.
    if ($dbHost === false || $dbName === false || $dbUser === false || $dbPass === false) {
        // Using trigger_error is often better than exit() as it can be caught and logged.
        trigger_error('Database environment variables are not fully configured.', E_USER_ERROR);
    }
    
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', 
        $dbHost, 
        $dbPort ?: '3306', // Default port if not set
        $dbName
    );

    $pdo = new PDO($dsn, $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

    return $pdo;
}

function uuidv4(): string
{
    $d = random_bytes(16);
    $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
    $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($d), 4));
}


function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf'];
}

function require_csrf(): void
{
    $t = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(403);
        exit('Bad CSRF');
    }
}