<?php
require_once '../config/database.php';

class Impuesto {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllImpuestos() {
        $stmt = $this->pdo->query("SELECT * FROM impuestos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getImpuestoById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM impuestos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createImpuesto($nombre, $porcentaje, $estado) {
        $stmt = $this->pdo->prepare("INSERT INTO impuestos (nombre, porcentaje, estado) VALUES (?, ?, ?)");
        return $stmt->execute([$nombre, $porcentaje, $estado]);
    }

    public function updateImpuesto($id, $nombre, $porcentaje, $estado) {
        $stmt = $this->pdo->prepare("UPDATE impuestos SET nombre = ?, porcentaje = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$nombre, $porcentaje, $estado, $id]);
    }

    public function deleteImpuesto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM impuestos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}