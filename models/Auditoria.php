<?php
require_once '../config/database.php';

class Auditoria {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createAuditoria($id_liquidacion, $id_detalle_liquidacion, $id_usuario, $tipo_accion, $detalles) {
        try {
            // Validar que tipo_accion no esté vacío
            if (empty($tipo_accion)) {
                throw new Exception("El tipo de acción es obligatorio.");
            }
    
            // Mapear tipo_accion a un valor permitido para la columna accion
            $accion = $this->mapTipoAccionToAccion($tipo_accion);
    
            $stmt = $this->pdo->prepare("
                INSERT INTO auditoria (id_liquidacion, id_detalle_liquidacion, id_usuario, usuario_nombre, accion, tipo_accion, detalles, fecha)
                SELECT ?, ?, ?, nombre, ?, ?, ?, NOW()
                FROM usuarios
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $id_liquidacion,
                $id_detalle_liquidacion,
                $id_usuario,
                $accion, // Usar el valor mapeado para la columna accion
                $tipo_accion, // Usar tipo_accion directamente para la columna tipo_accion
                $detalles,
                $id_usuario
            ]);
            if (!$result) {
                error_log("Error al insertar en auditoria: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Error al insertar en auditoria: " . print_r($stmt->errorInfo(), true));
            }
            return $result;
        } catch (Exception $e) {
            error_log("Error al crear auditoría: " . $e->getMessage());
            throw $e; // Relanzar la excepción para que sea capturada por el controlador
        }
    }
    
    private function mapTipoAccionToAccion($tipo_accion) {
        // Mapear tipo_accion a un valor permitido para la columna accion
        $mapping = [
            'CREADO' => 'APROBADO',
            'ACTUALIZADO' => 'APROBADO',
            'ELIMINADO' => 'RECHAZADO',
            'AUTORIZADO_POR_SUPERVISOR' => 'APROBADO',
            'RECHAZADO_POR_SUPERVISOR' => 'RECHAZADO',
            'AUTORIZADO_POR_CONTABILIDAD' => 'APROBADO',
            'RECHAZADO_POR_CONTABILIDAD' => 'RECHAZADO',
            'DESCARTADO' => 'RECHAZADO',
            'PENDIENTE_CORRECCIÓN' => 'RECHAZADO',
            'EXPORTADO' => 'EXPORTADO_SAP',
            'REPORTE_GENERADO' => 'EXPORTADO_SAP',
            'CREAR_USUARIO' => 'APROBADO',
            'ACTUALIZAR_USUARIO' => 'APROBADO',
            'ELIMINAR_USUARIO' => 'RECHAZADO',
            'CREAR_FACTURA' => 'APROBADO',
            'ACTUALIZAR_FACTURA' => 'APROBADO',
            'ELIMINAR_FACTURA' => 'RECHAZADO',
            'AUTORIZAR_FACTURA' => 'APROBADO',
            'RECHAZAR_FACTURA' => 'RECHAZADO',
            'PAGAR_FACTURA' => 'APROBADO',
            'RECHAZAR_FACTURA_CONTABILIDAD' => 'RECHAZADO'
        ];
    
        return $mapping[$tipo_accion] ?? 'RECHAZADO'; // Valor por defecto si no se encuentra en el mapeo
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