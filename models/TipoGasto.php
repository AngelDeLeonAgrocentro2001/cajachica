<?php
require_once '../config/database.php';

class TipoGasto {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllTiposGastos() {
        $stmt = $this->pdo->query("
            SELECT tg.*, cc.nombre AS cuenta_contable_nombre
            FROM tipos_gastos tg
            LEFT JOIN cuentas_contables cc ON tg.cuenta_contable_id = cc.id
        ");
        $tiposGastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch associated taxes for each tipo_gasto
        foreach ($tiposGastos as &$tipo) {
            $tipo['impuestos'] = $this->getImpuestosByTipoGastoId($tipo['id']);
        }
        return $tiposGastos;
    }

    public function getTipoGastoById($id) {
        $stmt = $this->pdo->prepare("
            SELECT tg.*, cc.nombre AS cuenta_contable_nombre
            FROM tipos_gastos tg
            LEFT JOIN cuentas_contables cc ON tg.cuenta_contable_id = cc.id
            WHERE tg.id = ?
        ");
        $stmt->execute([$id]);
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($tipo) {
            $tipo['impuestos'] = $this->getImpuestosByTipoGastoId($id);
        }
        return $tipo;
    }

    public function getTipoGastoByName($name, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("
                SELECT tg.*, cc.nombre AS cuenta_contable_nombre
                FROM tipos_gastos tg
                LEFT JOIN cuentas_contables cc ON tg.cuenta_contable_id = cc.id
                WHERE tg.name = ? AND tg.id != ?
            ");
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT tg.*, cc.nombre AS cuenta_contable_nombre
                FROM tipos_gastos tg
                LEFT JOIN cuentas_contables cc ON tg.cuenta_contable_id = cc.id
                WHERE tg.name = ?
            ");
            $stmt->execute([$name]);
        }
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($tipo) {
            $tipo['impuestos'] = $this->getImpuestosByTipoGastoId($tipo['id']);
        }
        return $tipo;
    }

    private function getImpuestosByTipoGastoId($tipo_gasto_id) {
        $stmt = $this->pdo->prepare("
            SELECT i.*
            FROM impuestos i
            INNER JOIN tipo_gasto_impuestos tgi ON i.id = tgi.impuesto_id
            WHERE tgi.tipo_gasto_id = ?
        ");
        $stmt->execute([$tipo_gasto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTipoGasto($name, $description, $cuenta_contable_id, $estado = 'ACTIVO') {
        $stmt = $this->pdo->prepare("
            INSERT INTO tipos_gastos (name, description, cuenta_contable_id, estado)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $description, $cuenta_contable_id, $estado]);
    }

    public function updateTipoGasto($id, $name, $description, $cuenta_contable_id, $estado) {
        $stmt = $this->pdo->prepare("
            UPDATE tipos_gastos
            SET name = ?, description = ?, cuenta_contable_id = ?, estado = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $cuenta_contable_id, $estado, $id]);
    }

    public function deleteTipoGasto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tipos_gastos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}