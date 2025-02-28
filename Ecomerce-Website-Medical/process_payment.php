<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['order_id'])) {
    header("Location: cart.php");
    exit();
}

$order_id = (int)$_POST['order_id'];
$shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

// Begin transaction
$conn->begin_transaction();

try {
    // Update order with shipping and payment details
    $stmt = $conn->prepare("
        UPDATE orders 
        SET status = 'processing',
            payment_status = ?,
            shipping_address = ?,
            payment_method = ?
        WHERE id = ? AND user_id = ?
    ");
    
    $payment_status = ($payment_method === 'cod') ? 'pending' : 'paid';
    $stmt->bind_param("sssii", $payment_status, $shipping_address, $payment_method, $order_id, $_SESSION['user_id']);
    $stmt->execute();

    // Update product stock
    $stmt = $conn->prepare("
        UPDATE products p
        JOIN order_items oi ON p.id = oi.product_id
        SET p.stock = p.stock - oi.quantity
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    $conn->commit();
    
    // Clear session order data
    unset($_SESSION['order_id']);
    
    // Redirect to success page
    header("Location: order_success.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Payment processing failed: " . $e->getMessage();
    header("Location: checkout.php");
    exit();
}
?> 