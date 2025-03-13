// Form toggle functionality
function toggleForms(formType) {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const forgotForm = document.getElementById('forgotForm');

    // Hide all forms first
    [loginForm, signupForm, forgotForm].forEach(form => {
        form.classList.add('hidden');
    });

    // Show the requested form
    switch(formType) {
        case 'login':
            loginForm.classList.remove('hidden');
            break;
        case 'signup':
            signupForm.classList.remove('hidden');
            break;
        case 'forgot':
            forgotForm.classList.remove('hidden');
            break;
    }
}

// Show forgot password form
function showForgotPassword() {
    toggleForms('forgot');
}

// Handle Login
function handleLogin(event) {
    event.preventDefault();
    const form = event.target;
    const email = form.email.value;
    const password = form.password.value;
    const remember = form.remember.checked;

    // Add your login logic here
    console.log('Login attempt:', { email, password, remember });

    // Example validation
    if (email && password) {
        // Add your API call here
        alert('Login successful!');
        // Redirect to dashboard or home page
        // window.location.href = 'dashboard.html';
    }

    return false;
}

// Handle Signup
function handleSignup(event) {
    event.preventDefault();
    const form = event.target;
    const fullname = form.fullname.value;
    const email = form.email.value;
    const password = form.password.value;
    const confirmPassword = form.confirm_password.value;

    // Password validation
    if (password !== confirmPassword) {
        alert("Passwords don't match!");
        return false;
    }

    // Add your signup logic here
    console.log('Signup attempt:', { fullname, email, password });

    // Example validation
    if (fullname && email && password) {
        // Add your API call here
        alert('Registration successful! Please login.');
        toggleForms('login');
        form.reset();
    }

    return false;
}

// Handle Forgot Password
function handleForgotPassword(event) {
    event.preventDefault();
    const form = event.target;
    const email = form.email.value;

    // Add your password reset logic here
    console.log('Password reset attempt:', { email });

    // Example
    if (email) {
        // Add your API call here
        alert('Password reset instructions have been sent to your email.');
        toggleForms('login');
        form.reset();
    }

    return false;
}

// Initialize the form
document.addEventListener('DOMContentLoaded', () => {
    // Show login form by default
    toggleForms('login');
});
function toggleForms(formType) {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const forgotForm = document.getElementById('forgotForm');

    // Hide all forms
    [loginForm, signupForm, forgotForm].forEach(form => {
        form.classList.add('hidden');
    });

    // Show selected form
    switch(formType) {
        case 'login':
            loginForm.classList.remove('hidden');
            break;
        case 'signup':
            signupForm.classList.remove('hidden');
            break;
        case 'forgot':
            forgotForm.classList.remove('hidden');
            break;
    }
}

// Show login form by default
document.addEventListener('DOMContentLoaded', () => {
    toggleForms('login');
});
function toggleForms(formType) {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const forgotForm = document.getElementById('forgotForm');

    // Hide all forms
    [loginForm, signupForm, forgotForm].forEach(form => {
        form.classList.add('hidden');
    });

    // Show selected form
    switch(formType) {
        case 'login':
            loginForm.classList.remove('hidden');
            break;
        case 'signup':
            signupForm.classList.remove('hidden');
            break;
        case 'forgot':
            forgotForm.classList.remove('hidden');
            break;
    }
}

// Show login form by default
document.addEventListener('DOMContentLoaded', () => {
    toggleForms('login');
});
function toggleForms(formType) {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const forgotForm = document.getElementById('forgotForm');

    switch(formType) {
        case 'login':
            loginForm.classList.remove('hidden');
            signupForm.classList.add('hidden');
            forgotForm.classList.add('hidden');
            break;
        case 'signup':
            loginForm.classList.add('hidden');
            signupForm.classList.remove('hidden');
            forgotForm.classList.add('hidden');
            break;
        case 'forgot':
            loginForm.classList.add('hidden');
            signupForm.classList.add('hidden');
            forgotForm.classList.remove('hidden');
            break;
    }
}
