<?php
function displayProduct($product) {
    ?>
    <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>">
        <div class="product-image-container">
            <?php
            $imagePath = "uploads/" . htmlspecialchars($product['product_image']);
            if (file_exists($imagePath)) {
                ?>
                <img src="<?php echo $imagePath; ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                     class="product-image"
                     onerror="this.src='img/default-product.jpg'">
            <?php } else { ?>
                <img src="img/default-product.jpg" 
                     alt="No image available" 
                     class="product-image">
            <?php } ?>
            
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
?>
