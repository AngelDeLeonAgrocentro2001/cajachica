<?php
require_once '../models/Impuesto.php';
require_once '../models/Usuario.php';

class ImpuestoController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listImpuestos() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar impuestos']);
            exit;
        }

        $impuestoModel = new Impuesto();
        $impuestos = $impuestoModel->getAllImpuestos();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($impuestos);
        } else {
            require '../views/impuesto/list.html';
        }
        exit;
    }

    public function createImpuesto() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en createImpuesto');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            error_log('Error: No tienes permiso para crear impuestos');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear impuestos']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $porcentaje = $_POST['porcentaje'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';

                error_log("Datos recibidos para crear impuesto: nombre=$nombre, porcentaje=$porcentaje, estado=$estado");

                if (empty($nombre) || empty($porcentaje)) {
                    throw new Exception('Nombre y porcentaje son obligatorios');
                }

                $porcentaje = floatval($porcentaje);
                if ($porcentaje <= 0 || $porcentaje > 100) {
                    throw new Exception('El porcentaje debe estar entre 0 y 100');
                }

                $impuestoModel = new Impuesto();
                $result = $impuestoModel->createImpuesto($nombre, $porcentaje, $estado);
                if ($result === false) {
                    throw new Exception('Error al crear impuesto en la base de datos');
                }

                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode(['message' => 'Impuesto creado']);
            } catch (Exception $e) {
                error_log('Error en createImpuesto: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }

        ob_start();
        require '../views/impuesto/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function updateImpuesto($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateImpuesto');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            error_log('Error: No tienes permiso para actualizar impuestos');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar impuestos']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $porcentaje = $_POST['porcentaje'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';

                error_log("Datos recibidos para actualizar impuesto ID $id: nombre=$nombre, porcentaje=$porcentaje, estado=$estado");

                if (empty($nombre) || empty($porcentaje)) {
                    throw new Exception('Nombre y porcentaje son obligatorios');
                }

                $porcentaje = floatval($porcentaje);
                if ($porcentaje <= 0 || $porcentaje > 100) {
                    throw new Exception('El porcentaje debe estar entre 0 y 100');
                }

                $impuestoModel = new Impuesto();
                $result = $impuestoModel->updateImpuesto($id, $nombre, $porcentaje, $estado);
                if ($result === false) {
                    throw new Exception('Error al actualizar impuesto en la base de datos');
                }

                header('Content-Type: application/json');
                echo json_encode(['message' => 'Impuesto actualizado']);
            } catch (Exception $e) {
                error_log('Error en updateImpuesto: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }

        $impuestoModel = new Impuesto();
        $data = $impuestoModel->getImpuestoById($id);
        if ($data === false) {
            error_log("Error: No se pudo obtener el impuesto con ID $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Impuesto no encontrado']);
            exit;
        }

        ob_start();
        require '../views/impuesto/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function deleteImpuesto($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar impuestos']);
            exit;
        }

        $impuestoModel = new Impuesto();
        if ($impuestoModel->deleteImpuesto($id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Impuesto eliminado']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar impuesto']);
        }
        exit;
    }
}