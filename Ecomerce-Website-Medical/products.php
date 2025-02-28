<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

// Handle Buy Now before any output
if (isset($_POST['buy_now'])) {
    $product_id = $_POST['product_id'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    $conn = mysqli_connect("127.0.0.1", "root", "", "ecommerce_website_medical");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    if (!$user_id) {
        echo "<script>alert('Please login to buy'); window.location.href='login.php';</script>";
    } else {
        $product_query = "SELECT price FROM products WHERE id = '$product_id'";
        $product_result = mysqli_query($conn, $product_query);
        $product = mysqli_fetch_assoc($product_result);
        
        $total_amount = $product['price'];
        $order_query = "INSERT INTO orders (user_id, total_amount, status, payment_status) 
                        VALUES ('$user_id', '$total_amount', 'pending', 'pending')";
        mysqli_query($conn, $order_query);
        
        $order_id = mysqli_insert_id($conn);
        $order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                            VALUES ('$order_id', '$product_id', 1, '$total_amount')";
        mysqli_query($conn, $order_item_query);
        
        // Use script redirect to match original behavior
        echo "<script>alert('Order placed! Redirecting to checkout...'); 
              window.location.href='checkout.php?order_id=$order_id';</script>";
        exit();
    }
}

$conn = mysqli_connect("127.0.0.1", "root", "", "ecommerce_website_medical");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Build product query with filters
$query = "SELECT p.*, pc.name as category_name FROM products p 
          LEFT JOIN product_categories pc ON p.category_id = pc.id 
          WHERE p.status = 1";

$conditions = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $conditions[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = intval($_GET['category']);
    $conditions[] = "p.category_id = '$category'";
}
if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
    $max_price = floatval($_GET['max_price']);
    $conditions[] = "p.price <= '$max_price'";
}

if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}
$query .= " ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medical Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #2ecc71;
            margin-bottom: 30px;
            animation: fadeInDown 1s ease;
        }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            animation: fadeIn 1s ease;
        }
        .filter-item {
            flex: 1;
            min-width: 200px;
        }
        .filter-item input, .filter-item select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .filter-item button {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .filter-item button:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px;
        }
        .product-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.2s; }
        .product-card:nth-child(3) { animation-delay: 0.3s; }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .product-card img {
            max-width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        .product-card:hover img {
            transform: scale(1.05);
        }
        .product-card h3 {
            color: #333;
            margin: 15px 0;
            font-size: 20px;
        }
        .product-card p {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            text-transform: uppercase;
        }
        .add-to-cart {
            background: #4CAF50;
            color: white;
        }
        .add-to-cart:hover {
            background: #45a049;
            transform: scale(1.1);
        }
        .buy-now {
            background: #008CBA;
            color: white;
        }
        .buy-now:hover {
            background: #006d9e;
            transform: scale(1.1);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">

        
        <!-- Filters Section -->
        <div class="filters">
            <div class="filter-item">
                <input type="text" id="search" name="search" placeholder="Search products..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            <div class="filter-item">
                <select id="category" name="category">
                    <option value="">All Categories</option>
                    <?php
                    $categories_result = mysqli_query($conn, "SELECT * FROM product_categories WHERE status = 1");
                    while ($cat = mysqli_fetch_assoc($categories_result)) {
                        $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                        echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter-item">
                <input type="number" id="max_price" name="max_price" placeholder="Max Price" 
                       min="0" step="1" 
                       value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
            </div>
            <div class="filter-item">
                <button onclick="applyFilters()">Apply Filters</button>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="product-container">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($product = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="product-card">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                        <p><strong>Stock:</strong> <?php echo $product['stock']; ?></p>
                        <p><strong>Category:</strong> <?php echo $product['category_name'] ?: 'Uncategorized'; ?></p>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn add-to-cart">Add to Cart</button>
                            <button type="submit" name="buy_now" class="btn buy-now">Buy Now</button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='text-align: center; width: 100%;'>No products found matching your criteria.</p>";
            }
            ?>
        </div>
    </div>

    <?php
    // Add to Cart functionality
    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        if (!$user_id) {
            echo "<script>alert('Please login to add items to cart'); window.location.href='login.php';</script>";
        } else {
            $check_query = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) > 0) {
                $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = '$user_id' AND product_id = '$product_id'";
                mysqli_query($conn, $update_query);
            } else {
                $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', 1)";
                mysqli_query($conn, $insert_query);
            }
            echo "<script>alert('Product added to cart!');</script>";
        }
    }
    ?>

    <script>
        function applyFilters() {
            const search = document.getElementById('search').value;
            const category = document.getElementById('category').value;
            const maxPrice = document.getElementById('max_price').value;
            
            let url = 'products.php?';
            if (search) url += 'search=' + encodeURIComponent(search) + '&';
            if (category) url += 'category=' + category + '&';
            if (maxPrice) url += 'max_price=' + maxPrice + '&';
            
            window.location.href = url;
        }

        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    </script>
</body>
</html>