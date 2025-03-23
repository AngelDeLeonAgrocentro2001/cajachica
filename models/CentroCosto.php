<?php
require_once '../config/database.php';

class CentroCosto {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllCentrosCostos($estado = null) {
        $query = "SELECT * FROM centros_costos";
        if ($estado) {
            $query .= " WHERE estado = :estado";
        }
        $stmt = $this->pdo->prepare($query);
        if ($estado) {
            $stmt->execute([':estado' => $estado]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCentroCostoById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM centros_costos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCentroCosto($nombre, $descripcion, $estado) {
        $stmt = $this->pdo->prepare("INSERT INTO centros_costos (nombre, descripcion, estado) VALUES (?, ?, ?)");
        return $stmt->execute([$nombre, $descripcion, $estado]);
    }

    public function updateCentroCosto($id, $nombre, $descripcion, $estado) {
        $stmt = $this->pdo->prepare("UPDATE centros_costos SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$nombre, $descripcion, $estado, $id]);
    }

    public function deleteCentroCosto($id) {
        $stmt = $this->pdo->prepare("UPDATE centros_costos SET estado = 'INACTIVO' WHERE id = ?");
        return $stmt->execute([$id]);
    }
}