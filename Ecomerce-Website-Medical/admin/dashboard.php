<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Perform auth checks before any output
checkAuth();
checkRole(['admin']);

// Now include header which will output HTML
require_once '../includes/header.php';

// Get counts for dashboard
$sql_orders = "SELECT COUNT(*) as order_count FROM orders";
$sql_products = "SELECT COUNT(*) as product_count FROM products";
$sql_doctors = "SELECT COUNT(*) as doctor_count FROM users WHERE role='doctor'";
$sql_patients = "SELECT COUNT(*) as patient_count FROM users WHERE role='patient'";

$orders_count = $conn->query($sql_orders)->fetch_assoc()['order_count'];
$products_count = $conn->query($sql_products)->fetch_assoc()['product_count'];
$doctors_count = $conn->query($sql_doctors)->fetch_assoc()['doctor_count'];
$patients_count = $conn->query($sql_patients)->fetch_assoc()['patient_count'];

// Get recent orders
$sql_recent_orders = "SELECT o.*, u.name as user_name 
                     FROM orders o 
                     JOIN users u ON o.user_id = u.id 
                     ORDER BY o.created_at DESC LIMIT 5";
$recent_orders = $conn->query($sql_recent_orders);
?>

<link rel="stylesheet" href="<?php echo url('assets/css/admin.css'); ?>">
<link rel="stylesheet" href="<?php echo url('assets/css/admin_sidebar.css'); ?>">

<div class="admin-container">
    <!-- Sidebar -->
    <?php include '../includes/admin_sidebar.php'; ?>


    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1>Dashboard Overview</h1>
            <div class="admin-user">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="<?php echo url('auth/logout.php'); ?>" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon orders">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-details">
                    <h3>Total Orders</h3>
                    <p class="stat-number"><?php echo $orders_count; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon products">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-details">
                    <h3>Products</h3>
                    <p class="stat-number"><?php echo $products_count; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon doctors">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-details">
                    <h3>Doctors</h3>
                    <p class="stat-number"><?php echo $doctors_count; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon patients">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3>Patients</h3>
                    <p class="stat-number"><?php echo $patients_count; ?></p>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="content-card">
            <div class="card-header">
                <h2>Recent Orders</h2>
                <a href="<?php echo url('admin/orders/manage_orders.php'); ?>" class="view-all">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="<?php echo url('admin/orders/order_details.php?id=' . $order['id']); ?>" 
                                       class="action-btn">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>