<?php
require_once '../config/database.php';

class Role {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllRoles() {
        $stmt = $this->pdo->query("SELECT * FROM roles");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRole($name, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
        return $stmt->execute([$name, $description]);
    }

    public function updateRole($id, $name, $description) {
        $stmt = $this->pdo->prepare("UPDATE roles SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $id]);
    }

    public function deleteRole($id) {
        $stmt = $this->pdo->prepare("DELETE FROM roles WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function assignRoleToUser($userId, $roleId) {
        $stmt = $this->pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $roleId]);
    }

    public function removeRoleFromUser($userId, $roleId) {
        $stmt = $this->pdo->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ?");
        return $stmt->execute([$userId, $roleId]);
    }
}