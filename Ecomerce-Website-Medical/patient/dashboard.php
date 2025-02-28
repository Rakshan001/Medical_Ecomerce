<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/header.php';

// Verify user is logged in and is a patient
checkAuth();
checkRole(['patient']);

$user_id = $_SESSION['user_id'];

// Fetch recent orders
$orders_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($orders_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_orders = $stmt->get_result();

// Fetch recent lab reports
$reports_sql = "SELECT lr.*, u.name as doctor_name 
                FROM lab_reports lr 
                JOIN users u ON lr.doctor_id = u.id 
                WHERE lr.patient_id = ? 
                ORDER BY lr.created_at DESC LIMIT 5";
$stmt = $conn->prepare($reports_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_reports = $stmt->get_result();

// Fetch user details
$user_sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_details = $stmt->get_result()->fetch_assoc();
?>

<!-- Add page-specific CSS -->
<link rel="stylesheet" href="<?php echo url('assets/css/dashboard.css'); ?>">

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard - Medical E-commerce</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/patient_dashboard.css">
</head>
  
<style>
        .sidebar {
    width: 250px;
    background: linear-gradient(180deg, #2c3e50 0%, #3498db 100%);
    padding: 20px;
    box-shadow: 4px 0 10px rgba(0,0,0,0.2);
    position: fixed;
    height: 100vh;
    transition: width 0.3s ease;
    overflow-y: auto;
}

.sidebar:hover {
    width: 270px;
}

.sidebar-header {
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    margin-bottom: 20px;
}

.sidebar-title {
    color: #fff;
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    animation: fadeIn 0.5s ease-in;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    opacity: 0;
    animation: slideIn 0.5s ease forwards;
}

.nav-item i {
    margin-right: 12px;
    width: 20px;
    text-align: center;
}

.nav-item span {
    font-size: 1rem;
}

/* Add delay to each nav item for staggered animation */
.nav-item:nth-child(1) { animation-delay: 0.1s; }
.nav-item:nth-child(2) { animation-delay: 0.2s; }
.nav-item:nth-child(3) { animation-delay: 0.3s; }
.nav-item:nth-child(4) { animation-delay: 0.4s; }
.nav-item:nth-child(5) { animation-delay: 0.5s; }
.nav-item:nth-child(6) { animation-delay: 0.6s; }

.nav-item:hover {
    background: rgba(255,255,255,0.1);
    transform: translateX(5px);
}

.nav-item.active {
    background: rgba(255,255,255,0.2);
    box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Adjust main content margin to prevent overlap */
.main-content {
    flex: 1;
    padding: 20px;
    margin-left: 250px;
}

/* Responsive design */
@media (max-width: 768px) {
    .sidebar {
        width: 80px;
    }
    
    .sidebar:hover {
        width: 250px;
    }
    
    .nav-item span {
        display: none;
    }
    
    .sidebar:hover .nav-item span {
        display: inline;
    }
    
    .main-content {
        margin-left: 80px;
    }
}
    </style>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
                <!-- Replace the existing sidebar div with this -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2 class="sidebar-title">
            <i class="fas fa-user-md me-2"></i>Patient Panel
        </h2>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="../orders.php" class="nav-item">
            <i class="fas fa-shopping-bag"></i>
            <span>My Orders</span>
        </a>
        <a href="lab_reports.php" class="nav-item active">
            <i class="fas fa-file-medical"></i>
            <span>Lab Reports</span>
        </a>
        <a href="../cart.php" class="nav-item">
            <i class="fas fa-shopping-cart"></i>
            <span>Shopping Cart</span>
        </a>
        <a href="profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>
        <a href="../auth/logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>


        <!-- Main Content -->
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                <p>Manage your medical orders and reports</p>
            </div>

            <!-- Dashboard Overview -->
            <div class="dashboard-grid">
                <!-- Profile Summary -->
                <div class="dashboard-card">
                    <h3>Profile Information</h3>
                    <div class="profile-info">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user_details['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_details['phone']); ?></p>
                        <a href="profile.php" class="btn">Edit Profile</a>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="dashboard-card">
                    <h3>Recent Orders</h3>
                    <div class="recent-orders">
                        <?php if ($recent_orders->num_rows > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <a href="orders.php" class="btn">View All Orders</a>
                        <?php else: ?>
                            <p>No orders found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Lab Reports -->
                <div class="dashboard-card">
                    <h3>Recent Lab Reports</h3>
                    <div class="recent-reports">
                        <?php if ($recent_reports->num_rows > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Report Title</th>
                                        <th>Doctor</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($report = $recent_reports->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($report['report_title']); ?></td>
                                            <td><?php echo htmlspecialchars($report['doctor_name']); ?></td>
                                            <td>
                                                <a href="lab_reports.php?id=<?php echo $report['id']; ?>" class="btn-small">View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <a href="lab_reports.php" class="btn">View All Reports</a>
                        <?php else: ?>
                            <p>No lab reports found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
require_once '../includes/footer.php';
?>
