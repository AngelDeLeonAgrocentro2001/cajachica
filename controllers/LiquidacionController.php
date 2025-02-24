<?php
require_once '../models/Liquidacion.php';

class LiquidacionController {
    public function listLiquidaciones() {
        $liquidacion = new Liquidacion();
        $liquidaciones = $liquidacion->getAllLiquidaciones();
        header('Content-Type: application/json');
        echo json_encode($liquidaciones);
        exit;
    }

    public function createLiquidacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_caja_chica = $_POST['id_caja_chica'] ?? '';
            $fecha_creacion = $_POST['fecha_creacion'] ?? date('Y-m-d');
            $monto_total = $_POST['monto_total'] ?? 0;

            $liquidacion = new Liquidacion();
            if ($liquidacion->createLiquidacion($id_caja_chica, $fecha_creacion, $monto_total)) {
                http_response_code(201);
                echo json_encode(['message' => 'Liquidación creada']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear liquidación']);
            }
            exit;
        }
        require '../views/liquidaciones/form.html';
    }

    public function updateLiquidacion($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_caja_chica = $_POST['id_caja_chica'] ?? '';
            $fecha_creacion = $_POST['fecha_creacion'] ?? '';
            $monto_total = $_POST['monto_total'] ?? 0;
            $estado = $_POST['estado'] ?? 'PENDIENTE';

            $liquidacion = new Liquidacion();
            if ($liquidacion->updateLiquidacion($id, $id_caja_chica, $fecha_creacion, $monto_total, $estado)) {
                echo json_encode(['message' => 'Liquidación actualizada']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar liquidación']);
            }
            exit;
        }
        $liquidacion = new Liquidacion();
        $data = $liquidacion->getLiquidacionById($id);
        require '../views/liquidaciones/form.html';
    }

    public function deleteLiquidacion($id) {
        $liquidacion = new Liquidacion();
        if ($liquidacion->deleteLiquidacion($id)) {
            echo json_encode(['message' => 'Liquidación eliminada']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar liquidación']);
        }
        exit;
    }
}