<?php
session_start();
require_once '../db_config.php';

// Initialize messages
$success_message = '';
$error_message = '';

// Fetch categories for dropdown
$category_query = "SELECT category_id, category_name FROM categories";
$category_result = mysqli_query($conn, $category_query) or die("Error fetching categories: " . mysqli_error($conn));

// Handle regular form submission for adding new products
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['ajax_action'])) {
    try {
        // Validate inputs
        if (empty($_POST['product_name']) || empty($_POST['category_id']) || empty($_POST['price'])) {
            throw new Exception("Please fill in all required fields");
        }

        $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
        $category_id = (int)$_POST['category_id'];
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Handle image upload
        if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== 0) {
            throw new Exception("Please select an image");
        }

        // Validate image
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['product_image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
            throw new Exception("File size too large. Maximum size is 5MB");
        }
        
        if (!in_array($filetype, $allowed)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and WEBP files are allowed");
        }

        $new_filename = uniqid() . '.' . $filetype;
        $upload_path = 'uploads/products/' . $new_filename;

        // Create upload directory if it doesn't exist
        if (!file_exists('uploads/products/')) {
            if (!mkdir('uploads/products/', 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }

        if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
            throw new Exception("Error uploading file");
        }

        // Insert into database
        mysqli_begin_transaction($conn);

        try {
            $query = "INSERT INTO products (product_name, category_id, price, stock_quantity, description, product_image) 
                     VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sidiss", $product_name, $category_id, $price, $stock, $description, $new_filename);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error adding product: " . mysqli_error($conn));
            }

            mysqli_commit($conn);
            $success_message = "Product added successfully!";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            // Delete uploaded file if database operation fails
            if (file_exists($upload_path)) {
                unlink($upload_path);
            }
            throw $e;
        }

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/TrialMart/Admin/FrontEnd/admin_style/admin-dashboard.css">
    <link rel="stylesheet" href="/TrialMart/Admin/FrontEnd/admin_style/add-product.css">

    
    <style>
        
          /* Table Styling */
    .products-table-container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h3 {
        color: #27ae60;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead {
        background-color: #27ae60;
        color: white;
    }

    .table tbody tr:hover {
        background-color: #f5f5f5;
    }

    .table td, .table th {
        vertical-align: middle;
        text-align: center;
    }

    /* Button Styling */
    .btn-success {
        background-color: #2ecc71;
        border: none;
    }

    .btn-success:hover {
        background-color: #27ae60;
    }

    .btn-warning, .btn-danger {
        color: white;
    }

    .btn-warning {
        background-color: #f39c12;
        border: none;
    }

    .btn-warning:hover {
        background-color: #e67e22;
    }

    .btn-danger {
        background-color: #e74c3c;
        border: none;
    }

    .btn-danger:hover {
        background-color: #c0392b;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 10px;
    }

    .modal-header {
    background: #27ae60;
    color: white;
    border-radius: 50px 50px 0 0;
    font-size: 27px;
    padding-left: 501px;
    padding-top: 14px;
    margin-top: 40px;
    }

    .modal-title {
        font-weight: bold;
    }

    .close {
        color: white;
        opacity: 1;
    }

    .form-control {
        border-radius: 5px;
    }

    /* Image Preview */
    #current_image_preview {
        display: block;
        max-width: 100px;
        margin-top: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    </style>
</head>
<body class="light-mode">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <button id="toggle-sidebar" class="toggle-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="/TrialMart/Admin/admin-dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                </li>
                <li>
                    <a href="#sales"><i class="fas fa-chart-bar"></i> Sales</a>
                </li>
                <li>
                    <a href="/TrialMart/Admin/customer-management.php"><i class="fas fa-users"></i> Customers</a>
                </li>
                <li class="active">
                    <a href="/TrialMart/Admin/add-product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
                </li>
                <li>
                    <a href="/TrialMart/Admin/add-category.php"><i class="fas fa-tags"></i> Add Category</a>
                </li>
                <li>
                    <a href="/TrialMart/Admin/admin-users.php"><i class="fas fa-user-shield"></i> Admin Users</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <div class="search-container">
                    <input type="text" placeholder="Search...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="top-bar-right">
                    <button id="theme-toggle" class="theme-toggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="user-profile">
                        <img src="https://via.placeholder.com/40" alt="Profile">
                        <span>Admin User</span>
                    </div>
                </div>
            </header>

            <div class="container">
                <h2>Add New Product</h2>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="product_name">Product Name *</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while($category = mysqli_fetch_assoc($category_result)): ?>
                                <option value="<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" value="0">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="product_image">Product Image *</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" accept=".jpg,.jpeg,.png,.webp" required>
                        <small class="form-text">Allowed formats: JPG, JPEG, PNG, WEBP</small>
                        <div id="image-preview"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
            
            <!-- Products Table Section -->
