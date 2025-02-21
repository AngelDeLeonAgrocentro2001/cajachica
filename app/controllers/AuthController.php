
<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public static function login($email, $password) {
        if(empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Por favor ingrese email y contraseña'];
        }
        
        $user = User::authenticate($email, $password);
        
        if($user) {
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            return ['success' => true, 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Credenciales incorrectas'];
    }
    
    public static function logout() {
        session_start();
        session_destroy();
        header('Location: /public/login.php');
        exit;
    }
}