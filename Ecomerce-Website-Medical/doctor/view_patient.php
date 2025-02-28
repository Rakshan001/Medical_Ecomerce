<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Check if user is logged in and is a doctor
checkAuth();
checkRole(['doctor']);

$user_id = $_SESSION['user_id'];
$report_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get report details with patient information
$report_query = $conn->prepare("
    SELECT r.*, u.name as patient_name, u.email as patient_email, u.phone as patient_phone
    FROM lab_reports r
    JOIN users u ON r.patient_id = u.id
    WHERE r.id = ? AND r.doctor_id = ?
");

$report_query->bind_param("ii", $report_id, $user_id);
$report_query->execute();
$report = $report_query->get_result()->fetch_assoc();

// Redirect if report not found or doesn't belong to this doctor
if (!$report) {
    header("Location: " . url('doctor/doctor_dashboard.php'));
    exit();
}

require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Report Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .report-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            border-bottom: 1px solid #dee2e6;
        }
        .report-content {
            padding: 30px;
            white-space: pre-wrap;
        }
        .patient-info {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .file-preview {
            max-width: 100%;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .btn-group {
            margin-top: 20px;
        }
        .timestamp {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="report-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><?php echo htmlspecialchars($report['report_title']); ?></h2>
                    <span class="timestamp">
                        <i class="far fa-calendar-alt"></i> 
                        <?php echo date('F d, Y h:i A', strtotime($report['created_at'])); ?>
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="patient-info">
                    <h4>Patient Information</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Name:</strong><br> 
                                <?php echo htmlspecialchars($report['patient_name']); ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Email:</strong><br>
                                <?php echo htmlspecialchars($report['patient_email']); ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Phone:</strong><br>
                                <?php echo htmlspecialchars($report['patient_phone']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="report-content">
                    <h4>Report Content</h4>
                    <div class="content-text">
                        <?php echo nl2br(htmlspecialchars($report['report_content'])); ?>
                    </div>

                    <?php if ($report['report_file']): ?>
                        <div class="file-preview">
                            <h4>Attached File</h4>
                            <?php
                            $file_extension = pathinfo($report['report_file'], PATHINFO_EXTENSION);
                            $file_extension = strtolower($file_extension);
                            ?>

                            <?php if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img src="<?php echo url($report['report_file']); ?>" 
                                     class="img-fluid" alt="Report Image">
                            <?php else: ?>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-medical fa-2x me-2"></i>
                                    <span><?php echo basename($report['report_file']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <a href="<?php echo url($report['report_file']); ?>" 
                                   class="btn btn-primary" 
                                   download>
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="btn-group">
                    <a href="<?php echo url('doctor/edit_report.php?id=' . $report_id); ?>" 
                       class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Edit Report
                    </a>
                    <a href="<?php echo url('doctor/doctor_dashboard.php?view_patient=' . $report['patient_id']); ?>" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Patient
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
