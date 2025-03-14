<?php
require_once '../models/Role.php';
require_once '../models/Usuario.php';

class RoleController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listRoles() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar roles']);
            exit;
        }

        $rolModel = new Role();
        $roles = $rolModel->getAllRoles();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($roles);
        } else {
            require '../views/roles/list.html';
        }
        exit;
    }

    public function createRol() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en createRol');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            error_log('Error: No tienes permiso para crear roles');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear roles']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';

                error_log("Datos recibidos para crear rol: nombre=$nombre, descripcion=$descripcion, estado=$estado");

                if (empty($nombre)) {
                    throw new Exception('El nombre del rol es obligatorio');
                }

                $rolModel = new Role();
                $result = $rolModel->createRol($nombre, $descripcion, $estado);
                if ($result === false) {
                    throw new Exception('Error al crear rol en la base de datos');
                }

                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode(['message' => 'Rol creado']);
            } catch (Exception $e) {
                error_log('Error en createRol: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }

        ob_start();
        require '../views/roles/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function updateRol($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateRol');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            error_log('Error: No tienes permiso para actualizar roles');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar roles']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';

                error_log("Datos recibidos para actualizar rol ID $id: nombre=$nombre, descripcion=$descripcion, estado=$estado");

                if (empty($nombre)) {
                    throw new Exception('El nombre del rol es obligatorio');
                }

                $rolModel = new Role();
                $result = $rolModel->updateRol($id, $nombre, $descripcion, $estado);
                if ($result === false) {
                    throw new Exception('Error al actualizar rol en la base de datos');
                }

                header('Content-Type: application/json');
                echo json_encode(['message' => 'Rol actualizado']);
            } catch (Exception $e) {
                error_log('Error en updateRol: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }

        $rolModel = new Role();
        $data = $rolModel->getRolById($id);
        if ($data === false) {
            error_log("Error: No se pudo obtener el rol con ID $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Rol no encontrado']);
            exit;
        }

        ob_start();
        require '../views/roles/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function deleteRol($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar roles']);
            exit;
        }

        $rolModel = new Role();
        if ($rolModel->deleteRol($id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Rol eliminado']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar rol. Es posible que esté en uso por algún usuario.']);
        }
        exit;
    }
}