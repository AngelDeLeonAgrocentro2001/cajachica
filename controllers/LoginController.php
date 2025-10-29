<?php
require_once '../models/Usuario.php';
require_once '../models/Login.php';
require_once '../vendor/autoload.php';
require_once '../config/env.php';

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
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .button { background-color: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; }
                            .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2>Recuperación de Contraseña - AgroCaja Chica</h2>
                            <p>Hola <strong>{$user['nombre']}</strong>,</p>
                            <p>Recibimos una solicitud para restablecer tu contraseña en el sistema AgroCaja Chica.</p>
                            <p>Haz clic en el siguiente botón para continuar:</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='{$resetLink}' class='button'>Restablecer Contraseña</a>
                            </p>
                            <p>O copia y pega este enlace en tu navegador:</p>
                            <p style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; word-break: break-all;'>
                                <code>{$resetLink}</code>
                            </p>
                            <p><strong>⚠️ Importante: Este enlace expirará en 1 hora.</strong></p>
                            <p>Si no solicitaste este restablecimiento, ignora este email.</p>
                            <div class='footer'>
                                <p>Este es un mensaje automático, por favor no respondas.</p>
                                <p>AgroCaja Chica &copy; " . date('Y') . "</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";
                
                $MensajeAlterno = "RECUPERACIÓN DE CONTRASEÑA - AGROCAJA CHICA\n\n" .
                    "Hola {$user['nombre']},\n\n" .
                    "Recibimos una solicitud para restablecer tu contraseña.\n\n" .
                    "Para continuar, visita el siguiente enlace:\n" .
                    "{$resetLink}\n\n" .
                    "Este enlace expirará en 1 hora.\n\n" .
                    "Si no solicitaste esto, ignora este email.\n\n" .
                    "Saludos,\nSistema AgroCaja Chica";

                // ESTRATEGIA DE ENVÍO MEJORADA
                $enviado = false;
                
                // 1. Intentar con Mailtrap (tu configuración exacta)
                if (!$enviado) {
                    $enviado = $this->sendWithExactConfig($email, $user['nombre'], $Asunto, $Mensaje, $MensajeAlterno);
                }
                
                // 2. Intentar con función mail() mejorada
                if (!$enviado) {
                    $enviado = $this->sendWithNativeMail($email, $Asunto, $MensajeAlterno);
                }
                
                // 3. Intentar con Gmail como respaldo
                if (!$enviado) {
                    $enviado = $this->sendWithGmail($email, $user['nombre'], $Asunto, $Mensaje, $MensajeAlterno);
                }

                if ($enviado) {
                    header('Location: index.php?controller=login&action=resetPassword&success=1');
                } else {
                    header('Location: index.php?controller=login&action=resetPassword&error=No se pudo enviar el email. Por favor contacte al administrador.');
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
            $mail = new PHPMailer();
            $mail->isSMTP();

            $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            $mail->Debugoutput = function($str, $level) {
            error_log("DEBUG Mailtrap: $str");
            };
            
            // USAR getenv() EN VEZ DE $_ENV
            $mail->Host = getenv('MAILTRAP_HOST') ?: 'live.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = getenv('MAILTRAP_PORT') ?: 2525;
            $mail->Username = getenv('MAILTRAP_USERNAME') ?: 'smtp@mailtrap.io';
            $mail->Password = getenv('MAILTRAP_PASSWORD') ?: '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 10;
            $mail->SMTPDebug = 2;

            $mail->setFrom(
                getenv('MAIL_FROM_EMAIL') ?: 'no-reply@agrocentro.site', 
                getenv('MAIL_FROM_NAME') ?: 'AgroCaja Chica'
            );
            $mail->addReplyTo(
                getenv('MAIL_FROM_EMAIL') ?: 'no-reply@agrocentro.site', 
                getenv('MAIL_FROM_NAME') ?: 'AgroCaja Chica'
            );
            $mail->addAddress($email, $nombre);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;

            if ($mail->send()) {
                error_log("✅ Email enviado exitosamente via Mailtrap a: $email");
                return true;
            }
            return false;
            
        } catch (Exception $e) {
            error_log("❌ Error Mailtrap para $email: " . $e->getMessage());
            return false;
        }
    }


    private function sendWithNativeMail($email, $subject, $message) {
        try {
            // Headers mejorados para mejor entrega
            $headers = "From: AgroCaja Chica <no-reply@agrocentro.site>\r\n";
            $headers .= "Reply-To: no-reply@agrocentro.site\r\n";
            $headers .= "Return-Path: no-reply@agrocentro.site\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "X-Priority: 1\r\n";
            $headers .= "Importance: High\r\n";

            // Agregar headers para reducir spam
            $headers .= "List-Unsubscribe: <mailto:unsubscribe@agrocentro.site?subject=unsubscribe>\r\n";

            // El parámetro -f es importante para el Return-Path
            if (mail($email, $subject, $message, $headers, "-f no-reply@agrocentro.site")) {
                error_log("✅ Email enviado via función mail() nativa a: $email");
                return true;
            } else {
                error_log("❌ Error enviando email via función mail() nativa a: $email");
                return false;
            }
        } catch (Exception $e) {
            error_log("❌ Excepción en función mail() nativa: " . $e->getMessage());
            return false;
        }
    }

    private function sendWithGmail($email, $nombre, $subject, $htmlBody, $textBody) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();

            $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            $mail->Debugoutput = function($str, $level) {
            error_log("DEBUG Office365: $str");
            };
            
            // USAR getenv() EN VEZ DE $_ENV
            $mail->Host = getenv('OFFICE365_HOST') ?: 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Port = getenv('OFFICE365_PORT') ?: 587;
            $mail->Username = getenv('OFFICE365_USERNAME') ?: 'angel.deleon@agrocentro.com';
            $mail->Password = getenv('OFFICE365_PASSWORD') ?: '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 10;
            $mail->SMTPDebug = 2;

            $mail->setFrom(
                getenv('MAIL_FROM_EMAIL') ?: 'no-reply@agrocentro.site', 
                getenv('MAIL_FROM_NAME') ?: 'AgroCaja Chica'
            );
            $mail->addAddress($email, $nombre);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;
            $mail->isHTML(true);

            if ($mail->send()) {
                error_log("✅ Email enviado exitosamente via Office365 a: $email");
                return true;
            }
            return false;
            
        } catch (Exception $e) {
            error_log("❌ Error Office365 para $email: " . $e->getMessage());
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
                    error_log("✅ Contraseña actualizada correctamente para $email");
                    // Limpiar tokens
                    unset($_SESSION['reset_token'][$email]);
                    unset($_SESSION['reset_token_expiry'][$email]);
                    
                    header('Location: index.php?controller=login&action=login&success=Contraseña restablecida con éxito. Ahora puedes iniciar sesión.');
                } else {
                    error_log("❌ Error al actualizar la contraseña para $email");
                    header('Location: index.php?controller=login&action=resetConfirm&token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=Error al actualizar la contraseña. Por favor intenta nuevamente.');
                }
            } else {
                error_log("❌ Usuario no encontrado para email: $email");
                header('Location: index.php?controller=login&action=resetPassword&error=Usuario no encontrado');
            }
            exit;
        }
    
        require '../views/login/reset_confirm.html';
    }
}