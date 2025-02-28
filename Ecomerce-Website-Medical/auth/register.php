<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);
    
    if ($result->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $sql = "INSERT INTO users (name, email, phone, password, role) 
                VALUES ('$name', '$email', '$phone', '$password', '$role')";
        
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<link rel="stylesheet" href="<?php echo url('assets/css/auth.css'); ?>">


<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-user-plus auth-icon pulse"></i>
            <h2>Create Account</h2>
            <p>Join our healthcare community</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" placeholder="Enter your phone number" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Create password" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group select-group">
                    <i class="fas fa-user-tag"></i>
                    <select name="role" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="auth-button">
                <span>Create Account</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? 
                <a href="login.php" class="register-link">
                    Login here
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>