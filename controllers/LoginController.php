<?php
require_once '../models/Login.php';

class LoginController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $login = new Login();
            $token = $login->authenticate($username, $password);

            if ($token) {
                header('Content-Type: application/json');
                echo json_encode(['token' => $token]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciales inválidas']);
            }
            exit;
        }
        // Redirigir o mostrar formulario de login
        require '../views/login/index.html';
    }
}