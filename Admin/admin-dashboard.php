<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="C:\xampp\htdocs\TrialMart\Admin\FrontEnd\admin_style\admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>

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

body {
    background-color: var(--background-light);
    color: var(--text-light);
    transition: background-color var(--transition-speed);
}

body.dark-mode {
    background-color: var(--background-dark);
    color: var(--text-dark);
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

            <div class="dashboard-content">
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-content">
                            <h3>Total Sales</h3>
                            <p class="number">₱24,500</p>
                            <p class="growth">+15% from last month</p>
                        </div>
                        <i class="fas fa-peso-sign"></i>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3>Customers</h3>
                            <p class="number">1,250</p>
                            <p class="growth">+8% new customers</p>
                        </div>
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3>Products</h3>
                            <p class="number">384</p>
                            <p class="growth">12 added today</p>
                        </div>
                        <i class="fas fa-box"></i>
                    </div>
                </div>

                <!-- Recent Activity Table -->
                <div class="recent-activity">
                    <h2>Recent Activity</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Activity</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#1234</td>
                                    <td>New Order</td>
                                    <td>John Doe</td>
                                    <td>2024-03-20</td>
                                    <td><span class="status completed">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>#1235</td>
                                    <td>Product Update</td>
                                    <td>Jane Smith</td>
                                    <td>2024-03-20</td>
                                    <td><span class="status pending">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="/TrialMart/Admin/FrontEnd/admin_js/admin-dashboard.js"></script>

</body>
</html>
