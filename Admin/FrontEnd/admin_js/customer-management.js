document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const customerModal = document.getElementById('customerModal');
    const deleteModal = document.getElementById('deleteModal');
    const customerForm = document.getElementById('customerForm');
    const addCustomerBtn = document.getElementById('addCustomerBtn');
    const closeModal = document.getElementById('closeModal');
    const closeDeleteModal = document.getElementById('closeDeleteModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDelete = document.getElementById('cancelDelete');
    const confirmDelete = document.getElementById('confirmDelete');
    const searchInput = document.getElementById('searchCustomers');
    const statusFilter = document.getElementById('statusFilter');

    // Sample Customers Data
    let customers = [
        {
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: 'active',
            lastLogin: '2024-03-20 10:30 AM'
        },
        {
            id: 2,
            name: 'Jane Smith',
            email: 'jane@example.com',
            status: 'active',
            lastLogin: '2024-03-19 03:45 PM'
        }
    ];

    // Initialize table
    renderCustomersTable(customers);

    // Event Listeners
    addCustomerBtn.addEventListener('click', () => {
        openModal();
    });

    closeModal.addEventListener('click', () => {
        customerModal.style.display = 'none';
    });

    closeDeleteModal.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
        customerModal.style.display = 'none';
    });

    cancelDelete.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    // Form Submit Handler
    customerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            const formData = {
                id: Date.now(),
                name: document.getElementById('fullName').value,
                email: document.getElementById('email').value,
                status: document.getElementById('status').checked ? 'active' : 'inactive',
                lastLogin: 'Never'
            };

            customers.push(formData);
            renderCustomersTable(customers);
            customerModal.style.display = 'none';
            showSuccessMessage('Customer added successfully!');
            customerForm.reset();
        }
    });

    // Search and Filter Handlers
    searchInput.addEventListener('input', filterCustomers);
    statusFilter.addEventListener('change', filterCustomers);

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
    function openModal(customerId = null) {
        const modalTitle = document.getElementById('modalTitle');
        if (customerId) {
            modalTitle.textContent = 'Edit Customer';
            const customer = customers.find(c => c.id === customerId);
            if (customer) {
                document.getElementById('fullName').value = customer.name;
                document.getElementById('email').value = customer.email;
                document.getElementById('status').checked = customer.status === 'active';
            }
        } else {
            modalTitle.textContent = 'Add New Customer';
            customerForm.reset();
        }
        customerModal.style.display = 'block';
    }

    function openDeleteModal(customerId) {
        deleteModal.style.display = 'block';
        confirmDelete.onclick = function() {
            customers = customers.filter(customer => customer.id !== customerId);
            renderCustomersTable(customers);
            deleteModal.style.display = 'none';
            showSuccessMessage('Customer deleted successfully!');
        };
    }

    function renderCustomersTable(customersData) {
        const tbody = document.getElementById('customersTableBody');
        tbody.innerHTML = '';

        customersData.forEach(customer => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${customer.name}</td>
                <td>${customer.email}</td>
                <td>
                    <span class="status-badge status-${customer.status}">
                        ${customer.status.charAt(0).toUpperCase() + customer.status.slice(1)}
                    </span>
                </td>
                <td>${customer.lastLogin}</td>
                <td>
                    <button class="btn btn-secondary" onclick="editCustomer(${customer.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger" onclick="deleteCustomer(${customer.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function filterCustomers() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        const filteredCustomers = customers.filter(customer => {
            const matchesSearch = customer.name.toLowerCase().includes(searchTerm) ||
                                customer.email.toLowerCase().includes(searchTerm);
            const matchesStatus = !statusValue || customer.status === statusValue;

            return matchesSearch && matchesStatus;
        });

        renderCustomersTable(filteredCustomers);
    }

    function validateForm() {
        let isValid = true;
        const fullName = document.getElementById('fullName');
        const email = document.getElementById('email');
        const password = document.getElementById('password');

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

    // Make functions available globally
    window.editCustomer = function(customerId) {
        openModal(customerId);
    };

    window.deleteCustomer = function(customerId) {
        openDeleteModal(customerId);
    };
});
