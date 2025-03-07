<?php

// Configuración de la conexión a la base de datos

    $servidor = "";
    $usuario = "";
    $contrasena = ""; // Asegúrate de que esta es la contraseña correcta
    $baseDatos = "";

//require '../conexion.php';   // Si está en una carpeta llamada "config"

// Conectar a la base de datos
$conn = new mysqli($servidor, $usuario, $contrasena, $baseDatos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //echo "<pre>";
    //print_r($_POST);
    //  echo "</pre>"; 

    // Recibir datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['pass']);
    $repass = trim($_POST['repass']);
    $cargo = "Usuario"; // Valor por defecto para el cargo

    // Validación de contraseña con regex
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{10,}$/";
    if (!preg_match($pattern, $password)) {
        echo "Error: La contraseña no cumple con los requisitos.";
        header("Location: /InterfazLogin/FuncionRegistro/registro.html?error=contrasena_no_valida");
        exit;
    }

    if ($password !== $repass) {
        echo "Error: Las contraseñas no coinciden.";
        header("Location: /InterfazLogin/FuncionRegistro/registro.html?error=contrasenas_no_coinciden");
        exit;
    }

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT Id FROM wp_employees WHERE Usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "Error: El nombre de usuario ya está en uso.";
        exit;
    }
    

    // Hashear la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Preparar la consulta de inserción
    $stmt = $conn->prepare("INSERT INTO wp_employees (Nombre, Apellido, Correo, Cargo, Usuario, Password, reset_token, token_expiration) VALUES (?, ?, ?, ?, ?, ?, NULL, NULL)");
    $stmt->bind_param("ssssss", $nombre, $apellido, $correo, $cargo, $usuario, $hashedPassword);
    
    // Ejecutar la consulta y verificar si se insertó correctamente
    if ($stmt->execute()) {
        echo "Registro exitoso.";
        header("Location: /InterfazLogin/FuncionLogin/login.html?registro_exitoso");
        exit;
    } else {
        echo "Error al insertar en la base de datos: " . $stmt->error;
        die("Error al insertar en la base de datos: " . $stmt->error);
    }
} else {
    echo "Error: Método no permitido.";
    header("Location: /InterfazLogin/FuncionRegistro/registro.html?error=metodo_no_permitido");
    exit;
}

// Cerrar conexión
$conn->close();
?>


