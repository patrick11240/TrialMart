
        // Image preview
        document.getElementById('product_image').onchange = function(evt) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('image-preview');
                if (!preview) {
                    const img = document.createElement('img');
                    img.id = 'image-preview';
                    this.parentNode.appendChild(img);
                }
                const previewImg = document.getElementById('image-preview');
                previewImg.src = URL.createObjectURL(file);
            }
        };

        // Theme toggle
        document.getElementById('theme-toggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-sun');
            icon.classList.toggle('fa-moon');
        });

        // Sidebar toggle
        document.getElementById('toggle-sidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('expanded');
        });
        // Function to load products
function loadProducts() {
    $.post('add-product.php', { ajax_action: 'get_products' }, function(response) {
        if (response.success) {
            const tbody = $('#productsTable tbody');
            tbody.empty();
            
            response.products.forEach(product => {
                tbody.append(`
                    <tr data-id="${product.product_id}">
                        <td><img src="${product.product_image}" alt="${product.product_name}" class="product-thumbnail"></td>
                        <td>${product.product_name}</td>
                        <td>${product.category_name}</td>
                        <td>$${parseFloat(product.price).toFixed(2)}</td>
                        <td>${product.stock_quantity}</td>
                        <td>${product.description || ''}</td>
                        <td class="action-buttons">
                            <button class="btn btn-warning btn-sm" onclick="editProduct(${product.product_id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.product_id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }
    });
}

// Delete product
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        $.post('add-product.php', {
            ajax_action: 'delete',
            product_id: productId
        }, function(response) {
            if (response.success) {
                $(`tr[data-id="${productId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
                toastr.success('Product deleted successfully');
            } else {
                toastr.error('Error deleting product');
            }
        });
    }
}

// Edit product
function editProduct(productId) {
    const row = $(`tr[data-id="${productId}"]`);
    
    $('#edit_product_id').val(productId);
    $('#edit_product_name').val(row.find('td:eq(1)').text());
    $('#edit_category_id').val(row.data('category-id'));
    $('#edit_price').val(row.find('td:eq(3)').text().replace('$', ''));
    $('#edit_stock').val(row.find('td:eq(4)').text());
    $('#edit_description').val(row.find('td:eq(5)').text());
    $('#current_image_preview').attr('src', row.find('img').attr('src'));
    
    $('#editProductModal').modal('show');
}

// Handle edit form submission
$('#saveProductChanges').click(function() {
    const formData = new FormData($('#editProductForm')[0]);
    formData.append('ajax_action', 'update');
    
    $.ajax({
        url: 'add-product.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#editProductModal').modal('hide');
                loadProducts();
                toastr.success('Product updated successfully');
            } else {
                toastr.error('Error updating product: ' + response.error);
            }
        },
        error: function() {
            toastr.error('Error updating product');
        }
    });
});

// Load products on page load
$(document).ready(function() {
    loadProducts();
    
    // Refresh products after new product is added
    $('form').on('submit', function() {
        setTimeout(loadProducts, 1000);
    });
});

// Preview image before upload in edit modal
$('#edit_product_image').change(function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#current_image_preview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }
});
