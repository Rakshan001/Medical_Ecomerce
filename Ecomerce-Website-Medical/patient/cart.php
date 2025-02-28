<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/header.php';

// Verify user is logged in and is a patient
checkAuth();
checkRole(['patient']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
        }
        
        .cart-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .cart-header {
            background: #2ecc71;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
        
        .cart-items {
            padding: 20px;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background: #f9f9f9;
            transform: translateY(-2px);
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .item-price {
            color: #666;
            font-weight: bold;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        
        .quantity-btn {
            background: #3498db;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .quantity-btn:hover {
            background: #2980b9;
            transform: scale(1.1);
        }
        
        .remove-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .remove-btn:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }
        
        .cart-footer {
            padding: 20px;
            background: #f9f9f9;
            text-align: right;
        }
        
        .total {
            font-size: 20px;
            color: #333;
            margin: 0 0 15px 0;
        }
        
        .checkout-btn {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            Your Shopping Cart
        </div>
        
        <div class="cart-items">
            <?php
            $total = 0;
            $query = "SELECT c.*, p.name, p.price, p.image 
                     FROM cart c 
                     JOIN products p ON c.product_id = p.id 
                     WHERE c.user_id = '$user_id'";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                while ($item = mysqli_fetch_assoc($result)) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="item-image">
                        <div class="item-details">
                            <h3 class="item-name"><?php echo $item['name']; ?></h3>
                            <p class="item-price">$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></p>
                            
                            <form method="POST" class="quantity-controls">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="decrease" class="quantity-btn">-</button>
                                <span><?php echo $item['quantity']; ?></span>
                                <button type="submit" name="increase" class="quantity-btn">+</button>
                            </form>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="remove" class="remove-btn">Remove</button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="empty-cart">Your cart is empty!</div>';
            }
            ?>
        </div>
        
        <?php if (mysqli_num_rows($result) > 0) { ?>
        <div class="cart-footer">
            <p class="total">Total: $<?php echo number_format($total, 2); ?></p>
            <form method="POST" action="process_order.php">
                <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                <button type="submit" name="place_order" class="checkout-btn">Proceed to Checkout</button>
            </form>
        </div>
        <?php } ?>
    </div>

    <?php
    // Handle quantity updates and removal
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cart_id = $_POST['cart_id'];
        
        if (isset($_POST['increase'])) {
            $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE id = '$cart_id' AND user_id = '$user_id'";
            mysqli_query($conn, $update_query);
            echo "<script>window.location.href='cart.php';</script>";
        }
        
        if (isset($_POST['decrease'])) {
            $check_query = "SELECT quantity FROM cart WHERE id = '$cart_id'";
            $check_result = mysqli_query($conn, $check_query);
            $current = mysqli_fetch_assoc($check_result);
            
            if ($current['quantity'] > 1) {
                $update_query = "UPDATE cart SET quantity = quantity - 1 WHERE id = '$cart_id' AND user_id = '$user_id'";
                mysqli_query($conn, $update_query);
            } else {
                $delete_query = "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'";
                mysqli_query($conn, $delete_query);
            }
            echo "<script>window.location.href='cart.php';</script>";
        }
        
        if (isset($_POST['remove'])) {
            $delete_query = "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'";
            mysqli_query($conn, $delete_query);
            echo "<script>window.location.href='cart.php';</script>";
        }
    }
    ?>
</body>
</html>