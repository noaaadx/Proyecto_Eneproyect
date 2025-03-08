<?php
session_start();
require '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['pass'];

    if (empty($usuario) || empty($password)) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        $_SESSION['tipo'] = "error";
        header("Location: mensaje.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, nombre, apellido, correo, usuario, password FROM wp_employees WHERE usuario = ?");
    
    if (!$stmt) {
        $_SESSION['mensaje'] = "Error en la base de datos.";
        $_SESSION['tipo'] = "error";
        header("Location: mensaje.php");
        exit();
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario'] = $row['usuario'];

            $_SESSION['mensaje'] = "Inicio de sesión exitoso. Redirigiendo...";
            $_SESSION['tipo'] = "success";
            $_SESSION['redirect'] = "https://eneproyect.com/intranet-eneproyect/";
            header("Location: mensaje.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "Contraseña incorrecta. Inténtalo de nuevo.";
            $_SESSION['tipo'] = "error";
            header("Location: mensaje.php");
            exit();
        }
    } else {
        $_SESSION['mensaje'] = "Usuario no encontrado. Inténtalo de nuevo.";
        $_SESSION['tipo'] = "error";
        header("Location: mensaje.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
