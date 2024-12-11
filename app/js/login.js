


//FUNCION PARA CAMBIAR DE FORM
function switchForm(formType) {
    const loginForm = document.getElementById('login-form-container');
    const registerForm = document.getElementById('register-form-container');
    
    if (formType === 'register') {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
    } else {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
    }
}

window.onload = function() {
    switchForm('login');
};

//acpetacion formulario login
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Evita el envío tradicional del formulario

    const formData = new FormData(this);  // Recoge los datos del formulario

    for (const [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    fetch('../../database/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);  // Imprime toda la respuesta en la consola del navegador

        if (data.success) {
            window.location.href = "./index.php"; 
        } else {
            // Si hubo un error, lo mostramos en el formulario
            document.getElementById('error-message').textContent = data.message;
        }
    })
    .catch(error => {
        document.getElementById('error-message').textContent = 'Hubo un problema con la solicitud.';
    });
});
/////form registrar
document.getElementById('register-form').addEventListener('submit', function(event) {
    event.preventDefault();  

    const formData = new FormData(this);  

    // Mostrar datos del formulario (opcional para depuración)
    for (const [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    // Enviar los datos al servidor usando fetch
    fetch('../../database/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);  // Imprime toda la respuesta en la consola del navegador

        if (data.success) {
            // Si el registro es exitoso, redirigir a la página principal
            window.location.href = "./index.php"; 
        } else {
            document.getElementById('error-message').textContent = data.message;

        }
    })
    .catch(error => {
        // Manejo de errores si hay un problema con la solicitud
        document.getElementById('error-message').textContent = error.message;
    });
});

function validatePassword() {
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm-password').value;
    const passwordMatchMessage = document.getElementById('password-match-message');
    const submitButton = document.getElementById('register-submit');

    // Verificar si las contraseñas coinciden
    if (password !== confirmPassword) {
        passwordMatchMessage.style.display = 'inline';  // Mostrar el mensaje de error
        submitButton.disabled = true;  // Deshabilitar el botón
    } else {
        passwordMatchMessage.style.display = 'none';  // Ocultar el mensaje de error
    }

    // Validar que todos los campos estén completos
    enableSubmitButton();
}

// Función para habilitar el botón de registro
function enableSubmitButton() {
    const name = document.getElementById('register-name').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm-password').value;
    const submitButton = document.getElementById('register-submit');

    // Verificar si todos los campos están completos y si las contraseñas coinciden
    if (name && email && password && confirmPassword && password === confirmPassword) {
        submitButton.disabled = false;  // Habilitar el botón si todos los campos están completos
    } else {
        submitButton.disabled = true;  // Deshabilitar el botón si falta algún campo o las contraseñas no coinciden
    }
}
