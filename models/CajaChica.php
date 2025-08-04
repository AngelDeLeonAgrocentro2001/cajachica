<?php
require_once '../config/database.php';

class CajaChica {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllCajasChicas() {
        $stmt = $this->pdo->query("
            SELECT cc.*, 
                   c.nombre AS centro_costo, 
                   u.nombre AS nombre_contador,
                   u2.nombre AS nombre_encargado
            FROM cajas_chicas cc 
            LEFT JOIN centros_costos c ON cc.id_centro_costo = c.id
            LEFT JOIN usuarios u ON cc.id_contador = u.id
            LEFT JOIN usuarios u2 ON cc.id_usuario_encargado = u2.id
        ");
        $cajasChicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch client names
        $controller = new CajaChicaController();
        $clientes = $controller->ctrObtenerClientes('GT_AGROCENTRO_2016');
        $clientesMap = [];
        if ($clientes !== 'sin_datos') {
            $clientesArray = explode('|', trim($clientes, '|'));
            foreach ($clientesArray as $cliente) {
                list($cardCode, $cardName) = explode('-', $cliente, 2);
                $clientesMap[$cardCode] = $cardName;
            }
        }
    
        foreach ($cajasChicas as &$caja) {
            $caja['nombre_caja_chica'] = $caja['nombre'];
            $caja['cliente_nombre_caja_chica'] = isset($clientesMap[$caja['clientes']]) ? $clientesMap[$caja['clientes']] : 'Cliente no encontrado';
            $caja['clientes'] = $caja['clientes'] ?? 'No asignado';
        }
    
        error_log('Cajas Chicas obtenidas: ' . print_r($cajasChicas, true));
        return $cajasChicas;
    }

    public function getCajaChicaById($id) {
        $stmt = $this->pdo->prepare("
            SELECT cc.*, 
                   c.nombre AS centro_costo, 
                   u.nombre AS nombre_contador,
                   u2.nombre AS nombre_encargado
            FROM cajas_chicas cc 
            LEFT JOIN centros_costos c ON cc.id_centro_costo = c.id
            LEFT JOIN usuarios u ON cc.id_contador = u.id
            LEFT JOIN usuarios u2 ON cc.id_usuario_encargado = u2.id
            WHERE cc.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($data) {
            // Map nombre_caja_chica to nombre for consistency with the view
            $data['nombre_caja_chica'] = $data['nombre'];
    
            // Fetch client name from SAP HANA
            if (!empty($data['clientes'])) {
                $controller = new CajaChicaController();
                $clientes = $controller->ctrObtenerClientes('GT_AGROCENTRO_2016');
                if ($clientes !== 'sin_datos') {
                    $clientesArray = explode('|', trim($clientes, '|'));
                    foreach ($clientesArray as $cliente) {
                        list($cardCode, $cardName) = explode('-', $cliente, 2);
                        if ($cardCode === $data['clientes']) {
                            $data['cliente_nombre_caja_chica'] = $cardName; // Use the correct key
                            break;
                        }
                    }
                }
                if (!isset($data['cliente_nombre_caja_chica'])) {
                    $data['cliente_nombre_caja_chica'] = 'Cliente no encontrado';
                }
            } else {
                $data['cliente_nombre_caja_chica'] = 'No asignado';
                $data['clientes'] = 'No asignado';
            }
    
            // Log the data for debugging
            error_log('Datos de getCajaChicaById: ' . print_r($data, true));
        }
    
        return $data;
    }

    public function createCajaChica($nombre, $monto_asignado, $id_usuario_encargado, $id_supervisor, $id_contador, $id_centro_costo, $estado, $clientes) {
        $stmt = $this->pdo->prepare("
            INSERT INTO cajas_chicas (nombre, monto_asignado, monto_disponible, id_usuario_encargado, id_supervisor, id_contador, id_centro_costo, estado, clientes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$nombre, $monto_asignado, $monto_asignado, $id_usuario_encargado, $id_supervisor, $id_contador, $id_centro_costo, $estado, $clientes]);
    }

    public function updateCajaChica($id, $nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor, $id_contador, $id_centro_costo, $estado, $clientes) {
        $stmt = $this->pdo->prepare("
            UPDATE cajas_chicas 
            SET nombre = ?, monto_asignado = ?, monto_disponible = ?, id_usuario_encargado = ?, id_supervisor = ?, id_contador = ?, id_centro_costo = ?, estado = ?, clientes = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor, $id_contador, $id_centro_costo, $estado, $clientes, $id]);
    }

    public function deleteCajaChica($id) {
        $stmt = $this->pdo->prepare("DELETE FROM cajas_chicas WHERE id = ?");
        return $stmt->execute([$id]);
    }
}