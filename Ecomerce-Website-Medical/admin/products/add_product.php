<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';

checkAuth();
checkRole(['admin']);

// Fetch categories for dropdown
$categories = $conn->query("SELECT * FROM product_categories WHERE status = 1 ORDER BY name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];
    
    // Handle main product image
    $main_image = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['main_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = 'product_' . uniqid() . '.' . $filetype;
            $upload_path = '../../uploads/products/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_path . $new_filename)) {
                $main_image = 'uploads/products/' . $new_filename;
            }
        }
    }
    
    // Insert product
    $sql = "INSERT INTO products (category_id, name, description, price, stock, image, status) 
            VALUES ($category_id, '$name', '$description', $price, $stock, " . 
            ($main_image ? "'$main_image'" : "NULL") . ", 1)";
            
    if ($conn->query($sql)) {
        $product_id = $conn->insert_id;
        
        // Handle additional product images
        if (isset($_FILES['additional_images'])) {
            $upload_path = '../../uploads/products/';
            
            foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['additional_images']['error'][$key] == 0) {
                    $filename = $_FILES['additional_images']['name'][$key];
                    $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                    
                    if (in_array(strtolower($filetype), ['jpg', 'jpeg', 'png', 'webp'])) {
                        $new_filename = 'product_' . uniqid() . '.' . $filetype;
                        
                        if (move_uploaded_file($tmp_name, $upload_path . $new_filename)) {
                            $image_path = 'uploads/products/' . $new_filename;
                            $sql = "INSERT INTO product_images (product_id, image_path) 
                                   VALUES ($product_id, '$image_path')";
                            $conn->query($sql);
                        }
                    }
                }
            }
        }
        
        $success = "Product added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="<?php echo url('assets/css/admin.css'); ?>">

