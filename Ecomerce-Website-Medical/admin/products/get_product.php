<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';

checkAuth();
checkRole(['admin']);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    // Get additional images
    $stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $additional_images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode([
        'product' => $product,
        'additional_images' => $additional_images
    ]);
    exit;
} 