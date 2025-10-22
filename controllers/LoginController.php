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
                $token = bin2hex(random_bytes(32));
                
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['reset_token'][$email] = $token;
                $_SESSION['reset_token_expiry'][$email] = time() + 3600;
                
                error_log("=== PRUEBA PHPMailer SIMPLIFICADA ===");
    
                $Asunto = 'Recuperación de Contraseña - AgroCaja Chica';
                $resetLink = "https://caja-chica.agrocentro.site/index.php?controller=login&action=resetConfirm&token={$token}&email=" . urlencode($email);
                $Mensaje = "Hola<br><br>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:<br><a href='{$resetLink}'>Restablecer Contraseña</a><br><br>Este enlace es válido por 1 hora.<br><br>Si no solicitaste esto, ignora este email.";
    
                try {
                    error_log("1. Creando PHPMailer...");
                    $mail = new PHPMailer(true);
                    error_log("✓ PHPMailer creado");
    
                    // CONFIGURACIÓN MÍNIMA
                    $mail->isSMTP();
                    $mail->Host = 'smtp.office365.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'angel.deleon@agrocentro.com';
                    $mail->Password = 'byvdynlmzjlpvncv';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    
                    error_log("✓ Configuración básica aplicada");
    
                    $mail->setFrom('angel.deleon@agrocentro.com', 'AgroCaja Chica');
                    $mail->addAddress($email);
                    
                    error_log("✓ From y To configurados");
    
                    $mail->Subject = $Asunto;
                    $mail->Body = $Mensaje;
                    $mail->isHTML(true);
                    
                    error_log("✓ Contenido configurado");
                    error_log("2. Intentando enviar...");
    
                    if ($mail->send()) {
                        error_log("*** ✓ EMAIL ENVIADO ***");
                        header('Location: index.php?controller=login&action=resetPassword&success=1');
                    } else {
                        error_log("*** ✗ send() = false ***");
                        throw new Exception('Send returned false');
                    }
                    
                } catch (Exception $e) {
                    error_log("*** ✗ ERROR PHPMailer ***");
                    error_log("Exception: " . $e->getMessage());
                    if (isset($mail)) {
                        error_log("ErrorInfo: " . $mail->ErrorInfo);
                    }
                    
                    // FALLBACK: Usar la función mail() nativa de PHP como respaldo
                    error_log("3. Intentando con mail() nativo...");
                    $headers = "From: angel.deleon@agrocentro.com\r\n";
                    $headers .= "Reply-To: no-reply@agrocentro.site\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    
                    if (mail($email, $Asunto, $Mensaje, $headers)) {
                        error_log("*** ✓ EMAIL ENVIADO CON mail() NATIVO ***");
                        header('Location: index.php?controller=login&action=resetPassword&success=1');
                    } else {
                        error_log("*** ✗ mail() también falló ***");
                        header('Location: index.php?controller=login&action=resetPassword&error=Error al enviar el email. Por favor intente más tarde.');
                    }
                }
    
            } else {
                error_log("Email no encontrado: $email");
                header('Location: index.php?controller=login&action=resetPassword&error=Email no encontrado');
            }
            exit;
        }
    }

    public function resetConfirm() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';
    
        // Validación básica
        if (!$token || !$email) {
            header('Location: index.php?controller=login&action=resetPassword&error=Token o email inválido');
            exit;
        }
    
        if (!isset($_SESSION['reset_token'][$email]) || $_SESSION['reset_token'][$email] !== $token) {
            header('Location: index.php?controller=login&action=resetPassword&error=Token inválido o expirado');
            exit;
        }
    
        // Validación de expiración (ahora funciona correctamente)
        $expiryTime = $_SESSION['reset_token_expiry'][$email] ?? 0;
        if (time() > $expiryTime) {
            header('Location: index.php?controller=login&action=resetPassword&error=El enlace ha expirado. Por favor solicita uno nuevo.');
            exit;
        }
    
        // Resto del código para el formulario...
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'] ?? '';
            $newPassword = trim($newPassword);
    
            if (strlen($newPassword) < 6) {
                header('Location: index.php?controller=login&action=resetConfirm&token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=La contraseña debe tener al menos 6 caracteres');
                exit;
            }
    
            $user = $this->usuario->getUsuarioByEmail($email);
            if ($user) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $result = $this->usuario->updateUsuario($user['id'], $user['nombre'], $email, $hashedPassword, $user['id_rol']);
                
                if ($result) {
                    // Limpiar tokens
                    unset($_SESSION['reset_token'][$email]);
                    unset($_SESSION['reset_token_expiry'][$email]);
                    
                    header('Location: index.php?controller=login&action=login&success=Contraseña restablecida con éxito');
                } else {
                    header('Location: index.php?controller=login&action=resetPassword&error=Error al actualizar la contraseña');
                }
            } else {
                header('Location: index.php?controller=login&action=resetPassword&error=Usuario no encontrado');
            }
            exit;
        }
    
        require '../views/login/reset_confirm.html';
    }
}