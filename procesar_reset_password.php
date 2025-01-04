<?php
function show_alert($message) {
    echo "<script>alert('$message'); window.history.back();</script>";
    exit();
}

// Verificar si se recibió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar que las contraseñas coincidan
    if ($new_password !== $confirm_password) {
        show_alert("Las contraseñas no coinciden.");
    }

    // Validar longitud de la contraseña
    if (strlen($new_password) < 10) {
        show_alert("La contraseña debe tener al menos 10 caracteres.");
    }

    // Validar que la contraseña tenga mayúsculas, minúsculas y caracteres especiales
    if (!validar_contraseña($new_password)) {
        show_alert("La contraseña debe contener al menos una letra mayúscula, una letra minúscula y un carácter especial.");
    }

    // Datos de la base de datos
    $db_host = "localhost";
    $db_nombre = "login_bd";
    $db_usuario = "root";
    $db_contra = "SEB23NOV2023_";

    // Crear conexión a la base de datos
    $conexion = new mysqli($db_host, $db_usuario, $db_contra, $db_nombre);

    // Verificar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Buscar el token en la base de datos
    $stmt = $conexion->prepare("SELECT id, email FROM login_t WHERE reset_token = ? AND token_expiration > NOW()");
    if (!$stmt) {
        show_alert("Error en la preparación de la consulta: " . $conexion->error);
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        $email = $row['email'];

        // Obtener el nombre del usuario utilizando el correo electrónico
        $stmt = $conexion->prepare("SELECT nombre FROM usuarios WHERE email = ?");
        if (!$stmt) {
            show_alert("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nombreUsuario = $row['nombre'];
        } else {
            show_alert("No se encontró el usuario.");
        }

        // Encriptar la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $stmt = $conexion->prepare("UPDATE login_t SET contraseña = ?, reset_token = NULL, token_expiration = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('Tu contraseña ha sido restablecida exitosamente.'); window.location.href='login.html';</script>";
        } else {
            show_alert("Hubo un error al actualizar la contraseña. Intenta nuevamente.");
        }
    } else {
        show_alert("Token inválido o expirado.");
    }

    $stmt->close();
    $conexion->close();
} else {
    show_alert("Método de solicitud no válido.");
}

// Función para validar la contraseña
function validar_contraseña($password) {
    if (!preg_match('/[A-Z]/', $password)) {
        return false; // No contiene mayúsculas
    }
    if (!preg_match('/[a-z]/', $password)) {
        return false; // No contiene minúsculas
    }
    if (!preg_match('/[\W]/', $password)) {
        return false; // No contiene caracteres especiales
    }
    return true;
}
?>