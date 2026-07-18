const signIn = document.getElementById('sign-in')
const signUp = document.getElementById('sign-up')
const form = document.getElementById('form')

if (signIn && signUp && form) {
    signIn.addEventListener('click', () => {
        form.classList.remove('toggle');
    });
    
    signUp.addEventListener('click', () => {
        form.classList.add('toggle');
    });
}

//validacion 

document.addEventListener('DOMContentLoaded', function() {
    // --- Credenciales predefinidas (simulación) ---
    const VALID_EMAIL = 'admin@mail.com';
    const VALID_PASSWORD = '123456';

    // --- Elementos del DOM ---
    const loginForm = document.getElementById('login');
    const registerForm = document.getElementById('registro');
    const signUpLink = document.getElementById('sign-up');
    const signInLink = document.getElementById('sign-in');

    // función para mostrar mensajes de error
    function showError(message) {
        let errorEl = document.querySelector('.error-message');
        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'error-message';
            errorEl.style.color = 'red';
            errorEl.style.marginTop = '10px';
            errorEl.style.fontSize = '14px';
            const buttons = loginForm.querySelector('.buttons');
            buttons.parentNode.insertBefore(errorEl, buttons.nextSibling);
        }
        errorEl.textContent = message;
    }

    function clearError() {
        const errorEl = document.querySelector('.error-message');
        if (errorEl) errorEl.remove();
    }

    loginForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Evita el envío tradicional

        // Obtener valores
        const emailInput = document.getElementById('login-email');
        const passwordInput = document.getElementById('login-password');
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (email === '' || password === '') {
            showError('Por favor, completa todos los campos.');
            return;
        }

        if (email === VALID_EMAIL && password === VALID_PASSWORD) {
            clearError();
            window.location.href = 'index.html';
        } else {
            showError('Correo o contraseña incorrectos. Inténtalo de nuevo.');
            passwordInput.focus();
        }
    });

});
