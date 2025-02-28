<?php
require_once __DIR__ . '/config.php';
// No need to start session here as it's already handled in config.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Your Health, Our Priority</title>
    <link rel="stylesheet" href="<?php echo url('assets/css/header-footer.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Add your page-specific CSS files after this line -->
</head>
<body>
    <header class="main-header">
        <div class="announcement-bar">
            <p>Free Delivery on Orders Above $99 | 24/7 Support</p>
        </div>
        <nav class="nav-container">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-heartbeat pulse"></i>
                </div>
                <a href="<?php echo url('index.php'); ?>" class="brand-name">
                    Medi<span>Care</span>
                </a>
            </div>
            
            <div class="nav-links">
                <a href="<?php echo url('products.php'); ?>" class="nav-link">
                    <i class="fas fa-capsules"></i> Products
                </a>
                <a href="<?php echo url('services.php'); ?>" class="nav-link">
                    <i class="fas fa-stethoscope"></i> Services
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <a href="<?php echo url('admin/dashboard.php'); ?>" class="nav-link">
                            <i class="fas fa-user-shield"></i> Admin Panel
                        </a>
                    <?php elseif ($_SESSION['user_role'] === 'doctor'): ?>
                        <a href="<?php echo url('doctor/doctor_dashboard.php'); ?>" class="nav-link">
                            <i class="fas fa-user-md"></i> Doctor Panel
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('patient/dashboard.php'); ?>" class="nav-link">
                            <i class="fas fa-user-circle"></i> Dashboard
                        </a>
                        <a href="<?php echo url('orders.php'); ?>" class="nav-link">
                            <i class="fas fa-shopping-bag"></i> Orders
                        </a>
                        <a href="<?php echo url('patient/lab_reports.php'); ?>" class="nav-link">
                            <i class="fas fa-file-medical"></i> Reports
                        </a>
                        <a href="<?php echo url('cart.php'); ?>" class="nav-link cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count">0</span>
                        </a>
                    <?php endif; ?>
                    
                    <div class="user-menu">
                        <span class="welcome-text">
                            Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </span>
                        <a href="<?php echo url('auth/logout.php'); ?>" class="nav-link logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?php echo url('auth/login.php'); ?>" class="nav-link login-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
    </header>
    <main>

    <!-- Add this before closing main tag -->
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const navLinks = document.querySelector('.nav-links');
            const body = document.body;
            
            if(mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    this.classList.toggle('active');
                    navLinks.classList.toggle('active');
                    body.classList.toggle('menu-open');
                });
            }

            // Add animation delay to menu items
            const navItems = document.querySelectorAll('.nav-link');
            navItems.forEach((item, index) => {
                item.style.setProperty('--item-index', index);
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.nav-links') && 
                    !event.target.closest('.mobile-menu-btn') && 
                    navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                    body.classList.remove('menu-open');
                }
            });
        });
    </script>
