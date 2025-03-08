<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/vendor/autoload.php';
require '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email'])) {
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensaje'] = "Correo inválido.";
        $_SESSION['tipo'] = "error";
        header("Location: mensaje.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, usuario FROM wp_employees WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("UPDATE wp_employees SET reset_token = ?, token_expiration = DATE_ADD(NOW(), INTERVAL 7 HOUR) WHERE id = ?");
        $stmt->bind_param("si", $token, $row['id']);
        $stmt->execute();

        $reset_link = "https://eneproyect.com/InterfazLogin/FuncionLogin/ValidaYDirec.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = '';
            $mail->SMTPAuth = true;
            $mail->Username = 'correo_emisor';
            $mail->Password = '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('correo emisor', 'fake nombre');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Restablece tu contraseña';
            $mail->Body = "Hola {$row['usuario']},<br><br>Haz clic aquí para restablecer tu contraseña: <a href='$reset_link'>$reset_link</a>";
            $mail->AltBody = "Hola {$row['usuario']},\n\nRestablece tu contraseña en: $reset_link";

            $mail->send();
            $_SESSION['mensaje'] = "Correo enviado con éxito.";
            $_SESSION['tipo'] = "success";
            header("Location: mensaje.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al enviar el correo.";
            $_SESSION['tipo'] = "error";
            header("Location: mensaje.php");
            exit();
        }
    } else {
        $_SESSION['mensaje'] = "Correo no registrado.";
        $_SESSION['tipo'] = "warning";
        header("Location: mensaje.php");
        exit();
    }
}
$conn->close();
?>

