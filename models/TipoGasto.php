<?php
require_once '../config/database.php';

class TipoGasto {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllTiposGastos() {
        $stmt = $this->pdo->query("SELECT * FROM tipos_gastos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTipoGastoById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tipos_gastos WHERE id = ?");
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

    public function createTipoGasto($name, $description, $estado = 'ACTIVO') {
        $stmt = $this->pdo->prepare("INSERT INTO tipos_gastos (name, description, estado) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $description, $estado]);
    }

    public function updateTipoGasto($id, $name, $description, $estado) {
        $stmt = $this->pdo->prepare("UPDATE tipos_gastos SET name = ?, description = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $estado, $id]);
    }

    public function deleteTipoGasto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tipos_gastos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}