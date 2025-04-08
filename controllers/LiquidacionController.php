<?php
require_once '../models/Liquidacion.php';
require_once '../models/CajaChica.php';
require_once '../models/DetalleLiquidacion.php';
require_once '../models/Auditoria.php';
require_once '../models/TipoDocumento.php';

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
                $fecha_inicio = $_POST['fecha_inicio'] ?? null;
                $fecha_fin = $_POST['fecha_fin'] ?? null;
                $monto_total = 0; // Inicializamos en 0, se calculará después si hay detalles
                $estado = 'PENDIENTE'; // Forzar el estado a PENDIENTE
    
                if (empty($id_caja_chica) || empty($fecha_creacion)) {
                    throw new Exception('Campos obligatorios (id_caja_chica, fecha_creacion) son requeridos y deben ser válidos.');
                }
    
                if ($this->liquidacionModel->createLiquidacion($id_caja_chica, $fecha_creacion, $fecha_inicio, $fecha_fin, $monto_total, $estado)) {
                    $lastInsertId = $this->pdo->lastInsertId();
                    $this->auditoriaModel->createAuditoria($lastInsertId, null, $_SESSION['user_id'], 'CREADO', 'Liquidación creada por encargado');
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
                $fecha_inicio = $_POST['fecha_inicio'] ?? null;
                $fecha_fin = $_POST['fecha_fin'] ?? null;
                $estado = $_POST['estado'] ?? 'PENDIENTE';
    
                // Obtener la liquidación actual para preservar el monto_total
                $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
                if (!$liquidacion) {
                    throw new Exception('Liquidación no encontrada');
                }
                $monto_total = $liquidacion['monto_total']; // Mantener el monto_total actual
    
                // Solo usuarios con permisos de autorización pueden cambiar el estado
                if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
                    $estado = $liquidacion['estado']; // Mantener el estado actual
                } else {
                    // Transformar el estado según el rol del usuario
                    $rol = strtoupper($usuario['rol']);
                    if ($estado === 'APROBADO') {
                        $estado = "AUTORIZADO_POR_{$rol}";
                    } elseif ($estado === 'RECHAZADO') {
                        $estado = "RECHAZADO_POR_{$rol}";
                    }
    
                    // Validar que el estado transformado sea un valor permitido en el ENUM
                    $allowedEstados = [
                        'PENDIENTE',
                        'AUTORIZADO_POR_ADMIN',
                        'RECHAZADO_POR_ADMIN',
                        'AUTORIZADO_POR_CONTABILIDAD',
                        'RECHAZADO_POR_CONTABILIDAD',
                        'AUTORIZADO_POR_SUPERVISOR',
                        'RECHAZADO_POR_SUPERVISOR',
                        'PENDIENTE_CORRECCIÓN',
                        'DESCARTADO',
                        'FINALIZADO'
                    ];
                    if (!in_array($estado, $allowedEstados)) {
                        throw new Exception("Estado no permitido: {$estado}. Contacta al administrador del sistema.");
                    }
                }
    
                // Validaciones existentes
                if (empty($id_caja_chica) || empty($fecha_creacion)) {
                    throw new Exception('Campos obligatorios (id_caja_chica, fecha_creacion) son requeridos.');
                }
    
                // Nueva validación: fecha_inicio <= fecha_fin
                if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                    $fechaInicioDate = new DateTime($fecha_inicio);
                    $fechaFinDate = new DateTime($fecha_fin);
                    if ($fechaInicioDate > $fechaFinDate) {
                        throw new Exception('La fecha de inicio no puede ser mayor que la fecha de fin.');
                    }
                }
    
                if ($this->liquidacionModel->updateLiquidacion($id, $id_caja_chica, $fecha_creacion, $fecha_inicio, $fecha_fin, $monto_total, $estado)) {
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ACTUALIZADO', 'Liquidación actualizada por usuario');
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
    
        // Proceder con la eliminación dentro de una transacción
        try {
            // Usar $this->pdo en lugar de $this->liquidacionModel->getPdo()
            $this->pdo->beginTransaction();
    
            // Registrar la acción en la auditoría ANTES de eliminar la liquidación
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ELIMINADO', 'Liquidación eliminada');
            error_log("Auditoría registrada para la liquidación ID $id antes de eliminarla");
    
            // Eliminar registros dependientes en la tabla auditoria
            $stmt = $this->pdo->prepare("DELETE FROM auditoria WHERE id_liquidacion = ?");
            $stmt->execute([$id]);
            error_log("Registros de auditoría eliminados para la liquidación ID $id");
    
            // Eliminar la liquidación
            if (!$this->liquidacionModel->deleteLiquidation($id)) {
                throw new Exception('Error al eliminar la liquidación en la base de datos');
            }
    
            $this->pdo->commit();
    
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Liquidación eliminada correctamente']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar liquidación ID $id: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar la liquidación: ' . $e->getMessage()]);
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
        if (!$usuario) {
            error_log("Usuario no encontrado para ID: " . $_SESSION['user_id']);
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para autorizar liquidaciones. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para autorizar liquidaciones']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            error_log("Liquidación no encontrada para ID: $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $motivo = $_POST['motivo'] ?? '';
            $detallesSeleccionados = $_POST['detalles'] ?? [];
    
            // Obtener el rol del usuario
            $rol = strtoupper($usuario['rol']);
            error_log("Rol del usuario: $rol");
    
            $allowedAcciones = [
                "AUTORIZADO_POR_{$rol}",
                "RECHAZADO_POR_{$rol}",
                'DESCARTADO'
            ];
    
            // Depuración: Registrar la acción recibida
            error_log("Acción recibida en autorizar: " . $accion);
    
            if (!in_array($accion, $allowedAcciones)) {
                error_log("Acción no válida: " . $accion . ". Acciones permitidas: " . implode(', ', $allowedAcciones));
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
                            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'DESCARTADO', $motivo);
                        }
                    }
                    if (!$liquidacionModel->updateEstado($id, 'PENDIENTE_CORRECCIÓN')) {
                        throw new Exception('Error al actualizar el estado de la liquidación a PENDIENTE_CORRECCIÓN');
                    }
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'PENDIENTE_CORRECCIÓN', 'Liquidación marcada para corrección');
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Detalles descartados y liquidación marcada para corrección']);
                } else {
                    // Actualizar el estado de la liquidación según la acción
                    $estado = $accion; // AUTORIZADO_POR_{ROL} o RECHAZADO_POR_{ROL}
                    if (!$liquidacionModel->updateEstado($id, $estado)) {
                        throw new Exception("Error al actualizar el estado de la liquidación a $estado");
                    }
    
                    // Actualizar el estado de los detalles seleccionados
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleEstado = $accion;
                            $detalleModel->updateEstado($detalleId, $detalleEstado);
                        }
                    }
    
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $accion, $motivo);
    
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
    
            // Obtener el rol del usuario
            $rol = strtoupper($usuario['rol']);
            $allowedAcciones = [
                "AUTORIZADO_POR_{$rol}",
                "RECHAZADO_POR_{$rol}",
                'DESCARTADO'
            ];
    
            // Log para depurar
            error_log("Acción recibida en revisar: " . $accion);
            error_log("Acciones permitidas en revisar: " . print_r($allowedAcciones, true));
    
            if (!in_array($accion, $allowedAcciones)) {
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
                    $estado = $accion; // El estado ya es dinámico (AUTORIZADO_POR_{$rol} o RECHAZADO_POR_{$rol})
                    $liquidacionModel->updateEstado($id, $estado);
    
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleEstado = $accion; // Usar el mismo estado para los detalles
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
    
    public function manageFacturas($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en manageFacturas');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log('Error: No tienes permiso para gestionar facturas');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar facturas']);
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
            $action = $_POST['action'] ?? '';
            try {
                $this->pdo->beginTransaction();
    
                if ($action === 'create') {
                    $tipo_documento = $_POST['tipo_documento'] ?? '';
                    $no_factura = $_POST['no_factura'] ?? '';
                    $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                    $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                    $fecha = $_POST['fecha'] ?? '';
                    $bien_servicio = $_POST['bien_servicio'] ?? '';
                    $t_gasto = $_POST['t_gasto'] ?? '';
                    $p_unitario = $_POST['p_unitario'] ?? 0;
                    $total_factura = $_POST['total_factura'] ?? 0;
                    $estado = 'PENDIENTE';
    
                    if (empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($bien_servicio) || empty($t_gasto) || !is_numeric($p_unitario) || !is_numeric($total_factura)) {
                        throw new Exception('Todos los campos son obligatorios y deben ser válidos.');
                    }
    
                    $fechaFactura = new DateTime($fecha);
                    $fechaInicio = new DateTime($liquidacion['fecha_inicio']);
                    $fechaFin = new DateTime($liquidacion['fecha_fin']);
                    if ($fechaFactura < $fechaInicio || $fechaFactura > $fechaFin) {
                        throw new Exception("La fecha de la factura debe estar entre {$liquidacion['fecha_inicio']} y {$liquidacion['fecha_fin']}.");
                    }
    
                    // Validar que no_factura no se repita para esta liquidación
                    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM detalle_liquidaciones WHERE id_liquidacion = ? AND no_factura = ?");
                    $stmt->execute([$id, $no_factura]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception("El número de factura '$no_factura' ya existe para esta liquidación.");
                    }
    
                    // Validar que nit_proveedor no se repita para esta liquidación (si se proporciona)
                    // if (!empty($nit_proveedor)) {
                    //     $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM detalle_liquidaciones WHERE id_liquidacion = ? AND nit_proveedor = ?");
                    //     $stmt->execute([$id, $nit_proveedor]);
                    //     if ($stmt->fetchColumn() > 0) {
                    //         throw new Exception("El NIT del proveedor '$nit_proveedor' ya existe para esta liquidación.");
                    //     }
                    // }
    
                    $detalleModel = new DetalleLiquidacion();
                    if ($detalleModel->createDetalleLiquidacion($id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $fecha, $bien_servicio, $t_gasto, $p_unitario, $total_factura, $estado)) {
                        $lastInsertId = $this->pdo->lastInsertId();
                        $this->auditoriaModel->createAuditoria($id, $lastInsertId, $_SESSION['user_id'], 'CREAR_DETALLE', "Factura creada: $no_factura");
                        $response = ['message' => 'Factura creada correctamente', 'detalle_id' => $lastInsertId];
                    } else {
                        throw new Exception('Error al crear la factura.');
                    }
    
                } elseif ($action === 'update') {
                    $detalle_id = $_POST['detalle_id'] ?? '';
                    $tipo_documento = $_POST['tipo_documento'] ?? '';
                    $no_factura = $_POST['no_factura'] ?? '';
                    $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                    $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                    $fecha = $_POST['fecha'] ?? '';
                    $bien_servicio = $_POST['bien_servicio'] ?? '';
                    $t_gasto = $_POST['t_gasto'] ?? '';
                    $p_unitario = $_POST['p_unitario'] ?? 0;
                    $total_factura = $_POST['total_factura'] ?? 0;
                    $estado = $_POST['estado'] ?? 'PENDIENTE';
    
                    if (empty($detalle_id) || empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($bien_servicio) || empty($t_gasto) || !is_numeric($p_unitario) || !is_numeric($total_factura)) {
                        throw new Exception('Todos los campos son obligatorios y deben ser válidos.');
                    }
    
                    $fechaFactura = new DateTime($fecha);
                    $fechaInicio = new DateTime($liquidacion['fecha_inicio']);
                    $fechaFin = new DateTime($liquidacion['fecha_fin']);
                    if ($fechaFactura < $fechaInicio || $fechaFactura > $fechaFin) {
                        throw new Exception("La fecha de la factura debe estar entre {$liquidacion['fecha_inicio']} y {$liquidacion['fecha_fin']}.");
                    }
    
                    // Validar que no_factura no se repita para esta liquidación (excluyendo el detalle actual)
                    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM detalle_liquidaciones WHERE id_liquidacion = ? AND no_factura = ? AND id != ?");
                    $stmt->execute([$id, $no_factura, $detalle_id]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception("El número de factura '$no_factura' ya existe para esta liquidación.");
                    }
    
                    // Validar que nit_proveedor no se repita para esta liquidación (excluyendo el detalle actual, si se proporciona)
                    if (!empty($nit_proveedor)) {
                        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM detalle_liquidaciones WHERE id_liquidacion = ? AND nit_proveedor = ? AND id != ?");
                        $stmt->execute([$id, $nit_proveedor, $detalle_id]);
                        if ($stmt->fetchColumn() > 0) {
                            throw new Exception("El NIT del proveedor '$nit_proveedor' ya existe para esta liquidación.");
                        }
                    }
    
                    $detalleModel = new DetalleLiquidacion();
                    if ($detalleModel->updateDetalleLiquidacion($detalle_id, $id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $fecha, $bien_servicio, $t_gasto, $p_unitario, $total_factura, $estado)) {
                        $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ACTUALIZAR_DETALLE', "Factura actualizada: $no_factura");
                        $response = ['message' => 'Factura actualizada correctamente'];
                    } else {
                        throw new Exception('Error al actualizar la factura.');
                    }
    
                } elseif ($action === 'delete') {
                    $detalle_id = $_POST['detalle_id'] ?? '';
                    if (empty($detalle_id)) {
                        throw new Exception('ID de detalle requerido para eliminar.');
                    }
    
                    $detalleModel = new DetalleLiquidacion();
                    $detalle = $detalleModel->getDetalleLiquidacionById($detalle_id);
                    if (!$detalle) {
                        throw new Exception('Factura no encontrada.');
                    }
    
                    if ($detalleModel->deleteDetalleLiquidacion($detalle_id)) {
                        $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ELIMINAR_DETALLE', "Factura eliminada: {$detalle['no_factura']}");
                        $response = ['message' => 'Factura eliminada correctamente'];
                    } else {
                        throw new Exception('Error al eliminar la factura.');
                    }
                }
    
                $detalleModel = new DetalleLiquidacion();
                $detalles = $detalleModel->getDetallesByLiquidacionId($id);
                $monto_total = array_sum(array_column($detalles, 'total_factura'));
                $liquidacionModel->updateLiquidacion($id, $liquidacion['id_caja_chica'], $liquidacion['fecha_creacion'], $liquidacion['fecha_inicio'], $liquidacion['fecha_fin'], $monto_total, $liquidacion['estado']);
    
                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode($response);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error en manageFacturas: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        // Obtener los tipos de gasto desde la tabla tipos_gastos
        $tipoGastoModel = new TipoGasto();
        $tiposGastos = $tipoGastoModel->getAllTiposGastos();
        $select_tipos_gastos = '';
        if (empty($tiposGastos)) {
            $select_tipos_gastos = '<option value="">No hay tipos de gastos disponibles</option>';
        } else {
            foreach ($tiposGastos as $tipo) {
                if ($tipo['estado'] === 'ACTIVO') {
                    $select_tipos_gastos .= "<option value='{$tipo['name']}'>{$tipo['name']}</option>";
                }
            }
        }
    
        // Obtener los tipos de documento desde la tabla tipos_documentos
        $tipoDocumentoModel = new TipoDocumento();
        $tiposDocumentos = $tipoDocumentoModel->getAllTiposDocumentos();
        $select_tipos_documentos = '';
        if (empty($tiposDocumentos)) {
            $select_tipos_documentos = '<option value="">No hay tipos de documentos disponibles</option>';
        } else {
            foreach ($tiposDocumentos as $tipo) {
                if ($tipo['estado'] === 'ACTIVO') {
                    $select_tipos_documentos .= "<option value='{$tipo['name']}'>{$tipo['name']}</option>";
                }
            }
        }
    
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
        $data = $liquidacion;
        require '../views/liquidaciones/manage_facturas.html';
        exit;
    }

    public function finalizar($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para finalizar liquidaciones']);
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
    
        // Depuración: Registrar el estado actual de la liquidación
        error_log("Estado actual de la liquidación con ID $id: " . $liquidacion['estado']);
    
        if (!in_array($liquidacion['estado'], ['PENDIENTE', 'PENDIENTE_CORRECCIÓN'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden finalizar liquidaciones en estado PENDIENTE o PENDIENTE_CORRECCIÓN']);
            exit;
        }
    
        try {
            $liquidacionModel->updateEstado($id, 'FINALIZADO');
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'FINALIZADO', 'Liquidación finalizada por encargado');
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Liquidación finalizada correctamente']);
        } catch (Exception $e) {
            error_log('Error al finalizar liquidación: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al finalizar la liquidación: ' . $e->getMessage()]);
        }
        exit;
    }
}

 // public function markAsExported($id) {
    //     $stmt = $this->pdo->prepare("UPDATE liquidaciones SET exportado = 1 WHERE id = ?");
    //     return $stmt->execute([$id]);
    // }