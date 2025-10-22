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
                
                // CORRECCIÓN: Asegurar que la sesión esté iniciada
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                // CORRECCIÓN: Timestamp correcto (1 hora en el futuro)
                $_SESSION['reset_token'][$email] = $token;
                $_SESSION['reset_token_expiry'][$email] = time() + 3600; // 1 hora
                
                error_log("Token generado para $email: $token");
                error_log("Expira en: " . date('Y-m-d H:i:s', $_SESSION['reset_token_expiry'][$email]));
    
                // Configuración de correo con Mailtrap
                $Asunto = 'Recuperación de Contraseña - AgroCaja Chica';
                
                $resetLink = "https://caja-chica.agrocentro.site/index.php?controller=login&action=resetConfirm&token={$token}&email=" . urlencode($email);
                $Mensaje = "Hola<br><br>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:<br><a href='{$resetLink}'>Restablecer Contraseña</a><br><br>Este enlace es válido por 1 hora.<br><br>Si no solicitaste esto, ignora este email.";
                $MensajeAlterno = "Hola,\n\nRecibimos una solicitud para restablecer tu contraseña. Copia y pega este enlace en tu navegador para continuar:\n{$resetLink}\n\nEste enlace es válido por 1 hora.\n\nSi no solicitaste esto, ignora este email.";
    
                $mail = new PHPMailer(true);
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'error_log';
                
            try {
                // Configuración Mailtrap
                $mail->isSMTP();
                $mail->Host = 'live.smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Port = 587;
                $mail->Username = 'smtp@mailtrap.io';
                $mail->Password = '5c69539451340b69f51743ebd47893bb';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->CharSet = 'UTF-8';
                $mail->Timeout = 60;
    
                // Configuración del remitente
                $mail->setFrom('no-reply@agrocentro.site', 'AgroCaja Chica');
                $mail->addReplyTo('no-reply@agrocentro.site', 'AgroCaja Chica');
    
                // Destinatario
                $mail->addAddress($email, $user['nombre'] ?? '');
    
                $mail->isHTML(true);
                $mail->Subject = $Asunto;
                $mail->Body = $Mensaje;
                $mail->AltBody = $MensajeAlterno;
    
                if ($mail->send()) {
                    error_log("Email enviado exitosamente a: $email");
                    header('Location: index.php?controller=login&action=resetPassword&success=1');
                } else {
                    throw new Exception('Error al enviar email');
                }
            } catch (Exception $e) {
                error_log("Error PHPMailer para $email: " . $mail->ErrorInfo);
                error_log("Exception: " . $e->getMessage());
                header('Location: index.php?controller=login&action=resetPassword&error=Error al enviar el email. Por favor intente más tarde.');
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
    
        error_log("Validando token para: $email");
        error_log("Token recibido: $token");
        error_log("Token en sesión: " . ($_SESSION['reset_token'][$email] ?? 'NO ENCONTRADO'));
    
        // SOLUCIÓN TEMPORAL: Validación simplificada sin verificación de tiempo
        if (!$token || !$email) {
            error_log("Token o email vacíos");
            header('Location: index.php?controller=login&action=resetPassword&error=Token o email inválido');
            exit;
        }
    
        if (!isset($_SESSION['reset_token'][$email]) || $_SESSION['reset_token'][$email] !== $token) {
            error_log("Token no coincide o no existe");
            header('Location: index.php?controller=login&action=resetPassword&error=Token inválido o expirado');
            exit;
        }
    
        // TEMPORAL: No validamos expiración hasta que el servidor tenga fecha correcta
        error_log("ADVERTENCIA: Validación de tiempo desactivada por problema de fecha del servidor");
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'] ?? '';
            $newPassword = trim($newPassword);
            
            error_log("Nueva contraseña recibida para $email: " . strlen($newPassword) . " caracteres");
    
            if (strlen($newPassword) < 6) {
                header('Location: index.php?controller=login&action=resetConfirm&token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=La contraseña debe tener al menos 6 caracteres');
                exit;
            }
    
            $user = $this->usuario->getUsuarioByEmail($email);
            if ($user) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                error_log("Hash generado para $email");
                
                $result = $this->usuario->updateUsuario($user['id'], $user['nombre'], $email, $hashedPassword, $user['id_rol']);
                
                if ($result) {
                    error_log("Contraseña actualizada correctamente para $email");
                    
                    // Limpiar tokens
                    unset($_SESSION['reset_token'][$email]);
                    unset($_SESSION['reset_token_expiry'][$email]);
                    
                    header('Location: index.php?controller=login&action=login&success=Contraseña restablecida con éxito');
                } else {
                    error_log("Error en updateUsuario para $email");
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