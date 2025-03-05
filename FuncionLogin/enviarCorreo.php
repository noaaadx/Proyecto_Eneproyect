<?php
// Iniciar sesión
session_start();

// Incluir PHPMailer para el envío de correos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar el autoload de Composer
require __DIR__ . '/vendor/autoload.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el correo del formulario
    $email = $_POST['email'];

    // Validación básica del correo
    if (empty($email)) {
        echo "<script>alert('El correo es obligatorio.'); window.history.back();</script>";
        exit();
    }

    // Validar el formato del correo
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('El correo electrónico no es válido.'); window.history.back();</script>";
        exit();
    }

    // Datos de la base de datos
    $db_host = "";
    $db_nombre = ""; // Nombre de la base de datos
    $db_usuario = ""; // Usuario de la base de datos
    $db_contra = ""; // Contraseña de la base de datos

    // Crear la conexión a la base de datos
    $conexion = new mysqli($db_host, $db_usuario, $db_contra, $db_nombre);

    // Verificar si hubo un error en la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Prevenir inyecciones SQL con una consulta preparada para el correo del usuario
    $stmt = $conexion->prepare("SELECT id, usuario, correo FROM login_t WHERE correo = ?");
    $stmt->bind_param("s", $email); // Vinculamos el correo al parámetro
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encuentra el correo en la base de datos
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id']; // Renombrado a user_id

        // Generar un token único
        $token = bin2hex(random_bytes(50)); // Genera un token aleatorio de 100 caracteres hexadecimales

        // Insertar el token en la base de datos
        $stmt = $conexion->prepare("UPDATE login_t SET reset_token = ?, token_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
        $stmt->bind_param("si", $token, $user_id); // Vinculamos el token y el ID del usuario
        $stmt->execute();

        // Enviar el correo al usuario con el enlace de restablecimiento de contraseña
        $reset_link = "http://localhost:3000/InterfazLogin/FuncionLogin/ValidaYDirec.php?token=$token"; // Asegúrate de usar tu dominio o ruta correcta

        // Configurar el correo con PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configurar el servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'sebastiannoacjt@gmail.com'; // Cambia esto por tu correo
            $mail->Password = 'ickq exyj ocwd ygjw'; // Cambia esto por tu contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usa STARTTLS para mayor seguridad
            $mail->Port = 587; // Puerto SMTP para STARTTLS

            // Configurar el remitente y destinatario
            $mail->setFrom('sebastiannoacjt@gmail.com', 'Soporte');
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Restablece tu contraseña';
            $mail->Body    = "Haz clic en el siguiente enlace para restablecer tu contraseña: <a href='$reset_link'>$reset_link</a>";
            $mail->AltBody = 'Haz clic en el siguiente enlace para restablecer tu contraseña: ' . $reset_link;

            // Enviar el correo
            $mail->send();
            echo "<script>alert('Te hemos enviado un correo para restablecer tu contraseña.'); window.location.href = '/InterfazLogin/home.html';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Hubo un error al enviar el correo. Intenta nuevamente. Error: {$mail->ErrorInfo}'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('El correo no está registrado en nuestra base de datos.'); window.history.back();</script>";
    }

    // Cerrar la conexión
    $conexion->close();
}
?>
