<?php
require_once '../config/database.php';

class CuentaContable {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllCuentasContables($estado = null) {
        $query = "SELECT * FROM cuentas_contables"; // Asegúrate de que la tabla sea 'cuentas_contables'
        if ($estado) {
            $query .= " WHERE estado = :estado";
        }
        $stmt = $this->pdo->prepare($query);
        if ($estado) {
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCuentaContableById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM cuentas_contables WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCuentaContable($nombre, $descripcion, $estado) {
        $stmt = $this->pdo->prepare("INSERT INTO cuentas_contables (nombre, descripcion, estado) VALUES (?, ?, ?)");
        return $stmt->execute([$nombre, $descripcion, $estado]);
    }

    public function updateCuentaContable($id, $nombre, $descripcion, $estado) {
        $stmt = $this->pdo->prepare("UPDATE cuentas_contables SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$nombre, $descripcion, $estado, $id]);
    }

    public function deleteCuentaContable($id) {
        $stmt = $this->pdo->prepare("UPDATE cuentas_contables SET estado = 'INACTIVO' WHERE id = ?");
        return $stmt->execute([$id]);
    }
}