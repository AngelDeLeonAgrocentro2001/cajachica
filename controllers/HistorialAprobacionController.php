<?php
require_once '../models/HistorialAprobacion.php';

class HistorialAprobacionController {
    public function listHistorialAprobaciones() {
        $historial = new HistorialAprobacion();
        $historiales = $historial->getAllHistorialAprobaciones();
        header('Content-Type: application/json');
        echo json_encode($historiales);
        exit;
    }

    public function createHistorialAprobacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_liquidacion = $_POST['id_liquidacion'] ?? '';
            $id_detalle_liquidacion = $_POST['id_detalle_liquidacion'] ?? NULL;
            $id_usuario = $_POST['id_usuario'] ?? '';
            $accion = $_POST['accion'] ?? 'APROBADO';
            $comentario = $_POST['comentario'] ?? NULL;

            $historial = new HistorialAprobacion();
            if ($historial->createHistorialAprobacion($id_liquidacion, $id_detalle_liquidacion, $id_usuario, $accion, $comentario)) {
                http_response_code(201);
                echo json_encode(['message' => 'Historial de aprobación creado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear historial de aprobación']);
            }
            exit;
        }
        require '../views/historial_aprobaciones/form.html';
    }
}