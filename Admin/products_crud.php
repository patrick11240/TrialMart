<?php
session_start();
require_once '../db_config.php';

header('Content-Type: application/json');

// Function to handle file upload
function handleFileUpload($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $filename = $file['name'];
    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("File size too large. Maximum size is 5MB");
    }
    
    if (!in_array($filetype, $allowed)) {
        throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and WEBP files are allowed");
    }
    
    $new_filename = uniqid() . '.' . $filetype;
    $upload_path = 'uploads/products/' . $new_filename;
    
    if (!file_exists('uploads/products/')) {
        if (!mkdir('uploads/products/', 0777, true)) {
            throw new Exception("Failed to create upload directory");
        }
    }
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception("Error uploading file");
    }
    
    return $new_filename;
}

try {
    if (!isset($_POST['ajax_action'])) {
        throw new Exception("Invalid request");
    }
    
    switch ($_POST['ajax_action']) {
        case 'read':
            $query = "SELECT p.*, c.category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.category_id 
                     ORDER BY p.product_id DESC";
            $result = mysqli_query($conn, $query);
            
            if (!$result) {
                throw new Exception("Error fetching products: " . mysqli_error($conn));
            }
            
            $products = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $row['product_image'] = 'uploads/products/' . $row['product_image'];
                $products[] = $row;
            }
            
            echo json_encode(['success' => true, 'products' => $products]);
            break;
            
        case 'update':
            if (empty($_POST['product_id']) || empty($_POST['product_name']) || 
                empty($_POST['category_id']) || empty($_POST['price'])) {
                throw new Exception("Please fill in all required fields");
            }
            
            $product_id = (int)$_POST['product_id'];
            $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
            $category_id = (int)$_POST['category_id'];
            $price = (float)$_POST['price'];
            $stock = (int)$_POST['stock'];
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            
            mysqli_begin_transaction($conn);
            
            try {
                if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                    $new_filename = handleFileUpload($_FILES['product_image']);
                    
                    // Get and delete old image
                    $query = "SELECT product_image FROM products WHERE product_id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "i", $product_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $old_image = mysqli_fetch_assoc($result)['product_image'];
                    
                    if ($old_image && file_exists('uploads/products/' . $old_image)) {
                        unlink('uploads/products/' . $old_image);
                    }
                    
                    $query = "UPDATE products SET product_name = ?, category_id = ?, price = ?, 
                             stock_quantity = ?, description = ?, product_image = ? 
                             WHERE product_id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "sidissi", $product_name, $category_id, $price, 
                                         $stock, $description, $new_filename, $product_id);
                } else {
                    $query = "UPDATE products SET product_name = ?, category_id = ?, price = ?, 
                             stock_quantity = ?, description = ? WHERE product_id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "sidisi", $product_name, $category_id, $price, 
                                         $stock, $description, $product_id);
                }
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error updating product: " . mysqli_error($conn));
                }
                
                mysqli_commit($conn);
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            } catch (Exception $e) {
                mysqli_rollback($conn);
                throw $e;
            }
            break;
            
        case 'delete':
            if (empty($_POST['product_id'])) {
                throw new Exception("Invalid product ID");
            }
            
            $product_id = (int)$_POST['product_id'];
            
            mysqli_begin_transaction($conn);
            
            try {
                // Get image filename before deletion
                $query = "SELECT product_image FROM products WHERE product_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $product_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $product = mysqli_fetch_assoc($result);
                
                // Delete from database
                $query = "DELETE FROM products WHERE product_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $product_id);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error deleting product: " . mysqli_error($conn));
                }
                
                // Delete image file
                if ($product && $product['product_image']) {
                    $image_path = 'uploads/products/' . $product['product_image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                
                mysqli_commit($conn);
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            } catch (Exception $e) {
                mysqli_rollback($conn);
                throw $e;
            }
            break;
            
        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
