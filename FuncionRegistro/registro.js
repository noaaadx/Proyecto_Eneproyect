$(document).ready(function () {
    console.log("JS cargado correctamente.");
    // Mensajes de error
    // Verificar si hay un parámetro de error en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    //
    if (error === 'usuario_existente') {
        Swal.fire({
            icon: 'warning',
            title: 'Usuario existente',
            text: 'El nombre de usuario ya está en uso. Por favor, elige otro.',
            confirmButtonText: 'Entendido'
        }).then(() => {  // Aquí va el then() correctamente
            window.location.href = 'registro.html';
        });
    }    
    //

    // Función para mostrar/ocultar contraseña
    function togglePassword(inputId, iconId) {
        console.log("Ejecutando togglePassword para:", inputId);
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }

    // Eventos para mostrar/ocultar contraseña
    $("#togglePass").click(function() {
        togglePassword("password", "togglePass");
    });

    $("#toggleRePass").click(function() {
        togglePassword("confirmPassword", "toggleRePass");
    });

    console.log("Eventos de click añadidos a los iconos.");

    // Evento para limpiar el formulario
    $('#btn_eliminar').click(function(){
        Swal.fire({
            title: '¿Está seguro de limpiar el formulario?',
            text: "Se perderán todos los datos ingresados.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, limpiar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("registroForm").reset();
                $(".error-message").text(""); // Limpiar mensajes de error
                $(".password-container").removeClass("error-active"); // Remover estilos de error
                Swal.fire('Limpio', 'El formulario ha sido limpiado.', 'success');
            }
        });
    });

    // Función para mostrar errores y evitar mover el icono
    function mostrarError(inputId, mensaje) {
        let input = $("#" + inputId);
        let errorSpan = input.siblings(".error-message");
        errorSpan.text(mensaje);
        input.parent().addClass("error-active");
    }

    // Validación del formulario y mensaje de éxito
    $("#registroForm").submit(function(event) {
        console.log("Formulario enviado"); 
        event.preventDefault(); // Evita el envío inmediato del formulario

        const password = $("#password").val();
        const confirmPassword = $("#confirmPassword").val();

        let errorMessage = "";
        let passwordError = $("#passwordError");
        let confirmPasswordError = $("#confirmPasswordError");

        // Limpiar mensajes previos
        $(".error-message").text("").hide();

        // Expresión regular para validar la contraseña
        if (password.length < 10) {
            errorMessage = "La contraseña debe tener al menos 10 caracteres.";
        } else if (!/[A-Z]/.test(password) || !/[a-z]/.test(password)) {
            errorMessage = "La contraseña debe incluir al menos una mayúscula y una minúscula.";
        } else if (!/\d/.test(password)) {
            errorMessage = "La contraseña debe incluir al menos un número.";
        } else if (!/[@$!%*?&]/.test(password)) {
            errorMessage = "La contraseña debe incluir al menos un carácter especial (@$!%*?&).";
        }

        if (errorMessage) {
            passwordError.text(errorMessage).show();
            return;
        }

        if (password !== confirmPassword) {
            confirmPasswordError.text("Las contraseñas no coinciden.").show();
            return;
        }

        // Si pasa todas las validaciones, muestra el mensaje de éxito
        Swal.fire({
            title: '¡Registro exitoso!',
            text: 'Tu cuenta ha sido creada correctamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            console.log("Enviando formulario...")
            $("#registroForm")[0].submit();
        });
    });    
});
