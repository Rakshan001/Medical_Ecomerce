<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';

// Perform auth checks
checkAuth();
checkRole(['admin']);

// Handle order status update
if (isset($_POST['mark_completed'])) {
    $order_id = intval($_POST['order_id']);
    $update_query = "UPDATE orders SET status = 'completed' WHERE id = '$order_id'";
    if ($conn->query($update_query)) {
        $success_message = "Order #$order_id marked as completed successfully!";
    } else {
        $error_message = "Failed to update order status: " . $conn->error;
    }
}

// Get all orders from the order_details view
$sql = "SELECT * FROM order_details ORDER BY order_date DESC";
$result = $conn->query($sql);

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="<?php echo url('assets/css/admin.css'); ?>">
<link rel="stylesheet" href="<?php echo url('assets/css/admin_sidebar.css'); ?>">


<div class="admin-container">
    <!-- Sidebar -->
    <?php include '../../includes/admin_sidebar.php'; ?>
    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1>Manage Orders</h1>
            <div class="admin-user">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="<?php echo url('auth/logout.php'); ?>" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="content-card">
            <div class="card-header">
                <h2>All Orders</h2>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($order['order_status']); ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo ucfirst($order['payment_status']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <a href="<?php echo url('admin/orders/order_details.php?id=' . $order['order_id']); ?>" 
                                       class="action-btn">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if ($order['order_status'] === 'processing'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="mark_completed" 
                                                    class="action-btn success-btn" 
                                                    onclick="return confirm('Mark this order as completed?');">
                                                <i class="fas fa-check"></i> Mark Completed
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<style>
    
</style>

<?php require_once '../../includes/footer.php'; ?>