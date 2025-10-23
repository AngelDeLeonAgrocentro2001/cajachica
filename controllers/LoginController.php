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
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['rol'] = $user['rol'];

                $redirectUrl = 'index.php?controller=dashboard&action=index';

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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: index.php?controller=login&action=login');
        exit;
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require '../views/login/reset.html';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
        
                $Asunto = 'Recuperación de Contraseña - AgroCaja Chica';
                $resetLink = "https://caja-chica.agrocentro.site/index.php?controller=login&action=resetConfirm&token={$token}&email=" . urlencode($email);
                
                $Mensaje = "
                    <html>
                    <head>
                        <title>Recuperación de Contraseña</title>
                    </head>
                    <body>
                        <h2>Recuperación de Contraseña</h2>
                        <p>Hola {$user['nombre']},</p>
                        <p>Recibimos una solicitud para restablecer tu contraseña.</p>
                        <p>Haz clic en el siguiente enlace para continuar:</p>
                        <p><a href='{$resetLink}' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a></p>
                        <p>O copia y pega este enlace en tu navegador:<br>{$resetLink}</p>
                        <p><strong>Este enlace es válido por 1 hora.</strong></p>
                        <p>Si no solicitaste esto, ignora este email.</p>
                    </body>
                    </html>
                ";
                
                $MensajeAlterno = "Hola {$user['nombre']},\n\nRecibimos una solicitud para restablecer tu contraseña. Copia y pega este enlace en tu navegador:\n{$resetLink}\n\nEste enlace es válido por 1 hora.\n\nSi no solicitaste esto, ignora este email.";

                // Usar EXACTAMENTE la configuración especificada
                if ($this->sendWithExactConfig($email, $user['nombre'], $Asunto, $Mensaje, $MensajeAlterno)) {
                    header('Location: index.php?controller=login&action=resetPassword&success=1');
                } else {
                    // Fallback a función mail nativa
                    if ($this->sendWithNativeMail($email, $Asunto, $MensajeAlterno)) {
                        header('Location: index.php?controller=login&action=resetPassword&success=1');
                    } else {
                        header('Location: index.php?controller=login&action=resetPassword&error=No se pudo enviar el email. Por favor contacte al administrador.');
                    }
                }
    
            } else {
                error_log("Email no encontrado: $email");
                // Por seguridad, mostrar mismo mensaje aunque el email no exista
                header('Location: index.php?controller=login&action=resetPassword&success=1');
            }
            exit;
        }
    }
    
    private function sendWithExactConfig($email, $nombre, $subject, $htmlBody, $textBody) {
        try {
            // CONFIGURACIÓN EXACTA como la especificaste
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'live.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->Username = 'smtp@mailtrap.io';
            $mail->Password = '5c69539451340b69f51743ebd47893bb';
            
            // Configuración adicional necesaria
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 15;
            $mail->SMTPDebug = 2; // Para ver detalles de la conexión
            $mail->Debugoutput = 'error_log';
            
            // Opciones para problemas de conexión
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Configuración del remitente
            $mail->setFrom('no-reply@agrocentro.site', 'AgroCaja Chica');
            $mail->addReplyTo('no-reply@agrocentro.site', 'AgroCaja Chica');
            $mail->addAddress($email, $nombre);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;

            error_log("🔧 Intentando conectar con Mailtrap usando configuración exacta...");
            
            if ($mail->send()) {
                error_log("✅ Email enviado exitosamente via Mailtrap a: $email");
                return true;
            } else {
                error_log("❌ Mailtrap send() retornó false");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("❌ Error Mailtrap para $email: " . $e->getMessage());
            if (isset($mail)) {
                error_log("❌ ErrorInfo: " . $mail->ErrorInfo);
            }
            return false;
        }
    }

    private function sendWithNativeMail($email, $subject, $message) {
        try {
            $headers = "From: no-reply@agrocentro.site\r\n";
            $headers .= "Reply-To: no-reply@agrocentro.site\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($email, $subject, $message, $headers)) {
                error_log("✓ Email enviado via función mail() nativa a: $email");
                return true;
            } else {
                error_log("✗ Error enviando email via función mail() nativa a: $email");
                return false;
            }
        } catch (Exception $e) {
            error_log("✗ Excepción en función mail() nativa: " . $e->getMessage());
            return false;
        }
    }

    public function resetConfirm() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';
    
        // Validar token y expiración
        if (!$token || !$email || !isset($_SESSION['reset_token'][$email]) || $_SESSION['reset_token'][$email] !== $token || time() > $_SESSION['reset_token_expiry'][$email]) {
            header('Location: index.php?controller=login&action=resetPassword&error=El enlace de recuperación ha expirado o es inválido. Por favor solicita uno nuevo.');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            $newPassword = trim($newPassword);
            $confirmPassword = trim($confirmPassword);

            if (strlen($newPassword) < 6) {
                header('Location: index.php?controller=login&action=resetConfirm&token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=La contraseña debe tener al menos 6 caracteres');
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                header('Location: index.php?controller=login&action=resetConfirm&token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=Las contraseñas no coinciden');
                exit;
            }

            $user = $this->usuario->getUsuarioByEmail($email);
            if ($user) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $result = $this->usuario->updateUsuario($user['id'], $user['nombre'], $email, $hashedPassword, $user['id_rol']);
                
                if ($result) {
                    error_log("Contraseña actualizada correctamente para $email");
                    // Limpiar tokens
                    unset($_SESSION['reset_token'][$email]);
                    unset($_SESSION['reset_token_expiry'][$email]);
                    
                    header('Location: index.php?controller=login&action=login&success=Contraseña restablecida con éxito. Ahora puedes iniciar sesión.');
                } else {
                    error_log("Error al actualizar la contraseña para $email");
                    header('Location: index.php?controller=login&action=resetConfirm&token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=Error al actualizar la contraseña. Por favor intenta nuevamente.');
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