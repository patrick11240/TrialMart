let isChatOpen = false;

function toggleChat() {
    const chatContainer = document.getElementById('chatContainer');
    isChatOpen = !isChatOpen;
    chatContainer.style.display = isChatOpen ? 'block' : 'none';
    
    if (isChatOpen) {
        // Send welcome message when chat is opened
        const chatMessages = document.getElementById('chatMessages');
        const welcomeMessage = document.createElement('div');
        welcomeMessage.style.textAlign = 'left';
        welcomeMessage.style.margin = '10px 0';
        welcomeMessage.innerHTML = `<span style="background-color: #e9ecef; padding: 5px 10px; border-radius: 10px;">Hello! How can I help you today?</span>`;
        chatMessages.appendChild(welcomeMessage);
    }
}

function sendMessage() {
    const userInput = document.getElementById('userMessage');
    const message = userInput.value.trim();
    
    if (message !== '') {
        const chatMessages = document.getElementById('chatMessages');
        
        // Add user message
        const userMessageDiv = document.createElement('div');
        userMessageDiv.style.textAlign = 'right';
        userMessageDiv.style.margin = '10px 0';
        userMessageDiv.innerHTML = `<span style="background-color: #007bff; color: white; padding: 5px 10px; border-radius: 10px;">${message}</span>`;
        chatMessages.appendChild(userMessageDiv);

        // Send message to server and get response
        fetch('chatbot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `message=${encodeURIComponent(message)}`
        })
        .then(response => response.json())
        .then(data => {
            const botMessageDiv = document.createElement('div');
            botMessageDiv.style.textAlign = 'left';
            botMessageDiv.style.margin = '10px 0';
            botMessageDiv.innerHTML = `<span style="background-color: #e9ecef; padding: 5px 10px; border-radius: 10px;">${data.response}</span>`;
            chatMessages.appendChild(botMessageDiv);
            
            // Auto scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        })
        .catch(error => {
            console.error('Error:', error);
        });

        // Clear input
        userInput.value = '';
        
        // Auto scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// Allow Enter key to send message
document.getElementById('userMessage').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});
// Add smooth scrolling
document.addEventListener('DOMContentLoaded', () => {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Add animation to feature items
    const features = document.querySelectorAll('.feature-item');
    features.forEach((feature, index) => {
        feature.style.opacity = '0';
        feature.style.transform = 'translateY(20px)';
        setTimeout(() => {
            feature.style.transition = 'all 0.5s ease';
            feature.style.opacity = '1';
            feature.style.transform = 'translateY(0)';
        }, 200 * index);
    });

    // Add hover effect to hashtags
    const hashtags = document.querySelectorAll('.hashtags span');
    hashtags.forEach(tag => {
        tag.addEventListener('mouseover', () => {
            tag.style.transform = 'scale(1.1)';
            tag.style.transition = 'transform 0.3s ease';
        });
        tag.addEventListener('mouseout', () => {
            tag.style.transform = 'scale(1)';
        });
    });
});
let cart = [];

function addToCart(productId) {
    // Get product details from the product card
    const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
    const productName = productCard.querySelector('.product-name').textContent;
    const productPrice = productCard.querySelector('.product-price').textContent;
    const productImage = productCard.querySelector('.product-image').src;

    // Add to cart array
    cart.push({
        id: productId,
        name: productName,
        price: productPrice,
        image: productImage,
        quantity: 1
    });

    updateCartDisplay();
    showNotification('Product added to cart!');
}

function updateCartDisplay() {
    const cartItems = document.querySelector('.cart-items');
    const cartCount = document.querySelector('.cart-count');
    
    // Update cart items display
    cartItems.innerHTML = cart.map(item => `
        <div class="cart-item" data-product-id="${item.id}">
            <img src="${item.image}" alt="${item.name}" class="cart-item-image">
            <div class="cart-item-details">
                <h4>${item.name}</h4>
                <div class="cart-item-price">${item.price}</div>
                <div class="cart-item-quantity">
                    <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                </div>
            </div>
            <button class="remove-item" onclick="removeFromCart(${item.id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `).join('');

    // Update cart count
    cartCount.textContent = `${cart.length} items`;

    // Update total
    updateCartTotal();
}

function updateQuantity(productId, newQuantity) {
    if (newQuantity < 1) return;
    
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity = newQuantity;
        updateCartDisplay();
    }
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay();
    showNotification('Product removed from cart!');
}

