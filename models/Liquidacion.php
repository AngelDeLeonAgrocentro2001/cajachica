<?php
require_once '../config/database.php';

class Liquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllLiquidaciones() {
        $stmt = $this->pdo->query("
            SELECT l.*, 
                   cc.nombre AS nombre_caja_chica
            FROM liquidaciones l
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            ORDER BY l.fecha_creacion DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLiquidacionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT l.*, 
                   cc.nombre AS nombre_caja_chica
            FROM liquidaciones l
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createLiquidacion($idCajaChica, $fechaCreacion, $fechaInicio, $fechaFin, $montoTotal, $estado) {
        $stmt = $this->pdo->prepare("
            INSERT INTO liquidaciones (id_caja_chica, fecha_creacion, fecha_inicio, fecha_fin, monto_total, estado)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$idCajaChica, $fechaCreacion, $fechaInicio, $fechaFin, $montoTotal, $estado]);
    }

    public function updateLiquidacion($id, $idCajaChica, $fechaCreacion, $fechaInicio, $fechaFin, $montoTotal, $estado) {
        $stmt = $this->pdo->prepare("
            UPDATE liquidaciones
            SET id_caja_chica = ?, fecha_creacion = ?, fecha_inicio = ?, fecha_fin = ?, monto_total = ?, estado = ?
            WHERE id = ?
        ");
        return $stmt->execute([$idCajaChica, $fechaCreacion, $fechaInicio, $fechaFin, $montoTotal, $estado, $id]);
    }

    public function deleteLiquidation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM liquidaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function markAsExported($id) {
        $stmt = $this->pdo->prepare("UPDATE liquidaciones SET exportado = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateEstado($id, $estado) {
        try {
            $stmt = $this->pdo->prepare("UPDATE liquidaciones SET estado = ? WHERE id = ?");
            $result = $stmt->execute([$estado, $id]);
            if ($result === false) {
                error_log("Error al ejecutar UPDATE en updateEstado: " . implode(', ', $stmt->errorInfo()));
                return false;
            }
            $rowCount = $stmt->rowCount();
            error_log("updateEstado ejecutado - ID: $id, Estado: $estado, Filas afectadas: $rowCount");
            return $rowCount > 0;
        } catch (PDOException $e) {
            error_log("Error PDO en updateEstado: " . $e->getMessage());
            return false;
        }
    }

    public function getLiquidacionesByFecha($fechaInicio, $fechaFin, $idCajaChica = null) {
        $query = "
            SELECT l.*, cc.nombre as caja_chica, SUM(dl.total_factura) as total_gastos
            FROM liquidaciones l
            LEFT JOIN detalle_liquidaciones dl ON l.id = dl.id_liquidacion
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE l.fecha_inicio >= ? AND l.fecha_fin <= ?
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

    public function updateMontoTotal($id, $montoTotal) {
        $stmt = $this->pdo->prepare("UPDATE liquidaciones SET monto_total = ? WHERE id = ?");
        return $stmt->execute([$montoTotal, $id]);
    }
}