<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Check if user is logged in and is a doctor
checkAuth();
checkRole(['doctor']);

$user_id = $_SESSION['user_id'];

// Build patient query with search
$patients_query = "SELECT id, name, email, phone, created_at 
                   FROM users 
                   WHERE role = 'patient'";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $patients_query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}
$patients_query .= " ORDER BY created_at DESC";
$patients_result = $conn->query($patients_query);

require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
        }
        .dashboard-header {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn i {
            margin-right: 5px;
        }
        .search-form {
            max-width: 400px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h1 class="h2">Doctor Dashboard</h1>
            <div>
                <span class="me-3">Welcome, Dr. <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="<?php echo url('auth/logout.php'); ?>" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Patients</h3>
                
                <!-- Search Form -->
                <form method="GET" class="search-form d-flex">
                    <input type="text" name="search" class="form-control me-2" 
                           placeholder="Search by name or email" 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                </form>

                <?php if ($patients_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($patient = $patients_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $patient['id']; ?></td>
                                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($patient['created_at'])); ?></td>
                                        <td>
                                            <a href="<?php echo url('doctor/add_report.php?patient_id=' . urlencode($patient['id'])); ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Add Lab Report
                                            </a>
                                            <a href="<?php echo url('doctor/doctor_dashboard.php?view_patient=' . urlencode($patient['id'])); ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No patients found.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_GET['view_patient'])): 
            $patient_id = intval($_GET['view_patient']);
            
            // Use prepared statement for security
            $patient_query = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'patient'");
            $patient_query->bind_param("i", $patient_id);
            $patient_query->execute();
            $patient = $patient_query->get_result()->fetch_assoc();
            
            // Get reports for this patient
            $reports_query = $conn->prepare("SELECT * FROM lab_reports WHERE patient_id = ? ORDER BY created_at DESC");
            $reports_query->bind_param("i", $patient_id);
            $reports_query->execute();
            $reports_result = $reports_query->get_result();
        ?>
            <div class="card mt-4">
                <div class="card-body">
                    <h3 class="card-title">Patient Details: <?php echo htmlspecialchars($patient['name']); ?></h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?></p>
                    
                    <h4>Lab Reports</h4>
                    <?php if ($reports_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($report = $reports_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($report['report_title']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                                            <td>
                                                <a href="<?php echo url('doctor/lab_reports.php?id=' . urlencode($report['id'])); ?>" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if ($report['report_file']): ?>
                                                    <a href="<?php echo url($report['report_file']); ?>" 
                                                       class="btn btn-secondary btn-sm" 
                                                       target="_blank">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No reports found for this patient.</p>
                    <?php endif; ?>
                    
                    <a href="<?php echo url('doctor/add_report.php?patient_id=' . urlencode($patient_id)); ?>" 
                       class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Add New Report
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>