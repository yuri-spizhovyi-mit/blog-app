<?php
require_once __DIR__ . '/config/constants.php';

// Proper logout handling
session_start();

// Clear all session data
$_SESSION = [];

// Destroy the session cookie (important for browsers)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session entirely
session_destroy();

// Redirect to homepage
header('Location: ' . ROOT_URL);
exit;
?>
