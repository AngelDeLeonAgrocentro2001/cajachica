<?php
require_once '../models/Usuario.php';
require_once '../config/jwt.php';

class UsuarioController {
    public function listUsuarios() {
        $usuario = new Usuario();
        $usuarios = $usuario->getAllUsuarios();
        header('Content-Type: application/json');
        echo json_encode($usuarios);
        exit;
    }

    public function createUsuario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $rol = $_POST['rol'] ?? 'ENCARGADO_CAJA_CHICA';

            $usuario = new Usuario();
            if ($usuario->createUsuario($nombre, $email, $password, $rol)) {
                http_response_code(201);
                echo json_encode(['message' => 'Usuario creado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear usuario']);
            }
            exit;
        }
        require '../views/usuarios/form.html';
    }

    public function updateUsuario($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $rol = $_POST['rol'] ?? '';

            $usuario = new Usuario();
            if ($usuario->updateUsuario($id, $nombre, $email, $rol)) {
                echo json_encode(['message' => 'Usuario actualizado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar usuario']);
            }
            exit;
        }
        $usuario = new Usuario();
        $data = $usuario->getUsuarioById($id);
        require '../views/usuarios/form.html';
    }

    public function deleteUsuario($id) {
        $usuario = new Usuario();
        if ($usuario->deleteUsuario($id)) {
            echo json_encode(['message' => 'Usuario eliminado']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar usuario']);
        }
        exit;
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            error_log("Email recibido: " . $email . ", Password: " . $password);
            $usuario = new Usuario();
            $user = $usuario->authenticate($email, $password);
        
            if ($user) {
                $token = generateJWT(['user' => $user]);
                header('Content-Type: application/json');
                echo json_encode(['token' => $token]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciales inválidas']);
            }
            exit;
        }
        require '../views/login/index.html';
    }
}