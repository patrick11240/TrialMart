<?php
session_start();
require_once '../db_config.php';



// Initialize messages
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $category_icon = mysqli_real_escape_string($conn, $_POST['category_icon']);
    
    // Check if category already exists
    $check_query = "SELECT category_id FROM categories WHERE category_name = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $category_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $error_message = "Category already exists!";
    } else {
        // Insert new category  
        $insert_query = "INSERT INTO categories (category_name, category_icon) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "ss", $category_name, $category_icon);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Category added successfully!";
        } else {
            $error_message = "Error adding category: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="C:\xampp\htdocs\TrialMart\Admin\FrontEnd\admin_style">
    <link rel="stylesheet" href="C:\xampp\htdocs\TrialMart\Admin\FrontEnd\admin_style\admin-dashboard.css">
    <style>
        :root {
    --primary-color: #2ecc71;
    --secondary-color: #27ae60;
    --error-color: #e74c3c;
    --text-color: #333;
    --border-color: #ddd;
    --background-color: #f8f9fa;
    --white: #ffffff;
    --shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: var(--background-color);
    color: var(--text-color);
    line-height: 1.6;
}

.container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.form-container {
    background-color: var(--white);
    padding: 2rem;
    border-radius: 10px;
    box-shadow: var(--shadow);
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

h1 {
    color: var(--text-color);
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #666;
    font-size: 0.9rem;
}

.category-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    position: relative;
}

label {
    font-weight: 600;
    font-size: 0.9rem;
}

input,
textarea {
    padding: 0.8rem;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

.error-message {
    color: var(--error-color);
    font-size: 0.8rem;
    min-height: 1rem;
}

.character-count {
    position: absolute;
    right: 0;
    top: 0;
    font-size: 0.8rem;
    color: #666;
}

/* Icon Selector Styles */
.icon-selector {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.icon-preview {
    width: 40px;
    height: 40px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: var(--primary-color);
}

.icon-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.icon-item {
    width: 40px;
    height: 40px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.icon-item:hover {
    border-color: var(--primary-color);
    background-color: #f0f0f0;
}

.icon-item.selected {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* Toggle Switch Styles */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-label {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.toggle-label:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-label {
    background-color: var(--primary-color);
}

input:checked + .toggle-label:before {
    transform: translateX(26px);
}

/* Button Styles */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1rem;
}

.btn {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background-color: var(--secondary-color);
}

.btn-secondary {
    background-color: #f1f1f1;
    color: var(--text-color);
}

.btn-secondary:hover {
    background-color: #e1e1e1;
}

/* Success Message Styles */
.success-message {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: var(--primary-color);
    color: white;
    padding: 1rem;
    border-radius: 5px;
    box-shadow: var(--shadow);
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        margin: 1rem auto;
    }

    .form-container {
        padding: 1.5rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }

    .icon-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
:root {
    --primary-color: #2ecc71;
    --secondary-color: #27ae60;
    --background-light: #f4f6f8;
    --background-dark: #1a1a1a;
    --text-light: #333;
    --text-dark: #fff;
    --sidebar-width: 250px;
    --sidebar-collapsed-width: 70px;
    --transition-speed: 0.3s;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}





.dashboard-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar Styles */
.sidebar {
    width: var(--sidebar-width);
    background: var(--primary-color);
    padding: 1rem;
    transition: width var(--transition-speed);
    position: fixed;
    height: 100vh;
    z-index: 1000;
}

.sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
}

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    color: white;
}

.toggle-btn {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    font-size: 1.2rem;
}

.nav-links {
    list-style: none;
}

.nav-links li {
    margin-bottom: 0.5rem;
}

.nav-links a {
    display: flex;
    align-items: center;
    padding: 0.8rem 1rem;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: background-color var(--transition-speed);
}

.nav-links a:hover {
    background-color: var(--secondary-color);
}

.nav-links a i {
    margin-right: 1rem;
    font-size: 1.2rem;
}

.nav-links li.active a {
    background-color: var(--secondary-color);
}

/* Main Content Styles */
.main-content {
    flex: 1;
    margin-left: var(--sidebar-width);
    transition: margin-left var(--transition-speed);
    padding: 1rem;
}

.main-content.expanded {
    margin-left: var(--sidebar-collapsed-width);
}

/* Top Bar Styles */
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dark-mode .top-bar {
    background: #2d2d2d;
}

.search-container {
    display: flex;
    align-items: center;
    background: #f5f5f5;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.dark-mode .search-container {
    background: #3d3d3d;
}

.search-container input {
    border: none;
    background: none;
    margin-right: 0.5rem;
    outline: none;
    color: inherit;
}

.top-bar-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.theme-toggle {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    color: inherit;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-profile img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

/* Dashboard Cards */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dark-mode .card {
    background: #2d2d2d;
}

.card i {
    font-size: 2rem;
    color: var(--primary-color);
}

.card-content h3 {
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.growth {
    color: var(--primary-color);
    font-size: 0.9rem;
}

/* Table Styles */
.recent-activity {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dark-mode .recent-activity {
    background: #2d2d2d;
}

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.dark-mode th,
.dark-mode td {
    border-bottom: 1px solid #3d3d3d;
}

.status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.status.completed {
    background: #e8f5e9;
    color: var(--secondary-color);
}

.status.pending {
    background: #fff3e0;
    color: #f57c00;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: var(--sidebar-collapsed-width);
        padding: 1rem 0.5rem;
    }

    .sidebar.expanded {
        width: var(--sidebar-width);
    }

    .main-content {
        margin-left: var(--sidebar-collapsed-width);
    }

    .sidebar-header h2,
    .nav-links a span {
        display: none;
    }

    .sidebar.expanded .sidebar-header h2,
    .sidebar.expanded .nav-links a span {
        display: inline;
    }

    .dashboard-cards {
        grid-template-columns: 1fr;
    }
}

        /* Add any additional styles specific to add-category page */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .character-count {
            float: right;
            font-size: 12px;
            color: #666;
        }

        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            margin-top: 10px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }

        .icon-item {
            text-align: center;
            padding: 10px;
            cursor: pointer;
            border: 1px solid #eee;
            border-radius: 4px;
        }

        .icon-item:hover {
            background-color: #f0f0f0;
        }

        .icon-item.selected {
            background-color: #e3f2fd;
            border-color: #2196f3;
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
                <li class="active"> 
                    <a href="/TrialMart/Admin/admin-dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                </li>
                <li>
                    <a href="#sales"><i class="fas fa-chart-bar"></i> Sales</a>
                </li>
                <li>
                    <a href="/TrialMart/Admin/customer-management.php"><i class="fas fa-users"></i> Customers</a>
                </li>
                <li>
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
                <div class="form-container">
                    <div class="form-header">
                        <h1>Add New Category</h1>
                        <p class="subtitle">Create a new product category for your store</p>
                    </div>

                    <?php if($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>

                    <?php if($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form id="addCategoryForm" class="category-form" method="POST" action="">
                        <div class="form-group">
                            <label for="categoryName">Category Name*</label>
                            <input 
                                type="text" 
                                id="categoryName" 
                                name="categoryName" 
                                required
                                placeholder="Enter category name"
                                maxlength="50"
                            >
                            <span class="error-message"></span>
                            <span class="character-count">0/50</span>
                        </div>

                        <div class="form-group">
                            <label for="categoryDescription">Description</label>
                            <textarea 
                                id="categoryDescription" 
                                name="categoryDescription" 
                                rows="4"
                                placeholder="Enter category description"
                                maxlength="200"
                            ></textarea>
                            <span class="error-message"></span>
                            <span class="character-count">0/200</span>
                        </div>

                        <div class="form-group">
                            <label for="categoryIcon">Category Icon</label>
                            <div class="icon-selector">
                                <input 
                                    type="text" 
                                    id="categoryIcon" 
                                    name="categoryIcon" 
                                    placeholder="Select an icon"
                                    readonly
                                >
                                <div class="icon-preview">
                                    <i class="fas fa-tag"></i>
                                </div>
                            </div>
                            <div class="icon-grid" id="iconGrid">
                                <div class="icon-item" data-icon="fa-drumstick-bite"><i class="fas fa-drumstick-bite"></i></div>
                                <div class="icon-item" data-icon="fa-box"><i class="fas fa-box"></i></div>
                                <div class="icon-item" data-icon="fa-pump-soap"><i class="fas fa-pump-soap"></i></div>
                                <div class="icon-item" data-icon="fa-cookie-bite"><i class="fas fa-cookie-bite"></i></div>
                                <div class="icon-item" data-icon="fa-bath"><i class="fas fa-bath"></i></div>
                                <div class="icon-item" data-icon="fa-utensils"><i class="fas fa-utensils"></i></div>
                                <div class="icon-item" data-icon="fa-wine-bottle"><i class="fas fa-wine-bottle"></i></div>
                                <div class="icon-item" data-icon="fa-snowflake"><i class="fas fa-snowflake"></i></div>
                                <div class="icon-item" data-icon="fa-apple-alt"><i class="fas fa-apple-alt"></i></div>
                                <div class="icon-item" data-icon="fa-carrot"><i class="fas fa-carrot"></i></div>
                                <div class="icon-item" data-icon="fa-soap"><i class="fas fa-soap"></i></div>
                                <div class="icon-item" data-icon="fa-cheese"><i class="fas fa-cheese"></i></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="categoryStatus">Status</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="categoryStatus" name="categoryStatus" checked>
                                <label for="categoryStatus" class="toggle-label">
                                    <span class="toggle-inner"></span>
                                    <span class="toggle-switch"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='product.php'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character count for category name
        const categoryName = document.getElementById('categoryName');
        const categoryNameCount = categoryName.nextElementSibling.nextElementSibling;
        
        categoryName.addEventListener('input', function() {
            categoryNameCount.textContent = `${this.value.length}/50`;
        });

        // Character count for description
        const categoryDescription = document.getElementById('categoryDescription');
        const categoryDescriptionCount = categoryDescription.nextElementSibling.nextElementSibling;
        
        categoryDescription.addEventListener('input', function() {
            categoryDescriptionCount.textContent = `${this.value.length}/200`;
        });

        // Icon selection
        const iconGrid = document.getElementById('iconGrid');
        const categoryIcon = document.getElementById('categoryIcon');
        const iconPreview = document.querySelector('.icon-preview i');

        iconGrid.addEventListener('click', function(e) {
            const iconItem = e.target.closest('.icon-item');
            if (iconItem) {
                // Remove previous selection
                document.querySelectorAll('.icon-item').forEach(item => {
                    item.classList.remove('selected');
                });

                // Add selection to clicked icon
                iconItem.classList.add('selected');

                // Update input and preview
                const iconClass = iconItem.dataset.icon;
                categoryIcon.value = iconClass;
                iconPreview.className = 'fas ' + iconClass;
            }
        });

        // Form validation
        const form = document.getElementById('addCategoryForm');
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const nameError = categoryName.nextElementSibling;

            // Validate category name
            if (categoryName.value.trim() === '') {
                nameError.textContent = 'Category name is required';
                isValid = false;
            } else {
                nameError.textContent = '';
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Auto-hide alerts after 3 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        });
    });
    </script>
</body>
</html>
