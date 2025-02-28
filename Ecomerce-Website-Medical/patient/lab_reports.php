<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/header.php';

// Verify user is logged in and is a patient
checkAuth();
checkRole(['patient']);

$user_id = $_SESSION['user_id'];

// Check if a specific report ID is requested
if (isset($_GET['id'])) {
    // Fetch single report with doctor details
    $report_sql = "SELECT lr.*, u.name as doctor_name, u.email as doctor_email 
                   FROM lab_reports lr 
                   JOIN users u ON lr.doctor_id = u.id 
                   WHERE lr.id = ? AND lr.patient_id = ?";
    $stmt = $conn->prepare($report_sql);
    $stmt->bind_param("ii", $_GET['id'], $user_id);
    $stmt->execute();
    $report = $stmt->get_result()->fetch_assoc();

    // If no report found or doesn't belong to user, redirect to all reports
    if (!$report) {
        header("Location: lab_reports.php");
        exit();
    }
} else {
    // Fetch all reports for the patient
    $reports_sql = "SELECT lr.*, u.name as doctor_name 
                    FROM lab_reports lr 
                    JOIN users u ON lr.doctor_id = u.id 
                    WHERE lr.patient_id = ? 
                    ORDER BY lr.created_at DESC";
    $stmt = $conn->prepare($reports_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $reports = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lab Reports - Medical E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .report-details {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .reports-table {
            width: 100%;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-small {
            padding: 5px 10px;
            margin: 2px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-small:hover {
            background: #0056b3;
            color: white;
        }
        .file-preview {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
    </style>

    
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
</head>
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
            <?php if (isset($report)): ?>
                <!-- Single Report View -->
                <div class="report-details">
                    <h2><?php echo htmlspecialchars($report['report_title']); ?></h2>
                    <div class="report-info">
                        <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($report['created_at'])); ?></p>
                        <p><strong>Doctor:</strong> <?php echo htmlspecialchars($report['doctor_name']); ?></p>
                        <p><strong>Doctor Email:</strong> <?php echo htmlspecialchars($report['doctor_email']); ?></p>
                    </div>
                    <div class="report-content">
                        <h3>Report Details</h3>
                        <div class="report-text">
                            <?php echo nl2br(htmlspecialchars($report['report_content'])); ?>
                        </div>
                        
                        <?php if (!empty($report['file_path'])): ?>
                            <div class="file-preview">
                                <h4>Attached File</h4>
                                <?php
                                $file_extension = strtolower(pathinfo($report['file_path'], PATHINFO_EXTENSION));
                                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): 
                                ?>
                                    <img src="<?php echo '../' . $report['file_path']; ?>" 
                                         class="img-fluid" alt="Report Image">
                                <?php else: ?>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-medical fa-2x me-2"></i>
                                        <span><?php echo basename($report['file_path']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-3">
                                    <a href="<?php echo '../' . $report['file_path']; ?>" 
                                       class="btn btn-primary" 
                                       download>
                                        <i class="fas fa-download"></i> Download File
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="lab_reports.php" class="btn btn-secondary mt-3">Back to All Reports</a>
                </div>
            <?php else: ?>
                <!-- Reports List View -->
                <div class="reports-list">
                    <h2>My Lab Reports</h2>
                    <?php if ($reports->num_rows > 0): ?>
                        <table class="table table-hover reports-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Report Title</th>
                                    <th>Doctor</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($report = $reports->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($report['report_title']); ?></td>
                                        <td><?php echo htmlspecialchars($report['doctor_name']); ?></td>
                                        <td>
                                            <a href="lab_reports.php?id=<?php echo $report['id']; ?>" 
                                               class="btn-small">View</a>
                                            <?php if (!empty($report['file_path'])): ?>
                                                <a href="<?php echo '../' . $report['file_path']; ?>" 
                                                   class="btn-small" 
                                                   download>Download</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No lab reports found.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?> 