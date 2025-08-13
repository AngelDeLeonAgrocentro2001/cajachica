<?php
require_once '../models/Usuario.php';
require_once '../models/Login.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class LoginController {
    private $usuario;
    private $login;

    public function __construct() {
        $this->usuario = new Usuario();
        $this->login = new Login();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Email y contraseña son obligatorios']);
                exit;
            }

            $user = $this->login->authenticate($email, $password);
            if ($user) {
                // Inicio de sesión exitoso
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['rol'] = $user['rol'];

                $redirectUrl = '';
                switch ($user['rol']) {
                    case 'ADMIN':
                        $redirectUrl = 'index.php?controller=dashboard&action=index';
                        break;
                    case 'ENCARGADO_CAJA_CHICA':
                        $redirectUrl = 'index.php?controller=dashboard&action=index';
                        break;
                    case 'SUPERVISOR_AUTORIZADOR':
                        $redirectUrl = 'index.php?controller=dashboard&action=index';
                        break;
                    case 'CONTABILIDAD':
                        $redirectUrl = 'index.php?controller=dashboard&action=index';
                        break;
                    default:
                        $redirectUrl = 'index.php?controller=dashboard&action=index';
                        break;
                }

                header('Content-Type: application/json');
                echo json_encode(['message' => 'Inicio de sesión exitoso', 'redirect' => $redirectUrl]);
            } else {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Email o contraseña incorrectos']);
            }
            exit;
        } else {
            require '../views/login/index.html';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php?controller=login&action=login');
        exit;
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require '../views/login/reset.html';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            if (!$email) {
                header('Location: index.php?controller=login&action=resetPassword&error=Email inválido');
                exit;
            }

            $user = $this->usuario->getUsuarioByEmail($email);
            if ($user) {
                $token = bin2hex(random_bytes(32)); // Generar token seguro
                $_SESSION['reset_token'][$email] = $token;
                $_SESSION['reset_token_expiry'][$email] = time() + 3600; // Válido por 1 hora

                // Enviar email con PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Configuración del servidor SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.outlook.com';
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8'; // Añadir codificación UTF-8

                    // Credenciales (ajusta con tus datos reales)
                    $mail->Username = $email; // Usar el email del formulario
                    $mail->Password = 'dhdktzzvklxjxxgk'; // Nota: La contraseña debe ser la del email proporcionado

                    // Remitente y destinatario
                    $mail->setFrom($email, 'AgroCaja Chica');
                    $mail->addAddress($email);
                    $mail->addReplyTo($email);

                    // Contenido del email
                    $resetLink = "http://localhost:8080/agrocaja-chica/public/index.php?controller=login&action=resetConfirm&token={$token}&email=" . urlencode($email);
                    $mail->IsHTML(true);
                    $mail->Subject = 'Recuperación de Contraseña - AgroCaja Chica';
                    $mail->Body = "Hola<br><br>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:<br><a href='{$resetLink}'>Restablecer Contraseña</a><br><br>Este enlace es válido por 1 hora.<br><br>Si no solicitaste esto, ignora este email.";
                    $mail->AltBody = "Hola,\n\nRecibimos una solicitud para restablecer tu contraseña. Copia y pega este enlace en tu navegador para continuar:\n{$resetLink}\n\nEste enlace es válido por 1 hora.\n\nSi no solicitaste esto, ignora este email.";

                    $mail->send();
                    header('Location: index.php?controller=login&action=resetPassword&success=1');
                } catch (Exception $e) {
                    // Fallback: Si el email del usuario no puede ser usado como remitente, usar un email por defecto
                    try {
                        $mail->Username = 'angel.deleon@agrocentro.com'; // Email por defecto
                        $mail->Password = 'dhdktzzvklxjxxgk';
                        $mail->setFrom('angel.deleon@agrocentro.com', 'AgroCaja Chica');
                        $mail->addReplyTo('angel.deleon@agrocentro.com');
                        $mail->send();
                        header('Location: index.php?controller=login&action=resetPassword&success=1');
                    } catch (Exception $e2) {
                        header('Location: index.php?controller=login&action=resetPassword&error=Error al enviar el email: ' . htmlspecialchars($mail->ErrorInfo));
                    }
                }
            } else {
                header('Location: index.php?controller=login&action=resetPassword&error=Email no encontrado');
            }
            exit;
        }
    }

    public function resetConfirm() {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';
    
        // Validar token y expiración
        if (!$token || !$email || !isset($_SESSION['reset_token'][$email]) || $_SESSION['reset_token'][$email] !== $token || time() > $_SESSION['reset_token_expiry'][$email]) {
            header('Location: index.php?controller=login&action=resetPassword&error=Ingresa Nuevamente tu correo como protocolo de verificacion');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'] ?? '';
            // Sanitizar la contraseña para evitar espacios o caracteres no deseados
            $newPassword = trim($newPassword);
            error_log("Nueva contraseña recibida: $newPassword");
    
            if (strlen($newPassword) < 6) {
                header('Location: index.php?controller=login&action=resetConfirm&token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=La contraseña debe tener al menos 6 caracteres');
                exit;
            }
    
            $user = $this->usuario->getUsuarioByEmail($email);
            if ($user) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                error_log("Hash generado en resetConfirm: $hashedPassword");
                $result = $this->usuario->updateUsuario($user['id'], $user['nombre'], $email, $newPassword, $user['id_rol']);
                if ($result) {
                    error_log("Contraseña actualizada correctamente para $email");
                    unset($_SESSION['reset_token'][$email]);
                    unset($_SESSION['reset_token_expiry'][$email]);
                    session_destroy();
                    session_start();
                    header('Location: index.php?controller=login&action=login&success=Contraseña restablecida con éxito');
                } else {
                    error_log("Error al actualizar la contraseña para $email");
                    header('Location: index.php?controller=login&action=resetPassword&error=Error al actualizar la contraseña');
                }
            } else {
                error_log("Usuario no encontrado para email: $email");
                header('Location: index.php?controller=login&action=resetPassword&error=Usuario no encontrado');
            }
            exit;
        }
    
        require '../views/login/reset_confirm.html';
    }
}