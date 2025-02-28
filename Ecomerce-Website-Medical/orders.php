<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

// Check if user is logged in and is a patient
checkAuth();
checkRole(['patient']);

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">

<div class="container">
    <h1>Your Orders</h1>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo ucfirst($order['status']); ?></td>
                            <td><?php echo ucfirst($order['payment_status']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo url('order_details.php?id=' . $order['id']); ?>" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>You haven't placed any orders yet.</p>
    <?php endif; ?>
    
    <a href="<?php echo url('products.php'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<?php require_once 'includes/footer.php'; ?>

<style>
    .container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .table th, .table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
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
    .btn-primary {
        background: #3498db;
        color: white;
        border: none;
    }
    .btn-primary:hover {
        background: #2980b9;
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