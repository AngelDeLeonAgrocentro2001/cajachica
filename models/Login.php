<?php
require_once '../config/database.php';
require_once '../models/Usuario.php';

class Login {
    private $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    public function authenticate($email, $password) {
        $usuario = $this->usuario->getUsuarioByEmail($email);
        error_log("Usuario obtenido en authenticate: " . print_r($usuario, true));
        if ($usuario && password_verify($password, $usuario['password'])) {
            error_log("Autenticación exitosa para $email");
            unset($usuario['password']);
            return $usuario;
        }
        error_log("Fallo en autenticación para $email");
        return false;
    }
}