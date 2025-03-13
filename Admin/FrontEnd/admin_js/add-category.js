document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addCategoryForm');
    const categoryName = document.getElementById('categoryName');
    const categoryDescription = document.getElementById('categoryDescription');
    const iconItems = document.querySelectorAll('.icon-item');
    const iconPreview = document.querySelector('.icon-preview i');

    // Character Counter
    function updateCharacterCount(input, maxLength) {
        const counter = input.parentElement.querySelector('.character-count');
        const currentLength = input.value.length;
        counter.textContent = `${currentLength}/${maxLength}`;
    }

    categoryName.addEventListener('input', () => {
        updateCharacterCount(categoryName, 50);
    });

    categoryDescription.addEventListener('input', () => {
        updateCharacterCount(categoryDescription, 200);
    });

    // Icon Selection
    iconItems.forEach(item => {
        item.addEventListener('click', () => {
            // Remove selected class from all items
            iconItems.forEach(i => i.classList.remove('selected'));
            // Add selected class to clicked item
            item.classList.add('selected');
            // Update icon preview
            const iconClass = item.dataset.icon;
            iconPreview.className = `fas ${iconClass}`;
            // Update hidden input
            document.getElementById('categoryIcon').value = iconClass;
        });
    });

    // Form Validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            // Here you would typically send the data to your backend
            const formData = {
                name: categoryName.value,
                description: categoryDescription.value,
                icon: document.getElementById('categoryIcon').value,
                status: document.getElementById('categoryStatus').checked
            };
            
            console.log('Form data:', formData);
            showSuccessMessage('Category added successfully!');
            form.reset();
            resetForm();
        }
    });

    function validateForm() {
        let isValid = true;

        // Validate Category Name
        if (!categoryName.value.trim()) {
            showError(categoryName, 'Category name is required');
            isValid = false;
        } else if (categoryName.value.length < 3) {
            showError(categoryName, 'Category name must be at least 3 characters');
            isValid = false;
        } else {
            clearError(categoryName);
        }

        return isValid;
    }

    function showError(field, message) {
        const errorElement = field.parentElement.querySelector('.error-message');
        errorElement.textContent = message;
        field.style.borderColor = '#e74c3c';
    }

    function clearError(field) {
        const errorElement = field.parentElement.querySelector('.error-message');
        errorElement.textContent = '';
        field.style.borderColor = '#ddd';
    }

    function resetForm() {
        // Reset icon selection
        iconItems.forEach(i => i.classList.remove('selected'));
        iconPreview.className = 'fas fa-tag';
        document.getElementById('categoryIcon').value = '';

        // Reset character counts
        const counters = document.querySelectorAll('.character-count');
        counters.forEach(counter => counter.textContent = '0/50');

        // Clear any errors
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(error => error.textContent = '');
    }

    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.textContent = message;
        document.body.appendChild(successDiv);

        setTimeout(() => {
            successDiv.remove();
        }, 3000);
    }
});
