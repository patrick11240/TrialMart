document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const userModal = document.getElementById('userModal');
    const deleteModal = document.getElementById('deleteModal');
    const userForm = document.getElementById('userForm');
    const addUserBtn = document.getElementById('addUserBtn');
    const closeModal = document.getElementById('closeModal');
    const closeDeleteModal = document.getElementById('closeDeleteModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDelete = document.getElementById('cancelDelete');
    const confirmDelete = document.getElementById('confirmDelete');
    const searchInput = document.getElementById('searchUsers');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');

    // Sample Users Data
    let users = [
        {
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            role: 'super-admin',
            status: 'active',
            lastLogin: '2024-03-20 10:30 AM'
        },
        {
            id: 2,
            name: 'Jane Smith',
            email: 'jane@example.com',
            role: 'admin',
            status: 'active',
            lastLogin: '2024-03-19 03:45 PM'
        }
    ];

    // Initialize table
    renderUsersTable(users);

    // Event Listeners
    addUserBtn.addEventListener('click', () => {
        openModal();
    });

    closeModal.addEventListener('click', () => {
        userModal.style.display = 'none';
    });

    closeDeleteModal.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
        userModal.style.display = 'none';
    });

    cancelDelete.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    // Form Submit Handler
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            const formData = {
                id: Date.now(), // Generate temporary ID
                name: document.getElementById('fullName').value,
                email: document.getElementById('email').value,
                role: document.getElementById('role').value,
                status: document.getElementById('status').checked ? 'active' : 'inactive',
                lastLogin: 'Never'
            };

            users.push(formData);
            renderUsersTable(users);
            userModal.style.display = 'none';
            showSuccessMessage('User added successfully!');
            userForm.reset();
        }
    });

    // Search and Filter Handlers
    searchInput.addEventListener('input', filterUsers);
    roleFilter.addEventListener('change', filterUsers);
    statusFilter.addEventListener('change', filterUsers);

    // Toggle Password Visibility
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Functions
    function openModal(userId = null) {
        const modalTitle = document.getElementById('modalTitle');
        if (userId) {
            modalTitle.textContent = 'Edit Admin User';
            // Populate form with user data
            const user = users.find(u => u.id === userId);
            if (user) {
                document.getElementById('fullName').value = user.name;
                document.getElementById('email').value = user.email;
                document.getElementById('role').value = user.role;
                document.getElementById('status').checked = user.status === 'active';
            }
        } else {
            modalTitle.textContent = 'Add New Admin User';
            userForm.reset();
        }
        userModal.style.display = 'block';
    }

    function openDeleteModal(userId) {
        deleteModal.style.display = 'block';
        confirmDelete.onclick = function() {
            users = users.filter(user => user.id !== userId);
            renderUsersTable(users);
            deleteModal.style.display = 'none';
            showSuccessMessage('User deleted successfully!');
        };
    }

    function renderUsersTable(usersData) {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';

        usersData.forEach(user => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="user-info">
                        <div class="user-avatar">${user.name.charAt(0)}</div>
                        <div>
                            <div>${user.name}</div>
                        </div>
                    </div>
                </td>
                <td>${user.email}</td>
                <td>${capitalizeFirstLetter(user.role)}</td>
                <td>
                    <span class="status-badge status-${user.status}">
                        ${capitalizeFirstLetter(user.status)}
                    </span>
                </td>
                <td>${user.lastLogin}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn edit-btn" onclick="editUser(${user.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteUser(${user.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value;
        const statusValue = statusFilter.value;

        const filteredUsers = users.filter(user => {
            const matchesSearch = user.name.toLowerCase().includes(searchTerm) ||
                                user.email.toLowerCase().includes(searchTerm);
            const matchesRole = !roleValue || user.role === roleValue;
            const matchesStatus = !statusValue || user.status === statusValue;

            return matchesSearch && matchesRole && matchesStatus;
        });

        renderUsersTable(filteredUsers);
    }

    function validateForm() {
        let isValid = true;
        const fullName = document.getElementById('fullName');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const role = document.getElementById('role');

        // Validate Full Name
        if (!fullName.value.trim()) {
            showError(fullName, 'Full name is required');
            isValid = false;
        } else {
            clearError(fullName);
        }

        // Validate Email
        if (!email.value.trim()) {
            showError(email, 'Email is required');
            isValid = false;
        } else if (!isValidEmail(email.value)) {
            showError(email, 'Please enter a valid email address');
            isValid = false;
        } else {
            clearError(email);
        }

        // Validate Password
        if (!password.value) {
            showError(password, 'Password is required');
            isValid = false;
        } else if (password.value.length < 6) {
            showError(password, 'Password must be at least 6 characters');
            isValid = false;
        } else {
            clearError(password);
        }

        // Validate Role
        if (!role.value) {
            showError(role, 'Please select a role');
            isValid = false;
        } else {
            clearError(role);
        }

        return isValid;
    }

    function showError(field, message) {
        const errorElement = field.parentElement.querySelector('.error-message');
        errorElement.textContent = message;
        field.style.borderColor = 'var(--danger-color)';
    }

    function clearError(field) {
        const errorElement = field.parentElement.querySelector('.error-message');
        errorElement.textContent = '';
        field.style.borderColor = 'var(--border-color)';
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: var(--shadow);
            z-index: 1000;
        `;
        successDiv.textContent = message;
        document.body.appendChild(successDiv);

        setTimeout(() => {
            successDiv.remove();
        }, 3000);
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Make functions available globally
    window.editUser = function(userId) {
        openModal(userId);
    };

    window.deleteUser = function(userId) {
        openDeleteModal(userId);
    };
});
