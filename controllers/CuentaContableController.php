<?php
require_once '../models/CuentaContable.php';
require_once '../models/Usuario.php';

class CuentaContableController {
    private $cuentaContableModel;
    private $usuarioModel;

    public function __construct() {
        $this->cuentaContableModel = new CuentaContable();
        $this->usuarioModel = new Usuario();
    }

    public function listCuentas() {
        if (!isset($_SESSION['user_id'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'No autorizado']);
                exit;
            } else {
                header('Location: index.php?controller=login&action=login');
                exit;
            }
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuario) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Usuario no encontrado']);
                exit;
            } else {
                header('Location: index.php?controller=login&action=login');
                exit;
            }
        }

        // Detectar si es una solicitud AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $estado = $_GET['estado'] ?? null;
            $cuentas = $this->cuentaContableModel->getAllCuentasContables($estado);
            header('Content-Type: application/json');
            echo json_encode($cuentas);
            exit;
        }

        // Si no es una solicitud AJAX, renderizar la vista
        $usuarioModel = $this->usuarioModel; // Pasar el modelo y el usuario a la vista
        include '../views/cuentacontable/list.html';
    }

    public function createForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$this->usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear cuentas contables']);
            exit;
        }

        include '../views/cuentacontable/form.html';
    }

    public function createCuenta() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$this->usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear cuentas contables']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $estado = $_POST['estado'] ?? 'ACTIVO';

            if (empty($nombre)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'El nombre es obligatorio']);
                exit;
            }

            $result = $this->cuentaContableModel->createCuentaContable($nombre, $descripcion, $estado);
            if ($result) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Cuenta contable creada con éxito']);
            } else {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear la cuenta contable']);
            }
        } else {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function updateCuenta($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$this->usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar cuentas contables']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $estado = $_POST['estado'] ?? 'ACTIVO';

            if (empty($nombre)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'El nombre es obligatorio']);
                exit;
            }

            $result = $this->cuentaContableModel->updateCuentaContable($id, $nombre, $descripcion, $estado);
            if ($result) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Cuenta contable actualizada con éxito']);
            } else {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar la cuenta contable']);
            }
        } else {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function updateForm($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$this->usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar cuentas contables']);
            exit;
        }

        $cuenta = $this->cuentaContableModel->getCuentaContableById($id);
        if (!$cuenta) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Cuenta contable no encontrada']);
            exit;
        }

        $cuentaContable = $cuenta; // Pasar la variable a la vista
        include '../views/cuentacontable/update_form.html';
    }

    public function deleteCuenta($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$this->usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar cuentas contables']);
            exit;
        }

        $result = $this->cuentaContableModel->deleteCuentaContable($id);
        if ($result) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Cuenta contable eliminada con éxito']);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar la cuenta contable']);
        }
    }
}