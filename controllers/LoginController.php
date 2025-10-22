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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            
            error_log("=== PRUEBA DIRECTA ===");
            error_log("Email recibido: " . $email);
            
            // Simplemente probar si el código llega aquí
            error_log("Llegó al método resetPassword POST");
            
            // Forzar un error de prueba
            try {
                throw new Exception("Esta es una prueba de error");
            } catch (Exception $e) {
                error_log("Prueba de error funcionando: " . $e->getMessage());
            }
            
            header('Location: index.php?controller=login&action=resetPassword&error=Prueba de error');
            exit;
        }
        
        require '../views/login/reset.html';
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