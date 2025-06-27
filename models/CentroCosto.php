<?php
require_once '../config/database.php';

class CentroCosto {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllCentrosCostos($searchTerm = '', $baseId = null) {
        try {
            $query = "SELECT * FROM centros_costos WHERE estado = 'ACTIVO'";
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
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Centros de costos obtenidos: " . print_r($results, true));
            return $results;
        } catch (PDOException $e) {
            error_log("Error en getAllCentrosCostos: " . $e->getMessage());
            return [];
        }
    }

    public function getCentroCostoById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM centros_costos WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Resultado de getCentroCostoById para ID $id: " . print_r($result, true));
            return $result;
        } catch (PDOException $e) {
            error_log("Error en getCentroCostoById: " . $e->getMessage());
            return false;
        }
    }

    public function createCentroCosto($data) {
        $stmt = $this->pdo->prepare("INSERT INTO centros_costos (codigo, nombre, tipo, estado, base_id) VALUES (?, ?, ?, ?, ?)");
        $estado = isset($data['estado']) && $data['estado'] === 'Y' ? 'ACTIVO' : 'INACTIVO';
        $base_id = isset($data['base_id']) ? $data['base_id'] : null;
        return $stmt->execute([$data['codigo'], $data['nombre'], $data['tipo'] ?? '5', $estado, $base_id]);
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function updateCentroCosto($id, $nombre, $estado, $tipo = null) {
        $estado = $estado === 'Y' ? 'ACTIVO' : 'INACTIVO';
        $stmt = $this->pdo->prepare("UPDATE centros_costos SET nombre = ?, estado = ?, tipo = ? WHERE id = ?");
        return $stmt->execute([$nombre, $estado, $tipo ?? '5', $id]);
    }

    public function deleteCentroCosto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM centros_costos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function checkCodigoExists($codigo) {
        try {
            $codigo = trim($codigo);
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM centros_costos WHERE TRIM(codigo) = ?");
            $stmt->execute([$codigo]);
            $count = $stmt->fetchColumn();
            error_log("Resultado de SELECT COUNT(*) para código '$codigo': $count");
            return $count > 0;
        } catch (PDOException $e) {
            error_log('Error en CentroCosto.php: ' . $e->getMessage());
            throw new Exception('Error al verificar el código: ' . $e->getMessage());
        }
    }
}