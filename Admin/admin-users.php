<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users Management</title>
    <link rel="stylesheet" href="/TrialMart/Admin/FrontEnd/admin_style/admin-users.css">
    <link rel="stylesheet" href="/TrialMart/Admin/FrontEnd/admin_style/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <!-- Admin Users List Section -->
                <div class="admin-panel">
                    <div class="panel-header">
                        <h1>Admin Users Management</h1>
                        <button class="btn btn-primary" id="addUserBtn">
                            <i class="fas fa-plus"></i> Add New Admin
                        </button>
                    </div>
            
                    <!-- Search and Filter Bar -->
                    <div class="search-bar">
                        <div class="search-input">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchUsers" placeholder="Search users...">
                        </div>
                        <div class="filter-options">
                            <select id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="super-admin">Super Admin</option>
                                <option value="admin">Admin</option>
                                <option value="editor">Editor</option>
                            </select>
                            <select id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
            
                    <!-- Users Table -->
                    <div class="table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <!-- Table rows will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            
                <!-- Add/Edit User Modal -->
                <div class="modal" id="userModal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 id="modalTitle">Add New Admin User</h2>
                            <button class="close-btn" id="closeModal">&times;</button>
                        </div>
                        <form id="userForm" class="user-form">
                            <div class="form-group">
                                <label for="fullName">Full Name*</label>
                                <input type="text" id="fullName" name="fullName" required>
                                <span class="error-message"></span>
                            </div>
            
                            <div class="form-group">
                                <label for="email">Email Address*</label>
                                <input type="email" id="email" name="email" required>
                                <span class="error-message"></span>
                            </div>
            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password">Password*</label>
                                    <div class="password-input">
                                        <input type="password" id="password" name="password" required>
                                        <button type="button" class="toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <span class="error-message"></span>
                                </div>
            
                                <div class="form-group">
                                    <label for="role">Role*</label>
                                    <select id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="super-admin">Super Admin</option>
                                        <option value="admin">Admin</option>
                                        <option value="editor">Editor</option>
                                    </select>
                                    <span class="error-message"></span>
                                </div>
                            </div>
            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="status" name="status" checked>
                                    <label for="status" class="toggle-label">
                                        <span class="toggle-inner"></span>
                                        <span class="toggle-switch"></span>
                                    </label>
                                </div>
                            </div>
            
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save User</button>
                            </div>
                        </form>
                    </div>
                </div>
            
                <!-- Delete Confirmation Modal -->
                <div class="modal" id="deleteModal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Confirm Deletion</h2>
                            <button class="close-btn" id="closeDeleteModal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" id="cancelDelete">Cancel</button>
                            <button class="btn btn-danger" id="confirmDelete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
           
        </main>
    </div>
    <script src="C:\xampp\htdocs\TrialMart\Admin\Admin\admin js\admin-dashboard.js"></script>
    <script src="C:\xampp\htdocs\TrialMart\Admin\Admin\admin js\admin-users.js"></script>
</body>
</html>
