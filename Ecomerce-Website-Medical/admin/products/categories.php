<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';

checkAuth();
checkRole(['admin']);

// Add/Edit category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (empty($name)) {
        $error = "Category name is required";
    } else {
        if (isset($_POST['add_category'])) {
            $stmt = $conn->prepare("INSERT INTO product_categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            
            if ($stmt->execute()) {
                $success = "Category added successfully!";
            } else {
                $error = "Error: " . $conn->error;
            }
            $stmt->close();
        } elseif (isset($_POST['edit_category'])) {
            $id = (int)$_POST['category_id'];
            $stmt = $conn->prepare("UPDATE product_categories SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $description, $id);
            
            if ($stmt->execute()) {
                $success = "Category updated successfully!";
            } else {
                $error = "Error: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        $error = "Cannot delete category: It contains products";
    } else {
        $stmt = $conn->prepare("DELETE FROM product_categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = "Category deleted successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
    $stmt->close();
}

// Fetch all categories with product count
$sql = "SELECT c.*, COUNT(p.id) as product_count 
        FROM product_categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY c.name";
$categories = $conn->query($sql);

require_once '../../includes/header.php';
?>
<link rel="stylesheet" href="<?php echo url('assets/css/admin_sidebar.css'); ?>">


<style>
/* Cool CSS and Animations */
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
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    animation: slideIn 0.5s ease-in-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

.admin-header h1 {
    font-size: 24px;
    color: #2c3e50;
    margin: 0;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
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

.alert {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    animation: fadeIn 0.5s ease-in-out;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
}

.content-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    animation: fadeIn 0.5s ease-in-out;
}

.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.admin-table th {
    background-color: #2c3e50;
    color: #fff;
    font-weight: 500;
}

.admin-table tr:hover {
    background-color: #f9f9f9;
    transition: background-color 0.3s ease;
}

.action-btn {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.action-btn.edit {
    background-color: #3498db;
    color: #fff;
}

.action-btn.edit:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

.action-btn.delete {
    background-color: #e74c3c;
    color: #fff;
}

.action-btn.delete:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content {
    background: white;
    padding: 20px;
    width: 90%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s ease-in-out;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.modal-header h2 {
    font-size: 20px;
    color: #2c3e50;
    margin: 0;
}

.close-btn {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #666;
    transition: color 0.3s ease;
}

.close-btn:hover {
    color: #333;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #2c3e50;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #1abc9c;
    outline: none;
    box-shadow: 0 0 5px rgba(26, 188, 156, 0.5);
}

.form-group textarea {
    height: 100px;
    resize: vertical;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}
</style>

<div class="admin-container">
    <?php include '../../includes/admin_sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Manage Categories</h1>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Category
            </button>
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
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Products</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($category = $categories->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description']); ?></td>
                                <td><?php echo $category['product_count']; ?></td>
                                <td>
                                    <button class="action-btn edit" onclick="editCategory(<?php echo $category['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($category['product_count'] == 0): ?>
                                        <button class="action-btn delete" onclick="deleteCategory(<?php echo $category['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Category</h2>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="" class="admin-form">
            <input type="hidden" id="category_id" name="category_id">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" id="submitBtn" name="add_category" class="btn btn-primary">Add Category</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Category';
    document.getElementById('submitBtn').textContent = 'Add Category';
    document.getElementById('submitBtn').name = 'add_category';
    document.getElementById('category_id').value = '';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
    document.getElementById('categoryModal').style.display = 'block';
}

function editCategory(id) {
    const modal = document.getElementById('categoryModal');
    modal.classList.add('loading');
    
    fetch(`get_category.php?id=${id}`)
        .then(response => response.json())
        .then(category => {
            modal.classList.remove('loading');
            document.getElementById('modalTitle').textContent = 'Edit Category';
            document.getElementById('submitBtn').textContent = 'Update Category';
            document.getElementById('submitBtn').name = 'edit_category';
            document.getElementById('category_id').value = category.id;
            document.getElementById('name').value = category.name;
            document.getElementById('description').value = category.description;
            modal.style.display = 'block';
        })
        .catch(error => {
            modal.classList.remove('loading');
            alert('Error fetching category details: ' + error.message);
        });
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        window.location.href = `?delete=${id}`;
    }
}

function closeModal() {
    document.getElementById('categoryModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('categoryModal')) {
        closeModal();
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>