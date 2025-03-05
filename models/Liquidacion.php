<?php
require_once '../config/database.php';

class Liquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllLiquidaciones() {
        $stmt = $this->pdo->query("
            SELECT l.*, cc.nombre as caja_chica
            FROM liquidaciones l
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLiquidacionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT l.*, cc.nombre as caja_chica
            FROM liquidaciones l
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createLiquidacion($idCajaChica, $fechaCreacion, $montoTotal, $estado) {
        $stmt = $this->pdo->prepare("
            INSERT INTO liquidaciones (id_caja_chica, fecha_creacion, monto_total, estado)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$idCajaChica, $fechaCreacion, $montoTotal, $estado]);
    }

    public function updateLiquidacion($id, $idCajaChica, $fechaCreacion, $montoTotal, $estado) {
        $stmt = $this->pdo->prepare("
            UPDATE liquidaciones
            SET id_caja_chica = ?, fecha_creacion = ?, monto_total = ?, estado = ?
            WHERE id = ?
        ");
        return $stmt->execute([$idCajaChica, $fechaCreacion, $montoTotal, $estado, $id]);
    }

    public function deleteLiquidacion($id) {
        $stmt = $this->pdo->prepare("DELETE FROM liquidaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function exportarLiquidacion($id) {
        $stmt = $this->pdo->prepare("UPDATE liquidaciones SET exportado = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Nuevo método para el reporte de resumen
    public function getLiquidacionesByFecha($fechaInicio, $fechaFin, $idCajaChica = null) {
        $query = "
            SELECT l.*, cc.nombre as caja_chica, SUM(dl.total_factura) as total_gastos
            FROM liquidaciones l
            LEFT JOIN detalle_liquidaciones dl ON l.id = dl.id_liquidacion
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE l.fecha_creacion BETWEEN ? AND ?
        ";
        $params = [$fechaInicio, $fechaFin];

        if (!empty($idCajaChica)) {
            $query .= " AND l.id_caja_chica = ?";
            $params[] = $idCajaChica;
        }

        $query .= " GROUP BY l.id";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}