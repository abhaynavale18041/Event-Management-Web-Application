/**
 * Event Management System - JavaScript
 */

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.getElementById('mobileToggle');
    const nav = document.getElementById('nav');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            nav.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (nav && nav.classList.contains('active') && 
            !nav.contains(event.target) && 
            !mobileToggle.contains(event.target)) {
            nav.classList.remove('active');
        }
    });
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Basic client-side validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    isValid = false;
                    field.classList.add('error');
                }
            });
            
            // Password confirmation validation
            const passwordField = form.querySelector('#password');
            const confirmPasswordField = form.querySelector('#confirm_password');
            
            if (passwordField && confirmPasswordField) {
                if (passwordField.value !== confirmPasswordField.value) {
                    isValid = false;
                    confirmPasswordField.classList.add('error');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
            }
        });
    });
});

// Email validation helper
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Password strength indicator (optional enhancement)
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    
    if (passwordField && document.getElementById('confirm_password')) {
        passwordField.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            // You can add visual feedback here if needed
        });
    }
});

function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    return strength;
}

// Auto-dismiss success messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successAlerts = document.querySelectorAll('.alert-success');
    
    successAlerts.forEach(alert => {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});

// Confirm before delete
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Image preview for file upload
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add image preview functionality here
                    console.log('Image selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Search form enhancement - clear button
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    
    if (searchInput && searchInput.value) {
        // Add visual feedback that search is active
        searchInput.style.fontStyle = 'italic';
    }
});

