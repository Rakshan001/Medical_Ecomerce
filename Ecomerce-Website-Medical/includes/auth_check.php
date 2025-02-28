<?php
// Place this at the very top of the file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAuth() {
    // Check if user is not logged in
    if (!isset($_SESSION['user_id'])) {
        // Store the current URL for redirect after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        // Use JavaScript for redirect to avoid headers already sent error
        echo "<script>window.location.href = '" . BASE_PATH . "auth/login.php';</script>";
        exit();
    }
}

function checkRole($allowed_roles) {
    // First check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "<script>window.location.href = '" . BASE_PATH . "auth/login.php';</script>";
        exit();
    }
    
    // Then check if user has correct role
    if (!in_array($_SESSION['user_role'], $allowed_roles)) {
        echo "<script>window.location.href = '" . BASE_PATH . "auth/unauthorized.php';</script>";
        exit();
    }
}

// Optional: Add a function to check if user is logged in without redirecting
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Optional: Add a function to check user's role without redirecting
function hasRole($roles) {
    if (!isset($_SESSION['user_role'])) return false;
    return in_array($_SESSION['user_role'], (array)$roles);
}
?> 