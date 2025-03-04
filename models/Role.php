<?php
require_once '../config/database.php';

class Role {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllRoles() {
        $stmt = $this->pdo->query("SELECT * FROM roles");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRolById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createRol($nombre, $descripcion, $estado) {
        $stmt = $this->pdo->prepare("INSERT INTO roles (nombre, descripcion, estado) VALUES (?, ?, ?)");
        return $stmt->execute([$nombre, $descripcion, $estado]);
    }

    public function updateRol($id, $nombre, $descripcion, $estado) {
        $stmt = $this->pdo->prepare("UPDATE roles SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$nombre, $descripcion, $estado, $id]);
    }

    public function deleteRol($id) {
        // Verificar si el rol está siendo utilizado por algún usuario
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id_rol = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            return false; // No se puede eliminar un rol que está en uso
        }

        $stmt = $this->pdo->prepare("DELETE FROM roles WHERE id = ?");
        return $stmt->execute([$id]);
    }
}