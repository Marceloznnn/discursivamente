document.addEventListener('DOMContentLoaded', () => {
    // Password visibility toggle
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.querySelector('.toggle-password');
    const toggleIcon = toggleBtn.querySelector('i');

    toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        toggleIcon.classList.toggle('icon-eye');
        toggleIcon.classList.toggle('icon-eye-off');
        toggleBtn.setAttribute('aria-label', isPassword ? 'Ocultar senha' : 'Mostrar senha');
    });

    // Simple email validation
    const form = document.querySelector('.auth-form');
    const emailInput = document.getElementById('email');

    form.addEventListener('submit', (e) => {
        const email = emailInput.value.trim();
        if (!validateEmail(email)) {
            e.preventDefault();
            showError('Por favor, insira um e-mail válido.');
            emailInput.focus();
        }
    });

    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\\.,;:\s@\"]+\.)+[^<>()[\]\\.,;:\s@\"]{2,})$/i;
        return re.test(email);
    }

    function showError(message) {
        // Remove existing error alert
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) existingAlert.remove();

        const alertDiv = document.createElement('div');
        alertDiv.classList.add('alert', 'alert-danger');
        alertDiv.innerHTML = `
            <i class="icon-warning"></i>
            <div class="alert-content">
                <strong>Erro:</strong> ${message}
            </div>
        `;
        form.parentNode.insertBefore(alertDiv, form);
    }

    // Remember me: save email locally
    const rememberCheckbox = document.querySelector('input[name="remember_me"]');
    if (rememberCheckbox) {
        // Populate email if saved
        const savedEmail = localStorage.getItem('rememberedEmail');
        if (savedEmail) {
            emailInput.value = savedEmail;
            rememberCheckbox.checked = true;
        }

        form.addEventListener('submit', () => {
            if (rememberCheckbox.checked) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    }

    // Social login buttons
    const googleBtn = document.querySelector('.btn.social.google');
    if (googleBtn) {
        googleBtn.addEventListener('click', () => {
            window.location.href = '/auth/google';
        });
    }
});
