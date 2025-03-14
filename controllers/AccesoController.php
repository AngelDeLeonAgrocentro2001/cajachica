<?php
require_once '../models/Acceso.php';
require_once '../models/Usuario.php';
require_once '../models/CuentaContable.php';

class AccesoController {
    private $accesoModel;
    private $usuarioModel;
    private $cuentaContableModel;

    public function __construct() {
        $this->accesoModel = new Acceso();
        $this->usuarioModel = new Usuario();
        $this->cuentaContableModel = new CuentaContable();
    }

    public function selectCuenta() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=login&action=login');
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar accesos']);
            exit;
        }

        $cuentas = $this->cuentaContableModel->getAllCuentas();
        require '../views/accesos/select_cuenta.html';
        exit;
    }

    public function list($cuenta_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar accesos']);
            exit;
        }

        $accesos = $this->accesoModel->getAccesosByCuenta($cuenta_id);
        $cuenta = $this->cuentaContableModel->getCuentaById($cuenta_id);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($accesos);
        } else {
            require '../views/accesos/list.html';
        }
        exit;
    }

    public function assignForm($cuenta_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para asignar usuarios']);
            exit;
        }

        $cuenta = $this->cuentaContableModel->getCuentaById($cuenta_id);
        require '../views/accesos/assign_form.html';
    }

    public function assign($cuenta_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para asignar usuarios']);
            exit;
        }

        $email = $_POST['email'] ?? '';
        $user = $this->usuarioModel->getUsuarioByEmail($email);
        if (!$user) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        if ($this->accesoModel->assignUsuario($user['id'], $cuenta_id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Usuario asignado correctamente']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al asignar usuario']);
        }
        exit;
    }

    public function remove($cuenta_id, $usuario_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar accesos']);
            exit;
        }

        if ($this->accesoModel->removeUsuario($usuario_id, $cuenta_id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Usuario eliminado correctamente']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar usuario']);
        }
        exit;
    }
}