<div class="admin-container">
<aside class="admin-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clinic-medical"></i>
            <h2>Admin Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="<?php echo url('admin/dashboard.php'); ?>" class="active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <div class="nav-group">
                <h3>Products</h3>
                <a href="<?php echo url('admin/products/manage_products.php'); ?>">
                    <i class="fas fa-box"></i> Manage Products
                </a>
                <a href="<?php echo url('admin/products/add_product.php'); ?>">
                    <i class="fas fa-plus"></i> Add Product
                </a>
                <a href="<?php echo url('admin/products/categories.php'); ?>">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </div>
            <div class="nav-group">
                <h3>Orders</h3>
                <a href="<?php echo url('admin/orders/manage_orders.php'); ?>">
                    <i class="fas fa-shopping-cart"></i> Manage Orders
                </a>
            </div>
            <div class="nav-group">
                <h3>Doctors</h3>
                <a href="<?php echo url('admin/doctors/manage_doctors.php'); ?>">
                    <i class="fas fa-user-md"></i> Manage Doctors
                </a>
                <a href="<?php echo url('admin/doctors/add_doctor.php'); ?>">
                    <i class="fas fa-user-plus"></i> Add Doctor
                </a>
            </div>
        </nav>
    </aside>
   
    

    <main class="admin-main">
        <div class="admin-header">
            <h1>Add New Product</h1>
            <nav class="breadcrumb">
                <a href="../dashboard.php">Dashboard</a> /
                <a href="manage_products.php">Products</a> /
                <span>Add Product</span>
            </nav>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="content-card">
            <form method="POST" action="" class="admin-form" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while($category = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="main_image">Main Product Image</label>
                        <div class="file-upload">
                            <input type="file" id="main_image" name="main_image" accept="image/*" required>
                            <div id="main_image_preview" class="image-preview"></div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="additional_images">Additional Images</label>
                        <div class="file-upload-zone" id="dropZone">
                            <input type="file" id="additional_images" name="additional_images[]" multiple accept="image/*">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Drag & Drop additional images here or click to browse</p>
                                <span class="file-info">Maximum 5 images, each up to 2MB</span>
                            </div>
                        </div>
                        <div id="additional_images_preview" class="image-preview-container"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- <script>
// Image preview functionality
function handleImagePreview(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Main image preview
document.getElementById('main_image').addEventListener('change', function() {
    handleImagePreview(this, 'main_image_preview');
});

// Additional images preview and drag & drop
const dropZone = document.getElementById('dropZone');
const additionalImages = document.getElementById('additional_images');
const additionalPreview = document.getElementById('additional_images_preview');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('highlight');
}

function unhighlight(e) {
    dropZone.classList.remove('highlight');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    additionalImages.files = files;
    handleFiles(files);
}

additionalImages.addEventListener('change', function() {
    handleFiles(this.files);
});

function handleFiles(files) {
    additionalPreview.innerHTML = '';
    [...files].forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-btn">&times;</button>
                `;
                additionalPreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
}
</script> -->



<script>
    // Highlight drag & drop zone
const dropZone = document.getElementById('dropZone');
const additionalImages = document.getElementById('additional_images');
const additionalPreview = document.getElementById('additional_images_preview');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.add('highlight'), false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.remove('highlight'), false);
});

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    additionalImages.files = files;
    handleFiles(files);
}

additionalImages.addEventListener('change', function() {
    handleFiles(this.files);
});

function handleFiles(files) {
    additionalPreview.innerHTML = '';
    [...files].forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-btn">&times;</button>
                `;
                additionalPreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
}

// Remove image preview
additionalPreview.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-btn')) {
        e.target.parentElement.remove();
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>
<style>
    /* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f7fa;
    color: #333;
    margin: 0;
    padding: 0;
}

.admin-container {
    display: flex;
    min-height: 100vh;
}

.admin-main {
    flex: 1;
    padding: 20px;
    background-color: #fff;
}

.admin-header {
    margin-bottom: 30px;
}

.admin-header h1 {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.breadcrumb {
    font-size: 14px;
    color: #7f8c8d;
}

.breadcrumb a {
    color: #1abc9c;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb a:hover {
    color: #16a085;
}

/* Alert Messages */
.alert {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    animation: slideIn 0.5s ease-out;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
}

.alert i {
    margin-right: 10px;
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Form Styles */
.content-card {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.admin-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #1abc9c;
    outline: none;
    box-shadow: 0 0 5px rgba(26, 188, 156, 0.5);
}

.form-group.full-width {
    grid-column: 1 / -1;
}

/* File Upload Styles */
.file-upload {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
}

.file-upload input[type="file"] {
    opacity: 0;
    position: absolute;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-upload .image-preview {
    width: 100px;
    height: 100px;
    border: 2px dashed #ddd;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: border-color 0.3s ease;
}

.file-upload .image-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}

.file-upload:hover .image-preview {
    border-color: #1abc9c;
}

/* Drag & Drop Zone */
.file-upload-zone {
    border: 2px dashed #ddd;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.3s ease, background-color 0.3s ease;
}

.file-upload-zone.highlight {
    border-color: #1abc9c;
    background-color: rgba(26, 188, 156, 0.1);
}

.file-upload-zone .upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    color: #7f8c8d;
}

.file-upload-zone .upload-content i {
    font-size: 24px;
    color: #1abc9c;
}

.file-upload-zone .upload-content p {
    margin: 0;
    font-size: 14px;
}

.file-upload-zone .file-info {
    font-size: 12px;
    color: #95a5a6;
}

/* Image Preview Container */
.image-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.preview-item {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-item .remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: rgba(255, 255, 255, 0.8);
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.preview-item .remove-btn:hover {
    background-color: rgba(255, 0, 0, 0.8);
    color: #fff;
}

/* Buttons */
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-primary {
    background-color: #1abc9c;
    color: #fff;
}

.btn-primary:hover {
    background-color: #16a085;
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: #95a5a6;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #7f8c8d;
    transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

