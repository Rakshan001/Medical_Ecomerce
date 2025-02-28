<?php
function isCurrentPage($page) {
    $current_file = basename($_SERVER['PHP_SELF']);
    if (strpos($page, '/') !== false) {
        // If checking a path with directories
        $current_path = dirname($_SERVER['PHP_SELF']);
        return strpos($current_path . '/' . $current_file, $page) !== false;
    }
    return $current_file === $page;
}
