<?php
require_once '../config/database.php';

class TipoGasto {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllTiposGastos() {
        $stmt = $this->pdo->query("
            SELECT tg.*, i.nombre AS impuesto_nombre, cc.nombre AS cuenta_contable_nombre
            FROM tipos_gastos tg
            LEFT JOIN impuestos i ON tg.impuesto_id = i.id
            LEFT JOIN cuentas_contables cc ON tg.cuenta_contable_id = cc.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTipoGastoById($id) {
        $stmt = $this->pdo->prepare("
            SELECT tg.*, i.nombre AS impuesto_nombre, cc.nombre AS cuenta_contable_nombre
            FROM tipos_gastos tg
            LEFT JOIN impuestos i ON tg.impuesto_id = i.id
            LEFT JOIN cuentas_contables cc ON tg.cuenta_contable_id = cc.id
            WHERE tg.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTipoGastoByName($name, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT * FROM tipos_gastos WHERE name = ? AND id != ?");
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM tipos_gastos WHERE name = ?");
            $stmt->execute([$name]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTipoGasto($name, $description, $impuesto_id, $cuenta_contable_id, $estado = 'ACTIVO') {
        $stmt = $this->pdo->prepare("
            INSERT INTO tipos_gastos (name, description, impuesto_id, cuenta_contable_id, estado)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $description, $impuesto_id, $cuenta_contable_id, $estado]);
    }

    public function updateTipoGasto($id, $name, $description, $impuesto_id, $cuenta_contable_id, $estado) {
        $stmt = $this->pdo->prepare("
            UPDATE tipos_gastos
            SET name = ?, description = ?, impuesto_id = ?, cuenta_contable_id = ?, estado = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $impuesto_id, $cuenta_contable_id, $estado, $id]);
    }

    public function deleteTipoGasto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tipos_gastos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}