function updateCartTotal() {
    const total = cart.reduce((sum, item) => {
        const price = parseFloat(item.price.replace('$', ''));
        return sum + (price * item.quantity);
    }, 0);

    document.querySelector('.cart-total h3').textContent = `Total: $${total.toFixed(2)}`;
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after animation
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function notifyWhenAvailable(productId) {
    // Implement notification functionality
    showNotification('You will be notified when this product is available!');
}

function proceedToCheckout() {
    if (cart.length === 0) {
        showNotification('Your cart is empty!');
        return;
    }
    // Implement checkout functionality
    window.location.href = 'checkout.php';
}

// Sort functionality
document.getElementById('sort-products').addEventListener('change', function(e) {
    const sortValue = e.target.value;
    const productsContainer = document.querySelector('.products-container');
    const products = Array.from(productsContainer.getElementsByClassName('product-card'));

    products.sort((a, b) => {
        const aPrice = parseFloat(a.querySelector('.product-price').textContent.replace('$', ''));
        const bPrice = parseFloat(b.querySelector('.product-price').textContent.replace('$', ''));
        const aName = a.querySelector('.product-name').textContent;
        const bName = b.querySelector('.product-name').textContent;

        switch(sortValue) {
            case 'price-low':
                return aPrice - bPrice;
            case 'price-high':
                return bPrice - aPrice;
            case 'name-asc':
                return aName.localeCompare(bName);
            default: // newest
                return b.dataset.productId - a.dataset.productId;
        }
    });

    productsContainer.innerHTML = '';
    products.forEach(product => productsContainer.appendChild(product));
});

// Mobile cart toggle
document.querySelector('.cart-toggle').addEventListener('click', function() {
    document.querySelector('.shopping-cart').classList.toggle('active');
});
        // Cart Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            const removeButtons = document.querySelectorAll('.remove-item');
            const cartToggle = document.querySelector('.cart-toggle');
            const shoppingCart = document.querySelector('.shopping-cart');
            const cartCount = document.querySelector('.cart-count');
            let cartItems = [];

            // Add to Cart
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const card = e.target.closest('.product-card');
                    const product = {
                        name: card.querySelector('.product-name').textContent,
                        price: card.querySelector('.product-price').textContent,
                        image: card.querySelector('.product-image').src
                    };
                    
                    addToCart(product);
                    updateCartUI();
                });
            });

            // Remove from Cart
            removeButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const item = e.target.closest('.cart-item');
                    item.remove();
                    updateCartTotal();
                });
            });

            // Mobile Cart Toggle
            cartToggle.addEventListener('click', function() {
                shoppingCart.classList.toggle('active');
            });

            function addToCart(product) {
                cartItems.push(product);
                updateCartCount();
            }

            function updateCartCount() {
                cartCount.textContent = `${cartItems.length} items`;
            }

            function updateCartUI() {
                const cartItemsContainer = document.querySelector('.cart-items');
                cartItemsContainer.innerHTML = cartItems.map(item => `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}">
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">${item.price}</div>
                        </div>
                        <div class="remove-item">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                `).join('');

                updateCartTotal();
            }

            function updateCartTotal() {
                const total = cartItems.reduce((sum, item) => {
                    const price = parseFloat(item.price.replace('$', ''));
                    return sum + price;
                }, 0);

                document.querySelector('.cart-total h3').textContent = `Total: $${total.toFixed(2)}`;
            }
        });
        