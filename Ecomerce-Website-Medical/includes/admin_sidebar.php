<!-- Sidebar Toggle Button -->
<button id="sidebarToggle" class="sidebar-toggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <i class="fas fa-clinic-medical"></i>
        <h2>MediAdmin</h2>
    </div>
    <nav class="sidebar-nav">
        <a href="<?php echo url('admin/dashboard.php'); ?>" class="<?php echo strpos($current_page, 'dashboard.php') !== false ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
        <div class="nav-group">
            <h3>Products</h3>
            <a href="<?php echo url('admin/products/manage_products.php'); ?>" class="<?php echo strpos($current_page, 'manage_products.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> <span>Manage Products</span>
            </a>
            <a href="<?php echo url('admin/products/add_product.php'); ?>" class="<?php echo strpos($current_page, 'add_product.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-plus"></i> <span>Add Product</span>
            </a>
            <a href="<?php echo url('admin/products/categories.php'); ?>" class="<?php echo strpos($current_page, 'categories.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> <span>Categories</span>
            </a>
        </div>
        <div class="nav-group">
            <h3>Orders</h3>
            <a href="<?php echo url('admin/orders/manage_orders.php'); ?>" class="<?php echo strpos($current_page, 'manage_orders.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> <span>Manage Orders</span>
            </a>
        </div>
        <div class="nav-group">
            <h3>Doctors</h3>
            <a href="<?php echo url('admin/doctors/manage_doctors.php'); ?>" class="<?php echo strpos($current_page, 'manage_doctors.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i> <span>Manage Doctors</span>
            </a>
            <a href="<?php echo url('admin/doctors/add_doctor.php'); ?>" class="<?php echo strpos($current_page, 'add_doctor.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-user-plus"></i> <span>Add Doctor</span>
            </a>
        </div>
        <div class="nav-group">
            <h3>System</h3>
            <a href="<?php echo url('admin/settings.php'); ?>" class="<?php echo strpos($current_page, 'settings.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
            <a href="<?php echo url('logout.php'); ?>">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>

<!-- Sidebar Overlay for Mobile -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<!-- JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById("adminSidebar");
        const overlay = document.getElementById("sidebarOverlay");
        const toggleBtn = document.getElementById("sidebarToggle");

        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("active");
            overlay.classList.toggle("active");
        });

        overlay.addEventListener("click", function () {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    });
</script>

<!-- CSS -->
<style>
    aside{
        margin-top: 110px;
    }
    .sidebar-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        z-index: 1100;
        font-size: 18px;
        border-radius: 5px;
    }

    @media (max-width: 768px) {
        .admin-sidebar {
            transform: translateX(-100%);
            position: fixed;
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
            width: 250px;
            height: 100%;
            background: #343a40;
            color: white;
            padding-top: 20px;
        }

        .admin-sidebar.active {
            transform: translateX(0);
        }

        #sidebarOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out;
        }

        #sidebarOverlay.active {
            opacity: 1;
            visibility: visible;
        }
    }
</style>
