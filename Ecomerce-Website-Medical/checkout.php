<?php

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

$conn = mysqli_connect("127.0.0.1", "root", "", "ecommerce_website_medical");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$user_id || !$order_id) {
    header("Location: products.php");
    exit();
}
require_once 'includes/header.php';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
        }
        .checkout-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }
        .order-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Checkout</h1>
        
        <?php
        $query = "SELECT o.*, p.name, oi.quantity, oi.price 
                  FROM orders o 
                  JOIN order_items oi ON o.id = oi.order_id 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE o.id = '$order_id' AND o.user_id = '$user_id'";
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            echo "<p>Error loading order: " . mysqli_error($conn) . "</p>";
            exit();
        }
        ?>
        
        <h3>Order Details (Order ID: <?php echo $order_id; ?>)</h3>
        <?php 
        $total_amount = 0;
        while ($item = mysqli_fetch_assoc($result)) {
            $subtotal = $item['price'] * $item['quantity'];
            $total_amount = $item['total_amount']; // Get total from order
        ?>
            <div class="order-item">
                <p>Product: <?php echo $item['name']; ?></p>
                <p>Quantity: <?php echo $item['quantity']; ?></p>
                <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
            </div>
        <?php } ?>
        <h4>Total Amount: $<?php echo number_format($total_amount, 2); ?></h4>
        
        <?php
        // Check order status
        $status_query = "SELECT status FROM orders WHERE id = '$order_id' AND user_id = '$user_id'";
        $status_result = mysqli_query($conn, $status_query);
        $order_status = mysqli_fetch_assoc($status_result)['status'];
        
        if ($order_status === 'pending') {
        ?>
            <form method="POST">
                <h3>Shipping Address</h3>
                <textarea name="shipping_address" required rows="4" cols="50"></textarea>
                
                <h3>Payment Method</h3>
                <select name="payment_method" required>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="cod">Cash on Delivery</option>
                </select>
                
                <br><br>
                <button type="submit" name="complete_purchase" class="btn">Complete Purchase</button>
            </form>
        <?php } else { ?>
            <p>Order Status: <?php echo $order_status; ?></p>
        <?php } ?>
        
        <?php
        if (isset($_POST['complete_purchase'])) {
            $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
            $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
            
            mysqli_begin_transaction($conn);
            
            try {
                $update_query = "UPDATE orders 
                               SET status = 'processing', 
                                   payment_status = 'paid', 
                                   shipping_address = '$shipping_address', 
                                   payment_method = '$payment_method' 
                               WHERE id = '$order_id' AND user_id = '$user_id'";
                if (!mysqli_query($conn, $update_query)) {
                    throw new Exception("Order update failed: " . mysqli_error($conn));
                }
                
                $stock_query = "UPDATE products p 
                              JOIN order_items oi ON p.id = oi.product_id 
                              SET p.stock = p.stock - oi.quantity 
                              WHERE oi.order_id = '$order_id'";
                if (!mysqli_query($conn, $stock_query)) {
                    throw new Exception("Stock update failed: " . mysqli_error($conn));
                }
                
                // Clear cart only after successful completion (moved from cart.php)
                $clear_cart = "DELETE FROM cart WHERE user_id = '$user_id'";
                if (!mysqli_query($conn, $clear_cart)) {
                    throw new Exception("Cart clearing failed: " . mysqli_error($conn));
                }
                
                mysqli_commit($conn);
                echo "<script>alert('Purchase completed!'); window.location.href='products.php';</script>";
            } catch (Exception $e) {
                mysqli_rollback($conn);
                echo "<p>Error: " . $e->getMessage() . "</p>";
            }
        }
        ?>
    </div>
</body>
</html>