<?php
require_once '../config/database.php';

class CuentaContable {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllCuentas($searchTerm = '', $baseId = null) {
        $query = "SELECT * FROM cuentas_contables WHERE 1=1";
        $params = [];
    
        if ($searchTerm) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%$searchTerm%";
        }
    
        if ($baseId !== null) {
            $query .= " AND base_id = ?";
            $params[] = $baseId;
        }
    
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCuentaById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM cuentas_contables WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Resultado de getCuentaById para ID $id: " . print_r($result, true)); // Depuración
            return $result;
        } catch (PDOException $e) {
            error_log("Error en getCuentaById: " . $e->getMessage()); // Depuración
            return false;
        }
    }

    public function createCuenta($data) {
        $stmt = $this->pdo->prepare("INSERT INTO cuentas_contables (codigo, nombre, tipo, estado, base_id) VALUES (?, ?, ?, ?, ?)");
        $estado = isset($data['estado']) && $data['estado'] === 'Y' ? 'ACTIVO' : 'INACTIVO';
        $base_id = isset($data['base_id']) ? $data['base_id'] : null; // Obtener base_id del formulario
        return $stmt->execute([$data['codigo'], $data['nombre'], $data['tipo'] ?? '5', $estado, $base_id]);
    }

    public function getPdo() {
        return $this->pdo; // Método para acceder a PDO desde el controlador
    }

    public function updateCuenta($id, $nombre, $estado, $tipo = null) {
        $estado = $estado === 'Y' ? 'ACTIVO' : 'INACTIVO';
        $stmt = $this->pdo->prepare("UPDATE cuentas_contables SET nombre = ?, estado = ?, tipo = ? WHERE id = ?");
        return $stmt->execute([$nombre, $estado, $tipo ?? '5', $id]);
    }

    public function deleteCuenta($id) {
        $stmt = $this->pdo->prepare("DELETE FROM cuentas_contables WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function checkCodigoExists($codigo) {
        try {
            $codigo = trim($codigo); // Eliminar espacios al inicio y al final
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM cuentas_contables WHERE TRIM(codigo) = ?");
            $stmt->execute([$codigo]);
            $count = $stmt->fetchColumn();
            error_log("Resultado de SELECT COUNT(*) para código '$codigo': $count"); // Depuración
            return $count > 0;
        } catch (PDOException $e) {
            error_log('Error en CuentaContable.php: ' . $e->getMessage());
            throw new Exception('Error al verificar el código: ' . $e->getMessage());
        }
    }
}