<?php
require_once '../models/CuentaContable.php';
require_once '../models/Usuario.php';

class CuentaContableController {
    private $cuentaContableModel;

    public function __construct() {
        $this->cuentaContableModel = new CuentaContable();
    }

    public function listCuentas() {
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
            echo json_encode(['error' => 'No tienes permiso para gestionar cuentas contables']);
            exit;
        }

        $cuentas = $this->cuentaContableModel->getAllCuentas();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($cuentas);
        } else {
            require '../views/cuentacontable/list.html';
        }
        exit;
    }

    public function createCuenta() {
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
            echo json_encode(['error' => 'No tienes permiso para crear cuentas contables']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $codigo = $_POST['codigo'] ?? '';
                $nombre = $_POST['nombre'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';
    
                if (empty($codigo) || empty($nombre)) {
                    throw new Exception('Código y nombre son obligatorios');
                }
    
                $result = $this->cuentaContableModel->createCuenta($codigo, $nombre, $estado);
                if ($result === false) {
                    throw new Exception('Error al crear cuenta contable');
                }
    
                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode(['message' => 'Cuenta contable creada']);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                $errorMessage = 'Error al crear cuenta contable';
                if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errorMessage = "El código '$codigo' ya está registrado. Por favor, usa un código diferente.";
                } else {
                    $errorMessage .= ': ' . $e->getMessage();
                }
                echo json_encode(['error' => $errorMessage]);
            }
            exit;
        }
    
        ob_start();
        require '../views/cuentacontable/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function updateCuenta($id) {
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
            echo json_encode(['error' => 'No tienes permiso para actualizar cuentas contables']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $codigo = $_POST['codigo'] ?? '';
                $nombre = $_POST['nombre'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';
    
                if (empty($codigo) || empty($nombre)) {
                    throw new Exception('Código y nombre son obligatorios');
                }
    
                $result = $this->cuentaContableModel->updateCuenta($id, $codigo, $nombre, $estado);
                if ($result === false) {
                    throw new Exception('Error al actualizar cuenta contable');
                }
    
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Cuenta contable actualizada']);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $cuenta = $this->cuentaContableModel->getCuentaById($id);
        if ($cuenta === false) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Cuenta contable no encontrada']);
            exit;
        }
    
        ob_start();
        require '../views/cuentacontable/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function deleteCuenta($id) {
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
            echo json_encode(['error' => 'No tienes permiso para eliminar cuentas contables']);
            exit;
        }

        if ($this->cuentaContableModel->deleteCuenta($id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Cuenta contable eliminada']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar cuenta contable']);
        }
        exit;
    }
}