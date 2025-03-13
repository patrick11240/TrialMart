<?php
include 'db_config.php';

if(isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    $sql = "SELECT p.*, c.category_name, 
            COALESCE(AVG(r.rating), 0) as average_rating,
            COUNT(r.review_id) as review_count
            FROM products p
            JOIN catergories c ON p.category_id = c.category_id
            LEFT JOIN reviews r ON p.product_id = r.product_id
            WHERE p.product_id = ?
            GROUP BY p.product_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($product = $result->fetch_assoc()) {
        // Format the data
        $product['average_rating'] = round($product['average_rating'], 1);
        $product['price'] = number_format($product['price'], 2);
        if($product['discount_price']) {
            $product['discount_price'] = number_format($product['discount_price'], 2);
        }
        
        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No product ID provided']);
}
?>
