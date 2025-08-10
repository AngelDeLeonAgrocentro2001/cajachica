<?php
require_once '../config/database.php';

class Auditoria {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createAuditoria($id_liquidacion, $id_detalle_liquidacion, $id_usuario, $tipo_accion, $detalles) {
        try {
            if (empty($tipo_accion)) {
                throw new Exception("El tipo de acción es obligatorio.");
            }

            // Validate that the user exists
            $stmt = $this->pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
            $stmt->execute([$id_usuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$usuario) {
                throw new Exception("Usuario con ID $id_usuario no encontrado.");
            }
            $usuario_nombre = $usuario['nombre'];

            $accion = $this->mapTipoAccionToAccion($tipo_accion);

            $stmt = $this->pdo->prepare("
                INSERT INTO auditoria (id_liquidacion, id_detalle_liquidacion, id_usuario, usuario_nombre, accion, tipo_accion, detalles, fecha)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $result = $stmt->execute([
                $id_liquidacion,
                $id_detalle_liquidacion,
                $id_usuario,
                $usuario_nombre,
                $accion,
                $tipo_accion,
                $detalles
            ]);
            if (!$result) {
                error_log("Error al insertar en auditoria: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Error al insertar en auditoria: " . implode(', ', $stmt->errorInfo()));
            }
            return $result;
        } catch (Exception $e) {
            error_log("Error al crear auditoría: " . $e->getMessage());
            throw $e;
        }
    }

    private function mapTipoAccionToAccion($tipo_accion) {
        $mapping = [
            'CREADO' => 'APROBADO',
            'ACTUALIZADO' => 'APROBADO',
            'ELIMINADO' => 'RECHAZADO',
            'AUTORIZADO_POR_SUPERVISOR' => 'APROBADO',
            'RECHAZADO_POR_SUPERVISOR' => 'RECHAZADO',
            'AUTORIZADO_POR_CONTABILIDAD' => 'APROBADO',
            'RECHAZADO_POR_CONTABILIDAD' => 'RECHAZADO',
            'DESCARTADO' => 'RECHAZADO',
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
            'RECHAZAR_FACTURA_CONTABILIDAD' => 'RECHAZADO',
            'FINALIZADO' => 'APROBADO'
        ];

        return $mapping[$tipo_accion] ?? 'RECHAZADO';
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
?>