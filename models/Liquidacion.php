<?php
require_once '../config/database.php';

class Liquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllLiquidaciones() {
        $stmt = $this->pdo->query("
            SELECT l.*, c.nombre AS nombre_caja_chica 
            FROM liquidaciones l 
            LEFT JOIN cajas_chicas c ON l.id_caja_chica = c.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLiquidacionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT l.*, c.nombre AS nombre_caja_chica 
            FROM liquidaciones l 
            LEFT JOIN cajas_chicas c ON l.id_caja_chica = c.id 
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createLiquidacion($id_caja_chica, $fecha_creacion, $monto_total, $estado) {
        $stmt = $this->pdo->prepare("
            INSERT INTO liquidaciones (id_caja_chica, fecha_creacion, monto_total, estado, exportado) 
            VALUES (?, ?, ?, ?, 0)
        ");
        return $stmt->execute([$id_caja_chica, $fecha_creacion, $monto_total, $estado]);
    }

    public function updateLiquidacion($id, $id_caja_chica, $fecha_creacion, $monto_total, $estado) {
        $stmt = $this->pdo->prepare("
            UPDATE liquidaciones 
            SET id_caja_chica = ?, fecha_creacion = ?, monto_total = ?, estado = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$id_caja_chica, $fecha_creacion, $monto_total, $estado, $id]);
    }

    public function deleteLiquidacion($id) {
        $stmt = $this->pdo->prepare("DELETE FROM liquidaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateEstado($id, $estado) {
        $stmt = $this->pdo->prepare("UPDATE liquidaciones SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }

    // Nuevo método para marcar una liquidación como exportada
    public function markAsExported($id) {
        $stmt = $this->pdo->prepare("UPDATE liquidaciones SET exportado = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}