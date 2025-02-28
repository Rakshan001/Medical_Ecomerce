<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect based on role
            switch($user['role']) {
                case 'admin':
                    header("Location: ../admin/dashboard.php");
                    break;
                case 'doctor':
                    header("Location: ../doctor/doctor_dashboard.php");
                    break;
                case 'patient':
                    header("Location: ../patient/dashboard.php");
                    break;
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<link rel="stylesheet" href="<?php echo url('assets/css/auth.css'); ?>">

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-heartbeat auth-icon pulse"></i>
            <h2>Welcome Back</h2>
            <p>Please login to your account</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="form-group remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
                <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="auth-button">
                <span>Login</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? 
                <a href="register.php" class="register-link">
                    Register here
                    <i class="fas fa-user-plus"></i>
                </a>
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>