<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';

checkAuth();
checkRole(['admin']);

// Handle product operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];

    if (empty($name) || $price <= 0) {
        $error = "Name and valid price are required";
    } else {
        if (isset($_POST['add_product'])) {
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $image = 'product_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], '../../uploads/products/' . $image);
                }
            }

            $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $category_id, $image);
            
            if ($stmt->execute()) {
                $success = "Product added successfully!";
            } else {
                $error = "Error: " . $conn->error;
            }
            $stmt->close();
        } elseif (isset($_POST['edit_product'])) {
            $id = (int)$_POST['product_id'];
            $current_image = $_POST['current_image'];
            $image = $current_image;

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $image = 'product_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], '../../uploads/products/' . $image);
                    
                    // Delete old image
                    if ($current_image && file_exists('../../uploads/products/' . $current_image)) {
                        unlink('../../uploads/products/' . $current_image);
                    }
                }
            }

            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdissi", $name, $description, $price, $stock, $category_id, $image, $id);
            
            if ($stmt->execute()) {
                $success = "Product updated successfully!";
            } else {
                $error = "Error: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get product images before deletion
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    // Get additional images
    $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $additional_images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Delete the product (product_images will be deleted automatically due to ON DELETE CASCADE)
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Delete main product image
        if ($product['image'] && file_exists('../../' . $product['image'])) {
            unlink('../../' . $product['image']);
        }
        
        // Delete additional images
        foreach ($additional_images as $img) {
            if (file_exists('../../' . $img['image_path'])) {
                unlink('../../' . $img['image_path']);
            }
        }
        
        $success = "Product deleted successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all products with category names
$sql = "SELECT p.*, c.name as category_name, 
        (SELECT COUNT(*) FROM product_images WHERE product_id = p.id) as additional_images_count 
        FROM products p 
        LEFT JOIN product_categories c ON p.category_id = c.id 
        WHERE p.status = 1
        ORDER BY p.name";
$products = $conn->query($sql);

// Fetch categories for dropdown
$categories = $conn->query("SELECT id, name FROM product_categories WHERE status = 1 ORDER BY name");

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="<?php echo url('assets/css/admin_sidebar.css'); ?>">
<link rel="stylesheet" href="<?php echo url('assets/css/products.css'); ?>">



<div class="admin-container">
    <?php include '../../includes/admin_sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Manage Products</h1>
            <a href="add_product.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Product
            </a>
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
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Main Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Additional Images</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($product = $products->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../../<?php echo $product['image'] ?: 'uploads/products/default.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-thumbnail">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <?php if ($product['additional_images_count'] > 0): ?>
                                        <button class="btn btn-info btn-sm" onclick="viewImages(<?php echo $product['id']; ?>)">
                                            View (<?php echo $product['additional_images_count']; ?>)
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">No additional images</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="action-btn edit" onclick="editProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Images Modal -->
<div id="imagesModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Additional Product Images</h2>
            <button class="close-btn" onclick="closeImagesModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="additionalImages" class="additional-images-grid"></div>
        </div>
    </div>
</div>

<!-- Add Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Product</h2>
            <button class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" id="edit_product_id" name="product_id">
                <input type="hidden" id="current_image" name="current_image">
                
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_category">Category</label>
                    <select id="edit_category" name="category_id" required>
                        <?php 
                        $categories->data_seek(0);
                        while($category = $categories->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_price">Price</label>
                    <input type="number" id="edit_price" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_stock">Stock</label>
                    <input type="number" id="edit_stock" name="stock" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Current Main Image</label>
                    <div id="current_image_preview" class="image-preview"></div>
                    <input type="file" name="main_image" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>Additional Images</label>
                    <div id="additional_images_container" class="additional-images-grid"></div>
                    <input type="file" name="additional_images[]" multiple accept="image/*">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" name="edit_product" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        window.location.href = `?delete=${id}`;
    }
}

function viewImages(productId) {
    fetch(`get_product_images.php?id=${productId}`)
        .then(response => response.json())
        .then(images => {
            const container = document.getElementById('additionalImages');
            container.innerHTML = '';
            
            images.forEach(image => {
                const div = document.createElement('div');
                div.className = 'additional-image';
                div.innerHTML = `<img src="../../${image.image_path}" alt="Product image">`;
                container.appendChild(div);
            });
            
            document.getElementById('imagesModal').style.display = 'block';
        });
}

function closeImagesModal() {
    document.getElementById('imagesModal').style.display = 'none';
}

function editProduct(id) {
    // Add a loading class to the modal
    const modal = document.getElementById('editModal');
    modal.classList.add('loading');
    
    // Update the fetch URL to include the full path
    fetch(`get_product.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Remove loading class
            modal.classList.remove('loading');
            
            // Populate form fields
            document.getElementById('edit_product_id').value = data.product.id;
            document.getElementById('edit_name').value = data.product.name;
            document.getElementById('edit_category').value = data.product.category_id;
            document.getElementById('edit_price').value = data.product.price;
            document.getElementById('edit_stock').value = data.product.stock;
            document.getElementById('edit_description').value = data.product.description;
            document.getElementById('current_image').value = data.product.image;
            
            // Show current main image
            const imagePreview = document.getElementById('current_image_preview');
            if (data.product.image) {
                imagePreview.innerHTML = `
                    <img src="../../${data.product.image}" alt="Current product image" style="max-width: 200px;">
                `;
            } else {
                imagePreview.innerHTML = '<p>No image available</p>';
            }
            
            // Show additional images
            const additionalImagesContainer = document.getElementById('additional_images_container');
            additionalImagesContainer.innerHTML = '';
            if (data.additional_images && data.additional_images.length > 0) {
                data.additional_images.forEach(image => {
                    const div = document.createElement('div');
                    div.className = 'additional-image';
                    div.innerHTML = `
                        <img src="../../${image.image_path}" alt="Additional image">
                        <label class="delete-checkbox">
                            <input type="checkbox" name="delete_images[]" value="${image.id}">
                            Delete
                        </label>
                    `;
                    additionalImagesContainer.appendChild(div);
                });
            }
            
            // Show the modal
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching product details. Please try again.');
            modal.classList.remove('loading');
        });
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('imagesModal');
    if (event.target == modal) {
        closeImagesModal();
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?> 