<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';

// Perform auth checks
checkAuth();
checkRole(['admin']);

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$order_id) {
    header("Location: " . url('admin/orders/manage_orders.php'));
    exit();
}

// Get order details
$order_query = "SELECT o.*, u.name, u.email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = '$order_id'";
$order_result = $conn->query($order_query);
$order = $order_result->fetch_assoc();

if (!$order) {
    header("Location: " . url('admin/orders/manage_orders.php'));
    exit();
}

// Get order items
$items_query = "SELECT oi.*, p.name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = '$order_id'";
$items_result = $conn->query($items_query);

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="<?php echo url('assets/css/admin.css'); ?>">

<div class="admin-container">
    <!-- Sidebar (same as dashboard) -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clinic-medical"></i>
            <h2>Admin Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="<?php echo url('admin/dashboard.php'); ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <div class="nav-group">
                <h3>Products</h3>
                <a href="<?php echo url('admin/products/manage_products.php'); ?>">
                    <i class="fas fa-box"></i> Manage Products
                </a>
                <a href="<?php echo url('admin/products/add_product.php'); ?>">
                    <i class="fas fa-plus"></i> Add Product
                </a>
                <a href="<?php echo url('admin/products/categories.php'); ?>">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </div>
            <div class="nav-group">
                <h3>Orders</h3>
                <a href="<?php echo url('admin/orders/manage_orders.php'); ?>" class="active">
                    <i class="fas fa-shopping-cart"></i> Manage Orders
                </a>
            </div>
            <div class="nav-group">
                <h3>Doctors</h3>
                <a href="<?php echo url('admin/doctors/manage_doctors.php'); ?>">
                    <i class="fas fa-user-md"></i> Manage Doctors
                </a>
                <a href="<?php echo url('admin/doctors/add_doctor.php'); ?>">
                    <i class="fas fa-user-plus"></i> Add Doctor
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1>Order Details - #<?php echo $order['id']; ?></h1>
            <div class="admin-user">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="<?php echo url('auth/logout.php'); ?>" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2>Order Information</h2>
            </div>
            <div class="card-body">
                <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                <p><strong>Customer Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Status:</strong> 
                    <span class="status-badge <?php echo strtolower($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </p>
                <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
                <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address'] ?: 'Not provided'); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?: 'Not provided'); ?></p>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2>Purchased Items</h2>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
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

        <a href="<?php echo url('admin/orders/manage_orders.php'); ?>" class="action-btn back-btn">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </main>
</div>

<?php require_once '../../includes/footer.php'; ?>