<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

// Check if user is logged in and is a doctor
checkAuth();
checkRole(['doctor']);

$doctor_id = $_SESSION['user_id'];
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;

if (!$patient_id) {
    header("Location: " . url('doctor/doctor_dashboard.php'));
    exit();
}

// Verify patient exists
$patient_query = "SELECT name FROM users WHERE id = '$patient_id' AND role = 'patient'";
$patient_result = $conn->query($patient_query);
$patient = $patient_result->fetch_assoc();

if (!$patient) {
    header("Location: " . url('doctor/doctor_dashboard.php'));
    exit();
}

// Handle report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_report'])) {
    $report_title = $conn->real_escape_string($_POST['report_title']);
    $report_content = $conn->real_escape_string($_POST['report_content']);
    $report_file = '';

    if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/reports/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['report_file']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('report_') . '.' . $file_extension;
        $report_file = 'uploads/reports/' . $file_name;
        
        if (!move_uploaded_file($_FILES['report_file']['tmp_name'], '../' . $report_file)) {
            $error = "Failed to upload file.";
        }
    }

    if (!isset($error)) {
        $insert_query = "INSERT INTO lab_reports (patient_id, doctor_id, report_title, report_content, file_path) 
                         VALUES ('$patient_id', '$doctor_id', '$report_title', '$report_content', '$report_file')";
        if ($conn->query($insert_query)) {
            header("Location: " . url('doctor/doctor_dashboard.php?message=Report added successfully'));
            exit();
        } else {
            $error = "Failed to add report: " . $conn->error;
        }
    }
}

require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Add Report for <?php echo htmlspecialchars($patient['name']); ?></h1>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="report_title" class="form-label">Report Title</label>
                        <input type="text" id="report_title" name="report_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="report_content" class="form-label">Report Content</label>
                        <textarea id="report_content" name="report_content" rows="5" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="report_file" class="form-label">Upload File (optional)</label>
                        <input type="file" id="report_file" name="report_file" class="form-control">
                    </div>
                    <button type="submit" name="add_report" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Report
                    </button>
                    <a href="<?php echo url('doctor/doctor_dashboard.php'); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>