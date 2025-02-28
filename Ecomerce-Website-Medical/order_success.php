<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
require_once 'includes/header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Success</title>
</head>
<body>
    <div class="success-container">
        <h1>Order Successful!</h1>
        <p>Your order #<?php echo $order_id; ?> has been placed successfully.</p>
        <a href="products.php" class="btn">Continue Shopping</a>
    </div>
</body>
</html> 