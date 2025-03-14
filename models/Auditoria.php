<?php
require_once '../config/database.php';

class Auditoria {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createAuditoria($id_liquidacion, $id_detalle_liquidacion, $id_usuario, $tipo_accion, $detalles) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO auditoria (id_liquidacion, id_detalle_liquidacion, id_usuario, usuario_nombre, tipo_accion, detalles, fecha)
                SELECT ?, ?, ?, nombre, ?, ?, NOW()
                FROM usuarios
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $id_liquidacion,
                $id_detalle_liquidacion,
                $id_usuario,
                $tipo_accion,
                $detalles,
                $id_usuario
            ]);
            if (!$result) {
                error_log("Error al insertar en auditoria: " . print_r($stmt->errorInfo(), true));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error PDO al insertar en auditoria: " . $e->getMessage());
            return false;
        }
    }

    public function getAuditoria($filters = []) {
        $sql = "
            SELECT a.*, u.nombre AS usuario_nombre 
            FROM auditoria a 
            LEFT JOIN usuarios u ON a.id_usuario = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['id_usuario'])) {
            $sql .= " AND a.id_usuario = ?";
            $params[] = $filters['id_usuario'];
        }
        if (!empty($filters['tipo_accion'])) {
            $sql .= " AND a.tipo_accion = ?";
            $params[] = $filters['tipo_accion'];
        }
        if (!empty($filters['fecha_inicio'])) {
            $sql .= " AND a.fecha >= ?";
            $params[] = $filters['fecha_inicio'];
        }
        if (!empty($filters['fecha_fin'])) {
            $sql .= " AND a.fecha <= ?";
            $params[] = $filters['fecha_fin'];
        }

        $sql .= " ORDER BY a.fecha DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}