//Animacion para cambiar entre inicio de sesion y crear cuenta

const signIn = document.getElementById('sign-in')
const signUp = document.getElementById('sign-up')
const form = document.getElementById('form')

signIn.addEventListener('click',()=>{
    form.classList.remove('toggle')
})
signUp.addEventListener('click',()=>{
    form.classList.add('toggle')
})

document.addEventListener("DOMContentLoaded", () => {
    
    // --- Mostrar los errores en los input
    function showInputError(inputElement, message) {
        clearInputError(inputElement);
        
        inputElement.style.border = "1px solid #cc0c39"; 
        inputElement.style.boxShadow = "0 0 0 1px #cc0c39 inset";
        
        const errorEl = document.createElement('div');
        errorEl.className = 'error-message';
        errorEl.style.color = '#c40000';
        errorEl.style.fontSize = '12px';
        errorEl.style.marginTop = '2px';
        errorEl.style.marginBottom = '8px';
        errorEl.style.textAlign = 'left';
        errorEl.innerHTML = message; 
        
        inputElement.parentNode.insertBefore(errorEl, inputElement.nextSibling);
    }

    function clearInputError(inputElement) {
        inputElement.style.border = "";
        inputElement.style.boxShadow = "";
        
        const nextEl = inputElement.nextSibling;
        if (nextEl && nextEl.classList && nextEl.classList.contains('error-message')) {
            nextEl.remove();
        }
    }
    
    function clearAllErrors(formElement) {
        const errors = formElement.querySelectorAll('.error-message');
        errors.forEach(err => err.remove());
        const inputs = formElement.querySelectorAll('input');
        inputs.forEach(input => clearInputError(input));
    }

    // Validacion del login
    const formLogin = document.querySelector('.login');
    if (formLogin) {
        formLogin.addEventListener('submit', (event) => {
            let hasError = false; 
            clearAllErrors(formLogin); 
            
            const inputTelefono = formLogin.querySelector('#login_telefono');
            const inputPassword = formLogin.querySelector('#login_password');
            const regexTelefono = /^\d{10}$/;

            if (!regexTelefono.test(inputTelefono.value)) {
                showInputError(inputTelefono, 'Introduce un número válido de 10 dígitos');
                hasError = true;
            }
            
            if (inputPassword.value.trim() === '') {
                showInputError(inputPassword, 'Introduce tu contraseña');
                hasError = true;
            }
            
            if (hasError) event.preventDefault(); 
        });
    }

    // Validaciones del formulario de registro
    const formRegistro = document.querySelector('.registro');
    if (formRegistro) {
        formRegistro.addEventListener('submit', (event) => {
            let hasError = false;
            clearAllErrors(formRegistro);
            
            const inputNombre = formRegistro.querySelector('#reg_nombre');
            const inputApellido = formRegistro.querySelector('#reg_apellido');
            const inputTelefono = formRegistro.querySelector('#reg_telefono');
            const inputPassword = formRegistro.querySelector('#reg_password');

            const contieneNumeros = /[0-9]/;
            const regexTelefono = /^\d{10}$/;

            // Validar Nombre
            if (inputNombre.value.trim().length < 2) {
                showInputError(inputNombre, 'Introduce tu nombre');
                hasError = true;
            } else if (contieneNumeros.test(inputNombre.value)) {
                showInputError(inputNombre, 'El nombre no puede contener números');
                hasError = true;
            }
            
            // Validar Apellido
            if (inputApellido.value.trim().length < 2) {
                showInputError(inputApellido, 'Introduce tu apellido');
                hasError = true;
            } else if (contieneNumeros.test(inputApellido.value)) {
                showInputError(inputApellido, 'El apellido no puede contener números');
                hasError = true;
            }

            // Validar Teléfono
            if (!regexTelefono.test(inputTelefono.value)) {
                showInputError(inputTelefono, 'Introduce un número válido de 10 dígitos');
                hasError = true;
            }

            // Validar Contraseña
            if (inputPassword.value.trim().length < 8) {
                showInputError(inputPassword, 'Al menos 8 caracteres');
                hasError = true;
            }
            
            if (hasError) event.preventDefault();
        });
    }

    // --- 4. ATRAPAR LOS ERRORES QUE MANDA PHP POR LA URL ---
    const urlParams = new URLSearchParams(window.location.search);
    const errorPHP = urlParams.get('error');
    const mensajePHP = urlParams.get('mensaje');

    // Si PHP nos regresó porque el usuario no existe o la contraseña está mal:
    if (errorPHP === 'credenciales') {
        const inputPasswordLogin = document.querySelector('#login_password');
        if (inputPasswordLogin) {
            showInputError(inputPasswordLogin, 'Teléfono o contraseña incorrectos');
        }
    }
    
    // Si PHP regresa al usuario porque la base de datos rechazó el registro
    if (errorPHP === 'registro') {
        const inputTelefonoReg = document.querySelector('#reg_telefono');
        if (inputTelefonoReg) {
            showInputError(inputTelefonoReg, 'Este teléfono ya está registrado o los datos son inválidos');
        }
    }
});
