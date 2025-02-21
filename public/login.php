
<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../app/controllers/AuthController.php';

$error_message = null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = AuthController::login($email, $password);
    
    if($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error_message = $result['message'];
    }
}

// Mostrar la vista de login
require_once __DIR__ . '/../app/views/login.view.php';