<?php
session_start();
require '../conexion.php';

if (!isset($_GET['token']) || empty($_GET['token'])) {
    $_SESSION['mensaje'] = "Token no proporcionado.";
    $_SESSION['tipo'] = "error";
    header("Location: ../mensaje.php");
    exit();
}

$token = $_GET['token'];
// Depuración: Verificar si se recibió el token
// echo "Token recibido: " . htmlspecialchars($token) . "<br>";

// Depuración: Verificar si el token se está recibiendo bien
error_log("Token recibido: " . $token);

// Verificar si el token existe en la base de datos y no ha expirado
$stmt = $conn->prepare("SELECT id, token_expiration FROM wp_employees WHERE reset_token = ? AND token_expiration > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $token_expiration);
    $stmt->fetch();

    // Depuración: Verificar fecha de expiración
    error_log("Token válido, expira en: " . $token_expiration);

    // Token válido, redirigir a la interfaz de actualización de contraseña
    header("Location: /InterfazLogin/FuncionLogin/RestablecerContra/procesar_reset_password.html?token=" . urlencode($token));
    exit();
} else {
    error_log("Token inválido o expirado.");
    $_SESSION['mensaje'] = "Token inválido o expirado.";
    $_SESSION['tipo'] = "error";
    header("Location: ../mensaje.php");
    exit();
}

$stmt->close();
$conn->close();
?>

