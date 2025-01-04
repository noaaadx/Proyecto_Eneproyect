<?php
// Configuración de la conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contrasena = "SEB23NOV2023_"; // Sustituir por tu contraseña real
$baseDatos = "login_bd"; // Cambiar si tu base de datos tiene otro nombre

// Conexión a la base de datos
$conn = new mysqli($servidor, $usuario, $contrasena, $baseDatos);

// Verificar si la conexión fue exitosa

$conn = new mysqli("localhost", "root", "SEB23NOV2023_", "login_bd");

// Verificar que se recibió una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['pass']);
    $repass = trim($_POST['repass']);

    // Validación de contraseñas
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{10,}$/";
    if (!preg_match($pattern, $password)) {
        header("Location: /InterfazLogin/FuncionRegistro/registro.html?error=contrasena_no_valida");
        exit;
    }

    if ($password !== $repass) {
        header("Location: /InterfazLogin/FuncionRegistro/registro.html?error=contrasenas_no_coinciden");
        exit;
    }

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT * FROM login_t WHERE Usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) { 
        // Si el usuario ya existe, mostrar una alerta y redirigir a la página de registro 
        echo "<script>alert('Usuario ya existe.'); window.location.href = '/InterfazLogin/FuncionRegistro/registro.html';</script>"; 
        exit; 
    }
    
    // Hash de la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insertar los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO login_t (Nombre, Apellido, Correo, Cargo, Usuario, Password) VALUES (?, ?, ?, 'Usuario', ?, ?)");
    $stmt->bind_param("sssss", $nombre, $apellido, $correo, $usuario, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: /InterfazLogin/FuncionLogin/login.html?registro_exitoso");
        exit;
    } else {
        header("Location: /InterfazLogin/FuncionRegistro/registro.html?error=error_insercion");
        exit;
    }
} else {
    header("Location: /InterfazLogin/FuncionRegistro/registro.html?error=metodo_no_permitido");
    exit;
}

// Cerrar la conexión
$conn->close();
?>


