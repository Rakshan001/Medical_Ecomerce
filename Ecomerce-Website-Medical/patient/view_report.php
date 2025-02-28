<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Check if user is logged in and is a patient
checkAuth();
checkRole(['patient']);

$user_id = $_SESSION['user_id'];
$report_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get report details with doctor information
$report_query = $conn->prepare("
    SELECT lr.*, u.name as doctor_name 
    FROM lab_reports lr 
    JOIN users u ON lr.doctor_id = u.id 
    WHERE lr.id = ? AND lr.patient_id = ?
");
$report_query->bind_param("ii", $report_id, $user_id);
$report_query->execute();
$report = $report_query->get_result()->fetch_assoc();

// Redirect if report not found or doesn't belong to this patient
if (!$report) {
    header("Location: " . url('patient/lab_reports.php'));
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
            padding: 20px 0;
        }
        .container {
            max-width: 900px;
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
        }
        .report-content {
            padding: 30px;
        }
        .file-preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mb-4">
            <a href="lab_reports.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>

        <div class="card">
            <div class="report-header">
                <h2 class="mb-0"><?php echo htmlspecialchars($report['report_title']); ?></h2>
                <div class="text-muted mt-2">
                    <p class="mb-0">
                        <i class="fas fa-user-md"></i> Dr. <?php echo htmlspecialchars($report['doctor_name']); ?>
                    </p>
                    <p class="mb-0">
                        <i class="far fa-calendar-alt"></i> <?php echo date('F d, Y', strtotime($report['created_at'])); ?>
                    </p>
                </div>
            </div>

            <div class="report-content">
                <div class="report-text">
                    <?php echo nl2br(htmlspecialchars($report['report_content'])); ?>
                </div>

                <?php if ($report['report_file']): ?>
                    <div class="file-preview">
                        <h5>Attached File</h5>
                        <?php
                        $file_extension = strtolower(pathinfo($report['report_file'], PATHINFO_EXTENSION));
                        if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): 
                        ?>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?> 