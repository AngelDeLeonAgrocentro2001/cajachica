<?php
require_once '../config/database.php';

class DetalleLiquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo(); // Usar la clase Database
    }

    public function getAllDetallesLiquidacion() {
        $stmt = $this->pdo->query("SELECT d.*, l.id_caja_chica, l.fecha_creacion FROM detalle_liquidaciones d JOIN liquidaciones l ON d.id_liquidacion = l.id ORDER BY d.id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetalleLiquidacionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM detalle_liquidaciones WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['rutas_archivos'])) {
            $row['rutas_archivos'] = json_decode($row['rutas_archivos'], true) ?: [];
        }
        return $row;
    }

    public function createDetalleLiquidacion($id_liquidacion, $no_factura, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $p_unitario, $total_factura, $estado, $rutas_archivos = '[]') {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO detalle_liquidaciones (id_liquidacion, no_factura, nombre_proveedor, fecha, bien_servicio, t_gasto, p_unitario, total_factura, estado, rutas_archivos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$id_liquidacion, $no_factura, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $p_unitario, $total_factura, $estado, $rutas_archivos]);
        } catch (PDOException $e) {
            error_log("Error al crear detalle de liquidación: " . $e->getMessage());
            return false;
        }
    }

    public function updateDetalleLiquidacion($id, $id_liquidacion, $no_factura, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $p_unitario, $total_factura, $estado, $rutas_archivos = '[]') {
        try {
            $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET id_liquidacion = ?, no_factura = ?, nombre_proveedor = ?, fecha = ?, bien_servicio = ?, t_gasto = ?, p_unitario = ?, total_factura = ?, estado = ?, rutas_archivos = ? WHERE id = ?");
            return $stmt->execute([$id_liquidacion, $no_factura, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $p_unitario, $total_factura, $estado, $rutas_archivos, $id]);
        } catch (PDOException $e) {
            error_log("Error al actualizar detalle de liquidación: " . $e->getMessage());
            return false;
        }
    }

    public function deleteDetalleLiquidacion($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM detalle_liquidaciones WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar detalle de liquidación: " . $e->getMessage());
            return false;
        }
    }

    public function updateEstado($id, $estado) {
        $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }
    
    public function getDetallesByLiquidacionId($id_liquidacion) {
        $stmt = $this->pdo->prepare("SELECT * FROM detalle_liquidaciones WHERE id_liquidacion = ?");
        $stmt->execute([$id_liquidacion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}