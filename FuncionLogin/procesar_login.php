<?php
// Iniciar sesión
session_start();

// Verificar si se han enviado datos por el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Verificar si los datos del formulario fueron recibidos correctamente
    if (empty($usuario) || empty($password)) {
        die("Todos los campos son obligatorios.");
    }

    $db_host = "";
    $db_nombre = ""; // Asegúrate de que el nombre de la base de datos sea correcto
    $db_usuario = "";
    $db_contra = ""; // Contraseña de tu base de datos

    // Crear una conexión con MySQLi
    $conexion = new mysqli($db_host, $db_usuario, $db_contra, $db_nombre);

    // Verificar si hubo un error en la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Establecer el conjunto de caracteres a UTF-8 para evitar problemas con caracteres especiales
    $conexion->set_charset("utf8");

    // Prevenir inyecciones SQL (usando una consulta preparada)
    $stmt = $conexion->prepare("SELECT id, nombre, apellido, correo, usuario, password FROM login_t WHERE usuario = ?");
    if (!$stmt) {
        echo "<script>alert('Error en la preparación de la consulta: " . $conexion->error . "'); 
        window.history.back();</script>";
        exit();
    }
    // Vincular el parámetro de la consulta
    $stmt->bind_param("s", $usuario); // "s" indica que el parámetro es un string

    // Ejecutar la consulta
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Verificar si la consulta devolvió resultados
    if ($result->num_rows > 0) {
        // Obtener los datos del usuario
        $row = $result->fetch_assoc();

        // Mostrar los datos del usuario encontrado
        /* echo "<h3>Datos del usuario:</h3>";
        echo "<p><strong>ID:</strong> " . $row['id'] . "</p>";
        echo "<p><strong>Nombre:</strong> " . $row['nombre'] . "</p>";
        echo "<p><strong>Apellido:</strong> " . $row['apellido'] . "</p>";
        echo "<p><strong>Correo:</strong> " . $row['correo'] . "</p>";
        echo "<p><strong>Usuario:</strong> " . $row['usuario'] . "</p>"; 
        */
        
        // Verificar la contraseña usando password_verify (si usas bcrypt para las contraseñas)
        if (password_verify($password, $row['password'])) {
            // El usuario y la contraseña son correctos, iniciar sesión
            $_SESSION['usuario_id'] = $row['id']; // Almacenar el ID del usuario en sesión
            $_SESSION['usuario'] = $row['usuario']; // Almacenar el nombre de usuario en sesión

            // Mostrar una alerta de inicio de sesión exitoso y redirigir al home
            echo "<script>
                    alert('Inicio de sesión exitoso');
                    window.location.href = '/InterfazLogin/home.html';
                  </script>";
            exit();
        } else {
            // Contraseña incorrecta
            echo "<script>alert('Contraseña incorrecta. Inténtalo de nuevo.'); window.history.back();</script>";
        }
    } else {
        // El usuario no existe
        echo "<script>alert('Usuario no encontrado. Inténtalo de nuevo.'); window.history.back();</script>";
    }

    // Cerrar la conexión
    $conexion->close();
}
?>
