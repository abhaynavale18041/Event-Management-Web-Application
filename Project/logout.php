<?php
/**
 * User logout
 */

require_once __DIR__ . '/config.php';

// Destroy session
$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();

// Redirect to home
header('Location: ' . SITE_URL . '/index.php');
exit;

