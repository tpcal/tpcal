<?php

// Start session management
session_start();

// Enable full error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Creates and returns a PDO database connection object.
 *
 * This function reads database configuration from environment variables,
 * which are passed in from the docker-compose.yml file.
 *
 * @return PDO The PDO database connection object.
 */
function pdo(): PDO
{
    // Use a static variable to ensure the connection is only made once per request.
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    // --- Database Configuration ---
    // The host is the name of the database service in docker-compose.yml
    $host = 'db';
    $dbname = 'tpcal';
    $user = 'user';
    $password = 'password';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $password, $options);
    } catch (\PDOException $e) {
        // If the connection fails, stop the script and show an error.
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    return $pdo;
}

/**
 * Generates and returns a CSRF (Cross-Site Request Forgery) token.
 *
 * This helps protect against CSRF attacks by ensuring that requests
 * are coming from the user's current session.
 *
 * @return string The CSRF token.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        // Generate a new random token if one doesn't exist.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}