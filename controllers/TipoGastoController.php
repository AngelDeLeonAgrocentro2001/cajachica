<?php
require_once '../models/TipoGasto.php';
require_once '../models/Usuario.php';

class TipoGastoController {
    private $tipoGastoModel;
    private $usuarioModel;

    public function __construct() {
        $this->tipoGastoModel = new TipoGasto();
        $this->usuarioModel = new Usuario();
    }

    public function listTiposGastos() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !$this->usuarioModel->tienePermiso($usuario, 'manage_tipos_gastos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar tipos de gastos']);
            exit;
        }

        $tipos = $this->tipoGastoModel->getAllTiposGastos();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($tipos);
        } else {
            require '../views/tipos_gastos/list.html';
        }
        exit;
    }

    public function createTipoGasto() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !$this->usuarioModel->tienePermiso($usuario, 'manage_tipos_gastos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear tipos de gastos']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';

                if (empty($name) || empty($description)) {
                    throw new Exception('Nombre y descripción son obligatorios.');
                }

                // Verificar si el nombre ya existe
                if ($this->tipoGastoModel->getTipoGastoByName($name)) {
                    throw new Exception("El nombre '$name' ya está registrado. Por favor, usa un nombre diferente.");
                }

                if ($this->tipoGastoModel->createTipoGasto($name, $description, $estado)) {
                    header('Content-Type: application/json');
                    http_response_code(201);
                    echo json_encode(['message' => 'Tipo de gasto creado']);
                } else {
                    throw new Exception('Error al crear tipo de gasto en la base de datos.');
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                $errorMessage = 'Error al crear tipo de gasto';
                if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errorMessage = "El nombre '$name' ya está registrado. Por favor, usa un nombre diferente.";
                } else {
                    $errorMessage .= ': ' . $e->getMessage();
                }
                echo json_encode(['error' => $errorMessage]);
            }
            exit;
        }

        $tipoGasto = [];
        ob_start();
        require '../views/tipos_gastos/form.html';
        $html = ob_get_clean();
        echo $html;
        exit;
    }

    public function updateTipoGasto($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !$this->usuarioModel->tienePermiso($usuario, 'manage_tipos_gastos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar tipos de gastos']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';

                if (empty($name) || empty($description)) {
                    throw new Exception('Nombre y descripción son obligatorios.');
                }

                // Verificar si el nombre ya existe (excluyendo el registro actual)
                $existingTipoGasto = $this->tipoGastoModel->getTipoGastoByName($name, $id);
                if ($existingTipoGasto) {
                    throw new Exception("El nombre '$name' ya está registrado por otro tipo de gasto. Por favor, usa un nombre diferente.");
                }

                if ($this->tipoGastoModel->updateTipoGasto($id, $name, $description, $estado)) {
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Tipo de gasto actualizado']);
                } else {
                    throw new Exception('Error al actualizar tipo de gasto en la base de datos.');
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                $errorMessage = 'Error al actualizar tipo de gasto';
                if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errorMessage = "El nombre '$name' ya está registrado por otro tipo de gasto. Por favor, usa un nombre diferente.";
                } else {
                    $errorMessage .= ': ' . $e->getMessage();
                }
                echo json_encode(['error' => $errorMessage]);
            }
            exit;
        }

        $tipoGasto = $this->tipoGastoModel->getTipoGastoById($id);
        if ($tipoGasto === false) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Tipo de gasto no encontrado']);
            exit;
        }

        ob_start();
        require '../views/tipos_gastos/form.html';
        $html = ob_get_clean();
        echo $html;
        exit;
    }

    public function deleteTipoGasto($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !$this->usuarioModel->tienePermiso($usuario, 'manage_tipos_gastos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar tipos de gastos']);
            exit;
        }

        if ($this->tipoGastoModel->deleteTipoGasto($id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Tipo de gasto eliminado']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar tipo de gasto']);
        }
        exit;
    }
}