<?php
require_once '../config/database.php';

class Liquidacion {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllLiquidaciones() {
        $stmt = $this->pdo->query("SELECT l.*, cc.nombre AS nombre_caja_chica 
                                 FROM liquidaciones l 
                                 JOIN cajas_chicas cc ON l.id_caja_chica = cc.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLiquidacionById($id) {
        $stmt = $this->pdo->prepare("SELECT l.*, cc.nombre AS nombre_caja_chica 
                                   FROM liquidaciones l 
                                   JOIN cajas_chicas cc ON l.id_caja_chica = cc.id 
                                   WHERE l.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createLiquidacion($id_caja_chica, $fecha_creacion, $monto_total) {
        $stmt = $this->pdo->prepare("INSERT INTO liquidaciones (id_caja_chica, fecha_creacion, monto_total) VALUES (?, ?, ?)");
        return $stmt->execute([$id_caja_chica, $fecha_creacion, $monto_total]);
    }

    public function updateLiquidacion($id, $id_caja_chica, $fecha_creacion, $monto_total, $estado) {
        $stmt = $this->pdo->prepare("UPDATE liquidaciones SET id_caja_chica = ?, fecha_creacion = ?, monto_total = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$id_caja_chica, $fecha_creacion, $monto_total, $estado, $id]);
    }

    public function deleteLiquidacion($id) {
        $stmt = $this->pdo->prepare("DELETE FROM liquidaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }
}