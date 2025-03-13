<?php
// product_functions.php

function getProducts($conn, $offset, $items_per_page, $category_id = null, $sort = 'newest') {
    // Base query with all product fields
    $product_query = "SELECT p.product_id, p.product_name, p.category_id, 
                            p.price, p.stock_quantity, p.description, 
                            p.product_image, p.created_at, p.updated_at,
                            c.category_name 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.category_id";
    
    // Add category filter if specified
    if($category_id) {
        $product_query .= " WHERE p.category_id = ?";
    }
    
    // Add sorting
    switch($sort) {
        case 'price-low':
            $product_query .= " ORDER BY p.price ASC";
            break;
        case 'price-high':
            $product_query .= " ORDER BY p.price DESC";
            break;
        case 'name-asc':
            $product_query .= " ORDER BY p.product_name ASC";
            break;
        case 'newest':
            $product_query .= " ORDER BY p.created_at DESC";
            break;
        case 'updated':
            $product_query .= " ORDER BY p.updated_at DESC";
            break;
        default:
            $product_query .= " ORDER BY p.created_at DESC";
    }
    
    // Add pagination
    $product_query .= " LIMIT ?, ?";
    
    // Prepare statement
    $stmt = mysqli_prepare($conn, $product_query);
    
    // Bind parameters
    if($category_id) {
        mysqli_stmt_bind_param($stmt, "iii", $category_id, $offset, $items_per_page);
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $offset, $items_per_page);
    }
    
    // Execute and return results
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function getCategories($conn) {
    $category_query = "SELECT c.*, 
                      (SELECT COUNT(*) FROM products p WHERE p.category_id = c.category_id) as product_count 
                      FROM categories c 
                      ORDER BY c.category_name";
    return mysqli_query($conn, $category_query);
}

function getTotalProducts($conn, $category_id = null) {
    $count_query = "SELECT COUNT(*) as total FROM products";
    
    if($category_id) {
        $count_query .= " WHERE category_id = ?";
        $stmt = mysqli_prepare($conn, $count_query);
        mysqli_stmt_bind_param($stmt, "i", $category_id);
    } else {
        $stmt = mysqli_prepare($conn, $count_query);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result)['total'];
}

function getCategoryCount($conn, $category_id) {
    $count_query = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
    $stmt = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result)['count'];
}

function getProductImage($product) {
    if (!empty($product['product_image'])) {
        $image_path = "uploads/" . $product['product_image'];
        if (file_exists($image_path)) {
            return $image_path;
        }
    }
    return "img/default-product.jpg";
}

function displayProductCard($product) {
    $image_path = getProductImage($product);
    ?>
    <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>">
        <div class="product-image-container">
            <img src="<?php echo htmlspecialchars($image_path); ?>" 
                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                 class="product-image"
                 onerror="this.src='img/default-product.jpg'">
            <?php if($product['stock_quantity'] <= 0) { ?>
                <div class="out-of-stock-overlay">Out of Stock</div>
            <?php } ?>
        </div>
        <div class="product-details">
            <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
            <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
            <p class="product-description">
                <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
            </p>
            <div class="product-meta">
                <span class="stock-info">Stock: <?php echo $product['stock_quantity']; ?></span>
                <span class="date-info">Added: <?php echo date('M d, Y', strtotime($product['created_at'])); ?></span>
            </div>
            <?php if($product['stock_quantity'] > 0) { ?>
                <button class="add-to-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            <?php } else { ?>
                <button class="notify-stock" onclick="notifyWhenAvailable(<?php echo $product['product_id']; ?>)">
                    Notify When Available
                </button>
            <?php } ?>
        </div>
    </div>
    <?php
}

function formatPrice($price) {
    return number_format($price, 2);
}

function sanitizeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function truncateText($text, $length = 100) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

function displayError($message) {
    return "<div class='error-message'>Error: " . sanitizeOutput($message) . "</div>";
}

function displaySuccess($message) {
    return "<div class='success-message'>" . sanitizeOutput($message) . "</div>";
}

function validateImage($file) {
    $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check file size (5MB maximum)
    if ($file['size'] > 5242880) {
        return false;
    }
    
    // Check file extension
    if (!in_array($file_extension, $valid_extensions)) {
        return false;
    }
    
    return true;
}

function getPaginationLink($page, $category_id = null, $sort = null) {
    $link = "?page=" . $page;
    if ($category_id) {
        $link .= "&category=" . (int)$category_id;
    }
    if ($sort) {
        $link .= "&sort=" . urlencode($sort);
    }
    return $link;
}

function isInStock($product) {
    return ($product['stock_quantity'] > 0);
}

function formatDate($date) {
    return date("F j, Y", strtotime($date));
}

// New function to handle image upload
function handleImageUpload($file) {
    $upload_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $unique_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $unique_filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $unique_filename;
    }
    
    return false;
}

// Function to get single product details
function getProductById($conn, $product_id) {
    $query = "SELECT p.*, c.category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id 
              WHERE p.product_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?>
