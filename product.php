<?php
require_once 'db_config.php';

try {
    $db = Database::getPDO();

    // Get current page number
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $records_per_page = 12;
    $offset = ($page - 1) * $records_per_page;

    // Get selected category and sort option
    $category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

    // Base query
    $query = "SELECT p.*, c.category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.category_id 
              WHERE 1=1";
    $params = [];

    // Add category filter
    if ($category_id > 0) {
        $query .= " AND p.category_id = :category_id";
        $params[':category_id'] = $category_id;
    }

    // Add sorting
    switch ($sort) {
        case 'price-low':
            $query .= " ORDER BY p.price ASC";
            break;
        case 'price-high':
            $query .= " ORDER BY p.price DESC";
            break;
        case 'name-asc':
            $query .= " ORDER BY p.product_name ASC";
            break;
        default:
            $query .= " ORDER BY p.created_at DESC";
    }

    // Add pagination
    $query .= " LIMIT :offset, :limit";
    $params[':offset'] = $offset;
    $params[':limit'] = $records_per_page;

    // Execute query
    $stmt = Database::queryPDO($query, $params);
    
    // Get total records for pagination
    $count_query = "SELECT COUNT(*) FROM products p WHERE 1=1" . 
                   ($category_id > 0 ? " AND p.category_id = :category_id" : "");
    $count_params = $category_id > 0 ? [':category_id' => $category_id] : [];
    $total_records = Database::queryPDO($count_query, $count_params)->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);

    // Get categories for sidebar
    $categories = Database::queryPDO("SELECT * FROM categories WHERE status = 1 ORDER BY category_name");

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <style>
        
        .category-sidebar {
    width: 250px;
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

.category-sidebar h2 {
    font-size: 1.5em;
    margin-bottom: 15px;
    color: #333;
    text-align: center;
}

.category-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-sidebar li {
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.category-sidebar li a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    display: block;
}

.category-sidebar li:hover {
    background-color: #e9ecef;
}

.category-sidebar li.active {
    background-color:rgba(37, 175, 99, 0.84);
}

.category-sidebar li.active a {
    color: white;
}

    </style>
    <link rel="stylesheet" href="Style\Product.css">
    <link rel="stylesheet" href="Style\Styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <a href="index.php">
                <img src="img/Downloaded adobe logo.png" alt="Logo" class="logo-img">
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="product.php">Product</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="LoginOut.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <!-- Category Sidebar -->
        <aside class="category-sidebar">
            <h2>Categories</h2>
            <ul>
                <li class="<?php echo $category_id === 0 ? 'active' : ''; ?>">
                    <a href="product.php">All Products</a>
                </li>
                <?php while ($category = $categories->fetch()): ?>
                    <li class="<?php echo $category_id === $category['category_id'] ? 'active' : ''; ?>">
                        <a href="product.php?category=<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </aside>

        <!-- Main Content -->
        <main>
            <!-- Sort Options -->
            <div class="sort-options">
                <select onchange="window.location.href=this.value">
                    <option value="?sort=newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="?sort=price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="?sort=price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="?sort=name-asc" <?php echo $sort === 'name-asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                </select>
            </div>

            <!-- Products Grid -->
            <div class="product-grid">
                <?php while ($product = $stmt->fetch()): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="<?php echo htmlspecialchars(Database::getProductImageUrl($product['product_image'])); ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 class="product-image"
                                 onerror="this.src='<?php echo Database::DEFAULT_IMAGE; ?>'">
                        </div>
                        <div class="product-details">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                            <button class="add-to-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>" 
                           class="<?php echo $page === $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
            
        </main>
        <div class="cart-overlay">
<!-- Shopping Cart -->
<aside class="shopping-cart">
                    <div class="cart-header">
                        <h2>Shopping Cart</h2>
                        <span class="cart-count">0 items</span>
                    </div>
                    <div class="cart-items">
                        <!-- Cart items will be populated dynamically -->
                    </div>
                    <div class="cart-total">
                        <h3>Total: ₱0.00</h3>
                        <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
                    </div>
                </aside>
            </div>
        </div>
            

    </div>
                        
    <!-- Chatbot Button -->
    <div class="chat-bot-button" onclick="toggleChat()">
        <i class="fas fa-comments"></i>
    </div>

    <!-- Chatbot Container -->
    <div class="chat-container" id="chatContainer">
        <div class="chat-header">
            <h3>Chat Support</h3>
            <span class="close-chat" onclick="toggleChat()">&times;</span>
        </div>
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be displayed here -->
        </div>
        <div class="chat-input">
            <input type="text" id="userMessage" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>About Us</h4>
                <p>Your company description here.</p>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <p>Email: info@example.com</p>
                <p>Phone: (123) 456-7890</p>
            </div>
            <div class="footer-section">
                <h4>Follow Us</h4>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Your Company. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add to cart functionality
        function addToCart(productId) {
            console.log('Adding product ' + productId + ' to cart');
        }

        // Notify when available functionality
        function notifyWhenAvailable(productId) {
            console.log('Setting notification for product ' + productId);
        }

        // Chat toggle functionality
        function toggleChat() {
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.classList.toggle('active');
        }

        // Send message functionality
        function sendMessage() {
            const messageInput = document.getElementById('userMessage');
            const message = messageInput.value.trim();
            if (message) {
                console.log('Sending message: ' + message);
                messageInput.value = '';
            }
        }

        // Checkout functionality
        function proceedToCheckout() {
            console.log('Proceeding to checkout');
        }
    </script>
</body>
</html>
