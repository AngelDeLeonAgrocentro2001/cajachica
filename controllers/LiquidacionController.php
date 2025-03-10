<?php
require_once '../models/Liquidacion.php';
require_once '../models/CajaChica.php';
require_once '../models/DetalleLiquidacion.php';
require_once '../models/Auditoria.php';

class LiquidacionController {
    private $pdo;
    private $liquidacionModel; // Declarar la propiedad
    private $detalleLiquidacionModel; // Declarar la propiedad
    private $auditoriaModel;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
        $this->liquidacionModel = new Liquidacion();
        $this->detalleLiquidacionModel = new DetalleLiquidacion();
        $this->auditoriaModel = new Auditoria();
    }

    public function listLiquidaciones() {
        error_log('Iniciando listLiquidaciones');
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        $liquidacion = new Liquidacion();
        $liquidaciones = $liquidacion->getAllLiquidaciones();
        error_log('Liquidaciones obtenidas: ' . print_r($liquidaciones, true));
    
        // Filtrar liquidaciones en modo revisar
        $urlParams = $_GET['mode'] ?? '';
        $isRevisarMode = $urlParams === 'revisar';
        if ($isRevisarMode) {
            $liquidaciones = array_filter($liquidaciones, function($liquidacion) {
                return $liquidacion['estado'] !== 'PENDIENTE_CORRECCIÓN';
            });
        }
    
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(array_values($liquidaciones));
        } else {
            require '../views/liquidaciones/list.html';
        }
        exit;
    }

    public function createLiquidacion() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en createLiquidacion');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log('Error: No tienes permiso para crear liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear liquidaciones']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_caja_chica = $_POST['id_caja_chica'] ?? '';
                $fecha_creacion = $_POST['fecha_creacion'] ?? '';
                $monto_total = $_POST['monto_total'] ?? 0;
                $estado = $_POST['estado'] ?? 'PENDIENTE';
    
                if (empty($id_caja_chica) || empty($fecha_creacion) || !is_numeric($monto_total)) {
                    throw new Exception('Todos los campos son obligatorios y deben ser válidos.');
                }
    
                // Forzar el estado inicial a PENDIENTE si no se especifica
                $estado = !empty($estado) ? $estado : 'PENDIENTE';
    
                $liquidacion = new Liquidacion();
                if ($liquidacion->createLiquidacion($id_caja_chica, $fecha_creacion, $monto_total, $estado)) {
                    $lastInsertId = $this->pdo->lastInsertId();
                    $auditoria = new Auditoria();
                    $auditoria->createAuditoria($lastInsertId, null, $_SESSION['user_id'], 'CREADO', 'Liquidación creada por encargado');
                    header('Content-Type: application/json');
                    http_response_code(201);
                    echo json_encode(['message' => 'Liquidación creada']);
                } else {
                    throw new Exception('Error al crear liquidación en la base de datos.');
                }
            } catch (Exception $e) {
                error_log('Error en createLiquidacion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $cajaChica = new CajaChica();
        $cajasChicas = $cajaChica->getAllCajasChicas();
        $selectCajasChicas = '';
        if (empty($cajasChicas)) {
            $selectCajasChicas = '<option value="">No hay cajas chicas disponibles</option>';
        } else {
            foreach ($cajasChicas as $cc) {
                $selectCajasChicas .= "<option value='{$cc['id']}'>{$cc['nombre']}</option>";
            }
        }
    
        ob_start();
        require '../views/liquidaciones/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_cajas_chicas}}', $selectCajasChicas, $html);
        echo $html;
        exit;
    }
    
    public function updateLiquidacion($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateLiquidacion');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') && 
            !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones') && 
            !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
            error_log('Error: No tienes permiso para actualizar liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar liquidaciones']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_caja_chica = $_POST['id_caja_chica'] ?? '';
                $fecha_creacion = $_POST['fecha_creacion'] ?? '';
                $monto_total = $_POST['monto_total'] ?? 0;
                $estado = $_POST['estado'] ?? 'PENDIENTE';
    
                if (empty($id_caja_chica) || empty($fecha_creacion) || !is_numeric($monto_total) || empty($estado)) {
                    throw new Exception('Todos los campos son obligatorios');
                }
    
                $liquidacion = new Liquidacion();
                if ($liquidacion->updateLiquidacion($id, $id_caja_chica, $fecha_creacion, $monto_total, $estado)) {
                    $auditoria = new Auditoria();
                    $auditoria->createAuditoria($id, null, $_SESSION['user_id'], 'ACTUALIZADO', 'Liquidación actualizada por usuario');
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Liquidación actualizada']);
                } else {
                    throw new Exception('Error al actualizar liquidación');
                }
            } catch (Exception $e) {
                error_log('Error en updateLiquidacion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $liquidacion = new Liquidacion();
        $data = $liquidacion->getLiquidacionById($id);
    
        $cajaChica = new CajaChica();
        $cajasChicas = $cajaChica->getAllCajasChicas();
        $selectCajasChicas = '';
        if (empty($cajasChicas)) {
            $selectCajasChicas = '<option value="">No hay cajas chicas disponibles</option>';
        } else {
            foreach ($cajasChicas as $cc) {
                $selected = $data['id_caja_chica'] == $cc['id'] ? 'selected' : '';
                $selectCajasChicas .= "<option value='{$cc['id']}' {$selected}>{$cc['nombre']}</option>";
            }
        }
    
        ob_start();
        require '../views/liquidaciones/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_cajas_chicas}}', $selectCajasChicas, $html);
        echo $html;
        exit;
    }
    
    public function deleteLiquidacion($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        // Verificar si el usuario tiene permisos (por ejemplo, solo ENCARGADO o ADMIN puede eliminar)
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar liquidaciones']);
            exit;
        }
    
        // Verificar si la liquidación existe
        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        // Verificar si existen detalles asociados
        $detalles = $this->detalleLiquidacionModel->getDetallesByLiquidacionId($id);
        if (!empty($detalles)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'No se puede eliminar la liquidación porque tiene detalles asociados']);
            exit;
        }
    
        // Proceder con la eliminación
        if ($this->liquidacionModel->deleteLiquidacion($id)) {
            // Registrar la acción en la auditoría
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ELIMINADO', 'Liquidación eliminada');
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Liquidación eliminada correctamente']);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar la liquidación']);
        }
        exit;
    }
    
    public function autorizar($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para autorizar liquidaciones']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $motivo = $_POST['motivo'] ?? '';
            $detallesSeleccionados = $_POST['detalles'] ?? [];
    
            if (!in_array($accion, ['AUTORIZADO_POR_SUPERVISOR', 'RECHAZADO_POR_SUPERVISOR', 'DESCARTADO'])) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                exit;
            }
    
            try {
                $detalleModel = new DetalleLiquidacion();
                $detalles = $detalleModel->getDetallesByLiquidacionId($id);
    
                if ($accion === 'DESCARTADO') {
                    // Actualizar los detalles seleccionados a DESCARTADO
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleModel->updateEstado($detalleId, 'DESCARTADO');
                            // Registrar en auditoria para cada detalle descartado
                            $auditoria = new Auditoria();
                            $auditoria->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'DESCARTADO', $motivo);
                        }
                    }
                    // Actualizar el estado de la liquidación a PENDIENTE_CORRECCIÓN
                    $liquidacionModel->updateEstado($id, 'PENDIENTE_CORRECCIÓN');
                    $auditoria = new Auditoria();
                    $auditoria->createAuditoria($id, null, $_SESSION['user_id'], 'PENDIENTE_CORRECCIÓN', 'Liquidación marcada para corrección');
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Detalles descartados y liquidación marcada para corrección']);
                } else {
                    // Actualizar el estado de la liquidación
                    $estado = $accion === 'AUTORIZADO_POR_SUPERVISOR' ? 'AUTORIZADO_POR_SUPERVISOR' : 'RECHAZADO_POR_SUPERVISOR';
                    $liquidacionModel->updateEstado($id, $estado);
    
                    // Actualizar los detalles seleccionados
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleEstado = $accion === 'AUTORIZADO_POR_SUPERVISOR' ? 'AUTORIZADO_POR_SUPERVISOR' : 'RECHAZADO_POR_SUPERVISOR';
                            $detalleModel->updateEstado($detalleId, $detalleEstado);
                        }
                    }
    
                    // Registrar en auditoria
                    $auditoria = new Auditoria();
                    $auditoria->createAuditoria($id, null, $_SESSION['user_id'], $accion, $motivo);
    
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Autorización registrada correctamente']);
                }
            } catch (Exception $e) {
                error_log('Error al registrar autorización: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error al registrar la autorización: ' . $e->getMessage()]);
            }
            exit;
        }
    
        // Mostrar formulario de autorización
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
        $data = $liquidacion;
        $mode = 'autorizar';
        require '../views/liquidaciones/autorizar_individual.html';
        exit;
    }
    
    public function revisar($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para revisar liquidaciones']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $motivo = $_POST['motivo'] ?? '';
            $detallesSeleccionados = $_POST['detalles'] ?? [];
    
            if (!in_array($accion, ['AUTORIZADO_POR_CONTABILIDAD', 'RECHAZADO_POR_CONTABILIDAD', 'DESCARTADO'])) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                exit;
            }
    
            try {
                $detalleModel = new DetalleLiquidacion();
                $detalles = $detalleModel->getDetallesByLiquidacionId($id);
    
                if ($accion === 'DESCARTADO') {
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleModel->updateEstado($detalleId, 'DESCARTADO');
                            $auditoria = new Auditoria();
                            $auditoria->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'DESCARTADO', $motivo);
                        }
                    }
                    $liquidacionModel->updateEstado($id, 'PENDIENTE_CORRECCIÓN');
                    $auditoria = new Auditoria();
                    $auditoria->createAuditoria($id, null, $_SESSION['user_id'], 'PENDIENTE_CORRECCIÓN', 'Liquidación marcada para corrección');
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Detalles descartados y liquidación marcada para corrección']);
                } else {
                    $estado = $accion === 'AUTORIZADO_POR_CONTABILIDAD' ? 'AUTORIZADO_POR_CONTABILIDAD' : 'RECHAZADO_POR_CONTABILIDAD';
                    $liquidacionModel->updateEstado($id, $estado);
    
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleEstado = $accion === 'AUTORIZADO_POR_CONTABILIDAD' ? 'AUTORIZADO_POR_CONTABILIDAD' : 'RECHAZADO_POR_CONTABILIDAD';
                            $detalleModel->updateEstado($detalleId, $detalleEstado);
                        }
                    }
    
                    $auditoria = new Auditoria();
                    $auditoria->createAuditoria($id, null, $_SESSION['user_id'], $accion, $motivo);
    
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Revisión registrada correctamente']);
                }
            } catch (Exception $e) {
                error_log('Error al registrar revisión: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error al registrar la revisión: ' . $e->getMessage()]);
            }
            exit;
        }
    
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
        $data = $liquidacion;
        $mode = 'revisar';
        require '../views/liquidaciones/autorizar_individual.html';
        exit;
    }
    
    public function exportar($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en exportar');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log('Error: No tienes permiso para exportar liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para exportar liquidaciones']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if (!preg_match('/^AUTORIZADO_POR_/', $liquidacion['estado'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden exportar liquidaciones en estado AUTORIZADO_POR_']);
            exit;
        }
    
        $forceExport = isset($_GET['force']) && $_GET['force'] === 'true';
    
        if ($liquidacion['exportado'] == 1 && !$forceExport) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Esta liquidación ya ha sido exportada']);
            exit;
        }
    
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
    
        $filename = "liquidacion_{$id}_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    
        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Liquidacion_ID', 'Caja_Chica', 'Fecha_Creacion', 'Monto_Total', 'Estado_Liquidacion',
            'Exportado', 'Detalle_ID', 'Numero_Factura', 'Proveedor', 'Fecha_Detalle',
            'Bien_Servicio', 'Tipo_Gasto', 'Precio_Unitario', 'Total_Factura', 'Estado_Detalle'
        ]);
    
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
    
        fclose($output);
    
        // Si se fuerza la exportación, no actualizamos el estado de exportado para evitar conflictos
        if ($forceExport) {
            $liquidacionModel->markAsExported($id); // Esto marcará como exportado nuevamente
            $auditoria = new Auditoria();
            $auditoria->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO', 'Liquidación reexportada a SAP como ' . $filename);
        } else {
            $liquidacionModel->markAsExported($id);
            $auditoria = new Auditoria();
            $auditoria->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO', 'Liquidación exportada a SAP como ' . $filename);
        }
    
        exit;
    }

    // public function markAsExported($id) {
    //     $stmt = $this->pdo->prepare("UPDATE liquidaciones SET exportado = 1 WHERE id = ?");
    //     return $stmt->execute([$id]);
    // }
}