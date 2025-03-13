<?php
require_once 'db_config.php';
require_once 'chatbot.php';
?>
<!DOCTYPE html>db_config.phpdb_config.php
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage with Chatbot</title>
    <link rel="stylesheet" href="Style\Styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <nav class="navbar">
         <div class="nav-brand">
         <a href="index.php">
            <img src="img\Downloaded adobe logo.png" alt="Logo" class="logo-img">
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




    <!-- Main Content -->
    <main class="main-content">
        <div class="hero-section">
            <h1>Welcome to Coop Mart</h1>
            <h2>Your Trusted Community Store! 🛒🌿</h2>
        </div>

        <div class="content-wrapper">
            <p class="intro-text">
                Looking for fresh, affordable, and high-quality products? At Coop Mart, 
                we bring you the best groceries, farm-fresh produce, household essentials, 
                and exclusive SIDC products—all at prices that fit your budget!
            </p>

            <div class="features">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <p>Quality You Can Trust – We provide only the best, sourced from local farmers and trusted suppliers.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <p>Affordable Prices – Shop smart and save more with our cooperative-driven pricing!</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <p>Convenient Shopping – Multiple branches near you, plus cashless payments for hassle-free transactions.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <p>Supporting Local Communities – Every purchase helps grow and strengthen our cooperative network!</p>
                </div>
            </div>

            <div class="cta-section">
                <p class="location-text">
                    <i class="fas fa-map-marker-alt"></i>
                   Come on now and visit our nearest Coop Mart today and experience shopping with a purpose!
                </p>
                <p class="tagline">Your community. Your store. Your savings.</p>
                <div class="hashtags">
                    <span>#ShopSmart</span>
                    <span>#SupportLocal</span>
                    <span>#CoopMartPH</span>
                </div>
            </div>
        </div>
    </main>

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

    <script src="Js\script.js"></script>
</body>
</html>
    