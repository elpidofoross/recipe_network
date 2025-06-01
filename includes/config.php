<?php
// =============================
// SESSION SETUP
// =============================
// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================
// ERROR REPORTING (DEV MODE)
// =============================
// Show all errors and warnings (only for development)
// In production: change display_errors to 0
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =============================
// DATABASE CONFIGURATION
// =============================
define('DB_HOST', 'localhost');
define('DB_NAME', 'recipe_network');
define('DB_USER', 'root');
define('DB_PASS', '');

// =============================
// DATABASE CONNECTION (PDO)
// =============================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,            // Throw exceptions on DB errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC        // Fetch associative arrays by default
        ]
    );
} catch (PDOException $e) {
    // Log error internally and stop script execution
    error_log("DB error: " . $e->getMessage());
    die("Database connection failed.");
}

// =============================
// SECURITY HEADERS
// =============================
// Protect against clickjacking
header("X-Frame-Options: DENY");

// Prevent MIME sniffing by browsers
header("X-Content-Type-Options: nosniff");

// Limit referrer data for privacy
header("Referrer-Policy: no-referrer");

// Block access to certain browser APIs
header("Permissions-Policy: geolocation=(), microphone=()");

// Enforce HTTPS and preload HSTS in browsers
header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload");
