<?php
require_once '../models/Liquidacion.php';
require_once '../models/DetalleLiquidacion.php';
require_once '../models/CajaChica.php';
require_once '../models/Usuario.php';

class ReportesController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function list() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en ReportesController::list');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        $allowedRoles = [Usuario::ROL_ADMIN, Usuario::ROL_CONTABILIDAD, Usuario::ROL_SUPERVISOR];
        if (!in_array($usuario['rol'], $allowedRoles)) {
            error_log('Error: No tienes permiso para generar reportes');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para generar reportes']);
            exit;
        }

        // Obtener datos para los filtros
        $cajaChica = new CajaChica();
        $cajasChicas = $cajaChica->getAllCajasChicas();
        $selectCajasChicas = '<option value="">Todas</option>';
        foreach ($cajasChicas as $cc) {
            $selectCajasChicas .= "<option value='{$cc['id']}'>{$cc['nombre']}</option>";
        }

        $estados = [
            'PENDIENTE' => 'Pendiente',
            'AUTORIZADO_POR_SUPERVISOR' => 'Autorizado por Supervisor',
            'RECHAZADO_POR_SUPERVISOR' => 'Rechazado por Supervisor',
            'AUTORIZADO_POR_CONTABILIDAD' => 'Autorizado por Contabilidad',
            'RECHAZADO_POR_CONTABILIDAD' => 'Rechazado por Contabilidad',
            'PENDIENTE_CORRECCIÓN' => 'Pendiente de Corrección'
        ];
        $selectEstados = '<option value="">Todos</option>';
        foreach ($estados as $key => $value) {
            $selectEstados .= "<option value='{$key}'>{$value}</option>";
        }

        ob_start();
        require '../views/reportes/list.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_cajas_chicas}}', $selectCajasChicas, $html);
        $html = str_replace('{{select_estados}}', $selectEstados, $html);
        echo $html;
        exit;
    }

    public function generarResumen() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en ReportesController::generarResumen');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        $allowedRoles = [Usuario::ROL_ADMIN, Usuario::ROL_CONTABILIDAD, Usuario::ROL_SUPERVISOR];
        if (!in_array($usuario['rol'], $allowedRoles)) {
            error_log('Error: No tienes permiso para generar reportes');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para generar reportes']);
            exit;
        }
    
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        $idCajaChica = $_GET['id_caja_chica'] ?? '';
    
        $sql = "SELECT estado, COUNT(*) as cantidad, SUM(monto_total) as monto_total 
                FROM liquidaciones 
                WHERE 1=1";
        $params = [];
    
        if (!empty($fechaInicio)) {
            $sql .= " AND fecha_creacion >= ?";
            $params[] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND fecha_creacion <= ?";
            $params[] = $fechaFin;
        }
        if (!empty($idCajaChica)) {
            $sql .= " AND id_caja_chica = ?";
            $params[] = $idCajaChica;
        }
    
        $sql .= " GROUP BY estado";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $filename = "reporte_resumen_liquidaciones_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Estado', 'Cantidad', 'MontoTotal']);
    
        foreach ($reporte as $row) {
            fputcsv($output, [
                $row['estado'],
                $row['cantidad'],
                $row['monto_total']
            ]);
        }
    
        fclose($output);
    
        $auditoria = new Auditoria();
        $auditoria->createAuditoria(null, null, $_SESSION['user_id'], 'REPORTE_GENERADO', 'Reporte de resumen generado: ' . $filename);
    
        exit;
    }
    
    public function generarDetalle() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en ReportesController::generarDetalle');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        $allowedRoles = [Usuario::ROL_ADMIN, Usuario::ROL_CONTABILIDAD, Usuario::ROL_SUPERVISOR];
        if (!in_array($usuario['rol'], $allowedRoles)) {
            error_log('Error: No tienes permiso para generar reportes');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para generar reportes']);
            exit;
        }
    
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        $idCajaChica = $_GET['id_caja_chica'] ?? '';
        $estado = $_GET['estado'] ?? '';
    
        $sql = "SELECT l.*, c.nombre AS nombre_caja_chica 
                FROM liquidaciones l 
                LEFT JOIN cajas_chicas c ON l.id_caja_chica = c.id 
                WHERE 1=1";
        $params = [];
    
        if (!empty($fechaInicio)) {
            $sql .= " AND l.fecha_creacion >= ?";
            $params[] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND l.fecha_creacion <= ?";
            $params[] = $fechaFin;
        }
        if (!empty($idCajaChica)) {
            $sql .= " AND l.id_caja_chica = ?";
            $params[] = $idCajaChica;
        }
        if (!empty($estado)) {
            $sql .= " AND l.estado = ?";
            $params[] = $estado;
        }
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $liquidaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $filename = "reporte_detalle_liquidaciones_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    
        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Liquidacion_ID', 'Caja_Chica', 'Fecha_Creacion', 'Monto_Total', 'Estado_Liquidacion', 
            'Exportado', 'Detalle_ID', 'Numero_Factura', 'Proveedor', 'Fecha_Detalle', 
            'Bien_Servicio', 'Tipo_Gasto', 'Precio_Unitario', 'Total_Factura', 'Estado_Detalle'
        ]);
    
        $detalleModel = new DetalleLiquidacion();
        foreach ($liquidaciones as $liquidacion) {
            $detalles = $detalleModel->getDetallesByLiquidacionId($liquidacion['id']);
            if (empty($detalles)) {
                fputcsv($output, [
                    $liquidacion['id'],
                    $liquidacion['nombre_caja_chica'],
                    $liquidacion['fecha_creacion'],
                    $liquidacion['monto_total'],
                    $liquidacion['estado'],
                    $liquidacion['exportado'],
                    '', '', '', '', '', '', '', ''
                ]);
            } else {
                foreach ($detalles as $detalle) {
                    fputcsv($output, [
                        $liquidacion['id'],
                        $liquidacion['nombre_caja_chica'],
                        $liquidacion['fecha_creacion'],
                        $liquidacion['monto_total'],
                        $liquidacion['estado'],
                        $liquidacion['exportado'],
                        $detalle['id'],
                        $detalle['no_factura'],
                        $detalle['nombre_proveedor'],
                        $detalle['fecha'],
                        $detalle['bien_servicio'],
                        $detalle['t_gasto'],
                        $detalle['p_unitario'],
                        $detalle['total_factura'],
                        $detalle['estado']
                    ]);
                }
            }
        }
    
        fclose($output);
    
        $auditoria = new Auditoria();
        $auditoria->createAuditoria(null, null, $_SESSION['user_id'], 'REPORTE_GENERADO', 'Reporte detallado generado: ' . $filename);
    
        exit;
    }
}