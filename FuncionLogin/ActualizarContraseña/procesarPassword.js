document.addEventListener("DOMContentLoaded", function () {
    const toggleIcons = document.querySelectorAll(".toggle-password");

    toggleIcons.forEach(icon => {
        icon.addEventListener("click", function () {
            togglePasswordVisibility(this);
        });
    });

    function togglePasswordVisibility(icon) {
        const fieldId = icon.getAttribute("data-target");
        const field = document.getElementById(fieldId);

        if (field.type === "password") {
            field.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            field.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }

    // Obtener el token de la URL
    
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token') || '';

    if (token) {
        // Obtener el nombre del usuario con AJAX
        fetch(`obtener_nombre.php?token=${token}`)
            .then(response => response.json())  // Convertir directamente a JSON
            .then(data => {
                console.log("Respuesta del servidor:", data); // Depuración

                if (data.Usuario) {
                    document.getElementById("nombreUsuario").textContent = data.Usuario;
                } else {
                    document.getElementById("nombreUsuario").textContent = "Usuario Desconocido";
                }
            })
            .catch(error => {
                console.error("Error al obtener el nombre del usuario:", error);
                document.getElementById("nombreUsuario").textContent = "Error al cargar usuario";
            });
    } else {
        console.error("No se encontró un token en la URL");
    }
});
