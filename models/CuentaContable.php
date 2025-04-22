<?php
require_once '../config/database.php';

class CuentaContable {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllCuentasContables($estado = null) {
        try {
            $query = "SELECT * FROM cuentas_contables";
            if ($estado) {
                $query .= " WHERE estado = :estado";
            }
            $stmt = $this->pdo->prepare($query);
            if ($estado) {
                $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllCuentasContables: " . $e->getMessage());
            return [];
        }
    }

    public function getCuentasByCentroCosto($id_centro_costo, $estado = 'ACTIVO') {
        try {
            $query = "SELECT * FROM cuentas_contables WHERE id_centro_costo = :id_centro_costo";
            if ($estado) {
                $query .= " AND estado = :estado";
            }
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id_centro_costo', $id_centro_costo, PDO::PARAM_INT);
            if ($estado) {
                $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCuentasByCentroCosto: " . $e->getMessage());
            return [];
        }
    }

    public function getCuentaContableById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM cuentas_contables WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCuentaContableById: " . $e->getMessage());
            return null;
        }
    }

    public function createCuentaContable($nombre, $descripcion, $estado) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO cuentas_contables (nombre, descripcion, estado) VALUES (?, ?, ?)");
            return $stmt->execute([$nombre, $descripcion, $estado]);
        } catch (PDOException $e) {
            error_log("Error in createCuentaContable: " . $e->getMessage());
            return false;
        }
    }

    public function updateCuentaContable($id, $nombre, $descripcion, $estado) {
        try {
            $stmt = $this->pdo->prepare("UPDATE cuentas_contables SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?");
            return $stmt->execute([$nombre, $descripcion, $estado, $id]);
        } catch (PDOException $e) {
            error_log("Error in updateCuentaContable: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCuentaContable($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE cuentas_contables SET estado = 'INACTIVO' WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in deleteCuentaContable: " . $e->getMessage());
            return false;
        }
    }
}