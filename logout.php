<?php
// Include database config (likely unnecessary here, but safe for consistency)
require_once 'includes/config.php';

// Start session if it is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ======================
//  Clear Session Data
// ======================

// Unset all session variables
$_SESSION = [];

// Delete the session cookie to fully log out the user
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session data on the server
session_destroy();

// ======================
// Redirect to Login
// ======================
header("Location: login.php");
exit();
