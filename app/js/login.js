        function switchForm(formType) {
            var formTitle = document.getElementById("form-title");
            var loginForm = document.getElementById("login-form");
            var registerForm = document.getElementById("register-form");
            var switchText = document.getElementById("switch-text");

            if (formType === "register") {
                formTitle.textContent = "Registro";
                loginForm.style.display = "none";
                registerForm.style.display = "block";
                switchText.innerHTML = '¿Ya tienes cuenta? <a href="javascript:void(0)" onclick="switchForm(\'login\')">Inicia sesión aquí</a>';
            } else {
                formTitle.textContent = "Ingreso";
                loginForm.style.display = "block";
                registerForm.style.display = "none";
                switchText.innerHTML = '¿No tienes cuenta? <a href="javascript:void(0)" onclick="switchForm(\'register\')">Regístrate aquí</a>';
            }
        }

        function validatePassword() {
            const password = document.getElementById("register-password").value;
            const confirmPassword = document.getElementById("register-confirm-password").value;
            const message = document.getElementById("password-match-message");
            const submitButton = document.getElementById("register-submit");
        
            if (password === confirmPassword) {
                message.style.display = "none";
                submitButton.disabled = false;
            } else {
                message.style.display = "block";
                message.textContent = "Las contraseñas no coinciden";
                submitButton.disabled = true;
            }
        }
        


