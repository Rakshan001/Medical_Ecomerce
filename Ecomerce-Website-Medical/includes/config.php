<?php
// Prevent direct access to this file
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/Ecomerce-Website-Medical/');
}

// Start session if not already started (this is the ONLY place we start the session)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site configuration
define('SITE_NAME', 'MediCare');
define('SITE_EMAIL', 'info@medicare.com');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_website_medical');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function to get absolute URL
function url($path = '') {
    return BASE_PATH . ltrim($path, '/');
}
?> 