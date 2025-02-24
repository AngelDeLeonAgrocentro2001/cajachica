<?php
require_once '../models/TipoGasto.php';

class TipoGastoController {
    public function listTiposGastos() {
        $tipoGasto = new TipoGasto();
        $tipos = $tipoGasto->getAllTiposGastos();
        header('Content-Type: application/json');
        echo json_encode($tipos);
        exit;
    }

    public function createTipoGasto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $tipoGasto = new TipoGasto();
            if ($tipoGasto->createTipoGasto($name, $description)) {
                http_response_code(201);
                echo json_encode(['message' => 'Tipo de gasto creado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear tipo de gasto']);
            }
            exit;
        }
        require '../views/tipos_gastos/form.html';
    }

    public function updateTipoGasto($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $tipoGasto = new TipoGasto();
            if ($tipoGasto->updateTipoGasto($id, $name, $description)) {
                echo json_encode(['message' => 'Tipo de gasto actualizado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar tipo de gasto']);
            }
            exit;
        }
        $tipoGasto = new TipoGasto();
        $data = $tipoGasto->getTipoGastoById($id);
        require '../views/tipos_gastos/form.html';
    }

    public function deleteTipoGasto($id) {
        $tipoGasto = new TipoGasto();
        if ($tipoGasto->deleteTipoGasto($id)) {
            echo json_encode(['message' => 'Tipo de gasto eliminado']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar tipo de gasto']);
        }
        exit;
    }
}