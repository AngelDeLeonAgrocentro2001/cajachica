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
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Email inválido']);
                exit;
            }
        
            $user = $this->usuario->getUsuarioByEmail($email);
            if ($user) {
                // Email válido y registrado
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => 'Email verificado correctamente',
                    'email' => $email
                ]);
            } else {
                // Email no registrado
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'error' => 'El email no está registrado en el sistema. Por favor, verifica tu dirección de correo.'
                ]);
            }
            exit;
        }
    }

    public function changePassword() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        
        error_log("🔧 changePassword llamado - Email: $email, Nueva contraseña: " . (strlen($newPassword) > 0 ? '***' : 'vacía'));
        
        if (empty($email) || empty($newPassword)) {
            error_log("❌ Error: Email o contraseña vacíos");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Email y contraseña son obligatorios']);
            exit;
        }

        if (strlen($newPassword) < 6) {
            error_log("❌ Error: Contraseña demasiado corta");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            exit;
        }

        $user = $this->usuario->getUsuarioByEmail($email);
        if ($user) {
            error_log("✅ Usuario encontrado: " . $user['id'] . " - " . $user['nombre']);
            error_log("🔧 Datos actuales - Código: '" . $user['clientes'] . "', Caja Chica: " . $user['id_caja_chica']);
            
            // PRESERVAR los datos existentes del usuario
            $nombre = $user['nombre'];
            $id_rol = $user['id_rol'];
            $card_code = $user['clientes']; // Preservar código existente
            $id_caja_chica = $user['id_caja_chica']; // Preservar caja chica existente
            
            // Actualizar usuario preservando todos los datos existentes
            $result = $this->usuario->updateUsuario(
                $user['id'], 
                $nombre, 
                $email, 
                $newPassword,  // ← Nueva contraseña
                $id_rol,
                $card_code,    // ← Preservar código
                $id_caja_chica // ← Preservar caja chica
            );
            
            error_log("🔧 Resultado de updateUsuario: " . ($result ? 'ÉXITO' : 'FALLO'));
            
            if ($result) {
                // Enviar correo de notificación
                $emailResult = $this->sendPasswordChangeNotification($email, $user['nombre']);
                error_log("🔧 Resultado del envío de correo: " . ($emailResult ? 'ÉXITO' : 'FALLO'));
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => 'Contraseña actualizada exitosamente. Se ha enviado un correo de confirmación.'
                ]);
            } else {
                error_log("❌ Error al actualizar en la base de datos");
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'error' => 'Error al actualizar la contraseña en la base de datos'
                ]);
            }
        } else {
            error_log("❌ Usuario no encontrado para email: $email");
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'error' => 'Usuario no encontrado'
            ]);
        }
        exit;
    }
}
    
    private function sendPasswordChangeNotification($email, $nombre) {
        try {
            $Asunto = 'Contraseña Actualizada - AgroCaja Chica';
            
            $Mensaje = "
                <html>
                <head>
                    <title>Contraseña Actualizada</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2>Contraseña Actualizada - AgroCaja Chica</h2>
                        <p>Hola <strong>{$nombre}</strong>,</p>
                        <p>Tu contraseña en el sistema AgroCaja Chica ha sido actualizada exitosamente.</p>
                        <p><strong>✅ Cambio realizado con éxito</strong></p>
                        <p>Si no realizaste este cambio, por favor contacta inmediatamente al administrador del sistema.</p>
                        <div class='footer'>
                            <p>Este es un mensaje automático, por favor no respondas.</p>
                            <p>AgroCaja Chica &copy; " . date('Y') . "</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $MensajeAlterno = "CONTRASEÑA ACTUALIZADA - AGROCAJA CHICA\n\n" .
                "Hola {$nombre},\n\n" .
                "Tu contraseña en el sistema AgroCaja Chica ha sido actualizada exitosamente.\n\n" .
                "Si no realizaste este cambio, por favor contacta inmediatamente al administrador.\n\n" .
                "Saludos,\nSistema AgroCaja Chica";
    
            // Usar la misma estrategia de envío que en resetPassword
            return $this->sendWithExactConfig($email, $nombre, $Asunto, $Mensaje, $MensajeAlterno);
            
        } catch (Exception $e) {
            error_log("❌ Error enviando notificación de cambio de contraseña: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendWithExactConfig($email, $nombre, $subject, $htmlBody, $textBody) {
        try {
            $mail = new PHPMailer();
            $mail->isSMTP();

            // $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            // $mail->Debugoutput = function($str, $level) {
            // error_log("DEBUG Mailtrap: $str");
            // };
            
            // USAR getenv() EN VEZ DE $_ENV
            $mail->Host = getenv('MAILTRAP_HOST') ?: 'live.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = getenv('MAILTRAP_PORT') ?: 2525;
            $mail->Username = getenv('MAILTRAP_USERNAME') ?: 'smtp@mailtrap.io';
            $mail->Password = getenv('MAILTRAP_PASSWORD') ?: '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 10;
            $mail->SMTPDebug = 0;

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
            $mail->Host = getenv('OFFICE365_HOST') ?: 'smtp-mail.outlook.com';
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
                // ENVIAR CONTRASEÑA EN TEXTO PLANO - EL MODELO SE ENCARGARÁ DEL HASHING
                $result = $this->usuario->updateUsuario($user['id'], $user['nombre'], $email, $newPassword, $user['id_rol']);
                
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

    public function sendLiquidationNotification($email, $nombre, $liquidationId, $liquidacionInfo) {
    try {
        error_log("🔧 Enviando correo de liquidación a: $email");
        
        $asunto = 'Liquidación Asignada - AgroCaja Chica';
        
        $systemUrl = $this->getSystemUrl();
        
        $mensaje = "
            <html>
            <head>
                <title>Liquidación Asignada</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
                    .info-box { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #007bff; }
                    .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center; }
                    .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
                    .urgent { color: #d9534f; font-weight: bold; }
                    .steps { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>📋 Liquidación Asignada - AgroCaja Chica</h2>
                    </div>
                    
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    
                    <p>Se te ha asignado una nueva liquidación para <span class='urgent'>revisión y autorización</span>.</p>
                    
                    <div class='info-box'>
                        <h3>📊 Información de la Liquidación</h3>
                        <p><strong>ID de Liquidación:</strong> #{$liquidationId}</p>
                        <p><strong>Estado Actual:</strong> <span class='urgent'>PENDIENTE DE AUTORIZACIÓN</span></p>
                        <p><strong>Fecha de Finalización:</strong> " . date('d/m/Y H:i') . "</p>
                        " . ($liquidacionInfo ? "<p><strong>Información Adicional:</strong> {$liquidacionInfo}</p>" : "") . "
                    </div>
                    
                    <div class='steps'>
                        <h4>📝 Para revisar la liquidación:</h4>
                        <ol>
                            <li>Inicia sesión en el sistema</li>
                            <li>Ve a la sección <strong>Liquidaciones</strong></li>
                            <li>Busca la liquidación con ID <strong>#{$liquidationId}</strong></li>
                            <li>Revisa y autoriza la liquidación</li>
                        </ol>
                    </div>
                    
                   
                    
                    <p style='text-align: center; font-size: 14px; color: #666;'>
                        <em>copia y pega esta URL en tu navegador:</em><br>
                        <code>{$systemUrl}</code>
                        
                    </p>
                    
                    <div class='footer'>
                        <p>Este es un mensaje automático del sistema AgroCaja Chica.</p>
                        <p>Por favor no respondas a este correo.</p>
                        <p>AgroCaja Chica &copy; " . date('Y') . "</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mensajeAlterno = "LIQUIDACIÓN ASIGNADA - AGROCAJA CHICA\n\n" .
            "Hola {$nombre},\n\n" .
            "Se te ha asignado una nueva liquidación para revisión y autorización.\n\n" .
            "INFORMACIÓN DE LA LIQUIDACIÓN:\n" .
            "ID: #{$liquidationId}\n" .
            "Estado: PENDIENTE DE AUTORIZACIÓN\n" .
            "Fecha: " . date('d/m/Y H:i') . "\n" .
            ($liquidacionInfo ? "Información: {$liquidacionInfo}\n" : "") . "\n\n" .
            "PARA REVISAR LA LIQUIDACIÓN:\n" .
            "1. Inicia sesión en el sistema: {$systemUrl}\n" .
            "2. Ve a la sección 'Liquidaciones'\n" .
            "3. Busca la liquidación con ID #{$liquidationId}\n" .
            "4. Revisa y autoriza la liquidación\n\n" .
            "Saludos,\nSistema AgroCaja Chica";

        $result = $this->sendWithExactConfig($email, $nombre, $asunto, $mensaje, $mensajeAlterno);
        
        if ($result) {
            error_log("✅ Correo de liquidación enviado exitosamente a: $email");
        } else {
            error_log("❌ Falló el envío de correo de liquidación a: $email");
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log("❌ Error enviando notificación de liquidación: " . $e->getMessage());
        return false;
    }
}

private function getSystemUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . "://" . $host . "/index.php?controller=login&action=login";
}

public function sendContadorNotification($email, $nombre, $liquidationId, $liquidacionInfo, $supervisorName) {
    try {
        error_log("🔧 Enviando correo de liquidación autorizada a contador: $email");
        
        $asunto = 'Liquidación Autorizada - Pendiente de Revisión Contable - AgroCaja Chica';
        
        $systemUrl = $this->getSystemUrl();
        
        $mensaje = "
            <html>
            <head>
                <title>Liquidación Autorizada</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
                    .info-box { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #007bff; }
                    .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center; }
                    .btn { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
                    .urgent { color: #d9534f; font-weight: bold; }
                    .steps { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>✅ Liquidación Autorizada - AgroCaja Chica</h2>
                    </div>
                    
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    
                    <p>Una liquidación ha sido <strong>autorizada por el supervisor</strong> y está pendiente de tu revisión contable.</p>
                    
                    <div class='info-box'>
                        <h3>📊 Información de la Liquidación</h3>
                        <p><strong>ID de Liquidación:</strong> #{$liquidationId}</p>
                        <p><strong>Estado Actual:</strong> <span class='urgent'>PENDIENTE REVISIÓN CONTABILIDAD</span></p>
                        <p><strong>Autorizada por:</strong> {$supervisorName}</p>
                        <p><strong>Fecha de Autorización:</strong> " . date('d/m/Y H:i') . "</p>
                        " . ($liquidacionInfo ? "<p><strong>Información Adicional:</strong> {$liquidacionInfo}</p>" : "") . "
                    </div>
                    
                    <div class='steps'>
                        <h4>📝 Para revisar la liquidación:</h4>
                        <ol>
                            <li>Inicia sesión en el sistema</li>
                            <li>Ve a la sección <strong>Liquidaciones</strong></li>
                            <li>Busca la liquidación con ID <strong>#{$liquidationId}</strong></li>
                            <li>Revisa y autoriza la liquidación desde el punto de vista contable</li>
                        </ol>
                    </div>
                    
                    <p style='text-align: center; font-size: 14px; color: #666;'>
                        <em>Si el botón no funciona, copia y pega esta URL en tu navegador:</em><br>
                        <code>{$systemUrl}</code>
                    </p>
                    
                    <div class='footer'>
                        <p>Este es un mensaje automático del sistema AgroCaja Chica.</p>
                        <p>Por favor no respondas a este correo.</p>
                        <p>AgroCaja Chica &copy; " . date('Y') . "</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mensajeAlterno = "LIQUIDACIÓN AUTORIZADA - AGROCAJA CHICA\n\n" .
            "Hola {$nombre},\n\n" .
            "Una liquidación ha sido AUTORIZADA POR EL SUPERVISOR y está pendiente de tu revisión contable.\n\n" .
            "INFORMACIÓN DE LA LIQUIDACIÓN:\n" .
            "ID: #{$liquidationId}\n" .
            "Estado: PENDIENTE REVISIÓN CONTABILIDAD\n" .
            "Autorizada por: {$supervisorName}\n" .
            "Fecha: " . date('d/m/Y H:i') . "\n" .
            ($liquidacionInfo ? "Información: {$liquidacionInfo}\n" : "") . "\n\n" .
            "PARA REVISAR LA LIQUIDACIÓN:\n" .
            "1. Inicia sesión en el sistema: {$systemUrl}\n" .
            "2. Ve a la sección 'Liquidaciones'\n" .
            "3. Busca la liquidación con ID #{$liquidationId}\n" .
            "4. Revisa y autoriza la liquidación desde el punto de vista contable\n\n" .
            "Saludos,\nSistema AgroCaja Chica";

        $result = $this->sendWithExactConfig($email, $nombre, $asunto, $mensaje, $mensajeAlterno);
        
        if ($result) {
            error_log("✅ Correo de liquidación autorizada enviado exitosamente al contador: $email");
        } else {
            error_log("❌ Falló el envío de correo de liquidación autorizada al contador: $email");
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log("❌ Error enviando notificación de liquidación autorizada al contador: " . $e->getMessage());
        return false;
    }
}

public function sendEncargadoNotification($email, $nombre, $liquidationId, $liquidacionInfo, $contadorName) {
    try {
        error_log("🔧 Enviando correo de liquidación finalizada a encargado: $email");
        
        $asunto = 'Liquidación Finalizada y Exportada a SAP - AgroCaja Chica';
        
        $systemUrl = $this->getSystemUrl();
        
        $mensaje = "
            <html>
            <head>
                <title>Liquidación Finalizada</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
                    .info-box { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #28a745; }
                    .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center; }
                    .btn { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
                    .success { color: #28a745; font-weight: bold; }
                    .steps { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>✅ Liquidación Finalizada - AgroCaja Chica</h2>
                    </div>
                    
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    
                    <p>Tu liquidación ha sido <strong class='success'>finalizada y exportada exitosamente a SAP</strong>.</p>
                    
                    <div class='info-box'>
                        <h3>📊 Información de la Liquidación</h3>
                        <p><strong>ID de Liquidación:</strong> #{$liquidationId}</p>
                        <p><strong>Estado Actual:</strong> <span class='success'>FINALIZADA</span></p>
                        <p><strong>Autorizada por contador:</strong> {$contadorName}</p>
                        <p><strong>Fecha de Finalización:</strong> " . date('d/m/Y H:i') . "</p>
                        <p><strong>Exportación SAP:</strong> <span class='success'>COMPLETADA</span></p>
                        " . ($liquidacionInfo ? "<p><strong>Información Adicional:</strong> {$liquidacionInfo}</p>" : "") . "
                    </div>
                    
                    <div class='steps'>
                        <h4>🎉 Proceso Completado</h4>
                        <ul>
                            <li>✅ Liquidación creada</li>
                            <li>✅ Revisada por supervisor</li>
                            <li>✅ Autorizada por contabilidad</li>
                            <li>✅ Exportada a SAP</li>
                            <li>✅ Proceso finalizado</li>
                        </ul>
                    </div>
                    
                    <p style='text-align: center;'>
                        <a href='{$systemUrl}' class='btn'>
                            📋 Ver Liquidaciones
                        </a>
                    </p>
                    
                    <p style='text-align: center; font-size: 14px; color: #666;'>
                        <em>Puedes revisar el estado de todas tus liquidaciones en el sistema.</em>
                    </p>
                    
                    <div class='footer'>
                        <p>Este es un mensaje automático del sistema AgroCaja Chica.</p>
                        <p>Por favor no respondas a este correo.</p>
                        <p>AgroCaja Chica &copy; " . date('Y') . "</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mensajeAlterno = "LIQUIDACIÓN FINALIZADA - AGROCAJA CHICA\n\n" .
            "Hola {$nombre},\n\n" .
            "Tu liquidación ha sido FINALIZADA Y EXPORTADA EXITOSAMENTE A SAP.\n\n" .
            "INFORMACIÓN DE LA LIQUIDACIÓN:\n" .
            "ID: #{$liquidationId}\n" .
            "Estado: FINALIZADA\n" .
            "Autorizada por contador: {$contadorName}\n" .
            "Fecha: " . date('d/m/Y H:i') . "\n" .
            "Exportación SAP: COMPLETADA\n" .
            ($liquidacionInfo ? "Información: {$liquidacionInfo}\n" : "") . "\n\n" .
            "PROCESO COMPLETADO:\n" .
            "✅ Liquidación creada\n" .
            "✅ Revisada por supervisor\n" .
            "✅ Autorizada por contabilidad\n" .
            "✅ Exportada a SAP\n" .
            "✅ Proceso finalizado\n\n" .
            "Saludos,\nSistema AgroCaja Chica";

        $result = $this->sendWithExactConfig($email, $nombre, $asunto, $mensaje, $mensajeAlterno);
        
        if ($result) {
            error_log("✅ Correo de liquidación finalizada enviado exitosamente al encargado: $email");
        } else {
            error_log("❌ Falló el envío de correo de liquidación finalizada al encargado: $email");
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log("❌ Error enviando notificación de liquidación finalizada al encargado: " . $e->getMessage());
        return false;
    }
}
}