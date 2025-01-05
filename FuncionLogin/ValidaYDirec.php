<?php
    // Datos de la base de datos
    $db_host = "localhost";
    $db_nombre = "login_bd"; // Nombre de la base de datos
    $db_usuario = "root"; // Usuario de la base de datos
    $db_contra = "SEB23NOV2023_"; // Contraseña de la base de datos

    // Crear la conexión a la base de datos
    $conexion = new mysqli($db_host, $db_usuario, $db_contra, $db_nombre);

    // Verificar si hubo un error en la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $token = $_GET['token'];

    //verificar si el token existe en la base de datos y no ha expirado
    $stmt = $conexion->prepare("SELECT id FROM login_t WHERE reset_token = ? AND token_expiration > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows() > 0) {
            // Token válido, redirigir a la interfaz de actualización de contraseña
            header("Location: /InterfazLogin/FuncionLogin/RestablecerContra/procesar_reset_password.html");
            exit();
    } else {
        // No se proporcionó token, redirigir a una página de error
        echo "Token invalido o expirado";
        exit();
    }
    $stmt -> close();
    $conexion  -> close();
?>