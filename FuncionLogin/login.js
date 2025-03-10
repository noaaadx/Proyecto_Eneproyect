document.addEventListener("DOMContentLoaded", function () {
    console.log("JS cargado correctamente.");

    // Mostrar/Ocultar contraseña
    const passwordInput = document.getElementById("password");
    const toggleIcon = document.getElementById("togglePass");
    const loginButton = document.getElementById("loginButton");
    const loginForm = document.getElementById("loginForm");
    const usernameInput = document.getElementById("usuario");
    const resetButton = document.getElementById("resetButton");
    const emailInput = document.getElementById("email");

    if (passwordInput && toggleIcon) {
        toggleIcon.addEventListener("click", function () {
            console.log("Icono clickeado.");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
            toggleIcon.classList.toggle("fa-eye");
            toggleIcon.classList.toggle("fa-eye-slash");
        });
    } else {
        console.error("No se encontró el input de contraseña o el icono.");
    }

    // Validación antes de enviar el formulario de login
    if (loginButton && loginForm) {
        loginButton.addEventListener("click", function (event) {
            if (!usernameInput.value.trim() || !passwordInput.value.trim()) {
                event.preventDefault();
                Swal.fire({
                    icon: "error",
                    title: "Campos vacíos",
                    text: "Por favor, completa todos los campos antes de continuar.",
                });
            } else {
                loginForm.submit();
            }
        });
    } else {
        console.error("No se encontró el botón o formulario de login.");
    }

    // Validación para restablecer contraseña
    if (resetButton) {
        resetButton.addEventListener("click", function () {
            let emailValue = emailInput.value.trim();

            if (emailValue === "") {
                Swal.fire({
                    icon: "warning",
                    title: "Campo vacío",
                    text: "Por favor, ingresa tu correo electrónico.",
                    confirmButtonColor: "#3085d6"
                });
                return;
            }

            if (!validateEmail(emailValue)) {
                Swal.fire({
                    icon: "error",
                    title: "Correo inválido",
                    text: "Por favor, ingresa un correo electrónico válido.",
                    confirmButtonColor: "#d33"
                });
                return;
            }

            document.getElementById("resetForm").submit();
        });
    } else {
        console.error("No se encontró el botón de restablecer contraseña.");
    }
});

// Función para validar el formato del correo
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
