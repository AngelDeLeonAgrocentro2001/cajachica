<?php
require_once '../config/database.php';

class CuentaContable {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
        // Asegurar que PDO use UTF-8
        $this->pdo->exec("SET NAMES 'utf8'");
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
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Registro de los datos sin procesar para depurar codificación
            error_log("Cuentas contables obtenidas: " . print_r($results, true));
            return $results;
        } catch (PDOException $e) {
            error_log("Error en getAllCuentasContables: " . $e->getMessage());
            return [];
        }
    }

    public function getCuentasByCentroCosto($id_centro_costo, $estado = 'ACTIVO') {
        try {
            $query = "SELECT * FROM cuentas_contables WHERE id_centro_costo = :id_centro_costo AND estado = :estado";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id_centro_costo', $id_centro_costo, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Cuentas obtenidas por centro de costo ($id_centro_costo): " . print_r($results, true));
            return $results;
        } catch (PDOException $e) {
            error_log("Error en getCuentasByCentroCosto: " . $e->getMessage());
            return [];
        }
    }

    public function getCuentaContableById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM cuentas_contables WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Cuenta obtenida por ID ($id): " . print_r($result, true));
            return $result;
        } catch (PDOException $e) {
            error_log("Error en getCuentaContableById: " . $e->getMessage());
            return null;
        }
    }

    public function createCuentaContable($nombre, $descripcion, $estado) {
        try {
            // Asegurar que la entrada esté codificada en UTF-8
            $nombre = mb_convert_encoding($nombre, 'UTF-8', mb_detect_encoding($nombre));
            $descripcion = mb_convert_encoding($descripcion, 'UTF-8', mb_detect_encoding($descripcion));
            $estado = mb_convert_encoding($estado, 'UTF-8', mb_detect_encoding($estado));
            error_log("Creando cuenta contable: nombre=$nombre, descripción=$descripcion, estado=$estado");
            $stmt = $this->pdo->prepare("INSERT INTO cuentas_contables (nombre, descripcion, estado) VALUES (?, ?, ?)");
            return $stmt->execute([$nombre, $descripcion, $estado]);
        } catch (PDOException $e) {
            error_log("Error en createCuentaContable: " . $e->getMessage());
            return false;
        }
    }

    public function updateCuentaContable($id, $nombre, $descripcion, $estado) {
        try {
            // Asegurar que la entrada esté codificada en UTF-8
            $nombre = mb_convert_encoding($nombre, 'UTF-8', mb_detect_encoding($nombre));
            $descripcion = mb_convert_encoding($descripcion, 'UTF-8', mb_detect_encoding($descripcion));
            $estado = mb_convert_encoding($estado, 'UTF-8', mb_detect_encoding($estado));
            error_log("Actualizando cuenta contable (ID $id): nombre=$nombre, descripción=$descripcion, estado=$estado");
            $stmt = $this->pdo->prepare("UPDATE cuentas_contables SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?");
            return $stmt->execute([$nombre, $descripcion, $estado, $id]);
        } catch (PDOException $e) {
            error_log("Error en updateCuentaContable: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCuentaContable($id) {
        try {
            error_log("Eliminando cuenta contable (ID $id)");
            $stmt = $this->pdo->prepare("UPDATE cuentas_contables SET estado = 'INACTIVO' WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en deleteCuentaContable: " . $e->getMessage());
            return false;
        }
    }
}