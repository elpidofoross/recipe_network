<?php
// Include database configuration
require_once 'config.php';

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is logged in
 * Returns true if user session exists
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to login page if user is not authenticated
 * Stores current URL for redirect after login
 */
function require_login() {
    if (!is_logged_in()) {
        // Save the URL the user was trying to access
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? 'profile.php';
        header("Location: login.php");
        exit();
    }
}

/**
 * Generate a CSRF token for the session or return existing one
 * Uses cryptographically secure random bytes
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
