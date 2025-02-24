<?php
require_once '../config/database.php';
require_once '../config/jwt.php';

class Login {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function authenticate($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // No incluimos la contraseña en el token
            return generateJWT(['user' => $user]);
        }
        return false;
    }
}