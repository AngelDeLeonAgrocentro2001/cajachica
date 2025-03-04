<?php
require_once '../models/TipoGasto.php';

class TipoGastoController {
    public function listTiposGastos() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $tipoGasto = new TipoGasto();
        $tipos = $tipoGasto->getAllTiposGastos();

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name) || empty($description)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Nombre y descripción son obligatorios.']);
                exit;
            }

            $tipoGasto = new TipoGasto();
            if ($tipoGasto->createTipoGasto($name, $description)) {
                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode(['message' => 'Tipo de gasto creado']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear tipo de gasto en la base de datos.']);
            }
            exit;
        }

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name) || empty($description)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Nombre y descripción son obligatorios.']);
                exit;
            }

            $tipoGasto = new TipoGasto();
            if ($tipoGasto->updateTipoGasto($id, $name, $description)) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Tipo de gasto actualizado']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar tipo de gasto en la base de datos.']);
            }
            exit;
        }

        $tipoGasto = new TipoGasto();
        $data = $tipoGasto->getTipoGastoById($id);

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

        $tipoGasto = new TipoGasto();
        if ($tipoGasto->deleteTipoGasto($id)) {
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