<?php
require_once 'includes/config.php';  // Always include config first
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
require_once 'includes/header.php';

// Check if user is logged in, if not redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$user_name = $_SESSION['user_name'];

// Redirect to appropriate dashboard based on role
switch($user_role) {
    case 'admin':
        header("Location: admin/dashboard.php");
        break;
    case 'doctor':
        header("Location: doctor/dashboard.php");
        break;
    case 'patient':
        // For patients, we'll show the main shop page
        // You can also redirect to patient dashboard if preferred
        // header("Location: patient/dashboard.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Medical E-commerce</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <header>
        <nav>
            <div class="nav-brand">Medical E-commerce</div>
            <div class="nav-items">
                <a href="products.php">Products</a>
                <a href="patient/cart.php">Cart</a>
                <a href="patient/orders.php">My Orders</a>
                <a href="patient/lab_reports.php">Lab Reports</a>
                <div class="user-menu">
                    Welcome, <?php echo htmlspecialchars($user_name); ?>
                    <a href="auth/logout.php">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Welcome to Medical E-commerce</h1>
            <p>Find all your medical supplies in one place</p>
        </section>

        <section class="featured-products">
            <h2>Featured Products</h2>
            <?php
            // Fetch featured products from database
            $sql = "SELECT * FROM products WHERE status = 1 LIMIT 4";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo '<div class="product-grid">';
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                        <a href="products.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                    </div>
                    <?php
                }
                echo '</div>';
            } else {
                echo '<p>No products found</p>';
            }
            ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Medical E-commerce. All rights reserved.</p>
    </footer>
</body>
</html>
<?php
        break;
}

require_once 'includes/footer.php';
?>
