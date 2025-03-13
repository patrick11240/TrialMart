<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_Login.php';

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    try {
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT id, fullname, password FROM app_users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify user credentials
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Security measure
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];

            // Remember me feature
            if ($remember) {
                $remember_token = bin2hex(random_bytes(32));
                $remember_expires = date('Y-m-d H:i:s', strtotime('+30 days'));

                $stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user['id'], $remember_token, $remember_expires);
                $stmt->execute();

                setcookie('remember_token', $remember_token, time() + (86400 * 30), "/"); // 30 days
            }

            // Redirect to index.php after login
            header("Location: index.php");
            exit();
        } else {
            throw new Exception("Invalid email or password.");
        }
    } catch (Exception $e) {
        $login_error = $e->getMessage();
    }
}

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    try {
        $fullname = sanitize_input($_POST['fullname']);
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long.");
        }
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match.");
        }

        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM app_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            throw new Exception("Email already exists.");
        }

        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO app_users (fullname, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $email, $hashed_password);

        if ($stmt->execute()) {
            $register_success = "Registration successful! Please log in.";
        } else {
            throw new Exception("Registration failed. Please try again.");
        }
    } catch (Exception $e) {
        $register_error = $e->getMessage();
    }
}

// Handle Forgot Password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['forgot_password'])) {
    try {
        $email = sanitize_input($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        $stmt = $conn->prepare("SELECT id FROM app_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $conn->prepare("UPDATE app_users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $expiry, $email);
            $stmt->execute();

            // Simulated email sending
            $forgot_success = "If an account exists with this email, you will receive password reset instructions.";
        } else {
            $forgot_success = "If an account exists with this email, you will receive password reset instructions.";
        }
    } catch (Exception $e) {
        $forgot_error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatBot Login System</title>
    <link rel="stylesheet" href="Style\Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .logo {
    width: 107px;
    height: 80px;
    margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forms-wrapper">
            <!-- Logo Section -->
            <div class="logo-section">
                <img src="img\Downloaded adobe logo.png" alt="Logo" class="logo">
                <h1>Welcome to CoopMart</h1>
            </div>

            <!-- Login Form -->
            <div class="form-container" id="loginForm">
                <h2>Right on Target, On The Budget</h2>
                <?php if(isset($login_error)): ?>
                    <div class="error-message"><?php echo $login_error; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="options-group">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            Remember me
                        </label>
                        <a href="#" onclick="toggleForms('forgot')" class="forgot-link">Forgot Password?</a>
                    </div>
                    <button type="submit" name="login" class="btn-primary">Login</button>
                    <p class="switch-text">
                        Go Here? 
                        <a href="#" onclick="toggleForms('signup')">Create Account</a>
                    </p>
                </form>
            </div>

            <!-- Sign Up Form -->
            <div class="form-container hidden" id="signupForm">
                <h2>Create Account</h2>
                <?php if(isset($register_error)): ?>
                    <div class="error-message"><?php echo $register_error; ?></div>
                <?php endif; ?>
                <?php if(isset($register_success)): ?>
                    <div class="success-message"><?php echo $register_success; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="fullname" placeholder="Full Name" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" name="register" class="btn-primary">Sign Up</button>
                    <p class="switch-text">
                        Already have an account? 
                        <a href="#" onclick="toggleForms('login')">Login</a>
                    </p>
                </form>
            </div>

            <!-- Forgot Password Form -->
            <div class="form-container hidden" id="forgotForm">
                <h2>Reset Password</h2>
                <?php if(isset($forgot_error)): ?>
                    <div class="error-message"><?php echo $forgot_error; ?></div>
                <?php endif; ?>
                <?php if(isset($forgot_success)): ?>
                    <div class="success-message"><?php echo $forgot_success; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" name="forgot_password" class="btn-primary">Reset Password</button>
                    <p class="switch-text">
                        Remember your password? 
                        <a href="#" onclick="toggleForms('login')">Back to Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <script src="Js\Login.js"></script>
</body>
</html>
