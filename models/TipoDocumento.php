<?php
require_once '../config/database.php';

class TipoDocumento {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllTiposDocumentos() {
        $stmt = $this->pdo->query("SELECT * FROM tipos_documentos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTipoDocumentoById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tipos_documentos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTipoDocumentoByName($name, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT * FROM tipos_documentos WHERE name = ? AND id != ?");
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM tipos_documentos WHERE name = ?");
            $stmt->execute([$name]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTipoDocumento($name, $description, $estado = 'ACTIVO') {
        $stmt = $this->pdo->prepare("INSERT INTO tipos_documentos (name, description, estado) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $description, $estado]);
    }

    public function updateTipoDocumento($id, $name, $description, $estado) {
        $stmt = $this->pdo->prepare("UPDATE tipos_documentos SET name = ?, description = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $estado, $id]);
    }

    public function deleteTipoDocumento($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tipos_documentos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}