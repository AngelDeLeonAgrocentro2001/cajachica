<?php
require_once '../models/CajaChica.php';

class CajaChicaController {
    public function listCajasChicas() {
        $cajaChica = new CajaChica();
        $cajas = $cajaChica->getAllCajasChicas();
        header('Content-Type: application/json');
        echo json_encode($cajas);
        exit;
    }

    public function createCajaChica() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $monto_asignado = $_POST['monto_asignado'] ?? 0;
            $monto_disponible = $_POST['monto_disponible'] ?? 0;
            $id_usuario_encargado = $_POST['id_usuario_encargado'] ?? '';
            $id_supervisor = $_POST['id_supervisor'] ?? '';

            $cajaChica = new CajaChica();
            if ($cajaChica->createCajaChica($nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor)) {
                http_response_code(201);
                echo json_encode(['message' => 'Caja chica creada']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear caja chica']);
            }
            exit;
        }
        require '../views/cajas_chicas/form.html';
    }

    public function updateCajaChica($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $monto_asignado = $_POST['monto_asignado'] ?? 0;
            $monto_disponible = $_POST['monto_disponible'] ?? 0;
            $id_usuario_encargado = $_POST['id_usuario_encargado'] ?? '';
            $id_supervisor = $_POST['id_supervisor'] ?? '';
            $estado = $_POST['estado'] ?? 'ACTIVA';

            $cajaChica = new CajaChica();
            if ($cajaChica->updateCajaChica($id, $nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor, $estado)) {
                echo json_encode(['message' => 'Caja chica actualizada']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar caja chica']);
            }
            exit;
        }
        $cajaChica = new CajaChica();
        $data = $cajaChica->getCajaChicaById($id);
        require '../views/cajas_chicas/form.html';
    }

    public function deleteCajaChica($id) {
        $cajaChica = new CajaChica();
        if ($cajaChica->deleteCajaChica($id)) {
            echo json_encode(['message' => 'Caja chica eliminada']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar caja chica']);
        }
        exit;
    }
}