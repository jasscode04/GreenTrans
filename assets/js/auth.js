/**
 * GreenTrans - Auth Validation
 * Real-time form validation for login and registration
 */

document.addEventListener('DOMContentLoaded', function() {

    // === VALIDATION RULES ===
    const rules = {
        name: {
            pattern: /^[A-Za-z\s]{2,100}$/,
            message: 'Only alphabets allowed (min 2 characters)'
        },
        email: {
            pattern: /^[a-zA-Z0-9._%+-]+@gmail\.com$/,
            message: 'Only Gmail accounts are allowed (@gmail.com)'
        },
        phone: {
            pattern: /^[0-9]{10}$/,
            message: 'Enter exactly 10 digits'
        },
        password: {
            minLength: 8,
            uppercase: /[A-Z]/,
            special: /[!@#$%^&*(),.?":{}|<>]/,
            message: 'Min 8 chars, 1 uppercase, 1 special character'
        }
    };

    // === REAL-TIME VALIDATION ===
    
    // Name validation
    const nameInputs = document.querySelectorAll('input[name="full_name"]');
    nameInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Remove non-alphabetic characters in real-time
            this.value = this.value.replace(/[^A-Za-z\s]/g, '');
            validateField(this, rules.name.pattern, rules.name.message);
        });
    });

    // Email validation
    const emailInputs = document.querySelectorAll('input[name="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(this, rules.email.pattern, rules.email.message);
        });
    });

    // Phone validation
    const phoneInputs = document.querySelectorAll('input[name="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Only allow digits and max 10
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            if (this.value.length === 10) {
                validateField(this, rules.phone.pattern, rules.phone.message);
            } else if (this.value.length > 0) {
                showError(this, `${10 - this.value.length} more digits needed`);
            } else {
                clearValidation(this);
            }
        });
    });

    // Password validation with strength meter
    const passwordInputs = document.querySelectorAll('input[name="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            validatePassword(this);
        });
    });

    // Confirm password validation
    const confirmInputs = document.querySelectorAll('input[name="confirm_password"]');
    confirmInputs.forEach(input => {
        input.addEventListener('input', function() {
            const password = document.querySelector('input[name="password"]');
            if (password && this.value !== password.value) {
                showError(this, 'Passwords do not match');
            } else if (this.value.length > 0) {
                showValid(this);
            }
        });
    });

    // === PASSWORD TOGGLE ===
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });

    // === ROLE SELECTOR ===
    document.querySelectorAll('.role-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });

    // === FORM SUBMISSION ===
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate all fields
            const name = this.querySelector('[name="full_name"]');
            const email = this.querySelector('[name="email"]');
            const phone = this.querySelector('[name="phone"]');
            const password = this.querySelector('[name="password"]');
            const confirm = this.querySelector('[name="confirm_password"]');

            if (name && !rules.name.pattern.test(name.value)) {
                showError(name, rules.name.message); isValid = false;
            }
            if (email && !rules.email.pattern.test(email.value)) {
                showError(email, rules.email.message); isValid = false;
            }
            if (phone && !rules.phone.pattern.test(phone.value)) {
                showError(phone, rules.phone.message); isValid = false;
            }
            if (password) {
                const strength = getPasswordStrength(password.value);
                if (strength < 3) {
                    showError(password, rules.password.message); isValid = false;
                }
            }
            if (confirm && password && confirm.value !== password.value) {
                showError(confirm, 'Passwords do not match'); isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                if (window.GTToast) GTToast.error('Please fix the errors below');
            }
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            const email = this.querySelector('[name="email"]');
            const password = this.querySelector('[name="password"]');

            if (email && !email.value.trim()) {
                showError(email, 'Email is required'); isValid = false;
            }
            if (password && !password.value.trim()) {
                showError(password, 'Password is required'); isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // === HELPER FUNCTIONS ===

    function validateField(input, pattern, message) {
        if (input.value.length === 0) {
            clearValidation(input);
            return;
        }
        if (pattern.test(input.value)) {
            showValid(input);
        } else {
            showError(input, message);
        }
    }

    function validatePassword(input) {
        const val = input.value;
        if (val.length === 0) {
            clearValidation(input);
            updateStrengthMeter(input, 0);
            return;
        }

        const strength = getPasswordStrength(val);
        updateStrengthMeter(input, strength);

        // Show specific feedback
        let errors = [];
        if (val.length < 8) errors.push('Min 8 characters');
        if (!/[A-Z]/.test(val)) errors.push('1 uppercase letter');
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(val)) errors.push('1 special character');

        if (errors.length > 0) {
            showError(input, 'Need: ' + errors.join(', '));
        } else {
            showValid(input);
        }
    }

    function getPasswordStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        return score;
    }

    function updateStrengthMeter(input, strength) {
        const group = input.closest('.gt-form-group');
        if (!group) return;
        
        const bar = group.querySelector('.strength-fill');
        const text = group.querySelector('.password-strength-text');
        
        if (!bar || !text) return;

        const levels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        const classes = ['', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
        const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];

        bar.className = 'strength-fill ' + (classes[strength] || '');
        text.textContent = levels[strength] || '';
        text.style.color = colors[strength] || '';
    }

    function showError(input, message) {
        const group = input.closest('.gt-form-group');
        if (!group) return;
        
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        group.classList.remove('valid');
        group.classList.add('invalid');
        
        const errorEl = group.querySelector('.gt-form-error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('show');
        }
    }

    function showValid(input) {
        const group = input.closest('.gt-form-group');
        if (!group) return;
        
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        group.classList.remove('invalid');
        group.classList.add('valid');
        
        const errorEl = group.querySelector('.gt-form-error');
        if (errorEl) errorEl.classList.remove('show');
    }

    function clearValidation(input) {
        const group = input.closest('.gt-form-group');
        if (!group) return;
        
        input.classList.remove('is-invalid', 'is-valid');
        group.classList.remove('invalid', 'valid');
        
        const errorEl = group.querySelector('.gt-form-error');
        if (errorEl) errorEl.classList.remove('show');
    }
});