<div class="products-table-container mt-5">
    <h3>Product List</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="productsTable">
            <thead class="thead-dark">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Products will be loaded here dynamically -->
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit_product_id" name="product_id">
                    
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" id="edit_category_id" name="category_id" required>
                            <?php 
                            mysqli_data_seek($category_result, 0);
                            while($category = mysqli_fetch_assoc($category_result)): 
                            ?>
                                <option value="<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" class="form-control" id="edit_stock" name="stock" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="edit_description" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" class="form-control" id="edit_product_image" name="product_image">
                        <img id="current_image_preview" src="" alt="" class="mt-2" style="max-width: 100px;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveProductChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>
                    

            
        </main>
  
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        $(document).ready(function() {
            // Load products on page load
            loadProducts();
            
            // Function to load products
            function loadProducts() {
                $.post('products_crud.php', { ajax_action: 'read' }, function(response) {
                    if (response.success) {
                        let rows = '';
                        response.products.forEach(product => {
                            rows += `
                                <tr>
                                    <td><img src="${product.product_image}" width="50" height="50" alt="${product.product_name}"></td>
                                    <td>${product.product_name}</td>
                                    <td>${product.category_name}</td>
                                    <td>$${parseFloat(product.price).toFixed(2)}</td>
                                    <td>${product.stock_quantity}</td>
                                    <td>${product.description || ''}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm edit-product" data-id="${product.product_id}">Edit</button>
                                        <button class="btn btn-danger btn-sm delete-product" data-id="${product.product_id}">Delete</button>
                                    </td>
                                </tr>
                            `;
                        });
                        $("#productsTable tbody").html(rows);
                    } else {
                        alert('Error loading products: ' + response.error);
                    }
                });
            }
            
            // Handle edit product
            $(document).on('click', '.edit-product', function() {
                const productId = $(this).data('id');
                const row = $(this).closest('tr');
                
                $('#edit_product_id').val(productId);
                $('#edit_product_name').val(row.find('td:eq(1)').text());
                $('#edit_category_id').val(row.find('td:eq(2)').data('category-id'));
                $('#edit_price').val(parseFloat(row.find('td:eq(3)').text().replace('$', '')));
                $('#edit_stock').val(row.find('td:eq(4)').text());
                $('#edit_description').val(row.find('td:eq(5)').text());
                $('#current_image_preview').attr('src', row.find('td:eq(0) img').attr('src'));
                
                $('#editProductModal').modal('show');
            });
            
            // Handle save changes
            $('#saveProductChanges').click(function() {
                const formData = new FormData($('#editProductForm')[0]);
                formData.append('ajax_action', 'update');
                
                $.ajax({
                    url: 'products_crud.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editProductModal').modal('hide');
                            loadProducts();
                            alert('Product updated successfully!');
                        } else {
                            alert('Error updating product: ' + response.error);
                        }
                    }
                });
            });
            
            // Handle delete product
            $(document).on('click', '.delete-product', function() {
                if (confirm('Are you sure you want to delete this product?')) {
                    const productId = $(this).data('id');
                    
                    $.post('products_crud.php', {
                        ajax_action: 'delete',
                        product_id: productId
                    }, function(response) {
                        if (response.success) {
                            loadProducts();
                            alert('Product deleted successfully!');
                        } else {
                            alert('Error deleting product: ' + response.error);
                        }
                    });
                }
            });
        });
        </script>
        <script src="/TrialMart/Admin/FrontEnd/admin_js/add-product.js"></script>
 

</body>
</html>
