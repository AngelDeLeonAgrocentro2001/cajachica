<?php
require_once '../config/database.php';

class TipoGasto {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
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

    public function createTipoGasto($name, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO tipos_gastos (name, description) VALUES (?, ?)");
        return $stmt->execute([$name, $description]);
    }

    public function updateTipoGasto($id, $name, $description) {
        $stmt = $this->pdo->prepare("UPDATE tipos_gastos SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $id]);
    }

    public function deleteTipoGasto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tipos_gastos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}