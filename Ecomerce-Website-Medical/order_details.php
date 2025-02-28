<?php



require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

// Check if user is logged in and is a patient
checkAuth();
checkRole(['patient']);

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header("Location: " . url('orders.php'));
    exit();
}

// Get order details
$order_query = "SELECT o.* 
                FROM orders o 
                WHERE o.id = '$order_id' AND o.user_id = '$user_id'";
$order_result = $conn->query($order_query);
$order = $order_result->fetch_assoc();

if (!$order) {
    header("Location: " . url('orders.php'));
    exit();
}

// Get order items
$items_query = "SELECT oi.*, p.name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = '$order_id'";
$items_result = $conn->query($items_query);

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">

<div class="container">
    <h1>Order Details - #<?php echo $order['id']; ?></h1>
    
    <div class="order-info">
        <h3>Order Information</h3>
        <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
        <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
        <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address'] ?: 'Not provided'); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?: 'Not provided'); ?></p>
    </div>
    
    <div class="order-items">
        <h3>Purchased Items</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <a href="<?php echo url('orders.php'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<?php require_once 'includes/footer.php'; ?>

<style>
    .container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
    }
    .order-info, .order-items {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .order-info p {
        margin: 10px 0;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th, .table td {
        padding: 12px;
        border: 1px solid #ddd;
    }
    .table th {
        background: #2ecc71;
        color: white;
    }
    .btn {
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
    }
    .btn-secondary:hover {
        background: #5a6268;
    }
</style>